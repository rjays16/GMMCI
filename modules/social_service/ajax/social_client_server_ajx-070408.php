<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_social_service.php');
require_once($root_path.'modules/social_service/ajax/social_client_common_ajx.php');
require_once($root_path.'include/care_api_classes/class_person.php');

include_once($root_path.'include/inc_date_format_functions.php');

#added by VAN 05-10-08
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

#added by VAN 06-24-08
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');

#added by VAN 07-02-08
require_once($root_path.'include/care_api_classes/class_encounter.php');
	

function ProcessAddSScForm($aFormValues){
	$objResponse = new xajaxResponse();
	if(array_key_exists("encounter_nr",$aFormValues)){
		return AddSSc($aFormValues, 'ssl');  
		//$objResponse->alert("ProcessAddSScForm = " . print_r($aFormValues, true));
	}
	return $objResponse;
}

function UpdateProfileForm($aFormValues){
	$objResponse = new xajaxResponse();
	if(array_key_exists("encNr", $aFormValues)){
		#$objResponse->alert(" UpdateProfileForm=" .print_r($aFormValues, true));
		return AddSSc($aFormValues, 'lcr');  
	}
	
	return $objResponse;
}


function AddSSc($aFormValues, $listType){
	global $db;
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;
	
	#added by VAN 06-24-08
	$srvObj=new SegLab();
	$radio_obj = new SegRadio;
	
	//$ssArray=array();
	$bError = false;
	$bolSuccess = false;
	
	#added by VAN 05-10-08
	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	
	switch ($listType){
		case 'ssl':	
			if($aFormValues['service_code'][0] == ''){
				$objResponse->alert("Please select classification..");
				$bError = true;
			}
			
			#if($aFormValues['personal_circumstance'] == '' || $aFormValues['community_situation'] == '' || $aFormValues['nature_of_disease'] == '' ){
			#if($aFormValues['personal_circumstance'][0] == 0 || $aFormValues['community_situation'][0] == 0 || $aFormValues['nature_of_disease'][0] == 0 ){
			#edited by VAN 07-04-08
			if($aFormValues['personal_circumstance'][0] == 0 && $aFormValues['community_situation'][0] == 0 && $aFormValues['nature_of_disease'][0] == 0 ){
				#$objResponse->alert("Fill all the fields required for modifiers");
				$objResponse->alert("Fill at least one field of modifier.");
				$bError = true;
			}
				
			if(!$bError){
				$ssResult= $objSS->getSSInfo($aFormValues['service_code'][0]);
				if($ssResult){
					$ssRow = $ssResult->FetchRow();
					$_POST['discountid'] =$ssRow['discountid'];
					$_POST['discount'] = $ssRow['discount'];
					
					$_POST['encounter_nr'] = $aFormValues['encounter_nr'];
					$_POST['grant_dte'] = date('Y-m-d H:i:s');
					$_POST['sw_nr'] = $aFormValues['encoder_id'];
					/*
					$_POST['personal_circumstance'] = $aFormValues['personal_circumstance']; 
					$_POST['community_situation']  = $aFormValues['community_situation'];
					$_POST['nature_of_disease'] = $aFormValues['nature_of_disease'];
					*/
					$_POST['personal_circumstance'] = $aFormValues['personal_circumstance'][0]; 
					$_POST['community_situation']  = $aFormValues['community_situation'][0];
					$_POST['nature_of_disease'] = $aFormValues['nature_of_disease'][0];
					
					$_POST['pid'] = $aFormValues['pid'];
					#$objResponse->alert('pid = '+$aFormValues['pid']);
									
					//$objResponse->alert(print_r($_POST));
					#$bolSuccess = $objSS->saveSSCData($_POST);	//save classification including the modifiers
					#edited by VAN 05-13-08
					if ($aFormValues['encounter_nr']){
						$bolSuccess = $objSS->saveSSCData($_POST);	//save classification including the modifiers
						
					}else{
						$bolSuccess = $objSS->saveSSCDataByPID($_POST);	//save classification including the modifiers
					}	
				}
			}
		break;
		case 'lcr': // Use this for  updating personal profile
			
			if($aFormValues['occupation_select'][0] == ''){
				$objResponse->alert("Please select educational attainment.");
			}
						
			if(!$bError){
				#added by VAN 05-10-08			
				$ssArray['informant_name'] = $aFormValues['resp']; // respondent  
				$ssArray['relation_informant'] = $aFormValues['relation'];  // relation to patient 
				$ssArray['educational_attain'] = $aFormValues['occupation_select'][0]; //informant
				$ssArray['source_income'] = $aFormValues['s_income'];   // source of income
				$ssArray['monthly_income'] = $aFormValues['m_income'];  // monthly income
				$ssArray['nr_dependents'] = $aFormValues['nr_dep'];   // No of dependents
				#$objResponse->alert('light = '.$aFormValues['light']);
				$ssArray['hauz_lot_expense'] = $aFormValues['hauz_lot']; //modify id;
				$ssArray['food_expense'] = $aFormValues['food']; //modify id;
				$ssArray['ligth_expense'] = $aFormValues['light']; //modify id;
				$ssArray['water_expense'] = $aFormValues['water']; //modify id;
				$ssArray['transport_expense'] = $aFormValues['transport']; //modify id;
				$ssArray['other_expense'] = $aFormValues['other']; //modify id;
				
				$ssArray['pid'] = $aFormValues['pidNr']; //modify id;
				$ssArray['encounter_nr'] = $aFormValues['encNr']; //modify id;
				
				$glob_obj->getConfig('mss_%');
				
				$socInfo = $objSS->getSocServPatient($aFormValues['pidNr']);
				#$objResponse->alert("sql = ".$objSS->sql);
				$is_exists = $objSS->count;
				
				if ($is_exists){
					$mss_no = $socInfo['mss_no'];
					$mode = 'update';
				}else{
					#get new mss no
					$mss_no = $objSS->getLastMSSnr(date("Y-m-d"),"'".$GLOBAL_CONFIG['mss_nr_init']."'");
					$mode = 'save';
				}
				
				$ssArray['mss_no'] = $mss_no; //modify id;
				
				#$objResponse->alert("mode = ".$mode);
				
				if ($mode=='save'){
					$ok = $objSS->saveSocialPatientArray(&$ssArray);
					#$objResponse->alert("sql1 = ".$objSS->sql);
					$ok2 = $objSS->saveSocServPatientArray(&$ssArray);
					#$objResponse->alert("sql2 = ".$objSS->sql);
					
					if (($ok)&&($ok2))
						$bolSuccess = true;		
					else
						$bolSuccess = false;		
							
				}elseif ($mode=='update'){
					$ssArray['modify_time'] = date('Y-m-d H:i:s'); //modify date 
					$ssArray['modify_id'] = $aFormValues['encoderName']; //modify id;
									
					$ok2 = $objSS->updateSocServPatientArray($mss_no, $aFormValues['encNr'], &$ssArray);
					#$objResponse->alert("sql2 = ".$objSS->sql);
					if ($ok2)
						$bolSuccess = true;		
					else
						$bolSuccess = false;		
				}
				
				#$objResponse->alert("lcr = ". print_r($ssArray, true));
				
				#edited by VAN 05-09-08
				/*
				$modify_time = date('Y-m-d H:i:s');
				$modify_id = $aFormValues['encoderName'];
				
				$sql_update = "UPDATE care_encounter SET informant_name='".$aFormValues['resp']."', relation_informant='".$aFormValues['relation']."', " .
							"\n occupation = '".$aFormValues['occupation_select'][0]."' , source_income = '".$aFormValues['s_income']."', " .
							"\n monthly_income = '".$aFormValues['m_income']."' ,   nr_dependents='".$aFormValues['nr_dep']."' , " .
							"\n modify_time ='".$modify_time."' , modify_id = '".$modify_id."' " .
							"\n WHERE encounter_nr = '".$aFormValues['encNr']."'"; 
				
				*/
				
				#$objResponse->addAlert(print_r($ssArray));
								
				#if($result = $db->Execute($sql_update)){
				/*
				if($result = $db->Execute($sql_update)){
					//$objResponse->alert("profile was successfully updated");	
					$bolSuccess = true;						
				}else{
					$bolSuccess = false;
					//$objResponse->alert("sql failed: ". $sql_update);
				}
				*/
			}
						
		break;		
	}// end switch
	if($bolSuccess){
		//$objResponse->alert("service_code = ".$ssRow['discountid']." notes=".$aFormValues['notes']." encoder_id = ".$aFormValues['encoder_id']." grand_dte=". $_POST['grand_dte'])
		#$objResponse->alert('enc = '.$aFormValues['encounter_nr']);
		if($listType == 'ssl'){
			#$objResponse->call("xajax_PopulateSSC",$aFormValues['encounter_nr'],'ssl');
			#added by VAN
			$objResponse->call("xajax_PopulateSSC",$aFormValues['encounter_nr'],"'".$aFormValues['pid']."'",'ssl');
		}else{
			#$objResponse->alert("Profile has been successfully updated");
			#$objResponse->alert('pid = '.$aFormValues['pidNr']);
			#setMSS($aFormValues['pidNr']);
			
			if ($mode=='save')
				$objResponse->alert("Profile has been successfully created");
			else
				$objResponse->alert("Profile has been successfully updated");	
				
		}
	}else{
	
		if ($bError!=TRUE){
	    // TODO:  change this message alert for appropriate error.
		if($listType == 'ssl'){	
			$objResponse->alert("Saving Data failed: SQL->". $objSS->sql);
			#$objResponse->alert("You are not allowed to update the profile.");
		}else{
			$objResponse->alert("Saving Data failed: SQL->". $sql_update);
			#$objResponse->alert("You are not allowed to update the profile.");
		}
		}
	}
		
	return $objResponse;
}//end of function AddSSc()



