<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class MultiAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ambil portal_code dari URL (misal: portal-admin)
        $portalCode = $request->route('portal_code');
        
        // 2. Ambil daftar admin yang sudah login dari session
        $activeAdmins = session('active_admins', []);

        // 3. Jika portal_code di URL ada dalam daftar login kita
        if ($portalCode && isset($activeAdmins[$portalCode])) {
            $userId = $activeAdmins[$portalCode];
            
            // 4. Cari user-nya
            $user = User::find($userId);

            if ($user) {
                // Tukar identitas user secara otomatis untuk request ini jika berbeda
                if (Auth::id() != $userId) {
                    Auth::setUser($user);
                }

                // --- TAMBAHKAN KODE DI BAWAH INI ---
                // Setiap kali user portal ini mengakses halaman, update waktu terakhir dilihat
                $user->updateQuietly([
                    'last_seen' => now()
                ]);
                // -----------------------------------
            }
        }

        return $next($request);
    }
}