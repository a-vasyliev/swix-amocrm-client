<?php

namespace Swix\AmoCrm\Entity\CustomField;

class CustomFieldValue
{
    /** @var mixed */
    private $value;

    /** @var string|null */
    private $enum;

    /** @var string|null */
    private $subtype;

    /** @var bool */
    private $isSystem;

    public function __construct($value, string $enum = null, string $subtype = null, bool $isSystem = false)
    {
        $this->value    = $value;
        $this->enum     = $enum;
        $this->subtype  = $subtype;
        $this->isSystem = $isSystem;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string|null
     */
    public function getEnum(): ?string
    {
        return $this->enum;
    }

    /**
     * @return string|null
     */
    public function getSubtype(): ?string
    {
        return $this->subtype;
    }

    /**
     * @return bool
     */
    public function isSystem(): bool
    {
        return $this->isSystem;
    }
}
