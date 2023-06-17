<?php
/**
* @package care_api
*/
require_once($root_path.'include/care_api_classes/class_notes.php');
/**
*  Medocs methods. Medocs = Textual documentation for diagnosis and therapy procedures as opposite of the DRG (code based documentation).
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance
*/

class Medocs extends Notes {
	/**
	* Table name for feeding types
	* @var string
	*/
	var $tb_medocs='care_type_feeding';
	/**
	* Table name for person registration data
	* @var string
	*/

	var $fld_primary_key;

	var $fld_primary_name;

	var $fld_foreign_key;

	var $fld_foreign_name;

	var $tb_foreign;

	var $tb_care_encounter_diagnosis = 'care_encounter_diagnosis';

	var $tb_care_encounter_procedure = 'care_encounter_procedure';

	var $tb_person='care_person';

	var $tb_seg_encounter_diagnosis = 'seg_encounter_diagnosis';

	var $tb_seg_encounter_icd='seg_encounter_icd';

	var $tb_seg_encounter_icp='seg_encounter_icp';

	var $tb_seg_encounter_result= 'seg_encounter_result';

	var $tb_seg_encounter_disposition = 'seg_encounter_disposition_refer';

	var $tb_seg_encounter_condition = 'seg_encounter_condition';



	var $fld_seg_encounter_result=array("encounter_nr",
										"result_code",
										"modify_id",
										"modify_time",
										"create_id",
										"create_time"
										);

	var $fld_seg_encounter_disposition=array("encounter_nr",
											"disp_code",
											"modify_id",
											"modify_time",
											"create_id",
											"create_time"
										);

	var $fld_seg_encounter_icd=array("encounter_nr",
									"diagnosis_code",
									"modify_id",
									"create_id",
									"create_time"
									);

	var $fld_seg_encounter_icp=array("encounter_nr",
									"procedure_code",
									"modify_id",
									"create_id",
									"create_time"
								);
	var $fld_seg_encounter_condition=array( "encounter_nr",
										"cond_code",
										"modify_id",
										"create_id",
										"create_time"
								);

	var $ok;
	/**
	* Constructor
	* @param int Encounter number
	*/
	function Medocs($nr=0){
		if($nr) $this->enc_nr=$nr;
		$this->coretable=$this->tb_medocs;
	}

	//Change by Mark on March 29, 2007
	function _useTable($tbl){
		switch($tbl){
			case "result":
				$this->coretable = $this->tb_seg_encounter_result;
				$this->ref_array = $this->fld_seg_encounter_result;
				$this->fld_primary_key = 'encounter_nr';
				$this->tb_foreign = 'seg_results';
				$this->fld_foreign_key = 'result_code';

				break;
			case "disposition":
				$this->coretable = $this->tb_seg_encounter_disposition;
				$this->ref_array = $this->fld_seg_encounter_disposition;
				$this->fld_primary_key = 'encounter_nr';
				$this->tb_foreign = 'seg_dispositions';
				$this->fld_foreign_key = 'disp_code';

				break;
			case "cond":
				$this->coretable = $this->tb_seg_encounter_condition;
				$this->ref_array = $this->fld_seg_encounter_condition;
				$this->tb_foreign = 'seg_conditions';

				break;
		}
	}


	//-	array_keys($_POST['chk'])
	function _insertDataResultDisp(&$array){
		$x='';
		$v='';
		$index='';
		$values='';
		if(!is_array($array)) return false;
		while(list($x,$v)=each($array)) {
				$index.="$x,";
				$values.="'$v',";
		}
		$index=substr_replace($index,'',(strlen($index))-1);
		$values=substr_replace($values,'',(strlen($values))-1);

		$this->sql="INSERT INTO $this->coretable ($index) VALUES ($values)";
		return $this->Transact();
	}

