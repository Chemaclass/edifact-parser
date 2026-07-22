<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class UNBInterchangeHeader extends AbstractSegment
{
    public function tag(): string
    {
        return 'UNB';
    }

    public function subId(): string
    {
        return $this->requiredSubId();
    }

    /**
     * Syntax identifier / character set (e.g., 'UNOA', 'UNOB', 'UNOC', 'UNOY' = UTF-8)
     */
    public function syntaxIdentifier(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * Syntax version number (e.g., '1', '2', '3', '4')
     */
    public function syntaxVersionNumber(): string
    {
        return $this->component(1);
    }

    /**
     * Interchange sender identification
     */
    public function senderIdentification(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * Interchange recipient identification
     */
    public function recipientIdentification(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * Preparation date (YYMMDD or CCYYMMDD depending on syntax version)
     */
    public function preparationDate(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * Preparation time (HHMM)
     */
    public function preparationTime(): string
    {
        return $this->component(1, 4);
    }

    /**
     * Interchange control reference (sender-assigned, unique per interchange)
     */
    public function interchangeControlReference(): string
    {
        return $this->element(5);
    }
}
