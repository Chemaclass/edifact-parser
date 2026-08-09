<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Console;

use EdifactParser\Console\Options;
use EdifactParser\Console\StreamOutput;
use PHPUnit\Framework\TestCase;

final class OptionsAndOutputTest extends TestCase
{
    /**
     * @test
     */
    public function parses_flags_values_and_a_positional_path(): void
    {
        $options = Options::fromArguments(['file.edi', '--pretty', '--rules=ORDERS']);

        self::assertSame('file.edi', $options->path());
        self::assertTrue($options->has('pretty'));
        self::assertTrue($options->pretty());
        self::assertFalse($options->has('quiet'));
        self::assertSame('ORDERS', $options->value('rules'));
        self::assertNull($options->value('tag'));
    }

    /**
     * @test
     */
    public function reads_a_file_when_a_path_is_given(): void
    {
        $path = (string) tempnam(sys_get_temp_dir(), 'edi');
        file_put_contents($path, "UNH+1+ORDERS'");

        try {
            self::assertSame("UNH+1+ORDERS'", Options::fromArguments([$path])->readInput());
        } finally {
            @unlink($path);
        }
    }

    /**
     * @test
     */
    public function a_missing_path_reads_as_no_input(): void
    {
        self::assertNull(Options::fromArguments(['/no/such/file.edi'])->readInput());
    }

    /**
     * @test
     */
    public function an_unreadable_file_reads_as_no_input(): void
    {
        if (posix_getuid() === 0) {
            self::markTestSkipped('Root can read files regardless of permissions.');
        }

        $path = (string) tempnam(sys_get_temp_dir(), 'edi');
        chmod($path, 0000);

        try {
            self::assertNull(Options::fromArguments([$path])->readInput());
        } finally {
            chmod($path, 0600);
            @unlink($path);
        }
    }

    /**
     * @test
     */
    public function reads_piped_input_when_no_path_is_given(): void
    {
        $stdin = self::streamContaining("UNH+1+ORDERS'");

        self::assertSame("UNH+1+ORDERS'", Options::fromArguments([], $stdin)->readInput());
    }

    /**
     * @test
     */
    public function blank_piped_input_reads_as_no_input(): void
    {
        self::assertNull(Options::fromArguments([], self::streamContaining("  \n "))->readInput());
    }

    /**
     * @test
     */
    public function no_path_and_no_stdin_reads_as_no_input(): void
    {
        self::assertNull(Options::fromArguments([], null)->readInput());
    }

    /**
     * @test
     */
    public function stream_output_keeps_data_and_messages_on_separate_streams(): void
    {
        $stdout = fopen('php://memory', 'r+');
        $stderr = fopen('php://memory', 'r+');
        self::assertIsResource($stdout);
        self::assertIsResource($stderr);

        $output = new StreamOutput($stdout, $stderr);
        $output->data(['tag' => 'NAD']);
        $output->info('some info');
        $output->error('some error');

        rewind($stdout);
        rewind($stderr);

        $out = (string) stream_get_contents($stdout);
        $err = (string) stream_get_contents($stderr);

        self::assertSame(['tag' => 'NAD'], json_decode(trim($out), true));
        self::assertSame("some info\nsome error\n", $err);
    }

    /**
     * @test
     */
    public function stream_output_can_pretty_print(): void
    {
        $stdout = fopen('php://memory', 'r+');
        self::assertIsResource($stdout);

        (new StreamOutput($stdout, $stdout))->data(['tag' => 'NAD'], pretty: true);

        rewind($stdout);
        $out = (string) stream_get_contents($stdout);

        self::assertStringContainsString("\n", trim($out));
        self::assertSame(['tag' => 'NAD'], json_decode(trim($out), true));
    }

    /**
     * @return resource
     */
    private static function streamContaining(string $content)
    {
        $stream = fopen('php://memory', 'r+');
        self::assertIsResource($stream);
        fwrite($stream, $content);
        rewind($stream);

        return $stream;
    }
}
