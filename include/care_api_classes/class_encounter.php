<?php
/**
* @package care_api
*/

/**
*/
require_once($root_path.'include/care_api_classes/class_notes.php');
/**
*  Patient encounter.
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance.
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/
class Encounter extends Notes {
	/**
	* Table name for encounter (admission) data
	* @var string
	*/
    var $tb_enc='care_encounter';
	/**
	* Table name for financial classes
	* @var string
	*/
	var $tb_fc='care_class_financial';
	/**
	* Table name for encounter's financial classes
	* @var string
	*/
	var $tb_enc_fc='care_encounter_financial_class';
	/**
	* Table name for encounter classes
	* @var string
	*/
	var $tb_ec='care_class_encounter';
	/**
	* Table name for insurance classes
	* @var string
	*/
	var $tb_ic='care_class_insurance';
	/**
	* Table name for person (registration) data
	* @var string
	*/
	var $tb_person='care_person';
	/**
	* Table name for person (registration) data
	* @var string
	* burn added: May 2, 2007
	*/
	var $tb_personell='care_personell';
	/**
	* Table name for city/town names
	* @var string
	*/
	var $tb_citytown='care_address_citytown';
	/**
	* Table name for encounter's location data
	* @var string
	*/
	var $tb_location='care_encounter_location';
	/**
	* Table name for discharge types
	* @var string
	*/
	var $tb_dis_type='care_type_discharge';
	/**
	* Table name for encounter's sick confirmations
	* @var string
	*/
	var $tb_sickconfirm='care_encounter_sickconfirm';
	/**
	* Table name for department general data
	* @var string
	*/
	var $tb_dept='care_department';
#------ added by Mark on April 17, 2007
	/**
	 * Table name for encounter diagnosis
	 * @var string
	 */
	var $tb_care_enc_diagnosis = 'care_encounter_diagnosis';

	/**
	 * Table name for encounter procedure
	 * @var string
	 */
	var $tb_care_enc_procedure = 'care_encounter_procedure';

	#-------------added 03-08-07-----------------
	/**
	* Table name for condition general data
	* @var string
	*/
	var $tb_condition='seg_conditions';
	var $tb_enc_condition='seg_encounter_condition';

	/**
	* Table name for disposition general data
	* @var string
	*/
	var $tb_disposition='seg_dispositions';
	var $tb_enc_disposition='seg_encounter_disposition';
	var $tb_enc_disposition_refer='seg_encounter_disposition_refer';

	/**
	* Table name for results general data
	* @var string
	*/
	var $tb_result='seg_results';
	var $tb_enc_result='seg_encounter_result';

		/**
	* Database table for the region address data.
	* @var string
	* burn added: March 10, 2007
	*/
	var $tb_regions='seg_regions';
	/**
	* Database table for the province address data.
	* @var string
	* burn added: March 10, 2007
	*/
	var $tb_provinces='seg_provinces';
	/**
	* Database table for the municipality/city address data.
	* @var string
	* burn added: March 10, 2007
	*/
	var $tb_municity='seg_municity';
	/**
	* Database table for the barangay address data.
	* @var string
	* burn added: March 10, 2007
	*/
	var $tb_barangays='seg_barangays';
	/**
	* Database table for the country of citizenship data.
	* @var string
	* burn added: March 15, 2007
	*/
	var $tb_country='seg_country';
	/**
	* Table name for religion data.
	* @var string
	*  burn added: March 15, 2007
	*/
	var $tb_religion='seg_religion';
	/**
	* Table name for occupation data.
	* @var string
	*  burn added: March 15, 2007
	*/
	var $tb_occupation='seg_occupation';

	var $tb_ward='care_ward';

	#-----------------------------------------
	/**
	* Table name for insurance firms' general data
	* @var string
	*/
	var $tb_insco='care_insurance_firm';
	/**
	* Table name for appointments data
	* @var string
	*/
	var $tb_appt='care_appointment';
	/**
	* Current encounter number
	* @var int
	*/
	var $enc_nr;
	/**
	* Name of user
	* @var string
	*/
	var $encoder;
	/**
	* Flag for ignoring certain events
	* @var boolean
	*/
	var $ignore_status=FALSE;
	/**
	* Flag for returning entire record or a part
	* @var boolean
	*/
	var $entire_record=FALSE;
	/**
	* Current encounter data in array
	* @var int
	*/
	var $encounter;
	/**
	* Status of preloaded encounter data
	* @var boolean
	*/
	var $is_loaded=FALSE;
	/**
	* Flag for returning single result or many
	* @var boolean
	*/
	var $single_result=FALSE;
	/**
	* Current record count
	* @var int
	*/
	var $record_count;
	/**
	* Current type number
	* @var int
	*/
	var $type_nr;
	/**
	* Current localization type number
	* @var int
	*/
	var $loc_nr;
	/**
	* Current group number
	* @var int
	*/
	var $group_nr;
	/**
	* Current date
	* @var string
	*/
	var $date;
	/**
	* Current time
	* @var string
	*/
	var $time;
	/**
	* Field names of care_encounter table
	* @var array
	*/
	var $tabfields=array('encounter_nr',
	                           'pid',
							   'encounter_date',
							   'encounter_class_nr',
							   'encounter_type',
							   'encounter_status',
								'official_receipt_nr',
								'er_opd_diagnosis',
								'consulting_dept_nr',
								'consulting_dr_nr',
							   'referrer_diagnosis',
							   'referrer_recom_therapy',
							   'referrer_dr',
							   'referrer_dept',
							   'referrer_institution',
							   'referrer_notes',
							   'financial_class',
							   'insurance_class_nr',
							   'current_ward_nr',
							   'current_room_nr',
							   'in_ward',
							   'area',
							   'current_dept_nr',
							   'current_firm_nr',
							   'current_att_dr_nr',
							   'consulting_dr',
							   'extra_service',
								'admission_dt',
							   'followup_date',
							   'followup_responsibility',
							   'post_encounter_notes',
								'informant_name',
								'info_address',
								'relation_informant',
							   'status',
							   'history',
							   'modify_id',
							   'modify_time',
							   'create_id',
							   'create_time',
								'is_medico',
								'is_confidential',
								'POI',
								'TOI',
								'DOI',
								'is_DOA',
								'is_DOA_reason',
								'category',
											 'parent_encounter_nr',
											 'chief_complaint',
                                             'smoker_history',
                                             'drinker_history');
	/**
	* Field names of care_encounter_sickconfirm table
	* @var array
	*/
	var $fld_sickconfirm=array(
								'nr',
								'encounter_nr',
	                            'date_confirm',
							   'date_start',
							   'date_end',
							   'date_create',
							   'diagnosis',
							   'dept_nr',
							   'insurance_co_nr',
							   'insurance_co_sub',
							   'status',
							   'history',
							   'modify_id',
							   'modify_time',
							   'create_id',
							   'create_time');

	#---------------added 03-08-07-------------

	/**
	* Field names of seg_encounter_condition table
	* @var array
	*/
	var $fld_condition=array(
								'encounter_nr',
								'cond_code',
								'modify_id',
							   'modify_time',
							   'create_id',
							   'create_time');

	/**
	* Field names of seg_encounter_disposition table
	* @var array
	*/
	var $fld_disposition=array(
								'encounter_nr',
								'disp_code',
								'modify_id',
							    'modify_time',
							    'create_id',
							    'create_time',
							    'hosp_name',//added by genz
							    'hosp_when', 
							    'hosp_add', //added by genz
							    'referral_reason_id'
							   );

	/**
	* Field names of seg_encounter_result table
	* @var array
	*/
	var $fld_result=array(
								'encounter_nr',
								'result_code',
								'modify_id',
							   'modify_time',
							   'create_id',
							   'create_time');
	/**
	 * Field names of care_encounter_diagnosis table
	 * @var array
	 */
	var $fld_care_enc_diagnosis = array(
						'encounter_nr',
						'encounter_type',
						'type_nr',
						'op_nr',
						'date',
						'code',
						'code_parent',
						'group_nr',
						'code_version',
						'localcode',
						'category_nr',
						'type',
						'localization',
						'diagnosing_clinician',
						'diagnosing_dept_nr',
						'status',
						'history',
						'modify_id',
						'modify_time',
						'create_id',
						'create_time'
		);


	#------------------------------------------
	/**
	* Constructor
	* @param int Encounter number
	*/
	function Encounter($enc_nr='') {
	    $this->enc_nr=$enc_nr;
		$this->setTable($this->tb_enc);
		$this->setRefArray($this->tabfields);
	}
	/**
	* Sets internal encounter number buffer to current encounter number
	* @param int Encounter number
	*/
	function setEncounterNr($enc_nr='') {
	    $this->enc_nr=$enc_nr;
	}
	/**
	* Sets internal encoder buffer to current encoder's name
	* @param string encoder's name
	*/
	function setEncoder($encoder='') {
	    $this->encoder=$encode;
	}
	/**
	* Sets internal ignore status flag to current ignore status
	* @param boolean Ignore status
	*/
	function setIgnoreStatus($bool=FALSE){
	    $this->ignore_status=$bool;
	}
	/**
	* Sets internal entire record flag to current record status
	* @param boolean Entire record status
	*/
	function setGetEntireRecord($bool=FALSE){
	    $this->entire_record=$bool;
	}
	/**
	* Sets core's table name variable to a table name
	* @param string Table name
	*/
	function setCoreTable($table){
	    $this->setTable($table);
	}
	/**
	* Sets internal single result flag to current single result status
	* @param boolean Single result status
	*/
	function setSingleResult($bool=FALSE){
	    $this->single_result=$bool;
	}
	/**
	* Gets a new encounter number.
	*
	* A reference number must be passed as parameter. The returned number will the highest number above the reference number PLUS 1.
	* @param int Reference Encounter number
	* @param int Encounter class number (1=inpatient, 2=outpatient)
	* @return integer
	*/
	function getNewEncounterNr($ref_nr,$enc_class_nr){
		global $db;
		$row=array();
		$opd_nr = (int)date('Y').'500000';
		$er_nr =  (int)date('Y').'300000';
		$ipd_nr = (int)date('Y').'000000';
		$dialysis_nr = (int)date('Y').'700000';	//added by cha, july 23, 2010
		$ic_nr = (int)date('Y').'800000';	//added by ngel, august 17, 2010
        $wellbaby_nr = (int)date('Y').'900000';

		if ($enc_class_nr == 1){
			#$this->sql="SELECT encounter_nr FROM $this->tb_enc WHERE encounter_nr BETWEEN '".$ref_nr."' AND '".$opd_nr."' AND encounter_type='".$enc_class_nr."' ORDER BY encounter_nr DESC";
			$this->sql="SELECT encounter_nr FROM $this->tb_enc WHERE encounter_nr BETWEEN '".$er_nr."' AND '".$ref_nr."' AND encounter_type='".$enc_class_nr."' ORDER BY encounter_nr DESC";
		}elseif ($enc_class_nr == 2){
			#$this->sql="SELECT encounter_nr FROM $this->tb_enc WHERE encounter_nr>='".$ref_nr."' AND encounter_type='".$enc_class_nr."' ORDER BY encounter_nr DESC";
			$this->sql="SELECT encounter_nr FROM $this->tb_enc WHERE encounter_nr BETWEEN '".$opd_nr."' AND '".$ref_nr."' AND encounter_type='".$enc_class_nr."' ORDER BY encounter_nr DESC";
		}
		else if($enc_class_nr == 5){
			$this->sql = "SELECT encounter_nr FROM $this->tb_enc WHERE encounter_nr BETWEEN ".$db->qstr($dialysis_nr)." AND ".$db->qstr($ref_nr)." AND encounter_type=".$db->qstr($enc_class_nr)." ORDER BY encounter_nr DESC";
		}
		elseif($enc_class_nr==6){
//			$this->sql = "SELECT encounter_nr FROM $this->tb_enc WHERE encounter_nr BETWEEN ".$db->qstr($ic_nr)." AND ".$db->qstr($ref_nr)." AND encounter_type=".$db->qstr($enc_class_nr)." ORDER BY encounter_nr DESC";
			$this->sql = "SELECT encounter_nr FROM $this->tb_enc where encounter_type=6 ORDER BY encounter_nr DESC ";
		}
        //wellbaby
        elseif($enc_class_nr==12){
            $this->sql = "SELECT encounter_nr FROM $this->tb_enc where encounter_nr BETWEEN '".$wellbaby_nr."' AND '".$ref_nr."' AND encounter_type='".$enc_class_nr."' ORDER BY encounter_nr DESC ";
        }
		else{
			#$this->sql="SELECT encounter_nr FROM $this->tb_enc WHERE encounter_nr BETWEEN '".$ref_nr."' AND '".$opd_nr."' AND (encounter_type='".$enc_class_nr."' OR encounter_type='4') ORDER BY encounter_nr DESC";
			$this->sql="SELECT encounter_nr FROM $this->tb_enc WHERE encounter_nr BETWEEN '".$ipd_nr."' AND '".$ref_nr."' AND (encounter_type='".$enc_class_nr."' OR encounter_type='4') ORDER BY encounter_nr DESC";
		}

		if($this->res['gnen']=$db->SelectLimit($this->sql,1)){
			if($this->res['gnen']->RecordCount()){
				$row=$this->res['gnen']->FetchRow();
				return $row['encounter_nr']+1;
		}else{
				/*echo $this->sql.'no xount';*/
				#return $ref_nr;
				if ($enc_class_nr == 2)
					return $opd_nr;
				elseif($enc_class_nr == 1)
					return $er_nr;
				elseif($enc_class_nr == 5)
					return $dialysis_nr;
				elseif($enc_class_nr == 6)
					return $ic_nr;
                //wellbaby    
                elseif($enc_class_nr == 12)
                    return $wellbaby_nr;    
				else
					return $ipd_nr;
		}
		}else{/*echo $this->sql.'no sql';*/return $ref_nr;}

		//$this->sql="SELECT encounter_nr FROM $this->tb_enc WHERE encounter_nr>=$ref_nr ORDER BY encounter_nr DESC";
		#echo "<br> sql = ".$this->sql;
		/*if($this->res['gnen']=$db->SelectLimit($this->sql,1)){
			if($this->res['gnen']->RecordCount()){
				$row=$this->res['gnen']->FetchRow();
				if (substr($row['encounter_nr'],0,4)!=(int)date('Y')){
					if ($enc_class_nr == 2)
						return $opd_nr;
					elseif($enc_class_nr == 1)
						return $er_nr;
					else
						return $ipd_nr;
				}else
				return $row['encounter_nr']+1;
			}else{
				if ($enc_class_nr == 2)
					return $opd_nr;
				elseif($enc_class_nr == 1)
					return $er_nr;
				else
					return $ipd_nr;

			}
		}else{return $ref_nr;}
		*/
	}

	/**
	* Resolves the encounter number internally.
	*
	* If there is no encounter number passed as parameter to a method,
	* the method will use the buffered current encounter number,  else it returns FALSE.
	* @param int Encounter number
	* @return boolean
	*/
	function internResolveEncounterNr($enc_nr='') {
	    if (empty($enc_nr)) {
		    if(empty($this->enc_nr)) {
			    return FALSE;
			} else { return true; }
		} else {
		     $this->enc_nr=$enc_nr;
			return true;
		}
	}
	/**
	* Gets the service class information of an encounter based on service type and encounter number.
	* @access private
	* @param int service class number
	* @param int Encounter number
	* @return mixed adodb record object or boolean
	*/
    function getServiceClass($type,$enc_nr) {
        global $db;

		if(empty($type)) return FALSE;
	    if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;

		$this->sql="SELECT   enfc.class_nr       AS sc_".$type."_class_nr,
			                          enfc.date_start  AS sc_".$type."_start,
									  enfc.date_end    AS sc_".$type."_end,
									  enfc.nr               AS sc_".$type."_nr,
									  fc.name             AS sc_".$type."_name,
									  fc.code              AS sc_".$type."_code,
									  fc.LD_var           AS \"sc_".$type."_LD_var\"
							FROM
							          $this->tb_fc AS fc,
									  $this->tb_enc_fc AS enfc
							WHERE
												enfc.encounter_nr='".$this->enc_nr."' AND fc.type='$type' AND enfc.class_nr=fc.class_nr
							 ORDER BY enfc.date_create ";

		if($this->single_result) $this->sql.=' LIMIT 1';

		if($this->result=$db->Execute($this->sql)) {
		    if($this->result->RecordCount()) {
			    // echo $this->sql.'<p>';
				 return $this->result;
		     } else { return FALSE;}
		} else { return FALSE;}
    }
	/**
	* Gets the Nursing care service class information of an encounter based on encounter number.
	*
	* The returned adodb record object contains an array with the data having the following index keys:
	* - sc_care_class_nr = the financial class number of encounter
	* - sc_care_start = the start date
	* - sc_care_end = the end date
	* - sc_care_nr = the service record's primary key number
	* - sc_care_name = the service name
	* - sc_care_code = the service code
	* - sc_care_LD_var = the variable's name for language dependent name of the service
	*
	* @param int Encounter number
	* @return mixed adodb record object or boolean
	*/
	function CareServiceClass($enc_nr='') {
	    return $this->getServiceClass('care',$enc_nr);
	}
	/**
	* Gets the room service class information of an encounter based on encounter number.
	*
	* The returned adodb record object contains an array with the data having the following index keys:
	* - sc_room_class_nr = the financial class number of encounter
	* - sc_room_start = the start date
	* - sc_room_end = the end date
	* - sc_room_nr = the service record's primary key number
	* - sc_room_name = the service name
	* - sc_room_code = the service code
	* - sc_room_LD_var = the variable's name for language dependent name of the service
	*
	* @param int Encounter number
	* @return mixed adodb record object or boolean
	*/
	function RoomServiceClass($enc_nr='') {
	    return $this->getServiceClass('room',$enc_nr);
	}
	/**
	* Gets the attending physician service class information of an encounter based on encounter number.
	*
	* The returned adodb record object contains an array with the data having the following index keys:
	* - sc_att_dr_class_nr = the financial class number of encounter
	* - sc_att_dr_start = the start date
	* - sc_att_dr_end = the end date
	* - sc_att_dr_nr = the service record's primary key number
	* - sc_att_dr_name = the service name
	* - sc_att_dr_code = the service code
	* - sc_att_dr_LD_var = the variable's name for language dependent name of the service
	*
	* @param int Encounter number
	* @return mixed adodb record object or boolean
	*/
	function AttDrServiceClass($enc_nr='') {
	    return $this->getServiceClass('att_dr',$enc_nr);
	}
	/**
	* Saves the service class information of an encounter based on service type and encounter number.
	* The service data must be packed in an associative array and passed by reference.
	* @access private
	* @param string service class 'care', 'room', 'att_dr'
	* @param array Service data for saving. Associative. By reference.
	* @param int Encounter number
	* @return boolean
	*/
    function saveServiceClass($type, &$val_array,$enc_nr='')
    {
	    global $db;

	    if(empty($type)||empty($val_array)) return FALSE;
        if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;

	    $this->sql="INSERT INTO $this->tb_enc_fc
	        (
	               encounter_nr,
				   class_nr,
				   date_start,
				   date_end,
				   date_create,
				   history,
				   modify_id,
				   modify_time,
				   create_id,
				   create_time
            )
			 VALUES
			 (
			    '$this->enc_nr',
				'".$val_array['sc_'.$type.'_class_nr']."',
				'".$val_array['sc_'.$type.'_start']."',
				'".$val_array['sc_'.$type.'_end']."',
				'".date('Y-m-d H:i:s')."',
				'Init.entry ".date('Y-m-d H:i:s')." = ".$val_array['encoder']."',
				'".$val_array['encoder']."',
				NULL,
				'".$val_array['encoder']."',
				NULL
			)";
        return $this->Transact();
    }
	/**
	* Saves the nursing care service class information of an encounter based on service type and encounter number.
	*
	* The service data must be packed in an associative array and passed by reference.
	* The data must have the following index keys:
	* - sc_care_class_nr = the financial class number of encounter
	* - sc_care_start = the start date
	* - sc_care_end = the end date
	* - sc_care_encoder = the user name
	*
	* @access public
	* @param array Service data for saving. Associative. By reference.
	* @param int Encounter number
	* @return boolean
	*/
	function saveCareServiceClass(&$val_array,$enc_nr) {
	    return $this->saveServiceClass('care',$val_array,$enc_nr);
	}
	/**
	* Saves the room service class information of an encounter based on service type and encounter number.
	*
	* The service data must be packed in an associative array and passed by reference.
	* The data must have the following index keys:
	* - sc_room_class_nr = the financial class number of encounter
	* - sc_room_start = the start date
	* - sc_room_end = the end date
	* - sc_room_encoder = the user name
	*
	* @access public
	* @param array Service data for saving. Associative. By reference.
	* @param int Encounter number
	* @return boolean
	*/
	function saveRoomServiceClass(&$val_array,$enc_nr) {
	    return $this->saveServiceClass('room',$val_array,$enc_nr);
	}
	/**
	* Saves the attending service class information of an encounter based on service type and encounter number.
	*
	* The service data must be packed in an associative array and passed by reference.
	* The data must have the following index keys:
	* - sc_att_dr_class_nr = the financial class number of encounter
	* - sc_att_dr_start = the start date
	* - sc_att_dr_end = the end date
	* - sc_att_dr_encoder = the user name
	*
	* @access public
	* @param array Service data for saving. Associative. By reference.
	* @param int Encounter number
	* @return boolean
	*/
	function saveAttDrServiceClass(&$val_array,$enc_nr) {
	    return $this->saveServiceClass('att_dr',$val_array,$enc_nr);
	}
	/**
	* Update the service class information of an encounter based on service type record's primary key number.
	* The service data must be packed in an associative array and passed by reference.
	* @access private
	* @param string service class 'care', 'room', 'att_dr'
	* @param array Service data for saving. Associative. By reference.
	* @return boolean
	*/
    function updateServiceClass($type, &$val_array)
    {
	    global $db;

		if(empty($val_array['sc_'.$type.'_class_nr'])) return FALSE;
	    $this->sql="UPDATE $this->tb_enc_fc SET
				   class_nr = '".$val_array['sc_'.$type.'_class_nr']."',
				   date_start = '".$val_array['sc_'.$type.'_start']."',
				   date_end = '".$val_array['sc_'.$type.'_end']."',
				   history =".$this->ConcatHistory("Update ".date('Y-m-d H:i:s')." ".$val_array['encoder']."\n").",
				   modify_id = '".$val_array['encoder']."'
			WHERE nr = '".$val_array['sc_'.$type.'_nr']."'";
		return $this->Transact();
    }
	/**
	* Updates the nursing care service class information of an encounter based on service type and record's primary key number.
	*
	* The service data must be packed in an associative array and passed by reference.
	* The data must have the following index keys:
	* - sc_care_nr = the record's primary key number
	* - sc_care_class_nr = the financial class number of encounter
	* - sc_care_start = the start date
	* - sc_care_end = the end date
	* - sc_care_encoder = the user name
	*
	* @access public
	* @param array Service data for saving. Associative. By reference.
	* @return boolean
	*/
	function updateCareServiceClass(&$val_array) {
		if(empty($val_array['sc_care_nr'])) return $this->saveCareServiceClass($val_array);
	        else return $this->updateServiceClass('care',$val_array);
	}
	/**
	* Updates the room service class information of an encounter based on service type and record's primary key number.
	*
	* The service data must be packed in an associative array and passed by reference.
	* The data must have the following index keys:
	* - sc_room_nr = the record's primary key number
	* - sc_room_class_nr = the financial class number of encounter
	* - sc_room_start = the start date
	* - sc_room_end = the end date
	* - sc_room_encoder = the user name
	*
	* @access public
	* @param array Service data for saving. Associative. By reference.
	* @param int Record's primary key number. (reserved)
	* @return boolean
	*/
	function updateRoomServiceClass(&$val_array,$nr) {
		if(empty($val_array['sc_room_nr'])) return $this->saveRoomServiceClass($val_array);
	        else return $this->updateServiceClass('room',$val_array);
	}
	/**
	* Updates the room service class information of an encounter based on service type and record's primary key number.
	*
	* The service data must be packed in an associative array and passed by reference.
	* The data must have the following index keys:
	* - sc_att_dr_nr = the record's primary key number
	* - sc_att_dr_class_nr = the financial class number of encounter
	* - sc_att_dr_start = the start date
	* - sc_att_dr_end = the end date
	* - sc_att_dr_encoder = the user name
	*
	* @access public
	* @param array Service data for saving. Associative. By reference.
	* @param int Record's primary key number. (reserved)
	* @return boolean
	*/
	function updateAttDrServiceClass(&$val_array,$nr) {
		if(empty($val_array['sc_att_dr_nr'])) return $this->saveAttDrServiceClass($val_array);
	        else return $this->updateServiceClass('att_dr',$val_array);
	}
	/**
	* Gets all service classes of a given class.
	*
	* @access private
	* @param string service class 'care', 'room', 'att_dr'
	* @return mixed adodb record object or boolean
	*/
    function getAllServiceClassesObject($type=''){
	    global $db;
		if(empty($type)) return FALSE;
		$this->sql="SELECT class_nr,class_id,code,name,LD_var AS \"LD_var\" FROM $this->tb_fc WHERE type='$type'";
		if($this->result=$db->Execute($this->sql)) {
		    if($this->result->RecordCount()) {
			    return $this->result;
		    } else { return FALSE;}
		} else { return FALSE;}
    }
	/**
	* Gets all service classes of 'care' class.
	*
	* @access public
	* @return mixed adodb record object or boolean
	*/
	function AllCareServiceClassesObject(){
	    return $this->getAllServiceClassesObject('care');
	}
	/**
	* Gets all service classes of 'room' class.
	*
	* @access public
	* @return mixed adodb record object or boolean
	*/
	function AllRoomServiceClassesObject(){
	    return $this->getAllServiceClassesObject('room');
	}
	/**
	* Gets all service classes of 'att_dr' class.
	*
	* @access public
	* @return mixed adodb record object or boolean
	*/
	function AllAttDrServiceClassesObject(){
	    return $this->getAllServiceClassesObject('att_dr');
	}
	/**
	* Gets all info of all encounter classes.
	* The returned adodb object contains rows of array.
	* Each array contains the data with the following index keys:
	* - class_nr = the class number, primary key (numeric)
	* - class_id = the class ID (alphanumeric)
	* - name = the name of class
	* - LD_var = the variable's name for language dependent name of class
	*
	* @access public
	* @return mixed adodb record object or boolean
	*/
	function AllEncounterClassesObject(){
	    global $db;
	    //$db->debug=true;
		$this->sql="SELECT class_nr,class_id,name,LD_var AS \"LD_var\" FROM $this->tb_ec ";
		#echo "sql = ".$this->sql;
		if($this->res['aec']=$db->Execute($this->sql)) {
		    if($this->res['aec']->RecordCount()) {
			    return $this->res['aec'];
		    } else { return FALSE;}
		} else { return FALSE;}
	}

	/**
	*	Returns the lastest encounter of a patient
	* 	@access public
	*	@param string, person id
	*	return an adodb record object or boolean FALSE
	*	burn added: March 10, 2007
	*/
	function getLastestEncounter($pid='0',$mod=0){
		global $db;

		$this->sql="SELECT * FROM $this->tb_enc WHERE pid='".$pid."' ORDER BY encounter_date DESC";
		#echo "getLastestEncounter : this->sql = '".$this->sql."' <br><br> \n";
		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}

	}

	function getLatestEncounter($pid='0'){
		global $db;

		#$this->sql="SELECT * FROM $this->tb_enc WHERE pid='".$pid."' AND is_discharged=0 ORDER BY encounter_date DESC";
        $this->sql="SELECT * FROM $this->tb_enc
                    WHERE pid='".$pid."'
                    AND status NOT IN ('deleted','hidden','inactive','void')
                    ORDER BY encounter_date DESC LIMIT 1";
		#echo "getLastestEncounter : this->sql = '".$this->sql."' <br><br> \n";
		if ($this->result=$db->Execute($this->sql)){
						if ($this->count = $this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}

	}

	function getEncounter($pid='0', $iswalkin){
		global $db;

		#echo "pid = $pid";
		if ($iswalkin){
			$this->sql = "SELECT cp.*,IF(fn_calculate_age(NOW(),cp.date_birth),
       					  fn_get_age(NOW(),cp.date_birth),'') AS age
								FROM care_person AS cp WHERE (pid = '$pid')";
		}else {
			$this->sql ="SELECT cp.pid, enc.encounter_nr,
							cp.name_last, cp.name_first, cp.name_2, cp.name_3, cp.name_middle,
							enc.encounter_date AS er_opd_datetime,
							dept.name_formal,
							cp.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
							cp.phone_1_nr, cp.phone_2_nr, cp.cellphone_1_nr, cp.cellphone_2_nr,
							cp.sex, cp.civil_status, cp.blood_group,
							IF(fn_calculate_age(enc.encounter_date,cp.date_birth),fn_get_age(enc.encounter_date,cp.date_birth),'') AS age,
							IF(fn_calculate_age(enc.encounter_date,date_birth),date_birth,'') AS date_birth,
							cp.place_birth,
							sc.country_name AS citizenship,
							sreli.religion_name AS religion,
							so.occupation_name AS occupation,
							cp.mother_fname, cp.mother_maidenname, cp.mother_mname, cp.mother_lname,
							cp.father_fname, cp.father_mname, cp.father_lname,
							cp.spouse_name, cp.guardian_name,
							enc.informant_name, enc.info_address, enc.relation_informant,
							enc.encounter_type,
							enc.encounter_class_nr,
							enc.encounter_status,
							enc.official_receipt_nr,
							enc.referrer_dept,
							enc.referrer_dr,
							enc.current_ward_nr,
							enc.referrer_diagnosis,

							enc.consulting_dept_nr AS er_opd_admitting_dept_nr,
							(
								SELECT name_formal FROM care_department WHERE nr = enc.consulting_dept_nr
							) AS er_opd_admitting_dept_name,
							enc.consulting_dr_nr AS er_opd_admitting_physician_nr,
	(
		SELECT CONCAT(cp_2.title,' ',cp_2.name_first,' ', IF(TRIM(cp_2.name_middle)<>'',CONCAT(LEFT(cp_2.name_middle,1),'. '),''), cp_2.name_last) AS fullname
		FROM $this->tb_enc AS enc_2, $this->tb_personell AS cpl_2, $this->tb_person AS cp_2
		WHERE (enc_2.pid='$pid' OR enc_2.pid=$pid) AND cpl_2.nr = enc_2.consulting_dr_nr AND cp_2.pid=cpl_2.pid
	) AS er_opd_admitting_physician_name,
							enc.current_dept_nr,
							enc.current_att_dr_nr AS attending_physician_nr,
	(
		SELECT CONCAT(cp_2.title,' ',cp_2.name_first,' ', IF(TRIM(cp_2.name_middle)<>'',CONCAT(LEFT(cp_2.name_middle,1),'. '),''), cp_2.name_last) AS fullname
		FROM $this->tb_enc AS enc_2, $this->tb_personell AS cpl_2, $this->tb_person AS cp_2
		WHERE enc_2.encounter_nr='$encounter_nr' AND cpl_2.nr = enc_2.current_att_dr_nr AND cp_2.pid=cpl_2.pid
	) AS attending_physician_name,
							enc.modify_id AS admitting_clerk,
							enc.create_id AS admitting_clerk_er_opd,
							enc.er_opd_diagnosis AS admitting_diagnosis,
							enc.admission_dt,
							enc.is_discharged,
							CONCAT(enc.discharge_date,' ',enc.discharge_time) AS discharge_dt,
							enc.create_time
						FROM $this->tb_enc AS enc, $this->tb_dept AS dept,
							$this->tb_barangays AS sb, $this->tb_municity AS sm,
							$this->tb_provinces AS sp, $this->tb_regions AS sr,
							$this->tb_country AS sc,
							$this->tb_person AS cp
								LEFT JOIN $this->tb_religion AS sreli ON sreli.religion_nr = cp.religion
								LEFT JOIN $this->tb_occupation AS so ON so.occupation_nr = cp.occupation
						WHERE (enc.pid='$pid')
							AND cp.pid=enc.pid
							AND dept.nr=enc.current_dept_nr
							AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr
							AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr
							AND sc.country_code=cp.citizenship
						ORDER BY er_opd_datetime DESC LIMIT 1" ;
		}

		 #echo "getEncounter : this->sql = '".$this->sql."' <br><br> \n";
		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}

	}
	/**
	*	Returns the patient's encounter information
	* 	@access public
	*	@param string, encounter number
	*	return an adodb record object or boolean FALSE
	*	burn added: March 10, 2007
	**/
	function getEncounterInfo($encounter_nr='', $opd=0){
		global $db;

	if ($opd){
		$mss_query = "(SELECT discountid FROM seg_charity_grants_pid
								WHERE pid=cp.pid
								AND DATE(grant_dte)=DATE(NOW())
								ORDER BY grant_dte DESC LIMIT 1) AS mss_id,
							(SELECT sd.parentid
								FROM seg_charity_grants_pid AS gp
								INNER JOIN seg_discount AS sd ON gp.discountid=sd.discountid
								WHERE pid=cp.pid
								AND DATE(grant_dte)=DATE(NOW())
								ORDER BY grant_dte DESC LIMIT 1) AS parent_mss_id,
							(SELECT sd.discountdesc
								FROM seg_charity_grants_pid AS gp
								INNER JOIN seg_discount AS sd ON gp.discountid=sd.discountid
								WHERE pid=cp.pid
								AND DATE(grant_dte)=DATE(NOW())
								ORDER BY grant_dte DESC LIMIT 1) AS mss_class";
	}else{
		$mss_query = "ss.discountid AS mss_id, sd.discountdesc AS mss_class,parentid AS parent_mss_id";
	}
	#commented by VAN 06-09-09
	#if (trim($encounter_nr))
		$enc_sql = "WHERE  enc.encounter_nr='$encounter_nr'";
	#else
		#$enc_sql = "";

	#echo "<br>van = ".$enc_sql."<br>";

	#edited by VAN 02-25-08
	#edited by Nick, 3/26/2014 - added mgh_setdte
	$this->sql ="SELECT cp.senior_ID, ps.job_function_title, ps.nr AS personnelID, enc_ins.hcare_id,ins.firm_id,enc.current_ward_nr, cp.pid, enc.encounter_nr,
							enc.current_room_nr, enc.current_dept_nr, enc.current_att_dr_nr, enc.consulting_dr_nr, enc.encounter_date,
							cp.name_last, cp.name_first, cp.name_2, cp.name_3, cp.name_middle, cp.civil_status,
							cp.name_maiden, cp.employer, w.ward_id,w.name AS ward_name, ms.mss_no,
							/*$mss_query,*/ cp.age AS age2,
							enc.encounter_date AS er_opd_datetime, enc.is_DOA_reason, enc.is_DOA,
							enc.is_medico,enc.POI,enc.TOI,enc.DOI,enc.category,
							dept.name_formal, cp.fromtemp,
							cp.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
							cp.phone_1_nr, cp.phone_2_nr, cp.cellphone_1_nr, cp.cellphone_2_nr,
							cp.sex, cp.civil_status, cp.blood_group,
							IF(fn_calculate_age(IF(enc.encounter_type IN (3,4),enc.admission_dt,enc.encounter_date),cp.date_birth),fn_get_age(IF(enc.encounter_type IN (3,4),enc.admission_dt,enc.encounter_date),cp.date_birth),age) AS age,
							cp.suffix,
							cp.date_reg,
							cp.date_birth,
							cp.place_birth,
							sc.country_name AS citizenship2,
							sc.citizenship,
							sreli.religion_name AS religion,
							so.occupation_name AS occupation,
							cp.mother_fname, cp.mother_maidenname, cp.mother_mname, cp.mother_lname,
							cp.father_fname, cp.father_mname, cp.father_lname,
							cp.spouse_name, cp.guardian_name,
							enc.informant_name, enc.info_address, enc.relation_informant,
							enc.encounter_type,
							enc.encounter_class_nr,
							enc.encounter_status,
							enc.official_receipt_nr,
							enc.referrer_dept,
							enc.referrer_dr,
							cp.create_id AS registered_by,
							enc.create_id AS encoded_by,
							enc.referrer_diagnosis,
							enc.chief_complaint,
							enc.er_opd_diagnosis,
                            enc.encounter_date,
                            enc.admission_dt, enc.is_maygohome,
							enc.current_att_dr_nr, enc.current_dept_nr,
							enc.consulting_dept_nr AS er_opd_admitting_dept_nr,
							(
								SELECT name_formal FROM care_department WHERE nr = enc.consulting_dept_nr
							) AS er_opd_admitting_dept_name,
							enc.consulting_dr_nr AS er_opd_admitting_physician_nr,
    CONCAT(IF(cp.title,cp.title,'DR.'),fn_get_personell_name(enc.consulting_dr_nr)) AS er_opd_admitting_physician_name,
							enc.current_dept_nr,
							enc.current_att_dr_nr AS attending_physician_nr,
    CONCAT(IF(cp.title,cp.title,'DR.'),fn_get_personell_name(enc.current_att_dr_nr)) AS attending_physician_name,
							enc.modify_id AS admitting_clerk,
							enc.create_id AS admitting_clerk_er_opd,
							enc.er_opd_diagnosis AS admitting_diagnosis,
							enc.admission_dt,
							enc.is_discharged, enc.encounter_type, enc.is_medico,
							enc.discharge_date, enc.smoker_history, enc.drinker_history,
							CONCAT(enc.discharge_date,' ',enc.discharge_time) AS discharge_dt,
                            enc.encounter_status,
							enc.create_time, enc_diag.code, w.accomodation_type,

							IF(((dep.dependent_pid IS NOT NULL) OR (ps.nr IS NOT NULL)),
								IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20),
									'PHS',
									''
								),
								IF(SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discountid)),20)='SC',
									IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20),
										'SC',
										'SC'
									),
									IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20) IS NULL,
										'',
										IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_type)),20)=2,
											SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discountid)),20),
											SUBSTRING(MAX(CONCAT(se.grant_dte,se.discountid)),20)
										)
									)
								)
							) AS discountid,

							IF(((dep.dependent_pid IS NOT NULL) OR (ps.nr IS NOT NULL)),
								IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20),
									'1',
									''
								),
								IF(SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discountid)),20)='SC',
									IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20),
										'1',
										'0.20'
									),
									IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20) IS NULL,
										'',
										IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_type)),20)=2,
											SUBSTRING(MAX(CONCAT(scp.grant_dte,scp.discount)),20),
											SUBSTRING(MAX(CONCAT(se.grant_dte,se.discount)),20)
										)
									)
								)
							) AS discount,enc.mgh_setdte,
							(SELECT memcategory_desc 
							  FROM seg_memcategory
							WHERE memcategory_id = sem.`memcategory_id`) AS InsuranceCat

						FROM $this->tb_enc AS enc
						     INNER JOIN $this->tb_person AS cp ON cp.pid=enc.pid
							  LEFT JOIN $this->tb_dept AS dept ON dept.nr=enc.current_dept_nr
							  LEFT JOIN $this->tb_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
							  LEFT JOIN $this->tb_municity AS sm ON sm.mun_nr=cp.mun_nr
							  LEFT JOIN $this->tb_provinces AS sp ON sp.prov_nr=sm.prov_nr
							  LEFT JOIN $this->tb_regions AS sr ON  sr.region_nr=sp.region_nr
							  LEFT JOIN $this->tb_country AS sc ON  sc.country_code=cp.citizenship
							  LEFT JOIN $this->tb_religion AS sreli ON sreli.religion_nr = cp.religion
							  LEFT JOIN $this->tb_occupation AS so ON so.occupation_nr = cp.occupation
							  LEFT JOIN $this->tb_ward AS w ON enc.current_ward_nr=w.nr
							  LEFT JOIN seg_encounter_insurance AS enc_ins ON enc_ins.encounter_nr=enc.encounter_nr
								LEFT JOIN care_encounter_diagnosis AS enc_diag ON enc_diag.encounter_nr=enc.encounter_nr
							  LEFT JOIN care_insurance_firm AS ins ON ins.hcare_id=enc_ins.hcare_id

								/*LEFT JOIN seg_charity_grants_pid AS ss ON ss.pid=enc.pid
								LEFT JOIN seg_discount AS sd ON sd.discountid=ss.discountid*/

								LEFT JOIN seg_charity_grants_pid AS scp ON scp.pid=cp.pid AND scp.status='valid' AND scp.discountid NOT IN ('LINGAP')
								LEFT JOIN seg_charity_grants AS se ON se.encounter_nr=enc.encounter_nr AND se.status='valid' AND se.discountid NOT IN ('LINGAP')

							  LEFT JOIN seg_social_patient AS ms ON ms.pid=enc.pid
			 				  LEFT JOIN seg_encounter_memcategory AS sem
    			               ON sem.`encounter_nr` = enc.`encounter_nr`
    			               LEFT JOIN seg_encounter_condition AS sec
    				           ON sec.`encounter_nr` = enc.`encounter_nr`
								LEFT JOIN care_personell AS ps ON cp.pid=ps.pid
											AND ((date_exit NOT IN (DATE(NOW())) AND date_exit > DATE(NOW())) OR date_exit='0000-00-00' OR date_exit IS NULL)
											AND ((contract_end NOT IN (DATE(NOW())) AND contract_end > DATE(NOW()))
											OR contract_end='0000-00-00' OR contract_end IS NULL)

								LEFT JOIN seg_dependents AS dep ON dep.dependent_pid=cp.pid AND dep.status='member'

						".$enc_sql."
						ORDER BY encounter_date DESC LIMIT 1";

