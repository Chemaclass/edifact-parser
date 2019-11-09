<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EDI\Parser;
use EdifactParser\SegmentedValues;
use EdifactParser\Segments\UNHMessageHeader;
use PHPUnit\Framework\TestCase;

final class SegmentedValuesTest extends TestCase
{
    /** @test */
    public function getListOfSegments(): void
    {
        $fileContent = "UNH+1+IFTMIN:S:93A:UN:PN001'\nUNH+2+IFTMIN:S:93A:UN:PN001'";
        $parser = new Parser($fileContent);
        $values = SegmentedValues::fromRaw($parser->get());

        self::assertEquals([
            new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
            new UNHMessageHeader(['UNH', '2', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
        ], $values->list());
    }
}
