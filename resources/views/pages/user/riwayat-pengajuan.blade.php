@extends('layouts.app')

@section('title', 'Riwayat Pengajuan Berkas - Sukabumi One Access')

@section('content')
<style>
    .history-hero {
        background-color: #0A1E33;
        padding: 4.5rem 1.5rem 7rem;
        border-bottom: 4px solid #E8A33D;
        position: relative;
        overflow: hidden;
    }
    .bg-dot-matrix {
        position: absolute; inset: 0; opacity: 0.12;
        background-image: radial-gradient(#E8A33D 1px, transparent 0);
        background-size: 30px 30px;
    }

    /* ================= SEARCH & FILTER ================= */
    .search-history-container {
        max-width: 820px;
        margin: -2.75rem auto 0;
        position: relative;
        z-index: 40;
    }
    .filter-chip {
        border: 1.5px solid #E2E8F0;
        background: #fff;
        color: #64748B;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 8px 16px;
        border-radius: 999px;
        white-space: nowrap;
        cursor: pointer;
        transition: all .2s ease;
    }
    .filter-chip:hover { border-color: #123457; color: #123457; }
    .filter-chip.active {
        background: #0A1E33;
        border-color: #0A1E33;
        color: #E8A33D;
        box-shadow: 0 8px 20px rgba(10,30,51,0.18);
    }

    /* ================= SUMMARY STATUS ================= */
    .summary-card {
        background: #fff;
        border: 1.5px solid #E2E8F0;
        border-radius: 18px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all .25s ease;
    }
    .summary-card:hover { border-color: #123457; transform: translateY(-3px); box-shadow: 0 12px 24px rgba(10,30,51,0.06); }
    .summary-icon {
        width: 40px; height: 40px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }
    .summary-number { font-size: 1.5rem; font-weight: 900; color: #0A1E33; line-height: 1; }
    .summary-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color: #64748B; margin-top: 2px; }

    /* ================= CARD PENGAJUAN ================= */
    .file-card {
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 24px;
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        gap: 15px;
        cursor: pointer;
        height: 100%;
    }
    .file-card:hover {
        background: #fff;
        border-color: #123457;
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(10,30,51,0.08);
    }

    .file-icon-box {
        width: 46px; height: 46px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        background: #123457; color: #E8A33D; font-size: 1.25rem;
        flex-shrink: 0;
    }

    /* STATUS PILLS */
    .status-pill {
        font-size: 9px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 1px; padding: 5px 12px; border-radius: 10px;
        display: inline-flex; align-items: center; gap: 6px;
        white-space: nowrap;
    }
    .st-diajukan { background: #eff6ff; color: #3b82f6; }
    .st-review   { background: #f0f9ff; color: #0ea5e9; border: 1px solid #0ea5e920; }
    .st-diproses { background: #fefce8; color: #ca8a04; }
    .st-revisi   { background: #fffbeb; color: #d97706; border: 1px solid #f59e0b40; }
    .st-selesai  { background: #f0fdf4; color: #16a34a; }
    .st-ditolak  { background: #fef2f2; color: #dc2626; }

    .revision-alert {
        background: #FFFBEB; border: 1.5px solid #FEF3C7;
        border-radius: 15px; padding: 12px 16px;
        display: flex; gap: 10px; align-items: flex-start;
    }
    .revision-alert i { color: #D97706; font-size: 1rem; margin-top: 1px; }
    .revision-alert-body { min-width: 0; }
    .revision-alert-title { font-size: 9px; font-weight: 900; letter-spacing: .06em; text-transform: uppercase; color: #D97706; margin: 0 0 2px; }
    .revision-alert p { font-size: 11px; color: #92400E; margin: 0; font-weight: 700; line-height: 1.4; }
    .revision-alert p.clamp { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .reveal-item { animation: fadeInUp 0.5s ease forwards; opacity: 0; }

    /* ================= BUTTONS ================= */
    .file-card-actions {
        display: flex;
        gap: 10px;
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid #E2E8F0;
    }
    .btn-edit-submission,
    .btn-view-submission {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
        cursor: pointer;
        border-radius: 11px;
        padding: 10px 14px;
        transition: all .2s ease;
    }
    .btn-edit-submission {
        background: #fff;
        border: 1.5px solid #E8A33D;
        color: #B4741E;
    }
    .btn-edit-submission:hover { background: #FFF7EB; border-color: #d1861f; }

    .btn-view-submission {
        border: 1.5px solid #0A1E33;
        background: #0A1E33;
        color: #fff;
    }
    .btn-view-submission:hover { background: #123457; border-color: #123457; }
    .btn-view-submission i { transition: transform .2s ease; }
    .btn-view-submission:hover i { transform: translateX(3px); }

    /* ================= EMPTY STATE ================= */
    .empty-state-box {
        border: 1.5px dashed #CBD5E1;
        border-radius: 24px;
        background: #F8FAFC;
        padding: 4rem 1.5rem;
    }
    .empty-state-icon {
        width: 72px; height: 72px; border-radius: 20px;
        background: #0A1E33; color: #E8A33D;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; margin: 0 auto 1.25rem;
    }

    @media (max-width: 640px) {
        .file-card-actions { flex-direction: column; }
    }
</style>

{{-- HERO --}}
<section class="history-hero">
    <div class="bg-dot-matrix"></div>
    <div class="max-w-7xl mx-auto relative z-10 text-center">
        <nav class="flex justify-center items-center gap-2 text-accent text-[10px] font-black uppercase tracking-[0.2em] mb-4">
            <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
            <i class="bi bi-chevron-right text-[8px]"></i>
            <span>Arsip Berkas</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold text-white tracking-tighter">Riwayat <span class="text-accent italic">Pengajuan.</span></h1>
        <p class="text-slate-400 mt-4 max-w-xl mx-auto text-sm sm:text-base">Pantau status dan perkembangan pengajuan layanan Anda.</p>
    </div>
</section>

{{-- SEARCH & FILTER --}}
<div class="search-history-container px-4">
    <div class="bg-white rounded-2xl p-3 shadow-2xl border border-slate-100">
        <div class="flex items-center px-4 py-3 bg-slate-50 rounded-xl">
            <i class="bi bi-search text-slate-400 mr-3"></i>
            <input type="text" id="fileSearch" onkeyup="filterFiles()"
                   class="bg-transparent border-none outline-none w-full font-bold text-primary text-sm"
                   placeholder="Cari nomor pengajuan, layanan, atau kategori...">
        </div>

        <div class="flex gap-2 mt-3 overflow-x-auto pb-1">
            <button type="button" class="filter-chip active" data-status="all" onclick="filterByStatus('all', this)">Semua</button>
            <button type="button" class="filter-chip" data-status="diajukan" onclick="filterByStatus('diajukan', this)">Baru</button>
            <button type="button" class="filter-chip" data-status="diproses" onclick="filterByStatus('diproses', this)">Diproses</button>
            <button type="button" class="filter-chip" data-status="perlu_revisi" onclick="filterByStatus('perlu_revisi', this)">Perlu Perbaikan</button>
            <button type="button" class="filter-chip" data-status="selesai" onclick="filterByStatus('selesai', this)">Selesai</button>
        </div>
    </div>
</div>

<section class="py-16 sm:py-24 bg-white min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-8">

        {{-- SUMMARY STATUS --}}
        @php
            $totalCount   = $submissions->count();
            $baruCount    = $submissions->where('status', 'diajukan')->count();
            $prosesCount  = $submissions->whereIn('status', ['review', 'diproses'])->count();
            $revisiCount  = $submissions->where('status', 'perlu_revisi')->count();
            $selesaiCount = $submissions->where('status', 'selesai')->count();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-10">
            <div class="summary-card">
                <div class="summary-icon" style="background:#EFF6FF; color:#2563EB;"><i class="bi bi-stack"></i></div>
                <div>
                    <p class="summary-number">{{ $totalCount }}</p>
                    <p class="summary-label">Total</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon" style="background:#EFF6FF; color:#3B82F6;"><i class="bi bi-file-earmark-plus"></i></div>
                <div>
                    <p class="summary-number">{{ $baruCount }}</p>
                    <p class="summary-label">Baru</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon" style="background:#FEFCE8; color:#CA8A04;"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <p class="summary-number">{{ $prosesCount }}</p>
                    <p class="summary-label">Diproses</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon" style="background:#FFFBEB; color:#D97706;"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <p class="summary-number">{{ $revisiCount }}</p>
                    <p class="summary-label">Perbaikan</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon" style="background:#F0FDF4; color:#16A34A;"><i class="bi bi-check-circle"></i></div>
                <div>
                    <p class="summary-number">{{ $selesaiCount }}</p>
                    <p class="summary-label">Selesai</p>
                </div>
            </div>
        </div>

        {{-- DAFTAR RIWAYAT PENGAJUAN --}}
        <div id="fileList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($submissions as $sub)
                @php
                    $statusClass = [
                        'diajukan' => 'st-diajukan',
                        'review' => 'st-review',
                        'diproses' => 'st-diproses',
                        'perlu_revisi' => 'st-revisi',
                        'selesai' => 'st-selesai',
                        'ditolak' => 'st-ditolak',
                    ][$sub->status] ?? '';

                    $statusLabel = [
                        'diajukan' => 'Baru',
                        'review' => 'Ditinjau',
                        'diproses' => 'Diproses',
                        'perlu_revisi' => 'Perlu Perbaikan',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                    ][$sub->status] ?? $sub->status;
                @endphp
                <div class="file-card reveal-item"
                     style="animation-delay: {{ $loop->index * 0.06 }}s"
                     data-id="{{ $sub->id }}"
                     data-status="{{ $sub->status }}"
                     data-code="{{ strtolower($sub->nomor_pengajuan) }}"
                     data-name="{{ strtolower($sub->layanan->nama) }}"
                     data-category="{{ strtolower($sub->category->nama ?? '') }}"
                     onclick="window.openPreviewModal('{{ $sub->id }}')">

                    <div class="flex justify-between items-start">
                        <div class="file-icon-box shadow-lg shadow-primary/20">
                            <i class="bi {{ $sub->category->icon ?? 'bi-file-earmark-text' }}"></i>
                        </div>
                        <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>

                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $sub->nomor_pengajuan }}</p>
                        <h4 class="text-base font-black text-primary-dark mt-1 leading-tight">{{ $sub->layanan->nama }}</h4>
                        <p class="text-[11px] font-bold text-accent mt-1">{{ $sub->category->nama }}</p>
                    </div>

                    {{-- PESAN REVISI --}}
                    @if($sub->status == 'perlu_revisi' && $sub->catatan_admin)
                        <div class="revision-alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div class="revision-alert-body">
                                <p class="revision-alert-title">Perlu Perbaikan</p>
                                <p class="clamp">{{ $sub->catatan_admin }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="pt-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <span>Diajukan: {{ $sub->created_at->format('d/m/Y') }}</span>
                    </div>

                    <div class="file-card-actions">
                        @if($sub->status === 'perlu_revisi')
                            <button type="button" class="btn-edit-submission"
                                    onclick="event.stopPropagation(); window.openEditSubmission('{{ $sub->id }}')">
                                <i class="bi bi-pencil-square"></i> Edit Pengajuan
                            </button>
                        @endif
                        <button type="button" class="btn-view-submission"
                                onclick="event.stopPropagation(); window.openPreviewModal('{{ $sub->id }}')">
                            Lihat Berkas <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="empty-state-box text-center">
                        <div class="empty-state-icon">
                            <i class="bi bi-folder2-open"></i>
                        </div>
                        <h3 class="text-lg font-black text-primary-dark">Belum Ada Pengajuan</h3>
                        <p class="text-sm text-slate-400 font-semibold mt-2 max-w-sm mx-auto">
                            Pengajuan layanan yang Anda lakukan akan muncul di sini.
                        </p>
                        @if(Route::has('layanan.index'))
                            <a href="{{ route('layanan.index') }}"
                               class="inline-flex items-center gap-2 mt-6 px-6 py-3 rounded-xl bg-primary text-white text-xs font-black uppercase tracking-wider hover:bg-primary-dark transition">
                                Lihat Layanan <i class="bi bi-arrow-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        {{-- EMPTY STATE saat hasil filter/pencarian kosong --}}
        <div id="noResultState" class="hidden text-center py-20">
            <i class="bi bi-search text-4xl text-slate-200 mb-4"></i>
            <p class="text-slate-400 font-bold text-sm">Tidak ada pengajuan yang cocok dengan pencarian/filter Anda.</p>
        </div>

    </div>
</section>

@include('components.user.modal-edit-submission')
@include('components.user.modal-preview-submission')

@endsection

@push('scripts')
<script>
    let activeStatus = 'all';

    function applyFilters() {
        const query = document.getElementById('fileSearch').value.toLowerCase();
        const cards = document.querySelectorAll('.file-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const code = card.getAttribute('data-code');
            const name = card.getAttribute('data-name');
            const category = card.getAttribute('data-category');
            const status = card.getAttribute('data-status');

            const matchesQuery = code.includes(query) || name.includes(query) || category.includes(query);
            const matchesStatus = activeStatus === 'all' || status === activeStatus;

            if (matchesQuery && matchesStatus) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const noResult = document.getElementById('noResultState');
        if (noResult) {
            noResult.classList.toggle('hidden', visibleCount !== 0 || cards.length === 0);
        }
    }

    function filterFiles() {
        applyFilters();
    }

    function filterByStatus(status, el) {
        activeStatus = status;
        document.querySelectorAll('.filter-chip').forEach(chip => chip.classList.remove('active'));
        el.classList.add('active');
        applyFilters();
    }
</script>
@endpush