	function saveResultDispFromArray($data,$flag=0){
		global $HTTP_SESSION_VARS, $db;

		if ($flag==0){
			//$this->_useResult();
			$this->_useTable("result");
			$code='result_code';
		}else{
			//$this->_useDisposition();
			$this->_useTable("disposition");
			$code='disp_code';
		}

		$arr = array();
		foreach ($data as $i=>$v) {
			$temp=array($v);
			array_push($arr,$temp);
		}

		$sql ="INSERT INTO $this->coretable (encounter_nr,$code,modify_id,modify_time,create_id,create_time) ".
		"VALUES ('".$HTTP_SESSION_VARS['sess_en']."',?,'".$HTTP_SESSION_VARS['sess_user_name']."', NOW(),'".$HTTP_SESSION_VARS['sess_user_name']."', NOW())";

		//if($ok=$db->Execute($sql,$arr)){
		$db->BeginTrans();
		$this->ok=$db->Execute($sql,$arr);
		if($this->ok){
			$db->CommitTrans();
			return TRUE;
		}else{
			$db->RollbackTrans();
			return false;
		}

	}

		/**
		* @internal     Save alternate description of ICD code for printing in Form 2.
		* @access       public
		* @author       Bong S. Trazo
		* @package      include
		* @subpackage   care_api_classes
		* @global       db - database object
		*
		* @param        enc_nr      - Encounter no.
		* @param        code        - Diagnosis code
		* @param        desc        - Alternate description
		* @param        user_id     - User id (session user name).
		* @return       boolean TRUE if successful, FALSE otherwise.
		*/
		function saveAltDesc($enc_nr, $code, $desc, $user_id) {
				global $db;

				if ($desc != "") {
						// Save alternate description ...
						$fldArray = array('encounter_nr'=>"'$enc_nr'", 'code'=>"'$code'", 'description'=>"'$desc'", 'is_deleted'=>"0", 'modify_id'=>"'$user_id'");
						return ($db->Replace('seg_encounter_diagnosis', $fldArray, array('encounter_nr', 'code')));
				}
				else {
						// Mark 'deleted' alternate description if a blank description is saved ...
						$this->sql = "update seg_encounter_diagnosis set
														 is_deleted = 1,
														 modify_id = '$user_id',
														 description = ''
														 where encounter_nr = '$enc_nr'
																and code = '$code'";
						return($this->Transact($this->sql));
				}
		}

		/**
		* @internal     Save alternate description of ICD code for printing in Form 2.
		* @access       public
		* @author       Bong S. Trazo
		* @package      include
		* @subpackage   care_api_classes
		* @global       db - database object
		*
		* @param        enc_nr      - Encounter no.
		* @param        code        - Diagnosis code
		* @param        desc        - Alternate description
		* @param        user_id     - User id (session user name).
		* @return       boolean TRUE if successful, FALSE otherwise.
		*/
		function saveAltDiagDesc($enc_nr, $code, $desc, $user_id = '') {
				global $db;

				$description = str_replace("'", "\'", $desc);//added by genz

				if($user_id != '')
					$user_id = "modify_id = '$user_id',";

				if ($desc != "") {
						// Save alternate description ...
						// $fldArray = array('encounter_nr'=>"'$enc_nr'", 'code'=>"'$code'", 'diagonosis_description'=>"'$desc'", 'status'=>"''", 'modify_id'=>"'$user_id'");
						// return ($db->Replace('care_encounter_diagnosis', $fldArray, array('encounter_nr', 'code')));
						$this->sql = "update care_encounter_diagnosis set														
														 $user_id
														 diagnosis_description = '$description'
														 where encounter_nr = '$enc_nr'
																and code = '$code'";
																
						// $description = ucfirst(strtolower($description));
						$sql2 = "update seg_encounter_diagnosis set														
														 $user_id
														 description = '$description'
														 where encounter_nr = '$enc_nr'
																and code = '$code'";
						return($this->Transact($this->sql) &&  $this->Transact($sql2));
				}
				else {
						// Mark 'deleted' alternate description if a blank description is saved ...
						$this->sql = "update care_encounter_diagnosis set
														 status = 'deleted',
														 $user_id
														 diagonosis_description = ''
														 where encounter_nr = '$enc_nr'
																and code = '$code'";

						$sql2 = "update seg_encounter_diagnosis set		
															is_deleted = 1,												
														 	$user_id
														 	description = '$description'
														 	where encounter_nr = '$enc_nr'
																and code = '$code'";									
						return($this->Transact($this->sql) &&  $this->Transact($sql2));
				}
		}


