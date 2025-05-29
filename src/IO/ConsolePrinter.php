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
            $render = $message->segmentsByTag($segmentName);
            if ($render === []) {
                continue;
            }
            $this->printSegment($render);
        }

        // Print context segments inline after standard segments
        $contexts = $message->contextSegments();
        if ($contexts !== []) {
            echo "Context segments:\n";
            foreach ($contexts as $context) {
                $this->printContextSegment($context);
            }
        }
    }

    /**
     * @phpstan-impure
     *
     * @param  array<string,SegmentInterface>  $segments
     */
    private function printSegment(array $segments): void
    {
        $key = array_key_first($segments);
        $first = $segments[$key] ?? new NullSegment();

        echo sprintf("> %s:\n", $first->tag());

        foreach ($segments as $segment) {
            echo sprintf(
                "    %s |> %s \n",
                str_pad($segment->subId(), 3),
                json_encode($segment->rawValues(), JSON_THROW_ON_ERROR)
            );
        }
    }

    /**
     * Recursively print a ContextSegment and its children.
     */
    private function printContextSegment(ContextSegment $context, int $indentLevel = 0): void
    {
        $indent = str_repeat('    ', $indentLevel);
        $segment = $context->segment();

        echo sprintf("%s> %s %s\n", $indent, $segment->tag(), $segment->subId());

        foreach ($context->children() as $child) {
            if ($child instanceof SegmentInterface) {
                echo sprintf(
                    "%s    - %s |> %s\n",
                    $indent,
                    $child->tag(),
                    json_encode($child->rawValues(), JSON_THROW_ON_ERROR)
                );
            } else {
                $this->printContextSegment($child, $indentLevel + 1);
            }
        }
    }
}
