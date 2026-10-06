<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache; // <--- TAMBAHKAN INI
use Illuminate\Support\Facades\Mail;  // <--- TAMBAHKAN INI
use Illuminate\Validation\Rule;
use App\Mail\AdminOTPMail; // <--- PASTIKAN INI JUGA ADA

class OperatorController extends Controller
{
    /**
     * Helper untuk mencatat aktivitas ke database
     */
    private function logAction($actorId, $activity, $description, $request)
    {
        ActivityLog::create([
            'user_id'       => $actorId,
            'activity_name' => $activity,
            'description'   => $description,
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);
    }

    /**
     * Validasi portal agar admin hanya bisa mengelola portalnya sendiri.
     */
    private function validatePortal($portal_code) {
        if ($portal_code !== 'portal-' . Auth::user()->username) {
            abort(404);
        }
    }

    /**
     * Tampilkan Daftar Operator dengan Eager Loading Activities
     */
    public function index($portal_code)
    {
        $this->validatePortal($portal_code);

        $operators = User::where('role', 'super_admin')
                        ->with(['activities' => function($query) {
                            $query->latest()->limit(5);
                        }])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('pages.admin.operator.index', compact('operators', 'portal_code'));
    }

    /**
     * Form Tambah Operator
     */
    public function create($portal_code)
    {
        $this->validatePortal($portal_code);
        return view('pages.admin.operator.create', compact('portal_code'));
    }

    /**
     * Simpan Operator Baru & Catat Log Awal
     */
    public function store(Request $request, $portal_code)
    {
        $this->validatePortal($portal_code);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:20|unique:users,phone',
            'instansi' => 'required|string|max:255',
            'password' => 'required|min:8|confirmed',
        ]);

        // Cek apakah email sudah diverifikasi via session
        if (session('verified_operator_email') !== $request->email) {
            return back()->withErrors(['email' => 'Silakan lakukan verifikasi email terlebih dahulu.'])->withInput();
        }

        $instansi = trim($request->instansi);

        $newOperator = User::create([
            'name'              => $request->name,
            'username'          => strtolower($request->username),
            'email'             => $request->email,
            'phone'             => $request->phone,
            'instansi'          => $instansi,
            'password'          => Hash::make($request->password),
            'role'              => 'super_admin',
            'email_verified_at' => now(),
        ]);

        // Bersihkan session verifikasi setelah akun dibuat
        session()->forget('verified_operator_email');

        $this->logAction(
            Auth::id(),
            'Registrasi Otoritas',
            'Berhasil membuat akun operator ' . $newOperator->name,
            $request
        );

        return redirect()->route('admin.operator.index', $portal_code)
            ->with('success', 'Operator ' . $request->name . ' berhasil didaftarkan.');
    }

    /**
     * Update Data Operator
     */
    public function update(Request $request, $portal_code, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'    => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'instansi' => 'required|string',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->update([
            'name'     => $request->name,
            'username' => strtolower($request->username),
            'email'    => $request->email,
            'phone'    => $request->phone,
            'instansi' => $request->instansi,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $this->logAction(
            Auth::id(),
            'Pembaruan Data',
            'Memperbarui data operator ' . $user->name,
            $request
        );

        return redirect()->route('admin.operator.index', $portal_code)
            ->with('success', 'Data operator ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Hapus Operator
     */
    public function destroy($portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $user = User::findOrFail($id);
        $namaOperator = $user->name;
        $user->delete();

        return redirect()->route('admin.operator.index', $portal_code)
            ->with('success', 'Akses operator ' . $namaOperator . ' telah dicabut.');
    }

    /**
     * Verifikasi Email: Kirim OTP
     */
    public function sendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:users,email'], [
            'email.unique' => 'Email ini sudah terdaftar sebagai pengguna aktif.'
        ]);

        $otp = rand(100000, 999999);
        
        // Simpan OTP di cache selama 5 menit
        Cache::put('otp_operator_' . $request->email, $otp, now()->addMinutes(5));

        // Kirim Email
        Mail::to($request->email)->send(new AdminOTPMail($otp));

        return response()->json(['success' => true, 'message' => 'Kode OTP berhasil dikirim ke ' . $request->email]);
    }

    /**
     * Verifikasi Email: Cek OTP
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        $cachedOtp = Cache::get('otp_operator_' . $request->email);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            session(['verified_operator_email' => $request->email]);
            return response()->json(['success' => true, 'message' => 'Email berhasil diverifikasi!']);
        }

        return response()->json(['success' => false, 'message' => 'Kode OTP salah atau sudah kedaluwarsa.'], 422);
    }
}