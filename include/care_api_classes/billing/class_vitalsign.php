<?php
  

require_once($root_path.'include/care_api_classes/class_core.php');

#created by Bryan on Feb 7, 09
class SegVitalsign extends Core {
 
    # database table 
    var $vital_tb='seg_encounter_vitalsigns'; 
    var $tb_vitunit = 'seg_encounter_vitalsigns_unit';
    
    
    var $fld_vital = array(
        "encounter_nr",
        "date",
        "pid",
        "systole",
        "diastole",
        "temp",
        "weight",
        "resp_rate",
        "pulse_rate",
        "bp_unit",
        "temp_unit",
        "weight_unit",
        "resp_rate_unit",
        "pulse_rate_unit",
        "history",
        "create_id",
        "create_time",
        "modify_id",
        "modify_time"
    );   
    
    function prepareVitals() {
        $this->coretable = $this->vital_tb;
        $this->setRefArray($this->fld_vital);
    }
    
    function fetchVitalsDetails($id){
        global $db;
        
        $this->sql = "SELECT * FROM $this->vital_tb WHERE vitalsign_no='$id'
                        ORDER BY date asc";
        $result = $db->Execute($this->sql);
        if($result)
            return $result;
        else return 0;
    
    }
    
    function fetchVitalsbyEncandPid($pid,$enc_nr){
        global $db;
        
        $this->sql = "SELECT * FROM $this->vital_tb WHERE pid='$pid' AND encounter_nr='$enc_nr'
                        ORDER BY date asc";
        $result = $db->Execute($this->sql);
        if($result)
            return $result;
        else return 0;
    
    }  
    
    function getOldestVitalDetailsbyPid($pid,$enc_nr) {
        global $db;
        
        $this->sql = "SELECT * FROM $this->vital_tb WHERE pid='$pid' AND encounter_nr='$enc_nr' 
                        ORDER BY date asc";
        $result = $db->Execute($this->sql);
        if($result)
            return $result;
        else return 0;
    }  
    
    function deleteVitalSign($id){
        global $db;
        
        $this->sql = "DELETE FROM $this->vital_tb WHERE vitalsign_no='$id'";
        $result = $db->Execute($this->sql);
        if($result)
            return $result;
        else return 0;
    
    }
    
    function getUnitName ($unit_id) {
        global $db;
        
        $this->sql = "SELECT * FROM $this->tb_vitunit WHERE unit_id=$unit_id";
        $result = $db->Execute($this->sql);
        if($result)
            return $result->FetchRow();
        else return 0;
    }
    
    /**
    * added by Omick, February 26 2009
    * @internal This function selects the latest vital sign
    * @access public
    * @author Omick <omick16@gmail.com>
    * @name db
    * @global array instance of a db connection
    * @package include
    * @subpackage care_api_classes
    * @param string $pid is the unique identification of patient
    * @param string $encounter_nr is the latest encounter of a patient
    * @return bool returns a success or fail in the query   
    */
    function get_latest_vital_signs($pid, $encounter_nr) {
      global $db;
      $this->sql = "SELECT systole, diastole, temp, resp_rate, pulse_rate FROM $this->vital_tb WHERE
                    pid='{$pid}' AND encounter_nr='{$encounter_nr}' ORDER BY date DESC";
      $result = $db->SelectLimit($this->sql,1);
      if ($result->RecordCount()) {
        return $result->FetchRow();
      }
      else {
        return false;
      }
    }
    
    function add_new_vital_sign($data) {
      extract($data);
      global $db;
      $this->sql = "INSERT INTO seg_encounter_vitalsigns(encounter_nr,
                                                         date,
                                                         pid,
                                                         systole,
                                                         diastole,
                                                         temp,
                                                         weight,
                                                         resp_rate,
                                                         pulse_rate,
                                                         history,
                                                         modify_id,
                                                         modify_dt,
                                                         create_id,
                                                         create_dt) VALUES ('$encounter_nr',
                                                                            $date,
                                                                            '$pid',
                                                                            $systole,
                                                                            $diastole,
                                                                            $temp,
                                                                            $weight,
                                                                            $resp_rate,
                                                                            $pulse_rate,
                                                                            $history,
                                                                            '$modify_id',
                                                                            $modify_dt,
                                                                            '$create_id',
                                                                            $create_dt)";
      $db->Execute($this->sql);
      if ($db->Affected_Rows() > 0) {
        return true;
      }
      else {
        return false;
      }                                                                            
                                                                            
    }
    
    function get_all_vital_signs($pid, $encounter_nr) {
      global $db;
      
      $this->result = $this->fetchVitalsbyEncandPid($pid, $encounter_nr);
      if ($this->result->RecordCount()) {
        $vital_signs = array();
        while ($row = $this->result->FetchRow()) {
          $vital_signs[] = array('date_taken' => date('m/d/Y', strtotime($row['date'])),
                                 'temperature' => $row['temp'],
                                 'pulse_rate' => $row['pulse_rate'],
                                 'respiratory_rate' => $row['resp_rate'],
                                 'blood_pressure' => $row['systole'].'/'.$row['diastole']);
        }
        return $vital_signs;
      }
      else {
        return false;
      }
    }

} 

?>
