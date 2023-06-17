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

#added by VAN 12-08-09
require_once($root_path.'include/care_api_classes/billing/class_billing.php');

#added by Jarel 07/24/13
require_once($root_path.'include/care_api_classes/or/class_segOr_miscCharges.php');

function ProcessAddSScForm($aFormValues){
	$objResponse = new xajaxResponse();
	if(array_key_exists("encounter_nr",$aFormValues)){
		return AddSSc($aFormValues, 'ssl');
		#$objResponse->alert("ProcessAddSScForm = " . print_r($aFormValues, true));
	}
	return $objResponse;
}

function UpdateProfileForm($aFormValues){
	$objResponse = new xajaxResponse();
	#$objResponse->alert('meem');
	if(array_key_exists("encNr", $aFormValues)){
		#$objResponse->alert(" UpdateProfileForm=" .print_r($aFormValues, true));
		return AddSSc($aFormValues, 'lcr');
	}

	return $objResponse;
}
#Added by Jarel 03-04-2013
function ProcessDeMeData($aFormValues){
    $objResponse = new xajaxResponse();
    if(array_key_exists("encounter_nr", $aFormValues)){
        return AddSSc($aFormValues, 'ssp');
    }

    return $objResponse;
}

function AddSSc($aFormValues, $listType){
	global $db;
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;

	#added by cheandrew 08-10-2010
	$details = (object) 'details';

	#added by VAN 06-24-08
	$srvObj=new SegLab();
	$radio_obj = new SegRadio;
	$person_obj = new person();
		$enc_obj=new Encounter;

	//$ssArray=array();
	$bError = false;
	$bolSuccess = false;

	#added by VAN 05-10-08
	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	#$objResponse->alert('here = '.$aFormValues['subc']);
	#$objResponse->alert('here1 = '.$aFormValues['subservice_code'][0]);
	switch ($listType){
		case 'ssl':
			#if($aFormValues['service_code'][0] == ''){
			if($aFormValues['service_code'][0] == "0"){
				$objResponse->alert("Please select classification..");
				$bError = true;
			}elseif(($aFormValues['service_code'][0]!="0")&&($aFormValues['subservice_code'][0] == "0")&&($aFormValues['subc']==1)){
				$objResponse->alert("Please select sub classification..");
				$bError = true;
			}#added by VAN 08-05-08
			 elseif((($aFormValues['subservice_code'][0]=='OT')||($aFormValues['subservice_code'][0]=='OTHER'))&&($aFormValues['subservice_code2']=='')){
					$objResponse->alert("Please enter the classification for other..");
					$bError = true;
			 }elseif((($aFormValues['subservice_code'][0]=='SC')||($aFormValues['subservice_code'][0]=='VET'))&&($aFormValues['idnumber']=='')){
					$objResponse->alert("Please enter the ID number of the senior citizen or veteran ID..");
					$bError = true;
			 }

			#if($aFormValues['personal_circumstance'] == '' || $aFormValues['community_situation'] == '' || $aFormValues['nature_of_disease'] == '' ){
			#if($aFormValues['personal_circumstance'][0] == 0 || $aFormValues['community_situation'][0] == 0 || $aFormValues['nature_of_disease'][0] == 0 ){
			#edited by VAN 07-04-08
			if ($aFormValues['withrec']==1){
				if($aFormValues['personal_circumstance'][0] == 0 && $aFormValues['community_situation'][0] == 0 && $aFormValues['nature_of_disease'][0] == 0 ){
					#$objResponse->alert("Fill all the fields required for modifiers");
					$objResponse->alert("Fill at least one field of modifier.");
					$bError = true;
				}
			}

			if(!$bError){
				#added by VAN 07-04-08
				#$objResponse->alert("sub - ".$aFormValues['subservice_code'][0]);
				#$objResponse->alert("ser - ".$aFormValues['service_code'][0]);
				#$objResponse->alert("subc = ".$aFormValues['subc']);
				if ($aFormValues['subc']==1){
					$ssResult= $objSS->getSSInfo($aFormValues['subservice_code'][0]);
				}else{
					if (($aFormValues['service_code'][0]=='C1')||($aFormValues['service_code'][0]=='C2')||($aFormValues['service_code'][0]=='C3')){
										#if ($aFormValues['service_code'][0]=='C3'){
						if($aFormValues['subservice_code'][0])
							$ssResult= $objSS->getSSInfo($aFormValues['subservice_code'][0]);
						else
							$ssResult= $objSS->getSSInfo($aFormValues['service_code'][0]);

					}else
						$ssResult= $objSS->getSSInfo($aFormValues['service_code'][0]);
				}
				if($ssResult){
					$ssRow = $ssResult->FetchRow();
					#$objResponse->alert($aFormValues['subservice_code2']);
					#added by VAN 08-05-08
					if ($aFormValues['subservice_code2']){
						$_POST['other_name'] =$aFormValues['subservice_code2'];
					}else{
						$_POST['other_name'] ='';
					}

					if ($aFormValues['idnumber']){
						/*
						if ($aFormValues['subservice_code'][0]=='SC')
							$_POST['senior_ID'] =$aFormValues['idnumber'];
						elseif ($aFormValues['subservice_code'][0]=='VET')
							$_POST['veteran_ID'] =$aFormValues['idnumber'];
						*/
						$_POST['id_number'] =$aFormValues['idnumber'];
					}else{
						/*
						if ($aFormValues['subservice_code'][0]=='SC')
							$_POST['senior_ID'] ='';
						elseif ($aFormValues['subservice_code'][0]=='VET')
							$_POST['veteran_ID'] ='';
						*/
						$_POST['id_number'] ='';
					}

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

					#--------added by VAN 07-04-08
					$glob_obj->getConfig('mss_%');

					$socInfo = $objSS->getSocServPatient($aFormValues['pid']);
					#$objResponse->alert("sql = ".$objSS->sql);
					$is_exists = $objSS->count;

					if (!($is_exists)){
						#get new mss no
						$mss_no = $objSS->getLastMSSnr(date("Y-m-d"),"'".$GLOBAL_CONFIG['mss_nr_init']."'");
						#$objResponse->alert("sql2 = ".$objSS->sql);
						$_POST['mss_no'] = $mss_no; //modify id;
						$ok = $objSS->saveSocialPatientArray(&$_POST);
						#$objResponse->alert("sql2 = ".$objSS->sql);
						if($ok){
							$objResponse->alert("MSS No. of patient with HRN ".$_POST['pid']." is successfully created");

							if ($aFormValues['subservice_code'][0]=='SC'){
								$type = 'SC';
								$id_label = 'Senior Citizen ID';
								$id_number = $_POST['id_number'];
								$person_obj->updatePatientID($_POST['pid'], $id_number, $type);
							}elseif ($aFormValues['subservice_code'][0]=='VET'){
								$type = 'VET';
								$id_label = 'Veteran ID';
								$id_number = $_POST['id_number'];
								$person_obj->updatePatientID($_POST['pid'], $id_number, $type);
							}

							$objResponse->assign("smss_no", "innerHTML", $mss_no);
							$objResponse->assign("mssno", "value", $mss_no);
							$objResponse->assign("can_classify", "value", 1);
							$objResponse->assign("senior_row","style.display","");
							$objResponse->assign("tdsenior1", "innerHTML", $id_label);
							$objResponse->assign("tdsenior2", "innerHTML", $id_number);
						}
					}else{
						if ($aFormValues['subservice_code'][0]=='SC'){
							$type = 'SC';
							$id_label = 'Senior Citizen ID';
							$id_number = $_POST['id_number'];
							$person_obj->updatePatientID($_POST['pid'], $id_number, $type);
						}elseif ($aFormValues['subservice_code'][0]=='VET'){
							$type = 'VET';
							$id_label = 'Veteran ID';
							$id_number = $_POST['id_number'];
							$person_obj->updatePatientID($_POST['pid'], $id_number, $type);
						}
						$objResponse->assign("senior_row","style.display","");
						$objResponse->assign("tdsenior1", "innerHTML", $id_label);
						$objResponse->assign("tdsenior2", "innerHTML", $id_number);
					}
					#--------------------------------

					//$objResponse->alert(print_r($_POST));
					#$bolSuccess = $objSS->saveSSCData($_POST);	//save classification including the modifiers
					#edited by VAN 05-13-08

					#edited by VAN 05-23-2009
					$encInfo = $enc_obj->getEncounterInfo($aFormValues['encounter_nr']);
					#discount id will be save based on the encounter nr when patient is in ER or IPD
					#if ($aFormValues['encounter_nr']){

					if (($aFormValues['encounter_nr']) &&(($encInfo['encounter_type']==1)||($encInfo['encounter_type']==3)||($encInfo['encounter_type']==4))){
						$bolSuccess = $objSS->saveSSCData($_POST);	//save classification including the modifiers
						if($encInfo['encounter_type']==1){
                    		$bolSuccess = $objSS->saveSSCDataByPID($_POST);
                    	}
					}else{
						$bolSuccess = $objSS->saveSSCDataByPID($_POST);	//save classification including the modifiers
					}

					$objEnc = new Encounter;
				    $result = $objEnc->getSaveBilling($aFormValues['encounter_nr']);
				    if ($result) {
				        $row = $result->FetchRow();
				        $billnr = $row['bill_nr'];
				    }
				    $discount = $objSS->getDiscountByEncounter($aFormValues['encounter_nr']);
				    $discountid = $discount['discountid'];
				    $discountrate = $discount['discount'];

				    $discount_amount = 0.00;
					if($billnr){
						$bolSuccess = $objSS->saveBillDiscountAmount($billnr, $discountid, $discountrate, $discount_amount);
					}

					#edited by VAN 09-23-08
										#commented by VAN 05-25-09
					#$bolSuccess = $objSS->saveSSCDataByPID($_POST);	//save classification including the modifiers

					#$objResponse->alert('sql = '.$objSS->sql);

					#added by VAN 11-19-09
					if ($bolSuccess){
							$is_show = 0;
							if ($_POST['discountid']=='LINGAP')
								$is_show = 1;
							#$objResponse->call("showLingap",$is_show);
							if ($is_show){
									$objResponse->assign("lingap_row","style.display","");
							}else{
									$objResponse->assign("lingap_row","style.display","none");
							}
					#-----------------------------

                        #show apply bill btn
                        if (($_POST['discountid'])&&(($aFormValues['encounter_nr']) &&(($encInfo['encounter_type']==1)||($encInfo['encounter_type']==3)||($encInfo['encounter_type']==4))))
                            $objResponse->assign("applybill_row","style.display","");
                        else
                            $objResponse->assign("applybill_row","style.display","none");

					}

				}
			}
		break;
		case 'lcr': // Use this for  updating personal profile
			#$objResponse->alert('here');
			if($aFormValues['occupation_select'][0] == ''){
				$objResponse->alert("Please select educational attainment.");
			}

			if(!$bError){
				if(!$aFormValues['hauz_lot'])
					$aFormValues['hauz_lot'] = 0;

				#added by VAN 05-10-08
				$ssArray['informant_name'] = addslashes($aFormValues['resp']); // respondent
				$ssArray['relation_informant'] = addslashes($aFormValues['relation']);  // relation to patient
				$ssArray['educational_attain'] = $aFormValues['occupation_select'][0]; //informant
				$ssArray['source_income'] = addslashes($aFormValues['s_income']);   // source of income
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

				#Added by Cherry 07-20-10
				$ssArray['address'] = $aFormValues['address'];
				$ssArray['house_type'] = $aFormValues['hauz_lot_type'];

				#added by VAN 07-26-08
				$ssArray['nr_children'] = $aFormValues['nr_chldren'];  // monthly income
				$ssArray['per_capita_income'] = $aFormValues['m_cincome'];  // monthly income

					#added by Daryl 10/17/2013
				$ssArray['monthly_income_remarks'] = $aFormValues['monthly_income_remarks'];  // monthly income
				$ssArray['monthly_expenses_remarks'] = $aFormValues['monthly_expenses_remarks'];  // monthly income
				

				#------------
				$details->address = $ssArray['address'];
				$details->house_type = $ssArray['house_type'];
				$details->informant_name = $ssArray['informant_name'];

				#$objResponse->alert('sar = '.$ssArray['address']);
				#$objResponse->alert('sd = '.print_r($details,true));
				#-------------

				$glob_obj->getConfig('mss_%');

				$socInfo = $objSS->getSocServPatient($aFormValues['pidNr']);
				#$objResponse->alert("sql = ".$objSS->sql);
				$is_exists = $objSS->count;

				if ($is_exists){
					#$objResponse->alert("exists");
					$mss_no = $socInfo['mss_no'];
					#$mode = 'update';
				}else{
					#get new mss no
					$mss_no = $objSS->getLastMSSnr(date("Y-m-d"),"'".$GLOBAL_CONFIG['mss_nr_init']."'");
					#$mode = 'save';
					$ssArray['mss_no'] = $mss_no;
					$ok = $objSS->saveSocialPatientArray(&$ssArray);

					if ($ok){
						$objResponse->alert("MSS No. of patient with HRN ".$aFormValues['pidNr']." is successfully created");
						$objResponse->assign("smss_no", "innerHTML", $mss_no);
						$objResponse->assign("can_classify", "value", 1);
						$objResponse->assign("mssno", "value", $mss_no);
					}
				}

				$ssArray['mss_no'] = $mss_no; //modify id;

				//$socservInfo = $objSS->getSocialServPatient($mss_no);
				$socservInfo = $objSS->getSocialServPatient($mss_no, 1, $aFormValues['encNr']);
				#$objResponse->alert("sql = ".$objSS->sql);

				if ($objSS->count)
					$mode = 'update';
				else
					$mode = 'save';

				#$objResponse->alert("mode = ".$mode);

				if ($mode=='save'){

					#$objResponse->alert("sql1 = ".$objSS->sql);
					$ok2 = $objSS->saveSocServPatientArray(&$ssArray);
					#$objResponse->alert("sql2 = ".$objSS->sql);

					if ($ok2)
						$bolSuccess = true;
					else
						$bolSuccess = false;

				}elseif ($mode=='update'){
					$objResponse->call("assignValue",$details);
					$ssArray['modify_time'] = date('Y-m-d H:i:s'); //modify date
					$ssArray['modify_id'] = $aFormValues['encoderName']; //modify id;

					$ok2 = $objSS->updateSocServPatientArray($mss_no, $aFormValues['encNr'], &$ssArray);
					#$objResponse->alert("sql2 = ".$objSS->sql);
					if ($ok2)
						$bolSuccess = true;
					else
						$bolSuccess = false;
				}
			}

			if (!$bolSuccess){
					$objResponse->assign("can_classify", "value", 0);
					$objResponse->alert('Profile has NOT been successfully saved');
			}

		break;
        case 'ssp': // added by Jarel 03-04-2013

           if(!$bError){
                    if($aFormValues['subservice_code']=='' && $aFormValues['service_code*']==''){
                        $ssResult= $objSS->getSSInfo($aFormValues['additional_support'],'','');
                    }elseif ($aFormValues['subservice_code']==''){
                        $ssResult= $objSS->getSSInfo($aFormValues['service_code*'],'','');
                    }else{
                        $ssResult= $objSS->getSSInfo($aFormValues['subservice_code'],'','');
                    }

                if($ssResult){
                    $ssRow = $ssResult->FetchRow();
                    if ($aFormValues['subservice_code']=='OT')
                        $_POST['other_name'] =$aFormValues['other_row'];
                    else
                        $_POST['other_name'] ='';

                    if ($aFormValues['subservice_code']=='SC')
                        $_POST['id_number'] = $aFormValues['id_no'];
                    else
                        $_POST['id_number'] = '';

                    $_POST['discountid'] =$ssRow['discountid'];
                    $_POST['discount'] = $ssRow['discount'];

                    $_POST['encounter_nr'] = $aFormValues['encounter_nr'];
                    $_POST['grant_dte'] = date('Y-m-d H:i:s');
                    $_POST['sw_nr'] = $aFormValues['encoder_id'];

                    if($aFormValues['modifier_select']==1)
                        $_POST['personal_circumstance'] = $aFormValues['sub_modifier_select'];
                    elseif($aFormValues['modifier_select']==2 )
                        $_POST['community_situation']  = $aFormValues['sub_modifier_select'];
                    else
                        $_POST['nature_of_disease'] = $aFormValues['sub_modifier_select'];

                    $_POST['pid'] = $aFormValues['pid'];
                }

                #Personal Details field
                $ssArray['date_interview'] = $aFormValues['interview_date'];
                $ssArray['status'] = $aFormValues['civil_status'];
                $ssArray['religion'] = $aFormValues['religion'];
                $ssArray['address'] = addslashes($aFormValues['address*']);
                $ssArray['companion'] = $aFormValues['companion'];
                $ssArray['contact_no'] = $aFormValues['contact_number'];
                $ssArray['educational_attain'] = $aFormValues['educ_select'];
                $ssArray['occupation'] = $aFormValues['occupation'];
                $ssArray['other_occupation'] = $aFormValues['ot_occu'];
                $ssArray['informant_name'] = addslashes($aFormValues['resp*']);
                $ssArray['relation_informant'] = addslashes($aFormValues['relation*']);
                $ssArray['info_address'] = addslashes($aFormValues['informant_address*']);
                $ssArray['employer'] = addslashes($aFormValues['employer']);
                $ssArray['employer_address'] = addslashes($aFormValues['employer_address']);

                #Monthly Income
                $ssArray['nr_dependents'] = $aFormValues['nr_dep*'];
                $ssArray['source_income'] = addslashes($aFormValues['other_source_income']);
                $ssArray['income'] = addslashes($aFormValues['m_income2*']);
                $ssArray['other_income'] = $aFormValues['other_income'];
                $ssArray['monthly_income'] = $aFormValues['total_income'];
                $ssArray['per_capita_income'] = $aFormValues['capita_income'];
                $ssArray['monthly_income_remarks'] = $aFormValues['monthly_income_remarks*'];


                #Monthly Expenses
                $ssArray['house_type'] = $aFormValues['living'];
                $ssArray['light_source'] = $aFormValues['light_source'];
                $ssArray['ligth_expense'] = $aFormValues['light_amount'];
                $ssArray['hauz_lot_expense'] = $aFormValues['living_amount'];
                $ssArray['water_source'] = $aFormValues['water*'];
                $ssArray['fuel_source'] = $aFormValues['fuel'];
                $ssArray['water_expense'] = $aFormValues['water_amount'];
                $ssArray['fuel_expense'] = $aFormValues['fuel_amount'];
                $ssArray['food_expense'] = $aFormValues['food_amount'];
                $ssArray['househelp_expense'] = $aFormValues['househelp_amount'];
                $ssArray['education_expense'] = $aFormValues['educ_amount'];
                $ssArray['med_expenditure'] = $aFormValues['medical_amount'];
                $ssArray['clothing_expense'] = $aFormValues['clothing_amount'];
                $ssArray['insurance_mortgage'] = $aFormValues['plan_amount'];
                $ssArray['transport_expense'] = $aFormValues['trans_amount'];
                $ssArray['other_expense'] = $aFormValues['others_amount'];
                $ssArray['total_monthly_expense'] = $aFormValues['total_expenses'];
                $ssArray['monthly_expenses_remarks'] = $aFormValues['monthly_expenses_remarks*'];
              


                #Medical Data
                $ssArray['final_diagnosis'] =addslashes($aFormValues['final_diagnosis']);
                $ssArray['duration_problem'] = addslashes($aFormValues['duration_prob']);
                $ssArray['duration_treatment'] = addslashes($aFormValues['prev_treatment']);
                $ssArray['treatment_plan'] = addslashes($aFormValues['present_treatment']);
                $ssArray['accessibility_problem'] = addslashes($aFormValues['health_access']);

                #Referral
                $ssArray['source_referral'] = $aFormValues['source_referral'];
                $ssArray['name_referral'] = $aFormValues['name_referral'];
                $ssArray['info_agency'] = addslashes( $aFormValues['name_address']);
                $ssArray['info_contact_no'] = $aFormValues['referral_number'];
                $ssArray['remarks'] = addslashes( $aFormValues['remarks']);
                $ssArray['social_worker'] = $aFormValues['social_worker'];
	        $ssArray['is_poc'] = $aFormValues['is_poc'];

                $ssArray['pid'] = $aFormValues['pid'];
                $ssArray['encounter_nr'] = $aFormValues['encounter_nr'];


                $details->address = $ssArray['address'];
                $details->house_type = $ssArray['house_type'];
                $details->informant_name = $ssArray['informant_name'];
                $details->discountid = $ssRow['discountid'];

                $glob_obj->getConfig('mss_%');

                $socInfo = $objSS->getSocServPatient($aFormValues['pid']);
                $is_exists = $objSS->count;

                if ($is_exists){
                    $mss_no = $socInfo['mss_no'];
                    if ($aFormValues['subservice_code']=='SC'){
                            $type = 'SC';
                            $id_label = 'Senior Citizen ID';
                            $id_number = $_POST['id_number'];
                            $person_obj->updatePatientID($_POST['pid'], $id_number, $type);
                        }elseif ($aFormValues['subservice_code']=='VET'){
                            $type = 'VET';
                            $id_label = 'Veteran ID';
                            $id_number = $_POST['id_number'];
                            $person_obj->updatePatientID($_POST['pid'], $id_number, $type);
                        }
                        $details->scLabel = $id_label;
                        $details->scNumber = $id_number;
                        $objResponse->call("assignValue",1,$details);
                }else{
                    $mss_no = $objSS->getLastMSSnr(date("Y-m-d"),"'".$GLOBAL_CONFIG['mss_nr_init']."'");
                    $ssArray['mss_no'] = $mss_no;
                    $ok = $objSS->saveSocialPatientArray(&$ssArray);

                    if ($ok){
                            if ($aFormValues['subservice_code']=='SC'){
                                $type = 'SC';
                                $id_label = 'Senior Citizen ID';
                                $id_number = $_POST['id_number'];
                                $person_obj->updatePatientID($_POST['pid'], $id_number, $type);
                            }elseif ($aFormValues['subservice_code']=='VET'){
                                $type = 'VET';
                                $id_label = 'Veteran ID';
                                $id_number = $_POST['id_number'];
                                $person_obj->updatePatientID($_POST['pid'], $id_number, $type);
                            }
                             $objResponse->alert("MSS No. of patient with HRN ".$aFormValues['pid']." is successfully created");
                             $details->scLabel = $id_label;
                             $details->scNumber = $id_number;
                             $details->mss = $mss_no;
                             $details->canClassify = 1;
                             $objResponse->call("assignValue",2,$details);
                    }
                }
                $ssArray['mss_no'] = $mss_no;
                $socservInfo = $objSS->getSocialServPatient($mss_no, 1, $aFormValues['encounter_nr']);

                if ($objSS->count)
                    $mode = 'update';
                else
                    $mode = 'save';

                if ($mode=='save'){
                    $ok2 = $objSS->saveSocServPatientArray(&$ssArray);
                    if ($ok2)
                        $bolSuccess = true;
                    else
                        $bolSuccess = false;

                }elseif ($mode=='update'){
                    $objResponse->call("assignValue",$details);
                    $ssArray['modify_time'] = date('Y-m-d H:i:s'); //modify date
                    $ssArray['modify_id'] = $aFormValues['encoderName']; //modify id;

                    $ok2 = $objSS->updateSocServPatientArray($mss_no, $aFormValues['encounter_nr'], &$ssArray);
                    if ($ok2)
                        $bolSuccess = true;
                    else
                        $bolSuccess = false;
                }

                $encInfo = $enc_obj->getEncounterInfo($aFormValues['encounter_nr']);

                if (($aFormValues['encounter_nr'] && $aFormValues['encounter_nr']!='0') &&(($encInfo['encounter_type']==1)||($encInfo['encounter_type']==3)||($encInfo['encounter_type']==4))){
                    $bolSuccess = $objSS->saveSSCData($_POST);    //save classification including the modifiers
                    if($encInfo['encounter_type']==1){
                    	$bolSuccess = $objSS->saveSSCDataByPID($_POST);
                    }
                }else{
                    $bolSuccess = $objSS->saveSSCDataByPID($_POST);    //save classification including the modifiers
                }
               
                    $objEnc = new Encounter;
				    $result = $objEnc->getSaveBilling($aFormValues['encounter_nr']);
				    if ($result) {
				        $row = $result->FetchRow();
				        $billnr = $row['bill_nr'];
				    }
				    $discount = $objSS->getDiscountByEncounter($aFormValues['encounter_nr']);
				    $discountid = $discount['discountid'];
				    $discountrate = $discount['discount'];

				    $discount_amount = 0.00;
				if($billnr){
					$bolSuccess = $objSS->saveBillDiscountAmount($billnr, $discountid, $discountrate, $discount_amount);
				}    
				    

            }

            if (!$bolSuccess){
                $objResponse->call("assignValue",3,$details);
                //$objResponse->assign("can_classify", "value", 0);
                $objResponse->alert('Profile has NOT been successfully saved');
            }else{
                $is_show = 0;
                if ($_POST['discountid']=='LINGAP')
                    $is_show = 1;

                if ($is_show){
                        $objResponse->call("assignValue",4,$details);
                        //$objResponse->assign("lingap_row","style.display","");
                }else{
                        $objResponse->call("assignValue",5,$details);
                       //$objResponse->assign("lingap_row","style.display","none");
                }

                if (($_POST['discountid'])&&(($aFormValues['encounter_nr']) &&(($encInfo['encounter_type']==1)||($encInfo['encounter_type']==3)||($encInfo['encounter_type']==4))))
                    $objResponse->call("assignValue",6,$details);
                    //$objResponse->assign("applybill_row","style.display","");
                else
                    $objResponse->call("assignValue",7,$details);
                    //$objResponse->assign("applybill_row","style.display","none");
            }

        break;
	}// end switch
	if($bolSuccess){

        if($listType == 'ssl'){

			$objResponse->script('cf.reload()');
			$objResponse->script('prof.reload()');

			$encInfo = $enc_obj->getEncounterInfo($encounter_nr);
			$grant_by_enc = 0;
			if (($encounter_nr)&&(($encInfo['encounter_type']==1)||($encInfo['encounter_type']==3)||($encInfo['encounter_type']==4)))
				$grant_by_enc = 1;

			$sslist = $objSS->getSSCInfo($aFormValues['encounter_nr'], $aFormValues['pid'],$grant_by_enc,0,10,'sg.grant_dte DESC',1);

			if ($objSS->count){
				$objResponse->assign("withrec","value",1);
				$objResponse->assign("personalMod","style.display","");
				$objResponse->assign("communityMod","style.display","");
				$objResponse->assign("diseaseMod","style.display","");
			}else{
				$objResponse->assign("withrec","value",0);
				$objResponse->assign("personalMod","style.display","none");
				$objResponse->assign("communityMod","style.display","none");
				$objResponse->assign("diseaseMod","style.display","none");
			}

			$objResponse->assign("subclass","style.display","none");
			$objResponse->assign("other_text","style.display","none");
			$objResponse->assign("subID","style.display","none");
			$objResponse->assign("subservice_code2","value","");
			$objResponse->assign("idnumber","value","");
			$objResponse->assign("discountId2","value",$_POST['discountid']);
		}elseif($listType == 'ssp'){
            $objResponse->script('window.parent.cf.reload()');
            $objResponse->script('window.parent.prof.reload()');

            if ($mode=='save'){
				$objResponse->alert("Profile has been successfully created");
			}else{
				$objResponse->alert("Profile has been successfully updated");
			}

            if($aFormValues['encounter_nr'] == $aFormValues['parent_enc'] || $aFormValues['parent_enc'] == '0'){
                $objResponse->call("assignValue",8,$details);
            }else{
                $objResponse->call("assignValue",3,$details);
            }


		}else{
            $objResponse->script('cf.reload()');
            $objResponse->script('prof.reload()');

            if ($mode=='save'){
                $objResponse->alert("Profile has been successfully created");
            }else{
                $objResponse->alert("Profile has been successfully updated");
            }
            $objResponse->assign("can_classify", "value", 1);
        }
	}else{

		if ($bError!=TRUE){
			// TODO:  change this message alert for appropriate error.
		if($listType == 'ssl'){
			#$objResponse->alert("Saving Data failed: SQL->". $objSS->sql);
			#$objResponse->alert("You are not allowed to update the profile.");
		}else{
			#$objResponse->alert("Saving Data failed: SQL->". $sql_update);
			#$objResponse->alert("You are not allowed to update the profile.");
		}
		}
	}

	return $objResponse;
}//end of function AddSSc()

