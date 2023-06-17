<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR),

### The following arrays are the "role" levels each containing an access point or groups of access points

$all='_a_0_all';
$sysadmin='System_Admin';

$allow_area=array(

#'admit'=>array('_a_1_admissionwrite','_a_1_medocswrite'),
'register'=>array('_a_1_admissionwrite','_a_1_medocswrite','_a_1_opdpatientmanage','_a_1_erpatientmanage','_a_1_ipdpatientmanage','_a_1_medocspatientmanage'),

'admit'=>array('_a_1_admissionwrite','_a_1_medocswrite','_a_1_opdpatientadmit','_a_1_erpatientadmit','_a_1_ipdpatientadmit'),

'address'=>array('_a_1_address_manager'),

'occupation'=>array('_a_1_occupation_manager'),

'icd10'=>array('_a_1_icd10_manager'),

'benefit'=>array('_a_1_benefits_manager'),

'phicrvu'=>array('_a_1_rvuphic_manager','_a_1_icpm_manager'),

'hospital_mgr'=>array('_a_1_hospital_manager'),

'canserved'=>array('_a_1_served'),

'cafe'=>array('_a_1_newsallwrite', '_a_1_newscafewrite'),

'medocs'=>array('_a_1_medocswrite', '_a_1_medocsmedrecmedical'),

'cancel'=>array('_a_1_ipdcancel','_a_1_opdcancel','_a_1_ercancel'),

'discharge_cancel'=>array('_a_1_ipddischargecancel','_a_1_opddischargecancel','_a_1_erdischargecancel'),

'phonedir'=>array('$sysadmin', '_a_1_teldirwrite'),

'doctors'=>array('_a_1_opdoctorallwrite', '_a_1_doctorsdutyplanwrite'),

'wards'=>array('_a_1_doctorsdutyplanwrite', '_a_1_opdoctorallwrite', '_a_1_nursingstationallwrite',  $all, $sysadmin),

'wardmanage'=>array('_a_1_nursingstationallwardmanagement'), //added by pol

'op_room'=>array('_a_1_opdoctorallwrite', '_a_1_opnursedutyplanwrite', '_a_2_opnurseallwrite'),

'tech'=>array('_a_1_techreception'),

'lab_r'=>array('_a_1_labresultswrite', '_a_2_labresultsread'),

'lab_w'=>array('_a_1_labresultswrite'),

'lab_request'=>array('a_1_labcreaterequest'),

'blood_request'=>array('_a_1_bloodcreaterequest'),

'radio_request'=>array('_a_1_radiocreaterequest'),

'pharma_request'=>array('_a_1_pharmaallareas'),

'or_request'=>array('_a_1_opcreaterequest','_a_1_opORmain'),

'charges'=>('_a_1_nursingcreaterequest'),

'radio'=>array('_a_1_radiowrite', '_a_1_opdoctorallwrite', '_a_2_opnurseallwrite'),

'pharma_db'=>array('_a_1_pharmadbadmin'),

'pharma_receive'=>array('_a_1_pharmadbadmin', '_a_2_pharmareception'),

'pharma'=>array('_a_1_pharmadbadmin', '_a_2_pharmareception',  '_a_3_pharmaorder'),

'depot_db'=>array('_a_1_meddepotdbadmin'),

'depot_receive'=>array('_a_1_meddepotdbadmin', '_a_2_meddepotreception'),

'depot'=>array('_a_1_meddepotdbadmin', '_a_2_meddepotreception', '_a_3_meddepotorder'),

'report'=>array('_a_1_report', '_a_1_phsreports', '_a_1_ipdreports','_a_1_erreports','_a_1_opdreports'),

#'edp'=>array('no_allow_type_all',),
'edp'=>array('_a_1_sysad_access'),

'news'=>array('_a_1_newsallwrite'),

'cafenews'=>array('_a_1_newsallwrite', '_a_2_newscafewrite'),

'op_docs'=>array('_a_1_opdoctorallwrite'),

'duty_op'=>array('_a_1_opnursedutyplanwrite'),

'fotolab'=>array('_a_1_photowrite'),

'test_diagnose'=>array('_a_1_diagnosticsresultwrite', '_a_1_labresultswrite'),

'test_receive'=>array('_a_1_diagnosticsresultwrite', '_a_1_labresultswrite', '_a_2_diagnosticsreceptionwrite'),

'test_order'=>array('_a_1_diagnosticsresultwrite', '_a_1_labresultswrite', '_a_2_diagnosticsreceptionwrite',   '_a_3_diagnosticsrequest'),

'billing' => array('_a_1_billmanage', '_a_2_billviewsave', '_a_1_billpackages', '_a_1_billtransmittal', '_a_1_billmiscellaneousmanage', '_a_1_billaddclaim', '_a_1_billeditclaim', '_a_1_billdeleteclaim'),

'industrial_clinic' => array('_a_1_ictransadd', '_a_1_ictransmanage', '_a_2_ictransdelete', '_a_2_ictransedit'),

'dialysis' => array('_a_1_dialysiscreaterequest', '_a_1_dialysislistrequest', '_a_1_dialysisreports','_a_1_dialysismachineread', '_a_1_dialysispackagemanager', '_a_1_dialysismachinemanager','_a_1_dialysisbilling', '_a_1_labadmin')
);

?>