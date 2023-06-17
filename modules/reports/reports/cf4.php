<?php

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once './roots.php';
require_once $root_path . 'include/inc_environment_global.php';
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
include_once $root_path . 'include/care_api_classes/class_globalconfig.php';
include_once $root_path . 'include/care_api_classes/class_person.php';
include_once $root_path . 'include/care_api_classes/Cf4_class.php';
include_once $root_path . 'include/care_api_classes/billing/class_billing_new.php';

// Mod by Jeff 03-14-18 for enhancement of form.
include 'parameters.php';

$enc_no = $param['enc_no'];
$pid = $param['pid'];
$addr_arr = array('building_name', 'city', 'province');
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

$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
    $row['hosp_name'] = strtoupper($row['hosp_name']);
    $row['addr_no_street'] = $row['addr_no_street'];
    $row['hosp_addr1'] = $row['hosp_addr1'];
}
//for address
$address = explode(",", $row['hosp_addr1']);
for ($i = 0; $i < count($address); $i++) {
    $params->put($addr_arr[$i], $address[$i]);
}
$params->put('hci_name', $row['hosp_name']);
$params->put('province', "DAVAO DEL SUR");
$params->put('zipcode', $row['zip_code']);
$params->put('date_signed', date('m-d-Y'));

//accr code
$accr_sql = "SELECT
                  sec.value
                FROM
                  seg_eclaims_config AS sec
                WHERE sec.id = 12 ";
$accr_res = $db->Execute($accr_sql);
while ($row = $accr_res->FetchRow()) {
    $params->put('pan', $row['value']);
}

$person = new Person;
$person_info = $person->getPersonInfo($pid);
//     var_dump($person_info['age']);die;
$params->put('patient_name_last', $person_info['name_last']);
$params->put('patient_name_first', $person_info['name_first']);
$params->put('patient_name_middle', $person_info['name_middle']);
$params->put('patient_name_suffix', $person_info['suffix']);
$params->put('age', $person->getFullAge($person_info['date_birth']));
$params->put('sex', $person_info['sex']);
$pin_sql = "SELECT
                  seim.patient_pin,
                  seim.insurance_nr,
                  seim.relation
                FROM
                  seg_encounter_insurance_memberinfo AS seim
                WHERE seim.encounter_nr =" . $enc_no . "";
$pin_res = $db->Execute($pin_sql);
while ($pin = $pin_res->FetchRow()) {
    if ($pin['relation'] == "M" || $pin['relation'] == "m") {
        $params->put('pin', $pin['insurance_nr']);
    } else {
        $params->put('pin', $pin['patient_pin']);
    }
}

//for chief complaint
$cc_sql = "SELECT
                  sccd.chief_complaint
                FROM
                  seg_cf4_chiefcomplaint_data AS sccd
                WHERE sccd.encounter_nr =" . $enc_no . "";
$cc_res = $db->Execute($cc_sql);
while ($row = $cc_res->FetchRow()) {
    $params->put('chief_complaint', $row['chief_complaint']);
}

//for case rate
$case_sql = "SELECT
              sbc.`bill_nr`,
              sbc.`package_id`,
              sbc.`rate_type`,
              scrp.`case_type`
            FROM
              seg_billing_encounter AS sbe
              INNER JOIN seg_billing_caserate AS sbc
                ON sbe.bill_nr = sbc.bill_nr
              INNER JOIN seg_case_rate_packages AS scrp
                ON sbc.package_id = scrp.`code`
            WHERE sbe.`encounter_nr` = $enc_no
            AND sbe.`is_deleted` IS NULL
            GROUP BY sbc.`package_id` ";
