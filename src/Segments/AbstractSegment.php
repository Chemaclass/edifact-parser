<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use function is_array;

/** @psalm-immutable */
abstract class AbstractSegment implements SegmentInterface
{
    public function __construct(
        protected array $rawValues = [],
    ) {
    }

    public function rawValues(): array
    {
        return $this->rawValues;
    }

    public function subId(): string
    {
        $value = $this->rawValues[1] ?? '';

        return is_array($value)
            ? implode(':', $value)
            : (string) $value;
    }

    /**
     * @return list<string>
     */
    public function parsedSubId(): array
    {
        $value = $this->rawValues[1] ?? '';

        return is_array($value)
            ? $value
            : explode(':', (string) $value);
    }
}
