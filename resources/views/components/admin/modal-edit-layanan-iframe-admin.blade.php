@once
<style>
    /* 1. ANIMATION KEYFRAMES (Disamakan dengan gaya Instansi) */
    @keyframes editSvcOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes editSvcOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes editSvcSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.01) translateY(-4px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes editSvcSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.96) translateY(10px); }
    }

    /* 2. OVERLAY */
    .edit-svc-overlay {
        position: fixed; inset: 0;
        background: rgba(10, 30, 51, 0.75);
        backdrop-filter: blur(5px);
        display: none; align-items: center; justify-content: center;
        z-index: 9999; padding: 15px;
        opacity: 0;
    }
    .edit-svc-overlay.is-active { display: flex; animation: editSvcOverlayIn 0.3s ease forwards; }
    .edit-svc-overlay.is-active .edit-svc-card { animation: editSvcSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .edit-svc-overlay.is-closing { animation: editSvcOverlayOut 0.3s ease forwards; }
    .edit-svc-overlay.is-closing .edit-svc-card { animation: editSvcSpringOut 0.3s ease forwards; }

    /* 3. CARD — Mengikuti visual edit-ins-card */
    .edit-svc-card {
        background: #ffffff;
        width: 100%; max-width: 380px;
        max-height: 80vh;
        border-radius: 22px; overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        display: flex; flex-direction: column;
    }

    .edit-svc-header {
        background: #0A1E33;
        padding: 18px 24px;
        border-bottom: 4px solid #E8A33D;
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px;
        flex-shrink: 0;
    }
    .edit-svc-header-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .edit-svc-icon-box {
        width: 32px; height: 32px; border-radius: 9px;
        background: rgba(255,255,255,0.1);
        display: flex; align-items: center; justify-content: center;
        color: #E8A33D; font-size: 15px; flex-shrink: 0;
    }
    .edit-svc-header h2 { color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; letter-spacing: 0.3px; }
    .edit-svc-header p { color: rgba(255,255,255,0.5); font-size: 10.5px; margin: 2px 0 0; }
    .edit-svc-close {
        width: 30px; height: 30px; border-radius: 9px;
        background: rgba(255,255,255,0.06); border: none; color: #94a3b8;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; cursor: pointer;
        transition: all 0.2s;
    }
    .edit-svc-close:hover { background: rgba(239,68,68,0.15); color: #f87171; }

    /* Body scrollable */
    .edit-svc-body-scroll { overflow-y: auto; flex: 1; }
    .edit-svc-body-scroll::-webkit-scrollbar { width: 5px; }
    .edit-svc-body-scroll::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }

    .edit-svc-section { padding: 20px 24px; }
    .edit-svc-section + .edit-svc-section { border-top: 1px solid #F1F5F9; }

    .edit-svc-section-heading { display: flex; align-items: center; gap: 9px; margin-bottom: 16px; }
    .edit-svc-section-number {
        width: 22px; height: 22px; border-radius: 7px;
        background: #123457; color: #E8A33D;
        font-size: 10px; font-weight: 900;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .edit-svc-section-heading h3 { font-size: 12px; font-weight: 800; color: #123457; margin: 0; }
    .edit-svc-section-heading p { font-size: 10px; color: #94a3b8; margin: 1px 0 0; }

    /* Fields */
    .edit-svc-field { margin-bottom: 14px; }
    .edit-svc-label { display: block; font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; color: #64748b; margin-bottom: 6px; margin-left: 2px; }
    .edit-svc-input-group { position: relative; display: flex; align-items: center; width: 100%; }
    .edit-svc-input {
        width: 100%; padding: 10px 14px 10px 40px;
        background: #F8FAFC; border: 1.2px solid #E2E8F0; border-radius: 12px;
        font-size: 12.5px; font-weight: 700; color: #123457;
        transition: all 0.2s; font-family: inherit;
    }
    .edit-svc-input:focus { outline: none; border-color: #123457; background: #fff; box-shadow: 0 0 0 3px rgba(18, 52, 87, 0.06); }
    .edit-svc-input-icon { position: absolute; left: 14px; color: #94a3b8; font-size: 1rem; z-index: 5; pointer-events: none; }
    
    .edit-svc-input-color { padding: 6px 10px 6px 40px !important; height: 42px; cursor: pointer; }

    /* Icon Picker Style */
    .edit-svc-icon-picker-wrap { position: relative; }
    .edit-svc-icon-picker-trigger {
        width: 100%; display: flex; align-items: center; gap: 10px;
        padding: 9px 14px; background: #F8FAFC; border: 1.2px solid #E2E8F0; border-radius: 12px;
        cursor: pointer; transition: all 0.25s ease;
    }
    .edit-svc-icon-picker-trigger:hover { border-color: #E8A33D; }
    .edit-svc-icon-picker-trigger.is-open { border-color: #123457; box-shadow: 0 0 0 3px rgba(18, 52, 87, 0.06); background: #fff; }
    .edit-svc-icon-picker-preview {
        width: 32px; height: 32px; border-radius: 8px; background: #123457; color: #E8A33D;
        display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;
    }
    .edit-svc-icon-picker-panel {
        position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 30;
        background: #fff; border: 1.2px solid #E2E8F0; border-radius: 14px;
        box-shadow: 0 20px 40px rgba(10, 30, 51, 0.14); padding: 12px; display: none;
    }
    .edit-svc-icon-picker-panel.is-open { display: block; }
    .edit-svc-icon-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 7px; max-height: 190px; overflow-y: auto; }
    .edit-svc-icon-item {
        aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center;
        background: #F8FAFC; border-radius: 9px; border: 1px solid #E2E8F0; cursor: pointer;
        transition: all 0.2s; font-size: 1rem; color: #94a3b8;
    }
    .edit-svc-icon-item:hover { border-color: #E8A33D; color: #E8A33D; transform: scale(1.06); }
    .edit-svc-icon-item.active { background: #123457; border-color: #123457; color: #E8A33D; box-shadow: 0 4px 10px rgba(18, 52, 87, 0.2); }

    /* Footer */
    .edit-svc-footer {
        padding: 14px 24px; background: #F8FAFC; border-top: 1px solid #e2e8f0;
        display: flex; justify-content: flex-end; gap: 10px; flex-shrink: 0;
    }
    .edit-svc-btn-cancel {
        padding: 10px 20px; border-radius: 10px; font-size: 11px; font-weight: 700; color: #475569;
        background: #e2e8f0; border: 1px solid #cbd5e1; cursor: pointer; transition: all 0.2s;
    }
    .edit-svc-btn-cancel:hover { background: #d1d5db; color: #1e293b; }
    .edit-svc-btn-save {
        padding: 10px 26px; border-radius: 10px; font-size: 11px; font-weight: 800; color: #fff;
        background: #123457; border: none; box-shadow: 0 8px 14px -3px rgba(18, 52, 87, 0.2);
        text-transform: uppercase; letter-spacing: 0.6px; cursor: pointer; transition: all 0.2s;
    }
    .edit-svc-btn-save:hover { background: #E8A33D; color: #0A1E33; }
</style>
@endonce

@php
    $svcIcons = ['bi-globe2', 'bi-window-stack', 'bi-cpu-fill', 'bi-database-fill-check', 'bi-shield-lock-fill', 'bi-cloud-check-fill', 'bi-qr-code-scan', 'bi-map-fill', 'bi-graph-up-arrow', 'bi-fingerprint', 'bi-patch-check-fill', 'bi-terminal-fill'];
@endphp

<div id="editLayananIframeModal" class="edit-svc-overlay" onclick="if(event.target === this) closeEditLayananIframeModal()">
    <div class="edit-svc-card">
        
        <div class="edit-svc-header">
            <div class="edit-svc-header-left">
                <div class="edit-svc-icon-box"><i class="bi bi-window-dock"></i></div>
                <div class="min-w-0">
                    <h2>Modifikasi Iframe</h2>
                    <p>Sesuaikan profil & link bridge layanan</p>
                </div>
            </div>
            <button type="button" class="edit-svc-close" onclick="closeEditLayananIframeModal()"><i class="bi bi-x-lg text-xs"></i></button>
        </div>

        <form id="editLayananIframeForm" method="POST" enctype="multipart/form-data" class="edit-svc-body-scroll">
            @csrf
            @method('PUT')

            <input type="hidden" name="secondary_color" value="#E8A33D">
            <input type="hidden" name="jenis" value="iframe">
            
            {{-- ========== SECTION 1: PROFIL LAYANAN ========== --}}
            <div class="edit-svc-section">
                <div class="edit-svc-section-heading">
                    <span class="edit-svc-section-number">1</span>
                    <div>
                        <h3>Profil Layanan</h3>
                        <p>Judul dan deskripsi singkat layanan</p>
                    </div>
                </div>

                <div class="edit-svc-field">
                    <label class="edit-svc-label">Judul Layanan</label>
                    <div class="edit-svc-input-group">
                        <input type="text" name="nama" id="edit_iframe_nama" class="edit-svc-input" placeholder="Contoh: Portal Data Statistik" required>
                        <i class="bi bi-tag edit-svc-input-icon"></i>
                    </div>
                </div>

                <div class="edit-svc-field">
                    <label class="edit-svc-label">Deskripsi</label>
                    <textarea name="deskripsi" id="edit_iframe_deskripsi" rows="3" class="edit-svc-input !pl-3.5" style="padding-top: 10px; height: auto;" placeholder="Berikan penjelasan singkat fungsi layanan ini..."></textarea>
                </div>
            </div>

            {{-- ========== SECTION 2: VISUAL & BRANDING ========== --}}
            <div class="edit-svc-section">
                <div class="edit-svc-section-heading">
                    <span class="edit-svc-section-number">2</span>
                    <div>
                        <h3>Visual & Branding</h3>
                        <p>Warna aksen dan ikon identitas</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 md:gap-x-4">
                    <div class="edit-svc-field">
                        <label class="edit-svc-label">Warna Identitas</label>
                        <div class="edit-svc-input-group">
                            <input type="color" name="primary_color" id="edit_iframe_primary_color" class="edit-svc-input edit-svc-input-color">
                            <i class="bi bi-palette edit-svc-input-icon"></i>
                        </div>
                    </div>

                    <div class="edit-svc-field">
                        <label class="edit-svc-label">Ikon Badge</label>
                        <input type="hidden" name="icon" id="edit_iframe_icon_value">
                        <div class="edit-svc-icon-picker-wrap">
                            <button type="button" class="edit-svc-icon-picker-trigger" id="editSvcIconPickerTrigger" onclick="toggleEditIframeIconPicker()">
                                <span class="edit-svc-icon-picker-preview" id="edit_iframe_icon_preview">
                                    <i class="bi bi-globe2"></i>
                                </span>
                                <span class="text-[11px] font-bold text-slate-500 flex-1 text-left">Pilih Ikon</span>
                                <i class="bi bi-chevron-down text-slate-400 text-xs" id="editSvcIconPickerChevron"></i>
                            </button>

                            <div class="edit-svc-icon-picker-panel" id="edit_iframe_icon_panel">
                                <div class="edit-svc-icon-grid">
                                    @foreach($svcIcons as $icon)
                                        <div class="edit-svc-icon-item" data-icon="{{ $icon }}" onclick="pickEditIframeIcon('{{ $icon }}', this)">
                                            <i class="bi {{ $icon }}"></i>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== SECTION 3: KONFIGURASI LINK ========== --}}
            <div class="edit-svc-section">
                <div class="edit-svc-section-heading">
                    <span class="edit-svc-section-number">3</span>
                    <div>
                        <h3>Konfigurasi Link</h3>
                        <p>Tautan target yang akan dibuka di iframe</p>
                    </div>
                </div>

                <div class="edit-svc-field mb-0">
                    <label class="edit-svc-label">URL Bridge Iframe</label>
                    <div class="edit-svc-input-group">
                        <input type="url" name="iframe_url" id="edit_iframe_url" class="edit-svc-input" placeholder="https://aplikasi-internal.go.id" required>
                        <i class="bi bi-link-45deg edit-svc-input-icon"></i>
                    </div>
                </div>
            </div>
        </form>

        <div class="edit-svc-footer">
            <button type="button" class="edit-svc-btn-cancel" onclick="closeEditLayananIframeModal()">Batal</button>
            <button type="submit" form="editLayananIframeForm" class="edit-svc-btn-save">Simpan Perubahan</button>
        </div>
    </div>
</div>

@once
<script>
    function toggleEditIframeIconPicker() {
        const panel = document.getElementById('edit_iframe_icon_panel');
        const trigger = document.getElementById('editSvcIconPickerTrigger');
        const chevron = document.getElementById('editSvcIconPickerChevron');
        
        panel.classList.toggle('is-open');
        trigger.classList.toggle('is-open');
        chevron.classList.toggle('bi-chevron-down');
        chevron.classList.toggle('bi-chevron-up');
    }

    function pickEditIframeIcon(icon, el) {
        document.getElementById('edit_iframe_icon_value').value = icon;
        document.getElementById('edit_iframe_icon_preview').innerHTML = `<i class="bi ${icon}"></i>`;
        document.querySelectorAll('.edit-svc-icon-item').forEach(i => i.classList.remove('active'));
        el.classList.add('active');
        toggleEditIframeIconPicker();
    }

    function openEditLayananIframeModal(data, updateUrl) {
        const modal = document.getElementById('editLayananIframeModal');
        document.getElementById('editLayananIframeForm').action = updateUrl;

        document.getElementById('edit_iframe_nama').value = data.nama || "";
        document.getElementById('edit_iframe_deskripsi').value = data.deskripsi || "";
        document.getElementById('edit_iframe_primary_color').value = data.primary_color || "#123457";
        document.getElementById('edit_iframe_url').value = data.iframe_url || "";

        const icon = data.icon || 'bi-globe2';
        document.getElementById('edit_iframe_icon_value').value = icon;
        document.getElementById('edit_iframe_icon_preview').innerHTML = `<i class="bi ${icon}"></i>`;
        document.querySelectorAll('.edit-svc-icon-item').forEach(el => {
            el.classList.toggle('active', el.dataset.icon === icon);
        });

        modal.classList.remove('is-closing');
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function closeEditLayananIframeModal() {
        const modal = document.getElementById('editLayananIframeModal');
        if(modal) {
            modal.classList.add('is-closing');
            setTimeout(() => {
                modal.classList.remove('is-active');
                modal.classList.remove('is-closing');
                document.body.style.overflow = 'auto';
            }, 300);
        }
    }

    // Klik luar untuk tutup picker
    document.addEventListener('click', function(e) {
        const wrap = document.querySelector('.edit-svc-icon-picker-wrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('edit_iframe_icon_panel').classList.remove('is-open');
            document.getElementById('editSvcIconPickerTrigger').classList.remove('is-open');
            const chevron = document.getElementById('editSvcIconPickerChevron');
            if(chevron) {
                chevron.classList.add('bi-chevron-down');
                chevron.classList.remove('bi-chevron-up');
            }
        }
    });

    // ESC to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeEditLayananIframeModal();
    });

    // Loading State
    document.getElementById('editLayananIframeForm').addEventListener('submit', function() {
        const btn = this.closest('.edit-svc-card').querySelector('.edit-svc-btn-save');
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-1"></i> Menyimpan...';
        btn.style.opacity = '0.8';
        btn.style.pointerEvents = 'none';
        btn.disabled = true;
    });

    document.getElementById('edit_iframe_primary_color').addEventListener('input', function () {
        document.getElementById('edit_iframe_icon_preview').style.background = this.value;
    });
</script>
@endonce