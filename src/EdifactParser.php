<?php

declare(strict_types=1);

namespace EdifactParser;

use EDI\Parser;
use EdifactParser\Exception\InvalidFile;

final class EdifactParser
{
    public static function parse(Parser $parser): TransactionResult
    {
        $errors = $parser->errors();

        if ($errors) {
            throw InvalidFile::withErrors($errors);
        }

        return TransactionResult::fromSegmentedValues(
            SegmentedValues::fromRaw($parser->get())
        );
    }
}
