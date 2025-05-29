<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use function is_string;

/** @psalm-immutable */
final class UnknownSegment extends AbstractSegment
{
    public function tag(): string
    {
        return (string) ($this->rawValues[0] ?? '');
    }

    public function subId(): string
    {
        $value = $this->rawValues[1] ?? null;

        if (is_string($value)) {
            return $value;
        }

        if (isset($value[0]) && is_string($value[0])) {
            return $value[0];
        }

        return $this->hashContentsWithMD5();
    }

    private function hashContentsWithMD5(): string
    {
        $encodedValues = json_encode($this->rawValues);

        return $encodedValues === false
            ? md5(self::class)
            : md5($encodedValues);
    }
}
