@extends('layouts.app')

@section('title', 'Keamanan Akun - Sukabumi One Access')

@section('content')

<style>

    /* =========================================
       1. SIDEBAR DASHBOARD LAYOUT
       ========================================= */
    .dashboard-wrapper {
        display: flex;
        min-height: calc(100vh - 80px); /* Kurangi tinggi navbar utama */
        background-color: #F8FAFC;
    }

    /* SIDEBAR STYLING */
    .profile-sidebar {
        width: 280px;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        padding: 2rem 1.5rem;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
    }

    .sidebar-menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 1rem 1.25rem;
        border-radius: 14px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #64748b;
        transition: all 0.3s ease;
        margin-bottom: 0.5rem;
    }

    .sidebar-menu-link i { font-size: 1.2rem; }

    .sidebar-menu-link:hover {
        background: #f1f5f9;
        color: #123457;
    }

    .sidebar-menu-link.active {
        background: #123457;
        color: #f1f5f9;
        box-shadow: 0 10px 20px -5px rgba(18, 52, 87, 0.2);
    }

    .sidebar-menu-link.active .menu-chevron { opacity: 1; transform: translateX(0); }
    .menu-chevron { opacity: 0; transform: translateX(-4px); transition: all 0.2s ease; margin-left: auto; font-size: 0.75rem; }

    .profile-content {
        flex: 1;
        padding: 3rem;
    }

    /* 2. SECURITY CARD DESIGN */
    .security-card {
        background: #ffffff;
        border-radius: 30px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        padding: 2.5rem;
    }

    .input-field-wrap {
        position: relative;
    }

    .input-secure {
        width: 100%;
        padding: 1rem 1.25rem;
        padding-right: 3rem; /* ruang buat ikon mata */
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 14px;
        font-size: 0.9rem;
        font-weight: 600;
        transition: border-color 0.25s ease, background 0.25s ease, box-shadow 0.25s ease;
    }

    .input-secure:focus {
        outline: none;
        border-color: #E8A33D;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(232, 163, 61, 0.08);
    }

    /* state error: password tidak cocok */
    .input-secure.is-invalid {
        border-color: #ef4444;
        background: #fef2f2;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.08);
    }

    /* state valid: password cocok */
    .input-secure.is-valid {
        border-color: #10b981;
        background: #f0fdf9;
    }

    .input-eye-toggle {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 1rem;
        cursor: pointer;
        padding: 4px;
        line-height: 1;
        transition: color 0.2s ease;
    }

    .input-eye-toggle:hover { color: #123457; }

    .field-error-msg {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        color: #ef4444;
        margin-top: 6px;
        margin-left: 2px;
    }

    .field-success-msg {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        color: #10b981;
        margin-top: 6px;
        margin-left: 2px;
    }

    .security-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    @media (max-width: 1024px) {
        .profile-content { padding: 1.5rem; }
    }
</style>

<div class="dashboard-wrapper">

    @include('partials.sidebar-profile')

    <main class="profile-content">
        <div class="max-w-4xl mx-auto">

            <div class="grid lg:grid-cols-12 gap-8">

                {{-- Kiri: Form Ganti Password --}}
                <div class="lg:col-span-7 reveal">
                    <div class="security-card">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-primary text-accent flex items-center justify-center shadow-lg">
                                <i class="bi bi-key-fill text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-black text-primary">Ganti Password</h2>
                                <p class="text-slate-400 text-xs mt-0.5">Perbarui kata sandi Anda secara berkala.</p>
                            </div>
                        </div>

                        <form id="formPasswordKeamanan" action="{{ route('user.security.update') }}" method="POST" class="space-y-5">
                            @csrf

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Password Saat Ini</label>
                                <div class="input-field-wrap">
                                    <input type="password" name="current_password" id="current_password" class="input-secure" placeholder="••••••••" required>
                                    <button type="button" class="input-eye-toggle" onclick="togglePasswordVisibility('current_password', this)" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Password Baru</label>
                                <div class="input-field-wrap">
                                    <input type="password" name="new_password" id="new_password" class="input-secure" placeholder="Minimal 8 karakter" required minlength="8" oninput="checkPasswordMatch()">
                                    <button type="button" class="input-eye-toggle" onclick="togglePasswordVisibility('new_password', this)" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Konfirmasi Password Baru</label>
                                <div class="input-field-wrap">
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="input-secure" placeholder="Ulangi password baru" required oninput="checkPasswordMatch()">
                                    <button type="button" class="input-eye-toggle" onclick="togglePasswordVisibility('new_password_confirmation', this)" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <p id="confirmErrorMsg" class="field-error-msg hidden">
                                    <i class="bi bi-exclamation-circle-fill"></i> Password baru dan konfirmasi tidak cocok
                                </p>
                                <p id="confirmSuccessMsg" class="field-success-msg hidden">
                                    <i class="bi bi-check-circle-fill"></i> Password cocok
                                </p>
                            </div>

                            <div class="pt-4">
                                <button type="button" onclick="validateAndOpenModal()" class="w-full bg-primary text-white py-4 rounded-2xl font-bold text-sm shadow-xl shadow-primary/20 hover:bg-primary-dark transition-all active:scale-95">
                                    Perbarui Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Kanan: Status Info --}}
                <div class="lg:col-span-5 space-y-6 reveal" style="transition-delay: 150ms">
                    <div class="bg-white border border-slate-100 p-8 rounded-[2rem] shadow-sm">
                        <h4 class="text-sm font-bold text-primary mb-6">Status Keamanan</h4>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-slate-500">Kekuatan Akun</span>
                                <span class="security-badge bg-emerald-50 text-emerald-600">Sangat Kuat</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-slate-500">Verifikasi Profil</span>
                                <span class="security-badge bg-emerald-50 text-emerald-600">Aktif</span>
                            </div>
                        </div>
                        <div class="mt-8 pt-6 border-t border-slate-50 text-center">
                            <p class="text-[10px] text-slate-400 italic">Terakhir diperbarui: {{ Auth::user()->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="bg-[#123457] p-8 rounded-[2rem] text-white relative overflow-hidden shadow-xl">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-white/5 rounded-full -mr-10 -mt-10"></div>
                        <i class="bi bi-lightbulb text-accent text-3xl mb-4 block"></i>
                        <h4 class="font-bold text-sm mb-2">Tips Keamanan</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Gunakan kombinasi huruf kapital, angka, dan simbol untuk password yang lebih aman.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

{{-- JEMBATAN DATA LARAVEL --}}
<div id="session-data"
     data-success="{{ session('success') }}"
     data-error="{{ $errors->first() }}"
     class="hidden">
</div>

@endsection

@push('scripts')
<script>
    /**
     * 1. TOGGLE LIHAT/SEMBUNYIKAN PASSWORD
     */
    function togglePasswordVisibility(inputId, btnEl) {
        const input = document.getElementById(inputId);
        const icon = btnEl.querySelector('i');
        const isHidden = input.type === 'password';

        input.type = isHidden ? 'text' : 'password';
        icon.classList.toggle('bi-eye', !isHidden);
        icon.classList.toggle('bi-eye-slash', isHidden);
    }

    /**
     * 2. VALIDASI REAL-TIME: PASSWORD BARU VS KONFIRMASI
     */
    function checkPasswordMatch() {
        const newPass = document.getElementById('new_password').value;
        const confirmInput = document.getElementById('new_password_confirmation');
        const confirmPass = confirmInput.value;
        const errorMsg = document.getElementById('confirmErrorMsg');
        const successMsg = document.getElementById('confirmSuccessMsg');

        // belum diisi sama sekali -> jangan tampilkan status apapun dulu
        if (confirmPass.length === 0) {
            confirmInput.classList.remove('is-invalid', 'is-valid');
            errorMsg.classList.add('hidden');
            successMsg.classList.add('hidden');
            return;
        }

        if (newPass !== confirmPass) {
            confirmInput.classList.add('is-invalid');
            confirmInput.classList.remove('is-valid');
            errorMsg.classList.remove('hidden');
            successMsg.classList.add('hidden');
        } else {
            confirmInput.classList.remove('is-invalid');
            confirmInput.classList.add('is-valid');
            errorMsg.classList.add('hidden');
            successMsg.classList.remove('hidden');
        }
    }

    /**
     * 3. FUNGSI VALIDASI SEBELUM BUKA MODAL
     */
    function validateAndOpenModal() {
        const form = document.getElementById('formPasswordKeamanan');
        const newPass = form.new_password.value;
        const confirmPass = form.new_password_confirmation.value;

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        if (newPass !== confirmPass) {
            checkPasswordMatch(); // pastikan state merah kelihatan juga di field-nya
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Konfirmasi password baru tidak cocok!',
                confirmButtonColor: '#123457'
            });
            return;
        }

        if (typeof openConfirmPasswordModal === "function") {
            openConfirmPasswordModal();
        }
    }

    /**
     * 4. HANDLING ALERT SUKSES & ERROR DARI LARAVEL
     */
    document.addEventListener('DOMContentLoaded', () => {
        const sessionElement = document.getElementById('session-data');
        const successMsg = sessionElement.getAttribute('data-success');
        const errorMsg = sessionElement.getAttribute('data-error');

        if (successMsg) {
            Swal.fire({
                html: `
                    <div style="padding: 20px; text-align: center;">
                        <div style="width: 60px; height: 60px; background: rgba(16, 185, 129, 0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: #10b981; font-size: 24px; border: 1px solid rgba(16, 185, 129, 0.15);">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 style="font-size: 18px; color: #123457; font-weight: 800; text-transform: uppercase;">Berhasil!</h3>
                        <p style="margin-top: 10px; font-size: 13px; color: #64748b;">${successMsg}</p>
                    </div>
                `,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: { popup: 'premium-modal' }
            });
        }

        if (errorMsg) {
            Swal.fire({
                html: `
                    <div style="padding: 20px; text-align: center;">
                        <div style="width: 60px; height: 60px; background: rgba(239, 68, 68, 0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: #ef4444; font-size: 24px; border: 1px solid rgba(239, 68, 68, 0.15);">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <h3 style="font-size: 18px; color: #123457; font-weight: 800; text-transform: uppercase;">Gagal!</h3>
                        <p style="margin-top: 10px; font-size: 13px; color: #64748b;">${errorMsg}</p>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'Perbaiki',
                confirmButtonColor: '#123457',
                customClass: { popup: 'premium-modal' }
            });
        }
    });
</script>
@endpush