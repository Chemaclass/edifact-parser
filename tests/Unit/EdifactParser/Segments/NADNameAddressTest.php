<?php

declare(strict_types=1);

namespace AnalyticsBundle\tests\unit\Service\EDIParser\Segments;

use App\EdifactParser\Segments\NADNameAddress;
use PHPUnit\Framework\TestCase;

final class NADNameAddressTest extends TestCase
{
    /** @test */
    public function subSegmentKey(): void
    {
        $segment = new NADNameAddress([
            'NAD',
            'CZ',
            ['0410106314', '160', 'Z12'],
            '',
            'Company Returns Centre',
            'c/o Carrier AB',
            'Malmo',
            '',
            '20713',
            'SE',
        ]);

        $this->assertEquals('CZ', $segment->subSegmentKey());
    }
}
