<?php

use App\Http\Controllers\PaytrCallbackController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(function () {
    Route::post('/paytr/callback', [PaytrCallbackController::class, 'handle'])
        ->withoutMiddleware(['csrf', 'web'])
        ->name('paytr.callback');
});
