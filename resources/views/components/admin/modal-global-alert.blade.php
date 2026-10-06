@once
<style>
    /* ANIMASI & OVERLAY (Tetap sama seperti referensi Anda) */
    @keyframes authOverlayFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes authOverlayFadeOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes authSpringIn {
        0% { opacity: 0; transform: scale(0.9) translateY(30px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes authSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.9) translateY(20px); }
    }

    .auth-alert-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.8);
        backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 15px; opacity: 0;
    }
    .auth-alert-overlay.is-active { display: flex; animation: authOverlayFadeIn 0.3s ease forwards; }
    .auth-alert-overlay.is-active .auth-alert-card { animation: authSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .auth-alert-overlay.is-closing { animation: authOverlayFadeOut 0.3s ease forwards; }
    .auth-alert-overlay.is-closing .auth-alert-card { animation: authSpringOut 0.3s ease forwards; }

    .auth-alert-card {
        background: #ffffff; width: 100%; max-width: 340px; 
        border-radius: 22px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }
    .auth-alert-header { background: #0A1E33; padding: 22px 18px; border-bottom: 3px solid #E8A33D; text-align: center; }
    .auth-alert-icon-box {
        width: 48px; height: 48px; background: rgba(232, 163, 61, 0.1);
        border-radius: 14px; display: flex; align-items: center; justify-content: center;
        margin: 0 auto 12px; color: #E8A33D; font-size: 20px; border: 1px solid rgba(232, 163, 61, 0.2);
    }
    .auth-alert-header h2 { color: #ffffff; font-size: 13px; font-weight: 800; margin: 0; letter-spacing: 1px; text-transform: uppercase; }
    .auth-alert-body { padding: 25px 20px; text-align: center; }
    .auth-alert-body p { font-size: 11px; color: #64748b; line-height: 1.6; font-weight: 600; margin: 0; }
    .auth-alert-context-box { margin-top: 15px; padding: 12px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; }
    .auth-alert-context-label { display: block; font-size: 8px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .auth-alert-context-text { font-size: 10px; font-weight: 700; color: #123457; line-height: 1.4; }
    .auth-alert-footer { padding: 15px 20px; background: #F8FAFC; border-top: 1px solid #e2e8f0; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

    .btn-auth-confirm { background:#123457; color:#fff; border:none; padding:12px; border-radius:10px; font-size:10px; font-weight:800; text-transform:uppercase; transition:all .25s ease; cursor:pointer; text-align:center; text-decoration:none; }
    .btn-auth-confirm:hover { background:#E8A33D; color:#0A1E33; transform:translateY(-2px); box-shadow:0 10px 20px rgba(232,163,61,.35); }
    .btn-auth-cancel { background:#e2e8f0; color:#475569; border:1px solid #cbd5e1; padding:12px; border-radius:10px; font-size:10px; font-weight:800; text-transform:uppercase; transition:all .25s ease; cursor:pointer; }
    .btn-auth-cancel:hover { background:#cbd5e1; transform:translateY(-2px); }
</style>
@endonce

<div id="modalGlobalAlert" class="auth-alert-overlay" onclick="if(event.target === this) closeGlobalAlert()">
    <div class="auth-alert-card">
        <div class="auth-alert-header">
            <div class="auth-alert-icon-box"><i id="alertIcon" class="bi bi-shield-lock-fill"></i></div>
            <h2 id="alertTitle">Peringatan</h2>
        </div>
        <div class="auth-alert-body">
            <p id="alertDescription">Deskripsi pesan...</p>
            <div class="auth-alert-context-box">
                <span class="auth-alert-context-label">Instruksi Sistem:</span>
                <span id="alertInstruction" class="auth-alert-context-text">Lakukan tindakan berikut.</span>
            </div>
        </div>
        
        <div class="auth-alert-footer">
            <button type="button" class="btn-auth-cancel" onclick="closeGlobalAlert()">KEMBALI</button>
            <a href="#" id="alertConfirmBtn" class="btn-auth-confirm">PROSES</a>
        </div>
    </div>
</div>

@once
<script>
    /**
     * @param options Object { title, message, instruction, icon, confirmText, confirmUrl }
     */
    function openGlobalAlert(options = {}) {
        const modal = document.getElementById('modalGlobalAlert');
        
        // Isi konten secara dinamis
        document.getElementById('alertTitle').innerText = options.title || 'Akses Terbatas';
        document.getElementById('alertDescription').innerText = options.message || 'Maaf, Anda tidak memiliki akses.';
        document.getElementById('alertInstruction').innerText = options.instruction || 'Silakan login untuk melanjutkan.';
        document.getElementById('alertConfirmBtn').innerText = options.confirmText || 'LOGIN';
        document.getElementById('alertConfirmBtn').href = options.confirmUrl || '/login';
        
        // Ubah ikon jika ada
        if (options.icon) {
            document.getElementById('alertIcon').className = `bi ${options.icon}`;
        }

        if(modal) {
            modal.classList.remove('is-closing');
            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeGlobalAlert() {
        const modal = document.getElementById('modalGlobalAlert');
        if(modal) {
            modal.classList.add('is-closing');
            setTimeout(() => {
                modal.classList.remove('is-active');
                modal.classList.remove('is-closing');
                document.body.style.overflow = 'auto';
            }, 300); 
        }
    }
</script>
@endonce