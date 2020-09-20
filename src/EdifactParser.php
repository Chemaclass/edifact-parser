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

    public static function createWithDefaultSegments(): self
    {
        return new self(SegmentFactory::withDefaultSegments());
    }

    public function __construct(SegmentFactoryInterface $segmentFactory)
    {
        $this->segmentFactory = $segmentFactory;
    }

    /** @codeCoverageIgnore */
    private function __clone()
    {
    }

    /** @return TransactionMessage[] */
    public function parse(string $fileContent): array
    {
        $parser = new Parser($fileContent);
        $errors = $parser->errors();

        if ($errors) {
            throw InvalidFile::withErrors($errors);
        }

        $segmentList = new SegmentList($this->segmentFactory);
        $segments = $segmentList->fromRaw($parser->get());

        return TransactionMessage::groupSegmentsByMessage(...$segments);
    }
}
