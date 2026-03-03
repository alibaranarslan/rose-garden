<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SeoDefaults
{
    public function handle(Request $request, Closure $next): Response
    {
        $noindex = $request->filled('page') || $request->filled('q');

        View::share('canonical', $request->url());
        View::share('noindex', $noindex);

        return $next($request);
    }
}
