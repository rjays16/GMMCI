<?php
// Class for updating `care_role_person` table.
// Created: 10-2-2006 (Bernard Klinch S. Clarito II)

require('./roots.php');	
require_once($root_path.'include/care_api_classes/class_core.php');

class RolePerson extends Core {

	/**
	* Database table for the role person transaction
	* @var string
	*/
	var $tb_role_person='care_role_person';

	/**
	* SQL query result. Resulting ADODB record object.
	* @var object
	*/
	var $result;
	
	/**
	* Resulting record count
	* @var int
	*/
	var $count;

	/**
	* Fieldnames of the care_role_person table.
	* @var array
	*/	
	var $fld_role_person=array(
		"nr",
		"group_nr",
		"job_type_nr",
		"role",
		"name",
		"LD_var",
		"status",
		"modify_id",
		"modify_time",
		"create_id",
		"create_time"
		);

	/**
	* Constructor
	* @param string refno
	*/
	function RolePerson(){
		$this->setTable($this->tb_role_person);
		$this->setRefArray($this->tabfields); # i dnt think we still need dis stmt..uhmm??
	}
	
	/**
	* Sets the core object to point to care_role_person and corresponding field names.
	*/
	function useRolePerson(){
		$this->coretable=$this->tb_role_person;
		$this->ref_array=$this->fld_role_person;
	}

	/**
	* Creates a Role Name of a Person entry in the database's 'care_role_person' table. 
	* @access public
	* @param int Group number
	* @param int Job type number
	* @param string Role
	* @param string Name
	* @param string LD var
	* @param string Status
	* @param string Encoder id
	* @return boolean
	*    documented by: burn Oct. 2, 2006
	*/
	function insertRoleNameOfPerson(
							$group_nr,   # group number
							$job_type_nr,   # job type number
							$role,   # role 
							$name,   # name
							$LD_var,   # LD var
							$status,   # status
							$encoder_id) {	# Encoder id
							
		$this->useRolePerson();
		$this->sql = "INSERT INTO $this->coretable (group_nr, job_type_nr, role, name, LD_var, status, modify_id, modify_time, create_id, create_time)    
				VALUES ($group_nr, $job_type_nr, '$role', '$name', '$LD_var', '$status', '$encoder_id', NOW(), '$encoder_id', NOW())"; 						
		return $this->Transact();
	}
	
	/**
	* Delete a Role Name of a Person entry in the database's 'care_role_person' table. 
	* @access public
	* @param int Unique reference number of the entry
	* @return boolean.
	*    documented by: burn Oct. 2, 2006
	*/
	function deleteRoleNameOfPerson($nr){
		$this->useRolePerson();
		$this->sql="DELETE FROM $this->coretable WHERE nr=$nr";
     	return $this->Transact();
	}
		
	/**
	* Updates a Role Name of a Person entry in the database's 'care_role_person' table. 
	* @access public
	* @param int Unique reference number of the entry
	* @param int Group number
	* @param int Job type number
	* @param string Role
	* @param string Name
	* @param string LD var
	* @param string Status
	* @param string Encoder id
	* @return boolean
	*    documented by: burn Oct. 2, 2006
	*/
	function updateRoleNameOfPerson(
                            $nr,
							$group_nr,
							$job_type_nr,
							$role,
							$name,
							$LD_var,
							$status,
							$encoder_id) {	// Encoder id
							
		$this->useRolePerson();
		$this->sql = "UPDATE $this->coretable 
		              SET group_nr=$group_nr, 
		                  job_type_nr=$job_type_nr, 
						  role='$role', 
						  name='$name', 
						  LD_var='$LD_var', 
						  status='$status', 
						  modify_id='$encoder_id', 
						  modify_time=NOW() 
					  WHERE nr=$nr"; 
		return $this->Transact();
	}	
	/**
	* Retrieves a Role Name of a Person entry from the database's 'care_role_person' table. 
	* @access public
	* @param int Unique reference number of the entry
	* @return boolean OR the Role Name of a Person information/record
	*    documented by: burn Oct. 2, 2006
	*/
	function getRoleNameofPerson($nr) {
		global $db;
		$this->useRolePerson();
		$this->count=0;
		$this->sql="SELECT * FROM $this->coretable WHERE nr=$nr";
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}	
	/**
	* Checks if a Role Name of a Person entry exists based on the reference number given.
	*   - uses the 'care_role_person' table. 
	* @access public
	* @param int/string Unique reference number of the entry
	* @return boolean
	*    documented by: burn Oct. 2, 2006
	*/
	function roleNameofPersonExists($this_searchKey){
		global $db;
		$this->useRolePerson();	
		if (ctype_digit($this_searchKey)) {
		   # numeric search
           $this->sql="SELECT * FROM $this->coretable WHERE nr=$this_searchKey";
        } else {
		   # string search
           $this->sql="SELECT * FROM $this->coretable WHERE name='$this_searchKey'";
        }
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}
}//end of class RolePerson
?>