<?php
/*
 * @package care_api
 * Class for updating `seg_radio_id`, `care_test_request_radio`, `care_test_findings_radio`,
 *		`seg_radio_serv` and `seg_radio_servdetails` tables.
 * Created: July 30, 2007-4-2006 (Bernard Klinch S. Clarito II)
 */

require('./roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');

class SegRadio extends Core{

	/**
	* Database table for the Radio ID of patient.
	* @var string
	*/
	var $tb_radio_id='seg_radio_id';
	/**
	* Database table for the Radio scheduling of services.
	* @var string
	*/
	var $tb_radio_schedule='seg_radio_schedule';
	/**
	* Database table for the Radio borrowing of films/plates.
	* @var string
	*/
	var $tb_radio_borrow='seg_radio_borrow';
	/**
	* Database table for the Radio Services data requested by patient.
	*    - includes refno, encounter, prices of Radio Services
	* @var string
	*/
	var $tb_radio_serv='seg_radio_serv';

	/*
	 * Database table for the test request radio info
	 * @var string
	 */
	var $tb_test_request_radio = 'care_test_request_radio';

	/*
	 * Database table for the test findings radio info
	 * @var string
	 */
	var $tb_test_findings_radio = 'care_test_findings_radio';

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
	/**
	* Table name for department general data
	* @var string
	*/
	var $tb_dept='care_department';
	/**
	* Table name for person (registration) data
	* @var string
	*/
	var $tb_person='care_person';
	/**
	* Table name for encounter (admission) data
	* @var string
	*/
	 var $tb_enc='care_encounter';

	var $tb_serv_discounts='seg_service_discounts';

	var $tb_discounts = 'seg_discount';

	/**
	* Database table for CT Scan
	* @var string
	*/
	var $tb_seg_radio_ct_history = 'seg_radio_ct_history';

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

#edited by VAN add request_time
	var $fld_radio_serv=array(
		"refno",
		"request_date",
		"request_time",
		"encounter_nr",
		"discountid",
		"discount",
		"pid",
		"ordername",
		"orderaddress",
		"is_cash",
		"type_charge",
		"is_urgent",
		"is_tpl",
		"comments",
		"status",
		"history",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt",
		"is_pay_full",
		"walkin_pid",
		"source_req",
		"area_type",
		"grant_type",  #added by VAN 07-16-2010 TEMPORARILY
		"is_pe",
		"is_rdu"
	);

	var $fld_radio_id=array(
		"rid",
		"pid",
		"status",
		"history",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt"
	);
/*
	var $fld_test_request_radio=array(
		"batch_nr",
		"refno",
#		"encounter_nr",   # burn commmented : August 30, 2007
#		"dept_nr",   # burn commmented : August 30, 2007
		"clinical_info",
		"service_code",
		"price_cash",
		"price_cash_orig",
		"price_charge",
		"service_date",
		"is_in_house",
		"request_doctor",
		"request_date",
		"encoder",
		"status",
		"history",
#		"priority",   # burn commmented : August 30, 2007
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt",
		"parent_batch_nr",
		"parent_refno",
		"approved_by_head",
		"remarks",
		"headID",
		"headpasswd"
	);
*/

	var $fld_test_request_radio=array(
		"batch_nr",
		"refno",
#		"encounter_nr",   # burn commmented : August 30, 2007
#		"dept_nr",   # burn commmented : August 30, 2007
		"clinical_info",
		"service_code",
		"price_cash",
		"price_cash_orig",
		"price_charge",
		"service_date",
		"is_in_house",
		"request_doctor",
		"request_date",
		"encoder",
		"status",
		"history",
#		"priority",   # burn commmented : August 30, 2007
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt"
	);


	var $fld_test_findings_radio=array(
		"batch_nr",
		"findings",
		"radio_impression",
		"findings_date",
		"doctor_in_charge",
		"encoder",
		"history",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt"
	);
/*
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
*/
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
		"create_dt",
		"is_socialized"
	);

	var $fld_radio_borrow=array(
		"batch_nr",
		"borrower_id",
		"date_borrowed",
		"time_borrowed",
		"releaser_id",
		"date_returned",
		"time_returned",
		"receiver_id",
		"remarks",
		"status",
		"history",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt"
	);

	var $fld_radio_schedule=array(
		"schedule_no",
		"batch_nr",
		"scheduled_dt",
		"remarks",
		"status",
		"history",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt"
	);

	//Added by Cherry 08-05-10
	var $fld_radio_ct_history=array(
		"refno",
		"subj_comp",
		"obj_comp",
		"assessment",
		"has_conscious",
		"did_vomit",
		"gcs",
		"rls",
		"had_surgery",
		"surgery_date",
		"surgery_proc",
		"has_blood_chem",
		"has_xray",
		"has_ultrasound",
		"has_ct_mri",
		"has_biopsy"
	);

	var $refCode;
		/*
		 * Constructor
		 * @param string primary key refCode
		 */
	function SegRadio($refCode=''){
		if(!empty($refCode)) $this->refCode=$refCode;
#		$this->setTable($this->tb_test_request_radio);
#		$this->setRefArray($this->fld_test_request_radio);
	}


		/*
		 * Sets the core object point to 'seg_radio_serv' and corresponding field names.
		 * @access private
		 */
	function _useRadioServ(){
		$this->coretable= $this->tb_radio_serv;
		$this->ref_array= $this->fld_radio_serv;
	}

	/*
	* Sets the core object point to 'seg_radio_ct_history' and corresponding field names.\
	* @access private
	*/
	#Added by Cherry 08-05-10
	function _useCTHistory(){
		$this->coretable = $this->tb_seg_radio_ct_history;
		$this->ref_array = $this->fld_radio_ct_history;
	}

		/*
		 * Sets the core object point to 'care_test_request_radio' and corresponding field names.
		 * @access private
		 */
	function _useRadioID(){
		$this->coretable= $this->tb_radio_id;
		$this->ref_array= $this->fld_radio_id;
	}

		/*
		 * Sets the core object point to 'care_test_request_radio' and corresponding field names.
		 * @access private
		 */
	function _useRequestRadio(){
		$this->coretable= $this->tb_test_request_radio;
		$this->ref_array= $this->fld_test_request_radio;
	}

		/*
		 * Sets the core object point to 'care_test_findings_radio' and corresponding field names.
		 * @access private
		 */
	function _useFindingRadio(){
		$this->coretable= $this->tb_test_findings_radio;
		$this->ref_array= $this->fld_test_findings_radio;
	}
		/**
		* Sets the core object to point to 'seg_radio_borrow' and corresponding field names.
		*/
	function _useRadioBorrow(){
		$this->coretable=$this->tb_radio_borrow;
		$this->ref_array=$this->fld_radio_borrow;
	}

		/**
		* Sets the core object to point to 'seg_radio_schedule' and corresponding field names.
		*/
	function _useRadioSchedule(){
		$this->coretable=$this->tb_radio_schedule;
		$this->ref_array=$this->fld_radio_schedule;
	}

		/**
		* Sets the core object to point to 'seg_radio_service_groups' and corresponding field names.
		*/
	function useRadioServiceGroups(){
		$this->coretable=$this->tb_radio_service_groups;
		$this->ref_array=$this->fld_radio_service_groups;
	}
		/**
		* Sets the core object to point to 'seg_radio_services' and corresponding field names.
		*/
	function useRadioServices(){
		$this->coretable=$this->tb_radio_services;
		$this->ref_array=$this->fld_radio_services;
	}

	/*
	*  returns the refCode set in the Constructor
	*	@burn added: October 23, 2007
	*/
	function getRefCode(){
		if ($this->refCode)
			return $this->refCode;
		return FALSE;
	}


		/**
		* Gets a new reference number (refno).
		*
		* A reference number must be passed as parameter. The returned number will the highest number above the reference number PLUS 1.
		* @param int Reference PID number
		* @return integer
		*	burn added: August 29, 2007
		*/
	function getNewRefNo($ref_nr){
		global $db;

		$temp_ref_nr = date('Y')."%";   # NOTE : XXXX?????? would be the format of Reference number
		$row=array();
		$this->sql="SELECT refno FROM $this->tb_radio_serv WHERE refno LIKE '$temp_ref_nr' ORDER BY refno DESC";
		if($this->res['gnpn']=$db->SelectLimit($this->sql,1)){
			if($this->res['gnpn']->RecordCount()){
				$row=$this->res['gnpn']->FetchRow();
				return $row['refno']+1;
			}else{/*echo $this->sql.'no xount';*/return $ref_nr;}
		}else{/*echo $this->sql.'no sql';*/return $ref_nr;}
	}

		/**
		* Gets a new Borrow number (borrow_nr).
		*
		* A reference number must be passed as parameter.
		* The returned number will the highest number above the reference number PLUS 1.
		* @param int borrow number
				i.e. the current year in 4-digit format + "000001"
		* @return integer
		* @created : burn, July 31, 2007
		*/
	function getNewBorrowNr($ref_nr){
		global $db;

		$temp_ref_nr = date('Y')."%";   # NOTE : XXXX?????? would be the format of Radiology ID number
		$row=array();
		$this->sql="SELECT borrow_nr FROM $this->tb_radio_borrow WHERE borrow_nr LIKE '$temp_ref_nr' ORDER BY borrow_nr DESC";

#echo "class_radiology.php : getNewBorrowNr : this->sql = '".$this->sql."' <br> \n";

		if($this->res['gnbn']=$db->SelectLimit($this->sql,1)){
			if($this->res['gnbn']->RecordCount()){
				$row=$this->res['gnbn']->FetchRow();
				return $row['borrow_nr']+1;
			}else{/*echo $this->sql.'no count';*/return $ref_nr;}
		}else{/*echo $this->sql.'no sql';*/return $ref_nr;}
	}# end of function getNewBorrowNr

		/**
		* Returns/Checks if RID number exists in the database.
		* @access public
		* @param int PID number
		* @return int RID or boolean
		* @created : burn, July 31, 2007
		*/
	function RIDExists($pid=0){
		global $db;

		if (!$pid)
			return FALSE;

		$this->sql="SELECT rid FROM $this->tb_radio_id WHERE pid='$pid'";
#		$this->sql="SELECT rid FROM $this->tb_radio_id";
		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()){
				$row=$this->result->FetchRow();
				return $row['rid'];
			} else { return FALSE; }
		} else { return FALSE; }
	}

		/**
		* Gets a new Radiology Patient ID number (a.k.a. RID).
		*
		* A reference number must be passed as parameter.
		* The returned number will the highest number above the reference number PLUS 1.
		* @param int Reference RID number
				i.e. the current year in 4-digit format + "000001"
		* @return integer
		* @created : burn, July 31, 2007
		*/
#	function getNewRID($ref_nr=date('Y')."000001"){
	function getNewRID($ref_nr){
		global $db;

		$temp_ref_nr = date('Y')."%";   # NOTE : XXXX?????? would be the format of Radiology ID number
		$row=array();
		$this->sql="SELECT rid FROM $this->tb_radio_id WHERE rid LIKE '$temp_ref_nr' ORDER BY rid DESC";

#echo "class_radiology.php :getNewRID : this->sql = '".$this->sql."' <br> \n";

		if($this->res['gnr']=$db->SelectLimit($this->sql,1)){
			if($this->res['gnr']->RecordCount()){
				$row=$this->res['gnr']->FetchRow();
				return $row['rid']+1;
			}else{/*echo $this->sql.'no count';*/return $ref_nr;}
		}else{/*echo $this->sql.'no sql';*/return $ref_nr;}
	}

		/**
		* Inserts the new RID into the 'seg_radio_id' table.
		* @access public
		* @param int PID number
		* @return newly generated RID or boolean
		* @created : burn, July 31, 2007; modified: October 16, 2007
		* @note : RID will be created using  DB trigger
		*/
	function createNewRID($pid=''){
		global $db,$HTTP_SESSION_VARS;

		if(empty($pid) || (!$pid))
			return FALSE;

		if ($rid = $this->RIDExists($pid))
			return $rid;

		$this->_useRadioID();
		$index = "pid, history, create_id, create_dt";
		$values = "'$pid', ".
					$this->ConcatHistory("Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n")
					.", '".$HTTP_SESSION_VARS['sess_user_name']."', NOW()";
		$this->sql="INSERT INTO $this->coretable ($index) VALUES ($values)";

#echo "class_radiology.php : createNewRID : this->sql = '".$this->sql."' <br> \n";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				return $this->RIDExists($pid);
#				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end of function createNewRID

		/**
		* Inserts the (a) granted Radiology request into 'seg_granted_request' table.
		* @access public
		* @param Array Data to by reference
		* @return boolean
		* @created : burn, October 25, 2007
		*/
	function cleargrantRadioRequest($refno) {
				global $db;
				$refno = $db->qstr($refno);
				$this->sql = "DELETE FROM seg_granted_request WHERE ref_no=$refno AND ref_source='RD'";

				return $this->Transact();
	}

	function grantRadioRequest($data){
		global $db;

		extract($data);
		$arrayItems = array();

		foreach ($items as $key => $value){
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

		$this->cleargrantRadioRequest($refno);

		$index = "ref_no, ref_source, service_code";
		$values = "$refno, 'RD', ?";   # NOTE: 'RD'=radiology

		$this->sql="INSERT INTO seg_granted_request ($index) VALUES ($values)";
		#echo "grant = ".$this->sql;
		if ($db->Execute($this->sql,$arrayItems)) {
			if ($db->Affected_Rows()) {
				$this->updateCharityRadioRequest($refno,$arrayItems);
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }

	}# end of function grantLabRequest


		/**
		* Checks if an encounter_nr has exisiting radiology request(s) based on ENCOUNTER NUMBER
		* @access public
		* @param int encounter number
		* @return boolean
		* @created : burn, August 8, 2007
		*/
	function encHasRadioRequest($enc){
		global $db;

		if(empty($enc) || (!$enc))
			return FALSE;

		$this->_useRequestRadio();

		$this->sql="SELECT * FROM $this->coretable WHERE encounter_nr='$enc' AND status NOT IN ($this->dead_stat)";

#echo "class_radiology.php : encHasRadioRequest : this->sql = '".$this->sql."' <br> \n";

		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()){
				return TRUE;
			} else { return FALSE; }
		} else { return FALSE; }
	}

		/**
		* Checks if a batch_nr has an existing radiology finding(s) entry in table 'care_test_findings_radio'
		* @access public
		* @param int encounter number
		* @return boolean
		* @created : burn, August 8, 2007
		*/
	function batchNrHasRadioFindings($batch_nr){
		global $db;

		$this->_useFindingRadio();

		$this->sql="SELECT * FROM $this->coretable WHERE batch_nr='$batch_nr'";
#		$this->sql="SELECT * FROM $this->coretable WHERE batch_nr=$batch_nr AND status NOT IN ($this->dead_stat)";

#echo "class_radiology.php : encHasRadioRequest : this->sql = '".$this->sql."' <br> \n";

		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()){
				return TRUE;
			} else { return FALSE; }
		} else { return FALSE; }
	}

		/*
		* Gets the basic radiology request/findings info based on BATCH NUMBER
		* @param int batch number
		* @param boolean limit, TRUE for one row returned value, FALSE for all records
		* @return array of radiology request/findings info or boolean
		* @created/modified : burn, August 6, 2007; burn, November 14, 2007
		*/
	function getAllRadioInfoByBatch($batch_nr='',$limit=TRUE){
		global $db;

		if(empty($batch_nr) || (!$batch_nr)){
			return FALSE;
		}

		$this->sql="SELECT r_serv.pid, sri.rid, r_serv.encounter_nr, r_serv.refno, r_serv.is_urgent,
					enc.encounter_type, enc.current_ward_nr, enc.current_room_nr, enc.in_ward,
					cw.ward_id, cw.name AS ward_name,
                    cw.accomodation_type,
					(SELECT dept.name_formal
						FROM care_personell_assignment AS cpa
							LEFT JOIN care_department AS dept ON cpa.location_nr = dept.nr
						WHERE cpa.personell_nr=r_request.request_doctor LIMIT 1) AS request_dept_name,
					IF((ISNULL(r_request.is_in_house) || r_request.is_in_house='0'),
						r_request.request_doctor,
						IF(STRCMP(r_request.request_doctor,CAST(r_request.request_doctor AS UNSIGNED INTEGER)),
							r_request.request_doctor, fn_get_personell_name(r_request.request_doctor)) ) AS request_doctor_name,
					r_request.request_doctor, r_request.refno, r_request.batch_nr, r_request.clinical_info, r_request.service_code,
					r_request.service_date, r_request.is_in_house, r_request.request_date, r_request.status, r_request.encoder AS request_encoder,
					r_request.price_cash AS price_net, r_request.price_cash_orig, r_request.price_charge,
					r_findings.findings, r_findings.radio_impression, r_findings.findings_date, r_findings.doctor_in_charge,
					r_findings.result_status, r_findings.encoder AS findings_encoder,
					r_services.name AS service_name,
					r_serv_group.group_code AS group_code, r_serv_group.name AS group_name,
					r_serv_group.other_name, r_serv_group.department_nr AS service_dept_nr,
					dept.name_formal AS service_dept_name, IF((r_serv.is_cash=0 && request_flag IS NULL), 'Charge', r_request.request_flag) AS request_flag
				FROM seg_radio_serv AS r_serv
					INNER JOIN care_test_request_radio AS r_request ON r_request.refno=r_serv.refno
						LEFT JOIN care_test_findings_radio AS r_findings ON r_request.batch_nr = r_findings.batch_nr
							INNER JOIN seg_radio_services AS r_services ON r_request.service_code = r_services.service_code
								INNER JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
									INNER JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
							LEFT JOIN care_encounter AS enc ON enc.encounter_nr=r_serv.encounter_nr
						LEFT JOIN care_ward AS cw ON cw.nr = enc.current_ward_nr
					INNER JOIN seg_radio_id AS sri ON sri.pid=r_serv.pid
				WHERE r_request.batch_nr IN ('$batch_nr')
					AND r_request.status NOT IN ($this->dead_stat)";
#echo "class_radiology.php : getAllRadioInfoByBatch : this->sql = '".$this->sql."' <br> \n";

		if ($buf=$db->Execute($this->sql)){
			if($this->count=$buf->RecordCount()) {
				if ($limit)
					return $buf->FetchRow();
				else
					return $buf;
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getAllRadioInfoByBatch

		/*
		* Gets the BASIC radiology request service info based on REFERENCE NUMBER
		* @param int reference number
		* @return an array of radiology request service info or boolean
		* @created : burn, August 31, 2007
		*/
	function getBasicRadioServiceInfo($ref_nr=''){
		global $db;

		if(empty($ref_nr) || (!$ref_nr)){
			return FALSE;
		}
		#edited by VAN 01-06-10
		$this->sql="SELECT e.current_ward_nr,e.current_room_nr,e.current_dept_nr,e.is_medico,e.encounter_type,
							IF ((request_flag IS NOT NULL),1,0) AS hasPaid,
							sri.rid,
							r_serv.*,
							p.name_last, p.name_first, p.date_birth, p.sex, p.pid, p.senior_ID,
							r.parent_batch_nr, r.parent_refno, r.approved_by_head, r.remarks
						FROM seg_radio_serv AS r_serv
							INNER JOIN care_test_request_radio AS r ON r.refno = r_serv.refno
							INNER JOIN care_person AS p ON p.pid = r_serv.pid
							INNER JOIN seg_radio_id AS sri ON sri.pid = r_serv.pid
							LEFT JOIN care_encounter AS e ON e.encounter_nr=r_serv.encounter_nr AND e.pid=r_serv.pid

						WHERE r_serv.refno='$ref_nr'
							AND r_serv.status NOT IN ($this->dead_stat) LIMIT 1";

#echo "class_radiology.php : getBasicRadioServiceInfo: this->sql = '".$this->sql."' <br> \n";
#exit();
		if ($buf=$db->Execute($this->sql)){
			if($this->count=$buf->RecordCount()) {
				return $buf->FetchRow();
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getBasicRadioServiceInfo

#added by daryl
function getBasicRadioServiceInfo_walkin($ref_nr=''){
		global $db;

		if(empty($ref_nr) || (!$ref_nr)){
			return FALSE;
		}
		#edited by VAN 01-06-10
		$this->sql="SELECT e.current_ward_nr,e.current_room_nr,e.current_dept_nr,e.is_medico,e.encounter_type,
							IF ((request_flag IS NOT NULL),1,0) AS hasPaid,
							sri.rid,
							r_serv.*,
							p.name_last, p.name_first, p.date_birth,  p.pid, 
							r.parent_batch_nr, r.parent_refno, r.approved_by_head, r.remarks
						FROM seg_radio_serv AS r_serv
							INNER JOIN care_test_request_radio AS r ON r.refno = r_serv.refno
							INNER JOIN seg_walkin AS p ON p.pid = r_serv.pid
							INNER JOIN seg_radio_id AS sri ON sri.pid = r_serv.pid
							LEFT JOIN care_encounter AS e ON e.encounter_nr=r_serv.encounter_nr AND e.pid=r_serv.pid
						WHERE r_serv.refno='$ref_nr'
							AND r_serv.status NOT IN ($this->dead_stat) LIMIT 1";

		if ($buf=$db->Execute($this->sql)){
			if($this->count=$buf->RecordCount()) {
				return $buf->FetchRow();
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getBasicRadioServiceInfo



		/*
		* Gets the list of requests [service code] based on REFERENCE NUMBER
		* @param int reference number
		* @return an array of radiology request service code ONLY or boolean
		* @created : burn, September 5, 2007
		*/
	function getListedRequestsByRefNo($ref_nr='',$cond=''){
		global $db;

		if(empty($ref_nr) || (!$ref_nr)){
			return FALSE;
		}
		if(empty($cond) || (!$cond)){
			$cond = "AND status NOT IN ($this->dead_stat)";
		}

		$this->sql="SELECT service_code
						FROM care_test_request_radio
						WHERE refno='$ref_nr'
							$cond";

#echo "class_radiology.php : getListedRequestsByRefNo: this->sql = '".$this->sql."' <br> \n";
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

		/*
		* Gets the radiology request service info based on REFERENCE NUMBER
		* @param int reference number
		* @return recordset of radiology request service info or boolean
		* @created/modified : burn, August 29, 2007; December 12, 2007
		*/
	#function getAllRadioInfoByRefNo($ref_nr='',$sub_dept_nr=''){
		#edited by daryl
	function getAllRadioInfoByRefNo($ref_nr='', $batch_nr='', $fromSS=0, $discount=0, $discountid='', $sub_dept_nr='', $walkin=''){
		global $db;

		#echo "<br>refno, batch, dept = ".$ref_nr.", ".$batch_nr." , ".$sub_dept_nr;

		if(empty($ref_nr) || (!$ref_nr)){
			return FALSE;
		}
		if(!empty($sub_dept_nr) && ($sub_dept_nr)){
			$WHERE_SQL = " AND r_serv_group.department_nr = '".$sub_dept_nr."'";
		}

		# added by VAN 01-11-08
		if(!empty($batch_nr) && ($batch_nr)){
			$WHERE_SQL2 = " AND r.batch_nr='".$batch_nr."'";
		}
#added by daryl
		if (($walkin == 1) || ($walkin != "")) {
			$sql_walk = "";
				$where_sql_walk = "INNER JOIN seg_walkin AS p ON p.pid = r_serv.pid";
		}else{
				$where_sql_walk = "INNER JOIN care_person AS p ON p.pid = r_serv.pid";
				$sql_walk = ", p.sex";
		}
		# added by VAN 01-15-08 ---- r.parent_batch_nr, r.parent_refno, r.approved_by_head, r.remarks line 760
		$this->sql="SELECT
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
							IF ((request_flag IS NOT NULL),1,0) AS hasPaid,
							r.request_flag,
							r_serv.refno, r_serv.request_date, r_serv.encounter_nr,
							r_serv.discountid, r_serv.discount,
							r_serv.pid, r_serv.ordername, r_serv.orderaddress,
							r_serv.is_cash, r_serv.is_urgent, r_serv.comments,
							r_serv.status, r_serv.history, r_serv.create_dt,
							r.batch_nr, r.clinical_info, r.service_code,
							r.price_cash, r.price_cash_orig, r.price_charge, r.price_alter, r.service_date,
							r.is_in_house, r.request_doctor, r.request_date, 1 AS quantity,
							r.parent_batch_nr, r.parent_refno, r.approved_by_head, r.remarks,
							r.status AS request_status,
							IF((ISNULL(r.is_in_house) ||  r.is_in_house='0'),
								r.request_doctor,
								IF(STRCMP(r.request_doctor,CAST(r.request_doctor AS UNSIGNED INTEGER)),
									r.request_doctor,
									fn_get_personell_name(r.request_doctor))
							) AS request_doctor_name,
							r_services.service_code, r_services.name, r_services.is_socialized,
							r_serv_group.group_code ,
							r_serv_group.department_nr AS sub_dept_nr,
							dept.id AS sub_dept_id, dept.name_formal AS sub_dept_name, dept.name_short AS dept_short_name,
							p.name_first, p.name_middle, p.name_last, p.date_birth $sql_walk
							FROM seg_radio_serv AS r_serv
							INNER JOIN care_test_request_radio AS r ON r.refno=r_serv.refno
							$where_sql_walk#edited by daryl
							INNER JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
							LEFT JOIN seg_service_discounts AS sd ON
								sd.service_code=r_services.service_code AND sd.discountid='$discountid' AND service_area='RD'
							INNER JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
							INNER JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
						WHERE r_serv.refno='$ref_nr'
							AND r_serv.status NOT IN ($this->dead_stat)
							AND r.status NOT IN ($this->dead_stat)
							$WHERE_SQL
							$WHERE_SQL2
						GROUP BY r.service_code
						ORDER BY create_dt ASC ";

#echo "class_radiology.php : getAllRadioInfoByRefNo: this->sql = '".$this->sql."' <br> \n";
#exit();
		if ($buf=$db->Execute($this->sql)){
			if($buf->RecordCount()) {
				return $buf;
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getAllRadioInfoByRefNo

		/*
		* Gets the borrowing history of radiology record of a radiology patient based on Batch number
		* @param int Batch Number
		* @return recordset, borrowing history of radiology record; or boolean
		* @created : burn, November 6, 2007
		*/
#	function getAllRadioPatientRecords($pid=''){
	function getRadioPatientRecordBorrowInfo($batch_nr=''){
		global $db;

		if(empty($batch_nr) || (!$batch_nr)){
			return FALSE;
		}
		/*
		$this->sql="SELECT
							IF(STRCMP(srb.borrower_id,CAST(srb.borrower_id AS UNSIGNED INTEGER)),
								srb.borrower_id, fn_get_personell_name(srb.borrower_id)) AS borrower_name,
							IF(STRCMP(srb.releaser_id,CAST(srb.releaser_id AS UNSIGNED INTEGER)),
								srb.releaser_id, fn_get_personell_name(srb.releaser_id)) AS releaser_name,
							IF(STRCMP(srb.receiver_id,CAST(srb.receiver_id AS UNSIGNED INTEGER)),
								srb.receiver_id, fn_get_personell_name(srb.receiver_id)) AS receiver_name,
							srb.*
						FROM seg_radio_borrow AS srb
						WHERE srb.status NOT IN ($this->dead_stat)
							AND (srb.batch_nr=$batch_nr OR srb.batch_nr='$batch_nr')
						ORDER BY srb.date_borrowed DESC, srb.time_borrowed DESC ";
		*/
		#edited by VAN 07-10-08
		$this->sql="SELECT  IF(r_serv.is_cash=1,r_services.price_cash,r_services.price_charge) AS price,
							IF(STRCMP(srb.borrower_id,CAST(srb.borrower_id AS UNSIGNED INTEGER)),
								srb.borrower_id, fn_get_personell_name(srb.borrower_id)) AS borrower_name,
							IF(STRCMP(srb.releaser_id,CAST(srb.releaser_id AS UNSIGNED INTEGER)),
								srb.releaser_id, fn_get_personell_name(srb.releaser_id)) AS releaser_name,
							IF(STRCMP(srb.receiver_id,CAST(srb.receiver_id AS UNSIGNED INTEGER)),
								srb.receiver_id, fn_get_personell_name(srb.receiver_id)) AS receiver_name,
							srb.*
						FROM seg_radio_borrow AS srb
						LEFT JOIN care_test_request_radio AS r ON r.batch_nr=srb.batch_nr
						LEFT JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
						LEFT JOIN seg_radio_serv AS r_serv ON r.refno=r_serv.refno
						WHERE srb.status NOT IN ($this->dead_stat)
							AND (srb.batch_nr='$batch_nr')
						ORDER BY srb.date_borrowed DESC, srb.time_borrowed DESC ";
/*
AND r_serv.status NOT IN ('deleted','hidden','inactive','void')
*/
#echo "class_radiology.php : getRadioPatientRecordBorrowInfo: this->sql = '".$this->sql."' <br> \n";
#exit();
		if ($buf=$db->Execute($this->sql)){
			if($buf->RecordCount()) {
				return $buf;
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getRadioPatientRecordBorrowInfo

		/*
		* Gets the basic radiology request/findings info based on ENCOUNTER NUMBER
		* @param int encounter number
		* @return recordset of radiology request/findings info or boolean
		* @created : burn, August 7, 2007
		* @to_be_deleted : burn, September 5, 2007
		*/
	function getAllRadioInfoByEncounter($enc_nr=''){
		global $db;

		if(empty($enc_nr) || (!$enc_nr)){
			return FALSE;
		}

		$this->sql="SELECT enc.pid,
					IF((ISNULL(r_request.is_in_house) ||  r_request.is_in_house='0'),
						r_request.request_doctor,
						IF(STRCMP(r_request.request_doctor,CAST(r_request.request_doctor AS UNSIGNED INTEGER)),
							r_request.request_doctor,
							fn_get_personell_name(r_request.request_doctor))
					) AS request_doctor_name,
					r_request.request_doctor,
					r_request.batch_nr, r_request.encounter_nr, r_request.clinical_info,
					r_request.service_code, r_request.service_date,	r_request.is_in_house,
					r_request.request_date, r_request.status, r_request.priority,
					r_request.encoder AS request_encoder,
					r_findings.findings, r_findings.radio_impression, r_findings.findings_date,
					r_findings.doctor_in_charge, r_findings.encoder AS findings_encoder,
					r_services.name AS service_name, r_services.price_cash, r_services.price_charge,
					r_serv_group.group_code AS group_code, r_serv_group.name AS group_name, r_serv_group.other_name,
					r_serv_group.department_nr AS service_dept_nr, dept.name_formal AS service_dept_name
				FROM care_test_request_radio AS r_request
					LEFT JOIN care_encounter AS enc ON enc.encounter_nr = r_request.encounter_nr
					LEFT JOIN care_test_findings_radio AS r_findings ON r_request.batch_nr = r_findings.batch_nr
					LEFT JOIN seg_radio_services AS r_services ON r_request.service_code = r_services.service_code
						LEFT JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
							LEFT JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
				WHERE r_request.encounter_nr='$enc_nr' AND r_request.status NOT IN ($this->dead_stat)";

#echo "class_radiology.php : getAllRadioInfoByEncounter: this->sql = '".$this->sql."' <br> \n";

		if ($buf=$db->Execute($this->sql)){
			if($buf->RecordCount()) {
				return $buf;
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getAllRadioInfoByEncounter

		/**
		* Updates the status of a radio request in table 'care_test_request_radio'.
		* @access public
		* @param int, batch_nr
		* @param string, new status
		* @return boolean
		* @created : burn, July 31, 2007
		*/
	function updateRadioRequestStatus($batch_nr='', $new_status='', $date_served='0000-00-00'){
		global $db,$HTTP_SESSION_VARS;

		if(empty($batch_nr) || (!$batch_nr))
			return FALSE;
		if(empty($new_status) || (!$new_status))
			return FALSE;

		$history = $this->ConcatHistory("Update status [$new_status] ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
# pending, referral, done
		$this->sql="UPDATE $this->tb_test_request_radio ".
						" SET status='".$new_status."',service_date='".$date_served."' ,history=".$history.", ".
						"		modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
						" WHERE batch_nr = $batch_nr";

#echo "class_radiology.php : updateRadioRequestStatus : this->sql = '".$this->sql."' <br> \n";

		if ($buf=$db->Execute($this->sql)){
			if($db->Affected_Rows()) {
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }
	}# end of function updateRadioRequestStatus


		/**
		* Updates the status of a radio request in table 'care_test_request_radio'.
		* @access public
		* @param int, refno
		* @param array, array of service code
		* @param string, new status
		* @return boolean
		* @created : burn, September 5, 2007
		*/
#	function updateRadioRequestStatusByRefNoServCode($refno='',$serv_code='', $new_status=''){
	function updateRadioRequestStatusByRefNoServCode($data,$arrayItems, $new_status=''){
		global $db,$HTTP_SESSION_VARS;
/*
echo "<br>class_radiology.php : updateRadioRequestStatusByRefNoServCode : data = '".$data."' <br> \n";
echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : is_object(data) = '".is_object($data)."' <br> \n";
echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : is_array(data) = '".is_array($data)."' <br> \n";
echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : data : "; print_r($data); echo " <br> \n";
*/

		if(!is_array($data) || (!$data))
			return FALSE;
		if(!is_array($arrayItems) || (!$arrayItems))
			return FALSE;

		$this->_useRequestRadio();
        extract($data);
        
		$refno = $data['refno'];

			$this->data_array=$data;
			unset($this->data_array['create_id']);
			unset($this->data_array['create_dt']);
			unset($this->data_array['modify_dt']);
			unset($this->data_array['status']);
			unset($this->data_array['service_code']);
			unset($this->data_array['service_code']);
			unset($this->data_array['clinical_info']);
			unset($this->data_array['request_doctor']);
			unset($this->data_array['is_in_house']);
			unset($this->data_array['pnet']);
			unset($this->data_array['pcash']);
			unset($this->data_array['pcharge']);
			unset($this->data_array['edit_tot_']);

			#------added by VAN 01-14-08
			unset($this->data_array['parent_batch_nr']);
			unset($this->data_array['parent_refno']);
			unset($this->data_array['approved_by_head']);
			unset($this->data_array['remarks']);

			unset($this->data_array['headID']);
			unset($this->data_array['headpasswd']);
			#---------------------------------------

			$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
/*
echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : new_status='$new_status' <br> \n";
echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : 1 this->data_array['status'] = '".$this->data_array['status']."' <br> \n";
echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : 1 isset(this->data_array['status']) = '".isset($this->data_array['status'])."' <br> \n";
*/
		if (!empty($new_status)){
#echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : status NEEDS to be change <br> \n";
				# if the status needs to be change
			$history = $this->ConcatHistory("Update status [$new_status] ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
			$this->data_array['history'] = $history;
			#commented by VAN 03-18-08
			$this->data_array['status'] = $new_status;
			#echo "<br>status = ".$this->data_array['status'];
		}else{
#echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : there is NO NEED for change of status <br> \n";
			$this->data_array['history'] = $this->ConcatHistory("Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		}
/*
echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : this->data_array = '".$this->data_array."' <br> \n";
echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : is_object(this->data_array) = '".is_object($this->data_array)."' <br> \n";
echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : this->data_array : "; print_r($this->data_array); echo " <br> \n";
echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : 2 this->data_array['status'] = '".$this->data_array['status']."' <br> \n";
echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : 2 isset(this->data_array['status']) = '".isset($this->data_array['status'])."' <br> \n";
*/
		if (empty($new_status) || ($new_status=='pending')){
				# there is NO NEED for change of status
			if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
				else $concatfx='concat';

				#	Only the keys of data to be updated must be present in the passed array.
			$x='';
			$v='';
			$this->buffer_array = array();
			while(list($x,$v)=each($this->ref_array)) {
	#			if(isset($this->data_array[$v])&&(trim($this->data_array[$v])!='')) {
				if (isset($this->data_array[$v]))
					$this->buffer_array[$v]=trim($this->data_array[$v]);
	#			}
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
#echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : elems = '".$elems."' <br> \n";
		}# end of if-stmt 'if (empty($new_status) || ($new_status=='pending'))'

# pending, referral, done
		# commented by VAN 01-14-08
		/*
		if (empty($new_status) || ($new_status=='pending')){
			$this->sql="UPDATE $this->tb_test_request_radio ".
							" SET $elems, ".
							" 		clinical_info=?, request_doctor=?, is_in_house=?, ".
							" 		price_cash=?, price_cash_orig=?, price_charge=?, modify_dt=NOW() ".
							" WHERE refno = '$refno' AND service_code = ?";
		}else{
			$this->sql="UPDATE $this->tb_test_request_radio ".
							" SET status='".$new_status."', history=".$history.", ".
							"		encoder='".$HTTP_SESSION_VARS['sess_user_name']."',".
							"		modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
							" WHERE refno = '$refno' AND service_code = ?";
		}
		*/
		#echo "<br>ss = ".$this->data_array['request_flag']."<br>";
		#echo "<br>1batch, head, remarks = ".$data['parent_batch_nr']." - ".$data['approved_by_head']." - ".$data['remarks'];
		if (empty($new_status) || ($new_status=='pending')){

			if ($this->data_array['request_flag']){
				 $add_qry = "request_flag = '".$this->data_array['request_flag']."', ";
			}else{
				$add_qry = "request_flag = NULL, ";
			}

			$this->sql="UPDATE $this->tb_test_request_radio ".
							" SET $elems, ".
							" 		clinical_info=?, request_doctor=?, is_in_house=?, ".
							" 		price_cash=?, price_cash_orig=?, price_charge=?, price_alter=?, ".
							" 		parent_batch_nr = ?, parent_refno=?, approved_by_head=?, remarks=?, ".
							"     headID = ?, headpasswd = ?, ".$add_qry.
							"     modify_dt=NOW() ".
							" WHERE refno = '$refno' AND service_code = ?";
			/*
			$this->sql="UPDATE $this->tb_test_request_radio ".
							" SET $elems, ".
							" 		clinical_info=?, request_doctor=?, is_in_house=?, ".
							" 		price_cash=?, price_cash_orig=?, price_charge=?, ".
							" 		parent_batch_nr = ?, parent_refno=?, approved_by_head=?, remarks=?, ".
							"     modify_dt=NOW() ".
							" WHERE refno = '$refno' AND service_code = ?";
			*/
		}else{
			#echo "sulod";

			$this->sql="UPDATE $this->tb_test_request_radio ".
							" SET status='".$new_status."', history=".$history.", ".
							"		encoder='".$HTTP_SESSION_VARS['sess_user_name']."',".
							"		modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW(), ".
							" 		parent_batch_nr = '".$data['parent_batch_nr']."', parent_refno = '".$data['parent_refno']."', ".
							"     approved_by_head='".$data['approved_by_head']."', remarks='".$data['remarks']."', ".
							"     headID='".$data['headID']."', headpasswd='".$data['headpasswd']."' ".
							" WHERE refno = '$refno' AND service_code = ?";
			/*
			$this->sql="UPDATE $this->tb_test_request_radio ".
							" SET status='".$new_status."', history=".$history.", ".
							"		encoder='".$HTTP_SESSION_VARS['sess_user_name']."',".
							"		modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW(), ".
							" 		parent_batch_nr = '".$data['parent_batch_nr']."', parent_refno = '".$data['parent_refno']."', ".
							"     approved_by_head='".$data['approved_by_head']."', remarks='".$data['remarks']."' ".
							" WHERE refno = '$refno' AND service_code = ?";
			*/
		}
/*
echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : arrayItems = '".$arrayItems."' <br> \n";
echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : arrayItems : "; print_r($arrayItems); echo " <br> \n";
*/
#echo "<br>class_radiology.php : updateRadioRequestStatusByRefNoServCode : this->sql = '".$this->sql."' <br> \n";

# $db->debug = true;

		if ($buf=$db->Execute($this->sql,$arrayItems)){
			if($db->Affected_Rows()) {
                #automatic apply the coverage upon saving the request
                #will clarify to the user when should the phic coverage will be applied
                # Handle applied coverage for PHIC and other benefits
                #$this->apply_coverage($refno, $arrayItemsList);
                
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }
#echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : FALSE 1 <br> \n";
#echo "<br>class_radiology.php : updateRadioRequestStatusByRefNoServCode : db->ErrorMsg()='$db->ErrorMsg()'<br> \n";

	}# end of function updateRadioRequestStatusByRefNoServCode

		/**
		* Deletes logically a radio request in table 'seg_radio_serv' and 'care_test_request_radio'.
		* @access public
		* @param int, refno
		* @return boolean
		* @created : burn, September 10, 2007
		*/
	function deleteRefNo($refno=''){
		global $db,$HTTP_SESSION_VARS, $_SESSION;

		if(empty($refno) || (!$refno))
			return FALSE;

		$this->_useRadioServ();
		#$history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
        $history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n");
		$encoder = $_SESSION['sess_temp_userid'];
			# logical deletion in table 'seg_radio_serv'
		$this->sql="UPDATE $this->coretable ".
						" SET status='deleted', history=".$history.", ".
						"		modify_id='$encoder', modify_dt=NOW() ".
						" WHERE refno = '$refno'";

#echo "class_radiology.php : deleteRefNo for table 'seg_radio_serv' : this->sql = '".$this->sql."' <br> \n";

		if ($buf=$db->Execute($this->sql)){
			if($db->Affected_Rows()) {
					# logical deletion in table 'care_test_request_radio'
				$this->sql="UPDATE $this->tb_test_request_radio ".
								" SET status='deleted', history=".$history.", ".
								"		modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
								" WHERE refno = '$refno'";
#echo "class_radiology.php : deleteRefNo for table 'care_test_request_radio' : this->sql = '".$this->sql."' <br> \n";
				if ($buf=$db->Execute($this->sql)){
					return TRUE;
				}
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }
	}# end of function deleteRefNo


		/**
		* Updates the date of service of a radio request in table 'care_test_request_radio'.
		* @access public
		* @param int, batch_nr
		* @param string, new date of service in (yyyy-mm-dd format)
		* @return boolean
		* @created : burn, August 2, 2007
		*/
	function updateRadioRequestServiceDate($batch_nr='', $new_service_date=''){
		global $db,$HTTP_SESSION_VARS;

		if(empty($batch_nr) || (!$batch_nr))
			return FALSE;
		if(empty($new_service_date) || (!$new_service_date))
			return FALSE;

		$history = $this->ConcatHistory("Update service_date ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");

		$this->sql="UPDATE $this->tb_test_request_radio ".
						" SET service_date='".$new_service_date."', history=".$history.", ".
						"		modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
						" WHERE batch_nr = $batch_nr";

#echo "class_radiology.php : updateRadioRequestServiceDate : this->sql = '".$this->sql."' <br> \n";

		if ($buf=$db->Execute($this->sql)){
			if($db->Affected_Rows()){
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }
	}# end of function updateRadioRequestServiceDate

	/**
	* Inserts ct scan info into table 'seg_radio_ct_history'
	* @param Array Data to by reference
	* @return boolean
	* @created by: Cherry, August 5, 2010
	*/
        /*    
        function saveCTClinicalHistory(&$data){
		global $db, $HTTP_SESSION_VARS;

		$this->_useCTHistory();
		extract($data);

		$index = "encounter_nr, subj_comp, obj_comp, assessment, has_conscious, did_vomit, gcs, rls, had_surgery, surgery_date,
							surgery_proc, has_blood_chem, has_xray, has_ultrasound, has_ct_mri, has_biopsy";
		$values = "'$encounter_nr','$subj_comp','$obj_comp','$assessment','$has_conscious','$did_vomit','$gcs','$rls',$had_surgery,".
							"'$surgery_date','$surgery_proc','$has_blood_chem','$has_xray','$has_ultrasound','$has_ct_mri','$has_biopsy'";

		$this->sql = "INSERT INTO $this->coretable ($index)
									VALUES ($values)";
		if($db->Execute($this->sql)){
			 return TRUE;
		}else{ return FALSE; }
	}# end function saveCTHistory
        */

	/**
	* Gets ct scan info from 'seg_radio_ct_history'
	* @created by: Cherry, August 5,2010
	*/
	 function getCTHistory($encounter_nr){
		 global $db;
		 $this->sql="SELECT * FROM $this->tb_seg_radio_ct_history WHERE encounter_nr='$encounter_nr'";
		if ($buf=$db->Execute($this->sql)){
			if($buf->RecordCount()) {
				return $buf->FetchRow();
			}else { return FALSE; }
		}else { return FALSE; }
	 }
     
     //added by Francis L.G 03-26-13
     function getCTHistoryInfo($pid,$refno,$grp){
         global $db;
         
         $ctpid = $pid;
         $ctrefno = $refno;
         $ctgrp = $grp;
         
         if($ctrefno){
            $this->sql="SELECT * FROM seg_radio_ct_history WHERE pid='$ctpid' AND refno='$ctrefno' AND group_code='$ctgrp'";      
         }
         else if($ctpid){
            $this->sql="SELECT * FROM seg_radio_ct_history WHERE pid='$ctpid' AND refno='0' AND group_code='$ctgrp'";   
         }
         else{
            return FALSE;
         }
         
        if ($buf=$db->Execute($this->sql)){
            if($buf->RecordCount()) {
                return $buf->FetchRow();
            }else { return FALSE; }
        }else { return FALSE; }
     }
    
    //added by Francis L.G 03-26-13 
    function getCTHistoryInfoPDF($refno){
         global $db;
         
         $this->sql="SELECT * FROM $this->tb_seg_radio_ct_history WHERE refno='$refno'";
        if ($buf=$db->Execute($this->sql)){
            if($buf->RecordCount()) {
                return $buf->FetchRow();
            }else { return FALSE; }
        }else { return FALSE; }
     }
     
     //added by Francis L.G 03-26-13
     function getMriHistoryInfo($pid,$refno,$grp){
         global $db;
         
         $mripid = $pid;
         $mrirefno = $refno;
         $mrigrp = $grp;
         
         if($mrirefno){
            $this->sql="SELECT * FROM seg_radio_mri_history WHERE pid='$mripid' AND refno='$mrirefno' AND group_code='$mrigrp'";      
         }
         else if($mripid){
            $this->sql="SELECT * FROM seg_radio_mri_history WHERE pid='$mripid' AND refno='0' AND group_code='$mrigrp'";   
         }
         else{
            return FALSE;
         }
         
        if ($buf=$db->Execute($this->sql)){
            if($buf->RecordCount()) {
                return $buf->FetchRow();
            }else { return FALSE; }
        }else { return FALSE; }
     }
     
     //added by Francis L.G 03-26-13 
    function getMriHistoryInfoPDF($refno){
         global $db;
         
         $this->sql="SELECT * FROM seg_radio_mri_history WHERE refno='$refno'";
        if ($buf=$db->Execute($this->sql)){
            if($buf->RecordCount()) {
                return $buf->FetchRow();
            }else { return FALSE; }
        }else { return FALSE; }
     }
    
     //added by Francis L.G 03-26-13
     function getRadioRequestdata($refno=0,$batchNum=0){
         global $db;
         
         $this->sql="SELECT rr.*,pr.or_no,pr.amount_due FROM care_test_request_radio AS rr LEFT JOIN seg_pay_request AS pr ON rr.refno=pr.ref_no AND rr.service_code=pr.service_code WHERE refno='$refno' AND rr.batch_nr='$batchNum'";
         if ($buf=$db->Execute($this->sql)){
             if($buf->RecordCount()) {
                return $buf->FetchRow();
             }else { return FALSE; }
        }else { return FALSE; }
     } 
    
    //added by Francis L.G 03-26-13 
    function saveMRIClinicalHistory(&$data){
        global $db, $HTTP_SESSION_VARS;

        $this->_useCTHistory();
        extract($data);

                            
        $index = "encounter_nr, 
                  history,
                  chief_comp,
                  phy_exam,
                  impression,
                  past_med_his,
                  creatinine,
                  bun,
                  mri_dr_nr,
                  mri_dr_name,
                  create_id,
                  create_tm,
                  modify_id,
                  modify_tm,
                  uuid,
                  dr_specialty,
                  info_gain,
                  med_prob,
                  dr_nr,
                  dr_name,
                  dr_address,
                  dr_contact_nr,
                  dr_phone,
                  refer_date,
                  purpose,
                  pid,
                  group_code,
                  mp_amount,
                  mp_request,
                  mp_trans_type,
                  mp_total,
                  encoder";

        $values = "'$encounterNr',
                   '$history',
                   '$chiefComp',
                   '$phyExam',
                   '$impression',
                   '$pastMedHis',
                   '$creatinine',
                   '$bun',
                   '$mriDrNr',
                   '$mriDrName',
                   '$createId',
                   '$createTm',
                   '$modifyId',
                   '$modifyTm',
                    UUID(),
                   '$drSpecialty',
                   '$infoGain',
                   '$medProb',
                   '$drNr',
                   '$drName',
                   '$drAddress',
                   '$drContactNr',
                   '$drPhone',
                   '$referDate',
                   '$mriPurpose',
                   '$pid',
                   '$grpCode',
                   '$amount',
                   '$serviceRequests',
                   '$paymentType',
                   '$totalMP',
                   '$encoder'";
        
         
        $this->sql = "INSERT INTO seg_radio_mri_history ($index) VALUES ($values)";
       
        if($db->Execute($this->sql)){
             return TRUE;
        }else{ return FALSE; }
    }
    
    //added by Francis L.G 03-26-13
     function updateMRIClinicalHistory($pid,$uuid,&$data,$grp=0){
        global $db;
        extract($data);
        
            
          $del = "modify_id='',
                  modify_tm='',
                  history='',
                  chief_comp='',
                  phy_exam='',
                  impression='',
                  past_med_his='',
                  creatinine='',
                  bun='',
                  mri_dr_nr='',
                  mri_dr_name='',
                  dr_specialty='',
                  info_gain='',
                  med_prob='',
                  dr_nr='',
                  dr_name='',
                  dr_address='',
                  dr_contact_nr='',
                  dr_phone='',
                  refer_date='',
                  purpose='',
                  group_code='',
                  mp_amount='',
                  mp_request='',
                  mp_trans_type='',
                  mp_total='',
                  encoder=''";
            
         $index1 = "modify_id='$modifyId',
                   modify_tm='$modifyTm',
                   history='$history',
                   chief_comp='$chiefComp',
                   phy_exam='$phyExam',
                   impression='$impression',
                   past_med_his='$pastMedHis',
                   creatinine='$creatinine',
                   bun='$bun',
                   mri_dr_nr='$mriDrNr',
                   mri_dr_name='$mriDrName',
                   dr_specialty='$drSpecialty',
                   info_gain='$infoGain',
                   med_prob='$medProb',
                   dr_nr='$drNr',
                   dr_name='$drName',
                   dr_address='$drAddress',
                   dr_contact_nr='$drContactNr',
                   dr_phone='$drPhone',
                   refer_date='$referDate',
                   purpose='$mriPurpose',
                   group_code='$grpCode',
                   mp_amount='$amount',
                   mp_request='$serviceRequests',
                   mp_trans_type='$paymentType',
                   mp_total='$totalMP',
                   encoder='$encoder'";
                   
         $index2 = "refno='$refno',
                   priority='$priority',
                   request='$request',
                   total='$totalAmount',
                   discount='$discount',
                   transaction='$transaction',
                   request_date='$reqDate'";  
            
            
            if(!$uuid){          
                $this->sql = "UPDATE seg_radio_mri_history SET $index2 WHERE pid='$pid' AND group_code='$grp' AND refno='0'";
                if($db->Execute($this->sql)){
                     return TRUE;
                }
                else{ return FALSE; }
            }else{         
                $this->sql = "UPDATE seg_radio_mri_history SET $del WHERE pid='$pid' AND uuid='$uuid'";
       
        if($db->Execute($this->sql)){
                    $this->sql = "UPDATE seg_radio_mri_history SET $index1 WHERE pid='$pid' AND uuid='$uuid'"; 
                    if($db->Execute($this->sql)){
             return TRUE;
                     }
                     else{ return FALSE; }
        }else{ return FALSE; }
    }
     }

    //added by Francis L.G 03-26-13
    function saveCTscanClinicalHistory(&$data){
        global $db, $HTTP_SESSION_VARS;

        extract($data);

        $index = "encounter_nr, subj_comp, obj_comp, assessment, has_conscious, did_vomit, gcs, rls, had_surgery, surgery_date,
                            surgery_proc, has_blood_chem, has_xray, has_ultrasound, has_ct_mri, has_biopsy, doctor_nr, create_id, create_tm,
                            modify_id,modify_tm,date_blood_chem,date_xray,date_ultrasound,date_ct_mri,date_biopsy,uuid,bld_chm_res,bld_chm_rem,
                            xray_res,xray_rem,ultrasound_res,ultrasound_rem,ct_mri_res,ct_mri_rem,biopsy_res,biopsy_rem,
                            noi,doi,poi,toi,cln_imp,chf_cmp,medico_legal,dr_name,pid,group_code,mp_amount,
                            mp_request,mp_trans_type,mp_total,encoder";
                            
        $values = "'$encounter_nr','$subj_comp','$obj_comp','$assessment','$has_conscious','$did_vomit','$gcs','$rls','$had_surgery',
                            '$surgery_date','$surgery_proc','$has_blood_chem','$has_xray','$has_ultrasound','$has_ct_mri','$has_biopsy','$doctor_in',
                            '$create_id', '$create_tm','$modify_id','$modify_tm','$date_blood_chem','$date_xray','$date_ultrasound','$date_ct_mri','$date_biopsy',UUID(),
                            '$bld_chm_res','$bld_chm_rem','$xray_res','$xray_rem','$ultrasound_res','$ultrasound_rem','$ct_mri_res','$ct_mri_rem',
                            '$biopsy_res','$biopsy_rem','$noi','$doi','$poi','$toi','$cln_imp','$chf_cmp','$medico_legal','$dr_name','$pid',
                            '$grpCode','$amount','$serviceRequests','$paymentType','$totalMP','$encoder'";

        $this->sql = "INSERT INTO seg_radio_ct_history ($index) VALUES ($values)";
       
        if($db->Execute($this->sql)){
             return TRUE;
        }else{ return FALSE; }
    }

    

    //added by Francis L.G 03-26-13
    function updateCTClinicalHistory($pid,$uuid,&$data,$grp=0){
        global $db;
        extract($data);
        
            $del = "subj_comp='',
                    obj_comp='',
                    assessment='',
                    has_conscious='',
                    did_vomit='', 
                    gcs='',
                    rls='',
                    had_surgery='',
                    surgery_date='',
                    surgery_proc='',
                    has_blood_chem='', 
                    has_xray='',
                    has_ultrasound='',
                    has_ct_mri='',
                    has_biopsy='',
                    doctor_nr='',
                    modify_id='',
                    modify_tm='',
                    date_blood_chem='',
                    date_xray='',
                    date_ultrasound='',
                    date_ct_mri='',
                    date_biopsy='',
                    bld_chm_res='',
                    bld_chm_rem='',
                    xray_res='',
                    xray_rem='',
                    ultrasound_res='',
                    ultrasound_rem='',
                    ct_mri_res='',
                    ct_mri_rem='',
                    biopsy_res='',
                    biopsy_rem='',
                    noi='',
                    doi='',
                    poi='',
                    toi='',
                    cln_imp='',
                    chf_cmp='',
                    medico_legal='',
                    dr_name='',
                    group_code='',
                    mp_amount='',
                    mp_request='',
                    mp_trans_type='',
                    mp_total='',
                    encoder=''";
        
            $index1 = "encounter_nr='$encounter_nr',
                        subj_comp='$subj_comp',
                        obj_comp='$obj_comp',
                        assessment='$assessment', 
                        has_conscious='$has_conscious',
                        did_vomit='$did_vomit',
                        gcs='$gcs',
                        rls='$rls',
                        had_surgery='$had_surgery', 
                        surgery_date='$surgery_date',
                        surgery_proc='$surgery_proc',
                        has_blood_chem='$has_blood_chem', 
                        has_xray='$has_xray',
                        has_ultrasound='$has_ultrasound',
                        has_ct_mri='$has_ct_mri',
                        has_biopsy='$has_biopsy',
                        doctor_nr='$doctor_in',
                        modify_id='$modify_id',
                        modify_tm='$modify_tm',
                        date_blood_chem='$date_blood_chem',
                        date_xray='$date_xray',
                        date_ultrasound='$date_ultrasound',
                        date_ct_mri='$date_ct_mri',
                        date_biopsy='$date_biopsy',
                        bld_chm_res='$bld_chm_res',
                        bld_chm_rem='$bld_chm_rem',
                        xray_res='$xray_res',
                        xray_rem='$xray_rem',
                        ultrasound_res='$ultrasound_res',
                        ultrasound_rem='$ultrasound_rem',
                        ct_mri_res='$ct_mri_res',
                        ct_mri_rem='$ct_mri_rem',
                        biopsy_res='$biopsy_res',
                        biopsy_rem='$biopsy_rem',
                        noi='$noi',
                        doi='$doi',
                        poi='$poi',
                        toi='$toi',
                        cln_imp='$cln_imp',
                        chf_cmp='$chf_cmp',
                        medico_legal='$medico_legal',
                        dr_name='$dr_name',
                        group_code='$grpCode',
                        mp_amount='$amount',
                        mp_request='$serviceRequests',
                        mp_trans_type='$paymentType',
                        mp_total='$totalMP',
                        encoder='$encoder'";
                            
             $index2 = "refno='$refno',
                        priority='$priority',
                        request='$request',
                        total='$totalAmount',
                        discount='$discount',
                        transaction='$transaction',
                        request_date='$reqDate'";
                            
        if(!$uuid){          
            $this->sql = "UPDATE seg_radio_ct_history SET $index2 WHERE pid='$pid' AND group_code='$grp' AND refno='0'";
            if($db->Execute($this->sql)){
                 return TRUE;
            }
            else{ return FALSE; }
        }
        else{         
            $this->sql = "UPDATE seg_radio_ct_history SET $del WHERE pid='$pid' AND uuid='$uuid'";
            
            if($db->Execute($this->sql)){
                $this->sql = "UPDATE seg_radio_ct_history SET $index1 WHERE pid='$pid' AND uuid='$uuid'"; 
                if($db->Execute($this->sql)){
                 return TRUE;
                 }
                 else{ return FALSE; }
            }else{ return FALSE; }
        }
     }
     
     //added by Francis 06-17-13
     function deleteCTClinicalHistory($uuid){
        global $db;
        $this->sql = "DELETE FROM seg_radio_ct_history WHERE uuid='$uuid'";
            
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                return TRUE;
            }else{ return FALSE; }
        }else{ return FALSE; }  
     }
            
     function deleteMRIClinicalHistory($uuid){
        global $db;
        $this->sql = "DELETE FROM seg_radio_mri_history WHERE uuid='$uuid'";
            
            if($db->Execute($this->sql)){
            if ($db->Affected_Rows()) {
                 return TRUE;
            }else{ return FALSE; }
            }else{ return FALSE; }
     }
    
     //added by Francis L.G 03-26-13
     function updateCtMriClinicalHistory($uuid,$refno,$data){
        global $db;
        extract($data);
            
            $index = "refno='$refno',priority='$priority',request='$request',total='$totalAmount',discount='$discount',transaction='$transaction'";

                $this->sql = "UPDATE $this->tb_seg_radio_ct_history SET $index WHERE uuid='$uuid'"; 
                if($db->Execute($this->sql)){
                 return TRUE;
                 }
                 else{ return FALSE; }

     }
    
    

	 /**
	 * Deletes data from 'seg_radio_ct_history'
	 * @created by: Cherry, August 09, 2010
	 */
	 function deleteCTHistory($encounter_nr){
		global $db;
			$this->sql = "DELETE FROM $this->tb_seg_radio_ct_history WHERE encounter_nr='$encounter_nr'";
			if($db->Execute($this->sql)){
				 return TRUE;
			}else{ return FALSE; }
	 }#end deleteCTHistory function

		/*
		* Inserts new radiology request info into table 'seg_radio_serv' & 'care_test_request_radio'
		* @param Array Data to by reference
		* @return boolean
		* @created : burn, Aug 30, 2007
		*/

		//edited by daryl
	function saveRadioRefNoInfoFromArray(&$data){
		global $db,$HTTP_SESSION_VARS;

#echo "class_radiology.php : saveRadioRefNoInfoFromArray : data : <br> "; print_r($data); echo " <br> \n";

		$this->_useRadioServ();
		extract($data);
// print_r($data);
		$arrayItems = array();
		foreach ($service_code as $key => $value){
			$tempArray = array($value);
			array_push($arrayItems,$tempArray);
		}
#echo "class_radiology.php : saveRadioRefNoInfoFromArray: arrayItems : <br> "; print_r($arrayItems); echo " <br> \n";
#		"refno","request_date","encounter_nr","pid","ordername","orderaddress","is_cash","hasPaid","is_urgent",
#		"comments","status","history","modify_id","modify_dt","create_id","create_dt"
		$refno = $this->getNewRefNo(date('Y')."000001");

		if (empty($area_type))
			$area_type = 'NULL';
		else
			$area_type = "'".$area_type."'";

		if (empty($encounter_nr))
			$encounter_nr = 'NULL';
		else
			$encounter_nr = "'".$encounter_nr."'";

		# edited by VAN 01-29-08 add: request_time
		#edited by VAN 07-16-2010 TEMPORARILY ;  grant_type
		$index = "refno,request_date,request_time,encounter_nr,discountid,discount,pid,ordername,orderaddress,is_cash,
					type_charge,is_urgent,is_tpl,
					comments,status,history,create_id,create_dt, is_pay_full,walkin_pid,source_req, area_type, grant_type, is_rdu, is_pe";
		$values = "'$refno','$request_date','$request_time',$encounter_nr,'$discountid','$discount','$pid','$ordername','$orderaddress',$is_cash,".
					"'$type_charge', '$is_urgent','$is_tpl','$comments','$status','$history','$encoder', NOW(),'$is_pay_full','$walkin_pid','$source_req',$area_type,'$grant_type','$is_rdu','$is_pe'";

#		$index = "encounter_nr, clinical_info, service_code, is_in_house,
#						request_doctor, request_date, encoder,	status, priority, history, create_id, create_dt";
		if ($pid == ""){
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
						}else if(strpos($ordername, " ") == true) {
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


					$index = "refno,request_date,request_time,encounter_nr,discountid,discount,pid,ordername,orderaddress,is_cash,
					type_charge,is_urgent,is_tpl,
					comments,status,history,create_id,create_dt, is_pay_full,walkin_pid,source_req, area_type, grant_type, is_rdu, is_pe";
					$values = "'$refno','$request_date','$request_time',$encounter_nr,'$discountid','$discount','$newpid','$ordername','$orderaddress',$is_cash,".
					"'$type_charge', '$is_urgent','$is_tpl','$comments','$status','$history','$encoder', NOW(),'$is_pay_full','$newpid','$source_req',$area_type,'$grant_type','$is_rdu','$is_pe'";


					$this->sql1 = "INSERT INTO seg_walkin ($index_walkin)
					   			 	VALUES ($values_walkin)";
					$db->Execute($this->sql1);  

		$this->sql = "INSERT INTO $this->coretable ($index)
							VALUES ($values)";

					
		}else{
		$this->sql = "INSERT INTO $this->coretable ($index)
							VALUES ($values)";
		}

		

#echo "class_radiology.php : saveRadioRefNoInfoFromArray : this->sql = '".$this->sql."' <br><br> \n";
#exit();
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
#echo "class_radiology.php : saveRadioRefNoInfoFromArray : db->Insert_ID() = '".$db->Insert_ID()."' <br> \n";
				$data['refno']=$refno;
#echo "class_radiology.php : saveRadioRefNoInfoFromArray : data['refno'] = '".$data['refno']."' <br> \n";
				$this->saveRadioRequestInfoFromArrayNEW($data);
				$this->grantRadioRequest($data);


				return $refno;
#				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end function saveRadioRefNoInfoFromArray

		/*
		* Inserts new radiology request info into table 'care_test_request_radio'
		* @param Array Data to by reference
		* @return boolean
		* @created : burn, Aug 30, 2007
		*/
	function saveRadioRequestInfoFromArrayNEW(&$data){
		global $db,$HTTP_SESSION_VARS;

#echo "<br>class_radiology.php : saveRadioRequestInfoFromArrayNEW : data : <br> "; print_r($data); echo " <br><br> \n";

		$this->_useRequestRadio();
		extract($data);

		$arrayItems = array();
		foreach ($service_code as $key => $value){
			$tempArray = array($value,$clinical_info[$key],$edit_tot_[$key],$edit_tot_[$key],$edit_tot_[$key],$is_in_house[$key],$request_doctor[$key],$edit_tot_[$key]);
			array_push($arrayItems,$tempArray);
		}
#echo "class_radiology.php : saveRadioRequestInfoFromArrayNEW : arrayItems : <br> "; print_r($arrayItems); echo " <br> \n";
#		"batch_nr","encounter_nr","clinical_info","service_code", "service_date", "is_in_house",
#		"request_doctor", "request_date", "encoder",	"status", "priority", "history",	"create_id", "create_dt"

		#commented by VAN 01-12-08
		/*
		$index = "refno, service_code, clinical_info, price_cash, price_cash_orig, price_charge, is_in_house,
						request_doctor, request_date, encoder,	status, history, create_id, create_dt";
		$values = "'$refno', ?, ?, ?, ?, ?, ?, ?".
						", '$request_date', '".$HTTP_SESSION_VARS['sess_user_name'].
						"', 'pending', '$history', '".$HTTP_SESSION_VARS['sess_user_name']."', NOW()";
		*/
		if (empty($request_flag))
			$request_flag = 'NULL';
		else
			$request_flag = "'".$request_flag."'";

		#echo "class radiology batch, head, remarks = ".$parent_batch_nr.",".$approved_by_head.",".$remarks;
		#added by VAN 07-16-2010 TEMPORARILY = request_flag
		#edited by daryl //added editable price
		if ($is_cash){
			$index = "refno, service_code, clinical_info, price_cash, price_cash_orig, price_charge, is_in_house,
							request_doctor,price_alter, request_date, encoder,	status, history, create_id, create_dt,parent_batch_nr,parent_refno,approved_by_head,remarks,headID,headpasswd,request_flag";
			$values = "'$refno', ?, ?, ?, ?, ?, ?, ?, ?".
							", '$request_date', '".$HTTP_SESSION_VARS['sess_user_name'].
							"', 'pending', '$history', '".$HTTP_SESSION_VARS['sess_user_name']."', NOW(),'$parent_batch_nr','$parent_refno',
							 '$approved_by_head','$remarks', '$headID', '$headpasswd',$request_flag";
		}else{
			$index = "refno, service_code, clinical_info, price_cash, price_cash_orig, price_charge, is_in_house,
							request_doctor,price_alter, request_date, encoder,	status, history, create_id, create_dt,parent_batch_nr,parent_refno,approved_by_head,remarks,headID,headpasswd,request_flag";
			$values = "'$refno', ?, ?, ?, ?, ?, ?, ?, ?".
							", '$request_date', '".$HTTP_SESSION_VARS['sess_user_name'].
							"', 'pending', '$history', '".$HTTP_SESSION_VARS['sess_user_name']."', NOW(),'$parent_batch_nr','$parent_refno',
							 '$approved_by_head','$remarks', '$headID', '$headpasswd',$request_flag";
		}

		$this->sql = "INSERT INTO $this->coretable ($index)
							VALUES ($values)";

#echo "<br>class_radiology.php : saveRadioRequestInfoFromArrayNEW : this->sql = '".$this->sql."' <br> \n";
#exit();
		if ($db->Execute($this->sql,$arrayItems)) {
			if ($db->Affected_Rows()) {
                
                #automatic apply the coverage upon saving the request
                #will clarify to the user when should the phic coverage will be applied
                # Handle applied coverage for PHIC and other benefits
                #$this->apply_coverage($refno, $arrayItemsList);
                
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end function saveRadioRequestInfoFromArrayNEW

	 /*
	* Updates radiology request info in table 'care_test_request_radio'
	* @param Array Data to by reference
	* @return boolean
	* @created : burn, September 4, 2007
	*/
	function updateRadioRefNoInfoFromArray(&$data){
		global $HTTP_SESSION_VARS, $_SESSION, $dbtype;
/*
	echo "updateRadioRequestInfoFromArray : data = ";
	print_r ($data);
	echo " <br> \n";
*/
		$this->_useRadioServ();
		$this->data_array=$data;
		// remove probable existing array data to avoid replacing the stored data
		unset($this->data_array['create_id']);
		unset($this->data_array['create_dt']);
		unset($this->data_array['modify_dt']);
		unset($this->data_array['status']);
		unset($this->data_array['service_code']);
		unset($this->data_array['clinical_info']);
		unset($this->data_array['request_doctor']);
		unset($this->data_array['is_in_house']);
		unset($this->data_array['pnet']);
		unset($this->data_array['pcash']);
		unset($this->data_array['pcharge']);
		unset($this->data_array['edit_tot_']);

		#------ added by VAN 01-12-08
		unset($this->data_array['parent_batch_nr']);
		unset($this->data_array['parent_refno']);
		unset($this->data_array['approved_by_head']);
		unset($this->data_array['remarks']);
		#added by VAN 03-19-08
		unset($this->data_array['headID']);
		unset($this->data_array['headpasswd']);



		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];

#echo "class_radiology.php : data['service_code'] : "; print_r($data['service_code']); echo " <br><br> \n";
	$current_list = $this->getListedRequestsByRefNo($data['refno']);
#echo "class_radiology.php : current_list : "; print_r($current_list); echo " <br><br> \n";
	$current_deleted_list = $this->getListedRequestsByRefNo($data['refno'],"AND status IN ($this->dead_stat)");
#echo "class_radiology.php : current_deleted_list : "; print_r($current_deleted_list); echo " <br><br> \n";
	$update_only_list = array_intersect($data['service_code'],$current_list);
#echo "class_radiology.php : update_only_list : "; print_r($update_only_list); echo " <br><br> \n";
	$add_only_list = array_diff($data['service_code'],$current_list);
#echo "class_radiology.php : add_only_list 1 : "; print_r($add_only_list); echo " <br><br> \n";
	$update_status_only_list = array_intersect($current_deleted_list,$add_only_list);
#echo "class_radiology.php : update_status_only_list 1 : "; print_r($update_status_only_list); echo " <br><br> \n";
	$update_deleted2pending_status_only_list = array_intersect($data['service_code'],$current_deleted_list);
#echo "class_radiology.php : update_deleted2pending_status_only_list : "; print_r($update_deleted2pending_status_only_list); echo " <br><br> \n";
	$update_status_only_list = array_unique(array_merge($update_status_only_list,$update_deleted2pending_status_only_list));
#echo "class_radiology.php : update_status_only_list 2 : "; print_r($update_status_only_list); echo " <br><br> \n";
	$add_only_list2 = array_diff($add_only_list,$update_status_only_list);
#echo "class_radiology.php : add_only_list 2 : "; print_r($add_only_list); echo " <br><br> \n";
#	$delete_only_list = array_diff($current_list,$_POST['service_code']);
	$delete_only_list = array_diff($current_list,$data['service_code']);
#echo "class_radiology.php : delete_only_list : "; print_r($delete_only_list); echo " <br><br> \n";
#exit();
/*
echo "class_radiology.php : add_only_list ='".$add_only_list."' <br> \n";
echo "class_radiology.php : 1 empty(add_only_list) ".empty($add_only_list)." <br> \n";
echo "class_radiology.php : is_array(add_only_list) ".is_array($add_only_list)." <br> \n";
*/
	#echo "batch, head, remarks".$data['parent_batch_nr']." - ".$data['approved_by_head']." - ".$data['remarks'];
		# Add service codes that are not yet in the 'care_test_request_radio' table
		if (is_array($add_only_list) && !empty($add_only_list)){
#echo "class_radiology.php : 2 is_array(add_only_list) ".is_array($add_only_list)." <br> \n";
			$temp_data = $data;
			$temp_serv_code = array();
			$temp_clinical_info = array();
			$temp_request_doctor = array();
			$temp_is_in_house = array();
			$temp_pnet = array();
			$temp_pcash = array();
			$temp_pcharge = array();

			foreach ($add_only_list as $key => $value){
				$orig_key = array_search($value, $data['service_code']);
				array_push($temp_serv_code,$value);
				array_push($temp_clinical_info,$data['clinical_info'][$orig_key]);
				array_push($temp_request_doctor,$data['request_doctor'][$orig_key]);
				array_push($temp_is_in_house,$data['is_in_house'][$orig_key]);
				#edited by daryl for editable price
				// array_push($temp_pcash,$data['pcash'][$orig_key]);
				// array_push($temp_pcharge,$data['pcharge'][$orig_key]);
				// array_push($temp_pnet,$data['pnet'][$orig_key]);

				array_push($temp_pnet,$data['edit_tot_'][$orig_key]);
				array_push($temp_pcash,$data['edit_tot_'][$orig_key]);
				array_push($temp_pcharge,$data['edit_tot_'][$orig_key]);
				array_push($temp_peditprice,$data['edit_tot_'][$orig_key]);

			}
			$temp_data['service_code'] = $temp_serv_code;
			$temp_data['clinical_info'] = $temp_clinical_info;
			$temp_data['request_doctor'] = $temp_request_doctor;
			$temp_data['is_in_house'] = $temp_is_in_house;
			$temp_data['pnet'] = $temp_pnet;
			$temp_data['pcash'] = $temp_pcash;
			$temp_data['pcharge'] = $temp_pcharge;
			$temp_data['edit_tot_'] = $temp_peditprice;

			$temp_data['parent_batch_nr'] = $data['parent_batch_nr'];
			$temp_data['parent_refno'] = $data['parent_refno'];
			$temp_data['approved_by_head'] = $data['approved_by_head'];
			$temp_data['remarks'] = $data['remarks'];

			#added by VAN 03-19-08
			$temp_data['headID'] = $data['headID'];
			$temp_data['headpasswd'] = $data['headpasswd'];

			$temp_data['request_flag'] = $data['grant_type'];

			#----------------------------------

			$temp_data['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
#echo "class_radiology.php : ADD service codes : temp_data : <br>\n"; print_r($temp_data); echo " <br><br> \n";
			$this->saveRadioRequestInfoFromArrayNEW($temp_data);
			#echo "<br>hello";
			#$this->data_array['status'] = 'pending';
		}

		# Logical deletion [setting the status to 'delete'] for service codes that are to be deleted
		if (is_array($delete_only_list) && !empty($delete_only_list)){
#echo "<br>class_radiology.php : is_array(delete_only_list) ".is_array($delete_only_list)." <br> \n";
			$arrayItems = array();
			foreach ($delete_only_list as $key => $value){
#echo "class_radiology.php : DELETE service codes : data['refno']='".$data['refno']."'; value='".$value."' <br> \n";
				$tempArray = array($value);
				array_push($arrayItems,$tempArray);
			}

#echo "class_radiology.php :  DELETE service codes : arrayItems : <br>\n"; print_r($arrayItems); echo " <br><br> \n";
			#edited by VAN 03-18-08
			/*
			echo "<br>";
			print_r($data);
			echo "<br>";
			print_r($arrayItems);
			*/
			$this->updateRadioRequestStatusByRefNoServCode($data, $arrayItems,'deleted');
			#$this->updateRadioRequestStatusByRefNoServCode($data, $arrayItems,'updated');
		}# end of if-stmt 'if (is_array($delete_only_list))'

#echo "<br>batch, head, remarks = ".$data['parent_batch_nr']." - ".$data['approved_by_head']." - ".$data['remarks'];

		# Change status from 'deleted' to 'pending' for service codes that are re-requested
		if (is_array($update_status_only_list) && !empty($update_status_only_list)){
#echo "<br>class_radiology.php : is_array(update_status_only_list) ".is_array($update_status_only_list)." <br> \n";
			$arrayItems = array();
			foreach ($update_status_only_list as $key => $value){
#echo "class_radiology.php : UPDATE EXISTING service codes : data['refno']='".$data['refno']."'; value='".$value."' <br> \n";
				$orig_key = array_search($value, $data['service_code']);
				#comment by VAN 01-14-08
				/*
				$tempArray = array($data['clinical_info'][$orig_key], $data['request_doctor'][$orig_key],
										$data['is_in_house'][$orig_key], $data['pnet'][$orig_key],
										$data['pcash'][$orig_key],$data['pcharge'][$orig_key], $value);
				*/
#edited by daryl for editable price
				// $tempArray = array($data['clinical_info'][$orig_key], $data['request_doctor'][$orig_key],
				// 						$data['is_in_house'][$orig_key], $data['pnet'][$orig_key],
				// 						$data['pcash'][$orig_key],$data['pcharge'][$orig_key],
				// 						$data['parent_batch_nr'], $data['parent_refno'], $data['approved_by_head'], $data['remarks'],
				// 						$data['headID'], $data['headpasswd'], $value);

				$tempArray = array($data['clinical_info'][$orig_key], $data['request_doctor'][$orig_key],
										$data['is_in_house'][$orig_key], $data['edit_tot_'][$orig_key],
										$data['edit_tot_'][$orig_key],$data['edit_tot_'][$orig_key],$data['edit_tot_'][$orig_key],
										$data['parent_batch_nr'], $data['parent_refno'], $data['approved_by_head'], $data['remarks'],
										$data['headID'], $data['headpasswd'], $value);
				/*
				$tempArray = array($data['clinical_info'][$orig_key], $data['request_doctor'][$orig_key],
										$data['is_in_house'][$orig_key], $data['pnet'][$orig_key],
										$data['pcash'][$orig_key],$data['pcharge'][$orig_key],
										$data['parent_batch_nr'], $data['parent_refno'], $data['approved_by_head'], $data['remarks'], $value);
				*/
				#$tempArray = array($value);
				array_push($arrayItems,$tempArray);
			}

			# added by VAN 01-14-08
			/*
			array_push($arrayItems,$data['parent_batch_nr']);
			array_push($arrayItems,$data['approved_by_head']);
			array_push($arrayItems,$data['remarks']);
			*/
			#-----------------------------

#echo "class_radiology.php :  UPDATE EXISTING service codes : arrayItems : <br>\n"; print_r($arrayItems); echo " <br><br> \n";
			$this->updateRadioRequestStatusByRefNoServCode($data, $arrayItems,'pending');
			#$this->data_array['status'] = ' ';
		}# end of if-stmt 'if (is_array($delete_only_list))'

		# Update service codes that have been modified and existing in the 'care_test_request_radio' table
		if (is_array($update_only_list) && !empty($update_only_list)){
#echo "<br>class_radiology.php : is_array(update_only_list) ".is_array($update_only_list)." <br> \n";
			$arrayItems = array();
			foreach ($update_only_list as $key => $value){
				$orig_key = array_search($value, $data['service_code']);
				#commented by VAN
				/*
				$tempArray = array($data['clinical_info'][$orig_key], $data['request_doctor'][$orig_key],
										$data['is_in_house'][$orig_key], $data['pnet'][$orig_key],
										$data['pcash'][$orig_key], $data['pcharge'][$orig_key], $value);
				*/
#edited by daryl for editable price
				// $tempArray = array($data['clinical_info'][$orig_key], $data['request_doctor'][$orig_key],
				// 						$data['is_in_house'][$orig_key], $data['pnet'][$orig_key],
				// 						$data['pcash'][$orig_key], $data['pcharge'][$orig_key],
				// 						$data['parent_batch_nr'], $data['parent_refno'], $data['approved_by_head'],
				// 						$data['remarks'],$data['headID'],$data['headpasswd'], $value);

				$tempArray = array($data['clinical_info'][$orig_key], $data['request_doctor'][$orig_key],
										$data['is_in_house'][$orig_key], $data['edit_tot_'][$orig_key],
										$data['edit_tot_'][$orig_key], $data['edit_tot_'][$orig_key],$data['edit_tot_'][$orig_key],
										$data['parent_batch_nr'], $data['parent_refno'], $data['approved_by_head'],
										$data['remarks'],$data['headID'],$data['headpasswd'], $value);
				/*

				$tempArray = array($data['clinical_info'][$orig_key], $data['request_doctor'][$orig_key],
										$data['is_in_house'][$orig_key], $data['pnet'][$orig_key],
										$data['pcash'][$orig_key], $data['pcharge'][$orig_key],
										$data['parent_batch_nr'], $data['parent_refno'], $data['approved_by_head'],
										$data['remarks'], $value);
				*/
				#$tempArray = array($value);
				array_push($arrayItems,$tempArray);
			}

			#-----------------------------
			$this->updateRadioRequestStatusByRefNoServCode($data, $arrayItems);
#echo "class_radiology.php : UPDATE service codes : arrayItems : <br>\n"; print_r($arrayItems); echo " <br><br> \n";
			//$this->saveRadioRequestInfoFromArrayNEW($temp_data);
		}

		#added by VAN 08-07-08
		$this->grantRadioRequest($data);

		$this->_useRadioServ();
		if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
			else $concatfx='concat';

			#	Only the keys of data to be updated must be present in the passed array.
		$x='';
		$v='';
		$this->buffer_array = array();
        $this->data_array['modify_id'] = $_SESSION['sess_temp_userid'];
        $this->data_array['history'] = $this->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_temp_userid']."\n");

		#added by VAN 03-19-08
		#if (is_array($update_status_only_list) && !empty($update_status_only_list)){
			#$this->data_array['status'] = ' ';
		#}
		#added by VAN 03-19-08
		$this->data_array['status'] = ' ';

		while(list($x,$v)=each($this->ref_array)) {
			#echo "v = ".$v;
#			if(isset($this->data_array[$v])&&(trim($this->data_array[$v])!='')) {
			if (isset($this->data_array[$v]))
				$this->buffer_array[$v]=trim($this->data_array[$v]);
#			}
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

#echo "class_radiology.php : updateRadioRequestStatusByRefNoServCode : <br>elems = '".$elems."' <br> \n";
			$this->sql="UPDATE $this->coretable SET $elems, modify_dt=NOW() ".
						" WHERE refno='".$this->data_array['refno']."' ";
#echo "<br>class_radiology.php :  updateRadioRefNoInfoFromArray : this->sql = '".$this->sql."' <br> \n";
#exit();
		return $this->Transact();
	}# end of function updateRadioRefNoInfoFromArray

		/*
		* Inserts new radiology request info into table 'care_test_request_radio'
		* @param Array Data to by reference
		* @return boolean
		* @created : burn, Aug 1, 2007
		*/
	function saveRadioRequestInfoFromArray(&$data){
		global $db,$HTTP_SESSION_VARS;

#echo "class_radiology.php : saveRadioRequestInfoFromArray : data : <br> "; print_r($data); echo " <br> \n";

		$this->_useRequestRadio();
		extract($data);

		$arrayItems = array();
		foreach ($service_code as $key => $value){
			$tempArray = array($value);
			array_push($arrayItems,$tempArray);
		}
#echo "class_radiology.php : saveRadioRequestInfoFromArray : arrayItems : <br> "; print_r($arrayItems); echo " <br> \n";
#		"batch_nr","encounter_nr","clinical_info","service_code", "service_date", "is_in_house",
#		"request_doctor", "request_date", "encoder",	"status", "priority", "history",	"create_id", "create_dt"
		$index = "encounter_nr, clinical_info, service_code, is_in_house,
						request_doctor, request_date, encoder,	status, priority, history, create_id, create_dt";
		$values = "$encounter_nr, '$clinical_info', ?, $is_in_house".
						", '$request_doctor', '$request_date', '".$HTTP_SESSION_VARS['sess_user_name'].
						"', 'pending', $priority, '$history', '".$HTTP_SESSION_VARS['sess_user_name']."', NOW()";

		$this->sql = "INSERT INTO $this->coretable ($index)
							VALUES ($values)";

#echo "class_radiology.php : saveRadioRequestInfoFromArray : this->sql = '".$this->sql."' <br> \n";

		if ($db->Execute($this->sql,$arrayItems)) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }

/*
			$arrayItems = array();
			$result=$curriculum->SelectOne("id=$curriculum_id");
			if ($curriculum->count > 0){
				$i=1;
				while($i <= $result['term']){
					if (!$this->itemExists($campusID,$item_id,$i,$curriculum_id,$group_name,$name,$amount)){
						$tempArray = array((int)$i, (int)$curriculum_id);
						array_push($arrayItems,$tempArray);
					}
					$i++;
				}# end of while loop
			}
		if (($ok) && (!empty($arrayItems)) ){
			$this->sql = "INSERT INTO $this->fee_amounts_table (campus_id,item_id,term_no,curriculum_id,amount)
									VALUES ('$campusID',$this->itemID,?,?,$amount)";
			$ok=$DB->Execute($this->sql,$arrayItems);
			$this->count=$DB->Affected_Rows();
		}
*/
	}# end function saveRadioRequestInfoFromArray

		/*
		* Insert new radiology request info into table 'care_test_request_radio'
		* @param Array Data to by reference
		* @return boolean
		* @created : burn, July 31, 2007
		*/
	function saveRadioRequestInfoFromArray2(&$data){
		$this->_useRequestRadio();
		$this->data_array=$data;
		return $this->insertDataFromInternalArray();
	}# end function saveRadioRequestInfoFromArray

	 /*
	* Updates radiology request info in table 'care_test_request_radio'
	* @param Array Data to by reference
	* @return boolean
	* @created : burn, July 31, 2007
	*/
	function updateRadioRequestInfoFromArray(&$data){
		global $HTTP_SESSION_VARS, $dbtype;

#	echo "updateRadioRequestInfoFromArray : data = ";
#	print_r ($data);
#	echo " <br> \n";

		$this->_useRequestRadio();
		$this->data_array=$data;
		// remove probable existing array data to avoid replacing the stored data
		unset($this->data_array['create_id']);
		unset($this->data_array['create_dt']);
		unset($this->data_array['modify_dt']);
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];

		if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
			else $concatfx='concat';

			#	Only the keys of data to be updated must be present in the passed array.
		$x='';
		$v='';
		while(list($x,$v)=each($this->ref_array)) {
#			if(isset($this->data_array[$v])&&(trim($this->data_array[$v])!='')) {
				$this->buffer_array[$v]=trim($this->data_array[$v]);
#			}
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
			$this->sql="UPDATE $this->coretable SET $elems, modify_dt=NOW() ".
						" WHERE batch_nr='".$this->data_array['batch_nr']."' ";
#echo "class_radiology.php : updateRadioRequestInfoFromArray : this->sql = '".$this->sql."' <br> \n";

		return $this->Transact();
	}# end of function updateRadioRequestInfoFromArray

		/*
		* Inserts new radiology request info into table 'care_test_request_radio'
		* @param Array Data to by reference
		* @return boolean
		* @created : burn, Aug 1, 2007
		*/
	function saveRadioFindingInfoFromArray(&$data){
		global $db,$HTTP_SESSION_VARS;

		$this->_useFindingRadio();
		extract($data);

#		"batch_nr", "findings",	"findings_date", "doctor_in_charge",
#		"history", "modify_id",	"modify_dt", "create_id", "create_dt"
#		"status", "service_date"   ==> from table 'care_test_request_radio'

		#$index = "batch_nr, findings, radio_impression,	findings_date, doctor_in_charge, result_status, encoder, history, create_id, create_dt";
		$index = "batch_nr, findings, radio_impression,	findings_date, doctor_in_charge, encoder, history, create_id, create_dt";
		#$values = "$batch_nr, '$findings', '$radio_impression', '$findings_date', '$doctor_in_charge', '$result_status', '".
		$values = "$batch_nr, '$findings', '$radio_impression', '$findings_date', '$doctor_in_charge', '".
						$HTTP_SESSION_VARS['sess_user_name'].
						"', '$history', '".$HTTP_SESSION_VARS['sess_user_name']."', NOW()";

		$this->sql = "INSERT INTO $this->coretable ($index)
							VALUES ($values)";

#echo "class_radiology.php : saveRadioFindingInfoFromArray : this->sql = '".$this->sql."' <br> \n";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$this->updateRadioRequestStatus($batch_nr, $status);
				$this->updateRadioRequestServiceDate($batch_nr, $service_date);
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end function saveRadioFindingInfoFromArray

		/*
		* Updates new radiology request info into table 'care_test_request_radio'
		* @param Array Data to by reference
		* @return boolean
		* @created : burn, Aug 2, 2007
		*/
	function updateRadioFindingInfoFromArray(&$data){
		global $db,$HTTP_SESSION_VARS;

#echo "class_radiology.php : updateRadioFindingInfoFromArray : data : <br>"; print_r($data); echo " <br> \n";
		$this->_useFindingRadio();
#		$data['history']=$this->ConcatHistory($data['mode']." a finding : ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		extract($data);

#		"batch_nr", "findings",	"findings_date", "doctor_in_charge",
#		"history", "modify_id",	"modify_dt", "create_id", "create_dt"
#		"status", "service_date"   ==> from table 'care_test_request_radio'
#" result_status = '$result_status', ".
		$elems="batch_nr = '$batch_nr', findings = '$findings', radio_impression ='$radio_impression', ".
					" findings_date = '$findings_date', doctor_in_charge = '$doctor_in_charge', ".
					" encoder = '".$HTTP_SESSION_VARS['sess_user_name']."', ".
					" history = $history, modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."'";
			$this->sql="UPDATE $this->coretable SET $elems, modify_dt=NOW() ".
						" WHERE batch_nr='".$batch_nr."' ";

#echo "class_radiology.php : updateRadioFindingInfoFromArray : this->sql = '".$this->sql."' <br> \n";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$this->updateRadioRequestStatus($batch_nr, $status);
				$this->updateRadioRequestServiceDate($batch_nr, $service_date);
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end function updateRadioFindingInfoFromArray


		/*
		* Adds/Updates a finding info into table 'care_test_findings_radio'
		* @param int, batch number
		* @param int, finding number/index
		* @param string, the new finding
		* @param date, date the finding reported
		* @param int, personell_nr of the reporting doctor
		* @return boolean
		* @created : burn, Aug 8, 2007
		*/
	function saveAFinding($batch_nr='',$finding_nr='-1', $findings='', $radio_impression='', $findings_date='', $doctor_id='',$result_status='',$mode='Add'){
		global $db,$HTTP_SESSION_VARS;

		$this->_useFindingRadio();
		if(empty($batch_nr) || (!$batch_nr))
			return FALSE;
		if(intval($finding_nr)<0)
			return FALSE;
#echo "class_radiology.php : saveAFinding : mode = '".$mode."'<br>\n";
#echo "class_radiology.php : saveAFinding : batch_nr = '".$batch_nr."'; finding_nr='".$finding_nr."'; ".
#	" findings='".$findings."'; radio_impression='".$radio_impression."'; findings_date='".$findings_date."'; doctor_id='".$doctor_id."'; mode='".$mode."' <br> \n";

#		"batch_nr", "findings",	"findings_date", "doctor_in_charge",
#		"history", "modify_id",	"modify_dt", "create_id", "create_dt"
#		"status", "service_date"   ==> from table 'care_test_request_radio'
		$this->sql=" SELECT *
						FROM care_test_findings_radio
						WHERE batch_nr='$batch_nr'";

		if ($buf=$db->Execute($this->sql)){
			if($this->count=$buf->RecordCount()) {
				$findingsInfo = $buf->FetchRow();
			}else { return FALSE; }
		}else { return FALSE; }

#echo "class_radiology.php : saveAFinding : before : findingsInfo : "; print_r($findingsInfo); echo " <br> \n";

		if (!$findingsInfo){
			return FALSE;   # no data retrieved
		}else{
			$findings_array = unserialize($findingsInfo['findings']);
			$findings_date_array = unserialize($findingsInfo['findings_date']);
			$doctor_in_charge_array = unserialize($findingsInfo['doctor_in_charge']);
			$radio_impression_array = unserialize($findingsInfo['radio_impression']);

				# add/update the particular findings using the findings_nr index
			$findings_array[$finding_nr] = $findings;
			$findings_date_array[$finding_nr] = $findings_date;
			$doctor_in_charge_array[$finding_nr] = $doctor_id;
			$radio_impression_array[$finding_nr] = $radio_impression;

#echo "class_radiology.php : saveAFinding : findings_array : "; print_r($findings_array); echo " <br> \n";
#echo "class_radiology.php : saveAFinding : findings_date_array : "; print_r($findings_date_array); echo " <br> \n";
#echo "class_radiology.php : saveAFinding : doctor_in_charge_array : "; print_r($doctor_in_charge_array); echo " <br> \n";
#echo "class_radiology.php : saveAFinding : radio_impression_array : "; print_r($radio_impression_array); echo " <br> \n";
			#echo "status = ".$result_status;
			#added by VAN 07-10-08
			$findingsInfo['result_status'] = serialize($result_status);

			$findingsInfo['findings'] = addslashes(serialize($findings_array));
			$findingsInfo['findings_date'] = serialize($findings_date_array);
			$findingsInfo['doctor_in_charge'] = serialize($doctor_in_charge_array);
			$findingsInfo['radio_impression'] = addslashes(serialize($radio_impression_array));
			$findingsInfo['history']=$this->ConcatHistory("$mode a finding : ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");

#echo "class_radiology.php : saveAFinding : after : findingsInfo : "; print_r($findingsInfo); echo " <br> \n";
			return $this->updateRadioFindingInfoFromArray($findingsInfo);
		}# end of else-stmt
	}# end function saveAFinding

		/*
		* Deletes a finding info from table 'care_test_findings_radio'
		* @param int, batch number
		* @param int, finding number/index
		* @return boolean
		* @created : burn, Aug 8, 2007
		*/
	function deleteAFinding($batch_nr='',$finding_nr='-1'){
		global $db,$HTTP_SESSION_VARS;

		$this->_useFindingRadio();

		if(empty($batch_nr) || (!$batch_nr))
			return FALSE;
		if(intval($finding_nr)<0)
			return FALSE;

#		"batch_nr", "findings",	"findings_date", "doctor_in_charge",
#		"history", "modify_id",	"modify_dt", "create_id", "create_dt"
#		"status", "service_date"   ==> from table 'care_test_request_radio'
		$this->sql=" SELECT *
						FROM care_test_findings_radio
						WHERE batch_nr='$batch_nr'";

		if ($buf=$db->Execute($this->sql)){
			if($this->count=$buf->RecordCount()) {
				$findingsInfo = $buf->FetchRow();
			}else { return FALSE; }
		}else { return FALSE; }

#echo "class_radiology.php : deleteAFinding : before : findingsInfo : "; print_r($findingsInfo); echo " <br> \n";

		if (!$findingsInfo){
			return FALSE;   # no data retrieved
		}else{
			$findings_array = unserialize($findingsInfo['findings']);
			$radio_impression_array = unserialize($findingsInfo['radio_impression']);
			$findings_date_array = unserialize($findingsInfo['findings_date']);
			$doctor_in_charge_array = unserialize($findingsInfo['doctor_in_charge']);
/*
echo "class_radiology.php : deleteAFinding : findings_array : "; print_r($findings_array); echo " <br> \n";
echo "class_radiology.php : deleteAFinding : radio_impression_array : "; print_r($radio_impression_array); echo " <br> \n";
echo "class_radiology.php : deleteAFinding : findings_date_array : "; print_r($findings_date_array); echo " <br> \n";
echo "class_radiology.php : deleteAFinding : doctor_in_charge_array : "; print_r($doctor_in_charge_array); echo " <br> \n";
*/
			$new_findings_array = array();
			$new_radio_impression_array = array();
			$new_findings_date_array = array();
			$new_doctor_in_charge_array = array();
			$count=0;
			foreach($findings_array as $key_finding => $value_finding){
				if (intval($finding_nr)!=intval($key_finding)){
					$new_findings_array[$count] = $value_finding;
					$new_radio_impression_array[$count] = $radio_impression_array[$key_finding];
					$new_findings_date_array[$count] = $findings_date_array[$key_finding];
					$new_doctor_in_charge_array[$count] =  $doctor_in_charge_array[$key_finding];
					$count++;
				}
			}# end of foreach loop
/*
echo "class_radiology.php : deleteAFinding : new_findings_array : "; print_r($new_findings_array); echo " <br> \n";
echo "class_radiology.php : deleteAFinding : new_radio_impression_array : "; print_r($new_radio_impression_array); echo " <br> \n";
echo "class_radiology.php : deleteAFinding : new_findings_date_array : "; print_r($new_findings_date_array); echo " <br> \n";
echo "class_radiology.php : deleteAFinding : new_doctor_in_charge_array : "; print_r($new_doctor_in_charge_array); echo " <br> \n";
*/
			if (empty($new_findings_array)|| (!$new_findings_array)){
				#$findingsInfo['status'] = 'pending';
				$findingsInfo['status'] = 'done';
			}
			$findingsInfo['findings'] = serialize($new_findings_array);
			$findingsInfo['radio_impression'] = serialize($new_radio_impression_array);
			$findingsInfo['findings_date'] = serialize($new_findings_date_array);
			$findingsInfo['doctor_in_charge'] = serialize($new_doctor_in_charge_array);
			$findingsInfo['history']=$this->ConcatHistory("Delete a finding : ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");

#echo "class_radiology.php : deleteAFinding : after : findingsInfo : "; print_r($findingsInfo); echo " <br> \n";
			return $this->updateRadioFindingInfoFromArray($findingsInfo);
/*
			extract($findingsInfo);
			$elems="batch_nr = $batch_nr, findings = '$findings', findings_date = '$findings_date', ".
						" doctor_in_charge = '$doctor_in_charge', encoder = '".$HTTP_SESSION_VARS['sess_user_name']."', ".
						" history = $history, modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."'";
			$this->sql="UPDATE $this->coretable SET $elems, modify_dt=NOW() ".
							" WHERE batch_nr=".$batch_nr." ";
echo "class_radiology.php : deleteAFinding : this->sql = '".$this->sql."' <br> \n";
			if ($db->Execute($this->sql)) {
				if ($db->Affected_Rows()) {
					return TRUE;
				}else{ return FALSE; }
			}else{ return FALSE; }
*/
		}# end of else-stmt
	}# end function deleteAFinding


		/*
		* Creates a borrowing entry in table 'seg_radio_borrow'
		* @param int, batch number
		* @param int, personell_nr of the borrowing doctor (doctor's id number)
		* @param date, date the film borrowed/released (in mm/dd/yyyy format)
		* @param time, time the film borrowed/released (in hh:mm:ss format)
		* @param int, personell_nr of the releaser/encoder (releaser/encoder's id number)
		* @param string, fullname of the releaser/encoder
		* @param string, comment
		* @return borrow number (borrow_nr) OR boolean
		* @created : burn, October 31, 2007
		*/
	function createBorrowEntry($batch_nr='', $borrower_id=0, $date_borrowed='', $time_borrowed='',
									 $releaser_id=0, $releaser_fullname='', $remarks=''){
		global $db;

		if(empty($batch_nr) || (!$batch_nr))
			return FALSE;
		if(intval($borrower_id)<0)
			return FALSE;

		$this->_useRadioBorrow();

		$history = "Created : ".date('Y-m-d H:i:s')." = $releaser_fullname \n";
		$index = "borrow_nr, batch_nr, borrower_id, date_borrowed, time_borrowed,
							releaser_id, remarks, status, history, create_id, create_dt";
		$values = "'$batch_nr', '$borrower_id', '$date_borrowed', '$time_borrowed',
							'$releaser_id', '$remarks', 'borrowed', '$history', '$releaser_fullname', NOW()";

		$borrow_nr=$this->getNewBorrowNr(date('Y')."000001");
		$this->sql = "INSERT INTO $this->coretable ($index)
							VALUES ('$borrow_nr',$values)";
#echo "class_radiology.php : createBorrowEntry :: this->sql = '".$this->sql."' <br> \n";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				return $borrow_nr;
#				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end function createBorrowEntry


		/*
		* Updates a borrowing entry in table 'seg_radio_borrow'
		* @param int, borrow number
		* @param int, personell_nr of the borrowing doctor (doctor's id number)
		* @param date, date the film borrowed/released (in mm/dd/yyyy format)
		* @param time, time the film borrowed/released (in hh:mm:ss format)
		* @param int, personell_nr of the releaser/encoder (releaser/encoder's id number)
		* @param string, fullname of the releaser/encoder
		* @param string, comment
		* @return boolean
		* @created : burn, November 7, 2007
		*/
	function updateBorrowEntry($borrow_nr='', $borrower_id=0, $date_borrowed='', $time_borrowed='',
										$releaser_id=0, $releaser_fullname='', $remarks=''){
		global $db;

		if(empty($borrow_nr) || (!$borrow_nr))
			return FALSE;

		$this->_useRadioBorrow();

		$history = $this->ConcatHistory("Updated (borrow) : ".date('Y-m-d H:i:s')." $releaser_fullname \n");
		$elems = "borrower_id='$borrower_id', date_borrowed='$date_borrowed',
						time_borrowed='$time_borrowed', releaser_id='$releaser_id', remarks='$remarks',
						history=$history, modify_id='$releaser_fullname', modify_dt=NOW()";
		$this->sql="UPDATE $this->coretable ".
						" SET $elems ".
						" WHERE borrow_nr='$borrow_nr'";

#echo "class_radiology.php : updateBorrowEntry :: this->sql = '".$this->sql."' <br> \n";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end function updateBorrowEntry

		/*
		* Updates the status a borrowing entry in table 'seg_radio_borrow' with 'returned'
		* @param int, borrow number
		* @param date, date the film returned (in mm/dd/yyyy format)
		* @param time, time the film returned (in hh:mm:ss format)
		* @param int, personell_nr of the receiver/encoder (receiver/encoder's id number)
		* @param string, fullname of the receiver/encoder
		* @param string, comment
		* @return boolean
		* @created : burn, November 8, 2007
		*/
	function updateReturnBorrowedFilm($borrow_nr='', $date_returned='', $time_returned='',
													$receiver_id=0, $receiver_fullname='', $remarks=''){
		global $db;

		if(empty($borrow_nr) || (!$borrow_nr))
			return FALSE;

		$this->_useRadioBorrow();

		$history = $this->ConcatHistory("Updated (return) : ".date('Y-m-d H:i:s')." $receiver_fullname \n");
		$elems = "date_returned='$date_returned', time_returned='$time_returned',
						receiver_id='$receiver_id', remarks='$remarks', status='returned',
						history=$history, modify_id='$receiver_fullname', modify_dt=NOW()";
		$this->sql="UPDATE $this->coretable ".
						" SET $elems ".
						" WHERE borrow_nr='$borrow_nr'";

#echo "class_radiology.php : updateReturnBorrowedFilm :: this->sql = '".$this->sql."' <br> \n";
#return FALSE;
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end function updateReturnBorrowedFilm

		/*
		* Updates the status a borrowing entry in table 'seg_radio_borrow' with 'returned'
		* @param int, borrow number
		* @param date, date the film returned (in mm/dd/yyyy format)
		* @param time, time the film returned (in hh:mm:ss format)
		* @param int, personell_nr of the receiver/encoder (receiver/encoder's id number)
		* @param string, fullname of the receiver/encoder
		* @param string, comment
		* @return boolean
		* @created : burn, November 9, 2007
		*/
	function updateDoneBorrowedFilm($borrow_nr=''){
		global $db,$HTTP_SESSION_VARS;

		if(empty($borrow_nr) || (!$borrow_nr))
			return FALSE;

		$this->_useRadioBorrow();

		$history = $this->ConcatHistory("Updated (done) : ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_fullname']." \n");
		$elems = "status='done', history=$history, modify_id='".$HTTP_SESSION_VARS['sess_temp_fullname']."', modify_dt=NOW()";
		$this->sql="UPDATE $this->coretable ".
						" SET $elems ".
						" WHERE borrow_nr='$borrow_nr'";

#echo "class_radiology.php : updateDoneBorrowedFilm :: this->sql = '".$this->sql."' <br> \n";
#return FALSE;
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end function updateDoneBorrowedFilm

	function getBorrowerBorrowedFilms($borrower_id=''){
		global $db;

		if(empty($borrower_id) || (!$borrower_id)){
			return FALSE;
		}

		$this->sql="SELECT IF(STRCMP(srb.borrower_id,CAST(srb.borrower_id AS UNSIGNED INTEGER)),
								srb.borrower_id, fn_get_personell_name(srb.borrower_id)) AS borrower_name,
							sri.rid, r_serv.pid,
							IF(ISNULL(r_serv.pid),
								r_serv.ordername,
								CONCAT(TRIM(cp.name_last),', ',TRIM(cp.name_first),' ', IF(TRIM(cp.name_middle)<>'',CONCAT(LEFT(TRIM(cp.name_middle),1),'.'),''))
							) AS patient_name,
							IF(ISNULL(r_serv.pid),
								r_serv.ordername,
								CONCAT(TRIM(cp.name_last),', ',TRIM(cp.name_first),' ', IF(TRIM(cp.name_middle)<>'',TRIM(cp.name_middle),''))
							) AS patient_fullname,
							TRIM(cp.name_first) AS name_first, TRIM(cp.name_middle) AS name_middle, TRIM(cp.name_last) AS name_last,
							IF(STRCMP(srb.releaser_id,CAST(srb.releaser_id AS UNSIGNED INTEGER)),
								srb.releaser_id, fn_get_personell_name(srb.releaser_id)) AS releaser_name,
							r.service_code, r.price_cash AS price_net, r.price_cash_orig AS price_gross,
							srb.*,r.batch_nr, r_serv.refno
						FROM seg_radio_borrow AS srb
							LEFT JOIN care_test_request_radio AS r ON r.batch_nr=srb.batch_nr
								LEFT JOIN seg_radio_serv AS r_serv ON r.refno=r_serv.refno
									LEFT JOIN care_person AS cp ON cp.pid=r_serv.pid
									LEFT JOIN seg_radio_id AS sri ON sri.pid = r_serv.pid
						WHERE srb.status IN ('borrowed') AND srb.borrower_id='$borrower_id'
						AND DATEDIFF(DATE(NOW()),srb.date_borrowed)>=3
						ORDER BY srb.date_borrowed ASC, srb.time_borrowed ASC ";

						#line 2168 is added by VAN 07-31-08 (AND DATEDIFF(DATE(NOW()),srb.date_borrowed)>=3)
/*
AND r_serv.status NOT IN ('deleted','hidden','inactive','void')
*/
#echo "class_radiology.php : getBorrowerBorrowedFilms: this->sql = '".$this->sql."' <br> \n";
#exit();
		if ($buf=$db->Execute($this->sql)){
			if($buf->RecordCount()) {
				return $buf;
			}else { return FALSE; }
		}else { return FALSE; }
	}# end function getBorrowerBorrowedFilms


	#added by VAN 07-08-08
	function getScheduledRadioRequestInfo2($batch_nr='',$limit=FALSE, $sub_dept_nr='', $date=''){
		global $db,$root_path;

		$OPTIONS_WHERE_SQL='';
		if ($limit){
				# needs ONLY ONE record/row to return
			if(empty($batch_nr) || (!$batch_nr)){
				return FALSE;
			}
			#$OPTIONS_WHERE_SQL .= "AND sr_sked.batch_nr='$batch_nr' ";
			$OPTIONS_WHERE_SQL .= "AND r.batch_nr='$batch_nr' ";
		}
		if ($date){
			include_once($root_path.'include/inc_date_format_functions.php');
			$date_format=getDateFormat();
				# Check if it is a complete date in mm/dd/yyyy format
			$this_date=@formatDate2STD($date,$date_format);
			if($this_date!='') {
				$OPTIONS_WHERE_SQL .= " AND DATE(sr_sked.scheduled_dt)='$this_date'";
			}
		}
		if ($sub_dept_nr){
				$OPTIONS_WHERE_SQL .= " AND r_serv_group.department_nr ='$sub_dept_nr'";
		}

		#edited by VAN 07-08-08
		$this->sql="SELECT sr_sked.schedule_no, sri.rid, IF ((request_flag IS NOT NULL),1,0) AS hasPaid,
							r.batch_nr, IFNULL(sr_sked.scheduled_dt,DATE(NOW())) AS scheduled_dt,
							IFNULL(sr_sked.scheduled_time,TIME(NOW())) AS scheduled_time,
							sr_sked.instructions, sr_sked.remarks,
							sr_sked.status, sr_sked.modify_id, sr_sked.create_id,
							r.service_code, r.service_date, r_services.name AS service_name,
							r_serv_group.department_nr AS sub_dept_nr, dept.name_short AS dept_name_short, dept.name_formal AS dept_name,
							IF(ISNULL(r_serv.pid),
								r_serv.ordername, fn_get_pid_name(r_serv.pid)) AS patient_name,
							cp.name_first, cp.name_middle, cp.name_last
							FROM care_test_request_radio AS r
							LEFT JOIN seg_radio_schedule AS sr_sked ON r.batch_nr=sr_sked.batch_nr
							INNER JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
							INNER JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
							INNER JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
							INNER JOIN seg_radio_serv AS r_serv ON r.refno=r_serv.refno
							INNER JOIN seg_radio_id AS sri ON sri.pid = r_serv.pid
							INNER JOIN care_person AS cp ON cp.pid=r_serv.pid
						WHERE r_serv.status NOT IN ($this->dead_stat)
							AND r.status NOT IN ($this->dead_stat)
							AND (request_flag IS NOT NULL OR r_serv.is_urgent=1 OR r_serv.is_cash=0)
							$OPTIONS_WHERE_SQL
							ORDER BY sr_sked.scheduled_dt ASC";

#echo "class_radiology.php : getScheduledRadioRequestInfo: this->sql = '".$this->sql."' <br> \n";
#exit();
		if ($buf=$db->Execute($this->sql)){
			if($this->count=$buf->RecordCount()) {
				if ($limit)
					return $buf->FetchRow();
				else
					return $buf;
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getScheduledRadioRequestInfo

	/*
		* Gets the BASIC radiology scheduled request(s) info
		* @param int batch number
		* @param boolean limit, TRUE for one row returned value, FALSE for all records
		* @param int department number
		* @param date date of query (in mm/dd/yyyy format)
		* @return a record of radiology unscheduled request(s) or boolean
		* @created : burn, December 8, 2007
		*/
	function getScheduledRadioRequestInfo($batch_nr='',$limit=FALSE, $sub_dept_nr='', $date='',$walk=''){
		global $db,$root_path;

		$OPTIONS_WHERE_SQL='';
		if ($limit){
				# needs ONLY ONE record/row to return
			if(empty($batch_nr) || (!$batch_nr)){
				return FALSE;
			}
			$OPTIONS_WHERE_SQL .= "AND sr_sked.batch_nr='$batch_nr' ";
			#$OPTIONS_WHERE_SQL .= "AND r.batch_nr='$batch_nr' ";
		}
		if ($walk == 1){
			$sql_ifwalk = "INNER JOIN seg_walkin AS cp ON cp.pid=r_serv.pid"; 
		}else{
			$sql_ifwalk = "INNER JOIN care_person AS cp ON cp.pid=r_serv.pid"; 

		}
		if ($date){
			include_once($root_path.'include/inc_date_format_functions.php');
			$date_format=getDateFormat();
				# Check if it is a complete date in mm/dd/yyyy format
			$this_date=@formatDate2STD($date,$date_format);
			if($this_date!='') {
				$OPTIONS_WHERE_SQL .= " AND DATE(sr_sked.scheduled_dt)= '$this_date'";
			}
		}
		if ($sub_dept_nr){
				$OPTIONS_WHERE_SQL .= " AND r_serv_group.department_nr ='$sub_dept_nr'";
		}
		$this->sql="SELECT sr_sked.schedule_no, sri.rid, IF ((request_flag IS NOT NULL),1,0) AS hasPaid,
							sr_sked.batch_nr,sr_sked.scheduled_dt, sr_sked.scheduled_time,
							sr_sked.instructions, sr_sked.remarks,
							sr_sked.status, sr_sked.modify_id, sr_sked.create_id,
							r.service_code, r.service_date, r_services.name AS service_name,
							r_serv_group.department_nr AS sub_dept_nr, dept.name_short AS dept_name_short, dept.name_formal AS dept_name,
							IF(ISNULL(r_serv.pid),
								r_serv.ordername, fn_get_pid_name(r_serv.pid)) AS patient_name,
							cp.name_first, cp.name_middle, cp.name_last
						FROM seg_radio_schedule AS sr_sked
							INNER JOIN care_test_request_radio AS r ON r.batch_nr=sr_sked.batch_nr
							INNER JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
							INNER JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
							INNER JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
							INNER JOIN seg_radio_serv AS r_serv ON r.refno=r_serv.refno
							INNER JOIN seg_radio_id AS sri ON sri.pid = r_serv.pid
							$sql_ifwalk
						WHERE r_serv.status NOT IN ($this->dead_stat)
							AND r.status NOT IN ($this->dead_stat)
							AND (request_flag IS NOT NULL OR r_serv.is_urgent=1 OR r_serv.is_cash=0)
							$OPTIONS_WHERE_SQL
							ORDER BY sr_sked.scheduled_dt ASC";

#echo "class_radiology.php : getScheduledRadioRequestInfo: this->sql = '".$this->sql."' <br> \n";
#exit();
		if ($buf=$db->Execute($this->sql)){
			if($this->count=$buf->RecordCount()) {
				if ($limit)
					return $buf->FetchRow();
				else
					return $buf;
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getScheduledRadioRequestInfo


		/*
		* Creates a schedule entry in table 'seg_radio_schedule'
		* @param int, batch number
		* @param datetime, scheduled date&time of service (in mm/dd/yyyy hh:mm:ss format)
		* @param string, instructions
		* @param string, comment/remarks
		* @param string, fullname of the encoder
		* @return boolean
		* @created : burn, November 24, 2007
		*/
	#edited by VAN 03-26-08
	#function createRadioSchedule($batch_nr='', $scheduled_dt='', $instructions='', $remarks='', $encoder=''){
	function createRadioSchedule($batch_nr='', $scheduled_dt='', $scheduled_time='', $instructions='', $remarks='', $encoder=''){
		global $db, $HTTP_SESSION_VARS;

		if(empty($batch_nr) || (!$batch_nr))
			return FALSE;

		$this->_useRadioSchedule();

		$history = "Created : ".date('Y-m-d H:i:s')." = $encoder \n";
		#$index = "batch_nr, scheduled_dt, instructions, remarks, history, create_id, create_dt";
		$index = "batch_nr, scheduled_dt, scheduled_time, instructions, remarks, history, create_id, create_dt";
		#$values = "'$batch_nr', '$scheduled_dt', '$instructions', '$remarks', '$history', '$encoder', NOW()";
		$values = "'$batch_nr', '$scheduled_dt', '$scheduled_time', '$instructions', '$remarks', '$history', '$encoder', NOW()";

#		$borrow_nr=$this->getNewBorrowNr(date('Y')."000001");
		$this->sql = "INSERT INTO $this->coretable ($index)
							VALUES ($values)";
#echo "class_radiology.php : createRadioSchedule :: this->sql = '".$this->sql."' <br> \n";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end function createRadioSchedule


		/*
		* Updates the schedule entry in table 'seg_radio_schedule'
		* @param int, batch number
		* @param datetime, scheduled date&time of service (in mm/dd/yyyy hh:mm:ss format)
		* @param date, date of service (in mm/dd/yyyy hh:mm:ss format)
		* @param string, instructions
		* @param string, comment/remarks
		* @param string, fullname of the encoder
		* @return boolean
		* @created : burn, December 11, 2007
		*/
	#edited by VAN 03-26-08
	#function updateRadioSchedule($batch_nr='', $scheduled_dt='', $service_dt='', $instructions='', $remarks='', $encoder=''){
	function updateRadioSchedule($batch_nr='', $scheduled_dt='', $scheduled_time='', $service_dt='', $instructions='', $remarks='', $encoder=''){
		global $db, $HTTP_SESSION_VARS;

		if(empty($batch_nr) || (!$batch_nr))
			return FALSE;

		$this->_useRadioSchedule();

		$add_update='';
		if (!empty($service_dt)){
			$add_update = ", status='done' ";
		}

		$history = $this->ConcatHistory("Updated : ".date('Y-m-d H:i:s')." ".$encoder." \n");
		#$elems = "scheduled_dt='$scheduled_dt', instructions='$instructions', remarks='$remarks', history=$history, modify_id='".$encoder."', modify_dt=NOW()";
		$elems = "scheduled_dt='$scheduled_dt', scheduled_time='$scheduled_time', instructions='$instructions', remarks='$remarks', history=$history, modify_id='".$encoder."', modify_dt=NOW()";
		$this->sql="UPDATE $this->coretable ".
						" SET $elems $add_update".
						" WHERE batch_nr='$batch_nr'";

#echo "class_radiology.php : updateRadioSchedule :: this->sql = '".$this->sql."' <br> \n";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end function updateRadioSchedule

		/*
		* Deletes a scheduled radio request from table 'seg_radio_schedule'
		* @param int, batch number
		* @return boolean
		* @created : burn, December 8, 2007
		*/
	function deleteRadioSchedule($batch_nr=''){
		global $db;

		if(empty($batch_nr) || (!$batch_nr))
			return FALSE;

		$this->_useRadioSchedule();

		$this->sql="DELETE FROM $this->coretable ".
						" WHERE batch_nr='$batch_nr'";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}#end of function deleteRadioSchedule


		/*
		* Gets the list of instructions of a radiology sub-department
		* @param int dept number
		* @param int instruction number
		* @return an array of radiology instructions info or boolean
		* @created : burn, November 28, 2007
		*/
	function getRadioInstructionsInfo($dept_nr='',$instruction_nr=''){
		global $db;

		if(empty($dept_nr) || (!$dept_nr)){
			return FALSE;
		}
		if ($instruction_nr){
			$WHERE_SQL = " AND sr_ins.nr='$instruction_nr'";
		}

		$this->sql="SELECT *
						FROM seg_radio_instructions AS sr_ins
						WHERE sr_ins.dept_nr IN (0,$dept_nr)
						$WHERE_SQL
						ORDER BY sr_ins.nr DESC";

#echo "class_radiology.php : getRadioInstructionsInfo :: this->sql = '".$this->sql."' <br> \n";
#exit();
		if ($buf=$db->Execute($this->sql)){
			if($this->count=$buf->RecordCount()) {
				if ($instruction_nr){
					return $buf->FetchRow();
				}else{
					return $buf;
				}
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getRadioInstructionsInfo


		/**
		*   Gets the list of unscheduled radiology requests
		*
		*   @access public
		*   @param string Table name
		*   @return boolean OR Record set
		*   @created burn: December 3, 2007
		*/
	function _searchRadioUnscheduled($key,$sub_dept_nr='',$ORDER_BY_OPTION='',$limit=FALSE,$len=30,$so=0){
		global $db, $sql_LIKE, $root_path;

		#added by VAN 03-24-08
		$key = utf8_decode($key);
#edited by VAN 03-19-08
		if(is_numeric($key)){
			#$key=(int)$key;
			$WHERE_SQL=" AND (srb.borrower_id = '".addslashes($key)."' OR r_serv.pid = '".addslashes($key)."'
									OR r_serv.rid = '".addslashes($key)."' OR srb.releaser_id = '".addslashes($key)."')";
		}elseif($key=='%'||$key=='*'){
			$WHERE_SQL="";
		}else{
/*
			$WHERE_SQL=" AND ((IF(STRCMP(srb.borrower_id,CAST(srb.borrower_id AS UNSIGNED INTEGER)),
										srb.borrower_id, fn_get_personell_name(srb.borrower_id)) LIKE '%$key%')
									OR (IF(STRCMP(srb.releaser_id,CAST(srb.releaser_id AS UNSIGNED INTEGER)),
										srb.releaser_id, fn_get_personell_name(srb.releaser_id)) LIKE '%$key%')
									OR (IF(ISNULL(r_serv.pid),
										r_serv.ordername, fn_get_pid_name(r_serv.pid)) LIKE '%$key%')
									OR r.service_code LIKE '%$key%' )";
*/
			$WHERE_SQL=" AND ( (fn_get_personell_name(srb.borrower_id) LIKE '".addslashes($key)."%')
									OR (fn_get_personell_name(srb.releaser_id) LIKE '".addslashes($key)."%')
									OR (fn_get_pid_name(r_serv.pid) = '".addslashes($key)."')
									OR r.service_code LIKE '".addslashes($key)."%' )";
		}

		include_once($root_path.'include/inc_date_format_functions.php');
		$date_format=getDateFormat();
			# Check if it is a complete date in mm/dd/yyyy format
		$this_date=@formatDate2STD($key,$date_format);
		if($this_date!='') {
			$WHERE_SQL=" AND (srb.date_borrowed='$this_date')";
		}

		if ($sub_dept_nr){
			$WHERE_SQL.=" AND r_serv_group.department_nr=".$sub_dept_nr;
		}

		$this->sql="SELECT IF(STRCMP(srb.borrower_id,CAST(srb.borrower_id AS UNSIGNED INTEGER)),
								srb.borrower_id, fn_get_personell_name(srb.borrower_id)) AS borrower_name,
							r.service_code,
							r_serv_group.department_nr AS sub_dept_nr, dept.id AS sub_dept_id, dept.name_formal AS sub_dept_name,
							sri.rid, r_serv.pid,
							IF(ISNULL(r_serv.pid),
								r_serv.ordername, fn_get_pid_name(r_serv.pid)) AS patient_name,
							IF(STRCMP(srb.releaser_id,CAST(srb.releaser_id AS UNSIGNED INTEGER)),
								srb.releaser_id, fn_get_personell_name(srb.releaser_id)) AS releaser_name,
							srb.*
						FROM seg_radio_borrow AS srb
							LEFT JOIN care_test_request_radio AS r ON r.batch_nr=srb.batch_nr
								LEFT JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
									LEFT JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
										LEFT JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
								LEFT JOIN seg_radio_serv AS r_serv ON r.refno=r_serv.refno
									LEFT JOIN seg_radio_id AS sri ON sri.pid = r_serv.pid
						WHERE srb.status IN ('borrowed')
						$WHERE_SQL
						$ORDER_BY_OPTION";
#						GROUP BY srb.borrower_id
#r.status NOT IN ('deleted','hidden','inactive','void')
#echo "class_radiology.php : _searchRadioBorrowers : this->sql = $this->sql <br> \n ";
#exit();
		if($limit){
				$this->res['srb']=$db->SelectLimit($this->sql,$len,$so);
		}else{
				$this->res['srb']=$db->Execute($this->sql);
		}
		if ($this->res['srb']){
			if ($this->record_count=$this->res['srb']->RecordCount()) {
				$this->rec_count=$this->record_count; # workaround
				return $this->res['srb'];
			} else{ return FALSE; }
		}else{ return FALSE;	}
	}# end of function _searchRadioUnscheduled


	/**
	* Limited results search returning list of unscheduled radiology requests.
	*
	* This method gives the possibility to sort the results based on an item and sorting direction.
	* @access public
	* @param string Search keyword
	* @param int department number
	* @param int Maximum number of rows returned
	* @param int Start index of rows returned
	* @param string Item as sort basis
	* @param string Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
	* @return mixed adodb record object or boolean
	* @created burn: December 3, 2007
	*/
	function searchLimitRadioUnscheduled($key,$sub_dept_nr,$len,$so,$sortitem='',$order='ASC'){
		if(!empty($sortitem)){
			$option=" ORDER BY $sortitem $order";
		}
		return $this->_searchRadioUnscheduled($key,$sub_dept_nr,$option,TRUE,$len,$so);
	}# end of function searchLimitRadioUnscheduled



/*
SELECT sr_sked.*
FROM seg_radio_schedule AS sr_sked
WHERE sr_sked.scheduled_dt LIKE '$date %'
ORDER BY sr_sked.scheduled_dt ASC
*/

		/**
		*   Gets the list of radiology borrowers
		*
		*   @access public
		*   @param string Table name
		*   @return boolean OR Record set
		*   @created/modified burn: November 10, 2007; November 12, 2007
		*/
	function _searchRadioBorrowers($key,$sub_dept_nr='',$ORDER_BY_OPTION='',$limit=FALSE,$len=30,$so=0){
		global $db, $sql_LIKE, $root_path;

		$key = utf8_decode($key);

		$key=strtr($key,'*?','%_');
		$key=trim($key);
		#$suchwort=$searchkey;
		$key = str_replace("^","'",$key);
		$key=addslashes($key);
#edited by VAN 03-19-08
		//note $key value either refno or name, last, date_birth
		if(is_numeric($key)){
			if (strlen(trim($key))>8)
				$WHERE_SQL=" AND (sri.rid = '".addslashes($key)."') ";
			else
				$WHERE_SQL=" AND (srb.borrower_id = '".addslashes($key)."') ";
		}elseif($key=='%'||$key=='*'){
			$WHERE_SQL="";
		#added by VAN 03-18-08
		}elseif(empty($key)){
			$WHERE_SQL=" AND r_serv.request_date = DATE(NOW()) ";
		}else{
			if(stristr($key,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$key=strtr($key,',',' ');
			$cbuffer=explode(' ',$key);

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

			if(sizeof($comp)>1){
				#$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') ";
				$WHERE_SQL=" AND ((p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')
													OR (p2.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p2.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')) ";
			}else{
				$WHERE_SQL=" AND ((p.name_last $sql_LIKE '".addslashes($key)."%') OR (p2.name_last $sql_LIKE '".addslashes($key)."%')) ";
			}
		}

		include_once($root_path.'include/inc_date_format_functions.php');
		$date_format=getDateFormat();
			# Check if it is a complete date in mm/dd/yyyy format
		$this_date=@formatDate2STD($key,$date_format);
		if($this_date!='') {
			$WHERE_SQL =" AND (srb.date_borrowed='$this_date')";
		}

		if ($sub_dept_nr){
			$WHERE_SQL.=" AND r_serv_group.department_nr=".$sub_dept_nr;
		}

		$this->sql="SELECT IF(r_serv.is_cash=1,r_services.price_cash,r_services.price_charge) AS price,
							IF(fn_get_person_name(srb.borrower_id) IS NULL,fn_get_personell_name(srb.borrower_id),fn_get_person_name(srb.borrower_id)) AS borrower_name,
							IF(fn_get_person_name(srb.borrower_id) IS NULL,0,1) AS is_owner,
							r.service_code,
							r_serv_group.department_nr AS sub_dept_nr, dept.id AS sub_dept_id, dept.name_formal AS sub_dept_name,
							sri.rid, r_serv.pid,
							fn_get_person_name(r_serv.pid) AS patient_name,
							fn_get_person_name(srb.releaser_id) AS releaser_name,
							srb.*
						FROM seg_radio_borrow AS srb
							INNER JOIN care_test_request_radio AS r ON r.batch_nr=srb.batch_nr
								LEFT JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
									LEFT JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
										LEFT JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
								INNER JOIN seg_radio_serv AS r_serv ON r.refno=r_serv.refno
									LEFT JOIN seg_radio_id AS sri ON sri.pid = srb.borrower_id
									LEFT JOIN care_personell AS pr ON pr.nr=srb.borrower_id
									LEFT JOIN care_person AS p ON  p.pid=pr.pid
									LEFT JOIN care_person AS p2 ON  p2.pid=srb.borrower_id
						WHERE srb.status IN ('borrowed')
						$WHERE_SQL
						$ORDER_BY_OPTION";

		#echo "class_radiology.php : _searchRadioBorrowers : this->sql = $this->sql <br> \n ";

		if($limit){
				$this->res['srb']=$db->SelectLimit($this->sql,$len,$so);
		}else{
				$this->res['srb']=$db->Execute($this->sql);
		}
		if ($this->res['srb']){
			if ($this->record_count=$this->res['srb']->RecordCount()) {
				$this->rec_count=$this->record_count; # workaround
				return $this->res['srb'];
			} else{ return FALSE; }
		}else{ return FALSE;	}
	}# end of function _searchRadioBorrowers


	/**
	* Limited results search returning list of records of a Radiology Patient.
	*
	* This method gives the possibility to sort the results based on an item and sorting direction.
	* @access public
	* @param string Search keyword
	* @param int department number
	* @param int Maximum number of rows returned
	* @param int Start index of rows returned
	* @param string Item as sort basis
	* @param string Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
	* @return mixed adodb record object or boolean
	* @created burn: October 23, 2007
	*/
	#function searchLimitRadioBorrowers($key,$is_owner,$sub_dept_nr,$len,$so,$sortitem='',$order='ASC'){
	function searchLimitRadioBorrowers($key,$sub_dept_nr,$len,$so,$sortitem='',$order='ASC'){
		if(!empty($sortitem)){
			$option=" ORDER BY $sortitem $order";
		}
		return $this->_searchRadioBorrowers($key,$sub_dept_nr,$option,TRUE,$len,$so);
	}# end of function searchLimitRadioBorrowers

		/**
		*   Gets the records of a radiology patient
		*
		*   @access public
		*   @param string Table name
		*   @return boolean OR Record set
		*   @created/modified burn: October 23, 2007
		*/
	function _searchRadioPatientRecords($key,$sub_dept_nr='',$ORDER_BY_OPTION='',$limit=FALSE,$len=30,$so=0){
		global $db, $sql_LIKE, $root_path;

		#added by VAN 03-24-08
		$key = utf8_decode($key);
#edited by VAN 03-19-08
		$PID = $this->getRefCode();
		if(is_numeric($key)){
			#$key=(int)$key;
			$WHERE_SQL=" AND (r.batch_nr = '".addslashes($key)."') ";
		}elseif($key=='%'||$key=='*'){
			$WHERE_SQL="";
		}else{
			$WHERE_SQL=" AND (r.batch_nr = '".addslashes($key)."'
						OR r.service_code = '".addslashes($key)."'
						OR r_services.name = '".addslashes($key)."'
						OR IF((ISNULL(r.is_in_house) || r.is_in_house='0'),
								r.request_doctor,
								IF(STRCMP(r.request_doctor,CAST(r.request_doctor AS UNSIGNED INTEGER)),
									r.request_doctor, fn_get_personell_name(r.request_doctor)) ) $sql_LIKE '".addslashes($key)."%') ";
		}

		include_once($root_path.'include/inc_date_format_functions.php');
		$date_format=getDateFormat();
			# Check if it is a complete date in mm/dd/yyyy format
		$this_date=@formatDate2STD($key,$date_format);
		if($this_date!='') {
			$WHERE_SQL=" AND (r_serv.request_date='$this_date' OR r.service_date='$this_date')";
		}

		if ($sub_dept_nr){
			$WHERE_SQL.=" AND r_serv_group.department_nr='".$sub_dept_nr."'";
		}

		$this->sql="SELECT sri.rid, IF ((request_flag IS NOT NULL),1,0) AS hasPaid,
							(SUBSTRING(MAX(CONCAT(CONCAT(srb.date_borrowed,' ',srb.time_borrowed),srb.status)),20)) AS is_borrowed,
							r.batch_nr, r.create_dt,
							r_serv.refno, r_serv.request_date, r_serv.encounter_nr, r_serv.pid, r_serv.ordername,
							r_serv.orderaddress, r_serv.is_cash, r_serv.is_urgent, r_serv.comments, r_serv.history,
							r.clinical_info, r.service_code, r.price_cash, r.price_charge,
							r.service_date, r.is_in_house, r.request_doctor, r.status,
							IF((ISNULL(r.is_in_house) || r.is_in_house='0'),
								r.request_doctor,
								IF(STRCMP(r.request_doctor,CAST(r.request_doctor AS UNSIGNED INTEGER)),
									r.request_doctor, fn_get_personell_name(r.request_doctor)) ) AS request_doctor_name,
							r_services.name AS service_name, r_serv_group.group_code ,
							r_serv_group.department_nr AS sub_dept_nr, dept.id AS sub_dept_id, dept.name_formal AS sub_dept_name
						FROM seg_radio_serv AS r_serv
							LEFT JOIN care_test_request_radio AS r ON r.refno=r_serv.refno
							LEFT JOIN seg_radio_id AS sri ON sri.pid=r_serv.pid
								LEFT JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
									LEFT JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
										LEFT JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
										LEFT JOIN seg_radio_borrow AS srb ON srb.batch_nr=r.batch_nr
						WHERE r_serv.status NOT IN ($this->dead_stat)
							/*AND r.status='done'*/
							AND (r_serv.pid  = '$PID')
						$WHERE_SQL
						GROUP BY r.batch_nr
						$ORDER_BY_OPTION ";
#echo "class_radiology.php : _searchRadioPatientRecords : this->sql = $this->sql <br> \n ";
#exit();
		if($limit){
				$this->res['srpr']=$db->SelectLimit($this->sql,$len,$so);
		}else{
				$this->res['srpr']=$db->Execute($this->sql);
		}
		if ($this->res['srpr']){
			if ($this->record_count=$this->res['srpr']->RecordCount()) {
				$this->rec_count=$this->record_count; # workaround
				return $this->res['srpr'];
			} else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}# end of function _searchRadioPatientRecords

	#added by VAN 07-24-08
	function getBorrowedInfo($batch_nr){
			global $db;

		 $this->sql="SELECT br.date_borrowed, br.time_borrowed, br.date_returned, br.time_returned,
								fn_get_pid_lastfirstmi(br.borrower_id) AS borrower_name
							,br.borrower_id, IF(br.borrower_id, pr.pid,r_serv.pid) AS pid_borrower,br.remarks,
							IF ((br.date_borrowed!='0000-00-00' AND br.date_returned='0000-00-00'),1,0) AS is_borrowed,
						 CONCAT(
							CAST((SELECT name_first FROM care_person AS p WHERE p.pid=IF(br.borrower_id, pr.pid,r_serv.pid)) AS BINARY),
							' ',
							SUBSTRING((SELECT name_middle FROM care_person AS p WHERE p.pid=IF(br.borrower_id, pr.pid,r_serv.pid)), 1, 1),
							IF(
								(SELECT name_middle FROM care_person AS p WHERE p.pid=IF(br.borrower_id, pr.pid,r_serv.pid))='', ' ','. '
							),
							(SELECT name_last FROM care_person AS p WHERE p.pid=IF(br.borrower_id, pr.pid,r_serv.pid))) AS borrower
							FROM seg_radio_borrow AS br
							LEFT JOIN care_personell AS pr ON pr.nr=br.borrower_id
							LEFT JOIN care_test_request_radio AS r ON r.batch_nr=br.batch_nr
							LEFT JOIN seg_radio_serv AS r_serv ON r_serv.refno=r.refno
							WHERE br.batch_nr='".$batch_nr."'
							order by br.date_borrowed DESC, br.time_borrowed DESC LIMIT 1";

			if ($this->result=$db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
						#if ($id)
					#return $this->result->FetchRow();
					#return $this->result;
				#else
					return $this->result;
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}
	#-----------------------

	/**
	* Limited results search returning list of records of a Radiology Patient.
	*
	* This method gives the possibility to sort the results based on an item and sorting direction.
	* @access public
	* @param string Search keyword
	* @param int department number
	* @param int Maximum number of rows returned
	* @param int Start index of rows returned
	* @param string Item as sort basis
	* @param string Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
	* @return mixed adodb record object or boolean
	* @created burn: October 23, 2007
	*/
	function searchLimitRadioPatientRecords($key,$sub_dept_nr,$len,$so,$sortitem='',$order='ASC'){
		if(!empty($sortitem)){
			$option=" ORDER BY $sortitem $order";
		}
		return $this->_searchRadioPatientRecords($key,$sub_dept_nr,$option,TRUE,$len,$so);
	}# end of function searchLimitRadioPatientRecords

		/**
		*   Get the list of radiology patients
		*
		* @access public
		* @param string Search keyword
		* @param int department number
		* @param int Maximum number of rows returned
		* @param int Start index of rows returned
		* @param string Item as sort basis
		* @param string Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
		* @return mixed adodb record object or boolean
		*   @created/modified burn: Oct. 18, 2007
		*/
	function _searchBasicInfoRadioPatientList($key,$sub_dept_nr='',$ORDER_BY_OPTION='',$limit=FALSE,$len=30,$so=0){
		global $db, $sql_LIKE, $root_path;
#edited by VAN 03-19-08
/*
echo "class_radiology.php : _searchBasicInfoRadioPatientList : 1 key ='".$key."' <br> \n ";
$tempSKey = explode("&",$key);
$key = $tempSKey[0];
echo "class_radiology.php : _searchBasicInfoRadioPatientList : tempSKey ='".$tempSKey."' <br> \n ";
echo "class_radiology.php : _searchBasicInfoRadioPatientList : tempSKey : "; print_r($tempSKey); echo" <br> \n ";

echo "class_radiology.php : _searchBasicInfoRadioPatientList : 1 key ='".$key."' <br> \n ";
echo "class_radiology.php : _searchBasicInfoRadioPatientList : sub_dept_nr ='".$sub_dept_nr."' <br> \n ";
*/
		#added by VAN 03-24-08
		$key = utf8_decode($key);
		if(is_numeric($key)){
			#$key=(int)$key;
			$WHERE_SQL=" AND (p.pid = '".addslashes($key)."' OR sri.rid = '".addslashes($key)."' /*OR r.batch_nr = '".addslashes($key)."'*/) ";
		}elseif($key=='%'||$key=='*'){
			$WHERE_SQL="";
#		}elseif(substr($key, 0, 8)=="dept_nr="){
#		substr_compare ( $key, string str, int offset [, int length [, bool case_sensitivity]])
# Provides: <body text='black'>
#$bodytag = str_replace("%body%", "black", "<body text='%body%'>");
#			$whereSQL="AND $key";
#			$whereSQL="AND ".str_replace("dept_nr=", "r_serv_group.department_nr=", $key);
		}else{
			/*
			$WHERE_SQL=" AND (sri.rid = '".addslashes($key)."'
						OR p.pid = '".addslashes($key)."'
						OR r.batch_nr = '".addslashes($key)."'
						OR p.name_last $sql_LIKE '".addslashes($key)."%'
						OR p.date_birth $sql_LIKE '".addslashes($key)."%') ";
			*/
			if(stristr($key,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$key=strtr($key,',',' ');
			$cbuffer=explode(' ',$key);

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

			if(sizeof($comp)>1){
				#$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') ";
				$WHERE_SQL=" AND ((p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')
											/*OR (p.name_last $sql_LIKE '".strtr($key,'+',' ')."%' )
									OR (r_serv.ordername $sql_LIKE '".strtr($key,'+',' ')."%')*/)";
			}else{
				$WHERE_SQL=" AND (p.name_last $sql_LIKE '".addslashes($key)."%'
						/*OR r_serv.ordername $sql_LIKE '".addslashes($key)."%'*/) ";
			}
		}
/*
if (((!empty($tempSKey[1])) || (trim($tempSKey[1])!='')) &&
	(substr($tempSKey[1], 0, 8)=="dept_nr=")){
	$whereSQL.=" AND ".str_replace("dept_nr=", "r_serv_group.department_nr=", $tempSKey[1]);
}*/
		include_once($root_path.'include/inc_date_format_functions.php');
		$date_format=getDateFormat();
			# Check if it is a complete date in mm/dd/yyyy format
		$this_date=@formatDate2STD($key,$date_format);
		if($this_date!='') {
			$WHERE_SQL=" AND p.date_birth='$this_date' ";
		}

		if ($sub_dept_nr){
			$WHERE_SQL.=" AND r_serv_group.department_nr='".$sub_dept_nr."'";
		}

		$this->sql="SELECT sri.rid, r_serv.refno,dept.nr, r.batch_nr,
							p.pid,p.name_last,p.name_first,p.date_birth,p.addr_zip, p.sex, p.death_date, p.status,
							p.street_name,sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name
						FROM seg_radio_id AS sri
							INNER JOIN care_person AS p ON p.pid=sri.pid
								LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
									LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
										LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
											LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
							INNER JOIN seg_radio_serv AS r_serv ON r_serv.pid=sri.pid
								INNER JOIN care_test_request_radio AS r ON r.refno=r_serv.refno
									INNER JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
										INNER JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
											INNER JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
						WHERE r_serv.status NOT IN ($this->dead_stat)
						$WHERE_SQL
						GROUP BY p.pid
						$ORDER_BY_OPTION ";
#							AND r.status NOT IN ($this->dead_stat)
#r.status NOT IN ('deleted','hidden','inactive','void')
# AND r_serv.hasPaid = 1
#echo "class_radiology.php : _searchBasicInfoRadioPatientList : this->sql = $this->sql <br> \n ";
#exit();
		if($limit){
				$this->res['sabi']=$db->SelectLimit($this->sql,$len,$so);
		}else{
				$this->res['sabi']=$db->Execute($this->sql);
		}
		if ($this->res['sabi']){
			if ($this->record_count=$this->res['sabi']->RecordCount()) {
				$this->rec_count=$this->record_count; # workaround
#				echo "class_radiology.php : _searchBasicInfoRadioPatientList :  TRUE <br>";
				return $this->res['sabi'];
			} else{
#				echo "_searchBasicInfoRadioPatientList : FALSE 01 <br>";
				return FALSE;
			}
		}else{
#			echo "class_radiology.php : _searchBasicInfoRadioPatientList : FALSE 02 <br>";
			return FALSE;
		}
	}# end of function _searchBasicInfoRadioPatientList

	/**
	* Limited results search returning list of Radiology Patients.
	*
	* This method gives the possibility to sort the results based on an item and sorting direction.
	* @access public
	* @param string Search keyword
	* @param int department number
	* @param int Maximum number of rows returned
	* @param int Start index of rows returned
	* @param string Item as sort basis
	* @param string Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
	* @return mixed adodb record object or boolean
	* @created burn: Oct. 18, 2007
	*/
	function searchLimitBasicInfoRadioPatientList($key,$sub_dept_nr,$len,$so,$sortitem='',$order='ASC'){
		if(!empty($sortitem)){
			$option=" ORDER BY $sortitem $order";
		}
		return $this->_searchBasicInfoRadioPatientList($key,$sub_dept_nr,$option,TRUE,$len,$so);
	}# end of function searchLimitBasicInfoRadioPatientList


		/**
		*   Gets the list of pending [pending&for referral status] radiology test requests
		*
		* @param string search type either Pending or Unscheduled requests
		* @param string Search keyword
		* @param int department number
		* @param string Item as sort basis & Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
		* @param boolean, to limit the number of search results or not
		* @param int Maximum number of rows returned
		* @param int Start index of rows returned
		* @return boolean OR mixed adodb record object
		*/
	function _searchBasicInfoRadioPending($search_type='',$key,$sub_dept_nr='',$ORDER_BY_OPTION='',$limit=FALSE,$len=30,$so=0){
		global $db, $sql_LIKE, $root_path;
		#added by VAN 03-24-08
		$key = utf8_decode($key);

		if(is_numeric($key)){
			#$key=(int)$key;
			$WHERE_SQL=" AND (r_serv.pid = '".addslashes($key)."' OR sri.rid = '".addslashes($key)."') ";
		}elseif($key=='%'||$key=='*'){
			$WHERE_SQL="";

		}else{
			#echo "key = ".$key;
			if ($key){
				if(stristr($key,',')){
					$lastnamefirst=TRUE;
				}else{
					$lastnamefirst=FALSE;
				}

				#$searchkey=strtr($key,',',' ');
				$cbuffer=explode(',',$key);

				# Remove empty variables
				for($x=0;$x<sizeof($cbuffer);$x++){
					$cbuffer[$x]=trim($cbuffer[$x]);
					if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
				}

				# Arrange the values, ln= lastname, fn=first name, bd = birthday
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
					$WHERE_SQL=" AND ((p.name_last $sql_LIKE '$ln%' AND p.name_first $sql_LIKE '$fn%'))";
				}#else
				// 	$WHERE_SQL=" AND (p.name_last $sql_LIKE '".addslashes($key)."%') ";
			}else
				$WHERE_SQL="";

			if ($key == 'null'){
				#echo "here";
				$sql_now = " AND r_serv.request_date = DATE(NOW()) ";
			}else{
				$sql_now = "";
			}
		}

		include_once($root_path.'include/inc_date_format_functions.php');
		$date_format=getDateFormat();
			# Check if it is a complete date in mm/dd/yyyy format
		$this_date=@formatDate2STD($key,$date_format);
		if($this_date!='') {
            $WHERE_SQL=" AND (r_serv.request_date='$this_date') ";
		}
		if ($sub_dept_nr){
			$WHERE_SQL.=" AND r_serv_group.department_nr='".$sub_dept_nr."'";
		}
		if ($search_type=='unscheduled'){
			$WHERE_SQL.=" AND r.batch_nr NOT IN (SELECT batch_nr FROM seg_radio_schedule)";
		}

		#added by VAN 07-10-08
		#$with_film = " AND (SELECT sz.batch_nr FROM seg_radio_service_sized AS sz WHERE sz.batch_nr=r.batch_nr LIMIT 1)!='' ";
		if ($search_type=='unscheduled')
			$with_film = " AND NOT EXISTS (SELECT sz.batch_nr FROM seg_radio_service_sized AS sz WHERE sz.batch_nr=r.batch_nr) ";
		else
			$with_film = " AND EXISTS (SELECT sz.batch_nr FROM seg_radio_service_sized AS sz WHERE sz.batch_nr=r.batch_nr) ";
		#$with_film = "";
		#added by VAN 07-07-08
		#if (empty($ORDER_BY_OPTION))
		$ORDER_BY_OPTION = " ORDER BY r.is_served desc, r_serv.request_date DESC, p.name_last ASC, p.name_first ASC ";


        /**
         * Check for request expiration
         */
        $joins = array();
        $fields = array();
        $having = array();
        if (!empty($sub_dept_nr)) {
            $row = $db->GetRow(
                "SELECT value,unit " .
                "FROM seg_request_expiration_department " .
                "WHERE module='radiology' AND department=" .
                $db->qstr($sub_dept_nr)
            );
            if (!empty($row)) {
                $having[] = "IF(is_served, 1, r_serv.request_date >= (DATE(NOW()- INTERVAL {$row['value']} {$row['unit']})))";
            }
        } else {
            // sub department is not specified (i.e., ALL)
            $fields[] = '`exp`.value';
            $fields[] = '`exp`.unit';
            $joins[] = "LEFT JOIN seg_request_expiration_department `exp` ON module='radiology' AND department=r_serv_group.department_nr";
            $having[] = "(CASE `exp`.`unit` " .
                "WHEN 'DAY' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` DAY)) " .
                "WHEN 'WEEK' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` WEEK)) " .
                "WHEN 'MONTH' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` MONTH)) " .
                "WHEN 'YEAR' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` YEAR)) " .
                "ELSE 1 " .
            "END)";
        }

        $this->sql="SELECT " .
                "(SELECT sz.batch_nr FROM seg_radio_service_sized AS sz WHERE sz.batch_nr=r.batch_nr LIMIT 1) AS film_size, " .
                "sri.rid, e.encounter_type, " .
                "IF ((request_flag IS NOT NULL),1,0) AS hasPaid, r.request_flag, " .
                "r.batch_nr, r.create_dt, " .
                "r_serv.refno, r_serv.request_date, r_serv.encounter_nr, r_serv.pid, r_serv.ordername, " .
                "r_serv.orderaddress, r_serv.is_cash, r_serv.is_urgent, r_serv.comments, " .
                "r_serv.history, r.clinical_info, r.service_code, " .
                "r.price_cash, r.price_charge, r.service_date, r.is_in_house, r.request_doctor, r.status,r.remarks, " .
                "IF((ISNULL(r.is_in_house) || r.is_in_house='0'), " .
                    "r.request_doctor, " .
                    "IF(STRCMP(r.request_doctor,CAST(r.request_doctor AS UNSIGNED INTEGER)), " .
                        "r.request_doctor, fn_get_personell_name(r.request_doctor)) ) AS request_doctor_name, " .
                "r_services.name, r_serv_group.group_code , " .
                "r_serv_group.department_nr AS sub_dept_nr,dept.name_short, dept.id AS sub_dept_id, dept.name_formal AS sub_dept_name, " .
                "p.name_last, p.name_first, p.date_birth, p.sex, r.is_served " .
                ($fields ? ','. implode(",", $fields) : '') . ' ' .
            "FROM seg_radio_serv AS r_serv " .
                "INNER JOIN care_test_request_radio AS r ON r.refno=r_serv.refno " .
                "LEFT JOIN care_encounter AS e ON e.encounter_nr=r_serv.encounter_nr " .
                "INNER JOIN care_person AS p ON p.pid = r_serv.pid " .
                "INNER JOIN seg_radio_id AS sri ON sri.pid=r_serv.pid " .
                "INNER JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code " .
                "INNER JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code " .
                "INNER JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr " .
                "LEFT JOIN seg_radio_service_sized AS sz ON sz.batch_nr=r.batch_nr " .
                ($joins ? implode(' ', $joins) : '') . ' ' .
            "WHERE r_serv.status NOT IN ($this->dead_stat) " .
                "AND r.status NOT IN ($this->dead_stat,'done') " .
                "AND (request_flag IS NOT NULL OR r_serv.is_urgent=1 OR r_serv.is_cash=0 OR (r.parent_batch_nr!='' AND r.parent_refno)) " .
            "{$WHERE_SQL} " .
						/*$with_film*/
            "{$sql_now} " .
            "GROUP BY r.batch_nr " .
            ($having ? ('HAVING ('.implode(")\n AND (", $having) . ") ") : '') .
            "{$ORDER_BY_OPTION} ";

		if($limit){
				$this->res['sabi']=$db->SelectLimit($this->sql,$len,$so);
		}else{
				$this->res['sabi']=$db->Execute($this->sql);
		}

		if ($this->res['sabi']){
			if ($this->record_count=$this->res['sabi']->RecordCount()) {
				$this->rec_count=$this->record_count; # workaround
#				echo "class_radiology.php : _searchBasicInfoRadioPending :  TRUE <br>";
				return $this->res['sabi'];
			} else{
#				echo "_searchBasicInfoRadioPending : FALSE 01 <br>";
				return FALSE;
			}
		}else{
#			echo "class_radiology.php : _searchBasicInfoRadioPending : FALSE 02 <br>";
			return FALSE;
		}
	}# end of function _searchBasicInfoRadioPending

		/*
		*  @since 02-11-09
		*  @author Raissa
		*  @access public
		*  @internal Function for retrieving the list of batch Requests for radiology
		*  @package incluce
		*  @subpackage care_api_classes
		*  @param String pid
		*  @param Integer maxcount
		*  @param Integer offset
		*  @return Array resultset
		*  @return Boolean false to indicate failure in the query
		*/
		function UnifiedBatchList($pid='',$maxcount=100,$offset=0){
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;


                #edited by VAN 02-28-2013
                #add the code that exclude the deleted requests
                $this->sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT r_serv.refno, r_serv.request_date,
												IF((r_serv.is_cash=0 && request_flag IS NULL), 'Charge', r.request_flag) AS or_no
												FROM seg_radio_serv AS r_serv
												INNER JOIN care_test_request_radio AS r ON r.refno=r_serv.refno
												WHERE r_serv.pid='".$pid."'
												AND (request_flag IS NOT NULL OR r_serv.is_urgent=1 OR r_serv.is_cash=0)
                                                AND r_serv.STATUS NOT IN ('deleted','hidden','inactive','void')
                                                AND r.STATUS NOT IN ('deleted','hidden','inactive','void')
												GROUP BY r_serv.refno
												ORDER BY r_serv.request_date DESC, r_serv.refno ASC";
				//echo $this->sql;

				if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
				}else{return false;}
		}
		/*
		*  @since 02-11-09
		*  @author Raissa
		*  @access public
		*  @internal Function for counting the list of batch Requests for radiology
		*  @package incluce
		*  @subpackage care_api_classes
		*  @param String pid
		*  @param Integer maxcount
		*  @param Integer offset
		*  @return Array resultset
		*  @return Boolean false to indicate failure in the query
		*/
		function countUnifiedBatchList($pid='',$maxcount=100,$offset=0) {
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

                #edited by VAN 02-28-2013
                #add the code that exclude the deleted requests
				$this->sql = "SELECT DISTINCT r_serv.refno, r_serv.request_date, IF((r_serv.is_cash=0 && request_flag IS NULL), 'Charge', r.request_flag) AS or_no
												FROM seg_radio_serv AS r_serv
												INNER JOIN care_test_request_radio AS r ON r.refno=r_serv.refno
												WHERE r_serv.pid='".$pid."'
												AND (request_flag IS NOT NULL OR r_serv.is_urgent=1 OR r_serv.is_cash=0)
                                                AND r_serv.STATUS NOT IN ('deleted','hidden','inactive','void')
                                                AND r.STATUS NOT IN ('deleted','hidden','inactive','void')
												GROUP BY r_serv.refno
												ORDER BY r_serv.request_date DESC, r_serv.refno ASC";

				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}
		/*
		*  @since 02-11-09
		*  @author Raissa
		*  @access public
		*  @internal Function for retrieving the list of Requests per batch for radiology
		*  @package incluce
		*  @subpackage care_api_classes
		*  @param String refno
		*  @param Integer maxcount
		*  @param Integer offset
		*  @return Array resultset
		*  @return Boolean false to indicate failure in the query
		*/
		function UnifiedBatchRequestList($refno='',$maxcount=100,$offset=0){
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				$this->sql = "SELECT SQL_CALC_FOUND_ROWS r.is_served, r.served_date, r.batch_nr, 
                                r.service_code, serv.name,r.remarks,
                                d.name_formal, name_short, d.id
												FROM care_test_request_radio as r
												INNER JOIN seg_radio_serv as r_serv ON r_serv.refno = r.refno
												INNER JOIN seg_radio_services as serv ON serv.service_code = r.service_code
                                INNER JOIN seg_radio_service_groups AS g ON g.group_code=serv.group_code
                                INNER JOIN care_department AS d ON d.nr=g.department_nr
												WHERE r_serv.refno='".$refno."'";

				if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
				}else{return false;}
		}
		/*
		*  @since 02-11-09
		*  @author Raissa
		*  @access public
		*  @internal Function for counting the list of Requests per batch for radiology
		*  @package incluce
		*  @subpackage care_api_classes
		*  @param String refno
		*  @param Integer maxcount
		*  @param Integer offset
		*  @return Array resultset
		*  @return Boolean false to indicate failure in the query
		*/
		function countUnifiedBatchRequestList($refno='',$maxcount=100,$offset=0) {
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				$this->sql = "SELECT r.batch_nr, r.service_code, serv.name
												FROM care_test_request_radio as r
												INNER JOIN seg_radio_serv as r_serv ON r_serv.refno = r.refno
												INNER JOIN seg_radio_services as serv ON serv.service_code = r.service_code
												WHERE r_serv.refno='".$refno."'";

				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}
		/*
		*  @since 02-13-09
		*  @author Raissa
		*  @access public
		*  @internal Function for retrieving the list of Requests per batch, including all data, for radiology
		*  @package incluce
		*  @subpackage care_api_classes
		*  @param String refno
		*  @param Integer maxcount
		*  @param Integer offset
		*  @return Array resultset
		*  @return Boolean false to indicate failure in the query
		*/
		function getAllInfoUnifiedBatchRequestList($refno=''){
				global $db, $sql_LIKE, $root_path, $date_format;

				$this->sql = "SELECT r_serv.pid,r_request.`served_date`, r_findings.`create_dt`, r_serv.refno, r_serv.encounter_nr, r_request.batch_nr AS id, r_serv.is_urgent,
												r_findings.findings, r_findings.radio_impression, r_findings.findings_date, r_findings.doctor_in_charge,
												r_findings.result_status, r_findings.encoder AS findings_encoder,
												IF((ISNULL(r_request.is_in_house) || r_request.is_in_house='0'),
														r_request.request_doctor,
														IF(STRCMP(r_request.request_doctor,CAST(r_request.request_doctor AS UNSIGNED INTEGER)),
																r_request.request_doctor, fn_get_personell_name(r_request.request_doctor)) ) AS request_doctor_name,
												r_request.request_doctor, r_request.refno, r_request.batch_nr, r_request.clinical_info, r_request.service_code,
												r_request.service_date, r_request.is_in_house, r_request.request_date, r_request.status, r_request.encoder AS request_encoder,
												r_request.price_cash AS price_net, r_request.price_cash_orig, r_request.price_charge,
												r_services.name AS service_name,
												r_serv_group.group_code AS group_code, r_serv_group.name AS group_name,
												r_serv_group.other_name, r_serv_group.department_nr AS service_dept_nr,
												(SELECT dept.name_formal
														FROM care_personell_assignment AS cpa
																INNER JOIN care_department AS dept ON cpa.location_nr = dept.nr
														WHERE cpa.personell_nr=r_request.request_doctor LIMIT 1) AS request_dept_name,
												dept.name_formal AS service_dept_name,
												enc.encounter_type, enc.current_ward_nr, enc.current_room_nr, enc.in_ward,
												sri.rid, cw.ward_id, cw.name AS ward_name,
												IF((r_serv.is_cash=0 && request_flag IS NULL), 'Charge', r_request.request_flag) AS request_flag, '' AS amount_or
												FROM care_test_request_radio AS r_request
												INNER JOIN seg_radio_serv AS r_serv ON r_serv.refno = r_request.refno
												LEFT JOIN care_test_findings_radio AS r_findings ON r_request.batch_nr = r_findings.batch_nr
												INNER JOIN seg_radio_services AS r_services ON r_request.service_code = r_services.service_code
												INNER JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
												INNER JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
												LEFT JOIN care_encounter AS enc ON enc.encounter_nr=r_serv.encounter_nr
												LEFT JOIN care_ward AS cw ON cw.nr = enc.current_ward_nr
												INNER JOIN seg_radio_id AS sri ON sri.pid=r_serv.pid
												WHERE r_request.refno='$refno'
												AND r_request.status NOT IN ($this->dead_stat)
												ORDER BY r_request.batch_nr ASC";
		//echo $this->sql;
				if ($buf=$db->Execute($this->sql)){
						if($this->count=$buf->RecordCount()) {
								if ($limit)
										return $buf->FetchRow();
								else
										return $buf;
						}else { return FALSE; }
				}else { return FALSE; }
		}

		/**
				* @author Raissa
				* @since 02-10-2009
				* @access public
				* @internal Function that gets the list of patients with radiology test requests
				* @package include
				* @subpackage care_api_classes
				* @param string search type either Pending or Unscheduled requests
				* @param string Search keyword
				* @param int department number
				* @param boolean, to limit the number of search results or not
				* @param int Maximum number of rows returned
				* @param int Start index of rows returned
				* @return boolean OR mixed adodb record object
				*/
		function searchRadioPatients($search_type='',$key,$sub_dept_nr='',$limit=FALSE,$len=30,$so=0){
				global $db, $sql_LIKE, $root_path;
				$key = utf8_decode($key);

				if(is_numeric($key)){
						$WHERE_SQL=" AND (r_serv.pid = '".addslashes($key)."' OR sri.rid = '".addslashes($key)."') ";
				}elseif($key=='%'||$key=='*'){
						$WHERE_SQL="";
				}else{
						if ($key){

								/*$WHERE_SQL=" AND (sri.rid = '".addslashes($key)."'
												OR p.pid = '".addslashes($key)."'
												OR p.name_last $sql_LIKE '".addslashes($key)."%'
												OR p.name_first $sql_LIKE '%".addslashes($key)."%') ";
								*/
								if(stristr($key,',')){
										$lastnamefirst=TRUE;
								}else{
										$lastnamefirst=FALSE;
								}
								$cbuffer=explode(',',$key);
								for($x=0;$x<sizeof($cbuffer);$x++){
										$cbuffer[$x]=trim($cbuffer[$x]);
										if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
								}
								if($lastnamefirst){
										$fn=$comp[1];
										$ln=$comp[0];
										$rd=$comp[2];
								}else{
										$fn=$comp[0];
										$ln=$comp[1];
										$rd=$comp[2];
								}
								if(sizeof($comp)>1){
										$WHERE_SQL=" AND (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";
								}else
										$WHERE_SQL=" AND (p.name_last $sql_LIKE '".addslashes($key)."%') ";
						}else
								$WHERE_SQL="";
				}
				if ($sub_dept_nr){
						$WHERE_SQL.=" AND r_serv_group.department_nr='".$sub_dept_nr."'";
				}
				$ORDER_BY_OPTION = " ORDER BY p.name_last ASC, p.name_first ASC ";

				$this->sql="SELECT DISTINCT sri.rid, p.name_last, p.name_first, e.encounter_type, dept.name_formal as name_short, r_serv.pid
												FROM seg_radio_serv as r_serv
														INNER JOIN seg_radio_id AS sri ON sri.pid=r_serv.pid
														INNER JOIN care_person AS p ON p.pid = r_serv.pid
														INNER JOIN care_test_request_radio AS r ON r.refno=r_serv.refno
																LEFT JOIN care_encounter AS e ON e.encounter_nr=r_serv.encounter_nr
																		LEFT JOIN care_department AS dept ON e.current_dept_nr = dept.nr
																LEFT JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
																LEFT JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
												WHERE r_serv.status NOT IN ('deleted','hidden','inactive','void')
												$WHERE_SQL
												GROUP BY sri.rid
												$ORDER_BY_OPTION ";
				//echo $this->sql;
				if($limit){
						$this->res['sabi']=$db->SelectLimit($this->sql,$len,$so);
				}else{
						$this->res['sabi']=$db->Execute($this->sql);
				}
				if ($this->res['sabi']){
						if ($this->record_count=$this->res['sabi']->RecordCount()) {
								$this->rec_count=$this->record_count; # workaround
								return $this->res['sabi'];
						} else{
								return FALSE;
						}
				}else{
						return FALSE;
				}
		}# end of function searchRadioPatients

	/**
	* Limited results search returning basic information as outlined at <var>_searchBasicInfoRadioPending()</var>.
	*
	* This method gives the possibility to sort the results based on an item and sorting direction.
	* @access public
	* @param string search type either Pending or Unscheduled requests
	* @param string Search keyword
	* @param int department number
	* @param int Maximum number of rows returned
	* @param int Start index of rows returned
	* @param string Item as sort basis
	* @param string Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
	* @return mixed adodb record object or boolean
	* @created/modified burn: Oct. 2, 2006
	*/
#	function searchLimitEncounterBasicInfoPending($key,$len,$so,$sortitem='',$order='ASC'){
	function searchLimitBasicInfoRadioPending($search_type='',$key,$sub_dept_nr,$len,$so,$sortitem='',$order='ASC'){
		if(!empty($sortitem)){
			$option=" ORDER BY $sortitem $order";
		}
		return $this->_searchBasicInfoRadioPending($search_type,$key,$sub_dept_nr,$option,TRUE,$len,$so);
	}# end of function searchLimitBasicInfoRadioPending




		/**
		*   Gets the list of DONE [done encoding the results and ready for printing] radiology test requests
		*
		* @access public
		* @param string Search keyword
		* @param int department number
		* @param string Item as sort basis & Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
		* @param boolean, to limit the number of search results or not
		* @param int Maximum number of rows returned
		* @param int Start index of rows returned
		* @return boolean OR mixed adodb record object
		* @created burn: November 19, 2007
		*/
	function _searchBasicInfoRadioDone($key,$sub_dept_nr='',$ORDER_BY_OPTION='',$limit=FALSE,$len=30,$so=0, $is_doctor=0, $encounter_nr='', $cond=''){
		global $db, $sql_LIKE, $root_path;
		#added by VAN 03-24-08
		$key = utf8_decode($key);

			if ($key == 'null'){
				#echo "here";
				$sql_now = " AND r_serv.request_date = DATE(NOW()) ";
			}else{
				$sql_now = "";
			}

		if(is_numeric($key)){
			#$key=(int)$key;
			$WHERE_SQL=" AND (r_serv.pid = '".addslashes($key)."' OR sri.rid = '".addslashes($key)."') ";
		}elseif($key=='%'||$key=='*'){
			$WHERE_SQL="";
		}else{

			if(stristr($key,',')){
					$lastnamefirst=TRUE;
				}else{
					$lastnamefirst=FALSE;
				}

				#$searchkey=strtr($key,',',' ');
				$cbuffer=explode(',',$key);

				# Remove empty variables
				for($x=0;$x<sizeof($cbuffer);$x++){
					$cbuffer[$x]=trim($cbuffer[$x]);
					if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
				}

				# Arrange the values, ln= lastname, fn=first name, bd = birthday
				if($lastnamefirst){
					$fn=$comp[1];
					$ln=$comp[0];
					$rd=$comp[2];
				}else{
					$fn=$comp[0];
					$ln=$comp[1];
					$rd=$comp[2];
				}
			// # Check the size of the comp
				if(sizeof($comp)>1){
					$WHERE_SQL=" AND ((p.name_last $sql_LIKE '$ln%' AND p.name_first $sql_LIKE '$fn%') )";
				}#else
			// 		$WHERE_SQL=" AND (p.name_last $sql_LIKE '".addslashes($key)."%') ";
		}

		include_once($root_path.'include/inc_date_format_functions.php');
		$date_format=getDateFormat();
			# Check if it is a complete date in mm/dd/yyyy format
		$this_date=@formatDate2STD($key,$date_format);
		if($this_date!='') {
			$WHERE_SQL=" AND (DATE(r_serv.request_date)='$this_date') ";
		}
		if ($sub_dept_nr){
			$WHERE_SQL.=" AND r_serv_group.department_nr='".$sub_dept_nr."'";
		}

		$ORDER_BY_OPTION = " ORDER BY r_serv.request_date DESC, p.name_last ASC, p.name_first ASC ";

		if ($is_doctor)
			$is_dr_con = " AND r_serv.encounter_nr='".$encounter_nr."' ";
		else
			$is_dr_con = "";

		if ($cond)
			$is_dr_con = $cond;
							 $this->sql="SELECT sri.rid, e.encounter_type,
														e.current_ward_nr,e.current_room_nr,e.current_dept_nr,
														r.batch_nr, r.create_dt,
														r_serv.refno, r_serv.request_date, r_serv.encounter_nr, r_serv.pid, r_serv.ordername,
														r_serv.orderaddress, r_serv.is_cash, r_serv.is_urgent, r_serv.comments,
														r_serv.history, r.clinical_info, r.service_code,
														r.price_cash, r.price_charge, r.service_date, r.is_in_house, r.request_doctor, r.status,r.remarks,
														IF((ISNULL(r.is_in_house) || r.is_in_house='0'),
																r.request_doctor,
																IF(STRCMP(r.request_doctor,CAST(r.request_doctor AS UNSIGNED INTEGER)),
																		r.request_doctor, fn_get_personell_name(r.request_doctor)) ) AS request_doctor_name,
														r_services.service_code, r_services.name, r_serv_group.group_code ,
														r_serv_group.department_nr AS sub_dept_nr, dept.id AS sub_dept_id, dept.name_formal AS sub_dept_name,
														dept.name_short AS dept_short_name,
														p.name_last, p.name_first, p.date_birth, p.sex
												FROM seg_radio_serv AS r_serv
														INNER JOIN care_test_request_radio AS r ON r.refno=r_serv.refno
														INNER JOIN care_person AS p ON p.pid = r_serv.pid
														INNER JOIN seg_radio_id AS sri ON sri.pid=r_serv.pid
																INNER JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
																		INNER JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
																				INNER JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
																				LEFT JOIN care_encounter AS e ON e.encounter_nr=r_serv.encounter_nr
												WHERE r_serv.status NOT IN ($this->dead_stat)
														AND r.status='done'
														$is_dr_con
												$WHERE_SQL
												$sql_now
												$ORDER_BY_OPTION ";

#echo "class_radiology.php : _searchBasicInfoRadioDone : this->sql = $this->sql <br> \n ";
#exit();
		#added by VAN 07-31-08
		#just get the total # of records
		if ($is_doctor){
			$this->res['sabi']=$db->Execute($this->sql);
			$this->record_tcount=$this->res['sabi']->RecordCount();
		}

		if($limit){
				$this->res['sabi']=$db->SelectLimit($this->sql,$len,$so);
		}else{
				$this->res['sabi']=$db->Execute($this->sql);
		}

		if ($this->res['sabi']){
			if ($this->record_count=$this->res['sabi']->RecordCount()) {
				$this->rec_count=$this->record_count; # workaround
#				echo "class_radiology.php : _searchBasicInfoRadioDone :  TRUE <br>";
				return $this->res['sabi'];
			} else{
#				echo "_searchBasicInfoRadioDone : FALSE 01 <br>";
				return FALSE;
			}
		}else{
#			echo "class_radiology.php : _searchBasicInfoRadioDone : FALSE 02 <br>";
			return FALSE;
		}
	}# end of function _searchBasicInfoRadioDone

	/**
	* Limited results search returning basic information as outlined at <var>_searchBasicInfoRadioDone()</var>.
	*
	* This method gives the possibility to sort the results based on an item and sorting direction.
	* @access public
	* @param string Search keyword
	* @param int department number
	* @param int Maximum number of rows returned
	* @param int Start index of rows returned
	* @param string Item as sort basis
	* @param string Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
	* @return mixed adodb record object or boolean
	* @created burn: November 19, 2007
	*/
	function searchLimitBasicInfoRadioDone($key,$sub_dept_nr,$len,$so,$sortitem='',$order='ASC', $is_doctor=0, $encounter_nr='', $cond=''){
		if(!empty($sortitem)){
			$option=" ORDER BY $sortitem $order";
		}
		return $this->_searchBasicInfoRadioDone($key,$sub_dept_nr,$option,TRUE,$len,$so, $is_doctor, $encounter_nr, $cond);
	}# end of function searchLimitBasicInfoRadioDone


#-------------------------- added by mark ----------------------------------------------

		/**
		*   Get the pending test requests
		*
		*   @access public
		*   @param string Table name
		*   @return boolean OR the list of undone (Pending) requests containing
		*                        batch_nr,encounter_nr,send_date,dept_nr, status,
		*                        lastname, firstname, date of birth, sex, pid,
		*                        personell_nr (assigend doctor), trace (history of assigned doctors)
		*                        in ASCENDING order i.e. from least recent to most
		*                        recent :-) para sabot-able!
		*   @created/modified burn: Oct. 3, 2007
		*/
	function _searchBasicInfoRadioRefNo($key,$sub_dept_nr='',$ORDER_BY_OPTION='',$limit=FALSE,$len=30,$so=0,$mod=0){
		global $db, $sql_LIKE, $root_path;
#echo "key = ".$key;
		#added by VAN 03-24-08
		$key = utf8_decode($key);

		$key=strtr($key,'*?','%_');
		$key=trim($key);
		#$suchwort=$searchkey;
		$key = str_replace("^","'",$key);
		$key=addslashes($key);
#edited by VAN 03-19-08
		//note $key value either refno or name, last, date_birth
		if(is_numeric($key)){
			if (strlen(trim($key))>8)
				$WHERE_SQL=" AND (r_id.rid = '".addslashes($key)."') ";
			else
				$WHERE_SQL=" AND (r_serv.pid = '".addslashes($key)."') ";
		}elseif($key=='%'||$key=='*'){
			$WHERE_SQL="";
		#added by VAN 03-18-08
		}elseif($key == "null"){
			$WHERE_SQL=" AND r_serv.request_date = DATE(NOW()) ";
		}else{
			if(stristr($key,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$key=strtr($key,',',' ');
			$cbuffer=explode(' ',$key);

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

			#commented by daryl
			if(sizeof($comp) > 1){
				$WHERE_SQL = " AND (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%') ";
			} else {
				$WHERE_SQL = " AND (p.name_last $sql_LIKE '".addslashes($key)."%') ";
			}
		}

		include_once($root_path.'include/inc_date_format_functions.php');
		$date_format=getDateFormat();
			# Check if it is a complete date in mm/dd/yyyy format
		$this_date=@formatDate2STD($key,$date_format);
		if($this_date!='') {
            $WHERE_SQL = " AND (r_serv.request_date='$this_date') ";
		}
		if ($sub_dept_nr){
			$WHERE_SQL.=" AND r.status NOT IN ($this->dead_stat) ".
							" AND r_serv_group.department_nr='".$sub_dept_nr."'";
		}

		if ($mod)
			#$option = ' ORDER BY r_serv.is_urgent DESC,r_serv.request_date ASC,r_serv.refno ASC ';
			$option = ' ORDER BY r_serv.is_urgent DESC, r_serv.request_date DESC, r_serv.refno DESC ';
		else
			$option = $ORDER_BY_OPTION;

#		$ORDER_BY_OPTION = $add_opt;
		# added by VAN in line 3065 : r.parent_batch_nr, r.parent_refno

        /**
         * Check for request expiration
         */
        $joins = array();
        $fields = array();
        $having = array();
        if (!empty($sub_dept_nr)) {
            $row = $db->GetRow(
                "SELECT value,unit " .
                "FROM seg_request_expiration_department " .
                "WHERE module='radiology' AND department=" .
                $db->qstr($sub_dept_nr)
            );
            if (!empty($row)) {
                $having[] = "IF(is_served, 1, r_serv.request_date >= (DATE(NOW()- INTERVAL {$row['value']} {$row['unit']})))";
            }
        } else {
            // sub department is not specified (i.e., ALL)
            $fields[] = '`exp`.value';
            $fields[] = '`exp`.unit';
            $joins[] = "LEFT JOIN seg_request_expiration_department `exp` ON module='radiology' AND department=r_serv_group.department_nr";
            $having[] = "(CASE `exp`.`unit` " .
                "WHEN 'DAY' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` DAY)) " .
                "WHEN 'WEEK' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` WEEK)) " .
                "WHEN 'MONTH' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` MONTH)) " .
                "WHEN 'YEAR' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` YEAR)) " .
                "ELSE 1 " .
            "END)";
        }


				$this->sql="SELECT SQL_CALC_FOUND_ROWS r.request_flag AS charge_name,
									r_serv.refno, r_serv.encounter_nr, r_serv.pid, r_serv.ordername, r_serv.orderaddress,
									r_serv.request_date, r_serv.request_time, r_serv.is_urgent, r_serv.status AS refno_status, r_serv.create_dt,
									r.batch_nr, r.status AS request_status, r_serv.is_cash,
									r_serv_group.department_nr AS sub_dept_nr,
									dept.id AS sub_dept_id, dept.name_formal AS sub_dept_name,
									r_services.service_code, r_services.name AS service_desp,
									p.name_last, p.name_first,p.name_middle, p.date_birth, p.sex, p.pid,
									p.date_birth, IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
									r_id.rid, e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type,
            r.parent_batch_nr, r.parent_refno, r.is_served " .
            ($fields ? ','. implode(",", $fields) : '') . ' ' .
        "FROM seg_radio_serv AS r_serv
								INNER JOIN care_person AS p ON p.pid = r_serv.pid
								LEFT JOIN care_encounter AS e ON e.encounter_nr=r_serv.encounter_nr AND e.pid=r_serv.pid
								INNER JOIN seg_radio_id AS r_id ON r_id.pid=r_serv.pid
								INNER JOIN care_test_request_radio AS r ON r.refno=r_serv.refno
								INNER JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
								INNER JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
								INNER JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
            LEFT JOIN seg_radio_schedule sched ON r.batch_nr=sched.batch_nr " .
            ($joins ? implode(' ', $joins) : '') . ' ' .
        "WHERE r_serv.status NOT IN ($this->dead_stat)
							AND r.status NOT IN ($this->dead_stat)
									$WHERE_SQL
        GROUP BY r_serv.refno ".
        ($having ? ('HAVING ('.implode(")\n AND (", $having) . ") ") : '') .
        $option;

		#WHERE r_serv.status NOT IN ('deleted','hidden','inactive','void')
#echo "class_radiology.php : _searchBasicInfoRadioRefNo : this->sql = $this->sql <br> \n ";
#exit();
		if($limit){
				$this->res['said']=$db->SelectLimit($this->sql,$len,$so);
		}else{
				$this->res['said']=$db->Execute($this->sql);
		}
		if ($this->res['said']){
			if ($this->record_count=$this->res['said']->RecordCount()) {
				$this->rec_count=$this->record_count; # workaround
#				echo " class_radiology.php :rec_count =".$this->record_count;
#				echo "class_radiology.php : _searchBasicInfoRadioPending :  TRUE <br>";
				return $this->res['said'];
			} else{
//				echo "_searchBasicInfoRadioPending : FALSE 01 <br>";
				return FALSE;
			}
		}else{
#			echo "class_radiology.php : _searchBasicInfoRadioPending : FALSE 02 <br>";
			return FALSE;
		}
	}# end of function _searchBasicInfoRadioRefNo


	/**
	* Limited results search returning basic information as outlined at <var>_searchAdmissionBasicInfo()</var>.
	*
	* This method gives the possibility to sort the results based on an item and sorting direction.
	* @access public
	* @param string Search keyword
	* @param int department number
	* @param int Maximum number of rows returned
	* @param int Start index of rows returned
	* @param string Item as sort basis
	* @param string Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
	* @return mixed adodb record object or boolean
	* @created/modified burn: Oct. 2, 2006
	*/
#	function searchLimitEncounterBasicInfoPending($key,$len,$so,$sortitem='',$order='ASC'){
	function searchLimitBasicInfoRadioRefNo($key,$sub_dept_nr, $len,$so,$sortitem='',$order='ASC', $mod){
		if(!empty($sortitem)){
#			$option=" ORDER BY r_serv.$sortitem $order";
			#edited by VAN 03-03-08
			#if ($mod)
				#$option=" ORDER BY r_serv.is_urgent DESC,r_serv.request_date ASC,r_serv.refno ASC ";
			#else
			$option=" ORDER BY $sortitem $order";
		}
		return $this->_searchBasicInfoRadioRefNo($key,$sub_dept_nr, $option,TRUE,$len,$so,$mod);
	}# end of function searchLimitBasicInfoRadioRefNo



#--------------------------end by mark--------------------------------------------------------
	#----added by van------------------

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

	#added by VAN 03-17-08
	function addRadioService($code, $name, $cash, $charge, $remarks, $grp, $is_socialized, $is_ER, $status) {
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
		$remarks=addcslashes($remarks,$charlist);
		$grp=addcslashes($grp,$charlist);

		$this->useRadioServices();
		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

		$this->sql="INSERT INTO $this->coretable(service_code, group_code, name, price_cash, price_charge,
									 status, history, modify_id, modify_dt, create_id, create_dt, is_socialized, is_ER, remarks)
							 VALUES('".strtoupper($code)."', '$grp', '$name', '$cash', '$charge',
										'$status', CONCAT('Create: ',NOW(),' [$userid]\\n'),
										'$userid', NOW(), '$userid', NOW(), '$is_socialized', '$is_ER','$remarks')
						";

		#echo "sql = ".$this->sql;
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;
	}

	function updateRadioService($excode, $code, $name, $cash, $charge, $remarks, $grp, $is_socialized, $is_ER, $status) {
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
		$remarks=addcslashes($remarks,$charlist);
		$grp=addcslashes($grp,$charlist);

		$this->useRadioServices();
		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

		#edited by VAN 04-28-08
		$this->sql="UPDATE $this->coretable SET ".
			"service_code='$code',".
			"group_code='$grp',".
			"name='$name',".
			"price_cash='$cash',".
			"price_charge='$charge',".
			"status='$status',".
			"history=CONCAT(history,'Update: ',NOW(),' [$userid]\\n'),".
			"modify_id='$userid',".
			"modify_dt=NOW(),".
			"is_socialized = '$is_socialized', ".
			"is_ER = '$is_ER', ".
			"remarks = '$remarks' ".
			"WHERE ((service_code='$excode') OR (service_code='".urlencode($excode)."'))";

		#echo "<br>sql update = ".$this->sql;
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;
	}

	function deleteRadioService($code) {
		global $HTTP_SESSION_VARS;

		$this->useRadioServices();
		#$this->sql="DELETE FROM $this->coretable WHERE service_code='$code'";
		$history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		$this->sql="UPDATE $this->coretable ".
						" SET status='deleted', history=".$history.", ".
						" modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
						" WHERE service_code = '$code'";
		return $this->Transact();
	}

	#edited by VAN 03-18-08
	/*
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
	*/
	function saveRadioServiceGroup($name, $code, $other_name, $dept_nr, $mode)	{
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;
		$this->useRadioServiceGroups();


		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

		if ($mode=='save'){
			$this->sql="INSERT INTO $this->coretable(group_code, department_nr, name, other_name, status, history, create_id, create_dt, modify_id, modify_dt) ".
				"VALUES('".$code."', '".$dept_nr."', '".$name."', '".$other_name."', '', CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW())";

		}else{
			$this->sql="UPDATE $this->coretable SET
									department_nr = '".$dept_nr."',
									name='".$name."',
									other_name='".$other_name."',
									status='',
									history=CONCAT(history,'Update: ',NOW(),' [$userid]\\n'),
									modify_id='$userid',
									modify_dt=NOW()
									WHERE group_code = '".$code."'";
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

	#function getRadioServices($cond="1", $sort='') {
	function getRadioServices($all, $cond="1", $sort='') {
		global $db;
		$this->useRadioServices();
		if(empty($sort)) $sort='name';

		if ($all)
			$limit = "LIMIT 20";
		else
			$limit = "";

		$this->sql="SELECT * FROM $this->coretable
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

    //added by Francis 06-01-13
    function getRadioServiceGroupInfo($serviceCode) {
        global $db;
        
        $this->sql="SELECT rs.group_code, rsg.name, rsg.department_nr
                    FROM seg_radio_services AS rs
                    LEFT JOIN seg_radio_service_groups AS rsg ON rsg.group_code = rs.group_code
                    WHERE rs.service_code = '$serviceCode'" ;

        if ($buf=$db->Execute($this->sql)){
            if($buf->RecordCount()) {
                return $buf->FetchRow();
            }else { 
                return FALSE; 
            }
        }else { 
            return FALSE; 
        }
    }

	#added by VAN 03-17-08
	function getAllRadioGroupInfo($group_code, $dept_nr){
			global $db;
		 $this->sql="SELECT * FROM $this->tb_radio_service_groups
						 WHERE group_code='$group_code'
						 AND department_nr = '$dept_nr'
						 AND status NOT IN ($this->dead_stat)";
		#echo "sql = ".$this->sql;
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


	function getRadioServiceGroups($cond="1", $sort='') {
		global $db;
		$this->useRadioServiceGroups();
		if(empty($sort)) $sort='name';
		#$this->sql="SELECT * FROM $this->tb_radio_service_groups WHERE $cond ORDER BY $sort";
		$this->sql="SELECT * FROM $this->tb_radio_service_groups
						WHERE $cond
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
        //added by ken 3/27/2014
	function getRoomRate($cond='1', $sort=''){
		global $db;

		$this->sql="SELECT * FROM seg_other_rates where $cond ORDER BY $sort";

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
        //ended by ken 3/27/2014
	function getServiceDiscount($cond="1",$sort='') {
	#function getServiceDiscount($service_code,$sort='') {
		global $db;

		$this->sql="SELECT * FROM $this->tb_serv_discounts where $cond ORDER BY $sort";
		#$this->sql="SELECT * FROM $this->tb_serv_discounts
		#            WHERE service_code = '$service_code' ORDER BY $sort";

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

	#-----------added by VAN 01-15-08
	function getRequestInfoByPrevRef($refno, $batch){
		global $db;

		$this->sql="SELECT * FROM care_test_request_radio
						WHERE parent_batch_nr='$batch'
						AND parent_refno='$refno'";

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
	#--------------------------------------
	//added by ken 3/27/2014
	function deleteRoomRate($code, $area){
		$this->sql="DELETE FROM seg_other_rates
						WHERE service_code='$code' AND source='$area'";
			return $this->Transact();
	}

	function addRoomRates($data, $code, $area, $status){
		global $db;
		$charlist="\0..\37";
		# escape strings
		$code=addcslashes($code,$charlist);
		if($status == 'add'){
			$code = strtoupper($code);
		}
		$this->sql = "INSERT INTO seg_other_rates
									(service_code, room_type, cash_price, charge_price, source)
								VALUES ('$code', ?, ?, ?, '$area')";

		if ($db->Execute($this->sql, $data)){
			if($db->Affected_Rows()){
				$ret = true;
			}
		}
		else{
			$this->error = $db->ErrorMsg();
		}
		if ($ret) return TRUE;
		else return FALSE;
	}
	//ended by ken 3/27/2014

	function deleteServiceDiscounts($code,$service_area) {
		$this->sql="DELETE FROM $this->tb_serv_discounts
						WHERE service_code='$code' AND service_area='$service_area'";
			return $this->Transact();
	}

	function AddServiceDiscounts($serv_discount,$service_code,$service_area){
		global $db;

		$charlist="\0..\37";
		# escape strings
		$service_code=addcslashes($service_code,$charlist);
		$dept_nr=addcslashes($dept_nr,$charlist);

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
		#$ok=$db->Execute($this->sql,$array);
		/*
		$ok=$db->Execute($this->sql,$serv_discount);
		$this->count=$db->Affected_Rows();
		$this->error=$db->ErrorMsg();
		echo "error".$this->error;
		return $ok;
		*/
	}
	#-----------------------------------

	#added by VAN 03-15-08
	function countSearchService($group_code, $searchkey='',$maxcount=100,$offset=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		#--added by CHA, May 23, 2010
		if($group_code)
			$cond = " AND s.group_code='".$group_code."' ";

		$this->sql = "SELECT s.* FROM seg_radio_services AS s, seg_radio_service_groups AS g
							WHERE s.group_code=g.group_code
							".$cond."
							AND (s.service_code LIKE '%".$keyword."%'
							OR s.name LIKE '%".$keyword."%')
							AND s.status NOT IN (".$this->dead_stat.")
							ORDER BY s.name";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	#added by VAN 03-18-08
	function countSearchGroup($searchkey='',$dept_nr=0,$maxcount=100,$offset=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		if ($dept_nr){
			$this->sql = "SELECT g.*, d.name_formal
								FROM seg_radio_service_groups AS g
								INNER JOIN care_department AS d ON d.nr=g.department_nr
								 WHERE (name LIKE '%".$keyword."%' OR other_name LIKE '%".$keyword."%')
								 AND g.department_nr='$dept_nr'
								 AND g.status NOT IN ($this->dead_stat)
								 ORDER BY g.name";
		}else{
			$this->sql = "SELECT g.*, d.name_formal
								FROM seg_radio_service_groups AS g
								INNER JOIN care_department AS d ON d.nr=g.department_nr
								WHERE (name LIKE '%".$keyword."%' OR other_name LIKE '%".$keyword."%')
								AND g.status NOT IN ($this->dead_stat)
								ORDER BY g.name";
		}

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function SearchGroup($searchkey='',$dept_nr=0,$maxcount=100,$offset=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		if ($dept_nr){
			$this->sql = "SELECT g.*, d.name_formal
								FROM seg_radio_service_groups AS g
								INNER JOIN care_department AS d ON d.nr=g.department_nr
								WHERE (name LIKE '%".$keyword."%' OR other_name LIKE '%".$keyword."%')
								AND g.department_nr='$dept_nr'
								AND g.status NOT IN ($this->dead_stat)
								ORDER BY g.name";
		}else{
			$this->sql = "SELECT g.*, d.name_formal
								FROM seg_radio_service_groups AS g
								INNER JOIN care_department AS d ON d.nr=g.department_nr
								WHERE (name LIKE '%".$keyword."%' OR other_name LIKE '%".$keyword."%')
								AND g.status NOT IN ($this->dead_stat)
								ORDER BY g.name";
		}

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

	function deleteServiceGroup($group_code, $dept_nr){
		global $HTTP_SESSION_VARS;
		#$userid = $HTTP_SESSION_VARS['sess_temp_userid'];
		$history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." [".$HTTP_SESSION_VARS['sess_user_name']."]\\n");
		$this->sql="UPDATE seg_radio_service_groups ".
						" SET status='deleted', history=".$history.", ".
						" modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
						" WHERE group_code = '$group_code' ".
						" AND department_nr = '$dept_nr' ";
			return $this->Transact();
	}

	function countSearchService2($searchkey='',$maxcount=100,$offset=0,$area='',$dept_nr=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		if ($area=='ER')
			$area_cond = " AND is_ER=1 ";
		else
			$area_cond = "";

		if ($dept_nr)
			$dept_cond = " AND department_nr='".$dept_nr."'";
		else
			$dept_cond = "";

		/*
		$this->sql = "SELECT r_serv.service_code, r_serv.group_code, r_serv.name, r_serv.is_socialized,
							IFNULL(r_serv.price_cash,0) AS price_cash,IFNULL(r_serv.price_charge,0) AS price_charge
								FROM seg_radio_services AS r_serv
								WHERE (service_code LIKE '%".$keyword."%'
							OR name LIKE '%".$keyword."%')
							AND status NOT IN (".$this->dead_stat.")
							ORDER BY name";
		*/
		$this->sql = "SELECT r_serv.status, d.name_short AS dept_short_name, d.name_formal,r_serv.service_code,
							r_serv.group_code, r_serv.name, r_serv.is_socialized,
							IFNULL(r_serv.price_cash,0) AS price_cash,IFNULL(r_serv.price_charge,0) AS price_charge
								FROM seg_radio_services AS r_serv
						INNER JOIN seg_radio_service_groups AS grp ON grp.group_code=r_serv.group_code
						INNER JOIN care_department AS d ON d.nr=grp.department_nr
								WHERE (service_code LIKE '%".$keyword."%'
							OR r_serv.name LIKE '%".$keyword."%')
							AND r_serv.status NOT IN (".$this->dead_stat.")
							$area_cond
							$dept_cond
							ORDER BY r_serv.name";
		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	/*function SearchService2($searchkey='',$maxcount=100,$offset=0,$area='',$dept_nr=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		if ($area=='ER')
			$area_cond = " AND is_ER=1 ";
		else
			$area_cond = "";

		if ($dept_nr)
			$dept_cond = " AND department_nr='".$dept_nr."'";
		else
			$dept_cond = "";

		$this->sql = "SELECT r_serv.status, d.name_short dept_short_name, d.name_formal,r_serv.service_code,
							r_serv.group_code, r_serv.name, r_serv.is_socialized,
							IFNULL(r_serv.price_cash,0) AS price_cash,IFNULL(r_serv.price_charge,0) AS price_charge
								FROM seg_radio_services AS r_serv
						INNER JOIN seg_radio_service_groups AS grp ON grp.group_code=r_serv.group_code
						INNER JOIN care_department AS d ON d.nr=grp.department_nr
								WHERE (service_code LIKE '%".$keyword."%'
							OR r_serv.name LIKE '%".$keyword."%')
							AND r_serv.status NOT IN (".$this->dead_stat.")
							$area_cond
							$dept_cond
							ORDER BY r_serv.name";

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}*/

function SearchService($group_code, $searchkey='',$maxcount=100,$offset=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		#--added by CHA, May 23, 2010
		if($group_code)
			$cond = " AND s.group_code='".$group_code."' ";

		$this->sql = "SELECT SQL_CALC_FOUND_ROWS s.* FROM seg_radio_services AS s, seg_radio_service_groups AS g
							WHERE s.group_code=g.group_code
							".$cond."
							AND (s.service_code LIKE '%".$keyword."%'
							OR s.name LIKE '%".$keyword."%')
							AND s.status NOT IN (".$this->dead_stat.")
							ORDER BY s.name";

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

#-----edited by VAN 08-16-2010
function SearchService2($source_req='LD', $is_charge2comp=0, $compID='',$dept_nr,$is_cash=1,$discountid='',$discount=0, $is_senior=0, $is_walkin=0, $sc_walkin_discount=0,$codenum, $searchkey='',$multiple=0,$maxcount=100,$offset=0,$area='',$sect=''){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		if ($dept_nr)
			$dept_cond = " AND department_nr='".$dept_nr."'";
		else
			$dept_cond = "";

		if($sect)
			$sect_cond = "AND s.group_code='".$sect."'";
		else
			$area_cond = "";
        #IF($is_cash,s.price_cash,s.price_charge), at line 5130
		if ($discountid){
			$with_disc_query = " IF(s.is_socialized=0,
                                                         IF($is_cash,IF($is_senior,s.price_cash*(1-$sc_walkin_discount),s.price_cash),s.price_charge),
														 IF($is_cash,
																	 IF($is_senior,IF($is_cash,IF($is_walkin,(s.price_cash*(1-$sc_walkin_discount)),
																	 IF(sd.price,sd.price,(s.price_cash*(1-$discount)))),s.price_charge),
																	 IF($is_cash,
																			 IF(sd.price,sd.price,
																				 IF($is_cash,
																							(s.price_cash*(1-$discount)),
																							(s.price_charge*(1-$discount))
																				 )
																			 ),
																			 s.price_charge
																		)
															),
															s.price_charge)
													) AS net_price, s.*
											FROM seg_radio_services AS s
											INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code
											INNER JOIN care_department AS d ON d.nr=g.department_nr
											LEFT JOIN seg_service_discounts AS sd ON sd.service_code=s.service_code
																AND sd.service_area='RD' AND sd.discountid='$discountid' ";
		} else{
			if ($source_req=='IC'){
				if ($is_charge2comp){
					$sql_ic_row = " IF(ics.price,IF(1,ics.price,ics.price),IF($is_cash,s.price_cash,s.price_charge)) AS net_price, ";
					$sql_ic_join = " LEFT JOIN seg_industrial_comp_price AS ics ON ics.service_code=s.service_code
														AND ics.company_id='".$compID."' AND ics.service_area='RD'";
				}else{
					$sql_ic_row = " IF($is_cash,s.price_cash,s.price_charge) AS net_price, ";
					$sql_ic_join = " ";
				}

				$with_disc_query = 	$sql_ic_row."
															s.*
														FROM seg_radio_services AS s
														INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code
														INNER JOIN care_department AS d ON d.nr=g.department_nr ".$sql_ic_join;
			}else{
				$with_disc_query = "  IF($is_cash,s.price_cash,s.price_charge) AS net_price, s.*
													FROM seg_radio_services AS s
													INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code
													INNER JOIN care_department AS d ON d.nr=g.department_nr ";
			}
		}


		if ($multiple){
			$keyword = $searchkey;

			/*if ($codenum)
				$cond_where = " AND (s.code_num IN (".$keyword.")) ";
			else  */
				$cond_where = "  AND (s.service_code IN (".$keyword.")) ";

			$this->sql = "SELECT DISTINCT SQL_CALC_FOUND_ROWS d.name_short AS dept_name,
											$with_disc_query
											WHERE  s.status NOT IN (".$this->dead_stat.")
											$cond_where
											AND s.status NOT IN (".$this->dead_stat.")
											$area_cond
											$dept_cond
											$sect_cond
											ORDER BY s.name";
		}else{
			# convert * and ? to % and &
			$searchkey=strtr($searchkey,'*?','%_');
			$searchkey=trim($searchkey);
			#$suchwort=$searchkey;
			$searchkey = str_replace("^","'",$searchkey);
			$keyword = addslashes($searchkey);

			/*if (is_numeric($keyword)){
				 $cond_where = " AND (s.service_code = '".$keyword."'
										OR s.name LIKE '".$keyword."') ";
			}else{*/
				 $cond_where = "  AND (s.service_code LIKE '%".$keyword."%'
									OR s.name LIKE '%".$keyword."%') ";
			#}

			$this->sql = "SELECT DISTINCT SQL_CALC_FOUND_ROWS d.name_short AS dept_name,
													$with_disc_query
											WHERE  s.status NOT IN (".$this->dead_stat.")
											$cond_where
											$area_cond
											$dept_cond
											ORDER BY s.name";
		}


		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

	function getAllServiceOfPackage($service_code, $is_cash=1, $discountid='',$discount=0, $is_senior=0, $is_walkin=0, $sc_walkin_discount=0){
				global $db;

				$this->sql="SELECT SQL_CALC_FOUND_ROWS lg.service_code_child AS service_code, s.service_code,
													s.name, s.price_cash, s.price_charge, s.is_socialized,s.group_code,
													IF(s.is_socialized=0,
                                                         IF($is_cash,IF($is_senior,s.price_cash*(1-$sc_walkin_discount),s.price_cash),s.price_charge),
														 IF($is_cash,
																IF($is_senior,IF($is_cash,IF($is_walkin,(s.price_cash*(1-$sc_walkin_discount)),
																	 IF(sd.price,sd.price,(s.price_cash*(1-$discount)))),s.price_charge),
																	 IF($is_cash,
																			 IF(sd.price,sd.price,
																				 IF($is_cash,
																							(s.price_cash*(1-$discount)),
																							(s.price_charge*(1-$discount))
																				 )
																			 ),
																			 s.price_charge
																		)
															),
															s.price_charge)
													) AS net_price
										FROM seg_radio_group AS lg
										INNER JOIN seg_radio_services AS s ON s.service_code=lg.service_code_child
										LEFT JOIN seg_service_discounts AS sd ON sd.service_code=s.service_code
												AND sd.service_area='RD' AND sd.discountid='$discountid'
										WHERE lg.service_code='".$service_code."'";

				if ($this->result=$db->Execute($this->sql)) {
					$this->count=$this->result->RecordCount();
					return $this->result;
				} else{
					 return FALSE;
				}
	}

	function isServiceAPackage($service_code){
				global $db;

				$this->sql="SELECT count(service_code_child) AS count_child
											FROM seg_radio_group AS lg
											WHERE service_code='".$service_code."'";

				if ($this->result=$db->Execute($this->sql)) {
					$row=$this->result->FetchRow();
					$this->count=$row['count_child'];
					return $this->count;
				} else{
					 return FALSE;
				}
	}
	#----------------------

	#added by VAN 03-19-08
	function getStaffInfo($userid, $password){
		global $db;

		/*
		Radiology = 158
		Ultrasound = 164
		Special Procedures = 165
		Computed Tomography = 166
		Social Service = 167
		*/
		$this->sql="SELECT u.name, u.login_id, u.password, u.personell_nr,
							pr.pid, pr.job_function_title, pr.short_id
						FROM care_users AS u
						INNER JOIN care_personell AS pr ON u.personell_nr=pr.nr
						INNER JOIN care_personell_assignment AS pa ON u.personell_nr=pa.personell_nr
						WHERE pa.location_nr IN ('158', '164', '165', '166', '167')
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
	#---------------------------------

	#-------added by VAN 03-26-08
	function countSearchSelect($searchkey='',$maxcount=100,$offset=0,$sked=0) {
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

			$sql2="	WHERE r.status NOT IN ($this->dead_stat) AND rs.status NOT IN ($this->dead_stat) AND (r.pid='$suchwort') ";
		} else {
			# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$searchkey=strtr($searchkey,',',' ');
			$cbuffer=explode(' ',$searchkey);

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
				$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') ";
				if(!empty($rd)){
					$DOB=@formatDate2STD($rd,$date_format);
					if($DOB=='') {
						$sql2.=" AND DATE(sk.scheduled_dt)='$rd' ";
					}else{
						$sql2.=" AND DATE(sk.scheduled_dt) = '$DOB' ";
					}
				}
				$sql2.=" AND r.status NOT IN ($this->dead_stat) AND rs.status NOT IN ($this->dead_stat) ";
			}else{
				# Check if * or %
				if($suchwort=='%'||$suchwort=='%%'){
					#return all the data
					#$sql2=" WHERE r.status NOT IN ($this->dead_stat) ";
					#edited by VAN 07-08-08
					$sql2=" WHERE r.status NOT IN ($this->dead_stat) AND rs.status NOT IN ($this->dead_stat) ";
				}elseif($suchwort=='now'){
					#$sql2=" WHERE sk.scheduled_dt=DATE(NOW()) AND r.status NOT IN ($this->dead_stat) ";

					#edited by VAN 07-08-08
					$sql2=" WHERE (sk.scheduled_dt=DATE(NOW()) || (ISNULL(sk.scheduled_dt) AND (rs.request_date=DATE(NOW()) /*AND (g.department_nr='164' OR e.encounter_type=1)*/)))
							AND r.status NOT IN ($this->dead_stat) AND rs.status NOT IN ($this->dead_stat) ";

				}else{
					# Check if it is a complete DOB
					$DOB=@formatDate2STD($suchwort,$date_format);
					if($DOB=='') {
						if(TRUE){
							if($fname){
								$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR r.ordername $sql_LIKE '%".strtr($suchwort,'+',' ')."%'
											 OR sk.modify_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR sk.create_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%')";
							}else{
								#$sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
								$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR r.ordername $sql_LIKE '%".strtr($suchwort,'+',' ')."%'
												OR sk.modify_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR sk.create_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%')";
							}
						}else{
							#$sql2=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
							$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR r.ordername $sql_LIKE '%".strtr($suchwort,'+',' ')."%'
											OR sk.modify_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR sk.create_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%')";
						}
					}else{
						$sql2=" WHERE sk.scheduled_dt = '$DOB' ";
					}
					$sql2.=" AND r.status NOT IN ($this->dead_stat) ";
				}
			}
		 }

		$cond_final = "";
		if ($sked) {
			$cond_final = " AND (b.is_final = 0 OR b.is_final IS NULL)";
		}

		#added by VAN 07-09-08
		$sql2  .= " AND rs.batch_nr NOT IN (SELECT batch_nr FROM seg_radio_service_sized GROUP BY batch_nr)
						AND (request_flag IS NOT NULL OR r.is_urgent=1 OR r.is_cash=0 OR (rs.parent_batch_nr!='' AND rs.parent_refno)
					/*AND (g.department_nr='164' OR e.encounter_type=1 OR schedule_no!='')*/)
					";

		#edited by VAN 07-08-08
		$sql2 = " AS rs
					 LEFT JOIN seg_radio_schedule AS sk ON rs.batch_nr=sk.batch_nr
					 INNER JOIN seg_radio_serv AS r ON r.refno=rs.refno
					 INNER JOIN seg_radio_services AS s ON s.service_code=rs.service_code
					 INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code
					 INNER JOIN care_department AS d ON g.department_nr=d.nr
					 LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
					 LEFT JOIN seg_billing_encounter AS b ON b.encounter_nr=e.encounter_nr
					 INNER JOIN seg_radio_id AS ri ON r.pid=ri.pid
					 INNER JOIN care_person AS p ON p.pid=r.pid ".$sql2;

		#edited by VAN 07-08-08
		#$this->buffer=$this->tb_radio_schedule.$sql2;
		$this->buffer=$this->tb_test_request_radio.$sql2;

		#if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY sk.scheduled_dt DESC, p.name_last ASC ";
		$sql3 =" ORDER BY r.is_urgent DESC,sk.scheduled_dt ASC, p.name_last ASC, p.name_first ASC, rs.batch_nr ASC ";

		$this->sql= " SELECT rs.parent_refno,rs.parent_batch_nr,IF ((request_flag IS NOT NULL),1,0) AS hasPaid,
							rs.batch_nr AS refnum,rs.refno AS batchnum, rs.modify_id AS encoder, rs.create_id AS encoder2, sk.*, sk.status AS skstatus, ri.rid, rs.service_code, s.name AS serv_name,
							s.group_code, d.id, d.name_formal, d.name_short AS dept_short_name,
							r.pid, r.refno, p.name_first, e.encounter_type,
							 p.name_middle, p.name_last, r.ordername ".
							 " FROM ".$this->buffer.$sql3;

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	#function SearchSelect($searchkey='',$maxcount=100,$offset=0, $count_sql=0){
    function SearchSelect($searchkey='',$sub_dept_nr=0,$maxcount=100,$offset=0){
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

            $whereClause = "WHERE r.status NOT IN ($this->dead_stat) AND rs.status NOT IN ($this->dead_stat) AND ((r.pid= '$suchwort') /*OR (sk.batch_nr = '$suchwort')*/ OR (ri.rid = '$suchwort')) ";
		} else {
			# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$searchkey=strtr($searchkey,',',' ');
			$cbuffer=explode(' ',$searchkey);

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
                $whereClause = "WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') ";
				if(!empty($rd)){
					$DOB=@formatDate2STD($rd,$date_format);
					if($DOB=='') {
                        $whereClause .= "AND DATE(sk.scheduled_dt)='$rd' ";
					}else{
                        $whereClause .= "AND DATE(sk.scheduled_dt) = '$DOB' ";
					}
				}
                $whereClause .= "AND r.status NOT IN ($this->dead_stat) AND rs.status NOT IN ($this->dead_stat) ";
			}else{
				# Check if * or %
				if($suchwort=='%'||$suchwort=='%%'){
					#return all the data
                    $whereClause = "WHERE r.status NOT IN ($this->dead_stat) AND rs.status NOT IN ($this->dead_stat) ";
				}elseif($suchwort=='now'){
                    $whereClause = "WHERE (sk.scheduled_dt=DATE(NOW()) || (ISNULL(sk.scheduled_dt) AND (rs.request_date=DATE(NOW())))) " .
                        "AND r.status NOT IN ($this->dead_stat) AND rs.status NOT IN ($this->dead_stat) ";
				}else{
					# Check if it is a complete DOB
					$DOB=@formatDate2STD($suchwort,$date_format);
					if($DOB=='') {
						if(TRUE){
							if($fname){
                                $whereClause = "WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR r.ordername $sql_LIKE '%".strtr($suchwort,'+',' ')."%' " .
                                    "OR sk.modify_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR sk.create_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%') ";
							}else{
                                $whereClause="WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR r.ordername $sql_LIKE '%".strtr($suchwort,'+',' ')."%' " .
                                    "OR sk.modify_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR sk.create_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%') ";
							}
						}else{
                            $whereClause="WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR r.ordername $sql_LIKE '%".strtr($suchwort,'+',' ')."%' " .
                                "OR sk.modify_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR sk.create_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%')";
						}
					}else{
                        //$whereClause = "WHERE (sk.scheduled_dt = '$DOB'  OR r.request_date = '$DOB') ";
                        $whereClause = "WHERE (r.request_date = '$DOB') ";
					}
                    $whereClause .= "AND r.status NOT IN ($this->dead_stat) ";
				}
			}
		 }

		 $cond_final = "";
		if ($sked) {
			$cond_final = " AND (b.is_final = 0 OR b.is_final IS NULL)";
		}

        if ($sub_dept_nr)
            $cond_dept = " AND g.department_nr = '".$sub_dept_nr."' ";
		 
         /**
         * Check for request expiration
         */
        $joins = array();
        $fields = array();
        $having = array();
        if (!empty($sub_dept_nr)) {
            $row = $db->GetRow(
                "SELECT value,unit " .
                "FROM seg_request_expiration_department " .
                "WHERE module='radiology' AND department=" .
                $db->qstr($sub_dept_nr)
            );
            if (!empty($row)) {
                $having[] = "IF(is_served, 1, r.request_date >= (DATE(NOW()- INTERVAL {$row['value']} {$row['unit']})))";
            }
        } else {
            // sub department is not specified (i.e., ALL)
            $fields[] = '`exp`.value';
            $fields[] = '`exp`.unit';
            $joins[] = "LEFT JOIN seg_request_expiration_department `exp` ON module='radiology' AND department=g.department_nr";
            $having[] = "(CASE `exp`.`unit` " .
                "WHEN 'DAY' THEN IF(is_served, 1, r.request_date >= DATE(NOW()-INTERVAL `exp`.`value` DAY)) " .
                "WHEN 'WEEK' THEN IF(is_served, 1, r.request_date >= DATE(NOW()-INTERVAL `exp`.`value` WEEK)) " .
                "WHEN 'MONTH' THEN IF(is_served, 1, r.request_date >= DATE(NOW()-INTERVAL `exp`.`value` MONTH)) " .
                "WHEN 'YEAR' THEN IF(is_served, 1, r.request_date >= DATE(NOW()-INTERVAL `exp`.`value` YEAR)) " .
                "ELSE 1 " .
            "END)";
        }


        $orderClause = "ORDER BY r.is_urgent DESC,r.request_date DESC, r.request_time DESC,sk.scheduled_dt ASC, p.name_last ASC, p.name_first ASC, rs.batch_nr ASC ";

        #added by VAN 06-03-2013
        #add field e.is_maygohome, r.encounter_nr, 
		$this->sql= "SELECT DISTINCT SQL_CALC_FOUND_ROWS e.is_maygohome, r.encounter_nr, r.is_cash, r.request_date, r.request_time, rs.parent_refno,rs.parent_batch_nr,IF ((rs.request_flag IS NOT NULL),1,0) AS hasPaid, " .
                "rs.batch_nr AS refnum,rs.refno AS batchnum, rs.modify_id AS encoder, rs.create_id AS encoder2, sk.*, sk.status AS skstatus, ri.rid, rs.service_code, s.name AS serv_name, " .
                "s.group_code, d.id, d.name_formal, d.name_short AS dept_short_name, " .
                "r.pid, r.refno, p.name_first, e.encounter_type, " .
                "p.name_middle, p.name_last, r.ordername, rs.is_served, rs.served_date " .
                ($fields ? ','. implode(",", $fields) : '') . ' ' .
            "FROM {$this->tb_test_request_radio} AS rs " .
                "LEFT JOIN seg_radio_schedule AS sk ON rs.batch_nr=sk.batch_nr " .
                "INNER JOIN seg_radio_serv AS r ON r.refno=rs.refno " .
                "INNER JOIN seg_radio_services AS s ON s.service_code=rs.service_code " .
                "INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code " .
                "INNER JOIN care_department AS d ON g.department_nr=d.nr " .
                "LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr " .
                "LEFT JOIN seg_billing_encounter AS b ON b.encounter_nr=e.encounter_nr " .
                "INNER JOIN seg_radio_id AS ri ON r.pid=ri.pid " .
                "INNER JOIN care_person AS p ON p.pid=r.pid ".
                ($joins ? implode(' ', $joins) : '') . ' ' .
            $whereClause . $cond_dept .
            ($having ? ('HAVING ('.implode(")\n AND (", $having) . ") ") : '') .
            $orderClause;

                if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
                    if($this->rec_count=$this->res['ssl']->RecordCount()) {
                        return $this->res['ssl'];
                    }else{return false;}
                }else{return false;}
	}
	#------------------------------------------
	#added by VAN 04-21-08
	function getStatReport($fromdate, $todate){
		global $db;

		if (($fromdate)&&($todate)){
			$cond = "AND (s.request_date >= '".$fromdate."' AND s.request_date <= '".$todate."')";
		}

		$this->sql="SELECT count(g.group_code) AS stat, EXTRACT(YEAR FROM s.request_date) AS year
						FROM seg_radio_serv AS s
						INNER JOIN care_test_request_radio AS d ON s.refno=d.refno
						INNER JOIN seg_radio_services AS ss ON d.service_code=ss.service_code
						INNER JOIN seg_radio_service_groups AS g ON g.group_code=ss.group_code
						INNER JOIN care_person AS p ON p.pid=s.pid
						INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
						WHERE s.status NOT IN($this->dead_stat)
						AND d.status NOT IN($this->dead_stat)
						$cond
						GROUP BY EXTRACT(YEAR FROM s.request_date)
						ORDER BY EXTRACT(YEAR FROM s.request_date) DESC";
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
			$cond = "AND (s.request_date >= '".$fromdate."' AND s.request_date <= '".$todate."')";
		}

		$this->sql="SELECT count(g.group_code) AS stat, EXTRACT(MONTH FROM s.request_date) AS month,
						EXTRACT(YEAR FROM s.request_date) AS year
						FROM seg_radio_serv AS s
						INNER JOIN care_test_request_radio AS d ON s.refno=d.refno
						INNER JOIN seg_radio_services AS ss ON d.service_code=ss.service_code
						INNER JOIN seg_radio_service_groups AS g ON g.group_code=ss.group_code
						INNER JOIN care_person AS p ON p.pid=s.pid
						INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
						WHERE s.status NOT IN($this->dead_stat)
						AND d.status NOT IN($this->dead_stat)
						".
						"AND d.status='done'       /*for done*/
						AND EXTRACT(YEAR FROM s.request_date)='".$year."'
						$cond
						GROUP BY EXTRACT(MONTH FROM s.request_date)
						ORDER BY EXTRACT(MONTH FROM s.request_date)";
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
			$cond = "AND (s.request_date >= '".$fromdate."' AND s.request_date <= '".$todate."')";
		}

		$this->sql="SELECT count(g.group_code) AS stat, EXTRACT(MONTH FROM s.request_date) AS month,
						EXTRACT(YEAR FROM s.request_date) AS year, g.name AS grp_name,
						g.group_code AS grp_code
						FROM seg_radio_serv AS s
						INNER JOIN care_test_request_radio AS d ON s.refno=d.refno
						INNER JOIN seg_radio_services AS ss ON d.service_code=ss.service_code
						INNER JOIN seg_radio_service_groups AS g ON g.group_code=ss.group_code
						INNER JOIN care_person AS p ON p.pid=s.pid
						INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
						WHERE s.status NOT IN($this->dead_stat)
						AND d.status NOT IN($this->dead_stat)
						AND EXTRACT(YEAR FROM s.request_date)='".$year."'
						AND EXTRACT(MONTH FROM s.request_date)='".$month."'
						$cond
						GROUP BY EXTRACT(MONTH FROM s.request_date), g.group_code
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

	function getStatByResult($year, $month, $fromdate, $todate, $group, $withResult){
		global $db;

		if (($fromdate)&&($todate)){
			$cond = "AND (s.request_date >= '".$fromdate."' AND s.request_date <= '".$todate."')";
		}

		if ($withResult){

			$this->sql="SELECT count(g.group_code) AS stat_result, EXTRACT(MONTH FROM s.request_date) AS month,
							EXTRACT(YEAR FROM s.request_date) AS year, g.name AS grp_name,
							g.group_code AS grp_code
							FROM seg_radio_serv AS s
							INNER JOIN care_test_request_radio AS d ON s.refno=d.refno
							INNER JOIN seg_radio_services AS ss ON d.service_code=ss.service_code
							INNER JOIN seg_radio_service_groups AS g ON g.group_code=ss.group_code
							INNER JOIN care_person AS p ON p.pid=s.pid
							INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
							INNER JOIN care_test_findings_radio AS rs ON d.batch_nr = rs.batch_nr
							WHERE s.status NOT IN($this->dead_stat)
							 AND d.status NOT IN($this->dead_stat)
							AND EXTRACT(YEAR FROM s.request_date)='".$year."'
							AND EXTRACT(MONTH FROM s.request_date)='".$month."'
							AND g.group_code='".$group."'
							GROUP BY EXTRACT(MONTH FROM s.request_date), g.group_code
							ORDER BY g.name";



		}else{
			$this->sql="SELECT count(g.group_code) AS stat_result, EXTRACT(MONTH FROM s.request_date) AS month,
							EXTRACT(YEAR FROM s.request_date) AS year, g.name AS grp_name,
							g.group_code AS grp_code
							FROM seg_radio_serv AS s
							INNER JOIN care_test_request_radio AS d ON s.refno=d.refno
							INNER JOIN seg_radio_services AS ss ON d.service_code=ss.service_code
							INNER JOIN seg_radio_service_groups AS g ON g.group_code=ss.group_code
							INNER JOIN care_person AS p ON p.pid=s.pid
							INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
							WHERE s.status NOT IN($this->dead_stat)
							AND d.status NOT IN($this->dead_stat)
							AND EXTRACT(YEAR FROM s.request_date)='".$year."'
							AND EXTRACT(MONTH FROM s.request_date)='".$month."'
							AND NOT EXISTS(SELECT rs.* FROM care_test_findings_radio AS rs
												WHERE d.batch_nr = rs.batch_nr)
							AND g.group_code='".$group."'
							GROUP BY EXTRACT(MONTH FROM s.request_date), g.group_code
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
			$cond = "AND (s.request_date >= '".$fromdate."' AND s.request_date <= '".$todate."')";
		}

		if ($withResult){

			/*$this->sql="SELECT count(g.group_code) AS stat_result,
							EXTRACT(MONTH FROM s.request_date) AS month,
							EXTRACT(YEAR FROM s.request_date) AS year,
							g.name AS grp_name, g.group_code AS grp_code, e.encounter_type
							FROM seg_radio_serv AS s
							INNER JOIN care_test_request_radio AS d ON s.refno=d.refno
							INNER JOIN seg_radio_services AS ss ON d.service_code=ss.service_code
							INNER JOIN seg_radio_service_groups AS g ON g.group_code=ss.group_code
							INNER JOIN care_person AS p ON p.pid=s.pid
							INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
							INNER JOIN care_test_findings_radio AS rs ON d.batch_nr = rs.batch_nr
							WHERE s.status NOT IN($this->dead_stat)
							AND d.status NOT IN($this->dead_stat)
							AND EXTRACT(YEAR FROM s.request_date)='".$year."'
							AND EXTRACT(MONTH FROM s.request_date)='".$month."'
							AND g.group_code='".$group."'
							AND e.encounter_type IN(".$enctype.")
							GROUP BY EXTRACT(MONTH FROM s.request_date), g.group_code, e.encounter_type IN(".$enctype.")
							ORDER BY g.name, e.encounter_type";*/

			#commented by angelo m. 09.09.2010
			#revised code by angelo m. 09.09.2010
			$this->sql="SELECT COUNT(fin.stat_result) AS stat_result,
												 fin.group_code,
												 fin.month AS month,
												 fin.year AS year,
												fin.encounter_type
											FROM
											(	SELECT COUNT(s.pid) AS stat_result,
												 s.pid,
												 ss.group_code,
												 EXTRACT(MONTH FROM s.request_date) AS month,
												 EXTRACT(YEAR FROM s.request_date) AS year,
												 e.encounter_type
												 from seg_radio_serv as s
												 INNER JOIN care_test_request_radio AS d ON s.refno=d.refno
												 INNER JOIN seg_radio_services AS ss ON d.service_code=ss.service_code
												 INNER JOIN seg_radio_service_groups AS g
												 ON g.group_code=ss.group_code
													AND g.group_code = '".$group."'
												 INNER JOIN care_person AS p ON p.pid=s.pid
												 INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
												 INNER JOIN care_test_findings_radio AS rs ON d.batch_nr = rs.batch_nr
												WHERE s.status NOT IN('deleted','hidden','inactive','void')
												 AND EXTRACT(YEAR FROM s.request_date)='".$year."'
													 AND EXTRACT(MONTH FROM s.request_date) IN ('".$month."')
													AND e.encounter_type='".$enctype."'
												 AND d.status='done'
												GROUP BY EXTRACT(MONTH FROM s.request_date),s.pid
												ORDER BY s.request_date
											) AS fin;";
		}else{
			/*$this->sql="SELECT count(g.group_code) AS stat_result, EXTRACT(MONTH FROM s.request_date) AS month,
							EXTRACT(YEAR FROM s.request_date) AS year,
							g.name AS grp_name, g.group_code AS grp_code, e.encounter_type
							FROM seg_radio_serv AS s
							INNER JOIN care_test_request_radio AS d ON s.refno=d.refno
							INNER JOIN seg_radio_services AS ss ON d.service_code=ss.service_code
							INNER JOIN seg_radio_service_groups AS g ON g.group_code=ss.group_code
							INNER JOIN care_person AS p ON p.pid=s.pid
							INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
							WHERE s.status NOT IN($this->dead_stat)
							AND d.status NOT IN($this->dead_stat)
							AND EXTRACT(YEAR FROM s.request_date)='".$year."'
							AND EXTRACT(MONTH FROM s.request_date)='".$month."'
							AND NOT EXISTS(SELECT rs.* FROM care_test_findings_radio AS rs
							WHERE d.batch_nr = rs.batch_nr)
							AND g.group_code='".$group."'
							AND e.encounter_type IN(".$enctype.")
							GROUP BY EXTRACT(MONTH FROM s.request_date), g.group_code, e.encounter_type IN(".$enctype.")
							ORDER BY g.name, e.encounter_type"; */

			#commented by angelo m. 09.09.2010
			#revised code by angelo m. 09.09.2010
			$this->sql="SELECT COUNT(s.pid) AS stat_result,
												 s.pid,
												 ss.group_code,
												 EXTRACT(MONTH FROM s.request_date) AS month,
												 EXTRACT(YEAR FROM s.request_date) AS year,
												 e.encounter_type
												 from seg_radio_serv as s
												 INNER JOIN care_test_request_radio AS d ON s.refno=d.refno
												 INNER JOIN seg_radio_services AS ss ON d.service_code=ss.service_code
												 INNER JOIN seg_radio_service_groups AS g
												 ON g.group_code=ss.group_code
													AND g.group_code = '".$group."'
												 INNER JOIN care_person AS p ON p.pid=s.pid
												 INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
												 INNER JOIN care_test_findings_radio AS rs ON d.batch_nr = rs.batch_nr
												WHERE s.status NOT IN('deleted','hidden','inactive','void')
												 AND EXTRACT(YEAR FROM s.request_date)='".$year."'
													 AND EXTRACT(MONTH FROM s.request_date) IN ('".$month."')
													AND e.encounter_type='".$enctype."'
												 AND d.status='done'
												GROUP BY EXTRACT(MONTH FROM s.request_date)
												ORDER BY s.request_date";

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
	#---------------------------------------------------

	#added by VAN 06-14-08
	function getChargeType($cond="",$order="charge_name"){
			global $db;
		 $this->sql="SELECT * FROM seg_type_charge $cond ORDER BY $order";

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

	function getLastIDChargeType(){
			global $db;
		 $this->sql="SELECT * FROM seg_type_charge ORDER BY id  DESC LIMIT 1";

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

	function getChargeTypeInfo($chargeType){
			global $db;
		 $this->sql="SELECT * FROM seg_type_charge WHERE id='$chargeType'";

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
	#---------------------

	#added by VAN 06-19-08
	function getListRadioSectionRequest_Status($grp_kind, $grpview, $grp_code, $datefrom, $dateto, $discountID, $pat_type, $fromtime, $totime, $doctor_nr){
		global $db;

		#edited by VAN 05-09-2011
		if (($grp_code == "all")&&($discountID == "all")&&(!($datefrom))&&(!($dateto))){
			$cond = "";

		}elseif (($grp_code == "all")&&($discountID != "all")&&(!($datefrom))&&(!($dateto))){

			#$cond = " AND c.discountid = '".$discountID."'";
			$cond = " AND s.discountid = '".$discountID."'";

		}elseif (($grp_code != "all")&&($discountID == "all")&&(!($datefrom))&&(!($dateto))){

			$cond = " AND g.department_nr = '".$grp_code."'";

		}elseif (($grp_code != "all")&&($discountID != "all")&&(!($datefrom))&&(!($dateto))){

			$cond = " AND g.department_nr = '".$grp_code."' AND s.discountid = '".$discountID."'";

		}elseif (($grp_code == "all")&&($discountID == "all")&& (($datefrom)&& ($dateto))){
			#$cond = " AND (s.request_date >= '".$datefrom."' AND s.request_date <= '".$dateto."')";

		}elseif (($grp_code != "all")&&($discountID == "all")&& (($datefrom)&& ($dateto))){
			#$cond = " AND g.department_nr = '".$grp_code."' AND (s.request_date >= '".$datefrom."'
			#				 AND s.request_date <= '".$dateto."')";
			$cond = " AND g.department_nr = '".$grp_code."'";

		}elseif (($grp_code == "all")&&($discountID != "all")&& (($datefrom)&& ($dateto))){
			#$cond = " AND (s.request_date >= '".$datefrom."' AND s.request_date <= '".$dateto."')
			#			AND s.discountid = '".$discountID."'";
			$cond = " AND s.discountid = '".$discountID."'";
		}else{
			#$cond = " AND g.department_nr = '".$grp_code."'
						#AND (s.request_date >= '".$datefrom."' AND s.request_date <= '".$dateto."')
						#AND s.discountid = '".$discountID."'";
			$cond = " AND g.department_nr = '".$grp_code."' AND s.discountid = '".$discountID."'";
		}

		if (($datefrom)&&($dateto))	{
			if ($grp_kind=='wo_result')
				$date_cond = " AND  (r.request_date >= '".$datefrom."' AND r.request_date <= '".$dateto."')";
			else
				#edited by VAN 05-09-2011
				$date_cond = " AND TRIM(SUBSTRING(rs.findings_date,LOCATE('\"',rs.findings_date)+1,10)) BETWEEN '".$datefrom."' AND '".$dateto."'";
		}else
			$date_cond = "";

		if ($grp_kind=='w_result'){
				$join_rep = " INNER JOIN care_test_findings_radio AS rs
								ON rs.batch_nr = d.batch_nr";
				$join_rep_cond = "";
				#$cond = " AND (d.service_date >= '".$datefrom."' AND d.service_date <= '".$dateto."')";
		}elseif($grp_kind=='wo_result'){
				$join_rep = "";
				$join_rep_cond = " AND NOT EXISTS(SELECT rs.* FROM care_test_findings_radio AS rs
														WHERE rs.batch_nr = d.batch_nr)";
				#$cond = "";
		}elseif($grp_kind=='all'){
				$join_rep = "";
				$join_rep_cond = "";
				#$cond = "";
				#$cond = " AND ((s.request_date >= '".$datefrom."' AND s.request_date <= '".$dateto."')
				#								OR (d.service_date >= '".$datefrom."' AND d.service_date <= '".$dateto."'))";
		}

		if ($doctor_nr){
			#$sql_cond = " SELECT IF(LOCATE(',',fn_get_personell_name('".$doctor_nr."'))>0,TRIM(SUBSTRING(fn_get_personell_name('".$doctor_nr."'),1,LOCATE(',',fn_get_personell_name('".$doctor_nr."'))-1)),fn_get_personell_name('".$doctor_nr."')) AS drname";
            $sql_cond = " SELECT IF(LOCATE(',',fn_get_personell_name2('".$doctor_nr."'))>0,TRIM(SUBSTRING(fn_get_personell_name2('".$doctor_nr."'),1,LOCATE(',',fn_get_personell_name2('".$doctor_nr."'))-1)),fn_get_personell_name2('".$doctor_nr."')) AS drname";
			$rs = $db->Execute($sql_cond);
			if (is_object($rs))
				$row_dr = $rs->FetchRow();
			#echo "drname = ".$row_dr['drname'];
            $doctor_cond = " AND (rs.doctor_in_charge LIKE '%".$row_dr['drname']."%' OR CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$doctor_nr."%')";
		}else
			$doctor_cond = "";

		#echo "type = ".$pat_type;
		if ($pat_type){
			if ($pat_type==1){
				#ER PATIENT
				$cond .= " AND enc.encounter_type IN (1) ";

				if ((($fromtime!='00:00:00')&&($totime!='00:00:00'))&& (($datefrom)&& ($dateto)))
					$cond .= "AND (request_time >= '".$fromtime."' AND request_time <= '".$totime."')";

			}elseif ($pat_type==2){
				#ADMITTED PATIENT
				$cond .= " AND enc.encounter_type IN (3,4) ";

				if ((($fromtime!='00:00:00')&&($totime!='00:00:00'))&& (($datefrom)&& ($dateto)))
					$cond .= "AND (request_time >= '".$fromtime."' AND request_time <= '".$totime."')";

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
                     CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) AS doc_nr,
					 ss.name AS service_name, s.*, d.*, ss.*,
					 p.*, enc.*, dept.name_formal, dept.name_short AS dept_name,
					 rs.doctor_in_charge ,
					 rs.findings_date, p.sex ";

		if ($grpview==1){
			$grp = "GROUP BY s.refno";
		}else{
			$grp = "";
		}
		 $order = " ORDER BY p.name_last, p.name_first, s.refno, g.name";


		$this->sql = "SELECT $items
							FROM seg_radio_serv AS s
							LEFT JOIN care_test_request_radio AS d
								ON s.refno=d.refno
							LEFT JOIN seg_radio_services AS ss
								ON d.service_code=ss.service_code
							LEFT JOIN seg_radio_service_groups AS g
								ON g.group_code=ss.group_code
							INNER JOIN care_department AS dept
								ON dept.nr=g.department_nr
							INNER JOIN care_person AS p
								ON p.pid=s.pid
							LEFT JOIN care_encounter AS enc
								ON s.encounter_nr = enc.encounter_nr
							LEFT JOIN care_test_findings_radio AS rs
								ON rs.batch_nr = d.batch_nr
                            LEFT JOIN care_test_findings_radio_doc_nr AS dr
                                ON dr.batch_nr = d.batch_nr
							$join_rep
							WHERE s.status NOT IN($this->dead_stat)
							AND d.status NOT IN($this->dead_stat)
							AND (request_flag IS NOT NULL OR is_cash=0)
							AND (findings IS NOT NULL AND findings<>'a:0:{}')
							$join_rep_cond
							$cond
							$date_cond
							$doctor_cond
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

	function getAllRadioDeptInfo($group_code){
			global $db;
		 /*
		 $this->sql="SELECT * FROM seg_lab_service_groups
						 WHERE department_nr='$group_code'
						 AND status NOT IN ($this->dead_stat)";
		*/
		$this->sql ="SELECT d.name_formal, d.name_short AS dept_name, d.*
					 FROM care_department AS d
					 WHERE nr='$group_code'
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

	function getSumPerTransaction($refno){
			global $db;
		 $this->sql="SELECT sum(price_cash) AS price_cash,
						 sum(price_cash_orig) AS price_cash_orig,
						 sum(price_charge) AS price_charge
						 FROM care_test_request_radio
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

	function getSumPaidPerTransaction($refno,$pid){
			global $db;

			$this->sql = "SELECT r.ref_no,r.ref_source,SUM(CASE WHEN r.amount_due then r.amount_due else 0.00 end) AS amount_paid,p.*
										FROM seg_pay_request AS r
										INNER JOIN seg_pay AS p ON r.or_no=p.or_no
										WHERE ref_no='$refno' AND ref_source='RD' AND pid='$pid'
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

	#edited by VAN 05-09-2011
	function getRequestedServicesPerGroup($refno, $grpcode) {
		global $db;

		if ($grpcode!='all')
			$isgroup = " AND sg.department_nr = '$grpcode' ";
		else
			$isgroup = " ";

		$this->sql="SELECT sd.*, ss.name, sg.group_code, sg.name AS groupnm,
					ss.is_socialized, dept.name_formal, dept.name_short AS dept_name,
					rs.doctor_in_charge ,
					rs.findings_date,
					TRIM(SUBSTRING(rs.findings_date,LOCATE('\"',rs.findings_date)+1,10)) AS findings_date
					FROM seg_radio_serv AS s
					INNER JOIN care_test_request_radio AS sd ON s.refno = sd.refno
					INNER JOIN seg_radio_services AS ss ON sd.service_code = ss.service_code
					INNER JOIN seg_radio_service_groups AS sg ON ss.group_code = sg.group_code
					INNER JOIN care_department AS dept ON dept.nr=sg.department_nr
					LEFT JOIN care_test_findings_radio AS rs ON rs.batch_nr = sd.batch_nr
					WHERE  s.refno = '$refno'
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

	function getPatientList($datefrom, $dateto, $fromtime, $totime, $grp_kind, $grp_code, $discountID, $enctype, $grp, $doctor_nr=''){
		global $db;

		if ($grp){
			$groupby = " GROUP BY r.refno ";
		}else{
			$groupby = "";
		}

		if (($fromtime!='00:00:00')&&($totime!='00:00:00')){
			$time_cond = " AND (r.request_time >= '".$fromtime."' AND r.request_time <= '".$totime."')";

		}else
			$time_cond = "";

		if (($datefrom)&&($dateto))	{
			if ($grp_kind=='wo_result')
			$date_cond = " AND  (r.request_date >= '".$datefrom."' AND r.request_date <= '".$dateto."')";
			else
				#edited by VAN 05-09-2011
				$date_cond = " AND TRIM(SUBSTRING(rs.findings_date,LOCATE('\"',rs.findings_date)+1,10)) BETWEEN '".$datefrom."' AND '".$dateto."'";
		}else
			$date_cond = "";

		if ($grp_kind=='w_result'){
				/*$join_rep = " INNER JOIN care_test_findings_radio AS rs
								ON rs.batch_nr = d.batch_nr";
				$join_rep_cond = "";
				*/
				$join_rep = " AND (findings IS NOT NULL AND findings<>'a:0:{}') ";
		}elseif($grp_kind=='wo_result'){
				/*$join_rep = "";
				$join_rep_cond = " AND NOT EXISTS(SELECT rs.* FROM care_test_findings_radio AS rs
														WHERE rs.batch_nr = d.batch_nr)";
				*/
				$join_rep = " AND (findings IS NULL OR findings='a:0:{}') ";
		}elseif($grp_kind=='all'){
				$join_rep = "";
				#$join_rep_cond = "";
		}

		if ($grp_code == "all")
			$group_cond = "";
		else{
			$group_cond = " AND g.department_nr = '".$grp_code."' ";
		}

		if ($discountID == "all")
			$class_cond = "";
		else{
			$class_cond = " AND r.discountid = '".$discountID."' ";
		}

		if ($doctor_nr){
            #$sql_cond = " SELECT IF(LOCATE(',',fn_get_personell_name('".$doctor_nr."'))>0,TRIM(SUBSTRING(fn_get_personell_name('".$doctor_nr."'),1,LOCATE(',',fn_get_personell_name('".$doctor_nr."'))-1)),fn_get_personell_name('".$doctor_nr."')) AS drname";
			$sql_cond = " SELECT IF(LOCATE(',',fn_get_personell_name2('".$doctor_nr."'))>0,TRIM(SUBSTRING(fn_get_personell_name2('".$doctor_nr."'),1,LOCATE(',',fn_get_personell_name2('".$doctor_nr."'))-1)),fn_get_personell_name2('".$doctor_nr."')) AS drname ";
												$rs = $db->Execute($sql_cond);
			if (is_object($rs))
				$row_dr = $rs->FetchRow();
			#echo "drname = ".$row_dr['drname'];
            $doctor_cond = " AND (rs.doctor_in_charge LIKE '%".$row_dr['drname']."%' OR CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$doctor_nr."%')";
		}else
			$doctor_cond = "";

		#edited by VAN 05-09-2011
		$this->sql="SELECT e.encounter_type,e.current_dept_nr,e.current_ward_nr, e.current_room_nr,
                    p.date_birth,CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) AS doc_nr,
					IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),p.age) AS age,
					r.*,d.batch_nr,
					request_flag, p.sex, rs.doctor_in_charge,
					TRIM(SUBSTRING(rs.findings_date,LOCATE('\"',rs.findings_date)+1,10)) AS findings_date
					FROM seg_radio_serv AS r
					INNER JOIN care_test_request_radio AS d ON d.refno=r.refno
					INNER JOIN care_person AS p ON p.pid=r.pid
					LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr
					LEFT JOIN care_test_findings_radio AS rs ON rs.batch_nr = d.batch_nr
                    LEFT JOIN care_test_findings_radio_doc_nr AS dr ON dr.batch_nr = d.batch_nr
					INNER JOIN seg_radio_services AS s ON s.service_code=d.service_code
					INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code
					INNER JOIN care_department AS dept ON dept.nr=g.department_nr
					WHERE  r.status NOT IN($this->dead_stat)
					AND d.status NOT IN($this->dead_stat)
					AND (request_flag IS NOT NULL OR is_cash=0)
					$join_rep
					$enctype
					$date_cond
					$time_cond
					$join_rep_cond
					$group_cond
					$class_cond
					$doctor_cond
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

	function getPatientListDetails($refno){
		global $db;

		$this->sql="SELECT d.price_cash, r.*, d.service_code, s.name AS service_name, g.department_nr,
								dept.name_formal AS dept_name_short, dept.name_short AS dept_name_short, r.pid, d.request_flag
								FROM seg_radio_serv AS r
								INNER JOIN care_test_request_radio AS d ON d.refno=r.refno
								INNER JOIN seg_radio_services AS s ON s.service_code=d.service_code
								INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code
								INNER JOIN care_department AS dept ON dept.nr = g.department_nr
								WHERE r.refno = '".$refno."'
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

	function getStatByYearMonth($year, $month){
		global $db;

		$this->sql="SELECT request_date, count(pid) AS totalpat
						FROM seg_radio_serv AS s
						WHERE EXTRACT(YEAR FROM s.request_date) = '".$year."'
						AND EXTRACT(MONTH FROM s.request_date) = '".$month."'
						GROUP BY extract(YEAR_MONTH FROM request_date)";

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
	#-----------------------

	#added by VAN 06-20-08
	function getListRadioClassification($rpt_cases, $tpl, $sc, $datefrom, $dateto, $not){
		global $db;

		#if there's date
		if (($datefrom)&&($dateto))	{
			$date_cond = " AND  (s.request_date >= '".$datefrom."' AND s.request_date <= '".$dateto."')";
		}else
			$date_cond = "";


		if ($rpt_cases==$tpl)
			$cond = " AND  s.is_tpl = '1' ";
		elseif ($rpt_cases==$sc)
			$cond = " AND (p.senior_ID != '' OR p.senior_ID != NULL) ";
		elseif ($rpt_cases==$not)
			$cond = " AND  ((p.senior_ID = '' OR p.senior_ID = NULL) AND s.is_tpl = '0' AND s.discountID='') ";
		elseif($rpt_cases=='all')
			$cond = "";
		else
			$cond = " AND s.type_charge = '$rpt_cases' ";

		$items = "s.discountid AS classID, s.pid AS patientID,
					 g.name AS grp_name, other_name AS grp_name2,
					 ss.name AS service_name, s.*, d.*, ss.*,
					 p.*, c.grant_dte, c.sw_nr, enc.*, dept.name_formal, dept.name_short AS dept_name ";

		$grp = "GROUP BY s.refno";

		$order = " ORDER BY p.name_last, p.name_first, s.refno, g.name";


		$this->sql = "SELECT $items
							FROM seg_radio_serv AS s
							INNER JOIN care_test_request_radio AS d
								ON s.refno=d.refno
							INNER JOIN seg_radio_services AS ss
								ON d.service_code=ss.service_code
							INNER JOIN seg_radio_service_groups AS g
								ON g.group_code=ss.group_code
							INNER JOIN care_department AS dept
								ON dept.nr=g.department_nr
							INNER JOIN care_person AS p
								ON p.pid=s.pid
							LEFT JOIN care_encounter AS enc
								ON s.encounter_nr = enc.encounter_nr
							LEFT JOIN seg_charity_grants AS c
								ON s.encounter_nr=c.encounter_nr
							WHERE s.status NOT IN($this->dead_stat)
							AND d.status NOT IN($this->dead_stat)
							$date_cond
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
	#----------------

	#added by VAN 07-11-08
	function countSearchImpressions($searchkey='',$maxcount=100,$offset=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$this->sql = "SELECT i.id AS impID, i.codename AS impcode, i.description AS impdesc, f.*
						FROM seg_radio_findings_code AS f
						LEFT JOIN seg_radio_impression_code AS i ON i.id=f.impression
						WHERE (f.codename LIKE '%".$keyword."%' OR f.description LIKE '%".$keyword."%')
						ORDER BY f.id ASC";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function SearchImpressions($searchkey='',$maxcount=100,$offset=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$this->sql = "SELECT i.*
						FROM seg_radio_impression_code AS i
						WHERE (i.codename LIKE '%".$keyword."%' OR i.description LIKE '%".$keyword."%')
						ORDER BY i.id ASC";

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

	//edited by celsy 08/10/13
	function saveRadioServiceImpression($name, $code, $id, $department_nr, $mode)	{
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;

		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

		#$name = str_replace("*","<br> *",$name);
		#$name = substr($name,5);

		if ($mode=='save'){
			$this->sql="INSERT INTO seg_radio_impression_code(department_nr, codename, description, status, history, create_id, create_dt, modify_id, modify_dt) ".
				"VALUES('".$department_nr."','".$code."', '".$name."', '', CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW())";

		}else{
			$this->sql="UPDATE seg_radio_impression_code SET
									department_nr = '".$department_nr."',
									codename = '".$code."',
									description='".$name."',
									status='',
									history=CONCAT(history,'Update: ',NOW(),' [$userid]\\n'),
									modify_id='$userid',
									modify_dt=NOW()
									WHERE id = '".$id."'";
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

	/*function saveRadioServiceImpression($name, $code, $id, $mode)	{
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;

		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

		#$name = str_replace("*","<br> *",$name);
		#$name = substr($name,5);

		if ($mode=='save'){
			$this->sql="INSERT INTO seg_radio_impression_code(codename, description, status, history, create_id, create_dt, modify_id, modify_dt) ".
				"VALUES('".$code."', '".$name."', '', CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW())";

		}else{
			$this->sql="UPDATE seg_radio_impression_code SET
									codename = '".$code."',
									description='".$name."',
									status='',
									history=CONCAT(history,'Update: ',NOW(),' [$userid]\\n'),
									modify_id='$userid',
									modify_dt=NOW()
									WHERE id = '".$id."'";
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
	} */

	function deleteServiceImpression($id){
		global $HTTP_SESSION_VARS;
		#$history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." [".$HTTP_SESSION_VARS['sess_user_name']."]\\n");
		$this->sql="DELETE FROM seg_radio_impression_code
					WHERE id = '$id'";
			return $this->Transact();
	}
	#----------------------

	#added by VAN 10-17-08
	function deleteDoctorPartner($id){
		global $HTTP_SESSION_VARS;
		$this->sql="DELETE FROM seg_radio_doctor_group
					WHERE group_nr = '$id'";
			return $this->Transact();

	}
	#--------------

	#added by VAN 07-07-08
	function countSearchFindings($searchkey='',$maxcount=100,$offset=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$this->sql = "SELECT i.*
						FROM seg_radio_impression_code AS i
						WHERE (i.codename LIKE '%".$keyword."%' OR i.description LIKE '%".$keyword."%')
						ORDER BY i.id ASC";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function SearchFindings($searchkey='',$maxcount=100,$offset=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$this->sql = "SELECT i.id AS impID, i.codename AS impcode, i.description AS impdesc, f.*
						FROM seg_radio_findings_code AS f
						LEFT JOIN seg_radio_impression_code AS i ON i.id=f.impression
						WHERE (f.codename LIKE '%".$keyword."%' OR f.description LIKE '%".$keyword."%')
						ORDER BY f.id ASC";

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}
	#edited by celsy 08/17/10
 function getAllRadioFindingsInfo($id=0, $dept_nr=0){
		 global $db;

		$cond_sql = "";
		if (($id)&&($dept_nr))
				$cond_sql = "WHERE f.department_nr='".$dept_nr."' AND f.id='".$id."' ";
		elseif (($id)&&(!$dept_nr))
				$cond_sql = "WHERE f.id='".$id."' ";
		elseif ((!$id)&&($dept_nr))
				$cond_sql = "WHERE f.department_nr='".$dept_nr."' ";

		$this->sql="SELECT f.*,i.id AS impID, i.codename AS impcode, i.description AS impdesc
						FROM seg_radio_findings_code AS f
						LEFT JOIN seg_radio_impression_code AS i ON i.id=f.impression
						".$cond_sql."
						ORDER BY f.codename ASC";

		#echo "sql = ".$this->sql;
			if ($this->result=$db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
						if ($id){
					return $this->result->FetchRow();
				}else{
					return $this->result;
				}
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}
/*	function getAllRadioFindingsInfo($id=0){
			global $db;
		if ($id){

			#$this->sql="SELECT f.* FROM seg_radio_findings_code AS f
			#			 WHERE id='$id'";

			$this->sql="SELECT f.*,i.id AS impID, i.codename AS impcode, i.description AS impdesc
						FROM seg_radio_findings_code AS f
						LEFT JOIN seg_radio_impression_code AS i ON i.id=f.impression
						WHERE f.id='$id'
						ORDER BY f.codename ASC";

		}else{

			#$this->sql="SELECT f.* FROM seg_radio_findings_code AS f
			#			 ORDER BY codename ASC";

			$this->sql="SELECT f.*, i.id AS impID, i.codename AS impcode, i.description AS impdesc
						FROM seg_radio_findings_code AS f
						LEFT JOIN seg_radio_impression_code AS i ON i.id=f.impression
						ORDER BY f.codename ASC";

		}

		#echo "sql = ".$this->sql;
			if ($this->result=$db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
						if ($id){
					return $this->result->FetchRow();
				}else{
					return $this->result;
				}
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}*/

	function saveRadioServiceFindings($name, $code, $impression, $id, $department_nr, $mode)	{
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;

		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

		if ($mode=='save'){
//			$this->sql="INSERT INTO seg_radio_findings_code(codename, description, impression, status, history, create_id, create_dt, modify_id, modify_dt) ".
//				"VALUES('".$code."', '".$name."','".$impression."', '', CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW())";
			$this->sql="INSERT INTO seg_radio_findings_code(department_nr, codename, description, impression, status, history, create_id, create_dt, modify_id, modify_dt) ".
				"VALUES('".$department_nr."','".$code."', '".$name."','".$impression."', '', CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW())";

		}
		else{
			$this->sql="UPDATE seg_radio_findings_code SET
									department_nr = '".$department_nr."',
									codename = '".$code."',
									description='".$name."',
									impression='".$impression."',
									status='',
									history=CONCAT(history,'Update: ',NOW(),' [$userid]\\n'),
									modify_id='$userid',
									modify_dt=NOW()
									WHERE id = '".$id."'";
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
	#---------------------end celsy----------------------------#

	function codeExists($code){
		global $db;
		 $this->sql="SELECT f.* FROM seg_radio_findings_code AS f
						 WHERE codename='$code'";
		#echo "sql = ".$this->sql;
			if ($this->result=$db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
						return true;
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}

	function codeExists_imp($code){
		global $db;
		 $this->sql="SELECT f.* FROM seg_radio_impression_code AS f
						 WHERE codename='$code'";
		#echo "sql = ".$this->sql;
			if ($this->result=$db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
						return true;
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}

	function deleteServiceFindings($id){
		global $HTTP_SESSION_VARS;
		#$history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." [".$HTTP_SESSION_VARS['sess_user_name']."]\\n");
		$this->sql="DELETE FROM seg_radio_findings_code
					WHERE id = '$id'";
			return $this->Transact();
	}
	#-----------------

	#added by VAN 07-10-08
		function getAllRadioImpressionInfo($id=0, $dept_nr=''){
		global $db;

		$cond_sql = "";
		if (($id)&&($dept_nr))
				$cond_sql = "WHERE imp.department_nr='".$dept_nr."' AND imp.id='".$id."' ";
		elseif (($id)&&(!$dept_nr))
				$cond_sql = "WHERE imp.id='".$id."' ";
		elseif ((!$id)&&($dept_nr))
				$cond_sql = "WHERE imp.department_nr='".$dept_nr."' ";

		/*$this->sql="SELECT imp.*, f.id AS finID, f.codename AS fincode, f.description AS findesc
						FROM seg_radio_impression_code AS imp
						LEFT JOIN seg_radio_findings_code AS f ON f.impression=imp.id
						".$cond_sql."
						ORDER BY imp.codename ASC";*/
		$this->sql="SELECT imp.*
						FROM seg_radio_impression_code AS imp
						".$cond_sql."
						ORDER BY imp.codename ASC";

		#echo "sql = ".$this->sql;

			if ($this->result=$db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
						if ($id)
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

	/*function getAllRadioImpressionInfo($id=0){
			global $db;
		if ($id){

			#$this->sql="SELECT f.* FROM seg_radio_impression_code AS f
		 #				 WHERE id='$id'";

			$this->sql="SELECT imp.*, f.id AS finID, f.codename AS fincode, f.description AS findesc
						FROM seg_radio_impression_code AS imp
						LEFT JOIN seg_radio_findings_code AS f ON f.impression=imp.id
						WHERE imp.id='$id'
						ORDER BY imp.codename ASC";

		}else{

			#$this->sql="SELECT f.* FROM seg_radio_impression_code AS f
			#			 ORDER BY codename ASC";


			$this->sql="SELECT imp.*, f.id AS finID, f.codename AS fincode, f.description AS findesc
						FROM seg_radio_impression_code AS imp
						LEFT JOIN seg_radio_findings_code AS f ON f.impression=imp.id
						ORDER BY imp.codename ASC";

		}
		#echo "sql = ".$this->sql;

			if ($this->result=$db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
						if ($id)
					return $this->result->FetchRow();
				else
					return $this->result;
			} else {
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}*/
	#---------------------

	#added by VAN 07-08-08
	function getPaidRequest($refno){
			global $db;

		$this->sql="SELECT pr.*,p.* FROM seg_pay_request AS pr
					INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
					LEFT JOIN care_test_request_radio AS d ON d.refno=pr.ref_no AND ref_source='RD' AND d.service_code=pr.service_code
					LEFT JOIN seg_radio_serv AS r ON r.refno=pr.ref_no	AND ref_source='RD'
					WHERE r.status NOT IN($this->dead_stat) AND d.status NOT IN($this->dead_stat)
					AND ref_no='$refno'
					AND ref_source = 'RD'";

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

	function getGrantedRequest($refno){
			global $db;

		$this->sql="SELECT gr.* FROM seg_granted_request AS gr
					LEFT JOIN care_test_request_radio AS d ON d.refno=gr.ref_no AND ref_source='RD' AND d.service_code=gr.service_code
					LEFT JOIN seg_radio_serv AS r ON r.refno=gr.ref_no	AND ref_source='RD'
					WHERE r.status NOT IN($this->dead_stat) AND d.statusNOT IN($this->dead_stat)
					AND ref_no='$refno'
					AND ref_source = 'RD'";

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

	function getFilmSize(){
			global $db;
		 $this->sql="SELECT * FROM seg_radio_film_size";

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
	#--------------------------

	#added by VAN 07-09-08
	function createRadioProcess($batch_nr, $sizes)	{
		global $db;
		global $HTTP_SESSION_VARS;
		$ret=FALSE;

		$userid = $HTTP_SESSION_VARS['sess_temp_userid'];

		#no_film_used
		$this->sql="INSERT INTO seg_radio_service_sized(batch_nr, id_size,no_film_used,status, history, create_id, create_dt, modify_id, modify_dt) ".
						 "VALUES('".$batch_nr."', ? , ? , '', CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW())";

		$ok=$db->Execute($this->sql,$sizes);
		$this->count=$db->Affected_Rows();
		return $ok;

	}

	function clearRadioProcess($batch_nr) {
		global $db;

		$this->sql = "DELETE FROM seg_radio_service_sized WHERE batch_nr=$batch_nr";
			return $this->Transact();
	}

	function getAllUnreturnedFilm(){
			global $db;
		 $this->sql="SELECT b.* FROM seg_radio_borrow AS b
					 WHERE ISNULL(date_returned) OR date_returned IN ('0000-00-00')";

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
	#------------------

	#added by VAN 08-01-08
	function getAllPatientRequestByShift($request_date, $fromhour, $tohour, $section, $encoder){
		global $db;

			$this->sql="SELECT rs.pid,rs.encounter_nr, rs.refno, rs.request_date, rs.request_time, rs.is_cash,
							rd.batch_nr AS film_no, rd.service_code, s.name, /*p.name_last, p.name_first,
							p.name_middle, p.date_birth, p.sex,
							IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
							p.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, spr.prov_name,
							sr.region_name, e.encounter_type,e.current_att_dr_nr,e.consulting_dr_nr,
							e.current_ward_nr, e.current_room_nr, e.current_dept_nr, rs.create_id,
							rs.modify_id, sp.or_no, pay.amount_due, gd.grant_no, s.price_cash,
							s.price_charge, rd.request_doctor, IF(rd.clinical_info!='',rd.clinical_info,e.er_opd_diagnosis) AS adm_diagnosis,
							CONCAT(	'Dr. ',CAST(SUBSTRING((SELECT name_first FROM care_person AS p WHERE p.pid=pr.pid),1,1) AS BINARY),
										IF(
											 (SELECT name_first FROM care_person AS p WHERE p.pid=pr.pid)='', ' ','. '
										 ),
									SUBSTRING((SELECT name_middle FROM care_person AS p WHERE p.pid=pr.pid), 1, 1),
										IF(
											 (SELECT name_middle FROM care_person AS p WHERE p.pid=pr.pid)='', ' ','. '
										 ),
									(SELECT name_last FROM care_person AS p WHERE p.pid=pr.pid)) AS dr_name
							FROM seg_radio_serv AS rs
							INNER JOIN care_test_request_radio AS rd ON rd.refno = rs.refno
							INNER JOIN seg_radio_services AS s ON s.service_code=rd.service_code
							INNER JOIN seg_radio_service_sized AS sz ON sz.batch_nr=rd.batch_nr
							INNER JOIN care_person AS p ON p.pid=rs.pid
							INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code
							LEFT JOIN care_personell AS pr ON pr.nr=rd.request_doctor

							LEFT JOIN seg_pay AS sp ON sp.pid=rs.pid AND (ISNULL(sp.cancel_date) OR sp.cancel_date='0000-00-00 00:00:00')
							LEFT JOIN seg_pay_request AS pay ON sp.or_no=pay.or_no AND pay.ref_no=rs.refno AND pay.ref_source='RD' AND pay.service_code=rd.service_code
							LEFT JOIN seg_granted_request AS gd ON gd.ref_no=rs.refno
									AND gd.ref_source='RD' AND gd.service_code=rd.service_code

							LEFT JOIN care_encounter AS e ON e.encounter_nr=rs.encounter_nr
							LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
							LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
							LEFT JOIN seg_provinces AS spr ON spr.prov_nr=sm.prov_nr
							LEFT JOIN seg_regions AS sr ON sr.region_nr=spr.region_nr
							WHERE rs.request_date LIKE '".$request_date."'
							AND rs.request_time >= '".$fromhour."' AND rs.request_time <= '".$tohour."'
							AND (pay.or_no!='' OR gd.grant_no!='')
							AND g.department_nr='".$section."'
							AND (rs.create_id='".$encoder."' OR rs.modify_id='".$encoder."')
														GROUP BY rs.pid,rs.refno,rd.service_code
							ORDER BY p.name_last, p.name_first, p.name_middle";


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

	//added by VAN 08-19-08
	function getStatFilmSize($fromdate, $todate){
		global $db;

		$this->sql="SELECT ls.service_code,ls.name,
						s.size, rs.id_size, count(rs.id_size) AS no_of_film
						FROM seg_radio_service_sized AS rs
						INNER JOIN seg_radio_film_size AS s ON s.id=rs.id_size
						INNER JOIN care_test_request_radio AS rd ON rd.batch_nr=rs.batch_nr
						INNER JOIN seg_radio_serv AS sr ON sr.refno=rd.refno
						INNER JOIN seg_radio_services AS ls ON ls.service_code=rd.service_code
						WHERE sr.request_date BETWEEN '".$fromdate."' AND '".$todate."'
						AND sr.status NOT IN('deleted','hidden','inactive','void')
						AND rd.status NOT IN('deleted','hidden','inactive','void')
						GROUP BY rd.service_code, rs.id_size";

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

	function getStatRadioServices($fromdate, $todate){
		global $db;
		/*
		$this->sql="SELECT rd.service_code,ls.name, count(rd.service_code) AS no_of_request
						FROM seg_radio_serv AS sr
						INNER JOIN care_test_request_radio AS rd ON rd.refno=sr.refno
						INNER JOIN seg_radio_services AS ls ON ls.service_code=rd.service_code
						LEFT JOIN seg_pay_request AS rp ON rp.service_code=rd.service_code AND rp.ref_source='RD'
						LEFT JOIN seg_granted_request AS rg ON rg.service_code=rd.service_code AND rg.ref_source='RD'
						WHERE (grant_no!='' OR or_no!='')
						AND sr.status NOT IN('deleted','hidden','inactive','void')
						AND rd.status NOT IN('deleted','hidden','inactive','void')
						AND sr.request_date BETWEEN '".$fromdate."' AND '".$todate."'
						GROUP BY rd.service_code";
		*/
		$this->sql="SELECT rd.service_code,ls.name, count(rd.service_code) AS no_of_request
						FROM seg_radio_serv AS sr
						INNER JOIN care_test_request_radio AS rd ON rd.refno=sr.refno
						INNER JOIN seg_radio_services AS ls ON ls.service_code=rd.service_code
						WHERE sr.status NOT IN('deleted','hidden','inactive','void')
						AND rd.status NOT IN('deleted','hidden','inactive','void')
						AND sr.request_date BETWEEN '".$fromdate."' AND '".$todate."'
						GROUP BY rd.service_code";

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
	//-----------------------------

	#added by VAN 09-11-08

	function serializePersonnelNr($elem,$pers_type){

		$i=1;
		foreach($elem as $key=>$value){
			$tmp_elem[$pers_type.'+'.$i] = $value;
			$i++;
		}
		return serialize($tmp_elem);
	}

	function saveDoctorPartner(&$data){
		global $db,$sql_LIKE,$HTTP_SESSION_VARS;

		extract($data);

		$history = "Created ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";

		$modify_id = $HTTP_SESSION_VARS['sess_user_name'];
		$create_id = $HTTP_SESSION_VARS['sess_user_name'];

		#$doctor_member = $this->serializePersonnelNr($doctor,'doctor');

		$index= "group_name,status,history,modify_id,modify_dt,create_id,create_dt";

		$elems="'$group_name','','$history','$modify_id',NOW(),'$create_id', NOW()";
		$this->sql="INSERT INTO seg_radio_doctor_group
							($index)
						VALUES
							($elems)";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}

	function saveDoctorMember($group_nr, $doctor_member_array){
		global $db;
		global $HTTP_SESSION_VARS;

		#echo "grp = ".$group_nr;
		#print_r($doctor_member_array);
		$this->sql = "INSERT INTO seg_radio_doctor_member(group_nr,doctor_member)
									VALUES($group_nr,?)";

		if($buf=$db->Execute($this->sql,$doctor_member_array)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}

	function updateDoctorPartner(&$data, $group_nr){
		global $db,$sql_LIKE,$HTTP_SESSION_VARS;

		$this->_useEncounterOp();
		extract($data);

		$history = $this->ConcatHistory("Updated ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		$modify_id = $HTTP_SESSION_VARS['sess_user_name'];

		$doctor_member = $this->serializePersonnelNr($doctor,'doctor');

		$elems= "group_name='$group_name',doctor_member='$doctor_member',
					history=".$history.",
					modify_id='$modify_id ',
					modify_dt=NOW()
					";

		$this->sql="UPDATEseg_radio_doctor_group ".
						" SET $elems ".
						" WHERE group_nr = '$group_nr'";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}
	#---------------

		#added by VAN 10-09-08
		function countSearchAllPartners($searchkey='',$maxcount=100,$offset=0) {
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				#$suchwort=$searchkey;
				$searchkey = str_replace("^","'",$searchkey);
				$keyword=addslashes($searchkey);

				$this->sql = "SELECT * FROM seg_radio_doctor_group
											WHERE (group_name LIKE '%".$keyword."%')
											ORDER BY group_name";

				#echo "sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		function SearchAllPartners($searchkey='',$maxcount=100,$offset=0){
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				#$suchwort=$searchkey;
				$searchkey = str_replace("^","'",$searchkey);
				$keyword=addslashes($searchkey);

				$this->sql = "SELECT * FROM seg_radio_doctor_group
											WHERE (group_name LIKE '%".$keyword."%')
											ORDER BY group_name";

				if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
				}else{return false;}
		}

		function getAllGroupMembers($grp_nr) {
				global $db, $sql_LIKE, $root_path;

				$this->sql = "SELECT m.doctor_member, p.name_last, p.name_first,
												p.name_middle, g.group_name
												FROM seg_radio_doctor_member AS m
												INNER JOIN seg_radio_doctor_group AS g ON g.group_nr=m.group_nr
												INNER JOIN care_personell AS pr ON pr.nr=m.doctor_member
												INNER JOIN care_person AS p ON p.pid=pr.pid
												WHERE m.group_nr='".$grp_nr."'
												ORDER BY name_last, name_first";

				#echo "sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		function getGroupName($grp_nr) {
				global $db, $sql_LIKE, $root_path;

				$this->sql = "SELECT * FROM seg_radio_doctor_group
											WHERE group_nr='".$grp_nr."'";

				#echo "sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result->FetchRow();
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		function getAllRadioDoctor($role,$nr){
				global $db, $sql_LIKE, $root_path;
                if($nr!=''){
                    $cond = "AND ps.nr NOT IN ($nr)";
                }else{
                    $cond = "AND ps.nr NOT IN (000000)";
                }
				$this->sql = "SELECT a.personell_nr,CONCAT(ifnull(p.name_last,''),', ',ifnull(p.name_first,''),' ',ifnull(concat(substr(p.name_middle,1,1),'.'),'')) AS dr_name, ps.doctor_level
												FROM care_personell AS ps
												INNER JOIN care_person AS p On p.pid=ps.pid
												INNER JOIN care_personell_assignment AS a ON a.personell_nr=ps.nr
												WHERE a.location_type_nr=1 AND (ps.short_id LIKE 'D%')
												AND (a.location_nr=164 OR a.location_nr IN (158, 165, 166, 167) )
												AND (a.date_end='0000-00-00' OR a.date_end>='2008-10-19')
												AND a.status NOT IN ('deleted','hidden','inactive','void')
                                                AND ps.doctor_role LIKE '%$role%'
                                                $cond
												ORDER BY dr_name";
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		function getAllRadioDoctorsName($grp_nr) {
				global $db, $sql_LIKE, $root_path;

				$this->sql = 'SELECT a.nr,CONCAT(ifnull(name_first,"")," ",ifnull(concat(substr(name_middle,1,1),"."),"")," ",ifnull(name_last,"")) AS dr_name,
											 IF (other_title,other_title,if(job_function_title="Doctor","MD",if(job_function_title="Consulting doctor","MD,FPCR",""))) as drtitle,
											 job_function_title,job_position
											 FROM seg_radio_doctor_member AS dm
											 INNER JOIN care_personell AS a ON a.nr=dm.doctor_member
											 INNER JOIN care_person AS p ON p.pid=a.pid
											 WHERE dm.group_nr="'.$grp_nr.'"';

				#echo "sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		#added by VAN 10-24-08
		function getRequestedServices($refno,$claimstab=0) {
		global $db;

		if(empty($sort)) $sort='name';

	 #if ($claimstab)
		#				$ispaid_sql = "AND (sd.request_flag IS NOT NULL OR s.is_cash=0)";

		$this->sql="SELECT DISTINCT d.nr,
					d.name_formal,
					IF(nr='166','XRAY',d.name_short) AS name_short,
					d.name_short AS name_short2,
					sg.department_nr,
					s.is_cash, sd.request_flag AS type_charge, sd.request_flag AS charge_name,
					sd.*, ss.name, sg.group_code, sg.name AS groupnm,
					ss.is_socialized, DATE_ADD(date(s.request_date), INTERVAL 5 DAY) AS datereleased
					FROM seg_radio_serv AS s
					INNER JOIN care_test_request_radio AS sd ON s.refno = sd.refno
					LEFT JOIN seg_radio_services AS ss ON sd.service_code = ss.service_code
					LEFT JOIN seg_radio_service_groups AS sg ON ss.group_code = sg.group_code
					LEFT JOIN care_department AS d ON d.nr=sg.department_nr
					WHERE s.refno = '$refno'
					AND s.status NOT IN ($this->dead_stat)
					$ispaid_sql
					ORDER BY name_short, sg.group_code,ss.name,sg.name";


			#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
				$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			return FALSE;
		}
	}

	function getRadioServiceReqInfo($refno, $day_interval){
		global $db;

		$this->sql ="SELECT r.rid, ch.charge_name,l.*,
									DATE_ADD(date(l.request_date), INTERVAL ".$day_interval." DAY) AS datereleased
									FROM seg_radio_serv AS l
									LEFT JOIN seg_type_charge AS ch ON ch.id=l.type_charge
									LEFT JOIN seg_radio_id AS r ON r.pid=l.pid
								 WHERE l.refno='$refno'
						 AND l.status NOT IN ($this->dead_stat)";

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

		function getDayOfWeek($refno){
				 global $db;

				$this->sql ="SELECT DAYNAME(date(l.request_date)) AS day_name
												FROM seg_radio_serv AS l
												WHERE l.refno='$refno'
												 AND l.status NOT IN ($this->dead_stat)";

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

		$this->sql ="SELECT * FROM care_test_request_radio WHERE refno='$refno' LIMIT 1";
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

	function getRID($pid){
		global $db;

		$this->sql ="SELECT rid FROM seg_radio_id WHERE pid='$pid'";
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
		#---------------------
		#-----------------------

		function getRequestExamInfo($batchNr){
				global $db;

				$this->sql ="SELECT a.area_code,g.department_nr  AS dept_nr,rd.*
												FROM care_test_request_radio AS rd
												INNER JOIN seg_radio_services AS s ON s.service_code=rd.service_code
												INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code
												LEFT JOIN seg_areas AS a ON a.dept_nr=g.department_nr
												WHERE batch_nr='".$batchNr."'";

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


	#--------------------------

		/** Added by omick, February 25, 2009 **/
		 function insert_or_request($data) {
			global $db;
			extract($data);
			$this->sql = "INSERT INTO or_main_other_requests(request_refno, or_refno, encounter_nr, pid, location, insert_date)
										VALUES('$lab_refno', '$or_refno', '$encounter_nr', '$pid', $location, NOW())";

			 $this->result = $db->Execute($this->sql);
			 if ($db->Affected_Rows()) {
				 return true;
			 }
			 else {
				 return false;
			 }
		}
		/** End **/

		/** Added by omick, March 16, 2009 **/
		function remove_service_code_by_refno($refno, $service_code) {
			global $db;
			$this->sql = "UPDATE care_test_request_radio SET status='deleted' WHERE refno='$refno' AND service_code='$service_code'";
			$this->result = $db->Execute($this->sql);
			if ($db->Affected_Rows()) {
				return true;
			}
			else {
				return false;
			}
		}
		/** end **/

	//added by cha 07-13-09
	function getMainRadioDepartment()
	{
		 global $db;
		 $this->sql= "select nr, name_formal from care_department where nr IN( '164','165','166','167') order by name_formal";
		 $this->result = $db->Execute($this->sql);
		 if ($this->result=$db->Execute($this->sql))
		 {
				if ($this->count=$this->result->RecordCount())
				{
					return $this->result;
				}
				else
				{
					return FALSE;
				}
		}
		else
		{
			return FALSE;
		}
	}

		function getRadioServiceGroupsbyDept($dept_nr)
		{
			global $db;
			$cond="1";
			$sort='';
			$this->useRadioServiceGroups();
			if(empty($sort)) $sort='name';
			$this->sql="SELECT g.group_code,g.name FROM $this->tb_radio_service_groups as g
							JOIN care_department as d
							WHERE $cond
							AND g.department_nr=d.nr
							AND g.department_nr=$dept_nr
							AND g.status NOT IN ($this->dead_stat)
							ORDER BY $sort";
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
	//end cha

		#added by VAN 02-11-09
	#get the total count of every film size used
	function getStatFilmSizeByUsed($fromdate, $todate, $id_size){
		global $db;

		$this->sql="SELECT ls.service_code,ls.name,
						s.size, rs.id_size, sum(rs.no_film_used) AS no_of_film ,
												sum(rs.no_film_spoilage) AS no_film_spoilage
						FROM seg_radio_service_sized AS rs
						INNER JOIN seg_radio_film_size AS s ON s.id=rs.id_size
						INNER JOIN care_test_request_radio AS rd ON rd.batch_nr=rs.batch_nr
						INNER JOIN seg_radio_serv AS sr ON sr.refno=rd.refno
						INNER JOIN seg_radio_services AS ls ON ls.service_code=rd.service_code
						LEFT JOIN seg_service_usage AS u ON u.refno=rd.batch_nr
						WHERE u.served_date BETWEEN '".$fromdate."' AND '".$todate."'
						AND u.ref_source='RD'
						AND sr.status NOT IN('deleted','hidden','inactive','void')
						AND rd.status NOT IN('deleted','hidden','inactive','void')
						AND id_size ='".$id_size."'
						GROUP BY rs.id_size";

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


	function getPersonInfoRadio($pid, $batch_nr){
		global $db;

		$this->sql="SELECT r.*
					FROM seg_radio_serv AS r
					INNER JOIN care_test_request_radio AS d ON d.refno=r.refno
					WHERE batch_nr='".$batch_nr."' AND pid='".$pid."'";

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
	#--------------------------

		#-------------- added by VAN 06-21-09
	function getFilmStockBySize($size){
		global $db;

		$this->sql="SELECT p.bestellnum, p.artikelname, f.size_id, s.size,
					SUBSTRING(MIN(CONCAT(i.expiry_date,' ',i.qty)),1,10) AS expiry_date,
					SUBSTRING(MIN(CONCAT(i.expiry_date,qty)),11) AS qty
					FROM care_pharma_products_main AS p
					INNER JOIN seg_radio_film_item AS f ON f.item_id=p.bestellnum
					INNER JOIN seg_radio_film_size AS s ON s.id=f.size_id
					LEFT JOIN seg_inventory AS i ON i.item_code=f.item_id  AND i.qty>0
					WHERE /*p.prod_class='RS'
					AND*/ f.size_id='$size'
					GROUP BY p.bestellnum";

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
	#-----------------------------

	#added by VAN 06-20-09

	function getAllRadioInPharmaMain($keyword,$maxcount=100,$offset=0){

		#$this->sql="SELECT p.*
		#			FROM care_pharma_products_main AS p
		#			WHERE p.artikelname like '%".$keyword."%'
		#			AND p.prod_class='RS'
		#			AND p.status NOT IN ($this->dead_stat)";

		global $db;

				$this->sql = "SELECT *,
												(SELECT z.unit_name FROM seg_unit AS z WHERE c.pack_unit_id=z.unit_id) AS pack_unitname,
												(SELECT y.unit_name FROM seg_unit AS y WHERE c.pc_unit_id=y.unit_id) AS pc_unitname
												FROM seg_eod_inventory AS a
												LEFT JOIN care_pharma_products_main AS b ON a.item_code=b.bestellnum
												LEFT JOIN seg_item_extended AS c ON a.item_code=c.item_code
						WHERE b.prod_class='RS'
						AND b.artikelname like '%".$keyword."%'
						AND b.status NOT IN ($this->dead_stat)
						AND b.bestellnum NOT IN (SELECT item_id FROM seg_radio_film_item)
						AND area_code IN ('XRAY','USD','CT')
						GROUP BY a.item_code ";

		if($this->result=$db->SelectLimit($this->sql,$maxcount,$offset)){
			$this->result_count=$db->Execute($this->sql);
			if($this->rec_count=$this->result_count->RecordCount()) {
				return $this->result;
			}else{return false;}
		}else{return false;}


		}

	#------------------------

	#added by VAN 12-14-09
	function getOPDcharge(){
		global $db;

		$this->sql="SELECT * FROM seg_radio_services
								WHERE service_code='SERVCHARGE'";

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

	function getRadioInfo($batch_nr){
		global $db;

		$this->sql="SELECT r.*
								FROM seg_radio_serv AS r
								INNER JOIN care_test_request_radio AS d ON d.refno=r.refno
								WHERE d.batch_nr='".$batch_nr."'";

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

	#get only all the xray request
	function	GetPatientRadioInfo($refno){
			global $db;

		$this->sql="SELECT
									d.refno, IF(p.pid IS NULL,w.pid,p.pid) AS pid,
									IF(p.name_last IS NULL,w.name_last,p.name_last) AS name_last,
									IF(p.name_first IS NULL,w.name_first,p.name_first) AS name_first,
									IF(p.name_middle IS NULL,w.name_middle,p.name_middle) AS name_middle,
									IF(p.date_birth IS NULL,w.date_birth,p.date_birth) AS date_birth,
									IF (IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),p.age),IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),p.age) ,
									IF(fn_calculate_age(NOW(),w.date_birth),fn_get_age(NOW(),w.date_birth),w.age)) AS age,
									IF(p.sex IS NULL,UPPER(w.sex),UPPER(p.sex)) AS sex,
									UPPER(s.short_name) AS service, d.service_date AS date_performed, UPPER(fn_get_personell_name_firstINlast(d.request_doctor)) AS doctor, rid
									FROM seg_radio_serv AS r
									INNER JOIN care_test_request_radio AS d ON d.refno = r.refno
									INNER JOIN seg_radio_id AS i ON i.pid=r.pid
									INNER JOIN seg_radio_services AS s ON s.service_code=d.service_code
									INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code
									LEFT JOIN care_person AS p ON p.pid=r.pid
									LEFT JOIN seg_walkin AS w ON w.pid=r.walkin_pid
									WHERE r.refno='".$refno."' AND g.department_nr='164'";

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

	#--------------------
	#added by VAN 01-20-10
	function updateCharityRadioRequest($refno,$svc_array){
			global $db;
			#print_r($svc_array);
			foreach ($svc_array as $i=>$v) {
				$this->sql = "UPDATE care_test_request_radio SET request_flag='charity'
											WHERE refno='".$refno."'
											AND service_code=".$db->qstr($svc_array[$i][0]);
				#echo "<br>s = ".$this->sql;
				$saveok = $db->Execute($this->sql);
			}
			if (!$saveok)
				return FALSE;
			else
				return TRUE;
	}

	function validateDelete($ref_nr){
		global $db;

		$this->sql = "SELECT ref_no FROM seg_pay_request
				WHERE ref_source = 'RD' AND ref_no = '$ref_nr'
				UNION
				SELECT batch_nr AS 'refno' FROM care_test_findings_radio
				WHERE batch_nr IN (SELECT batch_nr
				FROM seg_radio_serv AS s
				INNER JOIN care_test_request_radio AS d ON d.refno=s.refno
				WHERE d.refno= '$ref_nr')";

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->rec_count=$this->result->RecordCount()){
				return $this->result;
		 }else{
				return FALSE;
		 }
		}else{
			return FALSE;
			}
	}

	#added by VAN 10-16-09
	 function getAllRequestByPid($pid,$encounter_nr){
			 global $db;

			 $this->sql="SELECT CONCAT(r.request_date,' ',r.request_time) AS serv_dt, encounter_nr, s.name AS request_item,
													fn_get_personell_name(request_doctor) AS request_doc,
													IFNULL(fn_get_encoder_name(r.create_id),r.create_id) AS encoder,
													IF(d.status='done','DONE','UNDONE') AS status, is_cash, r.refno
										FROM seg_radio_serv AS r
										INNER JOIN care_test_request_radio AS d ON d.refno=r.refno
										INNER JOIN seg_radio_services AS s ON s.service_code=d.service_code
										WHERE r.pid='$pid' AND r.encounter_nr='$encounter_nr'
										AND d.status NOT IN ($this->dead_stat)
										AND r.status NOT IN ($this->dead_stat)
										ORDER BY CONCAT(r.request_date,' ',r.request_time) DESC, encounter_nr DESC, s.name";
			#echo  $this->sql;
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
	 #-------------------

	 #added by VAN 08-05-2010
	 function getAllRadioRequested($encounter_nr, $pid){
		 global $db;

		 $this->sql = "SELECT SQL_CALC_FOUND_ROWS r.refno, d.batch_nr,fn_get_radiotest_request_code_all(d.refno) AS services,
										r.request_date, r.request_time,
										r.is_urgent, d.request_flag
										FROM seg_radio_serv AS r
										INNER JOIN care_test_request_radio AS d ON d.refno=r.refno
										WHERE r.status NOT IN ('deleted','hidden','inactive','void')
										AND d.status NOT IN ('deleted','hidden','inactive','void')
										AND (is_urgent = 1 OR request_flag IS NOT NULL OR is_cash=0)
										AND r.encounter_nr='$encounter_nr'
										AND r.pid='$pid'
										GROUP BY r.refno
										ORDER BY is_urgent DESC,refno DESC,r.request_date DESC";

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


	 #added code by angelo
	 function isBorrowerOwner($key){
			$key = utf8_decode($key);
			global $db;
			$this->skey=$key;
			if(!(is_numeric($key) and !($key=='%'||$key=='*'))){
					if(stristr(addslashes($key),',')){
						$lastnamefirst=TRUE;
					}else{
						$lastnamefirst=FALSE;
					}

					$key=strtr(addslashes($key),',',' ');
					$cbuffer=explode(' ',$key);

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
					if(sizeof($comp)>1){
						$this->strSQL="select srb.borrower_id
													from  seg_radio_borrow as srb
													inner join care_person as p on p.pid=srb.borrower_id
													where  (p.name_last LIKE '".strtr($ln,'+',' ')."%' AND p.name_first LIKE '%".strtr($fn,'+',' ')."%') ;";
						$rs=$db->Execute($this->strSQL);
						$this->ownerCount=$rs->RecordCount();
						if($this->ownerCount>0)
							return 1;
						else
							return 0;
					}
					else
						return 0;
		 }
		 else
			return 0;

	}

	//added by cha, 11-22-2010
	function updateRequestFlagPerORNumber($data, $refno)
	{
		global $db;
		extract($data);
		$db->StartTrans();
		$saveok = TRUE;
		foreach($items as $i=>$v)
		{
			$this->sql = "SELECT request_flag FROM care_test_request_radio WHERE refno=".$db->qstr($refno)." \n".
														"AND service_code=".$db->qstr($items[$i]);
			$request_flag = $db->GetOne($this->sql);
			//echo "<br/>request_flag=".$request_flag." sql=".$this->sql;
			$history = "UPDATE ".date('Y-m-d H:i:s')." ".$_SESSION['sess_temp_userid']."\n";

			#edited by VAN 03-09-2011
			//$flag = $db->qstr('paid');
			#if($request_flag!="" && $or_number[$i]=="") {
			$or_number[$i] = trim($or_number[$i]);
			if(empty($or_number[$i])) {
				$flag = 'NULL';
			}else
				$flag = $db->qstr('paid');

			$this->sql = "UPDATE care_test_request_radio SET request_flag=".$flag.", \n".
																"or_number=".$db->qstr($or_number[$i]).", history=CONCAT(history, ".$db->qstr($history)."), \n".
																"modify_dt='NOW()', modify_id=".$db->qstr($_SESSION['sess_temp_userid'])." \n".
																"WHERE service_code=".$db->qstr($items[$i])." \n".
																"AND refno=".$db->qstr($refno);
			#echo "<br/>sql2=".$this->sql;
			$saveok = $db->Execute($this->sql);
			if(!$saveok) {
				$db->FailTrans();
				$this->error_msg = $db->ErrorMsg();
				$db->CompleteTrans();
				break;
			}
		}

		if($saveok!==FALSE) {
			$db->CompleteTrans();
			return TRUE;
		}
		else return FALSE;
	}
	//end cha

	//added by VAN 07-04-2011
	function getServiceInfo($service_code){
		 global $db;

		 $this->sql="SELECT s.group_code, d.nr, d.name_formal, d.name_short
									FROM seg_radio_services s
									INNER JOIN seg_radio_service_groups g ON g.group_code=s.group_code
									INNER JOIN care_department d ON d.nr=g.department_nr
									WHERE s.service_code='".$service_code."'";
			#echo  $this->sql;
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
	#------------------
    
    #added by VAS 03-22-2012
    #will be called if there are result made
    function apply_coverage($refno, $itemsArray){ 
        global $db;
        $enc_obj=new Encounter;
        
        if (!is_array($itemsArray)) $itemsArray = array($itemsArray);
        
        $ref = $db->GetRow("SELECT encounter_nr,IF(is_cash,NULL,grant_type) AS charge_type FROM seg_radio_serv\n".
            "WHERE refno=".$db->qstr($refno));
        
        for ($i=0; $i<sizeof($itemsArray);$i++){
            $dbOk = TRUE;    
            
            $item_status = $itemsArray[$i][0];
            $item = $itemsArray[$i][5];
            
            $quantity = 1;
            # Get request item details
            #$this->sql = "SELECT price_cash*$quantity AS total,status AS serve_status FROM care_test_request_radio\n".
            #                "WHERE refno=".$db->qstr($refno)." AND service_code=".$db->qstr($item);
            $this->sql = "SELECT price_cash*$quantity AS total,IF(is_served, 'done','pending') AS serve_status FROM care_test_request_radio\n".
                            "WHERE refno=".$db->qstr($refno)." AND service_code=".$db->qstr($item);
            #echo $this->sql;        
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
                                        "AND source='R'\n".
                                        "AND item_code=".$db->qstr($item)."\n".
                                        "AND hcare_id=".__PHIC_ID__;
                        
                        $coverage = parseFloatEx($db->GetOne($this->sql)) + parseFloatEx($item_details['total']);
                        $result = $db->Replace('seg_applied_coverage',
                                                array(
                                                     'ref_no'=>"T{$ref['encounter_nr']}",
                                                     'source'=>'R',
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
                        $ref = $db->GetRow("SELECT encounter_nr,IF(is_cash,NULL,grant_type) AS charge_type FROM seg_radio_serv\n".
                                            "WHERE refno=".$db->qstr($refno));
                         
                        #check if the encounter of the request has a final bill                    
                        $hasfinal_bill = $enc_obj->hasFinalBilling($ref['encounter_nr']);
                        
                        if (!$hasfinal_bill){
                            # Handle applied coverage for PHIC and other benefits
                            $sql_app = "SELECT coverage FROM seg_applied_coverage\n".
                                            "WHERE ref_no='T{$ref['encounter_nr']}'\n".
                                            "AND source='R'\n".
                                            "AND item_code=".$db->qstr($item)."\n".
                                            "AND hcare_id=".__PHIC_ID__;
                            
                            #less the cancelled or deleted item                                                    
                            $coverage = parseFloatEx($db->GetOne($sql_app)) - parseFloatEx($item_details['total']);
                            
                            $result = $db->Replace('seg_applied_coverage',
                                                        array(
                                                                'ref_no'=>"T{$ref['encounter_nr']}",
                                                                'source'=>'R',
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
                        FROM care_test_request_radio d
                        INNER JOIN seg_radio_serv s ON s.refno=d.refno
                        WHERE s.refno=".$db->qstr($refno)."
                        AND s.grant_type='phic' AND d.status='done'";
                
        $item_details = $db->GetRow($this->sql);
        
        return $item_details;
    }
    
    function ServedRadioRequest($batch_nr, $refno, $service_code, $is_served, $date_served, $rad_tech){
        global $db, $HTTP_SESSION_VARS;
        $ret=FALSE;
        
        if ($is_served){
           $serve_label = "Served Request";
           $status = 'done'; 
        }else{
           $serve_label = "Undo Served Request";     
           $status = 'pending'; 
        }
        
        $history = $this->ConcatHistory("$serve_label ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
        #automatic apply the coverage upon saving the request
        # Handle applied coverage for PHIC and other benefits
        $clerk = $HTTP_SESSION_VARS['sess_user_name'];
        $arrayItemsList[] = array($status, $is_served, $date_served, $clerk, $date_served, $service_code);
        
        #$db->StartTrans();        
        $this->apply_coverage($refno, $arrayItemsList);
                
        
        $this->sql="UPDATE care_test_request_radio SET
                is_served='".$is_served."',
                served_date='".$date_served."',
                rad_tech='".$rad_tech."',
                history=".$history.",
                modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', 
                modify_dt=NOW()
                WHERE batch_nr = '".$batch_nr."' and refno='".$refno."'";
        #echo $this->sql;
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else{
            #$db->FailTrans();
            #$db->CompleteTrans();
            return FALSE;
        } 
        
     }

     function UpdateDoctorNr($batch_nr,$findings_nr,$SenDoc,$JunDoc,$ConDoc){
         global $db;

         $this->sql = "UPDATE care_test_findings_radio_doc_nr SET
                       sen_doctor_nr = '".$SenDoc."',
                       jun_doctor_nr = '".$JunDoc."',
                       con_doctor_nr = '".$ConDoc."'
                       WHERE batch_nr ='".$batch_nr."' AND finding_nr ='".$findings_nr."'";

         if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)return TRUE;
        else{
            return FALSE;
        }
     }

     function SaveDoctorNR($batch_nr,$findings_nr,$SenDoc,$JunDoc,$ConDoc){
         global $db;

         $this->sql = "INSERT INTO care_test_findings_radio_doc_nr (batch_nr,finding_nr,sen_doctor_nr,jun_doctor_nr,con_doctor_nr)
                       VALUES ('".$batch_nr."','".$findings_nr."','".$SenDoc."','".$JunDoc."','".$ConDoc."')";

         if($db->Execute($this->sql)){
             return TRUE;
         }else{ return FALSE; }

     }

     function getDoctorNR($batch_nr,$findings_nr){
         global $db;

         $this->sql = "SELECT * FROM care_test_findings_radio_doc_nr WHERE batch_nr ='".$batch_nr."'
                        AND finding_nr='".$findings_nr."'";

         if ($this->result=$db->Execute($this->sql)) {
                    if ($this->count=$this->result->RecordCount()) {
                            return $this->result;
                    }
                    else{return FALSE;}
         }else{return FALSE;}

     }

     function deleteDoctorNR($batch_nr,$findings_nr){
        global $db;

        $this->sql = "DELETE FROM care_test_findings_radio_doc_nr
                      WHERE batch_nr ='".$batch_nr."' AND finding_nr='".$findings_nr."'";

        if ($this->result=$db->Execute($this->sql)) {
                if ($this->count=$this->result->RecordCount()) {
                        return $this->result;
                }
                else{return FALSE;}
         }else{return FALSE;}

     }

     function hasBatchNR($batch_nr,$findings_nr){
           global $db;

         $this->sql = "SELECT * FROM care_test_findings_radio_doc_nr WHERE batch_nr ='".$batch_nr."'
                        AND finding_nr='".$findings_nr."'";

         if ($this->result=$db->Execute($this->sql)) {
                    if ($this->count=$this->result->RecordCount()) {
                            return True;
                    }
                    else{return FALSE;}
         }else{return FALSE;}
     }

#added by daryl
     function ifwalkin($refno)
     {
     	global $db;
     	$this->sql = "SELECT   SUM(  CASE    WHEN (srv.pid = sw.pid)   
     							   THEN 1    	
     							    ELSE 0     END  ) AS ifwalk 
						FROM  seg_radio_serv AS srv 
  						INNER JOIN seg_walkin AS sw  ON srv.`pid` = sw.`pid` 
						WHERE srv.refno = ".$db->qstr($refno)."";

		$ifwalk = $db->Execute($this->sql);
		return $ifwalk;
     }

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

function selectwalkin($pid)
{
	global $db;
	$pid_ = $db->qstr($pid);
	$this->sql="SELECT sw.`name_first` , sw.`name_last` , sw.`name_middle`  ,sw.`address`,sls.*
	FROM seg_walkin AS sw
	INNER JOIN seg_radio_serv AS sls ON sls.`pid` = sw.`pid` 
	WHERE sw.`pid` = $pid_ ";

	if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			return FALSE;
		}

}

#added by daryl
function selectifwalkin($pid)
{
	global $db;
	$pid_ = $db->qstr($pid);
	$this->sql="SELECT SUM(CASE WHEN (sw.`pid` = sls.`pid`) THEN 1 ELSE 0 END) AS ifwalk
				FROM seg_walkin AS sw 
				INNER JOIN seg_radio_serv AS sls ON sls.`pid` = sw.`pid`
				 WHERE sw.`pid` = $pid_ ";

	$resultwalk = $db->Execute($this->sql);
	return $resultwalk;

}


#added by daryl
	#select walkin by batch nr
function selectwalkin_batch_nr($batch_nr)
{
	global $db;
	$batch_nr = $db->qstr($batch_nr);

	$this->sql="SELECT SUM(CASE WHEN (ctrr.`refno` = srs.`refno`) THEN 1 ELSE 0 END) AS ifwalk
				FROM care_test_request_radio AS ctrr
				INNER JOIN seg_radio_serv AS srs ON srs.`refno` = ctrr.`refno`
				INNER JOIN seg_walkin AS sw ON sw.`pid` = srs.`pid`
				WHERE ctrr.`batch_nr` = $batch_nr";
	
	$resultwalk = $db->Execute($this->sql);
	return $resultwalk;
}

//added by daryl

function _searchBasicInfoRadioRefNo_walkin($key,$sub_dept_nr='',$ORDER_BY_OPTION='',$limit=FALSE,$len=30,$so=0,$mod=0){
		global $db, $sql_LIKE, $root_path;

		#added by VAN 03-24-08
		$key = utf8_decode($key);

		$key=strtr($key,'*?','%_');
		$key=trim($key);
		#$suchwort=$searchkey;
		$key = str_replace("^","'",$key);
		$key=addslashes($key);
		#edited by VAN 03-19-08
		//note $key value either refno or name, last, date_birth
		if(is_numeric($key)){
			if (strlen(trim($key))>8)
				$WHERE_SQL=" AND (r_id.rid = '".addslashes($key)."') ";
			else
				$WHERE_SQL=" AND (r_serv.pid = '".addslashes($key)."') ";
		}elseif($key=='%'||$key=='*'){
			$WHERE_SQL="";
		#added by VAN 03-18-08
		}elseif(empty($key)){
			$WHERE_SQL=" AND r_serv.request_date = DATE(NOW()) ";
		}else{
			if(stristr($key,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$key=strtr($key,',',' ');
			$cbuffer=explode(' ',$key);

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

			#commented by daryl
			// if(sizeof($comp)>1){
			// 	#$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') ";
			// 	$WHERE_SQL=" AND (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%') ";
			// }else{
			// 	$WHERE_SQL=" AND (p.name_last $sql_LIKE '".addslashes($key)."%') ";
			// }

				//added by daryl
				$WHERE_SQL=" AND (r_serv.request_date = date(NOW())) ";
			
				

		}

		include_once($root_path.'include/inc_date_format_functions.php');
		$date_format=getDateFormat();
			# Check if it is a complete date in mm/dd/yyyy format
		$this_date=@formatDate2STD($key,$date_format);
		if($this_date!='') {
            $WHERE_SQL = " AND (r_serv.request_date='$this_date') ";
		}
		if ($sub_dept_nr){
			$WHERE_SQL.=" AND r.status NOT IN ($this->dead_stat) ".
							" AND r_serv_group.department_nr='".$sub_dept_nr."'";
		}

		if ($mod)
			#$option = ' ORDER BY r_serv.is_urgent DESC,r_serv.request_date ASC,r_serv.refno ASC ';
			$option = ' ORDER BY r_serv.is_urgent DESC, r_serv.request_date DESC, r_serv.refno DESC ';
		else
			$option = $ORDER_BY_OPTION;

#		$ORDER_BY_OPTION = $add_opt;
		# added by VAN in line 3065 : r.parent_batch_nr, r.parent_refno

        /**
         * Check for request expiration
         */
        $joins = array();
        $fields = array();
        $having = array();
        if (!empty($sub_dept_nr)) {
            $row = $db->GetRow(
                "SELECT value,unit " .
                "FROM seg_request_expiration_department " .
                "WHERE module='radiology' AND department=" .
                $db->qstr($sub_dept_nr)
            );
            if (!empty($row)) {
                $having[] = "IF(is_served, 1, r_serv.request_date >= (DATE(NOW()- INTERVAL {$row['value']} {$row['unit']})))";
            }
        } else {
            // sub department is not specified (i.e., ALL)
            $fields[] = '`exp`.value';
            $fields[] = '`exp`.unit';
            $joins[] = "LEFT JOIN seg_request_expiration_department `exp` ON module='radiology' AND department=r_serv_group.department_nr";
            $having[] = "(CASE `exp`.`unit` " .
                "WHEN 'DAY' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` DAY)) " .
                "WHEN 'WEEK' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` WEEK)) " .
                "WHEN 'MONTH' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` MONTH)) " .
                "WHEN 'YEAR' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` YEAR)) " .
                "ELSE 1 " .
            "END)";
        }


				$this->sql="SELECT SQL_CALC_FOUND_ROWS r.request_flag AS charge_name,
									r_serv.refno, r_serv.encounter_nr, r_serv.pid, r_serv.ordername, r_serv.orderaddress,
									r_serv.request_date, r_serv.request_time, r_serv.is_urgent, r_serv.status AS refno_status, r_serv.create_dt,
									r.batch_nr, r.status AS request_status, r_serv.is_cash,
									r_serv_group.department_nr AS sub_dept_nr,
									dept.id AS sub_dept_id, dept.name_formal AS sub_dept_name,
									r_services.service_code, r_services.name AS service_desp,
									p.name_last, p.name_first,p.name_middle, p.date_birth, p.sex, p.pid,
									p.date_birth, 
									r_id.rid, e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type,
            r.parent_batch_nr, r.parent_refno, r.is_served " .
            ($fields ? ','. implode(",", $fields) : '') . ' ' .
        "FROM seg_radio_serv AS r_serv
								INNER JOIN seg_walkin AS p ON p.pid = r_serv.pid
								LEFT JOIN care_encounter AS e ON e.encounter_nr=r_serv.encounter_nr 
								INNER JOIN seg_radio_id AS r_id ON r_id.pid=r_serv.pid
								INNER JOIN care_test_request_radio AS r ON r.refno=r_serv.refno
								INNER JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
								INNER JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
								INNER JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
            LEFT JOIN seg_radio_schedule sched ON r.batch_nr=sched.batch_nr " .
            ($joins ? implode(' ', $joins) : '') . ' ' .
        "WHERE r_serv.status NOT IN ($this->dead_stat)
							AND r.status NOT IN ($this->dead_stat)
									$WHERE_SQL
        GROUP BY r_serv.refno ".
        ($having ? ('HAVING ('.implode(")\n AND (", $having) . ") ") : '') .
        $option;

		#WHERE r_serv.status NOT IN ('deleted','hidden','inactive','void')
#echo "class_radiology.php : _searchBasicInfoRadioRefNo : this->sql = $this->sql <br> \n ";
#exit();
		if($limit){
				$this->res['said']=$db->SelectLimit($this->sql,$len,$so);
		}else{
				$this->res['said']=$db->Execute($this->sql);
		}
		if ($this->res['said']){
			if ($this->record_count=$this->res['said']->RecordCount()) {
				$this->rec_count=$this->record_count; # workaround
#				echo " class_radiology.php :rec_count =".$this->record_count;
#				echo "class_radiology.php : _searchBasicInfoRadioPending :  TRUE <br>";
				return $this->res['said'];
			} else{
//				echo "_searchBasicInfoRadioPending : FALSE 01 <br>";
				return FALSE;
			}
		}else{
#			echo "class_radiology.php : _searchBasicInfoRadioPending : FALSE 02 <br>";
			return FALSE;
		}
	}# end of function _searchBasicInfoRadioRefNo

//added by daryl
 function SearchSelect_walkin($searchkey='',$sub_dept_nr=0,$maxcount=100,$offset=0){
	global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$suchwort=addslashes($searchkey);

		if ($searchkey == "now"){
			$whereClause = "WHERE rs.request_date = DATE(NOW())";

		}


		if(is_numeric($suchwort)) {
			#$suchwort=(int) $suchwort;
			$this->is_nr=TRUE;

			if(empty($oitem)) $oitem='refno';
			if(empty($odir)) $odir='DESC'; # default, latest pid at top

            $whereClause = "WHERE r.status NOT IN ($this->dead_stat) AND rs.status NOT IN ($this->dead_stat) AND ((r.pid= '$suchwort') /*OR (sk.batch_nr = '$suchwort')*/ OR (ri.rid = '$suchwort')) ";
		} else {
			# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$searchkey=strtr($searchkey,',',' ');
			$cbuffer=explode(' ',$searchkey);

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
                $whereClause = "WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') ";
				if(!empty($rd)){
					$DOB=@formatDate2STD($rd,$date_format);
					if($DOB=='') {
                        $whereClause .= "AND DATE(sk.scheduled_dt)='$rd' ";
					}else{
                        $whereClause .= "AND DATE(sk.scheduled_dt) = '$DOB' ";
					}
				}
                $whereClause .= "AND r.status NOT IN ($this->dead_stat) AND rs.status NOT IN ($this->dead_stat) ";
			}else{
				# Check if * or %
				if($suchwort=='%'||$suchwort=='%%'){
					#return all the data
                    $whereClause = "WHERE r.status NOT IN ($this->dead_stat) AND rs.status NOT IN ($this->dead_stat) ";
				}elseif($suchwort=='now'){
                    $whereClause = "WHERE (sk.scheduled_dt=DATE(NOW()) || (ISNULL(sk.scheduled_dt) AND (rs.request_date=DATE(NOW())))) " .
                        "AND r.status NOT IN ($this->dead_stat) AND rs.status NOT IN ($this->dead_stat) ";
				}else{
					# Check if it is a complete DOB
					$DOB=@formatDate2STD($suchwort,$date_format);
					if($DOB=='') {
						if(TRUE){
							if($fname){
                                $whereClause = "WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR r.ordername $sql_LIKE '%".strtr($suchwort,'+',' ')."%' " .
                                    "OR sk.modify_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR sk.create_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%') ";
							}else{
                                $whereClause="WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR r.ordername $sql_LIKE '%".strtr($suchwort,'+',' ')."%' " .
                                    "OR sk.modify_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR sk.create_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%') ";
							}
						}else{
                            $whereClause="WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR r.ordername $sql_LIKE '%".strtr($suchwort,'+',' ')."%' " .
                                "OR sk.modify_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%' OR sk.create_id $sql_LIKE '%".strtr($suchwort,'+',' ')."%')";
						}
					}else{
                        //$whereClause = "WHERE (sk.scheduled_dt = '$DOB'  OR r.request_date = '$DOB') ";
                        $whereClause = "WHERE (r.request_date = '$DOB') ";
					}
                    $whereClause .= "AND r.status NOT IN ($this->dead_stat) ";
				}
			}
		 }

		 $cond_final = "";
		if ($sked) {
			$cond_final = " AND (b.is_final = 0 OR b.is_final IS NULL)";
		}

        if ($sub_dept_nr)
            $cond_dept = " AND g.department_nr = '".$sub_dept_nr."' ";
		 
         /**
         * Check for request expiration
         */
        $joins = array();
        $fields = array();
        $having = array();
        if (!empty($sub_dept_nr)) {
            $row = $db->GetRow(
                "SELECT value,unit " .
                "FROM seg_request_expiration_department " .
                "WHERE module='radiology' AND department=" .
                $db->qstr($sub_dept_nr)
            );
            if (!empty($row)) {
                $having[] = "IF(is_served, 1, r.request_date >= (DATE(NOW()- INTERVAL {$row['value']} {$row['unit']})))";
            }
        } else {
            // sub department is not specified (i.e., ALL)
            $fields[] = '`exp`.value';
            $fields[] = '`exp`.unit';
            $joins[] = "LEFT JOIN seg_request_expiration_department `exp` ON module='radiology' AND department=g.department_nr";
            $having[] = "(CASE `exp`.`unit` " .
                "WHEN 'DAY' THEN IF(is_served, 1, r.request_date >= DATE(NOW()-INTERVAL `exp`.`value` DAY)) " .
                "WHEN 'WEEK' THEN IF(is_served, 1, r.request_date >= DATE(NOW()-INTERVAL `exp`.`value` WEEK)) " .
                "WHEN 'MONTH' THEN IF(is_served, 1, r.request_date >= DATE(NOW()-INTERVAL `exp`.`value` MONTH)) " .
                "WHEN 'YEAR' THEN IF(is_served, 1, r.request_date >= DATE(NOW()-INTERVAL `exp`.`value` YEAR)) " .
                "ELSE 1 " .
            "END)";
        }


        $orderClause = "ORDER BY r.is_urgent DESC,r.request_date DESC, r.request_time DESC,sk.scheduled_dt ASC, p.name_last ASC, p.name_first ASC, rs.batch_nr ASC ";

        #added by VAN 06-03-2013
        #add field e.is_maygohome, r.encounter_nr, 
		$this->sql= "SELECT DISTINCT SQL_CALC_FOUND_ROWS e.is_maygohome, r.encounter_nr, r.is_cash, r.request_date, r.request_time, rs.parent_refno,rs.parent_batch_nr,IF ((rs.request_flag IS NOT NULL),1,0) AS hasPaid, " .
                "rs.batch_nr AS refnum,rs.refno AS batchnum, rs.modify_id AS encoder, rs.create_id AS encoder2, sk.*, sk.status AS skstatus, ri.rid, rs.service_code, s.name AS serv_name, " .
                "s.group_code, d.id, d.name_formal, d.name_short AS dept_short_name, " .
                "r.pid, r.refno, p.name_first, e.encounter_type, " .
                "p.name_middle, p.name_last, r.ordername, rs.is_served, rs.served_date " .
                ($fields ? ','. implode(",", $fields) : '') . ' ' .
            "FROM {$this->tb_test_request_radio} AS rs " .
                "LEFT JOIN seg_radio_schedule AS sk ON rs.batch_nr=sk.batch_nr " .
                "INNER JOIN seg_radio_serv AS r ON r.refno=rs.refno " .
                "INNER JOIN seg_radio_services AS s ON s.service_code=rs.service_code " .
                "INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code " .
                "INNER JOIN care_department AS d ON g.department_nr=d.nr " .
                "LEFT JOIN care_encounter AS e ON e.encounter_nr=r.encounter_nr " .
                "LEFT JOIN seg_billing_encounter AS b ON b.encounter_nr=e.encounter_nr " .
                "INNER JOIN seg_radio_id AS ri ON r.pid=ri.pid " .
                "INNER JOIN seg_walkin AS p ON p.pid=r.pid ".
                ($joins ? implode(' ', $joins) : '') . ' ' .
            $whereClause . $cond_dept .
            ($having ? ('HAVING ('.implode(")\n AND (", $having) . ") ") : '') .
            $orderClause;

                if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
                    if($this->rec_count=$this->res['ssl']->RecordCount()) {
                        return $this->res['ssl'];
                    }else{return false;}
                }else{return false;}
	}
	#------------------------------------------

#edited by daryl
	function searchBasicInfoRadioPending_walkin($search_type='',$key,$sub_dept_nr='',$ORDER_BY_OPTION='',$limit=FALSE,$len=30,$so=0){
		global $db, $sql_LIKE, $root_path;
		#added by VAN 03-24-08
		$key = utf8_decode($key);

		if(is_numeric($key)){
			#$key=(int)$key;
			$WHERE_SQL=" AND (r_serv.pid = '".addslashes($key)."' OR sri.rid = '".addslashes($key)."') ";
		}elseif($key=='%'||$key=='*'){
			$WHERE_SQL="";

		}else{
			#echo "key = ".$key;
			if ($key){
				if(stristr($key,',')){
					$lastnamefirst=TRUE;
				}else{
					$lastnamefirst=FALSE;
				}

				#$searchkey=strtr($key,',',' ');
				$cbuffer=explode(',',$key);

				# Remove empty variables
				for($x=0;$x<sizeof($cbuffer);$x++){
					$cbuffer[$x]=trim($cbuffer[$x]);
					if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
				}

				# Arrange the values, ln= lastname, fn=first name, bd = birthday
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
					$WHERE_SQL=" AND ((p.name_last $sql_LIKE '$ln%' AND p.name_first $sql_LIKE '$fn%') )";
				}#else
				// 	$WHERE_SQL=" AND (p.name_last $sql_LIKE '".addslashes($key)."%') ";
			}else
				$WHERE_SQL="";

			if ($key == 'null'){
				#echo "here";
				$sql_now = " AND r_serv.request_date = DATE(NOW()) ";
			}else{
				$sql_now = "";
			}
		}

		include_once($root_path.'include/inc_date_format_functions.php');
		$date_format=getDateFormat();
			# Check if it is a complete date in mm/dd/yyyy format
		$this_date=@formatDate2STD($key,$date_format);
		if($this_date!='') {
            $WHERE_SQL=" AND (r_serv.request_date='$this_date') ";
		}
		if ($sub_dept_nr){
			$WHERE_SQL.=" AND r_serv_group.department_nr='".$sub_dept_nr."'";
		}
		if ($search_type=='unscheduled'){
			$WHERE_SQL.=" AND r.batch_nr NOT IN (SELECT batch_nr FROM seg_radio_schedule)";
		}

		#added by VAN 07-10-08
		#$with_film = " AND (SELECT sz.batch_nr FROM seg_radio_service_sized AS sz WHERE sz.batch_nr=r.batch_nr LIMIT 1)!='' ";
		if ($search_type=='unscheduled')
			$with_film = " AND NOT EXISTS (SELECT sz.batch_nr FROM seg_radio_service_sized AS sz WHERE sz.batch_nr=r.batch_nr) ";
		else
			$with_film = " AND EXISTS (SELECT sz.batch_nr FROM seg_radio_service_sized AS sz WHERE sz.batch_nr=r.batch_nr) ";
		#$with_film = "";
		#added by VAN 07-07-08
		#if (empty($ORDER_BY_OPTION))
		$ORDER_BY_OPTION = " ORDER BY r.is_served desc, r_serv.request_date DESC, p.name_last ASC, p.name_first ASC ";


        /**
         * Check for request expiration
         */
        $joins = array();
        $fields = array();
        $having = array();
        if (!empty($sub_dept_nr)) {
            $row = $db->GetRow(
                "SELECT value,unit " .
                "FROM seg_request_expiration_department " .
                "WHERE module='radiology' AND department=" .
                $db->qstr($sub_dept_nr)
            );
            if (!empty($row)) {
                $having[] = "IF(is_served, 1, r_serv.request_date >= (DATE(NOW()- INTERVAL {$row['value']} {$row['unit']})))";
            }
        } else {
            // sub department is not specified (i.e., ALL)
            $fields[] = '`exp`.value';
            $fields[] = '`exp`.unit';
            $joins[] = "LEFT JOIN seg_request_expiration_department `exp` ON module='radiology' AND department=r_serv_group.department_nr";
            $having[] = "(CASE `exp`.`unit` " .
                "WHEN 'DAY' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` DAY)) " .
                "WHEN 'WEEK' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` WEEK)) " .
                "WHEN 'MONTH' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` MONTH)) " .
                "WHEN 'YEAR' THEN IF(is_served, 1, r_serv.request_date >= DATE(NOW()-INTERVAL `exp`.`value` YEAR)) " .
                "ELSE 1 " .
            "END)";
        }

        $this->sql="SELECT " .
                "(SELECT sz.batch_nr FROM seg_radio_service_sized AS sz WHERE sz.batch_nr=r.batch_nr LIMIT 1) AS film_size, " .
                "sri.rid, e.encounter_type, " .
                "IF ((request_flag IS NOT NULL),1,0) AS hasPaid, r.request_flag, " .
                "r.batch_nr, r.create_dt, " .
                "r_serv.refno, r_serv.request_date, r_serv.encounter_nr, r_serv.pid, r_serv.ordername, " .
                "r_serv.orderaddress, r_serv.is_cash, r_serv.is_urgent, r_serv.comments, " .
                "r_serv.history, r.clinical_info, r.service_code, " .
                "r.price_cash, r.price_charge, r.service_date, r.is_in_house, r.request_doctor, r.status,r.remarks, " .
                "IF((ISNULL(r.is_in_house) || r.is_in_house='0'), " .
                    "r.request_doctor, " .
                    "IF(STRCMP(r.request_doctor,CAST(r.request_doctor AS UNSIGNED INTEGER)), " .
                        "r.request_doctor, fn_get_personell_name(r.request_doctor)) ) AS request_doctor_name, " .
                "r_services.name, r_serv_group.group_code , " .
                "r_serv_group.department_nr AS sub_dept_nr,dept.name_short, dept.id AS sub_dept_id, dept.name_formal AS sub_dept_name, " .
                "p.name_last, p.name_first, p.date_birth, r.is_served " .
                ($fields ? ','. implode(",", $fields) : '') . ' ' .
            "FROM seg_radio_serv AS r_serv " .
                "INNER JOIN care_test_request_radio AS r ON r.refno=r_serv.refno " .
                "LEFT JOIN care_encounter AS e ON e.encounter_nr=r_serv.encounter_nr " .
                "INNER JOIN seg_walkin AS p ON p.pid = r_serv.pid " .
                "INNER JOIN seg_radio_id AS sri ON sri.pid=r_serv.pid " .
                "INNER JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code " .
                "INNER JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code " .
                "INNER JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr " .
                "LEFT JOIN seg_radio_service_sized AS sz ON sz.batch_nr=r.batch_nr " .
                ($joins ? implode(' ', $joins) : '') . ' ' .
            "WHERE r_serv.status NOT IN ($this->dead_stat) " .
                "AND r.status NOT IN ($this->dead_stat,'done') " .
                "AND (request_flag IS NOT NULL OR r_serv.is_urgent=1 OR r_serv.is_cash=0 OR (r.parent_batch_nr!='' AND r.parent_refno)) " .
            "{$WHERE_SQL} " .
						/*$with_film*/
            "{$sql_now} " .
            "GROUP BY r.batch_nr " .
            ($having ? ('HAVING ('.implode(")\n AND (", $having) . ") ") : '') .
            "{$ORDER_BY_OPTION} ";

		if($limit){
				$this->res['sabi']=$db->SelectLimit($this->sql,$len,$so);
		}else{
				$this->res['sabi']=$db->Execute($this->sql);
		}

		if ($this->res['sabi']){
			if ($this->record_count=$this->res['sabi']->RecordCount()) {
				$this->rec_count=$this->record_count; # workaround
#				echo "class_radiology.php : _searchBasicInfoRadioPending :  TRUE <br>";
				return $this->res['sabi'];
			} else{
#				echo "_searchBasicInfoRadioPending : FALSE 01 <br>";
				return FALSE;
			}
		}else{
#			echo "class_radiology.php : _searchBasicInfoRadioPending : FALSE 02 <br>";
			return FALSE;
		}
	}# end of function _searchBasicInfoRadioPending

#added by daryl
	function getScheduledRadioRequestInfo2_walk($batch_nr='',$limit=FALSE, $sub_dept_nr='', $date=''){
		global $db,$root_path;

		$OPTIONS_WHERE_SQL='';
		if ($limit){
				# needs ONLY ONE record/row to return
			if(empty($batch_nr) || (!$batch_nr)){
				return FALSE;
			}
			#$OPTIONS_WHERE_SQL .= "AND sr_sked.batch_nr='$batch_nr' ";
			$OPTIONS_WHERE_SQL .= "AND r.batch_nr='$batch_nr' ";
		}
		if ($date){
			include_once($root_path.'include/inc_date_format_functions.php');
			$date_format=getDateFormat();
				# Check if it is a complete date in mm/dd/yyyy format
			$this_date=@formatDate2STD($date,$date_format);
			if($this_date!='') {
				$OPTIONS_WHERE_SQL .= " AND DATE(sr_sked.scheduled_dt)='$this_date'";
			}
		}
		if ($sub_dept_nr){
				$OPTIONS_WHERE_SQL .= " AND r_serv_group.department_nr ='$sub_dept_nr'";
		}

		#edited by VAN 07-08-08
		$this->sql="SELECT sr_sked.schedule_no, sri.rid, IF ((request_flag IS NOT NULL),1,0) AS hasPaid,
							r.batch_nr, IFNULL(sr_sked.scheduled_dt,DATE(NOW())) AS scheduled_dt,
							IFNULL(sr_sked.scheduled_time,TIME(NOW())) AS scheduled_time,
							sr_sked.instructions, sr_sked.remarks,
							sr_sked.status, sr_sked.modify_id, sr_sked.create_id,
							r.service_code, r.service_date, r_services.name AS service_name,
							r_serv_group.department_nr AS sub_dept_nr, dept.name_short AS dept_name_short, dept.name_formal AS dept_name,
							IF(ISNULL(r_serv.pid),
								r_serv.ordername, fn_get_pid_name(r_serv.pid)) AS patient_name,
							cp.name_first, cp.name_middle, cp.name_last
							FROM care_test_request_radio AS r
							LEFT JOIN seg_radio_schedule AS sr_sked ON r.batch_nr=sr_sked.batch_nr
							INNER JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
							INNER JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
							INNER JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
							INNER JOIN seg_radio_serv AS r_serv ON r.refno=r_serv.refno
							INNER JOIN seg_radio_id AS sri ON sri.pid = r_serv.pid
							INNER JOIN seg_walkin AS cp ON cp.pid=r_serv.pid
						WHERE r_serv.status NOT IN ($this->dead_stat)
							AND r.status NOT IN ($this->dead_stat)
							AND (request_flag IS NOT NULL OR r_serv.is_urgent=1 OR r_serv.is_cash=0)
							$OPTIONS_WHERE_SQL
							ORDER BY sr_sked.scheduled_dt ASC";

		if ($buf=$db->Execute($this->sql)){
			if($this->count=$buf->RecordCount()) {
				if ($limit)
					return $buf->FetchRow();
				else
					return $buf;
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getScheduledRadioRequestInfo

#added by daryl
	function searchBasicInfoRadioDone_walk($key,$sub_dept_nr='',$ORDER_BY_OPTION='',$limit=FALSE,$len=30,$so=0, $is_doctor=0, $encounter_nr='', $cond=''){
		global $db, $sql_LIKE, $root_path;
		#added by VAN 03-24-08
		$key = utf8_decode($key);

			if ($key == 'null'){
				#echo "here";
				$sql_now = " AND r_serv.request_date = DATE(NOW()) ";
			}else{
				$sql_now = "";
			}

		if(is_numeric($key)){
			#$key=(int)$key;
			$WHERE_SQL=" AND (r_serv.pid = '".addslashes($key)."' OR sri.rid = '".addslashes($key)."') ";
		}elseif($key=='%'||$key=='*'){
			$WHERE_SQL="";
		}else{

			if(stristr($key,',')){
					$lastnamefirst=TRUE;
				}else{
					$lastnamefirst=FALSE;
				}

				#$searchkey=strtr($key,',',' ');
				$cbuffer=explode(',',$key);

				# Remove empty variables
				for($x=0;$x<sizeof($cbuffer);$x++){
					$cbuffer[$x]=trim($cbuffer[$x]);
					if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
				}

				# Arrange the values, ln= lastname, fn=first name, bd = birthday
				if($lastnamefirst){
					$fn=$comp[1];
					$ln=$comp[0];
					$rd=$comp[2];
				}else{
					$fn=$comp[0];
					$ln=$comp[1];
					$rd=$comp[2];
				}
				#commented by daryl
			# Check the size of the comp
				if(sizeof($comp)>1){
					$WHERE_SQL=" AND ((p.name_last $sql_LIKE '$ln%' AND p.name_first $sql_LIKE '$fn%') )";
				}#else
				// 	$WHERE_SQL=" AND (p.name_last $sql_LIKE '".addslashes($key)."%') ";
		}

		include_once($root_path.'include/inc_date_format_functions.php');
		$date_format=getDateFormat();
			# Check if it is a complete date in mm/dd/yyyy format
		$this_date=@formatDate2STD($key,$date_format);
		if($this_date!='') {
			$WHERE_SQL=" AND (DATE(r_serv.request_date)='$this_date') ";
		}
		if ($sub_dept_nr){
			$WHERE_SQL.=" AND r_serv_group.department_nr='".$sub_dept_nr."'";
		}

		$ORDER_BY_OPTION = " ORDER BY r_serv.request_date DESC, p.name_last ASC, p.name_first ASC ";

		if ($is_doctor)
			$is_dr_con = " AND r_serv.encounter_nr='".$encounter_nr."' ";
		else
			$is_dr_con = "";

		if ($cond)
			$is_dr_con = $cond;
							 $this->sql="SELECT sri.rid, e.encounter_type,
														e.current_ward_nr,e.current_room_nr,e.current_dept_nr,
														r.batch_nr, r.create_dt,
														r_serv.refno, r_serv.request_date, r_serv.encounter_nr, r_serv.pid, r_serv.ordername,
														r_serv.orderaddress, r_serv.is_cash, r_serv.is_urgent, r_serv.comments,
														r_serv.history, r.clinical_info, r.service_code,
														r.price_cash, r.price_charge, r.service_date, r.is_in_house, r.request_doctor, r.status,r.remarks,
														IF((ISNULL(r.is_in_house) || r.is_in_house='0'),
																r.request_doctor,
																IF(STRCMP(r.request_doctor,CAST(r.request_doctor AS UNSIGNED INTEGER)),
																		r.request_doctor, fn_get_personell_name(r.request_doctor)) ) AS request_doctor_name,
														r_services.service_code, r_services.name, r_serv_group.group_code ,
														r_serv_group.department_nr AS sub_dept_nr, dept.id AS sub_dept_id, dept.name_formal AS sub_dept_name,
														dept.name_short AS dept_short_name,
														p.name_last, p.name_first, p.date_birth
												FROM seg_radio_serv AS r_serv
														INNER JOIN care_test_request_radio AS r ON r.refno=r_serv.refno
														INNER JOIN seg_walkin AS p ON p.pid = r_serv.pid
														INNER JOIN seg_radio_id AS sri ON sri.pid=r_serv.pid
																INNER JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
																		INNER JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
																				INNER JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
																				LEFT JOIN care_encounter AS e ON e.encounter_nr=r_serv.encounter_nr
												WHERE r_serv.status NOT IN ($this->dead_stat)
														AND r.status='done'
														$is_dr_con
												$WHERE_SQL
												$sql_now
												$ORDER_BY_OPTION ";

		#added by VAN 07-31-08
		#just get the total # of records
		if ($is_doctor){
			$this->res['sabi']=$db->Execute($this->sql);
			$this->record_tcount=$this->res['sabi']->RecordCount();
		}

		if($limit){
				$this->res['sabi']=$db->SelectLimit($this->sql,$len,$so);
		}else{
				$this->res['sabi']=$db->Execute($this->sql);
		}

		if ($this->res['sabi']){
			if ($this->record_count=$this->res['sabi']->RecordCount()) {
				$this->rec_count=$this->record_count; # workaround
#				echo "class_radiology.php : _searchBasicInfoRadioDone :  TRUE <br>";
				return $this->res['sabi'];
			} else{
#				echo "_searchBasicInfoRadioDone : FALSE 01 <br>";
				return FALSE;
			}
		}else{
#			echo "class_radiology.php : _searchBasicInfoRadioDone : FALSE 02 <br>";
			return FALSE;
		}
	}# end of function _searchBasicInfoRadioDone

	//added by ken 3/28/2014
	function getRoomType($enc){
		global $db;

		$enc = $db->qstr($enc);

		$this->sql = "SELECT cr.`type_nr` FROM care_encounter ce
						LEFT JOIN care_room cr ON ce.`current_ward_nr` = cr.`ward_nr` 
						AND ce.`current_room_nr` = cr.`room_nr`
						WHERE encounter_nr = ".$enc;
		$this->result = $db->Execute($this->sql);
		if($this->result)
			while($row = $this->result->FetchRow())
				return $row['type_nr'];
		else
			return false;
	}//end of function getRoomType

	function getServiceRates($room, $code, $source, $is_cash){
		global $db;

		$code = $db->qstr($code);
		$where = "room_type = '$room' AND source = '$source' AND service_code = ".$code;

		$this->sql = "SELECT IF($is_cash, cash_price, charge_price) AS net_price, cash_price, charge_price
						FROM seg_other_rates
						WHERE $where";

		$result = $db->GetRow($this->sql);
		
		if($result)
			return $result;
		else
			return false; 
	}//end of function getServiceRates
	//ended by ken 3/28/2014
	//added by ken 5/8/2014
	function getServiceGroupCode($code){
		global $db;

		$code = $db->qstr($code);

		$this->sql = "SELECT group_code FROM seg_radio_services
						WHERE service_code =".$code;

		$result = $db->GetRow($this->sql);

		if($result)
			return $result;
		else
			return false; 
	}//end of function getServiceGroupCode
	//ended by ken 5/8/2014

	//added by maimai 11-21-2014
	function getTotalChargedServ($encounter_nr){
		global $db;
	
		$this->sql = "SELECT 
						  IFNULL(SUM(d.price_charge),0) AS total_charge 
						FROM
						  care_test_request_radio d 
						  LEFT JOIN seg_radio_serv h 
						    ON h.`refno` = d.`refno` 
						WHERE h.`encounter_nr` = ".$db->qstr($encounter_nr) 
						 ." AND d.`is_served` = 1 
						  AND h.`is_cash` = 0 ";
		if($this->result = $db->Execute($this->sql)){
			return $this->result;
		}

		return false;
	}

	function getDrPatients($dr_nr, $from, $to, $encounter_type='', $dept_nr=''){
		global $db;

		$where = "";
		$cond = array();

		if($encounter_type){
			switch($encounter_type){
				case "1":
					$cond[] = " ce.encounter_type = '1' ";
				break;
				case "2":
					$cond[] = " ce.encounter_type = '3' OR ce.encounter_type ='4' ";
				break;
				case "3":
					$cond[] = " ce.encounter_type = '2' ";
				break;
				case "4":
					$cond[] = " s.encounter_nr IS NULL ";
				break;
				case "5":
					$cond[] = " (s.encounter_nr IS NULL OR ce.encounter_type='2') ";
				break;
				case "6":
					$cond[] = " ce.encounter_type <> '2' ";
				break;

				default: 
					$cond[] = " s.encounter_nr='1' OR s.encounter_nr='2' s.encounter_nr='3' ";
				break;
			}

			if ($encounter_type != '4' || $encounter_type != '5'){
				$cond[] = " DATE(s.`request_date`) BETWEEN DATE(".$db->qstr($from).") 
						  AND DATE(".$db->qstr($to).")";
			} else{
				$cond[] = " DATE(r.`served_date`) BETWEEN DATE(".$db->qstr($from).") 
						  AND DATE(".$db->qstr($to).")";
			}
		}

		if($dept_nr!='all'){
			$cond[] = " g.department_nr = ".$db->qstr($dept_nr);
		}

		$where=implode("AND", $cond);
		if($where) $where=" AND ".$where;
		// var_dump($where);die;
		if($dr_nr!='0'){
			$doc = " CONCAT(d.`con_doctor_nr`,',', d.`sen_doctor_nr`,',', d.`jun_doctor_nr`) LIKE '%".$dr_nr."%' AND ";
		}
		else
			$doc = '';

		$this->sql = "SELECT 
						  DATE(r.`served_date`) served_date,
						  s.`request_date`,
						  s.`ordername`,
						  v.`name` AS service_name,
						  r.`price_alter`,
						  IF(
						    r.`request_flag` IS NULL,
						    'Personal',
						    IF(
						      r.`request_flag` <> 'paid',
						      (SELECT 
						        firm_id 
						      FROM
						        care_insurance_firm 
						      WHERE hcare_id = r.`request_flag` LIMIT 1),
						      (SELECT 
						        or_no 
						      FROM
						        seg_pay_request 
						      WHERE ref_source = 'RD' 
						        AND ref_no = r.`refno` 
						        AND service_code = r.`service_code` LIMIT 1)
						    )
						  ) or_no 
						FROM
						  care_test_findings_radio_doc_nr d 
						  RIGHT JOIN care_test_request_radio r 
						    ON r.`batch_nr` = d.`batch_nr` 
						  LEFT JOIN seg_radio_serv s 
						    ON s.`refno` = r.`refno` 
						  RIGHT JOIN seg_radio_services v 
						    ON v.`service_code` = r.`service_code` 
						  LEFT JOIN care_encounter ce 
    						ON ce.`encounter_nr` = s.`encounter_nr` 
    					  LEFT JOIN seg_radio_service_groups g 
   							ON g.`group_code` = v.`group_code` 
						WHERE 
						  $doc
						  r.`status` = 'done'
						  AND DATE(r.`served_date`) BETWEEN DATE(".$db->qstr($from).") 
						  AND DATE(".$db->qstr($to).")
						  $where
						ORDER BY r.`served_date`,
						  s.`ordername`, v.`name` ASC";
						  // echo $this->sql; exit();
		if($this->result=$db->Execute($this->sql)){
			return $this->result;
		}

		return false;
	}
	//end added by maimai

	function getRadPatients($rad_nr='', $doctor_nr='', $from, $to, $dept_nr=''){
		global $db;

		$where = "";
		
		if($rad_nr!='all'){
			$where .= " r.`rad_tech` = '".$rad_nr."' AND ";
		}

		if($doctor_nr!='0'){
			$where .= " CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$doctor_nr."%' AND ";
		}

		if($dept_nr!='all'){
			$where .= " g.department_nr = ".$db->qstr($dept_nr). " AND ";
		}



		$this->sql = "SELECT 
						  DATE(r.`served_date`) AS 'served_date',
						  s.`ordername` AS 'order_name',
						  v.`name` AS 'service_name',
						  IF(
						    r.`request_flag` IS NULL,
						    'Personal',
						    IF(
						      r.`request_flag` <> 'paid',
						      (SELECT 
						        firm_id 
						      FROM
						        care_insurance_firm 
						      WHERE hcare_id = r.`request_flag`),
						      (SELECT 
						        or_no 
						      FROM
						        seg_pay_request 
						      WHERE ref_source = 'RD' 
						        AND ref_no = r.`refno` 
						        AND service_code = r.`service_code`)
						    )
						  ) AS 'or_no',
						  0 AS 'cs',
						  r.`price_alter` AS 'amount',
						  '' AS 'stat_pf',
						  '' AS 'vat'
						FROM
						  care_test_request_radio r 
						  LEFT JOIN seg_radio_serv s 
						    ON s.`refno` = r.`refno` 
						  LEFT JOIN seg_radio_services v 
						    ON v.`service_code` = r.`service_code` 
						  LEFT JOIN care_encounter ce 
						    ON ce.`encounter_nr` = s.`encounter_nr` 
						  LEFT JOIN seg_radio_service_groups g 
						    ON g.`group_code` = v.`group_code`
						  LEFT JOIN care_test_findings_radio_doc_nr dr 
						  	ON dr.`batch_nr`=r.`batch_nr` 
						WHERE $where 
						  r.`status` = 'done'
						  AND DATE(r.`served_date`) BETWEEN ('".$from."') AND ('".$to."')
						ORDER BY served_date,
						  s.`ordername`,
						  v.`name` ASC ";
						  //echo $this->sql; exit();
		if($this->result=$db->Execute($this->sql)){
			return $this->result;
		}

		return false;
	}
} # end class SegRadio
?>