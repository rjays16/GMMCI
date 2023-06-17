<?php

	/**
	* Table name for encounter (admission) data
	* @var string
	*/
    var $tb_enc='care_encounter';
	/**
	* Table name for encounter classes
	* @var string
	*/
	var $tb_ec='care_class_encounter';
	/**
	* Table name for person (registration) data
	* @var string
	*/
	var $tb_person='care_person';
	/**
	* Table name for department general data
	* @var string
	*/
	var $tb_dept='care_department';

	/**
	* Database table for the region address data.
	* @var string
	* burn added: March 10, 2007
	*/
	var $tb_regions='seg_regions';
	/**
	* Database table for the province address data.
	* @var string
	* burn added: March 10, 2007
	*/
	var $tb_provinces='seg_provinces';
	/**
	* Database table for the municipality/city address data.
	* @var string
	* burn added: March 10, 2007
	*/
	var $tb_municity='seg_municity';
	/**
	* Database table for the barangay address data.
	* @var string
	* burn added: March 10, 2007
	*/
	var $tb_barangays='seg_barangays';

	/**
	*	Returns the personnel and department's information where a user belongs
	* 	@access public
	*	@param string, encounter number
	*	return mixed adodb record object or boolean FALSE
	*	burn added: March 10, 2007
	*/
	function getEncounterInfo($encounter_nr){
		global $db;
		
		$this->sql ="SELECT cp.pid, enc.encounter_nr, 
							cp.name_last, cp.name_first, cp.name_2, cp.name_3, cp.name_middle,
							enc.encounter_date AS er_opd_datetime, 
							dept.name_formal,
							cp.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
							cp.phone_1_nr, cp.phone_2_nr, cp.cellphone_1_nr, cp.cellphone_2_nr, cp.sex, cp.civil_status,
							cp.date_birth, cp.place_birth, cp.citizenship, cp.religion, cp.occupation, 
							cp.mother_fname, cp.mother_maidenname, cp.mother_mname, cp.mother_lname,
							cp.father_fname, cp.father_mname, cp.father_lname,
							cp.spouse_name, cp.guardian_name,
							enc.informant_name, enc.info_address, enc.relation_informant, 
							enc.consulting_dr AS attending_physician,
							enc.modify_id AS admitting_clerk,
							enc.create_id AS admitting_clerk_er_opd 
						FROM $this->tb_person AS cp, $this->tb_enc AS enc, 
							$this->tb_dept AS dept,
							$this->tb_barangays AS sb, $this->tb_municity AS sm, 
							$this->tb_provinces AS sp, $this->tb_regions AS sr 
						WHERE enc.encounter_nr='$encounter_nr'
							AND cp.pid=enc.pid AND dept.nr=enc.current_dept_nr
							AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr 
							AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr " ;
#echo "getEncounterInfo : this->sql = '".$this->sql."' <br> \n";
		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}		
	}

?>
