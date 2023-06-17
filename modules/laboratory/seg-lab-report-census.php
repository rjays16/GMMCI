<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
include_once($root_path.'include/care_api_classes/reports/JasperReport.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
DEFINE('REPORT','LABORATORY CENSUS');

$srvObj=new SegLab;
$jasper = new JasperReport();
$objInfo = new Hospital_Admin();
$row = $objInfo->getAllHospitalInfo();

$params = array();
$datas = array();
$data = array();

if ($row) {
    $row['hosp_agency'] = strtoupper($row['hosp_agency']);
    $row['hosp_name']   = strtoupper($row['hosp_name']);
    $row['hosp_addr1']  = strtoupper($row['hosp_addr1']);
}
else {
    $row['hosp_country'] = "Republic of the Philippines";
    $row['hosp_agency']  = "PROVINCE OF BUKIDNON";
    $row['hosp_name']    = "BPH";
    $row['hosp_addr1']   = "Bukidnon, Philippines";
}

$lab_group = $_GET['lab_group'];
$start_date = $_GET['fromdate'];
$end_date = $_GET['todate'];
$total_days = round(abs(strtotime($end_date) - strtotime($start_date)) / 86400, 0) + 1;
$i = 0;
if ($end_date >= $start_date)
{
  for ($day = 0; $day < $total_days; $day++)
  {
    $dailyDate = date("Y-m-d", strtotime("{$start_date} + {$day} days"));
	$data = $srvObj->getLabCensusDaily($dailyDate,$lab_group);
	if($data){
		foreach ($data as $key => $value) {
			$group = $value['group_name'];
			$datas[$i] = array(
						'name'			=> $value['name'],
						'serv' 			=> (int)$value['serv'],
						'date_daily' 	=> $value['date_daily'],
						);
			$i ++;
		}
	}else{
		$datas[$i] = array(
						'name'			=> "",
						'serv' 			=> 0,
						'date_daily' 	=> date("d", strtotime($dailyDate)),
						);
			$i ++;
	}
  }
}

// var_dump($datas);exit();

if( $_GET['lab_group'] == 'all'){
	$group = 'ALL GROUP';
}else if($_GET['lab_group'] == 'notBB'){
	$group = 'BLOOD BANK IS NOT INCLUDED';
}else{
	$group = strtoupper($group);
}
$params = array(
	        'image_path'	=> $jasper->getLogoPath(),
			'hosp_name' 	=> $row['hosp_name'],
			'hosp_add' 		=> $row['hosp_addr1'],
			'report' 		=> REPORT,
			'lab_group' 	=> $group,
			'date' 			=> date("F d, Y", strtotime($_GET['fromdate'])).' - '.date("F d, Y", strtotime($_GET['todate'])),
				);


showReport('laboratory_census',$params,$datas,$_GET['format']);

?>