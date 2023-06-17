<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/laboratory/ajax/lab-new.common.php"); # added by daryl
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

$title="$LDLab :: Reports Generator";
/* 2007-09-27 FDP
 replaced the orig line (which follows) for Close button target
$breakfile=$root_path."modules/pharmacy/seg-pharma-retail-functions.php".URL_APPEND."&userck=$userck";
 */
$breakfile = "labor.php";
$thisfile='seg-lab-reports.php';

//if ($send_details) include($root_path.'include/inc_retail_display_rdetails.php');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'include/inc_front_chain_lang.php');

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

 require_once($root_path.'include/care_api_classes/class_dateGenerator.php');
 $dategen = new DateGenerator;


 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('report_how2generate.php','Reports Generator')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title");

 # Assign Body Onload javascript code
 /*
 $onLoadJS='onLoad="';
	# POST
	if ($_POST['report_nr']) {
		$result=$db->Execute("SELECT rep_nr,rep_name,rep_script,rep_dept_nr FROM seg_reptbl WHERE rep_nr=".$_POST['report_nr']);
		$row=$result->FetchRow();
		$onLoadJS.="window.open('pdf_".$row['rep_script'].".php?from=".$_POST['fromdt']."&to=".$_POST['todt']."',null,'height=600,width=800,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');";
	}
 $onLoadJS.='"';
 */

 $onLoadJS='onLoad="preSet(); ShortcutKeys();"';
 #$onLoadJS.='';
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

 ob_start();
 # Load the javascript code
	#require($root_path.'include/inc_js_retail.php');
	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\r\n";

	echo '<script type="text/javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/checkdate.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";

	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\r\n";

	echo '<script type="text/javascript" src="'.$root_path.'js/NumberFormat154.js"></script>'."\r\n";

	$sTemp = ob_get_contents();
 ob_end_clean();

 $smarty->append('JavaScript',$sTemp);

 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="post" name="inputform" id="inputform" onSubmit="return prufform()">');
 $smarty->assign('sFormEnd','</form>');

 #echo "<br>report_group = ".$report_group;
 #echo "<br>report_class = ".$report_class;
 #echo "<br>from_date = ".$fromdt;
 #echo "<br>to_date = ".$todt;

 # select if view by reference number
 $smarty->assign('sViewGroup','<input type="checkbox" name="viewgrp" id="viewgrp" value="1">');
 #edited by Cherry 07-28-10
 #added by VAN 04-19-08
 $options = '';
 $options.='
				<option value="0">- Select Report Mode -</option>
				<option value="1">Status Report</option>
				<option value="2">Statistics Report</option>
				<option value="3">For Warding</option>
				<option value="4">Patient\'s List Report</option>
				<option value="5">Income Report</option>
				<option value="7">List of Hospital Services Charges</option>
				<option value="8">Summary Blood Bank Report</option>
				<option value="9">Laboratory Census Report</option>
				<option value="10">New Born Screening Report</option>
				';

 $smarty->assign('sReportSelectType',
							"<select name=\"report_type\" id=\"report_type\" onChange=\"selectMode(this.value);\">
								$options
							</select>");


 #added by VAN 06-03-08
 $options = '';
 $options.='
				<option value="0">-All Patient Type -</option>
				<option value="1">ER Patient</option>
				<option value="2">Admitted Patient</option>
				<option value="3">OutPatient</option>
				<option value="4">Walk-in</option>
				<option value="5">OPD & Walk-in</option>
				
				';

 $smarty->assign('sPatientSelect',
							"<select name=\"patient_type\" id=\"patient_type\" onChange=\"showShift(this.value);\">
								$options
							</select>");

 $smarty->assign('sPatientSelect2',
							"<select name=\"patient_type2\" id=\"patient_type2\">
								$options
							</select>");
 $smarty->assign('sPatientSelect3',
							"<select name=\"patient_type3\" id=\"patient_type3\">
								$options
							</select>");

#added by daryl
 $options = '';
 $options.='
				<option value="0">-All Quarter -</option>
				<option value="1">1st Quarter</option>
				<option value="2">2nd Quarter</option>
				<option value="3">3rd Quarter</option>
				<option value="4">4th Quarter</option>
				';
 $smarty->assign('sNewBornQuarter',
							"<select name=\"newborn_quarter\" id=\"newborn_quarter\">
								$options
							</select>");



	

$smarty->assign('sNewBornButton','<img name="viewnewborn" id="viewnewborn" onClick="viewNewBorn();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif','0','left') . ' border="0">');#added by daryl


$starting_year  = 2000;
$ending_year    = 2030;

for($starting_year; $starting_year <= $ending_year; $starting_year++) {
    $years[] = '<option value="'.$starting_year.'">'.$starting_year.'</option>';
}

$smarty->assign('sNewBornYear',
							"<select name=\"newborn_year\" id=\"newborn_year\">
								".implode("\n\r", $years)."
							</select>");


 # SHIFT : FROM
 #hours
 $selectfromHour .= '<select id="fromHour" name="fromHour">';

 for($i = 0; $i <= 12; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selectfromHour .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromHour .= '</select>';

 #minutes
 $selectfromMin .= '<select id="fromMin" name="fromMin">';

 for($i = 0; $i < 60; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selectfromMin .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromMin .= '</select>';

 #meridian
 $selectfromMeridian .= '<select id="fromMeridian" name="fromMeridian">';
 $meridime = array("AM", "PM");
 foreach ($meridime as $i) {
	$selectfromMeridian .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromMeridian .= '</select>';


 # SHIFT : TO
 #hours
 $selecttoHour .= '<select id="toHour" name="toHour">';

 for($i = 0; $i <= 12; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selecttoHour .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selecttoHour .= '</select>';

 #minutes
 $selecttoMin .= '<select id="toMin" name="toMin">';

 for($i = 0; $i < 60; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selecttoMin .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selecttoMin .= '</select>';

 #meridian
 $selecttoMeridian .= '<select id="toMeridian" name="toMeridian">';
 $meridime = array("AM", "PM");
 foreach ($meridime as $i) {
	$selecttoMeridian .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromMeridian .= '</select>';

 $sTempShift = '';
 $sTempShift = '<b>FROM <b> &nbsp;&nbsp;
				'.$selectfromHour.'<b>:</b>'.$selectfromMin.'&nbsp;'.$selectfromMeridian.'
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>TO <b> &nbsp;&nbsp;
				'.$selecttoHour.'<b>:</b>'.$selecttoMin.'&nbsp;'.$selecttoMeridian;

 $smarty->assign('sShift',$sTempShift);

 #-------------added by VAN 08-06-08
	# SHIFT : FROM
 #hours
 $selectfromHour3 .= '<select id="fromHour3" name="fromHour3">';

 for($i = 0; $i <= 12; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selectfromHour3 .= "\t<option value='$i' $selected>$i</option>\n";
	/*
	if ($i==7)
		$selectfromHour3 .= "\t<option value='$i' 'selected'>$i</option>\n";
	else
		$selectfromHour3 .= "\t<option value='$i'>$i</option>\n";
	*/
 }
 $selectfromHour3 .= '</select>';

 #minutes
 $selectfromMin3 .= '<select id="fromMin3" name="fromMin3">';

 for($i = 0; $i < 60; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selectfromMin3 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromMin3 .= '</select>';

 #meridian
 $selectfromMeridian3 .= '<select id="fromMeridian3" name="fromMeridian3">';
 $meridime = array("AM", "PM");
 foreach ($meridime as $i) {
	$selectfromMeridian3 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromMeridian3 .= '</select>';


 # SHIFT : TO
 #hours
 $selecttoHour3 .= '<select id="toHour3" name="toHour3">';

 for($i = 0; $i <= 12; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selecttoHour3 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selecttoHour3 .= '</select>';

 #minutes
 $selecttoMin3 .= '<select id="toMin3" name="toMin3">';

 for($i = 0; $i < 60; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selecttoMin3 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selecttoMin3 .= '</select>';

 #meridian
 $selecttoMeridian3 .= '<select id="toMeridian3" name="toMeridian3">';

 $meridime = array("AM", "PM");

 foreach ($meridime as $i) {
	$selecttoMeridian3 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromMeridian3 .= '</select>';

 $sTempShift = '';
 $sTempShift = '<b>FROM <b> &nbsp;&nbsp;
				'.$selectfromHour3.'<b>:</b>'.$selectfromMin3.'&nbsp;'.$selectfromMeridian3.'
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>TO <b> &nbsp;&nbsp;
				'.$selecttoHour3.'<b>:</b>'.$selecttoMin3.'&nbsp;'.$selecttoMeridian3;

 $smarty->assign('sShift3',$sTempShift);
 #--------------------------------------
 #Added by Cherry 04-29-09
	# SHIFT : FROM
 #hours
 $selectfromHour3 .= '<select id="fromHour3" name="fromHour3">';

 for($i = 0; $i <= 12; $i++){
	 if ($i<10)
		$i = '0'.$i;
	$selectfromHour3 .= "\t<option value='$i' $selected>$i</option>\n";
	/*
	if ($i==7)
		$selectfromHour3 .= "\t<option value='$i' 'selected'>$i</option>\n";
	else
		$selectfromHour3 .= "\t<option value='$i'>$i</option>\n";
	*/
 }
 $selectfromHour3 .= '</select>';

 #minutes
 $selectfromMin3 .= '<select id="fromMin3" name="fromMin3">';

 for($i = 0; $i < 60; $i++){
	 if ($i<10)
		$i = '0'.$i;
	$selectfromMin3 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromMin3 .= '</select>';

 #meridian
 $selectfromMeridian3 .= '<select id="fromMeridian3" name="fromMeridian3">';
 $meridime = array("AM", "PM");
 foreach ($meridime as $i) {
	$selectfromMeridian3 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromMeridian3 .= '</select>';


 # SHIFT : TO
 #hours
 $selecttoHour3 .= '<select id="toHour3" name="toHour3">';

 for($i = 0; $i <= 12; $i++){
	 if ($i<10)
		$i = '0'.$i;
	$selecttoHour3 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selecttoHour3 .= '</select>';

 #minutes
 $selecttoMin3 .= '<select id="toMin3" name="toMin3">';

 for($i = 0; $i < 60; $i++){
	 if ($i<10)
		$i = '0'.$i;
	$selecttoMin3 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selecttoMin3 .= '</select>';

 #meridian
 $selecttoMeridian3 .= '<select id="toMeridian3" name="toMeridian3">';

 $meridime = array("AM", "PM");

 foreach ($meridime as $i) {
	$selecttoMeridian3 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromMeridian3 .= '</select>';

 $sTempShift = '';
 $sTempShift = '<b>FROM <b> &nbsp;&nbsp;
				 '.$selectfromHour3.'<b>:</b>'.$selectfromMin3.'&nbsp;'.$selectfromMeridian3.'
				 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>TO <b> &nbsp;&nbsp;
				'.$selecttoHour3.'<b>:</b>'.$selecttoMin3.'&nbsp;'.$selecttoMeridian3;

 $smarty->assign('sShift4',$sTempShift);

 #---------------------------------------


 # select what kind of report to be generated
 $options = '';
 $options.='<option value="all"> - All Laboratory Requests - </option>
				<option value="wo_result">Requests Without Results</option>
				<option value="w_result">Requests With Results</option>';

 $smarty->assign('sReportSelect',
							"<select name=\"report_kind\" id=\"report_kind\">
								$options
							</select>");

 $smarty->assign('sReportSelect2',
							"<select name=\"report_kind2\" id=\"report_kind2\">
								$options
							</select>");

 # Assign form inputs (or values)

 //if ($saveok||$update) $smarty->assign('sOrderNrInput',$bestellnum.'</b><input type="hidden" name="bestellnum" value="'.$bestellnum.'">');
 //	else $smarty->assign('sOrderNrInput','<input type="text" name="bestellnum" value="'.$bestellnum.'" size=20 maxlength=20>');
	#$result=$db->Execute("SELECT * FROM seg_lab_service_groups ORDER BY name");
	$result = $srvObj->getLabServiceGroups2(1);

	$options="";
	//modified by Cherry 11-12-10
	/*while ($row=$result->FetchRow()) {
		$options_grp.='<option value="'.$row['group_code'].'">'.$row['name'].'</option>';
	}       */
	while ($row=$result->FetchRow()) {
		$options_grp.='<option value="'.$row['group_code'].'">'.$row['name'].'</option>';
	}
	$smarty->assign('sReportSelectGroup',
								"<select name=\"report_group\" id=\"report_group\">
										 <option value=\"all\">-All-</option>
										 $options_grp
								 </select>");

	$smarty->assign('sReportSelectGroup2',
								"<select name=\"report_group2\" id=\"report_group2\">
										 <option value=\"all\">-All-</option>
										 $options_grp
								 </select>");

	//added by Cherry 04-21-09
	$smarty->assign('sReportSelectGroup3',
									"<select name=\"report_group3\" id=\"report_group3\" onChange=\"showLabServices(this.value);\">
											<option value=\"all\">-All Group-</option>
											<option value=\"notBB\">-BLOOD BANK IS NOT INCLUDED-</option>
											$options_grp
									</select>");

	$smarty->assign('sReportSelectServices3',
									"<select name=\"service_list\" id=\"service_list\" >
											<option value=\"all\">-All Services-</option>
									</select>");

	$result=$db->Execute("SELECT * FROM seg_discount ORDER BY discountdesc");
	$options="";
	while ($row=$result->FetchRow()) {
		$options_class.='<option value="'.$row['discountid'].'">'.$row['discountdesc'].'</option>';
	}

	$result2 = $srvObj->getChargeType();
	#echo $srvObj->sql;
	while ($row2=$result2->FetchRow()) {
		$options_class2.='<option value="'.$row2['id'].'">'.$row2['charge_name'].'</option>';
	}
	$smarty->assign('sReportSelectClassification',
											"<select name=\"report_class\" id=\"report_class\">
												 <option value=\"all\">-All-</option>
												 $options_class
												 $$options_class2
											 </select>");

	$smarty->assign('sReportSelectClassification2',
											"<select name=\"report_class2\" id=\"report_class2\">
												 <option value=\"all\">-All-</option>
												 $options_class
												 $$options_class2
											 </select>");

	#commented for the meantime
	/*
	# select what kind of order to be generated
	 $options.='<option value="name"> PATIENT ID </option>
					<option value="wo_result">TRANSACTION NO.</option>
					<option value="wo_result">PATIENT NAME</option>
					<option value="wo_result">ORDER DATE</option>
					<option value="wo_result">TIME</option>
					<option value="wo_result">TEST</option>
					<option value="wo_result">SECTION</option>
					<option value="wo_result">PATIENT TYPE</option>
					<option value="w_result">DEPT/LOCATION</option>';

	 $smarty->assign('sReportOrder',
							"<select name=\"report_order\" id=\"report_order\">
								$options
							</select>");
	*/
	#$smarty->assign('sFromDateInput','<div id="show_from_date" style="margin-right:2px;padding:0px 2px;border:1px solid #888888;background-color:white;width:120px;min-height:18px;display:block;float:left"></div>');
	$smarty->assign('sFromDateInput','<input name="fromdt" id="from_date" type="text" size="8"
													value="">');
	#$smarty->assign('sFromDateHidden','<input type="hidden" name="fromdt" id="from_date" value="">');
	$smarty->assign('sFromDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="from_date_trigger" align="absmiddle" style="cursor:pointer">');

	#$smarty->assign('sToDateInput','<div id="show_to_date" style="margin-right:2px;padding:0px 2px;border:1px solid #888888;background-color:white;width:120px;min-height:18px;display:block;float:left"></div>');
	$smarty->assign('sToDateInput','<input name="todt" id="to_date" type="text" size="8"
													value="">');
	#$smarty->assign('sToDateHidden','<input type="hidden" name="todt" id="to_date" value="">');
	$smarty->assign('sToDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="to_date_trigger" align="absmiddle" style="cursor:pointer">');

	#$smarty->assign('sFromDateInput2','<div id="show_from_date2" style="margin-right:2px;padding:0px 2px;border:1px solid #888888;background-color:white;width:120px;min-height:18px;display:block;float:left"></div>');
	#$smarty->assign('sFromDateHidden2','<input type="hidden" name="fromdt2" id="from_date2" value="">');
	$smarty->assign('sFromDateInput2','<input name="fromdt2" id="from_date2" type="text" size="8"
													value="">');
	$smarty->assign('sFromDateIcon2','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="from_date_trigger2" align="absmiddle" style="cursor:pointer">');
	#$smarty->assign('sToDateInput2','<div id="show_to_date2" style="margin-right:2px;padding:0px 2px;border:1px solid #888888;background-color:white;width:120px;min-height:18px;display:block;float:left"></div>');
	#$smarty->assign('sToDateHidden2','<input type="hidden" name="todt2" id="to_date2" value="">');
	$smarty->assign('sToDateInput2','<input name="todt2" id="to_date2" type="text" size="8"
													value="">');
	$smarty->assign('sToDateIcon2','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="to_date_trigger2" align="absmiddle" style="cursor:pointer">');

#-----------------------

 #added by VAN 06-26-08
 #FORWARDING
 $options = '';
 $options.='<option value="0">- All Patient Type -</option>
			<option value="1">ER Patient</option>
			<option value="2">Outpatient</option>
			<option value="3">Inpatient</option>
			 ';

 $smarty->assign('sForwadingPType',
							"<select name=\"forwarding_type\" id=\"forwarding_type\" onChange=\"showhideWard(this.value)\">
								$options
							</select>");

 #added by VAN 07-08-2010
 $items='nr,name';
 $ward_info=&$ward_obj->getAllWardsItemsObject($items);

	$options_station="";
	if(!empty($ward_info)&&$ward_info->RecordCount()){
		while ($station=$ward_info->FetchRow()) {
			$options_station.='<option value="'.$station['nr'].'">'.$station['name'].'</option>';
		}
	}
	$smarty->assign('sWard',
								"<select name=\"ward_nr\" id=\"ward_nr\">
										 <option value=\"all\">-All-</option>
										 $options_station
								 </select>");

 #----------------

 $smarty->assign('sFromDateInput3','<input name="fromdt3" id="from_date3" type="text" size="8"
													value="">');
 $smarty->assign('sFromDateIcon3','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="from_date_trigger3" align="absmiddle" style="cursor:pointer">');

 $smarty->assign('sToDateInput3','<input name="todt3" id="to_date3" type="text" size="8"
													value="">');
 $smarty->assign('sToDateIcon3','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="to_date_trigger3" align="absmiddle" style="cursor:pointer">');

 #added by VAN 08-06-08
 $smarty->assign('sFromDateInput4','<input name="req_date" id="req_date" type="text" size="8"
													value="">');
 $smarty->assign('sFromDateIcon4','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="req_date_trigger" align="absmiddle" style="cursor:pointer">');


 #hours
 $selectfromHour2 .= '<select id="fromHour2" name="fromHour2">';

 for($i = 0; $i <= 12; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selectfromHour2 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromHour2 .= '</select>';

 #minutes
 $selectfromMin2 .= '<select id="fromMin2" name="fromMin2">';

 for($i = 0; $i < 60; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selectfromMin2 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromMin2 .= '</select>';

 #meridian
 $selectfromMeridian2 .= '<select id="fromMeridian2" name="fromMeridian2">';
 $meridime = array("AM", "PM");
 foreach ($meridime as $i) {
	$selectfromMeridian2 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromMeridian2 .= '</select>';


 # SHIFT : TO
 #hours
 $selecttoHour2 .= '<select id="toHour2" name="toHour2">';

 for($i = 0; $i <= 12; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selecttoHour2 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selecttoHour2 .= '</select>';

 #minutes
 $selecttoMin2 .= '<select id="toMin2" name="toMin2">';

 for($i = 0; $i < 60; $i++){
	if ($i<10)
		$i = '0'.$i;
	$selecttoMin2 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selecttoMin2 .= '</select>';

 #meridian
 $selecttoMeridian2 .= '<select id="toMeridian2" name="toMeridian2">';
 $meridime = array("AM", "PM");
 foreach ($meridime as $i) {
	$selecttoMeridian2 .= "\t<option value='$i' $selected>$i</option>\n";
 }
 $selectfromMeridian2 .= '</select>';

 $sTempShift = '';
 $sTempShift = '<b>FROM <b> &nbsp;&nbsp;
				'.$selectfromHour2.'<b>:</b>'.$selectfromMin2.'&nbsp;'.$selectfromMeridian2.'
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>TO <b> &nbsp;&nbsp;
				'.$selecttoHour2.'<b>:</b>'.$selecttoMin2.'&nbsp;'.$selecttoMeridian2;

 $smarty->assign('sShift2',$sTempShift);

 #--------------------

	$jsCalScript = "<script type=\"text/javascript\">
Calendar.setup (
{
	inputField : \"from_date\",
	ifFormat : \"%Y-%m-%d\",
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
	daFormat : \"$phpfd\",
	showsTime : false,
	button : \"from_date_trigger2\",
	singleClick : true,
	step : 1
}
);
Calendar.setup (
{
	inputField : \"to_date2\",
	ifFormat : \"%Y-%m-%d\",
	daFormat : \"$phpfd\",
	showsTime : false,
	button : \"to_date_trigger2\",
	singleClick : true, step : 1
}
);

Calendar.setup (
{
	inputField : \"from_date3\",
	ifFormat : \"%Y-%m-%d\",
	daFormat : \"$phpfd\",
	showsTime : false,
	button : \"from_date_trigger3\",
	singleClick : true,
	step : 1
}
);
Calendar.setup (
{
	inputField : \"to_date3\",
	ifFormat : \"%Y-%m-%d\",
	daFormat : \"$phpfd\",
	showsTime : false,
	button : \"to_date_trigger3\",
	singleClick : true, step : 1
}
);

Calendar.setup (
	{
		inputField : \"req_date\",
		ifFormat : \"%Y-%m-%d\",
		daFormat : \"$phpfd\",
		showsTime : false,
		button : \"req_date_trigger\",
		singleClick : true,
		step : 1
	}
);

</script>
";

	$smarty->assign('jsCalendarSetup', $jsCalScript);

#print_r($HTTP_SESSION_VARS);
# Collect hidden inputs

ob_start();
$sTemp='';
 ?>

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
	<input type="hidden" name="user" id="user" value="<?=$HTTP_SESSION_VARS['sess_user_name']?>" />

	<!--added by VAN 02-06-08-->
	<!--for shortcut keys -->
	<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
	<script type="text/javascript">

		//---------------adde by VAN 02-06-08
		function ShortcutKeys(){
			shortcut.add('Ctrl+Shift+M', BackMainMenu,
							{
								'type':'keydown',
								'propagate':false,
							}
						 )
		}

		function BackMainMenu(){
			urlholder="labor.php<?=URL_APPEND?>";
			window.location.href=urlholder;
		}
	//--------------------------------------

		function preSet(){
			document.getElementById('report_kind').focus();
		}

		//added by VAN 07-08-2010
		function showhideWard(pat_type){
			if (pat_type=='3'){
				document.getElementById('ward_name').style.display = "";
			}else{
				document.getElementById('ward_name').style.display = "none";
			}
		}
		//----------------

		function prufform(){
			var d = document.inputform;
			var mode = document.getElementById('report_type').value;

				/*
				if (d.report_group.value=="none") {
					alert("Select a laboratory section.");
					d.report_group.focus();
					return false;
				}
			 */
			 //alert('1 = '+parseInt(d.from_date.value));
			 //alert('2 = '+isNaN(parseInt(d.to_date.value=='')));
			 //alert('2 = '+parseInt(d.to_date.value));
			 if (mode==1){
					if (((d.from_date.value==' ')&&(d.to_date.value!=' ')) || ((isNaN(d.from_date.value)==false)&&(isNaN(d.to_date.value)==true))) {
						alert("Enter the starting date of the report.");
						d.from_date.focus();
						return false;
					}

					//if ((d.from_date.value!='') && (d.to_date.value=='')) {
					if (((d.from_date.value!=' ')&&(d.to_date.value==' ')) || ((isNaN(d.from_date.value)==true)&&(isNaN(d.to_date.value)==false))) {
						alert("Enter the end date of the report.");
						d.to_date.focus();
						return false;
					}

						if (d.from_date.value > d.to_date.value){
							alert("Starting date should be earlier than the ending date");
						d.from_date.focus();
						return false;
					}

					//added by VAN 06-04-08
				 // if ((d.patient_type.value==1) || (d.patient_type.value==2)){
						if ((d.from_date.value!=' ')&&(d.to_date.value!=' ')){
						if (d.from_date.value==d.to_date.value){
							if (d.fromHour.value==00) {
								alert("Enter the starting time of the report.");
								d.fromHour.focus();
								return false;
								}

							if (d.toHour.value==00) {
								alert("Enter the ending time of the report.");
								d.toHour.focus();
								return false;
								}

								if ((d.fromMeridian.value=="PM")&&(d.toMeridian.value=="AM")){
									alert("Starting time should be earlier than the ending time");
								d.frommer.focus();
								return false;
								}else{
									if (d.fromMeridian.value==d.toMeridian.value){
									if (d.fromHour.value>d.toHour.value){
										alert("Starting time should be earlier than the ending time");
										d.fromHour.focus();
										return false;
									}else if (d.fromHour.value==d.toHour.value){
										if (d.fromMin.value>d.toMin.value){
											alert("Starting time should be earlier than the ending time");
											d.fromMin.focus();
											return false;
										}
									}
								}
								}
						 }//if fromdate == todate
					 } //if date is not null
					 /*
					 else{
							if (d.fromHour.value!=00) {
							d.fromHour.value = 0;
							return true;
						}

						if (d.toHour.value!=00) {
							d.toHour.value = 0;
							return true;
							}

						if (d.fromMin.value!=00) {
							d.fromMin.value = 0;
							return true;
						}

						if (d.toMin.value!=00) {
							d.toMin.value = 0;
							return true;
							}
					 }
					 */
				//}// if patient type is ER or IPD
				//-------------------

			}else if (mode==2){
					//if ((d.from_date2.value=='')&&(d.to_date2.value!='')) {
					if (((d.from_date2.value==' ')&&(d.to_date2.value!=' ')) || ((isNaN(d.from_date2.value)==false)&&(isNaN(d.to_date2.value)==true))) {
						alert("Enter the starting date of the report.");
						d.from_date2.focus();
						return false;
					 }

					 //if ((d.from_date2.value!='') && (d.to_date2.value=='')) {
					if (((d.from_date2.value!=' ')&&(d.to_date2.value==' ')) || ((isNaN(d.from_date2.value)==true)&&(isNaN(d.to_date2.value)==false))) {
						alert("Enter the end date of the report.");
						d.to_date2.focus();
						return false;
						}

						 if (d.from_date2.value > d.to_date2.value){
							alert("Starting date should be earlier than the ending date");
						d.from_date2.focus();
						return false;
					 }
			}else if (mode==3){
					if (((d.from_date3.value==' ')&&(d.to_date3.value!=' ')) || ((isNaN(d.from_date3.value)==false)&&(isNaN(d.to_date3.value)==true))) {
						alert("Enter the starting date of the report.");
						d.from_date3.focus();
						return false;
					 }

					 if (((d.from_date3.value!=' ')&&(d.to_date3.value==' ')) || ((isNaN(d.from_date3.value)==true)&&(isNaN(d.to_date3.value)==false))) {
						alert("Enter the end date of the report.");
						d.to_date3.focus();
						return false;
						}

						 if (d.from_date3.value > d.to_date3.value){
							alert("Starting date should be earlier than the ending date");
						d.from_date3.focus();
						return false;
					 }

					 if ((d.from_date3.value!=' ')&&(d.to_date3.value!=' ')){
						if (d.from_date3.value==d.to_date3.value){
							/*
							if (d.fromHour2.value==00) {
								alert("Enter the starting time of the report.");
								d.fromHour2.focus();
								return false;
								}

							if (d.toHour2.value==00) {
								alert("Enter the ending time of the report.");
								d.toHour2.focus();
								return false;
								}
									*/
								if ((d.fromMeridian2.value=="PM")&&(d.toMeridian2.value=="AM")){
									alert("Starting time should be earlier than the ending time");
								d.frommer2.focus();
								return false;
								}else{
									if (d.fromMeridian2.value==d.toMeridian2.value){
									if (d.fromHour2.value>d.toHour2.value){
										alert("Starting time should be earlier than the ending time");
										d.fromHour2.focus();
										return false;
									}else if (d.fromHour2.value==d.toHour2.value){
										if (d.fromMin2.value>d.toMin2.value){
											alert("Starting time should be earlier than the ending time");
											d.fromMin2.focus();
											return false;
										}
									}
								}
								}
						 }//if fromdate == todate
					 } //if date is not null
			}
			//added by VAN 08-06-08
			else if (mode==4){
				//findings
				//alert('here');
				/*
				if(d.report_group2.value==0){
					alert("Select a radiology section.");
					d.report_group2.focus();
					return false;
				}
				*/
				if ((d.req_date.value==' ') || (isNaN(d.req_date.value)==false)) {
					alert("Enter the date of the report.");
					d.req_date.focus();
					return false;
				}

				if (d.fromHour3.value==00) {
					alert("Enter the starting time of the report.");
					d.fromHour3.focus();
					return false;
					}

				if (d.toHour3.value==00) {
					alert("Enter the ending time of the report.");
					d.toHour3.focus();
					return false;
					}

				if ((d.fromMeridian3.value=="PM")&&(d.toMeridian3.value=="AM")){
					alert("Starting time should be earlier than the ending time");
					d.frommer3.focus();
					return false;
					}else{
					if (d.fromMeridian3.value==d.toMeridian3.value){
						if //(parseInt(d.fromHour.value)>parseInt(d.toHour.value)){
							(d.fromHour3.value>d.toHour3.value){
							alert("Starting time should be earlier than the ending time");
							d.fromHour3.focus();
							return false;
						}else if //(parseInt(d.fromHour.value)==parseInt(d.toHour.value)){
							(d.fromHour3.value==d.toHour3.value){
							if //(parseInt(d.fromMin.value)>parseInt(d.toMin.value)){
								(d.fromMin3.value>d.toMin3.value){
								alert("Starting time should be earlier than the ending time");
								d.fromMin3.focus();
								return false;
							}
						}
					}
				}
			}else if (mode==5){
					//if ((d.from_date2.value=='')&&(d.to_date2.value!='')) {
					if (((d.from_date2.value==' ')&&(d.to_date2.value!=' ')) || ((isNaN(d.from_date2.value)==false)&&(isNaN(d.to_date2.value)==true))) {
						alert("Enter the starting date of the report.");
						d.from_date2.focus();
						return false;
					 }

					 if (((d.from_date2.value!=' ')&&(d.to_date2.value==' ')) || ((isNaN(d.from_date2.value)==true)&&(isNaN(d.to_date2.value)==false))) {
						alert("Enter the end date of the report.");
						d.to_date2.focus();
						return false;
						}

						 if (d.from_date2.value > d.to_date2.value){
							alert("Starting date should be earlier than the ending date");
						d.from_date2.focus();
						return false;
					 }
		 }
		 else if (mode==6){
			 if (((d.from_date3.value==' ')&&(d.to_date3.value!=' ')) || ((isNaN(d.from_date3.value)==false)&&(isNaN(d.to_date3.value)==true))) {
						alert("Enter the starting date of the report.");
						d.from_date3.focus();
						return false;
					 }

					 if (((d.from_date3.value!=' ')&&(d.to_date3.value==' ')) || ((isNaN(d.from_date3.value)==true)&&(isNaN(d.to_date3.value)==false))) {
						alert("Enter the end date of the report.");
						d.to_date3.focus();
						return false;
						}

						 if (d.from_date3.value > d.to_date3.value){
							alert("Starting date should be earlier than the ending date");
						d.from_date3.focus();
						return false;
					 }

					 if ((d.from_date3.value!=' ')&&(d.to_date3.value!=' ')){
						if (d.from_date3.value==d.to_date3.value){
							/*
							if (d.fromHour2.value==00) {
								alert("Enter the starting time of the report.");
								d.fromHour2.focus();
								return false;
								}

							if (d.toHour2.value==00) {
								alert("Enter the ending time of the report.");
								d.toHour2.focus();
								return false;
								}
									*/
								if ((d.fromMeridian2.value=="PM")&&(d.toMeridian2.value=="AM")){
									 alert("Starting time should be earlier than the ending time");
								d.frommer2.focus();
								return false;
								}else{
									 if (d.fromMeridian2.value==d.toMeridian2.value){
									if (d.fromHour2.value>d.toHour2.value){
										alert("Starting time should be earlier than the ending time");
										d.fromHour2.focus();
										return false;
									}else if (d.fromHour2.value==d.toHour2.value){
										if (d.fromMin2.value>d.toMin2.value){
											alert("Starting time should be earlier than the ending time");
											d.fromMin2.focus();
											return false;
										}
									}
								}
								 }
						 }//if fromdate == todate
					 } //if date is not null
		 }
			return true;
		}


				function viewLabCharges(){
						var url = "reports/pdf-charges.php?showBrowser=1";
						window.open(url,"viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
				}
//added by daryl
function viewNewBorn(){

	var newborn_quarter = document.getElementById('newborn_quarter').value;
	var newborn_year = document.getElementById('newborn_year').value;

	var url = "reports/pdf-NewBorn.php?showBrowser=1&newborn_quarter="+newborn_quarter+"&newborn_year="+newborn_year;
	window.open(url,"NewBorn","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}
//added by daryl
function showLabServices(data){
xajax_serviceByGroup(data);
}

//added by daryl
function js_AddOptions(tagId, text, value){
    var elTarget = $(tagId);
    if(elTarget){
        var opt = new Option(text, value);
        opt.id = value;
        elTarget.appendChild(opt);
    }
    var optionsList = elTarget.getElementsByTagName('OPTION');
}//end of function js_AddOption
//ended by daryl

		//function viewReport(report_group, report_class, fromdate, todate){
		function viewReport(val){
			var bol = prufform();
			var d = document.inputform;

			if (bol){
				var rpt_kind = document.getElementById('report_kind').value;
				var rpt_group = document.getElementById('report_group').value;
				var rpt_class = document.getElementById('report_class').value;
				var fromdate = document.getElementById('from_date').value;
				var todate = document.getElementById('to_date').value;
				var grpview;

				//added by VAN 06-04-08
				var pat_type = document.getElementById('patient_type').value;
				var fromhour = document.getElementById('fromHour').value;
				var frommin = document.getElementById('fromMin').value;
				var frommer = document.getElementById('fromMeridian').value;
				var fromtime = fromhour+":"+frommin+":00 "+frommer;

				var tohour = document.getElementById('toHour').value;
				var tomin = document.getElementById('toMin').value;
				var tomer = document.getElementById('toMeridian').value;
				var totime = tohour+":"+tomin+":00 "+tomer;

				var user = document.getElementById('user').value;

				if (isNaN(fromdate)==false){
					fromdate = 0;
				}

				if (isNaN(todate)==false)
					todate = 0;
				/*
				if ((d.patient_type.value==1) || (d.patient_type.value==2)){
						if ((d.from_date.value==' ')&&(d.to_date.value==' ')){
						if (d.fromHour.value!=00) {
							d.fromHour.value = "00";
						}

						if (d.toHour.value!=00) {
							d.toHour.value = "00";
						}

						if (d.fromMin.value!=00) {
							d.fromMin.value = "00";
						}

						if (d.toMin.value!=00) {
							d.toMin.value = "00";
						}
					}
				}
				*/

				grpview = 1;
				if (val){
					if (document.getElementById('viewgrp').checked){
						//grpview = 1;
						window.open("seg-lab-report-pdf.php?report_kind="+rpt_kind+"&report_group="+rpt_group+"&report_class="+rpt_class+"&fromdate="+fromdate+"&todate="+todate+"&grpview="+grpview+"&user="+user+"&pat_type="+pat_type+"&fromtime="+fromtime+"&totime="+totime+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
					}else{
						//grpview = 0;
						if (val==1){
							window.open("seg-lab-report-detailed-pdf.php?report_kind="+rpt_kind+"&report_group="+rpt_group+"&report_class="+rpt_class+"&fromdate="+fromdate+"&todate="+todate+"&grpview="+grpview+"&user="+user+"&pat_type="+pat_type+"&fromtime="+fromtime+"&totime="+totime+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
						}else{
							var req_date = document.getElementById('req_date').value;

							var fromhour3 = document.getElementById('fromHour3').value;
							var frommin3 = document.getElementById('fromMin3').value;
							var frommer3 = document.getElementById('fromMeridian3').value;
							var fromtime3 = fromhour3+":"+frommin3+":00 "+frommer3;

							var tohour3 = document.getElementById('toHour3').value;
							var tomin3 = document.getElementById('toMin3').value;
							var tomer3 = document.getElementById('toMeridian3').value;
							var totime3 = tohour3+":"+tomin3+":00 "+tomer3;

							var rpt_group2 = document.getElementById('report_group2').value;
							var patient_type2 = document.getElementById('patient_type2').value;
							var rpt_class2 = document.getElementById('report_class2').value;
							//alert("fromhour = "+fromhour);
							//alert("tohour = "+tohour);
							//alert(patient_type2);
							//alert('class - '+rpt_class2);
							window.open("seg-lab-report-patient-pdf.php?req_date="+req_date+"&fromtime="+fromtime3+"&totime="+totime3+"&rpt_group="+rpt_group2+"&user="+user+"&patient_type="+patient_type2+"&class="+rpt_class2+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
						}
					}
					//alert(rpt_group+", "+rpt_class+", "+fromdate+", "+todate);
					//window.open("seg-lab-report-pdf.php?report_kind="+rpt_kind+"&report_group="+rpt_group+"&report_class="+rpt_class+"&fromdate="+fromdate+"&todate="+todate+"&grpview="+grpview+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
				}else{
					//laboratory report based on their existing format
					//window.open("seg-lab-report-format-pdf.php?fromdate="+fromdate+"&todate="+todate+"&fromtime="+fromtime+"&totime="+totime+"&pat_type="+pat_type+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
					window.open("seg-lab-report-format-pdf.php?fromdate="+fromdate+"&todate="+todate+"&fromtime="+fromtime+"&totime="+totime+"&pat_type="+pat_type+"&report_kind="+rpt_kind+"&report_group="+rpt_group+"&report_class="+rpt_class+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
				}
			}
		}
		/*
		function viewStatistics(){
			var bol = prufform();
			//alert('statistics');
			if (bol){
				//var rpt_kind = document.getElementById('report_kind2').value;
				//var rpt_group = document.getElementById('report_group2').value;
				var rpt_kind = 'all';
				var rpt_group = 'all';
				var rpt_class = 'all';
				var fromdate = document.getElementById('from_date2').value;
				var todate = document.getElementById('to_date2').value;

				if (isNaN(fromdate)==false){
					fromdate = 0;
				}

				if (isNaN(todate)==false)
					todate = 0;

				grpview = 0;
				window.open("seg-lab-stat-report-pdf.php?report_kind="+rpt_kind+"&report_group="+rpt_group+"&report_class="+rpt_class+"&fromdate="+fromdate+"&todate="+todate+"&grpview="+grpview+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
			}
		}
		*/


		function viewStatistics(){
			var bol = prufform();
			if (bol){
				var fromdate = document.getElementById('from_date2').value;
				var todate = document.getElementById('to_date2').value;

				if (isNaN(fromdate)==false){
					fromdate = 0;
				}

				if (isNaN(todate)==false)
					todate = 0;

				//grpview = 0;
				window.open("seg-lab-stat-report-pdf.php?fromdate="+fromdate+"&todate="+todate+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
			}
		}

		function showShift(val){
			/*
			if ((val==1)||(val==2)||(val==3)){
				document.getElementById('shiftrow').style.display='';
			}else{
				document.getElementById('shiftrow').style.display='none';
			}
			*/
			document.getElementById('shiftrow').style.display='';
		}
		//Modified by Cherry 07-28-10
		function selectMode(val){
			//alert('val = '+val);
			if (val==1){
				document.getElementById('mode_status').style.display='';
				document.getElementById('mode_stat').style.display='none';
				document.getElementById('mode_forwarding').style.display='none';
				document.getElementById('mode_results').style.display='none';
				document.getElementById('rpt_charges').style.display='none';
				document.getElementById('rpt_NewBorn').style.display='none';
				//document.getElementById('mode_income').style.display='none';

				//reset values
				document.getElementById('viewgrp').checked=false;
				document.getElementById('report_kind').value='all';
				document.getElementById('report_group').value='all';
				document.getElementById('from_date').value=' ';
				document.getElementById('to_date').value=' ';
				document.getElementById('report_class').value='all';
				document.getElementById('patient_type').value = 0;

				document.getElementById('fromHour').value = '00';
				document.getElementById('fromMin').value = '00';
				document.getElementById('fromMeridian').value = 'AM';

				document.getElementById('toHour').value = '00';
				document.getElementById('toMin').value = '00';
				document.getElementById('toMeridian').value = 'AM';

			}else if ((val==2)||(val==5) || (val==8)) {      //modified by Cherry 07-28-10

				if(val==2){
					 document.getElementById('viewLabCensusPdf').style.display='none';
					 document.getElementById('viewLabCensusExl').style.display='none';
					 document.getElementById('viewStat').style.display='';
					 document.getElementById('viewbloodbank').style.display='none';
					 document.getElementById('viewincome').style.display='none';
					 document.getElementById('serv_grp').style.display='none';
					 document.getElementById('pat_type').style.display='none';
				}else if(val==8){
					 document.getElementById('viewLabCensusPdf').style.display='none';
					 document.getElementById('viewLabCensusExl').style.display='none';
					 document.getElementById('viewStat').style.display='none';
					 document.getElementById('viewbloodbank').style.display='';
					 document.getElementById('viewincome').style.display='none';
					 document.getElementById('serv_grp').style.display='none';
					 document.getElementById('pat_type').style.display='none';
				}else{
					 document.getElementById('viewLabCensusPdf').style.display='none';
					 document.getElementById('viewLabCensusExl').style.display='none';
					 document.getElementById('viewStat').style.display='none';
					 document.getElementById('viewbloodbank').style.display='none';
					 document.getElementById('viewincome').style.display='';
					 document.getElementById('serv_grp').style.display='';
					 document.getElementById('pat_type').style.display='';
				}
				document.getElementById('mode_status').style.display='none';
				document.getElementById('mode_stat').style.display='';
				document.getElementById('mode_forwarding').style.display='none';
				document.getElementById('mode_results').style.display='none';
				document.getElementById('rpt_charges').style.display='none';
				document.getElementById('rpt_NewBorn').style.display='none';
				//document.getElementById('mode_income').style.display='none';
				//reset values
				//document.getElementById('report_kind2').value='all';
				//document.getElementById('report_group2').value='all';
				document.getElementById('from_date2').value=' ';
				document.getElementById('to_date2').value=' ';
				document.getElementById('patient_type3').value=0;
				document.getElementById('report_group3').value='all';
			}else if ((val==3) || (val == 6)){

					if(val == 6){
						document.getElementById('viewForWard').style.dispaly = 'none';
						document.getElementById('viewMatch').style.display = '';
						document.getElementById('pat_type').style.display='none';
					}
				document.getElementById('mode_status').style.display='none';
				document.getElementById('mode_stat').style.display='none';
				document.getElementById('mode_forwarding').style.display='';

				document.getElementById('mode_results').style.display='none';
				document.getElementById('rpt_charges').style.display='none';
				document.getElementById('rpt_NewBorn').style.display='none';
				//document.getElementById('mode_income').style.display='none';

			//	document.getElementById('mode_results').style.display='none';
				document.getElementById('report_class').value='all';


				document.getElementById('from_date3').value=' ';
				document.getElementById('to_date3').value=' ';

				document.getElementById('fromHour2').value = '00';
				document.getElementById('fromMin2').value = '00';
				document.getElementById('fromMeridian2').value = 'AM';

				document.getElementById('toHour2').value = '00';
				document.getElementById('toMin2').value = '00';
				document.getElementById('toMeridian2').value = 'AM';

			//added by VAN 08-06-08
			}else  if (val==4){
				//alert('here');
				document.getElementById('mode_status').style.display='none';
				document.getElementById('mode_stat').style.display='none';
				document.getElementById('mode_forwarding').style.display='none';
				document.getElementById('mode_results').style.display='';
				document.getElementById('rpt_charges').style.display='none';
				document.getElementById('rpt_NewBorn').style.display='none';
				//document.getElementById('mode_income').style.display='none';

			}
			 else if(val==7 ){
								document.getElementById('mode_status').style.display='none';
								document.getElementById('mode_stat').style.display='none';
								document.getElementById('mode_forwarding').style.display='none';
								document.getElementById('mode_results').style.display='none';
								document.getElementById('rpt_charges').style.display='';
								document.getElementById('rpt_NewBorn').style.display='none';

						}
			 else if(val==10){
								document.getElementById('mode_status').style.display='none';
								document.getElementById('mode_stat').style.display='none';
								document.getElementById('mode_forwarding').style.display='none';
								document.getElementById('mode_results').style.display='none';
								document.getElementById('rpt_charges').style.display='none';
								document.getElementById('rpt_NewBorn').style.display='';

						}
			/*else if (val==5){
				document.getElementById('mode_status').style.display='none';
				document.getElementById('mode_stat').style.display='none';
				document.getElementById('mode_forwarding').style.display='none';
				document.getElementById('mode_results').style.display='none';
				document.getElementById('mode_income').style.display='';

				document.getElementById('from_date2').value=' ';
				document.getElementById('to_date2').value=' ';
			 */
			 else if(val==9){
				document.getElementById('viewLabCensusPdf').style.display='';
				document.getElementById('viewLabCensusExl').style.display='';
				document.getElementById('viewStat').style.display='none';
				document.getElementById('viewbloodbank').style.display='none';
				document.getElementById('viewincome').style.display='none';
				document.getElementById('serv_grp').style.display='';
				document.getElementById('pat_type').style.display='none';

			 	document.getElementById('mode_status').style.display='none';
				document.getElementById('mode_stat').style.display='';
				document.getElementById('mode_forwarding').style.display='none';
				document.getElementById('mode_results').style.display='none';
				document.getElementById('rpt_charges').style.display='none';

				document.getElementById('from_date2').value=' ';
				document.getElementById('to_date2').value=' ';
				document.getElementById('patient_type3').value=0;
			 }
			else{
				document.getElementById('mode_status').style.display='none';
				document.getElementById('mode_stat').style.display='none';
				document.getElementById('mode_forwarding').style.display='none';
				document.getElementById('mode_results').style.display='none';
			 // document.getElementById('mode_income').style.display='none';
			}
		}
		 /* Income Report*/
		function viewIncome(){
			var bol = prufform();
			//alert("bol is "+bol);
			if (bol){

				var fromdate = document.getElementById('from_date2').value;
				var todate = document.getElementById('to_date2').value;
				var patient_type = document.getElementById('patient_type3').value;
				var serv_group = document.getElementById('report_group3').value;
				var service_list = document.getElementById('service_list').value;
		//var serv_group = document.getElementById('serv_grp').value;
				if (isNaN(fromdate)==false){
					fromdate = 0;
				}

				if (isNaN(todate)==false)
					todate = 0;
				// alert('from ='+fromdate);
				 //alert('patient_type ='+patient_type);
				//grpview = 0;
				window.open("seg-lab-income_report-pdf.php?fromdate="+fromdate+"&todate="+todate+"&patient_type="+patient_type+"&serv_group="+serv_group+"&service_code="+service_list+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
			}
		}


		//Added by Cherry 07-28-10
		function viewBloodBank(){
				 var bol = prufform();
			if (bol){
				var fromdate = document.getElementById('from_date2').value;
				var todate = document.getElementById('to_date2').value;

				if (isNaN(fromdate)==false){
					fromdate = 0;
				}

				if (isNaN(todate)==false)
					todate = 0;

				//grpview = 0;
				window.open("seg-lab-report-bloodbank-pdf.php?fromdate="+fromdate+"&todate="+todate+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
			}

		}

		//added by VAN 06-26-08
		function viewFor_Warding(){
			//alert('viewFor_Warding');
			var bol = prufform();
			if (bol){
				var pat_type = document.getElementById('forwarding_type').value;
				var fromdate = document.getElementById('from_date3').value;
				var todate = document.getElementById('to_date3').value;

				var fromhour = document.getElementById('fromHour2').value;
				var frommin = document.getElementById('fromMin2').value;
				var frommer = document.getElementById('fromMeridian2').value;
				var fromtime = fromhour+":"+frommin+":00 "+frommer;

				var tohour = document.getElementById('toHour2').value;
				var tomin = document.getElementById('toMin2').value;
				var tomer = document.getElementById('toMeridian2').value;
				var totime = tohour+":"+tomin+":00 "+tomer;

				var ward_nr = document.getElementById('ward_nr').value;
			//alert(pat_type);
				window.open("seg-lab-forwarding-report-pdf.php?pat_type="+pat_type+"&fromdate="+fromdate+"&fromtime="+fromtime+"&todate="+todate+"&totime="+totime+"&ward_nr="+ward_nr+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
			}
		}

		//added by Cherry 04-29-09
		function viewMatch(){
			alert('view Crossmatch');
			var bol = prufform();
			if (bol){
				var fromdate = document.getElementById('from_date3').value;
				var todate = document.getElementById('to_date3').value;

				var fromhour = document.getElementById('fromHour2').value;
				var frommin = document.getElementById('fromMin2').value;
				var frommer = document.getElementById('fromMeridian2').value;
				var fromtime = fromhour+":"+frommin+":00 "+frommer;

				var tohour = document.getElementById('toHour2').value;
				var tomin = document.getElementById('toMin2').value;
				var tomer = document.getElementById('toMeridian2').value;
				var totime = tohour+":"+tomin+":00 "+tomer;
				window.open("seg-lab-gel-tech-crossmatching.php?fromdate="+fromdate+"&fromtime="+fromtime+"&todate="+todate+"&totime="+totime+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
			}
		}

		function viewLabCensusReport(format){
			var bol = prufform();
			if (bol){
				var fromdate = document.getElementById('from_date2').value;
				var todate = document.getElementById('to_date2').value;
				var lab_group = document.getElementById('report_group3').value;

				if (isNaN(fromdate)==false){
					fromdate = 0;
					alert('Please select date from.');
					return false;
				}

				if (isNaN(todate)==false){
					todate = 0;
					alert('Please select date to.');
					return false;
				}

				//grpview = 0;
				window.open("seg-lab-report-census.php?fromdate="+fromdate+"&todate="+todate+"&showBrowser=1&lab_group="+lab_group+"&format="+format,"viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
			}
			return false;
		}

	</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

	$smarty->assign('sHiddenInputs',$sTemp);
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
	#$smarty->assign('sContinueButton','<input type="image" '.createLDImgSrc($root_path,'continue.gif','0','left').' align="absmiddle">');

	$smarty->assign('sContinueButton','<img name="viewreport" id="viewreport" onClick="viewReport(1);" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif','0','left') . ' border="0">');
	$smarty->assign('sReportButton','<img name="viewlabreport" id="viewlabreport" onClick="viewReport(0);" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport2.gif','0','left') . ' border="0">');

	$smarty->assign('sStatButton','<img name="viewStat" id="viewStat" onClick="viewStatistics();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showstatreport.gif','0','left') . ' border="0">');

	#added by VAN 06-26-08
	$smarty->assign('sForwardButton','<img name="viewForWard" id="viewForWard" onClick="viewFor_Warding();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showforward.gif','0','left') . ' border="0">');

	#added by VAN 08-06-08
	$smarty->assign('sResultsButton','<img name="viewreport" id="viewreport" onClick="viewReport(2);" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showresults.gif','0','left') . ' border="0">');

		#$smarty->assign('sGenerateButton','<img name="viewlabresults" id="viewlabresults" onClick="viewResultReport(0);" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif','0','left') . ' border="0">');
		$smarty->assign('sGenerate3Button','<img name="viewlabcharges" id="viewlabcharges" onClick="viewLabCharges();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif','0','left') . ' border="0">');

	$smarty->assign('sIncomeButton','<img name="viewincome" id="viewincome" onClick="viewIncome();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif','0','left') . ' border="0">');

	#Added by Cherry 07-28-10
	$smarty->assign('sBloodBankButton','<img name="viewbloodbank" id="viewbloodbank" onClick="viewBloodBank();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif','0','left') . ' border="0">');

	$smarty->assign('sLabCensusPdf','<button class="segButton" id="viewLabCensusPdf" onclick="return viewLabCensusReport(\'PDF\')" ><img height="16" src="'.$root_path.'img/icons/page_white_acrobat.png" >PDF Format</button>');
	$smarty->assign('sLabCensusExl','<button class="segButton" id="viewLabCensusExl" onclick="return viewLabCensusReport(\'EXCEL\')" ><img height="16" src="'.$root_path.'img/icons/excel32.png" >EXCEL Format</button>');

	#added by Cherry 04-29-09
	#$smarty->assign('sCrossmatchingButton','img name="viewCrossmatching" id="viewCrossmatching" onClick="viewCrossmatching();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif', '0', 'left') . ' border="0">');
	$smarty->assign('sCrossmatchButton','<img name="viewMatch" id="viewMatch" onClick="viewMatch();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif','0','left') . ' border="0">');
	# Assign the form template to mainframe
	$smarty->assign('sMainBlockIncludeFile','laboratory/form_report.tpl');

	$xajax->printJavascript($root_path.'classes/xajax');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>