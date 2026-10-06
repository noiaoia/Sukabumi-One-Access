{{-- resources/views/components/admin/modal-process-submission.blade.php --}}
@props(['portalCode'])

<div id="modalProcessSub" class="proc-overlay" onclick="if(event.target === this) window.closeProcessModal()">
    <div class="proc-card">
        {{-- HEADER --}}
        <div class="proc-header">
            <div class="proc-header-left">
                <div class="proc-icon-box">
                    <i class="bi bi-gear-fill proc-spin"></i>
                </div>
                <div class="min-w-0">
                    <h2>Lanjutkan Proses</h2>
                    <p id="proc-nomor-text">#SOA-2024...</p>
                </div>
            </div>
            <button type="button" class="proc-close-x" onclick="window.closeProcessModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="proc-body-scroll">
            {{-- SECTION 1: KONFIRMASI --}}
            <div class="proc-section">
                <div class="proc-section-heading">
                    <span class="proc-section-number">1</span>
                    <div>
                        <h3>Validasi Internal</h3>
                        <p>Konfirmasi kesiapan berkas untuk diproduksi</p>
                    </div>
                </div>

                <div class="proc-warning-box">
                    <i class="bi bi-info-circle-fill"></i>
                    <p>Pastikan data pemohon valid. Status akan berubah menjadi <strong>"Diproses"</strong> dan akses edit warga akan dikunci.</p>
                </div>
            </div>

            {{-- SECTION 2: DETAIL PARAMETER --}}
            <div class="proc-section">
                <div class="proc-section-heading">
                    <span class="proc-section-number">2</span>
                    <div>
                        <h3>Informasi Berkas</h3>
                        <p>Data identitas pengajuan saat ini</p>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- Nama Pemohon --}}
                    <div class="proc-field">
                        <label class="proc-label">Nama Pemohon</label>
                        <div class="proc-input-mock">
                            <i class="bi bi-person-vcard proc-field-icon"></i>
                            <span id="proc-nama-text">Memuat...</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Tahap --}}
                        <div class="proc-field">
                            <label class="proc-label">Tahap Berikutnya</label>
                            <div class="proc-input-mock">
                                <i class="bi bi-arrow-right-circle proc-field-icon"></i>
                                <span>Produksi</span>
                            </div>
                        </div>
                        {{-- Otoritas --}}
                        <div class="proc-field">
                            <label class="proc-label">Otoritas</label>
                            <div class="proc-input-mock">
                                <i class="bi bi-shield-check proc-field-icon"></i>
                                <span>Sistem SOA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="proc-footer">
            <button type="button" class="btn-proc-cancel" onclick="window.closeProcessModal()">
                Batal
            </button>
            <button type="button" class="btn-proc-submit" id="btnConfirmProcess">
                Konfirmasi & Proses
            </button>
        </div>
    </div>
</div>

