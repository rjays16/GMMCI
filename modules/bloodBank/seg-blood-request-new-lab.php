<?php
	# Start Smarty templating here
 /**
 * LOAD Smarty
 */
	
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
 	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
#	define('LANG_FILE','lab.php');
	$lang_tables[] = 'departments.php';
	define('LANG_FILE','konsil.php');
	define('NO_2LEVEL_CHK',1);
#	$local_user='ck_lab_user';
	$local_user='ck_lab_user';   # burn added : September 24, 2007
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');
	require($root_path.'modules/bloodBank/ajax/blood-request-new.common.php');

	# Create global config object
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/inc_date_format_functions.php');

	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('refno_%');
	if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
	$date_format=$GLOBAL_CONFIG['date_format'];

	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	#$phpfd=str_replace("yy","%y", strtolower($phpfd));

$title="Blood Bank";

$breakfile=$root_path.'modules/laboratory/labor.php'.URL_APPEND; 
$thisfile=basename(__FILE__);

	# Create radiology object
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab();
	
	#added by VAN 06-17-08
	require_once($root_path.'include/care_api_classes/class_ward.php');
	$ward_obj = new Ward;
	
	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	#-------------------
	
	#added by VAN 06-25-08
	require_once($root_path.'include/care_api_classes/class_social_service.php');
	$objSS = new SocialService;
		
	#added by VAN 07-08-08
	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj = new Person;
	
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	#-------------------	
		
		
#	global $db;
	
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');

	if (!isset($popUp) || !$popUp){
		if (isset($_GET['popUp']) && $_GET['popUp']){
			$popUp = $_GET['popUp'];
		}
		if (isset($_POST['popUp']) && $_POST['popUp']){
			$popUp = $_POST['popUp'];
		}
	}
	
	# added by VAN 01-11-08
	
	if ($_GET['repeat'])
		$repeat = $_GET['repeat'];
	else
		$repeat = $_POST['repeat'];	
	
    $is_dr = $_GET['is_dr'];      
	#echo "<br>get repeat = ".$repeat."<br>";	
		
	if ($_GET['prevbatchnr'])
		$prevbatchnr = $_GET['prevbatchnr'];
		
	if ($_GET['prevrefno'])	
		$prevrefno = $_GET['prevrefno'];
	
	#echo "repeat - batch - refno = ".$repeat." - ".$prevbatchnr." - ".$prevrefno;
	
	#added by VAN 03-19-08
	$repeaterror = $_GET['repeaterror'];
	
	#added by VAN 06-25-08
	$discountid_get = $_GET['discountid'];
	
	#echo "<br>repeaterror = ".$_GET['repeaterror'];
	#echo "<br>repeat = ".$_GET['repeat'];
	
	#added by VAN 07-08-08
	if ($_GET['encounter_nr'])
		$encounter_nr = $_GET['encounter_nr'];
	
	if ($_GET['area'])
		$area = $_GET['area'];	
	
	if ($_GET['pid'])
		$pid = $_GET['pid'];
	#---------------------
    
    if ($encounter_nr){
        $patient = $enc_obj->getEncounterInfo($encounter_nr);
    }
	
	if ($repeaterror){
		#$smarty->assign('sWarning',"<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!");
		$smarty->assign('sysErrorMessage','<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!');
	}
	#-----------------------------
	
	#added by VAN 01-29-08
	$_POST['serv_tm'] = date('H:i:s');
	
	#added by VAN 06-16-08
	if (empty($_POST['is_tpl'])){
		$_POST['is_tpl'] = '0';
	}/*elseif($_POST['is_tpl']){
		$_POST['type_charge'] = '0';
	}*/
	#-----------------
	
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
			'headpasswd'=>$_POST['headpasswd']
		);
		
		if ($_POST['orderdate']) {
			$data["serv_dt"] = date("Ymd",strtotime($_POST['orderdate']));
			$data["serv_tm"] = date("H:i:s",strtotime($_POST['orderdate']));
		}

		if ($_POST["pid"]) $data["pid"] = $_POST["pid"];
	
	switch($mode){
		case 'save':
				$data["refno"] = $new_refno;
				$data["history"] = "Create: ".date('Y-m-d H:i:s')." [\\".$_SESSION['sess_temp_userid']."]\\n";
				$srvObj->useLabServ();
				$srvObj->setDataArray($data);
				if ($repeat){
					$srvObj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
					$isCorrectInfo = $srvObj->count;
					if ($isCorrectInfo){
						$saveok=$srvObj->insertDataFromInternalArray();
					}else{
						header("Location: seg-blood-request-new.php".URL_REDIRECT_APPEND."&user_origin=$user_origin&repeat=$repeat&prevrefno=".$_POST['parent_refno']."&serv_code=".stripslashes($_POST["items"][0])."&paid=1&popUp=1&repeaterror=1");
					}
				}else{
					$saveok=$srvObj->insertDataFromInternalArray();
				}	
				#echo "sql = ".$db->errorMsg();
				#echo "<br>add sql = ".$srvObj->sql;
				break;
		case 'update':
				$data["refno"] = $_POST["refno"];
									
				if ($data["refno"]==NULL)
					$data["refno"] = $_GET["refno"];
									
									
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
					if ($isCorrectInfo){
						$saveok=$srvObj->updateDataFromInternalArray($data["refno"]);
					}else{
						#echo '<em class="warn">Sorry but you are not allowed to do a repeat request!</em>';
						header("Location: seg-blood-request-new.php".URL_REDIRECT_APPEND."&user_origin=$user_origin&repeat=$repeat&prevrefno=".$_POST['parent_refno']."&serv_code=".stripslashes($_POST["items"][0])."&paid=1&popUp=1&repeaterror=1");
					}	
				}else{
					$saveok=$srvObj->updateDataFromInternalArray($data["refno"]);
				}	
				#echo "<br>update sql = ".$srvObj->sql;
				break;
