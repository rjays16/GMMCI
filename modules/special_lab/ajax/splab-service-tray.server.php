<?php

function populateSpecialLabServiceList($area='',$area_type='',$encounter_nr=0,$ref_source='LB',$is_cash=1,$discountid='',$discount=0,$is_senior=0,$is_walkin=1,$group_code,$source_req='LD',$isStat=0,$is_charge2comp=0,$compID='',$sElem,$searchkey,$page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srv=new SegSpecialLab();
		$objSS = new SocialService;
		$offset = $page * $maxRows;

		#$group_code = $ref_source;
		
		if (!$discount)
			$discount = 0;

		$ssInfo = $objSS->getSSClassInfo($discountid);

		if (($discountid=='SC')&& ($is_senior))
			$is_senior = 1;
		else
			$is_senior = 0;

		/*if ($ref_source!='BB'){
			if ($ssInfo['parentid'])
				$discountid = $ssInfo['parentid'];
		}else{
			if ($discountid!='SC'){
				if ($ssInfo['parentid'])
						$discountid = $ssInfo['parentid'];
			}
		}*/

		if ($discountid!='SC'){
			if ($ssInfo['parentid'])
				$discountid = $ssInfo['parentid'];
		}

		$sc_walkin_discount = 0;
		#if ((($is_senior) && ($is_walkin)) || ((($is_senior)&&($is_cash==0)))){
		#if (($is_senior) && ($is_walkin)){
        if ($is_senior){
			$discountid='SC';

			$sql_sc = "SELECT * FROM seg_default_value WHERE name='senior discount' AND source='SS'";
			$rs_sc = $db->Execute($sql_sc);
			$row_sc = $rs_sc->FetchRow();

			if ($row_sc['value'])
				$sc_walkin_discount = $row_sc['value'];
		}

		if ($isStat){
			$sql_stat = "SELECT * FROM seg_default_value WHERE name='stat charge' AND source='LD'";
			$rs_stat = $db->Execute($sql_stat);
			$row_stat = $rs_stat->FetchRow();

			if ($row_stat['value'])
				$stat_additional = $row_stat['value'];
		}

		if ($area_type=='pw'){
			$sql_pw = "SELECT * FROM seg_default_value WHERE name='payward charge' AND source='LD'";
			$rs_pw = $db->Execute($sql_pw);
			$row_pw = $rs_pw->FetchRow();

			if ($row_pw['value'])
				$pw_additional = $row_pw['value'];
		}

		#$objResponse->alert('van = '.$sc_walkin_discount);
		#--------
		if (stristr($searchkey,",")){
			$keyword_multiple = explode(",",$searchkey);
			#$objResponse->alert($keyword_multiple[0]);
			$codenum = 0;
			if (is_numeric($keyword_multiple[0]))
					$codenum = 1;

			for ($i=0;$i<sizeof($keyword_multiple);$i++){
				$keyword .= "'".trim($keyword_multiple[$i])."',";
			}
			#$objResponse->addAlert('keyword1 = '.$keyword);
			$word = trim($keyword);
			#$objResponse->addAlert('word = '.$word);
			$searchkey = substr($word,0,strlen($word)-1);
			#$objResponse->addAlert('keyword = '.$keyword);
			$multiple = 1;
		}else{
			$multiple = 0;
		}
	//added by poliam 03/28/2014
		if($encounter_nr){
			$RoomTypePrice=$srv->GetRoomType($encounter_nr);
		}else{
			$RoomTypePrice=$srv->GetRoomType($encounter_nr);
		}
		//ended by poliam 03/28/2014
		#----------------
		#$objResponse->alert($discountid);
        #$objResponse->alert($area);
		$ergebnis=$srv->SearchService($source_req, $is_charge2comp, $compID, $ref_source,$is_cash,$discountid,$discount, $is_senior, $is_walkin, $sc_walkin_discount,$group_code,$codenum,$searchkey,$multiple,$maxRows,$offset,$area);
		#$objResponse->alert($srv->sql);
		$total = $srv->FoundRows();

		$lastPage = floor($total/$maxRows);

		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","request-list");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				$name = $result["name"];
				if (strlen($name)>40)
					$name = substr($result["name"],0,40)."...";

				if ($result['status']=='unavailable')
						$available = 0;
				else
						$available = 1;

				#added by VAN 07-14-2010
				if ($area_type){
						$query4 = "SELECT IF($is_cash,p.price_cash,p.price_charge) AS net_price,
												p.price_cash, p.price_charge
												FROM seg_service_pricelist AS p
												WHERE p.service_code=".$db->qstr($result["service_code"])."
												AND p.ref_source='LB' AND p.area_code='$area_type'";
						#$objResponse->alert($query4);
						$lab_serv2 = $db->GetRow($query4);
						if ($lab_serv2){
							$result["price_cash"] = $lab_serv2["price_cash"];
							$result["price_charge"] = $lab_serv2["price_charge"];
							$result["net_price"] = $lab_serv2["net_price"];
						}else{
							$result["price_cash"] = $result["price_cash"] + ($result["price_cash"] * $pw_additional);
							$result["price_charge"] = $result["price_charge"] + ($result["price_charge"] * $pw_additional);
							$result["net_price"] = $result["net_price"] + ($result["net_price"] * $pw_additional);
						}

						#add additional charges
						if ($area_type!='pw'){
							if ($isStat){
									$result["price_cash"] = $result["price_cash"] + ($result["price_cash"] * $stat_additional);
									$result["price_charge"] = $result["price_charge"] + ($result["price_charge"] * $stat_additional);
									$result["net_price"] = $result["net_price"] + ($result["net_price"] * $stat_additional);
							}
						}else{
							if ($isStat){
									$price_cash = $result["price_cash"] + ($result["price_cash"] * $stat_additional);
									$result["price_cash"] = round($price_cash);
									$price_charge = $result["price_charge"] + ($result["price_charge"] * $stat_additional);
									$result["price_charge"] = round($price_charge);
									$net_price = $result["net_price"] + ($result["net_price"] * $stat_additional);
									$result["net_price"] = round($net_price);
							}
						}
			 }else{
				 #add additional charges
				 if ($isStat){
						$result["price_cash"] = $result["price_cash"] + ($result["price_cash"] * $stat_additional);
						$result["price_charge"] = $result["price_charge"] + ($result["price_charge"] * $stat_additional);
						$result["net_price"] = $result["net_price"] + ($result["net_price"] * $stat_additional);
				 }
			 }
                
                #get the list of child test
                if ($result['is_profile']){
                    $sql_child = "SELECT fn_get_labtest_child_code(".$db->qstr($result["service_code"]).") AS childtest";
                    $child_test = $db->GetOne($sql_child);
                }
                
              		$prices = $srv->GetRoomPrice($result["service_code"], $RoomTypePrice, $source='LB', $is_cash);
              		
                	
                	$other_cash=0;
                	$other_charge =0;
                	
                	if($prices){
                		while($rowprice=$prices->FetchRow()) {
                		$other_cash = $rowprice['cash_price'];
                		$other_charge = $rowprice['charge_price'];
                		$other_net = $rowprice['net_price'];
                		}
                	}
                	
                	if($prices){
                		$objResponse->addScriptCall("addProductToList","request-list",$result["service_code"],
														$name,$result["group_code"],$result["code_num"],$codenum,number_format($other_cash, 2, '.', ''),
														number_format($other_charge, 2, '.', ''), $result['is_socialized'],
                                                        $result['in_lis'],$result['oservice_code'],$result['ipdservice_code'],number_format($other_net, 2, '.', ''), 
                                                        $available, $result['is_blood_product'],$result['is_package'],$result['is_profile'], $child_test);
                	}else{
                	
				$objResponse->addScriptCall("addProductToList","request-list",$result["service_code"],
														$name,$result["group_code"],$result["code_num"],$codenum,number_format($result["price_cash"], 2, '.', ''),
														number_format($result["price_charge"], 2, '.', ''), $result['is_socialized'],
                                                        $result['in_lis'],$result['oservice_code'],$result['ipdservice_code'],number_format($result['net_price'], 2, '.', ''), 
                                                        $available, $result['is_blood_product'],$result['is_package'],$result['is_profile'], $child_test);
                         }			
                        }#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addProductToList","request-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}
