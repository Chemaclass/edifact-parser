<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

use function class_implements;
use function in_array;

/** @psalm-immutable */
final class SegmentFactory implements SegmentFactoryInterface
{
    /** @var array<string,string> */
    public const DEFAULT_SEGMENTS = [
        'UNH' => UNHMessageHeader::class,
        'UNB' => UNBInterchangeHeader::class,
        'DTM' => DTMDateTimePeriod::class,
        'NAD' => NADNameAddress::class,
        'MEA' => MEADimensions::class,
        'CNT' => CNTControl::class,
        'PCI' => PCIPackageId::class,
        'BGM' => BGMBeginningOfMessage::class,
        'UNT' => UNTMessageFooter::class,
        'RFF' => RFFReference::class,
        'CUX' => CUXCurrencyDetails::class,
        'LIN' => LINLineItem::class,
        'QTY' => QTYQuantity::class,
        'PRI' => PRIPrice::class,
        'PIA' => PIAAdditionalProductId::class,
        'UNS' => UNSSectionControl::class,
    ];

    private const TAG_LENGTH = 3;

    /**
     * The list of "segment class names" for every segment that might be created.
     *
     * @var array<string,string>
     */
    private array $segments;

    /**
     * @param array<string,string> $segments
     */
    private function __construct(array $segments)
    {
        foreach ($segments as $tag => $class) {
            Assert::length($tag, self::TAG_LENGTH);

            if (!$this->classImplements($class, SegmentInterface::class)) {
                throw new InvalidArgumentException("'{$class}' must implements 'SegmentInterface'");
            }
        }

        $this->segments = $segments;
    }

    /**
     * @psalm-pure
     *
     * @param array<string,string> $segments
     * The key: The 'Segment Tag' -> A three-character (alphanumeric) that identifies the segment.
     * The value: The class that will be created once that 'Segment Tag' is found. It must implement
     * the `SegmentInterface` in order to be able to work with the factory, otherwise it will be ignored.
     */
    public static function withSegments(array $segments): self
    {
        return new self($segments);
    }

    /**
     * @psalm-pure
     */
    public static function withDefaultSegments(): self
    {
        return new self(self::DEFAULT_SEGMENTS);
    }

    public function createSegmentFromArray(array $rawArray): SegmentInterface
    {
        $tag = (string) $rawArray[0];
        Assert::length($tag, self::TAG_LENGTH);
        $className = $this->segments[$tag] ?? '';

        if (empty($className)) {
            return new UnknownSegment($rawArray);
        }

        $segment = new $className($rawArray);
        Assert::isInstanceOf($segment, SegmentInterface::class);

        return $segment;
    }

    private function classImplements(string $className, string $interface): bool
    {
        $interfaces = class_implements($className);

        return $interfaces && in_array($interface, class_implements($className));
    }
}
