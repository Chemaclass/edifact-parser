<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

use function array_keys;
use function array_map;
use function is_a;
use function is_string;
use function sort;

/** @psalm-immutable */
final class SegmentFactory implements SegmentFactoryInterface
{
    /**
     * Service/control segments that frame every interchange (the UN* envelope).
     * Compose them with your own tags for a lean parser that still understands the
     * interchange structure, e.g. `withSegments(ENVELOPE_SEGMENTS + ['NAD' => ...])`.
     *
     * @var array<string,string>
     */
    public const ENVELOPE_SEGMENTS = [
        'UNB' => UNBInterchangeHeader::class,
        'UNG' => UNGFunctionalGroupHeader::class,
        'UNH' => UNHMessageHeader::class,
        'UNS' => UNSSectionControl::class,
        'UNT' => UNTMessageFooter::class,
        'UNE' => UNEFunctionalGroupTrailer::class,
        'UNZ' => UNZInterchangeTrailer::class,
    ];

    /**
     * The business-content segments (header, party/terms, detail and summary) carried
     * inside the envelope. Compose with {@see ENVELOPE_SEGMENTS} for a full parser.
     *
     * @var array<string,string>
     */
    public const BUSINESS_SEGMENTS = [
        'BGM' => BGMBeginningOfMessage::class,
        'DTM' => DTMDateTimePeriod::class,
        'RFF' => RFFReference::class,
        'NAD' => NADNameAddress::class,
        'CTA' => CTAContactInformation::class,
        'COM' => COMCommunicationContact::class,
        'CUX' => CUXCurrencyDetails::class,
        'PAT' => PATPaymentTerms::class,
        'PCD' => PCDPercentageDetails::class,
        'TAX' => TAXDutyTaxFee::class,
        'TOD' => TODTermsOfDelivery::class,
        'TDT' => TDTTransportDetails::class,
        'LOC' => LOCPlace::class,
        'FTX' => FTXFreeText::class,
        'LIN' => LINLineItem::class,
        'PIA' => PIAAdditionalProductId::class,
        'IMD' => IMDItemDescription::class,
        'QTY' => QTYQuantity::class,
        'PRI' => PRIPrice::class,
        'MEA' => MEADimensions::class,
        'PAC' => PACPackage::class,
        'GID' => GIDGoodsItemDetails::class,
        'MOA' => MOAMonetaryAmount::class,
        'PCI' => PCIPackageId::class,
        'CNT' => CNTControl::class,
    ];

    /**
     * The full set of segments typed and registered out of the box: the envelope
     * plus all business content.
     *
     * @var array<string,string>
     */
    public const DEFAULT_SEGMENTS = self::ENVELOPE_SEGMENTS + self::BUSINESS_SEGMENTS;

    private const TAG_LENGTH = 3;

    /**
     * The list of "segment class names" for every segment that might be created.
     *
     * @var array<array-key, class-string<SegmentInterface>>
     */
    private array $segments;

    /**
     * @param  array<string, string>  $segments
     * @param  bool  $validate  skipped for {@see self::DEFAULT_SEGMENTS}, whose classes ship
     *                          with the library: validating them would autoload every segment
     *                          class up front, even the ones a given interchange never uses
     */
    private function __construct(array $segments, bool $validate = true)
    {
        if ($validate) {
            self::assertValidSegments($segments);
        }

        /** @var array<array-key, class-string<SegmentInterface>> $segments */
        $this->segments = $segments;
    }

    /**
     * The key: The 'Segment Tag' -> A three-character (alphanumeric) that identifies the segment.
     * The value: The class that will be created once that 'Segment Tag' is found. It must implement
     * the `SegmentInterface` in order to be able to work with the factory, otherwise it will be ignored.
     *
     * @param  array<string,string>  $segments
     */
    public static function withSegments(array $segments): self
    {
        return new self($segments);
    }

    public static function withDefaultSegments(): self
    {
        return new self(self::DEFAULT_SEGMENTS, validate: false);
    }

    /**
     * The defaults plus every remaining segment of a UN/EDIFACT directory, generated from
     * the published definitions — 134 tags instead of 32.
     *
     * The hand-written defaults win: their accessors are better named than the official
     * element names and their signatures are public API. This is opt-in precisely so
     * {@see self::DEFAULT_SEGMENTS} keeps its meaning and its size.
     *
     * Nothing is autoloaded until a tag is actually seen, so the larger map costs nothing
     * to build.
     */
    public static function withDirectorySegments(): self
    {
        return new self(self::DEFAULT_SEGMENTS + GeneratedSegments::SEGMENTS, validate: false);
    }

    /**
     * The default segments plus the given ones. A custom class registered under a
     * default tag overrides the default. Use this to add custom segments without
     * having to re-declare the whole default map.
     *
     * @param  array<string,string>  $segments
     */
    public static function withAdditionalSegments(array $segments): self
    {
        self::assertValidSegments($segments);

        return new self($segments + self::DEFAULT_SEGMENTS, validate: false);
    }

    /**
     * Every tag this factory can build, sorted. Reading the map's keys autoloads nothing,
     * so this stays cheap even with every directory segment registered.
     *
     * @return list<string>
     */
    public function registeredTags(): array
    {
        $tags = array_map('strval', array_keys($this->segments));
        sort($tags);

        return $tags;
    }

    /**
     * The class registered for a tag, or null when the tag would become an
     * {@see UnknownSegment}. Returns the name only — nothing is autoloaded.
     *
     * @return class-string<SegmentInterface>|null
     */
    public function classForTag(string $tag): ?string
    {
        return $this->segments[$tag] ?? null;
    }

    /**
     * What the segment registered for this tag can tell you, or null when the tag is not
     * registered. This one does load the class, since it reflects over it.
     *
     * @psalm-suppress ImpureMethodCall reflection reads the class; the factory is untouched
     */
    public function describeTag(string $tag): ?SegmentDescriptor
    {
        $className = $this->classForTag($tag);

        return $className === null ? null : SegmentDescriptor::forClass($className);
    }

    public function createSegmentFromArray(array $rawArray): SegmentInterface
    {
        $tag = $rawArray[0] ?? null;

        // Every registered tag is exactly TAG_LENGTH chars (enforced on construction),
        // so a miss in the map already covers tags of any other length.
        if (!is_string($tag)) {
            return new UnknownSegment($rawArray);
        }

        $className = $this->segments[$tag] ?? null;

        if ($className === null) {
            return new UnknownSegment($rawArray);
        }

        // Guaranteed to be a SegmentInterface: the class was checked on construction.
        return new $className($rawArray);
    }

    /**
     * A numeric tag ('123') arrives here as an int key — PHP normalizes those — hence
     * `array-key` and the cast below.
     *
     * @param  array<array-key, string>  $segments
     */
    private static function assertValidSegments(array $segments): void
    {
        foreach ($segments as $tag => $class) {
            $tag = (string) $tag;
            Assert::length($tag, self::TAG_LENGTH, "Segment tag '{$tag}' must be " . self::TAG_LENGTH . ' chars');

            if (!is_a($class, SegmentInterface::class, allow_string: true)) {
                throw new InvalidArgumentException("'{$class}' must implement 'SegmentInterface'");
            }
        }
    }
}
