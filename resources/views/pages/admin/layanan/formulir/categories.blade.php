@extends('layouts.admin')

@section('title', 'Manajemen Kategori Formulir')
@section('page-title', 'Kelola Kategori')
@section('page-icon', 'bi-kanban')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
    
    // 1. Tambahkan daftar ikon ini
    $icons = [
        'bi-people-fill', 'bi-person-badge-fill', 'bi-person-workspace', 'bi-house-door-fill',
        'bi-building-fill', 'bi-buildings', 'bi-building-gear', 'bi-signpost-2-fill',
        'bi-broadcast', 'bi-wifi', 'bi-router-fill', 'bi-camera-video-fill', 'bi-newspaper', 'bi-megaphone-fill',
        'bi-cash-stack', 'bi-cash-coin', 'bi-bank', 'bi-bank2', 'bi-wallet2', 'bi-credit-card-2-front-fill', 'bi-calculator-fill', 'bi-graph-up-arrow',
        'bi-flower1', 'bi-flower2', 'bi-tree-fill', 'bi-basket3-fill', 'bi-water', 'bi-egg-fried',
        'bi-hospital-fill', 'bi-heart-pulse-fill', 'bi-life-preserver',
        'bi-mortarboard-fill', 'bi-book-fill', 'bi-journal-bookmark-fill',
        'bi-shield-shaded', 'bi-shield-fill-check', 'bi-shield-lock-fill', 'bi-incognito',
        'bi-bus-front-fill', 'bi-truck', 'bi-car-front-fill', 'bi-bicycle', 'bi-compass-fill',
        'bi-lightning-charge-fill', 'bi-droplet-fill', 'bi-droplet-half', 'bi-recycle',
        'bi-briefcase-fill', 'bi-clipboard-check-fill', 'bi-clipboard-data-fill', 'bi-file-earmark-text-fill',
        'bi-files', 'bi-gear-fill', 'bi-tools', 'bi-award-fill', 'bi-trophy-fill', 'bi-stars',
        'bi-globe', 'bi-globe2', 'bi-map-fill', 'bi-geo-alt-fill', 'bi-flag-fill',
    ];
@endphp

