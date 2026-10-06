<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Instansi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InstansiController extends Controller
{
    /**
     * Helper untuk memvalidasi akses portal
     */
    private function validatePortal($portal_code)
    {
        if ($portal_code !== 'portal-' . Auth::user()->username) {
            abort(404);
        }
    }

    private function getAuthorizedInstansi($id)
    {
        $user = Auth::user();

        if ($user->role === 'super_admin') {
            return Instansi::findOrFail($id);
        }

        return Instansi::where('id', $user->instansi_id)->firstOrFail();
    }

    public function index($portal_code)
    {
        $this->validatePortal($portal_code);

        $user = Auth::user();

        if ($user->role === 'super_admin') {
            $instansiList = Instansi::orderBy('urutan')
                ->orderBy('nama')
                ->get();
        } else {
            $instansiList = Instansi::where('id', $user->instansi_id)
                ->get();
        }

        return view('pages.admin.instansi.index', compact('portal_code', 'instansiList'));
    }

    public function create($portal_code)
    {
        $this->validatePortal($portal_code);

        if (Auth::user()->role !== 'super_admin') {
            abort(403, 'Anda tidak memiliki akses untuk menambahkan instansi.');
        }

        return view('pages.admin.instansi.create', compact('portal_code'));
    }

    public function store(Request $request, $portal_code)
    {
        $this->validatePortal($portal_code);

        if (Auth::user()->role !== 'super_admin') {
            abort(403, 'Anda tidak memiliki akses untuk menambahkan instansi.');
        }

        $request->validate([
            'nama'           => 'required|string|max:255',
            'slug'           => 'required|string|max:255|unique:instansis,slug',
            'kategori'       => 'required|string|max:100',
            'slogan'         => 'nullable|string|max:255',
            'about_title'    => 'nullable|string|max:255',
            'deskripsi'      => 'nullable|string',
            'primary_color'  => ['required', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'icon'           => 'required|string|max:100',
            'logo'           => 'required|image|mimes:png,jpg,jpeg,webp|max:2048',
            'cover'          => 'required|image|mimes:png,jpg,jpeg,webp|max:4096',
            'maps_embed'     => 'nullable|string',
            'alamat'         => 'nullable|string',
        ], [
            'nama.required'    => 'Nama instansi wajib diisi.',
            'slug.unique'      => 'Slug/URL instansi sudah digunakan, silakan pilih yang lain.',
            'primary_color.regex' => 'Format warna harus berupa kode HEX (contoh: #FFFFFF).',
            'logo.max'         => 'Ukuran logo tidak boleh lebih dari 2MB.',
            'cover.max'        => 'Ukuran foto cover tidak boleh lebih dari 4MB.',
        ]);

        $logo = $request->hasFile('logo') ? $request->file('logo')->store('instansi/logo', 'public') : null;
        $cover = $request->hasFile('cover') ? $request->file('cover')->store('instansi/cover', 'public') : null;

        Instansi::create([
            'nama'                   => $request->nama,
            'slug'                   => Str::slug($request->slug),
            'kategori'               => $request->kategori,
            'slogan'                 => $request->slogan,
            'about_title'            => $request->about_title,
            'deskripsi'              => $request->deskripsi,
            'logo'                   => $logo,
            'cover'                  => $cover,
            'icon'                   => $request->icon,
            'primary_color'          => $request->primary_color,
            'secondary_color'        => '#E8A33D',
            'icon_background_color'  => '#F8FAFC',
            'alamat'                 => $request->alamat,
            'maps_embed'             => $request->maps_embed,
            'status'                 => 'draft',
            'urutan'                 => 0,
        ]);

        return redirect()->route('admin.instansi.index', $portal_code)->with('success', 'Instansi berhasil ditambahkan.');
    }

    public function update(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $instansi = $this->getAuthorizedInstansi($id);

        $request->validate([
            'nama'           => 'required|string|max:255',
            'kategori'       => 'required|string|max:100',
            'slogan'         => 'nullable|string|max:255',
            'about_title'    => 'nullable|string|max:255',
            'deskripsi'      => 'nullable|string',
            'primary_color'  => ['required', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'icon'           => 'required|string|max:100',
            'logo'           => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'cover'          => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'maps_embed'     => 'nullable|string',
            'alamat'         => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            if ($instansi->logo) Storage::disk('public')->delete($instansi->logo);
            $instansi->logo = $request->file('logo')->store('instansi/logo', 'public');
        }

        if ($request->hasFile('cover')) {
            if ($instansi->cover) Storage::disk('public')->delete($instansi->cover);
            $instansi->cover = $request->file('cover')->store('instansi/cover', 'public');
        }

        $instansi->update([
            'nama'          => $request->nama,
            'kategori'      => $request->kategori,
            'slogan'        => $request->slogan,
            'about_title'   => $request->about_title,
            'deskripsi'     => $request->deskripsi,
            'primary_color' => $request->primary_color,
            'icon'          => $request->icon,
            'alamat'        => $request->alamat,
            'maps_embed'    => $request->maps_embed,
        ]);

        return redirect()->route('admin.instansi.index', $portal_code)->with('success', 'Data instansi berhasil diperbarui.');
    }

    public function updateCompanyProfile(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);
        
        $request->validate([
            'slogan'       => 'nullable|string|max:255',
            'stat1'        => 'nullable|string|max:50',
            'stat1_label'  => 'nullable|string|max:100',
            'stat2'        => 'nullable|string|max:50',
            'stat2_label'  => 'nullable|string|max:100',
            'stat3'        => 'nullable|string|max:50',
            'stat3_label'  => 'nullable|string|max:100',
            'about_title'  => 'nullable|string|max:255',
            'deskripsi'    => 'nullable|string',
            'maps_embed'   => 'nullable|string',
            'alamat'       => 'nullable|string|max:500',
            'logo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'stat1.max' => 'Angka statistik maksimal 50 karakter.',
        ]);

        $instansi = $this->getAuthorizedInstansi($id);

        if ($request->hasFile('logo')) {
            if ($instansi->logo) Storage::disk('public')->delete($instansi->logo);
            $instansi->logo = $request->file('logo')->store('instansi/logo', 'public');
            $instansi->save();
        }

        $instansi->update($request->except(['logo']));
        
        return back()->with('success', 'Company Profile berhasil diperbarui.');
    }

    public function updateVisualIdentity(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $instansi = $this->getAuthorizedInstansi($id);

        $request->validate([
            'name'              => 'required|string|max:255',
            'desc'              => 'nullable|string|max:255',
            'color'             => ['required', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'secondary_color'   => ['required', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'icon'              => 'required|string|max:100',
            'image'             => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            if ($instansi->cover) Storage::disk('public')->delete($instansi->cover);
            $instansi->cover = $request->file('image')->store('instansi/cover', 'public');
        }

        $instansi->update([
            'nama' => $request->name,
            'deskripsi_singkat' => $request->desc,
            'primary_color' => $request->color,
            'secondary_color' => $request->secondary_color,
            'icon' => $request->icon,
        ]);

        return back()->with('success', 'Visual Identity berhasil diperbarui.');
    }

    public function updateVisualLayanan(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $instansi = $this->getAuthorizedInstansi($id);

        $request->validate([
            'name'              => 'required|string|max:255',
            'cat'               => 'required|string|max:255',
            'primary_color'     => ['required', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'secondary_color'   => ['required', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'icon'              => 'required|string|max:100',
            'image'             => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            if ($instansi->cover) Storage::disk('public')->delete($instansi->cover);
            $instansi->cover = $request->file('image')->store('instansi/cover', 'public');
        }

        $instansi->update([
            'nama' => $request->name,
            'kategori' => $request->cat,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'icon' => $request->icon,
        ]);

        return back()->with('success', 'Visual Direktori berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);

        if (Auth::user()->role !== 'super_admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status instansi.');
        }

        $request->validate([
            'status' => 'required|in:aktif,draft',
        ]);

        Instansi::findOrFail($id)->update([
            'status' => $request->status
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $instansi = $this->getAuthorizedInstansi($id);

        if ($instansi->logo) Storage::disk('public')->delete($instansi->logo);
        if ($instansi->cover) Storage::disk('public')->delete($instansi->cover);

        $instansi->forceDelete();

        return redirect()->route('admin.instansi.index', $portal_code)->with('success', 'Instansi berhasil dihapus secara permanen.');
    }

    // Method view lainnya tetap sama...
    public function visualIdentity($portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $instansi = $this->getAuthorizedInstansi($id);
        return view('pages.admin.instansi.visual', compact('portal_code', 'instansi'));
    }

    public function visualLayanan($portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $instansi = $this->getAuthorizedInstansi($id);
        return view('pages.admin.instansi.visual-layanan', compact('portal_code', 'instansi'));
    }

    public function layananCount($portal_code, $id)
    {
        $this->validatePortal($portal_code);

        $instansi = $this->getAuthorizedInstansi($id);

        return response()->json([
            'count' => $instansi->layanans()->count(),
        ]);
    }

    public function companyProfile($portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $instansi = $this->getAuthorizedInstansi($id);
        return view('pages.admin.instansi.company-profile', compact('portal_code', 'instansi'));
    }

    /**
     * Reverse geocoding: menerima latitude & longitude dari frontend
     * (hasil ekstraksi dari Google Maps Embed URL), lalu mengambil
     * alamat lengkap via Nominatim (OpenStreetMap) — gratis, tanpa
     * API key, tanpa billing.
     *
     * Semua kegagalan (request gagal, koordinat tidak ditemukan)
     * dikembalikan sebagai JSON success:false dengan pesan yang
     * jelas — TIDAK PERNAH menampilkan halaman error Laravel,
     * sehingga admin tetap bisa mengisi alamat secara manual.
     */
    public function reverseGeocode(Request $request, $portal_code)
    {
        $this->validatePortal($portal_code);

        $validated = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    // Wajib oleh kebijakan Nominatim: identitas aplikasi + kontak.
                    // Ganti dengan domain/email instansi kamu yang sebenarnya.
                    'User-Agent' => 'PortalInstansiApp/1.0 (ranggis8089@gmail.com)',
                ])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat'             => $validated['latitude'],
                    'lon'             => $validated['longitude'],
                    'format'          => 'json',
                    'accept-language' => 'id',
                    'zoom'            => 18,
                ]);

            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghubungi layanan peta. Silakan isi alamat secara manual.',
                ], 502);
            }

            $data = $response->json();

            if (empty($data['display_name'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan untuk koordinat tersebut. Silakan isi manual.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'address' => $data['display_name'],
            ]);

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil alamat. Silakan isi manual.',
            ], 500);
        }
    }
}