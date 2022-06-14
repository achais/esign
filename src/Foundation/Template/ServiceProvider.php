<?php

namespace Lmh\ESign\Foundation\Template;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['template'] = function ($pimple) {
            return new Client($pimple['access_token']);
        };
    }
}