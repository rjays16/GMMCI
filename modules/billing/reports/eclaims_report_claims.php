<?php
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once $root_path . 'include/inc_environment_global.php';
include_once($root_path . 'include/care_api_classes/EclaimsReport.php');

$objInfo = new Hospital_Admin();
define('PHIC_ID', 18);
global $db;
$EclaimsReport = new EclaimsReport();
//hospital info
if ($row1 = $objInfo->getAllHospitalInfo()) {
    $row1['hosp_name'] = strtoupper($row1['hosp_name']);
    $row1['hosp_addr'] = strtoupper($row1['hosp_addr1']);
} else {
    $row1['hosp_name'] = "Gonzales Maranan Medical Center Incorporated";
    $row1['hosp_addr'] = "Quezon Ave., Digos City, Davao del Sur";
}

$filterBy = $_GET['status'] == 'returned' ? 'RETURN' : 'DENIED';

$report = $filterBy;
$from_dte = date('Y-m-d', strtotime($_GET['fromdte']));
$to_dte = date('Y-m-d', strtotime($_GET['todte']));
$date_span = date('M d, Y', strtotime($_GET['fromdte'])) . ' to ' . date('M d, Y', strtotime($_GET['todte']));
$hcare_id = $_GET['hcare_id'];
$whereQuery = "";

$returnedTable = "seg_claim_returned";
if($filterBy != 'RETURN'){
  $returnedTable = "seg_claim_denied";
}

if ($_GET['dtype'] == "all") {
  $header = "ALL INSURANCE";
}
else if($_GET['dtype'] == "non-phic"){
  $header = "NON-PHIC";
}else{
  $header = $db->getOne("SELECT name FROM care_insurance_firm WHERE hcare_id =".$db->qstr($_GET['dtype'])." ");

  $whereQuery = "WHERE scp.`hcare_id` = " . $db->qstr($_GET['dtype']);
}

$reasondb = $_GET['status'] == 'returned' ? '`seg_eclaims_return_claim_status` sercs'  : 
            '`seg_eclaims_denied_claim_status` sedcs';
$ondb = $_GET['status'] == 'returned' ? 'ON sercs.`status_id` = secs.`id`' : 'ON sedcs.`status_id` = secs.`id`';
$deficiencies_json = $_GET['status'] == 'returned' ? 'sercs.`deficiencies_json`' : 'sedcs.`reasons_json`';

// $sql = "SELECT 
//   Patient,
//   member_name,
//   Admission_Date,
//   Discharge_Date,
//   sec.`encounter_nr`,
//   sec.`transmit_no` AS Transmittal_Number,
//   (SELECT 
//     sbc.`package_id` 
//   FROM
//     `seg_billing_encounter` sbe 
//     INNER JOIN `seg_billing_caserate` sbc 
//       ON sbc.`bill_nr` = sbe.bill_nr 
//   WHERE sbe.`is_deleted` IS NULL 
//     AND sbc.rate_type = '1' 
//     AND sbe.encounter_nr = sec.`encounter_nr` 
//   LIMIT 1) AS package_id1,
    
//   (SELECT 
//     sbc.`hci_amount` 
//   FROM
//     `seg_billing_encounter` sbe 
//     INNER JOIN `seg_billing_caserate` sbc 
//       ON sbc.`bill_nr` = sbe.bill_nr 
//   WHERE sbe.`is_deleted` IS NULL 
//     AND sbc.rate_type = '1' 
//     AND sbe.encounter_nr = sec.`encounter_nr` 
//   LIMIT 1) AS hci_amount1,
    
//   (SELECT 
//     sbc.`amount` 
//   FROM
//     `seg_billing_encounter` sbe 
//     INNER JOIN `seg_billing_caserate` sbc 
//       ON sbc.`bill_nr` = sbe.bill_nr 
//   WHERE sbe.`is_deleted` IS NULL 
//     AND sbc.rate_type = '1' 
//     AND sbe.encounter_nr = sec.`encounter_nr` 
//   LIMIT 1) AS amount1,
    
