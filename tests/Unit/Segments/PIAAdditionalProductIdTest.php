<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\PIAAdditionalProductId;
use PHPUnit\Framework\TestCase;

class PIAAdditionalProductIdTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['PIA', '22'];
        $segment = new PIAAdditionalProductId($rawValues);

        self::assertEquals('PIA', $segment->tag());
        self::assertEquals('22', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
