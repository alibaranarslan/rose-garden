<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\StorefrontLocale;
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
            'kvkk_accepted.accepted' => __('auth.kvkk_required'),
        ]);

        auth()->user()->update(['kvkk_accepted_at' => now()]);

        return redirect()->intended(StorefrontLocale::route('home'));
    }

    public function reject(): RedirectResponse
    {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()
            ->to(StorefrontLocale::route('login'))
            ->with('error', __('auth.kvkk_reject_denied'));
    }
}
