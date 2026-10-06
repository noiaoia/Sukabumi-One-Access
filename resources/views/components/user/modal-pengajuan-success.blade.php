@if (session('success'))
<!-- Modal Overlay Success -->
<div id="modalPengajuanSuccess" 
     class="ps-overlay" 
     onclick="if(event.target === this) window.closePengajuanSuccessModal()">
    
    <div class="ps-card">
        <!-- Minimal Header -->
        <div class="ps-header">
            <div class="ps-icon-box"><i class="bi bi-patch-check-fill"></i></div>
            <h2>PENGAJUAN BERHASIL</h2>
        </div>

        <!-- Compact Body -->
        <div class="ps-body">
            <!-- Success Icon + Pulse Ring -->
            <div class="ps-check-wrapper">
                <span class="ps-pulse-ring"></span>
                <div class="ps-check-circle">
                    <i class="bi bi-check-lg"></i>
                </div>
            </div>

            <h3 class="ps-title">Berhasil!</h3>
            <p class="ps-text">{{ session('success', 'Pengajuan Anda telah berhasil dikirim.') }}</p>

            <!-- Compact Registration ID Box -->
            @if(session('nomor_pengajuan'))
            <div class="ps-ticket-box">
                <span class="ps-label"><i class="bi bi-qr-code-scan"></i> NOMOR PENGAJUAN</span>
                <div class="ps-id-row">
                    <span class="ps-id" id="pengajuanRegIdText">{{ session('nomor_pengajuan') }}</span>
                    <button type="button" class="ps-copy-btn" onclick="copyPengajuanRegId()" title="Salin nomor">
                        <i class="bi bi-clipboard" id="copyPengajuanIcon"></i>
                    </button>
                </div>
            </div>
            @endif
        </div>

        <!-- Action Button -->
        <div class="ps-footer">
            <button type="button" class="btn-ps-close" onclick="window.closePengajuanSuccessModal()">
                Selesai
            </button>
        </div>
    </div>
</div>
@endif

@once
<style>
    /* 1. LAYOUT & OVERLAY */
    .ps-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85);
        backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 10px; opacity: 0;
    }

    /* Munculkan modal (In) */
    .ps-overlay.is-active { 
        display: flex; 
        animation: psFade 0.3s ease forwards; 
    }
    .ps-overlay.is-active .ps-card { 
        animation: psSpring 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; 
    }

    /* Animasi Keluar (Out) */
    .ps-overlay.is-closing {
        animation: psFadeOut 0.3s ease forwards !important;
    }
    .ps-overlay.is-closing .ps-card {
        animation: psSpringOut 0.3s ease forwards !important;
    }

    /* 2. CARD & COMPONENTS */
    .ps-card { background: #fff; width: 100%; max-width: 300px; border-radius: 18px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
    .ps-header { background: #0A1E33; padding: 12px; border-bottom: 2px solid #E8A33D; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .ps-header h2 { color: #fff; font-size: 10px; font-weight: 800; margin: 0; letter-spacing: 1px; }
    .ps-icon-box { color: #E8A33D; font-size: 13px; display: flex; }
    .ps-body { padding: 20px 15px; text-align: center; }
    .ps-check-wrapper { position: relative; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; }
    .ps-pulse-ring { position: absolute; width: 45px; height: 45px; border-radius: 50%; border: 2px solid #E8A33D; animation: psPulseRing 1.8s ease-out infinite; z-index: 1; pointer-events: none; }
    .ps-check-circle { position: relative; z-index: 2; width: 45px; height: 45px; background: rgba(232, 163, 61, 0.1); border: 2px solid #E8A33D; color: #E8A33D; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; animation: psPop 0.5s ease; }
    .ps-title { font-size: 15px; font-weight: 800; color: #123457; margin-bottom: 4px; }
    .ps-text { font-size: 11px; color: #64748b; line-height: 1.4; margin-bottom: 15px; }
    .ps-ticket-box { background: #F8FAFC; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 8px; }
    .ps-label { display: block; font-size: 8px; font-weight: 800; color: #94a3b8; margin-bottom: 2px; }
    .ps-id-row { display: flex; align-items: center; justify-content: center; gap: 8px; }
    .ps-id { font-size: 12px; font-weight: 900; color: #123457; letter-spacing: 1px; }
    .ps-copy-btn { background: none; border: none; color: #94a3b8; cursor: pointer; display: flex; transition: 0.2s; }
    .ps-copy-btn:hover { color: #123457; }
    .ps-footer { padding: 12px; background: #F8FAFC; border-top: 1px solid #f1f5f9; }
    .btn-ps-close { width: 100%; background: #123457; color: #fff; border: none; padding: 10px; border-radius: 8px; font-size: 11px; font-weight: 800; cursor: pointer; transition: 0.2s; }
    .btn-ps-close:hover { background: #E8A33D; color: #0A1E33; }

    /* 3. ANIMATION KEYFRAMES */
    @keyframes psFade { from { opacity: 0; } to { opacity: 1; } }
    @keyframes psFadeOut { from { opacity: 1; } to { opacity: 0; } }
    
    @keyframes psSpring { 0% { opacity: 0; transform: scale(0.9) translateY(20px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
    @keyframes psSpringOut { from { opacity: 1; transform: scale(1) translateY(0); } to { opacity: 0; transform: scale(0.9) translateY(15px); } }
    
    @keyframes psPop { 0% { transform: scale(0); } 80% { transform: scale(1.2); } 100% { transform: scale(1); } }
    @keyframes psPulseRing { 0% { transform: scale(1); opacity: 0.6; } 100% { transform: scale(1.7); opacity: 0; } }
</style>

<script>
    (function() {
        /**
         * Membuka Modal Success
         */
        window.openPengajuanSuccessModal = function() {
            const modal = document.getElementById('modalPengajuanSuccess');
            if (modal) { 
                modal.classList.remove('is-closing');
                modal.classList.add('is-active'); 
                document.body.style.overflow = 'hidden'; 
            }
        };

        /**
         * Menutup Modal Success dengan Animasi
         */
        window.closePengajuanSuccessModal = function() {
            const modal = document.getElementById('modalPengajuanSuccess');
            if (modal && modal.classList.contains('is-active')) {
                modal.classList.add('is-closing');
                
                // Tunggu animasi CSS selesai (300ms)
                setTimeout(() => { 
                    modal.classList.remove('is-active', 'is-closing'); 
                    document.body.style.overflow = 'auto'; 
                }, 300);
            }
        };

        /**
         * Copy Functionality
         */
        window.copyPengajuanRegId = function() {
            const textEl = document.getElementById('pengajuanRegIdText');
            if (!textEl) return;
            
            const text = textEl.textContent.trim();
            navigator.clipboard.writeText(text).then(() => {
                const icon = document.getElementById('copyPengajuanIcon');
                if (icon) {
                    icon.classList.replace('bi-clipboard', 'bi-check-circle-fill');
                    setTimeout(() => icon.classList.replace('bi-check-circle-fill', 'bi-clipboard'), 2000);
                }
            });
        };

        /**
         * Auto-Trigger jika ada session success
         */
        document.addEventListener('DOMContentLoaded', () => { 
            if (document.getElementById('modalPengajuanSuccess')) {
                window.openPengajuanSuccessModal();
            }
        });

        // Close on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') window.closePengajuanSuccessModal();
        });
    })();
</script>
@endonce