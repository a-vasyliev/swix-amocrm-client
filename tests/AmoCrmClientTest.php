<?php

namespace Swix\AmoCrm\Tests;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

use PHPUnit\Framework\TestCase;

use Swix\AmoCrm\AmoCrmClient;
use Swix\AmoCrm\Hydrator\HydratorManager;

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

        $this->client = new AmoCrmClient($this->httpClient, new HydratorManager());
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

    public function testGetLeadsOk()
    {
        $this->mockHandler->append(new Response(
            200,
            [],
            file_get_contents('./tests/resources/lead/lead_ok.json')
        ));

        $data = $this->client->getLeads(['query' => 'does not matter']);

        $this->assertCount(10, $data);
    }

    /**
     * Provide 10 results, but set the limit higher.
     * There must not be a second API call, because we have smaller rows count than given limit.
     */
    public function testGetLeadsBelowLimit()
    {
        $this->mockHandler->append(new Response(
            200,
            [],
            file_get_contents('./tests/resources/lead/lead_ok.json')
        ));

        $data = $this->client->getLeads(['query' => 'does not matter'], 11);

        $this->assertCount(10, $data);
    }

    /**
     * Provide 10 results, but set the limit equal.
     * There must not be a second API call, because we have fulfilled limit.
     */
    public function testGetLeadsEqLimit()
    {
        $this->mockHandler->append(new Response(
            200,
            [],
            file_get_contents('./tests/resources/lead/lead_ok.json')
        ));

        $data = $this->client->getLeads(['query' => 'does not matter'], 10);

        $this->assertCount(10, $data);
    }
}
