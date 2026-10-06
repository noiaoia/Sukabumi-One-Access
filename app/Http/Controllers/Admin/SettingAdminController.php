<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingAdminController extends Controller
{
    public function index($portal_code)
    {
        return view(
            'pages.admin.pengaturan.pengaturan-admin',
            compact('portal_code')
        );
    }

    public function updatePassword(Request $request, $portal_code)
    {
        // nanti kita isi

        return back()->with(
            'success',
            'Password berhasil diperbarui.'
        );
    }
}