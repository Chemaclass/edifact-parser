<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\MEADimensions;
use PHPUnit\Framework\TestCase;

final class MEADimensionsTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['MEA', 'WT', 'G', ['KGM', '0.1']];
        $segment = new MEADimensions($rawValues);

        self::assertEquals('MEA', $segment->tag());
        self::assertEquals('WT', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
