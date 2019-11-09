<?php

declare(strict_types=1);

namespace EdifactParser;

use EDI\Parser;
use EdifactParser\Exception\InvalidFile;

final class EdifactParser
{
    public static function parse(string $fileContent): TransactionResult
    {
        $parser = new Parser($fileContent);
        $errors = $parser->errors();

        if ($errors) {
            throw InvalidFile::withErrors($errors);
        }

        return TransactionResult::fromSegmentedValues(
            SegmentedValues::fromRaw($parser->get())
        );
    }
}
