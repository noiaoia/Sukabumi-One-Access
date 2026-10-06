@extends('layouts.admin')

@section('title', 'Arsitek Formulir - ' . ($category->nama ?? 'Kategori'))

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
    $seedCards = old('structure') ? json_decode(old('structure'), true) : ($structure ?? []);
    $seedFormTitle = old('form_title', $category->form_title ?? 'Formulir Pengajuan');
    $seedFormDesc  = old('form_description', $category->form_description ?? '');

    // Batas Edit: -1 = Nonaktif, 0 = Tanpa Batas, >0 = Jam
    $seedEditLimit = old('edit_limit', $category->edit_limit ?? -1);

    $layananFormId = $category->layanan_id ?? ($svc->id ?? 0);

    $availableIcons = [
        'bi-person-fill', 'bi-person-badge-fill', 'bi-people-fill', 'bi-house-door-fill',
        'bi-building-fill', 'bi-geo-alt-fill', 'bi-map-fill', 'bi-file-earmark-text-fill',
        'bi-card-checklist', 'bi-clipboard-data-fill', 'bi-briefcase-fill', 'bi-mortarboard-fill',
        'bi-heart-pulse-fill', 'bi-hospital-fill', 'bi-car-front-fill', 'bi-truck',
        'bi-camera-video-fill', 'bi-megaphone-fill', 'bi-shield-lock-fill', 'bi-key-fill',
        'bi-lightning-charge-fill', 'bi-droplet-fill', 'bi-basket3-fill', 'bi-cash-stack',
        'bi-credit-card-2-front-fill', 'bi-bank', 'bi-wallet2', 'bi-award-fill',
        'bi-stars', 'bi-globe', 'bi-info-circle-fill', 'bi-question-circle-fill'
    ];
@endphp

