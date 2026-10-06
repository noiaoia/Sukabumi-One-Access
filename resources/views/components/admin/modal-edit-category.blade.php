{{-- resources/views/components/admin/modal-edit-category.blade.php --}}
@props(['icons', 'portalCode'])

@once
<style>
    /* 1. ANIMATION KEYFRAMES */
    @keyframes editCatOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes editCatOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes editCatSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.01) translateY(-4px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes editCatSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.96) translateY(10px); }
    }

    /* 2. OVERLAY */
    .edit-cat-overlay {
        position: fixed; inset: 0;
        background: rgba(10, 30, 51, 0.75);
        backdrop-filter: blur(5px);
        display: none; align-items: center; justify-content: center;
        z-index: 9999; padding: 15px;
        opacity: 0;
    }
    .edit-cat-overlay.is-active { display: flex; animation: editCatOverlayIn 0.3s ease forwards; }
    .edit-cat-overlay.is-active .edit-cat-card { animation: editCatSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .edit-cat-overlay.is-closing { animation: editCatOverlayOut 0.3s ease forwards; }
    .edit-cat-overlay.is-closing .edit-cat-card { animation: editCatSpringOut 0.3s ease forwards; }

    /* 3. CARD */
    .edit-cat-card {
        background: #ffffff;
        width: 100%; max-width: 380px;
        max-height: 80vh;
        border-radius: 24px; overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        display: flex; flex-direction: column;
    }

    .edit-cat-header {
        background: #0A1E33;
        padding: 20px 24px;
        border-bottom: 4px solid #E8A33D;
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px;
        flex-shrink: 0;
    }
    .edit-cat-header-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .edit-cat-icon-box {
        width: 36px; height: 36px; border-radius: 10px;
        background: rgba(232, 163, 61, 0.15);
        display: flex; align-items: center; justify-content: center;
        color: #E8A33D; font-size: 16px; flex-shrink: 0;
    }
    .edit-cat-header h2 { color: #ffffff; font-size: 15px; font-weight: 800; margin: 0; letter-spacing: 0.3px; text-transform: uppercase; }
    .edit-cat-header p { color: rgba(255,255,255,0.5); font-size: 10px; margin: 2px 0 0; font-weight: 700; text-transform: uppercase; }
    
    .edit-cat-close {
        width: 30px; height: 30px; border-radius: 9px;
        background: rgba(255,255,255,0.06); border: none; color: #94a3b8;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; cursor: pointer;
        transition: all 0.2s;
    }
    .edit-cat-close:hover { background: rgba(232,163,61,0.2); color: #E8A33D; }

    /* 4. BODY */
    .edit-cat-body-scroll { overflow-y: auto; flex: 1; background: #fff; }
    .edit-cat-body-scroll::-webkit-scrollbar { width: 5px; }
    .edit-cat-body-scroll::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }

    .edit-cat-section { padding: 28px; }

    .edit-cat-section-heading { display: flex; align-items: center; gap: 9px; margin-bottom: 22px; }
    .edit-cat-section-number {
        width: 24px; height: 24px; border-radius: 7px;
        background: #123457; color: #E8A33D;
        font-size: 10px; font-weight: 900;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .edit-cat-section-heading h3 { font-size: 12px; font-weight: 800; color: #123457; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Fields */
    .edit-cat-field { margin-bottom: 20px; }
    .edit-cat-label { display: block; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-bottom: 8px; margin-left: 4px; }
    .edit-cat-input-group { position: relative; display: flex; align-items: center; width: 100%; }
    .edit-cat-input {
        width: 100%; padding: 14px 16px 14px 48px;
        background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px;
        font-size: 13px; font-weight: 700; color: #123457;
        transition: all 0.3s; font-family: inherit;
    }
    .edit-cat-input:focus { outline: none; border-color: #123457; background: #fff; box-shadow: 0 0 0 4px rgba(18, 52, 87, 0.05); }
    .edit-cat-input-icon { position: absolute; left: 18px; color: #94a3b8; font-size: 1.1rem; z-index: 5; pointer-events: none; }

    /* Icon Picker Style */
    .edit-cat-icon-trigger {
        width: 100%; display: flex; align-items: center; gap: 12px; padding: 10px 16px;
        background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; cursor: pointer; transition: 0.25s ease;
    }
    .edit-cat-icon-preview {
        width: 38px; height: 38px; border-radius: 10px; background: #123457; color: #E8A33D;
        display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;
    }

    /* 5. FOOTER */
    .edit-cat-footer {
        padding: 20px 28px; background: #F8FAFC; border-top: 1px solid #e2e8f0;
        display: flex; justify-content: flex-end; gap: 12px; flex-shrink: 0;
    }
    .edit-cat-btn-cancel {
        padding: 12px 24px; border-radius: 12px; font-size: 11px; font-weight: 700; color: #475569;
        background: #e2e8f0; border: 1px solid #cbd5e1; cursor: pointer; transition: 0.3s; text-transform: uppercase;
    }
    .edit-cat-btn-cancel:hover { background: #d1d5db; transform: translateY(-1px); }
    
    .edit-cat-btn-save {
        padding: 12px 28px; border-radius: 12px; font-size: 11px; font-weight: 800; color: #fff;
        background: #123457; border: none; box-shadow: 0 10px 15px -3px rgba(18, 52, 87, 0.2);
        text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: 0.3s;
    }
    .edit-cat-btn-save:hover { background: #E8A33D; color: #0A1E33; transform: translateY(-2px); box-shadow: 0 12px 20px rgba(232, 163, 61, 0.25); }

    /* Modal Ikon Picker (Z-index lebih tinggi) */
    #editCatIconPicker { position: fixed; inset: 0; z-index: 10001; display: none; align-items: center; justify-content: center; padding: 20px; }
    #editCatIconPicker.is-open { display: flex; }
    .icon-picker-backdrop { position: absolute; inset: 0; background: rgba(10, 30, 51, 0.6); backdrop-filter: blur(4px); }
    .icon-picker-card { position: relative; z-index: 2; width: 100%; max-width: 380px; background: #fff; border-radius: 24px; overflow: hidden; box-shadow: 0 30px 60px rgba(0,0,0,0.4); }
    .icon-picker-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px; padding: 20px; max-height: 300px; overflow-y: auto; }
    .icon-item { aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center; background: #F8FAFC; border-radius: 10px; border: 1px solid #E2E8F0; cursor: pointer; transition: 0.2s; font-size: 1.2rem; color: #94a3b8; }
    .icon-item:hover, .icon-item.active { background: #123457; border-color: #123457; color: #E8A33D; }
</style>
@endonce

{{-- MODAL EDIT KATEGORI --}}
<div id="editCategoryModal" class="edit-cat-overlay" onclick="if(event.target === this) closeEditCategoryModal()">
    <div class="edit-cat-card">
        
        <div class="edit-cat-header">
            <div class="edit-cat-header-left">
                <div class="edit-cat-icon-box"><i class="bi bi-pencil-square"></i></div>
                <div class="min-w-0">
                    <h2>Ubah Kategori</h2>
                    <p id="edit_cat_context">Layanan: ...</p>
                </div>
            </div>
            <button type="button" class="edit-cat-close" onclick="closeEditCategoryModal()"><i class="bi bi-x-lg text-xs"></i></button>
        </div>

        <form id="editCategoryAction" method="POST" class="edit-cat-body-scroll">
            @csrf
            @method('PUT')
            
            <div class="edit-cat-section">
                <div class="edit-cat-section-heading">
                    <span class="edit-cat-section-number">1</span>
                    <h3>Informasi Visual</h3>
                </div>

                {{-- Judul --}}
                <div class="edit-cat-field">
                    <label class="edit-cat-label">Judul Kategori</label>
                    <div class="edit-cat-input-group">
                        <input type="text" name="nama" id="edit_cat_nama" class="edit-cat-input" placeholder="Nama kategori..." required>
                        <i class="bi bi-tag edit-cat-input-icon"></i>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="edit-cat-field">
                    <label class="edit-cat-label">Deskripsi Singkat</label>
                    <div class="edit-cat-input-group">
                        <textarea name="deskripsi" id="edit_cat_deskripsi" rows="3" class="edit-cat-input !pl-12" style="padding-top: 14px; height: auto;" placeholder="Keterangan kategori..."></textarea>
                        <i class="bi bi-chat-left-text edit-cat-input-icon" style="top: 18px; transform: none;"></i>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Warna --}}
                    <div class="edit-cat-field">
                        <label class="edit-cat-label">Warna Aksen</label>
                        <div class="edit-cat-input-group">
                            <input type="color" name="primary_color" id="edit_cat_color" class="edit-cat-input !p-1.5 !h-[50px] cursor-pointer">
                            <i class="bi bi-palette edit-cat-input-icon"></i>
                        </div>
                    </div>

                    {{-- Ikon --}}
                    <div class="edit-cat-field">
                        <label class="edit-cat-label">Simbol Ikon</label>
                        <input type="hidden" name="icon" id="edit_cat_icon_value">
                        <button type="button" class="edit-cat-icon-trigger" onclick="openEditCatIconPicker()">
                            <span class="edit-cat-icon-preview" id="edit_cat_icon_preview"><i class="bi bi-geo-fill"></i></span>
                            <i class="bi bi-chevron-down text-slate-400 text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div class="edit-cat-footer">
            <button type="button" class="edit-cat-btn-cancel" onclick="closeEditCategoryModal()">Batal</button>
            <button type="submit" form="editCategoryAction" class="edit-cat-btn-save" id="btnEditCatSubmit">Simpan Kategori</button>
        </div>
    </div>
</div>

{{-- MODAL IKON PICKER KHUSUS EDIT --}}
<div id="editCatIconPicker">
    <div class="icon-picker-backdrop" onclick="closeEditCatIconPicker()"></div>
    <div class="icon-picker-card">
        <div class="edit-cat-header">
            <h2 class="text-white text-xs font-black uppercase m-0">Pilih Ikon</h2>
            <button type="button" class="edit-cat-close" onclick="closeEditCatIconPicker()"><i class="bi bi-x-lg text-xs"></i></button>
        </div>
        <div class="icon-picker-grid">
            @foreach($icons as $iconItem)
                <div class="icon-item" data-icon="{{ $iconItem }}" onclick="selectEditCatIcon('{{ $iconItem }}', this)">
                    <i class="bi {{ $iconItem }}"></i>
                </div>
            @endforeach
        </div>
    </div>
</div>

@once
<script>
    /**
     * Membuka Modal Edit Kategori
     * data: { id, title, desc, icon, color_hex }
     */
    function openEditCategoryModal(data, updateUrl, serviceName) {
        const modal = document.getElementById('editCategoryModal');
        const form = document.getElementById('editCategoryAction');
        form.action = updateUrl;

        // Fill Fields
        document.getElementById('edit_cat_nama').value = data.title || "";
        document.getElementById('edit_cat_deskripsi').value = data.desc || "";
        document.getElementById('edit_cat_color').value = data.color_hex || "#E8A33D";
        document.getElementById('edit_cat_icon_value').value = data.icon || "bi-geo-fill";
        document.getElementById('edit_cat_icon_preview').innerHTML = `<i class="bi ${data.icon || 'bi-geo-fill'}"></i>`;
        
        document.getElementById('edit_cat_context').innerText = "Formulir: " + serviceName;

        // Mark active icon in picker
        document.querySelectorAll('#editCatIconPicker .icon-item').forEach(el => {
            el.classList.toggle('active', el.dataset.icon === data.icon);
        });

        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function closeEditCategoryModal() {
        const modal = document.getElementById('editCategoryModal');
        if(modal) {
            modal.classList.add('is-closing');
            setTimeout(() => {
                modal.classList.remove('is-active', 'is-closing');
                document.body.style.overflow = 'auto';
            }, 300);
        }
    }

    // ICON PICKER LOGIC
    function openEditCatIconPicker() { document.getElementById('editCatIconPicker').classList.add('is-open'); }
    function closeEditCatIconPicker() { document.getElementById('editCatIconPicker').classList.remove('is-open'); }
    
    function selectEditCatIcon(iconName, element) {
        document.getElementById('edit_cat_icon_value').value = iconName;
        document.getElementById('edit_cat_icon_preview').innerHTML = `<i class="bi ${iconName}"></i>`;
        
        document.querySelectorAll('#editCatIconPicker .icon-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        
        closeEditCatIconPicker();
    }

    // ESC to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if(document.getElementById('editCatIconPicker').classList.contains('is-open')) {
                closeEditCatIconPicker();
            } else {
                closeEditCategoryModal();
            }
        }
    });

    // Loading State
    document.getElementById('editCategoryAction').addEventListener('submit', function() {
        const btn = document.getElementById('btnEditCatSubmit');
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses...';
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.7';
    });
</script>
@endonce