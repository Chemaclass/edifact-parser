<?php

declare(strict_types=1);

namespace App\Tests\EdifactParser\Segments;

use App\EdifactParser\Segments\BGMBeginningOfMessage;
use PHPUnit\Framework\TestCase;

final class BGMBeginningOfMessageTest extends TestCase
{
    /** @test */
    public function segmentValues(): void
    {
        $rawValues = ['BGM', '340', '00250559268149700889', '9'];
        $segment = new BGMBeginningOfMessage($rawValues);

        self::assertEquals('BGM', $segment->name());
        self::assertEquals('340', $segment->subSegmentKey());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
