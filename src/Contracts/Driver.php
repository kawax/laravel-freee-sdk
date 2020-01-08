<?php

namespace Revolution\Freee\Contracts;

use Freee\Accounting\Configuration;

interface Driver
{
    /**
     * @param  Configuration  $config
     *
     * @return $this
     */
    public function config(Configuration $config);

    /**
     * @param  string  $refresh_token
     *
     * @return array
     */
    public function refreshToken(string $refresh_token);
}
