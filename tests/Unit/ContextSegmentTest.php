<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EdifactParser\ContextSegment;
use EdifactParser\Segments\UnknownSegment;
use PHPUnit\Framework\TestCase;

final class ContextSegmentTest extends TestCase
{
    public function test_tag_and_sub_id_proxy_parent_segment(): void
    {
        $segment = new UnknownSegment(['NAD', 'CN']);
        $context = new ContextSegment($segment);

        self::assertSame('NAD', $context->tag());
        self::assertSame('CN', $context->subId());
    }

    public function test_parsed_sub_id_and_raw_values_proxy_parent_segment(): void
    {
        $rawValues = ['NAD', ['CN', 'sub']];
        $context = new ContextSegment(new UnknownSegment($rawValues));

        self::assertSame(['CN', 'sub'], $context->parsedSubId());
        self::assertSame($rawValues, $context->rawValues());
    }
}
