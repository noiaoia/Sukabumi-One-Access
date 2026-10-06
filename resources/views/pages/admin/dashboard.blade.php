@extends('layouts.admin')

@section('page-title', 'Dashboard Overview')
@section('page-subtitle', 'Selamat datang kembali, Admin.')
@section('page-icon', 'bi-grid-1x2-fill')

@section('admin-content')

<style>
    #dashboard-root .dash-card {
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
    }
    #dashboard-root .dash-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px -12px rgba(18, 52, 87, 0.16);
        border-color: rgba(18, 52, 87, 0.12);
    }
    #dashboard-root .bar-fill {
        transition: width .6s cubic-bezier(.4,0,.2,1);
    }
    #dashboard-root .live-dot {
        position: relative;
    }
    #dashboard-root .live-dot::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 9999px;
        background: inherit;
        animation: dash-pulse 2.2s ease-out infinite;
    }
    @keyframes dash-pulse {
        0%   { opacity: .55; transform: scale(1); }
        100% { opacity: 0;   transform: scale(2.4); }
    }
    @media (prefers-reduced-motion: reduce) {
        #dashboard-root .live-dot::after { animation: none; }
        #dashboard-root .dash-card, #dashboard-root .bar-fill { transition: none; }
    }
    #dashboard-root .timeline-rail {
        position: absolute;
        left: 21px;
        top: 4px;
        bottom: 4px;
        width: 1px;
        background: linear-gradient(to bottom, rgba(18,52,87,0.10), rgba(18,52,87,0.02));
    }
</style>

