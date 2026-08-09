<?php

declare(strict_types=1);

namespace EdifactParser\Validation;

use EdifactParser\Diagnostics\Diagnostic;
use EdifactParser\Diagnostics\DiagnosticCode;
use EdifactParser\Directory\Composite;
use EdifactParser\Directory\DataElement;
use EdifactParser\Directory\DirectoryInterface;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\TransactionMessage;

use function is_array;
use function is_string;
use function sprintf;

/**
 * Checks segments against what a UN/EDIFACT directory actually defines: mandatory elements
 * and composites, representation (`an`/`n`/`a`) and maximum length, and — when asked —
 * whether coded values appear in the directory's code list.
 *
 * Complements {@see MessageValidator}, which checks message-level rules (which segments are
 * required, how often, in what order). This one looks inside each segment.
 *
 * Never throws, and never complains about a tag the directory does not define: unknown and
 * partner-specific segments stay valid, matching the parser's own permissiveness.
 */
final class DirectoryValidator
{
    public function __construct(
        private DirectoryInterface $directory,
        /** Code-list checking is opt-in: plenty of real traffic uses partner-specific codes. */
        private bool $validateCodes = false,
    ) {
    }

    public function withCodeValidation(bool $validateCodes = true): self
    {
        return new self($this->directory, $validateCodes);
    }

    /**
     * @return list<Diagnostic>
     */
    public function validate(TransactionMessage $message): array
    {
        $diagnostics = [];

        foreach ($message as $index => $segment) {
            foreach ($this->validateSegment($segment, $index) as $diagnostic) {
                $diagnostics[] = $diagnostic;
            }
        }

        return $diagnostics;
    }

    public function isValid(TransactionMessage $message): bool
    {
        return $this->validate($message) === [];
    }

    /**
     * @return list<Diagnostic>
     */
    public function validateSegment(SegmentInterface $segment, ?int $index = null): array
    {
        $definition = $this->directory->segment($segment->tag());

        if ($definition === null) {
            return [];
        }

        $diagnostics = [];
        $rawValues = $segment->rawValues();

        foreach ($definition->parts() as $position => $part) {
            // rawValues[0] is the tag, so part 0 lives at rawValues[1].
            $value = $rawValues[$position + 1] ?? null;

            $diagnostics = [...$diagnostics, ...($part instanceof Composite
                ? $this->validateComposite($part, $value, $segment, $index)
                : $this->validateElement($part, self::asString($value), $part->id(), $segment, $index))];
        }

        return $diagnostics;
    }

    /**
     * @return list<Diagnostic>
     */
    private function validateComposite(
        Composite $composite,
        mixed $value,
        SegmentInterface $segment,
        ?int $index,
    ): array {
        $components = is_array($value) ? $value : ($value === null ? [] : [$value]);
        $isEmpty = self::isEmpty($components);

        if ($composite->isRequired() && $isEmpty) {
            return [$this->diagnostic(
                DiagnosticCode::ELEMENT_REQUIRED,
                sprintf('Composite %s is mandatory in %s', $composite->id(), $segment->tag()),
                $segment,
                $index,
                $composite->id(),
            )];
        }

        // An absent optional composite has nothing inside it to check.
        if ($isEmpty) {
            return [];
        }

        $diagnostics = [];

        foreach ($composite->elements() as $position => $element) {
            $diagnostics = [...$diagnostics, ...$this->validateElement(
                $element,
                self::asString($components[$position] ?? null),
                $composite->id() . '/' . $element->id(),
                $segment,
                $index,
            )];
        }

        return $diagnostics;
    }

    /**
     * @return list<Diagnostic>
     */
    private function validateElement(
        DataElement $element,
        string $value,
        string $path,
        SegmentInterface $segment,
        ?int $index,
    ): array {
        if ($value === '') {
            return $element->isRequired()
                ? [$this->diagnostic(
                    DiagnosticCode::ELEMENT_REQUIRED,
                    sprintf('Data element %s is mandatory in %s', $element->id(), $segment->tag()),
                    $segment,
                    $index,
                    $path,
                )]
                : [];
        }

        $diagnostics = [];

        if ($element->exceedsMaxLength($value)) {
            $diagnostics[] = $this->diagnostic(
                DiagnosticCode::ELEMENT_TOO_LONG,
                sprintf(
                    'Data element %s allows at most %d characters, got %d',
                    $element->id(),
                    (int) $element->maxLength(),
                    mb_strlen($value),
                ),
                $segment,
                $index,
                $path,
            );
        } elseif (!$element->matchesType($value)) {
            $diagnostics[] = $this->diagnostic(
                DiagnosticCode::ELEMENT_TYPE,
                sprintf("Data element %s is '%s' but the value is not", $element->id(), $element->type()),
                $segment,
                $index,
                $path,
            );
        }

        if ($this->validateCodes) {
            $codes = $this->directory->codesFor($element->id());

            if ($codes !== [] && !isset($codes[$value])) {
                $diagnostics[] = $this->diagnostic(
                    DiagnosticCode::CODE_UNKNOWN,
                    sprintf("'%s' is not a listed code for data element %s", $value, $element->id()),
                    $segment,
                    $index,
                    $path,
                );
            }
        }

        return $diagnostics;
    }

    private function diagnostic(
        string $code,
        string $message,
        SegmentInterface $segment,
        ?int $index,
        string $path,
    ): Diagnostic {
        return Diagnostic::error($code, $message, $index, $segment->tag(), $path);
    }

    private static function asString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        // A composite where a simple element was expected: the first component is the value.
        if (is_array($value)) {
            return self::asString($value[0] ?? '');
        }

        return $value === null ? '' : (string) $value;
    }

    /**
     * @param array<array-key, mixed> $components
     */
    private static function isEmpty(array $components): bool
    {
        foreach ($components as $component) {
            if (self::asString($component) !== '') {
                return false;
            }
        }

        return true;
    }
}
