<?php

namespace Swix\AmoCrm\Middleware;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;

class OtherErrorsMiddleware
{
    const ERROR_CODES = [
        400  => 'Invalid input data or invalid custom field IDs',
        402  => 'Subscription has ended',
        403  => 'Account has been suspended due to high requests per second rate',
        429  => 'Too high requests per second rate',
        2002 => 'Nothing found by your request',
    ];

    public function pushMiddleware(HandlerStack $handlerStack)
    {
        $handlerStack->push(Middleware::mapResponse([$this, 'handleErrors']), 'otherErrorsHandle');

        return $this;
    }

    public function handleErrors(ResponseInterface $response)
    {
        if ($response->getStatusCode() == 200) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        if (!isset($data['response']) || !isset($data['response']['error_code'])) {
            throw new \RuntimeException('Unknown error with AmoCRM API');
        }

        $errorCode = $data['response']['error_code'];
        if (isset(self::ERROR_CODES[$errorCode])) {
            throw new \RuntimeException(self::ERROR_CODES[$errorCode], $errorCode);
        }

        $message = 'Unknown error with AmoCRM API';
        if (isset($data['response']['error'])) {
            $message .= ': ' . $data['response']['error'];
        }

        throw new \RuntimeException($message, $errorCode);
    }
}
