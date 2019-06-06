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
        switch ($name) {
            case 'pipeline': // skip duplicate value, provided by AmoCRM. Use pipeline_id instead
                return $this;
        }

        return parent::setEntityValue($entity, $name, $value);
    }
}
