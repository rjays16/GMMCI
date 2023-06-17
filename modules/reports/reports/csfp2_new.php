<?php
# NOTE : Temporary Workaround! This file needs table adjustment on database for getching complete data.
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path . 'include/inc_environment_global.php');
include_once($root_path . 'include/care_api_classes/class_globalconfig.php');
include('parameters.php');
$total_doc_charge;
$total_doc_discount;
$total_doc_coverage;
$total_hci_charge;
$total_hci_discount;
$total_hci_coverage;
$patient_name;
$total_charge;
$total_coverage;
$total_discount;
$excess;
$memcategory_id;
$is_discharged;
$total_meds;
$total_xlo;
$total_outside;
$bill_dte;
$charity;
$pid;
$total_phic_hci;
$total_phic_doc;

$enc_no = $param['enc_no'];
$pid = $param['pid'];

#patient info
$strSQL = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
                    p.name_3 AS ThirdName, p.name_middle AS MiddleName, p.suffix as Suffix, p.date_birth as Bday
                    FROM care_person AS p
                    WHERE p.pid = '$pid'";


$result = $db->Execute($strSQL);
$patient = $result->FetchRow();

$pt_name = mb_strtoupper($patient['LastName'] . ", " . $patient['FirstName'] . " " . (is_null($patient['Suffix']) || $patient['Suffix'] == "" ? "" : $patient['Suffix']) .
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


$strSQL = "SELECT sbp.dr_nr, cp.name_first, cp.name_last, cp.name_middle, cp.suffix, max_acc.accreditation_nr , sbp.role_area,
             sbp.dr_charge,sbp.dr_claim
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
} else {
    $params->put("authorize", '');
    $params->put("official", '');
}

// 1st case rate @ jeff 04-04-18
$caseSQL = "SELECT 
                  sbc.`package_id`
                FROM
                  `seg_billing_caserate` AS sbc
                WHERE sbc.`bill_nr` =  " . $db->qstr($bill_nr) . "
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
                WHERE sbc.`bill_nr` =  " . $db->qstr($bill_nr) . "
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
$applied_discount = getTotalAppliedDiscounts($enc_no);
$billDetails = getBillingDetails($bill_nr);
$billDetails['total_doc_coverage'] = (($billDetails['total_doc_coverage'] < $billDetails['total_doc_coverage2']) ? $billDetails['total_doc_coverage2'] : $billDetails['total_doc_coverage']);
$total_doc_charge = $billDetails['total_doc_charge'];
$total_doc_discount = $billDetails['total_doc_discount'];
$total_doc_coverage = $billDetails['total_doc_coverage'];
$total_hci_charge = $billDetails['total_hci_charge'];
$total_hci_discount = $billDetails['total_hci_discount'];
$total_hci_coverage = $billDetails['total_services_coverage'];
$patient_name = $billDetails['name'];
$total_charge = $billDetails['total_doc_charge'] + $billDetails['total_hci_charge'];
$total_coverage = $billDetails['total_doc_coverage'] + $billDetails['total_services_coverage'];
$total_discount = $billDetails['total_doc_discount'] + $billDetails['total_hci_discount'];
$excess = $total_charge - $total_discount -  $total_coverage;
$memcategory_id = $billDetails['memcategory_id'];
$is_discharged = $billDetails['is_discharged'];
$total_meds = (($billDetails['total_meds']) ? $billDetails['total_meds'] : 0);
$total_xlo = (($billDetails['total_xlo']) ? $billDetails['total_xlo'] : 0);
$total_outside = $total_meds + $total_xlo;
$bill_dte = $billDetails['bill_dte'];
$charity = (($billDetails['accommodation_type'] == '1') ? true : false);
$pid = $billDetails['pid'];
$total_phic_hci = $billDetails['total_services_coverage2'];
$total_phic_doc = $billDetails['total_doc_coverage2'];

