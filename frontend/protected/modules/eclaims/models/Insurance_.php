<?php
/**
* @package care_api
*/
/**
*/
require_once($root_path.'include/care_api_classes/class_core.php');
/**
*  Insurance methods.
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/
class Insurance extends Core {
	/**
	* Table name insurance classes
	* @var string
	*/
	var $tb_class='care_class_insurance'; # insurance classes table name
	/**
	* Table name for insurance companies' data
	* @var string
	*/
	var $tb_insurance='care_insurance_firm'; # insurance companies
	/**
	* Buffer for sql query results
	* @var mixed adodb record object or boolean
	*/
	var $result;
	/**
	* Buffer for row returned by adodb's FetchRow() method
	* @var array
	*/
	var $row;
	/**
	* Insurance company's id
	* @var string
	*/
	var $firm_id;
	/**
	* Universal buffer
	* @var mixed
	*/
	var $buffer;
	/**
	* Sql query string
	* @var string
	*/
	var $sql;
	/**
	* Universal event flag
	* @var boolean
	*/
	var $ok;

		var $benefitsked_id;            // Added by LST for use in seg_role_adjustment table ... 02.24.2009
		var $benefit_id;                // Added by LST ... 02.24.2009

	#------------------added by VAN ------------------------------
	/**
	 * Table name for hcare_bsked
	 * @var string
	*/
	var $tb_bsked='seg_hcare_bsked';

	/**
	 * Table name for hcare_RVUrange
	 * @var string
	*/
	var $tb_RVUrange='seg_hcare_rvurange';

	/**
	 * Table name for hcare_confinetype
	 * @var string
	*/
	var $tb_confinetype='seg_hcare_confinetype';

	/**
	 * Table name for hcare_roomtype
	 * @var string
	*/
	var $tb_roomtype='seg_hcare_roomtype';

	/**
	 * Table name for hcare_products
	 * @var string
	*/
	var $tb_peritem='seg_hcare_products';

	var $tb_peritem_serv='seg_hcare_srvops';

	var $tb_ward='care_ward';

	var $tb_conftype = 'seg_type_confinement';

	#var $tb_procedure = 'care_ops301_en';
	var $tb_procedure = 'seg_ops_rvs';

	var $tb_otherHS = 'seg_otherhosp_services';
	var $tb_othersrv = 'seg_other_services';
	var $tb_labserv = 'seg_lab_services';
	var $tb_radioserv = 'seg_radio_services';
	var $tb_med = 'care_pharma_products_main';

	var $tb_coveredpkg = 'seg_hcare_packages';

	#added by VAN 05-05-08
	var $prctable = 'seg_pharma_prices';
	var $labgrptable = 'seg_lab_service_groups';
	var $radiogrptable = 'seg_radio_service_groups';

	/**
	 * Table name for hcare_RVUrange
	 * @var string
	*/
	var $tb_benefits='seg_hcare_benefits';

	/**
	* Field names of seg_encounter_condition table
	* @var array
	*/
	var $fld_bsked=array(
								'hcare_id',
								'benefit_id',
																'tier_nr',
								'basis',
								'effectvty_dte');
	/*var $fld_RVUrange=array(
								'hcare_id',
								'benefit_id',
								'range_start',
								 'range_end',
								 'amountlimit',
								 'rateperRVU');
	*/
	var $fld_RVUrange=array(
								 'bsked_id',
								 'range_start',
								 'range_end',
								 'amountlimit',
								 'minamount',
								 'fixedamount',
								 'rateperRVU',
								 'percentofSF');

	/*var $fld_confinetype=array(
								'hcare_id',
								'benefit_id',
								'confinetype_id',
								'rateperday',
								'amountlimit',
								'dayslimit',
								'rateperRVU',
								 'year_dayslimit',
								 'year_dayslimit_alldeps');
	*/

	var $fld_confinetype=array(
								'bsked_id',
								'confinetype_id',
								'rateperday',
								'amountlimit',
								'dayslimit',
								'rateperRVU',
																'limit_rvubased',
								 'year_dayslimit',
								 'year_dayslimit_alldeps');
	/*
	var $fld_roomtype=array(
								'hcare_id',
								'benefit_id',
								'roomtype_nr',
								'rateperday',
								'amountlimit',
								'dayslimit',
								'rateperRVU',
								 'year_dayslimit',
								 'year_dayslimit_alldeps');
	*/
	var $fld_roomtype=array(
								'bsked_id',
								'roomtype_nr',
								'rateperday',
								'amountlimit',
								'dayslimit',
								'rateperRVU',
								 'year_dayslimit',
								 'year_dayslimit_alldeps');

	var $fld_benefit=array(
								'benefit_desc',
								'bill_area');

	var $fld_confinement=array(
								'confinetypedesc',
								'is_deleted',
								'modify_id',
								'modify_time',
								'create_id',
								'create_time');

	 var $fld_otherhospserv=array(
								'service_code',
								'name',
								'price',
								'chrgprice',
								'status',
								'account_type',
								'exclude_hcareid',
								'history',
								'modify_id',
								'modify_time',
								'create_id',
								'create_time');

	#--------------------------------------------------------------

	/**
	* Field names of care_insurance_firm table
	* @var array
	*/
	var $fld_insurance=array(
			'firm_id',
			'name',
			'iso_country_id',
			'sub_area',
			'type_nr',
			'addr',
			'addr_mail',
			'addr_billing',
			'addr_email',
			'phone_main',
			'phone_aux',
			'fax_main',
			'fax_aux',
			'contact_person',
			'contact_phone',
			'contact_fax',
			'contact_email',
			'use_frequency',
			'status',
			'history',
			'modify_id',
			'modify_time',
			'create_id',
			'create_time',
			'accreditation_no');

	/**
	* Constructor
	* @param string Insurance company id
	*/
	function Insurance($firm_id='') {
				global $db;
		#echo "insurance = ".$firm_id;
		 $this->firm_id=$this->stringTrim($firm_id);   # burn modified: August 28, 2006
		 $this->coretable=$this->tb_insurance;
		 $this->ref_array=$this->fld_insurance;
	}
	/**
	* Sets the internal firm id buffer to the insurance company's id.
	* @access public
	* @param string Insurance company id
	*/
	function setFirmID($firm_id='') {
			$this->firm_id=$firm_id;
	}
	/**
	* Resolves the insurance company's id.
	* @access private
	* @param string Insurance company id
	* @return boolean
	*/
	function _internResolveFirmID($firm_id='') {
			if (empty($firm_id)) {
				if(empty($this->firm_id)) {
				#echo " <br> hello burn enter _internResolveFirmID FALSE 1";
					return FALSE;
			} else { echo " <br> hello burn enter _internResolveFirmID TRUE 1"; return TRUE; }
		} else {
					$this->firm_id=$firm_id;
						#echo " <br> hello burn enter _internResolveFirmID TRUE 2";
			return TRUE;
		}
	}
	/**
	* Sets the core to point to the insurance table's name and field names.
	* @access public
	*/
	function _useInsurance(){
		$this->coretable=$this->tb_insurance;
		$this->ref_array=$this->fld_insurance;
	}
	/**
	* Gets the information of all insurance classes. Returns adodb record object.
	*
	* @access public
	* @param string Field names of values to be fetched
	* @return mixed adodb record object or boolean
	*/
		function getInsuranceClassInfoObject($items='class_nr,class_id,name,LD_var AS "LD_var", description,status,history') {

			global $db;
			#echo "SELECT $items  FROM $this->tb_class";
				if ($this->res['gicio']=$db->Execute("SELECT $items  FROM $this->tb_class")) {
						if ($this->res['gicio']->RecordCount()) {
								return $this->res['gicio'];
						} else {return FALSE;}
		} else {return FALSE; }
		}

		#--------added by VAN 09-19-07-------
