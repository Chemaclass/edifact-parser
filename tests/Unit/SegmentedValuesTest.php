<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EDI\Parser;
use EdifactParser\SegmentedValues;
use EdifactParser\Segments\CNTControl;
use EdifactParser\Segments\UNHMessageHeader;
use PHPUnit\Framework\TestCase;

final class SegmentedValuesTest extends TestCase
{
    /** @test */
    public function listWithOneSegment(): void
    {
        $fileContent = "UNH+1+IFTMIN:S:93A:UN:PN001'";
        $values = SegmentedValues::factory()->fromRaw((new Parser($fileContent))->get());

        self::assertEquals([
            new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
        ], $values->list());
    }

    /** @test */
    public function listWithMultipleSegments(): void
    {
        $fileContent = "UNH+1+IFTMIN:S:1A:UN:P1'\nUNH+2+IFTMIN:R:2A:UN:P2'\nCNT+7:0.1:KGM'";
        $values = SegmentedValues::factory()->fromRaw((new Parser($fileContent))->get());

        self::assertEquals([
            new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '1A', 'UN', 'P1']]),
            new UNHMessageHeader(['UNH', '2', ['IFTMIN', 'R', '2A', 'UN', 'P2']]),
            new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
        ], $values->list());
    }
}
