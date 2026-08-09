<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * goodsIdentityNumber — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class GINGoodsIdentityNumber extends AbstractSegment
{
    public function tag(): string
    {
        return 'GIN';
    }

    /**
     * 7405 (an..3)
     */
    public function identityNumberQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C208/7402 (an..35)
     */
    public function identityNumber(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C208/7402 (an..35)
     */
    public function identityNumber2(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C208/7402 (an..35)
     */
    public function identityNumber3(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C208/7402 (an..35)
     */
    public function identityNumber4(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C208/7402 (an..35)
     */
    public function identityNumber5(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C208/7402 (an..35)
     */
    public function identityNumber6(): string
    {
        return $this->component(1, 4);
    }

    /**
     * C208/7402 (an..35)
     */
    public function identityNumber7(): string
    {
        return $this->firstComponent(5);
    }

    /**
     * C208/7402 (an..35)
     */
    public function identityNumber8(): string
    {
        return $this->component(1, 5);
    }

    /**
     * C208/7402 (an..35)
     */
    public function identityNumber9(): string
    {
        return $this->firstComponent(6);
    }

    /**
     * C208/7402 (an..35)
     */
    public function identityNumber10(): string
    {
        return $this->component(1, 6);
    }
}
