<?php

namespace Swix\AmoCrm\Entity\Traits;

use Webmozart\Assert\Assert;

trait TagsTrait
{
    /**
     * @todo Currently we drop-off IDs. Are they really needed?
     * @var array
     */
    private $tags = [];

    /**
     * @param mixed $tags String with coma-delimited values or an array of rows with 'id' and 'name' keys.
     * @return $this
     */
    public function setTags($tags)
    {
        if (is_string($tags)) {
            $tags = array_map('trim', explode(', ', $tags));
        } else {
            Assert::isArray($tags);
            $tags = array_map(function ($value) {
                Assert::keyExists($value, 'name');

                return $value['name'];
            }, $tags);
        }

        $this->tags = $tags;

        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }
}