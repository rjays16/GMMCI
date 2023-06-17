<?php
/**
* @package care_api
*/

/**
*/

require_once($root_path.'include/care_api_classes/class_encounter.php');
/**
*  Ward methods.
*  Note this class should be instantiated only after a "$db" adodb  connector object  has been established by an adodb instance.
* @author Elpidio Latorilla
* @version beta 2.0.1
* @copyright 2002,2003,2004,2005,2005 Elpidio Latorilla
* @package care_api
*/
class Ward extends Encounter {
	/**#@+
	* @access private
	*/
	/**
	* Table name for ward data
	* @var string
	*/
		var $tb_ward='care_ward';
	/**
	* Table name for department data
	* @var string
	*/
	var $tb_dept='care_department';
	/**
	* Table name for room data
	* @var string
	*/
	var $tb_room='care_room';
	/**
	* Table name for encounter notes
	* @var string
	*/
	var $tb_notes='care_encounter_notes';
	/**
	* Table name for accomodation type
	* @var string
	* burn added; September 24, 2007
	*/
	var $tb_accomodation_type = 'seg_accomodation_type';
	/**
	* Ward number buffer
	* @var int
	*/
	var $ward_nr;
	/**
	* Department number buffer
	* @var int
	*/
	var $dept_nr;
	/**
	* Buffer for technical information
	* @var mixed
	*/
	var $techinfo;
	/**
	* Field names of care_ward table
	* @var array
	*/

	#added by VAN 06-10-08
	var $tb_roomtype='care_type_room';
	var $fld_roomtype=array('type',
							'name',
							'LD_var',
							'description',
							'room_rate',
							'status',
							'modify_id',
							'modify_time',
							'create_id',
							'create_time');


	var $fld_ward=array('nr',
									'accomodation_type',
									'ward_id',
									'name',
									'is_temp_closed',
									'date_create',
									'date_close',
									'description',
									'info',
									'dept_nr',
									'area_code',    //added by cha, june 15, 2010
									'room_nr_start',
									'room_nr_end',
									'roomprefix',
                                    'mandatory_excess',  // added by shan-----> 
									/*'ward_rate',*/
									'status',
									'history',
									'modify_id',
									'modify_time',
									'create_id',
									'create_time');

	/**
	*  Field names of table care_room
	* @var array
	*/
	var $fld_room=array(			'nr',
									'type_nr',
									'date_create',
									'date_close',
									'is_temp_closed',
									'room_nr',
									'ward_nr',
									'dept_nr',
									'nr_of_beds',
									'info',
									/*'room_rate',*/
									'status',   
									'history',
									'modify_id',
									'modify_time',
									'create_id',
									'create_time'
									);
	/**
	*  Field names of table seg_accomodation_type
	* @var array
	*/
	var $fld_accomodation_type=array('accomodation_nr',
								'accomodation_name',
								'status',
								'history',
								'modify_id',
								'modify_dt',
								'create_id',
								'create_dt');

	/**#@-*/

	/**
	* Constructor
	* @param int Ward number
	*/

	function Ward($ward_nr=0) {
			$this->ward_nr=$ward_nr;
		$this->Encounter();
	}
	/**
	* Sets the department number buffer.
	*
	* @access public
	*/
	function setDeptNr($dept_nr) {
			$this->dept_nr=$dept_nr;
	}
	/**
	* Sets core object to point to the care_ward table
	*
	* @access private
	*/
	function _useWard(){
		$this->ref_array=&$this->fld_ward;
		$this->coretable=$this->tb_ward;
	}

	/**
	* Sets core object to point to the care_room table
	*
	* @access private
	* @created : burn, September 28, 2007
	*/
	function _useRoom(){
		$this->coretable=$this->tb_room;
		$this->ref_array=$this->fld_room;
	}

	/**
	* Sets core object to point to the care_ward table
	*
	* @access private
	*/
	function _useAccomodationType(){
		$this->ref_array=&$this->fld_accomodation_type;
		$this->coretable=$this->tb_accomodation_type;
	}

	/**
	* Returns items of all wards.
	*
	* @access public
	* @param string Field names of items to be fetched
	* @return mixed adodb record object or boolean
	*/
	function getAllWardsItemsObject(&$items) {
			global $db;
		# burn commented : November 13, 2007 : all wards except OR ward.
#	    $this->sql="SELECT $items  FROM $this->tb_ward WHERE status NOT IN ($this->dead_stat)";   # burn commented: November 9, 2007
			$this->sql="SELECT $items,
								(SELECT d.name_formal
									FROM care_department AS d
									WHERE d.nr=dept_nr)  AS dept_name
							FROM $this->tb_ward
							WHERE status NOT IN ($this->dead_stat)
									AND nr<>0
									order by name
							";
#echo "class_ward.php : getAllWardsItemsObject :: this->sql ='".$this->sql."'";
				#echo $this->sql;
				if($this->res['gawio']=$db->Execute($this->sql)) {
						if($this->rec_count=$this->res['gawio']->RecordCount()) {
				 return $this->res['gawio'];
			} else { return false; }
		} else { return false; }
	}
	/**
	* Returns all wards data.
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the  data with the following index keys:
	* - all ward index keys as outlined in the <var>$fld_ward</var> variable
	* - dept_name = Department default name
	*
	* @access public
	* @return mixed adodb record object or boolean
	*/
	function getAllWardsDataObject() {
			global $db;
			$this->sql="SELECT w.*,d.name_formal AS dept_name  FROM $this->tb_ward AS w LEFT JOIN $this->tb_dept AS d ON w.dept_nr=d.nr
				 WHERE w.status NOT IN ('closed','deleted','hidden','inactive','void')";
				//echo $this->sql;
				if($this->result=$db->Execute($this->sql)) {
						if($this->result->RecordCount()) {
				 return $this->result;
			} else { return false; }
		} else { return false; }
	}

	/**
	* Returns items of all wards.
	*
	* Similar to getAllWardsItemsObject() but returns a 2 dimensional array.
	*
	* @access public
	* @param string Field names of items to be fetched
	* @return mixed array or boolean
	*/
	function getAllWardsItemsArray(&$items) {
			global $db;
			$this->sql="SELECT $items  FROM $this->tb_ward WHERE  status NOT IN ('hidden','deleted','closed','inactive')";
				//echo $this->sql;
				if($this->result=$db->Execute($this->sql)) {
						if($this->result->RecordCount()) {
				 return $this->result->GetArray();
			} else { return false; }
		} else { return false; }
	}
	/**
	* Returns all wards data.
	*
	* Similar to getAllWardsDataObject() but returns a 2 dimensional array.
	* Data returned have index keys as outlined in the <var>$fld_ward</var> array.
	*
	* @access public
	* @return mixed array or boolean
	*/
	function getAllWardsDataArray() {
			global $db;
			$this->sql="SELECT *  FROM $this->tb_ward WHERE 1";
				//echo $this->sql;
				if($this->result=$db->Execute($this->sql)) {
						if($this->result->RecordCount()) {
				 while($this->buffer_array=$this->result->FetchRow());
				 return $this->buffer_array;
			} else { return false; }
		} else { return false; }
		}
	/**
	* Returns ward name based on its record number.
	* @access public
	* @param int Record number
	* @return mixed string or boolean
	*/
	function WardName($nr){
			global $db;
		if(empty($nr)) return false;
				if($this->result=$db->Execute("SELECT name FROM $this->tb_ward WHERE nr=$nr")) {
						if($this->result->RecordCount()){
				 $this->row=$this->result->FetchRow();
				 return $this->row['name'];
			} else { return false; }
		} else { return false; }
	}
	/**
	* Returns ward information.
	*
	* The returned  array contains the  data with the following index keys:
	* - all ward index keys as outlined in the <var>$fld_ward</var> variable
	* - dept_name = Department default name
	*
	* @access public
	* @param int Ward number
	* @return mixed array or boolean
	*/
	function getWardInfo($ward_nr){
		global $db;
#		$this->sql="SELECT w.*,d.name_formal AS dept_name FROM $this->tb_ward AS w LEFT JOIN $this->tb_dept AS d ON w.dept_nr=d.nr
#					WHERE w.nr=$ward_nr AND w.status NOT IN ('closed',$this->dead_stat)";   # burn commented: November 13, 2007
		/*$this->sql="SELECT w.*,d.name_formal AS dept_name,
						(SELECT COUNT(*) FROM care_room WHERE ward_nr=$ward_nr AND status NOT IN ('closed',$this->dead_stat)) AS nr_of_rooms
					FROM $this->tb_ward AS w LEFT JOIN $this->tb_dept AS d ON w.dept_nr=d.nr
					WHERE w.nr=$ward_nr AND w.status NOT IN ('closed',$this->dead_stat)";   # burn added: November 13, 2007
			*/

		    $this->sql="SELECT w.*,d.name_formal AS dept_name,
		        SUM(CASE WHEN r.status NOT IN ('closed',$this->dead_stat) then 1 else 0 end) AS nr_of_rooms
		        FROM $this->tb_ward AS w
		        INNER JOIN $this->tb_room AS r ON r.ward_nr=w.nr
		        LEFT JOIN $this->tb_dept AS d ON w.dept_nr=d.nr
		        WHERE w.nr='$ward_nr' AND w.status NOT IN ('closed',$this->dead_stat)";   # burn added: November 13, 2007

#echo"class_ward.php : getWardInfo :: this->sql ='".$this->sql."' <br> \n";
				if($this->res['gwi']=$db->Execute($this->sql)) {
						if($this->rec_count=$this->res['gwi']->RecordCount()) {
				 return $this->res['gwi']->FetchRow();
			} else { return false; }
		} else { return false; }
	}

#added by VAN 01-24-08
	function getWardRoomInfo($ward_nr){
		global $db;
		$this->sql="SELECT w.*,d.name_formal AS dept_name,
						(SELECT COUNT(*) FROM care_room WHERE ward_nr=$ward_nr AND status NOT IN ('closed',$this->dead_stat)) AS nr_of_rooms, r.*, r.room_nr AS room
					FROM $this->tb_ward AS w LEFT JOIN $this->tb_dept AS d ON w.dept_nr=d.nr
					LEFT JOIN $this->tb_room AS r ON w.nr = r.ward_nr
					WHERE w.nr=$ward_nr AND w.status NOT IN ('closed',$this->dead_stat)";   # burn added: November 13, 2007
#echo"class_ward.php : getWardRoomInfo :: this->sql ='".$this->sql."' <br> \n";
				if($this->res['gwi']=$db->Execute($this->sql)) {
						if($this->rec_count=$this->res['gwi']->RecordCount()) {
				 return $this->res['gwi']->FetchRow();
			} else { return false; }
		} else { return false; }
	}


#----------------------------


