<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Rbac\Controllers\RoleController;
use App\Modules\Rbac\Controllers\PermissionController; # Added PermissionController
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\RateLimitMiddleware;

/*
|--------------------------------------------------------------------------
| RBAC Module API Routes
|--------------------------------------------------------------------------
|
| 此檔案定義了 RBAC 模組的 API 路由。
|
*/

Route::prefix('rbac')->middleware(['auth:sanctum', RateLimitMiddleware::class . ':60,1'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->middleware(CheckPermission::class . ':view-roles');
    Route->post('/roles', [RoleController::class, 'store'])->middleware(CheckPermission::class . ':create-role');
    Route->get('/roles/{id}', [RoleController::class, 'show'])->middleware(CheckPermission::class . ':view-role-detail');
    Route->put('/roles/{id}', [RoleController::class, 'update'])->middleware(CheckPermission::class . ':update-role');
    Route->delete('/roles/{id}', [RoleController::class, 'destroy'])->middleware(CheckPermission::class . ':delete-role');

    # Routes for Permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware(CheckPermission::class . ':view-permissions');
    Route->post('/permissions', [PermissionController::class, 'store'])->middleware(CheckPermission::class . ':create-permission');
    Route.get('/permissions/{id}', [PermissionController::class, 'show'])->middleware(CheckPermission::class . ':view-permission-detail');
    Route.put('/permissions/{id}', [PermissionController::class, 'update'])->middleware(CheckPermission::class . ':update-permission');
    Route.delete('/permissions/{id}', [PermissionController::class, 'destroy'])->middleware(CheckPermission::class . ':delete-permission');
});
