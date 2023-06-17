<?php
/*
 * @package care_api
 */

require_once($root_path.'include/care_api_classes/class_core.php');
 
class MedCertificate extends Core{
	/*
	 * Database table for the medical certificate info.
	 * @var string
	 */
	var $tb_seg_cert_med='seg_cert_med';
	
	/*
	 * Database table for the certificate of confinement info.
	 * @var string
	 */
	var $tb_seg_cert_conf='seg_cert_conf';
	
	/*
	 * Fieldnames of seg_cert_med table. Primary key "encounter_nr".
	 * @var array
	 */
	 //edited by VAN 03-27-08
	var $fld_seg_cert_med=array(
				'encounter_nr',
                'referral_nr',
				'diagnosis_verbatim',
				'procedure_verbatim',
				'remarks_verbatim', //added by genz 08/04/2014
				'is_medico_legal',
				'is_doc_sig',
				'dr_nr',
				'history',
				'modify_id',
				'modify_dt',
				'create_id',
				'create_dt',
				'scheduled_date',
				'consultation_date',
				'lic_no',
				'ptr_no',
				'prepared_by');
	
	/*
	 * Fieldnames of seg_cert_conf table. Primary key "encounter_nr".
	 * @var array
	 */
	var $fld_seg_cert_conf=array(
				'encounter_nr',
				'is_vehicular_accident',
				'is_medico_legal',
				'is_doc_sig',
				'dr_nr',
				'nurse_on_duty',
				'attending_doctor',
				'purpose',
				'requested_by',
				'relation_to_patient',
				'history',
				'modify_id',
				'modify_dt',
				'create_id',
				'create_dt',
				'operation_performed',
				'lic_no',
				'ptr_no',
				'admitting_diagnosis',
				'prepared_by'
				);			
				
	var $refCode;
/*
	 * Constructor
	 * @param string primary key refCode
	 */	
	function MedCertificate($refCode){
		if(!empty($refCode)) $this->refCode=$refCode;
		$this->setTable($this->tb_seg_cert_med);
		$this->setRefArray($this->fld_seg_cert_med);
	}

	/*
	 * Sets the core object point to seg_cert_med and corresponding field names.
	 * @access private
	 */
	function _useMedCertificate(){
		$this->coretable= $this->tb_seg_cert_med;
		$this->ref_array= $this->fld_seg_cert_med;
	}
	
	#added by VAN 03-27-08----
	/*
	 * Sets the core object point to seg_cert_conf and corresponding field names.
	 * @access private
	 */
	function _useConfCertificate(){
		$this->coretable= $this->tb_seg_cert_conf;
		$this->ref_array= $this->fld_seg_cert_conf;
	}
	#-------------------------

	/*
	 * Checks if medical certificate info exists based on encounter number
	 * @param string ref_code - encounter_nr
	 * @return array of med cert info or boolean
	 */

	function getMedCertRecord($refCode='',$ref_nr='', $cert_nr=''){
		global $db;
		
		if(empty($refCode) || (!$refCode)){
			$refCode=$this->refCode;
			if(empty($refCode) || (!$refCode))
				return FALSE;
		}
		
		if ($ref_nr)
			$sql_ref = " AND referral_nr='$ref_nr'";
		$this->sql="SELECT * FROM $this->tb_seg_cert_med WHERE encounter_nr='$refCode' $sql_ref AND cert_nr='$cert_nr'";
		if ($buf=$db->Execute($this->sql)){
			if($buf->RecordCount()) {
				return $buf->FetchRow();
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getMedCertRecord
    
    function getLatestCertNr($refCode='', $ref_nr=''){
        global $db;
        
        if(empty($refCode) || (!$refCode)){
            $refCode=$this->refCode;
            if(empty($refCode) || (!$refCode))
                return FALSE;
        }
        
        if ($ref_nr)
            $sql_ref = " AND referral_nr='$ref_nr'";
        $this->sql="SELECT cert_nr FROM $this->tb_seg_cert_med WHERE encounter_nr='$refCode' $sql_ref ORDER BY modify_dt DESC";
        if ($buf=$db->Execute($this->sql)){
            if($buf->RecordCount()) {
                return $buf->FetchRow();
            }else { return FALSE; }
        }else { return FALSE; }
    }

   /*
	* Insert new medical certificate info into table seg_cert_med
	* @param Array Data to by reference
	* @return boolean
	*/	
	function saveMedCertificateInfoFromArray(&$data){
		$this->_useMedCertificate();
		$this->data_array=$data;
		//$this->data_array['description']=$HTTP_POST_VARS['description'];
		return $this->insertDataFromInternalArray();
	}# end function saveMedCertificateInfoFromArray


   /*
	* Update medical certificate info in table seg_cert_med
	* @param Array Data to by reference
	* @return boolean
	*/
	function updateMedCertificateInfoFromArray(&$data){

		$this->_useMedCertificate();
		$this->data_array=$data;
		if(isset($this->data_array['encounter_nr'])) 
			unset($this->data_array['encounter_nr']);
		//if(isset($this->data_array['create_code'])) unset($this->data_array['create_code']);
		//$this->where='';
		if ($data['referral_nr'])
			$sql_ref = " AND referral_nr='".$data['referral_nr']."' ";
        $sql_ref .= " AND cert_nr='".$data['cert_nr']."' ";
			
		$this->where="encounter_nr='".$data['encounter_nr']."' $sql_ref";
		return $this->updateDataFromInternalArray($data['encounter_nr'],FALSE);
	}# end function updateMedCertificateInfoFromArray
	
	#-------added by VAN 03-27-08
	/*
	* Insert new certificate of confinement info into table seg_cert_conf
	* @param Array Data to by reference
	* @return boolean
	*/	
	function saveConfCertificateInfoFromArray(&$data){
		$this->_useConfCertificate();
		$this->data_array=$data;
		//$this->data_array['description']=$HTTP_POST_VARS['description'];
		return $this->insertDataFromInternalArray();
	}# end function saveConfCertificateInfoFromArray


   /*
	* Update certificate of confinement info in table seg_cert_conf
	* @param Array Data to by reference
	* @return boolean
	*/
	function updateConfCertificateInfoFromArray(&$data){
#print_r($data);
		$this->_useConfCertificate();
		$this->data_array=$data;
		if(isset($this->data_array['encounter_nr'])) 
			unset($this->data_array['encounter_nr']);
		//if(isset($this->data_array['create_code'])) unset($this->data_array['create_code']);
		//$this->where='';
		$this->where="encounter_nr='".$data['encounter_nr']."'";
		return $this->updateDataFromInternalArray($data['encounter_nr'],FALSE);
	}# end function updateConfCertificateInfoFromArray
	
	/*
	 * Checks if certificate of confinement info exists based on encounter number
	 * @param string ref_code - encounter_nr
	 * @return array of med cert info or boolean
	 */

	function getConfCertRecord($refCode=''){
		global $db;
		
		if(empty($refCode) || (!$refCode)){
			$refCode=$this->refCode;
			if(empty($refCode) || (!$refCode))
				return FALSE;
		}
		$this->sql="SELECT * FROM $this->tb_seg_cert_conf WHERE encounter_nr='$refCode'";
		if ($buf=$db->Execute($this->sql)){
			if($buf->RecordCount()) {
				return $buf->FetchRow();
			}else { return FALSE; }
		}else { return FALSE; }
	}//end fucntion getConfCertRecord
	#----------------------------------
} # end class MedCertificate

?>