<?php
	#created by VAN 04-08-08
	
	// added by Francis 07-18-13
	function moveToWaitingList($enr){
		global $db, $HTTP_SESSION_VARS;
		
		$objResponse = new xajaxResponse();
		$enc_obj = new Encounter;
		$resultFailed = "Failed to transfer patient.";

		$move = $enc_obj->MoveToWaitingList($enr);
		if($move){
			$objResponse->addScriptCall("refreshWindow",NULL);
		}else{
			$objResponse->alert($resultFailed);
		}
		

		  return $objResponse;
	}



	function populateWardList($sElem,$searchkey,$page,$personell_nr) {
		global $db, $HTTP_SESSION_VARS;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		$ward_obj = new Ward;
		$pers_obj=new Personell;
		$offset = $page * $maxRows;
		#$objResponse->addAlert("searchkey = ".$searchkey);
		$searchkey = utf8_decode($searchkey);
		if ($searchkey==NULL)
			$searchkey = '*';
		$total_srv = $ward_obj->countSearchNursingWard($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql c1 = ".$ward_obj->sql);
		$total = $ward_obj->count;
		#$objResponse->addAlert("total = ".$personell_nr);
		$lastPage = floor($total/$maxRows);
		
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;
		
		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$ward_obj->SearchNursingWard($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql c2 = ".$ward_obj->sql);
		$rows=0;
		$row_per = $pers_obj->get_Personell_info($personell_nr);
		$job = substr($row_per['short_id'],0,1);
		#$objResponse->alert(print_r($HTTP_SESSION_VARS,true));sess_permission
		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","labgrouplistTable");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			
			$admin_permission = array('System_Admin', '_a_0_all');
			
			for ($i=0; $i<sizeof($admin_permission);$i++){
					if (ereg($admin_permission[$i],$HTTP_SESSION_VARS['sess_permission'])){
							$allow_all = 1;
							break;
					}else
							$allow_all = 0; 		     
			}

			#$objResponse->alert($allow_all);
			#$assigned = in_array($HTTP_SESSION_VARS['sess_permission'], $admin_permission));
			while($result=$ergebnis->FetchRow()) {
				if ((!$row_per['is_reliever'])&&($job=='N')){
					$ward_row = $pers_obj->get_Nurse_Ward_Area_Assign($personell_nr, $result['nr']);
					$assigned_ward =$pers_obj->count;
				}elseif ((($row_per['is_reliever'])&&($job=='N'))||($allow_all)){
					$assigned_ward =1;
				}else{
					$assigned_ward =0;
				}	
				#$objResponse->alert($pers_obj->sql);
				#$objResponse->addAlert("sql c2 = ".$result["group_code"]." , ".$result["name"]." , ".$result["other_name"]);
			   $rooms = $result["room_nr_start"]." - ".$result["room_nr_end"];
				#$objResponse->alert($result['nr']." - ".$with_ward);	
				$ward_id = strtr('nursing-station-pass.php'.URL_APPEND.'&rt=pflege&edit=1&station='.urlencode($result['ward_id']).'&location_id='.urlencode($result['ward_id']).'&ward_nr='.$result['nr'],' ',' ');
				#$objResponse->addScriptCall("addWardList","labgrouplistTable",stripslashes($result["ward_id"]),stripslashes($result["name"]),$rooms,number_format($result["ward_rate"],2));
				#$objResponse->addScriptCall("addWardList","labgrouplistTable",$ward_id,strtoupper($result['ward_id']),stripslashes($result["name"]),$rooms,number_format($result["ward_rate"],2));
				$objResponse->addScriptCall("addWardList","labgrouplistTable",$ward_id,strtoupper($result['ward_id']),stripslashes($result["name"]),$rooms,number_format($result["ward_rate"],2),$result["is_temp_closed"],$result['nr'],$assigned_ward);
			}
		}
		#commented by VAN 03-17-08
		if (!$rows) $objResponse->addScriptCall("addWardList","labgrouplistTable",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		
		return $objResponse;
	}
	
	#----------------------
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	
	#added by VAN 03-10-08
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	
	require($root_path.'include/care_api_classes/class_ward.php');
	require($root_path.'include/care_api_classes/class_personell.php');
	require($root_path."modules/nursing/ajax/nursing-ward-common.php");
	$xajax->processRequests();
?>