<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\MEADimensions;
use PHPUnit\Framework\TestCase;

final class MEADimensionsTest extends TestCase
{
    /** @test */
    public function segmentValues(): void
    {
        $rawValues = ['MEA', 'WT', 'G', ['KGM', '0.1']];
        $segment = new MEADimensions($rawValues);

        self::assertEquals(MEADimensions::class, $segment->name());
        self::assertEquals('WT', $segment->subSegmentKey());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
