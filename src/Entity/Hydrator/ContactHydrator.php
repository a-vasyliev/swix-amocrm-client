<?php

namespace Swix\AmoCrm\Entity\Hydrator;

use Swix\AmoCrm\Entity\Contact;
use Swix\AmoCrm\Hydrator\AbstractHydrator;

class ContactHydrator extends AbstractHydrator
{
    public function createEntity()
    {
        return new Contact();
    }

    public function setEntityValue($entity, string $name, $value)
    {
        /** @var Contact $entity */

        if ($name == 'leads') {
            $entity->setLeads($value['id']);

            return $this;
        }

        return parent::setEntityValue($entity, $name, $value);
    }
}
