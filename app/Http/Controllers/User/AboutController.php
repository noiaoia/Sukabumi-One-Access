<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller; // Wajib diimport karena folder kita berbeda
use Illuminate\Http\Request;

class AboutController extends Controller
{
    /**
     * Menampilkan Halaman Tentang
     */
    public function index()
    {
        // Mengarah ke file resources/views/pages/user/about.blade.php
        return view('pages.user.about');
    }
}