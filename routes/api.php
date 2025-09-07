<?php

// routes/api.php

use App\Http\Controllers\PaypalWebhookController;

// PayPal 웹훅 라우트
Route::post('/webhooks/paypal', [PaypalWebhookController::class, 'handle'])
    ->name('paypal.webhook');