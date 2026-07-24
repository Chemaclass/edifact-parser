<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class CTAContactInformation extends AbstractSegment
{
    public function tag(): string
    {
        return 'CTA';
    }

    /**
     * Contact function code (3139), e.g. 'IC' = information contact,
     * 'PD' = purchasing dept.
     */
    public function contactFunction(): string
    {
        return $this->element(1);
    }

    /**
     * Department or employee identification (C056/3413):
     * CTA+IC+00001:John Smith -> '00001'.
     */
    public function contactId(): string
    {
        return $this->component(0, 2);
    }

    /**
     * Department or employee name (C056/3412):
     * CTA+IC+00001:John Smith -> 'John Smith'.
     */
    public function contactName(): string
    {
        return $this->component(1, 2);
    }
}
