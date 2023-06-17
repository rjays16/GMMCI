<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path . 'include/inc_environment_global.php');
include_once($root_path . 'include/care_api_classes/cf3_class.php');
include_once($root_path . 'include/care_api_classes/class_person.php');
include_once($root_path . 'include/care_api_classes/Cf4_class.php');
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
$logo_path = $baseurl . 'images/phic_logo.png'; #<-- Comment this for LOCAL TESTING!
$params->put("logo_path", $logo_path);


$person = new Person;
$person_info = $person->getPersonInfo($pid);
$params->put('patient_name_last', $person_info['name_last']);
$params->put('patient_name_first', $person_info['name_first']);
$params->put('patient_name_middle', $person_info['name_middle']);
$params->put('patient_suffix', $person_info['suffix']);


//AccreditationNumber
$accre_no = $cf3->getAccreditationCode();
$params->put('accreditation', $accre_no);

//ChiefComplaint
$chief_complaint = $cf3->getChiefComplaint();
$params->put('chief_complaint', $chief_complaint);


//DateDischargeAndAdmitted
$dateDisAndAdmitted = $cf3->getDateAdmittedAndDischarge();
//var_dump($dateDisAndAdmitted['admission']);die;
$params->put('date_admitted', $dateDisAndAdmitted['admission']);
$params->put('date_discharge', $dateDisAndAdmitted['discharge']);


//PresentIllness
$illness = $cf3->getPresentIllness();
$params->put('present_illness', $illness);


//GeneralSurvey
$gen_survey = $cf3->getGeneralSurvey();
if ($gen_survey['id'] === "1") {
    $params->put('general_survey', $gen_survey['name']);
} else {
    $params->put('general_survey', $gen_survey['name'] . " - remarks: " . $gen_survey['remarks']);
}


//vital signs
$vital_signs = $cf3->getVitalSigns();
$params->put('bp', $vital_signs['systolic'] . "/" . $vital_signs['diastolic']);
$params->put('cr', $vital_signs['cr']);
$params->put('rr', $vital_signs['rr']);
$params->put('temp', $vital_signs['temp']);


//HEENT
$heent = $cf3->getHEENT();
$others_heent = $heent['remarks'] ? $heent['name'] . ', ' . $heent['remarks'] : $heent['name'];
$params->put('heent', $cf3->filterString($others_heent));


//ChestLungs
$chest = $cf3->getChestLungs();
$others_chest = $chest['remarks'] ? $chest['name'] . ', ' . $chest['remarks'] : $chest['name'];
$params->put('chest', $cf3->filterString($others_chest));


//CVS
$cvs = $cf3->getCVS();
$other_cvs = $cvs['remarks'] ? $cvs['name'] . ', ' . $cvs['remarks'] : $cvs['name'];
$params->put('cvs', $cf3->filterString($other_cvs));


//abdomen
$abdomen = $cf3->getAbdomen();
$other_abdomen = $abdomen['remarks'] ? $abdomen['name'] . ', ' . $abdomen['remarks'] : $abdomen['name'];
$params->put('abdomen', $cf3->filterString($other_abdomen));


//GUIE
$guie = $cf3->getGuie();
$other_guie = $guie['remarks'] ? $guie['name'] . ', ' . $guie['remarks'] : $guie['name'];
$params->put('guie', $cf3->filterString($other_guie));


//Skin/Extremities
$skin = $cf3->getSkinExtremities();
$other_skin = $skin['remarks'] ? $skin['name'] . ', ' . $skin['remarks'] : $skin['name'];
$params->put('skin', $cf3->filterString($other_skin));


//Neuro Examination
$neuro = $cf3->getNeuroExamination();
$other_neuro = $neuro['remarks'] ? $neuro['name'] . ', ' . $neuro['remarks'] : $neuro['name'];
$params->put('neuro', $cf3->filterString($other_neuro));


//Course in the Wards
$course = $cf3->getCourseInTheWard();
$params->put('course', $course);


//Disposition
$disposition = $cf3->getDisposition();
$params->put('disposition', $disposition['disposition']);
$params->put('result', $disposition['result']);


//Initial Prenatal Consultation and Obstetric history and PE
$obs_and_pe = $cf3->getClinicalHistoryandPE();
$params->put('date_initial_prenatal_consultation',
    $obs_and_pe['date'] ? date('m-d-Y', strtotime($obs_and_pe['date'])) : null);
$params->put('is_normal', $obs_and_pe['vs']);
$params->put('is_low_risk', $obs_and_pe['low_risk']);
$params->put('date_of_lmp', $obs_and_pe['date_of_lmp'] ? date('m-d-Y', strtotime($obs_and_pe['date_of_lmp'])) : null);
$params->put('age_of_menarche', $obs_and_pe['age_of_menarche']);
$params->put('gravida', $obs_and_pe['gravida']);
$params->put('parity', $obs_and_pe['parity']);
$params->put('t', $obs_and_pe['t']);
$params->put('a', $obs_and_pe['a']);
$params->put('p', $obs_and_pe['p']);
$params->put('l', $obs_and_pe['l']);


