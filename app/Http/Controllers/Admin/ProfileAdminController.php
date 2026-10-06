<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileAdminController extends Controller
{
    public function index($portal_code)
    {
        return view('pages.admin.pengaturan.profil-admin', compact('portal_code'));
    }

    public function update(Request $request, $portal_code)
    {
        // update data

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}