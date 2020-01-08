<?php

namespace Revolution\Freee\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Freee\Accounting\Configuration;
use Freee\Accounting\HeaderSelector;

trait Config
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var ClientInterface
     */
    protected $http;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @var int Host index
     */
    protected $hostIndex = 0;

    /**
     * @param  Configuration  $config
     *
     * @return $this
     */
    public function config(Configuration $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return ClientInterface
     */
    public function httpClient(): ClientInterface
    {
        if (is_null($this->http)) {
            $this->http = new Client();
        }

        return $this->http;
    }

    /**
     * @param  ClientInterface  $http
     *
     * @return $this
     */
    public function setHttpClient(ClientInterface $http)
    {
        $this->http = $http;

        return $this;
    }

    /**
     * @param  HeaderSelector  $headerSelector
     *
     * @return $this
     */
    public function setHeaderSelector(?HeaderSelector $headerSelector)
    {
        $this->headerSelector = $headerSelector;

        return $this;
    }

    /**
     * @param  int  $hostIndex
     *
     * @return $this
     */
    public function setHostIndex(int $hostIndex = 0)
    {
        $this->hostIndex = $hostIndex;

        return $this;
    }
}
