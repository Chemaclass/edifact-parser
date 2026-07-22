<?php

declare(strict_types=1);

namespace EdifactParser\MessageDataBuilder;

use EdifactParser\GroupingRules;
use EdifactParser\LineItem;
use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNSSectionControl;

final class Builder
{
    use MultipleBuilderWrapper;

    private GroupingRules $rules;

    public function __construct(?GroupingRules $rules = null)
    {
        $this->rules = $rules ?? GroupingRules::default();
        $this->setCurrentBuilder(new SimpleBuilder());
    }

    public function addSegment(SegmentInterface $segment): self
    {
        $this->updateState($segment);

        $this->currentBuilder->addSegment($segment);
        return $this;
    }

    public function buildSegments(): array
    {
        return $this->buildWhereBuilderIs(SimpleBuilder::class);
    }

    /**
     * @return array<string|int, LineItem>
     */
    public function buildLineItems(): array
    {
        return array_map(
            static fn (array $data) => new LineItem($data),
            $this->buildWhereBuilderIs(DetailsSectionBuilder::class),
        );
    }

    private function updateState(SegmentInterface $segment): void
    {
        if ($this->atStartOfDetailsSection($segment)) {
            $this->setCurrentBuilder(new DetailsSectionBuilder());
        }

        if ($this->atEndOfDetailsSection($segment)) {
            $this->setCurrentBuilder(new SimpleBuilder());
        }
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

        if ($this->rules->isBreakLineItemTag($segment->tag())) {
            if ($segment instanceof UNSSectionControl) {
                return $segment->indicatesEndOfDetailsSection();
            }

            return true;
        }

        return false;
    }
}
