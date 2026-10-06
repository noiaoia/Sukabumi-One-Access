<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint; // Import model Pengaduan

class LacakController extends Controller
{
    /**
     * 1. Menampilkan halaman Riwayat Pengaduan Warga
     * Digunakan oleh route: user.riwayat.pengaduan
     */
    public function index()
    {
        // Mengambil data pengaduan milik warga yang sedang login
        $complaints = Complaint::where('user_id', Auth::id())
                        ->latest()
                        ->get();

        // Mengarah ke file: resources/views/pages/user/riwayat-pengaduan.blade.php
        return view('pages.user.riwayat-pengaduan', compact('complaints'));
    }

    /**
     * 2. Ambil data JSON detail pengaduan untuk Modal Preview
     * Digunakan oleh route: user.riwayat.pengaduan.json
     */
    public function jsonDetail($id)
    {
        // Pastikan warga hanya bisa melihat detail pengaduan miliknya sendiri
        $complaint = Complaint::where('user_id', Auth::id())
                        ->findOrFail($id);

        return response()->json($complaint);
    }

    public function notifications()
    {
        $userId = Auth::id();

        // 1. Ambil Update Pengaduan (yang statusnya bukan pending)
        $complaints = Complaint::where('user_id', $userId)
            ->whereIn('status', ['review', 'process', 'done', 'rejected'])
            ->get()
            ->map(function($item) {
                $item->notif_type = 'complaint';
                return $item;
            });

        // 2. Ambil Update Pengajuan Berkas
        $submissions = \App\Models\User\Pengajuan::where('user_id', $userId)
            ->with('layanan')
            ->get()
            ->map(function($item) {
                $item->notif_type = 'submission';
                return $item;
            });

        // 3. Gabungkan dan Urutkan berdasarkan update terbaru
        $allNotifications = $complaints->concat($submissions)
            ->sortByDesc('updated_at')
            ->values();

        return view('pages.user.notifications', compact('allNotifications'));
    }
}