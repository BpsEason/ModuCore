<?php
namespace App\Modules\Payment\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Payment\Contracts\PaymentGateway;
use App\Modules\Payment\Services\EcpayPaymentGateway;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, function ($app) {
            return new EcpayPaymentGateway(
                config('ecpay.merchant_id'), # Assuming ecpay config is loaded
                config('ecpay.hash_key'),
                config('ecpay.hash_iv')
            );
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../../../database/migrations'); # Load migrations from central migrations folder
    }
}
