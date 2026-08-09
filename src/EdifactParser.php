<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Exception\InvalidFile;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\SegmentFactoryInterface;
use EdifactParser\Tokenizer\SabasTokenizer;
use EdifactParser\Tokenizer\TokenizerInterface;

final class EdifactParser
{
    private GroupingRules $groupingRules;

    private SegmentList $segmentList;

    private TokenizerInterface $tokenizer;

    public function __construct(
        private SegmentFactoryInterface $segmentFactory,
        ?GroupingRules $groupingRules = null,
        ?TokenizerInterface $tokenizer = null,
    ) {
        $this->groupingRules = $groupingRules ?? GroupingRules::default();
        $this->segmentList = new SegmentList($segmentFactory);
        $this->tokenizer = $tokenizer ?? new SabasTokenizer();
    }

    /**
     * @codeCoverageIgnore
     */
    private function __clone()
    {
    }

    public static function createWithDefaultSegments(
        ?GroupingRules $groupingRules = null,
        ?TokenizerInterface $tokenizer = null,
    ): self {
        return new self(SegmentFactory::withDefaultSegments(), $groupingRules, $tokenizer);
    }

    public function parse(string $fileContent): ParserResult
    {
        $segments = $this->segmentList->fromRaw($this->tokenizer->tokenize($fileContent));

        return TransactionMessage::groupSegments($this->groupingRules, $segments);
    }

    public function parseFile(string $filePath): ParserResult
    {
        if (!is_file($filePath)) {
            throw InvalidFile::withErrors(["File not found: {$filePath}"]);
        }

        $content = @file_get_contents($filePath);
        if ($content === false) {
            throw InvalidFile::withErrors(["Unable to read file: {$filePath}"]);
        }

        return $this->parse($content);
    }
}
