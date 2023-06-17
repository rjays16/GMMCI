<?php
#created by Genz
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');

require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/care_api_classes/billing/class_claim.php';
require_once $root_path.'include/inc_environment_global.php';

global $db;

$from = date('Y-m-d', strtotime($_GET['from']));
$to = date('Y-m-d', strtotime($_GET['to']));

if ($_GET['report'] == 'claim_unpaid'){
  $date_span = date('M d,Y', strtotime($_GET['from'])).' - '.date('M d,Y', strtotime($_GET['to']));
}

$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
  $row['hosp_agency'] = strtoupper($row['hosp_agency']);
  $row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
  $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
  $row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
  $row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
}

$hosptal_name = $row['hosp_name'];
$hosptal_add = $row['hosp_addr1'];

#--------------------------------------------------------------------------------------

$report_type = $_GET['report'];
$insurance_type = $_GET['dtype'];
$personnel = $_GET['personnel'];

if($insurance_type=='PHIC'){
  $insurance_nr = 18;
  $header_dtype = "List of Claims Unpaid (PHIC)";
  $health_care = "scp.hcare_id = '18' AND st.`hcare_id` = '18' ";
}else if($insurance_type=='HMO'){
  $insurance_nr != 18;
  $header_dtype = "List of Claims Unpaid (HMO)";
  $health_care = "scp.hcare_id != '18' AND st.`hcare_id` != '18' ";
}else{
  $insurance_nr != 18;
  $header_dtype = "List of Claims Unpaid";
  $health_care = "scp.hcare_id != '18' AND st.`hcare_id` != '18' ";
}

if($report_type=='claim_unpaid'){
  $date_condition = "DATE(post_dte) BETWEEN DATE(".$db->qstr($from).") AND DATE(".$db->qstr($to).")";
}


if($personnel == 'all'){
  $personnel_condition = '';
}else{
  $personnel_condition = "fb.create_id = '".$personnel."' AND";
}

$transInfo = new Claim();
$trans_list = $transInfo->getListOfPaidTransmitNo(2);


if ($_GET['dtype'] == "all") {
  $hcare_condition = "!= ''";
  $header = "ALL INSURANCE";
}
else if($_GET['dtype'] == "non-phic"){
  $hcare_condition = "!= 18";
  $header = "NON-PHIC";
}else{
  $header = $db->getOne("SELECT name FROM care_insurance_firm WHERE hcare_id =".$db->qstr($_GET['dtype'])." ");
}


//added by julz
$insurancetype = $_GET['dtype'];
//===============================================================//
  
//  $medicardInsurance = '31';
//  $lingapInsurance = '66';
//  $starCareInsurance = '48';
//  $tortugaInsurance = '36';
//  $tortugaOneInsurance = '43';
//  $tortugaTwoInsurance = '44';
//  $tortugaCompanyInsurance = '45';
//  $tagumCoopInsurance = '54';
//  $dswdInsurance = '50';
//  $pcsoInsurance = '51';
//  $maxicareInsurance = '32';
//  $cocolifeInsurance = '33';
//  $seecoInsurance = '46';
//  $socaresInsurance = '47';
//  $oneCoophealthInsurance = '59';
//  $philCareInsurance = '42';
//  $intellicareInsurance = '29';
//  $valucareInsurance = '38';
//  $caritasHealthShieldInsurance = '30';
//  $asianLifeInsurance = '49';
//  $dasurecoInsurance = '64';
//  $iCareInsurance = '35';
//  $officeOfThePresidentInsurance = '63';
//  $insurances = array(
//    $medicardInsurance,
//    $lingapInsurance,
//    $starCareInsurance,
//    $tortugaInsurance,
//    $tagumCoopInsurance,
//    $dswdInsurance,
//    $pcsoInsurance,
//    $tortugaOneInsurance,
//    $tortugaTwoInsurance,
//    $tortugaCompanyInsurance,
//    $maxicareInsurance,
//    $cocolifeInsurance,
//    $seecoInsurance,
//    $socaresInsurance,
//    $oneCoophealthInsurance,
//    $philCareInsurance,
//    $intellicareInsurance,
//    $valucareInsurance,
//    $caritasHealthShieldInsurance,
//    $asianLifeInsurance,
//    $dasurecoInsurance,
//    $iCareInsurance,
//    $officeOfThePresidentInsurance
//  );

  $insurances = $db->getOne("SELECT name FROM care_insurance_firm WHERE hcare_id =".$db->qstr($insurancetype));

  $insurancesQuery = "";
  $insuranceJoin = "";
  $isInsurance = false;
  $selectedPostClaim = '18';
  if($insurances){ //if MEDICARD
    $insurancesQuery = "sbco.total_acc_coverage,
                sbco.total_med_coverage,
                sbco.total_sup_coverage,
                sbco.total_srv_coverage,
                sbco.total_ops_coverage,
                sbco.total_d1_coverage,
                sbco.total_d2_coverage,
                sbco.total_d3_coverage,
                sbco.total_d4_coverage,
                sbco.total_msc_coverage,
                sbco.total_pf_coverage,";
    $insuranceJoin = "INNER JOIN seg_billing_coverage AS sbco
        ON sbco.bill_nr = sbe.bill_nr AND sbco.hcare_id = ".$insurancetype."";
    $isInsurance = true;
    $selectedPostClaim = $insurancetype;
  }

