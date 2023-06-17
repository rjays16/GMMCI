<?php

/**
 * CaseRateClaim.php
 *
 * @author Alvin Jay C. Cosare <ajunecosare15@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation (http://www.segworks.com)
 */

Yii::import('eclaims.models.Claim');

/**
 * Class for 'ALL-CASE-RATE' claim type
 *
 * @package eclaims.models
 */
class CaseRateClaim extends Claim
{
    const HSM = 9;

    //Define Default Document type and URL
    const DEFAULT_DOCTYPE = 'CAB';
    const DEFAULT_DOCURL = 'https://www.segworks.com';

    const DEFAULT_STRING = ".";
    const DEFAULT_NUMBER = "0";

    //Define Membership Category id for HOSPITAL SPONSORED MEMBER
    const SM = 5;

    //Define Membership Category id for SPONSORED MEMBER
    const HCARE_ID = 18;

    //Define PHIC_ID id for SPONSORED MEMBER
    const WELLBABY = 12;
    const NEWBORN = 99432;

    const STR = 1;
    const NUMBER = 2;
    const DATETIME = 3;
    const CURRENCY = 4;


    protected $xmlCache;

    /* Contains XML validation errors for this claim */
    protected $xmlValidationErrors;

    /* XML elements */
    protected $dom;
    protected $claim;

    // 1 or more - $eTransmittal
    protected $cf1;

    // only once - $claim
    protected $cf2;

    // only once - $claim
    protected $diagnosis;

    // only once - $cf2
    protected $discharge;

    // 1 or more - $diagnosis
    protected $icdcode;

    // 0 or more - $discharge
    protected $rvscodes;

    // 0 or more - $discharge
    protected $special;

    // only once - $cf2
    protected $procedures;

    // 0 or 1    - $special
    protected $hemodialysis;

    // 0 or 1    - $procedures
    protected $peritoneal;

    // 0 or 1    - $procedures
    protected $linac;

    // 0 or 1    - $procedures
    protected $cobalt;

    // 0 or 1    - $procedures
    protected $brachytheraphy;

    // 0 or 1    - $procedures
    protected $transfusion;

    // 0 or 1    - $procedures
    protected $chemotherapy;

    // 0 or 1    - $procedures
    protected $debridement;

    // 0 or 1    - $procedures
    protected $sessions;

    // 0 or 1    - $hemodialysis,$peritoneal,$linac,$cobalt,$brachytheraphy,$transfusion,$chemotherapy,$debridement
    protected $mcp;

    // 0 or 1    - $special
    protected $tbdots;

    // 0 or 1    - $special
    protected $abp;

    // 0 or 1    - $special
    protected $ncp;

    // 0 or 1    - $special
    protected $essential;

    // 0 or 1    - $ncp
    protected $hivaids;

    // 0 or 1    - $special
    protected $professionals;

    // 1 or more - $cf2
    protected $consumption;

    // only once - $cf2
    protected $benefits;

    // 0 or 1    - $consumption
    protected $hcifees;

    // 0 or 1    - $consumption
    protected $proffees;

    // 0 or 1    - $consumption
    protected $purchases;

    // 0 or 1    - $consumption
    protected $allcaserate;

    // only once - $claim
    protected $caserate;

    // 1 or more - $allcaserate
    protected $cataract;

    // 0 or 1    - $caserate
    protected $zbenefit;

    // only once - $claim
    protected $cf3;

    // 0 or 1    - $claim
    protected $cf3_old;

    // 0 or 1    - $cf3
    protected $maternity;

    // 0 or 1    - $cf3_old
    protected $prenatal;

    // only once - $maternity
    protected $clinicalhist;

    // only once - $prenatal
    protected $obstetric;

    // only once - $prenatal
    protected $medisurg;

    // only once - $prenatal
    protected $consultation;

    // 1 or more - $prenatal
    protected $delivery;

    // only once - $maternity
    protected $postpartum;

    // only once - $maternity
    protected $cf3_new;

    // 0 or 1    - $cf3
    protected $admitreason;

    // only once - $cf3_new
    protected $clinical;

    // 0 or more - $admitreason
    protected $labdiag;

    // 0 or more - $admitreason
    protected $phex;

    // only once - $cf3_old,$cf3_new
    protected $course;

    // only once - $cf3_new
    protected $ward;

    // 1 or more - $course
    protected $particulars;

    // 0 or 1    - $claim
    protected $drgmed;

    // 0 or more - $particulars
    protected $xlso;

    // 0 or more - $particulars
    protected $receipts;

    // 0 or 1    - $claim
    protected $receipt;

    // 1 or more - $receipts
    protected $item;

    // 1 or more - $receipt
    protected $documents;

    // only once - $claim
    protected $document;

    // 1 or more - $documents

    protected $bhousecase;

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CaseRateClaim the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * Generates the XML tags for a claim and returns the whole <CLAIM> node generated
     *
     * @param boolean $regenerate Whether to regenrate the Claim XML even if an XML cache already exists
     * @return DOMElement Node to be attached to the <eTransmittal> node
     */
    public function generateXml($regenerate = false) {
        if (!$this->xmlCache || $regenerate) {

            $this->retrieveInfo();
            $this->dom = self::createDocument();
            $this->setClaim();
            $this->setCf1Node();
            $this->setCf2Node();
            $this->setCaseTypeNode();

            //TODO
            // $this->setCf3Node();

            // $this->setParticularsNode();
            // $this->setReceiptsNode();
            $this->setDocumentsNode();
             //TODO but is required in XML

            //$this->setValidationErrors();
            $this->xmlCache = $this->dom->saveXml($document->documentElement);
        }
        return $this->xmlCache;
    }

    /**
     * Retrieves information of related tables (thru CActiveRecord relations)
     * @return void
     */
    protected function retrieveInfo() {

        // $this->_encounter = $this->encounter;
        // $this->_person = $this->_encounter->person;

        // $member = $this->_person->phicMember;
        // if (!$member) {
        //     $member = new PhicMember();
        // }

        // $this->_eligibility = $this->_encounter->eligibility;
        // if (!$this->_eligibility) {
        //     $this->_eligibility = new Eligibility();
        // }
        // $this->_diagnoses = $this->_encounter->diagnosis;
        // $this->_operations = $this->_encounter->misc_ops;


    }

    /**
     * [appendNode description]
     * @param  [type] $parent [description]
     * @param  [type] $child  [description]
     * @param  [type] $name   [description]
     * @param  [type] $attrs  [description]
     * @return [type]         [description]
     */
    protected function appendNode(&$parent, &$child, $name, $attrs = array()) {
        $child = $parent->appendChild(new DOMElement($name));
        foreach ($attrs as $akey => $attr) {
            if (mb_detect_encoding($attr, 'UTF-8', true) === false) {
                $attr = utf8_encode($attr);
            }
            $child->setAttribute($akey, $attr);
        }
    }

    /**
     * Initializes the <CLAIM> node
     * @return void
     */
    protected function setClaim() {

        $eligibility = $this->encounter->eligibility;
        if (empty($eligibility)) {
            $eligibility = new Eligibility;
        }

        /* set attributes for node in $attrs[] */
        $attrs = array();
        $attrs['pClaimNumber'] = $this->encounter_nr;
        $attrs['pTrackingNumber'] = ($eligibility->tracking_number) ? $eligibility->tracking_number : '';
        $attrs['pPhilhealthClaimType'] = 'ALL-CASE-RATE';
         //todo
        $attrs['pPatientType'] = $this->encounter->getPatientType();
        $attrs['pIsEmergency'] = $this->encounter->getEmergencyStatus();

        /* append child(w/ attributes) to parent node*/
        $this->appendNode($this->dom, $this->claim, 'CLAIM', $attrs);
    }

