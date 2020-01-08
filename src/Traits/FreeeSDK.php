<?php

namespace Revolution\Freee\Traits;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Freee\Accounting\Configuration;
use Revolution\Freee\Contracts\Factory;
use Revolution\Freee\Contracts\Driver;

trait FreeeSDK
{
    /**
     * @param  string  $driver
     *
     * @return Driver
     * @throws BindingResolutionException
     */
    public function freee(string $driver = 'accounting')
    {
        $token = $this->tokenForFreee($driver);

        switch ($driver) {
            case 'accounting':
            default:
                $config = Configuration::getDefaultConfiguration()->setAccessToken($token);
        }

        return Container::getInstance()
                        ->make(Factory::class)
                        ->driver($driver)
                        ->config($config);
    }

    /**
     * @param  string  $driver
     *
     * @return string
     */
    abstract protected function tokenForFreee(string $driver): string;
}
