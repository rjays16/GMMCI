<?php
/**
* @package care_api
*/
/**
*/

#------------this class is created by VANESSA 10-09-07------------#
#------
#	Updated 08-20-2008 by LST
#-----
require('./roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');

class Hospital_Admin extends Core {
	/**#@+
	* @access private
	*/
	/**
	* Table name for person registration data.
	* @var string
	*/
		var $tb_hospital_info = 'seg_hospital_info';

	 var $tb_hospital_type = 'seg_hospital_type';
	/**
	* SQL query
	*/
	var $sql;
	var $result;
	/**
	* Universal flag
	* @var boolean
	*/
	var $ok;
	/**
	* Internal data buffer
	* @var array
	*/
	var $data_array;
	/**
	* Universal buffer
	* @var mixed
	*/
	var $buffer;
	/**
	* Returned row buffer
	* @var array
	*/
	var $row;

	/**
	* Field names of table seg_hospital_info
	* @var array
	*/
	var  $fld_info=array(
				 'hosp_type',
				 'hosp_name',
				 'hosp_id',
				 'house_case_dailyrate',
				 'addr_no_street',
								 'brgy_nr',
				 'zip_code',
				 'hosp_agency',
				 'hosp_country',
				 'accom_hrs_cutoff',
				 'pcf',
								 'default_city',
								 'authrep',
								 'designation');
	/**
	* Constructor
	* @param int PID number
	*/
	function Hospital_Admin() {
		$this->useHospitalInfo();
	}

	function useHospitalInfo() {
			$this->setRefArray($this->fld_info);
		$this->coretable=$this->tb_hospital_info;
	}

	function getAllHospitalType(){
	global $db;

		$this->sql ="SELECT * FROM $this->tb_hospital_type ORDER BY hosp_desc";
		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result;
			} else{
				 return FALSE;
			}
	}

		function getHospitalID() {
				global $db;

				$this->sql = "select hosp_id
												 from $this->tb_hospital_info
												 limit 1";
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				} else{
						return FALSE;
				}
		}

	function getAllHospitalInfo(){
	global $db;

		#$this->sql ="SELECT * FROM $this->tb_hospital_info WHERE hosp_id='$hosp_info'";
		#$this->sql ="SELECT * FROM $this->tb_hospital_info";
				#edited by VAN 03-26-08
				$this->sql ="SELECT r.region_nr, p.prov_nr, m.mun_nr, m.code, r.region_name, r.region_desc, p.prov_name, m.mun_name, b.brgy_name, h.*
												FROM $this->tb_hospital_info AS h
													 INNER JOIN seg_barangays as b ON b.brgy_nr = h.brgy_nr
													 INNER JOIN seg_municity AS m ON m.mun_nr = h.default_city
													 INNER JOIN seg_provinces AS p ON p.prov_nr = m.prov_nr
													 INNER JOIN seg_regions AS r ON r.region_nr = p.region_nr";

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
	}

	function getHospitalInfo() {
		return $this->getAllHospitalInfo();
	}

	function saveHospitalInfo(&$data){
		if(!is_array($data)) return FALSE;
		$dta = array('hosp_type'=>$data['hosp_type'],
					 'hosp_name'=>$data['hosp_name'],
					 'hosp_id'=>$data['hosp_id'],
					 'house_case_dailyrate'=>$data['house_case_dailyrate'],
//					 'hosp_addr1'=>$data['hosp_addr1'],
//					 'hosp_addr2'=>$data['hosp_addr2'],
										 'addr_no_street'=>$data['addr_no_street'],
										 'brgy_nr'=>$data['brgy_nr'],
										 'zip_code'=>$data['zip_code'],
					 'hosp_agency'=>$data['hosp_agency'],
					 'hosp_country'=>$data['hosp_country'],
					 'accom_hrs_cutoff'=>$data['accom_hrs_cutoff'],
					 'pcf'=>$data['pcf'],
										 'default_city'=>$data['mun_nr'],
										 'authrep'=>$data['authrep'],
										 'designation'=>$data['designation']);

		$this->setDataArray($dta);
//		$this->buffer_array=NULL;
		return $this->insertDataFromInternalArray();
	}

	function deleteHospInfo(){
		global $db;
		$this->sql = "DELETE FROM $this->tb_hospital_info";
		return $this->Transact();
	}

