# Rose Garden Turhost Migration Runbook - 2026-07-10

## Amaç

Rose Garden e-ticaret uygulamasını Turhost ortamına ADH haber sitesiyle çakışmadan taşımak ve production operasyonlarını çalışır hale getirmek.

## Uygulama Profili

- Framework: Laravel 11
- PHP: 8.2+
- Frontend build: Vite
- Queue: database
- Admin: Filament
- Kritik scheduled işler:
  - `cart:detect-abandoned`
  - `cart:send-reminders`
  - `events:send-reminders`
  - `loyalty:expire`
  - `transfers:check-timeout`
  - `sitemap:generate`
- Queue kullanan işler:
  - sipariş/banka transferi bildirimleri
  - terk edilmiş sepet hatırlatmaları
  - event reminders
  - analytics page views

## ADH ile Aynı Turhost Hesabında Konumlandırma

İki Laravel uygulaması birbirine karıştırılmamalı.

Önerilen dizin modeli:

```text
/home/USER/apps/adh
/home/USER/apps/rose-garden
```

Önerilen public root modeli:

```text
adiyamandijitalhaber.com.tr  -> /home/USER/apps/adh/public
adiyamancicekcisi.com.tr     -> /home/USER/apps/rose-garden/public
```

Turhost paneli addon domain/subdomain document root seçimine izin vermiyorsa `public_html` içine Laravel kökü taşınmamalı; mümkünse symlink veya panel document root ayarı kullanılmalı.

## Production `.env` Minimumları

```env
APP_NAME="Rose Garden Cicek Cikolata"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://adiyamancicekcisi.com.tr
APP_TIMEZONE=Europe/Istanbul

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

CACHE_STORE=file
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
QUEUE_CONNECTION=database
SCHEDULE_CACHE_STORE=file
SCHEDULE_QUEUE_WORKER=false

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@adiyamancicekcisi.com.tr
MAIL_FROM_NAME="${APP_NAME}"
```

## Worker Modeli

### VPS / Supervisor Varsa

`SCHEDULE_QUEUE_WORKER=false` kalır.

Supervisor command:

```bash
php artisan queue:work database --queue=default,analytics --sleep=3 --tries=3 --max-time=3600
```

### Shared Hosting / Supervisor Yoksa

`SCHEDULE_QUEUE_WORKER=true` açılır.

Tek cron yeterlidir:

```bash
* * * * * cd /home/USER/apps/rose-garden && /usr/bin/php artisan schedule:run >> storage/logs/cron.log 2>&1
```

Bu modda scheduler her dakika kısa ömürlü worker çalıştırır:

```bash
php artisan queue:work database --queue=default,analytics --sleep=1 --tries=3 --max-time=50 --stop-when-empty
```

## Deploy Sırası

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize
php artisan sitemap:generate
php artisan deploy:verify --base-url=https://adiyamancicekcisi.com.tr
```

Not: İlk production kurulumu dışında `key:generate --force` tekrar çalıştırılmamalıdır; mevcut `APP_KEY` değişirse şifreli veriler ve sessionlar bozulabilir.

## Panelden Kurulacak Dış Servisler

- Domain/DNS
- SSL
- MySQL
- SMTP
- PayTR
- SMS
- Google Analytics/Search Console
- Google Maps/OAuth opsiyonel

## Kabul Kriterleri

- `/` 200 döner.
- `/admin` erişilebilir.
- `/health` `status=ok`, `database=ok`, `cache=ok` döner.
- `php artisan schedule:list` scheduled işleri gösterir.
- Shared hosting modunda `queue:work ... --stop-when-empty` schedule listesinde görünür.
- `php artisan queue:failed` boş veya açıklanabilir olur.
- Ürün listeleme, ürün detay, sepet, checkout, login/register ve admin dashboard smoke geçer.
- SSL aktif olur ve `APP_URL` HTTPS ile uyumludur.

## Turhost Panelinde Kontrol Edilecekler

- PHP 8.2 veya 8.3 seçilebiliyor mu?
- SSH var mı?
- Composer terminalden çalışıyor mu?
- Cron en az dakikada bir çalıştırılabiliyor mu?
- Addon domain document root Laravel `public` dizinine verilebiliyor mu?
- Node/npm serverda var mı? Yoksa build dosyaları lokalde üretilip yüklenecek.

