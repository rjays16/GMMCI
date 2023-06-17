<?php
require('./roots.php');
include_once($root_path . 'include/care_api_classes/reports/JasperReport.php');
require_once($root_path.'include/care_api_classes/class_lab_results.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

$jasper = new JasperReport();
$objHosp = new Hospital_Admin();
$lab_obj = new Lab_Results();
$personell_obj = new Personell();

$refno = $_GET['refno'];
$group_id = $_GET['group_id'];
$service_code = $_GET['service_code'];
$nth_take = $_GET['nth_take'];
$exam =$lab_obj->getServiceName($service_code);
$hospInfo = $objHosp->getAllHospitalInfo();
$hospContact = $objHosp->getHospitalContact('main_info_phone');
$lab_result = $lab_obj->getLabResult($refno, $group_id, $nth_take);
$patient = $lab_obj->get_patient_data($refno, $group_id);
$group_name = $lab_obj->getGroupName($group_id);
$form_id = $lab_obj->getGroupForm($group_id);
$jrxml = $lab_obj->getFormJrxml($form_id);
$pathologist_name =$personell_obj->getNameTitle($lab_result['pathologist_pid']);
$pathologist = $personell_obj->is_personnel($lab_result['pathologist_pid']);
$med_tech = $personell_obj->is_personnel($lab_result['med_tech_pid']);
$medtech_name =$personell_obj->getNameTitle($lab_result['med_tech_pid']); 
$ordername = mb_strtoupper($patient['name_last']).", "
				.mb_strtoupper($patient['name_first'])." "
				.mb_strtoupper($patient['name_middle']);

$params = array();

//lab result
if($service_code){
	$sql = "SELECT 
			  par.name,
			  res.`result_value` 
			FROM
			  seg_lab_result res 
			  LEFT JOIN seg_lab_result_params par 
			    ON par.param_id = res.`param_id` 
			WHERE res.refno = '$refno' AND res.`nth_take` = $nth_take";

	$result = $lab_obj->exec_query($sql);
	if($result){
		while($row = $result->FetchRow()){
			$params[strtolower($row['name'])] = $row['result_value'];
			$params[strtoupper($row['name'])] = $row['result_value'];
			$params[$row['name']] = $row['result_value'];
		}
	}
}
//end lab result

$data = array('name'=>'');
$imgpath = $jasper->getLogoPath();
$params['hosp_name']=$hospInfo['hosp_name'];
$params['hosp_add']=$hospInfo['hosp_addr1'];
$params['name']=$ordername;
$params['name_last'] = $patient['name_last'];
$params['name_first'] = $patient['name_first'];
$params['name_middle'] = $patient['name_middle'];
$params['age']=$patient['age'];
$params['gender']=strtolower($patient['sex']) == 'f' ? 'Female' : 'Male';
$params['room']=strtoupper($patient['ward_name'] ? $patient['ward_name'].' Room '.$patient['current_room_nr'] : '');
$params['date']=$lab_result['service_date'];
$params['pathologist']=$pathologist_name;
$params['med_tech']=$medtech_name;
$params['logo']=$imgpath;
$params['group']=strtoupper($group_name);
$params['remarks']=$lab_result['remarks'];
$params['case']=$patient['encounter_nr'];
$params['hrn']=$patient['pid'];
$params['hosptelno']=$hospContact['main_info_phone'];
$params['birthdate']=date_format(date_create($patient['date_birth']), "M d, Y");
$params['clinical diagnosis']=$patient['clinical_info'];
$params['occupation']=$patient['occupation_name'];
$params['address']=$patient['orderaddress'];
$params['contact']=$patient['contact_no'];
$params['religion']=$patient['religion_name'];
$params['ward']=$patient['ward_name'];
$params['membership']=$patient['membership'];
$params['physician']=$patient['physician'];
$params['path_lic_no']=$pathologist['license_nr'];
$params['med_lic_no']=$med_tech['license_nr'];
$params['exam desired']=strtoupper($exam);

$jasper->setParams($params);

$jasper->setData($data);
$jasper->setJrxmlFilePath($jrxml);
$jasper->run();
?>