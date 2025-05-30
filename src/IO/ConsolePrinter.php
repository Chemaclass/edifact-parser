<?php

declare(strict_types=1);

namespace EdifactParser\IO;

use EdifactParser\ContextSegment;
use EdifactParser\Segments\NullSegment;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\TransactionMessage;
use function array_key_first;
use function json_encode;
use function sprintf;
use function str_pad;
use function str_repeat;

final class ConsolePrinter implements PrinterInterface
{
    private function __construct(
        /** @var list<string> */
        private array $segmentNames
    ) {
    }

    public static function createWithHeaders(array $segmentNames): self
    {
        return new self($segmentNames);
    }

    public function printMessage(TransactionMessage $message): void
    {
        foreach ($this->segmentNames as $segmentName) {
            $segments = $message->segmentsByTag($segmentName);
            if ($segments === []) {
                continue;
            }
            $this->printSegmentWithContext($segments);
        }
    }

    /**
     * Prints segments and inline context if present.
     *
     * @param  array<string, SegmentInterface>  $segments
     */
    private function printSegmentWithContext(array $segments): void
    {
        $key = array_key_first($segments);
        $first = $segments[$key] ?? new NullSegment();

        echo sprintf("%s:\n", $first->tag());

        foreach ($segments as $segment) {
            $this->printSingleSegmentWithContext($segment);
        }
    }

    /**
     * Handles printing of a segment or context segment inline with its children.
     */
    private function printSingleSegmentWithContext(SegmentInterface $segment): void
    {
        $indent = '  ';
        $subId = $segment->subId();
        $values = json_encode($segment->rawValues(), JSON_THROW_ON_ERROR);

        echo sprintf("%s%s |> %s\n", $indent, str_pad($subId, 3), $values);

        if ($segment instanceof ContextSegment) {
            foreach ($segment->children() as $child) {
                $childIndent = str_repeat($indent, 2);
                echo sprintf(
                    "%s%s |> %s\n",
                    $childIndent,
                    $child->tag(),
                    json_encode($child->rawValues(), JSON_THROW_ON_ERROR)
                );
            }
        }
    }
}
