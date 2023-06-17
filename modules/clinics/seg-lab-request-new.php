<?php
	# Start Smarty templating here
 /**
 * LOAD Smarty
 */
	
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
 	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'modules/clinics/ajax/lab-request-new.common.php'); 
	
	#-------------added by VAN ----------
	$dbtable='care_config_global'; // Taboile name for global configurations
	$GLOBAL_CONFIG=array();
	$new_date_ok=0;

	# Create global config object
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/inc_date_format_functions.php');

	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('refno_%');
	if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
	$date_format=$GLOBAL_CONFIG['date_format'];
	
	$breakfile = "labor.php";

	$phpfd=$date_format;

	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	$phpfd=str_replace("yy","%y", strtolower($phpfd));

	$php_date_format = strtolower($date_format);
	$php_date_format = str_replace("dd","d",$php_date_format);
	$php_date_format = str_replace("mm","m",$php_date_format);
	$php_date_format = str_replace("yyyy","Y",$php_date_format);
	$php_date_format = str_replace("yy","y",$php_date_format);
	
	#------------------------------------
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
	define('LANG_FILE','lab.php');
	$local_user='ck_lab_user';
	
	define('NO_2LEVEL_CHK',1);
	require_once($root_path.'include/inc_front_chain_lang.php');

#	$allowedarea=array("_a_1_labcreaterequest");
	
	# Create laboratory service object
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab();
	
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	
	require_once($root_path.'include/care_api_classes/class_personell.php');
	$pers_obj=new Personell;

	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	
	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj=new Person();
	
	require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
	$hclabObj = new HCLAB;
	
	require_once($root_path.'include/care_api_classes/class_ward.php');
	$ward_obj = new Ward;
	
	require_once($root_path.'include/care_api_classes/class_social_service.php');
	$objSS = new SocialService;
	
	global $db, $db_hclab, $allow_labrepeat;
	
    #echo "dsn = ".$dsn;
    #echo "<br>dblink_hclab_ok = ".$dblink_hclab_ok;
            
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');
	
	$popUp = $_GET['popUp'];
	$repeat = $_GET['repeat'];
	$prevrefno = $_GET['prevrefno'];
	$serv_code = $_GET['serv_code'];
	
	$dr_nr = $_GET['dr_nr'];
	$dept_nr = $_GET['dept_nr'];
	$is_dr = $_GET['is_dr'];
	
	$ptype = $_GET['ptype'];
    
    $lab_area = $_GET['labarea'];
    
    if(empty($lab_area))
        $lab_area='SL';
	
	#added by VAN 06-24-08
	#echo "id = ".$_GET['discountid'];
	if ($_GET['discountid'])
		$discountid = $_GET['discountid'];
	#echo "discountid = ".$discountid;
	
	if ($_GET['view_from'])
		$popUp = 1;
	
	$repeaterror = $_GET['repeaterror'];
	
	#added by VAN 07-02-08
	#echo '<br>enc = '.$_GET['encounter_nr'];
	#echo '<br>pid = '.$_GET['pid'];
	#echo '<br>area = '.$_GET['area'];
	if ($_GET['encounter_nr'])
		$encounter_nr = $_GET['encounter_nr'];
	
	if ($_GET['area'])
		$area = $_GET['area'];
		
	if ($_GET['pid'])
		$pid = $_GET['pid'];	
	#---------------------------
	#echo "f = ".$pid;
	#echo "<br>";
  
	if ($encounter_nr){
		$patient = $enc_obj->getEncounterInfo($encounter_nr);
	}else{
		$patient = $person_obj->getAllInfoArray($pid);
	}
	
	#echo "e = ".$enc_obj->sql;
	if ($repeaterror){
		$smarty->assign('sysErrorMessage',"<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!");
	}
	
	if (isset($_POST["submited"])) {
		
		$new_refno = $srvObj->getLastNr(date("Y-m-d"),"'".$GLOBAL_CONFIG['refno_init']."'");
		
		if ($_POST['pid']==NULL)
			$pid = " ";
		else
			$pid = $_POST['pid'];	
			
		if (empty($discountid))
			$discountid = " ";
		
		if (empty($encounter_nr))	
			$encounter_nr = " ";
		
		#echo "<br>enc = ".$encounter_nr;
		#echo "<br>pid = ".$pid;
		
		#added by VAN 01-0908
		#$patient = $enc_obj->getEncounterInfo($encounter_nr);
		#echo "sql = ".$enc_obj->sql;
		$lab_rs = $srvObj->getDeptRequested($refno);  
        #echo "dep = ".$lab_rs['request_dept'];  
      
        if ($patient['encounter_type'] == 1){
			$patient_type = "ER";
			$loc_code = "ER";
			#$loc_name = "Emergency Room";
			$loc_name = "ER";
		}elseif (($patient['encounter_type'] == 3)||($patient['encounter_type'] == 4)){
			$patient_type = "IN";	
			#$patient_type = "ADM";	
			$loc_code = $patient['current_ward_nr'];
			if ($loc_code)
				$ward = $ward_obj->getWardInfo($loc_code);
					
			$loc_name = stripslashes($ward['name']);
		}elseif ($patient['encounter_type'] == 2){
			$patient_type = "OP";	
			#$patient_type = "OPD";	
			$loc_code = $patient['current_dept_nr'];
			if ($loc_code)
				$dept = $dept_obj->getDeptAllInfo($loc_code);
				
			$loc_name = stripslashes($dept['name_formal']);
		}else{
			$patient_type = "WN";  #Walk-in	
			$loc_code = "WIN";
			$loc_name = "WIN";
		}
        
        #if (($lab_rs['request_dept'])&&($patient_type!='IN')&&($patient_type!='WN')){
        #edited by VAN 10-21-09
        if ($lab_rs['request_dept']){ 
            $loc_code = $lab_rs['request_dept'];
            if ($loc_code)
                $dept = $dept_obj->getDeptAllInfo($loc_code);
                
            $loc_name = stripslashes($dept['name_formal']);
        }    
		
		if (empty($_POST['is_tpl'])){
			$_POST['is_tpl'] = '0';
		}elseif($_POST['is_tpl']){
			$_POST['type_charge'] = '0';
		}
		
		#if ($_POST['is_cash'])
		#	$_POST['type_charge'] = '0';
		
		#echo "<br>charge = ".$_POST['type_charge'];
		$data = array(
			'encounter_nr'=>$encounter_nr,
			'pid'=>$pid,
			'is_cash'=>$_POST['is_cash'],
			'type_charge'=>$_POST['type_charge'],
			'is_urgent'=>$_POST['priority'],
			'is_tpl'=>$_POST['is_tpl'],
			'create_id'=>$_SESSION['sess_temp_userid'],   
			'modify_id'=>$_SESSION['sess_temp_userid'],   
			'modify_dt'=>date('YmdHis'),   
			'create_dt'=>date('YmdHis'),
			'comments'=>addslashes($_POST['comments']),
			'ordername'=>addslashes(trim($_POST['ordername'])),
			'orderaddress'=>addslashes($_POST['orderaddress']),
			'status'=>" ",
			'discountid'=>$_POST['discountid'],
			'loc_code'=>$loc_code,
			'parent_refno'=>$_POST['parent_refno'],
			'approved_by_head'=>$_POST['head'],
			'remarks'=>$_POST['remarks'],
			'headID'=>$_POST['headID'],
			'headpasswd'=>$_POST['headpasswd'],
            'is_rdu'=>$_POST['is_rdu']
		);
		  
		#'history'=>"Create: ".date('Y-m-d H:i:s')." [\\".$_SESSION['sess_temp_userid']."]\\n",
		if ($_POST['orderdate']) {
			#$time = strtotime($_POST['orderdate']);
			#$data["serv_dt"] = date("Ymd",$time);
			$data["serv_dt"] = date("Ymd",strtotime($_POST['orderdate']));
			$data["serv_tm"] = date("H:i:s",strtotime($_POST['orderdate']));
		}
	#echo "".$_POST['ordername']
		if ($_POST["pid"]) $data["pid"] = $_POST["pid"];
		#echo "mode = ".$mode;
		if ($_POST["items"]!=NULL){
			switch($mode) {      
        		   case 'save': 
									#insert table
									#echo "<br>switch save";
									$data["refno"] = $new_refno;
									$data["history"] = "Create: ".date('Y-m-d H:i:s')." [\\".$_SESSION['sess_temp_userid']."]\\n";
									$srvObj->useLabServ();
									$srvObj->setDataArray($data);
									if ($repeat){
										$srvObj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
										$isCorrectInfo = $srvObj->count;
										if (($isCorrectInfo)||($allow_labrepeat)){
											$saveok=$srvObj->insertDataFromInternalArray();
										}else{
											header("Location: seg-lab-request-new.php".URL_REDIRECT_APPEND."&user_origin=$user_origin&repeat=$repeat&prevrefno=".$_POST['parent_refno']."&serv_code=".stripslashes($_POST["items"][0])."&paid=1&popUp=1&repeaterror=1");
										}
									}else{
										$saveok=$srvObj->insertDataFromInternalArray();
									}	
									#echo "sql = ".$db->errorMsg();
							#echo "<br>add sql = ".$srvObj->sql;
									break;
					case 'update':
									#update table
									$data["refno"] = $_POST["refno"];
									
									if ($data["refno"]==NULL)
										$data["refno"] = $_GET["refno"];
                                        
                                    if (empty($data['is_rdu']))    
                                         $data["is_rdu"] = '0';
                                         
                                    if(isset($data['create_id'])) unset($data['create_id']);
									if(isset($data['create_dt'])) unset($data['create_dt']);
									if(isset($data['serv_tm'])) unset($data['serv_tm']);
									
									$srvObj->where=" refno='".$data["refno"]."'";
									#$srvObj->->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
									$data["history"] = $srvObj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [\\".$_SESSION['sess_temp_userid']."]\\n");
									$srvObj->useLabServ();
									#print_r($data);
									$srvObj->setDataArray($data);
									#added by VAN 03-07-08
									if ($repeat){
										$srvObj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
										$isCorrectInfo = $srvObj->count;
										if (($isCorrectInfo)||($allow_labrepeat)){
											$saveok=$srvObj->updateDataFromInternalArray($data["refno"]);
										}else{
											#echo '<em class="warn">Sorry but you are not allowed to do a repeat request!</em>';
											header("Location: seg-lab-request-new.php".URL_REDIRECT_APPEND."&user_origin=$user_origin&repeat=$repeat&prevrefno=".$_POST['parent_refno']."&serv_code=".stripslashes($_POST["items"][0])."&paid=1&popUp=1&repeaterror=1");
										}	
									}else{
										$saveok=$srvObj->updateDataFromInternalArray($data["refno"]);
									}	
									#echo "<br>update sql = ".$srvObj->sql;
									break;
			} #end of switch statement
		}	
		
		#echo "<br>save = ".$saveok;
		if ($saveok) {
			if ($_POST["items"]!=NULL){
				
				$bulk = array();
				#$withsample = array();
				foreach ($_POST["items"] as $i=>$v) {
					#if ($_POST["withsample"][$i])
					#	$_POST["withsample"][$i] = 1;
					#else
					#	$_POST["withsample"][$i] = 0;	
					
					#withsampleID
					$sampleid = 'withsampleID'.$_POST["items"][$i];
					#$sampleid = 'withsampleID'.$_POST["items"];
					#echo "<br>id =".$id;
					#echo "<br>i = ".$_POST[$id];
					if ($_POST[$sampleid]!=1)
						$_POST[$sampleid] = 0;
						
					#$withsample[] =  $_POST[$id];
					#echo "<br>".$_POST[$sampleid][$i];
					
					#echo "<br>here ".$i." = ".$_POST["items"][$i]." - ".$_POST["withsample"][$i];	
					
					if ($_POST['is_cash']){
						
						#---------added by VAN 10-17-07-----------
						if (empty($discountid)){
							if ($discountid == 'C1'){
								#echo "<br>discountid is C1 = ".$_POST["price_C1"][$i];
								$cash_price = $_POST["price_C1"][$i];
							}elseif ($discountid == 'C2'){
								#echo "<br>discountid is C2 = ".$_POST["price_C2"][$i];
								$cash_price = $_POST["price_C2"][$i];	
							}elseif ($discountid == 'C3'){
								#echo "<br>discountid is C3 = ".$_POST["price_C3"][$i];
								$cash_price = $_POST["price_C3"][$i];		
							}else{
								#echo "<br>discountid is none = ".$_POST["pcash"][$i];
								$cash_price = 0;
							}	
						}else{
							$cash_price = $_POST["pcash"][$i];
						}	
						#------------------------------------------
					
						$bulk[] = array($_POST["items"][$i],$cash_price,$_POST["pcashorig"][$i],$_POST["pcharge"][$i],stripslashes($_POST["requestDoc"][$i]),stripslashes($_POST["requestDept"][$i]),$_POST["isInHouse"][$i],stripslashes($_POST["clinicInfo"][$i]),$_POST[$sampleid]);
					}else{
						$cash_price = $_POST["pcharge"][$i];
						$bulk[] = array($_POST["items"][$i],$cash_price,$_POST["pcashorig"][$i],$_POST["pcharge"][$i],stripslashes($_POST["requestDoc"][$i]),stripslashes($_POST["requestDept"][$i]),$_POST["isInHouse"][$i],stripslashes($_POST["clinicInfo"][$i]),$_POST[$sampleid]);	
					}	
				}
				#print_r($_POST[$sampleid][$i]);
                #print_r($_POST["items"][$i]);
				$srvObj->clearOrderList($data['refno']);
				#echo "".$srvObj->sql;
				#print_r($bulk);
				$srvObj->addOrders($data['refno'],$bulk);
				#echo "".$srvObj->sql;
				$_POST['refno'] = $data['refno'];
				
        $srvObj->cleargrantLabRequest($data['refno']);
        #echo $srvObj->sql;
        $srvObj->grantLabRequest($_POST);
		#echo "here = ".$srvObj->sql;
		#echo "<br>er = ".$db->ErrorMsg();
				
				
				#-----post to HCLAB when
				# request is granted
				# request is to be paid later (is_urgent=1)
				# request is paid
				
				if ($_POST['priority']){
					$priority = "U";
				}else{
					$priority = "R";	
				}
				$saveHCLAB = 0;
				if ($mode == "save"){
					$trx_ID = "N";    # new order
					$ref_no = $new_refno;
					$new_order_no = $srvObj->getOrderLastNr("'".$GLOBAL_CONFIG['refno_hclab_init']."'");
					#$ok_insertHCLAB = $srvObj->insert_Orderno_HCLAB($new_order_no, $ref_no);
					$saveHCLAB = 1;
				}elseif ($mode=="update"){
					#$trx_ID = "U";		# update order
					$trx_ID = "N";		# update order
					$ref_no = $_POST["refno"];
					/*
					$resultLast = $srvObj->isExistOrderNo($_POST["refno"]);
					if ($resultLast['lis_order_no'])
						#$new_order_no = $resultLast['lis_order_no'];
						$new_order_no = $srvObj->getOrderLastNr("'".$GLOBAL_CONFIG['refno_hclab_init']."'");
					else{
						$new_order_no = $srvObj->getOrderLastNr("'".$GLOBAL_CONFIG['refno_hclab_init']."'");
						#$ok_insertHCLAB = $srvObj->insert_Orderno_HCLAB($new_order_no, $ref_no);
						$saveHCLAB = 1;
					}
					*/
					$new_order_no = $srvObj->getOrderLastNr("'".$GLOBAL_CONFIG['refno_hclab_init']."'");
					$saveHCLAB = 1;
					
					#$new_order_no = $srvObj->getOrderLastNr("'".$GLOBAL_CONFIG['refno_hclab_init']."'");
					#$ok_insertHCLAB = $srvObj->insert_Orderno_HCLAB($new_order_no, $new_refno);		
					#echo "<br>sql = ".$srvObj->sql;
				}else{
					$trx_ID = "C";		# cancel order
				}
				
				$trx_status = "N";   # before read by LIS default value
								
				if ($patient['sex']=="m"){
					$sex = 1;
				}elseif ($patient['sex']=="f"){
					$sex = 2;
				}else{
					$sex = 0;
				}

				$order_date = date("n/j/Y",strtotime($data["serv_dt"]))." ".date("g:i:s A",strtotime($data["serv_tm"]));
				
				$order_date_trx = date("n/j/Y")." ".date("g:i:s A");

				$time_bod = strtotime($patient["date_birth"]);
				$patient_bdate = date("n/j/Y",$time_bod);
				
				$patient_name = mb_strtoupper(trim($patient['name_last'])).", ".mb_strtoupper(trim($patient['name_first']))." ".mb_strtoupper(trim($patient['name_middle']));
				
				#$new_order_no = $srvObj->getOrderLastNr("'".$GLOBAL_CONFIG['refno_hclab_init']."'");
				#echo "sql = ".$srvObj->sql;
				#$new_order_no = $GLOBAL_CONFIG['refno_hclab_init'];
				
				#$ok_insertHCLAB = $srvObj->insert_Orderno_HCLAB($new_order_no);
				#echo "<br>new = ".$new_order_no;
				#exit();
				#'POH_ORDER_NO'=>$new_order_no,
				
				#interchange
				# order dt = the extraction date 'POH_ORDER_DT'=>$order_date,
				# transaction dt = the request date 'POH_TRX_DT'=>$order_date_trx,
				$data_HCLAB = array(
							'POH_TRX_NUM'=>$ref_no ,
							'POH_TRX_DT'=>$order_date,
							'POH_TRX_ID'=>$trx_ID,
							'POH_TRX_STATUS'=>$trx_status,
							'POH_ORDER_NO'=>$new_order_no,   
							'POH_ORDER_DT'=>$order_date_trx,   
							'POH_LOC_CODE'=>mb_strtoupper($loc_code),   
							'POH_LOC_NAME'=>mb_strtoupper($loc_name),
							'POH_DR_CODE'=>$_POST["requestDoc"][0],
							'POH_DR_NAME'=>addslashes(mb_strtoupper($_POST["requestDocName"][0])),
							'POH_PAT_ID'=>$pid,
							'POH_PAT_NAME'=>$patient_name,
							'POH_PAT_TYPE'=>mb_strtoupper($patient_type),
							'POH_PAT_ALTID'=>" ",
							'POH_PAT_DOB'=>$patient_bdate,
							'POH_PAT_SEX'=>$sex,
							'POH_PAT_CASENO'=>$encounter_nr,
							'POH_CLI_INFO'=>addslashes(mb_strtoupper($_POST["clinicInfo"][0])),
							'POH_PRIORITY'=>$priority
						);
				#exit();
				$bulk_HCLAB = array();
				
				#comment this for the meantime
				# for HCLAB (oracle connection)
			#be deleted today
            #$dsn = "hclabdb";	
			if ($dsn){	
				#$objconn = $hclabObj->ConnecttoDest($dsn);
				#echo "here = ".$dsn;
				# if connection to HCLAB is OK
                #be deleted today
				#$objconn = 1;
				#if ($objconn) {
				if ($db_hclab){
					# request to be post in HCLAB database
					# if priority is urgent, if charge and if repeat request						
					
					#added by VAN 08-29-08
					if ($prev_request)
						$prevrequest = explode(",",$prev_request);
											
					$serv = $_POST["items"];
					$serv_prev = $prevrequest;
									
					$size_prev = sizeof($serv_prev);
					$size_cur =  sizeof($serv);
			
					if ($size_prev < $size_cur) {
						for ($cnt=0; $cnt<sizeof($serv); $cnt++){
							if (in_array($serv[$cnt], $serv_prev)) {
								$existing = $existing."'".$serv[$cnt]."',";
							}else{
								$not_existing = $not_existing."'".$serv[$cnt]."',"; 
							}
						}
				
						for ($cnt=0; $cnt<sizeof($serv_prev); $cnt++){
							if (in_array($serv_prev[$cnt], $serv)) {
								$existing2 = $existing2."'".$serv_prev[$cnt]."',";
							}else{
								$not_existing2 = $not_existing2."'".$serv_prev[$cnt]."',";
							}
						}
				
			         }elseif($size_prev > $size_cur){
						for ($cnt=0; $cnt<sizeof($serv); $cnt++){
							if (in_array($serv[$cnt], $serv_prev)) {
								$existing2 = $existing2."'".$serv[$cnt]."',";
							}else{
								$not_existing2 = $not_existing2."'".$serv[$cnt]."',"; 
							}
						}
				
						for ($cnt=0; $cnt<sizeof($serv_prev); $cnt++){
							if (in_array($serv_prev[$cnt], $serv)) {
								$existing = $existing."'".$serv_prev[$cnt]."',";
							}else{
								$not_existing = $not_existing."'".$serv_prev[$cnt]."',";
							}
						}
											
			       	}elseif($size_prev == $size_cur){
				
						for ($cnt=0; $cnt<sizeof($serv); $cnt++){
							if (in_array($serv[$cnt], $serv_prev)) {
								$existing = $existing."'".$serv[$cnt]."',";
							}else{
								$not_existing = $not_existing."'".$serv[$cnt]."',"; 
							}
						}
				
						for ($cnt=0; $cnt<sizeof($serv_prev); $cnt++){
							if (in_array($serv_prev[$cnt], $serv)) {
								$existing2 = $existing2."'".$serv_prev[$cnt]."',";
							}else{
								$not_existing2 = $not_existing2."'".$serv_prev[$cnt]."',";
							}
						}
				}
			
				$existinglist = substr($existing, 0, strlen($existing)-1);  
				$serv_existing = explode(",",$existinglist);

				#echo "<br>existing array =";
				#print_r($serv_existing);
			
				$existinglist2 = substr($existing2, 0, strlen($existing2)-1);  
				$serv_existing2 = explode(",",$existinglist2);
			
				#echo "<br>existing array2 =";
				#print_r($serv_existing2);
			
				$not_existinglist = substr($not_existing, 0, strlen($not_existing)-1);  
				$serv_not_existing = explode(",",$not_existinglist);

				#echo "<br>not existing array =";
				#print_r($serv_not_existing);
			
				$not_existinglist2 = substr($not_existing2, 0, strlen($not_existing2)-1);  
				$serv_not_existing2 = explode(",",$not_existinglist2);
											
				#echo "<br>not existing array2 =";
				#print_r($serv_not_existing2);
				
				if (($serv_not_existing!=NULL) && ($size_cur >= $size_prev)){
					$to_be_added = implode(",",$serv_not_existing);
					$to_be_deleted = implode(",",$serv_not_existing2);
				}elseif(($serv_not_existing!=NULL) && ($size_cur < $size_prev)){
					$to_be_added = implode(",",$serv_not_existing2);
					$to_be_deleted = implode(",",$serv_not_existing);	
				}
				
				#echo "to_be_deleted = ".$to_be_deleted;
			#---------------------------
					
					#if (($_POST['priority'])||($_POST['is_cash']==0)||($_POST['is_tpl'])|| ($repeat)){
					#edited by VAN 07-02-08
					#POST in THE HCLAB if and only if
						# repeat request 
						# priority
						# from ER & charge, enctype = 1
						# approved by the billing
						# approved by the SS for TPL
						# if request is paid
					#if (($_POST['priority'])|| (($patient['encounter_type'] == 1)&&($_POST['is_cash']==0)) ||($repeat)){
					#if (($_POST['priority'])|| (($patient['encounter_type'] == 1)&&($_POST['is_cash']==0)) || ($_POST['is_cash']==0) ||($repeat)){
                     #echo "paid = ".$_POST['ispaid'];
                    if (($_POST['ispaid']) ||($_POST['priority'])|| (($patient['encounter_type'] != 2)&&($_POST['is_cash']==0)) || ($_POST['is_cash']==0) || ($_POST['type_charge']!=0)  ||($repeat)){
						#echo "here";
                        #added by VAN 06-02-09
                        $test_package = array();
                        $test_in_package=array();
                        $test_info = array();
                        foreach ($_POST["items"] as $i=>$v) {
                             $rs_test = $srvObj->getServiceInfo($_POST["items"][$i]);
                             #echo "<br>sql = ".$srvObj->sql;
                             #echo "<br><br>sl = ".$rs_test['is_package'];
                             if ($rs_test['is_package']){
                                 $test_package[] =  $_POST["items"][$i]; 
                                 
                                 $sampleid = 'withsampleID'.$_POST["items"][$i];
                                 if ($_POST[$sampleid]!=1)
                                    $_POST[$sampleid] = 0;
                                
                                 $rs_package = $srvObj->get_LabServiceGroupPackage($_POST["items"][$i]);
                                 #echo "<br>".$srvObj->sql;
                                 $count =  $srvObj->count;                               
                                 if ($count){
                                     while ($row_package=$rs_package->FetchRow()) {
                                           $test_in_package[] = $row_package['service_code_child'];
                                           #group_code, service_code of the package, sample, if package
                                           $test_info[$row_package['service_code_child']] = array($row_package['child_group'], $row_package['service_code'],$_POST[$sampleid], 1);
                                     }
                                 }
                             }
                        }    
                        
                        #print_r($test_info);
                        #package service is not included
                        $test_array = array_diff($_POST["items"], $test_package);
                        
                        #final array -- test included in the package is added in the array
                        $test_array_item = array_merge($test_array, $test_in_package);
                        
                        #new array items to be posted in HCLAB LIS
                        $_POST["items"] =  $test_array_item;
                        
                        #echo "<br>"; 
                        #print_r($_POST["items"]);
                         
                        #--------------   
                        
                        foreach ($_POST["items"] as $i=>$v) {
							#echo "<br>item = ".$_POST["items"][$i];
							#print_r($_POST["items"]);
                            #echo "<br>";
                            #exit();
                            if (empty($test_info[$_POST["items"][$i]][3])){  
							    $sampleid = 'withsampleID'.$_POST["items"][$i];
							    if ($_POST[$sampleid]!=1)
								    $_POST[$sampleid] = 0;
								    
							    $groupid = 'group'.$_POST["items"][$i];	
                            }else{
                                  $_POST[$sampleid] = $test_info[$_POST["items"][$i]][2];
                                  $_POST[$groupid] =   $test_info[$_POST["items"][$i]][0];
                            }
                            
							#only HEMA, CHEM and SERO can be saved in HCLAB
							$hclab_grp = array("H", "I", "C");
                            #$hclab_grp = array("H", "I", "C", "U");
							#PERIPHERAL BLOOD SMEAR, BONE MARROW SMEAR are not included to be posted in HCLAB
							$not_included_serv = array("PBS", "BMA","BMS");    
							#ER and IN Patients
							#$patient_type_notincluded = array("1","3","4");
                            #edited by VAN 06-02-09
                            $patient_type_notincluded = array();
                            
							$rowLab = $srvObj->getServiceInfo($_POST["items"][$i]);
                            
                            #added by VAN 06-02-09
                            #echo "<br><br> code = ".$_POST["items"][$i];
                            #echo "<br>group = ".$_POST[$groupid];
                            #echo "<br>parent = ".$test_info[$_POST["items"][$i]][1];
                            #echo "<br>sample = ".$_POST[$sampleid];
                            #echo "<br>package = ".$test_info[$_POST["items"][$i]][3];
                            #----------------
                            
                            #print_r($_POST[$groupid]);
							#echo "<br>wsample = ".$_POST["items"][$i]." - ".$_POST[$sampleid];	
							#echo "<br>group = ".$_POST[$sampleid]." - ".$_POST["items"][$i]." - ".$_POST[$groupid]." = ".(in_array($_POST[$groupid], $hclab_grp));	
							#echo "<br>here = ".$_POST["items"][$i];
							#if ($_POST[$sampleid]){
							if (($_POST[$sampleid]) && (in_array($_POST[$groupid], $hclab_grp))){
								#echo "hereagain";
                                if (($_POST[$groupid]=='H')&&(in_array($_POST["items"][$i], $not_included_serv))&&(in_array($patient['encounter_type'], $patient_type_notincluded))){
									#nothing to do
                                    #echo "here";
								}else{
									#echo "prevlist = ".$prev_request;
                                    #echo "<br>item = ".$_POST["items"][$i];
									#commented by VAN 10/09/08
									#if ($mode=='save'){
										$labservObj = $srvObj->getServiceInfo(addslashes($_POST["items"][$i]));
										if (($patient['encounter_type'] == 2) || empty($patient['encounter_type'])){
											#$_POST["items"][$i] = "O".$_POST["items"][$i];
											#get the oservice_code
											#$rowLab = $srvObj->getServiceInfo($_POST["items"][$i]);
											$_POST["items"][$i] = $rowLab['oservice_code'];
											
											#echo "<br>itemA = ".$_POST["items"][$i];
										}
										$bulk_HCLAB[] = array(addslashes($_POST["items"][$i]),addslashes($labservObj['name'])," ");	
									#echo "sh = ";
                                    #print_r($bulk_HCLAB);
									#exit();
									/* 
									}elseif ($mode=='update'){
											if ($prev_request)
													$prevrequest = explode(",",$prev_request);
									
											if (in_array($_POST["items"][$i],$prevrequest)){
												#nothing to do.. already in the hclab list
											}else{
									
												$labservObj = $srvObj->getServiceInfo(addslashes($_POST["items"][$i]));
												if (($patient['encounter_type'] == 2) || empty($patient['encounter_type'])){
													#$_POST["items"][$i] = "O".$_POST["items"][$i];
													$rowLab = $srvObj->getServiceInfo($_POST["items"][$i]);
													$_POST["items"][$i] = $rowLab['oservice_code'];
												}	
									
												$bulk_HCLAB[] = array(addslashes($_POST["items"][$i]),addslashes($labservObj['name'])," ");	
											}
									}   */
								}
							}
						}	
						# save to ORDERH in HCLAB if priority is to be paid later
						#echo "<br>request = ";
						#print_r($bulk_HCLAB);
						#print_r($data_HCLAB);
						#echo "<br>";
						#exit();
						if (sizeof($bulk_HCLAB)){
							if ($saveHCLAB)
								$ok_insertHCLAB = $srvObj->insert_Orderno_HCLAB($new_order_no, $ref_no);
								
							if ($hclabObj->isExists($ref_no)){
								#echo "<br>exist paid, charge, urgent = <br>".$hclabObj->sql;
								$hclabObj->updateOrderH_to_HCLAB($data_HCLAB);
								#echo "<br>sql update paid, charge, urgent = <br>".$hclabObj->sql;
							}else{
								#echo "<br>not exists = <br>".$hclabObj->sql;
								#print_r($data_HCLAB);
								$hclabObj->addOrderH_to_HCLAB($data_HCLAB);
								#echo "<br>sql add paid, charge, urgent = <br>".$hclabObj->sql;
							}	
						
							#echo $hclabObj->sql;
                            #edited by VAN 06-02-09
							#$hclabObj->clearOrderList_to_HCLAB($ref_no, $to_be_deleted);
							$hclabObj->clearOrderList_to_HCLAB($ref_no);
							#echo "<br>sql delete details paid, charge, urgent = <br>".$hclabObj->sql;
							$hclabObj->addOrders_to_HCLAB($ref_no, $bulk_HCLAB);
							#echo "<br>sql add details paid, charge, urgent = <br>".$hclabObj->sql;
						}
									
					}else{
						# request that to be granted or amount is 0
						# save to ORDERH in HCLAB
					     
						extract($_POST);
						$arrayItems = array();
						foreach ($_POST["items"] as $key => $value){
							if (floatval($pcash[$key])==0){
								$tempArray = array($value);
								array_push($arrayItems,$tempArray);
							}
						}

						if (empty($arrayItems)){
							# no items to be save
						}else{
                            
                            #added by VAN 06-02-09
                            $test_package = array();
                            $test_in_package=array();
                            $test_info = array();
                            foreach ($_POST["items"] as $i=>$v) {
                                 $rs_test = $srvObj->getServiceInfo($_POST["items"][$i]);
                                 #echo "<br>sql = ".$srvObj->sql;
                                 #echo "<br><br>sl = ".$rs_test['is_package'];
                                 if ($rs_test['is_package']){
                                     $test_package[] =  $_POST["items"][$i]; 
                                     
                                     $sampleid = 'withsampleID'.$_POST["items"][$i];
                                     if ($_POST[$sampleid]!=1)
                                        $_POST[$sampleid] = 0;
                                    
                                     $rs_package = $srvObj->get_LabServiceGroupPackage($_POST["items"][$i]);
                                     #echo "<br>".$srvObj->sql;
                                     $count =  $srvObj->count;                               
                                     if ($count){
                                         while ($row_package=$rs_package->FetchRow()) {
                                               $test_in_package[] = $row_package['service_code_child'];
                                               #group_code, service_code of the package, sample, if package
                                               $test_info[$row_package['service_code_child']] = array($row_package['child_group'], $row_package['service_code'],$_POST[$sampleid], 1);
                                         }
                                     }
                                 }
                            }    
                            
                            #print_r($test_info);
                            #package service is not included
                            $test_array = array_diff($_POST["items"], $test_package);
                            
                            #final array -- test included in the package is added in the array
                            $test_array_item = array_merge($test_array, $test_in_package);
                            
                            #new array items to be posted in HCLAB LIS
                            $_POST["items"] =  $test_array_item;
                            
                            #echo "<br>"; 
                            #print_r($_POST["items"]);
                             
                            #--------------   
                                
                            foreach ($_POST["items"] as $i=>$v) {
								if (floatval($pcash[$i])==0){
									/*
                                    $sampleid = 'withsampleID'.$_POST["items"][$i];
									if ($_POST[$sampleid]!=1)
									$_POST[$sampleid] = 0;
							
									#echo "<br>wsample = ".$_POST["items"][$i]." - ".$_POST[$sampleid];	
									
									#if ($_POST[$sampleid]){
									#	$labservObj = $srvObj->getServiceInfo(addslashes($_POST["items"][$i]));
									#	$bulk_HCLAB[] = array(addslashes($_POST["items"][$i]),addslashes($labservObj['name'])," ");	
									#}
									
									$groupid = 'group'.$_POST["items"][$i];	
									*/
                                    
                                    if (empty($test_info[$_POST["items"][$i]][3])){  
                                        $sampleid = 'withsampleID'.$_POST["items"][$i];
                                        if ($_POST[$sampleid]!=1)
                                            $_POST[$sampleid] = 0;
                                    
                                        $groupid = 'group'.$_POST["items"][$i];    
                                    }else{
                                        $_POST[$sampleid] = $test_info[$_POST["items"][$i]][2];
                                        $_POST[$groupid] =   $test_info[$_POST["items"][$i]][0];
                                    }
                                    
                                    #only HEMA, CHEM and SERO, CLINICAL MICROSCOPY can be saved in HCLAB
									$hclab_grp = array("H", "I", "C");
									#$hclab_grp = array("H", "I", "C","U");
									#PERIPHERAL BLOOD SMEAR, BONE MARROW SMEAR are not included to be post in HCLAB
									$not_included_serv = array("PBS", "BMA","BMS");  
                                    #edited by VAN 06-02-09
                                    #ER and IN Patients
                                    #$patient_type_notincluded = array("1","3","4");
                                    $patient_type_notincluded = array();
                                    
									$rowLab = $srvObj->getServiceInfo($_POST["items"][$i]);
									if (($_POST[$sampleid]) && (in_array($_POST[$groupid], $hclab_grp))){
										#if (($_POST[$groupid]=='H')&&(in_array($_POST["items"][$i], $not_included_serv))){
										if (($_POST[$groupid]=='H')&&(in_array($_POST["items"][$i], $not_included_serv))&&(in_array($patient['encounter_type'], $patient_type_notincluded))){	
											#nothing to do
										}else{
											/*
											$labservObj = $srvObj->getServiceInfo(addslashes($_POST["items"][$i]));
											if (($patient['encounter_type'] == 2) || empty($patient['encounter_type'])){
												#$_POST["items"][$i] = "O".$_POST["items"][$i];
												$rowLab = $srvObj->getServiceInfo($_POST["items"][$i]);
												$_POST["items"][$i] = $rowLab['oservice_code'];
											}	
										
											$bulk_HCLAB[] = array(addslashes($_POST["items"][$i]),addslashes($labservObj['name'])," ");	
											*/
											if ($mode=='save'){
												$labservObj = $srvObj->getServiceInfo(addslashes($_POST["items"][$i]));
												if (($patient['encounter_type'] == 2) || empty($patient['encounter_type'])){
													#$_POST["items"][$i] = "O".$_POST["items"][$i];
													#$rowLab = $srvObj->getServiceInfo($_POST["items"][$i]);
													$_POST["items"][$i] = $rowLab['oservice_code'];
											
													#echo "<br>itemB = ".$_POST["items"][$i];
												}	
									
												$bulk_HCLAB[] = array(addslashes($_POST["items"][$i]),addslashes($labservObj['name'])," ");	
												#print_r($bulk_HCLAB);
												#exit();
											}elseif ($mode=='update'){
												if ($prev_request)
													$prevrequest = explode(",",$prev_request);
									
												if (in_array($_POST["items"][$i],$prevrequest)){
													#nothing to do.. already in the hclab list
												}else{
									
													$labservObj = $srvObj->getServiceInfo(addslashes($_POST["items"][$i]));
													if (($patient['encounter_type'] == 2) || empty($patient['encounter_type'])){
														#$_POST["items"][$i] = "O".$_POST["items"][$i];
														#$rowLab = $srvObj->getServiceInfo($_POST["items"][$i]);
														$_POST["items"][$i] = $rowLab['oservice_code'];
													}	
									
													$bulk_HCLAB[] = array(addslashes($_POST["items"][$i]),addslashes($labservObj['name'])," ");	
												}
											}
										}
									}
								}	
							}	
							#print_r($bulk_HCLAB);
							#echo "second";
							#commented by VAN 08-25-08 for the meantime
							if (sizeof($bulk_HCLAB)){
								if ($saveHCLAB)
									$ok_insertHCLAB = $srvObj->insert_Orderno_HCLAB($new_order_no, $ref_no);
								
								if ($hclabObj->isExists($ref_no)){
									#echo "<br>exist granted = <br>".$hclabObj->sql;
									$hclabObj->updateOrderH_to_HCLAB($data_HCLAB);
									#echo "<br>sql update granted = <br>".$hclabObj->sql;
								}else{
								
									#echo "not exists granted = <br>".$hclabObj->sql;
									$hclabObj->addOrderH_to_HCLAB($data_HCLAB);
									#echo "<br>sql add granted = <br>".$hclabObj->sql;
								}
							
                                #edited by VAN 06-02-09
								$hclabObj->clearOrderList_to_HCLAB($ref_no);
								#$hclabObj->clearOrderList_to_HCLAB($ref_no, $to_be_deleted);
                                
								#echo "<br>sql delete details granted = <br>".$hclabObj->sql;
								$hclabObj->addOrders_to_HCLAB($ref_no, $bulk_HCLAB);
								#echo "<br>sql add details granted = <br>".$hclabObj->sql;
							}
						}	
					}
				
				}else{
					# can't connect to HCLAB
					echo '<em class="warn">Sorry, HCLAB connection failed..</em>';
				}	
				
			}	
				
				# Bulk write discounts
				
				$bulk = array();
				foreach ($_POST["discount"] as $i=>$v) {
					if ($v) $bulk[] = array($v);
				}
					
				if ($bulk!=NULL){
					$srvObj->clearDiscounts($data['refno']);
					$srvObj->addDiscounts($data['refno'],$bulk);
				}
				
				global $db;
				print_r($db->ErrorMsg());
			} #end of if ($_POST["items"]!=NULL)
			
			if ($mode=='save')
				$smarty->assign('sysInfoMessage',"Service item successfully created.");
			elseif ($mode=='update')
				$smarty->assign('sysInfoMessage',"Service item successfully updated.");		
		}
		else {
			$errorMsg = $db->ErrorMsg();
			#echo "error = ".$errorMsg;
			if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
				$smarty->assign('sysErrorMessage','<strong>Error:</strong> A service with the same request number already exists in the database.');
				#$smarty->assign('sWarning',"<strong>Error:</strong> $errorMsg");
			#elseif (strpos(strtolower($errorMsg), "latest billing") !== FALSE)
				#$smarty->assign('sWarning',"<strong>Error:</strong> $errorMsg");
			else{
				if ($errorMsg!=NULL)
					$smarty->assign('sysErrorMessage',"<strong>Error:</strong> $errorMsg");
				else	
					$smarty->assign('sysErrorMessage',"<strong>Error:</strong> Request must have at least one service.");
			}
		} 
	}
	