$case_res = $db->Execute($case_sql);
while ($row = $case_res->FetchRow()) {
    $code = $row['package_id'];
    $rate_type = $row['rate_type'];
    $case_type = $row['case_type'];
    if ($rate_type == "1") {
        $params->put('first_case', $row['package_id']);
    } else {
        $params->put('second_case', $row['package_id']);
    }
}
//    if ($case_type == "p"){
//        $dsc_sql = "SELECT
//                      smod.`description`
//                    FROM
//                      seg_misc_ops AS smo
//                      INNER JOIN
//                        (SELECT
//                          `refno`,
//                          MAX(modify_dt) AS MaxDate,
//                          encounter_nr
//                        FROM
//                          seg_misc_ops
//                        GROUP BY refno) AS smoRef
//                        ON smo.`modify_dt` = smoRef.MaxDate
//                        AND smo.`refno` = smoRef.refno
//                      INNER JOIN seg_misc_ops_details AS smod
//                        ON smo.`refno` = smod.`refno`
//                    WHERE smo.`encounter_nr` = $enc_no
//                    AND smod.ops_code = $code
//                    ORDER BY smo.`modify_dt` DESC
//                    LIMIT 2 ";
//        $dsc_res = $db->Execute($dsc_sql);
//        if ($dsc_res)
//            while ($row = $dsc_res->FetchRow()){
//                if ($desc === null){
//                    $desc = $code.' - '.$row['description'];
//                    $params->put('discharge_diagnosis',$code.' - '.$row['description']."<br>");
//                }else{
//                    if ($rate_type != 1){
//                        $params->put('discharge_diagnosis',$desc."<br>".$code.' - '.$row['description']."<br>");
//                    }else{
//                        $params->put('discharge_diagnosis',$code.' - '.$row['description']."<br>".$desc);
//                    }
//                }
//            }
//    }else{
//        $dsc_sql = "SELECT
//                      sed.`description`,
//                      sed.type_nr
//                    FROM
//                      seg_encounter_diagnosis AS sed
//                    WHERE sed.`encounter_nr` = $enc_no
//                      AND sed.`code` = '$code'
//                      AND sed.is_deleted = 0";
//
//        $dsc_res = $db->Execute($dsc_sql);
//        while ($row = $dsc_res->FetchRow()){
//            if ($desc == null){
//                $desc = $code.' - '.$row['description'];
//                $params->put('discharge_diagnosis', $code.' - '.$row['description']."<br>");
//            }else{
//                if($rate_type != 1){
//                    $params->put('discharge_diagnosis',$desc."<br>".$code.' - '.$row['description']."<br>");
//                }else{
//                    $params->put('discharge_diagnosis',$code.' - '.$row['description']."<br>".$desc);
//                }
//            }
//        }
//    }
//}

//for Discharge Diagnosis
$model = new Billing();
$final = $model->getFinalDiagnosis($enc_no);
$other = $model->getOtherDiagnosis($enc_no, $final['code']);
$misc = $cf4->getMisc();
while ($row = $other->FetchRow()) {
    $other_diag .= $row['description'] . '<br>';
}
$other_diag .= $misc;
$params->put('discharge_diagnosis', strtoupper($final['description']) . '<br>' . strtoupper($other_diag));

//for admitting and discharge
$adm_sql = "SELECT
                  ce.er_opd_diagnosis AS admitting_diagnosis
                FROM
                  care_encounter AS ce
                WHERE ce.encounter_nr = " . $enc_no . "";
$adm_res = $db->Execute($adm_sql);
while ($row = $adm_res->FetchRow()) {
    $params->put('admitting_diagnosis', strtoupper($row['admitting_diagnosis']));
}

//admitted and discharge date
$dt_sql = "SELECT
              ce.admission_dt,
              ce.encounter_date,
              ce.discharge_date,
              ce.discharge_time
            FROM
              care_encounter AS ce
            WHERE ce.encounter_nr = " . $enc_no . "";
$dt_res = $db->Execute($dt_sql);
while ($row = $dt_res->FetchRow()) {
    $params->put('admission_date', $row['admission_dt'] != null ? date('m-d-Y h:i:a', strtotime($row['admission_dt'])) : date('m-d-Y h:i:a', strtotime($row['encounter_date'])));
    $params->put('date_discharged', $row['discharge_date'] && $row['discharge_time'] != null ? date('m-d-Y', strtotime($row['discharge_date'])) . " " . date('h:i:a', strtotime($row['discharge_time'])) : " ");
}

