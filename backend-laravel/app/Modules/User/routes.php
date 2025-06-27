<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UserController;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\RateLimitMiddleware;

/*
|--------------------------------------------------------------------------
| User Module API Routes
|--------------------------------------------------------------------------
|
| 此檔案定義了使用者模組的 API 路由。
|
*/

Route::prefix('users')
    ->middleware(['auth:sanctum', RateLimitMiddleware::class . ':100,1']) # Changed to use middleware class name directly
    ->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware(CheckPermission::class . ':view-users');
        Route::get('/{id}', [UserController::class, 'show'])->middleware(CheckPermission::class . ':view-user-detail');
        Route::post('/', [UserController::class, 'store'])->middleware(CheckPermission::class . ':create-user');
        Route::put('/{id}', [UserController::class, 'update'])->middleware(CheckPermission::class . ':update-user');
        Route::delete('/{id}', [UserController::class, 'destroy'])->middleware(CheckPermission::class . ':delete-user');
});
