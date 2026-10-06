@extends('layouts.admin')

@section('title', 'Pusat Notifikasi')
@section('page-title', 'Pusat Notifikasi')
@section('page-icon', 'bi-bell-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<style>
    /* ==========================================================
       PUSAT NOTIFIKASI — Sukabumi One Access
       Palette: Navy #0A1E33 / #123457, Gold #E8A33D, White/Slate
       ========================================================== */

    .notif-page { max-width: 880px; margin: 0 auto; padding-bottom: 40px; }

    /* --- BANNER --- */
    .id-banner-sleek {
        position: relative;
        background: linear-gradient(135deg, #0A1E33 0%, #123457 100%);
        border-radius: 18px;
        overflow: hidden;
        padding: 22px 26px;
        border-bottom: 3px solid #E8A33D;
        box-shadow: 0 12px 24px -12px rgba(10, 30, 51, 0.45);
        margin-bottom: 22px;
    }

    .id-pattern {
        position: absolute; inset: 0;
        background-image: radial-gradient(rgba(232, 163, 61, 0.12) 0.5px, transparent 0.5px);
        background-size: 14px 14px; opacity: 0.6;
        pointer-events: none;
    }

    .id-banner-sleek .banner-flex {
        position: relative; z-index: 1;
        display: flex; align-items: center; justify-content: space-between;
        gap: 16px; flex-wrap: wrap;
    }

    .id-banner-sleek .banner-left { display: flex; align-items: center; gap: 14px; min-width: 0; }

    .banner-icon-wrap {
        width: 46px; height: 46px; border-radius: 13px; flex-shrink: 0;
        background: rgba(232, 163, 61, 0.12);
        border: 1px solid rgba(232, 163, 61, 0.35);
        display: flex; align-items: center; justify-content: center;
        color: #E8A33D; font-size: 1.15rem;
    }

    .id-banner-sleek h1 {
        font-size: 1.15rem; font-weight: 800; color: #fff;
        letter-spacing: -0.01em; line-height: 1.15; margin: 0;
    }

    .id-banner-sleek .banner-sub {
        color: #92a3ba; font-size: 10.5px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em; margin-top: 5px;
    }

    .banner-badge {
        display: flex; align-items: center; gap: 10px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 8px 16px;
        flex-shrink: 0;
    }

    .banner-badge .num { font-size: 1.35rem; font-weight: 800; color: #fff; line-height: 1; }
    .banner-badge .lbl { font-size: 8.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.09em; color: #92a3ba; }
    .banner-badge .unit { font-size: 10px; color: #E8A33D; font-weight: 700; }

    /* --- TOOLBAR: segmented filter + mark all --- */
    .notif-toolbar {
        display: flex; align-items: center; justify-content: space-between;
        gap: 14px; margin-bottom: 20px; flex-wrap: wrap;
    }

    .segmented-filter {
        display: inline-flex; align-items: center; gap: 3px;
        background: #f1f5f9; border-radius: 13px; padding: 4px;
    }

    .filter-btn {
        position: relative;
        padding: 7px 16px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        background: transparent;
        border: none;
        transition: color 0.25s ease, background 0.25s ease;
        white-space: nowrap;
    }

    .filter-btn:hover { color: #123457; }

    .filter-btn.active {
        background: #123457;
        color: #E8A33D;
        box-shadow: 0 4px 10px -3px rgba(18, 52, 87, 0.45);
    }

    .btn-mark-all {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 10.5px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: #123457;
        background: #fff;
        border: 1.5px solid #e6ebf1;
        border-radius: 10px;
        padding: 8px 14px;
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .btn-mark-all:hover {
        border-color: #E8A33D;
        color: #123457;
        background: #fff8ec;
    }

    .btn-mark-all i { font-size: 12px; color: #E8A33D; }

    /* --- NOTIFICATION CARD --- */
    #notifList { display: flex; flex-direction: column; gap: 10px; }

    .notif-row-card {
        position: relative;
        background: #ffffff;
        border: 1px solid #eef1f5;
        border-radius: 16px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
        overflow: hidden;
    }

    /* left accent bar encodes notification family without touching data */
    .notif-row-card::before {
        content: "";
        position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
        background: #cbd5e1;
    }

    .notif-row-card[data-type="complaint"]::before { background: #E8734D; }
    .notif-row-card[data-type="submission"]::before { background: #E8A33D; }

    .notif-row-card:hover {
        border-color: #E8A33D;
        box-shadow: 0 10px 22px -12px rgba(10, 30, 51, 0.18);
        transform: translateX(3px);
    }

    .icon-box-notif {
        width: 44px; height: 44px; border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; flex-shrink: 0;
    }

    .notif-body { flex: 1; min-width: 0; }

    .notif-meta {
        display: flex; align-items: center; gap: 7px; flex-wrap: wrap;
        margin-bottom: 4px;
    }

    .notif-code {
        font-size: 9px; font-weight: 800; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.06em;
    }

    .meta-dot { width: 3px; height: 3px; border-radius: 50%; background: #cbd5e1; flex-shrink: 0; }

    .type-pill {
        font-size: 8.5px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.05em; padding: 2px 8px; border-radius: 999px;
    }

    .notif-title {
        font-size: 13.5px; font-weight: 700; color: #0A1E33;
        line-height: 1.3; margin: 0 0 3px 0;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }

    .notif-time {
        font-size: 10px; color: #94a3b8; font-weight: 600; margin: 0;
        display: flex; align-items: center; gap: 5px;
    }

    .notif-time i { font-size: 10px; color: #cbd5e1; }

    .btn-notif-action {
        display: inline-flex; align-items: center; gap: 6px;
        background: #123457;
        color: #fff;
        padding: 9px 16px;
        border-radius: 11px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        transition: background 0.25s ease, transform 0.2s ease, box-shadow 0.25s ease;
        flex-shrink: 0;
        white-space: nowrap;
    }

    .btn-notif-action i { font-size: 10px; transition: transform 0.25s ease; }

    .btn-notif-action:hover {
        background: #E8A33D;
        color: #0A1E33;
        box-shadow: 0 6px 14px -6px rgba(232, 163, 61, 0.55);
    }

    .btn-notif-action:hover i { transform: translateX(2px); }

    /* --- EMPTY STATE --- */
    .notif-empty {
        text-align: center;
        padding: 64px 20px;
        background: #fff;
        border: 1px solid #eef1f5;
        border-radius: 18px;
    }

    .notif-empty .empty-icon {
        width: 58px; height: 58px; border-radius: 50%;
        background: #f8fafc; display: flex; align-items: center; justify-content: center;
        margin: 0 auto 14px auto;
    }

    .notif-empty .empty-icon i { font-size: 1.4rem; color: #cbd5e1; }
    .notif-empty h4 { color: #0A1E33; font-weight: 800; font-size: 15px; margin: 0 0 4px 0; }
    .notif-empty p { color: #94a3b8; font-size: 12.5px; margin: 0; }

    /* --- ANIMATION --- */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .reveal-item { animation: fadeInUp 0.4s ease forwards; opacity: 0; }

    @media (prefers-reduced-motion: reduce) {
        .reveal-item { animation: none; opacity: 1; }
        .notif-row-card, .btn-notif-action, .filter-btn, .btn-mark-all { transition: none; }
    }

    /* --- RESPONSIVE --- */
    @media (max-width: 767px) {
        .id-banner-sleek { padding: 18px 18px; }
        .id-banner-sleek h1 { font-size: 1rem; }
        .banner-badge { display: none; }

        .notif-toolbar { flex-direction: column; align-items: stretch; }
        .segmented-filter { width: 100%; justify-content: space-between; }
        .filter-btn { flex: 1; text-align: center; padding: 8px 10px; }
        .btn-mark-all { justify-content: center; }

        .notif-row-card { flex-wrap: wrap; padding: 14px; }
        .notif-body { width: calc(100% - 60px); order: 2; }
        .icon-box-notif { order: 1; }
        .action-notif { order: 3; width: 100%; margin-top: 10px; }
        .btn-notif-action { width: 100%; justify-content: center; }
        .notif-title { white-space: normal; }
    }

    @media (min-width: 768px) and (max-width: 1023px) {
        .id-banner-sleek { padding: 24px 28px; }
    }
</style>

<div class="notif-page">

    {{-- --- BANNER --- --}}
    <div class="id-banner-sleek reveal-item">
        <div class="id-pattern"></div>
        <div class="banner-flex">
            <div class="banner-left">
                <div class="banner-icon-wrap">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <div class="min-w-0">
                    <h1>Pusat Notifikasi</h1>
                    <p class="banner-sub">Pantau seluruh aktivitas masuk secara terpadu</p>
                </div>
            </div>
            <div class="banner-badge">
                <span class="num">{{ count($allNotifications) }}</span>
                <div>
                    <div class="lbl">Belum Dibaca</div>
                    <div class="unit">Pesan Masuk</div>
                </div>
            </div>
        </div>
    </div>

    {{-- --- TOOLBAR: FILTER + MARK ALL --- --}}
    <div class="notif-toolbar reveal-item" style="animation-delay: 0.08s">
        <div class="segmented-filter">
            <button class="filter-btn active" onclick="filterType('all', this)">Semua</button>
            <button class="filter-btn" onclick="filterType('complaint', this)">Pengaduan</button>
            <button class="filter-btn" onclick="filterType('submission', this)">Layanan</button>
        </div>

        <button id="markAllNotificationRead" class="btn-mark-all">
            <i class="bi bi-check2-all"></i>
            Tandai Semua Dibaca
        </button>
    </div>

    {{-- --- NOTIFICATION LIST --- --}}
    <div id="notifList">
        @forelse($allNotifications as $notif)
            <div class="notif-row-card reveal-item notif-item-box"
                data-type="{{ $notif->type }}"
                data-delay="{{ 0.1 + ($loop->index * 0.05) }}">

                <div class="icon-box-notif {{ $notif->bg }} {{ $notif->color }}">
                    <i class="bi {{ $notif->icon }}"></i>
                </div>

                <div class="notif-body">
                    <div class="notif-meta">
                        <span class="notif-code">{{ $notif->code }}</span>
                        <span class="meta-dot"></span>
                        <span class="type-pill {{ $notif->bg }} {{ $notif->color }}">
                            {{ $notif->type == 'complaint' ? 'Pengaduan' : 'Layanan' }}
                        </span>
                    </div>
                    <h4 class="notif-title">{{ $notif->subject }}</h4>
                    <p class="notif-time">
                        <i class="bi bi-clock-history"></i>
                        {{ $notif->created_at->translatedFormat('d M Y, H:i') }} &middot; {{ $notif->created_at->diffForHumans() }}
                    </p>
                </div>

                <div class="action-notif">
                    @php
                        $targetUrl = $notif->type == 'complaint'
                            ? route('admin.pengaduan.index', $portalCode)
                            : route('admin.submission.index', $portalCode);
                    @endphp
                    <a href="{{ $targetUrl }}" class="btn-notif-action no-underline">
                        Periksa
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="notif-empty reveal-item">
                <div class="empty-icon">
                    <i class="bi bi-bell-slash"></i>
                </div>
                <h4>Tidak ada notifikasi baru</h4>
                <p>Semua tugas Anda telah selesai dikerjakan.</p>
            </div>
        @endforelse
    </div>

</div>

<script>
    document.querySelectorAll('.notif-item-box').forEach(item => {
        const delay = item.dataset.delay;
        if (delay) {
            item.style.animationDelay = `${delay}s`;
        }
    });

    function filterType(type, el) {
        // Toggle Active UI
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        el.classList.add('active');

        // Filter List
        const items = document.querySelectorAll('.notif-item-box');
        items.forEach(item => {
            if (type === 'all' || item.dataset.type === type) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>

@endsection