<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Aktif - Sukabumi One Access</title>
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
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #040812; min-height: 100vh; overflow-x: hidden; color: white; }

        /* 1. BACKGROUND ELEMENT (Consistancy with 404 & Success) */
        canvas#stars { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
        .noise-overlay { position: fixed; inset: 0; opacity: 0.04; pointer-events: none; z-index: 1; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); }
        .bg-dot-matrix { position: fixed; inset: 0; opacity: 0.1; z-index: 1; background-image: radial-gradient(#E8A33D 1px, transparent 0); background-size: 40px 40px; }
        .aurora { position: fixed; border-radius: 9999px; filter: blur(120px); pointer-events: none; z-index: 1; transition: transform 0.6s cubic-bezier(0.16,1,0.3,1); }
        .aurora-blue { top: -10%; right: -5%; width: 600px; height: 600px; background: rgba(59,130,246,0.1); }
        .aurora-gold { bottom: -15%; left: -5%; width: 500px; height: 500px; background: rgba(232,163,61,0.08); }

        /* 2. THE WATCHER (EYE) - SUCCESS STATE */
        .eye-socket {
            position: relative;
            width: 130px;
            height: 130px;
            background: rgba(255, 255, 255, 0.02);
            border: 2px solid #E8A33D;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 3rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 50px rgba(232, 163, 61, 0.2);
            animation: float 6s ease-in-out infinite;
        }

        .eye-ball {
            position: relative;
            width: 65px;
            height: 65px;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.4);
        }

        .pupil {
            position: absolute;
            width: 32px;
            height: 32px;
            background: #0A1E33;
            border-radius: 50%;
            border: 4px solid #E8A33D;
            transition: transform 0.15s ease-out;
        }

        .pupil::after {
            content: '';
            position: absolute;
            top: 4px; left: 4px;
            width: 8px; height: 8px;
            background: white;
            border-radius: 50%;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-15px) scale(1.05); }
        }

        /* 3. BUTTONS */
        .btn-login-premium {
            background: #E8A33D;
            color: #0A1E33;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 15px 35px rgba(232, 163, 61, 0.3);
        }
        .btn-login-premium:hover {
            background: #ffffff;
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 20px 50px rgba(255, 255, 255, 0.2);
        }

        .scan-line {
            width: 150px;
            height: 1px;
            background: linear-gradient(90deg, transparent, #10b981, transparent);
            margin: 2.5rem auto;
            position: relative;
            overflow: hidden;
        }
        .scan-line::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: white;
            animation: scanning 2s infinite;
        }
        @keyframes scanning { to { left: 100%; } }

        .check-pop {
            animation: checkPop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s both;
        }
        @keyframes checkPop {
            0% { transform: scale(0) rotate(-30deg); opacity: 0; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }

        .sparkle {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #E8A33D;
            opacity: 0;
            pointer-events: none;
        }

        #sparkleContainer {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: visible;
        }

        .success-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            padding: 8px 20px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 2rem;
            border: 1px solid rgba(16, 185, 129, 0.2);
            animation: badgeGlow 2.4s ease-in-out infinite;
        }
        @keyframes badgeGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.15); }
            50% { box-shadow: 0 0 0 8px rgba(16,185,129,0); }
        }

        @media (max-width: 480px) {
            .eye-socket { width: 100px; height: 100px; margin-bottom: 2rem; }
            .eye-ball { width: 50px; height: 50px; }
            .pupil { width: 24px; height: 24px; border-width: 3px; }
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

        <!-- The Eye Tracker (Celebration Mode) -->
        <div class="eye-socket" id="eyeSocket">
            <div class="eye-ball">
                <div class="pupil" id="pupil"></div>
            </div>
            <!-- Checkmark Overlay -->
            <div class="check-pop absolute -bottom-2 -right-2 w-10 h-10 bg-[#10b981] rounded-full flex items-center justify-center border-4 border-[#040812] shadow-lg">
                <i class="bi bi-check-lg text-white font-bold text-xl"></i>
            </div>
            <div id="sparkleContainer"></div>
        </div>

        <div class="space-y-4">
            <div class="mb-6">
                <div class="success-badge">
                    <i class="bi bi-patch-check-fill"></i> Verification Complete
                </div>
                <h1 style="font-family: 'Space Grotesk'" class="text-4xl md:text-6xl font-extrabold leading-none tracking-tighter">
                    @if(Auth::check())
                        Selamat Datang, <br> <span class="text-accent">{{ explode(' ', Auth::user()->name)[0] }}!</span>
                    @else
                        Akun Anda <br> <span class="text-accent">Telah Aktif!</span>
                    @endif
                </h1>
            </div>

            <div class="scan-line"></div>

            <p class="text-slate-400 text-sm md:text-base max-w-xs mx-auto leading-relaxed mb-8">
                Identitas digital Anda telah berhasil diverifikasi dan disimpan ke dalam database pusat <strong class="text-white font-semibold">Sukabumi One Access</strong>.
            </p>

            <div class="flex flex-col items-center pt-6">
                {{-- Tombol Login Utama --}}
                <a href="{{ route('login') }}" class="btn-login-premium w-full max-w-xs py-5 rounded-2xl text-[11px] active:scale-95 flex items-center justify-center gap-3">
                    MASUK KE AKUN <i class="bi bi-box-arrow-in-right text-lg"></i>
                </a>

                <p class="mt-10 text-[10px] text-slate-600 font-bold uppercase tracking-widest">
                    Pemerintah Kota Sukabumi &bull; Integrated Service
                </p>
            </div>
        </div>
    </main>

    <script>
        // 1. STARFIELD CANVAS
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
                    ctx.fillStyle = `rgba(255, 255, 255, ${alpha * 0.3})`; ctx.fill();
                });
                requestAnimationFrame(draw);
            }
            window.addEventListener('resize', () => { resize(); initStars(); });
            resize(); initStars(); requestAnimationFrame(draw);
        })();

        // 2. THE EYE TRACKER LOGIC
        const pupil = document.getElementById('pupil');
        const eyeSocket = document.getElementById('eyeSocket');
        let hasMouseMoved = false;

        window.addEventListener('mousemove', (e) => {
            hasMouseMoved = true;
            const rect = eyeSocket.getBoundingClientRect();
            const eyeX = rect.left + rect.width / 2;
            const eyeY = rect.top + rect.height / 2;
            const angle = Math.atan2(e.clientY - eyeY, e.clientX - eyeX);
            const maxDistance = 15;
            const moveX = Math.cos(angle) * maxDistance;
            const moveY = Math.sin(angle) * maxDistance;
            pupil.style.transform = `translate(${moveX}px, ${moveY}px)`;
        });

        // 3. IDLE LOOK-AROUND: kalau perangkat tidak punya mouse (HP/tablet),
        // mata tetap "hidup" dengan melirik pelan ke beberapa arah secara berkala.
        (function () {
            const idleAngles = [-0.6, 0.4, -1.8, 2.3, 0.9, -0.2];
            let idleIndex = 0;

            setInterval(() => {
                if (hasMouseMoved) return; // berhenti kalau user sudah gerakin mouse
                const angle = idleAngles[idleIndex % idleAngles.length];
                idleIndex++;
                const maxDistance = 12;
                const moveX = Math.cos(angle) * maxDistance;
                const moveY = Math.sin(angle) * maxDistance;
                pupil.style.transform = `translate(${moveX}px, ${moveY}px)`;
            }, 2200);
        })();

        // 4. PARALLAX AURORA MENGIKUTI MOUSE
        (function () {
            const blue = document.querySelector('.aurora-blue');
            const gold = document.querySelector('.aurora-gold');

            window.addEventListener('mousemove', (e) => {
                const px = (e.clientX / window.innerWidth - 0.5);
                const py = (e.clientY / window.innerHeight - 0.5);
                blue.style.transform = `translate(${px * 35}px, ${py * 35}px)`;
                gold.style.transform = `translate(${px * -30}px, ${py * -30}px)`;
            }, { passive: true });
        })();

        // 5. LETUPAN SPARKLE SATU KALI SAAT HALAMAN DIMUAT (perayaan verifikasi berhasil)
        (function () {
            const container = document.getElementById('sparkleContainer');
            if (!container) return;

            const colors = ['#E8A33D', '#10b981', '#ffffff'];
            const count = 18;

            for (let i = 0; i < count; i++) {
                const el = document.createElement('span');
                el.className = 'sparkle';
                const angle = (Math.PI * 2 * i) / count + Math.random() * 0.3;
                const distance = 70 + Math.random() * 50;
                const dx = Math.cos(angle) * distance;
                const dy = Math.sin(angle) * distance;
                const size = 3 + Math.random() * 3;
                const delay = 0.5 + Math.random() * 0.3;
                const duration = 0.8 + Math.random() * 0.5;

                el.style.width = size + 'px';
                el.style.height = size + 'px';
                el.style.left = '50%';
                el.style.top = '50%';
                el.style.background = colors[Math.floor(Math.random() * colors.length)];

                container.appendChild(el);

                el.animate([
                    { transform: 'translate(-50%, -50%) scale(0)', opacity: 0 },
                    { transform: `translate(calc(-50% + ${dx * 0.3}px), calc(-50% + ${dy * 0.3}px)) scale(1)`, opacity: 1, offset: 0.3 },
                    { transform: `translate(calc(-50% + ${dx}px), calc(-50% + ${dy}px)) scale(0)`, opacity: 0 },
                ], {
                    duration: duration * 1000,
                    delay: delay * 1000,
                    easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
                    fill: 'forwards',
                });
            }

            // Bersihkan elemen sparkle setelah animasinya selesai semua
            setTimeout(() => { container.innerHTML = ''; }, 2200);
        })();
    </script>
</body>
</html>