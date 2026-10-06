@extends('layouts.app')

@section('title', 'Portal Layanan Terpadu')

@section('content')

<style>
    /* 1. CSS CUSTOM - ELEGAN & TENANG */
    @keyframes auraPulse {
        0%, 100% { opacity: 0.5; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
    }

    .hero-section {
        background-color: #0A1E33;
        position: relative;
        overflow: hidden;
        padding-top: 5rem;
        padding-bottom: 8rem;
        display: flex;
        align-items: center;
        border-bottom: 4px solid #E8A33D;
    }

    .bg-dot-matrix {
        position: absolute;
        inset: 0;
        z-index: 1;
        opacity: 0.12;
        background-image: radial-gradient(#E8A33D 1px, transparent 0);
        background-size: 32px 32px;
    }

    .bg-grid-overlay {
        position: absolute;
        inset: 0;
        z-index: 1;
        opacity: 0.03;
        background-image: linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px),
                          linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px);
        background-size: 50px 50px;
    }

    .spotlight {
        position: absolute;
        z-index: 0;
        border-radius: 9999px;
        filter: blur(120px);
        pointer-events: none;
    }

    .spotlight-blue {
        top: -10%; right: -5%; width: 600px; height: 600px;
        background: rgba(59, 130, 246, 0.08);
    }

    .spotlight-gold {
        bottom: -15%; left: -5%; width: 500px; height: 500px;
        background: rgba(232, 163, 61, 0.06);
        animation: auraPulse 8s ease-in-out infinite;
    }

    /* Layer Backgorund Image */
    .layered-frame-container {
        position: relative;
        width: 15rem;
        height: 15rem;
        z-index: 10;
    }

    @media (min-width: 640px) {
        .layered-frame-container {
            width: 20rem;
            height: 20rem;
        }
    }

    /* Layer 1: Navy (Belakang) */
    .frame-layer-1 {
        position: absolute; inset: 0;
        background: #123457;
        border-radius: 3rem;
        transform: rotate(8deg);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    /* Layer 2: Gold Soft (Tengah) */
    .frame-layer-2 {
        position: absolute; inset: 0;
        background: #E8A33D;
        border-radius: 3rem;
        transform: rotate(-4deg);
        opacity: 0.2;
    }

    /* Layer 3: Konten Utama (Depan) */
    .frame-layer-main {
        position: absolute; inset: 0;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 3rem;
        border: 2px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }

    /* TYPEWRITER */
    .cursor { border-right: 4px solid #E8A33D; margin-left: 4px; }
    .text-shadow-glow { text-shadow: 0 0 20px rgba(232, 163, 61, 0.2); }

    /* 2. DINAS CARD STYLING (COMPACT & PREMIUM) */
    .dinas-card-img-wrapper {
        position: relative;
        width: 100%;
        height: 100px; /* Lebih pendek untuk mobile (Default) */
        overflow: hidden;
        border-radius: 1.5rem 1.5rem 0 0;
    }

    .category-chip{
        transition: all .25s ease;
    }

    .category-chip.active{
        background: #123457;
        color: #fff;
        box-shadow: 0 10px 25px rgba(18,52,87,.25);
    }

    .category-chip.active:hover{
        background: #123457;
        color:#fff;
    }

    /* Ukuran desktop tetap 120px */
    @media (min-width: 768px) {
        .dinas-card-img-wrapper { height: 120px; }
    }

    .dinas-card-img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
        filter: brightness(0.9) contrast(1.1);
    }

    .service-card:hover .dinas-card-img {
        transform: scale(1.08);
    }

    /* Ikon Badge Lebih Kecil & Minimalis */
    .dinas-icon-badge {
        position: absolute;
        top: 80px; /* Posisi di mobile agar pas di garis potong */
        right: 12px;
        z-index: 30;
        width: 38px; /* Lebih kecil di mobile */
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        border: 3px solid #fff;
        transition: all 0.3s ease;
    }

    /* Posisi & ukuran desktop */
    @media (min-width: 768px) {
        .dinas-icon-badge {
            top: 100px; 
            right: 20px;
            width: 45px;
            height: 45px;
        }
    }

    /* Efek saat card di hover, badge ikut bereaksi */
    .service-card:hover .dinas-icon-badge {
        transform: scale(1.1);
        border-color: #fff;
    }

    .service-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #fff;
    }

    .service-card:hover {
        transform: translateY(-5px);
        border-color: #E8A33D;
    }
