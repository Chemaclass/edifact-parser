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
    /**
     * @test
     */
    public function list_with_one_segment(): void
    {
        $fileContent = "UNH+1+IFTMIN:S:93A:UN:PN001'";

        self::assertEquals([
            new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
        ], $this->segmentsFromFileContent($fileContent));
    }

    /**
     * @test
     */
    public function list_with_multiple_segments(): void
    {
        $fileContent = "UNH+1+IFTMIN:S:1A:UN:P1'\nUNH+2+IFTMIN:R:2A:UN:P2'\nCNT+7:0.1:KGM'";

        self::assertEquals([
            new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '1A', 'UN', 'P1']]),
            new UNHMessageHeader(['UNH', '2', ['IFTMIN', 'R', '2A', 'UN', 'P2']]),
            new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
        ], $this->segmentsFromFileContent($fileContent));
    }

    private function segmentsFromFileContent(string $fileContent): array
    {
        $parser = (new Parser())->loadString($fileContent);

        return SegmentList::withDefaultFactory()
            ->fromRaw($parser->get());
    }
}
