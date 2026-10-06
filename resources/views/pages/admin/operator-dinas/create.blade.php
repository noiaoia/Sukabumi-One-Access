@extends('layouts.admin')

@section('title', 'Tambah Staf Pelaksana')
@section('page-title', 'Registrasi Staf Operator')
@section('page-icon', 'bi-person-plus-fill')

@section('admin-content')

@php
    $portalCode = 'portal-' . Auth::user()->username;
    $myAgency = Auth::user()->instansi;
@endphp

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

<style>
    /* 1. Card & Layout Base */
    .command-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        overflow: hidden;
        max-width: 900px;
        margin: 0 auto;
    }

    .command-header {
        background: #0A1E33;
        padding: 32px 40px;
        border-bottom: 4px solid #E8A33D;
    }

    .command-header h2 {
        color: #ffffff;
        font-size: 20px;
        font-weight: 800;
        margin: 0;
    }

    .command-header p {
        color: rgba(255,255,255,0.6);
        font-size: 12px;
        margin-top: 5px;
    }

    /* 2. Form Styling (identical to Admin Dinas) */
    .input-field-wrapper { margin-bottom: 20px; }

    .label-command {
        display: block;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
        margin-bottom: 8px;
        margin-left: 4px;
    }

    .input-group-custom { position: relative; display: flex; align-items: center; width: 100%; }

    .input-control-command {
        width: 100%;
        padding: 14px 20px 14px 50px;
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 700;
        color: #123457;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .phone-field-style { padding-left: 95px !important; }

    .input-control-command:focus {
        outline: none;
        border-color: #123457;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(18, 52, 87, 0.05);
    }

    .input-control-command:disabled,
    .input-control-command[readonly] {
        background: #F1F5F9;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .input-icon-left {
        position: absolute;
        left: 18px;
        color: #94a3b8;
        font-size: 1.1rem;
        z-index: 5;
        pointer-events: none;
        transition: color 0.3s ease;
    }

    /* 3. Intl-Tel-Input fixes */
    .iti { width: 100% !important; font-family: inherit; }
    .iti__selected-dial-code { font-weight: 700 !important; font-size: 14px !important; color: #123457 !important; margin-left: 4px !important; }
    .iti__country-list { z-index: 100; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); font-family: inherit; }

    /* 4. Footer / Action buttons */
    .form-footer {
        padding: 25px 40px;
        background: #F8FAFC;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-command-cancel {
        padding: 12px 24px; border-radius: 12px; font-size: 12px; font-weight: 700;
        color: #475569; background: #e2e8f0; border: 1px solid #cbd5e1;
        transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center;
    }
    .btn-command-cancel:hover { background: #d1d5db; color: #1e293b; transform: translateY(-1px); }

    .btn-command-save {
        padding: 12px 32px; border-radius: 12px; font-size: 12px; font-weight: 800;
        color: #ffffff; background: #123457; border: none;
        box-shadow: 0 10px 15px -3px rgba(18, 52, 87, 0.2);
        transition: all 0.3s; text-transform: uppercase; letter-spacing: 1px;
    }
    .btn-command-save:hover:not(:disabled) { background: #E8A33D; color: #0A1E33; transform: translateY(-2px); }
    .btn-command-save:disabled { background: #e2e8f0; color: #94a3b8; cursor: not-allowed; box-shadow: none; transform: none; }

    /* 5. INLINE EMAIL VERIFY BUTTON */
    #operator_email { padding-right: 132px; }

    .btn-verify-inline {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        background: #123457;
        color: #fff;
        border: none;
        padding: 9px 16px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 10;
        white-space: nowrap;
    }
    .btn-verify-inline:hover:not(:disabled) { background: #E8A33D; color: #0A1E33; }
    .btn-verify-inline:disabled {
        background: #ECFDF5; color: #059669; cursor: default;
    }
    .btn-verify-inline.is-sent { background: #F1F5F9; color: #64748b; }

    .verified-text {
        font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
        color: #059669; margin-top: 8px; margin-left: 4px; display: flex; align-items: center; gap: 6px;
        animation: fadeInBox 0.35s ease;
    }

    /* 6. INLINE OTP BOX */
    .otp-inline-box {
        margin-top: 10px;
        background: #F0F6FF;
        border: 1px solid #DCE8FB;
        border-radius: 18px;
        padding: 18px 20px;
        animation: fadeInBox 0.4s ease;
    }
    @keyframes fadeInBox { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

    /* 7. LOCK MECHANISM */
    .locked-section { position: relative; }

    .form-locked {
        opacity: 0.35;
        pointer-events: none;
        filter: grayscale(0.6);
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .form-unlocked { opacity: 1; pointer-events: auto; filter: grayscale(0); }

    .lock-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-align: center;
        z-index: 20;
        transition: opacity 0.4s ease;
    }
    .lock-overlay i { font-size: 1.9rem; color: #94a3b8; }
    .lock-overlay span { font-size: 10px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; }
    .lock-overlay.hidden { display: none; }

    .lock-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: #F1F5F9; color: #94a3b8;
        font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
        padding: 5px 10px; border-radius: 999px;
        transition: all 0.4s ease;
    }
    .lock-badge.unlocked { background: #ECFDF5; color: #059669; }

    .field-hint {
        font-size: 9px; font-weight: 700; color: #94a3b8; margin-top: 6px; margin-left: 4px;
        letter-spacing: 0.3px;
    }
</style>

<div class="command-card">
    {{-- HEADER CARD --}}
    <div class="command-header">
        <div class="flex items-center justify-between">
            <div>
                <h2>Registrasi Staf Baru</h2>
                <p>Otoritas operasional untuk membantu pengelolaan layanan pada <strong>{{ $myAgency }}</strong>.</p>
            </div>
            <div class="hidden md:flex w-12 h-12 rounded-xl bg-white/10 items-center justify-center text-white border border-white/10">
                <i class="bi bi-person-gear text-xl text-accent"></i>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.operator-dinas.store', $portalCode) }}" method="POST" id="operatorForm">
        @csrf

        <div class="p-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">

                {{-- Nama --}}
                <div class="input-field-wrapper md:col-span-2">
                    <label class="label-command">Nama Lengkap Staf Pelaksana</label>
                    <div class="input-group-custom">
                        <input type="text" name="name" value="{{ old('name') }}" class="input-control-command @error('name') border-red-500 @enderror" placeholder="Masukkan nama lengkap..." required>
                        <i class="bi bi-person-vcard-fill input-icon-left"></i>
                    </div>
                    @error('name') <span class="text-red-500 text-[10px] font-bold ml-1">{{ $message }}</span> @enderror
                </div>

                {{-- Username --}}
                <div class="input-field-wrapper md:col-span-2">
                    <label class="label-command">Username Akses</label>
                    <div class="input-group-custom">
                        <input type="text" name="username" value="{{ old('username') }}" class="input-control-command @error('username') border-red-500 @enderror" placeholder="staf_admin" required>
                        <i class="bi bi-at input-icon-left"></i>
                    </div>
                    @error('username') <span class="text-red-500 text-[10px] font-bold ml-1">{{ $message }}</span> @enderror
                </div>

                {{-- Email — wajib verifikasi, tombol menempel di dalam kolom --}}
                <div class="input-field-wrapper md:col-span-2">
                    <label class="label-command">Email Staf (Wajib Verifikasi)</label>
                    <div class="input-group-custom">
                        <input type="email" name="email" id="operator_email" value="{{ old('email') }}" class="input-control-command @error('email') border-red-500 @enderror" placeholder="staf@sukabumikota.go.id" required>
                        <i class="bi bi-envelope-paper-fill input-icon-left" id="emailIcon"></i>
                        <button type="button" id="btnSendOtp" onclick="sendOtp()" class="btn-verify-inline">
                            <i class="bi bi-shield-check"></i> Verifikasi
                        </button>
                    </div>
                    @error('email') <span class="text-red-500 text-[10px] font-bold ml-1">{{ $message }}</span> @enderror

                    {{-- OTP box (muncul setelah klik Verifikasi) --}}
                    <div id="otp-section" class="otp-inline-box hidden">
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <div class="flex-1 w-full">
                                <label class="label-command !text-[#123457] mb-2">Masukkan 6 Digit Kode OTP</label>
                                <input type="text" id="otp_input" maxlength="6" placeholder="000000" class="input-control-command text-center tracking-[0.8em] font-black text-lg !pl-5">
                            </div>
                            <button type="button" id="btnConfirmOtp" onclick="verifyOtp()" class="btn-command-save !py-3.5 !px-8 whitespace-nowrap self-end">
                                Konfirmasi
                            </button>
                        </div>
                    </div>

                    {{-- Teks sukses — realtime, muncul begitu server mengonfirmasi OTP benar --}}
                    <p id="verified-text" class="verified-text" style="display:none;">
                        <i class="bi bi-patch-check-fill"></i>
                        Email berhasil diverifikasi
                    </p>
                </div>
            </div>

            {{-- SEKSI TERKUNCI: Phone, Instansi, Password --}}
            <div class="locked-section border-t border-slate-100 pt-8 mt-4">
                <div class="flex items-center gap-2 mb-5">
                    <span class="label-command !mb-0">Data Keanggotaan</span>
                    <span id="lock-badge" class="lock-badge">
                        <i class="bi bi-lock-fill" id="lockBadgeIcon"></i>
                        <span id="lockBadgeText">Terkunci</span>
                    </span>
                </div>

                <div id="locked-wrapper" class="form-locked grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">

                    {{-- WhatsApp --}}
                    <div class="input-field-wrapper">
                        <label class="label-command">Nomor WhatsApp Aktif</label>
                        <div class="input-group-custom">
                            <input type="tel" id="phone_display" class="input-control-command phone-field-style" required>
                            <input type="hidden" name="phone" id="phone_hidden">
                        </div>
                        <p class="field-hint">Maksimal 13 digit angka.</p>
                        @error('phone') <span class="text-red-500 text-[10px] font-bold ml-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Instansi (ReadOnly — otomatis mengikuti instansi admin) --}}
                    <div class="input-field-wrapper">
                        <label class="label-command">Instansi Pelaksana</label>
                        <div class="input-group-custom">
                            <input type="text" name="instansi" class="input-control-command bg-slate-100" value="{{ $myAgency }}" readonly>
                            <i class="bi bi-building-fill input-icon-left"></i>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="input-field-wrapper">
                        <label class="label-command">Kata Sandi</label>
                        <div class="input-group-custom">
                            <input type="password" name="password" id="passInput" class="input-control-command pr-12 @error('password') border-red-500 @enderror" placeholder="••••••••" required>
                            <i class="bi bi-lock-fill input-icon-left"></i>
                            <button type="button" onclick="togglePass('passInput', 'eyeIcon')" class="absolute right-4 text-slate-400">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password') <span class="text-red-500 text-[10px] font-bold ml-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="input-field-wrapper">
                        <label class="label-command">Konfirmasi Sandi</label>
                        <div class="input-group-custom">
                            <input type="password" name="password_confirmation" id="confirmPassInput" class="input-control-command pr-12" placeholder="••••••••" required>
                            <i class="bi bi-shield-lock-fill input-icon-left"></i>
                            <button type="button" onclick="togglePass('confirmPassInput', 'eyeIconConfirm')" class="absolute right-4 text-slate-400">
                                <i class="bi bi-eye" id="eyeIconConfirm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="lock-overlay" class="lock-overlay">
                    <i class="bi bi-lock-fill"></i>
                    <span>Verifikasi email terlebih dahulu untuk membuka kolom ini</span>
                </div>
            </div>
        </div>

        {{-- FOOTER BUTTONS --}}
        <div class="form-footer">
            <a href="{{ route('admin.operator-dinas.index', $portalCode) }}" class="btn-command-cancel">
                Batal
            </a>
            <button type="submit" class="btn-command-save" id="btnSubmit" disabled>
                Simpan Operator
            </button>
        </div>
    </form>
</div>

<x-admin.modal-form-errors />

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/intlTelInput.min.js"></script>

<script>
    let isEmailVerified = false;
    const PHONE_MAX_DIGITS = 13;

    // 1. Password Toggle Logic
    window.togglePass = function(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = "password";
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    };

    // 2. Kirim OTP ke email
    async function sendOtp() {
        const email = document.getElementById('operator_email').value;
        const btn = document.getElementById('btnSendOtp');
        const icon = document.getElementById('emailIcon');

        if (!email || !email.includes('@')) return alert('Masukkan email valid.');

        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Mengirim';

        try {
            const response = await fetch("{{ route('admin.operator-dinas.send-verification', $portalCode) }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email: email })
            });

            if (response.ok) {
                if (typeof openGlobalAlert === 'function') {
                    openGlobalAlert({ title: 'OTP Terkirim', message: 'Cek email ' + email, instruction: 'Masukkan 6 digit kode pada kolom yang muncul.', confirmText: 'OKE', confirmUrl: 'javascript:closeGlobalAlert()', icon: 'bi-send-check-fill' });
                }
                document.getElementById('otp-section').classList.remove('hidden');
                document.getElementById('operator_email').readOnly = true;
                icon.classList.replace('bi-envelope-paper-fill', 'bi-envelope-check-fill');
                btn.classList.add('is-sent');
                btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menunggu';
            } else {
                const data = await response.json();
                throw new Error(data.message || 'Gagal mengirim OTP. Email mungkin sudah terdaftar.');
            }
        } catch (error) {
            alert(error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-check"></i> Verifikasi';
        }
    }

    // 3. Verifikasi OTP & buka kolom terkunci — update UI realtime, tanpa delay buatan
    async function verifyOtp() {
        const email = document.getElementById('operator_email').value;
        const otp = document.getElementById('otp_input').value;
        const btnConfirm = document.getElementById('btnConfirmOtp');
        const otpSection = document.getElementById('otp-section');
        const verifiedText = document.getElementById('verified-text');

        if (otp.length < 6) {
            alert('Masukkan kode 6 digit lengkap.');
            return;
        }

        btnConfirm.disabled = true;
        btnConfirm.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i>';

        try {
            const response = await fetch("{{ route('admin.operator-dinas.verify-otp', $portalCode) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: email, otp: otp })
            });

            if (response.ok) {
                isEmailVerified = true;

                // Update UI seketika begitu server mengonfirmasi — tidak ada delay buatan
                otpSection.classList.add('hidden');
                verifiedText.style.display = 'flex';

                const btnSend = document.getElementById('btnSendOtp');
                btnSend.disabled = true;
                btnSend.classList.remove('is-sent');
                btnSend.innerHTML = '<i class="bi bi-check-circle-fill"></i> Terverifikasi';

                document.getElementById('emailIcon').classList.replace('bi-envelope-check-fill', 'bi-patch-check-fill');

                const lockedWrapper = document.getElementById('locked-wrapper');
                lockedWrapper.classList.remove('form-locked');
                lockedWrapper.classList.add('form-unlocked');
                document.getElementById('lock-overlay').classList.add('hidden');

                const lockBadge = document.getElementById('lock-badge');
                lockBadge.classList.add('unlocked');
                document.getElementById('lockBadgeIcon').classList.replace('bi-lock-fill', 'bi-unlock-fill');
                document.getElementById('lockBadgeText').innerText = 'Terbuka';

                document.getElementById('btnSubmit').disabled = false;
            } else {
                throw new Error('Kode salah');
            }
        } catch (error) {
            alert('Kode OTP yang Anda masukkan salah atau kadaluarsa.');
            btnConfirm.disabled = false;
            btnConfirm.innerHTML = 'Konfirmasi';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const phoneDisplay = document.querySelector("#phone_display");
        const phoneHidden = document.querySelector("#phone_hidden");

        if (!phoneDisplay) return;

        if (typeof window.intlTelInput !== 'function') {
            console.error('Intl Tel Input gagal dimuat.');
            return;
        }

        const iti = window.intlTelInput(phoneDisplay, {
            initialCountry: "id",
            separateDialCode: true,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/utils.js",
        });

        // Batasi nomor WhatsApp maksimal 13 digit
        phoneDisplay.addEventListener('input', function() {
            let rawValue = this.value.replace(/[^0-9]/g, '');
            if (rawValue.startsWith('0')) rawValue = rawValue.substring(1);
            if (rawValue.length > PHONE_MAX_DIGITS) rawValue = rawValue.substring(0, PHONE_MAX_DIGITS);
            phoneHidden.value = rawValue;
            this.value = rawValue.match(/.{1,4}/g)?.join('-') || rawValue;
        });

        // Form submit loading state
        document.getElementById('operatorForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('btnSubmit');
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses...';
            btn.classList.add('opacity-70', 'pointer-events-none');
        });
    });
</script>
@endpush
@endsection