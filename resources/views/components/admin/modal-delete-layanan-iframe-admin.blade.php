@once
<style>
    /* 1. ANIMATION KEYFRAMES (Nama unik agar tidak bentrok) */
    @keyframes delSvcOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes delSvcSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes delSvcOverlayOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }

    @keyframes delSvcSpringOut {
        from {
            opacity: 1;
            transform: scale(1) translateY(0);
        }

        to {
            opacity: 0;
            transform: scale(.96) translateY(10px);
        }
    }


    /* 2. OVERLAY */
    .del-svc-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85);
        backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 15px; opacity: 0;
    }
    .del-svc-overlay.is-active { display: flex; opacity: 1; animation: delSvcOverlayIn 0.3s ease forwards; }
    .del-svc-overlay.is-active .del-svc-card { animation: delSvcSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }

    .del-svc-overlay.is-closing {
        animation: delSvcOverlayOut .3s ease forwards;
    }

    .del-svc-overlay.is-closing .del-svc-card {
        animation: delSvcSpringOut .3s ease forwards;
    }

    /* 3. CARD DESIGN (Compact 380px) */
    .del-svc-card {
        background: #ffffff; width: 100%; max-width: 380px; max-height: 85vh;
        border-radius: 24px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        display: flex; flex-direction: column;
    }

    .del-svc-header {
        background: #0A1E33; padding: 20px;
        border-bottom: 3px solid #E8A33D;
        display: flex; align-items: center; gap: 12px;
        flex-shrink: 0;
    }
    .del-svc-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    .del-svc-body { padding: 24px; overflow-y: auto; flex: 1; }
    .del-svc-body::-webkit-scrollbar { width: 4px; }
    .del-svc-body::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }

    .del-svc-warning-box {
        background: #FFFBEB; border: 1px solid #FEF3C7;
        padding: 14px; border-radius: 12px; margin-bottom: 20px;
    }
    .del-svc-warning-box p { color: #92400E; font-size: 12px; line-height: 1.5; font-weight: 600; margin: 0; }

    .del-svc-danger-list { margin: 12px 0 0; padding-left: 18px; color: #B45309; font-size: 11px; font-weight: 600; }
    .del-svc-danger-list li { margin-bottom: 4px; }

    /* Target Service Chip */
    .del-svc-target-chip {
        display: flex; align-items: center; gap: 10px;
        background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;
        padding: 10px 12px; margin-bottom: 16px;
    }
    .del-svc-target-icon {
        width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: #123457; color: #ffffff; font-size: 0.95rem;
    }
    .del-svc-target-name { font-size: 12.5px; font-weight: 800; color: #123457; line-height: 1.2; }
    .del-svc-target-type { font-size: 10px; font-weight: 700; color: #94a3b8; margin-top: 1px; text-transform: uppercase; }

    /* STEP 2 AREA */
    #svc_verification_step { display: none; margin-top: 15px; border-top: 1px dashed #E2E8F0; padding-top: 15px; }
    .del-svc-instruction { font-size: 12px; color: #64748B; margin-bottom: 10px; text-align: center; font-weight: 500; }
    .del-svc-instruction b { color: #123457; font-weight: 800; background: #F1F5F9; padding: 2px 6px; border-radius: 4px; }

    .del-svc-input-verify {
        width: 100%; padding: 12px; background: #F8FAFC; border: 2px solid #E2E8F0;
        border-radius: 12px; font-size: 14px; font-weight: 800; color: #123457;
        text-align: center; transition: 0.2s;
    }
    .del-svc-input-verify:focus { outline: none; border-color: #E8A33D; background: #fff; }

    /* FOOTER */
    .del-svc-footer { padding: 16px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; flex-direction: column; gap: 10px; flex-shrink: 0; }

    .del-svc-btn-step-1 {
        background: #123457; color: #fff; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: pointer;
        text-transform: uppercase; letter-spacing: 0.5px;
    }

    .del-svc-btn-final {
        background: #E2E8F0; color: #94A3B8; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: not-allowed;
        text-transform: uppercase; display: none;
    }
    .del-svc-btn-final.active { background: #E11D48; color: #fff; cursor: pointer; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3); }

    .del-svc-btn-cancel {
        background: transparent; color: #64748B; border: none; padding: 5px;
        font-size: 11px; font-weight: 700; cursor: pointer; text-decoration: underline; text-align: center;
    }
</style>
@endonce

<div id="deleteLayananModal" class="del-svc-overlay">
    <div class="del-svc-card">
        <div class="del-svc-header">
            <div style="background: rgba(232, 163, 61, 0.15); padding: 6px; border-radius: 8px;">
                <i class="bi bi-trash3-fill text-accent"></i>
            </div>
            <h2>Hapus Layanan</h2>
        </div>

        <form id="deleteLayananForm" method="POST" class="del-svc-body">
            @csrf
            @method('DELETE')

            <div class="del-svc-target-chip">
                <div class="del-svc-target-icon" id="del_svc_target_icon"><i class="bi bi-window-fullscreen"></i></div>
                <div class="min-w-0">
                    <p class="del-svc-target-name truncate" id="del_svc_target_name">-</p>
                    <p class="del-svc-target-type truncate" id="del_svc_target_type_preview">Iframe Bridge</p>
                </div>
            </div>

            <!-- STEP 1: WARNING -->
            <div id="svc_warning_step">
                <div class="del-svc-warning-box">
                    <p>Peringatan: Jalur akses layanan akan diputus!</p>
                    <ul class="del-svc-danger-list">
                        <li>Layanan tidak akan muncul lagi di portal warga.</li>
                        <li>Seluruh konfigurasi visual & teknis akan dihapus.</li>
                        <li>Aplikasi eksternal tetap ada, namun link penghubung hilang.</li>
                    </ul>
                </div>
            </div>

            <!-- STEP 2: VERIFICATION -->
            <div id="svc_verification_step">
                <p class="del-svc-instruction">
                    Ketik nama layanan <b id="del_svc_name_hint">-</b> untuk konfirmasi.
                </p>
                <input type="text" id="svc_input_verify_name" class="del-svc-input-verify" placeholder="Ketik nama layanan..." autocomplete="off">
            </div>
        </form>

        <div class="del-svc-footer">
            <button type="button" id="svc_btn_next_step" class="del-svc-btn-step-1" onclick="goToVerifyStepSvc()">
                Saya mengerti konsekuensinya
            </button>

            <button type="submit" form="deleteLayananForm" id="svc_btn_confirm_delete" class="del-svc-btn-final" disabled>
                Hapus Layanan Permanen
            </button>

            <button type="button" class="del-svc-btn-cancel" onclick="closeDeleteLayananModal()">
                Batalkan & Kembali
            </button>
        </div>
    </div>
</div>

@once
<script>
    let currentTargetSvcName = "";

    function openDeleteLayananModal(data, deleteUrl) {
        const modal = document.getElementById('deleteLayananModal');
        const form = document.getElementById('deleteLayananForm');

        currentTargetSvcName = data.nama || "";
        form.action = deleteUrl;

        // --- PERBAIKAN BUG ICON DI SINI ---
        const iconContainer = document.getElementById('del_svc_target_icon');
        // Ambil icon dari data, jika tidak ada gunakan default bi-window-fullscreen
        const iconClass = data.icon || 'bi-window-fullscreen'; 
        // Render ulang isi container icon agar tag <i> mendapatkan class yang benar
        iconContainer.innerHTML = `<i class="bi ${iconClass}"></i>`;
        
        // Update Warna Latar Belakang Icon
        if (data.primary_color) {
            iconContainer.style.backgroundColor = data.color;
        } else {
            iconContainer.style.backgroundColor = '#123457'; // Warna default
        }
        // ----------------------------------

        // Update UI Chip Lainnya
        document.getElementById('del_svc_target_name').innerText = currentTargetSvcName;
        document.getElementById('del_svc_target_type_preview').innerText = (data.jenis === 'iframe') ? 'Portal Iframe' : 'Formulir Mandiri';
        
        // Reset UI ke Step 1
        document.getElementById('del_svc_name_hint').innerText = currentTargetSvcName;
        document.getElementById('svc_warning_step').style.display = 'block';
        document.getElementById('svc_verification_step').style.display = 'none';
        document.getElementById('svc_btn_next_step').style.display = 'block';
        document.getElementById('svc_btn_confirm_delete').style.display = 'none';
        document.getElementById('svc_input_verify_name').value = "";
        
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function goToVerifyStepSvc() {
        document.getElementById('svc_warning_step').style.display = 'none';
        document.getElementById('svc_btn_next_step').style.display = 'none';
        
        document.getElementById('svc_verification_step').style.display = 'block';
        document.getElementById('svc_btn_confirm_delete').style.display = 'block';
        document.getElementById('svc_input_verify_name').focus();

        const input = document.getElementById('svc_input_verify_name');
        const btn = document.getElementById('svc_btn_confirm_delete');

        input.oninput = function() {
            if (this.value === currentTargetSvcName) {
                btn.classList.add('active');
                btn.disabled = false;
            } else {
                btn.classList.remove('active');
                btn.disabled = true;
            }
        };
    }

    function closeDeleteLayananModal() {
        const modal = document.getElementById('deleteLayananModal');
        if(modal) {
            modal.classList.add('is-closing');

            setTimeout(() => {
                modal.classList.remove('is-active');
                modal.classList.remove('is-closing');
                document.body.style.overflow = 'auto';
            },300);
        }
    }

    // Klik luar untuk tutup
    document.getElementById('deleteLayananModal')?.addEventListener('click', function(e) {
        if(e.target === this) closeDeleteLayananModal();
    });

    // Escape untuk tutup
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteLayananModal();
    });
</script>
@endonce