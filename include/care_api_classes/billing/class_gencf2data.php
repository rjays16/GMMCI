<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class CF2Data {
    
    private $total_actual;
    private $total_claim;    
    private $total_ppaid;    
    
    private $final_diagnosis;
    private $icd_codes;
    
    function __construct() {
        $this->total_actual = 0;
        $this->total_claim = 0;
        $this->total_ppaid = 0;
    }
    
    function getEarliestFromDate($enc_nr) {
        global $db;

        $frmdate = "0000-00-00 00:00:00";
        $strSQL = "select bill_frmdte " .
                    "   from seg_billing_encounter " .
                    "   where (encounter_nr = '". $enc_nr ."') " .
                    "   order by bill_frmdte asc limit 1";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $frmdate = $row['bill_frmdte'];
            }
        }

        return($frmdate);
    }

    function getLatestRefDate($enc_nr) {
        global $db;

        $todate = "0000-00-00 00:00:00";
        $strSQL = "select bill_dte " .
                    "   from seg_billing_encounter " .
                    "   where (encounter_nr = '". $enc_nr ."') " .
                    "   order by bill_dte desc limit 1";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $todate = $row['bill_dte'];
            }
        }

        return($todate);
    }

    // Added by LST ----- 05.28.2010 -------------------------------------

    function concatname($slast, $sfirst, $smid) {
        $stmp = "";

        if (!empty($slast)) $stmp .= $slast;
        if (!empty($sfirst)) {
            if (!empty($stmp)) $stmp .= ", ";
            $stmp .= $sfirst;
        }
        if (!empty($smid)) {
            if (!empty($stmp)) $stmp .= " ";
            $stmp .= $smid;
        }
        return($stmp);
    }    
    
    function isCharity($enc_nr) {
        $actype = '';
        $acname = '';
        $this->getAccommodationType($enc_nr, $actype, $acname);
        return (!(strpos(strtoupper($acname), 'CHARITY', 0) === false));
    }    
    
    function getAddedAccommodationType($enc_nr, $from_date, $to_date, &$ntype, &$sname) {
        global $db;

        $ntype = 0;
        $sname = '';
    //	$filter = '';

    //	$from_date = getEarliestFromDate($enc_nr);
    //	$to_date   = getLatestRefDate($enc_nr);

        $strSQL = "select cw.accomodation_type, accomodation_name ".
                    "   from (seg_encounter_location_addtl as sela inner join care_ward as cw on sela.group_nr = cw.nr) ".
                    "      inner join seg_accomodation_type as sat on cw.accomodation_type = sat.accomodation_nr ".
                    "   where (sela.encounter_nr = '$enc_nr' or sela.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
                    "      and (str_to_date(sela.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "' ".
                    "      and str_to_date(sela.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $to_date . "') ".
                    "   order by entry_no desc limit 1";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $ntype = $row['accomodation_type'];
                    $sname = $row['accomodation_name'];
                }
            }
        }

        return($db->ErrorMsg() == '');
    }

    function getAccommodationType($enc_nr, &$ntype, &$sname) {
        global $db;

        $ntype = 0;
        $sname = '';
    //	$filter = '';

        $from_date = $this->getEarliestFromDate($enc_nr);
        $to_date   = $this->getLatestRefDate($enc_nr);

        $strSQL = "select
                    STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') occupy_date,
                    cw.accomodation_type, accomodation_name ".
                    "   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
                    "      inner join seg_accomodation_type as sat on cw.accomodation_type = sat.accomodation_nr) ".
                    "      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr ".
                    "   where (cel.encounter_nr = '$enc_nr' or cel.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
                    "      and exists (select nr ".
                    "                     from care_type_location as ctl ".
                    "                     where upper(type) = 'WARD' and ctl.nr = cel.type_nr) ".
                    "      and ((str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "' ".
                    "      and str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $to_date . "') ".
                    "         or ".
                    "      (str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "' ".
                    "      and str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $to_date . "') ".
                    "      or ".
                    "      str_to_date(concat(date_format(ifnull(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', date_format(ifnull(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00') ".
            " UNION ALL
              SELECT occupy_date, cw.accomodation_type, accomodation_name
                FROM (seg_encounter_location_addtl sel INNER JOIN care_ward AS cw ON sel.group_nr = cw.nr)
                  INNER JOIN seg_accomodation_type sat ON cw.accomodation_type = sat.accomodation_nr
              WHERE (sel.encounter_nr = '$enc_nr' or sel.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr'))
                AND (
                  STR_TO_DATE(
                    sel.create_dt,
                    '%Y-%m-%d %H:%i:%s'
                  ) >= '" . $from_date . "'
                  AND STR_TO_DATE(
                    sel.create_dt,
                    '%Y-%m-%d %H:%i:%s'
                  ) < '" . $to_date . "'
                )
              ORDER BY occupy_date DESC LIMIT 1";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $ntype = $row['accomodation_type'];
                    $sname = $row['accomodation_name'];
                }
            }
        }

        if ($ntype == 0)
            return($this->getAddedAccommodationType($enc_nr, $from_date, $to_date, $ntype, $sname));
        else
            return($db->ErrorMsg() == '');
    }    
    
    /**
    * @internal     returns the array of objects with confinement-related information.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    *
    * @param        enc_nr   - encounter no.
    * @param        hcare_id - insurance id.
    * @return       array of objects with confinement-related information.
    */
    function getConfinementData($enc_nr) {
        global $db;

        $data = array();

        // Changed ce.discharge_date to ce.mgh_setdte per request of Billing to reflect correct days for claim ... 03.29.2012 -- LST
        $strSQL = "SELECT
                      ce.admission_dt,
                      ce.discharge_date,
                      cp.death_date,
                      ce.discharge_time,
                      DATEDIFF(
                        (CASE WHEN ce.discharge_date < ce.mgh_setdte THEN ce.discharge_date ELSE ce.mgh_setdte END),
                        ce.admission_dt
                      ) claim_days
                    FROM
                      care_encounter AS ce
                      INNER JOIN
                      care_person AS cp
                      ON ce.pid = cp.pid
                    WHERE ce.encounter_nr = '$enc_nr'";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    $obj = new Confinement();

                    $obj->admit_dt      = strftime("%m/%d/%Y", strtotime($row['admission_dt']));
                    $obj->discharge_dt  = strftime("%m/%d/%Y", strtotime($row['discharge_date']));
                    $obj->death_dt      = ($row['death_date'] == '0000-00-00') ? '00-00-0000' : strftime("%m/%d/%Y", strtotime($row['death_date']));
                    $obj->claim_days    = $row['claim_days'];
                    if (strpos($row['discharge_time'],"24:") === 0) {
                        $row['discharge_time'] = "00:".substr($row['discharge_time'],3);
                        $obj->discharge_tm  = strftime("%I:%M %p", strtotime($row['discharge_time']));
                    }
                    else
                    $obj->discharge_tm  = strftime("%I:%M %p", strtotime($row['discharge_time']));
                    $obj->admit_tm      = strftime("%I:%M %p", strtotime($row['admission_dt']));

                    $data[] = $obj;
                }
            }
        }

        return $data;
    }
    
    /**
    * @internal     returns the array of hospital/ambulatory services charged.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    *
    * @param        enc_nr   - encounter no.
    * @param        hcare_id - insurance id.
    * @return       array of objects of HospServices.
    */
    function getHospAmbulSrvData($enc_nr, $hcare_id) {
        global $db;

        $strSQL = "select encounter_nr, sum(total_acc_charge) as acc_charge, sum(total_med_charge) as med_charge,
                         sum(total_srv_charge + total_msc_charge) as srv_charge, sum(total_ops_charge) as ops_charge,
                         sum(total_acc_coverage) as acc_coverage, sum(total_med_coverage) as med_coverage,
                         sum(total_srv_coverage + total_msc_coverage) as srv_coverage, sum(total_ops_coverage) as ops_coverage
                        from seg_billing_encounter as sbe left join seg_billing_coverage as sbc
                         on sbe.bill_nr = sbc.bill_nr
                        where sbc.hcare_id = $hcare_id and sbe.encounter_nr = '$enc_nr'
                        group by encounter_nr";

        $services = array();
        $bloaded = false;

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    // Room and Board
                    $obj = new HospServices();
                    $obj->charges = $row["acc_charge"];
                    $obj->claim_hospital = $row["acc_coverage"];
                    $obj->claim_patient = 0;
                    $services[]  = $obj;                    

                    // Drugs and Medicines
                    $obj = new HospServices();
                    $obj->charges = $row["med_charge"];
                    $obj->claim_hospital = $row["med_coverage"];
                    $obj->claim_patient = 0;
                    $services[] = $obj;                    

                    // X-Ray, Lab and Others
                    $obj = new HospServices();
                    $obj->charges = $row["srv_charge"];
                    $obj->claim_hospital = $row["srv_coverage"];
                    $obj->claim_patient = 0;
                    $services[] = $obj;                    

                    // O.R. Fees
                    $obj = new HospServices();
                    $obj->charges = $row["ops_charge"];
                    $obj->claim_hospital = $row["ops_coverage"];
                    $obj->claim_patient = 0;
                    $services[] = $obj;

                    // Medicines, Lab, Other charges reimbursible to patient.
                    $pclaim = (isset($_GET['claim'])) ? $_GET['claim'] : 0;
                    $obj = new HospServices();
                    $obj->charges = $pclaim;
                    $obj->claim_hospital = 0;
                    $obj->claim_patient = $pclaim;
                    $services[] = $obj;
                    
                    $this->total_actual += ($row["acc_charge"] + $row["med_charge"] + $row["srv_charge"] + $row["ops_charge"] + $pclaim);
                    $this->total_claim  += ($row["acc_coverage"] + $row["med_coverage"] + $row["srv_coverage"] + $row["ops_coverage"]);
                    $this->total_ppaid  += $pclaim;

                    $bloaded = true;
                }
            }
        }

        if (!$bloaded) {
            // Room and Board
            $obj = new HospServices();
            $obj->charges = 0;
            $obj->claim_hospital = 0;
            $obj->claim_patient = 0;
            $services[]  = $obj;

            // Drugs and Medicines
            $obj = new HospServices();
            $obj->charges = 0;
            $obj->claim_hospital = 0;
            $obj->claim_patient = 0;
            $services[] = $obj;

            // X-Ray, Lab and Others
            $obj = new HospServices();
            $obj->charges = 0;
            $obj->claim_hospital = 0;
            $obj->claim_patient = 0;
            $services[] = $obj;

            // O.R. Fees
            $obj = new HospServices();
            $obj->charges = 0;
            $obj->claim_hospital = 0;
            $obj->claim_patient = 0;
            $services[] = $obj;

            // Medicines, Lab, Other charges reimbursible to patient.
            $pclaim = (isset($_GET['claim'])) ? $_GET['claim'] : 0;
            $obj = new HospServices();
            $obj->charges = $pclaim;
            $obj->claim_hospital = 0;
            $obj->claim_patient = $pclaim;
            $services[] = $obj;
            
            $this->total_actual += $pclaim;
            $this->total_ppaid  += $pclaim;
        }

        return($services);
    }
    
    function isHouseCase($enc_nr) {
        global $db;

        $case = '';
        $sql = "select st.casetype_desc from seg_encounter_case sc
                                    inner join seg_type_case st on sc.casetype_id = st.casetype_id ".
                     "   where encounter_nr = '".$enc_nr."' and !sc.is_deleted ".
                     "   order by sc.modify_dt desc limit 1";

        if($result = $db->Execute($sql)){
                if($result->RecordCount()){
                        if ($row = $result->FetchRow()) {
                            $case = $row['casetype_desc'];
                        }
                }
        }

    //	return !(strpos($case, 'HOUSE') === false) && !$pdf->hasPrivateAccommodation($enc_nr);
        return !(strpos($case, 'HOUSE') === false);
    }

    function getHouseCaseDoctor($hcare_id, $role) {
        global $db;

        switch ($role) {
            case 'D1':
            case 'D2':
              $filter = "cpr.is_housecase_attdr = 1";
              break;
            case 'D3':
              $filter = "cpr.is_housecase_surgeon = 1";
              break;
            case 'D4':
              $filter = "cpr.is_housecase_anesth = 1";
        }
        $strSQL = "SELECT cpr.nr, cp.name_last, cp.name_first, cp.name_middle, tin,     \n
                        (SELECT accreditation_nr FROM seg_dr_accreditation AS sda WHERE sda.dr_nr = cpr.nr AND sda.hcare_id = $hcare_id) AS accno   \n
                   FROM care_personell cpr INNER JOIN care_person cp ON cpr.pid = cp.pid   \n
                   WHERE $filter";
        if($result = $db->Execute($strSQL)) {
                if($result->RecordCount()) {
                        if ($row = $result->FetchRow()) {
                            return $row;
                        }
                }
        }
        return false;
    }

    function addPFInfo($role, $pfarr, $obj) {
      foreach($pfarr as $key=> $value) {
          if ($value->role_area == $role) {
            $value->servperformance .= (!empty($value->servperformance) ? ";" : "").$obj->servperformance;
            $value->profcharges += $obj->profcharges;
            $value->claim_physician += $obj->claim_physician;
            if ($role == 'D3')
              $value->operation_dt .= (!empty($value->operation_dt) ? ";" : "").$obj->operation_dt;
            else
              $value->inclusive_dates .= (!empty($value->inclusive_dates) ? ";" : "").$obj->inclusive_dates;
            $pfarr[$key] = $value;
            break;
          }
      }
    }    
    
    function getOPDate($enc_nr, $dr_nr) {
        global $db;

        $strSQL = "SELECT DISTINCT
                                    ceo.op_date
                                FROM (seg_ops_personell AS sop
                                     INNER JOIN seg_ops_serv AS sos
                                         ON sop.refno = sos.refno)
                                    LEFT JOIN care_encounter_op AS ceo
                                        ON sos.refno = ceo.refno
                                WHERE sos.encounter_nr = '$enc_nr'
                                        AND sop.dr_nr = $dr_nr UNION SELECT DISTINCT
                                                                    smod.op_date
                                                                FROM (seg_misc_ops AS smo
                                                                       INNER JOIN seg_misc_ops_details AS smod
                                                                           ON smod.refno = smo.refno)
                                                                    INNER JOIN seg_ops_chrg_dr AS socd
                                                                        ON smod.refno = socd.ops_refno
                                                                            AND smod.entry_no = socd.ops_entryno
                                                                            AND smod.ops_code = socd.ops_code
                                                                WHERE smo.encounter_nr = '$enc_nr'
                                                                        AND socd.dr_nr = $dr_nr
                                ORDER BY op_date";
        $op_dates = array();
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    if (!is_null($row["op_date"])) $op_dates[] = $row["op_date"];
                }
            }
        }

        return $op_dates;
    }

    function format_opdates($opdates) {
        $month = 0;
        $yr = 0;
        $day = 0;
        $days = "";
        $fdates = array();
        if (is_array($opdates) && (count($opdates) > 0)) {
            foreach($opdates as $date) {
                $dt = getdate(strtotime($date));
                if ($dt['year'] != $yr) {
                    if ($yr != 0) {
                        $fdates[] = $month."/".$days."/".$yr;
                        $days = "";
                    }
                    $yr = $dt['year'];
                    $month = $dt['mon'];
                    $day = $dt['mday'];
                    $days .= $day;
                    continue;
                }
                if ($dt['mon'] != $month) {
                    if ($month != 0) {
                        $fdates[] = $month."/".$days;
                        $days = "";
                    }
                    $month = $dt['mon'];
                    $day = $dt['mday'];
                    $days .= $day;
                    continue;
                }
                if ($dt['mday'] != $day) {
                    $day = $dt['mday'];
                    if ($day != 0) $days .= (($days != '') ? "," : "").$day;
                }
            }
            $fdates[] = $month."/".$days."/".$yr;
            return implode(",",$fdates);
        }
        else
            return false;
    }    
    
    function getCaseTypeID($enc_nr) {
        global $db;

        $sdesc = '';
        $n_id  = 0;

        // fix for issue HISSPMC-143 ----- start ----
        $to_date   = $this->getLatestRefDate($enc_nr);
        $strSQL = "select confinetype_id from seg_encounter_confinement ".
                            "   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
                            "      and str_to_date(classify_dte, '%Y-%m-%d %H:%i:%s') < '".$to_date."' " .
                            "   order by classify_dte desc limit 1";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    $n_id  = $row["confinetype_id"];
                }
            }
        }

        if ($n_id == 0) {
            $strSQL = "select stci.confinetype_id, confinetypedesc from seg_type_confinement_icds as stci inner join seg_type_confinement as stc
                            on stci.confinetype_id = stc.confinetype_id
                            where exists(select * from care_encounter_diagnosis as ced0
                                            where substring(code, 1, if(instr(code, '.') = 0, length(code), instr(code, '.')-1)) =
                                                substring(stci.diagnosis_code, 1, if(instr(stci.diagnosis_code, '.') = 0, length(stci.diagnosis_code), instr(stci.diagnosis_code, '.')-1))
                            and ((exists(select * from care_encounter_diagnosis as ced where instr(stci.paired_codes, ced.code) > 0 and ced.code <> ced0.code and status <> 'deleted') and stci.paired_codes <> '') or stci.paired_codes = '')
                                             and (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and str_to_date(create_time, '%Y-%m-%d %H:%i:%s') < str_to_date(now(), '%Y-%m-%d %H:%i:%s')
                                             and status <> 'deleted')
                            order by confinetype_id desc limit 1";

            if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                    if ($row = $result->FetchRow()) {
                        $n_id  = $row["confinetype_id"];
                        $sdesc = $row["confinetypedesc"];
                    }
                }
            }

            if ($n_id == 0)
                $n_id = 1;
            else {
                switch (strtoupper($sdesc)) {
                    case "B":
                    case "INTENSIVE":
                        $n_id = 2;
                        break;

                    case "C":
                    case "CATASTROPHIC":
                        $n_id = 3;
                        break;

                    case "D":
                        $n_id = 4;
                        break;

                    default:
                        $n_id = 1;

                }
            }
        }
        // fix for issue HISSPMC-143  --- end ----

        return $n_id;
    }    
    
    function getDiagnosisCaseTypeData($enc_nr) {
        global $db;

        $data = array();

        $n_id = $this->getCaseTypeID($enc_nr);
        $strSQL = "select ced.code, ifnull(sd.description, ifnull(cie.description, '')) as description
                      from (care_encounter_diagnosis as ced inner join care_icd10_en as cie
                         on ced.code = cie.diagnosis_code) left join seg_encounter_diagnosis as sd
                            on sd.encounter_nr = ced.encounter_nr and sd.code = ced.code and sd.is_deleted = 0
                      where ced.encounter_nr = '$enc_nr' and status NOT IN ('deleted','hidden','inactive','void') 
                          AND IF(INSTR(cie.diagnosis_code,'.'),SUBSTR(cie.diagnosis_code,1,IF(INSTR(cie.diagnosis_code,'.'),INSTR(cie.diagnosis_code,'.')-1,0)),
cie.diagnosis_code) REGEXP '^[[:alpha:]][[:digit:]]' 
                      order by type_nr desc";
        
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $obj = new Diagnosis();
                    $obj->code = $row["code"];
                    $obj->fin_diagnosis = $row["description"];
                    $obj->case_type = $n_id;
                    $data[] = $obj;
                }
            }
        }

        if (empty($data)) {
            $obj = new Diagnosis();
            $obj->fin_diagnosis = "";
            $obj->code = "";
            $obj->case_type = $n_id;
            $data[] = $obj;
        }

        return $data;
    }    
        
    function getPFData($enc_nr, $hcare_id, &$data1, &$data2) {
        global $db;

        $data1 = array();
        $data2 = array();

        $from_date = $this->getEarliestFromDate($enc_nr);
        $to_date   = $this->getLatestRefDate($enc_nr);

        $tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($to_date)));

        $hosp_obj = new Hospital_Admin();
        $cutoff_hrs = $hosp_obj->getCutoff_Hrs();

        $strSQL = "select attending_dr_nr as dr_nr, name_last, name_first, name_middle, 'Attending Physician' as role,
         \n           (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
         \n             on sbp.bill_nr = sbe1.bill_nr
         \n             where sbp.dr_nr = t.attending_dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr') as charge,
         \n          (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
         \n             on sbp2.bill_nr = sbe2.bill_nr
         \n             where sbp2.dr_nr = t.attending_dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr') as claim,
         \n          role_nr, role_area, 0 as rvu, 0 as multiplier,
         \n          sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$cutoff_hrs.")) as services, tin,
         \n          (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = t.attending_dr_nr and sda.hcare_id = $hcare_id) as accno,
         \n					 concat(date_format(attend_start, '%m/%d/%Y'), '-', date_format(if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), '%m/%d/%Y')) as inc_dates,
                        date_format(attend_start, '%Y-%m-%d') attend_start, date_format(if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d'), discharge_date), attend_end), '%Y-%m-%d') attend_end
         \n          from
         \n             (select distinct attending_dr_nr, name_last, name_first, name_middle, attend_start,
         \n                 subdate((select attend_start
         \n                             from seg_encounter_dr_mgt as dm2
         \n                             where dm2.encounter_nr = dm1.encounter_nr and
         \n                                   dm2.att_hist_no > dm1.att_hist_no
         \n                             order by dm2.att_hist_no asc limit 1), 1) as attend_end, daily_rate, cpa.role_nr, role_area, discharge_date, tin
         \n                 from (seg_encounter_dr_mgt as dm1 inner join (((care_personell as cpn
         \n                    inner join care_person as cp on cpn.pid = cp.pid) inner join care_personell_assignment as cpa
         \n                    on cpn.nr = cpa.personell_nr) inner join care_role_person as crp on
         \n                    cpa.role_nr = crp.nr) on dm1.attending_dr_nr = cpn.nr) inner join care_encounter as ce
         \n                    on dm1.encounter_nr = ce.encounter_nr
         \n                 where (dm1.encounter_nr = '$enc_nr' or dm1.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr'))
         \n                    and str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'
         \n                    and str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'
         \n                 order by att_hist_no) as t
         \n          group by attending_dr_nr, role_area
         \n          union
         \n          select spd.dr_nr, name_last, name_first, name_middle, (CASE WHEN crp.role_area <> 'D2' THEN `name` ELSE CONCAT(`name`, ' - private') END) as role,
         \n              (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
         \n                 on sbp.bill_nr = sbe1.bill_nr
         \n                 where sbp.dr_nr = spd.dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr' AND sbp.role_area = crp.role_area) as charge,
         \n              (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
         \n                 on sbp2.bill_nr = sbe2.bill_nr
         \n                 where sbp2.dr_nr = spd.dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr' AND sbp2.role_area = crp.role_area) as claim,
         \n              spd.dr_role_type_nr, role_area, sum(ifnull(socd.rvu,0)) as tot_rvu, (sum(ifnull(socd.multiplier,0) * ifnull(socd.rvu,0))/sum(ifnull(socd.rvu,0))) as avg_multiplier,
         \n              (CASE WHEN crp.role_area = 'D1' OR crp.role_area = 'D2'
                               THEN (CASE WHEN days_attended = 0
                                          THEN DATEDIFF((select discharge_date from care_encounter where encounter_nr = '$enc_nr'), (select admission_dt from care_encounter where encounter_nr = '$enc_nr'))
                                          ELSE days_attended
                                     END)
                               ELSE GROUP_CONCAT(DISTINCT sor.code ORDER BY sor.code DESC SEPARATOR '; ') END) as services, tin,
         \n              (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = spd.dr_nr and sda.hcare_id = $hcare_id) as accno,
         \n              (CASE WHEN (role_area = 'D1') OR (role_area = 'D2') THEN
                                                concat(date_format((select admission_dt from care_encounter where encounter_nr = '$enc_nr'), '%m-%d-%Y'), '-',
                                                             date_format((select discharge_date from care_encounter where encounter_nr = '$enc_nr'), '%m/%d/%Y')) ELSE '' END) as inc_dates, 
                            date_format((select admission_dt from care_encounter where encounter_nr = '$enc_nr'), '%Y-%m-%d') attend_start, date_format((select discharge_date from care_encounter where encounter_nr = '$enc_nr'), '%Y-%m-%d') attend_end 
         \n              from ((seg_encounter_privy_dr as spd left join (seg_ops_chrg_dr as socd inner join seg_ops_rvs as sor on socd.ops_code = sor.code) on
         \n                 spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and
         \n                 spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn
         \n                 inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr)
         \n                 inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr
         \n              where (spd.encounter_nr = '$enc_nr' or spd.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr'))
         \n                 and is_excluded = 0
         \n                 and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'
         \n                 and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'
         \n              group by spd.dr_nr, name_last, name_first, name_middle, crp.role_area
         \n          union
         \n           select dr_nr, name_last, name_first, name_middle, concat(name, ' - ', cop.code) as role,
         \n              (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
         \n                 on sbp.bill_nr = sbe1.bill_nr
         \n                 where sbp.dr_nr = sop.dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr') as charge,
         \n              (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
         \n                 on sbp2.bill_nr = sbe2.bill_nr
         \n                 where sbp2.dr_nr = sop.dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr') as claim,
         \n           sop.role_type_nr, role_area, sum(sosd.rvu) as tot_rvu, (sum(multiplier * sosd.rvu)/sum(sosd.rvu)) as avg_multiplier,
         \n           group_concat(DISTINCT sor.code ORDER BY sor.code DESC SEPARATOR '; ') as services, tin,
         \n          (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = sop.dr_nr and sda.hcare_id = $hcare_id) as accno, '' as inc_dates,
                            '0000-00-00' attend_start, '0000-00-00' attend_end
         \n              from (((seg_ops_personell as sop inner join (care_personell as cpn
         \n                 inner join care_person as cp on cpn.pid = cp.pid) on sop.dr_nr = cpn.nr)
         \n                 inner join (seg_ops_serv as sos inner join (seg_ops_servdetails as sosd inner join seg_ops_rvs as sor on sosd.ops_code = sor.code)
         \n                    on sos.refno = sosd.refno) on sop.refno = sos.refno)
         \n                 inner join care_role_person as crp on sop.role_type_nr = crp.nr)
         \n                 inner join seg_ops_rvs as cop on sop.ops_code = cop.code
         \n              where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and upper(trim(sos.status)) <> 'DELETED'
         \n                 and str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'
         \n                 and str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'
         \n                 and role_area is not null
         \n              group by dr_nr, role_area
         \n           ORDER BY name_last, name_first, name_middle, role_area";

        $bConsultantNoted = false;
        $bSurgeonNoted = false;
        $bAnesthNoted = false;

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $nclaim = (is_null($row["claim"])) ? 0 : $row["claim"];
                    if ($nclaim != 0) {                        
                        $obj = ($row["role_area"] == 'D3') ? new Surgeon() : new HealthPersonnel();

                        $res = false;
                        $bjustadd = false;
                        if ( ($bHouseCase = $this->isHouseCase($enc_nr)) && !$this->isCharity($enc_nr) ) {
                          if ( in_array($row["role_area"], array('D1', 'D2')) ) {
                            // Consultant ...
                            if ( !$bConsultantNoted ) {
                              $res = $this->getHouseCaseDoctor($hcare_id, $row["role_area"]);
                              if ($res) $bConsultantNoted = true;
                            }
                            else
                              $bjustadd = true;
                          }
                          else if ( $row["role_area"] == 'D3' ) {
                            // Surgeon ...
                            if ( !$bSurgeonNoted ) {
                              $res = $this->getHouseCaseDoctor($hcare_id, $row["role_area"]);
                              if ($res) $bSurgeonNoted = true;
                            }
                            else
                              $bjustadd = true;
                          }
                          else {
                            // Anaesthesiologist ...
                            if ( $row["role_area"] == 'D4' ) {
                              if ( !$bAnesthNoted ) {
                                $res = $this->getHouseCaseDoctor($hcare_id, $row["role_area"]);
                                if ($res) $bAnesthNoted = true;
                              }
                              else
                                $bjustadd = true;
                            }
                          }
                        }

                        if ($res) {
//                          $obj->name = $this->concatname((is_null($res["name_last"])) ? "" : $res["name_last"],
//                                      (is_null($res["name_first"])) ? "" : $res["name_first"],
//                                      (is_null($res["name_middle"])) ? "" : $res["name_middle"]);
                            
                          $obj->lastname = (String)$res["name_last"];
                          $obj->firstname = (String)$res["name_first"];
                          $obj->middlename = (String)$res["name_middle"];
                          $obj->suffix = '';
                          $obj->accnum = (is_null($res["accno"])) ? "" : $res["accno"];
                          $obj->bir_tin_num = (is_null($res["tin"])) ? "" : $res["tin"];
                        }
                        else {
//                          $obj->name = $this->concatname((is_null($row["name_last"])) ? "" : $row["name_last"],
//                                      (is_null($row["name_first"])) ? "" : $row["name_first"],
//                                      (is_null($row["name_middle"])) ? "" : $row["name_middle"]);
                          
                          $obj->lastname = (String)$res["name_last"];
                          $obj->firstname = (String)$res["name_first"];
                          $obj->middlename = (String)$res["name_middle"];
                          $obj->suffix = '';                          
                          $obj->accnum = (is_null($row["accno"])) ? "" : $row["accno"];
                          $obj->bir_tin_num = (is_null($row["tin"])) ? "" : $row["tin"];
                        }
                        
                        $obj->drnr = $row['dr_nr'];
                        $obj->servperformance = (is_null($row["services"])) ? "" : $row["services"];
                        $obj->profcharges = (is_null($row["charge"])) ? 0 : $row["charge"];
                        $obj->claim_physician = (is_null($row["claim"])) ? 0 : $row["claim"];
                        $obj->claim_patient = 0;
                        $obj->role_area = $row["role_area"];
                        
                        // Add prof charges and claim to totals tracker ...
                        $this->total_actual += $obj->profcharges;
                        $this->total_claim  += $obj->claim_physician;

                        if ($row["role_area"] == 'D3') {
                            $op_dte = $this->getOPDate($enc_nr, $row["dr_nr"]);
                            if (is_array($op_dte) && (count($op_dte) > 0)) $obj->operation_dt = $this->format_opdates($op_dte);
                            if ($bjustadd)
                                $this->addPFInfo($row["role_area"], $data2, $obj);
                            else
                                $data2[] = $obj;
                        }
                        else {
                          if ($row["role_area"] == 'D4') {
                            $op_dte = $this->getOPDate($enc_nr, $row["dr_nr"]);
                            if (is_array($op_dte) && (count($op_dte) > 0)) $obj->inclusive_dates = $this->format_opdates($op_dte);
                          }
                          else {
                             $obj->inclusive_dates = $row["inc_dates"];
                             $obj->attend_start = $row["attend_start"];
                             $obj->attend_end   = $row["attend_end"];
                          }
                          if ($bjustadd)
                            $this->addPFInfo($row["role_area"], $data1, $obj);
                          else
                            $data1[] = $obj;
                        }
                    } // nclaim != 0
                } 	// while loop
            }
        }
    }
    
    function getDrugsandMeds($enc_nr, $hcare_id) {
        global $db;

        $data = array();

        $from_date = $this->getEarliestFromDate($enc_nr);
        $to_date   = $this->getLatestRefDate($enc_nr);

        $strSQL = "select bestellnum, generic, artikelname, description, max(flag) as flag, sum(qty) as qty, (sum(price * qty)/sum(qty)) as price, sum(itemcharge) as itemcharge, ".
                    "   (select sum(coverage) as tcoverage
                            from seg_applied_coverage sac left join seg_billing_encounter sbe on sbe.bill_nr = sac.ref_no
                         where (sbe.encounter_nr = '$enc_nr' or sac.ref_no = concat('T', '$enc_nr'))
                            and hcare_id = $hcare_id and source = 'M' and sac.item_code = t.bestellnum) as claim ".
                    " from ".
                    "(select 0 as flag, pd.bestellnum, generic, artikelname, description, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as price, sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge ".
                    "   from ((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
                    "         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'M') ".
                    "         left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
                    "      where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and is_cash = 0 and pd.serve_status <> 'N' and pd.request_flag is null ".
                    "        and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
                    "        and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'" .
                    "        and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'" .
                    "   group by pd.bestellnum, artikelname ".
                    " union all ".
                    "select 1 as flag, mpd.bestellnum, generic, artikelname, description, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as price, sum(quantity * unit_price) as itemcharge ".
                    "   from (seg_more_phorder as mph inner join seg_more_phorder_details as mpd on mph.refno = mpd.refno) ".
                    "      inner join care_pharma_products_main as p on mpd.bestellnum = p.bestellnum and p.prod_class = 'M' ".
                    "   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
                    "       and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
                    "       and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
                    "   group by mpd.bestellnum, artikelname) as t
                        LEFT JOIN (SELECT * FROM seg_applied_coverage c
                                              WHERE hcare_id = $hcare_id AND source = 'M'
                                                 AND (c.ref_no = CONCAT('T', '$enc_nr')
                                                    OR c.ref_no = (SELECT bill_nr FROM seg_billing_encounter sbe WHERE sbe.encounter_nr = '$enc_nr'))
                            ) AS sac ON sac.item_code = t.bestellnum
                                                group by bestellnum, artikelname order by artikelname";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $obj = new Form2Meds();
                    $obj->drug_code   = $row["bestellnum"];
                    $obj->gen_name    = (is_null($row["generic"]) || $row["generic"] == '') ? $row["artikelname"] : $row["generic"];
                    $obj->brand       = is_null($row["artikelname"]) ? "" : $row["artikelname"];
                    $obj->preparation = is_null($row["description"]) ? "" : $row["description"];
                    $obj->qty         = $row["qty"];
                    $obj->unit_price  = $row["price"];
                    $obj->actual_charges = $row["itemcharge"];
                    $obj->claim_hospital = $row["claim"];
                    $obj->claim_patient  = 0;
                    $data[] = $obj;
                }
            }
        }

        return $data;
    }    
    
    function getXRayLabOthers($enc_nr, $hcare_id, &$xray_array, &$lab_array, &$supp_array) {
    //	global $db;

        //$xraylab_array = array();
        $xray_array 	 = array();
        $lab_array		 = array();
        $supp_array    = array();
        //$others_array  = array();

        $from_date = $this->getEarliestFromDate($enc_nr);
        $to_date   = $this->getLatestRefDate($enc_nr);

        $strSQL = "select distinct ld.service_code, ls.name as service_desc, sum(ld.quantity) as qty, (sum(ld.price_charge * ld.quantity)/sum(ld.quantity)) as price,
                                         sum(ld.quantity * ld.price_charge) as itemcharge, fn_gettotalclaim($hcare_id, ld.service_code, '$enc_nr') as claim, 'LB' as source " .
                            "   from (((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
                            "          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
                            "          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code) ".
                            "      where /*ld.is_served <> 0 and*/ lh.is_cash = 0 and (ld.request_flag is null OR ld.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
                            "         and (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and upper(trim(lh.status)) <> 'DELETED' " .
                            "         and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "' ".
                            "         and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "' ".
                            "      group by ld.service_code, ls.name, ls.group_code, lsg.name, source";
        $this->putinXLOitemized_array($strSQL, $xray_array, $lab_array, $supp_array);

        $strSQL = "select rd.service_code, rs.name as service_desc, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as price,
                                            sum(rd.price_charge) as itemcharge, fn_gettotalclaim($hcare_id, rd.service_code, '$enc_nr') as claim, 'RD' as source " .
                             "   from (((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
                             "          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
                             "          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code) ".
                            "      where /*upper(rd.status) = 'DONE' and*/ rh.is_cash = 0 and (rd.request_flag is null OR rd.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
                            "         and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
                            "         and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
                            "         and (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and upper(trim(rh.status)) <> 'DELETED' " .
                            "         and upper(trim(rd.status)) <> 'DELETED' ".
                            "   group by rd.service_code, rs.name, rs.group_code, rsg.name, source ";
        $this->putinXLOitemized_array($strSQL, $xray_array, $lab_array, $supp_array);

        $strSQL = "select service_code, service_desc, sum(qty) as qty, (sum(price * qty)/sum(qty)) as price, sum(itemcharge) as itemcharge, ".
                            "   fn_gettotalclaim($hcare_id, service_code, '$enc_nr') as claim, t.source ".
                            " from ".
                            "(select 0 as grp, pd.bestellnum as service_code, artikelname as service_desc, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as price,
                                         sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge, 'SU' as source ".
                            "   from (((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
                            "      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'S') ".
                            "      left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum) ".
                            "   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and is_cash = 0 and pd.serve_status <> 'N' and pd.request_flag is null ".
                            "      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
                            "      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
                            "      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
                            "   group by pd.bestellnum, artikelname ".
                            " UNION ".
                            "select 1 as grp, mphd.bestellnum as service_code, artikelname as service_desc, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as price,
                                         sum(quantity * unit_price) as itemcharge, 'SU' as source ".
                            "   from ((seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
                            "      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum and p.prod_class = 'S') ".
                            "   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
                            "      and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
                            "      and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
                            "   group by mphd.bestellnum, artikelname) as t
                                    GROUP BY service_desc ORDER BY service_desc";
        $this->putinXLOitemized_array($strSQL, $xray_array, $lab_array, $supp_array);

        $strSQL = "select eqd.equipment_id as service_code, artikelname as service_desc, sum(number_of_usage) as qty, (sum(discounted_price * number_of_usage)/sum(number_of_usage)) as price,
                                        sum(number_of_usage * discounted_price) as itemcharge, fn_gettotalclaim($hcare_id, eqd.equipment_id, '$enc_nr') as claim, 'OE' as source
                                 from ((seg_equipment_orders as eqh inner join seg_equipment_order_items as eqd on eqh.refno = eqd.refno)
                                         inner join seg_ops_serv as sos on sos.refno = eqh.request_refno)
                                         inner join care_pharma_products_main as cppm on cppm.bestellnum = eqd.equipment_id
                                 where (sos.encounter_nr = '$enc_nr' or sos.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr'))
                                        and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'
                                        and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'
                                 group by eqd.equipment_id, artikelname";
        $this->putinXLOitemized_array($strSQL, $xray_array, $lab_array, $supp_array);

        $strSQL = "select md.service_code, ms.name as service_desc, sum(md.quantity) as qty, (sum(chrg_amnt * md.quantity)/sum(md.quantity)) as price,
                                         sum(md.quantity * chrg_amnt) as itemcharge, fn_gettotalclaim($hcare_id, md.service_code, '$enc_nr') as claim, 'OA' as source ".
                            "   from ((seg_misc_service as m inner join seg_misc_service_details as md on m.refno = md.refno) ".
                            "      inner join seg_other_services as ms on md.service_code = ms.alt_service_code) ".
                            "   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
                            "      and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
                            "      and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
                            "   group by md.service_code, ms.name";
        $this->putinXLOitemized_array($strSQL, $xray_array, $lab_array, $supp_array);

        $strSQL = "select mcd.service_code, ms.name as service_desc, sum(mcd.quantity) as qty, (sum(chrg_amnt * mcd.quantity)/sum(mcd.quantity)) as price,
                                         sum(mcd.quantity * chrg_amnt) as itemcharge, fn_gettotalclaim($hcare_id, mcd.service_code, '$enc_nr') as claim, 'OC' as source ".
                            "   from ((seg_misc_chrg as mc inner join seg_misc_chrg_details as mcd on mc.refno = mcd.refno) ".
                            "      inner join seg_other_services as ms on mcd.service_code = ms.service_code)
                                    where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
                            "      and str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
                            "      and str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
                            "   group by mcd.service_code, ms.name";
        $this->putinXLOitemized_array($strSQL, $xray_array, $lab_array, $supp_array);
    }

    function putinXLOitemized_array($strSQL, &$xray_array, &$lab_array, &$supp_array) {
        global $db;

        $result = $db->Execute($strSQL);
        if ($result) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $obj = new Laboratories();
                    
                    switch ($row["source"]) {
                        case 'LB':
                            $generic_item = "Lab Service";
                            $diagnostic_type = "LABORATORY";
                            break;

                        case 'RD':
                            $generic_item = "X-Ray Service";
                            $diagnostic_type = "IMAGING";
                            break;

                        case 'SU':
                        case 'MS':
                            $generic_item = "Supply Item";
                            $diagnostic_type = "SUPPLIES";
                            break;

                        case 'OE':
                            $generic_item = "Equipment Charge";
                            $diagnostic_type = "OTHERS";
                            break;

                        case 'OA':
                        case 'OC':
                            $generic_item = "Miscellaneous Charge";
                            $diagnostic_type = "OTHERS";
                    }

                    $obj->diagnostic_type = $diagnostic_type;
                    $obj->particulars     = is_null($row["service_desc"]) ? $generic_item : $row["service_desc"];
                    $obj->qty             = $row["qty"];
                    $obj->unit_price      = $row["price"];
                    $obj->actual_charges  = $row["itemcharge"];
                    $obj->claim_hospital  = $row["claim"];
                    $obj->claim_patient   = 0;

                    switch ($row["source"]) {
                        case 'LB':
                            $lab_array[] = $obj;
                            break;
                        case 'RD':
                            $xray_array[] = $obj;
                            break;

                        case 'SU':
                        case 'MS':

                        case 'OE':
                        case 'OA':
                        case 'OC':
                            $supp_array[] = $obj;
                    }

                }
            }
        }
    }    
    
    function isCoveredByPkg($enc_nr) {
        global $db;
        
		$benefit = 0.00;
		$strSQL = "SELECT SUM(sap.coverage) AS benefit FROM seg_applied_pkgcoverage AS sap
                        LEFT JOIN seg_billing_encounter AS sbe ON sbe.bill_nr = sap.ref_no
                        WHERE sbe.encounter_nr = '".$enc_nr."' ";
		$result_benefit = $db->Execute($strSQL);
		if ($result_benefit) {
            $row = $result_benefit->FetchRow();
			if ($row) {
				if (!is_null($row['benefit'])) {
					$benefit = $row['benefit'];					
				}
			}
		}
        
        return ($benefit > 0);
    }
    
    function collateFinalDiagnosis($darray) {            
        $diagnosis = "";
        $this->icd_codes = array();
        $i = 1;
//        $j = 1;
        foreach($darray as $objdiag) {
            if (isset($objdiag->fin_diagnosis) && (trim($objdiag->fin_diagnosis) != '') && !is_null($objdiag->fin_diagnosis)) {
                $diagnosis .= (($diagnosis == "") ? "" : ";\n").$objdiag->fin_diagnosis;
                $i++;
            }

            if ($objdiag->code != '') {
//                if ($code != '') $code .= ", ";
//                $code .= $objdiag->code;
                $this->icd_codes[] = $objdiag->code;
//                $j++;
            }
        }
        $this->final_diagnosis = $diagnosis;
    }
    
    function getPackagePHICCode($enc_nr) {
        global $db;

        $strSQL = "SELECT
                      sp.pkg_phiccode
                    FROM
                      (
                        seg_packages sp
                        INNER JOIN
                        seg_billing_pkg sbp
                        ON sp.package_id = sbp.package_id
                      )
                      INNER JOIN
                      seg_billing_encounter sbe
                      ON sbp.ref_no = sbe.bill_nr
                    WHERE sbe.encounter_nr = '$enc_nr'";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
                if ($row = $result->FetchRow())
                    return $row['pkg_phiccode'];
			}
		}

		return "";
    }
    
    function getEncounterOps($enc_nr) {
        global $db;
        
        $strSQL = "SELECT 
                    smod.refno,
                    ops_code,
                    op_date opdate,
                    rvu 
                  FROM
                    seg_misc_ops_details smod 
                    INNER JOIN seg_misc_ops smo 
                      ON smod.refno = smo.refno 
                  WHERE encounter_nr = '$enc_nr' 
                  UNION
                  SELECT 
                    sosd.refno,
                    ops_code,
                    (SELECT 
                      op_date 
                    FROM
                      care_encounter_op cop 
                    WHERE cop.refno = sosd.refno 
                      AND cop.ops_code = sosd.ops_code) opdate,
                    rvu 
                  FROM
                    seg_ops_servdetails sosd 
                    INNER JOIN seg_ops_serv sos 
                      ON sosd.refno = sos.refno 
                  WHERE encounter_nr = '$enc_nr' 
                  ORDER BY opdate";
        $result = $db->Execute($strSQL);
		if ($result) {
			if ($result->RecordCount()) {                
                return $result;
			}
		}
        return false;
    }
    
    function getPFsInOp($enc_nr, $refno, $rolearea, $confinetype) {
        global $db;
        
        $strSQL = "SELECT ops_refno, dr_nr, 
                        (SELECT op_date FROM seg_misc_ops_details smod WHERE smod.refno = socd.ops_refno
                              AND smod.ops_code = socd.ops_code) opdate, 
                        ops_code, rvu, (rvu * multiplier) charge, 
                        rvu * fn_getPCF(encounter_nr, NOW(), '$rolearea', role_type_level, rvu, $confinetype, dr_nr) coverage
                   FROM seg_ops_chrg_dr socd 
                   WHERE encounter_nr = '$enc_nr' AND ops_refno = '$refno'
                     AND EXISTS(SELECT * FROM care_role_person crp WHERE crp.nr = socd.dr_role_type_nr
                         AND crp.role_area = '$rolearea')
                     AND EXISTS(SELECT * FROM seg_misc_ops smo WHERE smo.refno = socd.ops_refno)
                   UNION 
                   SELECT ops_refno, dr_nr, 
                        (SELECT op_date FROM care_encounter_op cop WHERE cop.refno = socd.ops_refno
                              AND cop.ops_code = socd.ops_code) opdate, 
                        ops_code, rvu, (rvu * multiplier) charge, 
                        rvu * fn_getPCF(encounter_nr, NOW(), '$rolearea', role_type_level, rvu, $confinetype, dr_nr) coverage
                   FROM seg_ops_chrg_dr socd 
                   WHERE encounter_nr = '$enc_nr' AND ops_refno = '$refno'
                     AND EXISTS(SELECT * FROM care_role_person crp WHERE crp.nr = socd.dr_role_type_nr
                         AND crp.role_area = '$rolearea')
                     AND EXISTS(SELECT * FROM seg_ops_serv sos WHERE sos.refno = socd.ops_refno)
                   ORDER BY opdate";
        $result = $db->Execute($strSQL);
		if ($result) {
			if ($result->RecordCount()) {                
                return $result;
			}
		}
        return false;        
    }
        
    function getRoomType() {
        $accom_type = '';
        $accom_name = '';
        $this->getAccommodationType($this->encounter_nr, $accom_type, $accom_name);
        return ($accom_type == '1') ? 'W' : 'P';
    }
    
    function getAttachedDocs($enc_nr) {
        global $db;
        
        $strSQL = "SELECT 
                    document_type,
                    accessUrl 
                  FROM
                    seg_claim_attachments 
                  WHERE !is_deleted 
                    AND encounter_nr = '$enc_nr'";
        $result = $db->Execute($strSQL);
		if ($result) {
			if ($result->RecordCount()) {                
                $docsarray = array();
                while ($row = $result->FetchRow()) {
                    $docsarray[] = $row;
                }
                return $docsarray;
			}
		}
        return false;        
    }
            
    function getActualCharges() {
        return $this->total_actual;
    }
    
    function getAmountClaimed() {
        return $this->total_claim;
    }
    
    function getPatientPaidAmount() {
        return $this->total_ppaid;
    }
    
    function getFinalDiagnosis() {
        return $this->final_diagnosis;
    }
    
    function getICDCodes() {
        return $this->icd_codes;
    }
}

