<?php

declare(strict_types=1);

namespace EdifactParser\Diagnostics;

use JsonException;
use Stringable;

/**
 * One problem found while parsing or validating, in a form that can be acted on without
 * matching on English prose: a stable {@see DiagnosticCode}, a severity, and whatever
 * location is known.
 *
 * The same type is used by the tokenizers and by {@see \EdifactParser\Validation\MessageValidator},
 * so a caller has one vocabulary for both stages.
 */
final class Diagnostic implements Stringable
{
    public const SEVERITY_ERROR = 'error';

    public const SEVERITY_WARNING = 'warning';

    public function __construct(
        private string $code,
        private string $message,
        private string $severity = self::SEVERITY_ERROR,
        private ?int $segmentIndex = null,
        private ?string $tag = null,
        private ?string $elementPath = null,
    ) {
    }

    /**
     * A one-line rendering: `error [segment.unterminated] at segment 41 (NAD): …`
     */
    public function __toString(): string
    {
        $location = '';

        if ($this->segmentIndex !== null) {
            $location = " at segment {$this->segmentIndex}";
        }

        if ($this->tag !== null) {
            $location .= " ({$this->tag}" . ($this->elementPath !== null ? "/{$this->elementPath}" : '') . ')';
        }

        return "{$this->severity} [{$this->code}]{$location}: {$this->message}";
    }

    public static function error(
        string $code,
        string $message,
        ?int $segmentIndex = null,
        ?string $tag = null,
        ?string $elementPath = null,
    ): self {
        return new self($code, $message, self::SEVERITY_ERROR, $segmentIndex, $tag, $elementPath);
    }

    public static function warning(
        string $code,
        string $message,
        ?int $segmentIndex = null,
        ?string $tag = null,
        ?string $elementPath = null,
    ): self {
        return new self($code, $message, self::SEVERITY_WARNING, $segmentIndex, $tag, $elementPath);
    }

    public function code(): string
    {
        return $this->code;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function severity(): string
    {
        return $this->severity;
    }

    public function isError(): bool
    {
        return $this->severity === self::SEVERITY_ERROR;
    }

    /**
     * Zero-based position of the offending segment in the interchange, when known.
     */
    public function segmentIndex(): ?int
    {
        return $this->segmentIndex;
    }

    public function tag(): ?string
    {
        return $this->tag;
    }

    /**
     * Where inside the segment the problem sits, e.g. `C186/6060`, when known.
     */
    public function elementPath(): ?string
    {
        return $this->elementPath;
    }

    /**
     * @return array{code: string, severity: string, message: string, segmentIndex: int|null, tag: string|null, elementPath: string|null}
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'severity' => $this->severity,
            'message' => $this->message,
            'segmentIndex' => $this->segmentIndex,
            'tag' => $this->tag,
            'elementPath' => $this->elementPath,
        ];
    }

    /**
     * @throws JsonException
     */
    public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT): string
    {
        return json_encode($this->toArray(), $flags);
    }
}
