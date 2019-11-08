<?php

declare(strict_types=1);

namespace App\EdifactParser\Segments;

namespace App\EdifactParser\Segments;

final class CNTControl implements SegmentInterface
{
    public const NAME = 'CNT';

    /** @var array */
    private $rawValues;

    public function __construct(array $rawValues)
    {
        $this->rawValues = $rawValues;
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function subSegmentKey(): string
    {
        return $this->rawValues[1][0];
    }

    public function rawValues(): array
    {
        return $this->rawValues;
    }
}
