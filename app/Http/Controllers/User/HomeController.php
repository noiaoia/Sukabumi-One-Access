<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Instansi;

class HomeController extends Controller
{
    public function index()
    {
        $instansis = Instansi::where('status', 'aktif')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get();

        return view('pages.home', [
            'berkasAktif'     => false,
            'jumlahPengajuan' => 0,
            'instansis'       => $instansis,
        ]);
    }
}
