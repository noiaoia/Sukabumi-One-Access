@extends('layouts.app')

@section('title', ($category->form_title ?? 'Formulir Pengajuan') . ' - Sukabumi One Access')

@section('content')

@php
    /**
     * =====================================================================
     * DECODE STRUCTURE
     * =====================================================================
     * $category->structure adalah hasil JSON dari Form Builder admin.
     * Bisa berupa string JSON (raw) atau array (jika sudah di-cast di model).
     */
    $structure = $category->structure ?? [];
    if (is_string($structure)) {
        $structure = json_decode($structure, true) ?: [];
    }

    $formTitle = $category->form_title ?? 'Formulir Pengajuan';
    $formDesc  = $category->form_description ?? '';

    /**
     * =====================================================================
     * MAPPING PARAMETER -> KONFIGURASI INPUT
     * =====================================================================
     * Admin hanya memilih "parameter" di Builder (bukan mengetik label).
     * Di sini parameter key dipetakan ke label, tipe input, dan atribut
     * HTML yang sesuai. Jika ada parameter baru di masa depan, cukup
     * tambahkan entry di sini — tidak perlu mengubah Builder.
     */
    $parameterMap = [
        'nama_lengkap'  => ['label' => 'Nama Lengkap',            'input' => 'text',  'span' => 2, 'attrs' => ''],
        'nik'           => ['label' => 'NIK',                     'input' => 'text',  'span' => 1, 'attrs' => 'maxlength="16" inputmode="numeric" data-numeric16="1"', 'placeholder' => '3277xxxxxxxx'],
        'no_kk'         => ['label' => 'Nomor Kartu Keluarga',    'input' => 'text',  'span' => 1, 'attrs' => 'maxlength="16" inputmode="numeric" data-numeric16="1"', 'placeholder' => '3277xxxxxxxx'],
        'tempat_lahir'  => ['label' => 'Tempat Lahir',            'input' => 'text',  'span' => 1, 'attrs' => ''],
        'tanggal_lahir' => ['label' => 'Tanggal Lahir',           'input' => 'date',  'span' => 1, 'attrs' => ''],
        'jenis_kelamin' => ['label' => 'Jenis Kelamin',           'input' => 'select','span' => 1, 'attrs' => '', 'options' => ['Laki-laki', 'Perempuan']],
        'no_hp'         => ['label' => 'Nomor Handphone (WhatsApp)', 'input' => 'phone', 'span' => 1, 'attrs' => ''],
        'email'         => ['label' => 'Email',                   'input' => 'email', 'span' => 1, 'attrs' => '', 'placeholder' => 'contoh@mail.com'],
        'keterangan'    => ['label' => 'Keterangan Tambahan',     'input' => 'textarea', 'span' => 2, 'attrs' => ''],
    ];

    $addressFieldsMap = [
        'provinsi'        => 'Provinsi',
        'kota'            => 'Kabupaten / Kota',
        'kecamatan'       => 'Kecamatan',
        'kelurahan'       => 'Kelurahan / Desa',
        'alamat_lengkap'  => 'Alamat Lengkap',
        'rt'              => 'RT',
        'rw'              => 'RW',
    ];

    $formatAcceptMap = [
        'image' => 'image/*',
        'pdf'   => 'application/pdf',
        'both'  => 'image/*,application/pdf',
    ];
    $formatLabelMap = [
        'image' => 'Gambar (JPG/PNG/WEBP)',
        'pdf'   => 'PDF',
        'both'  => 'Gambar atau PDF',
    ];

    // Flag untuk tahu script apa saja yang perlu di-load
    $hasPhoneField    = false;
    $hasAddressBundle = false;
    $hasDocumentBundle = false;
    $hasNumericField  = false;

    foreach ($structure as $card) {
        $type = $card['type'] ?? 'normal';
        if ($type === 'bundle_address') $hasAddressBundle = true;
        if ($type === 'bundle_documents') $hasDocumentBundle = true;
        if ($type === 'normal') {
            foreach (($card['fields'] ?? []) as $f) {
                $pKey = $f['parameter'] ?? null;
                if ($pKey === 'no_hp') $hasPhoneField = true;
                if (in_array($pKey, ['nik', 'no_kk'])) $hasNumericField = true;
            }
        }
    }
@endphp