<style>
    /* KUNCI 1: Tambahkan align-items: start agar grid tidak memaksa card melar ke bawah */
    .category-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); 
        gap: 24px; 
        align-items: start; /* Mengunci item agar tidak ditarik ke bawah */
    }

    .option-card-admin {
        background: #ffffff; 
        border: 1px solid #f1f5f9; 
        border-radius: 2rem;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex; 
        flex-direction: column; 
        justify-content: space-between;
        
        /* KUNCI 2: Ubah dari height: 100% menjadi tinggi tetap (fixed) */
        height: 340px; 
        
        position: relative; 
        overflow: hidden; 
        isolation: isolate;
        padding: 30px;
    }

    .option-card-admin:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 24px 48px rgba(10, 30, 51, 0.08); 
        border-color: #E8A33D; 
    }

    .side-indicator { position: absolute; top: 0; left: 0; width: 5px; height: 0; background: #E8A33D; transition: height 0.4s ease; z-index: 3; }
    .option-card-admin:hover .side-indicator { height: 100%; }

    .option-ghost-number { position: absolute; top: -0.5rem; right: 0.5rem; font-size: 6rem; font-weight: 900; line-height: 1; color: #0F2A4A; opacity: 0.03; z-index: 0; pointer-events: none; }

    .option-icon-box {
        width: 54px; height: 54px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; margin-bottom: 1.5rem;
        position: relative; z-index: 2; transition: all 0.4s ease;
    }
    .option-card-admin:hover .option-icon-box { background: #123457; color: #E8A33D; box-shadow: 0 10px 24px rgba(18, 52, 87, 0.2); }

    .option-card-title { font-size: 18px; font-weight: 800; color: #123457; margin-bottom: 10px; z-index: 2; position: relative; }
    
    /* KUNCI 3: Batasi baris deskripsi agar tidak menabrak batas bawah card */
    .option-card-desc { 
        font-size: 11.5px; 
        color: #94a3b8; 
        line-height: 1.6; 
        margin-bottom: 20px; 
        z-index: 2; 
        position: relative;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .card-admin-footer { 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        padding-top: 20px; 
        border-top: 1px solid #f1f5f9; 
        z-index: 2; 
        position: relative; 
        margin-top: auto; /* Memaksa footer tetap berada di dasar card */
    }

    /* ACTION BUTTONS */
    .btn-mini-action {
        width: 36px; height: 36px; border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; border: none; font-size: 1.1rem;
    }

    /* STATUS DROPDOWN STYLING */
    .status-select-wrapper { position: relative; }
    .status-select {
        appearance: none; -webkit-appearance: none;
        font-size: 9px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.5px; padding: 6px 28px 6px 12px; border-radius: 10px;
        border: none; cursor: pointer; transition: all 0.2s;
    }
    .status-select-wrapper::after {
        content: '\F288'; font-family: "bootstrap-icons";
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        font-size: 8px; pointer-events: none; opacity: 0.7;
    }

    .status-active { background: #ecfdf5; color: #059669; }
    .status-draft { background: #fff1f2; color: #e11d48; }

    /* Jaga kestabilan tinggi area paginasi */
    #categoryGrid { 
        min-height: 380px; /* Diturunkan sedikit agar seimbang dengan tinggi card yang baru */
    }

    /* Keyframe Animasi Masuk */
    @keyframes cardFadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .reveal-item {
        animation: cardFadeIn 0.4s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    /* Style Tombol Paginasi Premium */
    .btn-page {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
        transition: all 0.3s ease;
    }

    .btn-page.active {
        background: #123457;
        color: white;
        box-shadow: 0 10px 20px -5px rgba(18, 52, 87, 0.3);
        transform: translateY(-2px);
    }

    .btn-page:not(.active):not(:disabled):hover {
        background: #f1f5f9;
        color: #E8A33D;
    }
</style>

<div class="max-w-7xl mx-auto space-y-6">

    {{-- TOP BAR --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white premium-shadow border border-slate-100 rounded-3xl p-5 mb-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.layanan.form.manage', [$portalCode, $instansi_slug]) }}" class="w-9 h-9 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-white transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h3 class="text-lg font-black text-primary-dark leading-none">Kategori: {{ $svc->nama ?? 'Layanan' }}</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">Kelola sub-layanan dan alur pengajuan</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" placeholder="Cari kategori..." class="bg-primary/5 border border-primary/10 rounded-full pl-10 pr-4 py-2.5 text-xs font-bold text-primary w-full sm:w-48 focus:bg-white transition-all outline-none">
            </div>
            <a href="{{ route('admin.layanan.form.categories.create', [$portalCode, $svc->id]) }}" 
            class="bg-primary text-white h-10 px-6 rounded-full flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all active:scale-95">
                <i class="bi bi-plus-lg"></i> <span>Tambah Kategori</span>
            </a>
        </div>
    </div>

    {{-- GRID KATEGORI --}}
    <div id="categoryGrid" class="category-grid">
        @foreach($categories as $index => $cat)
            <div class="reveal category-item">
                <div class="option-card-admin">
                    <div class="side-indicator"></div>
                    <span class="option-ghost-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>

                    <div>
                        <div class="option-icon-box" data-color="{{ $cat->primary_color }}">
                            <i class="bi {{ $cat->icon }}"></i>
                        </div>

                        <h3 class="option-card-title">{{ $cat->nama }}</h3>
                        <p class="option-card-desc">{{ $cat->deskripsi ?? 'Belum ada deskripsi kategori.' }}</p>
                    </div>

                    <div class="card-admin-footer">
                        <div class="status-select-wrapper">
                            <select class="status-select {{ $cat->status === 'aktif' ? 'status-active' : 'status-draft' }}" data-id="{{ $cat->id }}" onchange="updateStatus(this)">
                                <option value="aktif" {{ $cat->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="draft" {{ $cat->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            {{-- FORM BUILDER --}}
                            <a href="{{ route('admin.layanan.form.builder', [$portalCode, $svc->id, $cat->id]) }}" class="btn-mini-action bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white" title="Bangun Formulir">
                                <i class="bi bi-ui-checks"></i>
                            </a>

                            {{-- EDIT --}}
                            <button type="button" class="btn-mini-action bg-accent/10 text-accent-dark hover:bg-accent hover:text-white btn-edit-category" data-id="{{ $cat->id }}" data-title="{{ $cat->nama }}" data-desc="{{ $cat->deskripsi ?? '' }}" data-icon="{{ $cat->icon }}" data-color="{{ $cat->primary_color }}" data-action="{{ route('admin.layanan.form.categories.update', [$portalCode, $svc->id, $cat->id]) }}" data-service="{{ $svc->nama }}" title="Edit Kategori">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            {{-- DELETE --}}
                            <button type="button" class="btn-mini-action bg-rose-50 text-rose-400 hover:bg-rose-600 hover:text-white btn-delete-category" data-id="{{ $cat->id }}" data-title="{{ $cat->nama }}" data-icon="{{ $cat->icon }}" data-action="{{ route('admin.layanan.form.categories.destroy', [$portalCode, $svc->id, $cat->id]) }}" title="Hapus Kategori">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div id="categoryPagination"
        class="flex items-center justify-center gap-2 mt-8">
    </div>

    {{-- INFO BOX --}}
    <div class="bg-[#123457] border border-slate-100 rounded-[2.5rem] p-6 sm:p-8 flex items-start gap-5 shadow-2xl mt-10">
        <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-accent text-xl shrink-0 border border-white/10">
            <i class="bi bi-kanban text-xl"></i>
        </div>
        <div>
            <h4 class="font-bold text-white mb-1 tracking-tight">Navigasi Alur</h4>
            <p class="text-xs text-slate-400 leading-relaxed max-w-2xl">
                Gunakan ikon <i class="bi bi-ui-checks text-emerald-400 mx-1"></i> untuk masuk ke <b>Form Builder</b>. Di sana Anda dapat menyusun field input, syarat dokumen, dan alur validasi khusus untuk kategori tersebut.
            </p>
        </div>
    </div>
</div>

<x-admin.modal-edit-category :icons="$icons" :portalCode="$portalCode" />
<x-admin.modal-delete-category />

@push('scripts')
<script>
    // Konfigurasi Paginasi
    const itemsPerPage = 2; // Menampilkan 3 kategori per halaman
    let currentPage = 1;
    let filteredItems = [];

    /**
     * INISIALISASI
     */
    document.addEventListener('DOMContentLoaded', () => {
        // Ambil semua elemen kategori
        filteredItems = Array.from(document.querySelectorAll('.category-item'));
        renderPage(1);
        
        // Integrasi Live Search (Jika input search ada)
        const searchInput = document.querySelector('input[placeholder="Cari kategori..."]');
        if(searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                filteredItems = Array.from(document.querySelectorAll('.category-item')).filter(item => {
                    const title = item.querySelector('.option-card-title').textContent.toLowerCase();
                    return title.includes(query);
                });
                renderPage(1);
            });
        }
    });

    /**
     * RENDER HALAMAN
     */
    function renderPage(page) {
        currentPage = page;
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const toShow = filteredItems.slice(start, end);

        // 1. Sembunyikan semua item & Reset animasi
        document.querySelectorAll('.category-item').forEach(item => {
            item.style.display = 'none';
            item.classList.remove('reveal-item');
            item.style.opacity = '0';
        });

        // 2. Tampilkan item yang sesuai dengan efek mengalir (Staggered)
        toShow.forEach((item, index) => {
            item.style.display = 'block';
            setTimeout(() => {
                item.classList.add('reveal-item');
            }, index * 80);
        });

        renderPagination();
    }

    /**
     * RENDER TOMBOL NAVIGASI
     */
    function renderPagination() {
        const wrapper = document.getElementById('categoryPagination');
        wrapper.innerHTML = '';
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);

        if (totalPages <= 1) return;

        // Tombol Prev
        wrapper.appendChild(createPageButton('<i class="bi bi-chevron-left"></i>', () => {
            if(currentPage > 1) renderPage(currentPage - 1);
        }, currentPage === 1));

        // Tombol Angka
        for (let i = 1; i <= totalPages; i++) {
            wrapper.appendChild(createPageButton(i, () => renderPage(i), false, i === currentPage));
        }

        // Tombol Next
        wrapper.appendChild(createPageButton('<i class="bi bi-chevron-right"></i>', () => {
            if(currentPage < totalPages) renderPage(currentPage + 1);
        }, currentPage === totalPages));
    }

    /**
     * HELPER BUAT TOMBOL
     */
    function createPageButton(content, onClick, isDisabled, isActive = false) {
        const btn = document.createElement('button');
        btn.innerHTML = content;
        btn.className = `btn-page ${isActive ? 'active' : 'text-slate-400'}`;
        
        if (isDisabled) {
            btn.disabled = true;
            btn.style.opacity = '0.3';
            btn.style.cursor = 'not-allowed';
        } else {
            btn.onclick = (e) => {
                e.preventDefault();
                onClick();
            };
        }
        return btn;
    }

    /**
     * UPDATE STATUS VISUAL
     */
    async function updateStatus(selectElement) {
        const status = selectElement.value;
        const catId = selectElement.dataset.id;
        const portal = '{{ $portalCode }}';
        const svcId = '{{ $svc->id }}';

        // UI Feedback
        selectElement.className = `status-select ${status === 'aktif' ? 'status-active' : 'status-draft'}`;

        try {
            const response = await fetch(`/${portal}/layanan/form/${svcId}/categories/${catId}/status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            });

            const result = await response.json();
            if (!result.success) alert('Gagal memperbarui status di server.');
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan koneksi.');
        }
    }

    document.querySelectorAll('.btn-edit-category').forEach(button => {
        button.addEventListener('click', () => {
            openEditCategoryModal({
                id: button.dataset.id,
                title: button.dataset.title,
                desc: button.dataset.desc,
                icon: button.dataset.icon,
                color_hex: button.dataset.color
            }, button.dataset.action, button.dataset.service);
        });
    });

    document.querySelectorAll('.btn-delete-category').forEach(button => {
        button.addEventListener('click', () => {
            openDeleteCategoryModal({
                id: button.dataset.id,
                title: button.dataset.title,
                icon: button.dataset.icon
            }, button.dataset.action);
        });
    });

    document.querySelectorAll('.option-icon-box[data-color]').forEach(box => {
        const color = box.dataset.color;
        box.style.backgroundColor = `${color}15`;
        box.style.color = color;
    });
</script>
@endpush

@endsection