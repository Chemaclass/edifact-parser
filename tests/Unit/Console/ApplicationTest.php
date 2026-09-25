<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Console;

use EdifactParser\Console\Application;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    private const SAMPLE = __DIR__ . '/../../../example/edifact-sample.edi';

    private RecordingOutput $output;

    protected function setUp(): void
    {
        $this->output = new RecordingOutput();
    }

    /**
     * @test
     */
    public function help_is_the_default_and_goes_to_stderr(): void
    {
        $exit = $this->execute([]);

        self::assertSame(Application::EXIT_SUCCESS, $exit);
        // Usage text must never pollute stdout — that channel is for data only.
        self::assertSame([], $this->output->data);
        self::assertStringContainsString('edifact <command>', implode("\n", $this->output->messages));
    }

    /**
     * @test
     */
    public function an_unknown_command_is_a_usage_error(): void
    {
        $exit = $this->execute(['frobnicate']);

        self::assertSame(Application::EXIT_USAGE, $exit);
        self::assertStringContainsString('Unknown command', implode("\n", $this->output->messages));
    }

    /**
     * @test
     */
    public function parse_emits_the_envelope_and_every_message(): void
    {
        $exit = $this->execute(['parse', self::SAMPLE]);

        self::assertSame(Application::EXIT_SUCCESS, $exit);
        self::assertSame(['UNB', 'UNZ'], array_column($this->output->data[0]['globalSegments'], 'tag'));
        self::assertCount(2, $this->output->data[0]['messages']);
        self::assertSame('IFTMIN', $this->output->data[0]['messages'][0]['type']);
        self::assertSame([], $this->output->data[0]['functionalGroups']);
    }

    /**
     * @test
     */
    public function parse_links_functional_groups_to_flat_message_indexes(): void
    {
        $path = $this->fixture(<<<'EDI'
            UNB+UNOC:3+SENDER+RECIPIENT+20191011:1200+REF'
            UNG+ORDERS+S1+R1+20191011:1200+1+UN+D:96A'
            UNH+1+ORDERS:D:96A:UN'
            UNT+2+1'
            UNH+2+ORDERS:D:96A:UN'
            UNT+2+2'
            UNE+2+1'
            UNG+INVOIC+S1+R1+20191011:1200+2+UN+D:96A'
            UNH+3+INVOIC:D:96A:UN'
            UNT+2+3'
            UNE+1+2'
            UNZ+3+REF'
            EDI);

        try {
            self::assertSame(Application::EXIT_SUCCESS, $this->execute(['parse', $path]));

            $data = $this->output->data[0];
            self::assertSame(['UNB', 'UNZ'], array_column($data['globalSegments'], 'tag'));
            self::assertSame(['ORDERS', 'ORDERS', 'INVOIC'], array_column($data['messages'], 'type'));
            self::assertSame([0, 1], $data['functionalGroups'][0]['messageIndexes']);
            self::assertSame('UNG', $data['functionalGroups'][0]['header']['tag']);
            self::assertSame('UNE', $data['functionalGroups'][0]['trailer']['tag']);
            self::assertSame([2], $data['functionalGroups'][1]['messageIndexes']);
        } finally {
            @unlink($path);
        }
    }

    /**
     * @test
     */
    public function inspect_summarises_the_interchange(): void
    {
        $exit = $this->execute(['inspect', self::SAMPLE]);

        self::assertSame(Application::EXIT_SUCCESS, $exit);
        self::assertSame(2, $this->output->data[0]['messageCount']);
        self::assertSame('IFTMIN', $this->output->data[0]['messages'][0]['message_type']);
    }

    /**
     * @test
     */
    public function validate_reports_success_with_exit_zero(): void
    {
        $exit = $this->execute(['validate', self::SAMPLE, '--rules=IFTMIN']);

        self::assertSame(Application::EXIT_SUCCESS, $exit);
        self::assertTrue($this->output->data[0]['valid']);
    }

    /**
     * @test
     */
    public function validate_reports_failure_with_exit_one(): void
    {
        $path = $this->fixture("UNH+1+ORDERS:D:96A:UN'UNT+2+1'");

        try {
            $exit = $this->execute(['validate', $path, '--rules=ORDERS']);

            self::assertSame(Application::EXIT_INVALID, $exit);
            self::assertFalse($this->output->data[0]['valid']);
            self::assertNotEmpty($this->output->data[0]['messages'][0]['diagnostics']);
            self::assertArrayHasKey('code', $this->output->data[0]['messages'][0]['diagnostics'][0]);
        } finally {
            @unlink($path);
        }
    }

    /**
     * @test
     */
    public function validate_picks_the_rule_set_from_the_message_type(): void
    {
        $exit = $this->execute(['validate', self::SAMPLE]);

        self::assertSame(Application::EXIT_SUCCESS, $exit);
        self::assertSame('IFTMIN', $this->output->data[0]['messages'][0]['type']);
    }

    /**
     * @test
     */
    public function validate_skips_message_types_with_no_bundled_rule_set(): void
    {
        $path = $this->fixture("UNH+1+PAXLST:D:96A:UN'UNT+2+1'");

        try {
            $exit = $this->execute(['validate', $path]);

            self::assertSame(Application::EXIT_SUCCESS, $exit);
            self::assertArrayHasKey('skipped', $this->output->data[0]['messages'][0]);
        } finally {
            @unlink($path);
        }
    }

    /**
     * @test
     */
    public function an_unknown_rule_set_is_a_usage_error(): void
    {
        $exit = $this->execute(['validate', self::SAMPLE, '--rules=NOPE']);

        self::assertSame(Application::EXIT_USAGE, $exit);
        self::assertStringContainsString('Unknown rule set', implode("\n", $this->output->messages));
    }

    /**
     * @test
     */
    public function segments_lists_every_registered_tag(): void
    {
        $exit = $this->execute(['segments']);

        self::assertSame(Application::EXIT_SUCCESS, $exit);
        self::assertContains('NAD', $this->output->data[0]['tags']);
    }

    /**
     * @test
     */
    public function segments_describes_one_tag(): void
    {
        $exit = $this->execute(['segments', '--tag=QTY']);

        self::assertSame(Application::EXIT_SUCCESS, $exit);
        self::assertSame('QTY', $this->output->data[0]['tag']);
        self::assertArrayHasKey('quantityAsFloat', $this->output->data[0]['accessors']);
    }

    /**
     * @test
     */
    public function an_unregistered_tag_exits_one(): void
    {
        $exit = $this->execute(['segments', '--tag=ZZZ']);

        self::assertSame(Application::EXIT_INVALID, $exit);
        self::assertStringContainsString('not registered', implode("\n", $this->output->messages));
    }

    /**
     * @test
     */
    public function a_missing_file_is_a_usage_error(): void
    {
        $exit = $this->execute(['parse', '/no/such/file.edi']);

        self::assertSame(Application::EXIT_USAGE, $exit);
        self::assertStringContainsString('Cannot read "/no/such/file.edi"', implode("\n", $this->output->messages));
    }

    /**
     * @test
     */
    public function malformed_input_exits_one_with_diagnostics_on_stderr(): void
    {
        $path = $this->fixture("UNH+1+ORDERS'NAD+BY");

        try {
            $exit = $this->execute(['parse', $path]);

            self::assertSame(Application::EXIT_INVALID, $exit);
            self::assertSame([], $this->output->data);
            self::assertStringContainsString('segment.unterminated', implode("\n", $this->output->messages));
        } finally {
            @unlink($path);
        }
    }

    /**
     * @test
     */
    public function diff_reports_no_differences_with_exit_zero(): void
    {
        $path = $this->fixture("UNH+1+ORDERS:D:96A:UN'QTY+21:100'UNT+3+1'");

        try {
            $exit = $this->execute(['diff', $path, $path]);

            self::assertSame(Application::EXIT_SUCCESS, $exit);
            self::assertTrue($this->output->data[0]['identical']);
            self::assertSame([], $this->output->data[0]['differences']);
        } finally {
            @unlink($path);
        }
    }

    /**
     * @test
     */
    public function diff_reports_differences_with_exit_one(): void
    {
        // Exit 1 on difference mirrors diff(1), so the command composes in a shell.
        $before = $this->fixture("UNH+1+ORDERS:D:96A:UN'QTY+21:100'UNT+3+1'");
        $after = $this->fixture("UNH+1+ORDERS:D:96A:UN'QTY+21:250'UNT+3+1'");

        try {
            $exit = $this->execute(['diff', $before, $after]);

            self::assertSame(Application::EXIT_INVALID, $exit);
            self::assertFalse($this->output->data[0]['identical']);
            self::assertSame('changed', $this->output->data[0]['differences'][0]['kind']);
            self::assertSame('QTY', $this->output->data[0]['differences'][0]['tag']);
        } finally {
            @unlink($before);
            @unlink($after);
        }
    }

    /**
     * @test
     */
    public function diff_needs_two_readable_files(): void
    {
        $path = $this->fixture("UNH+1+ORDERS:D:96A:UN'UNT+2+1'");

        try {
            self::assertSame(Application::EXIT_USAGE, $this->execute(['diff', $path]));
            self::assertSame(Application::EXIT_USAGE, $this->execute(['diff', '/no/such/file.edi', $path]));
            self::assertSame(Application::EXIT_USAGE, $this->execute(['diff', $path, '/no/such/other.edi']));
            self::assertSame([
                'diff needs two files: edifact diff <before.edi> <after.edi>',
                'Cannot read "/no/such/file.edi": no such file, or not readable.',
                'Cannot read "/no/such/other.edi": no such file, or not readable.',
            ], $this->output->messages);
            self::assertSame([], $this->output->data);
        } finally {
            @unlink($path);
        }
    }

    /**
     * @test
     */
    public function pretty_only_changes_formatting(): void
    {
        $this->execute(['segments', '--tag=QTY']);
        $compact = $this->output->data[0];

        $this->output = new RecordingOutput();
        $this->execute(['segments', '--tag=QTY', '--pretty']);

        self::assertSame($compact, $this->output->data[0]);
        self::assertTrue($this->output->pretty[0]);
    }

    /**
     * @test
     */
    public function no_path_and_nothing_piped_is_a_usage_error(): void
    {
        $emptyStdin = fopen('php://memory', 'rb');
        self::assertIsResource($emptyStdin);

        $exit = (new Application($this->output, $emptyStdin))->run(['edifact', 'parse']);

        self::assertSame(Application::EXIT_USAGE, $exit);
        self::assertStringContainsString('No input', implode("\n", $this->output->messages));
    }

    /**
     * @test
     */
    public function version_prints_the_installed_version_as_data(): void
    {
        foreach (['version', '--version', '-V'] as $argument) {
            $this->output = new RecordingOutput();

            self::assertSame(Application::EXIT_SUCCESS, $this->execute([$argument]));
            self::assertSame('chemaclass/edifact-parser', $this->output->data[0]['name']);
            self::assertIsString($this->output->data[0]['version']);
        }
    }

    /**
     * @param list<string> $arguments
     */
    private function execute(array $arguments): int
    {
        return (new Application($this->output))->run(['edifact', ...$arguments]);
    }

    private function fixture(string $content): string
    {
        $path = (string) tempnam(sys_get_temp_dir(), 'edi');
        file_put_contents($path, $content);

        return $path;
    }
}
