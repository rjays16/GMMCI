<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/nursing/ajax/nursing-station-new-common.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
#added by VAN 04-09-08
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/care_api_classes/class_notes_nursing.php');

#added by VAN 10-30-09
require_once($root_path.'include/care_api_classes/class_encounter.php');

//populate ward list
#added by VAN 04-09-08
function PopulateRow($sElem,$searchkey,$page){
	$objResponse = new xajaxResponse();
	$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('pagin_patient_search_max_block_rows');
	$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

	$ward_obj=new Ward;
	#$objResponse->addAlert('total = '.$total);
	$offset = $page * $maxRows;
	$searchkey = utf8_decode($searchkey);
	$total_srv = $ward_obj->countSearchWard($searchkey,$maxRows,$offset);
	#$objResponse->addAlert($ward_obj->sql);
	$total = $ward_obj->count;
	#$objResponse->addAlert('total = '.$total);

	$lastPage = floor($total/$maxRows);

	if ((floor($total%10))==0)
		$lastPage = $lastPage-1;

	if ($page > $lastPage) $page=$lastPage;
	$ergebnis=$ward_obj->SearchWard($searchkey,$maxRows,$offset);
	#$objResponse->addAlert("sql = ".$ward_obj->sql);
	$rows=0;

	$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
	$objResponse->call("clearList","wardList");

	if ($ergebnis) {
		$rows=$ergebnis->RecordCount();

		while($result=$ergebnis->FetchRow()) {
			$ward_id = ucfirst($result['ward_id']);
			$desp = ucfirst($result['description']);
			#commented by VAN 04-1-08
			/*
			if($rows==1){
				$rooms=&$ward_obj->getAllActiveRoomsInfo($result['nr']);
			}else{
				$rooms=$ward_obj->countCreatedRooms();
			}
			*/
			$rooms=$ward_obj->countCreatedRooms();

			if(is_object($rooms)){
				while($room=$rooms->FetchRow()){
					$wbuf[$room['nr']]=$room['nr_rooms'];
				}
				$rm_nr = $wbuf[$result['nr']];
			}

			$objResponse->call("js_AddRow",$result['nr'], $result['name'], $ward_id, $desp, $rm_nr, $result['is_temp_closed'], $result['accomodation_type']);
		}
	}else{
		$msg = "<tr><td colspan=\"4\">No ward list exists</td></tr>";
		$objResponse->assign("twardList", "innerHTML", $msg);
	}

	#if (!$rows) $objResponse->call("js_AddRow","wardList",NULL);
	if ($sElem) {
		$objResponse->call("endAJAXSearch",$sElem);
	}

	return $objResponse;
}