    /**
     * Constructs the 'CF1' node including its child nodes (parent node: CLAIM)
     * @return void
     */
    protected function setCf1Node() {
        Yii::import('eclaims.models.EncounterInsurance');
        $person = $this->encounter->person;
        $member = $person->phicMember;
        if (!$member) {
            $member = new EclaimsPhicMember;
        }

        #added by monmon : fetch member info from HIS table
        if(!$member->insurance_nr){
            $insurance = new EncounterInsurance();
            $insurance_nr = $insurance->getMemberInsuranceNr($person->pid);
            $member->insurance_nr = $insurance_nr['insurance_nr'];
            $member->relation = $insurance_nr['relation'];
        }

        Yii::import('eclaims.components.EclaimsEncoder');
        $encoder = new EclaimsEncoder;

        #change middle name to N/A if blank
        $memberMiddlename = trim($encoder->formatUTF8(strtoupper($member->member_mname)));
        $patientMiddleName = trim($encoder->formatUTF8(strtoupper($person->name_middle)));
        $suffix = $person->getSuffix()?$person->getSuffix():$person->suffix;

        /* set attributes for cf1 */
        $attrs = array();

        #$pMemberPIN = str_replace(array("-", "–"), '', $this->formatter->formatPin($member->insurance_nr));
        $attrs['pMemberPIN'] = $this->formatter->formatPin($member->insurance_nr);
//        $attrs['pMemberLastName'] = $encoder->formatUTF8(strtoupper($member->member_lname));
//        $attrs['pMemberFirstName'] = $encoder->formatUTF8(strtoupper($member->member_fname)); commented by Juna, 2021
        $attrs['pMemberLastName'] = mb_convert_case($member->member_lname, MB_CASE_UPPER, "UTF-8");
        $attrs['pMemberFirstName'] = mb_convert_case($member->member_fname, MB_CASE_UPPER, "UTF-8");
        // $attrs['pMemberMiddleName'] = $encoder->formatUTF8(strtoupper($member->member_mname));
        $attrs['pMemberMiddleName'] = ($memberMiddlename) ? $memberMiddlename : 'N/A';
        $attrs['pMemberSuffix'] = strtoupper($member->suffix);
        $attrs['pMemberBirthDate'] = $this->formatter->formatDate($member->birth_date);
        $attrs['pMemberShipType'] = strtoupper(($member->member_type) ? (($member->member_type != 'HSM') ? $member->member_type : 'I') : 'NS');
        #modified address source : from member address to patient address
        $attrs['pMailingAddress'] = $encoder->formatUTF8(strtoupper($person->getFullAddress()));

        $attrs['pZipCode'] = ((strtoupper($member->getZipCode())) ? strtoupper($member->getZipCode()) : self::DEFAULT_NUMBER);
        $attrs['pMemberSex'] = ((strtoupper($member->relation) == 'M') ? strtoupper($person->sex) : strtoupper($member->sex));
        $attrs['pEmailAddress'] = ((strtoupper($member->relation) == 'M') ? (($person->email) ? $person->email : self::DEFAULT_STRING) : self::DEFAULT_STRING);
        $attrs['pLandlineNo'] = ((strtoupper($member->relation) == 'M')   ? (($person->phone_1_nr) ? $person->phone_1_nr : self::DEFAULT_NUMBER) : self::DEFAULT_NUMBER);
        $attrs['pMobileNo'] = ((strtoupper($member->relation) == 'M')     ? (($person->cellphone_1_nr) ? $person->cellphone_1_nr : self::DEFAULT_NUMBER) : self::DEFAULT_NUMBER);

        $attrs['pPatientIs'] = strtoupper($member->relation);
        $attrs['pPatientPIN'] = ((strtoupper($member->relation) == 'M') ? $this->formatter->formatPin($member->insurance_nr) : self::DEFAULT_NUMBER);
//        $attrs['pPatientLastName'] = $encoder->formatUTF8(strtoupper($person->name_last));
//        $attrs['pPatientFirstName'] = $encoder->formatUTF8(strtoupper($person->getNameFirst())); commented by Juna, June 21 2021
        $attrs['pPatientLastName'] = mb_convert_case($person->name_last, MB_CASE_UPPER, "UTF-8");
        $attrs['pPatientFirstName'] = mb_convert_case($person->getNameFirst(), MB_CASE_UPPER, "UTF-8");
        // $attrs['pPatientMiddleName'] = $encoder->formatUTF8(strtoupper($person->name_middle));
        $attrs['pPatientMiddleName'] = ($patientMiddleName) ? $patientMiddleName : 'N/A';
        $attrs['pPatientSuffix'] = strtoupper($suffix);
        $attrs['pPatientBirthDate'] = $this->formatter->formatDate($person->date_birth);
        $attrs['pPatientSex'] = strtoupper($person->sex);
        $attrs['pPEN'] = strtoupper($member->employer_no);
        $attrs['pEmployerName'] = strtoupper($member->employer_name);

        /* append cf1 node(w/ attributes) to claim node*/
        $this->appendNode($this->claim, $this->cf1, 'CF1', $attrs);

    }

    private function formatUTF8($val = null) {
        if(empty($val))
            return $val;

        if(mb_detect_encoding($val) == 'UTF-8') {
            return utf8_encode($val);
        }
        return $val;
    }

    /**
     * Constructs the 'CF2' node including its child nodes (parent node: CLAIM)
     *
     * @return void
     */
    protected function setCf2Node() {
        $billing = $this->billing;

        $encounter = $this->encounter;
        $person = $encounter->person;

        /**
        * Added by jeff 01-30-18 for fetching of discharge date/time based on XML of transmittal history.
        */
        $bill_dte = $billing->bill_dte;

        $bill_date_res = explode(' ',$bill_dte);
        $billDate = $bill_date_res[0];
        $billTime = $bill_date_res[1];

        $personIsDeath = $person->death_encounter_nr;
        if ($personIsDeath) {
            $dischargeDate = $person->death_date;
            $dischargeTime = $person->death_time;
        }
        else{
            // $dischargeDate = $encounter->discharge_date;
            // $dischargeTime = $encounter->discharge_time;
            $dischargeDate = $billDate;
            $dischargeTime = $billTime;
        }

        /* set attributes for node in $attrs[] */
        $attrs = array();

        /**
         * @todo Handle patient referrals
         */
        $attrs['pPatientReferred'] = 'N';
        $attrs['pReferredIHCPAccreCode'] = 'NA';

        $attrs['pAdmissionDate'] = $this->formatter->formatDate($encounter->getAdmissionDt());
        $attrs['pAdmissionTime'] = $this->formatter->formatTime($encounter->getAdmissionDt());
        $attrs['pDischargeDate'] = $this->formatter->formatDate($encounter->pullDischargeDate());
        $attrs['pDischargeTime'] = $this->formatter->formatTime($encounter->pullDischargeTime());

        $attrs['pDisposition'] = ($encounter->getDispositionCode()? $encounter->getDispositionCode() : 'I' );

        // Death date/time for expired patients
        $attrs['pExpiredDate'] = ($attrs['pDisposition'] == 'E') ? $this->formatter->formatDate($person->death_date) : '';
        $attrs['pExpiredTime'] = ($attrs['pDisposition'] == 'E') ? $this->formatter->formatTime($person->death_time) : '';

        /**
         *  @todo  Handle transferred patients
         */
        $attrs['pReferralIHCPAccreCode'] = '';
        $attrs['pReferralReasons'] = '';

        // Private/Non-private
        $attrs['pAccommodationType'] = ($billing->accommodation_type == 1) ? 'N' : 'P';

        /* append cf2 node(w/ attributes) to claim node*/
        $this->appendNode($this->claim, $this->cf2, 'CF2', $attrs);

        /* Set CF2 Child Nodes */
        $this->setCf2Diagnosis();
        $this->setCf2Special();
        $this->setCf2Professionals();
        $this->setCf2Consumption();
    }

    /**
     * Constructs the 'DIAGNOSIS' node including its child nodes (parent node: CF2)
     *
     * @return void
     */
    protected function setCf2Diagnosis() {
        $encounter = $this->encounter;

        $attrs = array();

        $encoding = mb_detect_encoding($encounter->er_opd_diagnosis);
        $diagnosis = $encounter->er_opd_diagnosis;
        if($encoding == 'UTF-8' || $encoding == false) {
            $diagnosis = utf8_encode($diagnosis);
        }

        $attrs['pAdmissionDiagnosis'] = strtoupper($diagnosis);
        $this->appendNode($this->cf2, $this->diagnosis, 'DIAGNOSIS', $attrs);

        /*  set CF2_DIAGNOSIS Child Nodes */
        $this->setDiagnosisDischarge();
        //todo foreach diagnosis
    }

    /**
     * Constructs the 'DISCHARGE' node including its child nodes (parent node: DIAGNOSIS)
     *
     * @return void
     */
    protected function setDiagnosisDischarge() {

//        $diagnoses = $this->diagnoses;
        $diagnoses = $this->careDiagnoses;
        $operations = $this->operations;

        /* Get RVS Codes */
        $operationDetails = array();
        foreach ($operations as $operation) {
            $details = $operation->details;
            foreach ($details as $detail) {
                $operationDetails[] = $detail;
            }
        }

        /** Get ICD Codes */
        $diagnosisDetails = array();
        foreach ($diagnoses as $diagnosis) {

//            if ($diagnosis->package) {
//                $_package_desc = trim($diagnosis->package->group);
//                $_package_desc = preg_replace('[\n]', ' ', $_package_desc);
//
//                // Group diagnosis by package
//                if (!isset($diagnosisDetails[$_package_desc])) {
//                    $diagnosisDetails[$_package_desc] = array();
//                }
//
//                /**
//                 * Use Case Rate Group as pDischargeDiagnosis value
//                 * @todo  Verify if this is correct
//                 */
//                $diagnosisDetails[$_package_desc][] = $diagnosis;
//            } else {
                // No group...
                if (trim($diagnosis->diagnosis_description)) {
                    $description = strtoupper($diagnosis->diagnosis_description);
                } else {
                    if(empty($diagnosis->icd10->description)) {
                        $description = 'NONE';
                    } else {
                        $description = strtoupper($diagnosis->icd10->description);
                    }
                }
                $diagnosisDetails[$description] = array($diagnosis);
//            }

        }

        /**
         * @todo Matching of Operations to Diagnoses. For now treat both as a
         * separate DISCHARGE entry
         *
         */
        foreach ($diagnosisDetails as $group => $groupedDiagnoses) {
            $attr = array();
            $attrs['pDischargeDiagnosis'] = $group;
            $this->appendNode($this->diagnosis, $this->discharge, 'DISCHARGE', $attrs);
            $this->setDischargeIcdCodes($groupedDiagnoses);
         }

        /**
        * Mod and added by jeff 02-17-18 for sorting of dates on generating XML.
        */
        $opdate_only = array();
        foreach ($operationDetails as $ops) {
            $opdate_only[] = $ops;
        }

        $opdate_sort = array();
        foreach ($opdate_only as $key => $part) {
                $opdate_sort[$key] = date('Y-m-d',strtotime($part['op_date']));
         }
             array_multisort($opdate_sort, SORT_ASC, $opdate_only);

             foreach ($opdate_only as $key => $OP_date_sort) {
                  $attrs = array(
                        'pDischargeDiagnosis' => strtoupper($OP_date_sort->rvs->description)
                    );
                    $this->appendNode($this->diagnosis, $this->discharge, 'DISCHARGE', $attrs);
                    $this->setDischargeRvsCode($OP_date_sort);
             }
    }

