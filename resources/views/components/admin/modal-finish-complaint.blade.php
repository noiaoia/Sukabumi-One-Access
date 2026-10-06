{{-- resources/views/components/admin/modal-finish-complaint.blade.php --}}
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
        display: none; 
        align-items: center; justify-content: center; 
        z-index: 12000; padding: 20px; opacity: 0; 
    }

    .fin-overlay.is-active { 
        display: flex; 
        opacity: 1; 
        animation: finOverlayIn 0.3s ease forwards; 
    }
    .fin-overlay.is-active .fin-card { 
        animation: finSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; 
    }

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

    /* 4. HEADER (Navy & Orange) */
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
    .fin-close-x:hover { background: rgba(232, 163, 61, 0.2); color: #E8A33D; }

    /* 5. BODY & SECTION */
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

    /* Orange/Gold Warning Box */
    .fin-warning-box { 
        background: #FFFBEB; border: 1px solid #FEF3C7; padding: 12px 16px; 
        border-radius: 14px; display: flex; gap: 12px;
    }
    .fin-warning-box i { color: #D97706; font-size: 1.1rem; flex-shrink: 0; }
    .fin-warning-box p { font-size: 11px; color: #92400E; margin: 0; line-height: 1.5; font-weight: 600; }

    .fin-label { 
        display: block; font-size: 9px; font-weight: 800; color: #64748b; 
        text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; margin-left: 4px;
    }
    #response_message_finish { 
        width: 100%; padding: 14px; border: 1.5px solid #E2E8F0; border-radius: 16px; 
        font-size: 13px; font-weight: 600; color: #123457; outline: none; 
        transition: all 0.3s; font-family: inherit; background: #F8FAFC; resize: none;
    }
    #response_message_finish:focus { border-color: #E8A33D; background: #fff; box-shadow: 0 0 0 4px rgba(232, 163, 61, 0.08); }

    /* 6. FOOTER */
    .fin-footer { padding: 16px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; gap: 10px; }
    .btn-fin-cancel { flex: 1; padding: 12px; border-radius: 12px; border: 1px solid #E2E8F0; background: #fff; color: #475569; font-weight: 800; font-size: 11px; cursor: pointer; text-transform: uppercase; }
    .btn-fin-cancel:hover { background: #F1F5F9; color: #1e293b; }
    
    .btn-fin-submit { 
        flex: 2; padding: 12px; border-radius: 12px; border: none; 
        background: #123457; color: #fff; font-weight: 800; font-size: 11px; 
        cursor: pointer; transition: all 0.3s; text-transform: uppercase; letter-spacing: 1px;
    }
    .btn-fin-submit:hover { background: #E8A33D; color: #0A1E33; box-shadow: 0 8px 16px rgba(232, 163, 61, 0.25); }
</style>
@endonce

<div id="modalFinishComplaint" class="fin-overlay" onclick="if(event.target === this) window.closeFinishModal()">
    <div class="fin-card">
        <div class="fin-header">
            <div class="fin-header-left">
                <div class="fin-icon-box"><i class="bi bi-patch-check-fill"></i></div>
                <div class="min-w-0">
                    <h2>Selesaikan Laporan</h2>
                    <p id="fin-ticket-text">#ADU-2024...</p>
                </div>
            </div>
            <button type="button" class="fin-close-x" onclick="window.closeFinishModal()"><i class="bi bi-x-lg text-xs"></i></button>
        </div>

        <form id="formFinishComplaint" method="POST" class="fin-body-scroll">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="done">
            
            <div class="fin-section">
                <div class="fin-section-heading">
                    <span class="fin-section-number">1</span>
                    <h3>Penyelesaian Laporan</h3>
                </div>
                <div class="fin-warning-box">
                    <i class="bi bi-info-circle-fill"></i>
                    <p>Konfirmasi ini menyatakan masalah telah <strong>Tuntas Ditangani</strong>. Warga akan menerima notifikasi bahwa laporan telah ditutup.</p>
                </div>
            </div>

            <div class="fin-section">
                <div class="fin-section-heading">
                    <span class="fin-section-number">2</span>
                    <h3>Respon Penyelesaian</h3>
                </div>
                <div class="fin-field">
                    <label for="response_message_finish" class="fin-label">Pesan Tanggapan Untuk Warga</label>
                    <textarea name="response_message" id="response_message_finish" rows="4" placeholder="Contoh: Laporan Anda telah ditindaklanjuti. Perbaikan jalan telah selesai dilakukan pada hari ini..." required></textarea>
                </div>
            </div>
        </form>

        <div class="fin-footer">
            <button type="button" class="btn-fin-cancel" onclick="window.closeFinishModal()">Batal</button>
            <button type="submit" form="formFinishComplaint" class="btn-fin-submit" id="btnConfirmFinish">Tutup Laporan</button>
        </div>
    </div>
</div>

<script>
    if (typeof window.finModalInitialized === 'undefined') {
        window.finModalInitialized = true;

        window.openFinishModal = function(id) {
            const modal = document.getElementById('modalFinishComplaint');
            const form = document.getElementById('formFinishComplaint');
            const portal = '{{ $portalCode }}';
            
            const container = document.getElementById(`cm-${id}`);
            const ticketId = container.getAttribute('data-code').toUpperCase();
            
            document.getElementById('fin-ticket-text').innerText = ticketId;
            form.action = `/${portal}/pengaduan/${id}/update`;
            
            modal.style.display = 'flex';
            modal.classList.remove('is-closing');
            setTimeout(() => {
                modal.classList.add('is-active');
            }, 10);

            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('response_message_finish').focus(), 400);
        }

        window.closeFinishModal = function() {
            const modal = document.getElementById('modalFinishComplaint');
            if(modal) {
                modal.classList.add('is-closing');
                setTimeout(() => {
                    modal.classList.remove('is-active', 'is-closing');
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                    document.getElementById('formFinishComplaint').reset();
                }, 300);
            }
        }

        document.getElementById('formFinishComplaint').addEventListener('submit', function() {
            const btn = document.getElementById('btnConfirmFinish');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> MEMPROSES...';
        });
    }
</script>