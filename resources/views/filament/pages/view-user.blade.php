<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Customer Info --}}
        <x-filament::section heading="Müşteri Bilgileri">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Ad Soyad</p>
                    <p class="font-medium">{{ $record->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">E-posta</p>
                    <p class="font-medium">{{ $record->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Telefon</p>
                    <p class="font-medium">{{ $record->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Kayıt Tarihi</p>
                    <p class="font-medium">{{ $record->created_at->format('d.m.Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">KVKK Onayı</p>
                    <p class="font-medium">{{ $record->kvkk_accepted_at?->format('d.m.Y') ?? 'Onaylanmadı' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pazarlama İzni</p>
                    <p class="font-medium">{{ $record->marketing_consent ? 'Evet' : 'Hayır' }}</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Loyalty Points --}}
        <x-filament::section heading="Paraçiçek Puanları">
            @if($record->loyaltyPoints)
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-primary-600">₺{{ number_format($record->loyaltyPoints->balance, 2) }}</p>
                        <p class="text-sm text-gray-500 mt-1">Mevcut Bakiye</p>
                    </div>
                    <div class="text-center p-4 bg-success-50 dark:bg-success-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-success-600">₺{{ number_format($record->loyaltyPoints->total_earned, 2) }}</p>
                        <p class="text-sm text-gray-500 mt-1">Toplam Kazanılan</p>
                    </div>
                    <div class="text-center p-4 bg-warning-50 dark:bg-warning-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-warning-600">₺{{ number_format($record->loyaltyPoints->total_spent, 2) }}</p>
                        <p class="text-sm text-gray-500 mt-1">Toplam Kullanılan</p>
                    </div>
                </div>
            @else
                <p class="text-gray-500">Henüz puan işlemi yok.</p>
            @endif
        </x-filament::section>

        {{-- Orders --}}
        <x-filament::section heading="Son Siparişler">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-2">Sipariş No</th>
                        <th class="pb-2">Tutar</th>
                        <th class="pb-2">Durum</th>
                        <th class="pb-2">Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($record->orders()->latest()->limit(10)->get() as $order)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2">{{ $order->order_number }}</td>
                            <td class="py-2">₺{{ number_format($order->total, 2) }}</td>
                            <td class="py-2">{{ $order->status }}</td>
                            <td class="py-2">{{ $order->created_at->format('d.m.Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::section>
    </div>
</x-filament-panels::page>
