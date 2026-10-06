@extends('layouts.admin')

@section('title', 'Tambah Instansi')
@section('page-title', 'Registrasi Instansi')
@section('page-icon', 'bi-building-add')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
    $icons = [
        // Kependudukan & sosial
        'bi-people-fill', 'bi-person-badge-fill', 'bi-person-workspace', 'bi-house-door-fill',
        // Bangunan & perizinan
        'bi-building-fill', 'bi-buildings', 'bi-building-gear', 'bi-signpost-2-fill',
        // Komunikasi & informasi
        'bi-broadcast', 'bi-wifi', 'bi-router-fill', 'bi-camera-video-fill', 'bi-newspaper', 'bi-megaphone-fill',
        // Keuangan & pajak
        'bi-cash-stack', 'bi-cash-coin', 'bi-bank', 'bi-bank2', 'bi-wallet2', 'bi-credit-card-2-front-fill', 'bi-calculator-fill', 'bi-graph-up-arrow',
        // Pertanian, pangan & perikanan
        'bi-flower1', 'bi-flower2', 'bi-tree-fill', 'bi-basket3-fill', 'bi-water', 'bi-egg-fried',
        // Kesehatan
        'bi-hospital-fill', 'bi-heart-pulse-fill', 'bi-life-preserver',
        // Pendidikan
        'bi-mortarboard-fill', 'bi-book-fill', 'bi-journal-bookmark-fill',
        // Keamanan & hukum
        'bi-shield-shaded', 'bi-shield-fill-check', 'bi-shield-lock-fill', 'bi-incognito',
        // Transportasi & infrastruktur
        'bi-bus-front-fill', 'bi-truck', 'bi-car-front-fill', 'bi-bicycle', 'bi-compass-fill',
        // Energi & lingkungan
        'bi-lightning-charge-fill', 'bi-droplet-fill', 'bi-droplet-half', 'bi-recycle',
        // Umum / administrasi
        'bi-briefcase-fill', 'bi-clipboard-check-fill', 'bi-clipboard-data-fill', 'bi-file-earmark-text-fill',
        'bi-files', 'bi-gear-fill', 'bi-tools', 'bi-award-fill', 'bi-trophy-fill', 'bi-stars',
        'bi-globe', 'bi-globe2', 'bi-map-fill', 'bi-geo-alt-fill', 'bi-flag-fill',
    ];
@endphp

