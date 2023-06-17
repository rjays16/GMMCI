<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/dialysis/ajax/dialysis-transaction.common.php');
require_once($root_path.'include/care_api_classes/dialysis/class_dialysis.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_social_service.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/care_api_classes/class_order.php');
require_once($root_path.'include/care_api_classes/or/class_segOr_miscCharges.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');
require_once($root_path."include/care_api_classes/billing/class_bill_info.php");

function getDoctors($nr="") {
	global $db;
	$objResponse = new xajaxResponse();
	$dialysis_obj = new SegDialysis();

	$result = $dialysis_obj->getDialysisPersonell(1, "doctor");
	//$objResponse->alert("doctor=".$dialysis_obj->sql);
	if($result){
		$options.="<option value='0'>-Select requesting doctor-</option>";
		while($data=$result->FetchRow())
		{
			$name = $data["name_last"].", ".$data["name_first"]." ".$data["name_middle"];
			if($nr==$data["personell_nr"])
				$options.="<option value='".$data["personell_nr"]."' selected='selected'>".$name."</option>";
			else
				$options.="<option value='".$data["personell_nr"]."'>".$name."</option>";
		}
		$objResponse->assign("request_doctor","innerHTML",$options);
	}else {
		$objResponse->assign("request_doctor","innerHTML", "<option value='0'>-No Doctor available-</option>");
	}

	return $objResponse;
}

function getNurses($nr="") {
	global $db;
	$objResponse = new xajaxResponse();
	$dialysis_obj = new SegDialysis();

	$result = $dialysis_obj->getDialysisPersonell(1, "nurse");
	//$objResponse->alert("nurse=".$dialysis_obj->sql);
	if($result){
		$options.="<option value='0'>-Select attending nurse-</option>";
		while($data=$result->FetchRow())
		{
			$name = $data["name_last"].", ".$data["name_first"]." ".$data["name_middle"];
			if($nr==$data["personell_nr"])
				$options.="<option value='".$data["personell_nr"]."' selected='selected'>".$name."</option>";
			else
				$options.="<option value='".$data["personell_nr"]."'>".$name."</option>";
		}
		$objResponse->assign("attending_nurse","innerHTML",$options);
	}else {
		$objResponse->assign("attending_nurse","innerHTML", "<option value='0'>-No Doctor available-</option>");
	}

	return $objResponse;
}

function setVisitNo($pid)
{
	global $db;
	$objResponse = new xajaxResponse();
	$dialysis_obj = new SegDialysis();

	$sql = "SELECT count(refno) FROM seg_dialysis_transaction WHERE pid=".$db->qstr($pid);
	$visit_no = $db->GetOne($sql);
	if($visit_no=="")
		$visit_no=0;
	$objResponse->assign("visit_no", "value", $visit_no);
	$objResponse->assign("visit_number", "value", $visit_no);   //for history
	return $objResponse;
}

