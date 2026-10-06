{{-- resources/views/components/modal-confirm-password.blade.php --}}
<div id="modalPasswordConfirm" class="mpc-modal-overlay">
    <div class="mpc-modal-container">

        <div class="mpc-mobile-handle"></div>

        {{-- VIEW 1: KONFIRMASI AWAL --}}
        <div id="view_main_pw" class="mpc-content-view">
            <div class="mpc-header">
                <div class="mpc-icon-badge">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <span class="mpc-tag">Keamanan Akun</span>
                <h3 class="mpc-title">Perbarui Password?</h3>
                <p class="mpc-description">Konfirmasi perubahan kata sandi. Pastikan Anda mengingat password baru Anda.</p>
            </div>

            @auth
            <div class="mpc-user-card">
                <div class="mpc-user-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="mpc-user-info">
                    <p class="mpc-user-label">Konfirmasi Akun</p>
                    <h4 class="mpc-user-name">{{ Auth::user()->name }}</h4>
                    <p class="mpc-user-sub">@ {{ Auth::user()->username }}</p>
                </div>
                <div class="mpc-status-pill">
                    <span class="mpc-dot-blink"></span> Aktif
                </div>
            </div>
            @endauth

            <div class="mpc-warning-box">
                <i class="bi bi-info-circle"></i>
                <span>Sesi Anda akan tetap aktif setelah perubahan password.</span>
            </div>
        </div>

        {{-- VIEW 2: LOADER & HASIL --}}
        <div id="view_loader_pw" class="mpc-loader-view">
            <div class="mpc-visual-wrapper">
                <div class="mpc-ring-container">
                    <svg width="88" height="88" viewBox="0 0 88 88" class="mpc-svg-ring">
                        <circle cx="44" cy="44" r="39" fill="none" stroke="#f1f5f9" stroke-width="6"/>
                        <circle id="ring_path_pw" cx="44" cy="44" r="39" fill="none"
                            stroke="#123457" stroke-width="6" stroke-linecap="round"
                            stroke-dasharray="245.04" stroke-dashoffset="245.04"/>
                    </svg>

                    <div id="icon_check_pw" class="mpc-status-icon success"><i class="bi bi-check-lg"></i></div>
                    <div id="icon_error_pw" class="mpc-status-icon error"><i class="bi bi-x-lg"></i></div>
                </div>
            </div>

            <div class="mpc-status-text">
                <h4 id="status_title_pw" class="mpc-result-title">Memproses...</h4>
                <p id="status_sub_pw" class="mpc-result-sub">Menyinkronkan dengan database</p>
            </div>

            <div id="step_dots_pw" class="mpc-pulse-dots">
                <span></span><span></span><span></span>
            </div>
        </div>

        {{-- FOOTER --}}
        <div id="mpc_footer" class="mpc-footer">
            <button type="button" class="mpc-btn-secondary" onclick="closeConfirmPasswordModal()">Batalkan</button>
            <button type="button" class="mpc-btn-primary" id="btn_execute_pw" onclick="executePasswordUpdate()">
                Konfirmasi <i class="bi bi-arrow-right-short"></i>
            </button>
        </div>
    </div>
</div>

