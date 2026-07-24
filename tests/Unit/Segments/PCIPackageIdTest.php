<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\PCIPackageId;
use PHPUnit\Framework\TestCase;

final class PCIPackageIdTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['PCI', '18', '00250559268149700889'];
        $segment = new PCIPackageId($rawValues);

        self::assertEquals('PCI', $segment->tag());
        self::assertEquals('18', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /**
     * @test
     */
    public function typed_accessors(): void
    {
        $segment = new PCIPackageId(['PCI', '18', '05055700896']);

        self::assertSame('18', $segment->markingInstructionsCode());
        self::assertSame('05055700896', $segment->marksAndLabels());
    }
}