//for Present Illness
$present_sql = "SELECT
                      sccr.present_illness
                    FROM
                      seg_cf4_clinical_record AS sccr
                    WHERE sccr.encounter_nr = " . $enc_no . "
                    AND sccr.is_deleted != 1";
$present_res = $db->Execute($present_sql);
while ($row = $present_res->FetchRow()) {
    $params->put('present_illness', " " . $row['present_illness']);
}

//for pertinent history
$pertinent_sql = "SELECT
                          scpmh.pertinent
                        FROM
                          seg_cf4_past_med_history AS scpmh
                        WHERE scpmh.encounter_nr =" . $enc_no . "
                        AND scpmh.is_deleted != 1";
$pertinent_res = $db->Execute($pertinent_sql);
while ($row = $pertinent_res->FetchRow()) {
    $params->put('medical_history', " " . $row['pertinent']);
}

//for OB
$ob_sql = "SELECT
                  scoh.gravida,
                  scoh.parity,
                  scoh.term_births,
                  scoh.preterm_births,
                  scoh.abortion,
                  scoh.living_children
                FROM
                  seg_cf4_obstetric_history AS scoh
                  WHERE scoh.encounter_nr = " . $enc_no . "
                    ";
$ob_res = $db->Execute($ob_sql);
//    var_dump($ob_sql);die;
while ($row = $ob_res->FetchRow()) {
    $params->put('date_gravity', $row['gravida']);
    $params->put('date_parity', $row['parity']);
    $params->put('T', $row['term_births']);
    $params->put('P', $row['preterm_births']);
    $params->put('A', $row['abortion']);
    $params->put('L', $row['living_children']);
}

$menstrual_sql = "SELECT
                          scmh.date_of_lmp,
                          scmh.is_applicable
                        FROM
                          seg_cf4_menstrual_history AS scmh
                          WHERE scmh.encounter_nr = " . $enc_no . "
                          ";
$menstrual_res = $db->Execute($menstrual_sql);
while ($row = $menstrual_res->FetchRow()) {
    $params->put('last_period_menstrual', $row['date_of_lmp']);
    $params->put('is_applicable', $row['is_applicable']);
}

//for ss
$ss_sql = "SELECT
				  scpss.sign_symptoms,
				  scpss.pains,
				  scpss.others
				FROM
				  seg_cf4_pertinent_sign_symptoms AS scpss
				WHERE scpss.encounter_nr = " . $enc_no . "
				AND scpss.is_deleted != 1";
