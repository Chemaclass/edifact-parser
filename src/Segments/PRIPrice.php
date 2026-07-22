<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use EdifactParser\Segments\Builder\PRIBuilder;

/** @psalm-immutable */
final class PRIPrice extends AbstractSegment
{
    public function tag(): string
    {
        return 'PRI';
    }

    public static function builder(): PRIBuilder
    {
        return new PRIBuilder();
    }

    public function subId(): string
    {
        return $this->requiredSubId();
    }

    /**
     * Price qualifier (e.g., 'AAA' = Calculation net, 'AAB' = Gross)
     */
    public function qualifier(): string
    {
        return $this->component(0);
    }

    public function price(): string
    {
        return $this->component(1);
    }

    public function priceAsFloat(): float
    {
        return (float) $this->price();
    }

    public function priceType(): string
    {
        return $this->component(2);
    }
}