#---------------------------------------------------

	function setALLDepartment($dept_nr=0){
		$dept_obj=new Department;

		$objResponse = new xajaxResponse();

		$rs=$dept_obj->getAllMedicalObject();
		$objResponse->addScriptCall("ajxClearDocDeptOptions",1);
		if ($rs) {
			$objResponse->addScriptCall("ajxAddDocDeptOption",1,"-Select a Department-",0);
			while ($result=$rs->FetchRow()) {
				 $objResponse->addScriptCall("ajxAddDocDeptOption",1,$result["name_formal"],$result["nr"]);
			}
		if($dept_nr)
				$list='';
				$objResponse->addScriptCall("ajxSetDepartment", $dept_nr, $list); # set the department
		}
		else {
			$objResponse->addAlert("setALLDepartment : Error retrieving Department information...");
		}
		return $objResponse;
	}

	function setDepartmentOfDoc($personell_nr=0) {
		$dept_obj=new Department;

		$objResponse = new xajaxResponse();
			if ($personell_nr!=0){
			$result=$dept_obj->getDeptofDoctor($personell_nr);
			if ($result){
				$list = $dept_obj->getAncestorChildrenDept($result["nr"]);   # burn added : July 19, 2007
				if (trim($list)!="")
					$list .= ",".$result["nr"];
				else
					$list .= $result["nr"];
				$objResponse->addScriptCall("ajxSetDepartment",$result["nr"],$list); # set the department
			}
			if($personell_nr)
				$objResponse->addScriptCall("ajxSetDoctor",$personell_nr); # set the doctor

		}else{
			$objResponse->addAlert("setDepartmentOfDoc : Error retrieving Department information of a doctor...");
		}
		return $objResponse;
	}

	function setDoctors($dept_nr=0, $personell_nr=0) {
		$objResponse = new xajaxResponse();

		$pers_obj=new Personell;
		if ($dept_nr)
			$rs=$pers_obj->getDoctorsOfDept($dept_nr);
		else
			$rs=$pers_obj->getDoctors(2);	# argument, $admit_patient NOT IN (0,1), BOTH Inpatient/ER & Outpatient

		$objResponse->addScriptCall("ajxClearDocDeptOptions",0);
		if ($rs) {
			$objResponse->addScriptCall("ajxAddDocDeptOption",0,"-Select a Doctor-",0);

			while ($result=$rs->FetchRow()) {
				if (trim($result["name_middle"]))
					$dot  = ".";

				$doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;
				$doctor_name = ucwords(strtolower($doctor_name)).", MD";

				$doctor_name = htmlspecialchars($doctor_name);
				$objResponse->addScriptCall("ajxAddDocDeptOption",0,$doctor_name,$result["personell_nr"]);
			}
			if($personell_nr)
				$objResponse->addScriptCall("ajxSetDoctor", $personell_nr); # set the doctor
			if($dept_nr)
				$objResponse->addScriptCall("ajxSetDepartment", $dept_nr); # set the department
			$objResponse->addScriptCall("request_doc_handler"); # set the 'request_doctor_out' textbox
		}
		else {
			$objResponse->addScriptCall("ajxAddDocDeptOption",0,"-No Doctor Available-",0);
		}
		return $objResponse;
	}

	function getDeptDocValues($encounter_nr){
		global $db;
		$objResponse = new xajaxResponse();

		$enc_obj=new Encounter;

		$patient = $enc_obj->getPatientEncounter($encounter_nr);
		#$objResponse->alert($enc_obj->sql);
		if (($patient['encounter_type']==1)|| ($patient['encounter_type']==2)){
			$dept_nr = $patient['current_dept_nr'];
			$doc_nr = $patient['current_att_dr_nr'];
		}elseif (($patient['encounter_type']==3)|| ($patient['encounter_type']==4)){
			$dept_nr = $patient['consulting_dept_nr'];
			$doc_nr = $patient['consulting_dr_nr'];
		}else{
			$dept_nr = 0;
			$doc_nr = 0;
		}

		$objResponse->addScriptCall("setDeptDocValues",$dept_nr, $doc_nr);

		return $objResponse;
	}

	function getAllServiceOfPackage($ref_source='LB', $service_code, $is_cash=1, $discountid='',$discount=0,$is_senior=0,$is_walkin=1, $isStat=0){
				global $db;
				$objResponse = new xajaxResponse();
				$srv=new SegSpecialLab();
				$objSS = new SocialService;

				$ssInfo = $objSS->getSSClassInfo($discountid);

				/*if ($ref_source!='BB'){
					if ($ssInfo['parentid'])
						$discountid = $ssInfo['parentid'];
				}else{
					if ($discountid!='SC'){
						if ($ssInfo['parentid'])
								$discountid = $ssInfo['parentid'];
					}
				}*/

				if ($discountid!='SC'){
					if ($ssInfo['parentid'])
						$discountid = $ssInfo['parentid'];
				}

				$sc_walkin_discount = 0;
				if (($is_senior) && ($is_walkin)){
					$discountid='SC';

					$sql_sc = "SELECT * FROM seg_default_value WHERE name='senior discount' AND source='SS'";
					$rs_sc = $db->Execute($sql_sc);
					$row_sc = $rs_sc->FetchRow();

					if ($row_sc['value'])
						$sc_walkin_discount = $row_sc['value'];
				}

				if ($isStat){
					$sql_stat = "SELECT * FROM seg_default_value WHERE name='stat charge' AND source='LD'";
					$rs_stat = $db->Execute($sql_stat);
					$row_stat = $rs_stat->FetchRow();

					if ($row_stat['value'])
						$stat_additional = $row_stat['value'];
				}

				if ($area_type=='pw'){
					$sql_pw = "SELECT * FROM seg_default_value WHERE name='payward charge' AND source='LD'";
					$rs_pw = $db->Execute($sql_pw);
					$row_pw = $rs_pw->FetchRow();

					if ($row_pw['value'])
						$pw_additional = $row_pw['value'];
				}

				#$objResponse->alert($is_cash." - ".$discountid." - ".$discount);
                
                #added by VAN 01-16-2013
                #identify if is a package or not
                #package is not the same with profile (have child test in the LIS)
                $sql_pk = "SELECT * from seg_lab_services where service_code='$service_code'";
                $rs_pk = $db->Execute($sql_pk);
                $row_pk = $rs_pk->FetchRow();
                
                $ispackage = 0;
                if (($row_pk['is_package'])&&(!$row_pk['is_profile']))
                    $ispackage = 1;
                
                
                if ($ispackage){
				    $rs_group = $srv->isServiceAPackage($service_code);
                    #$objResponse->alert($srv->sql);
                    $rs_count = $srv->count;
				    if ($rs_count){
					    if (!$discount)
						    $discount = 0;
					    $rs_group_inc = $srv->getAllServiceOfPackage($service_code, $is_cash, $discountid, $discount, $is_senior, $is_walkin, $sc_walkin_discount);
					    #$objResponse->alert('pkg = '.$srv->sql);
					    #lab exam request that is a package
					    if (is_object($rs_group_inc)){
						    while ($row=$rs_group_inc->FetchRow()){
							    if ($isStat){
									    $row['price_cash'] = $row['price_cash'] + ($row['price_cash'] * $stat_additional);
									    $row['price_charge'] = $row['price_charge'] + ($row['price_charge'] * $stat_additional);
									    $row['net_price'] = $row['net_price'] + ($row['net_price'] * $stat_additional);
							    }

							    $objResponse->addScriptCall("prepareAdd_Package",$row['service_code'],$row['name'],number_format($row['price_cash'], 2, '.', ''),
                                                             number_format($row['price_charge'], 2, '.', ''),$row['is_socialized'],
                                                             $row['in_lis'],$row['oservice_code'],$row['ipdservice_code'],$row['group_code'],
                                                             number_format($row['net_price'], 2, '.', ''),$row['is_blood_product']);
						    }
					    }

				    } else{
					     #lab exam request that is not a package
					     $objResponse->addScriptCall("prepareAdd_NotPackage",$service_code);
					     #$objResponse->alert('not pkg = '.$srv->sql);
				    }
                }else{
                    $objResponse->addScriptCall("prepareAdd_NotPackage",$service_code);   
                }    

				return $objResponse;
		}
		 #-----------------

	function checkTestERLab($service_code){
		global $db;
		$objResponse = new xajaxResponse();
		$srv=new SegSpecialLab();

		$row_service_code = $srv->get_TestAllowedER($service_code);
		#$objResponse->alert($srv->sql);
		#$objResponse->alert($row_service_code['service_code']);

		#allowed in ER LAB
		$service_code = trim($row_service_code['service_code']);

		if (!empty($service_code)){
			$objResponse->addScriptCall("enableButtonClear",1);
		}else{
			$objResponse->addScriptCall("enableButtonClear",0);
		}

		return $objResponse;
	}

	function populate_lab_checklist($section,$area_type='', $searchkey='', $area='',$ref_source='LB',$is_cash=1,$discountid='',$discount=0,$is_senior=0,$is_walkin=1,$source_req='LD',$isStat=0,$is_charge2comp=0,$compID='',$encounter_nr)
	{
		global $db;
		$objSS = new SocialService;
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("checklist-div", "innerHTML", "");
		#$objResponse->alert('splab d='.$discountid);
		#edited by VAN 07-30-2010
		if ($ref_source){
				if ($ref_source=='LB')
					$grp_cond = " AND l.group_code NOT IN ('SPL','IC') ";
				else{
					if ($ref_source=='BB')
						$ref_source = 'B';
					$grp_cond = " AND l.group_code='".$ref_source."' ";
				}
		}else
				#$grp_cond = "";
				$grp_cond = " AND l.group_code NOT IN (SPL','IC') ";

		$grp_cond2 = "";
		//added by poliam 03/28/2014
		if($encounter_nr){
			$RoomTypePrice=$objSS->GetRoomType($encounter_nr);
		}else{
			$RoomTypePrice=$objSS->GetRoomType($encounter_nr);
		}
		//ended by poliam 03/28/2014
		if ($group_code)
			$grp_cond2 = " AND l.group_code='$group_code'";

		if ($area=='ER')
			$area_cond = " AND is_ER=1 ";
		else
			$area_cond = "";
       
		if (!$discount)
			$discount = 0;

		$ssInfo = $objSS->getSSClassInfo($discountid);

		if ($discountid=='SC')
			$is_senior = 1;
		else
			$is_senior = 0;

		/*if ($ref_source!='BB'){
			if ($ssInfo['parentid'])
				$discountid = $ssInfo['parentid'];
		}else{
			if ($discountid!='SC'){
				if ($ssInfo['parentid'])
						$discountid = $ssInfo['parentid'];
			}
		}*/

		if ($discountid!='SC'){
			if ($ssInfo['parentid'])
				$discountid = $ssInfo['parentid'];
		}

		$sc_walkin_discount = 0;
		//if (($is_senior) && ($is_walkin)){
        if ($is_senior){
			$discountid='SC';

			$sql_sc = "SELECT * FROM seg_default_value WHERE name='senior discount' AND source='SS'";
			$rs_sc = $db->Execute($sql_sc);
			$row_sc = $rs_sc->FetchRow();

			if ($row_sc['value'])
				$sc_walkin_discount = $row_sc['value'];
		}

		if ($isStat){
			$sql_stat = "SELECT * FROM seg_default_value WHERE name='stat charge' AND source='LD'";
			$rs_stat = $db->Execute($sql_stat);
			$row_stat = $rs_stat->FetchRow();

			if ($row_stat['value'])
				$stat_additional = $row_stat['value'];
		}

		if ($area_type=='pw'){
			$sql_pw = "SELECT * FROM seg_default_value WHERE name='payward charge' AND source='LD'";
			$rs_pw = $db->Execute($sql_pw);
			$row_pw = $rs_pw->FetchRow();

			if ($row_pw['value'])
				$pw_additional = $row_pw['value'];
		}

		if ($discountid){
			$with_disc_query = " IF(l.is_socialized=0,
														 IF((l.in_phs=1 AND '$discountid'='PHS' AND $is_cash),(l.price_cash*(1-$discount)),IF($is_cash,IF($is_senior,l.price_cash*(1-$sc_walkin_discount),l.price_cash),l.price_charge)),
														 IF($is_cash,
																	 IF($is_senior,IF($is_cash,IF($is_walkin,(l.price_cash*(1-$sc_walkin_discount)),
																	 IF(sd.price,sd.price,(l.price_cash*(1-$discount)))),l.price_charge),
																	 IF($is_cash,
																			 IF(sd.price,sd.price,
																				 IF($is_cash,
																							(l.price_cash*(1-$discount)),
																							(l.price_charge*(1-$discount))
																				 )
																			 ),
																			 l.price_charge
																		)
															),
															l.price_charge)
													) AS net_price , ";

				$with_disc_join = "LEFT JOIN seg_service_discounts AS sd ON sd.service_code=l.service_code
																AND sd.service_area='LB' AND sd.discountid='$discountid'";

		} else{
			if ($source_req=='IC'){
				if ($is_charge2comp){
					$with_disc_query = " IF(ics.price,ics.price,IF($is_cash,l.price_cash,l.price_charge)) AS net_price, ";
					$with_disc_join = " LEFT JOIN seg_industrial_comp_price AS ics ON ics.service_code=l.service_code
															AND ics.company_id='".$compID."' AND ics.service_area='LB'";
				}else{
					$with_disc_query = " IF($is_cash,l.price_cash,l.price_charge) AS net_price, ";
					$with_disc_join = " ";
				}

			}else{
				$with_disc_query = "  IF($is_cash,l.price_cash,l.price_charge) AS net_price, ";

				$with_disc_join = "";
			}
		}

		#-------------------
		if($section){
			$query = "SELECT gm.* FROM seg_gui_mgr AS gm WHERE gm.ref_source='LD' AND gm.section=".$db->qstr($section);
		}else{
			$query = "SELECT gm.* FROM seg_gui_mgr AS gm WHERE gm.ref_source='LD'  AND gm.nr <> '1'";
		}

		$result = $db->Execute($query);
		if($result->RecordCount()>0) {

			while($row=$result->FetchRow())
			{
				//$query2 = "SELECT gmd.*, l.name, l.status, l.price_cash as`cash`, l.price_charge as `charge`, \n".
//									"l.group_code,l.is_socialized FROM seg_gui_mgr_details AS gmd \n".
//									"LEFT JOIN seg_lab_services AS l ON gmd.service_code=l.service_code \n".
//									"WHERE gmd.nr=".$db->qstr($row["nr"]);

				#edited by VAN 07-29-2010
				$query2 = "SELECT gmd.*, l.name, l.status, l.oservice_code, l.ipdservice_code, l.in_lis, l.is_blood_product, l.is_profile, l.is_package,
									$with_disc_query
									l.price_cash as`cash`, l.price_charge as `charge`, \n".
									"l.group_code,l.is_socialized FROM seg_gui_mgr_details AS gmd \n".
									"LEFT JOIN seg_lab_services AS l ON gmd.service_code=l.service_code ".$grp_cond." ".$grp_cond2." \n".
									$with_disc_join." WHERE gmd.nr=".$db->qstr($row["nr"])." ".$area_cond;

				$if_exists = true;
				if($searchkey!="") {
					$search_sql = "SELECT IF(EXISTS(SELECT l.service_code FROM seg_lab_services AS l WHERE l.service_code=".
						"gmd.service_code),1,0) AS `is_existing` \n".
						"FROM seg_gui_mgr_details AS gmd LEFT JOIN seg_lab_services AS l ON l.service_code=gmd.service_code \n".
						"WHERE (gmd.service_code LIKE '%".$searchkey."%' OR l.name LIKE '%".$searchkey."%') AND gmd.nr='".$row["nr"]."'";
					$if_exists = $db->GetOne($search_sql);
					if(!empty($if_exists))
					{
						 $query2.= "AND ((l.service_code like '%".$searchkey."%' OR l.name like '%".$searchkey."%')".
											" OR gmd.name_type='H')";
					}
				}

				if($if_exists)
				{
					$query2.=" AND l.status NOT IN ('deleted','hidden','inactive','void') ORDER BY gmd.row_order_no, gmd.col_order_no ASC";
					#$objResponse->alert($query2);
					$guiRes = $db->Execute($query2);
					if (is_object($guiRes)){
						while($guiDetails=$guiRes->FetchRow())
						{
							//$objResponse->alert("row-".print_r($guiDetails,true));
							if($guiDetails["name_type"]=="D")
							 {
								 #added by VAN 06-26-2010
								 if ($guiDetails['status']=='unavailable')
									$available = 0;
								 else
									$available = 1;

								#added by VAN 07-15-2010
									if ($area_type){
											if ($discountid){
												$with_disc_query2 = " IF(l.is_socialized=0,
																							 IF((l.in_phs=1 AND '$discountid'='PHS' AND $is_cash),(p.price_cash*(1-$discount)),IF($is_cash,IF($is_senior,p.price_cash*(1-$sc_walkin_discount),p.price_cash),p.price_charge)),
																							 IF($is_cash,
																										 IF($is_senior,IF($is_cash,IF($is_walkin,(p.price_cash*(1-$sc_walkin_discount)),
																										 IF(sd.price,sd.price,(p.price_cash*(1-$discount)))),p.price_charge),
																										 IF($is_cash,
																												 IF(sd.price,sd.price,
																													 IF($is_cash,
																																(p.price_cash*(1-$discount)),
																																(p.price_charge*(1-$discount))
																													 )
																												 ),
																												 p.price_charge
																											)
																								),
																								p.price_charge)
																						) AS net_price , ";

													$with_disc_join2 = "
																							 LEFT JOIN seg_service_discounts AS sd ON sd.service_code=l.service_code
																									AND sd.service_area='LB' AND sd.discountid='$discountid'";

											} else{
												$with_disc_query2 = "  IF($is_cash,p.price_cash,p.price_charge) AS net_price, ";

												$with_disc_join2 = "";
											}

											$query4 = "SELECT $with_disc_query2 p.service_code, p.price_cash as `cash`, p.price_charge as `charge`
																	FROM seg_service_pricelist AS p
																	INNER JOIN seg_lab_services AS l ON l.service_code=p.service_code
																	$with_disc_join2
																	WHERE p.service_code=".$db->qstr($guiDetails["service_code"])."
																	AND p.ref_source='LB' AND p.area_code='$area_type'";
											#$objResponse->alert($query4);
											$lab_serv2 = $db->GetRow($query4);
									}

									if ($lab_serv2){
										$guiDetails["cash"] = $lab_serv2["cash"];
										$guiDetails["charge"] = $lab_serv2["charge"];
										$guiDetails["net_price"] = $lab_serv2["net_price"];
									}else{
										$guiDetails["price_cash"] = $guiDetails["price_cash"] + ($guiDetails["price_cash"] * $pw_additional);
										$guiDetails["price_charge"] = $guiDetails["price_charge"] + ($guiDetails["price_charge"] * $pw_additional);
										$guiDetails["net_price"] = $guiDetails["net_price"] + ($guiDetails["net_price"] * $pw_additional);
									}

									#add additional charges
									if ($area_type!='pw'){
										if ($isStat){
												$guiDetails["cash"] = $guiDetails["cash"] + ($guiDetails["cash"] * $stat_additional);
												$guiDetails["charge"] = $guiDetails["charge"] + ($guiDetails["charge"] * $stat_additional);
												$guiDetails["net_price"] = $guiDetails["net_price"] + ($guiDetails["net_price"] * $stat_additional);
										}
									}else{
										if ($isStat){
												$price_cash = $result["price_cash"] + ($result["price_cash"] * $stat_additional);
												$result["price_cash"] = round($price_cash);
												$price_charge = $result["price_charge"] + ($result["price_charge"] * $stat_additional);
												$result["price_charge"] = round($price_charge);
												$net_price = $result["net_price"] + ($result["net_price"] * $stat_additional);
												$result["net_price"] = round($net_price);
										}
									}
                                 
                                 #get the list of child test
                                 if ($guiDetails["is_profile"]){
                                    $sql_child = "SELECT fn_get_labtest_child_code(".$db->qstr($guiDetails["service_code"]).") AS childtest";
                                    $child_test = $db->GetOne($sql_child);
                                 }
                
                                 //added by poliam 03/28/2014
                                 	$prices = $objSS->GetRoomPrice($guiDetails["service_code"], $RoomTypePrice, $source='LB', $is_cash);
				                	$other_cash=0;
				                	$other_charge =0;
				                	#$objResponse->alert($srv->sql);
				                	if($prices){
				                		while($rowprice=$prices->FetchRow()) {
				                		$other_cash = $rowprice['cash_price'];
				                		$other_charge = $rowprice['charge_price'];
				                		$other_net = $rowprice['net_price'];
				                		}
				                	}else{
				                		$other_cash = $guiDetails["cash"];
				                		$other_charge = $guiDetails["charge"];
				                		$other_net = $guiDetails["net_price"];
				                	}
                                 //ended by poliam 03/28/2014
                
								 #edited by VAN 07-30-2010
								 $service_details[] = array(
										"type"=>$guiDetails["name_type"],
										"col_nr"=>$guiDetails["col_order_no"],
										"row_nr"=>$guiDetails["row_order_no"],
										"service_code"=>$guiDetails["service_code"],
										"service_name"=>$guiDetails["name"],
										"service_cash"=>$other_cash,
										"service_charge"=>$other_charge,
										"service_net_price"=>$other_net,
										"service_in_lis"=>$guiDetails["in_lis"],
                                        "service_is_blood_product"=>$guiDetails["is_blood_product"],
										"oservice_code"=>$guiDetails["oservice_code"],
                                        "ipdservice_code"=>$guiDetails["ipdservice_code"],
										"group_code"=>$guiDetails["group_code"],
										"sservice"=>$guiDetails["is_socialized"],
										"available"=>$available,
                                        "service_is_profile"=>$guiDetails["is_profile"],
                                        "service_is_package"=>$guiDetails["is_package"],
                                        "service_child_test"=>$child_test,
									);
							 }
							 else	if($guiDetails["name_type"]=="H") {
								 $service_details[] = array(
									"type"=>$guiDetails["name_type"],
									"col_nr"=>$guiDetails["col_order_no"],
									"row_nr"=>$guiDetails["row_order_no"],
									"header"=>$guiDetails["header_data"]);
							 }
						}
						#$objResponse->alert($guiDetails["net_price"]);
						#$objResponse->alert("final-".print_r($service_details,true));
						$objResponse->addScriptCall("print_checklist", $service_details, $row["nr"]);
					}else{
						 $objResponse->addScriptCall("print_checklist_message", "NO CHECKLIST AVAILABLE FOR THIS SECTION..");
					}
				}
				$service_details = array();
			}
			if(!$if_exists){
				if($section != '0')
					$objResponse->addScriptCall("print_checklist_message", "SERVICE NOT FOUND..");
			}
		}
		else {
				$objResponse->addScriptCall("print_checklist_message", "NO CHECKLIST AVAILABLE FOR THIS SECTION..");
		}

		return $objResponse;
	}
	#end CHA---------------------


	require('./roots.php');

	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_department.php');
	require_once($root_path.'include/care_api_classes/class_personell.php');
	require($root_path."modules/special_lab/ajax/splab-service-tray.common.php");
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/care_api_classes/class_special_lab.php');
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	require_once($root_path.'include/care_api_classes/class_social_service.php');
	$xajax->processRequests();
?>