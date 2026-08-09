<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * customsStatusOfGoods — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class CSTCustomsStatusOfGoods extends AbstractSegment
{
    public function tag(): string
    {
        return 'CST';
    }

    /**
     * 1496 (n..5)
     */
    public function goodsItemNumber(): string
    {
        return $this->element(1);
    }

    /**
     * C246/7361 (an..18)
     */
    public function customsCodeIdentification(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C246/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C246/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C246/7361 (an..18)
     */
    public function customsCodeIdentification2(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C246/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C246/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C246/7361 (an..18)
     */
    public function customsCodeIdentification3(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C246/1131 (an..3)
     */
    public function codeListQualifier3(): string
    {
        return $this->component(1, 4);
    }

    /**
     * C246/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded3(): string
    {
        return $this->component(2, 4);
    }

    /**
     * C246/7361 (an..18)
     */
    public function customsCodeIdentification4(): string
    {
        return $this->firstComponent(5);
    }

    /**
     * C246/1131 (an..3)
     */
    public function codeListQualifier4(): string
    {
        return $this->component(1, 5);
    }

    /**
     * C246/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded4(): string
    {
        return $this->component(2, 5);
    }

    /**
     * C246/7361 (an..18)
     */
    public function customsCodeIdentification5(): string
    {
        return $this->firstComponent(6);
    }

    /**
     * C246/1131 (an..3)
     */
    public function codeListQualifier5(): string
    {
        return $this->component(1, 6);
    }

    /**
     * C246/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded5(): string
    {
        return $this->component(2, 6);
    }
}
