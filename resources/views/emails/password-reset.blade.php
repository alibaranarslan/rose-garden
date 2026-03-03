<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Şifre Sıfırlama</title>
</head>
<body style="margin:0;padding:0;background-color:#FAF7F5;font-family:Arial,Helvetica,sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#FAF7F5;">
  <tr>
    <td align="center" style="padding:30px 16px;">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

        <!-- HEADER -->
        <tr>
          <td style="background-color:#2D0A3E;padding:28px 40px;text-align:center;">
            <p style="margin:0;font-size:10px;letter-spacing:3px;text-transform:uppercase;color:#d4a8e8;">ÇIÇEK &amp; ÇİKOLATA</p>
            <h1 style="margin:6px 0 0;font-size:24px;font-weight:700;color:#ffffff;">Rose Garden</h1>
          </td>
        </tr>
        <tr>
          <td style="background-color:#8E44AD;padding:14px 40px;text-align:center;">
            <p style="margin:0;font-size:14px;font-weight:600;color:#ffffff;">🔒 Şifre Sıfırlama Talebi</p>
          </td>
        </tr>

        <!-- BODY -->
        <tr>
          <td style="padding:36px 40px 24px;">
            <p style="margin:0 0 16px;font-size:15px;color:#333333;">
              Sayın <strong>{{ $user->name ?? 'Değerli Üyemiz' }}</strong>,
            </p>
            <p style="margin:0 0 16px;font-size:14px;color:#555555;line-height:1.7;">
              Rose Garden hesabınız için şifre sıfırlama talebinde bulundunuz. Aşağıdaki butona tıklayarak yeni şifrenizi belirleyebilirsiniz.
            </p>
            <p style="margin:0;font-size:13px;color:#888888;line-height:1.6;">
              Bu link <strong>60 dakika</strong> geçerlidir. Süresi geçtikten sonra yeni bir sıfırlama talebi oluşturmanız gerekmektedir.
            </p>
          </td>
        </tr>

        <!-- CTA -->
        <tr>
          <td style="padding:0 40px 32px;text-align:center;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
              <tr>
                <td style="background-color:#8E44AD;border-radius:6px;">
                  <a href="{{ $url }}"
                     style="display:inline-block;padding:16px 40px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;letter-spacing:0.5px;">
                    Şifremi Sıfırla
                  </a>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- LINK FALLBACK -->
        <tr>
          <td style="padding:0 40px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f9f5fb;border-radius:6px;border:1px solid #ede8f0;">
              <tr>
                <td style="padding:16px 20px;">
                  <p style="margin:0 0 6px;font-size:12px;color:#888888;">Buton çalışmıyorsa aşağıdaki linki tarayıcınıza kopyalayın:</p>
                  <p style="margin:0;font-size:11px;color:#8E44AD;word-break:break-all;">{{ $url }}</p>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- WARNING -->
        <tr>
          <td style="padding:0 40px 32px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fff8e8;border-radius:6px;border:1px solid #f0d080;">
              <tr>
                <td style="padding:14px 20px;font-size:13px;color:#856404;line-height:1.6;">
                  ⚠ <strong>Bu talebi siz yapmadıysanız</strong> bu e-postayı dikkate almayın. Hesabınız güvende, hiçbir değişiklik yapılmamıştır. Herhangi bir şüpheniz varsa bizimle iletişime geçin.
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr><td style="padding:0 40px;"><hr style="border:none;border-top:1px solid #ede8f0;margin:0;"></td></tr>
        <tr>
          <td style="padding:24px 40px;text-align:center;">
            <p style="margin:0 0 8px;font-size:12px;color:#888888;">
              Rose Garden Çiçek Çikolata &nbsp;|&nbsp;
              <a href="mailto:info@rosegarden.com.tr" style="color:#8E44AD;text-decoration:none;">info@rosegarden.com.tr</a>
            </p>
            <p style="margin:0;font-size:11px;color:#aaaaaa;">&copy; {{ date('Y') }} Rose Garden. Tüm hakları saklıdır.</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
