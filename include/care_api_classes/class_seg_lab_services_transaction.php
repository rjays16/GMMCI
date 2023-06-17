<?php
// Class for updating `seg_lab_serv` and `seg_lab_servdetails` tables.
// Created: 9-5-2006 (Bernard Klinch S. Clarito II :-) )

require('./roots.php');	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegLab extends Core {

	/**
	* Database table for the Laboratory Service Groups data.
	* @var string
	*/
	var $tb_lab_service_groups='seg_lab_service_groups';
	/**
	* Database table for the Laboratory Services data. 
	*    - includes prices of Laboratory Services
	* @var string
	*/
	// var $tb_pharma_prices='seg_pharma_prices';
	var $tb_lab_services='seg_lab_services';
	/**
	* Database table for the Laboratory Parameters data.
	* @var string
	*/
	var $tb_lab_params='seg_lab_params';

	/**
	* Database table for the laboratory transaction details
	* @var string
	*/
	var $tb_lab_servdetails='seg_lab_servdetails';	
	var $tb_pharma_products='care_pharma_products_main';

	/**
	* Database table for the laboratory transaction information
	* @var string
	*/
	// var $tb_lab_retail='seg_lab_retail';
	var $tb_lab_serv='seg_lab_serv';
	
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

	/**
	* Fieldnames of the seg_lab_service_groups table.
	* @var array
	*/		
	var $fld_lab_service_groups=array(
		"group_id",
		"department_nr",
		"name",
		"sort_nr",
		"status",
		"history",
		"modify_id",
		"modify_time",
		"create_id",
		"create_time"
	);
/*
	var $fld_pharma_prices=array(
		"bestellnum",
		"ppriceppk",
		"chrgrpriceppk",
		"cshrpriceppk",
		"modify_id",
		"modify_date",
		"create_id",
		"create_date"
	);
*/
	/**
	* Fieldnames of the seg_lab_services table.
	* @var array
	*/		
	var $fld_lab_services=array(
		"service_id",
		"group_id",
		"name",
		"cshrpriceppk",
		"chrgrpriceppk",
		"sort_nr",
		"status",
		"history",
		"modify_id",
		"modify_time",
		"create_id",
		"create_time"
	);
	/**
	* Fieldnames of the seg_lab_params table.
	* @var array
	*/		
	var $fld_lab_params=array(
		"param_id",
		"service_id",
		"name",
		"id",
		"msr_unit",
		"median",
		"hi_bound",
		"lo_bound",
		"hi_critical",
		"lo_critical",
		"hi_toxic",
		"lo_toxic",
		"status",
		"remarks",
		"history",
		"modify_id",
		"modify_time",
		"create_id",
		"create_time"
	);

	/**
	* Fieldnames of the seg_lab_serv table.
	* @var array
	*/	
	var $fld_lab_serv=array(
		"refno",
		"department_nr",
		"purchasedte",
		"encounter_nr",
		"is_cash",
		"create_id",
		"create_time",
		"modify_id",
		"modify_time"
		);

	/**
	* Fieldnames of the seg_lab_servdetails table.
	* @var array
	*/	
	var $fld_lab_servdetails=array(
		"refno",
		"srvcitm_code",   # "bestellnum",
		"entrynum",
		"qty",
		"srvcitm_price",   # "rpriceppk"
	);

	/**
	* Constructor
	* @param string refno
	*/
	function SegLab($refno=''){
		if(!empty($refno)) $this->refno=$refno;
		$this->setTable($this->tb_lab_serv);
		$this->setRefArray($this->tabfields);
	}
	
	/**
	* Sets the core object to point to seg_lab_serv and corresponding field names.
	*/
	function useLabRetail(){
		$this->coretable=$this->tb_lab_serv;
		$this->ref_array=$this->fld_lab_serv;
	}
	
	/**
	* Sets the core object to point to seg_lab_servdetails and corresponding field names.
	*/
	function useLabRdetails(){
		$this->coretable=$this->tb_lab_servdetails;
		$this->ref_array=$this->fld_lab_servdetails;
	}

	/**
	* Sets the core object to point to seg_lab_services and corresponding field names.
	*    burn comment: not sure with this????
	*/
	function useLabPrices(){
		$this->coretable=$this->tb_lab_services;
		$this->ref_array=$this->fld_lab_services;
	}
	
	/**
	* Sets the core object to point to seg_lab_services and corresponding field names.
	*/
	function useLabServiceGroups(){
		$this->coretable=$this->tb_lab_service_groups;
		$this->ref_array=$this->fld_lab_service_groups;	
	}
	/**
	* Sets the core object to point to seg_lab_services and corresponding field names.
	*/
	function useLabServices(){
		$this->coretable=$this->tb_lab_services;
		$this->ref_array=$this->fld_lab_services;
	}
	/**
	* Sets the core object to point to seg_lab_services and corresponding field names.
	*/
	function useLabParams(){
		$this->coretable=$this->tb_lab_params;
		$this->ref_array=$this->fld_lab_params;	
	}
	
	/**
	* Insert new Laboratory Service Group info in the database 'seg_lab_service_groups' table. The data is
	*    contained in associative array and passed by reference. The array keys must correspond to the 
	*    field names contained in $fld_lab_service_groups.
	* @access public
	* @param array Data to save. By reference.
	* @return boolean
	*    created by: burn Sept. 6, 2006
	* @param how to use this function? caller function: saveLabServiceGroupsInfoFromArray($HTTP_POST_VARS);
	*/
	function saveLabServiceGroupsInfoFromArray(&$data){
	   global $HTTP_SESSION_VARS;
	   $this->useLabServiceGroups();
	   $this->data_array=$data;
	   //$this->data_array['status']='';
	   $this->data_array['history']="Create: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";
	   $this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
	   $this->data_array['modify_time']=date('Y-m-d H:i:s');
	   $this->data_array['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
	   $this->data_array['create_time']=date('Y-m-d H:i:s');
	   return $this->insertDataFromInternalArray();	   
	}
	/**
	* Updates the Laboratory Service Group's data in the database 'seg_lab_service_groups' table. The data 
	*    is contained in associative array and passed by reference. The array keys must correspond to the 
	*    field names contained in $fld_lab_service_groups. 
	* Only the keys of data to be updated must be present in the passed array.
	* @access public
	* @param int Laboratory Service Group's record nr (primary key)
	* @param array Data passed as reference
	* @return boolean
	*    created by: burn Sept. 6, 2006
	* @param how to use this function? caller function: updateLabServiceGroupsInfoFromArray($group_id,$HTTP_POST_VARS);
	*/
	function updateLabServiceGroupsInfoFromArray($pass_group_id,&$data){
	   global $HTTP_SESSION_VARS;
	   $this->useLabServiceGroups();
	   $this->data_array=$data;
	   // remove probable existing array data to avoid replacing the stored data
	   if(isset($this->data_array['group_id'])) unset($this->data_array['group_id']);
	   if(isset($this->data_array['create_id'])) unset($this->data_array['create_id']);
	   // set the where condition
	   $this->where="group_id=$pass_group_id";
	   $this->data_array['history']=$this->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
	   $this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
	   $this->data_array['modify_time']=date('Y-m-d H:i:s');
	   return $this->updateDataFromInternalArray($pass_group_id);
	}
	/**
	* Insert new Laboratory Service info in the database 'seg_lab_services' table. The data is contained in 
	*    associative array and passed by reference. The array keys must correspond to the field names 
	*    contained in $fld_lab_services.
	* @access public
	* @param array Data to save. By reference.
	* @return boolean
	*    created by: burn Sept. 6, 2006
	* @param how to use this function? caller function: saveLabServiceInfoFromArray($HTTP_POST_VARS);
	*/
	function saveLabServiceInfoFromArray(&$data){
	   global $HTTP_SESSION_VARS;
	   $this->useLabServices();
	   $this->data_array=$data;
	   //$this->data_array['status']='';
	   $this->data_array['history']="Create: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";
	   $this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
	   $this->data_array['modify_time']=date('Y-m-d H:i:s');
	   $this->data_array['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
	   $this->data_array['create_time']=date('Y-m-d H:i:s');
	   return $this->insertDataFromInternalArray();	   
	}
	/**
	* Updates the Laboratory Service's data in the database 'seg_lab_services' table. The data is contained 
	*    in associative array and passed by reference. The array keys must correspond to the field names 
	*    contained in $fld_lab_services. 
	* Only the keys of data to be updated must be present in the passed array.
	* @access public
	* @param int Laboratory Service's record nr (primary key)
	* @param array Data passed as reference
	* @return boolean
	*    created by: burn Sept. 6, 2006
	* @param how to use this function? caller function: updateLabServiceInfoFromArray($service_id,$HTTP_POST_VARS);
	*/
	function updateLabServiceInfoFromArray($pass_service_code,&$data){
	   global $HTTP_SESSION_VARS;
	   $this->useLabServices();
	   $this->data_array=$data;
	   // remove probable existing array data to avoid replacing the stored data
	   if(isset($this->data_array['service_code'])) unset($this->data_array['service_code']);
	   if(isset($this->data_array['create_id'])) unset($this->data_array['create_id']);
	   // set the where condition
	   $this->where="service_code='$pass_service_code'";
	   $this->data_array['history']=$this->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
	   $this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
	   $this->data_array['modify_time']=date('Y-m-d H:i:s');
  	   return $this->updateDataFromInternalArray($pass_service_id);
	}
	/**
	* Insert new Laboratory Parameter info in the database 'seg_lab_params' table. The data is contained in 
	*    associative array and passed by reference. The array keys must correspond to the field names 
	*    contained in $fld_lab_services.
	* @access public
	* @param array Data to save. By reference.
	* @return boolean
	*    created by: burn Sept. 6, 2006
	* @param how to use this function? caller function: saveLabParamsInfoFromArray($HTTP_POST_VARS);
	*/
	function saveLabParamsInfoFromArray(&$data){
	   global $HTTP_SESSION_VARS;
	   $this->useLabParams();
	   $this->data_array=$data;
	   //$this->data_array['status']='';
	   $this->data_array['history']="Create: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";
	   $this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
	   $this->data_array['modify_time']=date('Y-m-d H:i:s');
	   $this->data_array['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
	   $this->data_array['create_time']=date('Y-m-d H:i:s');
	   return $this->insertDataFromInternalArray();	   
	}
	/**
	* Updates the Laboratory Parameter's data in the database 'seg_lab_params' table. The data is contained 
	*    in associative array and passed by reference. The array keys must correspond to the field names 
	*    contained in $fld_lab_services. 
	* Only the keys of data to be updated must be present in the passed array.
	* @access public
	* @param int Laboratory Parameter's record nr (primary key)
	* @param array Data passed as reference
	* @return boolean
	*    created by: burn Sept. 6, 2006
	* @param how to use this function? caller function: updateLabParamsInfoFromArray($param_id,$HTTP_POST_VARS);
	*/
	function updateLabParamsInfoFromArray($pass_param_id,&$data){
	   global $HTTP_SESSION_VARS;
	   $this->useLabParams();
	   $this->data_array=$data;
	   // remove probable existing array data to avoid replacing the stored data
	   if(isset($this->data_array['param_id'])) unset($this->data_array['param_id']);
	   if(isset($this->data_array['create_id'])) unset($this->data_array['create_id']);
	   // set the where condition
	   $this->where="param_id=$pass_param_id";
	   $this->data_array['history']=$this->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
	   $this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
	   $this->data_array['modify_time']=date('Y-m-d H:i:s');
	   return $this->updateDataFromInternalArray($pass_param_id);
	}
	
	function GetProductPrice($bestellnum) {
		global $db;
		$this->useLabPrices();
		$this->count=0;
		$this->sql="SELECT * FROM $this->coretable WHERE bestellnum='$bestellnum'";
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}
	
	
	/**
	* Creates a Laboratory Service Transaction record into the database 'seg_lab_serv' table.
	* @access public
	* @param string Reference number
	* @param int Department number/id
	* @param Date of service.
	* @param int Patient encounter number
	* @param Payment mode
	* @param Encoder id
	* @return boolean
	*    created by: burn Sept. 6, 2006
	*/
	function CreateLabTransaction(
							$refno, 	    // Unique no. identifying transaction.
							$dept_nr, 	    // Department number/id
							$serv_dt, 	// Date of service.
							$encounter_nr, 	// Patient encounter number
							$is_cash, 		// Payment mode, cash or charge
							$encoder_id) {	// Encoder id
		$this->useLabRetail();
		$this->sql = "INSERT INTO $this->coretable (refno, department_nr, serv_dt, encounter_nr, is_cash, modify_id, modify_time, create_id, create_time)
                         VALUES ('$refno', $dept_nr, '$serv_dt', $encounter_nr, $is_cash, '$encoder_id', CURRENT_TIMESTAMP, '$encoder_id', CURRENT_TIMESTAMP)";
		#echo "CreateLabTransaction = ".$this->sql;
		return $this->Transact();
	}
	
	/**
	* Deletes a Laboratory Service Transaction record from the database 'seg_lab_serv' table.
	* @access public
	* @param string Reference number
	* @return boolean.
	*/
	function DeleteLabTransaction($refno){
		$this->useLabRetail();
		$this->sql="DELETE FROM $this->coretable WHERE refno='$refno'";
     return $this->Transact();
	}

	/**
	* Updates Laboratory Service Transaction record in the database 'seg_lab_serv' table.
	* @param string Reference number
	* @param int Department number/id
	* @param Date of service.
	* @param int Patient encounter number
	* @param Payment mode
	* @param Encoder id
	* @return boolean
	*    created by: burn Sept. 6, 2006
	*/	
	function UpdateLabTransaction(
							$refno, 	    // Unique no. identifying transaction.
							$newrefno,      // New Unique no. identifying transaction.
							$dept_nr, 	    // Department number/id
							$serv_dt, 	// Date of service.
							$encounter_nr, 	// Patient encounter number
							$is_cash, 		// Payment mode, cash or charge
							$encoder_id) 	// Encoder id

	{
		$this->useLabRetail();
		$this->sql = "UPDATE $this->coretable SET " .
							"refno='$newrefno', " .
							"department_nr=$dept_nr, ".
							"serv_dt='$serv_dt', " . 
							"encounter_nr=$encounter_nr, " .
							"is_cash=".$is_cash.", " .	
							"modify_id='$encoder_id', " .
							"modify_timestamp=CURRENT_TIMESTAMP " .
							"WHERE refno = '$refno'";				
		return $this->Transact();
	}
	
	/**
	* Checks if the Laboratory Service Transaction exists based on the reference number given.
	*   - uses the 'seg_lab_serv' table.
	* @access public
	* @param string Reference number of the Transaction
	* @return boolean
	*/
	function TransactionExists($refno){
		global $db;
		$this->useLabRetail($type);
		$this->sql="SELECT refno FROM $this->coretable WHERE refno='$refno'";
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}
	/**
	* Creates a Laboratory Service Transaction Details record into the database 'seg_lab_servdetails' table.
	* @access public
	* @param string Reference number
	* @param string Service code
	* @param int Number of Services involved
	* @param double Service Price 
	* @return boolean
	*    created by: burn Sept. 6, 2006
	*/	
	function AddTransactionDetails(
							$refno, 	    // reference number identifying transaction.
							$service_code,  // Service code
							$qty, 	        // number of services involved
							$service_price)	// service price 
	{

		global $db;
		$this->useLabRdetails();
		$this->sql="
			INSERT INTO $this->coretable (refno,service_code,entrynum,qty,service_price) 
			VALUES('$refno','$service_code',$entrynum,$qty,$service_price)";					
		$sqlResult=$this->Transact();
		if ($sqlResult) {
			$this->sql="SELECT MAX(entrynum) AS maxentry FROM $this->coretable WHERE refno='$refno' AND service_code='$service_code'";
			if ($result=$db->Execute($this->sql)) {
				$row=$result->FetchRow();
				return $row["maxentry"];
			}
			else {return FALSE;}
		}
		else{return FALSE;}
	}
	/**
	* Clears a Laboratory Service Transaction Details record from the database 'seg_lab_servdetails' table.
	* @access public
	* @param string Reference number
	* @return boolean
	*    documented by: burn Sept. 7, 2006
	*/		
	function ClearTransactionDetails($refno) {
		$this->useLabRdetails();
		$this->sql="DELETE FROM $this->coretable WHERE refno='$refno'";
		return $this->Transact();
	}
	/**
	* Removes a Laboratory Service Transaction Details record from the database 'seg_lab_servdetails' table.
	* @access public
	* @param string Reference number
	* @param int Number of Services involved
	* @return boolean
	*    documented by: burn Sept. 7, 2006
	*/			
	function RemoveTransactionDetails($refno, $entrynum) {
		$this->useLabRdetails();
		$this->sql="DELETE FROM $this->coretable WHERE refno='$refno' AND entrynum=$entrynum";
		return $this->Transact();
	}
	
	function GetTransactionDetails($refno) {
		global $db;
		$tb_products=$this->tb_pharma_products;
		$this->useLabRdetails();
		$this->count=0;
		$this->sql="SELECT $this->coretable.*, ".$tb_products.".artikelname FROM $this->coretable,$tb_products WHERE refno='$refno' AND $this->coretable.bestellnum=".$tb_products.".bestellnum";
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}
	
}
?>