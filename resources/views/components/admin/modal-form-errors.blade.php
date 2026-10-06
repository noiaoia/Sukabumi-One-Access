{{-- resources/views/components/admin/modal-form-errors.blade.php --}}

@if ($errors->any())
<style>
    #modalFormErrorOverlay { opacity: 0; transition: opacity .3s ease; }
    #modalFormErrorContent { opacity: 0; transform: scale(.94) translateY(16px); transition: opacity .3s ease, transform .3s cubic-bezier(.16,1,.3,1); }
    #modalFormError.show #modalFormErrorOverlay { opacity: 1; }
    #modalFormError.show #modalFormErrorContent { opacity: 1; transform: scale(1) translateY(0); }

    #modalFormErrorContent {
        max-width: 340px;
        border-top: 3px solid #ef4444;
        border-radius: 24px;
    }

    .mfe-close {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: #f8fafc;
        color: #94a3b8;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .mfe-close:hover { background: #f1f5f9; color: #475569; }

    @keyframes pulse-error {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.35); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 12px rgba(239, 68, 68, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .animate-pulse-error { animation: pulse-error 2s infinite; }

    .mfe-list-wrap {
        background: #FEF2F2;
        border: 1px solid #FECACA;
        border-radius: 14px;
        padding: 4px 12px;
        text-align: left;
        max-height: 140px;
        overflow-y: auto;
    }

    .mfe-list-wrap::-webkit-scrollbar { width: 5px; }
    .mfe-list-wrap::-webkit-scrollbar-track { background: transparent; }
    .mfe-list-wrap::-webkit-scrollbar-thumb { background: #fca5a5; border-radius: 10px; }

    .mfe-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .mfe-list li {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        font-size: 0.74rem;
        font-weight: 600;
        color: #b91c1c;
        line-height: 1.55;
        padding: 7px 0;
        border-bottom: 1px dashed #fecaca;
    }

    .mfe-list li:last-child { border-bottom: none; }
    .mfe-list li i { font-size: 0.85rem; margin-top: 1px; flex-shrink: 0; color: #ef4444; }
</style>

<div id="modalFormError" class="fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-6" onclick="if(event.target===this) closeFormErrorModal()">
    <div id="modalFormErrorOverlay" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

    <div id="modalFormErrorContent" class="relative bg-white shadow-2xl border border-slate-100 z-10 text-center mx-2 w-full p-6">

        <button onclick="closeFormErrorModal()" class="mfe-close">
            <i class="bi bi-x-lg"></i>
        </button>

        <div class="w-12 h-12 bg-red-50 text-red-500 border border-red-100 text-xl rounded-2xl flex items-center justify-center mx-auto mb-3 animate-pulse-error">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>

        <h5 class="font-extrabold text-base text-slate-900 mb-1.5">
            {{ $errors->count() > 1 ? $errors->count() . ' isian perlu diperiksa' : '' }}
        </h5>
        <p class="text-[11.5px] text-slate-500 leading-relaxed px-1 mb-3">
            Mohon periksa kembali data pada formulir sebelum menyimpan.
        </p>

        <div class="mfe-list-wrap mb-4">
            <ul class="mfe-list">
                @foreach ($errors->all() as $message)
                    <li>
                        <i class="bi bi-dot"></i>
                        <span>{{ $message }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <button onclick="closeFormErrorModal()" class="w-full bg-primary hover:bg-primary-dark text-white py-2.5 rounded-xl font-bold transition text-[13px]">
            Mengerti, saya perbaiki
        </button>
    </div>
</div>

<script>
    function openFormErrorModal() {
        const modal = document.getElementById('modalFormError');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function closeFormErrorModal() {
        const modal = document.getElementById('modalFormError');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 300);
    }

    // Otomatis terbuka begitu halaman dimuat, karena kemunculannya
    // artinya form baru saja di-submit dan gagal validasi.
    document.addEventListener('DOMContentLoaded', openFormErrorModal);
</script>
@endif