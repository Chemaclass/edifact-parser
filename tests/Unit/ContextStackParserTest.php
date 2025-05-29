<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EdifactParser\ContextStackParser;
use EdifactParser\ContextSegment;
use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\Segments\UNTMessageFooter;
use EdifactParser\Segments\UnknownSegment;
use PHPUnit\Framework\TestCase;

final class ContextStackParserTest extends TestCase
{
    /** @test */
    public function groups_segments_by_context(): void
    {
        $nad = new NADNameAddress(['NAD', 'CN']);
        $com = new UnknownSegment(['COM', '123:TE']);
        $lin = new LINLineItem(['LIN', '1']);
        $qty = new QTYQuantity(['QTY', ['21', '5']]);
        $unt = new UNTMessageFooter(['UNT', '19', '1']);

        $parser = new ContextStackParser();
        $result = $parser->parse($nad, $com, $lin, $qty, $unt);

        self::assertEquals([
            new ContextSegment($nad, [$com]),
            new ContextSegment($lin, [$qty]),
        ], $result);
    }
}
