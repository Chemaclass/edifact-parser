<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

use function class_implements;
use function in_array;
use function is_string;
use function strlen;

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
     * @var array<string,string>
     */
    private array $segments;

    /**
     * @param  array<string, string>  $segments
     */
    private function __construct(array $segments)
    {
        foreach ($segments as $tag => $class) {
            Assert::length($tag, self::TAG_LENGTH, "Segment tag '{$tag}' must be " . self::TAG_LENGTH . ' chars');
            if (!$this->isSegment($class)) {
                throw new InvalidArgumentException("'{$class}' must implement 'SegmentInterface'");
            }
        }
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
        return new self(self::DEFAULT_SEGMENTS);
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
        return new self($segments + self::DEFAULT_SEGMENTS);
    }

    public function createSegmentFromArray(array $rawArray): SegmentInterface
    {
        $tag = $rawArray[0] ?? null;

        if (!is_string($tag) || strlen($tag) !== self::TAG_LENGTH) {
            return new UnknownSegment($rawArray);
        }

        $className = $this->segments[$tag] ?? '';

        if (empty($className)) {
            return new UnknownSegment($rawArray);
        }

        $segment = new $className($rawArray);
        Assert::isInstanceOf($segment, SegmentInterface::class);

        return $segment;
    }

    private function isSegment(string $className): bool
    {
        // The '@' suppresses the "class does not exist" warning for an unknown class,
        // which class_implements() reports as `false` — handled below.
        $interfaces = @class_implements($className);

        return $interfaces !== false && in_array(SegmentInterface::class, $interfaces, true);
    }
}
