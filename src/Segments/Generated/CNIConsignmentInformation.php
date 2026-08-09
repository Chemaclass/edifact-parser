<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * consignmentInformation — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class CNIConsignmentInformation extends AbstractSegment
{
    public function tag(): string
    {
        return 'CNI';
    }

    /**
     * 1490 (n..4)
     */
    public function consolidationItemNumber(): string
    {
        return $this->element(1);
    }

    /**
     * C503/1004 (an..35)
     */
    public function documentmessageNumber(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C503/1373 (an..3)
     */
    public function documentmessageStatusCoded(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C503/1366 (an..35)
     */
    public function documentmessageSource(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C503/3453 (an..3)
     */
    public function languageCoded(): string
    {
        return $this->component(3, 2);
    }

    /**
     * 1312 (n..4)
     */
    public function consignmentLoadSequenceNumber(): string
    {
        return $this->element(3);
    }
}
