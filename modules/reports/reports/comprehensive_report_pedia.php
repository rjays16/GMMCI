<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path . 'include/inc_environment_global.php');
include_once($root_path . 'include/care_api_classes/cf3_class.php');
include_once($root_path . 'include/care_api_classes/class_person.php');
include_once($root_path . 'include/care_api_classes/Cf4_class.php');
include_once($root_path . 'modules/reports/reports/cf4.php');

$enc_no = $param['enc_no'];
$pid = $param['pid'];
$cf3 = new Cf3_class($enc_no);
$cf4 = new Cf4_class($enc_no);

$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);
#Logo of PHIC
$logo_path = $baseurl.'images/gmmci_logo.png'; #<-- Comment this for LOCAL TESTING!
$params->put("logo_path", $logo_path);


$person = new Person;
$person_info = $person->getPersonInfo($pid);
$params->put('patient_name_last', $person_info['name_last']);
$params->put('patient_name_first', $person_info['name_first']);
$params->put('patient_name_middle', $person_info['name_middle']);
$params->put('patient_suffix', $person_info['suffix']);
$params->put('sex', $person_info['sex']);
$params->put('age', $person->getFullAge($person_info['date_birth']));


//AccreditationNumber
$accre_no = $cf3->getAccreditationCode();
$params->put('accreditation', $accre_no);

//ChiefComplaint
$chief_complaint = $cf3->getChiefComplaint();
$params->put('chief_complaint', $chief_complaint);

//Admitting Diagnosis
$adm = $cf3->getAdmittingDiagnosis();
$params->put('admitting_diagnosis', $adm);

//PresentIllness
$illness = $cf3->getPresentIllness();
$params->put('present_illness', $illness);

$reviewOfSystems = $cf3->getReviewOfSystem();
$params->put('review_system', $reviewOfSystems);

$pertinent_sql = "SELECT 
                          scpmh.pertinent 
                        FROM
                          seg_cf4_past_med_history AS scpmh
                        WHERE scpmh.encounter_nr =" . $enc_no . "
                        AND scpmh.is_deleted != 1";
$pertinent_res = $db->Execute($pertinent_sql);
while ($row = $pertinent_res->FetchRow()) {
    $params->put('medical_history', " ".$row['pertinent']);
}

//vital signs
$vital_signs = $cf3->getVitalSigns();
$params->put('bp', $vital_signs['systolic'] . "/" . $vital_signs['diastolic']);
$params->put('cr', $vital_signs['cr']);
$params->put('rr', $vital_signs['rr']);
$params->put('temp', $vital_signs['temp']);

////for general survey
//$gs_sql = "SELECT
//				  scgs.gen_survey_id,
//				  scgs.remarks
//				FROM
//				  seg_cf4_general_survey AS scgs
//				WHERE scgs.encounter_nr = ".$enc_no."
//				AND scgs.is_deleted != 1";
//$gs_result = $db->Execute($gs_sql);
//foreach ($gs_result as $key){
//    if ($key['gen_survey_id'] == 1) {
//        $params->put('finding_1', "1");
//    }else{
//        $params->put('finding_2', "1");
//        $params->put('value_2_Ge', $key['remarks']);
//    }
//}


/*Attending Physician*/
//$doc_name = $cf4->getDoctor();
//$params->put('doc_name', $doc_name);

/*Admitting Physician*/
$attending_doc = $cf4->getDoctorAdmitting();
$params->put('doc_name', $attending_doc);

