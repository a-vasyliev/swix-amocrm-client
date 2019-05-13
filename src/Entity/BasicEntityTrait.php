<?php

namespace Swix\AmoCrm\Entity;

trait BasicEntityTrait
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $responsibleUserId;

    /** @var \DateTime */
    protected $createdAt;

    /** @var int */
    protected $createdBy;

    /** @var \DateTime */
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
     * @return $this
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
     * @return \DateTime
     */
    public function getCreatedAt()
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
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param int $updatedAt
     * @return BasicEntityTrait
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
     * @return BasicEntityTrait
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
     * @return BasicEntityTrait
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
     * @return BasicEntityTrait
     */
    public function setGroupId(int $groupId): self
    {
        $this->groupId = $groupId;

        return $this;
    }
}
