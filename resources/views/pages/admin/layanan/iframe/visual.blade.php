@extends('layouts.admin')

@section('title', 'Visual Card Editor')
@section('page-title', 'Visual Card Editor')
@section('page-icon', 'bi-card-image')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;

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
    /* --- 1. PREVIEW CARD STYLING (CLONE DKP3) --- */
    .service-premium-card-pv { background: #ffffff; border: 1px solid #f1f5f9; border-radius: 2rem; position: relative; height: 100%; display: flex; flex-direction: column; transition: all 0.4s ease; overflow: hidden; }
    .service-img-wrapper-pv { position: relative; width: 100%; height: 140px; overflow: hidden; border-radius: 2rem 2rem 0 0; background: #f8fafc; }
    .service-img-pv { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.9); transition: 0.6s; }
    .service-icon-floating-wrap-pv { position: relative; z-index: 30; display: flex; justify-content: flex-end; padding: 0 1.25rem; margin-top: -24px; }
    .service-icon-floating-pv{
        width:48px;
        height:48px;
        border-radius:14px;
        display:flex;
        align-items:center;
        justify-content:center;
        border:4px solid #fff;
        color:#E8A33D;
        transition:.3s;
    }
    .time-badge-pv { display: inline-flex; align-items: center; gap: 4px; background: #F4F6F8; color: #4A545D; font-size: 10px; font-weight: 600; padding: 3px 9px; border-radius: 999px; margin-top: 6px; }

    /* --- 2. FORM STYLING --- */
    .command-card { background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; position: relative; }
    .form-header-custom { background: #0A1E33; padding: 24px; border-radius: 24px 24px 0 0; border-bottom: 4px solid #E8A33D; }
    .form-section { padding: 30px; position: relative; }
    .form-section + .form-section { border-top: 1px solid #F1F5F9; }
    .section-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
    .section-number { width: 26px; height: 26px; border-radius: 8px; background: #123457; color: #E8A33D; font-size: 11px; font-weight: 900; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .section-heading h3 { font-size: 13px; font-weight: 800; color: #123457; margin: 0; text-transform: uppercase; }

    .input-field-wrapper { margin-bottom: 18px; position: relative; }
    .label-command { display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 8px; }
    .input-control-command { width: 100%; padding: 12px 16px 12px 45px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; font-size: 13px; font-weight: 700; color: #123457; transition: 0.3s; }
    .input-control-command:focus { outline: none; border-color: #123457; background: #fff; }
    .input-icon-left { position: absolute; left: 16px; color: #cbd5e1; top: 50%; transform: translateY(-50%); }

    /* --- 3. CUSTOM FILE UPLOAD --- */
    .file-upload-wrapper { position: relative; width: 100%; cursor: pointer; }
    .file-upload-box { display: flex; align-items: center; gap: 12px; padding: 10px 16px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; transition: all 0.3s ease; height: 48px; }
    .file-upload-wrapper:hover .file-upload-box { border-color: #E8A33D; background: #fff; }
    .file-icon { width: 32px; height: 32px; border-radius: 8px; background: #123457; color: #E8A33D; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .file-text { font-size: 11px; font-weight: 700; color: #94a3b8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .file-input-real { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10; }

    /* --- 4. ICON PICKER TRIGGER (tombol pemicu, tampilannya sama, cuma perilakunya beda) --- */
    .icon-picker-trigger {
        width: 100%; display: flex; align-items: center; gap: 12px;
        padding: 12px 16px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px;
        cursor: pointer; transition: all 0.25s ease;
    }
    .icon-picker-trigger:hover { border-color: #E8A33D; }
    .icon-picker-trigger.is-open { border-color: #123457; box-shadow: 0 0 0 4px rgba(18, 52, 87, 0.05); background: #ffffff; }

    .icon-picker-preview {
        width: 40px; height: 40px; border-radius: 10px; background: #123457; color: #E8A33D;
        display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;
    }

    /* --- 5. ICON PICKER MODAL: kotak mengambang di tengah layar, bukan dropdown --- */
    #iconPickerModal {
        position: fixed;
        inset: 0;
        z-index: 200;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
    }
    #iconPickerModal.is-open { display: flex; }

    #iconPickerBackdrop {
        position: absolute;
        inset: 0;
        background: rgba(10, 30, 51, 0.5);
        backdrop-filter: blur(6px);
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    #iconPickerModal.is-open #iconPickerBackdrop { opacity: 1; }

    #iconPickerCard {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 400px;
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 30px 60px rgba(10, 30, 51, 0.3);
        opacity: 0;
        transform: scale(0.92) translateY(12px);
        transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
    }
    #iconPickerModal.is-open #iconPickerCard {
        opacity: 1;
        transform: scale(1) translateY(0);
    }

    .icon-picker-modal-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 20px 22px;
        background: #0A1E33;
        border-bottom: 4px solid #E8A33D;
    }
    .icon-picker-modal-header h4 { font-size: 13px; font-weight: 800; color: #ffffff; margin: 0; }
    .icon-picker-modal-header p { font-size: 10.5px; color: rgba(255,255,255,0.5); margin: 2px 0 0; }

    .icon-picker-close {
        width: 30px; height: 30px; border-radius: 9px;
        background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6); border: 1px solid rgba(255,255,255,0.1);
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem; cursor: pointer; transition: all 0.2s ease;
    }
    .icon-picker-close:hover { background: rgba(232,163,61,0.15); color: #E8A33D; border-color: rgba(232,163,61,0.3); }

    .icon-picker-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 10px;
        padding: 18px 20px 22px;
        max-height: 320px;
        overflow-y: auto;
    }

    .icon-item {
        aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center;
        background: #F8FAFC; border-radius: 12px; border: 1px solid #E2E8F0; cursor: pointer;
        transition: all 0.2s; font-size: 1.2rem; color: #94a3b8;
    }
    .icon-item:hover, .icon-item.active { background: #123457; border-color: #123457; color: #E8A33D; box-shadow: 0 4px 10px rgba(18, 52, 87, 0.2); transform: scale(1.06); }

    @media (max-width: 420px) {
        .icon-picker-grid { grid-template-columns: repeat(5, 1fr); }
    }

    .sticky-preview { position: sticky; top: 110px; }

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
                    <h2 class="text-white font-black text-lg uppercase tracking-tight">Service Card Customizer</h2>
                    <p class="text-white/50 text-xs mt-1">Personalisasi tampilan kartu layanan Anda secara real-time.</p>
                </div>

                <form id="editorForm" action="{{ route('admin.layanan.iframe.visual.update', [$portalCode, $svc->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- SECTION 1 --}}
                    <div class="form-section">
                        <div class="section-heading">
                            <span class="section-number">1</span>
                            <h3>Informasi Konten</h3>
                        </div>
                        <div class="input-field-wrapper">
                            <label class="label-command">Nama Layanan</label>
                            <div class="relative">
                                <input type="text" id="in_title" name="nama" value="{{ old('nama', $svc->nama) }}" class="input-control-command">
                                <i class="bi bi-tag input-icon-left"></i>
                            </div>
                        </div>
                        <div class="input-field-wrapper">
                            <label class="label-command">Deskripsi Layanan</label>
                            <textarea id="in_desc" name="deskripsi" rows="3" class="input-control-command !pl-4" style="height: auto; padding-top: 12px;">{{ old('deskripsi', $svc->deskripsi) }}</textarea>
                        </div>
                    </div>

                    {{-- SECTION 2 --}}
                    <div class="form-section">
                        <div class="section-heading">
                            <span class="section-number">2</span>
                            <h3>Visual & Branding</h3>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="label-command">Warna Ikon</label>
                                <div class="relative">
                                    <input type="color" id="in_color" name="primary_color" value="{{ old('primary_color', $svc->primary_color) }}" class="input-control-command !p-1.5 !h-[48px] cursor-pointer">
                                    <i class="bi bi-palette input-icon-left"></i>
                                </div>
                            </div>
                            <div>
                                <label class="label-command">Foto Sampul</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="in_img" name="cover" class="file-input-real" accept="image/*">
                                    <div class="file-upload-box">
                                        <div class="file-icon"><i class="bi bi-image"></i></div>
                                        <span class="file-text" id="file_name_display">Pilih Foto...</span>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-2">
                                    PNG, JPG, JPEG • Maksimal 2 MB
                                </p>
                            </div>
                        </div>

                        {{-- ICON PICKER TRIGGER --}}
                        <div class="input-field-wrapper mb-0">
                            <label class="label-command">Ikon Badge</label>
                            <input type="hidden" name="icon" id="selected_icon_value" value="{{ old('icon', $svc->icon) }}">

                            <button type="button" class="icon-picker-trigger" id="iconPickerTrigger" onclick="openIconPicker()">
                                <span class="icon-picker-preview" id="iconPickerPreview">
                                    <i class="bi {{ $svc->icon }}"></i>
                                </span>
                                <span class="text-xs font-bold text-slate-500 flex-1 text-left">Klik untuk pilih ikon</span>
                                <i class="bi bi-grid-3x3-gap text-slate-400 text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-footer">
                        <a href="{{ route('admin.layanan.iframe.manage', [$portalCode, $instansi_slug ?? 'diskominfo']) }}"
                            class="btn-command-cancel">
                            Kembali
                        </a>

                        <button id="submitBtn" type="submit" class="btn-command-save">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SISI KANAN: PREVIEW (CLONE DKP3) -->
        <div class="lg:col-span-5">
            <div class="sticky-preview">
                <div class="text-center mb-6">
                    <span class="bg-primary/5 text-primary text-[10px] font-black px-5 py-2.5 rounded-full border border-primary/10 uppercase tracking-[0.2em] shadow-sm">
                        <i class="bi bi-eye-fill mr-1"></i> Live Card Preview
                    </span>
                </div>

                <div class="service-premium-card-pv shadow-2xl shadow-primary/10">
                    <div class="service-img-wrapper-pv">
                        <img id="pv_img" src="{{ $svc->cover ? asset('storage/'.$svc->cover) : asset('image/SIMPLEKESRAWAN.webp') }}" class="service-img-pv" alt="Preview">
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/60 to-transparent"></div>
                    </div>
                    <div class="service-icon-floating-wrap-pv">
                        <div id="pv_icon_bg" class="service-icon-floating-pv" style="background-color: {{ $svc->primary_color }};">
                            <i id="pv_icon" class="bi {{ $svc->icon }} text-xl"></i>
                        </div>
                    </div>
                    <div class="p-8 pt-4 flex flex-col flex-1 text-left bg-white">
                        <h3 id="pv_title" class="text-lg font-black text-primary-dark mb-1 leading-tight">{{ $svc->nama }}</h3>
                        <p id="pv_desc" class="text-xs text-slate-400 font-medium leading-relaxed">{{ $svc->deskripsi }}</p>
                        <span class="time-badge-pv w-fit"><i class="bi bi-box-arrow-up-right"></i> Portal eksternal</span>
                        <span class="mt-6 inline-flex items-center gap-2 text-xs font-black text-primary uppercase tracking-widest">Buka Layanan <i class="bi bi-arrow-right"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= ICON PICKER MODAL (kotak mengambang di tengah, bukan dropdown) ================= --}}
<div id="iconPickerModal">
    <div id="iconPickerBackdrop" onclick="closeIconPicker()"></div>

    <div id="iconPickerCard">
        <div class="icon-picker-modal-header">
            <div>
                <h4>Pilih Ikon Badge</h4>
                <p>Klik salah satu ikon untuk menerapkannya</p>
            </div>
            <button type="button" class="icon-picker-close" onclick="closeIconPicker()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="icon-picker-grid">
            @foreach($icons as $icon)
                <div class="icon-item {{ $icon == $svc->icon ? 'active' : '' }}" onclick="pickIcon('{{ $icon }}', this)">
                    <i class="bi {{ $icon }}"></i>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    // --- ICON PICKER LOGIC (MODAL MENGAMBANG DI TENGAH) ---
    function openIconPicker() {
        document.getElementById('iconPickerModal').classList.add('is-open');
        document.getElementById('iconPickerTrigger').classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeIconPicker() {
        document.getElementById('iconPickerModal').classList.remove('is-open');
        document.getElementById('iconPickerTrigger').classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function pickIcon(iconName, element) {
        document.getElementById('selected_icon_value').value = iconName;

        document.querySelectorAll('.icon-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        document.getElementById('iconPickerPreview').innerHTML = `<i class="bi ${iconName}"></i>`;
        document.getElementById('pv_icon').className = `bi ${iconName} text-xl`;

        closeIconPicker();
    }

    // Tutup modal dengan tombol Esc
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('iconPickerModal');
            if (modal.classList.contains('is-open')) closeIconPicker();
        }
    });

    // --- FILE UPLOAD LOGIC ---
    document.getElementById('in_img').onchange = function() {
        const [file] = this.files;
        if (file) {
            document.getElementById('file_name_display').innerText = file.name;
            document.getElementById('file_name_display').style.color = "#123457";
            document.getElementById('pv_img').src = URL.createObjectURL(file);
        }
    };

    // --- LIVE SYNC TEXT & COLOR ---
    document.getElementById('in_title').oninput = function() {
        document.getElementById('pv_title').innerText = this.value || 'Nama Layanan';
    };
    document.getElementById('in_desc').oninput = function() {
        document.getElementById('pv_desc').innerText = this.value || 'Deskripsi singkat...';
    };
    document.getElementById('in_color').oninput = function() {
        document.getElementById('pv_icon_bg').style.backgroundColor = this.value;
    };

    document.addEventListener('DOMContentLoaded', function () {

        document.getElementById('editorForm')
            .addEventListener('submit', function () {

                const btn = document.getElementById('submitBtn');

                btn.disabled = true;
                btn.style.opacity = '.8';
                btn.style.pointerEvents = 'none';

                btn.innerHTML = `
                    <i class="bi bi-arrow-repeat animate-spin mr-2"></i>
                    Memproses...
                `;
            });

    });
</script>
@endpush

@endsection