/*
	function countSearchSelect($searchkey='',$maxcount=100,$offset=0,$oitem='name',$odir='ASC') {
		global $db, $root_path;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		if ($searchkey=='*'){
			$this->sql= "SELECT * FROM $this->tb_insurance
									 WHERE status NOT IN ($this->dead_stat)
							 ORDER BY $oitem $odir";
		}else{
			$this->sql= "SELECT * FROM $this->tb_insurance
									 WHERE status NOT IN ($this->dead_stat)
							 AND (firm_id LIKE '%".$searchkey."%' OR name LIKE '%".$searchkey."%')
							 ORDER BY $oitem $odir";
		}
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function SearchSelect($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC'){
		global $db, $root_path;
		//$db->debug=true;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		if ($searchkey=='*'){
			$this->sql= "SELECT * FROM $this->tb_insurance
									 WHERE status NOT IN ($this->dead_stat)
							 ORDER BY $oitem $odir";
		}else{
			$this->sql= "SELECT * FROM $this->tb_insurance
									 WHERE status NOT IN ($this->dead_stat)
							 AND firm_id LIKE '%".$searchkey."%' OR name LIKE '%".$searchkey."%'
							 ORDER BY $oitem $odir";
		}

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}
*/

	#added by VAN 08-14-08
	function getPatientInsuranceInfo($pid, $insurance_nr){
	global $db;

		$this->sql ="SELECT * FROM care_person_insurance
							WHERE (pid='".$pid."'
							OR pid=(SELECT parent_pid
									FROM seg_dependents AS dep
									LEFT JOIN  care_person_insurance AS i ON (i.pid=dep.parent_pid OR i.pid=dep.dependent_pid)
											AND i.is_void=0
									WHERE (dep.parent_pid='$pid' OR dep.dependent_pid='$pid')
									AND i.hcare_id='$insurance_nr'
									AND dep.status NOT IN ('cancelled','deleted','expired') LIMIT 1))
							AND hcare_id='".$insurance_nr."' AND is_void=0";
		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
	}
	#--------------------

		#added by VAN 05-21-09
		function getPersonnelAccreditationInfo($personell_nr, $hcare_id){
		 global $db;

				$this->sql ="SELECT * FROM seg_dr_accreditation
										WHERE hcare_id ='$hcare_id'
										AND dr_nr='$personell_nr';";
				if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
		 }
		 #------------

	#-----------edited by VAN 04-17-08

	function countSearchSelect($searchkey='',$maxcount=100,$offset=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$this->sql = "SELECT * FROM $this->tb_insurance
							WHERE (firm_id LIKE '".$keyword."%'
							OR name LIKE '".$keyword."%')
							AND status NOT IN (".$this->dead_stat.")
							ORDER BY name";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function SearchSelect($searchkey='',$maxcount=100,$offset=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$this->sql = "SELECT * FROM $this->tb_insurance
							WHERE (firm_id LIKE '".$keyword."%'
							OR name LIKE '".$keyword."%')
							AND status NOT IN (".$this->dead_stat.")
							ORDER BY name";

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

	#--------------------------------------

	function deleteInsuranceComp($hcare_id) {
		global $db,$HTTP_SESSION_VARS;

		if(empty($hcare_id) || (!$hcare_id))
			return FALSE;

		$this->_useInsurance;
		#$this->sql="DELETE FROM $this->tb_lab_serv WHERE refno='$refno'";
		$history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		$this->sql="UPDATE $this->coretable ".
						" SET status='deleted', history=".$history.", ".
						" modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_time=NOW() ".
						" WHERE hcare_id = '$hcare_id'";
			#echo "sql = ".$this->sql;
		 return $this->Transact();
	}

	function getAllConfinement(){
	global $db;

		$this->sql ="SELECT * FROM $this->tb_conftype ORDER BY confinetypedesc";
		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result;
			} else{
				 return FALSE;
			}
	}

	function getAllRooms(){
	global $db;

		#$this->sql ="SELECT * FROM $this->tb_ward ORDER BY name";
		/*
		$this->sql ="SELECT r.*, w.accomodation_type, w.ward_id AS ward_name, w.name
					 FROM care_room AS r
					 INNER JOIN care_ward AS w ON w.nr=r.ward_nr
					 WHERE r.is_temp_closed = 0
					 AND r.date_close = '0000-00-00' AND r.status NOT IN ($this->dead_stat)
					 ORDER BY room_nr";
		*/
		$this->sql ="SELECT * FROM care_type_room ORDER BY name";

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result;
			} else{
				 return FALSE;
			}
	}

	function getAllBenefits(){
	global $db;

		$this->sql ="SELECT * FROM $this->tb_benefits ORDER BY benefit_desc";
		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result;
			} else{
				 return FALSE;
			}
	}

	function useBenefitSked(){
		$this->coretable=$this->tb_bsked;
		$this->ref_array=$this->fld_bsked;

	}

	function useRVURange(){
		$this->coretable=$this->tb_RVUrange;
		$this->ref_array=$this->fld_RVUrange;

	}

	function useConfinementType(){
		$this->coretable=$this->tb_confinetype;
		$this->ref_array=$this->fld_confinetype;

	}

	function useRoomType(){
		$this->coretable=$this->tb_roomtype;
		$this->ref_array=$this->fld_roomtype;

	}

		function getLastBskedID() {
				global $db;

				$id = 0;
				$this->sql = "select max(bsked_id) as last_bskedid
											from $this->tb_bsked";
				if ($this->result=$db->Execute($this->sql)) {
						$row = $this->result->FetchRow();
						$id = $row['last_bskedid'];
						$id = (is_null($id) ? 0 : $id);
				}
				return $id;
		}

		function isWithRoleAdjustment($role_area, $bsked_id, $conftyp_id = 0, $range_start = 0) {
				global $db;

				$filter = '';
				if ($conftyp_id != 0) $filter .= " and confinetype_id = $conftyp_id";
				if ($range_start != 0) $filter .= " and range_start = $range_start";
				$this->sql = "select * from seg_role_adjustment
												 where role_area = '$role_area' and bsked_id = $bsked_id".$filter;
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->result->RecordCount())
								return TRUE;
						else
								return FALSE;
				}
				return FALSE;
		}

	function saveBenefitSked(&$data){
		if(!is_array($data)) return FALSE;
		$this->useBenefitSked();
		$this->buffer_array=NULL;
		$bSuccess = $this->insertDataFromInternalArray();
				if ($bSuccess) {
						$this->benefitsked_id = $this->getLastBskedID();
				}
				return $bSuccess;
	}

	#added by VAN 08-27-08
	#function updateBenefitSked($hcare_id, $benefit_id,$effectvty_dte, $basis){
	function updateBenefitSked($bsked_id, $basis, $tier_nr = 0) {
//		global $db,$HTTP_SESSION_VARS;

		#if((empty($hcare_id) || (!$hcare_id)) && (empty($benefit_id) || (!$benefit_id)) && (empty($effectvty_dte) || (!$effectvty_dte)))
		if(empty($bsked_id) || (!$bsked_id))
			return FALSE;

//		$this->sql="UPDATE seg_hcare_bsked
//						SET basis='".$basis."'
//						WHERE hcare_id='".$hcare_id."'
//						AND benefit_id='".$benefit_id."'
//						AND effectvty_dte='".date("Y-m-d",strtotime($effectvty_dte))."'";

		$this->sql="UPDATE seg_hcare_bsked
						SET basis='".$basis."',
														tier_nr = $tier_nr
						WHERE bsked_id='".$bsked_id."'";
		#echo "sql = ".$this->sql;

				$this->benefitsked_id = $bsked_id;

				return $this->Transact();

//		if($db->Execute($this->sql)) {
//			return true;
//		} else {
//			#print_r($db->ErrorMsg());
//			#print_r($db->LastErrorMsg());
//			return false;
//		}
	}
	#-----------------

		function getBillArea($benefit_id) {
				global $db;

				$barea = '';
				$this->sql = "select bill_area
											from $this->tb_benefits
											where benefit_id = $benefit_id";
				if ($this->result=$db->Execute($this->sql)) {
						$row = $this->result->FetchRow();
						$barea = $row['bill_area'];
						$barea = (is_null($barea) ? '' : $barea);
				}
				return $barea;
		}

	function saveRVURange(&$data){
		if(!is_array($data)) return FALSE;
		$this->useRVURange();
		$this->buffer_array=NULL;
		$bSuccess = $this->insertDataFromInternalArray();
				if ($bSuccess) {
						$barea = $this->getBillArea($this->benefit_id);
						if ((($data['rateperRVU'] > 0) && ($data['rateperRVU'] <= 1)) && in_array($barea, array('D1', 'D2', 'D3', 'D4'))) {
								$this->sql = "insert into seg_role_adjustment (role_area, adjust_rate, bsked_id, range_start)
																 values('$barea', ".$data['rateperRVU'].", $this->benefitsked_id, ".$data['range_start'].")";
								$bSuccess = $this->Transact();
						}
				}
				return $bSuccess;
	}

	function saveConfinementType(&$data){
		if(!is_array($data)) return FALSE;
		$this->useConfinementType();
		$this->buffer_array=NULL;
		$bSuccess = $this->insertDataFromInternalArray();
				if ($bSuccess) {
						$barea = $this->getBillArea($this->benefit_id);
						if ((($data['rateperRVU'] > 0) && ($data['rateperRVU'] <= 1)) && in_array($barea, array('D1', 'D2', 'D3', 'D4'))) {
								$this->sql = "insert into seg_role_adjustment (role_area, adjust_rate, bsked_id, confinetype_id)
																 values('$barea', ".$data['rateperRVU'].", $this->benefitsked_id, ".$data['confinetype_id'].")";
								$bSuccess = $this->Transact();
						}
				}
				return $bSuccess;
	}

		function updateConfinementType($bsked_id, $conf_typ) {
				$this->setWhereCondition("bsked_id = ".$bsked_id." and confinetype_id = ".$conf_typ);
				$bSuccess = $this->updateDataFromInternalArray();
				if ($bSuccess) {
						$barea = $this->getBillArea($this->benefit_id);
						$data = $this->data_array;
						if ((($data['rateperRVU'] > 0) && ($data['rateperRVU'] <= 1)) && in_array($barea, array('D1', 'D2', 'D3', 'D4'))) {
								if ($this->isWithRoleAdjustment($barea, $bsked_id, $conf_typ))
										$this->sql = "update seg_role_adjustment
																		 adjust_rate = ".$data['rateperRVU']."
																		 where role_area = '$barea' and bsked_id = $bsked_id and confinetype_id = $conf_typ";
								else
										$this->sql = "insert into seg_role_adjustment (role_area, adjust_rate, bsked_id, confinetype_id)
																		 values('$barea', ".$data['rateperRVU'].", $this->benefitsked_id, ".$data['confinetype_id'].")";
								$bSuccess = $this->Transact();
						}
						else {
								$this->sql = "delete from seg_role_adjustment
																 where role_area = '$barea' and bsked_id = $bsked_id and confinetype_id = $conf_typ";
								$bSuccess = $this->Transact();
						}
				}
				return $bSuccess;
		}

	function saveRoomType(&$data){
		if(!is_array($data)) return FALSE;
		$this->useRoomType();
		$this->buffer_array=NULL;
		return $this->insertDataFromInternalArray();
	}

	#function clearProducts($hcare_id, $benefit_id) {
	function clearProducts($bskedID) {
		global $db;
		/*$this->sql = "DELETE FROM $this->tb_peritem
									WHERE hcare_id='$hcare_id'
							AND benefit_id = '$benefit_id'";
		*/
		$this->sql = "DELETE FROM $this->tb_peritem
									WHERE bsked_id='$bskedID'";
		#echo "<br>sql:clearProducts = ".$this->sql;
			return $this->Transact();
	}

	#function clearServices($hcare_id, $benefit_id) {
	function clearServices($bskedID) {
		global $db;
		/*$this->sql = "DELETE FROM $this->tb_peritem_serv
									WHERE hcare_id='$hcare_id'
							AND benefit_id = '$benefit_id'";
		*/
		$this->sql = "DELETE FROM $this->tb_peritem_serv
									WHERE bsked_id='$bskedID'";
		#echo "<br>sql:clearProducts = ".$this->sql;
			return $this->Transact();
	}

	function clearCoveredPackages($bskedID) {
		$this->sql = "delete from $this->tb_coveredpkg where bsked_id = '$bskedID'";
		return $this->Transact();
	}

	#function addProducts($hcare_id, $benefit_id, $orderArray) {
	function addProducts($bskedID, $orderArray) {
		global $db;

		#$this->sql = "INSERT INTO $this->tb_peritem(hcare_id,benefit_id,bestellnum,amountlimit) VALUES('$hcare_id','$benefit_id',?,?)";
		$this->sql = "INSERT INTO $this->tb_peritem(bsked_id,bestellnum,amountlimit) VALUES('$bskedID',?,?)";

//        $this->sql = "INSERT INTO $this->tb_peritem(bsked_id,bestellnum,amountlimit) VALUES('$bskedID','".$orderArray[0][0]."',".$orderArray[0][1].")";
//		return $this->Transact();

				if($buf=$db->Execute($this->sql,$orderArray)) {
//			if($buf->RecordCount()) {
				return true;
//			} else { return false; }
		} else { return false; }
	}

	#function addServices($hcare_id, $benefit_id, $orderArray) {
	function addServices($bskedID, $orderArray) {
		global $db;
		/*
		$this->sql = "INSERT INTO $this->tb_peritem_serv(hcare_id,benefit_id,code,provider,amountlimit,maxRVU)
									VALUES('$hcare_id','$benefit_id',?,?,?,?)";
		*/
		$this->sql = "INSERT INTO $this->tb_peritem_serv(bsked_id, code, provider, amountlimit, maxRVU)
						VALUES('$bskedID', ?, ?, ?, ?)";

//		$this->sql = "INSERT INTO $this->tb_peritem_serv(bsked_id,code,provider,amountlimit,maxRVU)
//                      VALUES('$bskedID','".$orderArray[0]."','".$orderArray[1]."',".$orderArray[2].",".$orderArray[3].")";
//        return $this->Transact();

		if($buf=$db->Execute($this->sql,$orderArray)) {
//			if($buf->RecordCount()) {               #  insert statements do not return the recordcount ....
				return true;
//			} else { return false; }
		} else { return false; }
	}

	/**
	* @internal     Save the covered packages.
	* @access       public
	* @author       Bong S. Trazo
	* @version      10.08.2009
	* @package      include
	* @subpackage   care_api_classes
	* @global       db - database object
	*
	* @param        bskedID  - benefit schedule id in header.
	* @param        inputarr - array (2-dimensional) of input values in sql statement.
	* @return       status of SQL operation (true - if ok, false otherwise).
	*/
	function addCoveredPackages($bskedID, $inputarr) {
		global $db;

		$this->sql = "insert into $this->tb_coveredpkg (bsked_id, package_id, amountlimit)  \n
						 values ('$bskedID', ?, ?)";
		if($buf=$db->Execute($this->sql, $inputarr)) {
				return true;
		} else { return false; }
	}

	#function getConfinementBenefit($hcare_id, $benefit_id, $conf_type){
	function getConfinementBenefit($bskedID, $conf_type){
		global $db;

		/*$this->sql = "SELECT DISTINCT b.* FROM $this->tb_confinetype AS b
							WHERE b.hcare_id='$hcare_id'
							AND b.benefit_id='$benefit_id'
										AND b.confinetype_id = '$conf_type'";
		*/
		$this->sql = "SELECT DISTINCT b.* FROM $this->tb_confinetype AS b
							WHERE b.bsked_id='$bskedID'
							AND b.confinetype_id = '$conf_type'";

		if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}
				else
						return FALSE;
	}

	function getBenefitShedule($hcare_id, $benefit_id, $conf_type, $eff_date){
		global $db;

		$this->sql = "SELECT DISTINCT b.*, bs.* FROM seg_hcare_confinetype AS b
							INNER JOIN seg_hcare_bsked AS bs ON bs.bsked_id=b.bsked_id
							WHERE bs.hcare_id='$hcare_id'
							AND bs.benefit_id='$benefit_id'
							AND b.confinetype_id = '$conf_type'
							AND bs.effectvty_dte='$eff_date'";

		if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}
				else
						return FALSE;
	}

	#function deleteConfinementBenefit($hcare_id, $benefit_id, $conf_type) {
	function deleteConfinementBenefit($bskedID, $conf_type) {
		global $db;
		/*$this->sql = "DELETE FROM $this->tb_confinetype
									WHERE hcare_id='$hcare_id'
							AND benefit_id='$benefit_id'
										AND confinetype_id = '$conf_type'";
		*/
		$this->sql = "DELETE FROM $this->tb_confinetype
									WHERE bsked_id='$bskedID'
							AND confinetype_id = '$conf_type'";
				return $this->Transact();
	}

		// Added by LST -- 04.02.2009 -------------------------------------------
		function clearConfinementBenefit($bskedID) {
				$this->sql = "delete from $this->tb_confinetype
												 where bsked_id = '$bskedID'";
				return $this->Transact();
		}

		function clearItemsBenefit($bskedID) {
				if ($this->clearProducts($bskedID))
						return $this->clearServices($bskedID);
				else
						return false;
		}

		function clearRoomTypeBenefit($bskedID) {
				$this->sql = "DELETE FROM $this->tb_roomtype
											WHERE bsked_id='$bskedID'";
				return $this->Transact();
		}
		// ---- end added by LST --------------------------------------------------

	#function getRoomTypeBenefit($hcare_id, $benefit_id, $room_type){
	function getRoomTypeBenefit($bskedID, $room_type){
		global $db;

		/*$this->sql = "SELECT DISTINCT b.* FROM $this->tb_roomtype AS b
							WHERE b.hcare_id='$hcare_id'
							AND b.benefit_id='$benefit_id'
							AND roomtype_nr = '$room_type'";
		*/
		$this->sql = "SELECT DISTINCT b.* FROM $this->tb_roomtype AS b
							WHERE b.bsked_id='$bskedID'
							AND roomtype_nr = '$room_type'";

		if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}
				else
						return FALSE;
	}

	#function deleteRoomTypeBenefit($hcare_id, $benefit_id, $room_type) {
	function deleteRoomTypeBenefit($bskedID, $room_type) {
		/*$this->sql = "DELETE FROM $this->tb_roomtype
									WHERE hcare_id='$hcare_id'
							AND benefit_id='$benefit_id'
							AND roomtype_nr = '$room_type'";
		*/
		$this->sql = "DELETE FROM $this->tb_roomtype
									WHERE bsked_id='$bskedID'
							AND roomtype_nr = '$room_type'";
				return $this->Transact();
	}

	// Modified by LST -- 03.23.2009 --------------
	function getRVUBenefit($bskedID){
		global $db;

		/*$this->sql = "SELECT DISTINCT b.* FROM $this->tb_RVUrange AS b
							WHERE b.hcare_id='$hcare_id'
							AND b.benefit_id='$benefit_id'
							AND b.range_start='$range_start'";
		*/
		$this->sql = "SELECT DISTINCT b.* FROM $this->tb_RVUrange AS b
							WHERE b.bsked_id='$bskedID'
													ORDER BY range_start";

		if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}
				else
						return FALSE;
	}

	#function deleteRVUBenefit($hcare_id, $benefit_id, $range_start) {
	function deleteRVUBenefit($bskedID) {
		/*$this->sql = "DELETE FROM $this->tb_RVUrange
									WHERE hcare_id='$hcare_id'
							AND benefit_id='$benefit_id'
							AND range_start='$range_start'";
		*/
		$this->sql = "DELETE FROM $this->tb_RVUrange
									WHERE bsked_id='$bskedID'";
				return $this->Transact();
	}

	#function deleteBenefitSked($hcare_id, $benefit_id) {
	function deleteBenefitSked($bskedID) {
		/*$this->sql = "DELETE FROM $this->tb_bsked
									WHERE hcare_id='$hcare_id'
							AND benefit_id='$benefit_id'";
		*/
		$this->sql = "DELETE FROM $this->tb_bsked
									WHERE bsked_id='$bskedID'";

				return $this->Transact();
	}

	#function getBenefitSked($hcare_id, $benefit_id, $basis){
	function getBenefitSked($hcare_id, $benefit_id, $role_level = 0){
		global $db;

		/*
		$this->sql = "SELECT DISTINCT bs.* FROM seg_hcare_bsked AS bs
							WHERE bs.hcare_id='$hcare_id'
							AND bs.benefit_id='$benefit_id'
										AND bs.basis = '$basis'";
		*/

		$this->sql = "SELECT DISTINCT bs.* FROM $this->tb_bsked AS bs
							WHERE bs.hcare_id='$hcare_id'
							AND bs.benefit_id='$benefit_id'
													and tier_nr = $role_level";

		if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}
				else
						return FALSE;
	}

	#added by VAN 05-03-08
	function getBenefitSkedInfo($hcare_id, $benefit_id, $effective_date, $tier_nr = 0){
		global $db;

		$this->sql = "SELECT DISTINCT bs.* FROM $this->tb_bsked AS bs
							WHERE bs.hcare_id='$hcare_id'
							AND bs.benefit_id='$benefit_id'
							AND effectvty_dte='$effective_date'
													and tier_nr = $tier_nr";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}
				else
						return FALSE;
	}

	#added by VAN 05-05-08
	function getBenefitSkedByID($bskedID){
		global $db;

		$this->sql = "SELECT b.* FROM $this->tb_bsked AS b
							WHERE b.bsked_id='$bskedID'";

		if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}
				else
						return FALSE;
	}
	#---------------------

	#added by VAN 05-05-08
	function countSearchService($searchkey='',$areas, $maxcount=100,$offset=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		if ($areas=='DM'){
			# Drug and Medicine
			$this->sql="SELECT a.bestellnum AS code,a.artikelname AS name, a.description AS description
								FROM $this->tb_med AS a
							LEFT JOIN $this->prctable AS b
							ON a.bestellnum=b.bestellnum
							WHERE (a.artikelname LIKE '%$keyword%' OR a.bestellnum LIKE '%$keyword%')
							AND a.status NOT IN ($this->dead_stat)
							ORDER BY a.artikelname";
		}elseif($areas=='LB'){
			# Laboratory
			$this->sql="SELECT l.service_code AS code, l.name AS name, g.name AS description
							FROM $this->tb_labserv AS l
							LEFT JOIN $this->labgrptable AS g
							ON l.group_code=g.group_code
							WHERE (l.service_code LIKE '%$keyword%' OR l.name LIKE '%$keyword%')
							AND l.status NOT IN ($this->dead_stat)
							ORDER BY l.name";

		}elseif($areas=='RD'){
			# Radiology
			$this->sql="SELECT r.service_code AS code, r.name AS name, g.name AS description
							FROM $this->tb_radioserv AS r
							LEFT JOIN $this->radiogrptable AS g
							ON r.group_code=g.group_code
							WHERE (r.service_code LIKE '%$keyword%' OR r.name LIKE '%$keyword%')
							AND r.status NOT IN ($this->dead_stat)
							ORDER BY r.name";

		}elseif($areas=='OR'){
			# OR Procedures
			/*
			$this->sql="SELECT p.code AS code, p.description AS name
								FROM $this->tb_procedure AS p
							WHERE (p.description LIKE '%$keyword%' OR p.code LIKE '%$keyword%')
							ORDER BY p.description";
			*/
			#edited by VAN 08-27-08
			$this->sql="SELECT p.code AS code, p.description AS name
								FROM $this->tb_procedure AS p
							WHERE (p.description LIKE '%$keyword%' OR p.code LIKE '%$keyword%')
							AND is_active <> 0
							ORDER BY p.description";

		}elseif ($areas=='OA') {

			$this->sql="SELECT o.service_code AS code, o.name AS name, o.description
							FROM $this->tb_othersrv AS o
							WHERE (o.service_code LIKE '%$keyword%' OR o.name LIKE '%$keyword%')
							AND is_billing_related <> 0
							ORDER BY o.name";

		}elseif ($areas=='XC') {
			#Other Hospital Services
			$this->sql="SELECT o.service_code AS code, o.name AS name
								FROM $this->tb_otherHS AS o
							WHERE (o.service_code LIKE '%$keyword%' OR o.name LIKE '%$keyword%')
							AND o.status NOT IN ($this->dead_stat)
							ORDER BY o.name";
		}

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function SearchService($searchkey='',$areas, $maxcount=100,$offset=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		if ($areas=='DM'){
			# Drug and Medicine
			$this->sql="SELECT a.bestellnum AS code,a.artikelname AS name, a.description AS description
								FROM $this->tb_med AS a
							LEFT JOIN $this->prctable AS b
							ON a.bestellnum=b.bestellnum
							WHERE (a.artikelname LIKE '%$keyword%' OR a.bestellnum LIKE '%$keyword%')
							AND a.status NOT IN ($this->dead_stat)
							ORDER BY a.artikelname";
		}elseif($areas=='LB'){
			# Laboratory
			$this->sql="SELECT l.service_code AS code, l.name AS name, g.name AS description
							FROM $this->tb_labserv AS l
							LEFT JOIN $this->labgrptable AS g
							ON l.group_code=g.group_code
							WHERE (l.service_code LIKE '%$keyword%' OR l.name LIKE '%$keyword%')
							AND l.status NOT IN ($this->dead_stat)
							ORDER BY l.name";

		}elseif($areas=='RD'){
			# Radiology
			$this->sql="SELECT r.service_code AS code, r.name AS name, g.name AS description
							FROM $this->tb_radioserv AS r
							LEFT JOIN $this->radiogrptable AS g
							ON r.group_code=g.group_code
							WHERE (r.service_code LIKE '%$keyword%' OR r.name LIKE '%$keyword%')
							AND r.status NOT IN ($this->dead_stat)
							ORDER BY r.name";

		}elseif($areas=='OR'){
			# OR Procedures

			/*
			$this->sql="SELECT p.code AS code, p.description AS name
								FROM $this->tb_procedure AS p
							WHERE (p.description LIKE '%$keyword%' OR p.code LIKE '%$keyword%')
							ORDER BY p.description";
			*/
			#edited by VAN 08-27-08
			$this->sql="SELECT p.code AS code, p.description AS name
								FROM $this->tb_procedure AS p
							WHERE (p.description LIKE '%$keyword%' OR p.code LIKE '%$keyword%')
							AND is_active <> 0
							ORDER BY p.description";

		}elseif ($areas=='OA') {

			$this->sql="SELECT o.service_code AS code, o.name AS name, o.name_short as description
							FROM $this->tb_othersrv AS o
							WHERE (o.service_code LIKE '%$keyword%' OR o.name LIKE '%$keyword%')
							AND is_billing_related <> 0
							ORDER BY o.name";

		}elseif ($areas=='XC') {
			#Other Hospital Services
			$this->sql="SELECT o.service_code AS code, o.name AS name, o.name as description
								FROM $this->tb_otherHS AS o
							WHERE (o.service_code LIKE '%$keyword%' OR o.name LIKE '%$keyword%')
							AND o.status NOT IN ($this->dead_stat)
							ORDER BY o.name";
		}

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

	/**
	* @internal     Assigns the resultset count to class variable;
	*               Returns resultset of packages that satisfy the parameters.
	* @access       public
	* @author       Bong S. Trazo
	* @package      include
	* @subpackage   care_api_classes
	* @global       db - database object
	*
	* @param        searchkey   - area of department where delivery is made.
	* @param        maxcount    - maximum number of records returned.
	* @param        offset      - record index from where maxcount starts.
	* @return       Resultset of packages that satisfy the parameters.
	*/
	function countSearchPackage($searchkey='', $maxcount=100, $offset=0) {
		global $db;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$this->sql = "select *              \n
						 from seg_packages  \n
						 where package_name like '%$keyword%'
						 order by package_name";
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	#function getProductBenefit($hcare_id, $benefit_id, $item_type){
	function getProductBenefit($bskedID, $item_type){
		global $db;

		if ($item_type=='MS'){
			/*$this->sql = "SELECT p.hcare_id, p.benefit_id, p.bestellnum AS code, p.amountlimit
										FROM $this->tb_peritem AS p
								WHERE p.hcare_id='$hcare_id'
								AND p.benefit_id='$benefit_id'";
			*/
			$this->sql = "SELECT p.bestellnum AS code, p.amountlimit
										FROM $this->tb_peritem AS p
								WHERE p.bsked_id='$bskedID'";
		}elseif ($item_type=='HS'){
			/*$this->sql = "SELECT s.* FROM $this->tb_peritem_serv AS s
								WHERE s.hcare_id='$hcare_id'
								AND s.benefit_id='$benefit_id'";
			*/
			$this->sql = "SELECT s.* FROM $this->tb_peritem_serv AS s
								WHERE s.bsked_id='$bskedID'";
		}

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result;
			} else{
				 return FALSE;
			}
	}

	function getBenefitInfo($benefit_id){
		global $db;

		$this->sql = "SELECT * FROM $this->tb_benefits
							WHERE benefit_id='$benefit_id'";

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
	}

	function getInsuranceInfo($hcare_id){
		global $db;

		$this->sql = "SELECT * FROM $this->tb_insurance
							WHERE hcare_id= $hcare_id";

		if ($this->result=$db->Execute($this->sql)) {
		 if ($this->count=$this->result->RecordCount())
				 return $this->result->FetchRow();
		 else
			return FALSE;
			} else{
				 return FALSE;
			}
	}

	function useBenefit(){
		$this->coretable=$this->tb_benefits;
		$this->ref_array=$this->fld_benefit;

	}

	function saveBenefit(&$data){
		if(!is_array($data)) return FALSE;
		$this->useBenefit();
		$this->buffer_array=NULL;
		return $this->insertDataFromInternalArray();
	}

	function updateBenefitFromInternalArray($benefit_id, $benefit_desc, $bill_area){
		global $db;
		$this->sql = "UPDATE $this->tb_benefits
							SET benefit_desc = '$benefit_desc',
									bill_area = '$bill_area'
						 WHERE benefit_id = '$benefit_id'";
		#echo "sql = ".$this->sql;
		return $this->Transact();
	}

	function deleteBenefitItem($benefit_id) {
		global $db,$HTTP_SESSION_VARS;

		if(empty($benefit_id) || (!$benefit_id))
			return FALSE;

		$this->useBenefit();
		$this->sql="DELETE FROM $this->tb_benefits WHERE benefit_id='$benefit_id'";
		return $this->Transact();
	}

	function getConfinementInfo($confinetype_id){
		global $db;

		$this->sql = "SELECT * FROM $this->tb_conftype
							WHERE confinetype_id='$confinetype_id'";

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
	}

	function useConfinement(){
		$this->coretable=$this->tb_conftype;
		$this->ref_array=$this->fld_confinement;

	}

	function saveConfinement(&$data){
		if(!is_array($data)) return FALSE;
		$this->useConfinement();
		$this->buffer_array=NULL;
		return $this->insertDataFromInternalArray();
	}

	function updateConfinementFromInternalArray($confinetype_id, $confinetypedesc){
		global $db;

		$this->sql = "UPDATE $this->tb_conftype
							SET confinetypedesc = '$confinetypedesc',
								is_deleted = '".$this->data_array['is_deleted']."',
								modify_id = '".$this->data_array['sess_user_name']."',
									modify_time = NOW()
						 WHERE confinetype_id = '$confinetype_id'";
		return $this->Transact();
	}

	function deleteConfinementItem($confinetype_id) {
		global $db;

		if(empty($confinetype_id) || (!$confinetype_id))
			return FALSE;

		$this->useConfinement();
		$this->sql = "update $this->tb_conftype set         \n
						 is_deleted = 1,                    \n
						 modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',        \n
						 modify_time = NOW()                                            \n
						 where confinetype_id='$confinetype_id'";
//		$this->sql = "DELETE FROM $this->tb_conftype WHERE confinetype_id='$confinetype_id'";
		return $this->Transact();
	}

	function getOtherHospServInfo($service_code){
		global $db;

		$this->sql = "SELECT * FROM $this->tb_otherHS
							WHERE service_code='$service_code'";

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
	}

	function getAllOtherHospServ(){
	global $db;

		$this->sql ="SELECT * FROM $this->tb_otherHS
						 WHERE status NOT IN ($this->dead_stat)
								 ORDER BY name";
		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result;
			} else{
				 return FALSE;
			}
	}

	function useOtherHospServ(){
		$this->coretable=$this->tb_otherHS;
		$this->ref_array=$this->fld_otherhospserv;

	}

	function saveOtherHospServ(&$data){
		if(!is_array($data)) return FALSE;
		$this->useOtherHospServ();
		$this->buffer_array=NULL;
		return $this->insertDataFromInternalArray();
	}

	function updateOtherHospServFromInternalArray($service_code, $name, $price, $chrgprice, $exclude_hcareid){
		global $db,$HTTP_SESSION_VARS;

		$this->useOtherHospServ();
		$history = $this->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");

		$price = str_replace(",", "", $price);
		$chrgprice = str_replace(",", "", $chrgprice);
		$exclude_hcareid = ($exclude_hcareid == 0) ? 'NULL' : $exclude_hcareid;

		$this->sql = "UPDATE $this->coretable
							SET name = '$name',
									price = '$price',
								chrgprice = '$chrgprice',
								exclude_hcareid = $exclude_hcareid,
									modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
									modify_dt = NOW(),
								history = $history
						 WHERE service_code = '$service_code'";
		return $this->Transact();
	}

	function deleteOtherHospServItem($service_code) {
		global $db,$HTTP_SESSION_VARS;

		if(empty($service_code) || (!$service_code))
			return FALSE;

		$this->useOtherHospServ();
		#$this->sql="DELETE FROM $this->tb_otherHS WHERE service_code='$service_code'";
		$history = $this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		$this->sql="UPDATE $this->coretable ".
						" SET status='deleted', history=".$history.", ".
						" modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
						" WHERE service_code = '$service_code'";
		return $this->Transact();
	}
	#--------------------------------------

	/**
	* Gets the information of all insurance classes. Returns 2 dimensional array.
	*
	* @access public
	* @param string Field names of values to be fetched
	* @return mixed adodb record object or boolean
	*/
		function getInsuranceClassInfoArray($items='class_nr,class_id,name,LD_var AS "LD_var", description,status,history') {

			global $db;

				if ($this->result=$db->Execute("SELECT $items  FROM $this->tb_class")) {
						if ($this->result->RecordCount()) {
		return $this->result->GetArray();
		 //$this->row=NULL;
		//while($this->row[]=$this->result->FetchRow());
		//		return $this->row;
						} else {return FALSE;}
		} else { return FALSE; }
		}
	/**
	* Checks if the insurance company exists in the database based on its firm name ONLY.
	*
	* @access public
	* @param string Firm name
	* @return boolean
	* burn added: August 24, 2006
	*/
	function FirmName_exists($firm_name='') {
		global $db;
			//if(!$this->_internResolveFirmID($firm_id)) return FALSE;
#        echo " <br> hello burn enter FirmName_exists = ".$firm_name;
		if($this->result=$db->Execute("SELECT name FROM $this->tb_insurance WHERE name='$firm_name'")) {
			if($this->result->RecordCount()) {
#				echo " <br> hello burn enter FirmName_exists TRUE";
				return TRUE;
			} else {
#				echo " <br> hello burn enter FirmName_exists FALSE 1";
				return FALSE;
			}
		} else {
#			echo " <br> hello burn enter FirmName_exists FALSE 2";
			return FALSE;
		}
	 }
	/**
	* Checks if the insurance company exists in the database based on its firm id.
	*
	* @access public
	* @param string Firm id
	* @return boolean
	*/
	function Firm_exists($firm_id='') {
			global $db;
			if(!$this->_internResolveFirmID($firm_id)) return FALSE;
			if($this->result=$db->Execute("SELECT firm_id FROM $this->tb_insurance WHERE firm_id='$this->firm_id'")) {
					if($this->result->RecordCount()) {
					return TRUE;
				} else { return FALSE; }
		 } else { return FALSE; }
	 }
	 /**
	 * Alias of Firm_exists()
	 */
	function FirmIDExists($firm_id) {
			return $this->Firm_exists($firm_id);
	 }
	/**
	* Gets the usage frequency of an insurance company based on its firm id key.
	*
	* @access public
	* @param string Firm id
	* @return mixed integer or boolean
	*/
		function getUseFrequency($firm_id='') {

				global $db;

			if(!$this->_internResolveFirmID($firm_id)) return FALSE;
			if($this->result=$db->Execute("SELECT use_frequency FROM $this->tb_insurance WHERE firm_id=$this->firm_id")) {
					if($this->result->RecordCount()) {
						$this->row=$this->result->FetchRow();
					return $this->row['use_frequency'];
				} else { return FALSE; }
		 } else { return FALSE; }
		}
	/**
	* Increases usage frequency of an insurance company.
	*
	* @access public
	* @param string Firm id
	* @param int Increase step
	* @return boolean
	*/
	function updateUseFrequency($firm_id='',$step=1) {

#		echo " <br> hello burn enter Insurance updateUseFrequency firm_id= ".$firm_id;
		if(!$this->_internResolveFirmID($firm_id)) return FALSE;
		# Get last usage frequency value
		//$this->buffer=getUseFrequency($this->firm_id);
		//$this->sql="UPDATE $this->tb_insurance SET use_frequency=".($this->buffer+$step)." WHERE firm_id=$this->firm_id";
		$this->sql="UPDATE $this->tb_insurance SET use_frequency=(use_frequency + 1) WHERE firm_id='$this->firm_id'";
		if($this->result=$this->Transact($this->sql)) {
/*			echo " <br> hello burn enter updateUseFrequency TRUE 1 this->result->Affected_Rows()=";
			if($this->result->Affected_Rows()) {
								echo " <br> hello burn enter updateUseFrequency TRUE 2";
				return TRUE;
			} else { echo " <br> hello burn enter updateUseFrequency FALSE 1"; return FALSE; }
*/
#		   echo " <br> hello burn enter Insurance updateUseFrequency TRUE 2";
			 return TRUE;
		} else {
#			echo " <br> hello burn enter Insurance updateUseFrequency FALSE 2";
			return FALSE;
		}
	 }
	/**
	* Gets the insurance company's name.
	* @access public
	* @param string Firm id
	* @return mixed string or boolean
	*/
	 function getFirmName($firm_id) {
			 global $db;
		 if(!$this->_internResolveFirmID($firm_id)) return FALSE;

		 $this->sql="SELECT name FROM $this->tb_insurance WHERE firm_id='$this->firm_id'";
			if($this->result=$db->Execute($this->sql)) {
					if($this->result->RecordCount()) {
						$this->row=$this->result->FetchRow();
					return $this->row['name'];
				} else { return FALSE; }
		 } else { return FALSE; }
	 }
	/**
	* Gets the insurance company's complete information.
	*
	* The returned adodb record object contains  a row of array.
	* Each array contains the company's data with index keys as outlined in the <var>$fld_insurance</var> array.
	*
	* @access public
	* @param string Firm id
	* @return mixed adodb record object or boolean
	*/
	 function getFirmInfo($firm_id) {
			 global $db;
		 if(!$this->_internResolveFirmID($firm_id)) return FALSE;
		 $this->sql="SELECT * FROM $this->tb_insurance WHERE firm_id='$this->firm_id'";
			if($this->result=$db->Execute($this->sql)) {
					if($this->result->RecordCount()) {
						return $this->result;
				} else { return FALSE; }
		 } else { return FALSE; }
	}
	/**
	* Inserts new insurance company's complete information in the database.
	*
	* The data must be passed by reference with an associative  array.
	* The data must have the index keys as outlined in the <var>$fld_insurance</var> array.
	*
	* @access public
	* @param array Insurance company data
	* @return boolean
	*/
	function saveFirmInfoFromArray(&$data){
		global $HTTP_SESSION_VARS;
		$this->_useInsurance();
		$this->data_array=$data;
		$this->data_array['history']="Create: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";
		//$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['create_time']=date('YmdHis');
		return $this->insertDataFromInternalArray();
	}
	/**
	* Updates an insurance company's information in the database.
	*
	* The new data must be passed by reference with an associative  array.
	* The data must have the index keys as outlined in the <var>$fld_insurance</var> array.
	*
	* @access public
	* @param int Firm id
	* @param array Insurance company data
	* @return boolean
	*/
	function updateFirmInfoFromArray($nr,&$data){
		global $HTTP_SESSION_VARS;
		$this->_useInsurance();
		$this->data_array=$data;
		# remove probable existing array data to avoid replacing the stored data
		if(isset($this->data_array['firm_id'])) unset($this->data_array['firm_id']);
		if(isset($this->data_array['create_id'])) unset($this->data_array['create_id']);
		# Set the where condition
		$this->where="firm_id='$nr'";
		$this->data_array['history']=$this->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['modify_time']=date('YmdHis');
		##### param FALSE disables strict numeric id behaviour of the method
		return $this->updateDataFromInternalArray($nr,FALSE);
	}
	/**
	* Gets all active insurance firms' information.
	*
	* The returned adodb record object contains  a row of array.
	* Each array contains the company's data with index keys as outlined in the <var>$fld_insurance</var> array.
	*
	* @access public
	* @param string Sort directive. Defaults to "name".
	* @return mixed adodb record object or boolean
	*/
	function getAllActiveFirmsInfo($sortby='name'){
		global $db;
		if($sortby=='use_frequency') $sortby.=' DESC';
		$this->sql="SELECT * FROM $this->tb_insurance WHERE status NOT IN ($this->dead_stat) ORDER BY $sortby";
			if($this->result=$db->Execute($this->sql)) {
					if($this->result->RecordCount()) {
						return $this->result;
				} else { return FALSE; }
		 } else { return FALSE; }
		}
	/**
	* Similar to <var>getAllActiveFirmsInfo()</var>  but returns limited rows.
	*
	* The returned adodb record object contains  a row of array.
	* Each array contains the company's data with index keys as outlined in the <var>$fld_insurance</var> array.
	*
	* @access public
	* @param int Maximum number of rows returned
	* @param int Index of first row to be returned
	* @param string Sort field name. Defaults to "name".
	* @param string Sort direction. Defaults to "ASC" = ascending.
	* @return mixed adodb record object or boolean
	*/
	function getLimitActiveFirmsInfo($len=30,$so=0,$sortby='name',$sortdir='ASC'){
		global $db;
		$this->sql="SELECT * FROM $this->tb_insurance WHERE status NOT IN ($this->dead_stat) ORDER BY $sortby $sortdir";
			if($this->res['glafi']=$db->SelectLimit($this->sql,$len,$so)) {
					if($this->rec_count=$this->res['glafi']->RecordCount()) {
						return $this->res['glafi'];
				} else { return FALSE; }
		 } else { return FALSE; }
		}
	/**
	* Counts all active insurance firms.
	* @access public
	* @return integer
	*/
	function countAllActiveFirms(){
		global $db;
		$this->sql="SELECT firm_id FROM $this->tb_insurance WHERE status NOT IN ($this->dead_stat)";
			if($buffer=$db->Execute($this->sql)) {
				return $buffer->RecordCount();
		 } else { return 0; }
		}

	/**
	* Returns all active insurance firms.
	* @access public
	* @return integer
	*/
	function getAllActiveFirms() {
		global $db;
		$this->sql="SELECT hcare_id FROM $this->tb_insurance WHERE status NOT IN ($this->dead_stat)";
		if($buffer=$db->Execute($this->sql)) {
			if ($buffer->RecordCount())
				return $buffer;
			else
				return false;
		 } else { return false; }
	}

	/**
	* Searches for all active firms based on the supplied search key.
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the insurance firm data with the following index keys:
	* - firm_id = firm id
	* - name = insurance firm name
	* - phone_main = main phone number
	* - fax_main = main fax number
	* - addr_email = main email address
	*
	* @access public
	* @param string Search keyword
	* @return mixed adodb record object or boolean
	*/
		function searchActiveFirm($key){
		global $db, $sql_LIKE;
		if(empty($key)) return FALSE;
		if(is_numeric($key)) $sortby=" ORDER BY firm_id";
			else $sortby=" ORDER BY name";
		$select="SELECT firm_id,name,phone_main,fax_main,addr_email  FROM $this->tb_insurance ";
		$append=" AND status NOT IN ($this->dead_stat) $sortby";
		$this->sql="$select WHERE ( firm_id $sql_LIKE '$key%' OR name $sql_LIKE '$key%' OR addr_email $sql_LIKE '$key%' ) $append";
		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()){
				return $this->result;
				}else{
				$this->sql="$select WHERE ( firm_id $sql_LIKE '%$key' OR name $sql_LIKE '%$key' OR addr_email $sql_LIKE '%$key' ) $append";
				if($this->result=$db->Execute($this->sql)){
					if($this->result->RecordCount()){
						return $this->result;
					}else{
						$this->sql="$select WHERE ( firm_id $sql_LIKE '%$key%' OR name $sql_LIKE '%$key%' OR addr_email $sql_LIKE '%$key%' ) $append";
						if($this->result=$db->Execute($this->sql)){
							if($this->result->RecordCount()){
								return $this->result;
							}else{return FALSE;}
						}else{return FALSE;}
					}
				}else{return FALSE;}
			}
		 } else { return FALSE; }
		}
	/**
	* Searches similar to <var>searchActiveFirm()</var> but returns limited number of rows.
	*
	* For detailed structure of the returned data, see <var>searchActiveFirm()</var> method.
	* @access public
	* @param string Search keyword
	* @param int Maximum number of rows returned, default = 30 rows
	* @param int Start index offset, default 0 = start
	* @param string Field name to sort, default = "name"
	* @param string Sorting direction, default = ASC
	* @return mixed adodb record object or boolean
	*/
		function searchLimitActiveFirm($key,$len=30,$so=0,$oitem='name',$odir='ASC'){
		global $db, $sql_LIKE;
		if(empty($key)) return FALSE;
		$sortby=" ORDER BY $oitem $odir";
		$select="SELECT firm_id,name,phone_main,fax_main,addr_email  FROM $this->tb_insurance ";
		$append=" AND status NOT IN ($this->dead_stat) $sortby";
		$this->sql="$select WHERE ( firm_id $sql_LIKE '$key%' OR name $sql_LIKE '$key%' OR addr_email $sql_LIKE '$key%' ) $append";
		if($this->res['saf']=$db->SelectLimit($this->sql,$len,$so)){
			if($this->rec_count=$this->res['saf']->RecordCount()){
				return $this->res['saf'];
				}else{
				$this->sql="$select WHERE ( firm_id $sql_LIKE '%$key' OR name $sql_LIKE '%$key' OR addr_email $sql_LIKE '%$key' ) $append";
				if($this->res['saf']=$db->SelectLimit($this->sql,$len,$so)){
					if($this->rec_count=$this->res['saf']->RecordCount()){
						return $this->res['saf'];
					}else{
						$this->sql="$select WHERE ( firm_id $sql_LIKE '%$key%' OR name $sql_LIKE '%$key%' OR addr_email $sql_LIKE '%$key%' ) $append";
						if($this->res['saf']=$db->SelectLimit($this->sql,$len,$so)){
							if($this->rec_count=$this->res['saf']->RecordCount()){
								return $this->res['saf'];
							}else{return FALSE;}
						}else{return FALSE;}
					}
				}else{return FALSE;}
			}
		 } else { return FALSE; }
		}
	/**
	* Searches similar to searchActiveFirm() but returns the resulting number of rows.
	*
	* Unsuccessful search returns zero value (0).
	* @param string Search keyword
	* @return integer
	*/
		function searchCountActiveFirm($key){
		global $db, $sql_LIKE;
		if(empty($key)) return FALSE;
		$select="SELECT firm_id FROM $this->tb_insurance ";
		$append=" AND status NOT IN ($this->dead_stat)";
		$this->sql="$select WHERE ( firm_id $sql_LIKE '$key%' OR name $sql_LIKE '$key%' OR addr_email $sql_LIKE '$key%' ) $append";
		if($this->res['scaf']=$db->Execute($this->sql)){
			if($this->rec_count=$this->res['scaf']->RecordCount()){
				return $this->rec_count;
			}else{
				$this->sql="$select WHERE ( firm_id $sql_LIKE '%$key' OR name $sql_LIKE '%$key' OR addr_email $sql_LIKE '%$key' ) $append";
				if($this->res['scaf']=$db->Execute($this->sql)){
					if($this->rec_count=$this->res['scaf']->RecordCount()){
						return $this->rec_count;
					}else{
						$this->sql="$select WHERE ( firm_id $sql_LIKE '%$key%' OR name $sql_LIKE '%$key%' OR addr_email $sql_LIKE '%$key%' ) $append";
						if($this->res['scaf']=$db->Execute($this->sql)){
							if($this->rec_count=$this->res['scaf']->RecordCount()){
								return $this->rec_count;
							}else{return 0;}
						}else{return 0;}
					}
				}else{return 0;}
			}
		}else{return 0;}
		}

		// Added by LST - 03.29.2009 ---------------------------
		function getAccreditationNo($hcare_id) {
				global $db;

				$this->sql="SELECT accreditation_no FROM $this->tb_insurance
												WHERE hcare_id = $hcare_id";

				if($this->result=$db->Execute($this->sql)){
						if($this->result->RecordCount()) {
								$this->row=$this->result->FetchRow();
								return (is_null($this->row["accreditation_no"]) ? "" : $this->row["accreditation_no"]);
						} else return FALSE;
				}else {
						return FALSE;
				}
		}

	// Added by LST - 04.18.2011 ---------------------------
	function getHospitalEmployerNo($hcare_id) {
		global $db;

		$this->sql="SELECT employer_no FROM $this->tb_insurance
						WHERE hcare_id = $hcare_id";

		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()) {
				$this->row=$this->result->FetchRow();
				return (is_null($this->row["employer_no"]) ? "" : $this->row["employer_no"]);
			} else return FALSE;
		}else {
			return FALSE;
		}
	}

		function startTrans() {
				global $db;
				$db->StartTrans();
		}

		function failTrans() {
				global $db;
				$db->FailTrans();
		}

		function completeTrans() {
				global $db;
				$db->CompleteTrans();
		}

		/**
		* @internal     Return the resultset of health insurances satisfying the given filter based on the name.
		* @access       public
		* @author       Bong S. Trazo
		* @package      include
		* @subpackage   care_api_classes
		* @global       db - database object
		*
		* @param        sfilter - filter of the health insurance name.
		* @return       resultset of health insurances.
		*/
		function getHealthInsurances($sfilter = '') {
				global $db;

				$this->sql = "select hcare_id, firm_id
												 from $this->tb_insurance
												 where firm_id regexp '.*$sfilter.*' and
														firm_id <> ''
												 order by firm_id";
				if ($this->result = $db->Execute($this->sql)) {
						if ($this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}
				else
						return FALSE;
		}

	/**
	* @internal     Return the hcare id of the dummy health insurance associated with the real health insurance.
	* @access       public
	* @author       Bong S. Trazo
	* @package      include
	* @subpackage   care_api_classes
	* @global       db - database object
	*
	* @return       hcare id of the dummy health insurance, FALSE otherwise.
	*/
	function getDummyHcareID($hcare_id = 0) {
		global $db;

		if ($hcare_id == 0) {
			$this->sql = "select assoc_hcare_id
							 from care_insurance_firm
							 where assoc_hcare_id <> 0";
		}
		else {
			$this->sql = "select assoc_hcare_id
							 from care_insurance_firm
							 where hcare_id = '$hcare_id'";
		}
		if ($this->result = $db->Execute($this->sql)) {
			if ($this->result->RecordCount()) {
				if ($this->row = $this->result->FetchRow())
					return (($this->row["assoc_hcare_id"] != 0) ? $this->row["assoc_hcare_id"] : FALSE);
				else
					return FALSE;
			}
			else
				return FALSE;
		}
		else
			return FALSE;
	}

	/**
	* @internal     Return the hcare id of the health insurance associated with the dummy health insurance id.
	* @access       public
	* @author       Bong S. Trazo
	* @package      include
	* @subpackage   care_api_classes
	* @global       db - database object
	*
	* @param        hcare_id - dummy health insurance id.
	* @return       hcare id of the health insurance associated with the dummy health id, FALSE otherwise.
	*/
	function getDefaultPHIP_HID($hcare_id) {
		global $db;

		$this->sql = "select hcare_id from care_insurance_firm as cif
					where cif.assoc_hcare_id = {$hcare_id}";
		if ($this->result = $db->Execute($this->sql)) {
			if ($this->result->RecordCount()) {
				if ($this->row = $this->result->FetchRow())
					return $this->row['hcare_id'];
				else
					return FALSE;
			}
			else
				return FALSE;
		}
		else
			return FALSE;
	}
}

