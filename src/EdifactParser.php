<?php

declare(strict_types=1);

namespace EdifactParser;

use EDI\Parser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\Segments\CustomSegmentFactoryInterface;

final class EdifactParser
{
    /** @var null|CustomSegmentFactoryInterface */
    private ?CustomSegmentFactoryInterface $customSegmentsFactory;

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

        $segmentedValues = SegmentedValues::fromRaw($parser->get(), $this->customSegmentsFactory);

        return TransactionResult::fromSegmentedValues($segmentedValues);
    }
}
