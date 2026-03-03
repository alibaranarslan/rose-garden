<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::warning('Google OAuth callback hatası', ['message' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Google ile giriş başarısız. Lütfen tekrar deneyin.');
        }

        try {
            // Check existing user by google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                // Check by email
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    // Link existing account to Google
                    $user->update(['google_id' => $googleUser->getId()]);
                } else {
                    // Create new user
                    $user = User::create([
                        'name'              => $googleUser->getName(),
                        'email'             => $googleUser->getEmail(),
                        'google_id'         => $googleUser->getId(),
                        'email_verified_at' => now(),
                        'password'          => null,
                    ]);
                }
            }

            Auth::login($user, remember: true);

            // Show KVKK consent if first login
            if (!$user->kvkk_accepted_at) {
                session(['pending_kvkk' => true]);
                return redirect()->route('kvkk.consent');
            }

            return redirect()->intended(route('account.dashboard'));
        } catch (\Exception $e) {
            Log::error('Google OAuth kullanıcı oluşturma hatası', [
                'email'   => $googleUser->getEmail(),
                'message' => $e->getMessage(),
            ]);
            return redirect()->route('login')->with('error', 'Hesap oluşturulurken bir hata oluştu.');
        }
    }
}
