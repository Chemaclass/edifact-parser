<?php

declare(strict_types=1);

namespace EdifactParser;

use EDI\Parser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\SegmentFactoryInterface;

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

    public function parse(string $fileContent): TransactionResult
    {
        $parser = new Parser($fileContent);
        $errors = $parser->errors();

        if ($errors) {
            throw InvalidFile::withErrors($errors);
        }

        $segmentedValues = (new SegmentedValues($this->segmentFactory))->fromRaw($parser->get());

        return TransactionResult::fromSegmentedValues($segmentedValues);
    }
}
