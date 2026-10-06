<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationAdminController extends Controller
{
    private function validatePortal($portal_code) {
        if ($portal_code !== 'portal-' . Auth::user()->username) {
            abort(404);
        }
    }

    public function index(Request $request, $portal_code)
    {
        $this->validatePortal($portal_code);

        // 1. Ambil Pengaduan Baru (Pending)
        $complaints = Complaint::where('status', 'pending')
            ->select('id', 'title as subject', 'created_at', 'status', 'ticket_id as code')
            ->get()
            ->map(function($item) {
                $item->type = 'complaint';
                $item->icon = 'bi-megaphone-fill';
                $item->color = 'text-red-500';
                $item->bg = 'bg-red-50';
                return $item;
            });

        // 2. Ambil Pengajuan Baru (Diajukan)
        $submissions = Pengajuan::where('status', 'diajukan')
            ->with('layanan')
            ->get()
            ->map(function($item) {
                return (object)[
                    'id' => $item->id,
                    'subject' => "Berkas Masuk: " . ($item->layanan->nama ?? 'Layanan Mandiri'),
                    'created_at' => $item->created_at,
                    'status' => $item->status,
                    'code' => $item->nomor_pengajuan,
                    'type' => 'submission',
                    'icon' => 'bi-file-earmark-text-fill',
                    'color' => 'text-emerald-500',
                    'bg' => 'bg-emerald-50'
                ];
            });

        // 3. Gabungkan dan Urutkan
        $allNotifications = $complaints->concat($submissions)
            ->sortByDesc('created_at')
            ->values();

        return view('pages.admin.notifications.index', compact('portal_code', 'allNotifications'));
    }
}