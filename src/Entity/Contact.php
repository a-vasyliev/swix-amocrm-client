<?php

namespace Swix\AmoCrm\Entity;

class Contact
{
    use BasicEntityTrait;

    /** @var string */
    protected $name;

    /** @var array */
    protected $company; // id, name -- Trait?

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
