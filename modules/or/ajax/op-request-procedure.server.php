<?php
		function populateProcedureList($sElem,$searchkey,$page,$include_firstname,$include_encounter=TRUE) {
				global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

				$objResponse = new xajaxResponse();

				//$person=& new Person();
				$person= new SegOps();
				$dept_obj = new Department;
				$ward_obj = new Ward;

				#added by VAN 06-25-08
				$objSS = new SocialService;

				$offset = $page * $maxRows;
				#$objResponse->addAlert($include_encounter);
				/*if ($include_encounter) {
						#$total = $person->countSearchSelectWithCurrentEncounter($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
						# $objResponse->addScript("document.write('".addslashes($person->sql)."')");
						# return $objResponse;
						#$objResponse->addAlert("total = ".$total);

						#commented by VAN 10-17-08
						$ergebnis=$person->SearchSelectWithCurrentEncounter($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
						#$ergebnis=$person->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
						#$objResponse->addAlert($person->sql);
						$total = $person->FoundRows();
						$lastPage = floor($total/$maxRows);
						if ($page > $lastPage) $page=$lastPage;
				}*/
				/*else {*/



						//$ergebnis=$person->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);

						$ergebnis=$person->SearchProcedures($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);

						#$objResponse->addAlert($person->sql);
						$total = $person->FoundRows();
						$lastPage = floor($total/$maxRows);
						if ($page > $lastPage) $page=$lastPage;
						#$objResponse->addScriptCall("display",$person->sql);
						#$objResponse->addAlert($_SESSION['DEBUG']);
						#return $objResponse;
			 /* }*/
				$rows=0;

				$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->addScriptCall("clearList","person-list");
				$details = (object) 'details';
				if ($ergebnis) {
						$rows=$ergebnis->RecordCount();
						while($result=$ergebnis->FetchRow()) {
/*---replaced by pet (may 28, 2008) to avoid hanging commas & in preparation of not requiring brgy_name---
								$addr = $result["street_name"];
								if ($result["brgy_name"])
										$addr.=", ".$result["brgy_name"];
								if ($result["mun_name"])
										$addr.=", ".$result["mun_name"];
								if ($result["prov_name"])
										$addr.=", ".$result["prov_name"];
								if ($result["zipcode"])
										$addr.=" ".$result["zipcode"];
#------------with------------------------------------------------------------------------------------------*/

# edited by alvin 05/28/08 :)
		$addr = implode(", ",array_filter(array($result['street_name'], $result["brgy_name"], $result["mun_name"])));
		if ($result["zipcode"])
				$addr.=" ".$result["zipcode"];
		if ($result["prov_name"])
				$addr.=" ".$result["prov_name"];

#------------until here only-------------------------------------------------------------fgdp----------------

#$objResponse->addAlert("populatePersonList :: person->sql = '".$person->sql."'");
#$objResponse->addAlert("populatePersonList :: addr = '".$addr."'");

#$objResponse->addAlert("populatePersonList :: sc = '".$result['senior_ID']."'");

								#added by VAN 06-25-08
								#if senior citizen, he will classified as D automatically
								/*
								if (trim($result['senior_ID'])){
										#$discountid = "C3";
										$discountid = "D";
										$socialInfo = $objSS->getSSClassInfo($discountid);
										$discount = $socialInfo['discount'];
										$senior_citizen = 1;
								#added by VAN 07-02-08
								#}elseif ($result["encounter_type"]!=2){
								}else{
								*/
										if (($result["parent_mss_id"])&&($result["parent_mss_id"]=='D')){
												$discountid = $result["parent_mss_id"];
												if ($result["discountid"]=='SC')
														$senior_citizen = 1;
										}else{
												$discountid = $result["discountid"];
												$senior_citizen = 0;
										}

										#$discountid = $result["discountid"];
										$discount = $result["discount"];
										#$senior_citizen = 0;
								/*
								}else{
										#$discountid = $result["discountid"];
										#$discount = $result["discount"];
										$discountid = "";
										$discount = "";
										$senior_citizen = 0;
								*/
						#    }
								#-------------------

								#$dob = $result["date_birth"];
								#if (!$dob || $dob=="0000-00-00") $dob="Unknown";
								if (($result["date_birth"])&&($result["date_birth"]!='00/00/0000')){
										$dob = date("Y-m-d",strtotime($result["date_birth"]));
								}else
										$dob = 'unknown';

								#Added by Cherry 02-24-10
								$lastId = $result["package_id"];
								$details->id = $result["package_id"];
								$details->name = $result["package_name"];

								#Commented by Cherry 02-24-10
								/*$lastId = $result["pid"];
								$details->id = $result["pid"];
								$details->lname = $result["name_last"];
								$details->fname = $result["name_first"];

								$details->mname = $result["name_middle"];

								$details->dob = $dob;
								$details->sex = $result["sex"];
								$details->addr = $addr;
								$details->zip = $result["zipcode"];
								$details->status = $result["status"];
								$details->nr = $result["encounter_nr"];
								$details->type = $result["encounter_type"];
								#$details->discountid = $result["discountid"];
								#$details->discount = $result["discount"];

								#added by VAN 06-25-08
								$details->senior_citizen = $senior_citizen;

								#edited by VAN 06-25-08
								$details->discountid = $discountid;
								$details->discount = $discount;

								$details->orig_discountid = $result["discountid"];
								#$objResponse->addAlert($result["discountid"]);
								$details->rid = $result['rid'];

								$details->admission_dt = '';
								$details->discharge_date = '';*/

								#added by VAN 06-02-08
								/*if ($result["encounter_type"]==1){
										$details->enctype = "ER PATIENT";
										$details->location = "EMERGENCY ROOM";
								}elseif ($result["encounter_type"]==2){
										$details->enctype = "OUTPATIENT";
										$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
										$details->location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
								}elseif (($result["encounter_type"]==3)||($result["encounter_type"]==4)){
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
										#$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
										#$details->location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
										$details->location = '';
								}*/

								#$details->is_medico = $result['is_medico'];

								#$objResponse->addAlert(print_r($details,true));
								#$objResponse->addAlert($enctype);

								$objResponse->addScriptCall("addPerson","person-list", $details);
/*

								$objResponse->addScriptCall("addPerson","person-list",
										$result["pid"],$result["name_last"],$result["name_first"],$dob,
										$result["sex"],$addr,$result["zipcode"],$result["status"],$result["encounter_nr"],
										$result["encounter_type"],$result["discountid"],$result["discount"],$result['rid']);
*/
						}
				}
				else {
						$details->error = nl2br(htmlentities($person->sql));
				}
				if (!$rows) $objResponse->addScriptCall("addPerson","person-list",$details);

				#$objResponse->alert($lastId);
				if ($rows==1 && $lastId) {
						$objResponse->addScriptCall("prepareSelect",$lastId);
				}

				if ($sElem) {
						$objResponse->addScriptCall("endAJAXSearch",$sElem);
				}
				return $objResponse;
		}

		require('./roots.php');
		require_once($root_path.'include/inc_environment_global.php');
		require_once($root_path.'classes/adodb/adodb-lib.inc.php');
		require_once($root_path.'include/care_api_classes/class_globalconfig.php');
		require_once($root_path.'include/care_api_classes/class_person.php');
		require_once($root_path."modules/or/ajax/op-request-procedure.common.php");
		#require_once($root_path."modules/registration_admission/ajax/order-psearch.common.php");

		#added by VAN 06-02-08
		require_once($root_path.'include/care_api_classes/class_department.php');
		require_once($root_path.'include/care_api_classes/class_ward.php');

		#added by VAN 06-25-08
		require_once($root_path.'include/care_api_classes/class_social_service.php');

		#added by Cherry 02-23-10
		require_once($root_path.'include/care_api_classes/billing/class_ops.php');

		$xajax->processRequests();
?>