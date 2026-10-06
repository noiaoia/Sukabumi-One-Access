@extends('layouts.app')

@section('title', $instansi->nama . ' - Sukabumi One Access')

@section('content')

<style>
    /* 1. DINAS HERO STYLING */
    .hero-dinas-premium {
        background-color: #0A1E33;
        position: relative;
        overflow: hidden;
        padding: 3.5rem 1.5rem 4.5rem;
        border-bottom: 4px solid #E8A33D;
    }

    .bg-pattern-dinas {
        position: absolute;
        inset: 0;
        opacity: 0.1;
        background-image: radial-gradient(#E8A33D 1px, transparent 0);
        background-size: 30px 30px;
    }

    #heroLogoWrap {
        will-change: transform, opacity, filter;
    }

    /* quick-info bar floating between hero and content */
    .quick-info-bar {
        background: #ffffff;
        border-radius: 1.5rem;
        box-shadow: 0 20px 45px -12px rgba(10, 30, 51, 0.18);
        margin-top: -3.5rem;
        position: relative;
        z-index: 20;
    }

    /* 2. CARD LAYANAN PREMIUM */
    .service-premium-card {
        background: #ffffff;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid #f1f5f9;
        position: relative;
        border-radius: 2rem;
    }

    .service-premium-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 30px 60px -12px rgba(18, 52, 87, 0.15);
        border-color: #E8A33D;
    }

    .service-img-wrapper {
        position: relative;
        width: 100%;
        height: 140px;
        overflow: hidden;
        border-radius: 2rem 2rem 0 0;
    }

    .service-premium-card {
        text-decoration: none !important;
        color: inherit;
    }

    .service-premium-card:active {
        transform: scale(0.98);
        transition: 0.1s;
    }

    @media (max-width: 639px) {
        .service-img-wrapper { height: 100px; border-radius: 1.5rem 1.5rem 0 0; }
    }

    .service-img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
        filter: brightness(0.9);
    }

    .service-premium-card:hover .service-img {
        transform: scale(1.1);
    }

    .service-icon-floating-wrap {
        position: relative;
        z-index: 30;
        display: flex;
        justify-content: flex-end;
        padding: 0 1.25rem;
        margin-top: -24px;
        pointer-events: none;
    }

    .service-icon-floating {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        border: 4px solid #fff;
    }

    @media (max-width: 639px) {
        .service-icon-floating {
            width: 38px; height: 38px;
            border-radius: 10px;
            border-width: 3px;
        }
        .service-icon-floating-wrap { margin-top: -19px; padding: 0 0.75rem; }
        .service-icon-floating i { font-size: 1rem !important; }
        .service-premium-card { border-radius: 1.5rem; }
    }

    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #F4F6F8;
        color: #4A545D;
        font-size: 10px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 999px;
        margin-top: 6px;
    }
</style>

{{-- 1. HERO SECTION --}}
<section class="hero-dinas-premium" id="heroDinas">
    <div class="bg-pattern-dinas"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <div class="flex flex-col items-center text-center gap-3">

            <div class="relative reveal" id="heroLogoWrap">
                <div class="absolute inset-0 m-auto w-36 h-36 sm:w-52 sm:h-52 bg-accent/25 rounded-full blur-3xl"></div>
                <img src="{{ asset('storage/' . $instansi->logo) }}" alt="{{ $instansi->nama }}"
                     class="relative w-32 h-32 sm:w-44 sm:h-44 object-contain drop-shadow-[0_15px_32px_rgba(0,0,0,0.35)]">
            </div>

            <div class="reveal">
                <nav class="flex justify-center items-center gap-2 text-accent text-[10px] font-bold uppercase tracking-[0.2em] mb-2">
                    <a href="{{ route('home') }}" class="hover:text-white transition flex items-center gap-1.5">
                        <i class="bi bi-house-door-fill"></i> Beranda
                    </a>
                    <i class="bi bi-chevron-right text-[8px]"></i>
                    <span>{{ $instansi->nama }}</span>
                </nav>
                @php
                    $words = explode(' ', $instansi->nama);
                    $lastWord = array_pop($words);
                @endphp

                <h1 class="text-2xl md:text-4xl font-extrabold text-white leading-tight tracking-tighter">
                    @php
                        $parts = explode('&', $instansi->slogan ?? '', 2);
                    @endphp

                    @if(count($parts) > 1)
                        {{ trim($parts[0]) }}
                        <span class="text-accent">
                            & {{ trim($parts[1]) }}
                        </span>
                    @else
                        {{ $instansi->slogan }}
                    @endif
                </h1>
            </div>
        </div>
    </div>
</section>

