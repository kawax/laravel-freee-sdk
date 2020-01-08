<?php

namespace Revolution\Freee\Facades;

use Illuminate\Support\Facades\Facade;
use Revolution\Freee\Contracts\Factory;
use Revolution\Freee\Contracts\Driver;

/**
 * @method static Driver config(\Freee\Accounting\Configuration $config)
 * @method static Driver driver(string $driver = null)
 * @method static array refreshToken(string $token)
 *
 * @see \Revolution\Freee\FreeeManager
 * @see \Revolution\Freee\Drivers\AccountingClient
 */
class Freee extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
