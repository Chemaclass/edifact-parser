<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class IMDItemDescription extends AbstractSegment
{
    public function tag(): string
    {
        return 'IMD';
    }

    /**
     * Description format code (e.g., 'A' = Free-form, 'C' = Code, 'F' = Free-form long)
     */
    public function descriptionFormatCode(): string
    {
        return $this->element(1);
    }

    /**
     * Item characteristic code (e.g., '35' = Colour, '38' = Size)
     */
    public function itemCharacteristicCode(): string
    {
        return $this->element(2);
    }

    /**
     * Item description code (first component of the description element)
     */
    public function itemDescriptionCode(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * Free-form item description, when present in the composite
     */
    public function itemDescription(): string
    {
        return $this->component(3, 3);
    }
}
