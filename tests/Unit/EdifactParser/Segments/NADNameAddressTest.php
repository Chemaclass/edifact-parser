<?php

declare(strict_types=1);

namespace App\Tests\EdifactParser\Segments;

use App\EdifactParser\Segments\NADNameAddress;
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
        $segment = new NADNameAddress($rawValues);

        self::assertEquals('NAD', $segment->name());
        self::assertEquals('CZ', $segment->subSegmentKey());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