<style>
    :root {
        --mpc-primary: #123457;
        --mpc-primary-dark: #0A1E33;
        --mpc-accent: #E8A33D;
        --mpc-accent-dark: #3A2205;
    }

    .mpc-modal-overlay {
        position: fixed; inset: 0; z-index: 9999;
        background: rgba(10,30,51,0.4);
        backdrop-filter: blur(8px);
        display: none; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.3s ease;
        padding: 1rem;
    }
    .mpc-modal-overlay.active { display: flex; opacity: 1; }

    .mpc-modal-container {
        background: #fff;
        width: 100%;
        max-width: 340px;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 45px -12px rgba(10,30,51,0.25);
        transform: translateY(16px) scale(0.97);
        transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.1), opacity 0.3s ease;
        border-top: 3px solid var(--mpc-accent);
    }
    .mpc-modal-overlay.active .mpc-modal-container { transform: translateY(0) scale(1); }

    .mpc-header { text-align: center; padding: 26px 24px 14px; }

    .mpc-icon-badge {
        width: 46px; height: 46px;
        background: #f8fafc;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 10px;
        font-size: 19px;
        color: var(--mpc-primary);
        box-shadow: 0 6px 14px rgba(18,52,87,0.06);
    }

    .mpc-tag {
        display: inline-block;
        padding: 2px 9px;
        background: rgba(232,163,61,0.1);
        color: var(--mpc-accent);
        border-radius: 100px;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 6px;
    }

    .mpc-title { font-size: 17px; font-weight: 800; color: var(--mpc-primary); margin: 0 0 4px; }
    .mpc-description { font-size: 12px; color: #64748b; line-height: 1.55; margin: 0; }

    .mpc-user-card {
        margin: 0 20px 12px;
        background: var(--mpc-primary);
        border-radius: 16px;
        padding: 12px 14px;
        display: flex; align-items: center; gap: 10px;
    }
    .mpc-user-avatar { font-size: 24px; color: rgba(255,255,255,0.22); flex-shrink: 0; }
    .mpc-user-info { flex: 1; min-width: 0; }
    .mpc-user-label { font-size: 8px; color: var(--mpc-accent); font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; margin: 0; }
    .mpc-user-name { color: #fff; font-size: 13px; font-weight: 700; margin: 1px 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mpc-user-sub { color: rgba(255,255,255,0.4); font-size: 10.5px; margin: 0; }

    .mpc-status-pill {
        background: rgba(16,185,129,0.15);
        color: #10b981;
        padding: 3px 8px;
        border-radius: 100px;
        font-size: 9px;
        font-weight: 700;
        display: flex; align-items: center; gap: 4px;
        flex-shrink: 0;
    }

    .mpc-warning-box {
        margin: 0 20px 20px;
        padding: 10px 12px;
        background: #fffcf0;
        border: 1px solid #feebc8;
        border-radius: 10px;
        display: flex; align-items: flex-start; gap: 7px;
        font-size: 10.5px; color: #c05621; line-height: 1.5;
    }
    .mpc-warning-box i { margin-top: 1px; flex-shrink: 0; }

    .mpc-loader-view {
        display: none;
        padding: 34px 24px;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .mpc-ring-container { position: relative; width: 88px; height: 88px; margin-bottom: 16px; }
    .mpc-svg-ring { transform: rotate(-90deg); }
    #ring_path_pw {
        transition: stroke-dashoffset 1.6s cubic-bezier(0.65, 0, 0.35, 1), stroke 0.3s ease;
    }
    .mpc-status-icon {
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; opacity: 0; transform: scale(0.5);
        transition: 0.35s ease;
    }
    .mpc-status-icon.success { color: var(--mpc-primary); }
    .mpc-status-icon.error { color: #ef4444; }

    .mpc-result-title { font-size: 15.5px; font-weight: 800; color: var(--mpc-primary); margin: 0 0 3px; }
    .mpc-result-sub { font-size: 12px; color: #64748b; margin: 0; }

    .mpc-footer { padding: 0 20px 24px; display: flex; gap: 8px; }
    .mpc-btn-secondary {
        flex: 1; height: 42px; border-radius: 12px; border: none;
        background: #f1f5f9; color: #64748b; font-weight: 700; font-size: 13px;
        cursor: pointer; transition: background 0.2s ease;
    }
    .mpc-btn-secondary:hover { background: #e7ecf1; }
    .mpc-btn-primary {
        flex: 1.5; height: 42px; border-radius: 12px; border: none;
        background: var(--mpc-primary); color: #fff; font-weight: 700; font-size: 13px;
        cursor: pointer; box-shadow: 0 8px 16px rgba(18,52,87,0.18);
        transition: background 0.2s ease;
    }
    .mpc-btn-primary:hover { background: var(--mpc-primary-dark); }

    .mpc-pulse-dots { display: flex; gap: 4px; margin-top: 12px; }
    .mpc-pulse-dots span { width: 5px; height: 5px; background: var(--mpc-accent); border-radius: 50%; animation: mpcPulse 1s infinite; }
    .mpc-pulse-dots span:nth-child(2) { animation-delay: 0.2s; }
    .mpc-pulse-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes mpcPulse { 0%, 100% { opacity: 0.3; transform: scale(0.8); } 50% { opacity: 1; transform: scale(1.15); } }

    .mpc-dot-blink { width: 5px; height: 5px; background: #10b981; border-radius: 50%; animation: mpcBlink 1.5s infinite; }
    @keyframes mpcBlink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

    .mpc-shake { animation: mpcShake 0.4s ease-in-out; }
    @keyframes mpcShake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }

    #ring_path_pw.error { stroke: #ef4444 !important; }

    .mpc-mobile-handle { display: none; }

    @media (max-width: 480px) {
        .mpc-modal-overlay { align-items: flex-end; padding: 0; }
        .mpc-modal-container { border-radius: 22px 22px 0 0; max-width: 100%; }
        .mpc-mobile-handle { display: block; width: 36px; height: 4px; background: #e2e8f0; border-radius: 10px; margin: 12px auto 0; }
    }
</style>

<script>
    var mpcState = { duration: 2500, running: false, isDone: false };

    function openConfirmPasswordModal() {
        mpcState.running = false;
        mpcState.isDone = false;
        document.getElementById('view_loader_pw').style.display = 'none';
        document.getElementById('view_main_pw').style.display = 'block';
        document.getElementById('mpc_footer').style.display = 'flex';

        const ring = document.getElementById('ring_path_pw');
        ring.style.transition = 'none'; // reset instan, tanpa animasi, biar nggak kelihatan "mundur"
        ring.style.strokeDashoffset = '245.04';
        void ring.offsetWidth; // force reflow supaya transition berikutnya kepakai lagi
        ring.style.transition = '';
        ring.classList.remove('error');

        document.getElementById('icon_check_pw').style.opacity = '0';
        document.getElementById('icon_error_pw').style.opacity = '0';
        document.getElementById('status_title_pw').style.color = '#123457';
        document.getElementById('status_title_pw').textContent = 'Memproses...';
        const modal = document.getElementById('modalPasswordConfirm');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('active'), 10);
    }

    function closeConfirmPasswordModal() {
        if (mpcState.running && !mpcState.isDone) return;
        const modal = document.getElementById('modalPasswordConfirm');
        modal.classList.remove('active');
        setTimeout(() => window.location.reload(), 300);
    }

    async function executePasswordUpdate() {
        if (mpcState.running) return;
        mpcState.running = true;
        document.getElementById('view_main_pw').style.display = 'none';
        document.getElementById('mpc_footer').style.display = 'none';
        document.getElementById('view_loader_pw').style.display = 'flex';

        const ring = document.getElementById('ring_path_pw');
        // mulai dari kosong (sudah di-reset di openConfirmPasswordModal), lalu animasi mengisi
        // secara bertahap menuju hampir penuh selama proses berlangsung — bukan lompat ke satu angka.
        requestAnimationFrame(() => {
            ring.style.strokeDashoffset = '35';
        });

        const form = document.getElementById('formPasswordKeamanan');
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });
            const result = await response.json();
            setTimeout(() => {
                if (response.ok && result.success) { showSuccessState(); }
                else { showErrorState(result.message || 'Verifikasi Gagal'); }
            }, 1600);
        } catch (e) { showErrorState('Koneksi terputus'); }
    }

    function showSuccessState() {
        mpcState.isDone = true;
        document.getElementById('ring_path_pw').style.strokeDashoffset = '0';
        document.getElementById('icon_check_pw').style.opacity = '1';
        document.getElementById('icon_check_pw').style.transform = 'scale(1)';
        document.getElementById('status_title_pw').textContent = 'Berhasil!';
        document.getElementById('status_sub_pw').textContent = 'Password telah diperbarui.';
        setTimeout(() => window.location.reload(), 1300);
    }

    function showErrorState(msg) {
        mpcState.running = false;
        document.getElementById('ring_path_pw').classList.add('error');
        document.getElementById('icon_error_pw').style.opacity = '1';
        document.getElementById('icon_error_pw').style.transform = 'scale(1)';
        document.getElementById('status_title_pw').textContent = 'Gagal!';
        document.getElementById('status_title_pw').style.color = '#ef4444';
        document.getElementById('status_sub_pw').textContent = msg;
        document.querySelector('.mpc-modal-container').classList.add('mpc-shake');
        setTimeout(() => document.querySelector('.mpc-modal-container').classList.remove('mpc-shake'), 600);
        setTimeout(() => {
            document.getElementById('mpc_footer').style.display = 'flex';
            const btn = document.getElementById('btn_execute_pw');
            btn.textContent = 'Coba Lagi';
            btn.onclick = () => window.location.reload();
        }, 700);
    }
</script>