<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Directory;

use EdifactParser\Diagnostics\Diagnostic;
use EdifactParser\Diagnostics\DiagnosticCode;
use EdifactParser\Directory\Composite;
use EdifactParser\Directory\DataElement;
use EdifactParser\Directory\XmlDirectory;
use EdifactParser\EdifactParser;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\TransactionMessage;
use EdifactParser\Validation\DirectoryValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function count;

/**
 * Uses a small committed directory under tests/fixtures so the suite does not depend on
 * the 150 MB `php-edifact/edifact-mapping` download. One test at the bottom loads the real
 * D96A when the package is installed, which is what proves the format is read correctly.
 */
final class DirectoryValidationTest extends TestCase
{
    private const FIXTURES = __DIR__ . '/../../fixtures/directory';

    /**
     * @test
     */
    public function reads_segment_definitions(): void
    {
        $directory = self::directory();

        self::assertSame('TEST', $directory->name());
        self::assertSame(['QTY', 'RFF', 'TST'], $directory->tags());
        self::assertNull($directory->segment('ZZZ'));

        $qty = $directory->segment('QTY');
        self::assertNotNull($qty);
        self::assertSame('quantity', $qty->name());
        self::assertSame(1, $qty->partCount());

        $composite = $qty->partAt(0);
        self::assertInstanceOf(Composite::class, $composite);
        self::assertSame('C186', $composite->id());
        self::assertSame('quantityDetails', $composite->name());
        self::assertTrue($composite->isRequired());
        self::assertCount(3, $composite->elements());
        self::assertNull($qty->partAt(9));

        $quantity = $composite->elementAt(1);
        self::assertInstanceOf(DataElement::class, $quantity);
        self::assertSame('6060', $quantity->id());
        self::assertSame('quantity', $quantity->name());
        self::assertTrue($quantity->isRequired());
        self::assertSame('n', $quantity->type());
        self::assertSame(15, $quantity->maxLength());
        self::assertNull($composite->elementAt(9));
    }

    /**
     * @test
     */
    public function reads_simple_elements_and_self_closing_composites(): void
    {
        $tst = self::directory()->segment('TST');
        self::assertNotNull($tst);
        self::assertSame(5, $tst->partCount());

        self::assertInstanceOf(DataElement::class, $tst->partAt(0));
        self::assertInstanceOf(Composite::class, $tst->partAt(2));
        // A self-closing composite still becomes a part.
        $selfClosing = $tst->partAt(4);
        self::assertInstanceOf(Composite::class, $selfClosing);
        self::assertSame('C902', $selfClosing->id());
        self::assertSame([], $selfClosing->elements());
    }

    /**
     * @test
     */
    public function reads_code_lists(): void
    {
        $directory = self::directory();

        self::assertSame(['21' => 'Ordered quantity', '12' => 'Despatch quantity'], $directory->codesFor('6063'));
        self::assertSame([], $directory->codesFor('9999'));
        // Cached on the second call.
        self::assertSame($directory->codesFor('6063'), $directory->codesFor('6063'));
    }

    /**
     * @test
     */
    public function a_directory_without_code_lists_reports_none(): void
    {
        self::assertSame([], self::directory('NOCODES')->codesFor('6063'));
    }

    /**
     * @test
     */
    public function a_conforming_segment_produces_nothing(): void
    {
        $message = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21:100:PCE'UNT+3+1'");

        self::assertSame([], (new DirectoryValidator(self::directory()))->validate($message));
        self::assertTrue((new DirectoryValidator(self::directory()))->isValid($message));
    }

    /**
     * @test
     */
    public function a_missing_mandatory_element_is_reported_with_its_path(): void
    {
        $message = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21'UNT+3+1'");

        $diagnostics = (new DirectoryValidator(self::directory()))->validate($message);

        self::assertSame([DiagnosticCode::ELEMENT_REQUIRED], self::codes($diagnostics));
        self::assertSame('QTY', $diagnostics[0]->tag());
        self::assertSame('C186/6060', $diagnostics[0]->elementPath());
        self::assertSame(1, $diagnostics[0]->segmentIndex());
    }

