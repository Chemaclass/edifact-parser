<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Segments;

use EdifactParser\Segments\UNHMessageHeader;
use PHPUnit\Framework\TestCase;

final class UNHMessageHeaderTest extends TestCase
{
    /** @test */
    public function segmentValues(): void
    {
        $rawValues = ['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']];
        $segment = new UNHMessageHeader($rawValues);

        self::assertEquals('UNH', $segment->name());
        self::assertEquals('1', $segment->subSegmentKey());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