function PopulateSSC($encounter_nr,$pid, $listtype){
	global $date_format;
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;
	
	#added by VAN 07-02-08
	$enc_obj=new Encounter;
	
	#$objResponse->addAlert('listtype, encountern, pid = '.$listtype." - ".$encounter_nr." - ".$pid);	
	#$objResponse->addAlert('pid = '.$pid);	
	switch ($listtype){
		case 'ssl':
			
			#GetProfile($objResponse, $encounter_nr); // get profile
			#added by VAN 05-12-08
			GetProfile($objResponse, $encounter_nr, $pid); // get profile
			#$sslist = $objSS->getSSCInfo($encounter_nr);
			#edited by VAN 05-13-08
			$sslist = $objSS->getSSCInfo($encounter_nr, $pid);
			
			#$objResponse->alert("sql = ".$objSS->sql);
			//$objResponse->alert("sslist->RecordCount() = ".$sslist->RecordCount()." \n sslist = " .print_r($sslist, true));					
			
			if($sslist){
				if($sslist->RecordCount()){
					$objResponse->call("js_clearRow", "ssctable");
					$temp=0;
					$tblesrc = '';
					while($row = $sslist->FetchRow()){
						if ($temp==0){
							$discountId = $row['discountid'];
							
							#added by VAN 05-13-08
							$pcircumstance = $row['pcircumstance'];
							$csituation = $row['csituation'];
							$ndesease = $row['ndesease'];
							
							$temp++;
						}
						//$objResponse->alert(print_r($row, true));
						$ssworker_name = ucfirst($row['name_last']).", ".ucfirst($row['name_first'])." ".$row['name_middle'] ;
						$grant_dte = @formatDate2Local($row['grant_dte'],$date_format);
	//function js_addRow(tableId, code, note, clsfby, grant_dte, listname, personal_circumstance, com_situation, nature_of_illness){					
					/*	$objResponse->call("js_addRow","ssctable",$row['discountid'],$row['notes'],$ssworker_name,$grant_dte, 
																'ssl', $row['pcircumstance'], $row['csituation'], $row['ndesease'] );	*/
						
						#added by VAN 05-13-08
						if (is_numeric(trim($row['pcircumstance']))){
							$pcircumstance_row = $objSS->getPatientModifier(1, trim($row['pcircumstance']));
							$pcircumstance_display = trim($pcircumstance_row['mod_subdesc']);
						}else{
							$pcircumstance_display = trim($row['pcircumstance']);
						}
						
						if (is_numeric(trim($row['csituation']))){		
							$csituation_row = $objSS->getPatientModifier(2, trim($row['csituation']));
							$csituation_display = trim($csituation_row['mod_subdesc']);
						}else{
							$csituation_display 	= trim($row['csituation']);
						}
							
						if (is_numeric(trim($row['ndesease']))){	
							$ndesease_row = $objSS->getPatientModifier(3, trim($row['ndesease']));
							$ndesease_display = trim($ndesease_row['mod_subdesc']);
						}else	{
							$ndesease_display = trim($row['ndesease']);
						}	
						/*
						$tblesrc .= "<table width=\"100%\"> ".
										"<tr>".
											"<td width=\"20%\"><h3> ".
												"<span>".$row['discountid']."</span>&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;".
												"<span>[".$grant_dte."</span>&nbsp;&nbsp;&nbsp;".
												"<span>".$ssworker_name."]</span>".
											"</h3></td>".
										"</tr>".
									"</table>".		
									"<table class=\"jedList\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"width:100%;margin-bottom:10px\">".
										"<thead>	".
											"<tr>".
												"<th width=\"20%\">Personal Circumtances</th>".
												"<th width=\"20%\">Community Situation</th>".
												"<th width=\"20%\">Nature of Illness/Disease</th>".
											"</tr>".
										"</thead>".
										"<tbody>".
											"<tr>".
												"<td>".trim($row['pcircumstance'])."</td>".
												"<td>".trim($row['csituation'])."</td>".
												"<td>".trim($row['ndesease'])."</td>".												
											"</tr>".
										"</tbody>".
									"</table>";
							*/
							$tblesrc .= "<table width=\"100%\"> ".
										"<tr>".
											"<td width=\"20%\"><h3> ".
												"<span>".$row['discountid']."</span>&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;".
												"<span>[".$grant_dte."</span>&nbsp;&nbsp;&nbsp;".
												"<span>".$ssworker_name."]</span>".
											"</h3></td>".
										"</tr>".
									"</table>".		
									"<table class=\"jedList\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"width:100%;margin-bottom:10px\">".
										"<thead>	".
											"<tr>".
												"<th width=\"20%\">Personal Circumtances</th>".
												"<th width=\"20%\">Community Situation</th>".
												"<th width=\"20%\">Nature of Illness/Disease</th>".
											"</tr>".
										"</thead>".
										"<tbody>".
											"<tr>".
												"<td>".$pcircumstance_display."</td>".
												"<td>".$csituation_display."</td>".
												"<td>".$ndesease_display."</td>".												
											"</tr>".
										"</tbody>".
									"</table>";		
						 
					}//end while
					$objResponse->assign("classification", "innerHTML", $tblesrc);
					$objResponse->assign("discountId", "value", $discountId);
				}
				//$discountId = $objSS->getSSCInfo($encounter_nr, true);
				#$objResponse->alert("discountid = ". print_r ($discountId, true). "\n  discountid = ".$discountId['discountid']);
				#$objResponse->alert($discountId);
				AddOptions($objResponse, $discountId );
				
				#added by VAN 05-13-08
				AddOptions_modifiers($objResponse, 1, $pcircumstance);
				AddOptions_modifiers($objResponse, 2, $csituation);
				AddOptions_modifiers($objResponse, 3, $ndesease);
				
				#added by VAN 
				# only C3 can be remodify
				if ($encounter_nr){
					#$objResponse->alert($discountId);
					$ssInfo = $objSS->getSSClassInfo($discountId);
					#$objResponse->alert($ssInfo['parentid']);
					
					#if (!(empty($discountId))&&($discountId!='C3'))	
					if (!(empty($discountId))&&(($discountId!='C3')&&($ssInfo['parentid']!='C3')))	
						$objResponse->assign("show", "style.display","none");
				}
				
			}else{
				$tr = "<tr><td colspan=\"5\">No classification exists</td></tr>";	
				$objResponse->addAssign("ssctbody", "innerHTML", $tr);
			}	
					break;
		case 'lcr':
			//TODO : CHANGE the query of the function  getLCRInfo($encounter_nr){
			$lcrlist = $objSS->getLCRInfo($encounter_nr);
			#$objResponse->alert("sql = ".$objSS->sql);
			#$objResponse->addAlert('hello ajax ='.$row['refno']);
			if($lcrlist){
				#$objResponse->addAlert('hello ajax true');
				$objResponse->assign("rqlistdiv", "style.display","''");
				while($row=$lcrlist->FetchRow()){
					//$objResponse->call("js_addRow","rqlisttable",$row['refno'],$row['orderdate'],$row['service_code'],$row['price'], 'lcr');	
					$totalCharge = sprintf('%01.2f', $row['total_charge']);
					
					$date_request = $row['date_request']." ".date("h:i:s A",$row["time_request"]);
					#$objResponse->alert($date_request);
					#$objResponse->call("js_addRow","rqlisttable",$row['refno'],$row['date_request'],$totalCharge,$row['dept'], 'lcr','' ,'' ,'' );	
					$objResponse->call("js_addRow","rqlisttable",$row['refno'],$date_request,$totalCharge,$row['dept'], 'lcr','' ,'' ,'' );	
				}
			}else{
				#commented by VAN 05-09-08
				#$objResponse->addAlert('hello ajax false = '.$encounter_nr);
				#$objResponse->assign("rqlistdiv", "style.display","none");
				if ($encounter_nr){
					#$objResponse->call("hideClassification");
					#edited by VAN 07-02-08
					$encInfo = $enc_obj->getEncounterInfo($encounter_nr);
					if ($encInfo['encounter_type']==2)
						$objResponse->call("hideClassification");
				}else{			
					$objResponse->call("js_addDefaultRow","rqlisttbody");
				}	
			}	
			
		break;
	}
		
	return $objResponse;
}

