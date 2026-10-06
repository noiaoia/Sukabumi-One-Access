{{-- resources/views/components/admin/modal-delete-category.blade.php --}}
@once
<style>
    /* 1. ANIMATION KEYFRAMES */
    @keyframes delCatOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes delCatOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes delCatSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes delCatSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(.96) translateY(10px); }
    }

    /* 2. OVERLAY */
    .del-cat-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85);
        backdrop-filter: blur(8px); 
        display: none; /* Default Hidden */
        align-items: center; justify-content: center;
        z-index: 10000; padding: 15px; opacity: 0;
    }
    .del-cat-overlay.is-active { display: flex; opacity: 1; animation: delCatOverlayIn 0.3s ease forwards; }
    .del-cat-overlay.is-active .del-cat-card { animation: delCatSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .del-cat-overlay.is-closing { animation: delCatOverlayOut .3s ease forwards; }
    .del-cat-overlay.is-closing .del-cat-card { animation: delCatSpringOut .3s ease forwards; }

    /* 3. CARD DESIGN */
    .del-cat-card {
        background: #ffffff; width: 100%; max-width: 380px; max-height: 80vh;
        border-radius: 24px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        display: flex; flex-direction: column;
    }

    .del-cat-header {
        background: #0A1E33; padding: 20px;
        border-bottom: 3px solid #E8A33D; 
        display: flex; align-items: center; gap: 12px;
        flex-shrink: 0;
    }
    .del-cat-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    .del-cat-body { padding: 24px; overflow-y: auto; flex: 1; }
    
    .del-cat-warning-box {
        background: #FFFBEB; border: 1px solid #FEF3C7;
        padding: 14px; border-radius: 12px; margin-bottom: 20px;
    }
    .del-cat-warning-box p { color: #92400E; font-size: 12px; line-height: 1.5; font-weight: 600; margin: 0; }

    .del-cat-danger-list { margin: 12px 0 0; padding-left: 18px; color: #B45309; font-size: 11px; font-weight: 600; }
    .del-cat-danger-list li { margin-bottom: 4px; }

    .del-cat-target-chip {
        display: flex; align-items: center; gap: 10px;
        background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;
        padding: 10px 12px; margin-bottom: 16px;
    }
    .del-cat-target-icon {
        width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: #123457; color: #ffffff; font-size: 0.95rem;
    }
    .del-cat-target-name { font-size: 12.5px; font-weight: 800; color: #123457; line-height: 1.2; }
    .del-cat-target-type { font-size: 10px; font-weight: 700; color: #94a3b8; margin-top: 1px; text-transform: uppercase; }

    #cat_verification_step { display: none; margin-top: 15px; border-top: 1px dashed #E2E8F0; padding-top: 15px; }
    .del-cat-instruction { font-size: 11px; color: #64748B; margin-bottom: 10px; text-align: center; font-weight: 500; }
    .del-cat-instruction b { color: #123457; font-weight: 800; background: #F1F5F9; padding: 2px 6px; border-radius: 4px; }

    .del-cat-input-verify {
        width: 100%; padding: 12px; background: #F8FAFC; border: 2px solid #E2E8F0;
        border-radius: 12px; font-size: 13px; font-weight: 800; color: #123457;
        text-align: center; transition: 0.2s;
    }
    .del-cat-input-verify:focus { outline: none; border-color: #E8A33D; background: #fff; }

    .del-cat-footer { padding: 16px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; flex-direction: column; gap: 10px; flex-shrink: 0; }

    .del-cat-btn-step-1 {
        background: #123457; color: #fff; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: pointer;
        text-transform: uppercase; letter-spacing: 0.5px; transition: 0.2s;
    }
    .del-cat-btn-step-1:hover { background: #0A1E33; }

    .del-cat-btn-final {
        background: #E2E8F0; color: #94A3B8; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: not-allowed;
        text-transform: uppercase; display: none;
    }
    .del-cat-btn-final.active { 
        background: #E8A33D; color: #0A1E33; cursor: pointer; box-shadow: 0 4px 12px rgba(232, 163, 61, 0.3); 
    }

    .del-cat-btn-cancel {
        background: transparent; color: #64748B; border: none; padding: 5px;
        font-size: 11px; font-weight: 700; cursor: pointer; text-decoration: underline; text-align: center;
    }
</style>

<script>
    let currentTargetCatName = ""; 

    window.openDeleteCategoryModal = function(data, deleteUrl) { 
        const modal = document.getElementById('deleteCategoryModal');
        const form = document.getElementById('deleteCategoryAction');

        currentTargetCatName = data.title || "";
        form.action = deleteUrl;

        document.getElementById('del_cat_target_name').innerText = currentTargetCatName;
        document.getElementById('del_cat_name_hint').innerText = currentTargetCatName;

        const iconContainer = document.getElementById('del_cat_target_icon');
        iconContainer.innerHTML = `<i class="bi ${data.icon || 'bi-tag-fill'}"></i>`;
        
        // RESET UI STEPS
        document.getElementById('cat_warning_step').style.display = 'block';
        document.getElementById('cat_verification_step').style.display = 'none';
        document.getElementById('cat_btn_next_step').style.display = 'block';
        document.getElementById('cat_btn_confirm_delete').style.display = 'none';
        document.getElementById('cat_input_verify_name').value = "";
        
        // Tampilkan Modal
        modal.style.display = 'flex';
        // Delay sedikit agar browser sempat merender display:flex sebelum animasi jalan
        setTimeout(() => {
            modal.classList.add('is-active');
        }, 10);
        
        document.body.style.overflow = 'hidden';
    }

    window.goToVerifyStepCat = function() {
        document.getElementById('cat_warning_step').style.display = 'none';
        document.getElementById('cat_btn_next_step').style.display = 'none';
        
        document.getElementById('cat_verification_step').style.display = 'block';
        document.getElementById('cat_btn_confirm_delete').style.display = 'block';
        document.getElementById('cat_input_verify_name').focus();

        const input = document.getElementById('cat_input_verify_name');
        const btn = document.getElementById('cat_btn_confirm_delete');

        input.oninput = function() {
            if (this.value.trim() === currentTargetCatName.trim()) {
                btn.classList.add('active');
                btn.disabled = false;
            } else {
                btn.classList.remove('active');
                btn.disabled = true;
            }
        };
    }

    window.closeDeleteCategoryModal = function() {
        const modal = document.getElementById('deleteCategoryModal');
        if(modal) {
            modal.classList.add('is-closing');
            setTimeout(() => {
                modal.classList.remove('is-active', 'is-closing');
                modal.style.display = 'none'; // Sembunyikan total
                document.body.style.overflow = 'auto';
            }, 300);
        }
    }

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('deleteCategoryModal');
        if(e.target === modal) closeDeleteCategoryModal();
    });
</script>
@endonce

<div id="deleteCategoryModal" class="del-cat-overlay" style="display: none;" onclick="if(event.target === this) window.closeDeleteCategoryModal()">
    <div class="del-cat-card">
        <div class="del-cat-header">
            <div style="background: rgba(232, 163, 61, 0.15); padding: 6px; border-radius: 8px;">
                <i class="bi bi-trash3-fill text-accent"></i>
            </div>
            <h2>Hapus Kategori</h2>
        </div>

        <form id="deleteCategoryAction" method="POST" class="del-cat-body">
            @csrf
            @method('DELETE')

            <div class="del-cat-target-chip">
                <div class="del-cat-target-icon" id="del_cat_target_icon">
                    <i class="bi bi-tag-fill"></i>
                </div>
                <div class="min-w-0">
                    <p class="del-cat-target-name truncate" id="del_cat_target_name">-</p>
                    <p class="del-cat-target-type truncate">Opsi Kategori Layanan</p>
                </div>
            </div>

            <!-- STEP 1 -->
            <div id="cat_warning_step">
                <div class="del-cat-warning-box">
                    <p>Konfirmasi Penghapusan Data</p>
                    <ul class="del-cat-danger-list">
                        <li>Kategori tidak akan tersedia lagi untuk warga.</li>
                        <li>Seluruh struktur form di kategori ini akan terhapus.</li>
                        <li>Tindakan ini permanen & tidak bisa dibatalkan.</li>
                    </ul>
                </div>
            </div>

            <!-- STEP 2 -->
            <div id="cat_verification_step">
                <p class="del-cat-instruction">
                    Ketik nama kategori <b id="del_cat_name_hint">-</b> untuk konfirmasi.
                </p>
                <input type="text" id="cat_input_verify_name" class="del-cat-input-verify" placeholder="Ketik nama di sini..." autocomplete="off">
            </div>
        </form>

        <div class="del-cat-footer">
            <button type="button" id="cat_btn_next_step" class="del-cat-btn-step-1" onclick="goToVerifyStepCat()">
                Saya Mengerti, Lanjutkan
            </button>

            <button type="submit" form="deleteCategoryAction" id="cat_btn_confirm_delete" class="del-cat-btn-final" disabled>
                Hapus Kategori Permanen
            </button>

            <button type="button" class="del-cat-btn-cancel" onclick="closeDeleteCategoryModal()">
                Batalkan & Kembali
            </button>
        </div>
    </div>
</div>