<?php

namespace Swix\AmoCrm\Middleware;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

class RequestRateMiddleware
{
    protected $firstRequestTime;

    protected $counter = 0;

    const RATE_LIMIT = 7;

    public function pushMiddleware(HandlerStack $handlerStack)
    {
        $handlerStack->push(Middleware::mapRequest([$this, 'checkRequestRate']), 'requestRateCheck');
    }

    public function checkRequestRate(RequestInterface $request)
    {
        ++$this->counter;

        $now = microtime(true);
        if ($now - $this->firstRequestTime >= 1) {
            $this->counter = 0; // more than a second or empty value
        }

        if (self::RATE_LIMIT == $this->counter) {
            sleep(1);
            $this->counter = 0; // after sleep
        }

        if (0 == $this->counter) {
            $this->firstRequestTime = $now;
        }

        return $request;
    }
}
