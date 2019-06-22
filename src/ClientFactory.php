<?php

namespace Swix\AmoCrm;

use GuzzleHttp\HandlerStack;

use Swix\AmoCrm\Extractor\ExtractorManager;
use Swix\AmoCrm\Hydrator\HydratorManager;
use Swix\AmoCrm\Middleware\AuthenticationMiddleware;
use Swix\AmoCrm\Middleware\OtherErrorsMiddleware;
use Swix\AmoCrm\Middleware\RequestRateMiddleware;

class ClientFactory
{
    public static function create(string $apiUrl, string $login, string $apiKey, $proxy = null): AmoCrmClient
    {
        $handler = HandlerStack::create();

        (new AuthenticationMiddleware($login, $apiKey))->pushMiddleware($handler);
        (new OtherErrorsMiddleware())->pushMiddleware($handler);
        (new RequestRateMiddleware())->pushMiddleware($handler);

        $client = new \GuzzleHttp\Client([
            'base_uri' => $apiUrl,
            'timeout' => 10,
            'allow_redirects' => true,
            'cookies' => true,
            'http_errors' => false,
            'request.options' => ['proxy' => $proxy],
            'handler' => $handler,
        ]);

        return new AmoCrmClient($client, new HydratorManager(), new ExtractorManager());
    }
}
