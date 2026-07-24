<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class PATPaymentTerms extends AbstractSegment
{
    public function tag(): string
    {
        return 'PAT';
    }

    /**
     * Payment terms type qualifier (4279), e.g. '1' = basic,
     * '3' = discount.
     */
    public function typeQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * Payment terms identification (C110/4277): PAT+1+3 -> '3'.
     */
    public function termsId(): string
    {
        return $this->firstComponent(2);
    }
}
