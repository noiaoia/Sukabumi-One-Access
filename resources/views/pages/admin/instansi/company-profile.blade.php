@extends('layouts.admin')

@section('title', 'Company Profile Editor')
@section('page-title', 'Company Profile')
@section('page-icon', 'bi-file-earmark-person-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
    $jumlahLayanan = $instansi->layanans()->count();
@endphp


<style>
    /* --- 1. CSS PREVIEW (CLONE DARI PROFIL DINAS USER) --- */
    .preview-window {
        width: 100%; background: #FAF7F1; border-radius: 2rem; overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;
    }
    
    /* FIX: Menggunakan Flexbox untuk centering absolut */
    .hero-preview {
        background-color: #0A1E33; 
        position: relative; 
        padding: 2.5rem 1rem;
        border-bottom: 4px solid #E8A33D; 
        display: flex;
        flex-direction: column;
        align-items: center; /* Centering Horizontal */
        justify-content: center;
        text-align: center;
    }

    .hero-preview img { 
        width: 75px; 
        height: 75px; 
        object-fit: contain; 
        margin-bottom: 1.25rem;
        display: block; /* Menghilangkan whitespace di bawah image */
    }

    .hero-preview h1 { 
        color: #fff; 
        font-size: 1.15rem; 
        font-weight: 800; 
        line-height: 1.3; 
        margin: 0;
        max-width: 90%;
    }

    .hero-preview h1 span { 
        color: #E8A33D; 
    }

    .quick-info-preview {
        background: #fff; margin: -1.2rem 1.5rem 0; border-radius: 1.25rem;
        display: grid; grid-template-columns: repeat(3, 1fr);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 10; position: relative;
        overflow: hidden;
    }
    .info-item-pv { padding: 12px 8px; text-align: center; border-right: 1px solid #f1f5f9; }
    .info-item-pv:last-child { border-right: none; }
    .info-item-pv p:first-child { color: #123457; font-weight: 900; font-size: 0.9rem; margin-bottom: 2px; }
    .info-item-pv p:last-child { color: #94a3b8; font-size: 6.5px; text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px; margin: 0; }

    .content-preview { padding: 2rem 1.5rem 1.5rem; background: #fff; }
    .content-preview h2 { color: #123457; font-size: 1rem; font-weight: 800; margin-bottom: 0.75rem; line-height: 1.4; }
    .content-preview p { color: #64748b; font-size: 10.5px; line-height: 1.6; margin-bottom: 1rem; }

    /* PETA PREVIEW */
    .map-preview-wrapper { width: 100%; height: 130px; background: #e2e8f0; margin-top: 1rem; border-radius: 1.25rem; overflow: hidden; border: 1px solid #f1f5f9; }
    .map-preview-wrapper iframe { width: 100%; height: 100%; border: 0; }

    /* --- 2. ADMIN FORM STYLING --- */
    .command-card { background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden; }
    .form-section { padding: 28px 35px; }
    .form-section + .form-section { border-top: 1px solid #F1F5F9; }
    .section-heading { display: flex; align-items: center; gap: 12px; margin-bottom: 22px; }
    .section-number { width: 28px; height: 28px; border-radius: 9px; background: #123457; color: #E8A33D; font-size: 12px; font-weight: 900; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .section-heading h3 { font-size: 14px; font-weight: 800; color: #123457; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    .input-control-command { width: 100%; padding: 12px 16px 12px 48px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; font-size: 14px; font-weight: 700; color: #123457; transition: 0.3s; }
    .input-control-command:focus { outline: none; border-color: #E8A33D; background: #fff; box-shadow: 0 0 0 4px rgba(232, 163, 61, 0.05); }
    .input-icon-left { position: absolute; left: 18px; color: #cbd5e1; top: 50%; transform: translateY(-50%); font-size: 1.1rem; }

    .file-upload-wrapper{
        position:relative;
        width:100%;
        cursor:pointer;
    }

    .file-upload-box{
        display:flex;
        align-items:center;
        gap:12px;
        height:48px;
        padding:0 16px;
        background:#F8FAFC;
        border:1.5px solid #E2E8F0;
        border-radius:14px;
        transition:.3s;
    }

    .file-upload-wrapper:hover .file-upload-box{
        border-color:#E8A33D;
        background:#fff;
    }

    .file-icon{
        width:32px;
        height:32px;
        border-radius:8px;
        background:#123457;
        color:#E8A33D;
        display:flex;
        justify-content:center;
        align-items:center;
    }

    .file-text{
        font-size:11px;
        font-weight:700;
        color:#94A3B8;
    }

    .file-input-real{
        position:absolute;
        inset:0;
        opacity:0;
        cursor:pointer;
    }

    .sticky-preview { position: sticky; top: 110px; }

    /* 5. ACTION FOOTER */
    .form-footer{
        padding: 25px 40px;
        background: #F8FAFC;
        border-top: 1px solid #E2E8F0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    /* Secondary Button */
    .btn-command-cancel{
        padding: 12px 24px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        background: #E2E8F0;
        border: 1px solid #CBD5E1;
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
        font-size: 12px;
        font-weight: 800;
        color: #FFFFFF;
        background: #123457;
        border: none;
        box-shadow: 0 10px 15px -3px rgba(18,52,87,.2);
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all .3s ease;
    }

    .btn-command-save:hover{
        background: #E8A33D;
        color: #0A1E33;
        transform: translateY(-2px);
    }
</style>

<div class="max-w-7xl mx-auto">
    <div class="grid lg:grid-cols-12 gap-8">
        
        <!-- SISI KIRI: FORM PENGISIAN -->
        <div class="lg:col-span-7">
            <div class="command-card shadow-sm">
                <div class="bg-[#0A1E33] p-6 border-b-4 border-accent">
                    <h2 class="text-white font-black text-lg uppercase tracking-tight">Company Profile Editor</h2>
                    <p class="text-white/50 text-xs mt-1">Konfigurasi narasi profil dan integrasi peta lokasi.</p>
                </div>

                <form id="companyProfileForm" action="{{ route('admin.instansi.company-profile.update', [$portalCode, $instansi->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    {{-- SECTION 1: HERO --}}
                    <div class="form-section">
                        <div class="section-heading">
                            <span class="section-number">1</span>
                            <h3 class="text-primary-dark">Hero & Branding</h3>
                        </div>
                        <div class="input-field-wrapper">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Headline Slogan</label>
                            <div class="relative">
                                <input type="text" id="in_slogan" name="slogan" value="{{ old('slogan', $instansi->slogan) }}" class="input-control-command">
                                <i class="bi bi-stars input-icon-left"></i>
                            </div>
                        </div>
                        <div class="input-field-wrapper mt-5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Logo Instansi</label>
                            <div class="file-upload-wrapper">
                                <input type="file" id="in_logo" name="logo" class="file-input-real" accept="image/*">
                                <div class="file-upload-box">
                                    <div class="file-icon"><i class="bi bi-image"></i></div>
                                    <span class="file-text" id="logo_name_display"> Ganti Logo Instansi... </span>
                                </div>
                            </div>
                            <p class="text-[9px] text-slate-400 mt-2 font-bold uppercase tracking-wider">Rekomendasi 1200 × 700 px</p>
                        </div>
                    </div>

                    {{-- SECTION 2: STATS --}}
                    <div class="form-section">
                        <div class="section-heading">
                            <span class="section-number">2</span>
                            <h3 class="text-primary-dark">Quick Info Statistics</h3>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="input-field-wrapper">
                                <label class="text-[9px] font-black text-slate-400 uppercase">Layanan</label>
                                <input type="text" id="in_stat1" name="stat1" value="{{ $jumlahLayanan }}" class="input-control-command !pl-4 text-center bg-slate-100" readonly>
                            </div>
                            <div class="input-field-wrapper">
                                <label class="text-[9px] font-black text-slate-400 uppercase">Jam Kerja</label>
                                <input type="text" id="in_stat2" name="stat2" value="{{ old('stat2', $instansi->stat2) }}" class="input-control-command !pl-4 text-center">
                            </div>
                            <div class="input-field-wrapper">
                                <label class="text-[9px] font-black text-slate-400 uppercase">Akses Digital</label>
                                <input type="text" id="in_stat3" name="stat3" value="24/7" class="input-control-command !pl-4 text-center bg-slate-100" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: MENGENAL INSTANSI --}}
                    <div class="form-section">
                        <div class="section-heading">
                            <span class="section-number">3</span>
                            <h3 class="text-primary-dark">Narasi Mengenal Instansi</h3>
                        </div>
                        <div class="input-field-wrapper mb-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Judul Narasi</label>
                            <div class="relative">
                                <input type="text" id="in_about_title" name="about_title" value="{{ old('about_title', $instansi->about_title) }}" class="input-control-command">
                                <i class="bi bi-chat-left-quote input-icon-left"></i>
                            </div>
                        </div>
                        <div class="input-field-wrapper">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Deskripsi Mengenal Instansi</label>
                            <textarea id="in_about_desc" name="deskripsi" rows="5" class="input-control-command !pl-4 !h-auto pt-3 leading-relaxed">{{ old('deskripsi', $instansi->deskripsi) }}</textarea>
                        </div>
                    </div>

                    {{-- SECTION 4: LOKASI & MAPS --}}
                    <div class="form-section">
                        <div class="section-heading">
                            <span class="section-number">4</span>
                            <h3 class="text-primary-dark">Lokasi Kantor & Maps</h3>
                        </div>
                        <div class="input-field-wrapper mb-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Google Maps Embed URL</label>
                            <div class="relative">
                                <input type="text" id="in_map_url" name="maps_embed" value="{{ old('maps_embed', $instansi->maps_embed) }}" class="input-control-command" placeholder="https://www.google.com/maps/embed?pb=...">
                                <i class="bi bi-map input-icon-left"></i>
                            </div>
                        </div>
                        <div class="input-field-wrapper">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat Kantor Lengkap</label>
                            <div class="relative">
                                <input type="text" id="in_address" name="alamat" value="{{ old('alamat', $instansi->alamat) }}" class="input-control-command">
                                <i class="bi bi-geo-alt input-icon-left"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <a href="{{ route('admin.instansi.index', $portalCode) }}"
                            class="btn-command-cancel">
                            Kembali
                        </a>

                        <button type="submit"
                            class="btn-command-save">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SISI KANAN: LIVE PREVIEW -->
        <div class="lg:col-span-5">
            <div class="sticky-preview">
                <div class="text-center mb-6">
                    <span class="bg-accent/10 text-accent-dark text-[10px] font-black px-5 py-2 rounded-full border border-accent/20 uppercase tracking-[0.2em]">
                        Live Page Preview
                    </span>
                </div>

                <div class="preview-window">
                    {{-- AREA HERO YANG SUDAH DIPERBAIKI --}}
                    <div class="hero-preview">
                        <img id="pv_logo" src="{{ asset('storage/' . $instansi->logo) }}" alt="Logo">
                        @php
                            $parts = explode('&', $instansi->slogan ?? '', 2);
                        @endphp

                        <h1 id="pv_hero_title">

                            @if(count($parts) > 1)
                                {{ trim($parts[0]) }}
                                <span id="pv_hero_slogan">& {{ trim($parts[1]) }}</span>
                            @else
                                {{ $instansi->slogan }}
                            @endif

                        </h1>
                    </div>

                    <div class="quick-info-preview">
                        <div class="info-item-pv"><p id="pv_stat1">{{ $jumlahLayanan }}</p><p>Layanan</p></div>
                        <div class="info-item-pv"><p id="pv_stat2">{{ $instansi->stat2 }}</p><p>Jam</p></div>
                        <div class="info-item-pv"><p id="pv_stat3">24/7</p><p>Akses Digital</p></div>
                    </div>

                    <div class="content-preview">
                        <p class="text-[8px] font-black text-accent uppercase tracking-widest mb-2">Mengenal Instansi</p>
                        <h2 id="pv_about_title">{{ $instansi->about_title }}</h2>
                        <p id="pv_about_desc">{{ old('deskripsi', $instansi->deskripsi) }}</p>
                        
                        <div class="map-preview-wrapper" id="pv_map_container">
                            <iframe id="pv_map" src="{{ $instansi->maps_embed }}" allowfullscreen="" loading="lazy"></iframe>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <p class="text-[8px] font-black text-primary-dark uppercase tracking-widest mb-1">
                                <i class="bi bi-geo-alt-fill text-accent mr-1"></i>Lokasi Kantor
                            </p>
                            <p id="pv_address" class="!text-[9px] opacity-70 leading-relaxed">{{ $instansi->alamat }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="layananCountConfig" data-url="{{ route('admin.instansi.layanan-count', [$portalCode, $instansi->id]) }}" hidden></div>

@push('scripts')
<script>
    // --- LIVE UPDATES LOGIC ---
    
    // Hero Title & Slogan
    document.getElementById('in_slogan').addEventListener('input', function () {
        const hero = document.getElementById('pv_hero_title');
        const slogan = this.value.trim();

        if (!slogan) {
            hero.innerHTML = '';
            return;
        }

        const parts = slogan.split('&', 2);

        hero.innerHTML = parts.length > 1
            ? `${parts[0].trim()} <span id="pv_hero_slogan">& ${parts[1].trim()}</span>`
            : slogan;
    });

    // Hero Cover
    document.getElementById('in_logo').addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            document.getElementById('logo_name_display').innerText = file.name;
            document.getElementById('pv_logo').src = URL.createObjectURL(file);
        }
    });

    // Stats
    document.getElementById('in_stat1').addEventListener('input', function() { document.getElementById('pv_stat1').innerText = this.value; });
    document.getElementById('in_stat2').addEventListener('input', function() { document.getElementById('pv_stat2').innerText = this.value; });
    document.getElementById('in_stat3').addEventListener('input', function() { document.getElementById('pv_stat3').innerText = this.value; });

    // About
    document.getElementById('in_about_title').addEventListener('input', function() { document.getElementById('pv_about_title').innerText = this.value; });
    document.getElementById('in_about_desc').addEventListener('input', function() { document.getElementById('pv_about_desc').innerText = this.value; });

    // Maps
    document.getElementById('in_map_url').addEventListener('input', function() {
        let url = this.value;
        if(url.includes('<iframe')) {
            const regex = /src="([^"]+)"/;
            const match = url.match(regex);
            if(match && match[1]) {
                url = match[1];
                this.value = url;
            }
        }
        document.getElementById('pv_map').src = url;
    });

    // Address
    document.getElementById('in_address').addEventListener('input', function() {
        document.getElementById('pv_address').innerText = this.value;
    });

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('companyProfileForm');
        form.addEventListener('submit', function () {
            const btn = this.querySelector('.btn-command-save');
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin me-2"></i> Menyimpan...';
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';
            btn.disabled = true;
        });
    });

    function updateJumlahLayanan() {
        const config = document.getElementById('layananCountConfig');

        if (!config) return;

        const url = config.dataset.url;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const jumlah = `${data.count}`;

                const input = document.getElementById('in_stat1');
                const preview = document.getElementById('pv_stat1');

                if (input) {
                    input.value = jumlah;
                }

                if (preview) {
                    preview.innerText = jumlah;
                }
            })
            .catch(error => {
                console.error('Gagal memperbarui jumlah layanan:', error);
            });
    }

    updateJumlahLayanan();
    setInterval(updateJumlahLayanan, 3000);
</script>
@endpush

@endsection