function GetProfile(&$objResponse,$enc, $pid){
	global $db;
	
	#$objResponse->alert("Getprofile -= encounter_nr = " .$enc);	
	/*
	$sql  = "SELECT e.encounter_type, e.pid, e.source_income, e.monthly_income, e.nr_dependents, e.er_opd_diagnosis,  ".
			"\n	e.occupation, oc.occupation_name , e.informant_name as respondent ,e.relation_informant as relation  ".
			"\n	FROM care_encounter as e ".
			"\n 	LEFT JOIN seg_occupation as oc on oc.occupation_nr = e.occupation ".
			"\n WHERE e.encounter_nr = '".$enc."'";		
	*/
	
	$sql  = "SELECT m.pid, p.*, e.educ_attain_name
				FROM seg_socserv_patient AS p 
				INNER JOIN seg_social_patient AS m ON m.mss_no=p.mss_no
				INNER JOIN seg_educational_attainment AS e ON e.educ_attain_nr=p.educational_attain
				WHERE m.pid = $pid";		
	
	#$objResponse->alert("sql -=" .$sql);		
	if($result = $db->Execute($sql)){
		#$objResponse->alert("result recordcount= =" .$result->RecordCount());		
		if($result->RecordCount()){
			if($row = $result->FetchRow()){
				#$objResponse->addAlert("diagnosis = ". $row['er_opd_diagnosis']);
				#added by VAN 04-07-08
			
				if (empty($row['er_opd_diagnosis'])&&($row['encounter_type']==1))
					$er_opd_diagnosis = "Not applicable. Patient is under ER Consultation";
				elseif (empty($row['er_opd_diagnosis'])&&($row['encounter_type']==2))
					$er_opd_diagnosis = "Not applicable. Patient is under OPD Consultation";	
				elseif (empty($row['er_opd_diagnosis'])&&(($row['encounter_type']==3)||($row['encounter_type']==4)))
					$er_opd_diagnosis = "None";	
				elseif ($row['er_opd_diagnosis'])
					$er_opd_diagnosis = $row['er_opd_diagnosis'];
								
				#$objResponse->call("setProfile",$row['er_opd_diagnosis'], $row['respondent'],$row['relation'],$row['occupation_name'] , 
				#					$row['source_income'] , $row['monthly_income'], $row['nr_dependents'] );				
				/*
				$objResponse->call("setProfile",$er_opd_diagnosis, $row['respondent'],$row['relation'],$row['occupation_name'] , 
									$row['source_income'] , $row['monthly_income'], $row['nr_dependents'] );				
				*/
				#edited by VAN
				#$expenses = $row['hauz_lot_expense'] + $row['food_expense'] + $row['ligth_expense'] + $row['water_expense'] + $row['transport_expense'] + $row['other_expense'];
				$objResponse->call("setProfile",$er_opd_diagnosis, $row['informant_name'],$row['relation_informant'],$row['educ_attain_name'] , 
									$row['source_income'] , $row['monthly_income'], $row['nr_dependents'], $row['hauz_lot_expense'],
									 $row['food_expense'], $row['ligth_expense'], $row['water_expense'], $row['transport_expense'], $row['other_expense'], $row['mss_no']);				
																			
				#OccupationOptions($objResponse, $row['occupation']);	//populate options			
				OccupationOptions($objResponse, $row['educational_attain']);	//populate options			
			}
		}else{
			#OccupationOptions($objResponse, $row['occupation']);//populate options
			OccupationOptions($objResponse, $row['educational_attain']);//populate options
		}
	}else{
		#$objResponse->alert("sql - ". $sql);
		$objResponse->alert("No record exists.");
	}
	
}// end of GetProfile


