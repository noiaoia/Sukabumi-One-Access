<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use App\Models\ActivityLog;
use App\Models\Admin\Layanan;
use App\Models\Complaint;
use App\Models\LayananClick;
use App\Models\User;
use App\Models\User\Pengajuan;

class DashboardController extends Controller
{
    /**
     * Halaman dashboard (render pertama, data langsung terisi — tidak nunggu polling pertama)
     */
    public function index($portal_code)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->authorizePortal($portal_code, $user);

        $payload = $this->buildPayload($user);

        return view('pages.admin.dashboard', array_merge($payload, [
            'portal_code' => $portal_code,
        ]));
    }

    /**
     * Endpoint JSON untuk polling realtime.
     * GET /{portal_code}/dashboard/data
     */
    public function data(Request $request, $portal_code)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->authorizePortal($portal_code, $user);

        return response()->json($this->buildPayload($user));
    }

    /**
     * 1. VALIDASI KEAMANAN — sama seperti sebelumnya, tidak diubah.
     * PENTING: instansi_id SELALU diambil dari Auth::user(), bukan dari request.
     */
    private function authorizePortal($portal_code, $user)
    {
        if ($user->role === 'admin_dinas') {
            if ($portal_code !== 'portal-' . $user->username) abort(404);
        } elseif ($user->role === 'operator') {
            $boss = User::where('instansi_id', $user->instansi_id)
                ->where('role', 'admin_dinas')
                ->first();
            if (!$boss || $portal_code !== 'portal-' . $boss->username) abort(404);
        } elseif ($user->role !== 'super_admin') {
            abort(404);
        }
    }

    /**
     * null = super_admin (global), int = instansi_id milik admin_dinas/operator
     */
    private function instansiScope($user)
    {
        return $user->isSuperAdmin() ? null : $user->instansi_id;
    }

    private function layananQuery($instansiId)
    {
        return Layanan::query()
            ->when($instansiId, fn ($q) => $q->where('instansi_id', $instansiId));
    }

    private function clickQuery($instansiId)
    {
        return LayananClick::query()
            ->when($instansiId, function ($q) use ($instansiId) {
                $q->whereHas('layanan', fn ($qq) => $qq->where('instansi_id', $instansiId));
            });
    }

    private function complaintQuery($instansiId)
    {
        return Complaint::query()
            ->when($instansiId, fn ($q) => $q->where('instansi_id', $instansiId));
    }

    private function pengajuanQuery($instansiId)
    {
        return Pengajuan::query()
            ->when($instansiId, function ($q) use ($instansiId) {
                $q->whereHas('layanan', fn ($qq) => $qq->where('instansi_id', $instansiId));
            });
    }

    /**
     * Bangun seluruh payload dashboard (dipakai baik oleh index() maupun data())
     */
    private function buildPayload($user)
    {
        $instansiId = $this->instansiScope($user);

        // admin_dinas/operator tanpa instansi_id -> jangan bocorkan data apa pun
        if (!$user->isSuperAdmin() && !$instansiId) {
            return $this->emptyPayload();
        }

        $totalClicks   = $this->clickQuery($instansiId)->count();
        $totalServices = $this->layananQuery($instansiId)->count();

        $totalComplaints   = $this->complaintQuery($instansiId)->count();
        $pendingComplaints = $this->complaintQuery($instansiId)->where('status', 'pending')->count();

        $totalSubmissions   = $this->pengajuanQuery($instansiId)->count();
        $pendingSubmissions = $this->pengajuanQuery($instansiId)->where('status', 'diajukan')->count();

        $totalOperators = $instansiId
            ? User::where('instansi_id', $instansiId)->count()
            : User::whereIn('role', ['super_admin', 'admin_dinas', 'operator'])->count();

        // 7. Layanan Terpopuler + 8. Ranking Layanan (klik asli, bukan rand())
        $ranked = $this->layananQuery($instansiId)
            ->withCount('clicks')
            ->orderByDesc('clicks_count')
            ->take(6)
            ->with('instansi:id,nama')
            ->get(['id', 'nama', 'slug', 'instansi_id']);

        $maxClicks = (int) ($ranked->max('clicks_count') ?: 1);

        $serviceRanking = $ranked->values()->map(function ($layanan, $index) use ($maxClicks) {
            return [
                'rank'    => $index + 1,
                'name'    => $layanan->nama,
                'org'     => optional($layanan->instansi)->nama,
                'clicks'  => $layanan->clicks_count,
                'percent' => (int) round(($layanan->clicks_count / $maxClicks) * 100),
            ];
        });

        $popularService = $serviceRanking->first();

        // 9. Chart tren kunjungan 7 hari terakhir (timezone aplikasi)
        $weeklyClicks = $this->weeklyClicks($instansiId);

        // 12. System health (dari cache hasil pengecekan berkala, lihat CheckLayananHealth)
        $systemHealth = $this->systemHealth($instansiId);

        // 11. Aktivitas terbaru gabungan
        $recentActivities = $this->recentActivities($instansiId);

        return [
            'stats' => [
                'total_clicks'        => $totalClicks,
                'total_services'      => $totalServices,
                'total_complaints'    => $totalComplaints,
                'pending_complaints'  => $pendingComplaints,
                'total_submissions'   => $totalSubmissions,
                'pending_submissions' => $pendingSubmissions,
                'total_operators'     => $totalOperators,
                'system_health'       => $systemHealth,
            ],
            'popular_service'  => $popularService,
            'weekly_clicks'    => $weeklyClicks,
            'service_ranking'  => $serviceRanking,
            'recent_activities' => $recentActivities,
        ];
    }

    private function weeklyClicks($instansiId)
    {
        $tz    = config('app.timezone');
        $start = Carbon::now($tz)->subDays(6)->startOfDay();

        $rows = $this->clickQuery($instansiId)
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as jumlah')
            ->groupBy('tanggal')
            ->pluck('jumlah', 'tanggal');

        $labelHari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $labels = [];
        $data   = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now($tz)->subDays($i);
            $labels[] = $labelHari[$date->dayOfWeekIso - 1];
            $data[]   = (int) ($rows[$date->format('Y-m-d')] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Dibaca dari cache yang diisi oleh command CheckLayananHealth (lihat bagian 5).
     * Dashboard TIDAK PERNAH melakukan HTTP request langsung supaya tidak lambat/error
     * hanya karena satu link eksternal mati.
     */
    private function systemHealth($instansiId)
    {
        $layananList = $this->layananQuery($instansiId)
            ->where('jenis', 'iframe')
            ->where('status', 'aktif')
            ->whereNotNull('iframe_url')
            ->get(['id']);

        if ($layananList->isEmpty()) {
            return ['total' => 0, 'ok' => 0, 'problem' => 0, 'checked_at' => null, 'available' => false];
        }

        $ok = 0;
        $checkedAt = null;
        $anyChecked = false;

        foreach ($layananList as $layanan) {
            $cached = Cache::get("layanan_health:{$layanan->id}");
            if (!$cached) continue;

            $anyChecked = true;
            if ($cached['ok']) $ok++;
            $checkedAt = $checkedAt ? max($checkedAt, $cached['checked_at']) : $cached['checked_at'];
        }

        return [
            'total'      => $layananList->count(),
            'ok'         => $ok,
            'problem'    => $layananList->count() - $ok,
            'checked_at' => $checkedAt,
            'available'  => $anyChecked, // false = command belum pernah jalan, tampilkan "Belum ada data"
        ];
    }

    private function recentActivities($instansiId)
    {
        $limit = 10;

        $clicks = $this->clickQuery($instansiId)
            ->with('layanan:id,nama')
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn ($c) => [
                'icon'  => 'bi-box-arrow-up-right',
                'color' => 'bg-sky-50 text-sky-500',
                'text'  => 'Warga diarahkan ke portal <strong>' . e(optional($c->layanan)->nama ?? 'layanan') . '</strong>',
                'time'  => $c->created_at,
            ]);

        $complaints = $this->complaintQuery($instansiId)
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn ($c) => [
                'icon'  => 'bi-exclamation-circle-fill',
                'color' => 'bg-red-50 text-red-500',
                'text'  => 'Pengaduan baru: "' . e($c->title) . '"',
                'time'  => $c->created_at,
            ]);

        $submissions = $this->pengajuanQuery($instansiId)
            ->with('layanan:id,nama')
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn ($p) => [
                'icon'  => 'bi-file-earmark-plus-fill',
                'color' => 'bg-emerald-50 text-emerald-600',
                'text'  => 'Pengajuan baru pada layanan <strong>' . e(optional($p->layanan)->nama ?? '-') . '</strong> (' . e($p->nomor_pengajuan) . ')',
                'time'  => $p->created_at,
            ]);

        $operatorLogs = ActivityLog::query()
            ->when($instansiId, fn ($q) => $q->whereHas('user', fn ($qq) => $qq->where('instansi_id', $instansiId)))
            ->with('user:id,name')
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn ($log) => [
                'icon'  => 'bi-pencil-fill',
                'color' => 'bg-amber-50 text-accent',
                'text'  => e($log->description),
                'time'  => $log->created_at,
            ]);

        return $clicks->concat($complaints)->concat($submissions)->concat($operatorLogs)
            ->sortByDesc('time')
            ->take($limit)
            ->values()
            ->map(function ($item) {
                $item['time_human'] = $item['time']->diffForHumans();
                unset($item['time']);
                return $item;
            });
    }

    private function emptyPayload()
    {
        return [
            'stats' => [
                'total_clicks' => 0, 'total_services' => 0, 'total_complaints' => 0,
                'pending_complaints' => 0, 'total_submissions' => 0, 'pending_submissions' => 0,
                'total_operators' => 0,
                'system_health' => ['total' => 0, 'ok' => 0, 'problem' => 0, 'checked_at' => null, 'available' => false],
            ],
            'popular_service'   => null,
            'weekly_clicks'     => ['labels' => [], 'data' => []],
            'service_ranking'   => collect(),
            'recent_activities' => collect(),
        ];
    }
}