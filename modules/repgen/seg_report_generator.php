<?php
#edited by VAN 04-12-09
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');
//$db->debug=1;

$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

require_once($root_path.'include/care_api_classes/class_dateGenerator.php');
$dategen = new DateGenerator;

require_once($root_path.'include/care_api_classes/class_personell.php');
	$pers_obj=new Personell;

require_once($root_path.'include/care_api_classes/class_insurance.php');
	$insurance_obj = new Insurance;
	
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$title="Report generator";
/* 2007-09-27 FDP
 replaced the orig line (which follows) for Close button target
$breakfile=$root_path."modules/pharmacy/seg-pharma-retail-functions.php".URL_APPEND."&userck=$userck";
 */

if ($_GET['from']!=''){
	if ($_GET['from']=='er')
		$breakfile=$root_path.'modules/er/seg-er-functions.php'.URL_APPEND;
	elseif ($_GET['from']=='opd')
		$breakfile=$root_path.'modules/opd/seg-opd-functions.php'.URL_APPEND;
	elseif ($_GET['from']=='ipd')
		$breakfile=$root_path.'modules/ipd/seg-ipd-functions.php'.URL_APPEND;
	elseif ($_GET['from']=='medocs')
		$breakfile=$root_path.'modules/medocs/seg-medocs-functions.php'.URL_APPEND;

}else
	$breakfile=$root_path.'main/startframe.php'.URL_APPEND;

$thisfile='seg_report_generator.php';

//if ($send_details) include($root_path.'include/inc_retail_display_rdetails.php');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('report_how2generate.php','Report Generator')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title");

 # Assign Body Onload javascript code
 #$onLoadJS='onLoad="preset();"';
 $onLoadJS='onLoad="';

 #added by VAN 09-19-08
 $fromtime = $_POST['fromHour'].":".$_POST['fromMin'].":00"." ".$_POST['fromMeridian'];
 $fromtime = date("H:i:s",strtotime($fromtime));
 $totime = $_POST['toHour'].":".$_POST['toMin'].":00"." ".$_POST['toMeridian'];
 $totime = date("H:i:s",strtotime($totime));
 #-----------------

	# POST
	if ($_POST['report_nr']) {
		$result=$db->Execute("SELECT rep_nr,rep_name,rep_script,rep_dept_nr FROM seg_reptbl WHERE rep_nr=".$_POST['report_nr']);
		$row=$result->FetchRow();

				if(($_POST['report_nr']==40) && ($_POST['exp_type']=='PDF'))
						$onLoadJS.="window.open('pdf_".$row['rep_script'].".php?fromdate=".$_POST['from_date']."&todate=".$_POST['to_date']."&codetype=".$_POST['code_type']."&icd=".$_POST['icd_code']."&icp=".$_POST['icp_code']."&ptype=".$_POST['ptype']."',null,'height=600,width=800,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');";
				else if(($_POST['report_nr']==40) && ($_POST['exp_type']=='EXCEL'))
						$onLoadJS.="window.open('excel_".$row['rep_script'].".php?fromdate=".$_POST['from_date']."&todate=".$_POST['to_date']."&codetype=".$_POST['code_type']."&icd=".$_POST['icd_code']."&icp=".$_POST['icp_code']."&ptype=".$_POST['ptype']."')";
				else if((($_POST['report_nr']==33)||($_POST['report_nr']==34)||($_POST['report_nr']==41)||($_POST['report_nr']==42)||($_POST['report_nr']==19)||($_POST['report_nr']==15)||($_POST['report_nr']==17)||($_POST['report_nr']==20)||($_POST['report_nr']==18)||($_POST['report_nr']==16)) && ($_POST['exp_type']=='EXCEL')){
					$onLoadJS.="window.open('excel_".$row['rep_script'].$format_name.".php?from=".$_POST['from_date']."&to=".$_POST['to_date']."&fromtime=".$fromtime."&totime=".$totime."&dept_nr=".$_POST['dept_nr']."&dept_nr_sub=".$_POST['dept_nr_sub']."&location=".$_POST['location']."&icd=".$_POST['icd']."&modkey=".$_POST['modkey']."&modkey2=".$_POST['modkey2']."&modkey3=".$_POST['modkey3']."&sclass=".$_POST['sclass']."&orderby=".$_POST['orderby']."')";
				}
						//$onLoadJS.="window.open('excel_".$row['rep_script'].".php?from=".$_POST['from_date']."&to=".$_POST['to_date']."&icd=".$_POST['icd']."')";
				else if((($_POST['report_nr']==12)||($_POST['report_nr']==22)||($_POST['report_nr']==23)||($_POST['report_nr']==25)||($_POST['report_nr']==26)||($_POST['report_nr']==45)||($_POST['report_nr']==24)||($_POST['report_nr']==57)||($_POST['report_nr']==58)||($_POST['report_nr']==59)||($_POST['report_nr']==61)||($_POST['report_nr']==62)||($_POST['report_nr']==31)||($_POST['report_nr']==32)||($_POST['report_nr']==11) ||($_POST['report_nr']==2) ||($_POST['report_nr']==28)||($_POST['report_nr']==29)) && ($_POST['exp_type']=='EXCEL'))
						$onLoadJS.="window.open('excel_".$row['rep_script'].".php?from=".$_POST['from_date']."&to=".$_POST['to_date']."&dept_nr=".$_POST['dept_nr']."&dept_nr_sub=".$_POST['dept_nr_sub']."&sclass=".$_POST['sclass']."')";
				else if((($_POST['report_nr']==27)||($_POST['report_nr']==30)) && ($_POST['exp_type']=='EXCEL'))
						$onLoadJS.="window.open('excel_".$row['rep_script'].".php?from=".$_POST['from_date']."&to=".$_POST['to_date']."&location=".$_POST['location']."')";
				else if($_POST['report_nr']==54){
						$onLoadJS.="window.open('pdf_".$row['rep_script'].".php?fromdate=".$_POST['from_date']."&todate=".$_POST['to_date']."&encoder=".$_POST['medocs_encoder']."&dept_nr=".$_POST['dept_nr']."&ptype=".$_POST['ptype']."',null,'height=600,width=800,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');";
				}else if(($_POST['report_nr']==3) && ($_POST['exp_type']=='EXCEL')){
						$onLoadJS.="window.open('excel_".$row['rep_script'].".php?from=".$_POST['from_date']."&to=".$_POST['to_date']."&location=".$_POST['location']."&modkey=".$_POST['modkey']."&modkey2=".$_POST['modkey2']."&modkey3=".$_POST['modkey3']."')";
				}else if((($_POST['report_nr']==1)||($_POST['report_nr']==7)||($_POST['report_nr']==43)||($_POST['report_nr']==63)) && ($_POST['exp_type']=='EXCEL')){
          $onLoadJS.="window.open('excel_".$row['rep_script'].$format_name.".php?from=".$_POST['from_date']."&to=".$_POST['to_date']."&fromtime=".$fromtime."&totime=".$totime."&dept_nr=".$_POST['dept_nr']."&dept_nr_sub=".$_POST['dept_nr_sub']."&location=".$_POST['location']."&icd=".$_POST['icd']."&modkey=".$_POST['modkey']."&modkey2=".$_POST['modkey2']."&modkey3=".$_POST['modkey3']."&sclass=".$_POST['sclass']."&orderby=".$_POST['orderby']."');";  
        }
				else{
						$format_name = "";
						#if (($_POST['notifiable_format']=='all')&&(($_POST['report_nr']==42)||($_POST['report_nr']==33)))
						#if (($_POST['notifiable_format']=='all')&&($_POST['report_nr']==42))
						#	$format_name = "_all";
						#$onLoadJS.="window.open('pdf_".$row['rep_script'].".php?from=".$_POST['fromdt']."&to=".$_POST['todt']."&fromtime=".$fromtime."&totime=".$totime."&dept_nr=".$_POST['dept_nr']."&location=".$_POST['location']."&icd=".$_POST['icd']."&modkey=".$_POST['modkey']."&modkey2=".$_POST['modkey2']."&orderby=".$_POST['orderby']."',null,'height=600,width=800,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');";
						$onLoadJS.="window.open('pdf_".$row['rep_script'].$format_name.".php?from=".$_POST['from_date']."&to=".$_POST['to_date']."&fromtime=".$fromtime."&totime=".$totime."&dept_nr=".$_POST['dept_nr']."&dept_nr_sub=".$_POST['dept_nr_sub']."&location=".$_POST['location']."&icd=".$_POST['icd']."&modkey=".$_POST['modkey']."&modkey2=".$_POST['modkey2']."&modkey3=".$_POST['modkey3']."&sclass=".$_POST['sclass']."&orderby=".$_POST['orderby']."&modkey4=".$_POST['insurance_name']."&is_senior=".$_POST['is_senior']."',null,'height=600,width=800,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');";
				}
	}
 $onLoadJS.='"';
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

