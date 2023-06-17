<?php

		require('./roots.php');
		require($root_path.'include/inc_environment_global.php');

		require_once($root_path.'include/care_api_classes/class_insurance.php');
		require_once($root_path.'include/care_api_classes/class_ward.php');
		require_once($root_path.'include/care_api_classes/class_paginator.php');
		require_once($root_path.'include/care_api_classes/class_globalconfig.php');

		require_once($root_path.'modules/insurance_co/ajax/hcplan-admin.common.php');

		define('MAX_BLOCK_ROWS', 30);

		#-------------------added by VAN -----------------------------------
		function populateInsuranceList($sElem,$searchkey,$page) {
				global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

				$objResponse = new xajaxResponse();
				$ins_obj=new Insurance;
				$offset = $page * $maxRows;
				#$objResponse->addAlert("searchkey = ".$searchkey);
				if ($searchkey==NULL)
						$searchkey = '*';
				$total_srv = $ins_obj->countSearchSelect($searchkey,$maxRows,$offset,"name","ASC");
				#$objResponse->addAlert("sql c1 = ".$ins_obj->sql);
				$total = $ins_obj->count;
				#$objResponse->addAlert("total = ".$total);
				$lastPage = floor($total/$maxRows);

				#added by VAN 03-10-08
				if ((floor($total%10))==0)
						$lastPage = $lastPage-1;

				if ($page > $lastPage) $page=$lastPage;
				$ergebnis=$ins_obj->SearchSelect($searchkey,$maxRows,$offset,"name","ASC");
				#$objResponse->addAlert("sql c2 = ".$ins_obj->sql);
				$rows=0;

				$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->addScriptCall("clearList","hcplanlistTable");
				if ($ergebnis) {
						$rows=$ergebnis->RecordCount();
						while($result=$ergebnis->FetchRow()) {
								#$objResponse->addAlert("firm id = ".$result['firm_id']);
								$firmId = "<a href=\"insurance_co_info.php".URL_APPEND."&retpath=list&firm_id=".$result['firm_id']."\">".$result['firm_id']."</a>";
								 $firmName = "<a href=\"insurance_co_info.php".URL_APPEND."&retpath=list&firm_id=".$result['firm_id']."\">".$result['name']."</a>";

							 $objResponse->addScriptCall("addInsurance","hcplanlistTable",$result["hcare_id"],$firmId,$firmName,$result["phone_main"],$result["fax_main"],$result["addr_email"]);
								#$objResponse->addScriptCall("addInsurance","hcplanlistTable",$result["hcare_id"],$result['firm_id'],$result['name'],$result["phone_main"],$result["fax_main"],$result["addr_email"]);
						}
				}
				if (!$rows) $objResponse->addScriptCall("addInsurance","hcplanlistTable",NULL);
				if ($sElem) {
						$objResponse->addScriptCall("endAJAXSearch",$sElem);
				}

				return $objResponse;
		}

		function deleteInsurance($hcare_id){
				global $db;
				$ins_obj=new Insurance;
				$objResponse = new xajaxResponse();

				$sql = "SELECT * FROM care_person_insurance WHERE hcare_id='".$hcare_id."'";
				#$objResponse->addAlert("sql = ".$sql);
				$res=$db->Execute($sql);
				$row=$res->RecordCount();

				if ($row==0){
						$status=$ins_obj->deleteInsuranceComp($hcare_id);

						if ($status) {
								$objResponse->addScriptCall("removeInsurance",$hcare_id);
								$objResponse->addAlert("The insurance firm company is successfully deleted.");
						}else{
								$objResponse->addScriptCall("showme", $ins_obj->sql);
						}
				 }else{
								 $objResponse->addAlert("The insurance firm company cannot be deleted. It is already been used.");
				 }
				return $objResponse;
		}

		#function populateConfinementBenefit($hcare_id, $benefit_id, $conftype){
		function populateConfinementBenefit($bskedID, $conftype){
				global $db;
				$ins_obj=new Insurance;
				$objResponse = new xajaxResponse();

				#$query=$ins_obj->getConfinementBenefit($hcare_id, $benefit_id, $conftype);
				$query=$ins_obj->getConfinementBenefit($bskedID, $conftype);
				if ($query) {
						$rows=$query->RecordCount();
						while($result=$query->FetchRow()) {
								$objResponse->addScriptCall("ajxSetConfinement",$result["confinetype_id"],$result["rateperday"],$result["amountlimit"],$result["dayslimit"],$result["rateperRVU"],$result["limit_rvubased"],$result["year_dayslimit"],$result["year_dayslimit_alldeps"]);
						}
				}

				return $objResponse;
		}

		#function populateRoomTypeBenefit($hcare_id, $benefit_id, $roomtype){
		function populateRoomTypeBenefit($bskedID, $roomtype){
				global $db;
				$ins_obj=new Insurance;
				$objResponse = new xajaxResponse();

				#$query=$ins_obj->getRoomTypeBenefit($hcare_id, $benefit_id, $roomtype);
				$query=$ins_obj->getRoomTypeBenefit($bskedID, $roomtype);
				#$objResponse->addAlert("sql = ".$ins_obj->sql);
				if ($query) {
						$rows=$query->RecordCount();
						while($result=$query->FetchRow()) {
								#$objResponse->addAlert("roomtype = ".$result["roomtype_nr"]);
								$objResponse->addScriptCall("ajxSetRoomType",$result["roomtype_nr"],$result["rateperday"],$result["amountlimit"],$result["dayslimit"],$result["rateperRVU"],$result["year_dayslimit"],$result["year_dayslimit_alldeps"]);
						}
				}

				return $objResponse;
		}

		// Modified by LST --- 03.23.2009 --------------------------------
		function populateRVUBenefit($bskedID){
				global $db;
				$ins_obj=new Insurance;
				$objResponse = new xajaxResponse();

				$query=$ins_obj->getRVUBenefit($bskedID);
				#$objResponse->addAlert("sql = ".$ins_obj->sql);
				$objResponse->addScriptCall("UnSetRVURange");
				if ($query) {
						$rows=$query->RecordCount();
						while($result=$query->FetchRow()) {
								$details->rangestart = $result["range_start"];
								$details->rangeend   = $result["range_end"];
								$details->fixedamnt  = $result["fixedamount"];
								$details->minamnt    = $result["minamount"];
								$details->amntlimit  = $result["amountlimit"];
								$details->rateperrvu = $result["rateperRVU"];
								$details->sfpercent  = $result["percentofSF"];

								$objResponse->addScriptCall("addRVURange", NULL, $details);
						}
				}

				return $objResponse;
		}

		#function populateItemBenefit($hcare_id, $benefit_id){
		function populateItemBenefit($bskedID, $benefit_id){
				global $db;
				$ins_obj=new Insurance;

				$dbtable='care_pharma_products_main';
				$labtable = 'seg_lab_services';
				$radiotable = 'seg_radio_services';
				#$ORtable = 'care_ops301_en';
				$ORtable = 'seg_ops_rvs';
				$othertable = 'seg_otherhosp_services';
		$others = 'seg_other_services';

				$objResponse = new xajaxResponse();

				$rsbillarea = $ins_obj->getBenefitInfo($benefit_id);

				if ($rsbillarea['bill_area'] == "MS")
						$item_type = "MS";
		elseif (($rsbillarea['bill_area'] == "HS")||($rsbillarea['bill_area'] == "OR")||($rsbillarea['bill_area'] == "XC"))
						$item_type = "HS";

				#$query=$ins_obj->getProductBenefit($hcare_id, $benefit_id,$item_type);
				$query=$ins_obj->getProductBenefit($bskedID, $item_type);
				#$objResponse->addAlert('sql = '.$ins_obj->sql);
				if ($query) {
						#$objResponse->addScriptCall("clearOrder","product-list");
						$rows=$query->RecordCount();
						if ($item_type=="MS"){
								while($result=$query->FetchRow()) {
										$sql = "SELECT p.bestellnum AS code, p.artikelname AS name, p.generic, p.description
														FROM $dbtable AS p
											 WHERE p.bestellnum='".$result['code']."'";
										$res=$db->Execute($sql);
										$row=$res->RecordCount();
										if ($row!=0){
												$rsProduct=$res->FetchRow();
										}

										$objResponse->addScriptCall("ajxSetMedItem","product-list",trim($result['code']),trim($rsProduct['name']),trim($result['amountlimit']),'DM');
								}
						}elseif ($item_type=="HS"){
								while($result=$query->FetchRow()) {
										if ($result['provider']=='LB'){
												$sql = "SELECT * FROM $labtable
																	WHERE service_code ='".$result['code']."'";
												$res=$db->Execute($sql);
												$row=$res->RecordCount();
												if ($row!=0){
														$rsProduct=$res->FetchRow();
												}
										}elseif ($result['provider']=='RD'){
												$sql = "SELECT * FROM $radiotable
																	WHERE service_code ='".$result['code']."'";
												$res=$db->Execute($sql);
												$row=$res->RecordCount();
												if ($row!=0){
														$rsProduct=$res->FetchRow();
												}
										}elseif ($result['provider']=='OR'){

												$sql = "SELECT description AS name FROM $ORtable
																	WHERE code ='".$result['code']."'";
												$res=$db->Execute($sql);
												$row=$res->RecordCount();
												if ($row!=0){
														$rsProduct=$res->FetchRow();
												}

										}elseif ($result['provider']=='OA'){
						$sql = "SELECT * FROM $others
									WHERE service_code ='".trim($result['code'])."'";
						$res=$db->Execute($sql);
						$row=$res->RecordCount();
						if ($row!=0){
							$rsProduct=$res->FetchRow();
						}
					}elseif ($result['provider']=='XC'){
												$sql = "SELECT * FROM $othertable
									WHERE service_code ='".trim($result['code'])."'";
												$res=$db->Execute($sql);
												$row=$res->RecordCount();
												if ($row!=0){
														$rsProduct=$res->FetchRow();
												}
										}

										$objResponse->addScriptCall("ajxSetServiceItem","product-list",trim($result['code']),trim($rsProduct['name']),trim($result['provider']),trim($result['amountlimit']),trim($result['maxRVU']));
								}
						}
				}

				return $objResponse;
		}

		function getBenefitSked($hcare_id, $benefit_id, $effective_date, $role_level = 0){
				global $db;
				$ins_obj=new Insurance;
				$objResponse = new xajaxResponse();

				$effective_date = date("Y-m-d",strtotime($effective_date));

				$query=$ins_obj->getBenefitSkedInfo($hcare_id, $benefit_id, $effective_date, $role_level);
				#$objResponse->addAlert("sql = ".$ins_obj->sql);

				if ($query) {
						$rows=$query->RecordCount();
						while($result=$query->FetchRow()) {
								$objResponse->addScriptCall("ajxSetBenefitBasis",$result["basis"],$result["bsked_id"]);
						}
				}

				return $objResponse;
		}


		function getAllEffDateofBenSked($hcare_id, $benefit_id, $role_level = 0) {
				global $db;
				$ins_obj=new Insurance;
				$objResponse = new xajaxResponse();

				$rs=$ins_obj->getBenefitSked($hcare_id, $benefit_id, $role_level);
//        $objResponse->addAlert("sql = ".$ins_obj->sql);

				if ($rs) {
						$objResponse->addScriptCall("ajxClearOptions");
						if ($ins_obj->count > 0){
										$objResponse->addScriptCall("ajxAddOption","Select Effective Date",0);
						}else{
								$objResponse->addScriptCall("ajxAddOption","No Effective Date",0);
						}

						while ($result=$rs->FetchRow()) {
//                $objResponse->addAlert("Effectivity date is ".$result["effectvty_dte"]);
								if (strcmp($result["effectvty_dte"], "0000-00-00") == 0)
										$date_eff = "00/00/0000";
								else
										$date_eff = date("m/d/Y",strtotime($result["effectvty_dte"]));
								$objResponse->addScriptCall("ajxAddOption",$date_eff,$date_eff);
						}

						#$objResponse->addScriptCall("ajxSetBSkedID",$result["bsked_id "]);

				}
				else {
						#$objResponse->addAlert("getServiceGroup : Error retrieving lab service groups information...");
						$objResponse->addScriptCall("ajxClearOptions");
						$objResponse->addScriptCall("ajxAddOption","No Effective Date",0);
				}
				return $objResponse;

		}

		function getBenefitArea($benefit_id){
				global $db;
				$ins_obj=new Insurance;
				$objResponse = new xajaxResponse();

				$rsbillarea = $ins_obj->getBenefitInfo($benefit_id);
				#$objResponse->addAlert($ins_obj->sql);
				#$objResponse->addAlert("getBenefitArea = ".$rsbillarea["bill_area"]);
				$objResponse->addScriptCall("ajxSetBenefitArea", $rsbillarea["bill_area"]);
				/*
				if ($rsbillarea["bill_area"] == 'D4')
						$objResponse->addScriptCall("ajxSetLabel_Anes");
				else
						$objResponse->addScriptCall("ajxSetLabel_Orig");
				*/
				return $objResponse;
		}

		function deleteBenefitItem($benefit_id, $benefit_name){
				global $db;
				$ins_obj=new Insurance;
				$objResponse = new xajaxResponse();

				$sql = "SELECT * FROM seg_hcare_bsked WHERE benefit_id='".$benefit_id."'";
				$res=$db->Execute($sql);
				$row=$res->RecordCount();

				if ($row==0){
						$status=$ins_obj->deleteBenefitItem($benefit_id);

						if ($status) {
								$objResponse->addScriptCall("removeBenefit",$benefit_id);
								$objResponse->addAlert("The benefit item ".strtoupper($benefit_name)." is successfully deleted.");
						}else{
								$objResponse->addScriptCall("showme", $ins_obj->sql);
						}
				 }else{
								 $objResponse->addAlert("The benefit item ".strtoupper($benefit_name)." cannot be deleted. It is already been used.");
				 }
				return $objResponse;
		}

		function deleteConfinementItem($confinetype_id, $confinetypedesc){
				global $db;
		$ins_obj = new Insurance;
				$objResponse = new xajaxResponse();

//        $sql = "SELECT * FROM seg_hcare_confinetype WHERE confinetype_id='".$confinetype_id."'";
//        $res=$db->Execute($sql);
//        $row=$res->RecordCount();
//
//        if ($row==0){

						$status=$ins_obj->deleteConfinementItem($confinetype_id);

						if ($status) {
								$objResponse->addScriptCall("removeConfinement",$confinetype_id);
			$objResponse->addAlert("The confinement type item ".strtoupper($confinetypedesc)." is now inactive!");
						}else{
								$objResponse->addScriptCall("showme", $ins_obj->sql);
						}

//         }else{
//                 $objResponse->addAlert("The confinement type item ".strtoupper($confinetypedesc)." cannot be deleted. It is already been used.");
//         }

				return $objResponse;
		}

		function deleteOtherHospServItem($service_code, $name){
				global $db;
				$ins_obj=new Insurance;
				$objResponse = new xajaxResponse();

				$sql = "SELECT * FROM seg_hcare_srvops WHERE code = '".$service_code."'";
				$res=$db->Execute($sql);
				$row=$res->RecordCount();

				if ($row==0){
						$status=$ins_obj->deleteOtherHospServItem($service_code);

						if ($status) {
								$objResponse->addScriptCall("removeOtherHospServ",$service_code);
								$objResponse->addAlert("The other hospital service item ".strtoupper($name)." is successfully deleted.");
						}else{
								$objResponse->addScriptCall("showme", $ins_obj->sql);
						}
				 }else{
								 $objResponse->addAlert("The other hospital service type item ".strtoupper($name)." cannot be deleted. It is already been used.");
				 }
				return $objResponse;
		}

		#--------------------------------------

		function getHealthInsurances($sFilter = '') {
				global $db;

				$sql = "select * from seg_hcares where hcare_desc like '$sFilter%' order by hcare_desc";
				$rs  = $db->Execute($sql);

				$rowcount = $rs->RecordCount();

				$objResponse = new xajaxResponse();
				$objResponse->addScriptCall("insurance_clearHealthPlans");

				while ($row = $rs->FetchRow()) {
						$objResponse->addScriptCall("addHealthPlan", $row["hcare_id"], $row["hcare_desc"], $row["hcare_company"], $row["hcare_contact_person"], $row["hcare_addr1"], $row["hcare_addr2"], $row["hcare_contact_no"]);

				}
				if (!rowcount) $objResponse->addScripCall("addHealthPlan", NULL);

				return $objResponse;
		}

		function getCarePersons($nStart, $nLimit) {
				global $db;

				$sql = "select pid, name_first, name_middle, name_last from care_person " .
							 "where name_last <> '' order by name_last, name_first, name_middle " .
							 "limit " . $nStart . ", " . $nLimit;
				$rs  = $db->Execute($sql);

				$rowcount = $rs->RecordCount();

				$objResponse = new xajaxResponse();
				$objResponse->addScriptCall("doClearCarePersons");

				while ($row = $rs->FetchRow()) {
						$objResponse->addScriptCall("addCarePerson", $row["pid"], $row["name_first"], $row["name_middle"], $row["name_last"]);
				}

				if (!rowcount) $objResponse->addScripCall("addCarePerson", NULL);

				return $objResponse;
		}

		function getResultsetMaxRows() {
				global $db;

				$sql = "select pid, name_first, name_middle, name_last from care_person
								where name_last <> '' order by name_last, name_first, name_middle";
				$rs  = $db->Execute($sql);
				$rowcount = $rs->RecordCount();

				$objResponse = new xajaxResponse();
				$objResponse->addScriptCall("getMaxRows", $rowcount);

				return $objResponse;
		}


