<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>OTP Administrator - Sukabumi One Access</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; padding: 0; width: 100% !important; 
            -webkit-font-smoothing: antialiased; 
            background-color: #F6F1E7; /* BG CREAM */
        }
        table { border-collapse: collapse; }
        
        .email-wrapper { padding: 40px 10px; background-color: #F6F1E7; }
        .email-card { 
            background-color: #ffffff; /* CARD PUTIH */
            max-width: 550px; 
            margin: 0 auto; 
            border-radius: 28px; 
            overflow: hidden; 
            box-shadow: 0 20px 50px rgba(11, 30, 51, 0.1); 
            border: 1px solid #e2e8f0;
        }

        /* Header Style: Logo Text Centered */
        .command-header { 
            background-color: #0B1E33; /* NAVY */
            padding: 45px 40px; 
            text-align: center;
            border-bottom: 5px solid #E8A33D; /* BORDER OREN TEBAL */
        }
        .brand-main { 
            color: #ffffff; 
            font-size: 26px; 
            font-weight: 800; 
            letter-spacing: -1px; 
            line-height: 1; 
            margin: 0;
        }
        .brand-sub { 
            color: #E8A33D; 
            font-size: 11px; 
            font-weight: 700; 
            letter-spacing: 5px; 
            text-transform: uppercase; 
            margin-top: 8px; 
            line-height: 1;
        }

        /* Body Content */
        .body-content { padding: 50px 45px; text-align: center; background-color: #ffffff; }
        .otp-box { 
            background: #F8FAFC; 
            border: 2px dashed #E8A33D; 
            border-radius: 24px; 
            padding: 40px 20px; 
            margin: 35px 0; 
        }
        .otp-code { 
            font-size: 46px; 
            font-weight: 800; 
            letter-spacing: 15px; 
            color: #0B1E33; 
            font-family: 'Courier New', monospace; 
            margin-left: 15px; /* Offset for tracking balance */
        }

        .footer { 
            background-color: #0B1E33; 
            padding: 40px; 
            text-align: center; 
            color: #94A3B8; 
            font-size: 11px; 
        }

        @media only screen and (max-width: 600px) {
            .email-wrapper { padding: 20px 10px !important; }
            .body-content { padding: 40px 25px !important; }
            .otp-code { font-size: 34px !important; letter-spacing: 10px !important; }
            .brand-main { font-size: 22px !important; }
        }
    </style>
</head>
<body>
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="email-card" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    
                    <!-- Header: Centered Logo Text -->
                    <tr>
                        <td class="command-header">
                            <div class="brand-main">SUKABUMI</div>
                            <div class="brand-sub">ONE ACCESS</div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td class="body-content">
                            <h2 style="color: #0B1E33; font-size: 22px; font-weight: 800; margin: 0 0 15px;">Verifikasi Administrator</h2>
                            <p style="color: #64748B; font-size: 15px; line-height: 1.6; margin: 0;">
                                Silakan gunakan kode OTP di bawah ini untuk memverifikasi identitas Anda dan melanjutkan akses ke <strong>Command Center</strong>.
                            </p>

                            <!-- OTP Box -->
                            <div class="otp-box">
                                <div style="font-size: 11px; font-weight: 800; color: #E8A33D; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px;">
                                    Kode OTP Rahasia
                                </div>
                                <div class="otp-code">{{ $otp }}</div>
                            </div>

                            <!-- Alert Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom: 30px;">
                                <tr>
                                    <td style="background-color: #FFF8EC; border-radius: 14px; padding: 18px; text-align: center; border: 1px solid #FFE4BC;">
                                        <p style="margin: 0; font-size: 13px; color: #92400E; line-height: 1.5;">
                                            ⏱ Kode ini berlaku selama <strong>5 menit</strong>. <br>Jangan bagikan kode ini kepada pihak manapun.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #94A3B8; font-size: 12px; line-height: 1.5; margin: 0;">
                                Jika Anda tidak merasa melakukan permintaan verifikasi ini, silakan abaikan email ini atau hubungi Tim IT Diskominfo.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <div style="color: #ffffff; font-weight: 800; font-size: 12px; margin-bottom: 10px; letter-spacing: 2px; text-transform: uppercase;">
                                DISKOMINFO KOTA SUKABUMI
                            </div>
                            <div style="line-height: 1.8; color: #64748B;">
                                Jl. Syamsudin SH No.25, Kota Sukabumi, Jawa Barat<br>
                                © {{ date('Y') }} Portal Sukabumi One Access
                            </div>
                            <div style="margin-top: 30px; padding-top: 25px; border-top: 1px solid rgba(255,255,255,0.05); font-size: 10px; color: #475569;">
                                Email ini dibuat secara otomatis. Mohon tidak membalas.
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>