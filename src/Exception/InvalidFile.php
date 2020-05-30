<?php

declare(strict_types=1);

namespace EdifactParser\Exception;

use Exception;
use function json_encode;

/** @psalm-immutable */
final class InvalidFile extends Exception
{
    public static function withErrors(array $errors): self
    {
        return new self($errors);
    }

    private function __construct(array $errors)
    {
        parent::__construct('Errors found while parsing the file: ' . json_encode($errors));
    }
}
