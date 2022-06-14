<?php

namespace Lmh\ESign;

use Exception;
use Lmh\ESign\Core\AccessToken;
use Lmh\ESign\Core\BaseClient;
use Lmh\ESign\Core\Http;
use Lmh\ESign\Support\Log;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Redis;

/**
 * Class Application
 *
 * @property AccessToken $access_token
 * @property \Lmh\ESign\Foundation\Account\Client $account
 * @property \Lmh\ESign\Foundation\File\Client $file
 * @property \Lmh\ESign\Foundation\SignFlow\Client $signflow
 * @property \Lmh\ESign\Foundation\Organization\Client $organizations
 * @property \Lmh\ESign\Foundation\Template\Client $template
 *
 * @package Lmh\ESign
 */
class Application extends Container
{
    protected $providers = [
        Foundation\Account\ServiceProvider::class,
        Foundation\File\ServiceProvider::class,
        Foundation\SignFlow\ServiceProvider::class,
        Foundation\Organization\ServiceProvider::class,
        Foundation\Template\ServiceProvider::class,
    ];






    public function __construct(array $config = array())
    {
        parent::__construct($config);

        $this['config'] = function () use ($config) {
            return new Foundation\Config($config);
        };
        $this->registerBase();
        $this->registerProviders();
        $this->initializeLogger();

        $production = $this['config']->get('production', true);
        if ($production) {
            $baseUri = 'https://openapi.esign.cn';
        } else {
            $baseUri = 'https://smlopenapi.esign.cn';
        }
        Http::setDefaultOptions($this['config']->get('guzzle', ['timeout' => 5.0, 'base_uri' => $baseUri]));
        BaseClient::maxRetries($this['config']->get('max_retries', 2));
        $this->logConfiguration($config);
    }

    private function registerBase()
    {
        /**
         * @var Redis $client
         */
        $cache = $this['config']->get('cache', true);
        $this['access_token'] = function () use ($cache) {
            return new AccessToken(
                $this['config']['app_id'],
                $this['config']['secret'],
                $cache
            );
        };
    }

    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    private function initializeLogger()
    {
        if (Log::hasLogger()) {
            return;
        }

        $logger = new Logger('esign');

        if (!$this['config']['debug'] || defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } elseif ($this['config']['log.handler'] instanceof HandlerInterface) {
            $logger->pushHandler($this['config']['log.handler']);
        } elseif ($logFile = $this['config']['log.file']) {
            try {
                $logger->pushHandler(new StreamHandler(
                        $logFile,
                        $this['config']->get('log.level', Logger::WARNING),
                        true,
                        $this['config']->get('log.permission', null))
                );
            } catch (Exception $e) {
            }
        }

        Log::setLogger($logger);
    }

    public function logConfiguration($config)
    {
        $config = new Foundation\Config($config);

        $keys = ['app_id', 'secret', 'open_platform.app_id', 'open_platform.secret', 'mini_program.app_id', 'mini_program.secret'];
        foreach ($keys as $key) {
            !$config->has($key) || $config[$key] = '***' . substr($config[$key], -5);
        }

    }

    public function getProviders()
    {
        return $this->providers;
    }

    public function setProviders(array $providers)
    {
        $this->providers = [];

        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    public function addProvider($provider)
    {
        array_push($this->providers, $provider);
        return $this;
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $args)
    {
        if (is_callable([$this['fundamental.api'], $method])) {
            return call_user_func_array([$this['fundamental.api'], $method], $args);
        }

        throw new Exception("Call to undefined method {$method}()");
    }
}