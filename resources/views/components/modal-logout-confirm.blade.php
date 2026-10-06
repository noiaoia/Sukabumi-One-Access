@auth
@once
<style>
    /* OVERLAY ANIMATION (IDENTIK) */
    @keyframes logoutOverlayFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes logoutOverlayFadeOut { from { opacity: 1; } to { opacity: 0; } }

    /* CARD ANIMATION (IDENTIK) */
    @keyframes logoutSpringIn {
        0% { opacity: 0; transform: scale(0.9) translateY(30px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes logoutSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.9) translateY(20px); }
    }

    .logout-overlay {
        position: fixed; inset: 0;
        background: rgba(10, 30, 51, 0.8);
        backdrop-filter: blur(6px);
        display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 15px;
        opacity: 0;
    }

    /* State Aktif */
    .logout-overlay.is-active {
        display: flex;
        animation: logoutOverlayFadeIn 0.3s ease forwards;
    }
    .logout-overlay.is-active .logout-card {
        animation: logoutSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    /* State Menutup */
    .logout-overlay.is-closing {
        animation: logoutOverlayFadeOut 0.3s ease forwards;
    }
    .logout-overlay.is-closing .logout-card {
        animation: logoutSpringOut 0.3s ease forwards;
    }

    /* DESAIN KARTU (IDENTIK) */
    .logout-card {
        background: #ffffff; width: 100%; max-width: 340px; 
        border-radius: 22px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    /* HEADER: NAVY & GOLD BORDER */
    .logout-header { 
        background: #0A1E33; padding: 18px; 
        border-bottom: 3px solid #E8A33D; 
        text-align: center; 
    }

    /* ICON BOX (IDENTIK) */
    .logout-icon-box {
        width: 48px; height: 48px; background: rgba(232, 163, 61, 0.1);
        border-radius: 14px; display: flex; align-items: center; justify-content: center;
        margin: 0 auto 12px; color: #E8A33D; font-size: 20px; 
        border: 1px solid rgba(232, 163, 61, 0.2);
    }

    .logout-header h2 { color: #ffffff; font-size: 13px; font-weight: 800; margin: 0; letter-spacing: 1px; text-transform: uppercase; }

    /* BODY (IDENTIK) */
    .logout-body { padding: 25px 20px; text-align: center; }
    .logout-body p { font-size: 12px; color: #64748b; line-height: 1.6; font-weight: 500; margin: 0; }

    /* TARGET BOX (IDENTIK) */
    .logout-target-box { 
        margin-top: 15px; padding: 10px; background: #F8FAFC; 
        border: 1px solid #E2E8F0; border-radius: 12px; 
    }
    .logout-target-label { 
        display: block; font-size: 8px; font-weight: 800; 
        color: #94a3b8; text-transform: uppercase; 
        letter-spacing: 0.5px; margin-bottom: 2px; 
    }
    .logout-target-name { font-size: 13px; font-weight: 800; color: #123457; }

    /* FOOTER GRID 2 COLUMN (IDENTIK) */
    .logout-footer { 
        padding: 15px 20px; background: #F8FAFC; 
        border-top: 1px solid #e2e8f0; 
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px; 
    }

    /* BUTTONS (IDENTIK) */
    .btn-logout-confirm{
        background:#123457;
        color:#fff;
        border:none;
        padding:11px;
        border-radius:10px;
        font-size:10px;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.5px;
        transition:all .25s ease;
        cursor:pointer;
    }

    .btn-logout-confirm:hover{
        background:#E8A33D;
        color:#0A1E33;
        transform:translateY(-2px);
        box-shadow:0 10px 20px rgba(232,163,61,.35);
    }

    .btn-logout-confirm:active{
        transform:scale(.96);
    }

    .btn-logout-cancel {
        background: #e2e8f0;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 11px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        transition: all .25s ease;
        cursor: pointer;
    }

    .btn-logout-cancel:hover{
        background:#cbd5e1;
        transform:translateY(-2px);
        box-shadow:0 8px 18px rgba(148,163,184,.25);
    }

    .btn-logout-cancel:active{
        transform:scale(.96);
    }

    .logout-card{
        transition:transform .3s ease, box-shadow .3s ease;
    }

    .logout-card:hover{
        transform:translateY(-2px);
        box-shadow:0 28px 55px rgba(0,0,0,.45);
    }

</style>
@endonce

<div id="logoutConfirmModal" class="logout-overlay" onclick="if(event.target === this) closeLogoutModal()">
    <div class="logout-card">
        <div class="logout-header">
            <div class="logout-icon-box"><i class="bi bi-box-arrow-right"></i></div>
            <h2>Konfirmasi Sesi</h2>
        </div>
        <div class="logout-body">
            <p>Apakah Anda yakin ingin mengakhiri sesi administrasi ini?</p>
            <div class="logout-target-box">
                <span class="logout-target-label">Akun Sedang Aktif:</span>
                <span class="logout-target-name">{{ Auth::user()->name }}</span>
            </div>
        </div>
        
        <form id="logout-form-modal" action="{{ route('logout') }}" method="POST">
            @csrf
            <div class="logout-footer">
                <button type="button" class="btn-logout-cancel" onclick="closeLogoutModal()">BATAL</button>
                <button type="submit" class="btn-logout-confirm">YA, KELUAR</button>
            </div>
        </form>
    </div>
</div>

@once
<script>
    function openLogoutModal() {
        const modal = document.getElementById('logoutConfirmModal');
        if(modal) {
            modal.classList.remove('is-closing');
            modal.classList.add('is-active');
        }
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logoutConfirmModal');
        if(modal) {
            modal.classList.add('is-closing');
            
            // Tunggu animasi SpringOut selesai (300ms)
            setTimeout(() => {
                modal.classList.remove('is-active');
                modal.classList.remove('is-closing');
                document.body.style.overflow = 'auto';
            }, 300); 
        }
    }
</script>
@endonce
@endauth