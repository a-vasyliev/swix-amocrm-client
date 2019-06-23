<?php

namespace Swix\AmoCrm\Entity;

use Swix\AmoCrm\Entity\Traits\CommonFieldsTrait;
use Swix\AmoCrm\Entity\Traits\CompanyTrait;
use Swix\AmoCrm\Entity\Traits\CustomFieldsTrait;
use Swix\AmoCrm\Entity\Traits\TagsTrait;
use Webmozart\Assert\Assert;

class Lead extends AbstractEntity
{
    use TagsTrait;
    use CustomFieldsTrait;
    use CompanyTrait;
    use CommonFieldsTrait;

    /** @var string|null */
    private $name;

    /** @var bool|null */
    private $isDeleted;

    /** @var array|null */
    private $mainContact;

    /** @var int|null */
    private $closedAt;

    /** @var int|null */
    private $closestTaskAt;

    /** @var int[]|null */
    private $contacts;

    /** @var int|null */
    private $statusId;

    /** @var int|null */
    private $sale;

    /** @var int */
    private $pipelineId;

    /** @var int|null */
    private $lossReasonId = 0;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Lead
     */
    public function setName(?string $name): Lead
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool|null $isDeleted
     * @return Lead
     */
    public function setIsDeleted(?bool $isDeleted): Lead
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getMainContact(): ?array
    {
        return $this->mainContact;
    }

    /**
     * @param array|null $mainContact
     * @return Lead
     */
    public function setMainContact(array $mainContact = null): Lead
    {
        if (is_array($mainContact) && !empty($mainContact)) {
            Assert::keyExists($mainContact, 'id');
        } else {
            $mainContact = null;
        }

        $this->mainContact = $mainContact;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClosedAt(): ?int
    {
        return $this->closedAt;
    }

    /**
     * @param int|null $closedAt
     * @return Lead
     */
    public function setClosedAt(int $closedAt = null): Lead
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClosestTaskAt(): ?int
    {
        return $this->closestTaskAt;
    }

    /**
     * @param int|null $closestTaskAt
     * @return Lead
     */
    public function setClosestTaskAt(int $closestTaskAt = null): Lead
    {
        $this->closestTaskAt = $closestTaskAt;

        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getContacts(): ?array
    {
        return $this->contacts;
    }

    /**
     * @param int[]|null $contacts
     * @return Lead
     */
    public function setContacts(array $contacts = null): Lead
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatusId(): ?int
    {
        return $this->statusId;
    }

    /**
     * @param int|null $statusId
     * @return Lead
     */
    public function setStatusId(int $statusId): Lead
    {
        $this->statusId = $statusId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSale(): ?int
    {
        return $this->sale;
    }

    /**
     * @param int|null $sale
     * @return Lead
     */
    public function setSale(int $sale): Lead
    {
        $this->sale = $sale;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPipelineId(): ?int
    {
        return $this->pipelineId;
    }

    /**
     * @param int $pipelineId
     * @return Lead
     */
    public function setPipelineId(int $pipelineId): Lead
    {
        $this->pipelineId = $pipelineId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLossReasonId(): ?int
    {
        return $this->lossReasonId;
    }

    /**
     * @param int|null $lossReasonId
     * @return Lead
     */
    public function setLossReasonId(?int $lossReasonId): Lead
    {
        $this->lossReasonId = $lossReasonId;

        return $this;
    }
}