//Obstetric Risk
$obs = $cf3->getObstetricRisk();
$params->put('obs_a', $obs['obs_a']);
$params->put('obs_b', $obs['obs_b']);
$params->put('obs_c', $obs['obs_c']);
$params->put('obs_d', $obs['obs_d']);
$params->put('obs_e', $obs['obs_e']);
$params->put('obs_f', $obs['obs_f']);
$params->put('obs_g', $obs['obs_g']);
$params->put('obs_h', $obs['obs_h']);
$params->put('obs_i', $obs['obs_i']);


//Medical/Surgical risk Factors
$surgical = $cf3->getMedicalSurgicalRisk();
$params->put('surgical_a', $surgical['surgical_a']);
$params->put('surgical_b', $surgical['surgical_b']);
$params->put('surgical_c', $surgical['surgical_c']);
$params->put('surgical_d', $surgical['surgical_d']);
$params->put('surgical_e', $surgical['surgical_e']);
$params->put('surgical_f', $surgical['surgical_f']);
$params->put('surgical_g', $surgical['surgical_g']);
$params->put('surgical_h', $surgical['surgical_h']);
$params->put('surgical_i', $surgical['surgical_i']);
$params->put('surgical_j', $surgical['surgical_j']);
$params->put('surgical_k', $surgical['surgical_k']);


//Admitting Diagnosis
$adm = $cf3->getAdmittingDiagnosis();
$params->put('admitting_diagnosis', $adm);


//Delivery Plan
$delivery = $cf3->getDeliveryPlan();
$params->put('orientation_to_mcp', $delivery['is_benefit']);
if ($delivery['edc']) {
    $params->put('date_of_delivery', date('m-d-Y', strtotime($delivery['edc'])));
} else {
    $params->put('date_of_delivery', null);
}


//Prenatal Consultation
$prenatal = $cf3->getFollowupPrenatalConsultation();
$arr_name = array(
    '0' => '2nd',
    '1' => '3rd',
    '2' => '4th',
    '3' => '5th',
    '4' => '6th',
    '5' => '7th',
    '6' => '8th',
    '7' => '9th',
    '8' => '10th',
    '9' => '11th',
    '10' => '12th'
);
for ($i = 0; $i <= 10; $i++) {
    if ($prenatal[$arr_name[$i]]) {
        $params->put($arr_name[$i] . '_date', date('m-d-y', strtotime($prenatal[$arr_name[$i]]['date'])));
        $params->put($arr_name[$i] . '_aog', $prenatal[$arr_name[$i]]['aog']);
        $params->put($arr_name[$i] . '_weight', $prenatal[$arr_name[$i]]['weight']);
        $params->put($arr_name[$i] . '_cr', $prenatal[$arr_name[$i]]['cr']);
        $params->put($arr_name[$i] . '_rr', $prenatal[$arr_name[$i]]['rr']);
        $params->put($arr_name[$i] . '_bp', $prenatal[$arr_name[$i]]['bp']);
        $params->put($arr_name[$i] . '_temp', $prenatal[$arr_name[$i]]['temp']);
    }
}


//Date Time Delivery Outcome
$date_time = $cf3->getDateTimeDeliveryOutcome();
$params->put('date_and_time_delivery', $date_time);


//Maternal Outcome
$maternal = $cf3->getMaternalOutcome();
$params->put('obstetric_index', $maternal['maternal_outcome']);
$params->put('aog_by_lmp', $maternal['aog_by_lmp']);
$params->put('presentation', $maternal['presentation']);
$params->put('manner_of_delivery', $maternal['manner_of_delivery']);


//Birth Outcome


//var_dump($date_time);die;
$birth = $cf3->getBirthOutcome();
$has_sex = $date_time !== null ? $birth['sex'] : '';
if ($has_sex == "M") {
    $sex = "Male";
} elseif ($has_sex == "F") {
    $sex = "Female";
} else {
    $sex = "";
}
$params->put('fetal_outcome', $birth['fetal']);
$params->put('sex', $sex);
$params->put('birth_weight', $birth['weight']);
$params->put('apgar_score', $birth['apgar']);


//PostpartumDate
$postpartum = $cf3->getPostpartumDate();
$params->put('postpartum_date', $postpartum ?: null);


//Date and Time Discharge
$dis = $cf3->getDateTimeDischarge();
$params->put('date_and_time_discharge', $dis);


//Postpartum Care
$postpartum_care = $cf3->getPostpartumCare();
$partum_arr = array(
    '0' => 'perineal',
    '1' => 'maternal',
    '2' => 'breastfeeding',
    '3' => 'family_planning',
    '4' => 'family_planning_service',
    '5' => 'referred_to_partner',
    '6' => 'schedule_postpartum',
);


for ($i = 0; $i <= 6; $i++) {
    $params->put($partum_arr[$i] . '_done', $postpartum_care[$partum_arr[$i]]['is_done']);
    $params->put($partum_arr[$i] . '_remarks', $postpartum_care[$partum_arr[$i]]['remarks']);
}
/*Attending Physician*/
$doc_name = $cf4->getDoctor();
$params->put('doc_name', $doc_name);

//Current Date
$params->put('current_date', $dateDisAndAdmitted['discharge']);
