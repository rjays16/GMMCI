<?php
/*
 * @package care_api
 */

require_once($root_path.'include/care_api_classes/class_core.php');

class BirthCertificate extends Core{
	/*
	 * Database table for the birth certificate info
	 * @var string
	 */
	var $tb_seg_cert_birth='seg_cert_birth';

	/*
	 * Fieldnames of seg_cert_birth table. Primary key "pid".
	 * @var array
	 */
	var $fld_seg_cert_birth=array(
				'pid',
				'registry_nr',
				'birth_place_basic',
				'birth_place_mun',
				'birth_place_prov',
				'birth_type',
				'birth_rank',
				'birth_order',
				'birth_weight',
				'm_name_first',
				'm_name_middle',
				'm_name_last',
				'm_citizenship',
				'm_religion',
				'm_occupation',
				'm_ethnic',
				'm_age',
				'm_total_alive',
				'm_still_living',
				'm_now_dead',
				'm_fetal_death',
				'm_residence_basic',
				'm_residence_brgy',
				'm_residence_mun',
				'm_residence_prov',
                'm_residence_country',
				'f_name_first',
				'f_name_middle',
				'f_name_last',
				'f_citizenship',
				'f_religion',
				'f_occupation',
				'f_ethnic',
				'f_age',
				'f_residence_basic',
                'f_residence_brgy',
                'f_residence_mun',
                'f_residence_prov',
                'f_residence_country',
                'is_married',
				'parent_marriage_date',
				'parent_marriage_place',
				'f_com_tax_nr',
				'f_com_tax_date',
				'f_com_tax_place',
				'm_com_tax_nr',
				'm_com_tax_date',
				'm_com_tax_place',
                'officer_date_sign',
				'officer_place_sign',
				'officer_title',
				'officer_name',
				'officer_address',
				'attendant_type',
				'birth_time',
				'attendant_name',
				'attendant_title',
				'attendant_address',
				'attendant_date_sign',
				'informant_name',
				'informant_relation',
				'informant_address',
				'informant_date_sign',
				'encoder_name',
				'encoder_title',
				'encoder_date_sign',
				'is_late_reg',
				'late_affiant_name',
				'late_affiant_address',
				'late_baby_citizenship',
				'late_reason',
				'late_purpose',
				'late_husband',
				'late_relationship',
				'affiant_com_tax_nr',
				'affiant_com_tax_date',
				'affiant_com_tax_place',
				'late_officer_date_sign',
				'late_officer_place_sign',
				'late_officer_title',
				'late_officer_name',
				'late_officer_address',
				'history',
				'modify_id',
				'modify_dt',
				'create_id',
				'create_dt',
				'non_resident_status',
				'is_tribalwed',
				'f_fullname',
                'is_subject_person',
                'affiant_com_tax_date2',
                'affiant_com_tax_place2',
                'receiver_name',
                'receiver_title',
                'm_residence_city',
                'f_residence_city',
                'receiver_date_sign',);
	var $refCode;
/*
	 * Constructor
	 * @param string primary key refCode
	 */
	function BirthCertificate($refCode){
		if(!empty($refCode)) $this->refCode=$refCode;
		$this->setTable($this->tb_seg_cert_birth);
		$this->setRefArray($this->fld_seg_cert_birth);
	}

	/*
	 * Sets the core object point to seg_cert_birth and corresponding field names.
	 * @access private
	 */
	function _useBirthCertificate(){
		$this->coretable= $this->tb_seg_cert_birth;
		$this->ref_array= $this->fld_seg_cert_birth;
	}

	/*
	 * Checks if birth certificate info exists based on PID number
	 * @param string ref_code - PID
	 * @return array of birth certificate info or boolean
	 */

