@extends('layouts.app')

@section('title', 'Tentang Kami - Sukabumi One Access')

@section('content')

<style>
    /* 1. HERO ABOUT STYLING */
    .about-hero {
        background-color: #0A1E33;
        position: relative;
        overflow: hidden;
        padding: 5rem 1.5rem 8rem;
        border-bottom: 4px solid #E8A33D;
    }

    @keyframes floatPattern {
        0% { background-position: 0 0; }
        100% { background-position: 30px 30px; }
    }

    .bg-dot-matrix {
        position: absolute;
        inset: 0;
        opacity: 0.12;
        background-image: radial-gradient(#E8A33D 1px, transparent 0);
        background-size: 30px 30px;
        animation: floatPattern 10s linear infinite; /* Pattern bergerak pelan */
    }

    /* 2. VISION CARD STYLING — Mengadaptasi gaya premium E-KTP */
    .vision-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 2rem;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        padding: 2.5rem;
        height: 100%;
        position: relative;
        overflow: hidden;
        isolation: isolate; /* Agar elemen di dalam tidak keluar jalur */
    }

    .vision-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 24px 48px rgba(10, 30, 51, 0.1);
        border-color: #E8A33D;
    }

    /* Garis aksen kiri (Side Indicator) */
    .side-indicator {
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 0;
        background: #E8A33D;
        transition: height 0.4s ease;
        z-index: 3;
    }

    .vision-card:hover .side-indicator {
        height: 100%;
    }

    /* Nomor urut raksasa samar (Ghost Number) */
    .option-ghost-number {
        position: absolute;
        top: -0.5rem;
        right: 0.5rem;
        font-size: 6rem;
        font-weight: 900;
        line-height: 1;
        color: #0F2A4A;
        opacity: 0.04;
        z-index: 0;
        pointer-events: none;
        transition: opacity 0.4s ease;
    }

    .vision-card:hover .option-ghost-number {
        opacity: 0.07;
    }

    /* Efek pendaran (Blob Glow) */
    .vision-card::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -30%;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(232,163,61,0.15) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.5s ease;
        z-index: 0;
        pointer-events: none;
    }

    .vision-card:hover::before {
        opacity: 1;
    }

    /* Box Icon */
    .icon-box-about {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: #f8fafc;
        color: #123457;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 2;
        transition: all 0.4s ease;
    }

    .vision-card:hover .icon-box-about {
        background: #123457;
        color: #E8A33D;
        box-shadow: 0 10px 24px rgba(18, 52, 87, 0.2);
    }

    .vision-card h3, .vision-card p {
        position: relative;
        z-index: 2;
    }

    /* 3. LOGO DECORATION — wrapper mengatur posisi/parallax, ukuran img diatur terpisah
       (sebelumnya CSS ini menyasar properti font-icon padahal elemennya sudah <img>,
       jadi ukurannya tidak pernah benar-benar ke-set) */
    .huge-logo-bg {
        position: absolute;
        right: -5%;
        top: 15%;
        opacity: 0.03;
        pointer-events: none;
        z-index: 0;
        will-change: transform;
    }

    .huge-logo-bg img {
        display: block;
        width: 620px;
        max-width: none;
        height: auto;
    }

    /* 4. CTA PANEL — pengganti banner navy polos */
    .cta-panel {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 30px 60px -20px rgba(10, 30, 51, 0.12);
    }

    .cta-panel-top {
        height: 4px;
        background: linear-gradient(90deg, #123457 0%, #E8A33D 100%);
    }

    .cta-grid {
        display: grid;
        grid-template-columns: 1fr;
    }

    @media (min-width: 1024px) {
        .cta-grid {
            grid-template-columns: 1.3fr 1fr;
        }
    }

    /* Kolom kiri: pesan utama */
    .cta-copy {
        padding: 3rem 2rem;
        position: relative;
    }

    @media (min-width: 640px) {
        .cta-copy { padding: 4rem 3.5rem; }
    }

    .cta-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #E8A33D;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.3em;
        margin-bottom: 1.25rem;
    }

    .cta-eyebrow::before {
        content: '';
        width: 18px;
        height: 2px;
        background: #E8A33D;
        display: inline-block;
    }

    .cta-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 2.5rem;
    }

    .cta-btn-primary {
        background: #123457;
        color: #fff;
        padding: 1rem 1.75rem;
        border-radius: 1rem;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        transition: all 0.3s ease;
    }

    .cta-btn-primary:hover {
        background: #E8A33D;
        color: #123457;
        transform: translateY(-3px);
    }

    .cta-btn-ghost {
        background: transparent;
        color: #123457;
        border: 1.5px solid #e2e8f0;
        padding: 1rem 1.75rem;
        border-radius: 1rem;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        transition: all 0.3s ease;
    }

    .cta-btn-ghost:hover {
        border-color: #123457;
        background: #f8fafc;
    }

    /* Kolom kanan: identitas Diskominfo, bukan lagi logo raksasa mengambang */
    .cta-identity {
        background: #0A1E33;
        position: relative;
        padding: 3rem 2rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
    }

    .cta-identity .bg-dot-matrix { opacity: 0.15; }

    .cta-identity::before {
        content: '';
        position: absolute;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(232,163,61,0.18) 0%, transparent 70%);
        top: -20%;
        right: -15%;
        pointer-events: none;
    }

    /* Brand mark — bahasa visual yang sama dengan navbar, bukan file logo */
    .cta-brand-mark {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(232,163,61,0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
        backdrop-filter: blur(4px);
    }

    .cta-brand-mark i {
        color: #E8A33D;
        font-size: 1.5rem;
    }

    .cta-wordmark {
        margin-top: 1.25rem;
        position: relative;
        z-index: 2;
    }

    .cta-wordmark .brand-name {
        display: block;
        color: #fff;
        font-weight: 800;
        font-size: 1.5rem;
        letter-spacing: -0.02em;
        line-height: 1;
    }

    .cta-wordmark .brand-sub {
        display: block;
        color: #E8A33D;
        font-weight: 700;
        font-size: 10px;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        margin-top: 0.5rem;
    }

    /* Signature element: "titik akses" — merepresentasikan konsep One Access */
    .cta-access-line {
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 1.75rem 0 1.5rem;
        position: relative;
        z-index: 2;
    }

    .cta-access-line .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #2c4a6e;
    }

    .cta-access-line .dot.active {
        background: #E8A33D;
        width: 22px;
        border-radius: 999px;
    }

    .cta-identity h4 {
        position: relative;
        z-index: 2;
    }

    .cta-identity p {
        position: relative;
        z-index: 2;
    }