    /**
     * @test
     */
    public function a_missing_mandatory_composite_is_reported(): void
    {
        $message = self::parse("UNH+1+ORDERS:D:96A:UN'TST+abc'UNT+3+1'");

        $diagnostics = (new DirectoryValidator(self::directory()))->validate($message);

        self::assertContains(DiagnosticCode::ELEMENT_REQUIRED, self::codes($diagnostics));
        self::assertContains('C901', array_map(static fn (Diagnostic $d) => $d->elementPath(), $diagnostics));
    }

    /**
     * @test
     */
    public function a_missing_mandatory_simple_element_is_reported(): void
    {
        $message = self::parse("UNH+1+ORDERS:D:96A:UN'TST++++x'UNT+3+1'");

        $paths = array_map(
            static fn (Diagnostic $d) => $d->elementPath(),
            (new DirectoryValidator(self::directory()))->validate($message),
        );

        self::assertContains('9001', $paths);
    }

    /**
     * @test
     */
    public function an_over_long_value_is_reported(): void
    {
        $message = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21:1:TOOLONGUNIT'UNT+3+1'");

        $diagnostics = (new DirectoryValidator(self::directory()))->validate($message);

        self::assertSame([DiagnosticCode::ELEMENT_TOO_LONG], self::codes($diagnostics));
        self::assertSame('C186/6411', $diagnostics[0]->elementPath());
        self::assertStringContainsString('at most 3', $diagnostics[0]->message());
    }

    /**
     * @test
     */
    public function a_value_that_does_not_match_the_representation_is_reported(): void
    {
        $numeric = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21:abc'UNT+3+1'");
        self::assertSame(
            [DiagnosticCode::ELEMENT_TYPE],
            self::codes((new DirectoryValidator(self::directory()))->validate($numeric)),
        );

        // 'a' is alphabetic: digits are not allowed.
        $alphabetic = self::parse("UNH+1+ORDERS:D:96A:UN'TST+ok+ab1++x'UNT+3+1'");
        self::assertContains(
            DiagnosticCode::ELEMENT_TYPE,
            self::codes((new DirectoryValidator(self::directory()))->validate($alphabetic)),
        );
    }

    /**
     * @test
     */
    public function signed_and_decimal_numbers_are_accepted(): void
    {
        foreach (['100', '-100', '1.5', '-0.25', '1,5'] as $value) {
            $message = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21:{$value}'UNT+3+1'");

            self::assertSame(
                [],
                (new DirectoryValidator(self::directory()))->validate($message),
                "expected '{$value}' to be a valid numeric value",
            );
        }
    }

    /**
     * @test
     */
    public function code_validation_is_opt_in(): void
    {
        $message = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+ZZ:100'UNT+3+1'");
        $validator = new DirectoryValidator(self::directory());

        self::assertSame([], $validator->validate($message));

        $diagnostics = $validator->withCodeValidation()->validate($message);
        self::assertSame([DiagnosticCode::CODE_UNKNOWN], self::codes($diagnostics));
        self::assertSame('C186/6063', $diagnostics[0]->elementPath());
        self::assertStringContainsString("'ZZ' is not a listed code", $diagnostics[0]->message());

        self::assertSame([], $validator->withCodeValidation(false)->validate($message));
    }

    /**
     * @test
     */
    public function a_listed_code_passes(): void
    {
        $message = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+12:100'UNT+3+1'");

        self::assertSame([], (new DirectoryValidator(self::directory(), validateCodes: true))->validate($message));
    }

    /**
     * @test
     */
    public function elements_with_no_code_list_are_never_flagged(): void
    {
        // 6060 has no codes.xml entry, so an arbitrary numeric value must stay valid.
        $message = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21:987654'UNT+3+1'");

        self::assertSame([], (new DirectoryValidator(self::directory(), validateCodes: true))->validate($message));
    }

    /**
     * @test
     */
    public function tags_the_directory_does_not_define_are_left_alone(): void
    {
        // UNH/UNT are absent from the fixture directory; unknown tags must not be errors.
        $message = self::parse("UNH+1+ORDERS:D:96A:UN'ZZZ+whatever'UNT+3+1'");

        self::assertSame([], (new DirectoryValidator(self::directory()))->validate($message));
    }

    /**
     * @test
     */
    public function a_composite_supplied_as_a_simple_value_still_validates(): void
    {
        // 'RFF+ON' — one component where a composite is expected.
        $message = self::parse("UNH+1+ORDERS:D:96A:UN'RFF+ON'UNT+3+1'");

        self::assertSame([], (new DirectoryValidator(self::directory()))->validate($message));
    }

