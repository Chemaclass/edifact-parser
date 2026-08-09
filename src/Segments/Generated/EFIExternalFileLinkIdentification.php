<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * externalFileLinkIdentification — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class EFIExternalFileLinkIdentification extends AbstractSegment
{
    public function tag(): string
    {
        return 'EFI';
    }

    /**
     * C077/1508 (an..35)
     */
    public function fileName(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C077/7008 (an..35)
     */
    public function itemDescription(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C099/1516 (an..17)
     */
    public function fileFormat(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C099/1056 (an..9)
     */
    public function version(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C099/1503 (an..3)
     */
    public function dataFormatCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C099/1502 (an..35)
     */
    public function dataFormat(): string
    {
        return $this->component(3, 2);
    }

    /**
     * 1050 (an..6)
     */
    public function sequenceNumber(): string
    {
        return $this->element(3);
    }
}
