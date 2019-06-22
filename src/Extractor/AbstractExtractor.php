<?php

namespace Swix\AmoCrm\Extractor;

use Swix\AmoCrm\Entity\AbstractEntity;
use Swix\AmoCrm\Entity\CustomField\CustomField;
use Swix\AmoCrm\Entity\CustomField\CustomFieldValue;

abstract class AbstractExtractor implements ExtractorInterface
{
    protected $basicFields = [
        'id',
        'responsible_user_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'account_id',
        'group_id',
        'tags',
        'custom_fields',
        'company',
    ];

    public function extractRows(array $rows): array
    {
        $extracted = [];
        foreach ($rows as $entity) {
            $extracted[] = $this->extract($entity);
        }

        return $extracted;
    }

    public function extract(AbstractEntity $entity): array
    {
        return $this->extractByFields($entity, $this->basicFields);
    }

    protected function extractByFields($entity, array $fields): array
    {
        $data = [];
        foreach ($fields as $fieldName) {
            $getter = $this->getGetterName($fieldName);

            if (!method_exists($entity, $getter)) {
                continue;
            }

            switch ($fieldName) {
                case 'tags':
                    $data['tags'] = implode(',', $entity->$getter());
                    break;
                case 'custom_fields':
                    $data['custom_fields'] = $this->extractCustomFields($entity->$getter());
                    break;
                default:
                    $data[$fieldName] = $entity->$getter();
            }
        }

        return $data;
    }

    protected function extractCustomFields(array $customFields)
    {
        $data = [];
        /** @var CustomField $customField */
        foreach ($customFields as $customField) {
            $fieldValues = [];
            /** @var CustomFieldValue $value */
            foreach ($customField->getValues() as $value) {
                $fieldValue = ['value' => $value->getValue()];

                if ($value->hasEnum()) {
                    $fieldValue['enum'] = $value->getEnum();
                }

                if ($value->hasSubtype()) {
                    $fieldValue['subtype'] = $value->getSubtype();
                }

                $fieldValues[] = $fieldValue;
            }

            $data[] = [
                'id' => $customField->getId(),
                'values' => $fieldValues
            ];
        }
    }

    protected function getGetterName($name)
    {
        return 'get' . str_replace('_', '', ucwords($name, '_'));
    }
}
