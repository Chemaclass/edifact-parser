<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EDI\Parser;
use EdifactParser\SegmentList;
use EdifactParser\Segments\CNTControl;
use EdifactParser\Segments\UNHMessageHeader;
use PHPUnit\Framework\TestCase;

final class SegmentListTest extends TestCase
{
    /** @test */
    public function listWithOneSegment(): void
    {
        $fileContent = "UNH+1+IFTMIN:S:93A:UN:PN001'";

        self::assertEquals([
            UNHMessageHeader::createFromArray(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
        ], $this->segmentsFromFileContent($fileContent));
    }

    /** @test */
    public function listWithMultipleSegments(): void
    {
        $fileContent = "UNH+1+IFTMIN:S:1A:UN:P1'\nUNH+2+IFTMIN:R:2A:UN:P2'\nCNT+7:0.1:KGM'";

        self::assertEquals([
            UNHMessageHeader::createFromArray(['UNH', '1', ['IFTMIN', 'S', '1A', 'UN', 'P1']]),
            UNHMessageHeader::createFromArray(['UNH', '2', ['IFTMIN', 'R', '2A', 'UN', 'P2']]),
            CNTControl::createFromArray(['CNT', ['7', '0.1', 'KGM']]),
        ], $this->segmentsFromFileContent($fileContent));
    }

    private function segmentsFromFileContent(string $fileContent)
    {
        return SegmentList::factory()->fromRaw((new Parser($fileContent))->get());
    }
}
