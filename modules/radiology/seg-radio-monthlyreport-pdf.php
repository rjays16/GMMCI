<?php
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/care_api_classes/class_radiology.php';
require_once $root_path.'include/care_api_classes/class_personell.php';
require_once $root_path.'include/care_api_classes/class_department.php';
require_once $root_path.'include/inc_environment_global.php';

$from = date('Y-m-d', strtotime($_GET['fromdate']));
$to = date('Y-m-d', strtotime($_GET['todate']));
// $rad_nr = $_GET['radtech'];
$doctor_nr = $_GET['doctor_nr'];
$dept_nr = $_GET['department'];

$objInfo = new Hospital_Admin();
$objPersonell = new Personell();
$objRad = new SegRadio();
$objDep = new Department();

if ($row1 = $objInfo->getAllHospitalInfo()) {
    $row1['hosp_name']   = strtoupper($row1['hosp_name']);
    $row1['hosp_addr']   = strtoupper($row1['hosp_addr1']);
}
else {
    $row1['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
    $row1['hosp_addr']   = "Quezon Ave., Digos City";
}
#--------------------------------------------------------------------------------------

$date_span =  date('M d, Y', strtotime($_GET['fromdate'])).' to '.date('M d, Y', strtotime($_GET['todate']));
$rad_name = '';

// if($rad_nr!= 'all'){
//     $rad_res = $objPersonell->getRadTechOne($rad_nr);
//     $rad_name = $rad_res['name_first'].' '.$rad_res['name_last'];
// }
// else{
//     $rad_name = 'ALL RAD. TECH';
// }

if($doctor_nr!='0'){
    $doctor_res = $objPersonell->getResDoctorOne($doctor_nr);
    $doctor_name = $doctor_res['name_first'].' '.$doctor_res['name_last'];
}
else{
    $doctor_name = 'ALL DOCTORS';
}

$totals = array("amount"=>0);

$dep = '';
$dep2 = '';
if($dept_nr!='all'){
    $dep = $objDep->getShortNameOne($dept_nr);

    // NOTE: Hardcoded lines incoming
    if($dep == 'XRAY'){
        $dep = 'X-RAY';
        $dep2 = 'X-RAY';
    }
    elseif($dep == 'USD'){
        $dep = 'ULTRASOUND';
        $dep2 = 'ULTRASOUND';
    }
    elseif($dep == 'CT'){
        $dep = 'CT SCAN';
        $dep2 = 'CT SCAN';
    }
}
else{
    $dep = 'ALL';
    $dep2 = 'All';
}


$rs = $objRad->getRadPatients('all', $doctor_nr, $from, $to, $dept_nr);

if($rs){
    if($rs->RecordCount()>0){
        $i = 0;
        while($row = $rs->FetchRow()){
            $data[$i] = array('date'=>ucwords($row['served_date']),
                              'name_patient'=>$row['order_name'],
                              'examination'=>$row['service_name'],
                              'or'=>$row['or_no'],
                              'amount'=>number_format($row['amount'],2),
                              
                             );

            $totals['amount'] += $row['amount'];
            $i++;
        }
    }else{
        $data['patient'][0] = "No data";
    }
}else{
    $data['patient'][0] = "No data";
}

$params = array("hosp_name"=>$row1['hosp_name'],
                "hosp_addr1"=>$row1['hosp_addr1'],
                "dep_name"=>$dep.' DEPARTMENT',
                "summ_dep"=>'Summary of '.$dep2.' Patients',
                "date_range"=>$date_span,
                // "rad_name"=>'',
                "doctor_name"=>$doctor_name,
                "total_amount"=>number_format($totals['amount'],2)
               );

showReport('radMonthlyReport',$params,$data,'pdf');
?>