@if($hasPhoneField)
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/intlTelInput.min.js"></script>
@endif

<style>
    .form-header {
        background-color: #0A1E33;
        padding: 3rem 1.5rem 6rem;
        position: relative;
        overflow: hidden;
        border-bottom: 4px solid #E8A33D;
    }

    .bg-dot-matrix {
        position: absolute;
        inset: 0;
        opacity: 0.1;
        background-image: radial-gradient(#E8A33D 1px, transparent 0);
        background-size: 30px 30px;
    }

    .form-container {
        margin-top: -4rem;
        position: relative;
        z-index: 20;
    }

    .input-group-custom {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 1rem;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .input-group-custom:focus-within {
        border-color: #E8A33D;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(232, 163, 61, 0.05);
    }

    .input-group-custom label {
        display: block;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        margin-bottom: 0.25rem;
    }

    .input-group-custom input,
    .input-group-custom select,
    .input-group-custom textarea {
        width: 100%;
        background: transparent;
        border: none;
        outline: none;
        font-size: 0.9rem;
        font-weight: 700;
        color: #123457;
    }

    /* Upload Area Styling */
    .upload-box {
        border: 2px dashed #e2e8f0;
        border-radius: 1.5rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        background: #f8fafc;
    }

    .upload-box:hover {
        border-color: #E8A33D;
        background: #fffef9;
    }

    .preview-wrap {
        position: relative;
        display: none;
    }

    .preview-img {
        width: 100%;
        max-height: 200px;
        object-fit: contain;
        border-radius: 1rem;
        display: block;
    }

    .preview-file-chip {
        display: none;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.6rem 0.9rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        color: #123457;
    }

    .remove-preview-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #ef4444;
        color: #ffffff;
        border: 3px solid #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        cursor: pointer;
        box-shadow: 0 6px 14px rgba(0,0,0,0.18);
        transition: background 0.2s ease, transform 0.2s ease;
        z-index: 5;
    }

    .remove-preview-btn:hover {
        background: #dc2626;
        transform: scale(1.08);
    }

    .doc-hint {
        font-size: 9px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 6px;
    }

    .iti { width: 100% !important; }

    .iti__tel-input,
    .input-group-custom .iti input {
        width: 100% !important;
        height: 28px;
        padding-left: 80px !important;
        font-size: 0.9rem;
        font-weight: 700;
        color: #123457;
        background: transparent;
    }

    .iti__flag-container {
        left: 0;
        top: 50%;
        transform: translateY(-50%);
    }

    .iti__selected-country {
        height: 100%;
        display: flex;
        align-items: center;
    }

    .iti__selected-dial-code {
        margin-left: 6px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #123457;
    }
</style>

{{-- 1. HEADER --}}
<section class="form-header">
    <div class="bg-dot-matrix"></div>
    <div class="max-w-4xl mx-auto relative z-10 text-center text-white">
        <div class="reveal">
            <nav class="flex justify-center items-center gap-2 text-accent text-[10px] font-bold uppercase tracking-[0.2em] mb-4">
                <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                <i class="bi bi-chevron-right text-[8px]"></i>
                @if($layanan->instansi)
                    <a href="{{ route('instansi.show', $layanan->instansi->slug) }}" class="hover:text-white transition">{{ $layanan->instansi->nama }}</a>
                    <i class="bi bi-chevron-right text-[8px]"></i>
                @endif
                <a href="{{ route('layanan.form.show', $layanan->slug) }}" class="hover:text-white transition">{{ $layanan->nama }}</a>
                <i class="bi bi-chevron-right text-[8px]"></i>
                <span>{{ $category->nama }}</span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tighter">{{ $formTitle }}</h1>
            @if($formDesc)
                <p class="text-slate-400 mt-2 text-sm">{{ $formDesc }}</p>
            @endif
        </div>
    </div>
</section>

{{-- 2. FORM SECTION --}}
<section class="pb-24 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 form-container">

        <form action="{{ route('layanan.form.submit', ['slug' => $layanan->slug, 'categoryId' => $category->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="dynamicUserForm">
            @csrf
            <input type="hidden" name="category_id" value="{{ $category->id ?? '' }}">

            @foreach($structure as $index => $card)
                @php
                    $cardType = $card['type'] ?? 'normal';
                    $iconBoxClass = $index % 2 === 0 ? 'bg-primary text-accent' : 'bg-accent text-primary';
                @endphp

                {{-- ============================================================= --}}
                {{-- KELOMPOK NORMAL (Parameter Data) --}}
                {{-- ============================================================= --}}
                @if($cardType === 'normal')
                    <div class="bg-white rounded-[2.5rem] p-6 sm:p-10 shadow-xl shadow-slate-200/50 reveal">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-10 h-10 rounded-xl {{ $iconBoxClass }} flex items-center justify-center shadow-lg">
                                <i class="bi {{ $card['icon'] ?? 'bi-collection-fill' }}"></i>
                            </div>
                            <h3 class="text-lg font-bold text-primary">{{ $card['title'] ?? 'Kelompok Data' }}</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $cardFields = $card['fields'] ?? [];
                            $fieldCount = count($cardFields);
                            $rowUsedColumns = 0;
                        @endphp

                        @foreach($cardFields as $fieldIndex => $field)
                            @php
                                $pKey = $field['parameter'] ?? null;
                                $def  = $parameterMap[$pKey] ?? null;
                                $isRequired = !empty($field['required']);
                                $span = (int) ($def['span'] ?? 1);

                                // Jika field terakhir memulai baris baru dan hanya menggunakan 1 kolom,
                                // otomatis dibuat full width agar tidak menyisakan ruang kosong.
                                $isLastField = $fieldIndex === $fieldCount - 1;
                                $isFullWidth = $span === 2 || ($isLastField && $rowUsedColumns === 0);

                                // Hitung posisi kolom pada baris berikutnya.
                                if ($isFullWidth || $span === 2) {
                                    $rowUsedColumns = 0;
                                } else {
                                    $rowUsedColumns += $span;

                                    if ($rowUsedColumns >= 2) {
                                        $rowUsedColumns = 0;
                                    }
                                }
                            @endphp
                                <div class="input-group-custom {{ $isFullWidth ? 'md:col-span-2' : '' }}">
                                    <label>{{ $def['label'] }}{{ $isRequired ? ' *' : '' }}</label>

                                    @switch($def['input'])
                                        @case('textarea')
                                            <textarea name="{{ $pKey }}" rows="3" {{ $isRequired ? 'required' : '' }}></textarea>
                                            @break

                                        @case('select')
                                            <select name="{{ $pKey }}" {{ $isRequired ? 'required' : '' }}>
                                                <option value="">Pilih {{ $def['label'] }}</option>
                                                @foreach(($def['options'] ?? []) as $opt)
                                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                            @break

                                        @case('phone')
                                            <input type="tel" id="phone_display" placeholder="812-3456-7890" {{ $isRequired ? 'required' : '' }}>
                                            <input type="hidden" name="{{ $pKey }}" id="phone">
                                            @break

                                        @case('date')
                                            <input type="date" name="{{ $pKey }}" {{ $isRequired ? 'required' : '' }}>
                                            @break

                                        @case('email')
                                            <input type="email" name="{{ $pKey }}" placeholder="{{ $def['placeholder'] ?? '' }}" {{ $isRequired ? 'required' : '' }}>
                                            @break

                                        @default
                                            <input type="text" name="{{ $pKey }}" placeholder="{{ $def['placeholder'] ?? '' }}" {!! $def['attrs'] ?? '' !!} {{ $isRequired ? 'required' : '' }}>
                                    @endswitch
                                </div>
                            @endforeach
                        </div>
                    </div>

                {{-- ============================================================= --}}
                {{-- BUNDLE ALAMAT (fixed field, tidak individual di Builder) --}}
                {{-- ============================================================= --}}
                @elseif($cardType === 'bundle_address')
                    <div class="bg-white rounded-[2.5rem] p-6 sm:p-10 shadow-xl shadow-slate-200/50 reveal">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-10 h-10 rounded-xl {{ $iconBoxClass }} flex items-center justify-center shadow-lg">
                                <i class="bi {{ $card['icon'] ?? 'bi-map-fill' }}"></i>
                            </div>
                            <h3 class="text-lg font-bold text-primary">{{ $card['title'] ?? 'Data Alamat Lengkap' }}</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" data-address-bundle="1">
                            <div class="input-group-custom">
                                <label>Provinsi</label>
                                <select name="provinsi" id="provinsi" required>
                                    <option value="">Pilih Provinsi</option>
                                </select>
                                <input type="hidden" name="provinsi_name" id="provinsi_name">
                            </div>
                            <div class="input-group-custom">
                                <label>Kabupaten / Kota</label>
                                <select name="kota" id="kota" required disabled>
                                    <option value="">Pilih Kota</option>
                                </select>
                                <input type="hidden" name="kota_name" id="kota_name">
                            </div>
                            <div class="input-group-custom">
                                <label>Kecamatan</label>
                                <select name="kecamatan" id="kecamatan" required disabled>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                                <input type="hidden" name="kecamatan_name" id="kecamatan_name">
                            </div>
                            <div class="input-group-custom">
                                <label>Kelurahan / Desa</label>
                                <select name="kelurahan" id="kelurahan" required disabled>
                                    <option value="">Pilih Kelurahan</option>
                                </select>
                                <input type="hidden" name="kelurahan_name" id="kelurahan_name">
                            </div>
                            <div class="input-group-custom md:col-span-2">
                                <label>Alamat Lengkap</label>
                                <input type="text" name="alamat_lengkap" placeholder="Nama jalan, nomor rumah" required>
                            </div>
                            <div class="input-group-custom">
                                <label>RT</label>
                                <input type="text" name="rt" id="rt" maxlength="3" inputmode="numeric" placeholder="001" required>
                            </div>
                            <div class="input-group-custom">
                                <label>RW</label>
                                <input type="text" name="rw" id="rw" maxlength="3" inputmode="numeric" placeholder="001" required>
                            </div>
                        </div>
                    </div>

                {{-- ============================================================= --}}
                {{-- BUNDLE DOKUMEN (mengikuti konfigurasi requirement dari Builder) --}}
                {{-- ============================================================= --}}
                @elseif($cardType === 'bundle_documents')
                    <div class="bg-white rounded-[2.5rem] p-6 sm:p-10 shadow-xl shadow-slate-200/50 reveal">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-10 h-10 rounded-xl {{ $iconBoxClass }} flex items-center justify-center shadow-lg">
                                <i class="bi {{ $card['icon'] ?? 'bi-file-earmark-text-fill' }}"></i>
                            </div>
                            <h3 class="text-lg font-bold text-primary">{{ $card['title'] ?? 'Dokumen Persyaratan' }}</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach(($card['requirements'] ?? []) as $req)
                                @php
                                    $reqId    = $req['id'];
                                    $format   = $req['format'] ?? 'image';
                                    $accept   = $formatAcceptMap[$format] ?? 'image/*';
                                    $isWajib  = !empty($req['wajib']);
                                    $maxSize  = $req['max_size_mb'] ?? 2;
                                    $maxFiles = $req['max_files'] ?? 1;
                                    $inputId  = 'doc_' . $reqId;
                                @endphp
                                <div class="space-y-3">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">
                                        {{ $req['nama_dokumen'] ?: 'Dokumen' }}{{ $isWajib ? ' *' : '' }}
                                    </p>
                                    <div class="upload-box" onclick="document.getElementById('{{ $inputId }}').click()">
                                        <input type="file"
                                               id="{{ $inputId }}"
                                               name="documents[{{ $reqId }}]{{ $maxFiles > 1 ? '[]' : '' }}"
                                               accept="{{ $accept }}"
                                               class="hidden doc-input"
                                               data-max-size-mb="{{ $maxSize }}"
                                               data-max-files="{{ $maxFiles }}"
                                               data-format="{{ $format }}"
                                               {{ $maxFiles > 1 ? 'multiple' : '' }}
                                               {{ $isWajib ? 'required' : '' }}
                                               onchange="previewDocument(this, '{{ $inputId }}')">
                                        <div id="placeholder_{{ $inputId }}">
                                            <i class="bi bi-cloud-arrow-up-fill text-3xl text-slate-300 block mb-2"></i>
                                            <span class="text-[10px] font-bold text-slate-400">Pilih {{ $formatLabelMap[$format] ?? 'File' }}</span>
                                        </div>
                                        <div id="previewWrap_{{ $inputId }}" class="preview-wrap mx-auto">
                                            <img id="previewImg_{{ $inputId }}" class="preview-img" style="display:none;">
                                            <div id="previewChip_{{ $inputId }}" class="preview-file-chip">
                                                <i class="bi bi-file-earmark-fill"></i>
                                                <span id="previewChipText_{{ $inputId }}"></span>
                                            </div>
                                            <button type="button" class="remove-preview-btn"
                                                    onclick="removeDocument(event, '{{ $inputId }}')">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="doc-hint">
                                        Format: {{ $formatLabelMap[$format] ?? 'Gambar' }} &middot;
                                        Maks {{ $maxSize }}MB @if($maxFiles > 1) &middot; Maks {{ $maxFiles }} file @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                {{-- ============================================================= --}}
                {{-- TIPE KELOMPOK TIDAK DIKENALI --}}
                {{-- ============================================================= --}}
                @else
                    @php \Illuminate\Support\Facades\Log::warning('Form user: tipe kelompok tidak dikenali diabaikan', ['type' => $cardType, 'category_id' => $category->id ?? null]); @endphp
                @endif
            @endforeach

            @if(!empty($structure))
                {{-- SUBMIT --}}
                <div class="flex flex-col gap-4 reveal">
                    <button type="submit" id="submitPengajuanBtn" class="w-full py-5 bg-primary text-white rounded-2xl font-black text-sm shadow-xl shadow-primary/20 hover:bg-primary-dark transition-all uppercase tracking-[0.2em]">
                        <span class="submit-btn-text">Kirim Permohonan</span> <i class="bi bi-send-fill ml-2 submit-btn-icon"></i><i class="bi bi-arrow-repeat animate-spin ml-2 submit-btn-loader hidden"></i>
                    </button>

                    <a href="{{ url()->previous() }}" class="text-center py-4 font-bold text-xs text-slate-400 hover:text-primary transition-all">
                        Batalkan Pengajuan
                    </a>
                </div>
            @endif

        </form>
    </div>
</section>

@include('components.user.modal-pengajuan-errors')
@include('components.user.modal-pengajuan-success')

@endsection

@push('scripts')

<div
    id="formConfig"
    data-address-bundle="{{ $hasAddressBundle ? '1' : '0' }}"
    data-phone-field="{{ $hasPhoneField ? '1' : '0' }}"
    hidden
></div>

<div
    id="pengajuanErrorData"
    data-errors="{{ e(json_encode($errors->all())) }}"
    data-session-error="{{ e(json_encode(session('error'))) }}"
    hidden
></div>

<script>
    window.openPengajuanErrorModal = function (messages = []) {
        const modal = document.getElementById('pengajuanErrorModal');
        const list = document.getElementById('pe-list-content');
        const title = document.getElementById('pe-title-text');
        const desc = document.getElementById('pe-desc-text');
        const submitBtn = document.getElementById('submitPengajuanBtn');

        if (!modal) {
            console.error('Modal pengajuan error tidak ditemukan.');
            return;
        }

        if (list && messages.length > 0) {
            list.innerHTML = messages.map(message => `
                <li>
                    <i class="bi bi-exclamation-circle-fill"></i>
                    ${message}
                </li>
            `).join('');
        }

        if (title) {
            title.textContent = 'Periksa Isian';
        }

        if (desc) {
            desc.textContent = messages.length > 1
                ? `Ada ${messages.length} data yang belum sesuai. Mohon perbaiki formulir Anda.`
                : 'Mohon perbaiki kesalahan pada formulir Anda.';
        }

        // Reset tombol submit ketika terjadi validasi error
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '';
            submitBtn.style.pointerEvents = '';

            const text = submitBtn.querySelector('.submit-btn-text');
            const icon = submitBtn.querySelector('.submit-btn-icon');
            const loader = submitBtn.querySelector('.submit-btn-loader');

            if (text) {
                text.textContent = 'Kirim Permohonan';
            }

            if (icon) {
                icon.classList.remove('hidden');
            }

            if (loader) {
                loader.classList.add('hidden');
            }
        }

        modal.classList.add('active');
        document.body.classList.add('modal-open');
    };
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const errorData = document.getElementById('pengajuanErrorData');

        if (!errorData) {
            return;
        }

        const errors = JSON.parse(errorData.dataset.errors || '[]');
        const sessionError = JSON.parse(
            errorData.dataset.sessionError || 'null'
        );

        if (sessionError) {
            errors.push(sessionError);
        }

        if (
            errors.length > 0 &&
            typeof window.openPengajuanErrorModal === 'function'
        ) {
            window.openPengajuanErrorModal(errors);
        }
    });
