<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/15/2019
 * Time: 3:29 AM
 */

namespace SegHis\modules\eclaims\services\cf4\nodes;

use Composer\Downloader\VcsDownloader;
use SegHis\modules\eclaims\helpers\cf4\CF4Helper;
use SegHis\modules\eclaims\models\BloodTypePatient;
use SegHis\modules\eclaims\models\cf4\Cf4Examination;
use SegHis\modules\eclaims\services\cf4\CF4DataService;
use SegHis\modules\eclaims\services\cf4\CF4Service;
use SegHis\modules\eclaims\services\cf4\nodes\services\MedicalHistoryService;
use SegHis\modules\eclaims\services\cf4\nodes\services\PhysicalExamService;
use SegHis\modules\eclaims\services\cf4\XmlWriter;
use Symfony\Component\HttpKernel\Tests\Fixtures\DataCollector\CloneVarDataCollector;

class ProfilingService extends XmlWriter
{

    public $document;

    /* EclaimsEncounter */
    public $encounter;

    public $data;

    public $physicalExam;

    const PROFILE_FEMALE = 'f';

    /* Initializes Class for Profiling Service*/
    public function __construct(
      \DOMDocument $document,
      \EclaimsEncounter $encounter,
      $data
    ) {
        $this->document = $document;
        $this->encounter = $encounter;
        $this->physicalExam = new PhysicalExamService($encounter);
        $this->medicalHistory = new MedicalHistoryService($encounter);
        $this->data = $data;
    }

    public function getHeader()
    {
        $header = $this->_createNode(
          $this->document,
          'PROFILING',
          array()
        );
        return $header;
    }

    public function generateNode()
    {

        $header = $this->getHeader();


        $detail = $this->_createNode(
          $this->document,
          'PROFILE',
          $this->getProfileData()
        );

        /* GENERATES OINFO NODE UNDER <SOAP> PROFILE */
        $this->getOINFO($detail);
        /* GENERATES MEDHIST NODE UNDER <SOAP> PROFILE */
        $this->getMEDHIST($detail);
        /* GENERATES MHSPECIFIC NODE UNDER <SOAP> PROFILE */
        $this->getMHSPECIFIC($detail);
//    /* GENERATES SURGHIST NODE UNDER <SOAP> PROFILE */
        $this->getSURGHIST($detail);
//    /* GENERATES FAMHIST NODE UNDER <SOAP> PROFILE */
        $this->getFAMHIST($detail);
//    /* GENERATES FHSPECIFIC NODE UNDER <SOAP> PROFILE */
        $this->getFHSPECIFIC($detail);
//    /* GENERATES SOCHIST NODE UNDER <SOAP> PROFILE */
        $this->getSOCHIST($detail);
//    /* GENERATES IMMUNIZATION NODE UNDER <SOAP> PROFILE */
        $this->getIMMUNIZATION($detail);
//    /* GENERATES MENSHIST NODE UNDER <SOAP> PROFILE */
        $this->getMENSHIST($detail);
//    /* GENERATES PREGHIST NODE UNDER <SOAP> PROFILE */
        $this->getPREGHIST($detail);
//    /* GENERATES PEPERT NODE UNDER <SOAP> PROFILE */
        $this->getPEPERT($detail);
//    /* GENERATES BLOODTYPE NODE UNDER <SOAP> PROFILE */
        $this->getBLOODTYPE($detail);
//    /* GENERATES PEGENSURVEY NODE UNDER <SOAP> PROFILE */
        $this->getPEGENSURVEY($detail);
//    /* GENERATES PEMISC NODE UNDER <SOAP> PROFILE */
        $this->getPEMISC($detail);
//    /* GENERATES PESPECIFIC NODE UNDER <SOAP> PROFILE */
        $this->getPESPECIFIC($detail);
//    /* GENERATES DIAGNOSTIC NODE UNDER <SOAP> PROFILE */
        $this->getDIAGNOSTIC($detail);
//    /* GENERATES MANAGEMENT NODE UNDER <SOAP> PROFILE */
        $this->getMANAGEMENT($detail);
//    /* GENERATES ADVICE NODE UNDER <SOAP> PROFILE */
        $this->getADVICE($detail);
//    /* GENERATES NCDQANS NODE UNDER <SOAP> PROFILE */
        $this->getNCDQANS($detail);

        $header->appendChild($detail);
        $this->document->appendChild($header);
        return $header;
    }

