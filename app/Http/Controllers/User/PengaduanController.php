<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    /**
     * Tampilkan Halaman Pengaduan & Statistik
     */
    public function index() 
    {
        $totalReports = Complaint::count();
        $resolvedCount = Complaint::where('status', 'done')->count();
        $resolvedPercentage = $totalReports > 0 ? round(($resolvedCount / $totalReports) * 100) : 0;
        $responseTime = "24h";

        return view('pages.user.pengaduan', compact(
            'totalReports', 
            'resolvedPercentage', 
            'responseTime'
        ));
    }

    /**
     * Simpan Laporan Baru dari Warga
     */
    public function store(Request $request)
    {
        // 1. Validasi Input yang Ketat
        $request->validate([
            'title'           => 'required|string|max:100',
            'category'        => 'required|string',
            'description'     => 'required|string|min:20',
            'latitude'        => 'required|numeric',
            'longitude'       => 'required|numeric',
            'alamat_kejadian' => 'required|string|max:500',
            'incident_date'   => 'required|date', // <--- TAMBAHKAN INI (Sesuai form seksi 2)
            'attachment'      => 'required|image|mimes:jpg,png,jpeg|max:2048', 
            
            // Validasi Kontak Opsional (Section 4)
            'phone_contact'   => 'nullable|digits_between:10,13',
            'email_contact'   => 'nullable|email|max:255',
        ], [
            'title.required'           => 'Judul laporan wajib diisi.',
            'incident_date.required'   => 'Tanggal kejadian wajib diisi.', // <--- TAMBAHKAN INI
            'description.min'          => 'Berikan deskripsi kejadian yang lebih detail (min. 20 karakter).',
            'latitude.required'        => 'Titik lokasi pada peta wajib ditandai.',
            'alamat_kejadian.required' => 'Alamat atau patokan lokasi wajib diisi.',
            'attachment.required'      => 'Laporan wajib melampirkan foto bukti.',
            'attachment.max'           => 'Ukuran foto bukti maksimal 2MB.',
            'phone_contact.digits_between' => 'Nomor WhatsApp harus berupa angka 10-13 digit.',
            'email_contact.email'      => 'Format alamat email tidak valid.',
        ]);

        // 2. Proses Unggah Foto
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('complaints', 'public');
        }

        // 3. Simpan data ke Database
        $complaint = Complaint::create([
            'user_id'          => Auth::id(),
            'title'            => $request->title,
            'category'         => $request->category,
            'description'      => $request->description,
            'attachment'       => $attachmentPath,
            'latitude'         => $request->latitude,
            'longitude'        => $request->longitude,
            'location_address' => $request->alamat_kejadian,
            'incident_date'    => $request->incident_date, // <--- TAMBAHKAN INI
            'is_anonymous'     => $request->has('is_anonymous'), 
            'contact_phone'    => $request->phone_contact,
            'contact_email'    => $request->email_contact,
            'status'           => 'pending',
        ]);

        // 4. Kirim data ke Session untuk ditampilkan di Modal "Struk/Dokumen"
        return redirect()->back()->with([
            'success_message'    => 'Laporan Anda telah berhasil kami terima.', // Gunakan kunci ini
            'ticket_id'          => $complaint->ticket_id,
            'res_title'          => $complaint->title,
            'res_category'       => $complaint->category,
            'res_date'           => $complaint->created_at->format('d M Y H:i')
        ]);
    }

    /**
     * Tandai seluruh notifikasi user telah dibaca
     */
    public function markNotificationsAsRead()
    {
        $userId = Auth::id();

        // 1. Update Tabel Pengaduan
        Complaint::where('user_id', $userId)
            ->where('is_read_user_notification', false)
            ->update(['is_read_user_notification' => true]);

        // 2. Update Tabel Pengajuan Berkas
        \App\Models\User\Pengajuan::where('user_id', $userId)
            ->where('is_read_user_notification', false)
            ->update(['is_read_user_notification' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mengambil jumlah notifikasi user & timestamp terbaru (AJAX)
     */
    public function notifCount()
    {
        $userId = Auth::id();

        // 1. Query Notif Pengaduan
        $complaintQuery = Complaint::where('user_id', $userId)
            ->where('is_read_user_notification', false)
            ->whereIn('status', ['review', 'process', 'done', 'rejected']);

        // 2. Query Notif Pengajuan Berkas
        $submissionQuery = \App\Models\User\Pengajuan::where('user_id', $userId)
            ->where('is_read_user_notification', false)
            ->whereIn('status', ['review', 'diproses', 'perlu_revisi', 'selesai', 'ditolak']);

        // 3. Hitung Total Gabungan
        $totalCount = $complaintQuery->count() + $submissionQuery->count();

        // 4. Ambil Timestamp terbaru dari kedua jenis data
        $latestComplaint = $complaintQuery->latest('updated_at')->first();
        $latestSubmission = $submissionQuery->latest('updated_at')->first();

        // Ambil mana yang paling baru diupdate di antara keduanya
        $latestTimestamp = max(
            $latestComplaint ? $latestComplaint->updated_at->timestamp : 0,
            $latestSubmission ? $latestSubmission->updated_at->timestamp : 0
        );

        return response()->json([
            'count'             => $totalCount,
            'latest_updated_at' => $latestTimestamp, // Digunakan JS untuk bunyi "Ting!"
        ]);
    }
}