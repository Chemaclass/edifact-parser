<?php

declare(strict_types=1);

namespace EdifactParser\Diff;

use EdifactParser\ParserResult;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\TransactionMessage;

use function count;
use function max;

/**
 * Compares two interchanges segment by segment.
 *
 * Alignment uses a longest-common-subsequence over segment keys (tag + subId) rather than
 * comparing position by position. That matters: with positional comparison a single
 * inserted segment shifts everything after it and every following segment is reported as
 * changed, which buries the one difference that actually mattered.
 *
 * Segments that align but carry different values are reported as changed; the rest are
 * added or removed.
 */
final class InterchangeDiff
{
    /**
     * @return list<Difference>
     */
    public function diff(ParserResult $before, ParserResult $after): array
    {
        $differences = [];
        $beforeMessages = $before->transactionMessages();
        $afterMessages = $after->transactionMessages();
        $beforeCount = count($beforeMessages);
        $afterCount = count($afterMessages);

        for ($index = 0, $total = max($beforeCount, $afterCount); $index < $total; ++$index) {
            if ($index >= $afterCount) {
                foreach ($beforeMessages[$index]->segments() as $segment) {
                    $differences[] = Difference::removed($index, $segment);
                }
                continue;
            }

            if ($index >= $beforeCount) {
                foreach ($afterMessages[$index]->segments() as $segment) {
                    $differences[] = Difference::added($index, $segment);
                }
                continue;
            }

            foreach ($this->diffMessages($beforeMessages[$index], $afterMessages[$index], $index) as $difference) {
                $differences[] = $difference;
            }
        }

        return $differences;
    }

    public function isIdentical(ParserResult $before, ParserResult $after): bool
    {
        return $this->diff($before, $after) === [];
    }

    /**
     * @return list<Difference>
     */
    public function diffMessages(TransactionMessage $before, TransactionMessage $after, int $messageIndex = 0): array
    {
        $left = $before->segments();
        $right = $after->segments();

        $differences = [];

        foreach ($this->align($left, $right) as [$leftIndex, $rightIndex]) {
            if ($leftIndex !== null && $rightIndex !== null) {
                if ($left[$leftIndex]->rawValues() !== $right[$rightIndex]->rawValues()) {
                    $differences[] = Difference::changed($messageIndex, $left[$leftIndex], $right[$rightIndex]);
                }
                continue;
            }

            if ($rightIndex !== null) {
                $differences[] = Difference::added($messageIndex, $right[$rightIndex]);
                continue;
            }

            if ($leftIndex !== null) {
                $differences[] = Difference::removed($messageIndex, $left[$leftIndex]);
            }
        }

        return $differences;
    }

    /**
     * Pairs up the two segment lists, in order. Each entry is [leftIndex, rightIndex];
     * a null on either side means the segment exists only on the other.
     *
     * @param list<SegmentInterface> $left
     * @param list<SegmentInterface> $right
     *
     * @return list<array{0: int|null, 1: int|null}>
     */
    private function align(array $left, array $right): array
    {
        $leftKeys = self::keys($left);
        $rightKeys = self::keys($right);
        $leftCount = count($leftKeys);
        $rightCount = count($rightKeys);

        // Standard LCS table over segment keys. O(n*m); a diff is a debugging tool run on
        // one interchange, not a hot path.
        $lengths = [];
        for ($i = 0; $i <= $leftCount; ++$i) {
            $lengths[$i] = array_fill(0, $rightCount + 1, 0);
        }

        for ($i = $leftCount - 1; $i >= 0; --$i) {
            for ($j = $rightCount - 1; $j >= 0; --$j) {
                $lengths[$i][$j] = $leftKeys[$i] === $rightKeys[$j]
                    ? $lengths[$i + 1][$j + 1] + 1
                    : max($lengths[$i + 1][$j], $lengths[$i][$j + 1]);
            }
        }

        $pairs = [];
        $i = 0;
        $j = 0;

        while ($i < $leftCount && $j < $rightCount) {
            if ($leftKeys[$i] === $rightKeys[$j]) {
                $pairs[] = [$i, $j];
                ++$i;
                ++$j;
                continue;
            }

            if ($lengths[$i + 1][$j] >= $lengths[$i][$j + 1]) {
                $pairs[] = [$i, null];
                ++$i;
                continue;
            }

            $pairs[] = [null, $j];
            ++$j;
        }

        for (; $i < $leftCount; ++$i) {
            $pairs[] = [$i, null];
        }

        for (; $j < $rightCount; ++$j) {
            $pairs[] = [null, $j];
        }

        return $pairs;
    }

    /**
     * @param list<SegmentInterface> $segments
     *
     * @return list<string>
     */
    private static function keys(array $segments): array
    {
        $keys = [];

        foreach ($segments as $segment) {
            $keys[] = $segment->tag() . ':' . $segment->subId();
        }

        return $keys;
    }
}
