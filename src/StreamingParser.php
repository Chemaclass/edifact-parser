<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Exception\InvalidFile;

use function fclose;
use function feof;
use function fopen;
use function fread;
use function implode;
use function strlen;
use function strtoupper;
use function substr;
use function trim;

/**
 * Parses an EDIFACT file incrementally, yielding one {@see TransactionMessage} at a
 * time. Only a single message is held in memory at once, so arbitrarily large
 * interchanges parse in bounded memory — unlike {@see EdifactParser} which builds
 * the whole result up front.
 *
 * Assumes the default segment terminator (`'`) and release char (`?`).
 */
final class StreamingParser
{
    private const CHUNK_BYTES = 8192;

    public function __construct(
        private EdifactParser $parser,
        private string $segmentTerminator = "'",
        private string $releaseCharacter = '?',
    ) {
    }

    public static function createWithDefaultSegments(): self
    {
        return new self(EdifactParser::createWithDefaultSegments());
    }

    /**
     * @return iterable<TransactionMessage>
     */
    public function parseFile(string $filePath): iterable
    {
        $handle = @fopen($filePath, 'rb');

        if ($handle === false) {
            throw InvalidFile::withErrors(["Unable to read file: {$filePath}"]);
        }

        try {
            yield from $this->streamMessages($handle);
        } finally {
            fclose($handle);
        }
    }

    /**
     * @param resource $handle
     *
     * @return iterable<TransactionMessage>
     */
    private function streamMessages($handle): iterable
    {
        $buffer = '';
        $message = [];
        $inMessage = false;

        while (!feof($handle)) {
            $chunk = fread($handle, self::CHUNK_BYTES);
            if ($chunk === false) {
                break;
            }

            $buffer .= $chunk;

            foreach ($this->extractSegments($buffer) as $segment) {
                $tag = strtoupper(substr($segment, 0, 3));

                if ($tag === 'UNH') {
                    $message = [$segment];
                    $inMessage = true;
                    continue;
                }

                if ($inMessage) {
                    $message[] = $segment;
                }

                if ($tag === 'UNT') {
                    $text = implode($this->segmentTerminator, $message) . $this->segmentTerminator;
                    yield from $this->parser->parse($text)->transactionMessages();

                    $message = [];
                    $inMessage = false;
                }
            }
        }
    }

    /**
     * Splits complete segments out of the buffer, leaving the trailing partial
     * segment (and its escape state) in place for the next read.
     *
     * @return list<string>
     */
    private function extractSegments(string &$buffer): array
    {
        $segments = [];
        $current = '';
        $escaped = false;
        $length = strlen($buffer);

        for ($i = 0; $i < $length; ++$i) {
            $char = $buffer[$i];

            if ($escaped) {
                $current .= $char;
                $escaped = false;
                continue;
            }

            if ($char === $this->releaseCharacter) {
                $current .= $char;
                $escaped = true;
                continue;
            }

            if ($char === $this->segmentTerminator) {
                $trimmed = trim($current);
                if ($trimmed !== '') {
                    $segments[] = $trimmed;
                }
                $current = '';
                continue;
            }

            $current .= $char;
        }

        $buffer = $current;

        return $segments;
    }
}
