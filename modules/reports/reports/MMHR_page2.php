<?php
//created by daryl
//for MMHR page 2

// FOR LIMITING RESULT FOR SURGICAL OUTPUT BASED ON SPECIFIC PROCEDURES
// BILATERAL TUBAL LIGATION = Bilateral Endoscopic Destruction Or Occlusion of Fallopian Tube
define(BILATERAL_TUBAL_LIGATION, '58600');
define(VASECTOMY, '55250');
define(CAESAREAN, '59513');
define(PHILHEALTH, 18);
define(NORMAL_DELIVERY_CODE, 'O80');
define(CAESAREAN_DELIVERY_CODE, '082');
define(MMHR_SIGNATORY, 'mmhr');
define(NO_DATA, '');

$first_date = date("Y-m-01", strtotime($mmhr_year."-".$mmhr_month));
$last_date = date("Y-m-31", strtotime($mmhr_year."-".$mmhr_month));
$patient_type = '3,4';

global $HTTP_SESSION_VARS;
$counter = 1;
  # modified by JOY @ 12-02-2016
// D. MOST COMMON CAUSES OF CONFINEMENT
    $confinement_sql = "SELECT
                          sd.`code` AS codex, /*added by MARK Dec 14, 2016*/
                          sd.description,
                          SUM(
                            CASE
                              WHEN EXISTS (SELECT ins.hcare_id FROM seg_encounter_insurance AS ins WHERE ins.encounter_nr = e.encounter_nr AND ins.hcare_id = '18') 
                              THEN 1 
                              ELSE 0 
                            END
                          ) AS phic_occurrence,
                          SUM(
                            CASE
                              WHEN NOT EXISTS (SELECT ins.hcare_id FROM seg_encounter_insurance AS ins WHERE ins.encounter_nr = e.encounter_nr AND ins.hcare_id = '18')
                              THEN 1 
                              ELSE 0 
                            END
                          ) AS nonphic_occurrence 
                        FROM
                            care_encounter_diagnosis AS ed 
                          INNER JOIN seg_encounter_diagnosis AS sd
                            ON ed.`encounter_nr`= sd.`encounter_nr`
                          INNER JOIN care_encounter AS e 
                            ON e.encounter_nr = ed.encounter_nr 
                          INNER JOIN care_icd10_en AS c 
                            ON c.diagnosis_code = ed.code
                        WHERE e.STATUS NOT IN (
                            'deleted',
                            'hidden',
                            'inactive',
                            'void'
                          ) 
                          AND ed.STATUS NOT IN (
                            'deleted',
                            'hidden',
                            'inactive',
                            'void'
                          ) 
                          AND DATE(e.discharge_date) BETWEEN ".$db->qstr($first_date)."
                          AND ".$db->qstr($last_date)."  
                          AND ed.type_nr IN ($type_nr) 
                          AND ed.encounter_type IN ($patient_type) 
                          AND IF(
                            INSTR(c.diagnosis_code, '.'),
                            SUBSTR(
                              c.diagnosis_code,
                              1,
                              IF(
                                INSTR(c.diagnosis_code, '.'),
                                INSTR(c.diagnosis_code, '.') - 1,
                                0
                              )
                            ),
                            c.diagnosis_code
                          ) REGEXP '^[[:alpha:]][[:digit:]]' 
                        GROUP BY 
                          (SELECT 
                            IF(
                              INSTR(ed.code, '.'),
                              SUBSTRING(ed.code, 1, 3),
                              IF(
                                INSTR(ed.code, '/'),
                                SUBSTRING(ed.code, 1, 5),
                                IF(
                                  INSTR(ed.code, ','),
                                  SUBSTRING(ed.code, 1, 3),
                                  IF(
                                    INSTR(ed.code, '-'),
                                    SUBSTRING(ed.code, 1, 3),
                                    ed.code
                                  )
                                )
                              )
                            )) 
                        ORDER BY COUNT(ed.CODE) DESC 
                        LIMIT 10";

    $confinement = $db->GetAll($confinement_sql);

    if ($confinement) {
        $arr_len = sizeof($confinement);

        foreach($confinement as $key => $value) {
            if (!empty($value['description'])) {
                $arr_confinement[] = array(
                                            "description"        => $value["codex"]." - ".$value["description"],
                                            "phic_occurrence"    => $value["phic_occurrence"],
                                            "nonphic_occurrence" => $value["nonphic_occurrence"]
                                          );
            } else {
                $check_arr_len++;
            }
        }

        if ($arr_len == $check_arr_len) {
            for ($i = 0; $i < 10; $i++) {
                if ($counter == 10)
                    $params->put("Confinement_".$i, $counter.". "."                       ".NO_DATA);
                else
                    $params->put("Confinement_".$i, $counter.". "."                        ".NO_DATA);
                
                $params->put("ConfinementNHIP_".$i, NO_DATA);
                $params->put("ConfinementNON-NHIP_".$i, NO_DATA);
                $counter++;
            }
        } else {
            for ($i = 0; $i < 10; $i++) {
                $params->put("Confinement_".$i, $counter.". ".$arr_confinement[$i]["description"]);
                $params->put("ConfinementNHIP_".$i, $arr_confinement[$i]["phic_occurrence"]);
                $params->put("ConfinementNON-NHIP_".$i, $arr_confinement[$i]["nonphic_occurrence"]);
                $counter++;
            }
        }
    } else {
        for ($i = 0; $i < 10; $i++) {
            if ($counter == 10)
                    $params->put("Confinement_".$i, $counter.". "."                       ".NO_DATA);
            else
                $params->put("Confinement_".$i, $counter.". "."                        ".NO_DATA);
                
            $params->put("ConfinementNHIP_".$i, NO_DATA);
            $params->put("ConfinementNON-NHIP_".$i, NO_DATA);
            $counter++;
        }
    }
    $counter = 1;
    $check_arr_len = 0;

