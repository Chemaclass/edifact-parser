<?php

declare(strict_types=1);

use EdifactParser\Console\Application;
use EdifactParser\Console\OutputInterface;

require __DIR__ . '/../vendor/autoload.php';

$input = fopen('php://memory', 'w+');
\assert($input !== false);
fwrite($input, <<<'EDI'
    UNB+UNOC:3+SENDER+RECIPIENT+20191011:1200+REF'
    UNG+ORDERS+S1+R1+20191011:1200+1+UN+D:96A'
    UNH+1+ORDERS:D:96A:UN'
    UNT+2+1'
    UNE+1+1'
    UNZ+1+REF'
    EDI);
rewind($input);

$output = new class() implements OutputInterface {
    /** @var array<string, mixed> */
    public array $result = [];

    public function data(array $data, bool $pretty = false): void
    {
        $this->result = $data;
    }

    public function info(string $message): void
    {
    }

    public function error(string $message): void
    {
    }
};

\assert((new Application($output, $input))->run(['edifact', 'parse']) === Application::EXIT_SUCCESS);
fclose($input);

\assert($output->result === [
    'globalSegments' => [
        ['tag' => 'UNB', 'subId' => 'UNOC', 'rawValues' => ['UNB', ['UNOC', '3'], 'SENDER', 'RECIPIENT', ['20191011', '1200'], 'REF']],
        ['tag' => 'UNZ', 'subId' => '1', 'rawValues' => ['UNZ', '1', 'REF']],
    ],
    'messages' => [[
        'type' => 'ORDERS',
        'segments' => [
            ['tag' => 'UNH', 'subId' => '1', 'rawValues' => ['UNH', '1', ['ORDERS', 'D', '96A', 'UN']]],
            ['tag' => 'UNT', 'subId' => '2', 'rawValues' => ['UNT', '2', '1']],
        ],
    ]],
    'functionalGroups' => [[
        'header' => ['tag' => 'UNG', 'subId' => 'ORDERS', 'rawValues' => ['UNG', 'ORDERS', 'S1', 'R1', ['20191011', '1200'], '1', 'UN', ['D', '96A']]],
        'messageIndexes' => [0],
        'trailer' => ['tag' => 'UNE', 'subId' => '1', 'rawValues' => ['UNE', '1', '1']],
    ]],
]);

echo "docs/llms/cli.md OK\n";