function populatePersonList($sElem,$searchkey,$page,$include_firstname,$include_encounter=TRUE,$include_walkin=FALSE,$exclude_mgh=FALSE,$from_dialysis="")
{
	global $db;
	$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('pagin_patient_search_max_block_rows');
	$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

	$objResponse = new xajaxResponse();

	$person=& new Person();
	$dept_obj = new Department;
	$ward_obj = new Ward;

	#added by VAN 06-25-08
	$objSS = new SocialService;

	$offset = $page * $maxRows;
	if ($include_encounter) {
		$ergebnis=$person->SearchSelectWithCurrentEncounter($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$exclude_mgh);	//modified cha, june 1,2010
		#$objResponse->alert($person->sql);
		$objResponse->alert("with cur enc");
		$total = $person->FoundRows();
		$lastPage = floor($total/$maxRows);
		if ($page > $lastPage) $page=$lastPage;
	}
	else {
		$ergebnis=$person->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
		#$objResponse->alert($person->sql);
		$objResponse->alert("with/o");
		$total = $person->FoundRows();
		$lastPage = floor($total/$maxRows);
		if ($page > $lastPage) $page=$lastPage;
	}
	$rows=0;

	$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
	$objResponse->call("clearList","person-list");
	$details = (object) 'details';
	if ($ergebnis) {
		$rows=$ergebnis->RecordCount();
		while($result=$ergebnis->FetchRow()) {
			$addr = implode(", ",array_filter(array($result['street_name'], $result["brgy_name"], $result["mun_name"])));
			if ($result["zipcode"])
				$addr.=" ".$result["zipcode"];
			if ($result["prov_name"])
				$addr.=" ".$result["prov_name"];
			$addr = trim($addr);

			if (($result["parent_mss_id"])&&($result["parent_mss_id"]=='D')){
				$discountid = $result["parent_mss_id"];
				if ($result["discountid"]=='SC')
					$senior_citizen = 1;
			}else{
				$discountid = $result["discountid"];
				$senior_citizen = 0;
			}

			$discount = $result["discount"];
			#$objResponse->alert('d = '.$result['date_birth']);
			#if (($data['date_birth'])&&(($data['date_birth']!='00/00/0000')||($data['date_birth']!='')||($data['date_birth']!=NULL))){
							#if (($result['date_birth'])&&(($result['date_birth']!='0000-00-00')||($result['date_birth']!='')||($result['date_birth']!=NULL))){
							if (($result['date_birth']) && ($result['date_birth']!='0000-00-00')){
				$dob = date("Y-m-d",strtotime($result['date_birth']));
			}else{
				$dob = 'unknown';
								 # $objResponse->alert('d = '.$result['date_birth']);
							 }
			#$dob = $result["date_birth"];

			$lastId = trim($result["pid"]);
			$details->id = trim($result["pid"]);
			$details->lname = trim($result["name_last"]);
			$details->fname = trim($result["name_first"]);
			$details->mname = trim($result["name_middle"]);

			$details->dob = $dob;
			$details->sex = trim($result["sex"]);
			$details->age = trim($result["age"]);
			$details->addr = trim($addr);
			$details->zip = trim($result["zipcode"]);
			$details->status = trim($result["status"]);
			$details->nr = trim($result["encounter_nr"]);
			$details->type = trim($result["encounter_type"]);

			$details->senior_citizen = trim($senior_citizen);
			$details->discountid = trim($discountid);
			$details->discount = trim($discount);

			$details->adm_diagnosis = trim(htmlentities($result['er_opd_diagnosis']));
			if (!$details->adm_diagnosis) {
				$details->adm_diagnosis = 'N/A';
			}

			$details->orig_discountid = trim($result["discountid"]);
			$details->rid = trim($result['rid']);


			$details->admission_dt = '';
			$details->discharge_date = '';

			#added by VAN 06-02-08
			if ($result["encounter_type"]==1){
				$details->enctype = "ER PATIENT";
				$details->location = "EMERGENCY ROOM";
			}elseif ($result["encounter_type"]==2){
				$details->enctype = "OUTPATIENT";
				$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
				$details->location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
			}
			//added by cha, july 23, 2010
			elseif($result["encounter_type"]==5){
				$details->enctype = "DIALYSIS PATIENT";
				//$details->location = "DIALYSIS DEPT.";
				$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
				$details->location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
			}
			elseif (($result["encounter_type"]==3)||($result["encounter_type"]==4)){
				if ($result["encounter_type"]==3)
					$details->enctype = "INPATIENT (ER)";
				elseif ($result["encounter_type"]==4)
					$details->enctype = "INPATIENT (OPD)";

				$ward = $ward_obj->getWardInfo($result['current_ward_nr']);
				#echo "sql = ".$ward_obj->sql;
				$details->location = strtoupper(strtolower(stripslashes($ward['name'])))."&nbsp;&nbsp;&nbsp;Room # : ".$result['current_room_nr'];

				if (($result["admission_dt"])&&(($result["admission_dt"]!='0000-00-00 00:00:00')||(empty($result["admission_dt"]))))
					$details->admission_dt = date("m/d/Y h:i A ",strtotime($result["admission_dt"]));

				if (($result["discharge_date"])&&(($result["discharge_date"]!='0000-00-00')||(empty($result["discharge_date"]))))
					$details->discharge_date = date("m/d/Y h:i A ",strtotime($result["discharge_date"]));
			}else{
				$details->enctype = "WALK-IN";
				#$details->location = 'WALK-IN';
				$details->location = 'None';
			}

			$details->is_medico = $result['is_medico'];
			$details->in_walkin = $result['in_walkin'];

			$details->date_admitted = $result['admission_dt'];  //added by omick, may 26, 2009
			$details->room_ward = $details->location; //added by omick, may 26, 2009
			$details->dept_nr = $result['current_dept_nr'];	//added by cha, may 18, 2010
			$details->ward_nr = $result['current_ward_nr'];	//added by cha, may 19, 2010
			$details->room_nr = $result['current_room_nr'];	//added by cha, may 19, 2010
			$details->civil_status = $result['civil_status'];	//added by cha, july 21, 2010
			$details->photo_filename = $result['photo_filename'];	//added by cha, july 21, 2010
			$details->from_dialysis = $from_dialysis;	//added by cha, july 22, 2010

			if ($result['current_ward_nr']){
				$ward_sql = "SELECT * FROM care_ward AS w WHERE w.nr='".$result['current_ward_nr']."'";
				$ward_info = $db->GetRow($ward_sql);
				if ($ward_info['accomodation_type']==1)
					#CHARITY
					$area_type = 'ch';
				elseif ($ward_info['accomodation_type']==2)
					#PAYWARD
					$area_type = 'pw';
			}

			$details->area_type = $area_type;

			$objResponse->call("addPerson","person-list", $details);

		}
	}
	else {
		$details->error = nl2br(htmlentities($person->sql));
	}
	if (!$rows) $objResponse->call("addPerson","person-list",$details);

	if ($rows==1 && $lastId) {
		$objResponse->call("prepareSelect",$lastId);
	}

	if ($sElem) {
		$objResponse->call("endAJAXSearch",$sElem);
	}
	return $objResponse;
}

