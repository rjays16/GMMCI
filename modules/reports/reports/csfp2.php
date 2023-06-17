<?php
    # NOTE : Temporary Workaround! This file needs table adjustment on database for getching complete data.
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    include_once($root_path.'include/care_api_classes/class_globalconfig.php');
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

    $pt_name = mb_strtoupper($patient['LastName'] . ", " . $patient['FirstName'] . " " .
               (is_null($patient['Suffix']) || $patient['Suffix'] == "" ? "" : $patient['Suffix']) .
               " " . $patient['MiddleName']);

    $params->put("patient_name", $pt_name);

    #signatory info
    $strSQL = "SELECT ss.personell_nr, ss.signatory_position, cpn.name_last, cpn.name_first, 
               cpn.name_middle, cpn.suffix, cpn.sex
               FROM seg_signatory ss INNER JOIN care_personell cp ON cp.nr = ss.personell_nr
               INNER JOIN care_person cpn ON cpn.pid = cp.pid WHERE ss.document_code = 'csf'";

    $result = $db->Execute($strSQL);
    $signatory = $result->FetchRow();


    $name_title = (strtoupper($signatory[sex]) == "M" ? "MR." : "MS.");

    $signatory_name = $name_title . " " . mb_strtoupper($signatory['name_first'] . " " . $signatory['name_middle'] . " " . 
                      $signatory['name_last'] . " " . (is_null($signatory['suffix']) || $signatory['suffix'] == "" ? "" : $signatory['suffix']));

    $params->put("signatory_name", $signatory_name);
    $params->put("designation", strtoupper($signatory['signatory_position']));
               
    #bill info
    $strSQL = "SELECT sbe.accommodation_type, sbe.bill_nr, sbe.bill_dte FROM seg_billing_encounter sbe 
               WHERE sbe.encounter_nr = '$enc_no' AND sbe.is_deleted IS NULL AND sbe.is_final = 1";

    $result = $db->Execute($strSQL);
    if (!$result) {
        die("Error: No final bill for this encounter yet!");
    }

    $bill = $result->FetchRow();
    $isServ = $bill['accommodation_type'];
    $bill_nr = $bill['bill_nr'];

    $sign_date = getCalculateDate($bill['bill_dte']);

    $params->put("sign_date", $sign_date); 
    $params->put("date_signed", $sign_date);


    $strSQL = "SELECT sbp.dr_nr, cp.name_first, cp.name_last, cp.name_middle, cp.suffix, max_acc.accreditation_nr , sbp.role_area
               FROM seg_billing_pf sbp LEFT JOIN (SELECT sda.dr_nr, sda.accreditation_nr, MAX(sda.create_dt) AS create_dt 
               FROM seg_dr_accreditation sda GROUP BY sda.dr_nr) AS max_acc ON max_acc.dr_nr = sbp.dr_nr 
               INNER JOIN care_personell cpl ON cpl.nr = sbp.dr_nr INNER JOIN care_person cp ON cp.pid = cpl.pid
               WHERE sbp.bill_nr = '$bill_nr'
               AND sbp.`hcare_id`='18'
                ORDER BY sbp.role_area ASC, sbp.dr_nr ASC";

    $doctors = $db->Execute($strSQL);
    if (!$result) {
        die("Error: No professional fee coverage!");
    }

    $inCharge = getInChargeResult();
    $resInCharge = explode(',', $inCharge);
    
    if ($inCharge) {
        $params->put("authorize", $resInCharge[0]);
        $params->put("official", $resInCharge[1]);
    }else{
         $params->put("authorize",'');
        $params->put("official",'');
    }

    // 1st case rate @ jeff 04-04-18
    $caseSQL = "SELECT 
                  sbc.`package_id`
                FROM
                  `seg_billing_caserate` AS sbc
                WHERE sbc.`bill_nr` =  ".$db->qstr($bill_nr)."
                  AND sbc.`rate_type` = '1' 
                  #AND sbc.`is_deleted` <> '1'<--remove hashtag and this comment after altering the table to add is_deleted column.
                  ";
    $cases = $db->Execute($caseSQL);
    // var_dump($caseSQL);die;
    $caseRate = $cases->FetchRow();
    $params->put("fcase", $caseRate['package_id']);
    
    // 2nd case rate @ jeff 04-04-18
    $caseSQL = "SELECT 
                  sbc.`package_id`
                FROM
                  `seg_billing_caserate` AS sbc
                WHERE sbc.`bill_nr` =  ".$db->qstr($bill_nr)."
                  AND sbc.`rate_type` = '2' 
                  #AND sbc.`is_deleted` <> '1'<--remove hashtag and this comment after altering the table to add is_deleted column.
                  ";
    $cases = $db->Execute($caseSQL);
    $caseRate = $cases->FetchRow();
    $params->put("scase", $caseRate['package_id']);

    $pattern = array('/[a-zA-Z]/', '/[ -]+/', '/^-|-$/');
    $rowindex = 0;
    $grpindex = 1;
    $data = array();
    $opdDept = getOpdAsuResult();
    $opdDept = explode(',', $opdDept);

    # Temporary workaround conditions...
    if ( (!isHouseCase($enc_no)) || isHouseCase($enc_no) || (isHouseCase($enc_no) && $isServ == $opdDept[0]) || (!isHouseCase($enc_no) && $isServ == $opdDept[1])) {
        if (is_object($doctors)){
            while($row=$doctors->FetchRow()){
                $accreditation_nr = preg_replace($pattern, '', $row['accreditation_nr']);
                $data[$rowindex] = array('rowindex' => $rowindex+1,
                                         'groupidx' => $grpindex,
                                         'accreditation_nr' => $accreditation_nr,
                                         'name_last' => utf8_decode(strtoupper($row['name_last'])),
                                         'name_first' => utf8_decode(strtoupper($row['name_first'])),
                                         'name_middle' => utf8_decode(strtoupper($row['name_middle'])),
                                         'suffix' => strtoupper($row['suffix']),
                                         'date_signed' => (is_null($accreditation_nr) || $accreditation_nr == "" ? "" : $sign_date)
                                        );               
               $rowindex++;
               if ($rowindex % 3 == 0) {
                    $grpindex++;
               }
            }  

            //add blank rows if necessary
            $rowspergroup = 3;
            $addrows = ($rowspergroup - $rowindex % 3);
            $totalrows = $addrows + $rowindex;
            $rowindex++;
            while ($rowindex <= $totalrows) {
                $data[$rowindex] = array('rowindex' => $rowindex+1,
                                         'groupidx' => $grpindex,
                                         'accreditation_nr' => "",
                                         'name_last' => "",
                                         'name_first' => "",
                                         'name_middle' => "",
                                         'suffix' => "",
                                         'date_signed' => ""
                                        );               
                $rowindex++;
            }  
        }else{
            $data[0]['code'] = NULL; 
        }
    } else { //housecase
        $pfroles = array();
        while($row=$doctors->FetchRow()){
            $pfroles[] = $row['role_area'];
        }
        $pfroles = array_unique($pfroles);
        $case = findCaseType($bill_nr);

        // var_dump($pfroles);die;
        $result = getHouseCaseDoctor($case, $pfroles);
        
        while($row=$result->FetchRow()){
               $accreditation_nr = preg_replace($pattern, '', $row['accreditation_nr']);
               $data[$rowindex] = array('rowindex' => $rowindex+1,
                                        'groupidx' => $grpindex,
                                        'accreditation_nr' => $accreditation_nr,
                                        'name_last' => utf8_decode(strtoupper($row['name_last'])),
                                        'name_first' => utf8_decode(strtoupper($row['name_first'])),
                                        'name_middle' => utf8_decode(strtoupper($row['name_middle'])),
                                        'suffix' => strtoupper($row['suffix']),
                                        'date_signed' => (is_null($accreditation_nr) || $accreditation_nr == "" ? "" : $sign_date)
                                       );               
            $rowindex++;
            if ($rowindex % 3 == 0) {
                $grpindex++;
            }
        }

        //add blank rows if necessary
        $rowspergroup = 3;
        $addrows = ($rowspergroup - $rowindex % 3);
        $totalrows = $addrows + $rowindex;
        $rowindex++;
        while ($rowindex <= $totalrows) {
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                                     'groupidx' => $grpindex,
                                     'accreditation_nr' => "",
                                     'name_last' => "",
                                     'name_first' => "",
                                     'name_middle' => "",
                                     'suffix' => "",
                                     'date_signed' => ""
                                    );               
            $rowindex++;
        }  
    }    

    // $samp= getHouseCaseDoctor($case, $pfroles);
    /**
    * Created By Jeff Ponteras
    * Created On 04/09/2018
    * Get Calculate Date Excluding Weekends
    * @param string bill_dte
    * @return date
    **/
    function getCalculateDate($bill_dte) {
        $bill_dte = date('Y-m-d',strtotime($bill_dte));
        $numberofdays = 5;

    // date_default_timezone_set('Asia/Manila');
        // $add_days = 3;
        $bill_dte = date('Y-m-d H:i:s',strtotime($bill_dte));     
    
        // $numberofdays = 5;
        $date_orig = new DateTime($bill_dte);
        
        $t = $date_orig->format("U"); //get timestamp


        // // loop for X days
        // for($i=0; $i<$numberofdays ; $i++){

        //     // add 1 day to timestamp
        //     $addDay = 86400;

        //     // get what day it is next day
        //     $nextDay = date('w', ($t+$addDay));

        //     // if it's Saturday or Sunday get $i-1
        //     if($nextDay == 0 || $nextDay == 6) {
        //         $i--;
        //     }

        //     // modify timestamp, add 1 day
        //     $t = $t+$addDay;
        // }

        return date('mdY', ($t));
    }    

    function isHouseCase($encno) {
        global $db;

        $housecase = true;
        $strSQL = "select fn_isHouseCase('" . $encno . "') as casetype";
        if ($result=$db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                     $housecase = is_null($row["casetype"]) ? true : ($row["casetype"] == 1);
                }
            }
        }
        return $housecase;
    }

    function findCaseType($billno) {
        global $db;
        $first_type = '';
        $second_type = '';
        $strSQL = "SELECT p.case_type, sc.rate_type
                    FROM seg_billing_caserate sc 
                    INNER JOIN seg_case_rate_packages p 
                        ON p.`code` = sc.`package_id`
                    WHERE bill_nr = '$billno'"; 
        
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    if($row['rate_type']==1)
                        $first_type = $row['case_type'];
                    else
                        $second_type = $row['case_type'];
                }
            }
        }

        //$case = 0;
        if ($first_type == 'm' && ($second_type == 'm' || is_null($second_type) || $second_type == '')) {
            $case = 1;
        } elseif($first_type == 'p' && ($second_type == 'p' || is_null($second_type) || $second_type == '')) {
            $case = 2;
        } elseif($first_type != $second_type && $second_type!='') {
            $case = 3;
        }

        return $case;
    }

    function getHouseCaseDoctor($case, $pfroles) {
        global $db;
        $attnCond = "cpl.is_housecase_attdr = 1";
        $surgCond = "cpl.is_housecase_surgeon = 1";
        $anesCond = "cpl.is_housecase_anesth = 1";

        // Comment out by jeff as per CF2 inline consistency.
        // if ($case == 1) { //medical case - default Dr. Vega
        //     $strSQL .= $surgCond;
        //     if (in_array("D4",$pfroles) && in_array("D3",$pfroles) && in_array("D1",$pfroles)){
        //         $strSQL .= " OR " . $anesCond . " OR ".$attnCond;
        //     }
        //     if (in_array("D3",$pfroles) && in_array("D1",$pfroles)){
        //         $strSQL .= " OR ".$attnCond;
        //     }
        // }
        // elseif($case == 2) { //surgical case - default Dr. Vega and Dr. Audan
        //     $strSQL .= $surgCond;
        //     if (in_array("D4",$pfroles) && in_array("D3",$pfroles) && in_array("D1",$pfroles)){
        //         $strSQL .= " OR " . $anesCond . " OR ".$attnCond;
        //     }
        //     else {
        //         $strSQL .= " OR " . $anesCond;
        //     }
        // } 
        // else { //mixed case - default Dr. Vega, Dr. Audan and Dr. Concha(if with D1 or D2)

        // $strSQ;
        // }

        $filter = '';

        if (in_array("D4", $pfroles)) {
            $filter .= ' OR '.$anesCond;
        } 

        if (in_array("D1", $pfroles)) {
            $filter .= ' OR '.$attnCond;
        }

        if (in_array("D3", $pfroles)) {
            $filter .= ' OR '.$surgCond;
        }

        if (in_array("D2", $pfroles)) {
            $filter .= ' OR '.$attnCond;
        }

        $orCount = substr_count($filter,"OR");

        $filter = substr($filter, 3);
        



        $strSQL = "SELECT cpl.nr, cp.name_first, cp.name_last, cp.name_middle, cp.suffix, max_acc.accreditation_nr,
           cpl.is_housecase_surgeon, cpl.is_housecase_anesth, cpl.is_housecase_attdr  
           FROM care_personell cpl LEFT JOIN (SELECT sda.dr_nr, sda.accreditation_nr, MAX(sda.create_dt) AS create_dt 
           FROM seg_dr_accreditation sda GROUP BY sda.dr_nr) AS max_acc ON max_acc.dr_nr = cpl.nr 
           INNER JOIN care_person cp ON cp.pid = cpl.pid WHERE " . $filter;


        // $orderby = " ORDER BY cpl.is_housecase_anesthsurgeon DESC, cpl.is_housecase_anesth DESC, cpl.is_housecase_attdr DESC";

        $result = $db->Execute($strSQL);

        return $result;

    }

    /**
     * Select values from global config for dynamic values
     * @author Jeff Ponteras 03-1620-18
     * @return int OPD-dept values
     */
    function getOpdAsuResult(){
        $obj_global = new GlobalConfig();
        // $opdValue = $obj_global->getOpdAsu();
        // return $opdValue;
    }

    /**
     * Select values from global config for dynamic values
     * @author Jeff Ponteras 04-25-18
     * @return values of personnel in charge csf
     */
    function getInChargeResult(){
        $obj_global = new GlobalConfig();
        $inCharge = $obj_global->getInCharge();
        return $inCharge;
    }
    