//Added by Cherry 07-23-10
function disableReadonlysegSocservPatient(){
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;
	$objResponse->alert("csdnfasdfa");

	//$objResponse->assign("resp", "readonly", FALSE);

	return $objResponse;
}
//end Cherry

//Added by Cherry 07-23-10
function ViewSocServPatient($encounter_nr){
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;
	#$objResponse->alert('dvdvd vjdfvkjadsvdas');
	$info = $objSS->getSocServPatientThroughEncounter($encounter_nr);
	//$objResponse->alert($info['house_type']);
	//$objResponse->alert($this->sql);
	//$objResponse->alert($info['address']);
	if($info){
		if($info['nr_dependents']=='0')
			$per_capita_income = $info['monthly_income'];
		else
			$per_capita_income = $info['monthly_income'] / $info['nr_dependents'];
		$tot_monthly_expense = $info['hauz_lot_expense'] + $info['food_expense'] + $info['water_expense'] + $info['transport_expense'] + $info['other_expense']+$info['ligth_expense'];
		 //number_format($objphysician->profcharges,2,'.',',')
		//$objResponse->assign('address','innerHTML', $info['address']);
//		$objResponse->assign("resp", "value", $info['informant_name']);
//		$objResponse->assign("relation", "value", $info['relation_informant']);
//		$objResponse->assign("hauz_lot2", "value", number_format($row['hauz_lot_expense'],2,'.',','));
//		$objResponse->assign("food2", "value", number_format($info['food_expense'],2,'.',',') );
//		$objResponse->assign("nr_dep", "value", $info['nr_dependents']);
//		$objResponse->assign("nr_chldren", "value", $info['nr_children']);
//		$objResponse->assign("water2", "value", number_format($info['water_expense'],2,'.',',') );
//		$objResponse->assign("s_income", "value", $info['source_income']);
//		$objResponse->assign("transport2", "value", number_format($info['transport_expense'],2,'.',','));
//		$objResponse->assign("m_income2", "value", number_format($info['monthly_income'],2,'.',','));
//		$objResponse->assign("other2", "value", number_format($info['other_expense'],2,'.',',') );
//		$objResponse->assign("m_capita_income", "value", number_format($per_capita_income,2,'.',','));
//		$objResponse->assign("light2", "value",  number_format($info['ligth_expense'],2,'.',','));
//		$objResponse->assign("m_expenses", "value", number_format($tot_monthly_expense,2,'.',','));

//		#$objResponse->assign("hauz_lot_type", "checked", $info['house_type']);
//		$objResponse->assign("setHauzRadio");

//		$objResponse->assign("occupation_select", "selected", $info['educational_attain']);
//		$objResponse->assign("occupation_select", "disabled", 1);
			$details = (object) 'details';
			$details->address = $info['address'];
			$details->resp = $info['informant_name'];
			$details->relation = $info['relation_informant'];
			$details->occupation_select = $info['educational_attain'];
			$details->hauz_lot2 = $info['hauz_lot_expense'];
			$details->food2 = $info['food_expense'];
			$details->nr_dep = $info['nr_dependents'];
			$details->nr_chldren = $info['nr_children'];
			$details->water2 = $info['water_expense'];
			$details->s_income = $info['source_income'];
			$details->transport2 = $info['transport_expense'];
			$details->m_income2 = $info['monthly_income'];
			$details->other2 = $info['other_expense'];
			$details->m_capita_income = $per_capita_income;
			$details->light2 = $info['ligth_expense'];
			$details->m_expenses = $tot_monthly_expense;
			$details->hauz_lot_type = $info['house_type'];


			$objResponse->call("setDataValues",$details);

	}

	return $objResponse;
}
//end Cherry