    public function getOINFO($detail)
    {

        $this->appendNode($detail,
          $enlistment,
          'OINFO',
          array(
            'pPatientPob' => null,
            'pPatientAge' => null,
            'pPatientOccupation' => null,
            'pPatientEducation' => null,
            'pPatientReligion' => null,
            'pPatientMotherMnln' => null,
            'pPatientMotherMnmi' => null,
            'pPatientMotherFn' => null,
            'pPatientMotherExtn' => null,
            'pPatientMotherBday' => null,
            'pPatientFatherLn' => null,
            'pPatientFatherMi' => null,
            'pPatientFatherFn' => null,
            'pPatientFatherExtn' => null,
            'pPatientFatherBday' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null,
          )
        );
    }

    public function getMEDHIST($detail)
    {

        $this->appendNode($detail,
          $enlistment,
          'MEDHIST',
          array(
            'pMdiseaseCode' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null,
          )
        );
    }

    public function getMHSPECIFIC($detail)
    {
        $service = new MedicalHistoryService($this->encounter);
        $data = $service->getPastMedicalHistory();

        if (empty($data)) {
            $data = array(0);
        }

        $data = array($data);

        foreach ($data as $datum) {

            $this->appendNode($detail,
              $enlistment,
              'MHSPECIFIC',
              array(
                'pMdiseaseCode' => $datum['disease_code'],
                'pSpecificDesc' => $datum['pertinent'],
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => null,
              )
            );
        }


    }

    public function getSURGHIST($detail)
    {

        $this->appendNode($detail,
          $enlistment,
          'SURGHIST',
          array(
            'pSurgDesc' => null,
            'pSurgDate' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null
          )
        );
    }

    public function getFAMHIST($detail)
    {
        $this->appendNode($detail,
          $enlistment,
          'FAMHIST',
          array(
            'pMdiseaseCode' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null,
          )
        );
    }

    public function getFHSPECIFIC($detail)
    {


        $this->appendNode($detail,
          $enlistment,
          'FHSPECIFIC',
          array(
            'pMdiseaseCode' => null,
            'pSpecificDesc' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null
          )
        );

    }

    public function getSOCHIST($detail)
    {


        $this->appendNode($detail,
          $enlistment,
          'SOCHIST',
          array(
            'pIsSmoker' => null,
            'pNoCigpk' => null,
            'pIsAdrinker' => null,
            'pNoBottles' => null,
            'pIllDrugUser' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null
          )
        );
    }

    public function getIMMUNIZATION($detail)
    {


        $this->appendNode($detail,
          $enlistment,
          'IMMUNIZATION',
          array(
            'pChildImmcode' => null,
            'pYoungwImmcode' => null,
            'pPregwImmcode' => null,
            'pElderlyImmcode' => null,
            'pOtherImm' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null
          )
        );

    }

    public function getMENSHIST($detail)
    {
        $service = new MedicalHistoryService($this->encounter);
        $data = $service->getMenshistory();

        $applicable = $data['is_applicable'] == "Y";

        $this->appendNode($detail,
          $enlistment,
          'MENSHIST',
          array(
            'pMenarchePeriod' => null,
            'pLastMensPeriod' => !$applicable ? null : $data['date_of_lmp'],
            'pPeriodDuration' => null,
            'pMensInterval' => null,
            'pPadsPerDay' => null,
            'pOnsetSexIc' => null,
            'pBirthCtrlMethod' => null,
            'pIsMenopause' => null,
            'pIsApplicable' => !$applicable ? "N" : "Y",
            'pMenopauseAge' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null,
          )
        );
    }

    public function getPREGHIST($detail)
    {

        $service = new MedicalHistoryService($this->encounter);
        $data = $service->getPregHistory();
        $mens = $service->getMenshistory();

        $applicable = $mens['is_applicable'] == "Y";


        $this->appendNode($detail,
          $enlistment,
          'PREGHIST',
          array(
            'pPregCnt' => !$applicable ? null : $data['gravida'],
            'pDeliveryCnt' => !$applicable ? null : $data['parity'],
            'pDeliveryTyp' => null,
            'pFullTermCnt' => !$applicable ? null : $data['term_births'],
            'pPrematureCnt' => !$applicable ? null : $data['preterm_births'],
            'pAbortionCnt' => !$applicable ? null : $data['abortion'],
            'pLivChildrenCnt' => !$applicable ? null : $data['living_children'],
            'pWPregIndhyp' => null,
            'pWFamPlan' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null
          )
        );

    }

