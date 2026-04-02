<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KvkkConsentController extends Controller
{
    public function show(): View
    {
        return view('auth.kvkk-consent');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kvkk_accepted' => ['accepted'],
        ], [
            'kvkk_accepted.accepted' => 'KVKK aydınlatma metnini kabul etmeniz zorunludur.',
        ]);

        auth()->user()->update(['kvkk_accepted_at' => now()]);

        return redirect()->intended(route('home'));
    }

    public function reject(): RedirectResponse
    {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('error', 'KVKK onayı olmadan giriş yapamazsınız.');
    }
}
