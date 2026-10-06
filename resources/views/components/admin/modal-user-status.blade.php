{{-- resources/views/components/admin/modal-user-status.blade.php --}}
@props(['portalCode'])

@once
<style>
    /* ANIMASI MODAL */
    @keyframes statusOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes statusOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes statusSpringIn {
        0% { opacity: 0; transform: scale(0.9) translateY(30px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes statusSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.9) translateY(20px); }
    }

    .status-modal-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85);
        backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 15px; opacity: 0;
    }
    .status-modal-overlay.is-active { display: flex; animation: statusOverlayIn 0.3s ease forwards; }
    .status-modal-overlay.is-active .status-modal-card { animation: statusSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .status-modal-overlay.is-closing { animation: statusOverlayOut 0.3s ease forwards; }
    .status-modal-overlay.is-closing .status-modal-card { animation: statusSpringOut 0.3s ease forwards; }

    .status-modal-card {
        background: #ffffff; width: 100%; max-width: 400px;
        border-radius: 24px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    /* HEADER THEMES */
    .status-modal-header { padding: 25px; display: flex; align-items: center; gap: 15px; color: #fff; }
    .status-modal-header.theme-suspend { background: #0A1E33; border-bottom: 4px solid #F43F5E; }
    .status-modal-header.theme-activate { background: #0A1E33; border-bottom: 4px solid #10B981; }

    .status-modal-icon-box { 
        width: 44px; height: 44px; border-radius: 12px; display: flex; 
        align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;
        background: rgba(255,255,255,0.15);
    }

    .status-modal-header h2 { font-size: 16px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-modal-header p { font-size: 10px; color: rgba(255,255,255,0.5); margin: 2px 0 0; font-weight: 700; }

    .status-modal-body { padding: 24px; }
    
    /* USER PREVIEW CHIP */
    .status-user-preview {
        display: flex; align-items: center; gap: 12px;
        padding: 12px; background: #F8FAFC; border: 1px solid #E2E8F0;
        border-radius: 16px; margin-bottom: 20px;
    }
    .status-user-preview img, .status-user-initial {
        width: 40px; height: 40px; border-radius: 10px; object-fit: cover;
        background: #123457; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800;
    }

    .status-warning-text { font-size: 13px; color: #64748b; line-height: 1.6; margin: 0; }
    .status-warning-text strong { color: #123457; }

    .status-modal-footer { padding: 16px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; gap: 12px; }
    
    .btn-status-cancel { flex: 1; padding: 12px; border-radius: 12px; border: 1px solid #E2E8F0; background: #fff; color: #475569; font-weight: 800; font-size: 11px; cursor: pointer; text-transform: uppercase; }
    .btn-status-submit { flex: 1.5; padding: 12px; border-radius: 12px; border: none; color: #fff; font-weight: 800; font-size: 11px; cursor: pointer; transition: 0.3s; text-transform: uppercase; }
    
    .btn-status-submit.bg-suspend { background: #123457; }
    .btn-status-submit.bg-suspend:hover { background: #F43F5E; box-shadow: 0 8px 20px rgba(244, 63, 94, 0.2); }
    
    .btn-status-submit.bg-activate { background: #123457; }
    .btn-status-submit.bg-activate:hover { background: #10B981; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.2); }
</style>
@endonce

<div id="modalUserStatus" class="status-modal-overlay" onclick="if(event.target === this) window.closeStatusModal()">
    <div class="status-modal-card">
        <div id="statusHeader" class="status-modal-header">
            <div class="status-modal-icon-box">
                <i id="statusIcon" class="bi"></i>
            </div>
            <div>
                <h2 id="statusTitle">Konfirmasi Status</h2>
                <p>Security Protocol SOA-{{ rand(1000, 9999) }}</p>
            </div>
        </div>

        <form id="formStatusAction" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="status-modal-body">
                <div class="status-user-preview">
                    <div id="statusUserAvatar"></div>
                    <div class="min-w-0">
                        <p id="statusUserName" class="text-xs font-black text-primary-dark truncate">-</p>
                        <p id="statusUserEmail" class="text-[10px] font-bold text-slate-400 truncate">-</p>
                    </div>
                </div>

                <p id="statusWarningMessage" class="status-warning-text"></p>
            </div>

            <div class="status-modal-footer">
                <button type="button" class="btn-status-cancel" onclick="window.closeStatusModal()">Batal</button>
                <button type="submit" id="btnStatusSubmit" class="btn-status-submit">Konfirmasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    if (typeof window.statusModalInitialized === 'undefined') {
        window.statusModalInitialized = true;

        window.openStatusModal = function(user, actionUrl) {
            const modal = document.getElementById('modalUserStatus');
            const isSuspending = user.status === 'active';
            
            // 1. Set Form Action
            document.getElementById('formStatusAction').action = actionUrl;

            // 2. Set UI Data User
            document.getElementById('statusUserName').innerText = user.name;
            document.getElementById('statusUserEmail').innerText = user.email;
            
            const avatarBox = document.getElementById('statusUserAvatar');
            if(user.profile_photo) {
                avatarBox.innerHTML = `<img src="/storage/${user.profile_photo}" alt="">`;
            } else {
                avatarBox.className = "status-user-initial";
                avatarBox.innerText = user.name.substring(0, 2).toUpperCase();
            }

            // 3. Set Theme & Text berdasarkan aksi
            const header = document.getElementById('statusHeader');
            const icon = document.getElementById('statusIcon');
            const title = document.getElementById('statusTitle');
            const msg = document.getElementById('statusWarningMessage');
            const btn = document.getElementById('btnStatusSubmit');

            if (isSuspending) {
                header.className = "status-modal-header theme-suspend";
                icon.className = "bi bi-slash-circle-fill";
                title.innerText = "Tangguhkan Akun";
                msg.innerHTML = `Apakah Anda yakin ingin <strong>Menonaktifkan</strong> akses warga ini? Pengguna tidak akan bisa masuk ke portal sampai diaktifkan kembali.`;
                btn.className = "btn-status-submit bg-suspend";
                btn.innerText = "Ya, Tangguhkan";
            } else {
                header.className = "status-modal-header theme-activate";
                icon.className = "bi bi-check-circle-fill";
                title.innerText = "Aktifkan Akun";
                msg.innerHTML = `Apakah Anda yakin ingin <strong>Memulihkan</strong> akses warga ini? Pengguna akan kembali memiliki otoritas penuh di portal.`;
                btn.className = "btn-status-submit bg-activate";
                btn.innerText = "Ya, Aktifkan";
            }

            // 4. Show Modal
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('is-active'), 10);
            document.body.style.overflow = 'hidden';
        }

        window.closeStatusModal = function() {
            const modal = document.getElementById('modalUserStatus');
            modal.classList.add('is-closing');
            setTimeout(() => {
                modal.classList.remove('is-active', 'is-closing');
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        }

        document.getElementById('formStatusAction').onsubmit = function() {
            const btn = document.getElementById('btnStatusSubmit');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses...';
        };
    }
</script>