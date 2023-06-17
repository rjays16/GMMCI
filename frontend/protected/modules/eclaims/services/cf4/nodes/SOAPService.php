<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/15/2019
 * Time: 3:27 AM
 */

namespace SegHis\modules\eclaims\services\cf4\nodes;


use Cf4ClinicalRecord;
use SegHis\modules\eclaims\helpers\cf4\CF4Helper;
use SegHis\modules\eclaims\services\cf4\CF4DataService;
use SegHis\modules\eclaims\services\cf4\CF4Service;
use SegHis\modules\eclaims\services\cf4\nodes\services\PhysicalExamService;
use SegHis\modules\eclaims\services\cf4\XmlWriter;

class SOAPService extends XmlWriter
{

    public $document;

    public $encounter;

    public $data;

    public $physicalExam;

    /* Initializes Class for SOAP Service*/
    public function __construct(
      \DOMDocument $document,
      \EclaimsEncounter $encounter,
      $data
    ) {
        $this->document = $document;
        $this->encounter = $encounter;
        $this->physicalExam = new PhysicalExamService($encounter);
        $this->data = $data;
    }


    public function getSOAPHeader()
    {
        $header = $this->_createNode(
          $this->document,
          'SOAPS',
          array()
        );
        return $header;
    }


    public function getSOAPDetail()
    {

        $detail = $this->_createNode(
          $this->document,
          'SOAP',
          $this->getSOAPData()
        );

        return $detail;
    }

    public function createSOAP()
    {
        /* GENERATES <SOAPS> HEADER */
        $header = $this->getSOAPHeader();
        /* GENERATES <SOAP> HEADER */
        $detail = $this->getSOAPDetail();

        /* GENERATES SUBJECTIVE NODE UNDER <SOAP> HEADER */
        $this->getSUBJECTIVE($detail);
        /* GENERATES PEPERT NODE UNDER <SOAP> HEADER */
        $this->getPEPERT($detail);
        /* GENERATES PEMISC NODE UNDER <SOAP> HEADER */
        $this->getPEMISC($detail);
        /* GENERATES PESPECIFIC NODE UNDER <SOAP> HEADER */
        $this->getPESPECIFIC($detail);
        /* GENERATES ICDS NODE UNDER <SOAP> HEADER */
        $this->getICDS($detail);
        /* GENERATES DIAGNOSTIC NODE UNDER <SOAP> HEADER */
        $this->getDIAGNOSTIC($detail);
        /* GENERATES MANAGEMENT NODE UNDER <SOAP> HEADER */
        $this->getMANAGEMENT($detail);
        /* GENERATES ADVICE NODE UNDER <SOAP> HEADER */
        $this->getADVICE($detail);


        /* Appending the SOAP NODE Under SOAPS HEADER*/
        $header->appendChild($detail);
        /* Adding the whole SOAP INTO XML*/
        $this->document->appendChild($header);

        return $header;
    }


    public function getSOAPData()
    {
        return array(
          'pHciCaseNo' => $this->encounter->encounter_nr,
          'pHciTransNo' => CF4Service::getpHciTransNo($this->encounter->encounter_nr),
          'pPatientPin' => CF4Service::getPatientPin($this->encounter->encounter_nr),
          'pPatientType' => CF4Service::getPatientType($this->encounter->encounter_nr),
          'pMemPin' => null,
          'pSoapDate' => date('Y-m-d', strtotime($this->encounter->discharge_date)),
          'pEffYear' => CF4Helper::getYear(),
          'pSoapATC' => 'CF4',
          'pReportStatus' => CF4Helper::getDefaultReportStatus(),
          'pDeficiencyRemarks' => ''
        );
    }

    public function getSUBJECTIVE($detail)
    {

        $service = new CF4DataService($this->encounter);
        $data = $service->getPertinentSigns();
        $chief = $service->getChiefComplaint();
        $clinical = $service->getClinicalRecord();
        $signs = '';


        foreach ($data as $datum) {

            $applied = ($datum['sign_symptoms'] == 38);
            $others = ($datum['sign_symptoms'] == "X");

            if ($others) {
                $others = $datum['others'];
            } else {
                if ($applied) {
                    $pPainSite = $datum['pains'];
                }
            }

            $signs .= $datum['sign_symptoms'] . ';';

        }

        $this->appendNode(
          $detail,
          $details,
          'SUBJECTIVE',
          array(
            'pChiefComplaint' => $chief['chief_complaint'],
            'pSignsSymptoms' => $signs,
            'pIllnessHistory' => $clinical['present_illness'],
            'pOtherComplaint' => $others,
            'pPainSite' => $pPainSite,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => ''
          )

        );
    }

    public function getPEPERT($detail)
    {
        $service = new CF4DataService($this->encounter);
        $data = $service->getVitalSigns();

        $this->appendNode(
          $detail,
          $details,
          'PEPERT',
          $data
        );
    }

    public function getPEMISC($detail)
    {

        $service = new PhysicalExamService($this->encounter);
        $data = $service->getPEMISCData();

        if (empty($data)) {
            $data = array(1);
        }

        foreach ($data as $datum) {
            foreach ($datum as $d) {
                $this->appendNode($detail,
                  $enlistment,
                  'PEMISC',
                  array(
                    'pSkinId' => $d['skin_id'],
                    'pHeentId' => $d['heent_id'],
                    'pChestId' => $d['chest_id'],
                    'pHeartId' => $d['heart_id'],
                    'pAbdomenId' => $d['abdomen_id'],
                    'pNeuroId' => $d['neuro_id'],
                    'pRectalId' => $d['rectal_id'],
                    'pGuId' => $d['guie_id'],
                    'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                    'pDeficiencyRemarks' => null
                  )
                );
            }
        }
    }


    public function getPESPECIFIC($detail)
    {
        $data = $this->physicalExam->getPESpecificData();
        $this->appendNode($detail,
          $enlistment,
          'PESPECIFIC',
          $data
        );
    }


    public function getICDS($detail)
    {
        $this->appendNode($detail,
          $icd,
          'ICDS',
          array(
            'pIcdCode' => CF4Helper::getDefaultIcd(),
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => ''
          )
        );
    }

    public function getDIAGNOSTIC($detail)
    {
        $this->appendNode($detail,
          $diagnostic,
          'DIAGNOSTIC',
          array(
            'pDiagnosticId' => '0',
            'pOthRemarks' => '',
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => ''
          )
        );
    }

    public function getMANAGEMENT($detail)
    {
        $this->appendNode($detail,
          $management,
          'MANAGEMENT',
          array(
            'pManagementId' => '0',
            'pOthRemarks' => '',
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => ''
          )
        );
    }

    public function getADVICE($detail)
    {

        $this->appendNode($detail,
          $advice,
          'ADVICE',
          array(
            'pRemarks' => CF4Helper::getDefaultNAstatus(),
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => ''
          )
        );
    }


}