<?php
	function populatePersonList($sElem,$searchkey,$page,$include_firstname,$include_encounter=TRUE,$include_walkin=FALSE,$exclude_mgh=FALSE) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();

		$person=& new Person();
		$dept_obj = new Department;
		$ward_obj = new Ward;
		$objSS = new SocialService;
        $srvObj=new SegLab();

		#added by VAN 06-25-08
		$objSS = new SocialService;

		#added by VAN 03-10-2011
		$enc_obj=new Encounter;

		$offset = $page * $maxRows;
		if ($include_encounter) {
			$ergebnis=$person->SearchSelectWithCurrentEncounter($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$exclude_mgh);	//modified cha, june 1,2010
			#$objResponse->alert('enc = '.$person->sql);
			$total = $person->FoundRows();
			$lastPage = floor($total/$maxRows);
			if ($page > $lastPage) $page=$lastPage;
		}
		else {
			$ergebnis=$person->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
			#$objResponse->alert('per = '.$person->sql);
			$total = $person->FoundRows();
			$lastPage = floor($total/$maxRows);
			if ($page > $lastPage) $page=$lastPage;
		}
		//$objResponse->alert($person->sql);
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

				#$parent_discountid = '';
				if ($result["discountid"]=='SC'){
					$senior_citizen = 1;
				}else{
					$senior_citizen = 0;
				}

				$discountid = $result["discountid"];
				$discount = $result["discount"];

				$ssInfo = $objSS->getSSClassInfo($discountid);
				if (($ssInfo['parentid'])&&($ssInfo['parentid']=='D'))
					$parent_discountid = $ssInfo['parentid'];
				else
					$parent_discountid = $result["discountid"];

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

				// Fix for Pharmacy
				// 09-08-10
				if ($ssInfo['parentid'])
					$details->real_parent_discountid = trim($ssInfo['parentid']);
				else
					$details->real_parent_discountid = trim($ssInfo['discountid']);

				$details->senior_citizen = trim($senior_citizen);
				$details->discountid = trim($discountid);
				$details->discount = trim($discount);
				$details->parent_discountid = trim($parent_discountid);

				if ((($result["encounter_type"]==2)||($result["encounter_type"]==5)||($result["encounter_type"]==6))||($result["encounter_type"]==1))
					$details->adm_diagnosis = trim(htmlentities($result['chief_complaint']));
				elseif (($result["encounter_type"]==3) || ($result["encounter_type"]==4))
					$details->adm_diagnosis = trim(htmlentities($result['er_opd_diagnosis']));

				#$details->adm_diagnosis = trim(htmlentities($result['er_opd_diagnosis']));

				if (!$details->adm_diagnosis) {
					#edited by VAN 03-09-2011
					#$details->adm_diagnosis = 'N/A';
					$details->adm_diagnosis = '';

					$details->adm_diagnosis = $enc_obj->getLatestImpression($result['pid'], $result['encounter_nr']);

				}

				# $objResponse->alert($details->adm_diagnosis);
				$details->orig_discountid = trim($result["discountid"]);
				$details->rid = trim($result['rid']);

                #for hact patient
                $request_time = date("Y-m-d H:i:s");
                $row_hact = $srvObj->checkHactInfo($result['pid'], $request_time);
                #echo $srvObj->sql;
                if ($row_hact['status']=='hact')
                    $details->is_hact = 1;
                else
                    $details->is_hact = 0; 
                    
                #patient blood type
                $request_time = date("Y-m-d H:i:s");
                #$row_pbt = $srvObj->checkBloodTypeInfo($result['pid'], $request_time);
                $row_pbt = $srvObj->getBloodTypeInfo($result['pid']);        
                $details->blood_type = $row_pbt['blood_type'];     
                
                #$objResponse->alert('s ='.$details->is_hact);
				$details->admission_dt = '';
				$details->discharge_date = '';

                #added by pol
                if($result['encounter_nr']){
                    $sql_mc = "SELECT cif.`firm_id` FROM seg_encounter_insurance AS sei 
								INNER JOIN care_insurance_firm AS cif ON cif.`hcare_id` = sei.`hcare_id` 
								WHERE cif.hcare_id != 18 AND sei.encounter_nr = ".$db->qstr($result["encounter_nr"]);
                                                      
                    //$objResponse->alert($sql_mc);
                    // $rs_mc = $db->Execute($sql_mc);
                    // $row_mc = $rs_mc->FetchRow();

                    $category = $db->GetOne($sql_mc);
                    //$objResponse->alert(print_r($row_mc,true));
                    if($category){                        
                        $details->category = $category;
                    }else{
                        $details->category = 'None';    
                    }
                    //$objResponse->alert('category');
                    //$objResponse->alert($row_mc['memcategory_desc']);
                } else {
                    $details->category    = 'N/A';
                }
                #pol end
                
                #addeb by Dommie to sum the total charges of med
                #Modified by: 4/15/14 Dommie to sum only the med that was served.
                #Modified by: 4/20/14 Dommie to add return quantity to minus the total quanty request med or sup
                if($result["encounter_nr"]){
			        $sql_mc = "SELECT SUM(seg_pharma_order_items.pricecharge*(seg_pharma_order_items.quantity)) AS TotalCharges
									FROM seg_pharma_orders 
									LEFT JOIN seg_pharma_order_items 
									ON  seg_pharma_orders.refno= seg_pharma_order_items.refno									
									WHERE seg_pharma_order_items.serve_status ='S' AND is_cash=0 AND seg_pharma_orders.encounter_nr=".$db->qstr($result["encounter_nr"]);
			        $rs_totalCharges = $db->Execute($sql_mc);
			        $row_totalCharges = $rs_totalCharges->FetchRow();

			        $sql_mc ="SELECT SQL_CALC_FOUND_ROWS  
								(SELECT IFNULL(SUM(IFNULL(r.quantity,0)),0) FROM seg_pharma_return_items r 
								WHERE r.ref_no=oi.refno AND r.bestellnum=oi.bestellnum) *
								oi.pricecharge AS TotalReturnCharge
								FROM seg_pharma_order_items oi 
								INNER JOIN seg_pharma_orders o ON o.refno=oi.refno 
								INNER JOIN care_person cp ON cp.pid=o.pid 
								INNER JOIN care_pharma_products_main p ON p.bestellnum=oi.bestellnum 
								WHERE (oi.serve_status='S') AND (o.is_cash=0) 
								AND (o.encounter_nr=".$db->qstr($result["encounter_nr"]).") 
								GROUP BY oi.bestellnum";
					$rs_ReturnCharges = $db->Execute($sql_mc);
					while ($res_ReturnCharges=$rs_ReturnCharges->FetchRow()) {
							$row_TotalReturnCharges=$row_TotalReturnCharges+$res_ReturnCharges['TotalReturnCharge'];
						}

					$TotalCharges=$row_totalCharges['TotalCharges']-$row_TotalReturnCharges;
			        $details->totalCharges = $TotalCharges;
			    }
                #Dommie end
                
				#added by VAN 06-02-08
				if ($result["encounter_type"]==1){
					$details->enctype = "ER PATIENT";
					$details->location = "EMERGENCY ROOM";
				}elseif (($result["encounter_type"]==2)||($result["encounter_type"]==5)||($result["encounter_type"]==6)){
					if ($result["encounter_type"]==2){
						$details->enctype = "OUTPATIENT";
						$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
						$details->location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
					}elseif ($result["encounter_type"]==5){
						$details->enctype = "RDU";
						$details->location = 'Dialysis';
					}elseif ($result["encounter_type"]==6){
						$details->enctype = "IC";
						$details->location = 'Industrial Clinic';

						$sql_ic = "SELECT c.*, t.*
												FROM seg_industrial_transaction AS t
												LEFT JOIN seg_industrial_company AS c ON c.company_id=t.agency_id
												WHERE encounter_nr='".$result["encounter_nr"]."'";
						#$objResponse->alert($sql_ic);
						$rs_ic = $db->Execute($sql_ic);
						$row_ic = $rs_ic->FetchRow();
						#$objResponse->alert(print_r($row_ic,true));
						$details->is_charge2comp = $row_ic['agency_charged'];
						$details->compID = $row_ic['agency_id'];
						$details->compName = $row_ic['name'];
						$details->discountid = "";
						$details->discount = 0;

					}
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
				$details->doc_nr = $result['current_att_dr_nr'];
				$details->insurance = $result['phic_nr']; //added by cha, august 17, 2010

				#included here in php script to get the database server time and not the time of the client time
				$details->currenttime = date('H');
                #$objResponse->alert($result['encounter_nr']);
                
                $billinfo = $enc_obj->hasSavedBilling($result['encounter_nr']);
                $bill_nr = $billinfo['bill_nr'];
                #$objResponse->alert('bill = '.$bill_nr);
                $details->bill_nr = $bill_nr;
                $details->is_maygohome = $result['is_maygohome'];
                $details->hasfinal_bill = $billinfo['is_final'];

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
			 #$objResponse->alert($details->rid);
			 #$objResponse->alert(print_r($details));
				$details->area_type = $area_type;
				#$objResponse->alert($details->is_charge2comp.", ".$details->compID.", ".$details->compName);
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

	require('./roots.php');
	require_once($root_path.'include/inc_environment_global.php');
	require_once($root_path.'classes/adodb/adodb-lib.inc.php');
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/care_api_classes/class_person.php');
	require_once($root_path."modules/registration_admission/ajax/order-psearch.common.php");

	#added by VAN 06-02-08
	require_once($root_path.'include/care_api_classes/class_department.php');
	require_once($root_path.'include/care_api_classes/class_ward.php');

	#added by VAN 06-25-08
	require_once($root_path.'include/care_api_classes/class_social_service.php');

        #added by VAN 03-10-2011
	require_once($root_path.'include/care_api_classes/class_encounter.php');
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');

	$xajax->processRequest();
?>