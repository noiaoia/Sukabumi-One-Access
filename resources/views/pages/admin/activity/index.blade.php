@extends('layouts.admin')

@section('title', 'Log Aktivitas')
@section('page-title', 'Jejak Audit')
@section('page-subtitle', 'Pantau seluruh riwayat perubahan dan aktivitas')
@section('page-icon', 'bi-clock-history')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp

<style>
    /* 1. ANIMASI & REVEAL */
    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .reveal-row {
        animation: slideInUp 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        opacity: 0;
    }

    /* 2. FILTER BAR PREMIUM */
    .glass-filter {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
        transition: all 0.4s ease;
    }

    .filter-input-premium {
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 16px;
        padding: 10px 16px;
        font-size: 12px;
        font-weight: 800;
        color: #123457;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
    }

    .filter-input-premium:focus {
        border-color: #E8A33D;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(232, 163, 61, 0.1);
        outline: none;
    }

    /* 3. LOG CARD & TABLE */
    .log-card-main {
        background: white;
        border-radius: 35px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 20px 50px -20px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .table-premium thead th {
        background: #0A1E33;
        color: rgba(255,255,255,0.6);
        text-transform: uppercase;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 1.5px;
        padding: 20px 24px;
        border: none;
    }

    .table-premium tbody td {
        padding: 22px 24px;
        vertical-align: middle;
        border-bottom: 1px solid #f8fafc;
        transition: all 0.3s ease;
    }

    .table-premium tr:hover td {
        background: #fbfcfe;
        transform: scale(1.002);
    }

    /* 4. BADGES & CHIPS */
    .badge-premium {
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .badge-registrasi { background: #ECFDF5; color: #10B981; border: 1px solid #D1FAE5; }
    .badge-pembaruan { background: #FFFBEB; color: #D97706; border: 1px solid #FEF3C7; }
    .badge-penghapusan { background: #FEF2F2; color: #EF4444; border: 1px solid #FEE2E2; }
    .badge-default { background: #F8FAFC; color: #64748B; border: 1px solid #E2E8F0; }

    /* Tech Tag for IP */
    .tech-tag {
        background: #123457;
        color: #E8A33D;
        padding: 4px 10px;
        border-radius: 8px;
        font-family: 'Monaco', monospace;
        font-size: 10px;
        font-weight: 700;
    }

    /* Custom Scrollbar for User Agent */
    .ua-text {
        font-size: 9px;
        color: #94a3b8;
        font-weight: 600;
        max-width: 140px;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: help;
    }
</style>

<div class="max-w-6xl mx-auto space-y-8 pb-12">

    {{-- Filter Toolbar --}}
    <div class="glass-filter p-8 rounded-[32px] reveal-row" style="animation-delay: 0.1s">
        <form action="{{ route('admin.activity.index', $portalCode) }}" method="GET" class="flex flex-col lg:flex-row items-end gap-6">
            <div class="w-full grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <label class="flex items-center gap-2 text-[11px] font-black text-primary-dark uppercase tracking-wider ml-1">
                        <i class="bi bi-calendar-event text-accent"></i> Mulai Tanggal
                    </label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="filter-input-premium">
                </div>
                <div class="space-y-3">
                    <label class="flex items-center gap-2 text-[11px] font-black text-primary-dark uppercase tracking-wider ml-1">
                        <i class="bi bi-calendar-check text-accent"></i> Sampai Tanggal
                    </label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="filter-input-premium">
                </div>
            </div>
            
            <div class="flex gap-3 w-full lg:w-auto">
                <button type="submit" class="flex-1 lg:flex-none bg-primary text-white px-8 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-[0.1em] shadow-lg shadow-primary/20 hover:bg-primary-dark active:scale-95 transition-all flex items-center justify-center gap-2">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                @if($startDate || $endDate)
                <a href="{{ route('admin.activity.index', $portalCode) }}" class="bg-red-50 text-red-500 px-5 py-3.5 rounded-2xl text-[11px] font-black uppercase hover:bg-red-100 transition-all flex items-center justify-center">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Main Content --}}
    <div id="log-data" class="log-card-main reveal-row" style="animation-delay: 0.2s">
        {{-- Header Tabel --}}
        <div class="p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-[20px] bg-primary-dark text-accent flex items-center justify-center shadow-xl shadow-primary/10">
                    <i class="bi bi-shield-shaded text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-primary-dark leading-tight">Jejak Digital</h3>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-tighter mt-1">Audit log untuk keamanan dan transparansi sistem</p>
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                <span class="text-[10px] font-black text-primary-dark uppercase">Total {{ number_format($logs->total()) }} Entri Tercatat</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left table-premium">
                <thead>
                    <tr>
                        <th>Operator</th>
                        <th class="text-center">Tindakan</th>
                        <th>Deskripsi Aktivitas</th>
                        <th class="text-center">Akses</th>
                        <th class="text-right">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                    @php
                        // Logika penentuan badge & icon
                        $badgeClass = 'badge-default';
                        $icon = 'bi-info-circle';
                        $act = strtolower($log->activity_name);
                        
                        if(str_contains($act, 'registrasi') || str_contains($act, 'buat')) { 
                            $badgeClass = 'badge-registrasi'; 
                            $icon = 'bi-plus-circle-fill'; 
                        } elseif(str_contains($act, 'update') || str_contains($act, 'pembaruan') || str_contains($act, 'edit')) { 
                            $badgeClass = 'badge-pembaruan'; 
                            $icon = 'bi-pencil-fill'; 
                        } elseif(str_contains($act, 'hapus') || str_contains($act, 'cabut')) { 
                            $badgeClass = 'badge-penghapusan'; 
                            $icon = 'bi-trash-fill'; 
                        }
                    @endphp

                    {{-- BARIS TABEL: Inline style dihapus untuk menghilangkan error VS Code --}}
                    <tr class="reveal-row">
                        <td>
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center text-[11px] font-black shadow-inner">
                                    {{ strtoupper(substr($log->user->name ?? 'OP', 0, 2)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-primary-dark">{{ $log->user->name ?? 'System' }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">@ {{ $log->user->username ?? 'admin' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge-premium {{ $badgeClass }}">
                                <i class="bi {{ $icon }}"></i>
                                {{ $log->activity_name }}
                            </span>
                        </td>
                        <td>
                            <div class="max-w-xs group">
                                <p class="text-[12px] text-slate-600 font-bold leading-relaxed group-hover:text-primary transition-colors">
                                    {{ $log->description }}
                                </p>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="flex flex-col items-center gap-1.5">
                                <span class="tech-tag">{{ $log->ip_address }}</span>
                                <span class="ua-text" title="{{ $log->user_agent }}">
                                    <i class="bi bi-cpu"></i> {{ Str::limit($log->user_agent, 15) }}
                                </span>
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-primary-dark">
                                    {{ $log->created_at->translatedFormat('d M Y') }}
                                </span>
                                <span class="text-[10px] font-black text-accent uppercase tracking-tighter">
                                    {{ $log->created_at->format('H:i') }} <small class="text-slate-300">WIB</small>
                                </span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-32 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                                    <i class="bi bi-database-exclamation text-4xl text-slate-200"></i>
                                </div>
                                <h4 class="text-sm font-black text-slate-400 uppercase tracking-[0.3em]">Data Kosong</h4>
                                <p class="text-[11px] text-slate-300 mt-2 font-bold uppercase">Tidak ada aktivitas pada periode ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer / Paginasi --}}
        @if($logs->hasPages())
        <div class="p-10 bg-slate-50/50 border-t border-slate-100 flex flex-col items-center gap-6">
            {{-- Info Halaman --}}
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} entri
            </span>

            <div class="premium-pagination">
                {{-- Penambahan fragment() di sini --}}
                {{ $logs->withQueryString()->fragment('log-data')->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    /* PAGINASI CUSTOM PREMIUM */
    .premium-pagination .pagination { 
        display: flex; 
        gap: 8px; 
        list-style: none;
        padding: 0;
        margin: 0;
        align-items: center;
    }

    /* Kotak angka dan panah */
    .premium-pagination .page-item .page-link, 
    .premium-pagination .page-item span { 
        width: 42px; 
        height: 42px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        border-radius: 14px; 
        font-size: 11px; 
        font-weight: 900; 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        border: 2px solid #f1f5f9;
        background: white;
        color: #94a3b8;
        text-decoration: none;
    }

    /* Status Aktif (Halaman saat ini) */
    .premium-pagination .page-item.active span { 
        background-color: #123457 !important;
        color: #E8A33D !important; 
        border-color: #123457 !important; 
        box-shadow: 0 10px 20px -5px rgba(18, 52, 87, 0.3); 
    }

    /* Hover Effect */
    .premium-pagination .page-item a:hover { 
        border-color: #E8A33D;
        color: #123457;
        transform: translateY(-3px);
    }

    /* Disabled (Panah saat di ujung) */
    .premium-pagination .page-item.disabled .page-link {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f8fafc;
    }

    /* Sembunyikan label bawaan bootstrap yang tidak perlu */
    .premium-pagination nav svg {
        display: none;
    }

    #log-data {
        /* Memberikan jarak 100px dari atas layar saat "loncat" ke ID ini */
        scroll-margin-top: 100px;
    }

    /* Membuat efek scroll menjadi halus (smooth) */
    html {
        scroll-behavior: smooth;
    }
</style>

@endsection