function populateMiscRequests($encounter_nr)
{
	global $db;
	$objResponse = new xajaxResponse();
	$misc_obj = new SegOR_MiscCharges();

	$refno = $misc_obj->getMiscRefno($encounter_nr, 'DIALYSIS');
	//$objResponse->alert("misc\n".$misc_obj->sql."\nrefno=".$refno);
	if($refno){
		 $objResponse->call("createTableHeader", "misc_requests", "misc-list".$refno, $refno);
		 $res = $misc_obj->getMiscOrderItems($encounter_nr,'DIALYSIS');
		// $objResponse->alert("misc\n".$misc_obj->sql);
		 if($res!==FALSE){
			 $req_flag=false;
			 while($row=$res->FetchRow())
			 {
				 switch(strtolower($row["request_flag"]))
				 {
					 case 'cmap':
							$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
							$req_flag=true;
							break;
					 case 'lingap':
							$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
							$req_flag=true;
							break;
					 case 'paid':
							$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
							$req_flag=true;
							break;
					 case 'charity':
							$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
							$req_flag=true;
							break;
					 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
				 }

				 $data = array(
						'refno'=>$refno,
						'order_date'=>date('m-M-Y h:i: a',strtotime($row["chrge_dte"])),
						'status'=>$request_flag,
						'item_name'=>$row["name"],
						'item_code'=>$row["code"],
						'item_qty'=>$row["quantity"],
						'item_prc'=>$row["chrg_amnt"],
						'total_prc'=>parseFloatEx($row["quantity"]*$row["chrg_amnt"])
					);
					$objResponse->call("printRequestlist", "misc_requests", "misc-list".$refno, $data);
			 }
			 if($req_flag==true)
			 {
				$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
									'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
			 }else {
				$buttons = '<button class="segButton" onclick="openEditRequest(\'misc_requests\',\''.$refno.'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
									'<button class="segButton" onclick="openDeleteRequest(\'misc_requests\',\''.$refno.'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
			 }
			 $objResponse->assign("btn-".$refno,"innerHTML", $buttons);
		 }
	}
	return $objResponse;
}

