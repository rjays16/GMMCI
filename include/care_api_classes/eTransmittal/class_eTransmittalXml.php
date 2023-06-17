<?php
/**
 * Created by Nick, 3/27/2014
 * Schema based on eClaimsDef_1.7.3.dtd
 */
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
include('roots.php');
include_once($root_path . 'include/care_api_classes/class_encounter.php');
include_once($root_path . 'include/care_api_classes/class_insurance.php');

define('HSM', 9); //Define Membership Category id for HOSPITAL SPONSORED MEMBER
define('SM', 5); //Define Membership Category id for SPONSORED MEMBER
define('HCARE_ID', 18); //Define PHIC_ID id for SPONSORED MEMBER
define('WELLBABY', 12);

define('CATARACT',array('66987','66983','66984'));
define('NEWBORN', '99432');

define('STR', 1);
define('NUMBER', 2);
define('DATETIME', 3);
define('CURRENCY', 4);
define('PERSON_NAME', 5);
define('ADDRESS', 6);
define("TMP_FOLDER",$root_path."include/care_api_classes/eTransmittal/tmp/");
define('InsuranceCutOff', 20140829);

class eTransmittalXml
{

    var $dom;            // only once
    var $eClaims;        // only once - $dom
    var $eTransmittal;   // only once - $eClaims
    var $claim;          // 1 or more - $eTransmittal
    var $cf1;            // only once - $claim
    var $cf2;            // only once - $claim
    var $diagnosis;      // only once - $cf2
    var $discharge;      // 1 or more - $diagnosis
    var $icdcode;        // 0 or more - $discharge
    var $rvscodes;       // 0 or more - $discharge
    var $special;        // only once - $cf2
    var $procedures;     // 0 or 1    - $special
    var $hemodialysis;   // 0 or 1    - $procedures
    var $peritoneal;     // 0 or 1    - $procedures
    var $linac;          // 0 or 1    - $procedures
    var $cobalt;         // 0 or 1    - $procedures
    var $brachytheraphy; // 0 or 1    - $procedures
    var $transfusion;    // 0 or 1    - $procedures
    var $chemotherapy;   // 0 or 1    - $procedures
    var $debridement;    // 0 or 1    - $procedures
    var $sessions;       // 0 or 1    - $hemodialysis,$peritoneal,$linac,$cobalt,$brachytheraphy,$transfusion,$chemotherapy,$debridement
    var $mcp;            // 0 or 1    - $special
    var $tbdots;         // 0 or 1    - $special
    var $abp;            // 0 or 1    - $special
    var $ncp;            // 0 or 1    - $special
    var $essential;      // 0 or 1    - $ncp
    var $hivaids;        // 0 or 1    - $special
    var $professionals;  // 1 or more - $cf2
    var $consumption;    // only once - $cf2
    var $benefits;       // 0 or 1    - $consumption
    var $hcifees;        // 0 or 1    - $consumption
    var $proffees;       // 0 or 1    - $consumption
    var $purchases;      // 0 or 1    - $consumption
    var $allcaserate;    // only once - $claim
    var $caserate;       // 1 or more - $allcaserate
    var $cataract;       // 0 or 1    - $caserate
    var $zbenefit;       // only once - $claim
    var $cf3;            // 0 or 1    - $claim
    var $cf3_old;        // 0 or 1    - $cf3
    var $maternity;      // 0 or 1    - $cf3_old
    var $prenatal;       // only once - $maternity
    var $clinicalhist;   // only once - $prenatal
    var $obstetric;      // only once - $prenatal
    var $medisurg;       // only once - $prenatal
    var $consultation;   // 1 or more - $prenatal
    var $delivery;       // only once - $maternity
    var $postpartum;     // only once - $maternity
    var $cf3_new;        // 0 or 1    - $cf3
    var $admitreason;    // only once - $cf3_new
    var $clinical;       // 0 or more - $admitreason
    var $labdiag;        // 0 or more - $admitreason
    var $phex;           // only once - $cf3_old,$cf3_new
    var $course;         // only once - $cf3_new
    var $ward;           // 1 or more - $course
    var $particulars;    // 0 or 1    - $claim
    var $drgmed;         // 0 or more - $particulars
    var $xlso;           // 0 or more - $particulars
    var $receipts;       // 0 or 1    - $claim
    var $receipt;        // 1 or more - $receipts
    var $item;           // 1 or more - $receipt
    var $documents;      // only once - $claim
    var $document;       // 1 or more - $documents

    var $transmit_no;
    var $bhousecase;
    var $charity;
    var $memcat;
    var $filename;
    var $log;
    var $zip_archive;
    var $generationTime;
    var $tmpFolder = TMP_FOLDER;
    private static $CATARACT = array('66987','66983','66984');//array of cataract codes

    function __construct($tno, $memcat)
    {
        $this->transmit_no = $tno;
        $this->memcat = $memcat;
    }

    function resetNodes()
    {
        unset($this->claim);
        unset($this->cf1);
        unset($this->cf2);
        unset($this->diagnosis);
        unset($this->discharge);
        unset($this->special);
        unset($this->procedures);
        unset($this->cf3);
        unset($this->cf3_old);
        unset($this->cf3_new);
        unset($this->admitreason);
        unset($this->course);
        unset($this->particulars);
        unset($this->receipts);
        unset($this->receipt);
        unset($this->documents);
    }

    function Generate()
    {
        $this->setDom();
        $this->setEClaims();

        $transmittals = $this->getTransmittals();
        $this->setETransmittals($transmittals);

        $enc_obj = new Encounter;

        if ($transmittals) {
            foreach ($transmittals as $tkey => $transmittal) {
                $encounter_nr = $transmittal['encounter_nr'];
                $bill_nr = $transmittal['bill_nr'];

                $encInfo = $enc_obj->getEncounterInfo($encounter_nr);

                $this->resetNodes();
                $this->setClaim($encounter_nr);

                /**** CLAIM Child Nodes ****/
                $this->setCf1_cf2($encounter_nr,$encInfo);

                /**** CF2 Child Nodes ****/
                $this->setCf2_Diagnosis($encounter_nr, $encInfo['er_opd_diagnosis']);
                $this->appendNode($this->cf2, $this->special, 'SPECIAL', null);

                /**** SPECIAL Child Nodes ****/
                $this->setSpecial_Procedures($encounter_nr);
                $this->setSpecial_Ncp($encounter_nr, $bill_nr);
                $this->setCf2_Professionals($encounter_nr);
                $this->setCf2_Consumption($encounter_nr);
                /**** End SPECIAL Child Nodes ****/

                $this->appendNode($this->claim, $this->allcaserate, 'ALLCASERATE', null);
                $this->setAllCaserate_Caserate($encounter_nr);
                $this->appendNode($this->claim, $this->documents, 'DOCUMENTS', null);

                /**** DOCUMENTS Child Nodes ****/
                $this->setDocuments_Document($encounter_nr);
                /**** End DOCUMENTS Child Nodes ****/

                /**** END CLAIM Child Nodes ****/
            }
        }
        return $this->dom->validate();
    }

