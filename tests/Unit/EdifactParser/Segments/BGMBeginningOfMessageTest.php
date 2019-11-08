<?php

declare(strict_types=1);

namespace AnalyticsBundle\tests\unit\Service\EDIParser\Segments;

use App\EdifactParser\Segments\BGMBeginningOfMessage;
use PHPUnit\Framework\TestCase;

final class BGMBeginningOfMessageTest extends TestCase
{
    /** @test */
    public function subSegmentKey(): void
    {
        $segment = new BGMBeginningOfMessage(['BGM', '340', '00250559268149700889', '9']);
        $this->assertEquals('340', $segment->subSegmentKey());
    }
}
