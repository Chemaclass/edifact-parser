<?php

declare(strict_types=1);

namespace App\EdifactParser;

use App\EdifactParser\Exception\ParsingFileException;
use EDI\Parser;

final class EdifactParser
{
    public static function parse(Parser $parser): TransactionResult
    {
        $errors = $parser->errors();

        if ($errors) {
            throw new ParsingFileException($errors);
        }

        return TransactionResult::fromSegmentedValues(
            SegmentedValues::fromRaw($parser->get())
        );
    }
}
