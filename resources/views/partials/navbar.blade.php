{{-- resources/views/partials/navbar.blade.php --}}

<!-- =============================================================
     1. CSS (STYLING)
     ============================================================= -->
<style>
    /* Global Navbar & Blur Effect */
    .navbar-glass {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    /* Logo & Brand Scaling */
    @media (max-width: 640px) {
        .brand-logo-img { width: 34px !important; height: 34px !important; }
        .brand-name-text { font-size: 0.9rem !important; }
        .brand-sub-text { font-size: 7px !important; letter-spacing: 0.1em !important; }
    }

    /* Profile Image Fix */
    .avatar-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Sidebar Mobile Base Style */
    #mobile-sidebar {
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 101;
        visibility: hidden; /* Mencegah elemen terdeteksi saat tertutup */
    }
    
    #mobile-sidebar.open {
        visibility: visible;
        transform: translateX(0);
    }

    #sidebar-overlay {
        transition: opacity 0.4s ease, backdrop-filter 0.4s ease;
        z-index: 100;
        backdrop-filter: blur(0px);
    }
    
    #sidebar-overlay.show {
        backdrop-filter: blur(4px);
        opacity: 1 !important;
    }

    /* --- ANIMASI MENU SIDEBAR (STAGGERED) --- */
    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 14px 16px;
        border-radius: 16px;
        font-weight: 700;
        color: #64748b;
        transition: all 0.25s ease;
        /* State awal untuk animasi */
        opacity: 0;
        transform: translateX(20px);
    }

    /* Ketika sidebar terbuka, jalankan animasi pada link */
    #mobile-sidebar.open .sidebar-link {
        animation: slideInMenu 0.5s ease forwards;
    }

    @keyframes slideInMenu {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Delay berurutan untuk tiap menu */
    #mobile-sidebar.open .sidebar-link:nth-child(1) { animation-delay: 0.1s; }
    #mobile-sidebar.open .sidebar-link:nth-child(2) { animation-delay: 0.2s; }
    #mobile-sidebar.open .sidebar-link:nth-child(3) { animation-delay: 0.3s; }
    #mobile-sidebar.open .sidebar-link:nth-child(4) { animation-delay: 0.4s; }
    #mobile-sidebar.open .sidebar-link:nth-child(5) { animation-delay: 0.5s; }

    .sidebar-link.active {
        background: #123457;
        color: #ffffff !important;
        box-shadow: 0 10px 20px -5px rgba(18, 52, 87, 0.25);
    }

    .sidebar-scroll-content::-webkit-scrollbar { display: none; }

    /* --- NOTIFIKASI (BARU) --- */
    #user-notif-dropdown{
        width:320px;
        max-width:calc(100vw - 2.5rem);
        max-height:min(26rem,70vh);

        display:flex;
        flex-direction:column;

        opacity:0;
        visibility:hidden;
        transform:translateY(10px) scale(.98);

        transition:
            opacity .25s ease,
            transform .25s ease,
            visibility .25s;
    }

    @media (min-width:640px){
        #user-notif-dropdown{
            width:380px;
        }
    }

    #user-notif-dropdown.is-open{
        opacity:1;
        visibility:visible;
        transform:translateY(0) scale(1);
    }

    .notif-item-user {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 12px 20px;
        border-bottom: 1px solid #F8FAFC;
        transition: background 0.15s ease;
        text-decoration: none;
    }
    .notif-item-user:last-child { border-bottom: none; }
    .notif-item-user{
        transition:.25s ease;
    }

    .notif-item-user.unread {
        background:#FFF8EC;
        border-left:3px solid #E8A33D;
        position: relative;
        padding-left: 28px;
    }
    .notif-item-user.unread::before {
        content: '';
        position: absolute;
        left: 10px; top: 19px;
        width: 6px; height: 6px;
        border-radius: 999px;
        background: #E8A33D;
    }

    .notif-icon-user {
        width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: 0.9rem;
    }

    .notif-scroll-user{
        flex:1;
        min-height:0;
        overflow-y:auto;
        overscroll-behavior:contain;
        background:#F8FAFC;
    }
    .notif-scroll-user::-webkit-scrollbar { width: 4px; }
    .notif-scroll-user::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }
</style>


<!-- =============================================================
    2. HTML (STRUCTURE)
============================================================= -->
<header class="navbar-glass sticky top-0 z-50 w-full border-b border-slate-100 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-3 flex justify-between items-center lg:grid lg:grid-cols-[300px_1fr_300px]">
        
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-3 group lg:justify-self-start">
            <div class="brand-logo-img w-10 h-10 rounded-xl bg-primary flex items-center justify-center shadow-lg shadow-primary/20 group-hover:scale-105 transition-transform">
                <i class="bi bi-bank text-accent text-lg"></i>
            </div>
            <div class="flex flex-col">
                <span class="brand-name-text font-extrabold text-primary leading-none tracking-tighter text-base sm:text-lg">SUKABUMI</span>
                <span class="brand-sub-text text-[10px] font-bold text-accent uppercase tracking-[0.2em] leading-none mt-1">One Access</span>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex items-center justify-center justify-self-center gap-8">
            @php
                $navLinks = [
                    ['name' => 'Beranda', 'route' => 'home', 'protected' => false],
                    ['name' => 'Layanan', 'route' => 'layanan', 'protected' => true],
                    ['name' => 'Pengaduan', 'route' => 'pengaduan', 'protected' => true],
                    ['name' => 'Tentang', 'route' => 'about', 'protected' => false],
                ];
            @endphp

            @foreach($navLinks as $link)
                @php
                    $isActive = Route::is($link['route']);
                    $class = "relative text-sm font-bold transition-colors duration-200 py-2 " . ($isActive ? 'text-primary' : 'text-slate-500 hover:text-primary');
                @endphp

                @if($link['protected'] && !Auth::check())
                    {{-- Jika menu diproteksi DAN belum login --}}
                    <button onclick="openAuthAlertModal()" class="{{ $class }} border-none bg-transparent">
                        {{ $link['name'] }}
                    </button>
                @else
                    {{-- Jika menu publik ATAU sudah login --}}
                    <a href="{{ route($link['route']) }}" class="{{ $class }}">
                        {{ $link['name'] }}
                        @if($isActive)
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-accent rounded-full"></span>
                        @endif
                    </a>
                @endif
            @endforeach
        </nav>

        <!-- Right Side Actions -->
        <div class="flex items-center justify-end gap-2 sm:gap-5 lg:justify-self-end">

            {{-- NOTIFIKASI --}}
            @php
                $userNotificationCount = $userNotificationCount ?? 0;
                $userNotifications     = $userNotifications ?? collect();
            @endphp
            @auth
            <div class="relative" id="user-notif-wrapper">
                <button onclick="toggleUserNotifDropdown(event)" class="relative p-2 rounded-xl hover:bg-slate-100 transition-colors text-slate-500">
                    <i class="bi bi-bell text-xl"></i>
                    @if($userNotificationCount > 0)
                        <span
                            id="notificationBadge"
                            class="absolute top-1.5 right-1.5 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[9px] font-bold rounded-full border-2 border-white flex items-center justify-center
                            {{ $userNotificationCount > 0 ? '' : 'hidden' }}">
                            {{ $userNotificationCount }}
                        </span>
                    @endif
                </button>

                <div id="user-notif-dropdown" onclick="event.stopPropagation()" class="absolute right-0 mt-5 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-50">
                    <div class="flex items-center justify-between px-5 py-4 bg-primary text-white flex-shrink-0">
                        <div>
                            <h4 class="text-sm font-black text-white leading-none">Notifikasi</h4>
                            <p
                                id="notificationCountText"
                                class="text-[10px] font-semibold text-white/70 mt-1">
                                {{ $userNotificationCount }} belum dibaca
                            </p>
                        </div>
                        <button
                            id="markUserNotificationRead"

                            {{ $userNotificationCount == 0 ? 'disabled' : '' }}

                            class="text-[10px] font-bold uppercase tracking-wide transition

                            {{ $userNotificationCount == 0
                                ? 'text-white/40 cursor-not-allowed'
                                : 'text-accent hover:text-white'
                            }}">
                            Tandai dibaca
                        </button>
                    </div>

                    <div class="notif-scroll-user">
                        @forelse($userNotifications as $notif)
                            @php
                                // 1. Identifikasi Jenis Notifikasi
                                $isComplaint = isset($notif->ticket_id); // Jika punya ticket_id = Pengaduan
                                $status = strtolower($notif->status);
                                
                                // 2. Tentukan Konfigurasi Visual (Icon, Warna, Judul)
                                switch ($status) {
                                    case 'review':
                                        $icon = 'bi-search';
                                        $iconBg = 'bg-blue-100';
                                        $iconColor = 'text-blue-600';
                                        $displayTitle = $isComplaint ? 'Laporan sedang ditinjau' : 'Berkas sedang ditinjau';
                                        break;

                                    case 'process':
                                    case 'diproses':
                                        $icon = 'bi-gear-fill';
                                        $iconBg = 'bg-amber-100';
                                        $iconColor = 'text-amber-600';
                                        $displayTitle = $isComplaint ? 'Laporan sedang diproses' : 'Berkas sedang diproses';
                                        break;

                                    case 'perlu_revisi': // Status khusus Pengajuan Berkas
                                        $icon = 'bi-exclamation-triangle-fill';
                                        $iconBg = 'bg-rose-100';
                                        $iconColor = 'text-rose-600';
                                        $displayTitle = 'Berkas perlu perbaikan';
                                        break;

                                    case 'done':
                                    case 'selesai':
                                        $icon = 'bi-check-circle-fill';
                                        $iconBg = 'bg-emerald-100';
                                        $iconColor = 'text-emerald-600';
                                        $displayTitle = $isComplaint ? 'Laporan telah selesai' : 'Berkas telah selesai';
                                        break;

                                    case 'rejected':
                                    case 'ditolak':
                                        $icon = 'bi-x-circle-fill';
                                        $iconBg = 'bg-red-100';
                                        $iconColor = 'text-red-600';
                                        $displayTitle = $isComplaint ? 'Laporan ditolak' : 'Berkas ditolak';
                                        break;

                                    default:
                                        $icon = 'bi-bell-fill';
                                        $iconBg = 'bg-slate-100';
                                        $iconColor = 'text-slate-600';
                                        $displayTitle = 'Pembaruan Status';
                                }

                                // 3. Tentukan Link Tujuan
                                $targetUrl = $isComplaint 
                                            ? route('user.riwayat.pengaduan') 
                                            : route('user.riwayat.pengajuan');
                            @endphp

                            <a href="{{ $targetUrl }}" class="notif-item-user unread">
                                <div class="notif-icon-user {{ $iconBg }} {{ $iconColor }}">
                                    <i class="bi {{ $icon }}"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    {{-- Label Jenis Layanan --}}
                                    <p class="text-[10px] font-black uppercase tracking-wider mb-0.5 {{ $iconColor }}">
                                        {{ $isComplaint ? 'Pusat Pengaduan' : 'Layanan Berkas' }}
                                    </p>

                                    <p class="text-xs font-bold text-slate-700 leading-tight">
                                        {{ $displayTitle }}
                                    </p>

                                    <p class="text-[11px] text-slate-400 mt-1 truncate">
                                        {{ $notif->title ?? ($notif->layanan->nama_layanan ?? 'Klik untuk detail') }}
                                    </p>

                                    {{-- Tampilkan Pesan Admin jika ada --}}
                                    @php
                                        $adminMsg = $notif->response_message ?? $notif->catatan_admin;
                                    @endphp
                                    @if($adminMsg)
                                        <p class="text-[10px] text-slate-500 mt-1 line-clamp-1 italic">
                                            "{{ $adminMsg }}"
                                        </p>
                                    @endif

                                    <span class="text-[9px] font-bold text-slate-300 mt-1 block">
                                        {{ $notif->updated_at->diffForHumans() }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            {{-- Tampilan jika kosong --}}
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="bi bi-bell-slash text-2xl text-slate-200"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                                    Belum ada notifikasi
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ route('user.notifications') }}"
                    class="block text-center py-3 text-[11px] font-bold text-primary hover:text-accent-dark uppercase tracking-widest bg-slate-100 hover:bg-white border-t border-slate-200 transition">
                        Lihat Semua Notifikasi
                    </a>
                </div>
            </div>
            @else
            <button onclick="openAuthAlertModal()" class="relative p-2 rounded-xl hover:bg-slate-100 transition-colors text-slate-500">
                <i class="bi bi-bell text-xl"></i>
            </button>
            @endauth

            <div class="h-8 w-[1px] bg-slate-200 mx-1 hidden md:block"></div>
            
            @auth
                <div class="relative hidden lg:block" id="user-menu-wrapper">
                    <button onclick="toggleUserDropdown()" class="flex items-center gap-3 bg-primary hover:bg-primary-dark text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md transition-all active:scale-95">
                        <div class="avatar-box w-7 h-7 rounded-lg overflow-hidden border border-accent/30 bg-white/10 flex items-center justify-center">
                            @if(Auth::user()->profile_photo)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}">
                            @else
                                <i class="bi bi-person-circle text-accent text-base"></i>
                            @endif
                        </div>
                        <span class="max-w-[100px] truncate block">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down text-[10px]"></i>
                    </button>

                    <div id="user-dropdown-menu" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-50 animate__animated animate__fadeInUp animate__faster">
                        <a href="{{ route('user.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">
                            <i class="bi bi-person-badge text-accent"></i> Lihat Profil
                        </a>
                        <div class="border-t border-slate-50 my-1"></div>
                        <button type="button" onclick="openLogoutModal()" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-red-500 hover:bg-red-50 transition-all">
                            <i class="bi bi-power"></i> Keluar Sesi
                        </button>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center bg-primary text-white text-xs sm:text-sm font-bold px-6 py-2.5 rounded-xl shadow-md transition-all">Masuk</a>
            @endauth

            <!-- Mobile Hamburger -->
            <button onclick="toggleSidebar(true)" class="lg:hidden p-2 text-primary bg-primary/5 rounded-xl active:scale-90 transition">
                <i class="bi bi-grid-fill text-xl"></i>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" onclick="toggleSidebar(false)" class="fixed inset-0 bg-slate-900/60 hidden opacity-0"></div>

