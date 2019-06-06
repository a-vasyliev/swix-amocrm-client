<?php

namespace Swix\AmoCrm\Hydrator;

interface HydratorManagerInterface
{
    public function __construct(array $hydratorEntityClassMap = null);

    public function getHydrator(string $entityClassName): HydratorInterface;
}
