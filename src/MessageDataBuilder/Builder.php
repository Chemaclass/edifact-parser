<?php

declare(strict_types=1);

namespace EdifactParser\MessageDataBuilder;

use EdifactParser\LineItem;
use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNSSectionControl;

use function in_array;

final class Builder
{
    use MultipleBuilderWrapper;

    /**
     * Tags that indicate the end of a line item section.
     * After one of these tags is found all following segments are
     * considered part of the summary until another LIN tag appears.
     */
    private const BREAK_LINEITEM_TAGS = ['UNS', 'CNT', 'UNT'];

    public function __construct()
    {
        $this->setCurrentBuilder(new SimpleBuilder());
    }

    public function addSegment(SegmentInterface $segment): self
    {
        $this->updateState($segment);

        $this->currentBuilder->addSegment($segment);
        return $this;
    }

    public function updateState(SegmentInterface $segment): void
    {
        if ($this->atStartOfDetailsSection($segment)) {
            $this->setCurrentBuilder(new DetailsSectionBuilder());
        }

        if ($this->atEndOfDetailsSection($segment)) {
            $this->setCurrentBuilder(new SimpleBuilder());
        }
    }

    public function buildSegments(): array
    {
        return $this->buildWhereBuilderIs(SimpleBuilder::class);
    }

    /**
     * @returns array<LineItem>
     */
    public function buildLineItems(): array
    {
        return array_map(
            static fn ($data) => new LineItem($data),
            $this->buildWhereBuilderIs(DetailsSectionBuilder::class),
        );
    }

    /**
     * @param class-string<BuilderInterface> $type
     */
    private function buildWhereBuilderIs(string $type): array
    {
        $data = [];

        foreach ($this->builders as $builder) {
            if (!is_a($builder, $type, true)) {
                continue;
            }

            $data += $builder->build();
        }

        return $data;
    }

    private function atStartOfDetailsSection(SegmentInterface $segment): bool
    {
        return $segment instanceof LINLineItem
            && !($this->currentBuilder instanceof DetailsSectionBuilder);
    }

    private function atEndOfDetailsSection(SegmentInterface $segment): bool
    {
        if (!($this->currentBuilder instanceof DetailsSectionBuilder)) {
            return false;
        }

        if (in_array($segment->tag(), self::BREAK_LINEITEM_TAGS, true)) {
            if ($segment instanceof UNSSectionControl) {
                return $segment->indicatesEndOfDetailsSection();
            }

            return true;
        }

        return false;
    }
}
