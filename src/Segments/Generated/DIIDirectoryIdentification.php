<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * directoryIdentification — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class DIIDirectoryIdentification extends AbstractSegment
{
    public function tag(): string
    {
        return 'DII';
    }

    /**
     * 1056 (an..9)
     */
    public function version(): string
    {
        return $this->element(1);
    }

    /**
     * 1058 (an..9)
     */
    public function release(): string
    {
        return $this->element(2);
    }

    /**
     * 9148 (an..3)
     */
    public function directoryStatus(): string
    {
        return $this->element(3);
    }

    /**
     * 1476 (an..2)
     */
    public function controlAgency(): string
    {
        return $this->element(4);
    }

    /**
     * 3453 (an..3)
     */
    public function languageCoded(): string
    {
        return $this->element(5);
    }

    /**
     * 4513 (an..3)
     */
    public function maintenanceOperationCoded(): string
    {
        return $this->element(6);
    }
}
