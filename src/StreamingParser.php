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
 * A leading `UNA` service-string advice is honoured — custom separators and
 * release char declared there are used for splitting. Otherwise the constructor
 * defaults (`'` terminator, `?` release) apply.
 */
final class StreamingParser
{
    private const CHUNK_BYTES = 8192;
    private const UNA_LENGTH = 9;

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
        $terminator = $this->segmentTerminator;
        $release = $this->releaseCharacter;
        $unaPrefix = '';
        $unaChecked = false;

        while (!feof($handle)) {
            $chunk = fread($handle, self::CHUNK_BYTES);
            // @codeCoverageIgnoreStart
            if ($chunk === false) {
                break;
            }
            // @codeCoverageIgnoreEnd

            $buffer .= $chunk;

            if (!$unaChecked && strlen($buffer) >= self::UNA_LENGTH) {
                $unaChecked = true;
                $una = $this->detectUna($buffer);
                if ($una !== null) {
                    [$terminator, $release, $buffer, $unaPrefix] = $una;
                }
            }

            foreach ($this->extractSegments($buffer, $terminator, $release) as $segment) {
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
                    $text = $unaPrefix . implode($terminator, $message) . $terminator;
                    yield from $this->parser->parse($text)->transactionMessages();

                    $message = [];
                    $inMessage = false;
                }
            }
        }
    }

    /**
     * Detects a leading UNA service-string advice and returns
     * [terminator, release, buffer-without-una, una-string], or null when there is no UNA.
     * The UNA string is prepended to each message so the batch parser reuses the
     * declared delimiters.
     *
     * @return array{0: string, 1: string, 2: string, 3: string}|null
     */
    private function detectUna(string $buffer): ?array
    {
        $offset = strspn($buffer, " \t\r\n");

        if (substr($buffer, $offset, 3) !== 'UNA' || strlen($buffer) < $offset + self::UNA_LENGTH) {
            return null;
        }

        $una = substr($buffer, $offset, self::UNA_LENGTH);

        return [$una[8], $una[6], substr($buffer, $offset + self::UNA_LENGTH), $una];
    }

    /**
     * Splits complete segments out of the buffer, leaving the trailing partial
     * segment (and its escape state) in place for the next read.
     *
     * @return list<string>
     */
    private function extractSegments(string &$buffer, string $terminator, string $release): array
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

            if ($char === $release) {
                $current .= $char;
                $escaped = true;
                continue;
            }

            if ($char === $terminator) {
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
