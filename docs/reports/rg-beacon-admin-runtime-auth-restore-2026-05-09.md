# RG Beacon Admin Runtime/Auth Restore - 2026-05-09

## Amac

Admin content scope'unu bloklayan runtime ve auth temelini restore etmek:

- aktif local DB baseline'ini netlestirmek
- CLI/artisan ile running server'in hangi DB'ye baktigini aciklamak
- `admin@admin.com / password` kullanicisini aktif baseline uzerinde dogrulamak
- browser-level admin login smoke'u tekrar calistirmak

## Baslangictaki Runtime/Auth Blocker

Baslangicta iki ayri problem vardi:

1. `.env` MySQL `rg_database` gosteriyordu, ancak `127.0.0.1:3306` kapaliydi.
2. Running server buna ragmen canliydi ve `/health` uzerinden `database=ok`, `queue_driver=sync` donuyordu; yani local HTTP runtime ile CLI/artisan ayni baseline'a bakmiyordu.

Sonuc:

- CLI/artisan MySQL baglanti hatasina dusuyordu.
- Running server farkli bir local baseline ile calisiyordu.
- Admin login denemesi gecersiz credential olarak gorunuyordu; aktif runtime DB'sinde admin kullanicisi eksikti.

## Aktif DB Karari

Bu tur icin aktif local baseline olarak SQLite secildi.

Gerekce:

- MySQL 3306 kapali ve onarim bu scope disindaydi.
- Running server fiilen SQLite-benzeri bir local smoke profiliyle calisiyordu:
  - `/health` -> `database=ok`
  - `/health` -> `queue_driver=sync`
- Repo icinde aktif SQLite dosyasi mevcuttu:
  - `C:\nwp0203\rose-garden\database\database.sqlite`

Bu nedenle lokal admin smoke icin MySQL'i zorlamak yerine runtime ile uyumlu SQLite baseline resmilestirildi.

## CLI/Artisan DB Baseline

`.env` su profile cekildi:

- `DB_CONNECTION=sqlite`
- `DB_DATABASE=C:/nwp0203/rose-garden/database/database.sqlite`
- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=sync`
- `APP_URL=http://127.0.0.1:8001`

CLI dogrulamasi:

- `php artisan env` -> `local`
- artisan bootstrap config kontrolu -> `database.default = sqlite`
- `php artisan test --filter=AdminPersistFixTest` -> gecti

Not:

- MySQL parametreleri dosyada tamamen silinmedi; ancak aktif baseline artik SQLite.
- Bu local smoke karari MariaDB repair yerine guvenli fallback olarak uygulandi.

## Running Server DB Baseline

Running server process:

- `php.exe -S 127.0.0.1:8001 ...`

HTTP runtime dogrulamasi:

- `http://127.0.0.1:8001/health` -> `database=ok`
- `http://127.0.0.1:8001/health` -> `queue_driver=sync`
- `http://127.0.0.1:8001/admin/login` -> `200`

Bu sinyaller, running server'in de artik ayni local SQLite smoke baseline'i gordugunu gosteriyor.

## Admin User Durumu

Aktif SQLite DB'de baslangicta `admin@admin.com` kullanicisi yoktu.

`AdminUserSeeder` beklentisi:

- email: `admin@admin.com`
- password: `password`
- `is_admin = true`
- `is_active = true`
- role: `super_admin`

Restore sonrasi dogrulanan state:

- `id = 1`
- `email = admin@admin.com`
- `is_admin = true`
- `is_active = true`
- `Hash::check('password', ...) = true`
- roles: `super_admin`

## Yapilan Restore Islemi

Destructive islem yapilmadi.

Yapilanlar:

1. `.env` local baseline'i SQLite smoke profiline alindi.
2. Aktif SQLite DB uzerinde role ve admin user restore edildi:
   - `Database\Seeders\RoleSeeder`
   - `Database\Seeders\AdminUserSeeder`

Bu restore `updateOrCreate` tabanli oldugu icin mevcut veri setini sifirlamadi veya silmedi.

## Browser Login Sonucu

Headless Playwright ile browser-level login smoke calistirildi.

Akis:

1. `GET /admin/login`
2. `admin@admin.com / password` form submit
3. Livewire `POST /livewire/update`
4. redirect -> `http://127.0.0.1:8001/admin`

Sonuc:

- login basarili
- invalid credential mesaji yok
- dashboard shell / operasyon masasi render oldu

## Yapilan Dogrulamalar

1. `php artisan env`
2. artisan bootstrap config kontrolu ile aktif `database.default`, session/cache/queue profili
3. `Test-NetConnection 127.0.0.1 -Port 3306` -> `False`
4. `Invoke-WebRequest http://127.0.0.1:8001/health`
5. `Invoke-WebRequest http://127.0.0.1:8001/admin/login`
6. SQLite baseline uzerinde role + admin user restore scripti
7. Playwright browser-level login smoke
8. `php artisan test --filter=AdminPersistFixTest`

## Kalan Riskler

1. MySQL/MariaDB halen kapali; MySQL tabanli bir local operasyon beklentisi varsa ayri onarim turu gerekir.
2. Running server baseline'i su an local smoke icin SQLite olarak secildi; ekip MySQL'e geri donmek isterse `.env` ve servis baseline'i yeniden uyumlanmali.
3. Bu tur yalnizca runtime/auth restore icindi; content resource browser QA tekrar oynatilmadi.

## Sonraki Guvenli Adim

1. Ayni local SQLite baseline ile admin content operations scope'unu yeniden oyna.
2. Blog/page/special occasion/layout resource login-sonrasi browser smoke'u tekrar calistir.
3. MySQL zorunluysa bunu ayri bir operasyon/hardening turu olarak ele al; content QA ile karistirma.
