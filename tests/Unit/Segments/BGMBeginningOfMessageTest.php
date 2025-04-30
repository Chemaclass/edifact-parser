<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\BGMBeginningOfMessage;
use PHPUnit\Framework\TestCase;

final class BGMBeginningOfMessageTest extends TestCase
{
    public function test_segment_values_with_single_subId(): void
    {
        $rawValues = ['BGM', '340', '00250559268149700889', '9'];
        $segment = new BGMBeginningOfMessage($rawValues);

        self::assertEquals('BGM', $segment->tag());
        self::assertEquals('340', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    public function test_segment_values_with_parsedSubId(): void
    {
        $rawValues = ['BGM', '220::9:ZBEN', '00250559268149700889', '9'];
        $segment = new BGMBeginningOfMessage($rawValues);

        self::assertEquals('BGM', $segment->tag());
        self::assertEquals('220::9:ZBEN', $segment->subId());
        self::assertEquals(['220', '', '9', 'ZBEN'], $segment->parsedSubId());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
