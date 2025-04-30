<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use function is_string;

/** @psalm-immutable */
final class UnknownSegment extends AbstractSegment
{
    public function tag(): string
    {
        return $this->rawValues[0];
    }

    public function subId(): string
    {
        if (is_string($this->rawValues[1])) {
            return $this->rawValues[1];
        }

        if (is_string($this->rawValues[1][0])) {
            return $this->rawValues[1][0];
        }

        return $this->hashContentsWithMD5();
    }

    private function hashContentsWithMD5(): string
    {
        $encodedValues = json_encode($this->rawValues);
        return ($encodedValues) ? md5($encodedValues) : md5(self::class);
    }
}