<div class="space-y-6" id="dashboard-root" data-endpoint="{{ route('admin.dashboard.data', $portal_code) }}">

    {{-- ================= 0. HERO STRIP ================= --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0F2338] via-[#123457] to-[#1B3D63] px-6 py-6 sm:px-8 sm:py-7 shadow-lg shadow-primary/20">
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-white/5"></div>
        <div class="absolute -right-6 bottom-0 w-40 h-40 rounded-full bg-amber-400/10"></div>

        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-amber-300/90 mb-1.5">Sukabumi One Access</p>
                <h2 class="text-xl sm:text-2xl font-black text-white leading-tight">Ringkasan Sistem Hari Ini</h2>
                <p class="text-xs sm:text-[13px] text-slate-300/80 mt-1.5" id="dash-clock">Memuat waktu…</p>
            </div>

            <div class="flex items-center gap-3 sm:gap-4">
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full pl-2.5 pr-3.5 py-1.5 border border-white/10">
                    <span class="live-dot w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
                    <span class="text-[11px] font-bold text-white tracking-wide">Live</span>
                </div>
                <div class="hidden sm:block text-right">
                    <p class="text-[11px] font-semibold text-slate-300/80">Data diperbarui otomatis</p>
                    <p class="text-[11px] font-bold text-white">setiap 8 detik</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= 1. STATISTIK UTAMA ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

        {{-- Total Kunjungan --}}
        <div class="dash-card bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary to-[#1B3D63] text-white flex items-center justify-center text-xl shadow-sm shadow-primary/30">
                    <i class="bi bi-cursor-fill"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-800 tracking-tight" id="stat-total-clicks">{{ number_format($stats['total_clicks'], 0, ',', '.') }}</p>
            <p class="text-xs font-semibold text-slate-400 mt-1">Total Kunjungan (Klik)</p>
        </div>

        {{-- Layanan Terpopuler --}}
        <div class="dash-card bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-accent flex items-center justify-center text-xl">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded-full uppercase tracking-wide">
                    <i class="bi bi-star-fill text-[9px]"></i> Terpopuler
                </span>
            </div>
            <p class="text-2xl font-black text-slate-800 truncate" id="stat-popular-name">{{ $popular_service['name'] ?? '-' }}</p>
            <div class="flex items-center gap-2 mt-2.5">
                <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="bar-fill h-full bg-gradient-to-r from-accent to-amber-400 rounded-full" id="stat-popular-bar" style="width: {{ $popular_service['percent'] ?? 0 }}%;"></div>
                </div>
                <span class="text-[10px] font-bold text-slate-400 flex-shrink-0" id="stat-popular-percent">{{ $popular_service['percent'] ?? 0 }}%</span>
            </div>
        </div>

        {{-- Pengaduan Masuk --}}
        @php
            $pendingCount = (int) ($stats['pending_complaints'] ?? 0);
            $urgencyLabel = $pendingCount === 0 ? 'Semua pengaduan sudah direspon' : ($pendingCount >= 5 ? 'Perlu perhatian segera' : 'Menunggu tindak lanjut');
            $urgencyColor = $pendingCount === 0 ? '#059669' : ($pendingCount >= 5 ? '#DC2626' : '#D97706');
        @endphp
        <div class="dash-card bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center text-xl">
                    <i class="bi bi-chat-left-dots-fill"></i>
                </div>
                <span class="inline-flex items-center gap-1 text-red-500 bg-red-50 text-[10px] font-bold px-2.5 py-1 rounded-full">
                    Belum direspon
                </span>
            </div>
            <p class="text-2xl font-black text-slate-800" id="stat-pending-complaints">{{ $stats['pending_complaints'] }}</p>
            <p class="text-xs font-semibold text-slate-400 mt-1">
                dari <span id="stat-total-complaints">{{ $stats['total_complaints'] }}</span> pengaduan
            </p>
            <p class="text-[10px] font-bold mt-1.5" id="stat-complaints-urgency" style="color: {{ $urgencyColor }};">{{ $urgencyLabel }}</p>
        </div>

        {{-- System Health --}}
        @php
            $healthAvailable = $stats['system_health']['available'] ?? false;
            $healthProblem = (int) ($stats['system_health']['problem'] ?? 0);
            $healthDotColor = !$healthAvailable ? '#CBD5E1' : ($healthProblem > 0 ? '#F59E0B' : '#10B981');
        @endphp
        <div class="dash-card bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-500 flex items-center justify-center text-xl">
                    <i class="bi bi-hdd-network-fill"></i>
                </div>
                <span class="inline-flex items-center gap-1 text-amber-600 bg-amber-50 text-[10px] font-bold px-2.5 py-1 rounded-full" id="stat-health-badge">
                    @if(!$healthAvailable)
                        Belum ada data
                    @else
                        <i class="bi bi-exclamation-triangle-fill"></i> {{ $healthProblem }} Bermasalah
                    @endif
                </span>
            </div>
            <p class="text-2xl font-black text-slate-800">
                <span id="stat-health-ok">{{ $stats['system_health']['ok'] }}</span><span class="text-slate-300">/<span id="stat-health-total">{{ $stats['system_health']['total'] }}</span></span>
            </p>
            <div class="flex items-center gap-1.5 mt-1">
                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" id="stat-health-dot" style="background-color: {{ $healthDotColor }};"></span>
                <p class="text-xs font-semibold text-slate-400">Link eksternal aktif</p>
            </div>
        </div>
    </div>

    {{-- ================= 2. TREN KUNJUNGAN ================= --}}
    @php $weekTotal = array_sum($weekly_clicks['data'] ?? []); @endphp
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-base font-black text-slate-800">Tren Kunjungan Mingguan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Jumlah klik "Buka Layanan" per hari, 7 hari terakhir</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Total minggu ini</p>
                    <p class="text-lg font-black text-slate-800" id="trend-total-week">{{ number_format($weekTotal, 0, ',', '.') }}</p>
                </div>
                <span class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary inline-block"></span> Total klik
                </span>
            </div>
        </div>
        <div class="relative h-64 sm:h-80">
            <canvas id="trafficLineChart"></canvas>
        </div>
    </div>

    {{-- ================= 3. PERBANDINGAN LAYANAN + RANKING ================= --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-5">

        <div class="xl:col-span-3 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100">
            <div class="mb-6">
                <h3 class="text-base font-black text-slate-800">Perbandingan Klik Antar Layanan</h3>
                <p class="text-xs text-slate-400 mt-0.5">6 layanan dengan klik terbanyak</p>
            </div>
            <div class="relative h-64 sm:h-72">
                <canvas id="serviceBarChart"></canvas>
            </div>
        </div>

        <div class="xl:col-span-2 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100">
            <div class="mb-5">
                <h3 class="text-base font-black text-slate-800">Ranking Layanan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Diurutkan dari klik terbanyak</p>
            </div>

            <div class="space-y-1" id="ranking-list">
                @foreach($service_ranking as $item)
                    @php
                        $badgeColor = $item['rank'] === 1 ? 'bg-gradient-to-br from-amber-300 to-amber-500 text-white shadow-sm shadow-amber-400/40'
                            : ($item['rank'] === 2 ? 'bg-gradient-to-br from-slate-200 to-slate-300 text-slate-700'
                            : ($item['rank'] === 3 ? 'bg-gradient-to-br from-orange-200 to-orange-400 text-orange-900' : 'bg-slate-50 text-slate-400 border border-slate-100'));
                    @endphp
                    <div class="flex items-center gap-3 py-2.5">
                        <div class="w-8 h-8 rounded-xl {{ $badgeColor }} flex items-center justify-center text-[11px] font-black flex-shrink-0">
                            {{ $item['rank'] }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <p class="text-xs font-bold text-slate-700 truncate">{{ $item['name'] }}</p>
                                <p class="text-xs font-black text-slate-800 flex-shrink-0">{{ number_format($item['clicks'], 0, ',', '.') }}</p>
                            </div>
                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="bar-fill h-full rounded-full {{ $item['rank'] <= 3 ? 'bg-gradient-to-r from-accent to-amber-400' : 'bg-slate-300' }}" style="width: {{ $item['percent'] }}%;"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if($service_ranking->isEmpty())
                    <p class="text-xs text-slate-400">Belum ada data klik.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= 4. AKTIVITAS TERBARU ================= --}}
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-black text-slate-800">Aktivitas Terbaru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Log klik, aktivitas operator, dan pengaduan masuk secara real-time</p>
            </div>
        </div>

        <div class="relative">
            <div class="timeline-rail"></div>
            <div class="space-y-0.5" id="activity-list">
                @foreach($recent_activities as $log)
                    <div class="relative flex items-start gap-4 py-3">
                        <div class="relative z-10 w-9 h-9 rounded-xl {{ $log['color'] }} flex items-center justify-center text-sm flex-shrink-0 ring-4 ring-white">
                            <i class="bi {{ $log['icon'] }}"></i>
                        </div>
                        <div class="flex-1 min-w-0 pt-1.5">
                            <p class="text-xs text-slate-600 leading-relaxed">{!! $log['text'] !!}</p>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400 flex-shrink-0 pt-2 whitespace-nowrap">{{ $log['time_human'] }}</span>
                    </div>
                @endforeach
                @if($recent_activities->isEmpty())
                    <p class="text-xs text-slate-400 py-3">Belum ada aktivitas.</p>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
    const chartColors = {
        primary: '#123457',
        primaryLight: 'rgba(18, 52, 87, 0.08)',
        accent: '#E8A33D',
        sky: '#0EA5E9',
        grid: '#F1F5F9',
        text: '#94A3B8',
    };

    const root = document.getElementById('dashboard-root');
    const endpoint = root.dataset.endpoint;

    // ---------- Live clock (visual only, does not affect polling) ----------
    const clockEl = document.getElementById('dash-clock');
    const hariList = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const bulanList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    function updateClock() {
        if (!clockEl) return;
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        clockEl.textContent = `${hariList[now.getDay()]}, ${now.getDate()} ${bulanList[now.getMonth()]} ${now.getFullYear()} • ${hh}:${mm}`;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ---------- Chart gradients ----------
    const lineCtx = document.getElementById('trafficLineChart').getContext('2d');
    const lineGradient = lineCtx.createLinearGradient(0, 0, 0, 300);
    lineGradient.addColorStop(0, 'rgba(18, 52, 87, 0.22)');
    lineGradient.addColorStop(1, 'rgba(18, 52, 87, 0.01)');

    const tooltipBase = {
        backgroundColor: '#0F2338',
        titleColor: '#F8FAFC',
        bodyColor: '#CBD5E1',
        padding: 10,
        cornerRadius: 10,
        displayColors: false,
        titleFont: { weight: 'bold', size: 12 },
        bodyFont: { size: 12 },
    };

    const lineChart = new Chart(document.getElementById('trafficLineChart'), {
        type: 'line',
        data: {
            labels: @json($weekly_clicks['labels']),
            datasets: [{
                label: 'Total Klik',
                data: @json($weekly_clicks['data']),
                borderColor: chartColors.primary,
                backgroundColor: lineGradient,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: chartColors.accent,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: { ...tooltipBase, callbacks: { label: (ctx) => ` ${new Intl.NumberFormat('id-ID').format(ctx.parsed.y)} klik` } },
            },
            scales: {
                y: { beginAtZero: true, grid: { color: chartColors.grid }, border: { display: false }, ticks: { color: chartColors.text, font: { size: 11 } } },
                x: { grid: { display: false }, border: { display: false }, ticks: { color: chartColors.text, font: { size: 11 } } }
            }
        }
    });

    const barChart = new Chart(document.getElementById('serviceBarChart'), {
        type: 'bar',
        data: {
            labels: @json($service_ranking->pluck('name')),
            datasets: [{
                label: 'Total Klik',
                data: @json($service_ranking->pluck('clicks')),
                backgroundColor: chartColors.primary,
                hoverBackgroundColor: chartColors.accent,
                borderRadius: 10,
                borderSkipped: false,
                maxBarThickness: 38,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { ...tooltipBase, callbacks: { label: (ctx) => ` ${new Intl.NumberFormat('id-ID').format(ctx.parsed.y)} klik` } },
            },
            scales: {
                y: { beginAtZero: true, grid: { color: chartColors.grid }, border: { display: false }, ticks: { color: chartColors.text, font: { size: 11 } } },
                x: { grid: { display: false }, border: { display: false }, ticks: { color: chartColors.text, font: { size: 10 }, autoSkip: false, maxRotation: 0 } }
            }
        }
    });

    function formatNumber(n) {
        return new Intl.NumberFormat('id-ID').format(n);
    }

    function renderRanking(list) {
        const badge = (rank) => rank === 1 ? 'bg-gradient-to-br from-amber-300 to-amber-500 text-white shadow-sm shadow-amber-400/40'
            : rank === 2 ? 'bg-gradient-to-br from-slate-200 to-slate-300 text-slate-700'
            : rank === 3 ? 'bg-gradient-to-br from-orange-200 to-orange-400 text-orange-900' : 'bg-slate-50 text-slate-400 border border-slate-100';

        const container = document.getElementById('ranking-list');
        if (!list.length) {
            container.innerHTML = '<p class="text-xs text-slate-400">Belum ada data klik.</p>';
            return;
        }

        container.innerHTML = list.map(item => `
            <div class="flex items-center gap-3 py-2.5">
                <div class="w-8 h-8 rounded-xl ${badge(item.rank)} flex items-center justify-center text-[11px] font-black flex-shrink-0">${item.rank}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <p class="text-xs font-bold text-slate-700 truncate">${item.name}</p>
                        <p class="text-xs font-black text-slate-800 flex-shrink-0">${formatNumber(item.clicks)}</p>
                    </div>
                    <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="bar-fill h-full rounded-full ${item.rank <= 3 ? 'bg-gradient-to-r from-accent to-amber-400' : 'bg-slate-300'}" style="width: ${item.percent}%;"></div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function renderActivities(list) {
        const container = document.getElementById('activity-list');
        if (!list.length) {
            container.innerHTML = '<p class="text-xs text-slate-400 py-3">Belum ada aktivitas.</p>';
            return;
        }

        container.innerHTML = list.map((log) => `
            <div class="relative flex items-start gap-4 py-3">
                <div class="relative z-10 w-9 h-9 rounded-xl ${log.color} flex items-center justify-center text-sm flex-shrink-0 ring-4 ring-white">
                    <i class="bi ${log.icon}"></i>
                </div>
                <div class="flex-1 min-w-0 pt-1.5">
                    <p class="text-xs text-slate-600 leading-relaxed">${log.text}</p>
                </div>
                <span class="text-[10px] font-semibold text-slate-400 flex-shrink-0 pt-2 whitespace-nowrap">${log.time_human}</span>
            </div>
        `).join('');
    }

    function applyPayload(payload) {
        const s = payload.stats;

        document.getElementById('stat-total-clicks').textContent = formatNumber(s.total_clicks);
        document.getElementById('stat-popular-name').textContent = payload.popular_service?.name ?? '-';
        document.getElementById('stat-popular-percent').textContent = (payload.popular_service?.percent ?? 0) + '%';
        document.getElementById('stat-popular-bar').style.width = (payload.popular_service?.percent ?? 0) + '%';
        document.getElementById('stat-pending-complaints').textContent = s.pending_complaints;
        document.getElementById('stat-total-complaints').textContent = s.total_complaints;
        document.getElementById('stat-health-ok').textContent = s.system_health.ok;
        document.getElementById('stat-health-total').textContent = s.system_health.total;

        const badge = document.getElementById('stat-health-badge');
        badge.innerHTML = !s.system_health.available
            ? 'Belum ada data'
            : `<i class="bi bi-exclamation-triangle-fill"></i> ${s.system_health.problem} Bermasalah`;

        const healthDot = document.getElementById('stat-health-dot');
        if (healthDot) {
            healthDot.style.backgroundColor = !s.system_health.available
                ? '#CBD5E1'
                : (s.system_health.problem > 0 ? '#F59E0B' : '#10B981');
        }

        const urgencyEl = document.getElementById('stat-complaints-urgency');
        if (urgencyEl) {
            const pending = Number(s.pending_complaints) || 0;
            urgencyEl.textContent = pending === 0 ? 'Semua pengaduan sudah direspon' : (pending >= 5 ? 'Perlu perhatian segera' : 'Menunggu tindak lanjut');
            urgencyEl.style.color = pending === 0 ? '#059669' : (pending >= 5 ? '#DC2626' : '#D97706');
        }

        lineChart.data.labels = payload.weekly_clicks.labels;
        lineChart.data.datasets[0].data = payload.weekly_clicks.data;
        lineChart.update();

        const trendTotalEl = document.getElementById('trend-total-week');
        if (trendTotalEl) {
            const total = (payload.weekly_clicks.data || []).reduce((a, b) => a + Number(b || 0), 0);
            trendTotalEl.textContent = formatNumber(total);
        }

        barChart.data.labels = payload.service_ranking.map(i => i.name);
        barChart.data.datasets[0].data = payload.service_ranking.map(i => i.clicks);
        barChart.update();

        renderRanking(payload.service_ranking);
        renderActivities(payload.recent_activities);
    }

    async function poll() {
        try {
            const res = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            applyPayload(await res.json());
        } catch (e) {
            // diamkan saja, coba lagi di interval berikutnya
        }
    }

    // Polling tiap 8 detik, tidak reload halaman
    setInterval(poll, 8000);
})();
</script>
@endpush