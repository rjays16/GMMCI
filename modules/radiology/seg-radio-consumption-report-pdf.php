<?php
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/care_api_classes/class_radiology.php';
require_once $root_path.'include/care_api_classes/class_personell.php';
require_once $root_path.'include/inc_environment_global.php';

$from = date('Y-m-d', strtotime($_GET['from']));
$to = date('Y-m-d', strtotime($_GET['to']));
$patient_type = $_GET['patient_type'];
$dept_nr = $_GET['dept_nr'];

$objInfo = new Hospital_Admin();
$objPersonell = new Personell();
$objRad = new SegRadio();

if ($row1 = $objInfo->getAllHospitalInfo()) {
	$row1['hosp_name']   = strtoupper($row1['hosp_name']);
	$row1['hosp_addr']   = strtoupper($row1['hosp_addr1']);
}
else {
	$row1['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
	$row1['hosp_addr']   = "Quezon Ave., Digos City, Davao del Sur";
}
#--------------------------------------------------------------------------------------

$dr_nr = $_GET['dr_nr'];
$date_span =  date('M d, Y', strtotime($_GET['from'])).' to '.date('M d, Y', strtotime($_GET['to']));

if($dr_nr){
	$where .= " AND h.dr_nr = ".$db->qstr($dr_nr);
	$dr_res = $objPersonell->get_Person_name($dr_nr);
	$dr_name = "DR. ".$dr_res['dr_name'];
}

$totals = array("amount"=>0);

$rs = $objRad->getDrPatients($dr_nr, $from, $to, $patient_type, $dept_nr);

if($rs){
	if($rs->RecordCount()>0){
		$i = 0;
		while($row = $rs->FetchRow()){
			$data[$i] = array('date'=>($row['served_date'] ? ucwords($row['served_date']) : ucwords($row['request_date']) ),
				              'name_patient'=>$row['ordername'],
				              'examination'=>$row['service_name'],
				              'amount'=>number_format($row['price_alter'],2),
				              'or'=>$row['or_no']
				             );

			$totals['amount'] += $row['price_alter'];
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
	            "date_range"=>$date_span,
	            "dr_name"=>$dr_name,
	            "total_amount"=>number_format($totals['amount'],2)
	           );

showReport('radConsumptionReport',$params,$data,'pdf');
?>