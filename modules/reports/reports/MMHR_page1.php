<?php
//created by daryl
//for MMHR page 1
if($hosp_type == "PH")
  $category = "LEVEL 1";
elseif($hosp_type == "SH")
  $category = "LEVEL 2";
elseif($hosp_type == "TH")
  $category = "LEVEL 3";
    
$monthNum  = $mmhr_month;
$dateObj   = DateTime::createFromFormat('!m', $monthNum);
$monthName = $dateObj->format('F');
$date_from = $mmhr_year."-".$mmhr_month."-01";
$date_to = $mmhr_year."-".$mmhr_month."-31";

//set parameters for reports
$params->put("rep_title", mb_strtoupper($report_title));
$params->put("month_year", "For the Month of ".strtoupper($monthName)." ".strtoupper($mmhr_year));

$params->put("hosp_name", mb_strtoupper($hosp_name));
$params->put("hosp_address", mb_strtoupper($hosp_addr1));
$params->put("country", mb_strtoupper($hosp_addr1));
$params->put("hosp_street", mb_strtoupper($hosp_street));
$params->put("hosp_mun", ($hosp_mun));
$params->put("hosp_province", ($hosp_prov));
$params->put("hosp_zipcode", ($hosp_zipcode));
$params->put("hosp_region", ($hosp_region));
$params->put("bed_capacity", ($bed_capacity));
$params->put("hosp_category", ($category));
$params->put("phic_beds", ($bed_capacity));
$params->put("doh_beds", ($bed_capacity));

$sql_1 = "SELECT cif.`accreditation_no`
                        FROM care_insurance_firm cif
                        WHERE cif.`hcare_id` = ".$db->qstr(hcare_id)."
                        ";
             
$accreditation_no = $db->GetOne($sql_1);

$params->put("acc_no", $accreditation_no);

for ($i=1; $i <= 31 ; $i++) { 
  $date_date = $mmhr_year."-".$mmhr_month."-".$i;
       $sql_admitted = "SELECT  SUM( CASE WHEN EXISTS  (SELECT encounter_nr  FROM `seg_encounter_insurance` b  WHERE b.encounter_nr = a.`encounter_nr`) 
                                    THEN 1 
                                    ELSE 0 
                                  END
                                ) nhip,
                                SUM( CASE WHEN NOT EXISTS  (SELECT  encounter_nr  FROM  `seg_encounter_insurance` b  WHERE b.encounter_nr = a.`encounter_nr`) 
                                    THEN 1 
                                    ELSE 0 
                                  END
                                ) nonnhip 
                    FROM
                      care_encounter a 
                    WHERE a.`discharge_date` > DATE('".$date_date."')
                      AND a.`admission_dt` <= DATE('".$date_date."')
                      AND a.encounter_type IN (3, 4) 
                      AND NOT UPPER(TRIM(a.encounter_status)) IN ('CANCELLED', 'DELETED')"; 
                        #echo $sql_admitted;exit();
    $admitd = $db->GetRow($sql_admitted);

            $sql_discharge = "SELECT 
                                  SUM(CASE WHEN EXISTS (SELECT encounter_nr FROM `seg_encounter_insurance` b WHERE b.encounter_nr = a.`encounter_nr`) 
                                      THEN 1 
                                      ELSE 0 
                                    END
                                  ) nhip,
                                  SUM(CASE WHEN NOT EXISTS (SELECT encounter_nr FROM `seg_encounter_insurance` b WHERE b.encounter_nr = a.`encounter_nr`) 
                                      THEN 1 
                                      ELSE 0 
                                    END
                                  ) nonnhip 
                                FROM
                                  care_encounter a 
                                WHERE DATE(a.`discharge_date`) = DATE('".$date_date."') 
                                  AND a.encounter_type IN (3, 4)
                                  AND NOT UPPER(TRIM(a.encounter_status)) IN ('CANCELLED','DELETED')" ;      

    $discharged = $db->GetRow($sql_discharge);
  
  $total_admit_nhip += $admitd['nhip'];
  $total_admit_nonnhip += $admitd['nonnhip'];
  $total_admit_total += $admitd['nhip'] + $admitd['nonnhip'];

  $total_discharged_nhip += $discharged['nhip'];
  $total_discharged_nonnhip += $discharged['nonnhip'];
  $total_discharged_total += $discharged['nhip'] + $discharged['nonnhip'];

  $data[$i] = array( 
                     'date_day'           => $i,
                        'admit_nhip' => $admitd['nhip'] ? $admitd['nhip'] : "0",
                        'admit_non-nhip' => $admitd['nonnhip'] ? $admitd['nonnhip'] : "0",
                     'admit_total'        => $admitd['nhip'] + $admitd['nonnhip'],
                        'discharge_nhip' => $discharged['nhip'] ? $discharged['nhip'] : "0",
                        'discharge_non-nhip' => $discharged['nonnhip'] ? $discharged['nonnhip'] : "0",
                     'discharge_total'    => $discharged['nhip'] + $discharged['nonnhip'] 
                   );
}

$days_in_month=cal_days_in_month(CAL_GREGORIAN,$mmhr_month,$mmhr_year);

$numdays_authbed = ($days_in_month * $bed_capacity);
$MBOR =  ($total_admit_total / $numdays_authbed) * 100 ;
$MNHIBOR =  ($total_admit_nhip / $numdays_authbed) * 100 ;
$ALSP =  ($total_admit_nhip / $total_discharged_nhip) * 100 ;

$params->put("total_admit_nhip", (string)$total_admit_nhip);
$params->put("total_admit_nonnhip", (string)$total_admit_nonnhip);
$params->put("total_admit_total", (string)$total_admit_total);

$params->put("total_discharge_nhip", (string)$total_discharged_nhip);
$params->put("total_discharge_nonnhip", (string)$total_discharged_nonnhip);
$params->put("total_discharge_total", (string)$total_discharged_total);

$sql_newborn = "SELECT
                  cp.date_reg,
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
                  ) AS nhip,
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
                  ) AS nonnhip 
                FROM
                  care_encounter AS e 
                  LEFT JOIN care_person AS cp 
                    ON cp.pid = e.pid 
                WHERE DATE(cp.date_reg) BETWEEN ".$db->qstr($date_from)."
                  AND ".$db->qstr($date_to)." 
                  AND cp.fromtemp = 1 
                  AND NOT UPPER(TRIM(e.encounter_status)) IN ('CANCELLED', 'DELETED')
                  GROUP BY cp.date_reg";
                  
$newborn = $db->GetRow($sql_newborn);

$newborn['nhip'] = $newborn['nhip'] ? $newborn['nhip'] : 0;
$newborn['nonnhip'] = $newborn['nonnhip'] ? $newborn['nonnhip'] : 0;
$total_newborn_total = $newborn['nhip'] + $newborn['nonnhip'];

$params->put("total_newborn_nhip", (string)$newborn['nhip']);
$params->put("total_newborn_nonnhip", (string)$newborn['nonnhip']);
$params->put("total_newborn_total", (string)$total_newborn_total);
 
$params->put("MBOR", number_format($MBOR,2)." %");
$params->put("MNHIBOR", number_format($MNHIBOR,2)." %");
$params->put("ALSP", number_format($ALSP,2)." %");

?>