<?php

namespace Swix\AmoCrm\Entity;

class Note
{
    use BasicEntityTrait;

    /** @var bool */
    private $isEditable;

    /** @var int */
    private $elementId; // Trait.

    /** @var int */
    private $elementType; // 1 - контакт, 2 - сделка, 3 - компания, 12 - покупатель

    /** @var string */
    private $text;

    /** @var int */
    private $noteType; // https://www.amocrm.ru/developers/content/api/notes/#note_types

    /** @var array */
    private $params; // text, service:  https://www.amocrm.ru/developers/content/api/notes/#params
}