</style>

{{-- 1. HERO SECTION --}}
<section class="about-hero">
    <div class="bg-dot-matrix"></div>
    <div class="huge-logo-bg">
        <img src="{{ asset('image/maskot.webp') }}" alt="Logo SOA">
    </div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="text-center reveal">
            <nav class="flex justify-center items-center gap-2 text-accent text-[10px] font-bold uppercase tracking-[0.3em] mb-6">
                <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                <i class="bi bi-chevron-right text-[8px]"></i>
                <span>Tentang SOA</span>
            </nav>
            <h1 class="text-4xl md:text-7xl font-extrabold text-white leading-tight tracking-tighter">
                Satu Akses untuk <br>
                <span class="text-accent italic">Sukabumi Masa Depan.</span>
            </h1>
            <p class="text-slate-400 mt-8 max-w-2xl mx-auto text-sm sm:text-lg leading-relaxed">
                Sukabumi One Access (SOA) adalah inisiatif strategis Diskominfo Kota Sukabumi untuk menyatukan seluruh birokrasi ke dalam satu genggaman tangan warga.
            </p>
        </div>
    </div>
</section>

{{-- 2. FILOSOFI SECTION --}}
<section class="py-20 sm:py-32 bg-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <div class="relative reveal">
                {{-- Ilustrasi Visual --}}
                <div class="relative w-full aspect-square max-w-md mx-auto">
                    <div class="absolute inset-0 bg-primary rounded-[3rem] rotate-6"></div>
                    <div class="absolute inset-0 bg-accent rounded-[3rem] -rotate-3 opacity-20"></div>
                    <div class="absolute inset-0 bg-slate-100 rounded-[3rem] flex items-center justify-center overflow-hidden border-8 border-white shadow-2xl">
                        <img src="{{ asset('image/walikota.webp') }}" alt="Sukabumi Digital" class="w-full h-full object-cover" loading="lazy">
                    </div>
                </div>
            </div>

            <div class="reveal" style="transition-delay: 200ms">
                <p class="text-accent font-black text-xs uppercase tracking-[0.3em] mb-4">Visi & Misi</p>
                <h2 class="text-3xl sm:text-5xl font-extrabold text-primary-dark mb-8 tracking-tight leading-tight">Mendigitalkan Tradisi, Memudahkan Akselerasi.</h2>
                <div class="space-y-6 text-slate-500 text-sm sm:text-base leading-relaxed">
                    <p>
                        Kami percaya bahwa pelayanan publik tidak seharusnya melelahkan. SOA lahir dari kegelisahan akan antrean panjang dan prosedur birokrasi yang berbelit.
                    </p>
                    <p>
                        Melalui teknologi, kami memotong jarak antara pemerintah dan masyarakat. Setiap baris kode yang kami bangun didedikasikan untuk transparansi, efisiensi, dan akuntabilitas bagi seluruh warga Kota Sukabumi.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-6 sm:gap-8 mt-12" id="statsCounterAbout">
                    <div>
                        <span class="text-primary font-black text-3xl sm:text-4xl block roll-number-about" data-final="100%">0%</span>
                        <span class="text-slate-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-widest">Transparansi</span>
                    </div>
                    <div>
                        <span class="text-primary font-black text-3xl sm:text-4xl block roll-number-about" data-final="24/7">00/0</span>
                        <span class="text-slate-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-widest">Akses Layanan</span>
                    </div>
                    <div>
                        <span class="text-primary font-black text-3xl sm:text-4xl block roll-number-about" data-final="5+">0+</span>
                        <span class="text-slate-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-widest">Instansi Terhubung</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- 3. VALUES GRID --}}
