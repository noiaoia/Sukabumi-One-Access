<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - Sukabumi One Access</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#123457', dark: '#0A1E33' },
                        accent: { DEFAULT: '#E8A33D', dark: '#3A2205' },
                    },
                }
            }
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800;900&family=Space+Grotesk:wght@300;700&display=swap" rel="stylesheet">

    <style>
        :root { --soa-gold: #E8A33D; }

        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #040812; min-height: 100vh; color: white; overflow-x: hidden; }

        /* 1. BACKGROUND ELEMENT */
        canvas#stars { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
        .noise-overlay { position: fixed; inset: 0; opacity: 0.04; pointer-events: none; z-index: 1; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); }
        .bg-dot-matrix { position: fixed; inset: 0; opacity: 0.1; z-index: 1; background-image: radial-gradient(#E8A33D 1px, transparent 0); background-size: 40px 40px; }
        .aurora { position: fixed; border-radius: 9999px; filter: blur(120px); pointer-events: none; z-index: 1; }
        .aurora-blue { top: -10%; right: -5%; width: 600px; height: 600px; background: rgba(59,130,246,0.1); }
        .aurora-gold { bottom: -15%; left: -5%; width: 500px; height: 500px; background: rgba(232,163,61,0.08); }

        /* 2. THE SIGNAL ENVELOPE */
        .envelope-container {
            position: relative;
            width: 130px;
            height: 130px;
            margin: 0 auto 3rem;
        }

        .signal-wave {
            position: absolute;
            inset: 0;
            border: 1px solid var(--soa-gold);
            border-radius: 35%;
            opacity: 0;
            animation: signal-pulse 3s infinite;
        }
        .signal-wave:nth-child(2) { animation-delay: 1s; }

        @keyframes signal-pulse {
            0% { transform: scale(1); opacity: 0.5; }
            100% { transform: scale(2); opacity: 0; }
        }

        .icon-box-premium {
            position: relative;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #E8A33D 0%, #b37a1f 100%);
            border-radius: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            color: #0A1E33;
            box-shadow: 0 0 50px rgba(232, 163, 61, 0.4);
            z-index: 2;
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* 3. BUTTONS */
        .btn-gold-fill {
            background: #E8A33D;
            color: #0A1E33;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 30px rgba(232, 163, 61, 0.2);
        }

        .btn-gold-fill:hover {
            background: white;
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(255, 255, 255, 0.1);
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            transition: 0.3s;
        }
        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            border-color: #E8A33D;
        }

        .btn-glass:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .btn-glass:disabled:hover {
            background: rgba(255, 255, 255, 0.03);
            color: #94a3b8;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .scan-bar {
            width: 120px;
            height: 1px;
            background: linear-gradient(90deg, transparent, #E8A33D, transparent);
            margin: 2.5rem auto;
            position: relative;
            overflow: hidden;
        }
        .scan-bar::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: white;
            animation: scanning 2s infinite;
        }
        @keyframes scanning { to { left: 100%; } }

        .spam-tip {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 12px 16px;
            display: inline-flex;
            align-items: flex-start;
            gap: 8px;
            text-align: left;
        }

        @media (max-width: 480px) {
            .envelope-container { width: 100px; height: 100px; margin-bottom: 2rem; }
            .icon-box-premium { font-size: 2.6rem; border-radius: 28px; }
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center p-6 text-center">

    <canvas id="stars"></canvas>
    <div class="noise-overlay"></div>
    <div class="bg-dot-matrix"></div>
    <div class="aurora aurora-blue"></div>
    <div class="aurora aurora-gold"></div>

    <main class="relative z-10 max-w-lg w-full py-10">

        <!-- Animated Signal Icon -->
        <div class="envelope-container">
            <div class="signal-wave"></div>
            <div class="signal-wave"></div>
            <div class="icon-box-premium">
                <i class="bi bi-envelope-at-fill"></i>
            </div>
        </div>

        <div class="space-y-6">
            <div>
                <p class="text-accent text-[11px] font-black uppercase tracking-[0.5em] mb-3">Registration Successful</p>
                <h1 style="font-family: 'Space Grotesk'" class="text-4xl md:text-6xl font-extrabold leading-none tracking-tighter">
                    Cek Email <br> <span class="text-white/30">Sekarang.</span>
                </h1>
            </div>

            <div class="scan-bar"></div>

            <p class="text-slate-400 text-sm md:text-base max-w-md mx-auto leading-relaxed font-light">
                Hatur nuhun! Data Anda telah kami simpan. Untuk mengaktifkan akses <strong class="text-white font-semibold">Sukabumi One Access</strong>, silakan klik tautan otorisasi di dalam email Anda.
            </p>

            @if(Auth::check() && Auth::user()->email)
                <p class="text-slate-500 text-xs">
                    Terkirim ke <span class="text-white font-semibold">{{ Auth::user()->email }}</span>
                </p>
            @endif

            {{-- Notifikasi Jika Link Berhasil Dikirim Ulang --}}
            @if (session('success'))
                <div class="text-emerald-400 text-[10px] font-bold uppercase tracking-widest">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Tips folder spam --}}
            <div class="flex justify-center">
                <div class="spam-tip">
                    <i class="bi bi-info-circle text-accent mt-0.5"></i>
                    <span class="text-[11px] text-slate-400 leading-relaxed">
                        Belum masuk juga setelah beberapa menit? Coba cek folder <strong class="text-slate-300">Spam</strong> atau <strong class="text-slate-300">Promosi</strong>.
                    </span>
                </div>
            </div>

            <div class="pt-6 flex flex-col items-center gap-4">

                {{-- Tombol 1: Buka Gmail (Jalan Pintas) --}}
                <a href="https://mail.google.com" target="_blank" rel="noopener" class="btn-gold-fill w-full max-w-xs py-4 rounded-2xl text-[11px] flex items-center justify-center gap-3 active:scale-95">
                    BUKA GMAIL <i class="bi bi-box-arrow-up-right text-lg"></i>
                </a>

                {{-- Tombol 2: Kirim Ulang Link (dengan cooldown biar nggak spam klik) --}}
                <form method="POST" action="{{ route('verification.send') }}" class="w-full max-w-xs" id="resendForm">
                    @csrf
                    <button type="submit" id="resendBtn" class="btn-glass w-full py-4 rounded-2xl text-[10px] active:scale-95">
                        Kirim Ulang Link Aktivasi
                    </button>
                </form>

                {{-- Tombol 3: Kembali ke Login --}}
                <a href="{{ route('login') }}" class="text-[10px] font-bold text-slate-600 hover:text-white transition-colors uppercase tracking-[0.2em] mt-4">
                    Sudah Verifikasi? Masuk Sini
                </a>
            </div>
        </div>

        <!-- Footer HUD -->
        <div class="mt-20 flex justify-between items-center opacity-20 text-[8px] font-black uppercase tracking-[0.4em] border-t border-white/10 pt-6">
            <span>Identity Protocol</span>
            <span>SOA Identity V.1</span>
        </div>
    </main>

    <script>
        // STARFIELD ANIMATION
        (function () {
            const canvas = document.getElementById('stars');
            const ctx = canvas.getContext('2d');
            let stars = [];
            function resize() { canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
            function initStars() {
                const count = Math.floor((canvas.width * canvas.height) / 10000);
                stars = Array.from({ length: count }, () => ({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    r: Math.random() * 1.5,
                    speed: Math.random() * 0.02,
                    phase: Math.random() * Math.PI * 2
                }));
            }
            function draw(time) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                stars.forEach(s => {
                    const alpha = Math.sin(time * s.speed + s.phase) * 0.5 + 0.5;
                    ctx.beginPath(); ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(255, 255, 255, ${alpha * 0.4})`; ctx.fill();
                });
                requestAnimationFrame(draw);
            }
            window.addEventListener('resize', () => { resize(); initStars(); });
            resize(); initStars(); requestAnimationFrame(draw);
        })();

        // RESEND COOLDOWN: cegah user spam-klik kirim ulang, kasih feedback jelas
        (function () {
            const form = document.getElementById('resendForm');
            const btn = document.getElementById('resendBtn');
            if (!form || !btn) return;

            const COOLDOWN_SECONDS = 60;
            const storageKey = 'soa_resend_cooldown_until';

            function startCooldown(untilTimestamp) {
                localStorage.setItem(storageKey, untilTimestamp);
                tick();
            }

            function tick() {
                const until = parseInt(localStorage.getItem(storageKey) || '0', 10);
                const remaining = Math.ceil((until - Date.now()) / 1000);

                if (remaining > 0) {
                    btn.disabled = true;
                    btn.textContent = `Coba lagi dalam ${remaining}s`;
                    setTimeout(tick, 1000);
                } else {
                    btn.disabled = false;
                    btn.textContent = 'Kirim Ulang Link Aktivasi';
                }
            }

            form.addEventListener('submit', () => {
                startCooldown(Date.now() + COOLDOWN_SECONDS * 1000);
            });

            tick(); // cek status cooldown saat halaman dimuat (misal setelah redirect balik)
        })();
    </script>
</body>
</html>