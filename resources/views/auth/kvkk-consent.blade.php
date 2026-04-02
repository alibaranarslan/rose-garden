<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KVKK Aydınlatma Onayı — Rose Garden</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center px-4">

    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-lg p-8 my-8">

        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">KVKK Aydınlatma Onayı</h1>
            <p class="text-gray-500 mt-1 text-sm">Rose Garden Çiçek & Çikolata — Kişisel Verilerin Korunması</p>
        </div>

        <div class="prose prose-sm max-w-none mb-6 text-gray-700 bg-gray-50 rounded-lg p-4 max-h-64 overflow-y-auto text-sm leading-relaxed">
            <h3 class="font-semibold text-gray-900 mb-2">Kişisel Verilerin İşlenmesine İlişkin Aydınlatma Metni</h3>

            <p>Rose Garden Çiçek ve Çikolata olarak, 6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) kapsamında veri sorumlusu sıfatıyla, kişisel verilerinizi aşağıda açıklanan amaçlar ve kapsamda işlemekteyiz.</p>

            <p><strong>İşlenen Kişisel Veriler:</strong> Ad, soyad, e-posta adresi, telefon numarası, teslimat adresi ve sipariş geçmişi bilgileriniz.</p>

            <p><strong>İşleme Amaçları:</strong></p>
            <ul>
                <li>Sipariş oluşturma, teslimat ve müşteri hizmetleri süreçleri</li>
                <li>Yasal yükümlülüklerin yerine getirilmesi</li>
                <li>Müşteri memnuniyeti analizleri</li>
                <li>İzin vermeniz halinde: kişiselleştirilmiş pazarlama iletişimi</li>
            </ul>

            <p><strong>Veri Güvenliği:</strong> Verileriniz SSL şifreleme ile korunmakta, yetkisiz erişime karşı teknik ve idari tedbirler uygulanmaktadır.</p>

            <p><strong>Haklarınız:</strong> KVKK'nın 11. maddesi kapsamında verilerinize erişme, düzeltme, silme, aktarım ve itiraz haklarına sahipsiniz. Taleplerinizi <a href="mailto:kvkk@rosegardencicek.com">kvkk@rosegardencicek.com</a> adresine iletebilirsiniz.</p>
        </div>

        <form method="POST" action="{{ route('kvkk.consent.store') }}">
            @csrf

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @error('kvkk_accepted')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    {{ $message }}
                </div>
            @enderror

            <label class="flex items-start gap-3 mb-6 cursor-pointer">
                <input type="checkbox" name="kvkk_accepted" value="1"
                       class="mt-1 rounded border-gray-300 text-rose-600 focus:ring-rose-500"
                       id="kvkk_accepted">
                <span class="text-sm text-gray-700">
                    <strong>KVKK Aydınlatma Metnini</strong> okudum ve kişisel verilerimin yukarıda belirtilen amaçlarla işlenmesini onaylıyorum.
                </span>
            </label>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit"
                        class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                    Kabul Ediyorum ve Devam Et
                </button>

                <a href="{{ route('kvkk.consent.reject') }}"
                   class="flex-1 text-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-3 px-6 rounded-lg transition-colors">
                    Reddediyorum (Çıkış Yap)
                </a>
            </div>
        </form>
    </div>

</body>
</html>
