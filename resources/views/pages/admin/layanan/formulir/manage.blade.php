@extends('layouts.admin')

@section('title', 'Manajemen Formulir Mandiri')
@section('page-title', 'Manajemen Formulir')
@section('page-icon', 'bi-file-earmark-text-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<style>
    /* 1. CONTAINER LAYANAN */
    .avatar-svc {
        background-color: var(--svc-color);
    }
    .svc-container {
        margin-bottom: 12px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 22px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 14px rgba(10, 30, 51, 0.03);
    }
    .svc-container:hover { border-color: rgba(16, 185, 129, 0.45); }
    .svc-row { padding: 16px 20px; }

    /* Laci Konfigurasi (Drawer) */
    .config-drawer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        background: #F8FAFC;
        border-top: 1px solid #f1f5f9;
    }
    .svc-container.active .config-drawer { max-height: 500px; }

    /* 2. VISUAL BANNER STYLE */
    .visual-banner-card {
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-radius: 24px;
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .visual-banner-card::after {
        content: '';
        position: absolute;
        top: -50px; right: -50px;
        width: 150px; height: 150px;
        background: rgba(232, 163, 61, 0.04);
        border-radius: 50%;
        z-index: 1;
    }

    .banner-info { display: flex; align-items: center; gap: 20px; z-index: 2; }
    
    .banner-icon-wrapper {
        width: 64px; height: 64px; border-radius: 18px;
        background: rgba(232, 163, 61, 0.1);
        color: #E8A33D;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
    }

    .banner-text h4 { font-size: 14px; font-weight: 800; color: #123457; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
    .banner-text p { font-size: 11px; color: #94a3b8; font-weight: 500; margin: 0; }

    .btn-customize-visual {
        background: #123457;
        color: #fff !important;
        padding: 12px 24px;
        border-radius: 14px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        z-index: 2;
        text-decoration: none !important;
    }

    .btn-customize-visual:hover {
        background: #E8A33D;
        color: #0A1E33 !important;
        transform: translateX(5px);
    }

    /* Action Buttons Group */
    .action-group { display: flex; gap: 8px; flex-shrink: 0; }
    
    @media (hover: hover) and (pointer: fine) {
        .action-group { opacity: 0; visibility: hidden; transform: translateX(16px); transition: all 0.3s; }
        .svc-row:hover .action-group { opacity: 1; visibility: visible; transform: translateX(0); }
    }

    /* Action Icon Base */
    .btn-action-icon {
        width: 38px; height: 38px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        border: none; transition: all 0.2s ease; cursor: pointer;
        font-size: 1.1rem;
    }

    /* Small Tags & Avatar */
    .avatar-svc { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; flex-shrink: 0; }
    .status-pill { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; padding: 4px 10px; border-radius: 8px; }
    .type-tag { font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; padding: 2px 8px; border-radius: 5px; margin-top: 4px; display: inline-block; background: rgba(16, 185, 129, 0.1); color: #10B981; }

    @media (max-width: 640px) {
        .visual-banner-card { flex-direction: column; text-align: center; }
        .banner-info { flex-direction: column; gap: 12px; }
        .btn-customize-visual { width: 100%; justify-content: center; }
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

/* Style khusus Dropdown Status Formulir */
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
        content: '\F288'; /* Icon chevron-down */
        font-family: "bootstrap-icons";
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 8px;
        pointer-events: none;
        opacity: 0.6;
    }

    /* Warna khusus State Aktif & Draft */
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
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.1);
    }
</style>

<div class="max-w-6xl mx-auto space-y-6">

    {{-- Top Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white premium-shadow border border-slate-100 rounded-3xl p-5 mb-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.layanan.index', $portalCode) }}" class="w-9 h-9 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-white transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h3 class="text-lg font-black text-primary-dark leading-none">Daftar Formulir {{ $instansi['nama'] }}</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">Kelola sistem pengajuan mandiri warga</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative flex-1 sm:flex-initial">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="svcSearch" placeholder="Cari formulir..." class="bg-emerald-50/30 border border-emerald-100 rounded-full pl-10 pr-4 py-2.5 text-xs font-bold text-emerald-700 w-full sm:w-56 focus:border-emerald-500 focus:bg-white outline-none transition-all">
            </div>
            <a href="{{ route('admin.layanan.form.create', [$portalCode, 'instansi' => $instansi['slug'], 'type' => 'form']) }}" 
            class="bg-emerald-500 text-white h-10 px-6 rounded-full flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20 hover:bg-emerald-600 transition-all active:scale-95 flex-shrink-0">
                <i class="bi bi-plus-lg"></i> <span class="hidden sm:inline">Tambah Layanan</span>
            </a>
        </div>
    </div>

    <div id="svcList">
        @forelse($layananList as $svc)
        <div class="svc-container" id="svc-{{ $svc->id }}">
            <div class="svc-row flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-3">
                
                <div class="flex items-center gap-4 min-w-0 flex-1">
                    <div class="avatar-svc" style="--svc-color: {{ $svc->primary_color ?? '#10B981' }};">
                        <i class="bi {{ $svc->icon ?? 'bi-file-earmark-text' }}"></i>
                    </div>
                    <div class="min-w-0 text-left">
                        <h4 class="text-sm font-black text-primary-dark truncate">{{ $svc->nama }}</h4>
                        <span class="type-tag">Formulir Mandiri</span>
                    </div>
                </div>

                <div class="flex items-center justify-between sm:justify-end gap-4 sm:gap-6 flex-shrink-0">
                    <div class="status-select-wrapper">
                        <select 
                            data-url="{{ route('admin.layanan.form.status', [$portalCode, $svc->id]) }}"
                            onchange="updateFormStatus('{{ $svc->id }}', this)"
                            class="status-select {{ $svc->status === 'aktif' ? 'status-active' : 'status-draft' }}">
                            <option value="aktif" {{ $svc->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="draft" {{ $svc->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>

                    <div class="action-group">
                        <!-- 1. MANAGE BUTTON (BARU) -->
                        <a href="{{ route('admin.layanan.form.categories.index', [$portalCode, $svc->id]) }}" 
                        class="btn-action-icon bg-primary/5 text-primary hover:bg-primary hover:text-white" 
                        title="Kelola Kategori">
                            <i class="bi bi-kanban"></i>
                        </a>

                        <!-- 2. CONFIG TOGGLE (GEAR) -->
                        <button onclick="toggleSvcConfig('{{ $svc->id }}')" 
                                class="btn-action-icon bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white" title="Konfigurasi Visual">
                            <i class="bi bi-gear-wide-connected" id="icon-{{ $svc->id }}"></i>
                        </button>

                        <!-- 3. EDIT -->
                        <button 
                            type="button"
                            data-json="{{ json_encode($svc) }}"
                            data-url="{{ route('admin.layanan.form.update', [$portalCode, $svc->id]) }}"
                            onclick="openEditFormModal(this)"
                            class="btn-action-icon bg-accent/10 text-accent-dark hover:bg-accent hover:text-white" 
                            title="Edit Data Dasar">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <!-- 4. DELETE -->
                        <button 
                            type="button"
                            data-json="{{ json_encode($svc) }}"
                            data-url="{{ route('admin.layanan.form.destroy', [$portalCode, $svc->id]) }}"
                            onclick="openDeleteFormModal(JSON.parse(this.getAttribute('data-json')), this.getAttribute('data-url'))"
                            class="btn-action-icon bg-rose-50 text-rose-400 hover:bg-rose-600 hover:text-white" 
                            title="Hapus">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Laci Konfigurasi (Model Banner) --}}
            <div class="config-drawer">
                <div class="p-8">
                    <div class="visual-banner-card shadow-sm">
                        <div class="banner-info">
                            <div class="banner-icon-wrapper">
                                <i class="bi bi-card-image"></i>
                            </div>
                            <div class="banner-text">
                                <h4>Identity & Branding</h4>
                                <p>Kelola bagaimana formulir <b>{{ $svc->nama }}</b> muncul di portal publik (Ikon, Thumbnail, Deskripsi).</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.layanan.form.visual', [$portalCode, $svc->id]) }}" class="btn-customize-visual">
                            Kustomisasi Visual <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-200">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <i class="bi bi-file-earmark-plus text-2xl"></i>
                </div>
                <h5 class="text-sm font-bold text-slate-400">Belum ada Layanan</h5>
                <p class="text-[11px] text-slate-300">Klik tombol Tambah Layanan untuk memulai.</p>
            </div>
        @endforelse
        <div id="opPagination" class="flex items-center justify-center gap-2 mt-8">
        </div>
    </div>
</div>

<x-admin.modal-delete-form />

@push('scripts')
<script>
    // --- KONFIGURASI PAGINASI (TETAP) ---
    const itemsPerPage = 3; 
    let currentPage = 1;
    let filteredElements = [];

    // Inisialisasi awal
    function initPagination() {
        const allContainers = document.querySelectorAll('.svc-container');
        filteredElements = Array.from(allContainers);
        renderPage(1);
    }

    // FUNGSI RENDER HALAMAN DENGAN TRANSISI HALUS (TETAP)
    function renderPage(page) {
        currentPage = page;
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const itemsToShow = filteredElements.slice(start, end);

        // 1. Sembunyikan semua item lebih dulu
        document.querySelectorAll('.svc-container').forEach(el => {
            el.style.display = 'none';
            el.classList.remove('reveal-item'); 
            el.style.opacity = '0';
        });

        // 2. Tampilkan item satu per satu
        itemsToShow.forEach((el, index) => {
            el.style.display = 'block';
            setTimeout(() => {
                el.classList.add('reveal-item');
            }, index * 60); 
        });

        updatePaginationButtons();
    }

    // UPDATE TOMBOL PAGINASI (TETAP)
    function updatePaginationButtons() {
        const totalPages = Math.ceil(filteredElements.length / itemsPerPage);
        const paginationWrapper = document.getElementById('opPagination');
        paginationWrapper.innerHTML = '';

        if (totalPages <= 1) return;

        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
        prevBtn.className = `w-10 h-10 rounded-xl flex items-center justify-center transition-all ${currentPage === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-primary hover:bg-primary/10'}`;
        prevBtn.onclick = () => { if(currentPage > 1) renderPage(currentPage - 1); };
        paginationWrapper.appendChild(prevBtn);

        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.innerText = i;
            pageBtn.className = `w-10 h-10 rounded-xl text-xs font-black transition-all ${i === currentPage ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-400 hover:bg-slate-100'}`;
            pageBtn.onclick = () => renderPage(i);
            paginationWrapper.appendChild(pageBtn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
        nextBtn.className = `w-10 h-10 rounded-xl flex items-center justify-center transition-all ${currentPage === totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-primary hover:bg-primary/10'}`;
        nextBtn.onclick = () => { if(currentPage < totalPages) renderPage(currentPage + 1); };
        paginationWrapper.appendChild(nextBtn);
    }

    // LIVE SEARCH (TETAP)
    document.getElementById('svcSearch').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        const allContainers = document.querySelectorAll('.svc-container');
        
        filteredElements = Array.from(allContainers).filter(container => {
            const name = container.querySelector('h4').textContent.toLowerCase();
            return name.includes(query);
        });

        renderPage(1);
    });

    // FUNGSI TOGGLE CONFIG (DIPERBAIKI LOGIC ICON-NYA)
    function toggleSvcConfig(id) {
        const container = document.getElementById('svc-' + id);
        const icon = document.getElementById('icon-' + id);
        const isActive = container.classList.contains('active');

        document.querySelectorAll('.svc-container').forEach(el => {
            if(el.id !== 'svc-' + id) {
                el.classList.remove('active');
                const otherIcon = el.querySelector('i[id^="icon-"]');
                if (otherIcon) otherIcon.classList.replace('bi-chevron-up', 'bi-gear-wide-connected');
            }
        });

        if (!isActive) {
            container.classList.add('active');
            icon.classList.replace('bi-gear-wide-connected', 'bi-chevron-up');
        } else {
            container.classList.remove('active');
            icon.classList.replace('bi-chevron-up', 'bi-gear-wide-connected');
        }
    }

    document.addEventListener('DOMContentLoaded', initPagination);

    /**
     * FUNGSI UPDATE STATUS FORMULIR (SINKRON DENGAN DATABASE)
     */
    async function updateFormStatus(id, selectElement) {
        const status = selectElement.value; // 'aktif' atau 'draft'
        const url = selectElement.dataset.url;
        
        // 1. Update Visual secara instan
        selectElement.className = 'status-select ' + (status === 'aktif' ? 'status-active' : 'status-draft');

        // 2. Animasi feedback klik
        selectElement.style.transform = 'scale(0.95)';
        setTimeout(() => { selectElement.style.transform = 'scale(1)'; }, 100);

        // 3. Kirim ke Server via AJAX
        try {
            const response = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            });

            const result = await response.json();
            if (!result.success) {
                alert('Gagal sinkronisasi status ke server.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan jaringan.');
        }
    }
</script>
@endpush

@endsection