<section class="py-20 sm:py-32 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="text-center mb-20 reveal">
            <p class="text-accent text-[10px] font-black uppercase tracking-[0.3em] mb-3">Nilai Utama</p>
            <h2 class="text-3xl sm:text-5xl font-extrabold text-primary-dark tracking-tight">Pilar Pelayanan Kami</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Value 1 --}}
            <div class="reveal">
                <div class="vision-card group">
                    <div class="side-indicator"></div>
                    <span class="option-ghost-number">01</span>
                    <div class="icon-box-about">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-4">Kecepatan Tinggi</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Kami meminimalisir proses manual yang lambat. Dengan SOA, pengajuan dokumen dilakukan secara instan dari perangkat Anda.
                    </p>
                </div>
            </div>

            {{-- Value 2 --}}
            <div class="reveal" style="transition-delay: 150ms">
                <div class="vision-card group">
                    <div class="side-indicator"></div>
                    <span class="option-ghost-number">02</span>
                    <div class="icon-box-about">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-4">Keamanan Data</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Data kependudukan Anda adalah aset berharga. Kami menggunakan enkripsi standar industri untuk menjaga privasi warga.
                    </p>
                </div>
            </div>

            {{-- Value 3 --}}
            <div class="reveal" style="transition-delay: 300ms">
                <div class="vision-card group">
                    <div class="side-indicator"></div>
                    <span class="option-ghost-number">03</span>
                    <div class="icon-box-about">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-4">Transparansi Total</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Warga berhak tahu posisi berkas mereka. Pantau setiap tahap proses verifikasi melalui fitur Lacak Berkas kami.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- 4. CTA / DISKOMINFO SECTION --}}
