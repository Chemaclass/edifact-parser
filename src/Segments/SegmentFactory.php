<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

final class SegmentFactory
{
    /** @var CustomSegmentFactoryInterface|null */
    private $customSegmentsFactory;

    public function __construct(?CustomSegmentFactoryInterface $customSegmentsFactory)
    {
        $this->customSegmentsFactory = $customSegmentsFactory;
    }

    public function segmentFromArray(array $rawArray): SegmentInterface
    {
        $customSegment = $this->customSegment($rawArray);

        if ($customSegment) {
            return $customSegment;
        }

        $name = $rawArray[0];

        switch ($name) {
            case 'UNH':
                return new UNHMessageHeader($rawArray);
            case 'DTM':
                return new DTMDateTimePeriod($rawArray);
            case 'NAD':
                return new NADNameAddress($rawArray);
            case 'MEA':
                return new MEADimensions($rawArray);
            case 'CNT':
                return new CNTControl($rawArray);
            case 'PCI':
                return new PCIPackageId($rawArray);
            case 'BGM':
                return new BGMBeginningOfMessage($rawArray);
            case 'UNT':
                return new UNTMessageFooter($rawArray);
        }

        return new UnknownSegment($rawArray);
    }

    private function customSegment(array $rawArray): ?SegmentInterface
    {
        if ($this->customSegmentsFactory) {
            $segment = $this->customSegmentsFactory->segmentFromArray($rawArray);
            if ($segment) {
                return $segment;
            }
        }

        return null;
    }
}
