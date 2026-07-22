<?php

declare(strict_types=1);

namespace EdifactParser\Serializer;

/**
 * The EDIFACT service-string advice (UNA): the delimiters used to serialize
 * segments. Defaults match the UN/EDIFACT standard (`UNA:+.? '`).
 */
final class UnaSeparators
{
    public function __construct(
        private string $component = ':',
        private string $element = '+',
        private string $decimal = '.',
        private string $release = '?',
        private string $segmentTerminator = "'",
    ) {
    }

    public static function default(): self
    {
        return new self();
    }

    public function component(): string
    {
        return $this->component;
    }

    public function element(): string
    {
        return $this->element;
    }

    public function release(): string
    {
        return $this->release;
    }

    public function segmentTerminator(): string
    {
        return $this->segmentTerminator;
    }

    /**
     * The UNA segment string that declares these separators.
     */
    public function toUnaSegment(): string
    {
        return 'UNA' . $this->component . $this->element . $this->decimal . $this->release . ' ' . $this->segmentTerminator;
    }

    /**
     * Characters that must be prefixed with the release char inside a data value.
     * The release char comes first so escapes added afterwards are not re-escaped.
     *
     * @return list<string>
     */
    public function specialCharacters(): array
    {
        return [$this->release, $this->component, $this->element, $this->segmentTerminator];
    }
}