//Added by Cherry 07-21-10
function RemoveSocServPatient($encounter_nr){
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;
	#$objResponse->alert('haller= '.$encounter_nr);

	$remove = $objSS->deleteSocialServPatient($encounter_nr);
	#$objResponse->alert("remove= ".$remove);
	if($remove){
		$objResponse->alert('Successfully Deleted');
		$objResponse->assign("can_classify", "value", 0);
        $objResponse->assign("intake", "disabled", false);
	}
	else{
		$objResponse->alert('Profile Not deleted');
		$objResponse->assign("can_classify", "value", 1);
        $objResponse->assign("intake", "disabled", true);
	}
	 $objResponse->script('prof.reload()');
	return $objResponse;
}
//End Cherry

function PopulateSSC($encounter_nr,$pid, $reclassify, $listtype){
	global $date_format, $db;
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;

	#added by VAN 07-02-08
	$enc_obj=new Encounter;

	#$objResponse->addAlert('listtype, encountern, pid = '.$listtype." - ".$encounter_nr." - ".$pid);
	#$objResponse->addAlert('pid = '.$reclassify);
	switch ($listtype){
		case 'ssl':

			#GetProfile($objResponse, $encounter_nr); // get profile
			#added by VAN 05-12-08
			GetProfile($objResponse, $encounter_nr, $pid); // get profile

			#$sslist = $objSS->getSSCInfo($encounter_nr);

			#edited by VAN 05-25-09
			$encInfo = $enc_obj->getEncounterInfo($encounter_nr);
			$grant_by_enc = 0;
			if (($encounter_nr)&&(($encInfo['encounter_type']==1)||($encInfo['encounter_type']==3)||($encInfo['encounter_type']==4)))
			    $grant_by_enc = 1;

			#edited by VAN 05-13-08
			$sslist = $objSS->getSSCInfo($encounter_nr, $pid, $grant_by_enc, 0,10,'sg.grant_dte DESC',1);
			#$objResponse->alert("sql = ".$objSS->sql);

			if ($objSS->count){
				$objResponse->assign("withrec","value",1);
				$objResponse->assign("personalMod","style.display","");
				$objResponse->assign("communityMod","style.display","");
				$objResponse->assign("diseaseMod","style.display","");

				#$objResponse->assign("subclass","style.display","none");
			}else{
				$objResponse->assign("withrec","value",0);
				$objResponse->assign("personalMod","style.display","none");
				$objResponse->assign("communityMod","style.display","none");
				$objResponse->assign("diseaseMod","style.display","none");
			}

			$objResponse->assign("subclass","style.display","none");
			#added by VAN 08-05-08
			$objResponse->assign("other_text","style.display","none");
			$objResponse->assign("subID","style.display","none");
			$objResponse->assign("subservice_code2","value","");
			$objResponse->assign("idnumber","value","");
			#$objResponse->assign("personal_circumstance","selected","0");
			#$objResponse->assign("community_situation","selected","0");
			#$objResponse->assign("nature_of_disease","selected","0");
			//$objResponse->alert("sslist->RecordCount() = ".$sslist->RecordCount()." \n sslist = " .print_r($sslist, true));

			if($sslist){

				$objResponse->assign("classification", "innerHTML", $tblesrc);
				$objResponse->assign("discountId", "value", $discountId);
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
					#commented by VAN 07-05-08
					#if (!(empty($discountId))&&(($discountId!='C3')&&($ssInfo['parentid']!='C3')))
					#	$objResponse->assign("show", "style.display","none");
				}

			}else{
				$tr = "<tr><td colspan=\"5\">No classification exists</td></tr>";
				$objResponse->addAssign("ssctbody", "innerHTML", $tr);
			}
					break;
		case 'lcr':
			//TODO : CHANGE the query of the function  getLCRInfo($encounter_nr){
			$lcrlist = $objSS->getLCRInfo($encounter_nr);
			$encInfo = $enc_obj->getEncounterInfo($encounter_nr);
			#$objResponse->alert("sql = ".$objSS->sql);
			#$objResponse->addAlert('hello ajax ='.$encInfo['encounter_type']);

            #added by VAN 08-15-08
			if ($encInfo['encounter_type']!=2){
				$objResponse->assign("show_billing", "disabled", FALSE);
				$objResponse->assign("show_billing_with_discount", "disabled", FALSE);
				$objResponse->assign("applyDiscount", "disabled", FALSE);

                $sql_d = "SELECT discount_amnt FROM seg_charity_grants WHERE encounter_nr=".$db->qstr($encounter_nr);
                $fixed_discount = $db->GetOne($sql_d);

			    if (($fixed_discount!='0.00') && ($fixed_discount!=NULL))
                    $objResponse->assign("discardDiscount", "disabled", FALSE);
                else
                    $objResponse->assign("discardDiscount", "disabled", TRUE);
            }else{
				$objResponse->assign("show_billing", "disabled", TRUE);
				$objResponse->assign("show_billing_with_discount", "disabled", TRUE);
				$objResponse->assign("applyDiscount", "disabled", TRUE);
                $objResponse->assign("discardDiscount", "disabled", TRUE);
            }
		break;
	}

	return $objResponse;
}

