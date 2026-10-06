<?php

use Illuminate\Support\Facades\Route;

// --- Controllers: Auth & Global ---
use App\Http\Controllers\AuthController;

// --- Controllers: User Space ---
use App\Http\Controllers\User\{
    HomeController,
    AboutController,
    LayananController,
    PengaduanController,
    LacakController,
    InstansiUserController,
    PortalUserController,
    FormController,
    PengajuanController
};

// --- Controllers: Admin Space ---
use App\Http\Controllers\Admin\{
    DashboardController,
    OperatorController,
    InstansiController,
    PengaduanAdminController,
    LayananAdminController,
    ActivityController,
    ProfileAdminController,
    SettingAdminController,
    AdminDinasController,
    UserAdminController,
    PengajuanLayananController,
    NotificationAdminController,
    OperatorDinasController
};

// --- Controllers: Layanan Sub-Folder ---
use App\Http\Controllers\Admin\Layanan\{
    IframeController,
    FormulirController,
    CategoriesAdminController
};

/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES (Bisa diakses siapa saja)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang', [AboutController::class, 'index'])->name('about');
Route::get('/layanan', [LayananController::class, 'index'])->name('layanan');
Route::get('/instansi/{slug}', [InstansiUserController::class, 'show'])->name('instansi.show');
Route::get('/portal/{slug}', [PortalUserController::class, 'show'])->name('portal.show');
Route::get('/layanan/form/{slug}', [FormController::class, 'show'])->name('layanan.form.show');
Route::get('/layanan/form/{slug}/kategori/{categoryId}', [FormController::class, 'form'])->name('layanan.form.category');
Route::post('/layanan/form/{slug}/kategori/{categoryId}/submit', [PengajuanController::class, 'store'])->name('layanan.form.submit');
Route::get('/instansi/{id}/layanan-count', [InstansiUserController::class, 'layananCount'])->name('instansi.layanan-count');

/*
|--------------------------------------------------------------------------
| 2. GUEST ROUTES (Hanya untuk yang BELUM login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/register', fn() => redirect()->route('login'));
});

// Verification Routes
Route::get('/pendaftaran-berhasil', fn() => view('auth.register-success'))->name('register.success');
Route::get('/verifikasi-akun/{token}', [AuthController::class, 'verifyAccount'])->name('register.verify');
Route::get('/akun-aktif', fn() => view('auth.register-verified'))->name('register.verified');
Route::post('/verifikasi/kirim-ulang', [AuthController::class, 'resendVerification'])->name('verification.send');
Route::post('/admin/login/resend', [AuthController::class, 'resendAdminOTP'])->name('admin.login.resend');

/*
|--------------------------------------------------------------------------
| 3. USER PROTECTED ROUTES (Hanya untuk akun ROLE: USER)
|--------------------------------------------------------------------------
| Admin tidak akan bisa masuk ke sini karena ada middleware 'role:user'
*/
Route::middleware(['auth', 'role:user'])->group(function () {
    
    // User Profile & Settings
    Route::prefix('user')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::get('/profile', 'showProfile')->name('user.profile');
            Route::post('/profile/update', 'updateProfile')->name('user.profile.update');
            Route::get('/security', 'showSecurity')->name('user.security');
            Route::post('/security/update', 'updatePassword')->name('user.security.update');
        });

        Route::controller(PengaduanController::class)->group(function () {
            Route::get('/pengaduan', 'index')->name('pengaduan');
            Route::post('/pengaduan', 'store')->name('pengaduan.store');
        });
    });

    // 1. RUTE RIWAYAT PENGADUAN (Lacak Pengaduan)
    // Pastikan namanya 'user.riwayat.pengaduan'
    Route::get('/riwayat-pengaduan', [LacakController::class, 'index'])->name('user.riwayat.pengaduan');
    Route::get('/riwayat-pengaduan/{id}/json', [LacakController::class, 'jsonDetail'])->name('user.riwayat.pengaduan.json');
    Route::get('/pemberitahuan', [LacakController::class, 'notifications'])->name('user.notifications');

    // 2. RUTE RIWAYAT PENGAJUAN (Arsip Berkas)
    Route::get('/riwayat-pengajuan', [PengajuanController::class, 'history'])->name('user.riwayat.pengajuan');
    Route::get('/riwayat-pengajuan/{id}/json', [PengajuanController::class, 'jsonDetail'])->name('user.riwayat.pengajuan.json');
    Route::get('/riwayat-pengajuan/{id}/edit-data', [PengajuanController::class, 'editData'])->name('riwayat.edit-data');
    Route::post('/riwayat-pengajuan/{id}/update', [PengajuanController::class, 'update'])->name('riwayat.update');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::post('/read', [PengaduanController::class, 'markNotificationsAsRead'])->name('read');
        Route::get('/check-count', [PengaduanController::class, 'notifCount'])->name('check-count');
        Route::get('/pengaduan/notif-count', [PengaduanController::class, 'notifCount'])->name('pengaduan.notif-count');
    });
});