    /**
     * Constructs a single 'ICDCODE' node including its child nodes (parent node: DISCHARGE)
     *
     * @param EncounterDiagnosis $diagnosis
     * @return void
     */
    protected function setDischargeIcdCodes($diagnoses) {
        foreach ($diagnoses as $diagnosis) {
            $attrs = array();
            $d = $diagnosis->package->alt_code;

            if (empty($d)) {
                $d = $diagnosis->code;
            }

            $attrs['pICDCode'] = $d;

            $this->appendNode($this->discharge, $this->icdcode, 'ICDCODE', $attrs);
        }
    }

    /**
     * Constructs a single 'RVSCODES' node including its child nodes (parent node: DISCHARGE)
     *
     * @param MiscellaneousOperationDetails $opsDetails
     * @return void
     */
    protected function setDischargeRvsCode($opsDetails) {
        $attrs = array();


        $attrs['pRelatedProcedure'] = substr(strtoupper($opsDetails->rvs->description), 0, 100);
        $attrs['pRVSCode'] = $opsDetails->rvs->code;
        $attrs['pProcedureDate'] = $this->formatter->formatDate($opsDetails['op_date']);
        $attrs['pLaterality'] = (($opsDetails->laterality != '') ? strtoupper($opsDetails->laterality) : 'N');
        $this->appendNode($this->discharge, $this->rvscodes, 'RVSCODES', $attrs);
    }

    /**
     * Constructs the 'SPECIAL' node including its child nodes (parent node: CF2)
     *
     * @return void
     */
    protected function setCf2Special() {

        //TODO
        $this->appendNode($this->cf2, $this->special, 'SPECIAL');

        /*  set CF2_DIAGNOSIS Child Nodes */
        $this->setSpecial_Procedures();
        $this->setSpecialNcp();

        //TODO
        // $this->setSpecialMcp();
        // $this->setSpecialTbdots();
        // $this->setSpecialAbp();
        // $this->setSpecialHIVAIDS();
    }


    protected function getSpecialDates($specialDates)
    {
        $dates = explode(',', trim($specialDates, ','));
        $arr_specialDates = array();
        foreach ($dates as $date) {
            array_push($arr_specialDates, $date);
        }
        return $arr_specialDates;
    }

    protected function setProcedure_Sessions(&$child, $procTitle, $specialDates)
    {
        # Mod by jeff 01-31-18 for proeper fetching of pSessionDate if Dialysis patient.
        $attrs = array();
        $this->appendNode($this->procedures, $child, $procTitle);
        foreach ($specialDates as $specialDate) {
            if (!$specialDate == '' || !$specialDate == NULL) {
                # Mod formatting of date.
                $attrs['pSessionDate'] = $this->formatter->formatDate($specialDate);
            $this->appendNode($child, $this->sessions, 'SESSIONS', $attrs);
        }
            else {
                # do nothing lang swah...
            }
        }
    }

    protected function addProcedures(&$arr_procedures, $procTitle, $dates)
    {
        $data = array(
            $procTitle,
            $dates
        );
        array_push($arr_procedures, $data);
    }

