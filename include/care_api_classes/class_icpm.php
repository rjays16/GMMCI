<?php
/*
 * @package care_api
 */

require_once($root_path.'include/care_api_classes/class_core.php');
 
class Icpm extends Core{
	/*
	 * Database table for the icd Code.
	 * @var string
	 */
	#var $tb_seg_icpm='seg_icpm';
	var $tb_seg_icpm='care_ops301_en';
	var $tb_seg_icpm_phic='seg_ops_rvs';
	/*
	var $fld_seg_icpm=array(
			'procedure_code',
			'description',
			'notes',
			'modify_id',
			'modify_time',
			'create_id',
			'create_time'
	);
	*/
	var $fld_seg_icpm=array(
				'code',
				'description',
				'inclusive',
				'exclusive',
				'notes',
				'std_code',
				'sub_level',
				'remarks',
				'term',
				'iscommon',
				'major',
				'rvu',
				'multiplier', 
				'modify_id',
				'modify_date',
				'create_id',
				'create_date'
				);
	
	#edited by VAN 08-27-08
	
	var $fld_seg_icpm_phic=array(
				'code',
				'description',
				'rvu',
				'is_active',
				'modify_id',
				'modify_date',
				'create_id',
				'create_date'
				);			
	
	var $refCode;
	/*
	 * Constructor
	 * @param string primary key refCode
	 */	
	function Icpm($ref_Code=''){
		if(!empty($ref_Code)) $this->refCode=$ref_Code;
		$this->setTable($this->tb_seg_icpm);
		$this->setRefArray($this->fld_seg_icpm);
	}

	/*
	 * Sets the core object point to care_icd10_en and corresponding field names.
	 * @access private
	 */
	function _useSegIcpm(){
		$this->coretable= $this->tb_seg_icpm;
		$this->ref_array= $this->fld_seg_icpm;
	}
	
	function _useSegIcpm_phic(){
		$this->coretable= $this->tb_seg_icpm_phic;
		$this->ref_array= $this->fld_seg_icpm_phic;
	}

