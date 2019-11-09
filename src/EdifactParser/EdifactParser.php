<?php

declare(strict_types=1);

namespace App\EdifactParser;

use App\EdifactParser\Exception\InvalidFile;
use EDI\Parser;

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
