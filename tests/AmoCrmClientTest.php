<?php

namespace Swix\AmoCrm\Tests;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

use PHPUnit\Framework\TestCase;

use Swix\AmoCrm\AmoCrmClient;
use Swix\AmoCrm\Extractor\ExtractorManager;
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

        $this->client = new AmoCrmClient($this->httpClient, new HydratorManager(), new ExtractorManager());
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

    public function testGetLeadsNoData()
    {
        $this->mockHandler->append(new Response(
            200,
            [],
            '{}'
        ));

        $data = $this->client->getLeads(['query' => 'does not matter']);

        $this->assertCount(0, $data);
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

    public function testGetLeadsAboveLimit()
    {
        $resourceData = json_decode(file_get_contents('./tests/resources/lead/lead_ok.json'), true);
        $resourceData = array_chunk($resourceData['_embedded']['items'], 5);

        $this->mockHandler->append(new Response(
            200,
            [],
            json_encode(['_embedded' => ['items' => $resourceData[0]]])
        ));

        unset($resourceData[1][count($resourceData[1]) - 1]);

        $this->mockHandler->append(new Response(
            200,
            [],
            json_encode(['_embedded' => ['items' => $resourceData[1]]])
        ));

        $this->client->setPageLimit(5);
        $data = $this->client->getLeads(['query' => 'does not matter'], 9);

        $this->assertCount(9, $data);
    }

    public function testGetLeadsWrongParam()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->getLeads(['wrong_param' => 'whatever']);
    }

    public function testGetLeadsWrongWith()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->getLeads(['with' => 'whatever']);
    }

    public function testGetLeadsWrongFilterDateCreate()
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->client->getLeads(['filter' => ['date_create' => ['from' => 1]]]);
    }

    public function testGetLeadsWrongFilterDateModify()
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->client->getLeads(['filter' => ['date_modify' => ['to' => 1]]]);
    }
}
