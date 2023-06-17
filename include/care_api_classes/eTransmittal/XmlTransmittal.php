<?php
//created by Nick 5-4-2015
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
include 'roots.php';
include_once $root_path.'include/inc_environment_global.php';
include 'Xml.php';

class XmlTransmittal {

    public $transmitNumber,
           $memberCategoryId,
           $dataArray = array(),
           $transmittalDetail = array(),
           $xml;

    const PAY_WARD = 2,
          CHARITY = 1,
          NBB_EFFECTIVE_DATE = '4/20/2015',
          SPONSORED_MEMBER = 5,
          HOSPITAL_SPONSORED_MEMBER = 9,
          KASAM_BAHAY = 11,
          LIFETIME_MEMBER = 6,
          SENIOR_CITIZEN = 10,
          NEW_BORN = 12,
          NEW_BORN_PACKAGE = 99432,
          OUT_PATIENT = 2,
          DIALYSIS_PATIENT = 5,
          HAMA_A = 9,
          HAMA_E = 4,
          TRANSFER_A = 8, 
          TRANSFER_E = 3,
          ABSCOND_A = 10,
          ABSCOND_E = 5,
          IMPROVE_A = 6,
          IMPROVE_E = 2 ,
          RECOVER_A = 5,
          RECOVER_E = 1;

    const MEMBERSHIP_TYPE_EMPLOYED_PRIVATE = 'S';

    const MEMBERSHIP_TYPE_INDIVIDUAL_PAYING = 'NS';

    const MEMBERSHIP_TYPE_LIFETIME_MEMBER = 'PS';

    public function __construct($transmitNumber, $memberCategoryId)
    {
        $this->transmitNumber = $transmitNumber;
        $this->memberCategoryId = $memberCategoryId;
        $this->getAll();
        $this->eClaims();
        $this->xml = $this->getXml();
    }

    public function getData()
    {
        return $this->dataArray;
    }

    private function getXml()
    {
        $rules = array(
            'pMemberLastName'         => array('upper', 'enye', 'defaultValue' => '.'),
            'pMemberFirstName'        => array('upper', 'enye', 'defaultValue' => '.'),
            'pMemberMiddleName'       => array('upper', 'enye', 'defaultValue' => '.'),
            'pMemberSuffix'          =>array('upper','enye'),
            'pPatientLastName'        => array('upper', 'enye', 'defaultValue' => '.'),
            'pPatientFirstName'       => array('upper', 'enye', 'defaultValue' => '.'),
            'pPatientMiddleName'      => array('upper', 'enye', 'defaultValue' => '.'),
            'pPatientSuffix'          => array('upper', 'enye'),
            'pMailingAddress'        =>array('upper','enye'),
            'pEmailAddress'          =>array('upper','enye'),
            'pPatientIs'             =>array('in'=>array('M','S','C','P')),
            'pPEN'                   =>array('upper','enye'),
            'pEmployerName'          =>array('upper','enye'),
            'pMemberBirthDate'       =>array('dateFormat'=>'m-d-Y'),
            'pPatientBirthDate'      =>array('dateFormat'=>'m-d-Y'),
            'pAdmissionDate'         =>array('dateFormat'=>'m-d-Y'),
            'pAdmissionTime'         =>array('dateFormat'=>'h:i:s A'),
            'pDischargeDate'         =>array('dateFormat'=>'m-d-Y'),
            'pDischargeTime'         =>array('dateFormat'=>'h:i:s A'),
            'pExpiredDate'           =>array('dateFormat'=>'m-d-Y'),
            'pExpiredTime'           =>array('dateFormat'=>'h:i:s A'),
            'pPhilhealthClaimType'   =>array('in'=>array('ALL-CASE-RATE','Z-BENEFIT')),
            'pPatientType'           =>array('in' => array('I','O')),
            'pIsEmergency'           =>array('in'=>array('Y','N')),
            'pDisposition'           =>array('in'=>array('I','R','H','A','E','T')),
            'pAccommodationType'     =>array('in'=>array('P','N')),
            'pLaterality'            =>array('in'=>array('L','R','B','N')),
            'pSessionDate'           =>array('dateFormat'=>'m-d-Y'),
            'pPatientSex'            =>array('upper'),
            'pMemberSex'             =>array('upper'),
            'pProcedureDate'         =>array('dateFormat'=>'m-d-Y'),
            'pRelatedProcedure'      =>array('upper', 'limit' => 100),
            'pDrying'                =>array('in'=>array('Y','N')),
            'pSkinToSkin'            =>array('in'=>array('Y','N')),
            'pCordClamping'          =>array('in'=>array('Y','N')),
            'pProphylaxis'           =>array('in'=>array('Y','N')),
            'pWeighing'              =>array('in'=>array('Y','N')),
            'pVitaminK'              =>array('in'=>array('Y','N')),
            'pBCG'                   =>array('in'=>array('Y','N')),
            'pNonSeparation'         =>array('in'=>array('Y','N')),
            'pHepatitisB'            =>array('in'=>array('Y','N')),
            'pDoctorLastName'         => array('upper', 'enye', 'defaultValue' => '.'),
            'pDoctorFirstName'        => array('upper', 'enye', 'defaultValue' => '.'),
            'pDoctorMiddleName'       => array('upper', 'enye', 'defaultValue' => '.'),
            'pDoctorSuffix'          =>array('upper','enye'),
            'pWithCoPay'             =>array('in'=>array('Y','N')),
            'pDoctorCoPay'           =>array('number'),
            'pTotalHCIFees'          =>array('number'),
            'pTotalProfFees'         =>array('number'),
            'pGrandTotal'            =>array('number'),
            'pTotalActualCharges'    =>array('number'),
            'pDiscount'              =>array('number'),
            'pPhilhealthBenefit'     =>array('number'),
            'pTotalAmount'           =>array('number'),
            'pMemberPatient'         =>array('in'=>array('Y','N')),
            'pHMO'                   =>array('in'=>array('Y','N')),
            'pDrugsMedicinesSupplies'=>array('in'=>array('Y','N')),
            'pDMSTotalAmount'        =>array('number'),
            'pExaminations'          =>array('in'=>array('Y','N')),
            'pZipCode'               =>array('defaultValue' => '0'),
        );

        $xml = new Xml($this->getData(),'eCLAIMS','eClaimsDef_1.7.3.dtd',$rules);

        return $xml;
    }

