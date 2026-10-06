<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Instansi;

class LayananController extends Controller
{
    /**
     * Menampilkan Halaman Direktori Semua Layanan
     */
    public function index()
    {
        $instansis = Instansi::where('status', 'aktif')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get();

        return view('pages.user.layanan', compact('instansis'));
    }
}