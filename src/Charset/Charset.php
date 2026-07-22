<?php

declare(strict_types=1);

namespace EdifactParser\Charset;

use function mb_convert_encoding;
use function strtoupper;

/**
 * Maps EDIFACT syntax identifiers (UNB element S001) to character encodings and
 * decodes values to UTF-8. The parser reads raw bytes; use this with the
 * interchange's syntax identifier to normalize non-ASCII data.
 */
final class Charset
{
    /** @var array<string, string> */
    private const ENCODINGS = [
        'UNOA' => 'ASCII',        // level A: restricted ASCII
        'UNOB' => 'ASCII',        // level B: ASCII
        'UNOC' => 'ISO-8859-1',   // Latin-1 (Western European)
        'UNOD' => 'ISO-8859-2',   // Latin-2 (Central European)
        'UNOE' => 'ISO-8859-5',   // Cyrillic
        'UNOF' => 'ISO-8859-7',   // Greek
        'UNOG' => 'ISO-8859-3',   // Latin-3
        'UNOH' => 'ISO-8859-4',   // Latin-4
        'UNOI' => 'ISO-8859-6',   // Arabic
        'UNOJ' => 'ISO-8859-8',   // Hebrew
        'UNOK' => 'ISO-8859-9',   // Latin-5 (Turkish)
        'UNOY' => 'UTF-8',        // UTF-8
    ];

    private function __construct()
    {
    }

    /**
     * PHP encoding name for a syntax identifier; UTF-8 for unknown identifiers.
     *
     * @psalm-pure
     */
    public static function encodingFor(string $syntaxIdentifier): string
    {
        return self::ENCODINGS[strtoupper($syntaxIdentifier)] ?? 'UTF-8';
    }

    /**
     * Decode a value from the encoding of the given syntax identifier to UTF-8.
     */
    public static function toUtf8(string $value, string $syntaxIdentifier): string
    {
        $encoding = self::encodingFor($syntaxIdentifier);

        if ($encoding === 'UTF-8') {
            return $value;
        }

        // $encoding always comes from the map above, so the conversion cannot fail.
        return mb_convert_encoding($value, 'UTF-8', $encoding);
    }
}
