<?php

namespace App\Http\Controllers;

use App\Models\CookieConsent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CookieConsentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'categories' => ['required', 'array'],
            'categories.*' => ['string', 'in:necessary,analytics,marketing'],
        ]);

        CookieConsent::create([
            'session_id' => $request->session()->getId(),
            'user_id' => auth()->id(),
            'ip_address' => (string) $request->ip(),
            'consent_categories' => $validated['categories'],
            'consented_at' => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }
}