    protected function setSpecial_Procedures()
    {
        $enc = $this->encounter_nr;
        global $db;
        $arr_procedures = array();

        $considerations = array('Hemodialysis',
            'Dialysis procedure other than hemo dialysis',
            'linac',
            'cobalt',
            'blood_transfusion',
            'brachytherapy',
            'chemotherapy',
            'debridement');
        # Query Mod by jeff 01-31-18 for proper query of special dates and sorting on special dates.
        $sql = $db->Prepare("SELECT
                              smod.ops_code,
                              scrp.description,
                              smod.laterality,
                              smod.op_date,
                              TRIM(TRAILING ',' FROM
                              GROUP_CONCAT(
                                CONCAT(smod.op_date,',',TRIM(TRAILING ',' FROM IFNULL(smod.special_dates,'')))
                              order by CONCAT(smod.op_date,',',TRIM(TRAILING ',' FROM IFNULL(smod.special_dates,''))))) AS special_dates
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
                              INNER JOIN (SELECT * FROM
                                            (SELECT crp.* FROM
                                                seg_case_rate_packages crp 
                                                INNER JOIN seg_misc_ops_details od 
                                                  ON od.`ops_code` = crp.`code` 
                                                INNER JOIN seg_misc_ops mo 
                                                  ON mo.`refno` = od.`refno` 
                                              WHERE mo.`encounter_nr` = ?
                                             ORDER BY crp.`date_from` DESC
                                            ) t 
                                         GROUP BY t.code 
                                         HAVING COUNT(t.code) > 1
                                        ) AS scrp
                                ON smod.ops_code = scrp.code
                            WHERE ce.encounter_nr = ?
                              AND scrp.description REGEXP ?
                              AND scrp.special_case = 1
                            GROUP BY smod.ops_code
                            UNION ALL
                            SELECT
                              smod.ops_code,
                              scrp.description,
                              smod.laterality,
                              smod.op_date,
                              TRIM(TRAILING ',' FROM
                              GROUP_CONCAT(
                                CONCAT(smod.op_date,',',TRIM(TRAILING ',' FROM IFNULL(smod.special_dates,'')))
                              order by CONCAT(smod.op_date,',',TRIM(TRAILING ',' FROM IFNULL(smod.special_dates,''))))) AS special_dates
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
                              INNER JOIN (SELECT * FROM
                                            (SELECT crp.* FROM
                                                seg_case_rate_packages crp 
                                                INNER JOIN seg_misc_ops_details od 
                                                  ON od.`ops_code` = crp.`code` 
                                                INNER JOIN seg_misc_ops mo 
                                                  ON mo.`refno` = od.`refno` 
                                              WHERE mo.`encounter_nr` = ?
                                             ORDER BY crp.`date_from` DESC
                                            ) t 
                                         GROUP BY t.code 
                                         HAVING COUNT(t.code) <= 1
                                        ) AS scrp
                                ON smod.ops_code = scrp.code 
                            WHERE ce.encounter_nr = ? 
                              AND scrp.description REGEXP ? 
                              AND scrp.special_case = 1 
                            GROUP BY smod.ops_code 
                                ORDER BY description");
        foreach ($considerations as $ckey => $consideration) {
            $cons = array($enc,$enc, $consideration,$enc,$enc,$consideration);
            $rs = $db->Execute($sql, $cons);
            if ($rs) {
                if ($rs->RecordCount()) {
                    $rows = $rs->GetRows();
                    foreach ($rows as $rkey => $row) {
                        switch ($ckey) {
                            case 0:
                                $this->addProcedures($arr_procedures, 'HEMODIALYSIS', $row['special_dates']);
                                break;
                            case 1:
                                $this->addProcedures($arr_procedures, 'PERITONEAL', $row['special_dates']);
                                break;
                            case 2:
                                $this->addProcedures($arr_procedures, 'LINAC', $row['special_dates']);
                                break;
                            case 3:
                                $this->addProcedures($arr_procedures, 'COBALT', $row['special_dates']);
                                break;
                            case 4:
                                $this->addProcedures($arr_procedures, 'TRANSFUSION', $row['special_dates']);
                                break;
                            case 5:
                                $this->addProcedures($arr_procedures, 'BRACHYTHERAPHY', $row['special_dates']);
                                break;
                            case 6:
                                $this->addProcedures($arr_procedures, 'CHEMOTHERAPY', $row['special_dates']);
                                break;
                            case 7:
                                $this->addProcedures($arr_procedures, 'DEBRIDEMENT', $row['special_dates']);
                                break;
                        }
                    }
                }
            }
        }

        if (count($arr_procedures)) {
            $this->appendNode($this->special, $this->procedures, 'PROCEDURES');
            foreach ($arr_procedures as $pkey => $value) {
                $this->setProcedure_Sessions($proc, $value[0], $this->getSpecialDates($value[1]));
            }
        }
    }


    //TODO
    protected function setSpecialMcp() {
        $attrs = array();
        $attrs['pCheckUpDate1'] = '';
        $attrs['pCheckUpDate2'] = '';
        $attrs['pCheckUpDate3'] = '';
        $attrs['pCheckUpDate4'] = '';
        $this->appendNode($this->special, $this->mcp, 'MCP', $attrs);
    }

    //TODO
    protected function setSpecialTbdots() {
        $attrs = array();
        $attrs['pTBType'] = 'I';

        // I|M
        $attrs['pNTPCardNo'] = '0';
        $this->appendNode($this->special, $this->tbdots, 'TBDOTS', $attrs);
    }

    //TODO
    protected function setSpecialAbp() {
        $attrs = array();
        $attrs['pDay0ARV'] = '';
        $attrs['pDay3ARV'] = '';
        $attrs['pDay7ARV'] = '';
        $attrs['pRIG'] = '';
        $attrs['pABPOthers'] = '';
        $attrs['pABPSpecify'] = '';
        $this->appendNode($this->special, $this->abp, 'ABP', $attrs);
    }

    //TODO
    protected function setSpecialNcp() {

        $enc = $this->encounter_nr;
        $isNewBorn = $this->isNewBorn($enc);
        $isHearingTestAvailed = $this->isHearingTestAvailed($enc,$isNewBorn) && $isNewBorn;
        if ($isNewBorn) {
            $attrs = array();
            $attrs['pEssentialNewbornCare'] = ($isNewBorn) ? 'Y' : 'N'; // Y|N
            $attrs['pNewbornHearingScreeningTest'] = (($isHearingTestAvailed == 1) && $isNewBorn) ? 'Y' : 'N'; // Y|N
            $attrs['pNewbornScreeningTest'] = (($isHearingTestAvailed == 1) && $isNewBorn) ? 'N' : 'Y'; // Y|N
            $attrs['pFilterCardNo'] = '0';
            $this->appendNode($this->special, $this->ncp, 'NCP', $attrs);

            $attrs = array();
            $attrs['pDrying'] = ($isHearingTestAvailed == 1) ? 'Y' : 'N'; // Y|N
            $attrs['pSkinToSkin'] = ($isHearingTestAvailed == 1) ? 'Y' : 'N'; // Y|N
            $attrs['pCordClamping'] = ($isHearingTestAvailed == 1) ? 'Y' : 'N'; // Y|N
            $attrs['pProphylaxis'] = ($isHearingTestAvailed == 1) ? 'Y' : 'N'; // Y|N
            $attrs['pWeighing'] = ($isHearingTestAvailed == 1) ? 'Y' : 'N'; // Y|N
            $attrs['pVitaminK'] = ($isHearingTestAvailed == 1) ? 'Y' : 'N'; // Y|N
            $attrs['pBCG'] = ($isHearingTestAvailed == 1) ? 'Y' : 'N'; // Y|N
            $attrs['pNonSeparation'] = ($isHearingTestAvailed == 1) ? 'Y' : 'N'; // Y|N
            $attrs['pHepatitisB'] = ($isHearingTestAvailed == 1) ? 'Y' : 'N'; // Y|N
            $this->appendNode($this->ncp, $this->essential, 'ESSENTIAL', $attrs);
        }
    }

    //TODO
    protected function setSpecialHIVAIDS() {
        $attrs = array();
        $attrs['pLaboratoryNumber'] = '';
        $this->appendNode($this->special, $this->hivaids, 'HIVAIDS', $attrs);
    }

    /**
     * Constructs the 'PROFESSIONALS' node including its child nodes (parent node: CF2)
     * @return void
     */
    protected function setCf2Professionals() {

        //TODO CActiveRecord
        $encounter = $this->encounter;
        $enc = $this->encounter_nr;
        global $db;
        $this->isHouseCase($enc);
        $applied_discount = $this->getTotalAppliedDiscounts($enc);
        $result = $this->getDoctorInfo($enc);

        $attrs = array();

        if ($result) {
            /*
                To resolve duplicate professionals in a claim.
                Added by: Jolly Caralos
            */
            $mapProfessionals = array();

            while ($row = $result->FetchRow()) {

                $bill = new HospitalBill();
                $billing = $bill->findByPk($row['bill_nr']);

                # Mod by jeff 04-25-18 for direct generation of doctors...
                // if ($this->bhousecase  && $billing->accommodation_type =='2') { #comment out by monmon : workaround for showing doctor information
                //     // if (!$this->isDiffCase($row['bill_nr']) && $row['role_area'] == 'D1') {
                //     //     $result2 = $this->getHouseCaseDoctor('D3');
                //     // } else {
                //         $result2 = $this->getHouseCaseDoctor($row['role_area']);
                //     // }
                //     if ($result2) {
                //         while ($row2 = $result2->FetchRow()) {
                //             $acc_no = str_replace("-", "", trim($row2['acc_no']));
                //             $acc_no1 = substr_replace($acc_no, '-', 4, 0);
                //             $pDoctorLastName = $row2['pDoctorLastName'];
                //             $pDoctorFirstName = $row2['pDoctorFirstName'];
                //             $pDoctorMiddleName = $row2['pDoctorMiddleName'];
                //             $pDoctorSuffix = $row2['pDoctorSuffix'];
                //         }
                //     }
                // } else {
                //     $acc_no = str_replace("-", "", trim($row['acc_no']));
                //     $acc_no1 = substr_replace($acc_no, '-', 4, 0);
                //     $pDoctorLastName = $row['pDoctorLastName'];
                //     $pDoctorFirstName = $row['pDoctorFirstName'];
                //     $pDoctorMiddleName = $row['pDoctorMiddleName'];
                //     $pDoctorSuffix = $row['pDoctorSuffix'];
                // }
                # END jeff ---

                $acc_no = str_replace("-", "", trim($row['acc_no']));
                $acc_no1 = substr_replace($acc_no, '-', 4, 0);
                $pDoctorLastName = $row['pDoctorLastName'];
                $pDoctorFirstName = $row['pDoctorFirstName'];
                $pDoctorMiddleName = $row['pDoctorMiddleName'];
                $pDoctorSuffix = $row['pDoctorSuffix'];

                $doc_discount = $row['dr_charge'] * $applied_discount;
                $copay_amount = $row['dr_charge'] - $doc_discount - $row['dr_claim'];

                # Mod by JEFF 01-18-18 fetching for describing not accredited doctors.
                $acc_noData = substr_replace($acc_no1, '-', -1, 0);

                $attrs['pDoctorAccreCode'] = ($acc_noData == '--')?'NOT ACCREDITED' : $acc_noData;
                $attrs['pDoctorLastName'] = $pDoctorLastName;
                $attrs['pDoctorFirstName'] = $pDoctorFirstName;
                // $attrs['pDoctorMiddleName'] = $pDoctorMiddleName;
                $attrs['pDoctorMiddleName'] = (trim($pDoctorMiddleName)) ? trim($pDoctorMiddleName) : 'N/A';
                $attrs['pDoctorSuffix'] = $pDoctorSuffix;

                if ($copay_amount <= 0) {
                    $attrs['pWithCoPay'] = 'N';
                    $attrs['pDoctorCoPay'] = '0.00';
                } else {
                    $attrs['pWithCoPay'] = 'Y';
                    $attrs['pDoctorCoPay'] = number_format($copay_amount, 2, '.', '');
                }

//                $attrs['pDoctorSignDate'] = $this->getCalculateDate($row['bill_dte']);
                $attrs['pDoctorSignDate'] = $this->formatter->formatDate($encounter->pullDischargeDate());


                if(empty($mapProfessionals[$attrs['pDoctorAccreCode']]))
                    $professionals = array();
                else
                    $professionals = $mapProfessionals[$attrs['pDoctorAccreCode']];

                $mapProfessionals[$attrs['pDoctorAccreCode']] = $this->_resolveProfessional($attrs, $professionals);
            }

            /* Append mapped and resolved Professional attributes */
            foreach($mapProfessionals as $professional)
            {
                $this->appendNode($this->cf2, $this->proffees, 'PROFESSIONALS', $professional);
            }
        } else {
            $attrs['pDoctorAccreCode'] = 'NONE';
            $attrs['pDoctorLastName'] = 'NONE';
            $attrs['pDoctorFirstName'] = 'NONE';
            $attrs['pDoctorMiddleName'] = 'NONE';
            $attrs['pDoctorSuffix'] = 'NONE';
            $attrs['pWithCoPay'] = 'N';
            $attrs['pDoctorCoPay'] = '0.00';
            $attrs['pDoctorSignDate'] = 'NONE';
            $this->appendNode($this->cf2, $this->proffees, 'PROFESSIONALS', $attrs);
        }
    }

    protected function applyHouseCaseProfessional()
    {
        $bill = HospitalBill::model()->findByAttributes(array(
            'encounter_nr' => $this->encounter_nr
        ));
        $billHasHouseDoctor = HospitalBill::model()->billHasHouseDoctor($enc);

        // Do not add HouseCase Professional
        if (!$bill->isHouseCase())
        {
            return;
        }

        if(!$billHasHouseDoctor) {
            return;
        }

        $doctor = Personnel::model()->findByPk(Config::get('house_doctor')->value);

        $acc_no = DoctorAccreditation::model()->findByAttributes(array(
            'dr_nr' => $doctor->nr,
            'hcare_id' => self::HCARE_ID
        ));

        $acc_no = str_replace("-", "", trim($acc_no->accreditation_nr));
        $acc_no = substr_replace($acc_no, '-', 4, 0);
        $acc_no = substr_replace($acc_no, '-', -1, 0);

        $data = array(
            'pWithCoPay' => 'N',
            'pDoctorCoPay' => '0.00',
            'pDoctorAccreCode' => ($acc_no == '--')?'NOT ACCREDITED' : $acc_no,
            'pDoctorLastName' => $doctor->person->name_last,
            'pDoctorFirstName' => $doctor->person->name_first,
            'pDoctorMiddleName' => $doctor->person->name_middle,
            'pDoctorSuffix' => $doctor->person->suffix,
            'pDoctorSignDate' => $this->getCalculateDate($bill->bill_dte),
        );

        $this->appendNode($this->cf2, $this->proffees, 'PROFESSIONALS', $data);
    }

    /**
     * [_resolveProfessional description]
     * @param  [type] $attr     [description]
     * @param  array  $professional professional attr with the same accreditation_no
     * @return [type]           [description]
     */
    private function _resolveProfessional($newAttr, $oldAttr = array())
    {
        /* Default Values */
        $resolve = array(
            'pWithCoPay' => 'N',
            'pDoctorCoPay' => '0.00',
            'pDoctorSignDate' => 'NONE',
        );
        if(empty($oldAttr)) {
            $resolve = $newAttr;
        } else {
            /* pWithCoPay */
            if(in_array('Y', array($newAttr['pWithCoPay'], $oldAttr['pWithCoPay'])))
                $resolve['pWithCoPay'] = 'Y';

            /* pDoctorCoPay */
            $sumDoctorCoPay = $newAttr['pDoctorCoPay'] + $oldAttr['pDoctorCoPay'];
            $resolve['pDoctorCoPay'] = number_format($sumDoctorCoPay, 2, '.', '');

            /* pDoctorSignDate */
            $resolve['pDoctorSignDate'] = (strcasecmp($newAttr['pDoctorSignDate'], $oldAttr['pDoctorSignDate']) >= 0)
                ? $newAttr['pDoctorSignDate'] : $oldAttr['pDoctorSignDate'];

            $resolve = CMap::mergeArray($newAttr, $resolve);
        }
        return $resolve;
    }

    /**
     * Constructs the 'CONSUMPTION' node including its child nodes (parent node: CF2)
     * @return void
     */

    protected function setCf2Consumption()
    {

        //TODO CActiveRecord
        $enc = $this->encounter_nr;
        global $db;
        $sql = $db->Prepare("SELECT
                  sbe.accommodation_type,
                  sbe.bill_dte,
                  sbc.`total_services_coverage`,
                  sbe.`total_doc_charge`,
                  SUM(
                    IFNULL(sbc.`total_d1_coverage`, 0) + 
                    IFNULL(sbc.`total_d2_coverage`, 0) + 
                    IFNULL(sbc.`total_d3_coverage`, 0) + 
                    IFNULL(sbc.`total_d4_coverage`, 0)
                  ) AS total_doc_coverage,
                  SUM(
                    IFNULL(sbe.`total_acc_charge`, 0) + 
                    IFNULL(sbe.`total_med_charge`, 0) + 
                    IFNULL(sbe.`total_ops_charge`, 0) + 
                    IFNULL(sbe.`total_msc_charge`, 0) + 
                    IFNULL(sbe.`total_srv_charge`, 0) + 
                    IFNULL(sbe.`total_sup_charge`, 0)
                  ) AS total_hci_charge,
                  SUM(
                    IFNULL(sbd.`total_acc_discount`, 0) + 
                    IFNULL(sbd.`total_med_discount`, 0) + 
                    IFNULL(sbd.`total_sup_discount`, 0) +
                    IFNULL(sbd.`total_srv_discount`, 0) + 
                    IFNULL(sbd.`total_ops_discount`, 0) + 
                    IFNULL(sbd.`total_msc_discount`, 0)  
                  ) AS total_hci_discount,
                  SUM(
                    IFNULL(sbd.`total_d1_discount`, 0) + 
                    IFNULL(sbd.`total_d2_discount`, 0) + 
                    IFNULL(sbd.`total_d3_discount`, 0) + 
                    IFNULL(sbd.`total_d4_discount`, 0)
                  ) AS total_doc_discount,
                  SUM(
                    IFNULL(sbd.`hospital_income_discount`, 0) + 
                    IFNULL(sbd.`total_msc_discount`, 0)
                  ) AS total_hci_discount_old,
                  sem.memcategory_id,
                  ser.total_meds,
                  ser.total_xlo,
                  (SELECT 
                    SUM(sbca.coverage)
                  FROM
                    seg_billingcoverage_adjustment sbca 
                  WHERE sbca.hcare_id = '" . self::HCARE_ID . "'
                    AND sbca.ref_no = sbe.bill_nr) AS total_hci_phic_coverage,
                  (SELECT 
                    SUM(sbp.dr_claim)
                  FROM
                    seg_billing_pf sbp 
                  WHERE sbp.hcare_id = '" . self::HCARE_ID . "' 
                    AND sbp.bill_nr = sbe.bill_nr) AS total_doc_phic_coverage,
                  (SELECT 
                    MAX(
                      IF(
                        b.`hcare_id` IN 
                        (SELECT 
                          hcare_id 
                        FROM
                          care_insurance_firm 
                        WHERE type_nr = '2'),
                        'Y',
                        'N'
                      )
                    ) AS `hmo` 
                  FROM
                    `care_insurance_firm` a 
                    LEFT JOIN seg_billing_pf b 
                      ON b.`hcare_id` = a.`hcare_id` 
                  WHERE (
                      a.`status` != 'deleted' 
                      OR a.`status` IS NULL
                    ) 
                    AND b.`bill_nr` = sbc.`bill_nr`) AS `pf_hmo`,
                  (SELECT 
                    MAX(
                      IF(
                        b.`hcare_id` IN 
                        (SELECT 
                          hcare_id 
                        FROM
                          care_insurance_firm 
                        WHERE type_nr = '3'),
                        'Y',
                        'N'
                      )
                    ) AS `hmo` 
                  FROM
                    `care_insurance_firm` a 
                    LEFT JOIN seg_billing_pf b 
                      ON b.`hcare_id` = a.`hcare_id` 
                  WHERE (
                      a.`status` != 'deleted' 
                      OR a.`status` IS NULL
                    ) 
                    AND b.`bill_nr` = sbc.`bill_nr`) AS `pf_other`,
                    (SELECT 
                    MAX(
                      IF(
                        b.`hcare_id` IN 
                        (SELECT 
                          hcare_id 
                        FROM
                          care_insurance_firm 
                        WHERE type_nr = '2'),
                        'Y',
                        'N'
                      )
                    ) AS `hmo` 
                  FROM
                    `care_insurance_firm` a 
                    LEFT JOIN seg_billingcoverage_adjustment b 
                      ON b.`hcare_id` = a.`hcare_id` 
                  WHERE (
                      a.`status` != 'deleted' 
                      OR a.`status` IS NULL
                    ) 
                    AND b.`ref_no` = sbc.`bill_nr`) AS `hci_hmo`,
                  (SELECT 
                    MAX(
                      IF(
                        b.`hcare_id` IN 
                        (SELECT 
                          hcare_id 
                        FROM
                          care_insurance_firm 
                        WHERE type_nr = '3'),
                        'Y',
                        'N'
                      )
                    ) AS `hmo` 
                  FROM
                    `care_insurance_firm` a 
                    LEFT JOIN seg_billingcoverage_adjustment b 
                      ON b.`hcare_id` = a.`hcare_id` 
                  WHERE (
                      a.`status` != 'deleted' 
                      OR a.`status` IS NULL
                    ) 
                    AND b.`ref_no` = sbc.`bill_nr`) AS `hci_other` 
                FROM
                  seg_billing_encounter sbe 
                  INNER JOIN seg_billing_coverage sbc 
                    ON sbe.bill_nr = sbc.`bill_nr` 
                    AND sbc.hcare_id = '" . self::HCARE_ID . "' 
                  INNER JOIN seg_billingcomputed_discount sbd 
                    ON sbd.`bill_nr` = sbe.`bill_nr` 
                  LEFT JOIN seg_encounter_memcategory sem 
                    ON sem.encounter_nr = sbe.encounter_nr 
                  LEFT JOIN seg_encounter_reimbursed ser 
                    ON ser.encounter_nr = sbe.encounter_nr 
                WHERE sbe.`encounter_nr` = ?
                  AND sbe.is_deleted IS NULL 
                  AND sbe.is_final = '1' ");

        if ($result = $db->Execute($sql, $enc)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $total_doc_charge = $row['total_doc_charge'];
                $total_doc_discount = $row['total_doc_discount'];
//                $total_doc_coverage = $row['total_doc_coverage'];
                $total_doc_coverage = $row['total_doc_phic_coverage'];
                $total_hci_charge = $row['total_hci_charge'];
                $total_hci_discount = $row['total_hci_discount'];
//                $total_hci_coverage = $row['total_services_coverage'];
                $total_hci_coverage = $row['total_hci_phic_coverage'];
                $patient_name = $row['name'];
                $total_charge = $row['total_doc_charge'] + $row['total_hci_charge'];
                $total_coverage = $row['total_doc_coverage'] + $row['total_services_coverage'];
                $total_discount = $row['total_doc_discount'] + $row['total_hci_discount'];
                $excess = $total_charge - $total_discount - $total_coverage;
                $memcategory_id = $row['memcategory_id'];
                $is_discharged = $row['is_discharged'];
                $total_meds = (($row['total_meds']) ? $row['total_meds'] : 0);
                $total_xlo = (($row['total_xlo']) ? $row['total_xlo'] : 0);
                $total_outside = $total_meds + $total_xlo;
                $bill_dte = $row['bill_dte'];
                $charity = (($row['accommodation_type']=='1') ? true : false);
                $hci_hmo = $row['hci_hmo'];
                $hci_other = $row['hci_other'];
                $pf_hmo = $row['pf_hmo'];
                $pf_other = $row['pf_other'];
            }
        }

        if (($excess <= 0 || (($row['memcategory_id'] == self::HSM || $row['memcategory_id'] == self::SM) && $charity)) && ($total_outside <= 0)) {
            $attrs1 = array();
            $attrs2 = array();
            $attrs1['pEnoughBenefits'] = 'Y';
            $attrs2['pTotalHCIFees'] = number_format($total_hci_charge, 2, '.', '');
            $attrs2['pTotalProfFees'] = number_format($total_doc_charge, 2, '.', '');
            $attrs2['pGrandTotal'] = number_format($total_charge, 2, '.', '');
            $this->appendNode($this->cf2, $this->consumption, 'CONSUMPTION', $attrs1);
            $this->appendNode($this->consumption, $this->benefits, 'BENEFITS', $attrs2);
        } else {
            $attrs1 = array();
            $attrs_hci = array();
            $attrs_doc = array();
            $attrs_purchase = array();

            $attrs1['pEnoughBenefits'] = 'N';
            $attrs_hci['pTotalActualCharges'] = number_format($total_hci_charge, 2, '.', '');
            $attrs_hci['pDiscount'] = (($total_hci_discount != 0) ? number_format($total_hci_charge - $total_hci_discount, 2, '.', '') : '0');
            $attrs_hci['pPhilhealthBenefit'] = number_format($total_hci_coverage, 2, '.', '');
            $attrs_hci['pTotalAmount'] = $total_hci_charge - ($total_hci_coverage) - $total_hci_discount;
            $attrs_hci['pMemberPatient'] = 'Y'; // Y|N
            $attrs_hci['pHMO'] = $hci_hmo; // Y|N
            $attrs_hci['pOthers'] = $hci_other; // Y|N

            $attrs_doc['pTotalActualCharges'] = number_format($total_doc_charge, 2, '.', '');
            $attrs_doc['pDiscount'] = (($total_doc_discount != 0) ? number_format($total_doc_charge - $total_doc_discount, 2, '.', '') : '0');
            $attrs_doc['pPhilhealthBenefit'] = number_format($total_doc_coverage, 2, '.', '');
            $attrs_doc['pTotalAmount'] = $total_doc_charge - ($total_doc_discount + $total_doc_coverage);
            $attrs_doc['pMemberPatient'] = 'Y'; // Y|N
            $attrs_doc['pHMO'] = $pf_hmo; // Y|N
            $attrs_doc['pOthers'] = $pf_other; // Y|N

            $attrs_purchase['pDrugsMedicinesSupplies'] = (($total_meds <= 0) ? 'N' : 'Y'); // Y|N
            $attrs_purchase['pDMSTotalAmount'] = number_format($total_meds, 2, '.', '');
            $attrs_purchase['pExaminations'] = (($total_xlo <= 0) ? 'N' : 'Y'); // Y|N
            $attrs_purchase['pExamTotalAmount'] = number_format($total_xlo, 2, '.', '');

            $this->appendNode($this->cf2, $this->consumption, 'CONSUMPTION', $attrs1);
            $this->appendNode($this->consumption, $this->hcifees, 'HCIFEES', $attrs_hci);
            $this->appendNode($this->consumption, $this->proffees, 'PROFFEES', $attrs_doc);
            $this->appendNode($this->consumption, $this->purchases, 'PURCHASES', $attrs_purchase);

        }
    }

    /**
     * Constructs the 'ALLCASERATE' node including its child nodes (parent node: CF2)
     *
     * @return void
     */
    protected function setCaseTypeNode() {

        //TEMPORARY
        $this->appendNode($this->claim, $this->allcaserate, 'ALLCASERATE');

        // CVarDumper::dump($this->dom->saveXml($document->documentElement), 10, true); die;

        $this->setCaseRate();

        //if CLAIM_TYPE = 'Z-BENEFIT'
        // $attrs['pZBenefitCode'] = ''
        // $this->appendNode($this->claim, $this->zbenefit, 'ZBENEFIT', $attrs);

    }

    /**
     * Constructs the 'CASERATE' node including its child nodes (parent node: ALLCASERATE)
     * @return void
     */
    protected function setCaseRate() {

        //TODO CActiveRecord
        $enc = $this->encounter->encounter_nr;

        global $db;
        $sql = $db->Prepare("SELECT p.code AS package_id, p.package, sbc.rate_type, p.case_type, sca.acr_groupid
            FROM seg_billing_encounter sbe
            INNER JOIN seg_billing_caserate sbc
                ON sbe.bill_nr=sbc.bill_nr
            INNER JOIN seg_case_rate_packages p
                ON p.code = sbc.package_id
            INNER JOIN seg_caserate_acr AS sca
                ON sca.package_id = sbc.package_id
            WHERE sbe.is_final = 1
            AND p.date_to <> '2016-05-06'
            AND sbe.is_deleted IS NULL
            AND encounter_nr = ? GROUP BY sbc.package_id
             ORDER BY sbc.rate_type");

        if ($result = $db->Execute($sql, $enc)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $attrs['pCaseRateCode'] = ($row['acr_groupid']) ? $row['acr_groupid'] : "";
                    $attrs['pICDCode'] = (($row['case_type'] == 'm') ? $row['package_id'] : '');
                    $attrs['pRVSCode'] = (($row['case_type'] == 'p') ? $row['package_id'] : '');
                    $attrs['pCaseRateAmount'] = number_format($row['package'], 2, ".", "");
                    $this->appendNode($this->allcaserate, $this->caserate, 'CASERATE', $attrs);


                    # Added by Jeff 02-22-18
                    $codesResult = array();
                    $codesResult = $this->checkRVSeclaims();
                    $codesResult = explode(',', $codesResult);
                    $pack_id = $row['package_id'];

                    // CVarDumper::dump($codesResult);die();
                    if(!empty($pack_id)) {
                        foreach($codesResult as $value) {
                            if ($value == $pack_id) {
                               $this->getCataract();
                            }
                        }
                    }
                    # End Jeff ---
                }
            }
        }
    }

