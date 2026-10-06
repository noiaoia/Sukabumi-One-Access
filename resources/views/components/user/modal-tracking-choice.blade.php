@once
<style>
    /* --- ANIMASI (Sesuai Referensi) --- */
    @keyframes trackOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes trackSpringIn {
        0% { opacity: 0; transform: scale(0.9) translateY(30px); }
        70% { transform: scale(1.02) translateY(-5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }

    @keyframes trackOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes trackSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.96) translateY(10px); }
    }

    .track-modal-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.8);
        backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 15px; opacity: 0;
    }
    .track-modal-overlay.is-active {
        display: flex;
        animation: trackOverlayIn .3s ease forwards;
    }

    .track-modal-overlay.is-active .track-modal-card {
        animation: trackSpringIn .45s cubic-bezier(.34,1.56,.64,1) forwards;
    }

    .track-modal-overlay.is-closing {
        animation: trackOverlayOut .3s ease forwards;
    }

    .track-modal-overlay.is-closing .track-modal-card {
        animation: trackSpringOut .3s ease forwards;
    }

    .track-modal-card {
        background: #ffffff; width: 100%; max-width: 480px;
        border-radius: 28px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    .track-modal-header {
        background: #0A1E33; padding: 25px; border-bottom: 4px solid #E8A33D; text-align: center;
        position: relative;
    }
    .track-modal-header h2 { color: #ffffff; font-size: 16px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
    .track-modal-header p { color: rgba(255,255,255,0.5); font-size: 11px; margin-top: 5px; font-weight: 600; }

    .track-modal-close {
        position: absolute; top: 14px; right: 14px;
        width: 28px; height: 28px; border-radius: 9px;
        background: rgba(255,255,255,0.08); border: none; color: #94a3b8;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s;
    }
    .track-modal-close:hover { background: rgba(239,68,68,0.15); color: #f87171; }

    .track-modal-context {
        display: inline-flex; align-items: center; gap: 6px;
        margin-top: 12px; padding: 5px 12px;
        background: rgba(232, 163, 61, 0.12); border: 1px solid rgba(232, 163, 61, 0.25);
        border-radius: 999px; color: #E8A33D; font-size: 10px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.5px;
    }

    .track-modal-body { padding: 30px; }

    /* PILIHAN CARD */
    .track-choice-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

    .choice-card-track {
        position: relative;
        background: #F8FAFC; border: 2px solid #E2E8F0; border-radius: 20px;
        padding: 20px 15px; text-align: center; cursor: pointer;
        transition: all 0.3s ease; text-decoration: none !important;
        display: flex; flex-direction: column; align-items: center; gap: 12px;
    }

    .choice-card-track:hover {
        border-color: #E8A33D; background: #fff;
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(232, 163, 61, 0.1);
    }

    .choice-icon-track {
        width: 50px; height: 50px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; transition: all 0.3s;
    }

    /* Aduan Style (Navy) */
    .choice-card-track.aduan-type .choice-icon-track { background: rgba(18, 52, 87, 0.1); color: #123457; }
    .choice-card-track.aduan-type:hover .choice-icon-track { background: #123457; color: #E8A33D; }

    /* Pengajuan Style (Gold) */
    .choice-card-track.pengajuan-type .choice-icon-track { background: rgba(232, 163, 61, 0.1); color: #E8A33D; }
    .choice-card-track.pengajuan-type:hover .choice-icon-track { background: #E8A33D; color: #0A1E33; }

    .choice-title-track { font-size: 13px; font-weight: 800; color: #123457; margin: 0; }
    .choice-desc-track { font-size: 9px; color: #94a3b8; font-weight: 600; line-height: 1.4; margin: 0; }

    .choice-arrow-track {
        position: absolute; bottom: 12px; right: 14px;
        color: #cbd5e1; font-size: 12px; opacity: 0;
        transform: translateX(-4px); transition: all 0.25s ease;
    }
    .choice-card-track:hover .choice-arrow-track { opacity: 1; transform: translateX(0); }
    .choice-card-track.aduan-type:hover .choice-arrow-track { color: #123457; }
    .choice-card-track.pengajuan-type:hover .choice-arrow-track { color: #E8A33D; }

    .track-modal-footer {
        padding: 15px; background: #F8FAFC; border-top: 1px solid #E2E8F0; text-align: center;
    }
    .btn-close-track {
        background: transparent; border: none; color: #94a3b8;
        font-size: 11px; font-weight: 800; text-transform: uppercase;
        cursor: pointer; letter-spacing: 1px;
    }
    .btn-close-track:hover { color: #475569; }
</style>
@endonce

<div id="trackingChoiceModal" class="track-modal-overlay" onclick="if(event.target === this) closeTrackingChoiceModal()">
    <div class="track-modal-card">
        <div class="track-modal-header">
            <button type="button" class="track-modal-close" onclick="closeTrackingChoiceModal()" aria-label="Tutup">
                <i class="bi bi-x-lg text-xs"></i>
            </button>
            <h2>Pusat Pelacakan</h2>
            <p>Tinjau status data yang sudah Anda kirimkan</p>
            <span class="track-modal-context">
                <i class="bi bi-patch-check"></i> <span>Status Real-time</span>
            </span>
        </div>

        <div class="track-modal-body">
            <div class="track-choice-grid">
                <!-- OPSI 1: PENGADUAN -->
                <a href="{{ route('user.riwayat.pengaduan') }}?tab=pengaduan" class="choice-card-track aduan-type">
                    <div class="choice-icon-track">
                        <i class="bi bi-megaphone-fill"></i>
                    </div>
                    <div>
                        <h4 class="choice-title-track">Laporan Aduan</h4>
                        <p class="choice-desc-track">Pantau laporan keluhan infrastruktur & sosial.</p>
                    </div>
                    <i class="bi bi-arrow-right choice-arrow-track"></i>
                </a>

                <!-- OPSI 2: PENGAJUAN -->
                <a href="{{ route('user.riwayat.pengajuan') }}" class="choice-card-track pengajuan-type">
                    <div class="choice-icon-track">
                        <i class="bi bi-file-earmark-check-fill"></i>
                    </div>
                    <div>
                        <h4 class="choice-title-track">Berkas Layanan</h4>
                        <p class="choice-desc-track">Cek progress pengajuan dokumen mandiri.</p>
                    </div>
                    <i class="bi bi-arrow-right choice-arrow-track"></i>
                </a>
            </div>
        </div>

        <div class="track-modal-footer">
            <button class="btn-close-track" onclick="closeTrackingChoiceModal()">Batalkan</button>
        </div>
    </div>
</div>

@once
<script>
    function openTrackingChoiceModal() {
        const modal = document.getElementById('trackingChoiceModal');
        modal.classList.remove('is-closing');
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function closeTrackingChoiceModal() {
        const modal = document.getElementById('trackingChoiceModal');
        modal.classList.add('is-closing');

        setTimeout(() => {
            modal.classList.remove('is-active');
            modal.classList.remove('is-closing');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    document.addEventListener('keydown', function (e) {
        const modal = document.getElementById('trackingChoiceModal');
        if (e.key === 'Escape' && modal && modal.classList.contains('is-active')) {
            closeTrackingChoiceModal();
        }
    });
</script>
@endonce