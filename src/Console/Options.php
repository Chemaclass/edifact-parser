<?php

declare(strict_types=1);

namespace EdifactParser\Console;

use function in_array;
use function is_file;
use function str_starts_with;
use function substr;

/**
 * Command-line arguments, parsed without a console framework: `--flag`, `--key=value`,
 * and at most one positional path.
 */
final class Options
{
    /**
     * @param list<string> $positional
     * @param array<string, string> $values
     * @param list<string> $flags
     * @param resource|null $stdin
     */
    private function __construct(
        private array $positional,
        private array $values,
        private array $flags,
        private $stdin,
    ) {
    }

    /**
     * @param list<string> $arguments
     * @param resource|null $stdin The stream to fall back to when no path is given; null
     *                             means there is none. Passed in rather than reaching for
     *                             the STDIN constant, so the piped path stays testable.
     */
    public static function fromArguments(array $arguments, $stdin = null): self
    {
        $positional = [];
        $values = [];
        $flags = [];

        foreach ($arguments as $argument) {
            if (!str_starts_with($argument, '--')) {
                $positional[] = $argument;
                continue;
            }

            $argument = substr($argument, 2);
            $separator = strpos($argument, '=');

            if ($separator === false) {
                $flags[] = $argument;
                continue;
            }

            $values[substr($argument, 0, $separator)] = substr($argument, $separator + 1);
        }

        return new self($positional, $values, $flags, $stdin);
    }

    public function value(string $name): ?string
    {
        return $this->values[$name] ?? null;
    }

    public function has(string $flag): bool
    {
        return in_array($flag, $this->flags, true);
    }

    public function pretty(): bool
    {
        return $this->has('pretty');
    }

    public function path(): ?string
    {
        return $this->pathAt(0);
    }

    /**
     * The nth positional path, for commands taking more than one — `diff a.edi b.edi`.
     */
    public function pathAt(int $index): ?string
    {
        return $this->positional[$index] ?? null;
    }

    /**
     * Read a specific positional file. Null when it is absent or unreadable, which the
     * caller reports as a usage error rather than an empty interchange.
     */
    public function readPath(int $index): ?string
    {
        $path = $this->pathAt($index);

        if ($path === null || !is_file($path)) {
            return null;
        }

        $content = @file_get_contents($path);

        return $content === false ? null : $content;
    }

    /**
     * The interchange to work on: the positional file if given, otherwise stdin. Null when
     * neither yields anything, which is a usage error rather than an empty interchange.
     */
    public function readInput(): ?string
    {
        $path = $this->path();

        if ($path !== null) {
            if (!is_file($path)) {
                return null;
            }

            $content = @file_get_contents($path);

            return $content === false ? null : $content;
        }

        // Only consume stdin when something is actually piped in, so an interactive
        // `edifact parse` prints usage instead of hanging on a tty.
        if ($this->stdin === null || stream_isatty($this->stdin)) {
            return null;
        }

        $content = stream_get_contents($this->stdin);

        return $content === false || trim($content) === '' ? null : $content;
    }
}
