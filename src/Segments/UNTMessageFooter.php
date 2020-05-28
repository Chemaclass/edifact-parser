<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalmphp-immutable */
final class UNTMessageFooter implements SegmentInterface
{
    private array $rawValues;

    public function __construct(array $rawValues)
    {
        $this->rawValues = $rawValues;
    }

    public function name(): string
    {
        return self::class;
    }

    public function subSegmentKey(): string
    {
        return $this->rawValues[1];
    }

    public function rawValues(): array
    {
        return $this->rawValues;
    }
}