if (($excess <= 0 || (($memcategory_id == HSM || $memcategory_id == SM) && $charity)) && ($total_outside <= 0)) {

    $check_value_part3 = '/';
    $check_value2_part3 = '';
    $total_hci = number_format($total_hci_charge, 2, '.', ',');
    $total_doc = number_format($total_doc_charge, 2, '.', ',');
    $total_grand = number_format($total_hci_charge + $total_doc_charge, 2, '.', ',');
} else {
    $check_value_part3 = '';
    $check_value2_part3 = '/';
    $total_hci2 = number_format($total_hci_charge, 2, '.', ',');
    $total_doc2 = number_format($total_doc_charge, 2, '.', ',');
    $total_hci_coverage = number_format($total_phic_hci, 2, '.', ',');
    $total_doc_coverage = number_format($total_phic_doc, 2, '.', ',');
    $total_hci_excess = number_format($total_hci_charge - ($total_hci_discount + $total_phic_hci), 2, '.', ',');
    $total_doc_excess = number_format($total_doc_charge - ($total_doc_discount + $total_phic_doc), 2, '.', ',');

    $total_hci = "";
    $total_doc = "";
    $total_grand = "";
    $memberPatientCheck = "";
    if ($total_hci_excess != '' && $total_hci_excess != '0.00') {
        $memberPatientCheck  = "/";
    } else {
        $memberPatientCheck = "";
    }

    if ($total_doc_excess != '' && $total_doc_excess != '0.00') {
        $memberPatientCheck2  = "/";
    } else {
        $memberPatientCheck2  = "";
    }

    if ($total_meds > 0 && ($memcategory_id != HSM || $memcategory_id != SM)) {
        $bchecknone = "";
        $bcheck = "/";
        $bvalue = "P " . number_format($total_meds, 2, '.', ',');
    } else {
        $bchecknone = "/";
        $bcheck = "";
        $bvalue = "";
    }

    if ($total_xlo > 0 && ($memcategory_id != HSM || $memcategory_id != SM)) {
        $bchecknone2 = "";
        $bcheck2 = "/";
        $bvalue2 = "P " . number_format($total_xlo, 2, '.', ',');
    } else {
        $bchecknone2 = "/";
        $bcheck2 = "";
        $bvalue2 = "";
    }

    if ($total_hci_discount != 0)
        $discount_hci = number_format($total_hci_charge - $total_hci_discount, 2, '.', ',');
    if ($total_doc_discount != 0)
        $discount_doc = number_format($total_doc_charge - $total_doc_discount, 2, '.', ',');
}