//	function updateHospInfoFromInternalArray($hosp_id, $hosp_type, $hosp_name, $doc_rate, $addr1, $addr2){
		/*
		if(empty($hosp_id)) return FALSE;
		$this->where=" hosp_id='$hosp_id'";
		$this->useHospitalInfo();
		$this->buffer_array=NULL;
		return $this->updateDataFromInternalArray($hosp_id);

		*/

	function updateHospInfoFromInternalArray(&$data){
		if(!is_array($data)) return FALSE;

		$dta = array('hosp_type'=>$data['hosp_type'],
					 'hosp_name'=>$data['hosp_name'],
					 'hosp_id'=>$data['hosp_id'],
					 'house_case_dailyrate'=>$data['house_case_dailyrate'],
//					 'hosp_addr1'=>$data['hosp_addr1'],
//					 'hosp_addr2'=>$data['hosp_addr2'],
										 'addr_no_street'=>$data['addr_no_street'],
										 'brgy_nr'=>$data['brgy_nr'],
										 'zip_code'=>$data['zip_code'],
					 'hosp_agency'=>$data['hosp_agency'],
					 'hosp_country'=>$data['hosp_country'],
					 'accom_hrs_cutoff'=>$data['accom_hrs_cutoff'],
					 'pcf'=>$data['pcf'],
										 'default_city'=>$data['mun_nr'],
										 'authrep'=>$data['authrep'],
										 'designation'=>$data['designation']);

//		$this->sql = "UPDATE $this->tb_hospital_info
//						  SET hosp_type = '$hosp_type',
//						      hosp_name = '$hosp_name',
//						      house_case_dailyrate = '$doc_rate',
//							  hosp_addr1  = '$addr1',
//							  hosp_addr2  = '$addr2',
//						 WHERE hosp_id = '$hosp_id'";
//		return $this->Transact();

		$this->setDataArray($dta);
		return $this->updateDataFromInternalArray();
	}

	function getHospitalType($hosp_type){
		global $db;
		$this->sql ="SELECT * FROM $this->tb_hospital_type WHERE hosp_type = '$hosp_type'";
		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
	}

	function getCutoff_Hrs() {
		global $db;

		$n_hrs = 0;

		$this->sql = "select accom_hrs_cutoff from $this->tb_hospital_info";
		if ($result = $db->Execute($this->sql)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
				$n_hrs = $row['accom_hrs_cutoff'];
			}
		}

		return($n_hrs);
	}

		function getDefinedPCF() {
				global $db;

				$n_pcf = 0;

				$this->sql = "select pcf from $this->tb_hospital_info";
				if ($result = $db->Execute($this->sql)) {
						if ($result->RecordCount()) {
								$row = $result->FetchRow();
								$n_pcf = $row['pcf'];
						}
				}

				return($n_pcf);
		}

		//addded by Raissa 03/11/09
		function countSearchOtherHospitals($group_code='', $searchkey='',$multiple=0,$maxcount=100,$offset=0,$area=''){
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				if ($multiple){
						$keyword = $searchkey;
						$this->sql = "SELECT * FROM seg_other_hospital
														 WHERE (id IN (".$keyword.")
													OR hosp_name IN (".$keyword."))
													AND (ISNULL(status) OR status NOT IN (".$this->dead_stat."))
													ORDER BY hosp_name";
				}else{
						# convert * and ? to % and &
						$searchkey=strtr($searchkey,'*?','%_');
						$searchkey=trim($searchkey);
						$searchkey = str_replace("^","'",$searchkey);
						$keyword = addslashes($searchkey);

						$this->sql = "SELECT * FROM seg_other_hospital
														 WHERE (id LIKE '%".$keyword."%'
															OR hosp_name LIKE '%".$keyword."%')
															AND (ISNULL(status) OR status NOT IN (".$this->dead_stat."))
															ORDER BY hosp_name";
				}
				#-----------------
				//echo "sql = ".$this->sql;
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		//added by Raissa 03/11/09
		function searchOtherHospitals($group_code, $searchkey='',$multiple=0,$maxcount=100,$offset=0,$area=''){
				global $db, $sql_LIKE, $root_path, $date_format;
				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

				if ($multiple){
						$keyword = $searchkey;
						$this->sql = "SELECT * FROM seg_other_hospital
														 WHERE (id IN (".$keyword.")
													OR hosp_name IN (".$keyword."))
													AND (ISNULL(status) OR status NOT IN (".$this->dead_stat."))
													ORDER BY hosp_name";
				}else{
						# convert * and ? to % and &
						$searchkey=strtr($searchkey,'*?','%_');
						$searchkey=trim($searchkey);
						#$suchwort=$searchkey;
						$searchkey = str_replace("^","'",$searchkey);
						$keyword = addslashes($searchkey);
						$this->sql = "SELECT * FROM seg_other_hospital
														 WHERE (id LIKE '%".$keyword."%'
															OR hosp_name LIKE '%".$keyword."%')
															AND (ISNULL(status) OR status NOT IN (".$this->dead_stat."))
															ORDER BY hosp_name";
				}
				#-----------------
				if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return false;}
				}else{return false;}
		}

		function deleteOtherHospital($code) {
				global $db;

				$history="";
				$this->sql = "SELECT history FROM seg_other_hospital WHERE id='$code'";
				$this->result=$db->Execute($this->sql);
				if ($row = $this->result->FetchRow()) {
						$history = $row["history"];
				}
				$history .= "'\nDeleted: ".$_SESSION['sess_user_name']." NOW()'";
				$this->sql="UPDATE seg_other_hospital ".
												" SET status='deleted', history=".$history.", ".
												" modify_id='".$_SESSION['sess_user_name']."', modify_dt=NOW() ".
												" WHERE id = '$code'";
				#echo $this->sql;
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return TRUE;
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

		function getOtherHospitalInfo($id=''){
				global $db;
				$this->sql = "SELECT * FROM seg_other_hospital WHERE id='$id'";
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result->FetchRow();
						}
						else{return FALSE;}
				}else{return FALSE;}
		}

	#added by VAN 04-26-09
	function getAllPHSInfo(){
		global $db;

		$this->sql ="SELECT * FROM seg_phs_config";

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
	}

	function getSystemCreatorInfo(){
		global $db;

		$this->sql ="SELECT * FROM seg_comp_info";

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
	}

	function getHospitalContact($param){
		global $db;

		$this->sql = "SELECT value as $param FROM care_config_global WHERE type = ".$db->qstr($param);
		
		if($this->result=$db->Execute($this->sql)){
			return $this->result->FetchRow();
		}

		return false;
	}
}
?>