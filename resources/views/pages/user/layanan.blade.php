@extends('layouts.app')

@section('title', 'Direktori Layanan - Sukabumi One Access')

@section('content')

<style>
    /* 1. HERO LAYANAN STYLING */
    .layanan-hero {
        background-color: #0A1E33;
        position: relative;
        overflow: hidden;
        padding: 4rem 1.5rem 6rem;
        border-bottom: 4px solid #E8A33D;
    }

    .bg-dot-matrix {
        position: absolute;
        inset: 0;
        opacity: 0.12;
        background-image: radial-gradient(#E8A33D 1px, transparent 0);
        background-size: 30px 30px;
    }

    /* 2. SEARCH BOX PREMIUM */
    .search-container {
        max-width: 600px;
        margin: -2.5rem auto 0;
        position: relative;
        z-index: 40;
    }

    .search-wrapper {
        background: #ffffff;
        border-radius: 1.5rem;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        box-shadow: 0 25px 50px -12px rgba(10, 30, 51, 0.2);
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }

    .search-wrapper:focus-within {
        transform: translateY(-5px);
        border-color: #E8A33D;
    }

    .search-input {
        flex: 1;
        border: none;
        outline: none;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: #123457;
    }

    /* 3. DINAS CARD PREMIUM (REFINED) */
    .dinas-card-premium {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 2rem;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative; /* Badge mengacu ke sini */
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .dinas-card-premium:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -15px rgba(10, 30, 51, 0.1);
        border-color: #E8A33D;
    }

    /* Wrapper Image dengan Overflow Hidden */
    .img-frame-premium {
        height: 120px;
        overflow: hidden;
        border-radius: 2rem 2rem 0 0;
        position: relative;
        z-index: 1;
    }

    /* Efek Mengkilap */
    .img-frame-premium::after {
        content: "";
        position: absolute;
        top: 0; left: -100%;
        width: 50%;
        height: 100%;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.3), transparent);
        transform: skewX(-25deg);
        z-index: 2;
    }

    /* Jalankan kilatan saat kartu di-hover */
    .dinas-card-premium:hover .img-frame-premium::after {
        animation: shimmer 0.7s forwards;
    }

    .dinas-img-premium {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.85);
        transition: transform 0.6s ease;
    }

    .dinas-card-premium:hover .dinas-img-premium {
        transform: scale(1.1);
        filter: brightness(1);
    }

    /* FIX: ICON BADGE MELAYANG (Tidak Kelelep) */
    .icon-badge-floating {
        position: absolute;
        z-index: 30; /* Lapisan di atas teks dan gambar */
        top: 100px; /* Posisi nangkring di garis potong (Desktop) */
        right: 20px;
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border: 3px solid #ffffff;
        transition: all 0.3s ease;
    }

    .dinas-card-premium:hover .icon-badge-floating {
        transform: scale(1.15); /* Hanya Zoom Saja */
        box-shadow: 0 15px 30px rgba(18, 52, 87, 0.15);
        border-color: #ffffff;
    }

    /* Animasi Kilatan Cahaya */
    @keyframes shimmer {
        100% { left: 125%; }
    }

    /* RESPONSIVE MOBILE */
    @media (max-width: 639px) {
        .img-frame-premium { height: 90px; border-radius: 1.5rem 1.5rem 0 0; }
        .icon-badge-floating { 
            width: 36px; 
            height: 36px; 
            top: 72px; /* Menyesuaikan tinggi gambar mobile 90px */
            right: 12px; 
            border-radius: 10px; 
        }
        .icon-badge-floating i { font-size: 1rem !important; }
        .dinas-card-premium { border-radius: 1.5rem; }
    }
</style>

{{-- Jembatan Data Login --}}
<div id="auth-check" data-status="{{ Auth::check() ? 'true' : 'false' }}" class="hidden"></div>

{{-- 1. HERO SECTION --}}
<section class="layanan-hero">
    <div class="bg-dot-matrix"></div>
    <div class="max-w-7xl mx-auto relative z-10 text-center">
        <div class="reveal">
            <nav class="flex justify-center items-center gap-2 text-accent text-[10px] font-bold uppercase tracking-[0.2em] mb-4">
                <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                <i class="bi bi-chevron-right text-[8px]"></i>
                <span>Direktori Layanan</span>
            </nav>
            <h1 class="text-3xl md:text-5xl font-extrabold text-white leading-tight tracking-tighter">
                Eksplorasi <span class="text-accent italic">Layanan Kota.</span>
            </h1>
            <p class="text-slate-400 mt-4 max-w-xl mx-auto text-sm sm:text-base leading-relaxed">
                Temukan kemudahan akses administrasi dan informasi dari seluruh instansi Pemerintah Kota Sukabumi dalam satu pintu.
            </p>
        </div>
    </div>
