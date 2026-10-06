<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\Instansi;
use App\Models\Admin\Layanan;

class InstansiUserController extends Controller
{
    public function show($slug)
    {
        $instansi = Instansi::where('slug', $slug)
            ->where('status', 'aktif')
            ->firstOrFail();

        $layanans = Layanan::where('instansi_id', $instansi->id)
            ->where('status', 'aktif')
            ->orderBy('urutan')
            ->get();

        return view('pages.user.instansi.show', compact('instansi', 'layanans'));
    }

    public function layananCount($id)
    {
        $count = Layanan::where('instansi_id', $id)
            ->where('status', 'aktif')
            ->count();

        return response()->json([
            'count' => $count,
        ]);
    }
}