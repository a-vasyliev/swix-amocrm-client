<?php

namespace Swix\AmoCrm\Entity\Extractor;

use Swix\AmoCrm\Entity\AbstractEntity;
use Swix\AmoCrm\Extractor\AbstractExtractor;

class NoteExtractor extends AbstractExtractor
{
    protected $fields = [
        'is_editable',
        'text',
        'note_type',
        'params',
    ];

    public function extract(AbstractEntity $entity): array
    {
        return array_merge(parent::extract($entity), $this->extractByFields($entity, $this->fields));
    }
}
