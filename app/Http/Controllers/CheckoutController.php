<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use App\Services\PaytrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private PaytrService $paytr) {}

    public function index(): View
    {
        return view('checkout.index');
    }

    public function processPayment(Order $order): View|RedirectResponse
    {
        try {
            $token    = $this->paytr->createToken($order);
            $iframeUrl = $this->paytr->getIframeUrl($token);

            return view('checkout.payment', [
                'order'     => $order,
                'iframeUrl' => $iframeUrl,
                'token'     => $token,
            ]);
        } catch (\Exception $e) {
            Log::error('Ödeme başlatılamadı', [
                'order_id' => $order->id,
                'message'  => $e->getMessage(),
            ]);

            return redirect()->route('checkout')
                ->with('error', 'Ödeme başlatılırken bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    public function success(Request $request): View
    {
        $orderNumber = $request->query('merchant_oid') ?? $request->query('order');
        $order = $orderNumber
            ? Order::where('order_number', $orderNumber)->first()
            : null;

        $bankInfo = [
            'bank_name' => Setting::get('payment', 'bank_name', 'Ziraat Bankasi'),
            'bank_iban' => Setting::get('payment', 'bank_iban', ''),
            'bank_holder' => Setting::get('payment', 'bank_holder', 'Rose Garden Cicekcilik'),
        ];

        return view('checkout.success', compact('order', 'bankInfo'));
    }

    public function fail(Request $request): View
    {
        $orderNumber = $request->query('merchant_oid');
        $order = $orderNumber
            ? Order::where('order_number', $orderNumber)->first()
            : null;

        return view('checkout.fail', compact('order'));
    }
}
