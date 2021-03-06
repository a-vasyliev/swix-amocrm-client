<?php

namespace Swix\AmoCrm\Entity\Hydrator;

use Swix\AmoCrm\Entity\Lead;
use Swix\AmoCrm\Hydrator\AbstractHydrator;

class LeadHydrator extends AbstractHydrator
{
    public function createEntity()
    {
        return new Lead();
    }

    public function setEntityValue($entity, string $name, $value)
    {
        /** @var Lead $entity */

        if ($name == 'pipeline') {
            return $this;
        }

        if ($name == 'contacts') {
            $entity->setContacts($value['id']);

            return $this;
        }

        return parent::setEntityValue($entity, $name, $value);
    }
}
