<?php

namespace Revolution\Freee\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Revolution\Freee\Contracts\Accounting;
use Revolution\Freee\Contracts\Factory;
use Revolution\Freee\Drivers\AccountingClient;
use Revolution\Freee\FreeeManager;

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
