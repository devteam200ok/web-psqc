<?php

// routes/web.php 또는 routes/api.php에 추가

use App\Http\Controllers\PaypalWebhookController;

// PayPal 웹훅 라우트 (CSRF 토큰 불필요)
Route::post('/webhooks/paypal', [PaypalWebhookController::class, 'handle'])
    ->name('paypal.webhook')
    ->withoutMiddleware(['web']); // 웹 미들웨어 제외 (CSRF 포함)

// 또는 API 라우트에 추가하는 경우
// Route::post('/api/webhooks/paypal', [PaypalWebhookController::class, 'handle'])
//     ->name('api.paypal.webhook');