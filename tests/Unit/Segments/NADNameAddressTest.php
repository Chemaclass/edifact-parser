<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\NADNameAddress;
use PHPUnit\Framework\TestCase;

final class NADNameAddressTest extends TestCase
{
    /** @test */
    public function segmentValues(): void
    {
        $rawValues = [
            'NAD',
            'CZ',
            ['0410314', '160', 'Z12'],
            '',
            'Company Returns Centre',
            'c/o Carrier AB',
            'Malmo',
            '',
            '20713',
            'DE',
        ];
        $segment = NADNameAddress::createFromArray($rawValues);

        self::assertEquals(NADNameAddress::class, $segment->tag());
        self::assertEquals('CZ', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
