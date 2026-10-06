@once
<style>
    /* ANIMASI (Identik dengan modal sebelumnya) */
    @keyframes typeOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes typeSpringIn {
        0% { opacity: 0; transform: scale(0.9) translateY(30px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }

    @keyframes typeOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes typeSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.96) translateY(10px); }
    }

    .type-modal-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.8);
        backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 15px; opacity: 0;
    }
    .type-modal-overlay.is-active {
        display: flex;
        animation: typeOverlayIn .3s ease forwards;
    }

    .type-modal-overlay.is-active .type-modal-card {
        animation: typeSpringIn .45s cubic-bezier(.34,1.56,.64,1) forwards;
    }

    .type-modal-overlay.is-closing {
        animation: typeOverlayOut .3s ease forwards;
    }

    .type-modal-overlay.is-closing .type-modal-card {
        animation: typeSpringOut .3s ease forwards;
    }

    .type-modal-card {
        background: #ffffff; width: 100%; max-width: 480px;
        border-radius: 28px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    .type-modal-header {
        background: #0A1E33; padding: 25px; border-bottom: 4px solid #E8A33D; text-align: center;
        position: relative;
    }
    .type-modal-header h2 { color: #ffffff; font-size: 16px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
    .type-modal-header p { color: rgba(255,255,255,0.5); font-size: 11px; margin-top: 5px; font-weight: 600; }

    .type-modal-close {
        position: absolute; top: 14px; right: 14px;
        width: 28px; height: 28px; border-radius: 9px;
        background: rgba(255,255,255,0.08); border: none; color: #94a3b8;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s;
    }
    .type-modal-close:hover { background: rgba(239,68,68,0.15); color: #f87171; }

    /* Badge nama instansi — TERPISAH dari teks instruksi, jadi instruksinya gak pernah hilang */
    .type-modal-context {
        display: inline-flex; align-items: center; gap: 6px;
        margin-top: 12px; padding: 5px 12px;
        background: rgba(232, 163, 61, 0.12); border: 1px solid rgba(232, 163, 61, 0.25);
        border-radius: 999px; color: #E8A33D; font-size: 10px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.5px;
    }

    .type-modal-body { padding: 30px; }

    /* PILIHAN CARD */
    .choice-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

    .choice-card {
        position: relative;
        background: #F8FAFC; border: 2px solid #E2E8F0; border-radius: 20px;
        padding: 20px 15px; text-align: center; cursor: pointer;
        transition: all 0.3s ease; text-decoration: none !important;
        display: flex; flex-direction: column; align-items: center; gap: 12px;
    }

    .choice-card:hover {
        border-color: #E8A33D; background: #fff;
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(232, 163, 61, 0.1);
    }

    .choice-card:focus-visible {
        outline: none; border-color: #E8A33D;
        box-shadow: 0 0 0 4px rgba(232, 163, 61, 0.15);
    }

    .choice-icon {
        width: 50px; height: 50px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; transition: all 0.3s;
    }

    /* Iframe Style */
    .choice-card.iframe-type .choice-icon { background: rgba(18, 52, 87, 0.1); color: #123457; }
    .choice-card.iframe-type:hover .choice-icon { background: #123457; color: #E8A33D; }

    /* Form Style */
    .choice-card.form-type .choice-icon { background: rgba(16, 185, 129, 0.1); color: #10B981; }
    .choice-card.form-type:hover .choice-icon { background: #10B981; color: #fff; }

    .choice-title { font-size: 13px; font-weight: 800; color: #123457; margin: 0; }
    .choice-desc { font-size: 9px; color: #94a3b8; font-weight: 600; line-height: 1.4; margin: 0; }

    /* Panah kecil, muncul saat hover — penanda bahwa kartu ini bisa diklik */
    .choice-arrow {
        position: absolute; bottom: 12px; right: 14px;
        color: #cbd5e1; font-size: 12px; opacity: 0;
        transform: translateX(-4px); transition: all 0.25s ease;
    }
    .choice-card:hover .choice-arrow { opacity: 1; transform: translateX(0); }
    .choice-card.iframe-type:hover .choice-arrow { color: #123457; }
    .choice-card.form-type:hover .choice-arrow { color: #10B981; }

    .type-modal-footer {
        padding: 15px; background: #F8FAFC; border-top: 1px solid #E2E8F0; text-align: center;
    }
    .btn-close-type {
        background: transparent; border: none; color: #94a3b8;
        font-size: 11px; font-weight: 800; text-transform: uppercase;
        cursor: pointer; letter-spacing: 1px;
    }
    .btn-close-type:hover { color: #475569; }
</style>
@endonce

<div id="serviceTypeModal" class="type-modal-overlay" onclick="if(event.target === this) closeServiceTypeModal()">
    <div class="type-modal-card">
        <div class="type-modal-header">
            <button type="button" class="type-modal-close" onclick="closeServiceTypeModal()" aria-label="Tutup">
                <i class="bi bi-x-lg text-xs"></i>
            </button>
            <h2>Metode Integrasi</h2>
            <p>Pilih cara layanan ini akan diakses oleh warga</p>
            <span class="type-modal-context">
                <i class="bi bi-building"></i> <span id="target_instansi_name">-</span>
            </span>
        </div>

        <div class="type-modal-body">
            <div class="choice-grid">
                <!-- OPSI 1: IFRAME -->
                <a href="#" id="link_iframe" class="choice-card iframe-type">
                    <div class="choice-icon">
                        <i class="bi bi-window-fullscreen"></i>
                    </div>
                    <div>
                        <h4 class="choice-title">Portal Iframe</h4>
                        <p class="choice-desc">Hubungkan link aplikasi eksternal yang sudah aktif.</p>
                    </div>
                    <i class="bi bi-arrow-right choice-arrow"></i>
                </a>

                <!-- OPSI 2: FORMULIR -->
                <a href="#" id="link_form" class="choice-card form-type">
                    <div class="choice-icon">
                        <i class="bi bi-ui-checks-grid"></i>
                    </div>
                    <div>
                        <h4 class="choice-title">Formulir Mandiri</h4>
                        <p class="choice-desc">Buat sistem input data & pengajuan berkas baru.</p>
                    </div>
                    <i class="bi bi-arrow-right choice-arrow"></i>
                </a>
            </div>
        </div>

        <div class="type-modal-footer">
            <button class="btn-close-type" onclick="closeServiceTypeModal()">Batalkan</button>
        </div>
    </div>
</div>

@once
<script>
    function openServiceTypeModal(slug, name, portalCode) {
        const modal = document.getElementById('serviceTypeModal');
        document.getElementById('target_instansi_name').innerText = name || 'Instansi ini';

        /**
         * 1. LINK IFRAME (Kelompok Iframe)
         * Menuju ke: /{portal_code}/layanan/iframe/{instansi_slug}/manage
         * Sesuai Route: admin.layanan.iframe.manage
         */
        document.getElementById('link_iframe').href = `/${portalCode}/layanan/iframe/${slug}/manage`;
        
        /**
         * 2. LINK FORM (Kelompok Formulir Mandiri)
         * Menuju ke: /{portal_code}/layanan/form/{instansi_slug}/manage
         * Sesuai Route: admin.layanan.form.manage
         */
        document.getElementById('link_form').href = `/${portalCode}/layanan/form/${slug}/manage`;

        // Tampilkan Modal
        modal.classList.remove('is-closing');
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function closeServiceTypeModal() {
        const modal = document.getElementById('serviceTypeModal');

        modal.classList.add('is-closing');

        setTimeout(() => {
            modal.classList.remove('is-active');
            modal.classList.remove('is-closing');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    // Esc untuk tutup, konsisten dengan modal lain di project ini
    document.addEventListener('keydown', function (e) {
        const modal = document.getElementById('serviceTypeModal');
        if (e.key === 'Escape' && modal && modal.classList.contains('is-active')) {
            closeServiceTypeModal();
        }
    });
</script>
@endonce