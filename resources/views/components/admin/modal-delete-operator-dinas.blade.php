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
        30% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 0; transform: scale(0.9) translateY(20px); }
    }

    .del-op-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85);
        backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 15px;
    }
    .del-op-overlay.is-active { display: flex; animation: delOpOverlayIn 0.3s ease forwards; }
    .del-op-overlay.is-active .del-op-card { animation: delOpSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }

    .del-op-overlay.is-leaving { animation: delOpOverlayOut 0.3s ease forwards; }
    .del-op-overlay.is-leaving .del-op-card { animation: delOpSpringOut 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }

    .del-op-card {
        background: #ffffff; width: 100%; max-width: 380px; max-height: 80vh;
        border-radius: 24px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        display: flex; flex-direction: column;
    }

    /* HEADER — disamakan dengan modal delete admin dinas (navy + border emas) */
    .del-op-header {
        background: #0A1E33; padding: 20px;
        border-bottom: 3px solid #E8A33D;
        display: flex; align-items: center; gap: 12px;
        flex-shrink: 0;
    }
    .del-op-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    /* BODY */
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
    .del-op-target-label { font-size: 10px; font-weight: 700; color: #94a3b8; margin-top: 1px; }

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

    .del-op-btn-confirm {
        background: #E2E8F0; color: #94A3B8; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: not-allowed;
        text-transform: uppercase; letter-spacing: 0.5px; transition: 0.3s;
        display: none; align-items: center; justify-content: center; gap: 6px;
    }
    .del-op-btn-confirm.active { background: #E11D48; color: #fff; cursor: pointer; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3); }
    .del-op-btn-confirm.active:hover { background: #BE123C; }
    .del-op-btn-confirm:disabled { background: #E2E8F0; color: #94A3B8; }

    .del-op-btn-cancel {
        background: transparent; color: #64748B; border: none; padding: 5px;
        font-size: 11px; font-weight: 700; cursor: pointer; text-decoration: underline;
    }
</style>
@endonce

<div id="deleteOperatorDinasModal" class="del-op-overlay">
    <div class="del-op-card">
        <div class="del-op-header">
            <div style="background: rgba(232, 163, 61, 0.15); padding: 6px; border-radius: 8px;">
                <i class="bi bi-person-badge-fill text-[#E8A33D]"></i>
            </div>
            <h2>Hapus Staf Operator</h2>
        </div>

        <form id="deleteOperatorDinasForm" method="POST" class="del-op-body">
            @csrf
            @method('DELETE')

            <div class="del-op-target-chip">
                <div class="del-op-target-icon"><i class="bi bi-person-x-fill"></i></div>
                <div class="min-w-0 text-left">
                    <p class="del-op-target-name truncate" id="del_op_name">-</p>
                    <p class="del-op-target-label truncate" id="del_op_user_preview">Staf Pelaksana</p>
                </div>
            </div>

            <div id="op_warning_step">
                <div class="del-op-warning-box text-left">
                    <p>Menghapus staf operator bersifat permanen!</p>
                    <ul class="del-op-danger-list">
                        <li>Seluruh akses staf pelaksana ini akan dicabut.</li>
                        <li>Data tidak dapat dikembalikan setelah dihapus.</li>
                        <li>Tindakan ini tidak dapat dibatalkan.</li>
                    </ul>
                </div>
            </div>

            <div id="op_verification_step">
                <p class="del-op-instruction-text">
                    Ketik kembali username <b id="del_op_hint">-</b> untuk mengonfirmasi penghapusan.
                </p>
                <input type="text" id="op_input_verify" class="del-op-input-verify" placeholder="Ketik username di sini..." autocomplete="off">
            </div>
        </form>

        <div class="del-op-footer">
            <button type="button" id="op_btn_next" class="del-op-btn-step-1" onclick="window.goToVerifyStepOperatorDinas()">
                Saya Mengerti
            </button>

            <button type="submit" form="deleteOperatorDinasForm" id="btnConfirmDeleteOp" class="del-op-btn-confirm" disabled>
                <i class="bi bi-trash3"></i> Hapus Staf Operator
            </button>

            <button type="button" class="del-op-btn-cancel" onclick="closeDeleteOperatorDinasModal()">
                Batalkan
            </button>
        </div>
    </div>
</div>

@once
<script>
    window.deleteOperatorDinasState = { username: '' };

    window.handleDeleteOperatorDinas = function(btn) {
        try {
            const data = JSON.parse(btn.getAttribute('data-op'));
            const url = btn.getAttribute('data-url');
            window.openDeleteOperatorDinasModal(data, url);
        } catch (e) {
            console.error('Gagal memproses data operator dinas:', e);
        }
    };

    window.openDeleteOperatorDinasModal = function(data, deleteUrl) {
        const modal = document.getElementById('deleteOperatorDinasModal');
        if (!modal) { console.error('Modal Delete Operator Dinas tidak ditemukan.'); return; }

        const form = modal.querySelector('#deleteOperatorDinasForm');
        const nameEl = modal.querySelector('#del_op_name');
        const previewEl = modal.querySelector('#del_op_user_preview');
        const hintEl = modal.querySelector('#del_op_hint');
        const warning = modal.querySelector('#op_warning_step');
        const verification = modal.querySelector('#op_verification_step');
        const nextButton = modal.querySelector('#op_btn_next');
        const confirmButton = modal.querySelector('#btnConfirmDeleteOp');
        const input = modal.querySelector('#op_input_verify');

        if (!form || !nameEl || !previewEl || !hintEl || !warning || !verification || !nextButton || !confirmButton || !input) {
            console.error('Element Modal Delete Operator Dinas tidak lengkap.');
            return;
        }

        window.deleteOperatorDinasState.username = (data.username || '').trim();
        form.action = deleteUrl;

        nameEl.innerText = data.name || '-';
        previewEl.innerText = window.deleteOperatorDinasState.username ? '@' + window.deleteOperatorDinasState.username : 'Staf Pelaksana';
        hintEl.innerText = window.deleteOperatorDinasState.username || '-';

        warning.style.display = 'block';
        verification.style.display = 'none';
        nextButton.style.display = 'block';

        confirmButton.style.display = 'none';
        confirmButton.classList.remove('active');
        confirmButton.disabled = true;
        confirmButton.innerHTML = '<i class="bi bi-trash3"></i> Hapus Staf Operator';

        input.value = '';

        modal.classList.remove('is-leaving');
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    };

    window.goToVerifyStepOperatorDinas = function() {
        const modal = document.getElementById('deleteOperatorDinasModal');
        if (!modal) { console.error('Modal Delete Operator Dinas tidak ditemukan.'); return; }

        const warning = modal.querySelector('#op_warning_step');
        const verification = modal.querySelector('#op_verification_step');
        const nextButton = modal.querySelector('#op_btn_next');
        const confirmButton = modal.querySelector('#btnConfirmDeleteOp');
        const input = modal.querySelector('#op_input_verify');

        if (!warning || !verification || !nextButton || !confirmButton || !input) {
            console.error('Element Modal Delete Operator Dinas tidak lengkap.');
            return;
        }

        warning.style.display = 'none';
        nextButton.style.display = 'none';
        verification.style.display = 'block';
        confirmButton.style.display = 'flex';

        setTimeout(() => input.focus(), 100);

        input.oninput = function() {
            const typedUsername = this.value.trim();
            const targetUsername = window.deleteOperatorDinasState.username;
            const isValid = typedUsername === targetUsername && targetUsername !== '';

            confirmButton.disabled = !isValid;
            confirmButton.classList.toggle('active', isValid);
        };
    };

    window.closeDeleteOperatorDinasModal = function() {
        const modal = document.getElementById('deleteOperatorDinasModal');
        if (!modal) return;

        modal.classList.add('is-leaving');

        setTimeout(() => {
            modal.classList.remove('is-active', 'is-leaving');
            document.body.style.overflow = '';
            window.deleteOperatorDinasState.username = '';

            const input = modal.querySelector('#op_input_verify');
            const confirmButton = modal.querySelector('#btnConfirmDeleteOp');

            if (input) input.value = '';
            if (confirmButton) {
                confirmButton.disabled = true;
                confirmButton.classList.remove('active');
                confirmButton.style.display = 'none';
            }
        }, 300);
    };

    const initDeleteOperatorDinasModal = function() {
        const form = document.getElementById('deleteOperatorDinasForm');
        const modal = document.getElementById('deleteOperatorDinasModal');

        if (form && !form.dataset.deleteOperatorInitialized) {
            form.dataset.deleteOperatorInitialized = 'true';

            form.addEventListener('submit', function() {
                const btn = modal ? modal.querySelector('#btnConfirmDeleteOp') : null;
                if (!btn) return;

                btn.disabled = true;
                btn.classList.remove('active');
                btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Menghapus...';
            });
        }

        if (modal && !modal.dataset.deleteOperatorOverlayInitialized) {
            modal.dataset.deleteOperatorOverlayInitialized = 'true';

            modal.addEventListener('click', function(e) {
                if (e.target === this) window.closeDeleteOperatorDinasModal();
            });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDeleteOperatorDinasModal);
    } else {
        initDeleteOperatorDinasModal();
    }
</script>
@endonce