    public function getPEPERT($detail)
    {
        $service = new CF4DataService($this->encounter);
        $data = $service->getVitalSigns();

        $this->appendNode($detail,
          $enlistment,
          'PEPERT',
          $data
        );


    }

    public function getBLOODTYPE($detail)
    {

        $this->appendNode($detail,
          $enlistment,
          'BLOODTYPE',
          array(
            'pBloodType' => null,
            'pBloodRh' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null
          )
        );


    }

    public function getPEGENSURVEY($detail)
    {

        $data = $this->getGenSurvey();

        $this->appendNode($detail,
          $enlistment,
          'PEGENSURVEY',
          array(
            'pGenSurveyId' => $data['gen_survey_id'],
            'pGenSurveyRem' => $data['remarks'],
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null
          )
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


    public function getDIAGNOSTIC($detail)
    {

        $this->appendNode($detail,
          $enlistment,
          'DIAGNOSTIC',
          array(
            'pDiagnosticId' => '0',
            'pOthRemarks' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null
          )
        );
    }

    public function getMANAGEMENT($detail)
    {
        $this->appendNode($detail,
          $enlistment,
          'MANAGEMENT',
          array(
            'pManagementId' => '0',
            'pOthRemarks' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null
          )
        );

    }

    public function getADVICE($detail)
    {
        $this->appendNode($detail,
          $enlistment,
          'ADVICE',
          array(
            'pRemarks' => CF4Helper::getDefaultNAstatus(),
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null
          )
        );

    }

    public function getNCDQANS($detail)
    {

        $this->appendNode($detail,
          $enlistment,
          'NCDQANS',
          array(
            'pQid1_Yn' => null,
            'pQid2_Yn' => null,
            'pQid3_Yn' => null,
            'pQid4_Yn' => null,
            'pQid5_Ynx' => null,
            'pQid6_Yn' => null,
            'pQid7_Yn' => null,
            'pQid8_Yn' => null,
            'pQid9_Yn' => null,
            'pQid10_Yn' => null,
            'pQid11_Yn' => null,
            'pQid12_Yn' => null,
            'pQid13_Yn' => null,
            'pQid14_Yn' => null,
            'pQid15_Yn' => null,
            'pQid16_Yn' => null,
            'pQid17_Abcde' => null,
            'pQid18_Yn' => null,
            'pQid19_Yn' => null,
            'pQid19_Fbsmg' => null,
            'pQid19_Fbsmmol' => null,
            'pQid19_Fbsdate' => null,
            'pQid20_Yn' => null,
            'pQid20_Choleval' => null,
            'pQid20_Choledate' => null,
            'pQid21_Yn' => null,
            'pQid21_Ketonval' => null,
            'pQid21_Ketondate' => null,
            'pQid22_Yn' => null,
            'pQid22_Proteinval' => null,
            'pQid22_Proteindate' => null,
            'pQid23_Yn' => null,
            'pQid24_Yn' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null
          )
        );

    }

    public function getProfileData()
    {
        return array(
          'pHciTransNo' => CF4Service::getpHciTransNo($this->encounter->encounter_nr),
          'pDeficiencyRemarks' => null,
          'pReportStatus' => CF4Helper::getDefaultReportStatus(),
          'pProfileATC' => 'CF4',
          'pEffYear' => date('Y'),
          'pRemarks' => null,
          'pProfDate' => date('Y-m-d', strtotime($this->encounter->encounter_date)),
          'pMemPin' => null,
          'pPatientType' => null,
          'pPatientPin' => CF4Service::getPatientPin($this->encounter->encounter_nr),
          'pHciCaseNo' => $this->encounter->encounter_nr,
        );
    }

    protected function getGenSurvey()
    {
        $command = \Yii::app()->db->createCommand();
        $command->from('seg_cf4_general_survey t');
        $command->where('t.encounter_nr = :encounter AND t.is_deleted != 1');
        $command->order('t.created_at DESC');
        $command->params[':encounter'] = $this->encounter->encounter_nr;

        return $command->queryRow();

    }


}