<?php

declare(strict_types=1);

namespace EdifactParser\Directory;

use InvalidArgumentException;
use XMLReader;

use function is_dir;
use function is_file;

/**
 * Reads a directory from the UNTDID XML published by `php-edifact/edifact-mapping`
 * (`segments.xml` + `codes.xml` per directory), or from any folder with the same layout.
 *
 * Both files are parsed with XMLReader on first use and cached — `codes.xml` alone is
 * ~1.5 MB per directory, so nothing is read until something asks for it, and nothing is
 * read twice.
 */
final class XmlDirectory implements DirectoryInterface
{
    /** @var array<string, SegmentDefinition>|null */
    private ?array $segments = null;

    /** @var array<string, array<string, string>>|null */
    private ?array $codes = null;

    private function __construct(
        private string $name,
        private string $path,
    ) {
    }

    /**
     * @param string $path Folder holding `segments.xml` and optionally `codes.xml`
     */
    public static function fromPath(string $name, string $path): self
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException("Directory path '{$path}' does not exist");
        }

        return new self($name, rtrim($path, '/'));
    }

    /**
     * Locate a directory inside an installed `php-edifact/edifact-mapping`, or any root
     * laid out the same way. Returns null when the data is not available, since the
     * package is optional and the parser must work without it.
     */
    public static function locate(string $name, ?string $root = null): ?self
    {
        foreach (self::candidateRoots($root) as $candidate) {
            $path = $candidate . '/' . $name;

            if (is_file($path . '/segments.xml')) {
                return new self($name, $path);
            }
        }

        return null;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function segment(string $tag): ?SegmentDefinition
    {
        return $this->segments()[$tag] ?? null;
    }

    /**
     * Every tag this directory defines.
     *
     * @return list<string>
     */
    public function tags(): array
    {
        return array_keys($this->segments());
    }

    public function codesFor(string $dataElementId): array
    {
        return $this->codes()[$dataElementId] ?? [];
    }

    /**
     * @return array<string, SegmentDefinition>
     */
    private function segments(): array
    {
        if ($this->segments !== null) {
            return $this->segments;
        }

        $reader = new XMLReader();
        $reader->open($this->path . '/segments.xml');

        $segments = [];
        $tag = '';
        $segmentName = '';
        $parts = [];
        $composite = null;

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT) {
                if ($reader->name === 'segment') {
                    $tag = (string) $reader->getAttribute('id');
                    $segmentName = (string) $reader->getAttribute('name');
                    $parts = [];
                    $composite = null;
                    continue;
                }

                if ($reader->name === 'composite_data_element') {
                    $composite = new Composite(
                        (string) $reader->getAttribute('id'),
                        (string) $reader->getAttribute('name'),
                        $reader->getAttribute('required') === 'true',
                        [],
                    );

                    // A self-closing composite has no children to wait for.
                    if ($reader->isEmptyElement) {
                        $parts[] = $composite;
                        $composite = null;
                    }
                    continue;
                }

                if ($reader->name === 'data_element') {
                    $element = self::toDataElement($reader);

                    if ($composite === null) {
                        $parts[] = $element;
                    } else {
                        $composite = $composite->withElement($element);
                    }
                }

                continue;
            }

            if ($reader->nodeType !== XMLReader::END_ELEMENT) {
                continue;
            }

            if ($reader->name === 'composite_data_element' && $composite !== null) {
                $parts[] = $composite;
                $composite = null;
                continue;
            }

            if ($reader->name === 'segment' && $tag !== '') {
                $segments[$tag] = new SegmentDefinition($tag, $segmentName, $parts);
                $tag = '';
            }
        }

        $reader->close();

        return $this->segments = $segments;
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function codes(): array
    {
        if ($this->codes !== null) {
            return $this->codes;
        }

        $file = $this->path . '/codes.xml';

        if (!is_file($file)) {
            return $this->codes = [];
        }

        $reader = new XMLReader();
        $reader->open($file);

        $codes = [];
        $elementId = '';

        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }

            if ($reader->name === 'data_element') {
                $elementId = (string) $reader->getAttribute('id');
                $codes[$elementId] ??= [];
                continue;
            }

            if ($reader->name === 'code' && $elementId !== '') {
                $codes[$elementId][(string) $reader->getAttribute('id')] = (string) $reader->getAttribute('desc');
            }
        }

        $reader->close();

        return $this->codes = $codes;
    }

    private static function toDataElement(XMLReader $reader): DataElement
    {
        $maxLength = $reader->getAttribute('maxlength');

        return new DataElement(
            (string) $reader->getAttribute('id'),
            (string) $reader->getAttribute('name'),
            $reader->getAttribute('required') === 'true',
            $reader->getAttribute('type') ?? 'an',
            $maxLength === null ? null : (int) $maxLength,
        );
    }

    /**
     * @return list<string>
     */
    private static function candidateRoots(?string $root): array
    {
        if ($root !== null) {
            return [rtrim($root, '/')];
        }

        return [
            __DIR__ . '/../../vendor/php-edifact/edifact-mapping/src/Mapping',
            __DIR__ . '/../../../../php-edifact/edifact-mapping/src/Mapping',
        ];
    }
}
