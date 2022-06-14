<?php

namespace Lmh\ESign\Foundation\Account;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['account'] = function ($pimple) {
            return new Client($pimple['access_token']);
        };
    }
}