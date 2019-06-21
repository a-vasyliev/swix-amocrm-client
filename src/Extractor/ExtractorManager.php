<?php

namespace Swix\AmoCrm\Extractor;

use Webmozart\Assert\Assert;

class ExtractorManager
{
    protected $extractorEntityClassMap = [
        '\Swix\AmoCrm\Entity\Lead' => '\Swix\AmoCrm\Entity\Extractor\LeadExtractor'
    ];

    protected $extractors = [];

    public function __construct(array $extractorEntityClassMap = null)
    {
        if (is_array($extractorEntityClassMap)) {
            Assert::allClassExists($extractorEntityClassMap);
            Assert::allClassExists(array_keys($extractorEntityClassMap));

            $this->extractorEntityClassMap = $extractorEntityClassMap;
        }
    }

    public function get(string $entityClassName): ExtractorInterface
    {
        if (isset($this->extractors[$entityClassName])) {
            return $this->extractors[$entityClassName];
        }

        Assert::classExists($entityClassName);
        Assert::keyExists($this->extractorEntityClassMap, $entityClassName);

        $extractor = $this->extractors[$entityClassName] = new $this->extractorEntityClassMap[$entityClassName];

        return $extractor;
    }
}
