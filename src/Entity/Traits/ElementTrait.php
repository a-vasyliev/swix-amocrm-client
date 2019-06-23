<?php

namespace Swix\AmoCrm\Entity\Traits;

use Webmozart\Assert\Assert;

trait ElementTrait
{
    protected $elementTypes = [
        'contact' => 1,
        'lead' => 2,
        'company' => 3,
        'task' => 4,
        'customer' => 12
    ];

    /** @var int */
    private $elementId;

    /** @var int */
    private $elementType;

    /**
     * @return int
     */
    public function getElementId(): int
    {
        return $this->elementId;
    }

    /**
     * @param int $elementId
     * @return ElementTrait
     */
    public function setElementId(int $elementId): ElementTrait
    {
        $this->elementId = $elementId;

        return $this;
    }

    /**
     * @return int
     */
    public function getElementType(): int
    {
        return $this->elementType;
    }

    /**
     * @param int $elementType
     * @return ElementTrait
     */
    public function setElementType(int $elementType): ElementTrait
    {
        Assert::oneOf($elementType, $this->getElementTypes());
        $this->elementType = $elementType;

        return $this;
    }

    /**
     * @param string $name
     * @return int
     */
    public function getElementTypeId(string $name): int
    {
        Assert::keyExists($this->getElementTypes(), $name);

        return $this->elementTypes[$name];
    }

    public function getElementTypes()
    {
        return $this->elementTypes;
    }
}
