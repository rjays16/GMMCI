<?php
    
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    include_once($root_path.'include/care_api_classes/reports/JasperReport.php');
    $jasper = new JasperReport();
    $objInfo = new Hospital_Admin();
    
    if ($row = $objInfo->getAllHospitalInfo()) {
        $hosp_agency = strtoupper($row['hosp_agency']);
        $hosp_name   = strtoupper($row['hosp_name']);
        $hosp_addr1   = strtoupper($row['hosp_addr1']);
        $hosp_country   = strtoupper($row['hosp_country']);
#added by daryl
        $hosp_prov   = strtoupper($row['prov_name']);
        $hosp_mun   = strtoupper($row['mun_name']);
        $hosp_street   = strtoupper($row['brgy_name']);
        $hosp_region   = strtoupper($row['region_name']);
        $hosp_type   = strtoupper($row['hosp_type']);
        $bed_capacity   = strtoupper($row['bed_capacity']);
        $hosp_zipcode   = strtoupper($row['zip_code']);
 #ended by daryl
    }else {
        $hosp_country = "Republic of the Philippines";
        $hosp_agency  = "DEPARTMENT OF HEALTH";
        $hosp_name    = "DAVAO MEDICAL CENTER";
        $hosp_addr1   = "JICA Bldg., JP Laurel Avenue, Davao City";
    }
        
    $surgical_type = ($param == "" ) ? $surgical_type = "Major and Minor" : $surgical_type = "";
    // $image_path = 'C:/xampp/tomcat/webapps/JavaBridge/resource/images/';
    $image_path = java_resource.'images/';
        
    $image_path = $jasper->getLogoPath();
    #get report description

    $sql = "SELECT rep_description, exclusive_opd_er, exclusive_death FROM seg_rep_templates_registry 
            WHERE rep_script=".$db->qstr($report_name)." AND is_active=1";
    #$report_title = $db->GetOne($sql);
    $report_info = $db->GetRow($sql);
    $report_title = $report_info['rep_description'];
    $exclusive_opd_er = $report_info['exclusive_opd_er'];
    $exclusive_death = $report_info['exclusive_death'];
    
    #additional parameters
    $paramsarr = explode(",",$param); 
    
    $with_ptype = 0;
    $with_icd10_class = 0;
    $with_surgery = 0;
    $with_dept = 0;
    $with_area = 0;
    $with_location = 0;
    $with_brgy = 0;
    $with_mun = 0;
    $with_prov = 0;
    $with_date_based = 0;
    $with_phic = 0;
    $with_status = 0;
    $with_mode_chart = 0;
    $for_all = false;
    $less_48H = false;
    $greater_48H = false;
    $limit = '10';
    
    if (count($paramsarr)){
        while (list($key,$val) = each($paramsarr))  {
            $val_arr = explode("--", trim($val));
            
            $id = $val_arr[0];
            $value = $val_arr[1];
            
            $param_id = substr($id, 6);
            
          //added by daryl
            //for Mandatory Monthly Hospital Report
            if($param_id=='MMHR_month'){
                $mmhr_month = $value;
            }
            if($param_id=='MMHR_year'){
                $mmhr_year = $value;
            }
          //endd by daryl
            #for beginning census
            if($param_id=='beg_census'){
                $with_beg_census = 1;
                $initial_census = $value;
            }
            
            #for no of holidays
            if($param_id=='no_holidays'){
                $with_no_holidays = 1;
                $no_holidays = $value;
            }
            
            # based date for report period
            if ($param_id=='date_based'){
                $with_date_based = 1;
                if ($value=='admission'){
                    $date_based = 'e.admission_dt';
                    $date_based_label = 'Based on Admission Date';
                }elseif ($value=='consultation'){
                    $date_based = 'e.encounter_date';
                    $date_based_label = 'Based on Consultation Date';
                }elseif ($value=='discharged'){
                    $date_based = 'e.discharge_date';
                    $date_based_label = 'Based on Discharged Date';
                }
            }
            
            # hours of death
            #'all-All','less48H-Less Than 48 Hours','48Handup-48 Hours and up'
            if ($param_id=='death_hours'){
                $with_death_hours = 1;
                if ($value=='all'){
                    $for_all = true;
                    $cond_death_hours = "AND (DATEDIFF(
                                           (IF(p.death_date='0000-00-00',e.discharge_date, p.death_date)),           
                                            DATE(e.admission_dt))<2)";
                    $death_hours_label = 'All Deaths';
                }elseif ($value=='less48H'){
                    $less_48H = true;
                    $cond_death_hours = "AND (DATEDIFF(
                                           (IF(p.death_date='0000-00-00',e.discharge_date, p.death_date)),           
                                            DATE(e.admission_dt))<2)";
                    $death_hours_label = 'Deaths Less Than 48 Hours';
                }elseif ($value=='48Handup'){
                    $greater_48H = true;
                    $cond_death_hours = "AND (DATEDIFF(
                                           (IF(p.death_date='0000-00-00',e.discharge_date, p.death_date)),           
                                            DATE(e.admission_dt))>=2)";
                    $death_hours_label = 'Deaths with 48 Hours and up';
                }
            }
            
            #patient type
            if ($param_id=='patienttype'){
                $with_ptype = 1;
                if ($value=='all'){
                    $patient_type = '1,2,3,4';
                    $patient_type_label = "ALL PATIENTS";            
                }elseif ($value=='ipd'){
                    $patient_type = '3,4';            
                    $patient_type_label = "INPATIENTS";            
                }elseif ($value=='er'){
                    $patient_type = '1';
                    $patient_type_label = "ER PATIENTS";            
                }elseif ($value=='opd'){
                    $patient_type = '2';     
                    $patient_type_label = "OUTPATIENTS";                       
                }else{
                    #walkin
                    $patient_type = '0';      
                    $patient_type_label = "WALKIN PATIENTS";                          
                }    
            }
            
            #icd 10 classification 
            if ($param_id=='type_nr'){
                $with_icd10_class = 1;
                if ($value=='all'){
                   $type_nr = '0,1';
                   $icd_class = "Primary and Secondary";
                }elseif ($value=='1'){   
                   $type_nr = '1';
                   $icd_class = "Primary"; 
                }else{   
                   $type_nr = '0';
                   $icd_class = "Secondary";   
                }    
            }
            
            #minor and major surgery
            if ($param_id=='type_surgery'){
                $with_surgery = 1;
                if ($value=='all'){
                   $cond_surgery = '';
                   $sub_caption = "All Minor and Major Operations";
                   $surgical_type = "Major & Minor";
                }elseif ($value=='minor'){   
                   $cond_surgery = " AND c.rvu < 30 ";
                   $sub_caption = "Minor Operations (below 30 RVU)";
                   $surgical_type = "Minor";
                }elseif ($value=='major'){   
                   $cond_surgery = " AND c.rvu >= 30 ";
                   $sub_caption = "Major Operations (30 and above RVU)";
                   $surgical_type = "Major";
                }    
            }
            
            #phic membeship classification
            if ($param_id=='classification'){
                $with_phic = 1;
                if ($value=='all'){
                   $cond_classification = '';
                   $sub_caption = "All PHIC and NPHIC Patients";
                }elseif ($value=='phic'){   
                   $cond_classification = " AND ins.hcare_id=18 ";
                   $sub_caption = "All PHIC Patients";
                }elseif ($value=='nphic'){   
                   $cond_classification = " AND (ins.hcare_id<>18 OR ins.hcare_id IS NULL) ";
                   $sub_caption = "All NPHIC Patients";
                }
            }
            
            #mode of report
            if ($param_id=='mode'){
                $with_mode_chart = 1;
                if ($value=='all'){
                   $cond_mode_chart = '';
                   $sub_caption = "All Patients";
                }elseif ($value=='notreceived'){   
                   $cond_mode_chart = " AND e.received_date IS NULL ";
                   $sub_caption = "All PHIC Patients with Chart that is Not Yet Received";
                }elseif ($value=='received'){   
                   $cond_mode_chart = " AND e.received_date IS NOT NULL ";
                   $sub_caption = "All PHIC Patients with Chart that is already Received";
                }
            }
            
            #patient's status
            if ($param_id=='status'){
                $with_status = 1;
                if ($value=='all'){
                   $cond_status = '';
                   $sub_caption = "All Patients";
                }elseif ($value=='died'){   
                   $cond_status = " AND ser.result_code IN (4,8) ";
                   $sub_caption = "All Died Patients";
                }elseif ($value=='alive'){   
                   $cond_status = " AND ser.result_code NOT IN (4,8) ";
                   $sub_caption = "All Still Alive Patients";
                }
            } 
            
            #for dept or clinic
            if ($param_id=='dept'){
                $with_dept = 1;
                switch($value){
                    case 'dental' :
                                $dept_label = "Dental";
                                $dept_list = '134';
                                break;
                                
                    case 'derma' :
                                $dept_label = "Dermatology";
                                $dept_list = '116';
                                break;            
                                
                    case 'ent' :
                                $dept_label = "ENT-HNS";
                                $dept_list = '136';
                                break;            
                                
                    case 'famed' :
                                $dept_label = "Family Medicine";
                                $dept_list = '133';
                                break;            
                                
                    case 'gyne' :
                                $dept_label = "Gynecology";
                                $dept_list = '124';
                                break;            
                                
                    case 'im' :
                                $dept_label = "Internal Medicine";
                                $dept_list = '154,104';
                                break;                        
                                
                    case 'med' :
                                $dept_label = "Medicines (Family Medicine and Internal Medicine)";
                                $dept_list = '104';
                                break;                        
                             
                    case 'ob' :
                                $dept_label = "Obstetrics";
                                $dept_list = '139';
                                break;            
                                
                    case 'optha' :
                                $dept_label = "Ophthalmology";
                                $dept_list = '131';
                                break;            
                                
                    case 'ortho' :
                                $dept_label = "Orthopedics";
                                $dept_list = '141';
                                break;            
                    case 'pedia' :
                                $dept_label = "Pediatrics";
                                $dept_list = '125';
                                break;                                    
                                
                    case 'surgery' :
                                $dept_label = "Surgery";
                                $dept_list = '117';
                                break;         
                    default :
                                $dept_label = "All Department";
                                $dept_list = '';
                                break;                                                            
                     
                }                                     
                 
                 if ($value){
                    $enc_dept_cond = " AND (e.current_dept_nr IN ($dept_list) \n".
                                        " OR e.current_dept_nr IN ( \n".
                                            " SELECT nr FROM care_department AS d WHERE d.parent_dept_nr IN ($dept_list))) ";                             
                 
                    $census_dept_cond = " AND IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr) IN ($dept_list) ";
                 }else{
                    $enc_dept_cond = " ";
                    $census_dept_cond = " "; 
                 }                                                      
                
                
                #for mortality tabulation code
                if ($value=='pedia'){
                    $table_tab_code = 'seg_icd_10_mortality_pedia_condensed_tabular';
                }else{
                    $table_tab_code = 'seg_icd_10_mortality_condensed_tabular';
                }
                
                            
            }
            
            # for demographic area
            if ($param_id=='location'){
                $with_area = 1;
                $with_location = 1;
                $field_with_municity = 0;
                if ($value=='all'){
                    $loc_area = 'All from Region XI excluding Davao del Sur';
                    $loc_cond = " AND sr.region_name='Region XI' \n
                                   AND sp.prov_name!='DAVAO DEL SUR' \n"; 
                    $field_with_municity = 0;               
                }elseif ($value=='within'){
                    $loc_area = 'Within Davao Del Sur (Davao City is included)';
                    $loc_cond = " AND sr.region_name='Region XI'
                                  AND sp.prov_name='DAVAO DEL SUR' \n";
                    $field_with_municity = 1;              
                }elseif ($value=='withinexcept'){
                    $loc_area = 'Within Davao Del Sur (except Davao City)';
                    $loc_cond = " AND sr.region_name='Region XI'
                                  AND sp.prov_name='DAVAO DEL SUR' 
                                  AND mun.mun_name <> 'DAVAO CITY' \n";
                    $field_with_municity = 1;              
                }elseif ($value=='withincity'){
                    $loc_area = 'Within Davao City';
                    $loc_cond = " AND sr.region_name='Region XI'
                                  AND sp.prov_name='DAVAO DEL SUR' 
                                  AND mun.mun_name = 'DAVAO CITY' \n";                                                                          
                    $field_with_municity = 1;              
                }elseif ($value=='outside'){
                    $loc_area = 'Outside Region XI';
                    $loc_cond = " AND sr.region_name!='Region XI' \n";
                    $field_with_municity = 0;
                }elseif ($value=='both'){
                    #all region
                    $loc_area = 'Within and Outside of Region XI';
                    $loc_cond = " ";
                    $field_with_municity = 0;
                }
            }
            
            #for barangay
            if ($param_id=='brgynr'){
                $with_area = 1;
                $with_brgy = 1;
                $sql_brgy = "SELECT b.brgy_name, b.mun_nr, b.CODE AS brgy_code
                             FROM seg_barangays b
                             WHERE b.brgy_nr=".$db->qstr($value);             
                                
                $row = $db->GetRow($sql_brgy);
                $brgy_name = trim($row['brgy_name']);
                
                $brgy_area = $brgy_name;
                
                #if long int and length = 9
                if ((strlen($brgy_code)==9) && (is_numeric($brgy_code)))
                    $brgy_cond = " AND sb.CODE = '$brgy_code' \n";
                else
                    $brgy_cond = " AND sb.brgy_nr = ".$db->qstr($value)." \n";
            } 
            #for Account Type
            if($param_id=='accnt_type'){
                $acct_type  = $value;

            }
            if($param_id=='encoder'){
                $encoder  = $value;
                
            }
               
            #for municity
            if ($param_id=='munnr'){
                $with_area = 1;
                $with_mun = 1;
                $sql_mun = "SELECT m.mun_name, m.CODE AS mun_code, m.mun_nr
                             FROM seg_municity m 
                             WHERE m.mun_nr=".$db->qstr($value);             
                                
                $row = $db->GetRow($sql_mun);
                $mun_name = trim($row['mun_name']);
                $mun_code = trim($row['mun_code']);
                
                $mun_area = $mun_name;
                
                #if long int and length = 9
                if ((strlen($mun_code)==9) && (is_numeric($mun_code)))
                    $mun_cond = " AND sm.CODE = '$mun_code' \n";
                else
                    $mun_cond = " AND sm.mun_nr = ".$db->qstr($value)." \n";
            }
            #for province
            if ($param_id=='provnr'){
                $with_area = 1;
                $prov = 1;
                $sql_prov = "SELECT p.prov_name, p.CODE AS prov_code, p.prov_nr
                             FROM seg_provinces p 
                             WHERE p.prov_nr=".$db->qstr($value);             
                                
                $row = $db->GetRow($sql_prov);
                $prov_name = trim($row['prov_name']);
                $prov_code = trim($row['prov_code']);
                
                $prov_area = $prov_name;
                
                #if long int and length = 9
                if ((strlen($prov_code)==9) && (is_numeric($prov_code)))
                    $prov_cond = " AND sp.CODE = '$prov_code' \n";
                else
                    $prov_cond = " AND sp.prov_nr = ".$db->qstr($value)." \n";
            } 
            
            #area type
            if ($param_id=='area'){
                $with_area_type = 1;
                if ($value=='ipd'){
                   #query for Discharges based on Accommodation 
                   $query_sub_accom = "SELECT d.name_formal AS Type_Of_Service,
                                    SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_non_phic,
                                    SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id IN (1,2,4,6,8) OR em.memcategory_id IS NULL) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_phic_memdep,
                                    SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_phic_indigent,
                                    SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id IN (3,7) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_phic_owwa,
                                    SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_non_phic,
                                    SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id IN (1,2,4,6,8) OR em.memcategory_id IS NULL) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_phic_memdep,
                                    SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND (w.accomodation_type=1) THEN 1 ELSE 0 END) AS charity_phic_indigent,
                                    SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id IN (3,7) AND (w.accomodation_type=1) THEN 1 ELSE 0 END) AS charity_phic_owwa,
                                    SUM(DATEDIFF(e.discharge_date,e.admission_dt)+1) AS total_len_stay
                                    FROM care_department AS d
                                    INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                    INNER JOIN care_person AS cp ON cp.pid = e.pid
                                    LEFT JOIN care_ward AS w ON e.current_ward_nr=w.nr
                                    LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
                                    LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid
                                    LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
                                    LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id
                                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                    AND e.discharge_date IS NOT NULL
                                    AND e.encounter_type IN (3,4)";
                   
                   #query for discharges based on result of treatment                 
                   $query_sub_result = "SELECT d.name_formal AS Type_Of_Service,
                                    SUM(CASE WHEN (sd.disp_code IN (1,6) AND (sr.result_code NOT IN (4,8,9,10) OR sr.result_code IS NULL)) THEN 1 ELSE 0 END) AS disp_admit_opd,
                                    SUM(CASE WHEN (sd.disp_code IN (2,7) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_disch,
                                    SUM(CASE WHEN (sd.disp_code IN (4, 9) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_hama,
                                    SUM(CASE WHEN (sd.disp_code IN (5, 10) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_absc,
                                    SUM(CASE WHEN (sd.disp_code IN (3,8) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_trans,
                                    SUM(CASE WHEN (sd.disp_code IS NULL ) THEN 1 ELSE 0 END) AS disp_none, 
                                    SUM(CASE WHEN (sr.result_code IN (1,5)) THEN 1 ELSE 0 END) AS total_rec,
                                    SUM(CASE WHEN (sr.result_code IN (2,6)) THEN 1 ELSE 0 END) AS total_imp,
                                    SUM(CASE WHEN (sr.result_code IN (3,7)) THEN 1 ELSE 0 END) AS total_unimp,
                                    SUM(CASE WHEN (sr.result_code IS NULL) THEN 1 ELSE 0 END) AS total_noresult,
                                    SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                        AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                        DATE(e.admission_dt))<2)) THEN 1 ELSE 0 END) AS deathbelow48,
                                    SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                        AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                        DATE(e.admission_dt))>=2)) THEN 1 ELSE 0 END) AS deathabove48
                                    FROM care_department AS d
                                    INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                    LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                                    LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                                    INNER JOIN care_person AS cp ON cp.pid = e.pid
                                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                    AND e.discharge_date IS NOT NULL
                                    AND e.encounter_type IN (3,4)";
                                    
                   $area_type = "Inpatient";
                   $column_name_disp = "No Disp";
                   $column_name_ave = "Admission";
                   $ave_patient_type = "3,4";
                   $ave_based_date = "e.admission_dt";
                }elseif ($value=='er'){   
                   #query for Discharges based on Accommodation  
                   $query_sub_accom = "SELECT d.name_formal AS Type_Of_Service,
                                        SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) THEN 1 ELSE 0 END) AS pay_non_phic,
                                        SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id IN (1,2,4,6,8) OR em.memcategory_id IS NULL) THEN 1 ELSE 0 END) AS pay_phic_memdep,
                                        SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 THEN 1 ELSE 0 END) AS pay_phic_indigent,
                                        SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id IN (3,7) THEN 1 ELSE 0 END) AS pay_phic_owwa,
                                        0 AS charity_non_phic,
                                        0 AS charity_phic_memdep,
                                        0 AS charity_phic_indigent,
                                        0 AS charity_phic_owwa,
                                        SUM(DATEDIFF(e.discharge_date,e.encounter_date)) AS total_len_stay
                                        FROM care_department AS d
                                        INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                        LEFT JOIN care_ward AS w ON e.current_ward_nr=w.nr
                                        LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
                                        LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid
                                        LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
                                        LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id
                                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                        AND e.discharge_date IS NOT NULL
                                        AND e.encounter_type=1 AND e.encounter_class_nr=1";
                   #query for discharges based on result of treatment                                  
                   $query_sub_result = "SELECT d.name_formal AS Type_Of_Service,
                                        SUM(CASE WHEN (sd.disp_code IN (1,6) AND (sr.result_code NOT IN (4,8,9,10) OR sr.result_code IS NULL)) THEN 1 ELSE 0 END) AS disp_admit_opd,
                                        SUM(CASE WHEN (sd.disp_code IN (2,7) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_disch, 
                                        SUM(CASE WHEN (sd.disp_code IN (4, 9) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_hama, 
                                        SUM(CASE WHEN (sd.disp_code IN (5, 10) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_absc, 
                                        SUM(CASE WHEN (sd.disp_code IN (3,8) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_trans, 
                                        SUM(CASE WHEN (sd.disp_code IS NULL ) THEN 1 ELSE 0 END) AS disp_none, 
                                        SUM(CASE WHEN (sr.result_code IN (1,5)) THEN 1 ELSE 0 END) AS total_rec,
                                        SUM(CASE WHEN (sr.result_code IN (2,6)) THEN 1 ELSE 0 END) AS total_imp,
                                        SUM(CASE WHEN (sr.result_code IN (3,7)) THEN 1 ELSE 0 END) AS total_unimp,
                                        SUM(CASE WHEN (sr.result_code IS NULL) THEN 1 ELSE 0 END) AS total_noresult,
                                        SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                            AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                            DATE(e.encounter_date))<2)) THEN 1 ELSE 0 END) AS deathbelow48,
                                        SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                            AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                            DATE(e.encounter_date))>=2)) THEN 1 ELSE 0 END) AS deathabove48
                                        FROM care_department AS d
                                        INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                        LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                                        LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                                        INNER JOIN care_person AS cp ON cp.pid = e.pid
                                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                        AND e.discharge_date IS NOT NULL
                                        AND e.encounter_type=1 AND e.encounter_class_nr=1";
                   
                   $area_type = "ER Patient";
                   $column_name_disp = "Admitted";
                   $column_name_ave = "ER Consulation";
                   $ave_patient_type = "1";
                   $ave_based_date = "e.encounter_date";
                }    
            }  
        }
    }
    
    #default
    if (!$with_area_type){
       #query for Discharges based on Accommodation 
       $query_sub_accom = "SELECT d.name_formal AS Type_Of_Service,
                    SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_non_phic,
                    SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id IN (1,2,4,6,8) OR em.memcategory_id IS NULL) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_phic_memdep,
                    SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_phic_indigent,
                    SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id IN (3,7) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_phic_owwa,
                    SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_non_phic,
                    SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id IN (1,2,4,6,8) OR em.memcategory_id IS NULL) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_phic_memdep,
                    SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND (w.accomodation_type=1) THEN 1 ELSE 0 END) AS charity_phic_indigent,
                    SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id IN (3,7) AND (w.accomodation_type=1) THEN 1 ELSE 0 END) AS charity_phic_owwa,
                    SUM(DATEDIFF(e.discharge_date,e.admission_dt)+1) AS total_len_stay
                    FROM care_department AS d
                    INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                    INNER JOIN care_person AS cp ON cp.pid = e.pid
                    LEFT JOIN care_ward AS w ON e.current_ward_nr=w.nr
                    LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
                    LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid
                    LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
                    LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id
                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                    AND e.discharge_date IS NOT NULL
                    AND e.encounter_type IN (3,4)";
       
       #query for discharges based on result of treatment                              
       $query_sub_result = "SELECT d.name_formal AS Type_Of_Service,  
                                    SUM(CASE WHEN (sd.disp_code IN (1,6) AND (sr.result_code NOT IN (4,8,9,10) OR sr.result_code IS NULL)) THEN 1 ELSE 0 END) AS disp_admit_opd,
                                    SUM(CASE WHEN (sd.disp_code IN (2,7) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_disch,
                                    SUM(CASE WHEN (sd.disp_code IN (4, 9) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_hama,
                                    SUM(CASE WHEN (sd.disp_code IN (5, 10) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_absc,
                                    SUM(CASE WHEN (sd.disp_code IN (3,8) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_trans,
                                    SUM(CASE WHEN (sd.disp_code IS NULL ) THEN 1 ELSE 0 END) AS disp_none, 
                                    SUM(CASE WHEN (sr.result_code IN (1,5)) THEN 1 ELSE 0 END) AS total_rec,
                                    SUM(CASE WHEN (sr.result_code IN (2,6)) THEN 1 ELSE 0 END) AS total_imp,
                                    SUM(CASE WHEN (sr.result_code IN (3,7)) THEN 1 ELSE 0 END) AS total_unimp,
                                    SUM(CASE WHEN (sr.result_code IS NULL) THEN 1 ELSE 0 END) AS total_noresult,
                                    SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                        AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                        DATE(e.admission_dt))<2)) THEN 1 ELSE 0 END) AS deathbelow48,
                                    SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                        AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                        DATE(e.admission_dt))>=2)) THEN 1 ELSE 0 END) AS deathabove48
                                    FROM care_department AS d
                                    INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                    LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                                    LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                                    INNER JOIN care_person AS cp ON cp.pid = e.pid
                                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                    AND e.discharge_date IS NOT NULL
                                    AND e.encounter_type IN (3,4)"; 
                                                 
       $area_type = "Inpatient";
       $column_name_disp = "No Disp";
       $column_name_ave = "Admission";
       $ave_patient_type = "3,4";
       $ave_based_date = "e.admission_dt";
    }
    
    if (!$with_beg_census){
        $initial_census = 0;    
    }
    
    if (!$with_no_holidays){
        $no_holidays = 0;    
    }
    
    if (!$with_date_based){
        $date_based = 'e.admission_dt';
        $date_based_label = 'Based on Admission Date';
    }
    
    if(!$with_death_hours){
        $for_all = true;
        $cond_death_hours = "";
        $death_hours_label = 'All Deaths';
    }
                
    if (!$with_ptype){
        $patient_type = '3,4';
        $patient_type_label = "INPATIENTS";
    }
    
    if (!$with_icd10_class){
        $type_nr = '1';
        $icd_class = "Primary";    
    }
    
    if (!$with_surgery){
        $cond_surgery = '';
        $sub_caption = "All Minor and Major Operations";
    }
    
    if (!$with_phic){
        $cond_mode_chart = '';
        $sub_caption = "All Patients";
    }
    
    if (!$with_mode_chart){
        $cond_mode_chart = '';
        $sub_caption = "All Patients";
    }
    
    if (!$with_status){
        $cond_status = '';
        $sub_caption = "All Patients";
    }
    
    if (!$with_dept){
        $dept_label = "All Clinics/Department";
        $enc_dept_cond = '';
        $table_tab_code = 'seg_icd_10_mortality_condensed_tabular';
        $census_dept_cond = " ";
    }
    
    if (!$with_location){
        $loc_area = 'Within Davao Del Sur (Davao City is included)';
        $loc_cond = " AND sr.region_name='Region XI'
                      AND sp.prov_name='DAVAO DEL SUR' \n";
        $field_with_municity = 1;              
    }
    
    if (!$with_area){
        #DEFAULT is DAVAO CITY, DAVAO DEL SUR
        $area = "DAVAO CITY";
        # Davao City and Davao del Sur
        $area_cond = " AND sm.CODE = '112402000' \n
                       AND sp.CODE = '112400000' \n";
        
        #$area_cond = " AND sm.mun_nr = '24' \n
        #               AND sp.prov_nr = '3' \n";               
    }else{
        if ($with_brgy){
            $area = $brgy_area.", ".$mun_area.", ".$prov_area;
            $area_cond = $brgy_cond." \n ".$mun_cond." \n ".$prov_cond;
        }elseif ($with_mun){
            $area = $mun_area.", ".$prov_area;
            $area_cond = $mun_cond." \n ".$prov_cond;
        }elseif ($with_prov){
            $area = $prov_area;
            $area_cond = $prov_cond;
        }elseif($with_location){
            $area = $loc_area;
            $area_cond = $loc_cond;
        }else{
            $area = "DAVAO CITY";
            $area_cond = " AND sm.CODE = '112402000' \n
                           AND sp.CODE = '112400000' \n";
        }
    }
    
    if ($exclusive_opd_er)
      $date_based = 'e.encounter_date'; 
      
    if ($exclusive_death)
      $date_based = 'p.death_date';      
    
    #get age
    $age_bdate = "(FLOOR(IF(fn_calculate_age(DATE($date_based),p.date_birth),(fn_get_ageyr(DATE($date_based),p.date_birth)),p.age))";
    
    #get age bracket               
    $age_bracket = "SUM(CASE WHEN p.sex='m' AND ($age_bdate < 1) OR $age_bdate IS NULL)) THEN 1 ELSE 0 END) AS male_below1, 
                    SUM(CASE WHEN p.sex='f' AND ($age_bdate < 1) OR $age_bdate IS NULL)) THEN 1 ELSE 0 END) AS female_below1, 

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 1 AND 4) THEN 1 ELSE 0 END) AS male_1to4, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 1 AND 4) THEN 1 ELSE 0 END) AS female_1to4, 

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 5 AND 9) THEN 1 ELSE 0 END) AS male_5to9, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 5 AND 9) THEN 1 ELSE 0 END) AS female_5to9, 

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS male_10to14, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS female_10to14, 

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS male_15to19, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS female_15to19, 

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 20 AND 24) THEN 1 ELSE 0 END) AS male_20to24, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 20 AND 24) THEN 1 ELSE 0 END) AS female_20to24, 

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 25 AND 29) THEN 1 ELSE 0 END) AS male_25to29, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 25 AND 29) THEN 1 ELSE 0 END) AS female_25to29,

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 30 AND 34) THEN 1 ELSE 0 END) AS male_30to34, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 30 AND 34) THEN 1 ELSE 0 END) AS female_30to34,

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 35 AND 39) THEN 1 ELSE 0 END) AS male_35to39, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 35 AND 39) THEN 1 ELSE 0 END) AS female_35to39,

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 40 AND 44) THEN 1 ELSE 0 END) AS male_40to44, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 40 AND 44) THEN 1 ELSE 0 END) AS female_40to44,

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 45 AND 49) THEN 1 ELSE 0 END) AS male_45to49, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 45 AND 49) THEN 1 ELSE 0 END) AS female_45to49,

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 50 AND 54) THEN 1 ELSE 0 END) AS male_50to54, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 50 AND 54) THEN 1 ELSE 0 END) AS female_50to54,

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 55 AND 59) THEN 1 ELSE 0 END) AS male_55to59, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 55 AND 59) THEN 1 ELSE 0 END) AS female_55to59,

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 60 AND 64) THEN 1 ELSE 0 END) AS male_60to64, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 60 AND 64) THEN 1 ELSE 0 END) AS female_60to64,

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 65 AND 69) THEN 1 ELSE 0 END) AS male_65to69, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 65 AND 69) THEN 1 ELSE 0 END) AS female_65to69,

                    SUM(CASE WHEN p.sex='m' AND $age_bdate >= 70) THEN 1 ELSE 0 END) AS male_70above, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate >= 70) THEN 1 ELSE 0 END) AS female_70above";