        //added by jasper 06/30/2013
        function saveAltCode($enc_nr, $code, $altcode, $user_id) {
                global $db;

                if ($altcode != "") {
                        // Save alternate description ...
                        $fldArray = array('encounter_nr'=>"'$enc_nr'", 'code'=>"'$code'", 'code_alt'=>"'$altcode'", 'is_deleted'=>"0", 'modify_id'=>"'$user_id'");
                        return ($db->Replace('seg_encounter_diagnosis', $fldArray, array('encounter_nr', 'code')));
                }
                /*else {
                        // Mark 'deleted' alternate description if a blank description is saved ...
                        $this->sql = "update seg_encounter_diagnosis set
                                                         is_deleted = 1,
                                                         modify_id = '$user_id',
                                                         description = ''
                                                         where encounter_nr = '$enc_nr'
                                                                and code = '$code'";
                        return($this->Transact($this->sql));
                }*/
        }
        //added by jasper 06/30/2013

	function _getResult($area_used){
		global $db;
		//$this->_useResult();
		$this->_useTable("result"); // Added by Mark on March 29, 2007
		$this->sql = "SELECT * FROM $this->tb_foreign WHERE area_used='$area_used'"; //OR area_used='NULL'
		if($this->res2['re']=$db->Execute($this->sql)){
			if($rec1=$this->res2['re']->RecordCount()){
				return $this->res2['re'];
			}else { return FALSE;}
		}else {return  FALSE;}
	}

	function _getDisp($area_used){
		global $db;
		//$this->_useDisposition();
		$this->_useTable("disposition"); // Added by Mark on March 29, 2007
		$this->sql = "SELECT * FROM $this->tb_foreign WHERE area_used='$area_used'";// OR area_used='NULL'";
		if($this->res3['rf']=$db->Execute($this->sql)){
			if($rec5=$this->res3['rf']->RecordCount()){
				return $this->res3['rf'];
			}else { return FALSE;}
		}else {return  FALSE;}
	}


	function _getCondition($area_used){
		global $db;
		$this->_useTable("cond");
		$this->sql="SELECT * FROM $this->tb_foreign WHERE area_used='$area_used'";
		if($this->res4['rt']=$db->Execute($this->sql)){
			if($rec6=$this->res4['rt']->RecordCount()){
				return $this->res4['rt'];
			}else{ return FALSE; }
		}else{ return FALSE;}
	}//End of function _getCondition(default='E')

/*
	function _getResultList($resultCode, $area_used='A'){
		global $db;

		if(empty($resultCode)) return FALSE;
		$this->_useResult();
		$this->sql="SELECT * FROM $this->coretable ".
							"\n WHERE result_code ='".$result_code."' " .
							"\n AND (area_used = '".$area_used."' OR area_used='NULL')";
		if($this->res['cc']=$db->Execute($this->sql)){
			if($this->rec=$this->res['cc']->RecordCount()){
				return $this->res['cc'];
			}else {return FALSE; }
		}else { return FALSE; }
	}

	*/
	/**
	* Gets all medocs documents based on the given key number.
	*
	* The type of key number is determined by the content of the $nr_type parameter.
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the encounter data with the following index keys:
	* - nr = record's primary key number
	* - encounter_nr = encounter number
	* - date= date of documentation
	* - time = time of documentation
	* - notes = the document text
	* - is_discharged = discharge status of encounter
	*
	* @access private
	* @param int Key number
	* @param string  Type of  key number. '_ENC' = encounter nr, '_REG' = pid nr.
	* @return mixed adodb record object or boolean
	*/
	function _getMedocsList($nr,$nr_type='_ENC'){
		global $db;
		# type nr 12 = diagnosis text notes
		if($nr_type=='_ENC'){
			$this->sql="SELECT n.nr,n.encounter_nr,n.date,n.time,n.notes,e.is_discharged  FROM   $this->tb_notes, $this->tb_enc AS e
				WHERE n.encounter_nr=".$nr." AND n.encounter_nr=e.encounter_nr AND n.type_nr=12 AND n.status NOT IN ($this->dead_stat)
				ORDER BY n.date DESC";
		}elseif($nr_type='_REG'){
			$this->sql="SELECT  n.nr,n.encounter_nr,n.date,n.time,n.notes,e.is_discharged  FROM $this->tb_person AS p, $this->tb_notes AS n, $this->tb_enc AS e
				WHERE p.pid=".$nr." AND e.pid=p.pid AND e.encounter_nr=n.encounter_nr AND n.type_nr=12 AND n.status NOT IN ($this->dead_stat)
				ORDER BY n.date DESC";
		}
		//echo $this->sql;
				if($this->res['_gmed']=$db->Execute($this->sql)) {
						if($this->rec_count=$this->res['_gmed']->RecordCount()) {
				 return $this->res['_gmed'];
			} else { return false; }
		} else { return false; }
	}
	/**
	* Gets all medocs records of an encounter number.
	*
	* For detailed structure of returned data, see the <var> _getMedocsList()</var> method.
	* @access public
	* @param int Encounter number
	* @return mixed adodb record object or boolean
	*/
	function encMedocsList($nr){
		return $this->_getMedocsList($nr,'_ENC');
	}
	/**
	* Gets all medocs records of a pid number.
	*
	* For detailed structure of returned data, see the <var> _getMedocsList()</var> method.
	* @access public
	* @param int PID number
	* @return mixed adodb record object or boolean
	*/
	function pidMedocsList($nr){
		return $this->_getMedocsList($nr,'_REG');
	}

