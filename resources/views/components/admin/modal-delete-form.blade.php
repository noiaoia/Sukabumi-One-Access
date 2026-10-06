{{-- resources/views/components/admin/modal-delete-form.blade.php --}}
@once
<style>
    /* 1. ANIMATION KEYFRAMES */
    @keyframes delFormOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes delFormSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes delFormOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes delFormSpringOut { from { opacity: 1; transform: scale(1) translateY(0); } to { opacity: 0; transform: scale(.96) translateY(10px); } }

    /* 2. OVERLAY */
    .del-form-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85);
        backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 15px; opacity: 0;
    }
    .del-form-overlay.is-active { display: flex; opacity: 1; animation: delFormOverlayIn 0.3s ease forwards; }
    .del-form-overlay.is-active .del-form-card { animation: delFormSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .del-form-overlay.is-closing { animation: delFormOverlayOut .3s ease forwards; }
    .del-form-overlay.is-closing .del-form-card { animation: delFormSpringOut .3s ease forwards; }

    /* 3. CARD DESIGN */
    .del-form-card {
        background: #ffffff; width: 100%; max-width: 380px; max-height: 85vh;
        border-radius: 24px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        display: flex; flex-direction: column;
    }

    .del-form-header {
        background: #0A1E33; padding: 20px;
        border-bottom: 3px solid #E8A33D; /* Warna ACCENT */
        display: flex; align-items: center; gap: 12px;
        flex-shrink: 0;
    }
    .del-form-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    .del-form-body { padding: 24px; overflow-y: auto; flex: 1; }
    
    /* Warning Box Warna Amber/Gold */
    .del-form-warning-box {
        background: #FFFBEB; border: 1px solid #FEF3C7;
        padding: 14px; border-radius: 12px; margin-bottom: 20px;
    }
    .del-form-warning-box p { color: #92400E; font-size: 12px; line-height: 1.5; font-weight: 600; margin: 0; }

    .del-form-danger-list { margin: 12px 0 0; padding-left: 18px; color: #B45309; font-size: 11px; font-weight: 600; }
    .del-form-danger-list li { margin-bottom: 4px; }

    /* Target Service Chip */
    .del-form-target-chip {
        display: flex; align-items: center; gap: 10px;
        background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;
        padding: 10px 12px; margin-bottom: 16px;
    }
    .del-form-target-icon {
        width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        color: #ffffff; font-size: 0.95rem;
    }
    .del-form-target-name { font-size: 12.5px; font-weight: 800; color: #123457; line-height: 1.2; }
    .del-form-target-type { font-size: 10px; font-weight: 700; color: #94a3b8; margin-top: 1px; text-transform: uppercase; }

    /* STEP 2 AREA */
    #form_verification_step { display: none; margin-top: 15px; border-top: 1px dashed #E2E8F0; padding-top: 15px; }
    .del-form-instruction { font-size: 12px; color: #64748B; margin-bottom: 10px; text-align: center; font-weight: 500; }
    .del-form-instruction b { color: #123457; font-weight: 800; background: #F1F5F9; padding: 2px 6px; border-radius: 4px; }

    .del-form-input-verify {
        width: 100%; padding: 12px; background: #F8FAFC; border: 2px solid #E2E8F0;
        border-radius: 12px; font-size: 14px; font-weight: 800; color: #123457;
        text-align: center; transition: 0.2s;
    }
    .del-form-input-verify:focus { outline: none; border-color: #E8A33D; background: #fff; }

    /* FOOTER */
    .del-form-footer { padding: 16px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; flex-direction: column; gap: 10px; flex-shrink: 0; }

    .del-form-btn-step-1 {
        background: #123457; color: #fff; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: pointer;
        text-transform: uppercase; letter-spacing: 0.5px;
    }

    .del-form-btn-final {
        background: #E2E8F0; color: #94A3B8; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: not-allowed;
        text-transform: uppercase; display: none;
    }
    /* Button Final Warna ACCENT saat aktif */
    .del-form-btn-final.active { 
        background: #E8A33D; 
        color: #0A1E33; 
        cursor: pointer; 
        box-shadow: 0 4px 12px rgba(232, 163, 61, 0.3); 
    }

    .del-form-btn-cancel {
        background: transparent; color: #64748B; border: none; padding: 5px;
        font-size: 11px; font-weight: 700; cursor: pointer; text-decoration: underline; text-align: center;
    }
</style>
@endonce

<div id="deleteFormModal" class="del-form-overlay">
    <div class="del-form-card">
        <div class="del-form-header">
            <div style="background: rgba(232, 163, 61, 0.15); padding: 6px; border-radius: 8px;">
                <i class="bi bi-trash3-fill text-accent"></i>
            </div>
            <h2>Hapus Formulir</h2>
        </div>

        <form id="deleteFormAction" method="POST" class="del-form-body">
            @csrf
            @method('DELETE')

            <div class="del-form-target-chip">
                <div class="del-form-target-icon" id="del_form_target_icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="min-w-0">
                    <p class="del-form-target-name truncate" id="del_form_target_name">-</p>
                    <p class="del-form-target-type truncate">Sistem Formulir Mandiri</p>
                </div>
            </div>

            <!-- STEP 1: WARNING -->
            <div id="form_warning_step">
                <div class="del-form-warning-box">
                    <p>Konfirmasi Penghentian Layanan</p>
                    <ul class="del-form-danger-list">
                        <li>Akses warga ke formulir ini akan ditutup.</li>
                        <li>Seluruh kategori formulir akan dihapus.</li>
                        <li>Tindakan ini tidak dapat dibatalkan.</li>
                    </ul>
                </div>
            </div>

            <!-- STEP 2: VERIFICATION -->
            <div id="form_verification_step">
                <p class="del-form-instruction">
                    Ketik nama formulir <b id="del_form_name_hint">-</b> untuk menghapus.
                </p>
                <input type="text" id="form_input_verify_name" class="del-form-input-verify" placeholder="Ketik di sini..." autocomplete="off">
            </div>
        </form>

        <div class="del-form-footer">
            <button type="button" id="form_btn_next_step" class="del-form-btn-step-1" onclick="goToVerifyStepForm()">
                Saya Mengerti, Lanjutkan
            </button>

            <button type="submit" form="deleteFormAction" id="form_btn_confirm_delete" class="del-form-btn-final" disabled>
                Konfirmasi Hapus Permanen
            </button>

            <button type="button" class="del-form-btn-cancel" onclick="closeDeleteFormModal()">
                Batalkan & Kembali
            </button>
        </div>
    </div>
</div>

@once
<script>
    let currentTargetFormName = ""; 

    function openDeleteFormModal(data, deleteUrl) { 
        const modal = document.getElementById('deleteFormModal');
        const form = document.getElementById('deleteFormAction');

        // DATABASE SYNC: Menggunakan data.nama & data.primary_color
        currentTargetFormName = data.nama || "";
        form.action = deleteUrl;

        // Update UI Text
        document.getElementById('del_form_target_name').innerText = currentTargetFormName;
        document.getElementById('del_form_name_hint').innerText = currentTargetFormName;

        // Update Icon & Warna Background dari Database
        const iconContainer = document.getElementById('del_form_target_icon');
        iconContainer.innerHTML = `<i class="bi ${data.icon || 'bi-file-earmark-text'}"></i>`;
        iconContainer.style.backgroundColor = data.primary_color || '#123457';
        
        // Reset Langkah-langkah
        document.getElementById('form_warning_step').style.display = 'block';
        document.getElementById('form_verification_step').style.display = 'none';
        document.getElementById('form_btn_next_step').style.display = 'block';
        document.getElementById('form_btn_confirm_delete').style.display = 'none';
        document.getElementById('form_input_verify_name').value = "";
        
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function goToVerifyStepForm() {
        document.getElementById('form_warning_step').style.display = 'none';
        document.getElementById('form_btn_next_step').style.display = 'none';
        
        document.getElementById('form_verification_step').style.display = 'block';
        document.getElementById('form_btn_confirm_delete').style.display = 'block';
        document.getElementById('form_input_verify_name').focus();

        const input = document.getElementById('form_input_verify_name');
        const btn = document.getElementById('form_btn_confirm_delete');

        input.oninput = function() {
            if (this.value === currentTargetFormName) {
                btn.classList.add('active');
                btn.disabled = false;
            } else {
                btn.classList.remove('active');
                btn.disabled = true;
            }
        };
    }

    function closeDeleteFormModal() {
        const modal = document.getElementById('deleteFormModal');
        if(modal) {
            modal.classList.add('is-closing');
            setTimeout(() => {
                modal.classList.remove('is-active', 'is-closing');
                document.body.style.overflow = 'auto';
            }, 300);
        }
    }

    // Klik luar area card untuk tutup
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('deleteFormModal');
        if(e.target === modal) closeDeleteFormModal();
    });

    // Tombol ESC untuk tutup
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteFormModal();
    });
</script>
@endonce