<?php

namespace Swix\AmoCrm;

use GuzzleHttp\Client as HttpClient;
use Swix\AmoCrm\Exception\InvalidScopeException;

/**
 * Class Client
 *
 * @package Swix\AmoCrm
 * @author Andrii Vasyliev
 */
class Client
{
    /** @var HttpClient */
    protected $httpClient;

    const SCOPE_TYPES = ['custom_fields', 'users', 'pipelines', 'groups', 'note_types', 'task_types'];

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
     * @param array $scope
     * @param bool $freeUsers
     * @return array Account information
     * @throws InvalidScopeException
     */
    public function account(array $scope = self::SCOPE_TYPES, bool $freeUsers = true)
    {
        $httpClient = $this->getHttpClient();

        foreach ($scope as $scopeType) {
            if (!in_array($scopeType, self::SCOPE_TYPES)) {
                throw new InvalidScopeException('Invalid scope provided');
            }
        }

        $response = $httpClient->get(
            '/api/v2/account?with=' . implode(',', $scope) . '&free_users=' . ($freeUsers ? 'Y' : 'N')
        );

        $responseData = json_decode($response->getBody()->getContents(), true);

        return $responseData;
    }
}
