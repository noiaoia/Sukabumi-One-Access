@once
<style>
    /* 1. ANIMASI MODAL (Spring & Fade Effect) */
    @keyframes editAdminOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes editAdminOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes editAdminSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes editAdminSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.95) translateY(10px); }
    }

    /* 2. OVERLAY (Latar Belakang Blur) */
    .edit-admin-overlay {
        position: fixed; inset: 0;
        background: rgba(10, 30, 51, 0.75);
        backdrop-filter: blur(5px);
        display: none; align-items: center; justify-content: center;
        z-index: 9999; padding: 15px;
        opacity: 0;
    }
    .edit-admin-overlay.is-active { display: flex; animation: editAdminOverlayIn 0.3s ease forwards; }
    .edit-admin-overlay.is-active .edit-admin-card { animation: editAdminSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .edit-admin-overlay.is-closing { animation: editAdminOverlayOut 0.3s ease forwards; }
    .edit-admin-overlay.is-closing .edit-admin-card { animation: editAdminSpringOut 0.3s ease forwards; }

    /* 3. STRUKTUR CARD (Flexbox untuk Scroll) */
    .edit-admin-card {
        background: #ffffff;
        width: 100%; max-width: 380px;
        max-height: 80vh;
        border-radius: 20px; overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        display: flex;
        flex-direction: column;
    }

    /* Header Statis */
    .edit-admin-header {
        background: #0A1E33; padding: 15px 20px;
        border-bottom: 3px solid #E8A33D;
        display: flex; align-items: center; gap: 12px;
        flex-shrink: 0;
    }
    .edit-admin-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; letter-spacing: 0.3px; text-transform: uppercase; }
    .edit-admin-header .icon-box { width: 28px; height: 28px; border-radius: 8px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: #E8A33D; font-size: 14px; flex-shrink: 0; }

    /* Body Scrollable */
    .edit-admin-body-scroll { overflow-y: auto; flex: 1; }
    .edit-admin-body { padding: 20px; }
    .edit-admin-body-scroll::-webkit-scrollbar { width: 5px; }
    .edit-admin-body-scroll::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }
    .edit-admin-body-scroll::-webkit-scrollbar-track { background: #0A1E33; }

    /* Footer Statis */
    .edit-admin-footer {
        padding: 12px 20px;
        background: #F8FAFC;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-shrink: 0;
    }

    /* 4. INPUT & LABEL STYLING */
    .label-admin { display: block; font-size: 8.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-bottom: 4px; margin-left: 2px; }
    .input-group-admin { position: relative; width: 100%; margin-bottom: 12px; }

    .input-admin {
        width: 100%; padding: 8px 12px 8px 38px; background: #F8FAFC; border: 1.2px solid #E2E8F0;
        border-radius: 8px; font-size: 12px; font-weight: 700; color: #123457; transition: all 0.2s;
        font-family: inherit;
    }
    .input-admin:focus { outline: none; border-color: #123457; background: #fff; box-shadow: 0 0 0 3px rgba(18, 52, 87, 0.05); }

    .icon-admin-left { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #cbd5e1; font-size: 0.9rem; pointer-events: none; z-index: 5; }
    .input-admin:focus + .icon-admin-left { color: #E8A33D; }

    /* 5. KHUSUS TELEPON (Intl-Tel-Input) */
    .iti { width: 100% !important; }
    .iti__selected-dial-code { font-weight: 700 !important; font-size: 12px !important; color: #123457 !important; }
    .phone-compact { padding-left: 82px !important; }

    /* 6. TOM SELECT (Dropdown Instansi) */
    .edit-admin-body .ts-wrapper.input-admin { padding: 0 !important; border: none !important; background: transparent !important; }
    .edit-admin-body .ts-control {
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
    .edit-admin-body .ts-wrapper.focus .ts-control { border-color: #123457 !important; background: #ffffff !important; box-shadow: 0 0 0 3px rgba(18, 52, 87, 0.05) !important; }
    .icon-instansi-admin { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #cbd5e1; font-size: 0.9rem; z-index: 10; pointer-events: none; }
    .ts-wrapper.single .ts-control:after { display: none !important; }

    /* 7. TOMBOL (Buttons) */
    .btn-save-compact { background: #123457; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; transition: 0.3s; cursor: pointer; }
    .btn-save-compact:hover { background: #E8A33D; color: #0A1E33; }
    .btn-cancel-compact { background: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; padding: 8px 14px; border-radius: 8px; font-size: 10px; font-weight: 700; transition: 0.3s; cursor: pointer; text-transform: uppercase; }
    .btn-cancel-compact:hover { background: #cbd5e1; }
    .btn-eye-compact { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #cbd5e1; background: none; border: none; padding: 0; cursor: pointer; z-index: 10; }
</style>
@endonce

<div id="editAdminDinasModal" class="edit-admin-overlay" onclick="if(event.target === this) closeEditAdminDinasModal()">
    <div class="edit-admin-card">
        <!-- HEADER: Tetap di Atas -->
        <div class="edit-admin-header">
            <div class="icon-box"><i class="bi bi-person-badge-fill"></i></div>
            <h2>Update Otoritas Dinas</h2>
        </div>

        <!-- BODY: Area yang bisa di-scroll -->
        <form id="editAdminDinasForm" method="POST" class="edit-admin-body-scroll">
            @csrf
            @method('PUT')

            <div class="edit-admin-body">
                <label class="label-admin">Nama Lengkap & Gelar</label>
                <div class="input-group-admin">
                    <input type="text" name="name" id="adm_name" class="input-admin" required>
                    <i class="bi bi-person-check icon-admin-left"></i>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-1">
                    <div>
                        <label class="label-admin">Username</label>
                        <div class="input-group-admin">
                            <input type="text" name="username" id="adm_username" class="input-admin" required>
                            <i class="bi bi-at icon-admin-left"></i>
                        </div>
                    </div>
                    <div>
                        <label class="label-admin">Email Dinas</label>
                        <div class="input-group-admin">
                            <input type="email" name="email" id="adm_email" class="input-admin" required>
                            <i class="bi bi-envelope icon-admin-left"></i>
                        </div>
                    </div>
                </div>

                <label class="label-admin">Nomor WhatsApp</label>
                <div class="input-group-admin">
                    <input type="tel" id="adm_phone_display" class="input-admin phone-compact" maxlength="15" placeholder="8xx-xxxx-xxxx" required>
                    <input type="hidden" name="phone" id="adm_phone_hidden">
                </div>

                <label class="label-admin">Unit Kerja / Instansi</label>
                <div class="input-group-admin">
                    <i class="bi bi-building icon-instansi-admin"></i>
                    <select id="adm_instansi_select" name="instansi" class="input-admin" required>
                        <option value="">Pilih Instansi...</option>
                        @foreach($instansiList as $ins)
                            <option value="{{ $ins->nama }}">{{ $ins->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="py-2 flex items-center gap-2">
                    <span class="text-[7px] font-black text-slate-300 uppercase tracking-widest whitespace-nowrap">Pengaturan Sandi</span>
                    <div class="h-[1px] w-full bg-slate-100"></div>
                </div>

                <div class="space-y-3">
                    <div class="input-group-admin">
                        <input type="password" name="password" id="admPassInput" class="input-admin" placeholder="Sandi Baru (Kosongkan jika tetap)">
                        <i class="bi bi-lock icon-admin-left"></i>
                        <button type="button" onclick="togglePassAdm('admPassInput', 'admEyeIcon')" class="btn-eye-compact">
                            <i class="bi bi-eye" id="admEyeIcon"></i>
                        </button>
                    </div>
                    <div class="input-group-admin">
                        <input type="password" name="password_confirmation" id="admConfirmPassInput" class="input-admin" placeholder="Ulangi Sandi Baru">
                        <i class="bi bi-shield-lock icon-admin-left"></i>
                        <button type="button" onclick="togglePassAdm('admConfirmPassInput', 'admConfirmEyeIcon')" class="btn-eye-compact">
                            <i class="bi bi-eye" id="admConfirmEyeIcon"></i>
                        </button>
                    </div>
                    <p style="font-size: 8px; color: #94a3b8; font-weight: 600;" class="ml-1">*Hanya diisi jika ingin mengganti kata sandi.</p>
                </div>
            </div>
        </form>

        <!-- FOOTER: Tetap di Bawah -->
        <div class="edit-admin-footer">
            <button type="button" class="btn-cancel-compact" onclick="closeEditAdminDinasModal()">BATAL</button>
            <button type="submit" form="editAdminDinasForm" class="btn-save-compact">SIMPAN PERUBAHAN</button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/intlTelInput.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

@once
<script>
    let admIti;
    let admInstansiTS;

    document.addEventListener('DOMContentLoaded', function() {
        // 1. Inisialisasi Tom Select untuk Instansi
        const instansiEl = document.getElementById('adm_instansi_select');
        if (instansiEl) {
            admInstansiTS = new TomSelect(instansiEl, {
                create: false,
                placeholder: "Cari instansi...",
            });
        }

        // 2. Inisialisasi intl-tel-input
        const phoneDisplay = document.getElementById('adm_phone_display');
        if (phoneDisplay) {
            admIti = window.intlTelInput(phoneDisplay, {
                initialCountry: "id",
                separateDialCode: true,
                containerClass: "iti-full-width",
                dropdownContainer: document.body, // Agar tidak terpotong modal
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
            });
        }
    });

    // Dipanggil dari tombol edit di list, mis: onclick="handleEditOp(this)"
    window.handleEditOp = function(btn) {
        try {
            const data = JSON.parse(btn.getAttribute('data-op'));
            const url = btn.getAttribute('data-url');
            openEditAdminDinasModal(data, url);
        } catch (e) {
            console.error("Gagal memproses data admin dinas:", e);
        }
    };

    window.openEditAdminDinasModal = function(data, updateUrl) {
        const modal = document.getElementById('editAdminDinasModal');
        const form = document.getElementById('editAdminDinasForm');

        form.action = updateUrl;
        document.getElementById('adm_name').value = data.name || "";
        document.getElementById('adm_username').value = data.username || "";
        document.getElementById('adm_email').value = data.email || "";

        // SINKRONISASI INSTANSI
        if (admInstansiTS) {
            const valInstansi = data.instansi || "";
            admInstansiTS.clear();

            if (valInstansi && !admInstansiTS.options[valInstansi]) {
                admInstansiTS.addOption({ value: valInstansi, text: valInstansi });
                admInstansiTS.refreshOptions(false);
            }
            admInstansiTS.setValue(valInstansi, true);
        }

        // SINKRONISASI TELEPON
        const phoneDisplay = document.getElementById('adm_phone_display');
        const phoneHidden = document.getElementById('adm_phone_hidden');
        let dbPhone = String(data.phone || "");

        if (admIti) {
            let cleanForIti = dbPhone.startsWith('62') ? '+' + dbPhone : '+62' + dbPhone;
            admIti.setNumber(cleanForIti);
        }

        let displayVal = dbPhone;
        if (displayVal.startsWith('62')) displayVal = displayVal.substring(2);
        else if (displayVal.startsWith('0')) displayVal = displayVal.substring(1);

        phoneDisplay.value = formatPhoneAdm(displayVal);
        phoneHidden.value = displayVal;

        // Reset Password Fields
        document.getElementById('admPassInput').value = "";
        document.getElementById('admConfirmPassInput').value = "";

        modal.classList.remove('is-closing');
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    };

    // Toggle lihat/sembunyikan sandi
    window.togglePassAdm = function(inputId, iconId) {
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

    window.formatPhoneAdm = function(value) {
        let digits = value.replace(/\D/g, '').substring(0, 13);
        let formatted = '';
        if (digits.length <= 3) formatted = digits;
        else if (digits.length <= 7) formatted = digits.substring(0,3) + '-' + digits.substring(3);
        else formatted = digits.substring(0,3) + '-' + digits.substring(3,7) + '-' + digits.substring(7);
        return formatted;
    };

    window.closeEditAdminDinasModal = function() {
        const modal = document.getElementById('editAdminDinasModal');
        if (modal) {
            modal.classList.add('is-closing');
            setTimeout(() => {
                modal.classList.remove('is-active');
                modal.classList.remove('is-closing');
                document.body.style.overflow = 'auto';
            }, 300);
        }
    };

    // Event Listener Input Telepon
    document.getElementById('adm_phone_display')?.addEventListener('input', function() {
        let raw = this.value.replace(/\D/g, '');
        if (raw.startsWith('0')) raw = raw.substring(1);
        this.value = formatPhoneAdm(raw);
        document.getElementById('adm_phone_hidden').value = raw;
    });
</script>
@endonce