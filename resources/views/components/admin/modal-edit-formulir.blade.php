{{-- resources/views/components/admin/modal-edit-form.blade.php --}}
@once
<style>
    /* 1. ANIMATION KEYFRAMES */
    @keyframes editFormOverlayIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes editFormOverlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes editFormSpringIn {
        0% { opacity: 0; transform: scale(0.95) translateY(20px); }
        70% { transform: scale(1.01) translateY(-4px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes editFormSpringOut {
        from { opacity: 1; transform: scale(1) translateY(0); }
        to { opacity: 0; transform: scale(0.96) translateY(10px); }
    }

    /* 2. OVERLAY */
    .edit-form-overlay {
        position: fixed; inset: 0;
        background: rgba(10, 30, 51, 0.75);
        backdrop-filter: blur(5px);
        display: none; align-items: center; justify-content: center;
        z-index: 9999; padding: 15px;
        opacity: 0;
    }
    .edit-form-overlay.is-active { display: flex; animation: editFormOverlayIn 0.3s ease forwards; }
    .edit-form-overlay.is-active .edit-form-card { animation: editFormSpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .edit-form-overlay.is-closing { animation: editFormOverlayOut 0.3s ease forwards; }
    .edit-form-overlay.is-closing .edit-form-card { animation: editFormSpringOut 0.3s ease forwards; }

    /* 3. CARD */
    .edit-form-card {
        background: #ffffff;
        width: 100%; max-width: 400px;
        max-height: 80vh;
        border-radius: 24px; overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        display: flex; flex-direction: column;
    }

    .edit-form-header {
        background: #0A1E33;
        padding: 20px 24px;
        border-bottom: 4px solid #E8A33D; /* Warna Accent Gold */
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px;
        flex-shrink: 0;
    }
    .edit-form-header-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .edit-form-icon-box {
        width: 36px; height: 36px; border-radius: 10px;
        background: rgba(232, 163, 61, 0.15);
        display: flex; align-items: center; justify-content: center;
        color: #E8A33D; font-size: 16px; flex-shrink: 0;
    }
    .edit-form-header h2 { color: #ffffff; font-size: 15px; font-weight: 800; margin: 0; letter-spacing: 0.3px; text-transform: uppercase; }
    .edit-form-header p { color: rgba(255,255,255,0.5); font-size: 10px; margin: 2px 0 0; font-weight: 700; text-transform: uppercase; }
    
    .edit-form-close {
        width: 30px; height: 30px; border-radius: 9px;
        background: rgba(255,255,255,0.06); border: none; color: #94a3b8;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; cursor: pointer;
        transition: all 0.2s;
    }
    .edit-form-close:hover { background: rgba(232,163,61,0.2); color: #E8A33D; }

    /* 4. BODY */
    .edit-form-body-scroll { overflow-y: auto; flex: 1; background: #fff; }
    .edit-form-body-scroll::-webkit-scrollbar { width: 5px; }
    .edit-form-body-scroll::-webkit-scrollbar-thumb { background: #E8A33D; border-radius: 10px; }

    .edit-form-section { padding: 28px; }

    .edit-form-section-heading { display: flex; align-items: center; gap: 9px; margin-bottom: 22px; }
    .edit-form-section-number {
        width: 24px; height: 24px; border-radius: 7px;
        background: #123457; color: #E8A33D;
        font-size: 10px; font-weight: 900;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .edit-form-section-heading h3 { font-size: 12px; font-weight: 800; color: #123457; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Fields */
    .edit-form-field { margin-bottom: 20px; }
    .edit-form-label { display: block; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-bottom: 8px; margin-left: 4px; }
    .edit-form-input-group { position: relative; display: flex; align-items: center; width: 100%; }
    .edit-form-input {
        width: 100%; padding: 14px 16px 14px 48px;
        background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px;
        font-size: 13px; font-weight: 700; color: #123457;
        transition: all 0.3s; font-family: inherit;
    }
    .edit-form-input:focus { outline: none; border-color: #123457; background: #fff; box-shadow: 0 0 0 4px rgba(18, 52, 87, 0.05); }
    .edit-form-input-icon { position: absolute; left: 18px; color: #94a3b8; font-size: 1.1rem; z-index: 5; pointer-events: none; }

    /* 4. CUSTOM FILE UPLOAD */
    .edit-file-wrapper { position: relative; width: 100%; cursor: pointer; }
    .edit-file-box { display: flex; align-items: center; gap: 12px; padding: 10px 16px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; transition: 0.3s; height: 52px; }
    .edit-file-wrapper:hover .edit-file-box { border-color: #E8A33D; background: #fff; }
    .edit-file-icon { width: 34px; height: 34px; border-radius: 10px; background: #123457; color: #E8A33D; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .edit-file-text { font-size: 11px; font-weight: 700; color: #94a3b8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .edit-file-input-real { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10; }

    /* 5. FOOTER */
    .edit-form-footer {
        padding: 20px 28px; background: #F8FAFC; border-top: 1px solid #e2e8f0;
        display: flex; justify-content: flex-end; gap: 12px; flex-shrink: 0;
    }
    .edit-form-btn-cancel {
        padding: 12px 24px; border-radius: 12px; font-size: 11px; font-weight: 700; color: #475569;
        background: #e2e8f0; border: 1px solid #cbd5e1; cursor: pointer; transition: 0.3s; text-transform: uppercase;
    }
    .edit-form-btn-cancel:hover { background: #d1d5db; transform: translateY(-1px); }
    
    .edit-form-btn-save {
        padding: 12px 28px; border-radius: 12px; font-size: 11px; font-weight: 800; color: #fff;
        background: #123457; border: none; box-shadow: 0 10px 15px -3px rgba(18, 52, 87, 0.2);
        text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: 0.3s;
    }
    .edit-form-btn-save:hover { background: #E8A33D; color: #0A1E33; transform: translateY(-2px); box-shadow: 0 12px 20px rgba(232, 163, 61, 0.25); }
</style>
@endonce

<div id="openEditFormModal" class="edit-form-overlay" onclick="if(event.target === this) closeEditFormModal()">
    <div class="edit-form-card">
        
        <div class="edit-form-header">
            <div class="edit-form-header-left">
                <div class="edit-form-icon-box"><i class="bi bi-pencil-square"></i></div>
                <div class="min-w-0">
                    <h2>Ubah Identitas</h2>
                    <p id="edit_form_context">Formulir Mandiri</p>
                </div>
            </div>
            <button type="button" class="edit-form-close" onclick="closeEditFormModal()"><i class="bi bi-x-lg text-xs"></i></button>
        </div>

        <form id="editFormLayananAction" method="POST" enctype="multipart/form-data" class="edit-form-body-scroll">
            @csrf
            @method('PUT')
            
            <div class="edit-form-section">
                <div class="edit-form-section-heading">
                    <span class="edit-form-section-number">1</span>
                    <h3>Informasi Dasar</h3>
                </div>

                {{-- FIELD NAMA --}}
                <div class="edit-form-field">
                    <label class="edit-form-label">Nama / Judul Formulir</label>
                    <div class="edit-form-input-group">
                        <input type="text" name="nama" id="edit_form_nama" class="edit-form-input" placeholder="Masukkan nama layanan..." required>
                        <i class="bi bi-tag edit-form-input-icon"></i>
                    </div>
                </div>

                {{-- FIELD DESKRIPSI --}}
                <div class="edit-form-field">
                    <label class="edit-form-label">Deskripsi Singkat</label>
                    <div class="edit-form-input-group">
                        <textarea name="deskripsi" id="edit_form_deskripsi" rows="4" class="edit-form-input !pl-12" style="padding-top: 14px; height: auto;" placeholder="Tuliskan keterangan singkat..." required></textarea>
                        <i class="bi bi-chat-left-text edit-form-input-icon" style="top: 18px; transform: none;"></i>
                    </div>
                </div>

                {{-- IMAGE --}}
                <div class="edit-form-field">
                    <label class="edit-form-label">Foto Sampul (Thumbnail)</label>
                    <div class="edit-file-wrapper">
                        <input type="file" name="cover" id="edit_form_cover" class="edit-file-input-real" accept="image/*">
                        <div class="edit-file-box">
                            <div class="edit-file-icon"><i class="bi bi-image"></i></div>
                            <span class="edit-file-text" id="edit_file_name_display">Ganti foto sampul...</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="edit-form-footer">
            <button type="button" class="edit-form-btn-cancel" onclick="closeEditFormModal()">Batal</button>
            <button type="submit" form="editFormLayananAction" class="edit-form-btn-save" id="btnEditFormSubmit">Simpan Perubahan</button>
        </div>
    </div>
</div>

@once
<script>
    /**
     * Membuka Modal Edit Formulir
     * @param {Object} button - Element tombol yang diklik
     */
    function openEditFormModal(button) {
        const data = JSON.parse(button.getAttribute('data-json'));
        const updateUrl = button.getAttribute('data-url');
        const baseUrl = window.location.origin;
        
        const modal = document.getElementById('openEditFormModal');
        document.getElementById('editFormLayananAction').action = updateUrl;

        // Reset Input File
        document.getElementById('edit_form_cover').value = "";
        document.getElementById('edit_file_name_display').innerText = "Ganti foto sampul...";

        // Isi Data Teks
        document.getElementById('edit_form_nama').value = data.nama || "";
        document.getElementById('edit_form_deskripsi').value = data.deskripsi || "";
        document.getElementById('edit_form_context').innerText = "Edit " + (data.nama || "Formulir");

        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    // Logic Live Preview saat pilih file baru
    document.getElementById('edit_form_cover').onchange = function() {
        const [file] = this.files;
        if (file) {
            document.getElementById('edit_file_name_display').innerText = file.name;
        }
    };

    /**
     * Menutup Modal dengan Animasi
     */
    function closeEditFormModal() {
        const modal = document.getElementById('openEditFormModal');
        if(modal) {
            modal.classList.add('is-closing');
            setTimeout(() => {
                modal.classList.remove('is-active', 'is-closing');
                document.body.style.overflow = 'auto';
            }, 300);
        }
    }

    // Listener tombol ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeEditFormModal();
    });

    // Handle Loading State saat Submit
    document.getElementById('editFormLayananAction').addEventListener('submit', function() {
        const btn = document.getElementById('btnEditFormSubmit');
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memproses...';
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.7';
    });
</script>
@endonce