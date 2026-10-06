@extends('layouts.app')

@section('title', 'Pusat Pemberitahuan - Sukabumi One Access')

@section('content')
<style>
    /* ============ HERO ============ */
    .notif-hero {
        background: linear-gradient(160deg, #0A1E33 0%, #0f2a47 55%, #0A1E33 100%);
        padding: 4.5rem 1.5rem 6.5rem;
        border-bottom: 4px solid #E8A33D;
        position: relative;
        overflow: hidden;
    }
    .bg-aura {
        position: absolute;
        top: -50%;
        right: -8%;
        width: 420px;
        height: 420px;
        background: radial-gradient(circle, rgba(232,163,61,0.10) 0%, transparent 70%);
        pointer-events: none;
    }
    .bg-aura-2 {
        position: absolute;
        bottom: -35%;
        left: -10%;
        width: 360px;
        height: 360px;
        background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 70%);
        pointer-events: none;
    }
    .notif-badge-hero {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(232,163,61,0.12);
        border: 1px solid rgba(232,163,61,0.3);
        color: #E8A33D;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        padding: 6px 14px;
        border-radius: 999px;
        margin-bottom: 1.25rem;
    }

    /* ============ CONTAINER ============ */
    .notif-page {
        width: 100%;
        max-width: 1180px;
        margin: 0 auto;
        padding: 0 1.5rem 5rem;
    }

    /* ============ TOOLBAR (upgraded) ============ */
    .notif-toolbar {
        margin-top: -2.75rem;
        margin-bottom: 1.75rem;
        background: #ffffff;
        border: 1px solid #eef1f5;
        border-radius: 20px;
        box-shadow: 0 16px 40px -18px rgba(10, 30, 51, 0.18);
        padding: 1.4rem 1.6rem;
        position: relative;
        z-index: 5;
    }

    .notif-toolbar-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.1rem;
    }

    .notif-toolbar-heading {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .notif-toolbar-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(160deg, #123457, #0A1E33);
        color: #E8A33D;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .notif-toolbar-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f2a47;
        line-height: 1.2;
    }
    .notif-toolbar-sub {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 2px;
    }

    .notif-count-pill {
        background: #123457;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 5px 13px;
        border-radius: 999px;
        flex-shrink: 0;
        white-space: nowrap;
    }

    /* ---- Filter tabs ---- */
    .notif-tabs {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        padding-top: 1rem;
        border-top: 1px solid #f1f5f9;
    }
    .notif-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 16px;
        border-radius: 12px;
        border: 1.5px solid #eef1f5;
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: border-color 0.2s ease, background-color 0.2s ease, color 0.2s ease;
        white-space: nowrap;
    }
    .notif-tab-btn i { font-size: 12px; }
    .notif-tab-btn .tab-count {
        background: rgba(100, 116, 139, 0.12);
        color: inherit;
        font-size: 10px;
        font-weight: 800;
        padding: 1px 7px;
        border-radius: 999px;
    }
    .notif-tab-btn:hover {
        border-color: #E8A33D;
        color: #123457;
    }
    .notif-tab-btn.is-active {
        background: #123457;
        border-color: #123457;
        color: #ffffff;
    }
    .notif-tab-btn.is-active .tab-count {
        background: rgba(232, 163, 61, 0.25);
        color: #ffffff;
    }
    .notif-tab-btn.is-active i { color: #E8A33D; }

    /* ============ GRID (3 per baris di desktop) ============ */
    .notif-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        align-items: stretch;
    }

    /* ============ CARD ============ */
    .notif-card-row {
        background: #ffffff;
        border: 1.5px solid #f1f5f9;
        border-radius: 18px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        text-decoration: none !important;
        position: relative;
        transition: border-color 0.22s ease, box-shadow 0.22s ease, background-color 0.22s ease;
        min-height: 190px;
    }

    .notif-card-row:hover {
        border-color: #E8A33D;
        box-shadow: 0 14px 32px -14px rgba(10, 30, 51, 0.16);
        background-color: #fffdf9;
    }
    .notif-card-row:active { background-color: #fdf7ee; }

    .notif-card-row::before {
        content: "";
        position: absolute;
        left: 0;
        top: 14px;
        bottom: 14px;
        width: 3px;
        border-radius: 4px;
        background: transparent;
        transition: background-color 0.22s ease;
    }
    .notif-card-row.accent-complaint::before { background: #123457; }
    .notif-card-row.accent-submission::before { background: #E8A33D; }

    .notif-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .notif-icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .type-complaint { background: rgba(18, 52, 87, 0.07); color: #123457; }
    .type-submission { background: rgba(232, 163, 61, 0.12); color: #E8A33D; }

    .icon-done { background: rgba(22, 163, 74, 0.10) !important; color: #16a34a !important; }
    .icon-rejected { background: rgba(220, 38, 38, 0.10) !important; color: #dc2626 !important; }
    .icon-revisi { background: rgba(217, 119, 6, 0.10) !important; color: #d97706 !important; }

    .notif-chevron {
        color: #e2e8f0;
        font-size: 1.05rem;
        flex-shrink: 0;
        margin-top: 4px;
        transition: transform 0.22s ease, color 0.22s ease;
    }
    .notif-card-row:hover .notif-chevron {
        color: #E8A33D;
        transform: translateX(3px);
    }

    .notif-main { min-width: 0; }

    .notif-meta-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }
    .notif-ticket-code {
        font-size: 9px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .notif-type-label {
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 2px 8px;
        border-radius: 999px;
    }
    .label-complaint { background: rgba(18, 52, 87, 0.08); color: #123457; }
    .label-submission { background: rgba(232, 163, 61, 0.14); color: #b9791f; }

    .notif-title {
        font-size: 0.9rem;
        font-weight: 800;
        color: #0f2a47;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .notif-message {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 5px;
        line-height: 1.55;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .notif-time {
        font-size: 10px;
        font-weight: 700;
        color: #b0b9c6;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: auto;
        padding-top: 8px;
    }

    /* ============ PAGINATION ============ */
    .notif-pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 2.5rem;
        flex-wrap: wrap;
    }
    .notif-page-btn {
        min-width: 38px;
        height: 38px;
        padding: 0 10px;
        border-radius: 11px;
        border: 1.5px solid #eef1f5;
        background: #ffffff;
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: border-color 0.2s ease, background-color 0.2s ease, color 0.2s ease;
    }
    .notif-page-btn:hover { border-color: #E8A33D; color: #123457; }
    .notif-page-btn.is-active {
        background: #123457;
        border-color: #123457;
        color: #ffffff;
    }
    .notif-page-btn:disabled {
        opacity: 0.35;
        cursor: not-allowed;
    }
    .notif-page-btn:disabled:hover { border-color: #eef1f5; color: #64748b; }

    /* ============ EMPTY STATE ============ */
    .notif-empty {
        text-align: center;
        padding: 5rem 1.5rem;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 20px;
        grid-column: 1 / -1;
    }
    .notif-empty-icon {
        width: 88px;
        height: 88px;
        margin: 0 auto 1.5rem;
        border-radius: 22px;
        background: linear-gradient(160deg, #f8fafc, #eef2f7);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        color: #cbd5e1;
        border: 1px solid #f1f5f9;
    }
    .notif-empty-title {
        font-weight: 800;
        color: #123457;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .notif-empty-sub {
        color: #94a3b8;
        font-size: 0.8rem;
        margin-top: 6px;
    }

    /* ============ ANIMATION ============ */
    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .reveal-notif {
        animation: fadeSlideIn 0.35s ease forwards;
        opacity: 0;
    }
    .notif-grid > .reveal-notif:nth-child(1) { animation-delay: 0.00s; }
    .notif-grid > .reveal-notif:nth-child(2) { animation-delay: 0.04s; }
    .notif-grid > .reveal-notif:nth-child(3) { animation-delay: 0.08s; }
    .notif-grid > .reveal-notif:nth-child(4) { animation-delay: 0.12s; }
    .notif-grid > .reveal-notif:nth-child(5) { animation-delay: 0.16s; }
    .notif-grid > .reveal-notif:nth-child(6) { animation-delay: 0.20s; }
    .notif-grid > .reveal-notif:nth-child(n+7) { animation-delay: 0.24s; }

    @media (prefers-reduced-motion: reduce) {
        .reveal-notif { animation: none; opacity: 1; }
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 992px) {
        .notif-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .notif-hero { padding: 3.5rem 1.25rem 5.5rem; }
        .notif-page { padding: 0 1rem 3.5rem; }
        .notif-toolbar { margin-top: -2.25rem; padding: 1.1rem; border-radius: 16px; }
        .notif-toolbar-title { font-size: 0.85rem; }
        .notif-tabs { gap: 6px; }
        .notif-tab-btn { padding: 7px 12px; font-size: 11px; }
        .notif-grid { grid-template-columns: 1fr; gap: 14px; }
        .notif-card-row { padding: 16px; min-height: auto; }
        .notif-title { font-size: 0.85rem; }
        .notif-message { font-size: 0.74rem; }
        .notif-pagination { margin-top: 1.75rem; }
    }
</style>

<section class="notif-hero text-center">
    <div class="bg-aura"></div>
    <div class="bg-aura-2"></div>
    <div class="relative z-10">
        <nav class="flex justify-center items-center gap-2 text-accent text-[10px] font-black uppercase tracking-[0.2em] mb-4">
            <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
            <i class="bi bi-chevron-right text-[8px]"></i>
            <span class="text-white/70">Pemberitahuan</span>
        </nav>

        <span class="notif-badge-hero">
            <i class="bi bi-bell-fill"></i> Pusat Notifikasi
        </span>

        <h1 class="text-white text-3xl md:text-5xl font-black tracking-tighter">
            Kotak <span class="text-accent italic">Masuk.</span>
        </h1>
        <p class="text-slate-400 mt-3 max-w-md mx-auto text-sm md:text-base">
            Informasi terbaru mengenai laporan dan pengajuan Anda di Sukabumi One Access.
        </p>
    </div>
</section>

<div class="notif-page">

    @php
        // Hitung total per kategori tanpa menyentuh struktur/collection asli
        $notifTotalAll = count($allNotifications);
        $notifTotalComplaint = 0;
        $notifTotalSubmission = 0;
        foreach ($allNotifications as $notifCount) {
            if ($notifCount->notif_type === 'complaint') {
                $notifTotalComplaint++;
            } else {
                $notifTotalSubmission++;
            }
        }
    @endphp

    @if($notifTotalAll > 0)
        <div class="notif-toolbar">
            <div class="notif-toolbar-top">
                <div class="notif-toolbar-heading">
                    <div class="notif-toolbar-icon">
                        <i class="bi bi-inboxes-fill"></i>
                    </div>
                    <div>
                        <div class="notif-toolbar-title">Pusat Pemberitahuan</div>
                        <div class="notif-toolbar-sub">Pantau seluruh update aduan &amp; pengajuan Anda</div>
                    </div>
                </div>
                <span class="notif-count-pill">{{ $notifTotalAll }} Notifikasi</span>
            </div>

            <div class="notif-tabs" id="notifTabs">
                <button type="button" class="notif-tab-btn is-active" data-filter="all">
                    <i class="bi bi-grid-fill"></i> Semua
                    <span class="tab-count">{{ $notifTotalAll }}</span>
                </button>
                <button type="button" class="notif-tab-btn" data-filter="complaint">
                    <i class="bi bi-megaphone-fill"></i> Pengaduan
                    <span class="tab-count">{{ $notifTotalComplaint }}</span>
                </button>
                <button type="button" class="notif-tab-btn" data-filter="submission">
                    <i class="bi bi-folder2-open"></i> Pengajuan
                    <span class="tab-count">{{ $notifTotalSubmission }}</span>
                </button>
            </div>
        </div>
    @endif

    <div class="notif-grid" id="notifGrid">
        @forelse($allNotifications as $index => $notif)
            @php
                $isComplaint = $notif->notif_type === 'complaint';
                $targetUrl = $isComplaint ? route('user.riwayat.pengaduan') : route('user.riwayat.pengajuan');

                // Tentukan Icon berdasarkan status
                $icon = 'bi-bell-fill';
                $statusClass = '';
                if($notif->status == 'done' || $notif->status == 'selesai') {
                    $icon = 'bi-check-circle-fill';
                    $statusClass = 'icon-done';
                }
                if($notif->status == 'rejected' || $notif->status == 'ditolak') {
                    $icon = 'bi-x-circle-fill';
                    $statusClass = 'icon-rejected';
                }
                if($notif->status == 'perlu_revisi') {
                    $icon = 'bi-exclamation-triangle-fill';
                    $statusClass = 'icon-revisi';
                }
            @endphp

            <a href="{{ $targetUrl }}"
               class="notif-card-row reveal-notif {{ $isComplaint ? 'accent-complaint' : 'accent-submission' }}"
               data-notif-type="{{ $isComplaint ? 'complaint' : 'submission' }}">

                <div class="notif-card-top">
                    <div class="notif-icon-circle {{ $isComplaint ? 'type-complaint' : 'type-submission' }} {{ $statusClass }}">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <i class="bi bi-chevron-right notif-chevron"></i>
                </div>

                <div class="notif-main">
                    <div class="notif-meta-row">
                        <span class="notif-ticket-code">
                            {{ $isComplaint ? $notif->ticket_id : $notif->nomor_pengajuan }}
                        </span>
                        <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                        <span class="notif-type-label {{ $isComplaint ? 'label-complaint' : 'label-submission' }}">
                            {{ $isComplaint ? 'Update Aduan' : 'Update Berkas' }}
                        </span>
                    </div>

                    <h4 class="notif-title">
                        {{ $isComplaint ? $notif->title : $notif->layanan->nama }}
                    </h4>

                    <p class="notif-message">
                        {{ $notif->response_message ?? $notif->catatan_admin ?? 'Status laporan Anda telah diperbarui menjadi ' . str_replace('_', ' ', $notif->status) }}
                    </p>
                </div>

                <span class="notif-time">
                    <i class="bi bi-clock-history"></i> {{ $notif->updated_at->diffForHumans() }}
                </span>
            </a>
        @empty
            <div class="notif-empty">
                <div class="notif-empty-icon">
                    <i class="bi bi-bell-slash"></i>
                </div>
                <p class="notif-empty-title">Tidak Ada Pemberitahuan</p>
                <p class="notif-empty-sub">Semua laporan dan pengajuan Anda akan muncul di sini.</p>
            </div>
        @endforelse
    </div>

    <div class="notif-pagination" id="notifPagination"></div>

</div>

@if($notifTotalAll > 0)
<script>
(function () {
    var grid = document.getElementById('notifGrid');
    var pagination = document.getElementById('notifPagination');
    var tabs = document.getElementById('notifTabs');
    if (!grid || !pagination) return;

    var PER_PAGE = 6; // 3 kolom x 2 baris per halaman
    var allCards = Array.prototype.slice.call(grid.querySelectorAll('[data-notif-type]'));
    var currentFilter = 'all';
    var currentPage = 1;

    function getFilteredCards() {
        if (currentFilter === 'all') return allCards;
        return allCards.filter(function (card) {
            return card.getAttribute('data-notif-type') === currentFilter;
        });
    }

    function buildPaginationButtons(totalPages) {
        pagination.innerHTML = '';
        if (totalPages <= 1) return;

        var prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.className = 'notif-page-btn';
        prevBtn.setAttribute('aria-label', 'Sebelumnya');
        prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
        prevBtn.disabled = (currentPage === 1);
        prevBtn.addEventListener('click', function () {
            if (currentPage > 1) renderPage(currentPage - 1);
        });
        pagination.appendChild(prevBtn);

        for (var p = 1; p <= totalPages; p++) {
            (function (pageNum) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'notif-page-btn notif-page-number' + (pageNum === currentPage ? ' is-active' : '');
                btn.textContent = pageNum;
                btn.addEventListener('click', function () {
                    renderPage(pageNum);
                });
                pagination.appendChild(btn);
            })(p);
        }

        var nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.className = 'notif-page-btn';
        nextBtn.setAttribute('aria-label', 'Berikutnya');
        nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
        nextBtn.disabled = (currentPage === totalPages);
        nextBtn.addEventListener('click', function () {
            if (currentPage < totalPages) renderPage(currentPage + 1);
        });
        pagination.appendChild(nextBtn);
    }

    function renderPage(page) {
        var filtered = getFilteredCards();
        var totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
        currentPage = Math.min(Math.max(1, page), totalPages);

        allCards.forEach(function (card) {
            card.style.display = 'none';
        });

        var start = (currentPage - 1) * PER_PAGE;
        var end = start + PER_PAGE;
        filtered.slice(start, end).forEach(function (card) {
            card.style.display = '';
        });

        buildPaginationButtons(totalPages);
    }

    if (tabs) {
        var tabButtons = tabs.querySelectorAll('.notif-tab-btn');
        tabButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                tabButtons.forEach(function (b) { b.classList.remove('is-active'); });
                btn.classList.add('is-active');
                currentFilter = btn.getAttribute('data-filter');
                renderPage(1);
            });
        });
    }

    renderPage(1);
})();
</script>
@endif
@endsection