//===============================================================//

// $query = "SELECT DISTINCT d.encounter_nr,
//   ce.pid,
//   fn_get_pid_lastfirstmi (ce.pid) AS full_name,
//   h.transmit_no,
//   h.transmit_dte,
//   ".$medicard."
//   CONCAT(
//     DATE_FORMAT(
//       (
//         CASE
//           WHEN admission_dt IS NULL 
//           OR admission_dt = '' 
//           THEN encounter_date 
//           ELSE admission_dt 
//         END
//       ),
//       '%b %e, %Y %l:%i%p'
//     ),
//     ' to ',
//     (
//       CASE
//         WHEN ce.discharge_date IS NULL 
//         OR ce.discharge_date = '' 
//         THEN 'present' 
//         ELSE DATE_FORMAT(
//           STR_TO_DATE(
//             ce.modify_time,
//             '%Y-%m-%d %H:%i:%s'
//           ),
//           '%b %e, %Y %l:%i%p'
//         ) 
//       END
//     )
//   ) AS confine_period
// FROM
//   (
//     (
//       seg_transmittal AS h 
//       INNER JOIN seg_transmittal_details AS d 
//         ON h.transmit_no = d.transmit_no
//     ) 
//     INNER JOIN care_encounter AS ce 
//       ON d.encounter_nr = ce.encounter_nr
//     INNER JOIN seg_billing_encounter AS sbe
//       ON sbe.encounter_nr = ce.encounter_nr
//     AND sbe.is_deleted IS NULL
//       OR  sbe.is_deleted = 0
//       ".$medicardJoin."
//   ) 
//   INNER JOIN care_person AS cp 
//     ON ce.pid = cp.pid 
//   WHERE (
//       (h.hcare_id = ".$db->qstr($insurancetype).") 
//       AND (
//         DATE(ce.admission_dt) BETWEEN ".$db->qstr($from)." AND ".$db->qstr($to)."
//       )
//     ) AND NOT EXISTS 
//     (SELECT 
//       * 
//     FROM
//       seg_claim_posting a 
//       LEFT JOIN seg_claim_pay_hosp b 
//         ON a.ref_no = b.ref_no
//       LEFT JOIN seg_claim_pay_patient c 
//         ON c.ref_no = a.ref_no
//       LEFT JOIN seg_claim_pay_pf e 
//         ON e.ref_no = a.ref_no 
//     WHERE (
//         b.encounter_nr = d.encounter_nr     
//       ) 
//       AND a.hcare_id = '18') 
//     ORDER BY h.transmit_dte ASC";


    $query = "SELECT DISTINCT
      d.encounter_nr,
      ce.pid,
      fn_get_pid_lastfirstmi (ce.pid) AS full_name,
      h.transmit_no,
      h.transmit_dte,
      ".$insurancesQuery."
      CONCAT (
        DATE_FORMAT (
          (
            CASE
              WHEN admission_dt IS NULL
              OR admission_dt = ''
              THEN encounter_date
              ELSE admission_dt
            END
          ),
          '%b %e, %Y %l:%i%p'
        ),
        ' to ',
        (
          CASE
            WHEN ce.discharge_date IS NULL
            OR ce.discharge_date = ''
            THEN 'present'
            ELSE DATE_FORMAT (
              STR_TO_DATE (
                ce.modify_time,
                '%Y-%m-%d %H:%i:%s'
              ),
              '%b %e, %Y %l:%i%p'
            )
          END
        )
      ) AS confine_period
    FROM
      (
        (
          seg_transmittal AS h
          INNER JOIN seg_transmittal_details AS d
            ON h.transmit_no = d.transmit_no
        )
        INNER JOIN care_encounter AS ce
          ON d.encounter_nr = ce.encounter_nr
        INNER JOIN seg_billing_encounter AS sbe
          ON sbe.encounter_nr = ce.encounter_nr
          AND sbe.is_deleted IS NULL
          OR sbe.is_deleted = 0
        ".$insuranceJoin."
      )
      INNER JOIN care_person AS cp
        ON ce.pid = cp.pid
    WHERE (
        (h.hcare_id = ".$db->qstr($insurancetype).")
        AND (
          DATE (h.transmit_dte) BETWEEN ".$db->qstr($from)." AND ".$db->qstr($to)."
        )
      )
      AND NOT EXISTS
      (SELECT
        *
      FROM
        seg_claim_posting a
        LEFT JOIN seg_claim_pay_hosp b
          ON a.ref_no = b.ref_no
        LEFT JOIN seg_claim_pay_patient c
          ON c.ref_no = a.ref_no
        LEFT JOIN seg_claim_pay_pf e
          ON e.ref_no = a.ref_no
      WHERE (b.encounter_nr = d.encounter_nr)
        AND a.hcare_id = ".$db->qstr($selectedPostClaim).")
    ORDER BY h.transmit_dte ASC";