    //TODO
    protected function setCf3Node() {

        $this->appendNode($this->claim, $this->cf3, 'CF3', $attrs);

        /* set CF3 ChildNodes */
        $this->setCf3Old();
        $this->setCf3New();
    }

    //TODO
    protected function setCf3Old() {
        $attrs['pChiefComplaint'] = '';
        $attrs['pBriefHistory'] = '';
        $attrs['pCourseWard'] = '';
        $attrs['pPertinentFindings'] = '';
        $this->appendNode($this->cf3, $this->cf3_old, 'CF3_OLD', $attrs);

        /* set CF3_OLD ChildNodes */
        $this->setCf3OldPhex();
        $this->setCf3OldMaternity();
    }

    //TODO
    protected function setCf3New() {
        $this->appendNode($this->cf3, $this->cf3_new, 'CF3_NEW', $attrs);

        /* set CF3_NEW ChildNodes */
        $this->setCf3NewAdmitReason();
        $this->setCf3NewCourse();
    }

    //TODO
    protected function setCf3OldPhex() {

        //TODO

        $attrs['pBP'] = '';
        $attrs['pCR'] = '';
        $attrs['pRR'] = '';
        $attrs['pTemp'] = '';
        $attrs['pHEENT'] = '';
        $attrs['pChestLungs'] = '';
        $attrs['pCVS'] = '';
        $attrs['pAbdomen'] = '';
        $attrs['pGUIE'] = '';
        $attrs['pSkinExtremities'] = '';
        $attrs['pNeuroExam'] = '';
        $this->appendNode($this->cf3, $this->phex, 'PHEX', $attrs);
    }