    private function getAll()
    {
        global $db;

        $where = array('transmit_no=?');
        $parameters = array($this->transmitNumber);

        if($this->memberCategoryId == 'all'){

        }else if($this->memberCategoryId == 'none'){
            $where[] = "sem.memcategory_id IS NULL";
        }else{
            $where[] = "sem.memcategory_id=?";
            $parameters[] = $this->memberCategoryId;
        }

        $where = implode(') AND (',$where);

        $this->transmittalDetail = $db->GetAll("SELECT
                                                  sd.*,
                                                  sbe.bill_nr,
                                                  sm.memcategory_desc
                                                FROM
                                                  seg_transmittal_details AS sd
                                                  INNER JOIN seg_billing_encounter AS sbe
                                                    ON sd.encounter_nr = sbe.encounter_nr
                                                    AND sbe.is_deleted IS NULL
                                                  LEFT JOIN seg_encounter_memcategory AS sem
                                                    ON sem.encounter_nr = sd.encounter_nr
                                                  LEFT JOIN seg_memcategory AS sm
                                                    ON sem.memcategory_id = sm.memcategory_id
                                                  INNER JOIN care_encounter AS ce
                                                    ON ce.encounter_nr = sd.encounter_nr
                                                  INNER JOIN care_person AS cp
                                                    ON cp.pid = ce.pid
                                                WHERE {$where}
                                                ORDER BY cp.name_last ASC",$parameters);
    }

    //NODES...

    private function eClaims()
    {
        global $db;
        $this->dataArray = array(
            'name' => 'eCLAIMS','attributes' => array(
                'pUserName' => '',
                'pUserPassword' => '',
                'pHospitalCode' => $db->GetOne("SELECT accreditation_no FROM care_insurance_firm WHERE hcare_id=?",PHIC_ID),
                'pHospitalEmail' => $db->GetOne("SELECT value FROM care_config_global WHERE type='main_info_email'"),
            ),'children' => array($this->eTransmittal())
        );
    }

    private function eTransmittal()
    {
        return array(
            'name' => 'eTRANSMITTAL','attributes'=>array('pHospitalTransmittalNo'=>$this->transmitNumber,'pTotalClaims'=>count($this->transmittalDetail)),
            'children' => $this->claims()
        );
    }

    private function claims()
    {
        $claims = array();
        foreach($this->transmittalDetail as $key => $claim){

            $claims[] = array(
                'name' => 'CLAIM','attributes' => array(
                    'pClaimNumber' => $claim['encounter_nr'],
                    'pTrackingNumber' => '',
                    'pPhilhealthClaimType' => 'ALL-CASE-RATE',
                    'pPatientType' => 'I',
                    'pIsEmergency' => 'N'
                ),'children' => $this->getClaimDetails($claim)
            );

        }
        return $claims;
    }

    private function getClaimDetails($claim)
    {
        global $db;
        //$db->debug = true;
        $data = $db->GetRow("SELECT
                                encounter.encounter_nr,
                                encounter.mgh_setdte,
                                encounter.encounter_date,
                                encounter.er_opd_diagnosis,
                                person.pid AS pid,
                                person.date_birth AS pPatientBirthDate,
								person.name_middle AS pPatientMiddleName,
								person.suffix AS pPatientSuffix,
								person.name_first AS pPatientFirstName,
								person.name_last AS pPatientLastName,
								person.email AS pEmailAddress,
                                person.cellphone_1_nr pMobileNo,
                                person.phone_1_nr pLandlineNo,
								person.sex AS pPatientSex,
								(SELECT zipcode FROM seg_municity sm WHERE sm.mun_nr = person.mun_nr) AS pZipCode,
								fn_get_complete_address(person.pid) AS pMailingAddress,
								IF(encounterInsurance.employer_name IS NOT NULL,encounterInsurance.employer_name,personInsurance.employer_name) AS pEmployerName,
								IF(encounterInsurance.employer_no IS NOT NULL,encounterInsurance.employer_no,personInsurance.employer_no) AS pPen,
								IF(encounterInsurance.insurance_nr IS NOT NULL,encounterInsurance.insurance_nr,personInsurance.insurance_nr) AS pPatientPIN,
								IF(encounterInsurance.relation IS NOT NULL,encounterInsurance.relation,personInsurance.relation) AS pPatientIs,
								#IF(encounterInsurance.member_type IS NOT NULL,encounterInsurance.member_type,personInsurance.member_type) AS pPatientIs,
								IF(encounterInsurance.birth_date IS NOT NULL,encounterInsurance.birth_date,personInsurance.birth_date) AS pMemberBirthDate,
								IF(encounterInsurance.member_mname IS NOT NULL,encounterInsurance.member_mname,personInsurance.member_mname) AS pMemberMiddleName,
								IF(encounterInsurance.suffix IS NOT NULL,encounterInsurance.suffix,personInsurance.suffix) AS pMemberSuffix,
								IF(encounterInsurance.member_fname IS NOT NULL,encounterInsurance.member_fname,personInsurance.member_fname) AS pMemberFirstName,
								IF(encounterInsurance.member_lname IS NOT NULL,encounterInsurance.member_lname,personInsurance.member_lname) AS pMemberLastName,
								IF(encounterInsurance.insurance_nr IS NOT NULL,encounterInsurance.insurance_nr,personInsurance.insurance_nr) AS pMemberPIN,
								IF(encounterInsurance.sex IS NOT NULL,encounterInsurance.sex,personInsurance.sex) AS pMemberSex,
								IF(encounterInsurance.member_type IS NOT NULL,encounterInsurance.member_type,personInsurance.member_type) AS pMemberShipType,
								encounter.admission_dt AS admission_date,
								CONCAT(encounter.discharge_date,' ',encounter.discharge_time) AS discharge_date,
								disposition.disp_code,
								result.result_code,
								billing.accommodation_type,
								billing.bill_dte,
                                encounter.encounter_type
							FROM care_encounter AS encounter

							LEFT JOIN seg_encounter_disposition AS disposition
							  ON disposition.encounter_nr = encounter.encounter_nr

							LEFT JOIN seg_encounter_result AS result
							  ON result.encounter_nr = encounter.encounter_nr

							LEFT JOIN seg_encounter_insurance_memberinfo AS encounterInsurance
							  ON encounter.encounter_nr = encounterInsurance.encounter_nr

                            LEFT JOIN seg_insurance_member_info AS personInsurance
                              ON personInsurance.pid = encounter.pid

							INNER JOIN seg_billing_encounter AS billing
							  ON billing.encounter_nr = encounter.encounter_nr

							INNER JOIN care_person AS person
							  ON encounter.pid = person.pid

							WHERE encounter.encounter_nr = ?
							AND billing.is_deleted IS NULL AND billing.is_final = '1'
							LIMIT 1",$claim['encounter_nr']);
        
        self::resolveMemberCategory($claim['encounter_nr'], $data);

        return array(
            $this->getCf1($data),
            $this->getCf2($data),
            $this->getAllCaseRates($claim['encounter_nr']),
            $this->getDocuments(),
        );
    }

    private static function resolveMemberCategory($encounterNr, &$insuranceData)
    {
        global $db;
        $memberCategory = $db->GetOne('SELECT memcategory_id FROM seg_encounter_memcategory WHERE encounter_nr = ?', $encounterNr);
        if ($memberCategory == self::KASAM_BAHAY) {

            if (trim($insuranceData['pEmployerName']) != '')
                $insuranceData['pMemberShipType'] = self::MEMBERSHIP_TYPE_EMPLOYED_PRIVATE;//employed private
            else
                $insuranceData['pMemberShipType'] = self::MEMBERSHIP_TYPE_INDIVIDUAL_PAYING;//individual paying

        } else if ($memberCategory == self::SENIOR_CITIZEN) {
            $insuranceData['pMemberShipType'] = self::MEMBERSHIP_TYPE_LIFETIME_MEMBER;//lifetime
        }
    }

    private function getCf1($data)
    {

        $encounterName = $this->getEncounterName($data['encounter_nr']);

        return array(
            'name' => 'CF1',
            'attributes' => array(
                'pMemberPIN' => str_replace(array('','-'),'',$data['pMemberPIN']),
                'pMemberLastName' => $data['pMemberLastName'],
                'pMemberFirstName' => $data['pMemberFirstName'],
                'pMemberSuffix' => $data['pMemberSuffix'],
                'pMemberMiddleName' => $data['pMemberMiddleName'],
                'pMemberBirthDate' => $data['pMemberBirthDate'],
                'pMemberShipType' => $data['pMemberShipType'],
                'pMailingAddress' => trim($data['pMailingAddress']),
                'pZipCode' => $data['pZipCode'],
                'pMemberSex' => $data['pMemberSex'] ? $data['pMemberSex'] : 'M',
                'pLandlineNo' => $data['pLandlineNo'],
                'pMobileNo' => $data['pMobileNo'],
                'pEmailAddress' => $data['pEmailAddress'],
                'pPatientIs' => $data['pPatientIs'],
                'pPatientPIN' => $data['pPatientPIN'],
                'pPatientLastName' => $encounterName['name_last'] ? $encounterName['name_last'] : $data['pPatientLastName'],
                'pPatientFirstName' => $encounterName['name_first'] ? $encounterName['name_first'] : $data['pPatientFirstName'],
                'pPatientSuffix' => $data['pPatientSuffix'],
                'pPatientMiddleName' => $encounterName['name_middle'] ? $encounterName['name_middle'] : $data['pPatientMiddleName'],
                'pPatientBirthDate' => $data['pPatientBirthDate'],
                'pPatientSex' => $data['pPatientSex'],
                'pPEN' => $data['pPen'],
                'pEmployerName' => $data['pEmployerName']
            ),
            'children' => array()
        );
    }

    private function getEncounterName($encounterNr)
    {
        global $db;
        return $db->GetRow("SELECT
                              encounterName.name_first,
                              encounterName.name_middle,
                              encounterName.name_last
                            FROM seg_encounter_name AS encounterName
                            WHERE encounterName.encounter_nr=?",$encounterNr);
    }

    private function getDispositionCodeById($dispositionId)
    {
        $dispositionCodes = array(5 => 'R', 6 => 'I', 7 => 'I', 8 => 'E', 9 => 'H', 10 => 'A');
        if(array_key_exists($dispositionId,$dispositionCodes))
            return $dispositionCodes[$dispositionId];
        else
            return 'I';
    }

    private function getDeathDateTime($encounterNr)
    {
        global $db;
        return $db->GetOne("SELECT CONCAT(p.death_date,' ',p.death_time) AS deathdate FROM care_person p WHERE death_encounter_nr = ?", $encounterNr);
    }

    private function getCf2($data)
    {

        $deathDate = $this->getDeathDateTime($data['encounter_nr']);
        $arrDeathDateTime = explode(' ', $deathDate);

        if (strtotime($deathDate))
            $dischargeDate = $deathDate;
        else if (strtotime($data['mgh_setdte']))
            $dischargeDate = $data['mgh_setdte'];
        else
            $dischargeDate = $data['bill_dte'];

        $arrDischargeDateTime = explode(' ', $dischargeDate);

        $disposition = $this->getDispositionCodeById($data['result_code']);

        if ($deathDate == "") {
            if ($data['encounter_type'] == self::OUT_PATIENT || $data['encounter_type'] == self::DIALYSIS_PATIENT || $this->hasNewBornPackage($data['encounter_nr'])) {
                $disposition = 'I';

            }elseif(!in_array($data['disp_code'],array(1,2,6,7))) {
                if ($data['disp_code']  == self::HAMA_E || $data['disp_code']  == self::HAMA_A)
                    $disposition = 'H';
                elseif ($data['disp_code']  == self::TRANSFER_E || $data['disp_code']  == self::TRANSFER_A)
                    $disposition = 'T';
                elseif ($data['disp_code']  == self::ABSCOND_E || $data['disp_code']  == self::ABSCOND_A)
                    $disposition = 'A';
            }elseif ($data['result_code'] == self::RECOVER_E || $data['result_code'] == self::RECOVER_A){
                $disposition = 'R';
            }elseif ($data['result_code'] == self::IMPROVE_E || $data['result_code'] == self::IMPROVE_E){
                $disposition = 'I'; 
            }
        } else {
            $disposition = "E";
        }

        $details = array(
            'name' => 'CF2','attributes'=>array(
                'pPatientReferred' => 'N',
                'pReferredIHCPAccreCode' => 0,
                'pAdmissionDate' => $data['encounter_date'],
                'pAdmissionTime' => $data['encounter_date'],
                'pDischargeDate' => $arrDischargeDateTime[0],
                'pDischargeTime' => $arrDischargeDateTime[1],
                'pDisposition' => $disposition,
                'pExpiredDate' => $arrDeathDateTime[0],
                'pExpiredTime' => $arrDeathDateTime[1],
                'pReferralIHCPAccreCode' => '',
                'pReferralReasons' => '',
                'pAccommodationType' => $data['accommodation_type'] == self::CHARITY ? 'N' : 'P',
                'pHasAttachedSOA' => 'Y' #always YES
            ),
            'children'=>array(
                $this->getDiagnosis($data['encounter_nr'],$data['er_opd_diagnosis']),
            )
        );

        $details['children'][] = $this->getSpecial($data['encounter_nr']);

        $details['children'] = array_merge($details['children'],$this->getProfessionals($data));//might be more than 1

        $details['children'][] = $this->getConsumption($data);

        return $details;
    }

    private function getDiagnosis($encounterNr, $er_opd_diagnosis)
    {
        return array(
            'name' => 'DIAGNOSIS',
            'attributes'=>array('pAdmissionDiagnosis'=>substr($er_opd_diagnosis, 0, 500)),
            'children'=>$this->getDischarge($encounterNr)
        );
    }

    private function getDischarge($encounterNr)
    {
        $discharges = array();
        $rvsCodes = $this->getRvsCodes($encounterNr);
        $icdCodes = $this->getIcdCodes($encounterNr);

        if(!empty($icdCodes)){
            foreach($icdCodes as $item){
                if(trim($item['group'])=='')
                    $item['group'] = 'NONE';

                $icdNodes[$item['acr_groupid']]['pDischargeDiagnosis'] = $item['group'];
                $icdNodes[$item['acr_groupid']]['codes'][] = array(
                    'pICDCode' => $item['code']
                );
            }
        }

        if(!empty($rvsCodes)){
            foreach($rvsCodes as $item){
                if(trim($item['group'])==''){
                    $item['group'] = 'NONE';
                }

                $rvsNodes[$item['acr_groupid']]['pDischargeDiagnosis'] = $item['group'];
                $rvsNodes[$item['acr_groupid']]['codes'][] = array(
                    'pRVSCode' => $item['code'],
                    'pRelatedProcedure' => $item['description'],
                    'pProcedureDate' => $item['op_date'],
                    'pLaterality' => $item['laterality'] ? $item['laterality'] : 'N',
                );
            }
        }

        if(!empty($icdNodes)){
            foreach($icdNodes as $acrKey => $acrGroup){
                $codes = array();
                if(!empty($acrGroup['codes'])){
                    foreach($acrGroup['codes'] as $code){
                        $codes[] = array('name'=>'ICDCODE','attributes'=>$code,'children'=>array());
                    }
                }
                $discharges[] = array(
                    'name'=>'DISCHARGE',
                    'attributes'=>array('pDischargeDiagnosis'=>$acrGroup['pDischargeDiagnosis']),
                    'children'=>$codes
                );
            }
        }

        if(!empty($rvsNodes)){
            foreach($rvsNodes as $acrKey => $acrGroup){
                $codes = array();
                if(!empty($acrGroup['codes'])){
                    foreach($acrGroup['codes'] as $code){
                        $codes[] = array('name'=>'RVSCODES','attributes'=>$code,'children'=>array());
                    }
                }
                $discharges[] = array(
                    'name'=>'DISCHARGE',
                    'attributes'=>array('pDischargeDiagnosis'=>$acrGroup['pDischargeDiagnosis']),
                    'children'=>$codes
                );
            }
        }

        if(empty($icdNodes) && empty($rvsNodes)){
            $discharges[] = array(
                'name'=>'DISCHARGE',
                'attributes'=>array('pDischargeDiagnosis'=>'NONE'),
                'children'=>array()
            );
        }
        return $discharges;
    }

    private function getIcdCodes($encounterNr)
    {
        global $db;
        return $db->GetAll("SELECT
                              scrp.code,
                              scrp.group
                            FROM
                              seg_encounter_diagnosis AS sed
                              INNER JOIN seg_case_rate_packages AS scrp
                                ON scrp.code = sed.code
                            WHERE is_deleted = 0
                              AND encounter_nr = ?",$encounterNr);
    }

    private function getRvsCodes($encounterNr)
    {
        global $db;
        return $db->GetAll("SELECT
                                  scrp.code,
                                  scrp.description,
                                  scrp.group,
                                  smod.laterality,
                                  smod.op_date
                                FROM
                                  seg_misc_ops AS smo
                                  INNER JOIN seg_misc_ops_details AS smod
                                    ON smod.refno = smo.refno
                                  INNER JOIN seg_case_rate_packages AS scrp
                                    ON scrp.code = smod.ops_code
                                WHERE smo.encounter_nr = ?",$encounterNr);
    }

    private function getSpecial($encounterNr)
    {
        $children = $this->getProcedures($encounterNr);
        $ncp = $this->getNcp($encounterNr);
        if(!empty($ncp)){
            $children = array_merge($children,$ncp);
        }

        if(!empty($children)){
            return array(
                'name' => 'SPECIAL',
                'attributes' => array(),
                'children' => $children
            );
        }else{
            return array(
                'name' => 'SPECIAL',
                'attributes' => array(),
                'children' => array()
            );
        }
    }

    private function getProcedures($encounterNr)
    {
        $foundSpecialProcedures = array();
        $considerations = array(
            'hemodialysis'  => 'Hemodialysis',
            'peritoneal'    => 'Peritoneal',
            'linac'         => 'linac',
            'cobalt'        => 'Cobalt',
            'transfusion'   => 'Transfusion',
            'brachytherapy' => 'Brachytherapy',
            'chemotherapy'  => 'Chemotherapy',
            'debridement'   => 'Debridement',
        );

        foreach($considerations as $key => $consideration){
            $specialProcedures = $this->findSpecialProcedure($encounterNr,$consideration);
            foreach ($specialProcedures as $specialProcedure) {
                $foundSpecialProcedures[$key][] = $specialProcedure['special_dates'];
            }
        }
        return self::_getProcedures($foundSpecialProcedures);
    }

    private static function _getProcedures($procedures)
    {
        $result = array();
        if(count($procedures) > 0){
            $children = array();
            foreach($procedures as $key => $procedureDates){
                $children[] = array(
                    'name' => strtoupper($key),
                    'attributes' => array(),
                    'children' => self::getSessions($procedureDates)
                );
            }
            $result = array(array('name'=>'PROCEDURES','attributes'=>array(),'children'=>$children));
        }
        return $result;
    }

    private static function getSessions($procedureDates)
    {
        $result = array();

        foreach($procedureDates as $procedureDate){
            $sessions = explode(',',$procedureDate);
            foreach ($sessions as $date) {
                if($date)
                    $result[] = array('name' => 'SESSIONS','attributes'=>array('pSessionDate'=>$date),'children'=>array());
            }
        }

        return $result;
    }

    private function findSpecialProcedure($encounterNr, $searchKey)
    {
        global $db;
        return $db->GetAll("SELECT
                              smod.ops_code,
                              scrp.description,
                              smod.laterality,
                              smod.op_date,
                              TRIM(TRAILING ',' FROM
                              GROUP_CONCAT(
                                CONCAT(smod.op_date,',',TRIM(TRAILING ',' FROM IFNULL(smod.special_dates,'')))
                              )) AS special_dates
                            FROM
                              seg_insurance_member_info AS sim
                              LEFT JOIN care_person AS cp
                                ON cp.pid = sim.pid
                              INNER JOIN care_encounter AS ce
                                ON ce.pid = sim.pid
                              INNER JOIN seg_misc_ops AS smo
                                ON smo.encounter_nr = ce.encounter_nr
                              INNER JOIN seg_misc_ops_details AS smod
                                ON smod.refno = smo.refno
                              INNER JOIN seg_case_rate_packages AS scrp
                                ON smod.ops_code = scrp.code
                            WHERE ce.encounter_nr = ?
                              AND scrp.description REGEXP ?
                              AND scrp.special_case = 1
                            GROUP BY smod.ops_code
                            ORDER BY scrp.description",array($encounterNr,$searchKey));
    }

    private function isHearingTestAvailed($encounterNr)
    {
        global $db;
        $isAvailed = $db->GetOne("SELECT
                                      scrs.is_availed
                                   FROM
                                     seg_caserate_hearing_test AS scrs
                                   WHERE scrs.encounter_nr = ?",$encounterNr);
        return isset($isAvailed) && $isAvailed == 1 ? true : false;
    }

    private function hasNewBornPackage($encounterNr)
    {
        global $db;
        $package = $db->GetOne("SELECT
                                  package_id
                                FROM
                                  seg_billing_encounter sbe
                                INNER JOIN seg_billing_caserate sbc
                                    ON sbe.bill_nr=sbc.bill_nr
                                WHERE sbe.is_final = 1
                                AND sbe.is_deleted IS NULL
                                AND encounter_nr = ? AND package_id = ?",array($encounterNr,self::NEW_BORN_PACKAGE));
        return ($package!='' ? true:false);
    }

    private function getNcp($encounterNr)
    {
        $isAvailed = $this->isHearingTestAvailed($encounterNr);
        $hasNewBornPackage = $this->hasNewBornPackage($encounterNr);

        if($isAvailed){
            return array(
                array('name'=>'NCP','attributes'=>array(
                    'pEssentialNewbornCare' => $hasNewBornPackage ? 'Y' : 'N',
                    'pNewbornHearingScreeningTest' => $hasNewBornPackage && $isAvailed ? 'Y' : 'N',
                    'pNewbornScreeningTest' => $hasNewBornPackage && $isAvailed ? 'N' : 'Y',
                    'pFilterCardNo' => 0,
                ),'children'=>array(
                    array('name'=>'ESSENTIAL','attributes'=>array(
                        'pDrying' => $isAvailed ? 'Y' : 'N',
                        'pSkinToSkin' =>  $isAvailed ? 'Y' : 'N',
                        'pCordClamping' =>  $isAvailed ? 'Y' : 'N',
                        'pProphylaxis' =>  $isAvailed ? 'Y' : 'N',
                        'pWeighing' =>  $isAvailed ? 'Y' : 'N',
                        'pVitaminK' =>  $isAvailed ? 'Y' : 'N',
                        'pBCG' =>  $isAvailed ? 'Y' : 'N',
                        'pNonSeparation' =>  $isAvailed ? 'Y' : 'N',
                        'pHepatitisB' => $isAvailed ? 'Y' : 'N'
                    ),'children'=>array())
                ))
            );
        }else{
            return array();
        }
    }

    private function getProfessionals($claim)
    {

        $professionals = array();

        $doctors = $this->getDoctors($claim['encounter_nr']);

        $discount = $this->getTotalAppliedDiscounts($claim['encounter_nr']);

        if(count($doctors) == 0){
            return array(array(
                'name' => 'PROFESSIONALS',
                'attributes' => array(
                    'pDoctorAccreCode' => 'NONE',
                    'pDoctorLastName' => 'NONE',
                    'pDoctorFirstName' => 'NONE',
                    'pDoctorMiddleName' => 'NONE',
                    'pDoctorSuffix' => 'NONE',
                    'pWithCoPay' => 'N',
                    'pDoctorCoPay' => 0,
                    'pDoctorSignDate' => 'NONE',
                ),
                'children' => array()
            ));
        }

        $isHouseCase = $this->isHouseCase($claim['encounter_nr'],$claim['accommodation_type']);

        foreach ($doctors as $doctor) {

            if($isHouseCase) {

                $attributes = array();

                if (!$this->isDiffCase($doctor['bill_nr']) && $doctor['role_area'] == 'D1') {
                    $houseCaseDoctors = $this->getHouseCaseDoctor('D3');
                } else {
                    $houseCaseDoctors = $this->getHouseCaseDoctor($doctor['role_area']);
                }

                foreach($houseCaseDoctors as $houseCaseDoctor){
                    $attributes = array(
                        'pDoctorAccreCode' => trim(str_replace('-', '', $houseCaseDoctor['acc_no'])),
                        'pDoctorLastName' => $houseCaseDoctor['pDoctorLastName'],
                        'pDoctorFirstName' => $houseCaseDoctor['pDoctorFirstName'],
                        'pDoctorMiddleName' => $houseCaseDoctor['pDoctorMiddleName'],
                        'pDoctorSuffix' => $houseCaseDoctor['pDoctorSuffix']
                    );
                }
            }else{
                $attributes = array(
                    'pDoctorAccreCode' => trim(str_replace('-', '', $doctor['acc_no'])),
                    'pDoctorLastName' => $doctor['pDoctorLastName'],
                    'pDoctorFirstName' => $doctor['pDoctorFirstName'],
                    'pDoctorMiddleName' => $doctor['pDoctorMiddleName'],
                    'pDoctorSuffix' => $doctor['pDoctorSuffix']
                );
            }
            $attributes = array(
                    'pDoctorAccreCode' => trim(str_replace('-', '', $doctor['acc_no'])),
                    'pDoctorLastName' => $doctor['pDoctorLastName'],
                    'pDoctorFirstName' => $doctor['pDoctorFirstName'],
                    'pDoctorMiddleName' => $doctor['pDoctorMiddleName'],
                    'pDoctorSuffix' => $doctor['pDoctorSuffix']
                );

            $doc_discount = $doctor['dr_charge'] * $discount;
            $copay_amount = $doctor['dr_charge'] - $doc_discount - $doctor['dr_claim'];

            if ($copay_amount <= 0) {
                $attributes = array_merge($attributes,array(
                    'pWithCoPay' => 'N',
                    'pDoctorCoPay' => '0.00'
                ));
            } else {
                $attributes = array_merge($attributes,array(
                    'pWithCoPay' => 'Y',
                    'pDoctorCoPay' => $copay_amount
                ));
            }
            $attributes = array_merge($attributes,array('pDoctorSignDate'=>$this->getCalculateDate($doctor['bill_dte'])));
            $professionals[] = array(
                'name' => 'PROFESSIONALS',
                'attributes' => $attributes,
                'children' => array()
            );
        }//end foreach $doctors

        return $professionals;
    }

    /**
     * @see class_eTransmittalXml.php getCalculateDate():string
     * @param $bill_dte
     * @return bool|string
     */
    private function getCalculateDate($bill_dte)
    {
        $bill_dte = date('Y-m-d', strtotime($bill_dte));
        $daysCount = 10;
        $date_orig = new DateTime($bill_dte);
        $t = doubleval($date_orig->format("U"));
        for ($i = 0; $i < $daysCount; $i++) {
            $addDay = 86400;
            $nextDay = date('w', ($t + $addDay));
            if ($nextDay == 0 || $nextDay == 6) {
                $i--;
            }
            $t = $t + $addDay;
        }
        return date('m-d-Y', ($t));
    }

    private function getTotalAppliedDiscounts($encounterNr)
    {
        global $db;
        return $db->GetOne("SELECT SUM(discount) AS total_discount FROM seg_billingapplied_discount WHERE encounter_nr = ?",$encounterNr);
    }

    /**
     * @see class_eTransmittalXml.php isDiffCase():bool
     * @param $billNr
     * @return bool
     */
    public function isDiffCase($billNr)
    {
        global $db;
        $first_type = '';
        $second_type = '';
        $caseRates = $db->GetAll("SELECT p.case_type, sc.rate_type
                                    FROM seg_billing_caserate sc
                                    INNER JOIN seg_case_rate_packages p
                                        ON p.code = sc.package_id
                                    WHERE bill_nr = ?",$billNr);

        foreach($caseRates as $caseRate){
            if ($caseRate['rate_type'] == 1)
                $first_type = $caseRate['case_type'];
            else
                $second_type = $caseRate['case_type'];
        }

        return $first_type != $second_type && $second_type != '';
    }

    /**
     * @see class_eTransmittalXml.php isDiffCase():AdoDbRecordSet
     * @param $role
     * @return mixed
     */
    public function getHouseCaseDoctor($role)
    {
        global $db;
        $filter = '';
        switch ($role) {
            case 'D2':
                $filter = "WHERE cpr.is_housecase_attdr = 1";
                break;
            case 'D3':
                $filter = "WHERE cpr.is_housecase_surgeon = 1";
                break;
            case 'D4':
                $filter = "WHERE cpr.is_housecase_anesth = 1";
                break;
        }
        return $db->GetAll("SELECT  fn_get_personell_first(cpr.nr) as pDoctorFirstName,
					 			  	fn_get_personell_last(cpr.nr) as pDoctorLastName,
								  	fn_get_personell_middle(cpr.nr) as pDoctorMiddleName,
								  	fn_get_personell_suffix(cpr.nr) as pDoctorSuffix,\n
			  						(SELECT accreditation_nr FROM seg_dr_accreditation AS sda WHERE
			  							sda.dr_nr = cpr.nr AND sda.hcare_id = '" . PHIC_ID . "') AS acc_no \n
			  				FROM care_personell cpr
			  				$filter");
    }

    private function _isHouseCase($encounterNr)
    {
        global $db;
        $isHouseCase = $db->GetOne("SELECT fn_isHouseCase(?) AS casetype",$encounterNr);
        return $isHouseCase == 1;
    }

    private function isHouseCase($encounterNr, $accommodation)
    {
        return $this->_isHouseCase($encounterNr) && $accommodation!=self::CHARITY;
    }

    private function getDoctors($encounterNr)
    {
        global $db;
        return $db->GetAll("SELECT
                                sbe.bill_nr,
                                sbe.bill_dte,
                                fn_get_personell_first(sbp.dr_nr) AS pDoctorFirstName,
                                fn_get_personell_last(sbp.dr_nr) AS pDoctorLastName,
                                fn_get_personell_middle(sbp.dr_nr) AS pDoctorMiddleName,
                                fn_get_personell_suffix(sbp.dr_nr) AS pDoctorSuffix,
                                SUM(sbp.dr_charge) AS dr_charge,
                                SUM(sbp.dr_claim) AS dr_claim,
                                sbp.role_area,
                                (SELECT accreditation_nr FROM seg_dr_accreditation AS sda
                                WHERE sda.dr_nr = sbp.dr_nr AND sda.hcare_id = ?) AS acc_no
                              FROM seg_billing_encounter AS sbe
                              INNER JOIN seg_billing_pf AS sbp ON sbe.bill_nr = sbp.bill_nr
                              WHERE sbe.is_final = '1' AND sbe.is_deleted IS NULL
                              AND sbe.encounter_nr = ? GROUP BY sbp.dr_nr",array(PHIC_ID,$encounterNr));
    }

    private function getConsumption($data)
    {
        $bill = $this->findBillingDetails($data['encounter_nr']);

        $excess = 0;
        $isCharity = $data['accommodation_type'] == self::CHARITY;
        $memberCategoryId = 0;
        $total_outside = 0;
        $total_charge = 0;
        $total_doc_charge = 0;
        $total_doc_discount = 0;
        $total_doc_coverage = 0;
        $total_hci_charge = 0;
        $total_hci_discount = 0;
        $total_hci_coverage = 0;
        $total_meds = 0;
        $total_xlo = 0;

        if(!empty($bill)){
            $total_doc_charge = $bill['total_doc_charge'];
            $total_doc_discount = $bill['total_doc_discount'];
            $total_doc_coverage = $bill['total_doc_coverage'];
            $total_hci_charge = $bill['total_hci_charge'];
            $total_hci_discount = $bill['total_hci_discount'];
            $total_hci_coverage = $bill['total_services_coverage'];
            $total_charge = $bill['total_doc_charge'] + $bill['total_hci_charge'];
            $total_coverage = $bill['total_doc_coverage'] + $bill['total_services_coverage'];
            $total_discount = $bill['total_doc_discount'] + $bill['total_hci_discount'];
            $excess = $total_charge - $total_discount - $total_coverage;
            $memberCategoryId = $bill['memcategory_id'];
            $total_meds = (($bill['total_meds']) ? $bill['total_meds'] : 0);
            $total_xlo = (($bill['total_xlo']) ? $bill['total_xlo'] : 0);
            $total_outside = $total_meds + $total_xlo;
        }

        if($excess<=0 || $this->isNbb($memberCategoryId,$data['encounter_date'],$isCharity) || $total_outside){
            return array('name' => 'CONSUMPTION','attributes'=>array('pEnoughBenefits'=>'Y'),
                'children'=>array(
                    array('name' => 'BENEFITS','attributes'=>array(
                        'pTotalHCIFees' => $total_hci_charge,
                        'pTotalProfFees' => $total_doc_charge,
                        'pGrandTotal' => $total_charge,
                ),'children'=>array()
                )));
        }else{
            return array('name' => 'CONSUMPTION','attributes'=>array('pEnoughBenefits'=>'N'),
                'children'=>array(
                    array('name' => 'HCIFEES','attributes'=>array(
                        'pTotalActualCharges' => $total_hci_charge,
                        'pDiscount' => $total_hci_discount !=0 ? $total_hci_charge - $total_hci_discount : 0,
                        'pPhilhealthBenefit' => $total_hci_coverage,
                        'pTotalAmount' => $total_hci_charge - $total_hci_discount - $total_hci_coverage,
                        'pMemberPatient' => 'Y',
                        'pHMO' => 'N',
                        'pOthers' => 'N'
                    ),'children'=>array()),
                    array('name' => 'PROFFEES','attributes'=>array(
                        'pTotalActualCharges' => $total_doc_charge,
                        'pDiscount' => $total_doc_discount != 0 ? $total_doc_charge - $total_doc_discount : 0,
                        'pPhilhealthBenefit' => $total_doc_coverage,
                        'pTotalAmount' => $total_doc_charge - $total_doc_discount - $total_doc_coverage,
                        'pMemberPatient' => 'Y',
                        'pHMO' => 'N',
                        'pOthers' => 'N'
                    ),'children'=>array()),
                    array('name' => 'PURCHASES','attributes'=>array(
                        'pDrugsMedicinesSupplies' => $total_meds <= 0 ? 'N' : 'Y',
                        'pDMSTotalAmount' => $total_meds,
                        'pExaminations' => $total_xlo <= 0 ? 'N' : 'Y',
                        'pExamTotalAmount' => $total_xlo
                    ),'children'=>array())
            ));
        }
    }

    private function isNbb($memberCategoryId, $encounterDate, $isCharity)
    {
        switch($memberCategoryId){
            case self::SPONSORED_MEMBER:
                return $isCharity;
                break;
            case self::HOSPITAL_SPONSORED_MEMBER:
                return $isCharity;
                break;
            case self::KASAM_BAHAY:
                return $isCharity && self::isEffectiveNbbDate($encounterDate);
                break;
            case self::LIFETIME_MEMBER:
                return $isCharity && self::isEffectiveNbbDate($encounterDate);
                break;
            case self::SENIOR_CITIZEN:
                return $isCharity && self::isEffectiveNbbDate($encounterDate);
                break;
            default:
                return false;
        }
    }

    private static function isEffectiveNbbDate($encounterDate)
    {
        return strtotime(self::NBB_EFFECTIVE_DATE) <= strtotime($encounterDate);
    }

    private function findBillingDetails($encounterNr)
    {
        global $db;
        return $db->GetRow("SELECT
                              sbe.accommodation_type,
                              sbe.bill_dte,
                              sbc.total_services_coverage,
                              sbe.total_doc_charge,
                              SUM(
                                IFNULL(sbc.total_d1_coverage, 0) + IFNULL(sbc.total_d2_coverage, 0) + IFNULL(sbc.total_d3_coverage, 0) +
                                IFNULL(sbc.total_d4_coverage, 0)
                              ) AS total_doc_coverage,
                              SUM(
                                IFNULL(sbe.total_acc_charge, 0) + IFNULL(sbe.total_med_charge, 0) +
                                IFNULL(sbe.total_ops_charge, 0) + IFNULL(sbe.total_msc_charge, 0) + IFNULL(sbe.total_srv_charge, 0) +
                                IFNULL(sbe.total_sup_charge, 0)
                              ) AS total_hci_charge,
                              SUM(
                                IFNULL(sbd.total_d1_discount, 0) + IFNULL(sbd.total_d2_discount, 0) + IFNULL(sbd.total_d3_discount, 0) +
                                IFNULL(sbd.total_d4_discount, 0)
                              ) AS total_doc_discount,
                              SUM(
                               IFNULL(sbd.hospital_income_discount,0) + IFNULL(sbd.total_msc_discount,0)
                              ) AS total_hci_discount,
                                sem.memcategory_id,
                                ser.total_meds,
                                ser.total_xlo
                            FROM seg_billing_encounter sbe
                              INNER JOIN seg_billing_coverage sbc
                                ON sbe.bill_nr = sbc.bill_nr AND sbc.hcare_id = ?
                              INNER JOIN seg_billingcomputed_discount sbd
                                ON sbd.bill_nr = sbe.bill_nr
                              LEFT JOIN seg_encounter_memcategory sem
                                ON sem.encounter_nr = sbe.encounter_nr
                                LEFT JOIN seg_encounter_reimbursed ser
                                ON ser.encounter_nr = sbe.encounter_nr
                            WHERE sbe.encounter_nr = ?
                             AND sbe.is_deleted IS NULL AND sbe.is_final = '1' ",array(PHIC_ID,$encounterNr));
    }

    private function getAllCaseRates($encounterNr)
    {
        $details = array(
            'name' => 'ALLCASERATE',
            'attributes' => array(),
            'children' => $this->getCaseRates($encounterNr)
        );
        return $details;
    }

    private function getCaseRates($encounterNr)
    {
        global $db;

       /* $caseRates = $db->GetAll("SELECT
                                      sbc.package_id,
                                      p.package,
                                      sbc.rate_type,
                                      p.case_type
                                      -- sca.acr_groupid #removed by monmon
                                    FROM
                                      seg_billing_encounter sbe
                                      INNER JOIN seg_billing_caserate sbc
                                        ON sbe.bill_nr = sbc.bill_nr
                                      INNER JOIN seg_case_rate_packages p
                                        ON p.code = sbc.package_id
                                    WHERE sbe.is_final = 1
                                      AND sbe.is_deleted IS NULL
                                      AND encounter_nr = ?
                                    ORDER BY sbc.rate_type",$encounterNr);*/
        #modified query by monmon

        $caseRates = $db->GetAll("SELECT * FROM (SELECT IF(p.alt_code != '',
                                  p.alt_code,
                                  sbc.package_id) AS package_id,
                                      sbc.package_id AS orig_id,
                                      p.package,
                                      sbc.rate_type,
                                      p.case_type,
                                      sca.acr_groupid
                                    FROM
                                      seg_billing_encounter sbe
                                      INNER JOIN seg_billing_caserate sbc
                                        ON sbe.bill_nr = sbc.bill_nr
                                      INNER JOIN seg_case_rate_packages AS p
                                        ON p.code = sbc.package_id
                                      INNER JOIN seg_caserate_acr AS sca
                                        ON sca.package_id = sbc.package_id
                                    WHERE sbe.is_final = 1
                                      AND sbe.is_deleted IS NULL
                                      AND encounter_nr = ?
                                    ORDER BY p.date_from DESC) t
                                    GROUP BY t.package_id
                                    ORDER BY t.rate_type",$encounterNr);
      

        $children = array();

        foreach ($caseRates as $caseRate) {
            $children[] = array(
                'name' => 'CASERATE',
                'attributes' => array(
                    'pCaseRateCode' => $caseRate['acr_groupid'],
                    'pICDCode' => $caseRate['case_type'] == 'm' ? $caseRate['package_id'] : '',
                    'pRVSCode' => $caseRate['case_type'] == 'p' ? $caseRate['package_id'] : '',
                    'pCaseRateAmount' => $caseRate['package']
                ),'children' => $this->getCataract($encounterNr,$caseRate['package_id'])
            );
        }

        return $children;
    }

    private function getCataract($encounterNr, $code)
    {
        global $db;
        if(in_array($code,array('66987','66983','66984'))){
            $cataractCode = $db->GetOne("SELECT
                                              smod.cataract_code
                                            FROM
                                              seg_misc_ops AS smo
                                              INNER JOIN seg_misc_ops_details AS smod
                                                ON smo.refno = smod.refno
                                            WHERE encounter_nr = ?
                                              AND smod.ops_code = ?",array($encounterNr,$code));
            return array(
                array(
                    'name' => 'CATARACT',
                    'attributes' => array('pCataractPreAuth' => $cataractCode ? $cataractCode : 'NONE'),
                    'children' => array()
                )
            );
        }else{
            return array();
        }
    }

    private function getDocuments()
    {
        return array(
            'name' => 'DOCUMENTS',
            'attributes' => array(),
            'children' => array(
                array(
                    'name' => 'DOCUMENT',
                    'attributes' => array('pDocumentType'=>'CAB','pDocumentURL'=>'NONE'),
                    'children' => array()
                )
            )
        );
    }

}//end class