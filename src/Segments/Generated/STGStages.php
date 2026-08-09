<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * stages — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class STGStages extends AbstractSegment
{
    public function tag(): string
    {
        return 'STG';
    }

    /**
     * 9421 (an..3)
     */
    public function stagesQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * 6426 (n..2)
     */
    public function numberOfStages(): string
    {
        return $this->element(2);
    }

    /**
     * 6428 (n..2)
     */
    public function actualStageCount(): string
    {
        return $this->element(3);
    }
}
