<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\StorefrontLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return redirect()
                ->to(StorefrontLocale::route('login'))
                ->with('error', __('auth.google_disabled'));
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return redirect()
                ->to(StorefrontLocale::route('login'))
                ->with('error', __('auth.google_disabled'));
        }

        $guestSessionId = session('cart_session_id');

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::warning('Google OAuth callback failed', ['message' => $e->getMessage()]);

            return redirect()
                ->to(StorefrontLocale::route('login'))
                ->with('error', __('auth.google_failed'));
        }

        try {
            $user = User::where('google_id', $googleUser->getId())->first();

            if (! $user) {
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    $user->update(['google_id' => $googleUser->getId()]);
                } else {
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'email_verified_at' => now(),
                        'password' => null,
                    ]);
                }
            }

            Auth::login($user, remember: true);

            if ($guestSessionId) {
                $this->mergeGuestCart($guestSessionId, $user->id);
            }

            if (! $user->kvkk_accepted_at) {
                session(['pending_kvkk' => true]);

                return redirect()->to(StorefrontLocale::route('kvkk.consent'));
            }

            return redirect()->intended(StorefrontLocale::route('account.dashboard'));
        } catch (\Exception $e) {
            Log::error('Google OAuth user creation failed', [
                'email' => $googleUser->getEmail(),
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->to(StorefrontLocale::route('login'))
                ->with('error', __('auth.account_creation_failed'));
        }
    }

    private function mergeGuestCart(string $sessionId, int $userId): void
    {
        \App\Models\CartItem::where('session_id', $sessionId)
            ->each(function ($guestItem) use ($userId) {
                $existing = \App\Models\CartItem::where('user_id', $userId)
                    ->where('product_id', $guestItem->product_id)
                    ->where('variant_id', $guestItem->variant_id)
                    ->first();

                if ($existing) {
                    $existing->increment('quantity', $guestItem->quantity);

                    if ($guestItem->card_message && ! $existing->card_message) {
                        $existing->update(['card_message' => $guestItem->card_message]);
                    }

                    $guestItem->delete();

                    return;
                }

                $guestItem->update(['user_id' => $userId, 'session_id' => null]);
            });
    }

    private function isConfigured(): bool
    {
        return trim((string) config('services.google.client_id', '')) !== ''
            && trim((string) config('services.google.client_secret', '')) !== '';
    }
}
