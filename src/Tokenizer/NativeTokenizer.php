<?php

declare(strict_types=1);

namespace EdifactParser\Tokenizer;

use EdifactParser\Diagnostics\Diagnostic;
use EdifactParser\Diagnostics\DiagnosticCode;
use EdifactParser\Exception\InvalidFile;

use function count;
use function is_string;
use function rtrim;
use function strcspn;
use function strlen;
use function strspn;
use function substr;

/**
 * A regex-free tokenizer: one pass over the input, copying runs of ordinary data with
 * strcspn/substr instead of splitting with lookbehind patterns. Roughly 3x faster than
 * {@see SabasTokenizer} on large interchanges, and it never rewrites the bytes it reads —
 * so non-ASCII data survives, where the default silently strips it.
 *
 * Opt in explicitly:
 *
 *     new EdifactParser(SegmentFactory::withDefaultSegments(), tokenizer: new NativeTokenizer());
 */
final class NativeTokenizer implements TokenizerInterface
{
    private const UNA_LENGTH = 9;

    /** Whitespace that separates segments, and that EDIFACT allows inside a wrapped one. */
    private const BLANKS = " \t\r\n";

    public function __construct(
        private string $component = ':',
        private string $element = '+',
        private string $release = '?',
        private string $segmentTerminator = "'",
    ) {
    }

    public function tokenize(string $content): array
    {
        $component = $this->component;
        $element = $this->element;
        $release = $this->release;
        $terminator = $this->segmentTerminator;

        $offset = strspn($content, self::BLANKS);

        // A leading UNA overrides the defaults and is not itself a segment.
        if (substr($content, $offset, 3) === 'UNA' && strlen($content) >= $offset + self::UNA_LENGTH) {
            $una = substr($content, $offset, self::UNA_LENGTH);
            $component = $una[3];
            $element = $una[4];
            $release = $una[6];
            $terminator = $una[8];
            $offset += self::UNA_LENGTH;
        }

        return $this->scan($content, $offset, $component, $element, $release, $terminator);
    }

    /**
     * @return list<array<mixed>>
     */
    private function scan(
        string $content,
        int $offset,
        string $component,
        string $element,
        string $release,
        string $terminator,
    ): array {
        $length = strlen($content);
        // CR/LF are stops so they can be dropped rather than copied: a segment wrapped
        // across lines is one segment, and the newline is not part of any value.
        $stops = $release . $component . $element . $terminator . "\r\n";

        $segments = [];
        $segment = [];
        $components = [];
        $value = '';
        $isComposite = false;

        $offset += strspn($content, self::BLANKS, $offset);

        while ($offset < $length) {
            $run = strcspn($content, $stops, $offset);

            if ($run !== 0) {
                $value .= substr($content, $offset, $run);
                $offset += $run;

                if ($offset >= $length) {
                    break;
                }
            }

            $char = $content[$offset];

            if ($char === "\r" || $char === "\n") {
                ++$offset;
                continue;
            }

            if ($char === $release) {
                $next = $offset + 1 < $length ? $content[$offset + 1] : '';

                // A release only releases a delimiter or itself. Before anything else it
                // is malformed, and both characters stay as data rather than the release
                // silently swallowing the one after it.
                if ($next === $release || $next === $component || $next === $element || $next === $terminator) {
                    $value .= $next;
                    $offset += 2;
                    continue;
                }

                $value .= $char;
                ++$offset;
                continue;
            }

            if ($char === $component) {
                $components[] = $value;
                $value = '';
                $isComposite = true;
                ++$offset;
                continue;
            }

            if ($char === $element) {
                $segment[] = self::closeElement($components, $value, $isComposite);
                $components = [];
                $value = '';
                $isComposite = false;
                ++$offset;
                continue;
            }

            $segment[] = self::closeElement($components, rtrim($value, self::BLANKS), $isComposite);
            $segments[] = $segment;

            $segment = [];
            $components = [];
            $value = '';
            $isComposite = false;
            ++$offset;
            $offset += strspn($content, self::BLANKS, $offset);
        }

        if ($segment !== [] || $components !== [] || rtrim($value, self::BLANKS) !== '') {
            $tag = $segment[0] ?? null;

            throw InvalidFile::withDiagnostics([
                Diagnostic::error(
                    DiagnosticCode::SEGMENT_UNTERMINATED,
                    'This file contains some segments without terminators',
                    count($segments),
                    is_string($tag) ? $tag : null,
                ),
            ]);
        }

        return $segments;
    }

    /**
     * An element with no component separator stays a plain string — only a genuine
     * composite becomes a list, which is what the segment accessors expect.
     *
     * @param list<string> $components
     *
     * @return string|list<string>
     */
    private static function closeElement(array $components, string $value, bool $isComposite): string|array
    {
        if (!$isComposite) {
            return $value;
        }

        $components[] = $value;

        return $components;
    }
}
