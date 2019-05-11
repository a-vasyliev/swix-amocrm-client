<?php

namespace Swix\AmoCrm\HttpClient\Factory;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;
use Swix\AmoCrm\HttpClient\AuthMiddleware;

/**
 * Class HttpClientFactory
 *
 * @package Swix\AmoCrm\HttpClient\Factory
 * @author Andrii Vasyliev
 */
class HttpClientFactory
{
    public static function create($apiUrl, $login, $apiKey, $proxy = null)
    {
        $handler = HandlerStack::create();

        (new AuthMiddleware($login, $apiKey))->pushMiddleware($handler);

        $client = new HttpClient([
            'base_uri' => $apiUrl,
            'timeout' => 10,
            'allow_redirects' => true,
            'cookies' => true,
            'http_errors' => false,
            'request.options' => [
                'proxy' => $proxy
            ],
            'handler' => $handler
        ]);

        return $client;
    }
}