<section class="py-20 sm:py-28 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-8 reveal">
        <div class="cta-panel">
            <div class="cta-panel-top"></div>
            <div class="cta-grid">

                {{-- Kolom kiri: pesan & aksi --}}
                <div class="cta-copy">
                    <span class="cta-eyebrow">Dikelola Resmi</span>
                    <h4 class="text-2xl sm:text-4xl font-extrabold text-primary-dark leading-tight tracking-tight">
                        Membangun Sukabumi yang<br>Cerdas &amp; Berdaya Saing.
                    </h4>
                    <p class="text-slate-500 text-sm sm:text-base leading-relaxed mt-5 max-w-lg">
                        SOA dikelola sepenuhnya oleh Dinas Komunikasi dan Informatika Kota Sukabumi untuk mewujudkan Smart City yang berkelanjutan bagi seluruh warga.
                    </p>

                    <div class="cta-actions">
                        <a href="https://sukabumikota.go.id" target="_blank" rel="noopener noreferrer" class="cta-btn-primary">
                            Website Pemerintah Kota
                            <i class="bi bi-arrow-up-right"></i>
                        </a>
                        <a href="{{ route('home') }}" class="cta-btn-ghost">
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>

                {{-- Kolom kanan: identitas Diskominfo --}}
                <div class="cta-identity">
                    <div class="bg-dot-matrix"></div>

                    <div class="cta-brand-mark">
                        <i class="bi bi-bank"></i>
                    </div>

                    <div class="cta-wordmark">
                        <span class="brand-name">SUKABUMI</span>
                        <span class="brand-sub">One Access</span>
                    </div>

                    <div class="cta-access-line">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>

                    <p class="text-slate-400 text-xs leading-relaxed relative z-[2] max-w-[220px]">
                        Dikelola oleh Diskominfo Kota Sukabumi — satu pintu, seluruh akses layanan.
                    </p>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
    // Fungsi untuk memutar angka secara acak sebelum berhenti di angka tujuan
    function rollNumber(el, finalText, totalFrames = 20, frameDuration = 50) {
        const chars = finalText.split('');
        let frame = 0;

        const timer = setInterval(() => {
            frame++;
            const display = chars.map((char, i) => {
                if (!/[0-9]/.test(char)) return char; // Abaikan simbol seperti % atau /

                const lockAt = Math.round(((i + 1) / chars.length) * totalFrames);
                if (frame >= lockAt) return char;
                return Math.floor(Math.random() * 10);
            }).join('');

            el.textContent = display;

            if (frame >= totalFrames) {
                el.textContent = finalText;
                clearInterval(timer);
            }
        }, frameDuration);
    }

    // Jalankan animasi hanya saat elemen terlihat di layar (scrolled into view)
    document.addEventListener('DOMContentLoaded', () => {
        const statsSection = document.getElementById('statsCounterAbout');
        if (!statsSection) return;

        let played = false;
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !played) {
                    played = true;
                    document.querySelectorAll('.roll-number-about').forEach(el => {
                        rollNumber(el, el.dataset.final);
                    });
                    observer.disconnect();
                }
            });
        }, { threshold: 0.5 });

        observer.observe(statsSection);
    });

    // Parallax untuk background logo di Hero
    window.addEventListener('scroll', () => {
        const hugeLogo = document.querySelector('.huge-logo-bg');
        let scrollValue = window.scrollY;

        if (hugeLogo) {
            hugeLogo.style.transform = `translateY(${scrollValue * 0.2}px) rotate(${scrollValue * 0.02}deg)`;
        }
    });
</script>
@endsection