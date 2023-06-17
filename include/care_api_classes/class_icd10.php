<?php
/*
 * @package care_api
 */

require_once($root_path.'include/care_api_classes/class_core.php');

class Icd extends Core{
	/*
	 * Database table for the icd Code.
	 * @var string
	 */
	var $tb_care_icd10='care_icd10_en';

	/*
	 * Fieldnames of care_icd10_en table. Primary key "diagnosis_code".
	 * @var array
	 */
	var $fld_care_icd=array(
				'diagnosis_code',
				'description',
				'class_sub',
				'type',
				'inclusive',
				'exclusive',
				'notes',
				'std_code',
				'sub_level',
				'remarks',
				'extra_codes',
				'extra_subclass');
	var $refCode;

/*
	 * Constructor
	 * @param string primary key refCode
	 */
	function Icd($refCode){
		if(!empty($refCode)) $this->refCode=$refCode;
		$this->setTable($this->tb_care_icd10);
		$this->setRefArray($this->fld_care_icd);
	}

	/*
	 * Sets the core object point to care_icd10_en and corresponding field names.
	 * @access private
	 */
	function _useCare_icd10(){
		$this->coretable= $this->tb_care_icd10;
		$this->ref_array= $this->fld_care_icd;
	}

	/*
	 * Check if icd10 code exists based on diagnosis_code
	 * @param string ref_codeA  - diagnosis_code
	 * @param boolean $dcode
	 * @return boolean
	 */
	function icdCodeExists($refCode=''){
		global $db;

		if(empty($refCode)) return FALSE;
		$this->sql="SELECT description FROM $this->tb_care_icd10 WHERE diagnosis_code='$refCode'";
		if ($buf=$db->Execute($this->sql)){
			if($this->count = $buf->RecordCount()) {
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion icdCodeExists

   /*
	* Insert new icd code info into table care_icd10_en
	* @param Array Data to by reference
	* @return boolean
	*/
	function saveIcdInfoFromArray(&$data){

//TODO check for dual icd code
		$this->_useCare_icd10();
		$this->data_array=$data;
		//$this->data_array['description']=$HTTP_POST_VARS['description'];
		return $this->insertDataFromInternalArray();
	}// end function saveIcdInfoFromArray

	function getLimitIcd10Info($len=30,$so=0,$sortby='diagnosis_code',$sortdir='ASC'){
		global $db;

		$this->sql="SELECT * FROM $this->tb_care_icd10 ORDER BY $sortby $sortdir";
		if($this->res['code']=$db->SelectLimit($this->sql,$len,$so)){
			if($this->rec_count=$this->res['code']->RecordCount()){
				return $this->res['code'];
			}else{ return FALSE; }
		}else{ return FALSE; }
	}//end function getLimitIcd10Info


	function getIcd10Info($icdcode=''){
		global $db;

		if(empty($icdcode)) return FALSE;
		//$this->sql="SELECT * FROM $this->tb_care_icd10 WHERE diagnosis_code='$icdcode'";
	
		#added by genz
        $this->sql = "SELECT 
					  diagnosis_code,
					  description 
					FROM
					  (SELECT 
					    b.`code` AS diagnosis_code,
					    b.`description`
					  FROM
					    seg_case_rate_packages AS b 
					  WHERE b.`code` = '$icdcode' 
					    AND b.`description` <> '' 
					  UNION ALL 
					  SELECT 
					    a.diagnosis_code,
					    a.description 
					  FROM
					    care_icd10_en AS a 
					  WHERE a.`diagnosis_code` = '$icdcode' 
					    AND a.`description` <> '') AS t 
					GROUP BY diagnosis_code";

		#echo "<br>sql = ".$this->sql;
		if($this->res['mcode']=$db->Execute($this->sql)){
			if($this->res['mcode']->RecordCount()){
				return $this->res['mcode'];
			}else{return FALSE; };
		}else{return FALSE; }
	}//end fucntion getIcd10sInfo


	function countAllIcd10(){
		global $db;
		$this->sql="SELECT diagnosis_code FROM $this->tb_care_icd10";
		if($buffer=$db->Execute($this->sql)){
			return $buffer->RecordCount();
		} else { return 0; }
	}

	function searchCountActiveIcd10($key){
		global $db, $sql_LIKE;

		if(empty($key)) return FALSE;
		$select="SELECT diagnosis_code FROM $this->tb_care_icd10";
		$this->sql="$select WHERE diagnosis_code $sql_LIKE '$key%' OR replace(description,',','') $sql_LIKE '%$key%'";

		if($this->res['scaf']=$db->Execute($this->sql)){
			if($this->rec_count=$this->res['scaf']->RecordCount()){
				return $this->rec_count;
			}else{
				$this->sql="$select WHERE diagnosis_code $sql_LIKE '%$key' OR replace(description,',','') $sql_LIKE '%$key%'";
				if($this->res['scaf']=$db->Execute($this->sql)){
					if($this->rec_count=$this->res['scaf']->RecordCount()){
						return $this->rec_count;
					}else{
						$this->sql="$select WHERE (diagnosis_code $sql_LIKE '%$key%' OR replace(description,',','') '%$key%')";
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

	function searchLimitActiveIcd10($key,$len=100,$so=0,$oitem='diagnosis_code',$odir='ASC'){
		global $db, $sql_LIKE;
		if(empty($key)) return FALSE;
		$select="SELECT * FROM $this->tb_care_icd10 ";
		$append="ORDER BY $oitem $odir";
		$this->sql="$select WHERE (diagnosis_code $sql_LIKE'$key%' OR replace(description,',','') $sql_LIKE'%$key%') ".$append;
		if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
			if($this->rec_count=$this->res['codec']->RecordCount()){
				return $this->res['codec'];
			}else{
				$this->sql="$select WHERE (diagnosis_code $sql_LIKE '%key' OR replace(description,',','') $sql_LIKE '%key%') ".$append;
				if($this->res['codec']=$db->SelectLimit($this->sql,$len,$so)){
					if($this->rec_count=$this->res['codec']->RecordCount()){
						return $this->res['codec'];
					}else{
						$this->sql="$select WHERE (diagnosis_code $sql_LIKE '%$key%' OR replace(description,',','') $sql_LIKE '%key% ) ".$append;
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


//updateIcd10InfoFromArray($diagnosis_code,$HTTP_POST_VARS)
/*
 function updateCityTownInfoFromArray($nr,&$data){
		global $HTTP_SESSION_VARS;
		$this->_useCityTown();
		$this->data_array=$data;
		// remove probable existing array data to avoid replacing the stored data
		if(isset($this->data_array['nr'])) unset($this->data_array['nr']);
		if(isset($this->data_array['create_id'])) unset($this->data_array['create_id']);
		// clear the where condition
		$this->where='';
		$this->data_array['history']=$this->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		return $this->updateDataFromInternalArray($nr);
	}

*/
	function updateIcdInfoFromArray($diagnosis_code, &$data){

		$this->_useCare_icd10();
		$this->data_array=$data;

		#commented by VAN 02-25-08
		#if(isset($this->data_array['diagnosis_code'])) unset($this->data_array['diagnosis_code']);

		//if(isset($this->data_array['create_code'])) unset($this->data_array['create_code']);
		//$this->where='';
		$this->where="diagnosis_code='$diagnosis_code'";

		return $this->updateDataFromInternalArray($diagnosis_code,FALSE);

	}

    #added by VAN 10-28-08
    function saveICD($code){
      $this->sql="INSERT INTO care_icd10_en(diagnosis_code) VALUES ('".$code."')";
      return $this->Transact();
    }

    /**
    * @internal     Return the resultset of ICD satisfying the given filter.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    *
    * @param        sfilter - filter of ICD (string).
    * @return       resultset of ICD.
    */
    function getSelectedICD($sfilter = '') {
        global $db;

				$sfilter = trim($sfilter);
        /*$this->sql = "select diagnosis_code, description
                         from care_icd10_en
                         where diagnosis_code regexp '^$sfilter.*' and
                            description <> ''
                            order by diagnosis_code";*/
                            
        #optimize by VAN 09-19-2012
        // $this->sql = "select diagnosis_code, description
        //                  from care_icd10_en
        //                  where diagnosis_code like '$sfilter%' and
        //                     description <> ''
        //                     order by diagnosis_code"; 

        #added by genz
        $this->sql = "SELECT 
					  diagnosis_code,
					  description 
					FROM
					  (SELECT 
					    b.`code` AS diagnosis_code,
					    b.`description`
					  FROM
					    seg_case_rate_packages AS b 
					  WHERE b.`code` LIKE '%$sfilter%' 
					    AND b.`description` <> '' 
					  UNION ALL 
					  SELECT 
					    a.diagnosis_code,
					    a.description 
					  FROM
					    care_icd10_en AS a 
					  WHERE a.`diagnosis_code` LIKE '%$sfilter%' 
					    AND a.`description` <> '') AS t 
					GROUP BY diagnosis_code ";

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
    * @internal     Return the resultset of ICD satisfying the given filter based on the description.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    *
    * @param        sfilter - filter of the ICD description (string).
    * @return       resultset of ICD.
    */
    function getSelectedICDDesc($sfilter = '') {
        global $db;
				#edited by VAN
				$char_array = array(".","'","{","}","[","]","^","(",")","-","`",",","|");
				$sfilter = str_replace($char_array,"",$sfilter);
				$sfilter = trim($sfilter);
        // $this->sql = "select diagnosis_code, description
        //                  from care_icd10_en
								// 				 where replace(description,',','') regexp '.*$sfilter.*' and
        //                     description <> ''
        //                  order by description";

	    #added by genz
		$this->sql = "SELECT 
						  cie.diagnosis_code,
						  cie.description FROM 
						  care_icd10_en AS cie
						LEFT JOIN seg_case_rate_packages AS sed ON cie.`diagnosis_code` = sed.`code`
						WHERE REPLACE(cie.description, ',', '') REGEXP '.*$sfilter.*'
						  AND cie.description <> ''
						ORDER BY cie.description";

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