    /**
     * @test
     */
    public function a_single_segment_can_be_validated_on_its_own(): void
    {
        $segment = SegmentFactory::withDefaultSegments()->createSegmentFromArray(['QTY', ['21']]);

        $diagnostics = (new DirectoryValidator(self::directory()))->validateSegment($segment);

        self::assertSame([DiagnosticCode::ELEMENT_REQUIRED], self::codes($diagnostics));
        self::assertNull($diagnostics[0]->segmentIndex());
    }

    /**
     * @test
     */
    public function element_definitions_answer_about_type_and_length_directly(): void
    {
        $numeric = new DataElement('6060', 'quantity', true, 'n', 5);

        self::assertTrue($numeric->matchesType(''));          // presence is a separate rule
        self::assertTrue($numeric->matchesType('123'));
        self::assertFalse($numeric->matchesType('12a'));
        self::assertFalse($numeric->exceedsMaxLength('12345'));
        self::assertTrue($numeric->exceedsMaxLength('123456'));

        $unbounded = new DataElement('9999', 'free', false, 'an', null);
        self::assertFalse($unbounded->exceedsMaxLength(str_repeat('x', 500)));
        self::assertTrue($unbounded->matchesType('anything 123'));
    }

    /**
     * @test
     */
    public function a_composite_supplied_where_a_simple_element_is_expected_uses_its_first_component(): void
    {
        // 9001 is a simple element, but the sender sent components. The leading one is
        // the value, so this must validate rather than blow up on the array.
        $message = self::parse("UNH+1+ORDERS:D:96A:UN'TST+ok:extra+++x'UNT+3+1'");

        self::assertSame([], (new DirectoryValidator(self::directory()))->validate($message));
    }

    /**
     * @test
     */
    public function a_segment_definition_reports_its_tag(): void
    {
        self::assertSame('QTY', self::directory()->segment('QTY')?->tag());
    }

    /**
     * @test
     */
    public function non_string_raw_values_are_read_as_strings(): void
    {
        // Raw values can carry ints; the validator must compare them as strings rather
        // than tripping over the type.
        $segment = SegmentFactory::withDefaultSegments()->createSegmentFromArray(['QTY', ['21', 100, 'PCE']]);

        self::assertSame([], (new DirectoryValidator(self::directory()))->validateSegment($segment));
    }

    /**
     * @test
     */
    public function a_missing_directory_path_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        XmlDirectory::fromPath('TEST', self::FIXTURES . '/does-not-exist');
    }

    /**
     * @test
     */
    public function locate_finds_a_directory_under_a_given_root_and_null_otherwise(): void
    {
        self::assertNotNull(XmlDirectory::locate('TEST', self::FIXTURES));
        self::assertNull(XmlDirectory::locate('NOPE', self::FIXTURES));
    }

    /**
     * @test
     */
    public function the_real_untdid_directory_loads_when_the_mapping_package_is_installed(): void
    {
        $directory = XmlDirectory::locate('D96A');

        if ($directory === null) {
            self::markTestSkipped('php-edifact/edifact-mapping is not installed.');
        }

        // This is what proves the reader understands the published format, rather than
        // only the hand-written fixture.
        self::assertGreaterThan(100, count($directory->tags()));

        $qty = $directory->segment('QTY');
        self::assertNotNull($qty);
        $composite = $qty->partAt(0);
        self::assertInstanceOf(Composite::class, $composite);
        self::assertSame('C186', $composite->id());
        self::assertSame('6060', $composite->elementAt(1)?->id());

        self::assertContains('Ordered quantity', $directory->codesFor('6063'));

        $message = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21:100:PCE'UNT+3+1'");
        self::assertSame([], (new DirectoryValidator($directory))->validate($message));
    }

    private static function directory(string $name = 'TEST'): XmlDirectory
    {
        return XmlDirectory::fromPath($name, self::FIXTURES . '/' . $name);
    }

    private static function parse(string $edi): TransactionMessage
    {
        return EdifactParser::createWithDefaultSegments()->parse($edi)->transactionMessages()[0];
    }

    /**
     * @param list<Diagnostic> $diagnostics
     *
     * @return list<string>
     */
    private static function codes(array $diagnostics): array
    {
        return array_map(static fn (Diagnostic $d): string => $d->code(), $diagnostics);
    }
}
