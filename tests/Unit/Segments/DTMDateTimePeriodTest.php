<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Exception\MissingSubId;
use EdifactParser\Segments\DTMDateTimePeriod;
use PHPUnit\Framework\TestCase;

final class DTMDateTimePeriodTest extends TestCase
{
    /** @test */
    public function segmentValues(): void
    {
        $rawValues = ['DTM', ['10', '20191002', '102']];
        $segment = DTMDateTimePeriod::createFromArray($rawValues);

        self::assertEquals(DTMDateTimePeriod::class, $segment->tag());
        self::assertEquals('10', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /** @test */
    public function missingSubId(): void
    {
        $segment = DTMDateTimePeriod::createFromArray(['DTM']);
        $this->expectException(MissingSubId::class);
        $segment->subId();
    }
}