	/**
	* Gets medocs document based on a field "nr" key
	*
	* The returned  array has the following keys:
	* diagnosis = Diagnosis text
	* short_notes = Short diagnosis notes
	* aux_notes = Auxilliary notes
	* date = Date of creation
	* time = Time of creation
	* personell_nr = Personnel number who created the document
	* personell_name = Personnel name
	* therapy = Therapy text
	* @access public
	* @param int document number
	* @return mixed array or boolean
	*/
	function getMedocsDocument($nr){
		global $db;
		if(empty($nr)) return FALSE;
		$this->sql="SELECT nd.notes AS diagnosis,
						nd.short_notes,
						nd.aux_notes,
						nd.date,
						nd.time,
						nd.personell_nr,
						nd.personell_name,
						nt.notes AS therapy
		FROM $this->tb_notes AS nd LEFT JOIN $this->tb_notes AS nt ON nd.nr=nt.ref_notes_nr
		WHERE   nd.nr=$nr";

				if($this->res['gmd']=$db->Execute($this->sql)) {
						if($this->rec_count=$this->res['gmd']->RecordCount()) {
				 return $this->res['gmd']->FetchRow();
			} else { return false; }
		} else { return false; }
	}


	function useCode($target){

		if($target=="icd"){
			//if($enc_diagnosis == 0){ //if $enc_diagnosis = 0 -> care_encounter_diagnosis else seg_encounter_icd
				$this->coretable=$this->tb_care_encounter_diagnosis;
			//}else{
			//	$this->coretable=$this->tb_seg_encounter_icd;
			//	$this->ref_array=$this->fld_seg_encounter_icd;
			//}

		}elseif($target=="icp"){  // target== icp
			//if($enc_diagnosis==0){  // if enc_diagnosis = 0 -> care_encounter_procedure else seg_encounter_icp
				$this->coretable=$this->tb_care_encounter_procedure;
			//}else{
			//	$this->coretable=$this->tb_seg_encounter_icp;
			//	$this->ref_array=$this->fld_seg_ecounter_icp;
			//}
		}
	}// End of Function useCode()


//added by daryls
//insert data from seg_encounter_diagnoses
//11/15/2013
	function save_Seg_encounter_diagnoses($encounter,$code='',$create_id='',$descr='',$type_nr='0'){
			global $db;
			$this->sql= "INSERT INTO $this->tb_seg_encounter_diagnosis 
										(encounter_nr,
											type_nr,
											code,
											description,
											modify_id,
											modify_time,
											create_id,
											create_time )
						 VALUES(".$db->qstr($encounter).",
						 		".$db->qstr($type_nr).",
						 		".$db->qstr($code).",
						 		".$db->qstr($descr).",
						 		".$db->qstr($create_id).",
						 		NOW(),
						 		".$db->qstr($create_id).",
						 		NOW())";

		
							return $this->Transact();
	}

	
	// Use for direct saving to database.
	//function AddCode($encounter,$encounter_type,$date,$code,$clinician,$dept_nr,$create_id,$target,$type){
	function AddCode($encounter,$encounter_type,$date,$code,$clinician,$dept,$create_id,$target,$type,$frombilling=''){
		$this->useCode($target);

		$history ="Create ".date('Y-m-d H:i:s')." ".$create_id."\n";//$HTTP_SESSION_VARS['sess_user_name']; //."\n\r"

		// Added by Mark on March March 31, 2007
		if($type=='0') $type_nr = 0; //other diagnosis
		else $type_nr = 1;  // final diagnosis or principal diagnosis

		if($target=="icd"){
			#added by genz
				$icdObj=new Icd($code);
				if($rw=$icdObj->getIcd10Info($code));
					$desc=$rw->FetchRow();
					$descr= explode("'", $desc['description']);
					$description = implode('', $descr);
					$diagnosis_description = strtoupper($description);
			#ended by genz

			$code_parent=substr($code,0,4);
			$code_parent = $code_parent."-";
			//$encounter_type == 3 || $encounter_type == 4
			if($frombilling==''){
				if ( ( empty($dept) || $dept == 0) && ($encounter_type == 3 || $encounter_type == 4 ) ){
					//without department
					//save to care_encounter_diagnosis
					$sql= "INSERT INTO $this->coretable(encounter_nr,encounter_type,type_nr,date,code,code_parent, ".
						"\n history,modify_id, modify_time,create_id,create_time,diagnosis_description) ".
						"\n VALUES('$encounter','$encounter_type','$type_nr','$date','$code','$code_parent', ".
						"\n '$history','$create_id',NOW(),'$create_id',NOW(),'$diagnosis_description')";
				}else{ //with department
					$sql = "INSERT INTO $this->coretable(encounter_nr, encounter_type, type_nr, date, code, code_parent, " .
						"\n diagnosing_clinician, diagnosing_dept_nr, history, modify_id, modify_time, create_id, create_time,diagnosis_description) ".
						"\n VALUES ('$encounter','$encounter_type','$type_nr','$date','$code','$code_parent', ".
						"\n '$clinician','$dept','$history','$create_id',NOW(),'$create_id', NOW(),'$diagnosis_description')";
				}
			}else{
						$sql = "INSERT INTO $this->coretable(encounter_nr, encounter_type, type_nr, date, code, code_parent, " .
						"\n diagnosing_clinician, diagnosing_dept_nr, history, modify_id, modify_time, create_id, create_time,status) ".
						"\n VALUES ('$encounter','$encounter_type','$type_nr','$date','$code','$code_parent', ".
						"\n '$clinician','$dept','$history','$create_id',NOW(),'$create_id', NOW(),'added')";
			}
			
			$this->sql = $sql;
		}elseif($target=="icp"){ // for icp
			#echo "target = ".$target;
			$code_parent=substr($code,0,4);
			$code_parent = $code_parent."-";
			//$encounter_type == 3 || $encounter_type == 4
			if((empty($dept) || $dept == 0) && ($encounter_type == 3 || $encounter_type == 4) ){ //without dept selected
				//create new data care_encounter_procedure
				$sql="INSERT INTO $this->coretable(encounter_nr,encounter_type,type_nr,date,code,code_parent, ".
					 "\n history,modify_id, modify_time,create_id,create_time) ".
					 "\n VALUES('$encounter','$encounter_type','$type_nr','$date','$code','$code_parent',".
					 "\n '$history','$create_id',NOW(),'$create_id',NOW())";
			}else{ //with department selected
				#echo "here";
				$sql = "INSERT INTO $this->coretable(encounter_nr, encounter_type, type_nr, date, code, code_parent, ".
					"\n responsible_clinician, responsible_dept_nr,history, modify_id, modify_time, create_id, create_time) ".
					"\n VALUES ('$encounter', '$encounter_type','$type_nr','$date','$code','$code_parent', ".
					"\n '$clinician','$dept','$history','$create_id',NOW(),'$create_id',NOW())";
			}

			$this->sql = $sql;

			#echo $this->sql;
		}
		#echo $this->sql;
		return $this->Transact();
	}//end AddCode	(ok March 28, 2007 by Mark)


