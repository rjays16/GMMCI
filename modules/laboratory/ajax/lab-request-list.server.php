<?php
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require($root_path.'include/care_api_classes/class_pharma_transaction.php');
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	require($root_path.'include/care_api_classes/class_discount.php');
	require($root_path.'modules/laboratory/ajax/lab-new.common.php');

	#require_once($root_path.'include/care_api_classes/inventory/class_inventory.php');

	require_once($root_path.'include/care_api_classes/class_department.php');
	require_once($root_path.'include/care_api_classes/class_personell.php');
	require_once($root_path.'include/care_api_classes/class_ward.php');

	require_once($root_path.'include/care_api_classes/class_person.php');
	require_once($root_path.'include/care_api_classes/class_special_lab.php');

	function populateRequestList($done, $sElem,$searchkey,$page,$include_firstname,$mod, $encounter_nr='', $is_doctor=0 ) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srv=new SegLab();
		$dept_obj=new Department;
		$ward_obj = new Ward;
		$person_obj=new Person();

		$offset = $page * $maxRows;

		$searchkey = utf8_decode($searchkey);

		if ($searchkey==NULL)
			$searchkey = 'now';

		$cond= '';
		if ($is_perpatient){
			if ($encounter_nr)
				$cond= "AND r.pid='$pid' AND e.encounter_nr='$encounter_nr'";
			else
				$cond= "AND r.pid='$pid'";
		}

		#$total_srv = $srv->countSearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr, 1);
		#$total_srv = $srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr,$cond, 1,1);
		$ref_source = 'SPL';

		$ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, 0,$is_doctor, $encounter_nr,$ref_source,'', 0);
		#$objResponse->addAlert($srv->sql);

		#$total = $srv->count;
		$total = $srv->FoundRows();
		$lastPage = floor($total/$maxRows);
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		#$ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr,$cond, 1,0);
		#$objResponse->addAlert("sql = ".$srv->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","RequestList");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {

				$urgency = $result["is_urgent"]?"Urgent":"Normal";
				if ($result["pid"]!=" ")
					$name = ucwords(strtolower(trim($result["name_first"])))." ".ucwords(strtolower(trim($result["name_middle"])))." ".ucwords(strtolower(trim($result["name_last"])));
				else
					$name = trim($result["ordername"]);

				if (!$name) $name='<i style="font-weight:normal">No name</i>';

				if ($result["serv_dt"]) {
					$date = strtotime($result["serv_dt"]);
					$time = strtotime($result["serv_tm"]);
					$requestDate = date("M d, Y",$date)." ".date("h:i A",$time);
				}

				$sql = "SELECT c.charge_name, d.*
													FROM seg_lab_servdetails AS d
													LEFT JOIN seg_type_charge AS c ON c.id=d.request_flag
													WHERE refno='".trim($result["refno"])."'
													AND status NOT IN ('deleted','hidden','inactive','void')
													AND request_flag IS NOT NULL ORDER BY ordering LIMIT 1";

								 $res=$db->Execute($sql);
								 $row=$res->RecordCount();
								 $result_paid = $res->FetchRow();
								 $or_no = '';

								 if ($row==0){
										$paid = 0;
								 }else{
										 $paid = 1;

										 if ($result_paid["request_flag"]=='paid'){
												$sql_paid = "SELECT pr.or_no, pr.ref_no,pr.service_code
																					FROM seg_pay_request AS pr
																					INNER JOIN seg_pay AS p ON p.or_no=pr.or_no AND p.pid='".$result["pid"]."'
																					WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
																					AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00') LIMIT 1";
														$rs_paid = $db->Execute($sql_paid);
														if ($rs_paid){
																$result2 = $rs_paid->FetchRow();
																$or_no = $result2['or_no'];
														}
										 }elseif ($result_paid["request_flag"]=='charity'){
												$sql_paid = "SELECT pr.grant_no AS or_no, pr.ref_no,pr.service_code
																					FROM seg_granted_request AS pr
																					WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
																					LIMIT 1";
												$rs_paid = $db->Execute($sql_paid);
												if ($rs_paid){
														$result2 = $rs_paid->FetchRow();
														$or_no = 'CLASS D';
												}
										 }elseif (($result_paid["request_flag"]!=NULL)||($result_paid["request_flag"]!="")){
											 if ($withOR)
													$or_no = $off_rec;
												else
													$or_no = $result_paid["charge_name"];
										 }
								}

				if ($result["date_birth"]!='0000-00-00')
					$age = $person_obj->getAge(date("m/d/Y",strtotime($result["date_birth"])),true,date("m/d/Y"));
				else
					$age = $result["age"];

				if ($result['encounter_type']==1){
					$enctype = "ERPx";
					$location = "EMERGENCY ROOM";
				}elseif ($result['encounter_type']==2){
					#$enctype = "OUTPATIENT (OPD)";
					$enctype = "OPDx";
					$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
					$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
				}elseif (($result['encounter_type']==3)||($result['encounter_type']==4)){
					if ($result['encounter_type']==3)
						$enctype = "INPx (ER)";
					elseif ($result['encounter_type']==4)
						$enctype = "INPx (OPD)";

					$ward = $ward_obj->getWardInfo($result['current_ward_nr']);
					$location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$result['current_room_nr'];
				}else{
					$enctype = "WPx";
					$location = 'WALK-IN';
				}

				#---------------------

				if ($mod){
					$labresult = $srv->hasResult(trim($result["refno"]));

					if ($labresult)
						$labstatus = 1;
					else
						$labstatus = 0;

					if ($result["type_charge"]){
						$result2['or_no'] = $result['charge_name'];
					}

					$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $labstatus, $paid, trim($result["pid"]),floor($age),$result["sex"],$location, $enctype,$or_no,$result["is_cash"]);
				}else{
					$labresult = $srv->hasResult(trim($result["refno"]), $result["service_code"]);

					if ($labresult)
						$labstatus = 1;
					else
						$labstatus = 0;

					if ($result["type_charge"]){
						$result2['or_no'] = $result['charge_name'];
					}
					$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency,$result2['or_no'], $result["service_name"], $result["service_code"], $repeat, trim($result["pid"]),floor($age),$result["sex"],$location, $enctype);
				}
				#$count++;
			}
		}
		if (!$rows) $objResponse->addScriptCall("addPerson","RequestList",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}

	function deleteRequest($refno){
		global $db;
		$srv=new SegLab;
		$objResponse = new xajaxResponse();

		#$objResponse->addAlert("ajax deleteRequest refno = $refno");
		$sql = "SELECT * FROM seg_pay_request
							WHERE ref_source = 'LD' AND ref_no = '$refno'";

		 $res=$db->Execute($sql);
		 #$objResponse->addAlert("sql = ".$sql);
		 $row=$res->RecordCount();
		 #$objResponse->addAlert("row = ".$row);

		if ($row==0){

			$status=$srv->deleteRequestor($refno);

			if ($status) {
				$srv->deleteLabServ_details($refno);
				$objResponse->addScriptCall("removeRequest",$refno);
				#$objResponse->addAlert("deleteRequest sql = ".$srv->sql);
				#$objResponse->addScriptCall("reload_page");
				$objResponse->addAlert("The request is successfully deleted.");
			}else
				$objResponse->addScriptCall("showme", $srv->sql);
		 }else{
				$objResponse->addAlert("The request cannot be deleted. It is already or partially paid.");
		 }
		return $objResponse;
	}

		#added by VAN 01-09-10
		function savedServedPatient($refno, $service_code,$is_served){
			global $db, $HTTP_SESSION_VARS;

			$objResponse = new xajaxResponse();
			$splabObj = new SegSpecialLab();
			#$objResponse->addAlert("ajax : refno, code = ".$refno." , ".$service_code);

			if ($is_served)
				$date_served = date("Y-m-d H:i:s");
			else
				$date_served = '';

			$save = $splabObj->ServedLabRequest($refno, $service_code, $is_served, $date_served);
			#$objResponse->addAlert("sql = ".$splabObj->sql);
			if ($save){
				$objResponse->addScriptCall("ReloadWindow");
			}

			return $objResponse;

		}

	#added by VAN 01-09-10
		function servedRequest($qty_approved, $refno,$service_code, $key, $page,$mod,$is_served=0){
			global $db, $HTTP_SESSION_VARS;

			$objResponse = new xajaxResponse();
			$srv=new SegLab;
			#$objResponse->alert('qty_approved = '+$qty_approved);

			$sql1 = "SELECT quantity FROM seg_lab_servdetails
							 WHERE refno='".$refno."' AND service_code='".$service_code."'";
			$rs1 = $db->Execute($sql1);
			if ($rs1)
				$row1 = $rs1->FetchRow();

			if ($qty_approved > $row1['quantity']){
					$objResponse->alert('Entered quantity exceeds as it requested.');
			}else{
				if (!$row1['quantity'])
					$row1['quantity'] = 0;

				$date_served = date("Y-m-d H:i:s");
				$save = $srv->ServedLabRequest2($qty_approved,$row1['quantity'], $refno, 0, $is_served, $date_served, $service_code,'done');
				#$objResponse->addAlert("sql = ".$srv->sql);

				if ($save){#(searchID, page, mod)
					$objResponse->addScriptCall("startAJAXSearch2",$key, $page,$mod,0);
					#$objResponse->addScriptCall("ReloadWindow");
				}

			}
			return $objResponse;
		}
	#---------------------

$xajax->processRequests();
?>