//   (SELECT 
//     sbc.`pf_amount` 
//   FROM
//     `seg_billing_encounter` sbe 
//     INNER JOIN `seg_billing_caserate` sbc 
//       ON sbc.`bill_nr` = sbe.bill_nr 
//   WHERE sbe.`is_deleted` IS NULL 
//     AND sbc.rate_type = '1' 
//     AND sbe.encounter_nr = sec.`encounter_nr` 
//   LIMIT 1) AS pf_amount1,
    
//   (SELECT 
//     sbc.`package_id` 
//   FROM
//     `seg_billing_encounter` sbe 
//     INNER JOIN `seg_billing_caserate` sbc 
//       ON sbc.`bill_nr` = sbe.bill_nr 
//   WHERE sbe.`is_deleted` IS NULL 
//     AND sbc.rate_type = '2' 
//     AND sbe.encounter_nr = sec.`encounter_nr` 
//   LIMIT 1) AS package_id2,
       
//   (SELECT 
//     sbc.`hci_amount` 
//   FROM
//     `seg_billing_encounter` sbe 
//     INNER JOIN `seg_billing_caserate` sbc 
//       ON sbc.`bill_nr` = sbe.bill_nr 
//   WHERE sbe.`is_deleted` IS NULL 
//     AND sbc.rate_type = '2' 
//     AND sbe.encounter_nr = sec.`encounter_nr` 
//   LIMIT 1) AS hci_amount2,
         
//   (SELECT 
//     sbc.`amount` 
//   FROM
//     `seg_billing_encounter` sbe 
//     INNER JOIN `seg_billing_caserate` sbc 
//       ON sbc.`bill_nr` = sbe.bill_nr 
//   WHERE sbe.`is_deleted` IS NULL 
//     AND sbc.rate_type = '2' 
//     AND sbe.encounter_nr = sec.`encounter_nr` 
//   LIMIT 1) AS amount2,
         
//   (SELECT 
//     sbc.`pf_amount` 
//   FROM
//     `seg_billing_encounter` sbe 
//     INNER JOIN `seg_billing_caserate` sbc 
//       ON sbc.`bill_nr` = sbe.bill_nr 
//   WHERE sbe.`is_deleted` IS NULL 
//     AND sbc.rate_type = '2' 
//     AND sbe.encounter_nr = sec.`encounter_nr` 
//   LIMIT 1) AS pf_amount2,
//   {$deficiencies_json},
//   sete.`transmission_date` 
// FROM
//   seg_eclaims_claim AS sec 
//   LEFT JOIN seg_eclaims_claim_status AS secs 
//     ON sec.`id` = secs.`claim_id` 
//   INNER JOIN 
//     (SELECT 
//       cp.`pid`,
//       cp.`name_first`,
//       cp.`name_last`,
//       cp.`name_middle`,
//       CONCAT(
//         cp.`name_last`,
//         ', ',
//         cp.`name_first`,
//         ' ',
//         cp.`name_middle`
//       ) AS Patient,
//       ce.`encounter_nr` AS Encounter_Number,
//       ce.`discharge_date` AS Discharge_Date,
//       ce.`admission_dt` AS Admission_Date 
//     FROM
//       care_person AS cp 
//       INNER JOIN care_encounter AS ce 
//         ON cp.`pid` = ce.`pid`) AS person_id 
//     ON person_id.Encounter_Number = sec.`encounter_nr` 
//   LEFT JOIN 
//     (SELECT 
//       seim.`encounter_nr`,
//       CONCAT(
//         seim.`member_lname`,
//         ', ',
//         seim.`member_fname`,
//         ' ',
//         seim.`member_mname`
//       ) AS member_name 
//     FROM
//       `seg_encounter_insurance_memberinfo` seim) AS member_info 
//     ON member_info.`encounter_nr` = sec.`encounter_nr` 
//     WHERE 
//       secs.`claim_date_received` BETWEEN DATE(" . $db->qstr($from_dte) . ") 
//       AND DATE(" . $db->qstr($to_dte) . ") 
//       AND secs.`status` = {$db->qstr($filterBy)} ";

