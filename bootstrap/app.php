<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(
            at: env('TRUSTED_PROXIES', '*'),
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB
        );

        $middleware->append(\App\Http\Middleware\ForceHttps::class);

        $middleware->alias([
            'set.locale'       => \App\Http\Middleware\SetLocale::class,
            'seo.defaults'     => \App\Http\Middleware\SeoDefaults::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'cache.page'       => \App\Http\Middleware\CachePage::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SeoDefaults::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        $middleware->append(\App\Http\Middleware\TrackPageView::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })->create();
