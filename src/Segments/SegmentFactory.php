<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

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
     * @psalm-var list<string>
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
                && class_exists($segmentFullClassName)
                && method_exists($segmentFullClassName, 'createFromArray')
            ) {
                $newSegment = $segmentFullClassName::createFromArray($rawArray);

                if ($newSegment instanceof SegmentInterface) {
                    return $newSegment;
                }
            }
        }

        return UnknownSegment::createFromArray($rawArray);
    }
}
