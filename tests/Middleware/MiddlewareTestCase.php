<?php

namespace Swix\AmoCrm\Tests\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class MiddlewareTestCase extends TestCase
{
    /**
     * @param string $uriString
     * @return \PHPUnit\Framework\MockObject\MockObject|RequestInterface
     */
    protected function getRequestMock(string $uriString)
    {
        $uri = $this->getMockBuilder('\GuzzleHttp\Psr7\Uri')->getMock();
        $request = $this->getMockBuilder('\Psr\Http\Message\RequestInterface')->getMock();

        $request
            ->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue($uri));

        $request
            ->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue([]));

        $uri
            ->expects($this->any())
            ->method('__toString')
            ->will($this->returnValue($uriString));

        return $request;
    }

    protected function getResponseMock($statusCode, $responseString)
    {
        $response = $this->getMockBuilder('\Psr\Http\Message\ResponseInterface')->getMock();
        $stream = $this->getMockBuilder('\Psr\Http\Message\StreamInterface')->getMock();

        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $response
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($stream));

        $stream
            ->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue($responseString));

        return $response;
    }
}
