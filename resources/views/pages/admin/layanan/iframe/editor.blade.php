@extends('layouts.admin')

@section('title', 'Iframe Bridge Editor')
@section('page-title', 'Iframe Bridge Editor')
@section('page-icon', 'bi-window-fullscreen')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
    
    // 1. Amankan data SVC agar tidak error Undefined Key
    $svc = $svc ?? [];

    // 2. Mapping Variabel Utama dengan Fallback (Cadangan)
    $svcId       = $svc['id'] ?? 0;
    $namaLayanan = $svc['nama'] ?? ($svc['title'] ?? 'Layanan');
    $accentColor = $svc['accent_color'] ?? ($svc['color'] ?? '#E8A33D');
    $loaderIcon  = $svc['loader_icon'] ?? ($svc['icon'] ?? 'bi-bank');
    $targetUrl = old(
        'iframe_url',
        $svc->iframe_url
    );

    // 3. Logika Otomatis Teks (Berdasarkan Nama Layanan)
    $headerTitle = $svc['header_title'] ?? ($namaLayanan . ' Kota Sukabumi');
    $loaderTitle = $svc['loader_text'] ?? ('Menghubungkan ke Portal ' . $namaLayanan);
    $loaderSub   = $svc['loader_subtext'] ?? 'Mohon tunggu sebentar, kami sedang membuka koneksi aman ke sistem.';
@endphp