<style>
    /* 1. Card & Layout Base */
    .command-card { background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; max-width: 900px; margin: 0 auto; }
    .command-header { background: #0A1E33; padding: 32px 40px; border-bottom: 4px solid #E8A33D; }
    .command-header h2 { color: #ffffff; font-size: 20px; font-weight: 800; margin: 0; }
    .command-header p { color: rgba(255,255,255,0.6); font-size: 12px; margin-top: 5px; }

    /* 2. Section grouping, biar struktur formnya jelas kelompoknya */
    .form-section { padding: 28px 40px; }
    .form-section + .form-section { border-top: 1px solid #F1F5F9; }

    .section-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }
    .section-number {
        width: 26px; height: 26px;
        border-radius: 8px;
        background: #123457;
        color: #E8A33D;
        font-size: 11px;
        font-weight: 900;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
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

    /* 4. ICON PICKER — gaya emoji keyboard: tombol trigger + panel popover */
    .icon-picker-trigger {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.25s ease;
    }
    .icon-picker-trigger:hover { border-color: #E8A33D; }
    .icon-picker-trigger.is-open { border-color: #123457; box-shadow: 0 0 0 4px rgba(18, 52, 87, 0.05); background: #ffffff; }

    .icon-picker-preview {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: #123457;
        color: #E8A33D;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .icon-picker-wrap { position: relative; }

    .icon-picker-panel {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        z-index: 30;
        background: #ffffff;
        border: 1.5px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(10, 30, 51, 0.12);
        padding: 14px;
        display: none;
    }
    .icon-picker-panel.is-open { display: block; }

    .icon-picker-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 8px;
        max-height: 220px;
        overflow-y: auto;
    }

    @media (max-width: 480px) {
        .icon-picker-grid { grid-template-columns: repeat(6, 1fr); }
    }

    .icon-item {
        aspect-ratio: 1/1;
        display: flex; align-items: center; justify-content: center;
        background: #F8FAFC;
        border-radius: 10px;
        border: 1px solid #E2E8F0;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 1.15rem;
        color: #94a3b8;
    }
    .icon-item:hover { border-color: #E8A33D; color: #E8A33D; transform: scale(1.08); }
    .icon-item.active { background: #123457; border-color: #123457; color: #E8A33D; box-shadow: 0 4px 10px rgba(18, 52, 87, 0.2); }

    /* 5. Action Buttons */
    .form-footer { padding: 25px 40px; background: #F8FAFC; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 12px; }
    .btn-command-cancel { padding: 12px 24px; border-radius: 12px; font-size: 12px; font-weight: 700; color: #475569; background: #e2e8f0; border: 1px solid #cbd5e1; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; }
    .btn-command-cancel:hover { background: #d1d5db; color: #1e293b; transform: translateY(-1px); }
    .btn-command-save { padding: 12px 32px; border-radius: 12px; font-size: 12px; font-weight: 800; color: #ffffff; background: #123457; border: none; box-shadow: 0 10px 15px -3px rgba(18, 52, 87, 0.2); transition: all 0.3s; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; }
    .btn-command-save:hover { background: #E8A33D; color: #0A1E33; transform: translateY(-2px); }

    /* ===============================
    CUSTOM FILE UPLOAD
    ==================================*/

    .file-upload-wrapper{
        position:relative;
        width:100%;
        cursor:pointer;
    }

    .file-upload-box{
        display:flex;
        align-items:center;
        gap:12px;
        height:52px;
        padding:0 16px;

        background:#F8FAFC;
        border:1.5px solid #E2E8F0;
        border-radius:14px;

        transition:.25s;
    }

    .file-upload-wrapper:hover .file-upload-box{
        background:#fff;
        border-color:#E8A33D;
    }

    .file-icon{
        width:34px;
        height:34px;
        border-radius:10px;

        background:#123457;
        color:#E8A33D;

        display:flex;
        align-items:center;
        justify-content:center;

        flex-shrink:0;
        font-size:15px;
    }

    .file-text{
        font-size:12px;
        font-weight:700;
        color:#94A3B8;

        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    .file-input-real{
        position:absolute;
        inset:0;
        opacity:0;
        cursor:pointer;
    }

    /* 6. MAPS EXTRACT STATUS + disabled button state */
    #btnTarikAlamat:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>


<div class="command-card">
    <div class="command-header">
        <div class="flex items-center justify-between">
            <div>
                <h2>Registrasi Instansi Baru</h2>
                <p>Lengkapi profil dan identitas visual dinas untuk publikasi portal utama.</p>
            </div>
            <div class="hidden md:flex w-12 h-12 rounded-xl bg-white/10 items-center justify-center text-white border border-white/10">
                <i class="bi bi-building text-xl text-accent"></i>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.instansi.store', $portalCode) }}" method="POST" enctype="multipart/form-data" id="instansiForm">
        @csrf

        {{-- ========== SECTION 1: INFORMASI DASAR ========== --}}
        <div class="form-section">
            <div class="section-heading">
                <span class="section-number">1</span>
                <div>
                    <h3>Informasi Dasar</h3>
                    <p>Nama resmi, kategori, dan alamat URL portal instansi.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
                <div class="input-field-wrapper md:col-span-2">
                    <label class="label-command">Nama Resmi Instansi</label>
                    <div class="input-group-custom">
                        <input type="text" name="nama" id="ins_name" class="input-control-command" placeholder="Contoh: Dinas Komunikasi dan Informatika" required>
                        <i class="bi bi-card-text input-icon-left"></i>
                    </div>
                </div>

                <div class="input-field-wrapper">
                    <label class="label-command">Slug URL (Otomatis)</label>
                    <div class="input-group-custom">
                        <input type="text" name="slug" id="ins_slug" class="input-control-command bg-slate-100" placeholder="diskominfo" readonly>
                        <i class="bi bi-link-45deg input-icon-left"></i>
                    </div>
                </div>

                <div class="input-field-wrapper">
                    <label class="label-command">Sektor Layanan</label>
                    <div class="input-group-custom">
                        <select name="kategori" class="input-control-command appearance-none" required>
                            <option value="" disabled selected>Pilih Sektor...</option>
                            <option value="kependudukan">Kependudukan</option>
                            <option value="perizinan">Perizinan</option>
                            <option value="keuangan">Keuangan</option>
                            <option value="pangan">Pangan & Tani</option>
                            <option value="komunikasi">Komunikasi</option>
                        </select>
                        <i class="bi bi-grid-1x2 input-icon-left"></i>
                        <i class="bi bi-chevron-down absolute right-4 text-slate-400 pointer-events-none"></i>
                    </div>
                </div>

                <div class="input-field-wrapper md:col-span-2">
                    <label class="label-command">Slogan / Tagline</label>
                    <div class="input-group-custom">
                        <input type="text" name="slogan" class="input-control-command" placeholder="Melayani Sepenuh Hati Dengan Sistem Terpadu" required>
                        <i class="bi bi-stars input-icon-left"></i>
                    </div>
                </div>

                <div class="input-field-wrapper md:col-span-2">
                    <label class="label-command">Narasi</label>

                    <div class="relative">
                        <textarea name="about_title" rows="3" class="input-control-command !pl-5" style="padding-top:14px;height:auto;"placeholder="Tuliskan narasi singkat yang akan ditampilkan pada halaman Company Profile..."></textarea>
                    </div>
                </div>

                <div class="input-field-wrapper md:col-span-2">
                    <label class="label-command">Deskripsi Profil</label>
                    <div class="relative">
                        <textarea name="deskripsi" rows="3" class="input-control-command !pl-5" style="padding-top: 14px; height: auto;" placeholder="Tuliskan visi misi atau tugas pokok instansi..." required></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== SECTION 2: IDENTITAS VISUAL ========== --}}
        <div class="form-section">
            <div class="section-heading">
                <span class="section-number">2</span>
                <div>
                    <h3>Identitas Visual</h3>
                    <p>Warna, logo, banner, dan ikon badge yang mewakili instansi.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
                <div class="input-field-wrapper">
                    <label class="label-command">Warna Identitas (Primary)</label>
                    <div class="input-group-custom">
                        <input type="color" name="primary_color" class="input-control-command input-color-custom" value="#123457">
                        <i class="bi bi-palette input-icon-left"></i>
                    </div>
                </div>

                {{-- ICON PICKER GAYA EMOJI KEYBOARD --}}
                <div class="input-field-wrapper">
                    <label class="label-command">Ikon Visual (Badge)</label>
                    <input type="hidden" name="icon" id="selected_icon_value" value="bi-building-fill">

                    <div class="icon-picker-wrap">
                        <button type="button" class="icon-picker-trigger" id="iconPickerTrigger" onclick="toggleIconPicker()">
                            <span class="icon-picker-preview" id="iconPickerPreview">
                                <i class="bi bi-building-fill"></i>
                            </span>
                            <span class="text-xs font-bold text-slate-500 flex-1 text-left">Klik untuk pilih ikon</span>
                            <i class="bi bi-chevron-down text-slate-400 text-xs" id="iconPickerChevron"></i>
                        </button>

                        <div class="icon-picker-panel" id="iconPickerPanel">
                            <div class="icon-picker-grid">
                                @foreach($icons as $icon)
                                    <div class="icon-item {{ $icon == 'bi-building-fill' ? 'active' : '' }}" onclick="pickIcon('{{ $icon }}', this)">
                                        <i class="bi {{ $icon }}"></i>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="input-field-wrapper">
                    <label class="label-command">Logo Instansi (PNG)</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="logo_file" name="logo" class="file-input-real" accept="image/png" required>
                        <div class="file-upload-box">
                            <div class="file-icon">
                                <i class="bi bi-patch-check"></i>
                            </div>
                            <span class="file-text" id="logo_file_name">Pilih Logo Instansi...</span>
                        </div>
                    </div>
                    <p class="mt-2 ml-1 text-[10px] font-medium text-slate-400">
                        Format: PNG, JPG, JPEG, WEBP • Maksimal 2 MB
                    </p>
                </div>

                <div class="input-field-wrapper">
                    <label class="label-command">Foto Cover / Banner</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="cover_file" name="cover" class="file-input-real" accept="image/*" required>
                        <div class="file-upload-box">
                            <div class="file-icon">
                                <i class="bi bi-image"></i>
                            </div>
                            <span class="file-text" id="cover_file_name">Pilih Cover Banner...</span>
                        </div>
                    </div>
                    <p class="mt-2 ml-1 text-[10px] font-medium text-slate-400">
                        Format: PNG, JPG, JPEG, WEBP • Maksimal 2 MB
                    </p>
                </div>
            </div>
        </div>

        {{-- ========== SECTION 3: LOKASI & KONTAK ========== --}}
        <div class="form-section">
            <div class="section-heading">
                <span class="section-number">3</span>
                <div>
                    <h3>Lokasi & Kontak</h3>
                    <p>Alamat kantor dan tautan lokasi di peta.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-x-8">
                <div class="input-field-wrapper">
                    <label class="label-command">Google Maps Embed URL</label>
                    <div class="input-group-custom">
                        <input type="text" name="maps_embed" id="map_url_input" class="input-control-command" placeholder="https://www.google.com/maps/embed?..." required>
                        <i class="bi bi-map input-icon-left"></i>
                        <button type="button" onclick="smartExtractAddress()" id="btnTarikAlamat" class="absolute right-3 bg-accent text-primary-dark text-[9px] font-black px-3 py-1.5 rounded-lg shadow-sm hover:brightness-110">TARIK ALAMAT</button>
                    </div>
                    <p class="mt-2 ml-1 text-[10px] font-medium" id="mapsExtractStatus"></p>
                </div>

                <div class="input-field-wrapper">
                    <label class="label-command">Alamat Kantor</label>
                    <div class="input-group-custom">
                        <input type="text" name="alamat" id="ins_address" class="input-control-command" placeholder="Gunakan tombol 'Tarik Alamat' atau isi manual..." required>
                        <i class="bi bi-geo-alt input-icon-left"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('admin.instansi.index', $portalCode) }}" class="btn-command-cancel">
                Batalkan
            </a>
            <button type="submit" class="btn-command-save">
                Simpan Instansi
            </button>
        </div>
    </form>
</div>

<x-admin.modal-form-errors />

@push('scripts')
<script>
    // --- 1. ICON PICKER: TOGGLE PANEL (gaya emoji keyboard) ---
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

    // Tutup panel dengan tombol Esc, konsisten sama picker/modal lain di project ini
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const panel = document.getElementById('iconPickerPanel');
            if (panel && panel.classList.contains('is-open')) {
                toggleIconPicker();
            }
        }
    });

    // --- 2. ICON PICKER: PILIH IKON ---
    function pickIcon(iconName, element) {
        document.getElementById('selected_icon_value').value = iconName;
        document.querySelectorAll('.icon-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        // Update preview di tombol trigger
        document.getElementById('iconPickerPreview').innerHTML = `<i class="bi ${iconName}"></i>`;

        // Otomatis tutup panel begitu ikon dipilih, kayak emoji picker beneran
        toggleIconPicker();
    }

    // --- 3. SMART ADDRESS EXTRACTOR (via backend reverse geocoding - Nominatim) ---

    // Ekstrak longitude & latitude dari parameter "pb" Google Maps Embed URL.
    // Formatnya: ...!1d<zoom-ish>!2d<longitude>!3d<latitude>...
    function extractLatLngFromEmbedUrl(url) {
        const regex = /!2d(-?\d+\.?\d*)!3d(-?\d+\.?\d*)/;
        const match = url.match(regex);

        if (!match) return null;

        const lng = parseFloat(match[1]);
        const lat = parseFloat(match[2]);

        if (isNaN(lat) || isNaN(lng)) return null;

        return { lat, lng };
    }

    function setMapsStatus(message, type) {
        const el = document.getElementById('mapsExtractStatus');
        if (!el) return;

        const colors = {
            error: '#dc2626',
            success: '#16a34a',
            loading: '#94a3b8',
        };

        el.textContent = message;
        el.style.color = colors[type] || '#94a3b8';
    }

    async function smartExtractAddress() {
        const url = document.getElementById('map_url_input').value.trim();
        const btn = document.getElementById('btnTarikAlamat');

        setMapsStatus('', 'loading');

        if (!url) {
            setMapsStatus('Tempelkan link Google Maps Embed URL dulu.', 'error');
            return;
        }

        const coords = extractLatLngFromEmbedUrl(url);

        if (!coords) {
            setMapsStatus('Koordinat tidak ditemukan pada URL ini. Pastikan ini Google Maps Embed URL yang valid, atau isi alamat secara manual.', 'error');
            return;
        }

        const originalBtnText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'MEMPROSES...';
        setMapsStatus('Mengambil alamat lengkap dari koordinat...', 'loading');

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            const response = await fetch('{{ route("admin.instansi.reverse-geocode", $portalCode) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({
                    latitude: coords.lat,
                    longitude: coords.lng,
                }),
            });

            const data = await response.json();

            if (response.ok && data.success && data.address) {
                document.getElementById('ins_address').value = data.address;
                setMapsStatus('Alamat berhasil diambil otomatis.', 'success');
            } else {
                setMapsStatus(data.message || 'Alamat tidak ditemukan. Silakan isi manual.', 'error');
            }

        } catch (err) {
            setMapsStatus('Tidak dapat terhubung ke server. Silakan isi alamat secara manual.', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = originalBtnText;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- 4. AUTO SLUG ---
        const nameInput = document.getElementById('ins_name');
        const slugInput = document.getElementById('ins_slug');

        nameInput.addEventListener('input', function() {
            slugInput.value = this.value.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
        });

        // --- 5. LOADING ---
        const form = document.getElementById('instansiForm');
        form.addEventListener('submit', function() {
            const btn = this.querySelector('.btn-command-save');
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses...';
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';
        });
    });

    document.getElementById('logo_file').addEventListener('change', function () {
        const file = this.files[0];
        if (file) document.getElementById('logo_file_name').innerText = file.name;
    });

    document.getElementById('cover_file').addEventListener('change', function () {
        const file = this.files[0];
        if (file) document.getElementById('cover_file_name').innerText = file.name;
    });
</script>
@endpush

@endsection