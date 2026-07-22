<?php

declare(strict_types=1);

namespace EdifactParser\Validation;

/**
 * A single conformance problem found by {@see MessageValidator}: which segment tag,
 * which rule was broken, and a human-readable description.
 */
final class ValidationViolation
{
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
}
