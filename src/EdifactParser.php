<?php

declare(strict_types=1);

namespace EdifactParser;

use EDI\Parser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\ReadModel\MessageSection;
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

    /** @codeCoverageIgnore */
    private function __clone()
    {
    }

    /** @return MessageSection[] */
    public function parse(string $fileContent): array
    {
        $parser = new Parser($fileContent);
        $errors = $parser->errors();

        if ($errors) {
            throw InvalidFile::withErrors($errors);
        }

        $segments = SegmentedValues::factory($this->segmentFactory)->fromRaw($parser->get());

        return TransactionMessage::fromSegmentedValues(...$segments);
    }
}
