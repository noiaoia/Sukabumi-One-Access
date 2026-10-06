<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Admin\Instansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaduanAdminController extends Controller
{
    /**
     * Keamanan: Validasi agar admin hanya bisa mengakses portal sesuai username-nya.
     */
    private function validatePortal($portal_code) {
        if ($portal_code !== 'portal-' . Auth::user()->username) {
            abort(404);
        }
    }

    /**
     * Tampilkan Daftar Pengaduan Warga (Dashboard Pengaduan)
     */
    public function index(Request $request, $portal_code)
    {
        $this->validatePortal($portal_code);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Complaint::query();

        if ($user->isSuperAdmin()) {
            // Super Admin melihat seluruh pengaduan
            $query->latest();
            } elseif ($user->isAdminDinas() || $user->isOperator()) {
                $query->where('instansi_id', $user->instansi_id)
                    ->whereIn('status', ['review', 'process'])
                    ->latest();
            } else {
            abort(403, 'Anda tidak memiliki akses ke pengaduan.');
        }

        // Filter Status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Fitur Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('ticket_id', 'like', "%{$search}%");
            });
        }

        $complaints = $query->paginate(10)->withQueryString();

        $totalLaporan = (clone $query)->count();

        // Hanya Super Admin yang membutuhkan daftar instansi
        $instansiList = $user->isSuperAdmin()
            ? Instansi::where('status', 'aktif')->orderBy('nama')->get()
            : collect();

        return view('pages.admin.pengaduan.index', compact(
            'complaints',
            'portal_code',
            'totalLaporan',
            'instansiList'
        ));
    }

    /**
     * AJAX: Mengambil data detail pengaduan untuk diisi ke Modal PDF
     */
    public function jsonDetail($portal_code, $id)
    {
        $this->validatePortal($portal_code);
        
        // Cari data atau kirim 404 jika tidak ada
        $complaint = Complaint::findOrFail($id);

        // Mengembalikan seluruh data model sebagai JSON
        return response()->json($complaint);
    }

    /**
     * AJAX: Mengubah status menjadi 'review' secara otomatis saat modal dibuka
     */
    public function markAsReview($portal_code, $id)
    {
        $this->validatePortal($portal_code);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $complaint = Complaint::findOrFail($id);

        // Super Admin:
        // Pengaduan baru masuk dan berubah menjadi review
        if ($user->isSuperAdmin()) {
            if ($complaint->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengaduan sudah tidak berstatus pending.'
                ]);
            }

            $complaint->update([
                'status' => 'review',
                'is_read_user_notification' => false,
            ]);

            return response()->json([
                'success' => true,
                'new_status' => 'review'
            ]);
        }

        // Admin Dinas:
        // Hanya boleh melakukan review terhadap pengaduan
        // yang memang sudah diteruskan ke instansinya
        if ($user->isAdminDinas()) {
            if ($complaint->instansi_id !== $user->instansi_id) {
                abort(403, 'Pengaduan ini bukan milik instansi Anda.');
            }

            if (!in_array($complaint->status, ['review', 'process'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengaduan tidak dapat direview pada status saat ini.'
                ]);
            }

            return response()->json([
                'success' => true,
                'new_status' => $complaint->status
            ]);
        }

        abort(403, 'Anda tidak memiliki akses ke pengaduan ini.');
    }

    /**
     * Simpan Tanggapan Admin & Perbarui Status Laporan (Proses/Selesai/Tolak)
     */
    public function updateStatus(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $complaint = Complaint::findOrFail($id);

        if ($user->isSuperAdmin()) {
            $request->validate([
                'status' => 'required|in:review,process,done,rejected',
                'response_message' => 'required|string|min:5',
                'instansi_id' => 'nullable|exists:instansis,id',
            ], [
                'response_message.required' => 'Pesan balasan untuk warga wajib diisi.',
            ]);

            if ($complaint->status === 'pending' && $request->status === 'review') {
                $complaint->update([
                    'status' => 'review',
                    'response_message' => $request->response_message,
                    'is_read_user_notification' => false,
                ]);

                return redirect()->back()->with('success', 'Pengaduan ' . $complaint->ticket_id . ' berhasil ditinjau.');
            }

            if ($complaint->status === 'review' && $request->status === 'process') {
                if (!$request->instansi_id) {
                    return redirect()->back()->with('error', 'Silakan pilih instansi tujuan terlebih dahulu.');
                }

                $complaint->update([
                    'status' => 'process',
                    'instansi_id' => $request->instansi_id,
                    'response_message' => $request->response_message,
                    'is_read_notification' => false,
                    'is_read_user_notification' => false,
                ]);

                return redirect()->back()->with('success', 'Pengaduan ' . $complaint->ticket_id . ' berhasil diteruskan ke instansi terkait.');
            }

            if ($complaint->status === 'process' && $request->status === 'done') {
                $complaint->update([
                    'status' => 'done',
                    'response_message' => $request->response_message,
                    'is_read_user_notification' => false,
                ]);

                return redirect()->back()->with('success', 'Pengaduan ' . $complaint->ticket_id . ' berhasil diselesaikan.');
            }

            if (in_array($complaint->status, ['review', 'process']) && $request->status === 'rejected') {
                $complaint->update([
                    'status' => 'rejected',
                    'response_message' => $request->response_message,
                    'is_read_user_notification' => false,
                ]);

                return redirect()->back()->with('success', 'Pengaduan ' . $complaint->ticket_id . ' berhasil ditolak.');
            }

            return redirect()->back()->with('error', 'Perubahan status pengaduan tidak valid.');
        }

        if ($user->isAdminDinas() || $user->isOperator()) {
            if ($complaint->instansi_id !== $user->instansi_id) {
                abort(403, 'Pengaduan ini bukan milik instansi Anda.');
            }

            $request->validate([
                'status' => 'required|in:done,rejected',
                'response_message' => 'required|string|min:5',
            ], [
                'response_message.required' => 'Pesan tanggapan wajib diisi.',
            ]);

            if ($complaint->status === 'process' && $request->status === 'done') {
                $complaint->update([
                    'status' => 'done',
                    'response_message' => $request->response_message,
                    'is_read_user_notification' => false,
                ]);

                return redirect()->back()->with('success', 'Pengaduan ' . $complaint->ticket_id . ' berhasil disetujui dan diselesaikan.');
            }

            if ($complaint->status === 'process' && $request->status === 'rejected') {
                $complaint->update([
                    'status' => 'rejected',
                    'response_message' => $request->response_message,
                    'is_read_user_notification' => false,
                ]);

                return redirect()->back()->with('success', 'Pengaduan ' . $complaint->ticket_id . ' berhasil ditolak oleh instansi.');
            }

            return redirect()->back()->with('error', 'Pengaduan tidak dapat diproses pada status saat ini.');
        }

        abort(403, 'Anda tidak memiliki akses untuk mengubah pengaduan.');
    }

    /**
     * AJAX: Menghitung notifikasi laporan baru (Status: Pending)
     */
    public function notifCount($portal_code)
    {
        $this->validatePortal($portal_code);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | NOTIFIKASI PENGADUAN
        |--------------------------------------------------------------------------
        */

        if ($user->isSuperAdmin()) {

            // Super Admin mendapat notifikasi pengaduan baru
            $complaintQuery = Complaint::where('status', 'pending')
                ->where('is_read_notification', false);

            } elseif ($user->isAdminDinas() || $user->isOperator()) {
                $complaintQuery = Complaint::where('instansi_id', $user->instansi_id)
                    ->where('status', 'process')
                    ->where('is_read_notification', false);
            } else {

            return response()->json([
                'count' => 0,
                'latest' => null,
            ]);
        }


        $complaintCount = $complaintQuery->count();


        /*
        |--------------------------------------------------------------------------
        | NOTIFIKASI PENGAJUAN BERKAS
        |--------------------------------------------------------------------------
        |
        | Hanya Super Admin yang menerima notifikasi pengajuan baru.
        |
        */

        if ($user->isSuperAdmin()) {

            $submissionCount = \App\Models\User\Pengajuan::where('status', 'diajukan')
                ->where('is_read_notification', false)
                ->count();

        } else {

            $submissionCount = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL NOTIFIKASI
        |--------------------------------------------------------------------------
        */

        $totalCount = $complaintCount + $submissionCount;


        /*
        |--------------------------------------------------------------------------
        | NOTIFIKASI TERBARU
        |--------------------------------------------------------------------------
        */

        $latestComplaint = $complaintQuery
            ->latest()
            ->first();


        $latestSubmission = null;

        if ($user->isSuperAdmin()) {

            $latestSubmission = \App\Models\User\Pengajuan::where('status', 'diajukan')
                ->where('is_read_notification', false)
                ->latest()
                ->first();
        }


        /*
        |--------------------------------------------------------------------------
        | Tentukan notif mana yang paling baru
        |--------------------------------------------------------------------------
        */

        $latest = null;

        if ($latestComplaint && $latestSubmission) {

            $latest = $latestComplaint->created_at->greaterThan(
                $latestSubmission->created_at
            )
                ? $latestComplaint
                : $latestSubmission;

        } elseif ($latestComplaint) {

            $latest = $latestComplaint;

        } elseif ($latestSubmission) {

            $latest = $latestSubmission;
        }


        /*
        |--------------------------------------------------------------------------
        | Response JSON
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'count' => $totalCount,

            'latest' => $latest ? [

                'ticket_id' => $latest->ticket_id
                    ?? $latest->nomor_pengajuan,

                'title' => $latest->title
                    ?? 'Berkas Baru: ' . $latest->nomor_pengajuan,

                'status' => $latest->status,

                'time' => $latest->created_at->diffForHumans(),

            ] : null,
        ]);
    }

    /**
     * AJAX: Menandai notifikasi admin sudah dibaca
     */
    public function markNotificationsAsRead($portal_code)
    {
        $this->validatePortal($portal_code);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // SUPER ADMIN
        if ($user->isSuperAdmin()) {

            Complaint::where('status', 'pending')
                ->where('is_read_notification', false)
                ->update([
                    'is_read_notification' => true
                ]);

            // Pengajuan berkas hanya untuk Super Admin
            \App\Models\User\Pengajuan::where('status', 'diajukan')
                ->where('is_read_notification', false)
                ->update([
                    'is_read_notification' => true
                ]);
        }

        // ADMIN DINAS
        elseif ($user->isAdminDinas() || $user->isOperator()) {
            Complaint::where('instansi_id', $user->instansi_id)
                ->where('status', 'process')
                ->where('is_read_notification', false)
                ->update([
                    'is_read_notification' => true
                ]);
        }

        else {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Halaman Arsip: Menampilkan laporan yang sudah Selesai atau Ditolak
     */
    public function archive(Request $request, $portal_code)
    {
        $this->validatePortal($portal_code);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $complaints = Complaint::whereIn('status', ['done', 'rejected'])
                ->latest()
                ->get();

            $instansiList = Instansi::withCount(['layanans as archived_count' => function($query) {
                $query->whereHas('pengajuans', function($sq) {
                    $sq->whereIn('status', ['selesai', 'ditolak']);
                });
            }])->orderBy('nama')->get();

            $archivedSubmissions = \App\Models\User\Pengajuan::whereIn('status', ['selesai', 'ditolak'])
                ->with(['layanan', 'category', 'data'])
                ->latest()
                ->get();

        } elseif ($user->isAdminDinas() || $user->isOperator()) {
            $complaints = Complaint::where('instansi_id', $user->instansi_id)
                ->whereIn('status', ['done', 'rejected'])
                ->latest()
                ->get();

            $instansiList = Instansi::where('id', $user->instansi_id)
                ->withCount(['layanans as archived_count' => function($query) {
                    $query->whereHas('pengajuans', function($sq) {
                        $sq->whereIn('status', ['selesai', 'ditolak']);
                    });
                }])
                ->get();

            $archivedSubmissions = \App\Models\User\Pengajuan::whereIn('status', ['selesai', 'ditolak'])
                ->whereHas('layanan', function($query) use ($user) {
                    $query->where('instansi_id', $user->instansi_id);
                })
                ->with(['layanan', 'category', 'data'])
                ->latest()
                ->get();

        } else {
            abort(403, 'Anda tidak memiliki akses ke arsip.');
        }

        $totalArchivedSubmissions = $archivedSubmissions->count();

        return view('pages.admin.pengaduan.archive', compact(
            'complaints',
            'instansiList',
            'portal_code',
            'totalArchivedSubmissions',
            'archivedSubmissions'
        ));
    }
}