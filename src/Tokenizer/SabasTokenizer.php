<?php

declare(strict_types=1);

namespace EdifactParser\Tokenizer;

use EDI\Parser;
use EdifactParser\Exception\InvalidFile;

/**
 * Tokenizes with `sabas/edifact`. This was the default up to 6.x; {@see NativeTokenizer}
 * is now the default because this one **discards data**: it strips every byte in
 * `\x80-\xFF` from each segment, so `NAD+BY+++Müller` yields `Mller`. Use it when you
 * need bug-for-bug compatibility with earlier releases, or the restricted `UNOB`
 * character repertoire enforced.
 */
final class SabasTokenizer implements TokenizerInterface
{
    public function tokenize(string $content): array
    {
        $parser = (new Parser())->loadString($content);

        // loadString() only unwraps; the per-segment work — and therefore most of the
        // errors — happens in get(). Reading errors() before this point (as the parser
        // did up to 6.x) meant almost nothing was ever reported.
        $segments = $parser->get();
        $errors = $parser->errors();

        if ($errors !== []) {
            throw InvalidFile::withErrors($errors);
        }

        /** @var list<array<mixed>> */
        return $segments;
    }
}
