<?php


namespace Lmh\ESign\Support;


use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Class Log
 * @package Lmh\ESign\Support
 * User: lmh <lmh@weiyian.com>
 * Date: 2022/6/14
 * @method debug($message, array $context = array())
 * @method info($message, array $context = array())
 */
class Log
{
    protected static $logger;

    /**
     * Tests if logger exists.
     *
     * @return bool
     */
    public static function hasLogger()
    {
        return self::$logger ? true : false;
    }

    /**
     * Forward call.
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return forward_static_call_array([self::getLogger(), $method], $args);
    }

    public static function getLogger()
    {
        return self::$logger ?: self::$logger = self::createDefaultLogger();
    }

    /**
     * Set logger.
     *
     * @param LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    /**
     * Make a default log instance.
     *
     * @return Logger
     */
    private static function createDefaultLogger()
    {
        $log = new Logger('ESign');

        if (defined('PHPUNIT_RUNNING') || 'cli' === php_sapi_name()) {
            $log->pushHandler(new NullHandler());
        } else {
            $log->pushHandler(new ErrorLogHandler());
        }

        return $log;
    }

    /**
     * Forward call.
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([self::getLogger(), $method], $args);
    }
}