@extends('layouts.admin')

@section('title', 'Gudang Arsip Digital')
@section('page-title', 'Pusat Arsip')
@section('page-icon', 'bi-safe2-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<style>
    /* --- 1. BANNER SECTION (Sesuai Desain Profil) --- */
    .id-banner-sleek {
        position: relative;
        background: #0A1E33;
        border-radius: 20px;
        overflow: hidden;
        padding: 30px 35px;
        border-bottom: 3px solid #E8A33D;
        box-shadow: 0 15px 30px -10px rgba(10, 30, 51, 0.4);
        margin-bottom: 25px;
    }
    
    .id-pattern {
        position: absolute; inset: 0;
        background-image: radial-gradient(rgba(232, 163, 61, 0.1) 0.5px, transparent 0.5px);
        background-size: 15px 15px; opacity: 0.5;
    }

    /* --- 2. CARD MODERN & HUB TILES --- */
    .card-modern {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #eef2f6;
        padding: 22px 28px;
        box-shadow: 0 10px 20px -5px rgba(18, 52, 87, 0.06); 
        transition: all 0.3s ease;
    }

    .archive-tile-btn {
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 18px;
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
        text-align: left;
        width: 100%;
    }

    .archive-tile-btn:hover {
        background: #ffffff;
        border-color: #E8A33D;
        transform: translateY(-5px);
        box-shadow: 0 15px 30px -10px rgba(18, 52, 87, 0.1);
    }

    .icon-box-sleek {
        width: 54px; height: 54px; border-radius: 15px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; flex-shrink: 0;
        background: #123457; color: #E8A33D;
    }

    /* --- 3. SEARCH & FILTERS (Sesuai Style Input Profil) --- */
    .input-wrapper { position: relative; display: flex; align-items: center; }
    
    .input-sleek {
        width: 100%; padding: 10px 14px 10px 42px;
        background: #F8FAFC; border: 1.5px solid #E2E8F0;
        border-radius: 12px; font-size: 12px; font-weight: 700;
        color: #123457; transition: all 0.2s;
    }

    .input-icon-center {
        position: absolute; left: 0; top: 0; bottom: 0;
        width: 42px; display: flex; align-items: center; justify-content: center;
        color: #cbd5e1; font-size: 1rem; pointer-events: none;
    }

    .filter-pill {
        padding: 7px 18px; border-radius: 10px; font-size: 9px; font-weight: 800;
        text-transform: uppercase; color: #94a3b8; cursor: pointer; transition: 0.3s;
        background: #F8FAFC; border: 1.5px solid #E2E8F0;
    }
    .filter-pill.active { background: #123457; color: #E8A33D; border-color: #123457; }

    /* --- 4. DATA ROWS --- */
    .data-row-container {
        background: #ffffff; border: 1.5px solid #f1f5f9; border-radius: 15px;
        margin-bottom: 10px; transition: 0.3s; overflow: hidden;
    }
    .data-row-container:hover { border-color: #E8A33D; }

    .data-clickable { padding: 15px 20px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; }

    .status-mini {
        font-size: 8px; font-weight: 900; text-transform: uppercase;
        padding: 4px 10px; border-radius: 6px; letter-spacing: 0.5px;
    }
    .st-done { background: #ecfdf5; color: #059669; }
    .st-rejected { background: #fff1f2; color: #e11d48; }

    .drawer-content {
        max-height: 0; overflow: hidden; transition: max-height 0.4s ease;
        background: #F8FAFC; border-top: 1px solid #f1f5f9;
    }
    .data-row-container.active .drawer-content { max-height: 500px; }

    .btn-back {
        background: #123457; color: #fff; padding: 8px 15px; border-radius: 10px;
        font-size: 9px; font-weight: 800; text-transform: uppercase; display: flex; align-items: center; gap: 8px;
    }

    .btn-primary-sleek {
        background: #123457; color: #fff; border: none;
        padding: 8px 18px; border-radius: 10px;
        font-size: 10px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 1px; transition: 0.3s;
    }
    .btn-primary-sleek:hover { background: #E8A33D; color: #0A1E33; }

    /* Animations */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .reveal-item { animation: fadeInUp 0.4s ease forwards; }

    .btn-action {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; transition: all 0.25s;
        border: none; cursor: pointer;
    }
    .btn-detail { background: rgba(18, 52, 87, 0.05); color: #123457; }
    .btn-detail:hover { background: #123457; color: #ffffff; transform: scale(1.1); }
</style>

<div class="max-w-6xl mx-auto pb-10 px-4">
    
    {{-- --- BANNER SECTION --- --}}
    <div class="id-banner-sleek">
        <div class="id-pattern"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center text-accent border border-white/10 shadow-inner">
                    <i class="bi bi-safe2-fill text-3xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 text-[8px] font-black uppercase tracking-widest">Protocol Active</span>
                        <span class="text-slate-400 text-[8px] font-bold uppercase tracking-widest">DB SYNC OK</span>
                    </div>
                    <h1 class="text-2xl font-black text-white tracking-tight leading-none" id="dynamicTitle">Pusat Direktori Arsip</h1>
                    <p class="text-slate-400 text-[11px] font-bold mt-2" id="dynamicDesc">Manajemen riwayat data final dan permanen sistem.</p>
                </div>
            </div>
            <div class="hidden lg:block text-right">
                <p class="text-[8px] font-bold text-slate-500 uppercase mb-1">Total Record Terkunci</p>
                {{-- Gabungan jumlah aduan + pengajuan --}}
                <p class="text-2xl font-black text-white">{{ count($complaints) + $totalArchivedSubmissions }} <span class="text-accent text-sm">Data</span></p>
            </div>
        </div>
    </div>

    {{-- --- STATE 1: HUB --- --}}
    <div id="view-hub" class="grid md:grid-cols-2 gap-5 reveal-item">
        <button class="archive-tile-btn" onclick="changeState('pengaduan')">
            <div class="icon-box-sleek"><i class="bi bi-megaphone-fill"></i></div>
            <div class="flex-1">
                <h4 class="text-sm font-black text-primary-dark mb-1">Arsip Pengaduan</h4>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ count($complaints) }} Laporan Tersimpan</p>
            </div>
            <i class="bi bi-arrow-right text-slate-300"></i>
        </button>

        <button class="archive-tile-btn" onclick="changeState('pengajuan')">
            <div class="icon-box-sleek"><i class="bi bi-file-earmark-check-fill"></i></div>
            <div class="flex-1">
                <h4 class="text-sm font-black text-primary-dark mb-1">Arsip Pengajuan</h4>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $totalArchivedSubmissions }} Berkas Layanan</p>
            </div>
            <i class="bi bi-arrow-right text-slate-300"></i>
        </button>
    </div>

    {{-- --- STATE 2: PENGADUAN LIST --- --}}
    <div id="view-pengaduan" class="hidden space-y-5 reveal-item">
        <div class="card-modern flex flex-wrap items-center justify-between gap-4">
            <button class="btn-back" onclick="changeState('hub')">
                <i class="bi bi-chevron-left"></i> Kembali ke Hub
            </button>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex gap-1">
                    <div class="filter-pill active" onclick="filterStatus('all', this)">Semua</div>
                    <div class="filter-pill" onclick="filterStatus('done', this)">Selesai</div>
                    <div class="filter-pill" onclick="filterStatus('rejected', this)">Ditolak</div>
                </div>
                <div class="input-wrapper w-48">
                    <input type="text" id="searchAduan" onkeyup="liveSearch('aduan')" class="input-sleek" placeholder="Cari ID Tiket...">
                    <div class="input-icon-center"><i class="bi bi-search"></i></div>
                </div>
            </div>
        </div>

        <div id="list-aduan" class="space-y-3">
            @forelse($complaints as $cm)
            <div class="data-row-container aduan-item" data-status="{{ $cm->status }}" data-code="{{ strtolower($cm->ticket_id) }}" data-title="{{ strtolower($cm->title) }}">
                <div class="flex items-center justify-between p-4 px-6">
                    {{-- Info Pengaduan --}}
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary-dark border border-slate-100 font-bold text-xs flex-shrink-0">
                            #
                        </div>
                        <div class="truncate">
                            <h4 class="text-xs font-black text-primary-dark mb-0.5 truncate">{{ $cm->title }}</h4>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest truncate">
                                {{ $cm->ticket_id }} &bull; {{ $cm->category }}
                            </p>
                        </div>
                    </div>

                    {{-- Status & Preview --}}
                    <div class="flex items-center gap-6">
                        <span class="status-mini {{ $cm->status == 'done' ? 'st-done' : 'st-rejected' }}">
                            {{ $cm->status == 'done' ? 'Selesai' : 'Ditolak' }}
                        </span>
                        
                        <button type="button"
                                onclick="window.openArchiveComplaintModal('{{ $cm->id }}')"
                                class="btn-action btn-detail"
                                title="Preview Laporan">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="card-modern text-center py-20">
                <i class="bi bi-inbox text-4xl text-slate-200"></i>
                <p class="text-[10px] font-black text-slate-400 uppercase mt-4">Belum ada data arsip pengaduan</p>
            </div>
            @endforelse
        </div>
        <div id="aduanPagination" class="flex justify-center gap-2 mt-6"></div>
    </div>

    {{-- --- STATE 3: PENGAJUAN LIST --- --}}
    <div id="view-pengajuan-selection" class="hidden space-y-5 reveal-item">
        <div class="card-modern flex items-center justify-between gap-4">
            <button class="btn-back" onclick="changeState('hub')">
                <i class="bi bi-chevron-left"></i> Kembali ke Hub
            </button>
            <div class="input-wrapper w-64">
                <input type="text" id="searchIns" onkeyup="liveSearch('ins')" class="input-sleek" placeholder="Cari nama instansi...">
                <div class="input-icon-center"><i class="bi bi-search"></i></div>
            </div>
        </div>

        <div id="list-instansi" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($instansiList as $ins)
            <div class="card-modern ins-item" data-name="{{ strtolower($ins->nama) }}">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="ins-color w-12 h-12 rounded-2xl flex items-center justify-center text-white font-black text-lg shadow-lg"
                        data-color="{{ $ins->primary_color }}"
                    >
                        {{ strtoupper(substr($ins->nama, 0, 1)) }}
                    </div>
                    <div class="text-right">
                        <p class="text-[8px] font-black text-slate-400 uppercase">Records</p>
                        <p class="text-sm font-black text-primary-dark">{{ $ins->archived_count }} Berkas</p>
                    </div>
                </div>
                <h4 class="text-xs font-black text-primary-dark mb-4 h-8 flex items-center leading-tight">{{ $ins->nama }}</h4>
                <button type="button" onclick="selectAgencyArchive('{{ $ins->id }}', '{{ $ins->nama }}')" class="btn-primary-sleek w-full">
                    Lihat Arsip
                </button>
            </div>
            @endforeach
        </div>
    </div>

    <!-- --- STATE 4: DAFTAR BERKAS (Setelah Pilih Instansi) --- -->
    <div id="view-pengajuan-list" class="hidden space-y-5 reveal-item">
        <div class="card-modern flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <button class="btn-back !bg-slate-100 !text-slate-600 !border-none" onclick="changeState('pengajuan')">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <div>
                    <h4 class="text-xs font-black text-primary-dark" id="display-selected-ins">NAMA INSTANSI</h4>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Daftar Arsip Berkas</p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex gap-1">
                    <div class="filter-pill active" onclick="filterStatusSub('all', this)">Semua</div>
                    <div class="filter-pill" onclick="filterStatusSub('selesai', this)">Selesai</div>
                    <div class="filter-pill" onclick="filterStatusSub('ditolak', this)">Ditolak</div>
                </div>
                <div class="input-wrapper w-48">
                    <input type="text" id="searchSub" onkeyup="liveSearch('sub')" class="input-sleek" placeholder="Cari No / Nama...">
                    <div class="input-icon-center"><i class="bi bi-search"></i></div>
                </div>
            </div>
        </div>

        <div id="list-pengajuan" class="space-y-3">
            @forelse($archivedSubmissions as $sub)
            @php $namaPemohon = $sub->data->where('parameter', 'nama_lengkap')->first()->value ?? 'Warga'; @endphp
            
            <div class="data-row-container sub-item" 
                data-instansi-id="{{ $sub->layanan->instansi_id }}"
                data-status="{{ $sub->status }}" 
                data-code="{{ strtolower($sub->nomor_pengajuan) }}" 
                data-title="{{ strtolower($namaPemohon) }}">
                
                {{-- Container Row Ringkas --}}
                <div class="flex items-center justify-between p-4 px-6">
                    
                    {{-- Bagian Kiri: Icon & Info --}}
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-primary/5 flex items-center justify-center text-primary border border-primary/10 flex-shrink-0">
                            <i class="bi {{ $sub->category->icon ?? 'bi-file-earmark-text' }}"></i>
                        </div>
                        <div class="truncate">
                            <h4 class="text-xs font-black text-primary-dark mb-0.5 truncate">{{ $namaPemohon }}</h4>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest truncate">
                                {{ $sub->nomor_pengajuan }} &bull; {{ $sub->layanan->nama }}
                            </p>
                        </div>
                    </div>

                    {{-- Bagian Kanan: Status & Action --}}
                    <div class="flex items-center gap-6">
                        <span class="status-mini {{ $sub->status == 'selesai' ? 'st-done' : 'st-rejected' }}">
                            {{ $sub->status == 'selesai' ? 'Selesai' : 'Ditolak' }}
                        </span>
                        
                        <button type="button"
                                onclick="window.openArchiveSubmission('{{ $sub->id }}')"
                                class="btn-action btn-detail"
                                title="Preview Pengajuan">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="card-modern text-center py-20">
                <i class="bi bi-folder-x text-4xl text-slate-200"></i>
                <p class="text-[10px] font-black text-slate-400 uppercase mt-4">Tidak ada arsip berkas ditemukan</p>
            </div>
            @endforelse
        </div>
        <div id="subPagination" class="flex justify-center gap-2 mt-8"></div>
    </div>
</div>

{{-- MODAL ARSIP PENGADUAN --}}
@include('components.admin.modal-archive-complaint', [
    'portalCode' => $portalCode
])

{{-- MODAL ARSIP PENGAJUAN --}}
@include('components.admin.modal-archive-submission', [
    'portalCode' => $portalCode
])

<script>
    // 1. KONFIGURASI UTAMA
    const CONFIG = {
        aduan: { perPage: 6, listId: 'list-aduan', pagId: 'aduanPagination', itemClass: '.aduan-item' },
        ins:   { perPage: 6, listId: 'list-instansi', pagId: 'insPagination', itemClass: '.ins-item' },
        sub:   { perPage: 6, listId: 'list-pengajuan', pagId: 'subPagination', itemClass: '.sub-item' }
    };

    let currentAgencyId = null; // Menyimpan ID instansi yang sedang dibuka

    // 2. NAVIGASI ANTAR STATE (VIEW)
    function changeState(target) {
        const title = document.getElementById('dynamicTitle');
        const desc = document.getElementById('dynamicDesc');
        
        // Sembunyikan semua kontainer
        document.getElementById('view-hub').classList.add('hidden');
        document.getElementById('view-pengaduan').classList.add('hidden');
        document.getElementById('view-pengajuan-selection').classList.add('hidden');
        document.getElementById('view-pengajuan-list').classList.add('hidden');

        if (target === 'hub') {
            document.getElementById('view-hub').classList.remove('hidden');
            title.innerText = "Pusat Direktori Arsip";
            desc.innerText = "Manajemen riwayat data final dan permanen sistem.";
        } 
        else if (target === 'pengaduan') {
            document.getElementById('view-pengaduan').classList.remove('hidden');
            title.innerText = "Arsip Pengaduan";
            desc.innerText = "Histori laporan masyarakat yang telah bersifat final.";
            initPagination('aduan');
        } 
        else if (target === 'pengajuan') {
            // Menampilkan daftar PILIHAN INSTANSI
            document.getElementById('view-pengajuan-selection').classList.remove('hidden');
            title.innerText = "Arsip Berkas Layanan";
            desc.innerText = "Pilih instansi untuk meninjau database permohonan warga.";
            initPagination('ins');
        }
    }

    // 3. FUNGSI PILIH INSTANSI (Drill-down ke List Berkas)
    function selectAgencyArchive(id, name) {
        currentAgencyId = id;

        const selectedName = document.getElementById('display-selected-ins');
        if (selectedName) {
            selectedName.innerText = name;
        }

        document.getElementById('view-pengajuan-selection').classList.add('hidden');
        document.getElementById('view-pengajuan-list').classList.remove('hidden');

        document.querySelectorAll('.sub-item').forEach(item => {
            item.style.display = item.dataset.instansiId == id ? '' : 'none';
        });

        initPagination('sub');
    }

    // 4. PENCARIAN (LIVE SEARCH)
    function liveSearch(type) {
        let inputId, listId, itemClass;
        
        if (type === 'aduan') { inputId = 'searchAduan'; listId = 'list-aduan'; }
        else if (type === 'ins') { inputId = 'searchIns'; listId = 'list-instansi'; }
        else if (type === 'sub') { inputId = 'searchSub'; listId = 'list-pengajuan'; }

        const q = document.getElementById(inputId).value.toLowerCase();
        const items = document.getElementById(listId).querySelectorAll(CONFIG[type].itemClass);

        items.forEach(el => {
            const text = (el.dataset.code || '') + (el.dataset.title || '') + (el.dataset.name || '');
            const isMatch = text.toLowerCase().includes(q);

            // Jika di list berkas, harus sinkron dengan Instansi yang dipilih
            if (type === 'sub') {
                el.style.display = (isMatch && el.dataset.instansiId == currentAgencyId) ? '' : 'none';
            } else {
                el.style.display = isMatch ? '' : 'none';
            }
        });

        initPagination(type);
    }

    // 5. FILTER STATUS (PENGADUAN & PENGAJUAN)
    function filterStatus(status, el, type = 'aduan') {
        const itemClass = (type === 'aduan') ? '.aduan-item' : '.sub-item';
        
        // Update UI Pill
        el.parentElement.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        el.classList.add('active');

        document.querySelectorAll(itemClass).forEach(item => {
            const matchStatus = (status === 'all' || item.dataset.status === status);
            
            if (type === 'sub') {
                // Pastikan tetap terfilter berdasarkan instansi
                item.style.display = (matchStatus && item.dataset.instansiId == currentAgencyId) ? '' : 'none';
            } else {
                item.style.display = matchStatus ? '' : 'none';
            }
        });

        initPagination(type);
    }

    // Shortcut untuk filter status pengajuan di HTML agar tidak bingung
    function filterStatusSub(status, el) {
        filterStatus(status, el, 'sub');
    }

    // 6. TOGGLE DRAWER (DETAIL)
    function toggleItemDetail(row) {
        const container = row.closest('.data-row-container');
        const isActive = container.classList.contains('active');
        document.querySelectorAll('.data-row-container').forEach(el => el.classList.remove('active'));
        if (!isActive) container.classList.add('active');
    }

    // 7. LOGIKA PAGINASI UNIVERSAL
    function initPagination(type) {
        const { perPage, listId, pagId, itemClass } = CONFIG[type];
        const wrapper = document.getElementById(pagId);
        if (!wrapper) return;

        // Ambil elemen yang lolos filter (display tidak none)
        const allItems = Array.from(document.getElementById(listId).querySelectorAll(itemClass));
        const visibleItems = allItems.filter(el => el.style.display !== 'none');
        
        const totalPages = Math.ceil(visibleItems.length / perPage);
        wrapper.innerHTML = "";

        if(totalPages <= 1) {
            // Jika cuma 1 halaman, pastikan item yang lolos filter tetap tampil
            visibleItems.forEach(el => el.style.display = "");
            return;
        };

        function showPage(p) {
            // Sembunyikan semua item dulu
            allItems.forEach(el => el.style.display = "none");
            
            // Tampilkan hanya yang masuk range halaman p dari daftar visible
            visibleItems.forEach((el, idx) => {
                if (idx >= (p - 1) * perPage && idx < p * perPage) {
                    el.style.display = "";
                }
            });
            renderButtons(p);
        }

        function renderButtons(activePage) {
            wrapper.innerHTML = "";
            for(let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.innerText = i;
                btn.className = `w-9 h-9 rounded-xl text-[11px] font-black transition-all ${i === activePage ? 'bg-primary text-accent shadow-lg' : 'bg-white border border-slate-200 text-slate-400 hover:border-primary'}`;
                btn.onclick = () => {
                    showPage(i);
                    // Scroll ke atas list
                    document.getElementById(listId).scrollIntoView({ behavior: 'smooth', block: 'start' });
                };
                wrapper.appendChild(btn);
            }
        }
        showPage(1);
    }

    // 8. STARTUP
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.ins-color').forEach(el => {
            const color = el.dataset.color;

            if (color) {
                el.style.backgroundColor = color;
            }
        });

        changeState('hub');
    });
</script>
@endsection