<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Sms\Controllers\SmsController;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\RateLimitMiddleware;

/*
|--------------------------------------------------------------------------
| SMS Module API Routes
|--------------------------------------------------------------------------
|
| 此檔案定義了簡訊模組的 API 路由。
|
*/

Route::prefix('sms')->group(function () {
    Route::post('/send', [SmsController::class, 'send'])
        ->middleware(['auth:sanctum', CheckPermission::class . ':send-sms', RateLimitMiddleware::class . ':60,1']);

    Route::get('/{externalId}/status', [SmsController::class, 'status'])
        ->middleware(['auth:sanctum', CheckPermission::class . ':query-sms-status', RateLimitMiddleware::class . ':120,1']);

    Route::post('/send-verification', [SmsController::class, 'sendVerification'])
        ->middleware(RateLimitMiddleware::class . ':5,1'); // 驗證碼發送通常有更嚴格的頻率限制
});
