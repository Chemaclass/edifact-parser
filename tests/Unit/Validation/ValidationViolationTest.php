<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Validation;

use EdifactParser\Validation\ValidationViolation;
use PHPUnit\Framework\TestCase;

final class ValidationViolationTest extends TestCase
{
    /**
     * @test
     */
    public function exposes_its_tag_rule_and_message(): void
    {
        $violation = new ValidationViolation('BGM', 'required', 'BGM is mandatory');

        self::assertSame('BGM', $violation->segmentTag());
        self::assertSame('required', $violation->rule());
        self::assertSame('BGM is mandatory', $violation->message());
    }
}
