<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Command Center - Sukabumi One Access</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { DEFAULT: '#0B1E33', light: '#16324F' },
                        gold: { DEFAULT: '#E2933A', deep: '#B5701F' },
                        creamy: { DEFAULT: '#F6F1E7', card: '#FFFDF9' },
                    },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,500;1,9..144,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy: #0B1E33;
            --navy-light: #16324F;
            --cream: #F6F1E7;
            --cream-card: #FFFDF9;
            --gold: #E2933A;
            --gold-deep: #B5701F;
            --hairline: #E7DFCB;
            --muted: #8C8271;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--cream);
            color: var(--navy);
        }

        .font-display { font-family: 'Fraunces', serif; }
        .font-mono { font-family: 'IBM Plex Mono', monospace; }

        /* --- SECURITY-PAPER TEXTURE (guilloché-style crosshatch, not a dot grid) --- */
        .security-paper {
            background-color: var(--cream);
            background-image:
                repeating-linear-gradient(115deg, rgba(11,30,51,0.035) 0px, rgba(11,30,51,0.035) 1px, transparent 1px, transparent 10px),
                repeating-linear-gradient(25deg, rgba(226,147,58,0.035) 0px, rgba(226,147,58,0.035) 1px, transparent 1px, transparent 10px);
        }

        .label-mono {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--muted);
        }

        /* --- TWO-CARD CERTIFICATE STACK --- */
        .cert-stack-row {
            position: relative;
        }

        .cert-id-card {
            background: var(--navy);
            border-radius: 28px;
            box-shadow: 0 30px 60px -22px rgba(11, 30, 51, 0.4);
            padding: 26px 32px 30px;
            margin-bottom: 10px;
            position: relative;
        }

        .cert-form-card {
            background: var(--cream-card);
            border: 1px solid var(--hairline);
            border-radius: 28px;
            box-shadow: 0 30px 60px -22px rgba(11, 30, 51, 0.2);
            padding: 34px 28px 32px;
            margin-top: 10px;
            position: relative;
        }

        @media (min-width: 640px) {
            .cert-form-card { padding: 38px 40px 36px; }
        }

        /* Desktop: cards sit side-by-side, filling the width instead of
           leaving empty page space either side of a narrow stacked card. */
        @media (min-width: 1024px) {
            .cert-stack-row {
                display: flex;
                align-items: stretch;
                gap: 20px;
            }
            .cert-id-card {
                flex: 0 0 40%;
                margin-bottom: 0;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .cert-form-card {
                flex: 1 1 auto;
                margin-top: 0;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
        }

        .seal-desktop {
            position: absolute;
            top: 50%;
            left: calc(40% + 10px);
            transform: translate(-50%, -50%);
            z-index: 30;
        }

        .status-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #34D399;
            animation: dotPulse 2s ease-in-out infinite;
        }
        @keyframes dotPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.35; } }

        /* --- SEAM DIVIDER: seal element bridges the two cards --- */
        .seal-row {
            position: relative;
            z-index: 30;
            display: flex;
            justify-content: center;
        }

        /* --- DIGITAL SEAL (signature element) --- */
        .seal-wrap {
            --seal-size: clamp(72px, 16vw, 96px);
            width: var(--seal-size);
            height: var(--seal-size);
            margin: calc(var(--seal-size) / -2 - 10px) 0;
            position: relative;
            animation: sealIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.5s backwards;
        }
        .seal-ring {
            position: absolute; inset: 0;
            animation: sealSpin 46s linear infinite;
        }
        .seal-ring svg text {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 8.6px;
            font-weight: 600;
            letter-spacing: 0.05em;
            fill: var(--navy);
            text-transform: uppercase;
        }
        .seal-core {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .seal-core-inner {
            width: 58%; height: 58%;
            background: var(--gold);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: var(--navy);
            font-size: clamp(1.1rem, 3vw, 1.5rem);
            box-shadow: 0 8px 20px rgba(226, 147, 58, 0.45), 0 0 0 5px var(--cream-card);
        }

        @keyframes sealSpin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes sealIn { from { opacity: 0; transform: scale(0.7) rotate(-15deg); } to { opacity: 1; transform: scale(1) rotate(0deg); } }

        /* --- ENTRANCE MOTION --- */
        .reveal-fade { animation: fadeUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) backwards; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }

        @media (prefers-reduced-motion: reduce) {
            .seal-ring { animation: none; }
            .seal-wrap, .reveal-fade, .status-dot { animation: none !important; opacity: 1 !important; transform: none !important; }
        }

        /* --- INPUT FIELDS --- */
        .input-group-modern {
            position: relative;
            background: var(--cream-card);
            border-radius: 14px;
            border: 1.5px solid var(--hairline);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .input-group-modern:focus-within {
            border-color: var(--gold);
            box-shadow: 0 8px 20px -10px rgba(226, 147, 58, 0.35);
        }
        .admin-input {
            width: 100%;
            background: transparent;
            border: none;
            padding: 15px 16px 15px 50px;
            font-size: 14px;
            font-weight: 700;
            color: var(--navy);
            outline: none;
        }
        .admin-input::placeholder { color: #C4BCA8; font-weight: 600; }
        .input-icon {
            position: absolute; left: 17px; top: 50%; transform: translateY(-50%);
            font-size: 1.15rem; color: #C7BEA9;
            transition: all 0.3s ease;
        }
        .input-group-modern:focus-within .input-icon { color: var(--gold-deep); }

        /* --- SUBMIT BUTTON --- */
        .btn-command {
            background: var(--navy);
            color: #ffffff;
            width: 100%;
            padding: 17px;
            border-radius: 14px;
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-size: 12px;
            transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
            display: flex; align-items: center; justify-content: center; gap: 10px;
            box-shadow: 0 15px 30px rgba(11, 30, 51, 0.28);
        }
        .btn-command:hover { background: var(--gold); color: var(--navy); box-shadow: 0 18px 36px rgba(226, 147, 58, 0.4); }
        .btn-command:active { transform: scale(0.98); }

        a:focus-visible, button:focus-visible, input:focus-visible {
            outline: 2px solid var(--gold-deep);
            outline-offset: 3px;
        }

        /* --- SIMPLE SUCCESS STATE --- */
        .seal-success {
            background-color: #10B981 !important; /* Hijau */
            color: white !important;
            transform: scale(1.1);
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.5) !important;
            border-color: #ffffff !important;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Animasi kecil saat centang muncul */
        .pop-icon {
            animation: iconPop 0.4s p-bezier forwards;
        }

        @keyframes iconPop {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* --- OTP INPUT --- */
        .otp-input { width: 100%; height: 50px; background: var(--cream); border: 2px solid var(--hairline); border-radius: 12px; text-align: center; font-size: 1.5rem; font-weight: 800; color: var(--navy); outline: none; transition: 0.3s; }
        .otp-input:focus { border-color: var(--gold); box-shadow: 0 0 15px rgba(226, 147, 58, 0.2); }
    </style>
</head>
<body class="min-h-screen security-paper flex items-center justify-center px-5 py-16">

    <div class="w-full max-w-4xl relative">

        {{-- EYEBROW --}}
        <div class="text-center mb-7 reveal-fade">
            <span class="label-mono" style="color: var(--gold-deep);">Portal Resmi &bull; Administrator Diskominfo</span>
        </div>

        <div class="cert-stack-row">

            {{-- CARD 1: IDENTITAS (Navy) --}}
            <div id="leftCard" class="cert-id-card reveal-fade mx-auto max-w-md lg:max-w-none">
                <div class="flex items-center justify-between mb-7">
                    <div>
                        <p class="text-white font-bold text-xs tracking-wide">Sukabumi One Access</p>
                        <p class="label-mono mt-0.5" style="color: rgba(255,255,255,0.45);">Command Center</p>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-3 py-1.5">
                        <span class="status-dot"></span>
                        <span class="label-mono" style="color: #A7F3D0;">Sistem Aktif</span>
                    </div>
                </div>

                <h1 class="font-display text-[2.1rem] sm:text-4xl font-semibold leading-[1.1] tracking-tight mb-3 text-white">
                    Verifikasi <span class="italic text-gold" id="dynamicTitle">Otoritas Anda.</span>
                </h1>
                <p id="dynamicSub" class="text-sm leading-relaxed" style="color: rgba(255,255,255,0.55);">
                    Masukkan kredensial resmi untuk mengakses panel manajemen layanan publik Kota Sukabumi.
                </p>
            </div>

            {{-- SEAM (mobile/tablet): stempel digital --}}
            <div class="main-seal-class seal-row lg:hidden" aria-hidden="true">
                <div class="seal-wrap">
                    <div class="seal-ring">
                        <svg viewBox="0 0 200 200">
                            <path id="sealCircle" d="M100,100 m-90,0 a90,90 0 1,1 180,0 a90,90 0 1,1 -180,0" fill="none"/>
                            <text font-family="IBM Plex Mono" font-size="12" font-weight="bold" fill="#0B1E33">
                                <textPath href="#sealCircle">SUKABUMI ONE ACCESS &bull; VERIFIED AUTHORITY &bull;</textPath>
                            </text>
                        </svg>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="seal-icon-class seal-core-inner">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEAM (desktop): stempel digital --}}
            <div class="main-seal-class seal-desktop hidden lg:flex" aria-hidden="true">
                <div class="seal-wrap" style="margin: 0;">
                    <div class="seal-ring">
                        <svg viewBox="0 0 200 200">
                            <path id="sealCircleDesktop" d="M100,100 m-90,0 a90,90 0 1,1 180,0 a90,90 0 1,1 -180,0" fill="none"/>
                            <text font-family="IBM Plex Mono" font-size="12" font-weight="bold" fill="#0B1E33">
                                <textPath href="#sealCircleDesktop">SUKABUMI ONE ACCESS &bull; VERIFIED AUTHORITY &bull;</textPath>
                            </text>
                        </svg>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="seal-icon-class seal-core-inner">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: FORM (Cream) --}}
            <div id="rightCard" class="cert-form-card reveal-fade mx-auto max-w-md lg:max-w-none">
                
                {{-- Error Feedback --}}
                @if ($errors->any())
                    <div class="rounded-2xl mb-6 flex items-center gap-3 p-4" style="background: #FDF1EE; border: 1px solid #F5D6CC;">
                        <i class="bi bi-exclamation-triangle-fill text-red-600"></i>
                        <p class="text-xs font-bold uppercase tracking-tight" style="color: #B5401E;">{{ $errors->first() }}</p>
                    </div>
                @endif

                <form action="{{ route('admin.login.submit') }}" method="POST" id="adminLoginForm">
                    @csrf

                    {{-- STEP 1: LOGIN (Username & Password) --}}
                    <div id="loginFields" class="space-y-5 fade-in">
                        <div>
                            <label class="label-mono ml-1 mb-2 block">ID Otoritas</label>
                            <div class="input-group-modern">
                                <input type="text" name="login" id="login_id" value="{{ old('login') }}" class="admin-input" placeholder="Username atau email" required autofocus>
                                <i class="bi bi-person-bounding-box input-icon"></i>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center ml-1 mb-2">
                                <label class="label-mono">Kunci Rahasia</label>
                                <a href="{{ route('admin.forgot.index') }}" class="text-[10px] font-black text-gold hover:text-navy transition-colors uppercase tracking-widest">Lupa Password?</a>
                            </div>
                            <div class="input-group-modern">
                                <input type="password" name="password" id="login_pass" class="admin-input" placeholder="••••••••••••" required>
                                <i class="bi bi-key-fill input-icon"></i>
                                <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2" style="color: #C7BEA9;" onclick="togglePass()">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="pt-3">
                            <button type="button" id="btnNext" onclick="goToOTP()" class="btn-command">
                                LANJUTKAN <i class="bi bi-arrow-right-short text-xl"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: OTP (Hidden by default) --}}
                    <div id="otpFields" class="hidden space-y-6 fade-in">
                        <div class="text-center">
                            <h2 class="font-display text-xl font-bold">Otorisasi Email</h2>
                            <p class="text-xs text-slate-400 mt-1">Masukkan 6-digit kode keamanan yang kami kirim.</p>
                        </div>
                        <div class="grid grid-cols-6 gap-2" id="otpGroup">
                            <input type="text" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" class="otp-input" onkeyup="otpNav(this, 0)">
                            <input type="text" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" class="otp-input" onkeyup="otpNav(this, 1)">
                            <input type="text" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" class="otp-input" onkeyup="otpNav(this, 2)">
                            <input type="text" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" class="otp-input" onkeyup="otpNav(this, 3)">
                            <input type="text" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" class="otp-input" onkeyup="otpNav(this, 4)">
                            <input type="text" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" class="otp-input" onkeyup="otpNav(this, 5)">
                        </div>
                        <div class="text-center">
                            <button type="button" id="btnResendOTP" onclick="resendOTP()" class="label-mono text-xs cursor-pointer hover:underline border-0 bg-transparent" style="color: var(--gold-deep);">Kirim ulang kode</button>
                        </div>
                    </div>

                    <div class="flex justify-center pt-4">
                        <a href="{{ route('home') }}" class="label-mono flex items-center gap-2 hover:opacity-70 transition-opacity">
                            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                        </a>
                    </div>
                </form>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="text-center mt-7">
            <p class="label-mono">
                <i class="bi bi-lock-fill mr-1"></i> Diskominfo Kota Sukabumi &bull; {{ date('Y') }} &bull; Koneksi terenkripsi
            </p>
        </div>

    </div>

    <script>
        // 1. Toggle Mata Password
        function togglePass() {
            const p = document.getElementById('login_pass');
            const i = document.getElementById('eyeIcon');
            if (p.type === 'password') {
                p.type = 'text';
                i.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                p.type = 'password';
                i.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        // 1. TAHAP 1: Kirim data ke server untuk cek User & Pass
        async function goToOTP() {
            const login = document.getElementById('login_id').value;
            const password = document.getElementById('login_pass').value;
            const btnNext = document.getElementById('btnNext');

            if(!login || !password) return alert("Silakan isi semua kolom.");

            // Loading State
            btnNext.disabled = true;
            btnNext.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> MEMVERIFIKASI...';

            try {
                const response = await fetch("{{ route('admin.login.precheck') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ login, password })
                });

                const data = await response.json();

                if (data.success) {
                    // JIKA SERVER BILANG OK, BARU PINDAH TAMPILAN
                    document.getElementById('loginFields').style.display = 'none';
                    document.getElementById('otpFields').style.display = 'block';
                    document.getElementById('dynamicTitle').innerText = "Email Verif.";
                    document.getElementById('dynamicSub').innerText = data.message;
                    
                    document.querySelectorAll('.seal-icon-class').forEach(el => {
                        el.innerHTML = '<i class="bi bi-envelope-at-fill"></i>';
                    });
                    document.getElementById('otpGroup').children[0].focus();
                } else {
                    alert(data.message); // Muncul jika salah pass/bukan admin
                }
            } catch (error) {
                alert("Terjadi kesalahan koneksi.");
            } finally {
                btnNext.disabled = false;
                btnNext.innerHTML =`LANJUTKAN <i class="bi bi-arrow-right-short text-xl"></i>`;
            }
        }

        async function resendOTP() {
            const btn = document.getElementById('btnResendOTP');

            if (btn.disabled) return;

            btn.disabled = true;
            btn.innerText = 'MENGIRIM ULANG...';

            try {
                const response = await fetch("{{ route('admin.login.resend') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    btn.innerText = 'KODE TERKIRIM';

                    document.querySelectorAll('input[name="otp[]"]').forEach(input => input.value = '');
                    document.querySelectorAll('input[name="otp[]"]')[0].focus();

                    setTimeout(() => {
                        btn.disabled = false;
                        btn.innerText = 'Kirim ulang kode';
                    }, 30000);
                } else {
                    alert(data.message);
                    btn.disabled = false;
                    btn.innerText = 'Kirim ulang kode';
                }
            } catch (error) {
                alert('Gagal mengirim ulang kode OTP.');
                btn.disabled = false;
                btn.innerText = 'Kirim ulang kode';
            }
        }

        // 2. Navigasi Kotak OTP
        function otpNav(el, index) {
            if (el.value.length === 1 && index < 5) {
                document.getElementById('otpGroup').children[index + 1].focus();
            }
            if (index === 5 && el.value.length === 1) {
                triggerBlast(); // Panggil ledakan jika kotak terakhir diisi
            }
        }

        // 3. TAHAP 2: Verifikasi OTP & Efek Terhempas
        async function triggerBlast() {
            const otpInputs = document.querySelectorAll('input[name="otp[]"]');
            const otpValues = Array.from(otpInputs).map(i => i.value);

            try {
                const response = await fetch("{{ route('admin.login.submit') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ otp: otpValues })
                });

                const data = await response.json();

                if (data.success) {
                    // 1. Cari semua lingkaran stempel (Mobile & Desktop)
                    const icons = document.querySelectorAll('.seal-icon-class');
                    
                    icons.forEach(el => {
                        // Tambahkan class sukses (Warna Hijau)
                        el.classList.add('seal-success');
                        // Ganti ikon menjadi Centang dengan animasi pop
                        el.innerHTML = '<i class="bi bi-check-lg pop-icon"></i>';
                    });

                    // 2. Beri jeda sebentar agar user melihat centang, lalu pindah halaman
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 800); // 0.8 detik saja biar terasa cepat

                } else {
                    // Jika salah: Getarkan kotak OTP (Feedback visual)
                    const otpGroup = document.getElementById('otpGroup');
                    otpGroup.style.animation = 'shake 0.4s';
                    setTimeout(() => otpGroup.style.animation = '', 400);
                    
                    alert(data.message);
                    otpInputs.forEach(i => i.value = ""); // Kosongkan kotak
                    otpInputs[0].focus();
                }
            } catch (e) {
                alert("Otoritas Gagal. Periksa koneksi.");
            }
        }
    </script>
</body>
</html>