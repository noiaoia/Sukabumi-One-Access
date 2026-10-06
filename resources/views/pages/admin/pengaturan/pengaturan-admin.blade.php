@extends('layouts.admin')

@section('title', 'Pengaturan Akun')
@section('page-title', 'Konfigurasi Sistem')
@section('page-icon', 'bi-gear-wide-connected')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<style>
    /* --- 1. BANNER SECTION (IDENTIK DENGAN PROFIL) --- */
    .id-banner-sleek {
        position: relative;
        background: #0A1E33;
        border-radius: 20px;
        overflow: hidden;
        padding: 25px 35px;
        border-bottom: 3px solid #E8A33D;
        box-shadow: 0 15px 30px -10px rgba(10, 30, 51, 0.4);
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

    /* --- 2. CARD MODERN (CONTRAST ENHANCED) --- */
    .card-modern {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #eef2f6;
        padding: 22px 28px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 10px 20px -5px rgba(18, 52, 87, 0.06); 
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

    .input-icon-center {
        position: absolute; left: 0; top: 0; bottom: 0;
        width: 42px; display: flex; align-items: center; justify-content: center;
        color: #cbd5e1; font-size: 1.1rem; pointer-events: none;
    }

    .input-sleek:focus { outline: none; border-color: #123457; background: #fff; }
    .input-sleek:focus + .input-icon-center { color: #E8A33D; }

    /* --- 4. TOGGLE & INFO BOX --- */
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

    /* Toggle Switch Style */
    .switch { position: relative; display: inline-block; width: 34px; height: 20px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; inset: 0; background-color: #e2e8f0; transition: .4s; border-radius: 20px; }
    .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #10B981; }
    input:checked + .slider:before { transform: translateX(14px); }
</style>

<div class="max-w-5xl mx-auto space-y-5 pb-10">

    @include('components.admin.modal-form-errors')

    {{-- --- BANNER SECTION --- --}}
    <div class="id-banner-sleek">
        <div class="id-pattern"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            <div class="avatar-wrapper">
                <div class="avatar-frame">
                    <span>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                </div>
            </div>

            <div class="text-center md:text-left flex-1">
                <div class="flex items-center justify-center md:justify-start gap-2 mb-1.5">
                    <span class="px-3 py-1 rounded-md border border-white/20 text-white text-[9px] font-black uppercase tracking-widest">System Settings</span>
                    <div class="flex items-center gap-1 text-accent text-[9px] font-bold uppercase"><i class="bi bi-shield-lock-fill"></i> Secure Session</div>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-none">Keamanan & Otoritas</h1>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-2">Kelola kredensial dan preferensi sistem portal</p>
            </div>

            <div class="hidden lg:flex items-center gap-6 border-l border-white/10 pl-6">
                <div class="text-center">
                    <p class="text-[8px] font-bold text-slate-500 uppercase mb-0.5">Status Server</p>
                    <p class="text-xs font-bold text-emerald-400 uppercase tracking-tighter">Normal</p>
                </div>
            </div>
        </div>
    </div>

    {{-- --- CONTENT SECTION --- --}}
    <div class="grid lg:grid-cols-12 gap-5">
        
        {{-- Sisi Kiri: Ganti Password --}}
        <div class="lg:col-span-8">
            <div class="card-modern">
                <div class="flex items-center gap-2 mb-6 border-b border-slate-50 pb-4">
                    <div class="w-8 h-8 rounded-lg bg-primary/5 flex items-center justify-center text-primary"><i class="bi bi-key-fill"></i></div>
                    <h3 class="text-xs font-black text-primary-dark uppercase tracking-widest">Perbarui Kata Sandi</h3>
                </div>

                <form action="{{ route('admin.settings.update-password', $portalCode) }}" method="POST" id="passwordForm">
                    @csrf @method('PUT')

                    <div class="space-y-1">
                        <div class="input-container">
                            <label class="label-sleek">Password Saat Ini</label>
                            <div class="input-wrapper">
                                <input type="password" name="current_password" class="input-sleek" placeholder="••••••••" required>
                                <div class="input-icon-center"><i class="bi bi-lock-fill"></i></div>
                            </div>
                            @error('current_password') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="py-2 flex items-center gap-3 opacity-20">
                            <div class="h-[1px] flex-1 bg-slate-400"></div>
                            <i class="bi bi-shield-shaded text-xs"></i>
                            <div class="h-[1px] flex-1 bg-slate-400"></div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-x-5">
                            <div class="input-container">
                                <label class="label-sleek">Password Baru</label>
                                <div class="input-wrapper">
                                    <input type="password" name="new_password" class="input-sleek" placeholder="Min. 8 Karakter" required>
                                    <div class="input-icon-center"><i class="bi bi-shield-lock"></i></div>
                                </div>
                            </div>
                            <div class="input-container">
                                <label class="label-sleek">Konfirmasi Password</label>
                                <div class="input-wrapper">
                                    <input type="password" name="new_password_confirmation" class="input-sleek" placeholder="Ulangi" required>
                                    <div class="input-icon-center"><i class="bi bi-check-circle"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="btn-primary-sleek">Simpan Password Baru</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sisi Kanan: Preferensi & Sesi --}}
        <div class="lg:col-span-4 space-y-5">
            <div class="card-modern">
                <div class="security-info">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="icon-box-center bg-primary-dark text-accent"><i class="bi bi-sliders"></i></div>
                        <h4 class="text-[11px] font-black text-primary-dark uppercase">Preferensi</h4>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-bold text-slate-700">Notifikasi Browser</p>
                                <p class="text-[9px] text-slate-400">Alert laporan masuk</p>
                            </div>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="flex items-center justify-between opacity-50">
                            <div>
                                <p class="text-[11px] font-bold text-slate-700">Mode Gelap</p>
                                <p class="text-[9px] text-slate-400">Coming Soon</p>
                            </div>
                            <label class="switch">
                                <input type="checkbox" disabled>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-modern">
                <div class="flex items-center gap-3">
                    <div class="icon-box-center bg-blue-50 text-blue-500"><i class="bi bi-display"></i></div>
                    <div>
                        <p class="text-[10px] font-black text-primary-dark uppercase">Sesi Saat Ini</p>
                        <p class="text-[11px] font-bold text-slate-400">Windows &bull; Chrome</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('passwordForm').addEventListener('submit', function() {
        const btn = this.querySelector('.btn-primary-sleek');
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i>';
        btn.classList.add('opacity-80', 'pointer-events-none');
    });
</script>

@endsection