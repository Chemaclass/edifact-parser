<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * structureComponentDefinition — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class SCDStructureComponentDefinition extends AbstractSegment
{
    public function tag(): string
    {
        return 'SCD';
    }

    /**
     * 7497 (an..3)
     */
    public function componentFunctionQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C786/7512 (an..35)
     */
    public function structureComponentIdentifier(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C786/7405 (an..3)
     */
    public function identityNumberQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C082/3039 (an..35)
     */
    public function partyIdIdentification(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C082/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C082/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 3);
    }

    /**
     * 4405 (an..3)
     */
    public function statusCoded(): string
    {
        return $this->element(4);
    }

    /**
     * 1222 (n..2)
     */
    public function configurationLevel(): string
    {
        return $this->element(5);
    }

    /**
     * C778/7164 (an..12)
     */
    public function hierarchicalIdNumber(): string
    {
        return $this->firstComponent(6);
    }

    /**
     * C778/1050 (an..6)
     */
    public function sequenceNumber(): string
    {
        return $this->component(1, 6);
    }

    /**
     * C240/7037 (an..17)
     */
    public function characteristicIdentification(): string
    {
        return $this->firstComponent(7);
    }

    /**
     * C240/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 7);
    }

    /**
     * C240/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 7);
    }

    /**
     * C240/7036 (an..35)
     */
    public function characteristic(): string
    {
        return $this->component(3, 7);
    }

    /**
     * C240/7036 (an..35)
     */
    public function characteristic2(): string
    {
        return $this->component(4, 7);
    }
}