if ($saveok) {
	
}

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Clinics :: New Test Request");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 #$smarty->assign('breakfile',$breakfile);
	#edited by VAN 03-06-08
	#echo "popup = ".$popUp;
	if ($popUp!='1'){
		 # href for the close button
		 $smarty->assign('breakfile',$breakfile);
	}else{
	
		# CLOSE button for pop-ups
		 $smarty->assign('breakfile','javascript:window.parent.cClick();');
		$smarty->assign('pbBack','');
	}

 # Window bar title
 $smarty->assign('sWindowTitle',"Clinics :: New Test Request");

 # Assign Body Onload javascript code

 #edited by VAN 02-06-08
 #$onLoadJS='onLoad="preSet();refreshDiscount();"'; 
 
 $onLoadJS='onLoad="preSet();refreshDiscount();ShortcutKeys();"'; 
 
 #echo "onLoadJS = ".$onLoadJS;
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

ob_start();
	 # Load the javascript code
?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	background-color:#0000ff;
	border:1px solid #4d4d4d;
}
.olcg {
	background-color:#aa00aa; 
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffcc; 
	text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
	font-family:Arial; font-size:13px; 
	font-weight:bold; 
	color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}
.olfgright {text-align: right;}
.olfgjustify {background-color:#cceecc; text-align: justify;}
.olfgleft {background-color:#cceecc; text-align: left;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style> 

			<!-- START for setting the DATE (NOTE: should be IN this ORDER) -->
<script type="text/javascript" language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>

<!--added by VAN 02-06-08-->
<!--for shortcut keys -->
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />

<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script language="javascript" src="<?=$root_path?>js/dtpick_care2x.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/request-gui.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	function openOrderTray() {
		window.open("seg-request-tray.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>&labarea=<?=$lab_area?>","patient_select","width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
	}
	
	// added by VAN 02-06-08
	//--------------------------------------
	
	function ShortcutKeys(){
		shortcut.add('Ctrl+Shift+N', NewRequest,
								{
									'type':'keydown',
									'propagate':false,
								}
						);

 		shortcut.add('Ctrl+Shift+L', RequestList,
							{
								'type':'keydown',
								'propagate':false,
							}
						 )

 		shortcut.add('Ctrl+Shift+A', AddItem,
							{
								'type':'keydown',
								'propagate':false,
							}
						 )
		/*				 
		shortcut.add('Shift+U', SelectPatient,
							{
								'type':'keydown',
								'propagate':false,
							}
						 )				
		*/
		shortcut.add('Ctrl+Shift+R', function (){window.location.href=window.location.href},
							{
								'type':'keydown',
								'propagate':false,
							}
						 )						  
						 
		shortcut.add('Ctrl+Shift+E', emptyTray,
							{
								'type':'keydown',
								'propagate':false,
							}
						 )						  
		
		shortcut.add('Ctrl+Shift+S', prufform,
							{
								'type':'keydown',
								'propagate':false,
							}
						 )	
			
		shortcut.add('Ctrl+Shift+M', BackMainMenu,
							{
								'type':'keydown',
								'propagate':false,
							}
						 )				 					  				 				 
		/*				 
		shortcut.add('Shift+C', jsCalendarSetup,
							{
								'type':'keydown',
								'propagate':false,
							}
						 )						  				 				 				 
		*/				 
	}
	
	//added by VAN 03-07-08
	function RepeatRequest(){
		//alert('RepeatRequest');
		//urlholder="seg-lab-request-new.php<?=URL_APPEND?>&user_origin=<?=$user_origin;?>&repeat=1&prevrefno=<?=$Ref;?>&serv_code=<?=stripslashes($service_code);?>&paid=1&popUp=1";
		//window.location.href=urlholder;
	}
	
	function BackMainMenu(){
		urlholder="labor.php<?=URL_APPEND?>";
		if (document.getElementById('popUp').value==1)
			window.parent.location.href=urlholder;
		else
			window.location.href=urlholder;
	}
	
	function NewRequest(){
		urlholder="seg-lab-request-new.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
		window.location.href=urlholder;
	}
	
	function RequestList(){
		urlholder="seg-lab-request-new-list.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
		window.location.href=urlholder;
	}
	
	function AddItem(){
		
		var area;
		var dr_nr = '<?=$dr_nr?>';
		var dept_nr = '<?=$dept_nr?>';
        var labarea = '<?=$lab_area?>';
		
		if (dr_nr=="")
			dr_nr = 0;
			
		if (dept_nr=="")
			dept_nr = 0;	
			
		if(($('patient_enctype').innerHTML=='ER PATIENT')&&($("iscash1").checked==false))
			document.getElementById('area').value = "ER";
		else
			document.getElementById('area').value = "";
		
		return overlib(
          OLiframeContent('seg-request-tray.php?area='+document.getElementById('area').value+'&dr_nr='+dr_nr+'&dept_nr='+dept_nr+'&labarea='+labarea, 600, 440, 'fOrderTray', 1, 'auto'),
          						WIDTH,440, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, MODAL, 
									CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 >',
						         CAPTIONPADDING,4, CAPTION,'Add service item from request tray',
						         MIDX,0, MIDY,0, 
						         STATUS,'Add service item from request tray');
	}
	//modules/registration_admission/seg-select-enc.php?$var_qry&var_include_enc=1
	/*
	function SelectPatient(){
		return overlib(
        	 OLiframeContent('seg-lab-select-enc.php', 700, 400, 'fSelEnc', 1, 'auto'),
 					        	   WIDTH,700, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, MODAL, 
									CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 >',
        	 						CAPTIONPADDING,4, CAPTION,'Select registered person',
						         MIDX,0, MIDY,0, 
						         STATUS,'Select registered person'); return false;
	}
	*/
	
	function jsCalendarSetup(){
		Calendar.setup ({
			//inputField : "orderdate", ifFormat : "<?=$phpfd?>", showsTime : false, button : "orderdate_trigger", singleClick : true, step : 1
			displayArea : "show_orderdate",
			inputField : "orderdate", 
			ifFormat : "<?="%Y-%m-%d %H:%M"?>", 
			daFormat : "<?="	%B %e, %Y %I:%M%P"?>", 
			showsTime : true, 
			button : "orderdate_trigger", 
			singleClick : true, 
			step : 1
		});
	}

	//-----------------------------------------------
		
	//-----------added by VAN ---------------
	
	function CheckRepeatInfo(){
		if (document.getElementById('repeat').checked){
			document.getElementById('repeatinfo01').style.display = '';
			document.getElementById('repeatinfo02').style.display = '';
			document.getElementById('repeatinfo03').style.display = '';
			document.getElementById('repeatinfo04').style.display = '';
			document.getElementById('repeatinfo05').style.display = '';
			document.getElementById('show-discount').value = formatNumber(0,2);
		}else	{
			document.getElementById('repeatinfo01').style.display = 'none';
			document.getElementById('repeatinfo02').style.display = 'none';
			document.getElementById('repeatinfo03').style.display = 'none';
			document.getElementById('repeatinfo04').style.display = 'none';
			document.getElementById('repeatinfo05').style.display = 'none';
		}	
	}	
	
	//function preSet($mod){
	function preSet(){
		if($('mode').value=='update'){	
			var count = $('index').value;
			//alert(count);
			document.getElementById('counter').innerHTML = count;
	  	}
		
		CheckRepeatInfo();
		if ((document.getElementById('view_from').value == 'ssview') && (document.getElementById('paidCash').value == 0)){
			document.getElementById('show-discount').readOnly = false;
			document.getElementById('discountbtn').style.display = '';
		}else{
			document.getElementById('show-discount').readOnly = true;
			document.getElementById('discountbtn').style.display = 'none';
		}	
		
		if ($("iscash1").checked){
			document.getElementById('is_cash').value = 1;
			//$('type_charge').style.display = 'none';
			$('type_charge').style.display = '';
			
		}else{
			document.getElementById('is_cash').value = 0;	
			$('type_charge').style.display = '';
		}	
	}
	
	function resetRefno(){
		document.getElementById('refno').value = document.getElementById('lastrefno').value;
	}
	
-->
</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
?>
<script type="text/javascript">
	function eDiscount(amount,bol){
		//edited by VAN 03-10-08
		document.getElementById('show-discount').readOnly = bol;
		if(bol){	
			document.getElementById('discountbtn').style.display = 'none';
		}else{
			document.getElementById('discountbtn').style.display = '';
		}
	}
	
	function saveDiscounts(){
		var refno, amtDiscount, encoderId; 
		refno = document.getElementById("refno").value;
		amtDiscount = document.getElementById("show-discount").value;
		amtDiscount = amtDiscount.replace(",","")
		encoderId = document.getElementById("encoder_id").value;
		if((amtDiscount == '')||(amtDiscount == 0)||isNaN(amtDiscount)){
			alert("Please enter discount.");
			$('show-discount').value=$F('latest_valid_show-discount');//reset to the lastest valid value
			document.getElementById('show-discount').focus();
		}else{
			//alert("save discounts value " + amtDiscount + " refno =" + refno + "\n encoder =" + encoderId );	
			if (refreshDiscount()){
				xajax_setCharityDiscounts(refno, encoderId, amtDiscount);
				$('latest_valid_show-discount').value=$F('show-discount');
			}else{
				$('show-discount').value=$F('latest_valid_show-discount');//reset to the lastest valid value
				refreshDiscount();
			}	
		}
	}
	
	//added by VAN 06-24-08
	function saveDiscounts2(){
		inputform.submit();
	}
	
</script>
	
<?php

$sTemp = ob_get_contents();
	
	if(($_GET["mode"]=="update") && $_GET["update"]){
		if (!isset($_GET["ref"])) {
			die("No reference number specified");
			exit;
		}
	}	
	
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages

# to determine if at least one service in the request is paid
	$hasPaid = $_GET['paid'];

# to determine if the social service open or access the form
	$view_from = $_GET['view_from'];
	$social_display = 'style="display:none"';

# Render form values
if (isset($_POST["submited"]) && !$saveok) {
	$smarty->assign('sRefNo','<input name="refno" id="refno" type="text" size="8" readonly="1" value="'.$_POST['refno'].'" style="font:bold 12px Arial"/>');
	
	#added by VAN 06-02-08
	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";
	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);
	
	$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
											
	$count=0;
	$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
	$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');

	$smarty->assign('sDiscountShow','<input type="checkbox" disabled name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' ><label for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
	$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($_POST["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
	
	$result = $srvObj->getChargeType();
	$options="";
	if (empty($_POST['type_charge']) || ($_POST['type_charge']==0))
		$_POST['type_charge'] = 0;
		
	while ($row=$result->FetchRow()) {
		if ($_POST['type_charge']==$row['id'])
			$checked = "selected";
		else
			$checked = "";
			
		$options.='<option value="'.$row['id'].'" '.$checked.' >'.$row['charge_name'].'</option>';
	}
	
	$smarty->assign('sChargeTyp',
								"<select class=\"jedInput\" name=\"type_charge\" id=\"type_charge\">
								     $options
								 </select>");
								 
	#---------------------
	
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.stripslashes(trim($_POST['ordername'])).'" style="font:bold 12px Arial; float:left; " readonly/>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" readonly>'.stripslashes($_POST['orderaddress']).'</textarea>');
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').'/>Normal');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').'/>Urgent');
	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic"></textarea>');
	
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"7\">Request list is currently empty...</td>
				</tr>");
				
}elseif ((isset($_POST["submited"]) && $saveok) || ((($mode="update") && $update))){
	# Fetch order data
	#echo "here";
	if ($_POST["ref"]!=NULL)
	 	$Ref = $_POST["ref"];
	elseif ($_GET["ref"]!=NULL)
		$Ref = $_GET["ref"];
	else
		$Ref = $data["refno"];
	
	#-------------------
	# check if this request is already paid or not
	$sql = "SELECT pr.ref_no,pr.service_code 
	            FROM seg_pay_request AS pr
				INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
			  WHERE pr.ref_source = 'LD' AND pr.ref_no = '".$Ref."'
			   AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')
			  UNION
			  SELECT gr.ref_no,gr.service_code  FROM seg_granted_request AS gr
           WHERE gr.ref_source = 'LD' AND gr.ref_no = '".$Ref."'";
	#echo "sql = ".$sql;
	$res=$db->Execute($sql);
	$row2=$res->RecordCount();
	
	#added by VAN 03-07-08
	$sqlPaid = "SELECT pr.or_no, pr.ref_no,pr.service_code 
				FROM seg_pay_request AS pr
				INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
			   WHERE pr.ref_source = 'LD' AND pr.ref_no = '".$Ref."' 
			   AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')
			   LIMIT 1";
		#
	$resPaid=$db->Execute($sqlPaid);
	$rowPaid=$resPaid->RecordCount();
	$resultPaid = $resPaid->FetchRow();

	if ($resultPaid['or_no'])
		$paidCash = 1;
	else	
		$paidCash = 0;

	
	if ($row2==0)
		$hasPaid = 0;
	else
		$hasPaid = 1; 
	#---------------------	

	$infoResult = $srvObj->getOrderInfo($Ref);
	#echo $srvObj->sql;
	$saved_discounts = $srvObj->getOrderDiscounts($Ref);
	if ($infoResult)	$info = $infoResult->FetchRow();
	
	#added by VAN 06-24-08
	#if social worker
	if ($_GET['view_from']){
		if ($info["encounter_type"]==2)
			$ss = $objSS->getPatientSocialClass($info["pid"], 1);
		else
			$ss = $objSS->getPatientSocialClass($info["pid"], 0);	
		
        if (empty($discountid)){	
		    $discountid = $ss['discountid'];	
		    $discount = $ss['discount'];	
        }   
	}
	#------------------------
		
# Render form values
	$readOnly = "readonly";
	
	if (empty($info['pid']) || !$info['pid']){
		$request_name = stripslashes(trim($info['ordername']));
	}else{
		# in case there is an updated profile of the person
		#$request_name = trim($info['name_first']).' '.trim($info['name_last']);
		$request_name = trim($info['name_last']).", ".trim($info['name_first'])." ".trim($info['name_middle']);
	}
	$smarty->assign('sRefNo','<input name="refno" id="refno" readonly="1" type="text" size="8" value="'.$Ref.'" style="font:bold 12px Arial"/>');

	if ($info['serv_dt']!='0000-00-00') {
		$requestDate = $info['serv_dt']." ".$info['serv_tm'];
		$submitted = 1;
	}
	
	$hasResult = $srvObj->hasResult($Ref);
	
	#added by VAN 06-02-08
	if ($info['encounter_type']==1){
		$enctype = "ER PATIENT";
		$location = "EMERGENCY ROOM";
		$info['encounter_type'] = 1;
	}elseif ($info['encounter_type']==2){
		#$enctype = "OUTPATIENT (OPD)";
		$enctype = "OUTPATIENT";
		$info['encounter_type'] = 2;
		$dept = $dept_obj->getDeptAllInfo($info['current_dept_nr']);
		$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
	}elseif (($info['encounter_type']==3)||($info['encounter_type']==4)){
		if ($info['encounter_type']==3)
			$enctype = "INPATIENT (ER)";
		elseif ($info['encounter_type']==4)
			$enctype = "INPATIENT (OPD)";
				
		$ward = $ward_obj->getWardInfo($info['current_ward_nr']);
		#echo "sql = ".$ward_obj->sql;
		$location = strtoupper(strtolower(stripslashes($ward['name'])))."&nbsp;&nbsp;&nbsp;Room # : ".$info['current_room_nr'];
	}else{
		$enctype = "WALK-IN";
		$dept = $dept_obj->getDeptAllInfo($info['current_dept_nr']);
		$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
	}
	
	/*
	if ((!$view_from)&&($info["discountid"])){
		$discountid = $info["discountid"]; 
	}else{
		$discountid = $discountid;
	}
	
	if((!($discountid))&&($info['encounter_type'])){
		#echo "enc = ".$info['encounter_type'];
		if ($info['encounter_type']==2)
			$ss = $objSS->getPatientSocialClass($info['pid'], 1);
		else
			$ss = $objSS->getPatientSocialClass($info['pid'], 0);	
			
		$discountid = $ss['discountid'];	
		$discount = $ss['discount'];	
	}
	
	#added by VAN 07-05-08
	$ssInfo = $objSS->getSSClassInfo($discountid);
	if ($ssInfo['parentid']=='D')
		$discountid = $ssInfo['parentid'];
	#------------	
	#added by VAN 06-25-08
	
	#if (trim($info['senior_ID']))
	if (empty($pid))
		$pid = $info['pid'];
		
	$ss_sc = $objSS->getPatientSocialClass($pid, 0);
	#$discountid_sc = $ss_sc['discountid'];
	#echo "sql = ".$objSS->sql;
	#echo "id = ".$ss_sc['discountid'];
	if ($ss_sc['discountid']=='SC')
		$_POST["issc"] = 1;
	else	
		$_POST["issc"] = 0;
	*/	
	#------------------	
	#echo "here";
   # echo "pid = ".$pid;
   $sql_discount = "SELECT discountid  FROM seg_charity_grants_pid   WHERE pid='".$pid."' ORDER BY grant_dte DESC LIMIT 1";
  
   $res_discount=$db->Execute($sql_discount);
   $discount_info=$res_discount->FetchRow();
    
   $orig_discountid = $discount_info['discountid'];  
       
	if ((empty($discountid))&&(empty($view_from)))
		$discountid = $info["discountid"];
  
    $ssInfo = $objSS->getSSClassInfo($discountid);
	if ($ssInfo['parentid']=='D')
		$discountid = $ssInfo['parentid'];	
		
	if (empty($pid))
		$pid = $info['pid'];
		
	$ss_sc = $objSS->getPatientSocialClass($pid, 0);
	if ($ss_sc['discountid']=='SC')
		$_POST["issc"] = 1;
	else	
		$_POST["issc"] = 0;	
		
	if (($_POST["issc"])&&(trim($info['encounter_type'])=="")){
		$discount = 0.20;
	}	
	
	if ($ptype=='phs')
		$discountid = 'D';
		
	#$smarty->assign('sClassification',(($info['discountid']) ? $info['discountid'] : 'None'));
	$smarty->assign('sClassification',(($discountid) ? $discountid : 'None'));
	$smarty->assign('sPatientType',(($enctype) ? $enctype : 'None'));
	$smarty->assign('sPatientLoc',(($location) ? $location : 'None'));
	#is_medico
	$smarty->assign('sPatientMedicoLegal',(($info['is_medico']) ? "YES" : 'NO'));
    
    #---------------------
	
	#added by VAN 03-05-09
	if (($info["admission_dt"])&&(($info["admission_dt"]!='0000-00-00 00:00:00')||(empty($info["admission_dt"]))))
		$admission_dt = date("m/d/Y h:i A",strtotime($info['admission_dt']));
	else
		$admission_dt = "";
	
	if (($info["discharge_date"])&&(($info["discharge_date"]!='0000-00-00')||(empty($info["discharge_date"]))))	
		$discharge_date = date("m/d/Y h:i A",strtotime($info['discharge_date']));
	else
		$discharge_date = "";
					
	$smarty->assign('sPatientHRN',$pid);
	$smarty->assign('sAdmissionDate',$admission_dt);
	$smarty->assign('sDischargedDate',$discharge_date);
	#--------------
	
	if (($hasPaid==1)||($view_from=='ssview')||($hasResult)){ 
		$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;" value="Clear" onclick="clearEncounter()" disabled />');
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" disabled value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" style="font-weight:bold;">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" disabled value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label for="iscash0" style="font-weight:bold;">Charge</label>');
		$smarty->assign('sDiscountShow','<input type="checkbox" disabled name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' ><label for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
		$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" disabled type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($info["is_tpl"]=="1")?'checked="checked" ':'').'/><label for="is_tpl" style="color:#006600; font-weight:bold;">To pay later</label></span>');
		$result = $srvObj->getChargeType();
		$options="";
		if (empty($info['type_charge']) || ($info['type_charge']==0))
			$info['type_charge'] = 0;
		
		while ($row=$result->FetchRow()) {
			if ($info['type_charge']==$row['id'])
				$checked = "selected";
			else
				$checked = "";
			
			$options.='<option value="'.$row['id'].'" '.$checked.' >'.$row['charge_name'].'</option>';
		}
	
		$smarty->assign('sChargeTyp',
								"<select class=\"jedInput\" name=\"type_charge\" id=\"type_charge\" disabled>
								     $options
								 </select>");
								 
		#---------------------
		
		$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$request_name.'" style="font:bold 12px Arial; float:left;" readonly/>');
	    $smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" readonly>'.stripslashes($info['orderaddress']).'</textarea>');
		$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority" value="0" disabled '.(($info["is_urgent"]!="1")?'checked="checked" ':'').'/>Normal');
		$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority" value="1" disabled '.(($info["is_urgent"]=="1")?'checked="checked" ':'').'/>Urgent');
		$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic" readonly>'.stripslashes($info["comments"]).'</textarea>');

		#added by VAN 06-02-08
		$dbtime_format = "Y-m-d H:i";
		$fulltime_format = "F j, Y g:ia";
		$curDate = date($dbtime_format);
		$curDate_show = date($fulltime_format);
	
		$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($requestDate)) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($requestDate)) : $curDate).'" style="font:bold 12px Arial">');
		$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' align="absmiddle">');
		
	}else{
        
		if ($area=="ER"){
			#edited by VAN 05-29-09, delete disabled
			$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" style="font-weight:bold;">Cash</label>');
			$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label for="iscash0" style="font-weight:bold;">Charge</label>');
			$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;" value="Clear" onclick="clearEncounter()" disabled />');
		
		}elseif ($area=="clinic"){
		    /*
			$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" style="font-weight:bold;">Cash</label>');
			$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" disabled value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label for="iscash0" style="font-weight:bold;">Charge</label>');
			$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;" value="Clear" onclick="clearEncounter()" disabled />');
			*/
            
            if ($is_dr){
               if (($patient['encounter_type']==3)||($patient['encounter_type']==4)){
                    $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                    $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
               }elseif($patient['encounter_type']==1){
                     #$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($pid)?'':'checked="checked" ').' '.(($pid)?'disabled="disabled" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                     #$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($pid)?'checked="checked" ':'').' '.(($pid)?'':'disabled="disabled" ').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
                     $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($pid)?'':'checked="checked" ').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                     $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($pid)?'checked="checked" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
               }elseif($patient['encounter_type']==2){
                     $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                     $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" disabled onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
               }
            
            }else{
                $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                #edited by VAN 05-29-09 delete disabled
				$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
            }	
            $smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;" value="Clear" onclick="clearEncounter()" disabled />');
		}else{	
            
			$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
			$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
			$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;cursor:pointer" value="Clear" onclick="clearEncounter()"'.(($info['pid'])?'':' disabled="disabled"').' />');
		}

		$smarty->assign('sDiscountShow','<input type="checkbox" disabled name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' ><label for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
		$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($info["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
		$result = $srvObj->getChargeType();
		$options="";
		if (empty($info['type_charge']) || ($info['type_charge']==0))
			$info['type_charge'] = 0;
		
		while ($row=$result->FetchRow()) {
			if ($info['type_charge']==$row['id'])
				$checked = "selected";
			else
				$checked = "";
			
			$options.='<option value="'.$row['id'].'" '.$checked.' >'.$row['charge_name'].'</option>';
		}
	
		$smarty->assign('sChargeTyp',
								"<select class=\"jedInput\" name=\"type_charge\" id=\"type_charge\">
								     $options
								 </select>");
		
		#---------------------
		
		
		$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$request_name.'" style="font:bold 12px Arial; float:left;" '.$readOnly.'/>');
		$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" '.$readOnly.'>'.stripslashes($info['orderaddress']).'</textarea>');
		$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority" value="0" '.(($info["is_urgent"]!="1")?'checked="checked" ':'').'/>Normal');
		$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority" value="1" '.(($info["is_urgent"]=="1")?'checked="checked" ':'').'/>Urgent');
		$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.stripslashes($info["comments"]).'</textarea>');

		#edited by VAN 06-01-08
		$dbtime_format = "Y-m-d H:i";
		$fulltime_format = "F j, Y g:ia";
		$curDate = date($dbtime_format);
		$curDate_show = date($fulltime_format);
	
		$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($requestDate)) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($requestDate)) : $curDate).'" style="font:bold 12px Arial">');
		$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
		
	}	

	$count=0;
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$info["pid"].'"/>');
	
	# Note: make a class function for this part later
	$result = $srvObj->getOrderitems($Ref);
	$rows=array();
	while ($row=$result->FetchRow()) {
		$rows[] = $row;
	}
	
	# get the discount in seg_charity_grants
	$sql8 = "SELECT * FROM seg_charity_grants_pid
	         WHERE pid ='".$info['pid']."' 
				ORDER BY grant_dte DESC LIMIT 1"; 			
	$res8=$db->Execute($sql8);
	$granted_discount=$res8->FetchRow();	
    $orig_discountid = $granted_discount["discountid"];
   	$prev_request = "";
    $ispaid =0;
	foreach ($rows as $i=>$row) {
		if ($row) {
			$count++;
			$alt = ($count%2)+1;
			
			#added by VAN 08-29-08
			$prev_request .= $row['service_code'].',';
			#-----------------------
			
			if ($info["is_cash"]){
				
				$prc=$row['price_cash'];
				$prc_orig=$row['price_cash_orig'];
			}else{
				$prc_orig=$row['price_charge'];
				$prc=$row['price_charge'];
			}	
			
			if (is_numeric($row['request_doctor'])){
			   $doctor = $pers_obj->getPersonellInfo($row['request_doctor']);
				$doctor_name = stripslashes($doctor["name_first"])." ".stripslashes($doctor["name_2"])." ".stripslashes($doctor["name_last"]);
				$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
				$doctor_name = htmlspecialchars($doctor_name);
			}else{
				$doctor_name = $row['request_doctor'];
			}	
			
			if (is_numeric($row['request_dept'])){	
				$dept = $dept_obj->getDeptAllInfo($row['request_dept']);
				$dept_name = stripslashes($dept['name_formal']);
			}else{
				$dept_name = stripslashes($row['request_dept']);
			}	
			
			$sql2 = "SELECT pr.ref_no,pr.service_code 
						FROM seg_pay_request AS pr
						INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
						WHERE pr.ref_source = 'LD' 
						AND pr.ref_no = '".$Ref."'
						AND pr.service_code = '".$row['service_code']."'
						AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')
						UNION
						SELECT gr.ref_no,gr.service_code FROM seg_granted_request AS gr
						WHERE gr.ref_source = 'LD' 
						AND gr.ref_no = '".$Ref."'
						AND gr.service_code = '".$row['service_code']."'
						UNION
						SELECT l.refno, l.service_code 
						FROM seg_lab_servdetails AS l
						INNER JOIN seg_lab_serv AS r ON r.refno=l.refno
						WHERE r.type_charge<>0 AND l.refno = '".$Ref."' AND l.service_code = '".$row['service_code']."'";
			#echo "<br>".$sql2;
		  	$res2=$db->Execute($sql2);
		   $rowpaid=$res2->RecordCount();
			
			$hasResult = $srvObj->hasResult($Ref);
			$hasResult_code = $srvObj->hasResult($Ref,$row['service_code']);
			if (($hasResult)&&($repeat!=1)){
				if ($hasResult_code){
					$delrow = '<img src="../../images/btn_donerequest.gif" align="absmiddle" border="0"/>'; 
				}else{
					$delrow = '<img src="../../images/btn_undone.gif" align="absmiddle" border="0"/>'; 	
				}	
			}else{	
				if (($repeat)||($info['parent_refno'])){
					$delrow = '<img src="../../images/btn_repeat.gif" align="absmiddle" border="0"/>'; 
				}else{
					if ($rowpaid==0){
						if (($hasPaid==1)||($view_from=='ssview')){ 
							$delrow = '<img src="../../images/btn_unpaiditem.gif" border="0"/>';
						}else{
							$delrow = '<a href="javascript: nd(); removeItem(\''.$row['service_code'].'\')"><img src="../../images/btn_delitem.gif" border="0"/></a>';
						}	
                        
                        $disabled = "disabled";    
                        $ispaid += 0;
					}else{
					
						$delrow = '<img src="../../images/btn_paiditem.gif" align="absmiddle" border="0"/>'; 
					    $disabled = "";  
                        $ispaid += 1;  
                    }	
				}
			}	

			
			$sql3 = "SELECT * FROM seg_lab_services WHERE service_code = '".$row['service_code']."'"; 
			$res3=$db->Execute($sql3);
		    $social=$res3->FetchRow();
			
			$toolTipText = "Requesting doctor: <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$doctor_name ."<br>".
							    "Department: <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$dept_name ."<br>".	
							    "Clinical Impression: <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".stripslashes(strtoupper($row['clinical_info']));
			
			$notallowedchar = array("+", "-", "*","/","%");
			$code = str_replace($notallowedchar, "", $row['service_code']);
			
			$toolTipTextHandler = "onMouseOver=\"return overlib($(toolTipText".$code.").value, CAPTION,'Details',  
					                 TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, 'oltxt', CAPTIONFONTCLASS, 'olcap', 
					                 WIDTH, 250,FGCLASS,'olfgleft',FGCOLOR, '#bbddff');\" onmouseout=\"nd();\"";
										  
				$nonSocialized='';
				
				if ($social['is_socialized']==0){
					$nonSocialized='<img src="../../images/btn_nonsocialized.gif" border="0" onClick=""'.
									   ' onMouseOver="return overlib(\'This is a non-socialized service which means..secret!\', CAPTION,\'Non-socialized Service\',  '.
								      '  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '.
								      '  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();">';
				   
					$social_display = 'style="display:\'\'"';
					
					#$('socialServiceNotes').style.display='';
					#added by VAN 07-05-08
					if ($info["is_cash"]){
                        #echo "here = ".$orig_discountid;
                        if (($orig_discountid=='DMC') || ($orig_discountid=='DMCDep') || ($orig_discountid=='DMCCD') || ($orig_discountid=='DMCConDep') || ($orig_discountid=='DMCPGI'))
						    $prc = 0;
                        else
                            $prc = $social['price_cash']; 
							
							#echo "prc = ".$prc;   
					}else{	
						$prc = $social['price_charge'];
					}
					$row['price_cash'] = $prc;
					$sservice_price	= $prc;
					
					#----------------------	
				}
			#dicounted price
			$sql4 = "SELECT * FROM seg_service_discounts WHERE discountid='C1' AND service_code = '".$row['service_code']."'"; 
			$res4=$db->Execute($sql4);
		    $discount_priceC1=$res4->FetchRow();
			$rowC1=$res4->RecordCount();
			if ($rowC1!=0){
				if ($discount_priceC1["price"]!=0)
					$cash_C1 = $discount_priceC1["price"];
				else
					$cash_C1 = $row['price_cash'];	
			}else		
				#$cash_C1 = $row['price_cash'];	
				$cash_C1 = "N/A";	
			
			$sql5 = "SELECT * FROM seg_service_discounts WHERE discountid='C2' AND service_code = '".$row['service_code']."'"; 
			$res5=$db->Execute($sql5);
		   $discount_priceC2=$res5->FetchRow();
			$rowC2=$res5->RecordCount();
			if ($rowC2!=0){
				if ($discount_priceC2["price"]!=0)
					$cash_C2 = $discount_priceC2["price"];
				else
					$cash_C2 = $row['price_cash'];
			}else
				#$cash_C2 = $row['price_cash'];
				$cash_C2 = "N/A";
					
			#echo "<br>sql 2= ".$sql5." - ".$cash_C2;
			$sql6 = "SELECT * FROM seg_service_discounts WHERE discountid='C3' AND service_code = '".$row['service_code']."'"; 
			#echo "sql = ".$sql6;
			$res6=$db->Execute($sql6);
		   $discount_priceC3=$res6->FetchRow();
			$rowC3=$res6->RecordCount();
			if ($rowC3!=0){
				if ($discount_priceC3["price"]!=0)
					$cash_C3 = $discount_priceC3["price"];
				else
					$cash_C3 = $row['price_cash'];
			}else		
				$cash_C3 = "N/A";
			
			if ($discountid == 'C1'){
			    $sservice_price = $discount_priceC1["price"];
				 $inSS = $cash_C1;
			}elseif ($discountid == 'C2'){
			    $sservice_price = $discount_priceC2["price"];
				 $inSS = $cash_C2;
			}elseif ($discountid == 'C3'){
			    $sservice_price = $discount_priceC3["price"];
				 $inSS = $cash_C3;
			}
			
			if (($social['is_socialized']==1) && (($discountid=="C1")||($discountid=="C2")||($discountid=="C3"))){
				if ($sservice_price==0){
					if (($hasPaid==1)||($view_from=='ssview')||($hasResult)){ 
						if (($_GET['view_from'])&&($discountid)){	
							
							if (!empty($sservice_price)){
								if ($inSS!="N/A"){
									$tot_price = '<input type="text" id="totprice'.$row['service_code'].'" name="totprice'.$row['service_code'].'" size="10" onKeyDown="keyEnter(event, this,\''.$row['service_code'].'\');" style="text-align:right" onBlur="getSocialPrice(\''.$row['service_code'].'\'); formatNumber(this.value,2); refreshDiscount();" value="'.number_format($prc, 2).'">';
								}else{
									$tot_price = $row['price_cash_orig'] - ($row['price_cash_orig'] * $ss["discount"]);
									$row['price_cash'] = $tot_price;
									$tot_price = number_format($tot_price, 2);
								
								}	
							}else{
								
								if ($inSS!="N/A"){
									$tot_price = number_format($prc, 2);
								}else{
									$tot_price = $row['price_cash_orig'] - ($row['price_cash_orig'] * $ss["discount"]);
									$row['price_cash'] = $tot_price;
									$tot_price = number_format($tot_price, 2);
								}	
							}	
						}else{
							$ssDInfo2 = $srvObj->getSocialDiscountInfo($discountid, $row['service_code']);
							if ($srvObj->count)
								$tot_price = '<input type="text" id="totprice'.$row['service_code'].'" name="totprice'.$row['service_code'].'" size="10" readonly onKeyDown="keyEnter(event, this,\''.$row['service_code'].'\');" style="text-align:right" onBlur="getSocialPrice(\''.$row['service_code'].'\'); formatNumber(this.value,2); refreshDiscount();" value="'.number_format($prc, 2).'">';	
							else
								$tot_price = number_format($prc, 2);
							
							$row['price_cash'] = $prc;
						#echo "prc = ".$prc;
						}
					}else{
						
						#edited by VAN 08-15-08
						if ($info["is_cash"]){
							if (empty($sservice_price))
								$tot_price = number_format($prc, 2);
							else	
								$tot_price = '<input type="text" id="totprice'.$row['service_code'].'" name="totprice'.$row['service_code'].'" size="10" onKeyDown="keyEnter(event, this,\''.$row['service_code'].'\');" style="text-align:right" onBlur="getSocialPrice(\''.$row['service_code'].'\'); formatNumber(this.value,2); refreshDiscount();" value="'.number_format($prc, 2).'">';
						}else{
							$tot_price = number_format($prc, 2);
						}
						$row['price_cash'] = $prc;
					}	
				}else{
					#$tot_price = number_format($prc, 2);
					if ($inSS!="N/A"){
						$tot_price = number_format($sservice_price, 2);
						$row['price_cash'] = $sservice_price;
					}else{	
						#$tot_price = $row['price_cash_orig'] - ($row['price_cash_orig'] * $discount);
						$tot_price = $row['price_cash_orig'] - ($row['price_cash_orig'] * $ss["discount"]);
						$row['price_cash'] = $tot_price;
						$tot_price = number_format($tot_price, 2);
						#echo "p = ".$tot_price;
					}	
				}	
			}else{
				#echo "here";
				#if charity
				
				if ($discountid){
				
					if (($granted_discount["discountid"]=='DMC') || ($granted_discount["discountid"]=='DMCDep') || ($granted_discount["discountid"]=='DMCCD') || ($granted_discount["discountid"]=='DMCConDep') || ($granted_discount["discountid"]=='DMCPGI')){
						$prc = 0;
						$tot_price = number_format($prc, 2);
						$row['price_cash'] = $prc;
                    }else{
						
					    if ($social['is_socialized']!=0){
							#$prc = $row['price_cash_orig'] - ($row['price_cash_orig'] * $ss["discount"]);
							#$prc = $row['price_cash'] - ($row['price_cash'] * $ss["discount"]);
						  if ($view_from=='ssview'){	
							$info["encounter_nr"] = trim($info["encounter_nr"]);
							if (empty($info["encounter_nr"])){
								$ss["discount"] = 0.20;
							}	
							
							$prc = $row['price_cash_orig'] - ($row['price_cash_orig'] * $ss["discount"]);
							$tot_price = number_format($prc, 2);
							$row['price_cash'] = $prc;
						  }else{
						  	$tot_price = number_format($row['price_cash'], 2);
							
						  }	
							#echo "<br>price = ".$row['price_cash'];
						}else
							#$row['price_cash'] = $row['price_cash_orig'];	
							#$row['price_cash'] = $row['price_cash'];	
							$tot_price = number_format($row['price_cash'], 2);
					}	
				}else{
					$tot_price = number_format($prc, 2);
					$row['price_cash'] = $prc;
					
				}	
			}	
			$adjust_amount = 0;
			
			if ($row['is_forward'])
				$checked = "checked";
			else
				$checked = "";	
				
			 if ($info["is_cash"]==0)
				$disabled = "";                                                                                                                                                       
            /*
			$forwarding = '<input type="checkbox" name="withsampleID'.$row['service_code'].'" '.$checked.' id="withsampleID'.$row['service_code'].'" '.(($hasPaid==1)?'disabled="disabled" ':'').' value="1" />
								<input type="hidden" name="group'.$row['service_code'].'" id="group'.$row['service_code'].'" value="'.$row['group_code'].'">';
		    */
            $forwarding = '<input type="checkbox" name="withsampleID'.$row['service_code'].'" '.$checked.' id="withsampleID'.$row['service_code'].'" '.$disabled.' value="1" />
                                <input type="hidden" name="group'.$row['service_code'].'" id="group'.$row['service_code'].'" value="'.$row['group_code'].'">';                    
        #echo "<br>here = ".$disabled;
			
		
			$src .= 
					'<tr class="wardlistrow'.$alt.'" id="row'.$row['service_code'].'">
						<input type="hidden" id="index" name="index" value="'.$srvObj->count.'"/>
						<input type="hidden" name="toolTipText'.$code.'" id="toolTipText'.$code.'" value="'.$toolTipText.'" />
						<input type="hidden" name="sservice[]" id="rowsservice'.$row['service_code'].'" value="'.$social['is_socialized'].'" />
						<input type="hidden" name="price_C1[]" id="rowpriceC1'.$row['service_code'].'" value="'.$cash_C1.'" />
						<input type="hidden" name="price_C2[]" id="rowpriceC2'.$row['service_code'].'" value="'.$cash_C2.'" />
						<input type="hidden" name="price_C3[]" id="rowpriceC3'.$row['service_code'].'" value="'.$cash_C3.'" />
						<input type="hidden" name="price_C1orig[]" id="rowpriceC1orig'.$row['service_code'].'" value="'.$discount_priceC1["price"].'" />
						<input type="hidden" name="price_C2orig[]" id="rowpriceC2orig'.$row['service_code'].'" value="'.$discount_priceC2["price"].'" />
						<input type="hidden" name="price_C3orig[]" id="rowpriceC3orig'.$row['service_code'].'" value="'.$discount_priceC3["price"].'" />
						<input type="hidden" name="items[]" id="rowID'.$row['service_code'].'" value="'.$row['service_code'].'" />
						<input type="hidden" name="pcash[]" id="rowPrcCash'.$row['service_code'].'" value="'.$row['price_cash'].'" />
						<input type="hidden" name="pcashorig[]" id="rowPrcCashorig'.$row['service_code'].'" value="'.$row['price_cash_orig'].'" />
						<input type="hidden" name="pcharge[]" id="rowPrcCharge'.$row['service_code'].'" value="'.$row['price_charge'].'" />
						<input type="hidden" name="requestDoc[]" id="rowDoc'.$row['service_code'].'" value="'.$row['request_doctor'].'" />
						<input type="hidden" name="requestDocName[]" id="rowDocName'.$row['service_code'].'" value="'.$doctor_name.'" />
						<input type="hidden" name="requestDept[]" id="rowDept'.$row['service_code'].'" value="'.$row['request_dept'].'" />
						<input type="hidden" name="requestDeptName[]" id="rowDeptName'.$row['service_code'].'" value="'.$dept_name.'" />
						<input type="hidden" name="isInHouse[]" id="rowHouse'.$row['service_code'].'" value="'.$row['is_in_house'].'" />
						<input type="hidden" name="clinicInfo[]" id="rowInfo'.$row['service_code'].'" value="'.$row['clinical_info'].'" />
						<td class="centerAlign">'.$delrow.'</td>
						<td align="centerAlign">'.$nonSocialized.'</td>
						<td  width="15%" id="id'.$row["service_code"].'" '.$toolTipTextHandler.'>'.$row['service_code'].'</td>
						<td id="name'.$row["service_code"].'" '.$toolTipTextHandler.'>'.$row['name'].'</td>
						<td width="5%" id="is_forward_row'.$row["service_code"].'" align="center">'.$forwarding.'</td>
						<td class="rightAlign" id="prc'.$row["service_code"].'">'.number_format($prc_orig, 2).'</td>
						<td class="rightAlign" id="tot'.$row["service_code"].'">'.$tot_price.'</td>
					</tr>
			';    #number_format($prc, 2)
		}
	}
	if ($src) $smarty->assign('sOrderItems',$src);
	
