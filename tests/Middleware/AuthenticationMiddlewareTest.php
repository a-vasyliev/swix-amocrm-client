<?php

namespace Swix\AmoCrm\Tests\Middleware;

use GuzzleHttp\HandlerStack;
use Swix\AmoCrm\Exception\AuthenticationException;
use Swix\AmoCrm\Middleware\AuthenticationMiddleware;

class AuthenticationMiddlewareTest extends MiddlewareTestCase
{
    public function testConstruct()
    {
        $middleware = new AuthenticationMiddleware('login', 'apiKey');

        $this->assertEquals('login', $middleware->getLogin());
        $this->assertEquals('apiKey', $middleware->getApiKey());
    }

    public function testAddParametersNoOtherParams()
    {
        $request = $this->getRequestMock('http://test.com');

        $middleware = new AuthenticationMiddleware('login', 'apiKey');
        $newRequest = $middleware->addParameters($request);

        $this->assertInstanceOf('\Psr\Http\Message\RequestInterface', $newRequest);
        $this->assertEquals('http://test.com?login=login&api_key=apiKey', $newRequest->getUri()->__toString());
    }

    public function testAddParametersWithOtherParams()
    {
        $request = $this->getRequestMock('http://test.com?well=something');

        $middleware = new AuthenticationMiddleware('login', 'apiKey');
        $newRequest = $middleware->addParameters($request);

        $this->assertInstanceOf('\Psr\Http\Message\RequestInterface', $newRequest);
        $this->assertEquals(
            'http://test.com?well=something&login=login&api_key=apiKey',
            $newRequest->getUri()->__toString()
        );
    }

    public function testPushMiddleware()
    {
        $handler = HandlerStack::create();
        $middleware = new AuthenticationMiddleware('login', 'apiKey');

        $middleware->pushMiddleware($handler);

        $handlers = $handler->__toString();
        $this->assertContains('Name: \'authAddParams\', Function: callable', $handlers);
        $this->assertContains('Name: \'authHandleErrors\', Function: callable', $handlers);
    }

    public function testHandleErrorsOk()
    {
        $response = $this->getResponseMock(200, '');
        $middleware = new AuthenticationMiddleware('login', 'apiKey');

        $this->assertEquals($response, $middleware->handleErrors($response));
    }

    public function testHandleErrorsUnknown()
    {
        $response = $this->getResponseMock(100500, '{"test":"ok"}');
        $middleware = new AuthenticationMiddleware('login', 'apiKey');

        $this->assertEquals($response, $middleware->handleErrors($response));
    }

    public function testHandleErrorsUnknownCode()
    {
        $response = $this->getResponseMock(100500, json_encode([
            'response' => [
                'error_code' => 100500
            ]
        ]));

        $middleware = new AuthenticationMiddleware('login', 'apiKey');
        $this->assertEquals($response, $middleware->handleErrors($response));
    }

    public function testHandleErrorsKnown()
    {
        $middleware = new AuthenticationMiddleware('login', 'apiKey');
        $domain = 'test.com';

        foreach (AuthenticationMiddleware::ERROR_CODES as $code => $message) {
            $responseData = [
                'response' => [
                    'error_code' => $code,
                    'domain' => $domain
                ]
            ];

            $message = str_replace('{{domain}}', $domain, $message);
            $response = $this->getResponseMock(100500, json_encode($responseData));

            try {
                $middleware->handleErrors($response);
            } catch (AuthenticationException $e) {
                $this->assertEquals($code, $e->getCode());
                $this->assertEquals($message, $e->getMessage());
            }
        }
    }
}
