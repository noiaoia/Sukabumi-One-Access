<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Jalankan request dulu sampai selesai (biar MultiAdminMiddleware tukar user dulu)
        $response = $next($request);

        // 2. Baru cek Auth setelah user ditukar
        if (Auth::check()) {
            /** @var User $currentUser */
            $currentUser = Auth::user();

            // Kita update untuk semua role agar semua status (termasuk warga/user) akurat
            // updateQuietly agar tidak merubah kolom 'updated_at' (biar tidak merusak log sistem)
            $currentUser->updateQuietly([
                'last_seen' => now()
            ]);
        }
        
        return $response;
    }
}