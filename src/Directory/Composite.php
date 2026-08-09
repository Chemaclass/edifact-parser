<?php

declare(strict_types=1);

namespace EdifactParser\Directory;

/**
 * A composite data element: an id, whether the composite itself is mandatory, and the
 * ordered data elements it is made of.
 */
final class Composite
{
    /**
     * @param list<DataElement> $elements
     */
    public function __construct(
        private string $id,
        private string $name,
        private bool $required,
        private array $elements,
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
     * @return list<DataElement>
     */
    public function elements(): array
    {
        return $this->elements;
    }

    /**
     * A copy with one more element appended — the XML reader builds composites
     * incrementally as it walks their children.
     */
    public function withElement(DataElement $element): self
    {
        return new self($this->id, $this->name, $this->required, [...$this->elements, $element]);
    }

    public function elementAt(int $index): ?DataElement
    {
        return $this->elements[$index] ?? null;
    }
}
