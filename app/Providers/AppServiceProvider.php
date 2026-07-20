<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SuperFaktura\ApiClient\ApiClient;
use SuperFaktura\ApiClient\Authorization\SimpleProvider;
use SuperFaktura\ApiClient\MarketUri;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ApiClient::class, function () {
            $cfg = config('services.superfaktura');
            return new ApiClient(
                new SimpleProvider(
                    (string) ($cfg['email'] ?? ''),
                    (string) ($cfg['api_key'] ?? ''),
                    (string) ($cfg['company_name'] ?? ''),
                    (int) ($cfg['company_id'] ?? 0),
                ),
                ($cfg['sandbox'] ?? true) ? MarketUri::SLOVAK_SANDBOX : MarketUri::SLOVAK,
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
