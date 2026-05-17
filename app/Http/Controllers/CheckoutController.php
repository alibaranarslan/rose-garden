<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaytrService;
use App\Support\PaymentSettings;
use App\Support\StorefrontLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Owns checkout continuation surfaces after the canonical checkout entry hands off to Livewire.
 *
 * Public `/odeme` entry is route closure -> resources/views/checkout/index.blade.php ->
 * App\Livewire\CheckoutWizard. This controller starts at payment handoff and success/fail routes.
 */
class CheckoutController extends Controller
{
    public function index(): View
    {
        // Legacy helper only. Canonical public checkout entry uses the route closure -> checkout.index shell.
        return view('checkout.index');
    }

    public function processPayment(Order $order, PaytrService $paytr): View|RedirectResponse
    {
        // Payment continuation begins only after CheckoutWizard has created the order.
        $this->authorizeCheckoutAccess($order);

        if ($order->payment_method !== 'credit_card') {
            return redirect()
                ->to(StorefrontLocale::route('checkout.success', ['order' => $order->order_number]))
                ->with('error', __('Bu sipariş için kart ile ödeme kullanılmıyor.'));
        }

        if (! PaymentSettings::isPaytrConfigured()) {
            return redirect()
                ->to(StorefrontLocale::route('checkout'))
                ->with('error', __('Kart ile ödeme henüz aktif değil. Lütfen havale/EFT seçeneğini kullanın.'));
        }

        try {
            $token = $paytr->createToken($order);
            $iframeUrl = $paytr->getIframeUrl($token);

            return view('checkout.payment', [
                'order' => $order,
                'iframeUrl' => $iframeUrl,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            Log::error('Ödeme başlatılamadı', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->to(StorefrontLocale::route('checkout'))
                ->with('error', __('Ödeme başlatılırken bir hata oluştu. Lütfen tekrar deneyin.'));
        }
    }

    public function success(Request $request): View
    {
        // Success screen is controller-owned; it is not rendered by CheckoutWizard directly.
        $orderNumber = $request->query('merchant_oid') ?? $request->query('order');
        $order = $orderNumber
            ? Order::where('order_number', $orderNumber)->first()
            : null;

        $bankInfo = PaymentSettings::bankTransferDetails();

        return view('checkout.success', compact('order', 'bankInfo'));
    }

    public function fail(Request $request): View
    {
        // Failure screen is the controller-owned result surface for payment handoff errors or declines.
        $orderNumber = $request->query('merchant_oid');
        $order = $orderNumber
            ? Order::where('order_number', $orderNumber)->first()
            : null;

        return view('checkout.fail', compact('order'));
    }

    private function authorizeCheckoutAccess(Order $order): void
    {
        if ($order->user_id) {
            abort_unless(auth()->check() && auth()->id() === $order->user_id, 403);

            return;
        }

        abort_unless(session('last_order_number') === $order->order_number, 403);
    }
}
