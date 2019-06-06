<?php

namespace Swix\AmoCrm\Entity;

use Swix\AmoCrm\Entity\Traits\BasicEntityTrait;

class Task
{
    use BasicEntityTrait;

    /** @var int */
    private $elementId;

    /** @var int */
    private $elementType; // 1 - контакт, 2- сделка, 3 - компания, 12 - покупатель

    /** @var \DateTime */
    private $completeTillAt;

    /** @var int */
    private $taskType; // 1 Звонок, 2 Встреча, 3 Написать письмо

    /** @var string */
    private $text;

    /** @var bool */
    private $isCompleted;

    /** @var string */
    private $result; // no info in docs, wtf?
}
