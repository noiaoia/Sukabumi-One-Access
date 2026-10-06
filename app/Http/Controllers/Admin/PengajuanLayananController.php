<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User\Pengajuan;
use App\Models\Admin\Instansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanLayananController extends Controller
{
    private function validatePortal($portal_code)
    {
        if ($portal_code !== 'portal-' . Auth::user()->username) {
            abort(404);
        }
    }

    /**
     * TAHAP 1: Menampilkan Daftar Instansi (Gudang Berkas)
     */
    public function index(Request $request, $portal_code)
    {
        $this->validatePortal($portal_code);

        $user = Auth::user();

        $query = Instansi::query();

        if ($user->role === 'admin_dinas') {
            $query->where('id', $user->instansi_id);
        }

        $instansiList = $query
            ->withCount(['layanans as total_pengajuan' => function($q) {
                $q->whereHas('pengajuans');
            }])
            ->withCount(['layanans as total_pending' => function($q) {
                $q->whereHas('pengajuans', function($sq) {
                    $sq->where('status', 'diajukan');
                });
            }])
            ->orderBy('nama')
            ->get();

        return view('pages.admin.submission.index', compact(
            'instansiList',
            'portal_code'
        ));
    }

    /**
     * TAHAP 2: Menampilkan Daftar Pengajuan per Instansi
     */
    public function manage(Request $request, $portal_code, $instansi_id)
    {
        $this->validatePortal($portal_code);

        $instansi = Instansi::findOrFail($instansi_id);

        $query = Pengajuan::whereHas('layanan', function($q) use ($instansi_id) {
            $q->where('instansi_id', $instansi_id);
        })->with(['layanan', 'category', 'data']);

        // Filter: Pencarian Nomor Pengajuan
        if ($request->filled('search')) {
            $query->where('nomor_pengajuan', 'like', '%' . $request->search . '%');
        }

        // Filter: Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $pengajuans = $query->latest()->paginate(10)->withQueryString();

        return view('pages.admin.submission.manage', compact('pengajuans', 'portal_code', 'instansi'));
    }

    /**
     * AJAX: Ambil data untuk Modal Detail
     */
    public function jsonDetail($portal_code, $id)
    {
        $this->validatePortal($portal_code);
        // Tambahkan layanan.instansi agar data dinas muncul di modal detail
        $pengajuan = Pengajuan::with(['layanan.instansi', 'category', 'data', 'dokumen'])->findOrFail($id);
        return response()->json($pengajuan);
    }

    /**
     * Update Status Pengajuan
     */
    public function updateStatus(Request $request, $portal_code, $id)
    {
        // 1. Tambahkan 'perlu_revisi' di validasi
        // 2. Tambahkan 'catatan_admin' sebagai input opsional
        $request->validate([
            'status' => 'required|in:diajukan,review,diproses,perlu_revisi,selesai,ditolak',
            'catatan_admin' => 'nullable|string'
        ]);

        $pengajuan = Pengajuan::findOrFail($id);

        $updateData = ['status' => $request->status];

        // Jika ada catatan admin (dari modal perbaikan), masukkan ke database
        if ($request->filled('catatan_admin')) {
            $updateData['catatan_admin'] = $request->catatan_admin;
        }

        $pengajuan->update($updateData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Status berkas berhasil diperbarui.');
    }

    /**
     * Hapus Data
     */
    public function destroy($portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $pengajuan = Pengajuan::findOrFail($id);
        $pengajuan->delete();

        return redirect()->back()->with('success', 'Data pengajuan telah dihapus.');
    }
}