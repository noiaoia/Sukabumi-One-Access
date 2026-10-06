@once
<style>
    /* 1. ANIMATION KEYFRAMES (Nama unik agar tidak bentrok) */
    @keyframes delInsOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes delInsSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes delInsOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes delInsSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.96) translateY(10px); }
    }

    /* 2. OVERLAY */
    .del-ins-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85);
        backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 15px;
    }
    .del-ins-overlay.is-active {
        display: flex;
        animation: delInsOverlayIn .3s ease forwards;
    }

    .del-ins-overlay.is-active .del-ins-card {
        animation: delInsSpringIn .45s cubic-bezier(.34,1.56,.64,1) forwards;
    }

    .del-ins-overlay.is-closing {
        animation: delInsOverlayOut .3s ease forwards;
    }

    .del-ins-overlay.is-closing .del-ins-card {
        animation: delInsSpringOut .3s ease forwards;
    }

    /* 3. CARD DESIGN */
    .del-ins-card {
        background: #ffffff; width: 100%; max-width: 380px; max-height: 85vh;
        border-radius: 24px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        display: flex; flex-direction: column;
    }

    .del-ins-header {
        background: #0A1E33; padding: 20px;
        border-bottom: 3px solid #E8A33D;
        display: flex; align-items: center; gap: 12px;
        flex-shrink: 0;
    }
    .del-ins-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    .del-ins-body { padding: 24px; overflow-y: auto; flex: 1; }
    .del-ins-body::-webkit-scrollbar { width: 4px; }
    .del-ins-body::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }

    .del-ins-warning-box {
        background: #FFFBEB; border: 1px solid #FEF3C7;
        padding: 14px; border-radius: 12px; margin-bottom: 20px;
    }
    .del-ins-warning-box p { color: #92400E; font-size: 12px; line-height: 1.5; font-weight: 600; margin: 0; }

    .del-ins-danger-list { margin: 12px 0 0; padding-left: 18px; color: #B45309; font-size: 11px; font-weight: 600; }
    .del-ins-danger-list li { margin-bottom: 4px; }

    /* Target Agency Chip */
    .del-ins-target-chip {
        display: flex; align-items: center; gap: 10px;
        background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;
        padding: 10px 12px; margin-bottom: 16px;
    }
    .del-ins-target-icon {
        width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: #123457; color: #E8A33D; font-size: 0.95rem;
    }
    .del-ins-target-name { font-size: 12.5px; font-weight: 800; color: #123457; line-height: 1.2; }
    .del-ins-target-slug { font-size: 10px; font-weight: 700; color: #94a3b8; margin-top: 1px; }

    /* STEP 2 AREA */
    #ins_verification_step { display: none; margin-top: 15px; border-top: 1px dashed #E2E8F0; padding-top: 15px; }
    .del-ins-instruction { font-size: 12px; color: #64748B; margin-bottom: 10px; text-align: center; font-weight: 500; }
    .del-ins-instruction b { color: #123457; font-weight: 800; background: #F1F5F9; padding: 2px 6px; border-radius: 4px; }

    .del-ins-input-verify {
        width: 100%; padding: 12px; background: #F8FAFC; border: 2px solid #E2E8F0;
        border-radius: 12px; font-size: 14px; font-weight: 800; color: #123457;
        text-align: center; transition: 0.2s;
    }
    .del-ins-input-verify:focus { outline: none; border-color: #E8A33D; background: #fff; }

    /* FOOTER */
    .del-ins-footer { padding: 16px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; flex-direction: column; gap: 10px; flex-shrink: 0; }

    .del-ins-btn-step-1 {
        background: #123457; color: #fff; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: pointer;
        text-transform: uppercase; letter-spacing: 0.5px;
    }

    .del-ins-btn-final {
        background: #E2E8F0; color: #94A3B8; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: not-allowed;
        text-transform: uppercase; display: none;
    }
    .del-ins-btn-final.active { background: #E11D48; color: #fff; cursor: pointer; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3); }

    .del-ins-btn-cancel {
        background: transparent; color: #64748B; border: none; padding: 5px;
        font-size: 11px; font-weight: 700; cursor: pointer; text-decoration: underline; text-align: center;
    }
</style>
@endonce

<div id="deleteInstansiModal" class="del-ins-overlay">
    <div class="del-ins-card">
        <div class="del-ins-header">
            <div style="background: rgba(232, 163, 61, 0.15); padding: 6px; border-radius: 8px;">
                <i class="bi bi-exclamation-octagon-fill text-accent"></i>
            </div>
            <h2>Terminasi Instansi</h2>
        </div>

        <form id="deleteInstansiForm" method="POST" class="del-ins-body">
            @csrf
            @method('DELETE')

            <div class="del-ins-target-chip">
                <div class="del-ins-target-icon" id="del_ins_target_icon"><i class="bi bi-building"></i></div>
                <div class="min-w-0">
                    <p class="del-ins-target-name truncate" id="del_ins_target_name">-</p>
                    <p class="del-ins-target-slug truncate" id="del_ins_target_slug_preview">-</p>
                </div>
            </div>

            <!-- STEP 1: WARNING -->
            <div id="ins_warning_step">
                <div class="del-ins-warning-box">
                    <p>Peringatan: Instansi akan dihapus secara permanen!</p>
                    <ul class="del-ins-danger-list">
                        <li>Seluruh profil dan visual branding akan dihapus.</li>
                        <li>Daftar layanan terkait tidak akan bisa diakses user.</li>
                        <li>Data log aktivitas instansi akan dimusnahkan.</li>
                    </ul>
                </div>
            </div>

            <!-- STEP 2: VERIFICATION -->
            <div id="ins_verification_step">
                <p class="del-ins-instruction">
                    Ketik slug instansi <b id="del_ins_slug_hint">-</b> untuk konfirmasi.
                </p>
                <input type="text" id="ins_input_verify_slug" class="del-ins-input-verify" placeholder="Ketik slug di sini..." autocomplete="off">
            </div>
        </form>

        <div class="del-ins-footer">
            <button type="button" id="ins_btn_next_step" class="del-ins-btn-step-1" onclick="goToVerifyStepIns()">
                Saya mengerti konsekuensinya
            </button>

            <button type="submit" form="deleteInstansiForm" id="ins_btn_confirm_delete" class="del-ins-btn-final" disabled>
                Hapus Instansi Permanen
            </button>

            <button type="button" class="del-ins-btn-cancel" onclick="closeDeleteInstansiModal()">
                Batalkan & Kembali
            </a>
        </div>
    </div>
</div>

@once
<script>
    let currentTargetInsSlug = "";

    function openDeleteInstansiModal(data, deleteUrl) {
        const modal = document.getElementById('deleteInstansiModal');
        const form = document.getElementById('deleteInstansiForm');

        currentTargetInsSlug = data.slug || "";
        form.action = deleteUrl;

        // Update UI Chip
        document.getElementById('del_ins_target_name').innerText = data.nama || '-' || "-";
        document.getElementById('del_ins_target_slug_preview').innerText = '/' + currentTargetInsSlug;
        if (data.primary_color) {
            document.getElementById('del_ins_target_icon').style.background =
                data.primary_color;
        }

        // Reset UI ke Step 1
        document.getElementById('del_ins_slug_hint').innerText = currentTargetInsSlug;
        document.getElementById('ins_warning_step').style.display = 'block';
        document.getElementById('ins_verification_step').style.display = 'none';
        document.getElementById('ins_btn_next_step').style.display = 'block';
        document.getElementById('ins_btn_confirm_delete').style.display = 'none';
        document.getElementById('ins_input_verify_slug').value = "";
        
        modal.classList.remove('is-closing');
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function goToVerifyStepIns() {
        document.getElementById('ins_warning_step').style.display = 'none';
        document.getElementById('ins_btn_next_step').style.display = 'none';
        
        document.getElementById('ins_verification_step').style.display = 'block';
        document.getElementById('ins_btn_confirm_delete').style.display = 'block';
        document.getElementById('ins_input_verify_slug').focus();

        const input = document.getElementById('ins_input_verify_slug');
        const btn = document.getElementById('ins_btn_confirm_delete');

        input.oninput = function() {
            if (this.value === currentTargetInsSlug) {
                btn.classList.add('active');
                btn.disabled = false;
            } else {
                btn.classList.remove('active');
                btn.disabled = true;
            }
        };
    }

    function closeDeleteInstansiModal() {
        const modal = document.getElementById('deleteInstansiModal');

        modal.classList.add('is-closing');

        setTimeout(() => {
            modal.classList.remove('is-active');
            modal.classList.remove('is-closing');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    document.addEventListener('keydown', function (e) {
        const modal = document.getElementById('deleteInstansiModal');

        if (e.key === 'Escape' && modal.classList.contains('is-active')) {
            closeDeleteInstansiModal();
        }
    });

    document.getElementById('deleteInstansiModal')?.addEventListener('click', function(e) {
        if(e.target === this) closeDeleteInstansiModal();
    });
</script>
@endonce