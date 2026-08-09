<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * documentmessageDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class DOCDocumentmessageDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'DOC';
    }

    /**
     * C002/1001 (an..3)
     */
    public function documentmessageNameCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C002/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C002/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C002/1000 (an..35)
     */
    public function documentmessageName(): string
    {
        return $this->component(3, 1);
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
     * 3153 (an..3)
     */
    public function communicationChannelIdentifierCoded(): string
    {
        return $this->element(3);
    }

    /**
     * 1220 (n..2)
     */
    public function numberOfCopiesOfDocumentRequired(): string
    {
        return $this->element(4);
    }

    /**
     * 1218 (n..2)
     */
    public function numberOfOriginalsOfDocumentRequired(): string
    {
        return $this->element(5);
    }
}
