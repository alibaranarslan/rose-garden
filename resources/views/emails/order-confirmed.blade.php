<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Siparişiniz Alındı</title>
</head>
<body style="margin:0;padding:0;background-color:#FAF7F5;font-family:Arial,Helvetica,sans-serif;">
@php
    $branding = \App\Support\SiteBranding::current();
    $siteName = $branding['site_name'] ?? 'Rose Garden';
    $bankDetails = \App\Support\PaymentSettings::bankTransferDetails();
    $transferTimeoutHours = $bankDetails['transfer_timeout_hours'] ?? 72;
    $mailFromAddress = config('mail.from.address');
@endphp

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#FAF7F5;">
  <tr>
    <td align="center" style="padding:30px 16px;">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

        <tr>
          <td style="background-color:#2D0A3E;padding:28px 40px;text-align:center;">
            <p style="margin:0;font-size:10px;letter-spacing:3px;text-transform:uppercase;color:#d4a8e8;">{{ $branding['site_tagline'] ?? 'Çiçek & Çikolata' }}</p>
            <h1 style="margin:6px 0 0;font-size:24px;font-weight:700;color:#ffffff;">{{ $siteName }}</h1>
          </td>
        </tr>
        <tr>
          <td style="background-color:#8E44AD;padding:14px 40px;text-align:center;">
            <p style="margin:0;font-size:14px;font-weight:600;color:#ffffff;">✓ Siparişiniz Alındı!</p>
          </td>
        </tr>

        <tr>
          <td style="padding:32px 40px 0;">
            <p style="margin:0;font-size:15px;color:#333333;">Sayın <strong>{{ $order->sender_name }}</strong>,</p>
            <p style="margin:12px 0 0;font-size:14px;color:#555555;line-height:1.6;">
              Siparişinizi aldık! En kısa sürede hazırlanacak ve belirttiğiniz teslimat tarihinde iletilecektir.
              Aşağıda siparişinizin detaylarını bulabilirsiniz.
            </p>
          </td>
        </tr>

        <tr>
          <td style="padding:24px 40px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f9f5fb;border-radius:6px;border:1px solid #ede8f0;">
              <tr>
                <td style="padding:16px 20px;">
                  <p style="margin:0 0 12px;font-size:13px;font-weight:700;color:#2D0A3E;text-transform:uppercase;letter-spacing:0.5px;">Sipariş Özeti</p>
                  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="font-size:13px;color:#555555;padding:3px 0;">Sipariş No:</td>
                      <td style="font-size:13px;color:#333333;font-weight:700;text-align:right;">{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                      <td style="font-size:13px;color:#555555;padding:3px 0;">Sipariş Tarihi:</td>
                      <td style="font-size:13px;color:#333333;text-align:right;">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                    <tr>
                      <td style="font-size:13px;color:#555555;padding:3px 0;">Teslimat Tarihi:</td>
                      <td style="font-size:13px;color:#333333;text-align:right;">{{ $order->delivery_date?->format('d.m.Y') }}</td>
                    </tr>
                    @if($order->deliveryTimeSlot)
                    <tr>
                      <td style="font-size:13px;color:#555555;padding:3px 0;">Teslimat Saati:</td>
                      <td style="font-size:13px;color:#333333;text-align:right;">{{ $order->deliveryTimeSlot->label ?? $order->deliveryTimeSlot->start_time . ' - ' . $order->deliveryTimeSlot->end_time }}</td>
                    </tr>
                    @endif
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td style="padding:20px 40px 0;">
            <p style="margin:0 0 10px;font-size:13px;font-weight:700;color:#2D0A3E;text-transform:uppercase;letter-spacing:0.5px;">Ürünler</p>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #ede8f0;border-radius:6px;overflow:hidden;">
              <tr style="background-color:#f9f5fb;">
                <td style="padding:10px 16px;font-size:12px;font-weight:700;color:#555555;">Ürün</td>
                <td style="padding:10px 8px;font-size:12px;font-weight:700;color:#555555;text-align:center;">Adet</td>
                <td style="padding:10px 16px;font-size:12px;font-weight:700;color:#555555;text-align:right;">Fiyat</td>
              </tr>
              @foreach($order->items as $item)
              <tr style="border-top:1px solid #ede8f0;">
                <td style="padding:12px 16px;font-size:13px;color:#333333;">
                  {{ $item->product_name }}
                  @if($item->variant_name)
                  <br><span style="font-size:11px;color:#888888;">{{ $item->variant_name }}</span>
                  @endif
                  @if($item->card_message)
                  <br><span style="font-size:11px;color:#8E44AD;font-style:italic;">"{{ $item->card_message }}"</span>
                  @endif
                </td>
                <td style="padding:12px 8px;font-size:13px;color:#333333;text-align:center;">{{ $item->quantity }}</td>
                <td style="padding:12px 16px;font-size:13px;color:#333333;text-align:right;font-weight:600;">{{ number_format($item->total_price, 2, ',', '.') }} ₺</td>
              </tr>
              @endforeach
              <tr style="border-top:1px solid #ede8f0;">
                <td colspan="2" style="padding:10px 16px;font-size:13px;color:#555555;">Ara Toplam</td>
                <td style="padding:10px 16px;font-size:13px;color:#333333;text-align:right;">{{ number_format($order->subtotal, 2, ',', '.') }} ₺</td>
              </tr>
              @if($order->delivery_fee > 0)
              <tr>
                <td colspan="2" style="padding:4px 16px;font-size:13px;color:#555555;">Teslimat</td>
                <td style="padding:4px 16px;font-size:13px;color:#333333;text-align:right;">{{ number_format($order->delivery_fee, 2, ',', '.') }} ₺</td>
              </tr>
              @endif
              @if($order->discount_amount > 0)
              <tr>
                <td colspan="2" style="padding:4px 16px;font-size:13px;color:#8E44AD;">İndirim</td>
                <td style="padding:4px 16px;font-size:13px;color:#8E44AD;text-align:right;">-{{ number_format($order->discount_amount, 2, ',', '.') }} ₺</td>
              </tr>
              @endif
              <tr style="background-color:#f9f5fb;border-top:2px solid #ede8f0;">
                <td colspan="2" style="padding:12px 16px;font-size:14px;font-weight:700;color:#2D0A3E;">Toplam</td>
                <td style="padding:12px 16px;font-size:14px;font-weight:700;color:#2D0A3E;text-align:right;">{{ number_format($order->total, 2, ',', '.') }} ₺</td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td style="padding:20px 40px 0;">
            <p style="margin:0 0 10px;font-size:13px;font-weight:700;color:#2D0A3E;text-transform:uppercase;letter-spacing:0.5px;">Teslimat Bilgileri</p>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f9f5fb;border-radius:6px;border:1px solid #ede8f0;">
              <tr>
                <td style="padding:16px 20px;font-size:13px;color:#555555;line-height:1.7;">
                  <strong style="color:#333333;">{{ $order->recipient_name }}</strong><br>
                  {{ $order->recipient_address }}<br>
                  @if($order->recipient_district) {{ $order->recipient_district }} @endif
                  @if($order->recipient_phone) <br>{{ $order->recipient_phone }} @endif
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td style="padding:20px 40px 0;">
            <p style="margin:0 0 10px;font-size:13px;font-weight:700;color:#2D0A3E;text-transform:uppercase;letter-spacing:0.5px;">Ödeme Bilgisi</p>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f9f5fb;border-radius:6px;border:1px solid #ede8f0;">
              <tr>
                <td style="padding:16px 20px;">
                  @if($order->payment_method === 'bank_transfer')
                    <p style="margin:0 0 10px;font-size:13px;color:#333333;font-weight:600;">💳 Havale / EFT ile ödeme seçildi.</p>
                    <p style="margin:0 0 6px;font-size:13px;color:#555555;">Lütfen <strong>{{ number_format($order->total, 2, ',', '.') }} ₺</strong>'yi aşağıdaki hesaba gönderin:</p>
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                      <tr><td style="font-size:13px;color:#555555;padding:3px 0;">Banka:</td><td style="font-size:13px;color:#333333;padding:3px 12px;">{{ $bankDetails['bank_name'] ?: '—' }}</td></tr>
                      <tr><td style="font-size:13px;color:#555555;padding:3px 0;">Hesap Sahibi:</td><td style="font-size:13px;color:#333333;padding:3px 12px;">{{ $bankDetails['bank_account_holder'] ?: '—' }}</td></tr>
                      <tr><td style="font-size:13px;color:#555555;padding:3px 0;">IBAN:</td><td style="font-size:13px;color:#333333;font-weight:700;padding:3px 12px;">{{ $bankDetails['bank_iban'] ?: '—' }}</td></tr>
                      <tr><td style="font-size:13px;color:#555555;padding:3px 0;">Açıklama:</td><td style="font-size:13px;color:#8E44AD;font-weight:700;padding:3px 12px;">{{ $order->order_number }}</td></tr>
                    </table>
                    <p style="margin:12px 0 0;font-size:12px;color:#e74c3c;">⚠ Ödemeniz {{ $transferTimeoutHours }} saat içinde alınmazsa siparişiniz otomatik iptal edilecektir.</p>
                  @else
                    <p style="margin:0;font-size:13px;color:#333333;">✅ Kredi/Banka Kartı ile ödeme alındı.</p>
                  @endif
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
                  <a href="{{ config('app.url') }}/siparis-takip"
                     style="display:inline-block;padding:14px 32px;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none;">
                    Siparişimi Takip Et
                  </a>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr><td style="padding:0 40px;"><hr style="border:none;border-top:1px solid #ede8f0;margin:0;"></td></tr>
        <tr>
          <td style="padding:24px 40px;text-align:center;">
            <p style="margin:0 0 8px;font-size:12px;color:#888888;">{{ $siteName }} &nbsp;|&nbsp; Adıyaman, Türkiye</p>
            @if ($mailFromAddress)
                <p style="margin:0 0 8px;font-size:12px;color:#888888;">{{ $mailFromAddress }}</p>
            @endif
            <p style="margin:0;font-size:11px;color:#aaaaaa;">&copy; {{ date('Y') }} {{ $siteName }}. Tüm hakları saklıdır.</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
