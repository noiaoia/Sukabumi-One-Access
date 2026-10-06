@extends('layouts.app')

@section('title', $layanan->nama . ' - Sukabumi One Access')

@section('content')

<style>
    html, body {
        height: 100%;
        margin: 0;
    }

    .portal-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 75px);
        background-color: #ffffff;
        transition: none;
    }

    /* MODE FULL-PAGE: nutupin seluruh layar, termasuk nav/footer situs utama */
    .portal-container.is-fullpage {
        position: fixed;
        inset: 0;
        height: 100vh;
        width: 100vw;
        z-index: 9999;
    }

    .btn-fullpage-toggle {
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.14);
        color: #ffffff;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 7px 14px;
        border-radius: 10px;
        transition: all 0.25s ease;
        position: relative;
        z-index: 2;
        cursor: pointer;
    }

    .btn-fullpage-toggle:hover {
        background: rgba(232,163,61,0.15);
        border-color: rgba(232,163,61,0.4);
        color: #E8A33D;
    }

    .portal-header {
        background-color: #0A1E33;
        padding: 0.75rem 1.5rem;
        border-bottom: 3px solid #E8A33D;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }

    .portal-header::before {
        content: '';
        position: absolute;
        inset: 0;
        opacity: 0.08;
        background-image: radial-gradient(#E8A33D 1px, transparent 0);
        background-size: 22px 22px;
        pointer-events: none;
    }

    .btn-back-portal {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }

    .btn-back-portal:hover { color: #E8A33D; }

    .portal-title {
        color: #ffffff;
        font-weight: 800;
        font-size: 15px;
        margin: 0;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .btn-open-newtab {
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.14);
        color: #ffffff;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 7px 14px;
        border-radius: 10px;
        transition: all 0.25s ease;
        position: relative;
        z-index: 2;
    }

    .btn-open-newtab:hover {
        background: rgba(232,163,61,0.15);
        border-color: rgba(232,163,61,0.4);
        color: #E8A33D;
    }

    /* PROGRESS BAR TIPIS DI ATAS IFRAME */
    .load-progress-track {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: rgba(18,52,87,0.08);
        z-index: 101;
        overflow: hidden;
    }

    .load-progress-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #123457, #E8A33D);
        transition: width 0.3s ease;
    }

    .iframe-wrapper {
        flex-grow: 1;
        position: relative;
        width: 100%;
        background: #ffffff;
    }

    #Iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    /* PREMIUM LOADER */
    .portal-loader {
        position: absolute;
        inset: 0;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 100;
        transition: opacity 0.5s ease;
    }

    .loader-badge {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        background: #F4F6F8;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        position: relative;
    }

    .loader-ring {
        position: absolute;
        inset: 0;
        border: 3px solid transparent;
        border-top-color: #E8A33D;
        border-right-color: #E8A33D;
        border-radius: 20px;
        animation: spinPortal 1s linear infinite;
    }

    @keyframes spinPortal { to { transform: rotate(360deg); } }

    .loader-title {
        font-size: 14px;
        font-weight: 800;
        color: #123457;
        margin-bottom: 4px;
    }

    .loader-sub {
        font-size: 11.5px;
        color: #94a3b8;
        max-width: 260px;
        text-align: center;
        line-height: 1.6;
    }

    .loader-dots span {
        display: inline-block;
        width: 5px; height: 5px;
        border-radius: 50%;
        background: #E8A33D;
        margin: 0 2px;
        animation: loaderDotPulse 1.2s ease-in-out infinite;
    }
    .loader-dots span:nth-child(2) { animation-delay: 0.15s; }
    .loader-dots span:nth-child(3) { animation-delay: 0.3s; }

    @keyframes loaderDotPulse {
        0%, 100% { opacity: 0.25; transform: scale(0.85); }
        50% { opacity: 1; transform: scale(1); }
    }

    /* FALLBACK JIKA IFRAME GAGAL DIMUAT (X-Frame-Options dari situs eksternal) */
    .portal-fallback {
        position: absolute;
        inset: 0;
        background: #ffffff;
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 99;
        padding: 2rem;
        text-align: center;
    }

    .portal-fallback.show { display: flex; }

    /* ================= MOBILE REFINEMENTS ================= */
    @media (max-width: 768px) {
        .portal-container { height: calc(100vh - 65px); }

        .portal-header {
            padding: 0.6rem 0.85rem;
        }

        .btn-back-portal span,
        .btn-open-newtab span,
        #fullpageLabel {
            display: none;
        }

        .portal-title {
            font-size: 12px;
            letter-spacing: 0.01em;
        }

        .btn-open-newtab,
        .btn-fullpage-toggle {
            padding: 7px 10px;
            border-radius: 9px;
        }
    }

    @media (max-width: 480px) {
        .portal-header {
            padding: 0.55rem 0.65rem;
        }

        .portal-header .flex.items-center.gap-4 {
            gap: 0.6rem;
        }

        .portal-header .flex.items-center.gap-3 {
            gap: 0.4rem;
        }

        .portal-divider {
            display: none; /* biar nggak sesak di layar sempit */
        }

        .btn-back-portal i { font-size: 1.2rem; }

        .portal-title { font-size: 11px; }

        .btn-open-newtab,
        .btn-fullpage-toggle {
            width: 34px;
            height: 34px;
            padding: 0;
            justify-content: center;
        }

        .w-10.h-10.rounded-xl {
            width: 34px;
            height: 34px;
        }

        /* Loader & fallback biar nggak kepotong di layar kecil */
        .loader-badge { width: 52px; height: 52px; border-radius: 16px; }
        .loader-title { font-size: 12.5px; }
        .loader-sub { font-size: 10.5px; max-width: 220px; }

        .portal-fallback { padding: 1.25rem; }
        .portal-fallback .w-16.h-16 { width: 52px; height: 52px; }
        .portal-fallback h4 { font-size: 13.5px; }
        .portal-fallback p { font-size: 11px; }
    }
</style>

<div class="portal-container" id="portalContainer">

    {{-- Mini Portal Header --}}
    <div class="portal-header shadow-lg" id="portalHeaderBar">
        <div class="flex items-center gap-4">
            <a href="{{ route('instansi.show', $layanan->instansi->slug) }}" class="btn-back-portal">
                <i class="bi bi-arrow-left-circle-fill text-2xl"></i>
                <span>Kembali ke {{ $layanan->instansi->nama }}</span>
            </a>
            <div class="h-6 w-[1px] bg-white/20 mx-1"></div>
            <h1 class="portal-title">
                {{ $layanan->nama }} <span class="hidden sm:inline">Kota Sukabumi</span>
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" class="btn-fullpage-toggle" id="btnFullpageToggle" onclick="toggleFullpage()">
                <i class="bi bi-arrows-fullscreen" id="fullpageIcon"></i> <span id="fullpageLabel">Layar Penuh</span>
            </button>
            <a href="{{ $layanan->iframe_url }}" target="_blank" rel="noopener noreferrer" class="btn-open-newtab">
                <i class="bi bi-box-arrow-up-right"></i> <span>Buka Tab Baru</span>
            </a>
            <div class="hidden sm:block text-right relative z-10">
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest leading-none">Security Status</p>
                <p class="text-[11px] font-black text-emerald-400 uppercase">Protected Connection</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center border border-white/10 text-emerald-400 relative z-10">
                <i class="bi bi-shield-lock-fill text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Iframe Container --}}
    <div class="iframe-wrapper">

        <div class="load-progress-track" id="loadProgressTrack">
            <div class="load-progress-fill" id="loadProgressFill"></div>
        </div>

        {{-- Loader premium --}}
        <div id="portalLoader" class="portal-loader">
            <div class="loader-badge">
                <div class="loader-ring"></div>
                <i class="bi bi-bank text-primary text-2xl"></i>
            </div>
            <p class="loader-title">Menghubungkan ke Portal {{ $layanan->nama }}</p>
            <p class="loader-sub">Mohon tunggu sebentar, kami sedang membuka koneksi aman ke sistem {{ $layanan->nama }}</p>
            <div class="loader-dots mt-3"><span></span><span></span><span></span></div>
        </div>

        {{-- Fallback kalau iframe gagal / diblokir situs tujuan --}}
        <div id="portalFallback" class="portal-fallback">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 text-accent flex items-center justify-center mb-4">
                <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
            </div>
            <h4 class="text-primary font-bold text-base mb-2">Portal tidak bisa ditampilkan di sini</h4>
            <p class="text-slate-400 text-xs max-w-sm mb-6 leading-relaxed">
                Situs {{ $layanan->nama }} mungkin membatasi tampilan tertanam demi keamanan. Silakan buka portal ini langsung di tab baru.
            </p>
            <a href="{{ $layanan->iframe_url }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-bold text-xs transition">
                <i class="bi bi-box-arrow-up-right"></i> Buka {{ $layanan->nama }} di Tab Baru
            </a>
        </div>

        <iframe
            src="{{ $layanan->iframe_url }}"
            id="Iframe"
            allow="geolocation; microphone; camera"
            onload="handleIframeLoad()">
        </iframe>
    </div>

</div>

<script>
    let progressInterval;
    let loadTimeoutId;

    function startProgress() {
        const fill = document.getElementById('loadProgressFill');
        let value = 0;
        progressInterval = setInterval(() => {
            // naik cepat di awal, melambat mendekati 90% (menunggu respons asli)
            value += (90 - value) * 0.08;
            fill.style.width = Math.min(value, 90) + '%';
        }, 200);
    }

    function finishProgress() {
        clearInterval(progressInterval);
        const fill = document.getElementById('loadProgressFill');
        const track = document.getElementById('loadProgressTrack');
        fill.style.width = '100%';
        setTimeout(() => { track.style.opacity = '0'; }, 300);
    }

    function hidePortalLoader() {
        const loader = document.getElementById('portalLoader');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => { loader.style.display = 'none'; }, 500);
        }
    }

    function handleIframeLoad() {
        clearTimeout(loadTimeoutId);
        finishProgress();
        hidePortalLoader();
    }

    function showFallback() {
        hidePortalLoader();
        finishProgress();
        document.getElementById('portalFallback').classList.add('show');
    }

    startProgress();

    // Backup: kalau dalam 12 detik iframe belum juga onload (kemungkinan diblokir situs tujuan
    // via X-Frame-Options), tampilkan fallback "buka di tab baru" alih-alih layar putih kosong.
    loadTimeoutId = setTimeout(showFallback, 12000);

    // TOGGLE LAYAR PENUH: menutupi seluruh viewport termasuk nav/footer situs utama,
    // supaya iframe portal dapat ruang lebih luas. Klik lagi atau tekan Esc untuk kembali.
    function toggleFullpage() {
        const container = document.getElementById('portalContainer');
        const icon = document.getElementById('fullpageIcon');
        const label = document.getElementById('fullpageLabel');
        const isFullpage = container.classList.toggle('is-fullpage');

        icon.classList.toggle('bi-arrows-fullscreen', !isFullpage);
        icon.classList.toggle('bi-fullscreen-exit', isFullpage);
        label.textContent = isFullpage ? 'Keluar Layar Penuh' : 'Layar Penuh';

        // Cegah scroll body ganda saat mode layar penuh aktif
        document.body.style.overflow = isFullpage ? 'hidden' : '';
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const container = document.getElementById('portalContainer');
            if (container.classList.contains('is-fullpage')) {
                toggleFullpage();
            }
        }
    });
</script>
@endsection