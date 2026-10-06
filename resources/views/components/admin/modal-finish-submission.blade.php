{{-- resources/views/components/admin/modal-finish-submission.blade.php --}}
@props(['portalCode'])

@once
<style>
    /* 1. ANIMATION KEYFRAMES */
    @keyframes finOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes finOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes finSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.01) translateY(-4px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes finSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.96) translateY(10px); }
    }

    /* 2. OVERLAY */
    .fin-overlay { 
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85); 
        backdrop-filter: blur(6px); 
        display: none; /* Hidden by default */
        align-items: center; justify-content: center; 
        z-index: 12000; padding: 20px; opacity: 0; 
    }

    /* State: Masuk */
    .fin-overlay.is-active { 
        display: flex; 
        opacity: 1; 
        animation: finOverlayIn 0.3s ease forwards; 
    }
    .fin-overlay.is-active .fin-card { 
        animation: finSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; 
    }

    /* State: Keluar */
    .fin-overlay.is-closing { 
        animation: finOverlayOut 0.3s ease forwards; 
    }
    .fin-overlay.is-closing .fin-card { 
        animation: finSpringOut 0.3s ease forwards; 
    }
    
    /* 3. CARD DESIGN */
    .fin-card { 
        background: #fff; width: 100%; max-width: 380px; max-height: 80vh; border-radius: 24px; 
        overflow: hidden; box-shadow: 0 30px 60px -12px rgba(0,0,0,0.4); 
        display: flex; flex-direction: column;
    }

    /* 4. HEADER & UI ELEMENTS (Sesuai gaya Oren/Gold Anda) */
    .fin-header { 
        background: #0A1E33; padding: 20px 24px; border-bottom: 4px solid #E8A33D; 
        display: flex; align-items: center; justify-content: space-between; gap: 12px; color: #fff; position: relative;
    }
    .fin-header-left { display: flex; align-items: center; gap: 16px; min-width: 0; }
    .fin-header h2 { font-size: 15px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .fin-header p { font-size: 10px; color: rgba(255,255,255,0.5); margin: 2px 0 0; font-family: 'JetBrains Mono', monospace; }
    
    .fin-icon-box { 
        width: 42px; height: 42px; border-radius: 12px; display: flex; 
        align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;
        background: rgba(232, 163, 61, 0.15); color: #E8A33D;
    }
    .fin-close-x { 
        width: 30px; height: 30px; border-radius: 9px;
        background: rgba(255,255,255,0.06); border: none; color: #94a3b8;
        display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; 
    }
    .fin-close-x:hover { background: rgba(232,163,61,0.2); color: #E8A33D; }

    .fin-body-scroll { overflow-y: auto; flex: 1; max-height: 70vh; }
    .fin-section { padding: 20px 24px; }
    .fin-section + .fin-section { border-top: 1px solid #F1F5F9; }

    .fin-section-heading { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .fin-section-number {
        width: 22px; height: 22px; border-radius: 7px;
        background: #123457; color: #E8A33D;
        font-size: 10px; font-weight: 900;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .fin-section-heading h3 { font-size: 12px; font-weight: 800; color: #123457; margin: 0; text-transform: uppercase; }

    .fin-warning-box { 
        background: #FFFBEB; border: 1px solid #FEF3C7; padding: 12px 16px; 
        border-radius: 14px; display: flex; gap: 12px;
    }
    .fin-warning-box i { color: #D97706; font-size: 1.1rem; flex-shrink: 0; }
    .fin-warning-box p { font-size: 11px; color: #92400E; margin: 0; line-height: 1.5; font-weight: 600; }

    .fin-label { 
        display: block; font-size: 9px; font-weight: 800; color: #64748b; 
        text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; margin-left: 4px;
    }
    #catatan_selesai { 
        width: 100%; padding: 14px; border: 1.5px solid #E2E8F0; border-radius: 16px; 
        font-size: 13px; font-weight: 600; color: #123457; outline: none; 
        transition: all 0.3s; font-family: inherit; background: #F8FAFC; resize: none;
    }
    #catatan_selesai:focus { border-color: #E8A33D; background: #fff; box-shadow: 0 0 0 4px rgba(232, 163, 61, 0.08); }

    .fin-footer { padding: 16px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; gap: 10px; }
    .btn-fin-cancel { flex: 1; padding: 12px; border-radius: 12px; border: 1px solid #E2E8F0; background: #fff; color: #475569; font-weight: 800; font-size: 11px; cursor: pointer; text-transform: uppercase; }
    .btn-fin-cancel:hover { background: #F1F5F9; color: #1e293b; }
    .btn-fin-submit { 
        flex: 2; padding: 12px; border-radius: 12px; border: none; 
        background: #123457; color: #fff; font-weight: 800; font-size: 11px; 
        cursor: pointer; transition: 0.3s; text-transform: uppercase;
    }
    .btn-fin-submit:hover { background: #E8A33D; color: #0A1E33; box-shadow: 0 8px 16px rgba(232, 163, 61, 0.25); }
</style>
@endonce

<div id="modalFinishSub" class="fin-overlay" onclick="if(event.target === this) window.closeFinishModal()">
    <div class="fin-card">
        <div class="fin-header">
            <div class="fin-header-left">
                <div class="fin-icon-box"><i class="bi bi-patch-check-fill"></i></div>
                <div class="min-w-0">
                    <h2>Selesaikan Berkas</h2>
                    <p id="fin-nomor-text">#SOA-2024...</p>
                </div>
            </div>
            <button type="button" class="fin-close-x" onclick="window.closeFinishModal()"><i class="bi bi-x-lg text-xs"></i></button>
        </div>

        <form id="formFinish" method="POST" class="fin-body-scroll">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="selesai">
            
            <div class="fin-section">
                <div class="fin-section-heading">
                    <span class="fin-section-number">1</span>
                    <h3>Penyelesaian Layanan</h3>
                </div>
                <div class="fin-warning-box">
                    <i class="bi bi-info-circle-fill"></i>
                    <p>Konfirmasi ini menyatakan berkas telah <strong>Selesai</strong>. Warga akan menerima notifikasi hasil.</p>
                </div>
            </div>

            <div class="fin-section">
                <div class="fin-section-heading">
                    <span class="fin-section-number">2</span>
                    <h3>Pesan Untuk Warga</h3>
                </div>
                <div class="fin-field">
                    <label for="catatan_selesai" class="fin-label">Instruksi Pengambilan / Penutup</label>
                    <textarea name="catatan_admin" id="catatan_selesai" rows="4" placeholder="Contoh: Berkas telah selesai dicetak. Silakan ambil di loket MPP..."></textarea>
                </div>
            </div>
        </form>

        <div class="fin-footer">
            <button type="button" class="btn-fin-cancel" onclick="window.closeFinishModal()">Batal</button>
            <button type="submit" form="formFinish" class="btn-fin-submit" id="btnConfirmFinish">Tandai Selesai</button>
        </div>
    </div>
</div>

<script>
    if (typeof window.finModalInitialized === 'undefined') {
        window.finModalInitialized = true;

        window.openFinishModal = function(id, nomor) {
            const modal = document.getElementById('modalFinishSub');
            const form = document.getElementById('formFinish');
            const portal = '{{ $portalCode }}';
            
            document.getElementById('fin-nomor-text').innerText = nomor;
            form.action = `/${portal}/submissions/${id}/status`;
            
            // Trigger Animasi Masuk
            modal.style.display = 'flex';
            modal.classList.remove('is-closing');
            setTimeout(() => {
                modal.classList.add('is-active');
            }, 10);

            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('catatan_selesai').focus(), 400);
        }

        window.closeFinishModal = function() {
            const modal = document.getElementById('modalFinishSub');
            if(modal) {
                // Trigger Animasi Keluar
                modal.classList.add('is-closing');
                setTimeout(() => {
                    modal.classList.remove('is-active', 'is-closing');
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                    document.getElementById('formFinish').reset();
                }, 300); // Sesuai durasi durasi CSS out
            }
        }

        document.getElementById('formFinish').addEventListener('submit', function() {
            const btn = document.getElementById('btnConfirmFinish');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> MEMPROSES...';
        });
    }
</script>