<?php

namespace Revolution\Freee;

use Illuminate\Support\Manager;
use Revolution\Freee\Contracts\Factory;
use Revolution\Freee\Contracts\Accounting;
use Illuminate\Contracts\Container\BindingResolutionException;

class FreeeManager extends Manager implements Factory
{
    /**
     * @return Accounting
     * @throws BindingResolutionException
     */
    public function createAccountingDriver()
    {
        return $this->container->make(Accounting::class);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultDriver()
    {
        return 'accounting';
    }
}
