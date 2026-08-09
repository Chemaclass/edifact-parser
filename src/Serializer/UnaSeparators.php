<?php

declare(strict_types=1);

namespace EdifactParser\Serializer;

use function strlen;

/**
 * The EDIFACT service-string advice (UNA): the delimiters used to read and write segments.
 * Defaults match the UN/EDIFACT standard (`UNA:+.? '`).
 *
 * The six characters after `UNA` are, in order: component separator, element separator,
 * decimal notation, release character, repetition separator, segment terminator. Position 5
 * is reserved in syntax version 3 (conventionally a space) and carries the **repetition
 * separator** in version 4, default `*`.
 */
final class UnaSeparators
{
    /** `UNA` plus its six characters. */
    public const LENGTH = 9;

    /** Position 5 is reserved in syntax 3 and conventionally written as a space. */
    public const RESERVED = ' ';

    /** The syntax-4 default repetition separator. */
    public const DEFAULT_REPETITION = '*';

    public function __construct(
        private string $component = ':',
        private string $element = '+',
        private string $decimal = '.',
        private string $release = '?',
        private string $segmentTerminator = "'",
        private string $repetition = self::RESERVED,
    ) {
    }

    public static function default(): self
    {
        return new self();
    }

    /**
     * The syntax version 4 defaults, i.e. with the repetition separator in position 5.
     */
    public static function syntax4(): self
    {
        return new self(repetition: self::DEFAULT_REPETITION);
    }

    /**
     * Read a `UNA` service-string advice. Returns null when the string is not one — it must
     * be exactly {@see self::LENGTH} characters starting with `UNA`.
     */
    public static function fromUnaSegment(string $una): ?self
    {
        if (strlen($una) < self::LENGTH || !str_starts_with($una, 'UNA')) {
            return null;
        }

        return new self(
            component: $una[3],
            element: $una[4],
            decimal: $una[5],
            release: $una[6],
            segmentTerminator: $una[8],
            repetition: $una[7],
        );
    }

    public function component(): string
    {
        return $this->component;
    }

    public function element(): string
    {
        return $this->element;
    }

    public function decimal(): string
    {
        return $this->decimal;
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
     * The repetition separator, or {@see self::RESERVED} when the interchange declares
     * none — which is the syntax version 3 case.
     */
    public function repetition(): string
    {
        return $this->repetition;
    }

    /**
     * Whether position 5 carries an actual repetition separator rather than the syntax-3
     * reserved placeholder.
     */
    public function hasRepetitionSeparator(): bool
    {
        return $this->repetition !== self::RESERVED && $this->repetition !== '';
    }

    public function withRepetition(string $repetition): self
    {
        return new self(
            $this->component,
            $this->element,
            $this->decimal,
            $this->release,
            $this->segmentTerminator,
            $repetition,
        );
    }

    /**
     * The UNA segment string that declares these separators.
     */
    public function toUnaSegment(): string
    {
        return 'UNA'
            . $this->component
            . $this->element
            . $this->decimal
            . $this->release
            . ($this->repetition === '' ? self::RESERVED : $this->repetition)
            . $this->segmentTerminator;
    }

    /**
     * Characters that must be prefixed with the release char inside a data value.
     * The release char comes first so escapes added afterwards are not re-escaped.
     *
     * The repetition separator is only special when the interchange actually declares one;
     * escaping a syntax-3 interchange's reserved space would corrupt ordinary data.
     *
     * @return list<string>
     */
    public function specialCharacters(): array
    {
        $special = [$this->release, $this->component, $this->element, $this->segmentTerminator];

        if ($this->hasRepetitionSeparator()) {
            $special[] = $this->repetition;
        }

        return $special;
    }
}
