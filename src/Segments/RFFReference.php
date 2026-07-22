<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class RFFReference extends AbstractSegment
{
    public function tag(): string
    {
        return 'RFF';
    }

    public function subId(): string
    {
        return $this->requiredSubId();
    }

    /**
     * Reference qualifier (e.g., 'ON' = Order number, 'IV' = Invoice number, 'CU' = Customs reference)
     */
    public function qualifier(): string
    {
        return $this->component(0);
    }

    public function referenceNumber(): string
    {
        return $this->component(1);
    }
}
