<?php

require_once("core.class.php");

class Doctor extends Core{
   var $person_table = 'care_person';
   var $user_table = 'care_users';
	
	var $appointment_table = 'seg_appointment';
	
	var $fld_appointments=array(
		"apptdate",
		"appttime",   
		"client",
		"purpose",
		"place",
		"dr_nr",
		"create_id",
		"create_dt",
		"modify_id",
		"modify_dt",
		"history"
	);
	
	/* Constructor */
   function Doctor() {
      $this->coretable = "care_personell";
      $this->result = NULL;
      $this->count = 0;
   }
	
	function useSegAppointment(){
		$this->coretable=$this->appointment_table;
		$this->ref_array=$this->fld_appointments;
	}
		
   function getDoctorUserInfo($username, $password) {
      global $db;

      $this->sql="SELECT u.*, p.*
						FROM $this->user_table AS u
						INNER JOIN $this->coretable AS p
						ON u.personell_nr=p.nr
						WHERE job_type_nr=1
						AND login_id='$username'
						AND password='$password'";

      if ($this->result=$db->Execute($this->sql)){
		   $this->count = $this->result->RecordCount();
			if ($this->count)
				return $this->result->FetchRow();
				#return $this->result;
			else
				return FALSE;
		}else{
			return FALSE;
		}		
   }/* end of function getDoctorUserInfo */
	
	function getPatients($doctor_id) {
      global $db;

      $this->sql="SELECT e.*, p.*
					   FROM care_encounter AS e
						INNER JOIN care_person AS p
						ON e.pid = p.pid
						WHERE (current_att_dr_nr = '$doctor_id' || consulting_dr_nr = '$doctor_id')";

      if ($this->result=$db->Execute($this->sql)){
		   $this->count = $this->result->RecordCount();
			if ($this->count)
				#return $this->result->FetchRow();
				return $this->result;
			else
				return FALSE;
		}else{
			return FALSE;
		}		
   }/* end of function getPatients */
	
	function getPatientDiagnosis($encounter_nr) {
      global $db;

      $this->sql="SELECT i.diagnosis_code, i.description, d.* 
						FROM care_encounter_diagnosis AS d
						INNER JOIN care_icd10_en AS i
						ON d.code = i.diagnosis_code
						WHERE d.encounter_nr='$encounter_nr'";

      if ($this->result=$db->Execute($this->sql)){
		   $this->count = $this->result->RecordCount();
			if ($this->count)
				#return $this->result->FetchRow();
				return $this->result;
			else
				return FALSE;
		}else{
			return FALSE;
		}		
   }/* end of function getPatientDiagnosis */
	
	function getAppointments($date, $doctor) {
      global $db;

      $this->sql="SELECT * FROM seg_appointment 
		            WHERE apptdate='$date' 
						AND dr_nr = '$doctor'
						ORDER BY appttime";
#echo "sql = ".$this->sql;
      if ($this->result=$db->Execute($this->sql)){
		   $this->count = $this->result->RecordCount();
			if ($this->count)
				#return $this->result->FetchRow();
				return $this->result;
			else
				return FALSE;
		}else{
			return FALSE;
		}		
   }/* end of function getAppointments */
	
	function getSpecificAppointment($id, $doctor) {
      global $db;

      $this->sql="SELECT * FROM seg_appointment WHERE id='$id' AND dr_nr='$doctor'";

      if ($this->result=$db->Execute($this->sql)){
		   $this->count = $this->result->RecordCount();
			if ($this->count)
				return $this->result->FetchRow();
				#return $this->result;
			else
				return FALSE;
		}else{
			return FALSE;
		}		
   }/* end of function getAppointments */

	
}	/* end of class Doctor */
?>
