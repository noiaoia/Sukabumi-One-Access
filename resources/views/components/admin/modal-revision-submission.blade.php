{{-- resources/views/components/admin/modal-revision-submission.blade.php --}}
@props(['portalCode'])

<div id="modalRevision" class="rev-overlay" onclick="if(event.target === this) window.closeRevisionModal()">
    <div class="rev-card">
        {{-- HEADER --}}
        <div class="rev-header">
            <div class="rev-icon-box">
                <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
            <div class="min-w-0">
                <h2>Minta Perbaikan</h2>
                <p id="rev-nomor-text" class="truncate">#SOA...</p>
            </div>
            <button type="button" class="rev-close-x" onclick="window.closeRevisionModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- FORM --}}
        <form id="formRevision" method="POST">
            @csrf
            @method('PATCH')
            {{-- Status otomatis diset ke perlu_revisi oleh JS saat submit --}}
            <input type="hidden" name="status" value="perlu_revisi">
            
            <div class="rev-body">
                <div class="rev-warning-box">
                    <i class="bi bi-info-circle-fill"></i>
                    <p>Warga akan menerima notifikasi untuk mengunggah ulang atau memperbaiki data sesuai instruksi Anda.</p>
                </div>

                <div class="rev-input-group">
                    <label for="catatan_revisi" class="rev-label">Instruksi Perbaikan</label>
                    <textarea name="catatan_admin" 
                              id="catatan_revisi" 
                              rows="5" 
                              placeholder="Contoh: Lampiran Akta Kelahiran tidak terbaca, mohon unggah foto asli yang lebih jelas..." 
                              required></textarea>
                </div>
                
                <div class="rev-hint">
                    <i class="bi bi-shield-lock"></i>
                    <span>Tindakan ini akan membuka akses edit bagi pemohon.</span>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="rev-footer">
                <button type="button" class="btn-rev-cancel" onclick="window.closeRevisionModal()">
                    Batal
                </button>
                <button type="submit" class="btn-rev-submit" id="btnSubmitRevision">
                    Kirim Instruksi <i class="bi bi-send-fill ml-1"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* 1. OVERLAY & ANIMATION */
    .rev-overlay { 
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85); 
        backdrop-filter: blur(6px); display: none; align-items: center; 
        justify-content: center; z-index: 11000; padding: 20px; opacity: 0; 
        transition: opacity 0.3s ease; 
    }
    .rev-overlay.active { display: flex; opacity: 1; }
    
    .rev-card { 
        background: #fff; width: 100%; max-width: 420px; border-radius: 24px; 
        overflow: hidden; box-shadow: 0 30px 60px -12px rgba(0,0,0,0.25); 
        transform: translateY(20px) scale(0.95); transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); 
    }
    .rev-overlay.active .rev-card { transform: translateY(0) scale(1); }

    /* 2. HEADER */
    .rev-header { 
        background: #0A1E33; padding: 22px 24px; border-bottom: 4px solid #F59E0B; 
        display: flex; align-items: center; gap: 16px; color: #fff; position: relative;
    }
    .rev-header h2 { font-size: 16px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .rev-header p { font-size: 11px; color: rgba(255,255,255,0.5); margin: 2px 0 0; font-family: 'JetBrains Mono', monospace; }
    .rev-icon-box { 
        width: 44px; height: 44px; background: rgba(245, 158, 11, 0.15); 
        color: #F59E0B; border-radius: 12px; display: flex; align-items: center; 
        justify-content: center; font-size: 1.4rem; flex-shrink: 0;
    }
    .rev-close-x { 
        position: absolute; top: 18px; right: 18px; background: none; border: none; 
        color: rgba(255,255,255,0.3); cursor: pointer; transition: 0.2s; 
    }
    .rev-close-x:hover { color: #fff; }

    /* 3. BODY */
    .rev-body { padding: 24px; }
    .rev-warning-box { 
        background: #FFFBEB; border: 1px solid #FEF3C7; padding: 12px 16px; 
        border-radius: 14px; display: flex; gap: 12px; margin-bottom: 20px;
    }
    .rev-warning-box i { color: #D97706; font-size: 1.1rem; flex-shrink: 0; }
    .rev-warning-box p { font-size: 11px; color: #92400E; margin: 0; line-height: 1.5; font-weight: 600; }

    .rev-label { 
        display: block; font-size: 10px; font-weight: 800; color: #64748b; 
        text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; margin-left: 4px;
    }
    #catatan_revisi { 
        width: 100%; padding: 14px; border: 1.5px solid #E2E8F0; border-radius: 16px; 
        font-size: 13px; font-weight: 600; color: #123457; outline: none; 
        transition: all 0.3s; font-family: inherit; background: #F8FAFC; resize: none;
    }
    #catatan_revisi:focus { border-color: #F59E0B; background: #fff; box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.08); }
    
    .rev-hint { 
        display: flex; align-items: center; gap: 6px; font-size: 10px; 
        color: #94a3b8; font-weight: 700; margin-top: 12px; text-transform: uppercase;
    }

    /* 4. FOOTER */
    .rev-footer { 
        padding: 16px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0; 
        display: flex; gap: 12px; 
    }
    .btn-rev-cancel { 
        flex: 1; padding: 12px; border-radius: 12px; border: 1px solid #E2E8F0; 
        background: #fff; color: #64748b; font-weight: 700; font-size: 12px; cursor: pointer; transition: 0.2s;
    }
    .btn-rev-cancel:hover { background: #F1F5F9; color: #1e293b; }
    
    .btn-rev-submit { 
        flex: 2; padding: 12px; border-radius: 12px; border: none; 
        background: #123457; color: #fff; font-weight: 800; font-size: 12px; 
        cursor: pointer; transition: all 0.3s; text-transform: uppercase; letter-spacing: 1px;
    }
    .btn-rev-submit:hover { background: #D97706; box-shadow: 0 8px 16px rgba(217, 119, 6, 0.25); }
</style>

<script>
    if (typeof window.revModalInitialized === 'undefined') {
        window.revModalInitialized = true;

        window.openRevisionModal = function(id, nomor) {
            const modal = document.getElementById('modalRevision');
            const form = document.getElementById('formRevision');
            const portal = '{{ $portalCode }}';
            
            document.getElementById('rev-nomor-text').innerText = nomor;
            // Sinkronkan rute action form
            form.action = `/${portal}/submissions/${id}/status`;
            
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Focus ke textarea setelah animasi selesai
            setTimeout(() => {
                document.getElementById('catatan_revisi').focus();
            }, 300);
        }

        window.closeRevisionModal = function() {
            const modal = document.getElementById('modalRevision');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            document.getElementById('formRevision').reset();
        }

        // Logic Submit Loading
        document.getElementById('formRevision').addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmitRevision');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> MEMPROSES...';
        });
    }
</script>