// Added by Mark on Sep 4,2007
		function PopulateHealthPlanList($tableId, $searchkey, $pgx, $thisfile, $rpath,$oitem, $odir){
				$objResponse = new xajaxResponse();
				//Display table header
				HealthPlanListHeader($objResponse, $tableId, $oitem, $odir);
				//Panigate list
				PaginateHealthPlanList($objResponse,$searchkey, $pgx, $thisfile, $rpath,$oitem, $odir);

				return $objResponse;
		}

		function HealthPlanListHeader(&$objResponse, $tableId, $oitem, $odir){
				$thead = "<thead><tr>";
				$thead .= makeSortLink('Firm ID','firm_id', $oitem, $odir,'15%', 'center');
				$thead .= makeSortLink('Insurance company name','name', $oitem, $odir,'30%', 'center');
				$thead .= "<th width=\"15%\">Phone No.</th>";
				$thead .= "<th width=\"15%\">Fax No.</th>";
				$thead .= "<th width=\"25%\">Email Address</th>";
				$thead .= "<th width=\"2\">Insurance Details</th>";   #added by VAN 09-18-07
				$thead .= "</tr></thead> \n";

				$tbody = "<tbody></tbody>";

				$html = $thead.$tbody;

				$objResponse->addAssign($tableId, "innerHTML",$html);

		} //end of function HealthPlanListHeader

		function makeSortLink($txt='SORT', $item, $oitem, $odir='ASC', $width, $align='center'){
				if($item == $oitem){
						if($odir == 'ASC'){
								$img = "<img src=\"../../gui/img/common/default/arrow_red_up_sm.gif\">";
						}else{
								$img = "<img src=\"../../gui/img/common/default/arrow_red_dwn_sm.gif\">";
						}
				}else{
						$img='&nbsp;';
				}

				if($odir == 'ASC') $dir = 'DESC';
				else $dir = 'ASC';

				$td = "<th width=\"".$width."\" align=\"".$align."\" onClick=\"jsSortHandler('$item', '$oitem','$dir');\">".$img."<b>".$txt."</b></th> ";
				//$td = "<th width=\"".$width."\" align=\"".$align."\">".$img."<b>".$txt."</b></th> ";

				return $td;
		} //end of function makeSortLink

		function PaginateHealthPlanList(&$objResponse, $searchkey, $pgx, $thisfile, $rpath , $oitem='create_dt', $odir='ASC'){
				global $date_format;
				$ins_obj=new Insurance;

				$pagen = new Paginator ($pgx, $thisfile, $searchkey, $rpath, $oitem, $odir);
				/*$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS);
				else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);*/

				$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('pagin_insurance_list_max_block_rows');
				if(empty($GLOBAL_CONFIG['pagin_insurance_list_max_block_rows'])) $GLOBAL_CONFIG['pagin_insurance_list_max_block_rows']=MAX_BLOCK_ROWS; # Last resort, use the default defined at the start of this page
				else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_insurance_list_max_block_rows']);

				if(($mode == 'search' || $mode == 'paginate') && !empty($searchkey)){
						$searchkey = strtr($searchkey, '*?', '%_');
				}
				//$objResponse->addAlert("pagen =".$pagen."\n " );

				$firms=$ins_obj->getLimitActiveFirmsInfo($GLOBAL_CONFIG['pagin_insurance_list_max_block_rows'],$pgx,$oitem,$odir);
				//$objResponse->addAlert("firms =".print_r($firms));
				//$objResponse->addAlert("firms =".$firms);

				$linecount=$ins_obj->LastRecordCount();
				$pagen->setTotalBlockCount($linecount);
				# Count total available data
				if(isset($totalcount)&&$totalcount){
						$pagen->setTotalDataCount($totalcount);
				}else{
						$totalcount=$ins_obj->countAllActiveFirms();
						$pagen->setTotalDataCount($totalcount);
				}

				$pagen->setSortItem($oitem);
				$pagen->setSortDirection($odir);


				// insert code here for number of item found

				$my_count=$pagen->BlockStartNr();
				if($firms){
						while($firm = $firms->FetchRow()){

								if($toggle) $bgc='#dddddd';
								else $bgc='#efefef';
								$toggle=!$toggle;

								$firmId = "<a href=\"insurance_co_info.php".URL_APPEND."&retpath=list&firm_id=".$firm['firm_id']."\">".$firm['firm_id']."</a>";
								 $firmName = "<a href=\"insurance_co_info.php".URL_APPEND."&retpath=list&firm_id=".$firm['firm_id']."\">".$firm['name']."</a>";

								//if($firm['addr_email']){
								//    $imgMail = "<img ".createComIcon($root_path,'email.gif','0')."><a href=\"mailto:".$firm['addr_email']."\">".$firm['addr_email']."</a>";
								//}
								//$objResponse->addAlert("firms =".$firmId."\n firmName=".$firmName);
								#$objResponse->addAlert("ajax : PaginateHealthPlanList = ".$pgx);
								$objResponse->addScriptCall("jsHealthCarePlanList",$firm['hcare_id'], $firmId, $firmName,$firm['phone_main'],$firm['fax_main'],$firm['addr_email']);
								#$objResponse->addAlert("5 ajax : PaginateHealthPlanList = ".$pgx);

						}//end while...loop
				}//end ..If

		}// end of function PaginateHealthPlanList

		#added by VAN 06-10-08
		function deleteRoomTypeItem($roomtype_nr, $roomtype_name){
				global $db;
				$ward_obj=new Ward;
				$objResponse = new xajaxResponse();

				$sql = "SELECT * FROM seg_hcare_roomtype WHERE roomtype_nr='".$roomtype_nr."'";
				$res=$db->Execute($sql);
				$row=$res->RecordCount();

				if ($row==0){
						$status=$ward_obj->deleteRoomTypeItem($roomtype_nr);

						if ($status) {
								$objResponse->addScriptCall("removeRoomType",$roomtype_nr);
								$objResponse->addAlert("The room type item ".strtoupper($roomtype_name)." is successfully deleted.");
						}else{
								$objResponse->addScriptCall("showme", $ward->sql);
						}
				 }else{
								 $objResponse->addAlert("The room type item ".strtoupper($roomtype_name)." cannot be deleted. It is already been used.");
				 }
				return $objResponse;
		}
		#--------------------

		#added by VAN 09-01-08
		function deleteEffectivityDateofBsked($hcare_id, $benefit_id, $effectivity_date){
				global $db;
				$objResponse = new xajaxResponse();
				$ins_obj=new Insurance;

				if ($effectivity_date!='00/00/0000')
						$effectivity_date = date("Y-m-d",strtotime($effectivity_date));
				else
						$effectivity_date = '0000-00-00';
				#$objResponse->addAlert("ajax hcare_id, ben, date = ".$hcare_id." , ".$benefit_id." , ".$effectivity_date);
				$res = $ins_obj->getBenefitSkedInfo($hcare_id, $benefit_id, $effectivity_date);
				#$objResponse->addAlert("sql = ".$ins_obj->sql);
				if ($res) $bskedinfo = $res->FetchRow();
				#$objResponse->addAlert("count = ".$ins_obj->count);
				if ($ins_obj->count){
						#$objResponse->addAlert("ajax bskedinfo = ".$bskedinfo['bsked_id']);
//            $sql = "SELECT bsked_id FROM seg_hcare_rvurange
//                            WHERE bsked_id='".$bskedinfo['bsked_id']."'
//                            AND range_start<>0 AND range_end<>0
//                        UNION
//                      SELECT bsked_id FROM seg_hcare_confinetype
//                              WHERE bsked_id='".$bskedinfo['bsked_id']."'
//                            AND confinetype_id<>0
//                        UNION
//                      SELECT bsked_id FROM seg_hcare_products
//                              WHERE bsked_id='".$bskedinfo['bsked_id']."'
//                        UNION
//                      SELECT bsked_id FROM seg_hcare_roomtype
//                              WHERE bsked_id='".$bskedinfo['bsked_id']."'
//                            AND roomtype_nr<>0
//                        UNION
//                      SELECT bsked_id FROM seg_hcare_srvops
//                              WHERE bsked_id='".$bskedinfo['bsked_id']."'";
//
//            $res=$db->Execute($sql);
//            $row=$res->RecordCount();
//
//            if ($row){
//                $objResponse->addAlert("The effectivity date is not successfully deleted. It is already been used.");
//            }else{
								if (@$ins_obj->deleteBenefitSked($bskedinfo['bsked_id'])){
										$objResponse->addAlert("The effectivity date is successfully deleted.");
										$objResponse->addScriptCall("refreshWindow");
								}else{
										$objResponse->addAlert($ins_obj->error_msg);
//                    $objResponse->addAlert("The effectivity date is not successfully deleted. There is an error.");
								}
//            }
				}else{
						$objResponse->addAlert("false");
				}
				return $objResponse;
		}

		#-----------------------

		function setOptionRoleLevel($role_level = 0) {
				global $db;
				$objResponse = new xajaxResponse();

				$sql = "SELECT * ".
								"\n  FROM seg_role_tier";

				if($result = $db->Execute($sql)){
						if($result->RecordCount()){
								$objResponse->addScriptCall("js_ClearOptions", "role_level");
								$objResponse->addScriptCall("js_AddOptions", "role_level", "-- Select Group Level --",0);
								while($row = $result->FetchRow()){
										$objResponse->addScriptCall("js_AddOptions", "role_level", $row['tier_desc'], $row['tier_nr'], $role_level);
								}
						}
				}else{
						$objResponse->addAlert("setOptionRoleLevel : Error retrieving levels of role ...");
				}

				return $objResponse;
		}

		function checkIfHasRowLevel($benefit_id) {
				global $db;

				$objResponse = new xajaxResponse();

				$bwithlevel = false;
				$strSQL = "select is_withlevel ".
									"\n   from seg_hcare_benefits ".
									"\n   where benefit_id = $benefit_id";
				if($result = $db->Execute($strSQL)){
						if($result->RecordCount()){
								if ($row = $result->FetchRow()) $bwithlevel = ($row["is_withlevel"] != 0);
						}
				}

				$objResponse->addScriptCall("showRoleLevelOption", (($bwithlevel) ? 1 : 0));

				return $objResponse;
		}

	function showAssocTabs($benefit_id) {
		global $db;

		$objResponse = new xajaxResponse();

		$b_overall = false;
		$strSQL = "select is_overall            \n
						from seg_hcare_benefits   \n
						where benefit_id = '$benefit_id'";
		if($result = $db->Execute($strSQL)){
			if($result->RecordCount()){
				if ($row = $result->FetchRow()) $b_overall = ($row["is_overall"] != 0);
			}
		}

		$objResponse->addScriptCall("toggleOverAllOption", (($b_overall) ? 1 : 0));

		return $objResponse;
	}

	function populatePkgsWithBenefit($bskedID) {
		global $db;

		$objResponse = new xajaxResponse();

		$rcount = 0;

		$strSQL = "select shp.package_id, package_name, amountlimit                   \n
						from seg_hcare_packages as shp inner join seg_packages as sp    \n
						 on shp.package_id = sp.package_id                            \n
						 where bsked_id = '$bskedID' order by package_name";

		$objResponse->addScriptCall("clearPkgList");

		if ($rs = $db->Execute($strSQL)) {
			if ($rs->RecordCount()) {
				while ($row = $rs->FetchRow()) {
					$obj = (object) 'details';
					$obj->id       = $row["package_id"];
					$obj->name     = $row["package_name"];
					$obj->amtlimit = $row["amountlimit"];

					$objResponse->addScriptCall("appendPkg", "package-list", $obj);
					$rcount++;
				}
			}
		}

		if ($rcount == 0) {
			$objResponse->addScriptCall("appendPkg", "package-list");
		}

		return $objResponse;
	}

		$xajax->processRequests();
?>