<?php

namespace Swix\AmoCrm\Entity\Traits;

use Swix\AmoCrm\Entity\CustomField\CustomFieldsFactory;
use Webmozart\Assert\Assert;

trait CustomFieldsTrait
{
    /** @var array */
    private $customFields;

    public function setCustomFields(array $customFields)
    {
        foreach ($customFields as $field) {
            Assert::isInstanceOf($field, '\Swix\AmoCrm\Entity\CustomField\CustomField');
        }

        $this->customFields = $customFields;
    }

    public function getCustomFields()
    {
        return $this->customFields;
    }
}
