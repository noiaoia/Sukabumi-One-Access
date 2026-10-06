<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        /* Desain CSS Internal agar didukung Gmail/Outlook */
        .main-bg { background-color: #F4F7F9; padding: 50px 0; }
        .email-card { background-color: #ffffff; max-width: 550px; margin: 0 auto; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 40px rgba(10, 30, 51, 0.05); border: 1px solid #E2E8F0; }
        .navy-header { background-color: #0A1E33; padding: 40px; text-align: center; border-bottom: 5px solid #E8A33D; }
        .body-content { padding: 45px; font-family: 'Helvetica Neue', Arial, sans-serif; }
        .text-main { color: #0A1E33; font-size: 16px; line-height: 1.8; margin-bottom: 25px; }
        .btn-gold { background-color: #E8A33D; color: #0A1E33 !important; padding: 18px 35px; border-radius: 14px; text-decoration: none; font-weight: 800; font-size: 14px; display: inline-block; letter-spacing: 1px; }
        .footer-text { padding: 30px; text-align: center; color: #94A3B8; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; }
    </style>
</head>
<body style="margin: 0; padding: 0;">
    <table class="main-bg" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="email-card" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <!-- Header -->
                    <tr>
                        <td class="navy-header">
                            <div style="color: #ffffff; font-weight: 900; font-size: 18px; letter-spacing: 3px; text-transform: uppercase;">Sukabumi One Access</div>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td class="body-content">
                            {{ $slot }}
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td class="footer-text">
                            &copy; {{ date('Y') }} PEMERINTAH KOTA SUKABUMI<br>
                            DINAS KOMUNIKASI DAN INFORMATIKA
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>