{{-- QUICK INFO BAR --}}
<div class="max-w-4xl mx-auto px-4 sm:px-8 relative">
    <div class="quick-info-bar grid grid-cols-3 divide-x divide-slate-100" id="quickInfoBar">
        <div class="px-3 sm:px-6 py-4 sm:py-5 text-center">
            <p id="statJumlahLayanan" class="text-primary-dark text-lg sm:text-2xl font-black roll-number" data-final="{{ $instansi->stat1 }}"></p>
            <p class="text-slate-400 text-[9px] sm:text-[11px] font-semibold uppercase tracking-wide mt-0.5">Jenis layanan</p>
        </div>
        <div class="px-3 sm:px-6 py-4 sm:py-5 text-center">
            <p class="text-primary-dark text-lg sm:text-2xl font-black roll-number" data-final="{{ $instansi->stat2 }}"></p>
            <p class="text-slate-400 text-[9px] sm:text-[11px] font-semibold uppercase tracking-wide mt-0.5">Jam layanan</p>
        </div>
        <div class="px-3 sm:px-6 py-4 sm:py-5 text-center">
            <p class="text-primary-dark text-lg sm:text-2xl font-black roll-number" data-final="{{ $instansi->stat3 }}"></p>
            <p class="text-slate-400 text-[9px] sm:text-[11px] font-semibold uppercase tracking-wide mt-0.5">Akses Digital</p>
        </div>
    </div>
</div>

{{-- 2. PROFIL & LOKASI --}}
<section class="pt-12 pb-12 sm:pt-16 sm:pb-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="reveal">
                <p class="text-accent font-black text-xs uppercase tracking-[0.3em] mb-4">Mengenal Instansi</p>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-primary-dark mb-6 leading-tight tracking-tight">{{ $instansi->about_title }}</h2>
                <div class="space-y-4 text-slate-500 text-sm sm:text-base leading-relaxed">
                    <p>{{ $instansi->deskripsi }}</p>
                </div>
            </div>

            <div class="reveal" style="transition-delay: 200ms">
                <div class="bg-slate-50 border border-slate-100 rounded-[2.5rem] overflow-hidden">
                    <div class="w-full h-44 sm:h-52 bg-slate-200">
                    <iframe
                        src="{{ $instansi->maps_embed }}" width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy">
                    </iframe>
                    </div>
                    <div class="p-6 sm:p-8">
                        <h4 class="text-sm font-bold text-primary mb-2 flex items-center gap-2">
                            <i class="bi bi-geo-alt-fill text-accent"></i> Lokasi Kantor Resmi
                        </h4>
                        <p class="text-xs text-slate-500 leading-relaxed mb-3">
                            {!! $instansi->alamat !!}
                        </p>
                        <a href="https://www.google.com/maps/dir//BPKPD+Kota+Sukabumi"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 text-[10px] font-black text-primary hover:text-accent transition-colors tracking-widest bg-white px-5 py-2.5 rounded-xl shadow-sm border border-slate-100">
                            PETUNJUK ARAH <i class="bi bi-arrow-up-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 3. GRID LAYANAN --}}
