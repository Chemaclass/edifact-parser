<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class SegmentFactory implements SegmentFactoryInterface
{
    /**
     * The list of "segment class names" for every segment that might created.
     * The "segment class name" must implement the SegmentInterface in order to be able to work with the factory,
     * otherwise it will be ignored.
     *
     * @psalm-var list<string>
     */
    private array $segmentClasses;

    /** @psalm-pure */
    public static function withDefaultSegments(string...$segments): self
    {
        $default = [
            UNHMessageHeader::class,
            DTMDateTimePeriod::class,
            NADNameAddress::class,
            MEADimensions::class,
            CNTControl::class,
            PCIPackageId::class,
            BGMBeginningOfMessage::class,
            UNTMessageFooter::class,
        ];

        return new self(...array_merge($default, $segments));
    }

    public function __construct(string...$segmentClasses)
    {
        $this->segmentClasses = $segmentClasses;
    }

    public function segmentFromArray(array $rawArray): SegmentInterface
    {
//        switch ($rawArray[0]) {
//            case 'UNH':
//                return new UNHMessageHeader($rawArray);
//            case 'DTM':
//                return new DTMDateTimePeriod($rawArray);
//            case 'NAD':
//                return new NADNameAddress($rawArray);
//            case 'MEA':
//                return new MEADimensions($rawArray);
//            case 'CNT':
//                return new CNTControl($rawArray);
//            case 'PCI':
//                return new PCIPackageId($rawArray);
//            case 'BGM':
//                return new BGMBeginningOfMessage($rawArray);
//            case 'UNT':
//                return new UNTMessageFooter($rawArray);
//        }
        foreach ($this->segmentClasses as $segmentFullClassName) {
            $segmentClassName = basename(str_replace('\\', '/', $segmentFullClassName));
            $segmentTag = mb_substr($segmentClassName, 0, 3);

            if ($rawArray[0] === $segmentTag
                && in_array(SegmentInterface::class, class_implements($segmentFullClassName))
            ) {
                return new $segmentFullClassName($rawArray);
            }
        }

        return new UnknownSegment($rawArray);
    }
}
