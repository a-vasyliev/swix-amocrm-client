<?php

namespace Swix\AmoCrm\Entity;

use Swix\AmoCrm\Entity\Traits\CompanyTrait;
use Swix\AmoCrm\Entity\Traits\CustomFieldsTrait;
use Swix\AmoCrm\Entity\Traits\TagsTrait;

class Contact extends AbstractEntity
{
    use CompanyTrait;
    use TagsTrait;
    use CustomFieldsTrait;

    /** @var string */
    protected $name;

    /** @var array */
    protected $leadIds = [];

    /** @var int|null */
    protected $closestTaskAt;

    /** @var array */
    protected $customers;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Contact
     */
    public function setName(string $name): Contact
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getLeadIds(): array
    {
        return $this->leadIds;
    }

    /**
     * @param array $leadIds
     * @return Contact
     */
    public function setLeadIds(array $leadIds): Contact
    {
        $this->leadIds = $leadIds;

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
     * @return Contact
     */
    public function setClosestTaskAt(?int $closestTaskAt): Contact
    {
        $this->closestTaskAt = $closestTaskAt;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomers(): array
    {
        return $this->customers;
    }

    /**
     * @param array $customers
     * @return Contact
     */
    public function setCustomers(array $customers): Contact
    {
        $this->customers = $customers;

        return $this;
    }
}
