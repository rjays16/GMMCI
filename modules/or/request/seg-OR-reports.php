<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
//require($root_path.'modules/or/ajax/ajax_report_common.php');
require_once($root_path.'modules/or/ajax/order.common.php'); //load the xajax module
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','lab.php');
#define('NO_2LEVEL_CHK',1);
define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');
//$db->debug=1;

$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$title="Operating Room :: Report Generator";
/* 2007-09-27 FDP
 replaced the orig line (which follows) for Close button target
$breakfile=$root_path."modules/pharmacy/seg-pharma-retail-functions.php".URL_APPEND."&userck=$userck";
 */
$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;
//$breakfile=$root_path.'modules/social_service/social_service_main.php'.URL_APPEND;
//$thisfile='seg-lab-reports.php';
$thisfile = 'seg-OR-reports.php';
//$returnfile = 'social_service_main.php';
//if ($send_details) include($root_path.'include/inc_retail_display_rdetails.php');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

	 require_once($root_path.'include/inc_front_chain_lang.php');
	# Create laboratory service object
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

	require_once($root_path.'include/care_api_classes/class_personell.php');
	$pers_obj=new Personell;

	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj=new Person();

	global $db;

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title");

 # href for the back button
 $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('report_how2generate.php','Report Generator')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title");

 # Assign Body Onload javascript code

 #Edited by Cherry 07-13-10
 $onLoadJS='onLoad="';
	# POST
	//if ($_POST['report_nr']) {
	if(isset($_POST['showed'])){
		$result=$db->Execute("SELECT rep_nr,rep_name,rep_script,rep_dept_nr FROM seg_reptbl WHERE rep_nr=".$_POST['report_nr']);
		$row=$result->FetchRow();

		if($_POST['report_nr']==50)
		$onLoadJS.="window.open('or_".$row['rep_script'].".php?observation=".$_POST['observe']."&from=".$_POST['from_date2']."&material=".$_POST['materials']."&environment=".$_POST['environment']."&endorsement=".$_POST['endorsement']."',null,'height=600,width=800,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');";
		else
		$onLoadJS.="window.open('or_".$row['rep_script'].".php?from=".$_POST['from_date']."&to=".$_POST['to_date']."',null,'height=600,width=800,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');";


	}
 $onLoadJS.='"';
 #End Cherry

 //$onLoadJS.='';
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

 ob_start();

	 # Load the javascript code
	#require($root_path.'include/inc_js_retail.php');
	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_modal.js"></script>'."\r\n";
	$sTemp = ob_get_contents();
 ob_end_clean();

 $smarty->append('JavaScript',$sTemp);

 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="post" name="inputform" onSubmit="return prufform()">');
 $smarty->assign('sFormEnd','</form>');

 # Assign form inputs (or values)

 /*--- Added by Cherry 08-08-10 ---*/
 #Submitted
 if(isset($_POST['submitted'])){
 $daterep = $_POST['from_date2'];
 $hum_prob = $_POST['observe'];
 $mat = $_POST['materials'];
 $env = $_POST['environment'];
 $endorse = $_POST['endorsement'];

 $index = "rep_date, human_resource, materials_equip, phy_environment,endorsement";

 $values = "'$daterep','$hum_prob', '$mat', '$env', '$endorse'";

	 $check = "SELECT * FROM seg_or_accomplishment_report WHERE rep_date='".$daterep."'";
		$db->Execute($check);
	 if($result){
		 $del = "DELETE FROM seg_or_accomplishment_report WHERE rep_date='".$daterep."'";
			$db->Execute($del);
	 }

	 $save = "INSERT INTO seg_or_accomplishment_report ($index) VALUES ($values)";
	 $saved = $db->Execute($save);
	 if($saved)
		echo "<script language='javascript'>saveReport();<script>";
 }
 $xajax->printJavascript($root_path.'classes/xajax_0.5');
 /*if(($_POST['from_date2'])){
	 echo "<script language='javascript'>alert('churvaloo');<script>";
 } */

 $dept_nr = '117'; // to be changed later...

 #------------Added by Cherry 07-13-10--------------------
 $result1=$db->Execute("SELECT rep_nr,rep_name,rep_script,rep_dept_nr FROM seg_reptbl WHERE rep_dept_nr='".$dept_nr."' ORDER BY rep_name");
	$options="<option value='0'>-Select Report-</option>";
	while ($row1=$result1->FetchRow()) {
		$options.='<option value="'.$row1['rep_nr'].'">'.$row1['rep_name'].'</option>';
	}
	$smarty->assign('sReportSelect',
"<select name=\"report_nr\" id=\"report_nr\" onChange=\"DisplayDept(this.value);\">
$options
</select>");
#-------------------End Cherry----------------------------

 //if ($saveok||$update) $smarty->assign('sOrderNrInput',$bestellnum.'</b><input type="hidden" name="bestellnum" value="'.$bestellnum.'">');
 //	else $smarty->assign('sOrderNrInput','<input type="text" name="bestellnum" value="'.$bestellnum.'" size=20 maxlength=20>');
	$result=$db->Execute("SELECT * FROM seg_discount ORDER BY discountdesc");
	#$options="";
	$options_grp = "";
	while ($row=$result->FetchRow()) {
		$options_grp.='<option value="'.$row['discountid'].'">'.$row['discountdesc'].'</option>';
	}
	$smarty->assign('sReportSelectGroup',
								"<select name=\"report_group\" id=\"report_group\">
										 <option value=\"all\">--All--</option>
										 $options_grp
								 </select>");

	#168 = social service dept
	$rs_socserv_personell = $pers_obj->getStaffOfDept(168);
	#echo "sql = ".$pers_obj->sql;
	$options_sworker = "";
	while ($row=$rs_socserv_personell->FetchRow()) {
		$staff_name = mb_strtoupper($row["name_last"]).", ".mb_strtoupper($row["name_first"])." ".mb_strtoupper($row["name_middle"]);
		$options_sworker.='<option value="'.$row['personell_nr'].'">'.$staff_name.'</option>';
	}
	$smarty->assign('sReportEncoder',
								"<select name=\"encoder\" id=\"encoder\">
										 <option value=\"all\">--All--</option>
										 $options_sworker
								 </select>");

	//$smarty->assign('sFromDateInput','<div id="show_from_date" style="margin-right:2px;padding:0px 2px;border:1px solid #888888;background-color:white;width:120px;min-height:18px;display:block;float:left"></div>');
	//$smarty->assign('sFromDateHidden','<input type="hidden" name="fromdt" id="from_date" value="">');
	//$smarty->assign('sFromDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="from_date_trigger" align="absmiddle" style="cursor:pointer">');
	//$smarty->assign('sToDateInput','<div id="show_to_date" style="margin-right:2px;padding:0px 2px;border:1px solid #888888;background-color:white;width:120px;min-height:18px;display:block;float:left"></div>');
	//$smarty->assign('sToDateHidden','<input type="hidden" name="todt" id="to_date" value="">');
	//$smarty->assign('sToDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="to_date_trigger" align="absmiddle" style="cursor:pointer">');

	//Added by Cherry 08-03-10
	$smarty->assign('sObservation', '<textarea name="observation" cols="43" name="observe">{{$observe}}</textarea>');
	$smarty->assign('sMaterials', '<textarea name="materials" cols="43" name="mats"></textarea>');
	$smarty->assign('sEnvironment','<textarea name="environment" cols="43" name="env"></textarea>');
	$smarty->assign('sEndorsement', '<textarea name="endorsement" cols="43" name="endorse"></textarea>');

	$smarty->assign('sFromDateInput2','<input type="text" name="from_date2" id="from_date2" value="" onchange="pertinentReport();" onclick="pertinentReport();">');
	$smarty->assign('sFromDateIcon2','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="from_date_trigger2" align="absmiddle" style="cursor:pointer">[YYYY-mm-dd]');
	//End Cherry

	$smarty->assign('sFromDateInput','<input type="text" name="from_date" id="from_date" value="">');
	$smarty->assign('sFromDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="from_date_trigger" align="absmiddle" style="cursor:pointer">[YYYY-mm-dd]');
	#$smarty->assign('sToDateInput','<div id="show_to_date" style="margin-right:2px;padding:0px 2px;border:1px solid #888888;background-color:white;width:120px;min-height:18px;display:block;float:left"></div>');
	$smarty->assign('sToDateInput','<input type="text" name="to_date" id="to_date" value="">');
	#$smarty->assign('sToDateHidden','<input type="hidden" name="todt" id="to_date" value="">');
	$smarty->assign('sToDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="to_date_trigger" align="absmiddle" style="cursor:pointer">[YYYY-mm-dd]');
	$jsCalScript = "<script type=\"text/javascript\">
Calendar.setup (
{
	inputField : \"from_date\",
	ifFormat : \"%Y-%m-%d\",
	displayArea : \"show_from_date\",
	daFormat : \"$phpfd\",
	showsTime : false,
	button : \"from_date_trigger\",
	singleClick : true,
	step : 1
}
);
Calendar.setup (
{
	inputField : \"to_date\",
	ifFormat : \"%Y-%m-%d\",
	displayArea : \"show_to_date\",
	daFormat : \"$phpfd\",
	showsTime : false,
	button : \"to_date_trigger\",
	singleClick : true, step : 1
}
);
Calendar.setup (
{
	inputField : \"from_date2\",
	ifFormat : \"%Y-%m-%d\",
	displayArea : \"show_from_date\",
	daFormat : \"$phpfd\",
	showsTime : false,
	button : \"from_date_trigger2\",
	singleClick : true,
	step : 1
}
);
</script>
";
	$smarty->assign('jsCalendarSetup', $jsCalScript);


# Collect hidden inputs

ob_start();
$sTemp='';
 ?>

	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">
	<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
	<!--<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">  --> <!--Edited by Cherry 07-13-10-->
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">

	<script type="text/javascript">
		function prufform(){
			var d = document.inputform;

				if (d.report_nr.value==0) {
					alert("Select the kind of report you want to generate.");
					d.report_nr.focus();
					return false;
				}
				if ((d.from_date.value=='')&&(d.to_date.value!='')) {
					alert("Enter the starting date of the report.");
					d.from_date.focus();
					return false;
				}

				if ((d.from_date.value!='') && (d.to_date.value=='')) {
					alert("Enter the end date of the report.");
					d.to_date.focus();
					return false;
				}

				if ((d.report_nr.value!=50) && (d.from_date.value=='') && (d.to_date.value=='')){
					alert("Enter start date and end date of report");
					d.from_date.focus();
					return false;
				}

				if (d.from_date.value > d.to_date.value){
						alert("Starting date should be earlier than the ending date");
					d.from_date.focus();
					return false;
				}

				if(d.report_nr.value==50 && d.from_date2.value==''){
					alert("Enter the date of the report.");
					d.from_date2.focus();
					return false;
				}

				return true;
		}

		function pertinentReport(){
			//alert('HALOO');
			var daterep = document.getElementById('from_date2').value;
			xajax_getData(daterep);
		}

		/* Added by Cherry 07-13-10 */
		function DisplayDept(rep_nr) {
			//alert(rep_nr);
			 if(rep_nr==47){
				 document.getElementById('section').style.display='';
				 document.getElementById('social_worker').style.display='';

				var rpt_group = document.getElementById('report_group').value;
				var fromdate = document.getElementById('from_date').value;
				var todate = document.getElementById('to_date').value;
				//var encoder = document.getElementById('encoder').value;
				//alert(encoder);
			 }
			 else if(rep_nr==50){
				document.getElementById('from').style.display='none';
				document.getElementById('to').style.display='none';
				document.getElementById('from2').style.display='';
				document.getElementById('prob_observation').style.display='';
				document.getElementById('prob_material').style.display='';
				document.getElementById('prob_environment').style.display='';
				document.getElementById('prob_endorsement').style.display='';
				document.getElementById('save').style.display='';
			 }
			 else{
				 //document.getElementById('section').style.display='none';
				 //document.getElementById('social_worker').style.display='none';
				 document.getElementById('from').style.display='';
				 document.getElementById('to').style.display='';
				 document.getElementById('from2').style.display='none';
				 document.getElementById('prob_observation').style.display='none';
				 document.getElementById('prob_material').style.display='none';
				 document.getElementById('prob_environment').style.display='none';
				 document.getElementById('prob_endorsement').style.display='none';
				 document.getElementById('save').style.display='none';
			 }
		}

		/* End Cherry */

		/**Added by Cherry 08-08-10 **/
		function saveReport(){
			//alert(document.getElemenstByName('endorse'));
			alert('successfully saved!');
			var daterep = document.getElementById('from_date2').value;
			var human_problems = document.getElementById('prob_observation').value;
			var materials = document.getElementById('prob_material').value;

		}
		/**End Cherry**/

		//function viewReport(report_group, report_class, fromdate, todate){
		function viewReport(){

			var bol = prufform();

			if (bol){
				var rpt_group = document.getElementById('report_group').value;
				var fromdate = document.getElementById('from_date').value;
				var todate = document.getElementById('to_date').value;
				var encoder = document.getElementById('encoder').value;

				//window.open("seg-lab-report-pdf.php?report_group="+rpt_group+"&report_class="+rpt_class+"&fromdate="+fromdate+"&todate="+todate+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
				//window.open("social_service_reports_pdf.php?report_group="+rpt_group+"&report_class=&fromdate="+fromdate+"&todate="+todate+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
				window.open("seg-socserv-report-pdf.php?class="+rpt_group+"&fromdate="+fromdate+"&todate="+todate+"&encoder="+encoder+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
			}
		}

	</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

	$smarty->assign('sHiddenInputs',$sTemp);
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
	#$smarty->assign('sContinueButton','<input type="image" '.createLDImgSrc($root_path,'continue.gif','0','left').' align="absmiddle">');
	#edited by Cherry 07-13-10
	#$smarty->assign('sContinueButton','<img name="viewreport" id="viewreport" onClick="viewReport();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif','0','left') . ' border="0">');
	$smarty->assign('sContinueButton','<input type="image" '.createLDImgSrc($root_path,'showreport.gif','0','left').' align="absmiddle">');
	#$smarty->assign('sContinueButton','<input type="image" '.createLDImgSrc($root_path,'savedisc.gif','0','left').' align="absmiddle">');

	#Added by Cherry 08-08-10
	$smarty->assign('sSaveButton', '<input style="display:none" type="image" '.createLDImgSrc($root_path, 'savedisc.gif','0','left').' align="absmiddle" id="save" onclick="saveReport();">');
	$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
	$smarty->assign('show', '<input type="hidden" value="TRUE" name="showed" />');
	# Assign the form template to mainframe
	//$smarty->assign('sMainBlockIncludeFile','laboratory/form_report.tpl');
	$smarty->assign('sMainBlockIncludeFile','or/or_form_reports.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>