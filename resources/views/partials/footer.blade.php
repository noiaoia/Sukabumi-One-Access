{{-- resources/views/partials/footer.blade.php --}}

<style>
    /* 
    FOOTER STYLES 
    */
    :root {
        --footer-bg-start: #123457;
        --footer-bg-end: #061626;
        --footer-accent: #E8A33D;
        --footer-text-muted: #94a3b8;
        --footer-text-light: #cbd5e1;
    }

    .footer-premium {
        background: linear-gradient(135deg, var(--footer-bg-start) 0%, var(--footer-bg-end) 100%);
        color: var(--footer-text-light);
        padding: 5rem 1.5rem 2rem 1.5rem;
        position: relative;
        overflow: hidden;
        border-top: 4px solid #E8A33D;
    }

    /* Efek Glow Dekoratif */
    .footer-premium::before {
        content: "";
        position: absolute;
        top: -10%; left: -10%; width: 40%; height: 40%;
        background: radial-gradient(circle, rgba(232, 163, 61, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Tipografi & Komponen */
    .footer-brand-area {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .footer-brand-title {
        color: #ffffff;
        font-weight: 800;
        font-size: 1.25rem;
        letter-spacing: -0.02em;
        margin-left: 0.75rem;
    }

    .footer-label {
        color: var(--footer-accent);
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        margin-bottom: 1.5rem;
        display: block;
    }

    .footer-nav-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.875rem;
    }

    .footer-link {
        font-size: 0.875rem;
        color: var(--footer-text-muted);
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .footer-link:hover {
        color: var(--footer-accent);
        padding-left: 6px;
    }

    /* Social Buttons Area */
    .social-container {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    .social-button {
        width: 38px;
        height: 38px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--footer-text-light);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .social-button:hover {
        background: var(--footer-accent);
        color: var(--footer-bg-start);
        transform: translateY(-4px);
        box-shadow: 0 8px 20px -6px rgba(232, 163, 61, 0.5);
    }

    /* Contact & Operational */
    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 1.25rem;
        font-size: 0.875rem;
        line-height: 1.6;
    }

    .contact-icon {
        color: var(--footer-accent);
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .op-hours-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 1.25rem;
        padding: 1.25rem;
        margin-top: 0.5rem;
    }

    .op-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .op-row:last-child { margin-bottom: 0; }

    /* Bottom Bar */
    .copyright-bar {
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        margin-top: 4rem;
        padding-top: 2rem;
        text-align: center;
        font-size: 0.75rem;
        color: #64748b;
    }

    /* 
    RESPONSIVE (Mobile Optimization)
    */
    @media (max-width: 768px) {
        .footer-premium {
            padding-top: 4rem;
            text-align: center; /* Rata tengah di mobile agar lebih rapi */
        }
        
        .footer-brand-area {
            align-items: center;
        }

        .footer-link {
            justify-content: center;
        }

        .social-container {
            justify-content: center;
        }

        .contact-item {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .footer-link:hover {
            padding-left: 0; /* Matikan efek geser di mobile */
        }
    }
</style>

<footer id="tentang-kami" class="footer-premium">
    <div class="max-w-7xl mx-auto">
        
        <!-- Main Grid Container -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8"> 
            <!-- Kolom 1: Profil -->
            <div class="footer-brand-area">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shadow-lg">
                        <i class="bi bi-bank text-accent text-xl"></i>
                    </div>
                    <span class="footer-brand-title">Sukabumi One Access</span>
                </div>
                <p class="text-sm leading-relaxed text-slate-400">
                    Portal integrasi layanan publik digital Pemerintah Kota Sukabumi. Satu akses untuk birokrasi yang lebih cerdas dan transparan.
                </p>
                <div class="social-container">
                    <a href="#" class="social-button" title="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-button" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-button" title="Youtube"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="social-button" title="Twitter"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>

            <!-- Kolom 2: Navigasi -->
            <div>
                <span class="footer-label">Navigasi</span>
                <ul class="footer-nav-list">
                    <li><a href="{{ route('home') }}" class="footer-link">Beranda Utama</a></li>
                    <li><a href="{{ route('layanan') }}" class="footer-link">Sektor Kedinasan</a></li>
                    <li><a href="{{ route('pengaduan') ?? '#' }}" class="footer-link">Pengaduan Warga</a></li>
                    <li><a href="#" class="footer-link">Lacak Dokumen</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Kontak -->
            <div>
                <span class="footer-label">Hubungi Kami</span>
                <div class="contact-item">
                    <i class="bi bi-geo-alt contact-icon"></i>
                    <p>Jl. Syamsudin SH No.25, Cikole, Kota Sukabumi, Jawa Barat 43111</p>
                </div>
                <div class="contact-item">
                    <i class="bi bi-envelope contact-icon"></i>
                    <p>diskominfo@sukabumikota.go.id</p>
                </div>
                <div class="contact-item">
                    <i class="bi bi-telephone contact-icon"></i>
                    <p>(0266) 221123</p>
                </div>
            </div>

            <!-- Kolom 4: Jam Operasional -->
            <div>
                <span class="footer-label">Jam Pelayanan</span>
                <div class="op-hours-card">
                    <div class="op-row">
                        <span class="text-slate-400">Senin - Kamis</span>
                        <span class="text-white font-bold">08:00 - 15:30</span>
                    </div>
                    <div class="op-row">
                        <span class="text-slate-400">Jumat</span>
                        <span class="text-white font-bold">08:00 - 16:00</span>
                    </div>
                    <div class="op-row" style="opacity: 0.4;">
                        <span class="text-slate-500">Sabtu - Minggu</span>
                        <span class="text-slate-500 italic">Tutup</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Copyright Bar -->
        <div class="copyright-bar">
            <p>&copy; {{ date('Y') }} Sukabumi One Access. <br class="sm:hidden"> 
            Dinas Komunikasi dan Informatika Kota Sukabumi. <br> 
            <span class="text-[10px] uppercase tracking-wider mt-2 block opacity-40">Handcrafted for better public services</span></p>
        </div>
    </div>
</footer>