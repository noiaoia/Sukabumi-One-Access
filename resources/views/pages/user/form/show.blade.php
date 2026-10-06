@extends('layouts.app')

@section('title', $layanan->nama . ' - Sukabumi One Access')

@section('content')

<style>

    /* 1. HERO SUB-LAYANAN (tidak diubah) */
    .hero-ktp {
        background-color: #0A1E33;
        position: relative;
        overflow: hidden;
        padding: 3rem 1.5rem 5rem;
        border-bottom: 4px solid #E8A33D;
    }

    .bg-dot-matrix {
        position: absolute;
        inset: 0;
        opacity: 0.1;
        background-image: radial-gradient(#E8A33D 1px, transparent 0);
        background-size: 30px 30px;
    }

    /* 2. CARD OPTION STYLING â€” versi diperbagus */
    .option-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 2rem;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        position: relative;
        overflow: hidden;
        isolation: isolate;
    }

    .option-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 24px 48px rgba(10, 30, 51, 0.1);
        border-color: #E8A33D;
    }

    /* garis aksen kiri â€” dipertahankan persis */
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

    .option-card:hover .side-indicator {
        height: 100%;
    }

    /* nomor urut raksasa samar di pojok, dekorasi khas card premium */
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
        transition: opacity 0.4s ease, transform 0.4s ease;
    }

    .option-card:hover .option-ghost-number {
        opacity: 0.07;
        transform: translateY(-4px);
    }

    /* blob gradasi lembut yang muncul pas hover, bukan icon yang zoom/rotate lagi */
    .option-card::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -30%;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(232,163,61,0.16) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.5s ease;
        z-index: 0;
        pointer-events: none;
    }

    .option-card:hover::before {
        opacity: 1;
    }

    .option-icon-box {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 2;
        transition: box-shadow 0.4s ease, background-color 0.4s ease, color 0.4s ease;
    }

    /* ganti animasi zoom+rotate jadi transisi warna & glow yang lebih tenang */
    .option-card:hover .option-icon-box {
        background: #123457 !important;
        color: #E8A33D !important;
        box-shadow: 0 10px 24px rgba(18, 52, 87, 0.28);
    }

    .option-card:hover .option-icon-box i {
        color: #E8A33D !important;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        color: #64748b;
        font-size: 10px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 10px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        position: relative;
        z-index: 2;
    }

    /* tombol panah bulat, geser halus ke kanan pas hover */
    .option-arrow-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #123457;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        z-index: 2;
    }

    .option-card:hover .option-arrow-btn {
        background: #E8A33D;
        color: #1f2937;
        transform: translateX(4px);
    }

    .option-card-title,
    .option-card-desc {
        position: relative;
        z-index: 2;
    }

</style>

{{-- 1. HERO SECTION --}}
<section class="hero-ktp"> {{-- Menggunakan class hero yang sama agar konsisten --}}
    <div class="bg-dot-matrix"></div>
    <div class="max-w-7xl mx-auto relative z-10 text-center">
        <div class="reveal">
            <nav class="flex justify-center items-center gap-2 text-accent text-[10px] font-bold uppercase tracking-[0.2em] mb-4">
                <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                <i class="bi bi-chevron-right text-[8px]"></i>
                <a href="{{ route('instansi.show', $layanan->instansi->slug) }}" class="hover:text-white transition">
                    {{ $layanan->instansi->nama }}
                </a>
                <i class="bi bi-chevron-right text-[8px]"></i>
                <span>{{ $layanan->nama }}</span>
            </nav>
            <h1 class="text-3xl md:text-5xl font-extrabold text-white leading-tight tracking-tighter">
                Layanan <span class="text-accent">{{ $layanan->nama }}</span>
            </h1>
            <p class="text-slate-400 mt-4 max-w-2xl mx-auto text-sm sm:text-base leading-relaxed">
                {{ $layanan->deskripsi }}
            </p>
        </div>
    </div>
</section>

{{-- 2. OPTIONS GRID --}}
<section class="py-16 sm:py-24 bg-slate-50 min-h-[60vh]">
    <div class="max-w-6xl mx-auto px-4 sm:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">

            @foreach($categories as $index => $category)
                <div class="reveal">
                    <a href="{{ route('layanan.form.category', ['slug' => $layanan->slug,'categoryId' => $category->id,]) }}"class="option-card p-6 sm:p-10 group text-decoration-none">
                        <div class="side-indicator"></div>

                        <span class="option-ghost-number">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>

                        <div>
                            <div
                                class="option-icon-box category-icon"
                                data-color="{{ $category->primary_color }}"
                            >
                                <i class="bi {{ $category->icon }}"></i>
                            </div>

                            <h3 class="option-card-title text-xl sm:text-2xl font-black text-primary-dark mb-3 group-hover:text-primary transition-colors leading-tight">
                                {{ $category->nama }}
                            </h3>

                            <p class="option-card-desc text-xs sm:text-sm text-slate-400 leading-relaxed mb-8">
                                {{ $category->deskripsi ?? 'Belum ada deskripsi kategori.' }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                            <span class="status-badge">
                                <i class="bi bi-lightning-charge-fill text-accent"></i>
                                Verifikasi Digital
                            </span>

                            <span class="option-arrow-btn">
                                <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                </div>
            @endforeach

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reveals = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, { threshold: 0.1 });
        reveals.forEach(el => observer.observe(el));
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.category-icon').forEach(function (iconBox) {
            const color = iconBox.dataset.color;

            if (!color) {
                return;
            }

            iconBox.style.backgroundColor = color + '15';
            iconBox.style.color = color;

            const icon = iconBox.querySelector('i');

            if (icon) {
                icon.style.color = color;
            }
        });
    });
</script>
@endpush