	// Remarks : FIXED by mark March 28, 2007
	//Note: remove $enc_type Mark March 28, 2007
	function removeCode($encounter,$code,$target,$create_id){
		$this->useCode($target,$tabs);

		$history =$this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$create_id."\n");

		// Added by Mark on March March 31, 2007
		//if($tabs==0) $type_nr = 0; //other diagnosis
		//else $type_nr = 1; // final diagnosis or principal diagnosis

		if($target=="icd"){
			$this->sql = "UPDATE $this->coretable SET status='deleted',history=".$history." ".
					"\n WHERE encounter_nr='$encounter' AND code='$code'; ";
			$sql2 .= "UPDATE seg_encounter_diagnosis SET is_deleted='1'".
					"\n WHERE encounter_nr='$encounter' AND code='$code'; ";

			return($this->Transact($this->sql) && $this->Transact($sql2));
		}elseif($target=="icp"){
		//	if($tabs==0){ // delete logically the data  care_encounter_procedure
				//$sql ="DELETE FROM $this->coretable WHERE encounter_nr='$encounter' AND code='$code'";
				$sql = "UPDATE $this->coretable SET status='deleted',history=".$history." ".
						"\n WHERE encounter_nr='$encounter' AND code='$code'";
		//	}else{ // seg_encounter_procedure
				//$sql = "DELETE FROM $this->coretable WHERE encounter_nr='$encounter' AND procedure_code='$code'";
		//		$sql = "UPDATE $this->coretable SET status='deleted' ".
		//				"\n WHERE encounter_nr='$encounter' AND procedure_code='$code'";
		//	}
			$this->sql = $sql;
			return $this->Transact();
		}
		
	}

	// Added by LST -- 03.27.2009 ------
		function removeICDCode($diagnosis_nr, $create_id){
				$this->useCode('icd',$tabs);

				//$history =$this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$create_id."\n");
				$this->sql = "UPDATE $this->tb_seg_encounter_diagnosis SET is_deleted='1' WHERE diagnosis_nr = $diagnosis_nr";
				return $this->Transact();
		}

		// Added by LST -- 03.30.2009 ------
		function getLatestDiagnosisNr() {
				global $db;

				$this->sql = "select diagnosis_nr from $this->coretable Order by diagnosis_nr DESC Limit 1";
				if ($this->result = $db->Execute($this->sql)) {
						if ($row = $this->result->FetchRow())
								return $row["diagnosis_nr"];
						else
								return FALSE;
				}
				else
						return FALSE;
		}

		
		function getSEDwithDiagnosisNr($diagnosis_nr) {
				global $db;

				$this->sql = "
							SELECT encounter_nr, code 
							from $this->tb_seg_encounter_diagnosis sed
							where diagnosis_nr = '$diagnosis_nr'
							AND is_deleted = 0
							";
				if ($this->result = $db->Execute($this->sql)) {
						if ($row = $this->result->FetchRow())
								return array(
									$row["encounter_nr"],
									$row["code"]
								);
						else
								return FALSE;
				}
				else
						return FALSE;
		}

		function getMiscOpsWithReff($reff) {
				global $db;

				$this->sql = "
							SELECT smo.encounter_nr, smod.ops_code AS code 
							from seg_misc_ops smo
							left join seg_misc_ops_details smod on smod.refno = smo.refno
							where smo.refno = '$reff'
							";
				if ($this->result = $db->Execute($this->sql)) {
						if ($row = $this->result->FetchRow())
								return array(
									$row["encounter_nr"],
									$row["code"]
								);
						else
								return FALSE;
				}
				else
						return FALSE;
		}

	#added by VAN 03-31-08
	function getICD_ICP($target, $searchkey) {
			global $db;

		if ($target=='icd'){
			$this->sql="SELECT diagnosis_code AS code, description
							FROM care_icd10_en
							WHERE (diagnosis_code LIKE '$searchkey%')
							ORDER BY diagnosis_code LIMIT 10";

		}else{

			$this->sql="SELECT code, description
							FROM care_ops301_en
							WHERE (code LIKE '$searchkey%')
							ORDER BY code LIMIT 10";

			
			/*
			$this->sql="SELECT code, description
							FROM seg_ops_rvs
							WHERE (code LIKE '$searchkey%')
							AND is_active <> 0
							ORDER BY code LIMIT 20";
			*/

		}
		#echo "sql = ".$this->sql;

		if($this->result=$db->Execute($this->sql)){
				if($this->count=$this->result->RecordCount()) {
				return $this->result;
			} else {return FALSE;}
		}else {return FALSE;}
	}
    
    //---notification
    function getSelectedNotification($sfilter = '') {
        global $db;

        $sfilter = trim($sfilter);
        
        $this->sql = "SELECT id, description \n
                         FROM seg_medrec_notifications  \n
                         WHERE id LIKE '$sfilter%' AND  \n
                            description <> '' \n
                            ORDER BY id";                    
        
        if ($this->result = $db->Execute($this->sql)) {
            if ($this->result->RecordCount())
                return $this->result;
            else
                return FALSE;
        }
        else
            return FALSE;
    }
    
    function getSelectedNotificationDesc($sfilter = '') {
        global $db;
                
        $char_array = array(".","'","{","}","[","]","^","(",")","-","`",",","|");
        $sfilter = str_replace($char_array,"",$sfilter);
        $sfilter = trim($sfilter);
        $this->sql = "SELECT id, description  \n
                        FROM seg_medrec_notifications \n
                        WHERE REPLACE(description,',','') REGEXP '.*$sfilter.*' AND \n
                        description <> '' \n
                        ORDER BY description";
        if ($this->result = $db->Execute($this->sql)) {
            if ($this->result->RecordCount())
                return $this->result;
            else
                return FALSE;
        }
        else
            return FALSE;
    }
    
    function getNotificationInfo($id=''){
        global $db;

        if(empty($id)) return FALSE;
        $this->sql="SELECT * FROM seg_medrec_notifications WHERE id=".$db->qstr($id);
        
        if($this->res['mcode']=$db->Execute($this->sql)){
            if($this->res['mcode']->RecordCount()){
                return $this->res['mcode'];
            }else{return FALSE; };
        }else{return FALSE; }
    }
    
    function AddNotificationCode($encounter_nr, $id, $request_date){
        global $db;
        
        $encoder = $_SESSION['sess_temp_userid'];
        $date_created = date("Y-m-d H:i:s");
        
        if (trim($request_date) == ''){
            $request_date = 'NULL';
        }else{
            $request_date = "'".date("Y-m-d",strtotime($request_date))."'";
        }    
        
        
         $history = $this->ConcatHistory("Updated ".$date_created." ".$encoder."\n");
                
        $result = $db->Replace('seg_medrec_enc_notification',
                                            array(
                                                     'encounter_nr'=>$db->qstr($encounter_nr),
                                                     'notification_id'=>$db->qstr($id),
                                                     'date_requested' =>$request_date,
                                                     'is_deleted' => '0',
                                                     'history'=>$history,
                                                     'create_id'=>$db->qstr($encoder),
                                                     'create_dt'=>$db->qstr($date_created),
                                                     'modify_id'=>$db->qstr($encoder),
                                                     'modify_dt'=>$db->qstr($date_created)
                                                ),
                                                array('encounter_nr','notification_id'),
                                                $autoquote=FALSE
                                           );
                                           
         if ($result) 
            return TRUE;
         else
            return FALSE;
    }
    
    function removeNotificationCode($encounter_nr,$id){
        global $db;
        
        $encoder = $_SESSION['sess_temp_userid'];
        $date_created = date("Y-m-d H:i:s");
        
        $history =$this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$create_id."\n");

        $this->sql = "UPDATE seg_medrec_enc_notification SET 
                        is_deleted='1',
                        history=".$history." ".
                        "WHERE encounter_nr='$encounter_nr' AND notification_id='$id'";
                        
       if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret)    return TRUE;
        else return FALSE; 
    }
    
    function getNotificationEnc($enc_nr=0){
        global $db;

        $this->sql="SELECT n.description, ne.* 
                    FROM seg_medrec_enc_notification ne 
                    INNER JOIN seg_medrec_notifications n ON n.id=ne.notification_id
                    WHERE encounter_nr=".$db->qstr($enc_nr);

        if($this->result=$db->Execute($this->sql)) {
            if($this->result->RecordCount()) {
                return $this->result;
            } else { return FALSE;}
        } else { return FALSE;}
    }
    
    #added by VAN 06-06-2013
    function AddNotification($id, $description){
        global $db;
        
        $result = $db->Replace('seg_medrec_notifications',
                                    array(
                                         'id'=>$db->qstr(mb_strtoupper($id)),
                                         'description'=>$db->qstr($description),
                                         'term' =>'(NULL)'
                                        ),
                                    array('id'),
                                    $autoquote=FALSE
                                   );
         
         if ($result) 
            return TRUE;
         else
            return FALSE;
    }
    
    //--------------end notification

    var $error;

    #added by VAN 06-10-2013
    function getOperationsEnc($enc_nr=0){
        global $db;

        $this->sql="SELECT DISTINCT s.description, d.ops_code, s.rvu, d.op_date, 
                    COUNT(*) AS quantity
                    FROM seg_misc_ops h
                    INNER JOIN seg_misc_ops_details d ON d.refno=h.refno
                    INNER JOIN seg_ops_rvs s ON s.CODE=d.ops_code
                    WHERE h.encounter_nr=".$db->qstr($enc_nr)."
                    GROUP BY d.ops_code, DATE(d.op_date)";

        if($this->result=$db->Execute($this->sql)) {
            if($this->result->RecordCount()) {
                return $this->result;
            } else { return FALSE;}
        } else { return FALSE;}
    }

    #added by Nick, 3/1/2014
    function updateIcdAltCode($data){
    	global $db;
    	$this->sql = $db->Prepare("UPDATE seg_encounter_diagnosis SET 
    		                      	code_alt=?,
    		                      	modify_id=?
    		                      WHERE encounter_nr = ? AND code = ? AND is_deleted=0");
    	$rs = $db->Execute($this->sql,$data);
    	if($rs){
    		return $db->Affected_Rows();
    	}else{
    		return false;
    	}
    }

    #added by Nick, 3/1/2014
    function updateIcdAltDesc($data){
    	global $db;
    	$this->sql = $db->Prepare("UPDATE seg_encounter_diagnosis SET 
    		                      	description=?,
    		                      	modify_id=?
    		                      WHERE encounter_nr = ? AND code = ? AND is_deleted=0");
    	$rs = $db->Execute($this->sql,$data);
    	if($rs){
    		return $db->Affected_Rows();
    	}else{
    		return false;
    	}
    }

    #added by Nick, 3/5/2014
    function updateIcpAltDesc($data){
    	global $db;
    	$this->sql = $db->Prepare("UPDATE seg_misc_ops_details SET 
    		                      	description=?
    		                      WHERE refno=? AND ops_code=?");
    	$rs = $db->Execute($this->sql,$data);
    	if($rs){
    		return $db->Affected_Rows();
    	}else{
    		return false;
    	}
    }

    function addDiffCode($data){
    	global $db;
    	$this->sql = $db->Prepare("INSERT INTO ");
    }

    #added by Nick, 4/15/2014
    function updateIcdEntryNo($data){
    	global $db;
    	$this->sql = $db->Prepare("UPDATE seg_encounter_diagnosis SET entry_no = ? WHERE encounter_nr = ? AND code = ?");
    	$rs = $db->Execute($this->sql,$data);
    	if(!$rs){
    		return false;
    	}
    }

    #added by Nick, 4/15/2014
    function updateIcdSequence($encounter_nr,$icd_list){
    	$index = 0;
    	$hasFailures = false;
    	foreach ($icd_list as $ikey => $icd) {
    		$index++;
    		$data = array($index,$encounter_nr,$icd);
    		if(!$this->updateIcdEntryNo($data)){
    			$hasFailures = true;
    		}
    	}
    	return $hasFailures;
    }

}
?>