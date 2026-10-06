<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light only" />
    <meta name="supported-color-schemes" content="light only" />
    <title>Aktivasi Akun - Sukabumi One Access</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; margin: 0; padding: 0; width: 100% !important; -webkit-font-smoothing: antialiased; }
        table { border-collapse: collapse; }
        .main-bg { background-color: #F4F7F9; padding: 50px 0; }
        .email-card { background-color: #ffffff; max-width: 550px; margin: 0 auto; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 40px rgba(10, 30, 51, 0.05); border: 1px solid #E2E8F0; }
        .navy-header { background-color: #0A1E33; padding: 40px 40px 32px; text-align: center; border-bottom: 5px solid #E8A33D; }
        .body-content { padding: 45px; text-align: left; }
        .text-main { color: #0A1E33; font-size: 16px; line-height: 1.8; margin-bottom: 25px; }
        .footer-text { padding: 28px 30px; text-align: center; color: #94A3B8; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; line-height: 1.9; }
        .link-box { background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 14px 16px; font-size: 12px; color: #123457; word-break: break-all; font-family: 'Courier New', monospace; }
        .expiry-box { background-color: #FFFBEB; border: 1px solid #FDE68A; border-radius: 14px; padding: 16px 18px; }
        .noreply-note { background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 12px 16px; font-size: 11.5px; color: #94A3B8; }
        .contact-line { color: #B0BAC6; font-size: 10.5px; text-transform: none; letter-spacing: 0; }
        .contact-line a { color: #94A3B8; text-decoration: underline; }

        @media only screen and (max-width: 600px) {
            .main-bg { padding: 24px 0 !important; }
            .email-card { border-radius: 20px !important; width: 92% !important; }
            .navy-header { padding: 30px 24px 24px !important; }
            .body-content { padding: 30px 24px !important; }
            .text-main { font-size: 15px !important; }
            .footer-text { padding: 22px 20px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #F4F7F9;">

    {{-- Preheader: teks preview yang muncul di inbox, tersembunyi dari tampilan email itu sendiri --}}
    <div style="display: none; max-height: 0; overflow: hidden; mso-hide: all;">
        Satu langkah lagi untuk mengaktifkan akun Sukabumi One Access Anda. Tautan berlaku 60 menit.
    </div>

    <table class="main-bg" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="email-card" width="550" cellpadding="0" cellspacing="0" role="presentation">

                    <!-- Header -->
                    <tr>
                        <td class="navy-header">
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td align="center">
                                        <table cellpadding="0" cellspacing="0" role="presentation">
                                            <tr>
                                                <td width="64" height="64" bgcolor="#ffffff" style="border-radius: 18px; text-align: center; vertical-align: middle; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                                    <img src="{{ $message->embed(public_path('image/logo.png')) }}"
                                                         width="42"
                                                         height="42"
                                                         alt="Logo Sukabumi One Access"
                                                         style="display: block; margin: 0 auto;">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 18px;">
                                        <span style="color: #ffffff; font-weight: 900; font-size: 17px; letter-spacing: 2.5px; text-transform: uppercase; font-family: 'Plus Jakarta Sans', Arial, sans-serif;">Sukabumi One Access</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 6px;">
                                        <span style="color: #E8A33D; font-weight: 700; font-size: 11px; letter-spacing: 2px; text-transform: uppercase;">Otoritas Keamanan Sistem</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="body-content">
                            <div class="text-main">
                                <p style="margin: 0 0 20px;">Sampurasun, <strong>{{ $name }}</strong>!</p>
                                <p style="margin: 0 0 20px;">Terima kasih telah bergabung di portal layanan terpadu <strong>Sukabumi One Access</strong>.</p>
                                <p style="margin: 0;">Satu langkah lagi untuk mengaktifkan akun Anda. Silakan klik tombol di bawah ini untuk memverifikasi identitas digital Anda:</p>
                            </div>

                            <!-- START OF PREMIUM BUTTON -->
                            <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                                            <tr>
                                                <td align="center" bgcolor="#E8A33D" style="border-radius: 14px; mso-padding-alt: 20px 40px;">
                                                    <a href="{{ $url }}"
                                                       target="_blank"
                                                       rel="noopener"
                                                       style="background-color: #E8A33D;
                                                              border: 1px solid #E8A33D;
                                                              border-radius: 14px;
                                                              color: #0A1E33 !important;
                                                              display: inline-block;
                                                              font-family: Arial, sans-serif;
                                                              font-size: 13px;
                                                              font-weight: 800;
                                                              line-height: 1;
                                                              mso-line-height-rule: exactly;
                                                              padding: 20px 40px;
                                                              text-align: center;
                                                              text-decoration: none;
                                                              text-transform: uppercase;
                                                              letter-spacing: 2px;
                                                              -webkit-text-size-adjust: none;">
                                                        Aktifkan Akun Saya &rarr;
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- END OF PREMIUM BUTTON -->

                            {{-- Fallback link, penting kalau tombol tidak bisa diklik di klien email tertentu --}}
                            <div style="margin-bottom: 24px;">
                                <p style="color: #94A3B8; font-size: 12px; margin: 0 0 8px;">Atau salin dan tempel tautan berikut ke browser Anda:</p>
                                <div class="link-box">{{ $url }}</div>
                            </div>

                            {{-- Kotak info kedaluwarsa, gaya konsisten sama warning box di situs --}}
                            <table class="expiry-box" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom: 16px;">
                                <tr>
                                    <td style="padding: 16px 18px;">
                                        <table cellpadding="0" cellspacing="0" role="presentation">
                                            <tr>
                                                <td style="font-size: 13px; color: #92400E; line-height: 1.6;">
                                                    <strong>&#9201; Berlaku 60 menit.</strong> Tautan verifikasi ini akan kedaluwarsa demi keamanan akun Anda. Jika Anda tidak merasa melakukan pendaftaran ini, silakan abaikan email ini.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Catatan no-reply, biar user tidak membalas ke alamat pengirim otomatis --}}
                            <table class="noreply-note" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td style="padding: 12px 16px;">
                                        <i>Email ini dikirim otomatis oleh sistem, mohon tidak membalas langsung ke alamat ini.</i> Jika butuh bantuan, hubungi kontak resmi Diskominfo di bagian bawah email.
                                    </td>
                                </tr>
                            </table>

                            <div class="text-main" style="margin-top: 28px; font-size: 14px; color: #64748B;">
                                <p style="margin: 0;">Hormat kami,<br><strong>Admin Sukabumi One Access</strong></p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #F8FAFC; border-top: 1px solid #E2E8F0;">
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="footer-text">
                                        &copy; {{ date('Y') }} PEMERINTAH KOTA SUKABUMI<br>
                                        DINAS KOMUNIKASI DAN INFORMATIKA<br>
                                        <span class="contact-line">Jl. Syamsudin SH No.25, Kota Sukabumi, Jawa Barat</span><br>
                                        <span class="contact-line">
                                            <a href="mailto:diskominfo@sukabumikota.go.id">diskominfo@sukabumikota.go.id</a>
                                            &bull; (0266) 221123
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>