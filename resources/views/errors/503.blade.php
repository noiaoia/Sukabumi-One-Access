<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeliharaan Sistem - Sukabumi One Access</title>
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

        .aurora-teal { top: -15%; right: -10%; width: 600px; height: 600px; background: rgba(20,184,166,0.13); }
        .aurora-gold { bottom: -20%; left: -10%; width: 560px; height: 560px; background: rgba(232,163,61,0.12); }
        .aurora-blue { top: 42%; left: 45%; width: 400px; height: 400px; background: rgba(59,130,246,0.08); }

        .error-number {
            font-size: clamp(6rem, 22vw, 15rem);
            font-weight: 900;
            line-height: 0.85;
            letter-spacing: -0.04em;
            background: linear-gradient(160deg, #ffffff 0%, #8fa3bd 55%, #3d4f68 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 80px rgba(20,184,166,0.2);
            position: relative;
            display: inline-block;
        }

        .error-number.glitch::before,
        .error-number.glitch::after {
            content: '503';
            position: absolute;
            inset: 0;
            background: none;
            -webkit-text-fill-color: initial;
        }

        .error-number.glitch::before {
            color: #14b8a6;
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

        .gear-wrap {
            transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
            animation: gearFloat 5s ease-in-out infinite;
        }

        @keyframes gearFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .gear-big {
            transform-origin: 60px 60px;
            animation: spinSlow 9s linear infinite;
        }

        .gear-small {
            transform-origin: 108px 100px;
            animation: spinSlowReverse 6s linear infinite;
        }

        @keyframes spinSlow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes spinSlowReverse { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }

        .wrench-tap {
            transform-origin: 90% 90%;
            animation: wrenchTap 3s ease-in-out infinite;
        }

        @keyframes wrenchTap {
            0%, 80%, 100% { transform: rotate(0deg); }
            85% { transform: rotate(-12deg); }
            90% { transform: rotate(0deg); }
            95% { transform: rotate(-8deg); }
        }

        .search-glass {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 1.25rem;
            padding: 0.35rem;
            display: flex;
            align-items: center;
            backdrop-filter: blur(12px);
        }

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
            background: rgba(20,184,166,0.08);
            border: 1px solid rgba(20,184,166,0.25);
            border-radius: 14px;
            padding: 10px 18px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #14b8a6;
            box-shadow: 0 0 8px 2px rgba(20,184,166,0.6);
            animation: blinkDot 1.4s ease-in-out infinite;
        }

        @keyframes blinkDot { 0%, 100% { opacity: 0.3; } 50% { opacity: 1; } }

        .contact-chip {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { opacity: 0; animation: fadeUp 0.7s cubic-bezier(0.16,1,0.3,1) forwards; }
    </style>
</head>
<body class="text-white">

    <canvas id="stars"></canvas>
    <div class="noise-overlay"></div>
    <div class="bg-dot-matrix"></div>
    <div class="aurora aurora-teal" id="auroraTeal"></div>
    <div class="aurora aurora-gold" id="auroraGold"></div>
    <div class="aurora aurora-blue" id="auroraBlue"></div>

    <main class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4 sm:px-8 py-16">

        <div class="max-w-2xl w-full text-center">

            {{-- Ilustrasi gear/perkakas kustom (SVG) --}}
            <div class="fade-up mb-6" style="animation-delay: 0s;">
                <div class="gear-wrap inline-block relative" id="gearWrap">
                    <svg width="150" height="150" viewBox="0 0 150 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g class="gear-big">
                            <circle cx="60" cy="60" r="26" fill="#1c2a3d" stroke="#3d4f68" stroke-width="3"/>
                            <circle cx="60" cy="60" r="9" fill="#0A1E33"/>
                            <g fill="#3d4f68">
                                <rect x="55" y="26" width="10" height="12" rx="2"/>
                                <rect x="55" y="82" width="10" height="12" rx="2"/>
                                <rect x="26" y="55" width="12" height="10" rx="2"/>
                                <rect x="82" y="55" width="12" height="10" rx="2"/>
                                <rect x="36" y="36" width="10" height="10" rx="2" transform="rotate(45 41 41)"/>
                                <rect x="74" y="36" width="10" height="10" rx="2" transform="rotate(45 79 41)"/>
                                <rect x="36" y="74" width="10" height="10" rx="2" transform="rotate(45 41 79)"/>
                                <rect x="74" y="74" width="10" height="10" rx="2" transform="rotate(45 79 79)"/>
                            </g>
                        </g>

                        <g class="gear-small">
                            <circle cx="108" cy="100" r="17" fill="#E8A33D" stroke="#B97A1F" stroke-width="2"/>
                            <circle cx="108" cy="100" r="6" fill="#3A2205"/>
                            <g fill="#B97A1F">
                                <rect x="104" y="79" width="8" height="8" rx="2"/>
                                <rect x="104" y="113" width="8" height="8" rx="2"/>
                                <rect x="83" y="96" width="8" height="8" rx="2"/>
                                <rect x="125" y="96" width="8" height="8" rx="2"/>
                            </g>
                        </g>

                        <g class="wrench-tap">
                            <rect x="115" y="35" width="8" height="42" rx="4" fill="#94a3b8" transform="rotate(35 119 56)"/>
                            <circle cx="128" cy="34" r="9" fill="#94a3b8"/>
                            <circle cx="128" cy="34" r="4" fill="#0A1E33"/>
                        </g>
                    </svg>
                </div>
            </div>

            <div class="fade-up" style="animation-delay: 0.1s;">
                <span class="error-number" id="errorNumber">503</span>
            </div>

            <div class="fade-up mt-2" style="animation-delay: 0.2s;">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight mb-3">
                    Sedang dalam pemeliharaan terjadwal
                </h1>
                <p class="text-slate-400 text-sm sm:text-base max-w-md mx-auto leading-relaxed mb-5">
                    Kami sedang meningkatkan kualitas layanan Sukabumi One Access. Mohon maaf atas ketidaknyamanannya, layanan akan kembali normal sebentar lagi.
                </p>
            </div>

            <div class="fade-up mb-8" style="animation-delay: 0.28s;">
                <div class="status-strip">
                    <span class="status-dot"></span>
                    <span class="text-xs text-slate-300">
                        @isset($retryAfter)
                            Perkiraan kembali normal dalam <span class="font-semibold text-white">{{ $retryAfter }}</span> menit
                        @else
                            Sedang berjalan &mdash; halaman ini akan cek ulang otomatis
                        @endisset
                    </span>
                </div>
            </div>

            <div class="fade-up flex flex-col sm:flex-row gap-3 justify-center mb-8" style="animation-delay: 0.4s;">
                <button onclick="reloadPage(this)"
                        class="btn-magnetic btn-primary-glow inline-flex items-center justify-center px-8 py-3.5 rounded-xl font-bold text-sm">
                    <i class="bi bi-arrow-clockwise mr-2" id="reloadIcon"></i> Cek Lagi Sekarang
                </button>
            </div>

            <div class="fade-up grid sm:grid-cols-2 gap-3 max-w-lg mx-auto" style="animation-delay: 0.46s;">
                <div class="contact-chip">
                    <i class="bi bi-envelope-at text-accent"></i>
                    <span class="text-xs text-slate-300">diskominfo@sukabumikota.go.id</span>
                </div>
                <div class="contact-chip">
                    <i class="bi bi-telephone text-accent"></i>
                    <span class="text-xs text-slate-300">(0266) 221-xxx</span>
                </div>
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
            const teal = document.getElementById('auroraTeal');
            const gold = document.getElementById('auroraGold');
            const blue = document.getElementById('auroraBlue');
            const gear = document.getElementById('gearWrap');

            window.addEventListener('mousemove', (e) => {
                const px = (e.clientX / window.innerWidth - 0.5);
                const py = (e.clientY / window.innerHeight - 0.5);

                teal.style.transform = `translate(${px * 40}px, ${py * 40}px)`;
                gold.style.transform = `translate(${px * -35}px, ${py * -35}px)`;
                blue.style.transform = `translate(${px * 25}px, ${py * -25}px)`;
                gear.style.transform = `translate(${px * 14}px, ${py * 10}px)`;
            }, { passive: true });
        })();

        // 3. EFEK GLITCH HALUS PADA ANGKA 503
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

        // 4. TOMBOL CEK LAGI
        function reloadPage(btn) {
            const icon = document.getElementById('reloadIcon');
            icon.style.transition = 'transform 0.5s ease';
            icon.style.transform = 'rotate(360deg)';
            btn.disabled = true;
            setTimeout(() => window.location.reload(), 450);
        }

        // 5. AUTO CEK ULANG BERKALA (misal tiap 30 detik), biar user nggak perlu klik manual terus
        setTimeout(() => window.location.reload(), 30000);
    </script>

</body>
</html>