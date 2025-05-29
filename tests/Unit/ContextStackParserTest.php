<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EdifactParser\ContextSegment;
use EdifactParser\ContextStackParser;
use EdifactParser\Segments\DTMDateTimePeriod;
use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\Segments\UnknownSegment;
use EdifactParser\Segments\UNTMessageFooter;
use PHPUnit\Framework\TestCase;

final class ContextStackParserTest extends TestCase
{
    public function test_groups_segments_by_context(): void
    {
        $nad = new NADNameAddress(['NAD', 'CN']);
        $com = new UnknownSegment(['COM', '123:TE']);
        $lin = new LINLineItem(['LIN', '1']);
        $qty = new QTYQuantity(['QTY', ['21', '5']]);
        $doc = new UnknownSegment(['DOC', '0001']);
        $dtm = new DTMDateTimePeriod(['DTM', [7, '20231102']]);
        $unt = new UNTMessageFooter(['UNT', '19', '1']);

        $parser = new ContextStackParser();
        $result = $parser->parse($nad, $com, $lin, $qty, $doc, $dtm, $unt);

        self::assertEquals([
            new ContextSegment($nad, [$com]),
            new ContextSegment($lin, [$qty]),
            new ContextSegment($doc, [$dtm]),
        ], $result);
    }
}
