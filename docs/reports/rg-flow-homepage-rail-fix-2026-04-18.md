# RG Flow Homepage Rail Fix - 2026-04-18

## Amac

Homepage rail interaction tarafinda smoke sırasında gorulen Alpine/runtime kirigini kapatmak. Hedef, `scrollRail is not defined`, `canPrev is not defined` ve `canNext is not defined` hatalarini kaldirmak; bunu yaparken homepage kompozisyonuna veya katalog mantigina dokunmamak.

## Root cause

Kirik, rail component'inin `x-data="scrollRail()"` ile bir runtime symbol beklemesi ama bu symbol'un browser tarafinda garanti edilmemesiydi. Controller source tarafinda vardI, fakat template tarafinin bekledigi isim runtime scope icinde guvenli degildi.

Bu yuzden Alpine component'i daha kurulmadan expression resolve asamasinda patliyordu ve `canPrev` / `canNext` state'i hic olusamiyordu.

## Duzeltme yaklasimi

- `scrollRail` factory tek yerde tanimli tutuldu.
- AynI factory hem `Alpine.data('scrollRail', ...)` olarak kaydedildi hem de `window.scrollRail` olarak expose edildi.
- Boylece template tarafindaki `x-data="scrollRail()"` ifadesi runtime'da gercekten cozulur hale geldi.
- Interaction modeli degistirilmedi; sadece runtime register/resolve kademesi saglamlastirildi.

## Etkilenen dosyalar

- `C:\nwp0203\rose-garden\resources\js\app.js`
- `C:\nwp0203\rose-garden\public\build\assets\app-BXiSn1_f.js` (build output)

## Yapilan dogrulamalar

- `php artisan test --filter=PublicSurfaceSmokeTest`
- `php artisan test --filter=StorefrontCompatibilityTest`
- `php artisan test --filter=HeaderThemeTest`
- `php artisan test --filter=Storefront`
- `npm run build`

Build cikti kontrolunde yeni bundle icinde `window.scrollRail` alias'i olustugu dogrulandi.

## Kalan riskler

- Browser console hicbir tarayici seansinda tekrar acilip canli olarak izlenmedi; runtime fix source ve build seviyesinde dogrulandi.
- Eger kullanici tarafinda eski cached bundle varsa, ilk yenilemede eski asset gorulebilir.

## Sonraki guvenli adim

Homepage'i bir kez daha dar manuel smoke ile acip rail oklarinin calistigini ve console'un temiz kaldigini son kez gozle dogrula. Bu dogrulama temizse tur kapatilabilir.
