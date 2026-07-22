<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\MOAMonetaryAmount;
use EdifactParser\Segments\SegmentFactory;
use PHPUnit\Framework\TestCase;

final class MOAMonetaryAmountTest extends TestCase
{
    /**
     * @test
     */
    public function typed_accessors(): void
    {
        $segment = new MOAMonetaryAmount(['MOA', ['79', '1250.50', 'EUR']]);

        self::assertSame('MOA', $segment->tag());
        self::assertSame('79', $segment->subId());
        self::assertSame('79', $segment->amountQualifier());
        self::assertSame('1250.50', $segment->amount());
        self::assertSame(1250.50, $segment->amountAsFloat());
        self::assertSame('EUR', $segment->currencyCode());
    }

    /**
     * @test
     */
    public function factory_creates_a_typed_moa_segment_by_default(): void
    {
        $segment = SegmentFactory::withDefaultSegments()
            ->createSegmentFromArray(['MOA', ['79', '1250.50']]);

        self::assertInstanceOf(MOAMonetaryAmount::class, $segment);
    }
}
