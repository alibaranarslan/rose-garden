<?php

namespace App\Http\Middleware;

use App\Support\StorefrontLocale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = StorefrontLocale::resolveRequestLocale($request);

        app()->setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }
}
