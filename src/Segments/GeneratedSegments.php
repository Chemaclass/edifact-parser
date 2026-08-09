<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/**
 * Segment classes generated from UN/EDIFACT D96A for the tags that have no
 * hand-written class. Compose with {@see SegmentFactory::DEFAULT_SEGMENTS}, or use
 * {@see SegmentFactory::withDirectorySegments()}.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 */
final class GeneratedSegments
{
    /** @var array<string,string> */
    public const SEGMENTS = [
        'ADR' => Generated\ADRAddress::class,
        'AGR' => Generated\AGRAgreementIdentification::class,
        'AJT' => Generated\AJTAdjustmentDetails::class,
        'ALC' => Generated\ALCAllowanceOrCharge::class,
        'ALI' => Generated\ALIAdditionalInformation::class,
        'APR' => Generated\APRAdditionalPriceInformation::class,
        'ARD' => Generated\ARDAmountsRelationshipDetails::class,
        'ARR' => Generated\ARRArrayInformation::class,
        'ASI' => Generated\ASIArrayStructureIdentification::class,
        'ATT' => Generated\ATTAttribute::class,
        'AUT' => Generated\AUTAuthenticationResult::class,
        'BII' => Generated\BIIStructureIdentification::class,
        'BUS' => Generated\BUSBusinessFunction::class,
        'CAV' => Generated\CAVCharacteristicValue::class,
        'CCD' => Generated\CCDCreditCoverDetails::class,
        'CCI' => Generated\CCICharacteristicclassId::class,
        'CDI' => Generated\CDIPhysicalOrLogicalState::class,
        'CDS' => Generated\CDSCodeSetIdentification::class,
        'CDV' => Generated\CDVCodeValueDefinition::class,
        'CED' => Generated\CEDComputerEnvironmentDetails::class,
        'CMP' => Generated\CMPCompositeDataElementIdentification::class,
        'CNI' => Generated\CNIConsignmentInformation::class,
        'COD' => Generated\CODComponentDetails::class,
        'COT' => Generated\COTContributionDetails::class,
        'CPI' => Generated\CPIChargePaymentInstructions::class,
        'CPS' => Generated\CPSConsignmentPackingSequence::class,
        'CST' => Generated\CSTCustomsStatusOfGoods::class,
        'DAM' => Generated\DAMDamage::class,
        'DGS' => Generated\DGSDangerousGoods::class,
        'DII' => Generated\DIIDirectoryIdentification::class,
        'DIM' => Generated\DIMDimensions::class,
        'DLI' => Generated\DLIDocumentLineIdentification::class,
        'DLM' => Generated\DLMDeliveryLimitations::class,
        'DMS' => Generated\DMSDocumentmessageSummary::class,
        'DOC' => Generated\DOCDocumentmessageDetails::class,
        'DSI' => Generated\DSIDataSetIdentification::class,
        'EFI' => Generated\EFIExternalFileLinkIdentification::class,
        'ELM' => Generated\ELMSimpleDataElementDetails::class,
        'ELU' => Generated\ELUDataElementUsageDetails::class,
        'EMP' => Generated\EMPEmploymentDetails::class,
        'EQA' => Generated\EQAAttachedEquipment::class,
        'EQD' => Generated\EQDEquipmentDetails::class,
        'EQN' => Generated\EQNNumberOfUnits::class,
        'ERC' => Generated\ERCApplicationErrorInformation::class,
        'ERP' => Generated\ERPErrorPointDetails::class,
        'FCA' => Generated\FCAFinancialChargesAllocation::class,
        'FII' => Generated\FIIFinancialInstitutionInformation::class,
        'FNS' => Generated\FNSFootnoteSet::class,
        'FNT' => Generated\FNTFootnote::class,
        'GDS' => Generated\GDSNatureOfCargo::class,
        'GIN' => Generated\GINGoodsIdentityNumber::class,
        'GIR' => Generated\GIRRelatedIdentificationNumbers::class,
        'GIS' => Generated\GISGeneralIndicator::class,
        'GOR' => Generated\GORGovernmentalRequirements::class,
        'GRU' => Generated\GRUSegmentGroupUsageDetails::class,
        'HAN' => Generated\HANHandlingInstructions::class,
        'ICD' => Generated\ICDInsuranceCoverDescription::class,
        'IDE' => Generated\IDEIdentity::class,
        'IHC' => Generated\IHCPersonCharacteristic::class,
        'IND' => Generated\INDIndexDetails::class,
        'INP' => Generated\INPPartiesToInstruction::class,
        'INV' => Generated\INVInventoryManagementRelatedDetails::class,
        'IRQ' => Generated\IRQInformationRequired::class,
        'LAN' => Generated\LANLanguage::class,
        'MEM' => Generated\MEMMembershipDetails::class,
        'MKS' => Generated\MKSMarketsalesChannelInformation::class,
        'MSG' => Generated\MSGMessageTypeIdentification::class,
        'NAT' => Generated\NATNationality::class,
        'PAI' => Generated\PAIPaymentInstructions::class,
        'PDI' => Generated\PDIPersonDemographicInformation::class,
        'PGI' => Generated\PGIProductGroupInformation::class,
        'PIT' => Generated\PITPriceItemLine::class,
        'PNA' => Generated\PNAPartyName::class,
        'PRC' => Generated\PRCProcessIdentification::class,
        'PSD' => Generated\PSDPhysicalSampleDescription::class,
        'PTY' => Generated\PTYPriority::class,
        'QVR' => Generated\QVRQuantityVariances::class,
        'RCS' => Generated\RCSRequirementsAndConditions::class,
        'REL' => Generated\RELRelationship::class,
        'RNG' => Generated\RNGRangeDetails::class,
        'RTE' => Generated\RTERateDetails::class,
        'SAL' => Generated\SALRemunerationTypeIdentification::class,
        'SCC' => Generated\SCCSchedulingConditions::class,
        'SCD' => Generated\SCDStructureComponentDefinition::class,
        'SEG' => Generated\SEGSegmentIdentification::class,
        'SEL' => Generated\SELSealNumber::class,
        'SEQ' => Generated\SEQSequenceDetails::class,
        'SFI' => Generated\SFISafetyInformation::class,
        'SGP' => Generated\SGPSplitGoodsPlacement::class,
        'SGU' => Generated\SGUSegmentUsageDetails::class,
        'SPS' => Generated\SPSSamplingParametersForSummaryStatistics::class,
        'STA' => Generated\STAStatistics::class,
        'STC' => Generated\STCStatisticalConcept::class,
        'STG' => Generated\STGStages::class,
        'STS' => Generated\STSStatus::class,
        'TCC' => Generated\TCCTransportChargerateCalculations::class,
        'TEM' => Generated\TEMTestMethod::class,
        'TMD' => Generated\TMDTransportMovementDetails::class,
        'TMP' => Generated\TMPTemperature::class,
        'TPL' => Generated\TPLTransportPlacement::class,
        'TSR' => Generated\TSRTransportServiceRequirements::class,
        'VLI' => Generated\VLIValueListIdentification::class,
    ];

    /**
     * @codeCoverageIgnore Prevents instantiation of this constants holder
     */
    private function __construct()
    {
    }
}