<section class="py-16 sm:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="text-center mb-16 reveal">
            <p class="text-accent text-[10px] font-black uppercase tracking-[0.3em] mb-2">Akses Portal</p>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-primary-dark tracking-tight">{{ $instansi->nama }}</h2>
        </div>

        <!-- PERUBAHAN DISINI: Menggunakan flex dan justify-center -->
        <div class=" grid grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-8 max-w-7xl mx-auto">
            @forelse($layanans as $layanan)
                <div class="reveal @if($layanans->count() == 1) col-span-2 lg:col-span-1 lg:col-start-2 @endif">
                    <a href="{{ $layanan->jenis === 'form' ? route('layanan.form.show', $layanan->slug) : route('portal.show', $layanan->slug) }}" class="service-premium-card flex flex-col h-full group">
                        <div class="service-img-wrapper">
                            @if($layanan->cover)
                                <img src="{{ asset('storage/' . $layanan->cover) }}" class="service-img" alt="{{ $layanan->nama }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center" style="background: {{ $layanan->primary_color ?? '#123457' }};">
                                    <i class="bi {{ $layanan->icon ?? 'bi-grid-1x2-fill' }}" style="color: {{ $layanan->secondary_color ?? '#E8A33D' }}; font-size:4rem;"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/60 to-transparent"></div>
                        </div>

                        <div class="service-icon-floating-wrap">
                            <div class="service-icon-floating" style="background: {{ $layanan->primary_color ?? '#123457' }};">
                                <i class="bi {{ $layanan->icon ?? 'bi-grid-1x2-fill' }} text-xl" style="color: {{ $layanan->secondary_color ?? '#E8A33D' }};"></i>
                            </div>
                        </div>

                        <div class="p-4 sm:p-8 pt-3 sm:pt-4 flex flex-col flex-1 text-left">
                            <h3 class="text-sm sm:text-lg font-black text-primary-dark mb-1 leading-tight group-hover:text-accent transition-colors">{{ $layanan->nama }}</h3>
                            <p class="text-[9px] sm:text-xs text-slate-400 font-medium">{{ $layanan->deskripsi }}</p>

                            <span class="time-badge w-fit">
                                @if($layanan->jenis === 'form')
                                    <i class="bi bi-file-earmark-text-fill"></i> Layanan Mandiri
                                @else
                                    <i class="bi bi-box-arrow-up-right"></i> Portal Eksternal
                                @endif
                            </span>

                            <span class="mt-4 sm:mt-5 inline-flex items-center gap-2 text-[9px] sm:text-xs font-black text-primary group-hover:text-accent transition-all uppercase tracking-widest">
                                @if($layanan->jenis === 'form')
                                    Ajukan Layanan
                                @else
                                    Buka Layanan
                                @endif
                                <i class="bi bi-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                            </span>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-full flex justify-center">
                    <div class="bg-white border border-slate-100 rounded-[2rem] p-8 text-center max-w-sm w-full shadow-sm reveal">
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-50 shadow-inner">
                            <i class="bi bi-inbox text-slate-300 text-2xl"></i>
                        </div>

                        <h4 class="text-xs font-black text-primary-dark uppercase tracking-widest mb-1">Belum Ada Layanan</h4>

                        <p class="text-[11px] text-slate-400 font-bold leading-relaxed px-4">Modul layanan untuk instansi ini sedang dalam tahap integrasi.</p>

                        <a href="{{ route('home') }}" class="mt-4 inline-block text-[10px] font-black text-accent hover:text-primary transition-colors uppercase tracking-tighter">
                            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<script>
    // 1. LOGO ANIMATION (Lerp Smoothing) — identik dengan Disdukcapil
    (function () {
        const hero = document.getElementById('heroDinas');
        const logoWrap = document.getElementById('heroLogoWrap');
        if (!hero || !logoWrap) return;

        let targetProgress = 0;
        let currentProgress = 0;

        function easeOutCubic(t) { return 1 - Math.pow(1 - t, 3); }

        function updateTarget() {
            const heroHeight = hero.offsetHeight;
            const scrollY = window.scrollY;
            targetProgress = Math.min(Math.max(scrollY / heroHeight, 0), 1);
        }

        function loop() {
            currentProgress += (targetProgress - currentProgress) * 0.1;
            const eased = easeOutCubic(currentProgress);
            logoWrap.style.opacity = String(1 - eased);
            logoWrap.style.transform = `translateY(${eased * 40}px) scale(${1 - eased * 0.15})`;
            logoWrap.style.filter = `blur(${eased * 10}px)`;
            requestAnimationFrame(loop);
        }

        window.addEventListener('scroll', updateTarget, { passive: true });
        updateTarget();
        requestAnimationFrame(loop);
    })();

    // 2. ROLLING NUMBER ANIMATION — sekarang disconnect() setelah main sekali, sama seperti Disdukcapil
    function rollNumber(el, finalText, totalFrames = 18, frameDuration = 55) {
        finalText = String(finalText ?? '').trim();
        if (finalText === '') {
            el.textContent = '';
            return;
        }
        const chars = finalText.split('');
        let frame = 0;
        const timer = setInterval(() => {
            frame++;
            const display = chars.map((char, i) => {
                if (!/[0-9]/.test(char)) return char;
                const lockAt = Math.round(((i + 1) / chars.length) * totalFrames);
                if (frame >= lockAt) return char;
                return Math.floor(Math.random() * 10);
            }).join('');
            el.textContent = display;
            if (frame >= totalFrames) { el.textContent = finalText; clearInterval(timer); }
        }, frameDuration);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const bar = document.getElementById('quickInfoBar');
        if (!bar) return;

        let played = false;
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !played) {
                    played = true;
                    document.querySelectorAll('.roll-number').forEach(el => {
                        rollNumber(el, el.dataset.final);
                    });
                    observer.disconnect();
                }
            });
        }, { threshold: 0.4 });
        observer.observe(bar);
    });

    function updateJumlahLayanan() {
        const url = "{{ route('instansi.layanan-count', $instansi->id) }}";

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengambil jumlah layanan');
                }

                return response.json();
            })
            .then(data => {
                const jumlah = `${data.count}`;
                const element = document.getElementById('statJumlahLayanan');

                if (element) {
                    element.dataset.final = jumlah;
                    element.textContent = jumlah;
                }
            })
            .catch(error => {
                console.error('Gagal memperbarui jumlah layanan:', error);
            });
    }

    updateJumlahLayanan();
    setInterval(updateJumlahLayanan, 3000);
</script>

@endsection