@extends('layouts.admin')

@section('title', 'Gudang Instansi')
@section('page-title', 'Manajemen Instansi')
@section('page-icon', 'bi-building')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<style>
    .ins-container {
        margin-bottom: 12px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 22px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 14px rgba(10, 30, 51, 0.03);
    }

    .ins-container:hover {
        border-color: rgba(232, 163, 61, 0.45);
    }

    .ins-row { padding: 16px 20px; }

    /* ACTION GROUP REVEAL */
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
        .ins-row:hover .action-group {
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
        background: #fcfcfd;
        border-top: 1px solid #f1f5f9;
    }

    .ins-container.active .config-drawer {
        max-height: 500px;
    }

    /* Config Cards inside Drawer */
    .mini-config-card {
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

    .avatar-ins { 
        width: 42px; height: 42px; border-radius: 12px; 
        display: flex; align-items: center; justify-content: center; 
        font-weight: 800; color: #fff; flex-shrink: 0; 
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

    /* Style untuk Status Pill yang bisa diklik */
    .status-pill {
        font-size: 9px; 
        font-weight: 800; 
        text-transform: uppercase;
        letter-spacing: 1px; 
        padding: 6px 12px; 
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .status-pill:hover {
        transform: scale(1.05);
        filter: brightness(0.9);
    }

    .pill-aktif { background: #ecfdf5; color: #059669; border: 1px solid #10b98120; }
    .pill-draft { background: #fff1f2; color: #e11d48; border: 1px solid #f43f5e20; }

    /* Style untuk Status Dropdown */
    .status-select-wrapper {
        position: relative;
        display: inline-block;
    }

    .status-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 6px 30px 6px 12px; /* Extra padding kanan untuk icon */
        border-radius: 10px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.3s ease;
        outline: none;
    }

    /* Icon panah custom untuk dropdown */
    .status-select-wrapper::after {
        content: '\F288'; /* Bootstrap Icon bi-chevron-down */
        font-family: "bootstrap-icons";
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 8px;
        pointer-events: none;
        opacity: 0.6;
    }

    /* Warna sesuai status */
    .status-active { 
        background: #ecfdf5; 
        color: #059669; 
        border-color: #10b98120; 
    }
    .status-draft { 
        background: #fff1f2; 
        color: #e11d48; 
        border-color: #f43f5e20; 
    }

    .status-select:hover {
        filter: brightness(0.95);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
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
            <h3 class="text-lg font-black text-primary-dark leading-none">Gudang Instansi</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">Kelola visual dan profil dinas</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative flex-1 sm:flex-initial">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="insSearch" placeholder="Cari instansi..." class="bg-primary/5 border border-primary/10 rounded-full pl-10 pr-4 py-2.5 text-xs font-bold text-primary w-full sm:w-56 focus:border-accent focus:bg-white outline-none transition-all">
            </div>
            @if(Auth::user()->isSuperAdmin())
                <a href="{{ route('admin.instansi.create', $portalCode) }}" class="bg-primary text-white h-10 w-10 sm:w-auto sm:px-6 rounded-full flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all active:scale-95 flex-shrink-0">
                    <i class="bi bi-plus-lg"></i> 
                    <span class="hidden sm:inline">Tambah Instansi</span>
                </a>
            @endif
        </div>
    </div>

    <div id="insList">
        @foreach($instansiList as $ins)
        <div class="ins-container" id="ins-{{ $ins->id }}">
            <div class="ins-row flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-3">
                
                {{-- Info Utama --}}
                <div class="flex items-center gap-4 min-w-0 flex-1">
                    <div class="avatar-ins" style="background-color: {{ $ins->primary_color }}">
                        {{ strtoupper(substr($ins->nama, 0, 1)) }}
                    </div>
                    <div class="min-w-0 text-left">
                        <h4 class="text-sm font-black text-primary-dark truncate">{{ $ins->nama }}</h4>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter truncate">URL: /{{ $ins->slug }}</p>
                    </div>
                </div>

                <div class="hidden lg:flex items-center px-8 border-l border-slate-50 ml-4 flex-1 text-left">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Warna Identitas</span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <div class="w-3 h-3 rounded-full" style="background: {{ $ins->primary_color }}"></div>
                            <span class="text-xs font-mono font-bold text-slate-500">{{ $ins->primary_color }}</span>
                        </div>
                    </div>
                </div>

                {{-- Status & Group Tombol --}}
                <div class="flex items-center justify-between sm:justify-end gap-4 sm:gap-6 flex-shrink-0">
                    {{-- DROPDOWN STATUS --}}
                    @if(Auth::user()->isSuperAdmin())
                        <div class="status-select-wrapper">
                            <select 
                                onchange="updateStatus('{{ $ins->id }}', this)"
                                class="status-select {{ $ins->status == 'aktif' ? 'status-active' : 'status-draft' }}">
                                <option value="aktif" {{ $ins->status == 'aktif' ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="draft" {{ $ins->status == 'draft' ? 'selected' : '' }}>
                                    Draft
                                </option>
                            </select>
                        </div>
                    @else
                        <span class="status-pill {{ $ins->status == 'aktif' ? 'pill-aktif' : 'pill-draft' }}">
                            <i class="bi {{ $ins->status == 'aktif' ? 'bi-check-circle-fill' : 'bi-clock-fill' }}"></i>
                            {{ ucfirst($ins->status) }}
                        </span>
                    @endif

                    <div class="action-group">
                        {{-- Tombol Buka Config --}}
                        <button onclick="toggleConfig('{{ $ins->id }}')" class="btn-icon-premium bg-primary/5 text-primary/60 hover:bg-primary hover:text-white" title="Konfigurasi">
                            <i class="bi bi-gear-wide-connected" id="icon-{{ $ins->id }}"></i>
                        </button>
                        <button 
                            onclick="openEditInstansiModal({{ json_encode($ins) }}, '{{ route('admin.instansi.update', [$portalCode, $ins->id]) }}')" 
                            class="btn-icon-premium bg-accent/10 text-accent-dark hover:bg-accent hover:text-white" 
                            title="Edit Dasar">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        @if(Auth::user()->isSuperAdmin())
                            <button 
                                onclick="openDeleteInstansiModal({{ json_encode($ins) }}, '{{ route('admin.instansi.destroy', [$portalCode, $ins->id]) }}')" 
                                class="btn-icon-premium bg-red-50 text-red-400 hover:bg-red-600 hover:text-white" 
                                title="Hapus Instansi">
                                <i class="bi bi-trash3"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Laci Konfigurasi (Drawer) --}}
            <div class="config-drawer">
                <div class="p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                        <h5 class="text-[10px] font-black text-primary uppercase tracking-[0.2em]">Panel Kendali {{ $ins->nama }}</h5>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <!-- Link ke Visual Identity -->
                        <a href="{{ route('admin.instansi.visual', [$portalCode, $ins->id]) }}" class="mini-config-card group">
                            <i class="bi bi-palette text-2xl text-blue-500 mb-3 block"></i>
                            <span class="text-xs font-black text-primary-dark uppercase tracking-widest">Visual Identity</span>
                            <p class="text-[10px] text-slate-400 mt-1">Warna, Logo, Banner</p>
                        </a>
                        
                        <!-- Link ke Visual Layanan -->
                        <a href="{{ route('admin.instansi.visual-layanan', [$portalCode, $ins->id]) }}" class="mini-config-card group">
                            <i class="bi-window-stack text-2xl text-amber-500 mb-3 block"></i>
                            <span class="text-xs font-black text-primary-dark uppercase tracking-widest">Visual Layanan</span>
                            <p class="text-[10px] text-slate-400 mt-1">Warna, Logo, Banner</p>
                        </a>

                        <!-- Link ke Company Profile -->
                        <a href="{{ route('admin.instansi.company-profile', [$portalCode, $ins->id]) }}" class="mini-config-card group">
                            <i class="bi bi-file-earmark-person text-2xl text-emerald-500 mb-3 block"></i>
                            <span class="text-xs font-black text-primary-dark uppercase tracking-widest">Company Profile</span>
                            <p class="text-[10px] text-slate-400 mt-1">Deskripsi & Slogan</p>
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
    const itemsPerPage = 3;
    let currentPage = 1;
    let filteredItems = [];

    document.addEventListener('DOMContentLoaded', () => {
        filteredItems = [...document.querySelectorAll('.ins-container')];
        renderPage(1);
    });

    // FUNGSI RENDER HALAMAN DENGAN TRANSISI HALUS
    function renderPage(page) {
        currentPage = page;
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const itemsToShow = filteredItems.slice(start, end);

        // 1. Sembunyikan semua item lebih dulu
        document.querySelectorAll('.ins-container').forEach(el => {
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

        renderPagination();
    }

    function renderPagination() {
        const wrapper = document.getElementById('opPagination');
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);

        wrapper.innerHTML = '';

        if (totalPages <= 1) return;

        wrapper.innerHTML += `
            <button class="w-10 h-10 rounded-xl hover:bg-primary/10 ${currentPage == 1 ? 'opacity-40 cursor-not-allowed' : ''}"
                    ${currentPage == 1 ? 'disabled' : ''}
                    onclick="renderPage(${currentPage - 1})">
                <i class="bi bi-chevron-left"></i>
            </button>
        `;

        for (let i = 1; i <= totalPages; i++) {
            wrapper.innerHTML += `
                <button onclick="renderPage(${i})"
                        class="w-10 h-10 rounded-xl text-xs font-black transition-all
                        ${i === currentPage ? 'bg-primary text-white shadow-lg' : 'hover:bg-slate-100 text-slate-500'}">
                    ${i}
                </button>
            `;
        }

        wrapper.innerHTML += `
            <button class="w-10 h-10 rounded-xl hover:bg-primary/10 ${currentPage == totalPages ? 'opacity-40 cursor-not-allowed' : ''}"
                    ${currentPage == totalPages ? 'disabled' : ''}
                    onclick="renderPage(${currentPage + 1})">
                <i class="bi bi-chevron-right"></i>
            </button>
        `;
    }

    function toggleConfig(id) {
        const container = document.getElementById(`ins-${id}`);
        const icon = document.getElementById(`icon-${id}`);
        const active = container.classList.contains('active');

        document.querySelectorAll('.ins-container').forEach(item => {
            item.classList.remove('active');

            const itemIcon = item.querySelector('i');
            if (itemIcon)
                itemIcon.classList.replace('bi-chevron-up', 'bi-gear-wide-connected');
        });

        if (!active) {
            container.classList.add('active');
            icon.classList.replace('bi-gear-wide-connected', 'bi-chevron-up');
        }
    }

    document.getElementById('insSearch').addEventListener('input', function () {
        const keyword = this.value.toLowerCase();

        filteredItems = [...document.querySelectorAll('.ins-container')].filter(item =>
            item.querySelector('h4').textContent.toLowerCase().includes(keyword)
        );

        renderPage(1);
    });

    /**
     * FUNGSI UPDATE STATUS VIA DROPDOWN
     */
    function updateStatus(id, selectElement) {
        const newStatus = selectElement.value;
        
        // Update class secara instan untuk feedback visual
        if (newStatus === 'aktif') {
            selectElement.className = 'status-select status-active';
        } else {
            selectElement.className = 'status-select status-draft';
        }

        // Animasi kecil saat berubah
        selectElement.style.transform = 'scale(0.95)';
        setTimeout(() => {
            selectElement.style.transform = 'scale(1)';
        }, 100);

        // Placeholder untuk AJAX Update ke database
        
        fetch(`{{ route('admin.instansi.status', [$portalCode, '__ID__']) }}`.replace('__ID__', id), {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        }).then(response => {
            if(!response.ok) {
                alert('Gagal sinkronisasi ke server');
            }
        });
        

        console.log(`Status Instansi ${id} berhasil diubah ke: ${newStatus}`);
    }
</script>
@endpush

@endsection