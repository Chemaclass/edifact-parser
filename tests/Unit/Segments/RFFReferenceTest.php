<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\RFFReference;
use PHPUnit\Framework\TestCase;

class RFFReferenceTest extends TestCase
{
    /** @test */
    public function segmentValues() {
        $rawValues = ['RFF', ['ADE', '123413287423784']];
        $segment = new RFFReference($rawValues);

        self::assertEquals(RFFReference::class, $segment->tag());
        self::assertEquals('ADE', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}