<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * agreementIdentification — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class AGRAgreementIdentification extends AbstractSegment
{
    public function tag(): string
    {
        return 'AGR';
    }

    /**
     * C543/7431 (an..3)
     */
    public function agreementTypeQualifier(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C543/7433 (an..3)
     */
    public function agreementTypeCoded(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C543/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C543/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(3, 1);
    }

    /**
     * C543/7434 (an..70)
     */
    public function agreementTypeDescription(): string
    {
        return $this->component(4, 1);
    }

    /**
     * 9419 (an..3)
     */
    public function serviceLayerCoded(): string
    {
        return $this->element(2);
    }
}
