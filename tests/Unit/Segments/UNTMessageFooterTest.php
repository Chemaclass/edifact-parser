<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\UNTMessageFooter;
use PHPUnit\Framework\TestCase;

final class UNTMessageFooterTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['UNT', '19', '1'];
        $segment = new UNTMessageFooter($rawValues);

        self::assertEquals(UNTMessageFooter::class, $segment->tag());
        self::assertEquals('19', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