function AddOptions(&$objResponse,$discountId=''){
	//$objResponse = new xajaxResponse();
	$objSS = new SocialService;
	#$result = $objSS->getSSInfo();
	$ssInfo = $objSS->getSSClassInfo($discountId);
		
	$result = $objSS->getSSInfo('',$discountId,$ssInfo['parentid']);	
	#$objResponse->alert($objSS->sql);	
	$objResponse->call("js_ClearOptions", "service_code");
	if($result){
		while($row=$result->FetchRow()){ 
			$objResponse->call("js_AddOptions","service_code",$row['discountdesc'],$row['discountid'], "b");
		}
		if($discountId){
			//$objResponse->alert("discountid Option =". $discountId);
			$objResponse->call("setOption_a", "service_code", $discountId);
		}else{
			$objResponse->call("js_AddOptions", "service_code", "-Not Indicated-", "b");
		}
	}else{
		$objResponse->alert("DB failed: ".$objSS->sql);
	}
	//return $objResponse;
}//end of function AddOptions

#added by VAN 05-13-08
function AddOptions_modifiers(&$objResponse, $modifier, $recent_mod){
	//$objResponse = new xajaxResponse();
	$objSS = new SocialService;
	$result = $objSS->getModifiers($modifier);	
	#$objResponse->alert($objSS->sql);
	
	if ($modifier==1){	
		$objResponse->call("js_ClearOptions", "personal_circumstance");
		$objResponse->call("js_AddOptions2", "personal_circumstance", "-Select Personal Circumstances-",0,0);		
	}elseif ($modifier==2){
		$objResponse->call("js_ClearOptions", "community_situation");
		$objResponse->call("js_AddOptions2", "community_situation", "-Select Community Situations-",0,0);			
	}elseif ($modifier==3){	
		$objResponse->call("js_ClearOptions", "nature_of_disease");
		$objResponse->call("js_AddOptions2", "nature_of_disease", "-Select Nature of Illness-",0,0);		
	}
				
	if($result){
		while($row=$result->FetchRow()){ 
			#if (empty($row['mod_subdesc']))
			#	$row['mod_subdesc'] = 0;
			
			$desc = stripslashes(trim($row['mod_subdesc']));	
			#$objResponse->alert($desc);
			
			if ($modifier==1){	
				$objResponse->call("js_AddOptions2","personal_circumstance",$row['mod_subcode'],$row['mod_subcode'],$desc);
			}elseif ($modifier==2){
				$objResponse->call("js_AddOptions2","community_situation",$row['mod_subcode'],$row['mod_subcode'],$desc);
			}elseif ($modifier==3){
				$objResponse->call("js_AddOptions2","nature_of_disease",$row['mod_subcode'],$row['mod_subcode'],$desc);		
			}
		}
		
		if($modifier){
		/*AddOptions_modifiers($objResponse, 1, $pcircumstance);
				AddOptions_modifiers($objResponse, 2, $csituation);
				AddOptions_modifiers($objResponse, 3, $ndesease);*/
			//$objResponse->alert("discountid Option =". $discountId);
			if ($modifier==1)	
				$objResponse->call("setOption_a", "personal_circumstance", $recent_mod);
			elseif ($modifier==2)
				$objResponse->call("setOption_a", "community_situation", $recent_mod);
			elseif ($modifier==3)
				$objResponse->call("setOption_a", "nature_of_disease", $recent_mod);		
		}else{
			if ($modifier==1)	
				$objResponse->call("js_AddOptions2", "personal_circumstance", "-Not Indicated-",0,0);
			elseif ($modifier==2)
				$objResponse->call("js_AddOptions2", "community_situation", "-Not Indicated-",0,0);
			elseif ($modifier==3)
				$objResponse->call("js_AddOptions2", "nature_of_disease", "-Not Indicated-",0,0);		
		}
		
	}else{
		$objResponse->alert("DB failed: ".$objSS->sql);
	}
	//return $objResponse;
}//end of function AddOptions_circumstances