// $sql = "SELECT
//   Patient,
//   member_name,
//   Admission_Date,
//   Discharge_Date,
//   stds.`encounter_nr`,
//   st.`transmit_no` AS Transmittal_Number,
//   (SELECT
//     sbc.`package_id`
//   FROM
//     `seg_billing_encounter` sbe
//     INNER JOIN `seg_billing_caserate` sbc
//       ON sbc.`bill_nr` = sbe.bill_nr
//   WHERE sbe.`is_deleted` IS NULL
//     AND sbc.rate_type = '1'
//     AND sbe.encounter_nr = stds.`encounter_nr`
//   LIMIT 1) AS package_id1,
//   (SELECT
//     sbc.`hci_amount`
//   FROM
//     `seg_billing_encounter` sbe
//     INNER JOIN `seg_billing_caserate` sbc
//       ON sbc.`bill_nr` = sbe.bill_nr
//   WHERE sbe.`is_deleted` IS NULL
//     AND sbc.rate_type = '1'
//     AND sbe.encounter_nr = stds.`encounter_nr`
//   LIMIT 1) AS hci_amount1,
//   (SELECT
//     sbc.`amount`
//   FROM
//     `seg_billing_encounter` sbe
//     INNER JOIN `seg_billing_caserate` sbc
//       ON sbc.`bill_nr` = sbe.bill_nr
//   WHERE sbe.`is_deleted` IS NULL
//     AND sbc.rate_type = '1'
//     AND sbe.encounter_nr = stds.`encounter_nr`
//   LIMIT 1) AS amount1,
//   (SELECT
//     sbc.`pf_amount`
//   FROM
//     `seg_billing_encounter` sbe
//     INNER JOIN `seg_billing_caserate` sbc
//       ON sbc.`bill_nr` = sbe.bill_nr
//   WHERE sbe.`is_deleted` IS NULL
//     AND sbc.rate_type = '1'
//     AND sbe.encounter_nr = stds.`encounter_nr`
//   LIMIT 1) AS pf_amount1,
//   (SELECT
//     sbc.`package_id`
//   FROM
//     `seg_billing_encounter` sbe
//     INNER JOIN `seg_billing_caserate` sbc
//       ON sbc.`bill_nr` = sbe.bill_nr
//   WHERE sbe.`is_deleted` IS NULL
//     AND sbc.rate_type = '2'
//     AND sbe.encounter_nr = stds.`encounter_nr`
//   LIMIT 1) AS package_id2,
//   (SELECT
//     sbc.`hci_amount`
//   FROM
//     `seg_billing_encounter` sbe
//     INNER JOIN `seg_billing_caserate` sbc
//       ON sbc.`bill_nr` = sbe.bill_nr
//   WHERE sbe.`is_deleted` IS NULL
//     AND sbc.rate_type = '2'
//     AND sbe.encounter_nr = stds.`encounter_nr`
//   LIMIT 1) AS hci_amount2,
//   (SELECT
//     sbc.`amount`
//   FROM
//     `seg_billing_encounter` sbe
//     INNER JOIN `seg_billing_caserate` sbc
//       ON sbc.`bill_nr` = sbe.bill_nr
//   WHERE sbe.`is_deleted` IS NULL
//     AND sbc.rate_type = '2'
//     AND sbe.encounter_nr = stds.`encounter_nr`
//   LIMIT 1) AS amount2,
//   (SELECT
//     sbc.`pf_amount`
//   FROM
//     `seg_billing_encounter` sbe
//     INNER JOIN `seg_billing_caserate` sbc
//       ON sbc.`bill_nr` = sbe.bill_nr
//   WHERE sbe.`is_deleted` IS NULL
//     AND sbc.rate_type = '2'
//     AND sbe.encounter_nr = stds.`encounter_nr`
//   LIMIT 1) AS pf_amount2,
//   st.`transmit_dte`
// FROM
//   seg_transmittal AS st
//   INNER JOIN seg_transmittal_details AS stds
//     ON st.`transmit_no` = stds.`transmit_no`
//   INNER JOIN
//     (SELECT
//       cp.`pid`,
//       cp.`name_first`,
//       cp.`name_last`,
//       cp.`name_middle`,
//       CONCAT(
//         cp.`name_last`,
//         ', ',
//         cp.`name_first`,
//         ' ',
//         cp.`name_middle`
//       ) AS Patient,
//       ce.`encounter_nr` AS Encounter_Number,
//       ce.`discharge_date` AS Discharge_Date,
//       ce.`admission_dt` AS Admission_Date
//     FROM
//       care_person AS cp
//       INNER JOIN care_encounter AS ce
//         ON cp.`pid` = ce.`pid`) AS person_id
//     ON person_id.Encounter_Number = stds.`encounter_nr`
//   LEFT JOIN
//     (SELECT
//       seim.`encounter_nr`,
//       CONCAT(
//         seim.`member_lname`,
//         ', ',
//         seim.`member_fname`,
//         ' ',
//         seim.`member_mname`
//       ) AS member_name
//     FROM
//       `seg_encounter_insurance_memberinfo` seim) AS member_info
//     ON member_info.`encounter_nr` = stds.`encounter_nr`
//     WHERE 
//       DATE(st.`transmit_dte`) BETWEEN " . $db->qstr($from_dte) . "
//       AND " . $db->qstr($to_dte) . "
//        {$filterStatus} ";





       $sql = "SELECT
  cp.`pid`,
  scpr.`hcare_id`,
  scpr.ref_no,
  member_name,
  CONCAT (
    cp.`name_last`,
    ', ',
    cp.`name_first`,
    ' ',
    cp.`name_middle`
  ) AS Patient,
  ce.`encounter_nr` AS Encounter_Number,
  ce.`discharge_date` AS Discharge_Date,
  ce.`admission_dt` AS Admission_Date,
  stds.`encounter_nr`,
  stds.`transmit_no` AS Transmittal_Number,
  (SELECT
    sbc.`package_id`
  FROM
    `seg_billing_encounter` sbe
    INNER JOIN `seg_billing_caserate` sbc
      ON sbc.`bill_nr` = sbe.bill_nr
  WHERE sbe.`is_deleted` IS NULL
    AND sbc.rate_type = '1'
    AND sbe.encounter_nr = stds.`encounter_nr`
  LIMIT 1) AS package_id1,
  (SELECT
    sbc.`hci_amount`
  FROM
    `seg_billing_encounter` sbe
    INNER JOIN `seg_billing_caserate` sbc
      ON sbc.`bill_nr` = sbe.bill_nr
  WHERE sbe.`is_deleted` IS NULL
    AND sbc.rate_type = '1'
    AND sbe.encounter_nr = stds.`encounter_nr`
  LIMIT 1) AS hci_amount1,
  (SELECT
    sbc.`amount`
  FROM
    `seg_billing_encounter` sbe
    INNER JOIN `seg_billing_caserate` sbc
      ON sbc.`bill_nr` = sbe.bill_nr
  WHERE sbe.`is_deleted` IS NULL
    AND sbc.rate_type = '1'
    AND sbe.encounter_nr = stds.`encounter_nr`
  LIMIT 1) AS amount1,
  (SELECT
    sbc.`pf_amount`
  FROM
    `seg_billing_encounter` sbe
    INNER JOIN `seg_billing_caserate` sbc
      ON sbc.`bill_nr` = sbe.bill_nr
  WHERE sbe.`is_deleted` IS NULL
    AND sbc.rate_type = '1'
    AND sbe.encounter_nr = stds.`encounter_nr`
  LIMIT 1) AS pf_amount1,
  (SELECT
    sbc.`package_id`
  FROM
    `seg_billing_encounter` sbe
    INNER JOIN `seg_billing_caserate` sbc
      ON sbc.`bill_nr` = sbe.bill_nr
  WHERE sbe.`is_deleted` IS NULL
    AND sbc.rate_type = '2'
    AND sbe.encounter_nr = stds.`encounter_nr`
  LIMIT 1) AS package_id2,
  (SELECT
    sbc.`hci_amount`
  FROM
    `seg_billing_encounter` sbe
    INNER JOIN `seg_billing_caserate` sbc
      ON sbc.`bill_nr` = sbe.bill_nr
  WHERE sbe.`is_deleted` IS NULL
    AND sbc.rate_type = '2'
    AND sbe.encounter_nr = stds.`encounter_nr`
  LIMIT 1) AS hci_amount2,
  (SELECT
    sbc.`amount`
  FROM
    `seg_billing_encounter` sbe
    INNER JOIN `seg_billing_caserate` sbc
      ON sbc.`bill_nr` = sbe.bill_nr
  WHERE sbe.`is_deleted` IS NULL
    AND sbc.rate_type = '2'
    AND sbe.encounter_nr = stds.`encounter_nr`
  LIMIT 1) AS amount2,
  (SELECT
    sbc.`pf_amount`
  FROM
    `seg_billing_encounter` sbe
    INNER JOIN `seg_billing_caserate` sbc
      ON sbc.`bill_nr` = sbe.bill_nr
  WHERE sbe.`is_deleted` IS NULL
    AND sbc.rate_type = '2'
    AND sbe.encounter_nr = stds.`encounter_nr`
  LIMIT 1) AS pf_amount2,
  stds.`transmit_dte`
