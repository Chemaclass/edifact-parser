<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * language — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class LANLanguage extends AbstractSegment
{
    public function tag(): string
    {
        return 'LAN';
    }

    /**
     * 3455 (an..3)
     */
    public function languageQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C508/3453 (an..3)
     */
    public function languageCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C508/3452 (an..35)
     */
    public function language(): string
    {
        return $this->component(1, 2);
    }
}