#							enc.referrer_dr AS er_opd_admitting_physician_nr,
#							enc.referrer_diagnosis AS admitting_diagnosis,
#		WHERE enc_2.encounter_nr='$encounter_nr' AND cpl_2.nr = enc_2.referrer_dr AND cp_2.pid=cpl_2.pid
#$this->tb_personell AS cpl,
#							fn_get_age(enc.encounter_date,cp.date_birth) AS age,
#							cp.date_birth,
#							AND sc.country_code=cp.citizenship AND sreli.religion_nr = cp.religion
#							AND so.occupation_nr = cp.occupation " ;
#							 cp.citizenship, cp.religion, cp.occupation,
#		echo "getEncounterInfo : this->sql = '".$this->sql."' <br><br> \n";
		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}/* end of function getEncounterInfo */

/*Added by Genz*/
function getErEncounterDate($encounter_nr=0){
	global $db;

	$this->sql = "SELECT * FROM care_encounter WHERE encounter_nr = '$encounter_nr'";

	if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
}


#------------- added 03-08-07---------------

function getEncounterDept($encounter_nr=0){
	    global $db;
		/*
		$this->sql="SELECT enc.encounter_nr, enc.pid, enc.current_dept_nr, d.nr, d.name_formal
						FROM $this->tb_enc AS enc, $this->tb_dept AS d
						WHERE enc.current_dept_nr = d.nr AND encounter_nr = '$encounter_nr'";
		*/
		$this->sql="SELECT enc.encounter_nr,enc.encounter_type, enc.pid, enc.current_dept_nr, d.nr, d.name_formal
                        FROM $this->tb_enc AS enc, $this->tb_dept AS d
                        WHERE enc.current_dept_nr = d.nr AND encounter_nr = '$encounter_nr'";

		if($this->result=$db->Execute($this->sql)){
		    if($this->result->RecordCount()) {
			    $this->row=$this->result->FetchRow();
				return $this->row;
			} else return FALSE;
		}else {
		    return FALSE;
		}
	}

function AllConditionClassesObject(){
	   global $db;

		#if (($enc_type==1)||($enc_type==2)){
		$this->sql="SELECT cond_code,cond_desc,area_used FROM $this->tb_condition
		            WHERE area_used = 'E'";
		#}elseif (($enc_type==3)||($enc_type==4)){
		#	$this->sql="SELECT cond_code,cond_desc,area_used FROM $this->tb_condition
		#               where area_used = 'A'";
		#}
		#echo "sql = ".$this->sql;
		if($this->res['acc']=$db->Execute($this->sql)) {
		    if($this->res['acc']->RecordCount()) {
			    return $this->res['acc'];
		    } else { return FALSE;}
		} else { return FALSE;}
}

function AllResultsClassesObject(){
	    global $db;
	    #if (($enc_type==1)||($enc_type==2)){
		$this->sql="SELECT result_code,result_desc,area_used FROM $this->tb_result
						WHERE area_used = 'E'";
		 #}elseif (($enc_type==3)||($enc_type==4)){
		#	$this->sql="SELECT result_code,result_desc,area_used FROM $this->tb_result
					#	   where area_used = 'A'";
		#}
		#echo "sql = ".$this->sql;
		if($this->res['arc']=$db->Execute($this->sql)) {
		    if($this->res['arc']->RecordCount()) {
			    return $this->res['arc'];
		    } else { return FALSE;}
		} else { return FALSE;}
}

