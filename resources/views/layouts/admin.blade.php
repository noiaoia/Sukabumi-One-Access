<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - Sukabumi One Access</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#123457', dark: '#0A1E33' },
                        accent: { DEFAULT: '#E8A33D', dark: '#3A2205' },
                    },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAF7F1;
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #0A1E33; }
        ::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }

        .sidebar-navy {
            background: linear-gradient(180deg, #0A1E33 0%, #06121D 100%);
        }

        .nav-link {
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #94a3b8;
        }

        .nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(2px);
        }

        .nav-link.active {
            color: #ffffff;
            background: rgba(232, 163, 61, 0.1);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            bottom: 20%;
            width: 4px;
            background: #E8A33D;
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 15px #E8A33D;
        }

        .nav-link.active i {
            color: #E8A33D;
        }

        .nav-badge {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5);
            animation: badgePulse 2s infinite;
        }
        @keyframes badgePulse {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .glass-header {
            background: rgba(250, 247, 241, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .premium-shadow {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        #mobileSidebarOverlay { transition: opacity 0.3s ease; }
        #mobileSidebar { transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1); }

        @media (max-width: 1023px) {
            #mobileSidebar { transform: translateX(-100%); }
            #mobileSidebar.is-open { transform: translateX(0); }
        }

        /* ---- Dropdown umum (profil & notifikasi pakai pola animasi yang sama) ---- */
        #profileDropdown,
        #notifDropdown {
            opacity: 0;
            visibility: hidden;
            transform: translateY(8px);
            transition: all 0.2s ease;
        }
        #profileDropdown.is-open,
        #notifDropdown.is-open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        #notifDropdown {
            z-index: 40;
            width: 320px;
            max-width: calc(100vw - 2.5rem);
            max-height: min(28rem, 70vh);
            display: flex;
            flex-direction: column;
        }
        @media (min-width: 640px) {
            #notifDropdown { width: 384px; }
        }

        .notif-header,
        .notif-footer {
            flex-shrink: 0;
        }

        .notif-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 12px 20px;
            border-bottom: 1px solid #F8FAFC;
            transition: background 0.15s ease;
        }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover { background: #F8FAFC; }
        .notif-item.unread {
            background: rgba(232, 163, 61, 0.05);
            position: relative;
            padding-left: 28px;
        }
        .notif-item.unread::before {
            content: '';
            position: absolute;
            left: 10px; top: 19px;
            width: 6px; height: 6px;
            border-radius: 999px;
            background: #E8A33D;
        }

        .notif-icon {
            width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 0.9rem;
        }

        .notif-scroll { flex: 1; min-height: 0; overflow-y: auto; overscroll-behavior: contain; }
        .notif-scroll::-webkit-scrollbar { width: 4px; }
        .notif-scroll::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }
    </style>
</head>
<body class="antialiased" data-notification-count="{{ $complaintNotificationCount }}">
    <!-- Variabel Component -->
    @include('components.modal-logout-confirm')
    @include('components.admin.modal-edit-operator')
    @include('components.admin.modal-delete-operator')
    @include('components.admin.modal-edit-instansi')
    @include('components.admin.modal-delete-instansi')
    @include('components.admin.modal-service-type')
    @include('components.admin.modal-edit-layanan-iframe-admin')
    @include('components.admin.modal-delete-layanan-iframe-admin')
    @include('components.admin.modal-edit-formulir')
    @include('components.admin.modal-edit-operator')
    @include('components.admin.modal-delete-category')
    @include('components.admin.modal-global-alert')

    <div class="flex min-h-screen">

        <!-- MOBILE SIDEBAR OVERLAY -->
        <div id="mobileSidebarOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden lg:hidden" onclick="toggleMobileSidebar()"></div>

        <!-- SIDEBAR -->
        <aside id="mobileSidebar" class="w-64 sidebar-navy flex flex-col fixed lg:sticky top-0 h-screen z-50 lg:z-40">

            <!-- Brand -->
            <div class="p-5 flex items-center justify-between border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/10 shadow-xl flex-shrink-0">
                        <i class="bi bi-bank text-accent text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-sm font-black text-white leading-none tracking-tighter truncate">SUKABUMI</h1>
                        <p class="text-[9px] font-bold text-accent uppercase tracking-[0.2em] mt-1">One Access Admin</p>
                    </div>
                </div>
                <button onclick="toggleMobileSidebar()" class="lg:hidden w-8 h-8 rounded-lg bg-white/5 border border-white/10 text-slate-400 hover:text-white flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <!-- Navigation -->
            @php
                $portalCode = 'portal-' . Auth::user()->username;
            @endphp

            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <p class="px-3 text-[9px] font-bold text-slate-500 uppercase tracking-[0.25em] mb-3">Main Menu</p>

                {{-- DASHBOARD (Shared) --}}
                <a href="{{ route('admin.dashboard', $portalCode) }}"
                class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl text-[13px] font-bold">
                    <i class="bi bi-grid-1x2-fill text-base"></i> Dashboard
                </a>

                {{-- ================= KHUSUS SUPER ADMIN ================= --}}
                @if(Auth::user()->isSuperAdmin())
                    <div class="pt-5 mt-5 border-t border-white/5">
                        <p class="px-3 text-[9px] font-bold text-slate-500 uppercase tracking-[0.25em] mb-3">User Management</p>
                        <a href="{{ route('admin.operator.index', $portalCode) }}" class="nav-link {{ Route::is('admin.operator.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl text-[13px] font-bold">
                            <i class="bi bi-people-fill text-base"></i> Kelola Super Admin
                        </a>
                        <a href="{{ route('admin.admin-dinas.index', $portalCode) }}" class="nav-link {{ Route::is('admin.admin-dinas.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl text-[13px] font-bold">
                            <i class="bi bi-person-badge-fill text-base"></i> Kelola Admin Dinas
                        </a>
                        <a href="{{ route('admin.users.index', $portalCode) }}" class="nav-link {{ Route::is('admin.users.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl text-[13px] font-bold">
                            <i class="bi bi-person-lines-fill text-base"></i> Kelola Pengguna
                        </a>
                    </div>

                    <div class="pt-5 mt-5 border-t border-white/5">
                        <p class="px-3 text-[9px] font-bold text-slate-500 uppercase tracking-[0.25em] mb-3">Portal Configuration</p>
                        <a href="{{ route('admin.instansi.index', $portalCode) }}" class="nav-link {{ Route::is('admin.instansi.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl text-[13px] font-bold">
                            <i class="bi bi-building-fill-gear text-base"></i> Master Instansi
                        </a>
                        <a href="{{ route('admin.layanan.index', $portalCode) }}" class="nav-link {{ Route::is('admin.layanan.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl text-[13px] font-bold">
                            <i class="bi bi-link-45deg text-base"></i> Master Layanan
                        </a>
                    </div>
                @endif

                {{-- ================= KEDINASAN (Admin Dinas & Operator) ================= --}}
                @if(Auth::user()->isAdminDinas() || Auth::user()->role === 'operator')
                    <div class="pt-5 mt-5 border-t border-white/5">
                        <p class="px-3 text-[9px] font-bold text-slate-500 uppercase tracking-[0.25em] mb-3">Kedinasan</p>
                        
                        {{-- HANYA Admin Dinas yang bisa kelola staf/operator --}}
                        @if(Auth::user()->isAdminDinas())
                            <a href="{{ route('admin.operator-dinas.index', $portalCode) }}" 
                            class="nav-link {{ Route::is('admin.operator-dinas.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl text-[13px] font-bold">
                                <i class="bi bi-people-fill text-base"></i> Kelola Operator
                            </a>
                        @endif

                        {{-- Keduanya bisa akses profil instansi dan layanan --}}
                        <a href="{{ route('admin.instansi.index', $portalCode) }}"
                        class="nav-link {{ Route::is('admin.instansi.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl text-[13px] font-bold">
                            <i class="bi bi-building text-base"></i> Profil Instansi
                        </a>
                        <a href="{{ route('admin.layanan.index', $portalCode) }}"
                        class="nav-link {{ Route::is('admin.layanan.index') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl text-[13px] font-bold">
                            <i class="bi bi-layers-fill text-base"></i> Layanan Kami
                        </a>
                    </div>
                @endif

                {{-- ================= PELAYANAN (Shared / Keduanya) ================= --}}
                <div class="pt-5 mt-5 border-t border-white/5">
                    <p class="px-3 text-[9px] font-bold text-slate-500 uppercase tracking-[0.25em] mb-3">Pelayanan Aktif</p>

                    <a href="{{ route('admin.pengaduan.index', $portalCode) }}"
                    class="nav-link {{ Route::is('admin.pengaduan.index') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl text-[13px] font-bold">
                        <i class="bi bi-chat-left-text-fill text-base"></i>
                        <span class="flex-1">Pengaduan</span>
                        @if(isset($unreadComplaintCount) && $unreadComplaintCount > 0)
                            <span class="nav-badge bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full">{{ $unreadComplaintCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.submission.index', $portalCode) }}"
                    class="nav-link {{ Route::is('admin.submission.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl text-[13px] font-bold">
                        <i class="bi bi-file-earmark-person-fill text-base"></i>
                        <span class="flex-1">Pengajuan</span>
                        @if(isset($pendingSubmissionCount) && $pendingSubmissionCount > 0)
                            <span class="nav-badge bg-emerald-500 text-white text-[10px] px-2 py-0.5 rounded-full font-black">{{ $pendingSubmissionCount }}</span>
                        @endif
                    </a>
                </div>

                {{-- ================= DATA MANAGEMENT (Shared / Keduanya) ================= --}}
                <div class="pt-5 mt-5 border-t border-white/5">
                    <p class="px-3 text-[9px] font-bold text-slate-500 uppercase tracking-[0.25em] mb-3">Data Management</p>

                    {{-- MENU ARSIP LAPORAN --}}
                    <a href="{{ route('admin.pengaduan.archive', [$portalCode, 'status' => 'archived']) }}"
                    class="nav-link {{ Route::is('admin.pengaduan.archive') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl text-[13px] font-bold">
                        <i class="bi bi-archive-fill text-base"></i>
                        <span class="flex-1">Arsip Laporan</span>
                        <span class="text-[8px] bg-white/10 text-slate-400 px-1.5 py-0.5 rounded border border-white/10">HISTORY</span>
                    </a>

                    @if(Auth::user()->isSuperAdmin())
                    <a href="{{ route('admin.activity.index', $portalCode) }}"
                    class="nav-link {{ Route::is('admin.activity.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl text-[13px] font-bold">
                        <i class="bi bi-clock-history text-base"></i>
                        <span>Log Aktivitas</span>
                    </a>
                    @endif
                </div>
            </nav>

            <!-- Bottom Profile -->
            <div class="p-4 mt-auto relative">
                <button onclick="toggleProfileDropdown()" class="w-full bg-white/5 border border-white/10 rounded-2xl p-3.5 flex items-center gap-2.5 hover:bg-white/10 transition">
                    <div class="w-9 h-9 rounded-lg bg-accent flex items-center justify-center font-black text-primary-dark text-sm flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden text-left">
                        <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest truncate">
                            @if(Auth::user()->isSuperAdmin())
                                Super Admin
                            @elseif(Auth::user()->role === 'operator')
                                Staff {{ Auth::user()->agency->nama ?? Auth::user()->instansi }}
                            @else
                                Admin {{ Auth::user()->agency->nama ?? Auth::user()->instansi }}
                            @endif
                        </p>
                    </div>
                    <i class="bi bi-chevron-up ml-auto text-slate-400 text-xs"></i>
                </button>

                <div id="profileDropdown" class="absolute bottom-full left-4 right-4 mb-2 bg-[#0F2A47] border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                    <a href="{{ route('admin.profile.index', $portalCode) }}"
                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-slate-300 hover:bg-white/5 hover:text-white transition">
                        <i class="bi bi-person-circle"></i>
                        Profil Saya
                    </a>
                    <a href="{{ route('admin.settings.index', ['portal_code' => 'portal-'.Auth::user()->username]) }}"
                        class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-slate-300 hover:bg-white/5 hover:text-white transition">
                        <i class="bi bi-gear-fill"></i>
                        Pengaturan
                    </a>
                    <div class="h-[1px] bg-white/10"></div>
                    <a href="javascript:void(0)" onclick="openLogoutModal()" class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-red-400 hover:bg-red-500/10 transition">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </a>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header/Topbar -->
            <header class="h-24 glass-header border-b border-primary/10 flex items-center justify-between px-5 sm:px-10 sticky top-0 z-30 gap-4">

                <div class="flex items-center gap-4 min-w-0">
                    <button onclick="toggleMobileSidebar()" class="lg:hidden w-11 h-11 rounded-2xl bg-primary/5 border border-primary/10 text-primary flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i class="bi bi-list text-xl"></i>
                    </button>

                    <div class="hidden sm:flex w-11 h-11 rounded-2xl bg-primary/8 text-primary items-center justify-center text-lg flex-shrink-0 border border-primary/10">
                        <i class="bi @yield('page-icon', 'bi-grid-1x2-fill')"></i>
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight truncate">@yield('page-title', 'Dashboard Overview')</h2>
                        <p class="text-xs font-medium text-slate-400 hidden sm:block">@yield('page-subtitle', 'Selamat datang kembali, Admin.')</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-6">

                    {{-- Jam & tanggal live, disembunyikan di layar sempit --}}
                    <div class="hidden md:flex items-center gap-3 bg-primary/5 border border-primary/10 rounded-2xl px-4 py-2.5">
                        <i class="bi bi-clock-history text-primary/50 text-sm"></i>
                        <div class="leading-tight">
                            <p id="liveClock" class="text-xs font-black text-slate-700 tabular-nums">--:--:--</p>
                            <p id="liveDate" class="text-[9px] font-semibold text-slate-400 uppercase tracking-wide">Memuat tanggal...</p>
                        </div>
                    </div>

                    <!-- Status Server -->
                    <div class="hidden xl:flex items-center gap-3 bg-primary/5 px-4 py-2 rounded-2xl border border-primary/10">
                        <div class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">System Operational</span>
                    </div>

                    <div class="h-10 w-[1px] bg-primary/10 hidden sm:block"></div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 sm:gap-3">

                        {{-- NOTIFIKASI --}}
                        <div class="relative">
                            <button onclick="toggleNotifDropdown()" class="relative w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-primary/5 border border-primary/10 text-primary/70 hover:text-primary transition-all flex items-center justify-center text-xl shadow-sm">
                                <i class="bi bi-bell"></i>
                                @if($complaintNotificationCount > 0)
                                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[9px] font-black rounded-full border-2 border-white flex items-center justify-center">
                                        {{ $complaintNotificationCount }}
                                    </span>
                                @endif
                            </button>

                            <div id="notifDropdown" class="absolute right-0 top-full mt-3 bg-white border border-slate-100 rounded-2xl shadow-2xl overflow-hidden">
                                <div class="notif-header flex items-center justify-between px-5 py-4 border-b border-slate-50">
                                    <div>
                                        <h4 class="text-sm font-black text-primary-dark leading-none">Notifikasi</h4>
                                        @if($complaintNotificationCount > 0)
                                            <p class="text-[10px] font-semibold text-slate-400 mt-1">
                                                {{ $complaintNotificationCount }} tugas baru butuh tindakan
                                            </p>
                                        @else
                                            <p class="text-[10px] font-semibold text-slate-400 mt-1">
                                                Tidak ada notifikasi baru
                                            </p>
                                        @endif
                                    </div>
                                    <button
                                        id="markAllNotificationRead"
                                        {{ $complaintNotificationCount == 0 ? 'disabled' : '' }}
                                        class="text-[10px] font-bold uppercase tracking-wide transition
                                        {{ $complaintNotificationCount == 0
                                            ? 'text-slate-300 cursor-not-allowed'
                                            : 'text-primary hover:text-accent-dark'
                                        }}">
                                        Tandai dibaca
                                    </button>
                                </div>

                                <div class="notif-scroll">
                                    {{-- 1. NOTIFIKASI PENGADUAN --}}
                                    @if($latestComplaint)
                                        <a href="{{ route('admin.pengaduan.index', $portalCode) }}"
                                            class="notif-item unread">
                                            <div class="notif-icon bg-red-50 text-red-500">
                                                <i class="bi bi-chat-left-dots-fill"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-bold text-slate-700 leading-snug">
                                                    Pengaduan Baru
                                                </p>
                                                <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                                    {{ $latestComplaint->title }}
                                                </p>
                                                <span class="text-[10px] font-semibold text-slate-300 mt-1 block">
                                                    {{ $latestComplaint->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </a>
                                    @endif

                                    {{-- 2. NOTIFIKASI PENGAJUAN BERKAS (BARU) --}}
                                    @if($latestSubmission)
                                        <a href="{{ route('admin.submission.index', $portalCode) }}"
                                            class="notif-item unread">
                                            <div class="notif-icon bg-emerald-50 text-emerald-500">
                                                <i class="bi bi-file-earmark-text-fill"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-bold text-slate-700 leading-snug">
                                                    Berkas Masuk Baru
                                                </p>
                                                <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                                    {{ $latestSubmission->nomor_pengajuan }} - {{ $latestSubmission->layanan->nama_layanan }}
                                                </p>
                                                <span class="text-[10px] font-semibold text-slate-300 mt-1 block">
                                                    {{ $latestSubmission->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </a>
                                    @endif

                                    {{-- 3. JIKA KEDUANYA KOSONG --}}
                                    @if(!$latestComplaint && !$latestSubmission)
                                        <div class="flex items-center justify-center py-10">
                                            <div class="text-center">
                                                <i class="bi bi-bell-slash text-3xl text-slate-300"></i>
                                                <p class="mt-3 text-xs font-bold text-slate-500">
                                                    Tidak ada notifikasi baru
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <a href="{{ route('admin.notifications.index', $portalCode) }}" class="notif-footer block text-center py-3 text-[11px] font-bold text-primary hover:text-accent-dark uppercase tracking-widest bg-slate-50 border-t border-slate-100 transition">
                                    Lihat Semua Notifikasi
                                </a>
                            </div>
                        </div>

                        <button onclick="openLogoutModal()" class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-red-50 border border-red-100 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center text-xl shadow-sm">
                            <i class="bi bi-power"></i>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="p-5 sm:p-10">
                @yield('admin-content')
            </main>
        </div>
    </div>

    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('mobileSidebarOverlay');
            const isOpen = sidebar.classList.toggle('is-open');

            if (isOpen) {
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        // ---- Dropdown header: profil & notifikasi saling menutup satu sama lain ----
        function closeAllHeaderDropdowns(exceptId) {
            ['profileDropdown', 'notifDropdown'].forEach(id => {
                if (id !== exceptId) document.getElementById(id)?.classList.remove('is-open');
            });
        }

        function toggleProfileDropdown() {
            const el = document.getElementById('profileDropdown');
            const willOpen = !el.classList.contains('is-open');
            closeAllHeaderDropdowns();
            if (willOpen) el.classList.add('is-open');
        }

        function toggleNotifDropdown() {
            const el = document.getElementById('notifDropdown');
            const willOpen = !el.classList.contains('is-open');
            closeAllHeaderDropdowns();
            if (willOpen) el.classList.add('is-open');
        }

        document.addEventListener('click', (e) => {
            const profileTrigger = e.target.closest('[onclick="toggleProfileDropdown()"]');
            const notifTrigger = e.target.closest('[onclick="toggleNotifDropdown()"]');
            const profileDropdown = document.getElementById('profileDropdown');
            const notifDropdown = document.getElementById('notifDropdown');

            if (!profileTrigger && profileDropdown && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('is-open');
            }
            if (!notifTrigger && notifDropdown && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.remove('is-open');
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                document.getElementById('mobileSidebar').classList.remove('is-open');
                document.getElementById('mobileSidebarOverlay').classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('mobileSidebar');
                if (sidebar.classList.contains('is-open')) toggleMobileSidebar();
                closeAllHeaderDropdowns();
            }
        });

        // JAM & TANGGAL LIVE
        (function () {
            const clockEl = document.getElementById('liveClock');
            const dateEl = document.getElementById('liveDate');
            if (!clockEl || !dateEl) return;

            function updateClock() {
                const now = new Date();

                const time = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
                });

                const date = now.toLocaleDateString('id-ID', {
                    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
                });

                clockEl.textContent = time + ' WIB';
                dateEl.textContent = date;
            }

            updateClock();
            setInterval(updateClock, 1000);
        })();


        const notificationUrl = "{{ route('admin.pengaduan.notif-count', $portalCode) }}";
        const markNotificationUrl = "{{ route('admin.pengaduan.notifications.read', $portalCode) }}";
        const csrfToken = "{{ csrf_token() }}";
        const initialNotificationCount =parseInt(document.body.dataset.notificationCount || '0', 10);

        let lastNotificationCount = initialNotificationCount;
        function playNotificationSound() {
            const audio = document.getElementById('notifSound');
            audio.currentTime = 0;
            audio.play().catch(() => {});
        }
        async function checkNotification() {
            try {
                const response = await fetch(notificationUrl);
                const data = await response.json();
                if (data.count > lastNotificationCount) {
                    playNotificationSound();
                }
                lastNotificationCount = data.count;
            } catch (error) {
                console.error(error);
            }
        }
        document.getElementById('markAllNotificationRead')
        ?.addEventListener('click', async function () {
            try {
                const response = await fetch(markNotificationUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    location.reload();
                }
            } catch (error) {
                console.error(error);
            }
        });
        setInterval(checkNotification, 3000);

        function sendHeartbeat() {
            if (navigator.onLine) {
                fetch("{{ route('admin.notifications.index', $portalCode) }}", {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).catch(err => {});
            }
        }
        setInterval(sendHeartbeat, 180000);
        sendHeartbeat();
    </script>

<audio id="notifSound" preload="auto">
    <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
</audio>

    @stack('scripts')
</body>
</html>