@extends('layouts.admin')

@section('title', 'Manajemen Pengajuan - Pilih Instansi')
@section('page-title', 'Gudang Pengajuan')
@section('page-icon', 'bi-folder-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<style>
    /* 1. CONTAINER: Ukuran Identik (Radius 22px, Margin 12px) */
    .sub-list-container {
        margin-bottom: 12px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 22px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 14px rgba(10, 30, 51, 0.03);
    }

    /* HOVER: Accent Gold Focus */
    .sub-list-container:hover {
        border-color: rgba(232, 163, 61, 0.6);
        box-shadow: 0 8px 20px rgba(232, 163, 61, 0.1);
        transform: translateX(4px);
    }

    .sub-list-row { padding: 16px 24px; }

    /* 2. AVATAR: Ukuran Identik (42px) */
    .avatar-ins { 
        width: 44px; height: 44px; border-radius: 12px; 
        display: flex; align-items: center; justify-content: center; 
        font-weight: 800; color: #fff; flex-shrink: 0; 
        transition: transform 0.3s ease;
        font-size: 1.2rem;
    }
    .sub-list-container:hover .avatar-ins { transform: scale(1.1) rotate(3deg); }

    /* 3. ACTION GROUP REVEAL */
    .action-group {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    @media (hover: hover) and (pointer: fine) {
        .action-group {
            opacity: 0; visibility: hidden;
            transform: translateX(16px);
            transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .sub-list-row:hover .action-group { opacity: 1; visibility: visible; transform: translateX(0); }
    }

    /* 4. BUTTON: Premium Gold/Navy */
    .btn-open-folder {
        background: #123457;
        color: #ffffff;
        padding: 8px 20px;
        border-radius: 12px;
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

    .btn-open-folder:hover {
        background: #E8A33D;
        color: #0A1E33;
        box-shadow: 0 4px 12px rgba(232, 163, 61, 0.3);
    }

    .badge-status {
        font-size: 9px; 
        font-weight: 800; 
        text-transform: uppercase;
        letter-spacing: 1px; 
        padding: 5px 12px; 
        border-radius: 8px;
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sub-list-container:hover .badge-status {
        background: rgba(232, 163, 61, 0.1);
        color: #E8A33D;
        border-color: rgba(232, 163, 61, 0.2);
    }

    .pulse-notif {
        width: 6px; height: 6px; background: #ef4444; border-radius: 50%;
        animation: pulse-red 2s infinite;
    }
    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    @keyframes opFadeSlide { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    .reveal-item { animation: opFadeSlide 0.4s ease forwards; }
</style>

<div class="max-w-6xl mx-auto space-y-6">

    {{-- Top Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white premium-shadow border border-slate-100 rounded-3xl p-5 mb-8">
        <div>
            <h3 class="text-lg font-black text-primary-dark leading-none">Pilih Instansi</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">Terdapat {{ $instansiList->count() }} otoritas terdaftar</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative flex-1 sm:flex-initial">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="insSearch" placeholder="Cari instansi..." class="bg-primary/5 border border-primary/10 rounded-full pl-10 pr-4 py-2.5 text-xs font-bold text-primary w-full sm:w-56 focus:border-accent focus:bg-white outline-none transition-all">
            </div>
        </div>
    </div>

    <div id="insList">
        @foreach($instansiList as $ins)
        <div class="sub-list-container">
            <div class="sub-list-row flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-3">
                
                {{-- 1. INFO UTAMA (Kiri) --}}
                <div class="flex items-center gap-5 min-w-0 flex-1">
                    <div class="avatar-ins shadow-inner" style="background-color: {{ $ins->primary_color }}">
                        {{ strtoupper(substr($ins->nama, 0, 1)) }}
                    </div>
                    <div class="min-w-0 text-left">
                        <h4 class="text-sm font-black text-primary-dark truncate">{{ $ins->nama }}</h4>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter truncate">Total berkas: {{ $ins->total_pengajuan ?? 0 }} permohonan</p>
                    </div>
                </div>

                {{-- 2. STATUS BADGE (Tengah) --}}
                <div class="hidden lg:flex items-center px-8 border-l border-slate-50 ml-4 flex-1 text-left">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest leading-none mb-1.5">Antrean Baru</span>
                        <div class="badge-status w-fit">
                            @if($ins->total_pending > 0)
                                <span class="pulse-notif"></span>
                                <span class="text-red-500">{{ $ins->total_pending }} Masuk</span>
                            @else
                                <i class="bi bi-check2-all text-emerald-500"></i>
                                <span>Clear</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 3. ACTION (Kanan) --}}
                <div class="flex items-center justify-between sm:justify-end gap-4 sm:gap-6 flex-shrink-0">
                    {{-- Badge mobile only --}}
                    @if($ins->total_pending > 0)
                        <span class="lg:hidden bg-red-500 text-white text-[8px] font-black px-2 py-1 rounded-md">{{ $ins->total_pending }} BARU</span>
                    @endif

                    <div class="action-group">
                        <a href="{{ route('admin.submission.manage', [$portalCode, $ins->id]) }}" 
                           class="btn-open-folder">
                            Buka Arsip <i class="bi bi-arrow-right-short text-base"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        {{-- Paginasi Wrapper --}}
        <div id="insPagination" class="flex items-center justify-center gap-2 mt-8"></div>
    </div>
</div>

@push('scripts')
<script>
    // Konfigurasi Paginasi & Pencarian
    const itemsPerPage = 5; 
    let currentPage = 1;
    let filteredElements = [];

    function initPagination() {
        const allContainers = document.querySelectorAll('.sub-list-container');
        filteredElements = Array.from(allContainers);
        renderPage(1);
    }

    function renderPage(page) {
        currentPage = page;
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const itemsToShow = filteredElements.slice(start, end);

        document.querySelectorAll('.sub-list-container').forEach(el => {
            el.style.display = 'none';
            el.classList.remove('reveal-item');
            el.style.opacity = '0';
        });

        itemsToShow.forEach((el, index) => {
            el.style.display = 'block';
            setTimeout(() => {
                el.classList.add('reveal-item');
            }, index * 60); 
        });

        updatePaginationButtons();
    }

    function updatePaginationButtons() {
        const totalPages = Math.ceil(filteredElements.length / itemsPerPage);
        const wrapper = document.getElementById('insPagination');
        wrapper.innerHTML = '';

        if (totalPages <= 1) return;

        // Tombol logic
        const createBtn = (content, target, isDisabled, isActive = false) => {
            const btn = document.createElement('button');
            btn.innerHTML = content;
            btn.className = `w-10 h-10 rounded-xl text-xs font-black transition-all flex items-center justify-center 
                            ${isActive ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-100'} 
                            ${isDisabled ? 'opacity-30 cursor-not-allowed' : ''}`;
            if(!isDisabled) btn.onclick = () => renderPage(target);
            return btn;
        };

        wrapper.appendChild(createBtn('<i class="bi bi-chevron-left"></i>', currentPage - 1, currentPage === 1));
        for (let i = 1; i <= totalPages; i++) {
            wrapper.appendChild(createBtn(i, i, false, i === currentPage));
        }
        wrapper.appendChild(createBtn('<i class="bi bi-chevron-right"></i>', currentPage + 1, currentPage === totalPages));
    }

    // Live Search Logic
    document.getElementById('insSearch').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        const allContainers = document.querySelectorAll('.sub-list-container');
        
        filteredElements = Array.from(allContainers).filter(container => {
            const name = container.querySelector('h4').textContent.toLowerCase();
            return name.includes(query);
        });

        renderPage(1);
    });

    document.addEventListener('DOMContentLoaded', initPagination);
</script>
@endpush

@endsection