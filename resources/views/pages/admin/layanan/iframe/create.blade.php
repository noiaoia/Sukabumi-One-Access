@extends('layouts.admin')

@section('title', 'Tambah Layanan Iframe')
@section('page-title', 'Registrasi Iframe')
@section('page-icon', 'bi-window-fullscreen')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
    // Daftar ikon lengkap sama dengan halaman instansi agar konsisten
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
    /* 1. Card & Layout Base - Identik dengan Instansi */
    .command-card { background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; max-width: 900px; margin: 0 auto; }
    .command-header { background: #0A1E33; padding: 32px 40px; border-bottom: 4px solid #E8A33D; }
    .command-header h2 { color: #ffffff; font-size: 20px; font-weight: 800; margin: 0; }
    .command-header p { color: rgba(255,255,255,0.6); font-size: 12px; margin-top: 5px; }

    /* 2. Section grouping */
    .form-section { padding: 28px 40px; }
    .form-section + .form-section { border-top: 1px solid #F1F5F9; }

    .section-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
    .section-number {
        width: 26px; height: 26px; border-radius: 8px;
        background: #123457; color: #E8A33D;
        font-size: 11px; font-weight: 900;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .section-heading h3 { font-size: 13px; font-weight: 800; color: #123457; margin: 0; }
    .section-heading p { font-size: 11px; color: #94a3b8; margin: 2px 0 0; }

    /* 3. Form Styling */
    .input-field-wrapper { margin-bottom: 18px; }
    .label-command { display: block; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 8px; margin-left: 4px; }
    .input-group-custom { position: relative; display: flex; align-items: center; width: 100%; }
    .input-control-command { width: 100%; padding: 14px 20px 14px 50px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; font-size: 14px; font-weight: 700; color: #123457; transition: all 0.3s ease; font-family: inherit; }
    .input-control-command:focus { outline: none; border-color: #123457; background: #ffffff; box-shadow: 0 0 0 4px rgba(18, 52, 87, 0.05); }
    .input-icon-left { position: absolute; left: 18px; color: #94a3b8; font-size: 1.1rem; z-index: 5; pointer-events: none; }

    .input-color-custom { padding: 8px 10px 8px 50px !important; height: 52px; cursor: pointer; }
    .input-file-custom { padding: 12px 20px 12px 50px !important; font-size: 11px !important; }

    /* 4. ICON PICKER ala Emoji Keyboard */
    .icon-picker-trigger {
        width: 100%; display: flex; align-items: center; gap: 12px; padding: 12px 16px;
        background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; cursor: pointer; transition: 0.25s ease;
    }
    .icon-picker-trigger:hover { border-color: #E8A33D; }
    .icon-picker-trigger.is-open { border-color: #123457; box-shadow: 0 0 0 4px rgba(18, 52, 87, 0.05); background: #ffffff; }

    .icon-picker-preview {
        width: 40px; height: 40px; border-radius: 10px; background: #123457; color: #E8A33D;
        display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;
    }

    .icon-picker-wrap { position: relative; }
    .icon-picker-panel {
        position: absolute; top: calc(100% + 8px); left: 0; right: 0; z-index: 30;
        background: #ffffff; border: 1.5px solid #E2E8F0; border-radius: 16px;
        box-shadow: 0 20px 40px rgba(10, 30, 51, 0.12); padding: 14px; display: none;
    }
    .icon-picker-panel.is-open { display: block; }
    .icon-picker-grid { display: grid; grid-template-columns: repeat(8, 1fr); gap: 8px; max-height: 220px; overflow-y: auto; }
    @media (max-width: 480px) { .icon-picker-grid { grid-template-columns: repeat(6, 1fr); } }

    .icon-item {
        aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center;
        background: #F8FAFC; border-radius: 10px; border: 1px solid #E2E8F0; cursor: pointer;
        transition: all 0.2s; font-size: 1.15rem; color: #94a3b8;
    }
    .icon-item:hover { border-color: #E8A33D; color: #E8A33D; transform: scale(1.08); }
    .icon-item.active { background: #123457; border-color: #123457; color: #E8A33D; box-shadow: 0 4px 10px rgba(18, 52, 87, 0.2); }

    /* 5. Action Buttons */
    .form-footer { padding: 25px 40px; background: #F8FAFC; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 12px; }
    .btn-command-cancel { padding: 12px 24px; border-radius: 12px; font-size: 12px; font-weight: 700; color: #475569; background: #e2e8f0; border: 1px solid #cbd5e1; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; }
    .btn-command-save{
        padding: 12px 32px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 800;
        color: #ffffff;
        background: #123457;
        border: none;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all .3s ease;
        box-shadow: 0 10px 15px -3px rgba(18,52,87,.2);
    }

    /* Hover Tombol Batalkan */
    .btn-command-cancel:hover{
        background: #D1D5DB;
        color: #1E293B;
        transform: translateY(-1px);
    }

    /* Hover Tombol Daftarkan */
    .btn-command-save:hover{
        background: #E8A33D;
        color: #0A1E33;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(232,163,61,.25);
    }

    /* Efek klik */
    .btn-command-cancel:active,
    .btn-command-save:active{
        transform: translateY(0);
    }

    /* --- 3. CUSTOM FILE UPLOAD --- */
    .file-upload-wrapper { position: relative; width: 100%; cursor: pointer; }
    .file-upload-box { display: flex; align-items: center; gap: 12px; padding: 10px 16px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; transition: all 0.3s ease; height: 48px; }
    .file-upload-wrapper:hover .file-upload-box { border-color: #E8A33D; background: #fff; }
    .file-icon { width: 32px; height: 32px; border-radius: 8px; background: #123457; color: #E8A33D; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .file-text { font-size: 11px; font-weight: 700; color: #94a3b8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .file-input-real { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10; }
</style>

<div class="command-card shadow-sm">
    <div class="command-header">
        <div class="flex items-center justify-between">
            <div>
                <h2>Registrasi Layanan Iframe</h2>
                <p>Otoritas pendaftaran aplikasi eksternal ke dalam ekosistem portal Sukabumi One Access.</p>
            </div>
            <div class="hidden md:flex w-12 h-12 rounded-xl bg-white/10 items-center justify-center text-accent border border-white/10">
                <i class="bi bi-window-fullscreen text-xl"></i>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.layanan.iframe.store', $portalCode) }}" method="POST" enctype="multipart/form-data" id="instansiForm">
        @csrf
        
        <input type="hidden" name="instansi_id" value="{{ $instansi->id }}">
        <input type="hidden" name="jenis" value="iframe">
        <input type="hidden" name="status" value="draft">
        <input type="hidden" name="secondary_color" value="#E8A33D">

        {{-- ========== SECTION 1: INFORMASI DASAR ========== --}}
        <div class="form-section">
            <div class="section-heading">
                <span class="section-number">1</span>
                <div>
                    <h3>Informasi Layanan</h3>
                    <p>Judul layanan dan narasi singkat yang akan muncul pada direktori.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
                <div class="input-field-wrapper md:col-span-2">
                    <label class="label-command">Judul Layanan</label>
                    <div class="input-group-custom">
                        <input type="text" name="nama" class="input-control-command" placeholder="Contoh: E-BPHTB Online" required>
                        <i class="bi bi-tag input-icon-left"></i>
                    </div>
                </div>

                <div class="input-field-wrapper md:col-span-2">
                    <label class="label-command">Deskripsi Singkat (Card Caption)</label>
                    <div class="relative">
                        <textarea name="deskripsi" rows="3" class="input-control-command !pl-5" style="padding-top: 14px; height: auto;" placeholder="Tuliskan keterangan fungsi layanan ini..." required></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== SECTION 2: IDENTITAS VISUAL ========== --}}
        <div class="form-section">
            <div class="section-heading">
                <span class="section-number">2</span>
                <div>
                    <h3>Visual & Branding</h3>
                    <p>Pilih ikon dan warna aksen sebagai identitas visual layanan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
                <div class="input-field-wrapper">
                    <label class="label-command">Warna Aksen Ikon</label>
                    <div class="input-group-custom">
                        <input type="color" name="primary_color" class="input-control-command input-color-custom" value="#123457">
                        <i class="bi bi-palette input-icon-left"></i>
                    </div>
                </div>

                <div class="input-field-wrapper">
                    <label class="label-command">Ikon Visual (Badge)</label>
                    <input type="hidden" name="icon" id="selected_icon_value" value="bi-globe2">
                    <div class="icon-picker-wrap">
                        <button type="button" class="icon-picker-trigger" id="iconPickerTrigger" onclick="toggleIconPicker()">
                            <span class="icon-picker-preview" id="iconPickerPreview"><i class="bi bi-globe2"></i></span>
                            <span class="text-xs font-bold text-slate-500 flex-1 text-left">Klik untuk pilih ikon</span>
                            <i class="bi bi-chevron-down text-slate-400 text-xs" id="iconPickerChevron"></i>
                        </button>
                        <div class="icon-picker-panel" id="iconPickerPanel">
                            <div class="icon-picker-grid">
                                @foreach($icons as $icon)
                                    <div class="icon-item {{ $icon == 'bi-globe2' ? 'active' : '' }}" onclick="pickIcon('{{ $icon }}', this)">
                                        <i class="bi {{ $icon }}"></i>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="input-field-wrapper md:col-span-2">
                    <label class="label-command">Upload Foto Sampul (Card Background)</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="cover" id="in_img" class="file-input-real" accept="image/*" required>
                        <div class="file-upload-box">
                            <div class="file-icon">
                                <i class="bi bi-image"></i>
                            </div>
                            <span class="file-text" id="file_name_display">
                                Pilih Foto Sampul...
                            </span>
                        </div>
                    </div>
                    <p class="mt-2 ml-1 text-[10px] font-medium text-slate-400">
                        Format: PNG, JPG, JPEG, WEBP • Maksimal 2 MB
                    </p>
                </div>
            </div>
        </div>

        {{-- ========== SECTION 4: KONFIGURASI LINK ========== --}}
        <div class="form-section">
            <div class="section-heading">
                <span class="section-number">3</span>
                <div>
                    <h3>Konfigurasi Jembatan (Bridge)</h3>
                    <p>Alamat URL aplikasi eksternal yang akan diintegrasikan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-x-8">
                <div class="input-field-wrapper">
                    <label class="label-command">URL Aplikasi Eksternal</label>
                    <div class="input-group-custom">
                        <input type="url" name="iframe_url" class="input-control-command" placeholder="https://aplikasi-dinas.sukabumikota.go.id" required>
                        <i class="bi bi-link-45deg input-icon-left"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('admin.layanan.iframe.manage', [$portalCode, $instansi_slug]) }}" class="btn-command-cancel">
                Batalkan
            </a>
            <button type="submit" class="btn-command-save">
                Daftarkan Layanan 
            </button>
        </div>
    </form>
</div>

<x-admin.modal-form-errors />

@push('scripts')
<script>
    // --- 1. ICON PICKER: TOGGLE PANEL ---
    function toggleIconPicker() {
        const panel = document.getElementById('iconPickerPanel');
        const trigger = document.getElementById('iconPickerTrigger');
        const chevron = document.getElementById('iconPickerChevron');
        panel.classList.toggle('is-open');
        trigger.classList.toggle('is-open');
        chevron.classList.toggle('bi-chevron-down');
        chevron.classList.toggle('bi-chevron-up');
    }

    // --- 2. ICON PICKER: PILIH IKON ---
    function pickIcon(iconName, element) {
        document.getElementById('selected_icon_value').value = iconName;
        document.querySelectorAll('.icon-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('iconPickerPreview').innerHTML = `<i class="bi ${iconName}"></i>`;
        toggleIconPicker();
    }

    // Tutup panel kalau klik di luar
    document.addEventListener('click', function (e) {
        const wrap = document.querySelector('.icon-picker-wrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('iconPickerPanel').classList.remove('is-open');
            document.getElementById('iconPickerTrigger').classList.remove('is-open');
            document.getElementById('iconPickerChevron').classList.remove('bi-chevron-up');
            document.getElementById('iconPickerChevron').classList.add('bi-chevron-down');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // LOADING ANIMATION
        document.getElementById('instansiForm').addEventListener('submit', function() {
            const btn = this.querySelector('.btn-command-save');
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses...';
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';
            btn.disabled = true;
        });
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

    document.querySelector('input[name="primary_color"]').addEventListener('input', function () {
        document.getElementById('iconPickerPreview').style.background = this.value;
    });
</script>
@endpush

@endsection