<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * structureIdentification — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class BIIStructureIdentification extends AbstractSegment
{
    public function tag(): string
    {
        return 'BII';
    }

    /**
     * 7429 (an..3)
     */
    public function indexingStructureQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C045/7436 (an..17)
     */
    public function levelOneId(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C045/7438 (an..17)
     */
    public function levelTwoId(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C045/7440 (an..17)
     */
    public function levelThreeId(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C045/7442 (an..17)
     */
    public function levelFourId(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C045/7444 (an..17)
     */
    public function levelFiveId(): string
    {
        return $this->component(4, 2);
    }

    /**
     * C045/7446 (an..17)
     */
    public function levelSixId(): string
    {
        return $this->component(5, 2);
    }

    /**
     * 7140 (an..35)
     */
    public function itemNumber(): string
    {
        return $this->element(3);
    }
}
