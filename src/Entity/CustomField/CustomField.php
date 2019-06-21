<?php

namespace Swix\AmoCrm\Entity\CustomField;

class CustomField
{
    private $id;

    private $name;

    private $values = [];

    public function __construct(int $id, string $name = null, array $values = [])
    {
        foreach ($values as $value) {
            $this->addValue($value);
        }

        $this->id = $id;
        $this->name = $name;
    }

    public function addValue(CustomFieldValue $value)
    {
        $this->values[] = $value;
    }

    public function setValues(array $values)
    {
        $this->values = [];
        foreach ($values as $value) {
            $this->addValue($value);
        }

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
