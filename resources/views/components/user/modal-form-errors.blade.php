{{-- resources/views/components/user/modal-form-errors.blade.php --}}

@if ($errors->any())
<!-- Modal Overlay -->
<div id="modalFormError" 
     class="em-overlay" 
     onclick="if(event.target === this) window.closeFormErrorModal()">
    
    <div class="em-card">
        <!-- Minimal Header -->
        <div class="em-header">
            <div class="em-icon-box"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <h2>VALIDASI GAGAL</h2>
        </div>

        <!-- Compact Body -->
        <div class="em-body">
            <!-- Error Icon + Pulse Ring -->
            <div class="em-check-wrapper">
                <span class="em-pulse-ring"></span>
                <div class="em-check-circle">
                    <i class="bi bi-x-lg"></i>
                </div>
            </div>

            <h3 class="em-title">Periksa Isian</h3>
            <p class="em-text">Ada {{ $errors->count() }} data yang belum sesuai. Mohon perbaiki formulir Anda.</p>

            <!-- Compact Error List Box -->
            <div class="em-list-box">
                <ul class="em-list">
                    @foreach ($errors->all() as $message)
                        <li><i class="bi bi-dot"></i> {{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Action Button -->
        <div class="em-footer">
            <button type="button" class="btn-em-close" onclick="window.closeFormErrorModal()">
                Perbaiki Sekarang
            </button>
        </div>
    </div>
</div>
@endif

@once
<style>
    /* 1. LAYOUT & CARD */
    .em-overlay {
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85);
        backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 10px; opacity: 0;
    }
    .em-overlay.is-active { display: flex; animation: emFade 0.3s ease forwards; }
    .em-overlay.is-active .em-card { animation: emSpring 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    
    .em-card {
        background: #fff; width: 100%; max-width: 300px;
        border-radius: 18px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }

    /* 2. HEADER */
    .em-header { 
        background: #0A1E33; padding: 12px; border-bottom: 2px solid #ef4444;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .em-header h2 { color: #fff; font-size: 10px; font-weight: 800; margin: 0; letter-spacing: 1px; }
    .em-icon-box { color: #ef4444; font-size: 13px; display: flex; }

    /* 3. BODY */
    .em-body { padding: 20px 15px; text-align: center; }
    
    .em-check-wrapper { position: relative; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; }
    .em-pulse-ring {
        position: absolute; width: 45px; height: 45px; border-radius: 50%;
        border: 2px solid #ef4444; animation: emPulseRing 1.8s ease-out infinite;
        z-index: 1; pointer-events: none;
    }
    .em-check-circle {
        position: relative; z-index: 2; width: 45px; height: 45px; 
        background: rgba(239, 68, 68, 0.1); border: 2px solid #ef4444; color: #ef4444; 
        border-radius: 50%; display: flex; align-items: center; justify-content: center; 
        font-size: 18px; animation: emPop 0.5s ease;
    }

    .em-title { font-size: 15px; font-weight: 800; color: #123457; margin-bottom: 4px; }
    .em-text { font-size: 10.5px; color: #64748b; line-height: 1.4; margin-bottom: 15px; }

    /* 4. ERROR LIST BOX */
    .em-list-box { 
        background: #FFF1F2; border: 1px dashed #FECACA; 
        border-radius: 12px; padding: 10px; margin-top: 5px;
        max-height: 120px; overflow-y: auto; text-align: left;
    }
    .em-list { list-style: none; padding: 0; margin: 0; }
    .em-list li { 
        font-size: 9.5px; font-weight: 700; color: #b91c1c; 
        line-height: 1.4; display: flex; gap: 4px; align-items: flex-start;
        margin-bottom: 6px;
    }
    .em-list li i { font-size: 14px; color: #ef4444; line-height: 1; }

    .em-list-box::-webkit-scrollbar { width: 4px; }
    .em-list-box::-webkit-scrollbar-thumb { background: #FECACA; border-radius: 10px; }

    /* 5. FOOTER & BUTTON */
    .em-footer { padding: 12px; background: #F8FAFC; border-top: 1px solid #f1f5f9; }
    .btn-em-close {
        width: 100%; background: #ef4444; color: #fff; border: none; padding: 10px;
        border-radius: 8px; font-size: 11px; font-weight: 800; cursor: pointer; transition: 0.2s;
    }
    .btn-em-close:hover { background: #b91c1c; }

    /* 6. ANIMATIONS - MENGGUNAKAN @@ UNTUK MENGHINDARI BLADE ERROR */
    @keyframes emFade { from { opacity: 0; } to { opacity: 1; } }
    @keyframes emSpring { 
        0% { opacity: 0; transform: scale(0.9) translateY(20px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes emPop {
        0% { transform: scale(0); }
        80% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    @keyframes emPulseRing {
        0% { transform: scale(1); opacity: 0.6; }
        100% { transform: scale(1.7); opacity: 0; }
    }

    .em-overlay.is-closing { animation: emFadeOut 0.3s ease forwards; }
    @keyframes emFadeOut { from { opacity: 1; } to { opacity: 0; } }
</style>

<script>
    (function() {
        window.openFormErrorModal = function() {
            const modal = document.getElementById('modalFormError');
            if (modal) {
                modal.classList.add('is-active');
                document.body.style.overflow = 'hidden';
            }
        };

        window.closeFormErrorModal = function() {
            const modal = document.getElementById('modalFormError');
            if (modal) {
                modal.classList.add('is-closing');
                setTimeout(function() {
                    modal.classList.remove('is-active');
                    modal.classList.remove('is-closing');
                    document.body.style.overflow = 'auto';
                }, 300);
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('modalFormError')) {
                window.openFormErrorModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') window.closeFormErrorModal();
        });
    })();
</script>
@endonce