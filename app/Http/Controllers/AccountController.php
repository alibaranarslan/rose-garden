<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\DataRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $latestOrders = $user->orders()
            ->latest()
            ->take(3)
            ->get();
        $loyaltyPoint = $user->loyaltyPoints;

        return view('account.dashboard', compact('user', 'latestOrders', 'loyaltyPoint'))->with([
            'metaTitle' => 'Hesabım',
            'metaDescription' => 'Rose Garden hesap paneli.',
        ]);
    }

    public function orders()
    {
        $orders = auth()->user()
            ->orders()
            ->latest()
            ->paginate(12);

        return view('account.orders.index', compact('orders'))->with([
            'metaTitle' => 'Siparişlerim',
            'metaDescription' => 'Rose Garden sipariş geçmişi.',
        ]);
    }

    public function orderShow(string $orderNumber)
    {
        $order = auth()->user()
            ->orders()
            ->where('order_number', $orderNumber)
            ->with(['items.product', 'statusHistory', 'deliveryTimeSlot', 'deliveryZone'])
            ->firstOrFail();

        return view('account.orders.show', compact('order'))->with([
            'metaTitle' => 'Sipariş ' . $order->order_number,
            'metaDescription' => 'Sipariş detayları ve durum bilgisi.',
        ]);
    }

    public function favorites()
    {
        $favorites = auth()->user()
            ->favorites()
            ->with(['product.images'])
            ->latest('created_at')
            ->paginate(12);

        return view('account.favorites', compact('favorites'))->with([
            'metaTitle' => 'Favorilerim',
            'metaDescription' => 'Kaydedilen favori ürünleriniz.',
        ]);
    }

    public function loyalty()
    {
        $user = auth()->user();
        $loyaltyPoint = $user->loyaltyPoints;
        $transactions = $user->loyaltyTransactions()
            ->latest('created_at')
            ->take(20)
            ->get();

        return view('account.loyalty', compact('loyaltyPoint', 'transactions'))->with([
            'metaTitle' => 'Puanlarım',
            'metaDescription' => 'Sadakat puan bakiyesi ve hareketleri.',
        ]);
    }

    public function addresses()
    {
        $addresses = auth()->user()
            ->addresses()
            ->latest()
            ->get();

        return view('account.addresses', compact('addresses'))->with([
            'metaTitle' => 'Adreslerim',
            'metaDescription' => 'Kayıtlı teslimat adresleriniz.',
        ]);
    }

    public function profile()
    {
        $user = auth()->user();

        return view('account.profile', compact('user'))->with([
            'metaTitle' => 'Profilim',
            'metaDescription' => 'Hesap profil bilgileriniz.',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
            'phone' => ['nullable', 'string', 'max:30'],
            'marketing_consent' => ['nullable', 'boolean'],
        ]);

        auth()->user()->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'marketing_consent' => (bool) ($data['marketing_consent'] ?? false),
            'marketing_consent_at' => isset($data['marketing_consent']) && $data['marketing_consent'] ? now() : null,
        ]);

        return back()->with('status', __('Profil bilgileriniz güncellendi.'));
    }

    public function kvkkPanel()
    {
        $user = auth()->user();
        $summary = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address_count' => $user->addresses()->count(),
            'order_count' => $user->orders()->count(),
            'loyalty_balance' => (float) ($user->loyaltyPoints?->balance ?? 0),
            'registered_at' => $user->created_at,
            'kvkk_accepted_at' => $user->kvkk_accepted_at,
            'marketing_consent' => (bool) $user->marketing_consent,
        ];

        $requests = $user->dataRequests()->latest()->get();

        return view('account.kvkk', compact('user', 'summary', 'requests'))->with([
            'metaTitle' => 'KVKK ve Gizlilik',
            'metaDescription' => '6698 sayılı KVKK kapsamındaki haklarınızı yönetin.',
        ]);
    }

    public function submitDataRequest(Request $request)
    {
        $user = auth()->user();
        $todayCount = $user->dataRequests()
            ->whereDate('created_at', now()->toDateString())
            ->count();

        if ($todayCount >= 3) {
            return back()->withErrors([
                'type' => __('Günlük veri talebi limitine (3) ulaştınız.'),
            ]);
        }

        $data = $request->validate([
            'type' => ['required', 'in:view,export,delete,consent_withdraw'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user->dataRequests()->create([
            'type' => $data['type'],
            'status' => 'pending',
            'reason' => $data['reason'] ?? null,
        ]);

        return back()->with('status', __('Veri talebiniz alındı.'));
    }

    public function withdrawMarketingConsent()
    {
        $user = auth()->user();

        $user->update([
            'marketing_consent' => false,
            'marketing_consent_at' => now(),
        ]);

        $user->dataRequests()->create([
            'type' => 'consent_withdraw',
            'status' => 'completed',
            'reason' => __('Kullanıcı pazarlama iznini geri çekti.'),
            'completed_at' => now(),
        ]);

        return back()->with('status', __('Pazarlama izni geri çekildi.'));
    }

    public function exportPersonalData(): StreamedResponse
    {
        $user = auth()->user();
        $user->dataRequests()->create([
            'type' => 'export',
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $payload = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'created_at' => $user->created_at?->toIso8601String(),
                'kvkk_accepted_at' => $user->kvkk_accepted_at?->toIso8601String(),
                'marketing_consent' => (bool) $user->marketing_consent,
                'marketing_consent_at' => $user->marketing_consent_at?->toIso8601String(),
            ],
            'addresses' => $user->addresses()->get(['label', 'recipient_name', 'recipient_phone', 'address_line', 'district', 'city', 'postal_code', 'is_default', 'created_at']),
            'orders' => $user->orders()->with('items')->get(),
            'loyalty' => [
                'balance' => (float) ($user->loyaltyPoints?->balance ?? 0),
                'transactions' => $user->loyaltyTransactions()->latest()->get(),
            ],
            'data_requests' => $user->dataRequests()->latest()->get(),
            'exported_at' => now()->toIso8601String(),
        ];

        $filename = 'kvkk-veri-raporu-' . $user->id . '-' . now()->format('YmdHis') . '.json';

        return response()->streamDownload(function () use ($payload) {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function storeAddress(Request $request)
    {
        $data = $this->validateAddress($request);
        $data['user_id'] = auth()->id();

        if ((bool) ($data['is_default'] ?? false)) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        Address::create($data);

        return back()->with('status', __('Adres eklendi.'));
    }

    public function updateAddress(Request $request, Address $address)
    {
        $this->authorizeAddress($address);
        $data = $this->validateAddress($request);

        if ((bool) ($data['is_default'] ?? false)) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        $address->update($data);

        return back()->with('status', __('Adres güncellendi.'));
    }

    public function deleteAddress(Address $address)
    {
        $this->authorizeAddress($address);
        $address->delete();

        return back()->with('status', __('Adres silindi.'));
    }

    public function setDefaultAddress(Address $address)
    {
        $this->authorizeAddress($address);
        auth()->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('status', __('Varsayılan adres güncellendi.'));
    }

    public function reorder(string $orderNumber)
    {
        $order = auth()->user()
            ->orders()
            ->where('order_number', $orderNumber)
            ->with(['items.product'])
            ->firstOrFail();

        foreach ($order->items as $item) {
            if (!$item->product || $item->product->stock_status !== 'in_stock') {
                continue;
            }

            $existing = CartItem::query()
                ->where('user_id', auth()->id())
                ->where('product_id', $item->product_id)
                ->where('variant_id', $item->variant_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $item->quantity);
            } else {
                CartItem::create([
                    'user_id' => auth()->id(),
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'card_message' => $item->card_message,
                ]);
            }
        }

        return redirect()->route('cart')->with('status', __('Ürünler sepetinize eklendi.'));
    }

    private function validateAddress(Request $request): array
    {
        if (!$request->filled('city')) {
            $request->merge(['city' => 'Adiyaman']);
        }

        return $request->validate([
            'label' => ['nullable', 'string', 'max:255'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:30'],
            'address_line' => ['required', 'string', 'max:500'],
            'district' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }

    private function authorizeAddress(Address $address): void
    {
        abort_unless($address->user_id === auth()->id(), 403);
    }
}
