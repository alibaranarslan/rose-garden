<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sipariş Durum Güncellemesi</title>
</head>
<body style="margin:0;padding:0;background-color:#FAF7F5;font-family:Arial,Helvetica,sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#FAF7F5;">
  <tr>
    <td align="center" style="padding:30px 16px;">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

        <tr>
          <td style="background-color:#2D0A3E;padding:28px 40px;text-align:center;">
            <p style="margin:0;font-size:10px;letter-spacing:3px;text-transform:uppercase;color:#d4a8e8;">ÇİÇEK &amp; ÇİKOLATA</p>
            <h1 style="margin:6px 0 0;font-size:24px;font-weight:700;color:#ffffff;">Rose Garden</h1>
          </td>
        </tr>
        <tr>
          <td style="background-color:#8E44AD;padding:14px 40px;text-align:center;">
            <p style="margin:0;font-size:14px;font-weight:600;color:#ffffff;">Sipariş Durum Güncellemesi</p>
          </td>
        </tr>

        <tr>
          <td style="padding:32px 40px 0;">
            <p style="margin:0;font-size:15px;color:#333333;">Sayın <strong>{{ $order->sender_name }}</strong>,</p>
            <p style="margin:12px 0 0;font-size:14px;color:#555555;line-height:1.6;">
              <strong>#{{ $order->order_number }}</strong> numaralı siparişinizin durumu güncellendi.
            </p>
          </td>
        </tr>

        <tr>
          <td style="padding:24px 40px;text-align:center;">
            @php
              $statusColors = [
                'paid'        => ['bg' => '#27ae60', 'icon' => '✓'],
                'preparing'   => ['bg' => '#f39c12', 'icon' => '🌸'],
                'on_the_way'  => ['bg' => '#2980b9', 'icon' => '🚚'],
                'delivered'   => ['bg' => '#27ae60', 'icon' => '✓'],
                'cancelled'   => ['bg' => '#e74c3c', 'icon' => '✗'],
              ];
              $statusStyle = $statusColors[$status ?? ''] ?? ['bg' => '#8E44AD', 'icon' => '•'];
            @endphp
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
              <tr>
                <td style="background-color:{{ $statusStyle['bg'] }};border-radius:50px;padding:12px 28px;">
                  <span style="font-size:16px;font-weight:700;color:#ffffff;">
                    {{ $statusStyle['icon'] }} &nbsp; {{ $statusLabel ?? $status ?? '' }}
                  </span>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td style="padding:0 40px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f9f5fb;border-radius:6px;border:1px solid #ede8f0;">
              <tr>
                <td style="padding:18px 20px;font-size:13px;color:#555555;line-height:1.7;text-align:center;">
                  @switch($status ?? '')
                    @case('paid')
                      Ödemeniz onaylandı. Siparişiniz hazırlanmaya başlayacak.
                      @break
                    @case('preparing')
                      Siparişiniz floristlerimiz tarafından özenle hazırlanıyor. 🌸
                      @break
                    @case('on_the_way')
                      Siparişiniz yola çıktı! Teslimat görevlimiz yakında kapınızda olacak. 🚚
                      @break
                    @case('delivered')
                      Siparişiniz teslim edildi. Alışverişiniz için teşekkür ederiz! 💐
                      @break
                    @case('cancelled')
                      Siparişiniz iptal edildi. Herhangi bir sorunuz varsa bizimle iletişime geçebilirsiniz.
                      @break
                    @default
                      Siparişinizde bir güncelleme var.
                  @endswitch
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td style="padding:0 40px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f9f5fb;border-radius:6px;border:1px solid #ede8f0;">
              <tr>
                <td style="padding:16px 20px;">
                  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="font-size:13px;color:#555555;padding:3px 0;">Sipariş No:</td>
                      <td style="font-size:13px;color:#333333;font-weight:700;text-align:right;">{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                      <td style="font-size:13px;color:#555555;padding:3px 0;">Teslimat Tarihi:</td>
                      <td style="font-size:13px;color:#333333;text-align:right;">{{ $order->delivery_date?->format('d.m.Y') }}</td>
                    </tr>
                    <tr>
                      <td style="font-size:13px;color:#555555;padding:3px 0;">Alıcı:</td>
                      <td style="font-size:13px;color:#333333;text-align:right;">{{ $order->recipient_name }}</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td style="padding:28px 40px;text-align:center;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
              <tr>
                <td style="background-color:#8E44AD;border-radius:6px;">
                  <a href="{{ config('app.url') }}/hesabim/siparis/{{ $order->order_number }}"
                     style="display:inline-block;padding:14px 32px;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none;">
                    Sipariş Detaylarını Gör
                  </a>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr><td style="padding:0 40px;"><hr style="border:none;border-top:1px solid #ede8f0;margin:0;"></td></tr>
        <tr>
          <td style="padding:24px 40px;text-align:center;">
            <p style="margin:0 0 8px;font-size:12px;color:#888888;">Rose Garden Çiçek Çikolata &nbsp;|&nbsp; Adıyaman, Türkiye</p>
            <p style="margin:0;font-size:11px;color:#aaaaaa;">&copy; {{ date('Y') }} Rose Garden. Tüm hakları saklıdır.</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
