# Rose Garden Canliya Alma Dis Erisim Checklist

Bu dokuman, musterinin admin panel disinda saglamasi gereken hesap, panel ve entegrasyon erisimlerini listeler. Admin panelde girilebilen icerik ve isletme verileri bu listenin disindadir.

## Zorunlu Erisimler

| Alan | Nereden erisim gerekir | Gerekli bilgiler | Baglanti / kurulum |
|---|---|---|---|
| Domain / DNS | Domain saglayici paneli: Natro, Turhost, GoDaddy, Cloudflare vb. | Domain panel girisi, DNS yonetim yetkisi | `A` kaydi sunucu IP adresine yonlenir. `www` icin `CNAME` veya ayri `A` kaydi girilir. |
| Hosting / Sunucu | VPS, hosting paneli veya SSH | Sunucu IP, SSH kullanici/sifre veya key, panel bilgisi | Laravel production kurulumu, `.env`, queue, scheduler, storage link ve SSL islemleri burada yapilir. |
| SSL | Hosting paneli veya Cloudflare | Domain dogrulama yetkisi | Let’s Encrypt/AutoSSL acilir. `.env` icinde `APP_URL=https://alanadi.com` olarak sabitlenir. |
| Veritabani | Hosting paneli, MySQL paneli veya SSH | DB adi, kullanici, sifre, host, port | `.env`: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`. |
| Mail / SMTP | Mail saglayici paneli: domain mail, Yandex, Google Workspace veya SMTP servisi | SMTP host, port, kullanici, sifre, encryption, gonderici e-posta | Admin `/admin/email-settings` uzerinden girilebilir. Gerekirse `.env`: `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`. |

## Canli Ticari Entegrasyonlar

| Alan | Nereden erisim gerekir | Gerekli bilgiler | Baglanti / kurulum |
|---|---|---|---|
| PayTR | PayTR merchant paneli | `Merchant ID`, `Merchant Key`, `Merchant Salt`, test/canli durumu | Admin `/admin/payment-settings` veya `.env`: `PAYTR_MERCHANT_ID`, `PAYTR_MERCHANT_KEY`, `PAYTR_MERCHANT_SALT`, `PAYTR_TEST_MODE=false`. Callback URL: `https://domain.com/api/paytr/callback`. |
| SMS | SMS saglayici paneli | API URL, kullanici adi, sifre/token, abone no, sender title | Admin `/admin/sms-settings` uzerinden girilebilir. Gerekirse `.env`: `SMS_API_URL`, `SMS_USERNAME`, `SMS_PASSWORD`, `SMS_SUBSCRIBER_NO`, `SMS_SENDER_TITLE`, `SMS_ENABLED=true`. |

## Analitik ve Google Servisleri

| Alan | Nereden erisim gerekir | Gerekli bilgiler | Baglanti / kurulum |
|---|---|---|---|
| Google Analytics | Google Analytics hesabi | GA4 Measurement ID: `G-XXXX` | Admin `/admin/seo-settings` veya `.env`: `GOOGLE_ANALYTICS_ID`. |
| Google Search Console | Search Console hesabi | Dogrulama meta kodu veya DNS TXT kaydi | Meta kod admin `/admin/seo-settings` icine girilir. DNS dogrulamasi gerekiyorsa domain DNS paneline TXT kaydi eklenir. |
| Google OAuth | Google Cloud Console | Client ID, Client Secret | Opsiyoneldir. `.env`: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`. Redirect URL: `https://domain.com/auth/google/callback`. |
| Google Maps | Google Cloud Console | Maps API key | Opsiyoneldir. `.env`: `GOOGLE_MAPS_KEY`. |
| Google Translate | Google Cloud Console | Translate API key | Opsiyoneldir. `.env`: `GOOGLE_TRANSLATE_API_KEY`. |

## Opsiyonel Altyapi Servisleri

| Alan | Nereden erisim gerekir | Gerekli bilgiler | Baglanti / kurulum |
|---|---|---|---|
| Dosya depolama / S3 | AWS veya S3 uyumlu servis paneli | Access key, secret, bucket, region, endpoint | Lokal `public` storage yeterliyse gerekmez. Harici storage istenirse `.env` `AWS_*` alanlari girilir. |
| Cloudflare / CDN | Cloudflare paneli | DNS yonetimi, SSL mode, proxy durumu | Domain Cloudflare’a alinirsa DNS burada yonetilir. SSL mode genelde `Full` veya `Full strict` olmalidir. |
| Error monitoring | Sentry vb. servis paneli | DSN | Opsiyoneldir. `.env`: `SENTRY_LARAVEL_DSN`. |

## Minimum Canliya Alma Seti

Canliya almak icin asgari dis erisimler:

- Domain / DNS paneli.
- Hosting veya sunucu SSH/panel erisimi.
- Production veritabani bilgileri.
- SSL kurulumu.
- SMTP/mail gonderim bilgileri.

PayTR, SMS, Analytics ve Search Console isletme kararina gore canli oncesi baglanmalidir. PayTR aktif kartli odeme icin zorunludur; sadece havale ile baslanacaksa PayTR daha sonra baglanabilir.
