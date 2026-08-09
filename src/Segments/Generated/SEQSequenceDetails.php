<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * sequenceDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class SEQSequenceDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'SEQ';
    }

    /**
     * 1245 (an..3)
     */
    public function statusIndicatorCoded(): string
    {
        return $this->element(1);
    }

    /**
     * C286/1050 (an..6)
     */
    public function sequenceNumber(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C286/1159 (an..3)
     */
    public function sequenceNumberSourceCoded(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C286/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C286/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(3, 2);
    }
}
