<?php

namespace Lmh\ESign\Core;

use Lmh\ESign\Exceptions\HttpException;
use Redis;

class AccessToken
{
    const API_TOKEN_GET = '/v1/oauth2/access_token';
    protected $appId;
    protected $secret;
    protected $cache;
    protected $cacheKey;
    protected $http;
    protected $tokenJsonKey = 'token';
    protected $prefix = 'esign.common.access_token.';

    public function __construct($appId, $secret, Redis $cache = null)
    {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->cache = $cache;
    }

    /**
     * @param bool $forceRefresh
     * @return bool|mixed
     * @throws HttpException
     */
    public function getToken($forceRefresh = false)
    {
        $cacheKey = $this->getCacheKey();
        $cached = $this->getCache()->get($cacheKey);

        if ($forceRefresh || empty($cached)) {
            $token = $this->getTokenFromServer();
            $this->getCache()->set($cacheKey, $token['data'][$this->tokenJsonKey], 60 * 100);
            return $token['data'][$this->tokenJsonKey];
        }

        return $cached;
    }

    protected function getCacheKey()
    {
        if (is_null($this->cacheKey)) {
            return $this->prefix . $this->appId;
        }

        return $this->cacheKey;
    }

    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;

        return $this;
    }

    protected function getCache()
    {
        return $this->cache;
    }

    /**
     * @return mixed
     * @throws HttpException
     */
    public function getTokenFromServer()
    {
        $params = [
            'appId' => $this->appId,
            'secret' => $this->secret,
            'grantType' => 'client_credentials',
        ];

        $http = $this->getHttp();

        $token = $http->request($http->get(self::API_TOKEN_GET, $params));

        if (empty($token['data'][$this->tokenJsonKey])) {
            throw new HttpException('Request AccessToken fail. response: ' . json_encode($token, JSON_UNESCAPED_UNICODE));
        }

        return $token;
    }

    public function getHttp()
    {
        return $this->http ?: $this->http = new Http();
    }

    public function setHttp($http)
    {
        $this->http = $http;
        return $this;
    }

    public function getAppId()
    {
        return $this->appId;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }
}