    //TODO
    protected function setCf3OldMaternity() {
        $this->appendNode($this->cf3, $this->maternity, 'MATERNITY', $attrs);

        $this->setMaternityPrenatal();
        $this->setMaternityDelivery();
        $this->setMaternityPostpartum();
    }

    //TODO
    protected function setMaternityPrenatal() {
        $attrs['pPrenatalConsultation'] = '';
        $attrs['pMCPOrientation'] = 'N';

        // Y|N
        $attrs['pExpectedDeliveryDate'] = '';
        $this->appendNode($this->maternity, $this->prenatal, 'PRENATAL', $attrs);

        /* set PRENATAL Child Nodes */
        $this->setPrenatalClinicalHist();
        $this->setPrenatalObstetric();
        $this->setPrenatalMediSurg();
        $this->setPrenatalConsultation();
    }

    //TODO
    protected function setPrenatalClinicalHist() {
        $attrs['pVitalSigns'] = 'N';

        // Y|N
        $attrs['pPregnancyLowRisk'] = 'N';

        // Y|N
        $attrs['pLMP'] = '';
        $attrs['pMenarcheAge'] = '';
        $attrs['pObstetricG'] = '';
        $attrs['pObstetricP'] = '';
        $attrs['pObstetric_T'] = '';
        $attrs['pObstetric_P'] = '';
        $attrs['pObstetric_A'] = '';
        $attrs['pObstetric_L'] = '';
        $this->appendNode($this->prenatal, $this->clinicalhist, 'CLINICALHIST', $attrs);
    }

    //TODO
    protected function setPrenatalObstetric() {
        $attrs['pMultiplePregnancy'] = 'N';

        // Y|N
        $attrs['pOvarianCyst'] = 'N';

        // Y|N
        $attrs['pMyomaUteri'] = 'N';

        // Y|N
        $attrs['pPlacentaPrevia'] = 'N';

        // Y|N
        $attrs['pMiscarriages'] = 'N';

        // Y|N
        $attrs['pStillBirth'] = 'N';

        // Y|N
        $attrs['pPreEclampsia'] = 'N';

        // Y|N
        $attrs['pEclampsia'] = 'N';

        // Y|N
        $attrs['pPrematureContraction'] = 'N';

        // Y|N
        $this->appendNode($this->prenatal, $this->clinicalhist, 'OBSTETRIC', $attrs);
    }

    //TODO
    protected function setPrenatalMediSurg() {
        $attrs['pHypertension'] = 'N';

        // Y|N
        $attrs['pHeartDisease'] = 'N';

        // Y|N
        $attrs['pDiabetes'] = 'N';

        // Y|N
        $attrs['pThyroidDisaster'] = 'N';

        // Y|N
        $attrs['pObesity'] = 'N';

        // Y|N
        $attrs['pAsthma'] = 'N';

        // Y|N
        $attrs['pEpilepsy'] = 'N';

        // Y|N
        $attrs['pRenalDisease'] = 'N';

        // Y|N
        $attrs['pBleedingDisorders'] = 'N';

        // Y|N
        $attrs['pPreviousCS'] = 'N';

        // Y|N
        $attrs['pUterineMyomectomy'] = 'N';

        // Y|N
        $this->appendNode($this->prenatal, $this->medisurg, 'MEDISURG', $attrs);
    }

    //TODO
    protected function setPrenatalConsultation() {

        $attrs['pVisitDate'] = '';
        $attrs['pAOGWeeks'] = '';
        $attrs['pWeight'] = '';
        $attrs['pCardiacRate'] = '';
        $attrs['pRespiratoryRate'] = '';
        $attrs['pBloodPressure'] = '';
        $attrs['pTemperature'] = '';
        $this->appendNode($this->prenatal, $this->consultation, 'CONSULTATION', $attrs);
    }

    //TODO
    protected function setMaternityDelivery() {
        $attrs['pDeliveryDate'] = '';
        $attrs['pDeliveryTime'] = '';
        $attrs['pObstetricIndex'] = '';
        $attrs['pAOGLMP'] = '';
        $attrs['pDeliveryManner'] = '';
        $attrs['pPresentation'] = '';
        $attrs['pFetalOutcome'] = '';
        $attrs['pSex'] = '';
        $attrs['pBirthWeight'] = '';
        $attrs['pAPGARScore'] = '';
        $attrs['pPostpartum'] = '';
        $this->appendNode($this->maternity, $this->delivery, 'DELIVERY', $attrs);
    }

