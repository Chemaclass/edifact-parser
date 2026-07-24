<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\RFFReference;
use PHPUnit\Framework\TestCase;

class RFFReferenceTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['RFF', ['ADE', '123413287423784']];
        $segment = new RFFReference($rawValues);

        self::assertEquals('RFF', $segment->tag());
        self::assertEquals('ADE', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /**
     * @test
     */
    public function typed_accessors(): void
    {
        $segment = new RFFReference(['RFF', ['ON', 'ORDER-42']]);

        self::assertSame('ON', $segment->qualifier());
        self::assertSame('ORDER-42', $segment->referenceNumber());
    }
}
