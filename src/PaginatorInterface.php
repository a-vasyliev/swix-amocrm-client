<?php

namespace Swix\AmoCrm;

interface PaginatorInterface
{
    public function __construct(\GuzzleHttp\Client $client, int $rowsLimit = 500);

    public function getHttpClient(): \GuzzleHttp\Client;

    public function getRowsLimit(): int;

    public function paginate(string $uri, array $query = []): array;
}
