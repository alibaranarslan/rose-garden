@php
    $mailAddress = config('mail.from.address', 'info@rosegardencicekcilik.com.tr');
    $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?: 'rosegardencicekcilik.com.tr';
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{{ $subject ?? 'Rose Garden Bildirim' }}</title>
<!--[if mso]>
<noscript>
<xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
</noscript>
<![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#FAF7F5;font-family:Arial,Helvetica,sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">

<!-- Outer wrapper -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#FAF7F5;min-width:100%;">
  <tr>
    <td align="center" style="padding:30px 16px;">

      <!-- Email container -->
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

        <!-- HEADER -->
        <tr>
          <td style="background-color:#2D0A3E;padding:28px 40px;text-align:center;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td align="center">
                  <p style="margin:0;font-size:10px;letter-spacing:3px;text-transform:uppercase;color:#d4a8e8;font-weight:400;">ÇIÇEK &amp; ÇİKOLATA</p>
                  <h1 style="margin:6px 0 0;font-size:24px;font-weight:700;color:#ffffff;letter-spacing:1px;">Rose Garden</h1>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- SUBJECT BAR -->
        <tr>
          <td style="background-color:#8E44AD;padding:14px 40px;text-align:center;">
            <p style="margin:0;font-size:14px;font-weight:600;color:#ffffff;letter-spacing:0.5px;">{{ $subject ?? '' }}</p>
          </td>
        </tr>

        <!-- BODY -->
        <tr>
          <td style="padding:36px 40px;color:#333333;">
            {!! $body ?? '' !!}
          </td>
        </tr>

        @if(!empty($actionUrl) && !empty($actionText))
        <!-- CTA BUTTON -->
        <tr>
          <td style="padding:0 40px 36px;text-align:center;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
              <tr>
                <td style="background-color:#8E44AD;border-radius:6px;">
                  <a href="{{ $actionUrl }}"
                     style="display:inline-block;padding:14px 32px;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none;letter-spacing:0.5px;">
                    {{ $actionText }}
                  </a>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        @endif

        <!-- DIVIDER -->
        <tr>
          <td style="padding:0 40px;">
            <hr style="border:none;border-top:1px solid #ede8f0;margin:0;">
          </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td style="padding:24px 40px;text-align:center;">
            <p style="margin:0 0 8px;font-size:12px;color:#888888;">
              Rose Garden Çiçek Çikolata &nbsp;|&nbsp; Adıyaman, Türkiye
            </p>
            <p style="margin:0 0 8px;font-size:12px;color:#888888;">
              <a href="mailto:{{ $mailAddress }}" style="color:#8E44AD;text-decoration:none;">{{ $mailAddress }}</a>
              &nbsp;|&nbsp;
              <a href="{{ config('app.url') }}" style="color:#8E44AD;text-decoration:none;">{{ $appHost }}</a>
            </p>
            <p style="margin:8px 0 0;font-size:11px;color:#aaaaaa;">
              Bu e-postayı almak istemiyorsanız
              <a href="{{ config('app.url') }}/hesabim/profilim" style="color:#aaaaaa;text-decoration:underline;">aboneliğinizi iptal edebilirsiniz</a>.
              &nbsp;&copy; {{ date('Y') }} Rose Garden. Tüm hakları saklıdır.
            </p>
          </td>
        </tr>

      </table>
      <!-- /Email container -->

    </td>
  </tr>
</table>

</body>
</html>
