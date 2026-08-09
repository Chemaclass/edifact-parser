<?php

declare(strict_types=1);

namespace EdifactParser\Tokenizer;

use EdifactParser\Diagnostics\Diagnostic;
use EdifactParser\Diagnostics\DiagnosticCode;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\Serializer\UnaSeparators;

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
    private const UNA_LENGTH = UnaSeparators::LENGTH;

    /** Whitespace that separates segments, and that EDIFACT allows inside a wrapped one. */
    private const BLANKS = " \t\r\n";

    public function __construct(
        private string $component = ':',
        private string $element = '+',
        private string $release = '?',
        private string $segmentTerminator = "'",
        private string $repetition = UnaSeparators::RESERVED,
    ) {
    }

    public function tokenize(string $content): array
    {
        $separators = new UnaSeparators(
            component: $this->component,
            element: $this->element,
            release: $this->release,
            segmentTerminator: $this->segmentTerminator,
            repetition: $this->repetition,
        );

        $offset = strspn($content, self::BLANKS);

        // A leading UNA overrides the defaults and is not itself a segment. Position 5
        // carries the repetition separator in syntax version 4 and is reserved in 3.
        $declared = UnaSeparators::fromUnaSegment(substr($content, $offset, self::UNA_LENGTH));

        if ($declared !== null) {
            $separators = $declared;
            $offset += self::UNA_LENGTH;
        }

        return $this->scan($content, $offset, $separators);
    }

    /**
     * @return list<array<mixed>>
     */
    private function scan(string $content, int $offset, UnaSeparators $separators): array
    {
        $component = $separators->component();
        $element = $separators->element();
        $release = $separators->release();
        $terminator = $separators->segmentTerminator();
        // Only a declared repetition separator is a delimiter; the syntax-3 reserved
        // space must stay ordinary data.
        $repetition = $separators->hasRepetitionSeparator() ? $separators->repetition() : null;

        $length = strlen($content);
        // CR/LF are stops so they can be dropped rather than copied: a segment wrapped
        // across lines is one segment, and the newline is not part of any value.
        $stops = $release . $component . $element . $terminator . "\r\n" . ($repetition ?? '');

        $segments = [];
        $segment = [];
        $components = [];
        $repeats = [];
        $value = '';
        $isComposite = false;
        $isRepeated = false;

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
                $releasable = $next === $release
                    || $next === $component
                    || $next === $element
                    || $next === $terminator
                    || ($repetition !== null && $next === $repetition);

                if ($releasable) {
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

            if ($repetition !== null && $char === $repetition) {
                $repeats[] = self::closeElement($components, $value, $isComposite);
                $components = [];
                $value = '';
                $isComposite = false;
                $isRepeated = true;
                ++$offset;
                continue;
            }

            if ($char === $element) {
                $segment[] = self::closeValue($components, $repeats, $value, $isComposite, $isRepeated);
                $components = [];
                $repeats = [];
                $value = '';
                $isComposite = false;
                $isRepeated = false;
                ++$offset;
                continue;
            }

            $segment[] = self::closeValue($components, $repeats, rtrim($value, self::BLANKS), $isComposite, $isRepeated);
            $segments[] = $segment;

            $segment = [];
            $components = [];
            $repeats = [];
            $value = '';
            $isComposite = false;
            $isRepeated = false;
            ++$offset;
            $offset += strspn($content, self::BLANKS, $offset);
        }

        if ($segment !== [] || $components !== [] || $repeats !== [] || rtrim($value, self::BLANKS) !== '') {
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
     * Finish an element, folding in its repeats when the interchange declares a repetition
     * separator and this element actually used it.
     *
     * A repeated element becomes a list of its repeats, each of which is a string or a
     * list of components — so `RFF+CU:A*CU:B'` yields `[['CU','A'], ['CU','B']]`. Only
     * syntax 4 interchanges can reach this, so syntax 3 shapes are untouched.
     *
     * @param list<string> $components
     * @param list<string|list<string>> $repeats
     *
     * @return string|list<string>|list<string|list<string>>
     */
    private static function closeValue(
        array $components,
        array $repeats,
        string $value,
        bool $isComposite,
        bool $isRepeated,
    ): string|array {
        $last = self::closeElement($components, $value, $isComposite);

        if (!$isRepeated) {
            return $last;
        }

        $repeats[] = $last;

        return $repeats;
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
