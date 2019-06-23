<?php

namespace Swix\AmoCrm\Entity\Hydrator;

use Swix\AmoCrm\Entity\Note;
use Swix\AmoCrm\Hydrator\AbstractHydrator;

class NoteHydrator extends AbstractHydrator
{
    public function createEntity()
    {
        return new Note();
    }
}
