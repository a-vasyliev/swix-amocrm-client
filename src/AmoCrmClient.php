<?php

namespace Swix\AmoCrm;

use GuzzleHttp\Client as HttpClient;
use Swix\AmoCrm\Entity\Contact;
use Swix\AmoCrm\Entity\Lead;
use Swix\AmoCrm\Entity\Note;
use Swix\AmoCrm\Extractor\ExtractorManager;
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

    const LEADS_PARAMS = ['id', 'query', 'responsible_user_id', 'with', 'status', 'filter', 'if-modified-since'];
    const LEADS_WITH = ['is_price_modified_by_robot', 'loss_reason_name'];

    const CONTACTS_PARAMS = ['id', 'query', 'responsible_user_id', 'if-modified-since'];

    const NOTES_PARAMS = ['id', 'type', 'require', 'element_id', 'note_type', 'if-modified-since'];
    const NOTE_ELEMENT_TYPES = ['contact', 'lead', 'company', 'task', 'customer'];

    /** @var int */
    protected $pageLimit = 500;

    /** @var HttpClient */
    protected $httpClient;

    /** @var HydratorManager */
    protected $hydratorManager;

    /** @var ExtractorManager */
    protected $extractorManager;

    /**
     * @param HttpClient $httpClient
     * @param HydratorManager $hydratorManager
     * @param ExtractorManager $extractorManager
     */
    public function __construct(
        HttpClient $httpClient,
        HydratorManager $hydratorManager,
        ExtractorManager $extractorManager
    ) {
        $this->httpClient       = $httpClient;
        $this->hydratorManager  = $hydratorManager;
        $this->extractorManager = $extractorManager;
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
     * @return ExtractorManager
     */
    protected function getExtractorManager()
    {
        return $this->extractorManager;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setPageLimit(int $limit): self
    {
        $this->pageLimit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getPageLimit(): int
    {
        return $this->pageLimit;
    }

    /**
     * Navigate through standard API structures.
     *
     * @param string $uri
     * @param string $entityClass
     * @param array $query
     * @param int $limit
     *
     * @return array
     */
    protected function paginate(string $uri, string $entityClass, array $query = [], int $limit = null): array
    {
        Assert::nullOrNotEq($limit, 0);

        $httpClient = $this->getHttpClient();

        $data = [];
        $pageLimit = $this->getPageLimit();
        $smallestLimit = isset($limit) && $limit < $pageLimit ? $limit : $pageLimit;
        $query['limit_rows'] = $smallestLimit;
        $lastCount = $offset = 0;

        $headers = [];
        if (isset($query['if-modified-since'])) {
            /** @var \DateTime $modifiedSince */
            $modifiedSince = $query['if-modified-since'];
            Assert::isInstanceOf($modifiedSince, '\DateTime');

            $headers['If-Modified-Since'] = $modifiedSince->format('D, d M Y H:i:s T');
            unset($query['if-modified-since']);
        }

        while ($offset == 0 || $lastCount == $smallestLimit) {
            $query['limit_offset'] = $offset;

            // low down the option to get not more than provided limit
            if (isset($limit) && $limit - $offset < $query['limit_rows']) {
                $query['limit_rows'] = $limit - $offset;
            }

            $response = $httpClient->get($uri . '?' . http_build_query($query), [
                'headers' => $headers
            ]);
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

        $hydrator = $this->getHydratorManager()->get($entityClass);

        return $hydrator->hydrateRows($data);
    }

    protected function post(string $url, array $entities)
    {
        if (count($entities) == 0) {
            return [];
        }
        $add = $update = [];

        $entityClass = '\\' . get_class(current($entities));
        $extractor = $this->getExtractorManager()->get($entityClass);

        foreach ($entities as $key => $entity) {
            Assert::methodExists($entity, 'hasId');

            $extracted = $extractor->extract($entity);

            // Mark rows with request_id to be able to map entity IDs to entity objects.
            $extracted['request_id'] = $key;

            if ($entity->hasId()) {
                $update[] = $extracted;
            } else {
                $add[] = $extracted;
            }
        }

        $client = $this->getHttpClient();

        $response = $client->post($url, [
            'json' => [
                'add' => $add,
                'update' => $update
            ]
        ]);
        $responseData = json_decode($response->getBody()->getContents(), true);

        if (isset($responseData['_embedded']['errors'])) {
            $errorList = $responseData['_embedded']['errors'];
            $addErrors = $updateErrors = '';

            if (isset($errorList['add'])) {
                $addErrors = 'Issues with adding new entities of ' . $entityClass . ':'
                    . PHP_EOL . implode(PHP_EOL, $errorList['add']);
            }

            if (isset($errorList['update'])) {
                $updateErrors = 'Issues with updating new entities of ' . $entityClass . ':'
                    . PHP_EOL . implode(PHP_EOL, $errorList['update']);
            }

            throw new \RuntimeException(implode(PHP_EOL . PHP_EOL, [$addErrors, $updateErrors]));
        }

        foreach ($responseData['_embedded']['items'] as $item) {
            Assert::keyExists($entities, $item['request_id']);
            Assert::methodExists($entities[$item['request_id']], 'setId');

            // Set entity ID provided by AmoCRM
            $entities[$item['request_id']]->setId($item['id']);
        }

        return $entities;
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

    /**
     * @param array $params
     * @param int|null $limit
     * @return Lead[]
     */
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

        return $this->paginate('/api/v2/leads', '\Swix\AmoCrm\Entity\Lead', $params, $limit);
    }

    /**
     * @param array $ids
     * @return Lead[]
     */
    public function getLeadsByIds(array $ids): array
    {
        Assert::allNumeric($ids);

        return $this->getLeads(['id' => $ids]);
    }

    /**
     * @param Lead[] $leads
     * @return $this
     */
    public function saveLeads(array $leads): self
    {
        $this->post('/api/v2/leads', $leads);

        return $this;
    }

    /**
     * @param array $params
     * @param int|null $limit
     * @return Contact[]
     */
    public function getContacts(array $params = [], int $limit = null): array
    {
        Assert::allOneOf(array_keys($params), self::CONTACTS_PARAMS);

        return $this->paginate('/api/v2/contacts', '\Swix\AmoCrm\Entity\Contact', $params, $limit);
    }

    /**
     * @param array $ids
     * @return Contact[]
     */
    public function getContactsByIds(array $ids): array
    {
        Assert::allNumeric($ids);

        return $this->getContacts(['id' => $ids]);
    }

    /**
     * @param Contact[] $contacts
     * @return $this
     */
    public function saveContacts(array $contacts): self
    {
        $this->post('/api/v2/contacts', $contacts);

        return $this;
    }

    /**
     * @param array $params
     * @param int|null $limit
     * @return Note[]
     */
    public function getNotes(array $params = [], int $limit = null): array
    {
        Assert::allOneOf(array_keys($params), self::NOTES_PARAMS);

        if (isset($params['type'])) {
            Assert::allOneOf($params['type'], array_keys(self::NOTE_ELEMENT_TYPES));
        }

        if (isset($params['require'])) {
            Assert::boolean($params['require']);
        }

        if (isset($params['element_id'])) {
            Assert::numeric($params['element_id']);
        }

        if (isset($params['note_type'])) {
            Assert::allOneOf($params['note_type'], Note::NOTE_TYPES);
        }

        return $this->paginate('/api/v2/notes', '\Swix\AmoCrm\Entity\Note', $params, $limit);
    }

    /**
     * @param array $ids
     * @return Note[]
     */
    public function getNotesByIds(array $ids): array
    {
        Assert::allNumeric($ids);

        return $this->getNotes(['id' => $ids]);
    }

    /**
     * @param Note[] $notes
     * @return $this
     */
    public function saveNotes(array $notes): self
    {
        $this->post('/api/v2/notes', $notes);

        return $this;
    }
}
