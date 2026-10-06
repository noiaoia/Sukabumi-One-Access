@extends('layouts.admin')

@section('title', 'Daftar Layanan')
@section('page-title', 'Manajemen Layanan')
@section('page-icon', 'bi-grid-3x3-gap-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<style>
    /* 1. CONTAINER LAYANAN (Identik dengan Gudang Instansi) */
    .svc-container {
        margin-bottom: 12px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 22px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 14px rgba(10, 30, 51, 0.03);
    }

    .svc-container:hover {
        border-color: rgba(232, 163, 61, 0.45);
    }

    .svc-row { padding: 16px 20px; }

    /* ACTION GROUP REVEAL (Identik) */
    .action-group {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    @media (hover: hover) and (pointer: fine) {
        .action-group {
            opacity: 0;
            visibility: hidden;
            transform: translateX(16px);
            transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .svc-row:hover .action-group {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }
    }

    /* Laci Konfigurasi (Drawer) */
    .config-drawer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: #F8FAFC;
        border-top: 1px solid #f1f5f9;
    }

    .svc-container.active .config-drawer {
        max-height: 500px;
    }

    /* Mini Config Cards — sekarang ikonnya dalam kotak warna + panah hover,
       triad warna disamakan dengan modal "Metode Integrasi": navy = iframe,
       emerald = form, gold/accent = opsi netral (visual card) */
    .mini-config-card {
        position: relative;
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-radius: 20px;
        padding: 20px;
        transition: all 0.3s ease;
        text-align: center;
        display: block;
        text-decoration: none !important;
    }
    .mini-config-card:hover {
        border-color: #E8A33D;
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }

    .mini-config-icon {
        width: 46px; height: 46px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; margin: 0 auto 14px;
        transition: all 0.3s ease;
    }

    .mini-config-card.visual .mini-config-icon { background: rgba(232, 163, 61, 0.12); color: #E8A33D; }
    .mini-config-card.visual:hover .mini-config-icon { background: #E8A33D; color: #fff; }

    .mini-config-card.iframe .mini-config-icon { background: rgba(18, 52, 87, 0.08); color: #123457; }
    .mini-config-card.iframe:hover .mini-config-icon { background: #123457; color: #E8A33D; }

    .mini-config-card.form .mini-config-icon { background: rgba(16, 185, 129, 0.1); color: #10B981; }
    .mini-config-card.form:hover .mini-config-icon { background: #10B981; color: #fff; }

    .mini-config-arrow {
        position: absolute; top: 16px; right: 16px;
        color: #cbd5e1; font-size: 12px; opacity: 0;
        transform: translateX(-4px); transition: all 0.25s ease;
    }
    .mini-config-card:hover .mini-config-arrow { opacity: 1; transform: translateX(0); color: #E8A33D; }

    .avatar-svc {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; color: #E8A33D; flex-shrink: 0;
    }

    .btn-icon-premium {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; border: none;
    }

    .status-pill {
        font-size: 9px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 1px; padding: 4px 10px; border-radius: 8px;
    }

/* Style khusus Dropdown Status Layanan */
    .status-select-wrapper {
        position: relative;
        display: inline-block;
    }

    .status-select {
        appearance: none;
        -webkit-appearance: none;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 6px 30px 6px 12px;
        border-radius: 10px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.3s ease;
        outline: none;
    }

    .status-select-wrapper::after {
        content: '\F288';
        font-family: "bootstrap-icons";
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 8px;
        pointer-events: none;
        opacity: 0.6;
    }

    .status-active { background: #ecfdf5; color: #059669; border-color: #10b98120; }
    .status-draft { background: #fff1f2; color: #e11d48; border-color: #f43f5e20; }

    .status-select:hover {
        filter: brightness(0.95);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .type-tag {
        font-size: 8px; font-weight: 900; text-transform: uppercase;
        letter-spacing: 1px; padding: 2px 8px; border-radius: 5px;
        margin-top: 4px; display: inline-block;
    }

    /* Animasi halus untuk perpindahan halaman */
    @keyframes opFadeSlide {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .reveal-item {
        animation: opFadeSlide 0.4s ease forwards;
    }
</style>

<div class="max-w-6xl mx-auto space-y-6">

    {{-- Top Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white premium-shadow border border-slate-100 rounded-3xl p-5 mb-8">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.layanan.index', $portalCode) }}" class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-primary hover:text-white transition-all shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="text-lg font-black text-primary-dark leading-none">Layanan {{ $instansi->nama }}</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">Kelola jalur akses aplikasi warga</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative flex-1 sm:flex-initial">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="svcSearch" placeholder="Cari layanan..." class="bg-primary/5 border border-primary/10 rounded-full pl-10 pr-4 py-2.5 text-xs font-bold text-primary w-full sm:w-56 focus:border-accent focus:bg-white outline-none transition-all">
            </div>
            <a href="{{ route('admin.layanan.iframe.create', [$portalCode, 'instansi' => $instansi->slug]) }}" 
            class="bg-primary text-white h-10 w-10 sm:w-auto sm:px-6 rounded-full flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all active:scale-95 flex-shrink-0">
                <i class="bi bi-plus-lg"></i> <span class="hidden sm:inline">Tambah Layanan</span>
            </a>
        </div>
    </div>

    <div id="svcList">
        @foreach($layananList as $svc)
        <div class="svc-container" id="svc-{{ $svc->id }}">
            <div class="svc-row flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-3">

                {{-- Info Utama --}}
                <div class="flex items-center gap-4 min-w-0 flex-1">
                    <div class="avatar-svc" style="background-color: {{ $svc->primary_color }}">
                        <i class="bi {{ $svc->icon }}"></i>
                    </div>
                    <div class="min-w-0 text-left">
                        <h4 class="text-sm font-black text-primary-dark truncate">{{ $svc->nama }}</h4>
                        <span class="type-tag {{ $svc->jenis == 'iframe' ? 'bg-primary/10 text-primary' : 'bg-emerald-50 text-emerald-600' }}">
                            {{ $svc->jenis == 'iframe' ? 'Portal Iframe' : 'Formulir Mandiri' }}
                        </span>
                        {{-- Target URL: tampil ringkas di sini kalau kolom "Target Akses" tersembunyi (di bawah lg) --}}
                        <p class="text-[9px] font-mono font-semibold text-slate-400 truncate mt-1 lg:hidden">{{ $svc->iframe_url }}</p>
                    </div>
                </div>

                {{-- Sisi Tengah (Target URL) --}}
                <div class="hidden lg:flex items-center px-8 border-l border-slate-50 ml-4 flex-1 text-left">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Target Akses</span>
                        <span class="text-xs font-mono font-bold text-slate-500 truncate max-w-[220px]">{{ $svc->iframe_url }}</span>
                    </div>
                </div>

                {{-- Status & Group Tombol --}}
                <div class="flex items-center justify-between sm:justify-end gap-4 sm:gap-6 flex-shrink-0">
                    {{-- DROPDOWN STATUS INTERAKTIF --}}
                    <div class="status-select-wrapper">
                        <select data-url="{{ route('admin.layanan.iframe.status', [$portalCode, $svc->id]) }}" onchange="updateSvcStatus(this)" class="status-select {{ $svc->status === 'aktif' ? 'status-active' : 'status-draft' }}">
                            <option value="aktif"
                                {{ $svc->status === 'aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>

                            <option value="draft"
                                {{ $svc->status === 'draft' ? 'selected' : '' }}>
                                Draft
                            </option>
                        </select>
                    </div>

                    <div class="action-group">
                        {{-- Tombol Buka Config --}}
                        <button onclick="toggleSvcConfig('{{ $svc->id }}')"
                                class="btn-icon-premium bg-primary/5 text-primary/60 hover:bg-primary hover:text-white"
                                title="Konfigurasi">
                            <i class="bi bi-gear-wide-connected" id="icon-{{ $svc->id }}"></i>
                        </button>

                        {{-- TOMBOL EDIT DASAR — belum disambungkan ke modal/route, tampilan saja untuk saat ini --}}
                        <button 
                            onclick="openEditLayananIframeModal({{ json_encode($svc) }}, '{{ route('admin.layanan.iframe.update', [$portalCode, $svc->id]) }}')" 
                            class="btn-icon-premium bg-accent/10 text-accent-dark hover:bg-accent hover:text-white">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        {{-- TOMBOL HAPUS — belum disambungkan ke modal/route, tampilan saja untuk saat ini --}}
                        <button 
                            onclick="openDeleteLayananModal({{ json_encode($svc) }}, '{{ route('admin.layanan.iframe.destroy', [$portalCode, $svc->id]) }}')" 
                            class="btn-icon-premium bg-red-50 text-red-400 hover:bg-red-600 hover:text-white">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Laci Konfigurasi (Drawer) --}}
            <div class="config-drawer">
                <div class="p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                        <h5 class="text-[10px] font-black text-primary uppercase tracking-[0.2em]">Panel Kendali {{ $svc->nama }}</h5>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Link ke Visual Card -->
                        <a href="{{ route('admin.layanan.iframe.visual', [$portalCode, $svc->id]) }}" class="mini-config-card visual group">
                            <i class="bi bi-arrow-right mini-config-arrow"></i>
                            <div class="mini-config-icon"><i class="bi bi-card-image"></i></div>
                            <span class="text-xs font-black text-primary-dark uppercase tracking-widest">Visual Card</span>
                            <p class="text-[10px] text-slate-400 mt-1">Atur thumbnail, ikon, dan deskripsi card</p>
                        </a>

                        <!-- Link ke Iframe Settings / Form Builder -->
                        <a href="{{ route('admin.layanan.iframe.editor', [$portalCode, $svc->id]) }}" class="mini-config-card iframe group">
                            <i class="bi bi-arrow-right mini-config-arrow"></i>
                            <div class="mini-config-icon"><i class="bi bi-window-fullscreen"></i></div>
                            <span class="text-xs font-black text-primary-dark uppercase tracking-widest">Iframe Bridge</span>
                            <p class="text-[10px] text-slate-400 mt-1">Konfigurasi link dan header portal</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        <div id="opPagination" class="flex items-center justify-center gap-2 mt-8">
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Konfigurasi Paginasi
    const itemsPerPage = 3; 
    let currentPage = 1;
    let filteredElements = [];

    // Inisialisasi awal
    function initPagination() {
        const allContainers = document.querySelectorAll('.svc-container');
        filteredElements = Array.from(allContainers);
        renderPage(1);
    }

    // FUNGSI RENDER HALAMAN DENGAN TRANSISI HALUS
    function renderPage(page) {
        currentPage = page;
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const itemsToShow = filteredElements.slice(start, end);

        // 1. Sembunyikan semua item lebih dulu
        document.querySelectorAll('.svc-container').forEach(el => {
            el.style.display = 'none';
            el.classList.remove('reveal-item'); // Reset animasi
            el.style.opacity = '0';
        });

        // 2. Tampilkan item satu per satu (Staggered Effect)
        itemsToShow.forEach((el, index) => {
            el.style.display = 'block';
            
            // Memberikan delay 60ms antar baris agar muncul mengalir
            setTimeout(() => {
                el.classList.add('reveal-item');
            }, index * 60); 
        });

        updatePaginationButtons();
    }

    // UPDATE TOMBOL PAGINASI (Desain Tetap)
    function updatePaginationButtons() {
        const totalPages = Math.ceil(filteredElements.length / itemsPerPage);
        const paginationWrapper = document.getElementById('opPagination');
        paginationWrapper.innerHTML = '';

        if (totalPages <= 1) return;

        // Tombol Prev
        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
        prevBtn.className = `w-10 h-10 rounded-xl flex items-center justify-center transition-all ${currentPage === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-primary hover:bg-primary/10'}`;
        prevBtn.onclick = () => { if(currentPage > 1) renderPage(currentPage - 1); };
        paginationWrapper.appendChild(prevBtn);

        // Tombol Angka
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.innerText = i;
            pageBtn.className = `w-10 h-10 rounded-xl text-xs font-black transition-all ${i === currentPage ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-400 hover:bg-slate-100'}`;
            pageBtn.onclick = () => renderPage(i);
            paginationWrapper.appendChild(pageBtn);
        }

        // Tombol Next
        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
        nextBtn.className = `w-10 h-10 rounded-xl flex items-center justify-center transition-all ${currentPage === totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-primary hover:bg-primary/10'}`;
        nextBtn.onclick = () => { if(currentPage < totalPages) renderPage(currentPage + 1); };
        paginationWrapper.appendChild(nextBtn);
    }

    // LIVE SEARCH
    document.getElementById('svcSearch').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        const allContainers = document.querySelectorAll('.svc-container');
        
        filteredElements = Array.from(allContainers).filter(container => {
            const name = container.querySelector('h4').textContent.toLowerCase();
            const username = container.querySelector('p').textContent.toLowerCase();
            return name.includes(query) || username.includes(query);
        });

        renderPage(1);
    });

    // FUNGSI TOGGLE LOG DRAWER (Perbaikan Icon Toggle)
    function toggleSvcConfig(id) {
        const container = document.getElementById('svc-' + id);
        const icon = document.getElementById('icon-' + id);
        const isActive = container.classList.contains('active');

        // Tutup laci lain
        document.querySelectorAll('.svc-container').forEach(el => {
            el.classList.remove('active');
            const otherIcon = el.querySelector('.bi-chevron-up');
            if (otherIcon) otherIcon.classList.replace('bi-chevron-up', 'bi-gear-wide-connected');
        });

        if (!isActive) {
            container.classList.add('active');
            icon.classList.replace('bi-gear-wide-connected', 'bi-chevron-up');
        }
    }

    document.addEventListener('DOMContentLoaded', initPagination);

    /**
     * FUNGSI UPDATE STATUS LAYANAN
     */
    async function updateSvcStatus(selectElement)
    {
        const url = selectElement.dataset.url;
        const status = selectElement.value;

        // Update warna langsung
        selectElement.className =
            'status-select ' +
            (status === 'aktif'
                ? 'status-active'
                : 'status-draft');

        try {

            const response = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status
                })
            });

            const result = await response.json();

            if (!result.success) {
                alert('Status gagal diperbarui.');
            }

        } catch (error) {

            alert('Terjadi kesalahan.');

            console.error(error);
        }
    }
</script>
@endpush

@endsection