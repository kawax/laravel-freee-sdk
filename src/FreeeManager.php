<?php

namespace Revolution\Freee;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Manager;
use Revolution\Freee\Contracts\Accounting;
use Revolution\Freee\Contracts\Factory;

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
     * {@inheritdoc}
     */
    public function getDefaultDriver()
    {
        return 'accounting';
    }
}