if ($view_from!='ssview'){	
	$smarty->assign('sViewPDF','<img name="viewfile" id="viewfile" onClick="viewPatientRequest(\''.$info["is_cash"].'\',\''.$info["pid"].'\',\''.$Ref.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'viewpdf.gif','0','left') . ' border="0">');
   # echo "enc = ".$info["encounter_type"]; 
    if (($ispaid) || (($info["encounter_type"]!=2)&&($info["encounter_type"]))){
        $smarty->assign('sClaimStub','<img name="claimstub" id="claimstub" onClick="viewClaimStub(\''.$info["is_cash"].'\',\''.$Ref.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'claim_stub.gif','0','left') . ' border="0">');
    		$withclaimstub = 1;
		}
}		
#-----------added by VAN 12-21-07---
}elseif ((isset($_POST["submited"]) && $saveok) || ((($mode="save") && $repeat))){
	# Fetch order data
	
	if ($_POST["ref"]!=NULL)
	 	$Ref = $_POST["ref"];
	elseif ($_GET["ref"]!=NULL)
		$Ref = $_GET["ref"];
	elseif ($prevrefno!=NULL)
		$Ref = $prevrefno;
	else
		$Ref = $data["refno"];
	
	#-------------------
	
	$sql = "SELECT pr.ref_no,pr.service_code 
				FROM seg_pay_request AS pr
				INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
			  WHERE pr.ref_source = 'LD' AND pr.ref_no = '".$Ref."'
			  AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')
			  UNION
			  SELECT gr.ref_no,gr.service_code  FROM seg_granted_request AS gr
           WHERE gr.ref_source = 'LD' AND gr.ref_no = '".$Ref."'";
	$res=$db->Execute($sql);
	$row2=$res->RecordCount();
	
	#added by VAN 03-07-08
	$sqlPaid = "SELECT pr.or_no, pr.ref_no,pr.service_code 
				FROM seg_pay_request AS pr
				INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
			   WHERE pr.ref_source = 'LD' AND pr.ref_no = '".$Ref."' 
			   AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')
			   LIMIT 1";
					
	$resPaid=$db->Execute($sqlPaid);
	$rowPaid=$resPaid->RecordCount();
	$resultPaid = $resPaid->FetchRow();
	#echo 'or no line 1336= '.$resultPaid['or_no'];
	
	if ($resultPaid['or_no'])
		$paidCash = 1;
	else	
		$paidCash = 0;

	
	if ($row2==0)
		$hasPaid = 0;
	else
		$hasPaid = 1; 
	#---------------------	

	$infoResult = $srvObj->getOrderInfo($Ref);
	$saved_discounts = $srvObj->getOrderDiscounts($Ref);
	if ($infoResult)	$info = $infoResult->FetchRow();
		
# Render form values
	$readOnly = "readonly";
	
	if (empty($info['pid']) || !$info['pid']){
		$request_name = stripslashes(trim($info['ordername']));
	}else{
		# in case there is an updated profile of the person
		#$request_name = trim($info['name_first']).' '.trim($info['name_last']);
		$request_name = trim($info['name_last']).", ".trim($info['name_first'])." ".trim($info['name_middle']);
	}
	
	$smarty->assign('sRefNo','<input name="refno" id="refno" readonly="1" type="text" size="8" value="" style="font:bold 12px Arial"/>');
	
	if ($info['serv_dt']) {
			$time = strtotime($info['serv_dt']);
			$requestDate = date("m/d/Y",$time);
	}
	
	$hasResult = $srvObj->hasResult($Ref);
	
	if (($hasPaid==1)||($view_from=='ssview')||($hasResult)){ 
		$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;" value="Clear" onclick="clearEncounter()" disabled />');
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
		$smarty->assign('sDiscountShow','<input type="checkbox" disabled name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' ><label for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
		$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($info["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
		$result = $srvObj->getChargeType();
		$options="";
		if (empty($info['type_charge']) || ($info['type_charge']==0))
			$info['type_charge'] = 0;
		
		while ($row=$result->FetchRow()) {
			if ($info['type_charge']==$row['id'])
				$checked = "selected";
			else
				$checked = "";
			
			$options.='<option value="'.$row['id'].'" '.$checked.' >'.$row['charge_name'].'</option>';
		}
	
		$smarty->assign('sChargeTyp',
								"<select class=\"jedInput\" name=\"type_charge\" id=\"type_charge\">
								     $options
								 </select>");
								 
		#---------------------
		
		$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$request_name.'" style="font:bold 12px Arial; float:left;" readonly/>');
	    $smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" readonly>'.stripslashes($info['orderaddress']).'</textarea>');
		$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority" value="0" disabled '.(($info["is_urgent"]!="1")?'checked="checked" ':'').'/>Normal');
		$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority" value="1" disabled '.(($info["is_urgent"]=="1")?'checked="checked" ':'').'/>Urgent');
		$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic" readonly>'.stripslashes($info["comments"]).'</textarea>');
		#added by VAN 06-02-08
		$dbtime_format = "Y-m-d H:i";
		$fulltime_format = "F j, Y g:ia";
		$curDate = date($dbtime_format);
		$curDate_show = date($fulltime_format);
	
		$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($requestDate)) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($requestDate)) : $curDate).'" style="font:bold 12px Arial">');
		$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
	
	}else{
		$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;cursor:pointer" value="Clear" onclick="clearEncounter()"'.(($info['pid'])?'':' disabled="disabled"').' />');
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
		$smarty->assign('sDiscountShow','<input type="checkbox" disabled name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' ><label for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
		$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($info["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
		$result = $srvObj->getChargeType();
		$options="";
		if (empty($info['type_charge']) || ($info['type_charge']==0))
			$info['type_charge'] = 0;
		
		while ($row=$result->FetchRow()) {
			if ($info['type_charge']==$row['id'])
				$checked = "selected";
			else
				$checked = "";
			
			$options.='<option value="'.$row['id'].'" '.$checked.' >'.$row['charge_name'].'</option>';
		}
	
		$smarty->assign('sChargeTyp',
								"<select class=\"jedInput\" name=\"type_charge\" id=\"type_charge\">
								     $options
								 </select>");
								 
		#---------------------
		
		$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$request_name.'" style="font:bold 12px Arial; float:left;" '.$readOnly.'/>');
		$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" '.$readOnly.'>'.stripslashes($info['orderaddress']).'</textarea>');
		$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority" value="0" '.(($info["is_urgent"]!="1")?'checked="checked" ':'').'/>Normal');
		$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority" value="1" '.(($info["is_urgent"]=="1")?'checked="checked" ':'').'/>Urgent');
		$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.stripslashes($info["comments"]).'</textarea>');
		
		#added by VAN 06-02-08
		$dbtime_format = "Y-m-d H:i";
		$fulltime_format = "F j, Y g:ia";
		$curDate = date($dbtime_format);
		$curDate_show = date($fulltime_format);
	
		$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($requestDate)) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($requestDate)) : $curDate).'" style="font:bold 12px Arial">');
		$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
	}	

	$count=0;
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$info["pid"].'"/>');
	
	if (empty($serv_code))
		$serv_code = "";
	
	$result = $srvObj->getOrderitems($Ref, $serv_code);
	$rows=array();
	while ($row=$result->FetchRow()) {
		$rows[] = $row;
	}
	
	# get the discount in seg_charity_grants
	$sql8 = "SELECT * FROM seg_charity_grants_pid
	         WHERE pid ='".$info['pid']."' 
				ORDER BY grant_dte DESC LIMIT 1";			
	$res8=$db->Execute($sql8);
    $granted_discount=$res8->FetchRow();	
	$prev_request = "";
    $ispaid = 0;
	foreach ($rows as $i=>$row) {
		if ($row) {
			$count++;
			$alt = ($count%2)+1;
			
			#added by VAN 08-29-08
			$prev_request .= $row['service_code'].',';
			#-----------------------
			
			if ($info["is_cash"]){
				$prc=$row['price_cash'];
				$prc_orig=$row['price_cash_orig'];
			}else{
				$prc_orig=$row['price_charge'];
				$prc=$row['price_charge'];
			}	
			
			if (is_numeric($row['request_doctor'])){
			   $doctor = $pers_obj->getPersonellInfo($row['request_doctor']);
				$doctor_name = stripslashes($doctor["name_first"])." ".stripslashes($doctor["name_2"])." ".stripslashes($doctor["name_last"]);
				$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
				$doctor_name = htmlspecialchars($doctor_name);
			}else{
				$doctor_name = $row['request_doctor'];
			}	
			
			if (is_numeric($row['request_dept'])){	
				$dept = $dept_obj->getDeptAllInfo($row['request_dept']);
				$dept_name = stripslashes($dept['name_formal']);
			}else{
				$dept_name = stripslashes($row['request_dept']);
			}	
			
			$sql2 = "SELECT pr.ref_no,pr.service_code 
						FROM seg_pay_request AS pr
						INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
						WHERE pr.ref_source = 'LD' 
						AND pr.ref_no = '".$Ref."'
						AND pr.service_code = '".$row['service_code']."'
						AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')
						UNION
						SELECT gr.ref_no,gr.service_code FROM seg_granted_request AS gr
						WHERE gr.ref_source = 'LD' 
						AND gr.ref_no = '".$Ref."'
						AND gr.service_code = '".$row['service_code']."'
						UNION
						SELECT l.refno, l.service_code 
						FROM seg_lab_servdetails AS l
						INNER JOIN seg_lab_serv AS r ON r.refno=l.refno
						WHERE r.type_charge<>0 AND l.refno = '".$Ref."' AND l.service_code = '".$row['service_code']."'";
			#echo $sql2;			
			
		  	$res2=$db->Execute($sql2);
		   $rowpaid=$res2->RecordCount();
			
			$hasResult = $srvObj->hasResult($Ref,$row['service_code']);
			
			$hasResult = $srvObj->hasResult($Ref);
			$hasResult_code = $srvObj->hasResult($Ref,$row['service_code']);
			#$delrow = '<img src="../../images/btn_undone.gif" align="absmiddle" border="0"/>'; 
			if (($hasResult)&&($repeat!=1)){
				if ($hasResult_code){
					$delrow = '<img src="../../images/btn_donerequest.gif" align="absmiddle" border="0"/>'; 
				}else{
					$delrow = '<img src="../../images/btn_undone.gif" align="absmiddle" border="0"/>'; 	
				}	
			}else{	
				if (($repeat)||($info['parent_refno'])){
					$delrow = '<img src="../../images/btn_repeat.gif" align="absmiddle" border="0"/>'; 
				}else{
					if ($rowpaid==0){
					
						if (($hasPaid==1)||($view_from=='ssview')){ 
							$delrow = '<img src="../../images/btn_unpaiditem.gif" border="0"/>';
						}else{
							$delrow = '<a href="javascript: nd(); removeItem(\''.$row['service_code'].'\')"><img src="../../images/btn_delitem.gif" border="0"/></a>';
						}	
                        $disabled = "disabled";   
                         $ispaid += 0;
					}else{
						$delrow = '<img src="../../images/btn_paiditem.gif" align="absmiddle" border="0"/>'; 
					    $disabled = "";   
                         $ispaid += 1;
                    }	
				}
			}	
			
			$sql3 = "SELECT is_socialized FROM seg_lab_services WHERE service_code = '".$row['service_code']."'"; 
			$res3=$db->Execute($sql3);
		   $social=$res3->FetchRow();

			$toolTipText = "Requesting doctor: <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$doctor_name ."<br>".
							    "Department: <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$dept_name ."<br>".	
							    "Clinical Impression: <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".stripslashes(strtoupper($row['clinical_info']));
			
			$notallowedchar = array("+", "-", "*","/","%");
			$code = str_replace($notallowedchar, "", $row['service_code']);
			
			$toolTipTextHandler = "onMouseOver=\"return overlib($(toolTipText".$code.").value, CAPTION,'Details',  
					                 TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, 'oltxt', CAPTIONFONTCLASS, 'olcap', 
					                 WIDTH, 250,FGCLASS,'olfgleft',FGCOLOR, '#bbddff');\" onmouseout=\"nd();\"";
										  
				$nonSocialized='';
				
				if ($social['is_socialized']==0){
					$nonSocialized='<img src="../../images/btn_nonsocialized.gif" border="0" onClick=""'.
									   ' onMouseOver="return overlib(\'This is a non-socialized service which means..secret!\', CAPTION,\'Non-socialized Service\',  '.
								      '  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '.
								      '  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();">';
				   
					$social_display = 'style="display:\'\'"';
					
					#added by VAN 07-05-08
					if ($info["is_cash"])
                         if (($orig_discountid=='DMC') || ($orig_discountid=='DMCDep') || ($orig_discountid=='DMCCD') || ($orig_discountid=='DMCConDep')|| ($orig_discountid=='DMCPGI'))
                            $prc = 0;
                        else
						    $prc = $social['price_cash'];
					else	
						$prc = $social['price_charge'];
					#----------------------	
				}
							
			#-----------added by VAN 10-17-2007--
			#dicounted price
			$sql4 = "SELECT * FROM seg_service_discounts WHERE discountid='C1' AND service_code = '".$row['service_code']."'"; 
			$res4=$db->Execute($sql4);
		   $discount_priceC1=$res4->FetchRow();
			$rowC1=$res4->RecordCount();
			if ($rowC1!=0){
				if ($discount_priceC1["price"]!=0)
					$cash_C1 = $discount_priceC1["price"];
				else
					$cash_C1 = $row['price_cash'];	
			}else		
				$cash_C1 = $row['price_cash'];	
			
			$sql5 = "SELECT * FROM seg_service_discounts WHERE discountid='C2' AND service_code = '".$row['service_code']."'"; 
			$res5=$db->Execute($sql5);
		   $discount_priceC2=$res5->FetchRow();
			$rowC2=$res5->RecordCount();
			if ($rowC2!=0){
				if ($discount_priceC2["price"]!=0)
					$cash_C2 = $discount_priceC2["price"];
				else
					$cash_C2 = $row['price_cash'];
			}else
				$cash_C2 = $row['price_cash'];
					
			$sql6 = "SELECT * FROM seg_service_discounts WHERE discountid='C3' AND service_code = '".$row['service_code']."'"; 
			$res6=$db->Execute($sql6);
		   $discount_priceC3=$res6->FetchRow();
			$rowC3=$res6->RecordCount();
			if ($rowC3!=0){
				if ($discount_priceC3["price"]!=0)
					$cash_C3 = $discount_priceC3["price"];
				else
					$cash_C3 = $row['price_cash'];
			}else		
				$cash_C3 = $row['price_cash'];
			#-----------------------------------				
			
			if ($info["discountid"] == 'C1')
			    $sservice_price = $discount_priceC1["price"];
			elseif ($info["discountid"] == 'C2')
			    $sservice_price = $discount_priceC2["price"];
			elseif ($info["discountid"] == 'C3')
			    $sservice_price = $discount_priceC3["price"];	 	 
				 
			if (($social['is_socialized']==1) && (($info["discountid"]=="C1")||($info["discountid"]=="C2")||($info["discountid"]=="C3"))){
				if ($sservice_price==0){
					
					#if (($hasPaid==1)||($view_from=='ssview')){ 
					if (($hasPaid==1)||($view_from=='ssview')||($hasResult)){ 
						$tot_price = '<input type="text" id="totprice'.$row['service_code'].'" name="totprice'.$row['service_code'].'" size="10" readonly onKeyDown="keyEnter(event, this,\''.$row['service_code'].'\');" style="text-align:right" onBlur="getSocialPrice(\''.$row['service_code'].'\'); formatNumber(this.value,2); refreshDiscount();" value="'.number_format($prc, 2).'">';
					}else{
						if ($info["is_cash"]){
							$tot_price = '<input type="text" id="totprice'.$row['service_code'].'" name="totprice'.$row['service_code'].'" size="10" onKeyDown="keyEnter(event, this,\''.$row['service_code'].'\');" style="text-align:right" onBlur="getSocialPrice(\''.$row['service_code'].'\'); formatNumber(this.value,2); refreshDiscount();" value="'.number_format($prc, 2).'">';
						}else{
							$tot_price = number_format($prc, 2);
						}
					}	
				}else{
					$tot_price = number_format($prc, 2);
				}	
			}else{
				$tot_price = number_format($prc, 2);
			}	
			$adjust_amount = 0;
			
			#----added by VAN 06-03-08
			
			if ($row['is_forward'])
				$checked = "checked";
			else
				$checked = "";	
			#-------------------	
			
			 if ($info["is_cash"]==0)
				$disabled = "";
				
			#$forwarding = '<input type="checkbox" name="withsample[]" '.$checked.' id="withsampleID'.$row['service_code'].'" value="1" />';
			$forwarding = '<input type="checkbox" name="withsampleID'.$row['service_code'].'" '.$checked.' id="withsampleID'.$row['service_code'].'" value="1" />
			               <input type="hidden" name="group'.$row['service_code'].'" id="group'.$row['service_code'].'" value="'.$row['group_code'].'">';
			
			$src .= 
					'<tr class="wardlistrow'.$alt.'" id="row'.$row['service_code'].'">
						<input type="hidden" id="index" name="index" value="'.$srvObj->count.'"/>
						<input type="hidden" name="toolTipText'.$code.'" id="toolTipText'.$code.'" value="'.$toolTipText.'" />
						<input type="hidden" name="sservice[]" id="rowsservice'.$row['service_code'].'" value="'.$social['is_socialized'].'" />
						<input type="hidden" name="price_C1[]" id="rowpriceC1'.$row['service_code'].'" value="'.$cash_C1.'" />
						<input type="hidden" name="price_C2[]" id="rowpriceC2'.$row['service_code'].'" value="'.$cash_C2.'" />
						<input type="hidden" name="price_C3[]" id="rowpriceC3'.$row['service_code'].'" value="'.$cash_C3.'" />
						<input type="hidden" name="price_C1orig[]" id="rowpriceC1orig'.$row['service_code'].'" value="'.$discount_priceC1["price"].'" />
						<input type="hidden" name="price_C2orig[]" id="rowpriceC2orig'.$row['service_code'].'" value="'.$discount_priceC2["price"].'" />
						<input type="hidden" name="price_C3orig[]" id="rowpriceC3orig'.$row['service_code'].'" value="'.$discount_priceC3["price"].'" />
						<input type="hidden" name="items[]" id="rowID'.$row['service_code'].'" value="'.$row['service_code'].'" />
						<input type="hidden" name="pcash[]" id="rowPrcCash'.$row['service_code'].'" value="'.$row['price_cash'].'" />
						<input type="hidden" name="pcashorig[]" id="rowPrcCashorig'.$row['service_code'].'" value="'.$row['price_cash_orig'].'" />
						<input type="hidden" name="pcharge[]" id="rowPrcCharge'.$row['service_code'].'" value="'.$row['price_charge'].'" />
						<input type="hidden" name="requestDoc[]" id="rowDoc'.$row['service_code'].'" value="'.$row['request_doctor'].'" />
						<input type="hidden" name="requestDocName[]" id="rowDocName'.$row['service_code'].'" value="'.$doctor_name.'" />
						<input type="hidden" name="requestDept[]" id="rowDept'.$row['service_code'].'" value="'.$row['request_dept'].'" />
						<input type="hidden" name="requestDeptName[]" id="rowDeptName'.$row['service_code'].'" value="'.$dept_name.'" />
						<input type="hidden" name="isInHouse[]" id="rowHouse'.$row['service_code'].'" value="'.$row['is_in_house'].'" />
						<input type="hidden" name="clinicInfo[]" id="rowInfo'.$row['service_code'].'" value="'.$row['clinical_info'].'" />
						<td class="centerAlign">'.$delrow.'</td>
						<td align="centerAlign">'.$nonSocialized.'</td>
						<td  width="15%" id="id'.$row["service_code"].'" '.$toolTipTextHandler.'>'.$row['service_code'].'</td>
						<td id="name'.$row["service_code"].'" '.$toolTipTextHandler.'>'.$row['name'].'</td>
						<td width="5%" id="is_forward_row'.$row["service_code"].'" align="center">'.$forwarding.'</td>
						<td class="rightAlign" id="prc'.$row["service_code"].'">'.number_format($prc_orig, 2).'</td>
						<td class="rightAlign" id="tot'.$row["service_code"].'">'.$tot_price.'</td>
					</tr>
			';    #number_format($prc, 2)
		}
	}
	if ($src) $smarty->assign('sOrderItems',$src);
