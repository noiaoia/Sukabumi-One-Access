@extends('layouts.app')

@section('title', 'Profil Saya - Sukabumi One Access')

@section('content')

<style>
    /* =========================================
       1. SIDEBAR DASHBOARD LAYOUT
       ========================================= */
    .dashboard-wrapper {
        display: flex;
        min-height: calc(100vh - 80px); /* Kurangi tinggi navbar utama */
        background-color: #F8FAFC;
    }

    /* SIDEBAR STYLING */
    .profile-sidebar {
        width: 280px;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        padding: 2rem 1.5rem;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
    }

    .sidebar-menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 1rem 1.25rem;
        border-radius: 14px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #64748b;
        transition: all 0.3s ease;
        margin-bottom: 0.5rem;
    }

    .sidebar-menu-link i { font-size: 1.2rem; }

    .sidebar-menu-link:hover {
        background: #f1f5f9;
        color: #123457;
    }

    .sidebar-menu-link.active {
        background: #123457;
        color: #f1f5f9;
        box-shadow: 0 10px 20px -5px rgba(18, 52, 87, 0.2);
    }

    .sidebar-menu-link.active .menu-chevron { opacity: 1; transform: translateX(0); }
    .menu-chevron { opacity: 0; transform: translateX(-4px); transition: all 0.2s ease; margin-left: auto; font-size: 0.75rem; }

    /* CONTENT AREA */
    .profile-content {
        flex: 1;
        padding: 3rem;
    }

    .profile-card-premium {
        background: #ffffff;
        border-radius: 30px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    .profile-cover {
        height: 130px;
        position: relative;
        background-image:
            linear-gradient(135deg, #123457 0%, #0A1E33 100%),
            radial-gradient(rgba(255,255,255,0.07) 1px, transparent 1px);
        background-size: cover, 14px 14px;
    }

    .avatar-dashboard {
        width: 100px;
        height: 100px;
        background: #ffffff;
        border-radius: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 900;
        color: #123457;
        border: 4px solid #fff;
        position: absolute;
        bottom: -50px;
        left: 40px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .avatar-dashboard img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: inherit;
    }

    .avatar-verified-badge {
        position: absolute;
        bottom: -4px;
        right: -4px;
        width: 30px;
        height: 30px;
        background: #E8A33D;
        border: 3px solid #fff;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3A2205;
        font-size: 0.8rem;
    }

    .verified-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #FBF1DE;
        color: #8A5A0F;
        font-size: 0.68rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 999px;
        margin-top: 6px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
    }

    .data-item {
        background: #F8FAFC;
        border: 1px solid #F1F5F9;
        border-radius: 18px;
        padding: 1.1rem 1.25rem;
        transition: border-color 0.2s ease;
    }

    .data-item:hover { border-color: #e2e8f0; }

    .data-item label {
        display: block;
        font-size: 0.68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
        margin-bottom: 0.55rem;
    }

    .data-value {
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.92rem;
    }

    .data-value.is-empty {
        color: #94a3b8;
        font-weight: 600;
        font-style: italic;
    }

    /* RESPONSIVE — aturan .profile-sidebar & .dashboard-wrapper untuk mobile
       ada di partials/sidebar-profile.blade.php, jangan diduplikasi di sini. */
    @media (max-width: 1024px) {
        .profile-content { padding: 1.5rem; }
    }

    @media (max-width: 640px) {
        .avatar-dashboard { width: 80px; height: 80px; left: 20px; bottom: -40px; font-size: 2rem; }
        .info-grid { grid-template-columns: 1fr; gap: 0.85rem; }
    }
</style>

<div class="dashboard-wrapper">

    {{-- 1. PANGGIL SIDEBAR PARTIAL --}}
    @include('partials.sidebar-profile')

    {{-- 2. MAIN CONTENT AREA --}}
    <main class="profile-content">
        <div class="max-w-4xl mx-auto">

            {{-- Profile Card Premium --}}
            <div class="profile-card-premium reveal" style="transition-delay: 100ms">
                <div class="profile-cover">
                    <div class="avatar-dashboard">
                        @if(Auth::user()->profile_photo)
                            {{-- Jika User sudah upload foto --}}
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Foto Profil">
                        @else
                            {{-- Jika belum ada foto, tampilkan inisial nama --}}
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                        <div class="avatar-verified-badge" title="Warga terverifikasi">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                    </div>
                </div>

                <div class="px-6 sm:px-10 pt-20 pb-12">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-12">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-black text-primary">{{ Auth::user()->name }}</h1>
                            <p class="text-slate-400 text-xs sm:text-sm font-mono font-medium mt-1">ID-{{ Auth::user()->id + 1000 }}</p>
                            <span class="verified-pill">
                                <i class="bi bi-patch-check-fill"></i> Warga Terverifikasi
                            </span>
                        </div>
                        <!-- Tombol di Halaman Profile -->
                        <button onclick="openEditProfileModal()" class="inline-flex items-center gap-2 bg-white border border-slate-200 text-primary px-6 py-2.5 rounded-xl font-bold text-xs hover:bg-slate-50 transition active:scale-95 shadow-sm self-start">
                            <i class="bi bi-pencil-fill text-[10px]"></i> Edit Profil
                        </button>
                    </div>

                    {{-- Data Information --}}
                    <div class="info-grid border-t border-slate-50 pt-10">

                        <div class="data-item">
                            <label>Nama Lengkap</label>
                            <div class="data-value">
                                <div class="w-8 h-8 rounded-lg bg-primary/5 text-primary flex items-center justify-center text-sm"><i class="bi bi-person"></i></div>
                                <span>{{ Auth::user()->name }}</span>
                            </div>
                        </div>

                        <div class="data-item">
                            <label>Alamat Email</label>
                            <div class="data-value">
                                <div class="w-8 h-8 rounded-lg bg-accent/10 text-accent-dark flex items-center justify-center text-sm"><i class="bi bi-envelope"></i></div>
                                <span>{{ Auth::user()->email }}</span>
                            </div>
                        </div>

                        <div class="data-item">
                            <label>Username Utama</label>
                            <div class="data-value">
                                <div class="w-8 h-8 rounded-lg bg-primary/5 text-primary flex items-center justify-center text-sm"><i class="bi bi-at"></i></div>
                                <span>{{ Auth::user()->username }}</span>
                            </div>
                        </div>

                        <div class="data-item">
                            <label>Nomor WhatsApp</label>
                            <div class="data-value {{ Auth::user()->phone ? '' : 'is-empty' }}">
                                <div class="w-8 h-8 rounded-lg bg-accent/10 text-accent-dark flex items-center justify-center text-sm"><i class="bi bi-whatsapp"></i></div>
                                <span>{{ Auth::user()->phone ?? 'Belum diatur' }}</span>
                            </div>
                        </div>

                        <div class="data-item">
                            <label>Status Kependudukan</label>
                            <div class="data-value">
                                <div class="w-8 h-8 rounded-lg bg-primary/5 text-primary flex items-center justify-center text-sm"><i class="bi bi-geo-alt"></i></div>
                                <span>Kota Sukabumi</span>
                            </div>
                        </div>

                        <div class="data-item">
                            <label>Tanggal Bergabung</label>
                            <div class="data-value">
                                <div class="w-8 h-8 rounded-lg bg-accent/10 text-accent-dark flex items-center justify-center text-sm"><i class="bi bi-calendar-event"></i></div>
                                <span>{{ Auth::user()->created_at->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Info Alert --}}
            <div class="mt-8 bg-primary rounded-[2rem] p-6 sm:p-8 text-white relative overflow-hidden reveal" style="transition-delay: 200ms">

                {{-- Dekorasi Lingkaran Samar --}}
                <div class="absolute right-0 top-0 w-48 h-48 bg-white/5 rounded-full -mr-20 -mt-20"></div>
                <div class="absolute left-10 bottom-0 w-24 h-24 bg-accent/5 rounded-full -mb-10"></div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-5">
                    {{-- Ikon dengan Backdrop Blur & Aksen Gold --}}
                    <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-2xl shadow-xl border border-white/10 shrink-0">
                        <i class="bi bi-info-circle text-accent"></i>
                    </div>

                    <div class="flex-1">
                        <h4 class="text-lg font-extrabold tracking-tight">Lengkapi Data Anda</h4>
                        <p class="text-slate-300 text-xs sm:text-sm mt-1 leading-relaxed">
                            Gunakan fitur <span class="text-white font-bold">Edit Profil</span> untuk memperbarui foto atau informasi kontak agar pelayanan lebih optimal.
                        </p>
                    </div>

                    <button onclick="openEditProfileModal()" class="shrink-0 bg-accent text-accent-dark px-5 py-2.5 rounded-xl font-bold text-xs hover:brightness-95 transition self-start sm:self-center">
                        Lengkapi sekarang
                    </button>
                </div>
            </div>

        </div>
    </main>
</div>

@endsection