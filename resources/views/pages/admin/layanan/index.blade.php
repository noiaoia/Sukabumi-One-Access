@extends('layouts.admin')

@section('title', 'Daftar Layanan')
@section('page-title', 'Pilih Otoritas')
@section('page-icon', 'bi-grid-3x3-gap-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<style>
    /* 1. CONTAINER: Ukuran Identik dengan Instansi (Radius 22px, Margin 12px) */
    .svc-container {
        margin-bottom: 12px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 22px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 14px rgba(10, 30, 51, 0.03);
    }

    /* HOVER: Accent Gold Focus */
    .svc-container:hover {
        border-color: rgba(232, 163, 61, 0.6);
        box-shadow: 0 8px 20px rgba(232, 163, 61, 0.1);
        transform: translateX(4px); /* Efek geser sedikit saat hover */
    }

    /* ROW: Padding Identik (16px 20px) */
    .svc-row { padding: 16px 20px; }

    /* 2. AVATAR: Ukuran Identik (42px) */
    .avatar-ins { 
        width: 42px; height: 42px; border-radius: 12px; 
        display: flex; align-items: center; justify-content: center; 
        font-weight: 800; color: #fff; flex-shrink: 0; 
        transition: transform 0.3s ease;
    }
    .svc-container:hover .avatar-ins {
        transform: scale(1.1);
    }

    /* 3. ACTION GROUP REVEAL (DNA yang sama) */
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

    /* 4. BUTTON: Padat & Premium */
    .btn-manage-svc {
        background: #123457;
        color: #ffffff;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
        border: none;
        text-decoration: none !important;
    }

    .btn-manage-svc:hover {
        background: #E8A33D;
        color: #0A1E33;
    }

    .badge-count {
        font-size: 9px; 
        font-weight: 800; 
        text-transform: uppercase;
        letter-spacing: 1px; 
        padding: 4px 10px; 
        border-radius: 8px;
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #f1f5f9;
    }

    .svc-container:hover .badge-count {
        background: rgba(232, 163, 61, 0.1);
        color: #E8A33D;
        border-color: rgba(232, 163, 61, 0.2);
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

    {{-- Top Toolbar (Sama dengan Instansi) --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white premium-shadow border border-slate-100 rounded-3xl p-5 mb-8">
        <div>
            <h3 class="text-lg font-black text-primary-dark leading-none">Pilih Instansi</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">Kelola gerbang layanan tiap dinas</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative flex-1 sm:flex-initial">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="svcSearch" placeholder="Cari instansi..." class="bg-primary/5 border border-primary/10 rounded-full pl-10 pr-4 py-2.5 text-xs font-bold text-primary w-full focus:border-accent focus:bg-white outline-none transition-all">
            </div>
        </div>
    </div>

    <div id="svcList">
        @foreach($instansiList as $ins)
        <div class="svc-container">
            <div class="svc-row flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-3">
                
                {{-- Info Utama (Sizing identik dengan instansi) --}}
                <div class="flex items-center gap-4 min-w-0 flex-1">
                    <div class="avatar-ins" style="background-color: {{ $ins->primary_color }}">
                        {{ strtoupper(substr($ins->nama, 0, 1)) }}
                    </div>
                    <div class="min-w-0 text-left">
                        <h4 class="text-sm font-black text-primary-dark truncate">{{ $ins->nama }}</h4>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter truncate">Portal: /{{ $ins->slug }}</p>
                    </div>
                </div>

                {{-- Sisi Tengah (Sama dengan section warna identitas) --}}
                <div class="hidden lg:flex items-center px-8 border-l border-slate-50 ml-4 flex-1 text-left">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest leading-none mb-1.5">Database Link</span>
                        <span class="badge-count w-fit">{{ $ins->layanans->count() }} Layanan Terdaftar</span>
                    </div>
                </div>

                {{-- Sisi Kanan (Action Group) --}}
                <div class="flex items-center justify-between sm:justify-end gap-4 sm:gap-6 flex-shrink-0">
                    <span class="lg:hidden badge-count">{{ $ins->layanans->count() }} Link</span>

                    <div class="action-group">
                        <button 
                            onclick="openServiceTypeModal('{{ $ins->slug }}', '{{ $ins->nama }}', '{{ $portalCode }}')" 
                            class="btn-manage-svc">
                            Kelola <i class="bi bi-arrow-right-short text-base"></i>
                        </button>
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

    document.addEventListener('DOMContentLoaded', initPagination);
</script>
@endpush

@endsection