if ($view_from!='ssview'){	
	$smarty->assign('sViewPDF','<img name="viewfile" id="viewfile" onClick="viewPatientRequest(\''.$info["is_cash"].'\',\''.$info["pid"].'\',\''.$Ref.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'viewpdf.gif','0','left') . ' border="0">');
    
    #if ($ispaid)
    if (($ispaid) || (($info["encounter_type"]!=2)&&($info["encounter_type"]))){
        $smarty->assign('sClaimStub','<img name="claimstub" id="claimstub" onClick="viewClaimStub(\''.$info["is_cash"].'\',\''.$Ref.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'claim_stub.gif','0','left') . ' border="0">');
				$withclaimstub = 1;
		}
}		
#-----------------------------------
}else {
    
	#edited by VAN 07-02-08
	if ($area=="ER"){
		#$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($pid)?'':'checked="checked" ').' '.(($pid)?'disabled="disabled" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		#edited by VAN 05-29-09
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($pid)?'checked="checked" ':'').' '.(($pid)?'':'disabled="disabled" ').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
	}elseif ($area=="clinic"){
        if ($is_dr){
			if (($patient['encounter_type']==3)||($patient['encounter_type']==4)){
				#$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
				$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
			}elseif($patient['encounter_type']==1){
				$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($pid)?'':'checked="checked" ').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($pid)?'checked="checked" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
			}elseif($patient['encounter_type']==2){
                $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" disabled onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
			}
			
		}else{
            $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1"  onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
			$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
		}
	}else{
        
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked="checked" onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
	}
	#edited by VAN 07-02-08
	if (($pid)&&(!empty($area))){
		$patientInfo = $person_obj->getAllInfoArray($pid);
		#echo $person_obj->sql;
		$ordername = ucwords(strtolower($patientInfo['name_first']))." ".ucwords(strtolower($patientInfo['name_last']));
		
		if ($patientInfo['street_name'])
			$addr_comma = ",";
		$orderaddress=ucwords(strtolower($patientInfo['street_name'])).$addr_comma." ".ucwords(strtolower($patientInfo['brgy_name']))." ".ucwords(strtolower($patientInfo['mun_name']));
	}else{
		$pid = "";
		$ordername = "";
		$orderaddress = "";
	}	
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');
	
	#$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="" style="font:bold 12px Arial;" readonly/>');
	#edited by VAN 07-02-08
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$ordername.'" style="font:bold 12px Arial;" readonly/>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" readonly>'.$orderaddress.'</textarea>');
	#-----------------
	
	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial; " value="Clear" onclick="clearEncounter()" disabled/>');
	
	#$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" readonly></textarea>');
	
	$smarty->assign('sRefNo','<input class="segInput" name="refno" id="refno" type="text" size="10" readonly="1" value="'.$lastnr.'" style="font:bold 12px Arial"/>');
	#$smarty->assign('sResetRefNo','<input class="segInput" type="button" value="Reset" onClick ="resetRefno();" disabled style="font:bold 11px Arial; cursor:pointer"/>');
	
	#$curDate = date("m/d/Y  h:i A");
	#$curDate = date("m/d/Y  h:i A");
	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";
	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);
	
	#edited by VAN 06-01-08
	$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
	
	$smarty->assign('sDiscountShow','<input type="checkbox" disabled name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' ><label for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
	
	#added by VAN 06-02-08
	$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" /><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
	$result = $srvObj->getChargeType();
	$options="";
	if (empty($type_charge) || ($type_charge==0))
		$type_charge = 0;
		
	while ($row=$result->FetchRow()) {
		if ($type_charge==$row['id'])
			$checked = "selected";
		else
			$checked = "";
			
		$options.='<option value="'.$row['id'].'" '.$checked.' >'.$row['charge_name'].'</option>';
	}
	
	$smarty->assign('sChargeTyp',
								"<select class=\"jedInput\" name=\"type_charge\" id=\"type_charge\">
								     $options
								 </select>");
								 
	#---------------------
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority" value="0" checked="checked" />Normal');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority" value="1" />Urgent');
	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic"></textarea>');
	
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"7\">Request list is currently empty...</td>
				</tr>");
}
	
	#edited by VAN 07-02-08
	if (($hasPaid==1)||($view_from=='ssview')||($hasResult)){ 
		$smarty->assign('sSelectEnc','<img name="select-enc" id="select-enc" src="'.$root_path.'images/btn_encounter_small.gif" border="0">');
		$smarty->assign('sBtnAddItem','<img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0">');
		$smarty->assign('sBtnEmptyList','<img src="'.$root_path.'images/btn_emptylist.gif" border="0" />');
		
		ob_start();
		$sTemp='';
?>
		<script>
			xajax_getCharityDiscounts(<?= $_GET['ref']?>);
		</script>
<?php
		$sTemp = ob_get_contents();
		ob_end_clean();
		$smarty->assign('sIntialRequestList',$sTemp);

		
	}else{
		#added by VAN 03-04-08
		$var_arr = array(
			"var_pid"=>"pid",
			"var_encounter_nr"=>"encounter_nr",
			"var_discountid"=>"discountid",
            "var_orig_discountid"=>"orig_discountid",   
			"var_discount"=>"discount",
			"var_name"=>"ordername",
			"var_addr"=>"orderaddress",
			"var_clear"=>"clear-enc"
		);
		$vas = array();
		foreach($var_arr as $i=>$v) {
			$vars[] = "$i=$v";
		}
		$var_qry = implode("&",$vars);
		 #  print_r($var_qry);
	  if ($area){
	  	$smarty->assign('sSelectEnc','<img name="select-enc" id="select-enc" src="'.$root_path.'images/btn_encounter_small.gif" border="0">');
	 
	 	#edited by VAN 07-02-08
		if ($area=="ER"){
			$enctype = "ER PATIENT";	
			$location = "EMERGENCY ROOM";
			$encounter_type = 1;
		
			$medico = $enc_obj->getEncounterMedicoCases($encounter_nr, $pid);
			#echo $enc_obj->sql;
			if ($medico)
				$info = $medico->FetchRow();
		}elseif ($area=="clinic"){
             #direct_admission
			#$enctype = "OUTPATIENT";	
			#$encounter_type = 2;
            if (($patient['encounter_type']==3)||($patient['encounter_type']==4)){
                if ($patient['encounter_status']=='direct_admission'){
                    $enctype = "INPATIENT (DIRECT ADMISSION)";
                }else{
                    if ($patient['encounter_type']==3)
                        $enctype = "INPATIENT (ER)";
                    elseif ($patient['encounter_type']==4)
                        $enctype = "INPATIENT (OPD)";
                }        
            }elseif($patient['encounter_type']==1){
                $enctype = "ER PATIENT"; 
            }elseif($patient['encounter_type']==2){
                $enctype = "OUTPATIENT";  
            } 
             #echo "sta = ".$patient['encounter_status'];
            $encounter_type = $patient['encounter_type'];
            
			$dept = $enc_obj->getEncounterDept($encounter_nr);
			$location = mb_strtoupper($dept['name_formal']);
		
			$medico = $enc_obj->getEncounterMedicoCases($encounter_nr, $pid);
			#echo $enc_obj->sql;
			if ($medico)
				$info = $medico->FetchRow();
		}
		
		if((!($info['discountid']))&&($encounter_type)){
			#echo "enc = ".$encounter_type;
			if ($encounter_type==2)
				$ss = $objSS->getPatientSocialClass($pid, 1);
			else
				$ss = $objSS->getPatientSocialClass($pid, 0);	
			#echo $objSS->sql;
			$discountid = $ss['discountid'];	
			$discount = $ss['discount'];	
			
		}else{
			$discountid = $info['discountid'];
			$discount = $info['discount'];
		}
		
		#added by VAN 07-05-08
		#$ssInfo = $objSS->getSSClassInfo($info['discountid']);
        $sql_discount = "SELECT discountid  FROM seg_charity_grants_pid   WHERE pid='".$pid."' ORDER BY grant_dte DESC LIMIT 1";
        $res_discount=$db->Execute($sql_discount);
        $discount_info=$res_discount->FetchRow();
    
        $orig_discountid = $discount_info['discountid'];  
        
		$ssInfo = $objSS->getSSClassInfo($discountid);
		if ($ssInfo['parentid']=='D')
			$info['discountid'] = $ssInfo['parentid'];
		#------------	
		#if (trim($info['senior_ID']))
	if (empty($pid))
		$pid = $info['pid'];
		
		$ss_sc = $objSS->getPatientSocialClass($pid, 0);
		
		if ($ss_sc['discountid']=='SC')
			$_POST["issc"] = 1;
		else	
			$_POST["issc"] = 0;
	
		if (($_POST["issc"])&&(trim($encounter_type)==""))
			$discount = 0.20;	
	#------------------	
		if ($ptype=='phs')
			$discountid = 'D';
		
		$smarty->assign('sClassification',(($discountid) ? $discountid : 'None'));
		$smarty->assign('sPatientType',(($enctype) ? $enctype : 'None'));
		$smarty->assign('sPatientLoc',(($location) ? $location : 'None'));
		#is_medico
		$smarty->assign('sPatientMedicoLegal',(($info['is_medico']) ? "YES" : 'NO'));   
		
		#added by VAN 03-05-09
		$smarty->assign('sPatientHRN',$pid);
		$smarty->assign('sAdmissionDate',$info['admission_dt']);
		$smarty->assign('sDischargedDate',$info['discharge_date']);
		#--------------
	  
	  }else{
	   $smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer"
       onclick="if (warnClear()) {  clearEncounter(); emptyTray(); overlib(
        OLiframeContent(\''.$root_path."modules/registration_admission/seg-select-enc.php?$var_qry&var_include_enc='+($('iscash1').checked?'0':'1'),".
				'700, 400, \'fSelEnc\', 0, \'auto\'),
        WIDTH,700, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, \'<img src='.$root_path.'/images/close_red.gif border=0 >\',
        CAPTIONPADDING,2, 
				CAPTION,\'Select registered person\',
        MIDX,0, MIDY,0, 
        STATUS,\'Select registered person\'); } return false;"
       onmouseout="nd();" />');
	 }	

		$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
          onclick="AddItem();"
          onmouseout="nd();">
			 <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');

		$smarty->assign('sBtnEmptyList','<img type="image" name="btnEmpty" id="btnEmpty" src="'.$root_path.'images/btn_emptylist.gif" border="0" style="cursor:pointer;" onclick="emptyTray();"></a>');
	}
	
	$smarty->assign('sResetRefNo','<input class="segInput" type="button" disabled value="Reset" onClick ="resetRefno();" style="font:bold 11px Arial;"/>');
	
	$granted_discount_amount = $srvObj->getSocialDiscount($info["refno"]);	
	
	#echo "<br>granted_discount_amount = ".$granted_discount_amount['amount'];	
	if (empty($granted_discount_amount['amount'])){
		$adjusted_amount = 0.00;
	}else{
		$adjusted_amount = $granted_discount_amount['amount'];				
	}	

	$smarty->assign('sAdjustedAmount','<input id="show-discount" name="show-discount" type="hidden" onBlur="formatDiscount(this.value);" readonly style="color:#006600; font-family:Arial; font-size:15px; font-weight:bold; text-align:right" size="5" value="'.number_format($adjusted_amount,2).'"/>');
	
	#enable only in the social service
	
	if ($view_from=='ssview'){ 
		if ($discountid)
			$smarty->assign('sBtnDiscounts','<img name="discountbtn" id="discountbtn" onclick="saveDiscounts2();" style="cursor:pointer" src="'.$root_path.'images/btn_discounts2.gif" border="0">');
		else
			$smarty->assign('sBtnDiscounts','<img name="discountbtn" id="discountbtn" src="'.$root_path.'images/btn_discounts2.gif" border="0" style="display:none">');
	}elseif ($hasPaid==1){
		#echo "sulod paid";
		$smarty->assign('sBtnDiscounts','<img name="discountbtn" id="discountbtn" src="'.$root_path.'images/btn_discounts2.gif" border="0" style="display:none">');
	}else{
		#echo "sulod 1";
		$smarty->assign('sBtnDiscounts','<img name="discountbtn" id="discountbtn" src="'.$root_path.'images/btn_discounts2.gif" border="0" style="display:none">');
	}
	
	if ($info["discountid"]) 
		$classification = $info["discountid"]; 
	else 
		$classification = $discountid;
	
	if ($info['parent_refno']){
		$repeat=1;
		$prevrefno = $info['parent_refno'];
	}	
	
	$smarty->assign('sRepeat','<input type="checkbox" name="repeat" id="repeat" value="yes" '.(($repeat=="1")?'checked="checked" ':'').' disabled>');
	if ($repeat){
		if (empty($serv_code)){
			$condition = "s.parent_refno = $prevrefno";
		}else{
			$condition = "s.parent_refno = $prevrefno AND d.service_code='$serv_code'";
		}
					
		$result_prev = $srvObj->getRepeatRequestInfo($condition);

		$smarty->assign('sParentRefno','<input class="segInput" id="parent_refno" name="parent_refno" type="text" size="40" value="'.$prevrefno.'" style="font:bold 12px Arial;" readonly/>');
		$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="37" rows="2" style="font:bold 12px Arial">'.stripslashes($result_prev["remarks"]).'</textarea>');
		$smarty->assign('sHead','<input class="segInput" id="head" name="head" type="text" size="40" value="'.$result_prev["approved_by_head"].'" style="font:bold 12px Arial;"/>');
		$smarty->assign('sHeadID','<input class="segInput" id="headID" name="headID" type="text" size="40" value="" style="font:bold 12px Arial;"/>');
		$smarty->assign('sHeadPassword','<input class="segInput" id="headpasswd" name="headpasswd" type="password" size="40" value="" style="font:bold 12px Arial;"/>');
		
		#$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType(1)" />Cash');
	   #$smarty->assign('sIsCharge','<input class="jedInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType(1)" />Charge');
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
		#$smarty->assign('sIsTPL','<input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($_POST["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label>');
		
		#added by VAN 06-02-08
		#$smarty->assign('sDiscountShow','<input type="checkbox" disabled name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label class="jedInput" for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
		$smarty->assign('sDiscountShow','<input type="checkbox" disabled name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' ><label for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
		

		$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($_POST["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
		$result = $srvObj->getChargeType();
		$options="";
		if (empty($_POST['type_charge']) || ($_POST['type_charge']==0))
			$_POST['type_charge'] = 0;
		
		while ($row=$result->FetchRow()) {
			if ($_POST['type_charge']==$row['id'])
				$checked = "selected";
			else
				$checked = "";
			
			$options.='<option value="'.$row['id'].'" '.$checked.' >'.$row['charge_name'].'</option>';
		}
	
		$smarty->assign('sChargeTyp',
								"<select class=\"jedInput\" name=\"type_charge\" id=\"type_charge\">
								     $options
								 </select>");
								 
		#---------------------
		
		$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority" value="0" '.(($info["is_urgent"]!="1")?'checked="checked" ':'').'/>Normal');
		$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority" value="1" '.(($info["is_urgent"]=="1")?'checked="checked" ':'').'/>Urgent');
		
		$smarty->assign('sSelectEnc','<img name="select-enc" id="select-enc" src="'.$root_path.'images/btn_encounter_small.gif" border="0">');
		$smarty->assign('sBtnAddItem','<img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0">');
		$smarty->assign('sBtnEmptyList','<img src="'.$root_path.'images/btn_emptylist.gif" border="0" />');
		$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;" value="Clear" onclick="clearEncounter()" disabled />');
		
		$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.stripslashes($result_prev["comments"]).'</textarea>');
		
		if ($result_prev["refno"]){	
			$Ref = $result_prev["refno"];
		}else{
			$Ref = "";
		}
		$smarty->assign('sRefNo','<input name="refno" id="refno" readonly="1" type="text" size="8" value="'.$Ref.'" style="font:bold 12px Arial"/>');
		
		if (($prevrefno!=NULL) && ($_GET["ref"]==NULL)){
			/*
			if (!empty($result_prev["serv_dt"])){
				$time = strtotime($result_prev["serv_dt"]);
				$requestDate = date("m/d/Y",$time);
			}else{
				$requestDate = 	date("m/d/Y");	
			}
			*/
			if (($result_prev["serv_dt"]!='0000-00-00')||(!empty($result_prev["serv_dt"]))) {
				$requestDate = $result_prev['serv_dt']." ".$result_prev['serv_tm'];
				$submitted = 1;
			}

			#added by VAN 06-02-08
			$dbtime_format = "Y-m-d H:i";
			$fulltime_format = "F j, Y g:ia";
			$curDate = date($dbtime_format);
			$curDate_show = date($fulltime_format);
	
			$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($requestDate)) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($requestDate)) : $curDate).'" style="font:bold 12px Arial">');
			$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');											
		}
		
	}
	
	$jsCalScript = "<script type=\"text/javascript\">jsCalendarSetup();</script>";
	$smarty->assign('jsCalendarSetup', $jsCalScript);
#----------------------------------

if($error=="refno_exists"){
	$smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
	$smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}


 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform" id="inputform" onSubmit="return prufform();">');
 $smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

include_once($root_path."include/care_api_classes/class_discount.php");
$discountClass = new SegDiscount();
$src = "";

if ($mode=="save"){
	if ($result = $discountClass->getAllDataObject()) {
		while ($row = $result->FetchRow()) {
			echo '	<input type="hidden" id="discount_'.$row['discountid'].'" name="discount[]" discount="'.$row["discount"].'" value="'.$posted_discounts[$row["discountid"]].'" />';
		}
	}
}elseif ($mode=="update"){

	if ($result = $discountClass->getAllDataObject()) {
		$posted_discounts=array();
		if ($saved_discounts) {
			foreach ($saved_discounts as $i=>$v) {
				if ($v) $posted_discounts[$v] = $v;
			}
		}

		while ($row = $result->FetchRow()) {
			echo '	<input type="hidden" id="discount_'.$row['discountid'].'" name="discount[]" discount="'.$row["discount"].'" value="'.$posted_discounts[$row["discountid"]].'" />';
		}
	}
}

?>

   <input type="hidden" name="submited" value="1" />
  <input type="hidden" name="sid" value="<?php echo $sid?>">
  <input type="hidden" name="lang" value="<?php echo $lang?>">
  <input type="hidden" name="cat" value="<?php echo $cat?>">
  <input type="hidden" name="userck" value="<?php echo $userck?>">  
  
  <?php
  		if ((($prevrefno) && ($result_prev["refno"])) || ($_GET["ref"]) || $data["refno"] || $_POST["refno"])
				$saveok = 1;
		else 
				$saveok = 0;
				
		#echo "stub = ".$withclaimstub;		
	?>
  
	<input type="hidden" name="withclaimstub" id="withclaimstub" value="<?=$withclaimstub?>" />
  <input type="hidden" name="mode" id="mode" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
  <!--<input type="text" name="mode" id="mode" value="<?php if(($saveok)||(($prevrefno) && ($result_prev["refno"]))) echo "update"; else echo "save"; ?>">-->
  <input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
  <input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
  <input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
  <input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
  <input type="hidden" name="update" id="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
	
	<input type="hidden" name="editpencnum"   id="editpencnum"   value="">	
	<input type="hidden" name="editpentrynum" id="editpentrynum" value="">
	<input type="hidden" name="editpname" id="editpname" value="">
	<input type="hidden" name="editpqty"  id="editpqty"  value="">
	<input type="hidden" name="editppk"   id="editppk"   value="">
	<input type="hidden" name="editppack" id="editppack" value="">
	<input type="hidden" name="is_cash" id="is_cash" value="<?=$is_cash; ?>">
	<input type="hidden" name="lastrefno" id="lastrefno" value="<?=$lastnr; ?>" >
	<input type="hidden" id="encounter_nr" name="encounter_nr" value="<?php if (trim($info["encounter_nr"])) echo $info["encounter_nr"]; else echo $encounter_nr;?>">
	
	<!--<input type="text" id="discountid" name="discountid" value="<?php if (trim($info["discountid"])) echo $info["discountid"]; else echo $discountid;?>">-->
	<?php
		#echo 'dis = '.$discount;
		
		if ((empty($discountid))&&(empty($view_from)))
			$discountid = $info["discountid"];
		
		$discountInfo = $objSS->getSSClassInfo($discountid);
		#echo "here = ".$objSS->sql;
		
		if ($discountInfo){
			#echo "hhere = ".$discountInfo['discount'];
			$discount = 	$discountInfo['discount'];
		}	
		
		
		#echo "sc = ".$_POST['issc'];
		#echo "<br>type = ".$info["encounter_type"];
		
		if(($_POST['issc'])&&(trim($info["encounter_type"])=="")){
			$discount = 0.20;
		}
		
		if ($ptype=='phs'){
			$discountid = "D";
			$discount = "1";
		}
		
	?>
    <input type="hidden" id="orig_discountid" name="orig_discountid" value="<?=$orig_discountid?>">
	<input type="hidden" id="discountid" name="discountid" value="<?=$discountid?>">
	<!--<input type="text" id="discount" name="discount" value="<?php if (trim($granted_discount['discount'])) echo $granted_discount['discount']; else echo $discount;?>">-->
	<input type="hidden" id="discount" name="discount" value="<?=$discount?>">
	<?php
		
		$discountInfo = $objSS->getSSClassInfo($info["discountid"]);
		
		if ($discountInfo){
			$olddiscount = 	$discountInfo['discount'];
		}	
	?>
	<input type="hidden" id="old_discountid" name="old_discountid" value="<?=$info["discountid"]?>" />
	<input type="hidden" id="old_discount" name="old_discount" value="<?=$olddiscount?>" />
	<input type="hidden" name="hasPaid" id="hasPaid" value="<?=$hasPaid?$hasPaid:'0'?>">
	<input type="hidden" name="view_from" id="view_from" value="<?=$view_from?$view_from:''?>">
	<input type="hidden" name="encoder_id" id="encoder_id" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>">
	<input type="hidden" name="latest_valid_show-discount" id="latest_valid_show-discount" value="<?=number_format($adjusted_amount, 2, '.', '')?>" >
	<input type="hidden" name="popUp" id="popUp" value="<?=$popUp;?>">
	
	<input type="hidden" name="paidCash" id="paidCash" value="<?=$paidCash?>">
    
    <input type="hidden" name="ispaid" id="ispaid" value="<?=$ispaid?>">
    
    <input type="hidden" name="labarea" id="labarea" value="<?=$lab_area?>">
	
	<input type="hidden" name="key" id="key" value="<?=$key?>">
	
	<input type="hidden" name="area" id="area" value="<?=$area?>">
	<?php
			if ($_GET['ref'])
				$refnum=$_GET['ref'];
				$sql_grp = "SELECT s.group_code, rs.* 
						FROM seg_lab_serv AS rs
						INNER JOIN seg_lab_servdetails AS rd ON rd.refno=rs.refno
						INNER JOIN seg_lab_services AS s ON s.service_code=rd.service_code
						WHERE rs.refno = '".$refnum."'
						GROUP BY s.group_code";
			#echo $sql_grp;			
			$res_grp=$db->Execute($sql_grp);
			$no_of_group=$res_grp->RecordCount();
	?>
	<input type="hidden" name="no_of_group" id="no_of_group" value="<?=$no_of_group?>">
	
	<?php
			$prev_request = substr_replace($prev_request,"",strlen($prev_request)-1)
	?>
	<input type="hidden" name="prev_request" id="prev_request" size="100" value="<?=$prev_request?>">

<?php 

$sTemp = ob_get_contents();
ob_end_clean();
#edited by VAN 03-06-08
/*
$sBreakImg ='close2.gif';	
$sBreakImg ='cancel.gif';
*/

$smarty->assign('sHiddenInputs',$sTemp);

if (($mode=="update") && ($popUp!='1')){
	$sBreakImg ='cancel.gif';
	$smarty->assign('sBreakButton','<img type="image" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' align="center" alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';" style="cursor:pointer">');
}elseif ($popUp!='1'){
	$sBreakImg ='close2.gif';	
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}

if ((($hasPaid==1)||($view_from=='ssview')||($hasResult))&&($repeat!=1)){ 
	if (($ispaid)&&($view_from!='ssview'))
        $smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" border="0" align="center" style="cursor:pointer" onclick="if (confirm(\'Process this request?\')) if (prufform()) document.inputform.submit()">');
    else
        $smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" border="0" align="center">');    
}else{
	$smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" border="0" align="center" style="cursor:pointer" onclick="if (confirm(\'Process this request?\')) if (prufform()) document.inputform.submit()">');
}	

if (($view_from!='ssview') && ($popUp!=1)){ 
	$smarty->assign('sViewRequest','<a href="javascript:RequestList();"><img '.createLDImgSrc($root_path,'showrequest.gif','0','left').' border=0 alt="View the List of Requestors"></a>');
}	

#added by VAN 07-14-09
$smarty->assign('sRDU','<input type="checkbox" '.(($info["is_rdu"]==1)?'checked="checked" ':'').' name="is_rdu" id="is_rdu" value="1" />'); 

$smarty->assign('sSocialServiceNotes','<img src="'.$root_path.'images/btn_nonsocialized.gif"> <span style="font-style:italic">Nonsocialized service.</span>');
$smarty->assign('social_display',$social_display);

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','laboratory/form_new.tpl');
$smarty->display('common/mainframe.tpl');

?>