</script>

<script>
    // =====================================================================
    // 0. FLAG DARI PHP -> JAVASCRIPT (menggantikan @@if/@@endif di dalam script)
    // =====================================================================
    const formConfig = document.getElementById('formConfig');

    const hasAddressBundle = formConfig?.dataset.addressBundle === '1';
    const hasPhoneField = formConfig?.dataset.phoneField === '1';

    // =====================================================================
    // 1. LIMIT NUMERIC-ONLY UNTUK PARAMETER NIK / NO_KK
    // =====================================================================
    document.querySelectorAll('input[data-numeric16="1"]').forEach(function (el) {
        el.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
        });
    });

    // =====================================================================
    // 2. LIMIT NUMERIC RT / RW (bundle alamat)
    // =====================================================================
    if (hasAddressBundle) {
        ['rt', 'rw'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function () {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3);
                });
            }
        });
    }

    // =====================================================================
    // 3. PREVIEW & VALIDASI DOKUMEN (bundle_documents)
    // =====================================================================
    function formatBytes(mb) {
        return mb + 'MB';
    }

    function previewDocument(input, inputId) {
        const files = Array.from(input.files || []);
        const placeholder = document.getElementById('placeholder_' + inputId);
        const previewWrap = document.getElementById('previewWrap_' + inputId);
        const previewImg = document.getElementById('previewImg_' + inputId);
        const previewChip = document.getElementById('previewChip_' + inputId);
        const previewChipText = document.getElementById('previewChipText_' + inputId);

        if (!files.length) return;

        const maxSizeMb = parseFloat(input.dataset.maxSizeMb || '2');
        const maxFiles = parseInt(input.dataset.maxFiles || '1', 10);

        // Validasi jumlah file
        if (files.length > maxFiles) {
            input.value = '';

            window.openPengajuanErrorModal([
                `Maksimal ${maxFiles} file untuk dokumen ini.`
            ]);

            return;
        }

        // Validasi ukuran file
        for (const file of files) {
            if (file.size > maxSizeMb * 1024 * 1024) {
                input.value = '';
                window.openPengajuanErrorModal([
                    `Ukuran file "${file.name}" melebihi batas ${formatBytes(maxSizeMb)}.`
                ]);
                return;
            }
        }

        const firstFile = files[0];
        placeholder.style.display = 'none';
        previewWrap.style.display = 'block';

        if (firstFile.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                previewChip.style.display = 'none';
            };
            reader.readAsDataURL(firstFile);
        } else {
            previewImg.style.display = 'none';
            previewChip.style.display = 'flex';
            previewChipText.textContent = files.length > 1
                ? `${files.length} file dipilih`
                : firstFile.name;
        }
    }

    function removeDocument(event, inputId) {
        event.stopPropagation();
        const input = document.getElementById(inputId);
        const placeholder = document.getElementById('placeholder_' + inputId);
        const previewWrap = document.getElementById('previewWrap_' + inputId);
        const previewImg = document.getElementById('previewImg_' + inputId);
        const previewChip = document.getElementById('previewChip_' + inputId);

        input.value = '';
        previewImg.src = '';
        previewImg.style.display = 'none';
        previewChip.style.display = 'none';
        previewWrap.style.display = 'none';
        placeholder.style.display = 'block';
    }

    // =====================================================================
    // 4. API WILAYAH INDONESIA (bundle_address)
    // =====================================================================
    if (hasAddressBundle) {
    (function () {
        const provinsiSelect = document.getElementById('provinsi');
        const kotaSelect = document.getElementById('kota');
        const kecamatanSelect = document.getElementById('kecamatan');
        const kelurahanSelect = document.getElementById('kelurahan');

        if (!provinsiSelect) return;

        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json`)
            .then(response => response.json())
            .then(provinces => {
                provinces.forEach(prov => {
                    let opt = document.createElement('option');
                    opt.value = prov.id;
                    opt.text = prov.name;
                    provinsiSelect.add(opt);
                });
            });

        provinsiSelect.addEventListener('change', function () {
            document.getElementById('provinsi_name').value = this.options[this.selectedIndex].text;

            kotaSelect.disabled = false;
            kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';

            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${this.value}.json`)
                .then(response => response.json())
                .then(regencies => {
                    regencies.forEach(kota => {
                        let opt = document.createElement('option');
                        opt.value = kota.id;
                        opt.text = kota.name;
                        kotaSelect.add(opt);
                    });
                });
        });

        kotaSelect.addEventListener('change', function () {
            document.getElementById('kota_name').value = this.options[this.selectedIndex].text;

            kecamatanSelect.disabled = false;
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${this.value}.json`)
                .then(response => response.json())
                .then(districts => {
                    districts.forEach(kec => {
                        let opt = document.createElement('option');
                        opt.value = kec.id;
                        opt.text = kec.name;
                        kecamatanSelect.add(opt);
                    });
                });
        });

        kecamatanSelect.addEventListener('change', function () {
            document.getElementById('kecamatan_name').value = this.options[this.selectedIndex].text;

            kelurahanSelect.disabled = false;
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${this.value}.json`)
                .then(response => response.json())
                .then(villages => {
                    villages.forEach(kel => {
                        let opt = document.createElement('option');
                        opt.value = kel.id;
                        opt.text = kel.name;
                        kelurahanSelect.add(opt);
                    });
                });
        });

        kelurahanSelect.addEventListener('change', function () {
            document.getElementById('kelurahan_name').value = this.options[this.selectedIndex].text;
        });
    })();
    }

    // =====================================================================
    // 5. NOMOR HANDPHONE (parameter no_hp)
    // =====================================================================
    if (hasPhoneField) {
    document.addEventListener('DOMContentLoaded', function () {
        const phoneDisplay = document.querySelector("#phone_display");
        const phoneHidden = document.querySelector("#phone");
        if (!phoneDisplay || !phoneHidden) return;

        const iti = window.intlTelInput(phoneDisplay, {
            initialCountry: "id",
            separateDialCode: true,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
        });

        phoneDisplay.addEventListener('input', function () {
            let rawValue = this.value.replace(/[^0-9]/g, '');
            if (rawValue.startsWith('0')) rawValue = rawValue.substring(1);
            if (rawValue.length > 13) rawValue = rawValue.slice(0, 13);

            phoneHidden.value = rawValue;

            let formattedValue = "";
            if (rawValue.length > 0) {
                formattedValue += rawValue.substring(0, 3);
                if (rawValue.length > 3) formattedValue += '-' + rawValue.substring(3, 7);
                if (rawValue.length > 7) formattedValue += '-' + rawValue.substring(7, 13);
            }
            this.value = formattedValue;
        });

        const form = document.getElementById('dynamicUserForm');
        form.addEventListener('submit', function (e) {
            if (!iti.isValidNumber()) {
                e.preventDefault();

                window.openPengajuanErrorModal([
                    'Nomor WhatsApp tidak valid. Silakan periksa kembali nomor WhatsApp Anda.'
                ]);
            }
        });
    });
    }

    // =====================================================================
    // 6. LOADER BUTTON
    // =====================================================================
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('dynamicUserForm');
        const submitBtn = document.getElementById('submitPengajuanBtn');

        if (!form || !submitBtn) return;

        form.addEventListener('submit', function (e) {

            // Jangan aktifkan loader jika validasi lain
            // sudah membatalkan submit.
            if (e.defaultPrevented) {
                return;
            }

            // Jangan aktifkan loader jika validasi HTML5 gagal.
            if (!form.checkValidity()) {
                return;
            }

            // Cegah double submit
            if (submitBtn.disabled) {
                e.preventDefault();
                return;
            }

            submitBtn.disabled = true;

            const text = submitBtn.querySelector('.submit-btn-text');
            const icon = submitBtn.querySelector('.submit-btn-icon');
            const loader = submitBtn.querySelector('.submit-btn-loader');

            if (text) {
                text.textContent = 'Memproses...';
            }

            if (icon) {
                icon.classList.add('hidden');
            }

            if (loader) {
                loader.classList.remove('hidden');
            }

            submitBtn.style.opacity = '0.8';
            submitBtn.style.pointerEvents = 'none';
        });
    });
</script>
@endpush