    //added by Nick 06-28-2014
    function GenerateZip($filename="")
    {
        $count = 0;
        $this->log = "";
        $this->filename = $filename;
        $transmittals = $this->getTransmittals();
        if ($transmittals) {

            if (!file_exists(TMP_FOLDER)) {
                mkdir(TMP_FOLDER, true);
            }

            $this->generationTime = time();
            $zip_path = TMP_FOLDER . $this->filename . "_" . $this->generationTime . "/";
            if (!file_exists($zip_path)) {
                mkdir($zip_path, 0777, true);
            }

            $this->zip_archive = $this->filename . "_" . $this->generationTime . ".zip";
            $zip_name = TMP_FOLDER . $this->zip_archive;
            foreach ($transmittals as $tkey => $transmittal) {
                $count++;
                $this->setDom();
                $this->setEClaims();
                $this->setETransmittals($transmittal);
                $enc_obj = new Encounter;
                $encounter_nr = $transmittal['encounter_nr'];
                $bill_nr = $transmittal['bill_nr'];
                $encInfo = $enc_obj->getEncounterInfo($encounter_nr);
                $this->resetNodes();
                $this->setClaim($encounter_nr);
                $this->setCf1_cf2($encounter_nr,$encInfo);
                $this->setCf2_Diagnosis($encounter_nr, $encInfo['er_opd_diagnosis']);
                $this->appendNode($this->cf2, $this->special, 'SPECIAL', null);
                $this->setSpecial_Procedures($encounter_nr);
                $this->setSpecial_Ncp($encounter_nr, $bill_nr);
                $this->setCf2_Professionals($encounter_nr);
                $this->setCf2_Consumption($encounter_nr);
                $this->appendNode($this->claim, $this->allcaserate, 'ALLCASERATE', null);
                $this->setAllCaserate_Caserate($encounter_nr);
                $this->appendNode($this->claim, $this->documents, 'DOCUMENTS', null);
                $this->setDocuments_Document($encounter_nr);
                if (!$this->dom->validate()) {
                    $this->log .= "CLAIM: " . $encounter_nr . " is not valid\n";
                } else {
                    $this->log .= "CLAIM: " . $encounter_nr . " is valid\n";
                }

                $fname = $zip_path . $count . "_" . $encounter_nr . ".xml";
                $fp = fopen($fname, 'w');
                fwrite($fp, $this->xmlToString());
                fclose($fp);
            }

            $fname = $zip_path . "info.txt";
            $fp = fopen($fname, 'w');
            fwrite($fp,"Transmittal no: " .  $this->transmit_no . "\n");
            fwrite($fp,"Date generated: " . date("m-d-Y h:i:s a") . "\n");
            fwrite($fp,"Generated by: " . $_SESSION['sess_user_name'] . "\n");
            fwrite($fp,$this->log);
            fclose($fp);

            unset($fp);

            $this->zipDir(substr($zip_path, 0, strlen($zip_path) - 1), $zip_name);
        }
    }

