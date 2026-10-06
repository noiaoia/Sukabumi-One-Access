<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminOTPMail;

class OperatorDinasController extends Controller
{
    private function logAction($activity, $description, $request)
    {
        ActivityLog::create([
            'user_id'       => Auth::id(),
            'activity_name' => $activity,
            'description'   => $description,
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);
    }

    private function validatePortal($portal_code) {
        $user = Auth::user();

        // 1. Jika Super Admin, bebas akses
        if ($user->role === 'super_admin') {
            return;
        }

        // 2. Jika Admin Dinas, portal harus sesuai dengan usernamenya sendiri
        if ($user->role === 'admin_dinas') {
            if ($portal_code !== 'portal-' . $user->username) {
                abort(404);
            }
            return;
        }

        // 3. JIKA USER ADALAH OPERATOR (STAF)
        if ($user->role === 'operator') {
            // Cari siapa Admin Dinas (atasan) di instansi yang sama dengan operator ini
            $adminDinas = User::where('instansi_id', $user->instansi_id)
                            ->where('role', 'admin_dinas')
                            ->first();

            // Staf hanya boleh masuk jika URL portal sesuai dengan username Boss-nya
            if (!$adminDinas || $portal_code !== 'portal-' . $adminDinas->username) {
                abort(404);
            }
            return;
        }

        // Jika role lain mencoba masuk
        abort(403);
    }

    public function index($portal_code)
    {
        $this->validatePortal($portal_code);
        $admin = Auth::user();

        $operators = User::where('role', 'operator')
            ->where('instansi_id', $admin->instansi_id)
            ->with(['activities' => fn($q) => $q->latest()->limit(5)])
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('pages.admin.operator-dinas.index', compact('operators', 'portal_code'));
    }

    public function create($portal_code)
    {
        $this->validatePortal($portal_code);
        return view('pages.admin.operator-dinas.create', compact('portal_code'));
    }

    public function store(Request $request, $portal_code)
    {
        $this->validatePortal($portal_code);
        $admin = Auth::user();

        // 1. Validasi Data
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:20|unique:users,phone', // Tambahkan phone
            'password' => 'required|min:8|confirmed',
        ], [
            'username.unique' => 'Username ini sudah digunakan.',
            'email.unique'    => 'Email ini sudah terdaftar.',
            'phone.unique'    => 'Nomor WhatsApp ini sudah digunakan oleh akun lain.',
        ]);

        // 2. Simpan ke Database
        $newOperator = User::create([
            'name'              => $request->name,
            'username'          => strtolower($request->username),
            'email'             => $request->email,
            'phone'             => $request->phone, // Sinkronkan WhatsApp
            'password'          => Hash::make($request->password),
            'role'              => 'operator',
            'instansi_id'       => $admin->instansi_id, // Ambil dari admin yang sedang login
            'instansi'          => $admin->instansi,    // Ambil dari admin yang sedang login
            'status'            => 'active',
            'email_verified_at' => now(), // Karena sudah lewat proses OTP di View
        ]);

        // 3. Catat Log Aktivitas
        $this->logAction(
            'Registrasi Operator', 
            'Berhasil mendaftarkan staf pelaksana baru: ' . $newOperator->name . ' untuk instansi ' . $admin->instansi, 
            $request
        );

        // 4. Redirect ke Index dengan pesan sukses
        return redirect()->route('admin.operator-dinas.index', $portal_code)
            ->with('success', 'Operator ' . $request->name . ' berhasil ditambahkan ke sistem.');
    }

    public function sendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:users,email'], [
            'email.unique' => 'Email ini sudah terdaftar sebagai pengguna aktif.'
        ]);

        $otp = rand(100000, 999999);
        Cache::put('otp_operator_' . $request->email, $otp, now()->addMinutes(5));

        // Kirim Email
        Mail::to($request->email)->send(new AdminOTPMail($otp));

        return response()->json(['success' => true, 'message' => 'OTP terkirim.']);
    }

    public function verifyOTP(Request $request)
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required|numeric']);
        $cachedOtp = Cache::get('otp_operator_' . $request->email);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'OTP salah.'], 422);
    }

    public function update(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $admin = Auth::user();

        // 1. Cari operator dan pastikan dia milik instansi admin ini (Security)
        $operator = User::where('id', $id)
            ->where('instansi_id', $admin->instansi_id)
            ->where('role', 'operator')
            ->firstOrFail();

        // 2. Validasi Data
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($operator->id)],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($operator->id)],
            'phone'    => ['required', 'string', Rule::unique('users')->ignore($operator->id)],
            'password' => 'nullable|min:8|confirmed',
        ], [
            'username.unique' => 'Username sudah digunakan staf lain.',
            'email.unique'    => 'Email sudah terdaftar.',
            'phone.unique'    => 'Nomor WhatsApp sudah digunakan staf lain.',
            'password.confirmed' => 'Konfirmasi sandi tidak cocok.'
        ]);

        // 3. Update Data Utama
        $operator->update([
            'name'     => $request->name,
            'username' => strtolower($request->username),
            'email'    => $request->email,
            'phone'    => $request->phone, // Mengambil dari input hidden op_phone_hidden
        ]);

        // 4. Update Password (Hanya jika diisi)
        if ($request->filled('password')) {
            $operator->update([
                'password' => Hash::make($request->password)
            ]);
        }

        // 5. Catat Log
        $this->logAction(
            'Pembaruan Data', 
            'Memperbarui profil operator: ' . $operator->name . ' (ID: ' . $operator->username . ')', 
            $request
        );

        // 6. Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Data staf ' . $operator->name . ' berhasil diperbarui.');
    }

    public function destroy($portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $admin = Auth::user();

        $operator = User::where('id', $id)
            ->where('instansi_id', $admin->instansi_id)
            ->firstOrFail();

        $name = $operator->name;
        $operator->delete();

        $this->logAction('Hapus Akses', 'Mencabut akses operator: ' . $name, request());

        return redirect()->back()->with('success', 'Akses operator ' . $name . ' telah dicabut.');
    }
}