$ss_result = $db->Execute($ss_sql);
//var_dump($ss_sql);die;
while ($row = $ss_result->FetchRow()) {
    if ($row['sign_symptoms'] == 1) {
        $params->put('sign_and_symp_1', "1");
    } else if ($row['sign_symptoms'] == 2) {
        $params->put('sign_and_symp_2', "1");
    } else if ($row['sign_symptoms'] == 3) {
        $params->put('sign_and_symp_3', "1");
    } else if ($row['sign_symptoms'] == 4) {
        $params->put('sign_and_symp_4', "1");
    } else if ($row['sign_symptoms'] == 5) {
        $params->put('sign_and_symp_5', "1");
    } else if ($row['sign_symptoms'] == 6) {
        $params->put('sign_and_symp_6', "1");
    } else if ($row['sign_symptoms'] == 7) {
        $params->put('sign_and_symp_8', "1");
    } else if ($row['sign_symptoms'] == 8) {
        $params->put('sign_and_symp_7', "1");
    } else if ($row['sign_symptoms'] == 9) {
        $params->put('sign_and_symp_9', "1");
    } else if ($row['sign_symptoms'] == 10) {
        $params->put('sign_and_symp_10', "1");
    } else if ($row['sign_symptoms'] == 11) {
        $params->put('sign_and_symp_11', "1");
    } else if ($row['sign_symptoms'] == 12) {
        $params->put('sign_and_symp_12', "1");
    } else if ($row['sign_symptoms'] == 13) {
        $params->put('sign_and_symp_13', "1");
    } else if ($row['sign_symptoms'] == 14) {
        $params->put('sign_and_symp_14', "1");
    } else if ($row['sign_symptoms'] == 15) {
        $params->put('sign_and_symp_15', "1");
    } else if ($row['sign_symptoms'] == 17) {
        $params->put('sign_and_symp_17', "1");
    } else if ($row['sign_symptoms'] == 18) {
        $params->put('sign_and_symp_18', "1");
    } else if ($row['sign_symptoms'] == 19) {
        $params->put('sign_and_symp_19', "1");
    } else if ($row['sign_symptoms'] == 20) {
        $params->put('sign_and_symp_20', "1");
    } else if ($row['sign_symptoms'] == 21) {
        $params->put('sign_and_symp_21', "1");
    } else if ($row['sign_symptoms'] == 22) {
        $params->put('sign_and_symp_22', "1");
    } else if ($row['sign_symptoms'] == 23) {
        $params->put('sign_and_symp_23', "1");
    } else if ($row['sign_symptoms'] == 25) {
        $params->put('sign_and_symp_24', "1");
    } else if ($row['sign_symptoms'] == 26) {
        $params->put('sign_and_symp_25', "1");
    } else if ($row['sign_symptoms'] == 27) {
        $params->put('sign_and_symp_26', "1");
    } else if ($row['sign_symptoms'] == 28) {
        $params->put('sign_and_symp_28', "1");
    } else if ($row['sign_symptoms'] == 30) {
        $params->put('sign_and_symp_31', "1");
    } else if ($row['sign_symptoms'] == 29) {
        $params->put('sign_and_symp_30', "1");
    } else if ($row['sign_symptoms'] == 32) {
        $params->put('sign_and_symp_32', "1");
    } else if ($row['sign_symptoms'] == 33) {
        $params->put('sign_and_symp_29', "1");
    } else if ($row['sign_symptoms'] == 34) {
        $params->put('sign_and_symp_33', "1");
    } else if ($row['sign_symptoms'] == 35) {
        $params->put('sign_and_symp_34', "1");
    } else if ($row['sign_symptoms'] == 36) {
        $params->put('sign_and_symp_35', "1");
    } else if ($row['sign_symptoms'] == 37) {
        $params->put('sign_and_symp_16', "1");
    } else if ($row['sign_symptoms'] == 38) {
        $params->put('opt_2', "1");
        $params->put('opt_2_values', $row['pains']);
    } else {
        $params->put('opt_3', "1");
        $params->put('opt_3_values', $row['others']);
    }
}
////cf4 vital signs
$cvs_sql = "SELECT
                  scvs.systolic,
                  scvs.diastolic,
				  scvs.rr,
				  scvs.temperature AS temp,
				  scvs.cr,
                  scvs.height,
                  scvs.weight
				FROM
				  seg_cf4_vital_signs AS scvs
				   WHERE scvs.encounter_nr = " . $enc_no . "
				   AND scvs.is_deleted != 1";
$result = $db->Execute($cvs_sql);
while ($row = $result->FetchRow()) {
    if ($row) {
        $params->put('vital_bp', $row['systolic'] . "/" . $row['diastolic'] . " mmHg");
        $params->put('vital_hr', $row['cr'] . " /m");
        $params->put('vital_temp', $row['temp']);
        $params->put('vital_rr', $row['rr'] . " /m");
        $params->put('height', $row['height']);
        $params->put('weight', $row['weight']);

    }
}
//for general surver
$gs_sql = "SELECT
				  scgs.gen_survey_id,
				  scgs.remarks
				FROM
				  seg_cf4_general_survey AS scgs
				WHERE scgs.encounter_nr = " . $enc_no . "
				AND scgs.is_deleted != 1";
$gs_result = $db->Execute($gs_sql);
foreach ($gs_result as $key) {
    if ($key['gen_survey_id'] == 1) {
        $params->put('finding_1', "1");
    } else {
        $params->put('finding_2', "1");
        $params->put('value_2_Ge', $key['remarks']);
    }
}
//for heent
$h_sql = "SELECT
				  sch.heent_id,
				  sch.remarks
				FROM
				  seg_cf4_heent AS sch
				WHERE sch.encounter_nr =" . $enc_no . "
				AND sch.is_deleted != 1";