function populateIpRequests($encounter_nr)
{
	global $db;
	$objResponse = new xajaxResponse();
	$order_obj = new SegOrder();
	$filters = array('inpatient'=>$encounter_nr,'area'=>'IP');
	$res = $order_obj->getActiveOrders($filters, 0, 10);
	//$objResponse->alert("IP order\n".$order_obj->sql);
	if($res!==FALSE) {
		while($row=$res->FetchRow())
		{
			$result = $order_obj->getOrderItemsFullInfo($row["refno"],'');
			//$objResponse->alert("details\n".$order_obj->sql);
			if($result!==FALSE) {
				$objResponse->call("createTableHeader", "ip_requests", "ip-list".$row["refno"], $row["refno"]);
				$req_flag=false;
				while($row2=$result->FetchRow())
				{
					switch(strtolower($row2["request_flag"]))
					{
						 case 'cmap':
								$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
								$req_flag=true;
								break;
						 case 'lingap':
								$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
								$req_flag=true;
								break;
						 case 'paid':
								$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
								$req_flag=true;
								break;
						 case 'charity':
								$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
								$req_flag=true;
								break;
						 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
					}

					$data = array(
						'refno'=>$row["refno"],
						'order_date'=>date('m-M-Y h:i: a',strtotime($row["orderdate"])),
						'status'=>$request_flag,
						'is_served'=>$row2["serve_status"],
						'item_name'=>$row2["artikelname"],
						'item_code'=>$row2["bestellnum"],
						'item_qty'=>$row2["quantity"],
						'item_prc'=>$row2["force_price"],
						'total_prc'=>parseFloatEx($row2["quantity"]*$row2["force_price"])
					);
					$objResponse->call("printRequestlist", "ip_requests", "ip-list".$row["refno"], $data);
				}
				if($req_flag==true)
				{
					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
				}else {
					$buttons = '<button class="segButton" onclick="openEditRequest(\'ip_requests\',\''.$row["refno"].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" onclick="openDeleteRequest(\'ip_requests\',\''.$row["refno"].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				}
				$objResponse->assign("btn-".$row["refno"],"innerHTML", $buttons);
			}
		}
	}
	return $objResponse;
}

function populateMgRequests($encounter_nr)
{
	global $db;
	$objResponse = new xajaxResponse();
	$order_obj = new SegOrder();
	$filters = array('inpatient'=>$encounter_nr,'area'=>'MG');
	$res = $order_obj->getActiveOrders($filters, 0, 10);
	//$objResponse->alert("Mg order\n".$order_obj->sql);
	if($res!==FALSE) {
		while($row=$res->FetchRow())
		{
			$result = $order_obj->getOrderItemsFullInfo($row["refno"],'');
			if($result!==FALSE) {
				$objResponse->call("createTableHeader", "mg_requests", "mg-list".$row["refno"], $row["refno"]);
				$req_flag=false;
				while($row2=$result->FetchRow())
				{
					switch(strtolower($row2["request_flag"]))
					{
						 case 'cmap':
								$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
								$req_flag=true;
								break;
						 case 'lingap':
								$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
								$req_flag=true;
								break;
						 case 'paid':
								$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
								$req_flag=true;
								break;
						 case 'charity':
								$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
								$req_flag=true;
								break;
						 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
					}

					$data = array(
						'refno'=>$row["refno"],
						'order_date'=>date('m-M-Y h:i: a',strtotime($row["orderdate"])),
						'status'=>$request_flag,
						'is_served'=>$row2["serve_status"],
						'item_name'=>$row2["artikelname"],
						'item_code'=>$row2["bestellnum"],
						'item_qty'=>$row2["quantity"],
						'item_prc'=>$row2["force_price"],
						'total_prc'=>parseFloatEx($row2["quantity"]*$row2["force_price"])
					);
					$objResponse->call("printRequestlist", "mg_requests", "mg-list".$row["refno"], $data);
					//$objResponse->alert("req_flag=".$req_flag);
				}
				//$objResponse->alert("final req_flag=".$req_flag);
				if($req_flag==true)
				{
					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
				}else {
					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				}
				$objResponse->assign("btn-".$row["refno"],"innerHTML", $buttons);
			}
		}
	}
	return $objResponse;
}

function populateSpLabRequests($encounter_nr, $pid)
{
	global $db;
	$objResponse = new xajaxResponse();
	$lab_obj = new SegLab();

	//get lab refno
	$sql = "SELECT refno FROM seg_lab_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND (ref_source='SPL') AND status <> 'deleted'".
				" ORDER BY serv_dt, serv_tm DESC ";
	$result = $db->Execute($sql);
	//$objResponse->alert("refno\n".$sql);
	if($result!==FALSE){
		while($ref = $result->FetchRow())
		{
			 $sql2 = "SELECT CONCAT(serv_dt,' ',serv_tm) AS serv_dt, encounter_nr, s.name AS request_item,\n".
						" s.service_code, d.price_cash, d.price_charge, d.quantity, r.ref_source, \n".
						" d.request_flag, d.is_served \n".
						" FROM seg_lab_serv AS r \n".
						" INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno \n".
						" INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
						" AND r.refno=".$db->qstr($ref["refno"])." AND d.status <> 'deleted' ORDER BY s.name ASC ";
			 $res = $db->Execute($sql2);
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "splab_requests", "splab-list".$ref["refno"], $ref["refno"]);
					$req_flag=false;
					while($row=$res->FetchRow())
					{
						switch(strtolower($row["request_flag"]))
						{
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
							 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('m-M-Y h:i: a',strtotime($row["serv_dt"])),
							'status'=>$request_flag,
							'is_served'=>$row["is_served"],
							'item_name'=>$row["request_item"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
						);
						$objResponse->call("printRequestlist", "splab_requests", "splab-list".$ref["refno"], $data);
					}

				 if($req_flag==true)
				 {
					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
				 }else {
					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'splab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'splab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				 }
				 $objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }
		}
	}
	return $objResponse;
}

