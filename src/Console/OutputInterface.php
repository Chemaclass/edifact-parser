<?php

declare(strict_types=1);

namespace EdifactParser\Console;

/**
 * Where the command writes. Split so `data` (stdout, machine-readable) can never be
 * contaminated by `error`/`info` (stderr, human-readable) — and so tests can capture both
 * separately.
 */
interface OutputInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function data(array $data, bool $pretty = false): void;

    public function info(string $message): void;

    public function error(string $message): void;
}
