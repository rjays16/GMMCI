<?php
require_once('./roots.php');
require_once($root_path . 'include/inc_environment_global.php');


class cf3_class
{
    public $enc_no;
    public $db;

    public function __construct($encounter)
    {
        $this->enc_no = $encounter;
    }

    public function getAccreditationCode()
    {
        global $db;
        $accr = null;
        $accr_sql = "SELECT 
                  sec.value 
                FROM
                  seg_eclaims_config AS sec 
                WHERE sec.id = 12 ";
        $accr_res = $db->Execute($accr_sql);
        while ($row = $accr_res->FetchRow()) {
            $accr = $row['value'];
        }
        return $accr;
    }

    public function getChiefComplaint()
    {
        global $db;
        $param = null;
        $cc_sql = "SELECT
                  sccd.chief_complaint
                FROM
                  seg_cf4_chiefcomplaint_data AS sccd
                WHERE sccd.encounter_nr =" . $this->enc_no . "";
        $cc_res = $db->Execute($cc_sql);
        while ($row = $cc_res->FetchRow()) {
            $param = $row['chief_complaint'];
        }
        return $param;
    }

    public function getDateAdmittedAndDischarge()
    {
        global $db;
        $admission_date = null;
        $discharge_date = null;
        $dt_sql = "SELECT
              ce.admission_dt,
              ce.encounter_date,
              ce.discharge_date,
              ce.discharge_time
            FROM
              care_encounter AS ce
            WHERE ce.encounter_nr = " . $this->enc_no . "";

        $dt_res = $db->Execute($dt_sql);
        while ($row = $dt_res->FetchRow()) {
            $admission_date = $row['admission_dt'] !== null ? date('m-d-Y h:i:a',
                strtotime($row['admission_dt'])) : date('m-d-Y h:i:a', $row['encounter_dt']);
            $discharge_date = $row['discharge_date'] && $row['discharge_time'] !== null ? date('m-d-Y',
                    strtotime($row['discharge_date'])) . " " . date('h:i:a', strtotime($row['discharge_time'])) : " ";
        }
        return array(
            'admission' => $admission_date,
            'discharge' => $discharge_date
        );
    }

    public function getPresentIllness()
    {
        global $db;
        $param = null;
        $present_sql = "SELECT 
                      sccr.present_illness 
                    FROM
                      seg_cf4_clinical_record AS sccr
                    WHERE sccr.encounter_nr = " . $this->enc_no . "
                    AND sccr.is_deleted != 1";
        $present_res = $db->Execute($present_sql);
        while ($row = $present_res->FetchRow()) {
            $param = $row['present_illness'];
        }
        return $param;
    }

    public function getGeneralSurvey()
    {
        global $db;
        $name = null;
        $id = null;
        $remarks = null;
        $gen_sql = "
        SELECT
        sclgs.name,
        scgs.`gen_survey_id`,
        scgs.remarks
        FROM
        seg_cf4_general_survey AS scgs
        INNER JOIN seg_cf4_lib_gen_survey AS sclgs
        ON scgs.`gen_survey_id` = sclgs.`id`
        WHERE scgs.encounter_nr = " . $this->enc_no . "
        AND scgs.is_deleted != 1";
        $gen_res = $db->Execute($gen_sql);
        while ($row = $gen_res->FetchRow()) {
            $name = $row['name'];
            $id = $row['gen_survey_id'];
            $remarks = $row['remarks'];
        }
        return array(
            'name' => $name,
            'id' => $id,
            'remarks' => $remarks
        );
    }

    public function getVitalSigns()
    {
        global $db;
        $systolic = null;
        $diastolic = null;
        $rr = null;
        $temp = null;
        $cr = null;
        $cvs_sql = "SELECT
                  scvs.systolic,
                  scvs.diastolic,
				  scvs.rr,
				  scvs.temperature AS temp,
				  scvs.cr
				FROM
				  seg_cf4_vital_signs AS scvs
				   WHERE scvs.encounter_nr = " . $this->enc_no . "
				   AND scvs.is_deleted != 1";
        $result = $db->Execute($cvs_sql);
        while ($row = $result->FetchRow()) {
            if ($row) {
                $systolic = $row['systolic'];
                $diastolic = $row['diastolic'];
                $rr = $row['rr'];
                $temp = $row['temp'];
                $cr = $row['cr'];
            }
        }
        return array(
            'systolic' => $systolic,
            'diastolic' => $diastolic,
            'rr' => $rr,
            'temp' => $temp,
            'cr' => $cr
        );
    }