$h_result = $db->Execute($h_sql);
foreach ($h_result as $key) {
    if ($key['heent_id'] == 11) {
        $params->put('finding_3', "1");
    } else if ($key['heent_id'] == 12) {
        $params->put('finding_4', "1");
    } else if ($key['heent_id'] == 13) {
        $params->put('finding_5', "1");
    } else if ($key['heent_id'] == 14) {
        $params->put('finding_6', "1");
    } else if ($key['heent_id'] == 15) {
        $params->put('finding_7', "1");
    } else if ($key['heent_id'] == 16) {
        $params->put('finding_8', "1");
    } else if ($key['heent_id'] == 17) {
        $params->put('finding_9', "1");
    } else if ($key['heent_id'] == 18) {
        $params->put('finding_10', "1");
    } else {
        $params->put('finding_others_HE', "1");
        $params->put('value_others_HE', $key['remarks']);
    }
}

//for Chest/Lungs
$c_sql = "SELECT
				  scc.chest_id,
				  scc.remarks
				FROM
				  seg_cf4_chest AS scc
				WHERE scc.encounter_nr =" . $enc_no . "
				AND scc.is_deleted != 1";
$c_result = $db->Execute($c_sql);
foreach ($c_result as $key) {
    if ($key['chest_id'] == 4) {
        $params->put('finding_51', "1");
    } else if ($key['chest_id'] == 5) {
        $params->put('finding_52', "1");
    } else if ($key['chest_id'] == 6) {
        $params->put('finding_53', "1");
    } else if ($key['chest_id'] == 7) {
        $params->put('finding_54', "1");
    } else if ($key['chest_id'] == 8) {
        $params->put('finding_55', "1");
    } else if ($key['chest_id'] == 10) {
        $params->put('finding_48', "1");
    } else if ($key['chest_id'] == 3) {
        $params->put('finding_50', "1");
    } else {
        $params->put('finding_others_Ch', "1");
        $params->put('value_others_Ch', $key['remarks']);
    }
}

//for cvs
$heart_sql = "SELECT
					  sch.heart_id,
					  sch.remarks
					FROM
					  seg_cf4_heart AS sch
					WHERE sch.encounter_nr =" . $enc_no . "
					AND sch.is_deleted != 1";
$heart_result = $db->Execute($heart_sql);
foreach ($heart_result as $key) {
    if ($key['heart_id'] == 3) {
        $params->put('finding_64', "1");
    } else if ($key['heart_id'] == 4) {
        $params->put('finding_65', "1");
    } else if ($key['heart_id'] == 5) {
        $params->put('finding_66', "1");
    } else if ($key['heart_id'] == 6) {
        $params->put('finding_67', "1");
    } else if ($key['heart_id'] == 7) {
        $params->put('finding_68', "1");
    } else if ($key['heart_id'] == 8) {
        $params->put('finding_69', "1");
    } else if ($key['heart_id'] == 9) {
        $params->put('finding_70', "1");
    } else {
        $params->put('finding_others_CV', "1");
        $params->put('value_others_CV', $key['remarks']);
    }
}

//for abdomen
$a_sql = "SELECT
				  sca.abdomen_id,
				  sca.remarks
				FROM
				  seg_cf4_abdomen AS sca
				WHERE sca.encounter_nr = " . $enc_no . "
				AND sca.is_deleted != 1";
$a_result = $db->Execute($a_sql);
foreach ($a_result as $key) {
    if ($key['abdomen_id'] == 7) {
        $params->put('finding_87', "1");
    } else if ($key['abdomen_id'] == 8) {
        $params->put('finding_88', "1");
    } else if ($key['abdomen_id'] == 9) {
        $params->put('finding_89', "1");
    } else if ($key['abdomen_id'] == 10) {
        $params->put('finding_78', "1");
    } else if ($key['abdomen_id'] == 11) {
        $params->put('finding_79', "1");
    } else if ($key['abdomen_id'] == 12) {
        $params->put('finding_80', "1");
    } else if ($key['abdomen_id'] == 13) {
        $params->put('finding_81', "1");
    } else {
        $params->put('finding_others_AB', "1");
        $params->put('value_others_AB', $key['remarks']);
    }
}

