<?php

declare(strict_types=1);

namespace App\Tests\EdifactParser\Segments;

use App\EdifactParser\Segments\PCIPackageId;
use PHPUnit\Framework\TestCase;

final class PCIPackageIdTest extends TestCase
{
    /** @test */
    public function segmentValues(): void
    {
        $rawValues = ['PCI', '18', '00250559268149700889'];
        $segment = new PCIPackageId($rawValues);

        self::assertEquals('PCI', $segment->name());
        self::assertEquals('18', $segment->subSegmentKey());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
