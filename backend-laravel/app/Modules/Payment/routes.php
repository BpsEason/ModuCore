<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Payment\Controllers\PaymentController;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\RateLimitMiddleware;

/*
|--------------------------------------------------------------------------
| Payment Module API Routes
|--------------------------------------------------------------------------
|
| 此檔案定義了金流模組的 API 路由。
|
*/

Route::prefix('payments')->group(function () {
    Route::post('/create', [PaymentController::class, 'createPayment'])
        ->middleware(['auth:sanctum', CheckPermission::class . ':initiate-payment', RateLimitMiddleware::class . ':60,1']);

    // 金流平台回呼通知 (此路由通常無需認證，由金流平台直接呼叫)
    Route::post('/callback', [PaymentController::class, 'handleCallback']);

    Route::get('/{orderId}/status', [PaymentController::class, 'queryPaymentStatus'])
        ->middleware(['auth:sanctum', CheckPermission::class . ':query-payment-status', RateLimitMiddleware::class . ':120,1']);

    Route::post('/{orderId}/refund', [PaymentController::class, 'refundPayment'])
        ->middleware(['auth:sanctum', CheckPermission::class . ':refund-payment', RateLimitMiddleware::class . ':30,1']);
});