    public function getHEENT()
    {
        global $db;
        $name = null;
        $remarks = null;
        $h_sql = "SELECT 
                  sch.heent_id,
                  sclh.name,
                  sch.remarks 
                FROM
                  seg_cf4_heent AS sch 
                  INNER JOIN seg_cf4_lib_heent AS sclh
                  ON sch.`heent_id` = sclh.`id`
                WHERE sch.encounter_nr = " . $this->enc_no . " 
                  AND sch.is_deleted != 1";
        $h_result = $db->Execute($h_sql);
        foreach ($h_result as $key) {
            if ($name) {
                if($key['name'] !== 'Others'){
                    $name = $name . ", " . $key['name'];
                }
            } else {
                if($key['name'] !== 'Others') {
                    $name = $key['name'];
                }
            }
            $remarks = $key['remarks'];
        }

        return array(
            'name' => $name,
            'remarks' => $remarks,
        );
    }

    public function getChestLungs()
    {
        global $db;
        $name = null;
        $remarks = null;
        $c_sql = "SELECT 
                      scc.chest_id,
                      sclc.`name`,
                      scc.remarks 
                    FROM
                      seg_cf4_chest AS scc 
                      INNER JOIN seg_cf4_lib_chest AS sclc
                      ON scc.`chest_id` = sclc.`id`
                    WHERE scc.encounter_nr = " . $this->enc_no . " 
                      AND scc.is_deleted != 1";
        $c_result = $db->Execute($c_sql);
        foreach ($c_result as $key) {
            if ($name) {
                if($key['name'] !== 'Others') {
                    $name = $name . ", " . $key['name'];
                }
            } else {
                if($key['name'] !== 'Others') {
                    $name = $key['name'];
                }
            }
            $remarks = $key['remarks'];
        }

        return array(
            'name' => $name,
            'remarks' => $remarks,
        );
    }

    public function getCVS()
    {
        global $db;
        $name = null;
        $remarks = null;
        $heart_sql = "SELECT 
                          sch.heart_id,
                          sclh.name,
                          sch.remarks 
                        FROM
                          seg_cf4_heart AS sch 
                          INNER JOIN seg_cf4_lib_heart AS sclh
                          ON sch.`heart_id` = sclh.id
                        WHERE sch.encounter_nr = " . $this->enc_no . " 
                          AND sch.is_deleted != 1 ";
        $heart_result = $db->Execute($heart_sql);
        foreach ($heart_result as $key) {
            if ($name) {
                if($key['name'] !== 'Others') {
                    $name = $name . ", " . $key['name'];
                }
            } else {
                if($key['name'] !== 'Others') {
                    $name = $key['name'];
                }
            }
            $remarks = $key['remarks'];
        }
        return array(
            'name' => $name,
            'remarks' => $remarks,
        );
    }

    public function getAbdomen()
    {
        global $db;
        $name = null;
        $remarks = null;
        $a_sql = "SELECT 
                      sca.abdomen_id,
                      scla.`name`,
                      sca.remarks 
                    FROM
                      seg_cf4_abdomen AS sca 
                      INNER JOIN seg_cf4_lib_abdomen AS scla
                      ON sca.`abdomen_id` = scla.`id`
                    WHERE sca.encounter_nr = " . $this->enc_no . " 
                      AND sca.is_deleted != 1 ";
        $a_result = $db->Execute($a_sql);
        foreach ($a_result as $key) {
            if ($name) {
                if($key['name'] !== 'Others') {
                    $name = $name . ", " . $key['name'];
                }
            } else {
                if($key['name'] !== 'Others') {
                    $name = $key['name'];
                }
            }
            $remarks = $key['remarks'];
        }
        return array(
            'name' => $name,
            'remarks' => $remarks,
        );
    }

    public function getGuie()
    {
        global $db;
        $name = null;
        $remarks = null;
        $gu_sql = "SELECT 
                      scg.guie_id,
                      sclg.`name`,
                      scg.remarks 
                    FROM
                      seg_cf4_guie AS scg 
                      INNER JOIN seg_cf4_lib_guie AS sclg
                      ON scg.`guie_id` = sclg.`id`
                    WHERE scg.encounter_nr = " . $this->enc_no . " 
                      AND scg.is_deleted != 1 ";
        $gu_result = $db->Execute($gu_sql);
        foreach ($gu_result as $key) {
            if ($name) {
                if($key['name'] !== 'Others') {
                    $name = $name . ", " . $key['name'];
                }
            } else {
                if($key['name'] !== 'Others') {
                    $name = $key['name'];
                }
            }
            $remarks = $key['remarks'];
        }
        return array(
            'name' => $name,
            'remarks' => $remarks,
        );
    }

    public function getSkinExtremities()
    {
        global $db;
        $name = null;
        $remarks = null;
        $s_sql = "SELECT 
                      scs.skin_id,
                      scls.`name`,
                      scs.remarks 
                    FROM
                      seg_cf4_skin AS scs 
                      INNER JOIN seg_cf4_lib_skin AS scls
                      ON scs.`skin_id` = scls.`id`
                    WHERE scs.encounter_nr = " . $this->enc_no . " 
                      AND scs.is_deleted != 1";
        $s_result = $db->Execute($s_sql);
        foreach ($s_result as $key) {
            if ($name) {
                if($key['name'] !== 'Others') {
                    $name = $name . ", " . $key['name'];
                }
            } else {
                if($key['name'] !== 'Others') {
                    $name = $key['name'];
                }
            }
            $remarks = $key['remarks'];
        }
        return array(
            'name' => $name,
            'remarks' => $remarks,
        );
    }

