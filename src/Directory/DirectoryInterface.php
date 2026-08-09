<?php

declare(strict_types=1);

namespace EdifactParser\Directory;

/**
 * A UN/EDIFACT directory (D96A, D24A, …): what each segment looks like, and which codes a
 * data element allows. Implementations are expected to load lazily — a single directory's
 * code lists are megabytes of XML.
 */
interface DirectoryInterface
{
    /**
     * The directory identifier, e.g. 'D96A'.
     */
    public function name(): string;

    public function segment(string $tag): ?SegmentDefinition;

    /**
     * Allowed codes for a data element id, as code => description. Empty when the element
     * is not coded or the directory does not list it.
     *
     * @return array<array-key, string>
     */
    public function codesFor(string $dataElementId): array;
}
