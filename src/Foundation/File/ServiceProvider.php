<?php

namespace Lmh\ESign\Foundation\File;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['file'] = function ($pimple) {
            return new Client($pimple['access_token']);
        };
    }
}