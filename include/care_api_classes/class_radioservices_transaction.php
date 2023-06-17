<?php
// Class for updating `seg_radio_serv` and `seg_radio_servdetails` tables.

require('./roots.php');	
require_once($root_path.'include/care_api_classes/class_core.php');
//echo "class segradio";
class SegRadio extends Core {

	/**
	* Database table for the Radio Service Groups data.
	* @var string
	*/
	var $tb_radio_service_groups='seg_radio_service_groups';
	/**
	* Database table for the Radio Services data. 
	*    - includes prices of Radio Services
	* @var string
	*/
	var $tb_radio_services='seg_radio_services';

	var $tb_person = 'care_person';
	/**
	* Reference number
	* @var string
	*/
	var $refno;
	
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

	var $fld_radio_service_groups=array(
		"group_code",
		"name",
		"other_name",
		"status",
		"history",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt"
	);

	var $fld_radio_services=array(
		"service_code",
		"group_code",
		"name",
		"price_cash",
		"price_charge",
		"status",
		"history",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt"
	);

	
	/**
	* Constructor
	* @param string refno
	*/
	function SegRadio($refno=''){
		if(!empty($refno)) $this->refno=$refno;
	}
	
	function useRadioPrices(){
		$this->coretable=$this->tb_radio_services;
		$this->ref_array=$this->fld_radio_services;
	}
	
	/**
	* Sets the core object to point to seg_radio_services and corresponding field names.
	*/
	function useRadioServiceGroups(){
		$this->coretable=$this->tb_radio_service_groups;
		$this->ref_array=$this->fld_radio_service_groups;	
	}
	/**
	* Sets the core object to point to seg_radio_services and corresponding field names.
	*/
	function useRadioServices(){
		$this->coretable=$this->tb_radio_services;
		$this->ref_array=$this->fld_radio_services;
	}
	
	# ---------------------------------------------------------------------------------------
	#  RADIO_SERVICES
	# ---------------------------------------------------------------------------------------	
	
	function createRadioService($code, $name, $cash, $charge, $status, $grp)	{
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;
		$this->useRadioServices();

		$charlist="\0..\37";
		$code=addcslashes($code,$charlist);
		$name=addcslashes($name,$charlist);		
		$cash=addcslashes($cash,$charlist);
		$charge=addcslashes($charge,$charlist);
		$status=addcslashes($status,$charlist);
		$grp=addcslashes($grp,$charlist);
		
		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];
		$this->sql="INSERT INTO $this->coretable(service_code, group_code,  name, price_cash, price_charge, status, history, create_id, create_dt, modify_id, modify_dt) ". 
			"VALUES('$code', '$grp','$name', $cash, $charge, '$status', CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW())";
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;
	}
	
	function updateRadioService($excode, $code, $name, $cash, $charge, $status, $grp) {
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;

		$charlist="\0..\37";
		$excode=addcslashes($excode,$charlist);
		$code=addcslashes($code,$charlist);
		$name=addcslashes($name,$charlist);		
		$cash=addcslashes($cash,$charlist);
		$charge=addcslashes($charge,$charlist);
		$status=addcslashes($status,$charlist);
		$grp=addcslashes($grp,$charlist);
		
		$this->useRadioServices();
		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];
		$this->sql="UPDATE $this->coretable SET service_code='".addslashes($code)."',".
			"group_code='$grp',".
			"name='$name',".
			"price_cash=$cash,".
			"price_charge=$charge,".
			"status='$status',".
			"history=CONCAT(history,'Update: ',NOW(),' [$userid]\\n'),".
			"modify_id='$userid',".
			"modify_dt=NOW() ".
			"WHERE service_code='$excode'";
			
		#echo "sql update = ".$this->sql;	
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;
	}
	
	function deleteRadioService($code) {
		$this->useRadioServices();
		$this->sql="DELETE FROM $this->coretable WHERE service_code='$code'";
    return $this->Transact();
	}

	function saveRadioServiceGroup($name, $code, $other_name, $dept_nr)	{
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;
		$this->useRadioServiceGroups();

		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];
		$this->sql="INSERT INTO $this->coretable(group_code, department_nr, name, other_name, history, create_id, create_dt, modify_id, modify_dt) ". 
			"VALUES('$code', '$dept_nr', '$name', '$other_name', CONCAT('Create: ',NOW(),'\\n'),' [$userid]',NOW(),'$userid',NOW())";
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}else{
			$this->error=$db->ErrorMsg();
		}
		if ($ret)	return TRUE;
		else return FALSE;
	}
	
	/*return if the data is already exists*/
	function getServiceGroupInfo($grpname, $code, $dept_nr){
	   global $db;
		$this->sql="SELECT * FROM $this->tb_radio_service_groups 
		            WHERE name = '$grpname' AND group_code = '$code'
						AND department_nr = '$dept_nr'";
	   if ($this->result=$db->Execute($this->sql)) {
	  		$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			 return FALSE;
		}
	}
	
	/*
	* Retrieves a Radiology Service record from the database's 'seg_radio_services' table.
	* @access public
	* @param string Service code
	* @return boolean OR the Radiology Service record including the Service Group name
	*    modified by: burn Sept. 8, 2006	
	*/	
	function GetRadioServicesPrice($service_code) {
		global $db;
		$this->useRadioServices();
		$this->count=0;
		$this->sql="SELECT $this->coretable.*, ".$this->tb_radio_service_groups.".name 
		            FROM $this->coretable, $this->tb_radio_service_groups 
					WHERE $this->coretable.service_code = '$service_code' 
					  AND $this->coretable.group_code = ".$this->tb_radio_service_groups.".group_code";
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}
	
	function getRadioServices($cond="1", $sort='') {
		global $db;
		$this->useRadioServices();
		if(empty($sort)) $sort='name';
		$this->sql="SELECT * FROM $this->coretable WHERE $cond ORDER BY $sort";		
    #echo "sql = ".$this->sql;
	 if ($this->result=$db->Execute($this->sql)) {
	    if ($this->count=$this->result->RecordCount()){
				# $this->rec_count=$this->dept_count;
	      return $this->result;
			}else{
				return FALSE;
			}
		}else{
		  return FALSE;
		}
	}
	
	function getRadioServicesInfo($cond="1", $sort='') {
		global $db;
		$this->useRadioServices();
		if(empty($sort)) $sort='name';
		$this->sql="SELECT sg.name AS grpname, sg.group_code,s.* 
						FROM $this->coretable AS s,
						     $this->tb_radio_service_groups AS sg
						WHERE $cond ORDER BY $sort";		
						
    if ($this->result=$db->Execute($this->sql)) {
	    if ($this->count=$this->result->RecordCount()){
				# $this->rec_count=$this->dept_count;
	      return $this->result;
			}else{
				return FALSE;
			}
		}else{
		  return FALSE;
		}
	}
	
	function getRadioServiceGroups2($cond="1", $sort='') {
		global $db;
		$this->useRadioServiceGroups();
		if(empty($sort)) $sort='name';
		$this->sql="SELECT * FROM $this->tb_radio_service_groups WHERE $cond ORDER BY $sort";		
		#echo "sql = ".$this->sql;
    if ($this->result=$db->Execute($this->sql)) {
	    if ($this->count=$this->result->RecordCount()){
				# $this->rec_count=$this->dept_count;
	      return $this->result;
			}else{
				return FALSE;
			}
		}else{
		  return FALSE;
		}
	}
	
}//end of class SegRadio
?>