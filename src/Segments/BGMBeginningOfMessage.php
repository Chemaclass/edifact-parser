<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class BGMBeginningOfMessage implements SegmentInterface
{
    private array $rawValues;

    public function __construct(array $rawValues)
    {
        $this->rawValues = $rawValues;
    }

    public function tag(): string
    {
        return 'BGM';
    }

    public function subId(): string
    {
        return (string) $this->rawValues[1];
    }

    /**
     * @return list<string>
     */
    public function parsedSubId(): array
    {
        $value = $this->rawValues[1];

        if (is_array($value)) {
            return $value;
        }

        return explode(':', (string) $value);
    }

    public function rawValues(): array
    {
        return $this->rawValues;
    }
}