function GetProfile(&$objResponse,$enc, $pid){
	global $db;

	 #edited by VAN 07-04-08
	$fld_enc = "";
	$sql_enc = "";
	if ($enc){
				$fld_enc = " , enc.encounter_type, enc.encounter_nr, enc.er_opd_diagnosis ";
				$sql_enc = " LEFT JOIN care_encounter AS enc ON enc.encounter_nr='$enc' ";
		}
		$sql  = "SELECT m.pid,m.mss_no AS mssno, p.*, e.educ_attain_name $fld_enc
			 FROM seg_social_patient AS m
			 LEFT JOIN seg_socserv_patient AS p ON m.mss_no=p.mss_no
			 LEFT JOIN seg_educational_attainment AS e ON e.educ_attain_nr=p.educational_attain
						 $sql_enc
			 WHERE m.pid = '$pid'";

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
					$er_opd_diagnosis = "Admitting diagnosis is not specified.";
				elseif ($row['er_opd_diagnosis'])
					$er_opd_diagnosis = $row['er_opd_diagnosis'];

			/*
								if (trim($row['er_opd_diagnosis']))
										$er_opd_diagnosis = $row['er_opd_diagnosis'];
								else
										$er_opd_diagnosis = "Admitting diagnosis is not specified.";
				*/
				#$objResponse->call("setProfile",$row['er_opd_diagnosis'], $row['respondent'],$row['relation'],$row['occupation_name'] ,
				#					$row['source_income'] , $row['monthly_income'], $row['nr_dependents'] );
				/*
				$objResponse->call("setProfile",$er_opd_diagnosis, $row['respondent'],$row['relation'],$row['occupation_name'] ,
									$row['source_income'] , $row['monthly_income'], $row['nr_dependents'] );
				*/
				#$objResponse->alert("children - ". $row['nr_children']);
				#$objResponse->alert("informant - ". print_r($row,true));
				#edited by VAN
				#$expenses = $row['hauz_lot_expense'] + $row['food_expense'] + $row['ligth_expense'] + $row['water_expense'] + $row['transport_expense'] + $row['other_expense'];
				$objResponse->call("setProfile",$er_opd_diagnosis, $row['informant_name'],$row['relation_informant'],$row['educ_attain_name'] ,
									$row['source_income'] , $row['monthly_income'], $row['nr_dependents'], $row['hauz_lot_expense'],
									 $row['food_expense'], $row['ligth_expense'], $row['water_expense'], $row['transport_expense'], $row['other_expense'], $row['mssno'],$row['per_capita_income'],$row['nr_children']);

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
	$objResponse->call("js_AddOptions", "service_code", "Select Classification",0,"b");
	if($result){
		while($row=$result->FetchRow()){
			$objResponse->call("js_AddOptions","service_code",$row['discountdesc'],$row['discountid'], "b");
		}
		#commented by VAN 07-05-08
		/*
		if($discountId){
			//$objResponse->alert("discountid Option =". $discountId);
			$objResponse->call("setOption_a", "service_code", $discountId);
		}else{
			$objResponse->call("js_AddOptions", "service_code", "-Not Indicated-", "b");
		}
		*/
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

	}else{
		$objResponse->alert("DB failed: ".$objSS->sql);
	}
	//return $objResponse;
}//end of function AddOptions_circumstances

