<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * simpleDataElementDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class ELMSimpleDataElementDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'ELM';
    }

    /**
     * 9150 (an..4)
     */
    public function simpleDataElementTag(): string
    {
        return $this->element(1);
    }

    /**
     * 9153 (an..3)
     */
    public function simpleDataElementCharacterRepresentationCoded(): string
    {
        return $this->element(2);
    }

    /**
     * 9155 (an..3)
     */
    public function simpleDataElementLengthTypeCoded(): string
    {
        return $this->element(3);
    }

    /**
     * 9156 (n..3)
     */
    public function simpleDataElementMaximumLength(): string
    {
        return $this->element(4);
    }

    /**
     * 9158 (n..3)
     */
    public function simpleDataElementMinimumLength(): string
    {
        return $this->element(5);
    }

    /**
     * 9161 (an..3)
     */
    public function codeSetIndicatorCoded(): string
    {
        return $this->element(6);
    }

    /**
     * 1507 (an..3)
     */
    public function classDesignatorCoded(): string
    {
        return $this->element(7);
    }

    /**
     * 4513 (an..3)
     */
    public function maintenanceOperationCoded(): string
    {
        return $this->element(8);
    }
}
