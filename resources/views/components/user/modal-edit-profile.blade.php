@auth
<!-- Modal Edit Profil (Wide & Compact Premium) -->
<div id="modalEditProfile" class="soa-modal-overlay">
    <div class="soa-modal-container max-w-[460px]">
        <!-- Handle khusus Mobile (Indikator bisa ditarik) -->
        <div class="soa-mobile-handle"></div>

        <!-- HEADER -->
        <div class="sep-header">
            <div class="sep-header-icon"><i class="bi bi-person-vcard"></i></div>
            <div class="sep-header-text">
                <h3>Perbarui Profil</h3>
                <p>Data ini digunakan untuk proses layanan kedinasan Anda.</p>
            </div>
            <button type="button" onclick="closeEditProfileModal()" class="sep-close-btn" aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formEditProfile" action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="sep-body">
                <div class="sep-form-row">
                    <!-- SISI KIRI: Avatar Section -->
                    <div class="sep-avatar-wrap">
                        <div class="sep-avatar-frame">
                            <label for="profile_photo_input" class="sep-avatar-photo">
                                @if(Auth::user()->profile_photo)
                                    <img id="imagePreview"
                                        src="{{ asset('storage/' . Auth::user()->profile_photo) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div id="imagePreview" class="sep-avatar-initial">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif

                                <div class="sep-avatar-overlay">
                                    <i class="bi bi-camera-fill"></i>
                                </div>
                            </label>
                            <label for="profile_photo_input" class="sep-avatar-fab">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            <input type="file" id="profile_photo_input" name="profile_photo" accept="image/*" class="hidden" onchange="previewProfileImage(this)">
                        </div>
                        <p class="sep-avatar-hint">JPG/PNG, maks 2MB</p>
                    </div>

                    <!-- SISI KANAN: Inputs -->
                    <div class="sep-form-grid">
                        <div class="sep-field">
                            <label class="sep-label">Nama Lengkap <span class="req">*</span></label>
                            <input type="text" name="name" value="{{ Auth::user()->name }}" required class="sep-input">
                        </div>

                        <div class="sep-grid-2">
                            <div class="sep-field">
                                <label class="sep-label">WhatsApp <span class="req">*</span></label>
                                <input type="text" name="phone" value="{{ Auth::user()->phone }}" required inputmode="numeric" maxlength="13" minlength="10" pattern="[0-9]{10,13}" class="sep-input" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)">
                            </div>
                            <div class="sep-field">
                                <label class="sep-label">Username</label>
                                <div class="sep-field-locked">
                                    <input type="text" value="{{ Auth::user()->username }}" disabled class="sep-input sep-input-disabled">
                                    <i class="bi bi-lock-fill sep-lock-icon"></i>
                                </div>
                            </div>
                        </div>

                        <div class="sep-field">
                            <label class="sep-label">Email <span class="req">*</span></label>
                            <input type="email" name="email" value="{{ Auth::user()->email }}" required class="sep-input">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="sep-footer">
                <button type="button" onclick="closeEditProfileModal()" class="sep-btn-cancel">
                    Batal
                </button>
                <button type="submit" id="btnSaveProfile" class="sep-btn-save">
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .soa-modal-overlay {
        position: fixed; inset: 0;
        background: rgba(8, 20, 35, 0);
        backdrop-filter: blur(0px);
        display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 20px;
        transition: background 0.45s cubic-bezier(0.16, 1, 0.3, 1), backdrop-filter 0.45s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .soa-modal-overlay.active {
        display: flex;
        background: rgba(8, 20, 35, 0.72);
        backdrop-filter: blur(10px);
    }

    .soa-modal-container {
        position: relative;
        background: #ffffff;
        width: 95%;
        border-radius: 26px;
        box-shadow: 0 25px 70px -15px rgba(10, 30, 51, 0.45), 0 8px 24px -8px rgba(10, 30, 51, 0.2);
        transform: scale(0.94) translateY(18px);
        opacity: 0;
        transition: all 0.45s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }

    .soa-modal-container::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #E8A33D, #f4c988, #E8A33D);
        z-index: 2;
    }

    .soa-modal-overlay.active .soa-modal-container { transform: scale(1) translateY(0); opacity: 1; }

    .soa-mobile-handle {
        display: none; width: 36px; height: 4px; background: #E2E8F0;
        border-radius: 10px; margin: 12px auto 0; flex-shrink: 0;
    }

    /* ============================= HEADER ============================= */
    .sep-header {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 20px 22px 16px; flex-shrink: 0;
        border-bottom: 1px solid #F1F5F9;
        background: linear-gradient(180deg, #FBFCFE 0%, #FFFFFF 100%);
    }
    .sep-header-icon {
        width: 38px; height: 38px; border-radius: 12px; flex-shrink: 0;
        background: linear-gradient(135deg, #0A1E33, #123457);
        color: #E8A33D; display: flex; align-items: center; justify-content: center;
        font-size: 16px; box-shadow: 0 4px 10px rgba(10, 30, 51, 0.25);
    }
    .sep-header-text { flex: 1; min-width: 0; }
    .sep-header-text h3 {
        font-size: 13.5px; font-weight: 800; color: #0A1E33;
        text-transform: uppercase; letter-spacing: 0.06em; margin: 0; line-height: 1.25;
    }
    .sep-header-text p {
        font-size: 10.5px; color: #94A3B8; font-weight: 500;
        margin: 4px 0 0; line-height: 1.45;
    }
    .sep-close-btn {
        width: 30px; height: 30px; border-radius: 10px; flex-shrink: 0;
        background: #F8FAFC; border: 1px solid #EEF2F7; color: #94A3B8;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s ease;
    }
    .sep-close-btn:hover { background: #FEF2F2; border-color: #FCA5A5; color: #EF4444; transform: rotate(90deg); }
    .sep-close-btn i { font-size: 11px; }

    /* ============================== BODY =============================== */
    .sep-body { padding: 22px; overflow-y: auto; flex: 1; }

    .sep-form-row { display: flex; flex-direction: column; gap: 22px; align-items: center; }
    @media (min-width: 640px) {
        .sep-form-row { flex-direction: row; align-items: flex-start; gap: 24px; }
    }

    /* --------------------------- AVATAR --------------------------- */
    .sep-avatar-wrap { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }
    .sep-avatar-frame { position: relative; width: 88px; height: 88px; }
    .sep-avatar-photo {
        display: block; width: 100%; height: 100%; border-radius: 20px; overflow: hidden;
        border: 3px solid rgba(232, 163, 61, 0.35); background: #F8FAFC; cursor: pointer;
        position: relative; box-shadow: 0 8px 20px -6px rgba(10, 30, 51, 0.25);
        transition: all 0.25s ease;
    }
    .sep-avatar-photo:hover { border-color: rgba(232, 163, 61, 0.65); box-shadow: 0 10px 24px -6px rgba(10, 30, 51, 0.35); }
    .sep-avatar-initial {
        width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #123457, #0A1E33); color: #E8A33D;
        font-size: 30px; font-weight: 800;
    }
    .sep-avatar-overlay {
        position: absolute; inset: 0; background: rgba(10, 30, 51, 0.55);
        opacity: 0; transition: opacity 0.25s ease;
        display: flex; align-items: center; justify-content: center; color: #fff; font-size: 15px;
    }
    .sep-avatar-photo:hover .sep-avatar-overlay { opacity: 1; }
    .sep-avatar-fab {
        position: absolute; bottom: -6px; right: -6px; width: 30px; height: 30px;
        border-radius: 10px; background: linear-gradient(135deg, #123457, #0A1E33); color: #E8A33D;
        border: 3px solid #ffffff; box-shadow: 0 4px 10px rgba(10, 30, 51, 0.35);
        display: flex; align-items: center; justify-content: center; font-size: 11px;
        cursor: pointer; transition: all 0.2s ease;
    }
    .sep-avatar-fab:hover { transform: scale(1.12); box-shadow: 0 6px 14px rgba(10, 30, 51, 0.45); }
    .sep-avatar-hint {
        font-size: 8.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;
        letter-spacing: 0.08em; margin-top: 12px; text-align: center;
    }

    /* --------------------------- FORM INPUT --------------------------- */
    .sep-form-grid { flex: 1; width: 100%; display: flex; flex-direction: column; gap: 14px; }
    .sep-field { display: flex; flex-direction: column; gap: 6px; }
    .sep-grid-2 { display: grid; grid-template-columns: 1fr; gap: 14px; }
    @media (min-width: 480px) {
        .sep-grid-2 { grid-template-columns: 1fr 1fr; gap: 12px; }
    }
    .sep-label {
        font-size: 9.5px; font-weight: 800; color: #94A3B8; text-transform: uppercase;
        letter-spacing: 0.06em; margin-left: 2px;
    }
    .sep-label .req { color: #E8A33D; margin-left: 1px; }
    .sep-input {
        width: 100%; padding: 11px 13px; background: #F8FAFC;
        border: 1.5px solid #E2E8F0; border-radius: 12px;
        font-size: 12.5px; font-weight: 700; color: #123457;
        font-family: inherit; transition: all 0.2s ease;
    }
    .sep-input::placeholder { color: #CBD5E1; font-weight: 600; }
    .sep-input:focus {
        outline: none; border-color: #E8A33D; background: #ffffff;
        box-shadow: 0 0 0 4px rgba(232, 163, 61, 0.12);
    }
    .sep-field-locked { position: relative; }
    .sep-input-disabled {
        background: #F1F5F9; border-color: #E2E8F0; color: #94A3B8;
        cursor: not-allowed; padding-right: 34px;
    }
    .sep-lock-icon {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        font-size: 10px; color: #CBD5E1; pointer-events: none;
    }

    /* ============================= FOOTER ============================== */
    .sep-footer {
        display: flex; gap: 10px; padding: 16px 22px 22px;
        border-top: 1px solid #F1F5F9; background: #FBFCFE; flex-shrink: 0;
    }
    .sep-btn-cancel {
        flex: 1; padding: 12px; border-radius: 12px; border: 1.5px solid #E2E8F0;
        background: #ffffff; color: #64748B; font-weight: 800; font-size: 11px;
        text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: all 0.2s ease;
    }
    .sep-btn-cancel:hover { background: #F8FAFC; border-color: #CBD5E1; color: #334155; }
    .sep-btn-save {
        flex: 1.5; padding: 12px; border-radius: 12px; border: none;
        background: linear-gradient(135deg, #123457, #0A1E33); color: #ffffff;
        font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;
        cursor: pointer; box-shadow: 0 10px 24px -8px rgba(10, 30, 51, 0.45);
        transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .sep-btn-save:hover { transform: translateY(-1px); box-shadow: 0 14px 28px -8px rgba(10, 30, 51, 0.55); }
    .sep-btn-save:active { transform: translateY(0) scale(0.98); }
    .sep-btn-save:disabled { opacity: 0.75; cursor: not-allowed; transform: none; }

    /* ============================ RESPONSIVE ============================ */
    @media (max-width: 640px) {
        .soa-modal-overlay.active { align-items: flex-end; padding: 0; }
        .soa-modal-container {
            width: 100%; max-width: 100% !important; border-radius: 22px 22px 0 0;
            max-height: 92vh; transform: translateY(24px); opacity: 0;
        }
        .soa-modal-overlay.active .soa-modal-container { transform: translateY(0); opacity: 1; }
        .soa-modal-container::before { border-radius: 22px 22px 0 0; }
        .soa-mobile-handle { display: block; }
        .sep-header { padding: 14px 18px 14px; }
        .sep-body { padding: 18px; }
        .sep-footer { padding: 14px 18px 18px; }
    }

    /* Catatan: aturan .dashboard-wrapper, .profile-sidebar, .profile-content,
       .avatar-dashboard, dan .info-grid untuk mobile sudah dipindahkan ke
       partials/sidebar-profile.blade.php dan profile.blade.php masing-masing,
       supaya tidak ada dua file yang saling menimpa aturan yang sama.
       Modal ini hanya menyimpan style miliknya sendiri. */
</style>

<script>
    const MAX_PHOTO_SIZE_MB = 2;

    function openEditProfileModal() {
        const modal = document.getElementById('modalEditProfile');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => { modal.classList.add('active'); }, 10);
    }

    function closeEditProfileModal() {
        const modal = document.getElementById('modalEditProfile');
        modal.classList.remove('active');
        setTimeout(() => { modal.style.display = 'none'; }, 500);
        document.body.style.overflow = 'auto';
    }

    /* SCRIPT KLIK LUAR UNTUK TUTUP MODAL */
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('modalEditProfile');
        if (event.target == modal) {
            closeEditProfileModal();
        }
    });

    /* TUTUP DENGAN TOMBOL ESC */
    window.addEventListener('keydown', function(event) {
        const modal = document.getElementById('modalEditProfile');
        if (event.key === 'Escape' && modal.classList.contains('active')) {
            closeEditProfileModal();
        }
    });

    function previewProfileImage(input) {
        const file = input.files[0];
        const preview = document.getElementById('imagePreview');
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            Swal.fire('Oops!', 'Hanya boleh file gambar ya.', 'error');
            input.value = "";
            return;
        }

        if (file.size > MAX_PHOTO_SIZE_MB * 1024 * 1024) {
            Swal.fire('Ukuran terlalu besar', `Ukuran foto maksimal ${MAX_PHOTO_SIZE_MB}MB ya.`, 'error');
            input.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => preview.src = e.target.result;
        reader.readAsDataURL(file);
    }

    /* STATUS LOADING SAAT SIMPAN, MENCEGAH KLIK GANDA */
    document.getElementById('formEditProfile').addEventListener('submit', function () {
        const btn = document.getElementById('btnSaveProfile');
        btn.disabled = true;
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i><span>Menyimpan...</span>';
    });
</script>
@endauth