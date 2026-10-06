<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Crypt; // Tambahkan ini
use App\Models\PendingRegistration;
use App\Mail\VerifyRegistrationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\AdminOTPMail;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'                => 'required|string',
            'password'             => 'required|string',
            'g-recaptcha-response' => 'required',
        ], [
            'login.required'                => 'Username wajib diisi.',
            'password.required'             => 'Password wajib diisi.',
            'g-recaptcha-response.required' => 'Harap verifikasi bahwa Anda bukan robot.',
        ]);

        // Verifikasi reCAPTCHA
        $response = Http::withoutVerifying()->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        if (!$response->json('success')) {
            return back()->withErrors(['login' => 'Verifikasi reCAPTCHA gagal.'])->onlyInput('login');
        }

        $credentials = [
            'username' => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // 1. CEK STATUS SUSPEND (Berlaku untuk semua pintu)
            if ($user->status === 'suspended') {
                Auth::logout();
                return back()->withErrors(['login' => 'Akun Anda telah ditangguhkan.'])->onlyInput('login');
            }

            // 2. ISOLASI ROLE: Hanya 'user' yang boleh lewat pintu ini
            // Jika Super Admin atau Admin Dinas mencoba login di sini, mereka ditolak.
            if ($user->role !== 'user') {
                Auth::logout(); 
                return back()->withErrors([
                    'login' => 'Akses ditolak. Silakan gunakan pintu masuk administrator.'
                ])->onlyInput('login');
            }

            // 3. Cek verifikasi email
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors(['login' => 'Akun Anda belum aktif. Silakan verifikasi email terlebih dahulu.']);
            }

            $request->session()->regenerate();
            session(['user_name' => $user->name]);

            return redirect()->route('home')->with('success', 'Selamat Datang Kembali!');
        }

        return back()->withErrors(['login' => 'Username atau password salah.'])->onlyInput('login');
    }

    public function register(Request $request)
    {
        // Bersihkan akun pending yang sudah kedaluwarsa
        PendingRegistration::where('expired_at', '<=', now())->delete();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email|unique:pending_registrations,email',
            'phone'    => 'required|string|max:20|unique:users,phone|unique:pending_registrations,phone',
            'username' => 'required|string|max:20|unique:users,username|unique:pending_registrations,username|alpha_dash',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.unique'    => 'Email sudah terdaftar atau sedang dalam proses verifikasi.',
            'username.unique' => 'Username sudah digunakan.',
        ]);

        $token = Str::random(64);

        PendingRegistration::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'username'    => strtolower($request->username),
            'password'    => Hash::make($request->password),
            'token'       => $token,
            'expired_at'  => now()->addHour(),
        ]);

        session(['pending_email' => $request->email]);

        $url = route('register.verify', ['token' => $token]);

        Mail::to($request->email)->send(
            new VerifyRegistrationMail($url, $request->name)
        );

        return redirect()->route('register.success');
    }

    public function resendVerification(Request $request)
    {
        $email = session('pending_email');

        if (!$email) {
            return back()->withErrors([
                'login' => 'Sesi berakhir, silakan lakukan pendaftaran ulang.'
            ]);
        }

        $pending = PendingRegistration::where('email', $email)->first();

        if (!$pending) {
            return back()->withErrors([
                'login' => 'Data pendaftaran tidak ditemukan.'
            ]);
        }

        if ($pending->expired_at->isPast()) {
            $pending->delete();
            session()->forget('pending_email');

            return redirect()->route('register')
                ->withErrors([
                    'login' => 'Masa verifikasi telah habis. Silakan daftar kembali.'
                ]);
        }

        $pending->update([
            'token' => Str::random(64),
            'expired_at' => now()->addHour(),
        ]);

        $url = route('register.verify', ['token' => $pending->token]);

        Mail::to($email)->send(
            new VerifyRegistrationMail($url, $pending->name)
        );

        return back()->with('success', 'Link verifikasi baru berhasil dikirim. Masa berlaku diperpanjang 1 jam.');
    }

    public function verifyAccount($token)
    {
        $pending = PendingRegistration::where('token', $token)->first();

        if (!$pending) {
            return redirect()->route('login')
                ->withErrors([
                    'login' => 'Link verifikasi tidak valid atau sudah kedaluwarsa.'
                ]);
        }

        if ($pending->expired_at->isPast()) {
            $pending->delete();
            session()->forget('pending_email');

            return redirect()->route('login')
                ->withErrors([
                    'login' => 'Link verifikasi telah kedaluwarsa. Silakan daftar kembali.'
                ]);
        }

        User::create([
            'name'              => $pending->name,
            'email'             => $pending->email,
            'phone'             => $pending->phone,
            'username'          => $pending->username,
            'password'          => $pending->password,
            'email_verified_at' => now(),
            'role'              => 'user',
        ]);

        $pending->delete();
        session()->forget('pending_email');

        return redirect()->route('register.verified');
    }

    // Sisanya (showProfile, updateProfile, showSecurity, updatePassword, logout)
    // --- SISANYA (Profile, Security, Logout) TETAP SAMA ---

    public function showProfile()
    {
        return view('pages.user.profile');
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user(); 
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $dataUpdate = ['name' => $request->name, 'email' => $request->email, 'phone' => $request->phone];

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) { Storage::disk('public')->delete($user->profile_photo); }
            $dataUpdate['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        $user->update($dataUpdate);
        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

    public function showSecurity()
    {
        return view('pages.user.security');
    }

    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password saat ini salah.'], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        return response()->json(['success' => true, 'message' => 'Kata sandi berhasil diperbarui!']);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $user->last_seen = null;
            $user->save();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar.');
    }

    /**
     * Tampilkan Halaman Login Khusus Admin
     */
    public function showAdminLogin()
    {
        // Hanya izinkan jika sudah lewat gate rahasia
        // if (!session('admin_gate_passed')) {
            // abort(404);
        // }
        return view('auth.admin-login');
    }

    /**
     * Eksekusi Login Khusus Admin (Super Admin & Admin Dinas)
     */
    public function adminLogin(Request $request)
    {
        $otpInput = implode('', $request->otp); 
        if (session('admin_attempt_otp') && $otpInput == session('admin_attempt_otp')) {
            $user = User::find(session('admin_attempt_user'));
            
            if ($user) {
                // 1. Validasi role (Tambahkan operator)
                if (!in_array($user->role, ['super_admin', 'admin_dinas', 'operator'])) {
                    return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
                }

                // 2. Lakukan login
                Auth::login($user, $request->filled('remember'));

                // 3. Tentukan Portal Code (Logika Menumpang Portal)
                if ($user->role === 'operator') {
                    // Cari Admin Dinas yang satu instansi dengan operator ini
                    $admin = User::where('instansi_id', $user->instansi_id)
                                ->where('role', 'admin_dinas')
                                ->first();
                    // Jika admin tidak ketemu, fallback ke username sendiri (opsional)
                    $targetUsername = $admin ? $admin->username : $user->username;
                    $portalCode = 'portal-' . $targetUsername;
                } else {
                    // Jika Super Admin atau Admin Dinas, pakai username sendiri
                    $portalCode = 'portal-' . $user->username;
                }

                // 4. Update Multi-Session
                $activeAdmins = session('active_admins', []);
                $activeAdmins[$portalCode] = $user->id;
                session(['active_admins' => $activeAdmins]);

                // 5. Tentukan URL redirect
                $redirectUrl = route('admin.dashboard', ['portal_code' => $portalCode]);

                // 6. Bersihkan session OTP
                session()->forget(['admin_attempt_otp', 'admin_attempt_user', 'admin_gate_passed']);
                
                return response()->json([
                    'success' => true, 
                    'redirect' => $redirectUrl
                ]);
            }
        }
        return response()->json(['success' => false, 'message' => 'Kode OTP salah.'], 422);
    }

    public function verifyAdminGate($token)
    {
        try {
            // Sekarang Crypt akan dikenali karena sudah di-import di atas
            $decrypted = Crypt::decryptString($token);
            
            $parts = explode('|', $decrypted);
            if (count($parts) !== 2) abort(404);

            $key = $parts[0];
            $createdAt = Carbon::parse($parts[1]);

            // Cek kunci .env dan masa berlaku link (5 menit)
            if ($key === env('ADMIN_GATE_KEY') && $createdAt->diffInMinutes(now()) <= 5) {
                session(['admin_gate_passed' => true]);
                return redirect()->route('admin.login');
            }
            abort(404);
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function sendAdminOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Username dan Password wajib diisi.'], 422);
        }

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($loginType, $request->login)->first();

        // 1. Cek User & Password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Kredensial tidak valid.'], 401);
        }

        // 2. Cek Role (Hanya Super Admin & Admin Dinas)
        if (!in_array($user->role, ['super_admin', 'admin_dinas', 'operator'])) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki otoritas akses.'], 403);
        }

        // 3. Generate OTP & Simpan di Session
        $otp = rand(100000, 999999);
        session([
            'admin_attempt_otp'  => $otp,
            'admin_attempt_user' => $user->id,
            'admin_attempt_time' => now(),
        ]);

        // 4. Kirim Email (Pastikan Mailer sudah dikonfigurasi)
        Mail::to($user->email)->send(new AdminOTPMail($otp));

        return response()->json([
            'success' => true, 
            'message' => 'OTP telah dikirim ke email ' . substr($user->email, 0, 3) . '***' . strstr($user->email, '@')
        ]);
    }

    public function resendAdminOTP(Request $request)
    {
        $userId = session('admin_attempt_user');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi login telah berakhir. Silakan login kembali.'
            ], 401);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Data administrator tidak ditemukan.'
            ], 404);
        }

        if (!in_array($user->role, ['super_admin', 'admin_dinas', 'operator'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki otoritas akses.'
            ], 403);
        }

        $lastSent = session('admin_attempt_time');

        if ($lastSent && Carbon::parse($lastSent)->diffInSeconds(now()) < 30) {
            return response()->json([
                'success' => false,
                'message' => 'Tunggu beberapa detik sebelum meminta kode baru.'
            ], 429);
        }

        $otp = rand(100000, 999999);

        session([
            'admin_attempt_otp' => $otp,
            'admin_attempt_user' => $user->id,
            'admin_attempt_time' => now(),
        ]);

        Mail::to($user->email)->send(new AdminOTPMail($otp));

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP baru berhasil dikirim ke email ' . substr($user->email, 0, 3) . '***' . strstr($user->email, '@')
        ]);
    }

    /**
     * Menampilkan Form Lupa Password
     */
    public function showForgotPasswordForm()
    {
        return view('auth.admin-forgot-password');
    }

    /**
     * Tahap 1: Cek identitas (Email/User) dan kirim OTP
     */
    public function forgotPasswordVerifyIdentity(Request $request)
    {
        $loginType = filter_var($request->user_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($loginType, $request->user_id)->first();

        // Pastikan user ada dan merupakan Admin/Super Admin
        if ($user && in_array($user->role, ['super_admin', 'admin_dinas', 'operator'])) {
            $otp = rand(100000, 999999);
            
            session([
                'recovery_user_id' => $user->id,
                'recovery_otp'     => $otp,
                'recovery_step'    => 2
            ]);

            // Kirim Email
            Mail::to($user->email)->send(new AdminOTPMail($otp));

            return response()->json(['success' => true, 'message' => 'OTP dikirim ke email ' . substr($user->email, 0, 3) . '***' . strstr($user->email, '@')]);
        }

        return response()->json(['success' => false, 'message' => 'Identitas admin tidak ditemukan.'], 404);
    }

    /**
     * Tahap 2: Cek Kode OTP
     */
    public function forgotPasswordVerifyOTP(Request $request)
    {
        $otpInput = implode('', $request->otp);

        if (session('recovery_otp') && $otpInput == session('recovery_otp')) {
            session(['recovery_step' => 3]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Kode OTP salah atau kedaluwarsa.'], 422);
    }

    /**
     * Tahap 3: Simpan Password Baru
     */
    public function forgotPasswordUpdate(Request $request)
    {
        if (session('recovery_step') !== 3) {
            return response()->json(['success' => false, 'message' => 'Akses ilegal.'], 403);
        }

        $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::find(session('recovery_user_id'));
        if ($user) {
            $user->update(['password' => Hash::make($request->password)]);
            
            // Bersihkan Sesi
            session()->forget(['recovery_user_id', 'recovery_otp', 'recovery_step']);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal memperbarui data.'], 500);
    }

}