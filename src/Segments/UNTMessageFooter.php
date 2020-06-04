<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class UNTMessageFooter implements SegmentInterface
{
    private array $rawValues;

    /** @psalm-pure */
    public static function createFromArray(array $rawValues): self
    {
        return new self($rawValues);
    }

    private function __construct(array $rawValues)
    {
        $this->rawValues = $rawValues;
    }

    public function tag(): string
    {
        return self::class;
    }

    public function subId(): string
    {
        return (string) $this->rawValues[1];
    }

    public function rawValues(): array
    {
        return $this->rawValues;
    }
}