$rs = $db->Execute($query);
$count = $rs->RecordCount();

$net_total = 0;

if($rs){
  if($rs->RecordCount()>0){
    $i = 0;
    while($row = $rs->FetchRow()){
      $cofine_total_amount = 0;

      if($isInsurance){
        $cofine_total_amount = $row['total_acc_coverage'] +  $row['total_med_coverage'] + $row['total_sup_coverage'] + $row['total_srv_coverage'] + $row['total_ops_coverage'] + $row['total_d1_coverage'] + $row['total_d2_coverage'] + $row['total_d3_coverage'] + $row['total_d4_coverage'] + $row['total_msc_coverage'] + $row['total_pf_coverage'];
      }

      $data[$i] = array('case_no'=>$row['encounter_nr'],
                        'pid'=>$row['pid'],
                        'trans_no'=>$row['transmit_no'],
                        'trans_date'=>date('M d,Y',strtotime($row['transmit_dte'])),
                        'patient_name'=>ucwords($row['full_name']),
                        'confine_period'=>$row['confine_period'],
                        'confine_amount'=> $isInsurance ? number_format($cofine_total_amount, 2) : $row['package'],
                        'no'=>($i+1));

      $net_total +=  $isInsurance ? $cofine_total_amount : $row['package'];
      
      $i++;
    }
  }else{
    $data['case_no'][0] = "No data";
  }
}else{
  $data['case_no'][0] = "No data";
}

$params = array("hosp_country"=>$row['hosp_country'],
  "hosp_agency"=>$row['hosp_agency'],
  "hosp_name"=>$hosptal_name,
  "hosp_addr1"=>$hosptal_add,
  "date_span"=>$date_span,
  "insurance_type"=>$header_dtype,
  "header"=>$header,
  "TOTAL"=>'Total Patient '.$count,
  "netotal"=>number_format($net_total,2)


  );

  // die("asdasd");
showReport('claims_unpaid',$params,$data,$_GET['reportFormat']);
?>