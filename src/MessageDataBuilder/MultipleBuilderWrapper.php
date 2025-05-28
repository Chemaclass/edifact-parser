<?php

declare(strict_types=1);

namespace EdifactParser\MessageDataBuilder;

trait MultipleBuilderWrapper
{
    /** @var array<string|int,BuilderInterface> */
    private array $builders = [];

    private BuilderInterface $currentBuilder;

    private function setCurrentBuilder(BuilderInterface $builder, string|int|null $index = null): void
    {
        $this->currentBuilder = $builder;

        if ($index === null) {
            $this->builders[] = $builder;
        } else {
            $this->builders[$index] = $builder; /** @psalm-suppress InvalidPropertyAssignmentValue */
        }
    }
}
