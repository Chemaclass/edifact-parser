<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * codeValueDefinition — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class CDVCodeValueDefinition extends AbstractSegment
{
    public function tag(): string
    {
        return 'CDV';
    }

    /**
     * 9426 (an..35)
     */
    public function codeValue(): string
    {
        return $this->element(1);
    }

    /**
     * 9434 (an..70)
     */
    public function codeName(): string
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