    //TODO
    protected function setMaternityPostpartum() {
        $attrs['pPerinealWoundCare'] = 'N';

        // Y|N
        $attrs['pPerinealRemarks'] = '';
        $attrs['pMaternalComplications'] = 'N';

        // Y|N
        $attrs['pMaternalRemarks'] = '';
        $attrs['pBreastFeeding'] = 'N';

        // Y|N
        $attrs['pBreastFeedingRemarks'] = '';
        $attrs['pFamilyPlanning'] = 'N';

        // Y|N
        $attrs['pFamilyPlanningRemarks'] = '';
        $attrs['pPlanningService'] = 'N';

        // Y|N
        $attrs['pPlanningServiceRemarks'] = '';
        $attrs['pSurgicalSterilization'] = 'N';

        // Y|N
        $attrs['pSterilizationRemarks'] = '';
        $attrs['pFollowupSchedule'] = 'N';

        // Y|N
        $attrs['pFollowupScheduleRemarks'] = '';
        $this->appendNode($this->maternity, $this->delivery, 'POSTPARTUM', $attrs);
    }

    //TODO
    protected function setCf3NewAdmitReason() {
        $encounter = $this->encounter;

        $attrs['pBriefHistory'] = '';
        $attrs['pReferredReason'] = '';
        $attrs['pIntensive'] = 'N';

        // Y|N
        $attrs['pMaintenance'] = 'N';

        // Y|N

        $this->appendNode($this->cf3, $this->admitreason, 'ADMITREASON', $attrs);

        $this->setAdmitReasonClinical();
        $this->setAdmitReasonLabDiag();
        $this->setAdmitReasonPhex();
    }

    //TODO
    protected function setAdmitReasonClinical() {
        $attrs['pCriteria'] = '';
         //todo
        $this->appendNode($this->admitreason, $this->clinical, 'CLINICAL', $attrs);
    }

    //TODO
    protected function setAdmitReasonLabDiag() {
        $attrs['pCriteria'] = '';
         //todo
        $this->appendNode($this->admitreason, $this->labdiag, 'LABDIAG', $attrs);
    }

    //TODO
    protected function setAdmitReasonPhex() {
        $attrs['pBP'] = '';
        $attrs['pCR'] = '';
        $attrs['pRR'] = '';
        $attrs['pTemp'] = '';
        $attrs['pHEENT'] = '';
        $attrs['pChestLungs'] = '';
        $attrs['pCVS'] = '';
        $attrs['pAbdomen'] = '';
        $attrs['pGUIE'] = '';
        $attrs['pSkinExtremities'] = '';
        $attrs['pNeuroExam'] = '';
        $this->appendNode($this->admitreason, $this->phex, 'PHEX', $attrs);
    }

    //TODO
    protected function setCf3NewCourse() {
        $this->appendNode($this->cf3, $this->course, 'COURSE', null);
        $this->setCourseWard();
    }

    //TODO
    protected function setCourseWard() {
        $attrs['pCourseDate'] = '';
        $attrs['pFindings'] = '';
        $attrs['pAction'] = '';

        $this->appendNode($this->course, $this->ward, 'WARD', $attrs);
    }

    /**
     * Constructs the 'PARTICULARS' node including its child nodes (parent node: claim)
     * @return void
     */
    protected function setParticularsNode() {
        $this->appendNode($this->claim, $this->particulars, 'PARTICULARS', null);

        /* set PARTICULARS Child Nodes */
        $this->setParticularsDrgMed();
        $this->setParticularsXlso();
    }

    //TODO
    protected function setParticularsDrgMed() {
        $attrs['pPurchaseDate'] = '';
        $attrs['pDrugCode'] = '';
        $attrs['pPNDFCode'] = '';
        $attrs['pGenericName'] = '';
        $attrs['pBrandName'] = '';
        $attrs['pPreparation'] = '';
        $attrs['pQuantity'] = '';
        $this->appendNode($this->particulars, $this->drgmed, 'DRGMED', $attrs);
    }

    //TODO
    protected function setParticularsXlso() {
        $attrs['pDiagnosticDate'] = '';
        $attrs['pDiagnosticType'] = 'OTHERS';

        // IMAGING|LABORATORY|SUPPLIES|OTHERS
        $attrs['pDiagnosticName'] = '';
        $attrs['pQuantity'] = '';
        $this->appendNode($this->particulars, $this->xlso, 'XLSO', $attrs);
    }

    //TODO
    protected function setDocumentsNode() {
        $this->appendNode($this->claim, $this->documents, 'DOCUMENTS');
        if(!empty($this->attachments)){
            foreach ($this->attachments as $attachment) {
            $this->appendNode($this->documents, $this->document, 'DOCUMENT', array(
                'pDocumentType' => $attachment->attachment_type,
                'pDocumentURL' => $attachment->getUrl()
            ));
            }
        }else{
            $this->appendNode($this->documents, $this->document, 'DOCUMENT', array(
                'pDocumentType' => self::DEFAULT_DOCTYPE,
                'pDocumentURL' => self::DEFAULT_DOCURL
            ));
        }

    }

    //TODO
    protected function setReceiptsNode() {
        $this->appendNode($this->claim, $this->receipts, 'RECEIPTS');

        /* set RECEIPTS Child Nodes */
        $this->setReceiptsReceipt();
    }

    //TODO
    protected function setReceiptsReceipt() {
        $attrs['pCompanyName'] = '';
        $attrs['pCompanyTIN'] = '';
        $attrs['pBIRPermitNumber'] = '';
        $attrs['pReceiptNumber'] = '';
        $attrs['pReceiptDate'] = '';
        $attrs['pVATExemptSale'] = '';
        $attrs['pVAT'] = '';
        $attrs['pTotal'] = '';
        $this->appendNode($this->receipts, $this->receipt, 'RECEIPT', $attrs);

        /* set RECEIPT Child Nodes */
        $this->setReceiptItem();
    }

    //TODO
    protected function setReceiptItem() {
        $attrs['pQuantity'] = '';
        $attrs['pUnitPrice'] = '';
        $attrs['pDescription'] = '';
        $attrs['pAmount'] = '';
        $this->appendNode($this->receipt, $this->item, 'ITEM', $attrs);
    }

