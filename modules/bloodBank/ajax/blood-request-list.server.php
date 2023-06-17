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
    require_once($root_path.'include/care_api_classes/class_encounter.php');

	function populateRequestList($done, $sElem,$searchkey,$page,$include_firstname,$mod, $encounter_nr='', $is_doctor=0 ) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srv=new SegLab;
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
		$ref_source = 'BB';
		#$total_srv = $srv->countSearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr, 1);
		$ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, 0,$is_doctor, $encounter_nr,$ref_source,'', 0);
		#$objResponse->addAlert($srv->sql);
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
										if ($result["is_cash"]==1)
											$paid = 1;
										else
											$paid = 0;

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

														#added by VAN 06-03-2011
														#for temp workaround
														if (!$or_no){
																 $sql_manual = "SELECT * FROM seg_payment_workaround WHERE service_area='LB' AND refno='".trim($result["refno"])."' AND is_deleted=0";
																 $res_manual=$db->Execute($sql_manual);
																 $row_manual_count=$res_manual->RecordCount();
																 $row_manual = $res_manual->FetchRow();

																 $or_no = $row_manual['control_no'];
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

				if (empty($result["parent_refno"]))
					$repeat = 0;
				else
					$repeat = 1;


				if ($mod){
					$labresult = $srv->hasResult(trim($result["refno"]));

					if ($labresult)
						$labstatus = 1;
					else
						$labstatus = 0;

					if ($result["type_charge"]){
						$result2['or_no'] = $result['charge_name'];
					}

					#added by VAN 05-31-2011
					$sql_rec = "SELECT * FROM seg_blood_received_header WHERE refno='".trim($result["refno"])."'";

					$res_rec=$db->Execute($sql_rec);
					$row_rec_count=$res_rec->RecordCount();
					$row_rec = $res_rec->FetchRow();

					#complete, lack or no sample
					if ($row_rec_count){
						if ($row_rec['status']=='none')
							$withsample = 'NO SAMPLE';
						else
							$withsample = mb_strtoupper($row_rec['status']);

						$sql_rec_d = "SELECT s.name, r.*
													FROM seg_blood_received_sample AS r
													INNER JOIN seg_lab_services AS s ON s.service_code=r.service_code
													WHERE refno = '".trim($result["refno"])."'";
						$res_rec_d=$db->Execute($sql_rec_d);
						$i = 1;
						$details_rec = '';
						while ($row_rec_d=$res_rec_d->FetchRow()){
							$details_rec .= '<br>'.$i.'.) Test: &nbsp;&nbsp;<font color=\'#000066\'><b>'.$row_rec_d["name"].' </b></font><br>
																		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Number of Received Unit: &nbsp;&nbsp;<font color=\'#000066\'><b>'.$row_rec_d["received_qty"].' out of '.$row_rec_d["ordered_qty"].'</font></b>';
							$i++;
						}
					}else{
						$withsample = 'NO SAMPLE';
					}
                    
                    #for hact patient
                    $request_time = $result['serv_dt']." ".$result['serv_tm'];
                    $request_time = date("Y-m-d H:i:s", strtotime($request_time));
                    $row_hact = $srv->checkHactInfo($result['pid'], $request_time);
                    #echo $srvObj->sql;
                    #$objResponse->alert($srv->sql);
                    if ($row_hact['status']=='hact')
                        $is_hact = 1;
                    else
                        $is_hact = 0;
                    

					$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $labstatus, $paid, $repeat,trim($result["pid"]),floor($age),$result["sex"],$location, $enctype,$or_no,$result["is_cash"], $withsample, $is_hact, $details_rec);
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
        $enc_obj=new Encounter;
        
		$objResponse = new xajaxResponse();

		$sql = "SELECT * FROM seg_pay_request
							WHERE ref_source = 'LD' AND ref_no = ".$db->qstr($refno);
                            
        $res=$db->Execute($sql);
		$row=$res->RecordCount();
		
         
        #get encounter and charge type info
        $ref = $db->GetRow("SELECT encounter_nr,IF(is_cash,NULL,grant_type) AS charge_type FROM seg_lab_serv\n".
                            "WHERE refno=".$db->qstr($refno));
         
        #check if the encounter of the request has a final bill                    
        $hasfinal_bill = $enc_obj->hasFinalBilling($ref['encounter_nr']);
        
		#if ($row==0){
        if (($row==0)&&(!$hasfinal_bill)){
            $status=$srv->deleteRequestor($refno);
            #$status = 1;
			if ($status) {
				$srv->deleteLabServ_details($refno);
				$objResponse->addScriptCall("removeRequest",$refno);
				
                #added by VAS 03-23-2012
                #update the applied coverage. minus the total of the cancelled request
                if ($ref['charge_type'] == 'phic') {
                    #get all items and store in an array
                    $sql_item = "SELECT service_code, price_cash*quantity AS total, is_served
                                    FROM seg_lab_servdetails d
                                    INNER JOIN seg_lab_serv s ON s.refno=d.refno
                                    WHERE s.refno=".$db->qstr($refno)."
                                    AND s.grant_type=".$db->qstr($ref['charge_type'])." AND d.is_served=1";
                    
                    $rs = $db->Execute($sql_item);
                    
                    if ($rs){ 
                        
                        while($item_details=$rs->FetchRow()) {
                            # Handle applied coverage for PHIC and other benefits
                            # Hardcode hcare ID (temporary workaround)
                            define('__PHIC_ID__', 18);
                            
                            $item = $item_details['service_code'];
                            
                            $sql_app = "SELECT coverage FROM seg_applied_coverage\n".
                                            "WHERE ref_no='T{$ref['encounter_nr']}'\n".
                                            "AND source='L'\n".
                                            "AND item_code=".$db->qstr($item)."\n".
                                            "AND hcare_id=".__PHIC_ID__;
                            
                            #less the cancelled or deleted item                                                    
                            $coverage = parseFloatEx($db->GetOne($sql_app)) - parseFloatEx($item_details['total']);
                            
                            $result = $db->Replace('seg_applied_coverage',
                                                    array(
                                                         'ref_no'=>"T{$ref['encounter_nr']}",
                                                         'source'=>'L',
                                                         'item_code'=>$item,
                                                         'hcare_id'=>__PHIC_ID__,
                                                         'coverage'=>$coverage
                                                    ),
                                                    array('ref_no', 'source', 'item_code', 'hcare_id'),
                                                    $autoquote=TRUE
                                               );
                        } 
                        $withcoverage=1;                 
                    }    
                }                    
                
                if ($withcoverage)
				    $objResponse->addAlert("The request is successfully deleted and Update the applied coverage.");
                else
                    $objResponse->addAlert("The request is successfully deleted.");    
			}else
				$objResponse->addScriptCall("showme", $srv->sql);
		 }else{
                if ($hasfinal_bill)
                    $objResponse->addAlert("Unable to delete the request. It has a saved bill or a final bill.");
                elseif ($row)    
				    $objResponse->addAlert("Unable to delete the request. It is already or partially paid.");
                else
                    $objResponse->addAlert("Unable to delete the request.");    
		 }
		return $objResponse;
	}
    
    

		function populate_promissory_note($search_field, $keyword, $page) {

			global $db;
			$objResponse = new xajaxResponse();
			 $person_obj=new Person();
			$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
			$glob_obj->getConfig('pagin_patient_search_max_block_rows');
			$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

			$offset = $page * $maxRows;

			$query = "select COUNT(*) as num from seg_lab_serv sls INNER JOIN care_person cp ON (sls.pid=cp.pid) where ref_source='BB'";
			$result = $db->Execute($query);
			$row = $result->FetchRow();
			$total = $row['num'];
			$lastPage = floor($total/$maxRows);
			if ((floor($total%10))==0) $lastPage = $lastPage-1;
			if ($page > $lastPage) $page=$lastPage;

			$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
			$objResponse->addScriptCall("clearList","RequestList");

			$query = "select refno, sls.pid, cp.sex, sls.is_urgent, cp.name_first, cp.name_middle, cp.name_last, sls.ordername,
								sls.serv_dt, sls.serv_tm, cp.date_birth, cp.age from seg_lab_serv sls INNER JOIN care_person cp ON (sls.pid=cp.pid)
								where ref_source='BB'";

			$result = $db->Execute($query);
			while ($row = $result->FetchRow()) {

				$urgency = $row["is_urgent"]?"Urgent":"Normal";
				if ($row["pid"]!=" ")
					$name = ucwords(strtolower(trim($row["name_first"])))." ".ucwords(strtolower(trim($row["name_middle"])))." ".ucwords(strtolower(trim($row["name_last"])));
				else
					$name = trim($row["ordername"]);

					if (!$name) $name='<i style="font-weight:normal">No name</i>';

					if ($row["serv_dt"]) {
						$date = strtotime($row["serv_dt"]);
						$time = strtotime($row["serv_tm"]);
						$request_date = date("M d, Y",$date)." ".date("h:i A",$time);
					}

				if ($row["date_birth"]!='0000-00-00')
					$age = $person_obj->getAge(date("m/d/Y",strtotime($row["date_birth"])),true,date("m/d/Y"));
				else
					$age = $row["age"];

				$details = new stdclass();
				$details->listID = 'RequestList';
				$details->refno = trim($row['refno']);
				$details->name = $name;
				$details->pid = $row['pid'];
				$details->age = floor($age);
				$details->request_date = $request_date;
				$details->sex = $row['sex'];
				$details->urgency = $urgency;
				$objResponse->addScriptCall("add_promissory_person","RequestList",$details);

			}


			if ($search_field) {
						$objResponse->addScriptCall("endAJAXSearch",$search_field);
			}

		 return $objResponse;
		}

		#added by VAN 01-09-10
		function savedServedPatient($refno, $service_code,$is_served){
			global $db, $HTTP_SESSION_VARS;

			$objResponse = new xajaxResponse();
			$srv=new SegLab;
			#$objResponse->addAlert("ajax : refno, code = ".$refno." , ".$service_code);

			if ($is_served)
				$date_served = date("Y-m-d H:i:s");
			else
				$date_served = '';

			$save = $srv->ServedLabRequest($refno, $service_code, $is_served, $date_served);
			#$objResponse->addAlert("sql = ".$srv->sql);
			if ($save){
				$objResponse->addScriptCall("ReloadWindow");
			}

			return $objResponse;

		}

		#added by VAN 01-09-10
		function savedSentOutRequest($refno,$service_code,$reason, $key, $page,$mod){
			global $db, $HTTP_SESSION_VARS;

			$objResponse = new xajaxResponse();
			$srv=new SegLab;		#$objResponse->alert('here = '.$refno." , ".$service_code." , ".$reason);

			$save = $srv->SentOutLabRequest($refno,0,$service_code,$reason);
			#$objResponse->alert("sql = ".$srv->sql);

			if ($save){#(searchID, page, mod)
				$objResponse->addScriptCall("startAJAXSearch2",$key, $page,$mod,0);
				#	$objResponse->addScriptCall("ReloadWindow");
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