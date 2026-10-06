<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Instansi;

class LayananAdminController extends Controller
{
    public function index($portal_code)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($portal_code !== 'portal-' . $user->username) {
            abort(404);
        }

        if ($user->isSuperAdmin()) {
            $instansiList = Instansi::where('status', 'aktif')
                ->with('layanans')
                ->orderBy('urutan')
                ->orderBy('nama')
                ->get();
        } else {
            if (!$user->instansi_id) {
                abort(403, 'Akun Anda belum memiliki instansi.');
            }

            $instansiList = Instansi::where('id', $user->instansi_id)
                ->where('status', 'aktif')
                ->with('layanans')
                ->get();
        }

        return view('pages.admin.layanan.index', compact(
            'portal_code',
            'instansiList'
        ));
    }
}