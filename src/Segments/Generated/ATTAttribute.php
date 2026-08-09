<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * attribute — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class ATTAttribute extends AbstractSegment
{
    public function tag(): string
    {
        return 'ATT';
    }

    /**
     * 9017 (an..3)
     */
    public function attributeFunctionQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C955/9021 (an..3)
     */
    public function attributeTypeCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C955/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C955/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C956/9019 (an..3)
     */
    public function attributeCoded(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C956/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C956/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C956/9018 (an..35)
     */
    public function attribute(): string
    {
        return $this->component(3, 3);
    }
}
