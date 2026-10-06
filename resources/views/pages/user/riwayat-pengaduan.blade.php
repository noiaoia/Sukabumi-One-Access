@extends('layouts.app')

@section('title', 'Tinjau Pengajuan - Sukabumi One Access')

@section('content')
<style>
    /* ============================================================
       HERO
    ============================================================ */
    .lacak-hero {
        background-color: #0A1E33;
        position: relative;
        overflow: hidden;
        padding: 5rem 1.5rem 8rem;
        border-bottom: 4px solid #E8A33D;
    }
    .bg-dot-matrix {
        position: absolute; inset: 0; opacity: 0.12;
        background-image: radial-gradient(#E8A33D 1px, transparent 0);
        background-size: 30px 30px;
    }
    .hero-glow {
        position: absolute;
        top: -120px; left: 50%;
        transform: translateX(-50%);
        width: 520px; height: 320px;
        background: radial-gradient(circle, rgba(232,163,61,0.22) 0%, transparent 70%);
        pointer-events: none;
    }
    .hero-divider {
        width: 44px; height: 3px; border-radius: 999px;
        background: #E8A33D;
        margin: 18px auto 0;
        opacity: 0.85;
    }

    .search-lacak-container {
        max-width: 700px;
        margin: -2.5rem auto 0;
        position: relative;
        z-index: 40;
    }

    /* ============================================================
       SEARCH
    ============================================================ */
    .search-lacak-card {
        background: #fff;
        border-radius: 22px;
        padding: 10px;
        box-shadow: 0 20px 45px rgba(10,30,51,0.16);
        border: 1px solid #EEF2F6;
    }
    .search-lacak-input-wrap {
        display: flex; align-items: center;
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 15px;
        padding: 12px 16px;
        transition: border-color .2s ease, background .2s ease;
    }
    .search-lacak-input-wrap:focus-within {
        border-color: #0A1E33;
        background: #fff;
    }
    .search-lacak-input-wrap i { color: #94a3b8; margin-right: 10px; font-size: 15px; }
    .search-lacak-input-wrap input { width: 100%; background: transparent; border: none; outline: none; font-weight: 700; color: #123457; font-size: 13px; }
    .search-lacak-input-wrap input::placeholder { color: #94a3b8; font-weight: 600; }

    .btn-cari-data {
        background: #0A1E33;
        color: #fff;
        border: 1px solid #0A1E33;
        border-radius: 15px;
        padding: 12px 28px;
        font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em;
        transition: all .2s ease;
        white-space: nowrap;
    }
    .btn-cari-data:hover { background: #E8A33D; border-color: #E8A33D; color: #0A1E33; }

    /* ============================================================
       STATUS OVERVIEW
    ============================================================ */
    .status-overview-card {
        background: #fff;
        border: 1.5px solid #E2E8F0;
        border-radius: 16px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all .2s ease;
        min-width: 0;
    }
    .status-overview-card:hover { border-color: #0A1E33; transform: translateY(-2px); }
    .status-overview-icon {
        width: 36px; height: 36px; border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.95rem; flex-shrink: 0;
    }
    .status-overview-number { font-size: 1.3rem; font-weight: 900; color: #0A1E33; line-height: 1; }
    .status-overview-label { font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; margin-top: 2px; white-space: nowrap; }

    /* ============================================================
       REPORT CARD
    ============================================================ */
    .report-card {
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 20px;
        padding: 18px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 3px 10px rgba(10, 30, 51, 0.04);
        display: flex;
        flex-direction: column;
        gap: 14px;
        height: 100%;
    }
    .report-card:hover {
        background: #ffffff;
        border-color: rgba(232, 163, 61, 0.5);
        transform: translateY(-4px);
        box-shadow: 0 14px 28px rgba(10, 30, 51, 0.1);
    }

    .report-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }

    .category-box {
        width: 42px; height: 42px; border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.05rem; flex-shrink: 0;
        background-color: var(--cat-bg);
        color: var(--cat-color);
        border: 1px solid color-mix(in srgb, var(--cat-color) 25%, transparent);
        transition: transform .2s ease;
    }
    .report-card:hover .category-box { transform: scale(1.06); }

    .report-card-body { flex: 1; }
    .report-tag-row { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; margin-bottom: 7px; }
    .ticket-tag {
        font-size: 8.5px; font-weight: 900; color: #94a3b8; text-transform: uppercase;
        letter-spacing: 0.05em; background: #ffffff; padding: 3px 8px; border-radius: 6px;
        border: 1px solid #E2E8F0;
    }
    .cat-tag { font-size: 8.5px; font-weight: 900; color: #E8A33D; text-transform: uppercase; letter-spacing: 0.05em; }
    .report-title { font-size: 13.5px; font-weight: 900; color: #123457; line-height: 1.35; }
    .report-date {
        font-size: 10px; font-weight: 700; color: #94a3b8;
        display: flex; align-items: center; gap: 5px; margin-top: 8px;
    }

    .report-card-footer {
        display: flex; align-items: center; justify-content: space-between;
        padding-top: 12px; border-top: 1px dashed #cbd5e1;
        font-size: 9.5px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em;
        color: #123457; transition: color 0.2s ease;
    }
    .report-card:hover .report-card-footer { color: #E8A33D; }
    .report-card-footer i { transition: transform 0.2s ease; font-size: 11px; }
    .report-card:hover .report-card-footer i { transform: translateX(4px); }

    /* Status Pills */
    .status-pill {
        font-size: 8.5px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.8px; padding: 4px 10px; border-radius: 8px;
        display: inline-flex; align-items: center; gap: 5px; flex-shrink: 0;
    }
    .pill-pending { background: #fff1f2; color: #e11d48; }
    .pill-review { background: #eff6ff; color: #3b82f6; }
    .pill-process { background: #fefce8; color: #ca8a04; }
    .pill-done { background: #f0fdf4; color: #16a34a; }
    .pill-rejected { background: #fef2f2; color: #dc2626; }
    .pulse-dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; animation: opPulse 2s infinite; }
    @keyframes opPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

    /* ============================================================
       MODAL DETAIL LAPORAN (dipertahankan apa adanya)
    ============================================================ */
    @keyframes rdOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes rdOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes rdSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.01) translateY(-4px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes rdSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.96) translateY(10px); }
    }

    .rd-overlay {
        position: fixed; inset: 0;
        background: rgba(10, 30, 51, 0.75);
        backdrop-filter: blur(5px);
        display: none; align-items: center; justify-content: center;
        z-index: 9999; padding: 15px;
        opacity: 0;
    }
    .rd-overlay.is-active { display: flex; animation: rdOverlayIn 0.3s ease forwards; }
    .rd-overlay.is-active .rd-card { animation: rdSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .rd-overlay.is-closing { animation: rdOverlayOut 0.3s ease forwards; }
    .rd-overlay.is-closing .rd-card { animation: rdSpringOut 0.3s ease forwards; }

    .rd-card {
        background: #ffffff;
        width: 100%; max-width: 380px;
        max-height: 80vh;
        border-radius: 22px; overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        display: flex; flex-direction: column;
    }

    .rd-header {
        background: #0A1E33;
        padding: 18px 24px;
        border-bottom: 4px solid #E8A33D;
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px;
        flex-shrink: 0;
    }
    .rd-header-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .rd-icon-box {
        width: 32px; height: 32px; border-radius: 9px;
        background: rgba(255,255,255,0.1);
        display: flex; align-items: center; justify-content: center;
        color: #E8A33D; font-size: 15px; flex-shrink: 0;
    }
    .rd-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; letter-spacing: 0.3px; }
    .rd-header p { color: rgba(255,255,255,0.5); font-size: 10.5px; margin: 2px 0 0; text-transform: uppercase; }
    .rd-close {
        width: 30px; height: 30px; border-radius: 9px;
        background: rgba(255,255,255,0.06); border: none; color: #94a3b8;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; cursor: pointer;
        transition: all 0.2s;
    }
    .rd-close:hover { background: rgba(239,68,68,0.15); color: #f87171; }

    .rd-body-scroll { overflow-y: auto; flex: 1; }
    .rd-body-scroll::-webkit-scrollbar { background: #0A1E33; width: 5px; }
    .rd-body-scroll::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }

    .rd-section { padding: 20px 24px; }
    .rd-section + .rd-section { border-top: 1px solid #F1F5F9; }

    .rd-section-heading { display: flex; align-items: center; gap: 9px; margin-bottom: 14px; }
    .rd-section-number {
        width: 22px; height: 22px; border-radius: 7px;
        background: #123457; color: #E8A33D;
        font-size: 10px; font-weight: 900;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .rd-section-heading h3 { font-size: 12px; font-weight: 800; color: #123457; margin: 0; }
    .rd-section-heading p { font-size: 10px; color: #94a3b8; margin: 1px 0 0; }

    .rd-status-row { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; }
    .rd-desc-box {
        font-size: 11px; font-weight: 700; color: #475569; line-height: 1.6;
        background: #F8FAFC; border: 1.2px solid #E2E8F0; padding: 12px 14px; border-radius: 12px;
    }
    .rd-meta-row {
        display: flex; align-items: center; gap: 6px;
        font-size: 9.5px; font-weight: 700; color: #94a3b8; margin-top: 10px;
    }

    .rd-attachment {
        border-radius: 14px; overflow: hidden; border: 1.2px solid #E2E8F0;
    }
    .rd-attachment img { width: 100%; height: 150px; object-fit: cover; display: block; }

    .admin-response-area {
        background: #0A1E33; color: white; padding: 14px;
        border-radius: 14px; border-left: 4px solid #E8A33D;
    }

    .rd-footer {
        padding: 14px 24px; background: #F8FAFC; border-top: 1px solid #e2e8f0;
        display: flex; justify-content: flex-end; gap: 10px; flex-shrink: 0;
    }
    .rd-btn-close {
        width: 100%;
        padding: 10px 20px; border-radius: 10px; font-size: 11px; font-weight: 800; color: #475569;
        background: #e2e8f0; border: 1px solid #cbd5e1; cursor: pointer; transition: all 0.2s;
        text-transform: uppercase; letter-spacing: 0.6px;
    }
    .rd-btn-close:hover { background: #d1d5db; color: #1e293b; }

    /* ============================================================
       EMPTY STATE
    ============================================================ */
    .empty-report-box {
        border: 1.5px dashed #CBD5E1;
        border-radius: 28px;
        background: #F8FAFC;
        padding: 4rem 1.5rem;
    }
    .empty-report-icon {
        width: 76px; height: 76px; border-radius: 20px;
        background: #0A1E33; color: #E8A33D;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; margin: 0 auto 1.25rem;
    }

    /* ============================================================
       PAGINATION (styling only — logic tetap di JS)
    ============================================================ */
    .pg-btn {
        transition: all .2s ease;
    }

    /* ============================================================
       ANIMATION
    ============================================================ */
    .reveal-item { animation: opFadeSlide .4s ease forwards; }
    @keyframes opFadeSlide {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ============================================================
       RESPONSIVE
    ============================================================ */
    @media (max-width: 480px) {
        .lacak-hero { padding: 4rem 1.25rem 6.5rem; }
        .status-overview-label { display: none; }
        .status-overview-card { justify-content: center; padding: 12px 8px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .reveal-item, .report-card, .status-overview-card, .category-box,
        .rd-overlay, .rd-card, .pulse-dot {
            animation: none !important;
            transition: none !important;
        }
    }
</style>

{{-- ============================================================
     1. HERO SECTION
============================================================ --}}
<section class="lacak-hero">
    <div class="bg-dot-matrix"></div>
    <div class="hero-glow"></div>
    <div class="max-w-7xl mx-auto relative z-10 text-center">
        <div class="reveal">
            <nav class="flex justify-center items-center gap-2 text-accent text-[10px] font-black uppercase tracking-[0.2em] mb-4">
                <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                <i class="bi bi-chevron-right text-[8px]"></i>
                <span>Tinjau Laporan</span>
            </nav>
            <h1 class="text-3xl md:text-5xl font-extrabold text-white leading-tight tracking-tighter">
                Pantau <span class="text-accent italic">Aduan Warga.</span>
            </h1>
            <div class="hero-divider"></div>
            <p class="text-slate-400 mt-5 max-w-xl mx-auto text-sm sm:text-base leading-relaxed">
                Transparansi penuh untuk setiap laporan Anda. Masukkan ID Tiket atau lihat riwayat pengaduan di bawah.
            </p>
        </div>
    </div>
</section>

{{-- ============================================================
     2. SEARCH SECTION
============================================================ --}}
<div class="search-lacak-container px-4">
    <div class="search-lacak-card flex flex-col sm:flex-row gap-2">
        <div class="flex-1 search-lacak-input-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="userSearch" onkeyup="filterReports()" placeholder="Cari Kode Tiket atau Judul Laporan...">
        </div>
        <button class="btn-cari-data">
            Cari Data
        </button>
    </div>
</div>

<section class="py-16 sm:py-24 bg-white min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-8">

        {{-- ============================================================
             3. STATUS OVERVIEW
        ============================================================ --}}
        @php
            $totalLaporan   = $complaints->count();
            $laporanBaru    = $complaints->where('status', 'pending')->count();
            $laporanReview  = $complaints->where('status', 'review')->count();
            $laporanProcess = $complaints->where('status', 'process')->count();
            $laporanDone    = $complaints->where('status', 'done')->count();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-10">
            <div class="status-overview-card">
                <div class="status-overview-icon" style="background:#EFF6FF; color:#2563EB;"><i class="bi bi-stack"></i></div>
                <div>
                    <p class="status-overview-number">{{ $totalLaporan }}</p>
                    <p class="status-overview-label">Total</p>
                </div>
            </div>
            <div class="status-overview-card">
                <div class="status-overview-icon" style="background:#fff1f2; color:#e11d48;"><i class="bi bi-file-earmark-plus"></i></div>
                <div>
                    <p class="status-overview-number">{{ $laporanBaru }}</p>
                    <p class="status-overview-label">Baru</p>
                </div>
            </div>
            <div class="status-overview-card">
                <div class="status-overview-icon" style="background:#eff6ff; color:#3b82f6;"><i class="bi bi-eye"></i></div>
                <div>
                    <p class="status-overview-number">{{ $laporanReview }}</p>
                    <p class="status-overview-label">Ditinjau</p>
                </div>
            </div>
            <div class="status-overview-card">
                <div class="status-overview-icon" style="background:#fefce8; color:#ca8a04;"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <p class="status-overview-number">{{ $laporanProcess }}</p>
                    <p class="status-overview-label">Diproses</p>
                </div>
            </div>
            <div class="status-overview-card">
                <div class="status-overview-icon" style="background:#f0fdf4; color:#16a34a;"><i class="bi bi-check-circle"></i></div>
                <div>
                    <p class="status-overview-number">{{ $laporanDone }}</p>
                    <p class="status-overview-label">Selesai</p>
                </div>
            </div>
        </div>

        {{-- ============================================================
             4. HEADER RIWAYAT
        ============================================================ --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-lg font-black text-primary uppercase tracking-tight">Riwayat Laporan Anda</h2>
                <p class="text-xs text-slate-400 font-semibold mt-1">Laporan yang telah Anda kirimkan</p>
            </div>
            <span class="text-[10px] font-black text-slate-400 bg-[#F8FAFC] px-4 py-2 rounded-full border border-slate-200 shadow-sm" id="reportCount">
                Memuat...
            </span>
        </div>

        {{-- ============================================================
             5 & 6. REPORT CARD (+ hover)
        ============================================================ --}}
        <div id="reportList" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($complaints as $cm)
                @php
                    $catMap = [
                        'Infrastruktur' => ['icon' => 'bi-cone-striped', 'color' => '#E8A33D'],
                        'Kesehatan'     => ['icon' => 'bi-heart-pulse', 'color' => '#EF4444'],
                        'Pelayanan'     => ['icon' => 'bi-person-badge', 'color' => '#123457'],
                        'Lingkungan'    => ['icon' => 'bi-trash3', 'color' => '#123457'],
                        'Lainnya'       => ['icon' => 'bi-grid', 'color' => '#64748B']
                    ];
                    $style = $catMap[$cm->category] ?? $catMap['Lainnya'];
                    $catIcon = $style['icon'];
                    $catColor = $style['color'];
                    $catBgColor = $catColor . '15';
                @endphp

                <div class="report-card reveal"
                     data-id="{{ $cm->id }}"
                     data-title="{{ strtolower($cm->title) }}"
                     data-code="{{ strtolower($cm->ticket_id) }}"
                     data-ticket="{{ $cm->ticket_id }}"
                     data-category="{{ $cm->category }}"
                     data-icon="{{ $catIcon }}"
                     data-catbg="{{ $catBgColor }}"
                     data-catcolor="{{ $catColor }}"
                     onclick="window.openPreviewModal('{{ $cm->id }}')">

                    {{-- 7. CATEGORY ICON --}}
                    <div class="report-card-top">
                        <div class="category-box" style="--cat-bg: {{ $catBgColor }}; --cat-color: {{ $catColor }};">
                            <i class="bi {{ $catIcon }}"></i>
                        </div>

                        {{-- 8. STATUS BADGE --}}
                        @if($cm->status == 'pending')
                            <span class="status-pill pill-pending"><span class="pulse-dot"></span> Baru</span>
                        @elseif($cm->status == 'review')
                            <span class="status-pill pill-review"><i class="bi bi-eye-fill"></i> Ditinjau</span>
                        @elseif($cm->status == 'process')
                            <span class="status-pill pill-process"><span class="pulse-dot"></span> Diproses</span>
                        @elseif($cm->status == 'done')
                            <span class="status-pill pill-done"><i class="bi bi-check-lg"></i> Selesai</span>
                        @else
                            <span class="status-pill pill-rejected"><i class="bi bi-x-lg"></i> Ditolak</span>
                        @endif
                    </div>

                    <div class="report-card-body">
                        <div class="report-tag-row">
                            <span class="ticket-tag">{{ $cm->ticket_id }}</span>
                            <span class="cat-tag">{{ $cm->category }}</span>
                        </div>
                        <h4 class="report-title">{{ $cm->title }}</h4>
                        <p class="report-date"><i class="bi bi-calendar3"></i> {{ $cm->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    <div class="report-card-footer">
                        <span>Lihat Detail Laporan</span>
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            @empty
                {{-- ============================================================
                     9. EMPTY STATE
                ============================================================ --}}
                <div class="md:col-span-2 xl:col-span-3 reveal">
                    <div class="empty-report-box text-center">
                        <div class="empty-report-icon">
                            <i class="bi bi-megaphone"></i>
                        </div>
                        <h3 class="text-lg font-black text-primary-dark">Belum Ada Riwayat Pengaduan</h3>
                        <p class="text-sm text-slate-400 font-semibold mt-2 max-w-sm mx-auto">
                            Pengaduan yang Anda kirimkan akan muncul di halaman ini.
                        </p>
                        @if(Route::has('pengaduan.create'))
                            <a href="{{ route('pengaduan.create') }}"
                               class="inline-flex items-center gap-2 mt-6 px-6 py-3 rounded-xl bg-primary text-white text-xs font-black uppercase tracking-wider hover:bg-primary-dark transition">
                                Buat Pengaduan <i class="bi bi-arrow-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        {{-- ============================================================
             10. PAGINATION (dirender oleh renderPagination() — tidak diubah)
        ============================================================ --}}
        <div id="reportPagination" class="flex items-center justify-center gap-2 mt-8"></div>
    </div>
</section>

<x-user.modal-detail-preview />
@endsection

@push('scripts')
<script>
    // --- KONFIGURASI PAGINASI ---
    const itemsPerPage = 6;
    let currentPage = 1;
    let filteredItems = [];

    /**
     * Memfilter kartu laporan berdasarkan input pencarian (Judul atau Kode)
     */
    function filterReports(){
        const keyword = document.getElementById('userSearch').value.toLowerCase();
        filteredItems = [...document.querySelectorAll('.report-card')]
            .filter(item => {
                return item.dataset.title.includes(keyword) ||
                       item.dataset.code.includes(keyword);
            });
        renderPage(1);
    }

    /**
     * Menampilkan data pada halaman tertentu
     */
    function renderPage(page){
        currentPage = page;
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        // Sembunyikan semua kartu terlebih dahulu
        document.querySelectorAll('.report-card').forEach(card => {
            card.style.display = 'none';
            card.classList.remove('reveal-item');
        });

        // Tampilkan hanya kartu yang masuk dalam range filter & paginasi
        filteredItems.slice(start, end).forEach((card, index) => {
            card.style.display = 'flex';
            // Animasi muncul bertahap
            setTimeout(() => {
                card.classList.add('reveal-item');
            }, index * 60);
        });

        // Update counter jumlah laporan
        const countLabel = document.getElementById('reportCount');
        if(countLabel) {
            countLabel.textContent = filteredItems.length + ' Laporan Ditemukan';
        }

        renderPagination();
    }

    /**
     * Merender tombol-tombol navigasi halaman
     */
    function renderPagination(){
        const wrapper = document.getElementById('reportPagination');
        if(!wrapper) return;

        wrapper.innerHTML = '';
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);

        if(totalPages <= 1) return;

        // Tombol Sebelumnya
        wrapper.innerHTML += `
            <button
                onclick="renderPage(${currentPage - 1})"
                ${currentPage == 1 ? 'disabled' : ''}
                class="pg-btn w-10 h-10 rounded-xl border border-slate-200 hover:border-[#0A1E33] hover:bg-slate-50 flex items-center justify-center text-slate-500 ${currentPage == 1 ? 'opacity-40 cursor-not-allowed' : ''}">
                <i class="bi bi-chevron-left"></i>
            </button>
        `;

        // Nomor Halaman
        for(let i = 1; i <= totalPages; i++){
            wrapper.innerHTML += `
                <button
                    onclick="renderPage(${i})"
                    class="pg-btn w-10 h-10 rounded-xl font-black ${
                        i == currentPage
                        ? 'bg-[#0A1E33] text-white shadow-lg'
                        : 'border border-slate-200 hover:border-[#E8A33D] hover:text-[#0A1E33] text-slate-500'
                    }">
                    ${i}
                </button>
            `;
        }

        // Tombol Selanjutnya
        wrapper.innerHTML += `
            <button
                onclick="renderPage(${currentPage + 1})"
                ${currentPage == totalPages ? 'disabled' : ''}
                class="pg-btn w-10 h-10 rounded-xl border border-slate-200 hover:border-[#0A1E33] hover:bg-slate-50 flex items-center justify-center text-slate-500 ${currentPage == totalPages ? 'opacity-40 cursor-not-allowed' : ''}">
                <i class="bi bi-chevron-right"></i>
            </button>
        `;
    }

    // Inisialisasi awal saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        filteredItems = [...document.querySelectorAll('.report-card')];
        renderPage(1);
    });
</script>
@endpush