</section>

{{-- 2. SEARCH BOX --}}
<div class="search-container px-4">
    <div class="search-wrapper">
        <i class="bi bi-search text-slate-400 ml-4"></i>
        <input type="text" id="searchInput" class="search-input" placeholder="Cari instansi atau kategori layanan...">
        <div class="hidden sm:block px-4 py-2 bg-slate-50 text-[10px] font-bold text-slate-400 rounded-xl mr-2">CARI</div>
    </div>
</div>

{{-- 3. GRID INSTANSI --}}
<section class="py-16 sm:py-24 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        
        <div id="agencyGrid" class="grid grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-10">
            @foreach($instansis as $d)
                <div class="reveal agency-item" data-name="{{ strtolower($d->nama) }}" data-category="{{ strtolower($d->kategori) }}">
                    
                    @php
                        $targetUrl = route('instansi.show', $d->slug);
                        $isExternal = false;
                    @endphp

                    {{-- LOGIKA AUTH: Jika Login pakai <a>, jika tidak pakai <button> --}}
                    @if(Auth::check())
                        <a href="{{ $targetUrl }}" 
                        {{ $isExternal ? 'target="_blank" rel="noopener noreferrer"' : '' }} 
                        class="dinas-card-premium group block text-decoration-none text-left h-full">
                    @else
                        <button onclick="openAuthAlertModal()" 
                                class="dinas-card-premium group block text-left w-full h-full bg-white">
                    @endif
                        
                        {{-- Image Frame --}}
                        <div class="img-frame-premium">
                            <img src="{{ asset('storage/' . $d->cover) }}" class="dinas-img-premium" alt="{{ $d->nama }}">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        </div>

                        {{-- Icon Badge --}}
                        <div class="icon-badge-floating" style="background: {{ $d->primary_color }}">
                            <i class="bi {{ $d->icon }} text-lg sm:text-xl" style="color: {{ $d->secondary_color }}"></i>
                        </div>

                        {{-- Content --}}
                        <div class="p-4 sm:p-7 pt-5 sm:pt-8">
                            <h3 class="text-sm sm:text-lg font-black text-primary-dark group-hover:text-primary transition-colors leading-tight truncate">
                                {{ $d->nama }}
                            </h3>
                            <p class="text-[9px] sm:text-[11px] font-bold text-accent uppercase tracking-widest mt-1">
                                {{ $d->kategori }}
                            </p>
                            
                            <div class="mt-4 flex items-center gap-2 text-[9px] sm:text-[10px] font-black text-primary border-b-2 border-transparent group-hover:border-accent w-fit transition-all">
                                {{ $isExternal ? 'BUKA PORTAL' : 'BUKA LAYANAN' }} <i class="bi bi-arrow-right"></i>
                            </div>
                        </div>

                    @if(Auth::check())
                        </a>
                    @else
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- No Result Message --}}
        <div id="noResult" class="hidden text-center py-20 reveal">
            <div class="w-20 h-20 bg-white border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i class="bi bi-search text-slate-300 text-3xl"></i>
            </div>
            <h4 class="text-slate-800 font-bold text-lg">Instansi tidak ditemukan</h4>
            <p class="text-slate-400 text-sm">Coba masukkan kata kunci dinas yang lain.</p>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
    // --- LIVE SEARCH LOGIC ---
    const searchInput = document.getElementById('searchInput');
    const agencyItems = document.querySelectorAll('.agency-item');
    const noResult = document.getElementById('noResult');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        let hasMatch = false;

        agencyItems.forEach(item => {
            const name = item.getAttribute('data-name');
            const category = item.getAttribute('data-category');

            if (name.includes(query) || category.includes(query)) {
                item.style.display = 'block';
                hasMatch = true;
            } else {
                item.style.display = 'none';
            }
        });

        if (hasMatch) {
            noResult.classList.add('hidden');
        } else {
            noResult.classList.remove('hidden');
        }
    });
</script>
@endpush