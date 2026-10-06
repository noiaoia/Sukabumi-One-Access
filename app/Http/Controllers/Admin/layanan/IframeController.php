<?php

namespace App\Http\Controllers\Admin\layanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Instansi;
use App\Models\Admin\Layanan;

class IframeController extends Controller
{
    /**
     * Helper untuk memvalidasi akses portal (Security Check)
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
     * Daftar Layanan Iframe
     */
    public function manage($portal_code, $instansi_slug)
    {
        $instansi = Instansi::where('slug', $instansi_slug)->firstOrFail();

        $this->validateInstansiAccess($instansi);

        $layananList = Layanan::with('instansi')
            ->where('instansi_id', $instansi->id)
            ->where('jenis', 'iframe')
            ->orderBy('urutan')
            ->get();

        return view('pages.admin.layanan.iframe.manage', compact(
            'portal_code',
            'instansi',
            'layananList'
        ));
    }

    /**
     * Form Tambah Layanan Iframe
     */
    public function create($portal_code, Request $request)
    {
        $this->validatePortal($portal_code);

        $instansi_slug = $request->query('instansi');
        $instansi = Instansi::where('slug', $instansi_slug)->firstOrFail();

        $this->validateInstansiAccess($instansi);

        return view('pages.admin.layanan.iframe.create', compact(
            'portal_code',
            'instansi',
            'instansi_slug'
        ));
    }

    /**
     * Simpan Layanan Baru
     */
    public function store(Request $request, $portal_code)
    {
        $this->validatePortal($portal_code);

        $validated = $request->validate([
            'instansi_id' => 'required|exists:instansis,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'icon' => 'required|string|max:100',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'primary_color' => ['required', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'secondary_color' => ['required', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'iframe_url' => 'required|url',
            'jenis' => 'required|in:iframe,form',
            'status' => 'required|in:aktif,draft',
        ], [
            'nama.required' => 'Nama layanan wajib diisi.',
            'primary_color.regex' => 'Format warna primary harus kode HEX (misal: #FFFFFF).',
            'secondary_color.regex' => 'Format warna secondary harus kode HEX (misal: #FFFFFF).',
            'iframe_url.url' => 'Alamat URL aplikasi tidak valid.',
            'cover.max' => 'Ukuran foto cover maksimal 2MB.',
        ]);

        $instansi = Instansi::findOrFail($validated['instansi_id']);

        $this->validateInstansiAccess($instansi);

        if ($request->hasFile('cover')) {
            $validated['cover'] = $request->file('cover')->store('layanan/cover', 'public');
        }

        $validated['slug'] = $this->generateUniqueSlug($validated['nama']);
        $validated['urutan'] = (Layanan::where('instansi_id', $validated['instansi_id'])->max('urutan') ?? 0) + 1;

        Layanan::create($validated);

        return redirect()->route('admin.layanan.iframe.manage', [$portal_code, $instansi->slug])
            ->with('success', 'Layanan Iframe berhasil ditambahkan.');
    }

    /**
     * Visual Layanan
     */
    public function visual($portal_code, $id)
    {
        $this->validatePortal($portal_code);

        $svc = Layanan::with('instansi')->findOrFail($id);

        $this->validateInstansiAccess($svc->instansi);

        $instansi_slug = $svc->instansi->slug;

        return view('pages.admin.layanan.iframe.visual', compact(
            'portal_code',
            'svc',
            'instansi_slug'
        ));
    }

    /**
     * Editor Layanan
     */
    public function editor($portal_code, $id)
    {
        $this->validatePortal($portal_code);

        $svc = Layanan::with('instansi')->findOrFail($id);

        $this->validateInstansiAccess($svc->instansi);

        $instansi_slug = $svc->instansi->slug;

        return view('pages.admin.layanan.iframe.editor', compact(
            'portal_code',
            'svc',
            'instansi_slug'
        ));
    }

    /**
     * Update Layanan (General)
     */
    public function update(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);

        $layanan = Layanan::with('instansi')->findOrFail($id);

        $this->validateInstansiAccess($layanan->instansi);

        $validated = $request->validate([
            'nama'             => 'required|string|max:255',
            'deskripsi'        => 'nullable|string|max:1000',
            'icon'             => 'required|string|max:100',
            'cover'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'primary_color'    => ['required', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'secondary_color'  => ['required', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'iframe_url'       => 'required|url',
            'jenis'            => 'required|in:iframe,form',
        ]);

        if ($layanan->nama !== $validated['nama']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['nama'], $layanan->id);
        }

        if ($request->hasFile('cover')) {
            if ($layanan->cover) Storage::disk('public')->delete($layanan->cover);
            $validated['cover'] = $request->file('cover')->store('layanan/cover', 'public');
        }

        $layanan->update($validated);

        return redirect()->back()->with('success', 'Perubahan layanan berhasil diperbarui.');
    }

    /**
     * Update Visual Identity
     */    
    public function updateVisual(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);

        $layanan = Layanan::with('instansi')->findOrFail($id);

        $this->validateInstansiAccess($layanan->instansi);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'icon' => 'required|string|max:100',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'primary_color' => ['required', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);

        if ($layanan->nama != $validated['nama']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['nama'], $layanan->id);
        }

        if ($request->hasFile('cover')) {
            if ($layanan->cover) {
                Storage::disk('public')->delete($layanan->cover);
            }

            $validated['cover'] = $request->file('cover')->store('layanan/cover', 'public');
        }

        $layanan->update($validated);

        return back()->with('success', 'Visual Card berhasil diperbarui.');
    }

    /**
     * Update Iframe URL (Bridge)
     */
    public function updateEditor(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);

        $layanan = Layanan::with('instansi')->findOrFail($id);

        $this->validateInstansiAccess($layanan->instansi);

        $validated = $request->validate([
            'iframe_url' => 'required|url',
        ], [
            'iframe_url.required' => 'URL Aplikasi wajib diisi.',
            'iframe_url.url' => 'Format URL tidak valid (Gunakan https://).',
        ]);

        $layanan->update(['iframe_url' => $validated['iframe_url']]);

        return back()->with('success', 'Konfigurasi Iframe berhasil diperbarui.');
    }

    /**
     * Update Status Aktif/Draft
     */
    public function updateStatus(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);

        $layanan = Layanan::with('instansi')->findOrFail($id);

        $this->validateInstansiAccess($layanan->instansi);

        $request->validate([
            'status' => 'required|in:aktif,draft',
        ]);

        $layanan->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui.',
        ]);
    }

    /**
     * Hapus Layanan
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

        return redirect()->back()->with('success', 'Layanan berhasil dihapus.');
    }

    /**
     * Logic Generator Slug Unik
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