/*				
		case 'cancel':

				if($srvObj->deleteRefNo($_POST['refno'])){

					header('Location: '.$breakfile);
					exit;
				}else{
					$errorMsg='<font style="color:#FF0000">'.$srvObj->getErrorMsg().'</font>';
				}
				break;
*/				
	}# end of switch stmt	
	
	if ($saveok) {
			if ($_POST["items"]!=NULL){
				
				$bulk = array();
				#$withsample = array();
				foreach ($_POST["items"] as $i=>$v) {
					/*
					#withsampleID
					$sampleid = 'withsampleID'.$_POST["items"][$i];
					if ($_POST[$sampleid]!=1)
						$_POST[$sampleid] = 0;
					*/	
					
					if ($_POST['is_cash']){
						
						#---------added by VAN 10-17-07-----------
						if (empty($discountid)){
							if ($discountid == 'C1'){
								$cash_price = $_POST["price_C1"][$i];
							}elseif ($discountid == 'C2'){
								$cash_price = $_POST["price_C2"][$i];	
							}elseif ($discountid == 'C3'){
								$cash_price = $_POST["price_C3"][$i];		
							}else{
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
				$smarty->assign('sysInfoMessage',"Laboratory Service item successfully created.");
			elseif ($mode=='update')
				$smarty->assign('sysInfoMessage',"Laboratory Service item successfully updated.");		
		}
		else {
			$errorMsg = $db->ErrorMsg();
			#echo "error = ".$errorMsg;
			if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
				$smarty->assign('sysErrorMessage','<strong>Error:</strong> A laboratory service with the same request number already exists in the database.');
				#$smarty->assign('sWarning',"<strong>Error:</strong> $errorMsg");
			#elseif (strpos(strtolower($errorMsg), "latest billing") !== FALSE)
				#$smarty->assign('sWarning',"<strong>Error:</strong> $errorMsg");
			else{
				if ($errorMsg!=NULL)
					$smarty->assign('sysErrorMessage',"<strong>Error:</strong> $errorMsg");
				else	
					$smarty->assign('sysErrorMessage',"<strong>Error:</strong> Request must have at least one laboratory service.");
			}
		} 
	

	if (!isset($refno) || !$refno){
		if (isset($_GET['refno']) && $_GET['refno']){
			$refno = $_GET['refno'];
		}
		if (isset($_POST['refno']) && $_POST['refno']){
			$refno = $_POST['refno'];
		}
		
		if (empty($refno)){
			$refno = $_GET['prevrefno'];
			$prevrefno = $refno;
		}	
	}
	
	# added by VAN 01-15-08
	$refInfo = $srvObj->getRequestInfoByPrevRef($prevrefno,$prevbatchnr);
	
	if ($refInfo['parent_refno'])
		//$refno = $refInfo['parent_refno'];
		$refno = $refInfo['refno'];
	
	$mode='save';   # default mode
	if ($refNoBasicInfo = $srvObj->getBasicRadioServiceInfo($refno)){
		#echo "van:seg-radio-request-new = ".$srvObj->sql;
		$mode='update';
		extract($refNoBasicInfo);
		if (empty($refNoBasicInfo['pid']) || !$refNoBasicInfo['pid']){
			$person_name = trim($refNoBasicInfo['ordername']);
		}else{
				# in case there is an updated profile of the person
			$person_name = trim($refNoBasicInfo['name_first']).' '.trim($refNoBasicInfo['name_last']);
		}
#echo "nursing-station-radio-request-new.php : before : request_date='".$serv_dt."' <br> \n";			
		$serv_dt = formatDate2Local($serv_dt,$date_format); 
#echo "nursing-station-radio-request-new.php : after : request_date='".$serv_dt."' <br> \n";			
	}#end of if-stmt
	#added by VAN 07-08-08
	#elseif (($pid)&&($area=="ER")){
	elseif (($pid)&&(!empty($area))){
		#echo "pid = ".$pid;
		$patientInfo = $person_obj->getAllInfoArray($pid);
		$person_name = ucwords(strtolower($patientInfo['name_first']))." ".ucwords(strtolower($patientInfo['name_last']));
		
		if ($patientInfo['street_name'])
			$addr_comma = ",";
		$orderaddress = ucwords(strtolower($patientInfo['street_name'])).$addr_comma." ".ucwords(strtolower($patientInfo['brgy_name']))." ".ucwords(strtolower($patientInfo['mun_name']));
		
		$rid = $srvObj->RIDExists($pid);
	}
	
  #added by VAN 06-25-08
   if (!(trim($discountid))){
	  	$discountid = $discountid_get;
		
	 #$discount
     $socialInfo = $objSS->getSSClassInfo($discountid_get);
	 #echo "discount = ".$socialInfo['discount'];
	 if (trim($discount)==0)
	 	$discount = $socialInfo['discount'];	
   }
   #--------------------	 

 # Title in the title bar
 
 $smarty->assign('sToolbarTitle',"Blood Bank :: New Test Request");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

if ($popUp!='1'){
		 # href for the close button
	 $smarty->assign('breakfile',$breakfile);
}else{
		# CLOSE button for pop-ups
	 #edited by VAN 07-11-08
	 #if ($area=='ER')
	 if ($area)
	 	$smarty->assign('breakfile','javascript:window.parent.cClick();');
	 else	
	 	$smarty->assign('breakfile','javascript:window.parent.pSearchClose();');
	 
	 $smarty->assign('pbBack','');
}

 # Window bar title
 $smarty->assign('sWindowTitle',"Blood Bank :: New Test Request");

 # Assign Body Onload javascript code
 
 #$onLoadJS='onLoad="preSet();"';
 #edited by VAN 06-14-08
 $onLoadJS='onLoad="CheckRepeatInfo();checkCash();"';
 #echo "onLoadJS = ".$onLoadJS;
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

ob_start();
	 # Load the javascript code
    $xajax->printJavascript($root_path.'classes/xajax-0.2.5');	 
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

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>

			<!-- START for setting the DATE (NOTE: should be IN this ORDER...i think soo..) -->
<script type="text/javascript" language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script language="javascript" src="<?=$root_path?>js/dtpick_care2x.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
			<!-- END for setting the DATE (NOTE: should be IN this ORDER...i think soo..) -->

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/blood-request-new.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	//added by VAN 06-14-08
	function checkCash(){
		if ($("iscash1").checked){
			document.getElementById('is_cash').value = 1;
			//$('tplrow').style.display = '';
			$('type_charge').style.display = '';
			
		}else{
			document.getElementById('is_cash').value = 0;	
			//$('tplrow').style.display = 'none';
			//$('type_charge').style.display = '';
			$('type_charge').style.display = '';
			//document.getElementById('is_tpl').checked = false;
		}	
	}
	//----------------------
	
	// added by VAN 01-11-08
	function CheckRepeatInfo(){
		if (document.getElementById('repeat').checked){
			document.getElementById('repeatinfo01').style.display = '';
			document.getElementById('repeatinfo02').style.display = '';
			document.getElementById('repeatinfo03').style.display = '';
			
			//added by VAN 03-19-08
			document.getElementById('repeatinfo04').style.display = '';
			document.getElementById('repeatinfo05').style.display = '';
			
			document.getElementById('show-discount').value = formatNumber(0,2);
		}else	{
			document.getElementById('repeatinfo01').style.display = 'none';
			document.getElementById('repeatinfo02').style.display = 'none';
			document.getElementById('repeatinfo03').style.display = 'none';
			
			//added by VAN 03-19-08
			document.getElementById('repeatinfo04').style.display = 'none';
			document.getElementById('repeatinfo05').style.display = 'none';
		}	
	}	
		//----------------------------
	
	function eDiscount(amount,bol){
		document.getElementById('show-discount').value = amount;
		document.getElementById('show-discount').disabled = bol;
		if(bol){	
			document.getElementById('btndiscount').style.display = 'none';
		}else{
			document.getElementById('btndiscount').style.display = '';
		}
	}
	
	function saveDiscounts(){
		var refno, amtDiscount, encoderId; 
		refno = document.getElementById("refno").value;
		amtDiscount = document.getElementById("show-discount").value;
		encoderId = document.getElementById("encoder_id").value;
				
		if((amtDiscount == '')||(amtDiscount == 0)||isNaN(amtDiscount)){
			alert("Please enter discount.");
//			$('show-discount').value='0.00';
			$('show-discount').value=$F('latest_valid_show-discount');//reset to the lastest valid value
			document.getElementById('show-discount').focus();
		}else{
			//alert("save discounts value " + amtDiscount + " refno =" + refno + "\n encoder =" + encoderId );	
			if (refreshDiscount()){
				xajax_setCharityDiscounts(refno, encoderId, amtDiscount);
				$('latest_valid_show-discount').value=$F('show-discount');
				//refreshDiscount();
			}else{
				$('show-discount').value=$F('latest_valid_show-discount');//reset to the lastest valid value
				refreshDiscount();
			}
		}
	}
	
	//added by VAN 06-25-08
	function saveDiscounts2(){
		inputform.submit();
	}
	
	//added by VAN 07-10-08
	function NewRequest(){
		urlholder="seg-blood-request-new.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
		window.location.href=urlholder;
	}
	
	
-->
</script>

<?php
#echo "nursing-station-radio-request-new.php : hasPaid='".$hasPaid."' <br> \n";			
	if ($popUp=='1'){
		echo $reloadParentWindow;
	}
	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->append('JavaScript',$sTemp);

	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');
	#$smarty->assign('sRID','<input class="segInput" id="rid" name="rid" type="text" size="10" value="'.$rid.'" style="font:bold 12px Arial;" readonly>');
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$person_name.'" style="font:bold 12px Arial;" readonly>');

	$var_arr = array(
		"var_pid"=>"pid",
		"var_encounter_nr"=>"encounter_nr",
		"var_discountid"=>"discountid",
        "var_orig_discountid"=>"orig_discountid",   
		"var_discount"=>"discount",
		"var_name"=>"ordername",
		"var_addr"=>"orderaddress",
		"var_clear"=>"clear-enc",
		"var_area"=>"area"
	);
	
	$vas = array();
	foreach($var_arr as $i=>$v) {
		$vars[] = "$i=$v";
	}
	$var_qry = implode("&",$vars);
	 
	 #edited by VAN 06-18-08
	 #if ($area=="ER"){
	 if ($area){
	  	$smarty->assign('sSelectEnc','<img name="select-enc" id="select-enc" src="'.$root_path.'images/btn_encounter_small.gif" border="0">');
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
	   
	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="cursor:pointer;font:bold 11px Arial" value="Clear" onclick="clearEncounter()" disabled>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" readonly>'.$orderaddress.'</textarea>');
	#$smarty->assign('sSSClassID','<input type="text" name="discountid" id="discountid" size="5" value="'.$discountid.'" readonly style="font:bold 12px Arial">');
	
	#added by VAN 06-16-08
	if ($area=="ER"){
			$enctype = "ER PATIENT";	
			$location = "EMERGENCY ROOM";
			$encounter_type = 1;
		
			$medico = $enc_obj->getEncounterMedicoCases($encounter_nr, $pid);
			#echo $enc_obj->sql;
			if ($medico)
				$info = $medico->FetchRow();
				$is_medico = $info['is_medico'];
	}elseif ($area=="clinic"){
			
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
            
            $encounter_type = $patient['encounter_type'];
            #$enctype = "OUTPATIENT";
			#$encounter_type = 2;	
			$dept = $enc_obj->getEncounterDept($encounter_nr);
			$location = mb_strtoupper($dept['name_formal']);
		
			$medico = $enc_obj->getEncounterMedicoCases($encounter_nr, $pid);
			#echo $enc_obj->sql;
			if ($medico)
				$info = $medico->FetchRow();			
	}else{	
		if ($encounter_type==1){
			$enctype = "ER PATIENT";
			$location = "EMERGENCY ROOM";
		}elseif ($encounter_type==2){
			#$enctype = "OUTPATIENT (OPD)";
			$enctype = "OUTPATIENT";
			$dept = $dept_obj->getDeptAllInfo($current_dept_nr);
			$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
		}elseif (($encounter_type==3)||($encounter_type==4)){
			if ($encounter_type==3)
				$enctype = "INPATIENT (ER)";
			elseif ($encounter_type==4)
				$enctype = "INPATIENT (OPD)";
				
			$ward = $ward_obj->getWardInfo($current_ward_nr);
			#echo "sql = ".$ward_obj->sql;
			$location = strtoupper(strtolower(stripslashes($ward['name'])))."&nbsp;&nbsp;&nbsp;Room # : ".$current_room_nr;
		}else{
			$enctype = "WALK-IN";
			$dept = $dept_obj->getDeptAllInfo($current_dept_nr);
			$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
		}
	}	
	
	if((!($discountid))&&($encounter_type)){
		#echo "enc = ".$encounter_type;
		if ($encounter_type==2)
			$ss = $objSS->getPatientSocialClass($pid, 1);
		else
			$ss = $objSS->getPatientSocialClass($pid, 0);	
			
		$discountid = $ss['discountid'];	
	}
	
	$ssInfo = $objSS->getSSClassInfo($discountid);

	if (($ssInfo['parentid'])&&($ssInfo['parentid']=='D')){
		$discountid = $ssInfo['parentid'];
		$discount = $ssInfo['discount'];
	}else{
		$discountid = $ssInfo['discountid'];
		$discount = $ssInfo['discount'];
	}	
	
	#echo "pid = ".$pid;
	if (empty($pid))
		$pid = $info['pid'];
		
		$ss_sc = $objSS->getPatientSocialClass($pid, 0);
		
		if ($ss_sc['discountid']=='SC')
			$_POST["issc"] = 1;
		else	
			$_POST["issc"] = 0;
	
		if (($_POST["issc"])&&(trim($encounter_type)==""))
			$discount = 0.20;	
			
	if ($_GET['view_from']=='ssview'){
		$discountid = $_GET['discountid'];
		$ssInfo = $objSS->getSSClassInfo($discountid);
		$discount = $ssInfo['discount'];
	}		
	#------------

	$smarty->assign('sDiscountShow','<input type="checkbox" disabled name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' ><label for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
	
	if (((isset($_POST['select-enc']))||($mode=='update'))||($area)){
		$smarty->assign('sClassification',(($discountid) ? $discountid : 'None'));
		$smarty->assign('sPatientType',(($enctype) ? $enctype : 'None'));
		$smarty->assign('sPatientLoc',(($location) ? $location : 'None'));
		#is_medico
		$smarty->assign('sPatientMedicoLegal',(($is_medico) ? "YES" : 'NO'));
	}
	#---------------------
	
	# added by VAN 01-14-08
	#echo "repeat = ".$repeat;
	
	if (($repeat)&&(empty($refInfo['parent_refno'])))
		$Ref = "";
	else{
		#if ($area=="ER"){
		#if ($area=="ER"){
		if ($is_cash==0){
			$Ref = $refno;
			$Ref2 = $refno;
	   }else{	
			#edited by VAN 07-05-08
			$srvObj->getSumPaidPerTransaction($refno,$pid);
		
			if ($srvObj->count){
				$Ref2 = $refno;
			}else{
				#echo "here = ".$discount;
				#if(($is_cash==0) || ($discount==1.00))
				if(($is_cash==0) || ($discount==1.00) || ($type_charge))
					$Ref2 = $refno;	
				else
					$Ref2 = "";	
					
			}	
			$Ref = $refno;
		}	
	}	
	#echo "refno = ".$refno."<br>";
	#commented by VAN	
	#$smarty->assign('sRefNo','<input class="segInput" name="refno" id="refno" type="text" size="10" value="'.$refno.'" readonly style="font:bold 12px Arial"/>');
	$smarty->assign('sRefNo','<input class="segInput" name="refno2" id="refno2" type="text" size="10" value="'.$Ref2.'" readonly style="font:bold 12px Arial"/><input name="refno" id="refno" type="hidden" size="10" value="'.$Ref.'"/>');
#	$smarty->assign('sResetRefNo','<input class="segInput" type="button" value="Reset" style="font:bold 11px Arial"/>');
	
	#---------------added by VAN -----------------------
	if (($parent_refno)&&($parent_batch_nr)){
		$repeat=1;
	}
	
	if (empty($parent_refno))
		$parent_refno = $refno;
	elseif ($prevrefno)	
		$parent_refno = $prevrefno;
	
	#echo "batch = ".$prevbatchnr;
		
	if ((empty($parent_batch_nr))||($prevbatchnr))
		$parent_batch_nr = $prevbatchnr;
	
	#echo "batch, head, remarks = ".$parent_batch_nr." - ".$approved_by_head." - ".$remarks;
	
	$smarty->assign('sRepeat','<input type="checkbox" name="repeat" id="repeat" value="yes" '.(($repeat=="1")?'checked="checked" ':'').' disabled>');
	#$smarty->assign('sParentBatchNr','<input class="segInput" id="parent_batch_nr" name="parent_batch_nr" type="text" size="40" value="'.$parent_batch_nr.'" style="font:bold 12px Arial;" readonly/>');
	$smarty->assign('sParentBatchNr','<input class="segInput" id="parent_refno" name="parent_refno" type="text" size="40" value="'.$parent_refno.'" style="font:bold 12px Arial;" readonly/><input id="parent_batch_nr" name="parent_batch_nr" type="hidden" size="40" value="'.$parent_batch_nr.'" style="font:bold 12px Arial;"/>');
	$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="37" rows="2" style="font:bold 12px Arial">'.stripslashes($remarks).'</textarea>');
	$smarty->assign('sHead','<input class="segInput" id="approved_by_head" name="approved_by_head" type="text" size="40" value="'.$approved_by_head.'" style="font:bold 12px Arial;"/>');
	
	#added by VAN 03-18-08
	$smarty->assign('sHeadID','<input class="segInput" id="headID" name="headID" type="text" size="40" value="" style="font:bold 12px Arial;"/>');
	$smarty->assign('sHeadPassword','<input class="segInput" id="headpasswd" name="headpasswd" type="password" size="40" value="" style="font:bold 12px Arial;"/>');
	#-----------------------------------------------------
	
	# commented by VAN
	#$curDate = ($serv_dt)? $serv_dt:date("m/d/Y");
	
	#if (($repeat)||(empty($serv_dt))){
		#$curDate = date("m/d/Y");
		$dbtime_format = "Y-m-d H:i";
		$fulltime_format = "F j, Y g:ia";
		
		$curDate = date($dbtime_format);
		$curDate_show = date($fulltime_format);
		#echo "time = ".$curDate;
		#echo "<br>ftime = ".$curDate_show;
	/*
	}else{
		#$curDate = 	$serv_dt;
		$curDate = 	$serv_dt." ".serv_tm;
		$dbtime_format = "Y-m-d H:i";
		$fulltime_format = "F j, Y g:ia";
		$curDate = date($dbtime_format,strtotime($curDate));
		$curDate_show = date($fulltime_format,strtotime($curDate));
	}	
	*/
		
	$jsCalScript = "
			<script type=\"text/javascript\">
				Calendar.setup ({
					displayArea : \"show_orderdate\",
					inputField : \"orderdate\", 
					ifFormat : \"%Y-%m-%d %H:%M\", 
					daFormat : \"	%B %e, %Y %I:%M%P\", 
					showsTime : true, 
					button : \"orderdate_trigger\", 
					singleClick : true, 
					step : 1
				});
			</script>";
	/*
	$smarty->assign('sOrderDate','<input name="serv_dt" id="serv_dt" type="text" size="10" 
											value="'.$curDate.'" style="font:bold 12px Arial"
											onFocus="this.select();"  
											onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
											onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
											onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">');

	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="serv_dt_trigger" name="serv_dt_trigger" align="absmiddle" style="cursor:pointer">'.$jsCalScript);
	*/
	
	$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">'.$jsCalScript);
	
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority0" value="0"'.($is_urgent? "": " checked").'>Routine');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority1" value="1"'.($is_urgent? " checked": "").'>STAT');
	
	if ($area=="ER"){
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($pid)?'':'checked="checked" ').' '.(($pid)?'disabled="disabled" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($pid)?'checked="checked" ':'').' '.(($pid)?'':'disabled="disabled" ').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
	}elseif ($area=="clinic"){
         
        if ($is_dr){
              # echo "type = ".$patient['encounter_type']; 
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
        $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" disabled onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
         }    
          
    }else{
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($is_cash!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($is_cash=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
	}
		
	#added by VAN 06-14-08
	
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
	#-------------------------
	
	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$comments.'</textarea>');
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"8\">Request list is currently empty...</td>
				</tr>");

if ($is_pay_full)
		$onchecked = "checked";
	else
		$onchecked = "";
				
	#$smarty->assign('sPayFull','<input type="checkbox" name="ispayfull" id="ispayfull" value="1" '.$onchecked.' onchange="checkIfFull()" /><b>Pay Full?</b>');			
	
$smarty->assign('sBtnAddItem','<img type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_additems.gif" border="0" style="cursor:pointer;"
			onclick="return overlib(
				OLiframeContent(\'seg-blood-service-tray.php?area='.$area.'&dr_nr='.$dr_nr.'&dept_nr='.$dept_nr.'\', 600, 435, \'fOrderTray\', 1, \'auto\'),
					WIDTH,435, TEXTPADDING,0, BORDER,0, 
					STICKY, SCROLL, CLOSECLICK, MODAL, 
					CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
					CAPTIONPADDING,4, 
					CAPTION,\'Add blood bank service item from request tray\',
					MIDX,0, MIDY,0, 
					STATUS,\'Add blood bank service item from request tray\');"
			onmouseout="nd();">');

$smarty->assign('sBtnEmptyList','<img type="image" name="btnEmpty" id="btnEmpty" src="'.$root_path.'images/btn_emptylist.gif" border="0" style="cursor:pointer;" onclick="emptyTray();"></a>');

$smarty->assign('sAdjustedAmount','<input id="show-discount" name="show-discount" type="hidden" onBlur="formatDiscount(this.value);" readonly style="color:#006600; font-family:Arial; font-size:15px; font-weight:bold; text-align:right" size="5" value="'.number_format($adjusted_amount,2).'"/>');

#edited by VAN 06-25-08
if ($view_from=='ssview'){ 
	#echo "discountid = ".$discountid;
	if ($discountid)
		$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" onclick="saveDiscounts2();" style="cursor:pointer" src="'.$root_path.'images/btn_discounts2.gif" border="0">');
	else
		$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_discounts2.gif" border="0" style="display:none">');
}elseif ($hasPaid==1){
	#echo "sulod paid";
	$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_discounts2.gif" border="0" style="display:none">');
}else{
	#echo "sulod 1";
	$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_discounts2.gif" border="0" style="display:none">');
}

$smarty->assign('sSocialServiceNotes','<img src="'.$root_path.'images/btn_nonsocialized.gif"> <span style="font-style:italic">Nonsocialized service.</span>');

 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'?popUp='.$popUp.'" method="POST" name="inputform" id="inputform" onSubmit="return checkRequestForm()">');
 $smarty->assign('sFormEnd','</form>');
 #echo "refno & batch = ".$refno." - ".$prevbatchnr;
 
?>
<?php
ob_start();
$sTemp='';

# added by VAN 01-14-08
#echo "b4 ref, batch = ".$refno." - ".$batchnr;
if (!empty($Ref)){
	$refno = $Ref;
	#$batchnr = $Ref; 
}else{
	if ($refInfo['parent_batch_nr'])
		$batchnr = $refInfo['batch_nr'];
	else	
		$batchnr = $prevbatchnr;
}
#echo "<br>after ref, batch = ".$refno." - ".$batchnr;
?>
	<script type="text/javascript" language="javascript">
		preset(<?= ($is_cash=='0')? "0":"1"?>);
		//xajax_populateRequestListByRefNo(<?=$refno? $refno:0?>);	
		
		// edited by VAN 01-11-08
		xajax_populateRequestListByRefNo(<?=$refno? $refno:0?>,<?=$batchnr? $batchnr:0?>);	
		
		//xajax_getCharityDiscounts(<?=$refno?>);
	</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

if ($mode=='update'){
	$smarty->assign('sIntialRequestList',$sTemp);
}

ob_start();
$sTemp='';
?>
	<input type="hidden" name="submitted" value="1">
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">  
<!--  
	<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
-->
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
	
	<?php
		$discountInfo = $objSS->getSSClassInfo($discountid);
		
		if ($discountInfo){
			$discount = 	$discountInfo['discount'];
		}	
		#echo "sc = ".$_POST['issc'];
		#echo "<br>type = ".$encounter_type;
		
		if(($_POST['issc'])&&(trim($encounter_type)=="")){
			$discount = 0.20;
		}
		#echo "pid = $pid";
		if (empty($orig_discountid)){
			$sql_discount = "SELECT discountid  FROM seg_charity_grants_pid   WHERE pid='".$pid."' ORDER BY grant_dte DESC LIMIT 1";
  
		   $res_discount=$db->Execute($sql_discount);
		   $discount_info=$res_discount->FetchRow();
    
		   $orig_discountid = $discount_info['discountid'];
		}
	?>
	
	<input type="hidden" name="is_cash" id="is_cash" value="<?=$is_cash?>" >
	<input type="hidden" id="encounter_nr" name="encounter_nr" value="<?=$encounter_nr?>">
	<input type="hidden" name="discount2" id="discount2" value="<?=$discount2?>" >
	<input type="hidden" name="discount" id="discount" value="<?=$discount?>" >
	<input type="hidden" name="latest_valid_show-discount" id="latest_valid_show-discount" value="<?=number_format($adjusted_amount, 2, '.', '')?>" >
	
	<!-- added by VAN 06-16-08 -->
	<!--<input type="hidden" id="discountid" name="discountid" value="<?php if ($info["discountid"]) echo $info["discountid"]; else $discountid;?>">-->
	<input type="hidden" id="orig_discountid" name="orig_discountid" value="<?=$orig_discountid?>">
    <input type="hidden" id="discountid" name="discountid" value="<?=$discountid;?>">
	
	<input type="hidden" id="is_pay_full" name="is_pay_full" value="<?=$is_pay_full?>" />
	
	<!-- -->
	
	
	<?php 
		#----- added by VAN 01-12-08
		if ((empty($refInfo['parent_batch_nr']))&&(empty($refInfo['parent_refno']))&&(empty($Ref)))
			$mode='save';		
		else
			$mode='update';		
		#---------------------------
	?>
	
	<input type="hidden" name="mode" id="mode" value="<?=$mode?$mode:'save'?>">
	<input type="hidden" name="popUp" id="popUp" value="<?=$popUp?$popUp:'0'?>">
	<input type="hidden" name="hasPaid" id="hasPaid" value="<?=$hasPaid?$hasPaid:'0'?>">
	<input type="hidden" name="view_from" id="view_from" value="<?=$view_from?$view_from:''?>">
	<input type="hidden" name="encoder_id" id="encoder_id" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>">
	
	<input type="hidden" name="repeat01" id="repeat01" value="<?= $repeat?$repeat:'0'?>">
	
	<input type="hidden" name="area" id="area" value="<?=$area?>" />
	<input type="hidden" name="ptype" id="ptype" value="<?=$encounter_type?>" />

<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sHiddenInputs',$sTemp);

if (($mode=="update") && ($popUp!='1')){
	$sBreakImg ='cancel.gif';
	$smarty->assign('sBreakButton','<img type="image" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' align="center" alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';" style="cursor:pointer">');
}elseif ($popUp!='1'){
	$sBreakImg ='close2.gif';	
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}

#edited by VAN 06-27-08
$smarty->assign('sContinueButton','<img type="image" name="btnSubmit" id="btnSubmit" src="'.$root_path.'images/btn_submitorder.gif" align="center" style="cursor:pointer" onclick="if (confirm(\'Process this radiology request?\')) if (checkRequestForm()) document.inputform.submit()">');

if (($hasPaid)|| ((($encounter_type!='')||($encounter_type!=NULL)) && ($encounter_type!=2)) || ($type_charge) || $repeat)
	$smarty->assign('sClaimStub','<img name="claimstub" id="claimstub" onClick="viewClaimStub(\''.$is_cash.'\',\''.$refno.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'claim_stub.gif','0','left') . ' border="0">');


#added by VAN 07-10-08
#echo "from = ".$popUp;
if (($view_from!='ssview') && ($popUp!=1)){ 
	#$smarty->assign('sAddNewRequest','<a href="javascript:NewRequest();" nd();><img '.createLDImgSrc($root_path,'newrequest.gif','0','left').' border=0 alt="Enter New Radiology Request"></a>');
}
#---May29,2008 Note: replaced btn_submitrequest.gif above with btn_submitorder.gif for consistency w/ other modules---pet---

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','blood/blood-request-new.tpl');
$smarty->display('common/mainframe.tpl');

?>