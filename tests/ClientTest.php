<?php

namespace Swix\AmoCrm\Tests;

use PHPUnit\Framework\TestCase;
use Swix\AmoCrm\Client;

class ClientTest extends TestCase
{
    public function testCreate()
    {
        $client = Client::create('test', 'test', 'test');
        $this->assertInstanceOf('\Swix\AmoCrm\AmoCrmClient', $client);
    }
}