    public function getNeuroExamination()
    {
        global $db;
        $name = null;
        $remarks = null;
        $n_sql = "SELECT 
                      scn.neuro_id,
                      scln.name,
                      scn.remarks 
                    FROM
                      seg_cf4_neuro AS scn 
                      INNER JOIN seg_cf4_lib_neuro AS scln
                      ON scn.`neuro_id` = scln.`id`
                    WHERE scn.encounter_nr = " . $this->enc_no . " 
                      AND scn.is_deleted != 1 ";
        $n_result = $db->Execute($n_sql);
        foreach ($n_result as $key) {
            if ($name) {
                if($key['name'] !== 'Others') {
                    $name = $name . ", " . $key['name'];
                }
            } else {
                if($key['name'] !== 'Others') {
                    $name = $key['name'];
                }
            }
            $remarks = $key['remarks'];
        }
        return array(
            'name' => $name,
            'remarks' => $remarks,
        );
    }

    public function getCourseInTheWard()
    {
        global $db;
        $course = null;
        $date = null;
        $first = null;
        $course_sql = "SELECT 
                  sccitw.`doctor_action`,
                  sccitw.date_action 
                FROM
                  seg_cf4_course_in_the_ward AS sccitw 
                WHERE sccitw.`encounter_nr` = " . $this->enc_no . " 
                  AND sccitw.`is_deleted` != 1 
                ORDER BY sccitw.date_action ASC";
        $course_res = $db->Execute($course_sql);
        foreach ($course_res as $key) {
            if ($course) {
                $course .= "\n " . date('Y-m-d', strtotime($key['date_action'])) . ' - ' . $key['doctor_action'];
            } else {
                $course = $key['doctor_action'];
                $date = date('Y-m-d', strtotime($key['date_action']));
                $course = $date . ' - ' . $course;
            }
        }
        return $course;
    }

    public function getDisposition()
    {
        global $db;
        $res_code = null;
        $dis_code = null;
        $code_sql = "SELECT 
                  sed.`disp_code` 
                FROM
                  seg_encounter_disposition AS sed 
                WHERE sed.`encounter_nr` = " . $this->enc_no . "";
        $course_res = $db->Execute($code_sql);
        while ($row = $course_res->FetchRow()) {
            $dis_code = $row['disp_code'];
        }

        $code_sql = "SELECT 
                      ser.`result_code` 
                    FROM
                      seg_encounter_result AS ser
                    WHERE ser.`encounter_nr` = " . $this->enc_no . "";
        $course_res = $db->Execute($code_sql);
        while ($row = $course_res->FetchRow()) {
            $res_code = $row['result_code'];
        }

        return array(
            'result' => $res_code,
            'disposition' => $dis_code
        );
    }


    public function getClinicalHistoryandPE()
    {
        global $db;
        //getPE
        $vs = null;
        $low_risk = null;
        $pe_sql = "SELECT 
                      scpe.`is_low_risk`,
                      scpe.`is_normal`
                    FROM
                      seg_cf4_physical_examination AS scpe 
                    WHERE scpe.`encounter_nr` = " . $this->enc_no . "";
        $pe_res = $db->Execute($pe_sql);
        while ($row = $pe_res->FetchRow()) {
            $vs = $row['is_normal'];
            $low_risk = $row['is_low_risk'];
        }

        //getDate
        $date = null;
        $date_of_lmp = null;
        $age_of_menarche = null;
        $cons_sql = "SELECT 
                      scmh.`init_prenatal_cons`,
                      scmh.`date_of_lmp`,
                      scmh.`age_of_menarche`
                    FROM
                      seg_cf4_menstrual_history AS scmh 
                    WHERE scmh.`encounter_nr` = " . $this->enc_no . "";
        $cons_res = $db->Execute($cons_sql);
        while ($row = $cons_res->FetchRow()) {
            $date = $row['init_prenatal_cons'] === "0000-00-00" ? null : $row['init_prenatal_cons'];
            $date_of_lmp = $row['date_of_lmp'] === "0000-00-00" ? null : $row['date_of_lmp'];
            $age_of_menarche = $row['age_of_menarche'];
        }

        //GetObstetricHistory
        $gravida = null;
        $t = null;
        $p = null;
        $a = null;
        $l = null;
        $obs_sql = "SELECT 
                      scoh.gravida AS gravida,
                      scoh.`parity` AS parity,
                      scoh.`term_births` AS t,
                      scoh.`preterm_births` AS p,
                      scoh.`abortion` AS a,
                      scoh.`living_children` AS l
                    FROM
                      seg_cf4_obstetric_history AS scoh 
                    WHERE scoh.`encounter_nr` = " . $this->enc_no . "";
        $obs_res = $db->Execute($obs_sql);
        while ($row = $obs_res->FetchRow()) {
            $gravida = $row['gravida'];
            $parity = $row['parity'];
            $t = $row['t'];
            $p = $row['p'];
            $a = $row['a'];
            $l = $row['l'];
        }

        return array(
            'vs' => $vs,
            'low_risk' => $low_risk,
            'date' => $date,
            'date_of_lmp' => $date_of_lmp,
            'age_of_menarche' => $age_of_menarche,
            'gravida' => $gravida,
            'parity' => $parity,
            't' => $t,
            'p' => $p,
            'a' => $a,
            'l' => $l
        );
    }

