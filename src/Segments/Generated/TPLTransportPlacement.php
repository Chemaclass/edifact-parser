<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * transportPlacement — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class TPLTransportPlacement extends AbstractSegment
{
    public function tag(): string
    {
        return 'TPL';
    }

    /**
     * C222/8213 (an..9)
     */
    public function idOfMeansOfTransportIdentification(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C222/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C222/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C222/8212 (an..35)
     */
    public function idOfTheMeansOfTransport(): string
    {
        return $this->component(3, 1);
    }

    /**
     * C222/8453 (an..3)
     */
    public function nationalityOfMeansOfTransportCoded(): string
    {
        return $this->component(4, 1);
    }
}