#------------------------

function OnChangeOptions($code){
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;
	if(!empty($code)) $result = $objSS->getSSInfo($code);
	#$objResponse->alert($objSS->sql);	
	if($result){
			//$objResponse->alert("DB failed: ".$objSS->sql);
			$row=$result->FetchRow();
			$objResponse->call("js_SetOptionDesc",'sscDesc' ,$row['discountdesc']);
	}else{
		//$objResponse->alert("DB failed: ".$objSS->sql);		
	}
	return $objResponse;
}

function OccupationOptions(&$objResponse, $selectedId = ''){
	$person_obj = new person();
	#$rs_obj = $person_obj->getOccupation();
	$rs_obj = $person_obj->getEducationalAttainment();
	
	$objResponse->call("js_ClearOptions", "occupation_select"); // clear options
	while ($result=$rs_obj->FetchRow()){
		$objResponse->call("js_AddOptions", "occupation_select", $result['educ_attain_name'], $result['educ_attain_nr'], "b");
	}	
	if($selectedId){
		$objResponse->call("setOption_b","occupation_select", $selectedId);
	}else{
		$objResponse->call("setOption_b","occupation_select", 0);
		//$objResponse->call("js_AddOptions","occupation_select", "-Not Indicated-", "b" );
	}
}// end of function OccupationOptions

function setMSS($pid){
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;
	if(!empty($pid)) $result = $objSS->getSocServPatient($pid);
	#$objResponse->alert($objSS->sql);
	#$objResponse->alert('mss = '.$result['mss_no']);	
	if($result){
			$objResponse->call("js_SetMssPatient",$result['mss_no']);
	}else{
		//$objResponse->alert("DB failed: ".$objSS->sql);		
	}
	return $objResponse;
}

$xajax->processRequest();
?>