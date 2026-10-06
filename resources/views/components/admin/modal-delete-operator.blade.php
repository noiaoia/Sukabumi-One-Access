@once
<style>
    /* ANIMASI */
    @keyframes delOpOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes delOpSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }

    /* ANIMASI KELUAR */
    @keyframes delOpOverlayOut { 
        from { opacity: 1; } 
        to { opacity: 0; } 
    }

    @keyframes delOpSpringOut {
        0% { opacity: 1; transform: scale(1) translateY(0); }
        30% { transform: scale(1.02) translateY(-5px); } /* Sedikit membal ke atas sebelum turun */
        100% { opacity: 0; transform: scale(0.9) translateY(20px); }
    }

    .del-op-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85);
        backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 15px;
    }
    .del-op-overlay.is-active { display: flex; animation: delOpOverlayIn 0.3s ease forwards; }
    .del-op-overlay.is-active .del-op-card { animation: delOpSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }

    /* State saat modal sedang menutup */
    .del-op-overlay.is-leaving { 
        animation: delOpOverlayOut 0.3s ease forwards; 
    }

    .del-op-overlay.is-leaving .del-op-card { 
        animation: delOpSpringOut 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; 
    }

    .del-op-card {
        background: #ffffff; width: 100%; max-width: 380px; max-height: 80vh;
        border-radius: 24px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        display: flex; flex-direction: column;
    }

    /* HEADER — identik dengan modal edit/hapus lain (navy + border gold) */
    .del-op-header {
        background: #0A1E33; padding: 20px;
        border-bottom: 3px solid #E8A33D;
        display: flex; align-items: center; gap: 12px;
        flex-shrink: 0;
    }
    .del-op-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    /* BODY & STEPPER — scroll sendiri kalau kontennya lebih tinggi dari max-height kartu */
    .del-op-body { padding: 24px; overflow-y: auto; flex: 1; }
    .del-op-body::-webkit-scrollbar { width: 4px; }
    .del-op-body::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }

    .del-op-warning-box {
        background: #FFFBEB; border: 1px solid #FEF3C7;
        padding: 14px; border-radius: 12px; margin-bottom: 20px;
    }
    .del-op-warning-box p { color: #92400E; font-size: 12px; line-height: 1.5; font-weight: 600; margin: 0; }

    .del-op-danger-list { margin: 12px 0 0; padding-left: 18px; color: #B45309; font-size: 11px; font-weight: 600; }
    .del-op-danger-list li { margin-bottom: 4px; }

    /* Info ringkas operator yang mau dihapus */
    .del-op-target-chip {
        display: flex; align-items: center; gap: 10px;
        background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;
        padding: 10px 12px; margin-bottom: 16px;
    }
    .del-op-target-icon {
        width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: #123457; color: #E8A33D; font-size: 0.95rem;
    }
    .del-op-target-name { font-size: 12.5px; font-weight: 800; color: #123457; line-height: 1.2; }
    .del-op-target-user { font-size: 10px; font-weight: 700; color: #94a3b8; margin-top: 1px; }

    /* VERIFICATION AREA (Hidden initially) */
    #op_verification_step { display: none; margin-top: 15px; border-top: 1px dashed #E2E8F0; padding-top: 15px; }

    .del-op-instruction-text { font-size: 12px; color: #64748B; margin-bottom: 10px; text-align: center; font-weight: 500; }
    .del-op-instruction-text b { color: #123457; font-weight: 800; background: #F1F5F9; padding: 2px 6px; border-radius: 4px; }

    .del-op-input-verify {
        width: 100%; padding: 12px; background: #F8FAFC; border: 2px solid #E2E8F0;
        border-radius: 12px; font-size: 14px; font-weight: 800; color: #123457;
        text-align: center; transition: 0.2s;
    }
    .del-op-input-verify:focus { outline: none; border-color: #E8A33D; background: #fff; }

    /* FOOTER BUTTONS */
    .del-op-footer { padding: 16px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; flex-direction: column; gap: 10px; flex-shrink: 0; }

    .del-op-btn-step-1 {
        background: #123457; color: #fff; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: pointer;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .del-op-btn-step-1:hover { background: #0A1E33; }

    .del-op-btn-final {
        background: #E2E8F0; color: #94A3B8; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: not-allowed;
        text-transform: uppercase; display: none;
    }
    .del-op-btn-final.active { background: #E11D48; color: #fff; cursor: pointer; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3); }

    .del-op-btn-cancel {
        background: transparent; color: #64748B; border: none; padding: 5px;
        font-size: 11px; font-weight: 700; cursor: pointer; text-decoration: underline;
    }
</style>
@endonce

<div id="deleteOperatorModal" class="del-op-overlay">
    <div class="del-op-card">
        <div class="del-op-header">
            <div style="background: rgba(232, 163, 61, 0.15); padding: 6px; border-radius: 8px;">
                <i class="bi bi-shield-lock-fill text-[#E8A33D]"></i>
            </div>
            <h2>Konfirmasi Keamanan</h2>
        </div>

        <form id="deleteOperatorForm" method="POST" class="del-op-body">
            @csrf
            @method('DELETE')

            <div class="del-op-target-chip">
                <div class="del-op-target-icon" id="del_op_target_icon"><i class="bi bi-person-fill"></i></div>
                <div class="min-w-0 text-left">
                    <p class="del-op-target-name truncate" id="del_op_target_name">-</p>
                    <p class="del-op-target-user truncate" id="del_op_target_user_preview">-</p>
                </div>
            </div>

            <div id="op_warning_step">
                <div class="del-op-warning-box text-left">
                    <p>Anda akan melakukan tindakan yang tidak dapat dibatalkan!</p>
                    <ul class="del-op-danger-list">
                        <li>Akses login operator akan langsung dicabut.</li>
                        <li>Seluruh riwayat aktivitas akan dihapus dari sistem.</li>
                        <li>Data yang telah dihapus tidak dapat dipulihkan.</li>
                    </ul>
                </div>
            </div>

            <div id="op_verification_step">
                <p class="del-op-instruction-text">
                    Ketik kembali username <b id="del_op_username_hint">-</b> untuk mengonfirmasi penghapusan.
                </p>
                <input type="text" id="op_input_verify_username" class="del-op-input-verify" placeholder="Ketik username di sini..." autocomplete="off">
            </div>
        </form>

        <div class="del-op-footer">
            <button type="button" id="op_btn_next_step" class="del-op-btn-step-1" onclick="goToVerifyStepOp()">
                Saya mengerti konsekuensinya
            </button>

            <button type="submit" form="deleteOperatorForm" id="op_btn_confirm_delete" class="del-op-btn-final">
                Cabut Akses Permanen
            </button>

            <button type="button" class="del-op-btn-cancel" onclick="closeDeleteOperatorModal()">
                Batalkan & Kembali
            </button>
        </div>
    </div>
</div>

@once
<script>
    let currentTargetOpUsername = "";

    function openDeleteOperatorModal(data, deleteUrl) {
        const modal = document.getElementById('deleteOperatorModal');
        const form = document.getElementById('deleteOperatorForm');

        // Pastikan mengambil data username dengan benar
        currentTargetOpUsername = data.username || "";
        form.action = deleteUrl;

        // Update UI Chip
        document.getElementById('del_op_target_name').innerText = data.name || "-";
        document.getElementById('del_op_target_user_preview').innerText = '@' + currentTargetOpUsername;
        document.getElementById('del_op_username_hint').innerText = currentTargetOpUsername;

        // Reset UI Modal ke Step 1
        document.getElementById('op_warning_step').style.display = 'block';
        document.getElementById('op_verification_step').style.display = 'none';
        document.getElementById('op_btn_next_step').style.display = 'block';
        
        const btnDelete = document.getElementById('op_btn_confirm_delete');
        btnDelete.style.display = 'none';
        btnDelete.classList.remove('active');
        btnDelete.disabled = true;
        
        document.getElementById('op_input_verify_username').value = "";

        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function goToVerifyStepOp() {
        document.getElementById('op_warning_step').style.display = 'none';
        document.getElementById('op_btn_next_step').style.display = 'none';

        document.getElementById('op_verification_step').style.display = 'block';
        const btnDelete = document.getElementById('op_btn_confirm_delete');
        btnDelete.style.display = 'block';
        
        const inputVerify = document.getElementById('op_input_verify_username');
        inputVerify.focus();

        // Real-time matching logic
        inputVerify.oninput = function () {
            if (this.value === currentTargetOpUsername && currentTargetOpUsername !== "") {
                btnDelete.classList.add('active');
                btnDelete.disabled = false;
            } else {
                btnDelete.classList.remove('active');
                btnDelete.disabled = true;
            }
        };
    }

    function closeDeleteOperatorModal() {
        const modal = document.getElementById('deleteOperatorModal');
        if(modal) {
            // 1. Tambahkan class 'is-leaving' untuk memicu animasi Spring Out
            modal.classList.add('is-leaving');

            // 2. Tunggu sampai animasi selesai (300ms sesuai durasi CSS)
            setTimeout(() => {
                modal.classList.remove('is-active');
                modal.classList.remove('is-leaving'); // Reset class untuk pemakaian berikutnya
                document.body.style.overflow = 'auto';
            }, 300); 
        }
    }

    // Klik overlay untuk tutup
    document.getElementById('deleteOperatorModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteOperatorModal();
    });
</script>
@endonce