class Form2Meds {
    var $drug_code;
	var $gen_name;
	var $brand;
	var $preparation;
	var $qty;
	var $unit_price;
	var $actual_charges;
	var $claim_hospital;
	var $claim_patient;
}

class Laboratories{
    var $diagnostic_type;
	var $particulars;
	var $qty;
	var $unit_price;
	var $actual_charges;
	var $claim_hospital;
	var $claim_patient;
}

class Diagnosis{
    var $fin_diagnosis;
    var $code;
    var $case_type;
}

class HospServices{
	var $charges;
	var $claim_hospital;
	var $claim_patient;
	var $reduction;
}

class Confinement{
	var $admit_dt;
	var $discharge_dt;
	var $death_dt;
	var $claim_days;
	var $admit_tm;
	var $discharge_tm;
}

class HealthPersonnel{
    var $drnr;
    var $lastname;
    var $firstname;
    var $middlename;
    var $suffix;
    var $accnum;
    var $bir_tin_num;
    var $servperformance;
    var $profcharges;
    var $claim_physician;
    var $claim_patient;
    var $inclusive_dates;
    var $role_area;
    var $attend_start;
    var $attend_end;
}

class Surgeon{
    var $drnr;
    var $lastname;
    var $firstname;
    var $middlename;
    var $suffix;
    var $accnum;
    var $bir_tin_num;
    var $servperformance;
    var $profcharges;
    var $claim_physician;
    var $claim_patient;
    var $operation_dt;
}
?>