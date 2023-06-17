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
	require($root_path.'modules/laboratory/ajax/lab-request-new.common.php'); 
	
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
	/*
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	*/
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
	
	#$phpfd=str_replace("yy","%y", strtolower($phpfd));

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
	global $db;
	
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');
	
	$popUp = $_GET['popUp'];
	$repeat = $_GET['repeat'];
	$prevrefno = $_GET['prevrefno'];
	$serv_code = $_GET['serv_code'];
	
	if ($_GET['view_from'])
		$popUp = 1;
	#echo "repeat = ".$repeat;
	#echo "repeat = ".$repeat." - ".$prevrefno." - ".$serv_code;
	
	$repeaterror = $_GET['repeaterror'];
	#echo "repeaterror = ".$repeaterror;
	#echo '<em class="warn">Sorry but you are not allowed to do a repeat request!</em>';
	
	if ($repeaterror){
		$smarty->assign('sWarning',"<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!");
	}
	
#echo "user = ".$_SESSION['sess_temp_userid'];
	if (isset($_POST["submited"])) {
		
		#echo "<br>encounter_nr = ".$encounter_nr;
		#echo "<br>discountid = ".$discountid;
		
		$new_refno = $srvObj->getLastNr(date("Y-m-d"),"'".$GLOBAL_CONFIG['refno_init']."'");
		#'refno'=>$new_refno,
		
		if ($_POST['pid']==NULL)
			$pid = " ";
		else
			$pid = $_POST['pid'];	
			
		if (empty($discountid))
			$discountid = " ";
		
		if (empty($encounter_nr))	
			$encounter_nr = " ";
		
		#added by VAN 01-0908
		$patient = $enc_obj->getEncounterInfo($encounter_nr);
		if ($patient['encounter_type'] == 1){
			$patient_type = "ER";
			$loc_code = "ER";
			$loc_name = "Emergency Room";
		}elseif (($patient['encounter_type'] == 3)||($patient['encounter_type'] == 4)){
			$patient_type = "IN";	
			$loc_code = $patient['current_ward_nr'];
			if ($loc_code)
				$ward = $ward_obj->getWardInfo($loc_code);
					
			$loc_name = stripslashes($ward['name']);;
		}else{
			$patient_type = "OP";	
			$loc_code = $patient['current_dept_nr'];
			if ($loc_code)
				$dept = $dept_obj->getDeptAllInfo($loc_code);
				
			$loc_name = stripslashes($dept['name_formal']);
		}
		
		if (empty($_POST['is_tpl']))
			$_POST['is_tpl'] = '0';
		#echo "tpl = ".$_POST['is_tpl'];			
		#------------------------------------------	
		#echo "name = ".$_POST['ordername'];
		$data = array(
			'encounter_nr'=>$encounter_nr,
			'pid'=>$pid,
			'is_cash'=>$_POST['is_cash'],
			'is_urgent'=>$_POST['priority'],
			'is_tpl'=>$_POST['is_tpl'],
			'type_charge'=>$_POST['type_charge'],
			'create_id'=>$_SESSION['sess_temp_userid'],   
			'modify_id'=>$_SESSION['sess_temp_userid'],   
			'modify_dt'=>date('YmdHis'),   
			'create_dt'=>date('YmdHis'),
			'comments'=>addslashes($_POST['comments']),
			'ordername'=>addslashes(trim($_POST['ordername'])),
			'orderaddress'=>addslashes($_POST['orderaddress']),
			'status'=>" ",
			'discountid'=>$discountid,
			'loc_code'=>$loc_code,
			'parent_refno'=>$_POST['parent_refno'],
			'approved_by_head'=>$_POST['head'],
			'remarks'=>$_POST['remarks'],
			'headID'=>$_POST['headID'],
			'headpasswd'=>$_POST['headpasswd']
		);
		
		#'history'=>"Create: ".date('Y-m-d H:i:s')." [\\".$_SESSION['sess_temp_userid']."]\\n",
		if ($_POST['orderdate']) {
			#$time = strtotime($_POST['orderdate']);
			#$data["serv_dt"] = date("Ymd",$time);
			$data["serv_dt"] = date("Ymd",strtotime($_POST['orderdate']));
			$data["serv_tm"] = date("H:i:s",strtotime($_POST['orderdate']));
		}
		
		#$data["serv_tm"] = date('H:i:s');
		#echo "time = ".$data["serv_tm"];
		
		if ($_POST["pid"]) $data["pid"] = $_POST["pid"];

		if ($_POST["items"]!=NULL){
			#echo "<br>mode = ".$mode;
			#echo "<br>repeat = ".$repeat;
			#exit;
			#switch($_POST['mode']) {
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
										#echo "<br>sql = ".$srvObj->sql;
										$isCorrectInfo = $srvObj->count;
										if ($isCorrectInfo){
											$saveok=$srvObj->insertDataFromInternalArray();
										}else{
											#echo '<em class="warn">Sorry but you are not allowed to do a repeat request!</em>';
											/*echo "<script type=\"text/javascript\">alert('Sorry but you are not allowed to do a repeat request!');</script>";*/
											header("Location: seg-lab-request-new.php".URL_REDIRECT_APPEND."&user_origin=$user_origin&repeat=$repeat&prevrefno=".$_POST['parent_refno']."&serv_code=".stripslashes($_POST["items"][0])."&paid=1&popUp=1&repeaterror=1");
										}
									}else{
										$saveok=$srvObj->insertDataFromInternalArray();
									}	
									#echo "sql = ".$db->errorMsg();
							
									break;
					case 'update':
									#update table
									#echo "<br>switch update";
									$data["refno"] = $_POST["refno"];
									
									if ($data["refno"]==NULL)
										$data["refno"] = $_GET["refno"];
									
									#echo "Ref = ".$data["refno"];
										
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
										if ($isCorrectInfo){
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
			#echo "<br>refno = ".$data["refno"]."<br>";
			#print_r($_POST["items"]);
			if ($_POST["items"]!=NULL){
				
				$bulk = array();
				$withsample = array();
				foreach ($_POST["items"] as $i=>$v) {
					#if ($_POST["withsample"][$i])
					#	$_POST["withsample"][$i] = 1;
					#else
					#	$_POST["withsample"][$i] = 0;	
					
					#withsampleID
					$id = 'withsampleID'.$_POST["items"][$i];
					echo "<br>id =".$id;
					echo "<br>i = ".$_POST[$id];
					if ($_POST[$id]!=1)
						$_POST[$id] = 0;
						
					#$withsample[] =  $_POST[$id];
					
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
					
						#$bulk[] = array($_POST["items"][$i],$_POST["pcash"][$i],$_POST["pcharge"][$i]);
						#$bulk[] = array($_POST["items"][$i],$_POST["pcash"][$i],$_POST["pcharge"][$i],stripslashes($_POST["requestDoc"][$i]),stripslashes($_POST["requestDept"][$i]),$_POST["isInHouse"][$i],stripslashes($_POST["clinicInfo"][$i]));
						$bulk[] = array($_POST["items"][$i],$cash_price,$_POST["pcashorig"][$i],$_POST["pcharge"][$i],stripslashes($_POST["requestDoc"][$i]),stripslashes($_POST["requestDept"][$i]),$_POST["isInHouse"][$i],stripslashes($_POST["clinicInfo"][$i]),$_POST[$id]);
					}else{
						#$bulk[] = array($_POST["items"][$i],$_POST["pcash"][$i],$_POST["pcharge"][$i]);	
						$cash_price = $_POST["pcharge"][$i];
						$bulk[] = array($_POST["items"][$i],$cash_price,$_POST["pcashorig"][$i],$_POST["pcharge"][$i],stripslashes($_POST["requestDoc"][$i]),stripslashes($_POST["requestDept"][$i]),$_POST["isInHouse"][$i],stripslashes($_POST["clinicInfo"][$i]),$_POST[$id]);	
					}	
				}
				#echo "<br>sulod<br>";
				#print_r($bulk);
				$srvObj->clearOrderList($data['refno']);
				#echo "".$srvObj->sql;
				$srvObj->addOrders($data['refno'],$bulk);
				#echo "".$srvObj->sql;
				$_POST['refno'] = $data['refno'];
				$srvObj->grantLabRequest($_POST);
				
				
				#-----post to HCLAB when
				# request is granted
				# request is to be paid later (is_urgent=1)
				# request is paid
				
				if ($_POST['priority']){
					$priority = "U";
				}else{
					$priority = "R";	
				}
				
				if ($mode == "save"){
					$trx_ID = "N";    # new order
					$ref_no = $new_refno;
				}elseif ($mode=="update"){
					$trx_ID = "U";		# update order
					$ref_no = $_POST["refno"];
				}else{
					$trx_ID = "C";		# cancel order
				}
				
				$trx_status = "N";   # before read by LIS default value
				#$trx_status = "Y";	# after read by LIS
				
				# if Outpatient
				#stripslashes($_POST["requestDept"][$i])
				#else
				# ward in care_encounter	
				/*
				$patient = $enc_obj->getEncounterInfo($encounter_nr);
				#echo "sql = ".$enc_obj->sql;
				#echo "sql = ".$enc_obj->sql;
				if ($patient['encounter_type'] == 1){
					$patient_type = "ER";
					$loc_code = "ER";
					$loc_name = "Emergency Room";
				}elseif (($patient['encounter_type'] == 3)||($patient['encounter_type'] == 4)){
					$patient_type = "IN";	
					$loc_code = $patient['current_ward_nr'];
					if ($loc_code)
						$ward = $ward_obj->getWardInfo($loc_code);
					
					$loc_name = stripslashes($ward['name']);;
				}else{
					$patient_type = "OP";	
					$loc_code = $patient['current_dept_nr'];
					if ($loc_code)
						$dept = $dept_obj->getDeptAllInfo($loc_code);
					$loc_name = stripslashes($dept['name_formal']);
				}
				*/
								
				if ($patient['sex']=="m"){
					$sex = 1;
				}elseif ($patient['sex']=="f"){
					$sex = 2;
				}else{
					$sex = 0;
				}
				#echo "nr = ".$encounter_nr;
				
				$time = strtotime($data["serv_tm"]);
				$data["serv_tm"] = date("His",$time);
				$order_date = $data["serv_dt"]."".$data["serv_tm"];
				#echo "<br>order = ".$order_date;
				
				$time_bod = strtotime($patient["date_birth"]);
				$patient["date_birth"] = date("Y-m-d",$time_bod);
				
				$data_HCLAB = array(
							'POH_TRX_NUM'=>$ref_no ,
							'POH_TRX_DT'=>$order_date,
							'POH_TRX_ID'=>$trx_ID,
							'POH_TRX_STATUS'=>$trx_status,
							'POH_ORDER_NO'=>$_POST["refno"],   
							'POH_ORDER_DT'=>$order_date,   
							'POH_LOC_CODE'=>$loc_code,   
							'POH_LOC_NAME'=>$loc_name,
							'POH_DR_CODE'=>$_POST["requestDoc"][0],
							'POH_DR_NAME'=>addslashes($_POST["requestDocName"][0]),
							'POH_PAT_ID'=>$pid,
							'POH_PAT_NAME'=>addslashes(trim($_POST['ordername'])),
							'POH_PAT_TYPE'=>$patient_type,
							'POH_PAT_ALTID'=>" ",
							'POH_PAT_DOB'=>$patient["date_birth"],
							'POH_PAT_SEX'=>$sex,
							'POH_PAT_CASENO'=>$encounter_nr,
							'POH_CLI_INFO'=>addslashes($_POST["clinicInfo"][0]),
							'POH_PRIORITY'=>$priority
						);
				
				$bulk_HCLAB = array();
				
				#comment this for the meantime
				# for HCLAB (oracle connection)
				#$objconn = $hclabObj->ConnecttoDest($dsn);
				# if connection to HCLAB is OK
				#if ($objconn) {
					
					# request to be post in HCLAB database
					# if priority is urgent, if charge and if repeat request						
					
					#if (($_POST['priority'])||($_POST['is_cash']==0)){
					
					#if (($_POST['priority'])||($_POST['is_cash']==0)|| ($repeat)){
					if (($_POST['priority'])||($_POST['is_cash']==0)||($_POST['is_tpl'])|| ($repeat)){
						foreach ($_POST["items"] as $i=>$v) {
							$labservObj = $srvObj->getServiceInfo(addslashes($_POST["items"][$i]));
							$bulk_HCLAB[] = array(addslashes($_POST["items"][$i]),addslashes($labservObj['name'])," ");	
						}	
						# save to ORDERH in HCLAB if priority is to be paid later
						if ($hclabObj->isExists($ref_no)){
							#echo "exist";
							$hclabObj->updateOrderH_to_HCLAB($data_HCLAB);
						}else{
							#echo "not exists";
							$hclabObj->addOrderH_to_HCLAB($data_HCLAB);
							#echo "urgent sql = ".$hclabObj->sql;
						}	
					
						$hclabObj->clearOrderList_to_HCLAB($ref_no);
						#echo "delete = ".$hclabObj->sql;
						$hclabObj->addOrders_to_HCLAB($ref_no, $bulk_HCLAB);
					}else{
						# request that to be granted
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
							foreach ($_POST["items"] as $i=>$v) {
								if (floatval($pcash[$i])==0){
									$labservObj = $srvObj->getServiceInfo(addslashes($_POST["items"][$i]));
									$bulk_HCLAB[] = array(addslashes($_POST["items"][$i]),addslashes($labservObj['name'])," ");	
								}	
							}	
					
							if ($hclabObj->isExists($ref_no)){
								#echo "exist";
								$hclabObj->updateOrderH_to_HCLAB($data_HCLAB);
							}else{
								#echo "not exists";
								$hclabObj->addOrderH_to_HCLAB($data_HCLAB);
								#echo "granted sql = ".$hclabObj->sql;
							}	
			
							$hclabObj->clearOrderList_to_HCLAB($ref_no);
							$hclabObj->addOrders_to_HCLAB($ref_no, $bulk_HCLAB);
						}	
					}
				/*
				}else{
					# can't connect to HCLAB
					echo '<em class="warn">Sorry, HCLAB connection failed..</em>';
				}	
				*/
				
				
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
				$smarty->assign('sWarning',"Laboratory Service item successfully created.");
			elseif ($mode=='update')
				$smarty->assign('sWarning',"Laboratory Service item successfully updated.");		
		}
		else {
			$errorMsg = $db->ErrorMsg();
			#echo "error = ".$errorMsg;
			if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
				$smarty->assign('sWarning','<strong>Error:</strong> A laboratory service with the same request number already exists in the database.');
				#$smarty->assign('sWarning',"<strong>Error:</strong> $errorMsg");
			#elseif (strpos(strtolower($errorMsg), "latest billing") !== FALSE)
				#$smarty->assign('sWarning',"<strong>Error:</strong> $errorMsg");
			else{
				if ($errorMsg!=NULL)
					$smarty->assign('sWarning',"<strong>Error:</strong> $errorMsg");
				else	
					$smarty->assign('sWarning',"<strong>Error:</strong> Request must have at least one laboratory service.");
			}
		} 
	}
	
