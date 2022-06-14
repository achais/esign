<?php

namespace Lmh\ESign\Foundation\Organization;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['organizations'] = function ($pimple) {
            return new Client($pimple['access_token']);
        };
    }
}