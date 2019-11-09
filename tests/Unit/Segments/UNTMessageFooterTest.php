<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\UNTMessageFooter;
use PHPUnit\Framework\TestCase;

final class UNTMessageFooterTest extends TestCase
{
    /** @test */
    public function segmentValues(): void
    {
        $rawValues = ['UNT', '19', '1'];
        $segment = new UNTMessageFooter($rawValues);

        self::assertEquals('UNT', $segment->name());
        self::assertEquals('19', $segment->subSegmentKey());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