function populateLabRequests($encounter_nr,$pid)
{
	global $db;
	$objResponse = new xajaxResponse();
	$lab_obj = new SegLab();

	//get lab refno
	$sql = "SELECT refno FROM seg_lab_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND (ref_source='LB' OR ISNULL(ref_source)) AND status <> 'deleted'".
				" ORDER BY serv_dt, serv_tm DESC ";
	$result = $db->Execute($sql);
	//$objResponse->alert("refno\n".$sql);
	if($result!==FALSE){
		while($ref = $result->FetchRow())
		{
			 $sql2 = "SELECT CONCAT(serv_dt,' ',serv_tm) AS serv_dt, encounter_nr, s.name AS request_item,\n".
						" s.service_code, d.price_cash, d.price_charge, d.quantity, r.ref_source, \n".
						" d.request_flag, d.is_served \n".
						" FROM seg_lab_serv AS r \n".
						" INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno \n".
						" INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
						" AND r.refno=".$db->qstr($ref["refno"])." AND d.status <> 'deleted' ORDER BY s.name ASC ";
			 $res = $db->Execute($sql2);
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "lab_requests", "lab-list".$ref["refno"], $ref["refno"]);
					$req_flag=false;
					while($row=$res->FetchRow())
					{
						switch(strtolower($row["request_flag"]))
						{
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
							 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('m-M-Y h:i: a',strtotime($row["serv_dt"])),
							'status'=>$request_flag,
							'is_served'=>$row["is_served"],
							'item_name'=>$row["request_item"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
						);
						$objResponse->call("printRequestlist", "lab_requests", "lab-list".$ref["refno"], $data);
					}

				 if($req_flag==true)
				 {
					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
				 }else {
					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				 }
				 $objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }
		}
	}
	return $objResponse;
}

