<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * documentLineIdentification — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class DLIDocumentLineIdentification extends AbstractSegment
{
    public function tag(): string
    {
        return 'DLI';
    }

    /**
     * 1073 (an..3)
     */
    public function documentLineIndicatorCoded(): string
    {
        return $this->element(1);
    }

    /**
     * 1082 (n..6)
     */
    public function lineItemNumber(): string
    {
        return $this->element(2);
    }
}
