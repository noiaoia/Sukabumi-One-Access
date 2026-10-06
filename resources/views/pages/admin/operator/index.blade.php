@extends('layouts.admin')

@section('title', 'Manajemen Operator')
@section('page-title', 'Otoritas Akses')
@section('page-subtitle', 'Kelola akun & pantau aktivitas operator portal.')
@section('page-icon', 'bi-shield-lock-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/intlTelInput.min.js"></script>

<style>
    /* Kartu operator: baris + laci log jadi SATU kontainer utuh (bukan overlap negative-margin) */
    .op-container {
        margin-bottom: 12px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 22px;
        overflow: hidden;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 14px rgba(10, 30, 51, 0.03);
    }

    .op-container:hover {
        border-color: rgba(232, 163, 61, 0.45);
    }

    .op-row {
        padding: 16px 20px;
    }

    /* SISTEM REVEAL: default-nya tombol aksi SELALU TERLIHAT.
       Efek sembunyi-lalu-muncul-saat-hover HANYA diaktifkan di perangkat
       yang benar-benar punya mouse (hover: hover) — supaya di HP/tablet
       (tidak ada hover) tombolnya tetap bisa diakses. */
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
        .op-row:hover .action-group {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }
    }

    /* Laci Log */
    .log-drawer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-top: 1px solid #f1f5f9;
    }

    .op-container.active .log-drawer {
        max-height: 420px;
    }

    .log-scroll-area {
        max-height: 300px;
        overflow-y: auto;
        padding: 22px 24px;
    }
    .log-scroll-area::-webkit-scrollbar { width: 4px; }
    .log-scroll-area::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }

    .log-item {
        border-left: 2px solid #f1f5f9;
        padding-left: 20px;
        position: relative;
        padding-bottom: 15px;
    }
    .log-item::before {
        content: '';
        position: absolute; left: -6px; top: 4px;
        width: 10px; height: 10px;
        background: #E8A33D; border-radius: 50%;
    }

    /* Avatar & Badge */
    .avatar-compact { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; flex-shrink: 0; }
    .btn-icon-premium { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; border: none; }
    .status-indicator { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
    .online-pulse { background: #10B981; box-shadow: 0 0 8px #10B981; animation: opPulse 2s infinite; }
    @keyframes opPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

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

    /* Status Offline (Tanpa animasi pulse) */
    .status-indicator.offline {
        background: #CBD5E1; /* Warna abu-abu */
        box-shadow: none;
        animation: none;
    }

    .text-offline {
        color: #94A3B8;
    }
</style>

<div class="max-w-6xl mx-auto space-y-6">

    {{-- Top Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white premium-shadow border border-slate-100 rounded-3xl p-5 mb-8">
        <div>
            <h3 class="text-lg font-black text-primary-dark leading-none">Manajemen Operator</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">
                {{ count($operators ?? []) }} super admin terdaftar
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative flex-1 sm:flex-initial">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="opSearch" placeholder="Cari nama..." class="bg-primary/5 border border-primary/10 rounded-full pl-10 pr-4 py-2.5 text-xs font-bold text-primary w-full sm:w-56 focus:border-accent focus:bg-white outline-none transition-all">
            </div>
            <a href="{{ route('admin.operator.create', $portalCode) }}" class="bg-primary text-white h-10 w-10 sm:w-auto sm:px-6 rounded-full flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all active:scale-95 flex-shrink-0">
                <i class="bi bi-person-plus-fill"></i> <span class="hidden sm:inline">Tambah</span>
            </a>
        </div>
    </div>

    <div id="opList">
        @forelse($operators as $op)
        <div class="op-container" id="op-{{ $op->id }}" data-status="{{ $op->isOnline() ? 'online' : 'offline' }}">
            <div class="op-row flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-3">
                {{-- Info Utama --}}
                <div class="flex items-center gap-4 min-w-0 flex-1">
                    <div class="avatar-compact" style="background-color: #123457">
                        {{ strtoupper(substr($op->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0 text-left">
                        <h4 class="text-sm font-black text-primary-dark truncate">{{ $op->name }}</h4>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter truncate">@ {{ $op->username }} &middot; {{ $op->email }}</p>
                    </div>
                </div>

                <div class="hidden lg:flex items-center px-8 border-l border-slate-50 ml-4 flex-1 text-left">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Email Kerja</span>
                        <span class="text-[11px] font-bold text-slate-500">{{ $op->email }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between sm:justify-end gap-4 sm:gap-6 flex-shrink-0">
                    <div class="flex items-center">
                        @if($op->isOnline())
                            <span class="status-indicator online-pulse"></span>
                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Online</span>
                        @else
                            <span class="status-indicator offline"></span>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Offline</span>
                                <span class="text-[8px] font-bold text-slate-300 uppercase tracking-tighter">
                                    {{ $op->last_seen ? $op->last_seen->diffForHumans() : 'Belum Login' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- ACTION GROUP --}}
                    <div class="action-group">
                        <button onclick="toggleLog('{{ $op->id }}')" class="btn-icon-premium bg-primary/5 text-primary/60 hover:bg-primary hover:text-white" title="Lihat Log">
                            <i class="bi bi-clock-history" id="icon-{{ $op->id }}"></i>
                        </button>

                        {{-- Tombol Edit --}}
                        <button type="button"
                                onclick="handleEditOp(this)"
                                data-op="{{ json_encode($op) }}"
                                data-url="{{ route('admin.operator.update', [$portalCode, $op->id]) }}"
                                class="btn-icon-premium bg-accent/10 text-accent-dark hover:bg-accent hover:text-white"
                                title="Edit Akun">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        {{-- Tombol Hapus --}}
                        <button type="button"
                                onclick="handleDeleteOp(this)"
                                data-op="{{ json_encode($op) }}"
                                data-url="{{ route('admin.operator.destroy', [$portalCode, $op->id]) }}"
                                class="btn-icon-premium bg-red-50 text-red-400 hover:bg-red-600 hover:text-white"
                                title="Hapus Akun">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="log-drawer">
                <div class="log-scroll-area">
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-[10px] font-black text-primary uppercase tracking-[0.2em]">Log Aktivitas Terakhir</span>
                    </div>

                    {{-- Data Log Asli dari Database --}}
                    @forelse($op->activities as $log)
                    <div class="log-item">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-black text-primary-dark">{{ $log->activity_name }}</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">{{ $log->description }}</p>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase italic">Belum ada aktivitas tercatat.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-[32px] p-20 text-center border border-dashed border-slate-200">
            <i class="bi bi-people text-4xl text-slate-200"></i>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-4">Tidak ada super admin terdaftar</p>
        </div>
        @endforelse

        <div id="opPagination" class="flex items-center justify-center gap-2 mt-8"></div>
    </div>
</div>

@push('scripts')
<script>
    const itemsPerPage = 3; 
    let currentPage = 1;
    let filteredElements = [];

    // --- FUNGSI SEARCH ---
    document.getElementById('opSearch').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        const allContainers = Array.from(document.querySelectorAll('.op-container'));
        
        filteredElements = allContainers.filter(container => {
            const name = container.querySelector('h4').textContent.toLowerCase();
            const username = container.querySelector('p').textContent.toLowerCase();
            return name.includes(query) || username.includes(query);
        });

        renderPage(1);
    });

    // --- FUNGSI TOGGLE LOG ---
    function toggleLog(id) {
        const container = document.getElementById('op-' + id);
        const icon = document.getElementById('icon-' + id);
        const isActive = container.classList.contains('active');

        document.querySelectorAll('.op-container').forEach(el => {
            if(el.id !== 'op-' + id) {
                el.classList.remove('active');
                const otherIcon = el.querySelector('.bi-clock-history'); // Reset icon lain
                if (otherIcon) otherIcon.className = 'bi bi-clock-history';
            }
        });

        if (!isActive) {
            container.classList.add('active');
            icon.className = 'bi bi-chevron-up';
        } else {
            container.classList.remove('active');
            icon.className = 'bi bi-clock-history';
        }
    }

    // --- FUNGSI PAGINASI ---
    function initPagination() {
        const allContainers = document.querySelectorAll('.op-container');
        filteredElements = Array.from(allContainers);
        renderPage(1);
    }

    function renderPage(page) {
        currentPage = page;
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const itemsToShow = filteredElements.slice(start, end);

        document.querySelectorAll('.op-container').forEach(el => {
            el.style.display = 'none';
            el.classList.remove('reveal-item');
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
        const wrapper = document.getElementById('opPagination');
        wrapper.innerHTML = '';
        if (totalPages <= 1) return;
        // Tombol Previous
        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
        prevBtn.className =
            `w-10 h-10 rounded-xl flex items-center justify-center transition-all ${
                currentPage === 1
                    ? 'text-slate-300 cursor-not-allowed'
                    : 'text-primary hover:bg-primary/10'
            }`;
        prevBtn.onclick = () => {
            if (currentPage > 1) {
                renderPage(currentPage - 1);
            }
        };
        wrapper.appendChild(prevBtn);
        // Tombol Nomor Halaman
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.innerText = i;
            btn.className =
                `w-10 h-10 rounded-xl text-xs font-black transition-all ${
                    i === currentPage
                        ? 'bg-primary text-white shadow-lg shadow-primary/30'
                        : 'text-slate-400 hover:bg-slate-100'
                }`;
            btn.onclick = () => renderPage(i);
            wrapper.appendChild(btn);
        }

        // Tombol Next
        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
        nextBtn.className =
            `w-10 h-10 rounded-xl flex items-center justify-center transition-all ${
                currentPage === totalPages
                    ? 'text-slate-300 cursor-not-allowed'
                    : 'text-primary hover:bg-primary/10'
            }`;

        nextBtn.onclick = () => {
            if (currentPage < totalPages) {
                renderPage(currentPage + 1);
            }
        };

        wrapper.appendChild(nextBtn);
    }

    document.addEventListener('DOMContentLoaded', initPagination);

    function handleDeleteOp(btn) { // Ganti nama di sini agar sesuai dengan tombol
        const data = JSON.parse(btn.getAttribute('data-op'));
        const url = btn.getAttribute('data-url');
        
        // Panggil fungsi modal
        openDeleteOperatorModal(data, url);
    }

    
</script>
@endpush

@endsection