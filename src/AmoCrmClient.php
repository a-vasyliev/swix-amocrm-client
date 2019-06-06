<?php

namespace Swix\AmoCrm;

use GuzzleHttp\Client as HttpClient;
use Swix\AmoCrm\Hydrator\HydratorManager;
use Webmozart\Assert\Assert;

/**
 * Class Client.
 *
 * @author Andrii Vasyliev
 */
class AmoCrmClient
{
    const ACCOUNT_SCOPES = ['custom_fields', 'users', 'pipelines', 'groups', 'note_types', 'task_types'];

    const LEADS_PARAMS = ['id', 'query', 'responsible_user_id', 'with', 'status', 'filter'];
    const LEADS_WITH = ['is_price_modified_by_robot', 'loss_reason_name'];

    const ITEMS_PER_PAGE = 500;

    /** @var HttpClient */
    protected $httpClient;

    /** @var HydratorManager */
    protected $hydratorManager;

    /**
     * @param HttpClient $httpClient
     * @param HydratorManager $manager
     */
    public function __construct(HttpClient $httpClient, HydratorManager $manager)
    {
        $this->httpClient      = $httpClient;
        $this->hydratorManager = $manager;
    }

    /**
     * @return HttpClient
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }


    /**
     * @return HydratorManager
     */
    protected function getHydratorManager()
    {
        return $this->hydratorManager;
    }

    /**
     * Navigate through standard API structures.
     *
     * @param string $uri
     * @param array $query
     * @param int $limit
     *
     * @return array
     */
    protected function paginate(string $uri, array $query = [], int $limit = null): array
    {
        Assert::nullOrNotEq($limit, 0);

        $httpClient = $this->getHttpClient();

        $data = [];
        $smallestLimit = isset($limit) && $limit < self::ITEMS_PER_PAGE ? $limit : self::ITEMS_PER_PAGE;
        $query['limit_rows'] = $smallestLimit;
        $lastCount = $offset = 0;

        while ($offset == 0 || $lastCount == $smallestLimit) {
            $query['limit_offset'] = $offset;

            // low down the option to get not more than provided limit
            if (isset($limit) && $limit - $offset < $query['limit_rows']) {
                $query['limit_rows'] = $limit - $offset;
            }

            $response = $httpClient->get($uri . '?' . http_build_query($query));
            $responseData = json_decode($response->getBody()->getContents(), true);

            if (!isset($responseData['_embedded']['items'])) {
                return $data;
            }

            $data = array_merge($data, $responseData['_embedded']['items']);

            $lastCount = count($responseData['_embedded']['items']);
            $offset += $lastCount;

            if (isset($limit) && $limit - $offset == 0) {
                return $data; // limit is fulfilled
            }
        }

        return $data;
    }

    /**
     * @param array $scopes
     * @param bool  $freeUsers
     *
     * @return array Account information
     */
    public function getAccount(array $scopes = self::ACCOUNT_SCOPES, $freeUsers = true): array
    {
        $httpClient = $this->getHttpClient();

        Assert::allOneOf($scopes, self::ACCOUNT_SCOPES, 'Invalid scopes given');

        $response = $httpClient->get(
            '/api/v2/account?'
            .http_build_query(['with' => implode(',', $scopes), 'free_users' => $freeUsers ? 'Y' : 'N'])
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function assertLeadsFilterDate(array $date): void
    {
        Assert::false(
            !isset($date['from']) && !isset($date['to']),
            'At least one of "from" or "to" parameters is required'
        );
    }

    public function getLeads(array $params = [], int $limit = null): array
    {
        Assert::allOneOf(array_keys($params), self::LEADS_PARAMS);

        if (isset($params['with'])) {
            Assert::allOneOf($params['with'], self::LEADS_WITH);
        }

        if (isset($params['filter'])) {
            if (isset($params['filter']['date_create'])) {
                $this->assertLeadsFilterDate($params['filter']['date_create']);
            }

            if (isset($params['filter']['date_modify'])) {
                $this->assertLeadsFilterDate($params['filter']['date_modify']);
            }
        }

        $data = $this->paginate('/api/v2/leads', $params, $limit);
        $hydrator = $this->getHydratorManager()->getHydrator('\Swix\AmoCrm\Entity\Lead');

        return $hydrator->hydrateRows($data);
    }
}
