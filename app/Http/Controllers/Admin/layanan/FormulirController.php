<?php

namespace App\Http\Controllers\Admin\layanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Admin\Instansi;
use App\Models\Admin\Layanan;
use App\Models\Admin\FormCategory;

class FormulirController extends Controller
{
    /**
     * Keamanan: Validasi agar admin hanya bisa mengakses portal miliknya sendiri.
     */
    private function validatePortal($portal_code)
    {
        if ($portal_code !== 'portal-' . Auth::user()->username) {
            abort(404);
        }
    }

    private function validateInstansiAccess($instansi)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return;
        }

        if (!$user->instansi_id || $user->instansi_id !== $instansi->id) {
            abort(403, 'Anda tidak memiliki akses ke instansi ini.');
        }
    }

    /**
     * Tampilan Daftar Formulir Mandiri
     */
    public function manage($portal_code, $instansi_slug)
    {
        $this->validatePortal($portal_code);

        $instansi = Instansi::where('slug', $instansi_slug)->firstOrFail();

        $this->validateInstansiAccess($instansi);

        // Mengambil hanya layanan yang berjenis 'form'
        $layananList = Layanan::where('instansi_id', $instansi->id)
            ->where('jenis', 'form')
            ->orderBy('urutan')
            ->get();

        return view('pages.admin.layanan.formulir.manage', compact('portal_code', 'instansi', 'layananList'));
    }

    /**
     * Halaman Tambah Formulir
     */
    public function create($portal_code, Request $request)
    {
        $this->validatePortal($portal_code);

        $instansi_slug = $request->query('instansi');
        $instansi = Instansi::where('slug', $instansi_slug)->firstOrFail();

        $this->validateInstansiAccess($instansi);

        return view('pages.admin.layanan.formulir.create', compact('portal_code', 'instansi', 'instansi_slug'));
    }

    /**
     * Simpan Registrasi Formulir Baru ke Database (Termasuk Upload Gambar)
     */
    public function store(Request $request, $portal_code)
    {
        // 1. Keamanan Portal: Pastikan admin hanya bisa memproses portalnya sendiri
        $this->validatePortal($portal_code);

        // 2. Pre-fetch Data Instansi (Untuk kebutuhan validasi relasi)
        $instansi = Instansi::where('slug', $request->instansi_slug)->firstOrFail();
        $this->validateInstansiAccess($instansi);

        // 3. Validasi Input yang Ketat
        $request->validate([
            'instansi_slug' => 'required|exists:instansis,slug',
            'nama'          => 'required|string|max:255|min:5',
            'deskripsi'     => 'required|string|min:20|max:1000',
            'cover'         => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // Batas 2MB
                // 'dimensions:min_width=400,min_height=200' // Opsional: Menjaga kualitas visual
            ],
        ], [
            // Pesan Error Kustom dalam Bahasa Indonesia
            'nama.required'      => 'Judul formulir wajib diisi.',
            'nama.min'           => 'Judul formulir terlalu pendek (Minimal 5 karakter).',
            'deskripsi.required' => 'Deskripsi layanan wajib diisi agar warga mengerti fungsi formulir ini.',
            'deskripsi.min'      => 'Berikan deskripsi yang lebih jelas (Minimal 20 karakter).',
            'cover.required'     => 'Foto sampul wajib diunggah sebagai identitas visual.',
            'cover.image'        => 'File yang diunggah harus berupa gambar.',
            'cover.mimes'        => 'Format gambar yang didukung hanya JPG, PNG, dan WEBP.',
            'cover.max'          => 'Ukuran foto sampul terlalu besar (Maksimal 2MB).',
        ]);

        // 4. Handle Proses Upload File
        $coverPath = null;
        if ($request->hasFile('cover')) {
            // Simpan gambar ke folder storage/app/public/layanan/cover
            $coverPath = $request->file('cover')->store('layanan/cover', 'public');
        }

        // 5. Eksekusi Penyimpanan dengan Error Handling (Database Transactional Feel)
        try {
            Layanan::create([
                'instansi_id'     => $instansi->id,
                'nama'            => strip_tags($request->nama),      // Security: Hapus tag HTML jika ada
                'deskripsi'       => strip_tags($request->deskripsi), // Security: Hapus tag HTML jika ada
                'cover'           => $coverPath,
                'slug'            => $this->generateUniqueSlug($request->nama),
                'jenis'           => 'form',
                'icon'            => 'bi-file-earmark-text',
                'primary_color'   => '#123457', // Default Navy SOA
                'secondary_color' => '#E8A33D', // Default Gold SOA
                'status'          => 'draft',
                'urutan'          => (Layanan::where('instansi_id', $instansi->id)->max('urutan') ?? 0) + 1,
            ]);

            return redirect()->route('admin.layanan.form.manage', [$portal_code, $instansi->slug])
                ->with('success', 'Formulir "' . $request->nama . '" berhasil didaftarkan sebagai draft.');

        } catch (\Exception $e) {
            // 6. Cleanup Logic: Jika database gagal saat proses create, hapus file yang sudah terupload
            if ($coverPath && Storage::disk('public')->exists($coverPath)) {
                Storage::disk('public')->delete($coverPath);
            }

            return back()
                ->with('error', 'Terjadi kesalahan sistem saat menyimpan data. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Halaman Konfigurasi Visual
     */
    public function visual($portal_code, $id)
    {
        $this->validatePortal($portal_code);

        $svc = Layanan::with('instansi')->findOrFail($id);
        $this->validateInstansiAccess($svc->instansi);

        $instansi_slug = $svc->instansi->slug;

        return view('pages.admin.layanan.formulir.visual', compact('portal_code', 'svc', 'instansi_slug'));
    }

    /**
     * Update Perubahan Visual & Identitas Dasar Formulir
     */
    public function update(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);

        // Perbaikan Validasi: primary_color, secondary_color, dan icon dibuat 'sometimes'
        // artinya hanya divalidasi JIKA ada di dalam request.
        $request->validate([
            'nama'            => 'required|string|max:255',
            'deskripsi'       => 'required|string|max:1000',
            'primary_color'   => ['sometimes', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'secondary_color' => 'sometimes|string|max:7',
            'icon'            => 'sometimes|string|max:100',
            'cover'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nama.required' => 'Nama layanan tidak boleh kosong.',
            'cover.max'     => 'Ukuran foto maksimal 2MB.'
        ]);

        $layanan = Layanan::findOrFail($id);
        $this->validateInstansiAccess($layanan->instansi);

        // Update Slug jika nama berubah
        if ($layanan->nama !== $request->nama) {
            $layanan->slug = $this->generateUniqueSlug($request->nama, $layanan->id);
        }

        // Handle Upload Gambar (Hanya jika ada file baru)
        if ($request->hasFile('cover')) {
            if ($layanan->cover && Storage::disk('public')->exists($layanan->cover)) {
                Storage::disk('public')->delete($layanan->cover);
            }
            $layanan->cover = $request->file('cover')->store('layanan/cover', 'public');
        }

        // Update data teks (Hanya update yang dikirim saja)
        $layanan->nama      = $request->nama;
        $layanan->deskripsi = $request->deskripsi;
        
        // Simpan warna/ikon hanya jika ada di request (mencegah null)
        if($request->has('primary_color'))   $layanan->primary_color = $request->primary_color;
        if($request->has('secondary_color')) $layanan->secondary_color = $request->secondary_color;
        if($request->has('icon'))            $layanan->icon = $request->icon;
        
        $layanan->save();

        return redirect()->back()->with('success', 'Identitas formulir berhasil diperbarui.');
    }
    
    /**
     * Update Status Formulir
     */
    public function updateStatus(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);

        $request->validate([
            'status' => 'required|in:aktif,draft',
        ]);

        $layanan = Layanan::with('instansi')
            ->where('jenis', 'form')
            ->findOrFail($id);

        $this->validateInstansiAccess($layanan->instansi);

        $layanan->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status formulir berhasil diperbarui.',
            'status' => $layanan->status,
        ]);
    }

    /**
     * Hapus Layanan Formulir
     */
    public function destroy($portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $layanan = Layanan::with('instansi')->findOrFail($id);
        $this->validateInstansiAccess($layanan->instansi);
        if ($layanan->cover) {
            Storage::disk('public')->delete($layanan->cover);
        }
        $layanan->forceDelete();
        return redirect()->back()->with('success', 'Formulir berhasil dihapus permanen.');
    }

    /**
     * Tampilan Form Builder
     */
    public function builder($portal_code, $id, $cat_id)
    {
        $this->validatePortal($portal_code);

        $svc = Layanan::with('instansi')->findOrFail($id);

        $this->validateInstansiAccess($svc->instansi);

        $instansi = $svc->instansi;

        $category = FormCategory::where('layanan_id', $id)
            ->findOrFail($cat_id);

        // Ambil struktur form yang sudah tersimpan
        $structure = $category->structure;

        // Jika database menyimpan JSON string, decode menjadi array
        if (is_string($structure)) {
            $structure = json_decode($structure, true) ?? [];
        }

        return view('pages.admin.layanan.formulir.builder', compact(
            'portal_code',
            'instansi',
            'svc',
            'category',
            'structure'
        ));
    }

    /**
     * Simpan konfigurasi Form Builder
     */
    public function saveBuilder(Request $request, $portal_code, $id, $cat_id)
    {
        $this->validatePortal($portal_code);

        $svc = Layanan::with('instansi')->findOrFail($id);
        $this->validateInstansiAccess($svc->instansi);

        $category = FormCategory::where('layanan_id', $id)
            ->findOrFail($cat_id);

        $request->validate([
            'form_title' => 'nullable|string|max:255',
            'form_description' => 'nullable|string|max:2000',
            'edit_limit' => 'required|integer|min:-1',
            'structure' => 'required|string',
        ]);

        $structure = json_decode($request->structure, true);

        if (!is_array($structure)) {
            return back()
                ->withInput()
                ->with('error', 'Struktur formulir tidak valid.');
        }

        $category->update([
            'form_title' => $request->form_title,
            'form_description' => $request->form_description,
            'edit_limit' => $request->edit_limit,
            'structure' => $structure,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Struktur formulir berhasil disimpan.');
    }

    /**
     * Helper: Generator Slug Unik
     */
    private function generateUniqueSlug($nama, $ignoreId = null)
    {
        $slug = Str::slug($nama);
        $originalSlug = $slug;
        $counter = 1;

        while (
            Layanan::when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }
}