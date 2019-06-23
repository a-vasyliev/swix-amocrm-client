<?php

namespace Swix\AmoCrm\Entity;

abstract class AbstractEntity
{
    /** @var int|null */
    protected $id;

    /** @var int|null */
    protected $responsibleUserId;

    /** @var int|null */
    protected $createdAt;

    /** @var int|null */
    protected $createdBy;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        if (isset($this->id)) {
            throw new \RuntimeException('Entity ID is immutable');
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
     * @return int|null
     */
    public function getResponsibleUserId(): ?int
    {
        return $this->responsibleUserId;
    }

    /**
     * @param int|null $responsibleUserId
     * @return $this
     */
    public function setResponsibleUserId(int $responsibleUserId = null): self
    {
        $this->responsibleUserId = $responsibleUserId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    /**
     * @param int $createdAt
     * @return $this
     */
    public function setCreatedAt(int $createdAt): self
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
     * @return int|null
     */
    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }
}
