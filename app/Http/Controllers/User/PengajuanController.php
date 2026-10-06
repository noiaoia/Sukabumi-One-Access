<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\FormCategory;
use App\Models\Admin\Layanan;
use App\Models\User\Pengajuan;
use App\Models\User\PengajuanData;
use App\Models\User\PengajuanDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // WAJIB ADA

class PengajuanController extends Controller
{
    /**
     * Mapping Label Parameter untuk pesan error & label input.
     * Sumber tunggal (single source of truth) dipakai baik di store() maupun update(),
     * dan HARUS identik dengan PARAM_MAP di resources/views/pages/user/form.blade.php
     * dan modal-edit-submission.blade.php.
     */
    protected static function parameterMap(): array
    {
        return [
            'nama_lengkap'  => ['label' => 'Nama Lengkap'],
            'nik'           => ['label' => 'NIK'],
            'no_kk'         => ['label' => 'Nomor KK'],
            'tempat_lahir'  => ['label' => 'Tempat Lahir'],
            'tanggal_lahir' => ['label' => 'Tanggal Lahir'],
            'jenis_kelamin' => ['label' => 'Jenis Kelamin'],
            'no_hp'         => ['label' => 'Nomor WhatsApp'],
            'email'         => ['label' => 'Email'],
            'keterangan'    => ['label' => 'Keterangan'],
        ];
    }

    protected static function addressParameters(): array
    {
        return ['provinsi', 'provinsi_name', 'kota', 'kota_name', 'kecamatan', 'kecamatan_name', 'kelurahan', 'kelurahan_name', 'alamat_lengkap', 'rt', 'rw'];
    }

    protected static function decodeStructure($structure): array
    {
        if (is_string($structure)) {
            $structure = json_decode($structure, true) ?? [];
        }

        return is_array($structure) ? $structure : [];
    }

    /**
     * Tampilkan daftar riwayat pengajuan milik warga
     */
    public function history()
    {
        $submissions = Pengajuan::where('user_id', Auth::id())
                        ->with(['layanan', 'category'])
                        ->latest()
                        ->get();

        // Mengarah ke resources/views/pages/user/riwayat-pengajuan.blade.php
        return view('pages.user.riwayat-pengajuan', compact('submissions'));
    }

    /**
     * Ambil data JSON detail pengajuan untuk modal preview warga
     */
    public function jsonDetail($id)
    {
        // Keamanan: Pastikan user hanya bisa melihat datanya sendiri
        $submission = Pengajuan::where('user_id', Auth::id())
                        ->with(['layanan.instansi', 'category', 'data', 'dokumen'])
                        ->findOrFail($id);

        // --- TAMBAHKAN INI ---
        // Tandai notifikasi perubahan status sudah dibaca oleh warga
        $submission->update(['is_read_user_notification' => true]);

        return response()->json($submission);
    }

