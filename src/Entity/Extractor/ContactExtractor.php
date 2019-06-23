<?php

namespace Swix\AmoCrm\Entity\Extractor;

use Swix\AmoCrm\Entity\AbstractEntity;
use Swix\AmoCrm\Extractor\AbstractExtractor;

class ContactExtractor extends AbstractExtractor
{
    protected $fields = [
        'name',
        'leads',
        'closest_task_at',
        'customers',
    ];

    public function extract(AbstractEntity $entity): array
    {
        return array_merge(parent::extract($entity), $this->extractByFields($entity, $this->fields));
    }
}