function AllDispositionClassesObject(){
	   global $db;

		#if (($enc_type==1)||($enc_type==2)){
		$this->sql="SELECT disp_code,disp_desc,area_used FROM $this->tb_disposition
						WHERE area_used = 'E' ";
		#}elseif (($enc_type==3)||($enc_type==4)){
		#	$this->sql="SELECT disp_code,disp_desc,area_used FROM $this->tb_disposition
			#			   where area_used = 'A' ";
	#	}
		#echo "sql = ".$this->sql;
		if($this->res['adc']=$db->Execute($this->sql)) {
		    if($this->res['adc']->RecordCount()) {
			    return $this->res['adc'];
		    } else { return FALSE;}
		} else { return FALSE;}
}

	/**
	* Points  the core to the seg_encounter_condition table and fields
	* @access public
	*/
	function useCondition(){
		$this->coretable=$this->tb_enc_condition;
		$this->ref_array=$this->fld_condition;

	}

	/**
	* Points  the core to the seg_encounter_disposition table and fields
	* @access public
	*/
	function useDisposition(){
		$this->coretable=$this->tb_enc_disposition;
		$this->ref_array=$this->fld_disposition;
	}

	/**
	* Added by Genz
	* Points  the core to the seg_encounter_disposition table and fields
	* @access public
	*/
	function useDispositionRefer(){
		$this->coretable=$this->tb_enc_disposition_refer;
		$this->ref_array=$this->fld_disposition;
	}

	/**
	* Points  the core to the seg_encounter_result table and fields
	* @access public
	*/
	function useResult(){
		$this->coretable=$this->tb_enc_result;
		$this->ref_array=$this->fld_result;

	}

	/**
	 * Use care_encounter_diagnosis table ang fields
	 * @access public
	 */
	function _useCareEncDiagnosis(){
		$this->coretabel = $this->tb_care_enc_diagnosis;
		$this->ref_array=$this->fld_care_enc_diagnosis;
	}

  /**
	* Saves a condition confirmation of an encounter.
	* @access public
	* @param array encounter condition data. By reference.
	* @return boolean
	*
	*/
	function saveEncounterCondition(&$data){
		if(!is_array($data)) return FALSE;
		$this->useCondition();
		$this->buffer_array=NULL;
		return $this->insertDataFromInternalArray();
	}

	function updateEncounterCondition($item_nr='',$code){
		if(empty($item_nr)) return FALSE;
		$this->where=" encounter_nr='$item_nr' AND cond_code='$code'";
		$this->useCondition();
		$this->buffer_array=NULL;
		return $this->updateDataFromInternalArray($item_nr);
	}




	/**
	* Saves a condition confirmation of an encounter.
	* @access public
	* @param array encounter disposition data. By reference.
	* @return boolean
	*/
	function saveEncounterDisposition(&$data){
		if(!is_array($data)) return FALSE;
		$this->useDisposition();
		$this->buffer_array=NULL;
		return $this->insertDataFromInternalArray();
	}

	/**
	* Added by Genz
	* Saves a condition confirmation of an encounter.
	* @access public
	* @param array encounter disposition data. By reference.
	* @return boolean
	*/
	function saveEncounterDispositionRefer(&$data){
		if(!is_array($data)) return FALSE;
		$this->useDispositionRefer();
		$this->buffer_array=NULL;
		return $this->insertDataFromInternalArray();
	}

	function updateEncounterDisposition($item_nr='',$code){
		if(empty($item_nr)) return FALSE;
		$this->where=" encounter_nr='$item_nr' AND disp_code='$code'";
		$this->useDisposition();
		$this->buffer_array=NULL;
		return $this->updateDataFromInternalArray($item_nr);
	}

	/*Added by Genz*/
	function updateEncounterDispositionRefer($item_nr='',$code){
		if(empty($item_nr)) return FALSE;
		$this->where=" encounter_nr='$item_nr' AND disp_code='$code'";
		$this->useDispositionRefer();
		$this->buffer_array=NULL;
		return $this->updateDataFromInternalArray($item_nr);
	}
	/**
	* Saves a condition confirmation of an encounter.
	* @access public
	* @param array encounter result data. By reference.
	* @return boolean
	*/
	function saveEncounterResults(&$data){
		if(!is_array($data)) return FALSE;
		$this->useResult();
		$this->buffer_array=NULL;
		return $this->insertDataFromInternalArray();
	}

	function updateEncounterResults($item_nr='', $code){
		if(empty($item_nr)) return FALSE;
		$this->where=" encounter_nr='$item_nr' AND result_code='$code'";
		$this->useResult();
		$this->buffer_array=NULL;
		return $this->updateDataFromInternalArray($item_nr);
	}


	function getEncounterConditionInfo($cond_code){
	    global $db;
		$this->sql="SELECT cond_code,cond_desc,area_used FROM $this->tb_condition WHERE cond_code='$cond_code'";
		if($this->result=$db->Execute($this->sql)){
		    if($this->result->RecordCount()) {
			    $this->row=$this->result->FetchRow();
				return $this->row;
			} else return FALSE;
		}else {
		    return FALSE;
		}
	}

	function getEncounterDispositionInfo($disp_code){
	    global $db;
		$this->sql="SELECT disp_code,disp_desc,area_used FROM $this->tb_disposition WHERE disp_code='$disp_code'";
		
		if($this->result=$db->Execute($this->sql)){
		    if($this->result->RecordCount()) {
			    $this->row=$this->result->FetchRow();
				return $this->row;
			} else return FALSE;
		}else {
		    return FALSE;
		}
	}

	function getEncounterResultInfo($result_code){
	    global $db;
		$this->sql="SELECT result_code,result_desc,area_used FROM $this->tb_result WHERE result_code='$result_code'";
		if($this->result=$db->Execute($this->sql)){
		    if($this->result->RecordCount()) {
			    $this->row=$this->result->FetchRow();
				return $this->row;
			} else return FALSE;
		}else {
		    return FALSE;
		}
	}

	function getPatientEncounterInsurance($encounter_nr=0){
	global $db;

		#$this->sql ="SELECT * FROM $this->tb_enc WHERE encounter_nr='$encounter_nr'" ;
		$this->sql ="SELECT i.hcare_id ,e.*
					 FROM $this->tb_enc AS e
					 LEFT JOIN seg_encounter_insurance as i ON i.encounter_nr=e.encounter_nr
					 WHERE e.encounter_nr='$encounter_nr'" ;

		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}

	}

    //Added by Francis 03-26-2014
    //Get transmittal info
    function getTransInfo($encounter_nr=0){
        global $db;

        $this->sql ="SELECT
                      st.`transmit_no`,
                      st.`patient_claim`
                    FROM
                      seg_transmittal_details AS st
                    WHERE st.`encounter_nr`=".$db->qstr($encounter_nr);

        if ($this->result=$db->Execute($this->sql)){
            if ($this->result->RecordCount())
                return $this->result->FetchRow();
            else
                return FALSE;
        }else{
            return FALSE;
        }
    }


	function getPatientEncounter($encounter_nr=0){
	global $db;

		#$this->sql ="SELECT * FROM $this->tb_enc WHERE encounter_nr='$encounter_nr'" ;
		// Modified by LST -- 03.30.2009 ----------------------
        $this->sql ="SELECT ce.*, cp.sex
                        FROM $this->tb_enc as ce inner join $this->tb_person as cp
                        on ce.pid = cp.pid
                        WHERE encounter_nr='$encounter_nr'" ;

		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
		/*
		$rs =$db->Execute("SELECT encounter_class_nr, encounter_type
								 FROM $this->tb_enc WHERE encounter_nr='$encounter_nr'");

		$field = $rs->FetchRow();
		return $field;

		*/
	}

	function getPatientEncounterCond($encounter_nr=0){
	global $db;

		$this->sql ="SELECT ced.encounter_nr, ced.cond_code, c.cond_desc, c.area_used
						 FROM $this->tb_enc_condition as ced, $this->tb_condition as c
                   WHERE ced.cond_code=c.cond_code
						 AND ced.encounter_nr='$encounter_nr'
						 ORDER BY ced.create_time DESC LIMIT 1";
		#echo "<br>con = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
		/*
		$rs =$db->Execute("SELECT * FROM $this->tb_enc_condition WHERE encounter_nr='$encounter_nr' ORDER BY modify_time ASC LIMIT 1");

		$field = $rs->FetchRow();
		return $field;

		*/
	}

	function getPatientEncounterDisp($encounter_nr=0){
	global $db;

		$this->sql ="SELECT sed.encounter_nr, sed.disp_code, d.disp_desc, d.area_used, sed.hosp_name, sed.hosp_add, sed.hosp_when, sed.referral_reason_id
						 FROM $this->tb_enc_disposition as sed, $this->tb_disposition_refer as d
                   WHERE sed.disp_code=d.disp_code
						 AND sed.encounter_nr='$encounter_nr'
						 ORDER BY sed.create_time DESC LIMIT 1";
		#echo "<br>disp = ".$this->sql;

		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	/*Added by Genz*/
	function getPatientEncounterDispRefer($encounter_nr=0){
	global $db;

		$this->sql ="SELECT sed.encounter_nr, sed.disp_code, d.disp_desc, d.area_used, sed.hosp_name, sed.hosp_add, sed.referral_reason_id
						 FROM $this->tb_enc_disposition_refer as sed, $this->tb_disposition as d
                   WHERE sed.disp_code=d.disp_code
						 AND sed.encounter_nr='$encounter_nr'
						 ORDER BY sed.create_time DESC LIMIT 1";
		#echo "<br>disp = ".$this->sql;

		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getPatientEncounterRes($encounter_nr=0){
	global $db;

		$this->sql ="SELECT red.encounter_nr, red.result_code, r.result_desc, r.area_used
						 FROM $this->tb_enc_result as red, $this->tb_result as r
                   WHERE red.result_code=r.result_code
						 AND red.encounter_nr='$encounter_nr'
						 ORDER BY red.create_time DESC LIMIT 1";
		#echo "<br>res = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}


#-------------------------------------------
	/**
	* Loads the encounter data including some data from the registration into an internal buffer array <var>$encounter</var>.
	*
	* This method returns only TRUE (data loaded) or FALSE (data not loaded).
	* The load success status can also be tested later by using the internal <var>$is_loaded</var> flag.
	*
	* The individual data is to be fetched via the appropriate methods.
	*
	* - all keys as set in the <var>$tabfields</var> array
	* - <b>pid</b> = the PID number of the patient
	* - <b>title</b> = the patient title
	* - <b>name_last</b> = last or family name
	* - <b>name_first</b> = first or given name
	* - <b>date_birth</b> = date of birth in yyyy-mm-format
	* - <b>sex</b> = sex
	* - <b>addr_str</b> = street name
	* - <b>addr_str_nr</b> = street number (alphanumeric)
	* - <b>addr_zip</b> = zip code
	* - <b>blood_group</b> = blood group (A, B, AB, O)
	* - <b>photo_filename</b> = filename of the stored ID photo
	* - <b>citytown_name</b> = name of the city or town
	* - <b>death_date</b> = date of death (in case deceased)
	*
	*
	* The content of the internal buffer <var>$encounter</var> array can also be fetched by the method  <var>getLoadedEncounterData()</var>
	* @param int Encounter number
	* @return boolean
	*/
	function loadEncounterData($enc_nr=''){
	    global $db;
		if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;
		#edited by VAN 05-13-08
		$this->sql="SELECT e.*, p.senior_ID, p.pid, p.title,p.name_last, p.name_first, p.date_birth, p.sex,
									p.addr_str,p.addr_str_nr,p.addr_zip, p.blood_group,
									p.photo_filename, t.name AS citytown_name,p.death_date, p.death_encounter_nr
									, p.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name
							FROM $this->tb_enc AS e
									 INNER JOIN $this->tb_person AS p ON e.pid=p.pid
									 LEFT JOIN $this->tb_citytown AS t ON p.addr_citytown_nr=t.nr
									 LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
									 LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
									 LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
									 LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
									 LEFT JOIN seg_country AS sc ON sc.country_code=p.citizenship
							WHERE e.encounter_nr='".$this->enc_nr."'";

		#echo $this->sql;
		if($this->res['lend']=$db->Execute($this->sql)) {
		    if($this->record_count=$this->res['lend']->RecordCount()) {
				$this->rec_count=$this->record_count;
			    $this->encounter=$this->res['lend']->FetchRow();
				//$this->result=NULL;
			    $this->is_loaded=true;
#echo "class_encounter.php : this->sql : <br> \n"; print_r($this->sql); echo "<br> \n";
#echo "class_encounter.php : this->encounter : <br> \n"; print_r($this->encounter); echo "<br> \n";
#echo "class_encounter.php : this->encounter['address'] ='".$this->encounter['address']."' <br> \n";
#exit();
				return true;
		    } else { return FALSE;}
		} else { return FALSE;}
	}

	#--------added by VAN 07-02-08
	function loadEncounterDataByPid($pid=''){
	    global $db;
		#if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;
		$this->sql="SELECT p.date_reg ,p.senior_ID, p.pid, p.title,p.name_last, p.name_first, p.date_birth, p.sex,
									p.addr_str,p.addr_str_nr,p.addr_zip, p.blood_group,
									p.photo_filename, t.name AS citytown_name,p.death_date, p.death_encounter_nr
									, p.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name
							FROM $this->tb_person AS p
									 LEFT JOIN $this->tb_enc AS e  ON e.pid=p.pid
									 LEFT JOIN $this->tb_citytown AS t ON p.addr_citytown_nr=t.nr
									 LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
									 LEFT JOIN seg_municity AS sm ON sm.mun_nr=sb.mun_nr
									 LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
									 LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
									 LEFT JOIN seg_country AS sc ON sc.country_code=p.citizenship
							WHERE (p.pid='$pid')";
							#WHERE (p.pid='$pid' || p.pid=$pid)
		#echo $this->sql;
		if($this->res['lend']=$db->Execute($this->sql)) {
		    if($this->record_count=$this->res['lend']->RecordCount()) {
				$this->rec_count=$this->record_count;
			    $this->encounter=$this->res['lend']->FetchRow();
				//$this->result=NULL;
			    $this->is_loaded=true;
				return true;
		    } else { return FALSE;}
		} else { return FALSE;}
	}

	function loadEncounterDataByPidWalkin($pid=''){
			global $db;
		#if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;
		$this->sql="SELECT create_time AS date_reg ,'' AS senior_ID, '' AS pid, '' AS title,p.name_last, p.name_first, p.date_birth, p.sex,
								'' AS addr_str,'' AS addr_str_nr,'' AS addr_zip, '' AS blood_group,
								'' AS photo_filename, '' AS citytown_name,'' AS death_date, p.address AS street_name,
								'' AS brgy_name, '' AS zipcode, '' AS mun_name, '' AS prov_name, '' AS region_name
								FROM seg_walkin AS p
							WHERE (p.pid='$pid')";

		#echo $this->sql;
		if($this->res['lend']=$db->Execute($this->sql)) {
			if($this->record_count=$this->res['lend']->RecordCount()) {
				$this->rec_count=$this->record_count;
				$this->encounter=$this->res['lend']->FetchRow();
				//$this->result=NULL;
				$this->is_loaded=true;
				return true;
			} else { return FALSE;}
		} else { return FALSE;}
	}
	#--------------------------

	#added by VAN 03-04-08
	/*
	function loadEncounterData2($pid=''){
	    global $db;
		if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;

		$this->sql="SELECT p.pid, p.title,p.name_last, p.name_first, p.date_birth, p.sex,
						p.addr_str,p.addr_str_nr,p.addr_zip, p.blood_group,
						p.photo_filename, t.name AS citytown_name,p.death_date,
						p.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name
						FROM $this->tb_person AS p
						LEFT JOIN $this->tb_enc AS e ON p.pid=e.pid
						LEFT JOIN $this->tb_citytown AS t ON p.addr_citytown_nr=t.nr
						LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
						LEFT JOIN seg_municity AS sm ON sm.mun_nr=sb.mun_nr
						LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
						LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
						LEFT JOIN seg_country AS sc ON sc.country_code=p.citizenship
						WHERE p.pid = '$pid'";

		#echo $sql;
		if($this->res['lend']=$db->Execute($this->sql)) {
		    if($this->record_count=$this->res['lend']->RecordCount()) {
				$this->rec_count=$this->record_count;
			    $this->encounter=$this->res['lend']->FetchRow();
				//$this->result=NULL;
			    $this->is_loaded=true;
				return true;
		    } else { return FALSE;}
		} else { return FALSE;}
	}
	*/
	/**
	* Returns last or family name.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function LastName($enr=0){
		if($this->is_loaded) {
			return $this->encounter['name_last'];
		}else{
			if($enr) return $this->getValue('name_last',$enr,TRUE);
				else return FALSE;
		}
	}
	/**
	* Returns first or given name.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function FirstName($enr=0){
		if($this->is_loaded) {
			return $this->encounter['name_first'];
		}else{
			if($enr) return $this->getValue('name_first',$enr,TRUE);
				else return FALSE;
		}
	}
	/**
	* Returns date of birth in yyyy-mm-format.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function BirthDate($enr=0){
		if($this->is_loaded) {
			return $this->encounter['date_birth'];
		}else{
			if($enr) return $this->getValue('date_birth',$enr,TRUE);
				else return FALSE;
		}
	}
	/**
	* Returns PID number.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed integer or boolean
	*/
	function PID($enr=0){
		if($this->is_loaded) {
			return $this->encounter['pid'];
		}else{
			if($enr) return $this->getValue('pid',$enr,TRUE);
				else return FALSE;
		}
	}
	/**
	* Returns blood group
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed integer or boolean
	*/
	function BloodGroup($enr=0){
		if($this->is_loaded) {
			return $this->encounter['blood_group'];
		}else{
			if($enr) return $this->getValue('blood_group',$enr,TRUE);
				else return FALSE;
		}
	}
	/**
	* Returns date of admission.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function EncounterDate($enr=0){
		if($this->is_loaded) {
			return $this->encounter['encounter_date'];
		}else{
			if($enr) return $this->getValue('encounter_date',$enr);
				else return FALSE;
		}
	}
    
    function AdmissionDate($enr=0){
        if($this->is_loaded) {
            return $this->encounter['encounter_date'];
        }else{
            if($enr) return $this->getValue('encounter_date',$enr);
                else return FALSE;
        }
    }
    
    function GetMGH($enr=0){
        if($this->is_loaded) {
            return $this->encounter['mgh_setdte'];
        }else{
            if($enr) return $this->getValue('mgh_setdte',$enr);
                else return FALSE;
        }
    }
    
    
	/**
	* Returns encounter or admission class.
	*
	* For example:  1 = inpatient , 2 = outpatient.
	*
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed integer or boolean
	*/
	function EncounterClass($enr=0){
		if($this->is_loaded) {
			return $this->encounter['encounter_class_nr'];
		}else{
			if($enr) return $this->getValue('encounter_class_nr',$enr);
				else return FALSE;
		}
	}

	/**
	* Returns official receipt number.
	*
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed integer or boolean
	*	burn added : May 23, 2007
	*/
	function ORNumber($enr=0){
		if($this->is_loaded) {
			return $this->encounter['official_receipt_nr'];
		}else{
			if($enr) return $this->getValue('official_receipt_nr',$enr);
				else return FALSE;
		}
	}

	/**
	* Returns financial class.
	*
	* For example: 1 = private , 2 = common , 3 = self pay.
	*
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed integer or boolean
	*/
	function FinancialClass($enr=0){
		if($this->is_loaded) {
			return $this->encounter['financial_class'];
		}else{
			if($enr) return $this->getValue('financial_class',$enr);
				else return FALSE;
		}
	}
	/**
	* Alias of <var>FinancialClass()</var>
	*/
	function BillingClass($enr=0){
		return $this->FinancialClass($enr);
	}
	/**
	* Returns referer's diagnosis text.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function RefererDiagnosis($enr=0){
		if($this->is_loaded) {
			return $this->encounter['referrer_diagnosis'];
		}else{
			if($enr) return $this->getValue('referrer_diagnosis',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns referer's recommended therapy text.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function RefererRecomTherapy($enr=0){
		if($this->is_loaded) {
			return $this->encounter['referrer_recom_therapy'];
		}else{
			if($enr) return $this->getValue('referrer_recom_therapy',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns referer's extra notes text.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function RefererNotes($enr=0){
		if($this->is_loaded) {
			return $this->encounter['referrer_notes'];
		}else{
			if($enr) return $this->getValue('referrer_notes',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns referer's name.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function Referer($enr=0){
		if($this->is_loaded) {
			return $this->encounter['referrer_dr'];
		}else{
			if($enr) return $this->getValue('referrer_dr',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns refererring department.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function RefererDept($enr=0){
		if($this->is_loaded) {
			return $this->encounter['referrer_dept'];
		}else{
			if($enr) return $this->getValue('referrer_dept',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns referring institution.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function RefererInstitution($enr=0){
		if($this->is_loaded) {
			return $this->encounter['referrer_institution'];
		}else{
			if($enr) return $this->getValue('referrer_institution',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns insurance number used in the encounter.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function InsuranceNr($enr=0){
		if($this->is_loaded) {
			return $this->encounter['insurance_nr'];
		}else{
			if($enr) return $this->getValue('insurance_nr',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns insurance company's id used in the encounter.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function InsuranceFirmID($enr=0){
		if($this->is_loaded) {
			return $this->encounter['insurance_firm_id'];
		}else{
			if($enr) return $this->getValue('insurance_firm_id',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns current ward number.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function CurrentWardNr($enr=0){
		if($this->is_loaded) {
			return $this->encounter['current_ward_nr'];
		}else{
			if($enr) return $this->getValue('current_ward_nr',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns current room number.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function CurrentRoomNr($enr=0){
		if($this->is_loaded) {
			return $this->encounter['current_room_nr'];
		}else{
			if($enr) return $this->getValue('current_room_nr',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns current department number.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function CurrentDeptNr($enr=0){
		if($this->is_loaded) {
			return $this->encounter['current_dept_nr'];
		}else{
			if($enr) return $this->getValue('current_dept_nr',$enr);
				else return FALSE;
		}
	}

#--------------added 03-05-07-----------
	/**
	* Returns attending doctor number.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function Consulting_Dr($enr=0){
		if($this->is_loaded) {
			return $this->encounter['consulting_dr'];
		}else{
			if($enr) return $this->getValue('consulting_dr',$enr);
				else return FALSE;
		}
	}

#---------------------------------------------
	/**
	* Returns current firm number.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function CurrentFirmNr($enr=0){
		if($this->is_loaded) {
			return $this->encounter['current_firm_nr'];
		}else{
			if($enr) return $this->getValue('current_firm_nr',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns current attending physician number.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function CurrentAttDrNr($enr=0){
		if($this->is_loaded) {
			return $this->encounter['current_att_dr_nr'];
		}else{
			if($enr) return $this->getValue('current_att_dr_nr',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns status flag if patient is finally admitted in ward.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return boolean
	*/
	function In_Ward($enr=0){
		if($this->is_loaded) {
			return $this->encounter['in_ward'];
		}else{
			if($enr) return $this->getValue('in_ward',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns status flag if patient is finally admitted in department.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return boolean
	*/
	function In_Dept($enr=0){
		if($this->is_loaded) {
			return $this->encounter['in_dept'];
		}else{
			if($enr) return $this->getValue('in_dept',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns status flag if patient is finally discharged.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return boolean
	*/
	function Is_Discharged($enr=0){
		if($this->is_loaded) {
			return $this->encounter['is_discharged'];
		}else{
			if($enr){
				 return $this->getValue('is_discharged',$enr);
				}else return FALSE;
		}
	}
	/**
	* Returns encounter status.
	*
	* Types of encounter status:
	* - <b>disallow_cancel</b>
	* - <b>cancelled</b>
	* - <b>valid</b>
	* - <var>empty char</var>
	*
	*
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function EncounterStatus($enr=0){
		if($this->is_loaded) {
			return $this->encounter['encounter_status'];
		}else{
			if($enr) return $this->getValue('encounter_status',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns encounter type. <b>Currently reserved.</b>
	*
	* Encounter types:
	* - <b>emergency</b>
	* - <b>normal</b>
	* - <b>walk-in</b>
	* - <b>home visit</b>
	*
	*
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function EncounterType($enr=0){
		if($this->is_loaded) {
			return $this->encounter['encounter_type'];
		}else{
			if($enr) return $this->getValue('encounter_type',$enr);
				else return FALSE;
		}
	}

	/**
	* Returns consulting physician's name.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function ConsultingDr($enr=0){
		if($this->is_loaded) {
			return $this->encounter['consulting_dr'];
		}else{
			if($enr) return $this->getValue('consulting_dr',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns follow-up date in yyyy-mm-dd format.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function FollowUpDate($enr=0){
		if($this->is_loaded) {
			return $this->encounter['followup_date'];
		}else{
			if($enr) return $this->getValue('followup_date',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns the name of physician or service responsible for follow-up.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function FollowUpResponsibility($enr=0){
		if($this->is_loaded) {
			return $this->encounter['followup_responsibility'];
		}else{
			if($enr) return $this->getValue('followup_responsibility',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns post encounter notes. Short notes after discharge, not to be used for discharge summary report.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function PostEncounterNotes($enr=0){
		if($this->is_loaded) {
			return $this->encounter['post_encounter_notes'];
		}else{
			if($enr) return $this->getValue('post_encounter_notes',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns the record entry's status. This status is technical and has nothing to do with the encounter status.
	*
	* Status types:
	* - <var>empty char</var>
	* - <b>normal</b>
	* - <b>inactive</b>
	* - <b>void</b>
	* - <b>hidden</b>
	* - <b>deleted</b>
	*
	*
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function RecordStatus($enr=0){
		if($this->is_loaded) {
			return $this->encounter['status'];
		}else{
			if($enr) return $this->getValue('status',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns record entry's history. This is the techical history of the record entry, not of the admission.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function RecordHistory($enr=0){
		if($this->is_loaded) {
			return $this->encounter['history'];
		}else{
			if($enr) return $this->getValue('history',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns record's modifier id or name. Technical.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function RecordModifierID($enr=0){
		if($this->is_loaded) {
			return $this->encounter['modify_id'];
		}else{
			if($enr) return $this->getValue('modify_id',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns record's creator id or name. Technical.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function RecordCreatorID($enr=0){
		if($this->is_loaded) {
			return $this->encounter['create_id'];
		}else{
			if($enr) return $this->getValue('create_id',$enr);
				else return FALSE;
		}
	}
	/**
	* Returns filename of the person's picture id.
	* Use only after the encounter data was successfully loaded by the <var>loadEncounterData()</var> method.
	* @return mixed string or boolean
	*/
	function PhotoFilename($enr=0){
		if($this->is_loaded) {
			return $this->encounter['photo_filename'];
		}else{
			if($enr) return $this->getValue('photo_filename',$enr,TRUE);
				else return FALSE;
		}
	}
	/**
	* Updates the encounter record with data from the internal buffer array.
	* @access public
	* @param int Encounter number
	* returns boolean
	*/
    function updateEncounterFromInternalArray($item_nr='') {
		if(empty($item_nr)) return FALSE;
		$this->where=" encounter_nr='$item_nr'";
		#echo "<br>updateEncounterFromInternalArray = '".$item_nr."'";
		return $this->updateDataFromInternalArray($item_nr);
	}
	/**
	* Checks if an encounter number is currently admitted (both inpatient & outpatient).
	* @access public
	* @param int Encounter number
	* @param string Type of param <var>$nr</var> (<b>_ENC</b> = encounter nr, <b>_PID</b> = pid nr) , defaults to _ENC = encounter nr.
	* @return mixed integer or boolean
	*/
	function isCurrentlyAdmitted($nr,$type='_ENC'){
	    global $db;
		if(!$nr) return FALSE;
		if($type=='_ENC') $cond='encounter_nr';
			elseif($type=='_PID') $cond='pid';
			 	else return FALSE;
		$this->sql="SELECT encounter_nr FROM $this->tb_enc
						WHERE $cond='$nr' AND (encounter_type='3' OR encounter_type='4') AND encounter_status <> 'cancelled' AND is_discharged=0 AND status NOT IN ($this->dead_stat)";
#					print_r($this->sql);
		if($buf=$db->Execute($this->sql)){
		    if($buf->RecordCount()) {
				$buf2=$buf->FetchRow();
				return $buf2['encounter_nr'];
			}else{return FALSE;}
		}else{return FALSE;}
	}
	/**
	* Checks if the person's  is currently admitted based on his PID number.
	* @access public
	* @param int PID number
	* @return mixed integer or boolean
	*/
	function isPIDCurrentlyAdmitted($nr){
	    return $this->isCurrentlyAdmitted($nr,'_PID');
	}
	/**
	* Checks if a given encounter number is currently admitted.
	* @access public
	* @param int Encounter number
	* @return mixed integer or boolean
	*/
	function isENCCurrentlyAdmitted($nr){
	    return $this->isCurrentlyAdmitted($nr,'_ENC');
	}
	/**
	* Adds a "View" note to the record's history data.
	* @access public
	* @param string Name of person viewing the data
	* @param int Encounter number
	* @return boolean
	*/
	function setHistorySeen($encoder='',$enc_nr=''){
	    global $db, $dbtype;
		if(empty($encoder)) return FALSE;
		if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;
		/*
		if($dbtype=='mysql')
			$this->sql="UPDATE $this->tb_enc SET history= CONCAT(history,'\nView ".date('Y-m-d H:i:s')." = $encoder') WHERE encounter_nr=$this->enc_nr";
		else
			$this->sql="UPDATE $this->tb_enc SET history= (history || '\nView ".date('Y-m-d H:i:s')." = $encoder') WHERE encounter_nr=$this->enc_nr";
		*/
		$this->sql="UPDATE $this->tb_enc SET history= ".$this->ConcatHistory("\nView ".date('Y-m-d H:i:s')." = $encoder")." WHERE encounter_nr='".$this->enc_nr."'";

        return $this->Transact($this->sql);

	}
	/**
	* Gets the encounter class' information based on its class_nr key.
	*
	* The returned array contains data with following index keys:
	* - <b>class_id</b> = the class id (alphanumeric)
	* - <b>name</b> = the class name
	* - <b>LD_var</b> = the variable's name for the language dependent version of the class name
	*
	*
	* @access public
	* @param int Encounter number
	* @return mixed array or boolean
	*/
	function getEncounterClassInfo($class_nr){
	    global $db;
		$this->sql="SELECT class_id,name, LD_var AS \"LD_var\" FROM $this->tb_ec WHERE class_nr='$class_nr'";
		if($this->result=$db->Execute($this->sql)){
		    if($this->result->RecordCount()) {
			    $this->row=$this->result->FetchRow();
				return $this->row;
			} else return FALSE;
		}else {
		    return FALSE;
		}
	}
	/**
	* Gets the insurance class' information based on its class_nr key.
	*
	* The returned array contains data with following index keys:
	* - <b>class_id</b> = the class id (alphanumeric)
	* - <b>name</b> = the class name
	* - <b>LD_var</b> = the variable's name for the language dependent version of the class name
	*
	*
	* @access public
	* @param int Encounter number
	* @return mixed array or boolean
	*/
    function getInsuranceClassInfo($class_nr){
	    global $db;
		$this->sql="SELECT class_id,name, LD_var AS \"LD_var\" FROM $this->tb_ic WHERE class_nr='$class_nr'";
		if($this->result=$db->Execute($this->sql)){
		    if($this->result->RecordCount()) {
			    $this->row=$this->result->FetchRow();
				return $this->row;
			} else return FALSE;
		}else {
		    return FALSE;
		}
	}
	/**
	* Private search function, usually called by another method.
	*
	* The resulting count can be fetched with the <var>LastRecordCount()</var> method.
	*
	* The returned adodb object contains rows of arrays.
	* Each array contains "basic" admission data with following index keys:
	* - <b>encounter_nr</b> = encounter number
	* - <b>encounter_class_nr</b> = encounter class number: 1 = inpatient, 2 = outpatient
	* - <b>pid</b> = the patient's PID number
	* - <b>name_last</b> = last or family name
	* - <b>name_first</b> = first or given name
	* - <b>date_birth</b> = date of birth in yyyy-mm-dd format
	* - <b>addr_zip</b> = zip code
	* - <b>blood_group</b> = patient's blood group
	*
	*
	* @param string Search keyword
	* @param int Encounter class number. default = 0
	* @param string Optional addtion to WHERE clause like e.g. for sorting
	* @param boolean  Flag whether the select query is limited or not, default FALSE = unlimited
	* @param int Maximum number or rows returned in case of limited select, default = 30 rows
	* @param int Start index offset in case of limited select, default 0 = start
	* @return mixed adodb record object or boolean
	*/
	function _searchAdmissionBasicInfo($key,$enc_class=0,$add_opt='',$limit=FALSE,$len=30,$so=0){
		global $db,$sql_LIKE;


		//if(empty($key)) return FALSE;
		$this->sql="SELECT e.encounter_nr, e.encounter_class_nr, e.official_receipt_nr, p.pid, p.name_last, p.name_first, p.date_birth, p.addr_zip, p.sex,p.blood_group
				FROM $this->tb_enc AS e LEFT JOIN $this->tb_person AS p ON e.pid=p.pid";

		if(is_numeric($key)){
			#$key=(int)$key;
			$this->sql.=" WHERE e.encounter_nr = '$key' AND  e.is_discharged IN ('',0)".$add_opt;
		}elseif($key=='%'||$key=='*'){
			$this->sql.=" WHERE e.is_discharged IN ('',0) AND e.status NOT IN ($this->dead_stat) ".$add_opt;
		}else{
			/*$this->sql.=" WHERE (e.encounter_nr $sql_LIKE '$key%'
						OR p.pid $sql_LIKE '$key%'
						OR p.name_last $sql_LIKE '$key%'
						OR p.name_first $sql_LIKE '$key%'
						OR p.date_birth $sql_LIKE '$key%')";
			*/

			#edited by VAN 05-26-2010
			# Try to detect if searchkey is composite of first name + last name
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
			# Check the size of the comp
			if(sizeof($comp)>1){
				$this->sql .= " WHERE ((p.name_last LIKE '".strtr($ln,'+',' ')."%' AND p.name_first LIKE '%".strtr($fn,'+',' ')."%'))";
			}else{
				$this->sql .= " WHERE (p.name_last LIKE '".$key."%')";
			}
			#--------------------05-26-2010

			#commented by VAN 02-05-08
			#encounter_class_nr = 2 is ER, no room yet
			#if($enc_class) $this->sql.="	AND e.encounter_class_nr=$enc_class";
			$this->sql.="  AND  e.is_discharged IN ('',0) AND e.status NOT IN ($this->dead_stat) ".$add_opt;
		}
#echo "class_encounter.php : _searchAdmissionBasicInfo :: this->sql='".$this->sql."' <br> \n";
		if($limit){
	    	$this->res['sabi']=$db->SelectLimit($this->sql,$len,$so);
		}else{
	    	$this->res['sabi']=$db->Execute($this->sql);
		}
	    if ($this->res['sabi']){
		   	if ($this->record_count=$this->res['sabi']->RecordCount()) {
				$this->rec_count=$this->record_count; # workaround
				return $this->res['sabi'];
			} else{return FALSE;}
		}else{return FALSE;}
	}
	/**
	* Searches and returns inpatient admissions based on a supplied keyword.
	*
	* See <var>_searchAdmissionBasicInfo()</var> for details of the resulting data structure.
	*
	* Example usage:
	* <code>
	* $kw="Magellan";
	* if($result=$obj->searchInpatientBasicInfo($kw)){
	*    echo $obj->LastRecordCount();  # Prints the number of resulting rows
	*    while($admission=$result->FetchRow()){
	*        echo $admission['name_last'];  # Prints the patient's name
	*      ......
	*    }
	* }
	* </code>
	*
	*
	* @access public
	* @param str Search keyword
	* @return mixed adodb object or boolean
	*/
	function searchInpatientBasicInfo($key){
		return $this->_searchAdmissionBasicInfo($key,1); // 1 = inpatient (encounter class)
	}
	/**
	* Searches and returns inpatient admissions based on a supplied keyword.
	*
	* See <var>_searchAdmissionBasicInfo()</var> for details of the resulting data structure.
	*
	* Example usage:
	* <code>
	* $kw="Jennifer";
	* if($result=$obj->searchOutpatientBasicInfo($kw)){
	*    echo $obj->LastRecordCount();  # Prints the number of resulting rows
	*    while($admission=$result->FetchRow()){
	*        echo $admission['name_last'];  # Prints the patient's name
	*      ......
	*    }
	* }
	* </code>
	*
	*
	* @access public
	* @param str Search keyword
	* @return mixed adodb object or boolean
	*/
	function searchOutpatientBasicInfo($key){
		return $this->_searchAdmissionBasicInfo($key,2); // 2 = outpatient (encounter class)
	}
	/**
	* Search returning the basic admission information as outlined at <var>_searchAdmissionBasicInfo()</var>.
	*
	* This method gives the possibility to sort the results based on an item and sorting direction.
	* @access public
	* @param string Search keyword
	* @param string Item as sort basis
	* @param string Sorting direction. ASC = ascending, DESC  = descending, empty = ascending
	* @return mixed adodb record object or boolean
	*/
	function searchEncounterBasicInfo($key,$sortitem='',$order=''){
		if(!empty($sortitem)){
			$option=' ORDER BY ';
			switch($sortitem){
				case 'LASTNAME': $option.=' p.name_last '; break;
				case 'FIRSTNAME': $option.=' p.name_first '; break;
				case 'ENCNR': $option.=' e.encounter_nr '; break;
				case 'BDAY': $option.=' p.date_birth '; break;
				default: $option.='';
			}
			$option.=$order;
		}
		return $this->_searchAdmissionBasicInfo($key,0,$option); // 0 = all kinds of admission
	}
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
	*/
	function searchLimitEncounterBasicInfo($key,$len,$so,$sortitem='',$order='ASC'){
		if(!empty($sortitem)){
			$option=" ORDER BY $sortitem $order";
		}
		return $this->_searchAdmissionBasicInfo($key,0,$option,TRUE,$len,$so); // 0 = all kinds of admission
	}
	/**
	* Search for inpatients who are not yet finally admittd in ward, returning basic information as outlined at <var>_searchAdmissionBasicInfo()</var>.
	*
	* The resulting data is usually listed on the "waiting list" modules.
	* @access public
	* @param string Search keyword
	* @return mixed adodb record object or boolean
	*/
	function searchInpatientNotInWardBasicInfo($key){
		return $this->_searchAdmissionBasicInfo($key,1,'AND NOT in_ward'); // 1 = outpatient (encounter class)
	}
	/**
	* Checks if the encounter exists in the database based on the encounter number key.
	*
	* If the encounter exists, its PID number will be returned, else FALSE will be returned.
	* @access public
	* @param int Encounter number
	* @return mixed integer or boolean
	*/
	function EncounterExists($enc_nr){
	    global $db;
		$this->sql="SELECT pid FROM $this->tb_enc WHERE encounter_nr='$enc_nr'";
		if($this->result=$db->Execute($this->sql)){
		    if($this->result->RecordCount()) {
			    $this->row=$this->result->FetchRow();
				return $this->row['pid'];
			} else return FALSE;
		}else {
		    return FALSE;
		}
	}
	/**
	* Checks if the encounter is in a location based on the  location's type number.
	*
	* If the encounter is in the said location, its record primary key number will be returned, else FALSE.
	* This method uses the internaly buffered encounter number. The number must be set first before using
	* this method either  with <var>setEncounterNr()</var> or by directly assigning to the <var>$enc_nr</var> variable .
	* @access private
	* @param int Encounter number
	* @return mixed integer or boolean
	*/
	function _InLocation($type_nr){
		global $db;
		/*
		if($this->result=$db->Execute("SELECT nr FROM $this->tb_location WHERE encounter_nr=$this->enc_nr AND type_nr=$type_nr AND location_nr=$this->loc_nr AND date_to='0000-00-00'")){
			if($this->result->RecordCount()){
				return $this->result['nr'];
			}else{return FALSE;}
		}else{return FALSE;}
		*/
		#edited by VAN 03-06-09
		$this->sql = "SELECT nr FROM ".$this->tb_location."
						WHERE encounter_nr='".$this->enc_nr."'
						AND type_nr='".$type_nr."'
						AND location_nr='".$this->loc_nr."'
						AND date_to='0000-00-00'";

				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								$row = $this->result->FetchRow();
				return $row['nr'];
						}
						else{return FALSE;}
				}else{return FALSE;}
	}
	/**
	* Checks if the encounter is finally admitted in a ward.
	*
	* If the encounter is in the said ward location, its record primary key number will be returned, else FALSE.
	* @access private
	* @param int Encounter number
	* @param int Ward number
	* @return mixed integer or boolean
	*/
	function InWard($enr,$loc_nr){
		$this->enc_nr=$enr;
		$this->loc_nr=$loc_nr;
		return $this->_InLocation(2);
	}
	/**
	* Checks if the encounter has been finally assigned a room.
	*
	* If the room location is finally assigned, its record primary key number will be returned, else FALSE.
	* @access private
	* @param int Encounter number
	* @param int Room number
	* @return mixed integer or boolean
	*/
	function InRoom($enr,$loc_nr){
		$this->enc_nr=$enr;
		$this->loc_nr=$loc_nr;
		return $this->_InLocation(4);
	}
	/**
	* Checks if the encounter has been finally assigned a bed.
	*
	* If the bed location is finally assigned, its record primary key number will be returned, else FALSE.
	* @access private
	* @param int Encounter number
	* @param int Bed number
	* @return mixed integer or boolean
	*/
	function InBed($enr,$loc_nr){
		$this->enc_nr=$enr;
		$this->loc_nr=$loc_nr;
		return $this->_InLocation(5);
	}
	/**
	* Checks if the encounter (outpatient) is finally admitted to a department (or clinic).
	*
	* If finally admitted to department location, its record primary key number will be returned, else FALSE.
	* @access private
	* @param int Encounter number
	* @param int Department number
	* @return mixed integer or boolean
	*/
	function InDept($enr,$loc_nr){
		$this->enc_nr=$enr;
		$this->loc_nr=$loc_nr;
		return $this->_InLocation(1);
	}
	/**
	* Saves the encounter location with a given location type, location group and location number.
	*
	* @access private
	* @param int Encounter number
	* @param int Location type number
	* @param int Location number
	* @param int Location group number
	* @param string  Date
	* @param string Time
	* @return boolean
	*/
	function _setLocation($enr=0,$type_nr=0,$loc_nr=0,$group_nr,$source='',$date='',$time=''){
		global $HTTP_SESSION_VARS, $db;
		//$db->debug=1;
		//if(!($enr&&$type_nr&&$loc_nr)) return FALSE;
		if(empty($date)) $date=date('Y-m-d');
		if(empty($time)) $time=date('H:i:s');
		$user=$HTTP_SESSION_VARS['sess_user_name'];
		$history="Create: ".date('Y-m-d H:i:s')." ".$user."\n";
		$this->sql="INSERT INTO $this->tb_location (encounter_nr,type_nr,location_nr,group_nr,date_from,time_from,history,create_id,create_time,source_assign)
						VALUES
						('$enr','$type_nr','$loc_nr','$group_nr','$date','$time','$history','$user','".date('YmdHis')."','".$source."')";
		#echo "<br>sql = ".$this->sql;
		//if($this->Transact($this->sql))	return true; else	echo $this->sql;
		return $this->Transact($this->sql);
	}
	/**
	* Saves the encounter's ward location.
	* If the save routine is successful, the "currently in ward" flag of the encounter record will also be set internally.
	* @access public
	* @param int Encounter number
	* @param int Ward number
	* @param int Department number
	* @param string  Date
	* @param string Time
	* @return boolean
	*/
	function assignInWard($enr,$loc_nr,$group_nr,$source,$date,$time){
		if($this->_setLocation($enr,2,$loc_nr,$group_nr,$source,$date,$time)){ # loc. type 2 = ward
			return $this->setCurrentWardInWard($enr,$loc_nr);
		}
	}
	/**
	* Saves the encounter's room location.
	* @access public
	* @param int Encounter number
	* @param int Room number
	* @param int Ward number
	* @param string  Date
	* @param string Time
	* @return boolean
	*/
	function assignInRoom($enr,$loc_nr,$group_nr,$source,$date,$time){
		return $this->_setLocation($enr,4,$loc_nr,$group_nr,$source,$date,$time); # loc. type 4 = room
	}
	/**
	* Saves the encounter's room location.
	* @access public
	* @param int Encounter number
	* @param int Bed number
	* @param int Room number
	* @param string  Date
	* @param string Time
	* @return boolean
	*/
	function assignInBed($enr,$loc_nr,$group_nr,$source,$date,$time){
		return $this->_setLocation($enr,5,$loc_nr,$group_nr,$source,$date,$time); # loc. type 5 = bed
	}
	/**
	* Saves the encounter's room location.
	* If the save routine is successful, the "currently in department" flag of the encounter record will also be set internally.
	* @access public
	* @param int Encounter number
	* @param int Department number
	* @param int Department number
	* @param string  Date
	* @param string Time
	* @return boolean
	*/
	function assignInDept($enr,$loc_nr,$group_nr,$date,$time){
		if($this->_setLocation($enr,1,$loc_nr,$group_nr,$date,$time)){ # loc. type 1 = department
			return $this->setCurrentDeptInDept($enr,$loc_nr);
		}
	}
	/**
	* Admits a patient in ward with  a ward number, room number and bed number.
	* If the save routine is successful, the "currently in ward" flag of the encounter record will also be set internally.
	* @access public
	* @param int Encounter number
	* @param int Ward number
	* @param int Room number
	* @param int Bed number
	* @return boolean
	*/
	#function AdmitInWard($enr,$ward_nr,$room_nr,$bed_nr){
	#edited by VAN 08-20-08
	function AdmitInWard($enr,$ward_nr,$room_nr,$bed_nr,$source='', $date='', $time=''){
		global $db;

		if (empty($date))
			$date=date('Y-m-d');
		else
			$date=date('Y-m-d',strtotime($date));

		if (empty($time))
			$time=date('H:i:s');
		else
			$time=date('H:i:s',strtotime($time));

		if($this->InWard($enr,$ward_nr)){
			$ok=true;
		}else{
			if($this->assignInWard($enr,$ward_nr,$ward_nr,$source,$date,$time)){
				$ok=true;
			}else{$ok=FALSE;}
		}
		if($this->InRoom($enr,$room_nr)){
			$ok=true;
		}else{
			if($this->assignInRoom($enr,$room_nr,$ward_nr,$source,$date,$time)){
				$ok=true;
			}else{$ok=FALSE;}
		}
		if($ok&&!$this->InBed($enr,$bed_nr)){
			if($this->assignInBed($enr,$bed_nr,$ward_nr,$source,$date,$time)){
				return true;
			}else{return FALSE;}
		}else{
			return FALSE;
		}
	}

	#---added by VAN 06-18-08
	function setPatientRoomRate($loc_enc_nr,$encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,$rate,$status){
		$this->sql="INSERT INTO seg_encounter_location_rate(loc_enc_nr,encounter_nr,ward_nr,room_nr,bed_nr,rate,status)
					VALUES('$loc_enc_nr','$encounter_nr','$current_ward_nr','$current_room_nr','$current_bed_nr','$rate','$status')";

    	return $this->Transact($this->sql);
	}

	function isExistInLocationRate($loc_enc_nr,$encounter_nr){
		global $db;

		$this->sql = "SELECT * FROM seg_encounter_location_rate
						  WHERE encounter_nr='$encounter_nr'
						  AND loc_enc_nr = '$loc_enc_nr'";

		if ($this->result=$db->Execute($this->sql)) {
         $this->count=$this->result->RecordCount();
         return $this->result;
      	} else{
        	 return FALSE;
     	}
	}

	function setTransferredLocation($encounter_nr, $loc_enc_nr=0){
		$this->sql="UPDATE seg_encounter_location_rate
					SET status='Transferred'
					WHERE encounter_nr='$encounter_nr'
					AND loc_enc_nr = '$loc_enc_nr'";

    	return $this->Transact($this->sql);
	}

	//added by VAN 05-26-2010 // edited by pol 07-02-2013
	function updateassignWardwaiting($encounter_nr, $ward_nr, $source, $date, $time){
			global $db, $HTTP_SESSION_VARS;
			$act = 'Modified Ward Assignment in Waitinglist';

			$fldarray = array('encounter_nr' => $db->qstr($encounter_nr),
                        'type_nr' => $db->qstr('2'),
                        'discharge_type_nr' => $db->qstr('0'),
                        'status' => $db->qstr(''),
                        'date_to' => $db->qstr('0000-00-00'),
                        'location_nr' => $db->qstr($ward_nr),
                        'source_assign' => $db->qstr($source),
                        'group_nr' => $db->qstr($ward_nr),
                        'date_from' => $db->qstr($date),
                        'time_from' => $db->qstr($time),
                        'history' => $db->qstr($this->ConcatHistory("\n$act ".date('Y-m- H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name'])),
                        'modify_id' => $db->qstr($HTTP_SESSION_VARS['sess_user_name']),
                        'modify_time' => $db->qstr(date('YmdHis')),
                        'create_id' => $db->qstr($HTTP_SESSION_VARS['sess_user_name']),
                        'create_time' => $db->qstr(date('YmdHis')),
                       );
      		 $bsuccess = $db->Replace('care_encounter_location', $fldarray, array('encounter_nr','type_nr','discharge_type_nr','status','date_to'));
      		  if($bsuccess){
           			 return true;
       			 }else{
       			 	return FALSE;
       			 }

	}

	#Added by Cherry 08-12-10

	function updateConsulation(&$data, $encounter_nr){
		global $db, $HTTP_SESSION_VARS;
		extract($data);

		$this->sql = "UPDATE care_encounter
									SET chief_complaint='$chief_complaint',
									consulting_dept_nr='$consulting_dept_nr',
									consulting_dr_nr='$consulting_dr_nr',
									history=".$this->ConcatHistory("\n$act ".date('Y-m- H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']).",
									modify_id='".$HTTP_SESSION_VARS['sess_user_name']."',
									modify_time='".date('YmdHis')."'
									WHERE encounter_nr='$encounter_nr'";
		return $this->Transact($this->sql);

	}

	#end cherry
	// edited by pol 07-02-2013
	function updateassignRoomwaiting($encounter_nr, $ward_nr, $room_nr, $source, $date, $time, $other_ward=0){
			global $db, $HTTP_SESSION_VARS;
			$act = 'Modified Room Assignment in Waitinglist';



			if ($other_ward){
					$fldarray = array('encounter_nr' => $db->qstr($encounter_nr),
                        'type_nr' => $db->qstr('4'),
                        'discharge_type_nr' => $db->qstr('0'),
                        'status' => $db->qstr(''),
                        'date_to' => $db->qstr('0000-00-00'),
                        'location_nr' => $db->qstr($room_nr),
                        'source_assign' => $db->qstr($source),
                        'group_nr' => $db->qstr($ward_nr),
                        'date_from' => $db->qstr($date),
                        'time_from' => $db->qstr($time),
                        'history' => $db->qstr($this->ConcatHistory("\n$act ".date('Y-m- H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name'])),
                        'modify_id' => $db->qstr($HTTP_SESSION_VARS['sess_user_name']),
                        'modify_time' => $db->qstr(date('YmdHis')),
                        'create_id' => $db->qstr($HTTP_SESSION_VARS['sess_user_name']),
                        'create_time' => $db->qstr(date('YmdHis')),
                       );
      				 $bsuccess = $db->Replace('care_encounter_location', $fldarray, array('encounter_nr','type_nr','discharge_type_nr','status','date_to'));
			}else{
					$fldarray = array('encounter_nr' => $db->qstr($encounter_nr),
                        'type_nr' => $db->qstr('4'),
                        'group_nr' => $db->qstr($ward_nr),
                        'discharge_type_nr' => $db->qstr('0'),
                        'status' => $db->qstr(''),
                        'date_to' => $db->qstr('0000-00-00'),
                        'location_nr' => $db->qstr($room_nr),
                        'source_assign' => $db->qstr($source),
                        'date_from' => $db->qstr($date),
                        'time_from' => $db->qstr($time),
                        'history' => $db->qstr($this->ConcatHistory("\n$act ".date('Y-m- H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name'])),
                        'modify_id' => $db->qstr($HTTP_SESSION_VARS['sess_user_name']),
                        'modify_time' => $db->qstr(date('YmdHis')),
                        'create_id' => $db->qstr($HTTP_SESSION_VARS['sess_user_name']),
                        'create_time' => $db->qstr(date('YmdHis')),
                       );
      				 $bsuccess = $db->Replace('care_encounter_location', $fldarray, array('encounter_nr','type_nr','discharge_type_nr','status','date_to'));
			}

			 if($bsuccess){
           			 return true;
       			 }else{
       			 	return FALSE;
       			 }
	}
	// edited by pol 07-02-2013
	function updateassignBedwaiting($encounter_nr, $ward_nr, $bed_nr, $source, $date, $time, $other_ward=0){
			global $db, $HTTP_SESSION_VARS;
			$act = 'Modified Bed Assignment in Waitinglist';


			if ($other_ward){
						$fldarray = array('encounter_nr' => $db->qstr($encounter_nr),
                        'type_nr' => $db->qstr('5'),
                        'discharge_type_nr' => $db->qstr('0'),
                        'status' => $db->qstr(''),
                        'date_to' => $db->qstr('0000-00-00'),
                        'location_nr' => $db->qstr($bed_nr),
                        'source_assign' => $db->qstr($source),
                        'group_nr' => $db->qstr($ward_nr),
                        'date_from' => $db->qstr($date),
                        'time_from' => $db->qstr($time),
                        'history' => $db->qstr($this->ConcatHistory("\n$act ".date('Y-m- H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name'])),
                        'modify_id' => $db->qstr($HTTP_SESSION_VARS['sess_user_name']),
                        'modify_time' => $db->qstr(date('YmdHis')),
                        'create_id' => $db->qstr($HTTP_SESSION_VARS['sess_user_name']),
                        'create_time' => $db->qstr(date('YmdHis')),
                       );
      				 $bsuccess = $db->Replace('care_encounter_location', $fldarray, array('encounter_nr','type_nr','discharge_type_nr','status','date_to'));
				}else{
						$fldarray = array('encounter_nr' => $db->qstr($encounter_nr),
                        'type_nr' => $db->qstr('5'),
                        'group_nr' => $db->qstr($ward_nr),
                        'discharge_type_nr' => $db->qstr('0'),
                        'status' => $db->qstr(''),
                        'date_to' => $db->qstr('0000-00-00'),
                        'location_nr' => $db->qstr($bed_nr),
                        'source_assign' => $db->qstr($source),
                        'date_from' => $db->qstr($date),
                        'time_from' => $db->qstr($time),
                        'history' => $db->qstr($this->ConcatHistory("\n$act ".date('Y-m- H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name'])),
                        'modify_id' => $db->qstr($HTTP_SESSION_VARS['sess_user_name']),
                        'modify_time' => $db->qstr(date('YmdHis')),
                        'create_id' => $db->qstr($HTTP_SESSION_VARS['sess_user_name']),
                        'create_time' => $db->qstr(date('YmdHis')),
                       );
      				 $bsuccess = $db->Replace('care_encounter_location', $fldarray, array('encounter_nr','type_nr','discharge_type_nr','status','date_to'));
				}
			 if($bsuccess){
           			 return true;
			}else{
       			 	return FALSE;
			}

	}

	// edited by pol 07-02-2013
	function updateLocateRatewaiting($encounter_nr, $ward_nr, $room_nr, $bed_nr, $rate, $loc_enc_nr){
			global $db, $HTTP_SESSION_VARS;

			$fldarray = array('encounter_nr' => $db->qstr($encounter_nr),
                        'loc_enc_nr' => $db->qstr($loc_enc_nr),
                        'ward_nr' => $db->qstr($ward_nr),
                        'room_nr' => $db->qstr($room_nr),
                        'bed_nr' => $db->qstr($bed_nr),
                        'rate' => $db->qstr($rate),
                       );
      				 $bsuccess = $db->Replace('seg_encounter_location_rate', $fldarray, array('encounter_nr','loc_enc_nr'));


			if($bsuccess){
           			 return true;
       			 }else{
       			 	return FALSE;
       			 }
	}
	//--------------

	function InsertLocateBedWaiting($encounter_nr, $ward_nr, $room_nr, $bed_nr, $rate, $loc_enc_nr){
		$this->sql="INSERT INTO seg_encounter_location_rate
			(nr,encounter_nr,ward_nr,room_nr,bed_nr,rate,STATUS)
		VALUES
			($loc_enc_nr,'$encounter_nr','$ward_nr','$room_nr','$bed_nr','$rate','')";
		return $this->Transact($this->sql);
	}
	function getLatestLocNr($encounter_nr){
		global $db;

		$this->sql = "SELECT * FROM care_encounter_location WHERE
					  encounter_nr='$encounter_nr'
						AND type_nr='4' AND status NOT IN('discharged')";

		if ($this->result=$db->Execute($this->sql)) {
         $this->count=$this->result->RecordCount();
         return $this->result->FetchRow();
      	} else{
        	 return FALSE;
     	}
	}
	#-------------------------

	/**
	* Updates location assignment items. Generic method for setting location assigment information.
	* @access private
	* @param int Encounter nr
	* @param string Data for updating, formatted in sql syntax
	* @param string Modification action for appeding to the record's history, defaults to "modified"
	* @return boolean
	*/
	function _setCurrentAssignment($enr,$data='',$act='Modified'){
		global $HTTP_SESSION_VARS, $dbtype;
		if(!$enr||empty($data)) return FALSE;

		#echo "<br>data = '".$data."'";
		/*
		if($dbtype=='mysql'){
			$data.=",history=CONCAT(history,'\n$act ".date('Y-m- H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."'), ";
		}else{
			$data.=",history=(history || '\n$act ".date('Y-m- H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."'), ";
		}
		*/
		$data.=",history=".$this->ConcatHistory("\n$act ".date('Y-m- H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']).", ";
		$data.="	modify_id='".$HTTP_SESSION_VARS['sess_user_name']."',
				modify_time='".date('YmdHis')."'";
		$this->sql="UPDATE $this->tb_enc SET $data WHERE encounter_nr='$enr'";

		#echo "<br>enc class replaceward = ".$this->sql;

		return $this->Transact($this->sql);
	}
	/**
	* Sets encounter's current ward number.
	* @access public
	* @param int Encounter nr
	* @param int New ward number
	* @return boolean
	*/
	function setCurrentWard($enr,$assign_nr){
		return $this->_setCurrentAssignment($enr,"current_ward_nr=$assign_nr",'Set ward');
	}
	/**
	* Sets encounter's current ward number and set the "currently in ward" status of the encounter.
	* @access public
	* @param int Encounter nr
	* @param int New ward number
	* @return boolean
	*/
	function setCurrentWardInWard($enr,$assign_nr){
		global $HTTP_POST_VARS;
		#return $this->_setCurrentAssignment($enr,"encounter_status='disallow_cancel',current_ward_nr=$assign_nr,in_ward=1",'Set ward + in ward');
		return $this->_setCurrentAssignment($enr,"encounter_status='".$HTTP_POST_VARS['encounter_status']."',current_ward_nr=$assign_nr,in_ward=1",'Set ward + in ward');
	}
	/**
	* Sets encounter's current room number.
	* @access public
	* @param int Encounter nr
	* @param int New room number
	* @return boolean
	*/
	function setCurrentRoom($enr,$assign_nr){
		return $this->_setCurrentAssignment($enr,"current_room_nr=$assign_nr",'Set room');
	}
	/**
	* Sets encounter's current department number.
	* @access public
	* @param int Encounter nr
	* @param int New department number
	* @return boolean
	*/
	function setCurrentDept($enr,$assign_nr){
		return $this->_setCurrentAssignment($enr,"current_dept_nr=$assign_nr",'Set dept');
	}
	/**
	* Sets encounter's current department number and sets the "currently in department" status of the encounter..
	* @access public
	* @param int Encounter nr
	* @param int New department number
	* @return boolean
	*/
	function setCurrentDeptInDept($enr,$assign_nr){
		global $HTTP_POST_VARS;

		#return $this->_setCurrentAssignment($enr,"encounter_status='disallow_cancel',current_dept_nr=$assign_nr,in_dept=1",'Set dept + in dept');
		return $this->_setCurrentAssignment($enr,"encounter_status='".$HTTP_POST_VARS['encounter_status']."',current_dept_nr=$assign_nr,in_dept=1",'Set dept + in dept');
	}
	/**
	* Sets encounter's current firm number.
	* @access public
	* @param int Encounter nr
	* @param int New firm number
	* @return boolean
	*/
	function setCurrentFirm($enr,$assign_nr){
		return $this->_setCurrentAssignment($enr,"current_firm_nr=$assign_nr",'Set firm');
	}
	/**
	* Sets encounter's current attending physician number.
	* @access public
	* @param int Encounter nr
	* @param int Attending physician number
	* @return boolean
	*/
	function setCurrentAttdDr($enr,$assign_nr){
		return $this->_setCurrentAssignment($enr,"current_att_dr_nr=$assign_nr",'Set attd dr.');
	}
	/**
	* Resets encounter's current ward number to 0.
	* @access public
	* @param int Encounter nr
	* @return boolean
	*/
	function resetCurrentWard($enr){
		return $this->_setCurrentAssignment($enr,"current_ward_nr=0,in_ward=0",'Reset current ward');
	}
	/**
	* Resets encounter's current department number to 0.
	* @access public
	* @param int Encounter nr
	* @return boolean
	*/
	function resetCurrentDept($enr){
		return $this->_setCurrentAssignment($enr,"current_dept_nr=0,in_dept=0",'Reset current dept');
	}
	/**
	* Sets encounter's current "ward" status to "In ward". Sets the encounter to "disallow cancel".
	* @access public
	* @param int Encounter nr
	* @return boolean
	*/
	function setInWard($enr){
		return $this->_setCurrentAssignment($enr,"current_status='disallow_cancel',in_ward=1",'Set in ward');
	}
	/**
	* Resets encounter's current "ward" status to "not in ward".
	* @access public
	* @param int Encounter nr
	* @return boolean
	*/
	function setNotInWard($enr){
		return $this->_setCurrentAssignment($enr,'in_ward=0','Set not in ward');
	}
	/**
	* Sets encounter's current "department" status to "In department". Sets the encounter to "disallow cancel".
	* @access public
	* @param int Encounter nr
	* @return boolean
	*/
	function setInDept($enr){
		return $this->_setCurrentAssignment($enr,"current_status='disallow_cancel',in_dept=1",'Set in dept');
	}
	/**
	* Resets encounter's current "department" status to "not in department".
	* @access public
	* @param int Encounter nr
	* @return boolean
	*/
	function setNotInDept($enr){
		return $this->_setCurrentAssignment($enr,'in_dept=0','Set not in dept');
	}
	/**
	* Sets encounter's two status to "In ward" and "disallow cancel". Sets the current ward number and current room number.
	* @access public
	* @param int Encounter nr
	* @param int Ward nr
	* @param int Room nr
	* @param int Bed nr (reserved)
	* @return boolean
	*/
	# commented by VAN 01-25-08
	/*
	function setAdmittedInWard($enr,$ward_nr,$room_nr,$bed_nr){
		return $this->_setCurrentAssignment($enr,"encounter_status='disallow_cancel',current_ward_nr=$ward_nr,current_room_nr=$room_nr,in_ward=1",'Admitted in ward');
		#Replaced ward
	}
	*/
	# edited by VAN 01-25-08
	function setAdmittedInWard($enr,$ward_nr,$room_nr,$bed_nr,$mode,$inward){
		global $HTTP_POST_VARS;
		#echo 'status = '.$HTTP_POST_VARS['encounter_status'];
		if ($mode){
			#echo "<br>setadmit 1 mode = ".$mode;
			#return $this->_setCurrentAssignment($enr,"encounter_status='disallow_cancel',current_ward_nr=$ward_nr,current_room_nr=$room_nr,in_ward=1",'Admitted in ward');
			#edited by VAN 02-06-08
			#return $this->_setCurrentAssignment($enr,"encounter_status='disallow_cancel',current_ward_nr='$ward_nr',current_room_nr='$room_nr',in_ward=1",'Admitted in ward');
			#return $this->_setCurrentAssignment($enr,"encounter_status='disallow_cancel',current_ward_nr='$ward_nr',current_room_nr='$room_nr',in_ward=$inward",'Admitted in ward');
			return $this->_setCurrentAssignment($enr,"encounter_status='".$HTTP_POST_VARS['encounter_status']."',current_ward_nr='$ward_nr',current_room_nr='$room_nr',in_ward=$inward",'Admitted in ward');
		}else{
			#echo "<br>setadmit 2 mode = ".$mode;
			#return $this->_setCurrentAssignment($enr,"current_ward_nr=$ward_nr,current_room_nr=$room_nr,in_ward=1",'Replaced ward');
			#edited by VAN 02-06-08
			#return $this->_setCurrentAssignment($enr,"current_ward_nr=$ward_nr,current_room_nr=$room_nr,in_ward=1",'Replaced ward');
			if ($inward)
				 $caption = 'Replaced ward';
			else
				 $caption = 'Delete ward';

			return $this->_setCurrentAssignment($enr,"current_ward_nr=$ward_nr,current_room_nr=$room_nr,in_ward=$inward",$caption);
			#echo "setAdmit sql = ".$this->sql;
		}
	}

	/**
	* Resets encounter's current locations to 0.
	* Resetted locations are:
	* - current ward number
	* - current room number
	* - current departement number
	* - current firm number
	* - in ward flag
	* @access public
	* @param int Encounter nr
	* @return boolean
	*/
	function ResetAllCurrentPlaces($enr){
		return $this->_setCurrentAssignment($enr,'current_ward_nr=0,current_room_nr=0,current_dept_nr=0,current_firm_nr=0,in_ward=0','Reset all locations');
	}

	/**
	* Cancels an encounter, but only when its encounter_status is set to '' (emtpy) or 'allow_cancel'.
	* Sets the encounter_status= 'cancelled', status='void', is_discharged=1 and stores history and modify infos
	* @access public
	* @param int Encounter number
	* @param string Optional name of person responsible for cancellation
	* @return boolean
	*/
	function Cancel($enc_nr=0,$iscancel_admission=0,$by,$enctype=0){
	//function Cancel($enc_nr=0,$by){
		global $HTTP_SESSION_VARS;
		if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;
		if(empty($by)) $by=$HTTP_SESSION_VARS['sess_user_name'];

		if ($iscancel_admission){
			$this->sql="UPDATE $this->tb_enc SET encounter_type='".$enctype."',
						history=".$this->ConcatHistory("Cancelled Only Admission ".date('Y-m-d H:i:s')." by $by, logged-user ".$HTTP_SESSION_VARS['sess_user_name']."\n").",
						encounter_status='cancelled',status='void',
						modify_id='".$HTTP_SESSION_VARS['sess_user_name']."',
						modify_time='".date('Y-m-d H:i:s')."'
						WHERE encounter_nr='$enc_nr'";
		}else{

			$this->sql="UPDATE $this->tb_enc SET encounter_status='cancelled',status='void',is_discharged=1,
						history=".$this->ConcatHistory("Cancelled ".date('Y-m-d H:i:s')." by $by, logged-user ".$HTTP_SESSION_VARS['sess_user_name']."\n").",
						modify_id='".$HTTP_SESSION_VARS['sess_user_name']."',
						modify_time='".date('Y-m-d H:i:s')."'
						WHERE encounter_nr='".$this->enc_nr."' /*AND encounter_status IN ('','0','allow_cancel','direct_admission')*/";
		}
#						WHERE encounter_nr=$this->enc_nr AND encounter_status IN ('','0','allow_cancel')";   # burn commented : May 24, 2007
#echo "class_encounter.php : Cancel($enc_nr,$by) : this->sql = '".$this->sql."' <br> \n";
#exit();
		return $this->Transact($this->sql);
	}

	#added by VAN 02-16-09
	function ResetEncounter($parent_enc_nr=0,$by){
		global $HTTP_SESSION_VARS;
		if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;
		if(empty($by)) $by=$HTTP_SESSION_VARS['sess_user_name'];

		$this->sql="UPDATE $this->tb_enc SET
						is_discharged=0,
						discharge_date=null,
						discharge_time=null,
						history=".$this->ConcatHistory("Cancelled Admission and Uncancel previous Encounter  ".date('Y-m-d H:i:s')." by $by, logged-user ".$HTTP_SESSION_VARS['sess_user_name']."\n").",
						modify_id='".$HTTP_SESSION_VARS['sess_user_name']."',
						modify_time='".date('Y-m-d H:i:s')."'
						WHERE encounter_nr='".$parent_enc_nr."'";

#						WHERE encounter_nr=$this->enc_nr AND encounter_status IN ('','0','allow_cancel')";   # burn commented : May 24, 2007
#echo "class_encounter.php : Cancel($enc_nr,$by) : this->sql = '".$this->sql."' <br> \n";
#exit();
		return $this->Transact($this->sql);
	}
	#---------------------

	/**
	* Replaces the current ward number and resets the in_ward flag to 0: status is "not in ward".
	* @access public
	* @param int Encounter number
	* @param int New ward number
	* @return boolean
	*/
	function ReplaceWard($enr,$ward_nr){
		return $this->_setCurrentAssignment($enr,"current_ward_nr=$ward_nr,in_ward=0",'Replaced ward');
	}

	/**
	* Sets the discharge status that the encounter/admission is fully discharged.
	* Sets the is_discharged field of care_encounter table and resets the current department,ward,room fields.
	* @access public
	* @param int Encounter number
	* @param string Date of discharge
	* @param string Time of discharge
	* @return boolean
	*/
	function setIsDischarged($enr,$date,$time){
		global $HTTP_SESSION_VARS;
		//$this->sql="UPDATE $this->tb_enc SET is_discharged=1, discharge_date='$date',discharge_time='$time', current_ward_nr=0,current_room_nr=0,current_dept_nr=0,current_firm_nr=0,in_ward=0 WHERE encounter_nr=$enr AND NOT is_discharged";
		#$this->sql="UPDATE $this->tb_enc SET is_discharged=1, discharge_date='$date',discharge_time='$time', in_ward=0,in_dept=0 WHERE encounter_nr=$enr AND is_discharged IN ('',0)";
        $this->sql="UPDATE $this->tb_enc SET is_discharged=1,
                          discharge_date='$date',
                          discharge_time='$time',
                          history =".$this->ConcatHistory("Update (discharged): ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n").",
													modify_id='".$HTTP_SESSION_VARS['sess_user_name']."',
													modify_time='".date('Y-m-d H:i:s')."',
                          in_ward=0,
                          in_dept=0
													WHERE encounter_nr='$enr' /*AND is_discharged IN ('',0)*/";
		//if($this->Transact($this->sql)) return true; else echo $this->sql;
		#echo "sql = ".$this->sql;
		return $this->Transact($this->sql);
	}

	/** // Added by mark on Apr 18, 2007
	 * Temp setting of discharged.. status..
	 * @access public
	 * @param int Encounter number
	 * @param string Date of discharge
	 * @param string Time of discharge
	 * @return boolean
	 */
	function setIsDischarged_d($enc,$date, $time,$current_dr_nr,$type){
		global $HTTP_SESSION_VARS;
        if($type == 3 || $type == 4){ //for Inpatient
			$this->sql = "UPDATE $this->tb_enc SET is_discharged =1 , discharge_date = '$date', ".
						  "\n discharge_time = '$time', ".
						  "\n history =".$this->ConcatHistory("Update (discharged): ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n").", ".
                          "\n current_att_dr_nr= '$current_dr_nr'
													WHERE encounter_nr='$enc'";
		}else{ // ER and OPD patient only
			$this->sql = "UPDATE $this->tb_enc SET is_discharged =1 , discharge_date = '$date', ".
						  "\n discharge_time = '$time', consulting_dr_nr='$current_dr_nr', ".
						  "\n history =".$this->ConcatHistory("Update (discharged): ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n").",".
													"\n current_att_dr_nr= '$current_dr_nr' WHERE encounter_nr='$enc'";
		}
		return $this->Transact($this->sql);
	}

	/**
	 * Update department of diagnosis / procedure of care_encounter_diagnosis
	 * @access public
	 * @param array encounter diagnosis / procedure. By reference.
	 * @return boolean
	 */
	function updateCareEncounterDiagnosis($enc='',$code,$dept,$doc){
		if(empty($enc)&& empty($code)) return FALSE;
		$this->sql = "UPDATE $this->tb_care_enc_diagnosis SET ".
					"\n diagnosing_clinician = '$doc' ,".
					"\n diagnosing_dept_nr = '$dept' ".
					"\n WHERE encounter_nr = '$enc' AND code = '$code'";
		return $this->Transact($this->sql);
	}

	function updateCareEncounterProcedure($enc='',$code,$dept,$doc){
		if(empty($enc) && empty($code)) return FALSE;
		$this->sql = "UPDATE $this->tb_care_enc_procedure SET ".
					"\n responsible_clinician = '$doc' ,".
					"\n responsible_dept_nr = '$dept' ".
					"\n WHERE encounter_nr = '$enc' AND code= '$code'";
		return $this->Transact($this->sql);
	}

	//added by Francis 7-24-13
	//move from bed to waiting list
	function MoveToWaitingList($enr){
		global $HTTP_SESSION_VARS, $dbf_nodate, $dbtype;
		$loc_types = '5';
		$d_type_nr = "'6'";
		if(empty($date)) $date=date('Y-m-d');
		if(empty($time)) $time=date('H:i:s');

		$this->sql1="UPDATE care_encounter
							SET	in_ward='0'
							WHERE encounter_nr='$enr'";

		$this->sql2="UPDATE $this->tb_location
							SET	location_nr='0',";        
        $this->sql2.= "history =".$this->ConcatHistory("Update (waiting): ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n").",";

		$this->sql2.=" modify_id='".$HTTP_SESSION_VARS['sess_user_name']."'
							WHERE encounter_nr='$enr'
							/*AND discharge_type_nr=0*/
							AND type_nr IN ($loc_types) ORDER BY nr DESC LIMIT 3";

	
		if($this->Transact($this->sql1)){
           if($this->Transact($this->sql2)){
           		return true;
       		}
       		else{
       			return FALSE;	
       		}
        }else{
            
              return FALSE;
         }
	}

	/**
	* Gets the discharge types.
	* The resulting adodb record object contains rows of arrays.
	* Each array contains the discharge type information with the following index keys:
	* - nr = The primary key number
	* - name = the name of discharge type
	* - LD_var = the variable name for the foreign language version of the discharge name
	*
	* @return mixed adodb record object or boolean
	*/
	function getDischargeTypesData(){
		global $db;
		//$db->debug=1;
		$this->sql="SELECT nr,name,LD_var AS \"LD_var\" FROM $this->tb_dis_type ORDER BY nr";
		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()){
				return $this->result;
			}else{return FALSE;}
		}else{return FALSE;}
	}
	/**
	* Discharge an encounter.
	* Avoid using this function directly. Use the appropriate methods
	* @access private
	* @param int Encounter number
	* @param int Location type number (ward number or department number)
	* @param int Discharge type number
	* @param string Date of discharge
	* @param string Time of discharge
	* @return boolean
	*/
	function _discharge($enr,$loc_types,$d_type_nr,$date='',$time=''){
		global $HTTP_SESSION_VARS, $dbf_nodate, $dbtype;
		if(empty($date)) $date=date('Y-m-d');
		if(empty($time)) $time=date('H:i:s');
		$this->sql="UPDATE $this->tb_location
							SET	discharge_type_nr=$d_type_nr,
									date_to='$date',
									time_to='$time',
									status='discharged',";
        /*
        if($dbtype=='mysql'){
			$this->sql.=" history=CONCAT(history,'\nUpdate (discharged): ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."'),";
		}else{
			$this->sql.=" history= history || '\nUpdate (discharged): ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."' ,";
		}
        */
            $this->sql.= "history =".$this->ConcatHistory("Update (discharged): ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n").",";
            #commented by VAN 01-23-08
				/*
				$this->sql.=" modify_id='".$HTTP_SESSION_VARS['sess_user_name']."'
							WHERE encounter_nr=$enr AND type_nr IN ($loc_types) AND date_to ='$dbf_nodate'";
				*/
				#$this->sql.=" modify_id='".$HTTP_SESSION_VARS['sess_user_name']."'
						#	WHERE encounter_nr=$enr AND type_nr IN ($loc_types)";
				#edited by VAN 02-05-08
				$this->sql.=" modify_id='".$HTTP_SESSION_VARS['sess_user_name']."'
							WHERE encounter_nr='$enr'
							/*AND discharge_type_nr=0*/
							AND type_nr IN ($loc_types) ORDER BY nr DESC LIMIT 3";

		#echo "<br>encounter class discharge = ".$this->sql;
		if($this->Transact($this->sql)){
           return true;
        }else{
              //echo $this->sql;
              return FALSE;
         }
		//return $this->Transact($this->sql);
	}
	/**
	* Complete discharge of patient from the hospital or clinic.
	* @access public
	* @param int Encounter number
	* @param int Discharge type number
	* @param string Date of discharge
	* @param string Time of discharge
	* @return boolean
	*/

	function Discharge($enr,$d_type_nr,$date='',$time=''){
		if($this->_discharge($enr,"'1','2','3','4','5','6'",$d_type_nr,$date,$time)){
			if($this->setIsDischarged($enr,$date,$time)){
				return true;
			}else{return FALSE;}
		}else{return FALSE;}
	}
	/**
	* Complete discharge of patient from the department, but patient remains admitted.
	* @access public
	* @param int Encounter number
	* @param int Discharge type number
	* @param string Date of discharge
	* @param string Time of discharge
	* @param boolean Reset encounter flag (reserved)
	* @return boolean
	*/
	function DischargeFromDept($enr,$d_type_nr,$date='',$time='',$rst_enc=1){
		if($this->_discharge($enr,"'1','2','3','4','5','6'",$d_type_nr,$date,$time)){
			return $this->resetCurrentDept($enr);
		}
	}
	/**
	* Complete discharge of patient from the ward but patient remains admitted.
	* @access public
	* @param int Encounter number
	* @param int Discharge type number
	* @param string Date of discharge
	* @param string Time of discharge
	* @return boolean
	*/
	function DischargeFromWard($enr,$d_type_nr,$date='',$time=''){
		if($this->_discharge($enr,"'2','4','5','6'",$d_type_nr,$date,$time)){
			return true;
		}else{return FALSE;}
	}
	/**
	* Complete discharge of patient from the room but patient remains in ward.
	* @access public
	* @param int Encounter number
	* @param int Discharge type number
	* @param string Date of discharge
	* @param string Time of discharge
	* @return boolean
	*/
	function DischargeFromRoom($enr,$d_type_nr,$date='',$time=''){
		if($this->_discharge($enr,"'4','5','6'",$d_type_nr,$date,$time)){
			return true;
		}else{return FALSE;}
	}
	/**
	* Complete discharge of patient from the bed but patient remains in room.
	* @access public
	* @param int Encounter number
	* @param int Discharge type number
	* @param string Date of discharge
	* @param string Time of discharge
	* @return boolean
	*/
	function DischargeFromBed($enr,$d_type_nr,$date='',$time=''){
		if($this->_discharge($enr,"'5'",$d_type_nr,$date,$time)){
			return true;
		}else{return FALSE;}
	}
	/**
	* Saves discharge notes of an encounter.
	* The data must be contained in an associative array and passed to the function by reference.
	* @param array Data to be saved
	* @return boolean
	*/
	function saveDischargeNotesFromArray(&$data_array){
		$this->setTable($this->tb_notes);
		$this->data_array=$data_array;
		$this->setRefArray($this->fld_notes);
		if($this->_insertNotesFromInternalArray(3)){ // 3 = discharge summary note type
			return true;
		}else{
			return FALSE;
		}
	}
	/**
	* Returns the contents of the internal encounter data buffer <var>$encounter</var>
	*
	* @return mixed adodb record object or boolean
	*/
	function getLoadedEncounterData(){
		if($this->is_loaded){
			return $this->encounter;
		}else{return FALSE;}
	}
	/**
	* Gets an adodb object containing the "very basic" encounter's first name, family name, birth date and sex.
	* @param int Encounter number
	* @return mixed adodb record object or boolean
	*/
	function getBasic4Data($enc_nr){
	    global $db;
		if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;
		$this->sql="SELECT p.name_last, p.name_first, p.date_birth, p.sex
							FROM $this->tb_enc AS e,
									 $this->tb_person AS p
							WHERE e.encounter_nr='".$this->enc_nr."'
								AND e.pid=p.pid";
		#echo "sql = ".$this->sql;
		if($this->result=$db->Execute($this->sql)) {
		    if($this->result->RecordCount()) {
				return $this->result;
		    } else { return FALSE;}
		} else { return FALSE;}
	}
	/**
	* Points  the core to the care_encounter_sickconfirm table and fields
	* @access public
	*/
	function useSicknessConfirm(){
		$this->coretable=$this->tb_sickconfirm;
		$this->ref_array=$this->fld_sickconfirm;
	}
	/**
	* Gets a stored sickness confirmation of an encounter.
	*
	* The returned adodb object contains rows of arrays.
	* Each array contains sickness data with following index keys:
	* - all keys as stored in the <var>$fld_sickconfirm</var> array
	* - <b>sig_stamp</b> = Signature stamp of the department
	* - <b>logo_mime_type</b> = Mime type (or extension) of the department's logo image
	*
	*
	* @access public
	* @param int Primary key number of the record
	* @return mixed adodb record object or boolean
	*/
	function getSicknessConfirm($nr=0){
	    global $db;
		if(!$nr) return FALSE;
		$this->sql="SELECT s.*,d.sig_stamp,d.logo_mime_type
							FROM $this->tb_sickconfirm AS s
							LEFT JOIN $this->tb_dept AS d ON s.dept_nr=d.nr
							WHERE s.nr='$nr'";
		//echo $sql;
		if($this->result=$db->Execute($this->sql)) {
		    if($this->rec_count=$this->result->RecordCount()) {
				return $this->result;
		    } else { return FALSE;}
		} else { return FALSE;}
	}
	/**
	* Gets all stored sickness confirmations of an encounter based on its department and encounter numbers.
	* @access public
	* @param int Department number , if department number is zero, all available sickness records will be fetched
	* @param int Encounter number
	* @return mixed adodb record object or boolean
	*/
	function allSicknessConfirm($dept_nr=0,$enc_nr=0){
	    global $db;
		//if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;
		$this->sql="SELECT s.*,d.LD_var,d.name_formal,d.sig_stamp,d.logo_mime_type
						FROM $this->tb_sickconfirm AS s
							LEFT JOIN $this->tb_dept AS d ON s.dept_nr=d.nr
						WHERE s.encounter_nr='".$this->enc_nr."' AND s.status NOT IN ($this->dead_stat)";
		if($dept_nr) $this->sql=$this->sql." AND s.dept_nr='$dept_nr'";
		$this->sql.=' ORDER BY s.date_confirm DESC';

		//echo $this->sql;
		if($this->result=$db->Execute($this->sql)) {
		    if($this->rec_count=$this->result->RecordCount()) {
				return $this->result;
		    } else { return FALSE;}
		} else { return FALSE;}
	}
	/**
	* Saves a sickness confirmation of an encounter.
	* @access public
	* @param array Sickness confirmation data. By reference.
	* @return boolean
	*/
	function saveSicknessConfirm(&$data){
		if(!is_array($data)) return FALSE;
		$this->useSicknessConfirm();
		$data['date_create']=date('Y-m-d H:i:s');
		$this->data_array=$data;
		return $this->insertDataFromInternalArray();
	}
	/**
	* Returns the insurance relevant data of an encounter.
	*
	* The returned adodb object contains rows of arrays.
	* Each array contains data with following index keys:
	* - <b>insurance_nr</b> = the insurance number
	* - <b>name</b> = insurance company's name
	* - <b>sub_area</b> = insurance company's sub area
	*
	* @access public
	* @param  int Encounter number
	* @return mixed adodb record object or boolean
	*/
	function EncounterInsuranceData($enc_nr=0){
	    global $db;
		if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;
		$this->sql="SELECT e.insurance_nr, i.name, i.sub_area FROM $this->tb_enc  AS e
							LEFT JOIN $this->tb_insco AS i ON e.insurance_firm_id=i.firm_id
						WHERE e.encounter_nr='".$this->enc_nr."' AND e.status NOT IN ($this->dead_stat)";
		//echo $sql;
		if($this->result=$db->Execute($this->sql)) {
		    if($this->rec_count=$this->result->RecordCount()) {
				return $this->result;
		    } else { return FALSE;}
		} else { return FALSE;}
	}

	 /**
	 * Marks an appointment's status as "done" and links the encounter number resulting from the appointment.
	 * @access public
	 * @param int Appointment record number
	 * @param int Final type of encounter (1= inpatient, 2= outpatient)
	 * @param int Encounter number that resulted from the appointment
	 * @return boolean
	 */
	function markAppointmentDone($appt_nr=0,$class_nr=0,$enc_nr=0){
	    global $HTTP_SESSION_VARS;
		if(!$appt_nr||!$this->internResolveEncounterNr($enc_nr)) return FALSE;
		$this->sql="UPDATE $this->tb_appt SET  appt_status='done',encounter_nr=$this->enc_nr,encounter_class_nr='$class_nr',
							history=".$this->ConcatHistory("Done ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n").",
							modify_id='".$HTTP_SESSION_VARS['sess_user_name']."',
							modify_time='".date('YmdHis')."'
							WHERE nr='$appt_nr'";
		return $this->Transact();
	}
	/**
	* Gets  basic information of all outpatients.
	*
	* The returned adodb object contains rows of arrays.
	* Each array contains data with following index keys:
	* - <b>encounter_nr</b> = the encounter number
	* - <b>pid</b> = PID number
	* - <b>insurance_class_nr</b> = insurance class number
	* - <b>title</b> = person's title
	* - <b>name_last</b> = person's last or family name
	* - <b>name_first</b> = person's first or given name
	* - <b>date_birth</b> = date of birth
	* - <b>sex</b> = sex
	* - <b>photo_filename</b> = filename of the stored picture ID
	* - <b>time</b> = appointment's scheduled time
	* - <b>urgency</b> = appointment's urgency level
	* - <b>LD_var</b> = variable's name for the foreign language version of insurance class name
	* - <b>insurance_name</b> = default insurance class name
	* - <b>notes</b> = clinic's notes primary key number
	*
	*
	* @access public
	* @param int Department number, if empty all departments will be searched
	* @return mixed adodb record object or boolean
	*/
	function OutPatientsBasic($dept_nr=0){
		global $db;
		//$db->debug=1;
		if($dept_nr) $cond="e.current_dept_nr='$dept_nr' AND";
			else $cond='';
			//$cond='';
		$this->sql="SELECT e.encounter_nr,e.pid,e.insurance_class_nr,p.title,p.name_last,p.name_first,p.date_birth,p.sex, p.photo_filename,
									a.date, a.time,a.urgency, i.LD_var AS \"LD_var\",i.name AS insurance_name,
									n.nr AS notes
							FROM $this->tb_enc AS e
									LEFT JOIN $this->tb_person AS p ON e.pid=p.pid
									LEFT JOIN $this->tb_appt AS a ON e.encounter_nr=a.encounter_nr
									LEFT JOIN $this->tb_ic AS i ON e.insurance_class_nr=i.class_nr
									LEFT JOIN $this->tb_notes as n ON (e.encounter_nr=n.encounter_nr AND n.type_nr=6)
							WHERE $cond e.encounter_class_nr='2' AND
									e.is_discharged=0  AND
									e.in_dept<>0 AND e.status NOT IN ($this->dead_stat)
							ORDER BY e.encounter_nr";
							/*							GROUP BY e.encounter_nr,e.pid,e.insurance_class_nr,p.title,p.name_last,p.name_first,p.date_birth,p.sex,
							p.photo_filename,a.date, a.time,a.urgency,i.LD_var,i.name, n.nr*/

        if($this->res['opb']=$db->Execute($this->sql)) {
            if($this->rec_count=$this->res['opb']->RecordCount()) {
				 return $this->res['opb'];
			} else { return FALSE; }
		} else { return FALSE; }
	}
	/**
	* createWaitingOutpatientList() creates a list of outpatients waiting to be admitted in the clinic
	*
	* The returned adodb object contains rows of arrays.
	* Each array contains data with following index keys:
	* - <b>encounter_nr</b> = the encounter number
	* - <b>encounter_class_nr</b> = the encounter class number (1 = inpatient, 2 = outpatient)
	* - <b>current_dept_nr</b> = the current department number
	* - <b>pid</b> = PID number
	* - <b>name_last</b> = person's last or family name
	* - <b>name_first</b> = person's first or given name
	* - <b>date_birth</b> = date of birth
	* - <b>sex</b> = sex
	* - <b>dept_nr</b> = current department number
	* - <b>name_short</b> = short department name
	* - <b>LD_var</b> = variable's name for the foreign language version of department name
	*
	*
	* @access public
	* @param int Department number, if empty all departments will be searched
	* @return mixed adodb record object or boolean
	*/
	function createWaitingOutpatientList($dept_nr=0){
		global $db;
		//$db->debug=1;
		if($dept_nr) $cond="AND current_dept_nr='$dept_nr'";
			else $cond='';
		$this->sql="SELECT e.encounter_nr, e.encounter_class_nr, e.current_dept_nr,
									p.pid, p.name_last, p.name_first, p.date_birth, p.sex,
									d.nr AS dept_nr, d.name_short, d.LD_var AS \"dept_LDvar\"
				FROM $this->tb_enc AS e
					LEFT JOIN $this->tb_person AS p ON e.pid=p.pid
					LEFT JOIN $this->tb_dept AS d ON e.current_dept_nr=d.nr
				WHERE e.encounter_class_nr='2' AND e.is_discharged=0 $cond
							AND  e.in_dept=0 AND e.encounter_status <> 'cancelled'
							AND e.status NOT IN ($this->dead_stat)
				ORDER BY p.name_last";
		//echo $sql;
	    if ($this->res['cwol']=$db->Execute($this->sql)){
		   	if ($this->rec_count=$this->res['cwol']->RecordCount()){
				return $this->res['cwol'];
			}else{return FALSE;}
		}else{return FALSE;}
	}
	/**
	* Returns the status information  and current locations of an encounter.
	*
	* The returned adodb object contains rows of arrays.
	* Each array contains data with following index keys:
	* - <b>encounter_status</b> =  encounter status
	* - <b>current_room_nr</b> =  current room number
	* - <b>current_ward_nr</b> =  current ward number
	* - <b>current_dept_nr</b> =  current department number
	* - <b>in_dept</b> = "in department" status
	* - <b>in_ward</b> = "in ward" status
	* - <b>is_discharged</b> = discharge status
	* - <b>status</b> = record's technical status
	*
	*
	* @access public
	* @param int Encounter number
	* @return mixed  adodb record object or boolean
	*/
	function AllStatus($enc_nr=0){
	    global $db;
		if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;
		$this->sql="SELECT encounter_status,current_ward_nr,current_room_nr,in_ward,current_dept_nr,in_dept,is_discharged,status
						FROM $this->tb_enc	WHERE encounter_nr='".$this->enc_nr."' AND status NOT IN ($this->dead_stat)";
		#echo $this->sql;
		if($this->res['ast']=$db->Execute($this->sql)) {
		    if($this->rec_count=$this->res['ast']->RecordCount()) {
				return $this->res['ast'];
		    } else { return FALSE;}
		} else { return FALSE;}
	}
	/**
	* Gets a particular encounter item based on its encounter number key.
	*
	* For details on field names of items that can be fetched, see the <var>$tab_fields</var> array.
	* @access private
	* @param string Field name of the item to be fetched
	* @param int encounter number
	* @return mixed string, integer, or boolean
	*/
	function getValue($item,$enr='',$person=FALSE) {
	    global $db;
	    if($this->is_loaded) {
		    if(isset($this->encounter[$item])) return $this->encounter[$item];
		        else  return false;
		} else {
			if($this->internResolveEncounterNr($enr)){
				if($person){
					$this->sql="SELECT p.$item FROM $this->tb_enc AS e, $tb_person AS p WHERE e.encounter_nr='".$this->enc_nr."' AND e.pid=p.pid";
				}else{
					$this->sql="SELECT $item FROM $this->tb_enc WHERE encounter_nr='".$this->enc_nr."'";
				}
			//return $this->sql;
		 		if($this->result=$db->Execute($this->sql)) {
					if($this->result->RecordCount()) {
						$row=$this->result->FetchRow();
						return $row[$item];
					} else { return false; }
				} else { return false; }
			}else{ return false; }
		}
	}
	/**
	* Private search function, usually called by another method.
	*
	* The resulting count can be fetched with the <var>LastRecordCount()</var> method.
	*
	* The returned adodb object contains rows of arrays.
	* Each array contains "basic" admission data with following index keys:
	* - <b>encounter_nr</b> = encounter number
	* - <b>encounter_class_nr</b> = encounter class number: 1 = inpatient, 2 = outpatient
	* - <b>pid</b> = the patient's PID number
	* - <b>name_last</b> = last or family name
	* - <b>name_first</b> = first or given name
	* - <b>date_birth</b> = date of birth in yyyy-mm-dd format
	* - <b>addr_zip</b> = zip code
	* - <b>blood_group</b> = patient's blood group
	*
	*
	* @param string Search keyword
	* @param int Encounter class number. default = 0
	* @param string Optional addtion to WHERE clause like e.g. for sorting
	* @param boolean  Flag whether the select query is limited or not, default FALSE = unlimited
	* @param int Maximum number or rows returned in case of limited select, default = 30 rows
	* @param int Start index offset in case of limited select, default 0 = start
	* @return mixed adodb record object or boolean
	*   created/modified burn: Oct. 2, 2006
	*/
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
	function _searchAdmissionBasicInfoPending($key,$enc_class=0,$add_opt='',$limit=FALSE,$len=30,$so=0){
		global $db,$sql_LIKE;

		if(is_numeric($key)){
			#$key=(int)$key;
			$whereSQL=" AND r.encounter_nr = '$key' ";
		}elseif($key=='%'||$key=='*'){
			$whereSQL="";
		}elseif(substr($key, 0, 10)=="r.dept_nr="){
#		substr_compare ( $key, string str, int offset [, int length [, bool case_sensitivity]])
			$whereSQL="AND $key";
		}else{
			$whereSQL=" AND (e.encounter_nr $sql_LIKE '$key%'
						OR p.pid $sql_LIKE '$key%'
						OR p.name_last $sql_LIKE '$key%'
						OR p.name_first $sql_LIKE '$key%'
						OR p.date_birth $sql_LIKE '$key%')";
		}

				$my_table = "care_test_request_radio";
				$tb_request_sked='seg_test_request_sked';
				$this->sql="SELECT r.batch_nr, r.encounter_nr, r.send_date, r.dept_nr AS sub_dept_nr, r.status, r.create_time,
						dept.id AS sub_dept_id, dept.name_formal AS  sub_dept_name,
						p.name_last, p.name_first, p.date_birth, p.sex, p.pid,
						s.personell_nr, s.trace
					FROM $this->tb_enc AS e, $this->tb_person AS p,
								".$my_table." AS r
								LEFT JOIN ".$tb_request_sked." AS s ON r.batch_nr=s.batch_nr
								LEFT JOIN ".$this->tb_dept." AS dept ON dept.nr = r.dept_nr
					WHERE e.pid=p.pid	AND e.is_discharged IN ('',0)
						AND e.status NOT IN ($this->dead_stat)
						AND r.status<>'done' AND e.encounter_nr=r.encounter_nr
						$whereSQL $add_opt ";
#		            WHERE status='pending' OR status='received' ORDER BY  send_date ASC";
          # echo "_searchAdmissionBasicInfoPending : this->sql = <br> $this->sql <br> \n ";
          # echo "_searchAdmissionBasicInfoPending : key = $key <br> \n ";


/*		//if(empty($key)) return FALSE;
		$this->sql="SELECT e.encounter_nr, e.encounter_class_nr, p.pid, p.name_last, p.name_first, p.date_birth, p.addr_zip, p.sex,p.blood_group
				FROM $this->tb_enc AS e LEFT JOIN $this->tb_person AS p ON e.pid=p.pid";

		if(is_numeric($key)){
			$key=(int)$key;
			$this->sql.=" WHERE e.encounter_nr = $key AND  e.is_discharged IN ('',0)".$add_opt;
		}elseif($key=='%'||$key=='*'){
			$this->sql.=" WHERE e.is_discharged IN ('',0) AND e.status NOT IN ($this->dead_stat) ".$add_opt;
		}else{
			$this->sql.=" WHERE (e.encounter_nr $sql_LIKE '$key%'
						OR p.pid $sql_LIKE '$key%'
						OR p.name_last $sql_LIKE '$key%'
						OR p.name_first $sql_LIKE '$key%'
						OR p.date_birth $sql_LIKE '$key%')";
			if($enc_class) $this->sql.="	AND e.encounter_class_nr=$enc_class";
			$this->sql.="  AND  e.is_discharged IN ('',0) AND e.status NOT IN ($this->dead_stat) ".$add_opt;
		}
*/
		if($limit){
	    	$this->res['sabi']=$db->SelectLimit($this->sql,$len,$so);
		}else{
	    	$this->res['sabi']=$db->Execute($this->sql);
		}
	    if ($this->res['sabi']){
		   	if ($this->record_count=$this->res['sabi']->RecordCount()) {
				$this->rec_count=$this->record_count; # workaround
				#echo "_searchAdmissionBasicInfoPending :  TRUE <br>";
				return $this->res['sabi'];
			} else{
				#echo "_searchAdmissionBasicInfoPending : FALSE 01 <br>";
			return FALSE;}
		}else{
			#echo "_searchAdmissionBasicInfoPending : FALSE 02 <br>";
		return FALSE;}
	}# end of function

	#----------added by VAN 09-03-07------
	#INSURANCE
	function getPersonInsuranceItems($enc_nr) {
    	global $db;
		#$refno = $db->qstr($refno);
		$this->sql="SELECT i.*, e.pid, f.firm_id, f.name
						FROM seg_encounter_insurance AS i
						LEFT JOIN care_encounter AS e
						ON e.encounter_nr = i.encounter_nr
						INNER JOIN care_insurance_firm AS f
						ON f.hcare_id = i.hcare_id
						WHERE i.encounter_nr = '$enc_nr'
						ORDER BY f.firm_id";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	#-------------------------------------

	#-----------added by VAN 02-01-08
	function getPatientLocation($encounter_nr=0, $ward_nr){
	    global $db;
		$this->sql="SELECT *
						FROM $this->tb_location
						WHERE status NOT IN ('discharged')
						AND type_nr=5 AND group_nr='$ward_nr'
						AND encounter_nr = '$encounter_nr'";

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

	#------------------------------------

	#---------------added by VAN 02-19-08
	function getPatientEncounterResult($encounter_nr=0){
	global $db;

		$this->sql ="SELECT * FROM $this->tb_enc_result
		             WHERE encounter_nr='$encounter_nr'
						 ORDER BY modify_time DESC LIMIT 1";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}
	#--------------------------------------

	#added by VAN 04-28-08
	function getMedicoCases(){
	global $db;

		$this->sql ="SELECT * FROM seg_medico_cases ORDER BY medico_cases";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count= $this->result->RecordCount())
				return $this->result;
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}
	#--------------------------

	#---------added by VAN 06-13-08
	function getTriageCategory(){
		global $db;

		$this->sql ="SELECT * FROM seg_triage_category ORDER BY category_id";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result;
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getTriageCategoryInfo($category){
		global $db;

		$this->sql ="SELECT * FROM seg_triage_category WHERE category_id='$category'";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}
	#-------------------------


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
	function searchLimitEncounterBasicInfoPending($key,$len,$so,$sortitem='',$order='ASC'){
		if(!empty($sortitem)){
			$option=" ORDER BY $sortitem $order";
		}
		return $this->_searchAdmissionBasicInfoPending($key,0,$option,TRUE,$len,$so); // 0 = all kinds of admission
	}# end of function searchLimitEncounterBasicInfoPending

	#added by VAN 04-26-08
	function getPatientOPDORNoforADay($pid=0, $name=''){
		global $db;

		if (($pid)&&(is_numeric($pid)))
			$sql_cond = " p.pid='".$pid."' ";
		else
			$sql_cond = " p.or_name LIKE '".$name."' ";

		$this->sql ="SELECT p.*, pr.service_code, pr.amount_due AS amount_paid,
					 SUBSTRING(pr.service_code,1,2) AS service_code,
					 so.service_code AS service_code2, so.name AS service_name
					 FROM seg_pay AS p
								INNER JOIN seg_pay_request AS pr ON p.or_no=pr.or_no AND pr.ref_source = 'OTHER'
					 INNER JOIN seg_other_services AS so ON so.service_code=SUBSTRING(pr.service_code,1,length(pr.service_code)-1)
								WHERE  ".$sql_cond."
								AND DATE(p.or_date)=DATE(NOW())
								AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')
					 AND so.account_type='33'";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	#added by Jarel 07-25-13
	function getPatientOPDORNoforADaySocial($pid=0, $name=''){
		global $db;

		if (($pid)&&(is_numeric($pid)))
			$sql_cond = " p.pid='".$pid."' ";
		else
			$sql_cond = " p.or_name LIKE '".$name."' ";

		$this->sql ="SELECT p.*, pr.service_code, pr.amount_due AS amount_paid,
					 SUBSTRING(pr.service_code,1,2) AS service_code,
					 so.service_code AS service_code2, so.name AS service_name
					 FROM seg_pay AS p
								INNER JOIN seg_pay_request AS pr ON p.or_no=pr.or_no AND pr.ref_source = 'MISC'
					 INNER JOIN seg_other_services AS so ON so.service_code='00002338'
								WHERE  ".$sql_cond."
								AND DATE(p.or_date)=DATE(NOW())
								AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	#added by VAN 04-29-08
	function addMedicoCasesEncounter($enc_nr='',$pid='',$cases){
		global $db;

		#$cases=addcslashes($cases,$charlist);
		#print_r($cases);
		$this->sql="INSERT INTO seg_encounter_medico
			            (encounter_nr,pid, medico_cases, description)
			         VALUES('".$enc_nr."','$pid',?, ?)";
		#echo "sql = ".$this->sql."<br>";

		if ($db->Execute($this->sql,$cases)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}else{
			$this->error=$db->ErrorMsg();
		}
		if ($ret)	return TRUE;
		else return FALSE;
	}

	function deleteMedicoCasesEncounter($enc_nr='',$pid='') {
		$this->sql="DELETE FROM seg_encounter_medico
						WHERE encounter_nr='".$enc_nr."' AND pid='$pid'";

      return $this->Transact();
	}

	function setMedico($medico=0,$enc_nr=''){
	  $this->sql="UPDATE $this->tb_enc SET is_medico= $medico WHERE encounter_nr='$enc_nr'";

      return $this->Transact($this->sql);
	}

	function updateDOA($enc_nr='', $DOA=0, $DOA_reason=''){
		$this->sql="UPDATE $this->tb_enc SET
		            is_DOA= '$DOA',
						is_DOA_reason = '$DOA_reason'
						WHERE encounter_nr='$enc_nr'";

    	return $this->Transact($this->sql);
	}

	#added by genz
	function updateAdmittingDiagnosis($enc_nr,$aux_notes){
		$this->sql = "UPDATE care_encounter SET er_opd_diagnosis = '$aux_notes' WHERE encounter_nr = '$enc_nr'";
		return $this->Transact($this->sql);
	}

		#added by VAN
		function MayGoHome($enc_nr='', $mgh_date='0000-00-00 00:00:00', $is_mgh=1){
				global $db;

				// Added history log
				// @author Alvin Quinones
				$logEntry = sprintf("MGH=%s %s [%s]\n", $is_mgh, date('Y-m-d H:i:s'), $_SESSION['sess_temp_userid']);
				$this->sql="UPDATE $this->tb_enc SET\n".
					"history=CONCAT(ISNULL(history, ''), " . $db->qstr($logEntry) . "),\n".
					"is_maygohome=".$db->qstr($is_mgh).",\n".
					"mgh_setdte=".$db->qstr($mgh_date)."\n".
					"WHERE encounter_nr=".$db->qstr($enc_nr);

				return $this->Transact($this->sql);
		}


	#added by VAN 06-20-08
	function updateConfidential($enc_nr='', $confidentiality=0){
		$this->sql="UPDATE $this->tb_enc SET
		            is_confidential= '$confidentiality'
					WHERE encounter_nr='$enc_nr'";

    	return $this->Transact($this->sql);
	}
	#---------------------------

	function updateWardArea($enc_nr='', $area=''){
		$this->sql="UPDATE $this->tb_enc SET
		            area= '$area'
					WHERE encounter_nr='$enc_nr'";

    	return $this->Transact($this->sql);
	}

	#------added by VAN 06-12-08
	function updateIncident($enc_nr='', $POI='', $TOI='00:00:00', $DOI='0000-00-00', $msg){
	global $HTTP_SESSION_VARS;
		#$history = $this->ConcatHistory($msg." : ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		$this->sql="UPDATE $this->tb_enc SET
		            	POI= '$POI',
						TOI = '$TOI',
						DOI = '$DOI',
						history =".$this->ConcatHistory("$msg : ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n").",
						modify_id='".$HTTP_SESSION_VARS['sess_user_name']."',
						modify_time='".date('YmdHis')."'
					WHERE encounter_nr='$enc_nr'";

    	return $this->Transact($this->sql);
	}
	#-------------------------

	function getEncounterMedicoCases($enc_nr='',$pid=''){
		global $db;

		#$this->sql ="SELECT * FROM seg_encounter_medico
		#            WHERE encounter_nr='$this->enc_nr' AND pid='$pid'";
		$this->sql ="SELECT em.*, mc.medico_cases
					 FROM seg_encounter_medico AS em
					 INNER JOIN seg_medico_cases AS mc ON mc.code=em.medico_cases
					 WHERE encounter_nr='$enc_nr' AND pid='$pid'";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result;
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getEncounterByMedicoCases($enc_nr='',$pid='', $medico_case){
		global $db;
		/*
		$this->sql ="SELECT * FROM seg_encounter_medico
		             WHERE encounter_nr='$this->enc_nr' AND pid='$pid'
						 AND medico_cases='$medico_case'";
		*/
		$this->sql ="SELECT * FROM seg_encounter_medico
		             WHERE encounter_nr='$enc_nr' AND pid='$pid'
						 AND medico_cases='$medico_case'";
		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	#-------------------------

	#added by VAN 05-02-08
	function getPatientOPDORNoforAnEncounter($pid, $consultation_date){
		global $db;

		#edited by VAN 06-27-08
		$this->sql ="SELECT p.*, pr.service_code, pr.amount_due AS amount_paid,
						 SUBSTRING(pr.service_code,1,2) AS service_code, so.service_code AS service_code2,
						 so.name AS service_name
						 FROM seg_pay AS p
						 INNER JOIN seg_pay_request AS pr ON p.or_no=pr.or_no AND pr.ref_source = 'OTHER'
						 INNER JOIN seg_other_services AS so ON so.service_code=SUBSTRING(pr.service_code,1,length(pr.service_code)-1)
						 WHERE (p.pid='$pid')
						 AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')
						 AND DATE(p.or_date)=DATE('$consultation_date')
						 AND so.account_type='33'";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	#added by VAN 06-21-08

	function getAllEncounterByPid($pid=''){
	global $db;

		$this->sql ="SELECT enc.*
					 FROM care_encounter AS enc
					 WHERE enc.pid='$pid'
					 ORDER BY encounter_date DESC";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result;
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function countSearchEncounterList($pid='', $searchkey='',$maxcount=100,$offset=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		 $sql_cond = "";
		 if (!empty($keyword)){
			if (stristr($searchkey,"/"))
					$sql_cond = " AND DATE(encounter_date) = '".DATE('Y-m-d',strtotime($keyword))."' ";
			else
					$sql_cond = " AND encounter_nr = '$keyword' ";

		}

		$this->sql = "SELECT enc.*, dept.id , dept.name_formal AS dept_name,
					  dept.name_short AS dept_name2,
					  w.ward_id, w.name AS ward_name
					  FROM care_encounter AS enc
						INNER JOIN care_department AS dept ON dept.nr=enc.current_dept_nr
					  LEFT JOIN care_ward AS w ON w.nr=enc.current_ward_nr
						WHERE enc.pid = '$pid'
						$sql_cond
						AND enc.status NOT IN ('deleted','hidden','inactive','void')
					  ORDER BY encounter_date DESC";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function SearchEncounterList($pid='', $searchkey='',$maxcount=100,$offset=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$sql_cond = "";
		if (!empty($keyword)){
			if (stristr($searchkey,"/"))
					$sql_cond = " AND DATE(encounter_date) = '".DATE('Y-m-d',strtotime($keyword))."' ";
			else
					$sql_cond = " AND encounter_nr = '$keyword' ";

		}

		$this->sql = "SELECT enc.*, dept.id , dept.name_formal AS dept_name,
					  dept.name_short AS dept_name2,
					  w.ward_id, w.name AS ward_name
					  FROM care_encounter AS enc
						INNER JOIN care_department AS dept ON dept.nr=enc.current_dept_nr
					  LEFT JOIN care_ward AS w ON w.nr=enc.current_ward_nr
						WHERE enc.pid = '$pid'
						$sql_cond
						AND enc.status NOT IN ('deleted','hidden','inactive','void')
					  ORDER BY encounter_date DESC";

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

	#--------------------------------

	/* function SearchAdmissionList
    *  @author Raissa 12/15/08
    *  @access public
    *  @internal Function for retrieving the list of uncancelled Admission History for a patient
    *  @param String pid, searchkey
    *  @param Integer maxcount, offset
    *  @return Array resultset
    *  @return Boolean false to indicate failure in the query
    */
    function SearchAdmissionList($pid='', $searchkey='',$maxcount=100,$offset=0){
        global $db, $sql_LIKE, $root_path, $date_format;
        if(empty($maxcount)) $maxcount=100;
        if(empty($offset)) $offset=0;

        # convert * and ? to % and &
        $searchkey=strtr($searchkey,'*?','%_');
        $searchkey=trim($searchkey);
        #$suchwort=$searchkey;
        $searchkey = str_replace("^","'",$searchkey);
        $keyword=addslashes($searchkey);

        $this->sql = "SELECT sr.referral_nr, sr.encounter_nr, sr.referrer_diagnosis, sr.referrer_dr,
                      sr.referrer_dept, sr.referrer_notes, sr.is_referral, sr.is_dept,
                      sr.create_id, sr.create_time, sr.referral_date, sr.status from seg_referral as sr
                      LEFT JOIN care_encounter as ce ON ce.encounter_nr = sr.encounter_nr
											WHERE ce.pid= '$pid'
                      AND (sr.create_time LIKE '%".$keyword."%'
                      OR sr.referral_nr LIKE '%".$keyword."%' )
                      AND sr.status!='deleted'
                      ORDER BY ce.encounter_date DESC";

        if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
            if($this->rec_count=$this->res['ssl']->RecordCount()) {
                return $this->res['ssl'];
            }else{return false;}
        }else{return false;}
    }
    /* function countSearchAdmissionList
    *  @author Raissa 12/15/08
    *  @access public
    *  @internal Function for counting the list of uncancelled Admission History for a patient
    *  @param String pid, searchkey
    *  @param Integer maxcount, offset
    *  @return Array resultset
    *  @return Boolean false to indicate failure in the query
    */
    function countSearchAdmissionList($pid='', $searchkey='',$maxcount=100,$offset=0) {
        global $db, $sql_LIKE, $root_path, $date_format;
        if(empty($maxcount)) $maxcount=100;
        if(empty($offset)) $offset=0;

        # convert * and ? to % and &
        $searchkey=strtr($searchkey,'*?','%_');
        $searchkey=trim($searchkey);
        #$suchwort=$searchkey;
        $searchkey = str_replace("^","'",$searchkey);
        $keyword=addslashes($searchkey);

        $this->sql = "SELECT sr.referral_nr, sr.encounter_nr, sr.referrer_diagnosis, sr.referrer_dr,
                      sr.referrer_dept, sr.referrer_notes, sr.is_referral, sr.is_dept,
                      sr.create_id, sr.create_time, sr.referral_date, sr.status from seg_referral as sr
                      LEFT JOIN care_encounter as ce ON ce.encounter_nr = sr.encounter_nr
											WHERE ce.pid= '$pid'
                      AND (sr.create_time LIKE '%".$keyword."%'
                      OR sr.referral_nr LIKE '%".$keyword."%' )
                      AND sr.status!='deleted'
                      ORDER BY ce.encounter_date DESC";

        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()) {
                return $this->result;
            }
            else{return FALSE;}
        }else{return FALSE;}
    }
    /* function countSearchAllAdmissionList
    *  @author Raissa 12/15/08
    *  @access public
    *  @internal Function for counting all Admission History for a patient
    *  @param String pid, searchkey
    *  @param Integer maxcount, offset
    *  @return Array resultset
    *  @return Boolean false to indicate failure in the query
    */
    function countSearchAllAdmissionList($pid='', $searchkey='',$maxcount=100,$offset=0) {
        global $db, $sql_LIKE, $root_path, $date_format;
        if(empty($maxcount)) $maxcount=100;
        if(empty($offset)) $offset=0;

        # convert * and ? to % and &
        $searchkey=strtr($searchkey,'*?','%_');
        $searchkey=trim($searchkey);
        #$suchwort=$searchkey;
        $searchkey = str_replace("^","'",$searchkey);
        $keyword=addslashes($searchkey);

        $this->sql = "SELECT sr.referral_nr, sr.encounter_nr, sr.referrer_diagnosis, sr.referrer_dr,
                      sr.referrer_dept, sr.referrer_notes, sr.is_referral, sr.is_dept,
                      sr.create_id, sr.create_time, sr.referral_date, sr.status from seg_referral as sr
                      LEFT JOIN care_encounter as ce ON ce.encounter_nr = sr.encounter_nr
											WHERE ce.pid= '$pid'
                      AND (sr.create_time LIKE '%".$keyword."%'
                      OR sr.referral_nr LIKE '%".$keyword."%' )
                      ORDER BY ce.encounter_date DESC";

        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()) {
                return $this->result;
            }
            else{return FALSE;}
        }else{return FALSE;}
    }
    /* function addReferral
    *  @author Raissa 01/05/09
    *  @access public
    *  @internal Function for adding a transfer or referral admission to database
    *  @param String encounter_nr, refer, date, referral_nr, doctor, dept, diagnosis, notes, creator
    *  @return Boolean returns a success or fail in the query
    */
    function addReferral($encounter_nr, $refer, $date, $referral_nr, $doctor, $dept, $diagnosis, $notes, $creator, $is_dept) {
        global $db;
        $today = date('Y-m-d H:i:s');
        $date = date("Y-m-d",strtotime($date));
        $history = "Added: " .$today;
        $this->sql = "INSERT INTO seg_referral VALUES(
                      $referral_nr, $encounter_nr, '".$diagnosis."', '".$doctor."', '".$dept."', '".$notes."', '".$history."', '".$creator."', '".$today."', '".$creator."', '".$today."', $refer, '".$date."', 'ok', '', $is_dept
                      );";

        //echo "sql = ".$this->sql;
        if ($this->result=$db->Execute($this->sql)) {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    /* function editReferral
    *  @author Raissa 01/05/09
    *  @access public
    *  @internal Function for editing a transfer or referral admission
    *  @param String encounter_nr, refer, date, referral_nr, doctor, dept, diagnosis, notes, creator
    *  @return Boolean returns a success or fail in the query
    */
    function editReferral($encounter_nr, $refer, $date, $referral_nr, $doctor, $dept, $diagnosis, $notes, $creator) {
        global $db;
        $this->sql = "SELECT history FROM seg_referral WHERE referral_nr='".$referral_nr."';";
        if ($this->result=$db->Execute($this->sql)) {
            $a = $this->result->FetchRow();
            $history = $a["history"];
        }
        $today = date('Y-m-d H:i:s');
        $date = date("Y-m-d",strtotime($date));
        $history = $history ."\nUpdated: " .$today;
        $this->sql = "UPDATE seg_referral SET
                       referrer_diagnosis = '".$diagnosis."',
                       referrer_dr = '".$doctor."',
                       referrer_dept = '".$dept."',
                       referrer_notes = '".$notes."',
                       history = '".$history."',
                       modify_id = '".$creator."',
                       modify_time = '".$today."',
                       is_referral = ".$refer.",
                       referral_date = '".$date."'
                      WHERE referral_nr='".$referral_nr."';";

        //echo "sql = ".$this->sql;
        if ($this->result=$db->Execute($this->sql)) {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    #Added by Jarel 07/27/13 To save patient referral
    function saveReferral(&$data){
    	global $db, $HTTP_SESSION_VARS;

    	extract($data);

		$index = "referral_nr, encounter_nr, referrer_dr, referrer_dept, is_referral, is_dept, 
					reason_referral_nr, history, create_id, create_time, referral_date";
		
		$values = "'$referral_nr', '$encounter_nr', '$referrer_dr', '$referrer_dept', '1', '1', 
				   '$reason_referral_nr', CONCAT('Create: ',NOW(),' [$userid]\\n'),
				   '$userid', NOW(), NOW()";

		$this->sql="INSERT INTO seg_referral ($index) VALUES ($values)";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;
	}

    /* function BillingDone
    *  @author Raissa 01/06/09
    *  @access public
    *  @internal Function for checking if the encounter has been billed or not
    *  @param String encounter_nr
    *  @return Boolean to indicate whether the encounter has been billed or not
    */
    function BillingDone($enr='') {
        global $db;
        $this->sql="SELECT * from seg_billing_encounter WHERE encounter_nr='$enr';";
        if($this->result=$db->Execute($this->sql)){
            if($this->result->RecordCount()){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }
    /* function cancelReferral
    *  @author Raissa 01/10/09
    *  @access public
    *  @internal Function for cancelling a transfer or referral admission
    *  @param String referral_nr, reason
    *  @return Boolean returns a success or fail in the query
    */
    function cancelReferral($referral_nr, $reason, $creator) {
        global $db;
        $this->sql = "SELECT history FROM seg_referral WHERE referral_nr='".$referral_nr."';";
        if ($this->result=$db->Execute($this->sql)) {
            $a = $this->result->FetchRow();
            $history = $a["history"];
        }
        $today = date('Y-m-d H:i:s');
        $history = $history ."\nDeleted: " .$today;
        $this->sql = "UPDATE seg_referral SET
                       status = 'deleted',
                       cancel_reason = '".$reason."',
                       history = '".$history."',
                       modify_id = '".$creator."',
                       modify_time = '".$today."'
                      WHERE referral_nr='".$referral_nr."';";

        //echo "sql = ".$this->sql;
        if ($this->result=$db->Execute($this->sql)) {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    /* function getReferrals
    *  @author Raissa 01/12/09
    *  @access public
    *  @internal Function for getting the referrals for a given encounter number
    *  @param String encounter_nr
    *  @return Boolean returns result set if success or boolean false if query fails
    */
    function getReferrals($encounter_nr) {
        global $db;
        $this->sql = "SELECT * FROM seg_referral WHERE encounter_nr='".$encounter_nr."' AND status!='deleted';";

        //echo "sql = ".$this->sql;
        if ($this->result=$db->Execute($this->sql)) {
            return $this->result;
        }
        else{
            return FALSE;
        }
    }
    /* function getReferralInfo
    *  @author Raissa 01/24/09
    *  @access public
    *  @internal Function for getting the details of a referral, given referral number
    *  @param String referral_nr
    *  @return Boolean returns result set if success or boolean false if query fails
    */
    function getReferralInfo($referral_nr) {
        global $db;
        $this->sql = "SELECT * FROM seg_referral WHERE referral_nr='".$referral_nr."' AND status!='deleted';";

        //echo "sql = ".$this->sql;
        if ($this->result=$db->Execute($this->sql)) {
            return $this->result->FetchRow();
        }
        else{
            return FALSE;
        }
    }
    /* function SearchEncRefMedCertList
    *  @author Raissa 1/22/08
    *  @access public
    *  @internal Function for retrieving the list of admissions and referrals with or without medical certificate
    *  @param String pid, searchkey
    *  @param Boolean med_cert
    *  @param Integer maxcount, offset
    *  @return Array resultset
    *  @return Boolean false to indicate failure in the query
    */
		#edited by VAN 07-12-2010
		function SearchEncRefMedCertList($pid='', $searchkey='', $med_cert=true,$maxcount=100,$offset=0, $count_sql=0){
        global $db, $sql_LIKE, $root_path, $date_format;
        if(empty($maxcount)) $maxcount=100;
        if(empty($offset)) $offset=0;
        # convert * and ? to % and &
        $searchkey=strtr($searchkey,'*?','%_');
        $searchkey=trim($searchkey);
        #$suchwort=$searchkey;
        $searchkey = str_replace("^","'",$searchkey);
        $keyword=addslashes($searchkey);
        if($med_cert=='true')
        {
						//$this->sql = "(SELECT m.create_dt, m.encounter_nr, m.referral_nr, m.cert_nr,
//													c.encounter_date as date_admit, d.name_formal as dept, m.create_id as prepared_by, fn_get_personell_name(m.dr_nr)  as dr
//													FROM seg_cert_med as m
//													LEFT JOIN care_encounter AS c ON c.encounter_nr = m.encounter_nr
//													LEFT JOIN care_department AS d ON d.nr = c.current_dept_nr
//													WHERE c.pid ='".$pid."'
//													AND (m.referral_nr='' OR ISNULL(m.referral_nr))
//													AND (m.encounter_nr LIKE '%".$searchkey."%'
//													OR c.encounter_date LIKE '%".$searchkey."%'
//													OR m.create_dt LIKE '%".$searchkey."%') )
//													UNION (SELECT m.create_dt, r.encounter_nr, r.referral_nr, m.cert_nr, r.referral_date as date_admit, d.name_formal as dept,
//													m.create_id as prepared_by, fn_get_personell_name(m.dr_nr) as dr from seg_referral as r LEFT JOIN seg_cert_med as m ON r.encounter_nr=m.encounter_nr
//													LEFT JOIN care_department as d ON d.nr = r.referrer_dept
//													LEFT JOIN care_encounter as c ON c.encounter_nr=r.encounter_nr
//													WHERE r.referral_nr = m.referral_nr AND c.pid='".$pid."'
//													AND (r.encounter_nr LIKE '%".$searchkey."%'
//													OR r.referral_nr LIKE '%".$searchkey."%'
//													OR r.referral_date LIKE '%".$searchkey."%'
//													OR m.create_dt LIKE '%".$searchkey."%'));";

						#edited by VAN 07-13-2010
						if ($searchkey)
							$sql_cond = " AND (m.encounter_nr='$searchkey' OR r.referral_nr='$searchkey') ";
						#edited by VAN 02-25-2011
						$this->sql = "SELECT SQL_CALC_FOUND_ROWS m.create_dt, m.encounter_nr, m.referral_nr, m.cert_nr,
														c.encounter_date as date_admit,
														IF (m.referral_nr,fn_get_department_name(r.referrer_dept),fn_get_department_name(c.current_dept_nr)) as dept, m.create_id as prepared_by, fn_get_personell_name(m.dr_nr)  as dr
                          FROM seg_cert_med as m
                          LEFT JOIN care_encounter AS c ON c.encounter_nr = m.encounter_nr
													LEFT JOIN seg_referral as r ON r.encounter_nr=m.encounter_nr AND r.referral_nr=m.referral_nr
													WHERE c.pid ='$pid' $sql_cond
													ORDER BY c.encounter_nr DESC, r.referral_nr";
        }
        else
        {
						//$this->sql = "(SELECT c.encounter_nr, '' as referral_nr, d.name_formal as dept, c.encounter_type, c.encounter_date as admit_date, c.pid FROM care_encounter as c
//														LEFT JOIN care_department as d ON c.current_dept_nr=d.nr
//														WHERE c.pid='".$pid."'
//														AND (c.encounter_nr LIKE '%".$searchkey."%'
//														OR c.encounter_date LIKE '%".$searchkey."%'))
//														UNION (SELECT r.encounter_nr, r.referral_nr, d.name_formal as dept, c.encounter_type, r.referral_date as admit_date, '' as pid FROM seg_referral as r
//														LEFT JOIN care_department as d ON d.nr = r.referrer_dept
//														LEFT JOIN care_encounter as c ON c.encounter_nr=r.encounter_nr
//														WHERE c.pid='".$pid."'
//														AND (r.encounter_nr LIKE '%".$searchkey."%'
//														OR r.referral_nr LIKE '%".$searchkey."%'
//														OR r.referral_date LIKE '%".$searchkey."%'));";
							#edited by VAN 07-13-2010
							if ($searchkey){
								$sql_cond1 = " AND (c.encounter_nr = '$searchkey'
																	OR c.encounter_date = '$searchkey') ";

								$sql_cond2 = " AND (r.encounter_nr = '$searchkey'
																		OR r.referral_nr = '$searchkey'
																		OR DATE(r.referral_date) = '$searchkey') ";
							}

								$this->sql = "SELECT SQL_CALC_FOUND_ROWS c.encounter_nr, '' as referral_nr, d.name_formal as dept, c.encounter_type, c.encounter_date as admit_date, c.pid
															FROM care_encounter as c
                            LEFT JOIN care_department as d ON c.current_dept_nr=d.nr
															WHERE c.pid='$pid' $sql_cond1
															UNION SELECT r.encounter_nr, r.referral_nr, d.name_formal as dept, c.encounter_type, r.referral_date as admit_date, '' as pid FROM seg_referral as r
                            LEFT JOIN care_department as d ON d.nr = r.referrer_dept
                            LEFT JOIN care_encounter as c ON c.encounter_nr=r.encounter_nr
															WHERE c.pid='$pid' $sql_cond2
															ORDER BY admit_date DESC, encounter_nr DESC, referral_nr";
        }
        #echo $this->sql;
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
						if($this->result=$db->SelectLimit($this->sql,$maxcount,$offset)){
							if($this->count=$this->result->RecordCount()) {
								return $this->result;
							}else{return false;}
						}else{return false;}
			}
    }

    # -------------------- --------added by shandy 08/28/2013 -------------- ------------------
    function SearchEncRefConfCertListHist($pid='', $searchkey='', $med_cert=true,$maxcount=100,$offset=0, $count_sql=0){
        global $db, $sql_LIKE, $root_path, $date_format;
        if(empty($maxcount)) $maxcount=100;
        if(empty($offset)) $offset=0;
        $searchkey=strtr($searchkey,'*?','%_');
        $searchkey=trim($searchkey);    
        $searchkey = str_replace("^","'",$searchkey);
        $keyword=addslashes($searchkey);
        if($med_cert=='true')
        {

						if ($searchkey)
							$sql_cond = " AND (m.encounter_nr='$searchkey') ";
						$this->sql = "SELECT SQL_CALC_FOUND_ROWS  s.create_dt, s.create_id, s.encounter_nr, s.requested_by, s.dr_nr, s.attending_doctor,
			  c.encounter_date AS date_admit
                          FROM seg_cert_conf AS s
                          LEFT JOIN care_encounter AS c ON c.encounter_nr = s.encounter_nr
			  LEFT JOIN seg_referral AS r ON r.encounter_nr=s.encounter_nr
			  WHERE c.pid ='$pid' $sql_cond
			  ORDER BY c.encounter_nr DESC, r.referral_nr";
        }
   
				if ($count_sql){
        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()) {
                return $this->result;
            }
            else{return FALSE;}
        }else{return FALSE;}
				}else{
						if($this->result=$db->SelectLimit($this->sql,$maxcount,$offset)){
							if($this->count=$this->result->RecordCount()) {
								return $this->result;
							}else{return false;}
						}else{return false;}
			}
    }
    # ------------------- ---------end ---------- -------------------

     function cmapRunningBalanceClass($pid='', $searchkey='', $med_cert=true,$maxcount=100,$offset=0, $count_sql=0){
        global $db, $sql_LIKE, $root_path, $date_format;
        if(empty($maxcount)) $maxcount=100;
        if(empty($offset)) $offset=0;
        # convert * and ? to % and &
        $searchkey=strtr($searchkey,'*?','%_');
        $searchkey=trim($searchkey);
        #$suchwort=$searchkey;
        $searchkey = str_replace("^","'",$searchkey);
        $keyword=addslashes($searchkey);
        if($med_cert=='true')
        {
					

						#edited by VAN 07-13-2010
						if ($searchkey)
							$sql_cond = " AND (scea.pid='$searchkey') ";
						#edited by VAN 02-25-2011
						$this->sql = "SELECT SQL_CALC_FOUND_ROWS
										scr.id,
										scr.pid, 
										scr.walkin_pid, 
										scr.cmap_account, 
										scr.referral_amount, 
										scr.current_balance, 
										sca.account_nr,
										sca.account_name, 
										scea.service_name, 
										scea.create_time, 
										scea.modify_id 

								      FROM seg_cmap_referrals AS scr
								      INNER JOIN seg_cmap_accounts AS sca ON sca.account_nr = scr.cmap_account 
								      INNER JOIN seg_cmap_entries_added AS scea ON scea.pid = scr.pid
								      WHERE scr.pid='$pid' AND scr.id='$id' $sql_cond
									  ORDER BY scea.pid DESC, scr.cmap_account";
        }
   
        
        #echo $this->sql;
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
						if($this->result=$db->SelectLimit($this->sql,$maxcount,$offset)){
							if($this->count=$this->result->RecordCount()) {
								return $this->result;
							}else{return false;}
						}else{return false;}
			}
    }
    # ------------------- ------------------- -------------------
    /* function countSearchAdmissionList
    *  @author Raissa 12/15/08
    *  @access public
    *  @internal Function for counting the list of uncancelled Admission History for a patient
    *  @param String pid, searchkey
    *  @param Integer maxcount, offset
    *  @return Array resultset
    *  @return Boolean false to indicate failure in the query
    */
    function countSearchEncRefMedCertList($pid='', $searchkey='', $med_cert=true,$maxcount=100,$offset=0) {
        global $db, $sql_LIKE, $root_path, $date_format;
        if(empty($maxcount)) $maxcount=100;
        if(empty($offset)) $offset=0;

        # convert * and ? to % and &
        $searchkey=strtr($searchkey,'*?','%_');
        $searchkey=trim($searchkey);
        #$suchwort=$searchkey;
        $searchkey = str_replace("^","'",$searchkey);
        $keyword=addslashes($searchkey);

        if($med_cert=='true')
        {
            $this->sql = "(SELECT m.create_dt, m.encounter_nr, m.referral_nr, m.cert_nr,
                          c.encounter_date as date_admit, d.name_formal as dept, m.create_id as prepared_by, fn_get_personell_name(m.dr_nr) as dr
                          FROM seg_cert_med as m
                          LEFT JOIN care_encounter AS c ON c.encounter_nr = m.encounter_nr
                          LEFT JOIN care_department AS d ON d.nr = c.current_dept_nr
                          WHERE c.pid ='".$pid."'
													AND (m.referral_nr='' OR ISNULL(m.referral_nr))
                          AND (m.encounter_nr LIKE '%".$searchkey."%'
                          OR c.encounter_date LIKE '%".$searchkey."%'
                          OR m.create_dt LIKE '%".$searchkey."%') )
                          UNION (SELECT m.create_dt, r.encounter_nr, r.referral_nr, m.cert_nr, r.referral_date as date_admit, d.name_formal as dept,
                          m.create_id as prepared_by, fn_get_personell_name(m.dr_nr) as dr from seg_referral as r LEFT JOIN seg_cert_med as m ON r.encounter_nr=m.encounter_nr
                          LEFT JOIN care_department as d ON d.nr = r.referrer_dept
                          LEFT JOIN care_encounter as c ON c.encounter_nr=r.encounter_nr
                          WHERE r.referral_nr = m.referral_nr AND c.pid='".$pid."'
                          AND (r.encounter_nr LIKE '%".$searchkey."%'
                          OR r.referral_nr LIKE '%".$searchkey."%'
                          OR r.referral_date LIKE '%".$searchkey."%'
                          OR m.create_dt LIKE '%".$searchkey."%'));";
        }
        else
        {
            $this->sql = "(SELECT c.encounter_nr, '' as referral_nr, d.name_formal as dept, c.encounter_type, c.encounter_date as admit_date, c.pid FROM care_encounter as c
                            LEFT JOIN care_department as d ON c.current_dept_nr=d.nr
                            WHERE c.pid='".$pid."'
                            AND (c.encounter_nr LIKE '%".$searchkey."%'
                            OR c.encounter_date LIKE '%".$searchkey."%'))
                            UNION (SELECT r.encounter_nr, r.referral_nr, d.name_formal as dept, c.encounter_type, r.referral_date as admit_date, '' as pid FROM seg_referral as r
                            LEFT JOIN care_department as d ON d.nr = r.referrer_dept
                            LEFT JOIN care_encounter as c ON c.encounter_nr=r.encounter_nr
                            WHERE c.pid='".$pid."'
                            AND (r.encounter_nr LIKE '%".$searchkey."%'
                            OR r.referral_nr LIKE '%".$searchkey."%'
                            OR r.referral_date LIKE '%".$searchkey."%'));";
        }

        /*$this->sql = "SELECT sr.referral_nr, sr.encounter_nr, sr.referrer_diagnosis, sr.referrer_dr,
                      sr.referrer_dept, sr.referrer_notes, sr.is_referral,
                      sr.create_id, sr.create_time, sr.referral_date, sr.status from seg_referral as sr
                      LEFT JOIN care_encounter as ce ON ce.encounter_nr = sr.encounter_nr
                      WHERE ce.pid= $pid
                      AND (sr.create_time LIKE '%".$keyword."%'
                      OR sr.referral_nr LIKE '%".$keyword."%' )
                      AND sr.status!='deleted'
                      ORDER BY ce.encounter_date DESC";             */

        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()) {
                return $this->result;
            }
            else{return FALSE;}
        }else{return FALSE;}
    }

	#--------------------------------


	#added by VAN 06-23-08
	function countSearchDiagnosisList($encounter_nr='', $maxcount=100,$offset=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

				// Added alt_desc column by LST -- 08.18.2009
				$this->sql = "SELECT d.*, icd.diagnosis_code, icd.description, e.is_confidential, sd.description as alt_desc
					  FROM care_encounter_diagnosis AS d
					  INNER JOIN care_icd10_en AS icd ON icd.diagnosis_code=d.code
					  INNER JOIN care_encounter AS e ON e.encounter_nr=d.encounter_nr
											left join seg_encounter_diagnosis as sd on d.encounter_nr = sd.encounter_nr and d.code = sd.code and sd.is_deleted = 0
					  WHERE d.encounter_nr='$encounter_nr'
					  AND e.status NOT IN ($this->dead_stat)
					  AND d.status NOT IN ($this->dead_stat)
					  ORDER BY d.date DESC";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	#Added by Cherry 11-15-10
	function SearchDiagnosisListForm2($encounter_nr='', $maxcount=100,$offset=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

				$this->sql = "SELECT SQL_CALC_FOUND_ROWS d.*, icd.diagnosis_code, icd.description, e.is_confidential, sd.description as alt_desc
											FROM seg_encounter_diagnosis AS d
											INNER JOIN care_icd10_en AS icd ON icd.diagnosis_code=d.code
											INNER JOIN care_encounter AS e ON e.encounter_nr=d.encounter_nr
											left join seg_encounter_diagnosis as sd on d.encounter_nr = sd.encounter_nr and d.code = sd.code and sd.is_deleted = 0
											WHERE d.encounter_nr='$encounter_nr'
											AND e.status NOT IN ($this->dead_stat)
											";

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}
    //edited by jasper 06/14/2013 added $frombilling=0
    //added (CASE WHEN sd.code_alt IS NULL OR sd.code_alt = '' THEN d.code ELSE sd.code_alt END) AS alt_code
	function SearchDiagnosisList($encounter_nr='', $maxcount=100,$offset=0, $frombilling=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

        // Added alt_desc column by LST -- 08.18.2009
        if ($frombilling==1) {
                $this->sql = "SELECT SQL_CALC_FOUND_ROWS d.*, icd.diagnosis_code, icd.description, e.is_confidential, sd.description as alt_desc,
                      (CASE WHEN sd.code_alt IS NULL OR sd.code_alt = '' or sd.code_alt = 'undefined' THEN d.code ELSE sd.code_alt END) AS alt_code
                      FROM care_encounter_diagnosis AS d
                      INNER JOIN care_icd10_en AS icd ON icd.diagnosis_code=d.code
                      INNER JOIN care_encounter AS e ON e.encounter_nr=d.encounter_nr
                      left join seg_encounter_diagnosis as sd on d.encounter_nr = sd.encounter_nr and d.code = sd.code and sd.is_deleted = 0
                      WHERE d.encounter_nr='$encounter_nr'
                      AND e.status NOT IN ($this->dead_stat)
                      AND d.status NOT IN ($this->dead_stat)
                      ORDER BY sd.entry_no";
        } else {
				$this->sql = "SELECT SQL_CALC_FOUND_ROWS d.*, icd.diagnosis_code, icd.description, e.is_confidential, sd.description as alt_desc
                      FROM care_encounter_diagnosis AS d
                      INNER JOIN care_icd10_en AS icd ON icd.diagnosis_code=d.code
                      INNER JOIN care_encounter AS e ON e.encounter_nr=d.encounter_nr
                      left join seg_encounter_diagnosis as sd on d.encounter_nr = sd.encounter_nr and d.code = sd.code and sd.is_deleted = 0
                      WHERE d.encounter_nr='$encounter_nr'
                      AND e.status NOT IN ($this->dead_stat)
                      AND d.status NOT IN ($this->dead_stat)
                      ORDER BY d.diagnosis_nr, d.date DESC";
        }

//		$this->sql = "SELECT d.*, icd.diagnosis_code, icd.description, e.is_confidential
//					  FROM care_encounter_diagnosis AS d
//				      INNER JOIN care_icd10_en AS icd ON icd.diagnosis_code=d.code
//					  INNER JOIN care_encounter AS e ON e.encounter_nr=d.encounter_nr
//					  WHERE d.encounter_nr='$encounter_nr'
//					  AND e.status NOT IN ($this->dead_stat)
//					  AND d.status NOT IN ($this->dead_stat)
//					  ORDER BY d.date DESC";

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

	#------added by VAN 06-25-08
	function ServedEncounter($encounter_nr, $dept, $is_served, $date_served){
		global $db, $HTTP_SESSION_VARS;
		$ret=FALSE;

		$history = $this->ConcatHistory("To be Served in ".$dept." : ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");

		$this->sql="UPDATE care_encounter SET
						is_served='".$is_served."',
						date_served='".$date_served."',
						clerk_served_by='".$HTTP_SESSION_VARS['sess_user_name']."',
						clerk_served_date=NOW(),
						clerk_served_history=".$history."
						WHERE encounter_nr = '".$encounter_nr."'";

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

	#added by VAN 08-20-08
	function deleteRecentRoomLocationIfCorrection($enc_nr='') {
		$this->sql="DELETE FROM care_encounter_location
						WHERE encounter_nr='".$enc_nr."'
						AND discharge_type_nr=0
						AND status=''
						AND date_to='0000-00-00'";

      return $this->Transact();
	}

	function SetPrevRoomLocationToRecent($enc_nr='') {
		$this->sql="UPDATE care_encounter_location SET
							date_to = '0000-00-00',
							time_to = '',
							discharge_type_nr=0,
							status = ''
						WHERE encounter_nr='".$enc_nr."'
						ORDER BY modify_time DESC
						LIMIT 3";

      return $this->Transact();
	}

	function getRecentWard($enc_nr=''){
	global $db;

		$this->sql ="SELECT * FROM care_encounter_location
							WHERE encounter_nr='".$enc_nr."'
							AND discharge_type_nr=0
							AND status=''
							AND type_nr=2";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getRecentRoom($enc_nr=''){
		global $db;

		$this->sql ="SELECT * FROM care_encounter_location
							WHERE encounter_nr='".$enc_nr."'
							AND discharge_type_nr=0
							AND status=''
							AND type_nr=4";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}
	#-----------------------------

	#added by VAN 10-04-08
	#Get all encounters within 1 week from date of classification in social service
	function getAllEncounterInSS($pid=''){
		global $db;

		$this->sql = "SELECT DISTINCT enc.encounter_nr
							FROM care_encounter AS enc
							LEFT JOIN seg_charity_grants_pid AS scgp ON scgp.pid=enc.pid
									WHERE scgp.pid='".$pid."'
									AND DATEDIFF(DATE(now()),date(enc.encounter_date))< 30
									ORDER BY encounter_date LIMIT 5";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->result->RecordCount())
				return $this->result;
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

    /**
    * Admit a patient, but only when its encounter_type is outpatient or ER patient.
    * Sets the encounter_type= '3 (er) or 4(opd)', admission_dt, and stores history and modify infos
    * @access public
    * @param int Encounter number
    * @param string Optional name of person responsible for admission
    * @return boolean
    */
    function Admit($enc_nr=0,$encounter_type,$by){
        global $HTTP_SESSION_VARS;
        if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;
        if(empty($by)) $by=$HTTP_SESSION_VARS['sess_user_name'];

        if ($encounter_type==1)
            $new_encounter_type = 3;
        elseif ($encounter_type==2)
            $new_encounter_type = 4;

        $this->sql="UPDATE $this->tb_enc SET
                        encounter_type='". $new_encounter_type."',
                        admission_dt='".date('Y-m-d H:i:s')."',
                        history=".$this->ConcatHistory("Admitted ".date('Y-m-d H:i:s')." by $by, logged-user ".$HTTP_SESSION_VARS['sess_user_name']."\n").",
                        modify_id='".$HTTP_SESSION_VARS['sess_user_name']."',
                        modify_time='".date('Y-m-d H:i:s')."'
												WHERE encounter_nr='".$this->enc_nr."' AND encounter_status IN ('','0','allow_cancel')";
        return $this->Transact($this->sql);
    }

		#added by VAN 04-28-09
		function getReferralInfoByEnc($encounter_nr, $referral_nr){
				global $db;

				$this->sql ="SELECT r.*,
												IF (r.is_dept=1,(SELECT name_formal FROM care_department WHERE nr=r.referrer_dept),
																				(SELECT hosp_name FROM seg_other_hospital WHERE id=r.referrer_dept)) AS referred_to
												FROM seg_referral AS r
												WHERE r.encounter_nr='".$encounter_nr."' AND r.referral_nr='".$referral_nr."'";

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

		function getVitalSign($encounter_nr, $is_initial){
				global $db;

				if ($is_initial)
						$order = " ASC ";
				else
						$order = " DESC ";

				$this->sql ="SELECT
												(SELECT unit_name FROM seg_encounter_vitalsigns_unit WHERE unit_id=v.bp_unit) AS bp_unit_id,
												(SELECT unit_name FROM seg_encounter_vitalsigns_unit WHERE unit_id=v.rr_unit) AS rr_unit_id,
												(SELECT unit_name FROM seg_encounter_vitalsigns_unit WHERE unit_id=v.pr_unit) AS pr_unit_id,
												v.*
												FROM seg_encounter_vitalsigns AS v
												WHERE encounter_nr='".$encounter_nr."'
												ORDER BY date $order LIMIT 1";

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

		function deleteDeathsInfo($pn){
				$this->sql = "DELETE FROM seg_encounter_deaths WHERE encounter_nr='$pn'";
				return $this->Transact();

		}
		function addDeathsInfo($pn,$ward_nr,$is_beyond_48hrs){
				$this->sql = "INSERT INTO seg_encounter_deaths(encounter_nr,ward_nr,is_beyond_48hrs) VALUES('$pn','$ward_nr','$is_beyond_48hrs')";
				return $this->Transact();

		}

		#----------------------
		#----------------------

		function loadEncounterDataByWalkinPid($pid=''){
				global $db;
				#if(!$this->internResolveEncounterNr($enc_nr)) return FALSE;
				$this->sql="SELECT p.create_time AS date_reg ,p.name_last, p.name_first, p.date_birth, p.sex,
																		p.address
														FROM seg_walkin AS p
																		 LEFT JOIN $this->tb_enc AS e  ON e.pid=p.pid
														WHERE (p.pid='$pid')";

				#echo $this->sql;
				if($this->res['lend']=$db->Execute($this->sql)) {
						if($this->record_count=$this->res['lend']->RecordCount()) {
								$this->rec_count=$this->record_count;
								$this->encounter=$this->res['lend']->FetchRow();
								//$this->result=NULL;
								$this->is_loaded=true;
								return true;
						} else { return FALSE;}
				} else { return FALSE;}
		}

		#added by VAN 10-07-09
		#get all encounter and vital signs of the specific encounter of a patient
		function getPatientTransaction($pid, $limit){
				 global $db;

		if ($limit)
			$limit_cond = 'LIMIT 2';

				$this->sql = "SELECT e.encounter_nr, e.encounter_date,e.encounter_type, e.is_discharged, e.discharge_date, e.er_opd_diagnosis, e.current_dept_nr,
														 fn_get_personell_name(e.current_att_dr_nr) AS dr_name,
														 (SUBSTRING(MIN(CONCAT(v.date,v.systole)),20)) AS systole,
														 (SUBSTRING(MIN(CONCAT(v.date,v.diastole)),20)) AS diastole,
														 (SUBSTRING(MIN(CONCAT(v.date,v.temp)),20)) AS temp,
														 (SUBSTRING(MIN(CONCAT(v.date,v.weight)),20)) AS weight,
														 (SUBSTRING(MIN(CONCAT(v.date,v.resp_rate)),20)) AS resp_rate,
														 (SUBSTRING(MIN(CONCAT(v.date,v.pulse_rate)),20)) AS pulse_rate,
														 (SUBSTRING(MIN(CONCAT(v.date,v.bp_unit)),20)) AS bp_unit,
														 (SUBSTRING(MIN(CONCAT(v.date,v.temp_unit)),20)) AS temp_unit,
														 (SUBSTRING(MIN(CONCAT(v.date,v.weight_unit)),20)) AS weight_unit,
														 (SUBSTRING(MIN(CONCAT(v.date,v.rr_unit)),20)) AS rr_unit,
														 (SUBSTRING(MIN(CONCAT(v.date,v.pr_unit)),20)) AS pr_unit
											FROM care_encounter AS e
											LEFT JOIN seg_encounter_vitalsigns AS v ON v.pid=e.pid AND v.encounter_nr=e.encounter_nr
											WHERE e.pid='$pid'
											GROUP BY e.encounter_nr
											ORDER BY encounter_date DESC $limit_cond";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->count=$this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

	#-------------------------

	#added by VAN 11-26-09
	function getInfoPatientInsurance($pid='',$insurance_nr='', $hcare_id=''){
			global $db;

			$this->sql = "SELECT p.*
								FROM care_person_insurance as p
								WHERE hcare_id='".$hcare_id."' AND pid='".$pid."' AND insurance_nr='".$insurance_nr."'";

			if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
	}
	#------------------------

	#added by VAN 12-18-09
	#get all ICD 10 given the encounter nr
	function getAllICD10($encounter_nr){
				 global $db;

		$this->sql = "SELECT d.code, ds.description
						FROM care_encounter_diagnosis as d
						INNER JOIN care_icd10_en AS ds ON ds.diagnosis_code=d.code
						WHERE encounter_nr='$encounter_nr'
						AND status NOT IN ('deleted','hidden','inactive','void')
						ORDER BY code";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->count=$this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

	#get all medicines that are already served (given the encounter nr)
	function getAllMedicines($encounter_nr){
				 global $db;

		$this->sql = "SELECT d.bestellnum AS code, d.quantity, 'pcs' AS unit, m.artikelname AS name, d.pricecash AS price_cash
						FROM seg_pharma_orders as p
						INNER JOIN seg_pharma_order_items AS d ON d.refno=p.refno
						INNER JOIN care_pharma_products_main AS m ON m.bestellnum=d.bestellnum
						WHERE p.encounter_nr='$encounter_nr'
						AND d.serve_status='S'
						ORDER BY artikelname";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->count=$this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

	#get all request, especially in lab and radio, that are already served (given the encounter nr)
	function getAllOtherRequests($encounter_nr){
				 global $db;

		$this->sql = "SELECT d.service_code, s.name, 'LB' AS source
						FROM seg_lab_serv as l
						INNER JOIN seg_lab_servdetails AS d ON d.refno=l.refno
						INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code
						WHERE l.encounter_nr='$encounter_nr'
						AND l.status NOT IN ('deleted','hidden','inactive','void')
						AND d.status NOT IN ('deleted','hidden','inactive','void')
						/*AND d.is_served=1*/
						UNION
						SELECT d.service_code, s.name, 'RD' AS source
						FROM seg_radio_serv as l
						INNER JOIN care_test_request_radio AS d ON d.refno=l.refno
						INNER JOIN seg_radio_services AS s ON s.service_code=d.service_code
						WHERE l.encounter_nr='$encounter_nr'
						AND l.status NOT IN ('deleted','hidden','inactive','void')
						/*AND d.status='done'*/";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->count=$this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

	#get all requests in medicines, lab and radiology
	function getAllRequests($encounter_nr){
				 global $db;

		$this->sql = "SELECT d.bestellnum AS code, d.quantity, 'pcs' AS unit, m.artikelname AS name, d.pricecash AS price_cash, 'PH' AS source
						FROM seg_pharma_orders as p
						INNER JOIN seg_pharma_order_items AS d ON d.refno=p.refno
						INNER JOIN care_pharma_products_main AS m ON m.bestellnum=d.bestellnum
						WHERE p.encounter_nr='$encounter_nr'
						AND d.serve_status='S'
						UNION
						SELECT d.service_code AS code,d.quantity, 'service' AS unit, s.name, d.price_cash, 'LB' AS source
						FROM seg_lab_serv as l
						INNER JOIN seg_lab_servdetails AS d ON d.refno=l.refno
						INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code
						WHERE l.encounter_nr='$encounter_nr'
						AND l.status NOT IN ('deleted','hidden','inactive','void')
						AND d.status NOT IN ('deleted','hidden','inactive','void')
						/*AND d.is_served=1*/
						UNION
						SELECT d.service_code AS code,'1' AS quantity, 'service' AS unit, s.name, d.price_cash, 'RD' AS source
						FROM seg_radio_serv as l
						INNER JOIN care_test_request_radio AS d ON d.refno=l.refno
						INNER JOIN seg_radio_services AS s ON s.service_code=d.service_code
						WHERE l.encounter_nr='$encounter_nr'
						AND l.status NOT IN ('deleted','hidden','inactive','void')
						/*AND d.status='done'*/
						ORDER BY source DESC, name";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->count=$this->result->RecordCount())
								return $this->result;
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}


		#get all patient transaction data given the encounter nr
		function getEncounterData($encounter_nr){
				 global $db;

		$this->sql = "SELECT * FROM care_encounter WHERE encounter_nr='$encounter_nr'";

				if ($this->result=$db->Execute($this->sql)){
						if ($this->count=$this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

	function getReferralByEnc($encounter_nr=0){
		global $db;

		$this->sql ="SELECT r.*, h.hosp_name, hosp_address
									FROM seg_referral AS r
									LEFT JOIN seg_other_hospital AS h ON h.id=r.referrer_dept
									WHERE encounter_nr='$encounter_nr' ORDER BY create_time LIMIT 1";
		#echo "<br>disp = ".$this->sql;

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}

	}
	#------------------------

	function countEncounterRequests($encounter_nr){
				global $db;

				$this->sql = "SELECT l_serv.refno, create_dt AS req_date, 1 AS req_type, u.name AS req_by
												FROM seg_lab_serv AS l_serv
												LEFT JOIN care_users AS u ON u.login_id = l_serv.create_id
											 WHERE encounter_nr='$encounter_nr'
											 AND l_serv.status NOT IN ('deleted','hidden','inactive','void')
										UNION
										SELECT r_serv.refno, create_dt AS req_date, 2 AS req_type, create_id AS req_by
												FROM seg_radio_serv AS r_serv
											 WHERE encounter_nr='$encounter_nr'
											 AND r_serv.status NOT IN ('deleted','hidden','inactive','void')
										UNION
										SELECT p.refno, orderdate AS req_date, 3 AS req_type, u.name AS req_by
												FROM seg_pharma_orders AS p
												LEFT JOIN care_users AS u ON u.login_id = p.create_id
											 WHERE encounter_nr='$encounter_nr' AND pharma_area='IP'
										UNION
										SELECT p.refno, orderdate AS req_date, 4 AS req_type, u.name AS req_by
												FROM seg_pharma_orders AS p
												LEFT JOIN care_users AS u ON u.login_id = p.create_id
											 WHERE encounter_nr='$encounter_nr' AND pharma_area<>'IP'
										UNION
										SELECT p.refno, chrge_dte AS req_date, 5 AS req_type, create_id AS req_by
												FROM seg_misc_chrg AS p
											 WHERE encounter_nr='$encounter_nr'
										ORDER BY req_date DESC";

				if ($this->result=$db->Execute($this->sql)){
						return $this->result;
				}else{
						return FALSE;
				}

		}

		function getEncounterRequests($encounter_nr, $offset=0){
				global $db;

				$this->sql = "SELECT l_serv.refno, create_dt AS req_date, 1 AS req_type, u.name AS req_by
												FROM seg_lab_serv AS l_serv
												LEFT JOIN care_users AS u ON u.login_id = l_serv.create_id
											 WHERE encounter_nr='$encounter_nr'
											 AND l_serv.status NOT IN ('deleted','hidden','inactive','void')
										UNION
										SELECT r_serv.refno, create_dt AS req_date, 2 AS req_type, create_id AS req_by
												FROM seg_radio_serv AS r_serv
											 WHERE encounter_nr='$encounter_nr'
											 AND r_serv.status NOT IN ('deleted','hidden','inactive','void')
										UNION
										SELECT p.refno, orderdate AS req_date, 3 AS req_type, u.name AS req_by
												FROM seg_pharma_orders AS p
												LEFT JOIN care_users AS u ON u.login_id = p.create_id
											 WHERE encounter_nr='$encounter_nr' AND pharma_area='IP'
										UNION
										SELECT p.refno, orderdate AS req_date, 4 AS req_type, u.name AS req_by
												FROM seg_pharma_orders AS p
												LEFT JOIN care_users AS u ON u.login_id = p.create_id
											 WHERE encounter_nr='$encounter_nr' AND pharma_area<>'IP'
										UNION
										SELECT p.refno, chrge_dte AS req_date, 5 AS req_type, create_id AS req_by
												FROM seg_misc_chrg AS p
											 WHERE encounter_nr='$encounter_nr'
										ORDER BY req_date DESC
										LIMIT $offset, 10";

				if ($this->result=$db->Execute($this->sql)){
						return $this->result;
				}else{
						return FALSE;
				}

		}

		function getRequestDetails($refno, $type){
				global $db;

				switch($type){
						case 0:
										break;
						case 1: $this->sql = "SELECT name AS item FROM seg_lab_services AS s
																		LEFT JOIN seg_lab_servdetails AS sd ON sd.service_code = s.service_code
																		WHERE sd.refno='$refno'";
										break;
						#modified by cha, june 12, 2010
							#changed short_name to name
						case 2: $this->sql = "SELECT name AS item FROM seg_radio_services AS s
																		LEFT JOIN care_test_request_radio AS sd ON sd.service_code = s.service_code
																		WHERE sd.refno='$refno'";
										break;
						case 3: $this->sql = "SELECT CONCAT('(', i.quantity, ') ', artikelname) AS item FROM care_pharma_products_main AS p
																		LEFT JOIN seg_pharma_order_items AS i ON i.bestellnum = p.bestellnum
																		WHERE i.refno='$refno'";
										break;
						case 4: $this->sql = "SELECT CONCAT('(', i.quantity, ') ', artikelname) AS item FROM care_pharma_products_main AS p
																		LEFT JOIN seg_pharma_order_items AS i ON i.bestellnum = p.bestellnum
																		WHERE i.refno='$refno'";
										break;
						case 5: $this->sql = "SELECT name AS item FROM seg_other_services AS s
																		LEFT JOIN seg_misc_chrg_details AS sd ON sd.service_code = s.service_code
																		WHERE sd.refno='$refno'";
										break;
						#added by CHA, May 11,2010
						case 6: $this->sql = "SELECT CONCAT('(', e.number_of_usage, ') ', p.artikelname) AS item FROM seg_equipment_order_items AS e
																		LEFT JOIN care_pharma_products_main as p ON e.equipment_id=p.bestellnum
																		WHERE e.refno='$refno'";
										break;
						#-------------------------
						default:
										break;
				}
				#echo $this->sql;

				if ($this->result=$db->Execute($this->sql)){
						return $this->result;
				}else{
						return FALSE;
				}

		}

	#added by VAN 01-25-10
	function getLastEncounterNr($triage){
		global $db;

		$this->sql ="SELECT last_encounter_nr FROM care_encounter_tracker WHERE triage='".$triage."'";
		#echo "<br>disp = ".$this->sql;

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount()){
				$row = $this->result->FetchRow();
				return  $row['last_encounter_nr'];
			}else
				return FALSE;
		}else{
			return FALSE;
		}

	}

	function update_Encounter_Tracker($encounter_nr,$triage){
		$this->sql = "UPDATE care_encounter_tracker SET last_encounter_nr='".$encounter_nr."' WHERE triage='".$triage."'";
		return $this->Transact();
	}
	#--------------------------

	#added by VAN 06-11-2010
	function hasFinalBilling($encounter_nr){
		global $db;

		$this->sql ="SELECT is_final FROM seg_billing_encounter WHERE encounter_nr='$encounter_nr'
					 AND is_final=1 AND (is_deleted=0 OR is_deleted IS NULL)				
									ORDER BY bill_dte DESC LIMIT 1";
		#echo "<br>disp = ".$this->sql;

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount()){
				$row = $this->result->FetchRow();
				return  $row['is_final'];
			}else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	#added by VAN 06-30-2010
	function hasSameConsultation($pid,$current_dept_nr, $encounter_type,$encounter_date){
		global $db;

		$this->sql ="SELECT e.*
									FROM care_encounter AS e
									WHERE pid='$pid'
									AND DATE(encounter_date)=DATE('$encounter_date')
									AND encounter_type IN ($encounter_type)
									AND e.current_dept_nr='$current_dept_nr'
                                    AND e.is_discharged<>1";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount()){
				$this->rowDup = $this->result->FetchRow();
				return  $this->count;
			}else
				return FALSE;
		}else{
			return FALSE;
		}
	}
	#----------------------

	//added by VAN 07-12-2010
	function deleteMedCert($encounter_nr, $cert_nr){
			$this->sql="DELETE FROM seg_cert_med where encounter_nr='$encounter_nr' AND cert_nr='$cert_nr'";

			return $this->Transact();
	}
	// added by shandy 08/28/2013 for delete cert of conf......
	function deleteConfCert($encounter_nr, $cert_nr){
			$this->sql="DELETE FROM seg_cert_conf where encounter_nr='$encounter_nr'";

			return $this->Transact();
	}

	#added by VAN 07-16-2010
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
	#--------------------

	function getSaveBilling($enr='') {
		global $db;
		$this->sql="SELECT * from seg_billing_encounter WHERE encounter_nr='$enr' and is_deleted IS NULL
								ORDER BY bill_dte DESC LIMIT 1";
		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()){
				return $this->result;
			}
			else{
				return false;
			}
		}
		else{
			return false;
			}
	}

	function Is_ReceivedChart($encounter_nr=0){
		global $db;

		$this->sql = "SELECT received_date FROM care_encounter WHERE encounter_nr='$encounter_nr'";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount()){
				$this->rowRec = $this->result->FetchRow();
				#return  $this->count;
				if ($this->rowRec['received_date'])
					return TRUE;
				else
					return FALSE;
			}else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	#added by VAN 03-10-2011
	function getLatestImpression($pid, $encounter_nr){
		global $db;
        #Edited by Jarel 04/13/2013
        /*$this->sql = "SELECT d.refno, s.serv_dt AS request_date, DATE_FORMAT(s.serv_tm ,'%T:%f')AS request_time, d.service_code, d.clinical_info  AS impression
                                    FROM seg_lab_serv AS s
                                    INNER JOIN seg_lab_servdetails AS d ON d.refno=s.refno
                                    WHERE s.encounter_nr= '".$encounter_nr."' AND s.pid='".$pid."'
                                 UNION
                                    SELECT d.refno, s.request_date AS request_date, DATE_FORMAT(s.request_time,'%T:%f')  AS request_time,d.service_code, d.clinical_info  AS impression
                                    FROM seg_radio_serv AS s
                                    INNER JOIN care_test_request_radio AS d ON d.refno=s.refno
                                    WHERE s.encounter_nr='".$encounter_nr."' AND s.pid='".$pid."'
                                 UNION
                                    SELECT d.refno, DATE_FORMAT(s.chrge_dte ,'%Y-%m-%d')AS request_date, DATE_FORMAT(s.chrge_dte,'%T:%f')  AS request_time, d.service_code, d.clinical_info  AS impression
                                    FROM seg_misc_service AS s
                                    INNER JOIN seg_misc_service_details AS d ON d.refno=s.refno
                                    WHERE s.encounter_nr= '".$encounter_nr."' AND s.pid='".$pid."'
                                    ORDER BY request_date DESC, request_time DESC, refno DESC LIMIT 1";*/
                                    
         #updated by VAN 07-23-2013
         $this->sql = "SELECT IF(e.encounter_type IN ('3','4'),e.er_opd_diagnosis,s.impression) AS impression
                        FROM care_encounter e
                        LEFT JOIN (SELECT d.refno, s.serv_dt AS request_date, DATE_FORMAT(s.serv_tm ,'%T:%f')AS request_time, d.service_code, d.clinical_info  AS impression,
                        s.encounter_nr, s.pid
                        FROM seg_lab_serv AS s
                        INNER JOIN seg_lab_servdetails AS d ON d.refno=s.refno
                        WHERE s.encounter_nr= ".$db->qstr($encounter_nr)." AND s.pid=".$db->qstr($pid)."
                        UNION
                        SELECT d.refno, s.request_date AS request_date, DATE_FORMAT(s.request_time,'%T:%f')  AS request_time,d.service_code, d.clinical_info  AS impression,
                        s.encounter_nr, s.pid
                        FROM seg_radio_serv AS s
                        INNER JOIN care_test_request_radio AS d ON d.refno=s.refno
                        WHERE s.encounter_nr= ".$db->qstr($encounter_nr)." AND s.pid=".$db->qstr($pid)."
                        UNION
                        SELECT d.refno, DATE_FORMAT(s.chrge_dte ,'%Y-%m-%d')AS request_date, DATE_FORMAT(s.chrge_dte,'%T:%f')  AS request_time, d.service_code, d.clinical_info  AS impression,
                        s.encounter_nr, s.pid
                        FROM seg_misc_service AS s
                        INNER JOIN seg_misc_service_details AS d ON d.refno=s.refno
                        WHERE s.encounter_nr= ".$db->qstr($encounter_nr)." AND s.pid=".$db->qstr($pid)."
                        ORDER BY request_date DESC, request_time DESC, refno DESC LIMIT 1) AS s ON s.pid=e.pid
                        AND s.encounter_nr=e.encounter_nr
                        WHERE e.encounter_nr=".$db->qstr($encounter_nr)." AND e.pid=".$db->qstr($pid);                            
                                    

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount()){
				$row = $this->result->FetchRow();
				return  $row['impression'];
			}else
				return FALSE;
		}else{
			return FALSE;
		}

	}

	#added by VAN 09-01-2011
	function getParentEncInfo($encounter_nr){
		global $db;

		$this->sql ="SELECT e.encounter_date, e.admission_dt, e.parent_encounter_nr,
											 (SELECT p.encounter_date FROM care_encounter AS p
												 WHERE p.encounter_nr=e.parent_encounter_nr) AS parent_encounter_date
								FROM care_encounter AS e
								WHERE encounter_nr='".$encounter_nr."'";
		#echo "<br>disp = ".$this->sql;

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount()){
				return $this->result->FetchRow();
			}else
				return FALSE;
		}else{
			return FALSE;
		}

	}

	function updateERdischargeDate($encounter_nr, $admission_date, $consult_date){
		global $HTTP_SESSION_VARS;

		if ($consult_date)
			 $consult_date_cond = "encounter_date='".date("Y-m-d H:i:s",strtotime($consult_date))."',\n";

		$this->sql = "UPDATE care_encounter SET
										".$consult_date_cond."
										discharge_date='".date("Y-m-d",strtotime($admission_date))."',
										discharge_time='".date("H:i:s",strtotime($admission_date))."',
										history=".$this->ConcatHistory("\n$act ".date('Y-m- H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']).",
										modify_id='".$HTTP_SESSION_VARS['sess_user_name']."',
										modify_time='".date('YmdHis')."'
										WHERE encounter_nr='".$encounter_nr."'";

		return $this->Transact();
	}

    #added by VAN 02-20-2012
    function getPatientEncInfo($encounter_nr){
        global $db;

        $this->sql ="SELECT p.pid, e.encounter_nr, p.name_first, p.name_last, p.name_middle, p.sex, p.date_birth,
                        p.civil_status, p.street_name, p.brgy_nr, p.mun_nr,
                        sb.brgy_name, sm.mun_name, sp.prov_name, srg.region_name,
                        IF(fn_calculate_age(e.encounter_date,p.date_birth),fn_get_age(e.encounter_date,p.date_birth),age) AS age,
                        e.encounter_date, IFNULL(e.admission_dt,'0000-00-00 00:00:00') AS admission_date,
                        IF(e.encounter_type=2, 'Outpatient', IF(e.encounter_type=1, 'ER Patient',
                        IF(e.encounter_type=3 OR e.encounter_type=4,'Inpatient','Walk-in'))) AS patient_type,
                        e.current_dept_nr, e.current_att_dr_nr, e.encounter_type,
                        IFNULL(e.discharge_date,'0000-00-00') AS discharged_date,
                        IFNULL(p.death_date,'0000-00-00') AS death_date, e.er_opd_diagnosis,
                        rl.religion_name AS religion, oc.occupation_name AS occupation,
                        sd.disp_desc AS disposition, sr.result_desc AS result, sr.result_code,
                        IF (sr.result_code IS NULL,'Unknown',IF(sr.result_code IN (4,8),'Dead','Alive')) AS outcome,
                        IF(e.encounter_type=2, fn_get_department_name(e.current_dept_nr), IF(e.encounter_type=1, 'ER',
                          IF(e.encounter_type=3 OR e.encounter_type=4,CONCAT(fn_get_ward_name(e.current_ward_nr),' Rm. ',e.current_room_nr),'WN'))) AS location
                        FROM care_encounter e
                        INNER JOIN care_person p ON p.pid=e.pid
                        LEFT JOIN seg_religion rl ON rl.religion_nr=p.religion
                        LEFT JOIN seg_occupation oc ON oc.occupation_nr=p.occupation
                        LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
                        LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
                        LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
                        LEFT JOIN seg_regions AS srg ON srg.region_nr=sp.region_nr
                        LEFT JOIN (SELECT ser.encounter_nr,SUBSTRING(MAX(CONCAT(ser.create_time,ser.result_code)),20) AS result_code, r.result_desc
                                   FROM seg_encounter_result AS ser
                                   INNER JOIN care_encounter AS em ON em.encounter_nr=ser.encounter_nr
                               INNER JOIN seg_results AS r ON r.result_code=ser.result_code
                                   WHERE em.encounter_nr='$encounter_nr'
                                   GROUP BY ser.encounter_nr
                                   ORDER BY ser.encounter_nr, ser.create_time DESC) AS sr ON sr.encounter_nr = e.encounter_nr
                        LEFT JOIN (SELECT sed.encounter_nr,SUBSTRING(MAX(CONCAT(sed.create_time,sed.disp_code)),20) AS disp_code, disp_desc
                                  FROM seg_encounter_disposition AS sed
                                  INNER JOIN care_encounter AS em ON em.encounter_nr=sed.encounter_nr
                              INNER JOIN seg_dispositions AS d ON d.disp_code=sed.disp_code
                                  WHERE em.encounter_nr='$encounter_nr'
                                  GROUP BY sed.encounter_nr
                                 ORDER BY sed.encounter_nr, sed.create_time DESC) AS sd ON sd.encounter_nr = e.encounter_nr

                        WHERE e.encounter_nr='$encounter_nr'";

        if ($this->result=$db->Execute($this->sql)){
            if ($this->count = $this->result->RecordCount()){
                return $this->result->FetchRow();
            }else
                return FALSE;
        }else{
            return FALSE;
        }

    }

    #added by VAS 09-04-2012
    function getPatientCaseType($pid){
        global $db;

        $this->sql = "SELECT IF((SUM(CASE WHEN p.pid THEN 1 ELSE 0 END) >= 1), 'Old Patient', 'New Patient') AS patient_type
                        FROM care_encounter p WHERE pid='".$pid."'";

        if ($this->result=$db->Execute($this->sql)){
            if ($this->count = $this->result->RecordCount()){
                $this->rowRec = $this->result->FetchRow();
                #return  $this->count;
                return $this->rowRec['patient_type'];
            }else
                return FALSE;
        }else{
            return FALSE;
        }
    }

    function getPatientCaseType_update($pid, $encounter_nr){
        global $db;

        $this->sql = "SELECT IF((SUM(CASE WHEN p.pid THEN 1 ELSE 0 END) >= 1), 'Old Patient', 'New Patient') AS patient_type
                        FROM care_encounter p
                        WHERE pid='".$pid."' AND encounter_nr NOT IN ('".$encounter_nr."')
                        AND encounter_date BETWEEN
                            (SELECT MIN(encounter_date) AS encounter_date FROM care_encounter p WHERE pid='".$pid."')
                            AND (SELECT encounter_date FROM care_encounter WHERE encounter_nr='".$encounter_nr."')";

        if ($this->result=$db->Execute($this->sql)){
            if ($this->count = $this->result->RecordCount()){
                $this->rowRec = $this->result->FetchRow();
                #return  $this->count;
                return $this->rowRec['patient_type'];
            }else
                return FALSE;
        }else{
            return FALSE;
        }
    }
    #========================

    function isPHIC($encounter_nr) {
			global $db;

			$this->sql = "SELECT fn_isPHIC('$encounter_nr') isphic";
      if ($this->result=$db->Execute($this->sql)) {
          if ($this->result->RecordCount()) {
						$row = $this->result->FetchRow();
						return ($row['isphic'] === "PHIC");
          } else
          	return FALSE;
      } else
				return FALSE;
    }

		function isHouseCase($encounter_nr) {
			global $db;

			$bhousecase = false;
			$strSQL = "select fn_isHouseCase('".$encounter_nr."') as casetype";
			if ($result=$db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					if ($row = $result->FetchRow()) {
						 $bhousecase = is_null($row["casetype"]) ? false : ($row["casetype"] == 1);
					}
				}
			}

		  return $bhousecase;
		}

    public function getMemberType($encounter_nr=null) {
        global $db;

        if (empty($encounter_nr)) {
            $encounter_nr = $this->enc_nr;
        }
        $this->sql = "SELECT memcategory_code\n".
            "FROM seg_encounter_memcategory `sem`\n".
            "INNER JOIN seg_memcategory `sm` ON sm.`memcategory_id`=sem.`memcategory_id`\n".
            "WHERE sem.`encounter_nr`=".$db->qstr($encounter_nr);
        $this->result=$db->GetOne($this->sql);
        return $this->result;
    }

    /***
     * This routine retrieves the tracking no. persisted in 'seg_verification_table' given the encounter no.
     *
     * @author  LST
     * @created 11.15.2012
     * @param   encounter_nr
     * @return  tracking_no
     */
    function getTrackingNo($encounter_nr) {
        global $db;

        $tracking_no = '';
        $this->sql = "SELECT tracking_no FROM seg_eclaims_verification_log WHERE encounter_nr = '{$encounter_nr}'";
        if ($result=$db->Execute($this->sql)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    if (!is_null($row["tracking_no"])) {
                        $tracking_no = $row["tracking_no"];
                    }
                }
            }
        }
        return $tracking_no;
    }
    
    function hasSavedBilling($encounter_nr){
        global $db;

        $this->sql ="SELECT * FROM seg_billing_encounter
                     WHERE encounter_nr=".$db->qstr($encounter_nr)."
                     AND is_final=1 AND (is_deleted=0 OR is_deleted IS NULL)
                     ORDER BY bill_dte DESC LIMIT 1";
        /*$this->sql ="SELECT DISTINCT b.bill_nr, e.encounter_nr, e.pid, b.is_final, e.is_maygohome,b.is_deleted, e.mgh_setdte, e.is_discharged, e.discharge_date, e.discharge_time, b.* 
                        FROM seg_billing_encounter b
                        INNER JOIN care_encounter e ON e.encounter_nr=b.encounter_nr 
                        WHERE b.encounter_nr=".$db->qstr($encounter_nr)."
                        AND (is_final=1 OR e.is_maygohome=1) AND (is_deleted=0 OR is_deleted IS NULL)
                        ORDER BY bill_dte DESC 
                        LIMIT 1";*/             
        #echo "<br>disp = ".$this->sql;

        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        }
    }

    function getPatientOPDFreeCFforADay($pid=0){
        global $db;

        $this->sql ="SELECT pid FROM seg_charity_grants_consultation
                            WHERE pid=".$db->qstr($pid)."
                            AND STATUS='valid'
                            AND DATE(grant_dte)=DATE(NOW())
                            ORDER BY grant_dte DESC LIMIT 1";

        #echo "sql = ".$this->sql;
        if ($this->result=$db->Execute($this->sql)){
            if ($this->count = $this->result->RecordCount())
                return $this->result->FetchRow();
            else
                return FALSE;
        }else{
            return FALSE;
        }
    }

    #Added by Jarel 07/17/2013
    function getOPDTempOR(){
        global $db;
        
        $this->sql = "SELECT * FROM seg_opd_or_temp";
        
        #echo "sql = ".$this->sql;
        if ($this->result=$db->Execute($this->sql)){
            if ($this->count = $this->result->RecordCount())
                return $this->result;
            else
                return FALSE;
        }else{
            return FALSE;
        }
        
    }

    function getEnc($bill_nr){
    	global $db;
    	$rs='';
    	$this->sql = "SELECT sbe.`encounter_nr`
    					FROM seg_billing_encounter `sbe`
    					WHERE sbe.`bill_nr` ='$bill_nr'";
    	if ($result=$db->Execute($this->sql)) {
                if($row=$result->FetchRow()){
                	$rs=$row['encounter_nr'];
                }
        }
        return $rs;
    }

function GetAdmission(){
	global $db;
	$rs='';
	$this->sql = "SELECT ce.`admission_dt` 
					FROM care_encounter `ce` 
					WHERE ce.`encounter_nr` =".$db->qstr($encounter_nr);
	if ($result=$db->Execute($this->sql)){
		if($row=$result->FetchRow()){
			$rs=$row['admission_dt'];
		}
	}
	return $rs;
}

function GetBillDate($bill_date){
	global $db;
	$rs='';
	$this->sql ="SELECT sbe.`bill_dte` 
				FROM seg_billing_encounter `sbe` 
				WHERE sbe.`bill_nr` =" .$db->qstr($bill_date);
	if ($result=$db->Execute($this->sql)){
		if($row=$result->FetchRow()){
			$rs=$row['bill_dte'];
		}
	}
	return $rs;
}


	/**
	* @author Jarel Mamac
	* created  on 05/07/2014
	* Check if has Well Baby Transaction
	* @param string pid
	* @return Bool
	**/
	function hasWellBabyTransaction($pid){
		global $db;
		$rs='';
		$this->sql ="SELECT encounter_nr 
					FROM care_encounter `ce` 
					WHERE encounter_type='12' AND ce.`pid` =" .$db->qstr($pid);

		 if ($this->result=$db->Execute($this->sql)){
            if ($this->count = $this->result->RecordCount())
                return TRUE;
            else
                return FALSE;
        }else{
            return FALSE;
        }
	}

	//added by EJ 09/26/2014
	function getAuxNote($encounter_nr) {
		global $db;
		$encounter_nr = $db->qstr($encounter_nr);
		$this->sql = "SELECT aux_notes 
					FROM care_encounter_notes  
					WHERE encounter_nr = $encounter_nr 
					ORDER BY nr 
					DESC LIMIT 1";

		$result = $db->Execute($this->sql);

		if ($result) {
			if($row = $result->FetchRow()) {
				return $row['aux_notes'];
			}
		}
	}

    //Added by Jarel 03102015
    function hasRecordEncounterDRAdd($encounter_nr)
    {
    	global $db;
    	$encounter_nr = $db->qstr($encounter_nr);
    	$this->sql = "SELECT encounter_nr FROM `seg_encounter_dr_add` a WHERE a.`encounter_nr` = $encounter_nr ";

		if($result = $db->GetOne($this->sql))
			return true;
		else
			return false;
	}

 	
 	//Added by Jarel 03102015
	function getEncounterDRAddList($encounter_nr,$area = '')
	{
		global $db;
		$encounter_nr = $db->qstr($encounter_nr);
		$where = '';
		if($area=='adm'){
			$where = " is_admitting = '1' AND ";
		}elseif($area=='con'){
			$where = " (is_consulting = '1' OR is_attending = '1') AND ";
		}

		$this->sql ="SELECT fn_get_personell_name(dr_nr) as name, dr_nr, is_consulting, is_admitting, is_attending  FROM seg_encounter_dr_add
					WHERE $where encounter_nr = $encounter_nr ";
		
		if($result = $db->GetAll($this->sql))
			return $result;
		else
			return false;
	}

 	
 	//Added by Jarel 03102015
	function setEncounterDRAddList($details)
	{
		global $db;
        $this->sql = "INSERT INTO seg_encounter_dr_add
                        (encounter_nr,
                         dr_nr,
                         is_attending,
                         is_admitting,
                         is_consulting,
                         create_id,
                         modify_id,
                         create_dt)
                       VALUES $details";
                             
        if($db->Execute($this->sql)){
            return true;
        }else{
            return false;
        }

	}


 	//Added by Jarel 03102015
	function getEncounterDRDetails($encounter_nr)
	{
		global $db;
		$encounter_nr = $db->qstr($encounter_nr);
		$this->sql = "SELECT fn_get_personell_name(a.`current_att_dr_nr`) as att_name , fn_get_personell_name(a.`consulting_dr_nr`) as adm_name, 
		                     a.`current_att_dr_nr`, a.`consulting_dr_nr` 
					  FROM care_encounter a 
					  WHERE a.encounter_nr = $encounter_nr";
		
		if($result = $db->GetRow($this->sql)){
			return $result;
		}else{
			return false;
		}
	}

 	
 	//Added by Jarel 03102015	
	function deleteExistingData($encounter_nr)
	{
		global $db;
		$encounter_nr = $db->qstr($encounter_nr);
		$this->sql ="DELETE FROM seg_encounter_dr_add WHERE encounter_nr = $encounter_nr";

		if($db->Execute($this->sql)){
            return true;
        }else{
            return false;
        }
	}


	function deleteEncounterDRDetails($encounter_nr,$dr_nr)
	{
		global $db;
		$encounter_nr = $db->qstr($encounter_nr);
		$dr_nr = $db->qstr($dr_nr);
		$this->sql ="DELETE FROM seg_encounter_dr_add WHERE encounter_nr = $encounter_nr AND dr_nr = $dr_nr";

		if($db->Execute($this->sql)){
            return true;
        }else{
            return false;
        }

	}


	function updateEncounterDRCharge($encounter_nr,$dr_nr,$charge)
	{
		global $db;
		$encounter_nr = $db->qstr($encounter_nr);
		$dr_nr = $db->qstr($dr_nr);
		$charge = $db->qstr($charge);

		$this->sql ="UPDATE seg_encounter_dr_add SET dr_charge = $charge WHERE encounter_nr = $encounter_nr AND dr_nr = $dr_nr";
		
		if($db->Execute($this->sql)){
            return true;
        }else{
            return false;
        }

	}


		//Added by Jarel 05112015
	function hasSavedBillOnly($encounter_nr){
        global $db;
        
        $this->sql ="SELECT bill_nr FROM seg_billing_encounter
                     WHERE encounter_nr=".$db->qstr($encounter_nr)."
                     AND (is_deleted=0 OR is_deleted IS NULL)
                     ORDER BY bill_dte DESC LIMIT 1";

        $result = $db->GetOne($this->sql);
        if($result){
        	return true;
        }else{
        	return false;
        }

    }

    function getReferralReason() {
    	global $db;

    	return $db->GetAll("SELECT 
							  reason 
							FROM
							  seg_referral_reason");
    }

    function getPatientReferralReason($encounter_nr) {
    	global $db;

    	return $db->GetOne("SELECT 
							  referral_reason_id
							FROM
							  seg_encounter_disposition 
							WHERE encounter_nr = ?", $encounter_nr);
    }


    function checkIfFinalBill($enc){
    	global $db;
    	$enc = $db->qstr($enc);
    	return $db->getOne("SELECT 
						  sbe.bill_nr
						FROM
						  seg_billing_encounter sbe 
						WHERE sbe.bill_nr = 
						  (SELECT 
						    MAX(bill_nr) 
						  FROM
						    seg_billing_encounter sbe2 
						  WHERE sbe2.`encounter_nr` = $enc) 
						  AND sbe.`is_final` = 1 AND sbe.`is_deleted` IS NULL");
    }

}# end of class Encounter
?>