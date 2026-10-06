{{-- resources/views/components/admin/modal-reject-submission.blade.php --}}
@props(['portalCode'])

@once
<style>
    /* 1. ANIMATION KEYFRAMES */
    @keyframes rejOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes rejOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes rejSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.01) translateY(-4px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes rejSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.96) translateY(10px); }
    }

    /* 2. OVERLAY */
    .rej-overlay { 
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85); 
        backdrop-filter: blur(6px); 
        display: none; 
        align-items: center; justify-content: center; 
        z-index: 12000; padding: 20px; opacity: 0; 
    }

    .rej-overlay.is-active { 
        display: flex; 
        opacity: 1; 
        animation: rejOverlayIn 0.3s ease forwards; 
    }
    .rej-overlay.is-active .rej-card { 
        animation: rejSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; 
    }

    .rej-overlay.is-closing { 
        animation: rejOverlayOut 0.3s ease forwards; 
    }
    .rej-overlay.is-closing .rej-card { 
        animation: rejSpringOut 0.3s ease forwards; 
    }
    
    /* 3. CARD DESIGN */
    .rej-card { 
        background: #fff; width: 100%; max-width: 380px; max-height: 80vh; border-radius: 24px; 
        overflow: hidden; box-shadow: 0 30px 60px -12px rgba(0,0,0,0.4); 
        display: flex; flex-direction: column;
    }

    /* 4. HEADER (Navy & Rose) */
    .rej-header { 
        background: #0A1E33; padding: 20px 24px; border-bottom: 4px solid #F43F5E; 
        display: flex; align-items: center; justify-content: space-between; gap: 12px; color: #fff; position: relative;
    }
    .rej-header-left { display: flex; align-items: center; gap: 16px; min-width: 0; }
    .rej-header h2 { font-size: 15px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .rej-header p { font-size: 10px; color: rgba(255,255,255,0.5); margin: 2px 0 0; font-family: 'JetBrains Mono', monospace; }
    
    .rej-icon-box { 
        width: 42px; height: 42px; border-radius: 12px; display: flex; 
        align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;
        background: rgba(244, 63, 94, 0.15); color: #F43F5E;
    }
    .rej-close-x { 
        width: 30px; height: 30px; border-radius: 9px;
        background: rgba(255,255,255,0.06); border: none; color: #94a3b8;
        display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; 
    }
    .rej-close-x:hover { background: rgba(244,63,94,0.2); color: #F43F5E; }

    /* 5. BODY & SECTION */
    .rej-body-scroll { overflow-y: auto; flex: 1; max-height: 70vh; }
    .rej-section { padding: 20px 24px; }
    .rej-section + .rej-section { border-top: 1px solid #F1F5F9; }

    .rej-section-heading { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .rej-section-number {
        width: 22px; height: 22px; border-radius: 7px;
        background: #123457; color: #E8A33D;
        font-size: 10px; font-weight: 900;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .rej-section-heading h3 { font-size: 12px; font-weight: 800; color: #123457; margin: 0; text-transform: uppercase; }

    .rej-warning-box { 
        background: #FFF1F2; border: 1px solid #FFE4E6; padding: 12px 16px; 
        border-radius: 14px; display: flex; gap: 12px;
    }
    .rej-warning-box i { color: #E11D48; font-size: 1.1rem; flex-shrink: 0; }
    .rej-warning-box p { font-size: 11px; color: #9F1239; margin: 0; line-height: 1.5; font-weight: 600; }

    .rej-label { 
        display: block; font-size: 9px; font-weight: 800; color: #64748b; 
        text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; margin-left: 4px;
    }
    #catatan_tolak { 
        width: 100%; padding: 14px; border: 1.5px solid #E2E8F0; border-radius: 16px; 
        font-size: 13px; font-weight: 600; color: #123457; outline: none; 
        transition: all 0.3s; font-family: inherit; background: #F8FAFC; resize: none;
    }
    #catatan_tolak:focus { border-color: #F43F5E; background: #fff; box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.08); }

    /* 6. FOOTER */
    .rej-footer { padding: 16px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; gap: 10px; }
    .btn-rej-cancel { flex: 1; padding: 12px; border-radius: 12px; border: 1px solid #E2E8F0; background: #fff; color: #475569; font-weight: 800; font-size: 11px; cursor: pointer; text-transform: uppercase; }
    .btn-rej-cancel:hover { background: #F1F5F9; color: #1e293b; }
    
    .btn-rej-submit { 
        flex: 2; padding: 12px; border-radius: 12px; border: none; 
        background: #123457; color: #fff; font-weight: 800; font-size: 11px; 
        cursor: pointer; transition: 0.3s; text-transform: uppercase;
    }
    .btn-rej-submit:hover { background: #E11D48; box-shadow: 0 8px 16px rgba(225, 29, 72, 0.25); }
</style>
@endonce

<div id="modalRejectSub" class="rej-overlay" onclick="if(event.target === this) window.closeRejectModal()">
    <div class="rej-card">
        <div class="rej-header">
            <div class="rej-header-left">
                <div class="rej-icon-box"><i class="bi bi-x-circle-fill"></i></div>
                <div class="min-w-0">
                    <h2>Tolak Pengajuan</h2>
                    <p id="rej-nomor-text">#SOA-2024...</p>
                </div>
            </div>
            <button type="button" class="rej-close-x" onclick="window.closeRejectModal()"><i class="bi bi-x-lg text-xs"></i></button>
        </div>

        <form id="formReject" method="POST" class="rej-body-scroll">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="ditolak">
            
            <div class="rej-section">
                <div class="rej-section-heading">
                    <span class="rej-section-number">1</span>
                    <h3>Konfirmasi Penolakan</h3>
                </div>
                <div class="rej-warning-box">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <p>Tindakan ini bersifat <strong>Permanen</strong>. Berkas akan ditangguhkan dan pemohon harus mengajukan ulang jika ingin memproses kembali.</p>
                </div>
            </div>

            <div class="rej-section">
                <div class="rej-section-heading">
                    <span class="rej-section-number">2</span>
                    <h3>Alasan Penolakan</h3>
                </div>
                <div class="rej-field">
                    <label for="catatan_tolak" class="rej-label">Catatan Untuk Warga</label>
                    <textarea name="catatan_admin" id="catatan_tolak" rows="4" placeholder="Contoh: Mohon maaf, dokumen yang Anda lampirkan tidak valid atau telah kedaluwarsa..." required></textarea>
                </div>
            </div>
        </form>

        <div class="rej-footer">
            <button type="button" class="btn-rej-cancel" onclick="window.closeRejectModal()">Batal</button>
            <button type="submit" form="formReject" class="btn-rej-submit" id="btnConfirmReject">Tolak Permanen</button>
        </div>
    </div>
</div>

<script>
    if (typeof window.rejModalInitialized === 'undefined') {
        window.rejModalInitialized = true;

        window.openRejectModal = function(id, nomor) {
            const modal = document.getElementById('modalRejectSub');
            const form = document.getElementById('formReject');
            const portal = '{{ $portalCode }}';
            
            document.getElementById('rej-nomor-text').innerText = nomor;
            form.action = `/${portal}/submissions/${id}/status`;
            
            // Trigger Animasi Masuk
            modal.style.display = 'flex';
            modal.classList.remove('is-closing');
            setTimeout(() => {
                modal.classList.add('is-active');
            }, 10);

            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('catatan_tolak').focus(), 400);
        }

        window.closeRejectModal = function() {
            const modal = document.getElementById('modalRejectSub');
            if(modal) {
                modal.classList.add('is-closing');
                setTimeout(() => {
                    modal.classList.remove('is-active', 'is-closing');
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                    document.getElementById('formReject').reset();
                }, 300);
            }
        }

        document.getElementById('formReject').addEventListener('submit', function() {
            const btn = document.getElementById('btnConfirmReject');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> MEMPROSES...';
        });
    }
</script>