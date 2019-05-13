<?php

namespace Swix\AmoCrm;

class Paginator
{
    /** @var \GuzzleHttp\Client */
    private $httpClient;

    /** @var int */
    private $rowsLimit;

    public function __construct(\GuzzleHttp\Client $client, int $rowsLimit = 500)
    {
        $this->httpClient = $client;
        $this->rowsLimit = $rowsLimit;
    }

    public function getHttpClient(): \GuzzleHttp\Client
    {
        return $this->httpClient;
    }

    public function getRowsLimit(): int
    {
        return $this->rowsLimit;
    }

    /**
     * Navigate through standard API structures.
     *
     * @param string $uri
     * @param array  $query
     *
     * @return array
     */
    public function paginate(string $uri, array $query = [])
    {
        $httpClient = $this->getHttpClient();
        $rowsLimit = $this->getRowsLimit();

        $data = [];
        $offset = 0;
        $query['limit_rows'] = $rowsLimit;
        $lastCount = null;

        while (null === $lastCount || $lastCount == $rowsLimit) {
            $query['limit_offset'] = $offset;

            $response = $httpClient->get($uri.'?'.http_build_query($query));
            $responseData = json_decode($response->getBody()->getContents(), true);

            if (!isset($responseData['_embedded']['items'])) {
                return $data;
            }

            $data = array_merge($data, $responseData['_embedded']['items']);

            $lastCount = count($responseData['_embedded']['items']);
            $offset += $lastCount;
        }

        return $data;
    }
}