#------------------------

#added by VAN 08-05-08
function OnChangeSubOptions($code){
	$objResponse = new xajaxResponse();
	#$objResponse->alert($code);
	if (($code=='OT')||($code=='OTHER')){
		#$objResponse->alert($code);
		$objResponse->assign("other_text","style.display","");
		$objResponse->assign("subID","style.display","none");
	}elseif (($code=='SC')||($code=='VET')){
		#$objResponse->alert($code);
		$objResponse->assign("subID","style.display","");
		$objResponse->assign("other_text","style.display","none");
	}else{
		$objResponse->assign("other_text","style.display","none");
		$objResponse->assign("subID","style.display","none");
	}

	return $objResponse;
}
#----------------------------

function OnChangeOptions($code, $encounter_nr,$pid){
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;
		$enc_obj=new Encounter;

	if(!empty($code))
			$result = $objSS->getSSInfo($code);

	#$objResponse->alert($objSS->sql);
	#$objResponse->alert($encounter_nr." , ".$pid);
	if($result){
			//$objResponse->alert("DB failed: ".$objSS->sql);
		$row=$result->FetchRow();
		$objResponse->call("js_SetOptionDesc",'sscDesc' ,$row['discountdesc']);

		#added by VAN 07-04-08
		$rs = $objSS->getSSChildArray($code);
		#$objResponse->alert($objSS->sql);
				$encInfo = $enc_obj->getEncounterInfo($encounter_nr);
				$grant_by_enc = 0;
				if (($encounter_nr)&&(($encInfo['encounter_type']==1)||($encInfo['encounter_type']==3)||($encInfo['encounter_type']==4)))
					 $grant_by_enc = 1;

		if ($objSS->count){
			#edited by VAN 07-26-08
			if($code!='C3'){
				$objResponse->assign("subclass","style.display","");
				$objResponse->assign("subc","value","1");
			}else{
				$sslist = $objSS->getSSCInfo($encounter_nr, $pid, $grant_by_enc);

				if ($objSS->count){
					$objResponse->assign("subclass","style.display","");
					#added by VAN 08-05-08
					if(($code=='C1')||($code=='C2')||($code=='C3'))
										#if($code=='C3')
						$objResponse->assign("subc","value","0");
					else
						$objResponse->assign("subc","value","1");
				}else{
					$objResponse->assign("subclass","style.display","none");
					$objResponse->assign("subc","value","0");
				}
				#$objResponse->assign("subc","value","1");
			}
			$objResponse->call("js_ClearOptions", "subservice_code");
			$objResponse->call("js_AddOptions", "subservice_code", "Select Sub Classification",0,"b");

			if($result){
				while($row=$rs->FetchRow()){
					$objResponse->call("js_AddOptions","subservice_code",$row['discountdesc'],$row['discountid'], "b");
				}
			}else{
				$objResponse->alert("No Selection");
			}
		}else{
			$objResponse->assign("subclass","style.display","none");
			$objResponse->assign("subc","value","0");

			#added by VAN 08-05-08
			$objResponse->assign("other_text","style.display","none");
			$objResponse->assign("subID","style.display","none");
			$objResponse->assign("subservice_code2","value","");
			$objResponse->assign("idnumber","value","");
		}

		#-------------------
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

#Added by Cherry 07-19-10
#edited by VAN 07-20-2010
function populateProfile($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	global $db, $date_format, $HTTP_SESSION_VARS;
	$objResponse = new xajaxResponse();

	$objSS = new SocialService;
	$enc_obj = new Encounter;

	$occurrence = 0; //for disabling "new profile" button
	$encounter_nr = $HTTP_SESSION_VARS['sess_en'];
	$pid = $HTTP_SESSION_VARS['sess_pid'];
	$mssInfo = $objSS->getPatientMSS($pid);
	#$objResponse->alert("encounter_nr= "+$encounter_nr);
	#$objResponse->alert($objSS->sql);
	#$objResponse->alert('please = '.$mssInfo['mss_no']);
	$offset = $page_num * $max_rows;

	$sortColumns = array('ss.create_time','ss.encounter_nr', 'ss.create_id');
	$sort = array();
	if (is_array($sort_obj)) {
		foreach ($sort_obj as $i=>$v) {
			$col = $sortColumns[$i] ? $sortColumns[$i] : "ss.create_time";
			if ((int)$v < 0) $sort[] = "$col DESC";
			elseif ((int)$v > 0) $sort[] = "$col ASC";
		}
	}
	if ($sort) $sort_sql = implode(',', $sort);
	else $sort_sql = 'create_time DESC';

	$mss_no = $mssInfo['mss_no'];

	#$sslist = $objSS->getSSCInfo($encounter_nr, $pid,$grant_by_enc, $offset, $max_rows, $sort_sql, 1, $issc);
	$proflist = $objSS->getProfileInfo($mss_no, $offset, $max_rows, $sort_sql);
	#$objResponse->alert($objSS->sql);

	if($proflist){
		$found_rows = $objSS->FoundRows();
		$last_page = ceil($found_rows/$max_rows)-1;
		if ($last_page < 0) $last_page=0;
		if ($page_num > $last_page) $page_num=$last_page;

		if($data_size=$proflist->RecordCount()) {
			$temp=0;
			$i=0;
			$objResponse->contextAssign('currentPage', $page_num);
			$objResponse->contextAssign('lastPage', $last_page);
			$objResponse->contextAssign('maxRows', $max_rows);
			$objResponse->contextAssign('listSize', $found_rows);

			$DATA = array();
			while($row = $proflist->FetchRow()) {
				//Added by Cherry 07-20-10
				$DATA[$i]['pid'] = $row['pid'];

				#if OPD
				if ($row['encounter_type']){
					if ($row['encounter_type']==2){
						 $rowD =  $objSS->getDiscountByPid($row['pid']);
					}else{
						 $rowD =  $objSS->getDiscountByEncounter($row['encounter_nr']);
					}
					$discountid = $rowD['discountid'];
					$create_date = date("m/d/Y", strtotime($rowD['grant_dte']));
					$encoder = ucwords($rowD['encoder']);
				}else{
					$rowD =  $objSS->getDiscountByPid($row['pid']);
					if ($rowD) {
						$discountid = $rowD['discountid'];
						$create_date = date("m/d/Y", strtotime($rowD['grant_dte']));
						$encoder = ucwords($rowD['encoder']);
					} else {
						$discountid = "";
						$create_date ="";
						$encoder = "";
				}
				}

				$DATA[$i]['create_date'] = $create_date;
				$DATA[$i]['encounter_nr'] = $row['encounter_nr'];

				$DATA[$i]['encoder'] = $encoder;

				//Added by Cherry 07-21-10
				if($row['encounter_nr']==$encounter_nr){
					$occurrence++;
				}
				//End Cherry

				$DATA[$i]['discountid'] = $discountid;    //for classification
				$DATA[$i]['details'] = " ";    //for classification

				$DATA[$i]['FLAG'] = 1;
				$i++;
			} //end while
			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
			#$objResponse->alert(print_r($DATA,true));
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);
		}
				if($occurrence!=0){
					#$objResponse->alert("occurrence= "+$occurrence);
					$objResponse->assign("updateprofile", "disabled", TRUE);
                    $objResponse->assign("intake", "disabled", TRUE);
				}else{
					$objResponse->assign("intake", "disabled", FALSE);
                    $objResponse->assign("updateprofile", "disabled", FALSE);
				}


	}else{
		// error
		#$objResponse->alert($objSS->sql);
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
		$objResponse->assign("updateprofile", "disabled", FALSE);

	}
	$objResponse->script('this.fetchDone()');
	return $objResponse;
}
#End Cherry

function populateClassifications($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	global $db, $date_format,$HTTP_SESSION_VARS;
	$objResponse = new xajaxResponse();

	$objSS = new SocialService;
		$enc_obj=new Encounter;
	#ask alvin for this
	/*
	$encounter_nr = $args[0];
	$pid = $args[1];
	*/
	#edited by VAN 09-23-08
	$encounter_nr = $HTTP_SESSION_VARS['sess_en'];
	$pid = $HTTP_SESSION_VARS['sess_pid'];

	$offset = $page_num * $max_rows;
#$objResponse->alert("DB failed enc: ".$HTTP_SESSION_VARS['sess_en']);
#$objResponse->alert("DB failed pid : ".$HTTP_SESSION_VARS['sess_pid']);
	$sortColumns = array('d.discountid','sg.grant_dte','cp.name_last');
	$sort = array();
	if (is_array($sort_obj)) {
		foreach ($sort_obj as $i=>$v) {
			$col = $sortColumns[$i] ? $sortColumns[$i] : "sg.grant_dte";
			if ((int)$v < 0) $sort[] = "$col DESC";
			elseif ((int)$v > 0) $sort[] = "$col ASC";
		}
	}
	if ($sort) $sort_sql = implode(',', $sort);
	else $sort_sql = 'sg.grant_dte DESC';

		#edited by VAN 05-25-09
		$encInfo = $enc_obj->getEncounterInfo($encounter_nr);
		$grant_by_enc = 0;
		if (($encounter_nr && $encounter_nr !=0 )&&(($encInfo['encounter_type']==1)||($encInfo['encounter_type']==3)||($encInfo['encounter_type']==4))){
				$grant_by_enc = 1;
				$sql_sc = "SELECT discountid FROM seg_charity_grants WHERE encounter_nr=".$encounter_nr." ORDER BY grant_dte DESC LIMIT 1";
		}else{
				$sql_sc = "SELECT discountid FROM seg_charity_grants_pid WHERE pid=".$pid." ORDER BY grant_dte DESC LIMIT 1";
		}

		$result_sc = $db->Execute($sql_sc);
		$row_sc = $result_sc->FetchRow();
		#$objResponse->alert("sql = ".$row_sc['discountid']);
		#if ($row_sc['discountid']=='SC')
	if (($row_sc['discountid']=='SC') || (strstr($row_sc['discountid'],'DMC')))
				$issc = 1;
		else
				$issc = 0;
	 # $objResponse->alert("sql = ".$issc);
	#$sslist = $objSS->getSSCInfo($encounter_nr, $pid, $offset, $max_rows, $sort_sql);
	$sslist = $objSS->getSSCInfo($encounter_nr, $pid,$grant_by_enc, $offset, $max_rows, $sort_sql, 1, $issc);

	#$objResponse->alert("sql = ".$objSS->sql);
	if($sslist) {
		$found_rows = $objSS->FoundRows();
		$last_page = ceil($found_rows/$max_rows)-1;
		if ($last_page < 0) $last_page=0;
		if ($page_num > $last_page) $page_num=$last_page;

		if($data_size=$sslist->RecordCount()) {
			$temp=0;
			$i=0;
			$objResponse->contextAssign('currentPage', $page_num);
			$objResponse->contextAssign('lastPage', $last_page);
			$objResponse->contextAssign('maxRows', $max_rows);
			$objResponse->contextAssign('listSize', $found_rows);

			$DATA = array();
			while($row = $sslist->FetchRow()) {
				$enctype = $row['encounter_type'];
				$ssworker_name = ucfirst($row['name_last']).", ".ucfirst($row['name_first'])." ".$row['name_middle'] ;
				$grant_dte = date("m-d-Y  h:ia",strtotime($row['grant_dte']));

				#added by VAN 05-13-08
				if (is_numeric(trim($row['pcircumstance']))){
					$pcircumstance_row = $objSS->getPatientModifier(1, trim($row['pcircumstance']));
					$pcircumstance_display = trim($pcircumstance_row['mod_subdesc']);
					$mod1 = array($row['pcircumstance'], $pcircumstance_display);
				}	else {
					$pcircumstance_display = trim($row['pcircumstance']);
					$mod1 = array('', $pcircumstance_display);
				}

				if ( is_numeric( trim( $row['csituation'] ) ) ){
					$csituation_row = $objSS->getPatientModifier(2, trim($row['csituation']));
					$csituation_display = trim($csituation_row['mod_subdesc']);
					$mod2 = array($row['csituation'], $csituation_display);
				} else {
					$csituation_display 	= trim($row['csituation']);
					$mod2 = array('', $csituation_display);
				}

				if ( is_numeric(trim($row['ndesease'])) ){
					$ndesease_row = $objSS->getPatientModifier(3, trim($row['ndesease']));
					$ndesease_display = trim($ndesease_row['mod_subdesc']);
					$mod3 = array($row['ndesease'], $ndesease_display);
				} else {
					$ndesease_display = trim($row['ndesease']);
					$mod3 = array('', $ndesease_display);
				}

				if (empty($row['parentid'])) {
					$dsID = $row['discountid'];
				} else {
					if ($row['parentid']!='C3'){
						if (($row['discountid']=='OT')||($row['discountid']=='OTHER'))	$dsID = $row['parentid']." (".$row['other_name'].")";
						else $dsID = $row['parentid']." (".$row['discountdesc'].")";
					}
					else	$dsID = $row['discountdesc'];
				} // else

				$DATA[$i]['encounter_nr'] = $encounter_nr;
				$DATA[$i]['pid'] = $pid;
				$DATA[$i]['timestamp'] = strtotime($grant_dte);

				$DATA[$i]['discount'] = $dsID;
				$DATA[$i]['grant_date'] = $grant_dte;
				$DATA[$i]['personnel'] = $ssworker_name;

				$DATA[$i]['modifier1_code'] = $mod1[0];
				$DATA[$i]['modifier1_text'] = $mod1[1];
				$DATA[$i]['modifier2_code'] = $mod2[0];
				$DATA[$i]['modifier2_text'] = $mod2[1];
				$DATA[$i]['modifier3_code'] = $mod3[0];
				$DATA[$i]['modifier3_text'] = $mod3[1];

				$DATA[$i]['FLAG'] = 1;
				$i++;
			} //end while
			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
			//$objResponse->alert(print_r($DATA,true));
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);
		}

	} else {
		// error
		$objResponse->alert($objSS->sql);
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}

	$objResponse->script('this.fetchDone()');
	return $objResponse;
}

