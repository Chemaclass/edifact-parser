<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use function basename;
use function class_implements;
use function in_array;
use function mb_substr;
use function str_replace;
use Webmozart\Assert\Assert;

/** @psalm-immutable */
final class SegmentFactory implements SegmentFactoryInterface
{
    public const DEFAULT_SEGMENT_CLASS_NAMES = [
        UNHMessageHeader::class,
        DTMDateTimePeriod::class,
        NADNameAddress::class,
        MEADimensions::class,
        CNTControl::class,
        PCIPackageId::class,
        BGMBeginningOfMessage::class,
        UNTMessageFooter::class,
    ];

    private const TAG_LENGTH = 3;

    /**
     * The list of "segment class names" for every segment that might be created.
     * The "segment class name" must implement the `SegmentInterface` in order to be
     * able to work with the factory, otherwise it will be ignored.
     *
     * @var string[]
     */
    private array $segmentClassNames;

    /** @psalm-pure */
    public static function withSegments(string...$segmentClassNames): self
    {
        return new self(...$segmentClassNames);
    }

    /** @psalm-pure */
    public static function withDefaultSegments(): self
    {
        return new self(...self::DEFAULT_SEGMENT_CLASS_NAMES);
    }

    private function __construct(string...$segmentClassNames)
    {
        $this->segmentClassNames = $segmentClassNames;
    }

    public function segmentFromArray(array $rawArray): SegmentInterface
    {
        $tag = (string) $rawArray[0];
        Assert::length($tag, self::TAG_LENGTH);

        foreach ($this->segmentClassNames as $className) {
            if ($this->isTheRightSegmentTag($tag, $className)) {
                $segment = new $className($rawArray);
                Assert::isInstanceOf($segment, SegmentInterface::class);

                return $segment;
            }
        }

        return new UnknownSegment($rawArray);
    }

    private function isTheRightSegmentTag(string $tag, string $className): bool
    {
        return $tag === $this->segmentTagFromClass($className)
            && $this->classImplements($className, SegmentInterface::class);
    }

    private function segmentTagFromClass(string $className): string
    {
        $basename = basename(str_replace('\\', '/', $className));

        return mb_substr($basename, 0, self::TAG_LENGTH);
    }

    private function classImplements(string $className, string $interface): bool
    {
        $interfaces = class_implements($className);

        return $interfaces && in_array($interface, class_implements($className));
    }
}
