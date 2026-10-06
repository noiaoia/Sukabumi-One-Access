@once
<style>
    /* 1. ANIMASI MODAL (Spring & Fade Effect) */
    @keyframes editOpOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes editOpOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes editOpSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes editOpSpringOut {
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
    .edit-op-overlay.is-active { display: flex; animation: editOpOverlayIn 0.3s ease forwards; }
    .edit-op-overlay.is-active .edit-op-card { animation: editOpSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .edit-op-overlay.is-closing { animation: editOpOverlayOut 0.3s ease forwards; }
    .edit-op-overlay.is-closing .edit-op-card { animation: editOpSpringOut 0.3s ease forwards; }

    /* 3. STRUKTUR CARD (Flexbox untuk Scroll) */
    .edit-op-card {
        background: #ffffff;
        width: 100%; max-width: 380px;
        max-height: 80vh;
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
        flex-shrink: 0;
    }
    .edit-op-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; letter-spacing: 0.3px; text-transform: uppercase; }
    .edit-op-header .icon-box { width: 28px; height: 28px; border-radius: 8px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: #E8A33D; font-size: 14px; flex-shrink: 0; }

    /* Body Scrollable */
    .edit-op-body-scroll { overflow-y: auto; flex: 1; }
    .edit-op-body { padding: 20px; }
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
        flex-shrink: 0;
    }

    /* 4. INPUT & LABEL STYLING */
    .label-op { display: block; font-size: 8.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-bottom: 4px; margin-left: 2px; }
    .input-group-op { position: relative; width: 100%; margin-bottom: 12px; }

    .input-op {
        width: 100%; padding: 8px 12px 8px 38px; background: #F8FAFC; border: 1.2px solid #E2E8F0;
        border-radius: 8px; font-size: 12px; font-weight: 700; color: #123457; transition: all 0.2s;
        font-family: inherit;
    }
    .input-op:focus { outline: none; border-color: #123457; background: #fff; box-shadow: 0 0 0 3px rgba(18, 52, 87, 0.05); }
    .input-op[readonly], .input-op:disabled { background: #F1F5F9; color: #94a3b8; cursor: not-allowed; }

    .icon-op-left { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #cbd5e1; font-size: 0.9rem; pointer-events: none; z-index: 5; }
    .input-op:focus + .icon-op-left { color: #E8A33D; }

    /* 5. KHUSUS TELEPON (Intl-Tel-Input) */
    .iti { width: 100% !important; }
    .iti__selected-dial-code { font-weight: 700 !important; font-size: 12px !important; color: #123457 !important; }
    .phone-compact { padding-left: 82px !important; }

    /* 6. TOMBOL (Buttons) */
    .btn-save-compact { background: #123457; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; transition: 0.3s; cursor: pointer; }
    .btn-save-compact:hover { background: #E8A33D; color: #0A1E33; }
    .btn-cancel-compact { background: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; padding: 8px 14px; border-radius: 8px; font-size: 10px; font-weight: 700; transition: 0.3s; cursor: pointer; text-transform: uppercase; }
    .btn-cancel-compact:hover { background: #cbd5e1; }
    .btn-eye-compact { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #cbd5e1; background: none; border: none; padding: 0; cursor: pointer; z-index: 10; }
</style>
@endonce

<div id="editOperatorDinasModal" class="edit-op-overlay" onclick="if(event.target === this) closeEditOperatorDinasModal()">
    <div class="edit-op-card">
        <!-- HEADER: Tetap di Atas -->
        <div class="edit-op-header">
            <div class="icon-box"><i class="bi bi-person-gear"></i></div>
            <h2>Update Staf Pelaksana</h2>
        </div>

        <!-- BODY: Area yang bisa di-scroll -->
        <form id="editOperatorDinasForm" method="POST" class="edit-op-body-scroll">
            @csrf
            @method('PUT')

            <div class="edit-op-body">
                <label class="label-op">Nama Lengkap Staf</label>
                <div class="input-group-op">
                    <input type="text" name="name" id="op_name" class="input-op" required>
                    <i class="bi bi-person-vcard-fill icon-op-left"></i>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-1">
                    <div>
                        <label class="label-op">Username</label>
                        <div class="input-group-op">
                            <input type="text" name="username" id="op_username" class="input-op" required>
                            <i class="bi bi-at icon-op-left"></i>
                        </div>
                    </div>
                    <div>
                        <label class="label-op">Email Staf</label>
                        <div class="input-group-op">
                            <input type="email" name="email" id="op_email" class="input-op" required>
                            <i class="bi bi-envelope icon-op-left"></i>
                        </div>
                    </div>
                </div>

                <label class="label-op">Nomor WhatsApp</label>
                <div class="input-group-op">
                    <input type="tel" id="op_phone_display" class="input-op phone-compact" maxlength="15" placeholder="8xx-xxxx-xxxx" required>
                    <input type="hidden" name="phone" id="op_phone_hidden">
                </div>

                <label class="label-op">Instansi Pelaksana</label>
                <div class="input-group-op">
                    <input type="text" id="op_instansi" class="input-op" style="padding-left: 38px;" readonly>
                    <i class="bi bi-building-fill icon-op-left"></i>
                </div>

                <div class="py-2 flex items-center gap-2">
                    <span class="text-[7px] font-black text-slate-300 uppercase tracking-widest whitespace-nowrap">Pengaturan Sandi</span>
                    <div class="h-[1px] w-full bg-slate-100"></div>
                </div>

                <div class="space-y-3">
                    <div class="input-group-op">
                        <input type="password" name="password" id="opPassInput" class="input-op" placeholder="Sandi Baru (Kosongkan jika tetap)">
                        <i class="bi bi-lock icon-op-left"></i>
                        <button type="button" onclick="togglePassOp('opPassInput', 'opEyeIcon')" class="btn-eye-compact">
                            <i class="bi bi-eye" id="opEyeIcon"></i>
                        </button>
                    </div>
                    <div class="input-group-op">
                        <input type="password" name="password_confirmation" id="opConfirmPassInput" class="input-op" placeholder="Ulangi Sandi Baru">
                        <i class="bi bi-shield-lock icon-op-left"></i>
                        <button type="button" onclick="togglePassOp('opConfirmPassInput', 'opConfirmEyeIcon')" class="btn-eye-compact">
                            <i class="bi bi-eye" id="opConfirmEyeIcon"></i>
                        </button>
                    </div>
                    <p style="font-size: 8px; color: #94a3b8; font-weight: 600;" class="ml-1">*Hanya diisi jika ingin mengganti kata sandi.</p>
                </div>
            </div>
        </form>

        <!-- FOOTER: Tetap di Bawah -->
        <div class="edit-op-footer">
            <button type="button" class="btn-cancel-compact" onclick="closeEditOperatorDinasModal()">BATAL</button>
            <button type="submit" form="editOperatorDinasForm" class="btn-save-compact">SIMPAN PERUBAHAN</button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/intlTelInput.min.js"></script>

@once
<script>
    let opIti;

    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi intl-tel-input
        const phoneDisplay = document.getElementById('op_phone_display');
        if (phoneDisplay) {
            opIti = window.intlTelInput(phoneDisplay, {
                initialCountry: "id",
                separateDialCode: true,
                containerClass: "iti-full-width",
                dropdownContainer: document.body, // Agar tidak terpotong modal
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
            });
        }
    });

    // Dipanggil dari tombol edit di list, mis: onclick="handleEditOperatorDinas(this)"
    window.handleEditOperatorDinas = function(btn) {
        try {
            const data = JSON.parse(btn.getAttribute('data-op'));
            const url = btn.getAttribute('data-url');
            openEditOperatorDinasModal(data, url);
        } catch (e) {
            console.error("Gagal memproses data operator dinas:", e);
        }
    };

    window.openEditOperatorDinasModal = function(data, updateUrl) {
        const modal = document.getElementById('editOperatorDinasModal');
        const form = document.getElementById('editOperatorDinasForm');

        form.action = updateUrl;
        document.getElementById('op_name').value = data.name || "";
        document.getElementById('op_username').value = data.username || "";
        document.getElementById('op_email').value = data.email || "";
        document.getElementById('op_instansi').value = data.instansi || "";

        // SINKRONISASI TELEPON
        const phoneDisplay = document.getElementById('op_phone_display');
        const phoneHidden = document.getElementById('op_phone_hidden');
        let dbPhone = String(data.phone || "");

        if (opIti) {
            let cleanForIti = dbPhone.startsWith('62') ? '+' + dbPhone : '+62' + dbPhone;
            opIti.setNumber(cleanForIti);
        }

        let displayVal = dbPhone;
        if (displayVal.startsWith('62')) displayVal = displayVal.substring(2);
        else if (displayVal.startsWith('0')) displayVal = displayVal.substring(1);

        phoneDisplay.value = formatPhoneOp(displayVal);
        phoneHidden.value = displayVal;

        // Reset Password Fields
        document.getElementById('opPassInput').value = "";
        document.getElementById('opConfirmPassInput').value = "";

        modal.classList.remove('is-closing');
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    };

    // Toggle lihat/sembunyikan sandi
    window.togglePassOp = function(inputId, iconId) {
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

    window.formatPhoneOp = function(value) {
        let digits = value.replace(/\D/g, '').substring(0, 13);
        let formatted = '';
        if (digits.length <= 3) formatted = digits;
        else if (digits.length <= 7) formatted = digits.substring(0,3) + '-' + digits.substring(3);
        else formatted = digits.substring(0,3) + '-' + digits.substring(3,7) + '-' + digits.substring(7);
        return formatted;
    };

    window.closeEditOperatorDinasModal = function() {
        const modal = document.getElementById('editOperatorDinasModal');
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
    document.getElementById('op_phone_display')?.addEventListener('input', function() {
        let raw = this.value.replace(/\D/g, '');
        if (raw.startsWith('0')) raw = raw.substring(1);
        this.value = formatPhoneOp(raw);
        document.getElementById('op_phone_hidden').value = raw;
    });
</script>
@endonce