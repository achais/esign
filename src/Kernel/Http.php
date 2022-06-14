<?php


namespace Lmh\ESign\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use Lmh\ESign\Exceptions\HttpException;
use Lmh\ESign\Support\Log;
use Psr\Http\Message\ResponseInterface;

class Http
{
    /**
     * Used to identify handler defined by client code
     * Maybe useful in the future.
     */
    const USER_DEFINED_HANDLER = 'userDefined';
    /**
     * @var array
     */
    protected static $globals = [
        'curl' => [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ],
    ];
    /**
     * Guzzle client default settings.
     *
     * @var array
     */
    protected static $defaults = [];
    /**
     * Http client.
     *
     * @var Client
     */
    protected $client;
    /**
     * The middlewares.
     *
     * @var array
     */
    protected $middlewares = [];

    /**
     * Set guzzle default settings.
     *
     * @param array $defaults
     */
    public static function setDefaultOptions($defaults = [])
    {
        self::$defaults = array_merge(self::$globals, $defaults);
    }

    /**
     * Return current guzzle default settings.
     *
     * @return array
     */
    public static function getDefaultOptions()
    {
        return self::$defaults;
    }

    /**
     * GET request.
     *
     * @param string $url
     * @param array $options
     *
     * @return ResponseInterface
     *
     */
    public function get($url, array $options = []): ResponseInterface
    {
        return $this->request($url, 'GET', ['query' => $options]);
    }

    /**
     * Make a request.
     *
     * @param string $url
     * @param string $method
     * @param array $options
     *
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public function request($url, $method = 'GET', $options = [])
    {
        $method = strtoupper($method);

        $options = array_merge(self::$defaults, $options);

        Log::debug('Client Request:', compact('url', 'method', 'options'));

        $options['handler'] = $this->getHandler();

        $response = $this->getClient()->request($method, $url, $options);

        Log::debug('API response:', [
            'Status' => $response->getStatusCode(),
            'Reason' => $response->getReasonPhrase(),
            'Headers' => $response->getHeaders(),
            'Body' => strval($response->getBody()),
        ]);

        return $response;
    }

    /**
     * Build a handler.
     *
     * @return HandlerStack
     */
    protected function getHandler()
    {
        $stack = HandlerStack::create();

        foreach ($this->middlewares as $middleware) {
            $stack->push($middleware);
        }

        if (isset(static::$defaults['handler']) && is_callable(static::$defaults['handler'])) {
            $stack->push(static::$defaults['handler'], self::USER_DEFINED_HANDLER);
        }

        return $stack;
    }

    /**
     * Return GuzzleHttp\Client instance.
     *
     * @return Client
     */
    public function getClient()
    {
        if (!($this->client instanceof Client)) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * Set GuzzleHttp\Client.
     *
     * @param Client $client
     *
     * @return Http
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * POST request.
     *
     * @param string $url
     * @param array|string $options
     *
     * @return ResponseInterface
     *
     * @throws HttpException
     */
    public function post($url, $options = [])
    {
        $key = is_array($options) ? 'form_params' : 'body';

        return $this->request($url, 'POST', [$key => $options]);
    }

    public function put($url, $options = [])
    {
        $key = is_array($options) ? 'form_params' : 'body';

        return $this->request($url, 'PUT', [$key => $options]);
    }

    public function delete($url, $options = []): ResponseInterface
    {
        $key = is_array($options) ? 'form_params' : 'body';

        return $this->request($url, 'DELETE', [$key => $options]);
    }

    /**
     * JSON request.
     *
     * @param string $url
     * @param string|array $options
     * @param array $queries
     * @param int $encodeOption
     *
     * @return ResponseInterface
     *
     * @throws HttpException
     */
    public function json($url, $options = [], $encodeOption = JSON_UNESCAPED_UNICODE, $queries = [])
    {
        is_array($options) && $options = json_encode($options, $encodeOption);

        return $this->request($url, 'POST', ['query' => $queries, 'body' => $options, 'headers' => ['content-type' => 'application/json']]);
    }

    /**
     * Upload file.
     *
     * @param string $url
     * @param array $files
     * @param array $form
     *
     * @return ResponseInterface
     *
     * @throws HttpException
     */
    public function upload($url, array $files = [], array $form = [], array $queries = [])
    {
        $multipart = [];

        foreach ($files as $name => $path) {
            $multipart[] = [
                'name' => $name,
                'contents' => fopen($path, 'r'),
            ];
        }

        foreach ($form as $name => $contents) {
            $multipart[] = compact('name', 'contents');
        }

        return $this->request($url, 'POST', ['query' => $queries, 'multipart' => $multipart]);
    }

    /**
     * Add a middleware.
     *
     * @param callable $middleware
     *
     * @return $this
     */
    public function addMiddleware(callable $middleware)
    {
        array_push($this->middlewares, $middleware);

        return $this;
    }

    /**
     * Return all middlewares.
     *
     * @return array
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    /**
     * @param ResponseInterface|string $body
     *
     * @return mixed
     *
     * @throws HttpException
     */
    public function parseJSON($body)
    {
        if ($body instanceof ResponseInterface) {
            $body = mb_convert_encoding($body->getBody(), 'UTF-8');
        }

        if (empty($body)) {
            return false;
        }

        $contents = json_decode($body, true, 512, JSON_BIGINT_AS_STRING);

        Log::debug('API response decoded:', compact('contents'));

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new HttpException('Failed to parse JSON: ' . json_last_error_msg());
        }

        return $contents;
    }
}