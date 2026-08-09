<?php

declare(strict_types=1);

namespace EdifactParser\Tokenizer;

use EDI\Parser;
use EdifactParser\Exception\InvalidFile;

/**
 * The default tokenizer, delegating to `sabas/edifact`. This is the reference behaviour
 * the library has always had; {@see NativeTokenizer} is the faster alternative.
 */
final class SabasTokenizer implements TokenizerInterface
{
    public function tokenize(string $content): array
    {
        $parser = (new Parser())->loadString($content);
        $errors = $parser->errors();

        if ($errors) {
            throw InvalidFile::withErrors($errors);
        }

        /** @var list<array<mixed>> */
        return $parser->get();
    }
}