<style>
    /* --- 1. LAYOUT BASE --- */
    .command-card { background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; max-width: 1100px; margin: 0 auto; }
    .command-header { background: #0A1E33; padding: 32px 40px; border-bottom: 4px solid #E8A33D; }
    .command-header h2 { color: #ffffff; font-size: 20px; font-weight: 800; margin: 0; }
    .command-header p { color: rgba(255,255,255,0.6); font-size: 12px; margin-top: 5px; }

    /* --- 2. SECTION & GRID --- */
    .form-section { padding: 28px 40px; }
    .form-section + .form-section { border-top: 1px solid #F1F5F9; }
    .section-heading { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
    .section-number { width: 28px; height: 28px; border-radius: 8px; background: #123457; color: #E8A33D; font-size: 11px; font-weight: 900; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .section-heading h3 { font-size: 13px; font-weight: 800; color: #123457; margin: 0; letter-spacing: 0.5px; }

    .fields-container-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 20px; }
    @media (max-width: 992px) { .fields-container-grid { grid-template-columns: 1fr; } }

    .field-item-box { background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 18px; padding: 18px; position: relative; transition: 0.3s; }
    .field-item-box:hover { border-color: #E8A33D; background: #ffffff; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

    .label-command { display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 6px; }
    .input-group-custom { position: relative; display: flex; align-items: center; width: 100%; }
    .input-control-command { width: 100%; padding: 10px 15px 10px 42px; background: #ffffff; border: 1.5px solid #E2E8F0; border-radius: 12px; font-size: 12px; font-weight: 700; color: #123457; transition: 0.3s; }
    .input-icon-left { position: absolute; left: 15px; color: #94a3b8; font-size: 1rem; }

    .field-badge { display: inline-flex; align-items: center; gap: 6px; background: #123457; color: #E8A33D; padding: 4px 10px; border-radius: 8px; font-size: 8px; font-weight: 900; text-transform: uppercase; margin-bottom: 12px; }

    /* --- 3. FIX: TRASH & X COLOR (RED) --- */
    .btn-remove, .bi-trash3-fill, .bi-x-circle-fill { color: #f43f5e !important; cursor: pointer; transition: 0.2s; }
    .btn-remove:hover, .bi-trash3-fill:hover, .bi-x-circle-fill:hover { color: #be123c !important; transform: scale(1.1); }

    /* --- 4. REVISION ACCESS UI --- */
    .revision-toggle-btn { padding: 10px 20px; border-radius: 12px; border: 2px solid #e2e8f0; background: #fff; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 10px; }
    .revision-toggle-btn.is-active { border-color: #10B981; color: #10B981; background: #f0fdf4; }
    .time-chip-group { display: none; gap: 8px; margin-top: 12px; flex-wrap: wrap; }
    .time-chip-group.is-visible { display: flex; }
    .time-chip { padding: 6px 14px; border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 10px; font-weight: 700; color: #94a3b8; cursor: pointer; transition: 0.2s; }
    .time-chip:hover { border-color: #E8A33D; color: #E8A33D; }
    .time-chip.selected { background: #123457; color: #E8A33D; border-color: #123457; box-shadow: 0 4px 10px rgba(18,52,87,0.2); }

    /* --- 5. MODAL DESIGN --- */
    @keyframes modalSpringIn { 0% { opacity: 0; transform: scale(0.95) translateY(20px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
    .modal-overlay { position: fixed; inset: 0; background: rgba(10, 30, 51, 0.8); backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center; z-index: 9999; padding: 20px; }
    .modal-overlay.is-active { display: flex; }
    .modal-card { background: #fff; width: 100%; max-width: 380px; max-height: 68vh; border-radius: 28px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); animation: modalSpringIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .modal-header { background: #0A1E33; padding: 20px 24px; border-bottom: 4px solid #E8A33D; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
    .modal-header h2 { color: #fff; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .modal-header p { color: rgba(255,255,255,0.5); font-size: 9px; margin: 2px 0 0; font-weight: 700; text-transform: uppercase; }
    .modal-close { background: rgba(255,255,255,0.1); border: none; color: #94a3b8; width: 28px; height: 28px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
    .modal-close:hover { background: #f87171; color: #fff; }
    .modal-body { overflow-y: auto; flex: 1; padding: 24px; }
    .modal-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .picker-item { padding: 15px 10px; border: 1.5px solid #F1F5F9; border-radius: 16px; display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer; transition: 0.2s; text-align: center; }
    .picker-item:hover { border-color: #E8A33D; background: #FFFBEB; transform: translateY(-3px); }
    .picker-item i { font-size: 1.5rem; color: #123457; }
    .picker-item span { font-size: 8px; font-weight: 800; text-transform: uppercase; color: #64748B; }

    /* --- 5b. PARAMETER / BUNDLE PICKER (ADDITIVE, TIDAK MENGUBAH STYLE LAMA) --- */
    .picker-section-title { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin: 0 0 10px; }
    .picker-section-title.mt-4 { margin-top: 20px; }
    .picker-item-disabled { opacity: 0.4; cursor: not-allowed; }
    .picker-item-disabled:hover { border-color: #F1F5F9; background: transparent; transform: none; }
    .picker-item-disabled span { color: #cbd5e1; }

    /* --- 6. FOOTER & BUTTONS --- */
    .form-footer { padding: 25px 40px; background: #F8FAFC; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 12px; }
    .btn-command-save { background: #123457; color: #fff; border: none; padding: 12px 28px; border-radius: 14px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 20px rgba(18, 52, 87, 0.15); }
    .btn-command-save:hover { background: #E8A33D; color: #0A1E33; transform: translateY(-2px); }
    .btn-command-cancel { background: #e2e8f0; color: #475569; border: none; padding: 12px 24px; border-radius: 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; }
    .btn-command-cancel:hover { background: #d1d5db; color: #1e293b; transform: translateY(-1px); }

    .btn-add-item { width: 100%; padding: 12px; border: 2px dashed #E2E8F0; border-radius: 14px; background: #ffffff; color: #94A3B8; font-size: 10px; font-weight: 800; text-transform: uppercase; cursor: pointer; transition: 0.3s; }
    .btn-add-item:hover { border-color: #10B981; color: #10B981; background: #F0FDF4; }
    .btn-add-card { width: calc(100% - 80px); margin: 0 40px 40px; padding: 16px; border: 2px dashed #123457; border-radius: 18px; background: #F8FAFC; color: #123457; font-size: 11px; font-weight: 800; text-transform: uppercase; cursor: pointer; transition: 0.3s; }
    .btn-add-card:hover { background: #123457; color: #fff; }

    .card-icon-selector { width: 40px; height: 40px; border-radius: 12px; background: #123457; color: #E8A33D; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; transition: 0.2s; flex-shrink: 0; box-shadow: 0 4px 10px rgba(18, 52, 87, 0.2); }

    /* --- 7. DRAG & DROP HANDLE (PARAMETER LEVEL SAJA) --- */
    .drag-handle-field {
        cursor: grab;
        color: #94a3b8;
        font-size: 0.85rem;
        padding: 2px 4px;
        border-radius: 6px;
        transition: 0.2s;
        touch-action: none;
    }
    .drag-handle-field:hover { color: #123457; background: rgba(255,255,255,0.15); }
    .drag-handle-field:active { cursor: grabbing; }

    .field-sortable-ghost {
        opacity: 0.4;
        background: #FFFBEB !important;
        border: 1.5px dashed #E8A33D !important;
    }
    .field-sortable-chosen { box-shadow: 0 10px 20px rgba(18,52,87,0.10); position: relative; z-index: 5; }
    .field-sortable-drag { opacity: 0.95; }
</style>

<div class="command-card">
    <div class="command-header">
        <div class="flex items-center justify-between">
            <div>
                <h2>Arsitek Struktur Formulir</h2>
                <p>Kelola data dan inputan untuk kategori <b>{{ $category->nama ?? 'Layanan' }}</b>.</p>
            </div>
            <div class="hidden md:flex w-12 h-12 rounded-xl bg-white/10 items-center justify-center text-white border border-white/10">
                <i class="bi bi-ui-checks text-xl text-accent"></i>
            </div>
        </div>
    </div>

    {{-- Penampung Data --}}
    <div id="data-bridge" data-structure='@json($seedCards)' data-icons='@json($availableIcons)' class="hidden"></div>

    <form action="{{ route('admin.layanan.form.builder.save', [$portalCode, $svc->id, $category->id]) }}" method="POST" id="mainFormBuilder">
        @csrf

        <div class="form-section">
            <div class="section-heading">
                <div class="section-number">01</div>
                <h3>Konfigurasi Formulir</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
                <div class="space-y-6">
                    <div class="input-field-wrapper mb-0">
                        <label class="label-command">Judul Formulir Publik</label>
                        <div class="input-group-custom">
                            <input type="text" name="form_title" value="{{ $seedFormTitle }}" class="input-control-command" placeholder="Nama Formulir" required>
                            <i class="bi bi-type input-icon-left"></i>
                        </div>
                    </div>
                    <div class="input-field-wrapper mb-0">
                        <label class="label-command">Keterangan Tambahan</label>
                        <div class="input-group-custom">
                            <input type="text" name="form_description" value="{{ $seedFormDesc }}" class="input-control-command" placeholder="Deskripsi Singkat">
                            <i class="bi bi-info-circle input-icon-left"></i>
                        </div>
                    </div>
                </div>

                <!-- FITUR AKSES REVISI -->
                <div>
                    <label class="label-command">Kebijakan Revisi (Edit Data)</label>
                    <button type="button" id="toggleRevisionBtn" class="revision-toggle-btn {{ $seedEditLimit >= 0 ? 'is-active' : '' }}" onclick="toggleRevision()">
                        <i class="bi {{ $seedEditLimit >= 0 ? 'bi-check-circle-fill' : 'bi-plus-circle' }}"></i>
                        <span>{{ $seedEditLimit >= 0 ? 'Akses Revisi Aktif' : 'Tambahkan Akses Edit' }}</span>
                    </button>

                    <input type="hidden" name="edit_limit" id="edit_limit_input" value="{{ $seedEditLimit }}">

                    <div id="timeOptions" class="time-chip-group {{ $seedEditLimit >= 0 ? 'is-visible' : '' }}">
                        <div class="time-chip {{ $seedEditLimit == 1 ? 'selected' : '' }}" onclick="setEditLimit(1, this)">1 Jam</div>
                        <div class="time-chip {{ $seedEditLimit == 12 ? 'selected' : '' }}" onclick="setEditLimit(12, this)">12 Jam</div>
                        <div class="time-chip {{ $seedEditLimit == 24 ? 'selected' : '' }}" onclick="setEditLimit(24, this)">24 Jam</div>
                        <div class="time-chip {{ $seedEditLimit == 72 ? 'selected' : '' }}" onclick="setEditLimit(72, this)">3 Hari</div>
                        <div class="time-chip {{ $seedEditLimit == 168 ? 'selected' : '' }}" onclick="setEditLimit(168, this)">7 Hari</div>
                        <div class="time-chip {{ $seedEditLimit == 0 ? 'selected' : '' }}" onclick="setEditLimit(0, this)">Tanpa Batas</div>
                    </div>
                    <p class="text-[9px] text-slate-400 mt-2">Izin warga untuk memperbaiki data setelah mengirim permohonan.</p>
                </div>
            </div>
        </div>

        <div id="cardLists"></div>

        <button type="button" class="btn-add-card" onclick="addNewCard()">
            <i class="bi bi-plus-circle mr-2"></i> Tambah Kelompok Data Baru
        </button>

        <div class="form-footer">
            @if($layananFormId)
                <a href="{{ route('admin.layanan.form.categories.index', [$portalCode, $layananFormId]) }}" class="btn-command-cancel">Kembali ke Daftar</a>
            @else
                <a href="#" onclick="history.back()" class="btn-command-cancel">Kembali</a>
            @endif
            <button type="submit" class="btn-command-save" id="btnSubmit">Simpan Struktur</button>
        </div>

        <input type="hidden" name="structure" id="structureInput">
    </form>
</div>

{{-- MODAL PARAMETER / BUNDLE PICKER --}}
<div id="typeModal" class="modal-overlay" onclick="if(event.target === this) closeTypePicker()">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h2>Tambah Parameter / Bundle</h2>
                <p>Pilih data yang ingin ditambahkan</p>
            </div>
            <button type="button" class="modal-close" onclick="closeTypePicker()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="modal-body" id="typeGrid"></div>
    </div>
</div>

{{-- MODAL ICON PICKER --}}
<div id="iconModal" class="modal-overlay" onclick="if(event.target === this) closeIconPicker()">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h2>Pilih Ikon Kelompok</h2>
                <p>Identitas visual kelompok data</p>
            </div>
            <button type="button" class="modal-close" onclick="closeIconPicker()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="modal-body">
            <div class="modal-grid modal-grid-icons" id="iconGrid" style="grid-template-columns: repeat(6, 1fr);"></div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
    const bridge = document.getElementById('data-bridge');
    const initialStructure = JSON.parse(bridge.getAttribute('data-structure') || '[]');
    const availableIcons = JSON.parse(bridge.getAttribute('data-icons') || '[]');

    let state = { cards: initialStructure };

    // =====================================================================
    // 1. KAMUS PARAMETER SISTEM (Admin hanya memilih, tidak mengisi manual)
    // =====================================================================
    const PARAMETERS = [
        { key: 'nama_lengkap',  label: 'Nama Lengkap',           type: 'text',   icon: 'bi-person-fill' },
        { key: 'nik',           label: 'NIK',                    type: 'nik',    icon: 'bi-person-vcard' },
        { key: 'no_kk',         label: 'Nomor Kartu Keluarga',   type: 'nik',    icon: 'bi-card-checklist' },
        { key: 'tempat_lahir',  label: 'Tempat Lahir',           type: 'text',   icon: 'bi-geo-alt-fill' },
        { key: 'tanggal_lahir', label: 'Tanggal Lahir',          type: 'date',   icon: 'bi-calendar-event' },
        { key: 'jenis_kelamin', label: 'Jenis Kelamin',          type: 'select', icon: 'bi-people-fill', options: ['Laki-laki', 'Perempuan'] },
        { key: 'no_hp',         label: 'Nomor Handphone',        type: 'phone',  icon: 'bi-whatsapp' },
        { key: 'email',         label: 'Email',                  type: 'email',  icon: 'bi-envelope-fill' },
    ];

    const BUNDLES = [
        { key: 'bundle_address',   label: 'Bundle Alamat',  icon: 'bi-map-fill',              title: 'Data Alamat' },
        { key: 'bundle_documents', label: 'Bundle Dokumen', icon: 'bi-file-earmark-text-fill', title: 'Dokumen Persyaratan' },
    ];

    const ADDRESS_FIELDS = ['Provinsi', 'Kabupaten/Kota', 'Kecamatan', 'Kelurahan/Desa', 'Alamat Lengkap', 'RT', 'RW'];

    const FORMAT_LABELS = { image: 'Gambar (JPG/PNG/WEBP)', pdf: 'PDF', both: 'Gambar + PDF' };

    // Toggle Revisi Logic
    function toggleRevision() {
        const btn = document.getElementById('toggleRevisionBtn');
        const options = document.getElementById('timeOptions');
        const input = document.getElementById('edit_limit_input');
        const isActive = btn.classList.contains('is-active');

        if (isActive) {
            btn.classList.remove('is-active');
            btn.innerHTML = '<i class="bi bi-plus-circle"></i> <span>Tambahkan Akses Edit</span>';
            options.classList.remove('is-visible');
            input.value = -1; // Status Nonaktif
        } else {
            btn.classList.add('is-active');
            btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> <span>Akses Revisi Aktif</span>';
            options.classList.add('is-visible');
            input.value = 24; // Default 24 Jam
            document.querySelectorAll('.time-chip').forEach(c => {
                if (c.innerText.includes('24 Jam')) c.classList.add('selected');
                else c.classList.remove('selected');
            });
        }
    }

    function setEditLimit(val, el) {
        document.getElementById('edit_limit_input').value = val;
        document.querySelectorAll('.time-chip').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
    }

    // =====================================================================
    // 2. RENDER KELOMPOK (CARD) — dispatch berdasarkan tipe
    // =====================================================================
    function renderCard(card) {
        if (card.type === 'bundle_address') return renderBundleAddressCard(card);
        if (card.type === 'bundle_documents') return renderBundleDocumentsCard(card);
        return renderNormalCard(card);
    }

    function renderNormalCard(card) {
        return `
            <div class="form-section" id="card-${card.id}">
                <div class="section-heading justify-between">
                    <div class="flex items-center gap-3">
                        <div class="card-icon-selector" onclick="openIconPicker('${card.id}')">
                            <i class="bi ${card.icon || 'bi-collection-fill'}" id="icon-preview-${card.id}"></i>
                        </div>
                        <input type="text" value="${card.title}" oninput="updateCardTitle('${card.id}', this.value)"
                               class="bg-transparent border-none focus:ring-0 p-0 text-sm font-black text-primary-dark" placeholder="JUDUL KELOMPOK">
                    </div>
                    <i class="bi bi-trash3-fill btn-remove" onclick="removeCard('${card.id}')"></i>
                </div>
                <div class="fields-container-grid" id="fields-${card.id}">
                    ${card.fields.map(f => renderParameterField(card.id, f)).join('')}
                </div>
                <button type="button" class="btn-add-item" onclick="openTypePicker('${card.id}')">
                    + Tambah Parameter / Bundle
                </button>
            </div>
        `;
    }

    function renderParameterField(cardId, f) {
        const paramDef = PARAMETERS.find(p => p.key === f.parameter) || { label: f.parameter, icon: 'bi-question-circle', type: 'text' };
        return `
            <div class="field-item-box" data-field-id="${f.id}">
                <div class="flex justify-between items-start">
                    <span class="field-badge">
                        <i class="bi bi-grip-vertical drag-handle-field" title="Geser untuk urutkan"></i>
                        <i class="bi ${paramDef.icon}"></i> ${paramDef.label}
                    </span>
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer mb-2">
                            <input type="checkbox" ${f.required ? 'checked' : ''} onchange="toggleRequired('${cardId}','${f.id}')" class="w-3.5 h-3.5 accent-navy-deep">
                            <span class="text-[8px] font-bold text-slate-400 uppercase">Wajib</span>
                        </label>
                        <i class="bi bi-x-circle-fill btn-remove mb-2" onclick="removeField('${cardId}','${f.id}')"></i>
                    </div>
                </div>
                <p class="text-[10px] font-bold text-slate-500 mt-1">
                    Parameter Sistem &middot; <span style="color:#123457;">${paramDef.type}</span>
                </p>
            </div>
        `;
    }

    function renderBundleAddressCard(card) {
        return `
            <div class="form-section" id="card-${card.id}">
                <div class="section-heading justify-between">
                    <div class="flex items-center gap-3">
                        <div class="card-icon-selector" onclick="openIconPicker('${card.id}')">
                            <i class="bi ${card.icon || 'bi-map-fill'}" id="icon-preview-${card.id}"></i>
                        </div>
                        <input type="text" value="${card.title}" oninput="updateCardTitle('${card.id}', this.value)"
                               class="bg-transparent border-none focus:ring-0 p-0 text-sm font-black text-primary-dark" placeholder="JUDUL KELOMPOK">
                    </div>
                    <i class="bi bi-trash3-fill btn-remove" onclick="removeCard('${card.id}')"></i>
                </div>
                <div class="fields-container-grid">
                    ${ADDRESS_FIELDS.map(label => `
                        <div class="field-item-box" style="cursor:default;">
                            <span class="field-badge"><i class="bi bi-geo-alt-fill"></i> Bundle Alamat</span>
                            <p class="text-[11px] font-bold mt-1" style="color:#123457;">${label}</p>
                        </div>
                    `).join('')}
                </div>
                <p class="text-[9px] text-slate-400">Field bundle alamat bersifat tetap dan tidak dapat diubah.</p>
            </div>
        `;
    }

    function renderBundleDocumentsCard(card) {
        return `
            <div class="form-section" id="card-${card.id}">
                <div class="section-heading justify-between">
                    <div class="flex items-center gap-3">
                        <div class="card-icon-selector" onclick="openIconPicker('${card.id}')">
                            <i class="bi ${card.icon || 'bi-file-earmark-text-fill'}" id="icon-preview-${card.id}"></i>
                        </div>
                        <input type="text" value="${card.title}" oninput="updateCardTitle('${card.id}', this.value)"
                               class="bg-transparent border-none focus:ring-0 p-0 text-sm font-black text-primary-dark" placeholder="JUDUL KELOMPOK">
                    </div>
                    <i class="bi bi-trash3-fill btn-remove" onclick="removeCard('${card.id}')"></i>
                </div>
                <div class="fields-container-grid">
                    ${card.requirements.map(r => renderRequirement(card.id, r)).join('')}
                </div>
                <button type="button" class="btn-add-item" onclick="addRequirement('${card.id}')">
                    + Tambah Persyaratan Dokumen
                </button>
            </div>
        `;
    }

    function renderRequirement(cardId, req) {
        return `
            <div class="field-item-box">
                <div class="flex justify-between items-start">
                    <span class="field-badge"><i class="bi bi-file-earmark-arrow-up"></i> ${FORMAT_LABELS[req.format] || 'Gambar'}</span>
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer mb-2">
                            <input type="checkbox" ${req.wajib ? 'checked' : ''} onchange="toggleRequirementWajib('${cardId}','${req.id}')" class="w-3.5 h-3.5 accent-navy-deep">
                            <span class="text-[8px] font-bold text-slate-400 uppercase">Wajib</span>
                        </label>
                        <i class="bi bi-x-circle-fill btn-remove mb-2" onclick="removeRequirement('${cardId}','${req.id}')"></i>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="input-field-wrapper mb-0">
                        <label class="label-command">Nama Dokumen</label>
                        <div class="input-group-custom">
                            <input type="text" value="${req.nama_dokumen}" oninput="updateRequirementField('${cardId}','${req.id}','nama_dokumen', this.value)" class="input-control-command" placeholder="Contoh: KTP">
                            <i class="bi bi-file-earmark-text input-icon-left"></i>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="input-field-wrapper mb-0">
                            <label class="label-command">Format File</label>
                            <select onchange="updateRequirementField('${cardId}','${req.id}','format', this.value)" class="input-control-command" style="padding-left:15px;">
                                <option value="image" ${req.format === 'image' ? 'selected' : ''}>Gambar</option>
                                <option value="pdf" ${req.format === 'pdf' ? 'selected' : ''}>PDF</option>
                                <option value="both" ${req.format === 'both' ? 'selected' : ''}>Gambar + PDF</option>
                            </select>
                        </div>
                        <div class="input-field-wrapper mb-0">
                            <label class="label-command">Maks Ukuran (MB)</label>
                            <div class="input-group-custom">
                                <input type="number" min="1" max="20" value="${req.max_size_mb}" oninput="updateRequirementField('${cardId}','${req.id}','max_size_mb', this.value)" class="input-control-command" style="padding-left:15px;">
                            </div>
                        </div>
                        <div class="input-field-wrapper mb-0">
                            <label class="label-command">Maks Jumlah File</label>
                            <div class="input-group-custom">
                                <input type="number" min="1" max="10" value="${req.max_files}" oninput="updateRequirementField('${cardId}','${req.id}','max_files', this.value)" class="input-control-command" style="padding-left:15px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // =====================================================================
    // 3. MODAL PICKER (Parameter + Bundle)
    // =====================================================================
    let activeCardId = null;
    function openTypePicker(cardId) { activeCardId = cardId; renderTypePicker(); document.getElementById('typeModal').classList.add('is-active'); }
    function closeTypePicker() { document.getElementById('typeModal').classList.remove('is-active'); }
    function openIconPicker(cardId) { activeCardId = cardId; renderIconPicker(); document.getElementById('iconModal').classList.add('is-active'); }
    function closeIconPicker() { document.getElementById('iconModal').classList.remove('is-active'); }

    function hasBundle(key) { return state.cards.some(c => c.type === key); }
    function isParameterUsed(key) { return state.cards.some(c => c.type === 'normal' && c.fields.some(f => f.parameter === key)); }

    function renderTypePicker() {
        const grid = document.getElementById('typeGrid');

        const paramItems = PARAMETERS.map(p => {
            const used = isParameterUsed(p.key);
            return `<div class="picker-item ${used ? 'picker-item-disabled' : ''}" ${used ? '' : `onclick="addParameterField('${p.key}')"`}>
                        <i class="bi ${p.icon}"></i><span>${p.label}${used ? ' (Dipakai)' : ''}</span>
                    </div>`;
        }).join('');

        const bundleItems = BUNDLES.map(b => {
            const used = hasBundle(b.key);
            return `<div class="picker-item ${used ? 'picker-item-disabled' : ''}" ${used ? '' : `onclick="addBundle('${b.key}')"`}>
                        <i class="bi ${b.icon}"></i><span>${b.label}${used ? ' (Sudah Ada)' : ''}</span>
                    </div>`;
        }).join('');

        grid.innerHTML = `
            <div class="picker-section-title">Parameter Data</div>
            <div class="modal-grid">${paramItems}</div>
            <div class="picker-section-title mt-4">Bundle Kelompok</div>
            <div class="modal-grid">${bundleItems}</div>
        `;
    }

    function renderIconPicker() {
        const grid = document.getElementById('iconGrid');
        grid.innerHTML = availableIcons.map(icon => `<div class="picker-item" onclick="updateCardIcon('${icon}')"><i class="bi ${icon}"></i></div>`).join('');
    }

    // =====================================================================
    // 4. STATE MUTATORS
    // =====================================================================
    function addParameterField(key) {
        const card = state.cards.find(c => c.id === activeCardId);
        if (!card || card.type !== 'normal') { closeTypePicker(); return; }
        if (isParameterUsed(key)) { closeTypePicker(); return; }
        card.fields.push({ id: 'f' + Date.now(), type: 'parameter', parameter: key, required: true });
        refresh(); closeTypePicker();
    }

    function addBundle(key) {
        if (hasBundle(key)) { closeTypePicker(); return; }
        const bundleDef = BUNDLES.find(b => b.key === key);
        if (key === 'bundle_address') {
            state.cards.push({ id: 'c' + Date.now(), type: 'bundle_address', title: bundleDef.title, icon: bundleDef.icon });
        } else if (key === 'bundle_documents') {
            state.cards.push({ id: 'c' + Date.now(), type: 'bundle_documents', title: bundleDef.title, icon: bundleDef.icon, requirements: [] });
        }
        refresh(); closeTypePicker();
    }

    function addRequirement(cardId) {
        const card = state.cards.find(c => c.id === cardId);
        card.requirements.push({ id: 'r' + Date.now(), nama_dokumen: '', format: 'image', wajib: true, max_size_mb: 2, max_files: 1 });
        refresh();
    }

    function updateRequirementField(cardId, reqId, key, val) {
        const card = state.cards.find(c => c.id === cardId);
        const req = card.requirements.find(r => r.id === reqId);
        if (key === 'max_size_mb') val = Math.max(1, Math.min(20, parseInt(val) || 1));
        if (key === 'max_files') val = Math.max(1, Math.min(10, parseInt(val) || 1));
        req[key] = val;
        if (key === 'max_size_mb' || key === 'max_files') refresh();
    }

    function toggleRequirementWajib(cardId, reqId) {
        const card = state.cards.find(c => c.id === cardId);
        const req = card.requirements.find(r => r.id === reqId);
        req.wajib = !req.wajib;
        refresh();
    }

    function removeRequirement(cardId, reqId) {
        const card = state.cards.find(c => c.id === cardId);
        card.requirements = card.requirements.filter(r => r.id !== reqId);
        refresh();
    }

    function updateCardIcon(icon) {
        const card = state.cards.find(c => c.id === activeCardId);
        if (card) { card.icon = icon; document.getElementById(`icon-preview-${activeCardId}`).className = `bi ${icon}`; }
        closeIconPicker();
    }

    function addNewCard() { state.cards.push({ id: 'c' + Date.now(), type: 'normal', title: 'Kelompok Baru', icon: 'bi-collection-fill', fields: [] }); refresh(); }
    function removeCard(id) { if (confirm('Hapus Kelompok Data?')) { state.cards = state.cards.filter(c => c.id !== id); refresh(); } }
    function removeField(cid, fid) { const card = state.cards.find(c => c.id === cid); card.fields = card.fields.filter(f => f.id !== fid); refresh(); }
    function updateCardTitle(id, val) { state.cards.find(c => c.id === id).title = val; }
    function toggleRequired(cid, fid) { const f = state.cards.find(c => c.id === cid).fields.find(f => f.id === fid); f.required = !f.required; }

    // =====================================================================
    // 5. DRAG & DROP (SortableJS) — HANYA untuk urutan PARAMETER di dalam
    //    kelompok bertipe 'normal'. Card/kelompok TIDAK bisa dipindah.
    //    Bundle Alamat & Bundle Dokumen TIDAK diberi drag & drop.
    // =====================================================================
    const fieldSortableInstances = {}; // { [cardId]: Sortable instance }

    function initFieldSortable(cardId) {
        const container = document.getElementById(`fields-${cardId}`);
        if (!container) return;

        // refresh() mengganti innerHTML #cardLists secara total, sehingga
        // node lama (dan instance Sortable yang menempel padanya) sudah hilang.
        // Hancurkan referensi lama sebelum membuat yang baru.
        if (fieldSortableInstances[cardId]) {
            fieldSortableInstances[cardId].destroy();
            delete fieldSortableInstances[cardId];
        }

        fieldSortableInstances[cardId] = new Sortable(container, {
            handle: '.drag-handle-field',   // hanya icon ⋮⋮ pada parameter yang memicu drag
            draggable: '.field-item-box',   // hanya parameter yang boleh dipindah
            animation: 180,
            ghostClass: 'field-sortable-ghost',
            chosenClass: 'field-sortable-chosen',
            dragClass: 'field-sortable-drag',
            onEnd: function () {
                const card = state.cards.find(c => c.id === cardId);
                if (!card || card.type !== 'normal') return;

                // Sinkronkan urutan card.fields sesuai urutan DOM final
                const newOrderIds = Array.from(container.querySelectorAll('.field-item-box'))
                    .map(el => el.getAttribute('data-field-id'));

                card.fields = newOrderIds
                    .map(id => card.fields.find(f => f.id === id))
                    .filter(Boolean);
            }
        });
    }

    function initAllFieldSortables() {
        // Bersihkan instance milik card yang sudah dihapus dari state
        Object.keys(fieldSortableInstances).forEach(cardId => {
            if (!state.cards.some(c => c.id === cardId)) {
                fieldSortableInstances[cardId].destroy();
                delete fieldSortableInstances[cardId];
            }
        });

        // Inisialisasi ulang untuk setiap card bertipe 'normal' saja.
        // Bundle Alamat (fixed) & Bundle Dokumen (requirement, bukan parameter)
        // sengaja tidak diberi Sortable.
        state.cards
            .filter(c => c.type === 'normal')
            .forEach(c => initFieldSortable(c.id));
    }

    function refresh() {
        document.getElementById('cardLists').innerHTML = state.cards.map(c => renderCard(c)).join('');
        initAllFieldSortables(); // wajib dipanggil ulang tiap refresh agar drag & drop tetap aktif
    }

    document.getElementById('mainFormBuilder').addEventListener('submit', function () {
        document.getElementById('structureInput').value = JSON.stringify(state.cards);
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Menyimpan...';
        btn.disabled = true;
    });

    refresh();
</script>
@endpush

@endsection