function populateBloodRequests($encounter_nr,$pid)
{
	global $db;
	$objResponse = new xajaxResponse();
	$lab_obj = new SegLab();

	//get lab refno
	$sql = "SELECT refno FROM seg_lab_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND (ref_source='BB') AND status <> 'deleted'".
				" ORDER BY serv_dt, serv_tm DESC ";
	$result = $db->Execute($sql);
	//$objResponse->alert("refno\n".$sql);
	if($result!==FALSE){
		while($ref = $result->FetchRow())
		{
			 $sql2 = "SELECT CONCAT(serv_dt,' ',serv_tm) AS serv_dt, encounter_nr, s.name AS request_item,\n".
						" s.service_code, d.price_cash, d.price_charge, d.quantity, r.ref_source, \n".
						" d.request_flag, d.is_served \n".
						" FROM seg_lab_serv AS r \n".
						" INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno \n".
						" INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
						" AND r.refno=".$db->qstr($ref["refno"])." AND d.status <> 'deleted' ORDER BY s.name ASC ";
			 $res = $db->Execute($sql2);
			// $objResponse->alert("refno\n".$sql2);
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "blood_requests", "blood-list".$ref["refno"], $ref["refno"]);
					$req_flag=false;
					while($row=$res->FetchRow())
					{
						switch(strtolower($row["request_flag"]))
						{
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
							 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('m-M-Y h:i: a',strtotime($row["serv_dt"])),
							'status'=>$request_flag,
							'is_served'=>$row["is_served"],
							'item_name'=>$row["request_item"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
						);
						$objResponse->call("printRequestlist", "blood_requests", "blood-list".$ref["refno"], $data);
					}

					if($req_flag==true)
					{
						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="return false;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
											'<button class="segButton" style="cursor: pointer;" onclick="return false;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
					}else {
						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'blood_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
											'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'blood_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
					}
					$objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }
		}
	}
	return $objResponse;
}

function populateRadioRequests($encounter_nr, $pid)
{
	global $db;
	$objResponse = new xajaxResponse();

	//get radio refno
	$sql = "SELECT refno FROM seg_radio_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND status <> 'deleted' ".
				" ORDER BY request_date, request_time DESC ";
	$result = $db->Execute($sql);
	//$objResponse->alert("refno\n".$sql);
	if($result!==FALSE){
		while($ref = $result->FetchRow())
		{
			 $sql2 = "SELECT CONCAT(r.request_date,' ',r.request_time) as `orderdate`, rd.service_code, s.name,\n".
						" rd.price_cash, rd.price_charge, 1 as `quantity`, rd.request_flag, \n".
						" EXISTS(SELECT f.batch_nr FROM care_test_findings_radio AS f WHERE f.batch_nr=rd.batch_nr) as `has_result`\n".
						" FROM seg_radio_serv AS r \n".
						" INNER JOIN care_test_request_radio AS rd ON r.refno=rd.refno \n".
						" INNER JOIN seg_radio_services AS s ON s.service_code=rd.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
						" AND r.refno=".$db->qstr($ref["refno"])." AND rd.status <> 'deleted' ORDER BY s.name ASC ";
			 $res = $db->Execute($sql2);
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "radio_requests", "radio-list".$ref["refno"], $ref["refno"]);
					$req_flag=false;
					while($row=$res->FetchRow())
					{
						switch(strtolower($row["request_flag"]))
						{
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
							 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('m-M-Y h:i: a',strtotime($row["orderdate"])),
							'status'=>$request_flag,
							'is_served'=>$row["has_result"],
							'item_name'=>$row["name"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
						);
						$objResponse->call("printRequestlist", "radio_requests", "radio-list".$ref["refno"], $data);
					}

					if($req_flag==true)
					{
						$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
											'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
					}else {
						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'radio_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
											'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'radio_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
					}
					$objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }
		}
	}
	return $objResponse;
}