//E. SURGICAL OUTPUT - TOP 10 Procedures
    $procedure_sql = "SELECT
                      smod.`ops_code` AS ops_codex, /*added by MARK Dec 14, 2016*/
                        smod.description, /* modified by JOY @ 12-02-2016 */
                        SUM(
                          CASE
                            WHEN EXISTS 
                            (SELECT 
                              ins.hcare_id 
                            FROM
                              seg_encounter_insurance AS ins 
                            WHERE ins.encounter_nr = e.encounter_nr 
                              AND ins.hcare_id = '18') 
                            THEN 1 
                            ELSE 0 
                          END
                        ) AS phic_occurrence,
                        SUM(
                          CASE
                            WHEN NOT EXISTS 
                            (SELECT 
                              ins.hcare_id 
                            FROM
                              seg_encounter_insurance AS ins 
                            WHERE ins.encounter_nr = e.encounter_nr 
                              AND ins.hcare_id = '18') 
                            THEN 1 
                            ELSE 0 
                          END
                        ) AS nonphic_occurrence ,
                        smod.`ops_code`
                      FROM
                        care_encounter AS e 
                        INNER JOIN seg_misc_ops smo 
                          ON smo.`encounter_nr` = e.`encounter_nr` 
                        INNER JOIN `seg_misc_ops_details` smod 
                          ON smod.`refno` = smo.`refno` 
                        INNER JOIN `seg_case_rate_packages` scrp 
                          ON scrp.code = smod.`ops_code` 
                          AND scrp.case_type = 'p' 
                           AND scrp.code NOT IN (".$db->qstr(CAESAREAN).",".$db->qstr(BILATERAL_TUBAL_LIGATION)." , ".$db->qstr(VASECTOMY).")
                      WHERE e.STATUS NOT IN (
                          'deleted',
                          'hidden',
                          'inactive',
                          'void'
                        ) 
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($first_date)."
                        AND ".$db->qstr($last_date)."  
                        GROUP BY smod.ops_code 
                      ORDER BY COUNT(smod.ops_code) DESC 
                      LIMIT 10 ";

                      // /print_r($procedure_sql); die();
                     

    $procedure = $db->GetAll($procedure_sql);

    if ($procedure) {
        $arr_len = sizeof($procedure);

        foreach($procedure as $key => $value) {
            if (!empty($value['description'])) {
                $arr_procedure[] = array(
                                          "description"        => $value["ops_codex"]." - ".$value["description"],
                                          "phic_occurrence"    => $value["phic_occurrence"],
                                          "nonphic_occurrence" => $value["nonphic_occurrence"]
                                        );
            } else {
                $check_arr_len++;
            }
        }

        if ($arr_len == $check_arr_len) {
            for ($i = 0; $i < 10; $i++) {
                if ($counter == 10)
                    $params->put("Procedure_".$i, $counter.". "."                       ".NO_DATA);
                else
                    $params->put("Procedure_".$i, $counter.". "."                        ".NO_DATA);
                
                $params->put("ProcedureNHIP_".$i, NO_DATA);
                $params->put("ProcedureNON-NHIP_".$i, NO_DATA);
                $counter++;
            }
        } else {
            for ($i = 0; $i < 10; $i++) {
                $params->put("Procedure_".$i, $counter.". ".$arr_procedure[$i]["description"]);
                $params->put("ProcedureNHIP_".$i, $arr_procedure[$i]["phic_occurrence"]);
                $params->put("ProcedureNON-NHIP_".$i, $arr_procedure[$i]["nonphic_occurrence"]);
                $counter++;
            }
        }
    } else {
        for ($i = 0; $i < 10; $i++) {
            if ($counter == 10)
                    $params->put("Procedure_".$i, $counter.". "."                       ".NO_DATA);
            else
                $params->put("Procedure_".$i, $counter.". "."                        ".NO_DATA);
                
            $params->put("ProcedureNHIP_".$i, NO_DATA);
            $params->put("ProcedureNON-NHIP_".$i, NO_DATA);
            $counter++;
        }
    }
    $data[0]["procedure"] = NULL;
    $counter = 1;
    $check_arr_len = 0;

