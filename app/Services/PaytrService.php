<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaytrService
{
    private string $merchantId;
    private string $merchantKey;
    private string $merchantSalt;
    private bool $testMode;
    private bool $debug;
    private int $timeout;
    private string $apiUrl;
    private string $iframeUrl;

    public function __construct()
    {
        $this->merchantId   = $this->getMerchantId();
        $this->merchantKey  = $this->getMerchantKey();
        $this->merchantSalt = $this->getMerchantSalt();
        $this->testMode     = config('services.paytr.test_mode', true);
        $this->debug        = config('services.paytr.debug', false);
        $this->timeout      = config('services.paytr.timeout', 30);
        $this->apiUrl       = config('services.paytr.api_url', 'https://www.paytr.com/odeme/api/get-token');
        $this->iframeUrl    = config('services.paytr.iframe_url', 'https://www.paytr.com/odeme/guvenli/');
    }

    // Admin panel overrides .env — Setting takes priority over config
    protected function getMerchantId(): string
    {
        return Setting::get('payment', 'paytr_merchant_id')
            ?: config('services.paytr.merchant_id')
            ?: throw new \RuntimeException('PayTR merchant_id yapılandırılmamış');
    }

    protected function getMerchantKey(): string
    {
        return Setting::get('payment', 'paytr_merchant_key')
            ?: config('services.paytr.merchant_key')
            ?: throw new \RuntimeException('PayTR merchant_key yapılandırılmamış');
    }

    protected function getMerchantSalt(): string
    {
        return Setting::get('payment', 'paytr_merchant_salt')
            ?: config('services.paytr.merchant_salt')
            ?: throw new \RuntimeException('PayTR merchant_salt yapılandırılmamış');
    }

    public function createToken(Order $order): string
    {
        $userBasket = $this->buildUserBasket($order);
        $userBasketEncoded = base64_encode(json_encode($userBasket));

        $paymentAmount = (int) round($order->total * 100); // Convert to kuruş
        $merchantOid   = $order->order_number;
        $email         = $order->sender_email ?? $order->user?->email ?? 'musteri@example.com';
        $userIp        = $order->ip_address ?? request()->ip();
        $noInstallment = 0;
        $maxInstallment = 0;
        $currency      = 'TL';
        $testMode      = $this->testMode ? '1' : '0';
        $debugOn       = $this->debug ? '1' : '0';
        $timeoutLimit  = '30';
        $okUrl         = url(config('services.paytr.success_url'));
        $failUrl       = url(config('services.paytr.fail_url'));
        $callbackUrl   = url(config('services.paytr.callback_url'));

        // HMAC-SHA256 hash
        $hashStr = $this->merchantId . $userIp . $merchantOid . $email . $paymentAmount
            . $userBasketEncoded . $noInstallment . $maxInstallment . $currency . $testMode;
        $paytrToken = base64_encode(hash_hmac('sha256', $hashStr . $this->merchantSalt, $this->merchantKey, true));

        $payload = [
            'merchant_id'      => $this->merchantId,
            'user_ip'          => $userIp,
            'merchant_oid'     => $merchantOid,
            'email'            => $email,
            'payment_amount'   => $paymentAmount,
            'paytr_token'      => $paytrToken,
            'user_basket'      => $userBasketEncoded,
            'debug_on'         => $debugOn,
            'no_installment'   => $noInstallment,
            'max_installment'  => $maxInstallment,
            'user_name'        => $order->sender_name,
            'user_address'     => $order->recipient_address ?? '',
            'user_phone'       => $order->sender_phone ?? '',
            'merchant_ok_url'  => $okUrl,
            'merchant_fail_url' => $failUrl,
            'merchant_notification_url' => $callbackUrl,
            'timeout_limit'    => $timeoutLimit,
            'currency'         => $currency,
            'test_mode'        => $testMode,
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->asForm()
                ->post($this->apiUrl, $payload);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? '') === 'success') {
                return $result['token'];
            }

            Log::error('PayTR token alınamadı', [
                'order'    => $merchantOid,
                'response' => $result,
            ]);

            throw new \RuntimeException('PayTR token alınamadı: ' . ($result['reason'] ?? 'Bilinmeyen hata'));
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('PayTR API bağlantı hatası', ['message' => $e->getMessage()]);
            throw new \RuntimeException('Ödeme servisi bağlantı hatası: ' . $e->getMessage());
        }
    }

    public function verifyCallback(Request $request): bool
    {
        // IP whitelist check
        if (!$this->isAllowedIp($request->ip())) {
            Log::warning('PayTR callback: izin verilmeyen IP', ['ip' => $request->ip()]);
            return false;
        }

        $merchantOid   = $request->input('merchant_oid');
        $status        = $request->input('status');
        $totalAmount   = $request->input('total_amount');
        $hash          = $request->input('hash');

        // Hash doğrulama
        $hashStr     = $merchantOid . $this->merchantSalt . $status . $totalAmount;
        $expectedHash = base64_encode(hash_hmac('sha256', $hashStr, $this->merchantKey, true));

        if (!hash_equals($expectedHash, $hash)) {
            Log::warning('PayTR callback: hash doğrulama başarısız', [
                'merchant_oid' => $merchantOid,
            ]);
            return false;
        }

        return true;
    }

    public function getIframeUrl(string $token): string
    {
        return $this->iframeUrl . $token;
    }

    private function buildUserBasket(Order $order): array
    {
        $basket = [];
        foreach ($order->items as $item) {
            $productName = $item->product?->getTranslation('name', 'tr') ?? $item->product_name ?? 'Ürün';
            $basket[] = [
                $productName,
                number_format($item->unit_price, 2, '.', ''),
                $item->quantity,
            ];
        }
        return $basket;
    }

    private function isAllowedIp(string $ip): bool
    {
        $allowedCidrs = config('services.paytr.allowed_ips', ['193.140.143.0/24']);

        foreach ($allowedCidrs as $cidr) {
            if ($this->ipInCidr($ip, $cidr)) {
                return true;
            }
        }

        // In test mode, allow any IP
        return $this->testMode;
    }

    private function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr);
        return (ip2long($ip) & ~((1 << (32 - (int)$mask)) - 1)) === ip2long($subnet);
    }
}
