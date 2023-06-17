<?php
/*
 * @package care_api
 * Class for updating  `seg_ops_serv`, `seg_ops_servdetails`, and `seg_ops_personell` tables
 *    Retrieves data from `care_encounter_op` table.
 * Created: October 1, 2007 (Bernard Klinch S. Clarito II)
 * Modified: August 26, 2008 (LST)
 */
require('./roots.php');
require_once($root_path."include/care_api_classes/class_hospital_admin.php");
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/billing/class_billing.php');
require_once($root_path.'include/care_api_classes/billing/class_bill_info.php');

define('HOUSE_CASE_PCF', 40);

class SegOps extends Core{


		/**
		* Database table for the requested operation.
		*    - includes refno, encounter
		* @var string
		*/
		var $tb_ops_serv='seg_ops_serv';

		/*
		 * Database table for the details of the operation.
		 *    - includes ops_code, rvu, multiplier
		 * @var string
		 */
		var $tb_ops_servdetails = 'seg_ops_servdetails';

		/*
		 * Database table for the personnel involve in a paticular operation.
		 *    - includes surgeons, assistant surgeons, scrub nurses, rotating nurses
		 * @var string
		 */
		var $tb_ops_personell = 'seg_ops_personell';

		/**
		* Database table for the operation requests.
		* @var string
		*/
		var $tb_encounter_op='care_encounter_op';
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

		var $fld_ops_serv=array(
				"refno",
				"nr",
				"request_date",
				"request_time",
				"encounter_nr",
				"pid",
				"is_cash",
				"is_urgent",
				"ordername",
				"orderaddress",
				"hasPaid",
				"comments",
				"status",
				"history",
				"modify_id",
				"modify_dt",
				"create_id",
				"create_dt"
		);

		var $fld_ops_servdetails=array(
				"refno",
				"ops_code",
				"rvu",
				"multiplier"
		);

		var $fld_ops_personell=array(
				"refno",
				"dr_nr",
				"role_entry_no",
				"role_type_nr",
				"ops_code",
				"ops_charge"
		);

		var $fld_encounter_op=array(
				"nr",
				"refno",
				"year",
				"dept_nr",
				"op_room",
				"op_nr",
				"op_date",
				"op_time",
				"op_src_date",
				"encounter_nr",
				"diagnosis",
				"operator",
				"assistant",
				"scrub_nurse",
				"rotating_nurse",
				"anesthesia",
				"an_doctor",
				"op_therapy",
				"result_info",
				"entry_time",
				"cut_time",
				"close_time",
				"exit_time",
				"entry_out",
				"cut_close",
				"wait_time",
				"bandage_time",
				"repos_time",
				"encoding",
				"doc_date",
				"doc_time",
				"duty_type",
				"material_codedlist",
				"container_codedlist",
				"icd_code",
				"ops_code",
				"ops_intern_code",
				"status",
				"history",
				"modify_id",
				"modify_time",
				"create_id",
				"create_time"
		);

		var $refCode;
				/*
				 * Constructor
				 * @param string primary key refCode
				 */
		function SegOps($refCode=''){
				if(!empty($refCode)) $this->refCode=$refCode;
		}
				/*
				 * Sets the core object point to 'seg_ops_serv' and corresponding field names.
				 * @access private
				 */
		function _useOpsServ(){
				$this->coretable= $this->tb_ops_serv;
				$this->ref_array= $this->fld_ops_serv;
		}

				/*
				 * Sets the core object point to 'se_ops_servdetails' and corresponding field names.
				 * @access private
				 */
		function _useOpsServDetails(){
				$this->coretable= $this->tb_ops_servdetails;
				$this->ref_array= $this->fld_ops_servdetails;
		}

				/*
				 * Sets the core object point to 'seg_ops_personell' and corresponding field names.
				 * @access private
				 */
		function _useOpsPersonell(){
				$this->coretable= $this->tb_ops_personell;
				$this->ref_array= $this->fld_ops_personell;
		}

				/*
				 * Sets the core object point to 'care_encounter_op' and corresponding field names.
				 * @access private
				 */
		function _useEncounterOp(){
				$this->coretable= $this->tb_encounter_op;
				$this->ref_array= $this->fld_encounter_op;
		}

				/**
				* Gets a new TEMPORARY patient number (pid).
				*
				* A reference number must be passed as parameter. The returned number will the highest number above the reference number PLUS 1.
				* @param int Reference PID number
				* @return integer
				*    burn added: August 29, 2007
				*/
		function getNewRefNo($ref_nr){
				global $db;

				$temp_ref_nr = date('Y')."%";   # NOTE : XXXX?????? would be the format of Reference number
				$row=array();
				$this->sql="SELECT refno FROM $this->tb_ops_serv WHERE refno LIKE '$temp_ref_nr' ORDER BY refno DESC";
				if($this->res['gnpn']=$db->SelectLimit($this->sql,1)){
						if($this->res['gnpn']->RecordCount()){
								$row=$this->res['gnpn']->FetchRow();
								return $row['refno']+1;
						}else{/*echo $this->sql.'no xount';*/return $ref_nr;}
				}else{/*echo $this->sql.'no sql';*/return $ref_nr;}
		}

				/**
				* Checks if an encounter OP nr [from `care_encounter_op` table]
				*         has an entry in `seg_ops_serv` table based on request ENCOUNTER OP NUMBER [nr]
				* @access public
				* @param int encounter op number
				* @return refno OR boolean
				* @created : burn, October 3, 2007
				*/
		function encOpsNrHasOpsServ($nr=''){
				global $db;

				if(empty($nr) || (!$nr))
						return FALSE;

				$this->_useOpsServ();

				$this->sql="SELECT refno FROM $this->coretable WHERE nr=$nr AND status NOT IN ($this->dead_stat)";

#echo "class_ops.php : encOpsNrHasOpsServ : this->sql = '".$this->sql."' <br> \n";

				if($this->result=$db->Execute($this->sql)){
						if($this->result->RecordCount()){
								$temp = $this->result->FetchRow();
								return $temp['refno'];
						} else { return FALSE; }
				} else { return FALSE; }
		}# end of function encOpsNrHasOpsServ

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

				$this->sql="SELECT * FROM $this->coretable WHERE batch_nr=$batch_nr";
#        $this->sql="SELECT * FROM $this->coretable WHERE batch_nr=$batch_nr AND status NOT IN ($this->dead_stat)";

#echo "class_ops.php : encHasRadioRequest : this->sql = '".$this->sql."' <br> \n";

