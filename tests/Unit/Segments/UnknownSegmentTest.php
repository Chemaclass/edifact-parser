<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\UnknownSegment;
use PHPUnit\Framework\TestCase;

final class UnknownSegmentTest extends TestCase
{
    /** @test */
    public function segmentValues(): void
    {
        $rawValues = ['UNKNOWN', 'SEGMENT'];
        $segment = new UnknownSegment($rawValues);

        self::assertEquals(UnknownSegment::class, $segment->tag());
        self::assertEquals(md5(json_encode($rawValues)), $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
