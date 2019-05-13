<?php

namespace Swix\AmoCrm\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Swix\AmoCrm\Paginator;

class PaginatorTest extends TestCase
{
    /** @var Client */
    private $httpClient;

    /** @var MockHandler */
    private $mockHandler;

    /** @var Paginator */
    private $paginator;

    /** @var HandlerStack */
    private $handler;

    const LIMIT = 10;

    protected function setUp()
    {
        $this->mockHandler = new MockHandler();
        $this->handler = HandlerStack::create($this->mockHandler);
        $this->httpClient = new Client(['handler' => $this->handler]);

        $this->paginator = new Paginator($this->httpClient, self::LIMIT);
    }

    protected function tearDown()
    {
        $this->httpClient = null;
        $this->mockHandler = null;
        $this->paginator = null;
    }

    protected function getItems($amount)
    {
        $items = [];

        while ($amount > 0) {
            $amount--;
            $items[] = ['name' => 'name #' . $amount];
        }

        return $items;
    }

    public function testGetters()
    {
        $this->assertInstanceOf('\GuzzleHttp\Client', $this->paginator->getHttpClient());
    }

    public function testPaginateNotOk()
    {
        $this->mockHandler->append(new Response(200, [], '{"test":"ok"}'));
        $result = $this->paginator->paginate('test');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testPaginateBelowLimit()
    {
        $items = $this->getItems(self::LIMIT - 3);
        $this->mockHandler->append(new Response(200, [], json_encode(['_embedded' => ['items' => $items]])));

        $actual = $expected = [];
        $this->handler->push(Middleware::mapRequest(function (Request $request) use (&$expected, &$actual) {
            $offset = count($actual) * self::LIMIT;

            $expected[] = 'some=thing&limit_rows=' . self::LIMIT . '&limit_offset=' . $offset;
            $actual[] = $request->getUri()->getQuery();

            return $request;
        }));

        $result = $this->paginator->paginate('test', ['some' => 'thing']);

        $this->assertEquals($items, $result);
        $this->assertEquals($expected, $actual);
        $this->assertCount(1, $actual); // should be called only once
    }

    public function testPaginateEqLimit()
    {
        $items = $this->getItems(self::LIMIT);

        $this->mockHandler->append(new Response(200, [], json_encode(['_embedded' => ['items' => $items]])));
        $this->mockHandler->append(new Response(200, [], json_encode(['_embedded' => ['items' => []]])));

        $actual = $expected = [];
        $this->handler->push(Middleware::mapRequest(function (Request $request) use (&$expected, &$actual) {
            $offset = count($actual) * self::LIMIT;

            $expected[] = 'some=thing&limit_rows=' . self::LIMIT . '&limit_offset=' . $offset;
            $actual[] = $request->getUri()->getQuery();

            return $request;
        }));

        $result = $this->paginator->paginate('test', ['some' => 'thing']);

        $this->assertEquals($items, $result);
        $this->assertEquals($expected, $actual);
        $this->assertCount(2, $actual);
    }

    public function testPaginateAboveLimit()
    {
        $items1 = $this->getItems(self::LIMIT);
        $items2 = $items1;
        $items3 = $this->getItems(self::LIMIT / 2);

        $this->mockHandler->append(new Response(200, [], json_encode(['_embedded' => ['items' => $items1]])));
        $this->mockHandler->append(new Response(200, [], json_encode(['_embedded' => ['items' => $items2]])));
        $this->mockHandler->append(new Response(200, [], json_encode(['_embedded' => ['items' => $items3]])));

        $actual = $expected = [];
        $this->handler->push(Middleware::mapRequest(function (Request $request) use (&$expected, &$actual) {
            $offset = count($actual) * self::LIMIT;

            $expected[] = 'some=thing&limit_rows=' . self::LIMIT . '&limit_offset=' . $offset;
            $actual[] = $request->getUri()->getQuery();

            return $request;
        }));

        $result = $this->paginator->paginate('test', ['some' => 'thing']);

        $this->assertEquals(array_merge($items1, $items2, $items3), $result);
        $this->assertEquals($expected, $actual);
        $this->assertCount(3, $actual);
    }
}