if ((!isHouseCase($enc_no)) || isHouseCase($enc_no) || (isHouseCase($enc_no) && $isServ == $opdDept[0]) || (!isHouseCase($enc_no) && $isServ == $opdDept[1])) {
    if (is_object($doctors)) {
        while ($row = $doctors->FetchRow()) {
            if (!is_null($applied_discount)) {
                $doc_discount =  $row['dr_charge'] * $applied_discount;
                $copay_amount = $row['dr_charge'] - $doc_discount - $row['dr_claim'];
            } else {
                $doc_discount =  $row['dr_charge'];
                $copay_amount = $row['dr_charge'] - $doc_discount - $row['dr_claim'];
            }
            $check_value = '';
            $check_value1 = '';
            if ($copay_amount <= 0) {
                $check_value = ' / ';
                $copay_amount = '';
            } else {
                $check_value1 = ' / ';
                $copay_amount = number_format($copay_amount, 2, '.', ',');
            }
            $accreditation_nr = preg_replace($pattern, '', $row['accreditation_nr']);
            $data[$rowindex] = array(
                'rowindex' => $rowindex + 1,
                'groupidx' => $grpindex,
                'accreditation_nr' => $accreditation_nr,
                'name_last' => utf8_decode(strtoupper($row['name_last'])),
                'name_first' => utf8_decode(strtoupper($row['name_first'])),
                'name_middle' => utf8_decode(strtoupper($row['name_middle'])),
                'suffix' => strtoupper($row['suffix']),
                'date_signed' => (is_null($accreditation_nr) || $accreditation_nr == "" ? "" : $sign_date),
                'check_value' => $check_value,
                'check_value1' => $check_value1,
                'copay_amount' => $copay_amount,
                'check_value_part3' => $check_value_part3,
                'check_value2_part3' => $check_value2_part3,
                'total_hci' => $total_hci,
                'total_doc' => $total_doc,
                'total_grand' => $total_grand,
                'total_hci2' => $total_hci2,
                'discount_hci' => $discount_hci,
                'total_hci_coverage' => $total_hci_coverage,
                'total_hci_excess' => $total_hci_excess,
                'memberPatientCheck' => $memberPatientCheck,
                'total_doc2' => $total_doc2,
                'discount_doc' => $discount_doc,
                'total_doc_coverage' => $total_doc_coverage,
                'total_doc_excess' => $total_doc_excess,
                'memberPatientCheck2' => $memberPatientCheck2,
                'bchecknone' => $bchecknone,
                'bcheck' => $bcheck,
                'bvalue' => $bvalue,
                'bchecknone2' => $bchecknone2,
                'bcheck2' => $bcheck2,
                'bvalue2' => $bvalue2,
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
            $data[$rowindex] = array(
                'rowindex' => $rowindex + 1,
                'groupidx' => $grpindex,
                'accreditation_nr' => "",
                'name_last' => "",
                'name_first' => "",
                'name_middle' => "",
                'suffix' => "",
                'date_signed' => "",
                'check_value' => "",
                'check_value1' => "",
                'copay_amount' => "",
                'check_value_part3' => $check_value_part3,
                'check_value2_part3' => $check_value2_part3,
                'total_hci' => $total_hci,
                'total_doc' => $total_doc,
                'total_grand' => $total_grand,
                'total_hci2' => $total_hci2,
                'discount_hci' => $discount_hci,
                'total_hci_coverage' => $total_hci_coverage,
                'total_hci_excess' => $total_hci_excess,
                'memberPatientCheck' => $memberPatientCheck,
                'total_doc2' => $total_doc2,
                'discount_doc' => $discount_doc,
                'total_doc_coverage' => $total_doc_coverage,
                'total_doc_excess' => $total_doc_excess,
                'memberPatientCheck2' => $memberPatientCheck2,
                'bchecknone' => $bchecknone,
                'bcheck' => $bcheck,
                'bvalue' => $bvalue,
                'bchecknone2' => $bchecknone2,
                'bcheck2' => $bcheck2,
                'bvalue2' => $bvalue2,
            );
            $rowindex++;
        }
    } else {
        $data[0]['code'] = NULL;
    }
} else { //housecase
    $pfroles = array();
    while ($row = $doctors->FetchRow()) {
        $pfroles[] = $row['role_area'];
    }
    $pfroles = array_unique($pfroles);
    $case = findCaseType($bill_nr);

    // var_dump($pfroles);die;
    $result = getHouseCaseDoctor($case, $pfroles);

    while ($row = $result->FetchRow()) {
        $accreditation_nr = preg_replace($pattern, '', $row['accreditation_nr']);
        $data[$rowindex] = array(
            'rowindex' => $rowindex + 1,
            'groupidx' => $grpindex,
            'accreditation_nr' => $accreditation_nr,
            'name_last' => utf8_decode(strtoupper($row['name_last'])),
            'name_first' => utf8_decode(strtoupper($row['name_first'])),
            'name_middle' => utf8_decode(strtoupper($row['name_middle'])),
            'suffix' => strtoupper($row['suffix']),
            'date_signed' => (is_null($accreditation_nr) || $accreditation_nr == "" ? "" : $sign_date),
            'check_value' => $check_value,
            'check_value1' => $check_value1,
            'copay_amount' => $copay_amount,
            'check_value_part3' => $check_value_part3,
            'check_value2_part3' => $check_value2_part3,
            'total_hci' => $total_hci,
            'total_doc' => $total_doc,
            'total_grand' => $total_grand,
            'total_hci2' => $total_hci2,
            'discount_hci' => $discount_hci,
            'total_hci_coverage' => $total_hci_coverage,
            'total_hci_excess' => $total_hci_excess,
            'memberPatientCheck' => $memberPatientCheck,
            'total_doc2' => $total_doc2,
            'discount_doc' => $discount_doc,
            'total_doc_coverage' => $total_doc_coverage,
            'total_doc_excess' => $total_doc_excess,
            'memberPatientCheck2' => $memberPatientCheck2,
            'bchecknone' => $bchecknone,
            'bcheck' => $bcheck,
            'bvalue' => $bvalue,
            'bchecknone2' => $bchecknone2,
            'bcheck2' => $bcheck2,
            'bvalue2' => $bvalue2,
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
        $data[$rowindex] = array(
            'rowindex' => $rowindex + 1,
            'groupidx' => $grpindex,
            'accreditation_nr' => "",
            'name_last' => "",
            'name_first' => "",
            'name_middle' => "",
            'suffix' => "",
            'date_signed' => "",
            'check_value' => "",
            'check_value1' => "",
            'copay_amount' => "",
            'check_value_part3' => $check_value_part3,
            'check_value2_part3' => $check_value2_part3,
            'total_hci' => $total_hci,
            'total_doc' => $total_doc,
            'total_grand' => $total_grand,
            'total_hci2' => $total_hci2,
            'discount_hci' => $discount_hci,
            'total_hci_coverage' => $total_hci_coverage,
            'total_hci_excess' => $total_hci_excess,
            'memberPatientCheck' => $memberPatientCheck,
            'total_doc2' => $total_doc2,
            'discount_doc' => $discount_doc,
            'total_doc_coverage' => $total_doc_coverage,
            'total_doc_excess' => $total_doc_excess,
            'memberPatientCheck2' => $memberPatientCheck2,
            'bchecknone' => $bchecknone,
            'bcheck' => $bcheck,
            'bvalue' => $bvalue,
            'bchecknone2' => $bchecknone2,
            'bcheck2' => $bcheck2,
            'bvalue2' => $bvalue2,
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
function getCalculateDate($bill_dte)
{
    $bill_dte = date('Y-m-d', strtotime($bill_dte));
    $numberofdays = 5;

    // date_default_timezone_set('Asia/Manila');
    // $add_days = 3;
    $bill_dte = date('Y-m-d H:i:s', strtotime($bill_dte));

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

function isHouseCase($encno)
{
    global $db;

    $housecase = true;
    $strSQL = "select fn_isHouseCase('" . $encno . "') as casetype";
    if ($result = $db->Execute($strSQL)) {
        if ($result->RecordCount()) {
            if ($row = $result->FetchRow()) {
                $housecase = is_null($row["casetype"]) ? true : ($row["casetype"] == 1);
            }
        }
    }
    return $housecase;
}

function findCaseType($billno)
{
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
                if ($row['rate_type'] == 1)
                    $first_type = $row['case_type'];
                else
                    $second_type = $row['case_type'];
            }
        }
    }

    //$case = 0;
    if ($first_type == 'm' && ($second_type == 'm' || is_null($second_type) || $second_type == '')) {
        $case = 1;
    } elseif ($first_type == 'p' && ($second_type == 'p' || is_null($second_type) || $second_type == '')) {
        $case = 2;
    } elseif ($first_type != $second_type && $second_type != '') {
        $case = 3;
    }

    return $case;
}

function getHouseCaseDoctor($case, $pfroles)
{
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
        $filter .= ' OR ' . $anesCond;
    }

    if (in_array("D1", $pfroles)) {
        $filter .= ' OR ' . $attnCond;
    }

    if (in_array("D3", $pfroles)) {
        $filter .= ' OR ' . $surgCond;
    }

    if (in_array("D2", $pfroles)) {
        $filter .= ' OR ' . $attnCond;
    }

    $orCount = substr_count($filter, "OR");

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
function getOpdAsuResult()
{
    $obj_global = new GlobalConfig();
    // $opdValue = $obj_global->getOpdAsu();
    // return $opdValue;
}

/**
 * Select values from global config for dynamic values
 * @author Jeff Ponteras 04-25-18
 * @return values of personnel in charge csf
 */
function getInChargeResult()
{
    $obj_global = new GlobalConfig();
    $inCharge = $obj_global->getInCharge();
    return $inCharge;
}

function getTotalAppliedDiscounts($enc_no)
{

    global $db;

    $sql = "SELECT SUM(discount) AS total_discount FROM seg_billingapplied_discount 
            WHERE encounter_nr = " . $db->qstr($enc_no);
    $rs = $db->Execute($sql);
    if ($rs) {
        if ($rs->RecordCount() > 0) {
            $row = $rs->FetchRow();
            return $row['total_discount'];
        } else {

            return 0;
        }
    } else {
        var_dump(1);
        die;
        return 0;
    }
}

function getBillingDetails($bill_nr)
{
    global $db;


    $sql = "SELECT 
				  sbe.accommodation_type,
				  sbe.bill_dte,
				  ce.pid,
				  `fn_get_person_name` (ce.pid) AS name,
				  ce.`encounter_nr`,
				  sbe.`total_doc_charge`,
				  SUM(
				    IFNULL(sbc.total_msc_coverage, 0) +
					IFNULL(sbc.total_acc_coverage, 0) +
					IFNULL(sbc.total_med_coverage, 0) +
					IFNULL(sbc.total_srv_coverage, 0) +
					IFNULL(sbc.total_ops_coverage, 0)
				  ) AS total_services_coverage,
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
				    IFNULL(sbd.total_acc_discount, 0) + IFNULL(sbd.total_msc_discount, 0) + IFNULL(sbd.total_med_discount, 0) + IFNULL(sbd.total_ops_discount, 0) + IFNULL(sbd.total_srv_discount, 0) + IFNULL(sbd.total_sup_discount, 0)
				  ) AS total_hci_discount,
				  (SELECT SUM(dr_claim) FROM seg_billing_pf a WHERE a.bill_nr = sbe.bill_nr AND a.hcare_id='18' 
				  	) as total_doc_coverage2,
					sem.memcategory_id,
					ce.is_discharged,
					ser.total_meds,
					ser.total_xlo,
				  (SELECT SUM(hci_amount) FROM seg_billing_caserate WHERE bill_nr = sbe.bill_nr ) as total_services_coverage2,
				  (SELECT SUM(pf_amount) FROM seg_billing_caserate WHERE bill_nr = sbe.bill_nr ) as total_doc_coverage2
				FROM
				  care_encounter ce 
				  INNER JOIN seg_billing_encounter sbe
				    ON sbe.`encounter_nr` = ce.`encounter_nr` 
				  LEFT JOIN seg_encounter_memcategory sem
				  	ON sem.encounter_nr = sbe.encounter_nr
				  INNER JOIN seg_billing_coverage sbc 
				    ON sbe.bill_nr = sbc.`bill_nr` AND sbc.hcare_id='18'
				  INNER JOIN seg_billingcomputed_discount sbd 
				    ON sbd.`bill_nr` = sbe.`bill_nr`
				  LEFT JOIN seg_encounter_reimbursed ser
				    ON ser.encounter_nr = ce.encounter_nr 
				WHERE sbe.`bill_nr` =" . $db->qstr($bill_nr);
    if ($result = $db->Execute($sql)) {
        if ($result->RecordCount()) {
            $row = $result->FetchRow();
            return $row;
            // $row['total_doc_coverage'] = (($row['total_doc_coverage']<$row['total_doc_coverage2']) ? $row['total_doc_coverage2'] : $row['total_doc_coverage']);
            // $this->total_doc_charge = $row['total_doc_charge'];
            // $this->total_doc_discount = $row['total_doc_discount'];
            // $this->total_doc_coverage = $row['total_doc_coverage'];
            // $this->total_hci_charge = $row['total_hci_charge'];
            // $this->total_hci_discount = $row['total_hci_discount'];
            // $this->total_hci_coverage = $row['total_services_coverage'];
            // $this->patient_name = $row['name'];
            // $this->total_charge = $row['total_doc_charge']+$row['total_hci_charge'];
            // $this->total_coverage = $row['total_doc_coverage']+$row['total_services_coverage'];
            // $this->total_discount = $row['total_doc_discount']+$row['total_hci_discount'];
            // $this->excess = $this->total_charge - $this->total_discount -  $this->total_coverage;
            // $this->memcategory_id = $row['memcategory_id'];
            // $this->is_discharged = $row['is_discharged'];
            // $this->total_meds = (($row['total_meds']) ? $row['total_meds'] : 0);
            // $this->total_xlo = (($row['total_xlo']) ? $row['total_xlo'] : 0);
            // $this->total_outside = $this->total_meds + $this->total_xlo;
            // $this->bill_dte = $row['bill_dte'];
            // $this->charity = (($row['accommodation_type']=='1') ? true : false);
            // $this->pid = $row['pid'];
            // $this->total_phic_hci = $row['total_services_coverage2'];
            // $this->total_phic_doc = $row['total_doc_coverage2'];
        }
    }
    return false;
}
