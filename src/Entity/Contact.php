<?php

namespace Swix\AmoCrm\Entity;

use Swix\AmoCrm\Entity\Traits\CompanyTrait;

class Contact extends AbstractEntity
{
    use CompanyTrait;

    /** @var string */
    protected $name;

    /** @var array */
    protected $leadIds;

    /** @var \DateTime */
    protected $closestTaskAt;

    /** @var array */
    protected $tags; // id, name -- Trait?

    /** @var array */
    protected $customFields; // id, name, values -- Trait?

    /** @var array */
    protected $customers;
}
