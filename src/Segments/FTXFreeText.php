<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class FTXFreeText extends AbstractSegment
{
    public function tag(): string
    {
        return 'FTX';
    }

    /**
     * Text subject qualifier (e.g., 'AAI' = General information, 'DEL' = Delivery)
     */
    public function subjectQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * Free-text value (first component of the text literal element)
     */
    public function text(): string
    {
        return $this->firstComponent(4);
    }
}
