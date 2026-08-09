<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * compositeDataElementIdentification — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class CMPCompositeDataElementIdentification extends AbstractSegment
{
    public function tag(): string
    {
        return 'CMP';
    }

    /**
     * 9146 (an..4)
     */
    public function compositeDataElementTag(): string
    {
        return $this->element(1);
    }

    /**
     * 1507 (an..3)
     */
    public function classDesignatorCoded(): string
    {
        return $this->element(2);
    }

    /**
     * 4513 (an..3)
     */
    public function maintenanceOperationCoded(): string
    {
        return $this->element(3);
    }
}
