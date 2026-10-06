@extends('layouts.admin')

@section('title', 'Tambah Kategori Baru')
@section('page-title', 'Registrasi Kategori')
@section('page-icon', 'bi-plus-circle-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
    
    // Sinkronisasi dengan Object Model
    $svcId = $svc->id;
    $namaLayanan = $svc->nama;

    // Daftar ikon lengkap
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
    /* --- 1. GLOBAL LAYOUT --- */
    .command-card { background: #ffffff; border-radius: 24px; border: 1px solid #eef2f6; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
    .form-header-custom { background: #0A1E33; padding: 24px; border-bottom: 4px solid #E8A33D; }
    .form-section { padding: 35px; }
    .form-section + .form-section { border-top: 1px solid #F1F5F9; }

    .section-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 25px; }
    .section-number { width: 28px; height: 28px; border-radius: 10px; background: #123457; color: #E8A33D; font-size: 11px; font-weight: 900; display: flex; align-items: center; justify-content: center; }
    .section-heading h3 { font-size: 13px; font-weight: 800; color: #123457; margin: 0; text-transform: uppercase; }

    /* --- 2. INPUT STYLING --- */
    .label-command { display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 8px; }
    .input-control-command { 
        width: 100%; padding: 12px 16px 12px 45px; background: #f8fafc; 
        border: 1.5px solid #e2e8f0; border-radius: 14px; font-size: 13px; 
        font-weight: 700; color: #123457; transition: all 0.3s ease; 
    }
    .input-control-command:focus { outline: none; border-color: #123457; background: #fff; }
    .input-icon-left { position: absolute; left: 16px; color: #cbd5e1; top: 50%; transform: translateY(-50%); font-size: 1.1rem; }

    /* --- 3. ICON PICKER TRIGGER --- */
    .icon-picker-trigger {
        width: 100%; display: flex; align-items: center; gap: 12px;
        padding: 12px 16px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px;
        cursor: pointer; transition: all 0.25s ease;
    }
    .icon-picker-trigger:hover { border-color: #E8A33D; }
    .icon-picker-preview {
        width: 40px; height: 40px; border-radius: 10px; background: #123457; color: #E8A33D;
        display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;
    }

    /* --- 4. ICON PICKER MODAL --- */
    #iconPickerModal { position: fixed; inset: 0; z-index: 200; display: none; align-items: center; justify-content: center; padding: 1.25rem; }
    #iconPickerModal.is-open { display: flex; }
    #iconPickerBackdrop { position: absolute; inset: 0; background: rgba(10, 30, 51, 0.5); backdrop-filter: blur(6px); opacity: 0; transition: opacity 0.25s ease; }
    #iconPickerModal.is-open #iconPickerBackdrop { opacity: 1; }
    #iconPickerCard { 
        position: relative; z-index: 2; width: 100%; max-width: 400px; 
        background: #ffffff; border-radius: 24px; box-shadow: 0 30px 60px rgba(10, 30, 51, 0.3);
        opacity: 0; transform: scale(0.92) translateY(12px); transition: 0.25s ease; overflow: hidden;
    }
    #iconPickerModal.is-open #iconPickerCard { opacity: 1; transform: scale(1) translateY(0); }
    .icon-picker-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 22px; background: #0A1E33; border-bottom: 4px solid #E8A33D; }
    .icon-picker-modal-header h4 { font-size: 13px; font-weight: 800; color: #ffffff; margin: 0; }
    .icon-picker-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px; padding: 18px 20px 22px; max-height: 320px; overflow-y: auto; }
    .icon-item {
        aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center;
        background: #F8FAFC; border-radius: 12px; border: 1px solid #E2E8F0; cursor: pointer;
        transition: all 0.2s; font-size: 1.2rem; color: #94a3b8;
    }
    .icon-item:hover, .icon-item.active { background: #123457; border-color: #123457; color: #E8A33D; transform: scale(1.06); }

    /* --- 5. LIVE PREVIEW CARD (MATCHING ADMIN STYLE) --- */
    .option-card-admin-pv {
        background: #ffffff; border: 1px solid #f1f5f9; border-radius: 2rem;
        display: flex; flex-direction: column; justify-content: space-between;
        height: 100%; position: relative; overflow: hidden; isolation: isolate;
        padding: 30px; text-align: left;
    }
    .side-indicator-pv { position: absolute; top: 0; left: 0; width: 5px; height: 100%; background: #E8A33D; z-index: 3; }
    .option-ghost-number-pv { position: absolute; top: -0.5rem; right: 0.5rem; font-size: 6rem; font-weight: 900; line-height: 1; color: #0F2A4A; opacity: 0.03; z-index: 0; pointer-events: none; }
    
    .option-icon-box-pv {
        width: 54px; height: 54px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; margin-bottom: 1.5rem;
        position: relative; z-index: 2; background: rgba(232, 163, 61, 0.1); color: #E8A33D;
    }
    .option-card-title-pv { font-size: 18px; font-weight: 800; color: #123457; margin-bottom: 10px; z-index: 2; position: relative; }
    .option-card-desc-pv { font-size: 11.5px; color: #94a3b8; line-height: 1.6; margin-bottom: 25px; z-index: 2; position: relative; min-height: 50px; }

    .card-admin-footer-pv { display: flex; align-items: center; justify-content: space-between; padding-top: 20px; border-top: 1px solid #f1f5f9; z-index: 2; position: relative; }
    
    /* Footer Elements */
    .status-select-pv { font-size: 9px; font-weight: 800; text-transform: uppercase; padding: 6px 12px; border-radius: 10px; background: #ecfdf5; color: #059669; }
    .btn-mini-action-pv { width: 36px; height: 36px; border-radius: 11px; display: flex; align-items: center; justify-content: center; background: #f8fafc; color: #cbd5e1; font-size: 1.1rem; }

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
        text-transform: uppercase;
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

<div class="max-w-7xl mx-auto pb-20">
    <div class="grid lg:grid-cols-12 gap-10">
        
        <!-- SISI KIRI: FORM -->
        <div class="lg:col-span-7">
            <div class="command-card shadow-sm">
                <div class="form-header-custom">
                    <h2 class="text-white font-black text-lg uppercase tracking-tight">Buat Kategori</h2>
                    <p class="text-white/50 text-[10px] mt-1 font-bold uppercase tracking-[0.2em]">Layanan: {{ $namaLayanan }}</p>
                </div>

                <form action="{{ route('admin.layanan.form.categories.store', [$portalCode, $svcId]) }}" method="POST" id="categoryForm">
                    @csrf
                    
                    <div class="form-section">
                        <div class="section-heading">
                            <span class="section-number">1</span>
                            <h3>Informasi Utama</h3>
                        </div>

                        <div class="space-y-6">
                            <div class="input-field-wrapper">
                                <label class="label-command">Judul Kategori</label>
                                <div class="relative">
                                    <input type="text" id="in_nama" name="nama" class="input-control-command" placeholder="Contoh: Pindah Dalam Kota" required autofocus>
                                    <i class="bi bi-tag input-icon-left"></i>
                                </div>
                            </div>

                            <div class="input-field-wrapper">
                                <label class="label-command">Deskripsi Kategori</label>
                                <div class="relative">
                                    <textarea id="in_deskripsi" name="deskripsi" rows="4" class="input-control-command !pl-4" style="height: auto; padding-top: 15px;" placeholder="Jelaskan secara singkat..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-heading">
                            <span class="section-number">2</span>
                            <h3>Branding Kategori</h3>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="label-command">Warna Aksen</label>
                                <div class="relative">
                                    <input type="color" id="in_primary_color" name="primary_color" value="#E8A33D" class="input-control-command !p-1.5 !h-[48px] cursor-pointer">
                                    <i class="bi bi-palette input-icon-left"></i>
                                </div>
                            </div>

                            <div>
                                <label class="label-command">Pilih Ikon</label>
                                <input type="hidden" name="icon" id="selected_icon_value" value="bi-geo-fill">
                                <button type="button" class="icon-picker-trigger" onclick="openIconPicker()">
                                    <span class="icon-picker-preview" id="iconPickerPreview">
                                        <i class="bi bi-geo-fill"></i>
                                    </span>
                                    <span class="text-xs font-bold text-slate-500 flex-1 text-left">Pilih Ikon...</span>
                                    <i class="bi bi-grid-3x3-gap text-slate-400 text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <a href="{{ route('admin.layanan.form.categories.index', [$portalCode, $svcId]) }}"
                            class="btn-command-cancel">
                            Batal
                        </a>

                        <button type="submit"
                            class="btn-command-save" id="btnSubmit">
                            Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SISI KANAN: PREVIEW (SAMA DENGAN ADMIN GRID) -->
        <div class="lg:col-span-5">
            <div class="sticky-preview">
                <div class="text-center mb-8">
                    <span class="bg-primary/5 text-primary text-[10px] font-black px-5 py-2.5 rounded-full border border-primary/10 uppercase tracking-widest">
                        <i class="bi bi-eye-fill mr-1"></i> Live Card Preview
                    </span>
                </div>

                <div class="option-card-admin-pv shadow-2xl shadow-primary/5">
                    <div class="side-indicator-pv" id="pv_indicator"></div>
                    <span class="option-ghost-number-pv">01</span>

                    <div>
                        <div class="option-icon-box-pv" id="pv_icon_box">
                            <i id="pv_icon" class="bi bi-geo-fill"></i>
                        </div>
                        <h3 class="option-card-title-pv" id="pv_title">Judul Kategori</h3>
                        <p class="option-card-desc-pv" id="pv_desc">Deskripsi yang Anda ketik akan muncul secara real-time di sini...</p>
                    </div>

                    <div class="card-admin-footer-pv">
                        <div class="status-select-pv">Aktif</div>
                        <div class="flex gap-2">
                            <div class="btn-mini-action-pv"><i class="bi bi-ui-checks"></i></div>
                            <div class="btn-mini-action-pv"><i class="bi bi-pencil-square"></i></div>
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
            <div>
                <h4>Pilih Ikon Badge</h4>
                <p>Visualisasi untuk kategori layanan</p>
            </div>
            <button type="button" class="icon-picker-close" onclick="closeIconPicker()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="icon-picker-grid">
            @foreach($icons as $icon)
                <div class="icon-item {{ $icon == 'bi-geo-fill' ? 'active' : '' }}" onclick="pickIcon('{{ $icon }}', this)">
                    <i class="bi {{ $icon }}"></i>
                </div>
            @endforeach
        </div>
    </div>
</div>

<x-admin.modal-form-errors />

@push('scripts')
<script>
    // Live Sync Input ke Preview Card
    document.getElementById('in_nama').oninput = function() { 
        document.getElementById('pv_title').innerText = this.value || 'Judul Kategori'; 
    };
    
    document.getElementById('in_deskripsi').oninput = function() { 
        document.getElementById('pv_desc').innerText = this.value || 'Deskripsi kategori...'; 
    };
    
    document.getElementById('in_primary_color').oninput = function() { 
        const hex = this.value;
        const iconBox = document.getElementById('pv_icon_box');
        const indicator = document.getElementById('pv_indicator');
        
        iconBox.style.color = hex;
        iconBox.style.backgroundColor = hex + '1A'; // Background transparan 10%
        indicator.style.backgroundColor = hex;
    };

    function openIconPicker() {
        document.getElementById('iconPickerModal').classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    function closeIconPicker() {
        document.getElementById('iconPickerModal').classList.remove('is-open');
        document.body.style.overflow = '';
    }
    function pickIcon(iconName, element) {
        document.getElementById('selected_icon_value').value = iconName;
        document.querySelectorAll('.icon-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('iconPickerPreview').innerHTML = `<i class="bi ${iconName}"></i>`;
        document.getElementById('pv_icon').className = `bi ${iconName}`;
        closeIconPicker();
    }

    document.getElementById('categoryForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');

        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses...';
        btn.style.opacity = '0.8';
        btn.style.cursor = 'not-allowed';
    });
</script>
@endpush

@endsection