</style>

{{-- Jembatan Data Login --}}
<div id="auth-check" data-status="{{ Auth::check() ? 'true' : 'false' }}" class="hidden"></div>

{{-- HERO SECTION --}}
<section class="hero-section">
    <div class="bg-dot-matrix"></div>
    <div class="bg-grid-overlay"></div>
    <div class="spotlight spotlight-blue"></div>
    <div class="spotlight spotlight-gold"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-8 relative z-10 w-full">
        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-5">
                <div class="max-w-xl">
                    <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 backdrop-blur-sm rounded-lg px-3 py-1 mb-4">
                        <i class="bi bi-stars text-accent text-[10px]"></i>
                        <span class="text-white/70 text-[10px] font-bold uppercase tracking-wider">Diskominfo Kota Sukabumi</span>
                    </div>

                    <h1 class="text-3xl md:text-5xl font-extrabold text-white leading-tight tracking-tight min-h-[90px] md:min-h-[110px]">
                        Satu akses, <br>
                        <span id="typewriter" class="text-accent text-shadow-glow"></span>
                        <span class="cursor ml-1 animate-pulse"></span>
                    </h1>

                    <p class="text-slate-400 text-sm md:text-base leading-relaxed mt-4">
                        Integrasi layanan Kota Sukabumi dalam satu genggaman. Cepat, transparan, dan dapat diakses kapan saja.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 pt-6">
                        {{-- 1. Tombol Pilih Layanan (Tetap) --}}
                        <a href="#sektor-kedinasan" class="inline-flex items-center justify-center bg-accent hover:bg-[#d49232] text-primary-dark px-8 py-3 rounded-xl font-bold transition-all shadow-lg active:scale-95 group text-sm no-underline">
                            PILIH LAYANAN <i class="bi bi-arrow-right-short text-xl ml-1 group-hover:translate-x-1 transition-transform"></i>
                        </a>

                        {{-- 2. Tombol Tinjau Status (Logika Baru) --}}
                        @if(Auth::check())
                            {{-- Jika sudah login, panggil modal pilihan (Pengaduan/Pengajuan) --}}
                            <button onclick="openTrackingChoiceModal()" class="inline-flex items-center justify-center bg-white/5 hover:bg-white/10 border border-white/10 text-white px-8 py-3 rounded-xl font-bold transition-all active:scale-95 backdrop-blur-sm text-sm border-0">
                                <i class="bi bi-search mr-2 text-accent"></i> Tinjau Status
                            </button>
                        @else
                            {{-- Jika belum login, panggil modal peringatan login --}}
                            <button onclick="openAuthAlertModal()" class="inline-flex items-center justify-center bg-white/5 hover:bg-white/10 border border-white/10 text-white px-8 py-3 rounded-xl font-bold transition-all active:scale-95 backdrop-blur-sm text-sm border-0">
                                <i class="bi bi-search mr-2 text-accent"></i> Tinjau Status
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 flex justify-center lg:justify-end reveal">
                <div class="layered-frame-container">
                    {{-- Tumpukan Bingkai --}}
                    <div class="frame-layer-1"></div>
                    <div class="frame-layer-2"></div>
                    <div class="frame-layer-main">
                        {{-- Ganti Ikon dengan Foto --}}
                        <img src="{{ asset('image/walikota.webp') }}" alt="Sukabumi One Access" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/10"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- MAIN CONTENT --}}
