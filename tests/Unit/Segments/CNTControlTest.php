<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Exception\MissingSubId;
use EdifactParser\Segments\CNTControl;
use PHPUnit\Framework\TestCase;

final class CNTControlTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['CNT', ['7', '0.1', 'KGM']];
        $segment = new CNTControl($rawValues);

        self::assertEquals(CNTControl::class, $segment->tag());
        self::assertEquals('7', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /**
     * @test
     */
    public function missing_sub_id(): void
    {
        $segment = new CNTControl(['CNT']);
        $this->expectException(MissingSubId::class);
        $segment->subId();
    }
}
