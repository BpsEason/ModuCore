<?php
namespace App\Modules\Sms\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Sms\Contracts\SmsService;
use App\Modules\Sms\Services\TwilioSmsService;

class SmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SmsService::class, function ($app) {
            return new TwilioSmsService(
                config('twilio.account_sid'), # Assuming twilio config is loaded
                config('twilio.auth_token'),
                config('twilio.from_phone_number')
            );
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../../../database/migrations'); # Load migrations from central migrations folder
    }
}
