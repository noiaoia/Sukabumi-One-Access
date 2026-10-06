<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sukabumi One Access - Masuk & Daftar</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/intlTelInput.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        :root {
            --primary-green: #123457;
            --dark-green: #0A1E33;
            --white: #ffffff;
            --text-main: #1E293B;
            --text-sub: #64748B;
            --border: #E2E8F0;
            --transit: cubic-bezier(0.4, 0, 0.2, 1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body {
            display: flex; justify-content: center; align-items: center; min-height: 100vh;
            background: radial-gradient(circle at top left, rgba(55,179,143,0.15), transparent 40%),
                        linear-gradient(135deg, #0A1E33 0%, #123457 100%);
            padding: 20px; overflow: hidden;
            width: 100%;
            height: 100%;
            overflow: hidden;
            overscroll-behavior: none;
            position: relative;
            touch-action: manipulation;
            -webkit-overflow-scrolling: touch;
        }

        * {
            overscroll-behavior: none;
        }
        
        .container {
            position: relative; width: 780px; height: 530px;
            background: #fff; border-radius: 35px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3); overflow: hidden; z-index: 10;
        }
        .form-box {
            position: absolute; right: 0; width: 50%; height: 100%;
            background: #fff; display: flex; align-items: center;
            text-align: center; padding: 40px; z-index: 1;
            transition: all .6s var(--transit);
        }
        .container.active .form-box { right: 50%; }
        .form-box.register { opacity: 0; pointer-events: none; }
        .container.active .form-box.register { opacity: 1; pointer-events: auto; }
        .container.active .form-box.login { opacity: 0; pointer-events: none; }
        form { width: 100%; }
        .mini-title { font-size: 11px; font-weight: 700; color: var(--primary-green); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 5px; }
        .form-title { font-size: 32px; font-weight: 800; color: var(--dark-green); margin-bottom: 5px; }
        .form-subtitle { font-size: 12px; color: var(--text-sub); margin-bottom: 20px; line-height: 1.4; }
        .input-box { position: relative; margin: 10px 0; }
        .input-box input {
            width: 100%; padding: 12px 45px 12px 18px; background: #F8FAFC;
            border-radius: 14px; border: 1.5px solid var(--border); outline: none;
            font-size: 13px; font-weight: 500; transition: 0.3s;
        }

        .input-box input::placeholder {
            font-weight: 500;
        }
        .input-box input:focus { border-color: var(--primary-green); background: #fff; box-shadow: 0 0 0 4px rgba(30,125,95,0.08); }
        .input-box i { position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: var(--primary-green); opacity: 0.7; }

        .input-error {
            border-color: #ef4444 !important;
            background: #fef2f2 !important;
            padding-right: 78px !important;
        }

        .input-box i {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-green);
            opacity: 0.7;
            transition: 0.3s;
        }

        .warning-icon {
            position: absolute;
            right: 42px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .warning-icon i {
            position: static !important;
            transform: none !important;
            color: #ef4444 !important;
            opacity: 1 !important;
            font-size: 12px;
            animation: pulseWarning 1.5s infinite;
        }

        .warning-icon .tooltip-error {
            position: absolute;
            bottom: 160%;
            right: -8px;
            background: #1e293b;
            color: white;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: 0.2s ease;
            box-shadow: 0 10px 20px rgba(0,0,0,0.12);
        }

        .warning-icon:hover .tooltip-error {
            opacity: 1;
            visibility: visible;
        }

        @keyframes pulseWarning {
            0% { transform: scale(1); }
            50% { transform: scale(1.12); }
            100% { transform: scale(1); }
        }

        .btn {
            width: 100%; height: 48px; background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            border-radius: 14px; border: none; cursor: pointer; font-size: 13px;
            color: #fff; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; transition: 0.4s;
            margin-top: 5px;
        }
        .toggle-box { position: absolute; width: 100%; height: 100%; }
        .toggle-box::before {
            content: ''; position: absolute; left: -250%; width: 300%; height: 100%;
            background: linear-gradient(135deg, var(--dark-green), var(--primary-green));
            border-radius: 150px; z-index: 2; transition: 1.5s var(--transit);
        }
        .container.active .toggle-box::before { left: 50%; }
        .toggle-panel {
            position: absolute; width: 50%; height: 100%; color: #fff;
            display: flex; flex-direction: column; justify-content: center;
            align-items: center; z-index: 3; text-align: center; padding: 0 45px;
            transition: opacity .6s var(--transit);
        }
        .toggle-panel.toggle-left { left: 0; }
        .toggle-panel.toggle-right { right: 0; }
        .container.active .toggle-panel.toggle-left { opacity: 0; pointer-events: none; }
        .container:not(.active) .toggle-panel.toggle-right { opacity: 0; pointer-events: none; }
        .toggle-panel h1 { font-size: 38px; font-weight: 800; margin-bottom: 12px; }
        .toggle-panel p { font-size: 13px; margin-bottom: 25px; opacity: 0.9; line-height: 1.7; }
        .toggle-panel .btn { width: 160px; height: 44px; background: transparent; border: 2px solid rgba(255,255,255,0.8); margin-top: 0; }
        .panel-logo { width: 150px; margin-bottom: 20px;  }
        
        /* Custom SweetAlert Premium Design */
        .swal2-popup.premium-modal {
            border-radius: 28px !important;
            padding: 0 !important;
            overflow: hidden !important;
            width: 350px !important; /* Ukuran lebih compact */
            border-top: 5px solid var(--primary-green) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4) !important;
        }

        .premium-body {
            padding: 35px 25px;
            text-align: center;
        }

        .premium-icon-wrapper {
            width: 60px;
            height: 60px;
            background: rgba(30, 125, 95, 0.1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            font-size: 26px;
            border: 1px solid rgba(30, 125, 95, 0.15);
        }

        .premium-icon-wrapper.success { color: var(--primary-green); animation: pulseGreen 2s infinite; }
        .premium-icon-wrapper.error { color: #E53E3E; background: rgba(229, 62, 62, 0.1); animation: pulseRed 2s infinite; }

        .premium-title {
            margin: 0;
            font-size: 18px;
            color: #1A3A5F;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .premium-text {
            margin: 10px 0 0;
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }

        /* Menghilangkan ikon mata bawaan Microsoft Edge */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }

        /* Pastikan ikon mata bisa diklik dan berubah warna saat dihover */
        .input-box i:hover {
            color: var(--dark-green);
            opacity: 1;
        }

        /* ===============================
        INTL TEL INPUT CUSTOM
        ================================== */

        .iti{
            width:100%;
        }

        .iti input{
            width:100% !important;
            height:48px !important;
            padding-left:90px !important;
            padding-right:45px !important;
            background:#F8FAFC !important;
            border:1.5px solid var(--border) !important;
            border-radius:14px !important;
            font-size:13px !important;
            font-weight:500;
            transition:.3s;
        }

        .iti input:focus{
            border-color:var(--primary-green)!important;
            background:#fff!important;
            box-shadow:0 0 0 4px rgba(18,52,87,.08);
        }

        .iti__selected-country{
            padding-left:14px;
        }

        .iti__selected-dial-code{
            font-size:13px;
            font-weight:600;
            color:#334155;
        }

        .iti__flag{
            border-radius:3px;
        }

        /* Style untuk tombol saat sedang terkunci */
        .btn-locked {
            background: #cbd5e1 !important; /* Warna abu-abu */
            cursor: not-allowed !important;
            opacity: 0.7;
            pointer-events: none; /* Mencegah klik sama sekali */
            transform: none !important;
            box-shadow: none !important;
        }

        /* Opsional: membuat reCAPTCHA terlihat lebih rapi di tengah */
        .g-recaptcha {
            transform: scale(0.9);
            transform-origin: center;
        }

        /* Container untuk Tooltip */
        .tooltip-container {
            position: relative;
            width: 100%;
        }

        /* Kotak Tooltip */
        .tooltip-btn {
            position: absolute;
            bottom: 125%; /* Muncul di atas tombol */
            left: 50%;
            transform: translateX(-50%) translateY(10px);
            background: #1e293b; /* Warna gelap slate */
            color: #ffffff;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s var(--transit);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
            z-index: 100;
            pointer-events: none; /* Agar tooltip tidak mengganggu ketukan */
        }

        /* Panah kecil di bawah tooltip */
        .tooltip-btn::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #1e293b transparent transparent transparent;
        }

        /* Tampilkan tooltip hanya jika tombol di dalamnya memiliki class .btn-locked */
        .tooltip-container:hover .btn-locked + .tooltip-btn {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        .tooltip-btn.show-mobile {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        @keyframes pulseGreen {
            0% { box-shadow: 0 0 0 0 rgba(30, 125, 95, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(30, 125, 95, 0); }
            100% { box-shadow: 0 0 0 0 rgba(30, 125, 95, 0); }
        }

        @keyframes pulseRed {
            0% { box-shadow: 0 0 0 0 rgba(229, 62, 62, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(229, 62, 62, 0); }
            100% { box-shadow: 0 0 0 0 rgba(229, 62, 62, 0); }
        }

        @media screen and (max-width: 768px) {
            body { padding: 15px; overflow: auto; }
            .container { width: 100%; height: 680px; display: flex; flex-direction: column; border-radius: 30px; }
            .form-box { position: absolute; bottom: 0; width: 100%; padding: 25px; transform: translateY(0); }
            /* LOGIN */
            .form-box.login {
                height: 100%;
            }

            /* REGISTER */
            .form-box.register {
                height: 85%;
            }
            .container.active .form-box { right: 0; transform: translateY(-25%); }
            .toggle-box::before { left: 0; top: -280%; width: 100%; height: 300%; border-radius: 40px; }
            .container.active .toggle-box::before { left: 0; top: 80%; }
            .toggle-panel { width: 100%; height: 20%; padding: 10px; }
            .toggle-panel.toggle-left { top: 0; }
            .toggle-panel.toggle-right { bottom: 0; }
            .panel-logo, .toggle-panel p { display: none; }
            .toggle-panel h1 { font-size: 22px; margin-bottom: 8px; }
            .toggle-panel .btn { width: 120px; height: 36px; font-size: 11px; }
            .form-title { font-size: 26px; }
            .form-box.register { overflow-y: auto; display: block; padding-top: 85px; } 
            .swal2-container.swal2-bottom {
                align-items: center !important;
            }
            .swal2-popup.premium-modal {
                width: 85% !important; /* Agar tidak menyentuh tepi layar HP */
                max-width: 320px !important;
            }
        }
    </style>
</head>
<body>

    <!-- JEMBATAN DATA LARAVEL (Menghindari Error di Script) -->
    <div id="laravel-data" 
        data-has-reg-errors="{{ $errors->hasAny(['name', 'email', 'phone', 'username', 'password']) ? 'true' : 'false' }}"
        data-login-error="{{ $errors->first('login') }}"
        data-success="{{ session('success') ?: session('status') }}">
    </div>

    <div class="container" id="container">
        
        <!-- LOGIN SECTION -->
        <div class="form-box login">
            <form action="{{ url('login') }}" method="POST" id="loginForm">
                @csrf
                <h1 class="form-title">Masuk Akun</h1>
                <p class="form-subtitle">Silakan masuk untuk mengakses seluruh layanan publik Kota Sukabumi.</p>

                <!-- Input Username & Password tetap sama ... -->
                <div class="input-box">
                    <input type="text" name="login" class="@error('login') input-error @enderror" placeholder="Username" required value="{{ old('login') }}">
                    <i class="fas fa-user-circle"></i>
                </div>

                <div class="input-box">
                    <input type="password" name="password" id="loginPass" placeholder="Password" required>
                    <i class="fas fa-eye-slash" style="cursor: pointer;" onclick="togglePassword('loginPass', this)"></i>
                </div>

                <!-- RECAPTCHA dengan Callback -->
                <div class="input-box" style="display: flex; justify-content: center; margin: 15px 0;">
                    <div class="g-recaptcha" 
                        data-sitekey="6LdTyC4tAAAAAGp4u4MLJqpUFk9wTYb4jYRPIVOz" 
                        data-callback="enableLoginBtn" 
                        data-expired-callback="disableLoginBtn">
                    </div>
                </div>

                <!-- Bagian Akhir Form Login -->
                <div class="tooltip-container">
                    <button type="submit" id="loginBtn" class="btn btn-locked" disabled>
                        Masuk
                    </button>
                    <!-- Tooltip Element -->
                    <span class="tooltip-btn">
                        <i class="fas fa-shield-alt mr-1"></i> Selesaikan verifikasi reCAPTCHA
                    </span>
                </div>
            </form>
        </div>

        <!-- REGISTER SECTION -->
        <div class="form-box register">
            <form action="{{ route('register') }}" method="POST" id="formRegister">
                @csrf
                <h1 class="form-title">Daftar Akun</h1>
                <p class="form-subtitle">Buat akun Sukabumi One Access Anda untuk mulai mengajukan layanan mandiri.</p>

                <div class="input-box">
                    <input type="text" name="name"
                    class="@error('name') input-error @enderror"
                    placeholder="Nama Lengkap"
                    required value="{{ old('name') }}">

                    <i class="fas fa-user"></i>

                    @error('name')
                    <div class="warning-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div class="tooltip-error">{{ $message }}</div>
                    </div>
                    @enderror
                </div>

                <!-- Field Email -->
                <div class="input-box">
                    <input type="email" name="email" placeholder="Alamat Email Aktif" required>
                    <i class="fas fa-envelope"></i>
                </div>

                <!-- Field No Telepon -->
                <div class="input-box">
                    <input id="phone" type="tel" name="phone" placeholder="81234567890" maxlength="13" inputmode="numeric" required>
                    <i class="fas fa-phone"></i>
                </div>

                <div class="input-box">
                    <input type="text" name="username"
                    class="@error('username') input-error @enderror"
                    placeholder="Username"
                    required value="{{ old('username') }}">

                    <i class="fas fa-at"></i>

                    @error('username')
                    <div class="warning-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div class="tooltip-error">{{ $message }}</div>
                    </div>
                    @enderror
                </div>

                <div class="input-box">
                    <input type="password" name="password" id="regPass"
                    class="@error('password') input-error @enderror"
                    placeholder="Password (Min. 8 Karakter)" required>
                    <!-- Ubah ikon fa-lock menjadi fa-eye-slash dan tambahkan onclick -->
                    <i class="fas fa-eye-slash" style="cursor: pointer;" onclick="togglePassword('regPass', this)"></i>

                    @error('password')
                    <div class="warning-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div class="tooltip-error">{{ $message }}</div>
                    </div>
                    @enderror
                </div>

                <div class="input-box">
                    <!-- Tambahkan id="confirmPass" -->
                    <input type="password" name="password_confirmation" id="confirmPass" placeholder="Konfirmasi Password" required>
                    <!-- Ubah ikon fa-shield-alt menjadi fa-eye-slash dan tambahkan onclick -->
                    <i class="fas fa-eye-slash" style="cursor: pointer;" onclick="togglePassword('confirmPass', this)"></i>
                </div>

                <button type="submit" class="btn">Daftar</button>
            </form>
        </div>

        <!-- TOGGLE SLIDER -->
        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <img src="{{ asset('image/logo.webp') }}"
                class="panel-logo"
                alt="Sukabumi One Access">
                <h1>Sampurasun!</h1>
                <p>Belum memiliki akun? Mari bergabung untuk mendapatkan akses birokrasi yang lebih cepat dan transparan.</p>
                <button class="btn" id="toRegister">Daftar</button>
            </div>
            <div class="toggle-panel toggle-right">
                <img src="{{ asset('image/logo.webp') }}"
                class="panel-logo"
                alt="Sukabumi One Access">
                <h1>Wilujeng Sumping!</h1>
                <p>Sudah memiliki akun? Silakan masuk kembali untuk melanjutkan pengajuan atau memantau status berkas Anda.</p>
                <button class="btn" id="toLogin">Sign In</button>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        const toRegister = document.getElementById('toRegister');
        const toLogin = document.getElementById('toLogin');
        const laravelData = document.getElementById('laravel-data');

        // Navigasi Panel
        toRegister.addEventListener('click', () => container.classList.add('active'));
        toLogin.addEventListener('click', () => container.classList.remove('active'));

        /**
         * Alert Component - Selalu di Tengah
         */
        const toastAlert = (type, title, text) => {
            const isSuccess = type === 'success';
            const icon = isSuccess ? 'fa-check-circle' : 'fa-exclamation-triangle';
            const iconClass = isSuccess ? 'success' : 'error';

            Swal.fire({
                html: `
                    <div class="premium-body">
                        <div class="premium-icon-wrapper ${iconClass}">
                            <i class="fas ${icon}"></i>
                        </div>
                        <h3 class="premium-title">${title}</h3>
                        <p class="premium-text">${text}</p>
                    </div>
                `,
                position: 'center', // Tetap di tengah untuk semua device
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#ffffff',
                customClass: {
                    popup: 'premium-modal'
                },
                showClass: {
                    popup: 'animate__animated animate__zoomIn' // Animasi muncul dari tengah
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOut'
                }
            });
        };

        // Ambil data dari Laravel Bridge
        const hasRegErrors = laravelData.getAttribute('data-has-reg-errors') === 'true';
        const loginError = laravelData.getAttribute('data-login-error');
        const successMsg = laravelData.getAttribute('data-success');

        // 1. Jika ada error saat pendaftaran (tetap di panel register)
        if (hasRegErrors) {
            container.classList.add('active');
            toastAlert('error', 'Pendaftaran Gagal', 'Data tidak valid atau sudah terdaftar.');
        }

        // 2. Jika login gagal (termasuk peringatan belum verifikasi)
        if (loginError) {
            toastAlert('error', 'Akses Ditolak', loginError);
        }

        // 3. Jika pendaftaran berhasil (Mengingatkan Verifikasi Email)
        if (successMsg) {
            // Cek apakah pesan mengandung kata kunci "email" atau "verifikasi"
            const isVerification = successMsg.toLowerCase().includes('email') || 
                                successMsg.toLowerCase().includes('verifikasi');

            if (isVerification) {
                // Tampilkan Popup Khusus Instruksi Verifikasi
                toastAlert('success', 'Verifikasi Akun', successMsg);
            } else {
                // Tampilkan Popup Sukses Biasa
                toastAlert('success', 'Berhasil!', successMsg);
            }
        }

        function togglePassword(inputId, icon) {
            const passwordInput = document.getElementById(inputId);
            
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                // Ubah ikon jadi mata terbuka
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordInput.type = "password";
                // Ubah ikon jadi mata tertutup
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }

        const input = document.querySelector("#phone");
        window.intlTelInput(input,{
            initialCountry:"id",
            preferredCountries:["id","sg","my"],
            separateDialCode:true,
            nationalMode:true,
            strictMode:true,

        });

        // 2. Filter hanya angka & hapus angka 0 di depan secara otomatis
        input.addEventListener('input', function () {
            // Ambil hanya angka
            let value = this.value.replace(/\D/g, '');

            // Hapus angka 0 di depan
            if (value.startsWith('0')) {
                value = value.substring(1);
            }

            // Batasi maksimal 13 digit
            value = value.substring(0, 13);

            // Tambahkan tanda "-"
            if (value.length > 7) {
                value = value.replace(/^(\d{4})(\d{4})(\d+)$/, '$1-$2-$3');
            } else if (value.length > 4) {
                value = value.replace(/^(\d{4})(\d+)$/, '$1-$2');
            }

            this.value = value;
        });

        formRegister.addEventListener('submit', function(event) {

            // Hilangkan tanda "-" sebelum dikirim
            input.value = input.value.replace(/-/g, '');

            if (!iti.isValidNumber()) {
                event.preventDefault();

                toastAlert(
                    'error',
                    'Nomor Tidak Valid',
                    'Format nomor HP tidak sesuai dengan standar Indonesia.'
                );

                return false;
            }
        });

        // 3. Validasi saat Form dikirim (Submit)
        const formRegister = document.querySelector("#formRegister");
        formRegister.addEventListener('submit', function(event) {
            
            // Cek apakah nomor valid berdasarkan standar negara (panjang & prefix)
            if (!iti.isValidNumber()) {
                event.preventDefault(); // Mencegah form terkirim ke Laravel
                
                toastAlert('error', 'Nomor Tidak Valid', 'Format nomor HP tidak sesuai dengan standar Indonesia.');
                return false;
            }

            /** 
             * OPSIONAL: Kirim nomor lengkap dengan kode negara (+62812...) ke database.
             * Secara default, yang terkirim hanya angka yang diketik (812...).
             * Jika ingin otomatis digabung dengan kode negara, gunakan baris di bawah ini:
             */
            // input.value = iti.getNumber(); 
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Ambil elemen form login
            const loginForm = document.querySelector('.form-box.login form');

            loginForm.addEventListener('submit', function(e) {
                // Cek respon reCAPTCHA (grecaptcha adalah objek global dari library Google)
                const captchaResponse = grecaptcha.getResponse();

                if (captchaResponse.length === 0) {
                    // Jika respon kosong (belum dicentang)
                    e.preventDefault(); // Hentikan proses pengiriman form
                    
                    // Tampilkan pesan peringatan menggunakan fungsi toastAlert yang sudah Anda buat
                    toastAlert('error', 'Verifikasi Diperlukan', 'Harap centang kotak "I\'m not a robot" sebelum melanjutkan.');
                    
                    return false;
                }
                
                // Jika sudah dicentang, form akan berlanjut ke Controller
                return true;
            });
        });

        // 1. Fungsi untuk mengaktifkan tombol (Dipanggil saat Captcha Sukses)
        function enableLoginBtn() {
            const btn = document.getElementById('loginBtn');
            btn.disabled = false;
            btn.classList.remove('btn-locked');
        }

        // 2. Fungsi untuk mematikan tombol (Dipanggil jika Captcha Expired/Kadaluarsa)
        function disableLoginBtn() {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.classList.add('btn-locked');
        }

        // 3. Tambahan Keamanan: Cegah tombol ENTER jika belum captcha
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const response = grecaptcha.getResponse();
            if (response.length === 0) {
                e.preventDefault();
                toastAlert('error', 'Verifikasi Diperlukan', 'Harap centang reCAPTCHA terlebih dahulu.');
                return false;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const loginBtn = document.getElementById('loginBtn');
            const tooltip = document.querySelector('.tooltip-btn');
            const tooltipContainer = document.querySelector('.tooltip-container');

            // Fungsi untuk menampilkan tooltip di Mobile
            tooltipContainer.addEventListener('click', function(e) {
                // Cek apakah tombol masih dalam keadaan terkunci (btn-locked)
                if (loginBtn.classList.contains('btn-locked')) {
                    // Munculkan tooltip
                    tooltip.classList.add('show-mobile');

                    // Hilangkan otomatis setelah 2 detik
                    setTimeout(() => {
                        tooltip.classList.remove('show-mobile');
                    }, 2000);
                }
            });
        });
    </script>
</body>
</html>