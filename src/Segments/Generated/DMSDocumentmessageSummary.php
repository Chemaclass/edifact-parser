<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * documentmessageSummary — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class DMSDocumentmessageSummary extends AbstractSegment
{
    public function tag(): string
    {
        return 'DMS';
    }

    /**
     * 1004 (an..35)
     */
    public function documentmessageNumber(): string
    {
        return $this->element(1);
    }

    /**
     * 1001 (an..3)
     */
    public function documentmessageNameCoded(): string
    {
        return $this->element(2);
    }

    /**
     * 7240 (n..15)
     */
    public function totalNumberOfItems(): string
    {
        return $this->element(3);
    }
}