function deleteRequest($refno)
{
	global $db;
	$srv=new SegLab;
	$objResponse = new xajaxResponse();

	$sql = "SELECT ref_no FROM seg_pay_request
				WHERE ref_source = 'LD' AND ref_no = '$refno'
				UNION
				SELECT refno FROM seg_lab_result
				WHERE refno = '$refno'";

	 $res=$db->Execute($sql);
	 $row=$res->RecordCount();

	if ($row==0){

		$status=$srv->deleteRequestor($refno);

		if ($status) {
			$srv->deleteLabServ_details($refno);
			$objResponse->alert("The request is successfully deleted.");
		}else
			$objResponse->call("showme", $srv->sql);
	 }else{
			$objResponse->alert("The request cannot be deleted. It is already or partially paid or it has a result already.");
	 }
	$objResponse->call("refreshPage");
	return $objResponse;
}

function deleteRadioServiceRequest($ref_nr)
{
	$objResponse = new xajaxResponse();
	$radio_obj = new SegRadio;

	if ($radio_obj->deleteRefNo($ref_nr)){
		$objResponse->alert("The request is successfully deleted.");
	}else{
		$objResponse->alert("The request cannot be deleted. It is already or partially paid or it has a result already.");
	}
	$objResponse->call("refreshPage");
	return $objResponse;
}

function deleteOrder($refno)
{
	global $db;
	$objResponse = new xajaxResponse();
	$oclass = new SegOrder();
	if ($oclass->deleteOrder($refno)) {
		$objResponse->alert("The request is successfully deleted.");
	}
	else {
		$objResponse->alert("The request cannot be deleted. It is already or partially paid or it has a result already.");
	}
	$objResponse->call("refreshPage");
	return $objResponse;
}

function deleteMiscRequest($refno)
{
	global $db;
	$objResponse = new xajaxResponse();
	$misc_obj = new SegOR_MiscCharges();
	if($saveok=$misc_obj->deleteMiscOrder($refno))
	{
		$objResponse->alert("Miscellenous order successfully deleted.");
	}else {
		$objResponse->alert("Miscellenous order not successfully deleted.");
	}
	$objResponse->call("refreshPage");
	return $objResponse;
}

function changeTransactionStatus($refno, $status, $reason="", $enc_nr)
{
	global $db;
	$objResponse = new xajaxResponse();
	$dialysis_obj = new SegDialysis();
	$update = $dialysis_obj->updateTransactionStatus($refno, $status, $reason, $enc_nr);
	if($update!==FALSE)
	{
		$objResponse->call("refreshHistory");
	}else {
		$objResponse->alert("Error:".$dialysis_obj->getErrorMsg()."\nSQL:".$dialysis_obj->sql);
	}
	return $objResponse;
}

function deleteDialysisRequest($enc_nr, $pid, $refno)
{
	global $db;
	$objResponse = new xajaxResponse();
	$dialysis_obj = new SegDialysis();
	$exists = $dialysis_obj->requestChecker($enc_nr);
	if($exists!==FALSE) {
		$objResponse->alert("Reference #".$refno." has charges already. Please ask the cost center in-charge officer to delete the request.");
	} else {
		//$objResponse->alert("exists?".$exists);
		//delete logically to dialysis transaction
		$delete = $dialysis_obj->deleteTransactionDetails($refno, $enc_nr);
		if($delete!==FALSE) {
			$objResponse->alert("Request deleted successfully.");
			$objResponse->call("refreshPage");
		}else {
			$objResponse->alert("Error:".$dialysis_obj->getErrorMsg()."\nSQL:".$dialysis_obj->sql);
		}
	}
	return $objResponse;
}

function deleteBill($bill_nr)
{
	global $db;
	$objResponse = new xajaxResponse();
	$objbill = new BillInfo();
	if ($objbill->deleteBillInfo($bill_nr)) {
			$objResponse->alert('Billing successfully deleted!');
	}
	else {
			$objResponse->alert('Error in billing deletion: '.$db->ErrorMsg());
	}
	$objResponse->call("refreshPage");
	return $objResponse;
}
$xajax->processRequest();
?>
