<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rose Garden'a Hoş Geldiniz!</title>
</head>
<body style="margin:0;padding:0;background-color:#FAF7F5;font-family:Arial,Helvetica,sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#FAF7F5;">
  <tr>
    <td align="center" style="padding:30px 16px;">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

        <!-- HEADER -->
        <tr>
          <td style="background-color:#2D0A3E;padding:36px 40px;text-align:center;">
            <p style="margin:0;font-size:10px;letter-spacing:3px;text-transform:uppercase;color:#d4a8e8;">ÇIÇEK &amp; ÇİKOLATA</p>
            <h1 style="margin:8px 0 0;font-size:28px;font-weight:700;color:#ffffff;">Rose Garden</h1>
            <p style="margin:10px 0 0;font-size:16px;color:#d4a8e8;font-style:italic;">Hoş Geldiniz! 💐</p>
          </td>
        </tr>

        <!-- GREETING -->
        <tr>
          <td style="padding:36px 40px 20px;text-align:center;">
            <h2 style="margin:0 0 12px;font-size:20px;font-weight:700;color:#2D0A3E;">Aramıza Hoş Geldiniz, {{ $user->name ?? 'Sevgili Üyemiz' }}!</h2>
            <p style="margin:0;font-size:14px;color:#555555;line-height:1.7;max-width:440px;margin:0 auto;">
              Hesabınız başarıyla oluşturuldu. Artık Rose Garden'ın tüm ayrıcalıklarından yararlanabilirsiniz.
            </p>
          </td>
        </tr>

        <!-- FEATURES -->
        <tr>
          <td style="padding:16px 40px 28px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">

              <!-- Feature 1 -->
              <tr>
                <td style="padding:10px 0;border-bottom:1px solid #f0e8f5;">
                  <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="width:44px;vertical-align:top;padding-top:2px;">
                        <div style="width:36px;height:36px;background-color:#f0e0fb;border-radius:50%;text-align:center;line-height:36px;font-size:18px;">📦</div>
                      </td>
                      <td style="padding-left:14px;vertical-align:top;">
                        <p style="margin:0 0 3px;font-size:14px;font-weight:700;color:#2D0A3E;">Sipariş Takibi</p>
                        <p style="margin:0;font-size:13px;color:#666666;line-height:1.5;">Tüm siparişlerinizi hesabınızdan anlık olarak takip edin.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <!-- Feature 2 -->
              <tr>
                <td style="padding:10px 0;border-bottom:1px solid #f0e8f5;">
                  <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="width:44px;vertical-align:top;padding-top:2px;">
                        <div style="width:36px;height:36px;background-color:#f0e0fb;border-radius:50%;text-align:center;line-height:36px;font-size:18px;">❤️</div>
                      </td>
                      <td style="padding-left:14px;vertical-align:top;">
                        <p style="margin:0 0 3px;font-size:14px;font-weight:700;color:#2D0A3E;">Favoriler</p>
                        <p style="margin:0;font-size:13px;color:#666666;line-height:1.5;">Sevdiğiniz ürünleri favori listenize ekleyin, hızlıca bulun.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <!-- Feature 3 -->
              <tr>
                <td style="padding:10px 0;">
                  <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="width:44px;vertical-align:top;padding-top:2px;">
                        <div style="width:36px;height:36px;background-color:#f0e0fb;border-radius:50%;text-align:center;line-height:36px;font-size:18px;">🌟</div>
                      </td>
                      <td style="padding-left:14px;vertical-align:top;">
                        <p style="margin:0 0 3px;font-size:14px;font-weight:700;color:#2D0A3E;">Paraçiçek Puanları</p>
                        <p style="margin:0;font-size:13px;color:#666666;line-height:1.5;">Her alışverişinizde puan kazanın, sonraki alışverişinizde kullanın.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

            </table>
          </td>
        </tr>

        <!-- CTA -->
        <tr>
          <td style="padding:8px 40px 36px;text-align:center;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
              <tr>
                <td style="background-color:#8E44AD;border-radius:6px;">
                  <a href="{{ config('app.url') }}"
                     style="display:inline-block;padding:16px 40px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;letter-spacing:0.5px;">
                    Alışverişe Başla 🛍️
                  </a>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr><td style="padding:0 40px;"><hr style="border:none;border-top:1px solid #ede8f0;margin:0;"></td></tr>
        <tr>
          <td style="padding:24px 40px;text-align:center;">
            <p style="margin:0 0 8px;font-size:12px;color:#888888;">
              Rose Garden Çiçek Çikolata &nbsp;|&nbsp; Adıyaman, Türkiye<br>
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
