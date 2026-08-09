<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * priceItemLine — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class PITPriceItemLine extends AbstractSegment
{
    public function tag(): string
    {
        return 'PIT';
    }

    /**
     * 1082 (n..6)
     */
    public function lineItemNumber(): string
    {
        return $this->element(1);
    }

    /**
     * 1229 (an..3)
     */
    public function actionRequestnotificationCoded(): string
    {
        return $this->element(2);
    }

    /**
     * C292/5377 (an..3)
     */
    public function priceChangeIndicatorCoded(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C292/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C292/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 3);
    }

    /**
     * 7011 (an..3)
     */
    public function articleAvailabilityCoded(): string
    {
        return $this->element(4);
    }

    /**
     * 5495 (an..3)
     */
    public function sublineIndicatorCoded(): string
    {
        return $this->element(5);
    }

    /**
     * 1222 (n..2)
     */
    public function configurationLevel(): string
    {
        return $this->element(6);
    }

    /**
     * 7083 (an..3)
     */
    public function configurationCoded(): string
    {
        return $this->element(7);
    }
}
