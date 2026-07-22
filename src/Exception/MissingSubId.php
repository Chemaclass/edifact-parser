<?php

declare(strict_types=1);

namespace EdifactParser\Exception;

use Exception;

final class MissingSubId extends Exception
{
    /**
     * @param array<int, string|array<int, string>> $rawValues
     */
    public function __construct(string $missingId, array $rawValues)
    {
        parent::__construct("SubId '{$missingId}' not found in " . json_encode($rawValues));
    }
}
