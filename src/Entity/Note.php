<?php

namespace Swix\AmoCrm\Entity;

use Swix\AmoCrm\Entity\Traits\ElementTrait;
use Webmozart\Assert\Assert;

class Note extends AbstractEntity
{
    use ElementTrait;

    const TYPE_COMMON = 4;
    const TYPE_TASK_RESULT = 13;
    const TYPE_SYSTEM = 25;
    const TYPE_SMS_IN = 102;
    const TYPE_SMS_OUT = 103;

    const NOTE_TYPES = [
        self::TYPE_COMMON => [],
        self::TYPE_TASK_RESULT => [],
        self::TYPE_SYSTEM => ['text', 'service'],
        self::TYPE_SMS_IN => ['text'],
        self::TYPE_SMS_OUT => ['text']
    ];

    /** @var bool */
    private $isEditable;

    /** @var string|null */
    private $text;

    /** @var int */
    private $noteType; // https://www.amocrm.ru/developers/content/api/notes/#note_types

    /** @var string|null */
    private $service;

    /**
     * @return bool
     */
    public function isEditable(): bool
    {
        return $this->isEditable;
    }

    /**
     * @param bool $isEditable
     * @return Note
     */
    public function setIsEditable(bool $isEditable): Note
    {
        $this->isEditable = $isEditable;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     * @return Note
     */
    public function setText(?string $text): Note
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return int
     */
    public function getNoteType(): int
    {
        return $this->noteType;
    }

    /**
     * @param int $noteType
     * @return Note
     */
    public function setNoteType(int $noteType): Note
    {
        Assert::oneOf($noteType, array_keys(self::NOTE_TYPES));
        $this->noteType = $noteType;

        return $this;
    }

    /**
     * @param array $params
     * @return Note
     */
    public function setParams(array $params): Note
    {
        if (isset($params['text'])) {
            $this->setText($params['text']);
        }

        if (isset($params['service'])) {
            $this->setService($params['service']);
        }

        return $this;
    }

    public function getParams()
    {
        $params = [];

        if (in_array($this->getNoteType(), [self::TYPE_SYSTEM, self::TYPE_SMS_IN, self::TYPE_SMS_OUT])) {
            $params['text'] = $this->getText();
        }

        if ($this->getNoteType() == self::TYPE_SYSTEM) {
            $params['service'] = $this->getService();
        }

        return $params;
    }

    /**
     * @return string|null
     */
    public function getService(): ?string
    {
        return $this->service;
    }

    /**
     * @param string|null $service
     * @return Note
     */
    public function setService(?string $service): Note
    {
        $this->service = $service;

        return $this;
    }
}