	/**
	* Returns room information.
	*
	* The returned adodb record object contains a row of array.
	* This array contains the  data with index keys as outlined in the <var>$fld_room</var> variable
	*
	* @access public
	* @param int Ward number
	* @param int Starting room number
	* @param int Ending room number
	* @return mixed adodb record object or boolean
	*/
	function getRoomInfo($ward_nr,$s_nr,$e_nr){
		global $db;

		# burn commented : September 27, 2007
#		$this->sql="SELECT * FROM $this->tb_room
#						WHERE type_nr=1 AND ward_nr=$ward_nr AND room_nr  >= '$s_nr' AND room_nr <= '$e_nr' AND  status NOT IN ($this->dead_stat) ORDER BY room_nr";

		$this->sql="SELECT *
						FROM $this->tb_room
						WHERE /*type_nr=1 AND*/ ward_nr=$ward_nr
							AND  status NOT IN ($this->dead_stat)
						ORDER BY room_nr";

#echo"class_ward.php : getRoomInfo :: this->sql ='".$this->sql."' <br> \n";

		if($this->result=$db->Execute($this->sql)) {
						if($this->rec_count=$this->result->RecordCount()) {
				 return $this->result;
			} else {return false; }
		} else {return false; }
	}
	/**
	* Returns ward occupants (inpatients) information.
	*
	* The object is stored in the internal buffer <var>$result</var>.
	* This adodb record object contains rows of arrays.
	* Each array contains the  data with the following index keys:
	* - room_nr = room number
	* - room_loc_nr = primary key number of room location
	* - bed_nr = bed number
	* - encounter_nr = encounter number
	* - bed_loc_nr = primary key number of bed location
	* - name_last = person's last or family name
	* - name_first = person's first or given name
	* - date_birth = date of birth
	* - bed_nr = bed number
	* - title = person's title
	* - sex = person's sex
	* - photo_filename = filename of stored picture id
	* - insurance_class_nr = insurance class nr
	* - insurance_name = insurance class default name
	* - insurance_LDvar = variable's name for the foreign language version of the insurance class name
	* - ward_notes = ward notes record number
	*
	* @access private
	* @param int Ward number
	* @param string Date of occupancy
	* @return boolean
	*/

