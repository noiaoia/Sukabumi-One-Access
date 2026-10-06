<?php

namespace App\Http\Controllers\Admin\Layanan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Layanan;
use App\Models\Admin\FormCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoriesAdminController extends Controller
{
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

    public function index($portal_code, $id)
    {
        $this->validatePortal($portal_code);

        // Ambil Layanan Induk
        $svc = Layanan::with('instansi')->findOrFail($id);

        // Pastikan layanan hanya bisa diakses oleh instansinya sendiri
        $this->validateInstansiAccess($svc->instansi);

        $instansi_slug = $svc->instansi->slug;

        // Ambil Kategori milik layanan ini
        $categories = FormCategory::where('layanan_id', $id)
            ->orderBy('urutan')
            ->get();

        return view('pages.admin.layanan.formulir.categories', compact(
            'portal_code',
            'svc',
            'categories',
            'instansi_slug'
        ));
    }

    public function create($portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $svc = Layanan::with('instansi')->findOrFail($id);
        $this->validateInstansiAccess($svc->instansi);

        return view('pages.admin.layanan.formulir.categories-create', compact('portal_code', 'svc'));
    }

    public function store(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);

        $svc = Layanan::with('instansi')->findOrFail($id);
        $this->validateInstansiAccess($svc->instansi);

        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'primary_color' => ['required', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'icon' => 'required|string|max:100',
        ]);

        $nextOrder = FormCategory::where('layanan_id', $id)->max('urutan') + 1;

        FormCategory::create([
            'layanan_id' => $id,
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama) . '-' . Str::random(5),
            'deskripsi' => $request->deskripsi,
            'primary_color' => $request->primary_color,
            'icon' => $request->icon,
            'urutan' => $nextOrder,
            'status' => 'draft'
        ]);

        return redirect()->route('admin.layanan.form.categories.index', [$portal_code, $id])
            ->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    /**
     * Update Data Kategori (Informasi Dasar & Visual)
     */
    public function update(Request $request, $portal_code, $id, $cat_id)
    {
        $this->validatePortal($portal_code);

        $svc = Layanan::with('instansi')->findOrFail($id);
        $this->validateInstansiAccess($svc->instansi);

        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'primary_color' => ['required', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'icon' => 'required|string|max:100',
        ], [
            'primary_color.regex' => 'Format warna harus kode HEX (misal: #FFFFFF).',
        ]);

        $category = FormCategory::where('layanan_id', $id)->findOrFail($cat_id);

        if ($category->nama !== $request->nama) {
            $category->slug = Str::slug($request->nama) . '-' . Str::random(5);
        }

        $category->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'primary_color' => $request->primary_color,
            'icon' => $request->icon,
        ]);

        return redirect()->back()->with('success', 'Kategori "' . $request->nama . '" berhasil diperbarui.');
    }

    /**
     * Update Status Kategori (Aktif/Draft) via AJAX
     */
    public function updateStatus(Request $request, $portal_code, $id, $cat_id)
    {
        $this->validatePortal($portal_code);

        $svc = Layanan::with('instansi')->findOrFail($id);
        $this->validateInstansiAccess($svc->instansi);

        $request->validate([
            'status' => 'required|in:aktif,draft',
        ]);

        $category = FormCategory::where('layanan_id', $id)->findOrFail($cat_id);
        $category->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status kategori berhasil diubah menjadi ' . $request->status
        ]);
    }

    /**
     * Hapus Kategori
     */
    public function destroy($portal_code, $id, $cat_id)
    {
        $this->validatePortal($portal_code);
        $svc = Layanan::with('instansi')->findOrFail($id);
        $this->validateInstansiAccess($svc->instansi);
        $category = FormCategory::where('layanan_id', $id)->findOrFail($cat_id);
        $category->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
    }
}