function populateRequests($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;
	$enc_obj=new Encounter;
	global $HTTP_SESSION_VARS, $db;

	#$encounter_nr = $args[0];
	#edited by VAN 09-23-08
	$encounter_nr = $HTTP_SESSION_VARS['sess_en'];
	$pid = $HTTP_SESSION_VARS['sess_pid'];

	$offset = $page_num * $max_rows;
	$sortColumns = array('refno','date_request','dept','total_charge');
	$sort = array();
	if (is_array($sort_obj)) {
		foreach ($sort_obj as $i=>$v) {
			$col = $sortColumns[$i] ? $sortColumns[$i] : "date_request";
			if ((int)$v < 0) $sort[] = "$col DESC";
			elseif ((int)$v > 0) $sort[] = "$col ASC";
		}
	}
	if ($sort) $sort_sql = implode(',', $sort);
	else $sort_sql = 'date_request DESC';

		#get all encounter with a certain patient
		$enc_rs = $enc_obj->getAllEncounterInSS($pid);
		#$objResponse->alert("1 = ".$enc_obj->sql;);
		if ($enc_rs){
					while($row_enc = $enc_rs->FetchRow()) {
						 if ($enc_set)
								$enc_set = $enc_set.",";
						 $enc_set = $enc_set.$row_enc['encounter_nr'];
					}
		}

	#added by VAN 12-15-08
	$isSC = 0;

		#edited by VAN 05-25-2009
		$encInfo = $enc_obj->getEncounterInfo($encounter_nr);
		#$objResponse->addAlert("2 = ".$enc_obj->sql;);
		$grant_by_enc = 0;
		if (($encounter_nr)&&(($encInfo['encounter_type']==1)||($encInfo['encounter_type']==3)||($encInfo['encounter_type']==4)))
				$sql_discount = "SELECT discountid  FROM seg_charity_grants   WHERE encounter_nr='".$encounter_nr."' AND status='valid' ORDER BY grant_dte DESC LIMIT 1";
		else
				$sql_discount = "SELECT discountid  FROM seg_charity_grants_pid   WHERE pid='".$pid."' AND status='valid'  ORDER BY grant_dte DESC LIMIT 1";

	$rs_allow = $objSS->getDiscountAllowWalkin();
	#$objResponse->alert($objSS->sql);

		#$class_allowed = array('SC','DMC','DMCDep','DMCCD','DMCConDep','DMCR','DMCSec','DMH','Ins','DMCPGI');

		 $class_allowed = array();
		 while ($allow_row=$rs_allow->FetchRow()) {
						$class_allowed[] = $allow_row['discountid'];
		 }

	#$objResponse->alert(print_r($class_allowed,true));
		#$objResponse->alert($sql_discount);
		$res_discount=$db->Execute($sql_discount);
	if ($res_discount){
			$discount_info=$res_discount->FetchRow();
		#if ($discount_info['discountid']=='SC')
		if (in_array($discount_info['discountid'],$class_allowed))
			$allowedWKCL = 1;
		}

	#--------------------------

		#$objResponse->alert("set = ".$enc_set);
	#$sslist = $objSS->getLCRInfo($encounter_nr, $offset, $max_rows, $sort_sql);
	$sslist = $objSS->getLCRInfo($enc_set, $offset, $max_rows, $sort_sql, $allowedWKCL,$pid);
	#$objResponse->alert($objSS->sql);
	if($sslist) {
		$found_rows = $objSS->FoundRows();
		$last_page = ceil($found_rows/$max_rows)-1;
		if ($page_num > $last_page) $page_num=$last_page;

		if($data_size=$sslist->RecordCount()) {
			$temp=0;
			$i=0;
			$objResponse->contextAssign('currentPage', $page_num);
			$objResponse->contextAssign('lastPage', $last_page);
			$objResponse->contextAssign('maxRows', $max_rows);
			$objResponse->contextAssign('listSize', $found_rows);

			$DATA = array();
			while($row = $sslist->FetchRow()) {
				#$date_request = date("Y-m-d  h:ia",strtotime($row["time_request"]));
				$date_request = date("m-d-Y",strtotime($row["date_request"]))." ".date("h:ia",strtotime($row["time_request"]));
				$total_charge = number_format($row['total_charge'], 2);

				$DATA[$i]['ref_no'] = $row['refno'];
				$DATA[$i]['request_date'] = $date_request;
				$DATA[$i]['timestamp'] = $row["time_request"];
				#$DATA[$i]['timestamp'] = date("h:ia",strtotime($row["time_request"]));
				$DATA[$i]['total_charge'] = $total_charge;
				$DATA[$i]['dept'] = $row['dept'];
				$DATA[$i]['modifier3_text'] = $mod3[1];
				$DATA[$i]['FLAG'] = 1;

				$i++;
			} //end while
			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
			//$objResponse->alert(print_r($DATA,true));
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);
		}

	} else {
		// error
		#$objResponse->alert($objSS->sql);
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}

	$objResponse->script('this.fetchDone()');
	return $objResponse;
}

