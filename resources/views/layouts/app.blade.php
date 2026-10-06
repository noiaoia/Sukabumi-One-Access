<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sukabumi One Access</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#123457', dark: '#0A1E33' },
                        accent: { DEFAULT: '#E8A33D', dark: '#3A2205' },
                    },
                }
            }
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        html, body {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
            display: none;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-hero-gradient { background: linear-gradient(135deg, #123457 0%, #0A1E33 100%); }
        .category-chip { transition: all 0.2s ease; }
        .service-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .service-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(14, 42, 71, 0.1); }

    </style>
    @stack('styles')
</head>
<body
    data-user-notification="{{ $userNotificationCount ?? 0 }}"
    class="bg-white min-h-screen text-slate-600 antialiased">

    @include('partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('components.modal-logout-confirm')
    @include('partials.footer')

    {{-- Load Modals as Components --}}
    @include('components.user.modal-auth-alert')
    @include('components.user.modal-confirm-password')
    @include('components.user.modal-edit-profile')
    @include('components.user.modal-status')
    @include('components.user.modal-form-errors')
    @include('components.user.modal-tracking-choice')

    {{-- Global Scripts (Modal Logic) --}}
    <script>
        // Ganti fungsi lama di layouts/app.blade.php dengan ini:
        function openAuthAlertModal() {
            const modal = document.getElementById('modalAuthAlert');
            if (modal) {
                modal.classList.remove('is-closing');
                modal.classList.add('is-active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeAuthAlertModal() {
            const modal = document.getElementById('modalAuthAlert');
            if (modal) {
                modal.classList.add('is-closing');
                setTimeout(() => {
                    modal.classList.remove('is-active');
                    modal.classList.remove('is-closing');
                    document.body.style.overflow = 'auto';
                }, 300);
            }
        }

        // Tutup modal ketika klik area luar (overlay)
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modalAuthAlert');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === modal || e.target.id === 'modalAuthOverlay') {
                        closeAuthAlertModal();
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>