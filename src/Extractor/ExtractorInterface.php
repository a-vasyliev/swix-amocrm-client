<?php

namespace Swix\AmoCrm\Extractor;

use Swix\AmoCrm\Entity\AbstractEntity;

interface ExtractorInterface
{
    public function extractRows(array $rows): array;

    public function extract(AbstractEntity $entity): array;
}
