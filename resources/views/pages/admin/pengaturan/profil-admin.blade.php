@extends('layouts.admin')

@section('title', 'Profil Saya')
@section('page-title', 'Otoritas Akun')
@section('page-icon', 'bi-shield-lock-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<style>
    /* --- 1. BANNER & AVATAR (COMPACT & SHARP) --- */
    .id-banner-sleek {
        position: relative;
        background: #0A1E33;
        border-radius: 20px;
        overflow: hidden;
        padding: 25px 35px;
        border-bottom: 3px solid #E8A33D;
        box-shadow: 0 15px 30px -10px rgba(10, 30, 51, 0.4); /* Shadow lebih dalam agar menonjol */
    }
    
    .id-pattern {
        position: absolute; inset: 0;
        background-image: radial-gradient(rgba(232, 163, 61, 0.1) 0.5px, transparent 0.5px);
        background-size: 15px 15px; opacity: 0.5;
    }

    .avatar-wrapper {
        position: relative; width: 80px; height: 80px; flex-shrink: 0;
    }

    .avatar-frame {
        width: 100%; height: 100%; border-radius: 18px;
        border: 3px solid rgba(255,255,255,0.15); background: #123457;
        overflow: hidden; display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; font-weight: 900; color: #E8A33D;
    }
    .avatar-frame img { width: 100%; height: 100%; object-fit: cover; }

    .btn-camera-upload {
        position: absolute; bottom: -5px; right: -5px;
        width: 28px; height: 28px; border-radius: 8px;
        background: #E8A33D; color: #0A1E33; border: 2px solid #0A1E33;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem; cursor: pointer; transition: 0.2s;
    }

    /* --- 2. CARD MODERN (CONTRAST ENHANCED) --- */
    .card-modern {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #eef2f6; /* Border lebih gelap sedikit dari putih */
        padding: 22px 28px;
        /* Layered shadow agar 'lepas' dari background putih */
        box-shadow: 
            0 4px 6px -1px rgba(0, 0, 0, 0.02), 
            0 10px 20px -5px rgba(18, 52, 87, 0.06); 
        transition: all 0.3s ease;
    }

    .card-modern:hover {
        border-color: #cbd5e1;
        box-shadow: 0 20px 25px -5px rgba(18, 52, 87, 0.1);
    }

    /* --- 3. INPUT DENGAN IKON CENTERED --- */
    .input-container { position: relative; margin-bottom: 16px; }
    
    .label-sleek {
        display: block; font-size: 9px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 1px;
        color: #94a3b8; margin-bottom: 6px; margin-left: 2px;
    }

    .input-wrapper { position: relative; display: flex; align-items: center; }

    .input-sleek {
        width: 100%; padding: 10px 14px 10px 42px;
        background: #F8FAFC; border: 1.5px solid #E2E8F0;
        border-radius: 12px; font-size: 13px; font-weight: 700;
        color: #123457; transition: all 0.2s;
    }

    /* Ikon benar-benar di tengah secara vertikal */
    .input-icon-center {
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 42px; /* Sama dengan padding-left input */
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        font-size: 1.1rem;
        pointer-events: none;
    }

    .input-sleek:focus { outline: none; border-color: #123457; background: #fff; }
    .input-sleek:focus + .input-icon-center { color: #E8A33D; }

    /* --- 4. INFO BOX CENTERED --- */
    .security-info {
        background: #F8FAFC; border-radius: 15px; padding: 18px;
        border: 1px solid #f1f5f9;
    }

    .icon-box-center {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .btn-primary-sleek {
        background: #123457; color: #fff; border: none;
        padding: 10px 22px; border-radius: 10px;
        font-size: 11px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 1px; transition: 0.3s; cursor: pointer;
        box-shadow: 0 4px 12px rgba(18, 52, 87, 0.2);
    }
    .btn-primary-sleek:hover { background: #E8A33D; color: #0A1E33; transform: translateY(-2px); }
</style>

<div class="max-w-5xl mx-auto space-y-5 pb-10">

    @include('components.admin.modal-form-errors')

    {{-- --- BANNER SECTION --- --}}
    <div class="id-banner-sleek">
        <div class="id-pattern"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            <div class="avatar-wrapper">
                <div class="avatar-frame" id="avatarPreviewWrap">
                    @if(Auth::user()->profile_photo)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Avatar">
                    @else
                        <span id="avatarInitial">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                    @endif
                </div>
                <label for="ap_photo" class="btn-camera-upload"><i class="bi bi-camera-fill"></i></label>
            </div>

            <div class="text-center md:text-left flex-1">
                <div class="flex items-center justify-center md:justify-start gap-2 mb-1.5">
                    <span class="px-3 py-1 rounded-md border border-white/20 text-white text-[9px] font-black uppercase tracking-widest">Admin Access</span>
                    <span class="flex items-center gap-1 text-emerald-400 text-[9px] font-bold uppercase"><i class="bi bi-patch-check-fill"></i> Verified</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-none">{{ Auth::user()->name }}</h1>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-2">@ {{ Auth::user()->username }} &bull; {{ $portalCode }}</p>
            </div>

            <div class="hidden lg:flex items-center gap-6 border-l border-white/10 pl-6">
                <div class="text-center">
                    <p class="text-[8px] font-bold text-slate-500 uppercase mb-0.5">Daftar Sejak</p>
                    <p class="text-xs font-bold text-white">{{ \Carbon\Carbon::parse(Auth::user()->created_at)->translatedFormat('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- --- CONTENT SECTION --- --}}
    <div class="grid lg:grid-cols-12 gap-5">
        
        {{-- Sisi Kiri: Form --}}
        <div class="lg:col-span-8">
            <div class="card-modern">
                <div class="flex items-center gap-2 mb-6 border-b border-slate-50 pb-4">
                    <div class="w-8 h-8 rounded-lg bg-primary/5 flex items-center justify-center text-primary"><i class="bi bi-person-badge"></i></div>
                    <h3 class="text-xs font-black text-primary-dark uppercase tracking-widest">Detail Identitas</h3>
                </div>

                <form action="{{ route('admin.profile.update', $portalCode) }}" method="POST" enctype="multipart/form-data" id="profileForm">
                    @csrf @method('PUT')
                    <input type="file" name="profile_photo" id="ap_photo" class="hidden" accept="image/*">

                    <div class="grid sm:grid-cols-2 gap-x-5">
                        <div class="input-container">
                            <label class="label-sleek">Nama Lengkap</label>
                            <div class="input-wrapper">
                                <input type="text" name="name" class="input-sleek" value="{{ Auth::user()->name }}" required>
                                <div class="input-icon-center"><i class="bi bi-person-vcard"></i></div>
                            </div>
                        </div>
                        <div class="input-container">
                            <label class="label-sleek">Email Resmi</label>
                            <div class="input-wrapper">
                                <input type="email" name="email" class="input-sleek" value="{{ Auth::user()->email }}" required>
                                <div class="input-icon-center"><i class="bi bi-envelope-at"></i></div>
                            </div>
                        </div>
                        <div class="input-container">
                            <label class="label-sleek">WhatsApp</label>
                            <div class="input-wrapper">
                                <input type="text" name="phone" class="input-sleek" value="{{ Auth::user()->phone }}" inputmode="numeric" maxlength="13" pattern="[0-9]{10,13}" placeholder="08xxxxxxxxxx">
                                <div class="input-icon-center"><i class="bi bi-whatsapp"></i></div>
                            </div>
                        </div>
                        <div class="input-container">
                            <label class="label-sleek">Otoritas Portal</label>
                            <div class="input-wrapper">
                                <input type="text" class="input-sleek bg-slate-50 text-slate-400" value="{{ $portalCode }}" disabled>
                                <div class="input-icon-center"><i class="bi bi-shield-lock"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="btn-primary-sleek">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sisi Kanan: Keamanan --}}
        <div class="lg:col-span-4 space-y-5">
            <div class="card-modern">
                <div class="security-info">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="icon-box-center bg-primary-dark text-accent"><i class="bi bi-fingerprint"></i></div>
                        <h4 class="text-[11px] font-black text-primary-dark uppercase">Akses Login</h4>
                    </div>
                    <p class="text-[10px] text-slate-500 leading-relaxed mb-4">Ganti kata sandi secara berkala untuk menjaga keamanan otoritas Anda.</p>
                    <a href="{{ route('admin.settings.index', ['portal_code' => 'portal-' . Auth::user()->username]) }}"
                    class="flex items-center justify-center gap-2 w-full py-2 rounded-lg border-2 border-slate-100 text-[9px] font-black text-primary-dark hover:bg-slate-50 transition-all uppercase">
                        Ubah Password <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="card-modern">
                <div class="flex items-center gap-3">
                    <div class="icon-box-center bg-emerald-50 text-emerald-500"><i class="bi bi-hdd-network"></i></div>
                    <div>
                        <p class="text-[10px] font-black text-primary-dark uppercase">IP Terdeteksi</p>
                        <p class="text-[12px] font-mono font-bold text-slate-400">{{ request()->ip() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('ap_photo').addEventListener('change', function () {
        const [file] = this.files;
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('avatarPreviewWrap').innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('profileForm').addEventListener('submit', function() {
        const btn = this.querySelector('.btn-primary-sleek');
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i>';
        btn.classList.add('opacity-80', 'pointer-events-none');
    });
</script>

@endsection