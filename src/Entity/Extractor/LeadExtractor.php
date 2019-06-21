<?php

namespace Swix\AmoCrm\Entity\Extractor;

use Swix\AmoCrm\Entity\AbstractEntity;
use Swix\AmoCrm\Extractor\AbstractExtractor;

class LeadExtractor extends AbstractExtractor
{
    protected $fields = [
        'name',
        'isDeleted',
        'main_contact',
        'closed_at',
        'closest_task_at',
        'contacts',
        'status_id',
        'sale',
        'pipeline_id',
        'loss_reason_id',
    ];

    public function extract(AbstractEntity $entity): array
    {
        return array_merge(parent::extract($entity), $this->extractByFields($entity, $this->fields));
    }
}