FROM
  (SELECT
    scp.`post_dte`,
    scr.`encounter_nr`,
    scr.`ref_no`,
    scp.`hcare_id`
  FROM
    seg_claim_posting AS scp
    INNER JOIN {$returnedTable} AS scr
      ON scp.`ref_no` = scr.`ref_no` {$whereQuery}) as scpr
  
  INNER JOIN
    (SELECT
      stdss.`encounter_nr`,
      stdss.`is_returned`,
      st.`transmit_dte`,
      st.`transmit_no`
    FROM
      seg_transmittal AS st
      INNER JOIN seg_transmittal_details AS stdss
        ON st.`transmit_no` = stdss.`transmit_no`
    GROUP BY stdss.`encounter_nr`) AS stds
    ON scpr.encounter_nr = stds.encounter_nr
  INNER JOIN care_encounter AS ce
    ON stds.`encounter_nr` = ce.`encounter_nr`
  INNER JOIN care_person AS cp
    ON cp.pid = ce.`pid`
  LEFT JOIN
    (SELECT
      seim.`encounter_nr`,
      CONCAT (
        seim.`member_lname`,
        ', ',
        seim.`member_fname`,
        ' ',
        seim.`member_mname`
      ) AS member_name
    FROM
      `seg_encounter_insurance_memberinfo` seim) AS member_info
    ON member_info.`encounter_nr` = stds.`encounter_nr`
