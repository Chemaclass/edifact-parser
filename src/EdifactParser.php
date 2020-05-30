<?php

declare(strict_types=1);

namespace EdifactParser;

use EDI\Parser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\SegmentFactoryInterface;

/** @psalm-immutable */
final class EdifactParser
{
    private SegmentFactoryInterface $segmentFactory;

    public static function create(?SegmentFactoryInterface $segmentFactory = null): self
    {
        return new self($segmentFactory ?? new SegmentFactory());
    }

    private function __construct(SegmentFactoryInterface $segmentFactory)
    {
        $this->segmentFactory = $segmentFactory;
    }

    private function __clone()
    {
    }

    public function __invoke(string $fileContent): array
    {
        return $this->parse($fileContent);
    }

    /** @psalm-return list<TransactionMessage> */
    public function parse(string $fileContent): array
    {
        $parser = new Parser($fileContent);
        $errors = $parser->errors();

        if ($errors) {
            throw InvalidFile::withErrors($errors);
        }

        return TransactionResult::fromSegmentedValues(
            ...SegmentedValues::factory($this->segmentFactory)->fromRaw($parser->get())
        );
    }
}
