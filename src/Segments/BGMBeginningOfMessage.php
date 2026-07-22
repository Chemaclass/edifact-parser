<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class BGMBeginningOfMessage extends AbstractSegment
{
    public function tag(): string
    {
        return 'BGM';
    }

    /**
     * Document/message name code (e.g., '220' = Order, '380' = Invoice, '351' = Despatch advice)
     */
    public function documentCode(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * Document/message number (buyer/sender reference for the document)
     */
    public function documentNumber(): string
    {
        return $this->element(2);
    }

    /**
     * Message function code (e.g., '9' = Original, '7' = Duplicate, '3' = Deletion)
     */
    public function messageFunction(): string
    {
        return $this->element(3);
    }
}
