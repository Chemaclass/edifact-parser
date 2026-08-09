<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Console;

use EdifactParser\Console\OutputInterface;

/**
 * Captures the two channels separately, so a test can assert that data never leaks into
 * the human-readable stream and vice versa.
 */
final class RecordingOutput implements OutputInterface
{
    /** @var list<array<string, mixed>> */
    public array $data = [];

    /** @var list<bool> */
    public array $pretty = [];

    /** @var list<string> */
    public array $messages = [];

    public function data(array $data, bool $pretty = false): void
    {
        $this->data[] = $data;
        $this->pretty[] = $pretty;
    }

    public function info(string $message): void
    {
        $this->messages[] = $message;
    }

    public function error(string $message): void
    {
        $this->messages[] = $message;
    }
}
