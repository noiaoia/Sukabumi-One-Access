<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  <-- Menggunakan variadic (...) agar bisa banyak role
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah sudah login?
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. PROTEKSI EKSTRA: Cek status akun (Suspended) secara Real-time
        // Jika admin menangguhkan akun saat user masih login, request berikutnya langsung ditendang
        if ($user->status === 'suspended') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors([
                'login' => 'Akses ditolak. Akun Anda telah ditangguhkan oleh administrator.'
            ]);
        }

        // 3. Cek apakah role user ada di dalam daftar role yang diizinkan (in_array)
        if (!in_array($user->role, $roles)) {
            // Jika role tidak cocok, kasih 403
            abort(403, 'Maaf, Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}