ob_start();
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

#icd_autocomplete, #icp_autocomplete {
	padding-bottom:1.75em;
	width: 300px;

}
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
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/yahoo/yahoo.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/yui-2.7/fonts/fonts-min.css"/>
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/yui-2.7/autocomplete/assets/skins/sam/autocomplete.css"/>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/connection/connection-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/animation/animation-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/autocomplete/autocomplete-min.js"></script>
<script type="text/javascript">
function prufformreport(d){
	if (d.report_nr.value==0) {
		alert("Select the kind of report you want to generate.");
		d.report_nr.focus();
		return false;
 }else if (d.from_date.value=='') {
		alert("Enter the starting date.");
		d.from_date.focus();
		return false;
 }else if (d.to_date.value=='') {
		alert("Enter the end date.");
		d.to_date.focus();
		return false;
 }else if (d.from_date.value > d.to_date.value){
		alert("Starting date should be earlier than the ending date");
		d.from_date.focus();
		return false;

 }/*else{
	startRetrieving();
	return true;
 }	      */
}

/*
*   Added startRetrieving and doneRetrieving progress indicator while report's data is being retrieved ...
*   @added:      VAS
*   @added date:   04-12-2009
*/
/*
var isRetrieving=0;
function startRetrieving() {
		doneRetrieving();
		if (!isRetrieving) {
				isRetrieving = 1;

				return overlib('Retrieving records ...<br><img src="<?=$root_path?>images/ajax_bar.gif">',
						WIDTH,300, TEXTPADDING,5, BORDER,0,
						STICKY, SCROLL, CLOSECLICK, MODAL,
						NOCLOSE, CAPTION,'Retrieving',
						MIDX,0, MIDY,0,
						STATUS,'Retrieving');
		}
}

function doneRetrieving() {
		if (isRetrieving) {
				//setTimeout('cClick()', 500);
				cClick();
				isRetrieving = 0;
		}
}
*/
</script>

<?php

	 # Load the javascript code
	#require($root_path.'include/inc_js_retail2.php');
	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\r\n";
	$sTemp = ob_get_contents();
ob_end_clean();
$sTemp.="
<script type='text/javascript'>
	var init=false;
	//var refno='$refno';
</script>";

$smarty->append('JavaScript',$sTemp);

