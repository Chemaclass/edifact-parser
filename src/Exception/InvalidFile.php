<?php

declare(strict_types=1);

namespace EdifactParser\Exception;

use Exception;

use function is_scalar;
use function json_encode;

final class InvalidFile extends Exception
{
    /**
     * @param array<string, mixed> $errors
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
     * @param array<int|string, string> $errors
     */
    public static function withErrors(array $errors): self
    {
        return new self($errors);
    }

    /**
     * @param array<int|string, string> $errors
     * @param array<string, mixed> $context
     */
    public static function withContext(array $errors, array $context): self
    {
        return new self($errors, $context);
    }

    /**
     * @return array<int|string, string>
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
