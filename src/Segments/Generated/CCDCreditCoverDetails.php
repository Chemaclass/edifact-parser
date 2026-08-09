<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * creditCoverDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class CCDCreditCoverDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'CCD';
    }

    /**
     * 4505 (an..3)
     */
    public function creditCoverRequestCoded(): string
    {
        return $this->element(1);
    }

    /**
     * 4507 (an..3)
     */
    public function creditCoverResponseCoded(): string
    {
        return $this->element(2);
    }

    /**
     * 4509 (an..3)
     */
    public function creditCoverReasonCoded(): string
    {
        return $this->element(3);
    }
}
