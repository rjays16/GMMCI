<?php
		# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 #edited by VAN 06-30-2010

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

#    $allowedarea=array("_a_1_labcreaterequest");

		# Create laboratory service object
		require_once($root_path.'include/care_api_classes/class_encounter.php');
		$enc_obj=new Encounter;

		require_once($root_path.'include/care_api_classes/class_personell.php');
		$pers_obj=new Personell;

		require_once($root_path.'include/care_api_classes/class_department.php');
		$dept_obj=new Department;

		global $db;

		require_once($root_path.'gui/smarty_template/smarty_care.class.php');
		$smarty = new smarty_care('common');

		$smarty->assign('bHideTitleBar',TRUE);
		 $smarty->assign('bHideCopyright',TRUE);

		 # Title in the title bar
		 $smarty->assign('sToolbarTitle',"$title");

		 # href for the back button
		// $smarty->assign('pbBack',$returnfile);

		 # href for the help button
		 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

		 # href for the close button
		 $smarty->assign('breakfile',$breakfile);

		 # Window bar title
		 $smarty->assign('sWindowTitle',"$title");

		$stat = "<input type=hidden name='status' value='save'>";
		$radio = "&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='transaction_type' value='1' checked='checked'>Refer &nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='transaction_type' value='0'>Transfer";
		$encounter_nr = $_GET['encounter_nr'];
		$pid = $_GET['pid'];

		if($_GET["is_dept"]){
				$dpt = $_GET["is_dept"];
				if($dpt=="hosp")
						$is_dept = 0;
				else
						$is_dept = 1;
		}
		else{
				$is_dept = 1;
				$dpt = "dept";
		}
		$date = date("m/d/Y");
		$status = $_POST["status"];
		$userid = $_POST["doctor"];
		$result = $pers_obj->get_Person_name($userid);
		$doctor_name = $result["drtitle"] .$result["dr_name"];
		$total_srv = $enc_obj->countSearchAllAdmissionList($pid, '', 100, 0);
		$cnt = $enc_obj->count + 1;
		$cnt = $pid .$cnt;
		$selected_dept ="";
		$diagnosis = "";
		$notes = "";
		if($status=="save")
		{
				$encounter_nr = $_POST['encounter_nr'];
				$enc_obj->addReferral($encounter_nr, $_POST["transaction_type"], $_POST["date"], $_POST["refno"], $_POST["doctor"], $_POST["dept"], $_POST["diagnosis"], $_POST["notes"], $_POST["doctor"], $is_dept);
				echo "<script type='text/javascript'>window.parent.location = '$root_path/modules/registration_admission/aufnahme_daten_zeigen.php?ntid=false&lang=en&from=such&encounter_nr=$encounter_nr&target=search';</script>";
		}
		else if($status=="edit")
		{
				$encounter_nr = $_POST['encounter_nr'];
				$date = $_POST['date'];
				$transaction_type = $_POST['transaction_type'];
				$cnt = $_POST['refno'];
				$doctor_name = $_POST["doctor"];
				$userid = $_POST["doctor"];
				$selected_dept = $_POST["dept"];
				$diagnosis = $_POST["diagnosis"];
				$notes = $_POST["notes"];
				if($transaction_type=="Transfer")
				{
						$radio = "&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='transaction_type' value='1'>Refer &nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='transaction_type' value='0' checked='checked'>Transfer";
				}
				$stat = "<input type=hidden name='status' value='editsave'>";
		}
		else if($status=="editsave")
		{
				$encounter_nr = $_POST['encounter_nr'];
				$enc_obj->editReferral($encounter_nr, $_POST["transaction_type"], $_POST["date"], $_POST["refno"], $_POST["doctor"], $_POST["dept"], $_POST["diagnosis"], $_POST["notes"], $_POST["doctor"]);
				echo "<script type='text/javascript'>window.parent.location = '$root_path/modules/registration_admission/seg-admission-history.php?encounter_nr=$encounter_nr';</script>";
		}
		else if($status=="cancel")
		{
				$encounter_nr = $_POST['encounter_nr'];
				$referral_nr = $_POST['refno'];
				$reason = $_POST['reason'];
				$enc_obj->cancelReferral($referral_nr, $reason, $_POST["doctor"]);
				echo "<script type='text/javascript'>window.parent.location = '$root_path/modules/registration_admission/seg-admission-history.php?encounter_nr=$encounter_nr';</script>";
		}
		#$deptlist="";
		$deptlist = "<option value=0>-Select a Department-</option>";
		if($is_dept==1){
				$result = $dept_obj->getAllMedical();
				for($i=0; !empty($result[$i]); $i++)
				{
						if($selected_dept==$result[$i]["nr"])
								$deptlist = $deptlist ."<option value='" .$result[$i]["nr"]. "' selected='selected'>" .$result[$i]["name_formal"]. "</option>";
						else
								$deptlist = $deptlist ."<option value='" .$result[$i]["nr"]. "'>" .$result[$i]["name_formal"]. "</option>";
				}
		}
		else{
				$sql = "SELECT * FROM seg_other_hospital WHERE (ISNULL(status) OR status!='deleted')";
				$res = $db->Execute($sql);
				while($res && $row = $res->FetchRow())
				{
						if($selected_dept==$row["id"])
								$deptlist = $deptlist ."<option value='" .$row["id"]. "' selected='selected'>" .$row["hosp_name"]. "</option>";
						else
								$deptlist = $deptlist ."<option value='" .$row["id"]. "'>" .$row["hosp_name"]. "</option>";
				}
		}
		$doclist="";
		$result = $pers_obj->getDoctors();
		/*for($i=0; !empty($result[$i]); $i++)
		{
				echo $result[$i]["nr"];
				echo $pers_obj->get_Person_name($result[$i]["personell_nr"]);
				/*if($selected_dept==$result[$i]["nr"])
						$deptlist = $deptlist ."<option value='" .$result[$i]["nr"]. "' selected='selected'>" .$result[$i]["name_formal"]. "</option>";
				else
						$deptlist = $deptlist ."<option value='" .$result[$i]["nr"]. "'>" .$result[$i]["name_formal"]. "</option>";
		}*/
		$doclist = "<option value=0>-Select a Doctor-</option>";
		while($val = $result->FetchRow())
		{
				$tmp = $pers_obj->get_Person_name($val["personell_nr"]);
				$dname =  $tmp["dr_name"]." ".$tmp["drtitle"];
				//echo $userid;
				if($userid==$val["personell_nr"])
						$doclist = $doclist ."<option value='" .$val["personell_nr"]. "' selected='selected'>" .$dname. "</option>";
				else
						$doclist = $doclist ."<option value='" .$val["personell_nr"]. "'>" .$dname. "</option>";
		}

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
</script>

