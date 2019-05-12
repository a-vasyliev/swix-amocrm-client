<?php

namespace Swix\AmoCrm\Tests\Middleware;

use GuzzleHttp\HandlerStack;
use Swix\AmoCrm\Middleware\RequestRateMiddleware;

class RequestRateMiddlewareTest extends MiddlewareTestCase
{
    public function testPushMiddleware()
    {
        $handler = HandlerStack::create();
        $middleware = new RequestRateMiddleware();

        $middleware->pushMiddleware($handler);

        $handlers = $handler->__toString();
        $this->assertContains('Name: \'requestRateCheck\', Function: callable', $handlers);
    }

    public function testCheckRequestRateBelowLimit()
    {
        $response = $this->getRequestMock('');
        $middleware = new RequestRateMiddleware();

        $start = microtime(true);

        $this->assertEquals($response, $middleware->checkRequestRate($response));
        $this->assertEquals($response, $middleware->checkRequestRate($response));
        $this->assertEquals($response, $middleware->checkRequestRate($response));

        $this->assertLessThan(1, microtime(true) - $start);
    }

    public function testCheckRequestRateEqLimit()
    {
        $response = $this->getRequestMock('');
        $middleware = new RequestRateMiddleware();

        $start = microtime(true);

        $i = 1;
        while ($i <= 7) {
            $this->assertEquals($response, $middleware->checkRequestRate($response));
            $i++;
        }

        $this->assertLessThan(1, microtime(true) - $start);
    }

    public function testCheckRequestRateAboveLimit()
    {
        $response = $this->getRequestMock('');
        $middleware = new RequestRateMiddleware();

        $start = microtime(true);

        $i = 1;
        while ($i <= 10) {
            $this->assertEquals($response, $middleware->checkRequestRate($response));
            $i++;
        }

        $this->assertGreaterThan(1, microtime(true) - $start);
    }
}