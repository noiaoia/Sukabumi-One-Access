{{-- resources/views/components/user/modal-edit-submission.blade.php --}}
<div id="modalEditSub" class="edit-sub-overlay" onclick="if(event.target === this) closeEditSubModal()">
    <div class="edit-sub-card">
        <div class="edit-sub-header">
            <div class="flex items-center gap-3">
                <div class="edit-sub-icon"><i class="bi bi-pencil-square"></i></div>
                <div>
                    <h2 class="text-white font-bold uppercase">Revisi Pengajuan</h2>
                    <p id="edit-nomor-display" class="text-white/50 font-mono">#SOA...</p>
                </div>
            </div>
            <button type="button" onclick="closeEditSubModal()" class="text-white/50 hover:text-white"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="edit-sub-body custom-scrollbar-sub">
            <!-- ALASAN REVISI -->
            <div id="edit-reason-box" class="mb-4 p-3 bg-orange-50 border border-orange-100 rounded-xl flex gap-2.5 hidden">
                <i class="bi bi-exclamation-triangle-fill text-orange-600 text-sm"></i>
                <div>
                    <p class="text-[8.5px] font-black text-orange-600 uppercase tracking-widest mb-0.5">Catatan Perbaikan:</p>
                    <p id="edit-catatan-admin" class="text-[11px] text-orange-800 font-bold leading-relaxed"></p>
                </div>
            </div>

            <!-- ALERT BOX (pengganti alert()) -->
            <div id="editSubAlertBox" class="edit-sub-alert hidden"></div>

            <form id="formEditSubmission" enctype="multipart/form-data" onsubmit="return false;">
                @csrf
                <div id="dynamic-edit-fields">
                    <!-- Fields dirender dinamis via JS mengikuti structure Form Builder -->
                </div>
            </form>
        </div>

        <div class="edit-sub-footer">
            <button type="button" class="btn-cancel" id="btnCancelEdit" onclick="closeEditSubModal()">Batal</button>
            <button type="button" class="btn-save" id="btnUpdateSubmit" onclick="submitRevision()">
                <span class="btn-save-text">Kirim Perbaikan</span>
                <i class="bi bi-arrow-repeat animate-spin btn-save-loader hidden"></i>
            </button>
        </div>
    </div>
</div>

@once
@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/intlTelInput.min.js"></script>
@endpush
@endonce

