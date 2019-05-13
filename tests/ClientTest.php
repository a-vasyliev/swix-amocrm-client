<?php

namespace Swix\AmoCrm\Tests;

use PHPUnit\Framework\TestCase;
use Swix\AmoCrm\ClientFactory;

class ClientTest extends TestCase
{
    public function testCreate()
    {
        $client = ClientFactory::create('test', 'test', 'test');
        $this->assertInstanceOf('\Swix\AmoCrm\AmoCrmClient', $client);
    }
}
