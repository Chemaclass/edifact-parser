<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\BGMBeginningOfMessage;
use PHPUnit\Framework\TestCase;

final class BGMBeginningOfMessageTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['BGM', '340', '00250559268149700889', '9'];
        $segment = new BGMBeginningOfMessage($rawValues);

        self::assertEquals('BGM', $segment->tag());
        self::assertEquals('340', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