<style>
    .edit-sub-overlay { position: fixed; inset: 0; background: rgba(10,30,51,0.85); backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center; z-index: 11000; padding: 20px; opacity: 0; transition: 0.3s; }
    .edit-sub-overlay.active { display: flex; opacity: 1; }
    .edit-sub-card { background: #fff; width: 100%; max-width: 480px; max-height: 85vh; border-radius: 22px; display: flex; flex-direction: column; overflow: hidden; transform: translateY(20px); transition: 0.3s; }
    .edit-sub-overlay.active .edit-sub-card { transform: translateY(0); }
    .edit-sub-header { background: #0A1E33; padding: 14px 18px; border-bottom: 3px solid #E8A33D; display: flex; align-items: center; justify-content: space-between; }
    .edit-sub-icon { width: 32px; height: 32px; background: rgba(232,163,61,0.15); color: #E8A33D; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; }
    .edit-sub-header h2 { font-size: 0.8rem; }
    .edit-sub-header p { font-size: 9px; }
    .edit-sub-body { padding: 18px; overflow-y: auto; flex: 1; }
    .edit-sub-footer { padding: 12px 18px; background: #F8FAFC; border-top: 1px solid #eee; display: flex; gap: 8px; }

    /* Alert box (pengganti alert()) */
    .edit-sub-alert { border-radius: 0.75rem; padding: 0.6rem 0.85rem; margin-bottom: 0.9rem; font-size: 0.7rem; font-weight: 700; line-height: 1.4; }
    .edit-sub-alert.hidden { display: none; }
    .edit-sub-alert.edit-alert-error { background: #FEF2F2; border: 1.5px solid #FCA5A5; color: #B91C1C; }
    .edit-sub-alert.edit-alert-success { background: #ECFDF5; border: 1.5px solid #6EE7B7; color: #047857; }
    .edit-sub-alert ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 3px; }
    .edit-sub-alert li { display: flex; align-items: flex-start; gap: 6px; }
    .edit-sub-alert li::before { content: "\2022"; flex-shrink: 0; }

    /* Kelompok field lebih ringkas */
    #dynamic-edit-fields { display: flex; flex-direction: column; gap: 1.1rem; }
    #dynamic-edit-fields h3 { font-size: 0.7rem; margin-bottom: 0.65rem; }
    #dynamic-edit-fields .grid { gap: 0.6rem !important; }

    /* Reusable input styles - konsisten dengan form.blade.php (.input-group-custom) */
    .edit-input-group { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; padding: 0.55rem 0.75rem; transition: all 0.3s ease; }
    .edit-input-group:focus-within { border-color: #E8A33D; background: #fff; box-shadow: 0 0 0 3px rgba(232, 163, 61, 0.05); }
    .edit-input-group label { display: block; font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; color: #94a3b8; margin-bottom: 0.2rem; }
    .edit-input-group input,
    .edit-input-group select,
    .edit-input-group textarea { width: 100%; background: transparent; border: none; outline: none; font-size: 0.8rem; font-weight: 700; color: #123457; font-family: inherit; }
    .edit-input-group textarea { resize: vertical; }

    /* Upload area - disamakan dengan form.blade.php, dirapatkan */
    .edit-upload-box { border: 2px dashed #e2e8f0; border-radius: 1rem; padding: 1rem; text-align: center; transition: all 0.3s ease; cursor: pointer; position: relative; background: #f8fafc; }
    .edit-upload-box:hover { border-color: #E8A33D; background: #fffef9; }
    .edit-upload-box i.bi-cloud-arrow-up-fill { font-size: 1.5rem !important; margin-bottom: 4px !important; }
    .edit-preview-wrap { position: relative; display: none; }
    .edit-preview-img { width: 100%; max-height: 120px; object-fit: contain; border-radius: 0.75rem; display: block; }
    .edit-preview-file-chip { display: none; align-items: center; gap: 8px; background: #fff; border: 1.5px solid #e2e8f0; border-radius: 0.65rem; padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 700; color: #123457; }
    .edit-remove-preview-btn { position: absolute; top: -8px; right: -8px; width: 24px; height: 24px; border-radius: 50%; background: #ef4444; color: #fff; border: 2px solid #fff; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.18); z-index: 5; }
    .edit-remove-preview-btn:hover { background: #dc2626; }
    .edit-doc-hint { font-size: 8.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em; margin-top: 5px; }
    .edit-doc-existing { display: flex; align-items: center; gap: 6px; background: #ECFDF5; border: 1.5px solid #A7F3D0; color: #047857; border-radius: 0.6rem; padding: 5px 9px; font-size: 9.5px; font-weight: 700; margin-bottom: 6px; text-decoration: none; }
    .edit-doc-existing:hover { background: #D1FAE5; }
    .edit-doc-existing i { flex-shrink: 0; }

    .edit-loading-box { text-align: center; padding: 30px 0; color: #94a3b8; }

    /* Skeleton shimmer loader (mengikuti gaya modal-preview-submission) */
    @keyframes editShimmer { 0% { background-position: 100% 0; } 100% { background-position: 0 0; } }
    .edit-sk-shimmer { background: linear-gradient(90deg, #edf1f5 25%, #f6f8fa 37%, #edf1f5 63%); background-size: 400% 100%; animation: editShimmer 1.4s ease infinite; border-radius: 8px; }
    .edit-sk-wrap { display: flex; flex-direction: column; gap: 1.1rem; }
    .edit-sk-heading { height: 10px; width: 38%; margin-bottom: 10px; }
    .edit-sk-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; }
    .edit-sk-field { height: 42px; border-radius: 0.75rem; }
    .edit-sk-field.full { grid-column: span 2 / span 2; }
    .edit-sk-doc { height: 84px; border-radius: 1rem; }

    .edit-iti { width: 100% !important; }
    .edit-iti__tel-input,
    .edit-input-group .iti input { width: 100% !important; height: 24px; padding-left: 72px !important; font-size: 0.8rem; font-weight: 700; color: #123457; background: transparent; }
    .edit-input-group .iti { width: 100%; }
    .edit-input-group .iti__flag-container { left: 0; top: 50%; transform: translateY(-50%); }
    .edit-input-group .iti__selected-country { height: 100%; display: flex; align-items: center; }
    .edit-input-group .iti__selected-dial-code { margin-left: 5px; font-size: 0.8rem; font-weight: 700; color: #123457; }

    .btn-cancel { flex: 1; padding: 10px; border-radius: 10px; border: 1px solid #ddd; font-weight: 700; font-size: 11px; cursor: pointer; background: #fff; }
    .btn-cancel:disabled { opacity: 0.6; cursor: not-allowed; }
    .btn-save { flex: 1.5; padding: 10px; border-radius: 10px; border: none; background: #123457; color: #fff; font-weight: 800; font-size: 11px; cursor: pointer; text-transform: uppercase; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-save:disabled { opacity: 0.7; cursor: not-allowed; }
</style>

<script>
(function () {
    'use strict';

    /*
    |--------------------------------------------------------------------------
    | PARAMETER MAP (harus identik dengan resources/views/pages/user/form.blade.php)
    |--------------------------------------------------------------------------
    | Struktur Form Builder hanya menyimpan "parameter" key. Modal ini me-render
    | field mengikuti map yang sama seperti form pengajuan asli, sehingga
    | perilaku (tipe input, span layout, validasi klien) tetap konsisten.
    */
    const PARAM_MAP = {
        nama_lengkap:  { label: 'Nama Lengkap',                 input: 'text',     span: 2 },
        nik:           { label: 'NIK',                          input: 'text',     span: 1, numeric16: true, placeholder: '3277xxxxxxxx' },
        no_kk:         { label: 'Nomor Kartu Keluarga',         input: 'text',     span: 1, numeric16: true, placeholder: '3277xxxxxxxx' },
        tempat_lahir:  { label: 'Tempat Lahir',                 input: 'text',     span: 1 },
        tanggal_lahir: { label: 'Tanggal Lahir',                input: 'date',     span: 1 },
        jenis_kelamin: { label: 'Jenis Kelamin',                input: 'select',   span: 1, options: ['Laki-laki', 'Perempuan'] },
        no_hp:         { label: 'Nomor Handphone (WhatsApp)',   input: 'phone',    span: 1 },
        email:         { label: 'Email',                        input: 'email',    span: 1, placeholder: 'contoh@mail.com' },
        keterangan:    { label: 'Keterangan Tambahan',          input: 'textarea', span: 2 },
    };

    const FORMAT_ACCEPT_MAP = { image: 'image/*', pdf: 'application/pdf', both: 'image/*,application/pdf' };
    const FORMAT_LABEL_MAP  = { image: 'Gambar (JPG/PNG/WEBP)', pdf: 'PDF', both: 'Gambar atau PDF' };
    const WILAYAH_BASE = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    let currentEditId = null;
    let currentEditData = null;
    let isSubmitting = false;
    let isLoadingData = false;
    let phoneWidgets = [];      // { iti, hiddenInput }
    let addressRefreshHandlers = {};

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */
    function el(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined && text !== null) node.textContent = text;
        return node;
    }

    /**
     * Skeleton shimmer loader — mengikuti gaya visual skeleton pada
     * modal-preview-submission.blade.php, disesuaikan dengan layout field form.
     */
    function buildEditSkeleton() {
        const wrap = el('div', 'edit-sk-wrap');

        // Kartu field biasa: judul + 2 kolom input
        for (let i = 0; i < 2; i++) {
            const card = el('div', '');
            card.appendChild(el('div', 'edit-sk-shimmer edit-sk-heading'));

            const grid = el('div', 'edit-sk-grid');
            grid.appendChild(el('div', 'edit-sk-shimmer edit-sk-field full'));
            grid.appendChild(el('div', 'edit-sk-shimmer edit-sk-field'));
            grid.appendChild(el('div', 'edit-sk-shimmer edit-sk-field'));
            card.appendChild(grid);

            wrap.appendChild(card);
        }

        // Kartu dokumen: judul + 2 kotak upload
        const docCard = el('div', '');
        docCard.appendChild(el('div', 'edit-sk-shimmer edit-sk-heading'));
        const docGrid = el('div', 'edit-sk-grid');
        docGrid.appendChild(el('div', 'edit-sk-shimmer edit-sk-doc'));
        docGrid.appendChild(el('div', 'edit-sk-shimmer edit-sk-doc'));
        docCard.appendChild(docGrid);
        wrap.appendChild(docCard);

        return wrap;
    }

    function clearEditAlert() {
        const box = document.getElementById('editSubAlertBox');
        box.innerHTML = '';
        box.classList.add('hidden');
        box.classList.remove('edit-alert-error', 'edit-alert-success');
    }

    function showEditAlert(messages, type) {
        type = type || 'error';
        const box = document.getElementById('editSubAlertBox');
        box.innerHTML = '';
        box.classList.remove('hidden');
        box.classList.toggle('edit-alert-error', type === 'error');
        box.classList.toggle('edit-alert-success', type === 'success');

        const list = document.createElement('ul');
        (Array.isArray(messages) ? messages : [messages]).forEach(function (msg) {
            const li = document.createElement('li');
            li.textContent = msg;
            list.appendChild(li);
        });
        box.appendChild(list);
        box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /**
     * Menerjemahkan Response fetch yang gagal (non-2xx) menjadi pesan yang
     * ramah pengguna, mencakup 422 / 403 / 404 / 419 / 500 dan error jaringan.
     */
    async function parseErrorResponse(res) {
        let payload = null;
        try { payload = await res.json(); } catch (e) { /* body bukan JSON / kosong */ }

        if (res.status === 422) {
            const errors = payload && payload.errors ? Object.values(payload.errors).flat() : null;
            return errors && errors.length ? errors : [payload && payload.message ? payload.message : 'Data yang Anda kirim belum valid.'];
        }
        if (res.status === 403) {
            return ['Anda tidak memiliki akses untuk melakukan aksi ini.'];
        }
        if (res.status === 404) {
            return ['Data pengajuan tidak ditemukan atau sudah tidak dapat direvisi.'];
        }
        if (res.status === 419) {
            return ['Sesi Anda telah berakhir. Silakan muat ulang halaman lalu coba lagi.'];
        }
        if (res.status >= 500) {
            return ['Terjadi kesalahan pada server. Silakan coba beberapa saat lagi.'];
        }
        return [payload && payload.message ? payload.message : 'Terjadi kesalahan yang tidak diketahui.'];
    }

    function isNetworkError(err) {
        return err instanceof TypeError;
    }

    /*
    |--------------------------------------------------------------------------
    | OPEN / CLOSE MODAL
    |--------------------------------------------------------------------------
    */
    window.openEditSubmission = function (id) {
        currentEditId = id;
        currentEditData = null;
        isSubmitting = false;
        isLoadingData = true;
        phoneWidgets = [];
        addressRefreshHandlers = {};

        const modal = document.getElementById('modalEditSub');
        const fieldContainer = document.getElementById('dynamic-edit-fields');
        const reasonBox = document.getElementById('edit-reason-box');

        clearEditAlert();
        reasonBox.classList.add('hidden');
        document.getElementById('edit-nomor-display').textContent = '#...';

        fieldContainer.innerHTML = '';
        fieldContainer.appendChild(buildEditSkeleton());

        modal.classList.add('active');
        document.body.classList.add('modal-open');

        fetch(`/riwayat-pengajuan/${id}/edit-data`, {
            headers: { 'Accept': 'application/json' }
        })
            .then(async function (res) {
                if (!res.ok) {
                    const messages = await parseErrorResponse(res);
                    throw new Error(messages.join(' '));
                }
                return res.json();
            })
            .then(function (data) {
                currentEditData = data;
                isLoadingData = false;

                document.getElementById('edit-nomor-display').textContent = '#' + (data.nomor_pengajuan || '-');

                if (data.catatan_admin) {
                    document.getElementById('edit-catatan-admin').textContent = data.catatan_admin;
                    reasonBox.classList.remove('hidden');
                }

                let structure = data.structure;
                if (typeof structure === 'string') {
                    try { structure = JSON.parse(structure); } catch (e) { structure = []; }
                }
                structure = Array.isArray(structure) ? structure : [];

                renderStructure(structure, data);
            })
            .catch(function (err) {
                isLoadingData = false;
                fieldContainer.innerHTML = '';
                const msg = isNetworkError(err)
                    ? 'Gagal terhubung ke server. Periksa koneksi internet Anda dan coba lagi.'
                    : (err.message || 'Gagal memuat data pengajuan.');
                showEditAlert([msg]);

                const retryBox = el('div', 'text-center py-6');
                const retryBtn = el('button', 'btn-save', 'Muat Ulang');
                retryBtn.type = 'button';
                retryBtn.style.maxWidth = '200px';
                retryBtn.style.margin = '0 auto';
                retryBtn.addEventListener('click', function () { window.openEditSubmission(id); });
                retryBox.appendChild(retryBtn);
                fieldContainer.appendChild(retryBox);
            });
    };

    window.closeEditSubModal = function () {
        if (isSubmitting) return; // cegah menutup modal saat sedang mengirim
        document.getElementById('modalEditSub').classList.remove('active');
        document.body.classList.remove('modal-open');
        destroyPhoneWidgets();
    };

    /*
    |--------------------------------------------------------------------------
    | RENDER STRUCTURE (mengikuti tipe kelompok yang sama dengan form.blade.php)
    |--------------------------------------------------------------------------
    */
    function renderStructure(structure, data) {
        const container = document.getElementById('dynamic-edit-fields');
        container.innerHTML = '';
        destroyPhoneWidgets();
        addressRefreshHandlers = {};

        if (!structure.length) {
            container.appendChild(el('p', 'text-xs text-slate-400 text-center py-6', 'Struktur formulir tidak tersedia.'));
            return;
        }

        structure.forEach(function (card) {
            const type = card.type || 'normal';
            let cardEl = null;

            if (type === 'normal') {
                cardEl = buildNormalCard(card, data);
            } else if (type === 'bundle_address') {
                cardEl = buildAddressCard(card, data);
            } else if (type === 'bundle_documents') {
                cardEl = buildDocumentsCard(card, data);
            }

            if (cardEl) container.appendChild(cardEl);
        });

        // Batasi NIK / No. KK maksimal 16 digit angka
        container.querySelectorAll('[data-numeric16="1"]').forEach(function (input) {
            input.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
            });
        });

        // Batasi RT / RW maksimal 3 digit angka
        container.querySelectorAll('[data-numeric3="1"]').forEach(function (input) {
            input.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3);
            });
        });

        // Inisialisasi widget nomor HP setelah elemen benar-benar berada di DOM
        phoneWidgets.forEach(initPhoneWidget);

        // Jalankan prefill cascading wilayah jika ada bundle alamat
        Object.values(addressRefreshHandlers).forEach(function (fn) { fn(); });
    }

    function findValue(data, parameter) {
        const found = (data.values || []).find(function (v) { return v.parameter === parameter; });
        return found ? found.value : '';
    }

    /*
    |--------------------------------------------------------------------------
    | CARD: NORMAL (parameter fields)
    |--------------------------------------------------------------------------
    */
    function buildNormalCard(card, data) {
        const section = el('div', '');

        const heading = el('h3', 'text-xs font-black text-primary uppercase mb-2 flex items-center gap-2');
        const icon = el('i', 'bi ' + (card.icon || 'bi-collection-fill') + ' text-accent');
        heading.appendChild(icon);
        heading.appendChild(document.createTextNode(' ' + (card.title || 'Kelompok Data')));
        section.appendChild(heading);

        const grid = el('div', 'grid grid-cols-1 md:grid-cols-2 gap-3');

        const fields = card.fields || [];
        const fieldCount = fields.length;
        let rowUsedColumns = 0;

        fields.forEach(function (field, fieldIndex) {
            const pKey = field.parameter;
            const def = pKey ? PARAM_MAP[pKey] : null;
            if (!def) return; // parameter tidak dikenali, diabaikan sama seperti form asli

            const isRequired = !!field.required;
            const span = def.span || 1;
            const isLastField = fieldIndex === fieldCount - 1;
            const isFullWidth = span === 2 || (isLastField && rowUsedColumns === 0);

            if (isFullWidth || span === 2) {
                rowUsedColumns = 0;
            } else {
                rowUsedColumns += span;
                if (rowUsedColumns >= 2) rowUsedColumns = 0;
            }

            const wrap = el('div', 'edit-input-group' + (isFullWidth ? ' md:col-span-2' : ''));
            const label = el('label', null, def.label + (isRequired ? ' *' : ''));
            wrap.appendChild(label);

            const currentVal = findValue(data, pKey);

            if (def.input === 'textarea') {
                const textarea = document.createElement('textarea');
                textarea.name = pKey;
                textarea.rows = 3;
                textarea.value = currentVal;
                if (isRequired) textarea.required = true;
                wrap.appendChild(textarea);
            } else if (def.input === 'select') {
                const select = document.createElement('select');
                select.name = pKey;
                if (isRequired) select.required = true;
                const emptyOpt = document.createElement('option');
                emptyOpt.value = '';
                emptyOpt.textContent = 'Pilih ' + def.label;
                select.appendChild(emptyOpt);
                (def.options || []).forEach(function (opt) {
                    const o = document.createElement('option');
                    o.value = opt;
                    o.textContent = opt;
                    if (opt === currentVal) o.selected = true;
                    select.appendChild(o);
                });
                wrap.appendChild(select);
            } else if (def.input === 'phone') {
                const display = document.createElement('input');
                display.type = 'tel';
                display.placeholder = '812-3456-7890';
                if (isRequired) display.required = true;

                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = pKey;
                hidden.value = currentVal;

                wrap.appendChild(display);
                wrap.appendChild(hidden);

                phoneWidgets.push({ displayEl: display, hiddenEl: hidden, initialValue: currentVal, iti: null });
            } else if (def.input === 'date') {
                const input = document.createElement('input');
                input.type = 'date';
                input.name = pKey;
                input.value = currentVal;
                if (isRequired) input.required = true;
                wrap.appendChild(input);
            } else if (def.input === 'email') {
                const input = document.createElement('input');
                input.type = 'email';
                input.name = pKey;
                input.value = currentVal;
                input.placeholder = def.placeholder || '';
                if (isRequired) input.required = true;
                wrap.appendChild(input);
            } else {
                const input = document.createElement('input');
                input.type = 'text';
                input.name = pKey;
                input.value = currentVal;
                input.placeholder = def.placeholder || '';
                if (isRequired) input.required = true;
                if (def.numeric16) {
                    input.maxLength = 16;
                    input.inputMode = 'numeric';
                    input.dataset.numeric16 = '1';
                }
                wrap.appendChild(input);
            }

            grid.appendChild(wrap);
        });

        section.appendChild(grid);
        return section;
    }

    /*
    |--------------------------------------------------------------------------
    | CARD: BUNDLE ALAMAT
    |--------------------------------------------------------------------------
    */
    function buildAddressCard(card, data) {
        const section = el('div', '');

        const heading = el('h3', 'text-xs font-black text-primary uppercase mb-2 flex items-center gap-2');
        const icon = el('i', 'bi ' + (card.icon || 'bi-map-fill') + ' text-accent');
        heading.appendChild(icon);
        heading.appendChild(document.createTextNode(' ' + (card.title || 'Data Alamat Lengkap')));
        section.appendChild(heading);

        const grid = el('div', 'grid grid-cols-1 md:grid-cols-2 gap-3');

        function makeSelectGroup(label, name, disabled) {
            const wrap = el('div', 'edit-input-group');
            wrap.appendChild(el('label', null, label));
            const select = document.createElement('select');
            select.name = name;
            select.id = 'edit_' + name;
            select.required = true;
            select.disabled = !!disabled;
            const emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.textContent = 'Pilih ' + label;
            select.appendChild(emptyOpt);
            wrap.appendChild(select);

            const hiddenName = document.createElement('input');
            hiddenName.type = 'hidden';
            hiddenName.name = name + '_name';
            hiddenName.id = 'edit_' + name + '_name';
            wrap.appendChild(hiddenName);

            grid.appendChild(wrap);
            return select;
        }

        const provinsiSelect = makeSelectGroup('Provinsi', 'provinsi', false);
        const kotaSelect = makeSelectGroup('Kabupaten / Kota', 'kota', true);
        const kecamatanSelect = makeSelectGroup('Kecamatan', 'kecamatan', true);
        const kelurahanSelect = makeSelectGroup('Kelurahan / Desa', 'kelurahan', true);

        const alamatWrap = el('div', 'edit-input-group md:col-span-2');
        alamatWrap.appendChild(el('label', null, 'Alamat Lengkap'));
        const alamatInput = document.createElement('input');
        alamatInput.type = 'text';
        alamatInput.name = 'alamat_lengkap';
        alamatInput.placeholder = 'Nama jalan, nomor rumah';
        alamatInput.required = true;
        alamatInput.value = findValue(data, 'alamat_lengkap');
        alamatWrap.appendChild(alamatInput);
        grid.appendChild(alamatWrap);

        function makeRtRwGroup(label, name) {
            const wrap = el('div', 'edit-input-group');
            wrap.appendChild(el('label', null, label));
            const input = document.createElement('input');
            input.type = 'text';
            input.name = name;
            input.maxLength = 3;
            input.inputMode = 'numeric';
            input.placeholder = '001';
            input.required = true;
            input.dataset.numeric3 = '1';
            input.value = findValue(data, name);
            wrap.appendChild(input);
            grid.appendChild(wrap);
        }
        makeRtRwGroup('RT', 'rt');
        makeRtRwGroup('RW', 'rw');

        section.appendChild(grid);

        // Simpan handler prefill + cascading untuk dijalankan setelah elemen tampil di DOM
        addressRefreshHandlers['address'] = function () {
            loadAddressCascade({
                provinsiSelect: provinsiSelect,
                kotaSelect: kotaSelect,
                kecamatanSelect: kecamatanSelect,
                kelurahanSelect: kelurahanSelect,
                existing: {
                    provinsi: findValue(data, 'provinsi'),
                    provinsi_name: findValue(data, 'provinsi_name'),
                    kota: findValue(data, 'kota'),
                    kota_name: findValue(data, 'kota_name'),
                    kecamatan: findValue(data, 'kecamatan'),
                    kecamatan_name: findValue(data, 'kecamatan_name'),
                    kelurahan: findValue(data, 'kelurahan'),
                    kelurahan_name: findValue(data, 'kelurahan_name'),
                }
            });
        };

        return section;
    }

    async function loadAddressCascade(refs) {
        const { provinsiSelect, kotaSelect, kecamatanSelect, kelurahanSelect, existing } = refs;

        function fillNameHidden(selectId, value) {
            const hidden = document.getElementById(selectId.id + '_name');
            if (hidden) hidden.value = value || '';
        }

        function bindChangeHandlers() {
            provinsiSelect.addEventListener('change', function () {
                fillNameHidden(provinsiSelect, this.options[this.selectedIndex].text);
                resetSelect(kotaSelect, 'Pilih Kota', false);
                resetSelect(kecamatanSelect, 'Pilih Kecamatan', true);
                resetSelect(kelurahanSelect, 'Pilih Kelurahan', true);
                if (!this.value) return;
                fetchWilayah('regencies', this.value, kotaSelect);
            });
            kotaSelect.addEventListener('change', function () {
                fillNameHidden(kotaSelect, this.options[this.selectedIndex].text);
                resetSelect(kecamatanSelect, 'Pilih Kecamatan', false);
                resetSelect(kelurahanSelect, 'Pilih Kelurahan', true);
                if (!this.value) return;
                fetchWilayah('districts', this.value, kecamatanSelect);
            });
            kecamatanSelect.addEventListener('change', function () {
                fillNameHidden(kecamatanSelect, this.options[this.selectedIndex].text);
                resetSelect(kelurahanSelect, 'Pilih Kelurahan', false);
                if (!this.value) return;
                fetchWilayah('villages', this.value, kelurahanSelect);
            });
            kelurahanSelect.addEventListener('change', function () {
                fillNameHidden(kelurahanSelect, this.options[this.selectedIndex].text);
            });
        }

        function resetSelect(select, placeholder, disabled) {
            select.innerHTML = '';
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = placeholder;
            select.appendChild(opt);
            select.disabled = disabled;
        }

        function fillOptions(select, items, selectedId) {
            items.forEach(function (item) {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = item.name;
                if (String(item.id) === String(selectedId)) opt.selected = true;
                select.appendChild(opt);
            });
        }

        async function fetchWilayah(type, parentId, targetSelect) {
            try {
                const res = await fetch(`${WILAYAH_BASE}/${type}/${parentId}.json`);
                if (!res.ok) throw new Error('gagal memuat data wilayah');
                return await res.json();
            } catch (e) {
                showEditAlert(['Gagal memuat data wilayah. Periksa koneksi internet Anda.']);
                return [];
            }
        }

        bindChangeHandlers();

        try {
            const provinces = await (await fetch(`${WILAYAH_BASE}/provinces.json`)).json();
            fillOptions(provinsiSelect, provinces, existing.provinsi);

            if (!existing.provinsi) return;
            provinsiSelect.value = existing.provinsi;
            provinsiSelect.disabled = false;
            fillNameHidden(provinsiSelect, existing.provinsi_name);

            const regencies = await fetchWilayah('regencies', existing.provinsi, kotaSelect);
            resetSelect(kotaSelect, 'Pilih Kota', false);
            fillOptions(kotaSelect, regencies, existing.kota);
            fillNameHidden(kotaSelect, existing.kota_name);

            if (!existing.kota) return;
            const districts = await fetchWilayah('districts', existing.kota, kecamatanSelect);
            resetSelect(kecamatanSelect, 'Pilih Kecamatan', false);
            fillOptions(kecamatanSelect, districts, existing.kecamatan);
            fillNameHidden(kecamatanSelect, existing.kecamatan_name);

            if (!existing.kecamatan) return;
            const villages = await fetchWilayah('villages', existing.kecamatan, kelurahanSelect);
            resetSelect(kelurahanSelect, 'Pilih Kelurahan', false);
            fillOptions(kelurahanSelect, villages, existing.kelurahan);
            fillNameHidden(kelurahanSelect, existing.kelurahan_name);
        } catch (e) {
            showEditAlert(['Gagal memuat data wilayah. Periksa koneksi internet Anda.']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CARD: BUNDLE DOKUMEN
    |--------------------------------------------------------------------------
    */
    function buildDocumentsCard(card, data) {
        const section = el('div', '');

        const heading = el('h3', 'text-xs font-black text-primary uppercase mb-2 flex items-center gap-2');
        const icon = el('i', 'bi ' + (card.icon || 'bi-file-earmark-text-fill') + ' text-accent');
        heading.appendChild(icon);
        heading.appendChild(document.createTextNode(' ' + (card.title || 'Dokumen Persyaratan')));
        section.appendChild(heading);

        const grid = el('div', 'grid grid-cols-1 md:grid-cols-2 gap-3');
        const existingDocs = data.documents || [];

        (card.requirements || []).forEach(function (req) {
            const reqId = req.id;
            const format = req.format || 'image';
            const accept = FORMAT_ACCEPT_MAP[format] || 'image/*';
            const isWajib = !!req.wajib;
            const maxSize = req.max_size_mb || 2;
            const maxFiles = req.max_files || 1;
            const inputId = 'edit_doc_' + reqId;
            const docsForReq = existingDocs.filter(function (d) { return String(d.requirement_id) === String(reqId); });

            const wrap = el('div', 'space-y-3');
            wrap.appendChild(el('p', 'text-xs font-bold text-slate-500 uppercase tracking-widest ml-1', (req.nama_dokumen || 'Dokumen') + (isWajib ? ' *' : '')));

            // Tampilkan dokumen lama (tidak dihapus kecuali diganti)
            docsForReq.forEach(function (doc) {
                const link = document.createElement('a');
                link.href = '/storage/' + doc.file_path;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.className = 'edit-doc-existing';
                const chipIcon = el('i', 'bi bi-check-circle-fill');
                link.appendChild(chipIcon);
                link.appendChild(document.createTextNode(' ' + doc.nama_dokumen));
                wrap.appendChild(link);
            });
            if (docsForReq.length) {
                wrap.appendChild(el('p', 'text-[9px] text-slate-400 font-bold -mt-1 mb-1', 'Berkas di atas akan tetap dipakai jika Anda tidak mengunggah pengganti.'));
            }

            const uploadBox = el('div', 'edit-upload-box');
            uploadBox.addEventListener('click', function () { document.getElementById(inputId).click(); });

            const input = document.createElement('input');
            input.type = 'file';
            input.id = inputId;
            input.name = maxFiles > 1 ? `documents[${reqId}][]` : `documents[${reqId}]`;
            input.accept = accept;
            input.className = 'hidden edit-doc-input';
            input.dataset.maxSizeMb = maxSize;
            input.dataset.maxFiles = maxFiles;
            input.dataset.format = format;
            if (maxFiles > 1) input.multiple = true;
            // Wajib hanya dipaksa jika belum ada dokumen tersimpan sebelumnya
            if (isWajib && docsForReq.length === 0) input.required = true;

            const placeholder = el('div');
            placeholder.id = 'edit_placeholder_' + inputId;
            placeholder.appendChild(el('i', 'bi bi-cloud-arrow-up-fill text-3xl text-slate-300 d-block mb-2'));
            placeholder.appendChild(el('span', 'text-[10px] font-bold text-slate-400', docsForReq.length ? 'Ganti ' + (FORMAT_LABEL_MAP[format] || 'File') : 'Pilih ' + (FORMAT_LABEL_MAP[format] || 'File')));

            const previewWrap = el('div', 'edit-preview-wrap mx-auto');
            previewWrap.id = 'edit_previewWrap_' + inputId;
            const previewImg = document.createElement('img');
            previewImg.id = 'edit_previewImg_' + inputId;
            previewImg.className = 'edit-preview-img';
            previewImg.style.display = 'none';
            const previewChip = el('div', 'edit-preview-file-chip');
            previewChip.id = 'edit_previewChip_' + inputId;
            const chipIconEl = el('i', 'bi bi-file-earmark-fill');
            const previewChipText = el('span');
            previewChipText.id = 'edit_previewChipText_' + inputId;
            previewChip.appendChild(chipIconEl);
            previewChip.appendChild(previewChipText);

            const removeBtn = el('button', 'edit-remove-preview-btn');
            removeBtn.type = 'button';
            removeBtn.appendChild(el('i', 'bi bi-trash3-fill'));
            removeBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                removeEditDocument(inputId, docsForReq.length > 0);
            });

            previewWrap.appendChild(previewImg);
            previewWrap.appendChild(previewChip);
            previewWrap.appendChild(removeBtn);

            input.addEventListener('change', function () { previewEditDocument(input, inputId, maxSize, maxFiles); });

            uploadBox.appendChild(input);
            uploadBox.appendChild(placeholder);
            uploadBox.appendChild(previewWrap);
            wrap.appendChild(uploadBox);

            wrap.appendChild(el('p', 'edit-doc-hint', 'Format: ' + (FORMAT_LABEL_MAP[format] || 'Gambar') + ' \u00b7 Maks ' + maxSize + 'MB' + (maxFiles > 1 ? ' \u00b7 Maks ' + maxFiles + ' file' : '')));

            grid.appendChild(wrap);
        });

        section.appendChild(grid);
        return section;
    }

    function previewEditDocument(input, inputId, maxSize, maxFiles) {
        const files = Array.from(input.files || []);
        const placeholder = document.getElementById('edit_placeholder_' + inputId);
        const previewWrap = document.getElementById('edit_previewWrap_' + inputId);
        const previewImg = document.getElementById('edit_previewImg_' + inputId);
        const previewChip = document.getElementById('edit_previewChip_' + inputId);
        const previewChipText = document.getElementById('edit_previewChipText_' + inputId);

        if (!files.length) return;

        if (files.length > maxFiles) {
            input.value = '';
            showEditAlert([`Maksimal ${maxFiles} file untuk dokumen ini.`]);
            return;
        }

        for (const file of files) {
            if (file.size > maxSize * 1024 * 1024) {
                input.value = '';
                showEditAlert([`Ukuran file "${file.name}" melebihi batas ${maxSize}MB.`]);
                return;
            }
        }

        const firstFile = files[0];
        placeholder.style.display = 'none';
        previewWrap.style.display = 'block';

        if (firstFile.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                previewChip.style.display = 'none';
            };
            reader.readAsDataURL(firstFile);
        } else {
            previewImg.style.display = 'none';
            previewChip.style.display = 'flex';
            previewChipText.textContent = files.length > 1 ? `${files.length} file dipilih` : firstFile.name;
        }
    }

    function removeEditDocument(inputId, hasExisting) {
        const input = document.getElementById(inputId);
        const placeholder = document.getElementById('edit_placeholder_' + inputId);
        const previewWrap = document.getElementById('edit_previewWrap_' + inputId);
        const previewImg = document.getElementById('edit_previewImg_' + inputId);
        const previewChip = document.getElementById('edit_previewChip_' + inputId);

        input.value = '';
        previewImg.src = '';
        previewImg.style.display = 'none';
        previewChip.style.display = 'none';
        previewWrap.style.display = 'none';
        placeholder.style.display = 'block';
    }

    /*
    |--------------------------------------------------------------------------
    | PHONE WIDGET (intl-tel-input) - perilaku sama dengan form.blade.php
    |--------------------------------------------------------------------------
    */
    function initPhoneWidget(widget) {
        if (typeof window.intlTelInput !== 'function') return;

        widget.iti = window.intlTelInput(widget.displayEl, {
            initialCountry: 'id',
            separateDialCode: true,
            utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js',
        });

        // Prefill dari nilai tersimpan
        if (widget.initialValue) {
            let raw = String(widget.initialValue).replace(/[^0-9]/g, '');
            let formatted = '';
            if (raw.length > 0) {
                formatted += raw.substring(0, 3);
                if (raw.length > 3) formatted += '-' + raw.substring(3, 7);
                if (raw.length > 7) formatted += '-' + raw.substring(7, 13);
            }
            widget.displayEl.value = formatted;
            widget.hiddenEl.value = raw;
        }

        widget.displayEl.addEventListener('input', function () {
            let rawValue = this.value.replace(/[^0-9]/g, '');
            if (rawValue.startsWith('0')) rawValue = rawValue.substring(1);
            if (rawValue.length > 13) rawValue = rawValue.slice(0, 13);

            widget.hiddenEl.value = rawValue;

            let formattedValue = '';
            if (rawValue.length > 0) {
                formattedValue += rawValue.substring(0, 3);
                if (rawValue.length > 3) formattedValue += '-' + rawValue.substring(3, 7);
                if (rawValue.length > 7) formattedValue += '-' + rawValue.substring(7, 13);
            }
            this.value = formattedValue;
        });
    }

    function destroyPhoneWidgets() {
        phoneWidgets.forEach(function (w) {
            if (w.iti && typeof w.iti.destroy === 'function') {
                try { w.iti.destroy(); } catch (e) { /* noop */ }
            }
        });
        phoneWidgets = [];
    }

    function validatePhoneWidgets() {
        for (const w of phoneWidgets) {
            if (w.iti && w.displayEl.required && !w.iti.isValidNumber()) {
                return false;
            }
        }
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | SUBMIT REVISI
    |--------------------------------------------------------------------------
    */
    window.submitRevision = function () {
        if (isSubmitting || isLoadingData || !currentEditId) return;

        clearEditAlert();

        const form = document.getElementById('formEditSubmission');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        if (!validatePhoneWidgets()) {
            showEditAlert(['Nomor WhatsApp tidak valid. Silakan periksa kembali nomor WhatsApp Anda.']);
            return;
        }

        const formData = new FormData(form);
        const btn = document.getElementById('btnUpdateSubmit');
        const cancelBtn = document.getElementById('btnCancelEdit');
        const btnText = btn.querySelector('.btn-save-text');
        const btnLoader = btn.querySelector('.btn-save-loader');

        isSubmitting = true;
        btn.disabled = true;
        cancelBtn.disabled = true;
        if (btnText) btnText.textContent = 'Memproses...';
        if (btnLoader) btnLoader.classList.remove('hidden');
        setFormDisabled(form, true);

        fetch(`/riwayat-pengajuan/${currentEditId}/update`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData,
        })
            .then(async function (res) {
                if (!res.ok) {
                    const messages = await parseErrorResponse(res);
                    throw new Error(messages.join('\n'));
                }
                return res.json();
            })
            .then(function (res) {
                if (res && res.success) {
                    showEditAlert([res.message || 'Perbaikan berhasil dikirim.'], 'success');
                    setTimeout(function () { location.reload(); }, 900);
                } else {
                    throw new Error((res && res.message) || 'Gagal mengirim perbaikan.');
                }
            })
            .catch(function (err) {
                const msg = isNetworkError(err)
                    ? 'Gagal terhubung ke server. Periksa koneksi internet Anda dan coba lagi.'
                    : (err.message || 'Terjadi kesalahan saat mengirim perbaikan.');
                showEditAlert(msg.split('\n'));

                isSubmitting = false;
                btn.disabled = false;
                cancelBtn.disabled = false;
                if (btnText) btnText.textContent = 'Kirim Perbaikan';
                if (btnLoader) btnLoader.classList.add('hidden');
                setFormDisabled(form, false);
            });
    };

    function setFormDisabled(form, disabled) {
        form.querySelectorAll('input, select, textarea, button').forEach(function (elm) {
            elm.disabled = disabled;
        });
    }
})();
</script>