// E.1 TOTAL SURGICAL STERILIZATION
    $sterilization_sql = "SELECT
                        scrp.description,
                        SUM(
                          CASE
                            WHEN EXISTS 
                            (SELECT 
                              ins.hcare_id 
                            FROM
                              seg_encounter_insurance AS ins 
                            WHERE ins.encounter_nr = e.encounter_nr 
                              AND ins.hcare_id = '18') 
                            THEN 1 
                            ELSE 0 
                          END
                        ) AS phic_occurrence,
                        SUM(
                          CASE
                            WHEN NOT EXISTS 
                            (SELECT 
                              ins.hcare_id 
                            FROM
                              seg_encounter_insurance AS ins 
                            WHERE ins.encounter_nr = e.encounter_nr 
                              AND ins.hcare_id = '18') 
                            THEN 1 
                            ELSE 0 
                          END
                        ) AS nonphic_occurrence ,
                        smod.`ops_code`
                      FROM
                        care_encounter AS e 
                        INNER JOIN seg_misc_ops smo 
                          ON smo.`encounter_nr` = e.`encounter_nr` 
                        INNER JOIN `seg_misc_ops_details` smod 
                          ON smod.`refno` = smo.`refno` 
                        INNER JOIN `seg_case_rate_packages` scrp 
                          ON scrp.code = smod.`ops_code` 
                          AND scrp.case_type = 'p' 
                           AND scrp.code IN (".$db->qstr(BILATERAL_TUBAL_LIGATION)." , ".$db->qstr(VASECTOMY).")
                      WHERE e.STATUS NOT IN (
                          'deleted',
                          'hidden',
                          'inactive',
                          'void'
                        ) 
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($first_date)."
                        AND ".$db->qstr($last_date)."  
                        GROUP BY smod.ops_code 
                      ORDER BY COUNT(smod.ops_code) DESC ";
                       // die($sterilization_s  ql);
    $sterilization = $db->GetAll($sterilization_sql);

    if ($sterilization) {
        for ($i=0; $i < 2 ; $i++) { 
            $params->put("STERI-NHIP_".$i,$sterilization[$i]["phic_occurrence"]);
            $params->put("STERI-NONNHIP_".$i,$sterilization[$i]["nonphic_occurrence"]);
            $sterilization_nhip += $sterilization[$i]["phic_occurrence"];
            $sterilization_non_nhip += $sterilization[$i]["nonphic_occurrence"];
        }

    } else {
     
        for ($i=0; $i < 2 ; $i++) { 
            $params->put("STERI-NHIP_".$i,NO_DATA);
            $params->put("STERI-NONNHIP_".$i,NO_DATA);
            $sterilization_nhip += $sterilization[$i]["phic_occurrence"];
            $sterilization_non_nhip += $sterilization[$i]["nonphic_occurrence"];
        }
    }
    
    if ($sterilization_nhip == 0 && $sterilization_non_nhip == 0) {
        $params->put("TOTAL_STERI-NHIP", NO_DATA);
        $params->put("TOTAL_STERI-NONNHIP", NO_DATA);
    } else {
        $params->put("TOTAL_STERI-NHIP", (string)$sterilization_nhip);
        $params->put("TOTAL_STERI-NONNHIP", (string)$sterilization_non_nhip);
    }

