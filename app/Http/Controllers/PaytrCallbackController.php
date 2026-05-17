<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\LoyaltyTransaction;
use App\Notifications\OrderStatusNotification;
use App\Services\LoyaltyService;
use App\Services\PaytrService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaytrCallbackController extends Controller
{
    public function handle(Request $request, PaytrService $paytr, LoyaltyService $loyalty): Response
    {
        Log::info('PayTR callback alındı', $request->except(['paytr_token']));

        if (!$paytr->verifyCallback($request)) {
            Log::warning('PayTR callback doğrulama başarısız');
            return response('FAIL', 400);
        }

        $merchantOid = $request->input('merchant_oid');
        $status      = $request->input('status');
        $totalAmount = $request->input('total_amount');

        $order = Order::where('order_number', $merchantOid)->with('user', 'payment')->first();

        if (!$order) {
            Log::error('PayTR callback: sipariş bulunamadı', ['merchant_oid' => $merchantOid]);
            return response('OK'); // Always return OK to prevent retries
        }

        try {
            if ($status === 'success') {
                $this->handleSuccess($order, $request, $loyalty);
            } else {
                $this->handleFailure($order, $request);
            }
        } catch (\Exception $e) {
            Log::error('PayTR callback işleme hatası', [
                'order_id' => $order->id,
                'message'  => $e->getMessage(),
            ]);
        }

        // PayTR requires "OK" response
        return response('OK');
    }

    private function handleSuccess(Order $order, Request $request, LoyaltyService $loyalty): void
    {
        DB::transaction(function () use ($order, $request, $loyalty) {
            $existingPayment = Payment::where('order_id', $order->id)->first();

            if ($order->status === 'paid' && $existingPayment?->status === 'completed') {
                return;
            }

            $order->update(['status' => 'paid']);

            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_method'    => $order->payment_method ?: 'credit_card',
                    'amount'            => $order->total,
                    'status'            => 'completed',
                    'transaction_id'    => $request->input('payment_type') . '_' . time(),
                    'raw_response'      => $request->all(),
                    'confirmed_at'      => now(),
                ]
            );

            if (! LoyaltyTransaction::where('order_id', $order->id)->where('type', 'earned')->exists()) {
                $loyalty->earnPoints($order);
            }
        });

        // Send notification outside transaction
        if ($order->user) {
            $order->user->notify(new OrderStatusNotification($order, 'order_paid'));
        }

        Log::info('PayTR ödeme başarılı', ['order_id' => $order->id]);
    }

    private function handleFailure(Order $order, Request $request): void
    {
        $order->update(['status' => 'pending']);

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'payment_method' => $order->payment_method ?: 'credit_card',
                'amount'         => $order->total,
                'status'         => 'failed',
                'error_message'  => $request->input('failed_reason_msg'),
                'raw_response'   => $request->all(),
            ]
        );

        Log::warning('PayTR ödeme başarısız', [
            'order_id' => $order->id,
            'reason'   => $request->input('failed_reason_msg'),
        ]);
    }
}
