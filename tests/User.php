<?php

namespace Tests;

use Revolution\Freee\Traits\FreeeSDK;

class User
{
    use FreeeSDK;

    /**
     * @param  string  $driver
     *
     * @return string
     */
    protected function tokenForFreee(string $driver): string
    {
        return $this->token ?? 'test';
    }
}
