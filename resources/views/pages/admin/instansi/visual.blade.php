@extends('layouts.admin')

@section('title', 'Visual Identity')
@section('page-title', 'Visual Identity')
@section('page-icon', 'bi-palette-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
    // Daftar ikon diperbanyak, sama dengan halaman Tambah Instansi, biar pilihannya lengkap
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

    $defaultBadgeColor = '#123457';
    $defaultIconColor = '#E8A33D';
    $currentBadgeColor = old('primary_color', $instansi->primary_color) ?: $defaultBadgeColor;
    $currentIconColor = old( 'secondary_color', $instansi->secondary_color) ?: $defaultIconColor;
@endphp

<style>
    /* --- 1. CSS CARD (CLONE IDENTIK DARI HOME USER) --- */
    .service-card-preview{
        position: relative;
        width:100%;
        max-width:320px;
        margin:0 auto;
        background:#fff;
        border:1px solid #f1f5f9;
        border-radius:2rem;
        overflow:hidden;
        transition:all .3s cubic-bezier(.4,0,.2,1);
    }
    .service-card-preview:hover{
        transform:translateY(-5px);
        border-color:#E8A33D;
        box-shadow:0 25px 50px rgba(0,0,0,.12);
    }
    .dinas-card-img-wrapper { position: relative; width: 100%; height: 120px; overflow: hidden; border-radius: 1.5rem 1.5rem 0 0; }
    .dinas-card-img{
        width:100%;
        height:100%;
        object-fit:cover;
        transition:transform .6s ease;
        filter:brightness(.9) contrast(1.1);
    }

    .dinas-icon-badge-preview {
        position: absolute; top: 100px; right: 20px; z-index: 30;
        width: 45px; height: 45px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 15px rgba(0,0,0,0.1); border: 3px solid #fff;
        transition:all .3s ease;
    }

    /* --- 2. LAYOUT SPLIT --- */
    .command-card { background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden; }
    .form-section { padding: 30px; }
    .form-section + .form-section { border-top: 1px solid #F1F5F9; }

    .section-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
    .section-number { width: 26px; height: 26px; border-radius: 8px; background: #123457; color: #E8A33D; font-size: 11px; font-weight: 900; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .section-heading h3 { font-size: 13px; font-weight: 800; color: #123457; margin: 0; text-transform: uppercase; }

    /* --- 3. CUSTOM FILE UPLOAD --- */
    .file-upload-wrapper { position: relative; width: 100%; cursor: pointer; }
    .file-upload-box { display: flex; align-items: center; gap: 12px; padding: 10px 16px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; transition: all 0.3s ease; height: 48px; }
    .file-upload-wrapper:hover .file-upload-box { border-color: #E8A33D; background: #fff; }
    .file-icon { width: 32px; height: 32px; border-radius: 8px; background: #123457; color: #E8A33D; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .file-text { font-size: 11px; font-weight: 700; color: #94a3b8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .file-input-real { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10; }

    /* 3. INPUTS */
    .input-field-wrapper { margin-bottom: 18px; }
    .input-field-wrapper:last-child { margin-bottom: 0; }
    .label-command { display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 8px; }
    /* Versi label tanpa margin, dipakai saat label disandingkan dengan tombol reset di baris yang sama */
    .label-command-inline { font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; margin: 0; }
    .input-control-command { width: 100%; padding: 12px 16px 12px 45px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; font-size: 13px; font-weight: 700; color: #123457; transition: all 0.3s; }
    .input-control-command:focus { outline: none; border-color: #123457; background: #fff; }
    .input-icon-left { position: absolute; left: 16px; color: #cbd5e1; top: 50%; transform: translateY(-50%); }

    /* Tombol Reset Warna — ditaruh sebaris sama label */
    .btn-reset-color {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 9px; font-weight: 800; text-transform: uppercase;
        color: #94a3b8; background: transparent; border: none;
        cursor: pointer; transition: color 0.2s ease; padding: 0; line-height: 1;
    }
    .btn-reset-color i { font-size: 11px; }
    .btn-reset-color:hover { color: #E8A33D; }

    /* 4. ICON PICKER — gaya emoji keyboard: tombol trigger + panel popover (bukan grid statis) */
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

    .icon-picker-wrap { position: relative; }

    .icon-picker-panel {
        position: absolute; top: calc(100% + 8px); left: 0; right: 0; z-index: 30;
        background: #ffffff; border: 1.5px solid #E2E8F0; border-radius: 16px;
        box-shadow: 0 20px 40px rgba(10, 30, 51, 0.12); padding: 14px; display: none;
    }
    .icon-picker-panel.is-open { display: block; }

    .icon-picker-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(34px, 1fr)); gap: 8px; max-height: 220px; overflow-y: auto; }

    .icon-item {
        aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center;
        background: #F8FAFC; border-radius: 10px; border: 1px solid #E2E8F0; cursor: pointer;
        transition: all 0.2s; font-size: 1.15rem; color: #94a3b8;
    }
    .icon-item:hover { border-color: #E8A33D; color: #E8A33D; transform: scale(1.08); }
    .icon-item.active { background: #123457; border-color: #123457; color: #E8A33D; box-shadow: 0 4px 10px rgba(18, 52, 87, 0.2); }

    .sticky-preview { position: sticky; top: 110px; }

    /* Action Footer */
    .form-footer{
        padding: 28px 30px;
        background: #F8FAFC;
        border-top: 1px solid #E2E8F0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    /* Secondary Button */
    .btn-command-cancel{
        padding: 12px 24px;
        border-radius: 14px;
        color: #475569; /* Slate 600 */
        background: #e2e8f0; /* Slate 200 (Warna Abu-abu Lembut) */
        border: 1px solid #cbd5e1;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        transition: all .3s ease;
    }

    /* Primary Button */
    .btn-command-save{
        padding: 12px 32px;
        border-radius: 14px;
        background: #123457;
        color: #FFFFFF;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
        transition: all .3s ease;
    }

    .btn-command-cancel:active,
    .btn-command-save:active{
        transform: translateY(0);
    }

    /* Hover tombol Batalkan */
    .btn-command-cancel:hover {
        background: #d1d5db;
        color: #1e293b;
        transform: translateY(-1px);
    }

    /* Hover tombol Simpan */
    .btn-command-save:hover {
        background: #E8A33D;
        color: #0A1E33;
        transform: translateY(-2px);
    }

    .service-card-preview:hover .dinas-card-img{
        transform:scale(1.08);
    }

    .service-card-preview:hover .dinas-icon-badge-preview{
        transform:scale(1.1);
    }

    .service-card-preview:hover #pv_nama{
        color:#123457;
    }

    .service-card-preview:hover .preview-arrow{
        color:#123457;
    }
</style>

<div class="max-w-7xl mx-auto">
    <div class="grid lg:grid-cols-12 gap-8">

        <!-- SISI KIRI: FORM PENGISIAN -->
        <div class="lg:col-span-7">
            <div class="command-card shadow-sm">
                <div class="bg-[#0A1E33] p-6 border-b-4 border-accent">
                    <h2 class="text-white font-black text-lg uppercase tracking-tight">Visual Customizer</h2>
                    <p class="text-white/50 text-xs mt-1">Sesuaikan tampilan kartu instansi Anda secara real-time.</p>
                </div>
                <form id="visualIdentityForm" action="{{ route('admin.instansi.visual.update', [$portalCode, $instansi->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    {{-- SECTION 1: INFORMASI KARTU --}}
                    <div class="form-section">
                        <div class="section-heading">
                            <span class="section-number">1</span>
                            <h3>Informasi Kartu</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="input-field-wrapper mb-0">
                                <label class="label-command">Nama Instansi</label>
                                <div class="relative">
                                    <input type="text" id="in_nama" name="name" value="{{ old('nama', $instansi->nama) }}" class="input-control-command" placeholder="Contoh: Dinas Kesehatan">
                                    <i class="bi bi-building input-icon-left"></i>
                                </div>
                            </div>
                            
                            <div class="input-field-wrapper mb-0">
                                <label class="label-command">Keterangan Singkat</label>
                                <div class="relative">
                                    <input type="text" id="in_desc" name="desc" value="{{ old('deskripsi_singkat', $instansi->deskripsi_singkat) }}" class="input-control-command" placeholder="Contoh: Pelayanan Kesehatan">
                                    <i class="bi bi-tag input-icon-left"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: VISUAL & BRANDING --}}
                    <div class="form-section">
                        <div class="section-heading">
                            <span class="section-number">2</span>
                            <h3>Visual & Branding</h3>
                        </div>

                        {{-- Baris 1: Warna Badge & Warna Ikon, masing-masing dengan tombol reset di sebelah label --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="input-field-wrapper mb-0">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="label-command-inline">Warna Badge</label>
                                    <button type="button" id="btnResetBadge" class="btn-reset-color" title="Reset ke Default">
                                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                                    </button>
                                </div>
                                <input type="color" id="in_color" name="color" value="{{ $currentBadgeColor }}"
                                       class="w-full h-[48px] p-1 bg-[#F8FAFC] border-1.5 border-[#E2E8F0] rounded-xl cursor-pointer">
                                <p class="text-[9px] text-slate-400 mt-2 font-bold uppercase tracking-wider">Warna latar kotak badge</p>
                            </div>

                            <div class="input-field-wrapper mb-0">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="label-command-inline">Warna Ikon</label>
                                    <button type="button" id="btnResetIconColor" class="btn-reset-color" title="Reset ke Default">
                                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                                    </button>
                                </div>
                                <input type="color" id="in_icon_color" name="secondary_color" value="{{ old('secondary_color', $instansi->secondary_color ?? $defaultIconColor) }}"
                                       class="w-full h-[48px] p-1 bg-[#F8FAFC] border-1.5 border-[#E2E8F0] rounded-xl cursor-pointer">
                                <p class="text-[9px] text-slate-400 mt-2 font-bold uppercase tracking-wider">Warna ikon di dalam badge</p>
                            </div>
                        </div>

                        {{-- Baris 2: Foto Background & Ikon Badge sejajar --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="input-field-wrapper mb-0">
                                <label class="label-command">Foto Background</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="in_img" name="image" class="file-input-real" accept="image/*">
                                    <div class="file-upload-box h-[48px]">
                                        <div class="file-icon"><i class="bi bi-image"></i></div>
                                        <span class="file-text" id="file_name_display">Ganti Foto...</span>
                                    </div>
                                </div>
                                <p class="text-[9px] text-slate-400 mt-2 font-bold uppercase tracking-wider">Rekomendasi: 800x400px</p>
                            </div>

                            <div class="input-field-wrapper mb-0">
                                <label class="label-command">Ikon Badge</label>
                                <input type="hidden" name="icon" id="selected_icon_value" value="{{ $instansi->icon }}">

                                <div class="icon-picker-wrap">
                                    <button type="button" class="icon-picker-trigger h-[48px]" id="iconPickerTrigger" onclick="toggleIconPicker()">
                                        <span class="icon-picker-preview" id="iconPickerPreview" style="width:32px;height:32px;">
                                            <i class="bi {{ $instansi->icon }}"></i>
                                        </span>
                                        <span class="text-[11px] font-black text-slate-700 flex-1 text-left truncate" id="selected_icon_name">{{ $instansi->icon }}</span>
                                        <i class="bi bi-chevron-down text-slate-400 text-xs" id="iconPickerChevron"></i>
                                    </button>

                                    <div class="icon-picker-panel" id="iconPickerPanel">
                                        <div class="icon-picker-grid">
                                            @foreach($icons as $icon)
                                                <div class="icon-item {{ $icon == $instansi['icon'] ? 'active' : '' }}" onclick="pickIcon('{{ $icon }}', this)">
                                                    <i class="bi {{ $icon }}"></i>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <a href="{{ route('admin.instansi.index', $portalCode) }}" class="btn-command-cancel">
                            Batal
                        </a>
                        <button type="submit" class="btn-command-save">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SISI KANAN: LIVE PREVIEW (IDENTIK HOME) -->
        <div class="lg:col-span-5">
            <div class="sticky-preview">
                <div class="text-center mb-6">
                    <span class="bg-primary/5 text-primary text-[10px] font-black px-4 py-2 rounded-full border border-primary/10 uppercase tracking-widest">
                        <i class="bi bi-eye-fill mr-1"></i> Live Preview
                    </span>
                </div>

                <div class="service-card-preview">
                    <div class="dinas-card-img-wrapper">
                        <img id="pv_img" src="{{ asset('storage/' . $instansi->cover) }}" class="dinas-card-img" alt="Preview">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div>
                    </div>

                    <div id="pv_badge" class="dinas-icon-badge-preview" style="background: {{ $currentBadgeColor }};"> 
                        <i id="pv_icon" class="bi {{ $instansi->icon }} text-xl" style="color: {{ $currentIconColor }};"></i> 
                    </div>

                    <div class="p-5 pt-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1 pr-4">
                                <h3 id="pv_nama" class="text-base font-bold text-slate-800 transition-colors leading-tight">{{ $instansi->nama }}</h3>
                                <p id="pv_desc" class="text-[10px] font-bold text-accent uppercase tracking-widest mt-1.5">{{ $instansi->deskripsi_singkat }}</p>
                            </div>
                            <i class="bi bi-arrow-up-right preview-arrow text-slate-300 transition-all"></i>
                        </div>
                    </div>
                </div>

                <div class="mt-8 p-6 bg-slate-50 rounded-[2rem] border border-slate-100">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 rounded-full bg-accent/10 flex items-center justify-center text-accent flex-shrink-0">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black text-slate-700 uppercase tracking-widest mb-1">Tips Branding</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed font-medium">
                                Gunakan <b>Warna Ikon</b> yang kontras dengan <b>Warna Badge</b> supaya tetap gampang dibaca. Foto latar sebaiknya memiliki area gelap di bagian bawah agar teks putih tetap terbaca.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // --- LIVE UPDATES LOGIC ---

    // 1. Update Nama
    document.getElementById('in_nama').addEventListener('input', function() {
        document.getElementById('pv_nama').innerText = this.value || 'Nama Instansi';
    });

    // 2. Update Deskripsi
    document.getElementById('in_desc').addEventListener('input', function() {
        document.getElementById('pv_desc').innerText = this.value || 'Keterangan';
    });

    // 3. Update Warna Badge (cuma background badge)
    document.getElementById('in_color').addEventListener('input', function() {
        document.getElementById('pv_badge').style.background = this.value;
    });

    // 3b. Update Warna Ikon (sekarang bebas dipilih sendiri, gak dipaksa emas lagi)
    document.getElementById('in_icon_color').addEventListener('input', function() {
        document.getElementById('pv_icon').style.color = this.value;
    });

    // 4. Update Gambar (Background)
    document.getElementById('in_img').onchange = function() {
        const [file] = this.files;
        if (file) {
            document.getElementById('file_name_display').innerText = file.name;
            document.getElementById('pv_img').src = URL.createObjectURL(file);
        }
    };
    
    // --- 5. ICON PICKER: TOGGLE PANEL (gaya emoji keyboard) ---
    function toggleIconPicker() {
        document.getElementById('iconPickerPanel').classList.toggle('is-open');
        document.getElementById('iconPickerTrigger').classList.toggle('is-open');
        document.getElementById('iconPickerChevron').classList.toggle('bi-chevron-down');
        document.getElementById('iconPickerChevron').classList.toggle('bi-chevron-up');
    }

    // Tutup panel kalau klik di luar area picker
    document.addEventListener('click', function (e) {
        const wrap = document.querySelector('.icon-picker-wrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('iconPickerPanel').classList.remove('is-open');
            document.getElementById('iconPickerTrigger').classList.remove('is-open');
            document.getElementById('iconPickerChevron').classList.remove('bi-chevron-up');
            document.getElementById('iconPickerChevron').classList.add('bi-chevron-down');
        }
    });

    // Tutup panel dengan tombol Esc
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const panel = document.getElementById('iconPickerPanel');
            if (panel && panel.classList.contains('is-open')) {
                toggleIconPicker();
            }
        }
    });

    // --- 6. ICON PICKER: PILIH IKON (sekaligus update live preview kartu) ---
    function pickIcon(iconName, element) {
        // Simpan ke field form supaya benar-benar terkirim saat submit
        document.getElementById('selected_icon_value').value = iconName;

        // Tandai aktif di grid
        document.querySelectorAll('.icon-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        // Update preview di tombol trigger
        document.getElementById('iconPickerPreview').innerHTML = `<i class="bi ${iconName}"></i>`;
        document.getElementById('selected_icon_name').innerText = iconName;

        // Update live preview kartu instansi di sisi kanan
        // BUGFIX: dulu warna icon dipaksa emas (#E8A33D) di sini, sekarang ngikutin
        // input "Warna Ikon" yang sudah bisa diatur sendiri.
        const icon = document.getElementById('pv_icon');
        icon.className = `bi ${iconName} text-xl`;
        icon.style.color = document.getElementById('in_icon_color').value;

        // Otomatis tutup panel begitu ikon dipilih, kayak emoji picker beneran
        toggleIconPicker();
    }

    // 7. Reset Warna ke Default
    const DEFAULT_BADGE = '#123457';
    const DEFAULT_ICON = '#E8A33D';

    document.getElementById('btnResetBadge').addEventListener('click', function () {
        document.getElementById('in_color').value = DEFAULT_BADGE;
        document.getElementById('pv_badge').style.background = DEFAULT_BADGE;
    });

    document.getElementById('btnResetIconColor').addEventListener('click', function () {
        document.getElementById('in_icon_color').value = DEFAULT_ICON;
        document.getElementById('pv_icon').style.color = DEFAULT_ICON;
    });

    document.addEventListener('DOMContentLoaded', function () {

        const form = document.getElementById('visualIdentityForm');

        form.addEventListener('submit', function () {

            const btn = this.querySelector('.btn-command-save');

            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses...';
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';

        });

    });
</script>
@endpush

@endsection