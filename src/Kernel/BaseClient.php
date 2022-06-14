<?php

namespace Lmh\ESign\Core;

use Closure;
use GuzzleHttp\Middleware;
use Lmh\ESign\Exceptions\HttpException;
use Lmh\ESign\Support\Collection;
use Lmh\ESign\Support\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class BaseClient
{
    const GET = 'get';
    const POST = 'post';
    const JSON = 'json';
    const PUT = 'put';
    const DELETE = 'delete';
    /**
     * @var int
     */
    protected static $maxRetries = 2;
    /**
     * Http instance.
     *
     * @var Http
     */
    protected $http;
    /**
     * The request token.
     *
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * Constructor.
     *
     * @param AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->setAccessToken($accessToken);
    }

    /**
     * @param int $retries
     */
    public static function maxRetries($retries)
    {
        self::$maxRetries = abs($retries);
    }

    /**
     * Return the current accessToken.
     *
     * @return AccessToken
     */
    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }

    /**
     * Set the request token.
     *
     * @param AccessToken $accessToken
     *
     * @return $this
     */
    public function setAccessToken(AccessToken $accessToken): BaseClient
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Parse JSON from response and check error.
     *
     * @param $method
     * @param array $args
     * @return Collection|null
     * @throws HttpException
     */
    public function request($method, array $args): ?Collection
    {
        $http = $this->getHttp();
        $response = call_user_func_array([$http, $method], $args);

        $contents = $http->parseJSON($response);
        if (empty($contents)) {
            return null;
        }
        $this->checkAndThrow($contents);
        return (new Collection($contents))->get('data');
    }

    /**
     * Return the http instance.
     *
     * @return Http
     */
    public function getHttp()
    {
        if (is_null($this->http)) {
            $this->http = new Http();
        }

        if (0 === count($this->http->getMiddlewares())) {
            $this->registerHttpMiddlewares();
        }

        return $this->http;
    }

    /**
     * Set the http instance.
     *
     * @param Http $http
     *
     * @return $this
     */
    public function setHttp(Http $http)
    {
        $this->http = $http;

        return $this;
    }

    /**
     * Register Guzzle middlewares.
     */
    protected function registerHttpMiddlewares()
    {
        // log
        $this->http->addMiddleware($this->logMiddleware());
        // retry
        $this->http->addMiddleware($this->retryMiddleware());
        // access token
        $this->http->addMiddleware($this->accessTokenMiddleware());
    }

    /**
     * Log the request.
     *
     * @return Closure
     */
    protected function logMiddleware()
    {
        return Middleware::tap(function (RequestInterface $request, $options) {
            Log::debug("Request: {$request->getMethod()} {$request->getUri()} " . json_encode($options));
            Log::debug('Request headers:' . json_encode($request->getHeaders()));
        });
    }

    /**
     * Return retry middleware.
     *
     * @return Closure
     */
    protected function retryMiddleware()
    {
        return Middleware::retry(function (
            $retries,
            RequestInterface $request,
            ResponseInterface $response = null
        ) {
            // Limit the number of retries to 2
            if ($retries <= self::$maxRetries && $response && $body = $response->getBody()) {
                // Retry on server errors
                if (false !== stripos($body, 'code') && (false !== stripos($body, '40001') || false !== stripos($body, '42001'))) {
                    $token = $this->accessToken->getToken(true);

                    $request = $request->withHeader('X-Tsign-Open-App-Id', $this->accessToken->getAppId());
                    $request = $request->withHeader('X-Tsign-Open-Token', $token);
                    $request = $request->withHeader('Content-Type', 'application/json');

                    Log::debug("Retry with Request Token: {$token}");

                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Attache access token to request query.
     *
     * @return Closure
     */
    protected function accessTokenMiddleware(): Closure
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if (!$this->accessToken) {
                    return $handler($request, $options);
                }

                $request = $request->withHeader('X-Tsign-Open-App-Id', $this->accessToken->getAppId());
                $request = $request->withHeader('X-Tsign-Open-Token', $this->accessToken->getToken());
                $request = $request->withHeader('Content-Type', 'application/json');

                return $handler($request, $options);
            };
        };
    }

    /**
     * Check the array data errors, and Throw exception when the contents contains error.
     *
     * @param array $contents
     * @throws HttpException
     */
    protected function checkAndThrow(array $contents)
    {
        if (isset($contents['code']) && 0 !== $contents['code']) {
            if (empty($contents['message'])) {
                $contents['message'] = 'Unknown';
            }

            throw new HttpException($contents['message'], $contents['code']);
        }
    }
}