@extends('layouts.admin')

@section('title', 'Visual Card Editor - Formulir')
@section('page-title', 'Visual Card Editor')
@section('page-icon', 'bi-card-image')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;

    // Sinkronisasi data dari object database ($svc)
    // Jika $svc adalah instance Model Layanan:
    $id = $svc->id;
    $nama = $svc->nama;
    $deskripsi = $svc->deskripsi;
    $primaryColor = $svc->primary_color; // Digunakan sebagai Background Ikon
    $icon = $svc->icon;
    $cover = $svc->cover;

    $icons = [
        'bi-people-fill', 'bi-person-badge-fill', 'bi-person-workspace', 'bi-house-door-fill',
        'bi-building-fill', 'bi-buildings', 'bi-building-gear', 'bi-signpost-2-fill',
        'bi-broadcast', 'bi-wifi', 'bi-router-fill', 'bi-camera-video-fill', 'bi-newspaper', 'bi-megaphone-fill',
        'bi-cash-stack', 'bi-cash-coin', 'bi-bank', 'bi-bank2', 'bi-wallet2', 'bi-credit-card-2-front-fill', 'bi-calculator-fill', 'bi-graph-up-arrow',
        'bi-flower1', 'bi-flower2', 'bi-tree-fill', 'bi-basket3-fill', 'bi-water', 'bi-egg-fried',
        'bi-hospital-fill', 'bi-heart-pulse-fill', 'bi-life-preserver',
        'bi-mortarboard-fill', 'bi-book-fill', 'bi-journal-bookmark-fill',
        'bi-shield-shaded', 'bi-shield-fill-check', 'bi-shield-lock-fill', 'bi-incognito',
        'bi-bus-front-fill', 'bi-truck', 'bi-car-front-fill', 'bi-bicycle', 'bi-compass-fill',
        'bi-lightning-charge-fill', 'bi-droplet-fill', 'bi-droplet-half', 'bi-recycle',
        'bi-briefcase-fill', 'bi-clipboard-check-fill', 'bi-clipboard-data-fill', 'bi-file-earmark-text-fill',
        'bi-files', 'bi-gear-fill', 'bi-tools', 'bi-award-fill', 'bi-trophy-fill', 'bi-stars',
        'bi-globe', 'bi-globe2', 'bi-map-fill', 'bi-geo-alt-fill', 'bi-flag-fill',
    ];
@endphp

