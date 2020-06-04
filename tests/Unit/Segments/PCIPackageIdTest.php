<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\PCIPackageId;
use PHPUnit\Framework\TestCase;

final class PCIPackageIdTest extends TestCase
{
    /** @test */
    public function segmentValues(): void
    {
        $rawValues = ['PCI', '18', '00250559268149700889'];
        $segment = new PCIPackageId($rawValues);

        self::assertEquals(PCIPackageId::class, $segment->tag());
        self::assertEquals('18', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
