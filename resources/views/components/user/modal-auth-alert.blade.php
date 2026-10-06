@once
<style>
    /* OVERLAY ANIMATION (IDENTIK DENGAN LOGOUT) */
    @keyframes authOverlayFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes authOverlayFadeOut { from { opacity: 1; } to { opacity: 0; } }

    /* CARD ANIMATION (IDENTIK DENGAN LOGOUT) */
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
        position: fixed; inset: 0;
        background: rgba(10, 30, 51, 0.8);
        backdrop-filter: blur(6px);
        display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 15px;
        opacity: 0;
    }

    /* State Aktif */
    .auth-alert-overlay.is-active {
        display: flex;
        animation: authOverlayFadeIn 0.3s ease forwards;
    }
    .auth-alert-overlay.is-active .auth-alert-card {
        animation: authSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    /* State Menutup */
    .auth-alert-overlay.is-closing {
        animation: authOverlayFadeOut 0.3s ease forwards;
    }
    .auth-alert-overlay.is-closing .auth-alert-card {
        animation: authSpringOut 0.3s ease forwards;
    }

    /* DESAIN KARTU (IDENTIK DENGAN LOGOUT) */
    .auth-alert-card {
        background: #ffffff; width: 100%; max-width: 340px; 
        border-radius: 22px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    /* HEADER: NAVY & GOLD BORDER (IDENTIK) */
    .auth-alert-header { 
        background: #0A1E33; padding: 22px 18px; 
        border-bottom: 3px solid #E8A33D; 
        text-align: center; 
    }

    /* ICON BOX (IDENTIK) */
    .auth-alert-icon-box {
        width: 48px; height: 48px; background: rgba(232, 163, 61, 0.1);
        border-radius: 14px; display: flex; align-items: center; justify-content: center;
        margin: 0 auto 12px; color: #E8A33D; font-size: 20px; 
        border: 1px solid rgba(232, 163, 61, 0.2);
    }

    .auth-alert-header h2 { color: #ffffff; font-size: 13px; font-weight: 800; margin: 0; letter-spacing: 1px; text-transform: uppercase; }

    /* BODY (IDENTIK) */
    .auth-alert-body { padding: 25px 20px; text-align: center; }
    .auth-alert-body p { font-size: 12px; color: #64748b; line-height: 1.6; font-weight: 500; margin: 0; }

    /* CONTEXT BOX (PENYESUAIAN DARI TARGET BOX) */
    .auth-alert-context-box { 
        margin-top: 15px; padding: 12px; background: #F8FAFC; 
        border: 1px solid #E2E8F0; border-radius: 12px; 
    }
    .auth-alert-context-label { 
        display: block; font-size: 8px; font-weight: 800; 
        color: #94a3b8; text-transform: uppercase; 
        letter-spacing: 1px; margin-bottom: 4px; 
    }
    .auth-alert-context-text { font-size: 11px; font-weight: 700; color: #123457; line-height: 1.4; }

    /* FOOTER GRID 2 COLUMN (IDENTIK) */
    .auth-alert-footer { 
        padding: 15px 20px; background: #F8FAFC; 
        border-top: 1px solid #e2e8f0; 
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px; 
    }

    /* BUTTONS (IDENTIK) */
    .btn-auth-confirm{
        background:#123457;
        color:#fff;
        border:none;
        padding:12px;
        border-radius:10px;
        font-size:10px;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.5px;
        transition:all .25s ease;
        cursor:pointer;
        text-align:center;
        text-decoration:none;
    }

    .btn-auth-confirm:hover{
        background:#E8A33D;
        color:#0A1E33;
        transform:translateY(-2px);
        box-shadow:0 10px 20px rgba(232,163,61,.35);
    }

    .btn-auth-confirm:active{
        transform:scale(.96);
    }

    /* BUTTON KEMBALI */
    .btn-auth-cancel{
        background:#e2e8f0;
        color:#475569;
        border:1px solid #cbd5e1;
        padding:12px;
        border-radius:10px;
        font-size:10px;
        font-weight:800;
        text-transform:uppercase;
        transition:all .25s ease;
        cursor:pointer;
    }

    .btn-auth-cancel:hover{
        background:#cbd5e1;
        transform:translateY(-2px);
        box-shadow:0 8px 18px rgba(148,163,184,.25);
    }

    .btn-auth-cancel:active{
        transform:scale(.96);
    }

    .auth-alert-card{
        background:#fff;
        width:100%;
        max-width:340px;
        border-radius:22px;
        overflow:hidden;
        box-shadow:0 25px 50px -12px rgba(0,0,0,.5);
        transition:transform .3s ease, box-shadow .3s ease;
    }

    .auth-alert-card:hover{
        transform:translateY(-2px);
        box-shadow:0 30px 60px rgba(0,0,0,.45);
    }
</style>
@endonce

<div id="modalAuthAlert" class="auth-alert-overlay" onclick="if(event.target === this) closeAuthAlertModal()">
    <div class="auth-alert-card">
        <div class="auth-alert-header">
            <div class="auth-alert-icon-box"><i class="bi bi-shield-lock-fill"></i></div>
            <h2>Akses Terbatas</h2>
        </div>
        <div class="auth-alert-body">
            <p>Maaf, fitur pengajuan layanan mandiri memerlukan identitas yang terverifikasi.</p>
            <div class="auth-alert-context-box">
                <span class="auth-alert-context-label">Instruksi Sistem:</span>
                <span class="auth-alert-context-text">Silakan masuk ke akun Anda atau daftar terlebih dahulu untuk melanjutkan.</span>
            </div>
        </div>
        
        <div class="auth-alert-footer">
            <button type="button" class="btn-auth-cancel" onclick="closeAuthAlertModal()">KEMBALI</button>
            <a href="{{ route('login') }}" class="btn-auth-confirm">LOGIN</a>
        </div>
    </div>
</div>

@once
<script>
    function openAuthAlertModal() {
        const modal = document.getElementById('modalAuthAlert');
        if(modal) {
            modal.classList.remove('is-closing');
            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden'; // Kunci scroll
        }
    }

    function closeAuthAlertModal() {
        const modal = document.getElementById('modalAuthAlert');
        if(modal) {
            modal.classList.add('is-closing');
            
            setTimeout(() => {
                modal.classList.remove('is-active');
                modal.classList.remove('is-closing');
                document.body.style.overflow = 'auto'; // Lepas scroll
            }, 300); 
        }
    }
</script>
@endonce