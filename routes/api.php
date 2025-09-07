<?php

// routes/web.php 또는 routes/api.php에 추가

use App\Http\Controllers\PaypalWebhookController;
use App\Http\Controllers\Api\PaypalController;

// PayPal 웹훅 라우트 (CSRF 토큰 불필요)
Route::post('/webhooks/paypal', [PaypalWebhookController::class, 'handle'])
    ->name('paypal.webhook')
    ->withoutMiddleware(['web']); // 웹 미들웨어 제외 (CSRF 포함)

// PayPal 관련 라우트 (웹 미들웨어 사용)
Route::middleware(['auth'])->group(function () {
    Route::post('/api/paypal/get-plan-id', [PaypalController::class, 'getPlanId'])->name('paypal.get-plan-id');
});