				if($this->result=$db->Execute($this->sql)){
						if($this->result->RecordCount()){
								return TRUE;
						} else { return FALSE; }
				} else { return FALSE; }
		}

				/*
				* Gets all the operation request(s) info from `care_encounter_op` table.
				* @param string, additional conditions
				* @return RECORDSET of encounter_op request info, if no limit
				* @return ARRAY of an encounter_op request info, if $limitToOne is set
				* @return boolean
				* @created : burn, October 1, 2007
				*/
		function getBasicEncounterOpInfo($nr='',$limitToOne=FALSE, $cond=''){
				global $db;

				$this->_useEncounterOp();

				$OPTIONS ='';
				if ( (empty($nr)) || (!$nr))
						return FALSE;
				if ($nr)
						$OPTIONS .= "AND enc_op.nr='".$nr."'";
#        if (!empty(trim($cond)))
				$cond = trim($cond);
				if (!empty($cond))
						$OPTIONS .= " AND ".$cond;
				$this->sql = "
												SELECT fn_get_pid_name(p.pid) AS person_name,
														p.pid, p.name_first, p.name_middle, p.name_last, p.date_birth, p.sex,
														p.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
														enc_op.op_date AS request_date,
														enc_op.*,
														dept.name_short, dept.id, dept.name_formal, dept.name_alternate, dept.description
												FROM care_encounter_op AS enc_op
														LEFT JOIN care_department AS dept ON enc_op.dept_nr=dept.nr
														LEFT JOIN care_encounter AS enc ON enc_op.encounter_nr=enc.encounter_nr
																LEFT JOIN care_person AS p ON enc.pid=p.pid
																		LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
																				LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
																						LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
																								LEFT JOIN seg_regions AS sr ON  sr.region_nr=sp.region_nr
												WHERE enc_op.status NOT IN ($this->dead_stat)
														 $OPTIONS
												";
#echo "class_ops.php : getBasicEncounterOpInfo : this->sql = '".$this->sql."' <br><br> \n";

				if ($this->result=$db->Execute($this->sql)){
						if($this->count=$this->result->RecordCount()) {
								if ($limitToOne)
										return $this->result->FetchRow();
								return $this->result;
						}else { return FALSE; }
				}else { return FALSE; }
		}# end of function getBasicEncounterOpInfo

				/*
				* Gets all the pid, and firstnames & lastnames of personnel.
				* @param array, array of personell_nr's
				* @return ARRAY of personell_nr, pid, and names of personnel or boolean
				* @created : burn, October 1, 2007
				*/
		function setPersonellNrNamePID($pers_array){

				if(!is_array($pers_array) || empty($pers_array))
						return FALSE;

				$pers_info = array();
				if ($pers_obj = $this->getPersonellNrName($pers_array)){
#echo "class_ops.php : setPersonellNrNamePID : pers_obj : "; print_r($pers_obj); echo " <br><br> \n";
#echo "class_ops.php : setPersonellNrNamePID : pers_info = '".$pers_info."' <br> \n";
#echo "class_ops.php : setPersonellNrNamePID : is_array(pers_info) = '".is_array($pers_info)."' <br> \n";
#echo "class_ops.php : setPersonellNrNamePID : empty(pers_info) = '".empty($pers_info)."' <br> \n";
						foreach($pers_array as $key=>$value){
										# in order
								$pers_info[$value]['pid'] = '';
								$pers_info[$value]['name'] = '';
						}
						foreach($pers_obj as $key=>$person_value){
										# in order
								$pers_info[$person_value['nr']]['pid'] = $person_value['pid'];
								$pers_info[$person_value['nr']]['name'] = $person_value['person_name'];
						}
#echo "class_ops.php : setPersonellNrNamePID : pers_info : "; print_r($pers_info); echo " <br><br> \n";
				}#end of if-stmt 'if ($pers_obj...'
				return $pers_info;
		}# end of function setPersonellNrNamePID
				/*
				* Gets all the pid, and firstnames & lastnames of personnel.
				* @param array, array of personell_nr's
				* @return ARRAY of personell_nr, pid, and names of personnel or boolean
				* @created : burn, October 1, 2007
				*/
		function getPersonellNrName($pers){
				global $db;

				if(!is_array($pers) || empty($pers))
						return FALSE;

				$array_pers_nr = '';
				foreach ($pers as $key => $value){
						$array_pers_nr .= $value.',';
				}
				$array_pers_nr=substr_replace($array_pers_nr,'',(strlen($array_pers_nr))-1);
#echo "class_ops.php : getPersonellNrName : pers : "; print_r($pers); echo " <br><br> \n";
#echo "class_ops.php : getPersonellNrName : array_pers_nr : "; print_r($array_pers_nr); echo " <br><br> \n";

				$this->sql = "SELECT pers.nr, pers.pid,
														fn_get_personell_name(pers.nr) AS person_name
												 FROM care_personell AS pers
														LEFT JOIN care_person AS p ON pers.pid=p.pid
												 WHERE pers.nr IN ($array_pers_nr)
												";
#echo "class_ops.php : getPersonellNrName : this->sql = '".$this->sql."' <br> \n";
#exit();
				if ($this->result=$db->Execute($this->sql)){
						if($this->count=$this->result->RecordCount()) {
								return $this->result->GetArray();
						}else { return FALSE; }
				}else { return FALSE; }
		}# end of function getPersonellNrName

				/*
				* Gets the BASIC operation service request info based on REFERENCE NUMBER
				* @param int reference number
				* @return an ARRAY of operation service request info or boolean
				* @created : burn, October 1, 2007
				*/
		function getBasicOpsServiceInfo($ref_nr=''){
				global $db;

				if(empty($ref_nr) || (!$ref_nr)){
						return FALSE;
				}
				$this->_useOpsServ();
				$this->sql="SELECT o_serv.*,
														p.name_last, p.name_first, p.date_birth, p.sex, p.pid
												FROM $this->coretable AS o_serv
														LEFT JOIN care_person AS p ON p.pid = o_serv.pid
												WHERE o_serv.refno='$ref_nr'
														AND o_serv.status NOT IN ($this->dead_stat)";

#echo "class_ops.php : getBasicOpsServiceInfo: this->sql = '".$this->sql."' <br> \n";
#exit();
				if ($this->result=$db->Execute($this->sql)){
						if($this->count=$this->result->RecordCount()) {
								return $this->result->FetchRow();
						}else { return FALSE; }
				}else { return FALSE; }
		}//end of function getBasicOpsServiceInfo

				/*
				* Gets the ALL operation service request info based on REFERENCE NUMBER
				* @param int reference number
				* @return an ARRAY of operation service request info or boolean
				* @created : burn, October 3, 2007
				*/
		function getAllEncounterOpsServiceInfo($ref_nr=''){
				global $db;

				if(empty($ref_nr) || (!$ref_nr)){
						return FALSE;
				}
				$this->_useOpsServ();
				$this->sql="SELECT fn_get_pid_name(p.pid) AS person_name,
														p.name_first, p.name_middle, p.name_last, p.date_birth, p.sex,
														o_serv.*,
														enc_op.*, enc.encounter_type, enc.is_medico,
														enc.current_dept_nr, enc.current_ward_nr, enc.current_room_nr,
														dept.name_short, dept.id, dept.name_formal, dept.name_alternate, dept.description
												FROM seg_ops_serv AS o_serv
														LEFT JOIN care_encounter_op AS enc_op ON o_serv.nr=enc_op.nr
																LEFT JOIN care_department AS dept ON enc_op.dept_nr=dept.nr
																LEFT JOIN care_encounter AS enc ON enc_op.encounter_nr=enc.encounter_nr
																		LEFT JOIN care_person AS p ON enc.pid=p.pid
												WHERE o_serv.nr='$ref_nr'
														AND enc_op.status NOT IN ('inactive','void','hidden','deleted')
												";
# AND o_serv.status NOT IN ($this->dead_stat)
#echo "class_ops.php : getEncounterOpsServiceInfo : this->sql = '".$this->sql."' <br> \n";
#exit();
				if ($this->result=$db->Execute($this->sql)){
						if($this->count=$this->result->RecordCount()) {
								return $this->result->FetchRow();
						}else { return FALSE; }
				}else { return FALSE; }
		}//end of function getEncounterOpsServiceInfo

				/*
				* Gets the DETAILS of operation service request based on REFERENCE NUMBER
				* @param int reference number
				* @return RECORDSET of the details of operation service request or boolean
				* @created : burn, October 1, 2007
				*/
		function getOpsServDetailsInfo($ref_nr=''){
				global $db;

				if(empty($ref_nr) || (!$ref_nr)){
						return FALSE;
				}
				$this->_useOpsServDetails();
				/*
				$this->sql="SELECT osd.*,
														(SELECT sop.ops_charge
																FROM seg_ops_personell AS sop
																WHERE sop.refno=osd.refno  AND sop.ops_code=osd.ops_code
																LIMIT 1
														) AS ops_charge,
														icpm.description, icpm.rvu AS icpm_rvu, icpm.multiplier AS icpm_multiplier
												FROM seg_ops_servdetails AS osd
														LEFT JOIN care_ops301_en AS icpm ON icpm.code = osd.ops_code
												WHERE osd.refno='$ref_nr'
												";
				*/

				$this->sql="SELECT osd.*,
														(SELECT sop.ops_charge
																FROM seg_ops_personell AS sop
																WHERE sop.refno=osd.refno  AND sop.ops_code=osd.ops_code
																LIMIT 1
														) AS ops_charge,
														icpm.description, icpm.rvu AS icpm_rvu, osd.multiplier AS icpm_multiplier
												FROM seg_ops_servdetails AS osd
														LEFT JOIN seg_ops_rvs AS icpm ON icpm.code = osd.ops_code
												WHERE osd.refno='$ref_nr'
												";

#echo "class_ops.php : getBasicOpsServiceInfo: this->sql = '".$this->sql."' <br> \n";
#exit();
				if ($this->result=$db->Execute($this->sql)){
						if($this->count=$this->result->RecordCount()) {
								return $this->result;
						}else { return FALSE; }
				}else { return FALSE; }
		}//end of function getBasicOpsServiceInfo

				/*
				* Gets the DETAILS of operation service personnel based on REFERENCE NUMBER
				* @param int reference number
				* @param int role type number; 7,surgeon; 8,assistant surgeon; 12,anesthesiologist;
				*                                            9,scrub nurse; 10,rotating nurse;
				*                                            refer to `care_role_person` table
				* @return RECORDSET of the details of operation service request or boolean
				* @created : burn, October 1, 2007
				*/
		function getOpsPersonellInfo($ref_nr='',$role_type_nr=''){
				global $db;

				if(empty($ref_nr) || (!$ref_nr)){
						return FALSE;
				}
				if (trim($role_type_nr)){
						$WHERE_SQL = " AND o_pers.role_type_nr='".trim($role_type_nr)."' ";
				}

				$this->_useOpsPersonell();
				$this->sql="SELECT fn_get_pid_name(p.pid) AS person_name,
														o_pers.*,
														p.name_last, p.name_first, p.date_birth, p.sex
												FROM seg_ops_personell AS o_pers
														LEFT JOIN care_personell AS pers ON pers.nr = o_pers.dr_nr
																LEFT JOIN care_person AS p ON p.pid = pers.pid
												WHERE o_pers.refno='$ref_nr' $WHERE_SQL
												";

#echo "class_ops.php : getOpsPersonellInfo: this->sql = '".$this->sql."' <br> \n";
#exit();
				if ($this->result=$db->Execute($this->sql)){
						if($this->count=$this->result->RecordCount()) {
								return $this->result;
						}else { return FALSE; }
				}else { return FALSE; }
		}//end of function getOpsPersonellInfo

				/*
				* Gets the Personell Nr of the personnel based on REFERENCE NUMBER
				* @param int reference number
				* @param int role type number; 7,surgeon; 8,assistant surgeon; 12,anesthesiologist;
				*                                            9,scrub nurse; 10,rotating nurse;
				*                                            refer to `care_role_person` table
				* @return ARRAY of dr_nr (personell_nr) of the personell or boolean
				* @created : burn, October 6, 2007
				*/
		function getOpsPersonellNr($ref_nr='',$role_type_nr=''){
				global $db;

				if(empty($ref_nr) || (!$ref_nr)){
						return FALSE;
				}
				if (trim($role_type_nr)){
						$WHERE_SQL = " AND o_pers.role_type_nr='".trim($role_type_nr)."' ";
				}

				$this->_useOpsPersonell();
				$this->sql="SELECT DISTINCT dr_nr
												FROM $this->coretable AS o_pers
												WHERE o_pers.refno='$ref_nr' $WHERE_SQL
												ORDER BY o_pers.role_entry_no
												";

#echo "class_ops.php : getOpsPersonellNr : this->sql = '".$this->sql."' <br> \n";
#exit();
				if ($this->result=$db->Execute($this->sql)){
						if($this->count=$this->result->RecordCount()) {
								$arr = array();
								while($tmp = $this->result->FetchRow()){
										array_push($arr,$tmp[0]);
								}
								return $arr;
#                return $this->result->GetArray();
						}else { return FALSE; }
				}else { return FALSE; }
		}//end of function getOpsPersonellNr

				/*
				* Gets the list of requests [ops code] based on REFERENCE NUMBER
				* @param int reference number
				* @return an ARRAY of ops code ONLY or boolean
				* @created : burn, October 1, 2007
				*/
		function getListedOpsRequestsByRefNo($ref_nr='',$cond=''){
				global $db;

				if(empty($ref_nr) || (!$ref_nr)){
						return FALSE;
				}
				if(empty($cond) || (!$cond)){
						$cond = " AND status NOT IN ($this->dead_stat) ";
				}

				$this->sql="SELECT ops_code
												FROM seg_ops_servdetails
												WHERE refno='$ref_nr'
														$cond";

#echo "class_ops.php : getListedOpsRequestsByRefNo: this->sql = '".$this->sql."' <br> \n";
#exit();
				if ($buf=$db->Execute($this->sql)){
						if($this->count=$buf->RecordCount()) {
								$arr = array();
								while($tmp = $buf->FetchRow()){
										array_push($arr,$tmp[0]);
								}
								return $arr;
						}else { return FALSE; }
				}else { return FALSE; }
		}//end fucntion getListedOpsRequestsByRefNo

				/**
				* Updates the status of a radio request in table 'care_test_request_radio'.
				* @access public
				* @param int, batch_nr
				* @param string, new status
				* @return boolean
				* @created : burn, July 31, 2007
				*/
		function updateRadioRequestStatus($batch_nr='', $new_status=''){
				global $db,$HTTP_SESSION_VARS;

				if(empty($batch_nr) || (!$batch_nr))
						return FALSE;
				if(empty($new_status) || (!$new_status))
						return FALSE;

				$history = $this->ConcatHistory("Update status [$new_status] ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
# pending, referral, done
				$this->sql="UPDATE $this->tb_test_request_radio ".
												" SET status='".$new_status."', history=".$history.", ".
												"        modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
												" WHERE batch_nr = $batch_nr";

#echo "class_ops.php : updateRadioRequestStatus : this->sql = '".$this->sql."' <br> \n";

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
#    function updateRadioRequestStatusByRefNoServCode($refno='',$serv_code='', $new_status=''){
		function updateRadioRequestStatusByRefNoServCode($data,$arrayItems, $new_status=''){
				global $db,$HTTP_SESSION_VARS;
/*
echo "<br>class_ops.php : updateRadioRequestStatusByRefNoServCode : data = '".$data."' <br> \n";
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : is_object(data) = '".is_object($data)."' <br> \n";
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : is_array(data) = '".is_array($data)."' <br> \n";
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : data : "; print_r($data); echo " <br> \n";
*/
				if(!is_array($data) || empty($data))
						return FALSE;
				if(!is_array($arrayItems) || empty($arrayItems))
						return FALSE;

				$this->_useRequestRadio();
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
						unset($this->data_array['pcash']);
						unset($this->data_array['pcharge']);
						$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
/*
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : new_status='$new_status' <br> \n";
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : 1 this->data_array['status'] = '".$this->data_array['status']."' <br> \n";
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : 1 isset(this->data_array['status']) = '".isset($this->data_array['status'])."' <br> \n";
*/
				if (!empty($new_status)){
#echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : status NEEDS to be change <br> \n";
								# if the status needs to be change
						$history = $this->ConcatHistory("Update status [$new_status] ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
						$this->data_array['history'] = $history;
						$this->data_array['status'] = $new_status;
				}else{
#echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : there is NO NEED for change of status <br> \n";
						$this->data_array['history'] = $this->ConcatHistory("Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				}
/*
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : this->data_array = '".$this->data_array."' <br> \n";
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : is_object(this->data_array) = '".is_object($this->data_array)."' <br> \n";
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : this->data_array : "; print_r($this->data_array); echo " <br> \n";
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : 2 this->data_array['status'] = '".$this->data_array['status']."' <br> \n";
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : 2 isset(this->data_array['status']) = '".isset($this->data_array['status'])."' <br> \n";
*/
				if (empty($new_status) || ($new_status=='pending')){
								# there is NO NEED for change of status
						if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
								else $concatfx='concat';

								#    Only the keys of data to be updated must be present in the passed array.
						$x='';
						$v='';
						$this->buffer_array = array();
						while(list($x,$v)=each($this->ref_array)) {
		#            if(isset($this->data_array[$v])&&(trim($this->data_array[$v])!='')) {
								if (isset($this->data_array[$v]))
										$this->buffer_array[$v]=trim($this->data_array[$v]);
		#            }
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
#echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : elems = '".$elems."' <br> \n";
				}# end of if-stmt 'if (empty($new_status) || ($new_status=='pending'))'

# pending, referral, done
				if (empty($new_status) || ($new_status=='pending')){
						$this->sql="UPDATE $this->tb_test_request_radio ".
														" SET $elems, ".
														"         clinical_info=?, request_doctor=?, is_in_house=?, ".
														"         price_cash=?, price_charge=?, modify_dt=NOW() ".
														" WHERE refno = '$refno' AND service_code = ?";
				}else{
						$this->sql="UPDATE $this->tb_test_request_radio ".
														" SET status='".$new_status."', history=".$history.", ".
														"        encoder='".$HTTP_SESSION_VARS['sess_user_name']."',".
														"        modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
														" WHERE refno = '$refno' AND service_code = ?";
				}
/*
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : arrayItems = '".$arrayItems."' <br> \n";
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : arrayItems : "; print_r($arrayItems); echo " <br> \n";
echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : this->sql = '".$this->sql."' <br> \n";
# $db->debug = true;
*/
				if ($buf=$db->Execute($this->sql,$arrayItems)){
						if($db->Affected_Rows()) {
								return TRUE;
						}else { return FALSE; }
				}else { return FALSE; }
#echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : FALSE 1 <br> \n";
#echo "<br>class_ops.php : updateRadioRequestStatusByRefNoServCode : db->ErrorMsg()='$db->ErrorMsg()'<br> \n";

		}# end of function updateRadioRequestStatusByRefNoServCode

				/**
				* Deletes logically a radio request in table 'seg_radio_serv' and 'care_test_request_radio'.
				* @access public
				* @param int, refno
				* @return boolean
				* @created : burn, September 10, 2007
				*/
		function deleteRefNo($refno=''){
				global $db,$HTTP_SESSION_VARS;

				if(empty($refno) || (!$refno))
						return FALSE;

				$this->_useOpsServ();
				$history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
						# logical deletion in table 'seg_radio_serv'
				$this->sql="UPDATE $this->coretable ".
												" SET status='deleted', history=".$history.", ".
												"        modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
												" WHERE refno = '$refno'";

#echo "class_ops.php : deleteRefNo for table 'seg_radio_serv' : this->sql = '".$this->sql."' <br> \n";

				if ($buf=$db->Execute($this->sql)){
						if($db->Affected_Rows()) {
										# logical deletion in table 'care_test_request_radio'
								$this->sql="UPDATE $this->tb_test_request_radio ".
																" SET status='deleted', history=".$history.", ".
																"        modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
																" WHERE refno = '$refno'";
#echo "class_ops.php : deleteRefNo for table 'care_test_request_radio' : this->sql = '".$this->sql."' <br> \n";
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
				* @param string, new date of service
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
												"        modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
												" WHERE batch_nr = $batch_nr";

#echo "class_ops.php : updateRadioRequestServiceDate : this->sql = '".$this->sql."' <br> \n";

				if ($buf=$db->Execute($this->sql)){
						if($db->Affected_Rows()){
								return TRUE;
						}else { return FALSE; }
				}else { return FALSE; }
		}# end of function updateRadioRequestServiceDate

		/*--added by Francis 11-26-13--*/
		/*--Delete procedures/ICP--*/
		function delProcedure($encounter, $bill_dt, $bill_frmdte, $op_code) {
			global $db;

			$bSuccess = false;

			$strSQL = "select * from seg_misc_ops_details ".
								"   where ops_code = '".$op_code."' and exists (select * from seg_misc_ops as smo where smo.refno = seg_misc_ops_details.refno ".
								"      and smo.encounter_nr in $encounter and smo.chrge_dte >= '".$bill_frmdte."') ".
					      "      and not EXISTS(SELECT * FROM seg_ops_chrgd_accommodation AS soca WHERE soca.ops_refno = seg_misc_ops_details.refno AND
											 soca.ops_entryno = seg_misc_ops_details.entry_no AND soca.ops_code = seg_misc_ops_details.ops_code)
						 and not EXISTS(SELECT * FROM seg_ops_chrg_dr AS socd WHERE socd.ops_refno = seg_misc_ops_details.refno AND
											 socd.ops_entryno = seg_misc_ops_details.entry_no AND socd.ops_code = seg_misc_ops_details.ops_code) ".
								"      and get_lock('smops_lock', 10) ".
								"   order by entry_no desc limit 1";

			    $rs = $db->Execute($strSQL);
			    if ($rs) {
					$db->StartTrans();
					$row = $rs->FetchRow();
					if ($row) {
						$refno = $row['refno'];
						$entryno = $row['entry_no'];

						$strSQL = "delete from seg_misc_ops_details where refno = '$refno' and entry_no = $entryno and ops_code = '$op_code'";
						$bSuccess = $db->Execute($strSQL);

						$strSQL = "select RELEASE_LOCK('smops_lock')";
						$db->Execute($strSQL);

						if ($bSuccess) {
								// Delete this header if already without details ...
								$dcount = 0;
								$strSQL = "select count(*) dcount from seg_misc_ops_details where refno = '$refno'";
		 					  $rs = $db->Execute($strSQL);
		 					  if ($rs) {
									$row = $rs->FetchRow();
									$dcount = ($row) ? $row['dcount'] : 0;
									if ($dcount == 0) {
											$strSQL = "delete from seg_misc_ops where refno = '$refno'";
											$bSuccess = $db->Execute($strSQL);
									}
		 					  }
						}


						if($bSuccess) {
							$db->CompleteTrans();
							return TRUE;

						}else{
							$db->FailTrans();
							return FALSE;
						}
					}
	  			}else{ return FALSE;};

		}//end of delProcedure function


		/*-----------------Add Procedures/ICP-------------------------------*/
		//added by Francis 11-27-2013
		function addProcedure($procData) {
			global $db;
			extract($procData);

			$bSuccess = true;

			if($encNr != ''){

				$db->StartTrans();

				$refno = $this->getMiscOpRefNo($billDate, $encNr);

				if ($refno == '') {
					$strSQL = "insert into seg_misc_ops (chrge_dte, encounter_nr, modify_id, create_id, create_dt) ".
										"   values ('".$billDate."', '".$encNr."', '".$user."', '".$user."', ".
										"          '".$billDate."')";
					if ($db->Execute($strSQL))
							$refno = $this->getMiscOpRefNo($billDate, $encNr);
					else
							$bSuccess = false;
				}

				if($bSuccess){
					$op_charge = str_replace(",", "", $charge);
					$strSQL = "insert into seg_misc_ops_details (refno, ops_code, rvu, multiplier, chrg_amnt, op_date) ".
								"   values ('".$refno."', '".$code."', ".$rvu.", ".$multiplier.", ".$op_charge.", '".$opDate."')";
					$bSuccess = $db->Execute($strSQL);
				}

				if($bSuccess) {
					$db->CompleteTrans();
					return TRUE;
				}else{
					$db->FailTrans();
					return FALSE;
				}

			}else{return FALSE;}			

		}
		/*---------end-----Add Procedures/ICP-----------end-----------------*/

		/*------------------------Get Procedure Refno-------------------------*/
		//added by Francis 11-27-2013
		function getMiscOpRefNo($bill_frmdte, $enc_nr){
		global $db;

			$srefno = '';
			$strSQL = "select refno ".
								"   from seg_misc_ops ".
								"   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= '".$bill_frmdte."' ".
								"      and encounter_nr = '".$enc_nr."' ".
								"   order by chrge_dte limit 1";

			if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
							while ($row = $result->FetchRow())
									$srefno = $row['refno'];
					}
			}

			return($srefno);
		}
		/*----------end-----------Get Procedure Refno------------end----------*/
				/*
				* Handles the SAVING of operation request info
				*         into tables `seg_ops_serv`,`seg_ops_servdetails`,`seg_ops_personell`,
				* @param Array Data to by reference
				* @return NEW refno OR boolean
				* @created : burn, October 5, 2007
				*/
		function saveOpsBilling2(&$data){

				if ($this->saveOpsServInfoFromArray($data)){
						$data['refno'] = $this->encOpsNrHasOpsServ($data['op_request_nr']);
				}else{
						return FALSE;   # failed in saving the ops service info
				}# end of else-stmt of if-stmt 'if ($this->saveOpsServInfoFromArray($data))'

#echo "class_ops.php : saveOpsBilling :: data['refno'] = '".$data['refno']."' <br><br> \n";
#echo "class_ops.php : saveOpsBilling :: data : <br> \n "; print_r($data); echo " <br><br> \n";

				if ($this->saveOpsServDetailsInfoFromArray($data)){
				}else{
						return FALSE;   # failed in saving ops service details
				}# end of else-stmt of if-stmt 'if ($this->saveOpsServDetailsInfoFromArray($data))'
#echo "class_ops.php : after saveOpsServDetailsInfoFromArray";
#exit();

								#NOTE : role_type_nr == 7,surgeon; 8,assistant surgeon; 12,anesthesiologist;
								#                9,scrub nurse, 10,rotating nurse
								#            refer to `care_role_person` table
				$data['pers_code'] = $data['surgeon'];
				$data['role_type_nr'] = 7;

				if ($this->saveOpsPersonell($data)){
				}else{
						return FALSE;   # failed in saving surgeon personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'
#exit();
				$data['pers_code'] = $data['assistant'];
				$data['role_type_nr'] = 8;
				if ($this->saveOpsPersonell($data)){
				}else{
						return FALSE;   # failed in saving assistant surgeon personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'

				$data['pers_code'] = $data['an_doctor'];
				$data['role_type_nr'] = 12;
				if ($this->saveOpsPersonell($data)){
				}else{
						return FALSE;   # failed in saving anesthesiologist personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'
/*
				*** DEFINITELY DO NOT OMIT this part...it may be useful someday :=) ***
				*** burn : October 5, 2007

				$data['pers_code'] = $data['scrub_nurse'];
				$data['role_type_nr'] = 9;
				if ($this->saveOpsPersonell($data)){
				}else{
						return FALSE;   # failed in saving scrub nurse personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'

				$data['pers_code'] = $data['rotating_nurse'];
				$data['role_type_nr'] = 10;
				if ($this->saveOpsPersonell($data)){
				}else{
						return FALSE;   # failed in saving rotating nurse personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'
*/
#echo "class_ops.php : after saveOpsPersonell";
#exit();
				return $data['refno'];   # successfully save all data in 3 tables...:=)
		}# end of function saveOpsBilling2



				/*
				* Handles the SAVING of operation request info
				*         into tables `seg_ops_serv`,`seg_ops_servdetails`,`seg_ops_personell`,
				* @param Array Data to by reference
				* @return NEW refno OR boolean
				* @created/modified : burn, October 5, 2007; December 19, 2007
				*/
		function saveOpsBilling(&$data){

				#$data['refno'] = $this->getNewRefNo('2007000001');

				#edited by VAN
				$ref = date('Y').'000001';
				#echo "ref = ".$ref;
				$data['refno'] = $this->getNewRefNo($ref);
#echo "class_ops.php : saveOpsBilling :: data['refno'] = '".$data['refno']."' <br><br> \n";
#echo "class_ops.php : saveOpsBilling :: data : <br> \n "; print_r($data); echo " <br><br> \n";
				if ($this->saveOpsServInfoFromArray($data)){
						#$data['refno'] = $this->encOpsNrHasOpsServ($data['op_request_nr']);
				}else{
						return FALSE;   # failed in saving the ops service info
				}# end of else-stmt of if-stmt 'if ($this->saveOpsServInfoFromArray($data))'

#exit();
				if ($this->saveOpsServDetailsInfoFromArray($data)){
				}else{
						return FALSE;   # failed in saving ops service details
				}# end of else-stmt of if-stmt 'if ($this->saveOpsServDetailsInfoFromArray($data))'
#echo "class_ops.php : after saveOpsServDetailsInfoFromArray";
#exit();

								#NOTE : role_type_nr == 7,surgeon; 8,assistant surgeon; 12,anesthesiologist;
								#                9,scrub nurse, 10,rotating nurse
								#            refer to `care_role_person` table
				$data['pers_code'] = $data['surgeon'];
				$data['role_type_nr'] = 7;

				#added by VAN 02-13-08
				#$date['date'] = date('Y-m-d H:i:s');

				if ($this->saveOpsPersonell($data)){
				}else{
						return FALSE;   # failed in saving surgeon personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'
#exit();
#        $data['pers_code'] = $data['assistant'];
				$data['pers_code'] = $data['surgeon_assist'];
				$data['role_type_nr'] = 8;
				if ($this->saveOpsPersonell($data)){
				}else{
						return FALSE;   # failed in saving assistant surgeon personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'

#        $data['pers_code'] = $data['an_doctor'];
				$data['pers_code'] = $data['anesthesiologist'];
				$data['role_type_nr'] = 12;
				if ($this->saveOpsPersonell($data)){
				}else{
						return FALSE;   # failed in saving anesthesiologist personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'

#        *** DEFINITELY DO NOT OMIT this part...it may be useful someday :=) ***
#        *** burn : October 5, 2007

#        $data['pers_code'] = $data['scrub_nurse'];
				$data['pers_code'] = $data['nurse_scrub'];
				$data['role_type_nr'] = 9;
				if ($this->saveOpsPersonell($data)){
				}else{
						return FALSE;   # failed in saving scrub nurse personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'

#        $data['pers_code'] = $data['rotating_nurse'];
				$data['pers_code'] = $data['nurse_rotating'];
				$data['role_type_nr'] = 10;
				if ($this->saveOpsPersonell($data)){
				}else{
						return FALSE;   # failed in saving rotating nurse personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'

#echo "class_ops.php : after saveOpsPersonell";
#exit();

#        $op_request_nr = $this->saveCareEncounterOp($data);
#echo "class_ops.php : after calling saveCareEncounterOp : 1 data['refno'] ='".$data['refno']."' <br> \n";
#echo "class_ops.php : after calling saveCareEncounterOp : 1 op_request_nr ='".$op_request_nr."' <br> \n";
#        if ($op_request_nr){

						# SAVE in 'care_encounter_op' table
				if ($op_request_nr = $this->saveCareEncounterOp($data)){
								#inserts the 'nr' from 'care_encounter_op' table to 'nr' in 'seg_ops_serv' table
#echo "class_ops.php : after calling saveCareEncounterOp : 2 data['refno'] ='".$data['refno']."' <br> \n";
#echo "class_ops.php : after calling saveCareEncounterOp : 2 op_request_nr ='".$op_request_nr."' <br> \n";
						$this->insertOpRequestNr($data['refno'],$op_request_nr);
				}else{
						return FALSE;   # failed in saving in 'care_encounter_op' table
				}

				return $data['refno'];   # successfully save all data in 3 tables...:=)
		}# end of function saveOpsBilling




				/*
				* Inserts new operation request info into table 'seg_ops_serv'
				* @param Array Data to by reference
				* @return boolean
				* @created : burn, October 1, 2007
				*/
		function saveOpsServInfoFromArray(&$data){
				global $db,$HTTP_SESSION_VARS;

#echo "class_ops.php :  saveOpsServInfoFromArray : data : <br> "; print_r($data); echo " <br> \n";

				$this->_useOpsServ();
				extract($data);

#echo "class_ops.php :  saveOpsServInfoFromArray : arrayItems : <br> "; print_r($arrayItems); echo " <br> \n";
#        "refno","nr","request_date","encounter_nr","pid","ordername","orderaddress","is_cash","hasPaid","is_urgent",
#        "comments","status","history","modify_id","modify_dt","create_id","create_dt"
#        $refno = $this->getNewRefNo(date('Y')."000001");
#        $index = "refno,request_date,encounter_nr,pid,ordername,orderaddress,is_cash,hasPaid,is_urgent,
#                comments,status,history,create_id,create_dt";

								# refno for seg_ops_serv is done using a trigger; October 5, 2007
								# refno for seg_ops_serv is NOT done using a trigger; Decemeber 5, 2007
				$index = "refno,nr,request_date,request_time,encounter_nr,pid,ordername,orderaddress,is_cash,hasPaid,is_urgent,
								comments,status,history,create_id,create_dt";
				$values = "'$refno','$nr','$request_date','$request_time','$encounter_nr','$pid','$ordername','$orderaddress',$is_cash,".
								"'$hasPaid', $is_urgent,'$comments','$status','$history','".$HTTP_SESSION_VARS['sess_user_name']."', NOW()";

#        $index = "encounter_nr, clinical_info, service_code, is_in_house,
#                        request_doctor, request_date, encoder,    status, priority, history, create_id, create_dt";

				$this->sql = "INSERT INTO $this->coretable ($index)
														VALUES ($values)";


#echo "class_ops.php :  saveOpsServInfoFromArray : this->sql = '".$this->sql."' <br><br> \n";
#exit();
			 // echo $this->sql . '<Br/>';
				if ($db->Execute($this->sql)) {
						if ($db->Affected_Rows()) {
#echo "class_ops.php :  saveOpsServInfoFromArray : db->Insert_ID() = '".$db->Insert_ID()."' <br> \n";
#                $data['refno']=$refno;
#                $this->saveOpsServDetailsInfoFromArray($data);
#                return $refno;
								return TRUE;
						}else{ return FALSE; }
				}else{ return FALSE; }
		}# end function  saveOpsServInfoFromArray

				/*
				* Inserts new operation request info into table 'seg_ops_servdetails'
				* @param Array Data to by reference
				* @return boolean
				* @created : burn, October 1, 2007
				*/
		function saveOpsServDetailsInfoFromArray(&$data){
				global $db,$HTTP_SESSION_VARS;

#echo "<br>class_ops.php : saveOpsServDetailsInfoFromArray : data : <br> "; print_r($data); echo " <br><br> \n";

				$this->_useOpsServDetails();
				extract($data);

				$arrayItems = array();
				foreach ($ops_code as $key => $value){
						$tempArray = array($value,$rvu[$key],$multiplier[$key]);
						array_push($arrayItems,$tempArray);
				}


#echo "class_ops.php : saveOpsServDetailsInfoFromArray : arrayItems : <br> "; print_r($arrayItems); echo " <br> \n";
#        "refno", "ops_code", "rvu", "multiplier"
				$index = "refno, ops_code, rvu, multiplier";
				$values = "'$refno', ?, ?, ?";

				$this->sql = "INSERT INTO $this->coretable ($index)
														VALUES ($values)";

#echo "class_ops.php : saveOpsServDetailsInfoFromArray : this->sql = '".$this->sql."' <br> \n";
#exit();
				if ($db->Execute($this->sql,$arrayItems)) {
						if ($db->Affected_Rows()) {
								return TRUE;
						}else{ return FALSE; }
				}else{ return FALSE; }
		}# end function saveOpsServDetailsInfoFromArray

				/*
				* Inserts new operation request info into table 'seg_ops_personell'
				* @param Array Data to by reference
				* @return boolean
				* @created : burn, October 1, 2007
				*/
		function saveOpsPersonell(&$data){
				global $db,$HTTP_SESSION_VARS;

#echo "<br>class_ops.php :saveOpsPersonell : data : <br> "; print_r($data); echo " <br><br> \n";

				$this->_useOpsPersonell();
				extract($data);

				if(!is_array($pers_code) || empty($pers_code))
						return TRUE; # nothing to save

				$arrayItems = array();
				$i=1;
				foreach ($ops_code as $ops_code_key => $ops_code_value){
						foreach ($pers_code as $key => $value){
#                $tempArray = array($value,$i,$ops_code_value,$ops_charge[$ops_code_key]);
#                $charge_temp = number_format($ops_charge[$ops_code_key], 2, '.', '');
								$charge_temp = str_replace(',', "", $ops_charge[$ops_code_key]);
#                $tempArray = array($value,$ops_code_value,$ops_charge[$ops_code_key]);
								$tempArray = array($value,$ops_code_value,$charge_temp);

								array_push($arrayItems,$tempArray);
								$i++;
						}
						reset($pers_code);
				}

#echo "class_ops.php : saveOpsPersonell : arrayItems : <br> "; print_r($arrayItems); echo " <br> \n";
#exit();
#refno, dr_nr, role_entry_no, role_type_nr, ops_code, ops_charge
				#edited by VAN 02-13-08
				#$index = "refno, dr_nr, role_type_nr, ops_code, ops_charge";
				#$values = "'$refno', ?, '$role_type_nr', ?, ?";
				$index = "refno, dr_nr, role_type_nr, ops_code, ops_charge, modify_id, modify_dt, create_id, create_dt";
				$values = "'$refno', ?, '$role_type_nr', ?, ?, '".$HTTP_SESSION_VARS['sess_user_name']."', NOW(), '".$HTTP_SESSION_VARS['sess_user_name']."', NOW()";

				$this->sql = "INSERT INTO $this->coretable ($index)
														VALUES ($values)";

#echo "class_ops.php : saveOpsPersonell : this->sql = '".$this->sql."' <br> \n";
#exit();
				if ($ok = $db->Execute($this->sql,$arrayItems)) {
						if ($db->Affected_Rows()) {
								return TRUE;
						}else{ return FALSE; }
				}else{ return FALSE; }
		}# end function saveOpsPersonell


				/*
				* Handles the SAVING of operation request info
				*         into tables `seg_ops_serv`,`seg_ops_servdetails`,`seg_ops_personell`,
				* @param Array Data to by reference
				* @return boolean
				* @created : burn, October 9, 2007
				*/
		function updateOpsBilling(&$data){

				if ($this->updateOpsServInfoFromArray($data)){
				}else{
						return FALSE;   # failed in updating the ops service info
				}# end of else-stmt of if-stmt 'if ($this->updateOpsServInfoFromArray($data))'

				if ($this->updateOpsServDetailsInfoFromArray($data)){
				}else{
						return FALSE;   # failed in updating ops service details
				}# end of else-stmt of if-stmt 'if ($this->updateOpsServDetailsInfoFromArray($data))'

				if ($this->updateOpsPersonell($data)){
				}else{
						return FALSE;   # failed in updating ops service personnel
				}# end of else-stmt of if-stmt 'if ($this->updateOpsPersonell($data))'

#echo "class_ops.php : after updateOpsServDetailsInfoFromArray";
#exit();
						# UPDATE in 'care_encounter_op' table
				if ($op_request_nr = $this->updateCareEncounterOp($data)){
								#inserts the 'nr' from 'care_encounter_op' table to 'nr' in 'seg_ops_serv' table
#echo "class_ops.php : after calling updateCareEncounterOp : 2 data['refno'] ='".$data['refno']."' <br> \n";
#echo "class_ops.php : after calling updateCareEncounterOp : 2 op_request_nr ='".$op_request_nr."' <br> \n";
//            $this->insertOpRequestNr($data['refno'],$op_request_nr);
				}else{
						return FALSE;   # failed in saving in 'care_encounter_op' table
				}

				return TRUE;
		}# end of function updateOpsBilling

	 /*
		* Updates OR operation code request info in table 'seg_ops_serv'
		* @param Array Data to by reference
		* @return boolean
		* @created : burn, October 9, 2007
		*/
		function updateOpsServInfoFromArray(&$data){
				global $HTTP_SESSION_VARS, $dbtype;
				global $db;
#    echo "updateOpsServInfoFromArray : data = ";
#    print_r ($data);
#    echo " <br> \n";


				$this->data_array=$data;
				// remove probable existing array data to avoid replacing the stored data
				unset($this->data_array['create_id']);
				unset($this->data_array['create_dt']);
				unset($this->data_array['modify_dt']);
				unset($this->data_array['status']);
				$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
				$this->data_array['history']=$this->ConcatHistory("Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n");

/*
#echo "class_ops.php : data['service_code'] : "; print_r($data['service_code']); echo " <br><br> \n";
		$current_list = $this->getListedOpsRequestsByRefNo($data['refno']);
#echo "class_ops.php : current_list : "; print_r($current_list); echo " <br><br> \n";
		$current_deleted_list = $this->getListedOpsRequestsByRefNo($data['refno'],"AND status IN ($this->dead_stat)");
#echo "class_ops.php : current_deleted_list : "; print_r($current_deleted_list); echo " <br><br> \n";
		$update_only_list = array_intersect($data['ops_code'],$current_list);
#echo "class_ops.php : update_only_list : "; print_r($update_only_list); echo " <br><br> \n";
		$add_only_list = array_diff($data['ops_code'],$current_list);
#echo "class_ops.php : add_only_list 1 : "; print_r($add_only_list); echo " <br><br> \n";
		$update_status_only_list = array_intersect($current_deleted_list,$add_only_list);
#echo "class_ops.php : update_status_only_list 1 : "; print_r($update_status_only_list); echo " <br><br> \n";
		$update_deleted2pending_status_only_list = array_intersect($data['ops_code'],$current_deleted_list);
#echo "class_ops.php : update_deleted2pending_status_only_list : "; print_r($update_deleted2pending_status_only_list); echo " <br><br> \n";
		$update_status_only_list = array_unique(array_merge($update_status_only_list,$update_deleted2pending_status_only_list));
#echo "class_ops.php : update_status_only_list 2 : "; print_r($update_status_only_list); echo " <br><br> \n";
		$add_only_list = array_diff($add_only_list,$update_status_only_list);
#echo "class_ops.php : add_only_list 2 : "; print_r($add_only_list); echo " <br><br> \n";
#    $delete_only_list = array_diff($current_list,$_POST['ops_code']);
		$delete_only_list = array_diff($current_list,$data['ops_code']);
#echo "class_ops.php : delete_only_list : "; print_r($delete_only_list); echo " <br><br> \n";
#exit();
*/
				$this->_useOpsServ();
				if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
						else $concatfx='concat';

						#    Only the keys of data to be updated must be present in the passed array.
				$x='';
				$v='';
				$this->buffer_array = array();
				while(list($x,$v)=each($this->ref_array)) {
#            if(isset($this->data_array[$v])&&(trim($this->data_array[$v])!='')) {
						if (isset($this->data_array[$v]))
								$this->buffer_array[$v]=trim($this->data_array[$v]);
#            }
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
#echo "class_ops.php : updateOpsServInfoFromArray : elems = '".$elems."' <br> \n";
			$this->sql="UPDATE $this->coretable SET $elems, modify_dt=NOW() ".
												" WHERE refno=".$this->data_array['refno']." ";

#echo "class_ops.php :  updateOpsServInfoFromArray : this->sql = '".$this->sql."' <br> \n";
#exit();
#return FALSE;
				//return $this->Transact();
						/** Commented out by omick, January 20, 2009
						* Reason: not so flexible considering that there are other transactions that needs to
						* be rollbacked when failed. either the seg_ops_serv table or the other tables
						* will be left orphaned.
						* Solution: use the manual db->Execute and the regular boolean value to return success or failure
						*/
				if ($db->Execute($this->sql)) {

					return true;
				}
				else {
					return false;
				}
		}# end of function updateOpsServInfoFromArray

				/*
				* Updates operation request info in table 'seg_ops_servdetails'
				* @param Array Data to by reference
				* @return boolean
				* @created : burn, October 9, 2007
				*/
		function updateOpsServDetailsInfoFromArray(&$data){
				global $db,$HTTP_SESSION_VARS;

#echo "<br>class_ops.php : updateOpsServDetailsInfoFromArray : data : <br> "; print_r($data); echo " <br><br> \n";

				$this->_useOpsServDetails();
				$refno = $data['refno'];

				$this->sql="DELETE FROM $this->coretable WHERE refno='$refno'";
#        $ok = $db->Execute($this->sql);
#echo "class_ops.php : updateOpsServDetailsInfoFromArray : ok = '".$ok."' <br> \n";
#echo "class_ops.php : updateOpsServDetailsInfoFromArray : this->sql = '".$this->sql."' <br> \n";
#echo "class_ops.php : updateOpsServDetailsInfoFromArray : db->Affected_Rows() = '".$db->Affected_Rows()."' <br> \n";
#exit();
				if ($db->Execute($this->sql)) {
						return $this->saveOpsServDetailsInfoFromArray($data);
						#return TRUE;
				}else{ return FALSE; }

		}# end function updateOpsServDetailsInfoFromArray

				/*
				* Updates operation request info in table 'seg_ops_personell'
				* @param Array Data to by reference
				* @return boolean
				* @created : burn, October 9, 2007
				*/
		function updateOpsPersonell(&$data){
				global $db;

#echo "<br>class_ops.php : updateOpsPersonell : data : <br> "; print_r($data); echo " <br><br> \n";

				$ok = $this->deleteOpsPersonell($data['refno']);

/*
echo "class_ops.php : updateOpsPersonell : after deleteOpsPersonell <br> \n";
echo "class_ops.php : updateOpsPersonell : ok = '".$ok."' <br> \n";
echo "class_ops.php : updateOpsPersonell : this->sql = '".$this->sql."' <br> \n";
#exit();
*/
								#NOTE : role_type_nr == 7,surgeon; 8,assistant surgeon; 12,anesthesiologist;
								#                9,scrub nurse, 10,rotating nurse
								#            refer to `care_role_person` table
				$data['pers_code'] = $data['surgeon'];
				$data['role_type_nr'] = 7;
				if ($this->saveOpsPersonell($data)){
				}else{
#echo "class_ops.php : updateOpsPersonell : failed in saving SURGEON personnel <br> \n";
						return FALSE;   # failed in saving surgeon personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'
#exit();
#        $data['pers_code'] = $data['assistant'];
				$data['pers_code'] = $data['surgeon_assist'];
				$data['role_type_nr'] = 8;
				if ($this->saveOpsPersonell($data)){
				}else{
#echo "class_ops.php : updateOpsPersonell : failed in saving ASSISTANT SURGEON personnel <br> \n";
						return FALSE;   # failed in saving assistant surgeon personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'

#        $data['pers_code'] = $data['an_doctor'];
				$data['pers_code'] = $data['anesthesiologist'];
				$data['role_type_nr'] = 12;
				if ($this->saveOpsPersonell($data)){
				}else{
#echo "class_ops.php : updateOpsPersonell : failed in saving ANESTHESIOLOGIST personnel <br> \n";
						return FALSE;   # failed in saving anesthesiologist personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'
/*
				*** DEFINITELY DO NOT OMIT this part...it may be useful someday :=) ***
				*** burn : October 9, 2007
*/
#        $data['pers_code'] = $data['scrub_nurse'];
				$data['pers_code'] = $data['nurse_scrub'];
				$data['role_type_nr'] = 9;
				if ($this->saveOpsPersonell($data)){
				}else{
						return FALSE;   # failed in saving scrub nurse personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'

#        $data['pers_code'] = $data['rotating_nurse'];
				$data['pers_code'] = $data['nurse_rotating'];
				$data['role_type_nr'] = 10;
				if ($this->saveOpsPersonell($data)){
				}else{
						return FALSE;   # failed in saving rotating nurse personnel
				}# end of else-stmt of if-stmt 'if ($this->saveOpsPersonell($data))'

				return TRUE;
		}# end function updateOpsPersonell

				/*
				* Deletes the personnel involved in a particular REFERENCE NUMBER
				* @param int reference number
				* @param string other conditions
				* @return boolean
				* @created : burn, October 9, 2007
				*/
		function deleteOpsPersonell($refno='', $cond='') {
				if (!$refno)
						return FALSE;
				$this->_useOpsPersonell();
				$this->sql="DELETE FROM $this->coretable WHERE refno='$refno' $cond";
		return $this->Transact();
		}

				/**
				* Checks if an encounter OP nr [from `care_encounter_op` table]
				*         has an entry in `seg_ops_serv` table based on request ENCOUNTER OP NUMBER [nr]
				* @access public
				* @param int encounter op number
				* @param string personnel type
				*    "operator", surgeon;    "assistant", assistant surgeon; "an_doctor", anesthesiologist
				*    "scrub_nurse", scrub nurse; "rotating_nurse", rotating nurse;
				* @return ARRAY of personnel_nr OR boolean
				* @created : burn, October 10, 2007
				*/
		function getPersonnelFromEncOps($nr='',$personnel_type=''){
				global $db;

				if(empty($nr) || (!$nr))
						return FALSE;
				if(empty($personnel_type) || (trim($personnel_type)==''))
						return FALSE;

				$this->_useEncounterOp();

				$this->sql="SELECT ".$personnel_type." FROM $this->coretable ".
												" WHERE nr=$nr AND status NOT IN ($this->dead_stat)";

#echo "class_ops.php : getPersonnelFromEncOps : this->sql = '".$this->sql."' <br> \n";

				if($this->result=$db->Execute($this->sql)){
						if($this->result->RecordCount()){
								$temp = $this->result->FetchRow();
#echo "class_ops.php : getPersonnelFromEncOps : temp = '".$temp."' <br> \n";
#echo "class_ops.php : getPersonnelFromEncOps : temp : <br> \n"; print_r($temp); echo" <br> \n";
								return unserialize($temp[$personnel_type]);
						} else { return FALSE; }
				} else { return FALSE; }
		}# end of function getPersonnelFromEncOps

				/*
				* Updates operation request info in table 'seg_ops_personell'
				*         to accomodate the changes from OP Room Journal
				* @param Array Data to by reference
				* @created : burn, October 10, 2007
				*/

		function updateOpsPersonellFromNurseJournal($nr=0,$personnel_type=''){
				global $db;

#echo "<br> \n class_ops.php : updateOpsPersonellFromNurseJournal : nr = '".$nr."' <br> \n";
				if (!$nr)
						return;
				#checks if the OP request number ['nr'] from table `care_encounter_op` exists in table 'seg_ops_serv'
				if (!($refno = $this->encOpsNrHasOpsServ($nr)))
						return;   # NO NEED to update

#echo "class_ops.php : updateOpsPersonellFromNurseJournal : refno = '".$refno."' <br> \n";
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : personnel_type = '".$personnel_type."' <br> \n";

				$personnel_type=trim($personnel_type);
						#NOTE : role_type_nr == 7,surgeon; 8,assistant surgeon; 12,anesthesiologist;
						#                9,scrub nurse, 10,rotating nurse
				switch($personnel_type){
						case "operator":
								$role_type_nr = 7;
						break;
						case "assistant":
								$role_type_nr = 8;
						break;
						case "an_doctor":
								$role_type_nr = 12;
						break;
						case "scrub_nurse":
								$role_type_nr = 9;
						break;
						case "rotating_nurse":
								$role_type_nr = 10;
						break;
				}# end of switch function

				if (!$role_type_nr)
						return;
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : role_type_nr = '".$role_type_nr."' <br> \n";

				$data=array();
				$data['refno']=$refno;
				$data['role_type_nr']=$role_type_nr;
				$ok = $this->deleteOpsPersonell($refno," AND role_type_nr='".$role_type_nr."' ");
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : this->sql = '".$this->sql."' <br> \n";
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : ok = '".$ok."' <br> \n";
				$pers_code = $this->getPersonnelFromEncOps($nr,$personnel_type);
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : pers_code = '".$pers_code."' <br> \n";
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : pers_code : <br> \n"; print_r($pers_code); echo" <br> \n";
				if (is_array($pers_code) && !empty($pers_code)){
						$data['pers_code'] = $pers_code;
				}else{
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : pers_code is empty <br> \n";
						return; # NOTHING to insert/update
				}

				$ops_code_charge_obj = $this->getOpsServDetailsInfo($refno);
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : ops_code_charge_obj = '".$ops_code_charge_obj."' <br> \n";
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : ops_code_charge_obj : <br> \n"; print_r($ops_code_charge_obj); echo" <br> \n";
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : this->sql = '".$this->sql."' <br> \n";
				if ($ops_code_charge_obj){
						$ops_code=array();
						$ops_charge=array();
						while($result=$ops_code_charge_obj->FetchRow()){
								$ops_code[] = $result['ops_code'];
								$ops_charge[] = $result['ops_charge'];
						}
				}else{
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : ops_code_charge_obj is empty <br> \n";
						return; # NOTHING to insert/update
				}
/*
echo "class_ops.php : updateOpsPersonellFromNurseJournal : ops_code = '".$ops_code."' <br> \n";
echo "class_ops.php : updateOpsPersonellFromNurseJournal : ops_code : <br> \n"; print_r($ops_code); echo" <br> \n";
echo "class_ops.php : updateOpsPersonellFromNurseJournal : ops_charge = '".$ops_charge."' <br> \n";
echo "class_ops.php : updateOpsPersonellFromNurseJournal : ops_charge : <br> \n"; print_r($ops_charge); echo" <br><br> \n";
*/
				if (is_array($ops_code) && !empty($ops_code)){
						$data['ops_code'] = $ops_code;
						$data['ops_charge'] = $ops_charge;
				}else{
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : ops_code is empty <br> \n";
						return; # NOTHING to insert/update
				}
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : data : <br> \n"; print_r($data); echo" <br> \n";

				if ($this->saveOpsPersonell($data)){
				}else{
#echo "class_ops.php : updateOpsPersonellFromNurseJournal : failed in saving '$personnel_type' personnel <br> \n";
						return FALSE;   # failed in saving '$personnel_type' personnel
				}
				return;
		}#end of function updateOpsPersonellFromNurseJournal

	 /*
		* Updates radiology request info in table 'care_test_request_radio'
		* @param Array Data to by reference
		* @return boolean
		* @created : burn, September 4, 2007
		*/
		function updateRadioRefNoInfoFromArray(&$data){
				global $HTTP_SESSION_VARS, $dbtype;

#    echo "updateRadioRequestInfoFromArray : data = ";
#    print_r ($data);
#    echo " <br> \n";

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
				unset($this->data_array['pcash']);
				unset($this->data_array['pcharge']);


				$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];

#echo "class_ops.php : data['service_code'] : "; print_r($data['service_code']); echo " <br><br> \n";
		$current_list = $this->getListedOpsRequestsByRefNo($data['refno']);
#echo "class_ops.php : current_list : "; print_r($current_list); echo " <br><br> \n";
		$current_deleted_list = $this->getListedOpsRequestsByRefNo($data['refno'],"AND status IN ($this->dead_stat)");
#echo "class_ops.php : current_deleted_list : "; print_r($current_deleted_list); echo " <br><br> \n";
		$update_only_list = array_intersect($data['service_code'],$current_list);
#echo "class_ops.php : update_only_list : "; print_r($update_only_list); echo " <br><br> \n";
		$add_only_list = array_diff($data['service_code'],$current_list);
#echo "class_ops.php : add_only_list 1 : "; print_r($add_only_list); echo " <br><br> \n";
		$update_status_only_list = array_intersect($current_deleted_list,$add_only_list);
#echo "class_ops.php : update_status_only_list 1 : "; print_r($update_status_only_list); echo " <br><br> \n";
		$update_deleted2pending_status_only_list = array_intersect($data['service_code'],$current_deleted_list);
#echo "class_ops.php : update_deleted2pending_status_only_list : "; print_r($update_deleted2pending_status_only_list); echo " <br><br> \n";
		$update_status_only_list = array_unique(array_merge($update_status_only_list,$update_deleted2pending_status_only_list));
#echo "class_ops.php : update_status_only_list 2 : "; print_r($update_status_only_list); echo " <br><br> \n";
		$add_only_list = array_diff($add_only_list,$update_status_only_list);
#echo "class_ops.php : add_only_list 2 : "; print_r($add_only_list); echo " <br><br> \n";
#    $delete_only_list = array_diff($current_list,$_POST['service_code']);
		$delete_only_list = array_diff($current_list,$data['service_code']);
#echo "class_ops.php : delete_only_list : "; print_r($delete_only_list); echo " <br><br> \n";
#exit();
/*
echo "class_ops.php : add_only_list ='".$add_only_list."' <br> \n";
echo "class_ops.php : 1 empty(add_only_list) ".empty($add_only_list)." <br> \n";
echo "class_ops.php : is_array(add_only_list) ".is_array($add_only_list)." <br> \n";
*/
				# Add service codes that are not yet in the 'care_test_request_radio' table
				if (is_array($add_only_list) && !empty($add_only_list)){
#echo "class_ops.php : 2 is_array(add_only_list) ".is_array($add_only_list)." <br> \n";
						$temp_data = $data;
						$temp_serv_code = array();
						$temp_clinical_info = array();
						$temp_request_doctor = array();
						$temp_is_in_house = array();
						$temp_pcash = array();
						$temp_pcharge = array();
						foreach ($add_only_list as $key => $value){
								$orig_key = array_search($value, $data['service_code']);
								array_push($temp_serv_code,$value);
								array_push($temp_clinical_info,$data['clinical_info'][$orig_key]);
								array_push($temp_request_doctor,$data['request_doctor'][$orig_key]);
								array_push($temp_is_in_house,$data['is_in_house'][$orig_key]);
								array_push($temp_pcash,$data['pcash'][$orig_key]);
								array_push($temp_pcharge,$data['pcharge'][$orig_key]);
						}
						$temp_data['service_code'] = $temp_serv_code;
						$temp_data['clinical_info'] = $temp_clinical_info;
						$temp_data['request_doctor'] = $temp_request_doctor;
						$temp_data['is_in_house'] = $temp_is_in_house;
						$temp_data['pcash'] = $temp_pcash;
						$temp_data['pcharge'] = $temp_pcharge;
						$temp_data['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
#echo "class_ops.php : ADD service codes : temp_data : <br>\n"; print_r($temp_data); echo " <br><br> \n";
						$this->saveRadioRequestInfoFromArrayNEW($temp_data);
				}

				# Logical deletion [setting the status to 'delete'] for service codes that are to be deleted
				if (is_array($delete_only_list) && !empty($delete_only_list)){
#echo "<br>class_ops.php : is_array(delete_only_list) ".is_array($delete_only_list)." <br> \n";
						$arrayItems = array();
						foreach ($delete_only_list as $key => $value){
#echo "class_ops.php : DELETE service codes : data['refno']='".$data['refno']."'; value='".$value."' <br> \n";
								$tempArray = array($value);
								array_push($arrayItems,$tempArray);
						}
#echo "class_ops.php :  DELETE service codes : arrayItems : <br>\n"; print_r($arrayItems); echo " <br><br> \n";
						$this->updateRadioRequestStatusByRefNoServCode($data, $arrayItems,'deleted');
				}# end of if-stmt 'if (is_array($delete_only_list))'

				# Change status from 'deleted' to 'pending' for service codes that are re-requested
				if (is_array($update_status_only_list) && !empty($update_status_only_list)){
#echo "<br>class_ops.php : is_array(update_status_only_list) ".is_array($update_status_only_list)." <br> \n";
						$arrayItems = array();
						foreach ($update_status_only_list as $key => $value){
#echo "class_ops.php : UPDATE EXISTING service codes : data['refno']='".$data['refno']."'; value='".$value."' <br> \n";
								$orig_key = array_search($value, $data['service_code']);
								$tempArray = array($data['clinical_info'][$orig_key], $data['request_doctor'][$orig_key],
																				$data['is_in_house'][$orig_key], $data['pcash'][$orig_key],
																				$data['pcharge'][$orig_key], $value);
#                $tempArray = array($value);
								array_push($arrayItems,$tempArray);
						}
#echo "class_ops.php :  UPDATE EXISTING service codes : arrayItems : <br>\n"; print_r($arrayItems); echo " <br><br> \n";
						$this->updateRadioRequestStatusByRefNoServCode($data, $arrayItems,'pending');
				}# end of if-stmt 'if (is_array($delete_only_list))'

				# Update service codes that have been modified and existing in the 'care_test_request_radio' table
				if (is_array($update_only_list) && !empty($update_only_list)){
#echo "<br>class_ops.php : is_array(update_only_list) ".is_array($update_only_list)." <br> \n";
						$arrayItems = array();
						foreach ($update_only_list as $key => $value){
								$orig_key = array_search($value, $data['service_code']);
								$tempArray = array($data['clinical_info'][$orig_key], $data['request_doctor'][$orig_key],
																				$data['is_in_house'][$orig_key], $data['pcash'][$orig_key],
																				$data['pcharge'][$orig_key], $value);
								array_push($arrayItems,$tempArray);
						}
						$this->updateRadioRequestStatusByRefNoServCode($data, $arrayItems);
#echo "class_ops.php : UPDATE service codes : arrayItems : <br>\n"; print_r($arrayItems); echo " <br><br> \n";
						//$this->saveRadioRequestInfoFromArrayNEW($temp_data);
				}

				$this->_useRadioServ();
				if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
						else $concatfx='concat';

						#    Only the keys of data to be updated must be present in the passed array.
				$x='';
				$v='';
				$this->buffer_array = array();
				while(list($x,$v)=each($this->ref_array)) {
#            if(isset($this->data_array[$v])&&(trim($this->data_array[$v])!='')) {
						if (isset($this->data_array[$v]))
								$this->buffer_array[$v]=trim($this->data_array[$v]);
#            }
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
#echo "class_ops.php : updateRadioRequestStatusByRefNoServCode : elems = '".$elems."' <br> \n";
			$this->sql="UPDATE $this->coretable SET $elems, modify_dt=NOW() ".
												" WHERE refno=".$this->data_array['refno']." ";
#echo "class_ops.php :  updateRadioRefNoInfoFromArray : this->sql = '".$this->sql."' <br> \n";
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

#echo "class_ops.php : saveRadioRequestInfoFromArray : data : <br> "; print_r($data); echo " <br> \n";

				$this->_useRequestRadio();
				extract($data);

				$arrayItems = array();
				foreach ($service_code as $key => $value){
						$tempArray = array($value);
						array_push($arrayItems,$tempArray);
				}
#echo "class_ops.php : saveRadioRequestInfoFromArray : arrayItems : <br> "; print_r($arrayItems); echo " <br> \n";
#        "batch_nr","encounter_nr","clinical_info","service_code", "service_date", "is_in_house",
#        "request_doctor", "request_date", "encoder",    "status", "priority", "history",    "create_id", "create_dt"
				$index = "encounter_nr, clinical_info, service_code, is_in_house,
												request_doctor, request_date, encoder,    status, priority, history, create_id, create_dt";
				$values = "$encounter_nr, '$clinical_info', ?, $is_in_house".
												", '$request_doctor', '$request_date', '".$HTTP_SESSION_VARS['sess_user_name'].
												"', 'pending', $priority, '$history', '".$HTTP_SESSION_VARS['sess_user_name']."', NOW()";

				$this->sql = "INSERT INTO $this->coretable ($index)
														VALUES ($values)";

#echo "class_ops.php : saveRadioRequestInfoFromArray : this->sql = '".$this->sql."' <br> \n";

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

#    echo "updateRadioRequestInfoFromArray : data = ";
#    print_r ($data);
#    echo " <br> \n";

				$this->_useRequestRadio();
				$this->data_array=$data;
				// remove probable existing array data to avoid replacing the stored data
				unset($this->data_array['create_id']);
				unset($this->data_array['create_dt']);
				unset($this->data_array['modify_dt']);
				$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];

				if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
						else $concatfx='concat';

						#    Only the keys of data to be updated must be present in the passed array.
				$x='';
				$v='';
				while(list($x,$v)=each($this->ref_array)) {
#            if(isset($this->data_array[$v])&&(trim($this->data_array[$v])!='')) {
								$this->buffer_array[$v]=trim($this->data_array[$v]);
#            }
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
												" WHERE batch_nr=".$this->data_array['batch_nr']." ";
#echo "class_ops.php : updateRadioRequestInfoFromArray : this->sql = '".$this->sql."' <br> \n";

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

#        "batch_nr", "findings",    "findings_date", "doctor_in_charge",
#        "history", "modify_id",    "modify_dt", "create_id", "create_dt"
#        "status", "service_date"   ==> from table 'care_test_request_radio'

				$index = "batch_nr, findings, radio_impression,    findings_date, doctor_in_charge, encoder, history, create_id, create_dt";
				$values = "$batch_nr, '$findings', '$radio_impression', '$findings_date', '$doctor_in_charge', '".
												$HTTP_SESSION_VARS['sess_user_name'].
												"', '$history', '".$HTTP_SESSION_VARS['sess_user_name']."', NOW()";

				$this->sql = "INSERT INTO $this->coretable ($index)
														VALUES ($values)";

#echo "class_ops.php : saveRadioFindingInfoFromArray : this->sql = '".$this->sql."' <br> \n";

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

#echo "class_ops.php : updateRadioFindingInfoFromArray : data : <br>"; print_r($data); echo " <br> \n";
				$this->_useFindingRadio();
#        $data['history']=$this->ConcatHistory($data['mode']." a finding : ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				extract($data);

#        "batch_nr", "findings",    "findings_date", "doctor_in_charge",
#        "history", "modify_id",    "modify_dt", "create_id", "create_dt"
#        "status", "service_date"   ==> from table 'care_test_request_radio'

				$elems="batch_nr = $batch_nr, findings = '$findings', radio_impression ='$radio_impression', ".
										" findings_date = '$findings_date', doctor_in_charge = '$doctor_in_charge', ".
										" encoder = '".$HTTP_SESSION_VARS['sess_user_name']."', ".
										" history = $history, modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."'";
			$this->sql="UPDATE $this->coretable SET $elems, modify_dt=NOW() ".
												" WHERE batch_nr=".$batch_nr." ";

#echo "class_ops.php : updateRadioFindingInfoFromArray : this->sql = '".$this->sql."' <br> \n";

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
				* @param string, teh new finding
				* @param date, date the finding reported
				* @param int, pesonell_nr of the reporting doctor
				* @return boolean
				* @created : burn, Aug 8, 2007
				*/
		function saveAFinding($batch_nr='',$finding_nr='-1', $findings='', $radio_impression='', $findings_date='', $doctor_id='',$mode='Add'){
				global $db,$HTTP_SESSION_VARS;

				$this->_useFindingRadio();
				if(empty($batch_nr) || (!$batch_nr))
						return FALSE;
				if(intval($finding_nr)<0)
						return FALSE;
#echo "class_ops.php : saveAFinding : mode = '".$mode."'<br>\n";
#echo "class_ops.php : saveAFinding : batch_nr = '".$batch_nr."'; finding_nr='".$finding_nr."'; ".
#    " findings='".$findings."'; radio_impression='".$radio_impression."'; findings_date='".$findings_date."'; doctor_id='".$doctor_id."'; mode='".$mode."' <br> \n";

#        "batch_nr", "findings",    "findings_date", "doctor_in_charge",
#        "history", "modify_id",    "modify_dt", "create_id", "create_dt"
#        "status", "service_date"   ==> from table 'care_test_request_radio'
				$this->sql=" SELECT *
												FROM care_test_findings_radio
												WHERE batch_nr='$batch_nr'";

				if ($buf=$db->Execute($this->sql)){
						if($this->count=$buf->RecordCount()) {
								$findingsInfo = $buf->FetchRow();
						}else { return FALSE; }
				}else { return FALSE; }

#echo "class_ops.php : saveAFinding : before : findingsInfo : "; print_r($findingsInfo); echo " <br> \n";

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

#echo "class_ops.php : saveAFinding : findings_array : "; print_r($findings_array); echo " <br> \n";
#echo "class_ops.php : saveAFinding : findings_date_array : "; print_r($findings_date_array); echo " <br> \n";
#echo "class_ops.php : saveAFinding : doctor_in_charge_array : "; print_r($doctor_in_charge_array); echo " <br> \n";
#echo "class_ops.php : saveAFinding : radio_impression_array : "; print_r($radio_impression_array); echo " <br> \n";

						$findingsInfo['findings'] = serialize($findings_array);
						$findingsInfo['findings_date'] = serialize($findings_date_array);
						$findingsInfo['doctor_in_charge'] = serialize($doctor_in_charge_array);
						$findingsInfo['radio_impression'] = serialize($radio_impression_array);
						$findingsInfo['history']=$this->ConcatHistory("$mode a finding : ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");

#echo "class_ops.php : saveAFinding : after : findingsInfo : "; print_r($findingsInfo); echo " <br> \n";
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

#        "batch_nr", "findings",    "findings_date", "doctor_in_charge",
#        "history", "modify_id",    "modify_dt", "create_id", "create_dt"
#        "status", "service_date"   ==> from table 'care_test_request_radio'
				$this->sql=" SELECT *
												FROM care_test_findings_radio
												WHERE batch_nr='$batch_nr'";

				if ($buf=$db->Execute($this->sql)){
						if($this->count=$buf->RecordCount()) {
								$findingsInfo = $buf->FetchRow();
						}else { return FALSE; }
				}else { return FALSE; }

#echo "class_ops.php : deleteAFinding : before : findingsInfo : "; print_r($findingsInfo); echo " <br> \n";

				if (!$findingsInfo){
						return FALSE;   # no data retrieved
				}else{
						$findings_array = unserialize($findingsInfo['findings']);
						$radio_impression_array = unserialize($findingsInfo['radio_impression']);
						$findings_date_array = unserialize($findingsInfo['findings_date']);
						$doctor_in_charge_array = unserialize($findingsInfo['doctor_in_charge']);
/*
echo "class_ops.php : deleteAFinding : findings_array : "; print_r($findings_array); echo " <br> \n";
echo "class_ops.php : deleteAFinding : radio_impression_array : "; print_r($radio_impression_array); echo " <br> \n";
echo "class_ops.php : deleteAFinding : findings_date_array : "; print_r($findings_date_array); echo " <br> \n";
echo "class_ops.php : deleteAFinding : doctor_in_charge_array : "; print_r($doctor_in_charge_array); echo " <br> \n";
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
echo "class_ops.php : deleteAFinding : new_findings_array : "; print_r($new_findings_array); echo " <br> \n";
echo "class_ops.php : deleteAFinding : new_radio_impression_array : "; print_r($new_radio_impression_array); echo " <br> \n";
echo "class_ops.php : deleteAFinding : new_findings_date_array : "; print_r($new_findings_date_array); echo " <br> \n";
echo "class_ops.php : deleteAFinding : new_doctor_in_charge_array : "; print_r($new_doctor_in_charge_array); echo " <br> \n";
*/
						if (empty($new_findings_array)|| (!$new_findings_array)){
								$findingsInfo['status'] = 'pending';
						}
						$findingsInfo['findings'] = serialize($new_findings_array);
						$findingsInfo['radio_impression'] = serialize($new_radio_impression_array);
						$findingsInfo['findings_date'] = serialize($new_findings_date_array);
						$findingsInfo['doctor_in_charge'] = serialize($new_doctor_in_charge_array);
						$findingsInfo['history']=$this->ConcatHistory("Delete a finding : ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");

#echo "class_ops.php : deleteAFinding : after : findingsInfo : "; print_r($findingsInfo); echo " <br> \n";
						return $this->updateRadioFindingInfoFromArray($findingsInfo);
/*
						extract($findingsInfo);
						$elems="batch_nr = $batch_nr, findings = '$findings', findings_date = '$findings_date', ".
												" doctor_in_charge = '$doctor_in_charge', encoder = '".$HTTP_SESSION_VARS['sess_user_name']."', ".
												" history = $history, modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."'";
						$this->sql="UPDATE $this->coretable SET $elems, modify_dt=NOW() ".
														" WHERE batch_nr=".$batch_nr." ";
echo "class_ops.php : deleteAFinding : this->sql = '".$this->sql."' <br> \n";
						if ($db->Execute($this->sql)) {
								if ($db->Affected_Rows()) {
										return TRUE;
								}else{ return FALSE; }
						}else{ return FALSE; }
*/
				}# end of else-stmt
		}# end function deleteAFinding


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
				*   created/modified burn: Oct. 3, 2006
				*/
		function _searchBasicInfoRadioPending($key,$sub_dept_nr='',$enc_class=0,$add_opt='',$limit=FALSE,$len=30,$so=0){
				global $db,$sql_LIKE;
/*
echo "class_ops.php : _searchBasicInfoRadioPending : 1 key ='".$key."' <br> \n ";
$tempSKey = explode("&",$key);
$key = $tempSKey[0];
echo "class_ops.php : _searchBasicInfoRadioPending : tempSKey ='".$tempSKey."' <br> \n ";
echo "class_ops.php : _searchBasicInfoRadioPending : tempSKey : "; print_r($tempSKey); echo" <br> \n ";

echo "class_ops.php : _searchBasicInfoRadioPending : 1 key ='".$key."' <br> \n ";
echo "class_ops.php : _searchBasicInfoRadioPending : sub_dept_nr ='".$sub_dept_nr."' <br> \n ";
*/
				if(is_numeric($key)){
						$key=(int)$key;
#            $whereSQL=" AND r.encounter_nr = $key ";
						$whereSQL=" AND (r_serv.encounter_nr $sql_LIKE '%$key%' OR r.batch_nr $sql_LIKE '%$key%' OR r_serv.pid $sql_LIKE '%$key%') ";
				}elseif($key=='%'||$key=='*'){
						$whereSQL="";
#        }elseif(substr($key, 0, 8)=="dept_nr="){
#        substr_compare ( $key, string str, int offset [, int length [, bool case_sensitivity]])
# Provides: <body text='black'>
#$bodytag = str_replace("%body%", "black", "<body text='%body%'>");
#            $whereSQL="AND $key";
#            $whereSQL="AND ".str_replace("dept_nr=", "r_serv_group.department_nr=", $key);
				}else{
						$whereSQL=" AND (r_serv.encounter_nr $sql_LIKE '%$key%'
												OR r_serv.pid $sql_LIKE '%$key%'
												OR p.pid $sql_LIKE '%$key%'
												OR p.name_last $sql_LIKE '$key%'
												OR p.name_first $sql_LIKE '$key%'
												OR p.date_birth $sql_LIKE '$key%') ";
				}
/*
if (((!empty($tempSKey[1])) || (trim($tempSKey[1])!='')) &&
		(substr($tempSKey[1], 0, 8)=="dept_nr=")){
		$whereSQL.=" AND ".str_replace("dept_nr=", "r_serv_group.department_nr=", $tempSKey[1]);
}*/
if ($sub_dept_nr){
		$whereSQL.=" AND r_serv_group.department_nr=".$sub_dept_nr;
}
/*
				$this->sql="SELECT r.batch_nr, r.encounter_nr, r.request_date,
												r.status, r.priority, r.create_dt,
												r_serv_group.department_nr AS sub_dept_nr,
												dept.id AS sub_dept_id, dept.name_formal AS sub_dept_name,
												p.name_last, p.name_first, p.date_birth, p.sex, p.pid
										FROM $this->tb_enc AS e, $this->tb_person AS p,
																".$this->tb_test_request_radio." AS r
																LEFT JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
																		LEFT JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
																				LEFT JOIN ".$this->tb_dept." AS dept ON r_serv_group.department_nr = dept.nr
										WHERE e.pid=p.pid    AND e.is_discharged IN ('',0)
												AND e.status NOT IN ($this->dead_stat)
												AND r.status<>'done' AND r.status NOT IN ($this->dead_stat) AND e.encounter_nr=r.encounter_nr
												$whereSQL $add_opt ";
#                    WHERE status='pending' OR status='received' ORDER BY  send_date ASC";
					 echo "class_ops.php : _searchBasicInfoRadioPending : this->sql = <br> $this->sql <br> \n ";
exit();
#           echo "class_ops.php : _searchBasicInfoRadioPending : key = $key <br> \n ";
*/

				$this->sql="SELECT r.batch_nr, r.create_dt,
														r_serv.refno, r_serv.request_date, r_serv.encounter_nr, r_serv.pid, r_serv.ordername,
														r_serv.orderaddress, r_serv.is_cash, r_serv.hasPaid, r_serv.is_urgent, r_serv.comments,
														r_serv.history, r.clinical_info, r.service_code,
														r.price_cash, r.price_charge, r.service_date, r.is_in_house, r.request_doctor, r.status,
														IF((ISNULL(r.is_in_house) || r.is_in_house='0'),
																r.request_doctor,
																IF(STRCMP(r.request_doctor,CAST(r.request_doctor AS UNSIGNED INTEGER)),
																		r.request_doctor, fn_get_personell_name(r.request_doctor)) ) AS request_doctor_name,
														r_services.service_code, r_services.name, r_serv_group.group_code ,
														r_serv_group.department_nr AS sub_dept_nr, dept.id AS sub_dept_id, dept.name_formal AS sub_dept_name,
														p.name_last, p.name_first, p.date_birth, p.sex
												FROM seg_radio_serv AS r_serv
														LEFT JOIN care_person AS p ON p.pid = r_serv.pid
														LEFT JOIN care_test_request_radio AS r ON r.refno=r_serv.refno
																LEFT JOIN seg_radio_services AS r_services ON r.service_code = r_services.service_code
																		LEFT JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
																				LEFT JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
												WHERE r_serv.status NOT IN ($this->dead_stat)
														AND r.status NOT IN ($this->dead_stat)
												$whereSQL $add_opt ";
# AND r_serv.hasPaid = 1
#echo "class_ops.php : _searchBasicInfoRadioPending : this->sql = $this->sql <br> \n ";
#exit();
				if($limit){
						$this->res['sabi']=$db->SelectLimit($this->sql,$len,$so);
				}else{
						$this->res['sabi']=$db->Execute($this->sql);
				}
				if ($this->res['sabi']){
						if ($this->record_count=$this->res['sabi']->RecordCount()) {
								$this->rec_count=$this->record_count; # workaround
#                echo "class_ops.php : _searchBasicInfoRadioPending :  TRUE <br>";
								return $this->res['sabi'];
						} else{
#                echo "_searchBasicInfoRadioPending : FALSE 01 <br>";
								return FALSE;
						}
				}else{
#            echo "class_ops.php : _searchBasicInfoRadioPending : FALSE 02 <br>";
						return FALSE;
				}
		}# end of function _searchBasicInfoRadioPending

		/**
		* Limited results search returning basic information as outlined at <var>_searchAdmissionBasicInfo()</var>.
		*
		* This method gives the possibility to sort the results based on an item and sorting direction.
		* @access public
		* @param string Search keyword
		* @param int Maximum number of rows returned
		* @param int Start index of rows returned
		* @param string Item as sort basis
		* @param string Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
		* @return mixed adodb record object or boolean
		*   created/modified burn: Oct. 2, 2006
		*/
#    function searchLimitEncounterBasicInfoPending($key,$len,$so,$sortitem='',$order='ASC'){
		function searchLimitBasicInfoRadioPending($key,$sub_dept_nr,$len,$so,$sortitem='',$order='ASC'){
				if(!empty($sortitem)){
						$option=" ORDER BY $sortitem $order";
				}
				return $this->_searchBasicInfoRadioPending($key,$sub_dept_nr,0,$option,TRUE,$len,$so); // 0 = all kinds of admission
		}# end of function searchLimitBasicInfoRadioPending

				/**
				*   Get the operation requests from `care_encounter_op` table
				*
				*   @access public
				*   @param string Table name
				*   @return boolean OR the list of operation requests from `care_encounter_op` table
				*   created/modified burn: October 1, 2007
				*/
		function _searchBasicEncounterOpInfo($key,$dept_nr='',$add_opt='',$limit=FALSE,$len=30,$so=0){
				global $db,$sql_LIKE,$root_path;

				//note $key value either pid, encounter_nr or name, last, date_birth
				if(is_numeric($key)){
						$key=(int)$key;
						$WHERE_SQL=" AND (p.pid $sql_LIKE '%$key%' OR enc_op.encounter_nr $sql_LIKE '%$key%') ";
				}elseif($key=='%'||$key=='*'){
						$WHERE_SQL="";
				#added by VAN 06-28-08
				}elseif(empty($key)){
						$WHERE_SQL=" AND enc_op.op_date=DATE(NOW())";
				}else{
						$WHERE_SQL=" AND (enc_op.encounter_nr $sql_LIKE '$key%'
												OR p.pid $sql_LIKE '$key%'
												OR p.name_last $sql_LIKE '$key%'
												OR p.name_first $sql_LIKE '$key%'
												OR p.date_birth $sql_LIKE '$key%') ";
				}
				include_once($root_path.'include/inc_date_format_functions.php');
				$date_format=getDateFormat();
						# Check if it is a complete date in mm/dd/yyyy format
				$this_date=@formatDate2STD($key,$date_format);
				if($this_date!='') {
						$WHERE_SQL=" AND (enc_op.op_date='$this_date')";
						#$WHERE_SQL=" AND (enc_op.op_date LIKE '%".$this_date."%')";
				}

				if ($dept_nr){
						$WHERE_SQL.=" AND enc_op.dept_nr=".$dept_nr;
				}

				$ORDER_BY_OPTION = $add_opt;

				$this->_useEncounterOp();

				$this->sql = "
												SELECT fn_get_pid_name(p.pid) AS person_name,
														p.pid, p.name_first, p.name_middle, p.name_last, p.date_birth, p.sex,
														enc_op.nr AS op_request_nr,
														enc_op.*,
														dept.name_short, dept.id AS dept_id, dept.name_formal AS dept_name, dept.name_alternate, dept.description
												FROM $this->coretable AS enc_op
														LEFT JOIN care_department AS dept ON enc_op.dept_nr=dept.nr
														LEFT JOIN care_encounter AS enc ON enc_op.encounter_nr=enc.encounter_nr
																LEFT JOIN care_person AS p ON enc.pid=p.pid
												WHERE enc_op.status NOT IN ($this->dead_stat)
																 $WHERE_SQL
												$ORDER_BY_OPTION
												";
# enc_op.status NOT IN ('inactive','void','hidden','deleted')
#echo "class_ops.php : _searchBasicEncounterOpInfo : this->sql = $this->sql <br> \n ";
#exit();
				if($limit){
						$this->res['sbeoi']=$db->SelectLimit($this->sql,$len,$so);
				}else{
						$this->res['sbeoi']=$db->Execute($this->sql);
				}
				if ($this->res['sbeoi']){
						if ($this->record_count=$this->res['sbeoi']->RecordCount()) {
								$this->rec_count=$this->record_count; # workaround
#                echo " class_ops.php :rec_count =".$this->record_count;
#                echo "class_ops.php : _searchBasicEncounterOpInfo :  TRUE <br>";
								return $this->res['sbeoi'];
						} else{
//                echo "_searchBasicEncounterOpInfo : FALSE 01 <br>";
								return FALSE;
						}
				}else{
#            echo "class_ops.php : _searchBasicEncounterOpInfo : FALSE 02 <br>";
						return FALSE;
				}
		}# end of function _searchBasicEncounterOpInfo


		/**
		* Limited results search returning basic information as outlined at <var>_searchBasicEncounterOpInfo()</var>.
		*
		* This method gives the possibility to sort the results based on an item and sorting direction.
		* @access public
		* @param string Search keyword
		* @param int Department number
		* @param int Maximum number of rows returned
		* @param int Start index of rows returned
		* @param string Item as sort basis
		* @param string Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
		* @return mixed adodb record object or boolean
		* @created/modified :  burn, October 1, 2007
		*/
		function searchLimitBasicEncounterOpInfo($key,$dept_nr='', $len,$so,$sortitem='',$order='ASC'){
				if(!empty($sortitem)){
						$option=" ORDER BY $sortitem $order";
				}
				return $this->_searchBasicEncounterOpInfo($key,$dept_nr,$option,TRUE,$len,$so);
		}# end of function searchLimitBasicEncounterOpInfo

				/**
				*   Get the operation requests from `care_encounter_op` table
				*
				*   @access public
				*   @param string Table name
				*   @return boolean OR the list of operation requests from `care_encounter_op` table
				*   created/modified burn: October 1, 2007
				*/
		function _searchOpsServInfo($key,$dept_nr='',$add_opt='',$limit=FALSE,$len=30,$so=0){
				global $db,$sql_LIKE;

				//note $key value either ref_no, pid, encounter_nr or name, last, date_birth
				if(is_numeric($key)){
						$key=(int)$key;
						$WHERE_SQL=" AND (o_serv.refno $sql_LIKE '%$key%' OR p.pid $sql_LIKE '%$key%' OR enc_op.encounter_nr $sql_LIKE '%$key%') ";
				}elseif($key=='%'||$key=='*'){
						$WHERE_SQL="";
				}else{
						$WHERE_SQL=" AND (o_serv.refno $sql_LIKE '%$key%'
												OR enc_op.encounter_nr $sql_LIKE '$key%'
												OR p.pid $sql_LIKE '$key%'
												OR p.name_last $sql_LIKE '$key%'
												OR p.name_first $sql_LIKE '$key%'
												OR p.date_birth $sql_LIKE '$key%') ";
				}
				if ($sub_dept_nr){
						$WHERE_SQL.=" AND enc_op.dept_nr=".$sub_dept_nr;
				}

				$ORDER_BY_OPTION = $add_opt;

				$this->_useEncounterOp();

				$this->sql = "
												SELECT fn_get_pid_name(p.pid) AS person_name,
														p.pid, p.name_first, p.name_middle, p.name_last, p.date_birth, p.sex,
														o_serv.*,
														enc_op.nr, enc_op.dept_nr, enc_op.op_date,
														dept.name_short, dept.id, dept.name_formal, dept.name_alternate, dept.description
												FROM seg_ops_serv AS o_serv
														LEFT JOIN care_encounter_op AS enc_op ON o_serv.nr=enc_op.nr
																LEFT JOIN care_department AS dept ON enc_op.dept_nr=dept.nr
																LEFT JOIN care_encounter AS enc ON enc_op.encounter_nr=enc.encounter_nr
																		LEFT JOIN care_person AS p ON enc.pid=p.pid
												WHERE enc_op.status NOT IN ($this->dead_stat)
																 $WHERE_SQL
												$ORDER_BY_OPTION
												";
# enc_op.status NOT IN ('inactive','void','hidden','deleted')
#echo "class_ops.php : _searchOpsServInfo : this->sql = $this->sql <br> \n ";
#exit();
				if($limit){
						$this->res['said']=$db->SelectLimit($this->sql,$len,$so);
				}else{
						$this->res['said']=$db->Execute($this->sql);
				}
				if ($this->res['said']){
						if ($this->record_count=$this->res['said']->RecordCount()) {
								$this->rec_count=$this->record_count; # workaround
#                echo " class_ops.php :rec_count =".$this->record_count;
#                echo "class_ops.php : _searchOpsServInfo :  TRUE <br>";
								return $this->res['said'];
						} else{
//                echo "_searchOpsServInfo : FALSE 01 <br>";
								return FALSE;
						}
				}else{
#            echo "class_ops.php : _searchOpsServInfo : FALSE 02 <br>";
						return FALSE;
				}
		}# end of function _searchOpsServInfo


		/**
		* Limited results search returning basic information as outlined at <var>_searchBasicEncounterOpInfo()</var>.
		*
		* This method gives the possibility to sort the results based on an item and sorting direction.
		* @access public
		* @param string Search keyword
		* @param int Department number
		* @param int Maximum number of rows returned
		* @param int Start index of rows returned
		* @param string Item as sort basis
		* @param string Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
		* @return mixed adodb record object or boolean
		* @created/modified :  burn, October 1, 2007
		*/
		function searchLimitOpsServInfo($key,$dept_nr, $len,$so,$sortitem='',$order='ASC'){
				if(!empty($sortitem)){
						$option=" ORDER BY $sortitem $order";
				}
				return $this->_searchOpsServInfo($key,$dept_nr='',$option,TRUE,$len,$so);
		}# end of function searchLimitOpsServInfo

		function getOpNr($dept_nr='', $op_room='', $op_date=''){
				global $db;

				$this->_useEncounterOp();

				$this->sql="SELECT op_nr FROM $this->coretable WHERE dept_nr='$dept_nr' AND op_room='$op_room' AND op_date='$op_date' ORDER BY op_nr DESC";   # burn added: December 20, 2007

#        $ergebnis=$db->Execute($this->sql);
#echo "class_ops.php : getOpNr :: ergebnis ='".$ergebnis."' <br> \n";
#echo "class_ops.php : getOpNr :: ergebnis->RecordCount() ='".$ergebnis->RecordCount()."' <br> \n";
#        if($ergebnis){
				if($ergebnis=$db->Execute($this->sql)){
						if($rows=$ergebnis->RecordCount()){
								$pdata=$ergebnis->FetchRow();
								$op_nr=$pdata['op_nr']+1;
						}else{
								$op_nr=1;
						}
				}else{
						return FALSE;
				}
#echo "class_ops.php : getOpNr :: this->sql ='".$this->sql."' <br> \n";
#echo "class_ops.php : getOpNr :: op_nr ='".$op_nr."' <br> \n";
				return $op_nr;
		}

				/*
				* @param array, personnel IDs
				* @param string, personnel type (operator,assistant,scrub_nurse,rotating_nurse,an_doctor)
				* @created burn: December 20, 2007
				*/
		function serializePersonnelNr($elem,$pers_type){

				$i=1;
				foreach($elem as $key=>$value){
						$tmp_elem[$pers_type.'+'.$i] = $value;
						$i++;
				}
				return serialize($tmp_elem);
		}

				/*
				*    Returns the 'nr' from 'care_encounter_op' table given the 'refno'
				* @param string, reference number
				* @created burn: December 20, 2007
				*/
		function getOpRequestNrByRefNo($refno=''){
				global $db;

				if ((!$refno)||(empty($refno)))
						return FALSE;

				$this->_useEncounterOp();
				$this->sql="SELECT nr FROM $this->coretable WHERE refno='$refno'";

#echo "class_ops.php : getOpRequestNrByRefNo :: this->sql ='".$this->sql."' <br> \n";
#        $ergebnis=$db->Execute($this->sql);
#echo "class_ops.php : getOpRequestNrByRefNo :: ergebnis ='".$ergebnis."' <br> \n";
#echo "class_ops.php : getOpRequestNrByRefNo :: ergebnis->RecordCount() ='".$ergebnis->RecordCount()."' <br> \n";
#        if($ergebnis){
				if($ergebnis=$db->Execute($this->sql)){
						if($rows=$ergebnis->RecordCount()){
								$pdata=$ergebnis->FetchRow();
#echo "class_ops.php : getOpRequestNrByRefNo :: pdata : <br> \n"; print_r($pdata); echo "<br> \n";
#echo "class_ops.php : getOpRequestNrByRefNo :: pdata['nr'] ='".$pdata['nr']."' <br> \n";
								return $pdata['nr'];
						}else{
								return FALSE;
						}
				}
				return FALSE;
		}# end of function getOpRequestNrByRefNo


				/*
				* Inserts the 'nr' from 'care_encounter_op' table to 'nr' in 'seg_ops_serv' table
				* @param string, reference number
				* @param string, 'nr' number from 'care_encounter_op' table
				* @created burn: December 20, 2007
				*/
		function insertOpRequestNr($refno='',$op_request_nr=''){
				global $db;

				if ((!$refno)||(empty($refno)))
						return FALSE;

				$this->_useOpsServ();

				$this->sql="UPDATE $this->coretable ".
												" SET nr = '$op_request_nr'".
												" WHERE refno = '$refno'";
#echo "class_ops.php : insertOpRequestNr :: this->sql ='".$this->sql."' <br> \n";
				if ($db->Execute($this->sql)) {
						if ($db->Affected_Rows()) {
								return TRUE;
						}else{ return FALSE; }
				}else{ return FALSE; }
		}#end of function insertOpRequestNr

		function saveCareEncounterOp(&$data){
				global $db,$sql_LIKE,$HTTP_SESSION_VARS;

				$this->_useEncounterOp();
				extract($data);

				include_once($root_path.'include/inc_date_format_functions.php');

				$date_format = getDateFormat();
						# Check if it is a complete date in mm/dd/yyyy format
				#$op_date=@formatDate2STD($op_date,$date_format);
				$operation_date = date('m-d-Y', strtotime($date_operation));
				$op_date = @formatDate2STD($operation_date, $date_format);

				list($pyear,$pmonth,$pday)=explode('-',$op_date);

				$op_nr = $this->getOpNr($dept_nr,$op_room,$op_date);
				$history = "Created ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";

				$modify_id = $HTTP_SESSION_VARS['sess_user_name'];
				$create_id = $HTTP_SESSION_VARS['sess_user_name'];

				$operator = $this->serializePersonnelNr($surgeon,'operator');
				$assistant = $this->serializePersonnelNr($surgeon_assist,'assistant');
				$scrub_nurse = $this->serializePersonnelNr($nurse_scrub,'scrub_nurse');
				$rotating_nurse = $this->serializePersonnelNr($nurse_rotating,'rotating_nurse');
				$an_doctor = $this->serializePersonnelNr($anesthesiologist,'an_doctor');

#echo "class_ops.php : saveCareEncounterOp :: data : <br>\n"; print_r($data); echo"<br> \n";
$msg = "    op_date = '$op_date' <br> \n
				(pyear,pmonth,pday)=('$pyear','$pmonth','$pday') <br> \n
				op_nr = '$op_nr' <br> \n
				history = '$history' <br> \n
				modify_id = '$modify_id' <br> \n
				create_id = '$create_id' <br> \n
				operator = '$operator'  <br> \n
				assistant = '$assistant'  <br> \n
				scrub_nurse = '$scrub_nurse'  <br> \n
				scrub_nurse = '$rotating_nurse'  <br> \n
				an_doctor = '$an_doctor'  <br> \n ";
#echo "class_ops.php : saveCareEncounterOp :: data : <br>\n"; print_r($data); echo"<br> \n";
#echo "class_ops.php : saveCareEncounterOp :: <br>\n".$msg;

				$index= "refno,year,dept_nr,op_room,op_nr,op_date,op_time,op_src_date,
										encounter_nr,diagnosis,
										operator,assistant,scrub_nurse,rotating_nurse,an_doctor,
										op_therapy,history,modify_id,create_id,create_time";
				$elems="'$refno','$pyear','$dept_nr','$op_room','$op_nr','$op_date','$op_time','".date(Ymd)."',
										'$encounter_nr','".addslashes($diagnosis)."\n',
										'$operator','$assistant','$scrub_nurse','$rotating_nurse','$an_doctor',
										'".addslashes($op_therapy)."\n','$history','$modify_id','$create_id', NOW()";
				$this->sql="INSERT INTO $this->coretable
														($index)
												VALUES
														($elems)";
#echo "class_ops.php : saveCareEncounterOp :: this->sql ='".$this->sql."' <br> \n";
#return FALSE;
//echo $this->sql . '<br/>';
				if ($db->Execute($this->sql)) {
						if ($db->Affected_Rows()) {
								return $this->getOpRequestNrByRefNo($refno);
#                return TRUE;
						}else{ return FALSE; }
				}else{ return FALSE; }
		}# end of function saveCareEncounterOp

		function updateCareEncounterOp(&$data){
				global $db,$sql_LIKE,$HTTP_SESSION_VARS;

				$this->_useEncounterOp();
				extract($data);

				include_once($root_path.'include/inc_date_format_functions.php');
				$date_format=getDateFormat();
						# Check if it is a complete date in mm/dd/yyyy format
				$op_date=@formatDate2STD($date_operation,$date_format);
				list($pyear,$pmonth,$pday)=explode('-',$op_date);

				$history = $this->ConcatHistory("Updated ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				$modify_id = $HTTP_SESSION_VARS['sess_user_name'];

				$operator = $this->serializePersonnelNr($surgeon,'operator');
				$assistant = $this->serializePersonnelNr($surgeon_assist,'assistant');
				$scrub_nurse = $this->serializePersonnelNr($nurse_scrub,'scrub_nurse');
				$rotating_nurse = $this->serializePersonnelNr($nurse_rotating,'rotating_nurse');
				$an_doctor = $this->serializePersonnelNr($anesthesiologist,'an_doctor');


				$elems= "year='$pyear',dept_nr='$dept_nr',op_room='$op_room',op_nr='$op_nr',
										op_date='$op_date',op_time='$op_time',op_src_date='".date(Ymd)."',
										encounter_nr='$encounter_nr',diagnosis='".addslashes($diagnosis)."\n',
										operator='$operator',assistant='$assistant',scrub_nurse='$scrub_nurse',
										rotating_nurse='$rotating_nurse',an_doctor='$an_doctor',
										op_therapy='".addslashes($op_therapy)."\n',history=".$history.",
										modify_id='$modify_id '";

				$this->sql="UPDATE $this->coretable ".
												" SET $elems ".
												" WHERE refno = '$refno'";
#echo "class_ops.php : updateCareEncounterOp :: this->sql ='".$this->sql."' <br> \n";
#return FALSE;

				if ($db->Execute($this->sql)) {
						if ($db->Affected_Rows()) {
								return TRUE;
						}else{ return FALSE; }
				}else{ return FALSE; }
		}#end of function updateCareEncounterOp

				#added by VAN 04-22-08
		function countSearchService($searchkey='',$maxcount=100,$offset=0) {
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				#$suchwort=$searchkey;
				$searchkey = str_replace("^","'",$searchkey);
				$keyword=addslashes($searchkey);

				/*
				$this->sql = "SELECT icpm.code, icpm.description, icpm.rvu, icpm.multiplier
													FROM care_ops301_en AS icpm
												WHERE ( icpm.description REGEXP '[[:<:]]$keyword' OR icpm.code LIKE '%$keyword%' )
												ORDER BY icpm.description ";
				*/

				$this->sql = "SELECT icpm.code, icpm.description, icpm.rvu, icpm.multiplier
													FROM care_ops301_en AS icpm
												WHERE ( icpm.description LIKE '%".$keyword."%'
													OR icpm.code LIKE '%".$keyword."%' )
												ORDER BY icpm.description ";

				#echo "sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		function countSearchOP($searchkey='',$maxcount=100,$offset=0) {
				global $db;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);

				$searchkey = str_replace("^","'",$searchkey);
				$keyword=addslashes($searchkey);

				$this->sql = "select * from seg_ops_rvs ".
										 "   where is_active <> 0 and ".
										 "      (description like '%".$keyword."%' or ".
										 "       code like '%".$keyword."%') ".
										 "   order by description";

				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

	function countCurrentOP($enc_nr, $bill_frmdte, $bill_dt,$maxcount=100,$offset=0, $b_all = false) {
				global $db;

		if ($b_all)
			$this->sql = "select ops_code, description, t.rvu, multiplier, op_charge, group_code, provider
							from
							(select od.ops_code, sum(od.rvu) as rvu, max(od.multiplier) as multiplier, sum(od.rvu * od.multiplier) as op_charge, group_code, 'OR' as provider
								 from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
								 where encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
									and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
								 group by ops_code
							 union
							 select md.ops_code, sum(md.rvu) as rvu, max(md.multiplier) as multiplier, sum(chrg_amnt) as chrg_amnt, group_code, 'OA' as provider
								from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
								where encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									 and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
								group by ops_code) as t inner join seg_ops_rvs as om on t.ops_code = om.code
							order by description";
		else
			$this->sql = "select ops_code, description, max(t.rvu) as rvu, max(multiplier) as multiplier, max(op_charge) as op_charge, group_code, provider
							from
							(select od.ops_code, od.rvu, od.multiplier, (od.rvu * od.multiplier) as op_charge, group_code, 'OR' as provider
								 from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
								 where encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
									and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
									and group_code <> ''
							 union
							 select md.ops_code, md.rvu, md.multiplier, chrg_amnt, group_code, 'OA' as provider
								from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
								where encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									 and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') and group_code <> ''
							 order by rvu desc) as t inner join seg_ops_rvs as om on t.ops_code = om.code
							group by group_code
							union
							select ops_code, description, t.rvu, multiplier, op_charge, group_code, provider
							from
							(select od.ops_code, sum(od.rvu) as rvu, max(od.multiplier) as multiplier, sum(od.rvu * od.multiplier) as op_charge, group_code, 'OR' as provider
								 from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
								 where encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
									and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
									and group_code = ''
								 group by ops_code
							 union
							 select md.ops_code, sum(md.rvu) as rvu, max(md.multiplier) as multiplier, sum(chrg_amnt) as chrg_amnt, group_code, 'OA' as provider
								from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
								where encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									 and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') and group_code = ''
								group by ops_code) as t inner join seg_ops_rvs as om on t.ops_code = om.code
							order by description";

//        $this->sql =  "select od.ops_code as code, description, sum(od.rvu) as rvu, avg(od.multiplier) as multiplier, sum(od.rvu * od.multiplier) as op_charge, 'OR' as provider  ".
//                      "   from (seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno) ".
//                      "      inner join seg_ops_rvs as om on od.ops_code = om.code ".
//                      "   where encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED' ".
//                      "      and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."' ".
//                      "      and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') ".
//                      "   group by od.ops_code, description ".
//                      " union ".
//                      " select md.ops_code, description, sum(md.rvu) as rvu, avg(md.multiplier) as multiplier, sum(md.chrg_amnt) as chrg_amnt, 'OA' as provider ".
//                      "    from (seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno) ".
//                      "       inner join seg_ops_rvs as om on md.ops_code = om.code ".
//                      "    where encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."' ".
//                      "       and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') ".
//                      "   group by md.ops_code, description ".
//                      " order by description";

				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->count;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

	function SearchCurrentOP($enc_nr, $bill_frmdte, $bill_dt,$maxcount=100,$offset=0, $b_all = false){
				global $db;

				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

		if ($b_all)
			$this->sql = "select refno, entry_no, ops_code as code, op_count, description, t.rvu, multiplier, op_charge, group_code, provider, op_date
							from
							(select od.refno, 0 as entry_no, od.ops_code, sum(od.rvu) as rvu, max(od.multiplier) as multiplier, sum(od.rvu * od.multiplier) as op_charge, group_code, 'OR' as provider,
									 (SELECT MAX(ceo.op_date) AS op_date
										FROM seg_ops_serv AS sos INNER JOIN care_encounter_op AS ceo ON sos.refno = ceo.refno
										WHERE sos.refno = os.refno) as op_date,
								 (SELECT COUNT(ops_code) AS op_count FROM seg_ops_servdetails AS od2 WHERE od2.ops_code = od.ops_code AND od2.refno = od.refno) AS op_count
								 from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
								 where encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
									and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
								 group by ops_code
							 union
							 select md.refno, md.entry_no, md.ops_code, sum(md.rvu) as rvu, max(md.multiplier) as multiplier, sum(chrg_amnt) as chrg_amnt, group_code, 'OA' as provider, md.op_date,
								(SELECT COUNT(ops_code) AS op_count FROM seg_misc_ops_details AS md2 WHERE md2.ops_code = md.ops_code AND md2.refno = md.refno) AS op_count
								from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
								where encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									 and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
								group by ops_code) as t inner join seg_ops_rvs as om on t.ops_code = om.code
							order by description LIMIT $offset, $maxcount";
		else
			$this->sql = "select refno, entry_no, ops_code as code, op_count, description, max(t.rvu) as rvu, max(multiplier) as multiplier, max(op_charge) as op_charge, group_code, provider, max(op_date) as op_date
							from
							(select od.refno, 0 as entry_no, od.ops_code, od.rvu, od.multiplier, (od.rvu * od.multiplier) as op_charge, group_code, 'OR' as provider,
									 (SELECT MAX(ceo.op_date) AS op_date
										FROM seg_ops_serv AS sos INNER JOIN care_encounter_op AS ceo ON sos.refno = ceo.refno
										WHERE sos.refno = os.refno) as op_date,
								 (SELECT COUNT(ops_code) AS op_count FROM seg_ops_servdetails AS od2 WHERE od2.ops_code = od.ops_code AND od2.refno = od.refno) AS op_count
								 from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
								 where encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
									and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
									and group_code <> ''
							 union
							 select md.refno, md.entry_no, md.ops_code, md.rvu, md.multiplier, chrg_amnt, group_code, 'OA' as provider, md.op_date,
								(SELECT COUNT(ops_code) AS op_count FROM seg_misc_ops_details AS md2 WHERE md2.ops_code = md.ops_code AND md2.refno = md.refno) AS op_count
								from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
								where encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									 and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') and group_code <> ''
							 order by rvu desc) as t inner join seg_ops_rvs as om on t.ops_code = om.code
							group by group_code
							union
							select refno, entry_no, ops_code, op_count, description, t.rvu, multiplier, op_charge, group_code, provider, op_date
							from
							(select od.refno, 0 as entry_no, od.ops_code, sum(od.rvu) as rvu, max(od.multiplier) as multiplier, sum(od.rvu * od.multiplier) as op_charge, group_code, 'OR' as provider,
									 (SELECT MAX(ceo.op_date) AS op_date
										FROM seg_ops_serv AS sos INNER JOIN care_encounter_op AS ceo ON sos.refno = ceo.refno
										WHERE sos.refno = os.refno) as op_date,
								 (SELECT COUNT(ops_code) AS op_count FROM seg_ops_servdetails AS od2 WHERE od2.ops_code = od.ops_code AND od2.refno = od.refno) AS op_count
								 from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
								 where encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
									and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
									and group_code = ''
								 group by ops_code
							 union
							 select md.refno, md.entry_no, md.ops_code, sum(md.rvu) as rvu, max(md.multiplier) as multiplier, sum(chrg_amnt) as chrg_amnt, group_code, 'OA' as provider, md.op_date,
								(SELECT COUNT(ops_code) AS op_count FROM seg_misc_ops_details AS md2 WHERE md2.ops_code = md.ops_code AND md2.refno = md.refno) AS op_count
								from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
								where encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									 and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') and group_code = ''
								group by ops_code) as t inner join seg_ops_rvs as om on t.ops_code = om.code
							order by description LIMIT $offset, $maxcount";

//        $this->sql =  "select od.ops_code as code, description, sum(od.rvu) as rvu, avg(od.multiplier) as multiplier, sum(od.rvu * od.multiplier) as op_charge, group_code, 'OR' as provider  ".
//                      "   from (seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno) ".
//                      "      inner join seg_ops_rvs as om on od.ops_code = om.code ".
//                      "   where encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED' ".
//                      "      and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."' ".
//                      "      and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') ".
//                      "   group by od.ops_code, description ".
//                      " union ".
//                      " select md.ops_code, description, sum(md.rvu) as rvu, avg(md.multiplier) as multiplier, sum(md.chrg_amnt) as chrg_amnt, group_code, 'OA' as provider ".
//                      "    from (seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno) ".
//                      "       inner join seg_ops_rvs as om on md.ops_code = om.code ".
//                      "    where encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."' ".
//                      "       and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') ".
//                      "   group by md.ops_code, description ".
//                      " order by description LIMIT $offset, $maxcount";

//        if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
				if($this->res['ssl']=$db->Execute($this->sql)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return FALSE;}
				}else{return FALSE;}
		}

	function SearchOpsForForm2($enc_nr, $bill_frmdte, $bill_dt, $maxcount=100, $offset=0){
		global $db;

		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		$this->sql = "select t.refno, t.entry_no, ops_code as code, op_count, (case when seo.description is null then om.description else seo.description end) as description, max(t.rvu) as rvu, max(multiplier) as multiplier, max(op_charge) as op_charge, group_code, provider, max(op_date) as op_date
						from
						(select od.refno, 0 as entry_no, od.ops_code, od.rvu, od.multiplier, (od.rvu * od.multiplier) as op_charge, group_code, _latin1'OR' COLLATE latin1_swedish_ci AS provider,
								 (SELECT MAX(ceo.op_date) AS op_date
									FROM seg_ops_serv AS sos INNER JOIN care_encounter_op AS ceo ON sos.refno = ceo.refno
									WHERE sos.refno = os.refno) as op_date,
							 (SELECT COUNT(ops_code) AS op_count FROM seg_ops_servdetails AS od2 WHERE od2.ops_code = od.ops_code AND od2.refno = od.refno) AS op_count
							 from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
							 where encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
								and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
								and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
								and group_code <> ''
						 union
						 select md.refno, md.entry_no, md.ops_code, md.rvu, md.multiplier, chrg_amnt, group_code, _latin1'OA' COLLATE latin1_swedish_ci AS provider, md.op_date,
							(SELECT COUNT(ops_code) AS op_count FROM seg_misc_ops_details AS md2 WHERE md2.ops_code = md.ops_code AND md2.refno = md.refno) AS op_count
							from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
							where encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
								 and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') and group_code <> ''
						 order by rvu desc) as t inner join seg_ops_rvs as om on t.ops_code = om.code
						 LEFT JOIN seg_encounter_ops AS seo ON seo.code = t.ops_code AND seo.refno = t.refno AND seo.entry_no = t.entry_no AND seo.source = t.provider AND seo.is_deleted = 0
						group by group_code
						union
						select t.refno, t.entry_no, ops_code, op_count, (case when seo.description is null then om.description else seo.description end) as description, t.rvu, multiplier, op_charge, group_code, provider, op_date
						from
						(select od.refno, 0 as entry_no, od.ops_code, sum(od.rvu) as rvu, max(od.multiplier) as multiplier, sum(od.rvu * od.multiplier) as op_charge, group_code, _latin1'OR' COLLATE latin1_swedish_ci AS provider,
								 (SELECT MAX(ceo.op_date) AS op_date
									FROM seg_ops_serv AS sos INNER JOIN care_encounter_op AS ceo ON sos.refno = ceo.refno
									WHERE sos.refno = os.refno) as op_date,
							 (SELECT COUNT(ops_code) AS op_count FROM seg_ops_servdetails AS od2 WHERE od2.ops_code = od.ops_code AND od2.refno = od.refno) AS op_count
							 from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
							 where encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
								and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
								and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
								and group_code = ''
							 group by ops_code
						 union
						 select md.refno, md.entry_no, md.ops_code, sum(md.rvu) as rvu, max(md.multiplier) as multiplier, sum(chrg_amnt) as chrg_amnt, group_code, _latin1'OA' COLLATE latin1_swedish_ci AS provider, md.op_date,
							(SELECT COUNT(ops_code) AS op_count FROM seg_misc_ops_details AS md2 WHERE md2.ops_code = md.ops_code AND md2.refno = md.refno) AS op_count
							from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
							where encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
								 and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') and group_code = ''
							group by ops_code) as t inner join seg_ops_rvs as om on t.ops_code = om.code
							LEFT JOIN seg_encounter_ops AS seo ON seo.code = t.ops_code AND seo.refno = t.refno AND seo.entry_no = t.entry_no AND seo.source = t.provider AND seo.is_deleted = 0
						order by description LIMIT $offset, $maxcount";

		if($this->res['ssl']=$db->Execute($this->sql)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return FALSE;}
		}else{return FALSE;}
	}

//    function SearchParentOP($enc_nr, $bill_dt, $cur_ops_code, $maxcount=100,$offset=0) {
//        global $db;
//
//        if(empty($maxcount)) $maxcount=100;
//        if(empty($offset)) $offset=0;
//
//        $this->sql =  "select od.ops_code as code, description, sum(od.rvu) as rvu, avg(od.multiplier) as multiplier, sum(od.rvu * od.multiplier) as op_charge, 'OR' as provider  ".
//                      "   from (seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno) ".
//                      "      inner join seg_ops_rvs as om on od.ops_code = om.code ".
//                      "   where parent_ops_code = '' and od.ops_code <> '$cur_ops_code' and encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED' ".
//                      "      and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."' ".
//                      "      and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') ".
//                      "   group by od.ops_code, description ".
//                      " union ".
//                      " select md.ops_code, description, sum(md.rvu) as rvu, avg(md.multiplier) as multiplier, sum(md.chrg_amnt) as chrg_amnt, 'OA' as provider ".
//                      "    from (seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno) ".
//                      "       inner join seg_ops_rvs as om on md.ops_code = om.code ".
//                      "    where parent_ops_code = '' and od.ops_code <> '$cur_ops_code' and encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."' ".
//                      "       and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') ".
//                      "   group by md.ops_code, description ".
//                      " order by description LIMIT $offset, $maxcount";

//        $this->sql =  "select od.ops_code as code, description, sum(od.rvu) as rvu, avg(od.multiplier) as multiplier, sum(od.rvu * od.multiplier) as op_charge, 'OR' as provider  ".
//                      "   from (seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno) ".
//                      "      inner join seg_ops_rvs as om on od.ops_code = om.code ".
//                      "   where parent_ops_code = '' and od.ops_code <> '$cur_ops_code' and encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED' ".
//                      "      and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') ".
//                      "   group by od.ops_code, description ".
//                      " union ".
//                      " select md.ops_code, description, sum(md.rvu) as rvu, avg(md.multiplier) as multiplier, sum(md.chrg_amnt) as chrg_amnt, 'OA' as provider ".
//                      "    from (seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno) ".
//                      "       inner join seg_ops_rvs as om on md.ops_code = om.code ".
//                      "    where parent_ops_code = '' and od.ops_code <> '$cur_ops_code' and encounter_nr = '". $enc_nr. "' ".
//                      "       and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') ".
//                      "   group by md.ops_code, description ".
//                      " order by description LIMIT $offset, $maxcount";
//
//        if($this->res['ssl']=$db->Execute($this->sql)){
//            if($this->rec_count=$this->res['ssl']->RecordCount()) {
//                return $this->res['ssl'];
//            }else{return FALSE;}
//        }else{return FALSE;}
//    }

		function countAppliedOP($enc_nr='',$searchkey='',$maxcount=100,$offset=0,$b_drchrg=0, $dr_nr=0) {
				global $db;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);

				$searchkey = str_replace("^","'",$searchkey);
				$keyword=addslashes($searchkey);

				if ($b_drchrg == 1) {
			$this->sql = "select refno, ops_code,
									 (select description from seg_ops_rvs as t3
												 where t3.code = t.ops_code
													and description like '%".$keyword."%') as description,
									 max(t.rvu) as rvu, max(multiplier) as multiplier, group_code, entry_no,
									 (select ifnull(count(*), 0) as count from seg_ops_chrg_dr as soca
										 where soca.ops_refno = t.refno and
											soca.ops_code = t.ops_code
											and dr_nr = ".$dr_nr.") as bselected
							from
							(select sosd.refno, sosd.ops_code, ifnull(soca.rvu, sosd.rvu) as rvu, ifnull(soca.multiplier, sosd.multiplier) as multiplier, group_code, 0 as entry_no,
									if(soca.ops_refno is null, 0, 1) as bselected
								 from (seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno)
									left join seg_ops_chrg_dr as soca on soca.ops_refno = sosd.refno and soca.ops_code = sosd.ops_code and ops_entryno = 0 and dr_nr = ".$dr_nr."
								 where sos.encounter_nr = '".$enc_nr."' and
									sosd.ops_code like '%".$keyword."%'
							 union
							select smod.refno, smod.ops_code, ifnull(soca.rvu, smod.rvu) as rvu, ifnull(soca.multiplier,smod.multiplier) as multiplier, group_code, smod.entry_no,
									if(soca.ops_refno is null, 0, 1) as bselected
								 from (seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno)
									left join seg_ops_chrg_dr as soca on soca.ops_refno = smod.refno and soca.ops_code = smod.ops_code and ops_entryno = smod.entry_no and dr_nr = ".$dr_nr."
								 where smo.encounter_nr = '".$enc_nr."' and
									smod.ops_code like '%".$keyword."%'
								 order by rvu desc) as t
							group by group_code having group_code <> ''
							union
							select refno, ops_code, description, rvu, multiplier, group_code, entry_no, bselected
							from
							(select sosd.refno, sosd.ops_code, description, ifnull(soca.rvu, sosd.rvu) as rvu, ifnull(soca.multiplier, sosd.multiplier) as multiplier, group_code, 0 as entry_no,
									if(soca.ops_refno is null, 0, 1) as bselected
								 from ((seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno)
									inner join seg_ops_rvs as sor on sosd.ops_code = sor.code)
									left join seg_ops_chrg_dr as soca on soca.ops_refno = sosd.refno and soca.ops_code = sosd.ops_code and ops_entryno = 0 and dr_nr = ".$dr_nr."
								 where sos.encounter_nr = '".$enc_nr."' and
									(description like '%".$keyword."%' or
									 sosd.ops_code like '%".$keyword."%')
							 union
							select smod.refno, smod.ops_code, description, ifnull(soca.rvu, smod.rvu) as rvu, ifnull(soca.multiplier,smod.multiplier) as multiplier, group_code, smod.entry_no,
									if(soca.ops_refno is null, 0, 1) as bselected
								 from ((seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno)
									inner join seg_ops_rvs as sor on smod.ops_code = sor.code)
									left join seg_ops_chrg_dr as soca on soca.ops_refno = smod.refno and soca.ops_code = smod.ops_code and ops_entryno = smod.entry_no and dr_nr = ".$dr_nr."
								 where smo.encounter_nr = '".$enc_nr."' and
									(description like '%".$keyword."%' or
									 smod.ops_code like '%".$keyword."%')
								 order by description) as t
							where group_code = '' order by description";

//                "select refno, ops_code, description, rvu, multiplier, group_code, entry_no, bselected
//                              from ".
//                             "(select sosd.refno, sosd.ops_code, description, ifnull(soca.rvu, sosd.rvu) as rvu, ifnull(soca.multiplier, sosd.multiplier) as multiplier, 0 as entry_no, ".
//                             "      if(soca.ops_refno is null, 0, 1) as bselected ".
//                             "   from ((seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno) ".
//                             "      left join seg_ops_chrg_dr as soca on soca.ops_refno = sosd.refno and soca.ops_code = sosd.ops_code and ops_entryno = 0 and dr_nr = ".$dr_nr.") ".
//                             "      inner join seg_ops_rvs as sor on sosd.ops_code = sor.code ".
//                             "   where sos.encounter_nr = '".$enc_nr."' and parent_ops_code = '' and ".
//                             "      (description like '%".$keyword."%' or ".
//                             "       sosd.ops_code like '%".$keyword."%') ".
//                             " union ".
//                             "select smod.refno, smod.ops_code, description, ifnull(soca.rvu, smod.rvu) as rvu, ifnull(soca.multiplier,smod.multiplier) as multiplier, smod.entry_no, ".
//                             "      if(soca.ops_refno is null, 0, 1) as bselected ".
//                             "   from ((seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno) ".
//                             "      left join seg_ops_chrg_dr as soca on soca.ops_refno = smod.refno and soca.ops_code = smod.ops_code and ops_entryno = smod.entry_no and dr_nr = ".$dr_nr.") ".
//                             "      inner join seg_ops_rvs as sor on smod.ops_code = sor.code ".
//                             "   where smo.encounter_nr = '".$enc_nr."' and parent_ops_code = '' and ".
//                             "      (description like '%".$keyword."%' or ".
//                            "       smod.ops_code like '%".$keyword."%') ".
//                             "   order by description";
		}
		else {
			$this->sql = "select refno, ops_code,
									 (select description from seg_ops_rvs as t3
												 where t3.code = t.ops_code
													and description like '%".$keyword."%') as description,
									 max(t.rvu) as rvu, max(multiplier) as multiplier, group_code, entry_no,
									 (select ifnull(count(*), 0) as count from seg_ops_chrgd_accommodation as soca
										 where soca.ops_refno = t.refno and
											soca.ops_code = t.ops_code) as bselected
							from
							(select sosd.refno, sosd.ops_code, sosd.rvu, sosd.multiplier, group_code, 0 as entry_no
								 from seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno
								 where sos.encounter_nr = '".$enc_nr."' and
									sosd.ops_code like '%".$keyword."%'
							 union
							select smod.refno, smod.ops_code, smod.rvu, smod.multiplier, group_code, smod.entry_no
								 from seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno
								 where smo.encounter_nr = '".$enc_nr."' and
									smod.ops_code like '%".$keyword."%' order by rvu desc) as t
							group by group_code having group_code <> ''
							union
							select refno, ops_code, description, rvu, multiplier, group_code, entry_no, bselected
							from ".
							 "(select sosd.refno, sosd.ops_code, description, sosd.rvu, sosd.multiplier, group_code, 0 as entry_no, ".
							 "   (select ifnull(count(*), 0) as count from seg_ops_chrgd_accommodation as soca ".
							 "       where soca.ops_refno = sosd.refno and soca.ops_code = sosd.ops_code and ops_entryno = 0) as bselected ".
							 "   from (seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno) ".
														 "      inner join seg_ops_rvs as sor on sosd.ops_code = sor.code ".
														 "   where sos.encounter_nr = '".$enc_nr."' and ".
														 "      (description like '%".$keyword."%' or ".
														 "       sosd.ops_code like '%".$keyword."%') ".
														 " union ".
							 "select smod.refno, smod.ops_code, description, smod.rvu, smod.multiplier, group_code, smod.entry_no, ".
							 "   (select ifnull(count(*), 0) as count from seg_ops_chrgd_accommodation as soca ".
							 "       where soca.ops_refno = smod.refno and soca.ops_code = smod.ops_code and ops_entryno = smod.entry_no) as bselected ".
							 "   from (seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno) ".
														 "      inner join seg_ops_rvs as sor on smod.ops_code = sor.code ".
														 "   where smo.encounter_nr = '".$enc_nr."' and ".
														 "      (description like '%".$keyword."%' or ".
														 "       smod.ops_code like '%".$keyword."%') ".
							 "   order by description) as t
								where group_code = '' order by description";

//                $this->sql = "select sosd.refno, sosd.ops_code, description, sosd.rvu, sosd.multiplier, 0 as entry_no, ".
//                         "   (select ifnull(count(*), 0) as count from seg_ops_chrgd_accommodation as soca ".
//                             "       where soca.ops_refno = sosd.refno and soca.ops_code = sosd.ops_code and ops_entryno = 0) as bselected ".
//                             "   from (seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno) ".
//                             "   inner join seg_ops_rvs as sor on sosd.ops_code = sor.code ".
//                             "   where sos.encounter_nr = '".$enc_nr."' and ".
//                             "      (description like '%".$keyword."%' or ".
//                             "       sosd.ops_code like '%".$keyword."%') ".
//                             " union ".
//                             "select smod.refno, smod.ops_code, description, smod.rvu, smod.multiplier, smod.entry_no, ".
//                         "   (select ifnull(count(*), 0) as count from seg_ops_chrgd_accommodation as soca ".
//                             "       where soca.ops_refno = smod.refno and soca.ops_code = smod.ops_code and ops_entryno = smod.entry_no) as bselected ".
//                             "   from (seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno) ".
//                             "   inner join seg_ops_rvs as sor on smod.ops_code = sor.code ".
//                             "   where smo.encounter_nr = '".$enc_nr."' and ".
//                             "      (description like '%".$keyword."%' or ".
//                             "       smod.ops_code like '%".$keyword."%') ".
//                             "   order by description";
				}

				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		/***
		* Fix for Bugzilla bug 68
		*
		*/
		function countOPForApplication() {
			global $db;
			if ($this->result=$db->Execute($this->sql)) {
					$this->count=$this->result->RecordCount();
					return $this->count;
			}
			else{return 0;}
		}

		function SearchService($searchkey='',$maxcount=100,$offset=0){
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				#$suchwort=$searchkey;
				$searchkey = str_replace("^","'",$searchkey);
				$keyword=addslashes($searchkey);

				$this->sql = "SELECT icpm.code, icpm.description, icpm.rvu, icpm.multiplier
													FROM care_ops301_en AS icpm
												WHERE ( icpm.description LIKE '%".$keyword."%'
													OR icpm.code LIKE '%".$keyword."%' )
												ORDER BY icpm.description ";

				if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
				}else{return false;}
		}

		function isHouseCase($enc_nr) {
			global $db;

			$case = '';
			$sql = "select st.casetype_desc from seg_encounter_case sc
										inner join seg_type_case st on sc.casetype_id = st.casetype_id ".
						 "   where encounter_nr = '".$enc_nr."' ".
						 "   order by sc.modify_dt desc limit 1";

			if($result = $db->Execute($sql)){
					if($result->RecordCount()){
							if ($row = $result->FetchRow()) {
								$case = $row['casetype_desc'];
							}
					}
			}

			return !(strpos($case, 'HOUSE') === false);
		}

		function SearchOP($searchkey='',$maxcount=100,$offset=0, $enc_nr='') {
				global $db;

				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				$hospObj = new Hospital_Admin();
				if ($this->isHouseCase($enc_nr))
					$nPCF = HOUSE_CASE_PCF;
				else
					$nPCF = $hospObj->getDefinedPCF();

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				$searchkey = str_replace("^","'",$searchkey);
				$keyword=addslashes($searchkey);

				$this->sql = "select distinct code, description, rvu, ".$nPCF." as multiplier ".
										 "   from seg_ops_rvs ".
										 "   where is_active <> 0 and ".
										 "      (description like '%".$keyword."%' or ".
										 "       code like '%".$keyword."%') ".
										 "   order by description";

				if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
				}else{return false;}
		}

		// Added by LST - 11.21.2008
		function SearchAppliedOP($enc_nr='',$searchkey='',$maxcount=100,$offset=0,$b_drchrg=0, $dr_nr=0, $b_all=0){
				global $db;

				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;
				if(empty($b_drchrg)) $b_drchrg = 0;

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				$searchkey = str_replace("^","'",$searchkey);
				$keyword=addslashes($searchkey);

				if ($b_drchrg == 1) {
					if ($b_all) {
					 $this->sql = "select refno, ops_code,
											 (select description from seg_ops_rvs as t3
														 where t3.code = t.ops_code
															and description like '%".$keyword."%') as description, op_date,
												t.rvu as rvu, multiplier, group_code, entry_no,
											 (select ifnull(count(*), 0) as count from seg_ops_chrg_dr as soca
												 where soca.ops_refno = t.refno and
													soca.ops_code = t.ops_code
													and dr_nr = ".$dr_nr.") as bselected
									from
									(select sosd.refno, sosd.ops_code, ifnull(soca.rvu, sosd.rvu) as rvu, ifnull(soca.multiplier, sosd.multiplier) as multiplier, group_code, 0 as entry_no,
											if(soca.ops_refno is null, 0, 1) as bselected,
									   (SELECT MAX(ceo.op_date) op_date
									       FROM care_encounter_op ceo
									       WHERE ceo.refno = sos.refno) op_date
										 from ((seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno)
											inner join seg_ops_rvs as sor on sosd.ops_code = sor.code)
											left join seg_ops_chrg_dr as soca on soca.ops_refno = sosd.refno and soca.ops_code = sosd.ops_code and ops_entryno = 0 and dr_nr = ".$dr_nr."
										 where sos.encounter_nr = '".$enc_nr."' and
											(description like '%".$keyword."%' or
											 sosd.ops_code like '%".$keyword."%')
									 union
									select smod.refno, smod.ops_code, ifnull(soca.rvu, smod.rvu) as rvu, ifnull(soca.multiplier,smod.multiplier) as multiplier, group_code, smod.entry_no,
											if(soca.ops_refno is null, 0, 1) as bselected, smod.op_date
										 from ((seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno)
											inner join seg_ops_rvs as sor on smod.ops_code = sor.code)
											left join seg_ops_chrg_dr as soca on soca.ops_refno = smod.refno and soca.ops_code = smod.ops_code and ops_entryno = smod.entry_no and dr_nr = ".$dr_nr."
										 where smo.encounter_nr = '".$enc_nr."' and
											(sor.description like '%".$keyword."%' or
											 smod.ops_code like '%".$keyword."%')) as t
									order by description";
					}
					else {
					 $this->sql = "select refno, ops_code,
											 (select description from seg_ops_rvs as t3
														 where t3.code = t.ops_code
															and description like '%".$keyword."%') as description, op_date,
											 max(t.rvu) as rvu, max(multiplier) as multiplier, group_code, entry_no,
											 (select ifnull(count(*), 0) as count from seg_ops_chrg_dr as soca
												 where soca.ops_refno = t.refno and
													soca.ops_code = t.ops_code
													and dr_nr = ".$dr_nr.") as bselected
									from
									(select sosd.refno, sosd.ops_code, ifnull(soca.rvu, sosd.rvu) as rvu, ifnull(soca.multiplier, sosd.multiplier) as multiplier, group_code, 0 as entry_no,
											 if(soca.ops_refno is null, 0, 1) as bselected,
										   (SELECT MAX(ceo.op_date) op_date
										       FROM care_encounter_op ceo
										       WHERE ceo.refno = sos.refno) op_date
										 from (seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno)
											left join seg_ops_chrg_dr as soca on soca.ops_refno = sosd.refno and soca.ops_code = sosd.ops_code and ops_entryno = 0 and dr_nr = ".$dr_nr."
										 where sos.encounter_nr = '".$enc_nr."' and
											sosd.ops_code like '%".$keyword."%'
									 union
									select smod.refno, smod.ops_code, ifnull(soca.rvu, smod.rvu) as rvu, ifnull(soca.multiplier,smod.multiplier) as multiplier, group_code, smod.entry_no,
											if(soca.ops_refno is null, 0, 1) as bselected, smod.op_date
										 from (seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno)
											left join seg_ops_chrg_dr as soca on soca.ops_refno = smod.refno and soca.ops_code = smod.ops_code and ops_entryno = smod.entry_no and dr_nr = ".$dr_nr."
										 where smo.encounter_nr = '".$enc_nr."' and
											smod.ops_code like '%".$keyword."%' order by rvu desc) as t
									group by group_code having group_code <> ''
									union
									select refno, ops_code, description, op_date, rvu, multiplier, group_code, entry_no, bselected
									from
									(select sosd.refno, sosd.ops_code, description, ifnull(soca.rvu, sosd.rvu) as rvu, ifnull(soca.multiplier, sosd.multiplier) as multiplier, group_code, 0 as entry_no,
											 if(soca.ops_refno is null, 0, 1) as bselected,
										   (SELECT MAX(ceo.op_date) op_date
										       FROM care_encounter_op ceo
										       WHERE ceo.refno = sos.refno) op_date
										 from ((seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno)
											inner join seg_ops_rvs as sor on sosd.ops_code = sor.code)
											left join seg_ops_chrg_dr as soca on soca.ops_refno = sosd.refno and soca.ops_code = sosd.ops_code and ops_entryno = 0 and dr_nr = ".$dr_nr."
										 where sos.encounter_nr = '".$enc_nr."' and
											(description like '%".$keyword."%' or
											 sosd.ops_code like '%".$keyword."%')
									 union
									select smod.refno, smod.ops_code, sor.description, ifnull(soca.rvu, smod.rvu) as rvu, ifnull(soca.multiplier,smod.multiplier) as multiplier, group_code, smod.entry_no,
											if(soca.ops_refno is null, 0, 1) as bselected, smod.op_date
										 from ((seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno)
											inner join seg_ops_rvs as sor on smod.ops_code = sor.code)
											left join seg_ops_chrg_dr as soca on soca.ops_refno = smod.refno and soca.ops_code = smod.ops_code and ops_entryno = smod.entry_no and dr_nr = ".$dr_nr."
										 where smo.encounter_nr = '".$enc_nr."' and
											(sor.description like '%".$keyword."%' or
											 smod.ops_code like '%".$keyword."%')
										 order by description) as t
									where group_code = '' order by description";
					}

//                $this->sql = "select distinct sosd.refno, sosd.ops_code, description, ifnull(soca.rvu, sosd.rvu) as rvu, ifnull(soca.multiplier, sosd.multiplier) as multiplier, 0 as entry_no, ".
//                             "      if(soca.ops_refno is null, 0, 1) as bselected ".
//                             "   from ((seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno) ".
//                             "      left join seg_ops_chrg_dr as soca on soca.ops_refno = sosd.refno and soca.ops_code = sosd.ops_code and ops_entryno = 0 and dr_nr = ".$dr_nr.") ".
//                             "   inner join seg_ops_rvs as sor on sosd.ops_code = sor.code ".
//                             "   where sos.encounter_nr = '".$enc_nr."' and parent_ops_code = '' and ".
//                             "      (description like '%".$keyword."%' or ".
//                             "       sosd.ops_code like '%".$keyword."%') ".
//                             " union ".
//                             "select distinct smod.refno, smod.ops_code, description, ifnull(soca.rvu, smod.rvu) as rvu, ifnull(soca.multiplier,smod.multiplier) as multiplier, smod.entry_no, ".
//                             "      if(soca.ops_refno is null, 0, 1) as bselected ".
//                             "   from ((seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno) ".
//                             "      left join seg_ops_chrg_dr as soca on soca.ops_refno = smod.refno and soca.ops_code = smod.ops_code and ops_entryno = smod.entry_no and dr_nr = ".$dr_nr.") ".
//                             "   inner join seg_ops_rvs as sor on smod.ops_code = sor.code ".
//                             "   where smo.encounter_nr = '".$enc_nr."' and parent_ops_code = '' and ".
//                             "      (description like '%".$keyword."%' or ".
//                             "       smod.ops_code like '%".$keyword."%') ".
//                             "   order by description";

				}
				else {
					if ($b_all) {
						$this->sql = "select refno, ops_code,
												 (select description from seg_ops_rvs as t3
															 where t3.code = t.ops_code
																and description like '%".$keyword."%') as description, op_date,
													t.rvu as rvu, multiplier, group_code, entry_no,
												 (select ifnull(count(*), 0) as count from seg_ops_chrgd_accommodation as soca
													 where soca.ops_refno = t.refno and
														soca.ops_code = t.ops_code) as bselected
										from ".
										 "(select sosd.refno, sosd.ops_code, description, sosd.rvu, sosd.multiplier, group_code, 0 as entry_no, ".
															 "   (select ifnull(count(*), 0) as count from seg_ops_chrgd_accommodation as soca ".
															 "       where soca.ops_refno = sosd.refno and soca.ops_code = sosd.ops_code and ops_entryno = 0) as bselected, ".
															 "   (SELECT MAX(ceo.op_date) op_date
															       FROM care_encounter_op ceo
															       WHERE ceo.refno = sos.refno) op_date ".
															 "   from (seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno) ".
															 "   inner join seg_ops_rvs as sor on sosd.ops_code = sor.code ".
															 "   where sos.encounter_nr = '".$enc_nr."' and ".
															 "      (description like '%".$keyword."%' or ".
															 "       sosd.ops_code like '%".$keyword."%') ".
															 " union ".
										 "select smod.refno, smod.ops_code, sor.description, smod.rvu, smod.multiplier, group_code, smod.entry_no, ".
															 "      (select ifnull(count(*), 0) as count from seg_ops_chrgd_accommodation as soca ".
															 "          where soca.ops_refno = smod.refno and soca.ops_code = smod.ops_code and ops_entryno = smod.entry_no) as bselected, ".
															 "       smod.op_date ".
															 "   from (seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno) ".
															 "   inner join seg_ops_rvs as sor on smod.ops_code = sor.code ".
															 "   where smo.encounter_nr = '".$enc_nr."' and ".
															 "      (sor.description like '%".$keyword."%' or ".
															 "       smod.ops_code like '%".$keyword."%') ".
										 "   order by description) as t
										 order by description";
					}
					else {
						$this->sql = "select refno, ops_code,
												 (select description from seg_ops_rvs as t3
															 where t3.code = t.ops_code
																and description like '%".$keyword."%') as description, op_date,
												 max(t.rvu) as rvu, max(multiplier) as multiplier, group_code, entry_no,
												 (select ifnull(count(*), 0) as count from seg_ops_chrgd_accommodation as soca
													 where soca.ops_refno = t.refno and
														soca.ops_code = t.ops_code) as bselected
										from
										(select sosd.refno, sosd.ops_code, sosd.rvu, sosd.multiplier, group_code, 0 as entry_no,
										   (SELECT MAX(ceo.op_date) op_date
										       FROM care_encounter_op ceo
										       WHERE ceo.refno = sos.refno) op_date
											 from seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno
											 where sos.encounter_nr = '".$enc_nr."' and
												sosd.ops_code like '%".$keyword."%'
										 union
										select smod.refno, smod.ops_code, smod.rvu, smod.multiplier, group_code, smod.entry_no, smod.op_date
											 from seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno
											 where smo.encounter_nr = '".$enc_nr."' and
												smod.ops_code like '%".$keyword."%' order by rvu desc) as t
										group by group_code having group_code <> ''
										union
										select refno, ops_code, description, op_date, rvu, multiplier, group_code, entry_no, bselected
										from ".
										 "(select sosd.refno, sosd.ops_code, description, sosd.rvu, sosd.multiplier, group_code, 0 as entry_no, ".
															 "   (select ifnull(count(*), 0) as count from seg_ops_chrgd_accommodation as soca ".
															 "       where soca.ops_refno = sosd.refno and soca.ops_code = sosd.ops_code and ops_entryno = 0) as bselected, ".
															 "   (SELECT MAX(ceo.op_date) op_date
																       FROM care_encounter_op ceo
																       WHERE ceo.refno = sos.refno) op_date ".
															 "   from (seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno) ".
															 "   inner join seg_ops_rvs as sor on sosd.ops_code = sor.code ".
															 "   where sos.encounter_nr = '".$enc_nr."' and ".
															 "      (description like '%".$keyword."%' or ".
															 "       sosd.ops_code like '%".$keyword."%') ".
															 " union ".
										 "select smod.refno, smod.ops_code, sor.description, smod.rvu, smod.multiplier, group_code, smod.entry_no, ".
															 "      (select ifnull(count(*), 0) as count from seg_ops_chrgd_accommodation as soca ".
															 "          where soca.ops_refno = smod.refno and soca.ops_code = smod.ops_code and ops_entryno = smod.entry_no) as bselected, ".
															 "      smod.op_date ".
															 "   from (seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno) ".
															 "   inner join seg_ops_rvs as sor on smod.ops_code = sor.code ".
															 "   where smo.encounter_nr = '".$enc_nr."' and ".
															 "      (sor.description like '%".$keyword."%' or ".
															 "       smod.ops_code like '%".$keyword."%') ".
										 "   order by description) as t
											where group_code = '' order by description";
					}


//            $this->sql = "select distinct sosd.refno, sosd.ops_code, description, sosd.rvu, sosd.multiplier, 0 as entry_no, ".
//                         "   (select ifnull(count(*), 0) as count from seg_ops_chrgd_accommodation as soca ".
//                         "       where soca.ops_refno = sosd.refno and soca.ops_code = sosd.ops_code and ops_entryno = 0) as bselected ".
//                         "   from (seg_ops_serv as sos inner join seg_ops_servdetails as sosd on sos.refno = sosd.refno) ".
//                         "   inner join seg_ops_rvs as sor on sosd.ops_code = sor.code ".
//                         "   where sos.encounter_nr = '".$enc_nr."' and parent_ops_code = '' and ".
//                         "      (description like '%".$keyword."%' or ".
//                         "       sosd.ops_code like '%".$keyword."%') ".
//                         " union ".
//                         "select distinct smod.refno, smod.ops_code, description, smod.rvu, smod.multiplier, smod.entry_no, ".
//                         "   (select ifnull(count(*), 0) as count from seg_ops_chrgd_accommodation as soca ".
//                         "       where soca.ops_refno = smod.refno and soca.ops_code = smod.ops_code and ops_entryno = smod.entry_no) as bselected ".
//                         "   from (seg_misc_ops as smo inner join seg_misc_ops_details as smod on smo.refno = smod.refno) ".
//                         "   inner join seg_ops_rvs as sor on smod.ops_code = sor.code ".
//                         "   where smo.encounter_nr = '".$enc_nr."' and parent_ops_code = '' and ".
//                         "      (description like '%".$keyword."%' or ".
//                         "       smod.ops_code like '%".$keyword."%') ".
//                         "   order by description";
				}

				if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->res['ssl']->RecordCount()) {   // fix for Bugzilla bug 68
								return $this->res['ssl'];
						}else{return false;}
				}else{return false;}
		}

		function getListLabSectionRequest_Status($fromdate, $todate){
				global $db;

				if (($fromdate)&&($todate)){
						$cond = "AND (se.op_date >= '".$fromdate."' AND se.op_date <= '".$todate."')";
				}
				/*
				$this->sql="SELECT s.request_date AS serv_dt, request_time AS serv_tm,
												s.pid AS patientID,
												ss.description AS service_name,
												s.*, d.*, ss.*, p.*, enc.*,se.*
												FROM seg_ops_serv AS s
												INNER JOIN care_encounter_op AS se ON s.refno=se.refno AND s.nr=se.nr
												INNER JOIN seg_ops_servdetails AS d ON s.refno=d.refno
												INNER JOIN care_ops301_en AS ss ON d.ops_code=ss.code
												INNER JOIN care_person AS p ON p.pid=s.pid
												INNER JOIN care_encounter AS enc ON s.encounter_nr = enc.encounter_nr
												WHERE s.status NOT IN($this->dead_stat)
												$cond
												GROUP BY s.refno
												ORDER BY p.name_last, p.name_first, s.refno";
				*/
				$this->sql="SELECT s.request_date AS serv_dt, request_time AS serv_tm,
												s.pid AS patientID,
												ss.description AS service_name,
												s.*, d.*, ss.*, p.*, enc.*,se.*
												FROM seg_ops_serv AS s
												INNER JOIN care_encounter_op AS se ON s.refno=se.refno AND s.nr=se.nr
												INNER JOIN seg_ops_servdetails AS d ON s.refno=d.refno
												INNER JOIN seg_ops_rvs AS ss ON d.ops_code=ss.code
												INNER JOIN care_person AS p ON p.pid=s.pid
												INNER JOIN care_encounter AS enc ON s.encounter_nr = enc.encounter_nr
												WHERE s.status NOT IN($this->dead_stat)
												$cond
												GROUP BY s.refno
												ORDER BY p.name_last, p.name_first, s.refno";
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

		function getRequestedServicesPerRef($refno) {
				global $db;
				/*
				$this->sql="SELECT sd.*, ss.code, ss.description AS name,
												ss.rvu AS rvu_unit, ss.multiplier AS multiplier_unit
												FROM seg_ops_serv AS s
												INNER JOIN seg_ops_servdetails AS sd ON s.refno = sd.refno
												INNER JOIN care_ops301_en AS ss ON sd.ops_code = ss.code
												WHERE s.refno = '".$refno."'
												AND s.status NOT IN ($this->dead_stat)
												ORDER BY ss.description";
				*/

				$this->sql="SELECT sd.*, ss.code, ss.description AS name,
												ss.rvu AS rvu_unit, sd.multiplier AS multiplier_unit
												FROM seg_ops_serv AS s
												INNER JOIN seg_ops_servdetails AS sd ON s.refno = sd.refno
												INNER JOIN seg_ops_rvs AS ss ON sd.ops_code = ss.code
												WHERE s.refno = '".$refno."'
												AND s.status NOT IN ($this->dead_stat)
												ORDER BY ss.description";
				/*
				$hospObj = new Hospital_Admin();
				$nPCF    = $hospObj->getDefinedPCF();

				$this->sql="SELECT sd.*, ss.code, ss.description AS name,
												ss.rvu AS rvu_unit, ".$nPCF." AS multiplier_unit
												FROM seg_ops_serv AS s
												INNER JOIN seg_ops_servdetails AS sd ON s.refno = sd.refno
												INNER JOIN seg_ops_rvs AS ss ON sd.ops_code = ss.code
												WHERE s.refno = '".$refno."'
												AND s.status NOT IN ($this->dead_stat)
												ORDER BY ss.description";
				*/

				#echo "sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)) {
							$this->count=$this->result->RecordCount();
						return $this->result;
				} else{
						return FALSE;
				}
		}

		function getStatReport($fromdate, $todate){
				global $db;

				if (($fromdate)&&($todate)){
						$cond = "AND (se.op_date  >= '".$fromdate."' AND se.op_date  <= '".$todate."')";
				}
				/*
				$this->sql="SELECT count(ss.code) AS stat, EXTRACT(YEAR FROM s.request_date) AS year
												FROM seg_ops_serv AS s
												INNER JOIN seg_ops_servdetails AS d ON s.refno=d.refno
												INNER JOIN care_ops301_en AS ss ON d.ops_code=ss.code
												INNER JOIN care_person AS p ON p.pid=s.pid
												INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
												WHERE s.status NOT IN($this->dead_stat)
												$cond
												GROUP BY EXTRACT(YEAR FROM s.request_date)
												ORDER BY EXTRACT(YEAR FROM s.request_date) DESC";
				*/
				/*
				$this->sql="SELECT count(EXTRACT(YEAR FROM se.op_date)) AS stat,
												EXTRACT(YEAR FROM se.op_date) AS year
												FROM seg_ops_serv AS s
												INNER JOIN care_encounter_op AS se ON s.refno=se.refno AND s.nr=se.nr
												INNER JOIN seg_ops_servdetails AS d ON s.refno=d.refno
												INNER JOIN care_ops301_en AS ss ON d.ops_code=ss.code
												INNER JOIN care_person AS p ON p.pid=s.pid
												INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
												WHERE s.status NOT IN($this->dead_stat)
												$cond
												GROUP BY EXTRACT(YEAR FROM se.op_date)
												ORDER BY EXTRACT(YEAR FROM se.op_date) DESC";
				*/
				$this->sql="SELECT count(EXTRACT(YEAR FROM se.op_date)) AS stat,
												EXTRACT(YEAR FROM se.op_date) AS year
												FROM seg_ops_serv AS s
												INNER JOIN care_encounter_op AS se ON s.refno=se.refno AND s.nr=se.nr
												INNER JOIN seg_ops_servdetails AS d ON s.refno=d.refno
												INNER JOIN seg_ops_rvs AS ss ON d.ops_code=ss.code
												INNER JOIN care_person AS p ON p.pid=s.pid
												INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
												WHERE s.status NOT IN($this->dead_stat)
												$cond
												GROUP BY EXTRACT(YEAR FROM se.op_date)
												ORDER BY EXTRACT(YEAR FROM se.op_date) DESC";
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
						$cond = "AND (se.op_date >= '".$fromdate."' AND se.op_date <= '".$todate."')";
				}
				/*
				$this->sql="SELECT count(ss.code) AS stat, EXTRACT(MONTH FROM s.request_date) AS month,
												EXTRACT(YEAR FROM s.request_date) AS year
												FROM seg_ops_serv AS s
												INNER JOIN seg_ops_servdetails AS d ON s.refno=d.refno
												INNER JOIN care_ops301_en AS ss ON d.ops_code=ss.code
												INNER JOIN care_person AS p ON p.pid=s.pid
												INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
												WHERE s.status NOT IN($this->dead_stat)
												$cond
												AND EXTRACT(YEAR FROM s.request_date)='".$year."'
												GROUP BY EXTRACT(MONTH FROM s.request_date)
												ORDER BY EXTRACT(MONTH FROM s.request_date)";
				*/
				/*
				$this->sql="SELECT count(EXTRACT(YEAR FROM se.op_date)) AS stat,
												EXTRACT(MONTH FROM se.op_date) AS month,
												EXTRACT(YEAR FROM se.op_date) AS year
												FROM seg_ops_serv AS s
												INNER JOIN care_encounter_op AS se ON s.refno=se.refno AND s.nr=se.nr
												INNER JOIN seg_ops_servdetails AS d ON s.refno=d.refno
												INNER JOIN care_ops301_en AS ss ON d.ops_code=ss.code
												INNER JOIN care_person AS p ON p.pid=s.pid
												INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
												WHERE s.status NOT IN($this->dead_stat)
												$cond
												AND EXTRACT(YEAR FROM se.op_date)='".$year."'
												GROUP BY EXTRACT(MONTH FROM se.op_date)
												ORDER BY EXTRACT(MONTH FROM se.op_date)";
						*/
						$this->sql="SELECT count(EXTRACT(YEAR FROM se.op_date)) AS stat,
												EXTRACT(MONTH FROM se.op_date) AS month,
												EXTRACT(YEAR FROM se.op_date) AS year
												FROM seg_ops_serv AS s
												INNER JOIN care_encounter_op AS se ON s.refno=se.refno AND s.nr=se.nr
												INNER JOIN seg_ops_servdetails AS d ON s.refno=d.refno
												INNER JOIN seg_ops_rvs AS ss ON d.ops_code=ss.code
												INNER JOIN care_person AS p ON p.pid=s.pid
												INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
												WHERE s.status NOT IN($this->dead_stat)
												$cond
												AND EXTRACT(YEAR FROM se.op_date)='".$year."'
												GROUP BY EXTRACT(MONTH FROM se.op_date)
												ORDER BY EXTRACT(MONTH FROM se.op_date)";

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

		function getStatByResultEncType($year, $month, $fromdate, $todate, $dept_nr, $enctype, $withResult){
				global $db;

				if (($fromdate)&&($todate)){
						$cond = "AND (se.op_date >= '".$fromdate."' AND se.op_date <= '".$todate."')";
				}

				if ($withResult){
						/*
						$this->sql="SELECT count(se.dept_nr) AS stat_result,
														EXTRACT(MONTH FROM se.op_date) AS month,
														EXTRACT(YEAR FROM se.op_date) AS year
														FROM seg_ops_serv AS s
														INNER JOIN care_encounter_op AS se ON s.refno=se.refno AND s.nr=se.nr
														INNER JOIN seg_ops_servdetails AS d ON s.refno=d.refno
														INNER JOIN care_ops301_en AS ss ON d.ops_code=ss.code
														INNER JOIN care_person AS p ON p.pid=s.pid
														INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
														WHERE s.status NOT IN($this->dead_stat)
														AND EXTRACT(YEAR FROM se.op_date)='".$year."'
														AND EXTRACT(MONTH FROM se.op_date)='".$month."'
														$cond
														AND se.result_info NOT IN ('')
														AND se.dept_nr='".$dept_nr."'
														AND e.encounter_type IN(".$enctype.")
														GROUP BY EXTRACT(MONTH FROM se.op_date), se.dept_nr,
														e.encounter_type IN(".$enctype.")
														ORDER BY se.dept_nr, e.encounter_type";
								*/
								$this->sql="SELECT count(se.dept_nr) AS stat_result,
														EXTRACT(MONTH FROM se.op_date) AS month,
														EXTRACT(YEAR FROM se.op_date) AS year
														FROM seg_ops_serv AS s
														INNER JOIN care_encounter_op AS se ON s.refno=se.refno AND s.nr=se.nr
														INNER JOIN seg_ops_servdetails AS d ON s.refno=d.refno
														INNER JOIN seg_ops_rvs AS ss ON d.ops_code=ss.code
														INNER JOIN care_person AS p ON p.pid=s.pid
														INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
														WHERE s.status NOT IN($this->dead_stat)
														AND EXTRACT(YEAR FROM se.op_date)='".$year."'
														AND EXTRACT(MONTH FROM se.op_date)='".$month."'
														$cond
														AND se.result_info NOT IN ('')
														AND se.dept_nr='".$dept_nr."'
														AND e.encounter_type IN(".$enctype.")
														GROUP BY EXTRACT(MONTH FROM se.op_date), se.dept_nr,
														e.encounter_type IN(".$enctype.")
														ORDER BY se.dept_nr, e.encounter_type";
				}else{
						/*
						$this->sql="SELECT count(se.dept_nr) AS stat_result,
														EXTRACT(MONTH FROM se.op_date) AS month,
														EXTRACT(YEAR FROM se.op_date) AS year
														FROM seg_ops_serv AS s
														INNER JOIN care_encounter_op AS se ON s.refno=se.refno AND s.nr=se.nr
														INNER JOIN seg_ops_servdetails AS d ON s.refno=d.refno
														INNER JOIN care_ops301_en AS ss ON d.ops_code=ss.code
														INNER JOIN care_person AS p ON p.pid=s.pid
														INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
														WHERE s.status NOT IN($this->dead_stat)
														AND EXTRACT(YEAR FROM se.op_date)='".$year."'
														AND EXTRACT(MONTH FROM se.op_date)='".$month."'
														$cond
														AND se.result_info IN ('')
														AND se.dept_nr='".$dept_nr."'
														AND e.encounter_type IN(".$enctype.")
														GROUP BY EXTRACT(MONTH FROM se.op_date), se.dept_nr,
														e.encounter_type IN(".$enctype.")
														ORDER BY se.dept_nr, e.encounter_type";
								*/
								$this->sql="SELECT count(se.dept_nr) AS stat_result,
														EXTRACT(MONTH FROM se.op_date) AS month,
														EXTRACT(YEAR FROM se.op_date) AS year
														FROM seg_ops_serv AS s
														INNER JOIN care_encounter_op AS se ON s.refno=se.refno AND s.nr=se.nr
														INNER JOIN seg_ops_servdetails AS d ON s.refno=d.refno
														INNER JOIN seg_ops_rvs AS ss ON d.ops_code=ss.code
														INNER JOIN care_person AS p ON p.pid=s.pid
														INNER JOIN care_encounter AS e ON e.encounter_nr = s.encounter_nr
														WHERE s.status NOT IN($this->dead_stat)
														AND EXTRACT(YEAR FROM se.op_date)='".$year."'
														AND EXTRACT(MONTH FROM se.op_date)='".$month."'
														$cond
														AND se.result_info IN ('')
														AND se.dept_nr='".$dept_nr."'
														AND e.encounter_type IN(".$enctype.")
														GROUP BY EXTRACT(MONTH FROM se.op_date), se.dept_nr,
														e.encounter_type IN(".$enctype.")
														ORDER BY se.dept_nr, e.encounter_type";
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

		#------------------------

		#added by VAN 09-10-08
		function getAllAnesthesia() {
				global $db;

				$this->sql="SELECT * FROM care_type_anaesthesia ORDER BY name";

				#echo "sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)) {
							$this->count=$this->result->RecordCount();
						return $this->result;
				} else{
						return FALSE;
				}
		}
		#----------------------

		function update_or_main_request($data, $or_main_data) {
			global $db;
			$error = array();

			$query = "START TRANSACTION";
			$result = $db->Execute($query);

			if (!$this->updateOpsServInfoFromArray($data)) {
				$error[] = 'Stage 1 Error';
			}
			if (!$this->updateCareEncounterOp($data)) {

				$error[] = 'Stage 2 Error';
			}
			//if (!$this->update_seg_or_main($data['refno'], $data)) {
			if (!$this->update_seg_or_main($data['refno'],$data)) {
				$error[] = 'Stage 3 Error';
			}

			if (!empty($error)) {

				$query = "ROLLBACK";
				$result = $db->Execute($query);

				foreach ($error as $value) {echo $value . '<br/>';}
				return false;
			}
			else {
				$query = "COMMIT";
				$result = $db->Execute($query);

				return true;
			}
		}


		function update_seg_or_main($reference_number, $data) {
			global $db;

			extract($data);
			$history = $this->ConcatHistory("Updated ".date('Y-m-d H:i:s')." ".$_SESSION['sess_user_name']."\n");
			$modify_id = $_SESSION['sess_user_name'];
			//$modify_dt = date("Y-m-d h:i:s");
			/*$query = "UPDATE seg_or_main SET request_priority = '$request_priority',
																			 consent_signed = $consent_signed,
																			 or_case = '$or_request_case',
																			 est_length_op = '$or_est_op_length',
																			 case_classification = '$case_classification',
																			 pre_op_diagnosis = '$pre_op_diagnosis',
																			 operation_procedure = '$operation_procedure',
																			 special_requirements = '$special_requirements',
																			 history = $history,
																			 modified_id = '$modify_id',
																			 modified_date = NOW(),
																			 or_type = '$or_type'
								 WHERE ceo_refno='$reference_number'"; */
			$query = "UPDATE seg_or_main SET trans_type = '$trans_type',
																			 or_procedure = '$or_procedure',
																			 procedure_id = '$procedure_id',
																			 special_requirements = '$special_requirements',
																			 date_operation = '$date_operation',
																			 history = $history,
																			 dr_nr = '$dr_nr',
																			 modify_id = '$modify_id',
																			 modify_dt = NOW(),
																			 dept_nr = '$dept_nr',
																			 is_main = '$is_main',
																			 or_type = '$or_type',
																			 request_priority = '$request_priority',
																			 remarks = '$remarks'
								 WHERE ceo_refno='$reference_number'";
			#echo "query= ".$query;
			 if ($db->Execute($query)) {
				 return true;
			 }
			 else {
				 return false;
			 }

		}

				#added by Cherry 02-26-10
				function save_or_main_request($data) {
						global $db;
						$error = array();
						$reference_number = date('Y').'000001';
						$data['refno'] = $this->getNewRefNo($reference_number);


						$query = "START TRANSACTION";
						$result = $db->Execute($query);

						if (!$this->saveOpsServInfoFromArray($data)) {
								$error[] = 'Stage 1 Error';
						}

						if (!$this->insertOpRequestNr($data['refno'], $this->saveCareEncounterOp($data))) {

								$error[] = 'Stage 2 Error';
						}

						if(!$this->insert_seg_or_main($data)){
								$error[] = 'Stage 3 Error';
						}

						if (!empty($error)) {
								$query = "ROLLBACK";
								$result = $db->Execute($query);
								foreach ($error as $value) {echo $value . '<br/>';}
								return false;
						}
						else {
								$query = "COMMIT";
								$result = $db->Execute($query);
								return $data['refno'];
						}
				}

		#Added by Cherry 02-26-10
		function insert_seg_or_main($data) {
				global $db;
				extract($data);
				$author = $_SESSION['sess_user_name'];
				$status = 'request';
				#echo $date_operation;
				#echo date('Y-m-d', strtotime($date_operation));

				/*if ($or_type=='DR')
						#$status = 'request_DR';
						$status = 'post';                    //changed by CHA 10-07-09
				else
						$status = 'request';
				*/
				$history = 'Create '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name'] . '\n';

			 #edited by Cherry 04-05-10
			 if($is_main == 0){
			 $query = "INSERT INTO seg_or_main(ceo_refno,encounter_nr, trans_type, date_request, or_procedure, procedure_id, request_source,
																					special_requirements, date_operation, history, time_operation, dr_nr, create_id,
																					create_dt, modify_id, modify_dt, status, dept_nr, is_main)
																					VALUES('$refno','$encounter_nr', '$trans_type', '$date_request', '$or_procedure', '$procedure_id', '$request_source',
																					'$special_requirements', '$date_operation', '$history', '$time_operation', '$dr_nr', '$author', NOW(),
																					'$author', NOW(), '$status', '$dept_nr', '$is_main')";
			 }else{
					$query = "INSERT INTO seg_or_main(ceo_refno,encounter_nr, trans_type, date_request, or_procedure, procedure_id, request_source,
																					special_requirements, date_operation, history, time_operation, dr_nr, create_id,
																					create_dt, modify_id, modify_dt, status, dept_nr, is_main, or_type, request_priority, remarks)
																					VALUES('$refno','$encounter_nr', '$trans_type', '$date_request', '$or_procedure', '$procedure_id', '$request_source',
																					'$special_requirements', '$date_operation', '$history', '$time_operation', '$dr_nr', '$author', NOW(),
																					'$author', NOW(), '$status', '$dept_nr', '$is_main', '$or_type', '$request_priority', '$remarks')";
			 }


				if($db->Execute($query)) {
						return true;
				}
				else {
						return false;
				}
		}

	function update_or_main_care_encounter($data) {
	global $db;
	extract($data);
	$query = "UPDATE care_encounter_op SET op_date='$op_date', op_time='$op_time', operator='$operator',
				assistant='$assistant', scrub_nurse='$scrub_nurse', rotating_nurse='$rotating_nurse',
				an_doctor='$an_doctor' WHERE refno=$refno";
	$result = $db->Execute($query);
	if ($result) {
		return true;
	}
	else {
		return false;
	}
	}

	function update_or_main_schedule($data) {
	 global $db;

	 $history = $this->ConcatHistory("Updated ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
	 $modify_id = $HTTP_SESSION_VARS['sess_user_name'];
	 extract($data);
	 $operator = $this->serializePersonnelNr($surgeon,'operator');
	 $assistant = $this->serializePersonnelNr($surgeon_assist,'assistant');
	 $scrub_nurse = $this->serializePersonnelNr($nurse_scrub,'scrub_nurse');
	 $rotating_nurse = $this->serializePersonnelNr($nurse_rotating,'rotating_nurse');
	 $an_doctor = $this->serializePersonnelNr($anesthesiologist,'an_doctor');

	 $care_data = array('op_date' => $op_date,
						'op_time' => $op_time,
						'refno' => $refno,
						'operator' => $operator,
						'assistant' => $assistant,
						'scrub_nurse' => $scrub_nurse,
						'rotating_nurse' => $rotating_nurse,
						'an_doctor' => $an_doctor);
	 $cp = $this->update_or_main_care_encounter($care_data);

	 $personell_data = array(7=>$surgeon, 8=>$surgeon_assist, 9=>$nurse_scrub, 10=>$nurse_rotating, 12=>$anesthesiologist);



	 $ok = $this->deleteOpsPersonell($data['refno']);
	 if ($ok && $cp) {

		if ($this->save_or_main_personell($data['refno'], $personell_data)) {

			/*if ($this->update_or_main_status($data['or_main_refno'], '', 'scheduled')){
												echo "pasok after update_or_main_status"."<br>";
												return true;
										}*/
			 return true;
		}
		else {
			return false;
		}
	 }
	 else {
		 return false;
	 }
	}

	 function update_post_operative($data) {
	 global $db;
	 extract($data);

	 $post_details = array('or_main_refno' => $or_main_refno,
							 'post_time_started' => date('H:i', strtotime($post_time_started)),
							 'post_time_finished' => date('H:i', strtotime($post_time_finished)),
							 'post_operative_diagnosis' => $post_operative_diagnosis,
							 'intra_operative' => $intra_operative,
							 'post_operative' => $post_operative,
							 'or_status' => $or_status,
							 'or_technique' => $or_technique,
							 'sponge_count' => $sponge_count,
							 'sutures' => $sutures,
							 'sponge_os' => $sponge_os,
							 'sponge_ap' => $sponge_ap,
							 'sponge_cb' => $sponge_cb,
							 'sponge_pp' => $sponge_pp,
							 'sponge_peanuts' => $sponge_peanuts,
							 'needle_count' => $needle_count,
							 'instrument_count' => $instrument_count,
							 'transferred_to' => $transferred_to,
							 'operation_performed' => $operation_performed,
							 'fluids' => $fluids,
										 'drain_inserted' => $drain_inserted,
										 'packs_inserted' => $packs_inserted,
										 'blood_replacement' => $blood_replacement,
										 'blood_loss' => $blood_loss,
										 'tissues_removed' => $tissues_removed,
										 'remarks' => $remarks);
	 $icpm_details = array('refno' => $refno,
							 'rvu' => $rvu,
							 'ops_code' => $ops_code,
							 'multiplier' => $multiplier,
							 'ops_charge' => $ops_charge);
	 $personell_details = array('surgeon' => $surgeon,
								'surgeon_assist' => $surgeon_assist,
								'nurse_scrub' => $nurse_scrub,
								'nurse_rotating' => $nurse_rotating,
								'anesthesiologist' => $anesthesiologist);
	 $icpm_personell = array_merge($icpm_details, $personell_details);
	 $anesthesia_details = array('time_begun' => $time_begun,
								 'time_ended' => $time_ended,
								 'tb_meridian' => $tb_meridian,
								 'te_meridian' => $te_meridian,
								 'anesthetics' => $anesthetics,
																 'anesthesia_procedure_category' => $anesthesia_procedure_category,
																 'anesthesia_procedure_specific' => $anesthesia_procedure_specific,
																 'anesthesia_id' => $anesthesia_id,
																 'or_main_refno' => $or_main_refno,
																 'order_refno' => $order_refno);	#added by cha 01-07-2010
	 $errors = array();
	 $query = "START TRANSACTION";
	 $result = $db->Execute($query);
	 if (!$this->update_or_main_post_details($post_details)) //save post operative details
		$errors[] = 'Stage 1 Error';

	 if (!$this->updateOpsServDetailsInfoFromArray($icpm_details)) //save ICPM
		$errors[] = 'Stage 2 Error';

	 if (!$this->updateOpsPersonell($icpm_personell)) //save Personell
		$errors[] = 'Stage 3 Error';

	 if (!$this->insert_care_encounter_anesthesia($anesthesia_details)){ //save anesthesia
		$errors[] = 'Stage 4 Error';

	 }

	 if (!$this->update_or_main_status($or_main_refno, '', 'post'))  //update status to post-operative
		$errors[] = 'Stage 5 Error';
	 if (empty($errors)) {

		 return true;
	 }
	 else {
		 foreach($errors as $value) {
			 echo $value . '</br>';
		 }
		 return false;
	 }


	 }
	function update_or_main_post_details($data) {
	global $db;
	extract($data);
	$mode = 'insert';
	$author = $_SESSION['sess_user_name'];
	$query = "SELECT COUNT(or_main_refno_post) as num FROM seg_or_main_post WHERE or_main_refno=$or_main_refno";

	$result = $db->Execute($query);
	$row = $result->FetchRow();
	if ($row['num'] > 0) {
		$mode = 'edit';
}
	if ($mode == 'insert') {
		$history = 'Created '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name']." \n";
		/*$query = "INSERT INTO seg_or_main_post(or_main_refno, time_started, time_finished, post_op_diagnosis, created_id,
				modified_id, created_date, modified_date, history, intra_operative, post_operative, or_status, or_technique,
				sponge_count, needle_count, instrument_count, transferred_to, operation_performed, fluids, drain_inserted,
				packs_inserted, blood_replacement, blood_loss, tissues_removed, remarks)
				VALUES ($or_main_refno, '$post_time_started', '$post_time_finished', '$post_operative_diagnosis',
				'$author', '$author', NOW(), NOW(), '$history', '$intra_operative', '$post_operative', '$or_status',
				'$or_technique', $sponge_count, $needle_count, $instrument_count, $transferred_to, '$operation_performed', '$fluids',
				'$drain_inserted', '$packs_inserted', '$blood_replacement', '$blood_loss', '$tissues_removed', '$remarks')"; */

		$query = "INSERT INTO seg_or_main_post(or_main_refno, time_started, time_finished, post_op_diagnosis, created_id,
				modified_id, created_date, modified_date, history, intra_operative, post_operative, or_status, or_technique
				, sponge_count
				, sutures
				, sponge_os
				, sponge_ap
				, sponge_cb
				, sponge_pp
				, sponge_peanuts
				, needle_count, instrument_count, operation_performed, fluids, drain_inserted,
				packs_inserted, blood_replacement, blood_loss, tissues_removed, remarks)
				VALUES ($or_main_refno, '$post_time_started', '$post_time_finished', '$post_operative_diagnosis',
				'$author', '$author', NOW(), NOW(), '$history', '$intra_operative', '$post_operative', '$or_status',
				'$or_technique',
				 $sponge_count,
				 '$sutures',
				 '$sponge_os',
				 '$sponge_ap',
				 '$sponge_cb',
				 '$sponge_pp',
				 '$sponge_peanuts',
				 $needle_count, $instrument_count, '$operation_performed', '$fluids',
				'$drain_inserted', '$packs_inserted', '$blood_replacement', '$blood_loss', '$tissues_removed', '$remarks')";
	}
	elseif ($mode == 'edit') {
		$history = $this->ConcatHistory("Updated ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		/*$query = "UPDATE seg_or_main_post SET time_started='$post_time_started', time_finished='$post_time_finished',
		post_op_diagnosis='$post_operative_diagnosis', modified_id='$author', modified_date=NOW(), history=$history,
		intra_operative='$intra_operative', post_operative='$post_operative', or_status='$or_status',
		or_technique='$or_technique', sponge_count=$sponge_count, needle_count=$needle_count, instrument_count=$instrument_count,
		transferred_to=$transferred_to, operation_performed='$operation_performed', fluids='$fluids',
		drain_inserted='$drain_inserted', packs_inserted='$packs_inserted', blood_replacement='$blood_replacement',
		blood_loss='$blood_loss', tissues_removed='$tissues_removed', remarks='$remarks' WHERE or_main_refno=$or_main_refno"; */
		$query = "UPDATE seg_or_main_post SET time_started='$post_time_started', time_finished='$post_time_finished',
		post_op_diagnosis='$post_operative_diagnosis', modified_id='$author', modified_date=NOW(), history=$history,
		intra_operative='$intra_operative', post_operative='$post_operative', or_status='$or_status',
		or_technique='$or_technique',
		sponge_count=$sponge_count ,
		sutures='$sutures' ,
		sponge_os='$sponge_os' ,
		sponge_ap='$sponge_ap' ,
		sponge_cb='$sponge_cb' ,
		sponge_pp='$sponge_pp' ,
		sponge_peanuts='$sponge_peanuts' ,
		 needle_count=$needle_count, instrument_count=$instrument_count,
		operation_performed='$operation_performed', fluids='$fluids',
		drain_inserted='$drain_inserted', packs_inserted='$packs_inserted', blood_replacement='$blood_replacement',
		blood_loss='$blood_loss', tissues_removed='$tissues_removed', remarks='$remarks' WHERE or_main_refno=$or_main_refno";
	}
	$result = $db->Execute($query);
	if ($result) {
		return true;
	}
	else {
		return false;
	}

	}

	function save_or_main_personell($refno, $role_data) {
	global $db;
	$personell = array();
	$error = array();

	foreach ($role_data as $role_key => $role_value) {
		foreach ($role_value as $personell_value) {
		$personell_array = array($role_key, $personell_value);
		$personell[] = $personell_array;
		}
	}

	$author = $_SESSION['sess_user_name'];

	$index = 'refno, role_type_nr, dr_nr, modify_id, create_id, modify_dt, create_dt';
	$values = "'$refno', ?, ?, '$author', '$author', NOW(), NOW()";

	$query = "START TRANSACTION";
	$result = $db->Execute($query);

	$query = "INSERT INTO seg_ops_personell ($index) VALUES ($values)";
	$result = $db->Execute($query, $personell);
	if (!$result) {
		$error[] = 'Stage 1 Error';
	}

	if (!empty($error)) {
		$query = "ROLLBACK";
		$result = $db->Execute($query);
		foreach ($error as $value) {echo $value . '<br/>';}
		return false;
	}
	else {
		$query = "COMMIT";
		$result = $db->Execute($query);
		return true;
	}

	}

	#Added by Cherry 09-13-10
	function update_or_main_approval($refno, $data){
		global $db;

		extract($data);
			$history = $this->ConcatHistory("Updated ".date('Y-m-d H:i:s')." ".$_SESSION['sess_user_name']."\n");
			$modify_id = $_SESSION['sess_user_name'];

			$query = "UPDATE seg_or_main SET room_nr = '$room_nr',
																			 bed_nr = '$bed_nr',
																			 final_date_operation = '$final_date_operation',
																			 length_op = '$length_op',
																			 history = $history,
																			 modify_id = '$modify_id',
																			 modify_dt = NOW()
								WHERE or_main_refno='$refno'";
			//echo "query= ".$query."<br>";
			 if ($db->Execute($query)) {
				 return true;
			 }
			 else {
				 return false;
			 }

	}

	function update_or_main_status($refno, $reason, $status) {
	global $db;
	$error = array();
	$mode = 'new';
	$author = $_SESSION['sess_user_name'];
	$query = "SELECT COUNT(status_req_id) as num FROM seg_or_main_status WHERE or_main_refno=$refno AND status='$status'";

	$result = $db->Execute($query);
	$row = $result->FetchRow();

	if ($row['num'] > 0) {
		$mode = 'edit';
	}

	$query = "START TRANSACTION";
	$result = $db->Execute($query);

	if ($mode == 'new') {
		$history = 'Created '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name']." \n";
		$query = "INSERT INTO seg_or_main_status(or_main_refno, status, reason, history, created_id, modified_id,
				created_date, modified_date) VALUES ($refno, '$status', '$reason', '$history', '$author', '$author',
				NOW(), NOW())";
	}

	else {
		$history = 'Updated '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name']." \n";
		$query = "UPDATE seg_or_main_status SET reason='$reason', history=CONCAT(history, '$history'),
				modified_id='$author', modified_date=NOW() WHERE or_main_refno = $refno AND status='$status'";
	}


	$result = $db->Execute($query);
	if ($result) {

		$history = 'Updated '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name']." \n";
		$query = "UPDATE seg_or_main SET status='$status', history=CONCAT(history, '$history'),
				modify_id='$author', modify_dt=NOW() WHERE or_main_refno = $refno";

		$result = $db->Execute($query);
		if (!$result) {
		$error[] = 'Stage 2 Error';
		}
	}
	else {
		$error[] = 'Stage 1 Error';
	}

	if (!empty($error)) {
		$query = "ROLLBACK";
		$result = $db->Execute($query);
		foreach ($error as $value) {echo $value . '<br/>';}
		return false;
	}
	else {
		$query = "COMMIT";
		$result = $db->Execute($query);
		if ($mode == 'new')
		return 'insert';
	 else
		return 'update';
	}
	}



 /**
		* @internal This function inserts the selected anesthesia and creates an association between the tables
		* care_encounter_op, care_encounter_anesthesia, and care_type_anesthesia for each or_main request
		* @access public
		* @author Omick <omick16@gmail.com>
		* @name db
		* @global array instance of a db connection
		* @package include
		* @subpackage care_api_classes
		* @param string $refnumber the reference number returned from the saveOpsBillingFunction
		* @param array $anesthesias the list of anesthesia(s)
		* @return bool returns a success or fail in the query
		*/
	function insert_care_encounter_anesthesia($anesthesias) {
		global $db;
			#---modified by CHA 01-05-2010-----
		$or_main_refno = $anesthesias['or_main_refno'];
		$query = "DELETE FROM seg_encounter_anesthesia WHERE or_main_refno=$or_main_refno";
		$result = $db->Execute($query);
		$query = "DELETE FROM seg_encounter_anesthetic WHERE or_main_refno=$or_main_refno";
		$result = $db->Execute($query);
			//echo "query1:".$query."<br>";
		$history = 'Created '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name']." \n";
		$author = $_SESSION['sess_user_name'];
			$query = "INSERT INTO seg_encounter_anesthesia(or_main_refno, anesthesia, time_begun, time_ended,
								history, create_id, modify_id, create_dt, modify_dt, anesthesia_category, anesthesia_specific) VALUES";
			foreach ($anesthesias['anesthesia_procedure_category'] as $key => $value) {
					//$anesthetics = $anesthesias['anesthetics'][$key];

			$time_begun = date('H:i:s', strtotime($anesthesias['time_begun'][$key].' '.$anesthesias['tb_meridian'][$key]));
			$time_ended = date('H:i:s', strtotime($anesthesias['time_ended'][$key].' '.$anesthesias['te_meridian'][$key]));
					$anesthesia_category = $anesthesias['anesthesia_procedure_category'][$key];
					$anesthesia_specific = $anesthesias['anesthesia_procedure_specific'][$key];
					$anesthesia_id = $anesthesias['anesthesia_id'][$key];
					$query .= "($or_main_refno, '$anesthesia_id', '$time_begun', '$time_ended', '$history', '$author', '$author',
						NOW(), NOW(), '$anesthesia_category', '$anesthesia_specific'),";
		}
		$query = substr($query, 0, -1);
		if ($db->Execute($query)) {
		if ($db->Affected_Rows()) {
					//echo "ok<br>";
					$query = "select anesthesia_care_id from seg_encounter_anesthesia where or_main_refno=".$db->qstr($or_main_refno);
					$result = $db->Execute($query);
					//echo "query3:".$query."<br>";
					foreach ($anesthesias['anesthesia_procedure_category'] as $key => $value)
					{
						$row = $result->FetchRow();
						for($i=0;$i<count($anesthesias['anesthetics'][$key]);$i++)
						{
							$query2 = "INSERT INTO seg_encounter_anesthetic(anesthesia_care_id,or_main_refno, order_refno, anesthetic_id) VALUES";
							$var_anesth = $anesthesias['anesthetics'][$key];
							$order_no = $anesthesias['order_refno'];
							$anesth_id = $row['anesthesia_care_id'];
							$query2 .= "('$anesth_id','$or_main_refno', '$order_no', '$var_anesth[$i]')";
							if($db->Execute($query2))
							{
								//echo "query4:".$query2."<br>";
								if($db->AffectedRows)
								{
									$ok=true;
								}
							}
							else
							{
								print_r($db->ErrorMsg());
							}

						}
					}
						return true;
				}
				else {
						return false;
				}
			}
			else {
					return false;
			}
		}



		/**
		* @internal This function inserts the additional information in the care_encounter_op_main table
		* @access public
		* @author Omick <omick16@gmail.com>
		* @name db
		* @global array instance of a db connection
		* @package include
		* @subpackage care_api_classes
		* @param string $refnumber the reference number returned from the saveOpsBillingFunction
		* @param array $data additional information for an or_main request
		* @return bool returns a success or fail in the query
		*/
		function insert_care_encounter_op_main($refnumber, $data) {
				global $db;
				extract($data);
				$anesthesias = serialize($anesthesias);
				$query = "INSERT INTO seg_encounter_op_main(request_type,
																										 pre_operative,
																										 proposed_surgery,
																										 consent_signed,
																										 op_case,
																										 case_classification,
																										 operation_start,
																										 operation_end,
																										 nr) VALUES('$request_type',
																																'$pre_operative',
																																'$proposed_surgery',
																																'$consent_signed',
																																'$case',
																																'$case_classification',
																																'$operation_start',
																																'$operation_end',
																																'$refnumber'
																																)";
			if ($db->Execute($query)) {
				if ($db->Affected_Rows()) {
						return true;
				}
				else {
						return false;
				}
			}
			else {
					return false;
			}

		}

		/**
		* @internal This function inserts the reference number of the tables seg_pharma_orders and care_encounter_op
		* @access public
		* @author Omick <omick16@gmail.com>
		* @name db
		* @global array instance of a db connection
		* @package include
		* @subpackage care_api_classes
		* @param string $pharma_refnumber is the reference number returned in the seg_pharma_orders table
		* @param string $or_main_refnumber is the reference number returned by the saveOpsBilling function from
		* the care_encounter_op_table
		* @return bool returns a success or fail in the query
		*/
		function insert_care_encounter_pharma_order($pharma_refnumber, $or_main_refnumber) {
				global $db;
				$query = "INSERT INTO seg_pharma_or_main(pharma_refno, or_main_refno) VALUES('$pharma_refnumber', '$or_main_refnumber')";
				if ($db->Execute($query)) {
					if ($db->Affected_Rows()) {
						return true;
					}
					else {
						return false;
					}
				}
				else {
					return false;
				}
		}

		#Added by Cherry 09-12-10
		function get_or_bed($room_nr){

			#edited by VAN 02-01-08
			#if($this->_getWardOccupants($ward_nr,$date)){
			$this->sql = "SELECT sor.bed_nr FROM seg_or_room_bed AS sor
							LEFT JOIN seg_or_main AS som ON som.room_nr = sor.room_nr
							WHERE sor.room_nr = '".$room_nr."'";
			if($this->result = $db->Execute($this->sql)){
				return $this->result;
			}else{return false;}

		}

		#Added by Cherry 09-12-10
		function get_or_room($dept_nr){
			global $db;

			/*$query = "SELECT room_nr, room_name FROM seg_or_room WHERE dept_nr = '$dept_nr'";
				if ($this->result=$db->Execute($query)){
						if($this->count=$this->result->RecordCount()) {
								return $this->result->FetchRow();
						}
						else {
								return false;
						}
				}else {
						return false;
				}*/

			$this->sql = "SELECT room_nr, room_name FROM seg_or_room WHERE dept_nr = '$dept_nr'";
			if($buf=$db->Execute($this->sql)){
				return $buf;
			}else{
				return FALSE;
			}

		}

		#Added by Cherry 04-05-10
		function get_or_main_basic_info($reference_number) {
				global $db;
				/*
				$query = "SELECT or_main_refno, trans_type, date_request, or_procedure, special_requirements, request_priority,
														dept_nr, or_type, encounter_nr, dr_nr, date_operation, time_operation, status
														FROM seg_or_main
														WHERE ceo_refno='$reference_number'";  */
				$query = "SELECT *  FROM seg_or_main 	WHERE ceo_refno='$reference_number'";


								#echo $query;
								if ($this->result=$db->Execute($query)){
						if($this->count=$this->result->RecordCount()) {
								return $this->result->FetchRow();
						}
						else {
								return false;
						}
				}
				else {
						return false;
				}
		}

	function get_or_main_anesthesia($or_main_refno) {
			#--modified by CHA 01-10-2010
				global $db;
				$this->sql = "SELECT c.nr as anesthesia_nr, c.name, s.anesthesia, s.anesthesia_category as `category`, s.anesthesia_specific as `specific`,
									time_format(s.time_begun, '%h:%i') as time_begun,
									time_format(s.time_ended, '%h:%i') as time_ended,
									time_format(s.time_begun, '%p') as tb_meridian,
									time_format(s.time_ended, '%p') as te_meridian
					FROM care_type_anaesthesia c INNER JOIN seg_encounter_anesthesia s ON (c.nr=s.anesthesia)


					WHERE s.or_main_refno = $or_main_refno";
				if ($this->result=$db->Execute($this->sql)) {
			if ($this->count = $this->result->RecordCount()) {
						return $this->result;
				}
			else {
			return false;
			}
		}
		else {
			return false;
		}
	}

	function get_or_main_anesthesia_as_array($or_main_refno) {
		global $db;
				$query = "SELECT c.nr as anesthesia_nr, c.name, s.anesthesia, s.anesthesia_category as `category`, s.anesthesia_specific as `specific`,
						time_format(s.time_begun, '%h:%i') as time_begun,
						time_format(s.time_ended, '%h:%i') as time_ended,
						time_format(s.time_begun, '%p') as tb_meridian,
						time_format(s.time_ended, '%p') as te_meridian
					FROM care_type_anaesthesia c INNER JOIN seg_encounter_anesthesia s ON (c.nr=s.anesthesia)
					WHERE s.or_main_refno = $or_main_refno";
		if ($this->result=$db->Execute($query)) {
			if ($this->count = $this->result->RecordCount()) {
			 $array = array();
			 while ($row = $this->result->FetchRow()) {
				 $item = array('anesthesia' => $row['name'],
							 'time_begun' => $row['time_begun'] . ' ' . $row['tb_meridian'],
							 'time_ended' => $row['time_ended'] . ' ' . $row['te_meridian']
							 );
				 $array[] = $item;
			 }
			 return $array;
			}
			else {
			return false;
			}
		}
		else {
			return false;
		}
		}

		function get_or_request_details($reference_number) {
				global $db;
				$query = "SELECT sos.refno, sos.encounter_nr, CONCAT_WS(' ',sos.request_date, sos.request_time) as request_date,
									sos.pid, CONCAT_WS(' ', ceo.op_date, ceo.op_time) as operation_date, sos.ordername,
									sos.orderaddress, cr.info, cd.name_formal FROM seg_ops_serv sos
									INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
									INNER JOIN care_department cd ON (cd.nr = ceo.dept_nr)
					INNER JOIN care_room cr ON (cr.room_nr=ceo.op_room)
									LEFT JOIN seg_charity_grants_pid scgp ON (scgp.pid = sos.pid)
									WHERE sos.refno='$reference_number'";

			 if ($this->result=$db->Execute($query)) {
						if($this->count=$this->result->RecordCount()) {
								return $this->result->FetchRow();
						}
						else {
								return false;
						}
				}
				else {
						return false;
				}
		}

	function get_or_main_post_details($or_main_refno = false) {
		global $db;
		if ($or_main_refno) {
		$query = "SELECT time_format(time_started, '%h:%i') as time_started,
						 time_format(time_started, '%p') as ts_meridian,
						 time_format(time_finished, '%h:%i') as time_finished,
						 time_format(time_finished, '%p') as tf_meridian,
						 intra_operative,
						 post_operative,
						 or_status,
						 or_technique,
						 sponge_count,
						 sutures,
						 sponge_os,
						 sponge_ap,
						 sponge_cb,
						 sponge_pp,
						 sponge_peanuts,
						 needle_count,
						 instrument_count,
						 operation_performed,
						 transferred_to,
						 post_op_diagnosis,
						 fluids,
						 drain_inserted,
						 packs_inserted,
						 blood_replacement,
						 blood_loss,
						 tissues_removed,
						 remarks FROM seg_or_main_post WHERE or_main_refno=$or_main_refno";


		if ($this->result = $db->Execute($query)) {
			if($this->count=$this->result->RecordCount()) {
			return $this->result->FetchRow();
			}
			else {
			return false;
			}
		}
		else {
			return false;
		}
		}
		else {
		return false;
		}
	}

	function get_seg_pharma_or_main($refno) {
		global $db;
		if ($refno) {
		$query = "SELECT pharma_refno FROM seg_pharma_or_main WHERE or_main_refno=$refno";
		# echo "ss = ".$query;
		if ($this->result = $db->Execute($query)) {
			if($this->count=$this->result->RecordCount()) {
			return $this->result->FetchRow();
			}
			else {
			return false;
			}
		}
		else {
			return false;
		}
		}
		else {
		return false;
		}
	}

	#added by CHE 07-06-2010
	function get_seg_pharma_orderRefno($encounter_nr, $pid) {
		global $db;
		if ($encounter_nr) {
		$this->sql = "SELECT * FROM seg_pharma_orders
								WHERE encounter_nr='$encounter_nr'
								AND pid='$pid' AND pharma_area='OR' LIMIT 1";
		# echo "ss = ".$this->sql;
		if ($this->result = $db->Execute($this->sql)) {
			if($this->count=$this->result->RecordCount()) {
			return $this->result->FetchRow();
			}
			else {
			return false;
			}
		}
		else {
			return false;
		}
		}
		else {
		return false;
		}
	}
	#---------------

	function get_events($month, $day, $year, $get_what) {
		global $db;
		if ($get_what == 'requests') {
		$this->sql = "SELECT sos.refno, time_format(sos.request_time, '%h:%i %p') as request_time,
						sos.ordername as patient_name, som.request_priority, 'request' as event from seg_ops_serv sos
						inner join care_encounter_op ceo on (sos.refno = ceo.refno)
						inner join seg_or_main som on (som.ceo_refno = sos.refno) where som.status IN ('request', 'approved')
						AND MONTHNAME(sos.request_date)='$month' AND DAYOFMONTH(sos.request_date)=$day
						AND YEAR(sos.request_date)=$year";
		}
		elseif ($get_what == 'operations') {
		$this->sql = "SELECT sos.refno, time_format(ceo.op_time, '%h:%i %p') as operation_time,
						sos.ordername as patient_name, som.request_priority, ceo.operator as surgeon,
						ceo.assistant as assistant_surgeon, ceo.an_doctor as anesthesiologist, 'operation' as event
						from seg_ops_serv sos inner join care_encounter_op ceo on (sos.refno = ceo.refno)
						inner join seg_or_main som on (som.ceo_refno = sos.refno) where som.status IN ('scheduled', 'post', 'pre_op')
						AND MONTHNAME(sos.request_date)='$month' AND DAYOFMONTH(sos.request_date)=$day
						AND YEAR(sos.request_date)=$year";
		}
		else {
		$this->sql = "SELECT sos.refno, time_format(ceo.op_time, '%h:%i %p') as joined_time,
						sos.ordername as patient_name, som.request_priority, ceo.operator as surgeon,
						ceo.assistant as assistant_surgeon, ceo.an_doctor as anesthesiologist, 'operation' as event
						from seg_ops_serv sos inner join care_encounter_op ceo on (sos.refno = ceo.refno)
						inner join seg_or_main som on (som.ceo_refno = sos.refno) where som.status IN ('scheduled', 'approved')
						AND MONTHNAME(sos.request_date)='$month' AND DAYOFMONTH(sos.request_date)=$day
						AND YEAR(sos.request_date)=$year

						UNION

						SELECT sos.refno, time_format(sos.request_time, '%h:%i %p') as request_time,
						sos.ordername as patient_name, som.request_priority, '', '', '', 'request' from
						seg_ops_serv sos inner join care_encounter_op ceo on (sos.refno = ceo.refno) inner join
						seg_or_main som on (som.ceo_refno = sos.refno) where som.status IN ('request', 'approved')
						AND MONTHNAME(sos.request_date)='$month' AND DAYOFMONTH(sos.request_date)=$day
						AND YEAR(sos.request_date)=$year";
		}
		$this->result = $db->Execute($this->sql);
		if ($this->result)  {
		return $this->result;
		}
		else {
		return false;
		}
	}

	function get_date_of_operation($refno) {
		global $db;
		$this->sql = "SELECT DATE_FORMAT(op_date, '%M %d, %Y') AS date_of_operation FROM care_encounter_op WHERE refno='$refno'";

		$this->result = $db->Execute($this->sql);
		if ($this->result->RecordCount()) {
		$row = $this->result->FetchRow();
		if ($row) {
			return $row['date_of_operation'];
		}
		else {
			return false;
		}
		}
		else {
		return false;
		}
	}



	/** February 26, 2009 See Maramag **/
	//edited by celsy 7/07/10
	function get_pre_op_checklist($or_main_refno, $source_area) {
		global $db;
		//fetch all checklist items without details and undeleted
		$this->sql = "SELECT seg_or_checklist.checklist_id, seg_or_checklist.checklist_question,
												 seg_or_checklist_main.is_mandatory
									FROM seg_or_checklist
									INNER JOIN seg_or_checklist_main
									ON seg_or_checklist_main.checklist_id=seg_or_checklist.checklist_id
									WHERE seg_or_checklist_main.source_area=$source_area
									AND seg_or_checklist.has_detail=0
									AND seg_or_checklist.is_deleted=0";
		if ($this->result = $db->Execute($this->sql)) {
			if ($this->result->RecordCount()) {
				$array = array();
				$array_selected = array();
				while ($row = $this->result->FetchRow()) {
					if( $row['is_mandatory']==1)
						$array[$row['checklist_id']] = $row['checklist_question'].'*';
					else
					$array[$row['checklist_id']] = $row['checklist_question'];
					$array_selected[] = $row['checklist_id'];
				}
				//$this->sql = "SELECT pre_op_checklist FROM seg_or_main WHERE or_main_refno=$or_main_refno";
				//fetch pre-checked q's w/o details if existing
				$this->sql =  "SELECT seg_or_checklist_preop_data.checklist_id
												FROM (seg_or_checklist_preop_data
												INNER JOIN seg_or_checklist
												ON seg_or_checklist.checklist_id=seg_or_checklist_preop_data.checklist_id)
												INNER JOIN seg_or_checklist_main
												ON seg_or_checklist_preop_data.checklist_id=seg_or_checklist_main.checklist_id
												WHERE seg_or_checklist.has_detail=0
												AND seg_or_checklist.is_deleted=0
												AND seg_or_checklist_main.source_area=$source_area
												AND seg_or_checklist_preop_data.refno=$or_main_refno";
				//"SELECT checklist_id FROM seg_or_checklist_preop_data WHERE refno=$or_main_refno";
				if ($this->result = $db->Execute($this->sql)) {
					if ($this->result->RecordCount()) {
						while($row2 = $this->result->FetchRow()){
								$selected[] = $row2['checklist_id'];
						}
							if (!empty($selected)) {
								$array_selected = $selected;
							}
					}
				}
				$multi = array('questions'=>$array, 'selected'=>$array_selected);
				return $multi;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	/**  February 26, 2009 See Maramag **/
 //edited by celsy 7/07/2010
	function update_pre_op_checklist($checklist, $or_main_refno,$source_area) {
		global $db;
		$no_error = true;
		$this->sql = "SELECT seg_or_checklist_main.checklist_id
					FROM seg_or_checklist_main
					INNER JOIN seg_or_checklist
					ON seg_or_checklist_main.checklist_id=seg_or_checklist.checklist_id
					WHERE seg_or_checklist_main.source_area=$source_area
					AND seg_or_checklist_main.is_mandatory=1
					AND seg_or_checklist.has_detail=0
					AND seg_or_checklist.is_deleted=0";
		//"SELECT checklist_id FROM seg_or_checklist_main WHERE source_area=1";
		$this->result = $db->Execute($this->sql);
		while ($row = $this->result->FetchRow()) {
			$array_mandatory[] = $row['checklist_id'];
		}
		//see if all mandatory items have been checked by the user
		$checked = explode(',',$checklist);
		$diff = array_diff($array_mandatory,$checked);
		//if not all mandatory items were checked then do not update dbase and return false
		if(!empty($diff)){
			return false;
		}
		//if all mandatory items were checked then update dbase
		else{
			//fetch all pre-checked data
			$this->sql = "SELECT seg_or_checklist_preop_data.checklist_id
										FROM seg_or_checklist_preop_data
										INNER JOIN seg_or_checklist
										ON seg_or_checklist.checklist_id=seg_or_checklist_preop_data.checklist_id
										WHERE seg_or_checklist.has_detail=0
										AND seg_or_checklist.is_deleted=0
										AND seg_or_checklist_preop_data.refno=$or_main_refno";
			//"SELECT checklist_id FROM seg_or_checklist_preop_data WHERE refno=$or_main_refno";
			$this->result = $db->Execute($this->sql);
			while ($row = $this->result->FetchRow()) {
				$db_checked[] = $row['checklist_id'];
			}
			if(empty($db_checked)){   //if the preop checklist data of the patient doesn't exist yet
				foreach($checked as $value){
					$this->sql = "INSERT INTO seg_or_checklist_preop_data (refno, checklist_id) VALUES ($or_main_refno, $value)";
					$db->Execute($this->sql);
					if($db->Affected_Rows() <= 0) {
						$no_error=false;
					}
				}
			}
			$checked_diff = array_diff($checked, $db_checked); //insert whatever was newly checked
			if(!empty($checked_diff)){
				foreach($checked_diff as $value){
					$this->sql = "INSERT INTO seg_or_checklist_preop_data (refno, checklist_id) VALUES ($or_main_refno, $value)";
					$db->Execute($this->sql);
					if($db->Affected_Rows() <= 0) {
						$no_error=false;
					}
				}
			}
			$unchecked = array_diff($db_checked, $checked);//delete if item was unchecked and not mandatory
			if(!empty($unchecked)){
				foreach($unchecked as $value){
					$this->sql = "DELETE FROM seg_or_checklist_preop_data WHERE refno=$or_main_refno AND checklist_id=$value";
					$db->Execute($this->sql);
					if($db->Affected_Rows() <= 0) {
						$no_error=false;
					}
				}
			}
		}
		if($no_error)	return true;
		else  				return false;
	}
	//added by celsy 07/09/10
	function get_pre_op_checklist_with_details($or_main_refno,$source_area) {
		global $db;
		$array_details = array();
		//fetch all checklist items with details and undeleted
		$this->sql = "SELECT seg_or_checklist.checklist_id, seg_or_checklist.checklist_question,
												 seg_or_checklist.label_data,  seg_or_checklist_main.is_mandatory
									FROM seg_or_checklist
									INNER JOIN seg_or_checklist_main
									ON seg_or_checklist.checklist_id=seg_or_checklist_main.checklist_id
									WHERE seg_or_checklist_main.source_area=$source_area
									AND seg_or_checklist.has_detail = 1
									AND seg_or_checklist.is_deleted=0";
		//"SELECT checklist_id, checklist_question, label_data FROM seg_or_checklist WHERE has_detail = 1";
		if ($this->result = $db->Execute($this->sql)) {
			if ($this->result->RecordCount()) {
				$array = array();
				$array_selected = array();
				$array_label = array();
				$details = array();
				while ($row = $this->result->FetchRow()) {
					if( $row['is_mandatory']==1)
						$array[$row['checklist_id']] = $row['checklist_question'].'*';
					else
					$array[$row['checklist_id']] = $row['checklist_question'];
					$array_selected[] = $row['checklist_id'];
					$array_label[$row['checklist_id']] = $row['label_data'];
					$array_details[$row['checklist_id']] = '';
				}
				//fetch pre-checked q's w/details if existing
				$this->sql = "SELECT seg_or_checklist_preop_data.checklist_id, seg_or_checklist_preop_data.add_detail
											FROM (seg_or_checklist_preop_data
											INNER JOIN seg_or_checklist
											ON seg_or_checklist.checklist_id=seg_or_checklist_preop_data.checklist_id)
											INNER JOIN seg_or_checklist_main
											ON seg_or_checklist_preop_data.checklist_id=seg_or_checklist_main.checklist_id
											WHERE seg_or_checklist.has_detail=1
											AND seg_or_checklist.is_deleted=0
											AND seg_or_checklist_main.source_area=$source_area
											AND seg_or_checklist_preop_data.refno=$or_main_refno";
				if ($this->result = $db->Execute($this->sql)) {
					if ($this->result->RecordCount()) {
						while($row2 = $this->result->FetchRow()){
							$selected[] = $row2['checklist_id'];
							$array_details[$row2['checklist_id']] = $row2['add_detail'];
						}
						if (!empty($selected)) {
							$array_selected = $selected;
							//$array_details = $details;
						}
					}
				}
							/*	//fetch questions with details if list has been prechecked
		$this->sql = "SELECT seg_or_checklist_preop_data.checklist_id, seg_or_checklist_preop_data.add_detail
									FROM (seg_or_checklist
									INNER JOIN seg_or_checklist_preop_data
									ON seg_or_checklist.checklist_id=seg_or_checklist_preop_data.checklist_id)
									INNER JOIN seg_or_checklist_main
									ON seg_or_checklist.checklist_id=seg_or_checklist_main.checklist_id
									WHERE seg_or_checklist.has_detail=1
											AND seg_or_checklist.is_deleted=0
									AND seg_or_checklist_main.source_area=1
									AND seg_or_checklist_preop_data.refno=$or_main_refno";
		if ($this->result = $db->Execute($this->sql)) {
			if ($this->result->RecordCount()) {
				while ($row = $this->result->FetchRow()) {
					$array_details[$row['checklist_id']] = $row['add_detail'];
				}
			}
			else {
					return false;
			}
				}	*/
				if(empty($array)){
			return false;
		}
		else{
			$multi = array('questions2'=>$array, 'selected2'=>$array_selected, 'labels'=>$array_label, 'details'=>$array_details);
			return $multi;
		}
	}
		}
		else {
				return false;
		}

	}


	function update_pre_op_checklist_with_details($checklist2, $detail, $or_main_refno,$source_area) {
		global $db;
		$no_error = true;
		$checklist2 = substr($checklist2,0,-1);
		$detail = substr($detail,0,-1);
		$checked = explode(',',$checklist2);
		$details = explode(',',$detail);

		$this->sql ="SELECT seg_or_checklist_main.checklist_id
					FROM seg_or_checklist_main
					INNER JOIN seg_or_checklist
					ON seg_or_checklist_main.checklist_id=seg_or_checklist.checklist_id
					WHERE seg_or_checklist_main.source_area=$source_area
					AND seg_or_checklist_main.is_mandatory=1
					AND seg_or_checklist.has_detail=1
					AND seg_or_checklist.is_deleted=0";
		$this->result = $db->Execute($this->sql);
		while ($row = $this->result->FetchRow()) {
			$array_mandatory[] = $row['checklist_id'];
		}
		$diff = array_diff($array_mandatory,$checked);
		//if not all mandatory items were checked then do not update dbase and return false
		if(!empty($diff)){
			return false;
		}
		//if all mandatory items were checked then update dbase
		else{
			//fetch all pre-checked data
			$this->sql = "SELECT seg_or_checklist_preop_data.checklist_id
										FROM seg_or_checklist_preop_data
										INNER JOIN seg_or_checklist
										ON seg_or_checklist.checklist_id=seg_or_checklist_preop_data.checklist_id
										WHERE seg_or_checklist.has_detail=1
										AND seg_or_checklist.is_deleted=0
										AND seg_or_checklist_preop_data.refno=$or_main_refno";

										/*"SELECT seg_or_checklist_preop_data.checklist_id
//										FROM (seg_or_checklist_preop_data
//										INNER JOIN seg_or_checklist
//										ON seg_or_checklist.checklist_id=seg_or_checklist_preop_data.checklist_id)
//										INNER JOIN seg_or_checklist_main
//										ON seg_or_checklist.checklist_id=seg_or_checklist_main.checklist_id
//										WHERE seg_or_checklist.has_detail=1
//										AND seg_or_checklist.is_deleted=0
//										AND seg_or_checklist_main.source_area=1
//										AND seg_or_checklist_preop_data.refno=$or_main_refno";     */
			$this->result = $db->Execute($this->sql);
			while ($row = $this->result->FetchRow()) {
				$db_checked[] = $row['checklist_id'];
			}
			$checked_diff = array_diff($checked, $db_checked); //insert whatever was newly checked
//				 print_r(" checked ");
//				 print_r($checked);
//				 print_r(" checked_diff ");
//				 print_r($checked_diff);
//				 print_r(" db_checked ");
//				 print_r($db_checked);
			if(empty($db_checked)||!empty($checked_diff)){
				if(empty($db_checked)){
					foreach($checked as $value){
						$this->sql = "INSERT INTO seg_or_checklist_preop_data (refno, checklist_id) VALUES ($or_main_refno, $value)";
						$db->Execute($this->sql);
						if($db->Affected_Rows() <= 0) {
							$no_error=false;
						}
					}
				}
			if(!empty($checked_diff)){
				foreach($checked_diff as $value){
						$this->sql = "INSERT INTO seg_or_checklist_preop_data (refno, checklist_id) VALUES ($or_main_refno, $value)";
						$db->Execute($this->sql);
						if($db->Affected_Rows() <= 0) {
							$no_error=false;
						}
					}
				}

			}

//			print_r("sa update ");
//			print_r($checked);
			//insert all details for the new data
			foreach($checked as $value){
//					print_r($value);
					$temp = array_keys($checked, $value);
					$checked_detail = $details[$temp[0]];
//					print_r($checked_detail);
					$this->sql = "UPDATE seg_or_checklist_preop_data
												SET add_detail=".$db->qstr($checked_detail)."
												WHERE refno = $or_main_refno AND checklist_id = $value";
					//"INSERT INTO seg_or_checklist_preop_data (refno, checklist_id, add_detail) VALUES ($or_main_refno, $value, ".$db->qstr($checked_detail).")";

					if(!$db->Execute($this->sql)) {
						$no_error=false;
					}
				}



/*			$checked_diff = array_diff($checked, $db_checked); //insert whatever was newly checked
//			if(!empty($checked_diff)){
//				foreach($checked_diff as $value){
//					$this->sql = "INSERT INTO seg_or_checklist_preop_data (refno, checklist_id) VALUES ($or_main_refno, $value)";
//					if(!$db->Execute($this->sql)) {
//						$no_error=false;
//					}
//				}
//			}  */
			$unchecked = array_diff($db_checked, $checked);//delete if item was unchecked and not mandatory
//			print_r(" unchecked ");
//			print_r($unchecked);
			if(!empty($unchecked)){
				foreach($unchecked as $value){
					$this->sql = "DELETE FROM seg_or_checklist_preop_data WHERE refno=$or_main_refno AND checklist_id=$value";
					$db->Execute($this->sql);
					if($db->Affected_Rows() <= 0) {
						$no_error=false;
					}
				}
			}
		}


		if($no_error)	return true;
		else  			return false;
	}


	//-------------added by celsy 7/13/10-----------//
	function get_or_checklist_items($item_id) {
		global $db;
		$array = array();
		$array_selected = array();
		$array_areas = array();
		if($item_id!=0){
			$this->sql = "SELECT DISTINCT soc.checklist_id, soc.checklist_question,
										soc.has_detail, soc.label_data, socm.is_mandatory
										FROM seg_or_checklist soc
										INNER JOIN seg_or_checklist_main socm
										ON socm.checklist_id=soc.checklist_id
										WHERE soc.checklist_id=$item_id";
			if ($this->result = $db->Execute($this->sql)) {
				if ($this->result->RecordCount()) {
					$row = $this->result->FetchRow();
					$array['c_id'] = $row['checklist_id'];
					$array['c_question'] = $row['checklist_question'];
					$array['has_detail'] = $row['has_detail'];
					$array['label'] = $row['label_data'];
					$array['is_mandatory'] = $row['is_mandatory'];
				}
			}
		}
		$this->sql = "SELECT source_area FROM seg_or_checklist_main WHERE checklist_id=$item_id";
		if ($this->result = $db->Execute($this->sql)) {
				while($row2 = $this->result->FetchRow()){
						$array_selected[] = $row2['source_area'];
				}
		}
		$this->sql = "SELECT id, description FROM seg_or_checklist_areas ORDER BY id asc";
		if ($this->result = $db->Execute($this->sql)) {
				while($row2 = $this->result->FetchRow()){
						$array_areas[$row2['id']] = $row2['description'];
				}
		}
		$multi = array('item_details'=>$array, 'selected'=>$array_selected, 'checklist_areas'=>$array_areas);
		return $multi;
	}
	//added by celsy 07/16/10
	function insert_checklist($selected_questions, $data){
		global $db, $HTTP_SESSION_VARS;
		$no_error = true;
		$selected_questions = explode(',',$selected_questions);
//		print_r("CLASS CELSY");
//		print_r($selected_questions);
//		print_r($data);
		extract($data);
		if($is_detail==0){
			 $label='';
		}
		$history = "Created ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";
//		$history = $this->ConcatHistory("Updated ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		$this->sql = "INSERT INTO seg_or_checklist
									(checklist_id, checklist_question, has_detail, label_data, history,date_created, created_id)
									VALUES ('',".$db->qstr($new_question).",".$is_detail.", ".$db->qstr($label).",".$db->qstr($history).", NOW(), ".$db->qstr($HTTP_SESSION_VARS['sess_user_name']).")";

		if(!$db->Execute($this->sql)) {
						$no_error=false;
		}

		$this->sql = "SELECT checklist_id FROM seg_or_checklist ORDER BY checklist_id DESC LIMIT 1";
		$this->result = $db->Execute($this->sql);
		$row = $this->result->FetchRow();
		$checklist_id = $row['checklist_id'];

		foreach($selected_questions as $value){
			$this->sql = "INSERT INTO seg_or_checklist_main
										(source_area, checklist_id, is_mandatory)
										VALUES ($value,$checklist_id,$is_mandatory)";
			if(!$db->Execute($this->sql))
				$no_error=false;
		}

		if($no_error)	return true;
		else  				return false;
	}

	function delete_checklist($checklist_id){
		return true;
	}

	function edit_checklist($selected_questions, $data){
		global $db, $HTTP_SESSION_VARS;
		$no_error = true;
		$selected_questions = explode(',',$selected_questions);
		//print_r("CLASS CELSY");
//		print_r($selected_questions);
//		print_r($data);
		extract($data);
		if($is_detail==0){
			 $label='';
		}

		$history = $this->ConcatHistory("Updated ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
//		$history = "Created ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";
		$this->sql = "UPDATE seg_or_checklist
							SET checklist_question = ".$db->qstr($new_question).", has_detail = $is_detail,
							label_data= ".$db->qstr($label).", date_modified=NOW(),
							modified_id= ".$db->qstr($HTTP_SESSION_VARS['sess_user_name']).",history= ".$history."
							WHERE checklist_id=$checklist_id";

		if(!$db->Execute($this->sql)) {
						$no_error=false;
		}

		//fetch all checked items
		$this->sql = "SELECT source_area FROM seg_or_checklist_main WHERE checklist_id=$checklist_id";
		$this->result = $db->Execute($this->sql);
		while ($row = $this->result->FetchRow()) {
			$array_checked[] = $row['source_area'];
	}
		$checked_diff = array_diff($selected_questions, $array_checked);   //insert whatever was newly checked
			if(!empty($checked_diff)){
				foreach($checked_diff as $value){
					$this->sql = "INSERT INTO seg_or_checklist_main (source_area, checklist_id) VALUES ($value,$checklist_id)";
					if(!$db->Execute($this->sql))
						$no_error=false;
				}
			}
			$unchecked = array_diff($array_checked, $selected_questions);//delete if item was unchecked and not mandatory
			if(!empty($unchecked)){
				foreach($unchecked as $value){
					$this->sql = "DELETE FROM seg_or_checklist_main WHERE source_area=$value AND checklist_id=$checklist_id";
					if(!$db->Execute($this->sql))
						$no_error=false;
				}
			}

		$this->sql = "UPDATE seg_or_checklist_main SET is_mandatory=$is_mandatory	WHERE checklist_id=$checklist_id";
		if(!$db->Execute($this->sql)) {
						$no_error=false;
		}

		if($no_error)	return true;
		else  				return false;
	}


	//-----------------------------CELSY end-------------------------------//


	/**  February 27, 2009 See Maramag **/

	function get_or_types() {
		global $db;
		$this->sql = "SELECT * FROM seg_or_type";
		if ($this->result = $db->Execute($this->sql)) {
		if ($this->result->RecordCount()) {
			$array = array();
			while ($row = $this->result->FetchRow()) {
			$array[$row['or_type_acro']] = $row['or_type_description'];
			}
			return $array;
		}
		else {
			return false;
		}
		}
		else {
		return false;
		}
	}

	function add_sponge_item_by_bulk($sponge_array) {
	global $db;
	extract($sponge_array);
	$sponge_items = array();
	foreach ($sponges as $key => $sponge_value) {
		$items_array = array($sponge_value, $first_count_on_table[$key], $first_count_on_floor[$key],
							 $second_count_on_table[$key], $second_count_on_floor[$key], $sponges_quantity[$key]);
		$sponge_items[] = $items_array;
	}

	$index = 'or_main_refno, sponge_code, f_count_table, f_count_floor, s_count_table, s_count_floor, initial_count';
	$values = "$or_main_refno, ?, ?, ?, ?, ?, ?";

	$query = "INSERT INTO seg_or_sponge ($index) VALUES ($values)";

	$result = $db->Execute($query, $sponge_items);
	if (!$result) {
		$error[] = 'Stage 1 Error';
	}
	}


	function remove_sponges($or_main_refno) {
	global $db;
	$this->sql = "DELETE FROM seg_or_sponge WHERE or_main_refno=$or_main_refno";
	$this->result = $db->Execute($this->sql);

	if ($this->result) {
		return true;
	}
	else {
		return false;
	}
	}

	function get_rvu($ref_nr='') {
		global $db;
		if(empty($ref_nr) || (!$ref_nr)){
			return FALSE;
		}
		$this->_useOpsServDetails();
		$this->sql="SELECT osd.*,(SELECT sop.ops_charge
								FROM seg_ops_personell AS sop
								WHERE sop.refno=osd.refno  AND sop.ops_code=osd.ops_code
								LIMIT 1
							) AS ops_charge,
					icpm.description, icpm.rvu AS icpm_rvu, osd.multiplier AS icpm_multiplier
					FROM seg_ops_servdetails AS osd
					LEFT JOIN seg_ops_rvs AS icpm ON icpm.code = osd.ops_code
					WHERE osd.refno='$ref_nr'";

		if ($this->result=$db->Execute($this->sql)){
		if($this->count=$this->result->RecordCount()) {
			$array = array();
			while ($row = $this->result->FetchRow()) {
			$item = array('rvu' => $row['icpm_rvu'],
							'rvs_code' => $row['ops_code'],
							'description' => $row['description']);
			$array[] = $item;
			}
			return $array;
		}
		else {
			return false;
		}
		}
		else {
		return FALSE;
		}
	}

	function get_sponges($or_main_refno) {
		global $db;
		$this->sql = "SELECT sos.sponge_code, cppm.artikelname as sponge_name, sos.f_count_table,
					sos.f_count_floor, sos.s_count_table, sos.s_count_floor, sos.initial_count
					FROM seg_or_sponge sos INNER JOIN care_pharma_products_main cppm ON
					(sos.sponge_code=cppm.bestellnum) WHERE sos.or_main_refno=$or_main_refno";
		if ($this->result = $db->Execute($this->sql)) {
		if($this->count=$this->result->RecordCount()) {
			$array = array();
			while ($row = $this->result->FetchRow()) {
			$item = array('sponge_name' => $row['sponge_name'],
							'initial_count' => $row['initial_count'],
							'first_count_on_table' => $row['f_count_table'],
							'first_count_on_floor' => $row['f_count_floor'],
							'first_count_total' => ((int)$row['f_count_table'] + (int)$row['f_count_floor']),
							'second_count_on_table' => $row['s_count_table'],
							'second_count_on_floor' => $row['s_count_floor'],
							'second_count_total' => ((int)$row['s_count_table'] + (int)$row['s_count_floor']));
			$array[] = $item;
			}
			return $array;
		}
		else {
			return false;
		}
		}
		else {
		return FALSE;
		}
	}


	 function get_or_daily_report($date) {
	 global $db;

	 $this->sql = "SELECT time_format(somp.time_started, '%h:%i %p') as time_started,
					 time_format(somp.time_finished, '%h:%i %p'),
					 UPPER(CONCAT_WS(', ', cp.name_last, cp.name_first)) as patient_name,
					 FLOOR(fn_calculate_age(cp.date_birth, CURRENT_DATE)) as patient_age,
					 UPPER(cp.sex) as sex,
					 cp.civil_status,
					 som.pre_op_diagnosis,
					 somp.operation_performed,
					 somp.post_op_diagnosis,
					 (SELECT CONCAT_WS(' ', cp.title, cp.name_first, cp.name_last) AS doctor_name
					 FROM seg_ops_personell sop
					 INNER JOIN care_personell cpe ON (sop.dr_nr=cpe.nr)
					 INNER JOIN care_person cp ON (cp.pid=cpe.pid)
					 WHERE sop.role_type_nr=7
					 LIMIT 1) as surgeon,
					 (SELECT CONCAT_WS(' ', cp.title, cp.name_first, cp.name_last) AS doctor_name
					 FROM seg_ops_personell sop
					 INNER JOIN care_personell cpe ON (sop.dr_nr=cpe.nr)
					 INNER JOIN care_person cp ON (cp.pid=cpe.pid)
					 WHERE sop.role_type_nr=12
					 LIMIT 1) as anesthesiologist, cta.name as anesthesia
					 FROM seg_or_main_post somp
					 INNER JOIN seg_or_main som ON (somp.or_main_refno=som.or_main_refno)
					 INNER JOIN seg_ops_serv sos ON (sos.refno=som.ceo_refno)
					 INNER JOIN care_person cp ON (cp.pid=sos.pid)
					 INNER JOIN seg_encounter_anesthesia sea ON (sea.or_main_refno=som.or_main_refno)
					 INNER JOIN care_type_anaesthesia cta ON (cta.nr=sea.anesthesia)
					 INNER JOIN care_encounter_op ceo ON (som.ceo_refno=ceo.nr)
					 WHERE ceo.op_date='$date'";

	 $this->result = $db->Execute($this->sql);
	 if ($this->result) {
		 if ($this->result->RecordCount()) {
		 $iterator = 1;
		 $array = array();
		 while ($row = $this->result->FetchRow()) {
			 switch ($row['civil_status']) {
			 case 'child':
				 $civil_status = 'C';
			 break;
			 case 'single':
				 $civil_status = 'S';
			 break;
			 case 'married':
				 $civil_status = 'M';
			 break;
			 case 'divorced':
				 $civil_status = 'D';
			 break;
			 case 'widowed':
				 $civil_status = 'W';
			 break;
			 case 'separated':
				 $civil_status = 'SP';
			 break;
			 default:
				 $civil_status = '';
			 break;
			 }
			 $array[] = array('no'=>$iterator,
							'time_started'=>$row['time_started'],
							'time_finished'=>$row['time_finished'],
							'patient_name'=>$row['patient_name'],
							'patient_age'=>$row['patient_age'],
							'patient_sex'=>$row['sex'],
							'patient_status'=>$civil_status,
							'pre_op_diagnosis'=>$row['pre_op_diagnosis'],
							'operation_performed'=>$row['operation_performed'],
							'post_op_diagnosis'=>$row['post_op_diagnosis'],
							'surgeon'=>$row['surgeon'],
							'anesthesiologist'=>$row['anesthesiologist'],
							'anesthesia'=>$row['anesthesia']
							);

			 $iterator++;
		 }
		 return $array;
		 }
		 else {
		 return false;
		 }
	 }
	 else {
		 return false;
	 }
	 }

	 function update_or_deaths($data) {
	 global $db;
	 extract($data);
	 $author = $_SESSION['sess_user_name'];
	 $this->sql = "SELECT COUNT(or_main_refno) as or_death FROM or_main_death WHERE or_main_refno=$or_main_refno";
	 $this->result = $db->Execute($this->sql);
	 $row = $this->result->FetchRow();
	 if ($row['or_death'] > 0) {
		 $history = 'Updated '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name']." \n";
		 $this->sql = "UPDATE or_main_death SET date_time_of_death='$date_time_of_death',
												cause_of_death='$cause_of_death',
												patient_classification=$patient_classification,
												death_time_range=$death_time_range,
												history=CONCAT(history, '$history'),
												modified_id='$author',
												modified_date=NOW() WHERE or_main_refno=$or_main_refno";

		 $db->Execute($this->sql);
		 if($db->Affected_Rows()) {
		 return true;
		 }
		 else {
		 return false;
		 }
	 }
	 else {
		 $this->sql = "INSERT INTO or_main_death(  or_main_refno
												 , date_time_of_death
												 , cause_of_death
												 , patient_classification
												 , death_time_range
												 , history
												 , created_id
												 , modified_id
												 , created_date
												 , modified_date) VALUES (  $or_main_refno
																		, '$date_time_of_death'
																		, '$cause_of_death'
																		, $patient_classification
																		, $death_time_range
																		, '$history'
																		, '$author'
																		, '$author'
																		, NOW()
																		, NOW())";

		$db->Execute($this->sql);
		if ($db->Affected_Rows()) {
			return true;
		}
		else {
			return false;
		}
	 }

	 }

	 function get_death_details($or_main_refno) {
	 global $db;
	 $this->sql = "SELECT or_main_refno, date_time_of_death, cause_of_death, patient_classification, death_time_range
					 FROM or_main_death WHERE or_main_refno = '$or_main_refno'";
	 $this->result = $db->Execute($this->sql);
	 if ($this->result->RecordCount()) {
		 $row = $this->result->FetchRow();
		 if ($row) {
		 return $row;
		 }else {
		 return false;
		 }
	 }
	 else {
		 return false;
	 }
	 }

	 function get_sponge_count($or_main_refno) {
	 global $db;
	 $this->sql = "SELECT sos.*, cppm.artikelname FROM seg_or_sponge sos
					INNER JOIN care_pharma_products_main cppm
					ON (sos.sponge_code = cppm.bestellnum) WHERE sos.or_main_refno=$or_main_refno";
	 $this->result = $db->Execute($this->sql);
	 if ($this->result) {
		 return $this->result;
	 }
	 else {
		 return false;
	 }
	 }

	 function get_or_type($or_main_refno)  {
	 global $db;
	 $this->sql = "SELECT or_type FROM seg_or_main WHERE or_main_refno=$or_main_refno";
	 $this->result = $db->Execute($this->sql);
	 if ($this->result) {
		 if ($this->result->RecordCount()==1) {
		 $row = $this->result->FetchRow();
		 return $row['or_type'];
		 }
		 else {
		 return false;
		 }
	 }
	 else {
		 return false;
	 }
	 }

	 function get_or_main_status($or_main_refno) {
	 global $db;
	 $this->sql = "SELECT status FROM seg_or_main WHERE or_main_refno=$or_main_refno";
	 $this->result = $db->Execute($this->sql);
	 if ($this->result) {
		 if ($this->result->RecordCount()==1) {
		 $row = $this->result->FetchRow();
		 return $row['status'];
		 }
		 else {
		 return false;
		 }
	 }
	 else {
		 return false;
	 }
	 }

	 //added by CHA 10-09-09
	 function saveORDelivery($or_delivery_details)
	 {
		global $db;
		extract($or_delivery_details);
		#edited by cha 11-10-09
		$this->sql = "INSERT INTO seg_or_delivery ".
									"( refno, pid, encounter_nr, gravida, para, abortion, prenatal_care, pregnancy_complications, blood_type,
									heart, lungs, bp_1, pulse_1, general_condition, general_condition_others,membrane_ruptured, cervix_cm, cervix_condition,
									onset_date_time, dilation_date_time, childborn_date_time, ergonovine_date_time, labor_duration_hour, labor_duration_min,
									delivery_spont,	blood_given, operative, episiotomy, perineal_tear, analgesic_given, anesthesia_given, complications, fundus,
									umbiculus,
									post_bp, post_temp, post_pulse, post_resprate, bleeding, labor_onset, date_confinement, deliver_dr) VALUES".
					 "( '$ref_no', '$pid', '$encounter_nr', '$gravida', '$para', '$abortion', ".
									"'$prenatal_care', '$pregnancy_complications', '$blood_type', '$heart', '$lungs', '$bp_1', '$pulse_1', '$general_condition',".
									"'$general_condition_others',".
									"'$membrane_ruptured', '$cervix_cm', '$cervix_condition', ".
									"'".date("Y-m-d H:i:s",strtotime($onset_date_time))."', '".date("Y-m-d H:i:s",strtotime($dilation_date_time))."',".
									"'".date("Y-m-d H:i:s",strtotime($childborn_date_time))."', '".date("Y-m-d H:i:s",strtotime($ergonovine_date_time))."',".
									"'$labor_duration_hour', '$labor_duration_minute', '$delivery_spont', '$blood_given', '$operative', '$episiotomy', ".
									"'$perineal_tear', '$analgesic_given', '$anesthesia_given', '$complications', '$fundus', '$umbiculus', ".
									"'$post_bp', '$post_temp', '$post_pulse', '$post_resprate', '$bleeding', '$labor_onset', '".date("Y-m-d H:i:s",strtotime($date_confinement))."', '$deliver_dr')";

		#echo "<br><br>query: ".$this->sql;
		$db->Execute($this->sql);
		if ($db->Affected_Rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	 }
	 #added by cha 11-11-09
	 function updateOrDelivery($or_delivery_details)
		{
		global $db;
		extract($or_delivery_details);
		$this->sql = "UPDATE seg_or_delivery SET gravida=".$db->qstr($gravida).
							", para=".$db->qstr($para).", abortion=".$db->qstr($abortion).", prenatal_care=".$db->qstr($prenatal_care).
							", pregnancy_complications=".$db->qstr($pregnancy_complications).", blood_type=".$db->qstr($blood_type).
							", heart=".$db->qstr($heart).", lungs=".$db->qstr($lungs).", bp_1=".$db->qstr($bp_1).", pulse_1=".$db->qstr($pulse_1).
							", general_condition=".$db->qstr($general_condition).", membrane_ruptured=".$db->qstr($membrane_ruptured).
							", general_condition_others=".$db->qstr($general_condition_others).
							", cervix_cm=".$db->qstr($cervix_cm).", cervix_condition=".$db->qstr($cervix_condition).
							", onset_date_time=".$db->qstr(date("Y-m-d H:i:s",strtotime($onset_date_time))).
							", dilation_date_time=".$db->qstr(date("Y-m-d H:i:s",strtotime($dilation_date_time))).
							", childborn_date_time=".$db->qstr(date("Y-m-d H:i:s",strtotime($childborn_date_time))).
							", ergonovine_date_time=".$db->qstr(date("Y-m-d H:i:s",strtotime($ergonovine_date_time))).
							", labor_duration_hour=".$db->qstr($labor_duration_hour).", labor_duration_min=".$db->qstr($labor_duration_minute).
							", delivery_spont=".$db->qstr($delivery_spont).", blood_given=".$db->qstr($blood_given).
							", operative=".$db->qstr($operative).", episiotomy=".$db->qstr($episiotomy).", perineal_tear=".$db->qstr($perineal_tear).
							", analgesic_given=".$db->qstr($analgesic_given).", anesthesia_given=".$db->qstr($anesthesia_given).
							", complications=".$db->qstr($complications).", fundus=".$db->qstr($fundus).", umbiculus=".$db->qstr($umbiculus).
							", post_bp=".$db->qstr($post_bp).", post_temp=".$db->qstr($post_temp).", post_pulse=".$db->qstr($post_pulse).
							",post_resprate=".$db->qstr($post_resprate).", labor_onset=".$db->qstr($labor_onset).
							",bleeding=".$db->qstr($bleeding).", date_confinement=".$db->qstr(date("Y-m-d H:i:s",strtotime($date_confinement))).
							",deliver_dr=".$db->qstr($deliver_dr)." where refno=".$db->qstr($ref_no);

	# echo "<br><br>query: ".$this->sql;
		$db->Execute($this->sql);
		if ($db->Affected_Rows())
		{
			return true;
		}
		else
		{
		#echo "<br><br>error: ".$db->ErrorMsg();
		return false;
		}
	 }
	 //end CHA

	 //added by omick october 07, 2009
	 function get_misc_details($code, $src) {
	 global $db;
	 $this->sql = 0;
	 if ($src == '1') {
		 $this->sql = "SELECT service_code, name, description, price FROM seg_other_services WHERE service_code='$code'";
	 }
	 if ($src == '2') {
		 $this->sql = "SELECT service_code, name, price FROM seg_otherhosp_services WHERE status NOT IN ('deleted','hidden','inactive','void') AND
					 service_code='$code'";
	 }
	 if ($this->sql) {
		 $this->result = $db->Execute($this->sql);
		 if ($this->result) {
		 if ($this->result->RecordCount()==1) {
			 return $this->result->FetchRow();
		 }
		 }
		 else {
		 return false;
		 }
	 }
	 else {
		 return false;
	 }

	 }

	 function get_pharma_order_mode($encounter_nr, $area) {
	 global $db;
	 $this->sql = "SELECT refno FROM seg_pharma_orders WHERE encounter_nr='$encounter_nr' AND pharma_area='$area' ORDER BY orderdate DESC";

	 $this->result = $db->Execute($this->sql);
	 if ($this->result) {
		 if ($this->result->RecordCount() >= 1) {
			$row = $this->result->FetchRow();
			return $row['refno'];
		 }
		 else {
		 return 0;
		 }
	 }
	 else {
		 return 0;
	 }
	 }

	 function get_encounter_or_type($encounter) {
	 global $db;
	 $this->sql = "select som.or_type from care_encounter_op ceo inner join seg_or_main som on (som.ceo_refno = ceo.refno) where ceo.encounter_nr='$encounter_nr'";
	 $this->result = $db->Execute($this->sql);
	 if ($this->result) {
		 if ($this->result->RecordCount() == 1) {
		 return $this->result->FetchRow();
		 }
		 else {
		 return false;
		 }
	 }
	 else {
		 return false;
	 }
	 }

	 function get_ward_name($ward_nr) {
	 global $db;
	 $this->sql = "select ward_id FROM care_ward WHERE nr='$ward_nr'";
	 $this->result = $db->Execute($this->sql);
	 if ($this->result) {
		 if ($this->result->RecordCount() == 1) {
		 return $this->result->FetchRow();
		 }
		 else {
		 return false;
		 }
	 }
	 else {
		 return false;
	 }
	 }

	function remove_accommodation($details) {
	global $db;
	extract($details);
	$room_nr = implode(',', $room_nr);
	$this->sql = "DELETE FROM seg_encounter_location_addtl WHERE room_nr IN ($room_nr) AND encounter_nr='$encounter_nr' AND area='$area'";
	$db->Execute($this->sql);
	}

	 function get_accommodation($encounter_nr, $area) {
	 global $db;
		//modified by cha, 09-29-2010
		 /*$this->sql = "select cw.name,cw.nr,od.group_nr,cr.info as `room_label`,od.room_nr,oc.rvu,oc.multiplier, od.charge, oa.chrge_dte, oa.refno,oa.modify_id, oa.create_id, oa.modify_dt
									from seg_opaccommodation as oa left join seg_opaccommodation_details as od on oa.refno=od.refno
									left join seg_ops_chrgd_accommodation as oc on oc.refno=od.refno
									left join care_ward as cw on cw.nr=od.group_nr
									left join care_room as cr on cr.nr=od.room_nr
									where oa.encounter_nr=".$db->qstr($encounter_nr); */
		 $this->sql = "SELECT oa.refno,cw.name,cw.nr,od.group_nr,cr.room_nr as `room_label`, od.room_nr, \n".
														 "(SELECT SUM(oc.rvu) FROM seg_ops_chrgd_accommodation as oc \n".
																"WHERE oc.refno=od.refno AND oc.entry_no=od.entry_no) `rvu`,\n".
														 "oc.multiplier, od.charge, oa.chrge_dte, oa.refno,oa.modify_id, oa.create_id, oa.modify_dt\n".
														 "FROM seg_opaccommodation AS oa \n".
														 "LEFT JOIN seg_opaccommodation_details AS od on oa.refno=od.refno\n".
														 "LEFT JOIN seg_ops_chrgd_accommodation AS oc on oc.refno=od.refno \n".
														 "LEFT JOIN care_ward AS cw on cw.nr=od.group_nr \n".
														 "LEFT JOIN care_room AS cr on cr.nr=od.room_nr \n".
														 "WHERE oa.encounter_nr=".$db->qstr($encounter_nr)." \n".
														 "GROUP BY name";

	 $this->result = $db->Execute($this->sql);
	 if ($this->result) {
		 if ($this->result->RecordCount() > 0) {
		 return $this->result;
		 }
		 else {
		 return false;
		 }
	 }
	 else {
		 return false;
	 }
	 }

	 //added by cha, sept 29, 2010
	 function get_room_accommodation($encounter_nr)
	 {
		 global $db;
		 $this->sql = "SELECT l.group_nr `ward_nr`, w.name `ward`, l.room_nr, r.room_nr  `room`, l.days_stay, l.hrs_stay, l.rate, create_dt `chrge_dte`
							FROM seg_encounter_location_addtl AS l
							LEFT JOIN care_ward AS w ON l.group_nr=w.nr
							LEFT JOIN care_room AS r ON l.room_nr=r.nr
							WHERE l.encounter_nr=".$db->qstr($encounter_nr);
		 $this->result = $db->Execute($this->sql);
		 if ($this->result) {
			 if ($this->result->RecordCount() > 0) {
				return $this->result;
			 }
			 else return FALSE;
		 }
		 else return FALSE;
	 }
	 //end cha

	 /*function save_misc_charges($details) {
	 global $db;
		 $db->StartTrans();
	 extract($details);
	 $refno = $this->get_misc_refno($encounter_nr, $area);
	 $no_error = false;
		 //echo "refno:".$refno."<br>";
	 if ($refno) { //edit
		 //echo "edit<br>";
		 //$amount_due = $this->calculate_total_misc_order(array('misc'=>$misc, 'quantity'=>$quantity));
		 if ($no_error = $this->update_misc_order($refno)) {
		 if ($no_error = $this->delete_misc_order_items($refno)) {
			 if ($no_error = (count($misc) > 0) && (count($misc)==count($quantity))) {
			 $no_error = $this->add_misc_order_items_by_bulk(array('misc'=>$misc, 'quantity'=>$quantity, 'price'=>$price, 'account_type'=>$account_type, 'refno'=>$refno));
			 }
		 }
		 }
	 }
	 else {
			// echo "new<br>";
			 $refno = $this->get_new_misc_refno((date('Y').'00000001'));
			// echo "new refno:".$refno."<br>";
			 if ($no_error = (count($misc) > 0) && (count($misc)==count($quantity))) {
				 if ($no_error = $this->add_misc_order($refno, $encounter_nr, $charge_date, $area)) {
					 //echo "added header <br>";
					 $no_error = $this->add_misc_order_items_by_bulk(array('misc'=>$misc, 'quantity'=>$quantity, 'price'=>$price, 'account_type'=>$account_type, 'refno'=>$refno));

				 }
			 }
		 }
		 if ($no_error) {
			 $db->CompleteTrans();
			 //echo "complete";
			 return true;
		 }
		 else {
			//echo "ERROR3:".$this->getErrorMsg();
			 $db->FailTrans();
			 return false;
		 }
	 }*/

	 #---added by CHA, 05-06-2010 (modified version of saving miscellaneous charges)
	 /*function save_misc_charges($details)
	 {
		 //echo "new saving<br><br>";
			global $db;
			extract($details);
			$refno = $this->get_misc_refno($encounter_nr, $area);
			$no_error = false;
			if ($refno) 	//edit
			{
				$no_error = $this->update_miscellaneous_charges($details,$refno);
			}
			else	//new
			{
		 $refno = $this->get_new_misc_refno((date('Y').'00000001'));
		 if ($no_error = (count($misc) > 0) && (count($misc)==count($quantity))) {
		 if ($no_error = $this->add_misc_order($refno, $encounter_nr, $charge_date, $area)) {
			 $no_error = $this->add_misc_order_items_by_bulk(array('misc'=>$misc, 'quantity'=>$quantity, 'price'=>$price, 'account_type'=>$account_type, 'refno'=>$refno));
		 }
		 }
	 }
	 if ($no_error) {
				 //echo "complete";
		 return true;
	 }
	 else {
				//echo "final ERROR";
		 return false;
	 }
	 }

	 function update_miscellaneous_charges($details,$refno)
	 {
		global $db;
		extract($details);
		$author = $_SESSION['sess_user_name'];
		$this->sql = "UPDATE seg_misc_chrg SET modify_id='$author', modify_dt=NOW() WHERE refno = '$refno'";
		$db->StartTrans();
		$this->result = $db->Execute($this->sql);
		if($db->Affected_Rows()>=1)
		{
			$this->sql = "DELETE FROM seg_misc_chrg_details WHERE refno='$refno'";
			$db->Execute($this->sql);
			if($db->Affected_Rows() >= 1)
			{
				if(count($misc)>0)
				{
					$res = $this->add_misc_order_items_by_bulk(array('misc'=>$misc, 'quantity'=>$quantity, 'price'=>$price, 'account_type'=>$account_type, 'refno'=>$refno));
					if($res)
					{
						$db->CompleteTrans();
						return true;
					}
					else
					{
						//echo "ERROR:add_misc_order_items_by_bulk<br>";
						$db->FailTrans();
						return false;
					}
				}
				else
				{
					$db->CompleteTrans();
					return true;
				}
			}
			else
			{
				if(count($misc)>0)
				{
					$res = $this->add_misc_order_items_by_bulk(array('misc'=>$misc, 'quantity'=>$quantity, 'price'=>$price, 'account_type'=>$account_type, 'refno'=>$refno));
					if($res)
					{
						$db->CompleteTrans();
						return true;
					}
					else
					{
						//echo "ERROR:add_misc_order_items_by_bulk<br>";
						$db->FailTrans();
						return false;
					}
				}
				else
				{
					$db->FailTrans();
						return false;
				}
			}
		}
		else
		{
			$db->FailTrans();
			return false;
		}
	 }*/
	 #---end CHA, 05-06-2010

	 function save_accommodation_charges($details) {
	 global $db;
	 extract($details);
	 $default_count = count($room_nr);
	 if ($default_count == count($ward_nr) && $default_count == count($room_rate) && $default_count == count($room_days) && $default_count == count($room_hours)) {
		 $room_items = array();
		 foreach ($room_nr as $key => $room_nr_value) {
		 $items_array = array($room_nr_value, $ward_nr[$key], $room_days[$key], $room_hours[$key], $room_rate[$key]);
		 $room_items[] = $items_array;
		 }
		 $author = $_SESSION['sess_user_name'];
		 $query = "INSERT INTO seg_encounter_location_addtl(encounter_nr, room_nr, group_nr, bed_nr, days_stay, hrs_stay, rate, modify_id,
				 modify_dt, create_id, create_dt, area) VALUES('$encounter_nr', ?, ?, 0, ?, ?, ?, '$author', NOW(), '$author', NOW(), '$area')";
		 $result = $db->Execute($query, $room_items);
		 if (!$result) {
		 return false;
		 }
		 else {
		 return true;
		 }
	 }
	 }

	 function add_misc_order($encounter_nr, $charge_date, $area) {
	 global $db;
	 $author = $_SESSION['sess_user_name'];
	 $refno = $db->GetOne("SELECT fn_get_new_refno_misc_chrg(".$db->qstr($charge_date).")");
	 $query = "INSERT INTO seg_misc_chrg(chrge_dte, encounter_nr, modify_id, modify_dt, create_id, create_dt, area) VALUES
				 ('$charge_date', '$encounter_nr',  '$author', NOW(), '$author', NOW(), '$area')";

	 if ($result = $db->Execute($query)) {

		return $refno;
	 }
	 else {
			echo "add_misc_order<br>";
			echo "query:".$query."<br>";
		return false;
	 }
	 }

	 #modified by cha, june 11,2010
		#--changed description field to name_short
	 function get_misc_order_items($encounter_nr, $area) {
	 global $db;

		 $this->sql = "(SELECT 1 as source, smc.refno, smc.encounter_nr, smc.area, s.name, t.name_short, smcd.chrg_amnt, s.service_code AS code, smcd.quantity, smcd.account_type,smc.create_id, smc.modify_id, smc.chrge_dte
				FROM seg_misc_chrg_details smcd INNER JOIN seg_misc_chrg smc ON (smc.refno = smcd.refno)
				INNER JOIN seg_other_services AS s ON (s.service_code = smcd.service_code)
				LEFT JOIN seg_cashier_account_subtypes AS t ON (s.account_type=t.type_id)
				LEFT JOIN seg_cashier_account_types AS p ON (t.parent_type=p.type_id) WHERE smc.encounter_nr='$encounter_nr' AND smc.area='$area')
				union
								(SELECT 2 as source, smc.refno, smc.encounter_nr, smc.area, s.name,t.name_short, smcd.chrg_amnt, s.service_code AS code, smcd.quantity, smcd.account_type,smc.create_id, smc.modify_id, smc.chrge_dte
				FROM seg_misc_chrg_details smcd INNER JOIN seg_misc_chrg smc ON (smc.refno = smcd.refno)
				INNER JOIN seg_otherhosp_services AS s ON (s.service_code = smcd.service_code)
				LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id
				LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id
				WHERE (s.status NOT IN ('deleted','hidden','inactive','void')) AND smc.encounter_nr='$encounter_nr' AND smc.area='$area')";

			if ($result = $db->Execute($this->sql)) {
		return $result;
		}
		else {
		return false;
		}
	 }

	 function add_misc_order_items_by_bulk($details) {
		 global $db;
		 extract($details);
		 $order_items = array();
		 foreach ($misc as $key => $misc_value) {
			$items_array = array($misc_value, $account_type[$key], $price[$key], $quantity[$key]);
			$order_items[] = $items_array;
		 }

		 $index = 'refno, service_code, account_type, chrg_amnt, quantity';
		 $values = "'$refno', ?, ?, ?, ?";

		 $this->sql = "INSERT INTO seg_misc_chrg_details ($index) VALUES ($values)";

		$result = $db->Execute($this->sql, $order_items);
		if (!$result) {
			echo $db->ErrorMsg();
			return false;
		}
		else {
			return true;
		}
	 }

	 function update_misc_order($refno) {
	 global $db;
	 $author = $_SESSION['sess_user_name'];
		 $this->sql = "UPDATE seg_misc_chrg SET modify_id='$author', modify_dt=NOW() WHERE refno = '$refno'";
		 if($db->Execute($this->sql)){
			if($db->Affected_Rows()){
		 return true;
			}else{
				return false;
			}
	 }
	 else {
		return false;
	 }
	 }

	 function delete_misc_order_items($refno) {
	 global $db;
		 $this->sql = "DELETE FROM seg_misc_chrg_details WHERE refno='$refno'";
		 if ($db->Execute($this->sql)) {
				if($db->Affected_Rows()){
		 return true;
			 }else{
				 return false;
			 }
	 }
	 else {
		 return false;
	 }
	 }

	 function get_misc_refno($encounter_nr, $area) {
	 global $db;
	$query = "SELECT refno FROM seg_misc_chrg WHERE encounter_nr='$encounter_nr' AND area='$area'";

	$result = $db->Execute($query);
	if ($result) {
		if ($result->RecordCount()) {
		$row = $result->FetchRow();
		return $row['refno'];
		}
		else {
		return 0;
		}
	}
	else {
		return 0;
	}
	}


	 function get_new_misc_refno($reference_number){
	global $db;

	$temp_refno = date('Y')."%";

	$query = "SELECT refno FROM seg_misc_chrg WHERE refno LIKE '$temp_refno' ORDER BY refno DESC";
	if($result = $db->SelectLimit($query, 1)){
		if($result->RecordCount()){
		$row = $result->FetchRow();
		return $row['refno']+1;
		}
		else {
		return $reference_number;
		}
	}
	else {
		return $reference_number;
	}
	}

	function get_charge_area($encounter_nr, $department_nr, $ward_nr) {
	global $db;

	$array = array();
	$this->sql = "SELECT cda.chargeable_area, sa.area_name FROM chargeable_department_area cda INNER JOIN seg_areas sa ON (cda.chargeable_area = sa.area_code) WHERE cda.department_id=$department_nr";

	$this->result = $db->Execute($this->sql);
	if ($this->result) {
		if ($this->result->RecordCount() > 0) {
		$row = $this->result->FetchRow();
		$array[$row['chargeable_area']] = $row['area_name'];

		}
	}
	$this->sql = "select som.or_type, sa.area_name from care_encounter_op ceo
					inner join seg_or_main som on (ceo.refno=som.ceo_refno)
					inner join seg_areas sa on (sa.area_code = som.or_type) WHERE ceo.encounter_nr='$encounter_nr' AND
					som.status IN ('post', 'approved', 'scheduled', 'pre_op')";

	$this->result = $db->Execute($this->sql);
	if ($this->result) {
		if ($this->result->RecordCount() > 0) {
		$row = $this->result->Fetchrow();
		$array[$row['or_type']] = $row['area_name'];
		}
	}
	$this->sql = "select area_code, area_name FROM seg_areas WHERE ward_nr='$ward_nr'";

	$this->result = $db->Execute($this->sql);
	if ($this->result) {
		if ($this->result->RecordCount() > 0) {
		$row = $this->result->Fetchrow();
		$array[$row['area_code']] = $row['area_name'];
		}
	}

	return $array;
	}

	function save_package($array) {
        extract($array);
        global $db;
        $author = $_SESSION['sess_user_name'];
        $history = "Created ".date('Y-m-d H:i:s')." ".$author."\n";

        $this->sql = "INSERT INTO seg_packages(package_name, package_price, is_surgical, create_id, modify_id, create_time, modify_time, history)
        VALUES ('$package_name', '$package_price', '$is_surgical', '$author', '$author', NOW(), NOW(), '$history')";

        $db->StartTrans();
        $db->Execute($this->sql);
        if ($db->Affected_Rows() >= 1) {
            $id = $db->Insert_ID();
            $items = array();
            foreach ($assigned_department as $key => $value) {
            $items_array = array($value);
            $items[] = $items_array;
            }
            $index = 'package_id, clinic_id';
            $values = "$id, ?";
            $this->sql = "INSERT INTO seg_packages_clinics ($index) VALUES ($values)";
            $db->Execute($this->sql, $items);
            if ($db->Affected_Rows() >= 1) {
            $db->CompleteTrans();
                    //return true;
            }
            else {
            $db->FailTrans();
            $db->CompleteTrans();
            return false;
            }

                #-----added by CHa, Feb 11, 2010------------
                    #-----save other items----------------------
                    if(empty($item_list))
                    {
                        return true;
                    }
                    else
                    {
                        $ok=0;
                        foreach($item_list as $key => $value)
                        {
                            $values=array();
                            for($i=0;$i<count($item_list[$key]);$i++)
                            {
                                $var = $item_list[$key][$i];
                                $values[]=$var;
                            }
                            #print_r($values);
                            #echo "<br/>";
                            $this->sql="insert into seg_package_details (package_id, item_id, quantity, item_purpose) values($id,?,?,?)";
                            #$this->sql="insert into seg_package_details (package_id, item_id, quantity, unit, item_purpose) values($id,$values[0],$values[1],$values[2],$values[3])";
                            $db->Execute($this->sql, array($values), $autoQuote=true);
                            if ($db->Affected_Rows()) {
                                $ok++;
                                #echo $this->sql."<br>";
                            }
                            else {
                                //$db->FailTrans();
                                //$db->CompleteTrans();
                                print_r($db->ErrorMsg());
                                return false;
                            }
                        }
                        if($ok>0)
                        {
                            $db->CompleteTrans();
                            return true;
                        }
                        else
                        {
                            return false;
                        }
                    }
                    #---end CHa-------------------------------
        }
        else {
            $db->FailTrans();
            $db->CompleteTrans();
            return false;
        }
	}

	/**
	* @internal     Set the group_code to specific value.
	* @access       public
	* @author       Bong S. Trazo
	* @package      include
	* @subpackage   care_api_classes
	* @global       db - database object
	*
	* @param        grpid, opcode, refno, entryno, provider
	* @return       boolean TRUE if successful, FALSE otherwise.
	*/
	function updateGrpID($grpid, $opcode, $refno, $entryno, $provider) {
		$tblname = ($provider == 'OA') ? "seg_misc_ops_details" : "seg_ops_servdetails";
		$filter  = ($provider == 'OA') ? "and entry_no = {$entryno}" : "";

		$this->sql = "update {$tblname} set group_code = '{$grpid}'
						 where refno = '{$refno}'
							and ops_code = '{$opcode}'
							{$filter}";
		return $this->Transact($this->sql);
	}

	function edit_package($array) {
	extract($array);
	global $db;
	$author = $_SESSION['sess_user_name'];
	$history = "Updated ".date('Y-m-d H:i:s')." ".$author."\n";

	$this->sql = "UPDATE seg_packages SET
					package_name='$package_name',
					package_price=".$db->qstr("$package_price").",
                    is_surgical='$is_surgical',
					create_id='$author',
					modify_id='$author',
					create_time=NOW(),
					modify_time=NOW(),
					history=CONCAT(history, '$history')
					WHERE package_id=$id";

	$db->StartTrans();
	$db->Execute($this->sql);
	if ($db->Affected_Rows() >= 1) {
		$this->sql = "DELETE FROM seg_packages_clinics WHERE package_id=$id;";
		$db->Execute($this->sql);
		if ($db->Affected_Rows() >= 1) {
		$items = array();
		foreach ($assigned_department as $key => $value) {
		$items_array = array($value);
		$items[] = $items_array;
		}

		$index = 'package_id, clinic_id';
		$values = "$id, ?";
		$this->sql = "INSERT INTO seg_packages_clinics ($index) VALUES ($values)";
		$db->Execute($this->sql, $items);
		if ($db->Affected_Rows() >= 1) {
				#$db->CompleteTrans();
				#return true;
				#-----added by CHa, Feb 13, 2010------------
				#-----update other items----------------------
				$this->sql = "DELETE FROM seg_package_details WHERE package_id=$id;";
				if($db->Execute($this->sql))
				{
					if(empty($item_list))
					{
		$db->CompleteTrans();
		return true;
		}
					else
					{
						$ok=0;
						foreach($item_list as $key => $value)
						{
							$values=array();
							for($i=0;$i<count($item_list[$key]);$i++)
							{
								$var = $item_list[$key][$i];
								$values[]=$var;
							}
							$this->sql="insert into seg_package_details (package_id, item_id, quantity, item_purpose) values($id,?,?,?)";
							$db->Execute($this->sql, array($values), $autoQuote=true);
							if ($db->Affected_Rows()) {
								$ok++;
							}
							else {
								print_r($db->ErrorMsg());
								return false;
							}
						}
						if($ok>0)
						{
							$db->CompleteTrans();
							return true;
						}
						else
						{
							$db->FailTrans();
							$db->CompleteTrans();
							return false;
						}
					}
				}
				else
				{
					print_r($db->ErrorMsg());
					$db->FailTrans();
					$db->CompleteTrans();
					return false;
				}
				#---end CHa-------------------------------
			}
		else {
		$db->FailTrans();
		$db->CompleteTrans();
		return false;
		}
		}
		else {
		$db->FailTrans();
		$db->CompleteTrans();
		return false;
		}
	}
	else {
		$db->FailTrans();
		$db->CompleteTrans();
		return false;
	}
	}

	function get_all_package_data($package_id) {
	global $db;
	$this->sql = "SELECT package_name, package_price, is_surgical FROM seg_packages WHERE package_id=$package_id";

	$this->result = $db->Execute($this->sql);

	if ($this->result) {
		if ($this->result->RecordCount() > 0) {
			return $this->result->FetchRow();
		}
		else {
		return false;
		}
	}
	else {
		return false;
	}
	}

	# added by CHA, April 11,2010
	function save_op_accommodation_charge($details,$bill_dt)
	{
		global $db;
		extract($details);
		$bSuccess = true;
		#save to seg_opaccommodation
		if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
			$tmp_dte = $bill_dt;
		else
			$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

		$tmpbill_dte = $tmp_dte;
		$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));

		$objBilling = new Billing($encounter_nr, $tmpbill_dte);
		$db->StartTrans();
		$refno = $this->getOpAccommodationRefNo($objBilling->bill_frmdte, $encounter_nr);
		if($refno == '')
		{
			$strSQL = "insert into seg_opaccommodation (chrge_dte, encounter_nr, modify_id, create_id, create_dt) ".
			"   values ('".$tmp_dte."', '".$encounter_nr."', '".$_SESSION['sess_user_name']."', '".$_SESSION['sess_user_name']."', ".
			"          '".$tmp_dte."')";
			/*if ($db->Execute($strSQL))
				$refno = getOpAccommodationRefNo($objBilling->bill_frmdte, $encounter_nr);
			else {
				$bSuccess = false;
				$err_msg = $db->ErrorMsg();
			}*/
			echo $strSQL;
		}

		$n = 0;
		if ($bSuccess) {
			$strSQL = "insert into seg_opaccommodation_details (refno, room_nr, group_nr, charge, modify_id, create_id, create_dt) ".
						"   values ('".$refno."', ".$rm_nr.", ".$w_nr.", ".$nchrg.", '".$_SESSION['sess_user_name']."', '".$_SESSION['sess_user_name']."', ".
						"          '".$tmp_dte."')";
			/*if ($db->Execute($strSQL)) {
				$n = getMaxNoFromOPAccomDetails($refno);
				$bSuccess = ($n > 0);
			}
			else {
				$bSuccess = false;
				$err_msg = $db->ErrorMsg();
			}*/
		}
	}

	function getOpAccommodationRefNo($bill_frmdte, $enc_nr) {
		global $db;

		$srefno = '';
		$strSQL = "select refno ".
					"   from seg_opaccommodation ".
					"   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= '".$bill_frmdte."' ".
					"      and encounter_nr = '".$enc_nr."' ".
					"   order by chrge_dte limit 1";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow())
					$srefno = $row['refno'];
			}
		}

		return($srefno);
}
	# end CHA

	#added by cha, june 11, 2010
	function saveMedsAndSuppliesCharges($pharma_refno, $order_refno, $order_data, $bulk, $post, $area)
	{
		global $db;
		$seg_order = new SegOrder("pharma");
		$db->StartTrans();
		if(count($post['items'])==0)
		{
			//delete order
			$saveok=$seg_order->deleteOrder($order_refno);
			if ($saveok)
			{
				$db->CompleteTrans();
				return true;
			}else
			{
				 $db->FailTrans();
				 return false;
			}
		}else
		{
			$seg_order->setDataArray($order_data);
			$order_count = count($post['items']);
			if (($pharma_refno == 0) && $order_count > 0) {	//new entry
				$saveok = $seg_order->insertDataFromInternalArray();
			}
			elseif ($pharma_refno != 0) {	//update entry
				$seg_order->where = "refno=".$db->qstr($pharma_refno);
				$saveok = $seg_order->updateDataFromInternalArray($pharma_refno,FALSE);
			}
			//save details
			if ($saveok) {
				$order_refno = ($pharma_refno == 0) ? $order_data['refno'] : $pharma_refno;
				if ($saveok=$this->clearItemList($order_refno)) {
					if (count($post['items']) > 0) {
						$saveok = $seg_order->addOrders($order_refno, $bulk);
						//if($order_data["is_cash"]=="0")
						//{
							$item_array = $post['items'];
							$status_array = array_fill(0, count($item_array), 'S');
							$remarks_array = array_fill(0, count($item_array), '');
							$saveok = $seg_order->changeServeStatus($order_refno, $item_array, $status_array, $remarks_array);
						//}
					}
					if ($saveok)
					{
						$db->CompleteTrans();
						return true;
					}
				}
			}else
			{
				$db->FailTrans();
				echo "error=".$seg_order->error_msg;
				return false;
			}
		}
	}

	//added by cha, July 7, 2010
	function clearItemList($refno)
	{
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "DELETE FROM seg_pharma_order_items WHERE refno=$refno AND ISNULL(request_flag)";
		return $this->Transact();
	}

	function save_misc_charges($details) {
		 global $db;
		 $db->StartTrans();
		 extract($details);
		 $refno = $this->get_misc_refno($encounter_nr, $area);
		 $no_error = false;
		 if(count($misc)==0)
		 {
				$saveok = $this->delete_misc_order($refno);
				if ($saveok)
				{
					$db->CompleteTrans();
					return true;
				}else
				{
					 $db->FailTrans();
					 return false;
				}
		 }else
		 {
				if ($refno) { //edit
				 if ($no_error = $this->update_misc_order($refno)) {
					 if ($no_error = $this->delete_misc_order_items($refno)) {
						 if ($no_error = (count($misc) > 0) && (count($misc)==count($quantity))) {
							 $no_error = $this->add_misc_order_items_by_bulk(array('misc'=>$misc, 'quantity'=>$quantity, 'price'=>$price, 'account_type'=>$account_type, 'refno'=>$refno));
						 }
						 else $db->FailTrans();
					 }
					 else  $db->FailTrans();
				 }
			 }
			 else { //new entry
				 if ($no_error = (count($misc) > 0) && (count($misc)==count($quantity))) {
					 if ($refno = $this->add_misc_order($encounter_nr, $charge_date, $area)) {
						 $no_error = $this->add_misc_order_items_by_bulk(array('misc'=>$misc, 'quantity'=>$quantity, 'price'=>$price, 'account_type'=>$account_type, 'refno'=>$refno));
					 }
				 }
			 }
			 if ($no_error) {
				 $db->CompleteTrans();
				 return true;
			 }
			 else {
				 $db->FailTrans();
				 return false;
			 }
		 }
	 }

	 function delete_misc_order($refno){
		 global $db;
		 $this->sql = "DELETE FROM seg_misc_chrg WHERE refno=".$db->qstr($refno);
		 if($db->Execute($this->sql)) {
			 return true;
		 }
		 else {
			 return false;
		 }
	 }
	#end cha

	#Added by Cherry 06-11-10
	function SearchProcedures($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE){
				global $db, $sql_LIKE, $root_path, $HTTP_SESSION_VARS;

				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				include_once($root_path.'include/inc_date_format_functions.php');
				$date_format=getDateFormat();   # burn added, October 11, 2007

				$searchkey = $db->qstr($searchkey);
				$searchkey = substr($searchkey, 1, strlen($searchkey)-2);

				# convert * and ? to % and &
				$searchkey=strtr($searchkey,'*?','%_');
				$searchkey=trim($searchkey);
				$suchwort=$searchkey;

				#echo "key = ".$suchwort;

				$this->sql = "SELECT package_id, package_name FROM seg_packages WHERE package_name $sql_LIKE '".$searchkey."%'";

				if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){

						$this->rec_count=$this->res['ssl']->RecordCount();
						#echo "count = ".$this->rec_count;
						return $this->res['ssl'];
				}else{return false;}
		}#end Cherry

	#added by CHA, september 2, 2010
	function get_anesthesia_procedures($filters, $maxrows, $offset, $key)
	{
		global $db;
		$phFilters = array();
		if(is_array($filters))
		{
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'sort': $sort_sql = $v; break;
				}
			}
		}
		$this->sql = "select SQL_CALC_FOUND_ROWS id, name from care_type_anaesthesia where status <> 'deleted'";
		if($key)
		{
			$this->sql.= " and (name like '%$key%')";
		}
		if($sort_sql)
		{
			$this->sql.=" order by {$sort_sql} ";
		}
		if($maxrows)
		{
			$this->sql.= " limit $offset, $maxrows ";
		}

		if($this->result = $db->Execute($this->sql))
		{
			if($this->result->RecordCount())
			{
				return $this->result;
			}
			else return false;
		}else return false;

	}

	function get_anesthesia_specific($filters, $id)
	{
		global $db;
		$phFilters = array();
		if(is_array($filters))
		{
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'sort': $sort_sql = $v; break;
				}
			}
		}
		$this->sql = "select sub_anesth_id, description from seg_or_sub_anesthesia where anesthesia_id='$id' and status <> 'deleted'";
		if($sort_sql)
		{
			$this->sql.=" order by {$sort_sql} ";
		}
		if($this->result = $db->Execute($this->sql))
		{
			if($this->result->RecordCount())
			{
				return $this->result;
			}
			else return false;
		}else return false;
	}
	#end cha-------------------
}  # end class SegOps
?>