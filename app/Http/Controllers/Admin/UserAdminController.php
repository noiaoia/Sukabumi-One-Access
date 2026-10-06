<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserAdminController extends Controller
{
    /**
     * Helper untuk validasi akses portal code
     */
    private function validatePortal($portal_code) {
        if ($portal_code !== 'portal-' . Auth::user()->username) {
            abort(404);
        }
    }

    /**
     * Helper untuk mencatat log aktivitas admin
     */
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

    /**
     * Halaman Utama: Daftar Warga & Statistik
     */
    public function index(Request $request, $portal_code)
    {
        $this->validatePortal($portal_code);

        $query = User::where('role', 'user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        // Statistik Global untuk HUD
        $totalWarga = User::where('role', 'user')->count();
        $totalVerified = User::where('role', 'user')->whereNotNull('email_verified_at')->count();
        $totalSuspended = User::where('role', 'user')->where('status', 'suspended')->count(); // Tambahkan ini

        return view('pages.admin.user-management.users', compact(
            'users', 
            'portal_code', 
            'totalWarga', 
            'totalVerified',
            'totalSuspended' // Kirim ke view
        ));
    }

    /**
     * Fitur Blokir / Aktifkan Akun Warga
     */
    public function toggleStatus(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);
        
        $user = User::findOrFail($id);
        
        // Balikkan status
        $oldStatus = $user->status;
        $user->status = ($oldStatus == 'active') ? 'suspended' : 'active';
        $user->save();

        // Catat ke Log Aktivitas
        $action = ($user->status == 'suspended') ? 'Blokir Pengguna' : 'Aktivasi Pengguna';
        $this->logAction($action, "Mengubah status akun {$user->name} dari {$oldStatus} menjadi {$user->status}", $request);

        return redirect()->back()->with('success', "Akun {$user->name} kini berstatus " . strtoupper($user->status));
    }

    /**
     * Hapus Akun Warga Permanen
     */
    public function destroy(Request $request, $portal_code, $id)
    {
        $this->validatePortal($portal_code);

        $user = User::findOrFail($id);
        $namaUser = $user->name;

        // Hapus foto profil dari storage agar tidak membebani server
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Catat ke Log sebelum data dihapus
        $this->logAction('Hapus Pengguna', "Menghapus akun warga: {$namaUser} secara permanen dari sistem.", $request);

        $user->delete();

        return redirect()->back()->with('success', "Akun warga {$namaUser} telah dimusnahkan dari database.");
    }
}