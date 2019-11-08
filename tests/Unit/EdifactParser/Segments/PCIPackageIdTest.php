<?php

declare(strict_types=1);

namespace AnalyticsBundle\tests\unit\Service\EDIParser\Segments;

use App\EdifactParser\Segments\PCIPackageId;
use PHPUnit\Framework\TestCase;

final class PCIPackageIdTest extends TestCase
{
    /** @test */
    public function subSegmentKey(): void
    {
        $segment = new PCIPackageId(['PCI', '18', '00250559268149700889']);
        $this->assertEquals('18', $segment->subSegmentKey());
    }
}
