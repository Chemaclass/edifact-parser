<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * partiesToInstruction — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class INPPartiesToInstruction extends AbstractSegment
{
    public function tag(): string
    {
        return 'INP';
    }

    /**
     * C849/3301 (an..17)
     */
    public function partyEnactingInstructionIdentification(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C849/3285 (an..17)
     */
    public function recipientOfTheInstructionIdentification(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C522/4403 (an..3)
     */
    public function instructionQualifier(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C522/4401 (an..3)
     */
    public function instructionCoded(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C522/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C522/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C522/4400 (an..35)
     */
    public function instruction(): string
    {
        return $this->component(4, 2);
    }

    /**
     * C850/4405 (an..3)
     */
    public function statusCoded(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C850/3036 (an..35)
     */
    public function partyName(): string
    {
        return $this->component(1, 3);
    }

    /**
     * 1229 (an..3)
     */
    public function actionRequestnotificationCoded(): string
    {
        return $this->element(4);
    }
}
