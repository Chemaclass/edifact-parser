<?php

declare(strict_types=1);

namespace EdifactParser\Console;

use function fwrite;
use function json_encode;

final class StreamOutput implements OutputInterface
{
    /**
     * @param resource $stdout
     * @param resource $stderr
     */
    public function __construct(
        private $stdout,
        private $stderr,
    ) {
    }

    public function data(array $data, bool $pretty = false): void
    {
        $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

        if ($pretty) {
            $flags |= JSON_PRETTY_PRINT;
        }

        fwrite($this->stdout, json_encode($data, $flags) . "\n");
    }

    public function info(string $message): void
    {
        fwrite($this->stderr, $message . "\n");
    }

    public function error(string $message): void
    {
        fwrite($this->stderr, $message . "\n");
    }
}