////for guie
$gu_sql = "SELECT
				  scg.guie_id,
				  scg.remarks
				FROM
				  seg_cf4_guie AS scg
				WHERE scg.encounter_nr =" . $enc_no . "
				AND scg.is_deleted != 1";
$gu_result = $db->Execute($gu_sql);
//    var_dump($gu_result);die;
while ($row = $gu_result->FetchRow()) {
    if ($row['guie_id'] == 1) {
        $params->put('finding_114', "1");
    } else if ($row['guie_id'] == 2) {
        $params->put('finding_115', "1");
    } else if ($row['guie_id'] == 3) {
        $params->put('finding_116', "1");
    } else if ($row['guie_id'] == 4) {
        $params->put('finding_117', "1");
    } else {
        $params->put('finding_others_GU', "1");
        $params->put('value_others_GU', $row['remarks']);
    }
}

//for skin
$s_sql = "SELECT
				  scs.skin_id,
				  scs.remarks
				FROM
				  seg_cf4_skin AS scs
				WHERE scs.encounter_nr = " . $enc_no . "
				AND scs.is_deleted != 1";
$s_result = $db->Execute($s_sql);
foreach ($s_result as $key) {
    if ($key['skin_id'] == 1) {
        $params->put('finding_32', "1");
    } else if ($key['skin_id'] == 2) {
        $params->put('finding_34', "1");
    } else if ($key['skin_id'] == 3) {
        $params->put('finding_35', "1");
    } else if ($key['skin_id'] == 4) {
        $params->put('finding_36', "1");
    } else if ($key['skin_id'] == 5) {
        $params->put('finding_37', "1");
    } else if ($key['skin_id'] == 6) {
        $params->put('finding_38', "1");
    } else if ($key['skin_id'] == 7) {
        $params->put('finding_39', "1");
    } else if ($key['skin_id'] == 8) {
        $params->put('finding_40', "1");
    } else if ($key['skin_id'] == 9) {
        $params->put('finding_41', "1");
    } else if ($key['skin_id'] == 10) {
        $params->put('finding_33', "1");
    } else {
        $params->put('finding_others_SK', "1");
        $params->put('value_others_SK', $key['remarks']);
    }
}

//for neuro
$n_sql = "SELECT
				  scn.neuro_id,
				  scn.remarks
				FROM
				  seg_cf4_neuro AS scn
				WHERE scn.encounter_nr =" . $enc_no . "
				AND scn.is_deleted != 1";
$n_result = $db->Execute($n_sql);
foreach ($n_result as $key) {
    if ($key['neuro_id'] == 6) {
        $params->put('finding_101', "1");
    } else if ($key['neuro_id'] == 7) {
        $params->put('finding_102', "1");
    } else if ($key['neuro_id'] == 8) {
        $params->put('finding_103', "1");
    } else if ($key['neuro_id'] == 9) {
        $params->put('finding_104', "1");
    } else if ($key['neuro_id'] == 10) {
        $params->put('finding_93', "1");
    } else if ($key['neuro_id'] == 11) {
        $params->put('finding_94', "1");
    } else if ($key['neuro_id'] == 12) {
        $params->put('finding_95', "1");
    } else if ($key['neuro_id'] == 13) {
        $params->put('finding_96', "1");
    } else {
        $params->put('finding_others_NE', "1");
        $params->put('value_others_NE', $key['remarks']);
    }
}
/*Attending Physician*/
$doc_name = $cf4->getDoctor();
$params->put('doc_name', $doc_name);
$disp = $cf4->getDisposition();
$result = $cf4->getResult();
$params->put('disposition', $disp);
$params->put('result', $result);

if ($cf4->doctorsAction() == null) {
    $data = array_merge($cf4->forHeader(), $cf4->medicine());
} else if ($cf4->medicine() == null) {
    $data = array_merge($cf4->doctorsAction(), $cf4->forHeader());
} else {
    $data = array_merge($cf4->doctorsAction(), $cf4->forHeader(), $cf4->medicine());
}

//        echo "<pre>";
//        var_dump($data);die();
