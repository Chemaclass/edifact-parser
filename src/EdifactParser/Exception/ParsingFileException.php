<?php

declare(strict_types=1);

namespace App\EdifactParser\Exception;

use Exception;
use function json_encode;

final class ParsingFileException extends Exception
{
    public function __construct(array $errors)
    {
        parent::__construct('Errors found while parsing the file: ' . json_encode($errors));
    }
}
