<?php

declare(strict_types=1);

/**
 * Generates typed segment classes from a UN/EDIFACT directory.
 *
 *   php tools/generate-segments.php [directory] [--force]
 *
 * Reads the directory through Directory\XmlDirectory (so it needs
 * php-edifact/edifact-mapping installed) and writes one class per tag into
 * src/Segments/Generated/. Tags that already have a hand-written class in
 * SegmentFactory::DEFAULT_SEGMENTS are skipped: those have better accessor names than the
 * official element names, and their signatures are public API.
 *
 * The output is committed, so the library needs no directory data at runtime and no
 * generation step at install time. Re-run this when adopting a newer directory.
 */

namespace EdifactParser\Tools;

use EdifactParser\Directory\Composite;
use EdifactParser\Directory\DataElement;
use EdifactParser\Directory\SegmentDefinition;
use EdifactParser\Directory\XmlDirectory;
use EdifactParser\Segments\SegmentFactory;

use function count;
use function in_array;
use function sprintf;

require __DIR__ . '/../vendor/autoload.php';

const OUTPUT_DIR = __DIR__ . '/../src/Segments/Generated';
const NAMESPACE_NAME = 'EdifactParser\\Segments\\Generated';

/** Methods on AbstractSegment that a generated accessor must never shadow. */
const RESERVED = [
    'tag', 'subid', 'parsedsubid', 'rawvalues', 'toarray', 'tojson',
    'element', 'component', 'firstcomponent', 'requiredsubid', 'builder',
];

$directoryName = $argv[1] ?? 'D96A';
$directory = XmlDirectory::locate($directoryName);

if ($directory === null) {
    fwrite(STDERR, "Directory {$directoryName} not found. Install php-edifact/edifact-mapping.\n");
    exit(2);
}

$handWritten = array_keys(SegmentFactory::DEFAULT_SEGMENTS);
$written = [];
$map = [];

foreach ($directory->tags() as $tag) {
    if (in_array($tag, $handWritten, true)) {
        continue;
    }

    $definition = $directory->segment($tag);

    if ($definition === null || !preg_match('/^[A-Z][A-Z0-9]{2}$/', $tag)) {
        continue;
    }

    $className = $tag . studly($definition->name());
    file_put_contents(OUTPUT_DIR . '/' . $className . '.php', renderClass($className, $definition, $directoryName));

    $written[] = $className;
    $map[$tag] = $className;
}

ksort($map);
file_put_contents(OUTPUT_DIR . '/../GeneratedSegments.php', renderMap($map, $directoryName));

printf("Generated %d classes from %s into src/Segments/Generated\n", count($written), $directoryName);

function renderClass(string $className, SegmentDefinition $definition, string $directoryName): string
{
    $namespace = NAMESPACE_NAME;
    $accessors = [];
    $used = RESERVED;

    foreach ($definition->parts() as $position => $part) {
        // rawValues[0] is the tag, so part 0 is element 1.
        $elementIndex = $position + 1;

        if ($part instanceof Composite) {
            foreach ($part->elements() as $componentIndex => $element) {
                // An element carrying a single value round-trips as a plain string rather
                // than a one-element list, so the leading component must handle both
                // shapes — that is exactly what firstComponent() is for.
                $expression = $componentIndex === 0
                    ? sprintf('$this->firstComponent(%d)', $elementIndex)
                    : sprintf('$this->component(%d, %d)', $componentIndex, $elementIndex);

                $accessors[] = renderAccessor(
                    uniqueName($element->name(), $used),
                    $expression,
                    $element,
                    $part->id(),
                );
            }
            continue;
        }

        $accessors[] = renderAccessor(
            uniqueName($part->name(), $used),
            sprintf('$this->element(%d)', $elementIndex),
            $part,
            null,
        );
    }

    $body = $accessors === []
        ? ''
        : "\n" . implode("\n", $accessors);

    return <<<PHP
        <?php

        declare(strict_types=1);

        namespace {$namespace};

        use EdifactParser\\Segments\\AbstractSegment;

        /**
         * {$definition->name()} — generated from UN/EDIFACT {$directoryName}.
         *
         * Do not edit by hand: run `php tools/generate-segments.php {$directoryName}` instead.
         *
         * @psalm-immutable
         */
        final class {$className} extends AbstractSegment
        {
            public function tag(): string
            {
                return '{$definition->tag()}';
            }
        {$body}}

        PHP;
}

function renderAccessor(string $name, string $expression, DataElement $element, ?string $compositeId): string
{
    $path = $compositeId === null ? $element->id() : $compositeId . '/' . $element->id();
    $length = $element->maxLength() === null ? '' : '..' . $element->maxLength();

    return <<<PHP

            /**
             * {$path} ({$element->type()}{$length})
             */
            public function {$name}(): string
            {
                return {$expression};
            }

        PHP;
}

/**
 * @param list<string> $used
 */
function uniqueName(string $name, array &$used): string
{
    $base = lcfirst(studly($name));
    $base = $base === '' ? 'value' : $base;

    $candidate = $base;
    $suffix = 1;

    while (in_array(strtolower($candidate), $used, true)) {
        ++$suffix;
        $candidate = $base . $suffix;
    }

    $used[] = strtolower($candidate);

    return $candidate;
}

function studly(string $value): string
{
    $value = preg_replace('/[^a-zA-Z0-9]+/', ' ', $value) ?? '';

    return str_replace(' ', '', ucwords(trim($value)));
}

/**
 * @param array<string, string> $map
 */
function renderMap(array $map, string $directoryName): string
{
    $entries = '';

    foreach ($map as $tag => $className) {
        $entries .= sprintf("        '%s' => Generated\\%s::class,\n", $tag, $className);
    }

    return <<<PHP
        <?php

        declare(strict_types=1);

        namespace EdifactParser\\Segments;

        /**
         * Segment classes generated from UN/EDIFACT {$directoryName} for the tags that have no
         * hand-written class. Compose with {@see SegmentFactory::DEFAULT_SEGMENTS}, or use
         * {@see SegmentFactory::withDirectorySegments()}.
         *
         * Do not edit by hand: run `php tools/generate-segments.php {$directoryName}` instead.
         */
        final class GeneratedSegments
        {
            /** @var array<string,string> */
            public const SEGMENTS = [
        {$entries}    ];

            /**
             * @codeCoverageIgnore Prevents instantiation of this constants holder
             */
            private function __construct()
            {
            }
        }

        PHP;
}
