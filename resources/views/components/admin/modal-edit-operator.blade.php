@once
<style>
    /* 1. ANIMASI MODAL (Spring & Fade Effect) */
    @keyframes editOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes editOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes editSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes editSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.95) translateY(10px); }
    }

    /* 2. OVERLAY (Latar Belakang Blur) */
    .edit-op-overlay {
        position: fixed; inset: 0;
        background: rgba(10, 30, 51, 0.75);
        backdrop-filter: blur(5px);
        display: none; align-items: center; justify-content: center;
        z-index: 9999; padding: 15px;
        opacity: 0;
    }
    .edit-op-overlay.is-active { display: flex; animation: editOverlayIn 0.3s ease forwards; }
    .edit-op-overlay.is-active .edit-op-card { animation: editSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .edit-op-overlay.is-closing { animation: editOverlayOut 0.3s ease forwards; }
    .edit-op-overlay.is-closing .edit-op-card { animation: editSpringOut 0.3s ease forwards; }

    /* 3. STRUKTUR CARD (Flexbox untuk Scroll) */
    .edit-op-card {
        background: #ffffff;
        width: 100%; max-width: 380px; 
        max-height: 80vh; /* Membatasi tinggi modal 85% dari tinggi layar */
        border-radius: 20px; overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        display: flex;
        flex-direction: column;
    }

    /* Header Statis */
    .edit-op-header {
        background: #0A1E33; padding: 15px 20px;
        border-bottom: 3px solid #E8A33D;
        display: flex; align-items: center; gap: 12px;
        flex-shrink: 0; /* Mencegah header menyusut */
    }
    .edit-op-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; letter-spacing: 0.3px; text-transform: uppercase; }
    .edit-op-header .icon-box { width: 28px; height: 28px; border-radius: 8px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: #E8A33D; font-size: 14px; flex-shrink: 0; }

    /* Body Scrollable */
    .edit-op-body-scroll {
        overflow-y: auto;
        flex: 1; /* Mengambil ruang sisa antara header dan footer */
    }
    .edit-op-body { padding: 20px; }

    /* Scrollbar Styling (Webkit) */
    .edit-op-body-scroll::-webkit-scrollbar { width: 5px; }
    .edit-op-body-scroll::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }
    .edit-op-body-scroll::-webkit-scrollbar-track { background: #0A1E33; }

    /* Footer Statis */
    .edit-op-footer { 
        padding: 12px 20px; 
        background: #F8FAFC; 
        border-top: 1px solid #e2e8f0; 
        display: flex; 
        justify-content: flex-end; 
        gap: 8px;
        flex-shrink: 0; /* Mencegah footer menyusut */
    }

    /* 4. INPUT & LABEL STYLING */
    .label-edit { display: block; font-size: 8.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-bottom: 4px; margin-left: 2px; }
    .input-group-edit { position: relative; width: 100%; margin-bottom: 12px; }
    
    .input-edit {
        width: 100%; padding: 8px 12px 8px 38px; background: #F8FAFC; border: 1.2px solid #E2E8F0;
        border-radius: 8px; font-size: 12px; font-weight: 700; color: #123457; transition: all 0.2s;
        font-family: inherit;
    }
    .input-edit:focus { outline: none; border-color: #123457; background: #fff; box-shadow: 0 0 0 3px rgba(18, 52, 87, 0.05); }
    
    .icon-edit-left { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #cbd5e1; font-size: 0.9rem; pointer-events: none; z-index: 5; }
    .input-edit:focus + .icon-edit-left { color: #E8A33D; }

    /* 5. KHUSUS TELEPON (Intl-Tel-Input) */
    .iti { width: 100% !important; }
    .iti__selected-dial-code { font-weight: 700 !important; font-size: 12px !important; color: #123457 !important; }
    .phone-compact { padding-left: 82px !important; }

    /* 6. TOM SELECT (Dropdown Instansi) */
    .edit-op-body .ts-wrapper.input-edit { padding: 0 !important; border: none !important; background: transparent !important; }
    .edit-op-body .ts-control {
        width: 100% !important;
        padding: 8px 12px 8px 38px !important;
        background: #F8FAFC !important;
        border: 1.2px solid #E2E8F0 !important;
        border-radius: 8px !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #123457 !important;
        min-height: 36px !important;
        display: flex; align-items: center;
    }
    .edit-op-body .ts-wrapper.focus .ts-control { border-color: #123457 !important; background: #ffffff !important; box-shadow: 0 0 0 3px rgba(18, 52, 87, 0.05) !important; }
    .icon-instansi-edit { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #cbd5e1; font-size: 0.9rem; z-index: 10; pointer-events: none; }
    .ts-wrapper.single .ts-control:after { display: none !important; }

    /* 7. TOMBOL (Buttons) */
    .btn-save-compact { background: #123457; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; transition: 0.3s; cursor: pointer; }
    .btn-save-compact:hover { background: #E8A33D; color: #0A1E33; }
    .btn-cancel-compact { background: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; padding: 8px 14px; border-radius: 8px; font-size: 10px; font-weight: 700; transition: 0.3s; cursor: pointer; text-transform: uppercase; }
    .btn-cancel-compact:hover { background: #cbd5e1; }
    .btn-eye-compact { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #cbd5e1; background: none; border: none; padding: 0; cursor: pointer; z-index: 10; }
</style>
@endonce

<div id="editOperatorModal" class="edit-op-overlay" onclick="if(event.target === this) closeEditOperatorModal()">
    <div class="edit-op-card">
        <!-- HEADER: Tetap di Atas -->
        <div class="edit-op-header">
            <div class="icon-box"><i class="bi bi-pencil-square"></i></div>
            <h2>MODIFIKASI OTORITAS</h2>
        </div>

        <!-- BODY: Area yang bisa di-scroll -->
        <!-- Tambahkan class edit-op-body-scroll di sini -->
        <form id="editOperatorForm" method="POST" class="edit-op-body-scroll">
            @csrf
            @method('PUT')
            
            <div class="edit-op-body">
                <label class="label-edit">Nama Lengkap Sesuai SK</label>
                <div class="input-group-edit">
                    <input type="text" name="name" id="edit_name" class="input-edit" required>
                    <i class="bi bi-person-vcard icon-edit-left"></i>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-1">
                    <div>
                        <label class="label-edit">Username</label>
                        <div class="input-group-edit">
                            <input type="text" name="username" id="edit_username" class="input-edit" required>
                            <i class="bi bi-at icon-edit-left"></i>
                        </div>
                    </div>
                    <div>
                        <label class="label-edit">Email Kerja</label>
                        <div class="input-group-edit">
                            <input type="email" name="email" id="edit_email" class="input-edit" required>
                            <i class="bi bi-envelope icon-edit-left"></i>
                        </div>
                    </div>
                </div>

                <label class="label-edit">Nomor WhatsApp Aktif</label>
                <div class="input-group-edit">
                    <input type="tel" id="edit_phone_display" class="input-edit phone-compact" maxlength="15" placeholder="8xx-xxxx-xxxx" required>
                    <input type="hidden" name="phone" id="edit_phone_hidden">
                </div>

                <label class="label-edit">Instansi / Unit Kerja</label>
                <div class="input-group-edit">
                    <i class="bi bi-building icon-instansi-edit"></i>
                    <select id="edit_instansi_select" name="instansi" class="input-edit" required>
                        <option value="">Pilih Instansi...</option>
                        <option value="Sekretariat Daerah">Sekretariat Daerah (SETDA)</option>
                        <option value="Dinas Komunikasi dan Informatika">Diskominfo</option>
                        <option value="Dinas Kesehatan">Dinas Kesehatan</option>
                        <option value="Dinas Pendidikan dan Kebudayaan">Dinas Pendidikan</option>
                        <option value="Dinas Pekerjaan Umum dan Penataan Ruang">Dinas PUPR</option>
                        <option value="Dinas Perhubungan">Dinas Perhubungan</option>
                        <option value="Badan Perencanaan Pembangunan Daerah">BAPPEDA</option>
                        <option value="Badan Pengelolaan Keuangan dan Aset Daerah">BPKAD</option>
                        <option value="Badan Kepegawaian dan Pengembangan SDM">BKPSDM</option>
                        <option value="Satuan Polisi Pamong Praja">Satpol PP</option>
                        <option value="Kecamatan">Wilayah Kecamatan</option>
                    </select>
                </div>

                <div class="py-2 flex items-center gap-2">
                    <span class="text-[7px] font-black text-slate-300 uppercase tracking-widest whitespace-nowrap">Akses Kata Sandi</span>
                    <div class="h-[1px] w-full bg-slate-100"></div>
                </div>

                <div class="space-y-3">
                    <div class="input-group-edit">
                        <input type="password" name="password" id="editPassInput" class="input-edit" placeholder="Sandi Baru (Opsional)">
                        <i class="bi bi-lock icon-edit-left"></i>
                        <button type="button" onclick="togglePassEdit('editPassInput', 'editEyeIcon')" class="btn-eye-compact">
                            <i class="bi bi-eye" id="editEyeIcon"></i>
                        </button>
                    </div>
                    <div class="input-group-edit">
                        <input type="password" name="password_confirmation" id="editConfirmPassInput" class="input-edit" placeholder="Konfirmasi Sandi Baru">
                        <i class="bi bi-shield-lock icon-edit-left"></i>
                        <button type="button" onclick="togglePassEdit('editConfirmPassInput', 'editConfirmEyeIcon')" class="btn-eye-compact">
                            <i class="bi bi-eye" id="editConfirmEyeIcon"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- FOOTER: Tetap di Bawah -->
        <div class="edit-op-footer">
            <button type="button" class="btn-cancel-compact" onclick="closeEditOperatorModal()">BATAL</button>
            <!-- Gunakan atribut form="..." untuk menghubungkan tombol ke form di atas -->
            <button type="submit" form="editOperatorForm" class="btn-save-compact">SIMPAN PERUBAHAN</button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/intlTelInput.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

@once
<script>
    let editIti;
    let editInstansiSelect;

    document.addEventListener('DOMContentLoaded', function() {
        // 1. Inisialisasi Tom Select
        const instansiEl = document.getElementById('edit_instansi_select');
        if (instansiEl) {
            editInstansiSelect = new TomSelect(instansiEl, {
                create: true,
                placeholder: "Cari instansi...",
                allowEmptyOption: true,
            });
        }

        // 2. Inisialisasi intl-tel-input
        const phoneDisplay = document.getElementById('edit_phone_display');
        if (phoneDisplay) {
            editIti = window.intlTelInput(phoneDisplay, {
                initialCountry: "id",
                separateDialCode: true,
                containerClass: "iti-full-width",
                dropdownContainer: document.body, // Agar tidak terpotong modal
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
            });
        }
    });

    // Gunakan window. agar bisa dipanggil dari onclick="handleEditOp(this)"
    window.handleEditOp = function(btn) {
        try {
            const data = JSON.parse(btn.getAttribute('data-op'));
            const url = btn.getAttribute('data-url');
            openEditOperatorModal(data, url);
        } catch (e) {
            console.error("Gagal memproses data operator:", e);
        }
    };

    window.openEditOperatorModal = function(data, updateUrl) {
        const modal = document.getElementById('editOperatorModal');
        const form = document.getElementById('editOperatorForm');
        
        form.action = updateUrl;
        document.getElementById('edit_name').value = data.name || "";
        document.getElementById('edit_username').value = data.username || "";
        document.getElementById('edit_email').value = data.email || "";
        
        // SINKRONISASI INSTANSI
        if (editInstansiSelect) {
            console.log(data.instansi);
            const valInstansi = data.instansi || "";

            editInstansiSelect.clear();

            if (valInstansi) {
                editInstansiSelect.addOption({
                    value: valInstansi,
                    text: valInstansi
                });

                editInstansiSelect.refreshOptions(false);
                editInstansiSelect.setValue(valInstansi, true);
            }
        }

        // SINKRONISASI TELEPON
        const phoneDisplay = document.getElementById('edit_phone_display');
        const phoneHidden = document.getElementById('edit_phone_hidden');
        let dbPhone = String(data.phone || "");

        if (editIti) {
            let cleanForIti = dbPhone.startsWith('62') ? '+' + dbPhone : '+62' + dbPhone;
            editIti.setNumber(cleanForIti);
        }

        let displayVal = dbPhone;
        if (displayVal.startsWith('62')) displayVal = displayVal.substring(2);
        else if (displayVal.startsWith('0')) displayVal = displayVal.substring(1);
        
        phoneDisplay.value = formatPhone(displayVal);
        phoneHidden.value = displayVal; 

        document.getElementById('editPassInput').value = "";
        document.getElementById('editConfirmPassInput').value = "";

        modal.classList.remove('is-closing');
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    };

    // Fungsi Password Toggle (YANG TADI HILANG)
    window.togglePassEdit = function(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        const isPass = input.type === "password";
        input.type = isPass ? "text" : "password";
        if (isPass) {
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    };

    window.formatPhone = function(value) {
        let digits = value.replace(/\D/g, '').substring(0, 13);
        let formatted = '';
        if (digits.length <= 3) formatted = digits;
        else if (digits.length <= 7) formatted = digits.substring(0,3) + '-' + digits.substring(3);
        else formatted = digits.substring(0,3) + '-' + digits.substring(3,7) + '-' + digits.substring(7);
        return formatted;
    };

    window.closeEditOperatorModal = function() {
        const modal = document.getElementById('editOperatorModal');
        if(modal) {
            modal.classList.add('is-closing');
            setTimeout(() => {
                modal.classList.remove('is-active');
                modal.classList.remove('is-closing');
                document.body.style.overflow = 'auto';
            }, 300);
        }
    };

    // Event Listener Input Telepon
    document.getElementById('edit_phone_display').addEventListener('input', function() {
        let raw = this.value.replace(/\D/g, '');
        if (raw.startsWith('0')) raw = raw.substring(1);
        this.value = formatPhone(raw);
        document.getElementById('edit_phone_hidden').value = raw;
    });
</script>
@endonce