if ($saveok) {
	
}

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$LDLab :: $LDLabNewTest");

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
 $smarty->assign('sWindowTitle',"$LDLab :: $LDLabNewTest");

 # Assign Body Onload javascript code
 #echo "get mode = ".$_GET['mode'];
	/*
 if ($_GET['mode'])
 	$onLoadJS='onLoad="preSet(1);refreshDiscount();"';
 else
 	$onLoadJS='onLoad="preSet(0);refreshDiscount();"'; 
 */

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
		window.open("seg-request-tray.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>","patient_select","width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
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
		//alert("popup = "+document.getElementById('popUp').value);
		//alert(window.location);
		//alert(window.parent.location);
		
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
		return overlib(
          OLiframeContent('seg-request-tray.php', 600, 480, 'fOrderTray', 1, 'auto'),
          						WIDTH,480, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, MODAL, 
									CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 >',
						         CAPTIONPADDING,4, CAPTION,'Add laboratory service item from request tray',
						         MIDX,0, MIDY,0, 
						         STATUS,'Add laboratory service item from request tray');
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
	
	/*
	function setTabindex(){
		//document.getElementById('help').tabindex = '1';
		//document.getElementById('iscash').tabindex = '1';
		//document.getElementById('ordername').tabIndex = '2';
		document.getElementById('orderaddress').tabIndex = '3';
		document.getElementById('orderdate').tabIndex = '4';
		//document.getElementById('priority').tabindex = '5';
		document.getElementById('comments').tabIndex = '6';
	}
	*/
	//function preSet($mod){
	function preSet(){
		//setTabindex();
		//alert("discount = "+document.getElementById('show-discount').value);
		//alert(document.getElementById('order-list').innerHTML);
		CheckRepeatInfo();
		//alert('ssview = '+document.getElementById('view_from').value);
		//alert('paid = '+document.getElementById('hasPaid').value);
		//paidCash
		
		//if ((document.getElementById('view_from').value == 'ssview') && (document.getElementById('hasPaid').value == 0)){
		if ((document.getElementById('view_from').value == 'ssview') && (document.getElementById('paidCash').value == 0)){
			document.getElementById('show-discount').readOnly = false;
			document.getElementById('discountbtn').style.display = '';
		}else{
			document.getElementById('show-discount').readOnly = true;
			document.getElementById('discountbtn').style.display = 'none';
		}	
		
		/*
		if (document.getElementById('view_from').value == 'ssview'){
			if (document.getElementById('paidCash').value == 1){
				document.getElementById('show-discount').readOnly = true;
				document.getElementById('discountbtn').style.display = 'none';
			}else{
				document.getElementById('show-discount').readOnly = false;
				document.getElementById('discountbtn').style.display = '';
			}
		}
		*/
		
		//if ($mod)
			//document.getElementById('mode').value = '<?= $_GET['mode']; ?>';
		
		if ($("iscash1").checked){
			document.getElementById('is_cash').value = 1;
			//added by VAN 06-01-08
			$('tplrow').style.display = '';
			$('type_charge').style.display = 'none';
			
		}else{
			document.getElementById('is_cash').value = 0;	
			$('tplrow').style.display = 'none';
			$('type_charge').style.display = '';
		}	
		//if (document.getElementById('saveok').value == 1)	
		
		//alert("document.getElementById('show-discount').value = "+document.getElementById('show-discount').value);
		//document.getElementById('show-discount-total').innerHTML = document.getElementById('show-discount').value;	
		//alert("document.getElementById('show-discount-total').innerHTML = "+document.getElementById('show-discount-total').innerHTML);
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
		//document.getElementById('show-discount').value = amount;
		//document.getElementById('show-discount').disabled = bol;
		//edited by VAN 03-10-08
		document.getElementById('show-discount').readOnly = bol;
		if(bol){	
			//document.getElementById('btndiscount').style.display = 'none';
			document.getElementById('discountbtn').style.display = 'none';
		}else{
			//document.getElementById('btndiscount').style.display = '';
			document.getElementById('discountbtn').style.display = '';
		}
	}
	
	function saveDiscounts(){
		var refno, amtDiscount, encoderId; 
		refno = document.getElementById("refno").value;
		amtDiscount = document.getElementById("show-discount").value;
		amtDiscount = amtDiscount.replace(",","")
		encoderId = document.getElementById("encoder_id").value;
		//alert(amtDiscount);		
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
	
	/*
	function setDiscount(){
		var total = parseFloat(document.getElementById('show-sub-total').innerHTML);
		var discount = parseFloat(document.getElementById('show-discount-total').innerHTML);
		var nettotal = parseFloat(document.getElementById('show-net-total').innerHTML);
			
		//document.getElementById('show-discount-total').innerHTML = formatNumber(amtDiscount,2);
		//document.getElementById('show-net-total').innerHTML = formatNumber(nettotal,2);
	}
	*/
	
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

#$lastnr = $srvObj->getLastNr(date("Y-m-d"),"'".$GLOBAL_CONFIG['refno_init']."'");

# to determine if at least one service in the request is paid
	$hasPaid = $_GET['paid'];

# to determine if the social service open or access the form
	$view_from = $_GET['view_from'];
	$social_display = 'style="display:none"';

# Render form values
if (isset($_POST["submited"]) && !$saveok) {
	$smarty->assign('sRefNo','<input name="refno" id="refno" type="text" size="8" readonly="1" value="'.$_POST['refno'].'" style="font:bold 12px Arial"/>');
	/*
	$smarty->assign('sOrderDate','<input name="orderdate" id="orderdate" type="text" size="8" 
											value="'.date("m/d/Y",strtotime($_POST['orderdate'])).'" style="font:bold 12px Arial"
											onFocus="this.select();"  
											onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
											onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
											onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">');
	*/
	
	#added by VAN 06-02-08
	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";
	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);
	
	$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
											
	#$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" align="absmiddle" style="cursor:pointer">');
	
	$count=0;
	#$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType(1)" />Cash');
	#$smarty->assign('sIsCharge','<input class="segInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType(1)" />Charge');
	#$smarty->assign('sIsCharge','<input class="jedInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType(1)" />Charge');
	$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
	$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
	#$smarty->assign('sIsTPL','<input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($_POST["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label>');
	
	#added by VAN 06-02-08
	$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label class="jedInput" for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
	$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($_POST["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
	
	$result = $srvObj->getChargeType();
	$options="";
	if (empty($_POST['type_charge']) || ($_POST['type_charge']==0))
		$_POST['type_charge'] = 3;
		
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
	#$smarty->assign('sResetRefNo','<input class="segInput" type="button" value="Reset" disabled onClick ="resetRefno();" style="font:bold 11px Arial;cursor:pointer"/>');
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').'/>Normal');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').'/>Urgent');
	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic"></textarea>');
	
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"7\">Request list is currently empty...</td>
				</tr>");
				
}elseif ((isset($_POST["submited"]) && $saveok) || ((($mode="update") && $update))){
	# Fetch order data
	
	if ($_GET["ref"]!=NULL)
		$Ref = $_GET["ref"];
	else
		$Ref = $data["refno"];
	
	#-------------------
	# check if this request is already paid or not
	/*
	$sql = "SELECT * FROM seg_pay_request
           WHERE ref_source = 'LD' AND ref_no = '".$Ref."'";
	*/
	
	$sql = "SELECT pr.ref_no,pr.service_code FROM seg_pay_request AS pr
			  WHERE pr.ref_source = 'LD' AND pr.ref_no = '".$Ref."'
			  UNION
			  SELECT gr.ref_no,gr.service_code  FROM seg_granted_request AS gr
           WHERE gr.ref_source = 'LD' AND gr.ref_no = '".$Ref."'";
	#echo "sql = ".$sql;
	$res=$db->Execute($sql);
	$row2=$res->RecordCount();
	
	#added by VAN 03-07-08
	$sqlPaid = "SELECT or_no, pr.ref_no,pr.service_code FROM seg_pay_request AS pr
			   WHERE pr.ref_source = 'LD' AND pr.ref_no = '".$Ref."' LIMIT 1";
					
	$resPaid=$db->Execute($sqlPaid);
	$rowPaid=$resPaid->RecordCount();
	$resultPaid = $resPaid->FetchRow();
	#print_r($resultPaid);
	#echo 'or no line 916= '.$resultPaid['or_no'];
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
		
# Render form values
	#$readOnly = (!$info['is_cash'] || $info['pid']) ? 'readonly="readonly"' : "";
	#$readOnly = (!$info['is_cash']) ? 'readonly="readonly"' : "";
	$readOnly = "readonly";
	#echo "here";
	/*
	if (($info["pid"]==" ") || (empty($info["pid"]))){
		$request_name = trim($info['ordername']);
		#$request_address = $info['orderaddress'];
	}else{
		$person = $person_obj->getAllInfoArray($info["pid"]);
		$request_name = trim($person['name_first'])." ".trim($person["name_2"])." ".trim($person["name_last"]);
		$request_name = ucwords(strtolower(stripslashes($request_name)));
		#$request_name = htmlspecialchars($request_name);
	}	
	*/
	
	if (empty($info['pid']) || !$info['pid']){
		$request_name = stripslashes(trim($info['ordername']));
	}else{
		# in case there is an updated profile of the person
		$request_name = trim($info['name_first']).' '.trim($info['name_last']);
	}
	#echo "name = ".$request_name;
	$smarty->assign('sRefNo','<input name="refno" id="refno" readonly="1" type="text" size="8" value="'.$Ref.'" style="font:bold 12px Arial"/>');
	/*
	if ($info['serv_dt']) {
			$time = strtotime($info['serv_dt']);
			$requestDate = date("m/d/Y",$time);
	}
	*/
	#if (($info['serv_dt']!='0000-00-00')&&($info['serv_tm']!='00:00:00')) {
	if ($info['serv_dt']!='0000-00-00') {
		$requestDate = $info['serv_dt']." ".$info['serv_tm'];
		$submitted = 1;
	}
	
	/*
	$smarty->assign('sOrderDate','<input name="orderdate" id="orderdate" type="text" size="8" 
											value="'.$requestDate.'" style="font:bold 12px Arial"
											onFocus="this.select();"  
											onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
											onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
											onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">');
	*/
	
	$hasResult = $srvObj->hasResult($Ref);
	
	#added by VAN 06-02-08
	
	if ($info['encounter_type']==1){
		$enctype = "ER PATIENT";
		$location = "EMERGENCY ROOM";
	}elseif ($info['encounter_type']==2){
		#$enctype = "OUTPATIENT (OPD)";
		$enctype = "OUTPATIENT";
		$dept = $dept_obj->getDeptAllInfo($info['current_dept_nr']);
		$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
	}elseif (($info['encounter_type']==3)||($info['encounter_type']==4)){
		if ($info['encounter_type']==3)
			$enctype = "INPATIENT (ER)";
		elseif ($info['encounter_type']==3)
			$enctype = "INPATIENT (OPD)";
				
		$ward = $ward_obj->getWardInfo($info['current_ward_nr']);
		#echo "sql = ".$ward_obj->sql;
		$location = strtoupper(strtolower(stripslashes($ward['name'])))."&nbsp;&nbsp;&nbsp;Room # : ".$info['current_room_nr'];
	}else{
		$enctype = "WALK-IN";
		$dept = $dept_obj->getDeptAllInfo($info['current_dept_nr']);
		$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
	}
	
	#$smarty->assign('sPatientType','<span id="show_patient_type" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px; font-size:10px;">'.$enctype.'</span>');
	#$smarty->assign('sPatientLoc','<span id="show_patient_loc" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px; font-size:10px;">'.$location.'</span>');
	
	$smarty->assign('sClassification',(($info['discountid']) ? $info['discountid'] : 'None'));
	$smarty->assign('sPatientType',(($enctype) ? $enctype : 'None'));
	$smarty->assign('sPatientLoc',(($location) ? $location : 'None'));
		
	#---------------------
	
	#if (($hasPaid==1)||($view_from=='ssview')){ 
	if (($hasPaid==1)||($view_from=='ssview')||($hasResult)){ 
		#$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" align="absmiddle">');
		$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;" value="Clear" onclick="clearEncounter()" disabled />');
		#$smarty->assign('sSelectEnc','<input class="segInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" style="margin-left:2px" disabled/>');
		#$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').' disabled />Cash');
		#$smarty->assign('sIsCharge','<input class="jedInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').' disabled />Charge');
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" disabled value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" style="font-weight:bold;">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" disabled value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label for="iscash0" style="font-weight:bold;">Charge</label>');
		#$smarty->assign('sIsTPL','<input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($info["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label>');
		
		#added by VAN 06-02-08
		$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label class="jedInput" for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
		$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" disabled type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($info["is_tpl"]=="1")?'checked="checked" ':'').'/><label for="is_tpl" style="color:#006600; font-weight:bold;">To pay later</label></span>');
		$result = $srvObj->getChargeType();
		$options="";
		if (empty($info['type_charge']) || ($info['type_charge']==0))
			$info['type_charge'] = 3;
		
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
		/*
		$smarty->assign('sOrderDate','<input name="orderdate" id="orderdate" type="text" size="8" readonly 
											value="'.$requestDate.'" style="font:bold 12px Arial"
											onFocus="this.select();"  
											onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
											onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
											onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">');
		*/
		#added by VAN 06-02-08
		$dbtime_format = "Y-m-d H:i";
		$fulltime_format = "F j, Y g:ia";
		$curDate = date($dbtime_format);
		$curDate_show = date($fulltime_format);
	
		$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($requestDate)) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($requestDate)) : $curDate).'" style="font:bold 12px Arial">');
		$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' align="absmiddle">');
		
	}else{
		#$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" align="absmiddle" style="cursor:pointer">');
		$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;cursor:pointer" value="Clear" onclick="clearEncounter()"'.(($info['pid'])?'':' disabled="disabled"').' />');
		#$smarty->assign('sSelectEnc','<input class="segInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" style="margin-left:2px"/>');
		#$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType(1)" />Cash');
	   #$smarty->assign('sIsCharge','<input class="jedInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType(1)" />Charge');
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
		#$smarty->assign('sIsTPL','<input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($info["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label>');
		
		#added by VAN 06-02-08
		$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label class="jedInput" for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
		$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($info["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
		$result = $srvObj->getChargeType();
		$options="";
		if (empty($info['type_charge']) || ($info['type_charge']==0))
			$info['type_charge'] = 3;
		
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
		/*
		$smarty->assign('sOrderDate','<input name="orderdate" id="orderdate" type="text" size="8" 
											value="'.$requestDate.'" style="font:bold 12px Arial"
											onFocus="this.select();"  
											onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
											onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
											onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">');
		*/
		#edited by VAN 06-01-08
		$dbtime_format = "Y-m-d H:i";
		$fulltime_format = "F j, Y g:ia";
		$curDate = date($dbtime_format);
		$curDate_show = date($fulltime_format);
	
		$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($requestDate)) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($requestDate)) : $curDate).'" style="font:bold 12px Arial">');
		$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
		
	}	

	$count=0;
	#$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType()" />Cash');
	#$smarty->assign('sIsCharge','<input class="segInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType()" />Charge');
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$info["pid"].'"/>');
	#$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$request_name.'" style="font:bold 12px Arial; float:left;" '.$readOnly.'/>');
	#$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" '.$readOnly.'>'.$info['orderaddress'].'</textarea>');
	#$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;cursor:pointer" value="Clear" onclick="clearEncounter()"'.(($info['pid'])?'':' disabled="disabled"').' />');
	#$smarty->assign('sSelectEnc','<input class="segInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" style="margin-left:2px"/>');
	#$smarty->assign('sResetRefNo','<input class="segInput" type="button" disabled value="Reset" onClick ="resetRefno();" style="font:bold 11px Arial;"/>');
	#$smarty->assign('sNormalPriority','<input type="radio" name="priority" value="0" '.(($info["is_urgent"]!="1")?'checked="checked" ':'').'/>Normal');
	#$smarty->assign('sUrgentPriority','<input type="radio" name="priority" value="1" '.(($info["is_urgent"]=="1")?'checked="checked" ':'').'/>Urgent');
	#$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$info["comments"].'</textarea>');
	
	# Note: make a class function for this part later
	#echo "refno update = ".$Ref;
	$result = $srvObj->getOrderitems($Ref);
	#echo "sql = ".$srvObj->sql;
	#echo "<br> count = ".$srvObj->count."<br>";
	#print_r($db->ErrorMsg());
	$rows=array();
	while ($row=$result->FetchRow()) {
		$rows[] = $row;
	}
	
	# get the discount in seg_charity_grants
		
	$sql8 = "SELECT * FROM seg_charity_grants 
	         WHERE encounter_nr ='".$info['encounter_nr']."' 
				ORDER BY grant_dte DESC LIMIT 1"; 
	$res8=$db->Execute($sql8);
   $granted_discount=$res8->FetchRow();	
	//echo "discount = ".$granted_discount['discount'];
	#print_r($rows);
	#print_r($_POST['item']);
	foreach ($rows as $i=>$row) {
		if ($row) {
			$count++;
			$alt = ($count%2)+1;
			if ($info["is_cash"]){
				
				#----------added by VAN 10-19-07-------
				/*
				$sql3 = "SELECT * FROM seg_lab_services 
			   	      WHERE service_code='".$row['service_code']."'";
	   		$res3=$db->Execute($sql3);
				$row_orig=$res3->RecordCount();
					
				if ($row_orig!=0){
					$rsObj_orig=$res3->FetchRow();
					$prc_orig=$rsObj_orig['price_cash'];
				}
				*/
				#-----------------------------------------
				
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
			
			#-----------added by VAN 10-11-2007--
			# check if this request is already paid or not
			/*
			$sql2 = "SELECT * FROM seg_pay_request
                 WHERE ref_source = 'LD' 
					  AND ref_no = '".$Ref."'
					  AND service_code = '".$row['service_code']."'";
			*/
			$sql2 = "SELECT pr.ref_no,pr.service_code FROM seg_pay_request AS pr
						WHERE pr.ref_source = 'LD' 
						AND pr.ref_no = '".$Ref."'
						AND pr.service_code = '".$row['service_code']."'
						UNION
						SELECT gr.ref_no,gr.service_code FROM seg_granted_request AS gr
						WHERE gr.ref_source = 'LD' 
						AND gr.ref_no = '".$Ref."'
						AND gr.service_code = '".$row['service_code']."'";
			
		  	$res2=$db->Execute($sql2);
		   $rowpaid=$res2->RecordCount();
			
			/*
			if ($rowpaid==0){
				if (($hasPaid==1)||($view_from=='ssview')){ 
		   		#$delrow = '<img src="../../images/btn_delitem.gif" border="0"/>';
					$delrow = '<img src="../../images/btn_unpaiditem.gif" border="0"/>';
				}else{
					$delrow = '<a href="javascript: nd(); removeItem(\''.$row['service_code'].'\')"><img src="../../images/btn_delitem.gif" border="0"/></a>';
				}	
			}else{
				$delrow = '<img src="../../images/btn_paiditem.gif" align="absmiddle" border="0"/>'; 
			}
			*/
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
					}else{
						$delrow = '<img src="../../images/btn_paiditem.gif" align="absmiddle" border="0"/>'; 
					}	
				}
			}	

			
			$sql3 = "SELECT is_socialized FROM seg_lab_services WHERE service_code = '".$row['service_code']."'"; 
			$res3=$db->Execute($sql3);
		   $social=$res3->FetchRow();
			/*
			if ($social['is_socialized']==0){
				$sservicon = '<img src="../../images/btn_nonsocialized.gif" border="0" align="absmiddle"/>';
			}else{
				$sservicon = '';
			}
			*/
			#----------------------------------------
			#sservicon = '<img src="../../images/btn_nonsocialized.gif" border="0" align="absmiddle"/>';
			
			$toolTipText = "Requesting doctor: <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$doctor_name ."<br>".
							    "Department: <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$dept_name ."<br>".	
							    "Clinical Impression: <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".stripslashes(strtoupper($row['clinical_info']));
			
			#if ($view_from!='')
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
			
				}
			/*
				onMouseOver="return overlib($(\'toolTipText'.$row['service_code'].'\').value, CAPTION,\'Details\',  
					  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', 
					  WIDTH, 250,FGCLASS,\'olfgleft\',FGCOLOR, \'#bbddff\');" onmouseout="nd();"
			*/					 
							
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
			
			#echo "<br>sql 1= ".$sql4." - ".$cash_C1;
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
					
			#echo "<br>sql 2= ".$sql5." - ".$cash_C2;
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
			#echo "<br>sql 3= ".$sql6." - ".$cash_C3;
			#$discount_price
			#-----------------------------------				
			#echo "<br>person_discountid = ".$prc;
			/*
			$sql7 = "SELECT * FROM seg_service_discounts WHERE discountid='".$info["discountid"]."' AND service_code = '".$row['service_code']."'"; 
			$res7=$db->Execute($sql7);
		   $sservice_price=$res7->FetchRow();
			*/
			
			if ($info["discountid"] == 'C1')
			    $sservice_price = $discount_priceC1["price"];
			elseif ($info["discountid"] == 'C2')
			    $sservice_price = $discount_priceC2["price"];
			elseif ($info["discountid"] == 'C3')
			    $sservice_price = $discount_priceC3["price"];	 	 
				 
			/*
				if ((ssClass[i].value==1) && ((person_discountid)||(person_discountid)) && ((person_discountid=="C1")||(person_discountid=="C2")||(person_discountid=="C3"))){
			if (prcSS==0){	
				if ((document.getElementById('hasPaid').value==1)||(document.getElementById('view_from').value=='ssview')){
					netprice = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" value="'+formatNumber(parseFloat(prcList[i].value),2)+'" size="5" style="text-align:right" readonly onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshTotal();">';
				}else{
					netprice = '<input type="text" id="totprice'+id+'" name="totprice'+id+'" value="'+formatNumber(parseFloat(prcList[i].value),2)+'" size="5" style="text-align:right" onBlur="getSocialPrice(\''+id+'\'); formatNumber(this.value,2); refreshTotal();">';
				}
			}else{
				netprice = formatNumber(parseFloat(prcList[i].value),2);
			}
		}else if ((ssClass[i].value==1) && (person_discountid) &&  ((person_discountid!="C1")||(person_discountid!="C2")||(person_discountid!="C3"))){
			netprice = parseFloat(prcList[i].value) - (parseFloat(prcList[i].value) * parseFloat(discount_percentage));
			netprice = formatNumber(netprice,2);
		}else{
			netprice = formatNumber(parseFloat(prcList[i].value),2);
		}
			*/
			
			if (($social['is_socialized']==1) && (($info["discountid"]=="C1")||($info["discountid"]=="C2")||($info["discountid"]=="C3"))){
				if ($sservice_price==0){
				#if ($sservice_price['price']==0){
					
					#if (($hasPaid==1)||($view_from=='ssview')){ 
					if (($hasPaid==1)||($view_from=='ssview')||($hasResult)){ 
						$tot_price = '<input type="text" id="totprice'.$row['service_code'].'" name="totprice'.$row['service_code'].'" size="10" readonly onKeyDown="keyEnter(event, this,\''.$row['service_code'].'\');" style="text-align:right" onBlur="getSocialPrice(\''.$row['service_code'].'\'); formatNumber(this.value,2); refreshDiscount();" value="'.number_format($prc, 2).'">';
					}else{
						$tot_price = '<input type="text" id="totprice'.$row['service_code'].'" name="totprice'.$row['service_code'].'" size="10" onKeyDown="keyEnter(event, this,\''.$row['service_code'].'\');" style="text-align:right" onBlur="getSocialPrice(\''.$row['service_code'].'\'); formatNumber(this.value,2); refreshDiscount();" value="'.number_format($prc, 2).'">';
					}	
				}else{
					$tot_price = number_format($prc, 2);
				}	
			}else{
				$tot_price = number_format($prc, 2);
			}	
			/*
				<input type="hidden" name="price_C1B[]" id="rowpriceC1B'.$row['service_code'].'" value="'.$discount_priceC1["price"].'" />
				<input type="hidden" name="price_C2B[]" id="rowpriceC2B'.$row['service_code'].'" value="'.$discount_priceC2["price"].'" />
				<input type="hidden" name="price_C3B[]" id="rowpriceC3B'.$row['service_code'].'" value="'.$discount_priceC3["price"].'" />
						
			*/			
			#$adjust_amount = tot - (tot * parseFloat($F('discount')));
			$adjust_amount = 0;
			#<input type="hidden" name="adjust_amount[]" id="rowadjust_amount'.$row['service_code'].'" value="'.$adjust_amount.'" />
			#$row['service_code'] = addslashes($row['service_code']);
			#$row['service_code'] = addslashes(urlencode($row['service_code']));
			#echo "<br>code = ".$row['service_code'];
			#echo "<br>code = ".addslashes(urldecode($row['service_code']));
			
			#----added by VAN 06-03-08
			#echo "<br>row = ".$row['service_code']." - ".$_POST['withsample'];
			
			if ($row['is_forward'])
				$checked = "checked";
			else
				$checked = "";	
				
			$forwarding = '<input type="checkbox" name="withsampleID'.$row['service_code'].'" '.$checked.' id="withsampleID'.$row['service_code'].'" value="1" />';
			#-------------------
			
			$src .= 
					'<tr class="wardlistrow'.$alt.'" id="row'.$row['service_code'].'">
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
	#echo "<br>src = <br>".$src;
	if ($src) $smarty->assign('sOrderItems',$src);
	
	#$smarty->assign('sViewPDF','<input type="image" '.createLDImgSrc($root_path,'viewpdf.gif','0','left').' align="absmiddle" name="viewfile" id="viewfile" onClick="viewPatientRequest(\''.$info["is_cash"].'\',\''.$info["pid"].'\',\''.$Ref.'\');">');
	$smarty->assign('sViewPDF','<img name="viewfile" id="viewfile" onClick="viewPatientRequest(\''.$info["is_cash"].'\',\''.$info["pid"].'\',\''.$Ref.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'viewpdf.gif','0','left') . ' border="0">');
	#$smarty->assign('sViewPDF','<img '.createLDImgSrc($root_path,'viewpdf.gif','0','left').' border="0" align="absmiddle" name="viewfile" id="viewfile" onClick="viewPatientRequest(\''.$info["is_cash"].'\',\''.$info["pid"].'\',\''.$Ref.'\');">');