// F. OBSTETRICAL PROCEDURES (TOTAL NUMBER OF DELIVERIES) = PHIC
    $obstetrical_delivery_phic_sql = "SELECT 
                                          SUM(
                                            CASE
                                              WHEN EXISTS (SELECT ins.hcare_id FROM seg_encounter_insurance AS ins WHERE ins.encounter_nr = e.encounter_nr AND ins.hcare_id = '18')
                                              AND  INSTR(ed.CODE, ".$db->qstr(NORMAL_DELIVERY_CODE).") 
                                              THEN 1 
                                              ELSE 0 
                                            END
                                          ) AS normal,
                                          SUM(
                                            CASE
                                              WHEN EXISTS (SELECT ins.hcare_id FROM seg_encounter_insurance AS ins WHERE ins.encounter_nr = e.encounter_nr AND ins.hcare_id = '18') 
                                              AND INSTR(c.diagnosis_code, ".$db->qstr(CAESAREAN_DELIVERY_CODE).") 
                                              THEN 1 
                                              ELSE 0 
                                            END
                                          ) AS caesarean 
                                        FROM
                                          care_encounter_diagnosis AS ed 
                                          INNER JOIN care_encounter AS e 
                                            ON e.encounter_nr = ed.encounter_nr 
                                          INNER JOIN care_icd10_en AS c 
                                            ON c.diagnosis_code = ed.CODE 
                                          INNER JOIN care_person AS p 
                                            ON p.pid = e.pid 
                                          INNER JOIN seg_icd_10_deliveries AS d 
                                            ON (
                                              d.icd_10 = ed.CODE 
                                              OR d.icd_10 = 
                                              (SELECT 
                                                IF(
                                                  INSTR(ed.CODE, '.'),
                                                  SUBSTRING(ed.CODE, 1, 3),
                                                  IF(
                                                    INSTR(ed.CODE, '/'),
                                                    SUBSTRING(ed.CODE, 1, 5),
                                                    IF(
                                                      INSTR(ed.CODE, ','),
                                                      SUBSTRING(ed.CODE, 1, 3),
                                                      IF(
                                                        INSTR(ed.CODE, '-'),
                                                        SUBSTRING(ed.CODE, 1, 3),
                                                        ed.CODE
                                                      )
                                                    )
                                                  )
                                                ))
                                            ) 
                                        WHERE e.STATUS NOT IN (
                                            'deleted',
                                            'hidden',
                                            'inactive',
                                            'void'
                                          ) 
                                          AND ed.STATUS NOT IN (
                                            'deleted',
                                            'hidden',
                                            'inactive',
                                            'void'
                                          ) 
                                          AND DATE(e.discharge_date) BETWEEN ".$db->qstr($first_date)."
                                          AND ".$db->qstr($last_date)."
                                          AND ed.type_nr IN ($type_nr) 
                                          AND ed.encounter_type IN ($patient_type) 
                                          AND IF(
                                            INSTR(c.diagnosis_code, '.'),
                                            SUBSTR(
                                              c.diagnosis_code,
                                              1,
                                              IF(
                                                INSTR(c.diagnosis_code, '.'),
                                                INSTR(c.diagnosis_code, '.') - 1,
                                                0
                                              )
                                            ),
                                            c.diagnosis_code
                                          ) REGEXP '^[[:alpha:]][[:digit:]]' 
                                        GROUP BY d.icd_10 
                                        ORDER BY d.diagnosis";

    $obstetrical_delivery_phic = $db->GetAll($obstetrical_delivery_phic_sql);

    if ($obstetrical_delivery_phic) {
        foreach ($obstetrical_delivery_phic as $value) {
            $phic_normal_delivery += $value["normal"];
            $phic_caesarian_delivery += $value["caesarean"];
        }

        $total_obstetrical_delivery_phic = $phic_normal_delivery + $phic_caesarian_delivery;

        $params->put("OBS-NHIP_0", (string)$total_obstetrical_delivery_phic);
        $params->put("OBS-NHIP_1", (string)$phic_caesarian_delivery);
    } else {
        $params->put("OBS-NHIP_0", NO_DATA);
        $params->put("OBS-NHIP_1", NO_DATA);
    }

