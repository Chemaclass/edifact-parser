<?php

declare(strict_types=1);

namespace EdifactParser\Tokenizer;

use EdifactParser\Exception\InvalidFile;

/**
 * Turns raw EDIFACT text into the raw segment arrays the rest of the library builds on:
 * element 0 is the tag, every other element is either a string (simple) or a list of
 * strings (composite). Delimiters come from a leading `UNA`, or the EDIFACT defaults.
 *
 * Swapping the implementation is how you trade the reference behaviour for speed —
 * see {@see NativeTokenizer}.
 */
interface TokenizerInterface
{
    /**
     * @throws InvalidFile when the content cannot be tokenized
     *
     * @return list<array<mixed>>
     */
    public function tokenize(string $content): array;
}
