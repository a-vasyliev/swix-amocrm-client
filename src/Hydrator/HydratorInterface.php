<?php

namespace Swix\AmoCrm\Hydrator;

interface HydratorInterface
{
    public function createEntity();

    public function hydrateRows(array $rows): array;

    public function hydrate($entity, array $data);
}
