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
        // Kept in one method on purpose: this runs for every segment of every message,
        // so each extra call here is paid hundreds of thousands of times on a big file.
        $inDetailsSection = $this->currentBuilder instanceof DetailsSectionBuilder;

        if (!$inDetailsSection && $segment instanceof LINLineItem) {
            $this->setCurrentBuilder(new DetailsSectionBuilder());
            $inDetailsSection = true;
        }

        if ($inDetailsSection && $this->endsDetailsSection($segment)) {
            $this->setCurrentBuilder(new SimpleBuilder());
        }

        $this->currentBuilder->addSegment($segment);

        return $this;
    }

    /**
     * A message alternates between header/summary sections and detail sections, so a
     * tag can be filled by more than one builder (a DTM before the first LIN and
     * another after UNS). Their maps are merged per tag — last subId wins, like within
     * a single section — instead of the first section shadowing the whole tag.
     *
     * @return array<string, array<array-key, SegmentInterface>>
     */
    public function buildSegments(): array
    {
        $data = [];

        foreach ($this->builders as $builder) {
            if (!$builder instanceof SimpleBuilder) {
                continue;
            }

            foreach ($builder->build() as $tag => $bySubId) {
                $data[$tag] = isset($data[$tag])
                    ? array_replace($data[$tag], $bySubId)
                    : $bySubId;
            }
        }

        return $data;
    }

    /**
     * @return array<array-key, LineItem>
     */
    public function buildLineItems(): array
    {
        return array_map(
            static fn (array $lineItem) => new LineItem($lineItem),
            $this->buildLineItemData(),
        );
    }

    /**
     * The raw line-item maps behind {@see self::buildLineItems()}, for callers that
     * need to post-process the segments before wrapping them.
     *
     * @return array<array-key, array<string, array<array-key, SegmentInterface>>>
     */
    public function buildLineItemData(): array
    {
        $data = [];

        foreach ($this->builders as $builder) {
            if (!$builder instanceof DetailsSectionBuilder) {
                continue;
            }

            $data += $builder->build();
        }

        return $data;
    }

    private function endsDetailsSection(SegmentInterface $segment): bool
    {
        if (!$this->rules->isBreakLineItemTag($segment->tag())) {
            return false;
        }

        // A UNS only closes the details section when it announces the summary section.
        return !($segment instanceof UNSSectionControl) || $segment->indicatesEndOfDetailsSection();
    }
}
