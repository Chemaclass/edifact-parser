<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * messageTypeIdentification — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class MSGMessageTypeIdentification extends AbstractSegment
{
    public function tag(): string
    {
        return 'MSG';
    }

    /**
     * C709/1475 (an..6)
     */
    public function messageTypeIdentifier(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C709/1056 (an..9)
     */
    public function version(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C709/1058 (an..9)
     */
    public function release(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C709/1476 (an..2)
     */
    public function controlAgency(): string
    {
        return $this->component(3, 1);
    }

    /**
     * C709/1523 (an..6)
     */
    public function associationAssignedIdentification(): string
    {
        return $this->component(4, 1);
    }

    /**
     * C709/1060 (an..6)
     */
    public function revisionNumber(): string
    {
        return $this->component(5, 1);
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
