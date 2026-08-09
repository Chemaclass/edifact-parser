<?php

declare(strict_types=1);

namespace EdifactParser\Diagnostics;

/**
 * The stable identifiers a {@see Diagnostic} can carry.
 *
 * These are **public API**: match on them rather than on message text, which is free to
 * change. Values are dotted, lowercase, and grouped by the stage that raises them.
 */
final class DiagnosticCode
{
    /** Tokenizing: the input ran out before a segment terminator. */
    public const SEGMENT_UNTERMINATED = 'segment.unterminated';

    /** Tokenizing: a byte outside the repertoire the interchange declares. */
    public const CHARACTER_NOT_PERMITTED = 'character.not-permitted';

    /** Tokenizing: a release character before something that is not a delimiter. */
    public const RELEASE_INVALID = 'release.invalid';

    /** Tokenizing: anything the underlying tokenizer reported without a mapped code. */
    public const TOKENIZE_FAILED = 'tokenize.failed';

    /** Validation: a mandatory data element or composite is empty. */
    public const ELEMENT_REQUIRED = 'element.required';

    /** Validation: a value is longer than the directory allows. */
    public const ELEMENT_TOO_LONG = 'element.too-long';

    /** Validation: a value does not match the element's representation (`n`, `a`, `an`). */
    public const ELEMENT_TYPE = 'element.type';

    /** Validation: a coded value is not in the directory's code list for that element. */
    public const CODE_UNKNOWN = 'code.unknown';

    /** Validation: a required segment is absent. */
    public const SEGMENT_REQUIRED = 'segment.required';

    /** Validation: a segment occurs too few or too many times. */
    public const SEGMENT_CARDINALITY = 'segment.cardinality';

    /** Validation: segments appear in an order the rule set forbids. */
    public const SEGMENT_SEQUENCE = 'segment.sequence';

    /**
     * @codeCoverageIgnore Prevents instantiation of this constants holder
     */
    private function __construct()
    {
    }
}
