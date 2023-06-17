<?php
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');

include_once('parameters.php');

define(NORMAL_DELIVERY_CODE, 'O80');
define(CAESARIAN_DELIVERY_CODE, 'O82');

global $db;

// PARAMETERS
$params->put("image_path", $image_path);

$rs = $db->GetAll("SELECT 
                        ed.code AS icd10code,
                        d.icd_10 AS code,
                        c.description AS descr,
                        SUM(
                          CASE
                            WHEN INSTR(c.diagnosis_code, ".$db->qstr(NORMAL_DELIVERY_CODE).") 
                            THEN 1 
                            ELSE 0 
                          END
                        ) AS normal,
                        SUM(
                          CASE
                            WHEN INSTR(c.diagnosis_code, ".$db->qstr(CAESARIAN_DELIVERY_CODE).") 
                            THEN 1 
                            ELSE 0 
                          END
                        ) AS caesarian,
                        SUM(
                          CASE
                            WHEN INSTR(c.diagnosis_code, ".$db->qstr(NORMAL_DELIVERY_CODE).") 
                            OR INSTR(c.diagnosis_code, ".$db->qstr(CAESARIAN_DELIVERY_CODE).") 
                            THEN 0 
                            ELSE 1 
                          END
                        ) AS other_deliveries 
                      FROM
                        care_encounter_diagnosis AS ed 
                        INNER JOIN care_encounter AS e 
                          ON e.encounter_nr = ed.encounter_nr 
                        INNER JOIN care_icd10_en AS c 
                          ON c.diagnosis_code = ed.code 
                        INNER JOIN care_person AS p 
                          ON p.pid = e.pid 
                        INNER JOIN seg_icd_10_deliveries AS d 
                          ON (
                            d.icd_10 = ed.code 
                            OR d.icd_10 = 
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
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)."
                        AND ".$db->qstr($to_date_format)."
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
                      ORDER BY d.diagnosis");

if ($rs) {
  foreach ($rs as $key => $value) {
    $number_normal += $value['normal'];
    $number_caesarian += $value['caesarian'];
    $number_other += $value['other_deliveries'];

    if ($value['code'] != NORMAL_DELIVERY_CODE && $value['code'] != CAESARIAN_DELIVERY_CODE) {
      $other_icd_codes = strpos($code_other, $value['code']);

      if ($other_icd_codes) {
        $code_other = $code_other;
      } else {
        $code_other .= $value['icd10code'].", ";
      } 
    } elseif ($value['code'] == CAESARIAN_DELIVERY_CODE) {
      $caesarian_icd_codes = strpos($code_caesarian, $value['code']);

      if ($caesarian_icd_codes) {
        $code_caesarian = $code_caesarian;
      } else {
        $code_caesarian .= $value['icd10code'].", ";
      } 
    }
  }

  if ($number_normal == 0) {
    $code_normal = NULL;
  } else {
    $code_normal = NORMAL_DELIVERY_CODE.", ";
  }

  $number_facility = (int) $number_normal + (int) $number_caesarian + (int) $number_other;
  $code_facility = rtrim($code_normal.$code_caesarian.$code_other, ", ");
}

// DATA
$data[0] = array(
                  "number_facility"   => $number_facility,
                  "code_facility"     => $code_facility,
                  "number_normal"     => $number_normal,
                  "code_normal"       => rtrim($code_normal, ", "),
                  "number_caesarian"  => $number_caesarian,
                  "code_caesarian"    => rtrim($code_caesarian, ", "),
                  "number_other"      => $number_other,
                  "code_other"        => rtrim($code_other, ", ")
                );

