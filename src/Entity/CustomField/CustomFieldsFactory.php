<?php

namespace Swix\AmoCrm\Entity\CustomField;

use Webmozart\Assert\Assert;

class CustomFieldsFactory
{
    public static function create(array $customFields): array
    {
        $fields = [];

        foreach ($customFields as $field) {
            Assert::keyExists($field, 'id');
            Assert::keyExists($field, 'values');

            $fields[] = (new CustomField(
                $field['id'],
                isset($field['name']) ? $field['name'] : null
            ))->setValues(CustomFieldValueFactory::create($field['values']));
        }

        return $fields;
    }
}
