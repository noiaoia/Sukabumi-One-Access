@extends('layouts.admin')

@section('title', 'Tambah Formulir Mandiri')
@section('page-title', 'Registrasi Formulir')
@section('page-icon', 'bi-file-earmark-plus-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
@endphp



<style>
    /* 1. Card & Layout Base */
    .command-card { background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; max-width: 800px; margin: 0 auto; }
    .command-header { background: #0A1E33; padding: 32px 40px; border-bottom: 4px solid #E8A33D; }
    .command-header h2 { color: #ffffff; font-size: 20px; font-weight: 800; margin: 0; }
    .command-header p { color: rgba(255,255,255,0.6); font-size: 12px; margin-top: 5px; }

    /* 2. Section grouping */
    .form-section { padding: 35px 40px; }

    .section-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 25px; }
    .section-number {
        width: 26px; height: 26px; border-radius: 8px;
        background: #123457; color: #E8A33D;
        font-size: 11px; font-weight: 900;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .section-heading h3 { font-size: 14px; font-weight: 800; color: #123457; margin: 0; text-transform: uppercase; }

    /* 3. Form Styling */
    .input-field-wrapper { margin-bottom: 22px; }
    .label-command { display: block; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 8px; margin-left: 4px; }
    .input-group-custom { position: relative; display: flex; align-items: center; width: 100%; }
    .input-control-command { width: 100%; padding: 14px 20px 14px 50px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 16px; font-size: 14px; font-weight: 700; color: #123457; transition: all 0.3s ease; }
    .input-control-command:focus { outline: none; border-color: #123457; background: #ffffff; box-shadow: 0 0 0 4px rgba(18, 52, 87, 0.05); }
    .input-icon-left { position: absolute; left: 18px; color: #94a3b8; font-size: 1.1rem; z-index: 5; pointer-events: none; }

    /* 4. Action Buttons */
    .form-footer{
        padding: 25px 40px;
        background: #F8FAFC;
        border-top: 1px solid #E2E8F0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    /* Secondary Button */
    .btn-command-cancel{
        padding: 12px 24px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        background: #E2E8F0;
        border: 1px solid #CBD5E1;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        transition: all .3s ease;
    }

    .btn-command-cancel:hover{
        background: #D1D5DB;
        color: #1E293B;
        transform: translateY(-1px);
    }

    /* Primary Button */
    .btn-command-save{
        padding: 12px 32px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 800;
        color: #FFFFFF;
        background: #123457;
        border: none;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all .3s ease;
        box-shadow: 0 10px 15px -3px rgba(18,52,87,.2);
    }

    .btn-command-save:hover{
        background: #E8A33D;
        color: #0A1E33;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(232,163,61,.28);
    }

    .btn-command-cancel:active,
    .btn-command-save:active{
        transform: translateY(0);
    }

    /* --- CUSTOM FILE UPLOAD --- */
    .file-upload-wrapper { position: relative; width: 100%; cursor: pointer; }
    .file-upload-box { display: flex; align-items: center; gap: 12px; padding: 10px 16px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 16px; transition: all 0.3s ease; height: 52px; }
    .file-upload-wrapper:hover .file-upload-box { border-color: #E8A33D; background: #fff; }
    .file-icon { width: 34px; height: 34px; border-radius: 10px; background: #123457; color: #E8A33D; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .file-text { font-size: 11px; font-weight: 700; color: #94a3b8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .file-input-real { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10; }
</style>

<div class="max-w-4xl mx-auto pb-12">
    <div class="command-card shadow-sm">
        <div class="command-header">
            <div class="flex items-center justify-between">
                <div>
                    <h2>Registrasi Formulir Mandiri</h2>
                    <p>Inisialisasi alur pengajuan data warga baru untuk portal Sukabumi One Access.</p>
                </div>
                <div class="hidden md:flex w-12 h-12 rounded-xl bg-white/10 items-center justify-center text-accent border border-white/10">
                    <i class="bi bi-file-earmark-text text-xl"></i>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.layanan.form.store', $portalCode) }}" method="POST" id="formulirForm" enctype="multipart/form-data">
            @csrf
            {{-- Hidden fields untuk logika sistem --}}
            <input type="hidden" name="type" value="form">
            <input type="hidden" name="instansi_slug" value="{{ $instansi_slug }}">

            {{-- ========== SECTION: INFORMASI UTAMA ========== --}}
            <div class="form-section">
                <div class="section-heading">
                    <span class="section-number">1</span>
                    <h3>Identitas Formulir</h3>
                </div>

                <div class="space-y-6">
                    {{-- Judul Layanan --}}
                    <div class="input-field-wrapper">
                        <label class="label-command">Judul Formulir / Layanan</label>
                        <div class="input-group-custom">
                            <input type="text" name="nama" value="{{ old('nama') }}" class="input-control-command" placeholder="Contoh: Pengajuan Akta Kelahiran" required autofocus>
                            <i class="bi bi-tag input-icon-left"></i>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="input-field-wrapper">
                        <label class="label-command">Deskripsi Singkat (Card Caption)</label>
                        <div class="relative">
                            <textarea name="deskripsi" rows="4" class="input-control-command !pl-5" style="padding-top: 14px; height: auto;" placeholder="Tuliskan keterangan singkat mengenai kegunaan formulir ini..." required>{{ old('deskripsi') }}</textarea>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-2 ml-1">
                            <i class="bi bi-info-circle mr-1"></i> Deskripsi ini akan tampil pada kartu layanan di halaman utama warga.
                        </p>
                    </div>

                    {{-- Upload Image --}}
                    <div class="input-field-wrapper">
                        <label class="label-command">Foto Sampul (Thumbnail Card)</label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="cover" id="in_cover" class="file-input-real" accept="image/*" required>
                            <div class="file-upload-box">
                                <div class="file-icon">
                                    <i class="bi bi-image"></i>
                                </div>
                                <span class="file-text" id="file_name_display">
                                    Klik untuk upload foto sampul...
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-2 ml-1">
                                <i class="bi bi-info-circle mr-1"></i> Format: JPG, PNG, WEBP. Maks: 2MB.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="form-footer">
                <a href="{{ route('admin.layanan.form.manage', [$portalCode, $instansi_slug]) }}" class="btn-command-cancel">
                    Batalkan
                </a>
                <button type="submit" class="btn-command-save" id="btnSubmit">
                    Simpan & Lanjutkan
                </button>
            </div>
        </form>
    </div>
</div>

<x-admin.modal-form-errors />

@push('scripts')
<script>
    // Preview Image Logic
    document.getElementById('in_cover').onchange = function(evt) {
        const [file] = this.files;
        if (file) {
            // Tampilkan Nama File
            document.getElementById('file_name_display').innerText = file.name;
            document.getElementById('file_name_display').style.color = "#123457";
            
        }
    };

    document.getElementById('formulirForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses...';
        btn.style.opacity = '0.8';
        btn.style.pointerEvents = 'none';
    });
</script>
@endpush

@endsection