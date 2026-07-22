<?php

declare(strict_types=1);

namespace EdifactParser\Exception;

use Exception;

final class MissingSubId extends Exception
{
    public function __construct(string $missingId, array $rawValues)
    {
        parent::__construct("SubId '{$missingId}' not found in " . json_encode($rawValues));
    }
}