    /**
     * Menyimpan pengajuan formulir.
     */
    public function store(Request $request, $slug, $categoryId)
    {
        $layanan = Layanan::where('slug', $slug)
            ->where('jenis', 'form')
            ->where('status', 'aktif')
            ->firstOrFail();

        $category = FormCategory::where('layanan_id', $layanan->id)
            ->where('status', 'aktif')
            ->findOrFail($categoryId);

        $parameterMap = static::parameterMap();

        /*
        |--------------------------------------------------------------------------
        | Ambil struktur Builder
        |--------------------------------------------------------------------------
        */
        $structure = static::decodeStructure($category->structure);

        /*
        |--------------------------------------------------------------------------
        | Ambil parameter yang tersedia
        |--------------------------------------------------------------------------
        */
        $allowedParameters = [];
        foreach ($structure as $group) {
            foreach ($group['fields'] ?? [] as $field) {
                if (($field['type'] ?? null) === 'parameter') {
                    $parameter = $field['parameter'] ?? null;
                    if ($parameter) {
                        $allowedParameters[] = $parameter;
                    }
                }
            }
        }

        // Tambahkan bundle alamat ke parameter yang diizinkan
        $allowedParameters = array_unique(array_merge($allowedParameters, static::addressParameters()));

        /*
        |--------------------------------------------------------------------------
        | Tambahkan Validasi Dinamis
        |--------------------------------------------------------------------------
        */
        $rules = [];
        $messages = [];

        foreach ($structure as $group) {
            foreach ($group['fields'] ?? [] as $field) {
                if (!empty($field['required'])) {
                    $pKey = $field['parameter'];
                    // Ambil label dari map, jika tidak ada pakai nama key-nya
                    $label = $parameterMap[$pKey]['label'] ?? str_replace('_', ' ', $pKey);

                    $rules[$pKey] = 'required';
                    $messages["$pKey.required"] = "Bidang $label wajib diisi.";
                }
            }
        }

        // Tambahkan validasi NIK/KK jika ada
        if ($request->has('nik')) { $rules['nik'] = 'required|digits:16'; }
        if ($request->has('no_kk')) { $rules['no_kk'] = 'required|digits:16'; }

        $request->validate($rules, $messages);

        /*
        |--------------------------------------------------------------------------
        | Simpan dalam satu transaksi
        |--------------------------------------------------------------------------
        */
        DB::beginTransaction();

        try {
            $nomorPengajuan = 'SOA-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

            /*
            |--------------------------------------------------------------------------
            | Simpan pengajuan utama (DITAMBAHKAN user_id)
            |--------------------------------------------------------------------------
            */
            $pengajuan = Pengajuan::create([
                'user_id'         => Auth::id(), // <--- SANGAT PENTING
                'layanan_id'      => $layanan->id,
                'category_id'     => $category->id,
                'nomor_pengajuan' => $nomorPengajuan,
                'status'          => 'diajukan',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Simpan parameter data
            |--------------------------------------------------------------------------
            */
            foreach ($allowedParameters as $parameter) {
                if (!$request->has($parameter)) {
                    continue;
                }

                $value = $request->input($parameter);
                if (is_array($value)) {
                    $value = json_encode($value);
                }

                PengajuanData::create([
                    'pengajuan_id' => $pengajuan->id,
                    'parameter' => $parameter,
                    'value' => $value,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan dokumen
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $requirementId => $files) {
                    if (!is_array($files)) {
                        $files = [$files];
                    }

                    foreach ($files as $file) {
                        if (!$file || !$file->isValid()) {
                            continue;
                        }

                        $path = $file->store('pengajuan/' . $pengajuan->id, 'public');

                        PengajuanDokumen::create([
                            'pengajuan_id' => $pengajuan->id,
                            'requirement_id' => $requirementId,
                            'nama_dokumen' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('layanan.form.category', [
                    'slug' => $slug,
                    'categoryId' => $categoryId,
                ])
                ->with('success', 'Pengajuan berhasil dikirim.')
                ->with('nomor_pengajuan', $nomorPengajuan);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan pengajuan', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return back()
                ->withInput()
                ->with('error', 'Pengajuan gagal dikirim. Silakan coba lagi.');
        }
    }

    /**
     * Ambil data pengajuan (beserta structure Form Builder) untuk modal revisi.
     * Hanya pengajuan berstatus 'perlu_revisi' milik warga yang bersangkutan
     * yang boleh diakses.
     */
    public function editData($id)
    {
        $submission = Pengajuan::where('user_id', Auth::id())
            ->where('status', 'perlu_revisi')
            ->with(['layanan', 'category', 'data', 'dokumen'])
            ->findOrFail($id);

        $structure = static::decodeStructure($submission->category->structure ?? []);

        return response()->json([
            'structure'       => $structure,
            'values'          => $submission->data,
            'documents'       => $submission->dokumen,
            'catatan_admin'   => $submission->catatan_admin,
            'nomor_pengajuan' => $submission->nomor_pengajuan,
        ]);
    }

    /**
     * Simpan hasil revisi pengajuan warga.
     * Validasi & parameter yang diizinkan mengikuti structure Form Builder yang sama
     * dengan form pengajuan asli (store()), sehingga field asing tidak bisa disisipkan
     * dan aturan wajib/NIK/KK/RT/RW tetap konsisten.
     */
    public function update(Request $request, $id)
    {
        $submission = Pengajuan::where('user_id', Auth::id())
            ->where('status', 'perlu_revisi')
            ->with('category')
            ->findOrFail($id);

        $category = $submission->category;
        $structure = static::decodeStructure($category->structure ?? []);
        $parameterMap = static::parameterMap();

        /*
        |--------------------------------------------------------------------------
        | Susun daftar parameter yang diizinkan & requirement dokumen dari structure
        |--------------------------------------------------------------------------
        */
        $allowedParameters = [];
        $documentRequirements = [];
        $hasAddressBundle = false;

        foreach ($structure as $group) {
            $groupType = $group['type'] ?? 'normal';

            if ($groupType === 'normal') {
                foreach ($group['fields'] ?? [] as $field) {
                    if (($field['type'] ?? null) === 'parameter' && !empty($field['parameter'])) {
                        $allowedParameters[] = $field['parameter'];
                    }
                }
            } elseif ($groupType === 'bundle_address') {
                $hasAddressBundle = true;
            } elseif ($groupType === 'bundle_documents') {
                foreach ($group['requirements'] ?? [] as $req) {
                    if (!empty($req['id'])) {
                        $documentRequirements[$req['id']] = $req;
                    }
                }
            }
        }

        if ($hasAddressBundle) {
            $allowedParameters = array_unique(array_merge($allowedParameters, static::addressParameters()));
        }

        /*
        |--------------------------------------------------------------------------
        | Bangun rules validasi dinamis
        |--------------------------------------------------------------------------
        */
        $rules = [];
        $messages = [];

        foreach ($structure as $group) {
            if (($group['type'] ?? 'normal') !== 'normal') {
                continue;
            }
            foreach ($group['fields'] ?? [] as $field) {
                if (!empty($field['required']) && !empty($field['parameter'])) {
                    $pKey = $field['parameter'];
                    $label = $parameterMap[$pKey]['label'] ?? str_replace('_', ' ', $pKey);
                    $rules[$pKey] = 'required';
                    $messages["$pKey.required"] = "Bidang $label wajib diisi.";
                }
            }
        }

        if (in_array('nik', $allowedParameters) && $request->filled('nik')) {
            $rules['nik'] = 'required|digits:16';
        }
        if (in_array('no_kk', $allowedParameters) && $request->filled('no_kk')) {
            $rules['no_kk'] = 'required|digits:16';
        }

        if ($hasAddressBundle) {
            $rules['provinsi'] = 'required';
            $rules['kota'] = 'required';
            $rules['kecamatan'] = 'required';
            $rules['kelurahan'] = 'required';
            $rules['alamat_lengkap'] = 'required|string|max:255';
            $rules['rt'] = 'required|digits_between:1,3';
            $rules['rw'] = 'required|digits_between:1,3';
        }

        // Validasi dokumen: wajib hanya jika belum ada dokumen tersimpan untuk requirement itu
        foreach ($documentRequirements as $reqId => $req) {
            $format = $req['format'] ?? 'image';
            $maxSize = (float) ($req['max_size_mb'] ?? 2);
            $maxFiles = (int) ($req['max_files'] ?? 1);
            $isWajib = !empty($req['wajib']);

            $hasExisting = PengajuanDokumen::where('pengajuan_id', $submission->id)
                ->where('requirement_id', $reqId)
                ->exists();

            $mimeRule = match ($format) {
                'pdf'  => 'mimes:pdf',
                'both' => 'mimes:jpg,jpeg,png,webp,pdf',
                default => 'mimes:jpg,jpeg,png,webp',
            };

            $requiredRule = ($isWajib && !$hasExisting) ? 'required' : 'nullable';
            $maxKb = (int) round($maxSize * 1024);

            if ($maxFiles > 1) {
                $rules["documents.$reqId"] = ['array', "max:$maxFiles"];
                $rules["documents.$reqId.*"] = "$requiredRule|file|$mimeRule|max:$maxKb";
            } else {
                $rules["documents.$reqId"] = "$requiredRule|file|$mimeRule|max:$maxKb";
            }
        }

        $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            /*
            |--------------------------------------------------------------------------
            | Update data parameter (hanya parameter yang diizinkan oleh structure)
            |--------------------------------------------------------------------------
            */
            foreach ($allowedParameters as $parameter) {
                if (!$request->has($parameter)) {
                    continue;
                }

                $value = $request->input($parameter);
                if (is_array($value)) {
                    $value = json_encode($value);
                }

                PengajuanData::updateOrCreate(
                    ['pengajuan_id' => $submission->id, 'parameter' => $parameter],
                    ['value' => $value]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Handle dokumen baru. Dokumen lama untuk requirement yang TIDAK diganti
            | tetap dipertahankan (tidak dihapus).
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $reqId => $files) {
                    if (!is_array($files)) {
                        $files = [$files];
                    }

                    $validFiles = array_filter($files, fn ($f) => $f && $f->isValid());
                    if (empty($validFiles)) {
                        continue;
                    }

                    // Hapus seluruh dokumen lama untuk requirement ini karena diganti
                    $oldDocs = PengajuanDokumen::where('pengajuan_id', $submission->id)
                        ->where('requirement_id', $reqId)
                        ->get();

                    foreach ($oldDocs as $oldDoc) {
                        Storage::disk('public')->delete($oldDoc->file_path);
                        $oldDoc->delete();
                    }

                    foreach ($validFiles as $file) {
                        $path = $file->store('pengajuan/' . $submission->id, 'public');

                        PengajuanDokumen::create([
                            'pengajuan_id' => $submission->id,
                            'requirement_id' => $reqId,
                            'nama_dokumen' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            }

            // Kembalikan status ke 'review' agar diproses ulang oleh admin
            $submission->update(['status' => 'review', 'is_read_notification' => false]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perbaikan berhasil dikirim.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan revisi pengajuan', [
                'error' => $e->getMessage(),
                'pengajuan_id' => $submission->id,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan perbaikan. Silakan coba lagi.',
            ], 500);
        }
    }
}