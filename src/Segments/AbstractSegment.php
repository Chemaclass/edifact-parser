<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use EdifactParser\Exception\MissingSubId;
use JsonException;

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

    /**
     * Convert segment to associative array for debugging
     *
     * @return array<string, mixed>
     *
     * @psalm-suppress ImpureMethodCall
     */
    public function toArray(): array
    {
        return [
            'tag' => $this->tag(),
            'subId' => $this->subId(),
            'rawValues' => $this->rawValues(),
        ];
    }

    /**
     * Convert segment to JSON string
     *
     * @throws JsonException
     */
    public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT): string
    {
        return json_encode($this->toArray(), $flags);
    }

    /**
     * subId taken from the first component of element [1], required by segments
     * whose [1][0] identifies the record (e.g. UNH/UNB/CNT/DTM/CUX/PRI/QTY/RFF).
     */
    protected function requiredSubId(): string
    {
        if (!isset($this->rawValues[1][0])) {
            throw new MissingSubId('[1][0]', $this->rawValues);
        }

        return (string) $this->rawValues[1][0];
    }

    /**
     * Read component $index of composite element $group, '' when absent.
     */
    protected function component(int $index, int $group = 1): string
    {
        $value = $this->rawValues[$group] ?? [];

        return is_array($value) ? (string) ($value[$index] ?? '') : '';
    }
}