<main class="relative">
    {{-- Search & Filter --}}
    <div class="max-w-3xl mx-auto px-4 sm:px-8 -mt-14 relative z-20">
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-2xl p-4 sm:p-5">
            <div class="flex items-center gap-3 bg-slate-50 rounded-2xl px-4 py-3 mb-3 border border-slate-100">
                <i class="bi bi-search text-slate-400"></i>
                <input type="text" id="mainSearchInput" placeholder='Cari layanan, misal "KTP" atau "izin usaha"' class="w-full bg-transparent text-sm text-slate-700 placeholder-slate-400 focus:outline-none">
            </div>
            <div class="flex flex-wrap gap-2" id="categoryFilters">
                <button class="category-chip bg-primary text-white text-xs font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-primary/20" data-filter="semua">Semua</button>
                <button class="category-chip bg-slate-50 text-slate-500 text-xs font-semibold px-5 py-2.5 rounded-xl hover:bg-slate-100" data-filter="kependudukan">Kependudukan</button>
                <button class="category-chip bg-slate-50 text-slate-500 text-xs font-semibold px-5 py-2.5 rounded-xl hover:bg-slate-100" data-filter="perizinan">Perizinan</button>
                <button class="category-chip bg-slate-50 text-slate-500 text-xs font-semibold px-5 py-2.5 rounded-xl hover:bg-slate-100" data-filter="keuangan">Keuangan</button>
                <button class="category-chip bg-slate-50 text-slate-500 text-xs font-semibold px-5 py-2.5 rounded-xl hover:bg-slate-100" data-filter="pangan">Pangan</button>
            </div>
        </div>
    </div>

    {{-- Sektor Kedinasan --}}
    <section id="sektor-kedinasan" class="max-w-7xl mx-auto px-4 sm:px-8 pt-16 pb-24 scroll-mt-24">
            <div class="text-center mb-10">
                <p class="text-slate-400 text-xs font-semibold uppercase tracking-widest mb-1">Sektor kedinasan</p>
                <h2 class="text-xl sm:text-2xl font-extrabold text-primary">
                    {{ $instansis->count() }} instansi terhubung, terus bertambah
                </h2>
            </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
            @foreach($instansis->take(5) as $d)
                <div class="reveal" data-name="{{ strtolower($d->nama) }}">
                    @php
                        $targetUrl = route('instansi.show', $d->slug);
                        $isExternal = false;
                    @endphp

                    @if(Auth::check())
                        <a href="{{ $targetUrl }}" 
                        {{ $isExternal ? 'target="_blank" rel="noopener noreferrer"' : '' }} 
                        class="service-card group relative border border-slate-100 rounded-[2rem] shadow-sm block transition-all hover:shadow-2xl" 
                        data-category="{{ strtolower($d->kategori) }}">
                    @else
                        <button onclick="openAuthAlertModal()" class="service-card group relative w-full text-left border border-slate-100 rounded-[2rem] shadow-sm block transition-all hover:shadow-2xl" data-category="{{ strtolower($d->kategori) }}">
                    @endif
                        
                        {{-- 1. Area Foto (Tetap pakai overflow hidden) --}}
                        <div class="dinas-card-img-wrapper">
                            <img src="{{ asset('storage/' . $d->cover) }}" class="dinas-card-img" alt="{{ $d->nama }}">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div>
                        </div>

                        {{-- 2. Icon Badge --}}
                        <div class="dinas-icon-badge" style="background: {{ $d->primary_color }};">
                            <i class="bi {{ $d->icon }} text-xl" style="color: {{ $d->secondary_color }}"></i>
                        </div>

                        {{-- 3. Area Teks --}}
                        <div class="p-5 pt-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-base font-bold text-slate-800 group-hover:text-primary transition-colors leading-none">{{ $d->nama }}</h3>
                                    <p class="text-[10px] font-bold text-accent uppercase tracking-widest mt-1.5">{{ $d->kategori }}</p>
                                </div>
                                <i class="bi bi-arrow-up-right text-slate-300 group-hover:text-primary transition-all"></i>
                            </div>
                        </div>

                    @if(Auth::check()) </a> @else </button> @endif
                </div>
            @endforeach

            {{-- Compact "Semua Layanan" Card --}}
            <div class="reveal">
                <a href="{{ route('layanan') ?? '#' }}" 
                class="h-full min-h-[170px] rounded-[2rem] p-8 flex flex-col items-center justify-center relative overflow-hidden transition-all duration-500 group shadow-lg border border-accent/20"
                style="background: linear-gradient(135deg, #123457 0%, #0A1E33 100%);">
                    
                    {{-- Efek Cahaya di Background --}}
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-accent/10 rounded-full blur-2xl group-hover:bg-accent/20 transition-all duration-500"></div>
                    
                    {{-- Ikon dengan Glow --}}
                    <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-4 shadow-inner group-hover:scale-110 group-hover:border-accent/50 transition-all duration-500">
                        <i class="bi bi-grid-3x3-gap-fill text-accent text-2xl filter drop-shadow-[0_0_8px_rgba(232,163,61,0.5)]"></i>
                    </div>
                    
                    {{-- Teks --}}
                    <div class="text-center">
                        <p class="text-[10px] font-black text-accent uppercase tracking-[0.3em] mb-1">Eksplorasi</p>
                        <h3 class="text-white font-extrabold text-sm uppercase tracking-wider group-hover:text-accent transition-colors">Semua Layanan</h3>
                    </div>

                    {{-- Panah Kecil yang muncul saat hover --}}
                    <i class="bi bi-arrow-right absolute bottom-6 text-accent opacity-0 group-hover:opacity-100 transition-all transform -translate-x-4 group-hover:translate-x-0"></i>
                </a>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const authStatus = document.getElementById('auth-check').getAttribute('data-status') === 'true';

        // --- 1. LOGIC LACAK BERKAS ---
        const btnLacak = document.getElementById('btnLacak');

        if(btnLacak) {
            btnLacak.addEventListener('click', (e) => {
                e.preventDefault(); // Mencegah scroll meloncat
                
                // Ambil status login dari jembatan data yang sudah Anda buat
                const authStatus = document.getElementById('auth-check').getAttribute('data-status') === 'true';
                
                if (authStatus) {
                    // JIKA LOGIN: Panggil fungsi dari component trackingChoiceModal
                    if (typeof openTrackingChoiceModal === "function") {
                        openTrackingChoiceModal();
                    } else {
                        console.error("Fungsi openTrackingChoiceModal tidak ditemukan.");
                    }
                } else {
                    // JIKA BELUM LOGIN: Buka modal peringatan login
                    if (typeof openAuthAlertModal === "function") {
                        openAuthAlertModal();
                    }
                }
            });
        }

        // --- 2. TYPEWRITER EFFECT ---
        const typewriterElement = document.getElementById('typewriter');
        const words = ["semua layanan kota.", "urusan jadi mudah.", "birokrasi lebih cepat.", "Sukabumi One Access."];
        let wordIndex = 0, charIndex = 0, isDeleting = false, typeSpeed = 100;

        function type() {
            const currentWord = words[wordIndex];
            typewriterElement.textContent = isDeleting 
                ? currentWord.substring(0, charIndex - 1) 
                : currentWord.substring(0, charIndex + 1);
            charIndex = isDeleting ? charIndex - 1 : charIndex + 1;
            typeSpeed = isDeleting ? 50 : 100;

            if (!isDeleting && charIndex === currentWord.length) {
                isDeleting = true; typeSpeed = 2000;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false; wordIndex = (wordIndex + 1) % words.length; typeSpeed = 500;
            }
            setTimeout(type, typeSpeed);
        }
        type();

        // --- 3. LIVE SEARCH LOGIC (FITUR BARU) ---
        const mainSearch = document.getElementById('mainSearchInput');
        const allCards = document.querySelectorAll('.service-card');

        mainSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            
            allCards.forEach(card => {
                const parent = card.closest('.reveal');
                const name = parent.getAttribute('data-name') || '';
                const category = card.getAttribute('data-category') || '';

                // Jika nama atau kategori cocok dengan ketikan
                if (name.includes(query) || category.includes(query)) {
                    parent.style.display = 'block';
                } else {
                    parent.style.display = 'none';
                }
            });

            // Khusus untuk card "Semua Layanan", kita sembunyikan jika sedang mencari
            const allServicesCard = document.querySelector('a[href*="layanan"]')?.closest('.reveal');
            if (allServicesCard) {
                allServicesCard.style.display = query.length > 0 ? 'none' : 'block';
            }
        });

        // --- 4. CATEGORY FILTER LOGIC ---
        document.querySelectorAll('.category-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                document.querySelectorAll('.category-chip').forEach(c => {
                    c.className = "category-chip bg-slate-50 text-slate-500 text-xs font-semibold px-5 py-2.5 rounded-xl hover:bg-slate-100";
                });

                chip.className = "category-chip bg-primary text-white text-xs font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-primary/20";

                const filter = chip.dataset.filter;
                allCards.forEach(card => {
                    const parent = card.closest('.reveal');
                    if (filter === 'semua' || card.dataset.category === filter) {
                        parent.style.display = 'block';
                    } else {
                        parent.style.display = 'none';
                    }
                });
                // Kosongkan search box saat filter kategori diklik agar tidak bingung
                mainSearch.value = "";
            });
        });
    });
</script>
@endpush