<style>
    /* --- 1. PREVIEW CARD STYLING --- */
    .service-premium-card-pv { background: #ffffff; border: 1px solid #f1f5f9; border-radius: 2rem; position: relative; height: 100%; display: flex; flex-direction: column; transition: all 0.4s ease; overflow: hidden; }
    .service-img-wrapper-pv { position: relative; width: 100%; height: 160px; overflow: hidden; border-radius: 2rem 2rem 0 0; background: #0A1E33; }
    .service-img-pv { width: 100%; height: 100%; object-fit: cover; opacity: 0.7; transition: 0.6s; }
    
    .service-icon-floating-wrap-pv { position: relative; z-index: 30; display: flex; justify-content: flex-end; padding: 0 1.5rem; margin-top: -28px; }
    
    /* REQ: BG Primary (User Color), Icon Accent (Gold) */
    .service-icon-floating-pv { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: #123457; color: #E8A33D; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border: 4px solid #fff; transition: all 0.3s; }
    .time-badge-pv { display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; color: #059669; font-size: 9px; font-weight: 800; padding: 4px 10px; border-radius: 8px; margin-top: 8px; text-transform: uppercase; }

    /* --- 2. FORM STYLING --- */
    .command-card { background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; position: relative; overflow: hidden; }
    .form-header-custom { background: #0A1E33; padding: 24px 32px; border-bottom: 4px solid #E8A33D; }
    .form-section { padding: 32px; position: relative; }
    .form-section + .form-section { border-top: 1px solid #F1F5F9; }
    
    .section-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
    .section-number { width: 26px; height: 26px; border-radius: 8px; background: #123457; color: #E8A33D; font-size: 11px; font-weight: 900; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .section-heading h3 { font-size: 12px; font-weight: 800; color: #123457; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    .input-field-wrapper { margin-bottom: 20px; position: relative; }
    .label-command { display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 8px; margin-left: 4px; letter-spacing: 0.5px; }
    .input-control-command { width: 100%; padding: 14px 16px 14px 48px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; font-size: 13px; font-weight: 700; color: #123457; transition: 0.3s; }
    .input-control-command:focus { outline: none; border-color: #123457; background: #fff; box-shadow: 0 0 0 4px rgba(18, 52, 87, 0.05); }
    .input-icon-left { position: absolute; left: 16px; top: 35px; color: #94a3b8; font-size: 1.1rem; }
    .input-icon-left-warna { position: absolute; left: 16px; top: 13px; color: #94a3b8; font-size: 1.1rem; }

    /* --- 3. CUSTOM FILE UPLOAD --- */
    .file-upload-wrapper { position: relative; width: 100%; cursor: pointer; }
    .file-upload-box { display: flex; align-items: center; gap: 12px; padding: 10px 16px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; transition: all 0.3s ease; height: 52px; }
    .file-upload-wrapper:hover .file-upload-box { border-color: #E8A33D; background: #fff; }
    .file-icon { width: 34px; height: 34px; border-radius: 10px; background: #123457; color: #E8A33D; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .file-text { font-size: 11px; font-weight: 700; color: #94a3b8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .file-input-real { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10; }

    /* --- 4. ICON PICKER TRIGGER --- */
    .icon-picker-trigger { width: 100%; display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; cursor: pointer; transition: 0.25s ease; }
    .icon-picker-trigger:hover { border-color: #E8A33D; }
    .icon-picker-preview { width: 40px; height: 40px; border-radius: 10px; background: #123457; color: #E8A33D; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; transition: background 0.3s; }

    /* --- 5. MODAL ICON PICKER --- */
    #iconPickerModal { position: fixed; inset: 0; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 20px; }
    #iconPickerModal.is-open { display: flex; }
    #iconPickerBackdrop { position: absolute; inset: 0; background: rgba(10, 30, 51, 0.8); backdrop-filter: blur(8px); }
    #iconPickerCard { position: relative; z-index: 2; width: 100%; max-width: 440px; background: #ffffff; border-radius: 28px; box-shadow: 0 30px 60px rgba(0,0,0,0.4); overflow: hidden; }
    .icon-picker-modal-header { padding: 20px 24px; background: #0A1E33; border-bottom: 4px solid #E8A33D; color: #fff; display: flex; justify-content: space-between; align-items: center; }
    .icon-picker-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; padding: 24px; max-height: 350px; overflow-y: auto; }
    .icon-item { aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center; background: #F8FAFC; border-radius: 12px; border: 1px solid #E2E8F0; cursor: pointer; transition: 0.2s; font-size: 1.25rem; color: #94a3b8; }
    .icon-item:hover, .icon-item.active { background: #123457; border-color: #123457; color: #E8A33D; transform: scale(1.1); }

    .sticky-preview { position: sticky; top: 40px; }

    /* ===== ACTION FOOTER ===== */
    .form-footer{
        padding: 25px 30px;
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
        color: #FFFFFF;
        border: none;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        box-shadow: 0 10px 15px -3px rgba(18,52,87,.2);
        transition: all .3s ease;
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

        <!-- SISI KIRI: FORM EDITOR -->
        <div class="lg:col-span-7">
            <div class="command-card shadow-sm">
                <div class="form-header-custom">
                    <h2 class="text-white font-black text-lg uppercase tracking-tight">Card Identity Customizer</h2>
                    <p class="text-white/50 text-[11px] mt-1 uppercase font-bold tracking-widest">Formulir: {{ $nama }}</p>
                </div>

                {{-- SINKRONISASI ACTION & ENCTYPE --}}
                <form action="{{ route('admin.layanan.form.update', [$portalCode, $id]) }}" method="POST" enctype="multipart/form-data" id="visualForm">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <div class="section-heading"><span class="section-number">1</span><h3>Konten Publik</h3></div>
                        
                        <div class="input-field-wrapper">
                            <label class="label-command">Nama Layanan (Judul Card)</label>
                            <input type="text" id="in_nama" name="nama" value="{{ $nama }}" class="input-control-command" required>
                            <i class="bi bi-tag input-icon-left"></i>
                        </div>

                        <div class="input-field-wrapper">
                            <label class="label-command">Deskripsi Layanan (Caption)</label>
                            <textarea id="in_deskripsi" name="deskripsi" rows="3" class="input-control-command !pl-4" style="height: auto; padding-top: 14px;" required>{{ $deskripsi }}</textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-heading"><span class="section-number">2</span><h3>Estetika & Branding</h3></div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="input-field-wrapper">
                                <label class="label-command">Warna Latar Ikon (Primary)</label>
                                <div class="relative">
                                    <input type="color" id="in_primary_color" name="primary_color" value="{{ $primaryColor }}" class="input-control-command !p-1.5 !h-[52px] cursor-pointer">
                                    <i class="bi bi-palette input-icon-left-warna"></i>
                                </div>
                            </div>

                            <div class="input-field-wrapper">
                                <label class="label-command">Foto Sampul Baru</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="in_cover" name="cover" class="file-input-real" accept="image/*">
                                    <div class="file-upload-box">
                                        <div class="file-icon"><i class="bi bi-image"></i></div>
                                        <span class="file-text" id="file_name_display">Pilih file gambar...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="input-field-wrapper mb-0">
                            <label class="label-command">Ikon Badge (Ikon Utama)</label>
                            <input type="hidden" name="icon" id="selected_icon_value" value="{{ $icon }}">
                            <button type="button" class="icon-picker-trigger" onclick="openIconPicker()">
                                <span class="icon-picker-preview" id="iconPickerPreview" data-primary-color="{{ $primaryColor }}">
                                    <i class="bi {{ $icon }}"></i>
                                </span>
                                <span class="text-xs font-bold text-slate-500 flex-1 text-left">Klik untuk mengganti simbol ikon</span>
                                <i class="bi bi-grid-3x3-gap text-slate-400 text-xs"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Hidden Field: Secondary Color Tetap Gold --}}
                    <input type="hidden" name="secondary_color" value="#E8A33D">

                    <div class="form-footer">
                        <a href="{{ route('admin.layanan.form.manage', [$portalCode, $instansi_slug]) }}" class="btn-command-cancel">Batal</a>
                        <button type="submit" class="btn-command-save" id="btnSubmit">Simpan Identitas Visual</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SISI KANAN: LIVE PREVIEW -->
        <div class="lg:col-span-5">
            <div class="sticky-preview">
                <div class="text-center mb-6">
                    <span class="bg-primary/5 text-primary text-[10px] font-black px-5 py-2.5 rounded-full border border-primary/10 uppercase tracking-widest">
                        <i class="bi bi-eye-fill mr-1"></i> Live Card Preview
                    </span>
                </div>
                
                <div class="service-premium-card-pv shadow-2xl shadow-primary/10">
                    <div class="service-img-wrapper-pv">
                        <img id="pv_img" src="{{ $cover ? asset('storage/'.$cover) : asset('image/no-image.png') }}" class="service-img-pv" alt="Preview">
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-primary/20 to-transparent"></div>
                    </div>
                    
                    <div class="service-icon-floating-wrap-pv">
                        <div id="pv_icon_bg" class="service-icon-floating-pv" data-primary-color="{{ $primaryColor }}">
                            <i id="pv_icon" class="bi {{ $icon }} text-xl"></i>
                        </div>
                    </div>
                    
                    <div class="p-8 pt-5 flex flex-col flex-1 text-left bg-white">
                        <h3 id="pv_nama" class="text-lg font-black text-primary-dark mb-1 leading-tight">{{ $nama }}</h3>
                        <p id="pv_deskripsi" class="text-[11px] text-slate-400 font-medium leading-relaxed">{{ $deskripsi }}</p>
                        
                        <div class="flex items-center justify-between mt-auto pt-6">
                            <span class="time-badge-pv"><i class="bi bi-file-earmark-medical mr-1"></i> Form Mandiri</span>
                            <span class="text-[10px] font-black text-primary uppercase tracking-widest">Mulai <i class="bi bi-arrow-right ml-1"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL ICON PICKER --}}
<div id="iconPickerModal">
    <div id="iconPickerBackdrop" onclick="closeIconPicker()"></div>
    <div id="iconPickerCard">
        <div class="icon-picker-modal-header">
            <div><h4 class="m-0 font-black text-sm uppercase">Pilih Ikon</h4><p class="text-[9px] text-white/50 uppercase font-bold tracking-widest mt-0.5">Visual Identity</p></div>
            <button type="button" class="w-8 h-8 rounded-lg bg-white/10 text-white flex items-center justify-center border-none cursor-pointer" onclick="closeIconPicker()"><i class="bi bi-x-lg text-xs"></i></button>
        </div>
        <div class="icon-picker-grid custom-scrollbar">
            @foreach($icons as $iconItem)
                <div class="icon-item {{ $iconItem == $icon ? 'active' : '' }}" onclick="pickIcon('{{ $iconItem }}', this)"><i class="bi {{ $iconItem }}"></i></div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openIconPicker() { document.getElementById('iconPickerModal').classList.add('is-open'); document.body.style.overflow = 'hidden'; }
    function closeIconPicker() { document.getElementById('iconPickerModal').classList.remove('is-open'); document.body.style.overflow = ''; }
    
    function pickIcon(iconName, element) {
        document.getElementById('selected_icon_value').value = iconName;
        document.querySelectorAll('.icon-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        
        // Update Preview Kecil & Preview Kartu
        document.querySelector('#iconPickerPreview i').className = `bi ${iconName}`;
        document.getElementById('pv_icon').className = `bi ${iconName} text-xl`;
        closeIconPicker();
    }

    // Live Sync Input -> Preview
    document.getElementById('in_nama').oninput = function() { document.getElementById('pv_nama').innerText = this.value || 'Nama Layanan'; };
    document.getElementById('in_deskripsi').oninput = function() { document.getElementById('pv_deskripsi').innerText = this.value || 'Deskripsi singkat layanan...'; };

    // Upload Image Preview
    document.getElementById('in_cover').onchange = function() {
        const [file] = this.files;
        if (file) {
            document.getElementById('file_name_display').innerText = file.name;
            document.getElementById('pv_img').src = URL.createObjectURL(file);
            document.getElementById('pv_img').style.opacity = "0.7";
        }
    };

    // Loading State
    document.getElementById('visualForm').onsubmit = function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Menyimpan...';
        btn.style.opacity = "0.7";
        btn.style.pointerEvents = "none";
    };
</script>
@endpush

@endsection