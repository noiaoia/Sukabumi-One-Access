@once
<style>
    /* ANIMASI */
    @keyframes delAdmOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes delAdmSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }

    /* ANIMASI KELUAR */
    @keyframes delAdmOverlayOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }

    @keyframes delAdmSpringOut {
        0% { opacity: 1; transform: scale(1) translateY(0); }
        30% { transform: scale(1.02) translateY(-5px); } /* Sedikit membal ke atas sebelum turun */
        100% { opacity: 0; transform: scale(0.9) translateY(20px); }
    }

    .del-adm-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85);
        backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 15px;
    }
    .del-adm-overlay.is-active { display: flex; animation: delAdmOverlayIn 0.3s ease forwards; }
    .del-adm-overlay.is-active .del-adm-card { animation: delAdmSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }

    /* State saat modal sedang menutup */
    .del-adm-overlay.is-leaving {
        animation: delAdmOverlayOut 0.3s ease forwards;
    }

    .del-adm-overlay.is-leaving .del-adm-card {
        animation: delAdmSpringOut 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .del-adm-card {
        background: #ffffff; width: 100%; max-width: 380px; max-height: 80vh;
        border-radius: 24px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        display: flex; flex-direction: column;
    }

    /* HEADER — identik dengan modal edit/hapus lain (navy + border gold) */
    .del-adm-header {
        background: #0A1E33; padding: 20px;
        border-bottom: 3px solid #E8A33D;
        display: flex; align-items: center; gap: 12px;
        flex-shrink: 0;
    }
    .del-adm-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    /* BODY & STEPPER — scroll sendiri kalau kontennya lebih tinggi dari max-height kartu */
    .del-adm-body { padding: 24px; overflow-y: auto; flex: 1; }
    .del-adm-body::-webkit-scrollbar { width: 4px; }
    .del-adm-body::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }

    .del-adm-warning-box {
        background: #FFFBEB; border: 1px solid #FEF3C7;
        padding: 14px; border-radius: 12px; margin-bottom: 20px;
    }
    .del-adm-warning-box p { color: #92400E; font-size: 12px; line-height: 1.5; font-weight: 600; margin: 0; }

    .del-adm-danger-list { margin: 12px 0 0; padding-left: 18px; color: #B45309; font-size: 11px; font-weight: 600; }
    .del-adm-danger-list li { margin-bottom: 4px; }

    /* Info ringkas admin dinas yang mau dihapus */
    .del-adm-target-chip {
        display: flex; align-items: center; gap: 10px;
        background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;
        padding: 10px 12px; margin-bottom: 16px;
    }
    .del-adm-target-icon {
        width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: #123457; color: #E8A33D; font-size: 0.95rem;
    }
    .del-adm-target-name { font-size: 12.5px; font-weight: 800; color: #123457; line-height: 1.2; }
    .del-adm-target-user { font-size: 10px; font-weight: 700; color: #94a3b8; margin-top: 1px; }

    /* VERIFICATION AREA (Hidden initially) */
    #adm_verification_step { display: none; margin-top: 15px; border-top: 1px dashed #E2E8F0; padding-top: 15px; }

    .del-adm-instruction-text { font-size: 12px; color: #64748B; margin-bottom: 10px; text-align: center; font-weight: 500; }
    .del-adm-instruction-text b { color: #123457; font-weight: 800; background: #F1F5F9; padding: 2px 6px; border-radius: 4px; }

    .del-adm-input-verify {
        width: 100%; padding: 12px; background: #F8FAFC; border: 2px solid #E2E8F0;
        border-radius: 12px; font-size: 14px; font-weight: 800; color: #123457;
        text-align: center; transition: 0.2s;
    }
    .del-adm-input-verify:focus { outline: none; border-color: #E8A33D; background: #fff; }

    /* FOOTER BUTTONS */
    .del-adm-footer { padding: 16px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; flex-direction: column; gap: 10px; flex-shrink: 0; }

    .del-adm-btn-step-1 {
        background: #123457; color: #fff; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: pointer;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .del-adm-btn-step-1:hover { background: #0A1E33; }

    .del-adm-btn-final {
        background: #E2E8F0; color: #94A3B8; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: not-allowed;
        text-transform: uppercase; display: none;
    }
    .del-adm-btn-final.active { background: #E11D48; color: #fff; cursor: pointer; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3); }

    .del-adm-btn-cancel {
        background: transparent; color: #64748B; border: none; padding: 5px;
        font-size: 11px; font-weight: 700; cursor: pointer; text-decoration: underline;
    }
</style>
@endonce

<div id="deleteAdminDinasModal" class="del-adm-overlay">
    <div class="del-adm-card">
        <div class="del-adm-header">
            <div style="background: rgba(232, 163, 61, 0.15); padding: 6px; border-radius: 8px;">
                <i class="bi bi-shield-lock-fill text-[#E8A33D]"></i>
            </div>
            <h2>Otoritas Keamanan</h2>
        </div>

        <form id="deleteAdminDinasForm" method="POST" class="del-adm-body">
            @csrf
            @method('DELETE')

            <div class="del-adm-target-chip">
                <div class="del-adm-target-icon"><i class="bi bi-person-badge-fill"></i></div>
                <div class="min-w-0 text-left">
                    <p class="del-adm-target-name truncate" id="del_adm_name">-</p>
                    <p class="del-adm-target-user truncate" id="del_adm_user_preview">-</p>
                </div>
            </div>

            <div id="adm_warning_step">
                <div class="del-adm-warning-box text-left">
                    <p>Mencabut akses Admin Dinas bersifat permanen!</p>
                    <ul class="del-adm-danger-list">
                        <li>Akses login ke instansi terkait akan diputus.</li>
                        <li>Riwayat log aktivitas admin akan diarsipkan.</li>
                        <li>Tindakan ini tidak dapat dibatalkan.</li>
                    </ul>
                </div>
            </div>

            <div id="adm_verification_step">
                <p class="del-adm-instruction-text">
                    Ketik kembali username <b id="del_adm_hint">-</b> untuk mengonfirmasi penghapusan.
                </p>
                <input type="text" id="adm_input_verify" class="del-adm-input-verify" placeholder="Ketik username di sini..." autocomplete="off">
            </div>
        </form>

        <div class="del-adm-footer">
            <button type="button" id="adm_btn_next" class="del-adm-btn-step-1" onclick="goToVerifyStepAdm()">
                Saya Mengerti
            </button>

            <button type="submit" form="deleteAdminDinasForm" id="adm_btn_confirm" class="del-adm-btn-final">
                Hapus Otoritas Permanen
            </button>

            <button type="button" class="del-adm-btn-cancel" onclick="closeDeleteAdminDinasModal()">
                Batalkan
            </button>
        </div>
    </div>
</div>

@once
<script>
    let currentAdmUsername = "";

    function openDeleteAdminDinasModal(data, deleteUrl) {
        const modal = document.getElementById('deleteAdminDinasModal');
        const form = document.getElementById('deleteAdminDinasForm');

        // Pastikan mengambil data username dengan benar
        currentAdmUsername = data.username || "";
        form.action = deleteUrl;

        // Update UI Chip
        document.getElementById('del_adm_name').innerText = data.name || "-";
        document.getElementById('del_adm_user_preview').innerText = '@' + currentAdmUsername;
        document.getElementById('del_adm_hint').innerText = currentAdmUsername;

        // Reset UI Modal ke Step 1
        document.getElementById('adm_warning_step').style.display = 'block';
        document.getElementById('adm_verification_step').style.display = 'none';
        document.getElementById('adm_btn_next').style.display = 'block';

        const btnFinal = document.getElementById('adm_btn_confirm');
        btnFinal.style.display = 'none';
        btnFinal.classList.remove('active');
        btnFinal.disabled = true;

        document.getElementById('adm_input_verify').value = "";

        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function goToVerifyStepAdm() {
        document.getElementById('adm_warning_step').style.display = 'none';
        document.getElementById('adm_btn_next').style.display = 'none';

        document.getElementById('adm_verification_step').style.display = 'block';
        const btnFinal = document.getElementById('adm_btn_confirm');
        btnFinal.style.display = 'block';

        const input = document.getElementById('adm_input_verify');
        input.focus();

        // Real-time matching logic
        input.oninput = function () {
            if (this.value === currentAdmUsername && currentAdmUsername !== "") {
                btnFinal.classList.add('active');
                btnFinal.disabled = false;
            } else {
                btnFinal.classList.remove('active');
                btnFinal.disabled = true;
            }
        };
    }

    function closeDeleteAdminDinasModal() {
        const modal = document.getElementById('deleteAdminDinasModal');
        if (modal) {
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
    document.getElementById('deleteAdminDinasModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteAdminDinasModal();
    });
</script>
@endonce