    /**** MISCELLANEOUS METHODS ****/
    protected function isWellBaby() {
        global $db;

        $enc_type = 0;
        $strSQL = "select encounter_type " . "   from care_encounter " . "   where encounter_nr = '" . $this->current_enr . "'";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $enc_type = $row['encounter_type'];
            }
        }

        return ($enc_type == self::WELLBABY);
    }

    protected function isNewBorn($enc) {
        global $db;
        $strSQL = $db->Prepare("SELECT package_id
                    FROM seg_billing_encounter sbe
                    INNER JOIN seg_billing_caserate sbc
                        ON sbe.bill_nr=sbc.bill_nr
                    WHERE sbe.is_final = 1
                    AND sbe.is_deleted IS NULL
                    AND encounter_nr = ?");

        if ($result = $db->Execute($strSQL, $enc)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    if ($row['package_id'] == self::NEWBORN) return true;
                    else return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @author Nick B. Alcala
     * Identify if patient is new born
     * Created On 4/21/2014
     * @param  String $enc
     * @return boolean
     */
    protected function isNewBorn2($enc) {
        global $db;
        $this->sql = $db->Prepare("SELECT
                                      smod.ops_code
                                    FROM
                                      seg_misc_ops AS smo
                                      INNER JOIN seg_misc_ops_details AS smod
                                        ON smod.refno = smo.refno
                                      INNER JOIN seg_case_rate_special AS scrs
                                        ON scrs.sp_package_id = smod.ops_code
                                    WHERE smo.encounter_nr = " . $db->qstr($enc));
        $row = $db->GetRow($this->sql);
        return (count($row)) ? true : false;
    }

    /**
     * @author Nick B. Alcala
     * Identify if patient (new born) availed the hearing test
     * Created On 4/22/2014
     * Legend:
     * -
     * @param  String $enc
     * @return boolean
     */
    protected function isHearingTestAvailed($enc, $isWellBaby) {
        global $db;

        /* default with hearing test */
        $sql = $db->Prepare("SELECT
            scrs.*
          FROM
            seg_caserate_hearing_test AS scrs
          WHERE scrs.`encounter_nr` = ?");

        if ($isWellBaby) {
            $rs = $db->Execute($sql, $enc);
            if ($rs) {
                if ($rs->RecordCount() > 0) {
                    $row = $rs->FetchRow();
                    return $row['is_availed'];
                } else {
                    $this->sql = $db->Prepare("INSERT INTO seg_caserate_hearing_test (encounter_nr,is_availed) VALUES (?,1)");
                    $rs = $db->Execute($this->sql, $enc);
                    if ($rs) {
                        return 1;
                    } else {
                        return 2;
                    }
                }
            } else {
                return 2;
            }
        }
    }

    /**
     * Updated by Nick, 4/23/2014
     * Join with seg_case_rate_special
     */
    protected function hasSavedPackage($bill_nr, $rtype) {
        global $db;

        $sql = "SELECT
                sbc.*,
                scrs.`sp_package_id`
              FROM
                seg_billing_caserate AS sbc
                INNER JOIN seg_case_rate_special AS scrs
                  ON sbc.`package_id` = scrs.`sp_package_id`
              WHERE rate_type = $rtype
                AND bill_nr = " . $db->qstr($bill_nr);

        if ($buf = $db->Execute($sql)) {
            if ($buf->RecordCount()) {
                return $buf->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Retrieve doctor information for specific case from database
     * @param string $enc - case number
     */
    protected function getDoctorInfo($enc) {
        global $db;
        $strSQL = $db->Prepare("SELECT sbe.`bill_nr`,
            sbe.`bill_dte`,
            fn_get_personell_first(sbp.`dr_nr`) as pDoctorFirstName,
            fn_get_personell_last(sbp.`dr_nr`) as pDoctorLastName,
            fn_get_personell_middle(sbp.`dr_nr`) as pDoctorMiddleName,
            fn_get_personell_suffix(sbp.`dr_nr`) as pDoctorSuffix,
            sbp.`dr_charge`,
            sbp.`dr_claim`,
            sbp.`role_area`,
            (SELECT accreditation_nr FROM seg_dr_accreditation AS sda WHERE sda.dr_nr = sbp.dr_nr and sda.hcare_id = '" . self::HCARE_ID . "') as acc_no
            FROM seg_billing_encounter AS sbe
            INNER JOIN seg_billing_pf AS sbp ON sbe.`bill_nr` = sbp.`bill_nr`
            WHERE sbe.is_final = '1' AND sbe.is_deleted IS NULL
            AND sbe.`encounter_nr` = ? ");



        if ($result = $db->Execute($strSQL, $enc)) {
            if ($result->RecordCount()) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    protected function checkifDoctorHasHousecase()
    {
        $bill = HospitalBill::model()->findByAttributes(array(
            'encounter_nr' => $this->encounter_nr,
            'is_final' => 1
        ));
    }

    /**
     * Retrieve house doctor information
     */
    protected function getHouseCaseDoctor($role) {
        global $db;

        switch ($role) {
            case 'D1':
            case 'D2':
                $filter = "cpr.is_housecase_attdr = 1";
                break;

            case 'D3':
                $filter = "cpr.is_housecase_surgeon = 1";
                break;

            case 'D4':
                $filter = "cpr.is_housecase_anesth = 1";
        }

        $strSQL = $db->Prepare("SELECT  fn_get_personell_first(cpr.nr) as pDoctorFirstName,
                                        fn_get_personell_last(cpr.nr) as pDoctorLastName,
                                        fn_get_personell_middle(cpr.nr) as pDoctorMiddleName,
                                        fn_get_personell_suffix(cpr.nr) as pDoctorSuffix,\n
                                        (SELECT accreditation_nr FROM seg_dr_accreditation AS sda WHERE
                                            sda.dr_nr = cpr.nr AND sda.hcare_id = '" . self::HCARE_ID . "') AS acc_no \n
                                FROM care_personell cpr
                                WHERE $filter");

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Determines if case is a house case
     * @param string $enc - case number
     */
    protected function isHouseCase($enc) {
        global $db;

        $housecase = true;
        $strSQL = "select fn_isHouseCase('" . $enc . "') as casetype";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    $housecase = is_null($row["casetype"]) ? true : ($row["casetype"] == 1);
                }
            }
        }

        $this->bhousecase = $housecase;
    }

    /**
     * Retrieve total applied discount for a specific case
     * @param string $enc - case number
     */
    protected function getTotalAppliedDiscounts($enc) {
        global $db;

        $sql = $db->Prepare("SELECT SUM(discount) AS total_discount FROM seg_billingapplied_discount
                WHERE encounter_nr = ?");

        $rs = $db->Execute($sql, $enc);
        if ($rs) {
            if ($rs->RecordCount() > 0) {
                $row = $rs->FetchRow();
                return $row['total_discount'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Created By Jarel
     * Created On 03/07/2014
     * Get Calculate Date Excluding Weekends
     * @param string bill_dte
     * @return date
     *
     */
    protected function getCalculateDate($bill_dte) {

	date_default_timezone_set('Asia/Manila');
        $bill_dte = date('Y-m-d',strtotime($bill_dte));
        $numberofdays = 5;

        $date_orig = new DateTime($bill_dte);

        $t = $date_orig->format("U"); //get timestamp


        // loop for X days
        for($i=0; $i<$numberofdays ; $i++){

            // add 1 day to timestamp
            $addDay = 86400;

            // get what day it is next day
            $nextDay = date('w', ($t+$addDay));

            // if it's Saturday or Sunday get $i-1
            if($nextDay == 0 || $nextDay == 6) {
                $i--;
            }

            // modify timestamp, add 1 day
            $t = $t+$addDay;
        }

        return date('m-d-Y', ($t));

        // $bill_dte = date('Y-m-d', strtotime($bill_dte));
        // $numberofdays = 3;

        // $date_orig = new DateTime($bill_dte);

        // $t = $date_orig->format("U");

        // //get timestamp

        // // loop for X days
        // for ($i = 0; $i < $numberofdays; $i++) {

        //     // add 1 day to timestamp
        //     $addDay = 86400;

        //     // get what day it is next day
        //     $nextDay = date('w', ($t + $addDay));

        //     // if it's Saturday or Sunday get $i-1
        //     if ($nextDay == 0 || $nextDay == 6) {
        //         $i--;
        //     }

        //     // modify timestamp, add 1 day
        //     $t = $t + $addDay;
        // }

    }

    /**
     * Created By Jarel
     * Created On 02/20/2014
     * Look up if patient avail Medical And Surgical Case
     * @return boolean
     *
     */
    protected function isDiffCase($bill_nr) {
        global $db;
        $first_type = '';
        $second_type = '';
        $strSQL = $db->Prepare("SELECT p.case_type, sc.rate_type
                    FROM seg_billing_caserate sc
                    INNER JOIN seg_case_rate_packages p
                        ON p.`code` = sc.`package_id`
                    WHERE bill_nr = ?");

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    if ($row['rate_type'] == 1) $first_type = $row['case_type'];
                    else $second_type = $row['case_type'];
                }
            }
        }

        if ($first_type != $second_type && $second_type != '') {
            return true;
        } else {
            return false;
        }
    }

    // /**
    // * Retrieve Icd Codes for an encounter based on its Diagnoses
    // * @return boolean
    // **/
    // protected function getIcdCodes($diagnoses) {
    //  $icd = array();

    //  foreach ($diagnoses as $dkey => $diagnosis) {
    //      $package = array();
    //      $package = CaseRatePackages::model()->find('code="'.$diagnosis->code.'"');
    //      if($package) {
    //              array_push($icd, $package);
    //      }
    //      unset($package);
    //  }

    //  return $icd;
    // }



    /**
     * Retrieve Rvs Codes for an encounter based on its miscellaneous operations
     * @return boolean
     *
     */

    // protected function getRvsCodes($operations) {
    //  $rvs = array();

    //  foreach ($operations as $okey => $operation) {
    //      $details = $operation->details;
    //      foreach ($details as $dkey => $detail) {
    //          array_push($rvs, $detail);
    //      }
    //  }

    //  return $rvs;
    // }

    /**** DOM Document functions ****/

    /**
     * Retrieves and sets all 'CLAIM' xml validation errors to $xmlValidationErrors[]
     * @return void
     */
    protected function setValidationErrors() {

        /* $this->dom is a DOMDocument object */
        $claim = new XmlValidator($this->dom, $this->encounter_nr);

        // copy constructor
        $isValid = $claim->validate();

        // won't create warnings

        if (!$isValid) {
            $this->xmlValidationErrors = $claim->errors;
        }
    }

    /**
     * Returns an array containing all validation errors of this claim XML
     * @return string[]
     */
    public function getXmlValidationErrors() {
        return $this->xmlValidationErrors;
    }

    /**
     * Retrieve xml string without headers (<?xml..?>)
     * @return string - xml body string
     */
    public function getXmlBody() {
        return $this->dom->saveXML($this->dom->documentElement);
    }

    /**
     * Initializes a dom document from an xml string
     * @param string $xmlString
     */
    protected function loadXml($xmlString) {
        $this->dom->loadXml($xmlString);
    }

    //  /**
    //  * Creates a complete xml with Document Type Definition(DTD) tag for validation testing
    //  * @param string $xmlBody - xml string (without headers)
    //  */
    //  public function createXml($xmlBody){
    //      $header = <<<XML
    //          <!DOCTYPE CLAIM SYSTEM "eClaimsDef_1.7.3.dtd">
    // XML;

    //      $xmlBody = htmlspecialchars_decode($xmlBody);
    //      $xmlString = $header.$xmlBody;

    //      $this->setDom();
    //      $this->loadXml($xmlString);
    //  }

    /**
     * Attributes added for pCataractPreAuth same as xclaims.
     * @author Jeff Ponteras - 02/22/18
     * @return attributes to eclaims XML generation
     */
    protected function getCataract()
    {
        global $db;
        $encounterNr = $this->encounter_nr;
        $codesResult = $this->checkRVSeclaims();

            $cataractCode = $db->GetOne("SELECT
                                              smod.cataract_code
                                            FROM
                                              seg_misc_ops AS smo
                                              INNER JOIN seg_misc_ops_details AS smod
                                                ON smo.refno = smod.refno
                                            WHERE encounter_nr =".$db->qstr($encounterNr)."
                                              AND smod.ops_code IN(".$codesResult.")");

            // CVarDumper::dump($cataractCode);die();
            $attrs['pCataractPreAuth'] = $cataractCode;
            $this->appendNode($this->caserate, $this->cataract, 'CATARACT', $attrs);
    }

    /**
     * Using global config for dynamic values of RVS cataract codes.
     * @author Jeff Ponteras - 02/22/18
     * @return array of codes
     */
    protected function checkRVSeclaims(){
        include_once($root_path.'include/care_api_classes/class_globalconfig.php');
        $obj_global = new GlobalConfig($this);
        $codesResult = $obj_global->getRVSeclaims();
            return $codesResult;
    }

    /**
    * @author Jeff Ponteras | 03-28-18
    * @return  String value of converted value of membership_type as per billing request as DTD required
    */
    function setMembershipType($memType){

        switch ($memType) {
            case 'HSM':
            case 'POS':
                    return 'I';
                break;

            case 'SC':
                    return 'PS';
                break;

            default:
                return false;
                break;
        }
    }

}
