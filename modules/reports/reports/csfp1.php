<?php
    /**
    * @author Jeff Ponteras 
    * @return report jasper-jrxml
    * @param insurance member information
    * Generation of CSF page 1 report.
    */
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');

	$enc_no = $param['enc_no'];
    $pid = $param['pid'];

    #patient info
	$strSQL = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
					p.name_3 AS ThirdName, p.name_middle AS MiddleName, p.suffix as Suffix, p.date_birth as Bday
					FROM care_person AS p
					WHERE p.pid = '$pid'";

	$result = $db->Execute($strSQL);
    $patient = $result->FetchRow();
    
    #encounter info
    $strSQL = "SELECT 
                  ce.admission_dt AS DateAdmitted,
                  bill.bill_dte AS DateDischarged,
                  ce.encounter_date,
                  ce.encounter_type,
                  ce.is_discharged,
                  p.`death_encounter_nr`,
                  p.`death_date`
                FROM
                  care_encounter ce 
                LEFT JOIN
                    seg_billing_encounter bill
                    ON bill.encounter_nr = ce.encounter_nr 
                    AND bill.is_final = 1
                    AND (bill.is_deleted IS NULL OR bill.is_deleted = 0)
                LEFT JOIN care_person p 
                    ON p.`pid` = ce.`pid` 
                WHERE ce.encounter_nr = '$enc_no'
                ORDER BY bill.bill_dte DESC
                ";

	$result = $db->Execute($strSQL);
    $encounter = $result->FetchRow();

    $params->put("date_admitted", is_null($encounter['DateAdmitted']) ? $encounter['encounter_date'] : $encounter['DateAdmitted']);
    $bill_date = $encounter['DateDischarged'] ? $encounter['DateDischarged'] : "";
    $bill_date = $encounter['death_encounter_nr'] ? $encounter['death_date'] : $bill_date;
    $params->put("date_discharged", ($encounter['is_discharged'] == 1 || ($encounter['is_discharged'] == 0 && ($encounter['encounter_type'] != 3 || $encounter['encounter_type'] != 4 || $encounter['encounter_type'] != 13))) ? $bill_date : '');

    #member info
    $strSQL = "SELECT seim.member_fname AS member_fname, 
				      seim.member_lname AS member_lname,
				      seim.member_mname AS member_mname,
				      seim.suffix AS member_suffix,
				      seim.birth_date AS member_bday,
				      seim.insurance_nr AS PIN,
				      seim.relation, 
				      seim.employer_no,
				      seim.employer_name,
                      seim.patient_pin,
                      seim.pid
			   FROM seg_encounter_insurance_memberinfo seim
			   WHERE seim.encounter_nr = '$enc_no' AND seim.hcare_id = '18'";

	$result = $db->Execute($strSQL);
    $member = $result->FetchRow();


    $pid = $member['pid'];
    $sql = "SELECT patient_pin FROM seg_insurance_member_info WHERE pid = $pid";
    $result_sql = $db->Execute($sql);
    $member_sql = $result_sql->FetchRow();

    $pattern = array('/[a-zA-Z]/', '/[ -]+/', '/^-|-$/');
    $pin = preg_replace($pattern, '', $member['PIN']);
    $patient_pin = preg_replace($pattern, '', $member_sql['patient_pin']);
    $params->put("member_pin", $pin);
    $params->put("patient_pin", $patient_pin);
    #condtion in JRXML (Dependent PIN). Removed due to Eclaims limitation- No Web-service of Dependent PIN 

    $params->put("member_lname", mb_strtoupper($member['member_lname']));
    $params->put("member_fname", mb_strtoupper($member['member_fname']));
    $params->put("member_mname", mb_strtoupper($member['member_mname']));
    $params->put("member_suffix", strtoupper($member['member_suffix']));
    $params->put("member_bday", $member['member_bday']);
    $params->put("member_type",$member['relation']);

    if($member['relation']!='M'){
    $params->put("name_last", mb_strtoupper($patient['LastName']));
    $params->put("name_first", mb_strtoupper($patient['FirstName']));
    $params->put("name_middle", mb_strtoupper($patient['MiddleName']));
    $params->put("name_suffix", strtoupper($patient['Suffix']));
    $params->put("birth_date", $patient['Bday']);
    }
    
    #employer info
    $employer_no = preg_replace($pattern, '', $member['employer_no']);
    $employer_name = $member['employer_name'];
    if (strlen($employer_no) < 12) {
    	$employer_no = "";
    	$employer_name = "";
   	}

    $params->put("employer_no", $employer_no);
    // $params->put("employer_name", utf8_decode(strtoupper($employer_name)));
    $params->put("employer_name", mb_strtoupper($employer_name));
    $params->put("relation", $member['relation']);

    $baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
	);

    #Logo of PHIC
    $logo_path = $baseurl.'images/phic_logo.png'; #<-- Comment this for LOCAL TESTING!
    $params->put("logo_path", $logo_path);
  
