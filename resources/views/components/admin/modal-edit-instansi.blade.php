@once
<style>
    /* 1. ANIMATION KEYFRAMES (nama unik biar gak bentrok sama modal lain) */
    @keyframes editInsOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes editInsOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes editInsSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.01) translateY(-4px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes editInsSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.96) translateY(10px); }
    }

    /* 2. OVERLAY */
    .edit-ins-overlay {
        position: fixed; inset: 0;
        background: rgba(10, 30, 51, 0.75);
        backdrop-filter: blur(5px);
        display: none; align-items: center; justify-content: center;
        z-index: 9999; padding: 15px;
        opacity: 0;
    }
    .edit-ins-overlay.is-active { display: flex; animation: editInsOverlayIn 0.3s ease forwards; }
    .edit-ins-overlay.is-active .edit-ins-card { animation: editInsSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .edit-ins-overlay.is-closing { animation: editInsOverlayOut 0.3s ease forwards; }
    .edit-ins-overlay.is-closing .edit-ins-card { animation: editInsSpringOut 0.3s ease forwards; }

    /* 3. CARD — bahasa visual sama persis dengan halaman Tambah Instansi (command-card) */
    .edit-ins-card {
        background: #ffffff;
        width: 100%; max-width: 380px;
        max-height: 80vh;
        border-radius: 22px; overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        display: flex; flex-direction: column;
    }

    .edit-ins-header {
        background: #0A1E33;
        padding: 18px 24px;
        border-bottom: 4px solid #E8A33D;
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px;
        flex-shrink: 0;
    }
    .edit-ins-header-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .edit-ins-icon-box {
        width: 32px; height: 32px; border-radius: 9px;
        background: rgba(255,255,255,0.1);
        display: flex; align-items: center; justify-content: center;
        color: #E8A33D; font-size: 15px; flex-shrink: 0;
    }
    .edit-ins-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; letter-spacing: 0.3px; }
    .edit-ins-header p { color: rgba(255,255,255,0.5); font-size: 10.5px; margin: 2px 0 0; }
    .edit-ins-close {
        width: 30px; height: 30px; border-radius: 9px;
        background: rgba(255,255,255,0.06); border: none; color: #94a3b8;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; cursor: pointer;
        transition: all 0.2s;
    }
    .edit-ins-close:hover { background: rgba(239,68,68,0.15); color: #f87171; }

    /* Body scrollable, header & footer tetap (sticky) */
    .edit-ins-body-scroll { overflow-y: auto; flex: 1; }
    .edit-ins-body-scroll::-webkit-scrollbar { width: 5px; }
    .edit-ins-body-scroll::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }

    .edit-ins-section { padding: 20px 24px; }
    .edit-ins-section + .edit-ins-section { border-top: 1px solid #F1F5F9; }

    .edit-ins-section-heading { display: flex; align-items: center; gap: 9px; margin-bottom: 16px; }
    .edit-ins-section-number {
        width: 22px; height: 22px; border-radius: 7px;
        background: #123457; color: #E8A33D;
        font-size: 10px; font-weight: 900;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .edit-ins-section-heading h3 { font-size: 12px; font-weight: 800; color: #123457; margin: 0; }
    .edit-ins-section-heading p { font-size: 10px; color: #94a3b8; margin: 1px 0 0; }

    /* Fields */
    .edit-ins-field { margin-bottom: 14px; }
    .edit-ins-label { display: block; font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; color: #64748b; margin-bottom: 6px; margin-left: 2px; }
    .edit-ins-input-group { position: relative; display: flex; align-items: center; width: 100%; }
    .edit-ins-input {
        width: 100%; padding: 10px 14px 10px 40px;
        background: #F8FAFC; border: 1.2px solid #E2E8F0; border-radius: 12px;
        font-size: 12.5px; font-weight: 700; color: #123457;
        transition: all 0.2s; font-family: inherit;
    }
    .edit-ins-input:focus { outline: none; border-color: #123457; background: #fff; box-shadow: 0 0 0 3px rgba(18, 52, 87, 0.06); }
    .edit-ins-input-icon { position: absolute; left: 14px; color: #94a3b8; font-size: 1rem; z-index: 5; pointer-events: none; }
    .edit-ins-hint { font-size: 9.5px; color: #94a3b8; font-weight: 600; margin-top: 4px; margin-left: 2px; }

    .edit-ins-input-color { padding: 6px 10px 6px 40px !important; height: 42px; cursor: pointer; }
    .edit-ins-input-file { padding: 9px 14px 9px 40px !important; font-size: 10.5px !important; }

    .edit-ins-file-preview-row { display: flex; align-items: center; gap: 10px; }
    .edit-ins-file-thumb {
        width: 42px; height: 42px; border-radius: 10px; object-fit: cover;
        border: 1.2px solid #E2E8F0; background: #F8FAFC; flex-shrink: 0;
    }

    /* Icon Picker — sama persis pola "emoji keyboard" dari halaman create */
    .edit-ins-icon-picker-wrap { position: relative; }
    .edit-ins-icon-picker-trigger {
        width: 100%; display: flex; align-items: center; gap: 10px;
        padding: 9px 14px; background: #F8FAFC; border: 1.2px solid #E2E8F0; border-radius: 12px;
        cursor: pointer; transition: all 0.25s ease;
    }
    .edit-ins-icon-picker-trigger:hover { border-color: #E8A33D; }
    .edit-ins-icon-picker-trigger.is-open { border-color: #123457; box-shadow: 0 0 0 3px rgba(18, 52, 87, 0.06); background: #fff; }
    .edit-ins-icon-picker-preview {
        width: 32px; height: 32px; border-radius: 8px; background: #123457; color: #E8A33D;
        display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;
    }
    .edit-ins-icon-picker-panel {
        position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 30;
        background: #fff; border: 1.2px solid #E2E8F0; border-radius: 14px;
        box-shadow: 0 20px 40px rgba(10, 30, 51, 0.14); padding: 12px; display: none;
    }
    .edit-ins-icon-picker-panel.is-open { display: block; }
    .edit-ins-icon-picker-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 7px; max-height: 190px; overflow-y: auto; }
    @media (max-width: 480px) { .edit-ins-icon-picker-grid { grid-template-columns: repeat(5, 1fr); } }
    .edit-ins-icon-item {
        aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center;
        background: #F8FAFC; border-radius: 9px; border: 1px solid #E2E8F0; cursor: pointer;
        transition: all 0.2s; font-size: 1rem; color: #94a3b8;
    }
    .edit-ins-icon-item:hover { border-color: #E8A33D; color: #E8A33D; transform: scale(1.06); }
    .edit-ins-icon-item.active { background: #123457; border-color: #123457; color: #E8A33D; box-shadow: 0 4px 10px rgba(18, 52, 87, 0.2); }

    /* Footer sticky */
    .edit-ins-footer {
        padding: 14px 24px; background: #F8FAFC; border-top: 1px solid #e2e8f0;
        display: flex; justify-content: flex-end; gap: 10px; flex-shrink: 0;
    }
    .edit-ins-btn-cancel {
        padding: 10px 20px; border-radius: 10px; font-size: 11px; font-weight: 700; color: #475569;
        background: #e2e8f0; border: 1px solid #cbd5e1; cursor: pointer; transition: all 0.2s;
    }
    .edit-ins-btn-cancel:hover { background: #d1d5db; color: #1e293b; }
    .edit-ins-btn-save {
        padding: 10px 26px; border-radius: 10px; font-size: 11px; font-weight: 800; color: #fff;
        background: #123457; border: none; box-shadow: 0 8px 14px -3px rgba(18, 52, 87, 0.2);
        text-transform: uppercase; letter-spacing: 0.6px; cursor: pointer; transition: all 0.2s;
    }
    .edit-ins-btn-save:hover { background: #E8A33D; color: #0A1E33; }

    /* ===============================
    CUSTOM FILE UPLOAD
    ==================================*/

    .edit-ins-file-upload-wrapper{
        position:relative;
        width:100%;
        cursor:pointer;
    }

    .edit-ins-file-upload-box{
        display:flex;
        align-items:center;
        gap:12px;

        height:42px;
        padding:0 14px;

        background:#F8FAFC;
        border:1.2px solid #E2E8F0;
        border-radius:12px;

        transition:.25s;
    }

    .edit-ins-file-upload-wrapper:hover .edit-ins-file-upload-box{
        background:#fff;
        border-color:#E8A33D;
    }

    .edit-ins-file-icon{
        width:28px;
        height:28px;

        border-radius:8px;

        background:#123457;
        color:#E8A33D;

        display:flex;
        align-items:center;
        justify-content:center;

        flex-shrink:0;
        font-size:13px;
    }

    .edit-ins-file-text{
        font-size:11px;
        font-weight:700;
        color:#94A3B8;

        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    .edit-ins-file-input-real{
        position:absolute;
        inset:0;
        opacity:0;
        cursor:pointer;
    }
</style>
@endonce

@php
    // Daftar ikon sama persis dengan halaman Tambah Instansi, biar picker-nya konsisten.
    // Didefinisikan lokal di sini supaya partial ini tetap mandiri di mana pun di-include.
    $editInsIcons = [
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

<div id="editInstansiModal" class="edit-ins-overlay" onclick="if(event.target === this) closeEditInstansiModal()">
    <div class="edit-ins-card">

        <div class="edit-ins-header">
            <div class="edit-ins-header-left">
                <div class="edit-ins-icon-box"><i class="bi bi-building-gear"></i></div>
                <div class="min-w-0">
                    <h2>Edit Instansi</h2>
                    <p>Perbarui profil & identitas visual dinas</p>
                </div>
            </div>
            <button type="button" class="edit-ins-close" onclick="closeEditInstansiModal()"><i class="bi bi-x-lg text-xs"></i></button>
        </div>

        <form id="editInstansiForm" method="POST" enctype="multipart/form-data" class="edit-ins-body-scroll">
            @csrf
            @method('PUT')

            {{-- ========== SECTION 1: INFORMASI DASAR ========== --}}
            <div class="edit-ins-section">
                <div class="edit-ins-section-heading">
                    <span class="edit-ins-section-number">1</span>
                    <div>
                        <h3>Informasi Dasar</h3>
                        <p>Nama, kategori, slogan & deskripsi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 md:gap-x-6">
                    <div class="edit-ins-field md:col-span-2">
                        <label class="edit-ins-label">Nama Resmi Instansi</label>
                        <div class="edit-ins-input-group">
                            <input type="text" name="nama" id="edit_ins_name" class="edit-ins-input" placeholder="Contoh: Dinas Komunikasi dan Informatika" required>
                            <i class="bi bi-card-text edit-ins-input-icon"></i>
                        </div>
                    </div>

                    <div class="edit-ins-field">
                        <label class="edit-ins-label">Slug URL</label>
                        <div class="edit-ins-input-group">
                            <input type="text" id="edit_ins_slug" class="edit-ins-input bg-slate-100" placeholder="Terisi Otomatis" readonly>
                            <i class="bi bi-link-45deg edit-ins-input-icon"></i>
                        </div>
                        <p class="edit-ins-hint">Terkunci — mengubahnya bisa merusak tautan yang sudah dibagikan.</p>
                    </div>

                    <div class="edit-ins-field">
                        <label class="edit-ins-label">Sektor Layanan</label>
                        <div class="edit-ins-input-group">
                            <select id="edit_ins_category" name="kategori" class="edit-ins-input appearance-none" required>
                                <option value="" disabled>Pilih Sektor...</option>
                                <option value="kependudukan">Kependudukan</option>
                                <option value="perizinan">Perizinan</option>
                                <option value="keuangan">Keuangan</option>
                                <option value="pangan">Pangan & Tani</option>
                                <option value="komunikasi">Komunikasi</option>
                            </select>
                            <i class="bi bi-grid-1x2 edit-ins-input-icon"></i>
                            <i class="bi bi-chevron-down absolute right-3.5 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <div class="edit-ins-field md:col-span-2">
                        <label class="edit-ins-label">Slogan / Tagline</label>
                        <div class="edit-ins-input-group">
                            <input type="text" name="slogan" id="edit_ins_slogan" class="edit-ins-input" placeholder="Melayani Sepenuh Hati Dengan Sistem Terpadu" required>
                            <i class="bi bi-stars edit-ins-input-icon"></i>
                        </div>
                    </div>

                    <div class="edit-ins-field md:col-span-2">
                        <label class="edit-ins-label">Headline Company Profile</label>

                        <div class="edit-ins-input-group">
                            <input type="text" name="about_title" id="edit_ins_about_title" class="edit-ins-input"placeholder="Contoh: Mengenal Dinas Komunikasi dan Informatika">
                            <i class="bi bi-type-h1 edit-ins-input-icon"></i>
                        </div>
                    </div>

                    <div class="edit-ins-field md:col-span-2">
                        <label class="edit-ins-label">Deskripsi Profil</label>
                        <textarea name="deskripsi" id="edit_ins_description" rows="3" class="edit-ins-input !pl-3.5" style="padding-top: 10px; height: auto;" placeholder="Tuliskan visi misi atau tugas pokok instansi..." required></textarea>
                    </div>
                </div>
            </div>

            {{-- ========== SECTION 2: IDENTITAS VISUAL ========== --}}
            <div class="edit-ins-section">
                <div class="edit-ins-section-heading">
                    <span class="edit-ins-section-number">2</span>
                    <div>
                        <h3>Identitas Visual</h3>
                        <p>Warna, logo, banner, dan ikon badge</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 md:gap-x-6">
                    <div class="edit-ins-field">
                        <label class="edit-ins-label">Warna Identitas (Primary)</label>
                        <div class="edit-ins-input-group">
                            <input type="color" name="primary_color" id="edit_ins_color" class="edit-ins-input edit-ins-input-color">
                            <i class="bi bi-palette edit-ins-input-icon"></i>
                        </div>
                    </div>

                    <div class="edit-ins-field">
                        <label class="edit-ins-label">Ikon Visual (Badge)</label>
                        <input type="hidden" name="icon" id="edit_ins_selected_icon_value" value="bi-building-fill">

                        <div class="edit-ins-icon-picker-wrap">
                            <button type="button" class="edit-ins-icon-picker-trigger" id="editInsIconPickerTrigger" onclick="toggleEditInsIconPicker()">
                                <span class="edit-ins-icon-picker-preview" id="editInsIconPickerPreview">
                                    <i class="bi bi-building-fill"></i>
                                </span>
                                <span class="text-[11px] font-bold text-slate-500 flex-1 text-left">Klik untuk pilih ikon</span>
                                <i class="bi bi-chevron-down text-slate-400 text-xs" id="editInsIconPickerChevron"></i>
                            </button>

                            <div class="edit-ins-icon-picker-panel" id="editInsIconPickerPanel">
                                <div class="edit-ins-icon-picker-grid">
                                    @foreach($editInsIcons as $icon)
                                        <div class="edit-ins-icon-item" data-icon="{{ $icon }}" onclick="pickEditInsIcon('{{ $icon }}', this)">
                                            <i class="bi {{ $icon }}"></i>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="edit-ins-field">
                        <label class="edit-ins-label">Logo Instansi (PNG)</label>
                        <div class="edit-ins-file-upload-wrapper">
                            <input type="file" id="editInsLogoInput" name="logo" class="edit-ins-file-input-real" accept="image/png">
                            <div class="edit-ins-file-upload-box">
                                <div class="edit-ins-file-icon">
                                    <i class="bi bi-patch-check"></i>
                                </div>
                                <span class="edit-ins-file-text" id="editInsLogoText">
                                    Ganti Logo Instansi...
                                </span>
                            </div>
                        </div>
                        <p class="edit-ins-hint">
                            Kosongkan jika tidak ingin mengganti logo.
                        </p>
                    </div>

                    <div class="edit-ins-field">
                        <label class="edit-ins-label">Foto Cover / Banner</label>
                        <div class="edit-ins-file-upload-wrapper">
                            <input type="file" id="editInsCoverInput" name="cover" class="edit-ins-file-input-real" accept="image/*">
                            <div class="edit-ins-file-upload-box">
                                <div class="edit-ins-file-icon">
                                    <i class="bi bi-image"></i>
                                </div>
                                <span class="edit-ins-file-text" id="editInsCoverText">
                                    Ganti Cover Banner...
                                </span>
                            </div>
                        </div>
                        <p class="edit-ins-hint">
                            Kosongkan jika tidak ingin mengganti cover.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ========== SECTION 3: LOKASI & KONTAK ========== --}}
            <div class="edit-ins-section">
                <div class="edit-ins-section-heading">
                    <span class="edit-ins-section-number">3</span>
                    <div>
                        <h3>Lokasi & Kontak</h3>
                        <p>Alamat kantor dan tautan lokasi di peta</p>
                    </div>
                </div>

                <div class="edit-ins-field">
                    <label class="edit-ins-label">Google Maps Embed URL</label>
                    <div class="edit-ins-input-group">
                        <input type="text" name="maps_embed" id="edit_ins_map_url" class="edit-ins-input pr-24" placeholder="https://www.google.com/maps/embed?..." required>
                        <i class="bi bi-map edit-ins-input-icon"></i>
                        <button type="button" onclick="editInsSmartExtractAddress()" class="absolute right-2.5 bg-accent text-primary-dark text-[8.5px] font-black px-2.5 py-1.5 rounded-lg shadow-sm hover:brightness-110">TARIK ALAMAT</button>
                    </div>
                </div>

                <div class="edit-ins-field mb-0">
                    <label class="edit-ins-label">Alamat Kantor</label>
                    <div class="edit-ins-input-group">
                        <input type="text" name="alamat" id="edit_ins_address" class="edit-ins-input" placeholder="Gunakan tombol 'Tarik Alamat' atau isi manual..." required>
                        <i class="bi bi-geo-alt edit-ins-input-icon"></i>
                    </div>
                </div>
            </div>
        </form>

        <div class="edit-ins-footer">
            <button type="button" class="edit-ins-btn-cancel" onclick="closeEditInstansiModal()">Batal</button>
            <button type="submit" form="editInstansiForm" class="edit-ins-btn-save">Simpan Perubahan</button>
        </div>
    </div>
</div>

@once
<script>
    // --- BUKA / TUTUP MODAL ---
    function openEditInstansiModal(data, updateUrl) {
        const modal = document.getElementById('editInstansiModal');
        const form = document.getElementById('editInstansiForm');
        form.action = updateUrl;

        document.getElementById('edit_ins_name').value = data.nama || '';
        document.getElementById('edit_ins_slug').value = data.slug || '';
        document.getElementById('edit_ins_category').value = data.kategori || '';
        document.getElementById('edit_ins_slogan').value = data.slogan || '';
        document.getElementById('edit_ins_about_title').value = data.about_title || '';
        document.getElementById('edit_ins_description').value = data.deskripsi || '';
        document.getElementById('edit_ins_color').value = data.primary_color || '#123457';
        document.getElementById('edit_ins_map_url').value = data.maps_embed || '';
        document.getElementById('edit_ins_address').value = data.alamat || '';

        // Ikon: set nilai + tandai aktif di grid + update preview tombol
        const icon = data.icon || 'bi-building-fill';
        document.getElementById('edit_ins_selected_icon_value').value = icon;
        document.getElementById('editInsIconPickerPreview').innerHTML = `<i class="bi ${icon}"></i>`;
        document.querySelectorAll('#editInsIconPickerPanel .edit-ins-icon-item').forEach(el => {
            el.classList.toggle('active', el.dataset.icon === icon);
        });

        // Preview logo & cover yang SEDANG AKTIF, input file dikosongkan (opsional diganti)
        document.getElementById('editInsLogoInput').value = '';
        document.getElementById('editInsCoverInput').value = '';

        document.getElementById('editInsLogoText').innerText = 'Ganti Logo Instansi...';
        document.getElementById('editInsCoverText').innerText = 'Ganti Cover Banner...';

        modal.classList.remove('is-closing');
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function closeEditInstansiModal() {
        const modal = document.getElementById('editInstansiModal');
        if (modal) {
            modal.classList.add('is-closing');
            setTimeout(() => {
                modal.classList.remove('is-active');
                modal.classList.remove('is-closing');
                document.body.style.overflow = 'auto';
            }, 300);
        }
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('editInstansiModal');
            if (modal && modal.classList.contains('is-active')) closeEditInstansiModal();
        }
    });

    // --- ICON PICKER (pola sama dengan halaman Tambah Instansi) ---
    function toggleEditInsIconPicker() {
        document.getElementById('editInsIconPickerPanel').classList.toggle('is-open');
        document.getElementById('editInsIconPickerTrigger').classList.toggle('is-open');
        document.getElementById('editInsIconPickerChevron').classList.toggle('bi-chevron-down');
        document.getElementById('editInsIconPickerChevron').classList.toggle('bi-chevron-up');
    }

    function pickEditInsIcon(iconName, element) {
        document.getElementById('edit_ins_selected_icon_value').value = iconName;
        document.querySelectorAll('#editInsIconPickerPanel .edit-ins-icon-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('editInsIconPickerPreview').innerHTML = `<i class="bi ${iconName}"></i>`;
        toggleEditInsIconPicker();
    }

    document.getElementById('editInsLogoInput').addEventListener('change', function () {
        const file = this.files[0];

        if (file) {
            document.getElementById('editInsLogoText').innerText = file.name;
        }
    });

    document.getElementById('editInsCoverInput').addEventListener('change', function () {
        const file = this.files[0];

        if (file) {
            document.getElementById('editInsCoverText').innerText = file.name;
        }
    });

    document.addEventListener('click', function (e) {
        const wrap = document.querySelector('#editInstansiModal .edit-ins-icon-picker-wrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('editInsIconPickerPanel').classList.remove('is-open');
            document.getElementById('editInsIconPickerTrigger').classList.remove('is-open');
            document.getElementById('editInsIconPickerChevron').classList.remove('bi-chevron-up');
            document.getElementById('editInsIconPickerChevron').classList.add('bi-chevron-down');
        }
    });

    // --- SMART ADDRESS EXTRACTOR ---
    function editInsSmartExtractAddress() {
        const url = document.getElementById('edit_ins_map_url').value;
        if (!url) return alert('Tempelkan link Google Maps dulu!');

        const regex = /!2s([^!]+)!3m/;
        const match = url.match(regex);

        if (match && match[1]) {
            let decoded = decodeURIComponent(match[1]).replace(/\+/g, ' ');
            document.getElementById('edit_ins_address').value = decoded;
        } else {
            alert('Alamat tidak ditemukan otomatis. Silakan isi manual.');
        }
    }

    // --- LIVE PREVIEW FILE LOGO/COVER SAAT DIGANTI ---
    function previewEditInsFile(input, previewId) {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => { document.getElementById(previewId).src = e.target.result; };
        reader.readAsDataURL(file);
    }

    // --- LOADING STATE SAAT SIMPAN ---
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('editInstansiForm');
        form.addEventListener('submit', function () {
            const btn = document.querySelector('.edit-ins-btn-save');
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-1"></i> Menyimpan...';
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';
        });
    });
</script>
@endonce