<!-- Mobile Sidebar -->
<aside id="mobile-sidebar" class="fixed top-0 right-0 h-full w-[280px] sm:w-[320px] bg-white shadow-2xl translate-x-full flex flex-col">
    <div class="p-5 flex items-center justify-between border-b border-slate-50">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center">
                <i class="bi bi-bank text-accent text-sm"></i>
            </div>
            <span class="font-black text-primary tracking-tighter">SUKABUMI</span>
        </div>
        <button onclick="toggleSidebar(false)" class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-500 rounded-full active:scale-90 transition">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="sidebar-scroll-content flex-1 py-8 px-5 space-y-1 overflow-y-auto">
        <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Navigasi Utama</p>
        
        {{-- List Menu dengan Animasi Staggered --}}
        <div class="menu-items-wrapper space-y-1">
            {{-- Beranda --}}
            <a href="{{ route('home') }}" class="sidebar-link {{ Route::is('home') ? 'active' : '' }}">
                <i class="bi bi-house-door text-lg"></i> Beranda
            </a>

            {{-- Layanan (Protected) --}}
            @if(Auth::check())
                <a href="{{ route('layanan') }}" class="sidebar-link {{ Route::is('layanan') ? 'active' : '' }}">
                    <i class="bi bi-grid text-lg"></i> Layanan
                </a>
            @else
                <button onclick="toggleSidebar(false); openAuthAlertModal()" class="sidebar-link w-full text-left bg-transparent border-none">
                    <i class="bi bi-grid text-lg"></i> Layanan
                </button>
            @endif

            {{-- Pengaduan (Protected) --}}
            @if(Auth::check())
                <a href="{{ route('pengaduan') }}" class="sidebar-link {{ Route::is('pengaduan') ? 'active' : '' }}">
                    <i class="bi bi-chat-dots text-lg"></i> Pengaduan
                </a>
            @else
                <button onclick="toggleSidebar(false); openAuthAlertModal()" class="sidebar-link w-full text-left bg-transparent border-none">
                    <i class="bi bi-chat-dots text-lg"></i> Pengaduan
                </button>
            @endif

            {{-- Tentang --}}
            <a href="{{ route('about') }}" class="sidebar-link {{ Route::is('about') ? 'active' : '' }}">
                <i class="bi bi-info-circle text-lg"></i> Tentang
            </a>
        </div>
    </div>

    <div class="p-5 border-t border-slate-50 bg-slate-50/50">
        @auth
            <div class="space-y-4">
                {{-- Kartu Profil: Berubah menjadi tag <a> agar bisa diklik --}}
                <a href="{{ route('user.profile') }}" class="p-4 bg-white rounded-[20px] border border-slate-100 shadow-sm flex items-center gap-3 active:scale-95 transition-all no-underline block">
                    <div class="avatar-box w-12 h-12 rounded-2xl bg-primary flex items-center justify-center text-white font-bold border-2 border-accent overflow-hidden flex-shrink-0">
                        @if(Auth::user()->profile_photo)
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="flex flex-col overflow-hidden">
                        <span class="text-xs text-accent font-bold uppercase tracking-widest leading-none mb-1">Akun Saya</span>
                        <span class="font-black text-primary text-sm truncate block">{{ Auth::user()->name }}</span>
                    </div>
                    {{-- Ikon panah sebagai petunjuk visual bahwa ini bisa diklik --}}
                    <i class="bi bi-chevron-right ms-auto text-slate-300"></i>
                </a>

                {{-- Tombol Keluar tetap di bawahnya --}}
                <button type="button" onclick="toggleSidebar(false); openLogoutModal()" class="w-full flex items-center justify-center gap-2 bg-red-50 text-red-600 py-4 rounded-2xl font-bold text-xs active:scale-95 transition-all">
                    KELUAR SESI <i class="bi bi-power"></i>
                </button>
            </div>
        @else
            <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 bg-primary text-white py-4 rounded-2xl font-bold active:scale-95 transition-all shadow-lg shadow-primary/20">
                Masuk Sekarang <i class="bi bi-box-arrow-in-right"></i>
            </a>
        @endauth
    </div>
