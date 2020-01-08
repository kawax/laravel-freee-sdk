<?php

namespace Revolution\Freee\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Revolution\Freee\Contracts\Factory;
use Revolution\Freee\FreeeManager;
use Revolution\Freee\Drivers\AccountingClient;
use Revolution\Freee\Contracts\Accounting;

class FreeeServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Factory::class, function ($app) {
            return new FreeeManager($app);
        });

        $this->app->singleton(Accounting::class, function ($app) {
            return new AccountingClient($app['config']['services.freee-accounting']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     * @codeCoverageIgnore
     */
    public function provides()
    {
        return [
            Factory::class,
            Accounting::class,
        ];
    }
}