	function getProcedures($cond=''){	
		global $db;
		
		if(!empty($cond)) $where=" WHERE ".$cond;
		$this->_useSegIcpm();
		$this->sql="SELECT * FROM $this->coretable $where";
		if($buf=$db->Execute($this->sql)){	
			if($buf->RecordCount()){
				return $buf;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}/* end of function getProcedures */

	function updateProcedures($data){	
		global $db;
		
/*		$arr=array();
		foreach ($data as $key => $value) {
			$temp = array(ucwords(strtolower($value)),$key);
			array_push($arr,$temp);			
		}
*/		$this->_useSegIcpm();
		$this->sql="UPDATE $this->coretable SET description=?, create_id='Bernard Klinch S. Clarito II',
							create_date=NOW() WHERE code=?";
		if($buf=$db->Execute($this->sql,$data)){	
			return TRUE;
		}else{ 
			return FALSE; 
		}
	}/* end of function getProcedures */
	
	/*
	 * Check if icpm code exists based on ref_code
	 * @param string ref_code  - ref_code
	 * @param boolean $dcode
	 * @return boolean
	 */	
	#function icpmCodeExists($ref_code=''){
	function icpmCodeExists($ref_code='',$phic=0){
		global $db;
	
		if(empty($ref_code)) return FALSE;
		//$this->sql="SELECT description FROM $this->tb_seg_icpm WHERE procedure_code='$ref_code'";
		if ($phic)
			$this->sql="SELECT description FROM $this->tb_seg_icpm_phic WHERE code='$ref_code'";
		else
			$this->sql="SELECT description FROM $this->tb_seg_icpm WHERE code='$ref_code'";
		
		#echo "phic = '$phic' exists sql = ".$this->sql;
		
		if($buf=$db->Execute($this->sql)){	
			if($buf->RecordCount()){
				return TRUE;
			}else {return FALSE;}
		}else {return FALSE;}
	}//end function icpmCodeExists
	
	 /*
	* Insert new icd code info into table seg_icpm
	* @param Array Data to by reference
	* @return boolean
	*/
	function saveIcpmInfoFromArray(&$data, $phic=0){
		global $HTTP_SESSION_VARS;
		
		if ($phic)
			$this->_useSegIcpm_phic();
		else
			$this->_useSegIcpm();
			
		$this->data_array=$data;
		
		$this->data_array['create_id'] = $HTTP_SESSION_VARS['sess_user_name'];
		#$this->data_array['create_time'] = date('YmdHis');
		$this->data_array['create_date'] = date('YmdHis');
		
		return $this->insertDataFromInternalArray();
	}// end function saveIcpmInfoFromArray
	
	#Note change by Mark on Mar 17. 2007 (procedure_code-> code ) table-> care_ops301_en
	//function getLimitIcpmInfo($len=30,$so=0,$sortby='procedure_code',$sortdir='ASC'){
	function getLimitIcpmInfo($len=30,$so=0,$sortby='code',$sortdir='ASC',$phic=0){
		global $db;
		
		if ($phic)
			$this->sql="SELECT * FROM $this->tb_seg_icpm_phic ORDER BY $sortby $sortdir";
		else
			$this->sql="SELECT * FROM $this->tb_seg_icpm ORDER BY $sortby $sortdir";
				
		if($this->res['code']=$db->SelectLimit($this->sql,$len,$so)){
			if($this->rec_count=$this->res['code']->RecordCount()){
				return $this->res['code'];
			}else{ return FALSE; }
		}else{ return FALSE; }
	}//end function getLimitIcpmInfo
	
	
	function getIcpmInfo($icpmCode='',$phic=0){
		global $db;	
		//echo "icpmcode=><br>".$icpmCode;
		if(empty($icpmCode)) return FALSE;
		//changed on March 17, 2007 
		//$this->sql="SELECT * FROM $this->tb_seg_icpm WHERE procedure_code='$icpmCode'";
		if ($phic)
			$this->sql="SELECT * FROM $this->tb_seg_icpm_phic WHERE code='$icpmCode'";
		else
			$this->sql="SELECT * FROM $this->tb_seg_icpm WHERE code='$icpmCode'";	
		#$this->sql="SELECT * FROM care_ops301_en WHERE code='$icpmCode'";
		#echo "<br>".$this->sql;
		if($this->res['mcode']=$db->Execute($this->sql)){
			if($this->res['mcode']->RecordCount()){
				return $this->res['mcode'];
			}else{return FALSE; };
		}else{return FALSE; }
	}//end fucntion getIcpmInfo
	
	
	function countAllIcpm(){
		global $db;
		//$this->sql="SELECT procedure_code FROM $this->tb_seg_icpm";
		$this->sql="SELECT code FROM $this->tb_seg_icpm";
		if($buffer=$db->Execute($this->sql)){
			return $buffer->RecordCount();
		} else { return 0; }
	}
	
	function searchCountActiveIcpm($key, $phic=0){
		global $db, $sql_LIKE;
	
		if(empty($key)) return FALSE;
		//$select="SELECT procedure_code FROM $this->tb_seg_icpm"; //Note changed on March 17, 2007 sat by mark
		if ($phic)
			$select="SELECT code FROM $this->tb_seg_icpm_phic";
		else
			$select="SELECT code FROM $this->tb_seg_icpm";
				
		//$this->sql="$select WHERE procedure_code $sql_LIKE '$key%' OR description $sql_LIKE '%$key'";
		// change table to be search from seg_icpm-> care_ops301_en 
		$this->sql="$select WHERE code $sql_LIKE '$key%' OR description $sql_LIKE '%$key'";
		
		if($this->res['scaf']=$db->Execute($this->sql)){
			if($this->rec_count=$this->res['scaf']->RecordCount()){
				return $this->rec_count;
			}else{
				//$this->sql="$select WHERE procedure_code $sql_LIKE '%$key' OR description $sql_LIKE '%$key'";
				//Note: change procedure_code -> code :modify by Mark Mar 17. 2007 
				$this->sql="$select WHERE code $sql_LIKE '%$key' OR description $sql_LIKE '%$key'";
				if($this->res['scaf']=$db->Execute($this->sql)){
					if($this->rec_count=$this->res['scaf']->RecordCount()){
						return $this->rec_count;
					}else{
						//$this->sql="$select WHERE (procedure_code $sql_LIKE '%$key%' OR description '%$key%')";
						//Note: change procedure_code -> code
						$this->sql="$select WHERE (code $sql_LIKE '%$key%' OR description '%$key%')";
						if($this->res['scaf']=$db->Execute($this->sql)){
							if($this->rec_count=$this->rec['scaf']->RecordCount()){
								return $this->rec_count;
							}else{return 0;}
						}else{return 0;}
					}
				}else{return 0;}
			}
		}else{return 0;}
	}//end function searchCountActiveIcd10	
	
	//Note change procedure_code to code
	//function searchLimitActiveIcpm($key,$len=30,$so=0,$oitem='procedure_code',$odir='ASC'){
	function searchLimitActiveIcpm($key,$len=30,$so=0,$oitem='code',$odir='ASC',$phic=0){
		global $db, $sql_LIKE;
		if(empty($key)) return FALSE;
		if ($phic)
			$select="SELECT * FROM $this->tb_seg_icpm_phic ";
		else
			$select="SELECT * FROM $this->tb_seg_icpm ";
				
		$append="ORDER BY $oitem $odir";
		//$this->sql="$select WHERE (procedure_code $sql_LIKE'$key%' OR description $sql_LIKE'$key%') ".$append;
		$this->sql="$select WHERE (code $sql_LIKE'$key%' OR description $sql_LIKE'$key%') ".$append;
		if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
			if($this->rec_count=$this->res['codec']->RecordCount()){
				return $this->res['codec'];
			}else{
				//$this->sql="$select WHERE (procedure_code $sql_LIKE '%key' OR description $sql_LIKE '%key') ".$append;
				$this->sql="$select WHERE (code $sql_LIKE '%key' OR description $sql_LIKE '%key') ".$append;
				if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
					if($this->rec_count=$this->res['codec']->RecordCount()){
						return $this->res['codec'];		
					}else{
						//$this->sql="$select WHERE (procedure_code $sql_LIKE '%$key%' OR description $sql_LIKE '%key% ) ".$append;
						$this->sql="$select WHERE (code $sql_LIKE '%$key%' OR description $sql_LIKE '%key% ) ".$append;
						if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
							if($this->rec_count=$this->res['codec']->RecordCount()){
								return $this->res['codec'];
							}else{return FALSE; }
						}else {return FALSE; }
					}
				}else{return FALSE; }
			}
		}else{return FALSE; }
	}
	

	function updateIcpmInfoFromArray($procedure_code, &$data, $phic=0){
		global $HTTP_SESSION_VARS;
		//$this->_useCare_icd10();
		if ($phic)
			$this->_useSegIcpm_phic();
		else
			$this->_useSegIcpm();
				
		$this->data_array=$data;
		
		//if(isset($this->data_array['procedure_code'])) unset($this->data_array['procedure_code']);
		#commented by VAN 02-25-08
		#if(isset($this->data_array['code'])) unset($this->data_array['code']);
		//if(isset($this->data_array['create_code'])) unset($this->data_array['create_code']);
		//$this->where='';
		//$this->where="procedure_code='$procedure_code'";
		$this->where="code='$procedure_code'";
		$this->data_array['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
		#$this->data_array['modify_time'] = date('YmdHis');
		$this->data_array['modify_date'] = date('YmdHis');
				
		return $this->updateDataFromInternalArray($procedure_code,FALSE);

	}
	
		/**
		* @internal     Return the resultset of ICPM satisfying the given filter.
		* @access       public
		* @author       Bong S. Trazo
		* @package      include
		* @subpackage   care_api_classes
		* @global       db - database object
		* 
		* @param        sfilter - filter of ICD (string).
		* @return       resultset of ICP.
		*/     
		function getSelectedICPM($sfilter = '') {
				global $db;
			 
				$sfilter = trim($sfilter); 
				$this->sql = "select code, description 
												 from $this->tb_seg_icpm 
												 where code regexp '^$sfilter.*'";
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
		* @internal     Return the resultset of ICP satisfying the given filter based on the description.
		* @access       public
		* @author       Vanessa A. Saren
		* @package      include
		* @subpackage   care_api_classes
		* @global       db - database object
		* 
		* @param        sfilter - filter of the ICP description (string).
		* @return       resultset of ICP.
		* 
		*/     
		function getSelectedICPDesc($sfilter = '') {
				global $db;
				
				$char_array = array(".","'","{","}","[","]","^","(",")","-","`",",","|");
				$sfilter = str_replace($char_array,"",$sfilter);
				$sfilter = trim($sfilter);
				$this->sql = "select code, description 
												 from care_ops301_en 
												 where replace(description,',','') regexp '.*$sfilter.*' and 
														description <> ''
												 order by description";
				if ($this->result = $db->Execute($this->sql)) {
						if ($this->result->RecordCount())
								return $this->result;
						else
								return FALSE;                        
				}
				else
						return FALSE;        
		}    
}//end class icd

?>