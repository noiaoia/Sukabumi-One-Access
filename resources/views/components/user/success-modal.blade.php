<!-- Modal Overlay -->
<div id="successComplaintModal" 
     class="sm-overlay" 
     data-trigger="{{ session('success_message') ? 'true' : 'false' }}"
     onclick="if(event.target === this) closeSuccessModal()">
    
    <div class="sm-card">
        <!-- Minimal Header -->
        <div class="sm-header">
            <div class="sm-icon-box"><i class="bi bi-shield-check"></i></div>
            <h2>LAPORAN DITERIMA</h2>
        </div>

        <!-- Compact Body -->
        <div class="sm-body">
            <!-- Simple Success Icon + Pulse Ring -->
            <div class="sm-check-wrapper">
                <span class="sm-pulse-ring"></span>
                <div class="sm-check-circle">
                    <i class="bi bi-check-lg"></i>
                </div>
            </div>

            <h3 class="sm-title">Berhasil!</h3>
            <p class="sm-text">{{ session('success_message', 'Laporan Anda telah berhasil kami terima.') }}</p>

            <!-- Compact Ticket Box -->
            <div class="sm-ticket-box">
                <span class="sm-label"><i class="bi bi-ticket-perforated"></i> NOMOR TIKET</span>
                <div class="sm-id-row">
                    <span class="sm-id" id="ticketIdText">{{ session('ticket_id', 'ADU-000000-000') }}</span>
                    <button type="button" class="sm-copy-btn" onclick="copyTicketId()" title="Salin nomor tiket">
                        <i class="bi bi-clipboard" id="copyTicketIcon"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="sm-footer">
            <button type="button" class="btn-sm-close" onclick="closeSuccessModal()">
                Selesai
            </button>
        </div>
    </div>
</div>

@once
<style>
    /* 1. LAYOUT & CARD (Ultra Compact) */
    .sm-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85);
        backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center;
        z-index: 9999; padding: 10px; opacity: 0;
    }
    .sm-overlay.is-active { display: flex; animation: smFade 0.3s ease forwards; }
    .sm-overlay.is-active .sm-card { animation: smSpring 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    
    .sm-card {
        background: #fff; width: 100%; max-width: 300px; /* Ukuran Lebih Kecil */
        border-radius: 18px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }

    /* 2. HEADER */
    .sm-header { 
        background: #0A1E33; padding: 12px; border-bottom: 2px solid #E8A33D;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .sm-header h2 { color: #fff; font-size: 11px; font-weight: 800; margin: 0; letter-spacing: 1px; }
    .sm-icon-box { color: #E8A33D; font-size: 14px; display: flex; }

    /* 3. BODY */
    .sm-body { padding: 20px 15px; text-align: center; }
    
    /* Simple Icon + Pulse Ring (ring absolute, tidak menambah tinggi layout) */
    .sm-check-wrapper { position: relative; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; }
    .sm-pulse-ring {
        position: absolute;
        width: 45px; height: 45px;
        border-radius: 50%;
        border: 2px solid #E8A33D;
        animation: smPulseRing 1.8s ease-out infinite;
        z-index: 1;
        pointer-events: none;
    }
    .sm-check-circle {
        position: relative; z-index: 2;
        width: 45px; height: 45px; background: rgba(232, 163, 61, 0.1);
        border: 2px solid #E8A33D; color: #E8A33D; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-size: 24px;
        animation: smPop 0.5s ease;
    }

    .sm-title { font-size: 15px; font-weight: 800; color: #123457; margin-bottom: 4px; }
    .sm-text { font-size: 11px; color: #64748b; line-height: 1.4; margin-bottom: 15px; }

    /* 4. TICKET BOX */
    .sm-ticket-box { 
        background: #F8FAFC; border: 1px dashed #cbd5e1; 
        border-radius: 10px; padding: 8px; margin-top: 5px;
    }
    .sm-label { display: block; font-size: 8px; font-weight: 800; color: #94a3b8; margin-bottom: 2px; }
    .sm-label i { font-size: 8px; margin-right: 2px; }
    .sm-id-row { display: flex; align-items: center; justify-content: center; gap: 8px; }
    .sm-id { font-size: 12px; font-weight: 900; color: #123457; letter-spacing: 1px; }
    .sm-copy-btn {
        background: none; border: none; color: #94a3b8; cursor: pointer;
        padding: 2px; display: flex; align-items: center; transition: color 0.2s ease;
        flex-shrink: 0;
    }
    .sm-copy-btn:hover { color: #123457; }
    .sm-copy-btn.copied { color: #10B981; }

    /* 5. FOOTER & BUTTON */
    .sm-footer { padding: 12px; background: #F8FAFC; border-top: 1px solid #f1f5f9; }
    .btn-sm-close {
        width: 100%; background: #123457; color: #fff; border: none; padding: 10px;
        border-radius: 8px; font-size: 11px; font-weight: 800; cursor: pointer; transition: 0.2s;
    }
    .btn-sm-close:hover { background: #E8A33D; color: #0A1E33; }

    /* 6. ANIMATIONS */
    @keyframes smFade { from { opacity: 0; } to { opacity: 1; } }
    @keyframes smSpring { 
        0% { opacity: 0; transform: scale(0.9) translateY(20px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes smPop {
        0% { transform: scale(0); }
        80% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    @keyframes smPulseRing {
        0% { transform: scale(1); opacity: 0.6; }
        100% { transform: scale(1.7); opacity: 0; }
    }

    .sm-overlay.is-closing { animation: smFadeOut 0.3s ease forwards; }
    @keyframes smFadeOut { from { opacity: 1; } to { opacity: 0; } }
</style>

<script>
    // Gunakan fungsi ekspresi untuk menghindari konflik linter
    window.closeSuccessModal = function() {
        const modal = document.getElementById('successComplaintModal');
        if (modal) {
            modal.classList.add('is-closing');
            setTimeout(function() {
                modal.classList.remove('is-active');
                modal.classList.remove('is-closing');
                document.body.style.overflow = 'auto';
            }, 300);
        }
    };

    // Salin nomor tiket ke clipboard + feedback ikon centang sebentar
    window.copyTicketId = function() {
        const text = document.getElementById('ticketIdText').textContent.trim();
        if (!navigator.clipboard) return;

        navigator.clipboard.writeText(text).then(function() {
            const btn = document.querySelector('.sm-copy-btn');
            const icon = document.getElementById('copyTicketIcon');
            icon.classList.remove('bi-clipboard');
            icon.classList.add('bi-clipboard-check-fill');
            btn.classList.add('copied');

            setTimeout(function() {
                icon.classList.remove('bi-clipboard-check-fill');
                icon.classList.add('bi-clipboard');
                btn.classList.remove('copied');
            }, 1500);
        });
    };

    // Deteksi trigger dari atribut data untuk menghindari error linter VS Code
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('successComplaintModal');
        if (modal && modal.getAttribute('data-trigger') === 'true') {
            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }
    });

    // Esc untuk tutup, konsisten dengan modal lain di project ini
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('successComplaintModal');
        if (e.key === 'Escape' && modal && modal.classList.contains('is-active')) {
            closeSuccessModal();
        }
    });
</script>
@endonce