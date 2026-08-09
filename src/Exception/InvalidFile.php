<?php

declare(strict_types=1);

namespace EdifactParser\Exception;

use EdifactParser\Diagnostics\Diagnostic;
use EdifactParser\Diagnostics\DiagnosticCode;
use Exception;

use function is_scalar;
use function json_encode;

final class InvalidFile extends Exception
{
    /** @var list<Diagnostic> */
    private array $diagnostics = [];

    /**
     * @param array<int|string, mixed> $errors
     * @param array<string, mixed> $context
     */
    private function __construct(private array $errors, private array $context = [])
    {
        $message = 'Errors found while parsing the file';

        if (!empty($this->context)) {
            $contextStr = $this->formatContext();
            $message .= "\n\nContext:\n{$contextStr}";
        }

        $message .= "\n\nErrors:\n" . json_encode($errors, JSON_PRETTY_PRINT);

        parent::__construct($message);
    }

    /**
     * @param array<int|string, mixed> $errors
     */
    public static function withErrors(array $errors): self
    {
        return new self($errors);
    }

    /**
     * Preferred over {@see self::withErrors()}: the diagnostics keep their codes and
     * positions, and `getErrors()` still returns the plain messages for older callers.
     *
     * @param list<Diagnostic> $diagnostics
     */
    public static function withDiagnostics(array $diagnostics): self
    {
        $exception = new self(array_map(
            static fn (Diagnostic $diagnostic): string => (string) $diagnostic,
            $diagnostics,
        ));
        $exception->diagnostics = $diagnostics;

        return $exception;
    }

    /**
     * Structured view of the same failures. Built from the plain error strings when the
     * exception was raised without diagnostics, so this is never empty on a thrown
     * InvalidFile.
     *
     * @return list<Diagnostic>
     */
    public function getDiagnostics(): array
    {
        if ($this->diagnostics !== []) {
            return $this->diagnostics;
        }

        $diagnostics = [];
        foreach ($this->errors as $error) {
            $diagnostics[] = Diagnostic::error(
                DiagnosticCode::TOKENIZE_FAILED,
                is_scalar($error) ? (string) $error : (string) json_encode($error),
            );
        }

        return $diagnostics;
    }

    /**
     * @param array<int|string, mixed> $errors
     * @param array<string, mixed> $context
     */
    public static function withContext(array $errors, array $context): self
    {
        return new self($errors, $context);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    private function formatContext(): string
    {
        $lines = [];
        foreach ($this->context as $key => $value) {
            $valueStr = is_scalar($value) ? (string) $value : json_encode($value);
            $lines[] = "  {$key}: {$valueStr}";
        }
        return implode("\n", $lines);
    }
}