// F. OBSTETRICAL PROCEDURES (TOTAL NUMBER OF DELIVERIES) = NON-PHIC
    $obstetrical_delivery_non_phic_sql = "SELECT 
                                              SUM(
                                                CASE
                                                  WHEN NOT EXISTS (SELECT ins.hcare_id FROM seg_encounter_insurance AS ins WHERE ins.encounter_nr = e.encounter_nr AND ins.hcare_id = '18')
                                                  AND INSTR(ed.CODE, ".$db->qstr(NORMAL_DELIVERY_CODE).")
                                                  THEN 1 
                                                  ELSE 0 
                                                END
                                              ) AS normal,
                                              SUM(
                                                CASE
                                                  WHEN NOT EXISTS (SELECT ins.hcare_id FROM seg_encounter_insurance AS ins WHERE ins.encounter_nr = e.encounter_nr AND ins.hcare_id = '18')
                                                  AND INSTR(c.diagnosis_code, ".$db->qstr(CAESAREAN_DELIVERY_CODE).") 
                                                  THEN 1 
                                                  ELSE 0 
                                                END
                                              ) AS caesarean 
                                            FROM
                                              care_encounter_diagnosis AS ed 
                                              INNER JOIN care_encounter AS e 
                                                ON e.encounter_nr = ed.encounter_nr 
                                              INNER JOIN care_icd10_en AS c 
                                                ON c.diagnosis_code = ed.CODE 
                                              INNER JOIN care_person AS p 
                                                ON p.pid = e.pid 
                                              INNER JOIN seg_icd_10_deliveries AS d 
                                                ON (
                                                  d.icd_10 = ed.CODE 
                                                  OR d.icd_10 = 
                                                  (SELECT 
                                                    IF(
                                                      INSTR(ed.CODE, '.'),
                                                      SUBSTRING(ed.CODE, 1, 3),
                                                      IF(
                                                        INSTR(ed.CODE, '/'),
                                                        SUBSTRING(ed.CODE, 1, 5),
                                                        IF(
                                                          INSTR(ed.CODE, ','),
                                                          SUBSTRING(ed.CODE, 1, 3),
                                                          IF(
                                                            INSTR(ed.CODE, '-'),
                                                            SUBSTRING(ed.CODE, 1, 3),
                                                            ed.CODE
                                                          )
                                                        )
                                                      )
                                                    ))
                                                ) 
                                            WHERE e.STATUS NOT IN (
                                                'deleted',
                                                'hidden',
                                                'inactive',
                                                'void'
                                              ) 
                                              AND ed.STATUS NOT IN (
                                                'deleted',
                                                'hidden',
                                                'inactive',
                                                'void'
                                              ) 
                                              AND DATE(e.discharge_date) BETWEEN ".$db->qstr($first_date)." 
                                              AND ".$db->qstr($last_date)." 
                                              AND ed.type_nr IN ($type_nr) 
                                              AND ed.encounter_type IN ($patient_type) 
                                              AND IF(
                                                INSTR(c.diagnosis_code, '.'),
                                                SUBSTR(
                                                  c.diagnosis_code,
                                                  1,
                                                  IF(
                                                    INSTR(c.diagnosis_code, '.'),
                                                    INSTR(c.diagnosis_code, '.') - 1,
                                                    0
                                                  )
                                                ),
                                                c.diagnosis_code
                                              ) REGEXP '^[[:alpha:]][[:digit:]]' 
                                            GROUP BY d.icd_10 
                                            ORDER BY d.diagnosis";

    $obstetrical_delivery_non_phic = $db->GetAll($obstetrical_delivery_non_phic_sql);

    if ($obstetrical_delivery_non_phic) {
        foreach ($obstetrical_delivery_non_phic as $value) {
            $non_phic_normal_delivery += $value["normal"];
            $non_phic_caesarian_delivery += $value["caesarean"];
        }

        $total_obstetrical_delivery_non_phic = $non_phic_normal_delivery + $non_phic_caesarian_delivery;

        $params->put("OBS-NONNHIP_0", (string)$total_obstetrical_delivery_non_phic);
        $params->put("OBS-NONNHIP_1", (string)$non_phic_caesarian_delivery);
    } else {
        $params->put("OBS-NONNHIP_0", NO_DATA);
        $params->put("OBS-NONNHIP_1", NO_DATA);
    }

