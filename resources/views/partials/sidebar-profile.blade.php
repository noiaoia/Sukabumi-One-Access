{{-- resources/views/partials/sidebar-profile.blade.php --}}
<aside class="profile-sidebar reveal">

    {{-- Kartu identitas user singkat --}}
    <div class="sidebar-user-card flex items-center gap-3 px-4 mb-6 pb-6 border-b border-slate-100">
        <div class="sidebar-avatar shrink-0">
            @if(Auth::user()->profile_photo)
                <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Foto Profil" class="w-full h-full object-cover">
            @else
                <span>{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
            @endif
        </div>
        <div class="min-w-0">
            <p class="sidebar-user-name">{{ Auth::user()->name }}</p>
            <p class="sidebar-user-tag">
                <i class="bi bi-patch-check-fill"></i>
                <span>Kelola Akun</span>
            </p>
        </div>
    </div>

    <div class="sidebar-title-label px-4 mb-3">
        Menu Akun
    </div>

    <nav class="flex-1">
        {{-- Link Detail Profil --}}
        <a href="{{ route('user.profile') }}" class="sidebar-menu-link {{ Route::is('user.profile') ? 'active' : '' }}">
            <span class="sidebar-menu-icon"><i class="bi bi-person-fill"></i></span>
            <span class="sidebar-menu-text">Detail Profil</span>
            <i class="bi bi-chevron-right menu-chevron"></i>
        </a>

        {{-- Link Keamanan (Ganti rute jika sudah ada) --}}
        <a href="{{ route('user.security') }}" class="sidebar-menu-link {{ Route::is('user.security') ? 'active' : '' }}">
            <span class="sidebar-menu-icon"><i class="bi bi-shield-lock-fill"></i></span>
            <span class="sidebar-menu-text">Keamanan</span>
            <i class="bi bi-chevron-right menu-chevron"></i>
        </a>

        <div class="sidebar-menu-divider" role="separator"></div>

        {{-- Kembali ke Beranda --}}
        <a href="{{ route('home') }}" class="sidebar-menu-link sidebar-menu-link--muted">
            <span class="sidebar-menu-icon"><i class="bi bi-house-door-fill"></i></span>
            <span class="sidebar-menu-text">Kembali ke Beranda</span>
        </a>
    </nav>

    {{-- Sidebar Footer Branding --}}
    <div class="sidebar-footer-brand mt-auto hidden lg:block">
        <div class="sidebar-footer-brand-inner">
            <div class="flex items-center gap-3">
                <div class="sidebar-footer-emblem shrink-0">
                    <i class="bi bi-bank2"></i>
                </div>
                <div class="flex flex-col leading-tight min-w-0">
                    <span class="sidebar-footer-title">SUKABUMI</span>
                    <span class="sidebar-footer-subtitle">One Access</span>
                </div>
            </div>
            <div class="sidebar-footer-divider"></div>
            <p class="sidebar-footer-meta">
                <i class="bi bi-patch-check-fill"></i>
                Official Portal &middot; v2.1.0
            </p>
        </div>
    </div>
</aside>

{{-- ============ FLOATING ASSIST MENU (mobile only) ============ --}}
<div id="mobileAssist" class="mobile-assist">
    <button type="button" id="assistBtn" class="mobile-assist-btn" aria-label="Buka menu akun" aria-haspopup="true" aria-expanded="false">
        <i class="bi bi-grid-3x3-gap-fill"></i>
    </button>

    <div id="assistMenu" class="mobile-assist-menu">
        <div class="mobile-assist-menu-header">
            <span>Menu Akun</span>
            <button type="button" id="assistCloseBtn" aria-label="Tutup menu"><i class="bi bi-x-lg"></i></button>
        </div>

        <a href="{{ route('user.profile') }}" class="mobile-assist-item {{ Route::is('user.profile') ? 'active' : '' }}">
            <span class="mobile-assist-item-icon"><i class="bi bi-person-fill"></i></span>
            <span>Detail Profil</span>
        </a>
        <a href="{{ route('user.security') }}" class="mobile-assist-item {{ Route::is('user.security') ? 'active' : '' }}">
            <span class="mobile-assist-item-icon"><i class="bi bi-shield-lock-fill"></i></span>
            <span>Keamanan</span>
        </a>
        <a href="{{ route('home') }}" class="mobile-assist-item">
            <span class="mobile-assist-item-icon"><i class="bi bi-house-door-fill"></i></span>
            <span>Kembali ke Beranda</span>
        </a>
    </div>
</div>

<style>
    /* =========================================
       SIDEBAR PROFIL — SATU-SATUNYA SUMBER ATURAN
       untuk .dashboard-wrapper & .profile-sidebar
       di breakpoint mobile/tablet. Jangan duplikasi
       aturan ini di file lain.
       ========================================= */

    /* ---------- Desktop sidebar container ---------- */
    .profile-sidebar {
        display: flex;
        flex-direction: column;
        padding: 1.75rem 1.1rem 1.5rem;
    }

    /* ---------- Kartu identitas user ---------- */
    .sidebar-avatar {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: linear-gradient(155deg, #123457 0%, #0A1E33 100%);
        color: #E8A33D;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.9rem;
        overflow: hidden;
        box-shadow: 0 6px 16px rgba(10, 30, 51, 0.22);
        border: 2px solid rgba(232, 163, 61, 0.35);
    }

    .sidebar-user-name {
        font-size: 0.82rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar-user-tag {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 2px;
        font-size: 0.62rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: #94a3b8;
    }

    .sidebar-user-tag i {
        color: #E8A33D;
        font-size: 0.65rem;
    }

    .sidebar-title-label {
        font-size: 0.62rem;
        font-weight: 800;
        color: #b3bdcc;
        text-transform: uppercase;
        letter-spacing: 0.22em;
    }

    /* ---------- Menu navigasi ---------- */
    .sidebar-menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 12px;
        margin: 0 0.25rem 4px;
        border-radius: 14px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
        text-decoration: none;
        position: relative;
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.15s ease;
    }

    .sidebar-menu-icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #123457;
        font-size: 0.85rem;
        flex-shrink: 0;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .sidebar-menu-text { flex: 1; min-width: 0; }

    .menu-chevron {
        font-size: 0.65rem;
        color: #cbd5e1;
        opacity: 0;
        transform: translateX(-4px);
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .sidebar-menu-link:hover {
        background: #f8fafc;
        color: #123457;
    }

    .sidebar-menu-link:hover .sidebar-menu-icon {
        background: #eef2f7;
    }

    .sidebar-menu-link:hover .menu-chevron {
        opacity: 1;
        transform: translateX(0);
    }

    .sidebar-menu-link.active {
        background: linear-gradient(135deg, #123457 0%, #0A1E33 100%);
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(10, 30, 51, 0.24);
    }

    .sidebar-menu-link.active .sidebar-menu-icon {
        background: rgba(232, 163, 61, 0.18);
        color: #E8A33D;
    }

    .sidebar-menu-link.active .menu-chevron {
        color: #E8A33D;
        opacity: 1;
        transform: translateX(0);
    }

    .sidebar-menu-link--muted {
        color: #94a3b8;
    }

    .sidebar-menu-link--muted .sidebar-menu-icon {
        background: #f8fafc;
        color: #94a3b8;
    }

    .sidebar-menu-divider {
        height: 1px;
        margin: 10px 1rem 12px;
        background: #f1f5f9;
    }

    /* ---------- Footer branding "official portal" ---------- */
    .sidebar-footer-brand {
        margin-top: 1.5rem;
    }

    .sidebar-footer-brand-inner {
        padding: 1.1rem 1.1rem 0.9rem;
        border-radius: 1.25rem;
        background: linear-gradient(160deg, #0A1E33 0%, #123457 65%, #16406e 100%);
        border: 1px solid rgba(232, 163, 61, 0.25);
        box-shadow: 0 12px 28px rgba(10, 30, 51, 0.22);
        position: relative;
        overflow: hidden;
    }

    .sidebar-footer-brand-inner::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(120px 80px at 100% 0%, rgba(232, 163, 61, 0.16), transparent 70%);
        pointer-events: none;
    }

    .sidebar-footer-emblem {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: rgba(232, 163, 61, 0.16);
        border: 1px solid rgba(232, 163, 61, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #E8A33D;
        font-size: 0.95rem;
    }

    .sidebar-footer-title {
        font-size: 0.72rem;
        font-weight: 900;
        letter-spacing: 0.05em;
        color: #ffffff;
    }

    .sidebar-footer-subtitle {
        font-size: 0.6rem;
        font-weight: 700;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: #E8A33D;
        margin-top: 1px;
    }

    .sidebar-footer-divider {
        height: 1px;
        margin: 0.85rem 0 0.6rem;
        background: rgba(255, 255, 255, 0.12);
    }

    .sidebar-footer-meta {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.6rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: rgba(255, 255, 255, 0.65);
        margin: 0;
    }

    .sidebar-footer-meta i {
        color: #E8A33D;
        font-size: 0.65rem;
    }

    @media (max-width: 1024px) {
        /* Sidebar penuh disembunyikan, diganti tombol mengambang */
        .profile-sidebar { display: none; }
    }

    /* ---------- Floating Assist Button (ala AssistiveTouch) ---------- */
    .mobile-assist { display: none; }

    @media (max-width: 1024px) {
        .mobile-assist {
            display: block;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 9000;
            touch-action: none;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .mobile-assist.idle {
            opacity: 0.55;
            transform: scale(0.94);
        }

        .mobile-assist-btn {
            width: 54px;
            height: 54px;
            border-radius: 9999px;
            background: linear-gradient(155deg, #123457 0%, #0A1E33 100%);
            color: #E8A33D;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255,255,255,0.25);
            box-shadow: 0 10px 25px rgba(10, 30, 51, 0.35);
            cursor: grab;
            touch-action: none;
        }

        .mobile-assist-btn:active { cursor: grabbing; }

        .mobile-assist-menu {
            position: absolute;
            bottom: calc(100% + 14px);
            right: 0;
            width: 216px;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 20px 45px rgba(10, 30, 51, 0.28);
            padding: 8px;
            opacity: 0;
            transform: scale(0.85);
            transform-origin: bottom right;
            pointer-events: none;
            transition: opacity 0.15s ease, transform 0.15s ease;
        }

        .mobile-assist-menu.open {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
        }

        /* Ekor gelembung yang mengarah ke tombol, biar kelihatan seperti bubble */
        .mobile-assist-menu::after {
            content: '';
            position: absolute;
            width: 14px;
            height: 14px;
            background: #ffffff;
            border-right: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            transform: rotate(45deg);
            bottom: -7px;
            right: 24px;
        }

        /* Flip posisi menu + ekor berdasarkan letak tombol di layar (diatur lewat JS) */
        #mobileAssist.open-left .mobile-assist-menu { right: auto; left: 0; transform-origin: bottom left; }
        #mobileAssist.open-left .mobile-assist-menu::after { right: auto; left: 24px; }

        #mobileAssist.open-down .mobile-assist-menu { bottom: auto; top: calc(100% + 14px); transform-origin: top right; }
        #mobileAssist.open-down .mobile-assist-menu::after {
            bottom: auto; top: -7px;
            border-right: none; border-bottom: none;
            border-left: 1px solid #f1f5f9;
            border-top: 1px solid #f1f5f9;
        }

        #mobileAssist.open-down.open-left .mobile-assist-menu { transform-origin: top left; }

        .mobile-assist-menu-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 4px 8px 8px;
            margin-bottom: 4px;
            border-bottom: 1px solid #f1f5f9;
        }

        .mobile-assist-menu-header span {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #94a3b8 !important;
        }

        .mobile-assist-menu-header button {
            width: 20px; height: 20px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 9999px;
            color: #94a3b8 !important;
            font-size: 0.6rem;
        }

        /* !important dipakai khusus di widget ini supaya warna teks & ikon
           tidak ketiban aturan global (mis. style anchor/link) dari halaman utama */
        .mobile-assist-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 12px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #475569 !important;
            text-decoration: none !important;
            transition: background-color 0.15s ease;
        }

        .mobile-assist-item span:last-child { color: inherit !important; }

        .mobile-assist-item:active,
        .mobile-assist-item:hover { background: #f1f5f9; }

        .mobile-assist-item.active {
            background: #123457;
            color: #ffffff !important;
        }

        .mobile-assist-item-icon {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #123457 !important;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .mobile-assist-item.active .mobile-assist-item-icon {
            background: rgba(255,255,255,0.15);
            color: #E8A33D !important;
        }
    }
</style>

<script>
    (function () {
        var wrap = document.getElementById('mobileAssist');
        var btn = document.getElementById('assistBtn');
        var menu = document.getElementById('assistMenu');
        var closeBtn = document.getElementById('assistCloseBtn');
        if (!wrap || !btn || !menu) return;

        var STORAGE_KEY = 'soaAssistPos';
        var EDGE_GAP = 10;
        var isOpen = false;
        var idleTimer = null;

        function btnSize() { return btn.offsetWidth || 54; }

        function clamp(val, min, max) { return Math.max(min, Math.min(val, max)); }

        function setPosition(left, top, persist) {
            var size = btnSize();
            left = clamp(left, EDGE_GAP, window.innerWidth - size - EDGE_GAP);
            top = clamp(top, EDGE_GAP, window.innerHeight - size - EDGE_GAP);
            wrap.style.left = left + 'px';
            wrap.style.top = top + 'px';
            if (persist) {
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify({
                        xFrac: left / window.innerWidth,
                        yFrac: top / window.innerHeight
                    }));
                } catch (e) {}
            }
        }

        function restorePosition() {
            var saved = null;
            try { saved = JSON.parse(localStorage.getItem(STORAGE_KEY)); } catch (e) {}
            if (saved && typeof saved.xFrac === 'number') {
                setPosition(saved.xFrac * window.innerWidth, saved.yFrac * window.innerHeight, false);
            } else {
                var size = btnSize();
                setPosition(window.innerWidth - size - 16, window.innerHeight - size - 96, false);
            }
        }

        function resetIdleTimer() {
            wrap.classList.remove('idle');
            clearTimeout(idleTimer);
            idleTimer = setTimeout(function () { wrap.classList.add('idle'); }, 2800);
        }

        function positionMenuFlip() {
            var rect = wrap.getBoundingClientRect();
            var menuWidth = menu.offsetWidth || 216;
            var menuHeight = menu.offsetHeight || 230;
            var gap = 14;

            // Default menu nempel di kanan wrapper & meluas ke KIRI.
            // Kalau itu bakal kepotong tepi kiri layar, balik: nempel di kiri wrapper & meluas ke KANAN.
            var fitsExtendingLeft = (rect.right - menuWidth) >= 8;
            wrap.classList.toggle('open-left', !fitsExtendingLeft);

            // Default menu nongol di ATAS tombol. Kalau kepotong tepi atas layar,
            // balik: nongol di BAWAH tombol.
            var fitsAbove = (rect.top - menuHeight - gap) >= 8;
            wrap.classList.toggle('open-down', !fitsAbove);
        }

        function openMenu() {
            positionMenuFlip();
            menu.classList.add('open');
            btn.setAttribute('aria-expanded', 'true');
            isOpen = true;
        }

        function closeMenu() {
            menu.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
            isOpen = false;
        }

        function toggleMenu() { isOpen ? closeMenu() : openMenu(); }

        function snapToEdge() {
            var rect = wrap.getBoundingClientRect();
            var goLeft = (rect.left + rect.width / 2) < (window.innerWidth / 2);
            wrap.style.transition = 'left .25s cubic-bezier(0.34,1.56,0.64,1)';
            setPosition(goLeft ? EDGE_GAP : window.innerWidth - btnSize() - EDGE_GAP, rect.top, true);
            setTimeout(function () { wrap.style.transition = ''; }, 260);
        }

        // ---- Drag handling (mouse + touch via Pointer Events) ----
        var dragging = false, moved = false, startX, startY, startLeft, startTop;

        btn.addEventListener('pointerdown', function (e) {
            dragging = true; moved = false;
            startX = e.clientX; startY = e.clientY;
            var rect = wrap.getBoundingClientRect();
            startLeft = rect.left; startTop = rect.top;
            try { btn.setPointerCapture(e.pointerId); } catch (err) {}
            resetIdleTimer();
        });

        btn.addEventListener('pointermove', function (e) {
            if (!dragging) return;
            var dx = e.clientX - startX, dy = e.clientY - startY;
            if (!moved && (Math.abs(dx) > 6 || Math.abs(dy) > 6)) {
                moved = true;
                closeMenu();
            }
            if (!moved) return;
            setPosition(startLeft + dx, startTop + dy, false);
        });

        btn.addEventListener('pointerup', function (e) {
            if (!dragging) return;
            dragging = false;
            try { btn.releasePointerCapture(e.pointerId); } catch (err) {}
            if (moved) {
                snapToEdge();
            } else {
                toggleMenu();
            }
            resetIdleTimer();
        });

        btn.addEventListener('pointercancel', function () { dragging = false; });

        closeBtn.addEventListener('click', closeMenu);

        document.addEventListener('pointerdown', function (e) {
            if (isOpen && !wrap.contains(e.target)) closeMenu();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && isOpen) closeMenu();
        });

        wrap.querySelectorAll('.mobile-assist-item').forEach(function (item) {
            item.addEventListener('click', closeMenu);
        });

        window.addEventListener('resize', function () {
            var rect = wrap.getBoundingClientRect();
            setPosition(rect.left, rect.top, true);
        });

        restorePosition();
        resetIdleTimer();
    })();
</script>