#-----------added by VAN 12-21-07---
}elseif ((isset($_POST["submited"]) && $saveok) || ((($mode="save") && $repeat))){
	# Fetch order data
	
	if ($_GET["ref"]!=NULL)
		$Ref = $_GET["ref"];
	elseif ($prevrefno!=NULL)
		$Ref = $prevrefno;
	else
		$Ref = $data["refno"];
	
	#-------------------
	
	$sql = "SELECT pr.ref_no,pr.service_code FROM seg_pay_request AS pr
			  WHERE pr.ref_source = 'LD' AND pr.ref_no = '".$Ref."'
			  UNION
			  SELECT gr.ref_no,gr.service_code  FROM seg_granted_request AS gr
           WHERE gr.ref_source = 'LD' AND gr.ref_no = '".$Ref."'";
	$res=$db->Execute($sql);
	$row2=$res->RecordCount();
	
	#added by VAN 03-07-08
	$sqlPaid = "SELECT or_no, pr.ref_no,pr.service_code FROM seg_pay_request AS pr
			   WHERE pr.ref_source = 'LD' AND pr.ref_no = '".$Ref."' LIMIT 1";
					
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
	
	#if ($info["pid"]==" "){
	/*
	if (($info["pid"]==" ") || (empty($info["pid"]))){
		$request_name = trim($info['ordername']);
	}else{
		$person = $person_obj->getAllInfoArray($info["pid"]);
		$request_name = trim($person['name_first'])." ".trim($person["name_2"])." ".trim($person["name_last"]);
		#$request_name = ucwords(strtolower($request_name));
		$request_name = ucwords(strtolower(stripslashes($request_name)));
		#$request_name = htmlspecialchars($request_name);
	}	
	*/
	if (empty($info['pid']) || !$info['pid']){
		$request_name = stripslashes(trim($info['ordername']));
	}else{
		# in case there is an updated profile of the person
		$request_name = trim($info['name_first']).' '.trim($info['name_last']);
	}
	
	$smarty->assign('sRefNo','<input name="refno" id="refno" readonly="1" type="text" size="8" value="" style="font:bold 12px Arial"/>');
	
	if ($info['serv_dt']) {
			$time = strtotime($info['serv_dt']);
			$requestDate = date("m/d/Y",$time);
	}
	
	$hasResult = $srvObj->hasResult($Ref);
	
	#if (($hasPaid==1)||($view_from=='ssview')){ 
	if (($hasPaid==1)||($view_from=='ssview')||($hasResult)){ 
		#$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" align="absmiddle">');
		$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;" value="Clear" onclick="clearEncounter()" disabled />');
		#$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').' disabled />Cash');
		#$smarty->assign('sIsCharge','<input class="jedInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').' disabled />Charge');
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
		#$smarty->assign('sIsTPL','<input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($info["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label>');
		#added by VAN 06-02-08
		$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label class="jedInput" for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
		$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($info["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
		$result = $srvObj->getChargeType();
		$options="";
		if (empty($info['type_charge']) || ($info['type_charge']==0))
			$info['type_charge'] = 3;
		
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
		/*
		$smarty->assign('sOrderDate','<input name="orderdate" id="orderdate" type="text" size="8" readonly 
											value="'.$requestDate.'" style="font:bold 12px Arial"
											onFocus="this.select();"  
											onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
											onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
											onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">');
		*/
		#added by VAN 06-02-08
		$dbtime_format = "Y-m-d H:i";
		$fulltime_format = "F j, Y g:ia";
		$curDate = date($dbtime_format);
		$curDate_show = date($fulltime_format);
	
		$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($requestDate)) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($requestDate)) : $curDate).'" style="font:bold 12px Arial">');
		$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
	
	}else{
		#$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" align="absmiddle" style="cursor:pointer">');
		$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;cursor:pointer" value="Clear" onclick="clearEncounter()"'.(($info['pid'])?'':' disabled="disabled"').' />');
		#$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType(1)" />Cash');
	   #$smarty->assign('sIsCharge','<input class="jedInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType(1)" />Charge');
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
		#$smarty->assign('sIsTPL','<input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($info["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label>');
		
		#added by VAN 06-02-08
		$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label class="jedInput" for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
		$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($info["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
		$result = $srvObj->getChargeType();
		$options="";
		if (empty($info['type_charge']) || ($info['type_charge']==0))
			$info['type_charge'] = 3;
		
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
		
		/*
		$smarty->assign('sOrderDate','<input name="orderdate" id="orderdate" type="text" size="8" 
											value="'.$requestDate.'" style="font:bold 12px Arial"
											onFocus="this.select();"  
											onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
											onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
											onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">');
		*/
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
	
	#echo "refno repeat= ".$Ref;
	# Note: make a class function for this part later
	#$result = $srvObj->getOrderitems($Ref);
	
	# edited by VAN 01-09-08
	
	if (empty($serv_code))
		$serv_code = "";
	
	#echo "s = $serv_code";	
	$result = $srvObj->getOrderitems($Ref, $serv_code);
	$rows=array();
	while ($row=$result->FetchRow()) {
		$rows[] = $row;
	}
	
	# get the discount in seg_charity_grants
		
	$sql8 = "SELECT * FROM seg_charity_grants 
	         WHERE encounter_nr ='".$info['encounter_nr']."' 
				ORDER BY grant_dte DESC LIMIT 1"; 
	$res8=$db->Execute($sql8);
   $granted_discount=$res8->FetchRow();	
	
	foreach ($rows as $i=>$row) {
		if ($row) {
			$count++;
			$alt = ($count%2)+1;
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
			
			$sql2 = "SELECT pr.ref_no,pr.service_code FROM seg_pay_request AS pr
						WHERE pr.ref_source = 'LD' 
						AND pr.ref_no = '".$Ref."'
						AND pr.service_code = '".$row['service_code']."'
						UNION
						SELECT gr.ref_no,gr.service_code FROM seg_granted_request AS gr
						WHERE gr.ref_source = 'LD' 
						AND gr.ref_no = '".$Ref."'
						AND gr.service_code = '".$row['service_code']."'";
			
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
					}else{
						$delrow = '<img src="../../images/btn_paiditem.gif" align="absmiddle" border="0"/>'; 
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
						$tot_price = '<input type="text" id="totprice'.$row['service_code'].'" name="totprice'.$row['service_code'].'" size="10" onKeyDown="keyEnter(event, this,\''.$row['service_code'].'\');" style="text-align:right" onBlur="getSocialPrice(\''.$row['service_code'].'\'); formatNumber(this.value,2); refreshDiscount();" value="'.number_format($prc, 2).'">';
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
			
			$forwarding = '<input type="checkbox" name="withsample[]" '.$checked.' id="withsampleID'.$row['service_code'].'" value="1" />';
			
			$src .= 
					'<tr class="wardlistrow'.$alt.'" id="row'.$row['service_code'].'">
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
	
	$smarty->assign('sViewPDF','<img name="viewfile" id="viewfile" onClick="viewPatientRequest(\''.$info["is_cash"].'\',\''.$info["pid"].'\',\''.$Ref.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'viewpdf.gif','0','left') . ' border="0">');

#-----------------------------------
}else {
	#$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1"  checked="checked" onchange="if (changeTransactionType) changeTransactionType(1)" />Cash');
	#$smarty->assign('sIsCharge','<input class="jedInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" onchange="if (changeTransactionType) changeTransactionType(1)" />Charge');
	$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked="checked" onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
	$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
	#$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" /><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
	
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value=""/>');
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="" style="font:bold 12px Arial;" readonly/>');
	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial; cursor:pointer" value="Clear" onclick="clearEncounter()" disabled/>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" readonly></textarea>');
	$smarty->assign('sRefNo','<input class="segInput" name="refno" id="refno" type="text" size="10" readonly="1" value="'.$lastnr.'" style="font:bold 12px Arial"/>');
	#$smarty->assign('sResetRefNo','<input class="segInput" type="button" value="Reset" onClick ="resetRefno();" disabled style="font:bold 11px Arial; cursor:pointer"/>');
	
	#$curDate = date("m/d/Y  h:i A");
	#$curDate = date("m/d/Y  h:i A");
	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";
	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);
	/*
	$smarty->assign('sOrderDate','<input name="orderdate" id="orderdate" type="text" size="20" 
											value="'.$curDate.'" style="font:bold 12px Arial"
											onFocus="this.select();"  
											onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
											onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
											onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">');
											
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" align="absmiddle" style="cursor:pointer">');
	*/
	#edited by VAN 06-01-08
	$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
	
	#$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));
	$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label class="jedInput" for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
	
	#added by VAN 06-02-08
	$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" /><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
	$result = $srvObj->getChargeType();
	$options="";
	if (empty($type_charge) || ($type_charge==0))
		$type_charge = 3;
		
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
	
	#if (($hasPaid==1)||($view_from=='ssview')){ 
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
		/*
		$smarty->assign('sSelectEnc','<input class="segInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" style=""
      	 onclick="overlib(
        	 OLiframeContent(\'seg-lab-select-enc.php\', 700, 400, \'fSelEnc\', 1, \'auto\'),
        	 WIDTH,700, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        	 CAPTIONPADDING,4, 
				CAPTION,\'Select registered person\',
          MIDX,0, MIDY,0, 
          STATUS,\'Select registered person\'); return false;"
          onmouseout="nd();"/>');
		*/
		#DRAGGABLE,
#edited by VAN 02-06-08
/*		
		$smarty->assign('sSelectEnc','<a href="javascript:void(0);"
      	 onclick="overlib(
        	 OLiframeContent(\'seg-lab-select-enc.php\', 700, 400, \'fSelEnc\', 1, \'auto\'),
        	 WIDTH,700, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL, 
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        	 CAPTIONPADDING,4, 
				CAPTION,\'Select registered person\',
          MIDX,0, MIDY,0, 
          STATUS,\'Select registered person\'); return false;"
          onmouseout="nd();"/>
			 <img name="select-enc" id="select-enc" src="'.$root_path.'images/btn_encounter_small.gif" border="0"></a>');

		$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
          onclick="return overlib(
          OLiframeContent(\'seg-request-tray.php\', 600, 400, \'fOrderTray\', 1, \'auto\'),
          WIDTH,600, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL, 
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
          CAPTIONPADDING,4, 
				CAPTION,\'Add laboratory service item from request tray\',
          MIDX,0, MIDY,0, 
          STATUS,\'Add laboratory service item from request tray\');"
          onmouseout="nd();">
			 <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');
*/		
		#added by VAN 03-04-08
		$var_arr = array(
			"var_pid"=>"pid",
			"var_encounter_nr"=>"encounter_nr",
			"var_discountid"=>"discountid",
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
		
		$smarty->assign('sSelectEnc','<img class="segInput" name="select-enc" id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer;"
       onclick="overlib(
        OLiframeContent(\''.$root_path."modules/registration_admission/seg-select-enc.php?$var_qry&var_include_enc=1',".
				'700, 395, \'fSelEnc\', 0, \'auto\'),
        WIDTH,700, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL, 
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Select registered person\',
        MIDX,0, MIDY,0, 
        STATUS,\'Select registered person\'); return false;"
       onmouseout="nd();">');
		
		/*
		$smarty->assign('sSelectEnc','<a href="javascript:void(0);"
      	 onclick="SelectPatient();"
          onmouseout="nd();"/>
			 <img name="select-enc" id="select-enc" src="'.$root_path.'images/btn_encounter_small.gif" border="0"></a>');
		*/
		$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
          onclick="AddItem();"
          onmouseout="nd();">
			 <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');

		#$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()"><img src="'.$root_path.'images/btn_emptylist.gif" border="0" /></a>');
		$smarty->assign('sBtnEmptyList','<img type="image" name="btnEmpty" id="btnEmpty" src="'.$root_path.'images/btn_emptylist.gif" border="0" style="cursor:pointer;" onclick="emptyTray();"></a>');
	}
	
	$smarty->assign('sResetRefNo','<input class="segInput" type="button" disabled value="Reset" onClick ="resetRefno();" style="font:bold 11px Arial;"/>');
	#$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()"><img src="'.$root_path.'images/btn_emptylist.gif" border="0" /></a>');
	#$smarty->assign('sDiscountInfo','<img src="'.$root_path.'images/discount.gif">');
	
	/*
	$sql_discount = "SELECT * FROM seg_charity_amount 
	                 WHERE ref_no = '".$info["refno"]."'
						  AND ref_source = 'LD'";
	#echo "<br>sql = ".$sql_discount;
	
	$res_discount=$db->Execute($sql_discount);
	$row_discount=$res_discount->RecordCount();
	*/
	
	$granted_discount_amount = $srvObj->getSocialDiscount($info["refno"]);	
	/*
	if ($row_discount!=0)
		$granted_discount_amount=$res_discount->FetchRow();	
	*/
	
	#echo "<br>granted_discount_amount = ".$granted_discount_amount['amount'];	
	if (empty($granted_discount_amount['amount'])){
		$adjusted_amount = 0.00;
	}else{
		$adjusted_amount = $granted_discount_amount['amount'];				
	}	
	#echo "<br>amount = ".number_format($discount_amount,2);	
	
	#$smarty->assign('sAdjustedAmount','<input id="show-discount" name="show-discount" type="text" onBlur="formatDiscount(this.value);" readonly style="color:#006600; font-family:Arial; font-size:15px; font-weight:bold; text-align:right" size="5" value="'.number_format($adjusted_amount,2).'"/>');
	$smarty->assign('sAdjustedAmount','<input id="show-discount" name="show-discount" type="hidden" onBlur="formatDiscount(this.value);" readonly style="color:#006600; font-family:Arial; font-size:15px; font-weight:bold; text-align:right" size="5" value="'.number_format($adjusted_amount,2).'"/>');
	
	#enable only in the social service
	
	if ($view_from=='ssview'){ 
		#echo "sulod ssview";
		$smarty->assign('sBtnDiscounts','<img name="discountbtn" id="discountbtn" onclick="saveDiscounts();" style="cursor:pointer" src="'.$root_path.'images/btn_discounts2.gif" border="0">');
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
	
	#$smarty->assign('sClassification','<input class="segClearInput" type="text" readonly id="discountid" name="discountid" value="'.empty($classification)?"NONE":$classification.'" style="font:bold 12px Arial;color:#0000FF;" size="25">');
	#if (empty($classification))
	#	$classification = "NONE";
	
	#$smarty->assign('sClassification','<input class="segClearInput" type="text" readonly id="discountid" name="discountid" value="'.$classification.'" style="font:bold 12px Arial;color:#0000FF;" size="25">');
	
	#if (($_POST['repeat'])||($info['parent_refno'])){
	#if (($info['parent_refno']!=NULL)||($info['parent_refno']!="")){
	#echo "prev1 = ".$prevrefno;
	if ($info['parent_refno']){
		$repeat=1;
		$prevrefno = $info['parent_refno'];
	}	
	
	$smarty->assign('sRepeat','<input type="checkbox" name="repeat" id="repeat" value="yes" '.(($repeat=="1")?'checked="checked" ':'').' disabled>');
	#echo "sulod";
	if ($repeat){
		if (empty($serv_code)){
			#$code = $serv_code;
			$condition = "s.parent_refno = $prevrefno";
		}else{
			#$code = $serv_code;
			$condition = "s.parent_refno = $prevrefno AND d.service_code='$serv_code'";
		}
					
		#$result_prev = $srvObj->getRepeatRequestInfo($prevrefno, $code);
		$result_prev = $srvObj->getRepeatRequestInfo($condition);
		#echo "<br>sql = ".$srvObj->sql."<br>";
		#print_r($result_prev);

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
		$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label class="jedInput" for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
		$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($_POST["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
		$result = $srvObj->getChargeType();
		$options="";
		if (empty($_POST['type_charge']) || ($_POST['type_charge']==0))
			$_POST['type_charge'] = 3;
		
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
			/*
			$smarty->assign('sOrderDate','<input name="orderdate" id="orderdate" type="text" size="8" 
											value="'.$requestDate.'" style="font:bold 12px Arial"
											onFocus="this.select();"  
											onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
											onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
											onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">');
			*/
			#added by VAN 06-02-08
			$dbtime_format = "Y-m-d H:i";
			$fulltime_format = "F j, Y g:ia";
			$curDate = date($dbtime_format);
			$curDate_show = date($fulltime_format);
	
			$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($requestDate)) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($requestDate)) : $curDate).'" style="font:bold 12px Arial">');
			$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');											
		}
		
	}
	
	#$smarty->assign('sLocation','<select id="loc_code" name="loc_code">
	#											<option>-Select Patient Location-</option>	
	#									 </select>');
	/*
	$loc_info=&$srvObj->getAllLocationObject();
	$sTemp = '';
	$sTemp = $sTemp.'<select name="loc_code" id="loc_code">
								<option value="">-Select Patient Location-</option>';
									if(!empty($ward_info)&&$ward_info->RecordCount()){
										while($station=$ward_info->FetchRow()){
							$sTemp = $sTemp.'
								<option value="'.$station['nr'].'" ';
							if(isset($current_ward_nr)&&($current_ward_nr==$station['nr'])) $sTemp = $sTemp.'selected';
							$sTemp = $sTemp.'>'.$station['name'].'</option>';
						}
					}
					$sTemp = $sTemp.'</select>
							<font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'> '.$LDForInpatient.'</font>';
				}
	$smarty->assign('sLocation',$sTemp);
	*/
#-------------added by VAN----------
#edited by VAN 02-06-08
/*
	$jsCalScript = "<script type=\"text/javascript\">
		Calendar.setup ({
			inputField : \"orderdate\", ifFormat : \"$phpfd\", showsTime : false, button : \"orderdate_trigger\", singleClick : true, step : 1
		});
	</script>
	";
*/	
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
	?>
  
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
	<input type="hidden" id="encounter_nr" name="encounter_nr" value="<?php if ($info["encounter_nr"]) echo $info["encounter_nr"]; else $encounter_nr;?>">
	<input type="hidden" id="discountid" name="discountid" value="<?php if ($info["discountid"]) echo $info["discountid"]; else $discountid;?>">
	<input type="hidden" id="discount" name="discount" value="<?php if ($granted_discount['discount']) echo $granted_discount['discount']; else $discount;?>">
	<input type="hidden" name="hasPaid" id="hasPaid" value="<?=$hasPaid?$hasPaid:'0'?>">
	<input type="hidden" name="view_from" id="view_from" value="<?=$view_from?$view_from:''?>">
	<input type="hidden" name="encoder_id" id="encoder_id" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>">
	<input type="hidden" name="latest_valid_show-discount" id="latest_valid_show-discount" value="<?=number_format($adjusted_amount, 2, '.', '')?>" >
	<input type="hidden" name="popUp" id="popUp" value="<?=$popUp;?>">
	
	<input type="hidden" name="paidCash" id="paidCash" value="<?=$paidCash?>">
	
	<input type="hidden" name="key" id="key" value="<?=$key?>">
	
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


#if ((($hasPaid==1)||($view_from=='ssview'))&&($repeat!=1)){ 
if ((($hasPaid==1)||($view_from=='ssview')||($hasResult))&&($repeat!=1)){ 
	#commented by VAN 03-06-08
	#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" >');
	$smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" border="0" align="center">');
}else{
	#commented by VAN 03-06-08
	#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
	#$smarty->assign('sContinueButton','<input type="image" src="'.$root_path.'images/btn_submitorder" align="center">');
	$smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" border="0" align="center" style="cursor:pointer" onclick="prufform();">');
}	

//if (($view_from!='ssview') || ($popUp!=1)){ 
if (($view_from!='ssview') && ($popUp!=1)){ 
	
	#edited by VAN 02-06-08
	#$fileforward="seg-lab-request-new-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
	#$smarty->assign('sViewRequest','<a href="'.$fileforward.'"><img '.createLDImgSrc($root_path,'showrequest.gif','0','left').' border=0 alt="View the List of Requestors"></a>');
	$smarty->assign('sViewRequest','<a href="javascript:RequestList();"><img '.createLDImgSrc($root_path,'showrequest.gif','0','left').' border=0 alt="View the List of Requestors"></a>');
	
	#$fileforward2="seg-lab-request-new.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
	#$smarty->assign('sAddNewRequest','<a href="'.$fileforward2.'" nd();><img '.createLDImgSrc($root_path,'newrequest.gif','0','left').' border=0 alt="Enter New Lab Request"></a>');
	#commented by VAN 03-05-08
	#$smarty->assign('sAddNewRequest','<a href="javascript:NewRequest();" nd();><img '.createLDImgSrc($root_path,'newrequest.gif','0','left').' border=0 alt="Enter New Lab Request"></a>');
}	

$smarty->assign('sSocialServiceNotes','<img src="'.$root_path.'images/btn_nonsocialized.gif"> <span style="font-style:italic">Nonsocialized service.</span>');
$smarty->assign('social_display',$social_display);

#added by VAN 03-03-08
/*
$smarty->assign('sRefreshDiscountButton','<input type="button" name="btnRefreshDiscount" id="btnRefreshDiscount" onclick="refreshDiscount()" value="Refresh Discount" style="cursor:pointer ">');
$smarty->assign('sRefreshTotalButton','<input type="button" name="btnRefreshTotal" id="btnRefreshTotal" onclick="refreshTotal()" value="Refresh Totals" style="cursor:pointer ">');
*/

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','laboratory/form_new.tpl');
$smarty->display('common/mainframe.tpl');

?>