#added by VAN 12-08-09
function ajaxApplyBillDiscount($encounter_nr, $amount_to_paid, $frmdte, $bill_date){
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;
    	if (isset($frmdte) && $frmdte)
		$frm_dte = strftime("%Y-%m-%d %H:%M:%S", $frmdte);
	else
		$frm_dte = "0000-00-00 00:00:00";

	if (isset($bill_date) && $bill_date)
			$bill_dte = strftime("%Y-%m-%d %H:%M:%S", $bill_date);
	else
			$bill_dte = "0000-00-00 00:00:00";

	//$objBilling = new Billing($encounter_nr, $bill_dte, $frm_dte);
    //added by jasper 05/10/2013
    $objEnc = new Encounter;
    $result = $objEnc->getSaveBilling($encounter_nr);
    if ($result->RecordCount()) {
        $row = $result->FetchRow();
        $billnr = $row['bill_nr'];
    }
    $discount = $objSS->getDiscountByEncounter($encounter_nr);
    $discountid = $discount['discountid'];
    $discountrate = $discount['discount'];
    //added by jasper 05/10/2013
	global $HTTP_SESSION_VARS, $db;

	#get the total bill of the patient
    $sql = "SELECT fn_billing_compute_net_amount((SELECT bill_nr FROM seg_billing_encounter WHERE encounter_nr='$encounter_nr' and is_deleted IS NULL ORDER BY bill_dte DESC LIMIT 1)) AS bill_amount";
    $bill_amount = $db->GetOne($sql);

    $sql = "SELECT fn_billing_compute_gross_amount((SELECT bill_nr FROM seg_billing_encounter WHERE encounter_nr='$encounter_nr' and is_deleted IS NULL ORDER BY bill_dte DESC LIMIT 1)) AS bill_amount";
    $bill_gross_amount = $db->GetOne($sql);
    #$bill_amount = $rowbill['bill_amount'];
    $discount_amount = ($bill_amount - $amount_to_paid) + ($bill_gross_amount - $bill_amount);
    #check if there is a saved bill
    if ($bill_amount){
	    if ($amount_to_paid <= $bill_amount){
            $rs = $objSS->applyBillDiscount($encounter_nr,$discount_amount);
            //added by jasper 05/10/2013
            $objResponse->alert($billnr ."-". $discountid."-".$discountrate."-".$discount_amount);
            $bSuccess = $objSS->saveBillDiscountAmount($billnr, $discountid, $discountrate, $discount_amount);
            //$objResponse->alert($objSS->sql);
			if ($rs && $bSuccess)
					$objResponse->alert('Discount is successfully applied in the bill.');
			else
					$objResponse->alert('Discount is NOT applied in the bill.');

            $objResponse->assign("discardDiscount", "disabled", FALSE);

	    }else{
			$msg = "Total Bill : ".number_format($bill_amount,2,".",',')." . \n Discount amount must not be greater than the total billing amount.".
						 "\nOnly medicines, laboratory, radiology services can be discounted.".
						 "\nAccomodation and Professional Fees can't be discounted.";
			$objResponse->alert($msg);
	    }
    }else{
        $msg = "There is no SAVED BILL for this encounter. \n Please call Billing Section to save the Billing first or confirmed if the bill is already saved. \n Thank you.";
        $objResponse->alert($msg);
    }
	return $objResponse;
}

function ajaxDiscardBillDiscount($encounter_nr){
    $objResponse = new xajaxResponse();
    $objSS = new SocialService;

    //added by jasper 05/10/2013
    $objEnc = new Encounter;
    $result = $objEnc->getSaveBilling($encounter_nr);
    if ($result->RecordCount()) {
        $row = $result->FetchRow();
        $billnr = $row['bill_nr'];
    }
    $discount = $objSS->getDiscountByEncounter($encounter_nr);
    $discountid = $discount['discountid'];
    $discountrate = $discount['discount'];
    //added by jasper 05/10/2013

    $discount_amount = 0.00;
    $rs = $objSS->applyBillDiscount($encounter_nr,$discount_amount);
    //added by jasper 05/10/2013
    $objResponse->alert($billnr ."-". $discountid."-".$discountrate."-".$discount_amount);
    $bSuccess = $objSS->saveBillDiscountAmount($billnr, $discountid, $discountrate, $discount_amount);
    #$objResponse->alert($objSS->sql);
    if ($rs && $bSuccess)
        $objResponse->alert('Discount is successfully omitted in the bill.');
    else
        $objResponse->alert('Discount is NOT omitted in the bill.');

    $objResponse->assign("discardDiscount", "disabled", TRUE);

    return $objResponse;
}
#------------------------

#Added by Jarel 03-01-13
function addDependent($data,$pid,$enc){
    global $db;
    $objResponse = new xajaxResponse();
    $objSS = new SocialService;

    foreach($data as $key => $value){
        $dep = explode("_",$key);
        $values = "(".$pid.",".$enc.",".$dep[1].",";
        if($dep[0]!='IncomeDep' && $dep[0]!='DepIncome[]'){
            $ids .= "'$value',";
        }elseif($dep[0]=='IncomeDep'){
            $insert .= $values.$ids."'$value'),";
            $ids = '';
        }
    }

    if($objSS->hasSocialFamily($pid,$enc)){
        if($pid!='' && $enc!=''){
            $sql = "DELETE FROM seg_social_patient_family WHERE encounter_nr = $enc";
        }elseif($enc!=''){
            $sql = "DELETE FROM seg_social_patient_family WHERE encounter_nr = $enc";
        }else{
            $sql = "DELETE FROM seg_social_patient_family WHERE pid = $pid";
        }

        if (!$db->Execute($sql)) {
            $objResponse->alert('DB failed: '.$objSS->sql);
        }
    }
    $insert = substr($insert, 0, -1);
    if(!empty($insert))
        $ok = $objSS->saveDependent(&$insert);

    if(!$ok && !empty($insert))
        $objResponse->alert("DB failed: ".$objSS->sql);

    return $objResponse;
}

#Added by Jarel 03-11-13
function removeDepedent($pid,$id){
    global $db;
    $objResponse = new xajaxResponse();
    $objSS = new SocialService;

    $sql="DELETE FROM seg_social_patient_family WHERE pid = '$pid' AND dependent_id = '$id'";

    if ($db->Execute($sql)) {
        //$objResponse->alert('Successfully Deleted.');
    }else{
        $objResponse->alert('DB failed: '.$objSS->sql);
    }
   return $objResponse;
}

#Added by Jarel 03-11-13
function populateDependent($pid,$enc,$mode){
    global $db;
    $objResponse = new xajaxResponse();
    $objSS = new SocialService;
    $details = (object) 'details';
    if($mode=='new'){
        $enc = '';
    }
    $dependent = $objSS->getDependent($pid,$enc);
    if (is_object($dependent)){
        while ($result=$dependent->FetchRow()) {
           $details->id = $result['dependent_id'];
           $details->name = $result['dependent_name'];
           $details->age = $result['dependent_age'];
           $details->status = $result['dependent_status'];
           $details->relation = $result['relation_to_patient'];
           $details->educ = $result['dep_educ_attainment'];
           $details->income = $result['dep_monthly_income'];
           $details->occu = $result['dependent_occupation'];
           $details->dep_nr = $dependent->RecordCount();
           $objResponse->call("populateDependent",$details);
        }
    }else{
        $objResponse->call("populateDependent",null);
    }
    return $objResponse;
}

#Added by Jarel 03-07-13
function getSubClass($id){
    global $db;
    $objResponse = new xajaxResponse();
    $objSS = new SocialService;

    $rs_sectoral = $objSS->getSSChildArray($id);

    $objResponse->call("ajxClearOptions","subservice_code");
    $objResponse->call("ajxAddOption","subservice_code","-Select Sub Classification-",'',0);
    if (is_object($rs_sectoral)){
        while ($result=$rs_sectoral->FetchRow()) {
           $objResponse->call("ajxAddOption","subservice_code",ucwords($result['discountdesc']),$result['discountid'],0);
        }
    }
    return $objResponse;
}