    function getTransmittals()
    {
        global $db;
        if ($this->memcat == 'all') {
            $memcat_cond = "";
            $sqlParam = $this->transmit_no;
        } else if ($this->memcat == 'none') {
            $memcat_cond = "AND sem.`memcategory_id` IS NULL";
            $sqlParam = $this->transmit_no;
        } else {
            $memcat_cond = "AND sem.`memcategory_id` = ?";
            $sqlParam = array(
                $this->transmit_no,
                $this->memcat
            );
        }

        $this->sql = $db->Prepare("SELECT
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
                                    WHERE transmit_no = ? $memcat_cond
                                    ORDER BY cp.name_last ASC");

        $rs = $db->Execute($this->sql, $sqlParam);
        if ($rs) {
            if ($rs->RecordCount()) {
                return $rs->GetRows();
            }
        }
        return false;
    }

    function setETransmittals($transmittals)
    {
        $attrs = array();
        $attrs['pHospitalTransmittalNo'] = $this->transmit_no;
        $attrs['pTotalClaims'] = count($transmittals);
        $this->appendNode($this->eClaims, $this->eTransmittal, 'eTRANSMITTAL', $attrs);
    }

    function setEClaims()
    {
        global $db;
        $objinsurance = new Insurance();
        $hospaccnum = $objinsurance->getAccreditationNo(HCARE_ID);
        $attrs['pUserName'] = '';
        $attrs['pUserPassword'] = '';
        $attrs['pHospitalCode'] = $hospaccnum;
        $email = $db->GetOne("SELECT value FROM care_config_global WHERE type='main_info_email'");
        $attrs['pHospitalEmail'] = $email ? $email : 'sample@email.com';
        $this->appendNode($this->dom, $this->eClaims, 'eCLAIMS', $attrs);
    }

    function setClaim($enc)
    {
        $attrs['pClaimNumber'] = $enc;
        $attrs['pTrackingNumber'] = '';
        $attrs['pPhilhealthClaimType'] = 'ALL-CASE-RATE'; // ALL-CASE-RATE|Z-BENEFIT
        $attrs['pPatientType'] = 'I'; // I|O
        $attrs['pIsEmergency'] = 'N'; // Y|N
        $this->appendNode($this->eTransmittal, $this->claim, 'CLAIM', $attrs);
    }

    /**
     * Temporary fix for {ñ} character
     * TODO refactor
     * Updated by Poliam
     * @param $str
     * @return string
     */
    function fixEnye($str)
    {
        $output = "";
        $letters = str_split($str);
        foreach ($letters as $key => $letter) {
            if (ord($letter) == 241) {
                $output .= (string)"&ntilde";
            } else if (ord($letter) == 209) {
                $output .= (string)"&Ntilde";
            } else if (ord($letter) == 195 || ord($letter) == 177) {
                if (ord($letter) == 177) {
                    $output .= "ñ";
                }
            } else {
                $output .= $letter;
            }
        }

        return $output;
    }

    function getCaption($type = STR, $value)
    {
        switch ($type) {
            case STR:
                if (trim($value) == '')
                    return '';
                break;
            case NUMBER:
                if (trim($value) == '')
                    return '0';
                break;
            case DATETIME:
                if (strtotime($value) <= 0)
                    return '00-00-0000';
                else
                    return date('m-d-Y', strtotime($value));
                break;
            case CURRENCY:
                if (trim($value) == '')
                    return '0.00';
                break;
            case PERSON_NAME:
                if (trim($value) == '') {
                    return '.';
                } else {
                    return strtoupper($this->fixEnye($value));
                }
                break;
            case ADDRESS:
                if(trim($value) == ''){
                    return '.';
                }else{
                    return strtoupper($this->fixEnye(trim($value)));
                }
        }

        return strtoupper($value);
    }

    function setCf1_cf2($enc,$encInfo)
    {
        global $db;
        $sql = $db->Prepare("SELECT	b.pid AS pid,
								d.employer_name AS pEmployerName,
								d.employer_no AS pPen,
								b.date_birth AS pPatientBirthDate,
								b.name_middle AS pPatientMiddleName,
								b.suffix AS pPatientSuffix,
								b.name_first AS pPatientFirstName,
								b.name_last AS pPatientLastName,
								b.sex AS pPatientSex,
								c.insurance_nr AS pPatientPIN,
								IF(d.relation IS NOT NULL,d.relation,'M') AS pPatientIs,
								b.email AS pEmailAddress,
								b.cellphone_1_nr pMobileNo,
								b.phone_1_nr pLandlineNo,
								(SELECT zipcode FROM seg_municity sm WHERE sm.mun_nr = b.mun_nr) AS pZipCode,
								fn_get_complete_address(b.pid) AS pMailingAddress,
								IF(d.member_type IS NOT NULL,d.member_type,'NS') AS pMemberShipType,
								d.birth_date AS pMemberBirthDate,
								d.member_mname AS pMemberMiddleName,
								d.suffix AS pMemberSuffix,
								d.member_fname AS pMemberFirstName,
								d.member_lname AS pMemberLastName,
								c.insurance_nr AS pMemberPIN,
								d.employer_no as pPEN,
								a.admission_dt as admission_date,
								CONCAT(a.discharge_date,' ',a.discharge_time) AS discharge_date,
								dis.disp_code,
								res.result_code,
								sbe.accommodation_type,
								sbe.bill_dte,
								a.mgh_setdte
							FROM care_encounter AS a
							LEFT JOIN seg_encounter_disposition as dis ON dis.encounter_nr = a.encounter_nr
							LEFT JOIN seg_encounter_result as res ON res.encounter_nr = a.encounter_nr
							INNER JOIN seg_billing_encounter as sbe on sbe.encounter_nr = a.encounter_nr
							INNER JOIN care_person AS b ON a.pid = b.pid
							INNER JOIN care_person_insurance AS c ON a.pid = c.pid AND c.hcare_id = '18'
							INNER JOIN seg_insurance_member_info AS d ON a.pid = d.pid AND d.hcare_id = '18'
							WHERE a.encounter_nr = ?
							AND sbe.is_deleted IS NULL AND sbe.is_final = '1' 
							LIMIT 1");

        $rs = $db->Execute($sql, $enc);
        if ($rs) {
            if ($rs->RecordCount()) {
                $row = $rs->FetchRow();
            }
        }

        
        $attrs = array();
        if(!empty($InsuranceEncInfo)){
            $attrs['pMemberPIN'] = trim(str_replace("-", "", $InsuranceEncInfo['insurance_nr']));
            $attrs['pMemberLastName'] = $this->getCaption(PERSON_NAME, $InsuranceEncInfo['member_lname']);
            $attrs['pMemberFirstName'] = $this->getCaption(PERSON_NAME, $InsuranceEncInfo['member_fname']);
            $attrs['pMemberSuffix'] = $this->getCaption(PERSON_NAME,$InsuranceEncInfo['suffix']);
            $attrs['pMemberMiddleName'] = $this->getCaption(PERSON_NAME, $InsuranceEncInfo['member_mname']);
            $attrs['pMemberBirthDate'] = date("m-d-Y", strtotime($InsuranceEncInfo['birth_date']));
            $attrs['pMemberShipType'] = $this->getCaption(STR, (($InsuranceEncInfo['member_type']) ? (($InsuranceEncInfo['member_type'] != 'HSM') ? $InsuranceEncInfo['member_type'] : 'I') : 'NS'));
            $attrs['pEmployerName'] = $this->getCaption(PERSON_NAME,(($InsuranceEncInfo['member_type'] == 'S' || $InsuranceEncInfo['member_type'] == 'G') ? (($InsuranceEncInfo['employer_name']) ? $InsuranceEncInfo['employer_name'] : 'NONE') : ''));
            $attrs['pPEN'] = (($InsuranceEncInfo['member_type'] == 'S' || $InsuranceEncInfo['member_type'] == 'G') ? (($InsuranceEncInfo['employer_no']) ? $InsuranceEncInfo['employer_no'] : '0') : '');
            $attrs['pPatientIs'] = mb_strtoupper($InsuranceEncInfo['relation']); // M|S|C|P
        }else{
        $attrs['pMemberPIN'] = trim(str_replace("-", "", $row['pMemberPIN']));
        $attrs['pMemberLastName'] = $this->getCaption(PERSON_NAME, ((mb_strtoupper($row['pPatientIs']) == 'M') ? $row['pPatientLastName'] : $row['pMemberLastName']));
        $attrs['pMemberFirstName'] = $this->getCaption(PERSON_NAME, ((mb_strtoupper($row['pPatientIs']) == 'M') ? $row['pPatientFirstName'] : $row['pMemberFirstName']));
        $attrs['pMemberSuffix'] = $this->getCaption(PERSON_NAME,((mb_strtoupper($row['pPatientIs']) == 'M') ? $row['pPatientSuffix'] : $row['pMemberSuffix']));
        $attrs['pMemberMiddleName'] = $this->getCaption(PERSON_NAME, ((mb_strtoupper($row['pPatientIs']) == 'M') ? $row['pPatientMiddleName'] : $row['pMemberMiddleName']));
            $attrs['pMemberBirthDate'] = $this->getCaption(DATETIME, ((mb_strtoupper($row['pPatientIs']) == 'M') ? date("m-d-Y", strtotime($row['pPatientBirthDate'])) : date("m-d-Y", strtotime($row['pMemberBirthDate']))));
        $attrs['pMemberShipType'] = $this->getCaption(STR, (($row['pMemberShipType']) ? (($row['pMemberShipType'] != 'HSM') ? $row['pMemberShipType'] : 'I') : 'NS'));
            $attrs['pEmployerName'] = $this->getCaption(PERSON_NAME,(($row['pMemberShipType'] == 'S' || $row['pMemberShipType'] == 'G') ? (($row['pEmployerName']) ? $row['pEmployerName'] : 'NONE') : ''));
            $attrs['pPEN'] = (($row['pMemberShipType'] == 'S' || $row['pMemberShipType'] == 'G') ? (($row['pPEN']) ? $row['pPEN'] : '0') : '');
            $attrs['pPatientIs'] = ((mb_strtoupper($row['pPatientIs'])) ? mb_strtoupper($row['pPatientIs']) : 'M'); // M|S|C|P
        }


        $attrs['pMailingAddress'] = $this->getCaption(PERSON_NAME, $row['pMailingAddress']);
        $attrs['pZipCode'] = $this->getCaption(NUMBER, $row['pZipCode']);
        $attrs['pMemberSex'] = $this->getCaption(STR, ((mb_strtoupper($row['pPatientIs']) == 'M') ? mb_strtoupper($row['pPatientSex']) : 'M')); // M|F
        $attrs['pLandlineNo'] = $this->getCaption(NUMBER, $row['pLandlineNo']);
        $attrs['pMobileNo'] = $this->getCaption(NUMBER, $row['pMobileNo']);
        $attrs['pEmailAddress'] = (($row['pEmailAddress']) ? $row['pEmailAddress'] : 'sample@email.com');
        $attrs['pPatientPIN'] = trim(str_replace("-", "", $attrs['pMemberPIN']));
        $attrs['pPatientLastName'] = $this->getCaption(PERSON_NAME, mb_convert_encoding($row['pPatientLastName'], 'UTF-8'));
        $attrs['pPatientFirstName'] = $this->getCaption(PERSON_NAME, $row['pPatientFirstName']);
        $attrs['pPatientSuffix'] = $this->getCaption(PERSON_NAME,$row['pPatientSuffix']);
        $attrs['pPatientMiddleName'] = $this->getCaption(PERSON_NAME, $row['pPatientMiddleName']);
        $attrs['pPatientBirthDate'] = $this->getCaption(PERSON_NAME, date("m-d-Y", strtotime($row['pPatientBirthDate'])));
        $attrs['pPatientSex'] = ((mb_strtoupper($row['pPatientSex'])) ? mb_strtoupper($row['pPatientSex']) : 'M'); // (M|F)

        $attrs_cf2 = array();
        $attrs_cf2['pPatientReferred'] = 'N'; // Y|N
        $attrs_cf2['pReferredIHCPAccreCode'] = '0';
        if($row['admission_date']){
        $attrs_cf2['pAdmissionDate'] = date("m-d-Y", strtotime($row['admission_date']));
        $attrs_cf2['pAdmissionTime'] = date("h:i:sA", strtotime($row['admission_date']));
        }else{
            $attrs_cf2['pAdmissionDate'] = date("m-d-Y", strtotime($row['encounter_date']));
            $attrs_cf2['pAdmissionTime'] = date("h:i:sA", strtotime($row['encounter_date']));
        }
        


        $rows = $this->getDeathDate($enc);

        $date = explode(' ', $rows);
        $get_is_death_time = $date[1];
        $get_is_death_date = $date[0];

        if(($get_is_death_date) && $get_is_death_date!='0000-00-00'){
            $attrs_cf2['pDischargeDate'] = date("m-d-Y", strtotime($get_is_death_date));
            $attrs_cf2['pDischargeTime'] = date("h:i:sA", strtotime($get_is_death_time));
        }elseif($encInfo['mgh_setdte']!='0000-00-00 00:00:00' && ($encInfo['mgh_setdte'])){
            $attrs_cf2['pDischargeDate'] = date("m-d-Y", strtotime($row['mgh_setdte']));
            $attrs_cf2['pDischargeTime'] = date("h:i:sA", strtotime($row['mgh_setdte']));
        }else{
            $attrs_cf2['pDischargeDate'] = date("m-d-Y", strtotime($row['bill_dte']));
            $attrs_cf2['pDischargeTime'] = date("h:i:sA", strtotime($row['bill_dte']));
        }


        if ($get_is_death_date == "") {

            if ($row['result_code'] == 5 || $row['result_code'] == 6) {
                if ($row['result_code'] == 5)
                    $dispo = 'R';
                elseif ($row['result_code'] == 6)
                    $dispo = 'I';

            } else {
                if ($row['disp_code'] != 7) {
                    if ($row['disp_code'] == 9)
                        $dispo = 'H';
                    elseif ($row['disp_code'] == 8)
                        $dispo = 'T';
                    elseif ($row['disp_code'] == 10)
                        $dispo = 'A';

                }
            }

        } else {

            $date_expired = date("m-d-Y", strtotime($get_is_death_date));
            $time_expired = date("h:i:sA", strtotime($get_is_death_time));
            $dispo = "E";
        }


        $attrs_cf2['pDisposition'] = (($dispo) ? $dispo : 'I'); // I|R|H|A|E|T
        $attrs_cf2['pExpiredDate'] = $date_expired;
        $attrs_cf2['pExpiredTime'] = $time_expired;
        $attrs_cf2['pReferralIHCPAccreCode'] = '';
        $attrs_cf2['pReferralReasons'] = '';
        $attrs_cf2['pAccommodationType'] = (($row['accommodation_type'] == 1) ? 'N' : 'P'); // P|N
        $this->charity = (($row['accommodation_type'] == '1') ? true : false);

        $this->appendNode($this->claim, $this->cf1, 'CF1', $attrs);
        $this->appendNode($this->claim, $this->cf2, 'CF2', $attrs_cf2);
    }

    function getDischargeName($enc){
        global $db;

        $this->sql ="SELECT sen.name_first, 
                            sen.name_last, 
                            sen.name_middle
                        FROM seg_encounter_name `sen`
                        WHERE encounter_nr = ".$db->qstr($enc);
        $this->result = $db->GetRow($this->sql);
        if($this->result){
            return $this->result;
        }else{
            return false;
        }
    }

    function EncInsurance($enc){
        global $db;

        $this->sql ="SELECT seim.insurance_nr, 
                            seim.member_fname, 
                            seim.member_lname, 
                            seim.member_mname,
                            seim.suffix,
                            seim.birth_date,
                            seim.member_type,
                            seim.employer_name,
                            seim.employer_no,
                            seim.relation
                    FROM seg_encounter_insurance_memberinfo `seim`
                    WHERE encounter_nr = ".$db->qstr($enc);
                    
        $this->result = $db->GetRow($this->sql);
        if($this->result){
            return $this->result;
        }else{
            return false;
        }
    }


    /**
     * Created By Jarel
     * Created On 03/12/2014
     * Get Patient Death date
     * @param string enc
     * @return string death date
     **/
    function getDeathDate($enc)
    {
        global $db;
        $strSQL = $db->Prepare("SELECT CONCAT(p.death_date,' ',p.death_time) as deathdate
                            FROM care_person p
                            WHERE death_encounter_nr = ?");

        if ($result = $db->Execute($strSQL, $enc)) {
            $row = $result->FetchRow();
            return $row['deathdate'];
        } else {
            return false;
        }
    }

    function getIcd($enc)
    {
        global $db;

        $encounterDate = self::getEncounterDate($enc);

        $sql = $db->Prepare("SELECT
                                  scrp.code,
                                  scrp.group,
                                  sca.acr_groupid
                                FROM
                                  seg_encounter_diagnosis AS sed
                                  INNER JOIN seg_case_rate_packages AS scrp
                                    ON scrp.code = sed.code  AND
                                    (
                                        STR_TO_DATE(scrp.date_from,'%Y-%m-%d') <= STR_TO_DATE('{$encounterDate}','%Y-%m-%d') AND
                                        STR_TO_DATE(scrp.date_to,'%Y-%m-%d') >= STR_TO_DATE('{$encounterDate}','%Y-%m-%d')
                                    )
                                  INNER JOIN seg_caserate_acr AS sca
                                    ON scrp.code = sca.package_id
                                WHERE is_deleted = 0
                                  AND encounter_nr = ?");
        $rs = $db->Execute($sql, $enc);
        if ($rs) {
            if ($rs->RecordCount()) {
                return $rs->GetRows();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getRvs($enc)
    {
        global $db;

        $encounterDate = self::getEncounterDate($enc);

        $sql = $db->Prepare("SELECT
                                  scrp.code,
                                  scrp.description,
                                  smod.laterality,
                                  smod.op_date,
                                  sca.acr_groupid
                                FROM
                                  seg_misc_ops AS smo
                                  INNER JOIN seg_misc_ops_details AS smod
                                    ON smod.refno = smo.refno
                                  INNER JOIN seg_case_rate_packages AS scrp
                                    ON scrp.code = smod.ops_code AND
                                    (
                                        STR_TO_DATE(scrp.date_from,'%Y-%m-%d') <= STR_TO_DATE('{$encounterDate}','%Y-%m-%d') AND
                                        STR_TO_DATE(scrp.date_to,'%Y-%m-%d') >= STR_TO_DATE('{$encounterDate}','%Y-%m-%d')
                                    )
                                  INNER JOIN seg_caserate_acr AS sca
                                    ON smod.ops_code = sca.package_id
                                WHERE smo.encounter_nr = ?");

        $rs = $db->Execute($sql, $enc);

        if ($rs) {
            if ($rs->RecordCount()) {
                return $rs->GetRows();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function get_doctor_info($enc)
    {
        global $db;
        $strSQL = $db->Prepare("SELECT sbe.`bill_nr`,
								  sbe.`bill_dte`,
								  fn_get_personell_first(sbp.`dr_nr`) as pDoctorFirstName,
					 			  fn_get_personell_last(sbp.`dr_nr`) as pDoctorLastName,
								  fn_get_personell_middle(sbp.`dr_nr`) as pDoctorMiddleName,
								  fn_get_personell_suffix(sbp.`dr_nr`) as pDoctorSuffix,
								  SUM(sbp.`dr_charge`) AS dr_charge,
								  SUM(sbp.`dr_claim`) AS dr_claim,
								  sbp.`role_area`,
								  (SELECT accreditation_nr from seg_dr_accreditation as sda
								  	where sda.dr_nr = sbp.dr_nr and sda.hcare_id = '" . HCARE_ID . "') as acc_no
						  FROM seg_billing_encounter AS sbe
						  INNER JOIN seg_billing_pf AS sbp ON sbe.`bill_nr` = sbp.`bill_nr`
						  WHERE sbe.is_final = '1' AND sbe.is_deleted IS NULL
						  AND sbe.`encounter_nr` = ? GROUP BY sbp.dr_nr");

        if ($result = $db->Execute($strSQL, $enc)) {
            if ($result->RecordCount()) {
                return $result;
            }
        }
        return false;
    }

    function getHouseCaseDoctor($role)
    {
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
			  							sda.dr_nr = cpr.nr AND sda.hcare_id = '" . HCARE_ID . "') AS acc_no \n
			  				FROM care_personell cpr 
			  				WHERE $filter");

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                return $result;
            }
        }
        return false;
    }

    function isHouseCase($enc)
    {
        global $db;

        $housecase = true;
        $strSQL = "SELECT fn_isHouseCase('" . $enc . "') AS casetype";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    $housecase = is_null($row["casetype"]) ? true : ($row["casetype"] == 1);
                }
            }
        }

        $this->bhousecase = $housecase;
    }

    function getTotalAppliedDiscounts($enc)
    {
        global $db;

        $sql = $db->Prepare("SELECT SUM(discount) AS total_discount FROM seg_billingapplied_discount
            WHERE encounter_nr = ?");

        $rs = $db->Execute($sql, $enc);
        if ($rs) {
            if ($rs->RecordCount() > 0) {
                $row = $rs->FetchRow();
                return $row['total_discount'];
            }
        }
        return false;
    }

    /**
     * Created By Jarel
     * Created On 03/07/2014
     * Get Calculate Date Excluding Weekends
     * @param string bill_dte
     * @return date
     **/
    function getCalculateDate($bill_dte)
    {
        $bill_dte = date('Y-m-d', strtotime($bill_dte));
        $numberofdays = 10;

        $date_orig = new DateTime($bill_dte);

        $t = $date_orig->format("U"); //get timestamp


        // loop for X days
        for ($i = 0; $i < $numberofdays; $i++) {

            // add 1 day to timestamp
            $addDay = 86400;

            // get what day it is next day
            $nextDay = date('w', ($t + $addDay));

            // if it's Saturday or Sunday get $i-1
            if ($nextDay == 0 || $nextDay == 6) {
                $i--;
            }

            // modify timestamp, add 1 day
            $t = $t + $addDay;
        }

        return date('m-d-Y', ($t));
    }

    /**
     * Created By Jarel
     * Created On 02/20/2014
     * Look up if patient avail Medical And Surgical Case
     * @return boolean
     **/
    function isDiffCase($bill_nr)
    {
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
                    if ($row['rate_type'] == 1)
                        $first_type = $row['case_type'];
                    else
                        $second_type = $row['case_type'];
                }
            }
        }

        if ($first_type != $second_type && $second_type != '') {
            return true;
        } else {
            return false;
        }

    }

    function setCf2_Diagnosis($enc, $diagnosis)
    {
        $attrs['pAdmissionDiagnosis'] = self::getCaption(STR,substr($diagnosis, 0, 500));
        $this->appendNode($this->cf2, $this->diagnosis, 'DIAGNOSIS', $attrs);

        $icd = $this->getIcd($enc);
        $rvs = $this->getRvs($enc);

        $icdNodes = array();
        $rvsNodes = array();

        if(!empty($icd)){
            foreach($icd as $item){
                if(trim($item['group'])=='')
                    $item['group'] = 'NONE';
                $icdNodes[$item['acr_groupid']]['pDischargeDiagnosis'] = $item['group'];
                $icdNodes[$item['acr_groupid']]['codes'][] = array(
                    'pICDCode' => $item['code']
                );
            }
        }

        if(!empty($rvs)){
            foreach($rvs as $item){
                if(trim($item['group'])=='')
                    $item['group'] = 'NONE';

                switch ($item['laterality']) {
                    case 'L':
                        $laterality = 'L';
                        break;
                    case 'R':
                        $laterality = 'R';
                        break;
                    case 'B':
                        $laterality = 'B';
                        break;
                    case '':
                        $laterality = 'N';
                        break;
                    default:
                        break;
                }

                $rvsNodes[$item['acr_groupid']]['pDischargeDiagnosis'] = $item['group'];
                $rvsNodes[$item['acr_groupid']]['codes'][] = array(
                    'pRVSCode' => $item['code'],
                    'pRelatedProcedure' => self::getCaption(STR,$item['description']),
                    'pProcedureDate' => date('m-d-Y', strtotime($item['op_date'])),
                    'pLaterality' => $laterality,
                );
            }
        }

        if(!empty($icdNodes)){
            foreach($icdNodes as $acrKey => $acrGroup){
                $this->appendNode($this->diagnosis, $this->discharge, 'DISCHARGE', array('pDischargeDiagnosis'=>$acrGroup['pDischargeDiagnosis']));
                if(!empty($acrGroup['codes'])){
                    foreach($acrGroup['codes'] as $code){
                        $this->appendNode($this->discharge, $this->icdcode, 'ICDCODE', $code);
                    }
                }
            }
        }

        if(!empty($rvsNodes)){
            foreach($rvsNodes as $acrKey => $acrGroup){
                $this->appendNode($this->diagnosis, $this->discharge, 'DISCHARGE', array('pDischargeDiagnosis'=>$acrGroup['pDischargeDiagnosis']));
                if(!empty($acrGroup['codes'])){
                    foreach($acrGroup['codes'] as $code){
                        $this->appendNode($this->discharge, $this->rvscodes, 'RVSCODES', $code);
                    }
                }
            }
        }

        //diagnosis is 1 or more, add 1 node if no icd/rvs codes used
        if(empty($icdNodes) && empty($rvsNodes)){
            $this->appendNode($this->diagnosis, $this->discharge, 'DISCHARGE', array('pDischargeDiagnosis'=>'NONE'));
        }
    }

    function getSpecialDates($specialDates)
    {
        $dates = explode(',', trim($specialDates, ','));
        $arr_specialDates = array();

        foreach ($dates as $dkey => $date) {
            array_push($arr_specialDates, $date);
        }

        return $arr_specialDates;
    }

    function setProcedure_Sessions(&$child, $procTitle, $specialDates)
    {
        $this->appendNode($this->procedures, $child, $procTitle, null);
        foreach ($specialDates as $skey => $specialDate) {
            $attrs = array();
            $attrs['pSessionDate'] = date('m-d-Y', strtotime($specialDate));
            $this->appendNode($child, $this->sessions, 'SESSIONS', $attrs);
        }
    }

    function addProcedures(&$arr_procedures, $procTitle, $dates)
    {
        $data = array(
            $procTitle,
            $dates
        );
        array_push($arr_procedures, $data);
    }

    function setSpecial_Procedures($enc)
    {

        global $db;
        $arr_procedures = array();

        $considerations = array('Hemodialysis',
            'Peritoneal Dialysis',
            'Radiotherapy (LINAC)',
            'Radiotherapy (COBALT)',
            'Blood transfusion',
            'Brachytherapy',
            'Chemotherapy',
            'Debridement');

        $sql = $db->Prepare("SELECT
                                  smod.ops_code,
                                  scrp.description,
                                  smod.laterality,
                                  smod.op_date,
                                  GROUP_CONCAT(
                                    CONCAT(smod.op_date,TRIM(TRAILING ',' FROM smod.special_dates))
                                  ) AS special_dates
                                FROM care_encounter AS ce
                                INNER JOIN seg_misc_ops AS smo
                                    ON smo.encounter_nr = ce.encounter_nr
                                  INNER JOIN seg_misc_ops_details AS smod
                                    ON smod.refno = smo.refno
                                  INNER JOIN seg_case_rate_packages AS scrp
                                    ON smod.ops_code = scrp.code
                                WHERE ce.encounter_nr = ?
                                  AND scrp.description REGEXP ?
                                  #AND scrp.special_case = 1
                                GROUP BY smod.ops_code
                                ORDER BY scrp.description");

        foreach ($considerations as $ckey => $consideration) {
            $cons = array($enc, $consideration);
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
            $this->appendNode($this->special, $this->procedures, 'PROCEDURES', null);
            foreach ($arr_procedures as $pkey => $value) {
                $this->setProcedure_Sessions($proc, $value[0], $this->getSpecialDates($value[1]));
            }
        }
    }

    function setSpecial_Mcp($enc)
    {
        $attrs = array();
        $attrs['pCheckUpDate1'] = '';
        $attrs['pCheckUpDate2'] = '';
        $attrs['pCheckUpDate3'] = '';
        $attrs['pCheckUpDate4'] = '';
        $this->appendNode($this->special, $this->mcp, 'MCP', $attrs);
    }

    function setSpecial_Tbdots($enc)
    {
        $attrs = array();
        $attrs['pTBType'] = 'I'; // I|M
        $attrs['pNTPCardNo'] = 'SAMPLE';
        $this->appendNode($this->special, $this->tbdots, 'TBDOTS', $attrs);
    }

    function setSpecial_Abp($enc)
    {
        $attrs = array();
        $attrs['pDay0ARV'] = '';
        $attrs['pDay3ARV'] = '';
        $attrs['pDay7ARV'] = '';
        $attrs['pRIG'] = '';
        $attrs['pABPOthers'] = '';
        $attrs['pABPSpecify'] = '';
        $this->appendNode($this->special, $this->abp, 'ABP', $attrs);
    }

    function isWellBaby()
    {
        global $db;
        $enc_type = 0;
        $strSQL = "SELECT encounter_type FROM care_encounter WHERE encounter_nr = ?";
        if ($result = $db->Execute($strSQL,$this->current_enr)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $enc_type = $row['encounter_type'];
            }
        }
        return ($enc_type == WELLBABY);
    }

    function isNewBorn($enc)
    {
        global $db;
        $strSQL = $db->Prepare("SELECT
                                  package_id
                                FROM
                                  seg_billing_encounter sbe
                                INNER JOIN seg_billing_caserate sbc
                                    ON sbe.bill_nr=sbc.bill_nr
                                WHERE sbe.is_final = 1
                                AND sbe.is_deleted IS NULL
                                AND encounter_nr = ?");

        if ($result = $db->Execute($strSQL, $enc)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    if ($row['package_id'] == NEWBORN)
                        return true;
                }
            }
        }
        return false;
    }

    /**
     * @author Nick B. Alcala
     * Identify if patient is new born
     * Created On 4/21/2014
     * @param  String $enc
     * @return boolean
     */
    function isNewBorn2($enc)
    {
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
     * @author Nick B. Alcala 05-30-2014
     * Identify if patient (new born) availed the hearing test
     * @param $enc
     * @return bool
     */
    function isHearingTestAvailed($enc)
    {
        global $db;
        $this->sql = $db->Prepare("SELECT
                                  scrs.*
                                FROM
                                  seg_caserate_hearing_test AS scrs
                                WHERE scrs.encounter_nr = ?");
        $rs = $db->Execute($this->sql, $enc);
        if ($rs) {
            if ($rs->RecordCount()) {
                $row = $rs->FetchRow();
                if ($row['is_availed'] == 1) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Updated by Nick, 4/23/2014
     * Join with seg_case_rate_special
     */
    function hasSavedPackage($bill_nr, $rtype)
    {
        global $db;

        $this->sql = "SELECT 
                          sbc.*,
                          scrs.`sp_package_id` 
                        FROM
                          seg_billing_caserate AS sbc 
                          INNER JOIN seg_case_rate_special AS scrs 
                            ON sbc.`package_id` = scrs.`sp_package_id` 
                        WHERE rate_type = $rtype
                          AND bill_nr = " . $db->qstr($bill_nr);

        if ($buf = $db->Execute($this->sql)) {
            if ($buf->RecordCount()) {
                return $buf->FetchRow();
            }
        }
        return false;
    }

    function setSpecial_Ncp($enc)
    {
        $isNewBorn = $this->isNewBorn($enc);
        $isHearingTestAvailed = $this->isHearingTestAvailed($enc) && $isNewBorn;
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

    function setSpecial_HIVAIDS($enc)
    {
        $attrs = array();
        $attrs['pLaboratoryNumber'] = '';
        $this->appendNode($this->special, $this->hivaids, 'HIVAIDS', $attrs);
    }

    function setCf2_Professionals($enc)
    {
        global $db;
        $this->isHouseCase($enc);
        $applied_discount = $this->getTotalAppliedDiscounts($enc);
        $result = $this->get_doctor_info($enc);
        if ($result) {
            while ($row = $result->FetchRow()) {
                $attrs = array();

                if ($this->bhousecase && !$this->charity) {
                    if (!$this->isDiffCase($row['bill_nr']) && $row['role_area'] == 'D1') {
                        $result2 = $this->getHouseCaseDoctor('D3');
                    } else {
                        $result2 = $this->getHouseCaseDoctor($row['role_area']);
                    }

                    if ($result2) {
                        while ($row2 = $result2->FetchRow()) {
                            $acc_no = trim(str_replace("-", "", $row2['acc_no']));
                            $pDoctorLastName = $row2['pDoctorLastName'];
                            $pDoctorFirstName = $row2['pDoctorFirstName'];
                            $pDoctorMiddleName = $row2['pDoctorMiddleName'];
                            $pDoctorSuffix = $row2['pDoctorSuffix'];
                        }
                    }

                } else {
                    $acc_no = trim(str_replace("-", "", $row['acc_no']));
                    $pDoctorLastName = $row['pDoctorLastName'];
                    $pDoctorFirstName = $row['pDoctorFirstName'];
                    $pDoctorMiddleName = $row['pDoctorMiddleName'];
                    $pDoctorSuffix = $row['pDoctorSuffix'];

                }

                $doc_discount = $row['dr_charge'] * $applied_discount;
                $copay_amount = $row['dr_charge'] - $doc_discount - $row['dr_claim'];

                $attrs['pDoctorAccreCode'] = $acc_no;
                $attrs['pDoctorLastName'] = $this->getCaption(PERSON_NAME, $pDoctorLastName);
                $attrs['pDoctorFirstName'] = $this->getCaption(PERSON_NAME, $pDoctorFirstName);
                $attrs['pDoctorMiddleName'] = $this->getCaption(PERSON_NAME, $pDoctorMiddleName);
                $attrs['pDoctorSuffix'] = $pDoctorSuffix;
                if ($copay_amount <= 0) {
                    $attrs['pWithCoPay'] = 'N';
                    $attrs['pDoctorCoPay'] = '0.00';
                } else {
                    $attrs['pWithCoPay'] = 'Y';
                    $attrs['pDoctorCoPay'] = number_format($copay_amount, 2, '.', '');
                }
                $attrs['pDoctorSignDate'] = $this->getCalculateDate($row['bill_dte']);
                $this->appendNode($this->cf2, $this->proffees, 'PROFESSIONALS', $attrs);
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

    function setCf2_Consumption($enc)
    {
        global $db;
        $sql = $db->Prepare("SELECT
				  sbe.accommodation_type,
				  sbe.bill_dte,
				  sbc.`total_services_coverage`,
				  sbe.`total_doc_charge`,
				  SUM(
				    IFNULL(sbc.`total_d1_coverage`, 0) + IFNULL(sbc.`total_d2_coverage`, 0) + IFNULL(sbc.`total_d3_coverage`, 0) + 
				    IFNULL(sbc.`total_d4_coverage`, 0)
				  ) AS total_doc_coverage,
				  SUM(
				    IFNULL(sbe.`total_acc_charge`, 0) + IFNULL(sbe.`total_med_charge`, 0) + 
				    IFNULL(sbe.`total_ops_charge`, 0) + IFNULL(sbe.`total_msc_charge`, 0) + IFNULL(sbe.`total_srv_charge`, 0) + 
				    IFNULL(sbe.`total_sup_charge`, 0)
				  ) AS total_hci_charge,
				  SUM(
				    IFNULL(sbd.`total_d1_discount`, 0) + IFNULL(sbd.`total_d2_discount`, 0) + IFNULL(sbd.`total_d3_discount`, 0) + 
				    IFNULL(sbd.`total_d4_discount`, 0)
				  ) AS total_doc_discount,
				  SUM(
				   IFNULL(sbd.`hospital_income_discount`,0) + IFNULL(sbd.`total_msc_discount`,0)
				  ) AS total_hci_discount,
					sem.memcategory_id,
					ser.total_meds,
					ser.total_xlo
				FROM seg_billing_encounter sbe
				  INNER JOIN seg_billing_coverage sbc 
				    ON sbe.bill_nr = sbc.`bill_nr` AND sbc.hcare_id = '".HCARE_ID."'
				  INNER JOIN seg_billingcomputed_discount sbd 
				    ON sbd.`bill_nr` = sbe.`bill_nr`
				  LEFT JOIN seg_encounter_memcategory sem
				  	ON sem.encounter_nr = sbe.encounter_nr
				  	LEFT JOIN seg_encounter_reimbursed ser
				    ON ser.encounter_nr = sbe.encounter_nr 
				WHERE sbe.`encounter_nr` = ? 
				 AND sbe.is_deleted IS NULL AND sbe.is_final = '1' ");

        if ($result = $db->Execute($sql, $enc)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $total_doc_charge = $row['total_doc_charge'];
                $total_doc_discount = $row['total_doc_discount'];
                $total_doc_coverage = $row['total_doc_coverage'];
                $total_hci_charge = $row['total_hci_charge'];
                $total_hci_discount = $row['total_hci_discount'];
                $total_hci_coverage = $row['total_services_coverage'];
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
                //$this->charity = (($row['accommodation_type']=='1') ? true : false);
            }
        }

        if (($excess <= 0 || (($row['memcategory_id'] == HSM || $row['memcategory_id'] == SM) && $this->charity)) && ($total_outside <= 0)) {
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
            $attrs_hci['pTotalAmount'] = $total_hci_charge - ($total_hci_discount + $total_hci_coverage);
            $attrs_hci['pMemberPatient'] = 'Y'; // Y|N
            $attrs_hci['pHMO'] = 'N'; // Y|N
            $attrs_hci['pOthers'] = 'N'; // Y|N

            $attrs_doc['pTotalActualCharges'] = number_format($total_doc_charge, 2, '.', '');
            $attrs_doc['pDiscount'] = (($total_doc_discount != 0) ? number_format($total_doc_charge - $total_doc_discount, 2, '.', '') : '0');
            $attrs_doc['pPhilhealthBenefit'] = number_format($total_doc_coverage, 2, '.', '');
            $attrs_doc['pTotalAmount'] = $total_doc_charge - ($total_doc_discount + $total_doc_coverage);
            $attrs_doc['pMemberPatient'] = 'Y'; // Y|N
            $attrs_doc['pHMO'] = 'N'; // Y|N
            $attrs_doc['pOthers'] = 'N'; // Y|N

            $attrs_purchase['pDrugsMedicinesSupplies'] = (($total_meds <= 0) ? 'N' : 'Y'); // Y|N
            $attrs_purchase['pDMSTotalAmount'] = number_format($total_meds, 2, '.', '');
            $attrs_purchase['pExaminations'] = (($total_xlo <= 0) ? 'N' : 'Y'); // Y|N
            $attrs_purchase['pExamTotalAmount'] = number_format($total_xlo, 2, '.', '');

            $this->appendNode($this->cf2, $this->consumption, 'CONSUMPTION', $attrs1);
            $this->appendNode($this->consumption, $this->Hcifees, 'HCIFEES', $attrs_hci);
            $this->appendNode($this->consumption, $this->proffees, 'PROFFEES', $attrs_doc);
            $this->appendNode($this->consumption, $this->purchases, 'PURCHASES', $attrs_purchase);
        }
    }

    function setAllCaserate_Caserate($enc)
    {
        global $db;

        $encounterDate = self::getEncounterDate($enc);

        $sql = $db->Prepare("SELECT
                              sbc.package_id,
                              p.package,
                              sbc.rate_type,
                              p.case_type,
                              sca.acr_groupid
                            FROM
                              seg_billing_encounter sbe
                              INNER JOIN seg_billing_caserate sbc
                                ON sbe.bill_nr = sbc.bill_nr
                              INNER JOIN seg_case_rate_packages p
                                ON p.code = sbc.package_id AND
                                (
                                  STR_TO_DATE(p.date_from,'%Y-%m-%d') <= STR_TO_DATE('{$encounterDate}','%Y-%m-%d') AND
                                  STR_TO_DATE(p.date_to,'%Y-%m-%d') >= STR_TO_DATE('{$encounterDate}','%Y-%m-%d')
                                )
                              INNER JOIN seg_caserate_acr AS sca
                                ON sca.package_id = sbc.package_id
                            WHERE sbe.is_final = 1
                              AND sbe.is_deleted IS NULL
                              AND encounter_nr = ?");

        if ($result = $db->Execute($sql, $enc)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $attrs = array();
                    $attrs['pCaseRateCode'] = ($row['acr_groupid']) ? $row['acr_groupid'] : ".";
                    $attrs['pICDCode'] = (($row['case_type'] == 'm') ? $row['package_id'] : '');
                    $attrs['pRVSCode'] = (($row['case_type'] == 'p') ? $row['package_id'] : '');
                    $attrs['pCaseRateAmount'] = number_format($row['package'], 2, ".", "");
                    $this->appendNode($this->allcaserate, $this->caserate, 'CASERATE', $attrs);
                    if(in_array($row['package_id'],self::$CATARACT)){
                        $cataractCode = $db->GetOne("SELECT
                                                          smod.`cataract_code`
                                                        FROM
                                                          seg_misc_ops AS smo
                                                          INNER JOIN seg_misc_ops_details AS smod
                                                            ON smo.`refno` = smod.`refno`
                                                        WHERE encounter_nr = {$db->qstr($enc)}
                                                          AND smod.`ops_code` = '".$row['package_id']."'");
                        $this->appendNode($this->caserate,$this->cataract,'CATARACT',array(
                            'pCataractPreAuth' => $cataractCode ? $cataractCode : 'NONE'
                        ));
                    }
                }
            } else {
                $attrs = array();
                $attrs['pCaseRateCode'] = 'NA';
                $attrs['pICDCode'] = 'NA';
                $attrs['pRVSCode'] = 'NA';
                $attrs['pCaseRateAmount'] = 'NA';
                $this->appendNode($this->allcaserate, $this->caserate, 'CASERATE', $attrs);
            }
        }

    }

    function setCaserate_Cataract($enc)
    {
        $attrs = array();
        $attrs['pCataractPreAuth'] = '';
        $this->appendNode($this->allcaserate, $this->caserate, 'CATARACT', $attrs);
    }

    function setClaim_Zbenefit($enc)
    {
        $attrs = array();
        $attrs['pZBenefitCode'] = 'Z0011'; // Z0011|Z0012|Z0013|Z0021|Z0022|Z003|Z0041|Z0042|Z0051|Z0052|Z0061|Z0062|Z0071|Z0072|Z0081|Z0082|Z0091|Z0092
        $attrs['pPreAuthDate'] = '';
        $this->appendNode($this->claim, $this->zbenefit, 'ZBENEFIT', $attrs);
    }

    function setCf3_Cf3_Old($enc)
    {
        $attrs = array();
        $attrs['pChiefComplaint'] = '';
        $attrs['pBriefHistory'] = '';
        $attrs['pCourseWard'] = '';
        $attrs['pPertinentFindings'] = '';
        $this->appendNode($this->cf3, $this->cf3_old, 'CF3_OLD', $attrs);
    }

    function setCf3_Old_Phex($enc)
    {
        $attrs = array();
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
        $this->appendNode($this->cf3_old, $this->phex, 'PHEX', $attrs);
    }

    function setMaternity_Prenatal($enc)
    {
        $attrs = array();
        $attrs['pPrenatalConsultation'] = '';
        $attrs['pMCPOrientation'] = 'N'; // Y|N
        $attrs['pExpectedDeliveryDate'] = '';
        $this->appendNode($this->maternity, $this->prenatal, 'PRENATAL', $attrs);
    }

    function setPrenatal_Clinicalhist($enc)
    {
        $attrs = array();
        $attrs['pVitalSigns'] = 'N'; // Y|N
        $attrs['pPregnancyLowRisk'] = 'N'; // Y|N
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

    function setPrenatal_Obstetric($enc)
    {
        $attrs = array();
        $attrs['pMultiplePregnancy'] = 'N'; // Y|N
        $attrs['pOvarianCyst'] = 'N'; // Y|N
        $attrs['pMyomaUteri'] = 'N'; // Y|N
        $attrs['pPlacentaPrevia'] = 'N'; // Y|N
        $attrs['pMiscarriages'] = 'N'; // Y|N
        $attrs['pStillBirth'] = 'N'; // Y|N
        $attrs['pPreEclampsia'] = 'N'; // Y|N
        $attrs['pEclampsia'] = 'N'; // Y|N
        $attrs['pPrematureContraction'] = 'N'; // Y|N
        $this->appendNode($this->prenatal, $this->clinicalhist, 'OBSTETRIC', $attrs);
    }

    function setPrenatal_Medisurg($enc)
    {
        $attrs = array();
        $attrs['pHypertension'] = 'N'; // Y|N
        $attrs['pHeartDisease'] = 'N'; // Y|N
        $attrs['pDiabetes'] = 'N'; // Y|N
        $attrs['pThyroidDisaster'] = 'N'; // Y|N
        $attrs['pObesity'] = 'N'; // Y|N
        $attrs['pAsthma'] = 'N'; // Y|N
        $attrs['pEpilepsy'] = 'N'; // Y|N
        $attrs['pRenalDisease'] = 'N'; // Y|N
        $attrs['pBleedingDisorders'] = 'N'; // Y|N
        $attrs['pPreviousCS'] = 'N'; // Y|N
        $attrs['pUterineMyomectomy'] = 'N'; // Y|N
        $this->appendNode($this->prenatal, $this->medisurg, 'MEDISURG', $attrs);
    }

    function setPrenatal_Consultation($enc)
    {
        $attrs = array();
        $attrs['pVisitDate'] = '';
        $attrs['pAOGWeeks'] = '';
        $attrs['pWeight'] = '';
        $attrs['pCardiacRate'] = '';
        $attrs['pRespiratoryRate'] = '';
        $attrs['pBloodPressure'] = '';
        $attrs['pTemperature'] = '';
        $this->appendNode($this->prenatal, $this->consultation, 'CONSULTATION', $attrs);
    }

    function setMaternity_Delivery($enc)
    {
        $attrs = array();
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

    function setMaternity_Postpartum($enc)
    {
        $attrs = array();
        $attrs['pPerinealWoundCare'] = 'N'; // Y|N
        $attrs['pPerinealRemarks'] = '';
        $attrs['pMaternalComplications'] = 'N'; // Y|N
        $attrs['pMaternalRemarks'] = '';
        $attrs['pBreastFeeding'] = 'N'; // Y|N
        $attrs['pBreastFeedingRemarks'] = '';
        $attrs['pFamilyPlanning'] = 'N'; // Y|N
        $attrs['pFamilyPlanningRemarks'] = '';
        $attrs['pPlanningService'] = 'N'; // Y|N
        $attrs['pPlanningServiceRemarks'] = '';
        $attrs['pSurgicalSterilization'] = 'N'; // Y|N
        $attrs['pSterilizationRemarks'] = '';
        $attrs['pFollowupSchedule'] = 'N'; // Y|N
        $attrs['pFollowupScheduleRemarks'] = '';
        $this->appendNode($this->maternity, $this->delivery, 'POSTPARTUM', $attrs);
    }

    function setCf3_New_Admitreason($enc)
    {
        $attrs = array();
        $attrs['pBriefHistory'] = '';
        $attrs['pReferredReason'] = '';
        $attrs['pIntensive'] = 'N'; // Y|N
        $attrs['pMaintenance'] = 'N'; // Y|N
        $this->appendNode($this->cf3_new, $this->admitreason, 'ADMITREASON', $attrs);
    }

    function setAdmitreason_Clinical($enc)
    {
        $attrs = array();
        $attrs['pCriteria'] = '';
        $this->appendNode($this->admitreason, $this->clinical, 'CLINICAL', $attrs);
    }

    function setAdmitreason_Labdiag($enc)
    {
        $attrs = array();
        $attrs['pCriteria'] = '';
        $this->appendNode($this->admitreason, $this->labdiag, 'LABDIAG', $attrs);
    }

    function setAdmitreason_Phex($enc)
    {
        $attrs = array();
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

    function setCf3_New_Course($enc)
    {
        $this->appendNode($this->cf3_new, $this->course, 'COURSE', null);
    }

    function setCourse_Ward($enc)
    {
        $attrs = array();
        $attrs['pCourseDate'] = '';
        $attrs['pFindings'] = '';
        $attrs['pAction'] = '';
        $this->appendNode($this->course, $this->ward, 'WARD', $attrs);
    }

    function setParticulars_Drgmed($enc)
    {
        $attrs = array();
        $attrs['pPurchaseDate'] = '';
        $attrs['pDrugCode'] = '';
        $attrs['pPNDFCode'] = '';
        $attrs['pGenericName'] = '';
        $attrs['pBrandName'] = '';
        $attrs['pPreparation'] = '';
        $attrs['pQuantity'] = '';
        $this->appendNode($this->particulars, $this->drgmed, 'DRGMED', $attrs);
    }

    function setParticulars_Xlso($enc)
    {
        $attrs = array();
        $attrs['pDiagnosticDate'] = '';
        $attrs['pDiagnosticType'] = 'OTHERS'; // IMAGING|LABORATORY|SUPPLIES|OTHERS
        $attrs['pDiagnosticName'] = '';
        $attrs['pQuantity'] = '';
        $this->appendNode($this->particulars, $this->xlso, 'XLSO', $attrs);
    }

    function setReceipts_Receipt($enc)
    {
        $attrs = array();
        $attrs['pCompanyName'] = '';
        $attrs['pCompanyTIN'] = '';
        $attrs['pBIRPermitNumber'] = '';
        $attrs['pReceiptNumber'] = '';
        $attrs['pReceiptDate'] = '';
        $attrs['pVATExemptSale'] = '';
        $attrs['pVAT'] = '';
        $attrs['pTotal'] = '';
        $this->appendNode($this->receipts, $this->receipt, 'RECEIPT', $attrs);
    }

    function setReceipt_Item($enc)
    {
        $attrs = array();
        $attrs['pQuantity'] = '';
        $attrs['pUnitPrice'] = '';
        $attrs['pDescription'] = '';
        $attrs['pAmount'] = '';
        $this->appendNode($this->receipt, $this->item, 'ITEM', $attrs);
    }

    function setDocuments_Document($enc)
    {
        $attrs = array();
        $attrs['pDocumentType'] = 'CAB'; // CAB|CAE|CF1|CF2|CF3|CSF|COE|CTR|DTR|MBC|MDR|MEF|MMC|MSR|MWV|NTP|OPR|ORS|PAC|PBC|PIC|POR|SOA|STR|TCC|TYP
        $attrs['pDocumentURL'] = 'NONE';
        $this->appendNode($this->documents, $this->document, 'DOCUMENT', $attrs);
    }

    public static function getEncounterDate($encounterNr){
        global $db;
        return $db->GetOne("SELECT encounter_date FROM care_encounter WHERE encounter_nr = ?",$encounterNr);
    }

    function getMemberCategories()
    {
        global $db;
        $this->sql = $db->Prepare("SELECT
                                  memcategory_id,
                                  memcategory_desc
                                FROM
                                  seg_memcategory");
        $rs = $db->Execute($this->sql);
        if ($rs) {
            if ($rs->RecordCount()) {
                return $rs->GetRows();
            }
        }
        return false;
    }

    function folderToZip($folder, &$zipFile, $exclusiveLength)
    {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory.
                    $zipFile->addEmptyDir($localPath);
                    self::folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }

    function zipDir($sourcePath, $outZipPath)
    {
        $pathInfo = pathInfo($sourcePath);
        $parentPath = $pathInfo['dirname'];
        $dirName = $pathInfo['basename'];

        $z = new ZipArchive();
        $z->open($outZipPath, ZIPARCHIVE::CREATE);
        $z->addEmptyDir($dirName);
        self::folderToZip($sourcePath, $z, strlen("$parentPath/"));
        $z->close();
    }

    function getXml(){ return $this->dom; }

    function xmlToString(){ return $this->dom->saveXML(); }

    function getXmlBody(){ return $this->dom->saveXML($this->dom->documentElement); }

    function is_xml_valid(){ $this->dom->validate(); }

    function xml_to_file($file_name)
    {
        $xml = $this->getXml();
        $xml->save($file_name);
    }

    function setDom($version='1.0',$encoding = 'UTF-8')//iso-8859-15
    {
        $implementation = new DOMImplementation();
        $dtd = $implementation->createDocumentType('eCLAIMS', '', 'eClaimsDef_1.7.3.dtd');
        $this->dom = $implementation->createDocument('', '', $dtd);
        $this->dom->encoding = $encoding;
        $this->dom->version = $version;
        $this->dom->preserveWhiteSpace = true;
        $this->dom->formatOutput = true;
    }

    function appendNode(&$parent, &$child, $name, $attrs)
    {
        $child = $parent->appendChild(new DOMElement($name));
        foreach ($attrs as $akey => $attr) {
            $child->setAttribute($akey, $attr);
        }
    }

}//end class