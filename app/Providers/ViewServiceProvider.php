<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\User\Pengajuan; // <--- Import Model Pengajuan

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void { }

    public function boot(): void
    {
        // =============================================================
        // 1. ADMIN (NOTIFIKASI PENGADUAN & PENGAJUAN BARU)
        // =============================================================
        View::composer('layouts.admin', function ($view) {
            // Hitung Pengaduan Baru (Pending)
            $unreadComplaints = Complaint::where('status', 'pending')
                ->where('is_read_notification', false)
                ->count();

            // Hitung Pengajuan Berkas Baru (Diajukan)
            $unreadSubmissions = Pengajuan::where('status', 'diajukan')
                ->where('is_read_notification', false)
                ->count();

            // Ambil data terbaru untuk cuplikan dropdown
            $latestComplaint = Complaint::where('status', 'pending')
                ->where('is_read_notification', false)
                ->latest()->first();

            $latestSubmission = Pengajuan::where('status', 'diajukan')
                ->where('is_read_notification', false)
                ->latest()->first();

            $view->with([
                // TOTAL GABUNGAN (Untuk Badge Lonceng Header & Suara)
                'complaintNotificationCount' => $unreadComplaints + $unreadSubmissions, 

                // KHUSUS PENGADUAN (Untuk Sidebar Pengaduan - TAMBAHKAN INI)
                'unreadComplaintCount'       => $unreadComplaints, 

                // KHUSUS PENGAJUAN (Untuk Sidebar Pengajuan)
                'pendingSubmissionCount'     => $unreadSubmissions,

                'latestComplaint'            => $latestComplaint,
                'latestSubmission'           => $latestSubmission,
            ]);
        });

        // =============================================================
        // 2. USER (NOTIFIKASI UPDATE STATUS DARI ADMIN)
        // =============================================================
        View::composer(['layouts.app', 'partials.navbar'], function ($view) {

            if (!Auth::check()) {
                $view->with([
                    'userNotificationCount' => 0,
                    'userNotifications'     => collect(),
                ]);
                return;
            }

            $userId = Auth::id();

            // Query Update Status Pengaduan
            $complaintNotifs = Complaint::where('user_id', $userId)
                ->where('is_read_user_notification', false)
                ->whereIn('status', ['review', 'process', 'done', 'rejected'])
                ->get();

            // Query Update Status Pengajuan Berkas
            $submissionNotifs = Pengajuan::where('user_id', $userId)
                ->where('is_read_user_notification', false)
                ->whereIn('status', ['review', 'diproses', 'perlu_revisi', 'selesai', 'ditolak'])
                ->get();

            // Gabungkan kedua jenis notifikasi menjadi satu koleksi
            $allNotifications = collect()
                ->concat($complaintNotifs)
                ->concat($submissionNotifs)
                ->sortByDesc('updated_at') // Urutkan yang terbaru di atas
                ->take(10);

            $view->with([
                'userNotificationCount' => $allNotifications->count(),
                'userNotifications'     => $allNotifications,
            ]);
        });
    }
}