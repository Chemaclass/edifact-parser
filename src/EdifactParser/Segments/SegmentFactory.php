<?php

declare(strict_types=1);

namespace App\EdifactParser\Segments;

final class SegmentFactory
{
    public static function factory(array $rawArray): SegmentInterface
    {
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
}
