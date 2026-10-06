@extends('layouts.admin')

@section('title', 'Manajemen Pengajuan - ' . $instansi->nama)
@section('page-title', 'Daftar Pengajuan')
@section('page-icon', 'bi-file-earmark-person-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<style>
    /* 1. Container & Layout (Identik dengan Pengaduan) */
    .sub-container {
        margin-bottom: 12px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 22px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 14px rgba(10, 30, 51, 0.03);
    }

    .sub-container:hover {
        border-color: #E8A33D;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(10, 30, 51, 0.05);
    }

    .sub-row { 
        padding: 16px 20px; 
        cursor: default; 
    }

    /* 2. Action Group Reveal (Muncul saat Hover) */
    .action-group { display: flex; gap: 8px; flex-shrink: 0; }

    @media (hover: hover) and (pointer: fine) {
        .action-group {
            opacity: 0; visibility: hidden;
            transform: translateX(16px);
            transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .sub-row:hover .action-group { 
            opacity: 1; visibility: visible; transform: translateX(0); 
        }
    }

    /* 3. Button Styles Premium */
    .btn-action {
        width: 38px; height: 38px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: none; cursor: pointer;
    }
    .btn-action:hover { transform: scale(1.1); }
    .btn-action:active { transform: scale(0.95); }

    .btn-detail  { background: rgba(18, 52, 87, 0.05); color: #123457; } 
    .btn-detail:hover { background: #123457; color: #fff; }

    .btn-process { background: rgba(232, 163, 61, 0.10); color: #E8A33D; } 
    .btn-process:hover { background: #E8A33D; color: #fff; }

    .btn-done    { background: rgba(16, 185, 129, 0.08); color: #059669; }
    .btn-done:hover { background: #10B981; color: #fff; }

    .btn-reject  { background: #FEF2F2; color: #F87171; } 
    .btn-reject:hover { background: #DC2626; color: #fff; }

    .btn-revision { background: rgba(245, 158, 11, 0.1); color: #D97706; }
    .btn-revision:hover { background: #D97706; color: #fff; }

    /* Pill status perlu revisi */
    .pill-perbaikan { background: #fffbeb; color: #d97706; }

    /* 4. Status Pills */
    .status-pill {
        font-size: 10px; font-weight: 800; text-transform: uppercase;
        padding: 6px 12px; border-radius: 10px;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .pill-diajukan { background: #fff1f2; color: #e11d48; }
    .pill-review   { background: #eff6ff; color: #3b82f6; }
    .pill-diproses { background: #fefce8; color: #ca8a04; }
    .pill-selesai  { background: #f0fdf4; color: #16a34a; }
    .pill-ditolak  { background: #fef2f2; color: #dc2626; }

    .pulse-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; animation: subPulse 2s infinite; }
    @keyframes subPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

    /* 5. Utility & Filter */
    .filter-select {
        background: #F8FAFC; border: 1.5px solid #E2E8F0;
        border-radius: 12px; font-size: 11px; font-weight: 800;
        color: #123457; padding: 0 15px; height: 40px;
        outline: none; transition: all 0.3s;
    }
    .avatar-icon {
        width: 44px; height: 44px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
    }

    @keyframes subFadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .sub-reveal { animation: subFadeInUp 0.4s ease forwards; }
</style>

<div class="max-w-6xl mx-auto space-y-6">

    {{-- Top Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white premium-shadow border border-slate-100 rounded-3xl p-5 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.submission.index', $portalCode) }}" 
               class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-primary hover:text-white transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h3 class="text-lg font-black text-primary-dark leading-none">{{ $instansi->nama }}</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5" id="ticketCount">Memuat data...</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <select id="statusFilter" class="filter-select hidden sm:block" onchange="applyFilters()">
                <option value="all">SEMUA STATUS</option>
                <option value="diajukan">BARU MASUK</option>
                <option value="review">DITINJAU</option>
                <option value="diproses">SEDANG DIPROSES</option>
                <option value="selesai">SELESAI</option>
                <option value="ditolak">DITOLAK</option>
            </select>

            <div class="relative flex-1 sm:flex-initial">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="subSearch" onkeyup="applyFilters()" placeholder="Cari nama atau tiket..." class="bg-primary/5 border border-primary/10 rounded-full pl-10 pr-5 py-2.5 text-xs font-bold text-primary w-full sm:w-56 focus:border-accent focus:bg-white outline-none transition-all">
            </div>
        </div>
    </div>

    <div id="subList">
        @forelse($pengajuans as $item)
            @php
                $namaPemohon = $item->data->where('parameter', 'nama_lengkap')->first()->value ?? 'Warga';
                $nikPemohon  = $item->data->where('parameter', 'nik')->first()->value ?? '-';
            @endphp

            <div class="sub-container" id="sub-{{ $item->id }}" data-status="{{ $item->status }}" data-code="{{ strtolower($item->nomor_pengajuan) }}" data-name="{{ strtolower($namaPemohon) }}">
                <div class="sub-row flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    
                    {{-- 1. IDENTITAS (Kiri) --}}
                    <div class="flex items-center gap-4 min-w-0 flex-1">
                        <div class="avatar-icon" style="background: {{ $instansi->primary_color }}15; color: {{ $instansi->primary_color }}">
                            <i class="bi {{ $item->category->icon }}"></i>
                        </div>
                        <div class="min-w-0 text-left">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-[9px] font-black text-primary uppercase tracking-tighter">{{ $item->nomor_pengajuan }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-[9px] font-black text-accent uppercase tracking-tighter">{{ $item->category->nama }}</span>
                            </div>
                            <h4 class="text-sm font-black text-primary-dark truncate">{{ $namaPemohon }}</h4>
                            <p class="text-[10px] font-bold text-slate-400">NIK: {{ $nikPemohon }} &middot; {{ $item->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- 2. STATUS (Tengah) --}}
                    <div class="hidden lg:flex items-center px-8 border-l border-slate-50 ml-4 flex-1 text-left">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest leading-none mb-1.5">Status Berkas</span>
                            <div class="status-wrapper">
                                @if($item->status == 'diajukan')
                                    <span class="status-pill pill-diajukan"><span class="pulse-dot"></span> Baru</span>
                                @elseif($item->status == 'review')
                                    <span class="status-pill pill-review"><i class="bi bi-eye-fill"></i> Ditinjau</span>
                                @elseif($item->status == 'perlu_revisi')
                                    <span class="status-pill pill-perbaikan"><i class="bi bi-exclamation-triangle-fill"></i> Perlu Perbaikan</span>
                                @elseif($item->status == 'diproses')
                                    <span class="status-pill pill-diproses"><span class="pulse-dot"></span> Diproses</span>
                                @elseif($item->status == 'selesai')
                                    <span class="status-pill pill-selesai"><i class="bi bi-check-lg"></i> Selesai</span>
                                @else
                                    <span class="status-pill pill-ditolak">Ditolak</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 3. ACTION GROUP (Kanan) --}}
                    <div class="flex items-center justify-between sm:justify-end gap-3 flex-shrink-0">
                        <div class="action-group" id="action-group-{{ $item->id }}">
                            
                            {{-- 1. TOMBOL DETAIL/REVIEW: Selalu Ada --}}
                            <button onclick="handleReview('{{ $item->id }}', '{{ $item->status }}')" 
                                    class="btn-action btn-detail" 
                                    title="Tinjau Berkas">
                                <i class="bi bi-eye-fill"></i>
                            </button>

                            {{-- 2. AKSI TAHAP AWAL (Hanya muncul jika status REVIEW atau PERLU REVISI) --}}
                            <div id="next-actions-{{ $item->id }}" 
                                class="flex gap-2 {{ ($item->status == 'review' || $item->status == 'perlu_revisi') ? '' : 'hidden' }}">
                                
                                <button onclick="openRevisionModal('{{ $item->id }}', '{{ $item->nomor_pengajuan }}')" 
                                        class="btn-action btn-revision" 
                                        title="Minta Perbaikan (Revisi)">
                                    <i class="bi bi-exclamation-octagon-fill"></i>
                                </button>

                                <button onclick="markAsProcess('{{ $item->id }}')" 
                                        class="btn-action btn-process" 
                                        title="Lanjutkan ke Proses">
                                    <i class="bi bi-arrow-right-circle-fill"></i>
                                </button>
                            </div>

                            {{-- 3. AKSI TAHAP AKHIR (Hanya muncul jika status sedang DIPROSES) --}}
                            <div id="finish-actions-{{ $item->id }}" 
                                class="flex gap-2 {{ ($item->status == 'diproses') ? '' : 'hidden' }}">
                                
                                <button onclick="openFinishModal('{{ $item->id }}', '{{ $item->nomor_pengajuan }}')" 
                                        class="btn-action btn-done" 
                                        title="Tandai Selesai">
                                    <i class="bi bi-check-circle-fill"></i>
                                </button>
                                
                                <button onclick="openRejectModal('{{ $item->id }}', '{{ $item->nomor_pengajuan }}')" 
                                        class="btn-action btn-reject" 
                                        title="Tolak Pengajuan">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[32px] p-20 text-center border border-dashed border-slate-200">
                <i class="bi bi-folder2 text-4xl text-slate-200"></i>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-4">Belum ada berkas masuk</p>
            </div>
        @endforelse
        <div id="subPagination" class="flex items-center justify-center gap-2 mt-8"></div>
    </div>
</div>

<x-admin.modal-detail-submission :portalCode="$portalCode" />
<x-admin.modal-revision-submission :portalCode="$portalCode" />
<x-admin.modal-process-submission :portalCode="$portalCode" />
<x-admin.modal-finish-submission :portalCode="$portalCode" />
<x-admin.modal-reject-submission :portalCode="$portalCode" />
@endsection

@push('scripts')
<script>
    const itemsPerPage = 5; 
    let currentPage = 1;
    let filteredElements = [];

    // --- Pencarian & Paginasi (Tetap Sama) ---
    function applyFilters() {
        const searchQuery = document.getElementById('subSearch').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const allItems = Array.from(document.querySelectorAll('.sub-container'));

        filteredElements = allItems.filter(container => {
            const matchSearch = container.getAttribute('data-code').includes(searchQuery) || 
                                container.getAttribute('data-name').includes(searchQuery);
            const matchStatus = statusFilter === 'all' || 
                                container.getAttribute('data-status') === statusFilter;
            return matchSearch && matchStatus;
        });

        document.getElementById('ticketCount').textContent = `Total ${filteredElements.length} Berkas Ditemukan`;
        renderPage(1);
    }

    function renderPage(page) {
        currentPage = page;
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const toShow = filteredElements.slice(start, end);

        document.querySelectorAll('.sub-container').forEach(el => {
            el.style.display = 'none';
            el.classList.remove('sub-reveal');
        });

        toShow.forEach((el, index) => {
            el.style.display = 'block';
            setTimeout(() => el.classList.add('sub-reveal'), index * 60);
        });
        renderPaginationButtons();
    }

    function renderPaginationButtons() {
        const totalPages = Math.ceil(filteredElements.length / itemsPerPage);
        const wrapper = document.getElementById('subPagination');
        if(!wrapper || totalPages <= 1) { if(wrapper) wrapper.innerHTML = ''; return; }
        wrapper.innerHTML = '';
        const createBtn = (content, targetPage, active = false, disabled = false) => {
            const btn = document.createElement('button');
            btn.innerHTML = content;
            btn.className = `w-10 h-10 rounded-xl text-xs font-black transition-all flex items-center justify-center ${active ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:bg-slate-100'} ${disabled ? 'opacity-30 cursor-not-allowed' : ''}`;
            if (!disabled) btn.onclick = () => renderPage(targetPage);
            return btn;
        };
        wrapper.appendChild(createBtn('<i class="bi bi-chevron-left"></i>', currentPage - 1, false, currentPage === 1));
        for (let i = 1; i <= totalPages; i++) { wrapper.appendChild(createBtn(i, i, i === currentPage)); }
        wrapper.appendChild(createBtn('<i class="bi bi-chevron-right"></i>', currentPage + 1, false, currentPage === totalPages));
    }

    /**
     * ==========================================
     * LOGIK UTAMA (DIUPDATE)
     * ==========================================
     */

    function handleReview(id, currentStatus) {
        if(currentStatus === 'diajukan') {
            updateStatusSubmission(id, 'review', true); 
        } else {
            window.openDetailSubmission(id); 
        }
    }

    // --- BARU: Fungsi untuk memproses berkas (Bisa dipanggil dari modal) ---
    window.markAsProcess = function(id) {
        // Ambil data dari baris/container untuk ditampilkan di modal
        const container = document.getElementById(`sub-${id}`);
        const nomor = container.getAttribute('data-code').toUpperCase();
        const nama = container.getAttribute('data-name').split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');

        // Panggil fungsi buka modal dari component
        window.openProcessModal(id, nomor, nama);
    }

    // --- BARU: Fungsi untuk membuka Modal Revisi (Minta Perbaikan) ---
    window.openRevisionModal = function(id, nomor) {
        const modal = document.getElementById('modalRevision');
        const form = document.getElementById('formRevision');
        const portal = '{{ $portalCode }}';
        
        // Isi nomor berkas di header modal
        const nomorEl = document.getElementById('rev-nomor-text');
        if(nomorEl) nomorEl.innerText = nomor;

        // Set URL Action Form secara dinamis sesuai Rute Laravel
        form.action = `/${portal}/submissions/${id}/status`;
        
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Focus ke textarea setelah animasi terbuka
        setTimeout(() => {
            document.getElementById('catatan_revisi').focus();
        }, 300);
    }

    // --- BARU: Fungsi menutup Modal Revisi ---
    window.closeRevisionModal = function() {
        const modal = document.getElementById('modalRevision');
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
        document.getElementById('formRevision').reset();
    }

    function updateStatusSubmission(id, status, openModalAfter = false) {
        const portal = '{{ $portalCode }}';
        const targetUrl = `/${portal}/submissions/${id}/status`;

        fetch(targetUrl, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        }).then(async res => { // Tambahkan async untuk membaca pesan error
            if(res.ok) {
                const container = document.getElementById(`sub-${id}`);
                const nextActions = document.getElementById(`next-actions-${id}`);
                const finishActions = document.getElementById(`finish-actions-${id}`);
                const sw = container.querySelector('.status-wrapper');

                if(container) {
                    container.setAttribute('data-status', status);

                    if (status === 'review') {
                        sw.innerHTML = `<span class="status-pill pill-review"><i class="bi bi-eye-fill"></i> Ditinjau</span>`;
                        if(nextActions) nextActions.classList.remove('hidden'); 
                        if(finishActions) finishActions.classList.add('hidden');  
                    } 
                    else if (status === 'diproses') {
                        sw.innerHTML = `<span class="status-pill pill-diproses"><span class="pulse-dot"></span> Diproses</span>`;
                        if(nextActions) nextActions.classList.add('hidden');
                        if(finishActions) finishActions.classList.remove('hidden');

                        // --- BARU: Tutup modal proses jika sedang terbuka ---
                        if(typeof window.closeProcessModal === 'function') {
                            window.closeProcessModal();
                        }
                    }
                    else if (status === 'selesai' || status === 'ditolak' || status === 'perlu_revisi') {
                        // Untuk status final atau revisi, reload agar data sinkron dengan filter
                        location.reload();
                        return;
                    }
                }

                if(openModalAfter) {
                    window.openDetailSubmission(id);
                }
            } else {
                // --- BARU: Jika Server Error (Misal status 422 atau 500) ---
                const errorData = await res.json();
                alert("Gagal: " + (errorData.message || "Terjadi kesalahan sistem."));
                resetLoadingButton();
            }
        }).catch(err => {
            console.error(err);
            alert("Terjadi kesalahan koneksi.");
            resetLoadingButton();
        });
    }

    // Helper untuk mengembalikan tombol jika gagal
    function resetLoadingButton() {
        const btn = document.getElementById('btnConfirmProcess');
        if(btn) {
            btn.disabled = false;
            btn.innerHTML = 'Konfirmasi & Proses';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        applyFilters();
    });
</script>
@endpush