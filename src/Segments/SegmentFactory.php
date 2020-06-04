<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use function basename;
use function class_implements;
use function in_array;
use function mb_substr;
use function str_replace;

/** @psalm-immutable */
final class SegmentFactory implements SegmentFactoryInterface
{
    public const DEFAULT_SEGMENT_NAMES = [
        UNHMessageHeader::class,
        DTMDateTimePeriod::class,
        NADNameAddress::class,
        MEADimensions::class,
        CNTControl::class,
        PCIPackageId::class,
        BGMBeginningOfMessage::class,
        UNTMessageFooter::class,
    ];

    /**
     * The list of "segment class names" for every segment that might be created.
     * The "segment class name" must implement the `SegmentInterface` in order to be
     * able to work with the factory, otherwise it will be ignored.
     *
     * @var string[]
     */
    private array $segmentClasses;

    /** @psalm-pure */
    public static function withSegments(string...$segments): self
    {
        return new self(...$segments);
    }

    /** @psalm-pure */
    public static function withDefaultSegments(): self
    {
        return new self(...self::DEFAULT_SEGMENT_NAMES);
    }

    private function __construct(string...$segmentClasses)
    {
        $this->segmentClasses = $segmentClasses;
    }

    public function segmentFromArray(array $rawArray): SegmentInterface
    {
        foreach ($this->segmentClasses as $segmentFullClassName) {
            $segmentClassName = basename(str_replace('\\', '/', $segmentFullClassName));
            $segmentTag = mb_substr($segmentClassName, 0, 3);

            if ($rawArray[0] === $segmentTag
                && $this->classImplements($segmentFullClassName, SegmentInterface::class)
            ) {
                /** @var SegmentInterface $segment */
                $segment = $segmentFullClassName::createFromArray($rawArray);

                return $segment;
            }
        }

        return UnknownSegment::createFromArray($rawArray);
    }

    private function classImplements(string $className, string $interface): bool
    {
        $interfaces = class_implements($className);

        return $interfaces && in_array($interface, $interfaces);
    }
}
