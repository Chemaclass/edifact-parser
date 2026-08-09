<?php

declare(strict_types=1);

namespace EdifactParser\Validation;

use EdifactParser\Diagnostics\Diagnostic;
use EdifactParser\Diagnostics\DiagnosticCode;

/**
 * A single conformance problem found by {@see MessageValidator}: which segment tag,
 * which rule was broken, and a human-readable description.
 *
 * {@see self::toDiagnostic()} converts it to the shared {@see Diagnostic} type, so parse
 * failures and validation failures can be handled with one vocabulary.
 */
final class ValidationViolation
{
    /** Maps the rule names this class has always used onto stable diagnostic codes. */
    private const CODES = [
        'required' => DiagnosticCode::SEGMENT_REQUIRED,
        'cardinality' => DiagnosticCode::SEGMENT_CARDINALITY,
        'sequence' => DiagnosticCode::SEGMENT_SEQUENCE,
    ];

    public function __construct(
        private string $segmentTag,
        private string $rule,
        private string $message,
    ) {
    }

    public function segmentTag(): string
    {
        return $this->segmentTag;
    }

    public function rule(): string
    {
        return $this->rule;
    }

    public function message(): string
    {
        return $this->message;
    }

    /**
     * The stable code for this violation's rule, for callers that would otherwise match
     * on `rule()` strings.
     */
    public function code(): string
    {
        return self::CODES[$this->rule] ?? $this->rule;
    }

    public function toDiagnostic(): Diagnostic
    {
        return Diagnostic::error($this->code(), $this->message, tag: $this->segmentTag);
    }

    /**
     * @return array{code: string, severity: string, message: string, segmentIndex: int|null, tag: string|null, elementPath: string|null}
     */
    public function toArray(): array
    {
        return $this->toDiagnostic()->toArray();
    }
}