<style>
    /* Definisikan warna awal dari PHP ke CSS */
    #mockup_container {
        --accent: {{ $accentColor }};
    }
    
    /* --- EDITOR UI --- */
    .command-card { background: #ffffff; border: 1px solid #eef2f6; border-radius: 24px; position: relative; }
    .form-header-custom { background: #0A1E33; padding: 24px; border-radius: 24px 24px 0 0; border-bottom: 4px solid #E8A33D; }
    .form-section { padding: 35px; position: relative; }
    .label-command { display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 8px; }
    .input-control-command { width: 100%; padding: 12px 16px 12px 45px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; font-size: 13px; font-weight: 700; color: #123457; transition: 0.3s; }
    .input-control-command:focus { outline: none; border-color: #123457; background: #fff; }
    .input-icon-left { position: absolute; left: 16px; color: #cbd5e1; top: 50%; transform: translateY(-50%); }
    
    /* Smart Card Style */
    .info-auto-badge { background: rgba(185, 129, 16, 0.04); border: 1px solid rgba(185, 129, 16, 0.08); padding: 20px; border-radius: 20px; margin-top: 20px; }

    /* --- MINI PORTAL PREVIEW (CLONE 1:1) --- */
    .mini-portal-wrapper {
        width: 100%; border-radius: 24px; overflow: hidden; 
        background: #fff; height: 500px; display: flex; flex-direction: column;
        position: relative; border: 1px solid #e2e8f0;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
    }

    .pv-portal-header {
        background-color: #0A1E33; padding: 12px 15px; border-bottom: 3px solid var(--accent);
        display: flex; align-items: center; justify-content: space-between; position: relative; overflow: hidden;
    }
    .pv-portal-header::before {
        content: ''; position: absolute; inset: 0; opacity: 0.08;
        background-image: radial-gradient(var(--accent) 1px, transparent 0); background-size: 15px 15px;
    }
    .pv-portal-title { color: #ffffff; font-weight: 800; font-size: 11px; text-transform: uppercase; position: relative; z-index: 2; margin: 0; }
    .pv-btn-dummy { padding: 5px 8px; border-radius: 8px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.14); color: #fff; font-size: 8px; }

    .pv-portal-loader {
        position: absolute; inset: 0; background: #ffffff; display: flex;
        flex-direction: column; align-items: center; justify-content: center; z-index: 100; transition: opacity 0.5s ease;
    }
    .pv-loader-badge { width: 50px; height: 50px; border-radius: 15px; background: #F4F6F8; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; position: relative; }
    .pv-loader-ring { position: absolute; inset: 0; border: 3px solid transparent; border-top-color: var(--accent); border-right-color: var(--accent); border-radius: 15px; animation: spinPortal 1s linear infinite; }
    @keyframes spinPortal { to { transform: rotate(360deg); } }
    
    .pv-loader-title { font-size: 12px; font-weight: 800; color: #123457; margin-bottom: 4px; text-align: center; }
    .pv-loader-sub { font-size: 9px; color: #94a3b8; max-width: 200px; text-align: center; line-height: 1.4; }
    
    .pv-load-progress-track { position: absolute; top: 0; left: 0; right: 0; height: 3px; background: rgba(18,52,87,0.08); z-index: 101; }
    .pv-load-progress-fill { height: 100%; width: 70%; background: linear-gradient(90deg, #123457, var(--accent)); transition: width 0.3s ease; }

    .pv-iframe-area { flex-grow: 1; position: relative; background: #fff; }
    .sticky-preview { position: sticky; top: 40px; }

    /* ===== ACTION FOOTER ===== */
    .form-footer{
        padding: 25px 35px;
        background: #F8FAFC;
        border-top: 1px solid #E2E8F0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        border-radius: 0 0 24px 24px;
    }

    /* Secondary Button */
    .btn-command-cancel{
        padding: 12px 24px;
        border-radius: 12px;
        background: #E2E8F0;
        border: 1px solid #CBD5E1;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .3s ease;
    }

    .btn-command-cancel:hover{
        background: #D1D5DB;
        color: #1E293B;
        transform: translateY(-1px);
    }

    /* Primary Button */
    .btn-command-save{
        padding: 12px 32px;
        border-radius: 12px;
        background: #123457;
        border: none;
        color: #FFFFFF;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all .3s ease;
        box-shadow: 0 10px 15px -3px rgba(18,52,87,.2);
    }

    .btn-command-save:hover{
        background: #E8A33D;
        color: #0A1E33;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(232,163,61,.28);
    }

    .btn-command-cancel:active,
    .btn-command-save:active{
        transform: translateY(0);
    }
</style>

<div class="max-w-7xl mx-auto pb-12">
    <div class="grid lg:grid-cols-12 gap-8">
        
        <!-- SISI KIRI: FORM EDITOR (6 Kolom) -->
        <div class="lg:col-span-6">
            <div class="command-card shadow-sm">
                <div class="form-header-custom">
                    <h2 class="text-white font-black text-lg uppercase tracking-tight">Iframe Setup</h2>
                    <p class="text-white/50 text-xs mt-1">Konfigurasi jembatan akses untuk <b>{{ $namaLayanan }}</b>.</p>
                </div>

                <form id="editorForm" action="{{ route('admin.layanan.iframe.editor.update', [$portalCode, $svcId]) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="form-section">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-7 h-7 rounded-lg bg-primary text-accent flex items-center justify-center font-bold text-xs">1</span>
                            <h3 class="text-xs font-black text-primary-dark uppercase">Koneksi Endpoint</h3>
                        </div>
                        
                        <div class="input-field-wrapper">
                            <label class="label-command">Target URL (Source)</label>
                            <div class="relative">
                                <input type="url" id="in_url" name="iframe_url" value="{{ old('iframe_url', $svc->iframe_url) }}" class="input-control-command" placeholder="https://domain-tujuan.com">
                                @error('iframe_url')
                                    <p class="text-red-500 text-xs mt-2">
                                        {{ $message }}
                                    </p>
                                @enderror
                                <i class="bi bi-globe2 input-icon-left"></i>
                            </div>
                        </div>

                        <!-- INFO OTOMATISASI -->
                        <div class="info-auto-badge">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="bi bi-robot text-accent"></i>
                                <span class="text-[10px] font-black text-primary-dark uppercase tracking-widest">Smart Identity Sync</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed mb-4">
                                Sistem secara otomatis menyamakan identitas portal dengan layanan <b>{{ $namaLayanan }}</b>:
                            </p>
                            
                            <div class="space-y-2">
                                <div class="flex items-center justify-between p-3 bg-white/50 rounded-xl border border-black/5">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Header Title</span>
                                    <span class="text-[10px] font-black text-primary">{{ $headerTitle }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white/50 rounded-xl border border-black/5">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Loader Icon</span>
                                    <div class="flex items-center gap-2">
                                        <i class="bi {{ $loaderIcon }} text-accent"></i>
                                        <span class="text-[10px] font-black text-primary uppercase">{{ str_replace('bi-', '', $loaderIcon) }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white/50 rounded-xl border border-black/5">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Accent Color</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded shadow-sm" id="mockup_container"></div>
                                        <span class="text-[10px] font-black text-primary uppercase">{{ $accentColor }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <a href="{{ route('admin.layanan.iframe.manage', [$portalCode, $instansi_slug ?? 'diskominfo']) }}"
                            class="btn-command-cancel">
                            Kembali
                        </a>

                        <button id="submitBtn" type="submit" class="btn-command-save">
                            Simpan Konfigurasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SISI KANAN: LIVE PREVIEW (6 Kolom) -->
        <div class="lg:col-span-6">
            <div class="sticky-preview">
                <div class="text-center mb-6">
                    <span class="bg-primary/5 text-primary text-[10px] font-black px-5 py-2.5 rounded-full border border-primary/10 uppercase tracking-widest">
                        <i class="bi bi-eye-fill mr-1"></i> Live Bridge Preview
                    </span>
                </div>

                <div class="mini-portal-wrapper" id="mockup_container" style="--accent: {{ $accentColor }}">
                    
                    {{-- Mini Header Clone --}}
                    <div class="pv-portal-header shadow-lg">
                        <div class="flex items-center gap-2">
                            <div class="pv-btn-dummy"><i class="bi bi-arrow-left-circle-fill"></i></div>
                            <h1 class="pv-portal-title">{{ $headerTitle }}</h1>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="pv-btn-dummy"><i class="bi bi-box-arrow-up-right"></i></div>
                            <div class="w-6 h-6 rounded-lg bg-white/5 flex items-center justify-center text-emerald-400 border border-white/10"><i class="bi bi-shield-lock-fill text-[9px]"></i></div>
                        </div>
                    </div>

                    {{-- Content Area --}}
                    <div class="pv-iframe-area">
                        {{-- Progress bar --}}
                        <div class="pv-load-progress-track" id="pv_progress_bar"><div class="pv-load-progress-fill"></div></div>

                        {{-- Loader Clone --}}
                        <div id="pv_loader" class="pv-portal-loader">
                            <div class="pv-loader-badge">
                                <div class="pv-loader-ring"></div>
                                <i class="bi {{ $loaderIcon }} text-primary text-xl"></i>
                            </div>
                            <p class="pv-loader-title">{{ $loaderTitle }}</p>
                            <p class="pv-loader-sub">{{ $loaderSub }}</p>
                        </div>

                        {{-- Real Iframe --}}
                        <iframe src="{{ $targetUrl }}" id="pv_iframe" class="w-full h-full border-none opacity-0 transition-opacity duration-700" onload="iframePreviewLoaded()"></iframe>
                    </div>
                </div>

                <div class="mt-8 p-6 bg-accent/5 rounded-[2rem] border border-accent/10">
                    <p class="text-[11px] text-primary/60 leading-relaxed font-medium text-center">
                        <i class="bi bi-info-circle mr-1"></i> Sisi preview mensimulasikan tampilan asli portal saat website target berhasil dimuat.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function iframePreviewLoaded() {
        const loader = document.getElementById('pv_loader');
        const progress = document.getElementById('pv_progress_bar');
        const iframe = document.getElementById('pv_iframe');

        if(loader) loader.style.opacity = '0';
        if(progress) progress.style.opacity = '0';
        
        setTimeout(() => {
            if(loader) loader.style.display = 'none';
            if(iframe) iframe.classList.replace('opacity-0', 'opacity-100');
        }, 500);
    }

    document.getElementById('in_url').addEventListener('input', function () {
        const url = this.value;
        if (url.startsWith('http')) {
            const loader = document.getElementById('pv_loader');
            const progress = document.getElementById('pv_progress_bar');
            const iframe = document.getElementById('pv_iframe');
            loader.style.display = 'flex';
            loader.style.opacity = '1';
            progress.style.opacity = '1';
            iframe.classList.replace('opacity-100', 'opacity-0');
            iframe.src = url;
        }

    });

    document.addEventListener('DOMContentLoaded', function () {

        const form = document.getElementById('editorForm');

        form?.addEventListener('submit', function () {

            const btn = document.getElementById('submitBtn');

            btn.disabled = true;
            btn.style.pointerEvents = 'none';

            btn.innerHTML = `
                <i class="bi bi-arrow-repeat animate-spin me-2"></i>
                Memproses...
            `;
        });

    });
</script>
@endpush

@endsection