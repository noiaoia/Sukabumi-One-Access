@extends('layouts.app')

@section('title', 'Pusat Pengaduan - Sukabumi One Access')

@section('content')

<!-- Modal Kamera Ultra-Compact -->
<div id="cameraModal" 
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm transition-opacity duration-300"
     role="dialog" aria-modal="true" aria-labelledby="cameraModalTitle">

    <div id="cameraModalPanel" 
         class="bg-white rounded-[20px] overflow-hidden max-w-sm w-full shadow-2xl border border-white/20 transition-all duration-300 scale-95 opacity-0">
        
        {{-- HEADER --}}
        <div class="bg-[#0A1E33] px-4 py-2.5 border-b-2 border-[#E8A33D] flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="bi bi-camera-fill text-[#E8A33D] text-sm"></i>
                <h4 id="cameraModalTitle" class="text-[10px] font-black text-white uppercase tracking-widest">Kamera Bukti</h4>
            </div>
            <button type="button" onclick="closeCamera()" aria-label="Tutup kamera" class="text-white/50 hover:text-white transition-colors">
                <i class="bi bi-x-lg text-xs"></i>
            </button>
        </div>

        {{-- AREA VIEWPORT --}}
        <div class="relative bg-black aspect-video flex items-center justify-center overflow-hidden">
            <video id="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
            <canvas id="canvas" class="hidden"></canvas>

            {{-- Preview hasil jepretan --}}
            <img id="snapshotPreview" class="hidden absolute inset-0 w-full h-full object-cover" alt="Preview foto bukti">

            {{-- Loading state saat kamera diinisialisasi --}}
            <div id="cameraLoading" class="absolute inset-0 flex flex-col items-center justify-center gap-2 bg-black/80 text-white text-[9px] font-bold uppercase tracking-wide">
                <i class="bi bi-arrow-repeat text-lg animate-spin text-[#E8A33D]"></i>
                Mengaktifkan kamera...
            </div>

            {{-- Error state kalau izin ditolak / tidak ada kamera --}}
            <div id="cameraError" class="hidden absolute inset-0 flex flex-col items-center justify-center gap-1 bg-black/90 text-white text-center px-4">
                <i class="bi bi-camera-video-off text-lg text-red-400"></i>
                <p class="text-[9px] font-bold uppercase">Kamera tidak tersedia</p>
                <p id="cameraErrorMsg" class="text-[7px] text-slate-300">Izinkan akses kamera di pengaturan browser.</p>
            </div>

            {{-- Scanner Frame --}}
            <div class="absolute inset-4 border border-white/5 rounded-lg pointer-events-none">
                <div class="absolute top-0 left-0 w-3 h-3 border-t-2 border-l-2 border-[#E8A33D]"></div>
                <div class="absolute top-0 right-0 w-3 h-3 border-t-2 border-r-2 border-[#E8A33D]"></div>
                <div class="absolute bottom-0 left-0 w-3 h-3 border-b-2 border-l-2 border-[#E8A33D]"></div>
                <div class="absolute bottom-0 right-0 w-3 h-3 border-b-2 border-r-2 border-[#E8A33D]"></div>
            </div>

            {{-- Label Live --}}
            <div id="liveLabel" class="absolute top-2 left-2">
                <span class="flex items-center gap-1 bg-red-600 text-white text-[7px] font-black px-1.5 py-0.5 rounded-sm uppercase animate-pulse">
                    LIVE
                </span>
            </div>

            {{-- Tombol ganti kamera (depan/belakang) --}}
            <button type="button" id="switchCameraBtn" onclick="switchCamera()"
                    class="hidden absolute top-2 right-2 w-6 h-6 bg-black/50 rounded-full items-center justify-center text-white text-xs">
                <i class="bi bi-arrow-repeat"></i>
            </button>

            {{-- Flash effect saat jepret --}}
            <div id="flashEffect" class="hidden absolute inset-0 bg-white pointer-events-none"></div>
        </div>

        {{-- FOOTER: Mode Live --}}
        <div id="footerLive" class="px-5 py-3 bg-white flex items-center justify-between">
            <div class="text-left">
                <p class="text-[9px] font-black text-[#123457] uppercase leading-none">Siap Jepret</p>
                <p class="text-[7px] text-slate-400 font-bold uppercase mt-1">Posisikan Objek</p>
            </div>

            <button type="button" onclick="takeSnapshot()" class="group relative flex items-center justify-center">
                <span class="absolute inline-flex h-10 w-10 rounded-full bg-[#123457]/10 animate-ping"></span>
                <div class="relative w-10 h-10 bg-[#123457] text-[#E8A33D] rounded-full border-2 border-slate-50 shadow-lg flex items-center justify-center active:scale-90 transition-transform">
                    <i class="bi bi-camera-fill text-sm"></i>
                </div>
            </button>
        </div>

        {{-- FOOTER: Mode Preview (setelah jepret) --}}
        <div id="footerPreview" class="hidden px-5 py-3 bg-white flex items-center justify-between gap-3">
            <button type="button" onclick="retakeSnapshot()"
                    class="flex-1 flex items-center justify-center gap-1 py-2 rounded-lg border border-slate-300 text-[9px] font-black uppercase text-slate-500">
                <i class="bi bi-arrow-counterclockwise"></i> Ulangi
            </button>
            <button type="button" onclick="confirmSnapshot()"
                    class="flex-1 flex items-center justify-center gap-1 py-2 rounded-lg bg-[#123457] text-[#E8A33D] text-[9px] font-black uppercase">
                <i class="bi bi-check-lg"></i> Gunakan Foto
            </button>
        </div>

    </div>
</div>

{{-- Leaflet (peta open-source, tanpa perlu API Key seperti Google Maps) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<style>
    /* 1. HERO PENGADUAN */
    .complaint-hero {
        background-color: #0A1E33;
        position: relative;
        overflow: hidden;
        padding: 5rem 1.5rem 8rem;
        border-bottom: 4px solid #E8A33D;
    }

    .bg-dot-matrix {
        position: absolute; inset: 0; opacity: 0.12;
        background-image: radial-gradient(#E8A33D 1px, transparent 0);
        background-size: 30px 30px;
    }

    /* 2. STATS CARD */
    .stats-box {
        background: #ffffff;
        border-radius: 2rem;
        margin-top: -5rem;
        position: relative;
        z-index: 30;
        box-shadow: 0 20px 50px rgba(10, 30, 51, 0.1);
        border: 1px solid #f1f5f9;
    }

    /* 3. STEPPER ALUR */
    .step-item {
        position: relative;
        text-align: center;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 1.5rem;
        padding: 1.5rem 1rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: all 0.3s ease;
    }

    .step-item:hover {
        border-color: #E8A33D;
        transform: translateY(-5px);
    }

    .step-number {
        width: 40px; height: 40px;
        background: #123457;
        color: #E8A33D;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem;
        font-weight: 800;
        box-shadow: 0 10px 20px rgba(18, 52, 87, 0.2);
    }

    /* ====================================================
       4. FORM PENGADUAN — DISERAGAMKAN DENGAN GAYA "INSTANSI"
    ==================================================== */
    .command-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        overflow: hidden;
        max-width: 900px;
        margin: 0 auto;
    }
    .command-header { background: #0A1E33; padding: 32px 40px; border-bottom: 4px solid #E8A33D; }
    .command-header h2 { color: #ffffff; font-size: 20px; font-weight: 800; margin: 0; }
    .command-header p { color: rgba(255,255,255,0.6); font-size: 12px; margin-top: 5px; }

    .form-section { padding: 28px 40px; }
    .form-section + .form-section { border-top: 1px solid #F1F5F9; }

    .section-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
    .section-number {
        width: 26px; height: 26px;
        border-radius: 8px;
        background: #123457;
        color: #E8A33D;
        font-size: 11px;
        font-weight: 900;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .section-heading h3 { font-size: 13px; font-weight: 800; color: #123457; margin: 0; }
    .section-heading p { font-size: 11px; color: #94a3b8; margin: 2px 0 0; }

    .input-field-wrapper { margin-bottom: 18px; }
    .label-command { display: block; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 8px; margin-left: 4px; }
    .input-group-custom { position: relative; display: flex; align-items: center; width: 100%; }
    .input-control-command { width: 100%; padding: 14px 20px 14px 50px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; font-size: 14px; font-weight: 700; color: #123457; transition: all 0.3s ease; font-family: inherit; }
    .input-control-command:focus { outline: none; border-color: #123457; background: #ffffff; box-shadow: 0 0 0 4px rgba(18, 52, 87, 0.05); }
    .input-icon-left { position: absolute; left: 18px; color: #94a3b8; font-size: 1.1rem; z-index: 5; pointer-events: none; }

    /* Textarea varian (tanpa icon kiri) */
    .textarea-command { padding: 14px 18px !important; height: auto; }

    /* 5. PETA LOKASI KEJADIAN */
    #mapPicker {
        width: 100%;
        height: 280px;
        border-radius: 16px;
        border: 1.5px solid #E2E8F0;
        overflow: hidden;
        position: relative;
        z-index: 1;
    }
    .map-hint {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 10.5px;
        color: #94a3b8;
        margin-top: 8px;
        font-weight: 600;
    }
    .btn-locate-me {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: #123457;
        color: #E8A33D;
        border: none;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.25s ease;
    }
    .btn-locate-me:hover { background: #E8A33D; color: #0A1E33; transform: translateY(-1px); }
    .btn-locate-me:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .coords-readout {
        display: flex;
        gap: 10px;
        margin-top: 12px;
    }
    .coords-readout .chip {
        flex: 1;
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 11px;
        font-weight: 700;
        color: #123457;
    }
    .coords-readout .chip span { display: block; font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; font-weight: 800; margin-bottom: 3px; }

    /* 6. UPLOAD FOTO — Kamera & Galeri */
    .photo-source-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }
    .photo-source-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 1.5rem 1rem;
        background: #F8FAFC;
        border: 1.5px dashed #E2E8F0;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.25s ease;
        text-align: center;
    }
    .photo-source-btn:hover { border-color: #E8A33D; background: #fffef9; }
    .photo-source-btn .icon-circle-btn {
        width: 42px; height: 42px;
        border-radius: 12px;
        background: #123457;
        color: #E8A33D;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
    }
    .photo-source-btn span.label { font-size: 11px; font-weight: 800; color: #123457; }
    .photo-source-btn span.sub { font-size: 9.5px; color: #94a3b8; font-weight: 600; }

    .upload-preview-box {
        border: 1.5px solid #E2E8F0;
        border-radius: 16px;
        padding: 1rem;
        background: #F8FAFC;
        display: none;
        align-items: center;
        gap: 14px;
    }
    .upload-preview-box.is-active { display: flex; }
    .upload-preview-box img {
        width: 72px; height: 72px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #E2E8F0;
        flex-shrink: 0;
    }
    .upload-preview-box .meta { flex: 1; min-width: 0; }
    .upload-preview-box .meta p.name { font-size: 11.5px; font-weight: 800; color: #123457; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .upload-preview-box .meta p.size { font-size: 10px; color: #94a3b8; font-weight: 600; margin-top: 2px; }
    .btn-remove-photo {
        width: 32px; height: 32px;
        border-radius: 10px;
        background: #FEE2E2;
        color: #DC2626;
        display: flex; align-items: center; justify-content: center;
        border: none;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .btn-remove-photo:hover { background: #DC2626; color: #fff; }

    /* Toggle Kontak Opsional */
    .contact-toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 14px;
    }
    .contact-toggle-row p.t { font-size: 11.5px; font-weight: 800; color: #123457; }
    .contact-toggle-row p.d { font-size: 10px; color: #94a3b8; font-weight: 600; margin-top: 2px; }
    .switch-command { position: relative; display: inline-block; width: 42px; height: 24px; flex-shrink: 0; }
    .switch-command input { opacity: 0; width: 0; height: 0; }
    .switch-slider { position: absolute; cursor: pointer; inset: 0; background-color: #CBD5E1; transition: .3s; border-radius: 999px; }
    .switch-slider::before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
    .switch-command input:checked + .switch-slider { background-color: #123457; }
    .switch-command input:checked + .switch-slider::before { transform: translateX(18px); }

    /* Footer aksi */
    .form-footer-command {
        padding: 25px 40px;
        background: #F8FAFC;
        border-top: 1px solid #e2e8f0;
    }

    @media (max-width: 640px) {
        .step-number { width: 32px; height: 32px; font-size: 0.8rem; border-radius: 10px; }
        .step-item h4 { font-size: 0.75rem; }
        .step-item p { font-size: 10px; line-height: 1.4; padding: 0 !important; }
        .form-section { padding: 24px 20px; }
        .command-header { padding: 24px 20px; }
        .form-footer-command { padding: 20px; }
        .photo-source-grid { grid-template-columns: 1fr; }
    }
</style>

{{-- 1. HERO SECTION --}}
<section class="complaint-hero">
    <div class="bg-dot-matrix"></div>
    <div class="max-w-7xl mx-auto relative z-10 text-center">
        <div class="reveal">
            <nav class="flex justify-center items-center gap-2 text-accent text-[10px] font-bold uppercase tracking-[0.2em] mb-4">
                <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                <i class="bi bi-chevron-right text-[8px]"></i>
                <span>Pusat Pengaduan</span>
            </nav>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight tracking-tighter">
                Suara Anda, <br>
                <span class="text-accent italic">Membangun Sukabumi.</span>
            </h1>
            <p class="text-slate-400 mt-6 max-w-2xl mx-auto text-sm sm:text-base leading-relaxed">
                Laporkan kendala pelayanan publik atau gangguan fasilitas kota secara cepat, transparan, dan terintegrasi dengan instansi terkait.
            </p>
        </div>
    </div>
</section>

{{-- 2. STATS SECTION --}}
<section class="max-w-5xl mx-auto px-4 sm:px-8">
    <div class="stats-box grid grid-cols-2 md:grid-cols-4 divide-x divide-slate-100 reveal" id="complaintStatsBar">
        <div class="p-6 text-center">
            <span class="block text-2xl font-black text-primary roll-number" data-final="{{ $totalReports }}+">0</span>
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Total Laporan</span>
        </div>
        <div class="p-6 text-center">
            <span class="block text-2xl font-black text-emerald-500 roll-number" data-final="{{ $resolvedPercentage }}%">0%</span>
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Terselesaikan</span>
        </div>
        <div class="p-6 text-center">
            <span class="block text-2xl font-black text-primary roll-number" data-final="{{ $responseTime }}">0h</span>
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Waktu Respon</span>
        </div>
        <div class="p-6 text-center">
            <span class="block text-2xl font-black text-amber-500">Live</span>
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Monitoring</span>
        </div>
    </div>
</section>

{{-- 3. ALUR PENGADUAN --}}
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="text-center mb-12 sm:mb-16 reveal">
            <p class="text-accent text-[10px] font-black uppercase tracking-[0.3em] mb-2">Prosedur Laporan</p>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-primary-dark tracking-tight leading-tight">Bagaimana Alurnya?</h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-8">
            <div class="reveal">
                <div class="step-item">
                    <div class="step-number shadow-primary/20">1</div>
                    <h4 class="font-bold text-primary mb-2 text-center leading-tight">Kirim Laporan</h4>
                    <p class="text-slate-400 text-center">Isi formulir pengaduan dengan data yang valid.</p>
                </div>
            </div>
            <div class="reveal" style="transition-delay: 100ms">
                <div class="step-item">
                    <div class="step-number shadow-primary/20">2</div>
                    <h4 class="font-bold text-primary mb-2 text-center leading-tight">Verifikasi</h4>
                    <p class="text-slate-400 text-center">Admin akan memeriksa keabsahan laporan Anda.</p>
                </div>
            </div>
            <div class="reveal" style="transition-delay: 200ms">
                <div class="step-item">
                    <div class="step-number shadow-primary/20">3</div>
                    <h4 class="font-bold text-primary mb-2 text-center leading-tight">Tindak Lanjut</h4>
                    <p class="text-slate-400 text-center">Laporan diteruskan ke dinas terkait.</p>
                </div>
            </div>
            <div class="reveal" style="transition-delay: 300ms">
                <div class="step-item">
                    <div class="step-number shadow-primary/20">4</div>
                    <h4 class="font-bold text-primary mb-2 text-center leading-tight">Selesai</h4>
                    <p class="text-slate-400 text-center">Warga mendapat notifikasi penyelesaian.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ====================================================
     4. FORM PENGADUAN — Gaya "Command Card" seperti Instansi
==================================================== --}}
<section class="py-20 bg-slate-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-8">
        <div class="command-card reveal">

            <div class="command-header">
                <div class="flex items-center justify-between">
                    <div>
                        <h2>Buat Laporan Pengaduan</h2>
                        <p>Identitas pelapor akan kami rahasiakan secara default (Anonim).</p>
                    </div>
                    <div class="hidden md:flex w-12 h-12 rounded-xl bg-white/10 items-center justify-center text-white border border-white/10">
                        <i class="bi bi-megaphone-fill text-xl text-accent"></i>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data" id="pengaduanForm" novalidate>
                @csrf

                {{-- ========== SECTION 1: DETAIL LAPORAN ========== --}}
                <div class="form-section">
                    <div class="section-heading">
                        <span class="section-number">1</span>
                        <div>
                            <h3>Detail Laporan</h3>
                            <p>Judul, kategori, dan penjelasan lengkap keluhan Anda.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
                        {{-- Judul Laporan (Full Width) --}}
                        <div class="input-field-wrapper md:col-span-2">
                            <label class="label-command">Judul Laporan</label>
                            <div class="input-group-custom">
                                <input type="text" name="title" value="{{ old('title') }}" class="input-control-command @error('title') border-red-400 @enderror" placeholder="Contoh: Lampu Jalan Mati" required>
                                @error('title') <p class="text-[10px] text-red-500 mt-1 ml-1">{{ $message }}</p> @enderror
                                <i class="bi bi-card-text input-icon-left"></i>
                            </div>
                        </div>

                        {{-- Kategori (Kolom Kiri) --}}
                        <div class="input-field-wrapper">
                            <label class="label-command">Kategori</label>
                            <div class="input-group-custom">
                                <select name="category" class="input-control-command appearance-none" required>
                                    <option value="" disabled {{ old('category') ? '' : 'selected' }}>Pilih Kategori...</option>
                                    @foreach(['Infrastruktur','Kesehatan','Pelayanan','Lingkungan','Lainnya'] as $cat)
                                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                                <i class="bi bi-grid-1x2 input-icon-left"></i>
                                <i class="bi bi-chevron-down absolute right-4 text-slate-400 pointer-events-none"></i>
                            </div>
                        </div>

                        {{-- TANGGAL KEJADIAN (Kolom Kanan) --}}
                        <div class="input-field-wrapper">
                            <label class="label-command">Tanggal Kejadian</label>
                            <div class="input-group-custom">
                                <input type="date" name="incident_date" value="{{ old('incident_date') }}" class="input-control-command" required>
                                <i class="bi bi-calendar-event input-icon-left"></i>
                            </div>
                            <p class="text-[9px] text-slate-400 mt-2 ml-1">*Kapan peristiwa ini terjadi?</p>
                        </div>

                        {{-- Isi Laporan (Full Width) --}}
                        <div class="input-field-wrapper md:col-span-2">
                            <label class="label-command">Isi Laporan / Keluhan</label>
                            <textarea name="description" class="input-control-command textarea-command" rows="5" placeholder="Silahkan isi detail laporan anda" required>{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ========== SECTION 2: LOKASI KEJADIAN ========== --}}
                <div class="form-section">
                    <div class="section-heading">
                        <span class="section-number">2</span>
                        <div>
                            <h3>Lokasi Kejadian</h3>
                            <p>Tandai titik lokasi di peta agar petugas mudah menemukannya.</p>
                        </div>
                    </div>

                    <div class="input-field-wrapper">
                        <div class="flex items-center justify-between mb-3">
                            <label class="label-command !mb-0">Titik Koordinat</label>
                            <button type="button" class="btn-locate-me" id="btnLocateMe" onclick="locateMe()">
                                <i class="bi bi-crosshair"></i> Gunakan Lokasi Saya
                            </button>
                        </div>

                        <div id="mapPicker"></div>
                        <p class="map-hint"><i class="bi bi-info-circle"></i> Klik/tap di peta atau geser penanda untuk memindahkan titik lokasi.</p>

                        <div class="coords-readout">
                            <div class="chip"><span>Latitude</span><span id="latReadout">-6.927700</span></div>
                            <div class="chip"><span>Longitude</span><span id="lngReadout">106.926900</span></div>
                        </div>
                        
                        <input type="hidden" name="latitude" id="ins_lat" value="{{ old('latitude', -6.9277) }}">
                        <input type="hidden" name="longitude" id="ins_lng" value="{{ old('longitude', 106.9269) }}">
                    </div>

                    <div class="input-field-wrapper">
                        <label class="label-command">Alamat / Patokan Lokasi</label>
                        <div class="input-group-custom">
                            <input type="text" name="alamat_kejadian" id="alamatKejadian" value="{{ old('alamat_kejadian') }}" class="input-control-command" placeholder="Terisi otomatis dari peta, atau tulis manual...">
                            <i class="bi bi-geo-alt input-icon-left"></i>
                        </div>
                    </div>
                </div>

                {{-- ========== SECTION 3: LAMPIRAN FOTO ========== --}}
                <div class="form-section">
                    <div class="section-heading">
                        <span class="section-number">3</span>
                        <div>
                            <h3>Lampiran Foto Bukti</h3>
                            <p>Potret langsung dari kamera atau pilih dari galeri (Maks. 2MB).</p>
                        </div>
                    </div>

                    <div class="photo-source-grid">
                        <div class="photo-source-btn" onclick="triggerCamera()">
                            <span class="icon-circle-btn">
                                <i class="bi bi-camera-fill"></i>
                            </span>
                            <span class="label">Ambil Foto</span>
                            <span class="sub">Gunakan kamera langsung</span>
                        </div>
                        <label class="photo-source-btn" for="galleryInput">
                            <span class="icon-circle-btn"><i class="bi bi-images"></i></span>
                            <span class="label">Pilih dari Galeri</span>
                            <span class="sub">Unggah foto tersimpan</span>
                        </label>
                    </div>

                    {{-- Input kamera: capture memaksa buka kamera di HP --}}
                    <input type="file" id="cameraInput" class="hidden" accept="image/*" capture="environment" onchange="handlePhotoPick(this)">
                    {{-- Input galeri: tanpa atribut capture --}}
                    <input type="file" id="galleryInput" class="hidden" accept="image/*" onchange="handlePhotoPick(this)">
                    {{-- Input asli yang benar-benar dikirim ke server --}}
                    <input type="file" name="attachment" id="finalAttachment" class="hidden">

                    <div class="upload-preview-box" id="previewBox">
                        <img id="previewImg" src="" alt="Preview">
                        <div class="meta">
                            <p class="name" id="previewName">-</p>
                            <p class="size" id="previewSize">-</p>
                        </div>
                        <button type="button" class="btn-remove-photo" onclick="removePhoto()">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>

                {{-- ========== SECTION 4: KONTAK OPSIONAL ========== --}}
                <div class="form-section">
                    <div class="section-heading">
                        <span class="section-number">4</span>
                        <div>
                            <h3>Kontak Balasan (Opsional)</h3>
                            <p>Aktifkan jika Anda ingin dihubungi terkait tindak lanjut laporan.</p>
                        </div>
                    </div>

                    <div class="contact-toggle-row">
                        <div>
                            <p class="t">Sertakan Kontak Saya</p>
                            <p class="d">Laporan tetap anonim ke publik, kontak hanya untuk internal admin.</p>
                        </div>
                        <label class="switch-command">
                            <input type="checkbox" id="toggleContact" onchange="toggleContactField()" {{ old('phone_contact') || old('email_contact') ? 'checked' : '' }}>
                            <span class="switch-slider"></span>
                        </label>
                    </div>

                    {{-- Kolom yang muncul saat di-klik (Terbagi 2) --}}
                    <div id="contactFieldWrapper" class="{{ old('phone_contact') || old('email_contact') ? '' : 'hidden' }} space-y-4 mt-4 animate-success-slide">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            
                            {{-- Input WhatsApp --}}
                            <div class="input-field-wrapper">
                                <label class="label-command">No. WhatsApp Aktif</label>
                                <div class="input-group-custom">
                                    <input type="tel" name="phone_contact" id="whatsappInput" value="{{ old('phone_contact') }}" class="input-control-command" maxlength="13">
                                    <i class="bi bi-whatsapp input-icon-left"></i>
                                </div>
                                <p class="text-[9px] text-slate-400 mt-2 ml-1">*Maksimal 13 digit angka</p>
                            </div>

                            {{-- Input Email --}}
                            <div class="input-field-wrapper">
                                <label class="label-command">Alamat Email</label>
                                <div class="input-group-custom">
                                    <input type="email" name="email_contact" id="emailInput" value="{{ old('email_contact') }}" class="input-control-command">
                                    <i class="bi bi-envelope-at-fill input-icon-left"></i>
                                </div>
                                <p class="text-[9px] text-slate-400 mt-2 ml-1">*Gunakan format email valid (@)</p>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="form-footer-command">
                    <button type="submit" id="btnSubmit" class="w-full bg-primary text-white py-4 rounded-2xl font-bold text-sm shadow-xl shadow-primary/20 hover:bg-primary-dark transition-all active:scale-95">
                        Kirim Laporan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- PANGGIL COMPONENT --}}
<x-user.success-modal />

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    /**
     * 1. ANIMASI ANGKA (Rolling Number)
     */
    function rollNumber(el, finalText) {
        if (!el || !finalText) return;
        const chars = finalText.split('');
        let frame = 0;
        const timer = setInterval(() => {
            frame++;
            const display = chars.map((char, i) => {
                if (!/[0-9]/.test(char)) return char;
                if (frame >= Math.round(((i + 1) / chars.length) * 20)) return char;
                return Math.floor(Math.random() * 10);
            }).join('');
            el.textContent = display;
            if (frame >= 25) clearInterval(timer);
        }, 40);
    }

    /**
     * 2. PETA LOKASI (Leaflet + OpenStreetMap — gratis, tanpa API Key)
     */
    let pengaduanMap, pengaduanMarker;
    const DEFAULT_LAT = -6.9277;
    const DEFAULT_LNG = 106.9269;

    function initMapPicker() {
        const lat = parseFloat(document.getElementById('ins_lat').value) || DEFAULT_LAT;
        const lng = parseFloat(document.getElementById('ins_lng').value) || DEFAULT_LNG;

        pengaduanMap = L.map('mapPicker', { zoomControl: true }).setView([lat, lng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(pengaduanMap);

        pengaduanMarker = L.marker([lat, lng], { draggable: true }).addTo(pengaduanMap);
        document.getElementById('latReadout').textContent = lat.toFixed(6);
        document.getElementById('lngReadout').textContent = lng.toFixed(6);

        pengaduanMarker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            updateCoords(pos.lat, pos.lng);
        });

        pengaduanMap.on('click', function (e) {
            pengaduanMarker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });
    }

    function updateCoords(lat, lng) {
        document.getElementById('ins_lat').value = lat.toFixed(6);
        document.getElementById('ins_lng').value = lng.toFixed(6);
        document.getElementById('latReadout').textContent = lat.toFixed(6);
        document.getElementById('lngReadout').textContent = lng.toFixed(6);
        reverseGeocode(lat, lng);
    }

    // Reverse geocoding pakai Nominatim (OpenStreetMap) — gratis, tanpa API Key
    let geocodeTimeout;
    function reverseGeocode(lat, lng) {
        clearTimeout(geocodeTimeout);
        geocodeTimeout = setTimeout(async () => {
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                const data = await res.json();
                if (data && data.display_name) {
                    document.getElementById('alamatKejadian').value = data.display_name;
                }
            } catch (err) {
                console.warn('Reverse geocoding gagal, isi alamat manual.', err);
            }
        }, 500);
    }

    function locateMe() {
        const btn = document.getElementById('btnLocateMe');
        if (!navigator.geolocation) {
            alert('Perangkat Anda tidak mendukung layanan lokasi.');
            return;
        }
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Mencari lokasi...';

        navigator.geolocation.getCurrentPosition(function (pos) {
            const { latitude, longitude } = pos.coords;
            pengaduanMap.setView([latitude, longitude], 17);
            pengaduanMarker.setLatLng([latitude, longitude]);
            updateCoords(latitude, longitude);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-crosshair"></i> Gunakan Lokasi Saya';
        }, function () {
            alert('Gagal mengambil lokasi. Pastikan izin lokasi diaktifkan, atau tandai manual di peta.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-crosshair"></i> Gunakan Lokasi Saya';
        }, { enableHighAccuracy: true, timeout: 10000 });
    }

    /**
     * 3. FOTO — Kamera / Galeri, digabung ke satu input file sebelum submit
     */
    function handlePhotoPick(sourceInput) {
        const file = sourceInput.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran foto maksimal 2MB. Silakan pilih foto lain.');
            sourceInput.value = '';
            return;
        }

        // Salin file terpilih ke input asli yang akan dikirim ke server
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('finalAttachment').files = dt.files;

        // Tampilkan preview
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('previewName').textContent = file.name;
            document.getElementById('previewSize').textContent = (file.size / 1024).toFixed(0) + ' KB';
            document.getElementById('previewBox').classList.add('is-active');
        };
        reader.readAsDataURL(file);
    }

    function removePhoto() {
        document.getElementById('cameraInput').value = '';
        document.getElementById('galleryInput').value = '';
        document.getElementById('finalAttachment').value = '';
        document.getElementById('previewBox').classList.remove('is-active');
    }

    /**
     * 4. TOGGLE KONTAK OPSIONAL
     */
    function toggleContactField() {
        const isChecked = document.getElementById('toggleContact').checked;
        const wrapper = document.getElementById('contactFieldWrapper');
        const whatsappInput = document.getElementById('whatsappInput');
        const emailInput = document.getElementById('emailInput');

        if (isChecked) {
            wrapper.classList.remove('hidden');
            // Tambahkan required jika diaktifkan (opsional)
            whatsappInput.setAttribute('required', 'required');
        } else {
            wrapper.classList.add('hidden');
            // Kosongkan nilai saat dimatikan agar tidak terkirim ke server
            whatsappInput.value = '';
            emailInput.value = '';
            whatsappInput.removeAttribute('required');
        }
    }

    // Tambahkan listener untuk validasi angka di input WhatsApp
    document.addEventListener('DOMContentLoaded', function() {
        const whatsappInput = document.getElementById('whatsappInput');
        
        if (whatsappInput) {
            whatsappInput.addEventListener('input', function(e) {
                // Hanya izinkan angka
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Batasi maksimal 13 karakter (tambahan proteksi JS)
                if (this.value.length > 13) {
                    this.value = this.value.slice(0, 13);
                }
            });
        }
    });

    /**
     * 5. DOM LOADED LOGIC
     */
    document.addEventListener('DOMContentLoaded', function() {
        initMapPicker();

        // --- A. Intersection Observer untuk Stats ---
        const statsBar = document.getElementById('complaintStatsBar');
        if (statsBar) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        document.querySelectorAll('.roll-number').forEach(el => {
                            rollNumber(el, el.getAttribute('data-final'));
                        });
                        observer.unobserve(statsBar);
                    }
                });
            }, { threshold: 0.5 });
            observer.observe(statsBar);
        }

        // --- B. Form Loading State + Validasi Foto ---
        const form = document.getElementById('pengaduanForm');
        const btn = document.getElementById('btnSubmit');
        if (form && btn) {
            form.addEventListener('submit', function(e) {
                if (!document.getElementById('finalAttachment').files.length) {
                    e.preventDefault();
                    alert('Mohon lampirkan foto bukti (ambil foto atau pilih dari galeri) sebelum mengirim laporan.');
                    return;
                }
                if (form.checkValidity()) {
                    btn.disabled = true;
                    btn.classList.add('opacity-80', 'cursor-not-allowed');
                    btn.innerHTML = `<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses Laporan...`;
                }
            });
        }
    });

    // Cek apakah ini HP atau Laptop
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

    function triggerCamera() {
        if (isMobile) {
            // Jika di HP, biarkan input file capture bekerja (langsung buka kamera HP)
            document.getElementById('cameraInput').click();
        } else {
            // Jika di Laptop, buka modal Webcam
            openCamera();
        }
    }

    // --- LOGIKA WEBCAM LAPTOP ---
    let stream = null;

    async function openCamera() {
        const modal = document.getElementById('cameraModal');
        const video = document.getElementById('video');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            video.srcObject = stream;
        } catch (err) {
            alert("Kamera tidak ditemukan atau akses ditolak.");
            closeCamera();
        }
    }

    function closeCamera() {
        const modal = document.getElementById('cameraModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    }

    function takeSnapshot() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');
        
        // Set ukuran canvas sesuai video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Gambar foto dari video ke canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Ubah ke Blob (File)
        canvas.toBlob((blob) => {
            const file = new File([blob], "captured_evidence.jpg", { type: "image/jpeg" });
            
            // Kirim ke input file final
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('finalAttachment').files = dt.files;

            // Tampilkan preview
            document.getElementById('previewImg').src = URL.createObjectURL(blob);
            document.getElementById('previewName').textContent = "Hasil Potret Kamera";
            document.getElementById('previewSize').textContent = (file.size / 1024).toFixed(0) + ' KB';
            document.getElementById('previewBox').classList.add('is-active');
            
            closeCamera();
        }, 'image/jpeg');
    }

    let currentStream = null;
    let currentFacingMode = 'environment';

    function openCamera() {
        const modal = document.getElementById('cameraModal');
        const panel = document.getElementById('cameraModalPanel');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });
        startCamera();
    }

    function closeCamera() {
        const modal = document.getElementById('cameraModal');
        const panel = document.getElementById('cameraModalPanel');
        panel.classList.add('scale-95', 'opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            stopCamera();
            retakeSnapshot(); // reset ke mode live
        }, 200);
    }

    async function startCamera() {
        const loading = document.getElementById('cameraLoading');
        const error = document.getElementById('cameraError');
        const video = document.getElementById('video');
        loading.classList.remove('hidden');
        error.classList.add('hidden');

        try {
            currentStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: currentFacingMode }
            });
            video.srcObject = currentStream;
            loading.classList.add('hidden');

            // Tampilkan tombol switch kamera jika device punya lebih dari 1 kamera
            const devices = await navigator.mediaDevices.enumerateDevices();
            const cams = devices.filter(d => d.kind === 'videoinput');
            document.getElementById('switchCameraBtn').classList.toggle('flex', cams.length > 1);
            document.getElementById('switchCameraBtn').classList.toggle('hidden', cams.length <= 1);
        } catch (err) {
            loading.classList.add('hidden');
            error.classList.remove('hidden');
            document.getElementById('cameraErrorMsg').textContent =
                err.name === 'NotAllowedError'
                    ? 'Izinkan akses kamera di pengaturan browser.'
                    : 'Tidak ada kamera yang terdeteksi.';
        }
    }

    function stopCamera() {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
    }

    function switchCamera() {
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        stopCamera();
        startCamera();
    }

    function takeSnapshot() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        // Flash effect
        const flash = document.getElementById('flashEffect');
        flash.classList.remove('hidden');
        setTimeout(() => flash.classList.add('hidden'), 150);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('snapshotPreview').src = dataUrl;
        document.getElementById('snapshotPreview').classList.remove('hidden');
        video.classList.add('hidden');
        document.getElementById('liveLabel').classList.add('hidden');
        document.getElementById('switchCameraBtn').classList.add('hidden');

        document.getElementById('footerLive').classList.add('hidden');
        document.getElementById('footerPreview').classList.remove('hidden');
        document.getElementById('footerPreview').classList.add('flex');
    }

    function retakeSnapshot() {
        document.getElementById('snapshotPreview').classList.add('hidden');
        document.getElementById('video').classList.remove('hidden');
        document.getElementById('liveLabel').classList.remove('hidden');
        document.getElementById('footerPreview').classList.add('hidden');
        document.getElementById('footerPreview').classList.remove('flex');
        document.getElementById('footerLive').classList.remove('hidden');
    }

    function confirmSnapshot() {
        const canvas = document.getElementById('canvas');
        
        // 1. Ubah Canvas menjadi Blob (data file)
        canvas.toBlob((blob) => {
            // 2. Buat objek File layaknya memilih dari folder
            const file = new File([blob], "foto_bukti.jpg", { type: "image/jpeg" });
            
            // 3. Masukkan ke input file utama (#finalAttachment) menggunakan DataTransfer
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('finalAttachment').files = dt.files;

            // 4. Update Preview di Form Utama
            document.getElementById('previewImg').src = URL.createObjectURL(blob);
            document.getElementById('previewName').textContent = "Hasil Potret Kamera.jpg";
            document.getElementById('previewSize').textContent = (file.size / 1024).toFixed(0) + ' KB';
            
            // 5. Tampilkan kotak preview di form utama
            document.getElementById('previewBox').classList.add('is-active');

            // 6. Tutup Modal
            closeCamera();
            
            // Opsional: Scroll halus ke arah preview agar user tahu foto sudah terpasang
            document.getElementById('previewBox').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 'image/jpeg', 0.9);
    }

    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !document.getElementById('cameraModal').classList.contains('hidden')) {
            closeCamera();
        }
    });
</script>
@endpush