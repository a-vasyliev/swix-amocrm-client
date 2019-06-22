<?php

namespace Swix\AmoCrm\Hydrator;

use Swix\AmoCrm\Entity\CustomField\CustomFieldsFactory;
use Webmozart\Assert\Assert;

abstract class AbstractHydrator implements HydratorInterface
{
    private function recursiveUnset(&$array, $unwantedKey)
    {
        unset($array[$unwantedKey]);
        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->recursiveUnset($value, $unwantedKey);
            }
        }
    }

    public function hydrateRows(array $rows): array
    {
        // It is assumed we do not preload any nested entities
        $this->recursiveUnset($rows, '_links');

        $hydrated = [];
        foreach ($rows as $row) {
            $entity = $this->createEntity();
            $this->hydrate($entity, $row);

            $hydrated[] = $entity;
        }

        return $hydrated;
    }

    public function hydrate($entity, array $data)
    {
        foreach ($data as $name => $value) {
            $this->setEntityValue($entity, $name, $value);
        }
    }

    protected function setEntityValue($entity, string $name, $value)
    {
        switch ($name) {
            case 'custom_fields':
                $value = CustomFieldsFactory::create($value);
                break;
        }

        $setter = $this->getSetterName($name);

        Assert::methodExists($entity, $setter);
        $entity->$setter($value);

        return $this;
    }

    protected function getSetterName($name)
    {
        return 'set'.str_replace('_', '', ucwords($name, '_'));
    }
}
