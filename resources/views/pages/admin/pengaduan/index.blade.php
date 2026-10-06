@extends('layouts.admin')

@section('title', 'Manajemen Pengaduan')
@section('page-title', 'Pengaduan Warga')
@section('page-icon', 'bi-megaphone-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<style>
    /* 1. Container & Layout (Identik dengan Operator) */
    .cm-container {
        margin-bottom: 12px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 22px;
        overflow: hidden;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 14px rgba(10, 30, 51, 0.03);
    }

    .cm-container:hover {
        border-color: rgba(232, 163, 61, 0.45);
    }

    /* PERBAIKAN: Kursor default agar user tahu card tidak bisa diklik langsung */
    .cm-row { 
        padding: 16px 20px; 
        cursor: default; 
    }

    /* 2. Action Group Reveal */
    .action-group { display: flex; gap: 8px; flex-shrink: 0; }

    @media (hover: hover) and (pointer: fine) {
        .action-group {
            opacity: 0; visibility: hidden;
            transform: translateX(16px);
            transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .cm-row:hover .action-group { opacity: 1; visibility: visible; transform: translateX(0); }
    }

    /* 3. Laci Detail (Drawer) */
    .detail-drawer {
        max-height: 0; overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-top: 1px solid #f1f5f9; background: #fcfcfd;
    }
    .cm-container.active .detail-drawer { max-height: 800px; }
    .detail-content { padding: 24px; }

    /* 4. Filter Component Styling */
    .filter-select {
        background: #F8FAFC; border: 1.5px solid #E2E8F0;
        border-radius: 12px; font-size: 11px; font-weight: 800;
        color: #123457; padding: 0 15px; height: 40px;
        outline: none; transition: all 0.3s;
    }
    .filter-select:focus { border-color: #E8A33D; background: #fff; }

    /* 5. UI Elements */
    .category-box{
        width:42px;
        height:42px;
        border-radius:12px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.2rem;
        flex-shrink:0;

        background-color: var(--cat-bg);
        color: var(--cat-color);
    }
    .btn-icon-premium { 
        width: 36px; height: 36px; border-radius: 10px; 
        display: flex; align-items: center; justify-content: center; 
        transition: all 0.2s; border: none; cursor: pointer;
    }
    .status-pill {
        font-size: 9px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 1px; padding: 4px 10px; border-radius: 8px;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .pill-pending { background: #fff1f2; color: #e11d48; }
    .pill-process { background: #fefce8; color: #ca8a04; }
    .pill-done { background: #f0fdf4; color: #16a34a; }
    .pulse-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; animation: opPulse 2s infinite; }

    .pill-review { background: #eff6ff; color: #3b82f6; } /* Warna Biru Soft */
    .pill-rejected{
        background:#fef2f2;
        color:#dc2626;
    }

    /* Animasi muncul satu per satu */
    @keyframes cmFadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .cm-reveal {
        animation: cmFadeInUp 0.4s ease forwards;
    }

    /* Container list agar stabil saat pindah halaman */
    #cmList {
        min-height: 400px;
    }

    /* Style Tombol Paginasi Premium */
    .btn-page {
        width: 40px; height: 40px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 800; transition: all 0.3s ease;
    }
    .btn-page.active {
        background: #123457; color: white;
        box-shadow: 0 10px 20px -5px rgba(18, 52, 87, 0.3);
        transform: translateY(-2px);
    }
    .btn-page:not(.active):not(:disabled):hover {
        background: #f1f5f9; color: #E8A33D;
    }

    .cm-container {
        margin-bottom: 12px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 20px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(10, 30, 51, 0.03);
    }

    .cm-container:hover {
        border-color: #E8A33D;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(10, 30, 51, 0.05);
    }

    .cm-row { padding: 14px 20px; }

    /* Action Group Styling */
    .action-group { 
        display: flex; 
        gap: 6px; 
        align-items: center;
    }

    /* Base Button Style */
    .btn-action {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        cursor: pointer;
        position: relative;
    }

    .btn-action:hover { transform: scale(1.1); }
    .btn-action:active { transform: scale(0.95); }

    /* Variants */
    /* 1. Tombol Detail (Soft Navy) */
    .btn-detail { background: rgba(18, 52, 87, 0.05); color: #123457; }
    .btn-detail:hover { background: #123457; color: #ffffff; }

    /* 2. Tombol Proses (Soft Gold) */
    .btn-process { background: rgba(232, 163, 61, 0.10); color: #E8A33D; }
    .btn-process:hover { background: #E8A33D; color: #ffffff; }

    /* 3. Tombol Selesai (Soft Emerald) */
    .btn-done { background: rgba(16, 185, 129, 0.08); color: #059669; }
    .btn-done:hover { background: #10B981; color: #ffffff; }

    /* 4. Tombol Tolak (Soft Rose) */
    .btn-reject { background: #FEF2F2; color: #F87171; }
    .btn-reject:hover { background: #DC2626; color: #ffffff; }

    /* Status Pill (Sama seperti sebelumnya tapi lebih compact) */
    .status-pill {
        font-size: 10px; font-weight: 800; text-transform: uppercase;
        padding: 6px 12px; border-radius: 10px;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .pill-pending { background: #fff1f2; color: #e11d48; }
    .pill-review  { background: #eff6ff; color: #3b82f6; }
    .pill-process { background: #fefce8; color: #ca8a04; }
    .pill-done    { background: #f0fdf4; color: #16a34a; }
    .pill-rejected{ background: #fef2f2; color: #dc2626; }

    .category-box {
        width: 44px; height: 44px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        background: var(--cat-bg); color: var(--cat-color);
        font-size: 1.2rem;
    }
</style>

<div class="max-w-6xl mx-auto space-y-6">

    {{-- Top Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white premium-shadow border border-slate-100 rounded-3xl p-5 mb-8">
        <div>
            <h3 class="text-lg font-black text-primary-dark leading-none">Pusat Pengaduan</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5" id="ticketCount">
                Memuat data...
            </p>
        </div>

        <div class="flex items-center gap-3">
            <select id="statusFilter" class="filter-select hidden sm:block" onchange="applyFilters()">
                <option value="all">SEMUA STATUS</option>

                @if(Auth::user()->role === 'super_admin')
                    <option value="pending">BARU MASUK</option>
                    <option value="review">SEDANG DITINJAU</option>
                    <option value="process">DIPROSES</option>
                    <option value="done">SELESAI</option>
                    <option value="rejected">DITOLAK</option>
                @elseif(Auth::user()->role === 'admin_dinas')
                    <option value="process">DIPROSES</option>
                @endif
            </select>

            <div class="relative flex-1 sm:flex-initial">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="cmSearch" onkeyup="applyFilters()" placeholder="Cari kode atau judul..." class="bg-primary/5 border border-primary/10 rounded-full pl-10 pr-4 py-2.5 text-xs font-bold text-primary w-full sm:w-56 focus:border-accent focus:bg-white outline-none transition-all">
            </div>
        </div>
    </div>

    <div id="cmList">
        @forelse($complaints as $cm)
            @php
                $catMap = [
                    'Infrastruktur' => ['icon' => 'bi-cone-striped', 'color' => '#E8A33D'],
                    'Kesehatan'     => ['icon' => 'bi-heart-pulse', 'color' => '#EF4444'],
                    'Pelayanan'     => ['icon' => 'bi-person-badge', 'color' => '#123457'],
                    'Lingkungan'    => ['icon' => 'bi-trash3', 'color' => '#123457'],
                    'Lainnya'       => ['icon' => 'bi-grid', 'color' => '#64748B']
                ];
                $style = $catMap[$cm->category] ?? $catMap['Lainnya'];
                $catIcon = $style['icon'];
                $catColor = $style['color'];
                $catBgColor = $catColor . '15';
            @endphp

            {{-- Ganti bagian ini di loop forelse Anda --}}
            <div class="cm-container" id="cm-{{ $cm->id }}" data-status="{{ $cm->status }}" data-title="{{ strtolower($cm->title) }}" data-code="{{ strtolower($cm->ticket_id) }}">
                <div class="cm-row flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-3">
                    
                    {{-- 1. INFO UTAMA (Kiri) --}}
                    <div class="flex items-center gap-4 min-w-0 flex-1">
                        <div class="category-box" style=" --cat-bg: {{ $catBgColor }}; --cat-color: {{ $catColor }}; ">
                            <i class="bi {{ $catIcon }}"></i>
                        </div>
                        <div class="min-w-0 text-left">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">{{ $cm->ticket_id }}</span>
                                <span class="text-[9px] font-bold text-slate-300">&bull;</span>
                                <span class="text-[9px] font-black text-accent uppercase tracking-tighter">{{ $cm->category }}</span>
                            </div>
                            <h4 class="text-sm font-black text-primary-dark truncate">{{ $cm->title }}</h4>
                            <p class="text-[10px] font-bold text-slate-400">Oleh: Warga &middot; {{ $cm->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- 2. STATUS PILL (Tengah) --}}
                    <div class="hidden lg:flex items-center px-8 border-l border-slate-50 ml-4 flex-1 text-left">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest leading-none mb-1">Status Laporan</span>
                            <div class="status-wrapper">
                                @if($cm->status == 'pending')
                                    <span class="status-pill pill-pending"><span class="pulse-dot"></span> Baru Masuk</span>
                                @elseif($cm->status == 'review')
                                    <span class="status-pill pill-review"><i class="bi bi-eye-fill"></i> Sedang Ditinjau</span>
                                @elseif($cm->status == 'process')
                                    <span class="status-pill pill-process"><span class="pulse-dot"></span> Diproses</span>
                                @elseif($cm->status == 'done')
                                    <span class="status-pill pill-done"><i class="bi bi-check-lg"></i> Selesai</span>
                                @else
                                    <span class="status-pill pill-rejected">Ditolak</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 3. DYNAMIC ACTION GROUP (Kanan) --}}
                    <div class="flex items-center justify-between sm:justify-end gap-3 flex-shrink-0">
                        <div class="action-group">

                            {{-- LIHAT DETAIL --}}
                            <button onclick="openDetailModal('{{ $cm->id }}')" class="btn-action btn-detail" title="Lihat Detail Laporan">
                                <i class="bi bi-eye-fill"></i>
                            </button>

                            {{-- ================================================= --}}
                            {{-- SUPER ADMIN --}}
                            {{-- ================================================= --}}

                            @if(Auth::user()->role === 'super_admin')

                                {{-- Review → Teruskan ke Instansi + Tolak --}}
                                @if($cm->status === 'review')
                                    <button onclick="openProcessModal('{{ $cm->id }}')" class="btn-action btn-process" title="Teruskan ke Instansi">
                                        <i class="bi bi-send-fill"></i>
                                    </button>

                                    <button onclick="openRejectModal('{{ $cm->id }}')" class="btn-action btn-reject" title="Tolak Pengaduan">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                @endif

                                {{-- Process → Setujui + Tolak --}}
                                @if($cm->status === 'process')
                                    <button onclick="openFinishModal('{{ $cm->id }}')" class="btn-action btn-done" title="Setujui Pengaduan">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </button>

                                    <button onclick="openRejectModal('{{ $cm->id }}')" class="btn-action btn-reject" title="Tolak Pengaduan">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                @endif

                            @endif

                            {{-- ================================================= --}}
                            {{-- ADMIN DINAS --}}
                            {{-- ================================================= --}}

                            @if(in_array(Auth::user()->role, ['admin_dinas', 'operator']))

                                {{-- Process → Setujui + Tolak --}}
                                @if($cm->status === 'process')
                                    <button onclick="openFinishModal('{{ $cm->id }}')" class="btn-action btn-done" title="Setujui Pengaduan">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </button>

                                    <button onclick="openRejectModal('{{ $cm->id }}')" class="btn-action btn-reject" title="Tolak Pengaduan">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                @endif

                            @endif

                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[32px] p-20 text-center border border-dashed border-slate-200">
                <i class="bi bi-megaphone text-4xl text-slate-200"></i>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-4">Tidak ada pengaduan masuk</p>
            </div>
        @endforelse
        <div id="cmPagination" class="flex items-center justify-center gap-2 mt-8"></div>
    </div>
</div>

<x-admin.modal-detail-pdf :portal-code="$portalCode" />
<x-admin.modal-process-complaint :portal-code="$portalCode" :instansi-list="$instansiList" />
<x-admin.modal-finish-complaint :portalCode="$portalCode" />
<x-admin.modal-reject-complaint :portalCode="$portalCode" />

@push('scripts')
<script>
    /**
     * ==========================================
     * 1. KONFIGURASI PAGINASI & FILTER
     * ==========================================
     */
    const itemsPerPage = 5; 
    let currentPage = 1;
    let filteredElements = [];

    function applyFilters() {
        const searchQuery = document.getElementById('cmSearch').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const allItems = Array.from(document.querySelectorAll('.cm-container'));

        filteredElements = allItems.filter(container => {
            const matchSearch = container.getAttribute('data-title').includes(searchQuery) || 
                                container.getAttribute('data-code').includes(searchQuery);
            const matchStatus = statusFilter === 'all' || 
                                container.getAttribute('data-status') === statusFilter;
            return matchSearch && matchStatus;
        });

        const countEl = document.getElementById('ticketCount');
        if(countEl) countEl.textContent = `Total ${filteredElements.length} Laporan Ditemukan`;
        
        renderPage(1);
    }

    function renderPage(page) {
        currentPage = page;
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const toShow = filteredElements.slice(start, end);

        document.querySelectorAll('.cm-container').forEach(el => {
            el.style.display = 'none';
            el.classList.remove('cm-reveal');
        });

        toShow.forEach((el, index) => {
            el.style.display = 'block';
            setTimeout(() => el.classList.add('cm-reveal'), index * 60);
        });

        renderPaginationButtons();
    }

    function renderPaginationButtons() {
        const totalPages = Math.ceil(filteredElements.length / itemsPerPage);
        const wrapper = document.getElementById('cmPagination');
        if(!wrapper) return;
        wrapper.innerHTML = '';

        if (totalPages <= 1) return;

        const createBtn = (content, targetPage, active = false, disabled = false) => {
            const btn = document.createElement('button');
            btn.innerHTML = content;
            btn.className = `w-10 h-10 rounded-xl text-xs font-black transition-all flex items-center justify-center 
                ${active ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-100'} 
                ${disabled ? 'opacity-30 cursor-not-allowed' : ''}`;
            if (!disabled) btn.onclick = () => renderPage(targetPage);
            return btn;
        };

        wrapper.appendChild(createBtn('<i class="bi bi-chevron-left"></i>', currentPage - 1, false, currentPage === 1));
        for (let i = 1; i <= totalPages; i++) {
            wrapper.appendChild(createBtn(i, i, i === currentPage));
        }
        wrapper.appendChild(createBtn('<i class="bi bi-chevron-right"></i>', currentPage + 1, false, currentPage === totalPages));
    }

    /**
     * ==========================================
     * 2. LOGIKA PEMANGGILAN MODAL (FINISH & REJECT)
     * ==========================================
     */
    
    // Fungsi Membuka Modal Selesai
    window.openFinishModal = function(id) {
        if (typeof window.openFinishModalLogic === 'function') {
            window.openFinishModalLogic(id);
        } else {
            // Fallback jika logic di component belum siap
            const modal = document.getElementById('modalFinishComplaint');
            const form = document.getElementById('formFinishComplaint');
            const portal = '{{ $portalCode }}';
            
            // Ambil ticket ID dari atribut data-code di container
            const container = document.getElementById(`cm-${id}`);
            const ticketId = container ? container.getAttribute('data-code').toUpperCase() : '---';
            
            document.getElementById('fin-ticket-text').innerText = ticketId;
            form.action = `/${portal}/pengaduan/${id}/update`;
            
            modal.style.display = 'flex';
            modal.classList.remove('is-closing');
            setTimeout(() => modal.classList.add('is-active'), 10);
            document.body.style.overflow = 'hidden';
        }
    };

    // Fungsi Membuka Modal Tolak
    window.openRejectModal = function(id) {
        const modal = document.getElementById('modalRejectComplaint');
        const form = document.getElementById('formRejectComplaint');
        const portal = '{{ $portalCode }}';
        
        const container = document.getElementById(`cm-${id}`);
        const ticketId = container ? container.getAttribute('data-code').toUpperCase() : '---';
        
        document.getElementById('rej-ticket-text').innerText = ticketId;
        form.action = `/${portal}/pengaduan/${id}/update`;
        
        modal.style.display = 'flex';
        modal.classList.remove('is-closing');
        setTimeout(() => modal.classList.add('is-active'), 10);
        document.body.style.overflow = 'hidden';
    };

    /**
     * ==========================================
     * 3. INITIALIZATION
     * ==========================================
     */
    document.addEventListener('DOMContentLoaded', () => {
        applyFilters();

        // Form Loading handling
        document.querySelectorAll('.complaintForm').forEach(form => {
            form.addEventListener('submit', function() {

                // Cari tombol submit yang terhubung dengan form ini
                const btn = document.querySelector(
                    `button[type="submit"][form="${this.id}"].complaintSubmitBtn`
                );

                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `
                        <i class="bi bi-arrow-repeat animate-spin mr-2"></i>
                        MEMPROSES...
                    `;
                    btn.style.opacity = '0.8';
                    btn.style.cursor = 'not-allowed';
                    btn.style.pointerEvents = 'none';
                }
            });
        });
    });
</script>
@endpush

@endsection