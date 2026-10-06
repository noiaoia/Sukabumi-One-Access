@extends('layouts.admin')

@section('title', 'Visual Direktori Layanan')
@section('page-title', 'Visual Direktori')
@section('page-icon', 'bi-window-stack')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
    // Daftar ikon lengkap sama dengan halaman identity biar konsisten
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

    $defaultBgColor = '#123457';
    $defaultIconColor = '#E8A33D';
@endphp

<style>
    /* --- 1. CSS CARD DIREKTORI (CLONE IDENTIK DARI USER SIDE) --- */
    .preview-container-layanan { width: 100%; max-width: 340px; margin: 0 auto; }
    
    .dinas-card-premium-preview {
        background: #ffffff; border: 1px solid #f1f5f9; border-radius: 2rem;
        position: relative; height: 100%; display: flex; flex-direction: column;
        transition: all 0.4s ease; overflow: hidden;
    }

    .img-frame-premium-preview { height: 120px; overflow: hidden; border-radius: 2rem 2rem 0 0; position: relative; z-index: 1; }
    .dinas-img-premium-preview { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.85); transition: 0.6s; }
    
    /* FIX: BADGE MELAYANG DI TENGAH GARIS POTONG */
    .icon-badge-floating-preview {
        position: absolute; z-index: 30; top: 100px; right: 20px;
        width: 46px; height: 46px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 3px solid #ffffff;
        transition: all 0.3s ease;
    }

    /* --- 2. ADMIN PANEL STYLING (SPLIT LAYOUT) --- */
    .command-card { background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden; }
    .form-section { padding: 30px; }
    .form-section + .form-section { border-top: 1px solid #F1F5F9; }
    .section-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
    .section-number { width: 26px; height: 26px; border-radius: 8px; background: #123457; color: #E8A33D; font-size: 11px; font-weight: 900; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .section-heading h3 { font-size: 13px; font-weight: 800; color: #123457; margin: 0; text-transform: uppercase; }

    /* INPUT STYLING */
    .input-field-wrapper { margin-bottom: 18px; }
    .input-field-wrapper:last-child { margin-bottom: 0; }
    .label-command { display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 8px; }
    /* Versi label tanpa margin, dipakai saat label disandingkan dengan tombol reset di baris yang sama */
    .label-command-inline { font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; margin: 0; }
    .input-control-command { width: 100%; padding: 12px 16px 12px 45px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; font-size: 13px; font-weight: 700; color: #123457; transition: 0.3s; }
    .input-control-command:focus { outline: none; border-color: #123457; background: #fff; }
    .input-icon-left { position: absolute; left: 16px; color: #cbd5e1; top: 50%; transform: translateY(-50%); }

    /* Tombol Reset Warna */
    .btn-reset-color {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 9px; font-weight: 800; text-transform: uppercase;
        color: #94a3b8; background: transparent; border: none;
        cursor: pointer; transition: color 0.2s ease; padding: 0; line-height: 1;
    }
    .btn-reset-color i { font-size: 11px; }
    .btn-reset-color:hover { color: #E8A33D; }

    /* --- 3. CUSTOM FILE UPLOAD --- */
    .file-upload-wrapper { position: relative; width: 100%; cursor: pointer; }
    .file-upload-box { display: flex; align-items: center; gap: 12px; padding: 10px 16px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; transition: all 0.3s ease; height: 48px; }
    .file-upload-wrapper:hover .file-upload-box { border-color: #E8A33D; background: #fff; }
    .file-icon { width: 32px; height: 32px; border-radius: 8px; background: #123457; color: #E8A33D; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .file-text { font-size: 11px; font-weight: 700; color: #94a3b8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .file-input-real { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10; }

    /* 3. ICON PICKER POP OVER (Gaya Emoji Keyboard) */
    .icon-picker-wrap { position: relative; }
    .icon-picker-trigger {
        width: 100%; display: flex; align-items: center; gap: 12px;
        padding: 12px 16px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px;
        cursor: pointer; transition: 0.25s ease;
    }
    .icon-picker-trigger:hover { border-color: #E8A33D; }
    .icon-picker-trigger.is-open { border-color: #123457; box-shadow: 0 0 0 4px rgba(18, 52, 87, 0.05); background: #ffffff; }

    .icon-picker-preview {
        width: 40px; height: 40px; border-radius: 10px; background: #123457; color: #E8A33D;
        display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;
    }

    .icon-picker-panel {
        position: absolute; top: calc(100% + 8px); left: 0; right: 0; z-index: 30;
        background: #ffffff; border: 1.5px solid #E2E8F0; border-radius: 16px;
        box-shadow: 0 20px 40px rgba(10, 30, 51, 0.12); padding: 14px; display: none;
    }
    .icon-picker-panel.is-open { display: block; }
    /* BUGFIX/IMPROVEMENT: pakai auto-fill supaya jumlah kolom otomatis menyesuaikan
       lebar panel — soalnya sekarang Ikon Visual cuma separuh lebar form (sejajar
       dengan Foto Sampul), jadi grid tetap rapi walau kolomnya jadi lebih sempit. */
    .icon-picker-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(34px, 1fr)); gap: 8px; max-height: 220px; overflow-y: auto; }

    .icon-item {
        aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center;
        background: #F8FAFC; border-radius: 10px; border: 1px solid #E2E8F0; cursor: pointer;
        transition: 0.2s; font-size: 1.15rem; color: #94a3b8;
    }
    .icon-item:hover { border-color: #E8A33D; color: #E8A33D; transform: scale(1.08); }
    .icon-item.active { background: #123457; border-color: #123457; color: #E8A33D; box-shadow: 0 4px 10px rgba(18, 52, 87, 0.2); }

    .sticky-preview { position: sticky; top: 110px; }

    /* Footer */
    .form-footer{
        padding: 25px 40px;
        background: #F8FAFC;
        border-top: 1px solid #E2E8F0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    /* Tombol Kembali */
    .btn-command-cancel{
        padding: 12px 24px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 700;
        color: #475569; /* Slate 600 */
        background: #e2e8f0; /* Slate 200 (Warna Abu-abu Lembut) */
        border: 1px solid #cbd5e1;
        transition: all .3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-command-cancel:hover{
        background: #D1D5DB;
        color: #1E293B;
        transform: translateY(-1px);
    }

    /* Tombol Simpan */
    .btn-command-save{
        padding: 12px 32px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 800;
        color: #FFFFFF;
        background: #123457;
        border: none;
        box-shadow: 0 10px 15px -3px rgba(18,52,87,.2);
        transition: all .3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
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
                    <h2 class="text-white font-black text-lg uppercase tracking-tight">Directory Card Customizer</h2>
                    <p class="text-white/50 text-xs mt-1">Sesuaikan tampilan kartu Anda pada halaman Direktori Layanan.</p>
                </div>

                <form action="{{ route('admin.instansi.visual-layanan.update', [$portalCode, $instansi->id]) }}" method="POST" enctype="multipart/form-data" id="visualLayananForm">
                    @csrf
                    @method('PUT')
                    {{-- SECTION 1 --}}
                    <div class="form-section">
                        <div class="section-heading">
                            <span class="section-number">1</span>
                            <h3>Informasi Utama</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="input-field-wrapper mb-0">
                                <label class="label-command">Nama Instansi</label>
                                <div class="relative">
                                    <input type="text" id="in_nama" name="name" value="{{ $instansi->nama }}" class="input-control-command">
                                    <i class="bi bi-building input-icon-left"></i>
                                </div>
                            </div>
                            <div class="input-field-wrapper mb-0">
                                <label class="label-command">Kategori Layanan</label>
                                <div class="relative">
                                    <input type="text" id="in_cat" name="cat" value="{{ $instansi->kategori }}" class="input-control-command">
                                    <i class="bi bi-grid-1x2 input-icon-left"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2 --}}
                    <div class="form-section">
                        <div class="section-heading">
                            <span class="section-number">2</span>
                            <h3>Media & Warna</h3>
                        </div>

                        {{-- Baris 1: Primary Color & Accent Color, tombol reset di sebelah judul --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="input-field-wrapper mb-0">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="label-command-inline">Primary Color</label>
                                    <button type="button" class="btn-reset-color" onclick="resetColor('in_bg_color', '{{ $defaultBgColor }}')">
                                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                                    </button>
                                </div>
                                <input type="color" id="in_bg_color" name="primary_color" value="{{ $instansi->primary_color ?? $defaultBgColor }}" class="input-control-command !p-1 !h-[48px]">
                                <p class="text-[9px] text-slate-400 mt-2 font-bold uppercase">
                                    Warna Background Badge
                                </p>
                            </div>
                            <div class="input-field-wrapper mb-0">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="label-command-inline">Accent Color</label>
                                    <button type="button" class="btn-reset-color" onclick="resetColor('in_icon_color', '{{ $defaultIconColor }}')">
                                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                                    </button>
                                </div>
                                <input type="color" id="in_icon_color" name="secondary_color" value="{{ $instansi->secondary_color ?? $defaultIconColor }}" class="input-control-command !p-1 !h-[48px]">
                                <p class="text-[9px] text-slate-400 mt-2 font-bold uppercase">
                                    Warna Ikon Badge
                                </p>
                            </div>
                        </div>

                        {{-- Baris 2: Foto Sampul & Ikon Visual sejajar dalam satu baris --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="input-field-wrapper mb-0">
                                <label class="label-command">Foto Sampul</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="in_img" name="image" class="file-input-real" accept="image/*">
                                    <div class="file-upload-box">
                                        <div class="file-icon"><i class="bi bi-image"></i></div>
                                        <span class="file-text" id="file_name_display">Ganti Foto...</span>
                                    </div>
                                </div>
                            </div>

                            <div class="input-field-wrapper mb-0">
                                <label class="label-command">Ikon Visual</label>
                                <input type="hidden" name="icon" id="selected_icon_value" value="{{ $instansi->icon }}">

                                <div class="icon-picker-wrap">
                                    <button type="button" class="icon-picker-trigger" id="iconPickerTrigger" onclick="toggleIconPicker()">
                                        <span class="icon-picker-preview" id="iconPickerPreview">
                                            <i class="bi {{ $instansi->icon }}"></i>
                                        </span>
                                        <span class="text-xs font-bold text-slate-500 flex-1 text-left truncate">Pilih ikon</span>
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

        <!-- SISI KANAN: LIVE PREVIEW (IDENTIK DIREKTORI LAYANAN) -->
        <div class="lg:col-span-5">
            <div class="sticky-preview">
                <div class="text-center mb-6">
                    <span class="bg-accent/10 text-accent-dark text-[10px] font-black px-5 py-2 rounded-full border border-accent/20 uppercase tracking-[0.2em]">
                        Directory Card Preview
                    </span>
                </div>

                <!-- CARD STRUCTURE (IDENTIK DIREKTORI USER) -->
                <div class="preview-container-layanan">
                    <div class="dinas-card-premium-preview shadow-2xl shadow-primary/10">
                        {{-- 1. Image Frame --}}
                        <div class="img-frame-premium-preview">
                            <img id="pv_img" src="{{ $instansi->cover ? asset('storage/'.$instansi->cover) : asset('images/default-cover.jpg') }}"
                            class="dinas-img-premium-preview">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        </div>

                        {{-- 2. Icon Badge (Floating di Garis Potong 100px) --}}
                        <div id="pv_badge" class="icon-badge-floating-preview" style="background-color: {{ $instansi->primary_color ?? $defaultBgColor }};">
                            <i id="pv_icon" class="bi {{ $instansi->icon }} text-xl" style="color: {{ $instansi->secondary_color ?? $defaultIconColor }}"> </i>
                        </div>

                        {{-- 3. Content --}}
                        <div class="p-4 sm:p-7 pt-5 sm:pt-8 bg-white">
                            <h3 id="pv_nama" class="text-sm sm:text-lg font-black text-primary-dark leading-tight truncate">
                                {{ $instansi->nama }}
                            </h3>
                            <p id="pv_cat" class="text-[9px] sm:text-[11px] font-bold text-accent uppercase tracking-widest mt-1">
                                {{ $instansi->kategori }}
                            </p>
                            
                            <div class="mt-4 flex items-center gap-2 text-[9px] sm:text-[10px] font-black text-primary border-b-2 border-accent w-fit">
                                BUKA LAYANAN <i class="bi bi-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 p-6 bg-primary/5 rounded-[2rem] border border-primary/10">
                    <p class="text-[11px] text-primary/60 leading-relaxed text-center font-medium">
                        <i class="bi bi-info-circle mr-1"></i> Preview di atas mencerminkan tampilan instansi Anda pada folder <b>"Direktori Layanan"</b> di halaman utama.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // --- LIVE UPDATES LOGIC ---
    
    // 1. Nama
    document.getElementById('in_nama').addEventListener('input', function() {
        document.getElementById('pv_nama').innerText = this.value || 'Nama Instansi';
    });

    // 2. Kategori
    document.getElementById('in_cat').addEventListener('input', function() {
        document.getElementById('pv_cat').innerText = this.value || 'Kategori';
    });

    // 3. Warna Background Badge
    document.getElementById('in_bg_color').addEventListener('input', function() {
        document.getElementById('pv_badge').style.backgroundColor = this.value;
    });

    document.getElementById('in_icon_color').addEventListener('input', function () {
        document.getElementById('pv_icon').style.color = this.value;
    });

    // 4. Update Gambar (Sampul)
    document.getElementById('in_img').onchange = function() {
        const [file] = this.files;
        if (file) {
            document.getElementById('file_name_display').innerText = file.name;
            document.getElementById('pv_img').src = URL.createObjectURL(file);
        }
    };

    // 5. Reset Warna ke Default
    function resetColor(inputId, defaultValue) {
        const input = document.getElementById(inputId);
        if (!input) return;
        input.value = defaultValue;
        // Trigger event 'input' biar preview di kanan ikut ke-update otomatis
        // lewat listener yang sudah ada (nomor 3 di atas), tanpa perlu duplikasi logic.
        input.dispatchEvent(new Event('input', { bubbles: true }));
    }

    // --- 6. ICON PICKER: POP-OVER LOGIC ---
    function toggleIconPicker() {
        document.getElementById('iconPickerPanel').classList.toggle('is-open');
        document.getElementById('iconPickerTrigger').classList.toggle('is-open');
        document.getElementById('iconPickerChevron').classList.toggle('bi-chevron-down');
        document.getElementById('iconPickerChevron').classList.toggle('bi-chevron-up');
    }

    // Klik luar untuk tutup
    document.addEventListener('click', function (e) {
        const wrap = document.querySelector('.icon-picker-wrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('iconPickerPanel').classList.remove('is-open');
            document.getElementById('iconPickerTrigger').classList.remove('is-open');
            document.getElementById('iconPickerChevron').classList.add('bi-chevron-down');
            document.getElementById('iconPickerChevron').classList.remove('bi-chevron-up');
        }
    });

    // Pilih Ikon
    function pickIcon(iconName, element) {

        // Save value
        document.getElementById('selected_icon_value').value = iconName;

        // UI Form
        document.querySelectorAll('.icon-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('iconPickerPreview').innerHTML =
            `<i class="bi ${iconName}"></i>`;
            
        // UI Preview Card
        const icon = document.getElementById('pv_icon');
        icon.className = `bi ${iconName} text-xl`;
        icon.style.color = document.getElementById('in_icon_color').value;
        toggleIconPicker();
    }

    document
    .getElementById('visualLayananForm')
    .addEventListener('submit', function () {

        const btn = document.querySelector('.btn-command-save');

        btn.innerHTML =
            '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses...';

        btn.style.opacity = '.8';
        btn.style.pointerEvents = 'none';

    });
</script>
@endpush

@endsection