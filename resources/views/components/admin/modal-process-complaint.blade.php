{{-- resources/views/components/admin/modal-process-complaint.blade.php --}}
@props(['portalCode', 'instansiList'])

<div id="modalProcess" class="process-ins-overlay" onclick="if(event.target === this) window.closeProcessModal()">
    <div class="process-ins-card">

        {{-- HEADER --}}
        <div class="process-ins-header">
            <div class="process-ins-header-left">
                <div class="process-ins-icon-box"><i class="bi bi-send-fill"></i></div>
                <div class="min-w-0">
                    <h2>Verifikasi Penanganan</h2>
                    <p>Validasi & teruskan ke dinas terkait</p>
                </div>
            </div>
            <button type="button" class="process-ins-close" onclick="window.closeProcessModal()"><i class="bi bi-x-lg text-xs"></i></button>
        </div>

        <form id="formProcess" method="POST" class="process-ins-body-scroll complaintForm">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="status" value="process">

            {{-- ========== SECTION 1: INSTANSI TUJUAN ========== --}}
            <div class="process-ins-section">
                <div class="process-ins-section-heading">
                    <span class="process-ins-section-number">1</span>
                    <div>
                        <h3>Instansi Tujuan</h3>
                        <p>Pilih dinas yang bertanggung jawab</p>
                    </div>
                </div>

                <div class="process-ins-field">
                    <label class="process-ins-label">Dinas / Instansi Terkait</label>
                    <div class="process-ins-input-group">
                        <select name="instansi_id" required class="process-ins-input appearance-none">
                            <option value="" disabled selected>-- Pilih Instansi Tujuan --</option>
                            @foreach($instansiList as $ins)
                                <option value="{{ $ins->id }}">{{ $ins->nama }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-building edit-ins-input-icon"></i>
                        <i class="bi bi-chevron-down absolute right-3.5 text-slate-400 pointer-events-none text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- ========== SECTION 2: RESPON WARGA ========== --}}
            <div class="process-ins-section">
                <div class="process-ins-section-heading">
                    <span class="process-ins-section-number">2</span>
                    <div>
                        <h3>Respon Warga</h3>
                        <p>Pesan balasan resmi dari sistem</p>
                    </div>
                </div>

                <div class="process-ins-field">
                    <label class="process-ins-label">Pesan Tanggapan</label>
                    <textarea name="response_message" 
                              rows="4" 
                              class="process-ins-input !pl-3.5" 
                              style="padding-top: 10px; height: auto;" 
                              placeholder="Contoh: Laporan Anda kami teruskan ke Dinas Perhubungan untuk ditindaklanjuti lebih lanjut..." 
                              required></textarea>
                    <p class="process-ins-hint"><i class="bi bi-info-circle mr-1"></i> Pesan ini akan dikirimkan ke notifikasi warga.</p>
                </div>
            </div>
        </form>

        <div class="process-ins-footer">
            <button type="button" class="process-ins-btn-cancel" onclick="window.closeProcessModal()">Batal</button>
            <button type="submit" form="formProcess" class="process-ins-btn-save complaintSubmitBtn">Teruskan Laporan</button>
        </div>
    </div>
</div>

@once
<style>
    /* 1. ANIMATIONS */
    @keyframes processInsOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes processInsSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.01) translateY(-4px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }

    /* 2. OVERLAY */
    .process-ins-overlay {
        position: fixed; inset: 0;
        background: rgba(10, 30, 51, 0.75);
        backdrop-filter: blur(5px);
        display: none; align-items: center; justify-content: center;
        z-index: 11000; padding: 15px;
        opacity: 0;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .process-ins-overlay.is-active { display: flex; animation: processInsOverlayIn 0.3s ease forwards; }
    .process-ins-overlay.is-active .process-ins-card { animation: processInsSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .process-ins-overlay.is-closing { animation: processInsOverlayOut 0.3s ease forwards; }

    /* 3. CARD */
    .process-ins-card {
        background: #ffffff;
        width: 100%; max-width: 400px;
        max-height: 85vh;
        border-radius: 22px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        display: flex; flex-direction: column;
    }

    .process-ins-header {
        background: #0A1E33;
        padding: 18px 24px;
        border-bottom: 4px solid #E8A33D;
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px;
        flex-shrink: 0;
    }
    .process-ins-header-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .process-ins-icon-box {
        width: 32px; height: 32px; border-radius: 9px;
        background: rgba(255,255,255,0.1);
        display: flex; align-items: center; justify-content: center;
        color: #E8A33D; font-size: 15px; flex-shrink: 0;
    }
    .process-ins-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; letter-spacing: 0.3px; }
    .process-ins-header p { color: rgba(255,255,255,0.5); font-size: 10.5px; margin: 2px 0 0; font-weight: 600; }
    .process-ins-close {
        width: 30px; height: 30px; border-radius: 9px;
        background: rgba(255,255,255,0.06); border: none; color: #94a3b8;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        transition: 0.2s;
    }
    .process-ins-close:hover { background: rgba(239,68,68,0.15); color: #f87171; }

    /* 4. BODY */
    .process-ins-body-scroll { overflow-y: auto; flex: 1; }
    .process-ins-section { padding: 20px 24px; }
    .process-ins-section + .process-ins-section { border-top: 1px solid #F1F5F9; }

    .process-ins-section-heading { display: flex; align-items: center; gap: 9px; margin-bottom: 16px; }
    .process-ins-section-number {
        width: 22px; height: 22px; border-radius: 7px;
        background: #123457; color: #E8A33D;
        font-size: 10px; font-weight: 900;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .process-ins-section-heading h3 { font-size: 12px; font-weight: 800; color: #123457; margin: 0; }
    .process-ins-section-heading p { font-size: 10px; color: #94a3b8; margin: 1px 0 0; font-weight: 600; }

    /* 5. FIELDS */
    .process-ins-field { margin-bottom: 14px; }
    .process-ins-label { display: block; font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; color: #64748b; margin-bottom: 6px; margin-left: 2px; }
    .process-ins-input-group { position: relative; display: flex; align-items: center; width: 100%; }
    .process-ins-input {
        width: 100%; padding: 10px 14px 10px 40px;
        background: #F8FAFC; border: 1.2px solid #E2E8F0; border-radius: 12px;
        font-size: 12.5px; font-weight: 700; color: #123457;
        transition: all 0.2s; font-family: inherit;
    }
    .process-ins-input:focus { outline: none; border-color: #123457; background: #fff; box-shadow: 0 0 0 3px rgba(18, 52, 87, 0.06); }
    .edit-ins-input-icon { position: absolute; left: 14px; color: #94a3b8; font-size: 1rem; z-index: 5; }
    .process-ins-hint { font-size: 9.5px; color: #E8A33D; font-weight: 700; margin-top: 6px; margin-left: 2px; }

    /* 6. FOOTER */
    .process-ins-footer {
        padding: 14px 24px; background: #F8FAFC; border-top: 1px solid #e2e8f0;
        display: flex; justify-content: flex-end; gap: 10px; flex-shrink: 0;
    }
    .process-ins-btn-cancel {
        padding: 10px 20px; border-radius: 10px; font-size: 11px; font-weight: 700; color: #475569;
        background: #e2e8f0; border: 1px solid #cbd5e1; cursor: pointer; transition: 0.2s;
    }
    .process-ins-btn-save {
        padding: 10px 26px; border-radius: 10px; font-size: 11px; font-weight: 800; color: #fff;
        background: #123457; border: none; box-shadow: 0 8px 14px -3px rgba(18, 52, 87, 0.2);
        text-transform: uppercase; letter-spacing: 0.6px; cursor: pointer; transition: 0.2s;
    }
    .process-ins-btn-save:hover { background: #E8A33D; color: #0A1E33; transform: translateY(-1px); }

    @keyframes processInsOverlayOut { from { opacity: 1; } to { opacity: 0; } }
</style>

<script>
    if (typeof window.processModalInitialized === 'undefined') {
        window.processModalInitialized = true;

        window.openProcessModal = function(id) {
            const modal = document.getElementById('modalProcess');
            const form = document.getElementById('formProcess');
            if (!modal || !form) return;

            /** 
             * PERBAIKAN UTAMA:
             * Gunakan helper route() Laravel untuk mendapatkan struktur URL yang benar.
             * Kita buat placeholder ':id' yang nanti akan diganti dengan ID asli via JS.
             */
            let urlTemplate = "{{ route('admin.pengaduan.update', ['portal_code' => $portalCode, 'id' => ':id']) }}";
            form.action = urlTemplate.replace(':id', id);

            // Reset form agar bersih
            form.reset();

            // Tampilkan Modal
            modal.classList.add('is-active');
            modal.classList.remove('is-closing');
            document.body.style.overflow = 'hidden';
            
            // Debugging: Lihat di console (F12) apakah URL-nya sudah benar
            console.log("Submit URL set to: " + form.action);
        };

        window.closeProcessModal = function() {
            const modal = document.getElementById('modalProcess');
            if (modal) {
                modal.classList.add('is-closing');
                setTimeout(() => {
                    modal.classList.remove('is-active', 'is-closing');
                    document.body.style.overflow = 'auto';
                }, 300);
            }
        };
    }

    document.addEventListener('DOMContentLoaded', function () {

        const form = document.getElementById('formProcess');
        const btn = document.querySelector(
            'button[type="submit"][form="formProcess"].complaintSubmitBtn'
        );

        if (!form || !btn) return;

        form.addEventListener('submit', function () {

            // Cegah double submit
            if (btn.disabled) return;

            btn.disabled = true;

            btn.innerHTML = `
                <i class="bi bi-arrow-repeat animate-spin mr-2"></i>
                MEMPROSES...
            `;

            btn.style.opacity = '0.8';
            btn.style.cursor = 'not-allowed';
            btn.style.pointerEvents = 'none';
        });

    });
</script>
@endonce