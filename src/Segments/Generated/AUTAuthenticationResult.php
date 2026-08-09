<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * authenticationResult — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class AUTAuthenticationResult extends AbstractSegment
{
    public function tag(): string
    {
        return 'AUT';
    }

    /**
     * 9280 (an..35)
     */
    public function validationResult(): string
    {
        return $this->element(1);
    }

    /**
     * 9282 (an..35)
     */
    public function validationKeyIdentification(): string
    {
        return $this->element(2);
    }
}
