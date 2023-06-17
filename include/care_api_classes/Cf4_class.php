<?php
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');

class Cf4_class
{
    public $enc_no;
    public function __construct($encounter_nr)
    {
        $this->enc_no = $encounter_nr;
    }

    public function doctorsAction()
    {
        global $db;
        $rowindex = 0;
        $detail_1 = 1;
        $doc_action_sql = "SELECT
                              sccitw.date_action,
                              sccitw.doctor_action
                            FROM
                              seg_cf4_course_in_the_ward AS sccitw
                            WHERE sccitw.encounter_nr =".$this->enc_no."
                            AND sccitw.is_deleted != 1
                            ORDER BY sccitw.date_action ASC";
        $doc_action_res = $db->Execute($doc_action_sql);
//        var_dump($doc_action_res);die;
        while ($row = $doc_action_res->FetchRow()){
            $data[$rowindex] = array(
                'requestDate' => date('m-d-Y', strtotime($row['date_action'])),
                'remarks' => $row['doctor_action'],
                'detail_1' => $detail_1
            );
            $rowindex++;
            $detail_1++;
        }
        return $data;
    }

    public function  medicine()
    {
        global $db;
        $rowindex = 1;
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
            if($row['is_pndf'] == 1){
                $drug_code = $row['drug_code'];
                $code_sql = "SELECT
                    spm.form_code,
                    spm.strength_code
                FROM
                    seg_phil_medicine AS spm
                WHERE spm.drug_code ='$drug_code'";;
                $code_res = $db->Execute($code_sql);
                while ($code_row = $code_res->FetchRow()){
                    $form_code = $code_row['form_code'];
                    $strength_code = $code_row['strength_code'];

                    $form_sql = "SELECT
                  spmf.form_desc
                FROM
                  seg_phil_medicine_form AS spmf
                WHERE spmf.form_code ='$form_code'";
                    $form_res = $db->Execute($form_sql);
                    while ($form_row = $form_res->FetchRow()){
                        $form_desc = $form_row['form_desc'];
                    }

                    $strength_sql = "SELECT
                  spms.strength_desc
                FROM
                  seg_phil_medicine_strength AS spms
                WHERE spms.strength_code = '$strength_code'";
                    $strength_res = $db->Execute($strength_sql);
                    while ($strength_row = $strength_res->FetchRow()){
                        $strength_desc = $strength_row['strength_desc'];
                    }
                }

                $data[$rowindex] = array(
                    'generic_name' => $row['generic'],
                    'total_cost' => $row['cost'],
                    'quantity' => $row['quantity'],
                    'route' => $row['route'],
                    'frequency' => $row['frequency'],
                    'strength_desc' => $strength_desc,
                    'form_desc' => $form_desc,
                );
                $rowindex++;
            }else{
                $data[$rowindex] = array(
                    'generic_name' => $row['generic'],
                    'total_cost' => $row['cost'],
                    'quantity' => $row['quantity'],
                    'route' => $row['route'],
                    'frequency' => $row['frequency'],
                    'strength_desc' => "NONE",
                    'form_desc' => "NONE",
                );
                $rowindex++;
            }

        }

        return $data;
    }

    public function forHeader()
    {
        $rowindex = 1;
        $data[$rowindex] = array(
            'for_header' => $rowindex
        );
        return $data;
    }

    /**
     * Get Doctor Attending Physician name.
     * params $bill_nr String
     * return $name String
     */
    public function getDoctor()
    {
        global $db;
        $query = "SELECT ce.encounter_type FROM care_encounter ce
                    WHERE ce.encounter_nr = '$this->enc_no'";
        $execute = $db->Execute($query);
        $enc_type = $execute->FetchRow();

        $doc_type = $enc_type['encounter_type'] == '3' ? "seda.is_attending = '1'" : "seda.is_consulting = '1'";

        $strSQL = "SELECT
                  seda.is_attending,
                  seda.is_consulting,
                  seda.encounter_nr,
                  cp.name_first,
                  cp.name_last,
                  cp.name_middle,
                  cp.suffix 
                FROM
                  seg_encounter_dr_add seda 
                  LEFT JOIN care_personell cpl 
                    ON cpl.nr = seda.dr_nr 
                  INNER JOIN care_person cp 
                    ON cp.pid = cpl.pid 
                WHERE seda.encounter_nr = '$this->enc_no' AND $doc_type";

        $execute = $db->Execute($strSQL);
        $doctor = $execute->FetchRow();

        $honorifics = $doctor ? 'DR.': '';
        $lastname = $doctor ? $doctor['name_last'].', ': '';
        $name = $honorifics .' '. $lastname .''. $doctor['name_first'] .' '. $doctor['name_middle'] .' '. $doctor['suffix'];

        return $name;
    }

    public function getMisc()
    {
        global $db;
        $misc = "";
        $misc_sql = "SELECT 
            smod.`description`
            FROM
              seg_misc_ops AS smo 
              LEFT JOIN seg_misc_ops_details AS smod 
                ON smo.`refno` = smod.`refno` 
            WHERE smo.`modify_dt` IN 
              (SELECT 
                MAX(smo.`modify_dt`) 
              FROM
                seg_misc_ops AS smo)
                AND smo.`encounter_nr` = '$this->enc_no'";
        $misc_res = $db->Execute($misc_sql);
        while ($row = $misc_res->FetchRow()){
            if ($misc){
                $misc .= '<br>' .$row['description'];
            }else{
                $misc = $row['description'];
            }
        }
        return $misc;
    }
    public function getResult()
    {
        global $db;
        $res_code = null;

        $code_sql = "SELECT 
                      ser.`result_code` 
                    FROM
                      seg_encounter_result AS ser
                    WHERE ser.`encounter_nr` = '$this->enc_no'";
        $course_res = $db->Execute($code_sql);
        while ($row = $course_res->FetchRow()) {
            $res_code = $row['result_code'];
        }
        return $res_code;
    }

    public function getDisposition()
    {
        global $db;
        $dis_code = null;
        $code_sql = "SELECT 
                          sed.`disp_code` 
                        FROM
                          seg_encounter_disposition_refer AS sed 
                            WHERE sed.`encounter_nr` = '$this->enc_no'";
        $code_res = $db->Execute($code_sql);
        while ($row = $code_res->FetchRow()) {
            $dis_code = $row['disp_code'];
        }
        return $dis_code;
    }

    /**
     * Get Admitting Doctor
     * @return string
     */
    public function getDoctorAdmitting()
    {
        global $db;
        $strSQL = "SELECT
                  seda.is_admitting,
                  seda.encounter_nr,
                  cp.name_first,
                  cp.name_last,
                  cp.name_middle,
                  cp.suffix 
                FROM
                  seg_encounter_dr_add seda 
                  LEFT JOIN care_personell cpl 
                    ON cpl.nr = seda.dr_nr 
                  INNER JOIN care_person cp 
                    ON cp.pid = cpl.pid 
                WHERE seda.encounter_nr = '$this->enc_no' AND seda.is_admitting = '1'";

        $execute = $db->Execute($strSQL);
        $doctor = $execute->FetchRow();

        $honorifics = $doctor ? 'DR.': '';
        $lastname = $doctor ? $doctor['name_last'].', ': '';
        $name = $honorifics .' '. $lastname .''. $doctor['name_first'] .' '. $doctor['name_middle'] .' '. $doctor['suffix'];

        return $name;
    }
}