WHERE DATE (scpr.`post_dte`) BETWEEN " . $db->qstr($from_dte) . "
  AND ". $db->qstr($to_dte);


$result = $db->Execute($sql);
$counter = 1;

if ($result) {
    while ($row = $result->FetchRow()) {
        $isPHIC = false;
        if($row['hcare_id'] == '18'){
          $isPHIC = true;
        }

        $data[$i++] = array(
            'counter' => $counter++,
            'full_name' => $row['Patient'],
            'member_name' => $row['member_name'],
            'admission_date' => $row['Admission_Date'] != null ? date('Y-m-d', strtotime($row['Admission_Date'])) : "-",
            'discharge_date' => date('Y-m-d', strtotime($row['Discharge_Date'])),
            'encounter_nr' => $row['encounter_nr'],
            'transmittal_no' => $row['Transmittal_Number'],
            'package_id1' => $isPHIC ? $row['package_id1'] : '',
            'hci_amount1' => $isPHIC ? $row['hci_amount1'] ? number_format($row['hci_amount1']) : '' : '' ,
            'pf_amount1' => $isPHIC ? $row['pf_amount1'] ? number_format($row['pf_amount1']) : '' : '',
            'package_id2' => $isPHIC ? $row['package_id2'] : '',
            'hci_amount2' => $isPHIC ? $row['hci_amount2'] ? number_format($row['hci_amount2']) : '' : '',
            'pf_amount2' => $isPHIC ? $row['pf_amount2'] ? number_format($row['pf_amount2']) : '' : '',
            'total_amount' => $EclaimsReport->calculateTotal($row['amount1'], $row['amount2']) ? number_format($EclaimsReport->calculateTotal($row['amount1'], $row['amount2'])) : '',
            'transmission_date' => date('Y-m-d', strtotime($row['transmit_dte'])),
        );    
    }
}

$total_rec = $counter - 1;
$params = array(
    "hosp_name" => $row1['hosp_name'],
    "hosp_add" => $row1['hosp_addr'],
    "date_range" => $date_span,
    "total_record" => (string)$total_rec,
    "report" => strtoupper($report . " claims"),
    "header" => $header,
);

showReport('eclaims_report_claims', $params, $data, $_GET['reportFormat']);
?>
