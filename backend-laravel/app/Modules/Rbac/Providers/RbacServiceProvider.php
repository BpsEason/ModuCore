<?php
namespace App\Modules\Rbac\Providers;

use Illuminate\Support\ServiceProvider;

class RbacServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 模組特定的依賴注入
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../../../database/migrations'); # Load migrations from central migrations folder
    }
}
