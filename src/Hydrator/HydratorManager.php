<?php

namespace Swix\AmoCrm\Hydrator;

use Webmozart\Assert\Assert;

class HydratorManager
{
    protected $hydratorEntityClassMap = [
        '\Swix\AmoCrm\Entity\Lead' => '\Swix\AmoCrm\Entity\Hydrator\LeadHydrator',
        '\Swix\AmoCrm\Entity\Contact' => '\Swix\AmoCrm\Entity\Hydrator\ContactHydrator'
    ];

    protected $hydrators = [];

    public function __construct(array $hydratorEntityClassMap = null)
    {
        if (is_array($hydratorEntityClassMap)) {
            Assert::allClassExists($hydratorEntityClassMap);
            Assert::allClassExists(array_keys($hydratorEntityClassMap));

            $this->hydratorEntityClassMap = $hydratorEntityClassMap;
        }
    }

    public function get(string $entityClassName): HydratorInterface
    {
        if (isset($this->hydrators[$entityClassName])) {
            return $this->hydrators[$entityClassName];
        }

        Assert::classExists($entityClassName);
        Assert::keyExists($this->hydratorEntityClassMap, $entityClassName);

        $hydrator = $this->hydrators[$entityClassName] = new $this->hydratorEntityClassMap[$entityClassName];

        return $hydrator;
    }
}
