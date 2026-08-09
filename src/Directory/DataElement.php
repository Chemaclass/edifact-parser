<?php

declare(strict_types=1);

namespace EdifactParser\Directory;

/**
 * One data element as the UNTDID directory defines it: its id, whether it is mandatory in
 * its position, and the representation it must fit (`an..35` and friends).
 */
final class DataElement
{
    public function __construct(
        private string $id,
        private string $name,
        private bool $required,
        private string $type,
        private ?int $maxLength,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * `an` alphanumeric, `n` numeric, `a` alphabetic.
     */
    public function type(): string
    {
        return $this->type;
    }

    public function maxLength(): ?int
    {
        return $this->maxLength;
    }

    /**
     * Whether a value matches this element's representation. Length is a separate rule —
     * {@see self::exceedsMaxLength()} — so the two produce distinct diagnostics.
     *
     * An empty value always matches: presence is yet another rule.
     */
    public function matchesType(string $value): bool
    {
        if ($value === '') {
            return true;
        }

        return match ($this->type) {
            // Numeric values may carry a sign and a decimal comma or point.
            'n' => (bool) preg_match('/^-?\d*[.,]?\d+$/', $value),
            'a' => (bool) preg_match('/^[^\d]+$/u', $value),
            default => true,
        };
    }

    public function exceedsMaxLength(string $value): bool
    {
        return $this->maxLength !== null && mb_strlen($value) > $this->maxLength;
    }
}
