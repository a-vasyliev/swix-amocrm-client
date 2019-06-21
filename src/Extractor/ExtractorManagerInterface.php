<?php

namespace Swix\AmoCrm\Extractor;

interface ExtractorManagerInterface
{
    public function __construct(array $hydratorEntityClassMap = null);

    public function getHydrator(string $entityClassName): ExtractorInterface;
}
