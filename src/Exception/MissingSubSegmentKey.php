<?php

declare(strict_types=1);

namespace EdifactParser\Exception;

final class MissingSubSegmentKey extends \Exception
{
    public function __construct(string $missingKey, array $rawValues)
    {
        parent::__construct("Key '$missingKey' not found in " . json_encode($rawValues));
    }
}
