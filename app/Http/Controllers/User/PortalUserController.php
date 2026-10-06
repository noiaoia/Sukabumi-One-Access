<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\Layanan;
use App\Models\LayananClick;

class PortalUserController extends Controller
{
    /**
     * Tampilkan Portal Layanan
     */
    public function show($slug)
    {
        $layanan = Layanan::with('instansi')
            ->where('slug', $slug)
            ->where('status', 'aktif')
            ->firstOrFail();

        // Catat klik "Buka Layanan" (dedup refresh ditangani di dalam recordIfNew)
        LayananClick::recordIfNew($layanan);

        return view(
            'pages.user.portal.show',
            compact('layanan')
        );
    }
}