<?php
  /**
   * Created by PhpStorm.
   * User: Bender
   * Date: 3/15/2019
   * Time: 3:22 AM
   */

  namespace SegHis\modules\eclaims\services\cf4\nodes;


  use HL7\Message;
  use SegHis\modules\eclaims\helpers\cf4\CF4Helper;
  use SegHis\modules\eclaims\services\cf4\CF4Service;
  use SegHis\modules\eclaims\services\cf4\XmlWriter;
  use SegHis\modules\laboratory\models\Hl7HclabMsgReceipt;
  use SegHEIRS\modules\integrations\hl7\segments\MSH;
  use SegHEIRS\modules\integrations\hl7\segments\OBR;
  use SegHEIRS\modules\integrations\hl7\segments\OBX;

  class LabResultService extends XmlWriter
  {
    const CBC = "CBC";
    const URINE = "URIN";
    const LIPID = "LIPID";
    const CHEST = "CHEST";
    const SPUTUM = "SPUTUM";
    const FBS = "FBS";
    const ECG = "ECG";
    const FECAL = "FECA";
    const PSMEAR = "PS";
    const OGTT = "OGTT";

    public $document;

    public $encounter;

    public function __construct(
        \DOMDocument $document,
        \EclaimsEncounter $encounter
    ) {

      $this->document = $document;
      $this->encounter = $encounter;
    }

    public function generateHeader()
    {
      $header = $this->_createNode(
          $this->document,
          'LABRESULTS',
          array()
      );
      return $header;
    }

    public function generateNode()
    {
      $header = $this->generateHeader();

      $detail = $this->_createNode(
          $this->document,
          'LABRESULT',
          array(
              'pHciCaseNo' => null,
              'pPatientPin' => null,
              'pPatientType' => null,
              'pMemPin' => null,
              'pEffYear' => null
          )
      );
      $this->getCBC($detail);
      $this->getURINALYSIS($detail);
      $this->getCHESTXRAY($detail);
      $this->getSPUTUM($detail);
      $this->getLIPIDPROF($detail);
      $this->getFBS($detail);
      $this->getECG($detail);
      $this->getFECALYSIS($detail);
      $this->getPAPSMEAR($detail);
      $this->getOGTT($detail);

      $header->appendChild($detail);

      $this->document->appendChild($header);

      return $header;
    }


    public function getCBC($detail)
    {
      $this->appendNode($detail,
          $cbc,
          'CBC',
          array(
              'pHciTransNo' => null,
              'pReferralFacility' => null,
              'pLabDate' => null,
              'pHematocrit' => null,
              'pHemoglobinG' => null,
              'pHemoglobinMmol' => null,
              'pMhcPg' => null,
              'pMhcFmol' => null,
              'pMchcGhb' => null,
              'pMchcMmol' => null,
              'pMcvUm' => null,
              'pMcvFl' => null,
              'pWbc1000' => null,
              'pWbc10' => null,
              'pMyelocyte' => null,
              'pNeutrophilsBnd' => null,
              'pNeutrophilsSeg' => null,
              'pLymphocytes' => null,
              'pMonocytes' => null,
              'pEosinophils' => null,
              'pBasophils' => null,
              'pPlatelet' => null,
              'pDateAdded' => null,
              'pIsApplicable' => "N",
              'pModule' => null,
              'pDiagnosticLabFee' => null,
              'pCoPay' => null,
              'pReportStatus' => CF4Helper::getDefaultReportStatus(),
              'pDeficiencyRemarks' => null,
          )
      );
    }

    public function getURINALYSIS($detail)
    {
      $this->appendNode($detail,
          $urinalysis,
          'URINALYSIS',
          array(
              'pHciTransNo' => null,
              'pReferralFacility' => null,
              'pLabDate' => null,
              'pGravity' => null,
              'pAppearance' => null,
              'pColor' => null,
              'pGlucose' => null,
              'pProteins' => null,
              'pKetones' => null,
              'pPh' => null,
              'pRbCells' => null,
              'pWbCells' => null,
              'pBacteria' => null,
              'pCrystals' => null,
              'pBladderCell' => null,
              'pSquamousCell' => null,
              'pTubularCell' => null,
              'pBroadCasts' => null,
              'pEpithelialCast' => null,
              'pGranularCast' => null,
              'pHyalineCast' => null,
              'pRbcCast' => null,
              'pWaxyCast' => null,
              'pWcCast' => null,
              'pAlbumin' => null,
              'pPusCells' => null,
              'pDateAdded' => null,
              'pDiagnosticLabFee' => null,
              'pCoPay' => null,
              'pReportStatus' => CF4Helper::getDefaultReportStatus(),
              'pIsApplicable' => "N",
              'pModule' => null,
              'pDeficiencyRemarks' => null,
          )
      );
    }

    public function getCHESTXRAY($detail)
    {
      $this->appendNode($detail,
          $enlistment,
          'CHESTXRAY',
          array(
              'pHciTransNo' => null,
              'pReferralFacility' => null,
              'pLabDate' => null,
              'pFindings' => null,
              'pRemarksFindings' => null,
              'pObservation' => null,
              'pRemarksObservation' => null,
              'pModule' => null,
              'pDiagnosticLabFee' => null,
              'pCoPay' => null,
              'pReportStatus' => CF4Helper::getDefaultReportStatus(),
              'pDeficiencyRemarks' => null,
              'pIsApplicable' => "N",
              'pDateAdded' => null
          )
      );
    }

    public function getSPUTUM($detail)
    {
      $this->appendNode($detail,
          $enlistment,
          'SPUTUM',
          array(
              'pHciTransNo' => null,
              'pReferralFacility' => null,
              'pLabDate' => null,
              'pFindings' => null,
              'pModule' => null,
              'pDiagnosticLabFee' => null,
              'pCoPay' => null,
              'pReportStatus' => CF4Helper::getDefaultReportStatus(),
              'pDeficiencyRemarks' => null,
              'pIsApplicable' => "N",
              'pDateAdded' => null,
              'pNoPlusses' => null,
              'pRemarks' => null,
              'pDataCollection' => 'X'
          )
      );

    }

    public function getLIPIDPROF($detail)
    {
      $this->appendNode($detail,
          $enlistment,
          'LIPIDPROF',
          array(
              'pHciTransNo' => null,
              'pReferralFacility' => null,
              'pLabDate' => null,
              'pLdl' => null,
              'pHdl' => null,
              'pTotal' => null,
              'pCholesterol' => null,
              'pTriglycerides' => null,
              'pDateAdded' => null,
              'pIsApplicable' => "N",
              'pModule' => "U",
              'pDiagnosticLabFee' => null,
              'pCoPay' => null,
              'pReportStatus' => CF4Helper::getDefaultReportStatus(),
              'pDeficiencyRemarks' => null,

          )
      );

    }

    public function getFBS($detail)
    {
      $this->appendNode($detail,
          $fbs,
          'FBS',
          array(
              'pHciTransNo' => null,
              'pReferralFacility' => null,
              'pLabDate' => null,
              'pGlucoseMg' => null,
              'pGlucoseMmol' => null,
              'pDateAdded' => null,
              'pIsApplicable' => "N",
              'pModule' => null,
              'pDiagnosticLabFee' => null,
              'pCoPay' => null,
              'pReportStatus' => CF4Helper::getDefaultReportStatus(),
              'pDeficiencyRemarks' => null
          )
      );


    }

    public function getECG($detail)
    {

      $this->appendNode($detail,
          $enlistment,
          'ECG',
          array(
              'pHciTransNo' => null,
              'pReferralFacility' => null,
              'pLabDate' => null,
              'pDateAdded' => null,
              'pIsApplicable' => "N",
              'pRemarks' => null,
              'pFindings' => null,
              'pModule' => 'U',
              'pDiagnosticLabFee' => null,
              'pCoPay' => null,
              'pReportStatus' => CF4Helper::getDefaultReportStatus(),
              'pDeficiencyRemarks' => null,
          )
      );
    }

    public function getFECALYSIS($detail)
    {
      $this->appendNode($detail,
          $fecalysis,
          'FECALYSIS',
          array(
              'pHciTransNo' => null,
              'pReferralFacility' => null,
              'pLabDate' => null,
              'pColor' => null,
              'pConsistency' => null,
              'pRbc' => null,
              'pWbc' => null,
              'pOva' => null,
              'pParasite' => null,
              'pBlood' => null,
              'pOccultBlood' => null,
              'pPusCells' => null,
              'pDateAdded' => null,
              'pIsApplicable' => "N",
              'pModule' => null,
              'pDiagnosticLabFee' => null,
              'pCoPay' => null,
              'pReportStatus' => CF4Helper::getDefaultReportStatus(),
              'pDeficiencyRemarks' => null
          )
      );
    }

    public function getPAPSMEAR($detail)
    {
      $this->appendNode($detail,
          $enlistment,
          'PAPSSMEAR',
          array(
              'pHciTransNo' => null,
              'pReferralFacility' => null,
              'pLabDate' => null,
              'pDateAdded' => null,
              'pIsApplicable' => "N",
              'pModule' => 'U',
              'pDiagnosticLabFee' => null,
              'pCoPay' => null,
              'pImpression' => null,
              'pFindings' => null,
              'pReportStatus' => CF4Helper::getDefaultReportStatus(),
              'pDeficiencyRemarks' => null,
          )
      );
    }

    public function getOGTT($detail)
    {

      $this->appendNode($detail,
          $ogtt,
          'OGTT',
          array(
              'pHciTransNo' => null,
              'pReferralFacility' => null,
              'pLabDate' => null,
              'pExamOgttTwoHrMmol' => null,
              'pExamOgttTwoHrMg' => null,
              'pExamOgttOneHrMmol' => null,
              'pExamOgttOneHrMg' => null,
              'pExamFastingMmol' => null,
              'pExamFastingMg' => null,
              'pDateAdded' => null,
              'pIsApplicable' => 'N',
              'pModule' => null,
              'pDiagnosticLabFee' => null,
              'pCoPay' => null,
              'pReportStatus' => CF4Helper::getDefaultReportStatus(),
              'pDeficiencyRemarks' => null,
          )
      );
    }
  }