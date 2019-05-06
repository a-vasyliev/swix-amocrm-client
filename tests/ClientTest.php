<?php

namespace Swix\AmoCrm\Test;

use GuzzleHttp\Client as HttpClient;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    protected function getHttpClientMock()
    {
        $httpClient = $this->createMock(HttpClient::class);

        return $httpClient;
    }

    public function testAuth()
    {

    }
}
