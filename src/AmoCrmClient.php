<?php

namespace Swix\AmoCrm;

use GuzzleHttp\Client as HttpClient;
use Swix\AmoCrm\Paginator\PaginatorInterface;
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
            '"from" or "to" parameter is required'
        );
    }

    public function getLeads(array $params): array
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

        $httpClient = $this->getHttpClient();
        $data = $this->getPaginator()->paginate('/api/v2/leads', $params);
    }
}
