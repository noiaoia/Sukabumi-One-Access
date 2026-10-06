{{-- resources/views/components/admin/modal-delete-user.blade.php --}}
@props(['portalCode'])

@once
<style>
    /* 1. ANIMATION KEYFRAMES */
    @keyframes delUserOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes delUserOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes delUserSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.01) translateY(-4px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes delUserSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.96) translateY(10px); }
    }

    /* 2. OVERLAY */
    .del-user-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85);
        backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center;
        z-index: 12000; padding: 15px; opacity: 0;
    }
    .del-user-overlay.is-active { display: flex; opacity: 1; animation: delUserOverlayIn 0.3s ease forwards; }
    .del-user-overlay.is-active .del-user-card { animation: delUserSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .del-user-overlay.is-closing { animation: delUserOverlayOut 0.3s ease forwards; }
    .del-user-overlay.is-closing .del-user-card { animation: delUserSpringOut 0.3s ease forwards; }

    /* 3. CARD DESIGN */
    .del-user-card {
        background: #ffffff; width: 100%; max-width: 400px; max-height: 85vh;
        border-radius: 24px; overflow: hidden;
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.5);
        display: flex; flex-direction: column;
    }

    .del-user-header {
        background: #0A1E33; padding: 20px 25px;
        border-bottom: 4px solid #E8A33D; /* TEMA OREN */
        display: flex; align-items: center; gap: 15px;
        flex-shrink: 0;
    }
    .del-user-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .del-user-header .icon-box {
        width: 42px; height: 42px; border-radius: 12px;
        background: rgba(232, 163, 61, 0.15); color: #E8A33D;
        display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
    }

    .del-user-body { padding: 24px; overflow-y: auto; flex: 1; }
    
    .del-user-target-chip {
        display: flex; align-items: center; gap: 12px;
        background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 16px;
        padding: 12px; margin-bottom: 20px;
    }
    .del-user-target-avatar {
        width: 45px; height: 45px; border-radius: 12px;
        background: #123457; color: #fff;
        display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem;
    }
    .del-user-target-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; }

    /* WARNING BOX - TEMA OREN SOFT */
    .del-user-warning-box {
        background: #FFFBEB; border: 1px solid #FEF3C7;
        padding: 14px; border-radius: 14px; margin-bottom: 20px;
    }
    .del-user-warning-box p { color: #92400E; font-size: 12px; line-height: 1.5; font-weight: 600; margin: 0; }
    .del-user-danger-list { margin: 10px 0 0; padding-left: 18px; color: #B45309; font-size: 11px; font-weight: 600; }

    /* VERIFICATION STEP */
    #user_del_verification_step { display: none; margin-top: 15px; border-top: 1px dashed #E2E8F0; padding-top: 15px; }
    .del-user-instruction { font-size: 12px; color: #64748B; margin-bottom: 12px; text-align: center; font-weight: 500; }
    .del-user-instruction b { color: #123457; font-weight: 800; background: #F1F5F9; padding: 2px 6px; border-radius: 4px; }

    .del-user-input-verify {
        width: 100%; padding: 12px; background: #F8FAFC; border: 2px solid #E2E8F0;
        border-radius: 12px; font-size: 14px; font-weight: 800; color: #123457;
        text-align: center; transition: 0.2s;
    }
    .del-user-input-verify:focus { outline: none; border-color: #E8A33D; background: #fff; }

    /* FOOTER */
    .del-user-footer { padding: 16px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; flex-direction: column; gap: 10px; flex-shrink: 0; }

    .btn-user-del-step-1 {
        background: #123457; color: #fff; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: pointer;
        text-transform: uppercase; letter-spacing: 0.5px; transition: 0.2s;
    }
    .btn-user-del-step-1:hover { background: #0A1E33; }

    .btn-user-del-final {
        background: #E2E8F0; color: #94A3B8; border: none; padding: 14px;
        border-radius: 12px; font-size: 11px; font-weight: 800; cursor: not-allowed;
        text-transform: uppercase; display: none;
    }
    .btn-user-del-final.active { 
        background: #123457; color: #fff; cursor: pointer; 
    }
    .btn-user-del-final.active:hover {
        background: #E8A33D; color: #0A1E33; box-shadow: 0 4px 12px rgba(232, 163, 61, 0.3); 
    }

    .btn-user-del-cancel {
        background: transparent; color: #64748B; border: none; padding: 5px;
        font-size: 11px; font-weight: 700; cursor: pointer; text-decoration: underline; text-align: center;
    }
</style>
@endonce

<div id="deleteUserModal" class="del-user-overlay" onclick="if(event.target === this) window.closeDeleteUserModal()">
    <div class="del-user-card">
        <div class="del-user-header">
            <div class="icon-box">
                <i class="bi bi-person-x-fill"></i>
            </div>
            <div>
                <h2>Hapus Akun Warga</h2>
            </div>
        </div>

        <form id="formDeleteUserAction" method="POST" class="del-user-body">
            @csrf
            @method('DELETE')

            <div class="del-user-target-chip">
                <div id="delUserAvatar" class="del-user-target-avatar"></div>
                <div class="min-w-0">
                    <p id="delUserNameText" class="text-xs font-black text-primary-dark truncate">-</p>
                    <p id="delUserIdText" class="text-[10px] font-bold text-slate-400 truncate">ID: -</p>
                </div>
            </div>

            <!-- STEP 1: WARNING -->
            <div id="user_del_warning_step">
                <div class="del-user-warning-box">
                    <p>Peringatan: Tindakan ini bersifat permanen!</p>
                    <ul class="del-user-danger-list">
                        <li>Seluruh profil warga akan dihapus selamanya.</li>
                        <li>Riwayat pengajuan berkas akan dimusnahkan.</li>
                        <li>Warga tidak akan bisa login kembali.</li>
                    </ul>
                </div>
            </div>

            <!-- STEP 2: VERIFICATION -->
            <div id="user_del_verification_step">
                <p class="del-user-instruction">
                    Ketik nama warga <b id="del_user_name_hint">-</b> untuk konfirmasi.
                </p>
                <input type="text" id="input_verify_user_name" class="del-user-input-verify" placeholder="Ketik nama lengkap di sini..." autocomplete="off">
            </div>
        </form>

        <div class="del-user-footer">
            <button type="button" id="btn_user_del_next" class="btn-user-del-step-1" onclick="window.goToVerifyStepUserDel()">
                Saya mengerti, Lanjutkan
            </button>

            <button type="submit" form="formDeleteUserAction" id="btn_user_del_final" class="btn-user-del-final">
                Hapus Akun Permanen
            </button>

            <button type="button" class="btn-user-del-cancel" onclick="window.closeDeleteUserModal()">
                Batalkan & Kembali
            </button>
        </div>
    </div>
</div>

<script>
    if (typeof window.deleteUserModalInitialized === 'undefined') {
        window.deleteUserModalInitialized = true;
        let targetNameForDelete = "";

        window.openDeleteUserModal = function(user, actionUrl) {
            const modal = document.getElementById('deleteUserModal');
            targetNameForDelete = user.name;

            // Set Data
            document.getElementById('formDeleteUserAction').action = actionUrl;
            document.getElementById('delUserNameText').innerText = user.name;
            document.getElementById('delUserIdText').innerText = 'ID: SOA-' + (parseInt(user.id) + 1000);
            document.getElementById('del_user_name_hint').innerText = user.name;

            const avatarBox = document.getElementById('delUserAvatar');
            if(user.profile_photo) {
                avatarBox.innerHTML = `<img src="/storage/${user.profile_photo}" alt="">`;
            } else {
                avatarBox.innerHTML = user.name.substring(0, 2).toUpperCase();
            }

            // Reset UI
            document.getElementById('user_del_warning_step').style.display = 'block';
            document.getElementById('user_del_verification_step').style.display = 'none';
            document.getElementById('btn_user_del_next').style.display = 'block';
            document.getElementById('btn_user_del_final').style.display = 'none';
            document.getElementById('input_verify_user_name').value = "";

            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('is-active'), 10);
            document.body.style.overflow = 'hidden';
        }

        window.goToVerifyStepUserDel = function() {
            document.getElementById('user_del_warning_step').style.display = 'none';
            document.getElementById('btn_user_del_next').style.display = 'none';
            document.getElementById('user_del_verification_step').style.display = 'block';
            
            const btnFinal = document.getElementById('btn_user_del_final');
            btnFinal.style.display = 'block';
            
            const input = document.getElementById('input_verify_user_name');
            input.focus();

            input.oninput = function() {
                if (this.value === targetNameForDelete) {
                    btnFinal.classList.add('active');
                    btnFinal.disabled = false;
                } else {
                    btnFinal.classList.remove('active');
                    btnFinal.disabled = true;
                }
            };
        }

        window.closeDeleteUserModal = function() {
            const modal = document.getElementById('deleteUserModal');
            modal.classList.add('is-closing');
            setTimeout(() => {
                modal.classList.remove('is-active', 'is-closing');
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        }

        document.getElementById('formDeleteUserAction').onsubmit = function() {
            const btn = document.getElementById('btn_user_del_final');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Menghapus...';
        };
    }
</script>