<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * temperature — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class TMPTemperature extends AbstractSegment
{
    public function tag(): string
    {
        return 'TMP';
    }

    /**
     * 6245 (an..3)
     */
    public function temperatureQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C239/6246 (n)
     */
    public function temperatureSetting(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C239/6411 (an..3)
     */
    public function measureUnitQualifier(): string
    {
        return $this->component(1, 2);
    }
}
