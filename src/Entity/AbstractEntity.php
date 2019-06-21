<?php

namespace Swix\AmoCrm\Entity;

abstract class AbstractEntity
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $responsibleUserId;

    /** @var int */
    protected $createdAt;

    /** @var int */
    protected $createdBy;

    /** @var int */
    protected $updatedAt;

    /** @var int */
    protected $updatedBy;

    /** @var int */
    protected $accountId;

    /** @var int */
    protected $groupId;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId($id): self
    {
        if (isset($this->id)) {
            //thr not immutable
        }
        $this->id = $id;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasId()
    {
        return !empty($this->getId());
    }

    /**
     * @return int
     */
    public function getResponsibleUserId()
    {
        return $this->responsibleUserId;
    }

    /**
     * @param int $responsibleUserId
     * @return $this
     */
    public function setResponsibleUserId($responsibleUserId): self
    {
        $this->responsibleUserId = $responsibleUserId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @param int $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param int $createdBy
     * @return self
     */
    public function setCreatedBy(int $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    /**
     * @return int
     */
    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    /**
     * @param int $updatedAt
     * @return self
     */
    public function setUpdatedAt(int $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param int $updatedBy
     * @return self
     */
    public function setUpdatedBy(int $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     * @return self
     */
    public function setAccountId(int $accountId): self
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     * @return self
     */
    public function setGroupId(int $groupId): self
    {
        $this->groupId = $groupId;

        return $this;
    }
}