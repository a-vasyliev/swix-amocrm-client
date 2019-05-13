<?php

namespace Swix\AmoCrm;

use GuzzleHttp\Client as HttpClient;
use Webmozart\Assert\Assert;

/**
 * Class Client.
 *
 * @author Andrii Vasyliev
 */
class AmoCrmClient
{
    /**
     * @var array
     *
     * @see https://www.amocrm.ru/developers/content/api/account#values
     */
    const SCOPE_TYPES = ['custom_fields', 'users', 'pipelines', 'groups', 'note_types', 'task_types'];

    /** @var HttpClient */
    protected $httpClient;

    /** @var PaginatorInterface */
    protected $paginator;

    /**
     * @param HttpClient $httpClient
     * @param PaginatorInterface $paginator
     */
    public function __construct(HttpClient $httpClient, PaginatorInterface $paginator)
    {
        $this->httpClient = $httpClient;
        $this->paginator  = $paginator;
    }

    /**
     * @return HttpClient
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @return PaginatorInterface
     */
    protected function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @param array $scopes
     * @param bool  $freeUsers
     *
     * @return array Account information
     */
    public function getAccount(array $scopes = self::SCOPE_TYPES, $freeUsers = true): array
    {
        $httpClient = $this->getHttpClient();

        Assert::allOneOf($scopes, self::SCOPE_TYPES, 'Invalid scopes given');

        $response = $httpClient->get(
            '/api/v2/account?'
            . http_build_query(['with' => implode(',', $scopes), 'free_users' => $freeUsers ? 'Y' : 'N'])
        );

        return json_decode($response->getBody()->getContents(), true);
    }
}