/*
|--------------------------------------------------------------------------
| 4. ADMIN AUTHENTICATION GATEWAY
|--------------------------------------------------------------------------
*/
Route::get('/v1-gateway-management-sukabumi-center-auth-verified-access-2024', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/process-master-auth-authorized', [AuthController::class, 'adminLogin'])->name('admin.login.submit');
Route::post('/admin/precheck', [AuthController::class, 'sendAdminOTP'])->name('admin.login.precheck');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Routes Lupa Password Admin ---
Route::prefix('admin/recovery')->name('admin.forgot.')->group(function () {
    // 1. Tampilkan Halaman
    Route::get('/v1-master-password-reset-terminal', [AuthController::class, 'showForgotPasswordForm'])->name('index');
    
    // 2. Tahap 1: Verifikasi Identitas & Kirim OTP
    Route::post('/verify-identity', [AuthController::class, 'forgotPasswordVerifyIdentity'])->name('verify.identity');
    
    // 3. Tahap 2: Verifikasi OTP
    Route::post('/verify-otp', [AuthController::class, 'forgotPasswordVerifyOTP'])->name('verify.otp');
    
    // 4. Tahap 3: Update Password Baru
    Route::post('/update-password', [AuthController::class, 'forgotPasswordUpdate'])->name('update');
});

/*
|--------------------------------------------------------------------------
| 5. ADMIN PROTECTED ROUTES (SUPER ADMIN & ADMIN DINAS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:super_admin,admin_dinas,operator', 'multi_admin'])
    ->prefix('{portal_code}')
    ->name('admin.')
    ->group(function () {

        // --- A. SHARED ROUTES (Bisa diakses Keduanya) ---
        // Dashboard (Logika filter data ada di DashboardController@index)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

        // Pengaduan (Shared - Filtered by Agency in Controller)
        Route::prefix('pengaduan')->name('pengaduan.')->controller(PengaduanAdminController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/arsip', 'archive')->name('archive');
            Route::get('/notif-count', 'notifCount')->name('notif-count');
            Route::put('/{id}/update', 'updateStatus')->name('update');
            Route::get('/{id}/json-detail', 'jsonDetail')->name('json-detail'); 
            Route::post('/{id}/mark-as-review', 'markAsReview')->name('review');
            Route::post('/notifikasi/read', 'markNotificationsAsRead')->name('notifications.read');
        });

        // Submissions (Shared - Filtered by Agency in Controller)
        Route::prefix('submissions')->name('submission.')->controller(PengajuanLayananController::class)->group(function () {
            Route::get('/', 'index')->name('index');                        
            Route::get('/instansi/{instansi_id}', 'manage')->name('manage'); 
            Route::get('/{id}/json-detail', 'jsonDetail')->name('json-detail');
            Route::patch('/{id}/status', 'updateStatus')->name('update-status');
            Route::delete('/{id}/hapus', 'destroy')->name('destroy');
        });

        // --- INSTANSI ACCESS UNTUK ADMIN DINAS & OPERATOR ---
        Route::prefix('instansi')->name('instansi.')->controller(InstansiController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/reverse-geocode', 'reverseGeocode')->name('reverse-geocode');
            Route::put('/{id}/update', 'update')->name('update');
            Route::get('/{id}/visual-identity', 'visualIdentity')->name('visual');
            Route::put('/{id}/visual-identity/update', 'updateVisualIdentity')->name('visual.update');
            Route::get('/{id}/company-profile', 'companyProfile')->name('company-profile');
            Route::put('/{id}/company-profile/update', 'updateCompanyProfile')->name('company-profile.update');
            Route::get('/{id}/layanan-count', 'layananCount')->name('layanan-count');
            Route::get('/{id}/visual-layanan', 'visualLayanan')->name('visual-layanan');
            Route::put('/{id}/visual-layanan/update', 'updateVisualLayanan')->name('visual-layanan.update');
        });

        // Layanan (Shared - Super Admin kelola semua, Admin Dinas kelola miliknya)
        Route::prefix('layanan')->name('layanan.')->group(function () {
            Route::get('/', [LayananAdminController::class, 'index'])->name('index');

            // Iframe
            Route::prefix('iframe')->name('iframe.')->controller(IframeController::class)->group(function () {
                Route::get('/{instansi_slug}/manage', 'manage')->name('manage');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/{id}/visual', 'visual')->name('visual');
                Route::put('/{id}/visual/update', 'updateVisual')->name('visual.update');
                Route::get('/{id}/editor', 'editor')->name('editor');
                Route::put('/{id}/editor/update', 'updateEditor')->name('editor.update');
                Route::put('/{id}/update', 'update')->name('update');
                Route::delete('/{id}/hapus', 'destroy')->name('destroy');
                Route::patch('/{id}/status', 'updateStatus')->name('status');
            });

            // Form
            Route::prefix('form')->name('form.')->group(function () {
                Route::controller(FormulirController::class)->group(function () {
                    Route::get('/{instansi_slug}/manage', 'manage')->name('manage');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/store', 'store')->name('store');
                    Route::get('/{id}/visual', 'visual')->name('visual');
                    Route::put('/{id}/update', 'update')->name('update');
                    Route::delete('/{id}/hapus', 'destroy')->name('destroy');
                    Route::patch('/{id}/status', 'updateStatus')->name('status'); 
                });

                Route::prefix('{id}/categories')->name('categories.')->controller(CategoriesAdminController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create-category', 'create')->name('create');
                    Route::post('/store', 'store')->name('store');
                    Route::put('/{cat_id}/update', 'update')->name('update');
                    Route::patch('/{cat_id}/status', 'updateStatus')->name('status');
                    Route::delete('/{cat_id}/hapus', 'destroy')->name('destroy');
                });
                Route::get('/{id}/categories/{cat_id}/builder', [FormulirController::class, 'builder'])->name('builder');
                Route::post('/{id}/categories/{cat_id}/builder/save', [FormulirController::class, 'saveBuilder'])->name('builder.save');
            });
        });

        // Profil & Settings (Shared)
        Route::get('/profile', [ProfileAdminController::class, 'index'])->name('profile.index');
        Route::put('/profile/update', [ProfileAdminController::class, 'update'])->name('profile.update');
        Route::get('/settings', [SettingAdminController::class, 'index'])->name('settings.index');
        Route::put('/settings/update-password', [SettingAdminController::class, 'updatePassword'])->name('settings.update-password');
        Route::get('/notifications', [NotificationAdminController::class, 'index'])->name('notifications.index');


        // --- B. EXCLUSIVE ROUTES (Hanya Super Admin) ---
        Route::middleware(['role:super_admin'])->group(function () {
            
            // Operator
            Route::prefix('operator')->name('operator.')->controller(OperatorController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/tambah', 'create')->name('create');
                Route::post('/simpan', 'store')->name('store');
                Route::put('/{id}/update', 'update')->name('update');
                Route::delete('/{id}/hapus', 'destroy')->name('destroy');
                Route::post('/send-verification', 'sendVerification')->name('send-verification');
                Route::post('/verify-otp', 'verifyOTP')->name('verify-otp');
            });

            // Master Instansi
            Route::prefix('instansi')->name('instansi.')->controller(InstansiController::class)->group(function () {
                Route::get('/tambah', 'create')->name('create');
                Route::post('/simpan', 'store')->name('store');
                Route::delete('/{id}/hapus', 'destroy')->name('destroy');
                Route::patch('/{id}/status', 'updateStatus')->name('status');
            });

            // Admin Dinas Management
            Route::prefix('admin-dinas')->name('admin-dinas.')->controller(AdminDinasController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/tambah', 'create')->name('create');
                Route::post('/simpan', 'store')->name('store');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}/update', 'update')->name('update');
                Route::delete('/{id}/hapus', 'destroy')->name('destroy');
                Route::post('/send-verification', 'sendVerification')->name('send-verification');
                Route::post('/verify-otp', 'verifyOTP')->name('verify-otp');
            });

            // User Warga Management
            Route::prefix('users')->name('users.')->controller(UserAdminController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::delete('/{id}/hapus', 'destroy')->name('destroy');
                Route::patch('/{id}/toggle-status', 'toggleStatus')->name('toggle-status');
            });

            // Log Aktivitas
            Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
        });

        // --- C. EXCLUSIVE ROUTES (Hanya Admin Dinas) ---
        Route::middleware(['role:admin_dinas'])->group(function () {

            // Route Manajemen Operator Internal (Anak Buah Admin Dinas)
            Route::prefix('operator-internal')->name('operator-dinas.')->controller(OperatorDinasController::class)->group(function () {
                Route::get('/', 'index')->name('index');           // Tampilan daftar
                Route::get('/tambah', 'create')->name('create');   // Halaman form tambah
                Route::post('/simpan', 'store')->name('store');    // Proses simpan data

                // --- TAMBAHKAN 2 BARIS INI ---
                Route::post('/send-verification', 'sendVerification')->name('send-verification');
                Route::post('/verify-otp', 'verifyOTP')->name('verify-otp');
                // -----------------------------

                Route::put('/{id}/update', 'update')->name('update'); // Proses edit
                Route::delete('/{id}/hapus', 'destroy')->name('destroy'); // Proses hapus
            });

            // Route profil instansi yang sudah ada sebelumnya
            Route::get('/my-agency/profile', [InstansiController::class, 'companyProfile'])->name('my-agency.profile');
        });

    });