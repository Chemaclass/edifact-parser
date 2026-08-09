<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * computerEnvironmentDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class CEDComputerEnvironmentDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'CED';
    }

    /**
     * 1501 (an..3)
     */
    public function computerEnvironmentDetailsQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C079/1511 (an..3)
     */
    public function computerEnvironmentCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C079/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C079/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C079/1510 (an..35)
     */
    public function computerEnvironment(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C079/1056 (an..9)
     */
    public function version(): string
    {
        return $this->component(4, 2);
    }

    /**
     * C079/1058 (an..9)
     */
    public function release(): string
    {
        return $this->component(5, 2);
    }

    /**
     * C079/7402 (an..35)
     */
    public function identityNumber(): string
    {
        return $this->component(6, 2);
    }
}
