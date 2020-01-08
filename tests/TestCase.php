<?php

namespace Tests;

use Revolution\Freee\Providers\FreeeServiceProvider;
use Revolution\Freee\Facades\Freee;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            FreeeServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Freee' => Freee::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('services.freee-accounting', [
            'client_id'          => 'test',
            'client_secret'      => 'test',
            'redirect'           => '/redirect',
        ]);
    }
}
