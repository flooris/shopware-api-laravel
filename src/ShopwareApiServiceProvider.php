<?php

namespace Flooris\ShopwareApi;

use Illuminate\Support\ServiceProvider;

class ShopwareApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
    }

    public function register(): void
    {
        $this->app->singleton(ShopwareApi::class, fn($app) => new ShopwareApi());
    }

    public function provides(): array
    {
        return [
            ShopwareApi::class,
        ];
    }
}