# Assign prompt messages

if ($mode=="deldetails") {
	if ($deleteok) $smarty->assign('sDeleteOK',"Transaction detail deleted.");
	else $smarty->assign('sDeleteFailed',"Unable to delete transaction detail.");
}
else {
	if($saveok){
	//if ($senddetail) {
	//	$smarty->assign('sSaveFeedBack',"HAHAHAHAHA");
	//}
		if($update) $smarty->assign('sSaveFeedBack',"Update was successful.");
		else $smarty->assign('sSaveFeedBack',"Data was successfully saved.");
	}
}

 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="post" name="inputform" onSubmit="return prufformreport(this)">');
 $smarty->assign('sFormEnd','</form>');

 # Assign form inputs (or values)

 //if ($saveok||$update) $smarty->assign('sOrderNrInput',$bestellnum.'</b><input type="hidden" name="bestellnum" value="'.$bestellnum.'">');
 //	else $smarty->assign('sOrderNrInput','<input type="text" name="bestellnum" value="'.$bestellnum.'" size=20 maxlength=20>');

	#added by VAN 09-14-2010
	#$source = $_GET['from'];
	#if ($source=='medocs'){
		$dept_nr = '151';
		$smarty->assign('sShowCategory',true);

		$sql = "SELECT * FROM seg_reptbl_category ORDER BY NAME ASC";

		$result=$db->Execute($sql);
		$options="<option value='0'>-All Category-</option>";
		if (is_object($result)){
			while ($row=$result->FetchRow()) {
				$options.='<option value="'.$row['code'].'">'.$row['name'].'</option>';
			}
		}
		$smarty->assign('sReportCategory',
												"<select name=\"category_code\" id=\"category_code\" onChange=\"DisplayAllReport(this.value);\">
												$options
												</select>");

		$sql = 'SELECT rep_nr,rep_name,rep_script,rep_dept_nr FROM seg_reptbl WHERE rep_dept_nr IN ('.$dept_nr.') ORDER BY rep_name';

	/*}else{
		$smarty->assign('sShowCategory',false);
		$sql = 'SELECT rep_nr,rep_name,rep_script,rep_dept_nr FROM seg_reptbl ORDER BY rep_name';
	}*/

	$sclass = '<select name="sclass" id="sclass">
								<option value="all">-All-</option>
								<option value="primary">Primary Diagnosis</option>
								<option value="secondary">Secondary Diagnosis</option></select>';

	$smarty->assign('sICDClassification',$sclass);

	$sclass = '<select name="notifiable_format" id="notifiable_format">
								<option value="all">Consolidated Format</option>
								<option value="byicd">Arrange by Diagnosis</option></select>';

	$smarty->assign('sNotifiableFormat',$sclass);
	#--------------------
	#echo $sql;
	$result=$db->Execute($sql);
	$options="<option value='0'>-Select Report-</option>";
	if (is_object($result)){
		while ($row=$result->FetchRow()) {
			$options.='<option value="'.$row['rep_nr'].'">'.$row['rep_name'].'</option>';
		}
	}
	$smarty->assign('sReportSelect',
"<select name=\"report_nr\" id=\"report_nr\" onChange=\"DisplayDept(this.value);\">
$options
</select>");

#added by cha 07-20-2009
	$smarty->assign('sICD10code', '<input type="radio" name="code_type" id="code_type" value="ICD10" onChange="displayCodes(this.value);">');
	$smarty->assign('sICPcode', '<input type="radio" name="code_type" id="code_type" value="ICP" onChange="displayCodes(this.value);">');
	$smarty->assign('sExportAsPdf','<input type="radio" name="exp_type" id="exp_type" value="PDF" checked>');
	$smarty->assign('sExportAsExcel','<input type="radio" name="exp_type" id="exp_type" value="EXCEL">');
	$smarty->assign('sSelectICPCode',"<select name=\"icp_code\" id=\"icp_code\"></select>");
	$smarty->assign('sSelectICDCode',"<select name=\"icd_code\" id=\"icd_code\"></select>");
	$smarty->assign('sPatientType','<select name="ptype" id="ptype">
		<option value="all">-All-</option>
		<option value="1">ER</option>
		<option value="2">OPD</option>
		<option value="3,4">IPD</option></select>');
#end cha


	#added by VAN 09-12-08
	$all_meds=&$dept_obj->getAllMedicalObject2();
	$options2="<option value='0'>-All Department-</option>";
	while ($row2=$all_meds->FetchRow()) {
		$options2.='<option value="'.$row2['nr'].'">'.$row2['name_formal'].'</option>';
	}
	$smarty->assign('sReportSelectDept',
"<select name=\"dept_nr\" id=\"dept_nr\">
$options2
</select>");

	$smarty->assign('sReportSelectLoc',
	'<select name = "location" id="location">
		<option value = "0">Within and Outside of Region XI</option>
		<option value = "1">Davao del Sur</option>
		<option value = "2">All from Region XI excluding Davao del Sur</option>
		<option value = "3">Outside Region XI</option>
		');
	#-----------------------

	#added by VAN 11-17-09
	$smarty->assign('sReportSelectDeptSub',
	'<select name = "dept_nr_sub" id="dept_nr_sub">
		<option value = "0">-All Department-</option>
		<option value = "1">Gynecology</option>
		<option value = "2">Medicines</option>
		<option value = "3">Obstetrics</option>
		<option value = "4">Pediatrics</option>
		<option value = "5">Surgery</option>
		<option value = "6">ENT</option>
		');
	#--------------

		#added by Cherry 11-25-09
		$smarty->assign('sReportSelectAge',
		'<select name = "age_distribution" id="age_distribution">
				<option value = "0">-    All Ages   -</option>
				<option value = "1">Below Six Years Old</option>
				<option value = "2">Six Years Old and Above</option>
		');


		#-----------------------

		$smarty->assign('sReportSelectKey',
	'<select name = "modkey" id="modkey">
		<option value = "0">All</option>
		<option value = "1">Not Yet Received</option>
		<option value = "2">Received</option>
		');

		$smarty->assign('sReportSelectKey2',
	'<select name = "modkey2" id="modkey2">
		<option value = "0">All</option>
		<option value = "1">Died</option>
		<option value = "2">Still Alive</option>
		');

		$smarty->assign('sReportSelectKey3',
	'<select name = "modkey3" id="modkey3">
		<option value = "0">All</option>
		<option value = "1">PHIC</option>
		<option value = "2">NPHIC</option>
		');



 #Added by Cherry 05-09-09
		/*$result2 = $db->Execute("SELECT DISTINCT IF(instr(d.diagnosis_code,'.'),
														substr(d.diagnosis_code,1,IF(instr(d.diagnosis_code,'.'),instr(d.diagnosis_code,'.')-1,0)),
														d.diagnosis_code) AS code_parent,
															(SELECT description FROM care_icd10_en AS i WHERE i.diagnosis_code=(IF(instr(d.diagnosis_code,'.'),
															substr(d.diagnosis_code,1,IF(instr(d.diagnosis_code,'.'),instr(d.diagnosis_code,'.')-1,0)),
															d.diagnosis_code) )) AS description
														FROM care_icd10_en as d
														WHERE IF(instr(d.diagnosis_code,'.'),
														substr(d.diagnosis_code,1,IF(instr(d.diagnosis_code,'.'),instr(d.diagnosis_code,'.')-1,0)),
														d.diagnosis_code) <> ''
														ORDER BY d.diagnosis_code;");*/
                                                        
        #edited by VAN 10-05-2012
        $sql = "SELECT DISTINCT
                    IF(INSTR(d.diagnosis_code,'.'),
                    SUBSTR(d.diagnosis_code,1,IF(INSTR(d.diagnosis_code,'.'),INSTR(d.diagnosis_code,'.')-1,0)),
                    d.diagnosis_code) AS code_parent,
                    (SELECT description FROM care_icd10_en AS i WHERE i.diagnosis_code=(IF(INSTR(d.diagnosis_code,'.'),
                    SUBSTR(d.diagnosis_code,1,IF(INSTR(d.diagnosis_code,'.'),INSTR(d.diagnosis_code,'.')-1,0)),
                    d.diagnosis_code) )) AS description
                    FROM care_icd10_en AS d
                    WHERE IF(INSTR(d.diagnosis_code,'.'),
                    SUBSTR(d.diagnosis_code,1,IF(INSTR(d.diagnosis_code,'.'),INSTR(d.diagnosis_code,'.')-1,0)),
                    d.diagnosis_code) REGEXP '^[[:alpha:]][[:digit:]]'
                    ORDER BY d.diagnosis_code";
                    
        $result2 = $db->Execute($sql);                                                                                                                    
		$options3="<option value='all'>-All-</option>";
		while ($row2=$result2->FetchRow()) {
		$options3.='<option value="'.$row2['code_parent'].'">'.$row2['code_parent'].'</option>';
	}
	$smarty->assign('sReportSelectCode',
"<select name=\"icd\" id=\"icd\">
$options3
</select>");
	#-----------------------

	//added by maimai 01-20-2015
	$insurances = $insurance_obj->getAllActiveFirmsInfo();
	$option_insurance .= "<option value='all'>-All-</option>";
	$option_insurance .= "<option value='non-phic'>Non-PHIC</option>";
	if($insurances){
		while($row = $insurances->FetchRow()){
			$option_insurance .= "<option value='".$row['hcare_id']."'>".$row['name']."</option>";
		}
	}

	$smarty->assign('sInsurances', '<select id="insurance_name" name="insurance_name">'.$option_insurance.'</select>');
	//end maimai

	$smarty->assign('sSenior', '<select id="is_senior" name="is_senior">
									<option value="-1">All</option>
									<option value="1">Senior</option>
									<option value="0">Non-Senior</option>
								</select>');

	#---------------Added by Cherry 09-10-10 ----------------#
		$medocs = 151;
		$rs_medocs_personell = $pers_obj->getStaffOfDept($medocs);
		$options_medocs = "";
	while ($row=$rs_medocs_personell->FetchRow()) {
		$staff_name = mb_strtoupper($row["name_last"]).", ".mb_strtoupper($row["name_first"])." ".mb_strtoupper($row["name_middle"]);
		$options_medocs.='<option value="'.$row['personell_nr'].'">'.$staff_name.'</option>';
	}
	$smarty->assign('sReportEncoder',
								"<select name=\"medocs_encoder\" id=\"medocs_encoder\">
										 <option value=\"all\">--All--</option>
										 $options_medocs
								 </select>");

	#----------------------end Cherry------------------------#

	#added by VAN 09-19-08
	$selectfromHour .= '<select id="fromHour" name="fromHour">';

	for($i = 0; $i <= 12; $i++){
	if ($i<10)
		$i = '0'.$i;

	if ($i==7)
		$selectfromHour .= "\t<option value='$i' 'selected'>$i</option>\n";
	else
		$selectfromHour .= "\t<option value='$i'>$i</option>\n";
 }
 $selectfromHour .= '</select>';

 #minutes
 $selectfromMin .= '<select id="fromMin" name="fromMin">';

 for($i = 0; $i < 60; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selectfromMin .= "\t<option value='$i'>$i</option>\n";
 }
 $selectfromMin .= '</select>';

 #meridian
 $selectfromMeridian .= '<select id="fromMeridian" name="fromMeridian">';
 $meridime = array("AM", "PM");
 foreach ($meridime as $i) {
	$selectfromMeridian .= "\t<option value='$i'>$i</option>\n";
 }
 $selectfromMeridian .= '</select>';


 # SHIFT : TO
 #hours
 $selecttoHour .= '<select id="toHour" name="toHour">';

 if (!($i))
	 $i=5;

 for($i = 0; $i <= 12; $i++){
	if ($i<10)
		$i = '0'.$i;

	if ($i==5){
		$selecttoHour .= "\t<option value='$i' 'selected'>$i</option>\n";
	}else{
		$selecttoHour .= "\t<option value='$i'>$i</option>\n";
	}
 }
 $selecttoHour .= '</select>';

 #minutes
 $selecttoMin .= '<select id="toMin" name="toMin">';

 for($i = 0; $i < 60; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selecttoMin .= "\t<option value='$i'>$i</option>\n";
 }
 $selecttoMin .= '</select>';

 #meridian
 $selecttoMeridian .= '<select id="toMeridian" name="toMeridian">';
 $meridime = array("AM", "PM");
 foreach ($meridime as $i) {
	if ($i=='PM')
		$selecttoMeridian .= "\t<option value='$i' 'selected'>$i</option>\n";
	else
		$selecttoMeridian .= "\t<option value='$i'>$i</option>\n";
 }
 $selectfromMeridian .= '</select>';

 $sTempShift = '';
 $sTempShift = '<b>FROM <b> &nbsp;&nbsp;
				'.$selectfromHour.'<b>:</b>'.$selectfromMin.'&nbsp;'.$selectfromMeridian.'
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>TO <b> &nbsp;&nbsp;
				'.$selecttoHour.'<b>:</b>'.$selecttoMin.'&nbsp;'.$selecttoMeridian;

 $smarty->assign('sShift',$sTempShift);
	#----------------------

	#$smarty->assign('sFromDateInput','<div id="show_from_date" style="margin-right:2px;padding:0px 2px;border:1px solid #888888;background-color:white;width:120px;min-height:18px;display:block;float:left"></div>');
	#$smarty->assign('sFromDateHidden','<input type="hidden" name="fromdt" id="from_date" value="">');
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
	showsTime : false,
	button : \"to_date_trigger\",
	singleClick : true,
	step : 1
}
);
</script>
";
	$smarty->assign('jsCalendarSetup', $jsCalScript);

require($root_path.'modules/repgen/ajax/repgen_common_ajx.php');
$xajax->printJavascript($root_path.'classes/xajax_0.5');
# Collect hidden inputs

ob_start();
$sTemp='';
 ?>
	<script type="text/javascript">
		function DisplayDept(rep_nr){
			//if (rep_nr==1){
			//OPD and ER
			//edited by Cherry 04-08-09
				document.getElementById('orderby').style.display='none';
				document.getElementById('is_senior').style.display='none';
			//if ((rep_nr==1)||(rep_nr==7)||(rep_nr==3)||(rep_nr==10)){
			if ((rep_nr==1)||(rep_nr==7)||(rep_nr==10)||(rep_nr==43)){
								document.getElementById('dept_row').style.display='';
								document.getElementById('dept_row_sub').style.display='none';
								document.getElementById('age_row').style.display='none';
								document.getElementById('mode_row').style.display='none';
								document.getElementById('died_row').style.display='none';
								document.getElementById('loc_row').style.display='none'; //added by Cherry 04-15-09
								document.getElementById('shiftrow').style.display='';
								document.getElementById('code').style.display='none';
								document.getElementById('orderby').style.display='';
								document.getElementById('medocs_encoder').style.display='none';

								//added by cha 07-20-09
								document.getElementById('codetype').style.display='none';
								document.getElementById('patient_type').style.display='none';
								//document.getElementById('export_type').style.display='none';
               					document.getElementById('export_type').style.display='';
								document.getElementById('icd_type').style.display='none';
								document.getElementById('icp_type').style.display='none';
								//end cha
								document.getElementById('insurance_type').style.display='none';

								if(rep_nr==1){
									document.getElementById('icd_class').style.display='none';
									document.getElementById('shiftrow').style.display='none';
									document.getElementById('orderby').style.display='none';
									document.getElementById('insurance_type').style.display='';
								}

								$('notifiable_format').style.display = 'none';

			//}else if((rep_nr==12)||(rep_nr==22) || (rep_nr==8)){
			}else if((rep_nr==6)||(rep_nr==3) || (rep_nr==8) || (rep_nr==44) || (rep_nr==65) || (rep_nr==66)){
								document.getElementById('dept_row').style.display='';
								document.getElementById('dept_row_sub').style.display='none';
								document.getElementById('age_row').style.display='none';
								document.getElementById('loc_row').style.display='none'; //added by Cherry 04-15-09
								document.getElementById('shiftrow').style.display='none';
								document.getElementById('code').style.display='none';
								document.getElementById('medocs_encoder').style.display='none';
								if (rep_nr==3){
										document.getElementById('mode_row').style.display='';
										document.getElementById('died_row').style.display='';
										document.getElementById('phic_row').style.display='';
										document.getElementById('export_type').style.display='';
								}else if((rep_nr==8) || (rep_nr==66)){
									document.getElementById('icd_class').style.display='none';
									document.getElementById('insurance_type').style.display='';
									document.getElementById('is_senior').style.display='';
								}else if(rep_nr==65){
									document.getElementById('icd_class').style.display='none';
									document.getElementById('insurance_type').style.display='none';
									document.getElementById('dept_row').style.display='none';
								}else{
										document.getElementById('mode_row').style.display='none';
										document.getElementById('died_row').style.display='none';
										document.getElementById('phic_row').style.display='none';
										document.getElementById('export_type').style.display='none';
										document.getElementById('insurance_type').style.display='none';
								}
								//added by cha 07-20-09
								document.getElementById('codetype').style.display='none';
								document.getElementById('patient_type').style.display='none';
								//document.getElementById('export_type').style.display='none';
								document.getElementById('icd_type').style.display='none';
								document.getElementById('icp_type').style.display='none';
								//end cha

								$('notifiable_format').style.display = 'none';

						}else if((rep_nr==30)||(rep_nr==27)){
								document.getElementById('dept_row').style.display='none';
								document.getElementById('dept_row_sub').style.display='none';
								document.getElementById('age_row').style.display='none';
								document.getElementById('mode_row').style.display='none';
								document.getElementById('died_row').style.display='none';
								document.getElementById('loc_row').style.display='';
								document.getElementById('shiftrow').style.display='none';
								document.getElementById('code').style.display='none';
								document.getElementById('medocs_encoder').style.display='none';
								//added by cha 07-20-09
								document.getElementById('codetype').style.display='none';
								document.getElementById('patient_type').style.display='none';
								document.getElementById('export_type').style.display='';
								document.getElementById('icd_type').style.display='none';
								document.getElementById('icp_type').style.display='none';
								document.getElementById('insurance_type').style.display='none';
								//end cha

								$('notifiable_format').style.display = 'none';

						}else if((rep_nr==33)||(rep_nr==34)){
								document.getElementById('dept_row').style.display='none';
								document.getElementById('dept_row_sub').style.display='none';
								document.getElementById('age_row').style.display='none';
								document.getElementById('loc_row').style.display='none';
								document.getElementById('mode_row').style.display='none';
								document.getElementById('died_row').style.display='none';
								document.getElementById('shiftrow').style.display='none';
								document.getElementById('code').style.display='';
								document.getElementById('medocs_encoder').style.display='none';
								//added by cha 07-20-09
								document.getElementById('codetype').style.display='none';
								document.getElementById('patient_type').style.display='none';
								document.getElementById('export_type').style.display='';
								document.getElementById('icd_type').style.display='none';
								document.getElementById('icp_type').style.display='none';
								document.getElementById('insurance_type').style.display='none';
								//end cha

								//if (rep_nr==33)
									//$('notifiable_format').style.display = '';
								//else
									$('notifiable_format').style.display = 'none';

						}else if(rep_nr==40){   //added by cha 07-20-09
								document.getElementById('dept_row').style.display='none';
								document.getElementById('dept_row_sub').style.display='none';
								document.getElementById('age_row').style.display='none';
								document.getElementById('shiftrow').style.display='none';
								document.getElementById('code').style.display='none';
								document.getElementById('loc_row').style.display='none';
								document.getElementById('mode_row').style.display='none';
								document.getElementById('died_row').style.display='none';
								document.getElementById('medocs_encoder').style.display='none';

								document.getElementById('codetype').style.display='';
								document.getElementById('patient_type').style.display='';
								document.getElementById('export_type').style.display='';
								document.getElementById('icd_type').style.display='none';
								document.getElementById('icp_type').style.display='none';
								document.getElementById('insurance_type').style.display='none';
								//end cha

								$('notifiable_format').style.display = 'none';

						}else if((rep_nr==12)||(rep_nr==22)||(rep_nr==23)||(rep_nr==24)||(rep_nr==31)||(rep_nr==32)){
								document.getElementById('dept_row').style.display='none';
								document.getElementById('dept_row_sub').style.display='';
								document.getElementById('age_row').style.display='none';
								document.getElementById('shiftrow').style.display='none';
								document.getElementById('code').style.display='none';
								document.getElementById('loc_row').style.display='none';
								document.getElementById('mode_row').style.display='none';
								document.getElementById('died_row').style.display='none';
								document.getElementById('medocs_encoder').style.display='none';

								document.getElementById('codetype').style.display='none';
								document.getElementById('patient_type').style.display='none';
								document.getElementById('export_type').style.display='';
								document.getElementById('icd_type').style.display='none';
								document.getElementById('icp_type').style.display='none';
								document.getElementById('insurance_type').style.display='none';

								$('notifiable_format').style.display = 'none';

						}else if((rep_nr==19)||(rep_nr==15)||(rep_nr==17)||(rep_nr==20)||(rep_nr==18)||(rep_nr==16)||(rep_nr==11)||(rep_nr==2)||(rep_nr==29)||(rep_nr==41)||(rep_nr==42)){
								document.getElementById('dept_row').style.display='none';
								document.getElementById('dept_row_sub').style.display='none';
								document.getElementById('age_row').style.display='none';
								document.getElementById('mode_row').style.display='none';
								document.getElementById('died_row').style.display='none';
								document.getElementById('loc_row').style.display='none';    //added by Cherry 04-15-09
								document.getElementById('shiftrow').style.display='none';
								document.getElementById('code').style.display='none';
								document.getElementById('medocs_encoder').style.display='none';
								 //added by cha 07-20-09
								document.getElementById('codetype').style.display='none';
								document.getElementById('patient_type').style.display='none';
								document.getElementById('export_type').style.display='';
								document.getElementById('icd_type').style.display='none';
								document.getElementById('icp_type').style.display='none';
								document.getElementById('insurance_type').style.display='none';
								//end cha

								//if (rep_nr==42)
									//$('notifiable_format').style.display = '';
								//else
									$('notifiable_format').style.display = 'none';

						}else if(rep_nr==45){
								document.getElementById('dept_row').style.display='none';
								document.getElementById('dept_row_sub').style.display='';
								document.getElementById('age_row').style.display='';
								document.getElementById('mode_row').style.display='none';
								document.getElementById('died_row').style.display='none';
								document.getElementById('loc_row').style.display='none';    //added by Cherry 04-15-09
								document.getElementById('shiftrow').style.display='none';
								document.getElementById('code').style.display='none';
								document.getElementById('medocs_encoder').style.display='none';
								 //added by cha 07-20-09
								document.getElementById('codetype').style.display='none';
								document.getElementById('patient_type').style.display='none';
								document.getElementById('export_type').style.display='';
								document.getElementById('icd_type').style.display='none';
								document.getElementById('icp_type').style.display='none';
								document.getElementById('insurance_type').style.display='none';

								$('notifiable_format').style.display = 'none';

						}else if(rep_nr==54){
								document.getElementById('dept_row').style.display='';
								document.getElementById('dept_row_sub').style.display='none';
								document.getElementById('age_row').style.display='none';
								document.getElementById('mode_row').style.display='none';
								document.getElementById('died_row').style.display='none';
								document.getElementById('loc_row').style.display='none'; //added by Cherry 04-15-09
								document.getElementById('shiftrow').style.display='none';
								document.getElementById('code').style.display='none';
								document.getElementById('orderby').style.display='none';
								document.getElementById('medocs_encoder').style.display='';

								//added by cha 07-20-09
								document.getElementById('codetype').style.display='none';
								document.getElementById('patient_type').style.display='';
								document.getElementById('export_type').style.display='none';
								document.getElementById('icd_type').style.display='none';
								document.getElementById('icp_type').style.display='none';
								document.getElementById('insurance_type').style.display='none';
								$('notifiable_format').style.display = 'none';
						}else if((rep_nr==25)||(rep_nr==26)||(rep_nr==28)||(rep_nr==57)||(rep_nr==58)||(rep_nr==59)||(rep_nr==61)||(rep_nr==62)){ //Added by Cherry 11-19-10
								document.getElementById('dept_row').style.display='none';
								document.getElementById('dept_row_sub').style.display='none';
								document.getElementById('age_row').style.display='none';
								document.getElementById('mode_row').style.display='none';
								document.getElementById('died_row').style.display='none';
								document.getElementById('loc_row').style.display='none';
								document.getElementById('shiftrow').style.display='none';
								document.getElementById('code').style.display='none';
								document.getElementById('medocs_encoder').style.display='none';

								document.getElementById('codetype').style.display='none';
								document.getElementById('patient_type').style.display='none';
								document.getElementById('export_type').style.display='';
								document.getElementById('icd_type').style.display='none';
								document.getElementById('icp_type').style.display='none';
								document.getElementById('insurance_type').style.display='none';
								$('notifiable_format').style.display = 'none';
                        }else if (rep_nr==63){
                                document.getElementById('dept_row').style.display='none';
                                document.getElementById('dept_row_sub').style.display='none';
                                document.getElementById('age_row').style.display='none';
                                document.getElementById('mode_row').style.display='none';
                                document.getElementById('died_row').style.display='none';
                                document.getElementById('loc_row').style.display='none';
                                document.getElementById('shiftrow').style.display='none';
                                document.getElementById('code').style.display='none';
                                document.getElementById('medocs_encoder').style.display='none';
                                document.getElementById('icd_class').style.display='none';
                               
                                document.getElementById('codetype').style.display='none';
                                document.getElementById('patient_type').style.display='none';
                                
                                document.getElementById('export_type').style.display='';
                                document.getElementById('icd_type').style.display='none';
                                document.getElementById('icp_type').style.display='none';
                                document.getElementById('insurance_type').style.display='none';
                                $('notifiable_format').style.display = 'none';

						}else{
								document.getElementById('dept_row').style.display='none';
								document.getElementById('dept_row_sub').style.display='none';
								document.getElementById('age_row').style.display='none';
								document.getElementById('mode_row').style.display='none';
								document.getElementById('died_row').style.display='none';
								document.getElementById('loc_row').style.display='none';	//added by Cherry 04-15-09
								document.getElementById('shiftrow').style.display='none';
								document.getElementById('code').style.display='none';
								document.getElementById('medocs_encoder').style.display='none';
								 //added by cha 07-20-09
								document.getElementById('codetype').style.display='none';
								document.getElementById('patient_type').style.display='none';
								document.getElementById('export_type').style.display='none';
								document.getElementById('icd_type').style.display='none';
								document.getElementById('icp_type').style.display='none';
								document.getElementById('insurance_type').style.display='none';
								//end cha

								$('notifiable_format').style.display = 'none';

						}
		}

		 //added by cha 07-20-09
		function displayCodes(code_type)
		{
				if(code_type=="ICD10")
				{
						//xajax_setCodes(code_type);
						document.getElementById('icd_type').style.display='';
						document.getElementById('icp_type').style.display='none';
				}
				else if(code_type=="ICP")
				{
						//xajax_setCodes(code_type);
						document.getElementById('icp_type').style.display='';
						document.getElementById('icd_type').style.display='none';
				}
		}
		//end cha

		//added by cha 07-22-09
		function mouseOver(tagId, id){
				//alert(objID);
				var elTarget = $(tagId);
				if(elTarget){

						idname = "code"+id;
						desc = $(idname).value;
						if(!desc) desc="No description";

						return overlib( desc, CAPTION,"Code Description",
													 TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, 'oltxt', CAPTIONFONTCLASS, 'olcap',
													WIDTH, 550,FGCLASS,'olfgjustify',FGCOLOR, '#bbddff',FIXX, 20,FIXY, 20);

				}
		}

		function DisplayAllReport(category_code){
			var dept_nr = '<?=$dept_nr?>';

			xajax_getListReport(category_code, dept_nr);
		}

		//end cha
	</script>

	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">
	<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">

	<input type="hidden" name="editpencnum"   id="editpencnum"   value="">
	<input type="hidden" name="editpentrynum" id="editpentrynum" value="">
	<input type="hidden" name="editpname" id="editpname" value="">
	<input type="hidden" name="editpqty"  id="editpqty"  value="">
	<input type="hidden" name="editppk"   id="editppk"   value="">
	<input type="hidden" name="editppack" id="editppack" value="">
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Append more hidden inputs acc. to mode

if($update){
	if($mode!="save"){
		$sTemp = $sTemp.'
		<input type="hidden" name="ref_bnum" value="'.$bestellnum.'">
		<input type="hidden" name="ref_artnum" value="'.$artnum.'">
	 <input type="hidden" name="ref_indusnum" value="'.$indusnum.'">
	 <input type="hidden" name="ref_artname" value="'.$artname.'">
	 ';
	}else{
		$sTemp = $sTemp.'
	 <input type="hidden" name="ref_bnum" value="'.$ref_bnum.'">
	 <input type="hidden" name="ref_artnum" value="'.$ref_artnum.'">
	 <input type="hidden" name="ref_indusnum" value="'.$ref_indusnum.'">
	 <input type="hidden" name="ref_artname" value="'.$ref_artname.'">
		';
	}
}

	$smarty->assign('sOrderBy','<input type="checkbox" name="orderby" id="orderby" value="1" />');
	$smarty->assign('sHiddenInputs',$sTemp);
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
	$smarty->assign('sContinueButton','<input type="image" '.createLDImgSrc($root_path,'continue.gif','0','left').' align="absmiddle">');

	# Assign the form template to mainframe
	$smarty->assign('sMainBlockIncludeFile','repgen/form.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>

<script>
function setMuniCity(mun_nr, mun_name) {
		document.getElementById('icd_code_nr').value   = mun_nr;
		document.getElementById('icd_code').value = mun_name;
}

function clearNr(id) {
	if (document.getElementById(id).value == '') {
		switch (id) {
			case "icd_code":
				document.getElementById('icd_code_nr').value = '';
			break;

			case "icp_code":
				document.getElementById('icp_code_nr').value = '';
			break;
		}
	}
}

YAHOO.example.BasicRemote = function() {
		// Use an XHRDataSource -- for barangay
		var brgyDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/repgen/ajax/seg_icd_query.php");
		// Set the responseType
		brgyDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		brgyDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		brgyDS.maxCacheEntries = 5;

		// Instantiate the AutoComplete
		var brgyAC = new YAHOO.widget.AutoComplete("icd_code", "icd_container", brgyDS);
		brgyAC.formatResult = function(oResultData, sQuery, sResultMatch) {
				return "<span style=\"display:none;\">"+oResultData[0]+"</span><span style=\"float:left;width:50%\">"+oResultData[1]+"</span><span>"+oResultData[2]+"</span>";
		};
		brgyAC.generateRequest = function(sQuery) {
				return "?query="+sQuery;
		};

		var munName = YAHOO.util.Dom.get("icp_code");
		var brgyName = YAHOO.util.Dom.get("icd_code");

		// Define an event handler to populate a hidden form field
		// when an item gets selected
		var brgyNr = YAHOO.util.Dom.get("icd_code_nr");
		var brgyHandler = function(sType, aArgs) {
				var bmyAC  = aArgs[0]; // reference back to the AC instance
				var belLI  = aArgs[1]; // reference to the selected LI element
				var boData = aArgs[2]; // object literal of selected item's result data

				// update text input control ...
				brgyNr.value = boData[0];
				brgyName.value = boData[1];
				xajax_getICD(brgyNr.value);
		};
		brgyAC.itemSelectEvent.subscribe(brgyHandler);

		 // Use an XHRDataSource --- for municipality or city
		var munDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/repgen/ajax/seg_icp_query.php");
		// Set the responseType
		munDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		munDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		munDS.maxCacheEntries = 5;

		// Instantiate the AutoComplete
		var munAC = new YAHOO.widget.AutoComplete("icp_code", "icp_container", munDS);
		munAC.formatResult = function(oResultData, sQuery, sResultMatch) {
				return "<span style=\"display:none;\">"+oResultData[0]+"</span><span style=\"float:left;width:50%\">"+oResultData[1]+"</span><span>"+oResultData[2]+"</span>";
		};

		// Define an event handler to populate a hidden form field
		// when an item gets selected
		var munNr = YAHOO.util.Dom.get("icp_code_nr");
		var munHandler = function(sType, aArgs) {
				var mmyAC  = aArgs[0]; // reference back to the AC instance
				var melLI  = aArgs[1]; // reference to the selected LI element
				var moData = aArgs[2]; // object literal of selected item's result data

				// update text input control ...
				munNr.value = moData[0];
				munName.value = moData[1];
				xajax_getICP(munNr.value);
				//brgyNr.value = '';
				//SbrgyName.value = '';
		};
		munAC.itemSelectEvent.subscribe(munHandler);

		return {
				brgyDS: brgyDS,
				brgyAC: brgyAC,
				munDS: munDS,
				munAC: munAC,
		};
}();
</script>