    public function getObstetricRisk()
    {
        global $db;
        $obs_a = null;
        $obs_b = null;
        $obs_c = null;
        $obs_d = null;
        $obs_e = null;
        $obs_f = null;
        $obs_g = null;
        $obs_h = null;
        $obs_i = null;
        $obs_sql = "SELECT 
                      scorf.`clinical_history_id` 
                    FROM
                      seg_cf4_obstetric_risk_factor AS scorf 
                    WHERE scorf.`encounter_nr` = " . $this->enc_no . "
                    AND scorf.`is_deleted` != 1";
        $obs_res = $db->Execute($obs_sql);
        foreach ($obs_res as $key) {
            if ($key['clinical_history_id'] === '1') {
                $obs_a = "1";
            } else {
                if ($key['clinical_history_id'] === '2') {
                    $obs_b = "1";
                } else {
                    if ($key['clinical_history_id'] === '3') {
                        $obs_c = "1";
                    } else {
                        if ($key['clinical_history_id'] === '4') {
                            $obs_d = "1";
                        } else {
                            if ($key['clinical_history_id'] === '5') {
                                $obs_e = "1";
                            } else {
                                if ($key['clinical_history_id'] === '6') {
                                    $obs_f = "1";
                                } else {
                                    if ($key['clinical_history_id'] === '7') {
                                        $obs_g = "1";
                                    } else {
                                        if ($key['clinical_history_id'] === '8') {
                                            $obs_h = "1";
                                        } else {
                                            $obs_i = "1";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return array(
            'obs_a' => $obs_a,
            'obs_b' => $obs_b,
            'obs_c' => $obs_c,
            'obs_d' => $obs_d,
            'obs_e' => $obs_e,
            'obs_f' => $obs_f,
            'obs_g' => $obs_g,
            'obs_h' => $obs_h,
            'obs_i' => $obs_i,
        );
    }

    public function getMedicalSurgicalRisk()
    {
        global $db;
        $surgical_a = null;
        $surgical_b = null;
        $surgical_c = null;
        $surgical_d = null;
        $surgical_e = null;
        $surgical_f = null;
        $surgical_g = null;
        $surgical_h = null;
        $surgical_i = null;
        $surgical_j = null;
        $surgical_k = null;
        $surgical_sql = "SELECT 
                          scmrf.`clinical_history_id` 
                        FROM
                          seg_cf4_medical_risk_factor AS scmrf 
                        WHERE scmrf.`encounter_nr` = " . $this->enc_no . "
                        AND scmrf.`is_deleted` != 1";
        $surgical_res = $db->Execute($surgical_sql);
        foreach ($surgical_res as $key) {
            if ($key['clinical_history_id'] === '10') {
                $surgical_a = "1";
            } else {
                if ($key['clinical_history_id'] === '11') {
                    $surgical_b = "1";
                } else {
                    if ($key['clinical_history_id'] === '12') {
                        $surgical_c = "1";
                    } else {
                        if ($key['clinical_history_id'] === '13') {
                            $surgical_d = "1";
                        } else {
                            if ($key['clinical_history_id'] === '14') {
                                $surgical_e = "1";
                            } else {
                                if ($key['clinical_history_id'] === '15') {
                                    $surgical_f = "1";
                                } else {
                                    if ($key['clinical_history_id'] === '16') {
                                        $surgical_g = "1";
                                    } else {
                                        if ($key['clinical_history_id'] === '17') {
                                            $surgical_h = "1";
                                        } else {
                                            if ($key['clinical_history_id'] === '18') {
                                                $surgical_i = "1";
                                            } else {
                                                if ($key['clinical_history_id'] === '19') {
                                                    $surgical_j = "1";
                                                } else {
                                                    $surgical_k = "1";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return array(
            'surgical_a' => $surgical_a,
            'surgical_b' => $surgical_b,
            'surgical_c' => $surgical_c,
            'surgical_d' => $surgical_d,
            'surgical_e' => $surgical_e,
            'surgical_f' => $surgical_f,
            'surgical_g' => $surgical_g,
            'surgical_h' => $surgical_h,
            'surgical_i' => $surgical_i,
            'surgical_j' => $surgical_j,
            'surgical_k' => $surgical_k,
        );
    }

    public function getAdmittingDiagnosis()
    {
        global $db;
        $admitting_diagnosis = null;
        $adm_sql = "SELECT
                  ce.er_opd_diagnosis AS admitting_diagnosis
                FROM
                  care_encounter AS ce
                WHERE ce.encounter_nr = " . $this->enc_no . "";
        $adm_res = $db->Execute($adm_sql);
        while ($row = $adm_res->FetchRow()) {
            $admitting_diagnosis = $row['admitting_diagnosis'];
        }
        return $admitting_diagnosis;
    }

    public function getDeliveryPlan()
    {
        global $db;
        $is_benefit = null;
        $edc = null;
        $delivery_sql = "SELECT 
              scdp.`is_benefit`,
              scdp.`edc`
            FROM
              seg_cf4_delivery_plan AS scdp 
            WHERE scdp.`encounter_nr` = " . $this->enc_no . "";
        $delivery_res = $db->Execute($delivery_sql);
        while ($row = $delivery_res->FetchRow()) {
            $is_benefit = $row['is_benefit'];
            $edc = $row['edc'];
        }
        return array(
            'is_benefit' => $is_benefit,
            'edc' => $edc
        );
    }

    public function getFollowupPrenatalConsultation()
    {
        global $db;
        $two = array();
        $three = array();
        $four = array();
        $five = array();
        $six = array();
        $seven = array();
        $eight = array();
        $nine = array();
        $ten = array();
        $eleven = array();
        $twelve = array();
        $prenatal_sql = "SELECT 
                        scpv.`date_visit`,
                        scpv.`aog`,
                        scpv.`weight`,
                        scpv.`cardiac_rate`,
                        scpv.`respiratory_rate`,
                        scpv.`bp`,
                        scpv.`temperature`,
                        scpv.`prenatal_consultation_no`
                        FROM
                          seg_cf4_prenatal_visits AS scpv 
                        WHERE scpv.`encounter_nr` = " . $this->enc_no . "
                          AND scpv.`is_deleted` != 1 ";
        $prenatal_res = $db->Execute($prenatal_sql);
        foreach ($prenatal_res as $key) {
            if ($key['prenatal_consultation_no'] === '2') {
                $two = array(
                    'date' => $key['date_visit'],
                    'aog' => $key['aog'],
                    'weight' => $key['weight'],
                    'cr' => $key['cardiac_rate'],
                    'rr' => $key['respiratory_rate'],
                    'bp' => $key['bp'],
                    'temp' => $key['temperature']
                );
            } else {
                if ($key['prenatal_consultation_no'] === '3') {
                    $three = array(
                        'date' => $key['date_visit'],
                        'aog' => $key['aog'],
                        'weight' => $key['weight'],
                        'cr' => $key['cardiac_rate'],
                        'rr' => $key['respiratory_rate'],
                        'bp' => $key['bp'],
                        'temp' => $key['temperature']
                    );
                } else {
                    if ($key['prenatal_consultation_no'] === '4') {
                        $four = array(
                            'date' => $key['date_visit'],
                            'aog' => $key['aog'],
                            'weight' => $key['weight'],
                            'cr' => $key['cardiac_rate'],
                            'rr' => $key['respiratory_rate'],
                            'bp' => $key['bp'],
                            'temp' => $key['temperature']
                        );
                    } else {
                        if ($key['prenatal_consultation_no'] === '5') {
                            $five = array(
                                'date' => $key['date_visit'],
                                'aog' => $key['aog'],
                                'weight' => $key['weight'],
                                'cr' => $key['cardiac_rate'],
                                'rr' => $key['respiratory_rate'],
                                'bp' => $key['bp'],
                                'temp' => $key['temperature']
                            );
                        } else {
                            if ($key['prenatal_consultation_no'] === '6') {
                                $six = array(
                                    'date' => $key['date_visit'],
                                    'aog' => $key['aog'],
                                    'weight' => $key['weight'],
                                    'cr' => $key['cardiac_rate'],
                                    'rr' => $key['respiratory_rate'],
                                    'bp' => $key['bp'],
                                    'temp' => $key['temperature']
                                );
                            } else {
                                if ($key['prenatal_consultation_no'] === '7') {
                                    $seven = array(
                                        'date' => $key['date_visit'],
                                        'aog' => $key['aog'],
                                        'weight' => $key['weight'],
                                        'cr' => $key['cardiac_rate'],
                                        'rr' => $key['respiratory_rate'],
                                        'bp' => $key['bp'],
                                        'temp' => $key['temperature']
                                    );
                                } else {
                                    if ($key['prenatal_consultation_no'] === '8') {
                                        $eight = array(
                                            'date' => $key['date_visit'],
                                            'aog' => $key['aog'],
                                            'weight' => $key['weight'],
                                            'cr' => $key['cardiac_rate'],
                                            'rr' => $key['respiratory_rate'],
                                            'bp' => $key['bp'],
                                            'temp' => $key['temperature']
                                        );
                                    } else {
                                        if ($key['prenatal_consultation_no'] === '9') {
                                            $nine = array(
                                                'date' => $key['date_visit'],
                                                'aog' => $key['aog'],
                                                'weight' => $key['weight'],
                                                'cr' => $key['cardiac_rate'],
                                                'rr' => $key['respiratory_rate'],
                                                'bp' => $key['bp'],
                                                'temp' => $key['temperature']
                                            );
                                        } else {
                                            if ($key['prenatal_consultation_no'] === '10') {
                                                $ten = array(
                                                    'date' => $key['date_visit'],
                                                    'aog' => $key['aog'],
                                                    'weight' => $key['weight'],
                                                    'cr' => $key['cardiac_rate'],
                                                    'rr' => $key['respiratory_rate'],
                                                    'bp' => $key['bp'],
                                                    'temp' => $key['temperature']
                                                );
                                            } else {
                                                if ($key['prenatal_consultation_no'] === '11') {
                                                    $eleven = array(
                                                        'date' => $key['date_visit'],
                                                        'aog' => $key['aog'],
                                                        'weight' => $key['weight'],
                                                        'cr' => $key['cardiac_rate'],
                                                        'rr' => $key['respiratory_rate'],
                                                        'bp' => $key['bp'],
                                                        'temp' => $key['temperature']
                                                    );
                                                } else {
                                                    $twelve = array(
                                                        'date' => $key['date_visit'],
                                                        'aog' => $key['aog'],
                                                        'weight' => $key['weight'],
                                                        'cr' => $key['cardiac_rate'],
                                                        'rr' => $key['respiratory_rate'],
                                                        'bp' => $key['bp'],
                                                        'temp' => $key['temperature']
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return array(
            '2nd' => $two,
            '3rd' => $three,
            '4th' => $four,
            '5th' => $five,
            '6th' => $six,
            '7th' => $seven,
            '8th' => $eight,
            '9th' => $nine,
            '10th' => $ten,
            '11th' => $eleven,
            '12th' => $twelve
        );
    }

    public function getDateTimeDeliveryOutcome()
    {
        global $db;
        $date_time = null;
        $date_time_sql = "SELECT 
                        scddo.`date`,
                        scddo.`time`
                        FROM
                          seg_cf4_dt_delivery_outcome AS scddo 
                        WHERE scddo.`encounter_nr` = " . $this->enc_no . "";
        $date_time_res = $db->Execute($date_time_sql);
        while ($row = $date_time_res->FetchRow()) {
            $date_time = date('m-d-Y', strtotime($row['date'])) . " " . date('h:i:a', strtotime($row['time']));
        }
        return $date_time;
    }

    public function getMaternalOutcome()
    {
        global $db;
        $manner_of_delivery = null;
        $maternal_outcome = null;
        $aog_by_lmp = null;
        $presentation = null;
        $maternal_sql = "SELECT 
                            scpu.`manner_of_delivery`,
                            scpu.`maternal_outcome`,
                            scpu.`aog_by_lmp`,
                            scpu.`presentation`
                        FROM
                          seg_cf4_pregnancy_uterine AS scpu 
                        WHERE scpu.`encounter_nr` = " . $this->enc_no . "";
        $maternal_res = $db->Execute($maternal_sql);
        while ($row = $maternal_res->FetchRow()) {
            $manner_of_delivery = $row['manner_of_delivery'];
            $maternal_outcome = $row['maternal_outcome'];
            $aog_by_lmp = $row['aog_by_lmp'];
            $presentation = $row['presentation'];
        }

        return array(
            'manner_of_delivery' => $manner_of_delivery,
            'maternal_outcome' => $maternal_outcome,
            'aog_by_lmp' => $aog_by_lmp,
            'presentation' => $presentation
        );
    }

    public function getBirthOutcome()
    {
        global $db;
        $fetal = null;
        $apgar = null;
        $weight = null;
        $sex = null;
        $birth_sql = "SELECT 
                    scbo.`fetal_outcome`,
                    scbo.`apgar_score`,
                    scbo.`birth_weight`,
                    scbo.`sex`
                    FROM
                      seg_cf4_birth_outcome AS scbo 
                    WHERE scbo.`encounter_nr` = " . $this->enc_no . " 
                    AND scbo.`is_deleted` != 1";
        $birth_res = $db->Execute($birth_sql);
        while ($row = $birth_res->FetchRow()) {
            $fetal = $row['fetal_outcome'];
            $apgar = $row['apgar_score'];
            $weight = $row['birth_weight'];
            $sex = $row['sex'];
        }

        return array(
            'fetal' => $fetal,
            'apgar' => $apgar,
            'weight' => $weight,
            'sex' => $sex
        );
    }

    public function getPostpartumDate()
    {
        global $db;
        $date = null;
        $postpartum_sql = "SELECT 
                        scpdo.`date`
                        FROM
                          seg_cf4_spf_delivery_outcome AS scpdo 
                        WHERE scpdo.`encounter_nr` = " . $this->enc_no . "";
        $postparutm_res = $db->Execute($postpartum_sql);
        while ($row = $postparutm_res->FetchRow()) {
            $date = $row['date'];
        }

        return $date ? date('m-d-Y', strtotime($date)) : null;
    }

    public function getDateTimeDischarge()
    {
        global $db;
        $date_time = null;
        $dis_sql = "SELECT 
                            scddo.`date`,
                            scddo.`time`
                            FROM
                              seg_cf4_dt_discharge_outcome AS scddo 
                            WHERE scddo.`encounter_nr` = " . $this->enc_no . "";
        $dis_res = $db->Execute($dis_sql);
        while ($row = $dis_res->FetchRow()) {
            $date_time = date('m-d-Y', strtotime($row['date'])) . " " . date('h:i:a', strtotime($row['time']));
        }
        return $date_time;
    }

    public function getPostpartumCare()
    {
        global $db;
        $perineal = array();
        $perineal_sql = "SELECT 
                    scpwc.`is_done`,
                    scpwc.`remarks`
                    FROM
                      seg_cf4_perineal_wound_care AS scpwc 
                    WHERE scpwc.`encounter_nr` = " . $this->enc_no . "";
        $perineal_res = $db->Execute($perineal_sql);
        while ($row = $perineal_res->FetchRow()) {
            $perineal = array(
                'is_done' => $row['is_done'],
                'remarks' => $row['remarks']
            );
        }

        $maternal = array();
        $maternal_sql = "SELECT 
                    scsom.`is_done`,
                    scsom.`remarks`
                    FROM
                      seg_cf4_signs_of_meternal AS scsom 
                    WHERE scsom.`encounter_nr` = " . $this->enc_no . "";
        $maternal_res = $db->Execute($maternal_sql);
        while ($row = $maternal_res->FetchRow()) {
            $maternal = array(
                'is_done' => $row['is_done'],
                'remarks' => $row['remarks']
            );
        }

        $breastfeed = array();
        $breastfeed_sql = "SELECT 
                        scbn.`is_done`,
                        scbn.`remarks`
                        FROM
                          seg_cf4_breastfeeding_nutrition AS scbn 
                        WHERE scbn.`encounter_nr` = " . $this->enc_no . "";
        $breastfeed_res = $db->Execute($breastfeed_sql);
        while ($row = $breastfeed_res->FetchRow()) {
            $breastfeed = array(
                'is_done' => $row['is_done'],
                'remarks' => $row['remarks']
            );
        }

        $family_planning = array();
        $family_planning_sql = "SELECT 
                                scfp.`is_done`,
                                scfp.`remarks`
                                FROM
                                  seg_cf4_family_planning AS scfp 
                                WHERE scfp.`encounter_nr` = " . $this->enc_no . "";
        $family_planning_res = $db->Execute($family_planning_sql);
        while ($row = $family_planning_res->FetchRow()) {
            $family_planning = array(
                'is_done' => $row['is_done'],
                'remarks' => $row['remarks']
            );
        }

        $family_planning_service = array();
        $family_planning_service_sql = "SELECT 
                                scfps.`is_done`,
                                scfps.`remarks`
                                FROM
                                  seg_cf4_family_planning_service AS scfps 
                                WHERE scfps.`encounter_nr` = " . $this->enc_no . "";
        $family_planning_service_res = $db->Execute($family_planning_service_sql);
        while ($row = $family_planning_service_res->FetchRow()) {
            $family_planning_service = array(
                'is_done' => $row['is_done'],
                'remarks' => $row['remarks']
            );
        }

        $referred_to_partner = null;
        $reffered_to_partner_sql = "SELECT 
                                    scrpp.`is_done`,
                                    scrpp.`remarks`
                                    FROM
                                      seg_cf4_referred_partner_physician AS scrpp 
                                    WHERE scrpp.`encounter_nr` = " . $this->enc_no . "";
        $reffered_to_partner_res = $db->Execute($reffered_to_partner_sql);
        while ($row = $reffered_to_partner_res->FetchRow()) {
            $referred_to_partner = array(
                'is_done' => $row['is_done'],
                'remarks' => $row['remarks']
            );
        }

        $next_postpartum = array();
        $next_postpartum_sql = "SELECT 
                                    scsnp.`is_done`,
                                    scsnp.`remarks`
                                    FROM
                                      seg_cf4_schedule_next_postpartum AS scsnp 
                                    WHERE scsnp.`encounter_nr` =  " . $this->enc_no . "";
        $next_postpartum_res = $db->Execute($next_postpartum_sql);
        while ($row = $next_postpartum_res->FetchRow()) {
            $next_postpartum = array(
                'is_done' => $row['is_done'],
                'remarks' => $row['remarks']
            );
        }

        return array(
            'perineal' => $perineal,
            'maternal' => $maternal,
            'breastfeeding' => $breastfeed,
            'family_planning' => $family_planning,
            'family_planning_service' => $family_planning_service,
            'referred_to_partner' => $referred_to_partner,
            'schedule_postpartum' => $next_postpartum
        );
    }

    /**
     * Get Doctor Attending Physician name.
     * params $bill_nr String
     * return $name String
     */
    public function getDoctor()
    {
        global $db;
        $strSQL = "SELECT sbe.accommodation_type, sbe.bill_nr, sbe.bill_dte, sbe.is_final FROM seg_billing_encounter sbe 
               WHERE sbe.encounter_nr = '$this->enc_no' AND sbe.is_deleted IS NULL AND sbe.is_final = 1";
        $result = $db->Execute($strSQL);
        $bill = $result->FetchRow();
        $bill_nr = $bill['bill_nr'];

        $strSQL = "SELECT sbp.dr_nr, cp.name_first, cp.name_last, cp.name_middle, cp.suffix, max_acc.accreditation_nr , sbp.role_area
               FROM seg_billing_pf sbp LEFT JOIN (SELECT sda.dr_nr, sda.accreditation_nr, MAX(sda.create_dt) AS create_dt 
               FROM seg_dr_accreditation sda GROUP BY sda.dr_nr) AS max_acc ON max_acc.dr_nr = sbp.dr_nr 
               INNER JOIN care_personell cpl ON cpl.nr = sbp.dr_nr INNER JOIN care_person cp ON cp.pid = cpl.pid
               WHERE sbp.bill_nr = '$bill_nr' GROUP BY sbp.dr_nr";
        $execute = $db->Execute($strSQL);
        $doctor = $execute->FetchRow();
        $honorifics = $doctor ? 'DR.' : '';
        $lastname = $doctor ? $doctor['name_last'] . ', ' : '';
        $name = $honorifics . ' ' . $lastname . '' . $doctor['name_first'] . ' ' . $doctor['name_middle'] . ' ' . $doctor['suffix'];

        return $name;
    }

    public function getReviewOfSystem()
    {
        global $db;
        $string_data = "";
        $ss_sql = "SELECT 
				  scpss.sign_symptoms,
				  scpss.pains,
				  scpss.others 
				FROM
				  seg_cf4_pertinent_sign_symptoms AS scpss 
				WHERE scpss.encounter_nr = '$this->enc_no'AND scpss.is_deleted != 1";
        $ss_result = $db->Execute($ss_sql);

        while ($row = $ss_result->FetchRow()) {
            $id = $row['sign_symptoms'];
            $get_name = "SELECT 
                          sclcc.name 
                        FROM
                          `seg_cf4_lib_chief_complaint` AS sclcc 
                        WHERE sclcc.id = '$id' AND sclcc.is_active = 1 ";
            $get_name_result = $db->Execute($get_name);

            while ($get_name_result_row = $get_name_result->FetchRow()) {
                if ($row['sign_symptoms'] === '38') {
                    $string_data .= $get_name_result_row['name'].': '.$row['pains'].', ';
                }elseif ($row['sign_symptoms'] === 'X'){
                    $string_data .= $get_name_result_row['name'].': '.$row['others'].', ';
                }else{
                    $string_data .= $get_name_result_row['name'].', ';
                }
            }
        }

        return $string_data !== "" ? substr(trim($string_data), 0, -1) : '';
    }

    public function getMedicineStringFormat()
    {
        global $db;
        $string_data = "";
        $med_sql = "SELECT 
                      scm.drug_code,
                      scm.generic,
                      scm.cost,
                      scm.frequency,
                      scm.quantity,
                      scm.is_pndf,
                      scm.route 
                    FROM
                      seg_cf4_medicine AS scm 
                    WHERE scm.encounter_nr = ".$this->enc_no."
                      AND scm.is_deleted != 1
                    ORDER BY scm.created_at ASC";
        $med_res = $db->Execute($med_sql);

        while ($row = $med_res->FetchRow()){
            $string_data .= $row['generic'].', ';
        }

        return $string_data !== "" ? substr(trim($string_data), 0, -1) : '';
    }

    public function filterString($words)
    {
        $arr = explode(',', $words);
        $arr_fltr = array_filter($arr);
        return implode(',', $arr_fltr);
    }
}
