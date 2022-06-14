<?php

namespace Lmh\ESign\Foundation\SignFlow;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['signflow'] = function ($pimple) {
            return new Client($pimple['access_token']);
        };
    }
}