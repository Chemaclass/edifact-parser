<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

use function in_array;

/**
 * Describes what a segment class can tell you, derived by reflection rather than
 * hand-maintained — so it cannot drift from the code.
 *
 * The point is discoverability: without this, finding out that a `NAD` exposes
 * `countryCode()` means opening the source. With it, a tool (or an agent writing code
 * against this library) can enumerate the accessors instead of guessing method names.
 */
final class SegmentDescriptor
{
    /** Structural methods every segment has; the interesting ones are what remains. */
    private const STRUCTURAL_METHODS = [
        'tag', 'subId', 'parsedSubId', 'rawValues', 'toArray', 'toJson',
        'builder', 'segment', 'children', '__construct', '__toString',
    ];

    /**
     * @param class-string<SegmentInterface> $className
     */
    private function __construct(
        private string $tag,
        private string $className,
        /** @var array<string, string> accessor name => return type */
        private array $accessors,
    ) {
    }

    /**
     * @param class-string<SegmentInterface> $className
     */
    public static function forClass(string $className): self
    {
        $reflection = new ReflectionClass($className);
        $accessors = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic() || in_array($method->getName(), self::STRUCTURAL_METHODS, true)) {
                continue;
            }

            // Only no-argument readers describe data; anything taking parameters is
            // behaviour, not a field.
            if ($method->getNumberOfRequiredParameters() > 0) {
                continue;
            }

            $type = $method->getReturnType();
            $accessors[$method->getName()] = $type instanceof ReflectionNamedType ? $type->getName() : 'mixed';
        }

        ksort($accessors);

        return new self(self::tagFor($className), $className, $accessors);
    }

    public function tag(): string
    {
        return $this->tag;
    }

    /**
     * @return class-string<SegmentInterface>
     */
    public function className(): string
    {
        return $this->className;
    }

    /**
     * Accessor name => declared return type, alphabetically.
     *
     * @return array<string, string>
     */
    public function accessors(): array
    {
        return $this->accessors;
    }

    /**
     * @return array{tag: string, class: string, accessors: array<string, string>}
     */
    public function toArray(): array
    {
        return [
            'tag' => $this->tag,
            'class' => $this->className,
            'accessors' => $this->accessors,
        ];
    }

    /**
     * @param class-string<SegmentInterface> $className
     */
    private static function tagFor(string $className): string
    {
        // Every built-in segment returns a constant from tag(); an empty rawValues array
        // is enough to read it without needing a real interchange.
        return (new $className([]))->tag();
    }
}
