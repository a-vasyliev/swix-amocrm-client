<?php

namespace Swix\AmoCrm\Tests\Middleware;

use GuzzleHttp\HandlerStack;
use Swix\AmoCrm\Middleware\OtherErrorsMiddleware;

class OtherErrorsMiddlewareTest extends MiddlewareTestCase
{
    public function testPushMiddleware()
    {
        $handler = HandlerStack::create();
        $middleware = new OtherErrorsMiddleware();

        $middleware->pushMiddleware($handler);

        $handlers = $handler->__toString();
        $this->assertContains('Name: \'otherErrorsHandle\', Function: callable', $handlers);
    }

    public function testHandleErrorsOk()
    {
        $response = $this->getResponseMock(200, '');
        $middleware = new OtherErrorsMiddleware();

        $this->assertEquals($response, $middleware->handleErrors($response));
    }

    public function testHandleErrorsWrongResponse()
    {
        $response = $this->getResponseMock(100500, '');
        $middleware = new OtherErrorsMiddleware();

        $this->expectException('\RuntimeException');
        $middleware->handleErrors($response);
    }

    public function testHandleErrorsUnknownCode()
    {
        $error = '{{test}}';
        $response = $this->getResponseMock(100500, json_encode([
            'response' => [
                'error_code' => 100500,
                'error' => $error
            ]
        ]));

        $middleware = new OtherErrorsMiddleware();

        try {
            $middleware->handleErrors($response);
        } catch (\RuntimeException $e) {
            $this->assertEquals(100500, $e->getCode());
            $this->assertContains($error, $e->getMessage());
        }
    }

    public function testHandleErrorsKnown()
    {
        $middleware = new OtherErrorsMiddleware();

        foreach (OtherErrorsMiddleware::ERROR_CODES as $code => $message) {
            $response = $this->getResponseMock(100500, json_encode([
                'response' => [
                    'error_code' => $code
                ]
            ]));

            try {
                $middleware->handleErrors($response);
            } catch (\RuntimeException $e) {
                $this->assertEquals($code, $e->getCode());
                $this->assertEquals($message, $e->getMessage());
            }
        }
    }
}