// ********** class PersonInsurance

/**
*  Personinsurance methods.
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/
class PersonInsurance extends Insurance {
	/**
	* Table name for  person's insurance data
	* @var string
	*/
		var $tb_person_insurance='care_person_insurance';

	#-----------added by VAN 09-01-07---------------
	var $tb_seg_insurance = 'seg_encounter_insurance';
	#---------------------------------------------

	/**
	* PID number
	* @var int
	*/
	var $pid;
	/**
	* Constructor
	* @param int PID number
	*/
	function PersonInsurance ($pid=0) {
			$this->pid=$pid;
	}
	/**
	* Sets the internal PID number buffer
	* @param int PID number
	*/
	function setPID($pid) {
			$this->pid=$pid;
	}
	/**
	* Resolves the PID number to be used.
	* @param int PID number
	*/
	function internResolvePID($pid) {
			if (empty($pid)) {
				if(empty($this->pid)) {
					return FALSE;
			} else { return TRUE; }
		} else {
				 $this->pid=$pid;
			return TRUE;
		}
	}
	/**
	* Updates a database table record with data from an array.
	*
	* @param array Data to save. By reference.
	* @param mixed interger or string
	* @return boolean
	*/
		function updateDataFromArray(&$array,$item_nr='') {
			global $dbtype;

		if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
			else $concatfx='concat';

		$x='';
		$v='';
		$sql='';
		if(!is_array($array)||empty($item_nr)||!is_numeric($item_nr)) return FALSE;
		while(list($x,$v)=each($array)) {
#		    $sql.="$x='$v',";
			if(stristr($v,$concatfx)||stristr($v,'null')) $sql.=" $x= $v,";
				else $sql.="$x= '$v',";
		}
		$sql=substr_replace($sql,'',(strlen($sql))-1);
				$this->sql="UPDATE $this->tb_person_insurance SET $sql WHERE item_nr=$item_nr";
		return $this->Transact();
	}
	/**
	*  Inserts data from an array into a database table.
	*
	* @param array Data to save. By reference.
	* @param mixed interger or string
	* @return boolean
	*/
		function insertDataFromArray(&$array) {
		$x='';
		$v='';
		$index='';
		$values='';
		if(!is_array($array)) return FALSE;
		while(list($x,$v)=each($array)) {
				$index.="$x,";
				$values.="'$v',";
		}
				$index=substr_replace($index,'',(strlen($index))-1);
				$values=substr_replace($values,'',(strlen($values))-1);

					$this->sql="INSERT INTO $this->tb_person_insurance ($index) VALUES ($values)";
		return $this->Transact();
	}
	/**
	* Gets person's insurance data based on his PID number.
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the  data with the following index keys:
	* - insurance_item_nr = insurance record primar key number
	* - insurance_type = insurance type
	* - insurance_nr = insurance number
	* - insurance_firm_id = firm id
	* - insurance_class_nr = insurance class number
	*
	* @access public
	* @param int PID number
	* @return mixed adodb record object or boolean
	*/
	function getPersonInsuranceObject($pid='') {
			global $db;

		if(!$this->internResolvePID($pid)) return FALSE;

			#-------commented by VAN 09-05-07-------
			/*
				$this->sql="SELECT
															 item_nr AS insurance_item_nr,
									 type AS insurance_type,
														 insurance_nr,
									 firm_id AS insurance_firm_id,
									 class_nr AS insurance_class_nr
									 FROM $this->tb_person_insurance
							WHERE
											pid='$this->pid' AND (is_void=0 OR is_void='')
							ORDER BY
											modify_time DESC";
			*/

			$this->sql="SELECT pi.*
									 FROM $this->tb_person_insurance AS pi
							WHERE
											pid='$this->pid' AND (is_void=0 OR is_void='')
							ORDER BY
											modify_time DESC";

			#echo "sql = ".$this->sql;
				if($this->result=$db->Execute($this->sql)) {
						if($this->result->RecordCount()) {
								return $this->result;
						} else { return FALSE;}
				} else { return FALSE; }
		}

	 #-------------added by VAN 09-01-07---------
	#----------added by VAN 09-03-07------
	#INSURANCE
	function getPersonInsuranceItems($pid) {
			global $db;
		#$refno = $db->qstr($refno);
		/*$this->sql="SELECT i.*, e.pid, f.firm_id, f.name
						FROM care_person_insurance AS i
						LEFT JOIN care_person AS e
						ON e.pid = i.pid
						INNER JOIN care_insurance_firm AS f
						ON f.hcare_id = i.hcare_id
						WHERE i.pid = '$pid'
						OR (i.pid=(SELECT parent_pid FROM seg_dependents AS dep
											WHERE (dep.dependent_pid='$pid' OR dep.dependent_pid='$pid')
											AND dep.status NOT IN ('cancelled','deleted','expired'))
								 )
						AND i.is_void=0
						ORDER BY f.firm_id";
		 */

		 $this->sql = "SELECT ci.* , f.firm_id, f.name,
										IF((pm.memcategory_id IS NOT NULL AND ci.hcare_id='18'),pm.memcategory_id,'NONE') AS memcategory_id,
										IF((m.memcategory_desc IS NOT NULL AND ci.hcare_id='18'),m.memcategory_desc,'NONE') AS memcategory_desc
										FROM care_person_insurance AS ci
										INNER JOIN care_insurance_firm AS f ON f.hcare_id = ci.hcare_id
										LEFT JOIN seg_pid_memcategory AS pm ON pm.pid=ci.pid
										LEFT JOIN seg_memcategory AS m ON m.memcategory_id=pm.memcategory_id
										WHERE (ci.pid ='$pid' /*OR
												ci.pid=(SELECT parent_pid
																FROM seg_dependents AS dep
																LEFT JOIN care_person_insurance AS i ON (i.pid=dep.parent_pid OR i.pid=dep.dependent_pid)
																WHERE (dep.parent_pid='$pid' OR dep.dependent_pid='$pid')
													AND IF(i.is_void,i.is_void,(SELECT is_void FROM care_person_insurance WHERE pid=dep.parent_pid LIMIT 1))=0
																AND dep.status NOT IN ('cancelled','deleted','expired') LIMIT 1)*/)
										AND ci.is_void=0 ORDER BY f.firm_id";

		if($this->result=$db->Execute($this->sql)) {
			$this->count = $this->result->RecordCount();
			return $this->result;
		} else { return false; }
	}

	function getInsurance($pid) {
			global $db;
		#$refno = $db->qstr($refno);
		$this->sql="SELECT * FROM $this->tb_person_insurance WHERE pid='$pid'";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

    //edited by jasper 06/20/2013
	function clearInsuranceList($encounter_nr) {
        $bSuccess = FALSE;

		$this->sql = "DELETE FROM $this->tb_seg_insurance WHERE encounter_nr='$encounter_nr'";
			#echo "<br>delete sql = ".$this->sql;
        $bSuccess = $this->Transact($this->sql);
/*        if ($bSuccess) {
            //added by jasper 06/20/2013
            $this->sql = "DELETE FROM seg_encounter_memcategory WHERE encounter_nr='$encounter_nr'";
            $bSuccess = $this->Transact($this->sql);
        }*/
        return $bSuccess;
			//return $this->Transact();
	}
    //edited by jasper 06/20/2013

	function clearInsuranceList_reg($patient_id) {
		global $db;
		$this->sql = "DELETE FROM $this->tb_person_insurance WHERE pid='$patient_id'";
			#echo "<br>delete sql = ".$this->sql;
			return $this->Transact();
	}

	#---------------------------------------------------------------
	#
	# orderArray is a one-dimensional array ....
	#
	#---------------------------------------------------------------
	function addInsurance($encounter_nr, $orderArray, $encoder, $current_date) {
				global $db;
//		$this->sql = "INSERT INTO $this->tb_seg_insurance(encounter_nr,hcare_id,modify_id,modify_dt,create_id,create_dt) VALUES('$encounter_nr',?,'$encoder','$current_date','$encoder','$current_date')";

				$bUpdated = FALSE;
				$bSuccess = FALSE;

				$this->sql = "select * from $this->tb_seg_insurance where encounter_nr = '$encounter_nr' and hcare_id = ".$orderArray[0];
				if ($result = $db->Execute($this->sql)) {
						if ($result->RecordCount()) {
								$this->sql = "update $this->tb_seg_insurance set
																 modify_id = '$encoder',
																 modify_dt = '$current_date'
																 where encounter_nr = '$encounter_nr'
																		and hcare_id = ".$orderArray[0];
								$bUpdated = TRUE;
								$bSuccess = $this->Transact($this->sql);
						}
				}

				if (!$bUpdated) {
						$this->sql = "INSERT INTO $this->tb_seg_insurance(encounter_nr,hcare_id,modify_id,modify_dt,create_id,create_dt) VALUES('$encounter_nr',".$orderArray[0].",'$encoder','$current_date','$encoder','$current_date')";
						$bSuccess = $this->Transact($this->sql);
				}

				return $bSuccess;
//		if($buf=$db->Execute($this->sql,$orderArray)) {
//			if($buf->RecordCount()) {
//				return true;
//			} else { return false; }
//		} else { return false; }
	}

	function addInsurance_reg($patient_id, $orderArray, $encoder, $current_date, $class_nr) {
		global $db;
//        $this->sql = "INSERT INTO $this->tb_person_insurance(pid,hcare_id,insurance_nr,is_principal,class_nr,modify_id,modify_time,create_id,create_time) VALUES('$patient_id',".$orderArray[0].",'".$orderArray[1]."',".$orderArray[2].",'$class_nr','$encoder','$current_date','$encoder','$current_date')";
//        return $this->Transact();

		if (!is_array($orderArray[0]))
			$inputarr = array($orderArray);
		else
			$inputarr = $orderArray;

		$this->sql = "INSERT INTO $this->tb_person_insurance(pid,hcare_id,insurance_nr,is_principal,class_nr,modify_id,modify_time,create_id,create_time) VALUES('$patient_id', ?, ?, ?,'$class_nr','$encoder','$current_date','$encoder','$current_date')";

		if($buf=$db->Execute($this->sql,$inputarr)) {
			return true;
		} else {
//			print_r($db->ErrorMsg(), true);
			return false;
		}
	}

	#added by VAN 05-04-09 temporarily, will be updated soon
	function addInsurance_reg2($patient_id, $orderArray, $encoder, $current_date, $class_nr) {
		global $db;

//        $this->sql = "INSERT INTO $this->tb_person_insurance(pid,hcare_id,insurance_nr,is_principal,class_nr,modify_id,modify_time,create_id,create_time) VALUES('$patient_id',".$orderArray[0][0].",'".$orderArray[0][1]."',".$orderArray[0][2].",'$class_nr','$encoder','$current_date','$encoder','$current_date')";
//        return $this->Transact();

		$this->sql = "INSERT INTO $this->tb_person_insurance(pid,hcare_id,insurance_nr,is_principal,class_nr,modify_id,modify_time,create_id,create_time) VALUES('$patient_id', ?, ?, ?,'$class_nr','$encoder','$current_date','$encoder','$current_date')";

		if($buf=$db->Execute($this->sql,$orderArray)) {
//            if($buf->RecordCount()) {
				return true;
//            } else { return false; }
		} else {
//			print_r($db->ErrorMsg());
			$this->setErrorMsg($db->ErrorMsg());
			return false;
		}
	}

	function addInsurance2($encounter_nr, $orderArray, $encoder, $current_date) {
//        global $db;
//        $this->sql = "INSERT INTO $this->tb_seg_insurance(encounter_nr,hcare_id,modify_id,modify_dt,create_id,create_dt) VALUES('$encounter_nr',?,'$encoder','$current_date','$encoder','$current_date')";
		global $db;

		$bUpdated = FALSE;
		$bSuccess = FALSE;

		foreach ($orderArray as $i=>$v) {
			$this->sql = "select * from $this->tb_seg_insurance where encounter_nr = '$encounter_nr' and hcare_id = ".$v[0];
			if ($result = $db->Execute($this->sql)) {
				if ($result->RecordCount()) {
					$this->sql = "update $this->tb_seg_insurance set
									 modify_id = '$encoder',
									 modify_dt = '$current_date'
									 where encounter_nr = '$encounter_nr'
										and hcare_id = ".$v[0];
					$bUpdated = TRUE;
					$bSuccess = $this->Transact();
				}
			}

			if (!$bUpdated) {
				$this->sql = "INSERT INTO $this->tb_seg_insurance(encounter_nr,hcare_id,modify_id,modify_dt,create_id,create_dt) VALUES('$encounter_nr',".$v[0].",'$encoder','$current_date','$encoder','$current_date')";
				$bSuccess = $this->Transact();
			}

			if (!$bSuccess) break;
		}

		return $bSuccess;
//		if($buf=$db->Execute($this->sql,$orderArray)) {
//			if($buf->RecordCount()) {
//				return true;
//			} else { return false; }
//        } else { return false; }
	}

	/*
	function updateInsurance_reg($patient_id, $orderArray, $encoder, $current_date, $class_nr) {
		global $db;
		#echo "<br>updateInsurance_reg";
		#print_r($orderArray);
		#$this->sql = "INSERT INTO $this->tb_person_insurance(pid,hcare_id,insurance_nr,is_principal,class_nr,modify_id,modify_time,create_id,create_time) VALUES('$patient_id',?,?,?,'$class_nr','$encoder','$current_date','$encoder','$current_date')";

		$this->sql = "UPDATE $this->tb_person_insurance
									SET insurance_nr = ?,
							is_principal = ?,
							class_nr = '".$class_nr."',
							modify_id = '".$encoder."',
							modify_time = '".$current_date."',
							create_id = '".$encoder."',
							create_time = '".$current_date."'
							WHERE pid = '".$patient_id."'
							AND hcare_id = ?  ";

		if($buf=$db->Execute($this->sql,$orderArray)) {
		#echo "<br>buf = ".$buf;
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else {
			print_r($db->ErrorMsg());
			return false;
		}
	}
	*/

	function getListInsurance($hcare_id) {
			global $db;
		#$refno = $db->qstr($refno);
		$this->sql="SELECT * FROM $this->tb_insurance WHERE hcare_id='$hcare_id'";
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
	#--------------------------------------------

	/**
	* Gets insurance class's information based on the class number.
	*
	* The returned  array contains the  data with the following index keys:
	* - class_id = class id
	* - name = class name
	*
	* @access public
	* @param int Class number
	* @return mixed array or boolean
	*/
		function getInsuranceClassInfo($class_nr) {
				global $db;
			if($this->result=$db->Execute("SELECT class_nr,class_id,name,LD_var AS  \"LD_var\", description,status,history FROM $this->tb_class WHERE class_nr=$class_nr")) {
						if($this->result->RecordCount()) {
				 $this->row= $this->result->FetchRow();
				 return $this->row;
			} else { return FALSE; }
		} else { return FALSE; }
	}

	#added by VAN 05-01-08
	function InsuranceBenefitsExists($hcare_id, $benefit_id){
			global $db;
		$this->sql="SELECT * FROM $this->tb_bsked
						WHERE hcare_id = $hcare_id
						AND benefit_id = $benefit_id";
		if($this->result=$db->Execute($this->sql)){
				if($this->result->RecordCount()) {
					$this->row=$this->result->FetchRow();
				return TRUE;
			} else return FALSE;
		}else {
				return FALSE;
		}
	}

	/**
		* @internal This function checks for any additional holder of a particular insurance number
		* @access public
		* @author Omick <omick16@gmail.com>
		* @name db
		* @global array instance of a db connection
		* @package include
		* @subpackage care_api_classes
		* @param string $firm_id the firm where a particular insurance belong, e.g.: PhilHealth
		* @param string $insurance_number the insurance number itself
		* @return bool returns a success if found
		*/
	function is_holder_existing($firm_id, $insurance_number) {
		global $db;
		$this->sql = "SELECT COUNT(insurance_nr) as num FROM $this->tb_person_insurance WHERE hcare_id=$firm_id AND insurance_nr='$insurance_number' AND is_principal=0";
		if ($this->result = $db->Execute($this->sql)) {
			if ($this->result->RecordCount() > 0) {
				$row = $this->result->FetchRow();
				if ((int)$row['num'] > 0) {
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
		else {
			return false;
		}

	}

	function delete_member_details_info($pid, $firm, $insurance_number) {
		global $db;
		$this->sql = "DELETE FROM seg_insurance_member_info WHERE pid=$pid AND hcare_id=$firm AND insurance_nr='$insurance_number'";
		echo $this->sql;
		$db->Execute($this->sql);
	}

	function save_member_details_info($details, $pid) {
		global $db;
		extract($details);

		$this->sql = "INSERT INTO seg_insurance_member_info(pid, hcare_id, insurance_nr, member_lname, member_fname, member_mname, street_name, brgy_nr, mun_nr)
									VALUES ($pid, $hcare_id, '$insurance_nr', '$member_lname', '$member_fname', '$member_mname', '$street_name', $brgy_nr, $mun_nr)";

		if ($this->result = $db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				return true;
			}
			else {
				$this->setErrorMsg("No row inserted.");
				return false;
			}
		}
		else {
			$this->setErrorMsg($db->ErrorMsg());
			return false;
		}
	}

	/**
	* Get the profile of principal holder.
	*
	* @access public
	* @param string insurance_nr
	* @param numeric hcare_id
	* @return resultset
	* @author LST - 05.10.2012
	*/
	function getPrincipalHolder($insurance_nr, $hcare_id) {
			global $db;

			$this->sql = "SELECT
			                cpi.pid,
										  cp.name_last last_name,
										  cp.name_first first_name,
										  cp.name_middle middle_name,
										  cp.brgy_nr,
										  cp.mun_nr,
										  street_name street,
										  sb.brgy_nr barangay,
										  sm.mun_nr municipality
										FROM
										  care_person_insurance cpi
										  INNER JOIN
										  care_person cp
										  ON cpi.pid = cp.pid
										  LEFT JOIN
										  seg_barangays sb
										  ON cp.brgy_nr = sb.brgy_nr
										  LEFT JOIN
										  seg_municity sm
										  ON cp.mun_nr = sm.mun_nr
										WHERE cpi.insurance_nr = '$insurance_nr'
										  AND cpi.is_principal != 0
										  AND cpi.is_void = 0
										  AND cpi.hcare_id = $hcare_id";
		if ($this->result = $db->Execute($this->sql)) {
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return false;
		}
		else
			return false;
	}

	/**
	* Clear the is_principal flag in care_person_insurance record of other persons with same number.
	*
	* @access public
	* @param string pid
	* @param string insurance_nr
	* @return boolean status of update
	* @author LST - 05.14.2012
	*/
	function clearOtherPrincipalHolder($pid, $insurance_nr) {
			$this->sql = "UPDATE
										  care_person_insurance
										SET
										  is_principal = 0
										WHERE pid != '$pid'
										  AND insurance_nr = '$insurance_nr'
										  AND is_principal != 0";
			return $this->Transact();
	}

	/**
	* Update the is_principal flag in care_person_insurance record of person with given pid with same insurance number.
	*
	* @access public
	* @param string pid
	* @param string insurance_nr
	* @return boolean status of update
	* @author LST - 05.14.2012
	*/
	function updateFoundPrincipalHolderByName($pid, $insurance_nr) {
			$this->sql = "UPDATE
										  care_person_insurance
										SET
										  is_principal = 1
										WHERE pid = '$pid'
										  AND insurance_nr = '$insurance_nr'
										  AND is_principal = 0";
			return $this->Transact();
	}

	/**
	* Get the id of principal holder, if exists, given the lastname, firstname, middlename and insurance no.
	* Assumption:  current patient is not the principal holder.
	*
	* @access public
	* @param string lastname
	* @param string firstname
	* @param string middlename
	* @param string insurance_nr
	* @return string pid
	* @author LST - 05.14.2012
	*/
	function getPrincipalHolderPID($lastname, $firstname, $middlename, $insuranceno) {
	    global $db;

	    $this->sql = "SELECT
                                cp.pid
                            FROM
                                care_person cp
                                INNER JOIN
                                care_person_insurance cpi
                                ON cp.pid = cpi.pid
                            WHERE name_last LIKE '$lastname%'
                                AND name_first LIKE '$firstname%'
                                AND name_middle LIKE '$middlename%'
                                AND cpi.insurance_nr = '$insuranceno'";

			if ($this->result = $db->Execute($this->sql)) {
				if ($this->result->RecordCount()){
						$row = $this->result->FetchRow();
						return $row['pid'];
				}
				else
					return false;
			}
			else
				return false;
	}

	function is_member_info_editable($pid, $firm_id, $insurance_nr) {
		global $db;
		$this->sql = "SELECT simi.hcare_id, simi.insurance_nr, simi.member_lname, simi.member_fname, simi.member_mname, simi.street_name,
									simi.brgy_nr, simi.mun_nr, cp.pid FROM seg_insurance_member_info simi LEFT JOIN care_person cp
									ON (simi.member_lname=cp.name_last AND simi.member_fname=cp.name_first)
									WHERE simi.pid=$pid AND simi.hcare_id=$firm_id AND simi.insurance_nr='$insurance_nr'";
// -- replaced last line of query by removing filter "cp.pid IS NULL" .... by LST ... 06.13.2012...
//									WHERE simi.pid=$pid AND simi.hcare_id=$firm_id AND simi.insurance_nr='$insurance_nr' AND cp.pid IS NULL";
		if ($this->result = $db->Execute($this->sql)) {
			if ($this->result->RecordCount() > 0) {
				$row = $this->result->FetchRow();
				return array('last_name' => $row['member_lname'],
										 'first_name' => $row['member_fname'],
										 'middle_name' => $row['member_mname'],
										 'street' => $row['street_name'],
										 'barangay' => $row['brgy_nr'],
										 'municipality' => $row['mun_nr'],
										 'fnr' => $row['hcare_id'],
										 'inr' => $row['insurance_nr']);
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	function isPrincipal($pid, $hid) {
		global $db;

		$bIsPrincipal = false;

		$sql = "SELECT i.is_principal AS Member FROM care_person_insurance AS i
					 where i.pid = '{$pid}' and i.hcare_id = {$hid}";
		if ($result = $db->Execute($sql)) {
			if ($row = $result->FetchRow()) {
				$bIsPrincipal = (is_null($row['Member'])) ? false : ($row['Member'] != 0);
			}
		}

		return $bIsPrincipal;
	}

	function isDependent($pid) {
		global $db;

		$parent_pid = '';

		$sql = "SELECT d.parent_pid AS parent
				FROM seg_dependents AS d
				where d.dependent_pid = '{$pid}' and upper(d.status) = 'MEMBER'";
		if ($result = $db->Execute($sql)) {
			if ($row = $result->FetchRow()) {
				$parent_pid = (is_null($row['parent'])) ? '' : $row['parent'];
			}
		}

		return ($parent_pid != '');
	}

	function hasNoPrincipal($pid, $hid) {
		$bNoPrincipal = false;

		if (!$this->isPrincipal($pid, $hid)) {
			if (!$this->isDependent($pid)) $bNoPrincipal = true;
		}

		return $bNoPrincipal;
	}

	#added by VAN 05-17-2010
	function clearPidInsuranceCategory($pid) {
		$this->sql = "DELETE FROM seg_pid_memcategory WHERE pid='$pid'";
		#echo "<br>delete sql = ".$this->sql;
		return $this->Transact();
	}

	function addPidInsuranceCategory($pid, $categoryArray) {
		global $db;
		$refno = $db->qstr($pid);
		#print_r($doctorArray);
		$this->sql = "INSERT INTO seg_pid_memcategory(pid,memcategory_id) VALUES($pid,?)";

		if($buf=$db->Execute($this->sql,$categoryArray)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}

	function clearEncounterInsuranceCategory($encounter_nr) {
		$this->sql = "DELETE FROM seg_encounter_memcategory WHERE encounter_nr='$encounter_nr'";
		#echo "<br>delete sql = ".$this->sql;
		return $this->Transact();
	}

	function addEncounterInsuranceCategory($encounter_nr, $categoryArray) {
		global $db;
		$refno = $db->qstr($encounter_nr);
		#print_r($doctorArray);
		$this->sql = "INSERT INTO seg_encounter_memcategory(encounter_nr,memcategory_id) VALUES($encounter_nr,?)";

		if($buf=$db->Execute($this->sql,$categoryArray)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}


    public function setEncounterInsuranceCategory($encounter_nr, $category)
    {
        global $db;
        $memCategory = $db->GetOne('SELECT memcategory_id FROM seg_memcategory WHERE memcategory_code=' . $db->qstr($category));
        $ok = $db->Replace('seg_encounter_memcategory',
            array(
                'encounter_nr' => $db->qstr($encounter_nr),
                'memcategory_id' => $db->qstr($memCategory)
            ),
            array('encounter_nr'),
            false
        );


        return $ok;
    }
	#-------------

}
?>