<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\BGMBeginningOfMessage;
use EdifactParser\Segments\CNTControl;
use EdifactParser\Segments\DTMDateTimePeriod;
use EdifactParser\Segments\MEADimensions;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\PCIPackageId;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UnknownSegment;
use EdifactParser\Segments\UNTMessageFooter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SegmentFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function with_default_segments(): void
    {
        $factory = SegmentFactory::withDefaultSegments();

        self::assertInstanceOf(UNHMessageHeader::class, $factory->createSegmentFromArray(['UNH']));
        self::assertInstanceOf(DTMDateTimePeriod::class, $factory->createSegmentFromArray(['DTM']));
        self::assertInstanceOf(NADNameAddress::class, $factory->createSegmentFromArray(['NAD']));
        self::assertInstanceOf(MEADimensions::class, $factory->createSegmentFromArray(['MEA']));
        self::assertInstanceOf(CNTControl::class, $factory->createSegmentFromArray(['CNT']));
        self::assertInstanceOf(PCIPackageId::class, $factory->createSegmentFromArray(['PCI']));
        self::assertInstanceOf(BGMBeginningOfMessage::class, $factory->createSegmentFromArray(['BGM']));
        self::assertInstanceOf(UNTMessageFooter::class, $factory->createSegmentFromArray(['UNT']));
        self::assertInstanceOf(UnknownSegment::class, $factory->createSegmentFromArray(['___']));
    }

    /**
     * @test
     */
    public function with_custom_segments(): void
    {
        $factory = SegmentFactory::withSegments([
            'UNH' => UNHMessageHeader::class,
        ]);

        self::assertInstanceOf(UNHMessageHeader::class, $factory->createSegmentFromArray(['UNH']));
        self::assertInstanceOf(UnknownSegment::class, $factory->createSegmentFromArray(['DTM']));
    }

    /**
     * @test
     */
    public function exception_when_tag_too_large(): void
    {
        $this->expectException(InvalidArgumentException::class);
        SegmentFactory::withSegments(['TAG_TOO_LARGE' => UNHMessageHeader::class]);
    }

    /**
     * @test
     */
    public function exception_when_creating_non_valid_tag(): void
    {
        $this->expectException(InvalidArgumentException::class);
        SegmentFactory::withSegments(['NON' => NONFakeSegment::class]);
    }
}