// INDICATIONS FOR CS
    $indications_for_cs_sql = "SELECT
                                c.`diagnosis_code` AS diag_codex,/*added By MARK Dec 14, 2016*/
                                  c.description,
                                  SUM(
                                    CASE
                                      WHEN EXISTS (SELECT ins.hcare_id FROM seg_encounter_insurance AS ins WHERE ins.encounter_nr = e.encounter_nr AND ins.hcare_id = '18')
                                      AND INSTR(c.diagnosis_code, ".$db->qstr(CAESAREAN_DELIVERY_CODE).") 
                                      THEN 1 
                                      ELSE 0 
                                    END
                                  ) AS caesarean_phic,
                                  SUM(
                                    CASE
                                      WHEN NOT EXISTS (SELECT ins.hcare_id FROM seg_encounter_insurance AS ins WHERE ins.encounter_nr = e.encounter_nr AND ins.hcare_id = '18')
                                      AND INSTR(c.diagnosis_code, ".$db->qstr(CAESAREAN_DELIVERY_CODE).") 
                                      THEN 1 
                                      ELSE 0 
                                    END
                                  ) AS caesarean_non_phic 
                                FROM
                                  care_encounter_diagnosis AS ed 
                                  INNER JOIN care_encounter AS e 
                                    ON e.encounter_nr = ed.encounter_nr 
                                  INNER JOIN care_icd10_en AS c 
                                    ON c.diagnosis_code = ed.CODE 
                                  INNER JOIN care_person AS p 
                                    ON p.pid = e.pid 
                                  INNER JOIN seg_icd_10_deliveries AS d 
                                    ON (
                                      d.icd_10 = ed.CODE 
                                      OR d.icd_10 = 
                                      (SELECT 
                                        IF(
                                          INSTR(ed.CODE, '.'),
                                          SUBSTRING(ed.CODE, 1, 3),
                                          IF(
                                            INSTR(ed.CODE, '/'),
                                            SUBSTRING(ed.CODE, 1, 5),
                                            IF(
                                              INSTR(ed.CODE, ','),
                                              SUBSTRING(ed.CODE, 1, 3),
                                              IF(
                                                INSTR(ed.CODE, '-'),
                                                SUBSTRING(ed.CODE, 1, 3),
                                                ed.CODE
                                              )
                                            )
                                          )
                                        ))
                                    ) 
                                WHERE e.STATUS NOT IN (
                                    'deleted',
                                    'hidden',
                                    'inactive',
                                    'void'
                                  ) 
                                  AND ed.STATUS NOT IN (
                                    'deleted',
                                    'hidden',
                                    'inactive',
                                    'void'
                                  ) 
                                  AND DATE(e.discharge_date) BETWEEN ".$db->qstr($first_date)." 
                                  AND ".$db->qstr($last_date)." 
                                  AND ed.type_nr IN ($type_nr) 
                                  AND ed.encounter_type IN ($patient_type) 
                                  AND IF(
                                    INSTR(c.diagnosis_code, '.'),
                                    SUBSTR(
                                      c.diagnosis_code,
                                      1,
                                      IF(
                                        INSTR(c.diagnosis_code, '.'),
                                        INSTR(c.diagnosis_code, '.') - 1,
                                        0
                                      )
                                    ),
                                    c.diagnosis_code
                                  ) REGEXP '^[[:alpha:]][[:digit:]]' 
                                GROUP BY d.icd_10 
                                ORDER BY d.diagnosis 
                                LIMIT 5";
                                // die($indications_for_cs_sql);
    $indications_for_cs = $db->GetAll($indications_for_cs_sql);

    if ($indications_for_cs) {
        $arr_len = sizeof($indications_for_cs);

        foreach($indications_for_cs as $key => $value) {
            if (!empty($value['description'])) {
                $arr_indications_for_cs[] = array(
                                                  "description"        =>  $value["diag_codex"]." - ".$value["description"],
                                                  "caesarean_phic"    => $value["caesarean_phic"],
                                                  "caesarean_non_phic" => $value["caesarean_non_phic"]
                                                );
            } else {
                $check_arr_len++;
            }
        }

        if ($arr_len == $check_arr_len) {
            for ($i = 0; $i < 5; $i++) {
                $params->put("CS_".$i, $counter.". "."                ".NO_DATA);
                $params->put("CS-NHIP_".$i, NO_DATA);
                $params->put("CS-NONNHIP_".$i, NO_DATA);
                $counter++;
            }
        } else {
            for ($i = 0; $i < 5; $i++) {
                $params->put("CS_".$i, $counter.". ".$arr_indications_for_cs[$i]["description"]);
                $params->put("CS-NHIP_".$i, $arr_indications_for_cs[$i]["caesarean_phic"]);
                $params->put("CS-NONNHIP_".$i, $arr_indications_for_cs[$i]["caesarean_non_phic"]);
                $counter++;
            }
        }
    } else {
        for ($i = 0; $i < 5; $i++) {
            $params->put("CS_".$i, $counter.". "."                ".NO_DATA);  
            $params->put("CS-NHIP_".$i, NO_DATA);
            $params->put("CS-NONNHIP_".$i, NO_DATA);
            $counter++;
        }
    }

    $counter = 1;
    $check_arr_len = 0;

