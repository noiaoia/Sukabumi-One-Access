@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Pusat Identitas Warga')
@section('page-subtitle', 'Otoritas verifikasi dan manajemen akun penduduk.')
@section('page-icon', 'bi-person-vcard-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<style>
    /* --- 1. PREMIUM HUD (STATS) --- */
    .vault-hud {
        background: #0A1E33;
        border-radius: 24px;
        padding: 25px 40px;
        border-bottom: 4px solid #E8A33D;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        margin-bottom: 30px;
        box-shadow: 0 20px 40px -15px rgba(10, 30, 51, 0.5);
        position: relative; overflow: hidden;
    }
    /* Efek cahaya halus di HUD */
    .vault-hud::before {
        content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(232,163,61,0.15) 0%, transparent 70%);
        pointer-events: none;
    }

    .hud-stat-box { display: flex; align-items: center; gap: 15px; position: relative; z-index: 2; }
    .hud-icon-box {
        width: 48px; height: 48px; border-radius: 14px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        display: flex; align-items: center; justify-content: center;
        color: #E8A33D; font-size: 1.4rem;
    }
    .hud-title { font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px; }
    .hud-count { font-size: 20px; font-weight: 900; color: #fff; margin-top: 2px; }

    /* --- 2. SEARCH & FILTER BAR --- */
    .search-card-sleek {
        background: #ffffff; border-radius: 20px; padding: 12px 15px;
        border: 1.5px solid #eef2f6; display: flex; align-items: center;
        justify-content: space-between; gap: 20px;
        box-shadow: 0 10px 25px -10px rgba(18, 52, 87, 0.05);
    }
    .search-input-wrap { position: relative; width: 100%; max-width: 450px; }
    .search-input-wrap i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #cbd5e1; }
    .input-search-custom {
        width: 100%; padding: 10px 15px 10px 48px; background: #F8FAFC;
        border: 1.5px solid #E2E8F0; border-radius: 14px;
        font-size: 13px; font-weight: 700; color: #123457; transition: 0.3s;
    }
    .input-search-custom:focus { outline: none; border-color: #123457; background: #fff; box-shadow: 0 0 0 4px rgba(18, 52, 87, 0.04); }

    /* --- 3. RESIDENT RECORD CARD --- */
    .record-card {
        background: #ffffff; border-radius: 22px; border: 1px solid #f1f5f9;
        margin-bottom: 12px; position: relative; overflow: hidden;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .record-card:hover {
        transform: translateY(-4px); border-color: #E8A33D;
        box-shadow: 0 15px 35px -10px rgba(18, 52, 87, 0.1);
    }

    /* Side status indicator */
    .record-card::after {
        content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
        background: var(--status-color);
    }

    .record-row { padding: 16px 24px; display: flex; align-items: center; gap: 25px; }

    /* Avatar System */
    .record-avatar {
        width: 52px; height: 52px; border-radius: 16px;
        background: #F8FAFC; border: 1.5px solid #E2E8F0;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; color: #123457; font-size: 1.1rem;
        flex-shrink: 0; position: relative;
    }
    .record-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 14px; }
    
    /* Small Badge on Avatar */
    .badge-overlap {
        position: absolute; -right: -5px; -bottom: -5px;
        width: 22px; height: 22px; border-radius: 50%;
        background: #fff; border: 2px solid #fff;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); font-size: 10px;
    }

    /* Metadata Info */
    .meta-label { font-size: 8.5px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .meta-value { font-size: 12.5px; font-weight: 800; color: #123457; line-height: 1.2; }

    /* Action Buttons */
    .btn-command-mini {
        width: 36px; height: 36px; border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        transition: 0.2s; border: none; cursor: pointer; font-size: 1.1rem;
    }

    /* Drawer Info */
    .record-drawer {
        max-height: 0; overflow: hidden; transition: max-height 0.4s ease;
        background: #fcfcfd; border-top: 1px solid #f1f5f9;
    }
    .record-card.active .record-drawer { max-height: 400px; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .reveal { animation: fadeIn 0.4s ease forwards; }
</style>

<div class="max-w-7xl mx-auto space-y-6 pb-12">
    @include('components.admin.modal-form-errors')

    {{-- --- 1. PREMIUM HUD (STATS) --- --}}
    <div class="vault-hud reveal">
        <div class="hud-stat-box">
            <div class="hud-icon-box"><i class="bi bi-people-fill"></i></div>
            <div>
                <p class="hud-title">Populasi Terdaftar</p>
                <p class="hud-count">{{ number_format($totalWarga) }}</p>
            </div>
        </div>
        <div class="hud-stat-box">
            <div class="hud-icon-box" style="color: #10B981;"><i class="bi bi-patch-check-fill"></i></div>
            <div>
                <p class="hud-title">Identitas Terverifikasi</p>
                <p class="hud-count">{{ number_format($totalVerified) }}</p>
            </div>
        </div>
        <div class="hud-stat-box">
            <div class="hud-icon-box" style="color: #EF4444;"><i class="bi bi-slash-circle-fill"></i></div>
            <div>
                <p class="hud-title">Akun Ditangguhkan</p>
                <p class="hud-count">{{ number_format($totalSuspended) }}</p>
            </div>
        </div>
    </div>

    {{-- --- 2. SEARCH TOOLBAR --- --}}
    <div class="search-card-sleek reveal" style="animation-delay: 0.1s">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-5 bg-accent rounded-full"></div>
            <h3 class="text-xs font-black text-primary-dark uppercase tracking-widest">Master Record Index</h3>
        </div>

        <form action="{{ route('admin.users.index', $portalCode) }}" method="GET" class="search-input-wrap">
            <i class="bi bi-search"></i>
            <input type="text" name="search" value="{{ request('search') }}" class="input-search-custom" placeholder="Cari Nama, Email, atau Nomor WhatsApp...">
        </form>
    </div>

    {{-- --- 3. RESIDENT LIST --- --}}
    <div id="userList">
        @forelse($users as $user)
        <div class="record-card reveal" id="user-{{ $user->id }}" style="--status-color: {{ $user->status == 'active' ? '#10B981' : '#EF4444' }}">
            <div class="record-row">
                
                {{-- Column 1: Avatar --}}
                <div class="record-avatar shadow-sm">
                    @if($user->profile_photo)
                        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}">
                    @else
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    @endif
                    
                    @if($user->email_verified_at)
                        <div class="badge-overlap text-emerald-500" title="Email Terverifikasi">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                    @endif
                </div>

                {{-- Column 2: Name & User ID --}}
                <div class="min-w-0" style="width: 240px;">
                    <p class="meta-label">Nama Penduduk</p>
                    <h4 class="meta-value truncate">{{ $user->name }}</h4>
                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tighter">ID: SOA-{{ $user->id + 1000 }} ({{ $user->username }})</p>
                </div>

                {{-- Column 3: Contact Info --}}
                <div class="hidden md:block flex-1">
                    <p class="meta-label">Kontak Resmi</p>
                    <div class="flex items-center gap-2">
                        <i class="bi bi-envelope-at text-slate-300 text-xs"></i>
                        <span class="meta-value !text-[11px]">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <i class="bi bi-whatsapp text-emerald-500 text-xs"></i>
                        <span class="meta-value !text-[11px]">{{ $user->phone ?? '---' }}</span>
                    </div>
                </div>

                {{-- Column 4: Status --}}
                <div class="hidden lg:block w-32">
                    <p class="meta-label">Status Akun</p>
                    <span class="px-3 py-1 rounded-lg font-black text-[9px] uppercase tracking-widest {{ $user->status == 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                        {{ $user->status }}
                    </span>
                </div>

                {{-- Column 5: Actions --}}
                <div class="flex items-center gap-2 border-l border-slate-100 pl-6">
                    <button onclick="toggleUserInfo('{{ $user->id }}')" class="btn-command-mini bg-primary/5 text-primary/60 hover:bg-primary hover:text-white" title="Info Lengkap">
                        <i class="bi bi-info-circle-fill"></i>
                    </button>

                    {{-- Form Toggle Status --}}
                    <form action="{{ route('admin.users.toggle-status', [$portalCode, $user->id]) }}" method="POST">
                        @csrf @method('PATCH')
                        {{-- Tombol Toggle Status (Panggil Modal) --}}
                        <button type="button" 
                            class="btn-command-mini {{ $user->status == 'active' ? 'bg-red-50 text-red-400 hover:bg-red-500' : 'bg-emerald-50 text-emerald-500 hover:bg-emerald-500' }} hover:text-white" 
                            data-user="{{ json_encode($user) }}"
                            data-url="{{ route('admin.users.toggle-status', [$portalCode, $user->id]) }}"
                            onclick="window.openStatusModal(JSON.parse(this.dataset.user), this.dataset.url)"
                            title="{{ $user->status == 'active' ? 'Blokir Akun' : 'Aktifkan Akun' }}">
                            <i class="bi {{ $user->status == 'active' ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                        </button>
                    </form>

                    {{-- Form Hapus --}}
                    <form action="{{ route('admin.users.destroy', [$portalCode, $user->id]) }}" method="POST" onsubmit="return confirm('Hapus permanen data warga: {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf @method('DELETE')
                        {{-- Tombol Hapus (Panggil Modal Konfirmasi Berjenjang) --}}
                        <button type="button" 
                            class="btn-command-mini bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white"
                            data-user="{{ json_encode($user) }}"
                            data-url="{{ route('admin.users.destroy', [$portalCode, $user->id]) }}"
                            onclick="window.openDeleteUserModal(JSON.parse(this.dataset.user), this.dataset.url)"
                            title="Hapus Permanen">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- --- INFO DRAWER --- --}}
            <div class="record-drawer">
                <div class="p-8 grid grid-cols-2 lg:grid-cols-4 gap-10">
                    <div>
                        <p class="meta-label">Tanggal Registrasi</p>
                        <p class="text-xs font-bold text-primary-dark">
                            {{ $user->created_at->translatedFormat('d F Y') }}
                        </p>
                        <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <p class="meta-label">Aktivitas Terakhir</p>
                        <p class="text-xs font-bold text-primary-dark">
                            {{ $user->last_seen ? $user->last_seen->translatedFormat('d M Y, H:i') : 'N/A' }}
                        </p>
                        <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold">
                            {{ $user->last_seen ? $user->last_seen->diffForHumans() : 'Belum pernah login' }}
                        </p>
                    </div>
                    <div class="lg:col-span-2">
                        <p class="meta-label">Informasi Sistem</p>
                        <div class="bg-white border border-slate-100 p-3 rounded-xl flex items-start gap-3">
                            <i class="bi bi-shield-check text-emerald-500 mt-0.5"></i>
                            <p class="text-[10px] font-bold text-slate-500 leading-relaxed">
                                Pengguna ini terdaftar melalui portal publik Sukabumi One Access. Seluruh data kependudukan sinkron dengan database pusat. IP Terdeteksi saat registrasi: <span class="text-primary-dark">{{ request()->ip() }}</span>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="py-32 text-center bg-white rounded-[2.5rem] border border-dashed border-slate-200 reveal">
            <i class="bi bi-person-exclamation text-5xl text-slate-200 mb-4"></i>
            <p class="text-slate-400 text-sm font-black uppercase tracking-widest">Index Kosong</p>
            <p class="text-slate-300 text-xs mt-2">Tidak ditemukan data warga dengan kata kunci "{{ request('search') }}".</p>
        </div>
        @endforelse
    </div>

    {{-- --- PAGINATION --- --}}
    <div class="mt-10">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>

<x-admin.modal-user-status :portalCode="$portalCode" />
<x-admin.modal-delete-user :portalCode="$portalCode" />

<script>
    function toggleUserInfo(id) {
        const container = document.getElementById('user-' + id);
        const isActive = container.classList.contains('active');
        
        // Tutup drawer lain jika ada yang terbuka
        document.querySelectorAll('.record-card').forEach(el => el.classList.remove('active'));
        
        if (!isActive) {
            container.classList.add('active');
        }
    }
</script>

@endsection