#added by VAN 04-12-08
	function saveWardRoom($data, $mode, $excess){
		global $db, $dbtype, $HTTP_SESSION_VARS;

		$objResponse = new xajaxResponse();
		$ward_obj = new Ward();
       
		$data['date_create']=date('Y-m-d');
		$data['history']="Create: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";
		$data['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
		$data['create_time']=date('Y-m-d H:i:s');
        //added by nursing mandatory excess
        $data['mandatory_excess']=$excess;
        
		#$objResponse->addAlert(print_r($data));
		if ($mode=='create'){
			$temp = isWardIDExists($data['ward_id']);
			if (!isWardIDExists($data['ward_id'])){
				$wardInfo = setWardInfo($data);

				if ($ward_obj->saveWard($wardInfo)){
					if($dbtype=='mysql'){
						$ward_nr=$ward_obj->insert_id;
					}else{
						$ward_nr=$ward_obj->postgre_Insert_ID($dbtable,'nr',$db->Insert_ID());
					}
					#$objResponse->addAlert("sql = ".$ward_obj->sql);
					#$objResponse->addScript("document.write('"+html_entities(addslashes($ward_obj->sql))+"')");
					#$objResponse->addAlert("sql = ".$dbtype." - ".$ward_nr);
                    
					$HTTP_SESSION_VARS['ward_nr'] = $ward_nr;
					$roomInfo = setRoomInfo($data,$ward_nr);
                    
					#$objResponse->addAlert("sql = ".$dbtype." - ".$ward_nr);
					#$objResponse->addAlert("sql = ".print_r($roomInfo,true));
					if($ward_obj->saveWardRoomInfo($roomInfo)){
						$objResponse->addAlert("Successfully created the new Ward and its Room(s)");
						$objResponse->addScriptCall("changeMode","update",$ward_nr);
					}else{
						$objResponse->addAlert("Unable to create the Room(s)");
					}
                    
					#$objResponse->addAlert(print_r($data));
					#$objResponse->addAlert("sql = ".$ward_obj->sql);
					#$objResponse->addAlert("erro = ".$db->ErrorMsg());
                    
				}else{
					$objResponse->addAlert("Unable to create the Ward & Room(s)");
				}
			}else{
				$objResponse->addScriptCall("wardIdExists");
			}
		}elseif ($mode=='update'){
				$wardInfo = setWardInfo($data);
				#$objResponse->addAlert('type = '.$data['accomodation_type']);

				if ($ward_obj->updateWard($data['ward_nr'],$wardInfo)){
					#if there are occupants in the ward, room should be higher or equal the previous # of rooms in that ward
					$roomsquery = $ward_obj->getRoomsData($data['ward_nr']);
					$room = $roomsquery->FetchRow();
					/*
					$ok=1;
					if ($ward_obj->hasPatient($data['ward_nr'])){
						if ($data['nr_of_beds']<$room['nr_of_beds']){
							$objResponse->addAlert("Number of beds must be higher or equal to ".$room['nr_of_beds']." beds because this ward has already an occupants.");
							$data['nr_of_beds'] = $room['nr_of_beds'];
											$ok=0;
						}
					}
					*/

					#$objResponse->addAlert($ok);
					$roomInfo = setRoomInfo($data,$data['ward_nr']);
					//$objResponse->addAlert(print_r($data,TRUE));
					#print_r($roomInfo);
					#if(($ward_obj->updateWardRoomInfo($roomInfo, $data['accomodation_type'], $data['ward_nr']))&&($ok)){
					if($ward_obj->updateWardRoomInfo($roomInfo, $data['accomodation_type'], $data['ward_nr'])){
                        $objResponse->addAlert("Successfully updated the Ward and its Room(s)");
						$objResponse->addScriptCall("changeMode","update",$data['ward_nr']);
					}else{
						$objResponse->addAlert("Unable to update the Room(s)");
					}
					#$objResponse->addAlert("room sql = ".$ward_obj->sql);
				}else{
					#$objResponse->alert($ward_obj->sql);
					$objResponse->addAlert("Unable to update the Ward & Room(s)");
				}

		}

		return $objResponse;
	}

	#-------------------------

	function isWardIDExistsTest($ward_id='',$data){
		$objResponse = new xajaxResponse();
		$ward_obj=new Ward();

#$objResponse->addAlert("nursing-station-new-server.php : isWardIDExistsTest : ward_id = '".$ward_id."'");
#$objResponse->addAlert("nursing-station-new-server.php : isWardIDExistsTest : data ='".$data."' print_r(data)\n".print_r($data,true));

		if ($rs = $ward_obj->getAllWardsDataObject()){
			while($result=$rs->FetchRow()){
				if ($ward_id==$result['ward_id']){
					#$objResponse->addAlert("nursing-station-new-server.php : isWardIDExistsTest : ward id is already existing! ");
					break;
				}
			}
			#$objResponse->addAlert("nursing-station-new-server.php : isWardIDExistsTest : ward id is ACCEPTED! ");
		}else{
			# no entry yet in the table
			#$objResponse->addAlert("nursing-station-new-server.php : isWardIDExistsTest : no entry yet in the table! ");
		}
		$wardInfo = setWardInfo($data);
#$objResponse->addAlert("nursing-station-new-server.php : isWardIDExistsTest : wardInfo ='".$wardInfo."' print_r(wardInfo)\n".print_r($wardInfo,true));
		$roomInfo = setRoomInfo($data,22);
#$objResponse->addAlert("nursing-station-new-server.php : isWardIDExistsTest : roomInfo ='".$roomInfo."' print_r(roomInfo)\n".print_r($roomInfo,true));

		return $objResponse;
	}

	#added by VAN 06-24-08
	function checkRoomNrExists($wardNr, $roomNr, $mod){
		$objResponse = new xajaxResponse();
		$ward_obj=new Ward();

		#$exists = $ward_obj->RoomNrExists($wardNr, $roomNr);
		#if ((empty($exists))|| (!($exists)))
		#	$exists = 0;
		$row = $ward_obj->RoomNrExists($wardNr, $roomNr,0);
		#if ((empty($exists))|| (!($exists)))

		if ((($ward_obj->count)&&($row['room_nr']==$roomNr))||($ward_obj->count==0))
			$exists = 0;

		#$objResponse->addAlert("exists = ".$ward_obj->sql);
		if ($mod)
			$objResponse->addScriptCall("checkRoomNrExists",$roomNr, $exists);
		else{
			$objResponse->addScriptCall("checkRoomNrExists_Payward",$roomNr, $exists);
			#$objResponse->assign("exists", "value", $exists);
		}
		return $objResponse;
	}
	#----------------------

	function isWardIDExists($ward_id=''){
		$ward_obj=new Ward();

		if ($rs = $ward_obj->getAllWardsDataObject()){
			while($result=$rs->FetchRow()){
				if ($ward_id==$result['ward_id'])
					return TRUE;
			}
			return FALSE;
		}else{
			# no entry yet in the table
			return FALSE;
		}
	}

	function setWardInfo($data){
		$newData = array();

		$newData['accomodation_type'] = $data['accomodation_type'];
		$newData['name'] = $data['name'];
		$newData['ward_id'] = $data['ward_id'];
		$newData['description'] = $data['description'];
		#$newData['ward_rate'] = $data['ward_rate'];
		$newData['roomprefix'] = $data['roomprefix']; 
		$newData['dept_nr'] = $data['dept_nr'];
		$newData['date_create'] = $data['date_create'];
        $newData['mandatory_excess'] = $data['mandatory_excess']; // added by shandy.
		$newData['history'] = $data['history'];
		$newData['create_id'] = $data['create_id'];
//		$newData['create_time'] = $data['create_time'];

		#added by VAN 04-11-08
		$newData['room_nr_start'] = $data['room_nr_start'];
		$newData['room_nr_end'] = $data['room_nr_end'];
		#----------------
		$newData['area_code'] = $data['area_code']; //added by cha, june 15, 2010
		return $newData;
	}# end of function setWardInfo

	function setRoomInfo($data,$ward_nr=0){
		$newData = array();
		#$objResponse->addAlert("type = ".$data['type_nr']);
		$newData['accomodation_type'] = $data['accomodation_type'];
		$newData['ward_nr'] = $ward_nr;
		#care_type_room
		# 1- Ward room
		# 2 - Operating room
		#$newData['type_nr']=1; // 1 = ward room type nr
		#$newData['type_nr']=$data['type_nr']; // 1 = ward room type nr

		$newData['dept_nr'] = $data['dept_nr'];
		$newData['date_create'] = $data['date_create'];
		$newData['history'] = $data['history'];
		$newData['create_id'] = $data['create_id'];
//		$newData['create_time'] = $data['create_time'];
          
		if ($data['accomodation_type']=='1'){
			# charity accomodation
			//$newData['room_nr'] = $data['room_nr'];                   #edited by pol
			//$newData['nr_of_beds'] = $data['nr_of_beds'];              #edited by pol
			//$newData['info'] = $data['info_room'];                             #edited by pol
			#$newData['room_rate'] = number_format($data['rate_room'],2,".","");
			//$newData['type_nr'] = $data['type_nr'];                      #edited by pol
            $newData['rooms'] = array();                                  #edited by pol
            $newData['beds'] = array();
            $newData['info'] = array();
            #$newData['rate'] = array();
            $newData['type'] = array();
            #print_r($data);
            foreach ($data as $key => $value){
                if (substr($key, 0, 5)=='rooms'){
                    array_push($newData['rooms'],$value);
                }
                if (substr($key, 0, 4)=='beds'){
                    array_push($newData['beds'],$value);
                }
                if (substr($key, 0, 5)=='infos'){
                    array_push($newData['info'],$value);
                }
                /*
                if (substr($key, 0, 5)=='rates'){
                    array_push($newData['rate'],$value);
                }
                */
                if (substr($key, 0, 5)=='types'){
                    array_push($newData['type'],$value);
                }
            } 
                                                                               #end edited by pol
		}else{
			$newData['rooms'] = array();
			$newData['beds'] = array();
			$newData['info'] = array();
			#$newData['rate'] = array();
			$newData['type'] = array();
			#print_r($data);
			foreach ($data as $key => $value){
				if (substr($key, 0, 5)=='rooms'){
					array_push($newData['rooms'],$value);
				}
				if (substr($key, 0, 4)=='beds'){
					array_push($newData['beds'],$value);
				}
				if (substr($key, 0, 5)=='infos'){
					array_push($newData['info'],$value);
				}
				/*
				if (substr($key, 0, 5)=='rates'){
					array_push($newData['rate'],$value);
				}
				*/
				if (substr($key, 0, 5)=='types'){
					array_push($newData['type'],$value);
				}
			}# end of for loop
#			$newData['rooms'] = $data['rooms'];
#			$newData['beds'] = $data['beds'];
#			$newData['info'] = $data['infos'];
		}
		return $newData;
	}# end of function setRoomInfo

		function update_header($x, $y, $encounter_nr) {
			$objResponse = new xajaxResponse();
			$nursing = new NursingNotes();
			//$objResponse->alert('x='.$x.'y='.$y.'e='.$encounter_nr);
			$headers = $nursing->get_header($x, $y, $encounter_nr);
			if ($headers == 'E')
				$objResponse->call('change_mode', 'insert');
			else
				$objResponse->call('change_mode', 'update');

			$objResponse->call('assign_to_axis', $x, $y);
			$objResponse->call('assign_header', $headers);
			return $objResponse;
		}

		function submit_update_header($x, $y, $record_date, $hospital_days, $day_po_pp, $encounter_nr, $mode) {
				$objResponse = new xajaxResponse();
				$nursing = new NursingNotes();
				$success = $nursing->insert_header($x, $y, $record_date, $hospital_days, $day_po_pp, $encounter_nr, $mode);
				if ($success) {
						$objResponse->call('refresh_graph', 'h');
				}
				return $objResponse;
		}

		function plot_points($x, $y, $encounter_nr, $value, $plot_what) {
				$objResponse = new xajaxResponse();
				$nursing_notes = new NursingNotes();
				$nursing_notes->save_points($x, $y, $encounter_nr, $value, $plot_what);

				$objResponse->call('refresh_graph', 't');
				return $objResponse;
		}

		function update_footer($x, $y, $encounter_nr, $options) {
			$objResponse = new xajaxResponse();
			$nursing_notes = new NursingNotes();
			$footers = $nursing_notes->get_footers($x, $y, $encounter_nr, $options);
	// $objResponse->alert($footers);
			if ($footers == 'E')
				$objResponse->call('change_mode', 'insert');
			else
				$objResponse->call('change_mode', 'update');

			$objResponse->call('assign_to_axis', $x, $y);
			$objResponse->call('assign_footer', $footers, $options);
			return $objResponse;
		}

		function submit_update_first_footer($x, $y, $respiration, $blood_pressure, $encounter_nr, $mode) {
			$objResponse = new xajaxResponse();
			$nursing_notes = new NursingNotes();
		 // $objResponse->alert($respiration);
			//$objResponse->alert($blood_pressure);
			$array = array('respiration' => $respiration, 'blood_pressure' => $blood_pressure);
			$success = $nursing_notes->insert_footer($x, $y, $array, $encounter_nr, $mode, 1);
			if ($success) {
				 $objResponse->call('refresh_graph', 'h');
			}
			return $objResponse;
		}
	function submit_update_second_footer($x, $y, $weight, $encounter_nr, $mode) {
			$objResponse = new xajaxResponse();
			$nursing_notes = new NursingNotes();

			$array = array('weight' => $weight);
			$success = $nursing_notes->insert_footer($x, $y, $array, $encounter_nr, $mode, 2);
			if ($success) {
				 $objResponse->call('refresh_graph', 'h');
			}
			return $objResponse;
		}

	function submit_update_third_footer($x, $y, $intake_oral, $parenteral, $output_urine, $drainage, $emesis, $stool, $encounter_nr, $mode) {
		$objResponse = new xajaxResponse();
			$nursing_notes = new NursingNotes();

			$array = array('intake_oral' => $intake_oral, 'parenteral' => $parenteral, 'output_urine' => $output_urine, 'drainage' => $drainage,
						 'emesis' => $emesis, 'stool' => $stool);
			$success = $nursing_notes->insert_footer($x, $y, $array, $encounter_nr, $mode, 3);
			if ($success) {
				 $objResponse->call('refresh_graph', 'h');
			}
			return $objResponse;
	}

	#-------added by VAN 10-30-09
	function setMGH($encounter_nr, $is_mgh, $mgh_date){
			global $db;
			$objResponse = new xajaxResponse();
			$enc_obj=new Encounter;

			#$released = $enc_obj->MayGoHome($encounter_nr, '0000-00-00 00:00:00', $is_mgh);
			#edited by VAN 06-27-2010
			if ($is_mgh)
				$mgh_date = date("Y-m-d H:i:s",strtotime($mgh_date));
			else
				$mgh_date = '0000-00-00 00:00:00';

			#$objResponse->alert('mgh = '.$mgh_date);
			$enc_obj->MayGoHome($encounter_nr, $mgh_date, $is_mgh);
				#$objResponse->alert($enc_obj->sql);
			/*if ($released){
				if ($is_mgh){
					 $objResponse->addScriptCall("setShowRow",$is_mgh);
				}else{
					 $objResponse->addScriptCall("setShowRow",$is_mgh);
				}
			} */
			$objResponse->call("setShowRow",$is_mgh);

			return $objResponse;
	}

	function cancelDischarged($encounter_nr){
		global $db, $HTTP_SESSION_VARS;
		$ward_obj = new Ward;
		$objResponse = new xajaxResponse();

		$bed_info = $ward_obj->getLastBedNr($encounter_nr);
		$hasbed = $ward_obj->count;
		#$objResponse->alert($hasbed);
		if ($hasbed){
			$in_ward = 1;
		}else
			$in_ward = 0;

		$discharged_update = "  in_ward = ".$in_ward.",
														is_discharged = 0,
														discharge_date = '',
														discharge_time = '',
														received_date= '',";

		$history = "CONCAT(history,'Cancel Discharge: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";

		#undo the discharge in care_encounter
		$sql_update = "UPDATE care_encounter SET
											received_date='".date("Y-m-d",strtotime($objvalue['received_date']))."',
											".$discharged_update."
											history = $history,
											modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
											modify_time = '".date('Y-m-d H:i:s')."'
											WHERE encounter_nr='".$encounter_nr."'";


		#added by VAN 11-03-09
		#undo the care_encounter_location
		$sql_update_loc = "UPDATE care_encounter_location SET
												status='',
												date_to='',
												time_to='',
												discharge_type_nr = 0,
												status = '',
												history = $history,
												modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
												modify_time = '".date('Y-m-d H:i:s')."'
												WHERE encounter_nr='".$encounter_nr."'
												ORDER BY modify_time DESC LIMIT 3";

		#delete the seg_encounter_result
		$sql_update_result = "DELETE FROM seg_encounter_result WHERE encounter_nr='".$encounter_nr."'";

		#delete the seg_encounter_disposition
		#$sql_update_disposition = "DELETE FROM seg_encounter_disposition WHERE encounter_nr='".$encounter_nr."'";
		#-------------------------------

		#$objResponse->alert($sql_update);

		$db->BeginTrans();

		#undo the discharge in care_encounter
		$ok=$db->Execute($sql_update);

		#undo the care_encounter_location
		$ok=$db->Execute($sql_update_loc);

		#delete the seg_encounter_result
		$ok=$db->Execute($sql_update_result);

		#delete the seg_encounter_disposition
		#$ok=$db->Execute($sql_update_disposition);


		if ($ok){
				$db->CommitTrans();
				$objResponse->alert("The patient status is successfully change.");
		}else{
				$db->RollbackTrans();
				$objResponse->alert("Changing patient's status is failed.");
		}

		$objResponse->addScriptCall("redirectWindow",$encounter_nr);

			return $objResponse;
	}

   
	#-------------------------------

	#added by angelo m. 10.04.2010
	#start-----------------
    
	function cancel_discharged($encounter_nr,$bed_loc_nr){
		 global $db, $HTTP_SESSION_VARS;
		 $ward_obj = new Ward;
		 $objResponse = new xajaxResponse();
         

		 /*$objResponse = new xajaxResponse();

			global $db;

			$strSQL = "UPDATE care_encounter
											SET is_discharged = '0',
													discharge_date = '(NULL)',
													discharge_time = '(NULL)',
													in_ward = 1
											WHERE encounter_nr = '$encounter_nr' ";
			$result = $db->Execute($strSQL);
			$strSQL = "UPDATE care_encounter_location
											SET status = '(NULL)',
													date_to = '(NULL)',
													time_to = '(NULL)'
											WHERE encounter_nr = '$encounter_nr' AND nr='$bed_loc_nr' ";
			$result = $db->Execute($strSQL);*/

			#edited by VAN 01-31-2011
		$bed_info = $ward_obj->getLastBedNr($encounter_nr);
		$hasbed = $ward_obj->count;
		#$objResponse->alert($hasbed);
		if ($hasbed){
			$in_ward = 1;
		}else
			$in_ward = 0;

		$discharged_update = "  in_ward = ".$in_ward.",
														is_discharged = 0,
														discharge_date = '',
														discharge_time = '',
														received_date= '',";

		$history = "CONCAT(history,'Cancel Discharge: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";

		#undo the discharge in care_encounter
		$sql_update = "UPDATE care_encounter SET
											received_date='".date("Y-m-d",strtotime($objvalue['received_date']))."',
											".$discharged_update."
											history = $history,
											modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
											modify_time = '".date('Y-m-d H:i:s')."'
											WHERE encounter_nr='".$encounter_nr."'";


		#added by VAN 11-03-09
		#undo the care_encounter_location
		$sql_update_loc = "UPDATE care_encounter_location SET
												status='',
												date_to='',
												time_to='',
												discharge_type_nr = 0,
												status = '',
												history = $history,
												modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
												modify_time = '".date('Y-m-d H:i:s')."'
												WHERE encounter_nr='".$encounter_nr."'
												ORDER BY modify_time DESC LIMIT 3";

		#delete the seg_encounter_result
		$sql_update_result = "DELETE FROM seg_encounter_result WHERE encounter_nr='".$encounter_nr."'";

		#delete the seg_encounter_disposition
		#$sql_update_disposition = "DELETE FROM seg_encounter_disposition WHERE encounter_nr='".$encounter_nr."'";
		#-------------------------------

		#$objResponse->alert($sql_update);

		$db->BeginTrans();

		#undo the discharge in care_encounter
		$ok=$db->Execute($sql_update);

		#undo the care_encounter_location
		$ok=$db->Execute($sql_update_loc);

		#delete the seg_encounter_result
		$ok=$db->Execute($sql_update_result);

		#delete the seg_encounter_disposition
		#$ok=$db->Execute($sql_update_disposition);


		if ($ok){
				$db->CommitTrans();
				$objResponse->alert("The patient status is successfully change.");
		}else{
				$db->RollbackTrans();
				$objResponse->alert("Changing patient's status is failed.");
		}

		$objResponse->addScriptCall("list_refresh");
		return $objResponse;
	}
	#end-----------------

$xajax->processRequests();
?>