<!--added by VAN 02-06-08-->
<!--for shortcut keys -->
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>


<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/request-gui.js?t=<?=time()?>"></script>

<?php
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
?>
<?php
		$smarty->assign("sFormStart", "<form action='seg-patient-admission.php?is_dept=$dpt' method='post'>");
		$smarty->assign("sDate","<input type='text' name='date' id='date' value='".$date."' size=8>");
		$smarty->assign("miniCalendar",'<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="issuedate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer" onclick="javascript:callCalendar()">');
		$jsCalScript = "<script type=\"text/javascript\">
						function callCalendar(){
						Calendar.setup ({
								displayArea : \"date\",
								inputField : \"date\",
								ifFormat : \"%m/%d/%Y\",
								daFormat : \"%m/%d/%Y\",
								showsTime : false,
								button : \"issuedate_trigger\",
								singleClick : true,
								step : 1
						});
						}
				</script>";
		$smarty->assign("jsCalendarSetup", $jsCalScript);
		$smarty->assign("sRefer", $radio);
		$smarty->assign("sRefNo", "<input type='text' name=refno id=refno value='$cnt' readonly='readonly' size=8>");
		//$smarty->assign("sDoctor", "<input type='text' name=doctor id=doctor value='$doctor_name' readonly='readonly' size=25>");
		$smarty->assign("sDoctor", "<select name=doctor id=doctor>$doclist</select>");
		$smarty->assign("sDept", "<select name=dept>$deptlist</select>");
		$smarty->assign("sDiagnosis", "<textarea name=diagnosis id=diagnosis wrap='physical'  cols='45' rows='3' style='overflow-y:scroll; overflow-x:hidden; '>$diagnosis</textarea>");
		$smarty->assign("sNotes", "<textarea name=notes id=notes wrap='physical'  cols='45' rows='3' style='overflow-y:scroll; overflow-x:hidden; '>$notes</textarea>");
		$smarty->assign("sContinueButton", "<input type=image name=submit src='$root_path/images/btn_done.gif' value='done'>");
		//$smarty->assign("sContinueButton", "<a href='seg-patient-admission.php?status=save'><img src=$root_path/images/btn_done.gif border=0 onClick=''></a>");
		$smarty->assign("sFormEnd", "<input type=hidden name='docpid' id='docpid' value='$userid'><input type=hidden name='encounter_nr' value='$encounter_nr'>".$stat."</form>");

 ob_start();
 include_once($root_path."include/care_api_classes/class_discount.php");
$discountClass = new SegDiscount();
$src = "";

$sTemp = ob_get_contents();
ob_end_clean();
#edited by VAN 03-06-08
/*
$sBreakImg ='close2.gif';
$sBreakImg ='cancel.gif';
*/

$smarty->assign('sHiddenInputs',$sTemp);


# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','registration_admission/seg-patient-admission.tpl');
$smarty->display('common/mainframe.tpl');

?>
