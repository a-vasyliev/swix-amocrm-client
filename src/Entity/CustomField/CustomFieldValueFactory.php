<?php

namespace Swix\AmoCrm\Entity\CustomField;

use Webmozart\Assert\Assert;

class CustomFieldValueFactory
{
    public static function create(array $values)
    {
        $valueObjects = [];
        foreach ($values as $value) {
            if (is_array($value)) {
                $valueObject = self::fromArray($value);
            } else {
                $valueObject = self::fromInlineArray($value);
            }


            $valueObjects[] = $valueObject;
        }

        return $valueObjects;
    }

    private static function fromInlineArray($value)
    {
        Assert::keyNotExists($value, 'value');

        return new CustomFieldValue($value);
    }

    private static function fromArray(array $value)
    {
        Assert::keyExists($value, 'value');

        $enum = isset($value['enum']) ? $value['enum'] : null;
        $subtype = isset($value['subtype']) ? $value['subtype'] : null;
        $isSystem = isset($value['is_system']) ? $value['is_system'] : false;

        return new CustomFieldValue($value['value'], $enum, $subtype, $isSystem);
    }
}