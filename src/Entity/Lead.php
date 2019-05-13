<?php

namespace Swix\AmoCrm\Entity;

class Lead
{
    use BasicEntityTrait;

    /** @var string */
    private $name;

    /** @var bool */
    private $isDeleted;

    /** @var int */
    private $mainContactId;

    /** @var array */
    private $company; // id, name -- trait? class?

    /** @var \DateTime */
    private $closedAt;

    /** @var \DateTime */
    private $closestTaskAt;

    /** @var array */
    private $tags; // [id, name] -- trait? class?

    /** @var array */
    private $customFields; // .... -- trait? class?

    /** @var int[] */
    private $contactIds;

    /** @var int */
    private $statusId;

    /** @var int */
    private $sale;

    /** @var int */
    private $pipelineId;
}
