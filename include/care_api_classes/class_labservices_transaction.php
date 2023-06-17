<?php
// Class for updating `seg_lab_serv` and `seg_lab_servdetails` tables.
// Created: 9-5-2006 (Vanessa A. Saren :-) )
// Edited by VAS 06-02-2009

require('./roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
#require_once($root_path.'include/inc_hclab_connection.php');

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
	# var $tb_pharma_prices='seg_pharma_prices';
	var $tb_lab_services='seg_lab_services';

	#var $tb_lab_service_discounts='seg_lab_service_discounts';
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
	#var $tb_pharma_products='care_pharma_products_main';

	/**
	* Database table for the laboratory transaction information
	* @var string
	*/
	// var $tb_lab_retail='seg_lab_retail';
	var $tb_lab_serv='seg_lab_serv';

	var $tb_serv_discounts='seg_service_discounts';

	var $tb_other_rates ='seg_other_rates';

	var $tb_discounts = 'seg_discount';
	//added by poliam 03/26/2014
	var $tb_type_room = 'care_type_room';
	//ended by poliam 03/26/2014

	var $tb_discounts_request = 'seg_lab_serv_discounts';

	var $tb_person = 'care_person';

	var $tb_socialdiscount = 'seg_charity_amount';
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

	var $fld_lab_service_groups=array(
		"group_code",
		"name",
		"other_name",
		"sort_nr",
		"status",
		"history",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt"
	);

	/**
	* Fieldnames of the seg_lab_services table.
	* @var array
	*/
	var $fld_lab_services=array(
		"service_code",
		"group_code",
		"name",
		"price_cash",
		"price_charge",
		"sort_nr",
		"status",
		"history",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt",
		"is_socialized"
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
		"modify_dt",
		"create_id",
		"create_dt"
	);

	var $fld_lab_serv=array(
		"refno",
		"serv_dt",
		"serv_tm",
		"encounter_nr",
		"pid",
		"is_cash",
		"type_charge",
		"is_urgent",
		"is_tpl",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt",
		"history",
		"comments",
		"ordername",
		"orderaddress",
		"status",
		"discountid",
		"loc_code",
		"parent_refno",
		"approved_by_head",
		"remarks",
		"headID",
		"headpasswd",
		"discount",
		"fromBB",
		"is_rdu",
        "is_walkin",
		"is_pe",
		"source_req",
		"area_type",
		"is_repeat",
		"grant_type",
		"ref_source"
		);

	var $fld_lab_servdetails=array(
		"refno",
		"service_code",
		"price_cash",
		"price_charge",
		"request_doctor",
		"request_dept",
		"is_in_house",
		"clinical_info",
		"status",
		"is_forward",
		"is_served",
		"date_served",
		"clerk_served_by",
		"clerk_served_date",
		"quantity",
		"request_flag",
        "history"
	);


	/**
	* Constructor
	* @param string refno
	*/
	function SegLab($refno=''){
		if(!empty($refno)) $this->refno=$refno;
		#$this->setTable($this->tb_lab_serv);
		#$this->setRefArray($this->tabfields);
	}

	/**
	* Sets the core object to point to seg_lab_serv and corresponding field names.
	*/
	function useLabServ(){
		$this->coretable=$this->tb_lab_serv;
		$this->ref_array=$this->fld_lab_serv;
	}

	/**
	* Sets the core object to point to seg_lab_servdetails and corresponding field names.
	*/
	function useLabServDetails(){
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


	# ---------------------------------------------------------------------------------------
	#  LAB_SERVICES
	# ---------------------------------------------------------------------------------------

	#function getLabServices($cond="1", $sort='') {
	function getLabServices($all, $cond="1", $sort='') {
		global $db;
		$this->useLabServices();
		if(empty($sort)) $sort='name';


	if ($all)
		$limit = "LIMIT 20";
	else
		$limit = "";

	 $this->sql="SELECT ss.* FROM $this->coretable AS ss
							 WHERE $cond
					 AND status NOT IN ($this->dead_stat)
					 ORDER BY $sort
					 $limit";


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

	function getLabServicesInfo($cond="1", $sort='') {
		global $db;
		$this->useLabServices();
		if(empty($sort)) $sort='name';

		$this->sql="SELECT sg.name AS grpname, sg.group_code,s.*
						FROM $this->coretable AS s,
								 $this->tb_lab_service_groups AS sg
						WHERE $cond ORDER BY $sort";

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

	function createLabService($code, $name, $cash, $charge, $status, $grp)	{
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;
		$this->useLabServices();

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

	#function updateLabService($xcode, $code, $codenum, $name, $cash, $charge, $remarks, $grp, $is_socialized, $is_ER, $is_package, $opd_code, $ipd_code, $with_result, $female_only, $male_only,$status,$with_inventory,$in_lis,$in_phs,$has_param_group, $is_serial, $no_serial, $is_profile) {
    function updateLabService($data) {
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;

		$charlist="\0..\37";
        extract($data);
        
		#$excode=addcslashes($excode,$charlist);
		$xcode=addcslashes($xcode,$charlist);
		$code=addcslashes($code,$charlist);
		#$codenum=addcslashes($codenum,$charlist);
		$name=addcslashes($name,$charlist);
		$cash=addcslashes($cash,$charlist);
		$charge=addcslashes($charge,$charlist);
		$status=addcslashes($status,$charlist);
		$remarks=addcslashes($remarks,$charlist);
		$grp=addcslashes($grp,$charlist);
        
        $this->useLabServices();
		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];
		$this->sql="UPDATE $this->coretable SET ".
			"code_num='$codenum',".
			"service_code='$code',".
			"oservice_code='$opd_code',".
            "ipdservice_code='$ipd_code',".
			"group_code='$grp',".
			"name=".$db->qstr($name).",".
			"price_cash='$cash',".
			"price_charge='$charge',".
			"status='$status',".
			"history=CONCAT(history,'Update: ',NOW(),' [$userid]\\n'),".
			"modify_id='$userid',".
			"modify_dt=NOW(),".
			"is_socialized = '$is_socialized', ".
			"is_other_rate = '$is_other_rate', ".
			"is_ER = '$is_ER', ".
			"is_package = '$is_package', ".
			"with_result = '$with_result', ".
			"female_only = '$female_only', ".
			"male_only = '$male_only', ".
			"with_inventory = '$with_inventory', ".
			"remarks = '$remarks', ".
			"in_lis = '$in_lis', ".
			"has_param_group = '$has_param_group', ".
			"in_phs = '$in_phs', ".
            "is_serial = '$is_serial', ".
            "no_serial = '$no_serial', ".
            "is_profile = '$is_profile' ".
			"WHERE ((service_code='$xcode') OR (service_code='".urlencode($xcode)."'))";

		#echo "sql update = ".$this->sql;
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;
	}

	#function addLabService($code, $codenum,$name, $cash, $charge, $remarks, $grp, $is_socialized, $is_ER, $is_package, $opd_code, $ipd_code, $with_result, $female_only, $male_only, $status, $with_inventory,$in_lis,$in_phs,$has_param_group, $is_serial, $no_serial, $is_profile) {
    function addLabService($data) {
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;

		$charlist="\0..\37";
        
        extract($data);
        
		#$excode=addcslashes($excode,$charlist);
		$code=addcslashes($code,$charlist);
		#$codenum=addcslashes($codenum,$charlist);
		$name=addcslashes($name,$charlist);
		$cash=addcslashes($cash,$charlist);
		$charge=addcslashes($charge,$charlist);
		$remarks=addcslashes($remarks,$charlist);
		$status=addcslashes($status,$charlist);
		$grp=addcslashes($grp,$charlist);
		#$codenum = addcslashes($codenum,$charlist);

        $this->useLabServices();
		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

		#$code = str_replace("'","",$code);

		$this->sql="INSERT INTO $this->coretable(code_num, service_code, oservice_code, ipdservice_code, group_code, name, price_cash, price_charge,
									 status, history, modify_id, modify_dt, create_id, create_dt, is_socialized, is_other_rate, is_ER, is_package, with_result, female_only, male_only, with_inventory, remarks,in_lis,has_param_group, in_phs, is_serial, no_serial, is_profile)
							 VALUES('$codenum','".strtoupper($code)."','".strtoupper($opd_code)."','".strtoupper($ipd_code)."', '$grp', '$name', '$cash', '$charge',
										'$status', CONCAT('Create: ',NOW(),' [$userid]\\n'),
										'$userid', NOW(), '$userid', NOW(), '$is_socialized', '$is_other_rate', '$is_ER', '$is_package','$with_result', '$female_only', '$male_only', '$with_inventory', '$remarks','$in_lis','$has_param_group', '$in_phs','$is_serial','$no_serial', '$is_profile')
						";

		#echo "sql update = ".$this->sql;
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;
	}

	function deleteLabService($code) {
		global $HTTP_SESSION_VARS;

		$this->useLabServices();
		#$this->sql="DELETE FROM $this->coretable WHERE service_code='$code'";
		$history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		$this->sql="UPDATE $this->coretable ".
						" SET status='deleted', history=".$history.", ".
						" modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
						" WHERE service_code = '$code'";
			return $this->Transact();
	}

	function deleteServiceDiscounts($code,$service_area) {
		$this->sql="DELETE FROM $this->tb_serv_discounts
						WHERE service_code='$code' AND service_area='$service_area'";

			return $this->Transact();
	}

	# ---------------------------------------------------------------------------------------
	#  LAB_PARAMS
	# ---------------------------------------------------------------------------------------

	function getLabParams($cond="1", $sort='') {
		global $db;
		$this->useLabParams();
		if(empty($sort)) $sort='name';
		$this->sql="SELECT * FROM $this->coretable WHERE $cond ORDER BY $sort";
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
				return $this->result;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function createLabParam($svcode="", $name="", $unit="", $median="", $lbound="", $ubound="", $lcrit="", $ucrit="", $ltoxic="", $utoxic="", $status="") {
		global $db;
		global $HTTP_SESSION_VARS;
		$charlist="\0..\37";
		$ret=FALSE;
		$this->useLabParams();

		// escape strings
		$svcode=addcslashes($svcode,$charlist);
		$name=addcslashes($name,$charlist);
		$unit=addcslashes($unit,$charlist);
		$median=addcslashes($median,$charlist);
		$lbound=addcslashes($lbound,$charlist);
		$ubound=addcslashes($ubound,$charlist);
		$lcrit=addcslashes($lcrit,$charlist);
		$ucrit=addcslashes($ucrit,$charlist);
		$ltoxic=addcslashes($ltoxic,$charlist);
		$utoxic=addcslashes($utoxic,$charlist);
		$unit=addcslashes($unit,$charlist);

		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];
		$this->sql="INSERT INTO $this->coretable(service_code, name, msr_unit, median, lo_bound, hi_bound, lo_critical, hi_critical, lo_toxic, hi_toxic, status, history, create_id, create_dt, modify_id, modify_dt) ".
			"VALUES('$svcode', '$name', '$unit', '$median', '$lbound', '$ubound', '$lcrit', '$ucrit', '$ltoxic', '$utoxic', '$status', CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW())";
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				return $db->Insert_ID();
			}
		}
		return FALSE;
	}

	function updateLabParam($id, $svcode="", $name="", $unit="", $median="", $lbound="", $ubound="", $lcrit="", $ucrit="", $ltoxic="", $utoxic="", $status="") {
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;

		$charlist="\0..\37";
		// escape strings
		$svcode=addcslashes($svcode,$charlist);
		$name=addcslashes($name,$charlist);
		$unit=addcslashes($unit,$charlist);
		$median=addcslashes($median,$charlist);
		$lbound=addcslashes($lbound,$charlist);
		$ubound=addcslashes($ubound,$charlist);
		$lcrit=addcslashes($lcrit,$charlist);
		$ucrit=addcslashes($ucrit,$charlist);
		$ltoxic=addcslashes($ltoxic,$charlist);
		$utoxic=addcslashes($utoxic,$charlist);
		$unit=addcslashes($unit,$charlist);

		$this->useLabParams();
		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];
		$this->sql="UPDATE $this->coretable SET service_code='$svcode',".
			"name='$name',".
			"msr_unit='$unit',".
			"median='$median',".
			"lo_bound='$lbound',".
			"hi_bound='$ubound',".
			"lo_critical='$lcrit',".
			"hi_critical='$ucrit',".
			"lo_toxic='$ltoxic',".
			"hi_toxic='$utoxic',".
			"status='$status',".
			"history=CONCAT(history,'Update: ',NOW(),' [$userid]\\n'),".
			"modify_id='$userid',".
			"modify_dt=NOW() ".
			"WHERE param_id='$id'";
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;
	}


	/**
	* Retrieve all records in the seg_lab_service_groups table
	* @access public
	* @param string Query filter clause
	* @param string Sort fields, each separated by a comma
	* @return boolean
	* created by: AJMQ, 09/20/06
	*/
	function getLabServiceGroups($sort='',$other_cond='') {
		global $db;
		$this->useLabServiceGroups();
		if(empty($sort)) $sort='name';
		$this->sql="SELECT * FROM $this->coretable
						WHERE status NOT IN ($this->dead_stat) $other_cond
						ORDER BY $sort";

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


	/*----------Added by VAS--------------*/
	/*get all the lab service group by dept*/
	function getLabServiceGroups2($report='', $cond="1", $sort='') {
		global $db;
		$this->useLabServiceGroups();
		if(empty($sort)) $sort='name';

		if ($report==1)
			$cond2 = " AND group_code NOT IN ('SPL') ";

		$this->sql="SELECT * FROM $this->tb_lab_service_groups
								WHERE $cond
						$cond2
						AND status NOT IN ($this->dead_stat)
						ORDER BY $sort";
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

	function getAllLabGroupInfo($group_code){
			global $db;
				 if ($group_code!='all')
						$cond = " AND group_code='$group_code' ";
		 $this->sql="SELECT * FROM $this->tb_lab_service_groups
						 WHERE status NOT IN ($this->dead_stat)
												 $cond ";
			if ($this->result=$db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
						return $this->result->FetchRow();
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}

	function getDiscountList($sort='') {
		global $db;

		$this->sql="SELECT * FROM $this->tb_discounts ORDER BY $sort";

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){

				return $this->result;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}


	function getServiceDiscount($cond="1",$sort='') {
	#function getServiceDiscount($service_code,$sort='') {
		global $db;

		$this->sql="SELECT * FROM $this->tb_serv_discounts where $cond ORDER BY $sort";

			if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){

				return $this->result;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}


	function getRequestorList($encmode){
		global $db;

		if ($encmode == 0){
			# ALL
			$this->sql="SELECT cp.pid, cp.name_first, cp.name_middle, cp.name_last,
									 ls.refno, ls.is_cash,ls.is_tpl, ls.serv_dt, ls.encounter_type
						FROM $this->tb_person AS cp,
								 $this->tb_lab_serv AS ls
						WHERE cp.pid = ls.pid
						AND ls.status NOT IN ($this->dead_stat)
						ORDER BY ls.refno";

						#ORDER BY cp.name_last, cp.name_first";
		}elseif ($encmode == 1){
			# INPATIENT
			$this->sql="SELECT cp.pid, cp.name_first, cp.name_middle, cp.name_last,
									 ls.refno, ls.is_cash, ls.is_tpl,ls.serv_dt, ls.encounter_type
						FROM $this->tb_person AS cp,
								 $this->tb_lab_serv AS ls
						WHERE cp.pid = ls.pid
						AND ls.status NOT IN ($this->dead_stat)
						AND encounter_type != '5'
						ORDER BY ls.refno";
						#ORDER BY cp.name_last, cp.name_first";    # 5 - Walkin (OPD and walkin)
		}elseif ($encmode == 2){
			# WALKIN AND OPD
			$this->sql="SELECT cp.pid, cp.name_first, cp.name_middle, cp.name_last,
									 ls.refno, ls.is_cash, ls.is_tpl, ls.serv_dt, ls.encounter_type
						FROM $this->tb_person AS cp,
								 $this->tb_lab_serv AS ls
						WHERE cp.pid = ls.pid
						AND ls.status NOT IN ($this->dead_stat)
						AND encounter_type = '5'
						ORDER BY ls.refno";
						#ORDER BY cp.name_last, cp.name_first";
		}

		 #echo "getEncounter : this->sql = '".$this->sql."' <br><br> \n";
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

	function deleteRequestor($refno) {
		global $db,$HTTP_SESSION_VARS;

		if(empty($refno) || (!$refno))
			return FALSE;

		$this->useLabServ();
		#$this->sql="DELETE FROM $this->tb_lab_serv WHERE refno='$refno'";
		#$history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
        $history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n");
		$encoder = $_SESSION['sess_temp_userid']; 
		$this->sql="UPDATE $this->coretable ".
						" SET status='deleted', history=".$history.", ".
						" modify_id='$encoder', modify_dt=NOW() ".
						" WHERE refno = '$refno'";
			#echo "sql = ".$this->sql;
		 return $this->Transact();
	}

	function deleteLabServ_details($refno) {
		global $db,$HTTP_SESSION_VARS;

		if(empty($refno) || (!$refno))
			return FALSE;

		$this->sql="UPDATE seg_lab_servdetails ".
						" SET status='deleted'".
						" WHERE refno = '$refno'";
			#echo "sql = ".$this->sql;
		 return $this->Transact();
	}

	/********************************************/

	#---------------------Added by VAS---------------------

	function getRequestedServices($refno,$ref_source='LB',$claimstub=0, $group=0, $group_code='') {
		global $db;
		$this->useLabServices();
		if(empty($sort)) $sort='name';

		if ($group)
			$groupby = " GROUP BY sg.group_code";

		if ($group_code)
			$groupcode = "AND ss.group_code='".$group_code."'";

		if ($group_code)
			$groupcode .= " AND ref_source = '".$group_code."'";

		if ($claimstub)
			$ispaid_sql = "AND (request_flag IS NOT NULL OR is_cash=0)";

		if ($ref_source){
				if ($ref_source=='LB')
					$grp_cond = " ";
				else{
					if ($ref_source=='BB')
						$ref_source = 'B';
					$grp_cond = " AND ss.group_code='".$ref_source."' ";
				}
		}else
				#$grp_cond = "";
				$grp_cond = " AND ss.group_code NOT IN ('B','SPL','IC') ";

			$this->sql="SELECT DISTINCT s.*, sd.request_flag AS type_charge, sd.request_flag AS charge_name,
					sd.*, ss.name, sg.group_code, sg.name AS groupnm,
					ss.is_socialized, sd.*, ss.name, sg.group_code, sg.name AS groupnm
					FROM seg_lab_serv AS s
					INNER JOIN seg_lab_servdetails AS sd ON s.refno = sd.refno
					LEFT JOIN seg_lab_services AS ss ON sd.service_code = ss.service_code
					LEFT JOIN seg_lab_service_groups AS sg ON ss.group_code = sg.group_code
					WHERE s.refno = '$refno'
					AND s.status NOT IN ($this->dead_stat)
                    AND sd.status NOT IN ($this->dead_stat)
					$ispaid_sql
					$groupcode
					$grp_cond
					$groupby
					ORDER BY sg.group_code,ss.name,sg.name";


			#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
				$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			return FALSE;
		}
	}

	#edited by VAN 03-10-08
	function saveLabServiceGroup($name, $code, $other_name, $mode, $xcode='')	{
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;
		$this->useLabServiceGroups();


		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

		if ($mode=='save'){
			$this->sql="INSERT INTO $this->coretable(group_code, name, other_name, status, history, create_id, create_dt, modify_id, modify_dt) ".
				"VALUES('".$code."', '".$name."', '".$other_name."', '', CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW())";

		}else{
			$this->sql="UPDATE $this->coretable SET
									group_code ='".$code."',
									name='".$name."',
									other_name='".$other_name."',
									status='',
									history=CONCAT(history,'Update: ',NOW(),' [$userid]\\n'),
									modify_id='$userid',
									modify_dt=NOW()
									WHERE group_code = '".$xcode."'";
		}

		#echo "sql = ".$this->sql;
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

	#-----------------------------

	#---Added by VAS
	/*return if the data is already exists*/
	function getServiceGroupInfo($grpname, $code){
		 global $db;
		$this->sql="SELECT * FROM $this->tb_lab_service_groups
								WHERE (name = '$grpname' AND group_code='$code')
						 OR (group_code='$code') OR name = '$grpname'";
		 if ($this->result=$db->Execute($this->sql)) {
				$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			 return FALSE;
		}
	}

	/**
	* Insert new Laboratory Service Group info in the database's 'seg_lab_service_groups' table. The data is
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
		 $this->data_array['modify_dt']=date('Y-m-d H:i:s');
		 $this->data_array['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
		 $this->data_array['create_dt']=date('Y-m-d H:i:s');
		 return $this->insertDataFromInternalArray();
	}
	/**
	* Updates the Laboratory Service Group's data in the database's 'seg_lab_service_groups' table. The data
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
	function updateLabServiceGroupsInfoFromArray($pass_group_code,&$data){
		 global $HTTP_SESSION_VARS;
		 $this->useLabServiceGroups();
		 $this->data_array=$data;
		 // remove probable existing array data to avoid replacing the stored data
		 if(isset($this->data_array['group_code'])) unset($this->data_array['group_code']);
		 if(isset($this->data_array['create_id'])) unset($this->data_array['create_id']);
		 // set the where condition
		 $this->where="group_code=$pass_group_code";
		 $this->data_array['history']=$this->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		 $this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		 $this->data_array['modify_dt']=date('Y-m-d H:i:s');
		 return $this->updateDataFromInternalArray($pass_group_code);
	}
	/**
	* Insert new Laboratory Service info in the database's 'seg_lab_services' table. The data is contained in
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
		 $this->data_array['modify_dt']=date('Y-m-d H:i:s');
		 $this->data_array['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
		 $this->data_array['create_dt']=date('Y-m-d H:i:s');
		return $this->insertDataFromInternalArray();
	}
	/**
	* Updates the Laboratory Service's data in the database's 'seg_lab_services' table. The data is contained
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
		 $this->data_array['modify_dt']=date('Y-m-d H:i:s');
		 print_r($this->data_array);
		 return $this->updateDataFromInternalArray($pass_service_code);
	}
	/**
	* Insert new Laboratory Parameter info in the database's 'seg_lab_params' table. The data is contained in
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
		 $this->data_array['modify_dt']=date('Y-m-d H:i:s');
		 $this->data_array['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
		 $this->data_array['create_dt']=date('Y-m-d H:i:s');
		 return $this->insertDataFromInternalArray();
	}
	/**
	* Updates the Laboratory Parameter's data in the database's 'seg_lab_params' table. The data is contained
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
		 $this->data_array['modify_dt']=date('Y-m-d H:i:s');
		 return $this->updateDataFromInternalArray($pass_param_id);
	}
	/*
	* Retrieves a Laboratory Service record from the database's 'seg_lab_services' table.
	* @access public
	* @param string Service code
	* @return boolean OR the Laboratory Service record including the Service Group name
	*    modified by: burn Sept. 8, 2006
	*/
	function GetLabServicesPrice($service_code) {
		global $db;
		# $this->useLabPrices();
		$this->useLabServices();
		$this->count=0;
		# $this->sql="SELECT * FROM $this->coretable WHERE service_code='$service_code'";
		$this->sql="SELECT $this->coretable.*, ".$this->tb_lab_service_groups.".name
								FROM $this->coretable, $this->tb_lab_service_groups
					WHERE $this->coretable.service_code = '$service_code'
						AND $this->coretable.group_code = ".$this->tb_lab_service_groups.".group_code";
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function CreateLabTransaction(
							$refno, 	    // Unique no. identifying transaction.
							$serv_dt, 	    // Date of service.
							$encounter_nr, 	// Patient encounter number
							$encounter_type,
							$pid,
							$is_cash, 		// Payment mode, cash or charge
							$encoder_id) {	// Encoder id

		$this->useLabServ();
		$this->sql = "INSERT INTO $this->coretable (refno, serv_dt, encounter_nr, encounter_type, pid, is_cash, modify_id, modify_dt, create_id, create_dt, history)
												 VALUES ('$refno', '$serv_dt', '$encounter_nr', '$encounter_type', '$pid', '$is_cash', '$encoder_id', NOW(), '$encoder_id', NOW(), CONCAT('Create: ',NOW(),' [$encoder_id]\\n'))";
		#echo "CreateLabTransaction = ".$this->sql;
		return $this->Transact();
	}



	/**
	* Deletes a Laboratory Service Transaction record from the database's 'seg_lab_serv' table.
	* @access public
	* @param string Reference number of the Transaction
	* @return boolean.
	*/
	function DeleteLabTransaction($refno){
		$this->useLabServ();
		$this->sql="DELETE FROM $this->coretable WHERE refno='$refno'";
		 return $this->Transact();
	}

	function UpdateLabTransaction(
							$refno, 	    // Unique no. identifying transaction.
							$newrefno,      // New Unique no. identifying transaction.
							$serv_dt, 	    // Date of service.
							$encounter_nr, 	// Patient encounter number
							$encounter_type,
							$pid,
							$is_cash, 		// Payment mode, cash or charge
							$encoder_id,   // Encoder id
							$history) 	// history
	{
		$this->useLabServ();
		$this->sql = "UPDATE $this->coretable SET " .
							"refno='$newrefno', " .
							"serv_dt='$serv_dt', " .
							"encounter_nr='$encounter_nr', " .
							"encounter_type = '$encounter_type', ".
							"pid = '$pid', ".
							"is_cash = '$is_cash', ".
							"is_tpl = '$is_tpl', ".
							"modify_id='$encoder_id', " .
							"modify_dt=NOW(), " .
							"history='\\n$history'".
							"WHERE refno = '$refno'";
							#"history=CONCAT(history,'Update: ',NOW(),' [$encoder_id]\\n') ".
		#echo "sql update = ".$this->sql;
		return $this->Transact();
	}

	/**
	* Checks if the Laboratory Service Transaction exists based on the reference number given.
	*   - uses the 'seg_lab_serv' table.
	* @access public
	* @param string Reference number of the Transaction
	* @return boolean
	*    documented by: burn Sept. 7, 2006
	*/
	function TransactionExists($refno){
		global $db;
		$this->useLabServ();
		$this->sql="SELECT refno FROM $this->coretable WHERE refno='$refno'";
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}

	#---------added by VAN----------

		/**
	* Clears a Laboratory Service Transaction Details record from the database's 'seg_lab_servdetails' table.
	* @access public
	* @param string Reference number of the Transaction
	* @return boolean
	*    documented by: burn Sept. 7, 2006
	*/
	#edited by VAN
	function ClearTransactionDetails($refno, $groupcode) {
	#function ClearTransactionDetails($refno, $service_list) {
		global $db;
		$this->useLabServDetails();

		$this->sql = "SELECT sd.*, ss.group_code from
									$this->tb_lab_servdetails AS sd,
									 $this->tb_lab_services AS ss
							WHERE sd.service_code = ss.service_code
							AND sd.refno='$refno'
							AND ss.group_code ='$groupcode'";

		#echo "ClearTransactionDetails : sql = ".$this->sql."<br>";
		$rs=$db->Execute($this->sql);
		$serv = array();
		if ($rs) {
			while($row=$rs->FetchRow()) {
				$this->sql="DELETE FROM $this->tb_lab_servdetails
								 WHERE refno='$refno' AND service_code='".$row['service_code']."'";
				#echo "sql = ".$this->sql."<br>";
				$this->Transact();
			}
		}
	}


	function AddLabServiceDetails(
							$refno, 	    // reference number identifying transaction.
							$groupID,             // group laboratory service ID
							$service_list       // Service code and its price
							)
	{
		global $db;

		$charlist="\0..\37";
		# escape strings
		$refno=addcslashes($refno,$charlist);
		$groupID=addcslashes($groupID,$charlist);

		$this->useLabServDetails();
		$this->sql="
			INSERT INTO $this->tb_lab_servdetails
									(refno,service_code, rate)
					 VALUES('$refno',?,?)";
		#echo "sql = ".$this->sql;
		#$ok=$db->Execute($this->sql,$array);
		$ok=$db->Execute($this->sql,$service_list);
		$this->count=$db->Affected_Rows();
		return $ok;
	}

	function AddServiceDiscounts($serv_discount,$service_code,$service_area){
		global $db;

		$charlist="\0..\37";
		# escape strings
		$service_code=addcslashes($service_code,$charlist);

		$this->sql="INSERT INTO $this->tb_serv_discounts
									(discountid,service_code, price, service_area)
							 VALUES(?,'$service_code',?,'$service_area')";
		#echo "sql = ".$this->sql."<br>";

		if ($db->Execute($this->sql,$serv_discount)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}else{
			$this->error=$db->ErrorMsg();
		}
		if ($ret)	return TRUE;
		else return FALSE;

	}

	function UpdateLabServiceDetails(
							$refno, 	    // reference number identifying transaction.
							$groupID,             // group laboratory service ID
							$service_list,       // Service code and its price
							$mode
							)
	{
		global $db;

		$charlist="\0..\37";
		# escape strings
		$refno=addcslashes($refno,$charlist);
		$groupID=addcslashes($groupID,$charlist);

		$this->useLabServDetails();

		if ($mode == 1){
			$this->sql="
				INSERT INTO $this->tb_lab_servdetails
									(refno,service_code, rate)
					 VALUES('$refno',?,?)";
		}else{
			$this->sql="
				DELETE FROM $this->tb_lab_servdetails
					WHERE refno = '$refno'
				 AND service_code = ?
				 AND rate = ? ";
		}
		#echo "sql = ".$this->sql;
		$ok=$db->Execute($this->sql,$service_list);
		$this->count=$db->Affected_Rows();
		return $ok;
	}


	function getLabServiceInfo($code, $group_code){
		global $db;

		$this->sql ="SELECT * FROM $this->tb_lab_services
								 WHERE service_code='$code'
						 AND group_code='$group_code'
						 AND status NOT IN ($this->dead_stat)";
		if ($this->result=$db->Execute($this->sql)){
			#$this->count=$this->result->RecordCount();
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getServiceInfo($code){
		global $db;

		$this->sql ="SELECT g.name AS group_name,g.other_name,s.*
						 FROM $this->tb_lab_services AS s
						 LEFT JOIN $this->tb_lab_service_groups AS g
						 ON g.group_code = s.group_code
								 WHERE service_code='$code' OR oservice_code='$code' OR ipdservice_code='$code'
						 AND s.status NOT IN ($this->dead_stat)";

		if ($this->result=$db->Execute($this->sql)){
			#$this->count=$this->result->RecordCount();
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getServiceRequestInfo($refno, $code){
		global $db;

				$code_cond="";
				if ($code)
						$code_cond=" AND i.service_code='$code' ";

		$this->sql="SELECT i.*,s.service_code as code,s.name,g.name AS group_name,g.other_name
							FROM $this->tb_lab_servdetails AS i
							LEFT JOIN $this->tb_lab_services AS s ON s.service_code=i.service_code
						LEFT JOIN $this->tb_lab_service_groups AS g ON g.group_code = s.group_code
							WHERE i.refno='$refno'
						$code_cond";

		if ($this->result=$db->Execute($this->sql)){
			#$this->count=$this->result->RecordCount();
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getLabServiceReqInfo($refno){
		global $db;

		//$this->sql ="SELECT ch.charge_name,l.* FROM $this->tb_lab_serv AS l
//									LEFT JOIN seg_type_charge AS ch ON ch.id=l.type_charge
//								 WHERE refno='$refno'
//						 AND status NOT IN ($this->dead_stat)";

		$this->sql ="SELECT l.* FROM $this->tb_lab_serv AS l
									WHERE refno='$refno'
									AND status NOT IN ($this->dead_stat)";

		if ($this->result=$db->Execute($this->sql)){
			#$this->count=$this->result->RecordCount();
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getRequestInfo($refno){
		global $db;

		$this->sql ="SELECT * FROM $this->tb_lab_servdetails WHERE refno='$refno' LIMIT 1";
		if ($this->result=$db->Execute($this->sql)){
			#$this->count=$this->result->RecordCount();
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getLabRequest($refno, $pid) {
		global $db;
		$this->useLabServices();

		$this->sql="SELECT ls.*, cp.*
						FROM $this->tb_lab_serv AS ls,
								 $this->tb_person AS cp
						WHERE refno='$refno'
						AND ls.pid='$pid'
						AND ls.pid=cp.pid
						AND ls.status NOT IN ($this->dead_stat)";

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


	function getNewRefno($refno){
		global $db;
		$row=array();

		$this->sql="SELECT refno FROM $this->tb_lab_serv
								WHERE refno >= '$refno' ORDER BY refno DESC";

		#echo "<br> sql = ".$this->sql;
		if($this->res['gnen']=$db->SelectLimit($this->sql,1)){
			if($this->res['gnen']->RecordCount()){
				$row=$this->res['gnen']->FetchRow();
				return $row['refno']+1;
			}else{ return $refno;}
		}else{ return $refno;}
	}

	function getLastNr($today, $ref_init) {
		global $db;
		$this->useLabServ();
		$today = $db->qstr($today);
		$this->sql="SELECT IFNULL(MAX(CAST(refno AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),$ref_init)) FROM $this->coretable WHERE SUBSTRING(refno,1,4)=EXTRACT(YEAR FROM NOW())";
		return $db->GetOne($this->sql);
	}

	function getActiveOrders($now) {
			global $db;
		$this->useLabServ();
		if (is_numeric($now)) $dDate = date("Ymd",$now);
		if (!$dDate) $dDate = "NOW()";
		else $dDate = $db->qstr($dDate);
		$this->sql="SELECT r.*,p.name_last,p.name_first,p.name_middle\n".
				"FROM $this->coretable AS r\n".
				"LEFT JOIN $this->tb_person AS p ON p.pid=r.pid\n".
				"WHERE r.serv_dt=$dDate\n".
				"ORDER BY r.serv_dt DESC,is_urgent DESC,refno ASC";
		if($this->result=$db->Execute($this->sql)) {
			#if($this->result->RecordCount()) {
				$this->count = $this->result->RecordCount();
				return $this->result;
			#} else { return false; }
		} else { return false; }
	}

	function getOrderInfo($refno) {
			global $db;
		$this->useLabServ();
		$refno = $db->qstr($refno);

		$this->sql="SELECT r.*,p.name_last,p.name_first,p.name_middle,p.senior_ID,\n".
				"e.encounter_type, e.encounter_class_nr, e.is_medico,\n".
				"e.current_ward_nr, e.current_room_nr, e.current_dept_nr,\n".
				"p.sex, p.civil_status, p.blood_group, \n".
				"IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_age(e.encounter_date,p.date_birth),age) AS age,\n".
				"p.date_birth\n".
				"FROM $this->coretable AS r\n".
				"INNER JOIN $this->tb_person AS p ON p.pid=r.pid\n".
				"LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr\n".
				"WHERE r.refno=$refno";

		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	#function getOrderItems($refno) {
	function getOrderItems($refno, $serv_code) {
			global $db;
		$refno = $db->qstr($refno);

		if (empty($serv_code)){
			$this->sql="SELECT i.*,s.service_code as code,s.name,
								s.group_code, g.name AS group_name
								FROM $this->tb_lab_servdetails AS i
								LEFT JOIN $this->tb_lab_services AS s ON s.service_code=i.service_code
							LEFT JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code
								WHERE i.refno=$refno";
		}else{
			$this->sql="SELECT i.*,s.service_code as code,s.name,
								s.group_code, g.name AS group_name
								FROM $this->tb_lab_servdetails AS i
								LEFT JOIN $this->tb_lab_services AS s ON s.service_code=i.service_code
							LEFT JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code
								WHERE i.refno=$refno AND i.service_code='$serv_code'";
		}

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			#return $this->result->FetchRow();
			return $this->result;
		 }else{
			return FALSE;
		 }
		}else{
			 return FALSE;
			}
	}

	function clearOrderList($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "DELETE FROM $this->tb_lab_servdetails WHERE refno=$refno";
	return $this->Transact();
	}

	function addOrders($refno, $orderArray) {
		global $db;
		#print_r($orderArray);
		$refno = $db->qstr($refno);
		/*$this->sql = "INSERT INTO $this->tb_lab_servdetails(refno,service_code,price_cash,price_cash_orig,price_charge,request_doctor,request_dept,is_in_house,clinical_info,is_forward,is_served,is_monitor,request_flag)
									VALUES($refno,?,?,?,?,?,?,?,?,?,?,?,?)";

		if($buf=$db->Execute($this->sql,$orderArray)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
		*/
		#edited by VAN 06-24-2010
		#multiple insert
		$this->sql = "INSERT INTO $this->tb_lab_servdetails(refno,
																												service_code,price_cash,price_cash_orig,
																												price_charge,request_doctor,request_dept,
																												is_in_house,clinical_info,is_forward,
																												is_served,is_monitor,request_flag)
									VALUES ";
		#loop the data to be saved

		$i=0;
		for ($i=0; $i<sizeof ($orderArray);$i++){
				if ($i > 0) $this->sql .= ",";
				if (empty($orderArray[$i][11]))
					$orderArray[$i][11] = 'NULL';
				else
					$orderArray[$i][11] = "'".$orderArray[$i][11]."'";

				$this->sql .= "(".$refno
												 .", '".$orderArray[$i][0]."', '".$orderArray[$i][1]."', '".$orderArray[$i][2]
												 ."', '".$orderArray[$i][3]."', '".$orderArray[$i][4]."', '".$orderArray[$i][5]
												 ."', '".$orderArray[$i][6]."', '".$orderArray[$i][7]."', '".$orderArray[$i][8]
												 ."', '".$orderArray[$i][9]."', '".$orderArray[$i][10]."', ".$orderArray[$i][11].")";
		}
		#echo "ss = ".$this->sql;
		return $this->Transact();
	}

	function clearDiscounts($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "DELETE FROM $this->tb_discounts_request WHERE refno=$refno";
			return $this->Transact();
	}

	function addDiscounts($refno, $discArray) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "INSERT INTO $this->tb_discounts_request(refno,discountid) VALUES($refno,?)";
		if($buf=$db->Execute($this->sql,$discArray)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}

	function getOrderDiscounts($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql="SELECT r.discountid\n".
				"FROM $this->tb_discounts_request AS r\n".
				"WHERE r.refno=$refno";
		if($this->result=$db->Execute($this->sql)) {
			$ret = array();
			while ($row = $this->result->FetchRow())
				$ret[$row['discountid']] = $row['discountid'];
			return $ret;
		} else { return false; }
	}
	#--------------------------------

	#--------added by VAN 09-12-07-------

	function getSocialDiscount($refno){
		global $db;

		$this->sql="SELECT * FROM seg_charity_amount
									 WHERE ref_no = '".$refno."'
							AND ref_source = 'LD'";

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result->FetchRow();
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
		}

	function getListLabSectionRequest_Status($grp_kind, $grpview, $grp_code, $datefrom, $dateto, $discountID, $pat_type, $fromtime, $totime){
		global $db;
		if (($grp_code == "all")&&($discountID == "all")&&(!($datefrom))&&(!($dateto))){
			$cond = "";

		}elseif (($grp_code == "all")&&($discountID != "all")&&(!($datefrom))&&(!($dateto))){

			#edited by VAN 07-01-08
			$classInfo = $this->getChargeTypeInfo($discountID);
			if ($this->count)
				$cond = " AND s.type_charge = '".$discountID."' ";
			else
				$cond = " AND s.discountid = '".$discountID."'";
			#---------------

		}elseif (($grp_code != "all")&&($discountID == "all")&&(!($datefrom))&&(!($dateto))){

			$cond = " AND g.group_code = '".$grp_code."'";

		}elseif (($grp_code != "all")&&($discountID != "all")&&(!($datefrom))&&(!($dateto))){

			#$cond = " AND g.group_code = '".$grp_code."' AND s.discountid = '".$discountID."'";
			$classInfo = $this->getChargeTypeInfo($discountID);
			if ($this->count)
				$cond = " AND g.group_code = '".$grp_code."' AND s.type_charge = '".$discountID."'";
			else
				$cond = " AND g.group_code = '".$grp_code."' AND s.discountid = '".$discountID."'";
			#-----------------------
		}elseif (($grp_code == "all")&&($discountID == "all")&& (($datefrom)&& ($dateto))){
			$cond = " AND (serv_dt >= '".$datefrom."' AND serv_dt <= '".$dateto."')";

		}elseif (($grp_code != "all")&&($discountID == "all")&& (($datefrom)&& ($dateto))){
			$cond = " AND g.group_code = '".$grp_code."' AND (serv_dt >= '".$datefrom."'
							 AND serv_dt <= '".$dateto."')";

		}elseif (($grp_code == "all")&&($discountID != "all")&& (($datefrom)&& ($dateto))){
			#$cond = " AND (serv_dt >= '".$datefrom."' AND serv_dt <= '".$dateto."')
			#			AND s.discountid = '".$discountID."'";
			$classInfo = $this->getChargeTypeInfo($discountID);
			if ($this->count){
				$cond = " AND (serv_dt >= '".$datefrom."' AND serv_dt <= '".$dateto."')
							AND s.type_charge = '".$discountID."'";
			}else{
				$cond = " AND (serv_dt >= '".$datefrom."' AND serv_dt <= '".$dateto."')
							AND s.discountid = '".$discountID."'";
			}

		}else{

			$classInfo = $this->getChargeTypeInfo($discountID);
			if ($this->count){
				$cond = " AND g.group_code = '".$grp_code."'
						AND (serv_dt >= '".$datefrom."' AND serv_dt <= '".$dateto."')
						AND s.type_charge = '".$discountID."'";
			}else{
				$cond = " AND g.group_code = '".$grp_code."'
						AND (serv_dt >= '".$datefrom."' AND serv_dt <= '".$dateto."')
						AND s.discountid = '".$discountID."'";
			}
		}

		if ($grp_kind=='w_result'){
				$join_rep = " INNER JOIN seg_lab_resultdata AS rs
								 ON s.refno = rs.refno";
				$join_rep_cond = "";
		}elseif($grp_kind=='wo_result'){
				$join_rep = "";
				$join_rep_cond = " AND NOT EXISTS(SELECT rs.* FROM seg_lab_results AS rs
															 WHERE rs.refno = s.refno)";
		}elseif($grp_kind=='all'){
				$join_rep = "";
				$join_rep_cond = "";
		}
		#echo "type = ".$pat_type;
		if ($pat_type){
			if ($pat_type==1){
				#ER PATIENT
				$cond .= " AND enc.encounter_type IN (1) ";

				if ((($fromtime!='00:00:00')&&($totime!='00:00:00'))&& (($datefrom)&& ($dateto)))
					$cond .= "AND (serv_tm >= '".$fromtime."' AND serv_tm <= '".$totime."')";

			}elseif ($pat_type==2){
				#ADMITTED PATIENT
				$cond .= " AND enc.encounter_type IN (3,4) ";

				if ((($fromtime!='00:00:00')&&($totime!='00:00:00'))&& (($datefrom)&& ($dateto)))
					$cond .= "AND (serv_tm >= '".$fromtime."' AND serv_tm <= '".$totime."')";

			}elseif ($pat_type==3){
				#OUT PATIENT
				$cond .= " AND enc.encounter_type IN (2) ";

			}elseif ($pat_type==4){
				#WALK-IN PATIENT
				$cond .= " AND s.encounter_nr='' ";
			}elseif	($pat_type==5){
				#OPD & WALKIN
				$cond .= " AND (enc.encounter_type IN (2)  OR s.encounter_nr='')";
			}
		}

		$items = "s.discountid AS classID, s.pid AS patientID,
					 g.name AS grp_name, g.other_name AS grp_name2,
					 ss.name AS service_name, s.*, d.*, ss.*,
					 p.*, w.*, c.grant_dte, c.sw_nr, enc.* ";

		if ($grpview==1){
			$grp = "GROUP BY s.refno";
		}else{
			$grp = "";
		}
		 $order = " ORDER BY p.name_last, p.name_first, s.refno, g.name";


		$this->sql = "SELECT $items
							FROM seg_lab_serv AS s
							INNER JOIN seg_lab_servdetails AS d
								ON s.refno=d.refno
							LEFT JOIN seg_lab_services AS ss
								ON d.service_code=ss.service_code
							LEFT JOIN seg_lab_service_groups AS g
								ON g.group_code=ss.group_code
							LEFT JOIN care_person AS p 
								ON p.pid=s.pid
							LEFT JOIN seg_walkin AS w
							    ON w.`pid` = s.`pid`
							LEFT JOIN care_encounter AS enc
								ON s.encounter_nr = enc.encounter_nr
							LEFT JOIN seg_charity_grants AS c
								ON s.encounter_nr=c.encounter_nr
							$join_rep
							WHERE s.status NOT IN($this->dead_stat)
							AND d.status NOT IN($this->dead_stat)
							$join_rep_cond
							$cond
							$grp
							$order
						 ";

		#echo "sql = ".$this->sql;

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			#return $this->result->FetchRow();
			return $this->result;
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
		}

	function getListLabSectionRequest_Stat($grp_kind, $grpview, $grp_code, $datefrom, $dateto, $discountID){
		global $db;
		if (($grp_code == "all")&&($discountID == "all")&&(!($datefrom))&&(!($dateto))){
			$cond = "";

		}elseif (($grp_code == "all")&&($discountID != "all")&&(!($datefrom))&&(!($dateto))){

			$cond = "c.discountid = '".$discountID."'";

		}elseif (($grp_code != "all")&&($discountID == "all")&&(!($datefrom))&&(!($dateto))){

			$cond = "g.group_code = '".$grp_code."'";

		}elseif (($grp_code != "all")&&($discountID != "all")&&(!($datefrom))&&(!($dateto))){

			$cond = "g.group_code = '".$grp_code."' AND c.discountid = '".$discountID."'";

		}elseif (($grp_code == "all")&&($discountID == "all")&& (($datefrom)&& ($dateto))){
			$cond = "(s.serv_dt >= '".$datefrom."' AND s.serv_dt <= '".$dateto."')";

		}elseif (($grp_code != "all")&&($discountID == "all")&& (($datefrom)&& ($dateto))){
			$cond = "g.group_code = '".$grp_code."' AND (s.serv_dt >= '".$datefrom."'
							 AND s.serv_dt <= '".$dateto."')";

		}elseif (($grp_code == "all")&&($discountID != "all")&& (($datefrom)&& ($dateto))){
			$cond = "(s.serv_dt >= '".$datefrom."' AND s.serv_dt <= '".$dateto."')
						AND c.discountid = '".$discountID."'";
		}else{
			$cond = "g.group_code = '".$grp_code."'
						AND (s.serv_dt >= '".$datefrom."' AND s.serv_dt <= '".$dateto."')
						AND c.discountid = '".$discountID."'";
		}

		if ($grp_kind=='w_result'){
				$join_rep = "INNER JOIN seg_lab_results AS rs
						 ON s.refno = rs.refno WHERE s.status NOT IN (".$this->dead_stat.")";
				if (!empty($cond))
					$conj = "AND";
				else
					$conj = "";
		}elseif($grp_kind=='wo_result'){
				$join_rep = "WHERE NOT EXISTS(SELECT rs.* FROM seg_lab_results AS rs
															 WHERE rs.refno = s.refno)
								 AND s.status NOT IN (".$this->dead_stat.")";
				if (!empty($cond))
					$conj = "AND";
				else
					$conj = "";
		}elseif($grp_kind=='all'){
				$join_rep = "";
				if (!empty($cond))
					$conj = "WHERE";
				else
					$conj = "";
		}

		$items = "count(g.name) AS stat, EXTRACT(MONTH FROM s.serv_dt) AS month,
						 s.serv_dt,s.discountid AS classID, s.pid AS patientID,
						 g.name AS grp_name, other_name AS grp_name2,
						 ss.name AS service_name, s.*, d.*, ss.*,
						 p.*, c.grant_dte, c.sw_nr, enc.* ";

		$grp = "GROUP BY g.name , EXTRACT(YEAR FROM s.serv_dt), EXTRACT(MONTH FROM s.serv_dt)";
		$order = 'ORDER BY month, p.name_last, p.name_first, s.refno, g.name';

		$this->sql = "SELECT $items
							FROM seg_lab_serv AS s
							INNER JOIN seg_lab_servdetails AS d
								ON s.refno=d.refno
							INNER JOIN seg_lab_services AS ss
								ON d.service_code=ss.service_code
							INNER JOIN seg_lab_service_groups AS g
								ON g.group_code=ss.group_code
							INNER JOIN care_person AS p
								ON p.pid=s.pid
							LEFT JOIN care_encounter AS enc
								ON s.encounter_nr = enc.encounter_nr
							LEFT JOIN seg_charity_grants AS c
								ON s.encounter_nr=c.encounter_nr
							$join_rep
							$conj
							$cond
							$grp
							$order
						 ";

		#echo "sql = ".$this->sql;

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			#return $this->result->FetchRow();
			return $this->result;
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
		}

	#added by VAN 04-21-08
	function getStatReport($fromdate, $todate){
		global $db;

		if (($fromdate)&&($todate)){
			$cond = "AND (s.serv_dt >= '".$fromdate."' AND s.serv_dt <= '".$todate."')";
		}

		$this->sql="SELECT count(g.group_code) AS stat, EXTRACT(YEAR FROM s.serv_dt) AS year
						FROM seg_lab_serv AS s
						INNER JOIN seg_lab_servdetails AS d ON s.refno=d.refno
						INNER JOIN seg_lab_services AS ss ON d.service_code=ss.service_code
						INNER JOIN seg_lab_service_groups AS g ON g.group_code=ss.group_code
						INNER JOIN care_person AS p ON p.pid=s.pid
						LEFT JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
						WHERE s.status NOT IN($this->dead_stat)
						AND d.status NOT IN($this->dead_stat)
						$cond
						GROUP BY EXTRACT(YEAR FROM s.serv_dt)
						ORDER BY EXTRACT(YEAR FROM s.serv_dt) DESC";
		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result;
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
	}

	function getStatReportByYear($year, $fromdate, $todate){
		global $db;

		if (($fromdate)&&($todate)){
			$cond = "AND (s.serv_dt >= '".$fromdate."' AND s.serv_dt <= '".$todate."')";
		}

		$this->sql="SELECT count(g.group_code) AS stat, EXTRACT(MONTH FROM s.serv_dt) AS month,
						EXTRACT(YEAR FROM s.serv_dt) AS year
						FROM seg_lab_serv AS s
						INNER JOIN seg_lab_servdetails AS d ON s.refno=d.refno
						INNER JOIN seg_lab_services AS ss ON d.service_code=ss.service_code
						INNER JOIN seg_lab_service_groups AS g ON g.group_code=ss.group_code
						INNER JOIN care_person AS p ON p.pid=s.pid
						LEFT JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
						WHERE s.status NOT IN($this->dead_stat)
						AND d.status NOT IN($this->dead_stat)
						AND EXTRACT(YEAR FROM s.serv_dt)='".$year."'
						$cond
						GROUP BY EXTRACT(MONTH FROM s.serv_dt)
						ORDER BY EXTRACT(MONTH FROM s.serv_dt)";
		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result;
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
	}

	function getStatReportByMonth($year, $month, $fromdate, $todate){
		global $db;

		if (($fromdate)&&($todate)){
			$cond = "AND (s.serv_dt >= '".$fromdate."' AND s.serv_dt <= '".$todate."')";
		}

		$this->sql="SELECT count(g.group_code) AS stat, EXTRACT(MONTH FROM s.serv_dt) AS month,
						EXTRACT(YEAR FROM s.serv_dt) AS year, g.name AS grp_name,
						g.group_code AS grp_code
						FROM seg_lab_serv AS s
						INNER JOIN seg_lab_servdetails AS d ON s.refno=d.refno
						INNER JOIN seg_lab_services AS ss ON d.service_code=ss.service_code
						INNER JOIN seg_lab_service_groups AS g ON g.group_code=ss.group_code
						INNER JOIN care_person AS p ON p.pid=s.pid
						LEFT JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
						WHERE s.status NOT IN($this->dead_stat)
						AND d.status NOT IN($this->dead_stat)
						AND EXTRACT(YEAR FROM s.serv_dt)='".$year."'
						AND EXTRACT(MONTH FROM s.serv_dt)='".$month."'
						$cond
						GROUP BY EXTRACT(MONTH FROM s.serv_dt), g.group_code
						ORDER BY g.name";
		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result;
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
	}

	function getStatByResult($year, $month, $fromdate, $todate, $labgroup, $withResult){
		global $db;

		if (($fromdate)&&($todate)){
			$cond = "AND (s.serv_dt >= '".$fromdate."' AND s.serv_dt <= '".$todate."')";
		}

		if ($withResult){

			$this->sql="SELECT count(g.name) AS stat_result, EXTRACT(MONTH FROM s.serv_dt) AS month,
						EXTRACT(YEAR FROM s.serv_dt) AS year, g.name AS grp_name,
						g.group_code AS grp_code
						FROM seg_lab_serv AS s
						INNER JOIN seg_lab_servdetails AS d ON s.refno=d.refno
						INNER JOIN seg_lab_services AS ss ON d.service_code=ss.service_code
						INNER JOIN seg_lab_service_groups AS g ON g.group_code=ss.group_code
						INNER JOIN care_person AS p ON p.pid=s.pid
						LEFT JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
						INNER JOIN seg_lab_results AS rs ON d.refno = rs.refno AND rs.service_code=d.service_code
						WHERE s.status NOT IN($this->dead_stat)
						AND d.status NOT IN($this->dead_stat)
						AND EXTRACT(YEAR FROM s.serv_dt)='".$year."'
						AND EXTRACT(MONTH FROM s.serv_dt)='".$month."'
						$cond
						AND g.group_code='".$labgroup."'
						GROUP BY EXTRACT(MONTH FROM s.serv_dt), g.group_code
						ORDER BY g.name";
		}else{
			$this->sql="SELECT count(g.name) AS stat_result, EXTRACT(MONTH FROM s.serv_dt) AS month,
						EXTRACT(YEAR FROM s.serv_dt) AS year, g.name AS grp_name,
						g.group_code AS grp_code
						FROM seg_lab_serv AS s
						INNER JOIN seg_lab_servdetails AS d ON s.refno=d.refno
						INNER JOIN seg_lab_services AS ss ON d.service_code=ss.service_code
						INNER JOIN seg_lab_service_groups AS g ON g.group_code=ss.group_code
						INNER JOIN care_person AS p ON p.pid=s.pid
						LEFT JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
						WHERE s.status NOT IN($this->dead_stat)
						AND d.status NOT IN($this->dead_stat)
						AND EXTRACT(YEAR FROM s.serv_dt)='".$year."'
						AND EXTRACT(MONTH FROM s.serv_dt)='".$month."'
						$cond
						AND NOT EXISTS(SELECT rs.* FROM seg_lab_results AS rs
													 WHERE rs.refno = d.refno AND rs.service_code=d.service_code)
						AND g.group_code='".$labgroup."'
						GROUP BY EXTRACT(MONTH FROM serv_dt), g.group_code
						ORDER BY g.name";
		}
		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result->FetchRow();
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
	}

	function getStatByResultEncType($year, $month, $fromdate, $todate, $group, $enctype, $withResult){
		global $db;

		if (($fromdate)&&($todate)){
			$cond = "AND (s.serv_dt >= '".$fromdate."' AND s.serv_dt <= '".$todate."')";
		}

		if ($withResult){

			$this->sql="SELECT count(g.group_code) AS stat_result,
							EXTRACT(MONTH FROM s.serv_dt) AS month,
							EXTRACT(YEAR FROM s.serv_dt) AS year,
							g.name AS grp_name, g.group_code AS grp_code, e.encounter_type
							FROM seg_lab_serv AS s
							INNER JOIN seg_lab_servdetails AS d ON s.refno=d.refno
							INNER JOIN seg_lab_services AS ss ON d.service_code=ss.service_code
							INNER JOIN seg_lab_service_groups AS g ON g.group_code=ss.group_code
							INNER JOIN care_person AS p ON p.pid=s.pid
							LEFT JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
							INNER JOIN seg_lab_results AS rs ON d.refno = rs.refno AND rs.service_code=d.service_code
							WHERE s.status NOT IN($this->dead_stat)
							AND d.status NOT IN($this->dead_stat)
							AND EXTRACT(YEAR FROM s.serv_dt)='".$year."'
							AND EXTRACT(MONTH FROM s.serv_dt)='".$month."'
							AND g.group_code='".$group."'
							AND e.encounter_type IN(".$enctype.")
							GROUP BY EXTRACT(MONTH FROM s.serv_dt), g.group_code, e.encounter_type IN(".$enctype.")
							ORDER BY g.name, e.encounter_type";
		}else{
			$this->sql="SELECT count(g.group_code) AS stat_result, EXTRACT(MONTH FROM s.serv_dt) AS month,
							EXTRACT(YEAR FROM s.serv_dt) AS year,
							g.name AS grp_name, g.group_code AS grp_code, e.encounter_type
							FROM seg_lab_serv AS s
							INNER JOIN seg_lab_servdetails AS d ON s.refno=d.refno
							INNER JOIN seg_lab_services AS ss ON d.service_code=ss.service_code
							INNER JOIN seg_lab_service_groups AS g ON g.group_code=ss.group_code
							INNER JOIN care_person AS p ON p.pid=s.pid
							LEFT JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
							WHERE s.status NOT IN($this->dead_stat)
							AND d.status NOT IN($this->dead_stat)
							AND EXTRACT(YEAR FROM s.serv_dt)='".$year."'
							AND EXTRACT(MONTH FROM s.serv_dt)='".$month."'
							AND NOT EXISTS(SELECT rs.* FROM seg_lab_results AS rs
															WHERE rs.refno = d.refno AND rs.service_code=d.service_code)
							AND g.group_code='".$group."'
							AND e.encounter_type IN(".$enctype.")
							GROUP BY EXTRACT(MONTH FROM s.serv_dt), g.group_code, e.encounter_type IN(".$enctype.")
							ORDER BY g.name, e.encounter_type";
		}
		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result->FetchRow();
			#return $this->result;
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
	}

	function getStatByYearMonth($year, $month){
		global $db;

		$this->sql="SELECT serv_dt, count(pid) AS totalpat
						FROM seg_lab_serv AS s
						WHERE EXTRACT(YEAR FROM s.serv_dt) = '".$year."'
						AND EXTRACT(MONTH FROM s.serv_dt) = '".$month."'
						GROUP BY extract(YEAR_MONTH FROM serv_dt)";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result->FetchRow();
			#return $this->result;
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
	}
	#-----------------------------

	function getPatientList($datefrom, $dateto, $fromtime, $totime, $grp_kind, $grp_code, $discountID, $enctype, $grp){
		global $db;

		if ($grp){
			$groupby = " GROUP BY refno ";
		}else{
			$groupby = "";
		}

		if (($fromtime!='00:00:00')&&($totime!='00:00:00')){
				$time_cond = " AND (r.serv_tm >= '".$fromtime."' AND r.serv_tm <= '".$totime."')";
		}else
			$time_cond = "";

		if (($datefrom)&&($dateto))	{
				$date_cond = " AND  (r.serv_dt >= '".$datefrom."' AND r.serv_dt <= '".$dateto."')";
		}else
			$date_cond = "";

		if ($grp_kind=='w_result'){
				$join_rep = " INNER JOIN seg_lab_results AS rs
								 ON r.refno = rs.refno";
				$join_rep_cond = "";
		}elseif($grp_kind=='wo_result'){
				$join_rep = "";
					$join_rep_cond = " AND NOT EXISTS(SELECT rs.* FROM seg_lab_results AS rs
															 WHERE rs.refno = r.refno)";
		}elseif($grp_kind=='all'){
				$join_rep = "";
				$join_rep_cond = "";
		}

		if ($grp_code == "all")
			$group_cond = "";
		else{
				$group_cond = " AND g.group_code = '".$grp_code."' ";
		}

		if ($discountID == "all")
			$class_cond = "";
		else{
				#added by VAN 07-01-08
				$classInfo = $this->getChargeTypeInfo($discountID);
				#echo $this->count;

				if ($this->count)
					$class_cond = " AND r.type_charge = '".$discountID."' ";
				else
					$class_cond = " AND r.discountid = '".$discountID."' ";
		}

		$this->sql="SELECT IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),'') AS agebydbate,
					pay.qty,pay.amount_due AS paid, d.price_cash,
					e.encounter_type,e.current_dept_nr,e.current_ward_nr, e.current_room_nr,
					g.name AS grp_name, g.other_name AS grp_name2, r.*, p.*,
					d.service_code, s.name AS service_name
					FROM seg_lab_serv AS r
					INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno
					LEFT JOIN seg_lab_services AS s ON s.service_code=d.service_code
					LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
					LEFT JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code
					INNER JOIN care_person AS p ON p.pid=r.pid
					LEFT JOIN seg_charity_grants AS c ON r.encounter_nr=c.encounter_nr
					$join_rep
					LEFT JOIN seg_pay_request AS pay ON pay.ref_no=r.refno AND ref_source='LD' AND pay.service_code=d.service_code
					LEFT JOIN seg_pay AS sp ON sp.or_no=pay.or_no AND (ISNULL(sp.cancel_date) OR sp.cancel_date='0000-00-00 00:00:00')
					WHERE  r.status NOT IN($this->dead_stat)
					AND d.status NOT IN($this->dead_stat)
					$enctype
					$date_cond
					$time_cond
					$join_rep_cond
					$group_cond
					$class_cond
					$groupby
					ORDER BY name_last";
		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result;
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
	}

	#added by VAN 06-26-08
	function getPatientForWardList($datefrom, $dateto, $fromtime, $totime, $enctype, $ward_nr, $grp){
		global $db;

		if (($fromtime)&&($totime)){
				$time_cond = " AND (r.serv_tm BETWEEN '".$fromtime."' AND '".$totime."')";
		}else
			$time_cond = "";

		if (((trim($datefrom))&&(trim($dateto))) || ((trim($datefrom)!='0000-00-00')&&(trim($dateto)!='0000-00-00')))	{
				$date_cond = " AND  (r.serv_dt BETWEEN '".$datefrom."' AND '".$dateto."')";
		}else
			$date_cond = "";

		$sql_ward = "";
		if ($ward_nr!='all')
			$sql_ward = " AND e.current_ward_nr='$ward_nr' ";

		$this->sql="SELECT DISTINCT IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),'') AS agebydbate,
					d.price_cash, 
					e.encounter_type,e.current_dept_nr,e.current_ward_nr, e.current_room_nr,
					IF(e.encounter_type=2, fn_get_department_name(e.current_dept_nr), IF(e.encounter_type=1, 'ER', 
                    IF(e.encounter_type=3 OR e.encounter_type=4,CONCAT(fn_get_ward_name(e.current_ward_nr),' Rm. ',e.current_room_nr),'WN'))) AS location,
					g.name AS grp_name, g.other_name AS grp_name2, r.*, p.*,
					d.service_code, s.name AS service_name, is_forward
					FROM seg_lab_serv AS r
					INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno
					INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code
					LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
					INNER JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code
					INNER JOIN care_person AS p ON p.pid=r.pid
					WHERE  r.status NOT IN($this->dead_stat)
					AND d.status NOT IN($this->dead_stat)
					AND (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0)
					AND is_forward=0
					$sql_ward
					$enctype
					$date_cond
					$time_cond
					$join_rep_cond
					GROUP BY refno
					ORDER BY e.current_ward_nr,e.current_room_nr , r.serv_dt, r.serv_tm, name_last";
		#echo "<br>sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result;
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
	}
	#--------------------------

	function getPatientListDetails($refno){
		global $db;

		$this->sql="SELECT DISTINCT pay.qty,pay.amount_due AS paid, d.price_cash, r.*,
					d.service_code, s.name AS service_name
					FROM seg_lab_serv AS r
					INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno
					LEFT JOIN seg_lab_services AS s ON s.service_code=d.service_code
					LEFT JOIN seg_pay AS sp ON sp.pid=r.pid AND (ISNULL(sp.cancel_date) OR sp.cancel_date='0000-00-00 00:00:00')
										LEFT JOIN seg_pay_request AS pay ON sp.or_no=pay.or_no AND pay.ref_no=r.refno AND ref_source='LD' AND pay.service_code=d.service_code
					LEFT JOIN seg_granted_request AS gr ON gr.ref_no=r.refno AND gr.ref_source='LD' AND gr.service_code=d.service_code
					WHERE r.refno = '".$refno."' 
										AND (r.is_cash=0 OR r.is_urgent=1 OR r.is_tpl=1 OR sp.or_no!='' OR gr.grant_no!='')
					ORDER BY d.service_code";
		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result;
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
	}
	#-----------------------

	function SearchSelect($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE,$mod,$done=0, $samplelist=0, $is_doctor=0, $encounter_nr='', $source='LB', $cond='', $count_sql=0, $group_code=FALSE, $isERIP=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				#$suchwort=$searchkey;
				$searchkey = str_replace("^","'",$searchkey);
				$suchwort=addslashes($searchkey);

				if(is_numeric($suchwort)) {
						#$suchwort=(int) $suchwort;
						$this->is_nr=TRUE;

						if(empty($oitem)) $oitem='refno';
						if(empty($odir)) $odir='DESC'; # default, latest pid at top

						$sql2="    WHERE r.status NOT IN ($this->dead_stat) AND (r.pid = '$suchwort')";
				} else {
						# Try to detect if searchkey is composite of first name + last name
						if(stristr($searchkey,',')){
								$lastnamefirst=TRUE;
						}else{
								$lastnamefirst=FALSE;
						}

						#$searchkey=strtr($searchkey,',',' ');
						$cbuffer=explode(',',$searchkey);

						# Remove empty variables
						for($x=0;$x<sizeof($cbuffer);$x++){
								$cbuffer[$x]=trim($cbuffer[$x]);
								if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
						}

						# Arrange the values, ln= lastname, fn=first name, rd = request date
						if($lastnamefirst){
								$fn=$comp[1];
								$ln=$comp[0];
								$rd=$comp[2];
						}else{
								$fn=$comp[0];
								$ln=$comp[1];
								$rd=$comp[2];
						}
						# Check the size of the comp
						if(sizeof($comp)>1){
								#$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') ";
								$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";

								if(!empty($rd)){
										$DOB=@formatDate2STD($rd,$date_format);
										if($DOB=='') {
												#$sql2.=" AND serv_dt $sql_LIKE '$rd%' ";
										}else{
												if ($done)
													$sql2.=" AND DATE(serv_dt) = '$DOB' ";
												else
													$sql2.=" AND serv_dt = '$DOB' ";
												#$sql2.=" AND serv_dt LIKE '$DOB%' ";
										}
								}
								$sql2.=" AND r.status NOT IN ($this->dead_stat) ";
						}else{
								# Check if * or %
								if($suchwort=='%'||$suchwort=='%%'){
										#return all the data
										$sql2=" WHERE r.status NOT IN ($this->dead_stat) ";
								}elseif($suchwort=='now'){
										#$sql2=" WHERE r.serv_dt=now() AND r.status NOT IN ($this->dead_stat) ";
										if ($done)
												$sql2=" WHERE DATE(serv_dt)=DATE(NOW()) AND r.status NOT IN ($this->dead_stat) ";
										else
												$sql2=" WHERE r.serv_dt=DATE(NOW()) AND r.status NOT IN ($this->dead_stat) ";
								}else{
										# Check if it is a complete DOB
										$DOB=@formatDate2STD($suchwort,$date_format);
										if($DOB=='') {
												if(TRUE){
														if($fname){
																#$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%') ";
																$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%') ";
														}else{
																$sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
														}
												}else{
														$sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
												}
										}else{
												if ($done)
													$sql2=" WHERE DATE(serv_dt) = '$DOB' ";
												else
													$sql2=" WHERE serv_dt = '$DOB' ";
												#$sql2=" WHERE serv_dt LIKE '$DOB%' ";
										}
										$sql2.=" AND r.status NOT IN ($this->dead_stat) ";
								}
						}
				 }
			 $sql_erip = "";
			 if ($isERIP){
				 $sql_erip = " AND (r.is_urgent=1 OR e.encounter_type IN (1,3,4)) ";
			 }

			 $sql_source = '';
			 #REQUEST LIST
			 if ($mod){
						if ($source=='LB'){
							#added by VAN 05-24-2010
							$sql_sample = "";
							$sql_sample_gr = "";

							if ($samplelist){
								$sql_sample = " INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno ";
								$sql_sample_gr = " GROUP BY r.refno ";

								if ($group_code=='1')
									$sql_source .= " AND is_forward = 1";
								else if($group_code=='2')
									$sql_source .= " AND is_forward = 0";
							}else{
								if($group_code && $group_code!='0'){
									if($group_code=='1')
										$sql_source .= " AND ISNULL(encounter_type) ";
									if($group_code=='2')
										$sql_source .= " AND encounter_type = 2 ";
									if($group_code=='3')
										$sql_source .= " AND encounter_type = 5 ";
									if($group_code=='4')
										$sql_source .= " AND encounter_type = 1 ";
									if($group_code=='5')
										$sql_source .= " AND encounter_type IN (3,4,6) ";
								}
							}

							$sql_source .= " AND ref_source = '".$source."'";
						}else{
							 $sql_source .= " AND ref_source = '".$source."'";
						}

						if(isset($oitem)&&!empty($oitem)) $sql3 =" $sql_erip $sql_source $cond $sql_sample_gr ORDER BY is_urgent DESC,refno DESC,r.serv_dt DESC ";

						$this->sql= "SELECT SQL_CALC_FOUND_ROWS r.refno,p.name_last,p.name_first, p.name_middle, p.pid, p.sex,
														IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
														p.date_birth,r.encounter_nr, e.encounter_type,
														r.serv_dt,r.serv_tm, r.is_urgent, r.is_cash, r.is_tpl,
														e.current_ward_nr,e.current_room_nr,e.current_dept_nr,e.current_att_dr_nr
														FROM seg_lab_serv AS r ".$sql_sample ."
														INNER JOIN care_person AS p ON p.pid=r.pid
														LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
														$sql2
														$sql3
												 ";
							#$this->mod = "list";
				}else{
					 #DONE LIST

					 $sql_source .= " AND ref_source = '".$source."'";

					 if(isset($oitem)&&!empty($oitem)) $sql3 =" $sql_erip $sql_source $cond ORDER BY is_urgent DESC,refno DESC,r.serv_dt DESC ";

					 #added by VAN 07-02-08
						if ($done){
								$served = " AND d.is_served = 1 ";

								if ($source=='LB'){
									$join_sql = " INNER JOIN seg_lab_hclab_orderno AS o ON o.refno=r.refno
																INNER JOIN seg_lab_results AS res ON res.order_no=o.lis_order_no ";
								}
						}else{
								#UNDONE LIST
							 $served = " AND d.is_served = 0 ";
							 $join_sql = "";
						}

						#$cond = " AND ((r.is_cash=1 AND (gr.grant_no IS NOT NULL OR pay.or_no IS NOT NULL)
						#											 OR r.is_cash=0 OR is_tpl!=0 OR is_urgent=1 OR type_charge<>0)) ";
						$cond = " AND (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0) ";

						$this->sql = "SELECT DISTINCT SQL_CALC_FOUND_ROWS r.refno,p.name_last,p.name_first, p.name_middle, p.pid, p.sex,
														IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
														p.date_birth,r.encounter_nr, e.encounter_type, d.request_flag,
														d.service_code, r.serv_dt,r.serv_tm, r.is_urgent, r.is_cash, r.is_tpl, s.name AS service_name
														FROM seg_lab_serv AS r
														INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno
														INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code
														INNER JOIN care_person AS p ON p.pid=r.pid
														LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
														$join_sql
														$sql2
														$served
														$cond
														$sql3
												 ";
					 #$this->mod = "done";
				}

				$this->count_sql = $count_sql;
				#COUNTSEARCH SELECT
				if ($count_sql){
						if ($this->result=$db->Execute($this->sql)) {
								if ($this->count=$this->result->RecordCount()) {
										return $this->result;
								}
								else{return FALSE;}
						}else{return FALSE;}
				}else{
						#SEARCH SELECT
						if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
						}else{return false;}
				}
	}

	function SearchService($group_code, $searchkey='',$multiple=0,$maxcount=100,$offset=0,$area='',$codenum=0,$count_sql=0,$frommgr=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		if ($group_code)
				$grp_cond = " AND s.group_code='".$group_code."' ";
			else
				$grp_cond = "";

		if ($area=='ER')
			$area_cond = " AND is_ER=1 AND only_in_clinic=1 ";
		elseif (($area=='clinic') || ($area=='ward'))
						$area_cond = " AND only_in_clinic=1 ";
		else
			$area_cond = "";

		#if ($frommgr)
			$group_bb_cond = " ";
		#else
			#$group_bb_cond = " AND s.group_code!='B' ";

		if ($multiple){
			$keyword = $searchkey;

		if ($codenum)
				$cond_where = " AND (s.code_num IN (".$keyword.")) ";
		else
				$cond_where = "  AND (s.service_code IN (".$keyword.")) ";

			$this->sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g
									WHERE s.group_code=g.group_code
							$grp_cond
							$cond_where
							AND s.status NOT IN (".$this->dead_stat.")
							$group_bb_cond
							$area_cond
							ORDER BY s.name";

		}else{
			# convert * and ? to % and &
			$searchkey=strtr($searchkey,'*?','%_');
			$searchkey=trim($searchkey);
			#$suchwort=$searchkey;
			$searchkey = str_replace("^","'",$searchkey);
			$keyword = addslashes($searchkey);

			if (is_numeric($keyword)){
				$this->sql = "SELECT SQL_CALC_FOUND_ROWS s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g
											WHERE s.group_code=g.group_code
								$grp_cond
											AND (s.service_code = '".$keyword."'
										OR s.name LIKE '".$keyword."' OR s.code_num LIKE '".$keyword."')
										AND s.status NOT IN (".$this->dead_stat.")
										$group_bb_cond
										$area_cond
									ORDER BY s.name";
			}else{
					$this->sql = "SELECT SQL_CALC_FOUND_ROWS s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g
									WHERE s.group_code=g.group_code
								$grp_cond
										AND (s.service_code LIKE '%".$keyword."%'
									OR s.name LIKE '%".$keyword."%' OR s.code_num LIKE '%".$keyword."%')
									AND s.status NOT IN (".$this->dead_stat.")
									$group_bb_cond
									$area_cond
									ORDER BY s.name";
			}
		}
		#-----------------

		#$this->count_sql = $count_sql;
				#COUNTSEARCH SELECT
				if ($count_sql){
						if ($this->result=$db->Execute($this->sql)) {
								if ($this->count=$this->result->RecordCount()) {
										return $this->result;
								}
								else{return FALSE;}
						}else{return FALSE;}
				}else{
						#SEARCH SELECT
						if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
						}else{return false;}
				}
	}

	#---------------------------

#added by VAN 03-10-08
	function countSearchGroup($searchkey='',$maxcount=100,$offset=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$this->sql = "SELECT * FROM $this->tb_lab_service_groups
							WHERE (name LIKE '%".$keyword."%' OR other_name LIKE '%".$keyword."%')
							AND status NOT IN ($this->dead_stat)
							ORDER BY name";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function SearchGroup($searchkey='',$maxcount=100,$offset=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$this->sql = "SELECT * FROM $this->tb_lab_service_groups
							WHERE (name LIKE '%".$keyword."%' OR other_name LIKE '%".$keyword."%')
							AND status NOT IN ($this->dead_stat)
							ORDER BY name";

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

	function deleteServiceGroup($group_code){
		global $HTTP_SESSION_VARS;
		#$userid = $HTTP_SESSION_VARS['sess_temp_userid'];
		$history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." [".$HTTP_SESSION_VARS['sess_user_name']."]\\n");
		$this->sql="UPDATE $this->tb_lab_service_groups ".
						" SET status='deleted', history=".$history.", ".
						" modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
						" WHERE group_code = '$group_code'";
			return $this->Transact();
	}
#----------------------------

		function cleargrantLabRequest($refno) {
				global $db;
				$refno = $db->qstr($refno);
				$this->sql = "DELETE FROM seg_granted_request WHERE ref_no=$refno AND ref_source='LD'";

				return $this->Transact();
		}

	/**
		* Inserts the (a) granted Laboratory request into 'seg_granted_request' table.
		* @access public
		* @param Array Data to by reference
		* @return boolean
		* @created : burn, October 25, 2007
		*/

	function grantLabRequest($data){
		global $db;

		extract($data);
		#print_r($data);
		$arrayItems = array();

		foreach ($items as $key => $value){
			#echo "<br>pnet = ".$value.' - '.$pnet[$key];
			#echo "<br>pcash = ".$value.' - '.$pcash[$key]."<br>";

			if (isset($pnet[$key])){
				if (floatval($pnet[$key])==0.00){
					$tempArray = array($value);
					array_push($arrayItems,$tempArray);
				}
			}elseif (isset($pcash[$key])){
				if (floatval($pcash[$key])==0.00){
					$tempArray = array($value);
					array_push($arrayItems,$tempArray);
				}
			}
		}
		#print_r($arrayItems);
		if (empty($arrayItems))
			return TRUE;

		$this->cleargrantLabRequest($refno);

		$index = "ref_no, ref_source, service_code";
		$values = "$refno, 'LD', ?";   # NOTE: 'LD'=laboratory

		$this->sql="INSERT INTO seg_granted_request ($index) VALUES ($values)";
		#echo "grant = ".$this->sql;
		if ($db->Execute($this->sql,$arrayItems)) {
			if ($db->Affected_Rows()) {
				$this->updateCharityLabRequest($refno,$arrayItems);
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }

	}# end of function grantLabRequest


	/**
	* Removes a Laboratory Service Transaction Details record from the database's 'seg_lab_servdetails' table.
	* @access public
	* @param string Reference number of the Transaction
	* @param int Entry number
	* @return boolean
	*    documented by: burn Sept. 7, 2006
	*/
	function RemoveTransactionDetails($refno, $entrynum) {
		$this->useLabServDetails();
		$this->sql="DELETE FROM $this->coretable WHERE refno='$refno' AND entrynum=$entrynum";
		return $this->Transact();
	}
	/*
	* Retrieves a Laboratory Service Transaction Details record from the database's 'seg_lab_servdetails' table.
	* @access public
	* @param string Reference number of the Transaction
	* @return boolean OR
	*         the Laboratory Service Transaction Details record including the service and group service names
	*    documented by: burn Sept. 8, 2006
	*/
	function GetTransactionDetails($refno) {
		global $db;
		# $tb_products=$this->tb_pharma_products;
		$this->useLabServDetails();
		$this->count=0;
		# $this->sql="SELECT $this->coretable.*, ".$tb_products.".artikelname FROM $this->coretable,$tb_products WHERE refno='$refno' AND $this->coretable.bestellnum=".$tb_products.".bestellnum";
		$this->sql="SELECT $this->coretable.*,
											 ".$this->tb_lab_service_groups.".name
											 ".$this->tb_lab_services.".name
								FROM $this->coretable, $this->tb_lab_service_groups, $this->tb_lab_services
					WHERE refno = '$refno'
						AND $this->coretable.service_code = ".$this->tb_lab_services.".service_code
						AND ".$this->tb_lab_services.".group_code = ".$this->tb_lab_service_groups.".group_code";

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	#------------ added by VAN 12-20-07
	function hasResult($refno, $code='') {

		global $db;

		if ($code)
			#$cond = "refno = '$refno' AND service_code = '$code'";
			$cond = "order_no = '$refno' AND service_code = '$code'";
		else
			#$cond = "refno = '$refno'";
			$cond = "order_no = '$refno'";

		#$this->sql="select * FROM seg_lab_results WHERE refno = '$refno'";
		$this->sql="select * FROM seg_lab_results WHERE $cond";

		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}

	function getLabResult($refno, $service_code){
		global $db;

				$code_cond = "";
				if ($service_code)
						$code_cond = " AND h.service_code='$service_code' ";

		$this->sql="SELECT h.*, d.*
								FROM seg_lab_results AS h
						INNER JOIN seg_lab_results_details AS d
						ON h.refno = d.refno /*AND h.service_code=d.test_code*/
						WHERE h.refno='$refno'
						$code_cond";

		if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			return FALSE;
		}
	}

	function getLabResult_order($pid, $order_no, $service_code){
		global $db;

				$code_cond = "";

				if (empty($order_no))
					$order_no = 'NULL';

				if ($service_code)
						$code_cond = " AND h.service_code='$service_code' ";
		#edited by VAN 12-30-2011
		$this->sql="SELECT h.*, d.*,g.name, d.test_code,d.test_name,
											 h.tg_code,h.service_code,d.parent_item,
											 s.service_code AS service_code_lab,s.name AS service_name,s.oservice_code, s.ipdservice_code, s.group_code AS grp,
                                             (SUBSTRING(MAX(CONCAT(h.trx_dt,d.result_value)),20)) AS result_value,
                                             (SUBSTRING(MAX(CONCAT(h.trx_dt,d.unit)),20)) AS unit,
                                             (SUBSTRING(MAX(CONCAT(h.trx_dt,d.ranges)),20)) AS ranges
									FROM seg_lab_results AS h
									INNER JOIN seg_lab_results_details AS d ON h.refno = d.refno
									INNER JOIN seg_lab_service_groups AS g ON g.group_code=substr(h.tg_code,1,1)
									INNER JOIN seg_lab_services AS s
											ON (s.service_code=h.service_code OR s.oservice_code=h.service_code OR s.ipdservice_code=h.service_code) 
											AND s.status NOT IN ($this->dead_stat)
								WHERE h.order_no IN (".$order_no.")
                                AND h.pid='".$pid."'
								$code_cond
                                GROUP BY test_code
								ORDER BY h.tg_code/*, d.parent_item*/, line_no";

		if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			return FALSE;
		}
	}

	#added by VAN 03-07-08
	function getStaffInfo($userid, $password){
		global $db;


		$this->sql="SELECT u.name, u.login_id, u.password, u.personell_nr,
							pr.pid, pr.job_function_title, pr.short_id
						FROM care_users AS u
						INNER JOIN care_personell AS pr ON u.personell_nr=pr.nr
						INNER JOIN care_personell_assignment AS pa ON u.personell_nr=pa.personell_nr
						WHERE pa.location_nr=156
						AND pr.job_function_title LIKE '%head%'
						AND u.login_id='".$userid."'
						AND u.password = '".md5($password)."'";

		if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result->FetchRow();
		} else{
			return FALSE;
		}
	}

	function getLabResultHeader($order_no, $code='',$encounter_nr, $pid, $refno){
		global $db;

				#modified by cha, july 17, 2010
				$code_cond= "";

				if (empty($order_no))
					$order_no = 'NULL';

				if ($code)
						$sql_cond = " h.refno IN (".$order_no.") AND service_code = '$code' ";
				else
						$sql_cond = " order_no IN (".$order_no.")";

		$this->sql="SELECT refno, 1 AS 'in_lis', loc_code, dr_code,h.order_dt,h.trx_dt
											FROM seg_lab_results AS h
						WHERE
						$sql_cond
						AND patient_caseNo='$encounter_nr' AND pid='$pid'

						UNION
								SELECT r.refno, 0 AS 'in_lis',d.request_dept AS loc_code, d.request_doctor AS dr_code, serv_dt AS order_dt, service_date AS trx_dt
								FROM seg_lab_resultdata AS r
								INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno
								INNER JOIN seg_lab_serv AS l ON l.refno=d.refno
								WHERE r.refno='$refno'";

		if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result->FetchRow();
		} else{
			return FALSE;
		}
	}

	function getRepeatRequestInfo($condition){
		global $db;

		$this->sql="SELECT * FROM seg_lab_serv AS s
						INNER JOIN seg_lab_servdetails AS d
						ON s.refno = d.refno
						WHERE $condition";

		if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result->FetchRow();
		} else{
			return FALSE;
		}
	}
	#------------------------------------

	#---------------added by VAN 01-29-08---------
	function getPatientLabResults($pid) {
			global $db;

		$this->sql="SELECT DISTINCT s.refno AS ref, e.encounter_nr, s.pid, s.serv_dt,
						d.service_code AS code, ls.name AS service_name,
						d.request_doctor, CONCAT(p.name_first, ' ', p.name_last) AS dr_name,
						d.request_dept, dp.id, dp.name_formal, d.clinical_info,
						p2.*, r.*, e.*
						FROM seg_lab_serv as s
						INNER JOIN seg_lab_servdetails AS d
						ON s.refno=d.refno
						INNER JOIN seg_lab_services AS ls
						ON ls.service_code=d.service_code
						INNER JOIN care_personell AS pr
						ON pr.nr=d.request_doctor
						INNER JOIN care_person AS p
						ON pr.pid=p.pid
						INNER JOIN care_department AS dp
						ON dp.nr=d.request_dept
						INNER JOIN care_person AS p2
						ON p2.pid=s.pid
						LEFT JOIN care_encounter AS e
						ON s.pid=e.pid
						LEFT JOIN seg_lab_results AS r
						ON r.refno = s.refno
						WHERE s.pid='$pid'";
		#echo "sql = ".$this->sql;

		if($this->result=$db->Execute($this->sql)){
				if($this->rec_count=$this->result->RecordCount()) {
				return $this->result;
			} else {return FALSE;}
		}else {return FALSE;}
	}

	function getLastModifyTime($enc_nr=0){
		global $db;
		$buf;
		$row;
		if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;
		$this->sql="SELECT modify_dt FROM seg_lab_serv WHERE encounter_nr='$enc_nr' AND status NOT IN ($this->dead_stat) ORDER BY modify_dt DESC";
		if($buf=$db->SelectLimit($this->sql,1)){
				if($buf->RecordCount()) {
				$row=$buf->FetchRow();
				return $row['modify_dt'];
			} else {return FALSE;}
		}else {return FALSE;}
	}

	#---------------------------------------------

	#added by VAN 04-18-08
	function getSumPerTransaction($refno){
			global $db;
		 $this->sql="SELECT sum(price_cash) AS price_cash,
						 sum(price_cash_orig) AS price_cash_orig,
						 sum(price_charge) AS price_charge
						 FROM seg_lab_servdetails
						 WHERE refno = '$refno'
						 AND status NOT IN ($this->dead_stat)";

			if ($this->result=$db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
						return $this->result->FetchRow();
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}

	function getSumPaidPerTransaction($refno, $pid){
			global $db;
			$this->sql = "SELECT r.ref_no,r.ref_source,SUM(r.amount_due) AS amount_paid,p.*
										FROM seg_pay_request AS r
										INNER JOIN seg_pay AS p ON r.or_no=p.or_no
										WHERE ref_no='$refno' AND ref_source='LD' AND pid='$pid'
										GROUP BY p.or_no";

			if ($this->result=$db->Execute($this->sql)) {
				if ($this->count=$this->result->RecordCount()) {
						return $this->result->FetchRow();
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}

	#----------added by VAN 06-01-08
	function getChargeType(){
			global $db;
		 $this->sql="SELECT * FROM seg_type_charge ORDER BY charge_name";
				#echo "sql = ".$this->sql;
			if ($this->result=$db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
						return $this->result;
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}
	#-------------------------------

	function getRequestedServicesPerGroup($refno, $grpcode) {
		global $db;
		$this->useLabServices();
		if(empty($sort)) $sort='name';

		if ($grpcode!='all')
			$isgroup = " AND sg.group_code = '$grpcode' ";
		else
			$isgroup = " ";

		$this->sql="SELECT sd.*, ss.name, sg.group_code, sg.name AS groupnm, ss.is_socialized
						FROM $this->tb_lab_serv AS s,
									$this->tb_lab_servdetails AS sd,
								 $this->tb_lab_service_groups AS sg,
								 $this->tb_lab_services AS ss
						WHERE s.refno = sd.refno
						AND   sd.service_code = ss.service_code
						AND   ss.group_code = sg.group_code
						AND   s.refno = '$refno'
						$isgroup
						AND s.status NOT IN ($this->dead_stat)
						ORDER BY ss.name,sg.name";

			#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
				$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			return FALSE;
		}
	}

	#----added by VAN 07-01-08----
	function getChargeTypeInfo($type_charge) {
		global $db;

		$this->sql="SELECT * FROM seg_type_charge
					WHERE id = '$type_charge'";

		if ($this->result=$db->Execute($this->sql)) {
				$this->count=$this->result->RecordCount();
			return $this->result->FetchRow();
		} else{
			return FALSE;
		}
	}
	#------------------------

	#added by VAN 07-05-08
	function getSocialDiscountInfo($discountid, $service_code) {
		global $db;

		$this->sql="SELECT * FROM seg_service_discounts
					WHERE discountid='$discountid'
					AND service_code='$service_code'
					AND service_area='LB'";

		if ($this->result=$db->Execute($this->sql)) {
				$this->count=$this->result->RecordCount();
			return $this->result->FetchRow();
		} else{
			return FALSE;
		}
	}
	#-------------------

	#----added by VAN 07-23-08
	function getPatientRefnoInfo($refno) {
		global $db;

			$this->sql="SELECT r.serv_dt AS order_date,
					r.serv_tm, 'N' AS trx_ID, 'N' AS trx_status,
					r.encounter_nr, r.pid, ' ' AS altID,
					IF(r.is_urgent, 'U', 'R') AS priority,
					r.ordername AS patient_name,
					p.name_first, p.name_last, p.name_middle,
					IF(r.encounter_nr=' ',0, e.encounter_type) AS encounter_type,
					(CASE IF(r.encounter_nr=' ',0, e.encounter_type) WHEN 0 THEN 'WN' WHEN 1 THEN 'ER' WHEN 2 THEN 'OP' ELSE 'IN' END) AS patient_type,
					(CASE IF(r.encounter_nr=' ',0, e.encounter_type) WHEN 0 THEN 'WIN' WHEN 1 THEN 'ER' WHEN 2 THEN e.current_dept_nr ELSE e.current_ward_nr END) AS loc_code,
					(CASE IF(r.encounter_nr=' ',0, e.encounter_type) WHEN 0 THEN 'WIN' WHEN 1 THEN 'ER' WHEN 2 THEN (SELECT name_formal FROM care_department WHERE nr=e.current_dept_nr)
						ELSE (SELECT name FROM care_ward WHERE nr=e.current_ward_nr) END) AS loc_name,
					d.request_doctor, d.request_dept, pr.pid AS dr_pid,
					CONCAT(
						'Dr. ',CAST((SELECT name_first FROM care_person AS p WHERE p.pid=pr.pid) AS BINARY),
						' ',
						SUBSTRING((SELECT name_middle FROM care_person AS p WHERE p.pid=pr.pid), 1, 1),
						IF(
							 (SELECT name_middle FROM care_person AS p WHERE p.pid=pr.pid)='', ' ','. '
							),
						(SELECT name_last FROM care_person AS p WHERE p.pid=pr.pid)) AS dr_name,
					d.clinical_info, p.date_birth, p.sex
					FROM seg_lab_serv AS r
					LEFT JOIN seg_lab_servdetails AS d ON d.refno=r.refno
					LEFT JOIN care_personell AS pr ON pr.nr=d.request_doctor
					LEFT JOIN care_person AS p ON p.pid=r.pid
					LEFT JOIN care_encounter AS e ON e.pid=r.pid
					WHERE d.refno = '".$refno."'
					GROUP BY d.refno";

		if ($this->result=$db->Execute($this->sql)) {
				$this->count=$this->result->RecordCount();
			return $this->result->FetchRow();
		} else{
			return FALSE;
		}
	}
	#-----------------------

	function getMonth($mnth){
		switch($mnth){
			case  1: $month ='January';
						break;
			case  2: $month ='February';
						break;
			case  3: $month ='March';
						break;
			case  4: $month ='April';
						break;
			case  5: $month ='May';
						break;
			case  6: $month ='June';
						break;
			case  7: $month ='July';
						break;
			case  8: $month ='August';
						break;
			case  9: $month ='September';
						break;
			case 10: $month ='October';
						break;
			case 11: $month ='November';
						break;
			case 12: $month ='December';
						break;
		}
		return $month;
	}

	#----------------------
	#------added by VAN 08-21-08
	#-----modified by cHA, July 6, 2010 (followed cGH)
	function ServedLabRequest($refno, $service_code, $is_served, $date_served, $service_code=''){
		global $db, $HTTP_SESSION_VARS;
		$ret=FALSE;

		#$history = CONCAT(history,"To be Served in Laboratory : ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");

		/*$this->sql="UPDATE seg_lab_servdetails SET
						is_served='".$is_served."',
						date_served='".$date_served."',
						clerk_served_by='".$HTTP_SESSION_VARS['sess_user_name']."',
						clerk_served_date=NOW()
						WHERE refno = '".$refno."'
						AND service_code = '".$service_code."'"; */
		if($service_code)
				$this->sql="UPDATE seg_lab_servdetails SET
										is_served='".$is_served."',
										date_served='".$date_served."',
										clerk_served_by='".$HTTP_SESSION_VARS['sess_user_name']."',
										clerk_served_date=NOW(),
										status='done'
										WHERE refno = '".$refno."'
										AND service_code ='$service_code'";
		else
		$this->sql="UPDATE seg_lab_servdetails SET
				is_served='".$is_served."',
				date_served='".$date_served."',
				clerk_served_by='".$HTTP_SESSION_VARS['sess_user_name']."',
				clerk_served_date=NOW(),
				status='done'
				WHERE refno = '".$refno."'
				AND service_code IN (SELECT gp.service_code FROM seg_lab_result_groupparams AS gp WHERE gp.group_id='".$group_id."' UNION SELECT service_code_child FROM seg_lab_result_group AS g WHERE g.service_code IN (SELECT gp.service_code FROM seg_lab_result_groupparams AS gp WHERE group_id='".$group_id."'))";

		#return $this->Transact();

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;

	}
	#----------------------------

	function OfficialLabResult($refno, $group_id, $is_served, $date_served, $service_code=''){
				global $db, $HTTP_SESSION_VARS;
				$ret=FALSE;

				#$history = CONCAT(history,"To be Served in Laboratory : ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				if(($service_code)&&(!$group_id))
						$this->sql="UPDATE seg_lab_servdetails SET
												is_served='".$is_served."',
												date_served='".$date_served."',
												clerk_served_by='".$HTTP_SESSION_VARS['sess_user_name']."',
												clerk_served_date=NOW(),
												status='done'
												WHERE refno = '".$refno."'
												AND service_code ='$service_code'";
				else{
						#edited by VAN 08-18-2010
						$sql = "SELECT fn_get_service_code(".$group_id.",'".$refno."') AS service_list";
						$rs = $db->Execute($sql);
						$row_rs = $rs->FetchRow();

						$this->sql="UPDATE seg_lab_servdetails SET
												is_served='".$is_served."',
												date_served='".$date_served."',
												clerk_served_by='".$HTTP_SESSION_VARS['sess_user_name']."',
												clerk_served_date=NOW(),
												status='done'
												WHERE refno = '".$refno."'
												AND service_code IN (".$row_rs['service_list'].")";

				}
				#echo $this->sql;

				#return $this->Transact();

				if ($db->Execute($this->sql)) {
						if ($db->Affected_Rows()) {
								$ret=TRUE;
						}
				}
				if ($ret)    return TRUE;
				else return FALSE;

		}

	#added by VAN 03-03-09
	function SentOutLabRequest($refno,$group_id,$service_code,$reason){
		global $db, $HTTP_SESSION_VARS;
		$ret=FALSE;

		#$history = CONCAT(history,"To be Served in Laboratory : ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		if ($group_id){
				if ((!$group_id) && (!$service_code)){
					$this->sql="UPDATE seg_lab_servdetails SET
						status='sent-out',
						reason_sent_out='".$reason."',
						sent_out_by='".$HTTP_SESSION_VARS['sess_user_name']."',
						sent_out_date=NOW()
						WHERE refno = '".$refno."'
						AND service_code IN (SELECT fn_get_labtest_request_code_list(".$refno."))";
				}else{
					$this->sql="UPDATE seg_lab_servdetails SET
						status='sent-out',
						reason_sent_out='".$reason."',
						sent_out_by='".$HTTP_SESSION_VARS['sess_user_name']."',
						sent_out_date=NOW()
						WHERE refno = '".$refno."'
						AND service_code IN (".$service_code.")";
				}
		}else{
				$this->sql="UPDATE seg_lab_servdetails SET
						status='sent-out',
						reason_sent_out='".$reason."',
						sent_out_by='".$HTTP_SESSION_VARS['sess_user_name']."',
						sent_out_date=NOW()
						WHERE refno = '".$refno."'
						AND service_code = '".$service_code."'";

		}

		#return $this->Transact();

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;

	}
	#------------------------

	function getLabRequestInfo($refno, $pid, $service_code) {
		global $db;
		$this->useLabServices();

		$this->sql="SELECT (SELECT nr FROM care_department WHERE name_formal LIKE 'Pathology') AS dept_nr,
						s.group_code AS area_code, g.name AS area_name,s.name AS service_name,
						ld.*,ls.*, cp.*
						FROM seg_lab_serv AS ls
						INNER JOIN care_person AS cp ON ls.pid=cp.pid
						INNER JOIN seg_lab_servdetails AS ld ON ld.refno=ls.refno
						INNER JOIN seg_lab_services AS s ON s.service_code=ld.service_code
						INNER JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code
						WHERE ls.refno='$refno'
						AND ls.pid='$pid'
						AND ld.service_code='".$service_code."'
						AND ls.status NOT IN ($this->dead_stat)";
	 #echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
				# $this->rec_count=$this->dept_count;
				return $this->result->FetchRow();
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function getReagents($service_code){
			global $db;
		 $this->sql="SELECT r.reagent_name,sr.*
						FROM seg_lab_service_reagents AS sr
						INNER JOIN seg_lab_reagents AS r ON r.reagent_code=sr.reagent_code
						WHERE sr.service_code='".$service_code."'";

			if ($this->result=$db->Execute($this->sql)) {
				if ($this->count = $this->result->RecordCount()) {
						return $this->result;
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}

	function clearLabReagentProcess($refno, $service_code) {
		global $db;

		$this->sql = "DELETE FROM seg_lab_reagents_usage
						WHERE refno='$refno' AND service_code='$service_code'";
			return $this->Transact();
	}

	function createLabReagentProcess($refno, $service_code, $reagents)	{
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;

		#$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

		#no_film_used
		$this->sql="INSERT INTO seg_lab_reagents_usage(refno, service_code, item_code,item_qty, unit_id, is_unitperpc) ".
						 "VALUES('".$refno."','".$service_code."', ? , ? , ? , ?)";

		$ok=$db->Execute($this->sql,$reagents);
		$this->count=$db->Affected_Rows();
		return $ok;

	}

	function getRequestExamInfo($refno, $service_code){
		global $db;

		$this->sql ="SELECT a.area_code, g.dept_nr  AS dept_nr,d.*
						FROM seg_lab_servdetails AS d
						INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code
						INNER JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code
						LEFT JOIN seg_areas AS a ON a.dept_nr=g.dept_nr
						WHERE d.refno='".$refno."' AND d.service_code='".$service_code."'";

		if ($this->result=$db->Execute($this->sql)){
			#$this->count=$this->result->RecordCount();
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

		#added by VAN 12-07-08
		function countSearchReagents($searchkey='',$maxcount=100,$offset=0) {
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				#$suchwort=$searchkey;
				$searchkey = str_replace("^","'",$searchkey);
				$keyword=addslashes($searchkey);

				$this->sql = "SELECT * FROM seg_lab_reagents
													WHERE (reagent_name LIKE '%".$keyword."%' OR other_name LIKE '%".$keyword."%')
													AND status NOT IN ($this->dead_stat)
													ORDER BY reagent_name";

				#echo "sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		function SearchReagents($searchkey='',$maxcount=100,$offset=0){
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				#$suchwort=$searchkey;
				$searchkey = str_replace("^","'",$searchkey);
				$keyword=addslashes($searchkey);

				$this->sql = "SELECT * FROM seg_lab_reagents
													WHERE (reagent_name LIKE '%".$keyword."%' OR other_name LIKE '%".$keyword."%')
													AND status NOT IN ($this->dead_stat)
													ORDER BY reagent_name";

				if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
				}else{return false;}
		}

		function deleteReagent($reagent_code){
				global $HTTP_SESSION_VARS;

				$history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." [".$HTTP_SESSION_VARS['sess_user_name']."]\\n");
				$this->sql="UPDATE seg_lab_reagents  ".
												" SET status='deleted', history=".$history.", ".
												" modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
												" WHERE reagent_code = '$reagent_code'";
			return $this->Transact();
		}

		function getAllLabReagentInfo($reagent_code=''){
				global $db;

				 if ($reagent_code)
						 $cond = " WHERE reagent_code='$reagent_code'";
				 else
						 $cond = "";

				 $this->sql="SELECT * FROM seg_lab_reagents
													$cond
												 AND status NOT IN ($this->dead_stat)";
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->result->RecordCount()) {
								 if ($reagent_code)
												return $this->result->FetchRow();
								 else
										 return $this->result;
						} else {
								return FALSE;
						}
				} else {
						return FALSE;
				}
		}

		function saveLabServiceReagents($name, $code, $other_name, $mode, $xcode='')    {
				global $db;
				global $HTTP_SESSION_VARS;
				$ret=FALSE;


				$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

				if ($mode=='save'){
						$this->sql="INSERT INTO seg_lab_reagents(reagent_code, reagent_name, other_name, status, history, create_id, create_dt, modify_id, modify_dt) ".
								"VALUES('".$code."', '".$name."', '".$other_name."', '', CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW())";

				}else{

						$this->sql="UPDATE seg_lab_reagents SET
																		reagent_code ='".$code."',
																		reagent_name='".$name."',
																		other_name='".$other_name."',
																		status='',
																		history=CONCAT(history,'Update: ',NOW(),' [$userid]\\n'),
																		modify_id='$userid',
																		modify_dt=NOW()
																		WHERE reagent_code = '".$xcode."'";
				}

				#echo "sql = ".$this->sql;
				if ($db->Execute($this->sql)) {
						if ($db->Affected_Rows()) {
								$ret=TRUE;
						}
				}else{
						$this->error=$db->ErrorMsg();
				}
				if ($ret)    return TRUE;
				else return FALSE;
		}

		function getAllReagentsInService($service_code){
				global $db;

				$this->sql="SELECT u.unit_name,r.reagent_name, sr.service_code, sr.reagent_code, sr.item_qty,
										sr.unit_id, sr.is_unitperpc
										FROM seg_lab_service_reagents AS sr
										INNER JOIN seg_lab_reagents AS r ON r.reagent_code=sr.reagent_code
										LEFT JOIN seg_unit AS u ON u.unit_id=sr.unit_id
										WHERE sr.service_code='".$service_code."'";
				#echo "sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)) {
				if ($this->count=$this->result->RecordCount()){
						return $this->result;
				 }else{
								return FALSE;
				 }
				 }else{
						return FALSE;
				}
		}

		function clearReagentList($service_code){
				global $db;

				$this->sql = "DELETE FROM seg_lab_service_reagents WHERE service_code='$service_code'";
				return $this->Transact();
		}

		function addReagent($data, $reagent_list){
				global $db;

				$service_code = $db->qstr($data['service_code']);

				$this->sql = "INSERT INTO seg_lab_service_reagents(service_code,reagent_code,item_qty,unit_id,is_unitperpc) VALUES($service_code ,?,?,?,?)";
				#echo "sql = ".$this->sql;
				if($buf=$db->Execute($this->sql,$reagent_list)) {
						$this->saveOK = true;
						if($buf->RecordCount()) {
								return true;
						} else { return false; }
				} else { $this->saveOK = false; return false; }
		}
		#-----------------------

	#added by VAN 01-08-09
	function getBasicLabServiceInfo($ref_nr=''){
		global $db;

		if(empty($ref_nr) || (!$ref_nr)){
			return FALSE;
		}

		$this->sql= "SELECT IF ((request_flag IS NOT NULL),1,0) AS hasPaid, r_serv.*,
										r_serv.parent_refno, r_serv.approved_by_head, r_serv.remarks
									FROM seg_lab_serv AS r_serv
									INNER JOIN seg_lab_servdetails AS r ON r.refno = r_serv.refno
									WHERE r_serv.refno='$ref_nr'
									AND r_serv.status NOT IN ($this->dead_stat) LIMIT 1";

		if ($buf=$db->Execute($this->sql)){
			if($this->count=$buf->RecordCount()) {
				return $buf->FetchRow();
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getBasicLabServiceInfo

//added by daryl
	function getLastWalkinPid()
	{
			global $db;
			$this->sql= "SELECT 
						  pid
						FROM
						  seg_walkin 
						ORDER BY CONVERT(pid, UNSIGNED INTEGER) DESC 
						LIMIT 1 ";

			$resultwalk = $db->Execute($this->sql);
			return $resultwalk;
	}

#added by daryl
function selectifwalkin($pid)
{
	global $db;
	$pid_ = $db->qstr($pid);
	$this->sql="SELECT SUM(CASE WHEN (sw.`pid` = sls.`pid`) THEN 1 ELSE 0 END) AS ifwalk
				FROM seg_walkin AS sw 
				INNER JOIN seg_lab_serv AS sls ON sls.`pid` = sw.`pid` 
				WHERE sw.`pid` = $pid_ ";

	$resultwalk = $db->Execute($this->sql);
	return $resultwalk;

}


	function saveLabRefNoInfoFromArray(&$data){
		global $db, $HTTP_SESSION_VARS;

		$this->useLabServ();
		extract($data);

		$arrayItems = array();
		foreach ($service_code as $key => $value){
			$tempArray = array($value);
			array_push($arrayItems,$tempArray);
		}

		if (empty($area_type))
			$area_type = 'NULL';
		else
			$area_type = "'".$area_type."'";

		if (empty($encounter_nr))
			$encounter_nr = 'NULL';
		else
			$encounter_nr = "'".$encounter_nr."'";


//edited by daryl


		$index = "refno,serv_dt,serv_tm,encounter_nr,pid,is_cash,type_charge,is_urgent,
							is_tpl,create_id,create_dt,history,comments,ordername,orderaddress,
					status,discountid,loc_code,parent_refno,approved_by_head,remarks,
					headID,headpasswd, discount, fromBB, source_req, area_type, is_rdu, is_walkin,is_pe, grant_type, ref_source";

		$values = "'$refno','$serv_dt','$serv_tm',$encounter_nr,'$pid',$is_cash,'$type_charge','$is_urgent',".
					"'$is_tpl','$encoder', NOW(),'$history','$comments','$ordername','$orderaddress',".
					"'$status','$discountid','$loc_code','$parent_refno','$approved_by_head','$remarks',".
					"'$headID','$headpasswd','$discount','$fromBB', '$source_req',$area_type, '$is_rdu', '$is_walkin','$is_pe', '$grant_type', '$ref_source'";



#added by daryl

				if ($pid == "")
		{
				$rrr = $this->getLastWalkinPid();

				$result = $rrr->FetchRow();
				$pid_result = $result['pid'];
				
				if(strpos($ordername, ".") == true){
				 	$walk_name = explode(".", $ordername);

					$lname = $walk_name[0];
					$fname = $walk_name[1];
					$mname = $walk_name[2];
				}else if(strpos($ordername, ",") == true){
					$walk_name = explode(",", $ordername);

					$lname = $walk_name[0];
					$fname = $walk_name[1];
					$mname = $walk_name[2];
				}else if(strpos($ordername, " ") == true){
					$walk_name = explode(" ", $ordername);

					$lname = $walk_name[0];
					$fname = $walk_name[1];
					$mname = $walk_name[2];
				}



				// if (strlen($pid_result) != 4){
				if ($pid_result < 1000){
					$newpid = '1000';
				}else{
				$newpid = $result['pid'] + 1;
				}
				$pid = " ";
				
		$index_walkin = "pid,name_last,name_first,name_middle,address,history,create_id,create_time";			
		$values_walkin ="'$newpid','$lname','$fname','$mname','$orderaddress','$history','$encoder',NOW()";
			
		$index = "refno,serv_dt,serv_tm,encounter_nr,pid,is_cash,type_charge,is_urgent,
							is_tpl,create_id,create_dt,history,comments,ordername,orderaddress,
					status,discountid,loc_code,parent_refno,approved_by_head,remarks,
					headID,headpasswd, discount, fromBB, source_req, area_type, is_rdu, is_walkin,is_pe, grant_type, ref_source, walkin_pid";

		$values = "'$refno','$serv_dt','$serv_tm',$encounter_nr,'$newpid',$is_cash,'$type_charge','$is_urgent',".
					"'$is_tpl','$encoder', NOW(),'$history','$comments','$ordername','$orderaddress',".
					"'$status','$discountid','$loc_code','$parent_refno','$approved_by_head','$remarks',".
					"'$headID','$headpasswd','$discount','$fromBB', '$source_req',$area_type, '$is_rdu', '$is_walkin','$is_pe', '$grant_type', '$ref_source','$newpid'";

		$this->sql1 = "INSERT INTO seg_walkin ($index_walkin)
							VALUES ($values_walkin)";
						$db->Execute($this->sql1);  

		$this->sql = "INSERT INTO $this->coretable ($index)
							VALUES ($values)";

		}else{
		$this->sql = "INSERT INTO $this->coretable ($index)
							VALUES ($values)";
		}
            
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				
				$ok = $this->saveLabRequestInfoFromArrayNEW($data);
				$this->grantLabRequest($data);
                                
                                #update table seg_blood_hact for hact patient
                if ($ref_source=='BB'){ 
                                $row_hact = $this->getHactInfo($pid);
                                $request_time = $serv_dt." ".$serv_tm; 
                                if ($row_hact['pid']){
                                   if ($is_hact)
                                       $status = 'hact'; 
                                   else   
                                       $status = 'normal'; 
                    
                                   $this->updateHactPatient($pid, $status, $request_time);
                                }else{
                                   if ($is_hact){
                                      $this->addHactPatient($pid, 'hact', $request_time);
                                   }
                                }
                    
                    #update patient's blood type
                    if ($blood_type){
                        $details = (object) 'details';
                        $details->pid = $pid;
                        $details->blood_type = $blood_type;
                        $request_time = date("Y-m-d H:i:s", strtotime($request_time));
                        $details->create_tm = $request_time;
                        $this->updateBlood_Type($details);
                    }
                }    
                                #===============
      
                #added by VAS 04-23-2012
                #save to seg_lab_serv_blood table to store the data if reference
                #source from blood bank and the requests are blood products and not just blood test
               /* if ($ref_source=='BB'){
                  $this->sql = "INSERT INTO $this->coretable.'_blood' ($index)
                                VALUES ($values)";  
                  $db->Execute($this->sql);              
                }*/
				#if (!$ok) $this->FailTrans();
				#$this->CompleteTrans();

				return $refno;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end function saveLabRefNoInfoFromArray

	function saveLabRequestInfoFromArrayNEW(&$data){
		#global $db, $HTTP_SESSION_VARS, $db_hclab, $dblink_hclab_ok;
                #$hclabObj = new HCLAB;
		global $db, $HTTP_SESSION_VARS;

		$this->useLabServDetails();
		extract($data);
		#print_r($data);
		$arrayItems = array();

		$with_monitor_array = array();
		for ($i=0; $i<sizeof ($arrayMonitorItems);$i++){
			$is_monitor_id = $arrayMonitorItems[$i][0];
			$with_monitor_array[] = $is_monitor_id;
		}

		foreach ($service_code as $key => $value){
				//for monitoring
				if (in_array($service_code[$key],$with_monitor_array))
					$is_monitor = 1;
				else
					$is_monitor = 0;

				if (in_array($value,$arraySampleItems))
					$is_forward = 1;
				else
					$is_forward = 0;

			$tempArray = array($value,$pnet[$key],$pcash[$key],$pcharge[$key],$request_doctor[$key],
																$request_dept[$key],$is_in_house[$key],$clinical_info[$key],
																$quantity[$key], $is_forward, $is_monitor);

			array_push($arrayItems,$tempArray);

		}
        $flag = $request_flag;
		if (empty($request_flag))
			$request_flag = 'NULL';
		else
			$request_flag = "'".$request_flag."'";

        #added by VAS 07-04-2012
        #add history for update
        $history = $this->ConcatHistory("Create request details [$flag] at laboratory ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
            
		$index = "refno, service_code, price_cash, price_cash_orig, price_charge, request_doctor, request_dept,
							is_in_house, clinical_info, quantity, is_forward, is_monitor, request_flag, status,history";
		$values = "'$refno', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, $request_flag,'pending',$history";

		$this->sql = "INSERT INTO $this->coretable ($index)
							VALUES ($values)";
        #echo "<br>details = ".$this->sql."<br>";
        #print_r($arrayItems);
		if ($db->Execute($this->sql,$arrayItems)) {
			#if ($with_sample){
				#$this->DoneRequest($refno,$arrayItemsList);

                #added by VAS 03-22-2012
                #for the meantime, since applying coverage is just available in blood bank module
                #will apply the coverage when a sample was received, it means it was served by the hospital
                # Handle applied coverage for PHIC and other benefits
                #temp value, must change
                $this->apply_coverage($refno, $arrayItemsList);
                
                $this->DoneRequest($refno,$arrayItemsList);
            #}
            
			if ($with_monitor){
				$this->clearOrdersMonitorList($refno);
				$this->addOrdersMonitor($refno,$arrayMonitorItems);
			}else{
				$this->clearOrdersMonitorList($refno);
			}

			if($with_lis){
                
                $row_order = $this->getLabOrderNoLIMIT($refno);
                if ($row_order['lis_order_no']){
                    $data_HCLAB['POH_ORDER_NO'] = $row_order['lis_order_no'];
                }else{
                    $new_order_no = $this->getLastOrderNo();
                    $data_HCLAB['POH_ORDER_NO'] = $new_order_no;
                    $okHCLAB = $this->insert_Orderno_HCLAB($new_order_no, $refno);
                    $ok = $this->update_HCLabRefno_Tracker($new_order_no);
                }
                
                #echo "<br> save = ".$HTTP_SESSION_VARS['connection_type'];
                if ($HTTP_SESSION_VARS['connection_type']=='odbc'){
                    global $db_hclab, $dblink_hclab_ok;
                    $hclabObj = new HCLAB;
				 #require_once($root_path.'include/inc_hclab_connection.php');
				 #$dblink_hclab_ok = $hclabObj->ConnectHCLAB();
				 #print_r($arrayLISItems);
				 #$new_order_no = $this->getLastOrderNo();
				 #echo "<br> new_order_no = ".$new_order_no;
				if ($dblink_hclab_ok){
					#echo "sulod";
					    #$new_order_no = $this->getLastOrderNo();
					    #$data_HCLAB['POH_ORDER_NO'] = $new_order_no;
					#header
					$okHCLAB = $hclabObj->addOrderH_to_HCLAB($data_HCLAB);
					#details
					#set NULL
					$deleted_test_array = array();
					$okHCLAB = $hclabObj->clearOrderList_to_HCLAB($refno, $deleted_test_array);
					$okHCLAB = $hclabObj->addOrders_to_HCLAB($refno, $arrayLISItems);
					#echo "<br>details = ".$hclabObj->sql;
					#if ($okHCLAB)
						    #$okHCLAB = $this->insert_Orderno_HCLAB($new_order_no, $refno);
					#if ($okHCLAB)
						    #$ok = $this->update_HCLabRefno_Tracker($new_order_no);
				}else{
					# can't connect to HCLAB
					echo '<em class="warn">Sorry, HCLAB connection failed..</em>';
				}

                }else{
				     #hl7 connection
                     /*$new_order_no = $this->getLastOrderNo();
                 $data_HCLAB['POH_ORDER_NO'] = $new_order_no;
				
                 $okHCLAB = $this->insert_Orderno_HCLAB($new_order_no, $refno);
                     $ok = $this->update_HCLabRefno_Tracker($new_order_no);*/
			}
           }

			#added by VAN 05-31-2011
			/*if ($with_sample_rec){
				$db->StartTrans();
				$bSuccess = $this->clearReceivedSample_h($refno);
				$bSuccess = $this->clearReceivedSample_d($refno);
				$bSuccess = $this->SaveReceivedSample_h($refno,$arraySampleItems_h);
				$bSuccess = $this->SaveReceivedSample_d($refno,$arraySampleItems_d);
                
				if (!$bSuccess) $db->FailTrans();
				$db->CompleteTrans();
			}*/
			#------------------------

			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end function saveLabRequestInfoFromArrayNEW

	function getAllLabInfoByRefNo($ref_nr='', $ref_source='LB', $fromSS=0, $discount=0, $discountid=''){
		global $db;

		if(empty($ref_nr) || (!$ref_nr)){
			return FALSE;
		}

		$this->sql="SELECT
									r.is_forward,is_monitor, m.every_hour, m.no_takes,
									/*r.price_cash AS discounted_price,*/
									IF($fromSS,
										IF(r_services.is_socialized=0,
											IF(r_serv.is_cash,r_services.price_cash,r_services.price_charge),
												 IF(sd.price,sd.price,
											IF(1,
													(r_services.price_cash*(1-$discount)),
													(r_services.price_charge*(1-$discount))
											)
										)
											 )
											,r.price_cash
									) AS discounted_price,
									IF ((request_flag IS NOT NULL),1,0) AS hasPaid,request_flag,
									r_serv.refno, r_serv.serv_dt, r_serv.encounter_nr,
									r_serv.discountid, r_serv.discount,
									r_serv.pid, r_serv.ordername, r_serv.orderaddress,
									r_serv.is_cash, r_serv.is_urgent, r_serv.comments,
									r_serv.status, r_serv.history, r_serv.create_dt,
									r.refno, r.clinical_info, r.service_code,
									r.price_cash, r.price_cash_orig, r.price_charge,
									r.is_in_house, r.request_doctor,r.quantity,
									r_serv.parent_refno, r_serv.parent_refno, r_serv.approved_by_head, r_serv.remarks,
									r.is_forward, r.is_served, r.date_served, r.is_monitor,
									r.status AS request_status,
									IF((ISNULL(r.is_in_house) ||  r.is_in_house='0'),
										r.request_doctor,
										IF(STRCMP(r.request_doctor,CAST(r.request_doctor AS UNSIGNED INTEGER)),
										r.request_doctor,
										fn_get_personell_name(r.request_doctor))
									) AS request_doctor_name,
									r.request_dept,
									r_services.service_code, r_services.name, r_services.is_socialized,
									r_serv_group.group_code, r_services.in_lis, r_services.oservice_code, r_services.ipdservice_code,
									p.name_first, p.name_middle, p.name_last, p.date_birth, p.sex, rc.qty_received
									FROM seg_lab_serv AS r_serv
									LEFT JOIN seg_lab_servdetails AS r ON r.refno=r_serv.refno
									LEFT JOIN care_person AS p ON p.pid = r_serv.pid
									LEFT JOIN seg_lab_services AS r_services ON r.service_code = r_services.service_code
									LEFT JOIN seg_service_discounts AS sd ON
										 sd.service_code=r_services.service_code AND sd.discountid='$discountid' AND service_area='LB'
									LEFT JOIN seg_lab_service_groups AS r_serv_group ON r_serv_group.group_code = r_services.group_code
									LEFT JOIN seg_lab_serv_monitor AS m ON m.refno=r_serv.refno AND m.service_code=r.service_code
									LEFT JOIN seg_blood_received_sample_d AS rc ON rc.refno=r_serv.refno AND rc.service_code=r.service_code
									WHERE r_serv.refno='$ref_nr'
										AND r_serv.ref_source = '$ref_source'
										AND r_serv.status NOT IN ($this->dead_stat)
										AND r.status NOT IN ($this->dead_stat)
										GROUP BY r.service_code
									ORDER BY create_dt ASC ";

		if ($buf=$db->Execute($this->sql)){
			if($buf->RecordCount()) {
				return $buf;
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getAllLabInfoByRefNo

		 /*
	* Updates laboratory request info in table 'seg_lab_servdetails'
	* @param Array Data to by reference
	* @return boolean
	* @created : van, September 4, 2007
	*/

	function updateLabRefNoInfoFromArray(&$data){
		global $HTTP_SESSION_VARS, $_SESSION, $dbtype;

		extract($data);
        
		$this->useLabServ();
		$this->data_array=$data;

		// remove probable existing array data to avoid replacing the stored data
		unset($this->data_array['service_code']);
		unset($this->data_array['pnet']);
		unset($this->data_array['pcash']);
		unset($this->data_array['pcharge']);
		unset($this->data_array['request_doctor']);
		unset($this->data_array['request_dept']);
		unset($this->data_array['is_in_house']);
		unset($this->data_array['clinical_info']);
		unset($this->data_array['quantity']);
		unset($this->data_array['status']);
		unset($this->data_array['is_forward']);
		unset($this->data_array['is_monitor']);
        
		$current_list = $this->getListedRequestsByRefNo($data['refno']);
        $current_deleted_list = $this->getListedRequestsByRefNo($data['refno'],"AND status IN ($this->dead_stat)");
        $update_only_list = array_intersect($data['service_code'],$current_list);
		$add_only_list = array_diff($data['service_code'],$current_list);
		$update_status_only_list = array_intersect($current_deleted_list,$add_only_list);
		$update_deleted2pending_status_only_list = array_intersect($data['service_code'],$current_deleted_list);
		$update_status_only_list = array_unique(array_merge($update_status_only_list,$update_deleted2pending_status_only_list));
		$add_only_list2 = array_diff($add_only_list,$update_status_only_list);
		$delete_only_list = array_diff($current_list,$data['service_code']);
        
		$with_monitor_array = array();
		for ($i=0; $i<sizeof ($arrayMonitorItems);$i++){
			$is_monitor_id = $arrayMonitorItems[$i][0];
			$with_monitor_array[] = $is_monitor_id;
		}

		# Add service codes that are not yet in the 'seg_lab_servdetails' table
		if (is_array($add_only_list) && !empty($add_only_list)){

			$temp_data = $data;
			$temp_serv_code = array();
			$temp_pnet = array();
			$temp_pcash = array();
			$temp_pcharge = array();
			$temp_request_doctor = array();
			$temp_request_dept = array();
			$temp_is_in_house = array();
			$temp_clinical_info = array();
			$temp_status = array();
			$temp_quantity = array();

			foreach ($add_only_list as $key => $value){
				$orig_key = array_search($value, $data['service_code']);
				array_push($temp_serv_code,$value);
				array_push($temp_pnet,$data['pnet'][$orig_key]);
				array_push($temp_pcash,$data['pcash'][$orig_key]);
				array_push($temp_pcharge,$data['pcharge'][$orig_key]);
				array_push($temp_request_doctor,$data['request_doctor'][$orig_key]);
				array_push($temp_request_dept,$data['request_dept'][$orig_key]);
				array_push($temp_is_in_house,$data['is_in_house'][$orig_key]);
				array_push($temp_clinical_info,$data['clinical_info'][$orig_key]);
				array_push($temp_quantity,$data['quantity'][$orig_key]);
			}

			$temp_data['service_code'] = $temp_serv_code;
			$temp_data['pnet'] = $temp_pnet;
			$temp_data['pcash'] = $temp_pcash;
			$temp_data['pcharge'] = $temp_pcharge;
			$temp_data['request_doctor'] = $temp_request_doctor;
			$temp_data['request_dept'] = $temp_request_dept;
			$temp_data['is_in_house'] = $temp_is_in_house;
			$temp_data['clinical_info'] = $temp_clinical_info;
			$temp_data['is_in_house'] = $temp_is_in_house;
			$temp_data['quantity'] = $temp_quantity;

			$temp_data['request_flag'] = $data['grant_type'];

			$this->saveLabRequestInfoFromArrayNEW($temp_data);
		}
       
		# Logical deletion [setting the status to 'delete'] for service codes that are to be deleted
		if (is_array($delete_only_list) && !empty($delete_only_list)){
			$arrayItems = array();
			foreach ($delete_only_list as $key => $value){
				$tempArray = array($value);
				array_push($arrayItems,$tempArray);
			}
            
			$this->updateLabRequestStatusByRefNoServCode($data, $arrayItems,'deleted');
		}# end of if-stmt 'if (is_array($delete_only_list))'
        
		# Change status from 'deleted' to 'pending' for service codes that are re-requested
		if (is_array($update_status_only_list) && !empty($update_status_only_list)){
			$arrayItems = array();
			foreach ($update_status_only_list as $key => $value){
				$orig_key = array_search($value, $data['service_code']);

				if (in_array($value,$with_monitor_array))
					$is_monitor = 1;
				else
					$is_monitor = 0;

				if (in_array($value,$arraySampleItems))
					$is_forward = 1;
				else
					$is_forward = 0;

				$tempArray = array($data['pnet'][$orig_key],
									 $data['pcash'][$orig_key],$data['pcharge'][$orig_key],
									 $data['request_doctor'][$orig_key],$data['request_dept'][$orig_key],
									 $data['is_in_house'][$orig_key],$data['clinical_info'][$orig_key],
									 $data['quantity'][$orig_key], $is_forward, $is_monitor,$value);

				array_push($arrayItems,$tempArray);
			}

			$this->updateLabRequestStatusByRefNoServCode($data, $arrayItems,'pending');
		}# end of if-stmt 'if (is_array($delete_only_list))'

		# Update service codes that have been modified and existing in the 'seg_lab_servdetails' table
		if (is_array($update_only_list) && !empty($update_only_list)){
			$arrayItems = array();

			#print_r($with_monitor_array);
			foreach ($update_only_list as $key => $value){
				$orig_key = array_search($value, $data['service_code']);

				if (in_array($value,$with_monitor_array))
					$is_monitor = 1;
				else
					$is_monitor = 0;

				if (in_array($value,$arraySampleItems))
					$is_forward = 1;
				else
					$is_forward = 0;

				$tempArray = array($data['pnet'][$orig_key],
									 $data['pcash'][$orig_key],$data['pcharge'][$orig_key],
									 $data['request_doctor'][$orig_key],$data['request_dept'][$orig_key],
									 $data['is_in_house'][$orig_key],$data['clinical_info'][$orig_key],
									 $data['quantity'][$orig_key], $is_forward, $is_monitor, $value);

				array_push($arrayItems,$tempArray);
			}

			$this->updateLabRequestStatusByRefNoServCode($data, $arrayItems);
		}

		$this->grantLabRequest($data);

        #update table seg_blood_hact for hact patient
        if ($data['ref_source']=='BB'){
        $row_hact = $this->getHactInfo($data['pid']);
        $request_time = $data['serv_dt']." ".$data['serv_tm']; 
        if ($row_hact['pid']){
            if ($is_hact)
               $status = 'hact'; 
            else   
               $status = 'normal'; 
            
            $this->updateHactPatient($data['pid'], $status, $request_time);
        }else{
            if ($is_hact){
                $this->addHactPatient($data['pid'], 'hact', $request_time);
            }
        }
            
            #update patient's blood type
            if ($blood_type){
                $details = (object) 'details';
                $details->pid = $pid;
                $details->blood_type = $blood_type;
                $request_time = date("Y-m-d H:i:s", strtotime($request_time));
                $details->create_tm = $request_time;        
                $this->updateBlood_Type($details);
            }
        }   
        #===============

		$this->useLabServ();
		if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
			else $concatfx='concat';

			#	Only the keys of data to be updated must be present in the passed array.
		$x='';
		$v='';
		$this->buffer_array = array();

		$this->data_array['status'] = ' ';
        $this->data_array['modify_id'] = $_SESSION['sess_temp_userid'];
        $this->data_array['history'] = $this->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_temp_userid']."\n");
        

		while(list($x,$v)=each($this->ref_array)) {
			if (isset($this->data_array[$v]))
				$this->buffer_array[$v]=trim($this->data_array[$v]);
		}
		$elems='';
		while(list($x,$v)=each($this->buffer_array)) {
			# use backquoting for mysql and no-quoting for other dbs.

			if ($dbtype=='mysql') $elems.="`$x`=";
				else $elems.="$x=";

			if(stristr($v,$concatfx)||stristr($v,'null')) $elems.=" $v,";
				else $elems.="'$v',";
		}
		# Bug fix. Reset array.
		reset($this->data_array);
		reset($this->buffer_array);
		$elems=substr_replace($elems,'',(strlen($elems))-1);
#echo "<br> updateLabRefNoInfoFromArray = ".$elems;
			$this->sql="UPDATE $this->coretable SET $elems, modify_dt=NOW() ".
						" WHERE refno='".$this->data_array['refno']."' ";
#echo "<br>class_labservices_transactions.php :  updateLabRefNoInfoFromArray : this->sql = '".$this->sql."' <br> \n";
#exit();
		return $this->Transact();
	}# end of function updateLabRefNoInfoFromArray

			/**
		* Updates the status of a laboratory request in table 'seg_lab_servdetails'.
		* @access public
		* @param int, refno
		* @param array, array of service code
		* @param string, new status
		* @return boolean
		* @created : burn, September 5, 2007
		*/

	function updateLabRequestStatusByRefNoServCode($data,$arrayItems, $new_status=''){
                #global $db,$HTTP_SESSION_VARS, $db_hclab, $dblink_hclab_ok;
                #$hclabObj = new HCLAB;			
                global $db,$HTTP_SESSION_VARS;

			if(!is_array($data) || (!$data))
				return FALSE;
			if(!is_array($arrayItems) || (!$arrayItems))
				return FALSE;

			$this->useLabServDetails();

			extract($data);

			$refno = $data['refno'];
			$this->data_array=$data;

			unset($this->data_array['create_id']);
			unset($this->data_array['create_dt']);
			unset($this->data_array['modify_dt']);
			unset($this->data_array['status']);
			unset($this->data_array['service_code']);
			unset($this->data_array['pnet']);
			unset($this->data_array['pcash']);
			unset($this->data_array['pcharge']);
			unset($this->data_array['request_doctor']);
			unset($this->data_array['request_dept']);
			unset($this->data_array['is_in_house']);
			unset($this->data_array['clinical_info']);
			unset($this->data_array['status']);
			unset($this->data_array['quantity']);

			unset($this->data_array['parent_batch_nr']);
			unset($this->data_array['parent_refno']);
			unset($this->data_array['approved_by_head']);
			unset($this->data_array['remarks']);

			unset($this->data_array['headID']);
			unset($this->data_array['headpasswd']);

			$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
     
		if (!empty($new_status)){
				# if the status needs to be change
			#$history = $this->ConcatHistory("Update status [$new_status] ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
			#$this->data_array['history'] = $history;
			#$history = $this->ConcatHistory("Update status [$new_status] ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
			$history = $this->ConcatHistory("Update request details-status [".$new_status."] at laboratory ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
			$this->data_array['history'] = $history;
			$this->data_array['status'] = $new_status;
		}else{
			#$this->data_array['history'] = $this->ConcatHistory("Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
            if ($this->data_array['for_manual_payment']){
                $workaround_cap = "-workaround";
            }    
            $history = $this->ConcatHistory("Update request details [".$this->data_array['request_flag']."] at laboratory $workaround_cap ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		
		}

		if (empty($new_status) || ($new_status=='pending')){
				# there is NO NEED for change of status
			if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
				else $concatfx='concat';

				#	Only the keys of data to be updated must be present in the passed array.
			$x='';
			$v='';
			$this->buffer_array = array();
			while(list($x,$v)=each($this->ref_array)) {
				if (isset($this->data_array[$v]))
					$this->buffer_array[$v]=trim($this->data_array[$v]);
			}
			$elems='';
			while(list($x,$v)=each($this->buffer_array)) {
				# use backquoting for mysql and no-quoting for other dbs.
				if ($dbtype=='mysql') $elems.="`$x`=";
					else $elems.="$x=";

				if(stristr($v,$concatfx)||stristr($v,'null')) $elems.=" $v,";
					else $elems.="'$v',";
			}
			# Bug fix. Reset array.
			reset($this->data_array);
			reset($this->buffer_array);
			$elems=substr_replace($elems,'',(strlen($elems))-1);
		}# end of if-stmt 'if (empty($new_status) || ($new_status=='pending'))'

		if (empty($new_status) || ($new_status=='pending')){
			$add_qry = '';
			$add_comma = ", ";

			if ($this->data_array['request_flag']){
				 $add_qry .= $add_comma." request_flag = '".$this->data_array['request_flag']."' ";
                $flag = $this->data_array['request_flag'];
                $history_cond = ', history='.$history;
            }else{
                $flag = "NULL";  
                $add_qry .= $add_comma." request_flag = NULL";
            }   
            
            #added by VAS 07-04-2012
            #add history for update     
            $history = $this->ConcatHistory("Update request details [".$flag."] at laboratory-[".$this->data_array['view_from']."] ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");

			$this->sql="UPDATE $this->tb_lab_servdetails ".
							" SET $elems, ".
							" 		price_cash=?, price_cash_orig=?, price_charge=?, ".
							" 		request_doctor=?, request_dept=? ,is_in_house=?, clinical_info=?, quantity=?,".
							"       is_forward=?, is_monitor=?  $history_cond".
										$add_qry.
							"       WHERE refno = '$refno' AND service_code = ?";
        
		}else{
            $history = $this->ConcatHistory("Delete request details at laboratory ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
			$this->sql="UPDATE $this->tb_lab_servdetails ".
							" SET status='".$new_status."', ".
                            " history = $history ".
							" WHERE refno = '$refno' AND service_code = ?";

		}

		#echo "<br>".$this->sql;
		if ($buf=$db->Execute($this->sql,$arrayItems)){
            
			#if ($with_sample){
			#$this->DoneRequest($refno,$arrayItemsList);
                
                #if ($new_status=='deleted')
                #added by VAS 03-22-2012
                #for the meantime, since applying coverage is just available in blood bank module
                #will apply the coverage when a sample was received, it means it was served by the hospital
                # Handle applied coverage for PHIC and other benefits
                $this->apply_coverage($refno, $arrayItemsList);
                
                $this->DoneRequest($refno,$arrayItemsList);
            #}    
                
			if ($with_monitor){
				$this->clearOrdersMonitorList($refno);
				$this->addOrdersMonitor($refno,$arrayMonitorItems);
			}else{
				$this->clearOrdersMonitorList($refno);
			}
			if($with_lis){
                #echo "<br> update = ".$HTTP_SESSION_VARS['connection_type'];
                
                $row_order = $this->getLabOrderNoLIMIT($refno);
                if ($row_order['lis_order_no']){
                    $data_HCLAB['POH_ORDER_NO'] = $row_order['lis_order_no'];
                }else{
                    $new_order_no = $this->getLastOrderNo();
                    $data_HCLAB['POH_ORDER_NO'] = $new_order_no;
                    $okHCLAB = $this->insert_Orderno_HCLAB($new_order_no, $refno);
                    $ok = $this->update_HCLabRefno_Tracker($new_order_no);
                }
                
                if ($HTTP_SESSION_VARS['connection_type']=='odbc'){
                    global $db_hclab, $dblink_hclab_ok;
                    $hclabObj = new HCLAB;            
				if ($dblink_hclab_ok){
					    #$new_order_no = $this->getLastOrderNo();
					    #$data_HCLAB['POH_ORDER_NO'] = $new_order_no;
					    
					#print_r($data_HCLAB);
					#header
					#echo "new = ".$new_order_no;
					$okHCLAB = $hclabObj->addOrderH_to_HCLAB($data_HCLAB);
					#details
					#set NULL
					#print_r($arrayLISItems);
					$deleted_test_array = array();
					$okHCLAB = $hclabObj->clearOrderList_to_HCLAB($refno, $deleted_test_array);
					$okHCLAB = $hclabObj->addOrders_to_HCLAB($refno, $arrayLISItems);
					#echo "<br>details = ".$hclabObj->sql;
					#if ($okHCLAB)
						    #$okHCLAB = $this->insert_Orderno_HCLAB($new_order_no, $refno);
					#if ($okHCLAB)
						    #$ok = $this->update_HCLabRefno_Tracker($new_order_no);
				}else{
					# can't connect to HCLAB
					echo '<em class="warn">Sorry, HCLAB connection failed..</em>';
				}	#if ($dblink_hclab_ok)

                }else{
                #check if the request has an existing order no
                    /*$row_order = $this->getLabOrderNoLIMIT($refno);
                if ($row_order['lis_order_no']){
                    $data_HCLAB['POH_ORDER_NO'] = $row_order['lis_order_no'];
                }else{
                    $new_order_no = $this->getLastOrderNo();
                    $data_HCLAB['POH_ORDER_NO'] = $new_order_no;
				    $okHCLAB = $this->insert_Orderno_HCLAB($new_order_no, $refno);
                    $ok = $this->update_HCLabRefno_Tracker($new_order_no);
                    } */ 
			}
            }

			#added by VAN 05-31-2011
			/*if ($with_sample_rec){
				$db->StartTrans();
				$bSuccess = $this->clearReceivedSample_h($refno);
				$bSuccess = $this->clearReceivedSample_d($refno);
				$bSuccess = $this->SaveReceivedSample_h($refno,$arraySampleItems_h);
				$bSuccess = $this->SaveReceivedSample_d($refno,$arraySampleItems_d);
                
				if (!$bSuccess) $db->FailTrans();
				$db->CompleteTrans();
			}*/
			#------------------------

			if($db->Affected_Rows()) {
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }

	}# end of function updateLabRequestStatusByRefNoServCode

	function getListedRequestsByRefNo($ref_nr='',$cond=''){
		global $db;

		if(empty($ref_nr) || (!$ref_nr)){
			return FALSE;
		}
		if(empty($cond) || (!$cond)){
			$cond = "AND status NOT IN ($this->dead_stat)";
		}

		$this->sql="SELECT service_code
						FROM seg_lab_servdetails
						WHERE refno='$ref_nr'
							$cond";

#echo "<br>class_labservices_transaction.php : getListedRequestsByRefNo: this->sql = '".$this->sql."' <br> \n";
#exit();
		if ($buf=$db->Execute($this->sql)){
			if($this->count=$buf->RecordCount()) {
				$arr = array();
				while($tmp = $buf->FetchRow()){
                    array_push($arr,$tmp['service_code']);
				}
				return $arr;
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getListedRequestsByRefNo

	function getRequestInfoByPrevRef($refno){
		global $db;

		$this->sql="SELECT * FROM seg_lab_serv
						WHERE parent_refno='$refno'";

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result->FetchRow();
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
		}
	#-------------------------

	#added by VAN 04-17-09  01:17AM
	function insert_Orderno_HCLAB($order_no, $refno){
		$this->sql = "INSERT INTO seg_lab_hclab_orderno(lis_order_no,refno) VALUES('".$order_no."','".$refno."')";
		return $this->Transact();
	}

	function getLastOrderNo(){
		global $db;

		$this->sql ="SELECT last_orderno FROM seg_lab_hclab_tracker LIMIT 1";
		#echo "<br>disp = ".$this->sql;

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount()){
				$row = $this->result->FetchRow();
				return  $row['last_orderno']+1;
			}else
				return FALSE;
		}else{
			return FALSE;
		}

	}

	function getOrderLastNr($order_init) {
		global $db;
		$this->sql="SELECT IFNULL(MAX(CAST(lis_order_no AS UNSIGNED)+1),$order_init) FROM seg_lab_hclab_orderno";
		return $db->GetOne($this->sql);
	}

	function isExistOrderNo($refno){
		global $db;

		$this->sql="SELECT * FROM seg_lab_hclab_orderno
						WHERE refno='$refno'";

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result->FetchRow();
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
		}

		function hasBloodRequest($encounter_nr){
				global $db;

				//$this->sql="SELECT r.*
//										FROM seg_lab_serv AS r
//										WHERE r.ref_source='BB'
//										AND r.encounter_nr='$encounter_nr'
//										AND (r.comments LIKE '%borrow%' OR r.comments LIKE '%BR%')
//										AND r.status NOT IN ($this->dead_stat)";
				#edited by VAN 07-13-2010
				$this->sql = "SELECT d.refno
											FROM seg_pharma_orders as o
											INNER JOIN seg_pharma_order_items AS d ON d.refno=o.refno
											INNER JOIN care_pharma_products_main AS m ON m.bestellnum=d.bestellnum
											WHERE o.encounter_nr='$encounter_nr' AND is_cash=0 AND d.serve_status='S' AND m.prod_class='M'
											AND m.artikelname like '%blood%' AND o.pharma_area='BB'
											AND (o.comments LIKE '%borrow%' OR o.comments LIKE '%BR%')
											/*UNION
											SELECT d.refno
											FROM seg_more_phorder as o
											INNER JOIN seg_more_phorder_details AS d ON d.refno=o.refno
											INNER JOIN care_pharma_products_main AS m ON m.bestellnum=d.bestellnum
											WHERE encounter_nr='$encounter_nr' and m.prod_class='M'
											AND m.artikelname LIKE '%blood%'*/
											UNION
											SELECT r.refno
											FROM seg_lab_serv AS r
											LEFT JOIN seg_blood_borrow_info AS b ON b.refno=r.refno
											WHERE r.ref_source='BB'
											AND r.encounter_nr='$encounter_nr'
											AND (r.comments LIKE '%borrow%' OR r.comments LIKE '%BR%' OR b.is_borrowed=1)
											AND r.status NOT IN ($this->dead_stat)";

				if ($this->result=$db->Execute($this->sql)) {
				if ($this->count=$this->result->RecordCount()){
						return $this->result->FetchRow();
				 }else{
								return FALSE;
				 }
				 }else{
						return FALSE;
				}
			}

		#added by VAN 06-02-09
		function clearGroupServiceList($service_code) {
				$this->sql = "DELETE FROM seg_lab_group WHERE service_code='$service_code'";
				#echo "<br>delete sql = ".$this->sql;
				return $this->Transact();
		}

		function addGroupService($service_code, $orderArray) {
				global $HTTP_SESSION_VARS;
				global $db;

				$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

				$this->sql = "INSERT INTO seg_lab_group(service_code,service_code_child,status,history,modify_id,modify_dt,create_id,create_dt)
																		VALUES('$service_code',?,'',CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW())";

				if($buf=$db->Execute($this->sql,$orderArray)) {
						if($buf->RecordCount()) {
								return true;
						} else { return false; }
				} else { return false; }

		}

		function get_LabServiceGroupPackage($service_code=''){
				global $db;

				$this->sql ="SELECT s.name AS parent_name, s.group_code AS parent_group, s.price_cash AS parent_cash, s.price_charge AS parent_charge,
														(SELECT name FROM seg_lab_services WHERE service_code=p.service_code_child) AS child_name,
														(SELECT group_code FROM seg_lab_services WHERE service_code=p.service_code_child) AS child_group,
														(SELECT price_cash FROM seg_lab_services WHERE service_code=p.service_code_child) AS child_cash,
														(SELECT price_charge FROM seg_lab_services WHERE service_code=p.service_code_child) AS child_charge,
														p.*
										 FROM seg_lab_group AS p
										 INNER JOIN seg_lab_services AS s ON s.service_code=p.service_code
										 WHERE p.service_code='$service_code'";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->count = $this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

		function get_OrderNo_by_Refno($refno=''){
				global $db;

				$this->sql ="SELECT * FROM seg_lab_serv where refno='$refno'";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->count = $this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

		function getDeptRequested($refno){
				global $db;

				$this->sql="SELECT * FROM seg_lab_servdetails WHERE refno='$refno'
										AND status NOT IN ($this->dead_stat) LIMIT 1";

				if ($this->result=$db->Execute($this->sql)) {
				if ($this->count=$this->result->RecordCount()){
						return $this->result->FetchRow();
				 }else{
								return FALSE;
				 }
				 }else{
						return FALSE;
				}
			}

	 #--------------------

	 #added by VAN 08-10-09

	 function getLabOrderNo($refno){
		global $db;

		$this->sql="SELECT lis_order_no FROM seg_lab_hclab_orderno WHERE refno='".$refno."'";

		if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			return FALSE;
		}
	}

    function getLabOrderNoLIMIT($refno){
        global $db;

        $this->sql="SELECT lis_order_no FROM seg_lab_hclab_orderno WHERE refno='".$refno."'
                    ORDER BY lis_order_no DESC LIMIT 1";

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        }
    }

    function isExistHL7Msg($refno){
        global $db;

        $this->sql="SELECT * FROM seg_hl7_lab_tracker WHERE refno='$refno' ORDER BY modify_date DESC LIMIT 1";

        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()){
            return $this->result->FetchRow();
         }else{
                return FALSE;
         }
        }else{
            return FALSE;
            }
    }
    
    function isExistHL7MsgLISOrder($refno, $lis_order_no){
        global $db;

        $this->sql="SELECT * FROM seg_hl7_lab_tracker 
                    WHERE refno='$refno' 
                    AND lis_order_no = '$lis_order_no'
                    ORDER BY modify_date DESC LIMIT 1";

        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()){
            return $this->result->FetchRow();
         }else{
                return FALSE;
         }
        }else{
            return FALSE;
            }
    }
    
    function isforReplaceHL7Msg($refno, $key){
        global $db;

        $this->sql="SELECT * FROM seg_hl7_lab_tracker WHERE refno='$refno' 
                    AND hl7_msg LIKE '%|".$key."|%'
                    ORDER BY modify_date LIMIT 1";

        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()){
            return $this->result->FetchRow();
         }else{
                return FALSE;
         }
        }else{
            return FALSE;
            }
    }
    
    function isforReplaceHL7MsgLISOrder($refno, $lis_order_no,$key){
        global $db;

        $this->sql="SELECT * FROM seg_hl7_lab_tracker WHERE refno='$refno' 
                    AND lis_order_no = '$lis_order_no'
                    AND hl7_msg LIKE '%|".$key."|%'
                    ORDER BY modify_date LIMIT 1";

        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()){
            return $this->result->FetchRow();
         }else{
                return FALSE;
         }
        }else{
            return FALSE;
            }
    }

	function getLabListOrderNo($refno){
		global $db;

		$this->sql="SELECT fn_get_lab_orderno(".$refno.") AS lis_order_no";

		if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			return FALSE;
		}
	}

	function getServiceCode($test_code, $mode){
		global $db;

		if ($mode){
			#INPATIENT CODE
			$this->sql = "SELECT * FROM seg_lab_services
							WHERE ipdservice_code='".$test_code."'
							AND status NOT IN ('deleted','hidden','inactive','void')";
		}else{
			#OUTPATIENT
			$this->sql = "SELECT * FROM seg_lab_services
							WHERE oservice_code='".$test_code."'
							AND status NOT IN ('deleted','hidden','inactive','void')";
		}
		if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result->FetchRow();
		} else{
			return FALSE;
		}
	}

	function getTestCode($test_code){
		global $db;

		$this->sql = "SELECT * FROM seg_lab_services
						WHERE service_code='".$test_code."' OR oservice_code='".$test_code."' OR ipdservice_code='".$test_code."'
						AND status NOT IN ('deleted','hidden','inactive','void')";

		if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result->FetchRow();
		} else{
			return FALSE;
		}
	}

	function hasResult2($order_no, $code='',$encounter_nr='',$pid='',$refno='') {

		global $db;

		if (empty($order_no))
			$order_no = 'NULL';
		#modified by cha, july 17, 2010
		if ($code)
			#$cond = "refno = '$refno' AND service_code = '$code'";
			$cond = "order_no IN (".$order_no.") AND service_code = '$code'";
		else
			#$cond = "refno = '$refno'";
			$cond = "order_no IN (".$order_no.")";

		#$this->sql="select * FROM seg_lab_results WHERE refno = '$refno'";
		$this->sql="SELECT SQL_CALC_FOUND_ROWS refno, 1 AS 'in_lis' FROM seg_lab_results WHERE $cond
								AND patient_caseNo='$encounter_nr' AND pid='$pid'
								UNION
								SELECT refno, 0 AS 'in_lis'
								FROM seg_lab_resultdata WHERE refno='$refno'";

		if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result->FetchRow();
		} else{
			return FALSE;
		}
	}

	#----------------------

	#added by VAN 10-02-09
	function isServiceAPackage($service_code){
				global $db;

				$this->sql="SELECT count(service_code_child) AS count_child
											FROM seg_lab_group AS lg
											WHERE service_code='".$service_code."'";

				if ($this->result=$db->Execute($this->sql)) {
					$row=$this->result->FetchRow();
					$this->count=$row['count_child'];
					return $this->count;
				} else{
					 return FALSE;
				}
	}

	function isTestAPackage($service_code){
				global $db;

				$this->sql="SELECT is_package FROM seg_lab_services WHERE service_code='".$service_code."'";

				if ($this->result=$db->Execute($this->sql)) {
					$row=$this->result->FetchRow();
					$this->count=$row['is_package'];
					return $this->count;
				} else{
					 return FALSE;
				}
	}

	function getAllServiceOfPackage($service_code){
				global $db;

				$this->sql="SELECT lg.service_code_child AS service_code,
										(SELECT name FROM seg_lab_services WHERE service_code=lg.service_code_child) AS name,
										(SELECT price_cash FROM seg_lab_services WHERE service_code=lg.service_code_child) AS cash,
										(SELECT price_charge FROM seg_lab_services WHERE service_code=lg.service_code_child) AS charge,
										(SELECT is_socialized FROM seg_lab_services WHERE service_code=lg.service_code_child) AS sservice,
										(SELECT group_code FROM seg_lab_services WHERE service_code=lg.service_code_child) AS group_code,
										(SELECT price FROM seg_service_discounts WHERE service_code=lg.service_code_child AND discountid='C1' AND service_area='LB') AS priceC1,
										(SELECT price FROM seg_service_discounts WHERE service_code=lg.service_code_child AND discountid='C2' AND service_area='LB') AS priceC2,
										(SELECT price FROM seg_service_discounts WHERE service_code=lg.service_code_child AND discountid='C3' AND service_area='LB') AS priceC3
										FROM seg_lab_group AS lg
										WHERE lg.service_code='".$service_code."'";

				if ($this->result=$db->Execute($this->sql)) {
					$this->count=$this->result->RecordCount();
					return $this->result;
				} else{
					 return FALSE;
				}
	}
	#----------------

	#added by VAN 01-20-10
	function updateCharityLabRequest($refno,$arrayItems){
			global $db, $HTTP_SESSION_VARS;

			//foreach ($svc_array as $i=>$v) {
//				$this->sql = "UPDATE seg_lab_servdetails SET request_flag='charity'
//											WHERE refno='".$refno."'
//											AND service_code=".$db->qstr($svc_array[$i][0]);
//				#echo "<br>s = ".$this->sql;
//				$saveok = $db->Execute($this->sql);
//			}
            
            #added by VAS 07-04-2012
            #add history for update
            $history = $this->ConcatHistory("Create request details [charity] at laboratory-social service ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
            
			$this->sql = "UPDATE seg_lab_servdetails SET request_flag='charity',
                                        history=$history    
										WHERE refno='".$refno."' AND service_code= ? ";

			if ($db->Execute($this->sql,$arrayItems)) {
				if ($db->Affected_Rows()) {
					return TRUE;
				}else{ return FALSE; }
			}else{ return FALSE; }
	}

	#added by VAN 01-26-10
	function getHCLABgroup(){
				global $db;

				$this->sql="SELECT * FROM seg_lab_service_groups WHERE in_lis=1";

				if ($this->result=$db->Execute($this->sql)) {
					$this->count=$this->result->RecordCount();
					return $this->result;
				} else{
					 return FALSE;
				}
	}

	function getNOTHCLABservices(){
				global $db;

				$this->sql="SELECT s.*
										FROM seg_lab_service_groups AS g
										INNER JOIN seg_lab_services AS s ON s.group_code=g.group_code
										WHERE g.in_lis=1 AND s.in_lis=0";

				if ($this->result=$db->Execute($this->sql)) {
					$this->count=$this->result->RecordCount();
					return $this->result;
				} else{
					 return FALSE;
				}
	}
	#-----------------------------------

	#added by VAN 04-22-2010
	function getLastRefno(){
		global $db;

		$this->sql ="SELECT last_refno FROM seg_lab_tracker LIMIT 1";
		#echo "<br>disp = ".$this->sql;

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount()){
				$row = $this->result->FetchRow();

				#edited by VAN 01-15-2010
				$substr_last_refno = substr($row['last_refno'],0,4);
				$current_year = date('Y');

				if ($substr_last_refno==$current_year){
				return  $row['last_refno']+1;
				}else{
					$refno = $this->getNewRefNo(date('Y')."000001");
					return  $refno;
				}
				#--------

			}else
				return FALSE;
		}else{
			return FALSE;
		}

	}

	function update_LabRefno_Tracker($refno){
		global $db;
        
		$this->sql = "UPDATE seg_lab_tracker SET last_refno='".$refno."'";
		#return $this->Transact();
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
	}

	function update_HCLabRefno_Tracker($orderno){
		global $db;
        
		$this->sql = "UPDATE seg_lab_hclab_tracker SET last_orderno='".$orderno."'";
		#return $this->Transact();
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
	}
	#--------------------------

function getAllLabServiceChargesByGroup(){
				global $db;

				$this->sql="SELECT g.group_code, g.name AS grp_name, s.* FROM seg_lab_service_groups AS g
										LEFT JOIN seg_lab_services AS s ON s.group_code = g.group_code AND (ISNULL(s.status) OR s.status<>'deleted')
										WHERE (ISNULL(g.status) OR g.status<>'deleted')
										ORDER BY g.name ASC, s.name ASC";

				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()){
								return $this->result;
						}
						else{
								 return FALSE;
						}
				 }
				 else{
					 return FALSE;
				}
		}

		#added by VAN 06-10-2010
		function SearchRequests($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE,$mod,$done=0, $is_doctor=0,$encounter_nr='', $source='LB', $count_sql=0, $group_code=FALSE, $isERIP=0){
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				$searchkey = str_replace("^","'",$searchkey);
				$suchwort=addslashes($searchkey);

				if(is_numeric($suchwort)) {
						$this->is_nr=TRUE;

						if(empty($oitem)) $oitem='refno';
						if(empty($odir)) $odir='DESC'; # default, latest pid at top

						$sql2="    WHERE r.status NOT IN ($this->dead_stat) AND ((r.pid = '$suchwort')) ";
				} else {
						# Try to detect if searchkey is composite of first name + last name
						if(stristr($searchkey,',')){
								$lastnamefirst=TRUE;
						}else{
								$lastnamefirst=FALSE;
						}

						#$searchkey=strtr($searchkey,',',' ');
						$cbuffer=explode(',',$searchkey);

						# Remove empty variables
						for($x=0;$x<sizeof($cbuffer);$x++){
								$cbuffer[$x]=trim($cbuffer[$x]);
								if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
						}

						# Arrange the values, ln= lastname, fn=first name, rd = request date
						if($lastnamefirst){
								$fn=$comp[1];
								$ln=$comp[0];
								$rd=$comp[2];
						}else{
								$fn=$comp[0];
								$ln=$comp[1];
								$rd=$comp[2];
						}
						# Check the size of the comp
						if(sizeof($comp)>1){
								#$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') ";
								$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";

								if(!empty($rd)){
										$DOB=@formatDate2STD($rd,$date_format);
										if($DOB=='') {
												#$sql2.=" AND serv_dt $sql_LIKE '$rd%' ";
										}else{
												$sql2.=" AND serv_dt = '$DOB' ";
												#$sql2.=" AND serv_dt LIKE '$DOB%' ";
										}
								}
								$sql2.=" AND r.status NOT IN ($this->dead_stat) ";
						}else{
								# Check if * or %
								if($suchwort=='%'||$suchwort=='%%'){
										#return all the data
										$sql2=" WHERE r.status NOT IN ($this->dead_stat) ";
								}elseif($suchwort=='now'){
										$sql2=" WHERE r.serv_dt=DATE(NOW()) AND r.status NOT IN ($this->dead_stat) ";
								}else{
										# Check if it is a complete DOB
										$DOB=@formatDate2STD($suchwort,$date_format);
										if($DOB=='') {
												if(TRUE){
														if($fname){
																#$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%') ";
																$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%') ";
														}else{
																$sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
														}
												}else{
														$sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
												}
										}else{
												$sql2=" WHERE serv_dt = '$DOB' ";
										}
										$sql2.=" AND r.status NOT IN ($this->dead_stat) ";
								}
						}
				 }

				$order_by = " ORDER BY r.serv_dt DESC, r.refno,d.service_code,s.in_lis ";
				if ($group_code){
					#$name_qry = " IF(s.in_lis, fn_get_labtest_request_grpcode(d.refno,'".$group_code."'), d.service_code) AS service_name, ";
					$name_qry = " IF(ISNULL(gp.group_id),gp.group_id,gp.group_id) AS group_id,
													IF(s.in_lis,'',IF(gp.group_id,'',s.service_code)) AS service_code,
													IF(s.in_lis, fn_get_labtest_request_grpcode(d.refno,'".$group_code."'), IF(ISNULL(gp.group_id),s.name,'')) AS service_name, ";

					$grp_cond = " AND s.group_code='$group_code' ";
				}else{
					#$name_qry = " IF(s.in_lis, fn_get_labtest_request_code(d.refno), d.service_code) AS service_name, ";
					$name_qry = " IF(ISNULL(gp.group_id),gp.group_id,gp.group_id) AS group_id,
													IF(s.in_lis,'',IF(gp.group_id,'',s.service_code)) AS service_code,
													IF(s.in_lis, fn_get_labtest_request_code(d.refno), IF(ISNULL(gp.group_id),s.name,'')) AS service_name, ";
					$grp_cond = "";
				}
				if ($done){
						$cond_done = " AND d.status='done' AND d.is_served=1 ";
						#$name_qry = " IF(s.in_lis, fn_get_labtest_request_code(d.refno), d.service_code) AS service_name, ";
						$order_by = " ORDER BY r.serv_dt DESC, r.serv_tm DESC, r.refno,d.service_code,s.in_lis ";
				}else{
						$cond_done = " AND d.status='pending' AND d.is_served=1 ";
				}

				$sql_erip = "";
				if ($isERIP){
				 $sql_erip = " AND (r.is_urgent=1 OR e.encounter_type=1 OR r.area_type='pw') ";
				}
                
                $this->sql = "SELECT DISTINCT SQL_CALC_FOUND_ROWS $name_qry
												ho.lis_order_no,
                                                IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
												r.pid, name_last, name_first, name_middle, date_birth,
												sex, e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type,
												r.is_cash, s.in_lis, r.refno,r.is_urgent,r.serv_dt,
												r.serv_tm,r.parent_refno,r.is_repeat, r.type_charge, d.request_flag AS charge_name,
												s.with_result, s.with_inventory, gp.group_id
											FROM seg_lab_serv AS r
											INNER JOIN care_person AS p ON p.pid=r.pid
											INNER JOIN seg_lab_servdetails AS d ON d.refno = r.refno
											LEFT JOIN seg_lab_hclab_orderno ho ON ho.refno=r.refno
                                            LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
											INNER JOIN seg_lab_services AS s ON (s.service_code = d.service_code)
											LEFT JOIN seg_lab_group AS g ON g.service_code_child = d.service_code
											LEFT JOIN seg_lab_result_group AS rg ON rg.service_code_child = d.service_code
											LEFT JOIN seg_lab_result_groupparams AS gp
												ON (gp.service_code = d.service_code OR gp.service_code=rg.service_code)
												AND gp.status <> 'deleted'

											$sql2
											AND (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0 )
											$sql_erip
											$cond_done
											$grp_cond
											AND ref_source = 'LB'
											$order_by";

			 #echo $this->sql;
			 #COUNTSEARCH SELECT
				if ($count_sql){
						if ($this->result=$db->Execute($this->sql)) {
								if ($this->count=$this->result->RecordCount()) {
										return $this->result;
								}
								else{return FALSE;}
						}else{return FALSE;}
				}else{
						#SEARCH SELECT
						if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
						}else{return false;}
				}

		}

		//added by Raissa 04-03-09
		function SearchReqResults($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE,$mod,$done=0, $is_doctor=0,$encounter_nr='', $source=0, $count_sql=0){
				global $db;
				$this->sql = "SELECT DISTINCT '' AS group_id, s.service_code, s.name as service_name, rd.is_confidential,
												d.date_served AS service_date, d.is_served, ch.charge_name,d.status AS test_status,
													 AS name_last,
													 name_first,
													 name_middle,
													 date_birth,
													 IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
													 pid,
													 AS sex,  e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type,
												r.* FROM seg_lab_serv AS r
													 LEFT JOIN care_person AS p ON p.pid=r.pid
													 LEFT JOIN seg_walkin AS w ON w.pid=r.walkin_pid
													 INNER JOIN seg_lab_servdetails AS d ON d.refno = r.refno
													 LEFT JOIN seg_lab_result_group AS g ON g.service_code_child = d.service_code
													 LEFT JOIN seg_type_charge as ch ON ch.id=r.type_charge
													 LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
													 INNER JOIN seg_lab_services AS s ON s.service_code = d.service_code
													 LEFT JOIN seg_lab_resultdata AS rd ON rd.refno = d.refno AND rd.service_code = s.service_code
													WHERE r.status NOT IN ($this->dead_stat)  AND (EXISTS(SELECT DISTINCT gr.ref_no, gr.service_code
															 FROM seg_granted_request AS gr WHERE gr.ref_source = 'LD' AND r.refno=gr.ref_no AND d.service_code=gr.service_code)
																OR EXISTS(SELECT DISTINCT pr.ref_no, pr.service_code AS test_code FROM seg_pay_request AS pr
																					 INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
																WHERE pr.ref_source = 'LD' AND r.refno=pr.ref_no AND d.service_code=pr.service_code
																				AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00'))
													 OR EXISTS(SELECT DISTINCT d.refno, d.service_code FROM seg_lab_servdetails AS d
																WHERE (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0)))
													 AND r.encounter_nr='$encounter_nr' AND fromBB = 0 AND (ISNULL(rd.is_confidential) OR rd.is_confidential=0)
						AND (s.service_code NOT IN (SELECT DISTINCT service_code FROM seg_lab_result_groupparams))
UNION SELECT DISTINCT gp.group_id, '' AS service_code, gn.name AS service_name, rd.is_confidential,
												d.date_served AS service_date, d.is_served, ch.charge_name,d.status AS test_status,
													 name_last,
													 name_first,
													 name_middle,
													 date_birth,
													 IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
													 pid,
													 sex,  e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type,
												r.*
													 FROM seg_lab_serv AS r
													 LEFT JOIN care_person AS p ON p.pid=r.pid
													 LEFT JOIN seg_walkin AS w ON w.pid=r.walkin_pid
													 INNER JOIN seg_lab_servdetails AS d ON d.refno = r.refno
													 LEFT JOIN seg_lab_result_groupparams AS gp ON gp.service_code = d.service_code
													 LEFT JOIN seg_lab_result_group AS g ON g.service_code_child = d.service_code
													 LEFT JOIN seg_type_charge as ch ON ch.id=r.type_charge
													 LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
													 LEFT JOIN seg_lab_resultdata AS rd ON rd.refno = d.refno AND rd.group_id= gp.group_id
													 INNER JOIN seg_lab_services AS s ON s.service_code = d.service_code
INNER JOIN seg_lab_result_groupname AS gn ON gn.group_id = gp.group_id
													 WHERE r.status NOT IN ($this->dead_stat)  AND (EXISTS(SELECT DISTINCT gr.ref_no, gr.service_code
															 FROM seg_granted_request AS gr WHERE gr.ref_source = 'LD' AND r.refno=gr.ref_no AND d.service_code=gr.service_code)
																OR EXISTS(SELECT DISTINCT pr.ref_no, pr.service_code AS test_code FROM seg_pay_request AS pr
																					 INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
																WHERE pr.ref_source = 'LD' AND r.refno=pr.ref_no AND d.service_code=pr.service_code
																				AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00'))
													 OR EXISTS(SELECT DISTINCT d.refno, d.service_code FROM seg_lab_servdetails AS d
																WHERE (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0)))
													 AND r.encounter_nr='$encounter_nr' AND fromBB = 0 AND (ISNULL(rd.is_confidential) OR rd.is_confidential=0)
						AND NOT (ISNULL(gp.group_id) OR gp.group_id=0)
													 ORDER BY group_id ASC,is_urgent DESC, refno DESC, serv_dt ASC";
				#echo $this->sql;
				#COUNTSEARCH SELECT
				if ($count_sql){
						if ($this->result=$db->Execute($this->sql)) {
								if ($this->count=$this->result->RecordCount()) {
										return $this->result;
								}
								else{return FALSE;}
						}else{return FALSE;}
				}else{
						#SEARCH SELECT
						if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
						}else{return false;}
				}
		}
		#------------------

	#added by VAN 06-11-2010
	function getAllReagentsInPharmaMain($keyword){
		global $db;
		 $this->sql="SELECT p.*
					FROM care_pharma_products_main AS p
					WHERE p.artikelname like '%".$keyword."%'
					AND p.prod_class='LS'
					AND p.status NOT IN ($this->dead_stat)";

			if ($this->result=$db->Execute($this->sql)) {
				if ($this->count = $this->result->RecordCount()) {
						return $this->result;
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}

	#added by VAN 01-10-10
	#this will update the quantity serve by the staff and store the old quantity requested in 'old_qty_request'
	function ServedLabRequest2($qty_approved,$old_qty, $refno, $group_id, $is_served, $date_served, $service_code='',$status='pending'){
		global $db, $HTTP_SESSION_VARS;
		$ret=FALSE;

		if($service_code){
						$this->sql="UPDATE seg_lab_servdetails SET
												quantity ='".$qty_approved."',
												old_qty_request ='".$old_qty."',
												is_served='".$is_served."',
												date_served='".$date_served."',
												status='".$status."',
												clerk_served_by='".$HTTP_SESSION_VARS['sess_user_name']."',
												clerk_served_date=NOW()
												WHERE refno = '".$refno."'
												AND service_code ='$service_code'";
		}else{
				$this->sql="UPDATE seg_lab_servdetails SET
						quantity ='".$qty_approved."',
						old_qty_request ='".$old_qty."',
						is_served='".$is_served."',
						date_served='".$date_served."',
						status='".$status."',
						clerk_served_by='".$HTTP_SESSION_VARS['sess_user_name']."',
						clerk_served_date=NOW()
						WHERE refno = '".$refno."'
						AND service_code IN (SELECT gp.service_code FROM seg_lab_result_groupparams AS gp WHERE gp.group_id='".$group_id."' UNION SELECT service_code_child FROM seg_lab_result_group AS g WHERE g.service_code IN (SELECT gp.service_code FROM seg_lab_result_groupparams AS gp WHERE group_id='".$group_id."'))";
		}
				#echo $this->sql;

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;

	}

	function updateMonitorFlag($refno, $monitorArray) {
		global $db;
		$refno = $db->qstr($refno);

		$this->sql = "UPDATE seg_lab_servdetails
										SET is_monitor=?
										WHERE service_code=? AND refno=$refno";
		#print_r($monitorArray);
		#echo "ss = ".$this->sql;
		if($buf=$db->Execute($this->sql,$monitorArray)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}

	function clearOrdersMonitorList($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "DELETE FROM seg_lab_serv_monitor WHERE refno=$refno";
		return $this->Transact();
	}

	function addOrdersMonitor($refno, $orderArray) {
		global $db;
		$refno = $db->qstr($refno);
		$monitorArray = array();
		$this->sql = "INSERT INTO seg_lab_serv_monitor(refno,service_code,every_hour) VALUES ";

		$i=0;
		for ($i=0; $i<sizeof ($orderArray);$i++){
				if ($i > 0) $this->sql .= ",";
				$this->sql .= "(".$refno.", '".$orderArray[$i][0]."', '".$orderArray[$i][1]."')";
				#$monitorArray[] = array(1,$orderArray[$i][0]);
		}
		#$this->updateMonitorFlag($refno,$monitorArray);
		return $this->Transact();
		#echo "<br>".$this->sql;
 }

	function getAllRequestByPid($pid,$encounter_nr,$ref_source){
			 global $db;

			 $this->sql="SELECT CONCAT(serv_dt,' ',serv_tm) AS serv_dt, encounter_nr, s.name AS request_item,
													fn_get_personell_name(request_doctor) AS request_doc,
													IFNULL(fn_get_encoder_name(r.create_id),r.create_id) AS encoder,
													IF(d.is_served,'DONE','UNDONE') AS status, is_cash, r.refno
										FROM seg_lab_serv AS r
										INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno
										INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code
										WHERE r.pid='$pid' AND r.encounter_nr='$encounter_nr'
										AND ref_source='$ref_source'
										AND d.status NOT IN ($this->dead_stat)
										AND r.status NOT IN ($this->dead_stat)
										ORDER BY CONCAT(serv_dt,' ',serv_tm) DESC, encounter_nr DESC, s.name";

			 if ($this->result=$db->Execute($this->sql)) {
					if ($this->count=$this->result->RecordCount()){
						return $this->result;
					}else{
						return FALSE;
					}
			 }else{
					return FALSE;
			 }
	 }

	#------------------------

	#added by CHA 07-15-2010----------
	function getParametersByService($service_code)
	{
		global $db;
		$this->sql = "SELECT p.param_id FROM seg_lab_result_params p \n".
						"LEFT JOIN seg_lab_result_param_assignment pa ON p.param_id=pa.param_id \n".
						"WHERE p.status <> 'deleted' AND pa.service_code=".$db->qstr($service_code);
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		}else {
			$this->error_msg = $db->ErrorMsg();
			$db->FailTrans();
			return FALSE;
		}

	}
	#end------------------------------

	#added by Raissa 02-04-09
	#revised by CHA 07-13-2010
	function DeleteParameter($param_id, $service_code) {
			global $db;
			$db->StartTrans();

			$saveok = $this->deleteParamAssignment($param_id, $service_code);
			if($saveok) {
				$this->sql = "UPDATE seg_lab_result_params SET STATUS = 'deleted' WHERE service_code = '$service_code' AND param_id = '$param_id'";

				$is_active = $db->GetOne($this->sql);
				if(!$is_active) {
					$this->sql = "SELECT history FROM seg_lab_result_params WHERE param_id='".$param_id."'";
					if ($this->result=$db->Execute($this->sql)) {
							if ($this->count=$this->result->RecordCount()) {
									if($rs = $this->result->FetchRow())
											$history = $rs["history"];
							}
					}
					$history .= "\nDELETED: ".date('Y-m-d H:i:s');
					$modify_dt = date('Y-m-d H:i:s');
					$modify_id = $_SESSION['sess_temp_userid'];

					$this->sql = "UPDATE seg_lab_result_params SET status='deleted', history='".$history."', modify_dt='".$modify_dt."', modify_id='".$modify_id."' WHERE param_id='".$param_id."'";
					if ($this->result=$db->Execute($this->sql)) {
						if (!$db->Affected_Rows()) {
							$this->error_msg = $db->ErrorMsg();
							$db->FailTrans();
							return FALSE;
						}
					}else {
						$this->error_msg = $db->ErrorMsg();
						$db->FailTrans();
						return FALSE;
					}
				}
				$db->CompleteTrans();
				return TRUE;
			}else {
				$this->error_msg = $db->ErrorMsg();
				$db->FailTrans();
				return FALSE;
			}

			/*$this->sql = "SELECT history FROM seg_lab_result_params WHERE param_id='".$param_id."'";
			if ($this->result=$db->Execute($this->sql)) {
					if ($this->count=$this->result->RecordCount()) {
							if($rs = $this->result->FetchRow())
									$history = $rs["history"];
					}
			}
			$history .= "\nDELETED: ".date('Y-m-d H:i:s');
			$modify_dt = date('Y-m-d H:i:s');
			$modify_id = $_SESSION['sess_temp_userid'];

			$this->sql = "UPDATE seg_lab_result_params SET status='deleted', history='".$history."', modify_dt='".$modify_dt."', modify_id='".$modify_id."' WHERE param_id='".$param_id."'";
			#-----------------
			if ($this->result=$db->Execute($this->sql)) {
					if ($db->Affected_Rows()>0) {
							$saveok = $this->deleteParamAssignment($param_id, $service_code);
							if($saveok) {
								$db->CompleteTrans();
								return TRUE;
							}else {
								$this->error_msg = $db->ErrorMsg();
								$db->FailTrans();
								return FALSE;
							}
					}
					else {
						$this->error_msg = $db->ErrorMsg();
						$db->FailTrans();
						return FALSE;
					}
			}else {
				$this->error_msg = $db->ErrorMsg();
				$db->FailTrans();
				return FALSE;
			}*/
	}

	#added by RAISSA 02-04-09
	#revised by CHA 07-13-2010
	#modified by EJ 09-20-2014
	function GetParameterData($param_id,$service_code) {
			global $db;

			//$this->sql = "SELECT * FROM seg_lab_result_params WHERE param_id='".$param_id."' AND (ISNULL(status) OR status<>'deleted')";
			$this->sql = "SELECT p.*,pa.order_nr FROM seg_lab_result_params AS p \n".
				"LEFT JOIN seg_lab_result_param_assignment AS pa ON p.param_id=pa.param_id \n".
				"WHERE p.param_id='".$param_id."' AND (ISNULL(p.status) OR p.status<>'deleted')AND pa.service_code ='".$service_code."'";
			#-----------------
			if ($this->result=$db->Execute($this->sql)) {
					if ($this->count=$this->result->RecordCount()) {
							return $this->result->FetchRow();
					}
					else{return FALSE;}
			}else{return FALSE;}
	}

	#added by Raissa 02-05-09
	#revised by CHA 07-13-2010
	function UpdateParameter($param_id, $order_nr, $service_code, $data) {
			global $db;
			$db->StartTrans();

			$this->sql = "SELECT history FROM seg_lab_result_params WHERE param_id='".$param_id."'";
			if ($this->result=$db->Execute($this->sql)) {
					if ($this->count=$this->result->RecordCount()) {
							if($rs = $this->result->FetchRow())
									$history = $rs["history"];
					}
			}
			$history .= "\nUPDATED: ".date('Y-m-d H:i:s');
			$modify_dt = date('Y-m-d H:i:s');
			$modify_id = $_SESSION['sess_temp_userid'];

			/*$this->sql = "UPDATE seg_lab_result_params SET
										name = '".$data["name"]."', is_numeric = ".$data["is_numeric"].", is_boolean = ".$data["is_boolean"].", is_longtext = ".$data["is_longtext"].",
										order_nr = '".$data["order_nr"]."', SI_unit = '".$data["SI_unit"]."', SI_lo_normal = '".$data["SI_lo_normal"]."', SI_hi_normal = '".$data["SI_hi_normal"]."',
										CU_unit = '".$data["CU_unit"]."', CU_lo_normal = '".$data["CU_lo_normal"]."', CU_hi_normal = '".$data["CU_hi_normal"]."',
										is_male = ".$data["is_male"].", is_female = ".$data["is_female"].",
										history = '".$history."', modify_id = '".$modify_id."', modify_dt = '".$modify_dt."',
										param_group_id = '".$data["param_group_id"]."', group_id = '".$data["group_id"]."'
										WHERE param_id='".$param_id."'"; */
			$this->sql = "UPDATE seg_lab_result_params SET
										name = '".$data["name"]."', is_numeric = ".$data["is_numeric"].", is_boolean = ".$data["is_boolean"].", is_longtext = ".$data["is_longtext"].",
										SI_unit = '".$data["SI_unit"]."', SI_lo_normal = '".$data["SI_lo_normal"]."', SI_hi_normal = '".$data["SI_hi_normal"]."',
										CU_unit = '".$data["CU_unit"]."', CU_lo_normal = '".$data["CU_lo_normal"]."', CU_hi_normal = '".$data["CU_hi_normal"]."',
										norm_type = '".$data["norm_type"]."',
										history = '".$history."', modify_id = '".$modify_id."', modify_dt = '".$modify_dt."',
										param_group_id = '".$data["param_group_id"]."', group_id = '".$data["group_id"]."'
										WHERE param_id='".$param_id."'";
			#echo $this->sql;
			#-----------------
				
			if ($this->result=$db->Execute($this->sql)) {
					if ($db->Affected_Rows()>0) {
							$saveok = $this->updateParamAssignment($param_id, $service_code, $order_nr);
							if($saveok) {
								$db->CompleteTrans();
								return TRUE;
							}else {
								$this->error_msg = $db->ErrorMsg();
								$db->FailTrans();
								return FALSE;
							}
					}
					else {
						$this->error_msg = $db->ErrorMsg();
						$db->FailTrans();
						return FALSE;
					}
			}else {
				$this->error_msg = $db->ErrorMsg();
				$db->FailTrans();
				return FALSE;
			}
	}

	#added by Raissa 02-05-09
	#revised by CHA 07-13-2010
	function AddParameter($service_code, $order_nr, $data) {
			global $db;
			$db->StartTrans();
			$history .= "CREATED: ".date('Y-m-d H:i:s');
			$modify_dt = date('Y-m-d H:i:s');
			$modify_id = $_SESSION['sess_temp_userid'];
			$create_dt = date('Y-m-d H:i:s');
			$create_id = $_SESSION['sess_temp_userid'];

			/*$this->sql = "INSERT INTO seg_lab_result_params (service_code, name, is_numeric, history, modify_id, modify_dt, create_id, create_dt, is_boolean, is_longtext, order_nr,
										SI_unit, SI_lo_normal, SI_hi_normal, CU_unit, CU_lo_normal, CU_hi_normal, is_male, is_female, param_group_id, group_id)
										VALUES ('".$service_code."', '".$data["name"]."', ".$data["is_numeric"].", '".$history."', '".$modify_id."', '".$modify_dt."', '".$create_id."', '".$create_dt."', ".$data["is_boolean"].", ".$data["is_longtext"].", ".$data["order_nr"].",
										'".$data["SI_unit"]."', '".$data["SI_lo_normal"]."', '".$data["SI_hi_normal"]."', '".$data["CU_unit"]."', '".$data["CU_lo_normal"]."', '".$data["CU_hi_normal"]."', ".$data["is_male"].", ".$data["is_female"].
										", '".$data["param_group_id"]."', '".$data["group_id"]."')";*/
			$this->sql = "INSERT INTO seg_lab_result_params (service_code,name, is_numeric, history, modify_id, modify_dt, create_id, create_dt, is_boolean, is_longtext,
										SI_unit, SI_lo_normal, SI_hi_normal, CU_unit, CU_lo_normal, CU_hi_normal, norm_type, param_group_id, group_id)
										VALUES (".$db->qstr($service_code).", ".$db->qstr($data["name"]).", ".$data["is_numeric"].", '".$history."', '".$modify_id."', '".$modify_dt."', '".$create_id."', '".$create_dt."', ".$data["is_boolean"].", ".$data["is_longtext"].",
										'".$data["SI_unit"]."', '".$data["SI_lo_normal"]."', '".$data["SI_hi_normal"]."', '".$data["CU_unit"]."', '".$data["CU_lo_normal"]."', '".$data["CU_hi_normal"]."', ".$db->qstr($data["norm_type"]).
										", '".$data["param_group_id"]."', '".$data["group_id"]."')";

			if ($this->result=$db->Execute($this->sql)) {
					if ($db->Affected_Rows()>0) {
							$param_id =$db->Insert_ID();
							$saveok = $this->addParamAssignment($param_id, $service_code, $order_nr);
							if($saveok) {
								$db->CompleteTrans();
								return TRUE;
							}else {
								$this->error_msg = $db->ErrorMsg();
								$db->FailTrans();
								return FALSE;
							}
					}
					else {
						$this->error_msg = $db->ErrorMsg();
						$db->FailTrans();
						return FALSE;
					}
			}else {
				$this->error_msg = $db->ErrorMsg();
				$db->FailTrans();
				return FALSE;
			}
	}
	#-------------------------

	#added by CHA, July 13, 2010--------------------------
	function addParamAssignment($param_id, $service_id, $order_no, $is_copied=0)
	{
		global $db;
		$modify_id = $_SESSION['sess_temp_userid'];
		$create_id = $_SESSION['sess_temp_userid'];
		$this->sql = "INSERT INTO seg_lab_result_param_assignment (param_id, service_code, order_nr, create_id, create_date, \n".
					"modify_id, modify_date, is_copied) VALUES ('".$param_id."', '".$service_id."', '".$order_no."', \n".
					"'".$create_id."', NOW(), '".$modify_id."', NOW(), '".$is_copied."')";
		if ($this->result=$db->Execute($this->sql)) {
					if ($db->Affected_Rows()>0) {
						return TRUE;
					}else {
						$this->error_msg = $db->ErrorMsg();
						return FALSE;
					}
		}else {
			$this->error_msg = $db->ErrorMsg();
			return FALSE;
		}
	}

	function updateParamAssignment($param_id, $service_id, $order_no)
	{
		global $db;
		$modify_id = $_SESSION['sess_temp_userid'];
		$this->sql = "UPDATE seg_lab_result_param_assignment SET order_nr=".$db->qstr($order_no).
					", modify_id=".$db->qstr($modify_id).", modify_date=NOW() WHERE param_id=".$db->qstr($param_id).
					" AND service_code=".$db->qstr($service_id);
		if ($this->result=$db->Execute($this->sql)) {
					if ($db->Affected_Rows()>0) {
						return TRUE;
					}else {
						$this->error_msg = $db->ErrorMsg();
						return FALSE;
					}
		}else {
			$this->error_msg = $db->ErrorMsg();
			return FALSE;
		}
	}

	/*added by mai 09082014*/
	function updateParamGroupId($param_id, $service_code){
		global $db;
		$this->sql = "UPDATE seg_lab_result_params SET group_id=0 WHERE ";

		if($param_id){
			$this->sql.= " param_id=".$db->qstr($param_id)." AND ";
		}
		
		$this->sql .= " service_code=".$db->qstr($service_code);

		if($this->result = $db->Execute($this->sql)){
			return TRUE;
		}else{
			$this->error_msg = $db->ErrorMsg();
			return FALSE;
		}
	}
	/*end added by mai*/

	function deleteParamAssignment($param_id, $service_id)
	{
		global $db;

		/*$this->sql = "UPDATE seg_lab_result_param_assignment SET status='deleted', \n".
					"modify_id=".$db->qstr($modify_id).", modify_date=NOW() \n".
					"WHERE param_id=".$db->qstr($param_id)." AND service_code=".$db->qstr($service_id); */
		$this->sql = "DELETE FROM seg_lab_result_param_assignment WHERE ";
		if($param_id)
			$this->sql.="param_id=".$db->qstr($param_id)." AND ";

		$this->sql.=" service_code=".$db->qstr($service_id);
		if ($this->result=$db->Execute($this->sql)) {
					/*if ($db->Affected_Rows()>0) {
						return TRUE;
					}else {
						$this->error_msg = $db->ErrorMsg();
						return FALSE;
					}*/
					return TRUE;
		}else {
			$this->error_msg = $db->ErrorMsg();
			return FALSE;
		}
	}
	#end--------------------------------------------------

	#added by cha, june 30, 2010--------------------------
	function addParamGroup($param_grp_name)
	{
		global $db;

		$this->sql = "INSERT INTO seg_lab_result_paramgroups (name, create_id, create_time, modify_id, modify_time) VALUES ".
							"(".$db->qstr($param_grp_name).", ".$db->qstr($_SESSION['sess_temp_userid']).", NOW(), ".
							$db->qstr($_SESSION['sess_temp_userid']).", NOW())";
		if($this->result=$db->Execute($this->sql)){
			if($db->Affected_Rows()>0){
				return true;
			}
		}else{ $this->error_msg = $db->ErrorMsg(); return FALSE;}
	}

	function deleteParamGroup($param_grp_id)
	{
		global $db;

		$this->sql = "UPDATE seg_lab_result_paramgroups SET status='deleted', ".
								"modify_id='".$_SESSION['sess_temp_userid']."', modify_time=NOW() ".
								"WHERE param_group_id=".$db->qstr($param_grp_id);
		if($this->result=$db->Execute($this->sql)){
			if($db->Affected_Rows()>0){
				return true;
			}
		}else{ $this->error_msg = $db->ErrorMsg(); return FALSE;}
	}

	function updateParamGroup($id, $name)
	{
		global $db;

		$this->sql = "UPDATE seg_lab_result_paramgroups SET name=".$db->qstr($name).
								", modify_id='".$_SESSION['sess_temp_userid']."', modify_time=NOW() ".
								"WHERE param_group_id=".$db->qstr($id);
		if($this->result=$db->Execute($this->sql)){
			if($db->Affected_Rows()>0){
				return true;
			}
		}else{ $this->error_msg = $db->ErrorMsg(); return FALSE;}
	}

	function getParamGroups()
	{
		global $db;

		$this->sql = "SELECT SQL_CALC_FOUND_ROWS param_group_id, name FROM seg_lab_result_paramgroups WHERE status <> 'deleted' OR STATUS IS NULL ORDER BY name ASC";
		if ($this->result=$db->Execute($this->sql)) {
					if ($this->count=$this->result->RecordCount()) {
							return $this->result;
					}
					else{return FALSE;}
			}else{return FALSE;}
	}

	#added by genz
	function getGroups()
	{
		global $db;

		$this->sql = "SELECT group_id, name FROM seg_lab_result_groupname ORDER BY name ASC";
		if ($this->result=$db->Execute($this->sql)) {
					if ($this->count=$this->result->RecordCount()) {
							return $this->result;
					}
					else{return FALSE;}
			}else{return FALSE;}
	}
	#end cha-----------------------------------------------

	function countSearchRequests($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE,$mod,$done=0, $is_doctor=0, $encounter_nr='', $source=0, $group_code=FALSE) {
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				$searchkey = str_replace("^","'",$searchkey);
				$suchwort=addslashes($searchkey);

				if(is_numeric($suchwort)) {
						$this->is_nr=TRUE;

						if(empty($oitem)) $oitem='refno';
						if(empty($odir)) $odir='DESC'; # default, latest pid at top

						$sql2="    WHERE r.status NOT IN ($this->dead_stat) AND ((r.pid = '$suchwort')) ";
				} else {
						# Try to detect if searchkey is composite of first name + last name
						if(stristr($searchkey,',')){
								$lastnamefirst=TRUE;
						}else{
								$lastnamefirst=FALSE;
						}

						#$searchkey=strtr($searchkey,',',' ');
						$cbuffer=explode(',',$searchkey);

						# Remove empty variables
						for($x=0;$x<sizeof($cbuffer);$x++){
								$cbuffer[$x]=trim($cbuffer[$x]);
								if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
						}

						# Arrange the values, ln= lastname, fn=first name, rd = request date
						if($lastnamefirst){
								$fn=$comp[1];
								$ln=$comp[0];
								$rd=$comp[2];
						}else{
								$fn=$comp[0];
								$ln=$comp[1];
								$rd=$comp[2];
						}
						# Check the size of the comp
						if(sizeof($comp)>1){
								#$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') ";
								$sql2=" WHERE ((p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')
															OR (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%'
															OR (w.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND w.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')
															OR (w.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%')))";

								if(!empty($rd)){
										$DOB=@formatDate2STD($rd,$date_format);
										if($DOB=='') {
												#$sql2.=" AND serv_dt $sql_LIKE '$rd%' ";
										}else{
												$sql2.=" AND serv_dt = '$DOB' ";
												#$sql2.=" AND serv_dt LIKE '$DOB%' ";
										}
								}
								$sql2.=" AND r.status NOT IN ($this->dead_stat) ";
						}else{
								# Check if * or %
								if($suchwort=='%'||$suchwort=='%%'){
										#return all the data
										$sql2=" WHERE r.status NOT IN ($this->dead_stat) ";
								}elseif($suchwort=='now'){
										$sql2=" WHERE r.serv_dt=DATE(NOW()) AND r.status NOT IN ($this->dead_stat) ";
								}else{
										# Check if it is a complete DOB
										$DOB=@formatDate2STD($suchwort,$date_format);
										if($DOB=='') {
												if(TRUE){
														if($fname){
																$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR w.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' /*OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR p.name_middle $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR r.serv_dt LIKE '%".strtr($suchwort,'+',' ')."%'*/) ";
														}else{
																$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR w.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%') ";
														}
												}else{
														$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%'  OR w.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%')";
												}
										}else{
												$sql2=" WHERE serv_dt = '$DOB' ";
										}
										$sql2.=" AND r.status NOT IN ($this->dead_stat) ";
								}
						}
				 }

				if ($done){
						$wres = "";
						$trx_date = "d.date_served AS service_date,";

						$wresB = "LEFT JOIN seg_lab_result AS rs ON r.refno = rs.refno AND rs.service_code = d.service_code AND rs.status NOT IN ('deleted','hidden','inactive','void')";
						$trx_dateB = "d.date_served AS service_date,";
				}else{
						$wres = "";
						$trx_date = "";
				}
				#-------------------------
					 $sql2A = " AS r
										 LEFT JOIN care_person AS p ON p.pid=r.pid
										 LEFT JOIN seg_walkin AS w ON w.pid=r.walkin_pid
										 INNER JOIN seg_lab_servdetails AS d ON d.refno = r.refno
										 LEFT JOIN seg_lab_result_groupparams AS gp ON gp.service_code = d.service_code
										 LEFT JOIN seg_lab_group AS g ON g.service_code_child = d.service_code
										 LEFT JOIN seg_lab_result_group AS rg ON rg.service_code_child = d.service_code
										 LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
										 LEFT JOIN seg_type_of_charge as ch ON ch.id=r.type_charge
										 ".$wres."
										 INNER JOIN seg_lab_services AS s ON s.service_code = d.service_code ".$sql2;

				$sql2B = " AS r
										 LEFT JOIN care_person AS p ON p.pid=r.pid
										 LEFT JOIN seg_walkin AS w ON w.pid=r.walkin_pid
										 INNER JOIN seg_lab_servdetails AS d ON d.refno = r.refno
										 LEFT JOIN seg_lab_result_groupparams AS gp ON gp.service_code = d.service_code
										 LEFT JOIN seg_lab_group AS g ON g.service_code_child = d.service_code
										 LEFT JOIN seg_lab_result_group AS rg ON rg.service_code_child = d.service_code
										 LEFT JOIN seg_type_of_charge as ch ON ch.id=r.type_charge
										 LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
										 ".$wresB."
										 INNER JOIN seg_lab_services AS s ON s.service_code = d.service_code ".$sql2;

				$this->buffer=$this->tb_lab_serv.$sql2A;
				$this->bufferB=$this->tb_lab_serv.$sql2B;

				#if ($source)
				#		$sql_source = " AND fromBB = 1 ";
				#else
				#		$sql_source = " AND fromBB = 0 ";
				$sql_source .= " AND ref_source = '".$source."'";

						if($group_code && $group_code!='0')
								$sql_source = " AND group_code = '$group_code' ";
						/*
						if(isset($oitem)&&!empty($oitem)) $sql3 =" AND (EXISTS(SELECT DISTINCT gr.ref_no, gr.service_code FROM seg_granted_request AS gr
																																		WHERE gr.ref_source = 'LD' AND r.refno=gr.ref_no)
																																		OR EXISTS(SELECT DISTINCT pr.ref_no, pr.service_code FROM seg_pay_request AS pr
																																		WHERE pr.ref_source = 'LD' AND r.refno=pr.ref_no))
																																		ORDER BY is_urgent DESC,r.serv_dt ASC, refno ASC ";
						*/
						#added by VAN 07-02-08
						if ($done){
								$wores = "";
								$served = " AND d.is_served=1 /*AND is_forward=1*/ AND d.status<>'sent-out' ";
						}else{
								#$wores = " AND NOT EXISTS(SELECT rs.* FROM seg_lab_result AS rs WHERE rs.refno = r.refno)";
								#$served = " AND d.is_served=0 ";
								$served = " AND d.is_served=0 /*AND is_forward=1*/ AND d.status<>'sent-out'";
						}
						#-----------------

						if ($is_doctor)
								$is_dr_con = " AND r.encounter_nr='".$encounter_nr."' ";
						else
								$is_dr_con = "";

						if(isset($oitem)&&!empty($oitem)) $sql3 = " AND ( EXISTS(SELECT DISTINCT gr.ref_no, gr.service_code
																																		FROM seg_granted_request AS gr
																																											 WHERE gr.ref_source = 'LD' AND r.refno=gr.ref_no AND d.service_code=gr.service_code)
																																					OR EXISTS(SELECT DISTINCT pr.ref_no, pr.service_code AS test_code
																																											FROM seg_pay_request AS pr
																																											INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
																																										WHERE pr.ref_source = 'LD' AND r.refno=pr.ref_no
																																										AND d.service_code=pr.service_code
																																										AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00'))
																																						OR EXISTS(SELECT DISTINCT d.refno, d.service_code
																																											 FROM seg_lab_servdetails AS d
																																										WHERE (is_urgent = 1 OR is_cash=0 OR is_tpl=1 OR type_charge<>0))
																																						)
																																						$served
																																						$is_dr_con
																																						$sql_source
																																		 ";

					$this->sql= " SELECT $trx_date gp.group_id, s.service_code, s.name AS service_name, r.refno,r.is_urgent,r.serv_dt,r.parent_refno, r.type_charge, ch.charge_name,
														IF(p.name_last IS NULL,w.name_last,p.name_last) AS name_last,
												IF(p.name_first IS NULL,w.name_first,p.name_first) AS name_first,
												IF(p.name_middle IS NULL,w.name_middle,p.name_middle) AS name_middle,
												IF(p.date_birth IS NULL,w.date_birth,p.date_birth) AS date_birth,
												IF (IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),p.age),IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),p.age) , IF(fn_calculate_age(NOW(),w.date_birth),fn_get_age(NOW(),w.date_birth),w.age)) AS age,
												IF(p.pid IS NULL,w.pid,p.pid) AS pid,
												IF(p.sex IS NULL,w.sex,p.sex) AS sex, ".
														" e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type ".
													 " FROM ".$this->buffer.$sql3.
													 " AND ISNULL(gp.group_id) AND ISNULL(g.service_code) AND ISNULL(rg.service_code)".
												" UNION SELECT DISTINCT $trx_date IF(ISNULL(gp.group_id),(SELECT group_id FROM seg_lab_result_groupparams WHERE service_code=rg.service_code LIMIT 1),gp.group_id) as group_id, '' AS service_code, '' AS service_name, r.refno,r.is_urgent,r.serv_dt,r.parent_refno, r.type_charge, ch.charge_name,
														IF(p.name_last IS NULL,w.name_last,p.name_last) AS name_last,
												IF(p.name_first IS NULL,w.name_first,p.name_first) AS name_first,
												IF(p.name_middle IS NULL,w.name_middle,p.name_middle) AS name_middle,
												IF(p.date_birth IS NULL,w.date_birth,p.date_birth) AS date_birth,
												IF (IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),p.age),IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),p.age) , IF(fn_calculate_age(NOW(),w.date_birth),fn_get_age(NOW(),w.date_birth),w.age)) AS age,
												IF(p.pid IS NULL,w.pid,p.pid) AS pid,
												IF(p.sex IS NULL,w.sex,p.sex) AS sex, ".
														" e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type ".
														" FROM ".$this->buffer.$sql3.
													 " AND (NOT ISNULL(gp.group_id) OR (ISNULL(gp.group_id) AND (NOT ISNULL(rg.service_code)))) ".
													 " ORDER BY group_id ASC, is_urgent DESC,refno DESC,serv_dt ASC";

				#echo "sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		function getLabRequestByEnc($searchkey,$maxcount=100,$offset=0,$encounter_nr, $pid, $ref_source='LB', $count_sql){
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				#$suchwort=$searchkey;
				$searchkey = str_replace("^","'",$searchkey);
				$suchwort=addslashes($searchkey);

				$where_cond = "";
				if (($suchwort)&& ($suchwort!='%' && $suchwort!='%%')){
					$suchwort = date('Y-m-d',strtotime($suchwort));
					$where_cond = " AND r.serv_dt='$suchwort' ";
				}

					/*$this->sql = "SELECT SQL_CALC_FOUND_ROWS r.refno,
													fn_get_labtest_request_code_all(d.refno) AS services,
													r.serv_dt, r.serv_tm, r.is_urgent,
													d.date_served, d.request_flag
													FROM seg_lab_serv AS r
													INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno
													WHERE r.status NOT IN ('deleted','hidden','inactive','void')
													AND d.status NOT IN ('deleted','hidden','inactive','void')
													AND (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0)
													AND ref_source = '$ref_source'
													AND r.encounter_nr='$encounter_nr'
													AND r.pid='$pid'
													$where_cond
													GROUP BY r.refno
													ORDER BY is_urgent DESC,refno DESC,r.serv_dt DESC";*/
                                                    
              //edited by VAN 02-06-2013
              //changed the query
              $this->sql = "SELECT SQL_CALC_FOUND_ROWS \n". 
                           "sr.nth_take, sr.service_code, \n".
                           "fn_get_labtest_request_all(r.refno) AS services, \n".
                           "r.refno, r.encounter_nr, r.pid, \n".
                           "tr.lis_order_no, r.serv_dt, r.serv_tm, rs.filename,rs.date_received \n".
                           "FROM seg_lab_serv r \n".
                           "INNER JOIN seg_lab_servdetails d ON d.refno=r.refno \n".
                           "LEFT JOIN seg_hl7_lab_tracker tr ON tr.refno=r.refno \n".
                           "LEFT JOIN seg_hl7_pdffile_received rs ON rs.filename=CONCAT(r.pid,'_',tr.lis_order_no,'.pdf') \n".
                           "LEFT JOIN seg_lab_serv_serial sr ON sr.refno=r.refno AND sr.lis_order_no=tr.lis_order_no \n".
                           "WHERE r.status NOT IN ('deleted','hidden','inactive','void') \n".
                           "AND d.status NOT IN ('deleted','hidden','inactive','void') \n".
                           "AND (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0) \n".
                           "AND ref_source = ".$db->qstr($ref_source)." \n".
                           "AND r.encounter_nr= ".$db->qstr($encounter_nr)." \n".
                           "AND r.pid= ".$db->qstr($pid)." \n".
                           #"AND d.is_served=1 \n".
                           " $where_cond \n".
                           "GROUP BY r.refno, lis_order_no \n".
                           "ORDER BY r.refno DESC";
                                    

				#COUNTSEARCH SELECT
				if ($count_sql){
						if ($this->result=$db->Execute($this->sql)) {
								if ($this->count=$this->result->RecordCount()) {
										return $this->result;
								}
								else{return FALSE;}
						}else{return FALSE;}
				}else{
						#SEARCH SELECT
						if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
						}else{return false;}
				}
		}

		#added by VAN 07-09-2010
		function getAllSegHisLabResult($refno){
				global $db;

				$this->sql = "SELECT h.refno, g.name, h.service_code,s.oservice_code,s.ipdservice_code,
												h.service_code AS test_code ,h.service_code AS parent_item, pg.name AS test_name,
												d.param_id,p.name AS test_name, d.result_value, d.unit, h.service_date,
												fn_get_person_name(h.pathologist_pid) AS pathologist, fn_get_person_name(h.med_tech_pid) AS mlt_name, h.is_confidential,
												IF (((SI_lo_normal!='')&&(SI_hi_normal!='')),CONCAT(SI_lo_normal,' - ',SI_hi_normal),'') AS ranges
											FROM seg_lab_resultdata AS h
											INNER JOIN seg_lab_result AS d ON d.refno=h.refno
											INNER JOIN seg_lab_result_params AS p ON p.param_id=d.param_id AND p.group_id=h.group_id
											INNER JOIN seg_lab_result_paramgroups AS pg ON pg.param_group_id=p.param_group_id
											INNER JOIN seg_lab_services AS s ON s.service_code=h.service_code
											INNER JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code
											WHERE h.refno='$refno'
											AND d.status NOT IN ('deleted','hidden','inactive','void')
											AND h.status NOT IN ('deleted','hidden','inactive','void')
											/*AND d.result_value!=''*/
											ORDER BY p.group_id,p.order_nr";

				if ($this->result=$db->Execute($this->sql)) {
					$this->count=$this->result->RecordCount();
					return $this->result;
				} else{
					return FALSE;
				}
		}

		function getAllSegHisLabResultGroup($refno){
				global $db;

				$this->sql = "SELECT DISTINCT ls.service_code, rd.group_id, g.name AS section_name,rd.service_code  AS test_code, rd.service_code AS parent_item, r.param_id, p.name,
										pg.name AS test_gr_name,p.param_group_id, r.result_value, r.unit
										FROM seg_lab_resultdata AS rd
										LEFT JOIN seg_lab_result AS r ON r.refno=rd.refno AND r.status NOT IN ('deleted','hidden','inactive','void')
										LEFT JOIN seg_lab_result_param_assignment AS pa ON pa.param_id=r.param_id
										LEFT JOIN seg_lab_result_params AS p ON p.param_id=pa.param_id
										LEFT JOIN seg_lab_result_paramgroups AS pg ON pg.param_group_id=p.param_group_id
										LEFT JOIN seg_lab_result_groupparams AS gp ON p.group_id=gp.group_id
										LEFT JOIN seg_lab_services AS s ON s.service_code=pa.service_code
										LEFT JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code
										inner JOIN seg_lab_servdetails AS ls ON ls.service_code=pa.service_code and ls.service_code=rd.service_code

										WHERE ls.refno='$refno' AND r.result_value!='' and ls.service_code=pa.service_code
										AND rd.status NOT IN ('deleted','hidden','inactive','void')
										group by pa.order_nr
										ORDER BY rd.service_code, r.param_id,p.group_id,gp.order_nr";

				if ($this->result=$db->Execute($this->sql)) {
					$this->count=$this->result->RecordCount();
					return $this->result;
				} else{
					return FALSE;
				}
		}

		function getAllSegHisLabResultParam($refno, $param_group_id, $group_id){
				global $db;

				$this->sql = "SELECT h.refno, h.service_code,
												h.service_code AS test_code ,h.service_code AS parent_item,
												d.param_id,p.name AS test_name,d.result_value, d.unit, h.service_date,
												fn_get_person_name(h.pathologist_pid) AS pathologist, fn_get_person_name(h.med_tech_pid) AS mlt_name, h.is_confidential,
												IF (((SI_lo_normal!='')&&(SI_hi_normal!='')),CONCAT(SI_lo_normal,' - ',SI_hi_normal),'') AS ranges
											FROM seg_lab_resultdata AS h
											INNER JOIN seg_lab_result AS d ON d.refno=h.refno
											INNER JOIN seg_lab_result_params AS p ON p.param_id=d.param_id
											WHERE h.refno='$refno' AND h.group_id='$group_id' AND p.param_group_id='$param_group_id'
											AND d.result_value!=''
											AND d.status NOT IN ('deleted','hidden','inactive','void')
											AND h.status NOT IN ('deleted','hidden','inactive','void')";

				if ($this->result=$db->Execute($this->sql)) {
					$this->count=$this->result->RecordCount();
					return $this->result;
				} else{
					return FALSE;
				}
		}

		function get_LabServiceGroupPackage_Result($service_code=''){
				global $db;

				$this->sql ="SELECT s.name AS parent_name, s.group_code AS parent_group, s.price_cash AS parent_cash, s.price_charge AS parent_charge,
														(SELECT name FROM seg_lab_services WHERE service_code=p.service_code_child) AS child_name,
														(SELECT group_code FROM seg_lab_services WHERE service_code=p.service_code_child) AS child_group,
														p.*
										 FROM seg_lab_result_group AS p
										 INNER JOIN seg_lab_services AS s ON s.service_code=p.service_code
										 WHERE p.service_code='$service_code'";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->count = $this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

		function clearGroupServiceList_res($service_code) {
				$this->sql = "DELETE FROM seg_lab_result_group WHERE service_code='$service_code'";
				#echo "<br>delete sql = ".$this->sql;
				return $this->Transact();
		}

		function addGroupService_res($service_code, $orderArray) {
				global $HTTP_SESSION_VARS;
				global $db;

				$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

				$this->sql = "INSERT INTO seg_lab_result_group(service_code,service_code_child,status,
																		history,modify_id,modify_dt,create_id,create_dt)
																		VALUES('$service_code',?,'',CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',
																		NOW(),'$userid',NOW())";

				if($buf=$db->Execute($this->sql,$orderArray)) {
						if($buf->RecordCount()) {
								return true;
						} else { return false; }
				} else { return false; }

		}
		#--------------------------------

		#added by VAN 08-04-2010
		function DoneRequest($refno, $arrayItems){
		    global $db, $HTTP_SESSION_VARS;
		    $ret=FALSE;
           
            $this->sql="UPDATE seg_lab_servdetails SET
									status = ?,
									is_served= ?,
									date_served= ?,
									clerk_served_by= ?,
									clerk_served_date= ?
									WHERE service_code = ?
									AND refno = '".$refno."'";
		#print_r($arrayItems);
		#echo "<br><br>".$this->sql;
		if ($buf=$db->Execute($this->sql,$arrayItems)){
			if($db->Affected_Rows()) {
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }

	}

	#added by VAN 03-30-2011
	#update if request is already served
	function SetWithForwardedSample($refno, $service_code, $date_served, $is_served=0) {
		global $db, $HTTP_SESSION_VARS;

		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

		# with no result yet
		$status = 'pending';
		if (!$is_served){
			$userid = '';
			$status = 'pending';
		}

		$this->sql = "UPDATE seg_lab_servdetails SET
											status = '".$status."',
											is_forward='".$is_served."',
											is_served='".$is_served."',
											date_served='".$date_served."',
											clerk_served_by= '".$userid."',
											clerk_served_date= '".$date_served."'
									WHERE refno='".$refno."'
									AND service_code='".$service_code."'";

		$result=$db->Execute($this->sql);
		if($result){
			return true;
		}else return false;
	}

	function SetWithForwardedSampleAll($refno, $service_code_list ='', $date_served, $is_served=0) {
		global $db, $HTTP_SESSION_VARS;

		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

		# with no result yet
		$status = 'pending';
		if (!$is_served){
			$userid = '';
			$status = 'pending';
		}

		$this->sql = "UPDATE seg_lab_servdetails SET
											status = '".$status."',
											is_forward='".$is_served."',
											is_served='".$is_served."',
											date_served='".$date_served."',
											clerk_served_by= '".$userid."',
											clerk_served_date= '".$date_served."'
									WHERE refno='".$refno."'
									AND service_code IN (".$service_code_list.")";

		$result=$db->Execute($this->sql);
		if($result){
			return true;
		}else return false;
	}
	#--------------

	#added by VAN 08-19-2010
	function isIPinERLab($ip_address) {
			global $db;

			$this->sql = "SELECT ip_address FROM seg_lab_er_ip WHERE ip_address='".$ip_address."'";
			#-----------------
			if ($this->result=$db->Execute($this->sql)) {
					if ($this->count=$this->result->RecordCount()) {
							return TRUE;
					}
					else{return FALSE;}
			}else{return FALSE;}
	}

	#added by angelo m. 09.15.2010
	#used in Blood Bank request module
	#start
	function save_BorrowInfo($arr_data){
		global $db;

		$this->sql="INSERT INTO seg_blood_borrow_info
								(refno,
								is_borrowed,
								qty_borrowed,
								bb_remarks,
								partner_type,
								partner_name
								)
								VALUES
								('".$arr_data['refno']."',
								'".$arr_data['is_borrowed']."',
								'".$arr_data['qty_borrowed']."',
								'".$arr_data['bb_remarks']."',
								'".$arr_data['partner_type']."',
								'".$arr_data['partner_name']."'
								);";

		$result=$db->Execute($this->sql);
		if($result){
			return true;
		}else return false;
	}

	function update_BorrowInfo($arr_data){
		global $db;

		#added by VAN 09-17-2010
		if (!$arr_data['is_borrowed'])
			$arr_data['is_borrowed'] = 0;

		if (!$arr_data['qty_borrowed'])
			$arr_data['qty_borrowed'] = 0;

		if (!$arr_data['partner_type'])
			$arr_data['partner_type'] = 'NULL';
		else
			$arr_data['partner_type'] = "'".$arr_data['partner_type']."'";

		$this->sql="UPDATE seg_blood_borrow_info
											SET
											is_borrowed  = '".$arr_data['is_borrowed']."' ,
											qty_borrowed = '".$arr_data['qty_borrowed']."' ,
											bb_remarks 	 = '".$arr_data['bb_remarks']."' ,
											partner_type = ".$arr_data['partner_type']." ,
											partner_name = '".$arr_data['partner_name']."'
											WHERE
											refno = '".$arr_data['refno']."' ";

		$result=$db->Execute($this->sql);
		if($result){
			return true;
		}else return false;
	}

	function get_BorrowedInfo($refno){
		global $db;

		$this->sql="SELECT 	refno,
												is_borrowed,
												qty_borrowed,
												bb_remarks,
												partner_type,
												partner_name
											FROM
												seg_blood_borrow_info
												WHERE refno='".$refno."';";

		$result=$db->Execute($this->sql);
			if($result){
					return $result->FetchRow();
			}else return false;
	}
	#end

	#added by VAN 03-11-2011
	function SearchSelectTobeServed($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$source='LB', $tab='', $isERIP=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				#$suchwort=$searchkey;
				$searchkey = str_replace("^","'",$searchkey);
				$suchwort=addslashes($searchkey);

				if(is_numeric($suchwort)) {
						#$suchwort=(int) $suchwort;
						$this->is_nr=TRUE;

						if(empty($oitem)) $oitem='refno';
						if(empty($odir)) $odir='DESC'; # default, latest pid at top

						$sql2="    WHERE r.status NOT IN ($this->dead_stat) AND (r.pid = '$suchwort')";
				} else {
						# Try to detect if searchkey is composite of first name + last name
						if(stristr($searchkey,',')){
								$lastnamefirst=TRUE;
						}else{
								$lastnamefirst=FALSE;
						}

						#$searchkey=strtr($searchkey,',',' ');
						$cbuffer=explode(',',$searchkey);

						# Remove empty variables
						for($x=0;$x<sizeof($cbuffer);$x++){
								$cbuffer[$x]=trim($cbuffer[$x]);
								if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
						}

						# Arrange the values, ln= lastname, fn=first name, rd = request date
						if($lastnamefirst){
								$fn=$comp[1];
								$ln=$comp[0];
								$rd=$comp[2];
						}else{
								$fn=$comp[0];
								$ln=$comp[1];
								$rd=$comp[2];
						}
						# Check the size of the comp
						if(sizeof($comp)>1){
								#$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') ";
								$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";

								if(!empty($rd)){
										$DOB=@formatDate2STD($rd,$date_format);
										if($DOB=='') {
												#$sql2.=" AND serv_dt $sql_LIKE '$rd%' ";
										}else{
												if ($done)
													$sql2.=" AND DATE(serv_dt) = '$DOB' ";
												else
													$sql2.=" AND serv_dt = '$DOB' ";
												#$sql2.=" AND serv_dt LIKE '$DOB%' ";
										}
								}
								$sql2.=" AND r.status NOT IN ($this->dead_stat) ";
						}else{
								# Check if * or %
								if($suchwort=='%'||$suchwort=='%%'){
										#return all the data
										$sql2=" WHERE r.status NOT IN ($this->dead_stat) ";
								}elseif($suchwort=='now'){
										#$sql2=" WHERE r.serv_dt=now() AND r.status NOT IN ($this->dead_stat) ";
										if ($done)
												$sql2=" WHERE DATE(serv_dt)=DATE(NOW()) AND r.status NOT IN ($this->dead_stat) ";
										else
												$sql2=" WHERE r.serv_dt=DATE(NOW()) AND r.status NOT IN ($this->dead_stat) ";
								}else{
										# Check if it is a complete DOB
										$DOB=@formatDate2STD($suchwort,$date_format);
										if($DOB=='') {
												if(TRUE){
														if($fname){
																#$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%') ";
																$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%') ";
														}else{
																$sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
														}
												}else{
														$sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
												}
										}else{
												if ($done)
													$sql2=" WHERE DATE(serv_dt) = '$DOB' ";
												else
													$sql2=" WHERE serv_dt = '$DOB' ";
												#$sql2=" WHERE serv_dt LIKE '$DOB%' ";
										}
										$sql2.=" AND r.status NOT IN ($this->dead_stat) ";
								}
						}
				 }
			 $sql_erip = "";
			 if ($isERIP){
				 $sql_erip = " AND (r.is_urgent=1 OR e.encounter_type IN (1,3,4)) ";
			 }

			 # for All
			 if ($tab==0){
				 $cond1 = "";
			 # for served
			 }elseif ($tab==1){
				 $cond1 = " AND d.is_served=1 ";
			 # for not served
			 }elseif ($tab==2){
				 $cond1 = " AND d.is_served=0 ";
			 }

			 #if(isset($oitem)&&!empty($oitem)) $sql3 =" $sql_erip ORDER BY is_urgent DESC,refno DESC,r.serv_dt DESC ";
			 if(isset($oitem)&&!empty($oitem)) $sql3 =" $sql_erip ORDER BY r.serv_dt DESC, p.name_last, p.name_first, p.name_middle  ";

			 $this->sql= "SELECT DISTINCT SQL_CALC_FOUND_ROWS r.refno,p.name_last,p.name_first, p.name_middle, p.pid, p.sex,
														IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
														p.date_birth,r.encounter_nr, e.encounter_type,
														r.serv_dt,r.serv_tm, r.is_urgent, r.is_cash, r.is_tpl,
														e.current_ward_nr,e.current_room_nr,e.current_dept_nr,e.current_att_dr_nr
														FROM seg_lab_serv AS r
														INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno
														INNER JOIN care_person AS p ON p.pid=r.pid
														LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
														$sql2
														$cond1
														AND (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0 )
														AND r.status NOT IN ('deleted','hidden','inactive','void')
														AND d.status NOT IN ('deleted','hidden','inactive','void')
														$sql3
												 ";

				if ($this->result=$db->Execute($this->sql)) {
					if ($this->count=$this->result->RecordCount()) {
						return $this->result;
					}
						else{return FALSE;}
				} else{
					return FALSE;
				}

	}

	function getAllLabInfoDetailsByRefNo($ref_nr='', $ref_source='LB'){
		global $db;

		if(empty($ref_nr) || (!$ref_nr)){
			return FALSE;
		}

		$this->sql="SELECT
									r.is_forward,is_monitor, m.every_hour, m.no_takes, IF ((request_flag IS NOT NULL),1,0) AS hasPaid,request_flag,
									r_serv.refno, r_serv.serv_dt, r_serv.encounter_nr,
									r_serv.discountid, r_serv.discount,
									r_serv.pid, r_serv.ordername, r_serv.orderaddress,
									r_serv.is_cash, r_serv.is_urgent, r_serv.comments,
									r_serv.status, r_serv.history, r_serv.create_dt,
									r.refno, r.clinical_info, r.service_code,
									r.price_cash, r.price_cash_orig, r.price_charge,
									r.is_in_house, r.request_doctor,r.quantity,
									r_serv.parent_refno, r_serv.parent_refno, r_serv.approved_by_head, r_serv.remarks,
									r.is_forward, r.is_served, r.date_served, r.is_monitor,
									r.status AS request_status,
									IF((ISNULL(r.is_in_house) ||  r.is_in_house='0'),
										r.request_doctor,
										IF(STRCMP(r.request_doctor,CAST(r.request_doctor AS UNSIGNED INTEGER)),
										r.request_doctor,
										fn_get_personell_name(r.request_doctor))
									) AS request_doctor_name,
									r.request_dept,
									r_services.service_code, r_services.name, r_services.is_socialized,
									r_serv_group.group_code, r_services.in_lis, r_services.oservice_code, r_services.ipdservice_code,
									r.price_cash AS discounted_price, r_serv.is_cash, r.is_served, r.is_posted_lis, r_services.is_serial
									FROM seg_lab_serv AS r_serv
									LEFT JOIN seg_lab_servdetails AS r ON r.refno=r_serv.refno
									LEFT JOIN seg_lab_services AS r_services ON r.service_code = r_services.service_code
									LEFT JOIN seg_lab_service_groups AS r_serv_group ON r_serv_group.group_code = r_services.group_code
									LEFT JOIN seg_lab_serv_monitor AS m ON m.refno=r_serv.refno AND m.service_code=r.service_code
									WHERE r_serv.refno='$ref_nr'
										AND r_serv.ref_source = '$ref_source'
										AND r_serv.status NOT IN ($this->dead_stat)
										AND r.status NOT IN ($this->dead_stat)
										GROUP BY r.service_code
									ORDER BY create_dt ASC ";

		if ($buf=$db->Execute($this->sql)){
			if($buf->RecordCount()) {
				return $buf;
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getAllLabInfoDetailsByRefNo
	#-----------------------

	function getTestbyRefno($refno, $service_code){
				global $db;

				$this->sql="SELECT SQL_CALC_FOUND_ROWS d.*, s.*
										FROM seg_lab_servdetails AS d
										INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code
										INNER JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code
										WHERE d.refno='$refno' AND d.service_code='$service_code'
										AND d.status NOT IN ('deleted','hidden','inactive','void')";

				if ($this->result=$db->Execute($this->sql)) {
					$this->count=$this->result->RecordCount();
					return $this->result->FetchRow();
				} else{
					 return FALSE;
				}
	}

	#added by VAN 04-12-2011
	function getOrderHeader($refno){
				global $db;

				$this->sql="SELECT DISTINCT SQL_CALC_FOUND_ROWS
											h.refno AS POH_TRX_NUM,
											CONCAT(IF((TRIM(serv_dt)!='0000-00-00') OR TRIM(serv_dt) IS NULL,
																	TRIM(serv_dt),''), ' ',
														 IF((TRIM(serv_tm)!='00:00:00') OR TRIM(serv_tm) IS NULL,
																	TRIM(serv_tm),'')) AS POH_TRX_DT,
											o.lis_order_no AS POH_ORDER_NO,
											NOW() AS POH_ORDER_DT,
											(CASE WHEN (e.encounter_type = 1)
															 THEN 'ER'
														WHEN (e.encounter_type = 2)
															 THEN e.current_dept_nr
														WHEN (e.encounter_type = 3 OR e.encounter_type = 4)
															 THEN e.current_ward_nr
														WHEN (e.encounter_type = 5)
															 THEN 'RDU'
														WHEN (e.encounter_type = 6)
															 THEN 'IC'
														ELSE 'WIN'
											 END) AS POH_LOC_CODE,
											UPPER(CASE WHEN (e.encounter_type = 1)
															 THEN 'ER'
														WHEN (e.encounter_type = 2)
															 THEN fn_get_department_name(e.current_dept_nr)
														WHEN (e.encounter_type = 3 OR e.encounter_type = 4)
															 THEN fn_get_ward_name(e.current_ward_nr)
														WHEN (e.encounter_type = 5)
															 THEN 'RDU'
														WHEN (e.encounter_type = 6)
															 THEN 'IC'
														ELSE 'WIN'
											 END) AS POH_LOC_NAME,
											(CASE WHEN (e.encounter_type = 1)
															 THEN 'ER'
														WHEN (e.encounter_type = 2)
															 THEN 'OPD'
														WHEN (e.encounter_type = 3 OR e.encounter_type = 4)
															 THEN 'IPD'
														WHEN (e.encounter_type = 5)
															 THEN 'RDU'
														WHEN (e.encounter_type = 6)
															 THEN 'IC'
														ELSE 'WIN'
											 END) AS POH_LOC_CODE2,
											d.request_doctor AS POH_DR_CODE,
											(CASE WHEN (e.encounter_type = 1)
															 THEN 'ER Department'
														WHEN (e.encounter_type = 2)
															 THEN 'Outpatient DepartmentD'
														WHEN (e.encounter_type = 3 OR e.encounter_type = 4)
															 THEN 'Inpatient Department'
														WHEN (e.encounter_type = 5)
															 THEN 'RDU'
														WHEN (e.encounter_type = 6)
															 THEN 'Industrial Clinic'
														ELSE 'Walkin'
											 END) AS POH_LOC_NAME2,
											d.request_doctor AS POH_DR_CODE,
											fn_get_personell_name(d.request_doctor) AS POH_DR_NAME,
											h.pid AS POH_PAT_ID,
											CONCAT(IF(TRIM(p.name_last) IS NULL,'',TRIM(p.name_last)),', ',
														 IF(TRIM(p.name_first) IS NULL ,'',TRIM(p.name_first)),' ',
														 IF(TRIM(p.name_middle) IS NULL,'',TRIM(p.name_middle))) AS POH_PAT_NAME,
											(CASE WHEN (e.encounter_type = 1)
															 THEN 'ER'
														WHEN (e.encounter_type = 2)
															 THEN 'OP'
														WHEN (e.encounter_type = 3 OR e.encounter_type = 4)
															 THEN 'IN'
														WHEN (e.encounter_type = 5)
															 THEN 'RDU'
														WHEN (e.encounter_type = 6)
												 THEN 'IC'
														ELSE 'WN'
											 END) AS POH_PAT_TYPE,
											'' AS POH_PAT_ALTID,
											p.date_birth AS POH_PAT_DOB,
											UPPER(p.sex) AS POH_PAT_SEX,
											h.encounter_nr AS POH_PAT_CASENO,
											d.clinical_info AS POH_CLI_INFO,
											(CASE WHEN (h.is_urgent = 1)
												 THEN 'U' ELSE 'R' END)  AS POH_PRIORITY,
											(CASE WHEN (h.is_urgent = 1)
												 THEN 'S' ELSE 'R' END)  AS POH_PRIORITY2,
											h.orderaddress AS POH_ADDRESS,
											CONCAT(IF (TRIM(p.street_name) IS NULL,'',TRIM(p.street_name)),' ',
												IF (TRIM(sb.brgy_name) IS NULL,'',TRIM(sb.brgy_name)),' ',
												IF (TRIM(sm.mun_name) IS NULL,'',TRIM(sm.mun_name)),' ',
												IF (TRIM(sm.zipcode) IS NULL,'',TRIM(sm.zipcode)),' ',
												IF (TRIM(sp.prov_name) IS NULL,'',TRIM(sp.prov_name)),' ',
												IF (TRIM(sr.region_name) IS NULL,'',TRIM(sr.region_name))) AS POH_ADDRESS2,
											p.street_name AS POH_STREET,
											sb.brgy_name AS POH_BRGY,
											sm.mun_name AS POH_CITY,
											sm.zipcode AS POH_ZIPCODE,
											sp.prov_name AS POH_PROVINCE,
											sr.region_name AS POH_REGION,
											UPPER(p.civil_status) AS POH_CIVIL_STAT,
											p.name_last AS POH_LASTNAME,
											p.name_first AS POH_FIRSTNAME,
											p.name_middle AS POH_MIDDLENAME

											FROM seg_lab_serv AS h
											INNER JOIN seg_lab_servdetails AS d ON d.refno=h.refno
											INNER JOIN seg_lab_hclab_orderno AS o ON o.refno=h.refno
											INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code
											INNER JOIN care_person AS p ON p.pid=h.pid
											INNER JOIN care_encounter AS e ON e.encounter_nr=h.encounter_nr
											LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
											LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
											LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
											LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
											WHERE /*d.is_served = 1
											AND d.is_forward = 1
											AND s.in_lis = 1
											AND*/ h.refno='".$refno."'
											LIMIT 1";

				if ($this->result=$db->Execute($this->sql)) {
					$this->count=$this->result->RecordCount();
					return $this->result->FetchRow();
				} else{
					 return FALSE;
				}
	}

	function getLastMsgID(){
		global $db;

		$this->sql ="SELECT msg_id FROM seg_lab_hl7_msg_id LIMIT 1";

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 $row = $this->result->FetchRow();
				 return $row['msg_id'];
			} else{
				 return FALSE;
			}
	}

	function getRequestDetailsbyRefno($refno){
				global $db;

				$this->sql="SELECT SQL_CALC_FOUND_ROWS d.*, s.*
										FROM seg_lab_servdetails AS d
										INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code
										INNER JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code
										WHERE d.refno='$refno'
										AND d.is_served=1
										AND d.status NOT IN ('deleted','hidden','inactive','void')";

				if ($this->result=$db->Execute($this->sql)) {
					$this->count=$this->result->RecordCount();
					return $this->result;
				} else{
					 return FALSE;
				}
	}

    function getRequestDetailsbyRefnoLIS($refno){
                global $db;

                $this->sql="SELECT SQL_CALC_FOUND_ROWS d.*, s.*
                                        FROM seg_lab_servdetails AS d
                                        INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code
                                        INNER JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code
                                        WHERE d.refno='$refno'
                                        AND d.is_forward=1
                                        AND d.status NOT IN ('deleted','hidden','inactive','void')";

                if ($this->result=$db->Execute($this->sql)) {
                    $this->count=$this->result->RecordCount();
                    return $this->result;
                } else{
                     return FALSE;
                }
    }

	//added by VAN 05-31-2011
	function clearReceivedSample_h($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "DELETE FROM seg_blood_received_sample_h WHERE refno=$refno";
		return $this->Transact();
	}

	function SaveReceivedSample_h($refno, $sampleArray) {
		global $db, $HTTP_SESSION_VARS;
		$refno = $db->qstr($refno);
		$this->sql = "INSERT INTO seg_blood_received_sample_h(refno,receiver_id,received_date,status, history) VALUES ";

		$i=0;
        $history = $this->ConcatHistory("Created  as $status patient ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		for ($i=0; $i<sizeof ($sampleArray);$i++){
				if ($i > 0) $this->sql .= ",";
				$this->sql .= "(".$refno.", '".$sampleArray[$i][0]."', '".$sampleArray[$i][1]."', '".$sampleArray[$i][2]."',".$history.")";
		}
		#echo "<br><br>h = ".$this->sql;
		return $this->Transact();
	}

	function clearReceivedSample_d($refno) {
		global $db, $HTTP_SESSION_VARS;
		$refno = $db->qstr($refno);
		$this->sql = "DELETE FROM seg_blood_received_sample_d WHERE refno=$refno";
		return $this->Transact();
	}

	function SaveReceivedSample_d($refno, $sampleArray) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "INSERT INTO seg_blood_received_sample_d(refno,service_code,qty_ordered,qty_received) VALUES ";

		$i=0;
        $history = $this->ConcatHistory("Created  as $status patient ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		for ($i=0; $i<sizeof ($sampleArray);$i++){
				if ($i > 0) $this->sql .= ",";
				$this->sql .= "(".$refno.", '".$sampleArray[$i][0]."', '".$sampleArray[$i][1]."', '".$sampleArray[$i][2]."')";
		}

		#echo "<br><br>d = ".$this->sql;
		return $this->Transact();
	}
	//-------------------

    #added by VAN 12-29-2011
    function getTestRequest($refno){
                global $db;

                $this->sql="SELECT request_dept, request_doctor FROM seg_lab_servdetails 
                            WHERE refno='".$refno."'
                            AND STATUS NOT IN ('deleted','hidden','inactive','void') 
                            LIMIT 1";

                if ($this->result=$db->Execute($this->sql)) {
                    $this->count=$this->result->RecordCount();
                    return $this->result->FetchRow();
                } else{
                     return FALSE;
                }
    }
    
    #added by VAN 01-31-2012
    function getLastMsgControlID(){
        global $db;

        $this->sql ="SELECT msg_control_id FROM seg_hl7_msg_control_id WHERE dept='LB' LIMIT 1";

        if ($this->result=$db->Execute($this->sql)) {
                 $this->count=$this->result->RecordCount();
                 $row = $this->result->FetchRow();
                 return $row['msg_control_id']+1;
            } else{
                 return FALSE;
            }
    }
    
    function updateHL7_msg_control_id($new_msg_control_id){
        global $db;
        
        $this->sql = "UPDATE seg_hl7_msg_control_id SET msg_control_id='".$new_msg_control_id."' WHERE dept='LB'";
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
    }
    
    function getInfo_HL7_tracker($msg_control_id){
        global $db;

        $this->sql ="SELECT * FROM seg_hl7_lab_tracker WHERE msg_control_id='".$msg_control_id."'";

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    function addInfo_HL7_tracker($details){
        global $db;
        
        $index = " msg_control_id,lis_order_no,msg_type,event_id,refno,pid,encounter_nr,hl7_msg,create_date,modify_date";

        $values = "'".$details->msg_control_id."','".$details->lis_order_no."','".$details->msg_type."','".
                     $details->event_id."','".$details->refno."','".$details->pid."','".
                     $details->encounter_nr."','".$details->hl7_msg."',NOW(),NOW()";

        $this->sql = "INSERT INTO seg_hl7_lab_tracker ($index)
                            VALUES ($values)";
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
    }
    
    function updateInfo_HL7_tracker($details){
        global $db;
        
        $this->sql = "UPDATE seg_hl7_lab_tracker SET 
                            lis_order_no = '".$details->lis_order_no."',
                            msg_type = '".$details->msg_type."',
                            event_id = '".$details->event_id."',
                            refno = '".$details->refno."',
                            pid = '".$details->pid."',
                            encounter_nr = '".$details->encounter_nr."',
                            hl7_msg = '".$details->hl7_msg."',
                            modify_date = NOW() 
                      WHERE msg_control_id='".$details->msg_control_id."'";
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
    }
    
    function getInfo_HL7_result_receipt($msg_control_id){
        global $db;

        $this->sql ="SELECT * FROM seg_hl7_hclab_msg_receipt WHERE msg_control_id='".$msg_control_id."'";

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    function addInfo_HL7_result_receipt($details){
        global $db;
        
        $index = " msg_control_id,lis_order_no,msg_type,event_id,pid,hl7_msg,create_date,modify_date";

        $values = "'".$details->msg_control_id."','".$details->lis_order_no."','".$details->msg_type."','".
                      $details->event_id."','".$details->pid."','".$details->hl7_msg."',NOW(),NOW()";

        $this->sql = "INSERT INTO seg_hl7_hclab_msg_receipt ($index)
                            VALUES ($values)";
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
    }
     
    function updateInfo_HL7_result_receipt($details){
        global $db;
        
        $this->sql = "UPDATE seg_hl7_hclab_msg_receipt SET 
                            lis_order_no = '".$details->lis_order_no."',
                            msg_type = '".$details->msg_type."',
                            event_id = '".$details->event_id."',
                            pid = '".$details->pid."',
                            hl7_msg = '".$details->hl7_msg."',
                            modify_date = NOW() 
                      WHERE msg_control_id='".$details->msg_control_id."'";
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
    }
    
    function getAddress($brgy_nr, $mun_nr){
        global $db;

        $this->sql ="SELECT b.brgy_nr, b.brgy_name, b.mun_nr, m.mun_name, m.prov_nr, m.zipcode, p.prov_name 
                     FROM seg_barangays b
                     INNER JOIN seg_municity m ON m.mun_nr=b.mun_nr
                     INNER JOIN seg_provinces p ON p.prov_nr=m.prov_nr
                     WHERE b.brgy_nr='".$brgy_nr."' AND m.mun_nr='".$mun_nr."'";

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    function getPatientInfo($encounter_nr){
        global $db;

        $this->sql ="SELECT p.pid, e.encounter_nr, p.name_first, p.name_last, p.name_middle, p.sex, p.date_birth,
                        p.civil_status, p.street_name, p.brgy_nr, p.mun_nr,
                        e.encounter_date, e.admission_dt, 
                        IF(e.encounter_type=2, fn_get_department_name(e.current_dept_nr), IF(e.encounter_type=1, 'ER', 
                          IF(e.encounter_type=3 OR e.encounter_type=4,CONCAT(fn_get_ward_name(e.current_ward_nr),'^',e.current_room_nr,'^',l.location_nr),'WN'))) AS location,
                        fn_get_personell_firstname_last(e.current_att_dr_nr) AS requesting_doc,
                        IF(e.encounter_type=2, 'OP', IF(e.encounter_type=1, 'ER', 
                          IF(e.encounter_type=3 OR e.encounter_type=4,'IN','WN'))) AS POH_PAT_TYPE,
                          e.current_dept_nr, e.current_att_dr_nr
                        FROM care_encounter e
                        INNER JOIN care_person p ON p.pid=e.pid
                        LEFT JOIN care_encounter_location l ON l.encounter_nr=e.encounter_nr 
                            AND l.type_nr=5 AND l.discharge_type_nr=0 AND l.status=''
                        WHERE e.encounter_nr='".$encounter_nr."'";

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    function getPatientLocation($encounter_nr){
        global $db;

        $this->sql =" SELECT IF(e.encounter_type=2, fn_get_department_name(e.current_dept_nr), IF(e.encounter_type=1, 'ER', 
                          IF(e.encounter_type=3 OR e.encounter_type=4,CONCAT(fn_get_ward_name(e.current_ward_nr),'^',e.current_room_nr,'^',l.location_nr),'WN'))) AS location, e.current_dept_nr
                          FROM care_encounter e
                          LEFT JOIN care_encounter_location l ON l.encounter_nr=e.encounter_nr 
                          AND l.type_nr=5 AND l.discharge_type_nr=0 AND l.status=''
                        WHERE e.encounter_nr='".$encounter_nr."'";

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    function addInfo_HL7_file_received($details){
        global $db;
        
        /*
        $index = " date_received,filename,hl7_msg,parse_status";

        $values = "NOW(),'".$details->filename."','".$details->hl7_msg."','pending'";

        $this->sql = "INSERT INTO seg_hl7_file_received ($index)
                            VALUES ($values)";
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;*/
        $datenow = date("Y-m-d H:i:s");
        $result = $db->Replace('seg_hl7_file_received',
                                            array(
                                                     'date_received'=>$datenow,
                                                     'filename'=>$details->filename,
                                                     'hl7_msg'=>$details->hl7_msg,
                                                     'parse_status'=>$details->parse_status
                                                ),
                                                array('filename'),
                                                $autoquote=TRUE
                                           );
                                           
         if ($result) 
            return TRUE;
         else
            return FALSE;
         
    }
    
    function addInfo_PDF_file_received($details){
        global $db;
        
        /*
        $index = " date_received,filename,hl7_msg";

        $values = "NOW(),'".$details->filename."','".$details->hl7_msg."'";

        $this->sql = "INSERT INTO seg_hl7_pdffile_received ($index)
                            VALUES ($values)";
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;*/
        $datenow = date("Y-m-d H:i:s");
        $result = $db->Replace('seg_hl7_pdffile_received',
                                            array(
                                                     'date_received'=>$datenow,
                                                     'filename'=>$details->filename,
                                                     'hl7_msg'=>$details->hl7_msg
                                                ),
                                                array('filename'),
                                                $autoquote=TRUE
                                           );
                                           
         if ($result) 
            return TRUE;
         else
            return FALSE;
    }
    #----------------------

    #added by VAS 03-22-2012
    function apply_coverage($refno, $itemsArray){ 
        global $db;
        $enc_obj=new Encounter;
        
        if (!is_array($itemsArray)) $itemsArray = array($itemsArray);
        
        $ref = $db->GetRow("SELECT encounter_nr,IF(is_cash,NULL,grant_type) AS charge_type FROM seg_lab_serv\n".
            "WHERE refno=".$db->qstr($refno));
        
        for ($i=0; $i<sizeof($itemsArray);$i++){
            $dbOk = TRUE;    
            
            #$item_status = $itemsArray[$i][0];
            $is_served = $itemsArray[$i][1];
            
             if ($is_served){
               $item_status = 'done'; 
            }else{
               $item_status = 'pending'; 
            }
            
            
            $item = $itemsArray[$i][5];
            
            # Get request item details
            #$this->sql = "SELECT price_cash*quantity AS total,status AS serve_status FROM seg_lab_servdetails\n".
            #                "WHERE refno=".$db->qstr($refno)." AND service_code=".$db->qstr($item);
            $this->sql = "SELECT price_cash*quantity AS total,IF(is_served, 'done','pending') AS serve_status FROM seg_lab_servdetails\n".
                "WHERE refno=".$db->qstr($refno)." AND service_code=".$db->qstr($item);
                
            $item_details = $db->GetRow($this->sql);
            if (!$item_details) {
                $this->error_msg = 'Unable to retrieve request item details...';
                return FALSE;
            }
            
            $old_serve_status = $item_details['serve_status'];
            $new_serve_status = $item_status;
            
            if (($old_serve_status != $new_serve_status)){
                if ($ref['charge_type'] == 'phic') { 
                        // Hardcode hcare ID (temporary workaround)
                        define('__PHIC_ID__', 18);

                    if ($item_status=='done'){

                        $this->sql = "SELECT coverage FROM seg_applied_coverage\n".
                                        "WHERE ref_no='T{$ref['encounter_nr']}'\n".
                                            "AND source='L'\n".
                                            "AND item_code=".$db->qstr($item)."\n".
                                            "AND hcare_id=".__PHIC_ID__;
                                
                        $coverage = parseFloatEx($db->GetOne($this->sql)) + parseFloatEx($item_details['total']);
                        $result = $db->Replace('seg_applied_coverage',
                                            array(
                                                 'ref_no'=>"T{$ref['encounter_nr']}",
                                                 'source'=>'L',
                                                 'item_code'=>$item,
                                                 'hcare_id'=>__PHIC_ID__,
                                                 'coverage'=>$coverage
                                            ),
                                            array('ref_no', 'source', 'item_code', 'hcare_id'),
                                            $autoquote=TRUE
                                       );

                        if ($result) 
                            $dbOk = TRUE;
                        else {
                            $this->error_msg = "Unable to update applied coverage for item #{$item}...";
                            $dbOk = FALSE;
                        }
                    }else{

                        // Possible but leads to some complications
                        // Handle later
                        #$this->error_msg = "Cannot unserve item #{$item} due to PHIC coverage...";
                        
                        #check if there is a final bill
                        #get encounter and charge type info
                        $ref = $db->GetRow("SELECT encounter_nr,IF(is_cash,NULL,grant_type) AS charge_type FROM seg_lab_serv\n".
                                            "WHERE refno=".$db->qstr($refno));
                         
                        #check if the encounter of the request has a final bill                    
                        $hasfinal_bill = $enc_obj->hasFinalBilling($ref['encounter_nr']);
                        
                        if (!$hasfinal_bill){
                            # Handle applied coverage for PHIC and other benefits
                            $sql_app = "SELECT coverage FROM seg_applied_coverage\n".
                                            "WHERE ref_no='T{$ref['encounter_nr']}'\n".
                                            "AND source='L'\n".
                                            "AND item_code=".$db->qstr($item)."\n".
                                            "AND hcare_id=".__PHIC_ID__;
                            
                            #less the cancelled or deleted item                                                    
                            $coverage = parseFloatEx($db->GetOne($sql_app)) - parseFloatEx($item_details['total']);

                            $result = $db->Replace('seg_applied_coverage',
                                                        array(
                                                                'ref_no'=>"T{$ref['encounter_nr']}",
                                                                'source'=>'L',
                                                                'item_code'=>$item,
                                                                'hcare_id'=>__PHIC_ID__,
                                                                'coverage'=>$coverage
                                                            ),
                                                        array('ref_no', 'source', 'item_code', 'hcare_id'),
                                                        $autoquote=TRUE
                                                  );
                            $dbOk = TRUE; 
                            
                        }else{
                        $this->error_msg = "Cannot unserve item #{$item} due to PHIC coverage...";
                        $dbOk = FALSE;
                    }
                }
            }       
        }
        }
        return TRUE;
    }
    
    #added by VAS 03-23-2012
    function getInfoAppliedCoverage($refno){
        global $db;
        
        $this->sql = "SELECT encounter_nr, SUM(price_cash) AS total    
                        FROM seg_lab_servdetails d
                        INNER JOIN seg_lab_serv s ON s.refno=d.refno
                        WHERE s.refno=".$db->qstr($refno)."
                        AND s.grant_type='phic' AND d.is_served=1";
                
        $item_details = $db->GetRow($this->sql);
        
        return $item_details;
    }    

    function getBasicPatientInfo($refno=''){
        global $db;

        if(empty($refno) || (!$refno)){
            return FALSE;
        }

        $this->sql= "SELECT s.pid, p.name_last, p.name_first, p.name_middle, e.encounter_type, s.encounter_nr
                        FROM care_person p
                        INNER JOIN seg_lab_serv s ON s.pid=p.pid
                        LEFT JOIN care_encounter e ON e.encounter_nr=s.encounter_nr
                        WHERE s.refno='$refno'
                        AND s.status NOT IN ($this->dead_stat) LIMIT 1";

        if ($buf=$db->Execute($this->sql)){
            if($this->count=$buf->RecordCount()) {
                return $buf->FetchRow();
            }else { return FALSE; }
        }else { return FALSE; }
    }
    
    function DoneLabRequest($refno, $date_served){
        global $db;
        
        $this->sql = "UPDATE seg_lab_servdetails
                        SET is_served=1, date_served='".$date_served."',status='done'
                        WHERE refno='".$refno."' AND is_forward=1";
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
    }
    
    function getLISOrderNo($order_no){
        global $db;

        $this->sql ="SELECT * FROM seg_lab_hclab_orderno WHERE lis_order_no='$order_no'";
        
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            $row = $this->result->FetchRow();
            return $row['refno'];
        } else{
            return FALSE;
        } 
    }
    
    function getHactInfo($pid){
        global $db;

        $this->sql ="SELECT * FROM seg_blood_hact WHERE pid='$pid'";
        
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    function checkHactInfo($pid, $request_date){
        global $db;

        $this->sql ="SELECT * FROM seg_blood_hact WHERE pid='$pid'
                     AND create_tm <= '".$request_date."'";
        
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    function addHactPatient($pid, $status, $request_time){
        global $db, $HTTP_SESSION_VARS;
        
        $index = "pid, history, status, create_id, create_tm, modify_id, modify_tm";
        
        $clerk = $HTTP_SESSION_VARS['sess_user_name'];
        $date_created = date("Y-m-d H:i:s");
        $request_time = date("Y-m-d H:i:s", strtotime($request_time));
        $history = $this->ConcatHistory("Created  as $status patient ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
        
        $values = "'".$pid."',".$history.",'".$status."','".$clerk."','".$request_time."','".$clerk."','".$date_created."'";

        $this->sql = "INSERT INTO seg_blood_hact ($index)
                            VALUES ($values)";
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
    }
    
    function updateHactPatient($pid, $status){
        global $db, $HTTP_SESSION_VARS;
        
        $clerk = $HTTP_SESSION_VARS['sess_user_name'];
        $date_created = date("Y-m-d H:i:s");
        $request_time = date("Y-m-d H:i:s", strtotime($request_time));
        $history = $this->ConcatHistory("Updated as $status patient ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
        
        $this->sql = "UPDATE seg_blood_hact
                        SET history=".$history.", 
                        status='".$status."',
                        modify_id='".$clerk."',
                        modify_tm = '".$date_created."'
                        WHERE pid='".$pid."'";
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
    }    

    function getPatientInfoRefno($refno, $service_code){
        global $db;

        $this->sql = "SELECT h.pid, fn_get_person_lastname_first(h.pid) AS patient_name, s.NAME AS test_name, d.quantity, d.service_code, h.serv_dt, h.serv_tm,
                        IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
                        IF(UPPER(p.sex)='F','Female',IF(UPPER(p.sex)='M','Male','Unspecified')) AS sex,
                        bt.NAME AS blood_type 
                        FROM seg_lab_serv h 
                        INNER JOIN seg_lab_servdetails d ON d.refno=h.refno 
                        INNER JOIN seg_lab_services s ON s.service_code=d.service_code 
                        INNER JOIN care_person p ON p.pid=h.pid
                        LEFT JOIN seg_blood_type_patient b ON b.pid=h.pid
                        LEFT JOIN seg_blood_type bt ON bt.id=b.blood_type
                        WHERE h.refno=".$db->qstr($refno)." AND d.service_code=".$db->qstr($service_code);

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        }
    }
    
    function getBloodTypeInfo($pid){
        global $db;

        $this->sql ="SELECT * FROM seg_blood_type_patient WHERE pid='$pid'";
        
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    function checkBloodTypeInfo($pid, $request_date){
        global $db;

        $this->sql ="SELECT * FROM seg_blood_type_patient WHERE pid='$pid'
                     AND create_tm <= '".$request_date."'";
        
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    function updateBlood_Type($details){
        global $db, $HTTP_SESSION_VARS;
        
        $encoder = $HTTP_SESSION_VARS['sess_user_name'];
        $date_created = date("Y-m-d H:i:s");
        $history = $this->ConcatHistory("Updated ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
        
        $result = $db->Replace('seg_blood_type_patient',
                                            array(
                                                     'pid'=>$db->qstr($details->pid),
                                                     'history'=>$history,
                                                     'blood_type'=>$db->qstr($details->blood_type),
                                                     'create_id'=>$db->qstr($encoder),
                                                     'create_tm'=>$db->qstr($details->create_tm),
                                                     'modify_id'=>$db->qstr($encoder),
                                                     'modify_tm'=>$db->qstr($date_created)
                                                ),
                                                array('pid'),
                                                $autoquote=FALSE
                                           );
                                           
         if ($result) 
            return TRUE;
         else
            return FALSE;
         
    }
    
    function UpdateReceivedSample_h($refno, $data){
        global $db,$HTTP_SESSION_VARS;
        
        extract($data);
        $encoder = $HTTP_SESSION_VARS['sess_user_name'];
        $date_created = date("Y-m-d H:i:s");
        $history = $this->ConcatHistory("Updated ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
        
        $result = $db->Replace('seg_blood_received_header',
                                            array(
                                                     'refno'=>$db->qstr($refno),
                                                     'status'=>$db->qstr($status_rec),
                                                     'history'=>$history,
                                                     'create_id'=>$db->qstr($encoder),
                                                     'create_date'=>$db->qstr($date_created),
                                                     'modify_id'=>$db->qstr($encoder),
                                                     'modify_date'=>$db->qstr($date_created)
                                                ),
                                                array('refno'),
                                                $autoquote=FALSE
                                           );
                                           
         if ($result) 
            return TRUE;
         else
            return FALSE;
    }
    
    function UpdateReceivedSample_sh($refno, $service_code, $data){
        global $db,$HTTP_SESSION_VARS;
        
        extract($data);
        $encoder = $HTTP_SESSION_VARS['sess_user_name'];
        $date_created = date("Y-m-d H:i:s");
        $history = $this->ConcatHistory("Updated ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
        
        $result = $db->Replace('seg_blood_received_sample',
                                            array(
                                                     'refno'=>$db->qstr($refno),
                                                     'service_code'=>$db->qstr($service_code),
                                                     'ordered_qty' =>$db->qstr($ordered_qty),
                                                     'received_qty' =>$db->qstr($received_qty),
                                                     'status'=>$db->qstr($status_sh),
                                                     'history'=>$history,
                                                     'create_id'=>$db->qstr($encoder),
                                                     'create_dt'=>$db->qstr($date_created),
                                                     'modify_id'=>$db->qstr($encoder),
                                                     'modify_dt'=>$db->qstr($date_created)
                                                ),
                                                array('refno','service_code'),
                                                $autoquote=FALSE
                                           );
                                           
         if ($result) 
            return TRUE;
         else
            return FALSE;
    }
    
    function UpdateReceivedSample_d($refno, $service_code, $data){
        global $db,$HTTP_SESSION_VARS;
        
        $encoder = $HTTP_SESSION_VARS['sess_user_name'];
        $date_created = date("Y-m-d H:i:s");
        $history = $this->ConcatHistory("Updated ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
          
        $this->sql = "INSERT INTO seg_blood_received_details(refno,service_code,ordering,received_date,component,
                         serial_no,status,result,history,create_id,create_dt,modify_id,modify_dt) VALUES ";

        $i=0;
        for ($i=0; $i<sizeof ($data);$i++){
                if ($i > 0) $this->sql .= ",";
                $this->sql .= "(".$db->qstr($refno).", ".$db->qstr($service_code).",".$db->qstr($data[$i][0]).",".$db->qstr($data[$i][1]).
                              ",".$db->qstr($data[$i][2]).", ".$db->qstr($data[$i][3]).", ".$db->qstr($data[$i][4]).",
                              ".$db->qstr($data[$i][5]).",
                              ".$history.", ".$db->qstr($encoder).", ".$db->qstr($date_created).", ".$db->qstr($encoder).", ".$db->qstr($date_created).")";
        }

        return $this->Transact();
               
    }
    
    function emptyReceivedSample_d($refno,$service_code) {
        global $db, $HTTP_SESSION_VARS;
        $refno = $db->qstr($refno);
        $this->sql = "DELETE FROM seg_blood_received_details WHERE refno=$refno and service_code='$service_code'";
        return $this->Transact();
    }
    
    function updatebloodReceivedSample($refno, $service_code, $data){
        global $db,$HTTP_SESSION_VARS;
        
        extract($data);
        
        $db->StartTrans();
        $bSuccess = $this->UpdateReceivedSample_h($refno,$data);
        $bSuccess = $this->UpdateReceivedSample_sh($refno, $service_code, $data);
        
        $bSuccess = $this->emptyReceivedSample_d($refno, $service_code);
        $bSuccess = $this->UpdateReceivedSample_d($refno, $service_code, $arraySampleItems_d);
                
        if (!$bSuccess) $db->FailTrans();
            $db->CompleteTrans();
            
        return $bSuccess;    
    }
    
    function getBloodReceived($refno, $service_code, $index){
        global $db;

        $this->sql="SELECT * FROM seg_blood_received_details 
                    WHERE refno=".$db->qstr($refno)." 
                    AND service_code=".$db->qstr($service_code)." 
                    AND ordering=".$db->qstr($index);
        
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        }
    } 
    
    function getBloodReceivedStatus($refno, $service_code, $index){
        global $db;

        $this->sql="SELECT * FROM seg_blood_received_status 
                     WHERE refno=".$db->qstr($refno)." 
                     AND service_code=".$db->qstr($service_code)." 
                     AND ordering=".$db->qstr($index);

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        }
    }
    
    function getTestProfileInclude($service_code){
        global $db;

        $this->sql ="SELECT * FROM seg_lab_group WHERE service_code='$service_code' 
                     AND status NOT IN ('deleted','hidden','inactive','void')";
        
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result;
        } else{
            return FALSE;
        } 
    }
    
    function getTestInfo($service_code){
        global $db;

        $this->sql =" SELECT * FROM seg_lab_services WHERE service_code='$service_code' 
                      AND status NOT IN ('deleted','hidden','inactive','void')";
        
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    function saveInfoSerial($details){
        global $db, $HTTP_SESSION_VARS;
        
        $encoder = $HTTP_SESSION_VARS['sess_user_name'];
        $date_created = date("Y-m-d H:i:s");
        $history = $this->ConcatHistory("Added ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
        extract($details);
        
        $deleted = '0';
        $result = $db->Replace('seg_lab_serv_serial',
                                            array(
                                                     'refno'=>$db->qstr($refno),
                                                     'service_code'=>$db->qstr($service_code),
                                                     'lis_order_no'=>$db->qstr($lis_order_no),
                                                     'nth_take'=>$db->qstr($nth_take),
                                                     'is_served'=>$db->qstr($is_served),
                                                     'with_result'=>$db->qstr($with_result),
                                                     'is_repeated'=>$db->qstr($is_repeated),
                                                     'history'=>$history,
                                                     'create_id'=>$db->qstr($encoder),
                                                     'create_date'=>$db->qstr($date_created),
                                                     'modify_id'=>$db->qstr($encoder),
                                                     'modify_date'=>$db->qstr($date_created),
                                                     'is_deleted'=>$db->qstr($deleted)
                                                ),
                                                array('refno','service_code','nth_take'),
                                                $autoquote=FALSE
                                           );
                                           
         if ($result) 
            return TRUE;
         else
            return FALSE;
    }
    
    function getRecentSerialTestInfo($refno, $service_code){
        global $db;

        $this->sql ="SELECT * FROM seg_lab_serv_serial WHERE refno='$refno' 
                     AND service_code='$service_code'
                     AND is_deleted=0
                     ORDER BY nth_take DESC LIMIT 1";
        
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    function getSerialCountInfo($refno, $service_code){
        global $db;

        $this->sql ="SELECT COUNT(*) AS no_takes FROM seg_lab_serv_serial 
                     WHERE refno='$refno' AND service_code='$service_code' AND is_deleted=0";
        
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    function getSerialTestCatered($refno, $service_code, $nth_take){
        global $db;

        $this->sql ="SELECT * FROM seg_lab_serv_serial 
                     WHERE refno='$refno' AND service_code='$service_code'
                     AND nth_take='$nth_take' AND is_deleted=0";
                     
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }    
    
    function getEncounterType($refno){
        global $db;

        $this->sql ="SELECT e.encounter_type as ptype, s.encounter_nr 
                     FROM seg_lab_serv AS s
                     INNER JOIN care_encounter e ON e.encounter_nr=s.encounter_nr
                     WHERE s.refno='$refno'";
                     
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    function deleteSerial($refno, $service_code, $index){
        global $db, $HTTP_SESSION_VARS;
        
        $clerk = $HTTP_SESSION_VARS['sess_user_name'];
        $date_created = date("Y-m-d H:i:s");
        $request_time = date("Y-m-d H:i:s", strtotime($request_time));
        $history = $this->ConcatHistory("Updated as deleted item ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
        
        $this->sql = "UPDATE seg_lab_serv_serial
                        SET history=".$history.", 
                        is_deleted = 1,
                        modify_id='".$clerk."',
                        modify_date = '".$date_created."'
                        WHERE refno='".$refno."'
                        AND service_code = '".$service_code."'
                        AND nth_take= '".$index."'";
        
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE;
        
    }
    
    function getPatientOrderInfo($refno) {
            global $db;
        $this->useLabServ();
        $refno = $db->qstr($refno);

        $this->sql="SELECT r.pid, p.name_last,p.name_first,p.name_middle,p.senior_ID,\n".
                "e.encounter_type, e.encounter_class_nr, e.is_medico,\n".
                "e.current_ward_nr, e.current_room_nr, e.current_dept_nr,\n".
                "p.sex, p.civil_status, p.blood_group, fn_get_person_name(r.pid) as patient_name,\n".
                "IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_age(e.encounter_date,p.date_birth),age) AS age,\n".
                "p.date_birth\n".
                "FROM $this->coretable AS r\n".
                "INNER JOIN $this->tb_person AS p ON p.pid=r.pid\n".
                "LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr\n".
                "WHERE r.refno=$refno LIMIT 1";

        if($this->result=$db->Execute($this->sql)) {
            return $this->result->FetchRow();
        } else { return false; }
    }
    
    function getRequestResult($pid, $lis_order_no){
        global $db;

        $filename = $pid.'_'.$lis_order_no.'.pdf';
        $this->sql ="SELECT * FROM seg_hl7_pdffile_received WHERE filename=".$db->qstr($filename)."  
                         ORDER BY date_received DESC
                         LIMIT 1";
                     
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        } 
    }
    
    //added by VAN 02-11-2013
    //include the serial lab result viewing
    //exclude the test item not in LIS
    function SearchLabRequests($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE,$mod,$done=0, $is_doctor=0,$encounter_nr='', $source='LB'){
                global $db, $sql_LIKE, $root_path, $date_format;
                if(empty($maxcount)) $maxcount=100;
                if(empty($offset)) $offset=0;

                # convert * and ? to % and &
                $searchkey=strtr($searchkey,'*?','%_');
                $searchkey=trim($searchkey);
                $searchkey = str_replace("^","'",$searchkey);
                $suchwort=addslashes($searchkey);

                if(is_numeric($suchwort)) {
                        $this->is_nr=TRUE;

                        if(empty($oitem)) $oitem='refno';
                        if(empty($odir)) $odir='DESC'; # default, latest pid at top

                        $sql2="    WHERE r.status NOT IN ($this->dead_stat) AND ((r.pid = '$suchwort')) ";
                } else {
                        # Try to detect if searchkey is composite of first name + last name
                        if(stristr($searchkey,',')){
                                $lastnamefirst=TRUE;
                        }else{
                                $lastnamefirst=FALSE;
                        }

                        #$searchkey=strtr($searchkey,',',' ');
                        $cbuffer=explode(',',$searchkey);

                        # Remove empty variables
                        for($x=0;$x<sizeof($cbuffer);$x++){
                                $cbuffer[$x]=trim($cbuffer[$x]);
                                if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
                        }

                        # Arrange the values, ln= lastname, fn=first name, rd = request date
                        if($lastnamefirst){
                                $fn=$comp[1];
                                $ln=$comp[0];
                                $rd=$comp[2];
                        }else{
                                $fn=$comp[0];
                                $ln=$comp[1];
                                $rd=$comp[2];
                        }
                        # Check the size of the comp
                        if(sizeof($comp)>1){
                                #$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') ";
                                $sql2=" WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";

                                if(!empty($rd)){
                                        $DOB=@formatDate2STD($rd,$date_format);
                                        if($DOB=='') {
                                                #$sql2.=" AND serv_dt $sql_LIKE '$rd%' ";
                                        }else{
                                                $sql2.=" AND serv_dt = '$DOB' ";
                                                #$sql2.=" AND serv_dt LIKE '$DOB%' ";
                                        }
                                }
                                $sql2.=" AND r.status NOT IN ($this->dead_stat) ";
                        }else{
                                # Check if * or %
                                if($suchwort=='%'||$suchwort=='%%'){
                                        #return all the data
                                        $sql2=" WHERE r.status NOT IN ($this->dead_stat) ";
                                }elseif($suchwort=='now'){
                                        $sql2=" WHERE r.serv_dt=DATE(NOW()) AND r.status NOT IN ($this->dead_stat) ";
                                }else{
                                        # Check if it is a complete DOB
                                        $DOB=@formatDate2STD($suchwort,$date_format);
                                        if($DOB=='') {
                                                if(TRUE){
                                                        if($fname){
                                                                #$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%') ";
                                                                $sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%') ";
                                                        }else{
                                                                $sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
                                                        }
                                                }else{
                                                        $sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
                                                }
                                        }else{
                                                $sql2=" WHERE serv_dt = '$DOB' ";
                                        }
                                        $sql2.=" AND r.status NOT IN ($this->dead_stat) ";
                                }
                        }
                 }

                $order_by = " ORDER BY r.serv_dt DESC, r.refno,d.service_code,s.in_lis ";
                
                if ($done){
                        $cond_done = " AND d.status='done' AND d.is_served=1 ";
                        #$cond_done = " AND filename IS NOT NULL";
                        $order_by = " ORDER BY r.serv_dt DESC, r.serv_tm DESC, r.refno,d.service_code,s.in_lis ";
                }else{
                        $cond_done = " AND d.status='pending' AND d.is_served=1 ";
                        #$cond_done = " AND filename IS NULL";
                }
                
                //edited by VAN 02-11-2013
                //include the serial lab result (same reference no. with different order LIS no.)
                $this->sql = /*"SELECT SQL_CALC_FOUND_ROWS \n".
                                "r.refno, r.ordername AS patient_name, r.pid, r.encounter_nr, \n". 
                                "sr.nth_take, sr.service_code, name_last, name_first, name_middle, date_birth, sex, \n".
                                "IF(fn_calculate_age(r.serv_dt,p.date_birth),fn_get_age(r.serv_dt,p.date_birth),age) AS age, \n".
                                "fn_get_labtest_request_all(r.refno) AS services, s.in_lis, \n".
                                "IF(o.lis_order_no IS NOT NULL, o.lis_order_no, tr.lis_order_no) AS lis_order_no, r.serv_dt, r.serv_tm, rs.filename,rs.date_received, \n".
                                "e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type, \n".
                                "r.is_cash, r.refno,r.is_urgent,r.serv_dt, \n".
                                "r.serv_tm,r.parent_refno,r.is_repeat, r.type_charge, d.request_flag AS charge_name \n".
                                "FROM seg_lab_serv r \n".
                                "INNER JOIN seg_lab_servdetails d ON d.refno=r.refno \n".
                                "INNER JOIN care_person p ON p.pid=r.pid \n".
                                "INNER JOIN seg_lab_services s ON s.service_code=d.service_code \n".
                                "LEFT JOIN care_encounter e ON e.encounter_nr=r.encounter_nr \n".
                                "LEFT JOIN seg_hl7_lab_tracker tr ON tr.refno=r.refno \n".
                                "LEFT JOIN seg_lab_hclab_orderno o ON o.refno=r.refno \n".
                                "LEFT JOIN seg_hl7_pdffile_received rs ON rs.filename=CONCAT(r.pid,'_',IF(o.lis_order_no IS NOT NULL, o.lis_order_no, tr.lis_order_no),'.pdf') \n".
                                "LEFT JOIN seg_lab_serv_serial sr ON sr.refno=r.refno AND sr.lis_order_no=tr.lis_order_no \n".
                                " $sql2 \n".
                                "AND r.STATUS NOT IN ('deleted','hidden','inactive','void') \n".
                                "AND d.STATUS NOT IN ('deleted','hidden','inactive','void') \n".
                                "AND (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0) \n".
                                "AND ref_source = 'LB' \n".
                                " $cond_done \n".
                                //"GROUP BY r.refno, o.lis_order_no \n".
                                "ORDER BY r.refno DESC";*/

                                "SELECT SQL_CALC_FOUND_ROWS \n".
                                "IF(ISNULL(gp.group_id),gp.group_id,gp.group_id) AS group_id, s.with_result, IFNULL(srl.`nth_take`, 0) nth_take, r.refno, r.ordername AS patient_name, r.pid, r.encounter_nr, \n". 
                                "GROUP_CONCAT(s.service_code) AS service_code, name_last, name_first, name_middle, date_birth, sex, \n".
                                "IF(fn_calculate_age(r.serv_dt,p.date_birth),fn_get_age(r.serv_dt,p.date_birth),age) AS age, \n".
                                "GROUP_CONCAT(s.name) AS services, \n".
                                "r.serv_dt, r.serv_tm, \n".
                                "e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type, \n".
                                "r.is_cash, r.refno,r.is_urgent,r.serv_dt, \n".
                                "r.serv_tm,r.parent_refno,r.is_repeat, r.type_charge, d.request_flag AS charge_name \n".
                                "FROM seg_lab_serv r \n".
                                "INNER JOIN seg_lab_servdetails d ON d.refno=r.refno \n".
                                "INNER JOIN care_person p ON p.pid=r.pid \n".
                                "INNER JOIN seg_lab_services s ON s.service_code=d.service_code \n".
                                "LEFT JOIN care_encounter e ON e.encounter_nr=r.encounter_nr \n".
                                "LEFT JOIN seg_lab_result_groupparams AS gp ON gp.service_code = d.service_code  \n".
                                "LEFT JOIN seg_lab_serv_serial srl 
								    ON (
								      srl.`refno` = d.`refno` 
								      AND srl.`service_code` = d.`service_code` 
								      AND s.`is_serial` = 1
								      AND srl.`is_deleted` <> 1
								    ) ".
                                " $sql2 \n".
                                "AND r.STATUS NOT IN ('deleted','hidden','inactive','void') \n".
                                "AND d.STATUS NOT IN ('deleted','hidden','inactive','void') \n".
                                "AND (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0) \n".
                                "AND ref_source = 'LB' \n".
                                " $cond_done \n".
                                "GROUP BY gp.group_id, r.refno, srl.`nth_take` \n".
                                "ORDER BY r.`serv_dt` DESC";

            #echo $this->sql;
            #SEARCH SELECT
            if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
                if($this->rec_count=$this->res['ssl']->RecordCount()) {
                        return $this->res['ssl'];
                }else{
                    return false;
                }
            }else{
                return false;
            }
               
        }
        //added by: borj
        //2013/28/11
     function UpdateBloodStatus($refno, $service_code, $index, $mode, $datetime, $return_reason){
        global $db;
        
        $encoder = $_SESSION['sess_temp_userid'];
        $date_created = date("Y-m-d H:i:s");
        
        //$objResponse = new xajaxResponse();
        //return $return_reason;
        if ($mode=='done')
            $date_fld = "done_date";
        elseif ($mode=='issuance')
            $date_fld = "issuance_date";
        elseif ($mode=='returned')
            $date_fld = "date_return";
        elseif ($mode=='reissue')
            $date_fld = "date_reissue";
        elseif ($mode=='consumed')
            $date_fld = "date_consumed";
        elseif ($return_reason !='return_reason')
             $date_fld = "return_reason";


            
         if (trim($datetime) == ''){
            $date_value = 'NULL';
            $history = $this->ConcatHistory("Updated [".$mode.":Cancelled] ".$date_created." ".$encoder."\n");
        }else{
            $date_value = "'".date("Y-m-d H:i:s",strtotime($datetime))."'";
            $history = $this->ConcatHistory("Updated [".$mode."] ".$date_created." ".$encoder."\n");
        }        
        
        $result = $db->Replace('seg_blood_received_status',
                                            array(
                                                     'refno'=>$db->qstr($refno),
                                                     'service_code'=>$db->qstr($service_code),
                                                     'ordering' =>$db->qstr($index),
                                                      $date_fld =>$date_value,
                                                     'history'=>$history,
                                                     'create_id'=>$db->qstr($encoder),
                                                     'create_dt'=>$db->qstr($date_created),
                                                     'modify_id'=>$db->qstr($encoder),
                                                     'modify_dt'=>$db->qstr($date_created),
                                                     'return_reason'=>$db->qstr($return_reason)
                                                ),
                                                array('refno','service_code','ordering'),
                                                $autoquote=FALSE
                                           );
                                           
         if ($result) 
          return true;
         else
             return false;
     }
    
    // Added by janjan 09/07/2015
    function checkIfAllDone($refno) {
    	global $db;

    	$this->sql = "SELECT COUNT(`refno`) AS count_done
    					FROM seg_lab_servdetails
    					WHERE `refno`=".$db->qstr($refno);
    	$count_row = $db->GetRow($this->sql);

    	$this->sql = "SELECT COUNT(`refno`) AS are_done
    					FROM seg_lab_servdetails
    					WHERE `refno`=".$db->qstr($refno)." AND 
    						`status`='done' AND `is_served`='1'";
    	$are_row = $db->GetRow($this->sql);

    	if($are_row['are_done']==$count_row['count_done'])
    		return 1;
    	else
    		return 0;
    }

    function checkRetReason($refno) {
    	global $db;

    	 $this->sql = "SELECT return_reason
                           FROM seg_blood_received_status 
                           WHERE refno=".$db->qstr($refno);

          $enc_row = $db->GetRow($this->sql);
          $enc_row['return_reason'];
         if ($enc_row['return_reason'])
         	return $enc_row['return_reason'];
         else
         	return false;
    }
   		//end borj
    
    function getLabResultPid($searchkey,$maxcount=100,$offset=0,$encounter_nr, $pid, $ref_source='LB', $count_sql){
                global $db, $sql_LIKE, $root_path, $date_format;
                if(empty($maxcount)) $maxcount=100;
                if(empty($offset)) $offset=0;

                # convert * and ? to % and &
                $searchkey=strtr($searchkey,'*?','%_');
                $searchkey=trim($searchkey);
                #$suchwort=$searchkey;
                $searchkey = str_replace("^","'",$searchkey);
                $suchwort=addslashes($searchkey);

                $where_cond = "";
                if (($suchwort)&& ($suchwort!='%' && $suchwort!='%%')){
                    $suchwort = date('Y-m-d',strtotime($suchwort));
                    $where_cond = " AND r.serv_dt='$suchwort' ";
                }
              
                $query = "SELECT pid, encounter_type, encounter_date, admission_dt, discharge_date, is_discharged 
                            FROM care_encounter WHERE encounter_nr=".$db->qstr($encounter_nr);

                $enc_row = $db->GetRow($query);

                if (($enc_row['encounter_type'] == '1') || ($enc_row['encounter_type'] == '2')){
                    $encounter_date = date("Y-m-d",strtotime($enc_row['encounter_date']));
                    $discharged_date = date("Y-m-d",strtotime($enc_row['encounter_date']));
                }else{
                    $encounter_date = date("Y-m-d",strtotime($enc_row['admission_dt']));
                    if (!$enc_row['is_discharged'])
                       $enc_row['discharge_date'] = date("Y-m-d"); 
                    $discharged_date = date("Y-m-d",strtotime($enc_row['discharge_date']));
                }
            
               $this->sql = /*"SELECT SQL_CALC_FOUND_ROWS o.refno, date_received AS request_date, 
                                SUBSTR(h.filename,INSTR(h.filename, '_')+1,LENGTH(SUBSTR(h.filename,INSTR(h.filename, '_')+1))-4) `lis_order_no`,
                                SUBSTR(h.filename,1,INSTR(h.filename, '_')-1) `pid`,
                                IF(fn_get_labtest_request_all(o.refno)<>'',
                                   fn_get_labtest_request_all(o.refno),
                                   CONCAT('MANUALLY ENCODED with Order No. ',
                                           SUBSTR(h.filename,INSTR(h.filename, '_')+1,
                                               LENGTH(SUBSTR(h.filename,INSTR(h.filename, '_')+1))-4))) AS services, 
                                o.refno, sr.nth_take, sr.service_code, h.*
                                FROM seg_hl7_pdffile_received h
                                LEFT JOIN seg_lab_hclab_orderno o ON o.lis_order_no=(SUBSTR(h.filename,INSTR(h.filename, '_')+1,LENGTH(SUBSTR(h.filename,INSTR(h.filename, '_')+1))-4))
                                LEFT JOIN seg_lab_serv_serial sr ON sr.refno=o.refno AND sr.lis_order_no=o.lis_order_no
                                WHERE filename LIKE '$pid%'
                                AND date_received BETWEEN ".$db->qstr($encounter_date)." AND ".$db->qstr($discharged_date)." + INTERVAL 1 MONTH
                                ORDER BY date_received DESC"*/
                                "SELECT s.`refno`, rd.`service_code`, serv.`name`, IFNULL(gp.`group_id`,'') AS group_id, rd.`service_date`, gn.`form_id`, rd.`nth_take` 
									FROM seg_lab_serv AS s
										INNER JOIN seg_lab_servdetails AS sd
											ON s.`refno` = sd.`refno`
										LEFT JOIN seg_lab_resultdata AS rd
											ON rd.`refno` = sd.`refno`
										LEFT JOIN seg_lab_services AS serv
    										ON serv.`service_code` = sd.`service_code`
										LEFT JOIN seg_lab_result_groupparams AS gp 
	    									ON gp.`service_code` = sd.`service_code` 
	    								LEFT JOIN seg_lab_result_groupname gn 
   										 ON gn.`group_id` = gp.`group_id` 
									WHERE sd.`status` = 'done' AND s.`encounter_nr` = ".$db->qstr($encounter_nr)."";           
                                    

                if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
                    if($this->rec_count=$this->res['ssl']->RecordCount()) {
                            return $this->res['ssl'];
                    }else{return false;}
                }else{return false;}
                
        }

	//added by Nick, 1/4/2014
	var $temp_sql = "";
    function getPending($refno,$code,$return_type){
	        global $db;
	        $sql = "SELECT  (IF((a.`status`='received'),'true','false')) AS is_received,
	                        (IF((a.`serial_no`!=''),a.`serial_no`,'PENDING')) AS serial_no,
	                        (IF((a.`component`!=''),a.`component`,'PENDING')) AS component,
	                        (IF(((a.`received_date`!='' AND a.`received_date`!='0000-00-00 00:00:00') AND a.`status`='received'),a.`received_date`,'PENDING')) AS received_dt,
	                        (IF((b.`done_date`!='' AND b.`done_date`!='0000-00-00 00:00:00'),b.`done_date`,'PENDING')) AS done_dt,
	                        (IF((b.`issuance_date`!='' AND b.`issuance_date`!='0000-00-00 00:00:00'),b.`issuance_date`,'PENDING')) AS issuance_dt,
	                        (IF((b.`date_return`!='' AND b.`date_return`!='0000-00-00 00:00:00'),b.`date_return`,'PENDING')) AS return_dt,
	                        (IF((b.`date_reissue`!='' AND b.`date_reissue`!='0000-00-00 00:00:00'),b.`date_reissue`,'PENDING')) AS reissue_dt,
	                        (IF((b.`date_consumed`!='' AND b.`date_consumed`!='0000-00-00 00:00:00'),b.`date_consumed`,'PENDING')) AS condumed_dt
	                FROM seg_blood_received_details AS a
	                LEFT JOIN seg_blood_received_status AS b ON a.`refno` = b.`refno` AND a.`ordering` = b.`ordering` AND a.`service_code` = b.`service_code`
	                WHERE a.`refno` = ".$db->qstr($refno)." AND a.`service_code` = ".$db->qstr($code);

	        $this->temp_sql = $sql;
	        $rs = $db->Execute($sql);
	        if(is_object($rs)){
	        	if($return_type=="string"){
		            while($row_pending = $rs->FetchRow()){
		                $dataInStringDB .= $row_pending['is_received'].','.
		                                   $row_pending['serial_no'].','.
		                                   $row_pending['component'].','.                                   
		                                   $row_pending['received_dt'].','.
		                                   $row_pending['done_dt'].','.
		                                   $row_pending['issuance_dt'].','.
		                                   $row_pending['return_dt'].','.
		                                   $row_pending['reissue_dt'].','.
		                                   $row_pending['condumed_dt']."\n";
		            }
		            //echo $sql; exit();
		            return $dataInStringDB;
		        }else{
		        	return $rs;
		        }            
	        }else{
	            return false;
	        }
    	}
    //end nick
  	
  	//Added by Jarel 12/12/13 Update seg_blood_borrow_info if has borrowed Blood
    function replacedBlood($enc)
    {
    	global $db;
    	$db->BeginTrans();
    	$sqlOrders =  "UPDATE seg_pharma_orders AS o
					   INNER JOIN seg_pharma_order_items AS d ON d.refno=o.refno
					   INNER JOIN care_pharma_products_main AS m ON m.bestellnum=d.bestellnum
					   SET o.comments = IF(o.comments LIKE '%borrow%','replaced',o.comments) 
					   WHERE o.encounter_nr=".$db->qstr($enc)." AND is_cash=0 AND d.serve_status='S' AND m.prod_class='M'
					   AND m.artikelname like '%blood%' AND o.pharma_area='BB'
					   AND (o.comments LIKE '%borrow%')";
		$ok = $db->Execute($sqlOrders);

		if ($ok) {
			$sqlRequest ="UPDATE seg_lab_serv AS r
						  LEFT JOIN seg_blood_borrow_info AS b ON b.refno=r.refno
						  SET r.comments = IF(r.comments LIKE '%borrow%','replaced',r.comments),
						  b.is_borrowed=0
						  WHERE r.ref_source='BB'
						  AND r.encounter_nr=".$db->qstr($enc)."
						  AND (r.comments LIKE '%borrow%' OR b.is_borrowed=1)
						  AND r.status NOT IN ($this->dead_stat)";
			$ok = $db->Execute($sqlRequest);
		}

	
		if (!$ok)  {
	    	$db->RollbackTrans();
	        return FALSE;
	    }else{
	    	$db->CommitTrans();
	    	return TRUE;
	    }
	}

	#added by VAS 01-14-2014
	function getPathologist(){
		global $db;

		$this->sql="SELECT fn_get_pid_name(cp.pid) AS fullname, cp.name_last, cp.name_first, cp.name_middle, cper.job_position, cper.other_title 
					FROM care_person AS cp INNER JOIN (((care_personell AS cper INNER JOIN care_personell_assignment AS cpa 
					ON cper.nr = cpa.personell_nr) INNER JOIN care_department AS cd ON cpa.location_nr = cd.nr)) ON cp.pid = cper.pid 
					WHERE UPPER(cper.job_position) REGEXP 'PATHOLOGIST' AND UPPER(cd.id) REGEXP 'PATHOLOGY'
					AND cper.status NOT IN ('void','hidden','deleted','inactive') 
					LIMIT 1";

		$result=$db->Execute($this->sql);
		if($result){
			return $result->FetchRow();
		}else return false;
	}

	function getLabGroup($service_code){
        global $db;

        $this->sql="SELECT s.name AS testname, g.*
					FROM seg_lab_services s
					INNER JOIN seg_lab_service_groups g ON g.group_code=s.group_code
					WHERE (service_code=".$db->qstr($service_code)." 
					OR oservice_code=".$db->qstr($service_code)." 
					OR ipdservice_code=".$db->qstr($service_code).")
					AND s.status NOT IN ('deleted','hidden','inactive','void')
					AND g.status NOT IN ('deleted','hidden','inactive','void')";

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        }
    }

    function getLabTestChild($service_code){
        global $db;

        $this->sql="SELECT g.service_code_child
					FROM seg_lab_group g
					INNER JOIN seg_lab_services s ON (s.service_code=g.service_code OR s.oservice_code=g.service_code OR s.ipdservice_code=g.service_code)
					WHERE (s.service_code=".$db->qstr($service_code)." 
						OR s.oservice_code=".$db->qstr($service_code)." 
						OR s.ipdservice_code=".$db->qstr($service_code).")
					AND s.status NOT IN ('deleted','hidden','inactive','void')
					AND g.status NOT IN ('deleted','hidden','inactive','void')";

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result;
        } else{
            return FALSE;
        }
    }

    #--------------

        //added by Nick
        function getClaimStubInfo($ref){
        	global $db;
        	$this->sql = 'SELECT 	c.`pid` AS hrn,
									a.`refno` AS refno,
									b.`ordername` AS patient_name,	
									a.`service_code` AS service_code,
									DATE_FORMAT(a.`received_date`,\'%M %d,%Y / %h:%i %p\') AS received_dt,
									DATE_FORMAT(DATE_ADD(a.`received_date`,INTERVAL 2 HOUR),\'%M %d,%Y / %h:%i %p\') AS due_dt,
									(SELECT care_ward.`name` FROM care_ward WHERE care_ward.nr = d.`current_ward_nr`) AS ward
								FROM seg_blood_received_details AS a 
								INNER JOIN seg_lab_serv AS b ON b.`refno` = a.`refno`
								INNER JOIN care_person AS c ON c.`pid` = b.`pid`
								INNER JOIN care_encounter AS d ON c.`pid` = d.`pid`
								WHERE a.`refno` = '.$db->qstr($ref).'
								GROUP BY refno;';
			if ($this->result=$db->Execute($this->sql)) {
            	$this->count=$this->result->RecordCount();
            	return $this->result->FetchRow();
        	} else{
            	return FALSE;
        	}
        }


//added by daryl
function ifWalkin()
{
	global $db;

	$this->sql="SELECT sls.*,sw.* ,
   fn_get_labtest_request_all(sls.refno) AS services
   FROM seg_lab_serv AS sls 
 	INNER JOIN seg_walkin AS sw ON sls.`pid` = sw.`pid`
	ORDER BY sls.`refno` DESC ";

	if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			return FALSE;
		}

}


#added by daryl
function selectwalkin($pid)
{
	global $db;
	$pid_ = $db->qstr($pid);
	$this->sql="SELECT sw.`name_first`, sw.`name_last`, sw.`name_middle`,sw.`address`,sls.*
				FROM seg_walkin AS sw 
				INNER JOIN seg_lab_serv AS sls ON sls.`pid` = sw.`pid`
				WHERE sw.`pid` = $pid_ ";

	if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			return FALSE;
		}

}


#added by daryl
	function SearchSelect_walk($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE,$mod,$done=0, $samplelist=0, $is_doctor=0, $encounter_nr='', $source='LB', $cond='', $count_sql=0, $group_code=FALSE, $isERIP=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				#$suchwort=$searchkey;
				$searchkey = str_replace("^","'",$searchkey);
				$suchwort=addslashes($searchkey);

				if(is_numeric($suchwort)) {
						#$suchwort=(int) $suchwort;
						$this->is_nr=TRUE;

						if(empty($oitem)) $oitem='refno';
						if(empty($odir)) $odir='DESC'; # default, latest pid at top

						$sql2="    WHERE r.status NOT IN ($this->dead_stat) AND (r.pid = '$suchwort')";
				} else {
						# Try to detect if searchkey is composite of first name + last name
						if(stristr($searchkey,',')){
								$lastnamefirst=TRUE;
						}else{
								$lastnamefirst=FALSE;
						}

						#$searchkey=strtr($searchkey,',',' ');
						$cbuffer=explode(',',$searchkey);

						# Remove empty variables
						for($x=0;$x<sizeof($cbuffer);$x++){
								$cbuffer[$x]=trim($cbuffer[$x]);
								if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
						}

						# Arrange the values, ln= lastname, fn=first name, rd = request date
						if($lastnamefirst){
								$fn=$comp[1];
								$ln=$comp[0];
								$rd=$comp[2];
						}else{
								$fn=$comp[0];
								$ln=$comp[1];
								$rd=$comp[2];
						}
						# Check the size of the comp
						if(sizeof($comp)>1){
								#$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') ";
								$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";

								if(!empty($rd)){
										$DOB=@formatDate2STD($rd,$date_format);
										if($DOB=='') {
												#$sql2.=" AND serv_dt $sql_LIKE '$rd%' ";
										}else{
												if ($done)
													$sql2.=" AND DATE(serv_dt) = '$DOB' ";
												else
													$sql2.=" AND serv_dt = '$DOB' ";
												#$sql2.=" AND serv_dt LIKE '$DOB%' ";
										}
								}
								$sql2.=" AND r.status NOT IN ($this->dead_stat) ";
						}else{
								# Check if * or %
								if($suchwort=='%'||$suchwort=='%%'){
										#return all the data
										$sql2=" WHERE r.status NOT IN ($this->dead_stat) ";
								}elseif($suchwort=='now'){
										#$sql2=" WHERE r.serv_dt=now() AND r.status NOT IN ($this->dead_stat) ";
										if ($done)
												$sql2=" WHERE DATE(serv_dt)=DATE(NOW()) AND r.status NOT IN ($this->dead_stat) ";
										else
												$sql2=" WHERE r.serv_dt=DATE(NOW()) AND r.status NOT IN ($this->dead_stat) ";
								}else{
										# Check if it is a complete DOB
										$DOB=@formatDate2STD($suchwort,$date_format);
										if($DOB=='') {
												if(TRUE){
														if($fname){
																#$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%') ";
																$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%') ";
														}else{
																$sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
														}
												}else{
														$sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
												}
										}else{
												if ($done)
													$sql2=" WHERE DATE(serv_dt) = '$DOB' ";
												else
													$sql2=" WHERE serv_dt = '$DOB' ";
												#$sql2=" WHERE serv_dt LIKE '$DOB%' ";
										}
										$sql2.=" AND r.status NOT IN ($this->dead_stat) ";
								}
						}
				 }
			 $sql_erip = "";
			 if ($isERIP){
				 $sql_erip = " AND (r.is_urgent=1 OR e.encounter_type IN (1,3,4)) ";
			 }

			 $sql_source = '';
			 #REQUEST LIST
			 if ($mod){
						if ($source=='LB'){
							#added by VAN 05-24-2010
							$sql_sample = "";
							$sql_sample_gr = "";

							if ($samplelist){
								$sql_sample = " INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno ";
								$sql_sample_gr = " GROUP BY r.refno ";

								if ($group_code=='1')
									$sql_source .= " AND is_forward = 1";
								else if($group_code=='2')
									$sql_source .= " AND is_forward = 0";
							}else{
								if($group_code && $group_code!='0'){
									if($group_code=='1')
										$sql_source .= " AND ISNULL(encounter_type) ";
									if($group_code=='2')
										$sql_source .= " AND encounter_type = 2 ";
									if($group_code=='3')
										$sql_source .= " AND encounter_type = 5 ";
									if($group_code=='4')
										$sql_source .= " AND encounter_type = 1 ";
									if($group_code=='5')
										$sql_source .= " AND encounter_type IN (3,4,6) ";
								}
							}

							$sql_source .= " AND ref_source = '".$source."'";
						}else{
							 $sql_source .= " AND ref_source = '".$source."'";
						}

						if(isset($oitem)&&!empty($oitem)) $sql3 =" $sql_erip $sql_source $cond $sql_sample_gr ORDER BY is_urgent DESC,refno DESC,r.serv_dt DESC ";

						$this->sql= "SELECT SQL_CALC_FOUND_ROWS r.refno,p.name_last,p.name_first, p.name_middle, p.pid, p.sex,
														
														p.date_birth,r.encounter_nr, e.encounter_type,
														r.serv_dt,r.serv_tm, r.is_urgent, r.is_cash, r.is_tpl,
														e.current_ward_nr,e.current_room_nr,e.current_dept_nr,e.current_att_dr_nr
														FROM seg_lab_serv AS r ".$sql_sample ."
														INNER JOIN seg_walkin AS p ON p.pid=r.pid
														LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
														$sql2
														$sql3
												 ";
							
				}else{
					 #DONE LIST

					 $sql_source .= " AND ref_source = '".$source."'";

					 if(isset($oitem)&&!empty($oitem)) $sql3 =" $sql_erip $sql_source $cond ORDER BY is_urgent DESC,refno DESC,r.serv_dt DESC ";

					 #added by VAN 07-02-08
						if ($done){
								$served = " AND d.is_served = 1 ";

								if ($source=='LB'){
									$join_sql = " INNER JOIN seg_lab_hclab_orderno AS o ON o.refno=r.refno
																INNER JOIN seg_lab_results AS res ON res.order_no=o.lis_order_no ";
								}
						}else{
								#UNDONE LIST
							 $served = " AND d.is_served = 0 ";
							 $join_sql = "";
						}

					
						$cond = " AND (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0) ";

						$this->sql = "SELECT DISTINCT SQL_CALC_FOUND_ROWS r.refno,p.name_last,p.name_first, p.name_middle, p.pid, p.sex,
														
														p.date_birth,r.encounter_nr, e.encounter_type, d.request_flag,
														d.service_code, r.serv_dt,r.serv_tm, r.is_urgent, r.is_cash, r.is_tpl, s.name AS service_name
														FROM seg_lab_serv AS r
														INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno
														INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code
														INNER JOIN seg_walkin AS p ON p.pid=r.pid
														LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
														$join_sql
														$sql2
														$served
														$cond
														$sql3
												 ";
					 
				}

				$this->count_sql = $count_sql;
				#COUNTSEARCH SELECT
				if ($count_sql){
						if ($this->result=$db->Execute($this->sql)) {
								if ($this->count=$this->result->RecordCount()) {
										return $this->result;
								}
								else{return FALSE;}
						}else{return FALSE;}
				}else{
						#SEARCH SELECT
						if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
						}else{return false;}
				}
	}



#added by daryl
 function SearchLabRequests_walk($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE,$mod,$done=0, $is_doctor=0,$encounter_nr='', $source='LB'){
                global $db, $sql_LIKE, $root_path, $date_format;
                if(empty($maxcount)) $maxcount=100;
                if(empty($offset)) $offset=0;

                # convert * and ? to % and &
                $searchkey=strtr($searchkey,'*?','%_');
                $searchkey=trim($searchkey);
                $searchkey = str_replace("^","'",$searchkey);
                $suchwort=addslashes($searchkey);

                if(is_numeric($suchwort)) {
                        $this->is_nr=TRUE;

                        if(empty($oitem)) $oitem='refno';
                        if(empty($odir)) $odir='DESC'; # default, latest pid at top

                        $sql2="    WHERE r.status NOT IN ($this->dead_stat) AND ((r.pid = '$suchwort')) ";
                } else {
                        # Try to detect if searchkey is composite of first name + last name
                        if(stristr($searchkey,',')){
                                $lastnamefirst=TRUE;
                        }else{
                                $lastnamefirst=FALSE;
                        }

                        #$searchkey=strtr($searchkey,',',' ');
                        $cbuffer=explode(',',$searchkey);

                        # Remove empty variables
                        for($x=0;$x<sizeof($cbuffer);$x++){
                                $cbuffer[$x]=trim($cbuffer[$x]);
                                if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
                        }

                        # Arrange the values, ln= lastname, fn=first name, rd = request date
                        if($lastnamefirst){
                                $fn=$comp[1];
                                $ln=$comp[0];
                                $rd=$comp[2];
                        }else{
                                $fn=$comp[0];
                                $ln=$comp[1];
                                $rd=$comp[2];
                        }
                        # Check the size of the comp
                        if(sizeof($comp)>1){
                                
                                $sql2=" WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";

                                if(!empty($rd)){
                                        $DOB=@formatDate2STD($rd,$date_format);
                                        if($DOB=='') {
                                              
                                        }else{
                                                $sql2.=" AND serv_dt = '$DOB' ";
                                             
                                        }
                                }
                                $sql2.=" AND r.status NOT IN ($this->dead_stat) ";
                        }else{
                                # Check if * or %
                                if($suchwort=='%'||$suchwort=='%%'){
                                        #return all the data
                                        $sql2=" WHERE r.status NOT IN ($this->dead_stat) ";
                                }elseif($suchwort=='now'){
                                        $sql2=" WHERE r.serv_dt=DATE(NOW()) AND r.status NOT IN ($this->dead_stat) ";
                                }else{
                                        # Check if it is a complete DOB
                                        $DOB=@formatDate2STD($suchwort,$date_format);
                                        if($DOB=='') {
                                                if(TRUE){
                                                        if($fname){
                                                                
                                                                $sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%') ";
                                                        }else{
                                                                $sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
                                                        }
                                                }else{
                                                        $sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
                                                }
                                        }else{
                                                $sql2=" WHERE serv_dt = '$DOB' ";
                                        }
                                        $sql2.=" AND r.status NOT IN ($this->dead_stat) ";
                                }
                        }
                 }

                $order_by = " ORDER BY r.serv_dt DESC, r.refno,d.service_code,s.in_lis ";
                
                if ($done){
                        $cond_done = " AND d.status='done' AND d.is_served=1 ";
                      
                        $order_by = " ORDER BY r.serv_dt DESC, r.serv_tm DESC, r.refno,d.service_code,s.in_lis ";
                }else{
                        $cond_done = " AND d.status='pending' AND d.is_served=1 ";
                       
                }
                
                //edited by VAN 02-11-2013
                //include the serial lab result (same reference no. with different order LIS no.)
                $this->sql = /*"SELECT SQL_CALC_FOUND_ROWS \n".
                                "r.refno, r.ordername AS patient_name, r.pid, r.encounter_nr, \n". 
                                "sr.nth_take, sr.service_code, name_last, name_first, name_middle, date_birth, sex, \n".
                               
                                "fn_get_labtest_request_all(r.refno) AS services, s.in_lis, \n".
                                "IF(o.lis_order_no IS NOT NULL, o.lis_order_no, tr.lis_order_no) AS lis_order_no, r.serv_dt, r.serv_tm, rs.filename,rs.date_received, \n".
                                "e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type, \n".
                                "r.is_cash, r.refno,r.is_urgent,r.serv_dt, \n".
                                "r.serv_tm,r.parent_refno,r.is_repeat, r.type_charge, d.request_flag AS charge_name \n".
                                "FROM seg_lab_serv r \n".
                                "INNER JOIN seg_lab_servdetails d ON d.refno=r.refno \n".
                                "INNER JOIN seg_walkin p ON p.pid=r.pid \n".
                                "INNER JOIN seg_lab_services s ON s.service_code=d.service_code \n".
                                "LEFT JOIN care_encounter e ON e.encounter_nr=r.encounter_nr \n".
                                "LEFT JOIN seg_hl7_lab_tracker tr ON tr.refno=r.refno \n".
                                "LEFT JOIN seg_lab_hclab_orderno o ON o.refno=r.refno \n".
                                "LEFT JOIN seg_hl7_pdffile_received rs ON rs.filename=CONCAT(r.pid,'_',IF(o.lis_order_no IS NOT NULL, o.lis_order_no, tr.lis_order_no),'.pdf') \n".
                                "LEFT JOIN seg_lab_serv_serial sr ON sr.refno=r.refno AND sr.lis_order_no=tr.lis_order_no \n".
                                " $sql2 \n".
                                "AND r.STATUS NOT IN ('deleted','hidden','inactive','void') \n".
                                "AND d.STATUS NOT IN ('deleted','hidden','inactive','void') \n".
                                "AND (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0) \n".
                                "AND ref_source = 'LB' \n".
                                " $cond_done \n".
                                "GROUP BY r.refno, o.lis_order_no \n".
                                "ORDER BY r.refno DESC";*/

                                "SELECT SQL_CALC_FOUND_ROWS \n".
                                "IF(ISNULL(gp.group_id),gp.group_id,gp.group_id) AS group_id, s.with_result, r.refno, r.ordername AS patient_name, r.pid, r.encounter_nr, \n". 
                                "s.service_code, name_last, name_first, name_middle, date_birth, sex, \n".
                                "s.name AS services,\n".
                                "r.serv_dt, r.serv_tm, \n".
                                "e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type, \n".
                                "r.is_cash, r.refno,r.is_urgent,r.serv_dt, \n".
                                "r.serv_tm,r.parent_refno,r.is_repeat, r.type_charge, d.request_flag AS charge_name \n".
                                "FROM seg_lab_serv r \n".
                                "INNER JOIN seg_lab_servdetails d ON d.refno=r.refno \n".
                                "INNER JOIN seg_walkin p ON p.pid=r.pid \n".
                                "INNER JOIN seg_lab_services s ON s.service_code=d.service_code \n".
                                "LEFT JOIN care_encounter e ON e.encounter_nr=r.encounter_nr \n".
                                "LEFT JOIN seg_lab_result_groupparams AS gp ON gp.service_code = d.service_code  \n".
                                " $sql2 \n".
                                "AND r.STATUS NOT IN ('deleted','hidden','inactive','void') \n".
                                "AND (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0) \n".
                                "AND ref_source = 'LB' \n".
                                " $cond_done \n".
                                // "GROUP BY group_id \n".
                                "ORDER BY r.refno DESC";
            if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
                if($this->rec_count=$this->res['ssl']->RecordCount()) {
                        return $this->res['ssl'];
                }else{
                    return false;
                }
            }else{
                return false;
            }
               
        }

//added by poliam 03/26/2014
function GetRoomType() {
		global $db;

		$this->sql="SELECT nr, name
					FROM $this->tb_type_room
					WHERE type != 'or'
					ORDER BY name";

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){

				return $this->result;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}
function GetRoomType2($cond="1") {
		global $db;

		$this->sql="SELECT * FROM $this->tb_other_rates WHERE $cond ORDER BY nr";

			if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){

				return $this->result;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

function deleteRoomRate($code,$service_area) {
		$this->sql="DELETE FROM $this->tb_other_rates
						WHERE service_code='$code' 
						AND source='$service_area'";
		return $this->Transact();
}

function AddOtherRate($RommRate,$service_code,$service_area){
		global $db;

		$charlist="\0..\37";
		# escape strings
		$service_code=addcslashes($service_code,$charlist);
		$service_code = strtoupper($service_code);


		$this->sql="INSERT INTO $this->tb_other_rates
									(service_code,room_type, cash_price, charge_price, source)
							 VALUES('$service_code',?,?,?,'$service_area')";
		#echo "sql = ".$this->sql."<br>";

		if ($db->Execute($this->sql,$RommRate)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}else{
			$this->error=$db->ErrorMsg();
		}
		if ($ret)	return TRUE;
		else return FALSE;
}
//ended by poliam 03/26/2014
//added by maimai 
function getTotalChargedServ($encounter_nr){
	global $db;
	
	$this->sql = "SELECT 
					  IFNULL(SUM(d.price_charge),0) AS total_charge 
					FROM
					  seg_lab_servdetails d 
					  LEFT JOIN seg_lab_serv h 
					    ON h.`refno` = d.`refno` 
					WHERE h.`encounter_nr` = ".$db->qstr($encounter_nr) 
					 ." AND d.`is_served` = 1 
					  AND h.`is_cash` = 0 ";
	if($this->result = $db->Execute($this->sql)){
		return $this->result;
	}

	return false;
}
//end mai

function getGroupServe($encounter_nr){
	global $db;
	
	$this->sql = "SELECT 
                    SQL_CALC_FOUND_ROWS IF(
                      ISNULL(gp.group_id),
                      gp.group_id,
                      gp.group_id
                    ) AS group_id,
                    r.refno,
                    r.pid,
                    s.service_code,
                    GROUP_CONCAT(s.name) AS services,
                    d.date_served AS request_date,
                    d.date_served AS date_received 
                  FROM
                    seg_lab_serv r 
                    INNER JOIN seg_lab_servdetails d 
                      ON d.refno = r.refno 
                    INNER JOIN seg_lab_services s 
                      ON s.service_code = d.service_code 
                    LEFT JOIN seg_lab_result_groupparams AS gp 
                      ON gp.service_code = d.service_code 
                  WHERE r.status NOT IN (
                      'deleted',
                      'hidden',
                      'inactive',
                      'void'
                    ) 
                    AND r.STATUS NOT IN (
                      'deleted',
                      'hidden',
                      'inactive',
                      'void'
                    ) 
                    AND d.STATUS NOT IN (
                      'deleted',
                      'hidden',
                      'inactive',
                      'void'
                    ) 
                    AND d.status = 'done' 
                    AND d.is_served = 1 
                    AND r.`encounter_nr` = ".$db->qstr($encounter_nr) 
                   ." GROUP BY gp.group_id, r.refno 
                  ORDER BY UNIX_TIMESTAMP(date_received) DESC ";

    if($this->result = $db->Execute($this->sql)){
    	return $this->result;
    }

    return false;
}
//end added by mai

function getLabCensusDaily($date,$lab_group){
	global $db;

	$date_daily = date("d", strtotime($date));
	$date_daily = $db->qstr($date_daily);
	$date = $db->qstr($date);

	if($lab_group == "notBB"){
		$lab_group = $db->qstr($lab_group);
		$cond_lab_gourp = " AND sls.`group_code` <> 'B' "; // B = BLOOD BANK
	}else if($lab_group == "all"){
		$cond_lab_gourp = "";
	}else{
		$lab_group = $db->qstr($lab_group);
		$cond_lab_gourp = " AND sls.`group_code` = $lab_group ";
	}

	$this->sql = "SELECT 
				*
				FROM
				  (SELECT 
				    sls.`name`,
				    COUNT(slsd.`service_code`) AS serv,
				  	$date_daily AS date_daily,
				  	slsg.name AS group_name
				  FROM
				    seg_lab_serv slserv 
				    INNER JOIN seg_lab_servdetails slsd 
				      ON slserv.`refno` = slsd.`refno` 
				    INNER JOIN seg_lab_services sls 
				      ON slsd.`service_code` = sls.`service_code` 
				    INNER JOIN seg_lab_service_groups slsg 
				      ON sls.`group_code` = slsg.`group_code` 
				  WHERE DATE_FORMAT(slsd.`date_served`, '%Y-%m-%d') = DATE($date)
				    AND slserv.`status` <> 'deleted'
				    $cond_lab_gourp 
				  GROUP BY slsd.`service_code` 
				  UNION
				  ALL 
				  SELECT 
				    sls.`name`,
				    0 AS serv,
				    $date_daily AS date_daily,
				    slsg.name AS group_name 
				  FROM
				    seg_lab_service_groups slsg 
				    INNER JOIN seg_lab_services sls 
				      ON slsg.`group_code` = sls.`group_code` 
				  WHERE slsg.`status` NOT IN ('deleted','hidden','inactive','void')
					$cond_lab_gourp ) t
				    ORDER BY t.name";

	$this->result = $db->Execute($this->sql);
        
        if($this->result) {
            $result = $this->result->getArray();
            return $result;
        }
     
    return false;
   
}

#added by daryl
function getserviceByGroup($group_id){
	global $db;
	$id = $db->qstr($group_id);

	$this->sql = "SELECT service.`service_code`, service.`name` 
					FROM seg_lab_services service
					WHERE service.`group_code` = $id
					ORDER BY service.`name` ASC";

	    if($result = $db->GetAll($this->sql))
            return $result;
        else
            return false;
}


#added by daryl
function getNewBornData($NB_quarter,$NB_year){
	global $db;
	
	switch ($NB_quarter) {
    case '1':
        $date_month = "AND MONTH(serv.`serv_dt`) IN (".$db->qstr(1).",".$db->qstr(2).",".$db->qstr(3).")";
        $date_month2 = "AND MONTH(e.`discharge_date`) IN (".$db->qstr(1).",".$db->qstr(2).",".$db->qstr(3).")";

        break;

    case '2':
        $date_month = "AND MONTH(serv.`serv_dt`) IN (".$db->qstr(4).",".$db->qstr(5).",".$db->qstr(6).")";
        $date_month2 = "AND MONTH(e.`discharge_date`) IN (".$db->qstr(4).",".$db->qstr(5).",".$db->qstr(6).")";
        break;

    case '3':
        $date_month = "AND MONTH(serv.`serv_dt`) IN (".$db->qstr(7).",".$db->qstr(8).",".$db->qstr(9).")";
        $date_month2 = "AND MONTH(e.`discharge_date`) IN (".$db->qstr(7).",".$db->qstr(8).",".$db->qstr(9).")";
        break;

    case '4':
        $date_month = "AND MONTH(serv.`serv_dt`) IN (".$db->qstr(10).",".$db->qstr(11).",".$db->qstr(12).")";
        $date_month2 = "AND MONTH(e.`discharge_date`) IN (".$db->qstr(10).",".$db->qstr(11).",".$db->qstr(12).")";
        break;

    default:
        $date_month = "";
        break;
}

	$this->sql = " SELECT MONTHNAME(STR_TO_DATE(month_date, '%m')) AS months, SUM(live_birth) AS live_birth, 
							SUM(neonatal) AS neonatal, SUM(inborn) AS inborn, SUM(outborn) AS outborn,  SUM(delivery) AS delivery, 
							month_date
					 FROM (
							SELECT 	MONTH(serv.`serv_dt`) month_date,
							COUNT(*) AS live_birth,
							0 AS neonatal,
							0 AS inborn,
							0 AS outborn,
							0 AS delivery

							 FROM
							care_person cp
							INNER JOIN
							care_encounter AS ce
							ON ce.`pid` = cp.`pid`
							INNER JOIN 
							seg_lab_serv AS serv
							ON serv.`pid` = ce.`pid`
							INNER JOIN seg_lab_servdetails AS serv_d
							ON serv.`refno` = serv_d.`refno`
							WHERE
							serv_d.`service_code` IN ('NB','NEWSCREE')
							AND ce.`encounter_type` = '12'
							AND ce.`is_discharged` = '1'
							 AND ce.`encounter_status` <> 'cancelled'
							 AND serv_d.`is_served` = '1'
							 AND  cp.`death_date` = '0000-00-00'
							 $date_month
							 AND YEAR(serv.`serv_dt`) = ".$db->qstr($NB_year)."
							 GROUP BY month_date 

							UNION ALL

							SELECT 
							MONTH(serv.`serv_dt`) month_date,
							0 AS live_birth,
							COUNT(*) AS neonatal,
							0 AS inborn,
							0 AS outborn,
							0 AS delivery
							 FROM
							care_person cp
							INNER JOIN
							care_encounter AS ce
							ON ce.`pid` = cp.`pid`
							INNER JOIN 
							seg_lab_serv AS serv
							ON serv.`pid` = ce.`pid`
							INNER JOIN seg_lab_servdetails AS serv_d
							ON serv.`refno` = serv_d.`refno`
							WHERE
							serv_d.`service_code` IN ('NB','NEWSCREE')
							AND ce.`is_discharged` = 1
							 AND ce.`encounter_status` <> 'cancelled'
							 AND serv_d.`is_served` = 1
							 AND  cp.`death_date` <> '0000-00-00'
							 AND DATEDIFF(cp.`death_date`, cp.`date_birth` ) <= 28
							 $date_month
							 AND YEAR(serv.`serv_dt`) = ".$db->qstr($NB_year)."
							 GROUP BY month_date
							 
							 UNION ALL
							 
							 SELECT 
							MONTH(serv.`serv_dt`) month_date,
							0 AS live_birth,
							0 AS neonatal,
							SUM(IF(ce.`encounter_type` = 12,1,0) )AS inborn,
							SUM(IF(ce.`encounter_type` <> 12,1,0) )AS outborn,
							0 AS delivery
							 FROM
							care_person cp
							INNER JOIN
							care_encounter AS ce
							ON ce.`pid` = cp.`pid`
							INNER JOIN 
							seg_lab_serv AS serv
							ON serv.`pid` = ce.`pid`
							INNER JOIN seg_lab_servdetails AS serv_d
							ON serv.`refno` = serv_d.`refno`
							WHERE
							serv_d.`service_code` IN ('NB','NEWSCREE')
							AND ce.`is_discharged` = 1
							 AND ce.`encounter_status` <> 'cancelled'
							 AND serv_d.`is_served` = 1
							 $date_month
							 AND YEAR(serv.`serv_dt`) = ".$db->qstr($NB_year)."
							 GROUP BY month_date

							 UNION ALL

					SELECT  MONTH(e.`discharge_date`) AS month_date,
							0 AS live_birth,
							0 AS neonatal,
							0 AS inborn,
							0 AS outborn,
					COUNT(*) AS delivery

					FROM
					  care_encounter_diagnosis AS ed 
					  INNER JOIN care_encounter AS e 
					    ON e.encounter_nr = ed.encounter_nr 
					  INNER JOIN care_icd10_en AS c 
					    ON c.diagnosis_code = ed.code 
					  INNER JOIN care_person AS p 
					    ON p.pid = e.pid 
					  INNER JOIN seg_icd_10_deliveries AS d 
					    ON (
					      d.icd_10 = ed.code 
					      OR d.icd_10 = 
					      (SELECT 
					        IF(
					          INSTR(ed.code, '.'),
					          SUBSTRING(ed.code, 1, 3),
					          IF(
					            INSTR(ed.code, '/'),
					            SUBSTRING(ed.code, 1, 5),
					            IF(
					              INSTR(ed.code, ','),
					              SUBSTRING(ed.code, 1, 3),
					              IF(
					                INSTR(ed.code, '-'),
					                SUBSTRING(ed.code, 1, 3),
					                ed.code
					              )
					            )
					          )
					        ))
					    ) 
					WHERE e.STATUS NOT IN (
					    'deleted',
					    'hidden',
					    'inactive',
					    'void'
					  ) 
					  AND ed.STATUS NOT IN (
					    'deleted',
					    'hidden',
					    'inactive',
					    'void'
					  ) 
					   $date_month2
					  AND YEAR(e.`discharge_date`) = ".$db->qstr($NB_year)."
					  AND ed.type_nr IN (1) 
					  AND ed.encounter_type IN (3, 4) 
					  AND IF(
					    INSTR(c.diagnosis_code, '.'),
					    SUBSTR(
					      c.diagnosis_code,
					      1,
					      IF(
					        INSTR(c.diagnosis_code, '.'),
					        INSTR(c.diagnosis_code, '.') - 1,
					        0
					      )
					    ),
					    c.diagnosis_code
					  ) REGEXP '^[[:alpha:]][[:digit:]]' 
					GROUP BY month_date 


					) AS datax group by months order by month_date ASC";

	    if($result = $db->GetAll($this->sql))
            return $result;
        else
            return false;
}


}//end of class SegLab
?>