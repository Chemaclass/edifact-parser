<?php

declare(strict_types=1);

namespace EdifactParser\Console;

use EdifactParser\Analysis\MessageAnalyzer;
use EdifactParser\Diagnostics\Diagnostic;
use EdifactParser\EdifactParser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\TransactionMessage;
use EdifactParser\Validation\MessageRuleSets;
use EdifactParser\Validation\MessageValidator;

use function array_slice;
use function count;
use function defined;
use function in_array;
use function sprintf;

/**
 * The `edifact` command. Kept dependency-free on purpose — a parsing library should not
 * drag a console framework into everyone's `vendor/`.
 *
 * Contract, so the output can be consumed by tooling without guessing:
 *  - data goes to stdout, diagnostics to stderr, never interleaved
 *  - JSON is the default; `--pretty` is for humans
 *  - exit 0 = success/valid, 1 = invalid input, 2 = usage error
 */
final class Application
{
    public const EXIT_SUCCESS = 0;

    public const EXIT_INVALID = 1;

    public const EXIT_USAGE = 2;

    /** @var array<string, string> */
    private const COMMANDS = [
        'parse' => 'Print the parsed interchange as JSON',
        'inspect' => 'Summarise an interchange: types, counts, line items',
        'validate' => 'Check messages against a rule set',
        'segments' => 'Show what the parser knows about a segment tag',
        'help' => 'Show this help',
    ];

    /** @var resource|null */
    private $stdin;

    /**
     * @param resource|null $stdin Stream to read when no file argument is given
     */
    public function __construct(
        private OutputInterface $output,
        $stdin = null,
    ) {
        $this->stdin = $stdin ?? (defined('STDIN') ? STDIN : null);
    }

    /**
     * @param list<string> $argv
     */
    public function run(array $argv): int
    {
        $command = $argv[1] ?? 'help';
        $arguments = array_slice($argv, 2);

        if ($command === 'help' || in_array($command, ['-h', '--help'], true)) {
            $this->printUsage();

            return self::EXIT_SUCCESS;
        }

        if (!isset(self::COMMANDS[$command])) {
            $this->output->error(sprintf('Unknown command "%s". Try `edifact help`.', $command));

            return self::EXIT_USAGE;
        }

        try {
            return $this->dispatch($command, $arguments);
        } catch (InvalidFile $e) {
            foreach ($e->getDiagnostics() as $diagnostic) {
                $this->output->error((string) $diagnostic);
            }

            return self::EXIT_INVALID;
        }
    }

    /**
     * @param list<string> $arguments
     */
    private function dispatch(string $command, array $arguments): int
    {
        $options = Options::fromArguments($arguments, $this->stdin);

        if ($command === 'segments') {
            return $this->segments($options);
        }

        $content = $options->readInput();

        if ($content === null) {
            $this->output->error('No input: pass a file path or pipe an interchange on stdin.');

            return self::EXIT_USAGE;
        }

        $result = EdifactParser::createWithDefaultSegments()->parse($content);

        return match ($command) {
            'parse' => $this->parse($result->transactionMessages(), $options),
            'inspect' => $this->inspect($result->transactionMessages(), $options),
            default => $this->validate($result->transactionMessages(), $options),
        };
    }

    /**
     * @param list<TransactionMessage> $messages
     */
    private function parse(array $messages, Options $options): int
    {
        $this->output->data([
            'messages' => array_map(
                static fn (TransactionMessage $message): array => $message->toArray(),
                $messages,
            ),
        ], $options->pretty());

        return self::EXIT_SUCCESS;
    }

    /**
     * @param list<TransactionMessage> $messages
     */
    private function inspect(array $messages, Options $options): int
    {
        $summaries = [];

        foreach ($messages as $message) {
            $summaries[] = (new MessageAnalyzer($message))->getSummary();
        }

        $this->output->data([
            'messageCount' => count($messages),
            'messages' => $summaries,
        ], $options->pretty());

        return self::EXIT_SUCCESS;
    }

    /**
     * @param list<TransactionMessage> $messages
     */
    private function validate(array $messages, Options $options): int
    {
        $ruleSetName = $options->value('rules');
        $ruleSet = $ruleSetName === null ? null : MessageRuleSets::byName($ruleSetName);

        if ($ruleSetName !== null && $ruleSet === null) {
            $this->output->error(sprintf(
                'Unknown rule set "%s". Available: %s.',
                $ruleSetName,
                implode(', ', MessageRuleSets::names()),
            ));

            return self::EXIT_USAGE;
        }

        $validator = new MessageValidator();
        $report = [];
        $valid = true;

        foreach ($messages as $index => $message) {
            $type = $message->messageType();
            $rules = $ruleSet ?? ($type === null ? null : MessageRuleSets::byName($type));

            if ($rules === null) {
                $report[] = ['message' => $index, 'type' => $type, 'skipped' => 'no rule set for this type'];
                continue;
            }

            $diagnostics = $validator->diagnose($message, $rules);
            $valid = $valid && $diagnostics === [];

            $report[] = [
                'message' => $index,
                'type' => $type,
                'valid' => $diagnostics === [],
                'diagnostics' => array_map(
                    static fn (Diagnostic $diagnostic): array => $diagnostic->toArray(),
                    $diagnostics,
                ),
            ];
        }

        $this->output->data(['valid' => $valid, 'messages' => $report], $options->pretty());

        return $valid ? self::EXIT_SUCCESS : self::EXIT_INVALID;
    }

    private function segments(Options $options): int
    {
        $factory = SegmentFactory::withDefaultSegments();
        $tag = $options->value('tag');

        if ($tag === null) {
            $this->output->data(['tags' => $factory->registeredTags()], $options->pretty());

            return self::EXIT_SUCCESS;
        }

        $descriptor = $factory->describeTag($tag);

        if ($descriptor === null) {
            $this->output->error(sprintf('Tag "%s" is not registered; it would parse as an UnknownSegment.', $tag));

            return self::EXIT_INVALID;
        }

        $this->output->data($descriptor->toArray(), $options->pretty());

        return self::EXIT_SUCCESS;
    }

    private function printUsage(): void
    {
        $lines = [
            'edifact — inspect, validate and convert UN/EDIFACT interchanges',
            '',
            'USAGE',
            '  edifact <command> [file] [options]',
            '',
            'COMMANDS',
        ];

        foreach (self::COMMANDS as $name => $description) {
            $lines[] = sprintf('  %-10s %s', $name, $description);
        }

        $lines[] = '';
        $lines[] = 'OPTIONS';
        $lines[] = '  --pretty        Pretty-print the JSON';
        $lines[] = '  --rules=NAME    Rule set for `validate` (' . implode(', ', MessageRuleSets::names()) . ')';
        $lines[] = '  --tag=TAG       Segment tag for `segments`';
        $lines[] = '';
        $lines[] = 'Reads stdin when no file is given. Data on stdout, diagnostics on stderr.';
        $lines[] = 'Exit codes: 0 success, 1 invalid input, 2 usage error.';

        $this->output->info(implode("\n", $lines));
    }
}