// G. MONTHLY MORTALITY CENSUS (All Cases)
    $monthly_mortality_census_sql = "SELECT 
                                c.`diagnosis_code` AS MORTALITY_code,/*added By MARK Dec 14, 2016*/
                                      c.description,
                                      SUM(
                                        CASE
                                          WHEN EXISTS (SELECT ins.hcare_id FROM seg_encounter_insurance AS ins WHERE ins.encounter_nr = e.encounter_nr AND ins.hcare_id = '18')
                                          THEN 1 
                                          ELSE 0 
                                        END
                                      ) AS phic_occurrence,
                                      SUM(
                                        CASE
                                          WHEN NOT EXISTS (SELECT ins.hcare_id FROM seg_encounter_insurance AS ins WHERE ins.encounter_nr = e.encounter_nr AND ins.hcare_id = '18')
                                          THEN 1 
                                          ELSE 0 
                                        END
                                      ) AS nonphic_occurrence 
                                    FROM
                                      care_encounter_diagnosis AS ed 
                                      INNER JOIN care_encounter AS e 
                                        ON e.encounter_nr = ed.encounter_nr 
                                      INNER JOIN care_icd10_en AS c 
                                        ON c.diagnosis_code = 
                                        (SELECT 
                                          IF(
                                            INSTR(ed.code, '.'),
                                            SUBSTRING(ed.code, 1, 3),
                                            IF(
                                              INSTR(ed.code, '/'),
                                              SUBSTRING(ed.code, 1, 5),
                                              IF(
                                                INSTR(ed.code, ','),
                                                SUBSTRING(ed.code, 1, 3),
                                                IF(
                                                  INSTR(ed.code, '-'),
                                                  SUBSTRING(ed.code, 1, 3),
                                                  ed.code
                                                )
                                              )
                                            )
                                          )) 
                                      INNER JOIN care_person AS cp 
                                        ON cp.pid = e.pid 
                                        AND cp.death_encounter_nr = e.encounter_nr 
                                      LEFT JOIN seg_encounter_result AS r 
                                        ON r.encounter_nr = e.encounter_nr 
                                    WHERE e.STATUS NOT IN (
                                        'deleted',
                                        'hidden',
                                        'inactive',
                                        'void'
                                      ) 
                                      AND ed.STATUS NOT IN (
                                        'deleted',
                                        'hidden',
                                        'inactive',
                                        'void'
                                      ) 
                                      AND r.result_code IN (4, 8, 9, 10) 
                                      AND DATE(e.discharge_date) BETWEEN ".$db->qstr($first_date)." 
                                      AND ".$db->qstr($last_date)." 
                                      AND ed.type_nr IN ($type_nr) 
                                      AND ed.encounter_type IN ($patient_type) 
                                      AND IF(
                                        INSTR(c.diagnosis_code, '.'),
                                        SUBSTR(
                                          c.diagnosis_code,
                                          1,
                                          IF(
                                            INSTR(c.diagnosis_code, '.'),
                                            INSTR(c.diagnosis_code, '.') - 1,
                                            0
                                          )
                                        ),
                                        c.diagnosis_code
                                      ) REGEXP '^[[:alpha:]][[:digit:]]' 
                                    GROUP BY 
                                      (SELECT 
                                        IF(
                                          INSTR(ed.code, '.'),
                                          SUBSTRING(ed.code, 1, 3),
                                          IF(
                                            INSTR(ed.code, '/'),
                                            SUBSTRING(ed.code, 1, 5),
                                            IF(
                                              INSTR(ed.code, ','),
                                              SUBSTRING(ed.code, 1, 3),
                                              IF(
                                                INSTR(ed.code, '-'),
                                                SUBSTRING(ed.code, 1, 3),
                                                ed.code
                                              )
                                            )
                                          )
                                        )) 
                                    ORDER BY COUNT(ed.code) DESC 
                                    LIMIT 5";
                                      // die($monthly_mortality_census_sql);
    $monthly_mortality_census = $db->GetAll($monthly_mortality_census_sql);

    if ($monthly_mortality_census) {
        $arr_len = sizeof($monthly_mortality_census);

        foreach($monthly_mortality_census as $key => $value) {
            if (!empty($value['description'])) {
                $arr_monthly_mortality_census[] = array(
                                                         "description"        => str_replace(" ","", $value["MORTALITY_code"]."- ".$value["description"]) ,
                                                         "phic_occurrence"    => $value["phic_occurrence"],
                                                         "nonphic_occurrence" => $value["nonphic_occurrence"]
                                                       );
            } else {
                $check_arr_len++;
            }
        }

        if ($arr_len == $check_arr_len) {
            for ($i = 0; $i < 5; $i++) {
                $params->put("MORTALITY_".$i, $counter.". "."                      ".NO_DATA);
                $params->put("MORTALITY-NHIP_".$i, NO_DATA);
                $params->put("MORTALITY-NONNHIP_".$i, NO_DATA);
                $counter++;
            }
        } else {
            for ($i = 0; $i < 5; $i++) {
                $params->put("MORTALITY_".$i, $counter.". ".$arr_monthly_mortality_census[$i]["description"]);
                $params->put("MORTALITY-NHIP_".$i, $arr_monthly_mortality_census[$i]["phic_occurrence"]);
                $params->put("MORTALITY-NONNHIP_".$i, $arr_monthly_mortality_census[$i]["nonphic_occurrence"]);
                $counter++;
            }
        }
    } else {
        for ($i = 0; $i < 5; $i++) {
            $params->put("MORTALITY_".$i, $counter.". "."                       ".NO_DATA);  
            $params->put("MORTALITY-NHIP_".$i, NO_DATA);
            $params->put("MORTALITY-NONNHIP_".$i, NO_DATA);
            $counter++;
        }
    }

    $counter = 1;
    $check_arr_len = 0;