	function getBirthCertRecord($refCode=''){
		global $db;

		if(empty($refCode) || (!$refCode)){
			$refCode=$this->refCode;
			if(empty($refCode) || (!$refCode))
				return FALSE;
		}

		$pid_format = " cb.pid='".$refCode."' ";

		$this->sql="SELECT cb.*, p.*, cb.birth_time, IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),'') AS age,
							(SELECT country_name FROM seg_country WHERE country_code=cb.m_citizenship) AS m_citizenship_name,
							(SELECT religion_name FROM seg_religion WHERE religion_nr=cb.m_religion) AS m_religion_name,
							(SELECT occupation_name FROM seg_occupation WHERE occupation_nr=cb.m_occupation) AS m_occupation_name,
							(SELECT country_name FROM seg_country WHERE country_code=cb.f_citizenship) AS f_citizenship_name,
							(SELECT religion_name FROM seg_religion WHERE religion_nr=cb.f_religion) AS f_religion_name,
							(SELECT occupation_name FROM seg_occupation WHERE occupation_nr=cb.f_occupation) AS f_occupation_name
						FROM $this->tb_seg_cert_birth AS cb
						INNER JOIN care_person AS p ON p.pid=cb.pid
						WHERE $pid_format";
		if ($buf=$db->Execute($this->sql)){
			if($this->count=$buf->RecordCount()) {
				return $buf->FetchRow();
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getBirthCertRecord

	 /*
	* Insert new birth certificate info into table seg_cert_birth
	* @param Array Data to by reference
	* @return boolean
	*/
	function saveBirthCertificateInfoFromArray(&$data){
		$this->_useBirthCertificate();
		$this->data_array=$data;
		//$this->data_array['description']=$HTTP_POST_VARS['description'];
		return $this->insertDataFromInternalArray();
	}# end function saveBirthCertificateInfoFromArray

	 /*
	* Update birth certificate info in table 'seg_cert_birth'
	* @param Array Data to by reference
	* @return boolean
	*/
	function updateBirthCertificateInfoFromArray(&$data){
		global $HTTP_SESSION_VARS, $dbtype;

#	echo "updateBirthCertificateInfoFromArray : data = ";
#	print_r ($data);
#	echo " <br> \n";

		$this->_useBirthCertificate();
		$this->data_array=$data;
		// remove probable existing array data to avoid replacing the stored data
		if(isset($this->data_array['create_id'])) unset($this->data_array['create_id']);
				if(isset($this->data_array['create_dt'])) unset($this->data_array['create_dt']);
						$this->data_array['create_dt'] = NULL;
		unset($this->data_array['modify_dt']);
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
#		return $this->updateDataFromInternalArray($nr);

		if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
			else $concatfx='concat';

		$pid_format = " pid='".$data['pid']."' ";

			#	Only the keys of data to be updated must be present in the passed array.
		$x='';
		$v='';
		while(list($x,$v)=each($this->ref_array)) {
            #commented by VAN 09-27-2012
			#if(isset($this->data_array[$v])&&(trim($this->data_array[$v])!='')) {
				$this->buffer_array[$v]=trim($this->data_array[$v]);
			#}
		}
		$elems='';
		while(list($x,$v)=each($this->buffer_array)) {
			# use backquoting for mysql and no-quoting for other dbs.
			if ($dbtype=='mysql') $elems.="`$x`=";
				else $elems.="$x=";

			#edited by VAN 05-30-2011
			#if(stristr($v,$concatfx)||stristr($v,'null')) $elems.=" $v,";
				#else $elems.="'$v',";
			if(stristr($v,$concatfx)||!strcasecmp($v,'null')) $elems.="$v,";
				else $elems.="'$v',";
		}
		# Bug fix. Reset array.
		reset($this->data_array);
		reset($this->buffer_array);
		$elems=substr_replace($elems,'',(strlen($elems))-1);
			$this->sql="UPDATE $this->coretable SET $elems, modify_dt=NOW() WHERE $pid_format";
	#echo "updateBirthCertificateInfoFromArray : this->sql = '".$this->sql."' <br> \n";
#	exit();
		return $this->Transact();
	}# end of function updateBirthCertificateInfoFromArray

	 /*
	* Update birth certificate info in table 'seg_cert_birth'
	* @param Array Data to by reference
	* @return boolean
	*/
	function updateBirthCertificateInfoFromArray2(&$data){

		$this->_useBirthCertificate();
		$this->data_array=$data;
		if(isset($this->data_array['pid']))
			unset($this->data_array['pid']);
		//if(isset($this->data_array['create_code'])) unset($this->data_array['create_code']);
		//$this->where='';
			# burn added : July 28, 2007
	/*
		if (intval($data['pid']))
			$pid_format = " (pid='".$data['pid']."' OR pid=".$data['pid'].") ";
		else
	*/
			$pid_format = " pid='".$data['pid']."' ";
		$this->where = $pid_format;
		return $this->updateDataFromInternalArray($data['pid'],FALSE);
	}# end function updateBirthCertificateInfoFromArray

	#added by VAN 05-20-08
	function convertWord($int){
		switch($int){
			case 1 :  $word = "first";
						 break;
			case 2 :  $word = "second";
						 break;
			case 3 :  $word = "third";
						 break;
			case 4 :  $word = "fourth";
						 break;
			case 5 :  $word = "fifth";
						 break;
			case 6 :  $word = "sixth";
						 break;
			case 7 :  $word = "seventh";
						 break;
			case 8 :  $word = "eighth";
						 break;
			case 9 :  $word = "ninth";
						 break;
			case 10 : $word = "tenth";
						 break;
		}
		return $word;
	}

} # end class BirthCertificate
?>