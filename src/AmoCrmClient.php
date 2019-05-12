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

    /**
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return HttpClient
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param array $scopes
     * @param bool  $freeUsers
     *
     * @return array Account information
     */
    public function getAccount(array $scopes = self::SCOPE_TYPES, $freeUsers = true)
    {
        $httpClient = $this->getHttpClient();

        Assert::allOneOf($scopes, self::SCOPE_TYPES, 'Invalid scopes given');

        $response = $httpClient->get(
            '/api/v2/account?with='.implode(',', $scopes).'&free_users='.($freeUsers ? 'Y' : 'N')
        );

        return json_decode($response->getBody()->getContents(), true);
    }
}
