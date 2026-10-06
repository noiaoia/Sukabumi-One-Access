<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Mail\AdminOTPMail;
use App\Models\Admin\Instansi;

class AdminDinasController extends Controller
{
    /**
     * Helper untuk mencatat aktivitas
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

    private function validatePortal($portal_code) {
        if ($portal_code !== 'portal-' . Auth::user()->username) {
            abort(404);
        }
    }

    public function index($portal_code)
    {
        $this->validatePortal($portal_code);

        // 1. Ambil data Admin Dinas
        $admins = User::where('role', 'admin_dinas')
            ->with(['activities' => function($q) { 
                $q->latest()->limit(5); 
            }])
            ->orderBy('name', 'asc')
            ->paginate(10);

        // 2. TAMBAHKAN INI: Ambil data instansi agar Modal Edit tidak error
        // Modal Edit membutuhkan variabel $instansiList untuk menampilkan pilihan dropdown
        $instansiList = Instansi::where('status', 'aktif')
            ->orderBy('nama', 'asc') 
            ->get();

        // 3. Kirim ke View (tambahkan instansiList di compact)
        return view('pages.admin.user-management.admin-dinas', compact('admins', 'portal_code', 'instansiList'));
    }

    public function create($portal_code)
    {
        $this->validatePortal($portal_code);
        $instansiList = Instansi::where('status', 'aktif')->orderBy('nama', 'asc')->get();
        return view('pages.admin.user-management.admin-dinas-create', compact('portal_code', 'instansiList'));
    }

    public function store(Request $request, $portal_code)
    {
        $this->validatePortal($portal_code);
        $request->merge(['username' => strtolower(trim($request->username))]);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'required|email|max:255|unique:users,email',
            'phone'    => 'required|string|max:20|unique:users,phone',
            'instansi' => 'required|exists:instansis,nama', 
            'password' => 'required|min:8|confirmed',
        ]);

        if (session('verified_admin_dinas_email') !== $request->email) {
            return back()->withErrors(['email' => 'Verifikasi email wajib diselesaikan.'])->withInput();
        }

        $instansiData = Instansi::where('nama', $request->instansi)->first();

        $newAdmin = User::create([
            'name'              => $request->name,
            'username'          => $request->username,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'instansi'          => $request->instansi,
            'instansi_id'       => $instansiData->id,
            'password'          => Hash::make($request->password),
            'role'              => 'admin_dinas',
            'email_verified_at' => now(),
        ]);

        session()->forget('verified_admin_dinas_email');
        $this->logAction(Auth::id(), 'Registrasi Admin Dinas', 'Mendaftarkan: ' . $newAdmin->name, $request);

        return redirect()->route('admin.admin-dinas.index', $portal_code)->with('success', 'Admin Dinas berhasil didaftarkan.');
    }

    // --- TAMBAHAN FUNGSI EDIT & UPDATE DI SINI ---

    /**
     * Menampilkan Form Edit
     */
    public function edit($portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $admin = User::findOrFail($id);
        $instansiList = Instansi::where('status', 'aktif')->orderBy('nama', 'asc')->get();

        return view('pages.admin.user-management.admin-dinas-edit', compact('admin', 'portal_code', 'instansiList'));
    }

    /**
     * Memproses Update Data
     */
    public function update(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'email'    => 'required|email|max:255|unique:users,email,' . $id,
            'phone'    => 'required|string|max:20|unique:users,phone,' . $id,
            'instansi' => 'required|exists:instansis,nama',
            'password' => 'nullable|min:8|confirmed', // Password opsional saat edit
        ]);

        $instansiData = Instansi::where('nama', $request->instansi)->first();

        $data = [
            'name'        => $request->name,
            'username'    => strtolower($request->username),
            'email'       => $request->email,
            'phone'       => $request->phone,
            'instansi'    => $request->instansi,
            'instansi_id' => $instansiData->id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $this->logAction(Auth::id(), 'Update Admin Dinas', 'Memperbarui data admin: ' . $user->name, $request);

        return redirect()->route('admin.admin-dinas.index', $portal_code)
            ->with('success', 'Data Admin Dinas ' . $request->name . ' berhasil diperbarui.');
    }

    // --- FUNGSI LAINNYA ---

    public function sendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:users,email']);
        $otp = rand(100000, 999999);
        Cache::put('otp_admin_dinas_' . $request->email, $otp, now()->addMinutes(5));
        try {
            Mail::to($request->email)->send(new AdminOTPMail($otp));
            return response()->json(['success' => true, 'message' => 'Kode OTP terkirim.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal kirim email.'], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        $cachedOtp = Cache::get('otp_admin_dinas_' . $request->email);
        if ($cachedOtp && $cachedOtp == $request->otp) {
            session(['verified_admin_dinas_email' => $request->email]);
            Cache::forget('otp_admin_dinas_' . $request->email);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'OTP salah.'], 422);
    }

    public function destroy($portal_code, $id)
    {
        $this->validatePortal($portal_code);
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.admin-dinas.index', $portal_code)->with('success', 'Akses telah dicabut.');
    }
}