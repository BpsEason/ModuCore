<?php
namespace App\Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\User\Contracts\UserInterface;
use App\Modules\User\Services\UserService;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserInterface::class, UserService::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
        // $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations'); // If User module had its own migrations
    }
}
