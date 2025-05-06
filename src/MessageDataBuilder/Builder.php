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

    private const SUMMARY_TAGS = ['UNS', 'CNT', 'UNT'];

    public function __construct()
    {
        $this->setCurrentBuilder(new SimpleBuilder());
    }

    public function addSegment(SegmentInterface $segment): self
    {
        $this->updateState($segment);

        // Always add summary segments to the main builder
        if (in_array($segment->tag(), self::SUMMARY_TAGS)) {
            $this->builders[0]->addSegment($segment);
            return $this;
        }

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
        return $segment instanceof UNSSectionControl
            && $segment->indicatesEndOfDetailsSection();
    }
}
