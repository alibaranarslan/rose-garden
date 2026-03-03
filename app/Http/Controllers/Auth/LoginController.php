<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('account.login')->with([
            'metaTitle' => 'Giris Yap',
            'metaDescription' => 'Rose Garden musteri giris sayfasi.',
        ]);
    }

    public function showRegister()
    {
        return view('account.register')->with([
            'metaTitle' => 'Kayit Ol',
            'metaDescription' => 'Rose Garden yeni musteri kayit sayfasi.',
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $guestSessionId = session('cart_session_id');

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email veya şifre hatalı.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        if ($guestSessionId) {
            $this->mergeGuestCart($guestSessionId, Auth::id());
        }

        return redirect()->intended(route('account.dashboard'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'kvkk_acknowledged' => ['accepted'],
            'marketing_consent' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'kvkk_accepted_at' => now(),
            'marketing_consent' => (bool) ($data['marketing_consent'] ?? false),
            'marketing_consent_at' => !empty($data['marketing_consent']) ? now() : null,
            'preferred_language' => app()->getLocale(),
            'is_active' => true,
        ]);

        $cartSessionId = session('cart_session_id');

        Auth::login($user);

        if ($cartSessionId) {
            $this->mergeGuestCart($cartSessionId, $user->id);
        }

        $request->session()->regenerate();

        return redirect()->route('account.dashboard');
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
                    if ($guestItem->card_message && !$existing->card_message) {
                        $existing->update(['card_message' => $guestItem->card_message]);
                    }
                    $guestItem->delete();
                } else {
                    $guestItem->update(['user_id' => $userId, 'session_id' => null]);
                }
            });
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
