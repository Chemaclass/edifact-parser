<?php

declare(strict_types=1);

namespace EdifactParser;

use EDI\Parser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\SegmentFactoryInterface;

final class EdifactParser
{
    public function __construct(private SegmentFactoryInterface $segmentFactory)
    {
    }

    /**
     * @codeCoverageIgnore
     */
    private function __clone()
    {
    }

    public static function createWithDefaultSegments(): self
    {
        return new self(SegmentFactory::withDefaultSegments());
    }

    public function parse(string $fileContent): ParserResult
    {
        $parser = (new Parser())->loadString($fileContent);
        $errors = $parser->errors();

        if ($errors) {
            throw InvalidFile::withErrors($errors);
        }

        $segmentList = new SegmentList($this->segmentFactory);
        $segments = $segmentList->fromRaw($parser->get());

        return TransactionMessage::groupSegmentsByMessage(...$segments);
    }
}
