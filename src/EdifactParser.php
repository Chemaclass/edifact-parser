<?php

declare(strict_types=1);

namespace EdifactParser;

use EDI\Parser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\Segments\CustomSegmentFactoryInterface;
use EdifactParser\Segments\SegmentFactory;

final class EdifactParser
{
    private ?CustomSegmentFactoryInterface $customSegmentsFactory;

    public static function create(?CustomSegmentFactoryInterface $customSegmentsFactory = null): self
    {
        return new self($customSegmentsFactory);
    }

    public function __construct(?CustomSegmentFactoryInterface $customSegmentsFactory = null)
    {
        $this->customSegmentsFactory = $customSegmentsFactory;
    }

    public function parse(string $fileContent): TransactionResult
    {
        $parser = new Parser($fileContent);
        $errors = $parser->errors();

        if ($errors) {
            throw InvalidFile::withErrors($errors);
        }

        $factory = new SegmentFactory($this->customSegmentsFactory);
        $segmentedValues = (new SegmentedValues($factory))->fromRaw($parser->get());

        return TransactionResult::fromSegmentedValues($segmentedValues);
    }
}
