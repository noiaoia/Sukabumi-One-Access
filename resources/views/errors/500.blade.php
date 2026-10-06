<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gangguan Sistem - Sukabumi One Access</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background: #060D18;
            min-height: 100vh;
            overflow-x: hidden;
        }

        canvas#stars {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .noise-overlay {
            position: fixed;
            inset: 0;
            opacity: 0.035;
            pointer-events: none;
            z-index: 1;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }

        .bg-dot-matrix {
            position: fixed;
            inset: 0;
            opacity: 0.1;
            z-index: 1;
            background-image: radial-gradient(#E8A33D 1px, transparent 0);
            background-size: 34px 34px;
        }

        .aurora {
            position: fixed;
            border-radius: 9999px;
            filter: blur(130px);
            pointer-events: none;
            z-index: 1;
            transition: transform 0.6s cubic-bezier(0.16,1,0.3,1);
        }

        .aurora-orange { top: -15%; right: -10%; width: 600px; height: 600px; background: rgba(249,115,22,0.13); }
        .aurora-gold { bottom: -20%; left: -10%; width: 560px; height: 560px; background: rgba(232,163,61,0.12); }
        .aurora-blue { top: 42%; left: 45%; width: 400px; height: 400px; background: rgba(59,130,246,0.06); }

        .error-number {
            font-size: clamp(6rem, 22vw, 15rem);
            font-weight: 900;
            line-height: 0.85;
            letter-spacing: -0.04em;
            background: linear-gradient(160deg, #ffffff 0%, #8fa3bd 55%, #3d4f68 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 80px rgba(249,115,22,0.18);
            position: relative;
            display: inline-block;
        }

        .error-number.glitch::before,
        .error-number.glitch::after {
            content: '500';
            position: absolute;
            inset: 0;
            background: none;
            -webkit-text-fill-color: initial;
        }

        .error-number.glitch::before {
            color: #f97316;
            opacity: 0.6;
            animation: glitchShift 0.25s linear;
            clip-path: polygon(0 20%, 100% 20%, 100% 35%, 0 35%);
        }

        .error-number.glitch::after {
            color: #E8A33D;
            opacity: 0.5;
            animation: glitchShift 0.25s linear reverse;
            clip-path: polygon(0 60%, 100% 60%, 100% 78%, 0 78%);
        }

        @keyframes glitchShift {
            0% { transform: translateX(0); }
            25% { transform: translateX(-6px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-3px); }
            100% { transform: translateX(0); }
        }

        .server-wrap {
            transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
            animation: serverFloat 5s ease-in-out infinite;
        }

        @keyframes serverFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        .server-light {
            animation: blinkLight 1.6s ease-in-out infinite;
        }
        .server-light:nth-of-type(2) { animation-delay: 0.5s; }
        .server-light:nth-of-type(3) { animation-delay: 1s; }

        @keyframes blinkLight {
            0%, 100% { opacity: 0.25; }
            50% { opacity: 1; }
        }

        .server-spark {
            animation: sparkFlicker 2.2s ease-in-out infinite;
            transform-origin: center;
        }

        @keyframes sparkFlicker {
            0%, 100% { opacity: 0; transform: scale(0.8) rotate(-5deg); }
            48% { opacity: 0; }
            50% { opacity: 1; transform: scale(1.1) rotate(5deg); }
            56% { opacity: 0.2; }
            60% { opacity: 0.9; transform: scale(1) rotate(0deg); }
            70% { opacity: 0; }
        }

        .search-glass {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 1.25rem;
            padding: 0.35rem;
            display: flex;
            align-items: center;
            backdrop-filter: blur(12px);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .search-glass:focus-within {
            border-color: rgba(232,163,61,0.5);
            box-shadow: 0 0 0 4px rgba(232,163,61,0.08);
        }

        .search-glass input {
            background: transparent;
            border: none;
            outline: none;
            color: #ffffff;
            font-size: 0.85rem;
            padding: 0.65rem 0.5rem;
            width: 100%;
        }

        .search-glass input::placeholder { color: rgba(255,255,255,0.35); }

        .btn-magnetic {
            transition: transform 0.25s cubic-bezier(0.16,1,0.3,1), box-shadow 0.25s ease, background 0.25s ease;
        }

        .btn-primary-glow {
            background: #E8A33D;
            color: #2A1704;
            box-shadow: 0 10px 30px -8px rgba(232,163,61,0.5);
        }
        .btn-primary-glow:hover { box-shadow: 0 18px 38px -8px rgba(232,163,61,0.65); }

        .btn-ghost-glass {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
        }
        .btn-ghost-glass:hover { background: rgba(255,255,255,0.09); }

        .status-strip {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            padding: 10px 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #f97316;
            box-shadow: 0 0 8px 2px rgba(249,115,22,0.6);
            animation: blinkLight 1.4s ease-in-out infinite;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { opacity: 0; animation: fadeUp 0.7s cubic-bezier(0.16,1,0.3,1) forwards; }

        .btn-reload i.spin-on-click { transition: transform 0.5s ease; }
    </style>
</head>
<body class="text-white">

    <canvas id="stars"></canvas>
    <div class="noise-overlay"></div>
    <div class="bg-dot-matrix"></div>
    <div class="aurora aurora-orange" id="auroraOrange"></div>
    <div class="aurora aurora-gold" id="auroraGold"></div>
    <div class="aurora aurora-blue" id="auroraBlue"></div>

    <main class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4 sm:px-8 py-16">

        <div class="max-w-2xl w-full text-center">

            {{-- Ilustrasi server rack kustom (SVG) --}}
            <div class="fade-up mb-6" style="animation-delay: 0s;">
                <div class="server-wrap inline-block relative" id="serverWrap">
                    <svg width="150" height="150" viewBox="0 0 150 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="42" y="30" width="66" height="90" rx="10" fill="#1c2a3d" stroke="#3d4f68" stroke-width="2"/>

                        <rect x="50" y="42" width="50" height="16" rx="4" fill="#0f1c30"/>
                        <circle class="server-light" cx="58" cy="50" r="3" fill="#f97316"/>
                        <rect x="66" y="47" width="26" height="6" rx="2" fill="#3d4f68"/>

                        <rect x="50" y="64" width="50" height="16" rx="4" fill="#0f1c30"/>
                        <circle class="server-light" cx="58" cy="72" r="3" fill="#E8A33D"/>
                        <rect x="66" y="69" width="26" height="6" rx="2" fill="#3d4f68"/>

                        <rect x="50" y="86" width="50" height="16" rx="4" fill="#0f1c30"/>
                        <circle class="server-light" cx="58" cy="94" r="3" fill="#f97316"/>
                        <rect x="66" y="91" width="26" height="6" rx="2" fill="#3d4f68"/>

                        <g class="server-spark">
                            <path d="M118 55 L106 78 L114 78 L104 100 L124 74 L115 74 Z" fill="#E8A33D"/>
                        </g>
                    </svg>
                </div>
            </div>

            <div class="fade-up" style="animation-delay: 0.1s;">
                <span class="error-number" id="errorNumber">500</span>
            </div>

            <div class="fade-up mt-2" style="animation-delay: 0.2s;">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight mb-3">
                    Ada gangguan di sistem kami
                </h1>
                <p class="text-slate-400 text-sm sm:text-base max-w-md mx-auto leading-relaxed mb-5">
                    Ini bukan kesalahan Anda. Server kami sedang mengalami kendala teknis. Tim kami sudah menerima laporan otomatis dan sedang menanganinya.
                </p>
            </div>

            <div class="fade-up mb-8" style="animation-delay: 0.28s;">
                <div class="status-strip">
                    <span class="status-dot"></span>
                    <span class="text-xs text-slate-300">Insiden tercatat pada <span id="incidentTime" class="font-semibold text-white"></span> WIB</span>
                </div>
            </div>

            <div class="fade-up mb-6" style="animation-delay: 0.34s;">
                <form action="{{ route('layanan') ?? '#' }}" method="GET" class="search-glass max-w-md mx-auto">
                    <i class="bi bi-search text-white/40 ml-3"></i>
                    <input type="text" name="q" placeholder='Cari layanan, misal "KTP" atau "izin usaha"'>
                </form>
            </div>

            <div class="fade-up flex flex-col sm:flex-row gap-3 justify-center" style="animation-delay: 0.4s;">
                <button onclick="reloadPage(this)"
                        class="btn-magnetic btn-primary-glow btn-reload inline-flex items-center justify-center px-8 py-3.5 rounded-xl font-bold text-sm">
                    <i class="bi bi-arrow-clockwise mr-2 spin-on-click" id="reloadIcon"></i> Muat Ulang Halaman
                </button>
                <a href="{{ route('home') }}"
                   class="btn-magnetic btn-ghost-glass inline-flex items-center justify-center text-white px-8 py-3.5 rounded-xl font-bold text-sm">
                    <i class="bi bi-house-door-fill mr-2 text-accent"></i> Kembali ke Beranda
                </a>
            </div>

            <div class="fade-up mt-10" style="animation-delay: 0.5s;">
                <p class="text-slate-500 text-xs">
                    Masih bermasalah setelah beberapa saat?
                    <a href="{{ route('pengaduan') ?? '#' }}" class="text-accent hover:text-white transition font-semibold">Laporkan di sini</a>
                </p>
            </div>

        </div>

        <div class="fade-up absolute bottom-6 left-0 right-0 flex items-center justify-center gap-2 opacity-60" style="animation-delay: 0.6s;">
            <div class="w-5 h-5 rounded-md bg-white/10 flex items-center justify-center">
                <i class="bi bi-building-fill text-accent text-[10px]"></i>
            </div>
            <span class="text-[11px] text-slate-400">Sukabumi One Access &mdash; Diskominfo Kota Sukabumi</span>
        </div>

    </main>

    <script>
        // 0. WAKTU INSIDEN (real-time saat halaman dimuat)
        (function () {
            const el = document.getElementById('incidentTime');
            if (!el) return;
            const now = new Date();
            const hh = String(now.getHours()).padStart(2, '0');
            const mm = String(now.getMinutes()).padStart(2, '0');
            el.textContent = `${hh}:${mm}`;
        })();

        // 1. STARFIELD PARTIKEL BERKELIP
        (function () {
            const canvas = document.getElementById('stars');
            const ctx = canvas.getContext('2d');
            let stars = [];

            function resize() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            }

            function initStars() {
                const count = Math.floor((canvas.width * canvas.height) / 9000);
                stars = Array.from({ length: count }, () => ({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    r: Math.random() * 1.2 + 0.3,
                    baseAlpha: Math.random() * 0.5 + 0.15,
                    speed: Math.random() * 0.015 + 0.005,
                    phase: Math.random() * Math.PI * 2,
                }));
            }

            function draw(time) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                stars.forEach(s => {
                    const twinkle = Math.sin(time * s.speed + s.phase) * 0.4 + 0.6;
                    ctx.beginPath();
                    ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(255,255,255,${s.baseAlpha * twinkle})`;
                    ctx.fill();
                });
                requestAnimationFrame(draw);
            }

            window.addEventListener('resize', () => { resize(); initStars(); });
            resize();
            initStars();
            requestAnimationFrame(draw);
        })();

        // 2. PARALLAX MENGIKUTI GERAKAN MOUSE
        (function () {
            const orange = document.getElementById('auroraOrange');
            const gold = document.getElementById('auroraGold');
            const blue = document.getElementById('auroraBlue');
            const server = document.getElementById('serverWrap');

            window.addEventListener('mousemove', (e) => {
                const px = (e.clientX / window.innerWidth - 0.5);
                const py = (e.clientY / window.innerHeight - 0.5);

                orange.style.transform = `translate(${px * 40}px, ${py * 40}px)`;
                gold.style.transform = `translate(${px * -35}px, ${py * -35}px)`;
                blue.style.transform = `translate(${px * 25}px, ${py * -25}px)`;
                server.style.transform = `translate(${px * 14}px, ${py * 10}px)`;
            }, { passive: true });
        })();

        // 3. EFEK GLITCH HALUS PADA ANGKA 500
        (function () {
            const el = document.getElementById('errorNumber');
            if (!el) return;

            function triggerGlitch() {
                el.classList.add('glitch');
                setTimeout(() => el.classList.remove('glitch'), 250);
                setTimeout(triggerGlitch, 3500 + Math.random() * 4000);
            }

            setTimeout(triggerGlitch, 2000);
        })();

        // 4. TOMBOL MUAT ULANG: ikon berputar dulu sebelum reload beneran
        function reloadPage(btn) {
            const icon = document.getElementById('reloadIcon');
            icon.style.transform = 'rotate(360deg)';
            btn.disabled = true;
            setTimeout(() => window.location.reload(), 450);
        }
    </script>

</body>
</html>