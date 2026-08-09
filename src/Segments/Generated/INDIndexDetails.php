<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * indexDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class INDIndexDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'IND';
    }

    /**
     * C545/5013 (an..3)
     */
    public function indexQualifier(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C545/5027 (an..3)
     */
    public function indexTypeCoded(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C545/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C545/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(3, 1);
    }

    /**
     * C546/5030 (n..6)
     */
    public function indexValue(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C546/5039 (an..3)
     */
    public function indexValueRepresentationCoded(): string
    {
        return $this->component(1, 2);
    }
}
