<?php

namespace Revolution\Freee\Drivers;

use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Revolution\Freee\Contracts\Accounting;
use Revolution\Freee\Concerns\Config;
use Revolution\Freee\Concerns\RefreshToken;

class AccountingClient implements Accounting
{
    use Macroable {
        __call as macroCall;
    }

    use Config;
    use RefreshToken;

    /**
     * @var string
     */
    protected $client_id;

    /**
     * @var string
     */
    protected $client_secret;

    /**
     * @param  array  $client_config
     */
    public function __construct(array $client_config)
    {
        $this->client_id = $client_config['client_id'] ?? '';
        $this->client_secret = $client_config['client_secret'] ?? '';
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string  $method
     * @param  array  $parameters
     *
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        $api = 'Freee\\Accounting\\Api\\'.Str::studly($method).'Api';

        if (class_exists($api)) {
            return new $api($this->http, $this->config, $this->headerSelector, $this->hostIndex);
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }
}
