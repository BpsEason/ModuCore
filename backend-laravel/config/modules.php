<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ModuCore 模組配置
    |--------------------------------------------------------------------------
    |
    | 此檔案用於註冊和管理 ModuCore 應用程式中的所有模組。
    |
    */

    'enabled_modules' => [
        'User',    // 啟用 User 模組
        'Payment', // 啟用 Payment 模組
        'Sms',     // 啟用 Sms 模組
        'Rbac',    // 啟用 Rbac 模組
        // 在此處添加您自定義的模組名稱
    ],

    /*
    |--------------------------------------------------------------------------
    | 模組服務提供者自動載入
    |--------------------------------------------------------------------------
    |
    | 如果您希望模組能夠自動載入它們的服務提供者，可以在此處定義。
    | 通常，您會將它們列在 AppServiceProvider 或此處。
    |
    */
    'module_service_providers' => [
        \App\Modules\User\Providers\UserServiceProvider::class,
        \App\Modules\Payment\Providers\PaymentServiceProvider::class,
        \App\Modules\Sms\Providers\SmsServiceProvider::class,
        \App\Modules\Rbac\Providers\RbacServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | 模組路徑
    |--------------------------------------------------------------------------
    |
    | 定義您的模組存儲的基本路徑。
    |
    */
    'path' => app_path('Modules'),
];
