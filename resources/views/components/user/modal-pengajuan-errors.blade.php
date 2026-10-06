{{-- resources/views/components/user/modal-pengajuan-errors.blade.php --}}
@php
    $hasServerError = $errors->any() || session('error');
    $errorCount = $errors->count() + (session('error') ? 1 : 0);
@endphp

{{-- Wrapper Utama Modal (ID & Class tetap sama) --}}
<div id="pengajuanErrorModal" class="em-modal" data-has-server-error="{{ $hasServerError ? 'true' : 'false' }}" onclick="if(event.target === this) closePengajuanErrorModal()">
    <div class="em-card">
        {{-- 1. Header Compact --}}
        <div class="em-header">
            <div class="em-icon-box">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <h2>VALIDASI GAGAL</h2>
        </div>

        {{-- 2. Body Compact --}}
        <div class="em-body">
            {{-- Ikon Silang (X) Kecil --}}
            <div class="em-check-wrapper">
                <span class="em-pulse-ring"></span>
                <div class="em-check-circle">
                    <i class="bi bi-x-lg"></i>
                </div>
            </div>

            <h3 class="em-title" id="pe-title-text">Periksa Isian</h3>
            
            <p class="em-text" id="pe-desc-text">
                {{ $hasServerError 
                    ? 'Ada '.$errorCount.' data yang belum sesuai.' 
                    : 'Mohon perbaiki kesalahan pada formulir Anda.' 
                }}
            </p>

            {{-- Box Daftar Error Kecil --}}
            <div class="em-list-box">
                <ul class="em-list" id="pe-list-content">
                    @if(session('error'))
                        <li>
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ session('error') }}
                        </li>
                    @endif

                    @foreach($errors->all() as $message)
                        <li>
                            <i class="bi bi-dot"></i>
                            {{ $message }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- 3. Footer Compact --}}
        <div class="em-footer">
            <button type="button" class="btn-em-close" onclick="window.closePengajuanErrorModal()">
                Perbaiki Sekarang
            </button>
        </div>
    </div>
</div>

<style>
    /* 1. LAYOUT & OVERLAY */
    .em-modal {
        position: fixed !important;
        inset: 0 !important;
        background: rgba(10, 30, 51, 0.85) !important;
        backdrop-filter: blur(4px);
        display: none; 
        align-items: center;
        justify-content: center;
        z-index: 9999999 !important;
        padding: 15px;
        opacity: 0; /* Tambahan untuk transisi awal */
    }

    /* Munculkan modal (In) */
    .em-modal.active {
        display: flex !important;
        animation: emFade 0.3s ease forwards;
    }
    .em-modal.active .em-card {
        animation: emSpring 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    /* Logic Animasi Keluar (Out) */
    .em-modal.is-closing {
        animation: emFadeOut 0.3s ease forwards !important;
    }
    .em-modal.is-closing .em-card {
        animation: emSpringOut 0.3s ease forwards !important;
    }

    body.modal-open { overflow: hidden !important; }

    /* 2. CARD COMPACT */
    .em-card {
        background: #fff;
        width: 100%;
        max-width: 300px;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }

    /* ... STYLE LAINNYA TETAP SAMA ... */
    .em-header { background: #0A1E33; padding: 12px; border-bottom: 2px solid #ef4444; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .em-header h2 { color: #fff; font-size: 10px; font-weight: 800; margin: 0; letter-spacing: 1px; text-transform: uppercase; }
    .em-icon-box { color: #ef4444; font-size: 13px; display: flex; }
    .em-body { padding: 20px 15px; text-align: center; }
    .em-check-wrapper { position: relative; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; }
    .em-pulse-ring { position: absolute; width: 45px; height: 45px; border-radius: 50%; border: 2px solid #ef4444; animation: emPulseRing 1.8s ease-out infinite; }
    .em-check-circle { position: relative; z-index: 2; width: 45px; height: 45px; background: rgba(239, 68, 68, 0.1); border: 2px solid #ef4444; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .em-title { font-size: 15px; font-weight: 800; color: #123457; margin-bottom: 4px; }
    .em-text { font-size: 10.5px; color: #64748b; line-height: 1.4; margin-bottom: 15px; }
    .em-list-box { background: #FFF1F2; border: 1px dashed #FECACA; border-radius: 12px; padding: 10px; max-height: 120px; overflow-y: auto; text-align: left; }
    .em-list { list-style: none; padding: 0; margin: 0; }
    .em-list li { font-size: 9.5px; font-weight: 700; color: #b91c1c; line-height: 1.4; display: flex; gap: 4px; align-items: flex-start; margin-bottom: 6px; }
    .em-list li i { font-size: 12px; color: #ef4444; }
    .em-footer { padding: 12px; background: #F8FAFC; border-top: 1px solid #f1f5f9; }
    .btn-em-close { width: 100%; background: #ef4444; color: #fff; border: none; padding: 10px; border-radius: 8px; font-size: 11px; font-weight: 800; cursor: pointer; transition: 0.2s; text-transform: uppercase; }
    .btn-em-close:hover { background: #b91c1c; transform: translateY(-1px); }

    /* KEYFRAMES ANIMASI */
    @keyframes emFade { from { opacity: 0; } to { opacity: 1; } }
    @keyframes emFadeOut { from { opacity: 1; } to { opacity: 0; } }

    @keyframes emSpring { 
        0% { opacity: 0; transform: scale(0.9) translateY(20px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes emSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.9) translateY(15px); }
    }

    @keyframes emPulseRing {
        0% { transform: scale(1); opacity: 0.6; }
        100% { transform: scale(1.7); opacity: 0; }
    }
</style>

@push('scripts')
<script>
    (function() {
        /**
         * Fungsi Menutup Modal dengan Animasi Out
         */
        window.closePengajuanErrorModal = function() {
            const modal = document.getElementById('pengajuanErrorModal');
            if (modal && modal.classList.contains('active')) {
                // Tambahkan class closing untuk memicu animasi keluar
                modal.classList.add('is-closing');

                // Tunggu animasi selesai (300ms) baru hilangkan modal
                setTimeout(() => {
                    modal.classList.remove('active');
                    modal.classList.remove('is-closing');
                    document.body.classList.remove('modal-open');
                }, 300);
            }
        };

        /**
         * Fungsi Membuka Modal (Tetap)
         */
        window.openPengajuanErrorModal = function(messages = []) {
            const modal = document.getElementById('pengajuanErrorModal');
            const listContent = document.getElementById('pe-list-content');
            const titleText = document.getElementById('pe-title-text');
            const descText = document.getElementById('pe-desc-text');

            if (modal && listContent) {
                if (messages.length > 0) {
                    titleText.innerText = "Gagal Validasi";
                    descText.innerText = "Mohon perbaiki data berikut:";
                    listContent.innerHTML = '';
                    messages.forEach(function(msg) {
                        const li = document.createElement('li');
                        li.innerHTML = '<i class="bi bi-dot"></i> ' + msg;
                        listContent.appendChild(li);
                    });
                }

                modal.classList.remove('is-closing'); // Pastikan bersih
                modal.classList.add('active');
                document.body.classList.add('modal-open');
            }
        };

        /**
         * Auto-Trigger Laravel Errors
         */
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('pengajuanErrorModal');
            if (modal) {
                const hasError = modal.getAttribute('data-has-server-error') === 'true';
                if (hasError) {
                    window.openPengajuanErrorModal([]);
                }
            }
        });

        // Dukungan tombol ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') window.closePengajuanErrorModal();
        });
    })();
</script>
@endpush