#Added by Jarel 03-07-13
function getSubMod($id){
    global $db;
    $objResponse = new xajaxResponse();
    $objSS = new SocialService;

    $rs_sectoral = $objSS->getModifiers($id);
    $objResponse->call("ajxClearOptions","sub_modifier_select");
    $objResponse->call("ajxAddOption","sub_modifier_select","-Select Sub Modifier-",'',0);
    if (is_object($rs_sectoral)){
        while ($result=$rs_sectoral->FetchRow()) {
            $desc = trim($result['mod_subdesc']);
           $objResponse->call("ajxAddOption","sub_modifier_select",$result['mod_subcode'],$result['mod_subcode'],$desc);
        }
    }
    return $objResponse;
}

#Added by Jarel 03-20-13
function saveSocialFunctioning($data,$pid,$enc,$auto){
    global $db;
    $objResponse = new xajaxResponse();
    $objSS = new SocialService;

    foreach($data as $key => $value){
        $fxn_id = explode("_",$key);
        $values = "(".$pid.",".$enc.",".$fxn_id[0].",";

        if($fxn_id[0]=='txt'){
            $other = $value;
        }elseif(!strpos($key,'coping')){
            $ids .= $value.",";
        }elseif(strpos($key,'coping')){
            $insert .= $values.$ids.$value.",'".$other."'),";
            $ids = '';
            $other = '';
        }
    }

    $insert = substr($insert, 0, -1);
    if($objSS->hasSocialFunctioning($pid,$enc)){
        if($pid!='' && $enc!=''){
            $sql = "DELETE FROM seg_social_functioning_patient WHERE encounter_nr = $enc";
        }elseif($enc!=''){
            $sql = "DELETE FROM seg_social_functioning_patient WHERE encounter_nr = $enc";
        }else{
            $sql = "DELETE FROM seg_social_functioning_patient WHERE pid = $pid";
        }

        if (!$db->Execute($sql)) {
            $objResponse->alert('DB failed: '.$objSS->sql);
        }
    }

    $ok = $objSS->saveSocialFunctioning(&$insert);
    $ok2 = $objSS->saveNoSocialProblem($pid,$enc,$data['no_social_problem']);

    if($ok && $ok2 && !$auto)
       $objResponse->alert("Successfully Save");
   elseif (!$ok && !$ok2)
       $objResponse->alert("DB failed: ".$objSS->sql);

    return $objResponse;
}

#Added by Jarel 03-21-2013
function saveSocialProblem($data,$pid,$enc){
    global $db;
    $objResponse = new xajaxResponse();
    $objSS = new SocialService;

    foreach($data as $key => $value){
        $fxn_id = explode("_",$key);
        $values = "(".$pid.",".$enc.",".$fxn_id[0].",";

        if($fxn_id[0]=='txt'){
            $other = $value;
        }elseif(!strpos($key,'duration')){
            $ids .= $value.",";
        }elseif(strpos($key,'duration')){
            $insert .= $values.$ids.$value.",'".$other."'),";
            $ids = '';
            $other = '';
        }
    }

    $insert = substr($insert, 0, -1);
    if($objSS->hasSocialProblem($pid,$enc)){
        if($pid!='' && $enc!=''){
            $sql = "DELETE FROM seg_social_problems_patient WHERE encounter_nr = $enc";
        }elseif($enc!=''){
            $sql = "DELETE FROM seg_social_problems_patient WHERE encounter_nr = $enc";
        }else{
            $sql = "DELETE FROM seg_social_problems_patient WHERE pid = $pid";
        }

        if (!$db->Execute($sql)) {
            $objResponse->alert('DB failed: '.$objSS->sql);
        }
    }
    $ok = $objSS->saveSocialProblems(&$insert);

    if(!$ok)
       $objResponse->alert("DB failed: ".$objSS->sql);

    return $objResponse;
}

#Added by Jarel 03-21-2013
function saveSocialFindings($data){
    global $db;
    $objResponse = new xajaxResponse();
    $objSS = new SocialService;

    $ssfArray['pid'] = $data['pid'];
    $ssfArray['encounter_nr'] = $data['encounter_nr'];
    $ssfArray['problem_presented'] = substr($data['problem'],0,-1);
    $ssfArray['other_problem'] = $data['other_problem'];
    $ssfArray['counseling_done'] = $data['counseling_done'];
    $ssfArray['topic_concern'] = substr($data['topics'],0,-1);
    $ssfArray['no_reason'] = $data['no_reason'];
    $ssfArray['social_diagnosis'] = $data['social_diagnosis'];
    $ssfArray['intervention'] = $data['intervention'];
    $ssfArray['action_taken'] = $data['action_taken'];
    $ssfArray['remarks'] = $data['fremarks'];

    $ok = $objSS->saveSocialFindings(&$ssfArray);

    if(!$ok)
       $objResponse->alert("DB failed: ".$objSS->sql);

    return $objResponse;

}

#Added by Jarel 03-27-2013
function saveSocialCase($data){
    global $db;
    $objResponse = new xajaxResponse();
    $objSS = new SocialService;

    $ssfArray['pid'] = $data['pid'];
    $ssfArray['encounter_nr'] = $data['encounter_nr'];
    $ssfArray['planning'] = substr($data['planning'],0,-1);
    $ssfArray['provision'] = substr($data['provision'],0,-1);
    $ssfArray['outgoing'] = substr($data['outgoing'],0,-1);
    $ssfArray['incoming'] = substr($data['incoming'],0,-1);
    $ssfArray['leading_reasons'] = substr($data['leading'],0,-1);
    $ssfArray['social_work'] = substr($data['socialworkservices'],0,-1);
    $ssfArray['discharge_services'] = substr($data['dischargeservices'],0,-1);
    $ssfArray['case_con'] = substr($data['cases'],0,-1);
    $ssfArray['follow_up'] = substr($data['followup'],0,-1);
    $ssfArray['coordination'] = substr($data['coordination'],0,-1);
    $ssfArray['documentation'] = substr($data['documentation'],0,-1);
    $ssfArray['others_coordination'] = $data['other_coordination'];
    $ssfArray['others_documentation'] = $data['other_documentation'];
    $ssfArray['remarks'] = $data['cremarks'];
    $ok = $objSS->saveSocialCase(&$ssfArray);

    if($ok)
       $objResponse->alert("Successfully Save");
    else
       $objResponse->alert("DB failed: ".$objSS->sql);

    return $objResponse;

}

function checkEncounterDetails($enc){
	global $db;
    $objResponse = new xajaxResponse();
	$objEnc = new Encounter;
	$enc_Info = $objEnc->getEncounterInfo($enc);
	if($enc_Info['is_discharged']=='1'){
		$objResponse->alert("\t\t\t\tThis patient is already discharged. \n Please call Medical Record to undo discharged enable to update profile.");	
	}else{
		$objResponse->alert("This patient has no transaction.");	
	}
	
	return $objResponse;

}

#Added By Jarel 06/14/2013
function applyConsultation($pid,$sw_nr,$stat){
	global $db;
    $objResponse = new xajaxResponse();
    $objSS = new SocialService;

	if($stat){
		$ok = $objSS->updateConsultationFee($pid);	
	}else{
		$ok1 = $objSS->saveConsultationFee($pid,$sw_nr);
	}

   	if($ok1){
       $objResponse->alert("Successfully Applied Consultation Fee Discount.");
   		$objResponse->assign("consultation","value",1);
   		$objResponse->assign("consultation","innerHTML","Cancel Consultation Fee Discount");
   	}elseif($ok){
   		$objResponse->alert("Successfully Cancel Consultation Fee Discount.");
   		$objResponse->assign("consultation","value",0);
   		$objResponse->assign("consultation","innerHTML","Apply Consultation Fee Discount");
   	}
   		
    else
       $objResponse->alert("DB failed: ".$objSS->sql);

    return $objResponse;

}

#Added By Jarel 07/24/2013
function applyConsultationWithAmount($amount,$pid){
	global $db;
    $objResponse = new xajaxResponse();
    $seg_ormisc = new SegOR_MiscCharges();


			$miscItems[] ='201200002338';
			$miscQty[] = '1';
			$miscPrc[] = $amount;
            $miscAdj[] =  $amount;
			$miscType[] = '33';
            $miscClinic[] = '';
	$array = array(
			'refno' => $seg_ormisc->getMiscRefno(date('Y-m-d H:i:s')),
			'charge_date' => date('Y-m-d H:i:s'),
			'encounter_nr' => '',
            'pid' => $pid,
			'misc' => $miscItems,
            'discountid' => '',
            'discount' =>  '',
            'misc' => $miscItems,
			'quantity' => $miscQty,
            'adj_amnt' => $miscAdj,
			'price' => $miscPrc,
			'account_type' => $miscType,
			'is_cash' => '1',
            'clinical_info' => $miscClinic,
			'area' => 'opd'); //edit
	
	$saveok = $seg_ormisc->saveMiscCharges($array);

	if($saveok){
		$objResponse->alert("Successfully Applied Consultation Fee Discount.");
		$objResponse->assign("consultation","value",1);
   		$objResponse->assign("consultation","innerHTML","Cancel Consultation Fee Discount");
	}else{
		$objResponse->alert("Failed to Apply Consultation Fee Discount!");
	}


    return $objResponse;

}

function isForNewBilling($enc){
	global $db;
	$objResponse = new xajaxResponse();

	$sql = "SELECT a.bill_nr FROM seg_billing_encounter AS a
			INNER JOIN seg_billing_encounter_details AS b ON a.bill_nr = b.bill_nr
			WHERE encounter_nr = ".$db->qstr($enc)." AND a.is_deleted IS NULL 
			LIMIT 1";
	$rs = $db->Execute($sql);
	if($rs){
		if($rs->RecordCount()>0){
			$row = $rs->FetchRow();
			$data = $row['bill_nr'];
		}else{
			$data = 0;
		}
	}else{
		$data = 0;
	}

	$objResponse->call("setValue",$data);
	return $objResponse;
}

$xajax->processRequest();
?>