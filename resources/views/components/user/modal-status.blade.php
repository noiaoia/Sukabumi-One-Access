<div id="modalCekStatus" class="fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-6">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="toggleModal()"></div>
    <div class="relative bg-white rounded-3xl p-5 sm:p-8 w-full max-w-lg shadow-2xl border border-slate-100 z-10 mx-2">
        <div class="flex justify-between items-center mb-5 sm:mb-6">
            <h5 class="font-bold text-lg sm:text-xl text-primary">Status berkas Anda</h5>
            <button onclick="toggleModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                <i class="bi bi-x-lg text-xs sm:text-sm"></i>
            </button>
        </div>

        @if(isset($berkasAktif) && $berkasAktif)
            <div class="space-y-6 pt-2">
                <div class="bg-sky-50 text-primary px-4 py-2.5 rounded-xl text-xs font-semibold flex justify-between items-center">
                    <span>Total pengajuan berjalan:</span>
                    <span class="bg-primary text-white px-2.5 py-0.5 rounded-md font-bold text-[11px]">{{ $jumlahPengajuan ?? 1 }}</span>
                </div>
                {{-- ... Sisa kode progress bar kamu ... --}}
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-truck text-accent text-lg"></i>
                        <span class="text-xs font-bold text-slate-800">ADM-2026-0012</span>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 px-2.5 py-1 rounded-md">Pengajuan KK Mandiri</span>
                </div>
                <div class="relative flex items-center justify-between w-full px-2 pt-4">
                    <div class="absolute left-0 right-0 top-1/2 -translate-y-5 h-1 bg-slate-100 z-0 mx-8 rounded-full">
                        <div class="h-full bg-accent rounded-full" style="width: 50%;"></div>
                    </div>
                    <div class="relative z-10 flex flex-col items-center flex-1">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs bg-accent text-white ring-4 ring-amber-100"><i class="bi bi-file-earmark-text"></i></div>
                        <span class="text-[10px] font-extrabold mt-2 text-accent">Diajukan</span>
                    </div>
                    <div class="relative z-10 flex flex-col items-center flex-1">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs bg-accent text-white ring-4 ring-amber-100"><i class="bi bi-gear-fill"></i></div>
                        <span class="text-[10px] font-extrabold mt-2 text-accent">Diproses</span>
                    </div>
                    <div class="relative z-10 flex flex-col items-center flex-1">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs bg-slate-200 text-slate-500"><i class="bi bi-check-all"></i></div>
                        <span class="text-[10px] font-extrabold mt-2 text-slate-400">Selesai</span>
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <div class="w-16 h-16 bg-slate-50 text-slate-400 border border-slate-100 text-3xl rounded-2xl flex items-center justify-center mb-4">
                    <i class="bi bi-folder-x"></i>
                </div>
                <h6 class="font-bold text-slate-800 text-base mb-1">Belum ada pengajuan</h6>
                <p class="text-xs text-slate-400 max-w-[280px] leading-relaxed mb-6">Anda belum mengirimkan berkas permohonan layanan mandiri apapun ke sistem kedinasan.</p>
                <div class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 flex justify-between items-center text-xs">
                    <span class="text-slate-500 font-medium">Jumlah pengajuan aktif</span>
                    <span class="font-extrabold text-slate-800 bg-slate-200 px-3 py-1 rounded-lg text-[11px]">{{ $jumlahPengajuan ?? 0 }}</span>
                </div>
            </div>
        @endif

        <div class="mt-6 flex gap-3">
            <a href="{{ route('home') }}" class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-600 py-3 rounded-xl font-bold transition text-xs">Kembali ke home</a>
            <button onclick="toggleModal()" class="flex-1 bg-primary hover:bg-primary-dark text-white py-3 rounded-xl font-bold transition text-xs">Tutup status</button>
        </div>
    </div>
</div>