</aside>


<!-- =============================================================
    3. JAVASCRIPT (LOGIC)
============================================================= -->
<script>
    const userMarkNotificationUrl = "{{ route('notifications.read') }}";
    const csrfToken = "{{ csrf_token() }}";

    function toggleSidebar(show) {
        const sidebar = document.getElementById('mobile-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (show) {
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                overlay.classList.add('show');
                sidebar.classList.add('open');
            }, 10);
        } else {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = 'auto';
            setTimeout(() => { 
                overlay.classList.add('hidden'); 
            }, 500);
        }
    }

    function toggleUserDropdown() {
        const menu = document.getElementById('user-dropdown-menu');
        // Tutup dropdown notifikasi kalau lagi kebuka, biar gak numpuk
        document.getElementById('user-notif-dropdown')?.classList.add('hidden');
        menu.classList.toggle('hidden');
    }

    function toggleUserNotifDropdown(event) {
        event.stopPropagation();
        const menu = document.getElementById('user-notif-dropdown');
        document.getElementById('user-dropdown-menu')
            ?.classList.remove('is-open');
        menu.classList.toggle('is-open');
    }

    document.addEventListener('click', function(e) {
        const notifWrapper = document.getElementById('user-notif-wrapper');
        const notifMenu = document.getElementById('user-notif-dropdown');

        if (notifMenu && !notifWrapper.contains(e.target)) {
            notifMenu.classList.remove('is-open');
        }

        const userWrapper = document.getElementById('user-menu-wrapper');
        const userMenu = document.getElementById('user-dropdown-menu');

        if (userMenu && userWrapper && !userWrapper.contains(e.target)) {
            userMenu.classList.add('hidden');
        }
    });

    // Esc untuk menutup sidebar mobile & dropdown yang lagi kebuka
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Escape') return;

        const sidebar = document.getElementById('mobile-sidebar');
        if (sidebar && sidebar.classList.contains('open')) {
            toggleSidebar(false);
        }

        document.getElementById('user-dropdown-menu')?.classList.add('hidden');
        document.getElementById('user-notif-dropdown')?.classList.add('hidden');
    });

    const markUserBtn = document.getElementById('markUserNotificationRead');
    if (markUserBtn) {
        markUserBtn.addEventListener('click', async function () {
            if (this.disabled) return;
            try {
                const response = await fetch(userMarkNotificationUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (result.success) {
                    location.reload();
                }
            } catch (error) {
                console.error(error);
            }
        });
    }
</script>

@auth
<script>
    (function() {
        // 1. Simpan Jumlah Notif dan Timestamp Update Terakhir
        let lastUserNotifCount = parseInt("{{ $userNotificationCount ?? 0 }}") || 0;
        let lastUserNotifTimestamp = 0; // Akan diisi saat pengecekan pertama
        
        const userCheckNotifUrl = "{{ route('notifications.check-count') }}";

        function playUserNotifSound() {
            const audio = document.getElementById('notifSoundUser');
            if (audio) {
                audio.currentTime = 0;
                audio.play().catch(function(e) {
                    // Browser biasanya memblokir autoplay jika belum ada interaksi
                });
            }
        }

        async function checkUserNotifications() {
            try {
                const response = await fetch(userCheckNotifUrl);
                if (!response.ok) return;
                
                const data = await response.json();
                
                // LOGIKA: Bunyi jika ada Notif Baru ATAU ada Update Status pada notif yang sudah ada
                const isNewNotif = data.count > lastUserNotifCount;
                const isStatusUpdate = data.latest_updated_at > lastUserNotifTimestamp && lastUserNotifTimestamp !== 0;

                if (isNewNotif || isStatusUpdate) {
                    playUserNotifSound();
                    
                    // Update UI Badge
                    const badge = document.getElementById('notificationBadge');
                    if (badge) {
                        badge.textContent = data.count;
                        data.count > 0 ? badge.classList.remove('hidden') : badge.classList.add('hidden');
                    }

                    // Update Teks Dropdown
                    const countText = document.getElementById('notificationCountText');
                    if (countText) {
                        countText.textContent = data.count + " belum dibaca";
                    }

                    // Update Tombol "Tandai dibaca"
                    const markBtn = document.getElementById('markUserNotificationRead');
                    if (markBtn) {
                        markBtn.disabled = (data.count === 0);
                        if (data.count > 0) {
                            markBtn.classList.remove('text-white/40', 'cursor-not-allowed');
                            markBtn.classList.add('text-accent', 'hover:text-white');
                        } else {
                            markBtn.classList.add('text-white/40', 'cursor-not-allowed');
                            markBtn.classList.remove('text-accent', 'hover:text-white');
                        }
                    }
                }
                
                // Simpan data terbaru untuk pengecekan berikutnya
                lastUserNotifCount = data.count;
                lastUserNotifTimestamp = data.latest_updated_at;

            } catch (error) {
                // Silently fail jika koneksi bermasalah
            }
        }

        // Jalankan pengecekan pertama kali saat halaman dimuat untuk ambil timestamp awal
        checkUserNotifications();

        // Jalankan pengecekan rutin setiap 5 detik
        setInterval(checkUserNotifications, 5000);
    })();
</script>
@endauth
<audio id="notifSoundUser" preload="auto">
    <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
</audio>