	#edited by VAN 02-01-08
	#function _getWardOccupants($ward_nr,$date){
	function _getWardOccupants($ward_nr,$room_nr,$date,$option,$group_nr){
		global $db, $dbf_nodate;

		#echo $option;
		#echo "<p>nka<p>";
		#added by angelo m. 10.04.2010
		#start--------------------------
		if($option=="")
			 $option='still-in';
		if($group_nr=="")
			 $group_nr=$ward_nr;
		$sql_notIn_status = "";
		switch($option){
			case 'all':
						$sql_is_discharged = ' AND e.is_discharged IN (0,1) ';
						$sql_notIn_status = "";
						break;
			case 'still-in':
						$sql_is_discharged = ' AND e.is_discharged=0 ';
						$sql_notIn_status = " 'discharged', ";
						break;
			case 'discharged':
						$sql_is_discharged = ' AND e.is_discharged=1';
						$sql_notIn_status = "";
						break;
		}
		#end--------------------------




		#die("here casdsad");
		if($date==date('Y-m-d')) $pstat='discharged';
			else $pstat='dummy';

		if ($room_nr)
			$roomsql = "AND r.location_nr='$room_nr'";
		else
			$roomsql = "";
		#echo "date = ".$date;
		#edited by VAN 04-08-08
		/*
		$this->sql="SELECT r.location_nr AS room_nr,
									r.nr AS room_loc_nr,
									b.location_nr AS bed_nr,
									b.encounter_nr,
									b.nr AS bed_loc_nr,
									p.pid,
									p.name_last,
									p.name_first,
									p.date_birth,
									p.title,
									p.sex,
									p.photo_filename,
									e.insurance_class_nr,
									i.name AS insurance_name,
									i.LD_var AS \"insurance_LDvar\",
									n.nr AS ward_notes
							FROM $this->tb_location AS r
									LEFT JOIN $this->tb_location AS b  ON 	(r.encounter_nr=b.encounter_nr
																								AND r.group_nr=b.group_nr
																								AND	b.type_nr=5
																								AND b.status NOT IN ('$pstat','closed',$this->dead_stat)
																								AND b.date_from<='$date'
																								AND ('$date'<=b.date_to OR b.date_to ='$dbf_nodate')
																								)
									LEFT JOIN $this->tb_enc AS e ON b.encounter_nr=e.encounter_nr
									LEFT JOIN $this->tb_person AS p ON e.pid=p.pid
									LEFT JOIN $this->tb_ic AS i ON e.insurance_class_nr=i.class_nr
									LEFT JOIN $this->tb_notes AS n ON b.encounter_nr=n.encounter_nr AND n.type_nr=6
							WHERE  r.group_nr=$ward_nr
											AND	r.type_nr=4
											$roomsql
											AND r.status NOT IN ('$pstat','closed',$this->dead_stat)
											AND ('$date'<=r.date_to OR r.date_to ='$dbf_nodate')
							ORDER BY r.location_nr,b.location_nr";
	*/
		$this->sql="SELECT DISTINCT r.location_nr AS room_nr,
									e.is_discharged,
									r.nr AS room_loc_nr,
									b.location_nr AS bed_nr,
									b.encounter_nr,
									b.nr AS bed_loc_nr,
									p.pid,
									p.name_last,
									p.name_first,
									p.date_birth,
									p.title,
									p.sex,
									p.photo_filename,
									e.insurance_class_nr,
									i.name AS insurance_name,
									i.LD_var AS \"insurance_LDvar\",
									n.nr AS ward_notes
							FROM $this->tb_location AS r
									LEFT JOIN $this->tb_location AS b  ON 	(r.encounter_nr=b.encounter_nr
																								AND r.group_nr=b.group_nr
																								AND	b.type_nr=5
																								AND b.status NOT IN ( $sql_notIn_status 'closed',$this->dead_stat)
																								AND b.date_from<='$date'
																								AND ('$date'<=b.date_to OR b.date_to ='$dbf_nodate')
																								)
									LEFT JOIN $this->tb_enc AS e ON b.encounter_nr=e.encounter_nr
									LEFT JOIN $this->tb_person AS p ON e.pid=p.pid
									LEFT JOIN $this->tb_ic AS i ON e.insurance_class_nr=i.class_nr
									LEFT JOIN $this->tb_notes AS n ON b.encounter_nr=n.encounter_nr AND n.type_nr=6
							WHERE  r.group_nr=$ward_nr
											AND	r.type_nr=4
											$roomsql
											AND r.status NOT IN ( $sql_notIn_status 'closed',$this->dead_stat)
											AND e.status NOT IN ($this->dead_stat) 
                                            AND ('$date'<=r.date_to OR r.date_to ='$dbf_nodate')
											$sql_is_discharged
							ORDER BY r.location_nr,b.location_nr ";

						 #echo $this->sql;
#echo"class_ward.php : _getWardOccupants :: this->sql ='".$this->sql."' <br> \n";

		if($this->result=$db->Execute($this->sql)){
			if($this->rec_count=$this->result->RecordCount()){
				//echo $this->result->RecordCount();
				//echo $this->sql.'  count';
				return true;
			}else{
				//echo $this->sql.'no count';
				return false;
			}
		}else{
			//echo $this->sql.'no sql';
			return false;}
	}
	/**
	* Returns ward occupants (inpatients) information on a given date.
	*
	* For detailed structure of the returned data, see the <var>_getWardOccupants()</var> method.
	* @access public
	* @param int Ward number
	* @param string Date of occupancy
	* @return mixed adodb record object or boolean
	*/
	#edited by VAN
	#function getDayWardOccupants($ward_nr,$date=''){
	function getDayWardOccupants($ward_nr,$room_nr='',$date='',$option='',$group_nr=''){
		if(empty($date))
			$date=date('Y-m-d');
		#edited by VAN 02-01-08
		#if($this->_getWardOccupants($ward_nr,$date)){
		if($this->_getWardOccupants($ward_nr,$room_nr,$date,$option,$group_nr)){
			return $this->result;
		}else{return false;}
	}
	/**
	* Reserved method name.
	*/
	function exitBed($loc_nr){
	}
	/**
	* Reserved method name.
	*/
	function exitRoom($loc_nr){
	}
	/**
	* Reserved method name.
	*/
	function exitWard($loc_nr){
	}
	/**
	* Closes a bed.
	*
	* @access public
	* @param int Ward number
	* @param int Room number
	* @param int Bed number
	* @return boolean
	*/
	function closeBed($ward_nr,$room_nr,$bed_nr){
		$this->sql="UPDATE $this->tb_room SET closed_beds=".$this->ConcatFieldString("closed_beds","$bed_nr/")." WHERE /*type_nr=1 AND*/ room_nr=$room_nr AND ward_nr=$ward_nr";
		//if( $this->Transact($this->sql)) return true; else echo $this->sql;
		return $this->Transact($this->sql);
	}
	/**
	* Opens a bed.
	*
	* @access public
	* @param int Ward number
	* @param int Room number
	* @param int Bed number
	* @return boolean
	*/
	function openBed($ward_nr,$room_nr,$bed_nr){
		global $dbtype;
		switch ($dbtype){
			case 'mysql': $this->sql="UPDATE $this->tb_room SET closed_beds=".$this->ReplaceFieldString("closed_beds","$bed_nr/","")." WHERE /*type_nr=1 AND*/ room_nr=$room_nr AND ward_nr=$ward_nr";
				break;
			case 'postgres':
			case 'postgres7':
				$room_obj=$this->getRoomInfo($ward_nr,$room_nr,$room_nr);
				$room_info=$room_obj->FetchRow();
				$closedbeds=str_replace("$bed_nr/","",$room_info['closed_beds']);
				$this->sql="UPDATE $this->tb_room SET closed_beds='$closedbeds' WHERE /*type_nr=1 AND*/ room_nr=$room_nr AND ward_nr=$ward_nr";
				break;
		}
		//if( $this->Transact($this->sql)) return true; else echo $this->sql;
		return $this->Transact($this->sql);
	}
	/**
	* Saves ward new ward information.
	*
	* Data passed by reference with associative array and have index keys as outlined in the <var>$fld_ward</var> array.
	* @access public
	* @param array Data to save.
	* @return boolean
	*/
	function saveWard(&$data){
		global $HTTP_SESSION_VARS;
		#print_r($data);
		$this->_useWard();
		$this->data_array=$data;
		$this->data_array['date_create']=date('Y-m-d');
		$this->data_array['history']="Create: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";
		//$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['create_time']=date('Y-m-d H:i:s');
		return $this->insertDataFromInternalArray();
	}
	/**
	* Updates a ward information.
	*
	* Data passed by reference with associative array and have index keys as outlined in the <var>$fld_ward</var> array.
	* Only the field  to be updated must be present in the array as index key to avoid replacing the wrong data.
	* @access public
	* @param int Primary key number of the ward record to be updated.
	* @param array Data to save.
	* @return boolean
	*/
	function updateWard($nr,&$data){
		global $HTTP_SESSION_VARS;
		$this->_useWard();
		$this->data_array=$data;
		// remove probable existing array data to avoid replacing the stored data
		if(isset($this->data_array['date_create'])) unset($this->data_array['date_create']);
		if(isset($this->data_array['create_id'])) unset($this->data_array['create_id']);
		// clear the where condition
		$this->where='';
		$this->data_array['history']="CONCAT(history,'Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n')";
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array['modify_time'] = date('Y-m-d H:i:s');   # burn added : September 28, 2007
		return $this->updateDataFromInternalArray($nr);
	}
	/**
	* IDExists() checks if the ward id is existing.
	* @access public
	* @param int Ward id
	* @return boolean
	*/
	function IDExists($id){
		global $db;
		$this->sql="SELECT ward_id FROM $this->tb_ward WHERE ward_id='$id' AND status NOT IN ('closed','inactive','void','deleted','hidden')";
		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount()){
				return true;
			}else{return false;}
		}else{return false;}
	}
	/**
	* Checks if there is at least one patient admitted in the ward.
	* @access public
	* @param int Ward id
	* @return boolean
	*/
	#edited by VAN 10-05-09
	#added a room_nr and ward_type as parameters
	#function hasPatient($ward_nr,$room_nr, $ward_type){
	function hasPatient($ward_nr){
		global $db;

			$this->sql="SELECT nr FROM $this->tb_location
						WHERE type_nr=4
							AND group_nr=$ward_nr
							AND date_from NOT  IN  ('0000-00-00','')
							AND date_to  IN  ('0000-00-00','')
							AND status NOT IN ('closed','inactive','void','hidden','deleted','discharged')";

		if($this->result=$db->Execute($this->sql)){
			if($this->count = $this->result->RecordCount()){
				return true;
			}else{return false;}
		}else{return false;}
	}

	/**
	* Sets the ward to "temporary closed" status.
	*
	* Toggles the is_temp_closed field of the care_ward table
	* @access private
	* @param int Primary record key number
	* @param int Flag to set. Default to 1.
	* @return boolean
	*/
	function _setIsTemporaryClosed($nr,$flag=1){
		global $HTTP_SESSION_VARS;
		$this->_useWard();
		// clear the where condition
		$this->where='';

		#edited by VAN 04-09-08
		if ($flag)
			$data['is_temp_closed']=$flag;
		else
			$data['is_temp_closed']='0';
		#echo 'data = '.$data['is_temp_closed'];
		if($flag){
			$action='Closed temporary';
		}else{
			$action='Reopened';
		}
		$data['history']="CONCAT(history,'$action: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n')";
		$data['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array=$data;
		return $this->updateDataFromInternalArray($nr);
	}
	/**
	* Closes a ward temporarily.
	* @access public
	* @param int Primary record key number
	* @return boolean
	*/
	function closeWardTemporary($nr){
			return $this->_setIsTemporaryClosed($nr,1);
	}
	/**
	* Reopens a ward that was previously closed temporarily.
	* @access public
	* @param int Primary record key number
	* @return boolean
	*/
	function reOpenWard($nr){
			return $this->_setIsTemporaryClosed($nr,0);
	}
	/**
	* Closes a ward irreversibly.
	* @access public
	* @param int Primary record key number
	* @return boolean
	*/
	function closeWardNonReversible($nr){
		global $HTTP_SESSION_VARS;
		$this->_useWard();
		// clear the where condition
		$this->where='';
		$data['date_close']=date('Y-m-d');
		$data['status']='inactive';
		$data['history']="CONCAT(history,'Closed nonreversible: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n')";
		$data['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$this->data_array=$data;
		return $this->updateDataFromInternalArray($nr);
	}
	/**
	* Returns information of all ACTIVE wards.
	*
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the  data with the following index keys:
	* - all ward index keys as outlined in the <var>$fld_ward</var> variable
	* - dept_name = Department default name
	*
	* @access public
	* @param int Primary record key number
	* @return mixed adodb record object or boolean
	*/
	function getAllActiveWards() {
			global $db;
			$this->sql="SELECT w.*,d.name_formal AS dept_name
						FROM $this->tb_ward AS w
							LEFT JOIN $this->tb_dept AS d ON w.dept_nr=d.nr
						WHERE w.status NOT IN ('closed','inactive','void','hidden','deleted')
						ORDER BY w.name";
				//echo $this->sql;
				if($this->result=$db->Execute($this->sql)) {
						if($this->result->RecordCount()) {
				 return $this->result;
			} else { return false; }
		} else { return false; }
	}

		// Added by LST - 11.11.2008 -- returns all wards for operation.
		function getAllOpWards() {
				global $db;
				$this->sql="select distinct cr.ward_nr as nr, cw.name
											 from (care_room as cr inner join care_type_room as ctr
													on cr.type_nr = ctr.nr) inner join care_ward as cw
													on cr.ward_nr = cw.nr
											 where ctr.type in ('or','op') and cw.status not in ('closed','inactive','void','hidden','deleted')";

				if($this->result=$db->Execute($this->sql)) {
						if($this->result->RecordCount()) {
								 return $this->result;
						} else { return false; }
				} else { return false; }
		}
	/**
	* Returns total number of created rooms.
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the  data with the following index keys:
	* - room_nr = room number
	* - nr_rooms  = total number of rooms
	* - nr = the primary record key
	*
	* @access public
	* @param int Primary record key number
	* @return boolean
	*/
	function countCreatedRooms(){
			global $db, $dbf_nodate;
		$this->sql="SELECT COUNT(r.room_nr) AS nr_rooms,w.nr  FROM $this->tb_room AS r, $this->tb_ward AS w
							WHERE w.nr=r.ward_nr AND r.date_close='$dbf_nodate' AND r.status NOT IN ('closed','inactive','void','hidden','deleted')
							 GROUP BY w.nr";
				if($result=$db->Execute($this->sql)) {
						if($result->RecordCount()) {
						 return $result;
			} else { return false; }
		} else { return false; }
	}
	/**
	* Returns rooms data of a ward
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the  data with the following index keys:
	* - room_nr = room number
	* - nr_rooms  = total number of rooms
	* - nr = the primary record key
	*
	* @access public
	* @param int Primary record key number
	* @return boolean
	*/
	function getRoomsData($ward_nr=0){
			global $db;
			if(!$ward_nr) return FALSE;

		$this->sql="SELECT * FROM $this->tb_room WHERE ward_nr='$ward_nr' AND status NOT IN ('closed','inactive','void','hidden','deleted')
						ORDER BY room_nr";
		#$this->sql="SELECT *  FROM $this->tb_room WHERE ward_nr='$ward_nr' AND  status NOT IN ($this->dead_stat)
		#					 ORDER BY room_nr";
				if($this->result=$db->Execute($this->sql)) {
					$this->count = $this->result->RecordCount();
						#if($this->result->RecordCount()) {
				 return $this->result;
		} else { return false; }

	}

	#Added by Cherry 07-07-10
	function getListWards(){
		global $db;
		$listWard[0]= "-Select Ward-";
		$sql = "SELECT nr, name FROM care_ward WHERE status NOT IN ('hidden', 'void', 'deleted', 'inactive')";
		if ($result=$db->Execute($sql)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					 $listWard[$row['nr']] = $row['name'];
				}
				return $listWard;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	/**
	* Saves new ward's room  information.
	*
	* Data passed by reference with associative array and have index keys as outlined in the <var>$fld_room</var> array.
	* @access public
	* @param array Data to save.
	* @return boolean
	* @created : burn September 26, 2007
	*/
	var $arrayItems = array();
	function saveWardRoomInfo(&$data){
		global $db;
		#$this->coretable=$this->tb_room;
		#$this->ref_array=$this->fld_room;
		#added by VAN 04-14-08
		$this->_useRoom();
		extract($data);
        #print_r($data);
        #type_nr, date_create, date_close, is_temp_closed, room_nr, ward_nr, dept_nr,
        #nr_of_beds, info, status, history, modify_id, modify_time, create_id, create_time
        #$index = " type_nr, date_create, room_nr, ward_nr, dept_nr,
		#nr_of_beds, info,room_rate, history, create_id, create_time ";
		$index = " type_nr, date_create, room_nr, ward_nr, dept_nr,
						nr_of_beds, info, history, create_id, create_time ";
		#edited by pol
        if ($accomodation_type==1){
            
			$arrayItems = array();
            foreach ($rooms as $key => $value){
                #$tempArray = array($value,$beds[$key],$info[$key]);
                #$tempArray = array($value,$beds[$key],$info[$key],$rate[$key]);
                $tempArray = array($type[$key],$value,$beds[$key],$info[$key]);
                array_push($arrayItems,$tempArray);
            }
            $this->arrayItems = $arrayItems;
#echo "class_ward.php : saveWardRoomInfo : arrayItems : <br> "; print_r($arrayItems); echo " <br> \n";
            #$values = "$type_nr, '$date_create', ?, $ward_nr, $dept_nr,
            #                ?, ?, ?, '$history', '$create_id', NOW()";
            $values = "?, '$date_create', ?, $ward_nr, $dept_nr,
                            ?, ?, '$history', '$create_id', NOW()";
        
           #end pol
		}elseif ($accomodation_type==2){
			# PAYWARD accomodation
			$arrayItems = array();
			foreach ($rooms as $key => $value){
				#$tempArray = array($value,$beds[$key],$info[$key]);
				#$tempArray = array($value,$beds[$key],$info[$key],$rate[$key]);
				$tempArray = array($type[$key],$value,$beds[$key],$info[$key]);
				array_push($arrayItems,$tempArray);
			}
			$this->arrayItems = $arrayItems;
#echo "class_ward.php : saveWardRoomInfo : arrayItems : <br> "; print_r($arrayItems); echo " <br> \n";
			#$values = "$type_nr, '$date_create', ?, $ward_nr, $dept_nr,
			#				?, ?, ?, '$history', '$create_id', NOW()";
			$values = "?, '$date_create', ?, $ward_nr, $dept_nr,
							?, ?, '$history', '$create_id', NOW()";
		}

		$this->sql = "INSERT INTO $this->coretable ($index)
							VALUES ($values)";

#echo "class_ward.php : saveWardRoomInfo : this->sql = '".$this->sql."' <br> \n";
#exit();

		if ($accomodation_type==1){
			$ok = $db->Execute($this->sql,$arrayItems);
		}elseif ($accomodation_type==2){
			$ok = $db->Execute($this->sql,$arrayItems);
		}

#		if ($db->Execute($this->sql,$arrayItems)) {
		if ($ok) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end of function saveWardRoomInfo

	/**
	* Saves new ward's room  information.
	*
	* Data passed by reference with associative array and have index keys as outlined in the <var>$fld_room</var> array.
	* @access public
	* @param array Data to save.
	* @return boolean
	*/
	function saveWardRoomInfoFromArray(&$data){
		$this->coretable=$this->tb_room;
		$this->ref_array=$this->fld_room;
		$this->data_array=$data;
		$this->data_array['type_nr']=1; // 1 = ward room type nr
		return $this->insertDataFromInternalArray();
	}

		/*
		* Gets the list of room numbers [room_nr] based on WARD NUMBER
		* @param int ward number
		* @return an ARRAY of room number ONLY or boolean
		* @created : burn, September 28, 2007
		*/

	function getListOfRoomNrByWard($ward_nr='',$cond=''){
		global $db;

		if(empty($ward_nr) || (!$ward_nr)){
			return FALSE;
		}
		if(empty($cond) || (!$cond)){
			$cond = "AND status NOT IN ('closed',$this->dead_stat)";
		}

		$this->sql="SELECT room_nr
						FROM care_room
						WHERE ward_nr='$ward_nr'
							$cond";

#echo "class_radiology.php : getListOfRoomNrByWard : this->sql = '".$this->sql."' <br> \n";
#exit();
		if ($buf=$db->Execute($this->sql)){
			if($this->count=$buf->RecordCount()) {
				$arr = array();
				while($tmp = $buf->FetchRow()){
					array_push($arr,$tmp[0]);
				}
				return $arr;
#			}else { return FALSE; }
#		}else { return FALSE; }
			}else { return array(); }
		}else { return array(); }
	}//end fucntion getListOfRoomNrByWard

		/**
		* Updates the status of a room(s) in table 'care_room'.
		* @access public
		* @param int, refno
		* @param array, array of rooms
		* @param string, new status
		* @return boolean
		* @created : burn, September 28, 2007
		*/
	function changeRoomStatusOnly($ward_nr=0, $arrayRooms, $new_status=''){
		global $db,$HTTP_SESSION_VARS;

		if ( empty($ward_nr) || (!$ward_nr) )
			return FALSE;
		if(!is_array($arrayRooms) || (!$arrayRooms) || empty($arrayRooms))
			return FALSE;

		$this->_useRoom();

		$history = $this->ConcatHistory("Update status [$new_status] ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");

		$this->sql="UPDATE $this->coretable ".
			" SET status='".$new_status."', history=".$history.", ".
			"		encoder='".$HTTP_SESSION_VARS['sess_user_name']."',".
			"		modify_id='".$HTTP_SESSION_VARS['sess_user_name']."', modify_dt=NOW() ".
			" WHERE ward_nr = '$ward_nr' AND room_nr = ?";

		if ($buf=$db->Execute($this->sql,$arrayItems)){
			if($db->Affected_Rows()) {
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }
	}# end of function changeRoomStatusOnly

	 /*
	* Updates ward's room info in table 'care_room'
	* @param Array Data to by reference
	* @return boolean
	* @edited : VAN, April 12, 2008
	*/

function updateWardRoomInfo($data, $type, $ward_nr){
		global $db, $HTTP_SESSION_VARS;
		#print_r($data);
		#Charity
		if ($type==1){
			#print_r($data);
			#echo "class_ward.php : data['ward_nr'] ='".$data['ward_nr']."'<br> \n";
			#echo "class_ward.php : data['rooms'] : "; print_r($data['rooms']); echo " <br><br> \n";
			$current_list = $this->getListOfRoomNrByWard($data['ward_nr']);
			#echo "class_ward.php : current_list : "; print_r($current_list); echo " <br><br> \n";
			$current_deleted_list = $this->getListOfRoomNrByWard($data['ward_nr'],"AND status IN ('closed',$this->dead_stat)");
			#echo "class_ward.php : current_deleted_list : "; print_r($current_deleted_list); echo " <br><br> \n";
			#echo "class_ward.php : empty(current_deleted_list) = '".empty($current_deleted_list)."' <br> \n";
			#echo "class_ward.php : is_array(current_deleted_list) = '".is_array($current_deleted_list)."' <br> \n";
			$update_only_list = array_intersect($data['rooms'],$current_list);
			#echo "class_ward.php : update_only_list : "; print_r($update_only_list); echo " <br><br> \n";
			$add_only_list = array_diff($data['rooms'],$current_list);
			#echo "class_ward.php : add_only_list 1 : "; print_r($add_only_list); echo " <br><br> \n";
			$update_status_only_list = array_intersect($current_deleted_list,$add_only_list);
			#echo "class_ward.php : update_status_only_list 1 : "; print_r($update_status_only_list); echo " <br><br> \n";
			$update_deleted2pending_status_only_list = array_intersect($data['rooms'],$current_deleted_list);
			#echo "class_ward.php : update_deleted2pending_status_only_list : "; print_r($update_deleted2pending_status_only_list); echo " <br><br> \n";
			$update_status_only_list = array_unique(array_merge($update_status_only_list,$update_deleted2pending_status_only_list));
			#echo "class_ward.php : update_status_only_list 2 : "; print_r($update_status_only_list); echo " <br><br> \n";
			$add_only_list = array_diff($add_only_list,$update_status_only_list);
			#echo "class_ward.php : add_only_list 2 : "; print_r($add_only_list); echo " <br><br> \n";
			$delete_only_list = array_diff($current_list,$data['rooms']);
			#echo "class_ward.php : delete_only_list : "; print_r($delete_only_list); echo " <br><br> \n";
			#exit();
			#Add room that not in the previous list but it is in the current room
			if (is_array($add_only_list) && !empty($add_only_list)){
				$temp_data = $data;
				$temp_room_nr = array();
				$temp_nr_of_beds = array();
				$temp_info = array();
				$temp_type = array();
				#$temp_rate = array();
				#echo "<br>room = ".$data['room_nr'];
				#print_r($add_only_list );
				#exit();
				foreach ($add_only_list as $key => $value){
					$orig_key = array_search($value, $data['rooms']);
					array_push($temp_room_nr,$value);
					array_push($temp_nr_of_beds,$data['beds'][$orig_key]);
					array_push($temp_info,$data['info'][$orig_key]);
					#array_push($temp_rate,$data['rate'][$orig_key]);
					array_push($temp_type,$data['type'][$orig_key]);
				}
				$temp_data['rooms'] = $temp_room_nr;
				$temp_data['beds'] = $temp_nr_of_beds;
				$temp_data['info'] = $temp_info;
				#$temp_data['rate'] = $temp_rate;
				$temp_data['type'] = $temp_type;
				$temp_data['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
				#print_r($temp_data);
				#return $this->saveWardRoomInfoNEW($temp_data);
				$ok = $this->saveWardRoomInfoNEW($temp_data);
			}
			#Delete the room logically that not in the current list
			if (is_array($delete_only_list) && !empty($delete_only_list)){
				$arrayItems = array();
				foreach ($delete_only_list as $key => $value){
					$tempArray = array($value);
					array_push($arrayItems,$tempArray);
				}
				$ok = $this->updateWardRoomByWard($data, $arrayItems,'deleted');
			}
			#Change status from 'deleted' to '' for rooms that are re-created
			if (is_array($update_status_only_list) && !empty($update_status_only_list)){
				$arrayItems = array();
				foreach ($update_status_only_list as $key => $value){
					$orig_key = array_search($value, $data['rooms']);
					#$tempArray = array($data['beds'][$orig_key], $data['info'][$orig_key], $value);
					#$tempArray = array($data['beds'][$orig_key], $data['info'][$orig_key],$data['rate'][$orig_key], $value);
					$tempArray = array($data['beds'][$orig_key], $data['info'][$orig_key],$data['type'][$orig_key], $value);
					array_push($arrayItems,$tempArray);
				}
				$ok = $this->updateWardRoomByWard($data, $arrayItems,'');
			}
			#Update the room info in the previous list
			if (is_array($update_only_list) && !empty($update_only_list)){
				$arrayItems = array();
				foreach ($update_only_list as $key => $value){
					$orig_key = array_search($value, $data['rooms']);
					#$tempArray = array($data['beds'][$orig_key], $data['info'][$orig_key], $value);
					#$tempArray = array($data['beds'][$orig_key], $data['info'][$orig_key],$data['rate'][$orig_key], $value);
					$tempArray = array($data['beds'][$orig_key], $data['info'][$orig_key],$data['type'][$orig_key], $value);
					array_push($arrayItems,$tempArray);
				}
				$ok = $this->updateWardRoomByWard($data, $arrayItems);
			}


		}elseif ($type==2){ 	#Payward
			#print_r($data);
			#echo "class_ward.php : data['ward_nr'] ='".$data['ward_nr']."'<br> \n";
			#echo "class_ward.php : data['rooms'] : "; print_r($data['rooms']); echo " <br><br> \n";
			$current_list = $this->getListOfRoomNrByWard($data['ward_nr']);
			#echo "class_ward.php : current_list : "; print_r($current_list); echo " <br><br> \n";
			$current_deleted_list = $this->getListOfRoomNrByWard($data['ward_nr'],"AND status IN ('closed',$this->dead_stat)");
			#echo "class_ward.php : current_deleted_list : "; print_r($current_deleted_list); echo " <br><br> \n";
			#echo "class_ward.php : empty(current_deleted_list) = '".empty($current_deleted_list)."' <br> \n";
			#echo "class_ward.php : is_array(current_deleted_list) = '".is_array($current_deleted_list)."' <br> \n";
			$update_only_list = array_intersect($data['rooms'],$current_list);
			#echo "class_ward.php : update_only_list : "; print_r($update_only_list); echo " <br><br> \n";
			$add_only_list = array_diff($data['rooms'],$current_list);
			#echo "class_ward.php : add_only_list 1 : "; print_r($add_only_list); echo " <br><br> \n";
			$update_status_only_list = array_intersect($current_deleted_list,$add_only_list);
			#echo "class_ward.php : update_status_only_list 1 : "; print_r($update_status_only_list); echo " <br><br> \n";
			$update_deleted2pending_status_only_list = array_intersect($data['rooms'],$current_deleted_list);
			#echo "class_ward.php : update_deleted2pending_status_only_list : "; print_r($update_deleted2pending_status_only_list); echo " <br><br> \n";
			$update_status_only_list = array_unique(array_merge($update_status_only_list,$update_deleted2pending_status_only_list));
			#echo "class_ward.php : update_status_only_list 2 : "; print_r($update_status_only_list); echo " <br><br> \n";
			$add_only_list = array_diff($add_only_list,$update_status_only_list);
			#echo "class_ward.php : add_only_list 2 : "; print_r($add_only_list); echo " <br><br> \n";
			$delete_only_list = array_diff($current_list,$data['rooms']);
			#echo "class_ward.php : delete_only_list : "; print_r($delete_only_list); echo " <br><br> \n";
			#exit();

			#Add room that not in the previous list but it is in the current room
			if (is_array($add_only_list) && !empty($add_only_list)){

				$temp_data = $data;
				$temp_room_nr = array();
				$temp_nr_of_beds = array();
				$temp_info = array();
				$temp_type = array();
				#$temp_rate = array();
				#echo "<br>room = ".$data['room_nr'];
				#print_r($add_only_list );
				#exit();

				foreach ($add_only_list as $key => $value){
					$orig_key = array_search($value, $data['rooms']);
					array_push($temp_room_nr,$value);
					array_push($temp_nr_of_beds,$data['beds'][$orig_key]);
					array_push($temp_info,$data['info'][$orig_key]);
					#array_push($temp_rate,$data['rate'][$orig_key]);
					array_push($temp_type,$data['type'][$orig_key]);
				}

				$temp_data['rooms'] = $temp_room_nr;
				$temp_data['beds'] = $temp_nr_of_beds;
				$temp_data['info'] = $temp_info;
				#$temp_data['rate'] = $temp_rate;
				$temp_data['type'] = $temp_type;

				$temp_data['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";

				#print_r($temp_data);

				#return $this->saveWardRoomInfoNEW($temp_data);
				$ok = $this->saveWardRoomInfoNEW($temp_data);
			}

			#Delete the room logically that not in the current list
			if (is_array($delete_only_list) && !empty($delete_only_list)){
				$arrayItems = array();
				foreach ($delete_only_list as $key => $value){
					$tempArray = array($value);
					array_push($arrayItems,$tempArray);
				}

				$ok = $this->updateWardRoomByWard($data, $arrayItems,'deleted');
			}

			#Change status from 'deleted' to '' for rooms that are re-created
			if (is_array($update_status_only_list) && !empty($update_status_only_list)){
				$arrayItems = array();
				foreach ($update_status_only_list as $key => $value){
					$orig_key = array_search($value, $data['rooms']);
					#$tempArray = array($data['beds'][$orig_key], $data['info'][$orig_key], $value);
					#$tempArray = array($data['beds'][$orig_key], $data['info'][$orig_key],$data['rate'][$orig_key], $value);
					$tempArray = array($data['beds'][$orig_key], $data['info'][$orig_key],$data['type'][$orig_key], $value);
					array_push($arrayItems,$tempArray);
				}

				$ok = $this->updateWardRoomByWard($data, $arrayItems,'');
			}

			#Update the room info in the previous list
			if (is_array($update_only_list) && !empty($update_only_list)){
				$arrayItems = array();
				foreach ($update_only_list as $key => $value){
					$orig_key = array_search($value, $data['rooms']);

					#$tempArray = array($data['beds'][$orig_key], $data['info'][$orig_key], $value);
					#$tempArray = array($data['beds'][$orig_key], $data['info'][$orig_key],$data['rate'][$orig_key], $value);
					$tempArray = array($data['beds'][$orig_key], $data['info'][$orig_key],$data['type'][$orig_key], $value);

					array_push($arrayItems,$tempArray);
				}

				$ok = $this->updateWardRoomByWard($data, $arrayItems);
			}
		}	#end of elseif ($type==2){

		return $ok;

# see function updateRadioRefNoInfoFromArray in class_ward.php
	} #end of updateWardRoomInfo


	function roomhasPatient($nr, $room_nr){
		global $db;
		$this->sql="SELECT nr FROM $this->tb_location
						WHERE type_nr=4
							AND group_nr='$nr'
							AND location_nr = '$room_nr'
							AND date_from NOT  IN  ('0000-00-00','')
							AND date_to  IN  ('0000-00-00','')
							AND status NOT IN ('closed','inactive','void','hidden','deleted')";
		if($this->result=$db->Execute($this->sql)){
			if($this->count = $this->result->RecordCount()){
				return true;
			}else{return false;}
		}else{return false;}
	}

	function getLastBedNr_Occupied($ward_nr, $room_nr=''){
		global $db;

		if ($room_nr)
			$this->sql="SELECT b.location_nr
						FROM care_encounter_location AS r
						LEFT JOIN care_encounter_location AS b ON  (
												r.encounter_nr=b.encounter_nr
												AND b.type_nr=5 AND b.group_nr='$ward_nr'
												AND b.date_from NOT IN ('0000-00-00','')
												AND b.date_to IN ('0000-00-00','')
												AND b.status NOT IN ('closed','inactive','void','hidden','deleted'))
						WHERE r.type_nr=4 AND r.group_nr='$ward_nr' AND r.location_nr = '$room_nr'
						AND r.date_from NOT IN ('0000-00-00','')
						AND r.date_to IN ('0000-00-00','')
						AND r.status NOT IN ('closed','inactive','void','hidden','deleted')
					ORDER BY location_nr DESC LIMIT 1";
		else
			$this->sql="SELECT location_nr FROM care_encounter_location
					WHERE type_nr=5 AND group_nr='$ward_nr'
					AND date_from NOT IN ('0000-00-00','')
					AND date_to IN ('0000-00-00','')
					AND status NOT IN ('closed','inactive','void','hidden','deleted','discharged')
					ORDER BY location_nr DESC LIMIT 1";

		if($this->result=$db->Execute($this->sql)){
			if($this->count = $this->result->RecordCount()){
				$row = $this->result->FetchRow();
				return $row['location_nr'];
			}else{return false;}
		}else{return false;}
	}
#----------------------------------

		#added by VAN 04-14-08
	function saveWardRoomInfoNEW(&$data){
		global $db,$HTTP_SESSION_VARS;

		$this->_useRoom();
		extract($data);
		#print_r($data);
		$arrayItems = array();
		foreach ($rooms as $key => $value){
			#$tempArray = array($value,$beds[$key],$info[$key]);
			$tempArray = array($type[$key],$value,$beds[$key],$info[$key]);
			array_push($arrayItems,$tempArray);
		}
		#print_r($arrayItems);
		#$this->arrayItems = $arrayItems;

		#$index = " type_nr, date_create, room_nr, ward_nr, dept_nr,
		#				nr_of_beds, info, room_rate, history, create_id, create_time ";
		$index = " type_nr, date_create, room_nr, ward_nr, dept_nr,
						nr_of_beds, info, history, create_id, create_time ";

		#$values = "$type_nr, '$date_create', ?, $ward_nr, $dept_nr,
		#					?, ?,?, '$history', '$create_id', NOW()";

		$values = "?, '$date_create', ?, $ward_nr, $dept_nr,
							?, ?, '$history', '$create_id', NOW()";

		$this->sql = "INSERT INTO $this->coretable ($index)
							VALUES ($values)";

		if ($db->Execute($this->sql,$arrayItems)) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}# end function saveWardRoomInfoNEW

	#------------------------------------


	#added by VAN 04-15-08
	function updateWardRoomByWard($data,$arrayItems, $new_status=''){
		global $db,$HTTP_SESSION_VARS;

		if(!is_array($data) || (!$data))
			return FALSE;
		if(!is_array($arrayItems) || (!$arrayItems))
			return FALSE;

		$this->_useRoom();
		$ward_nr = $data['ward_nr'];
		$this->data_array=$data;
		unset($this->data_array['rooms']);
		unset($this->data_array['beds']);
		unset($this->data_array['info']);
		#unset($this->data_array['rate']);
		unset($this->data_array['type']);

		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];

		if (!empty($new_status)){
			# if the status needs to be change
			$history = $this->ConcatHistory("Update status [$new_status] ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
			$this->data_array['history'] = $history;
			$this->data_array['status'] = $new_status;
		}else{
			$this->data_array['history'] = $this->ConcatHistory("Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
		}

		#if (empty($new_status) || ($new_status=='')){
		if (empty($new_status)){
				# there is NO NEED for change of status
			if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
				else $concatfx='concat';

			#	Only the keys of data to be updated must be present in the passed array.
			$x='';
			$v='';
			$this->buffer_array = array();
			while(list($x,$v)=each($this->ref_array)) {
				if (isset($this->data_array[$v]))
					$this->buffer_array[$v]=trim($this->data_array[$v]);
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
		}# end of if-stmt 'if (empty($new_status) || ($new_status=='pending'))'

		#print_r($elems);

		#if (empty($new_status) || ($new_status=='pending')){
		if (empty($new_status)){

			$this->sql="UPDATE $this->coretable SET
								$elems,
								nr_of_beds = ?,
								info = ?,
								type_nr = ?,
								status = '".$new_status."',
								modify_time = NOW()
							WHERE ward_nr = '$ward_nr' AND room_nr = ?
						 ";
			/*
			$this->sql="UPDATE $this->coretable SET
								$elems,
								nr_of_beds = ?,
								info = ?,
								status = '".$new_status."',
								modify_time = NOW()
							WHERE ward_nr = '$ward_nr' AND room_nr = ?
						 ";
			*/
		}else{

			$this->sql="UPDATE $this->coretable SET
								status='".$new_status."', history=".$history.",
								 modify_id='".$HTTP_SESSION_VARS['sess_user_name']."',
								 modify_time=NOW()
							WHERE ward_nr = '$ward_nr' AND room_nr = ?";
		}
		#echo "sql = ".$this->sql;

		if ($buf=$db->Execute($this->sql,$arrayItems)){
			if($db->Affected_Rows()) {
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }
	}# end of function updateWardRoomByWard

	#------------------------------------------------

	/**
	* Checks if a room number of a given ward number exists.
	*
	* @access public
	* @param int Room number
	* @param int Ward number
	* @return boolean
	*/
	function RoomExists($room_nr=0,$ward_nr=0){
			global $db, $dbf_nodate;
		if(!$room_nr) return false;
		if($ward_nr) $this->ward_nr=$ward_nr;
			elseif(!$this->ward_nr) return false;
		$this->sql="SELECT room_nr FROM $this->tb_room
							WHERE ward_nr=$this->ward_nr
								AND room_nr=$room_nr
								AND date_close = '$dbf_nodate'
								AND status NOT IN ($this->dead_stat)";
				if($this->result=$db->Execute($this->sql)) {
						if($this->result->RecordCount()) {
				 return true;
			} else { return false; }
		} else { return false; }
	}
	/**
	* Gets one/all active (not closed) room(s) information.
	*
	* The resulting adodb object is stored in the internal buffer <var>$result</var>.
	*
	* param room_nr = the room number. If supplied, the open room's info will be returned, else all open rooms info will be returned.
	*
	* param ward_nr = the ward number (optional). Used if supplied, else the ward number set by the constructor will used.
	*
	* return true = if room(s) found.  The result is stored in the internal result variable and returned by a public function.
	*
	* return false = if ward_nr is 0 AND internal ward_nr is 0
	*
	* return false = if no open room(s) found
	* @access private
	* @param int Room number
	* @param int Ward number
	* @return boolean
	*/
	function _getActiveRoomInfo($room_nr=0,$ward_nr=0){
			global $db,$dbf_nodate;
			//$db->debug=1;
		if($ward_nr) $this->ward_nr=$ward_nr;
			elseif(!$this->ward_nr) return false;
		#$this->sql="SELECT * FROM $this->tb_room
		#					WHERE ward_nr='$this->ward_nr'";

		$this->sql="SELECT t.nr, t.type, t.name AS roomtype,t.room_rate,r.*
					FROM $this->tb_room AS r
					INNER JOIN care_type_room AS t ON t.nr=r.type_nr
							WHERE ward_nr='$this->ward_nr'";

		if($room_nr) $this->sql.=" AND r.room_nr='$room_nr'";

		$this->sql.="  AND r.date_close = '$dbf_nodate' AND r.status NOT IN ($this->dead_stat) ORDER BY r.room_nr";
#echo "class_ward.php : _getActiveRoomInfo : this->sql ='".$this->sql."' <br> \n";
				if($this->result=$db->Execute($this->sql)) {
					#$this->count = $this->result->RecordCount();
						#if($this->result->RecordCount()) {
				if($this->count = $this->result->RecordCount()) {
				 return true;
			} else { return false; }
		} else { return false; }
	}
	/**
	* Gets all active rooms information.
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the  data with index keys as outlined in the <var>$fld_room</var> variable
	*
	* @access public
	* @param int Ward number
	* @return mixed adodb record object or boolean
	*/
	function getAllActiveRoomsInfo($ward_nr=0){
				if($this->_getActiveRoomInfo(0,$ward_nr)) {
#echo "class_ward.php : getAllActiveRoomsInfo : this->sql ='".$this->sql."' <br> \n";
			return $this->result;
		} else { return false; }
	}
	/**
	* Gets one active room information.
	*
	* The returned adodb record object contains a row of array.
	* This array contains the  data with index keys as outlined in the <var>$fld_room</var> variable
	*
	* @access public
	* @param int Room number
	* @param int Ward number
	* @return mixed adodb record object or boolean
	*/
	function getActiveRoomInfo($room_nr=0,$ward_nr=0){
		if(!$room_nr) return false;
				if($this->_getActiveRoomInfo($room_nr,$ward_nr)) {
			return $this->result;
		} else { return false; }
	}
	/**
	* Counts and returns the number of  beds available to the ward.
	* @access public
	* @param int Ward number
	* @return mixed integer or boolean
	*/
	function countBeds($ward_nr){
			global $db;
		$this->sql="SELECT SUM(nr_of_beds) AS nr FROM $this->tb_room WHERE ward_nr=$ward_nr AND
		is_temp_closed IN ('',0) AND status NOT IN ($this->dead_stat)";

#echo"class_ward.php : countBeds :: this->sql ='".$this->sql."' <br> \n";
				if($buf=$db->Execute($this->sql)) {
						if($buf->RecordCount()) {
				$row=$buf->FetchRow();
				 return $row['nr'];
			} else { return false; }
		} else { return false; }
	}
	/**
	* Creates and returns a list of patients waiting to be assigned a room or bed.
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the  data with the following index keys:
	* - encounter_nr = encounter number
	* - encounter_class_nr  = encounter class number
	* - current_ward_nr = current ward number
	* - pid = PID number
	* - name_last  = Patient's last name
	* - name_first  = Patient's first name
	* - date_birth = date of birth
	* - sex = sex
	* - ward_id = ward id
	*
	* @access public
	* @param int Ward number
	* @return mixed adodb record object or boolean
	*/
	function createWaitingInpatientList($ward_nr=0,$option){
		global $db;

		#echo $option;
		#echo "<p>nka<p>";
		#added by angelo m. 10.04.2010
		#start--------------------------
		if($option=="")
			$option='still-in';
		$sql_notIn_status = "";
		$option='still-in';

		switch($option){
			case 'all':
						$sql_is_discharged = ' e.is_discharged IN (0,1) ';
						$sql_in_ward = " (1,0) ";
						break;
			case 'still-in':
						$sql_is_discharged = ' e.is_discharged=0 ';
						$sql_in_ward = "('',1)";
						break;
			case 'discharged':
						$sql_is_discharged = ' e.is_discharged=1 ';
						$sql_in_ward = " (0) ";
						break;
		}
		#end--------------------------

		if($ward_nr) $cond="AND current_ward_nr='$ward_nr'";
			else $cond='';
		//if(empty($key)) return false;
		/*
		$this->sql="SELECT e.encounter_nr, e.encounter_class_nr, e.current_ward_nr, p.pid, p.name_last, p.name_first, p.date_birth, p.sex,w.ward_id
				FROM $this->tb_enc AS e
					LEFT JOIN $this->tb_person AS p ON e.pid=p.pid
					LEFT JOIN $this->tb_ward AS w ON e.current_ward_nr=w.nr
				WHERE e.encounter_class_nr='1' AND  e.is_discharged IN ('',0) $cond AND  in_ward IN ('',0)
					AND w.nr NOT IN ('',0)";
		*/
		#edited by VAN 02-09-08
		$this->sql="SELECT e.encounter_nr, e.encounter_class_nr, e.current_ward_nr, p.pid, p.name_last, p.name_first, p.date_birth, p.sex,w.ward_id
				    FROM $this->tb_enc AS e
					LEFT JOIN $this->tb_person AS p ON e.pid=p.pid
					LEFT JOIN $this->tb_ward AS w ON e.current_ward_nr=w.nr
				    WHERE e.is_discharged IN ('',0) $cond AND  in_ward IN ('',0)
					AND w.nr NOT IN ('',0)
                    AND e.status NOT IN ($this->dead_stat)";
					# NOTE: burn, November 15, 2007 : ward number 0 (i.e. w.nr=0), is for Operating Rooms
		#echo $sql;
			if ($this->res['_cwil']=$db->Execute($this->sql)){
				if ($this->rec_count=$this->res['_cwil']->RecordCount()){
				return $this->res['_cwil'];
			}else{return false;}
		}else{return false;}
	}
	/**
	* Returns current location information of an encounter.
	*
	* The returned adodb record object contains rows of arrays.
	* Each array contains the  data with the following index keys:
	* - ward_id = ward id
	* - ward_name = ward name
	* - roomprefix = room prefix
	* - dept_id  = department id
	* - dept_name = department default name
	* - room_nr = room number
	* - bed_nr = bed number
	*
	* @access public
	* @param int Encounter number
	* @return mixed adodb record object or boolean
	*/
	function EncounterLocationsInfo($enc_nr){
		global $db;

		$this->sql="SELECT w.ward_id,w.name AS ward_name, w.roomprefix,
							d.id AS dept_id,d.name_formal AS dept_name,
							r.location_nr AS room_nr, b.location_nr AS bed_nr
				FROM $this->tb_enc AS e
					LEFT JOIN $this->tb_ward AS w ON e.encounter_class_nr=1 AND e.current_ward_nr=w.nr
					LEFT JOIN $this->tb_dept AS d ON (e.encounter_class_nr=1 AND e.current_ward_nr=w.nr AND w.dept_nr = d.nr)
																	OR	(e.encounter_class_nr='2' AND e.current_dept_nr=d.nr)
					LEFT JOIN $this->tb_location AS r ON r.encounter_nr='$enc_nr' AND r.group_nr=w.nr AND r.type_nr=4 AND r.status<>'discharged'
					LEFT JOIN $this->tb_location AS b ON b.encounter_nr='$enc_nr' AND  b.group_nr=w.nr AND b.type_nr=5 AND b.status<>'discharged'
					WHERE e.encounter_nr='$enc_nr' AND e.status NOT IN ($this->dead_stat)";
		//echo $sql;
			if ($this->res['eli']=$db->Execute($this->sql)){
				if ($this->rec_count=$this->res['eli']->RecordCount()){
				return $this->res['eli']->FetchRow();
			}else{return false;}
		}else{return false;}
	}

	/**
	* Returns all accomodation type data.
	*
	* The returned adodb record object contains rows of arrays.
	*
	* @access public
	* @return mixed adodb record object or boolean
	* burn added: September 24, 2007
	*/
	function getAllAccomodationTypeDataObject() {
		global $db;

		$this->_useAccomodationType();
		$this->sql="SELECT * FROM $this->coretable WHERE status NOT IN ('closed',$this->dead_stat)";
				//echo $this->sql;
		if($this->result=$db->Execute($this->sql)) {
			if($this->result->RecordCount()) {
				return $this->result;
			} else { return false; }
		} else { return false; }
	}
	/**
	* Returns ward information.
	*
	* The returned  array contains the  data with the following index keys:
	* - all ward index keys as outlined in the <var>$fld_ward</var> variable
	* - dept_name = Department default name
	*
	* @access public
	* @param int accomodation type number
	* @return mixed array or boolean
	* burn added: September 24, 2007
	*/
	function getAccomodationTypeInfo($a_nr){
		global $db;

		$this->_useAccomodationType();
		$this->sql="SELECT * FROM $this->coretable WHERE accomodation_nr=$a_nr AND status NOT IN ('closed',$this->dead_stat)";
		if($this->res['gati']=$db->Execute($this->sql)) {
			if($this->rec_count=$this->res['gati']->RecordCount()) {
				return $this->res['gati']->FetchRow();
			} else { return false; }
		} else { return false; }
	}

	#added by VAN 04-08-08
	function countSearchNursingWard($searchkey='',$maxcount=100,$offset=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$this->sql = "SELECT w.*,
								(SELECT d.name_formal
									FROM care_department AS d
									WHERE d.nr=dept_nr)  AS dept_name
							FROM $this->tb_ward AS w
							WHERE status NOT IN ($this->dead_stat)
							AND ((ward_id LIKE '%$keyword%') OR (name LIKE '%$keyword%'))
								AND nr<>0";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function SearchNursingWard($searchkey='',$maxcount=100,$offset=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$this->sql = "SELECT SQL_CALC_FOUND_ROWS w.*,
								(SELECT d.name_formal
									FROM care_department AS d
									WHERE d.nr=dept_nr)  AS dept_name
							FROM $this->tb_ward AS w
							WHERE status NOT IN ($this->dead_stat)
							AND ((ward_id LIKE '%$keyword%') OR (name LIKE '%$keyword%'))
								AND nr<>0";

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}
	#---------------------------------

	#added by VAN 04-09-08
	function countSearchWard($searchkey='',$maxcount=100,$offset=0) {
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$this->sql = "SELECT w.*,d.name_formal AS dept_name
							FROM $this->tb_ward AS w
							LEFT JOIN $this->tb_dept AS d ON w.dept_nr=d.nr
							WHERE w.status NOT IN ('closed','inactive','void','hidden','deleted')
							AND ((ward_id LIKE '%$keyword%') OR (name LIKE '%$keyword%'))";

		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function SearchWard($searchkey='',$maxcount=100,$offset=0){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		#$suchwort=$searchkey;
		$searchkey = str_replace("^","'",$searchkey);
		$keyword=addslashes($searchkey);

		$this->sql = "SELECT SQL_CALC_FOUND_ROWS w.*,d.name_formal AS dept_name
							FROM $this->tb_ward AS w
							LEFT JOIN $this->tb_dept AS d ON w.dept_nr=d.nr
							WHERE w.status NOT IN ('closed','inactive','void','hidden','deleted')
							AND ((ward_id LIKE '%$keyword%') OR (name LIKE '%$keyword%'))";

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

	#---------------------------------

	#added by VAN 04-10-08
	function getLastRoomNr($ward_nr){
		global $db;
		$this->sql = "SELECT room_nr_start, room_nr_end FROM $this->tb_ward
						WHERE nr='$ward_nr'
									ORDER BY room_nr_end DESC LIMIT 1";

		#echo "sql = ".$this->sql;


		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result->FetchRow();
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function getOR_RoomInfo($room_nr){
		global $db;

		$this->sql="SELECT o.*, w.ward_id, w.name AS wardname, d.name_formal AS deptname,
						d.name_short AS deptshort, d.LD_var AS \"LD_var\"
						FROM care_room AS o
						LEFT JOIN care_ward AS w ON o.ward_nr=w.nr
						LEFT JOIN care_department AS d ON o.dept_nr=d.nr
						WHERE o.type_nr=2
						AND o.room_nr='".$room_nr."'";
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

	#-----------------------------

	#added by VAN 06-10-08
	function useRoomType(){
		$this->coretable=$this->tb_roomtype;
		$this->ref_array=$this->fld_roomtype;

	}

	function saveRoomType(&$data){
		if(!is_array($data)) return FALSE;
		$this->useRoomType();
		$this->buffer_array=NULL;
		return $this->insertDataFromInternalArray();
	}

	function getRoomTypeInfo($roomtype_nr){
		global $db;

		$this->sql = "SELECT * FROM $this->tb_roomtype
							WHERE nr='$roomtype_nr'";

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
			} else{
				 return FALSE;
			}
	}

	#added by VAN 06-18-08
	#edited by VAN 04-30-2010
	function getRoomRate($current_room_nr, $ward_nr){
		global $db;

		$this->sql = "SELECT t.room_rate,t.name,t.description, r.*
						FROM care_room AS r
						INNER JOIN care_type_room AS t ON t.nr=r.type_nr
						WHERE room_nr='$current_room_nr'
						AND ward_nr= '$ward_nr' LIMIT 1";

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
				} else{
					 return FALSE;
			}
	}
	#-----------------

	function deleteRoomTypeItem($roomtype_nr) {
		global $db,$HTTP_SESSION_VARS;

		if(empty($roomtype_nr) || (!$roomtype_nr))
			return FALSE;

		$this->sql="DELETE FROM $this->tb_roomtype WHERE nr='$roomtype_nr'";
		return $this->Transact();
	}

	function getAllRoomType(){
	global $db;

		$this->sql ="SELECT * FROM $this->tb_roomtype ORDER BY name";
		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result;
			} else{
				 return FALSE;
			}
	}

	function updateRoomTypeFromInternalArray($nr, $type, $name, $desc, $rate){
	#function updateRoomTypeFromInternalArray($nr, $type, $name, $desc){
		global $db,$HTTP_SESSION_VARS;

		$this->sql = "UPDATE $this->tb_roomtype
							SET type = '$type',
									name = '$name',
								description = '$desc',
								room_rate = '$rate',
									modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
									modify_time = NOW()
						 WHERE nr = '$nr'";
		/*
		$this->sql = "UPDATE $this->tb_roomtype
							SET type = '$type',
									name = '$name',
								description = '$desc',
								modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
									modify_time = NOW()
						 WHERE nr = '$nr'";
		*/
		return $this->Transact();
	}
	#--------------------------

	#added by VAN 06-24-08
	/*
	function RoomNrExists($wardNr=0, $room_nr=0){
			global $db, $dbf_nodate;
		if(!$room_nr) return false;

		$this->sql="SELECT room_nr FROM $this->tb_room
							WHERE ward_nr='$wardNr' AND room_nr='$room_nr'";
				if($this->result=$db->Execute($this->sql)) {
						if($this->result->RecordCount()) {
				 return true;
			} else { return false; }
		} else { return false; }
	}
	*/

function RoomNrExists($wardNr=0, $room_nr=0, $bol=1){
			global $db, $dbf_nodate;
		if(!$room_nr) return false;

		$this->sql="SELECT room_nr FROM $this->tb_room
							WHERE ward_nr='$wardNr' AND room_nr='$room_nr'";

	if ($bol){
		if($this->result=$db->Execute($this->sql)) {
						if($this->result->RecordCount()) {
				 return true;
			} else { return false; }
		} else { return false; }
	}else{
		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
				} else{
					 return FALSE;
			}
	}
}
	#--------------------

	#added by VAN 06-26-08
	function getBedNr($encounter_nr){
		global $db;

		$this->sql = "SELECT * FROM care_encounter_location
						WHERE encounter_nr='$encounter_nr' AND type_nr=5
						AND status NOT IN('discharged',$this->dead_stat)
						ORDER BY create_id DESC LIMIT 1";

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
				} else{
					 return FALSE;
			}
	}

	//added by Omick October 09 2009
	//used for generating select boxes
	function get_all_active_wards_as_pair($key, $value) {
		global $db;
		$this->sql = "SELECT $key, $value FROM $this->tb_ward WHERE status NOT IN ('closed','inactive','void','hidden','deleted')";
		if ($this->result = $db->Execute($this->sql)) {
			if ($this->result->RecordCount() > 0) {
				$array = array();
				while ($row = $this->result->FetchRow()) {
					$array[$row[$key]] = $row[$value];
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

	//added by omick october 10 2009
	function get_room_rate($room_nr){
		global $db;

		$this->sql = "SELECT ctr.room_rate, ctr.name as type FROM care_room cr INNER JOIN care_type_room ctr ON (cr.type_nr = ctr.nr) WHERE cr.nr=$room_nr";
		if ($this->result = $db->Execute($this->sql)) {
			if ($this->result->RecordCount() > 0) {
				return $this->result->FetchRow();
			}
			else {
				return false;
			}
		}
		else{
			return false;
		}
	}
	#----------------

	function getLastBedNr($encounter_nr){
		global $db;

		$this->sql = "SELECT SQL_CALC_FOUND_ROWS * FROM care_encounter_location
						WHERE encounter_nr='$encounter_nr' AND type_nr=5
						AND status IN('discharged')
						ORDER BY create_id DESC LIMIT 1";

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
				} else{
					 return FALSE;
			}
	}

	function getWardByNr($ward_nr){
		global $db;

		$this->sql = "SELECT * FROM care_ward WHERE nr='$ward_nr'";

		if ($this->result=$db->Execute($this->sql)) {
				 $this->count=$this->result->RecordCount();
				 return $this->result->FetchRow();
				} else{
					 return FALSE;
			}
	}
}# end of class Ward