<?php

namespace Swix\AmoCrm\Tests;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

use PHPUnit\Framework\TestCase;

use Swix\AmoCrm\AmoCrmClient;
use Swix\AmoCrm\Paginator;

/**
 * Class ClientTest.
 *
 * @author Andrii Vasyliev
 */
class AmoCrmClientTest extends TestCase
{
    /** @var HttpClient */
    private $httpClient;

    /** @var MockHandler */
    private $mockHandler;

    /** @var AmoCrmClient */
    private $client;

    protected function setUp()
    {
        $this->mockHandler = new MockHandler();
        $this->httpClient = new HttpClient(['handler' => HandlerStack::create($this->mockHandler)]);

        $this->client = new AmoCrmClient($this->httpClient, new Paginator($this->httpClient));
    }

    protected function tearDown()
    {
        $this->httpClient = null;
        $this->mockHandler = null;
        $this->client = null;
    }

    public function testGetAccountWrongScopeTest()
    {
        $this->expectException('\InvalidArgumentException');
        $this->client->getAccount(['test']);
    }

    public function testGetAccountOk()
    {
        $this->mockHandler->append(new Response(200, [], '{"test":"ok"}'));

        $data = $this->client->getAccount(['users', 'custom_fields']);
        $this->assertEquals(['test' => 'ok'], $data);
    }
}