// H. REFERRALS
    $sql_referral = "SELECT 
                        srr.reason,
                        SUM(
                          CASE
                            WHEN EXISTS 
                            (SELECT 
                              ins.hcare_id 
                            FROM
                              seg_encounter_insurance AS ins 
                            WHERE ins.encounter_nr = e.encounter_nr 
                              AND ins.hcare_id = '18') 
                            THEN 1 
                            ELSE 0 
                          END
                        ) AS phic_occurrence,
                        SUM(
                          CASE
                            WHEN NOT EXISTS 
                            (SELECT 
                              ins.hcare_id 
                            FROM
                              seg_encounter_insurance AS ins 
                            WHERE ins.encounter_nr = e.encounter_nr 
                              AND ins.hcare_id = '18') 
                            THEN 1 
                            ELSE 0 
                          END
                        ) AS nonphic_occurrence 
                      FROM
                        care_encounter AS e 
                        LEFT JOIN seg_encounter_disposition AS disp 
                          ON disp.encounter_nr = e.encounter_nr 
                        LEFT JOIN seg_dispositions AS disps 
                          ON disps.disp_code = disp.disp_code 
                          AND disps.disp_code IN (3, 8) 
                        LEFT JOIN seg_referral_reason AS srr 
                          ON srr.id = disp.referral_reason_id 
                      WHERE e.STATUS NOT IN (
                          'deleted',
                          'hidden',
                          'inactive',
                          'void'
                        ) 
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($first_date)." 
                        AND ".$db->qstr($last_date)." 
                        AND disp.referral_reason_id IS NOT NULL 
                        AND e.encounter_type IN ($patient_type) 
                      GROUP BY srr.reason 
                      ORDER BY COUNT(srr.reason) DESC 
                      LIMIT 5";
                      // die($sql_referral);
    $referral = $db->GetAll($sql_referral);

    if ($referral) {
        $arr_len = sizeof($referral);

        foreach($referral as $key => $value) {
            if (!empty($value['reason'])) {
                $arr_referral[] = array(
                                         "reason"             => $value["reason"],
                                         "phic_occurrence"    => $value["phic_occurrence"],
                                         "nonphic_occurrence" => $value["nonphic_occurrence"]
                                       );
            } else {
                $check_arr_len++;
            }
        }

        if ($arr_len == $check_arr_len) {
            for ($i = 0; $i < 5; $i++) {
                $params->put("REFERRAL_".$i, $counter.". "."                      ".NO_DATA);
                $params->put("REFERRAL-NHIP_".$i, NO_DATA);
                $params->put("REFERRAL-NONNHIP_".$i, NO_DATA);
                $counter++;
            }
        } else {
            for ($i = 0; $i < 5; $i++) {
                $params->put("REFERRAL_".$i, $counter.". ".$arr_referral[$i]["reason"]);
                $params->put("REFERRAL-NHIP_".$i, $arr_referral[$i]["phic_occurrence"]);
                $params->put("REFERRAL-NONNHIP_".$i, $arr_referral[$i]["nonphic_occurrence"]);
                $counter++;
            }
        }
    } else {
        for ($i = 0; $i < 5; $i++) {
            $params->put("REFERRAL_".$i, $counter.". "."                       ".NO_DATA);  
            $params->put("REFERRAL-NHIP_".$i, NO_DATA);
            $params->put("REFERRAL-NONNHIP_".$i, NO_DATA);
            $counter++;
        }
    }

    // PREPARED BY
    $prepared_by = $HTTP_SESSION_VARS['sess_user_fullname'];
    $params->put("prepared_by", $prepared_by);
    // CERTIFIED CORRECT
    $verified_by = $db->GetOne("SELECT fn_get_personell_firstname_last(personell_nr) FROM seg_signatory WHERE document_code = ".$db->qstr(MMHR_SIGNATORY));
    if ($verified_by)
      $params->put("certified_correct", $verified_by);
    else
      $params->put("certified_correct", NULL);
?>