<style>
    /* 1. OVERLAY & CARD */
    .proc-overlay { 
        position: fixed; inset: 0; background: rgba(10, 30, 51, 0.85); 
        backdrop-filter: blur(6px); display: none; align-items: center; 
        justify-content: center; z-index: 12000; padding: 20px; opacity: 0; 
        transition: opacity 0.3s ease; 
    }
    .proc-overlay.active { display: flex; opacity: 1; }
    
    .proc-card { 
        background: #fff; width: 100%; max-width: 380px; max-height: 80vh; border-radius: 24px; 
        overflow: hidden; box-shadow: 0 30px 60px -12px rgba(0,0,0,0.4); 
        transform: scale(0.95) translateY(20px); transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); 
        display: flex; flex-direction: column;
    }
    .proc-overlay.active .proc-card { transform: scale(1) translateY(0); }

    /* 2. HEADER - TEMA NAVY & GOLD (OREN) */
    .proc-header { 
        background: #0A1E33; padding: 20px 24px; border-bottom: 4px solid #E8A33D; 
        display: flex; align-items: center; justify-content: space-between; gap: 12px; color: #fff;
    }
    .proc-header-left { display: flex; align-items: center; gap: 16px; min-width: 0; }
    .proc-header h2 { font-size: 15px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .proc-header p { font-size: 10px; color: rgba(255,255,255,0.5); margin: 2px 0 0; font-family: 'JetBrains Mono', monospace; }
    
    .proc-icon-box { 
        width: 42px; height: 42px; border-radius: 12px; display: flex; 
        align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;
        background: rgba(232, 163, 61, 0.15); color: #E8A33D;
    }
    .proc-spin { animation: procGear 4s linear infinite; }
    @keyframes procGear { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    .proc-close-x { background: none; border: none; color: rgba(255,255,255,0.4); cursor: pointer; }

    /* 3. BODY & SECTIONS */
    .proc-body-scroll { overflow-y: auto; flex: 1; }
    .proc-section { padding: 20px 24px; }
    .proc-section + .proc-section { border-top: 1px solid #F1F5F9; }

    .proc-section-heading { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .proc-section-number {
        width: 22px; height: 22px; border-radius: 7px;
        background: #123457; color: #E8A33D;
        font-size: 10px; font-weight: 900;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .proc-section-heading h3 { font-size: 12px; font-weight: 800; color: #123457; margin: 0; text-transform: uppercase; }
    .proc-section-heading p { font-size: 10px; color: #94a3b8; margin: 1px 0 0; font-weight: 600; }

    /* WARNING BOX - TEMA OREN SOFT */
    .proc-warning-box { 
        background: #FFFBEB; border: 1px solid #FEF3C7; padding: 12px 16px; 
        border-radius: 14px; display: flex; gap: 12px;
    }
    .proc-warning-box i { color: #D97706; font-size: 1.1rem; flex-shrink: 0; }
    .proc-warning-box p { font-size: 11px; color: #92400E; margin: 0; line-height: 1.5; font-weight: 600; }

    /* 4. PARAMETER FIELDS */
    .proc-field { margin-bottom: 5px; }
    .proc-label { 
        display: block; font-size: 9px; font-weight: 800; color: #64748b; 
        text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; margin-left: 4px;
    }
    .proc-input-mock {
        width: 100%; padding: 10px 14px 10px 40px; background: #F8FAFC; 
        border: 1.2px solid #E2E8F0; border-radius: 12px; font-size: 12px; 
        font-weight: 800; color: #123457; position: relative; display: flex; align-items: center;
    }
    .proc-field-icon { position: absolute; left: 14px; color: #94a3b8; font-size: 1rem; }

    /* 5. FOOTER */
    .proc-footer { padding: 16px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; gap: 10px; }
    .btn-proc-cancel { flex: 1; padding: 12px; border-radius: 12px; border: 1px solid #E2E8F0; background: #fff; color: #475569; font-weight: 800; font-size: 11px; cursor: pointer; text-transform: uppercase; }
    .btn-proc-submit { 
        flex: 1.5; padding: 12px; border-radius: 12px; border: none; 
        background: #123457; color: #fff; font-weight: 800; font-size: 11px; 
        cursor: pointer; transition: 0.3s; text-transform: uppercase;
    }
    .btn-proc-submit:hover { background: #E8A33D; color: #0A1E33; box-shadow: 0 8px 16px rgba(232, 163, 61, 0.25); }
    .btn-proc-cancel:hover { background: #F1F5F9; color: #1e293b; }
</style>

<script>
    if (typeof window.procModalInitialized === 'undefined') {
        window.subModalInitialized = true; // Flag global
        let currentProcId = null;

        window.openProcessModal = function(id, nomor, nama) {
            currentProcId = id;
            document.getElementById('proc-nomor-text').innerText = nomor;
            document.getElementById('proc-nama-text').innerText = nama;
            
            const modal = document.getElementById('modalProcessSub');
            const btn = document.getElementById('btnConfirmProcess');
            
            // Reset tombol saat modal dibuka
            btn.disabled = false;
            btn.innerHTML = 'Konfirmasi & Proses';
            
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        window.closeProcessModal = function() {
            document.getElementById('modalProcessSub').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('btnConfirmProcess').onclick = function() {
            const btn = this;
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> MEMPROSES...';
            
            if(typeof window.updateStatusSubmission === 'function') {
                // Panggil fungsi utama di manage.blade.php
                window.updateStatusSubmission(currentProcId, 'diproses', false);
                
                // Tambahkan timeout pengaman: Jika dalam 10 detik tidak reload/respon, kembalikan tombol
                setTimeout(() => {
                   if(btn.disabled) {
                       btn.disabled = false;
                       btn.innerHTML = originalHtml;
                   }
                }, 10000);
            } else {
                alert("Kesalahan Sistem: Fungsi pembaruan tidak ditemukan.");
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        };
    }
</script>