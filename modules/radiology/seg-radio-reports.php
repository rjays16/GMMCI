<?php
#created by VAN 06-19-08
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

$title="Radiology :: Reports Generator";
/* 2007-09-27 FDP
 replaced the orig line (which follows) for Close button target
$breakfile=$root_path."modules/pharmacy/seg-pharma-retail-functions.php".URL_APPEND."&userck=$userck";
 */
$breakfile = "radiolog.php";
$thisfile='seg-radio-reports.php';

//if ($send_details) include($root_path.'include/inc_retail_display_rdetails.php');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

	 require_once($root_path.'include/inc_front_chain_lang.php');

 # Create laboratory service object
 require_once($root_path.'include/care_api_classes/class_radiology.php');
 $srvObj=new SegRadio();

 require_once($root_path.'include/care_api_classes/class_encounter.php');
 $enc_obj=new Encounter;

 require_once($root_path.'include/care_api_classes/class_personell.php');
 $pers_obj=new Personell;

 require_once($root_path.'include/care_api_classes/class_department.php');
 $dept_obj=new Department;

 require_once($root_path.'include/care_api_classes/class_person.php');
 $person_obj=new Person();

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

 $onLoadJS='onLoad="preSet(); ShortcutKeys();"';
 #$onLoadJS.='';
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

 ob_start();
 # Load the javascript code
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

 # select if view by reference number
 $smarty->assign('sViewGroup','<input type="checkbox" name="viewgrp" id="viewgrp" value="1">');

 $options = '';
 $options.='
				<option value="0">- Select Report Mode -</option>
				<option value="1">Consumption Report</option>
				<option value="2">Statistics Report</option>
				<option value="3">Classification Report</option>
				<option value="4">Patient\'s List Report</option>
                <option value="5">Logbook</option>
                <option value="6">Monthly Report</option>
				';

 $smarty->assign('sReportSelectType',
							"<select name=\"report_type\" id=\"report_type\" onChange=\"selectMode(this.value);\">
								$options
							</select>");


 $options = '';
 $options.='
				<option value="0">-All Patient Type -</option>
				<option value="1">ER Patient</option>
				<option value="2">Inpatient</option>
				<option value="3">OutPatient</option>
				<option value="4">Walk-in</option>
				<option value="5">OPD & Walk-in</option>
				<option value="6">ER & Inpatient</option>
				';
 /*
 $smarty->assign('sPatientSelect',
							"<select name=\"patient_type\" id=\"patient_type\" onChange=\"showShift(this.value);\">
								$options
							</select>");
 */
 $smarty->assign('sPatientSelect',
							"<select name=\"patient_type\" id=\"patient_type\">
								$options
							</select>");
                            
 
 $options_status.='
                <option value="0">-All Served Status -</option>
                <option value="1">Served</option>
                <option value="2">Not Served</option>
                ';                           
                            
 $smarty->assign('sReportStatus',
                            "<select name=\"request_status\" id=\"request_status\">
                                $options_status
                            </select>");                            

 $smarty->assign('sPatientSelect2',
							"<select name=\"patient_type2\" id=\"patient_type2\">
								$options
							</select>");
                            
 $smarty->assign('sPatientSelect3',
                            "<select name=\"patient_type3\" id=\"patient_type3\">
                                $options
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

 # select what kind of report to be generated
 $options = '';
 $options.='<option value="all"> - All Radiological Requests - </option>
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
                                                       
 $options = '';
 $options.='<option value="all"> - All Radiological Requests - </option>
                <option value="wo_result">Without Results</option>
                <option value="w_result_initial">Initial Results</option>
                <option value="w_result_official">Official Results</option>';                           

 $smarty->assign('sReportSelect3',
                            "<select name=\"report_kind3\" id=\"report_kind3\">
                                $options
                            </select>");                            
                            
 # Assign form inputs (or values)

	$result = &$dept_obj->getAllRadiologyDept();

	$options="";
	if(is_object($result)){
		while ($row=$result->FetchRow()) {
			$options_grp.='<option value="'.$row['nr'].'">'.$row['name_formal'].'</option>';
		}
	}
	$smarty->assign('sReportSelectGroup',
								"<select name=\"report_group\" id=\"report_group\">
										 <option value=\"all\">-All-</option>
										 $options_grp
								 </select>");

	$smarty->assign('sReportSelectGroup2',
								"<select name=\"report_group2\" id=\"report_group2\">
										 <option value=\"0\">-All Radioloy Section-</option>
										 $options_grp
								 </select>");
    
    $smarty->assign('sReportSelectGroup3',
                                "<select name=\"report_group3\" id=\"report_group3\">
                                         <option value=\"0\">-All Radioloy Section-</option>
                                         $options_grp
                                 </select>");                             

 #added by VAN 08-19-08
 $options = '';
 $options.='<option value="1"> By Radiology Section-Groups</option>
						<option value="2">By Radiology Department</option>
			<option value="3">By Radiology Service</option>
			<option value="4">By Film Size</option>';

 $smarty->assign('sReportSelectStat',
							"<select name=\"report_statkind\" id=\"report_statkind\">
								$options
							</select>");
	#-------------------------

	#added by VAN 06-20-08
	$result_cases = $srvObj->getChargeType();
	$options_grp="";
	if(is_object($result_cases)){
		while ($row=$result_cases->FetchRow()) {
			$options_grp.='<option value="'.$row['id'].'">'.$row['charge_name'].'</option>';
		}
	}

	$lastid = $srvObj->getLastIDChargeType();

	#$last = sprintf ("%d",$lastid['id']);
	$last = floor($lastid['id'])+1;
	$last2 = floor($lastid['id'])+2;
	$last3 = floor($lastid['id'])+3;

	$smarty->assign('sCases',"<select name=\"report_cases\" id=\"report_cases\">
										 <option value=\"all\">-All-</option>
										 $options_grp
									 <option value=\"".$last."\">TPL</option>
									 <option value=\"".$last2."\">SENIOR CITIZEN</option>
									 <option value=\"".$last3."\">NOT CLASSIFIED</option>
								 </select>");

	#---------------------------

	#added by VAN 04-29-2011
	#get all the doctor's in radiology department
	#Radiology
	$deptarray = $dept_obj->_getalldata("name_formal LIKE '%Radiology%'");
	while(list($x,$v)=each($deptarray)){
		$dept_nr = $v ['nr'];
	}

	$result_dr = $pers_obj->getDoctorsOfDept($dept_nr);

	$options_dr='';
	if(is_object($result_dr)){
		while ($row_dr=$result_dr->FetchRow()) {

			$middleInitial = "";
			if (trim($row_dr['name_middle'])!=""){
					$thisMI=split(" ",$row_dr['name_middle']);
					foreach($thisMI as $value){
						if (!trim($value)=="")
						$middleInitial .= $value[0];
					}
					if (trim($middleInitial)!="")
					$middleInitial .= ". ";
			}

			$doctor_name = mb_strtoupper($row_dr["name_last"]).", ".mb_strtoupper($row_dr["name_first"])." ".$middleInitial;
			$options_dr.='<option value="'.$row_dr['personell_nr'].'">'. $doctor_name.'</option>';
		}
	}
    
    $smarty->assign('sReportRadDoctor',
											"<select name=\"doctor_choices\" id=\"doctor_choices\">
												 <option value=\"0\">--Select a Doctor--</option>
												 $options_dr
											 </select>");
                                             
    $smarty->assign('sReportRadDoctor2',
                                            "<select name=\"doctor_choices2\" id=\"doctor_choices2\">
                                                 <option value=\"0\">--Select a Doctor--</option>
                                                 $options_dr
                                             </select>");          

    $smarty->assign('sReportRadDoctor3',
                                            "<select name=\"doctor_choices3\" id=\"doctor_choices3\">
                                                 <option value=\"0\">--Select a Doctor--</option>
                                                 $options_dr
                                             </select>");                                       
	#----------------------

	$result=$db->Execute("SELECT * FROM seg_discount ORDER BY discountdesc");
	$options="";
	while ($row=$result->FetchRow()) {
		$options_class.='<option value="'.$row['discountid'].'">'.$row['discountdesc'].'</option>';
	}
	$smarty->assign('sReportSelectClassification',
											"<select name=\"report_class\" id=\"report_class\">
												 <option value=\"all\">-All-</option>
												 $options_class
											 </select>");
                                             
    
    $result_radtech = $pers_obj->getRadTech();
    if(is_object($result_radtech)){
        while ($row_radtech=$result_radtech->FetchRow()) {

            $options_radtech.='<option value="'.$row_radtech['nr'].'">'.mb_strtoupper($row_radtech['name_last'].", ".$row_radtech['name_first']).'</option>';
        }
    }
                                             
    $smarty->assign('sReportRadTech2',
                                            "<select name=\"radtech_choices\" id=\"radtech_choices\">
                                                 <option value=\"0\">--All Rad Tech--</option>
                                                 $options_radtech
                                             </select>");                                         
    // -For Monthly Report

    $smarty->assign('sReportRadTech3',
                                            "<select name=\"radtech_choices2\" id=\"radtech_choices2\">
                                                 <option value=\"all\">--All Rad Tech--</option>
                                                 $options_radtech
                                             </select>");   

    $smarty->assign('sReportSelectedDepartment',
                                            "<select name=\"department_choices\" id=\"department_choices\">
                                                 <option value=\"all\">--All Department--</option>
                                                 <option value=\"164\">X-ray Department</option>
                                                 <option value=\"165\">Ultrasound Department</option>
                                                 <option value=\"167\">CT scan Department</option>
                                             </select>");         

    // 

	$smarty->assign('sFromDateInput','<input name="fromdt" id="from_date" type="text" size="8"
													value="">');
	$smarty->assign('sFromDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="from_date_trigger" align="absmiddle" style="cursor:pointer">');

	$smarty->assign('sToDateInput','<input name="todt" id="to_date" type="text" size="8"
													value="">');
	$smarty->assign('sToDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="to_date_trigger" align="absmiddle" style="cursor:pointer">');

	$smarty->assign('sFromDateInput2','<input name="fromdt2" id="from_date2" type="text" size="8"
													value="">');
	$smarty->assign('sFromDateIcon2','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="from_date_trigger2" align="absmiddle" style="cursor:pointer">');
	$smarty->assign('sToDateInput2','<input name="todt2" id="to_date2" type="text" size="8"
													value="">');
	$smarty->assign('sToDateIcon2','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="to_date_trigger2" align="absmiddle" style="cursor:pointer">');

	
    $smarty->assign('sFromDateInput5','<input name="fromdt5" id="from_date5" type="text" size="8"
                                                    value="">');
    $smarty->assign('sFromDateIcon5','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="from_date_trigger5" align="absmiddle" style="cursor:pointer">');

    $smarty->assign('sToDateInput5','<input name="todt5" id="to_date5" type="text" size="8"
                                                    value="">');
    $smarty->assign('sToDateIcon5','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="to_date_trigger5" align="absmiddle" style="cursor:pointer">');
    

    $smarty->assign('sFromDateInput6','<input name="fromdt6" id="from_date6" type="text" size="8"
                                                    value="">');
    $smarty->assign('sFromDateIcon6','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="from_date_trigger6" align="absmiddle" style="cursor:pointer">');

    $smarty->assign('sToDateInput6','<input name="todt6" id="to_date6" type="text" size="8"
                                                    value="">');
    $smarty->assign('sToDateIcon6','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="to_date_trigger6" align="absmiddle" style="cursor:pointer">');

    #added by cha 07-14-09
	$smarty->assign('sExportAsPdf','<input type="radio" name="exp_type" id="exp_type" value="PDF">');
	$smarty->assign('sExportAsExcel','<input type="radio" name="exp_type" id="exp_type" value="EXCEL">');
	#end cha

	#added by VAN 11-11-09
	$smarty->assign('sOrderBy','<input type="checkbox" name="is_alphabetical" id="is_alphabetical" value="1" />');
    
    $smarty->assign('sOrderBy2','<input type="checkbox" name="is_alphabetical2" id="is_alphabetical2" value="1" />');

	#Added by Cherry 11-12-10
	$smarty->assign('sFilterImp', '<input type="checkbox" name="is_filter_imp" id="is_filter_imp" value="1" />');
	$smarty->assign('sImpression','<input name="impression" id="impression" type="text" size="50" value="">');
	#End Cherry

	#added by VAN 06-20-08
	#classification
	$smarty->assign('sFromDateInput3','<input name="fromdt3" id="from_date3" type="text" size="8"
													value="">');
	$smarty->assign('sFromDateIcon3','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="from_date_trigger3" align="absmiddle" style="cursor:pointer">');

	$smarty->assign('sToDateInput3','<input name="todt3" id="to_date3" type="text" size="8"
													value="">');
	$smarty->assign('sToDateIcon3','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="to_date_trigger3" align="absmiddle" style="cursor:pointer">');

	#added by VAN 08-02-08
	$smarty->assign('sFromDateInput4','<input name="req_date" id="req_date" type="text" size="8"
													value="">');
	$smarty->assign('sFromDateIcon4','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="req_date_trigger" align="absmiddle" style="cursor:pointer">');


	$smarty->assign('sReportRadTech','<input name="radtech" id="radtech" type="text" size="50" value="">');
	#findings
	$var_arr = array(
		"var_rid"=>"rid",
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

	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$person_name.'" style="font:bold 12px Arial;" readonly>');

	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="cursor:pointer;font:bold 11px Arial" value="Clear" onclick="clearEncounter()" disabled>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" readonly>'.$orderaddress.'</textarea>');

	$smarty->assign('sRID','<input class="segInput" id="rid" name="rid" type="hidden" size="10" value="'.$rid.'" style="font:bold 12px Arial;" readonly>');

	$var_arr = array(
		"var_rid"=>"rid",
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

	$smarty->assign('sSelectPatient','<img type="image" name="select-enc" id="select-enc" src="'.$root_path.'images/btn_encounter_small.gif" border="0" style="cursor:pointer;"
			onclick="return overlib(
				OLiframeContent(\''.$root_path.'modules/registration_admission/seg-select-patient.php?$var_qry&var_include_enc=0\', 700, 400, \'fSelEnc\', 1, \'auto\'),
					WIDTH,700, TEXTPADDING,0, BORDER,0,
					STICKY, SCROLL, CLOSECLICK, MODAL,
					CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
					CAPTIONPADDING,4,
					CAPTION,\'Select registered person\',
					MIDX,0, MIDY,0,
					STATUS,\'Select registered person\');"
			onmouseout="nd();">');

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
								singleClick : true,
								step : 1
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
								singleClick : true,
								step : 1
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
                        
                        Calendar.setup (
                            {
                                inputField : \"from_date5\",
                                ifFormat : \"%Y-%m-%d\",
                                daFormat : \"$phpfd\",
                                showsTime : false,
                                button : \"from_date_trigger5\",
                                singleClick : true,
                                step : 1
                            }
                        );
                        Calendar.setup (
                            {
                                inputField : \"to_date5\",
                                ifFormat : \"%Y-%m-%d\",
                                daFormat : \"$phpfd\",
                                showsTime : false,
                                button : \"to_date_trigger5\",
                                singleClick : true,
                                step : 1
                            }
                        );
						
						Calendar.setup (
                            {
                                inputField : \"from_date6\",
                                ifFormat : \"%Y-%m-%d\",
                                daFormat : \"$phpfd\",
                                showsTime : false,
                                button : \"from_date_trigger6\",
                                singleClick : true,
                                step : 1
                            }
                        );
                        Calendar.setup (
                            {
                                inputField : \"to_date6\",
                                ifFormat : \"%Y-%m-%d\",
                                daFormat : \"$phpfd\",
                                showsTime : false,
                                button : \"to_date_trigger6\",
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

	<input type="hidden" name="tpl" id="tpl" value="<?= $last ?>" />
	<input type="hidden" name="sc" id="sc" value="<?= $last2 ?>" />
	<input type="hidden" name="notc" id="notc" value="<?= $last3 ?>" />

	<input type="hidden" id="encounter_nr" name="encounter_nr" value="<?=$encounter_nr?>">
	<input type="hidden" name="discount" id="discount" value="<?=$discount?>" >
	<input type="hidden" id="discountid" name="discountid" value="<?php if ($info["discountid"]) echo $info["discountid"]; else $discountid;?>">

	<!-- added by VAN 06-20-08 -->
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

	<!-- -->

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
			//document.getElementById('report_kind').focus();
		}

		function prufform(){
			var d = document.inputform;
			var mode = document.getElementById('report_type').value;
            
					//alert('here1= '+d.fromHour.value);
						//	alert('here1= '+d.toHour.value);
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
					/*
					if ((d.patient_type.value==1) || (d.patient_type.value==2)){
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
									if (parseInt(d.fromHour.value)>parseInt(d.toHour.value)){
										alert("Starting time should be earlier than the ending time");
										d.fromHour.focus();
										return false;
									}else if (parseInt(d.fromHour.value)==parseInt(d.toHour.value)){
										if (parseInt(d.fromMin.value)>parseInt(d.toMin.value)){
											alert("Starting time should be earlier than the ending time");
											d.fromMin.focus();
											return false;
										}
									}
								}
								}
						 }//if fromdate == todate
					 } //if date is not null
				}// if patient type is ER or IPD
				*/
				//-------------------

			}else if (mode==2){
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
			}else if (mode==3){
				//classification
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

			}else if (mode==4){
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
						//if (parseInt(d.fromHour.value)>parseInt(d.toHour.value)){
						if (d.fromHour.value>d.toHour.value){
							alert("Starting time should be earlier than the ending time");
							d.fromHour.focus();
							return false;
						}else if //(parseInt(d.fromHour.value)==parseInt(d.toHour.value)){
								(d.fromHour.value==d.toHour.value){
							//if (parseInt(d.fromMin.value)>parseInt(d.toMin.value)){
							if (d.fromMin.value>d.toMin.value){
								alert("Starting time should be earlier than the ending time");
								d.fromMin.focus();
								return false;
							}
						}
					}
				}
			}else if (mode==5){
               if ((($('from_date5').value==' ')&&($('to_date5').value!=' ')) || ((isNaN($('from_date5').value)==false)&&(isNaN($('to_date5').value)==true))) {
                    alert("Enter the starting date of the report.");
                    $('from_date5').focus();
                    return false;
                }

                if ((($('from_date5').value!=' ')&&($('to_date5').value==' ')) || ((isNaN($('from_date5').value)==true)&&(isNaN($('to_date5').value)==false))) {
                    alert("Enter the end date of the report.");
                    $('to_date5').focus();
                    return false;
                }

                if ($('from_date5').value > $('to_date5').value){
                    alert("Starting date should be earlier than the ending date");
                    $('from_date5').focus();
                    return false;
                }
                
                if (($('from_date5').value==' ')&&($('to_date5').value==' ')){
                    alert("Enter a report period.");
                    $('from_date5').focus();
                    return false; 
                }    
            }

			return true;
		}

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
				var is_alphabetical;
				var grpview;

				//added by VAN 04-29-2011
				var doctor_nr = $('doctor_choices').value;

				if (document.getElementById('is_alphabetical').checked)
					is_alphabetical =1;
				else
					is_alphabetical =0;

				//added by VAN 06-04-08
				var pat_type = document.getElementById('patient_type').value;

				var user = document.getElementById('user').value;

				if (isNaN(fromdate)==false){
					fromdate = 0;
				}

				if (isNaN(todate)==false)
					todate = 0;

				grpview = 1;
				//alert(val);
				if (val){
					if (document.getElementById('viewgrp').checked){
						//grpview = 1;
						//alert('not detailed');
						window.open("seg-radio-report-pdf.php?report_kind="+rpt_kind+"&report_group="+rpt_group+"&report_class="+rpt_class+"&fromdate="+fromdate+"&todate="+todate+"&grpview="+grpview+"&user="+user+"&pat_type="+pat_type+"&doctor_nr="+doctor_nr+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
					}else{
						//grpview = 0;
						//alert('detailed');
						if (val==1){
							window.open("seg-radio-report-detailed-pdf.php?report_kind="+rpt_kind+"&report_group="+rpt_group+"&report_class="+rpt_class+"&fromdate="+fromdate+"&todate="+todate+"&grpview="+grpview+"&user="+user+"&pat_type="+pat_type+"&doctor_nr="+doctor_nr+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
						}else{
							var req_date = document.getElementById('req_date').value;
							var fromhour = document.getElementById('fromHour').value;
							var frommin = document.getElementById('fromMin').value;
							var frommer = document.getElementById('fromMeridian').value;
							var fromtime = fromhour+":"+frommin+":00 "+frommer;

							var tohour = document.getElementById('toHour').value;
							var tomin = document.getElementById('toMin').value;
							var tomer = document.getElementById('toMeridian').value;
							var totime = tohour+":"+tomin+":00 "+tomer;

							var radtech = document.getElementById('radtech').value;

							var rpt_group2 = document.getElementById('report_group2').value;

							var pat_type = document.getElementById('patient_type2').value;

							//alert("radtech = "+radtech);
							window.open("seg-radio-patient-report-pdf.php?req_date="+req_date+"&fromtime="+fromtime+"&totime="+totime+"&rpt_group="+rpt_group2+"&user="+user+"&radtech="+radtech+"&pat_type="+pat_type+"&is_alphabetical="+is_alphabetical+"&doctor_nr="+doctor_nr+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
						}
					}
					//alert(rpt_group+", "+rpt_class+", "+fromdate+", "+todate);
					//window.open("seg-lab-report-pdf.php?report_kind="+rpt_kind+"&report_group="+rpt_group+"&report_class="+rpt_class+"&fromdate="+fromdate+"&todate="+todate+"&grpview="+grpview+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
				}else{
					//alert('here');
					//laboratory report based on their existing format
					//window.open("seg-radio-report-format-pdf.php?fromdate="+fromdate+"&todate="+todate+"&pat_type="+pat_type+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");

					window.open("seg-radio-report-format-pdf.php?fromdate="+fromdate+"&todate="+todate+"&pat_type="+pat_type+"&report_kind="+rpt_kind+"&report_group="+rpt_group+"&report_class="+rpt_class+"&doctor_nr="+doctor_nr+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
				}
			}
		}

		//added by VAN 06-20-08
		function viewClassReport(){
			var bol = prufform();
			var d = document.inputform;
			if (bol){
				var rpt_cases = document.getElementById('report_cases').value;
				var fromdate = document.getElementById('from_date3').value;
				var todate = document.getElementById('to_date3').value;
				var tpl = document.getElementById('tpl').value;
				var sc = document.getElementById('sc').value;
				var not = document.getElementById('notc').value;

				var user = document.getElementById('user').value;

				if (isNaN(fromdate)==false){
					fromdate = 0;
				}

				if (isNaN(todate)==false)
					todate = 0;

				window.open("seg-radio-report-cases-pdf.php?fromdate="+fromdate+"&todate="+todate+"&rpt_cases="+rpt_cases+"&tpl="+tpl+"&sc="+sc+"&not="+not+"&showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
			}
		}

		function viewStatistics(){
			var bol = prufform();
			if (bol){
				var fromdate = document.getElementById('from_date2').value;
				var todate = document.getElementById('to_date2').value;
				var statkind = document.getElementById('report_statkind').value;

				for (var i=0; i < document.inputform.exp_type.length; i++)
				 {
				 if (document.inputform.exp_type[i].checked)
						{
						var reptkind = document.inputform.exp_type[i].value;
						//alert('value='+rad_val);
						}
				 }


				if (isNaN(fromdate)==false){
					fromdate = 0;
				}

				if (isNaN(todate)==false)
					todate = 0;

				if (statkind==1){
					window.open("seg-radio-stat-report-pdf.php?fromdate="+fromdate+"&todate="+todate+"&showBrowser=1","viewPatientResultBySection","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
				}else if(statkind==2 && reptkind=='PDF'){
										window.open("seg-radio-stat-report-bydepartment-pdf.php?fromdate="+fromdate+"&todate="+todate+"&showBrowser=1","viewPatientResultByService","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
								}else if(statkind==2 && reptkind=='EXCEL'){
										window.open("seg-radio-stat-report-bydepartment-excel.php?fromdate="+fromdate+"&todate="+todate)
								}else if (statkind==3){
					window.open("seg-radio-stat-report-serv-pdf.php?fromdate="+fromdate+"&todate="+todate+"&showBrowser=1","viewPatientResultByService","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
				}else if (statkind==4){
					window.open("seg-radio-stat-report-film-pdf.php?fromdate="+fromdate+"&todate="+todate+"&showBrowser=1","viewPatientResultByFilm","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
				}
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

		function selectMode(val){
			//alert('val = '+val);
			if (val==1){
				//status
				document.getElementById('mode_status').style.display='';
				document.getElementById('mode_stat').style.display='none';
				document.getElementById('mode_class').style.display='none';
				document.getElementById('mode_results').style.display='none';
                document.getElementById('mode_logbook').style.display='none';
                document.getElementById('mode_monthlyreport').style.display='none';

				//reset values
				//document.getElementById('viewgrp').checked=false;
				/*document.getElementById('report_kind').value='all';
				document.getElementById('report_group').value='all';*/
				document.getElementById('from_date').value=' ';
				document.getElementById('to_date').value=' ';
				/*document.getElementById('report_class').value='all';
				document.getElementById('patient_type').value = 0;*/
				$('doctor_choices').value = 0;

			}else if (val==2){
				//statistics
				document.getElementById('mode_status').style.display='none';
				document.getElementById('mode_stat').style.display='';
				document.getElementById('mode_class').style.display='none';
				document.getElementById('mode_results').style.display='none';
                document.getElementById('mode_logbook').style.display='none';
                document.getElementById('mode_monthlyreport').style.display='none';
                
				//reset values
				//document.getElementById('report_kind2').value='all';
				//document.getElementById('report_group2').value='all';
				document.getElementById('from_date2').value=' ';
				document.getElementById('to_date2').value=' ';
				//document.getElementById('exp_pdf').style.display='';
				//document.getElementById('exp_excel').style.display='';
			}else  if (val==3){
				//classification
				document.getElementById('mode_status').style.display='none';
				document.getElementById('mode_stat').style.display='none';
				document.getElementById('mode_class').style.display='';
				document.getElementById('mode_results').style.display='none';
                document.getElementById('mode_logbook').style.display='none';
                document.getElementById('mode_monthlyreport').style.display='none';

				document.getElementById('from_date3').value=' ';
				document.getElementById('to_date3').value=' ';

			}else  if (val==4){
				//findings
				document.getElementById('mode_status').style.display='none';
				document.getElementById('mode_stat').style.display='none';
				document.getElementById('mode_class').style.display='none';
				document.getElementById('mode_results').style.display='';
                document.getElementById('mode_logbook').style.display='none';
                document.getElementById('mode_monthlyreport').style.display='none';

				document.getElementById('patient_type2').value = 0;
                
            }else  if (val==5){    
                document.getElementById('mode_status').style.display='none';
                document.getElementById('mode_stat').style.display='none';
                document.getElementById('mode_class').style.display='none';
                document.getElementById('mode_results').style.display='none';
                document.getElementById('mode_logbook').style.display='';
                document.getElementById('mode_monthlyreport').style.display='none';

                document.getElementById('patient_type2').value = 0;
                
                document.getElementById('from_date5').value=' ';
                document.getElementById('to_date5').value=' ';

			}else  if (val==6){
				//monthly report
				document.getElementById('mode_status').style.display='none';
				document.getElementById('mode_stat').style.display='none';
				document.getElementById('mode_class').style.display='none';
				document.getElementById('mode_results').style.display='none';
                document.getElementById('mode_logbook').style.display='none';
                document.getElementById('mode_monthlyreport').style.display='';
                
            }else{
				document.getElementById('mode_status').style.display='none';
				document.getElementById('mode_stat').style.display='none';
				document.getElementById('mode_class').style.display='none';
				document.getElementById('mode_results').style.display='none';
				document.getElementById('mode_monthlyreport').style.display='none';
			}
		}
        
        function viewConsumptionReport(){

        	var fromdate = document.getElementById('from_date').value;
			var todate = document.getElementById('to_date').value;
			var patient_type = document.getElementById('patient_type').value;
			var report_group = document.getElementById('report_group').value;
			var doctor_nr = document.getElementById('doctor_choices').value;

        	if(fromdate.trim() && todate.trim() && doctor_nr){
        		window.open('seg-radio-consumption-report-pdf.php?from='+fromdate+"&to="+todate+"&patient_type="+patient_type+"&dr_nr="+doctor_nr+"&dept_nr="+report_group,"viewLogbook","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
        	}
        }

        function viewLogbookReport(){
            var bol = prufform();
            if (bol){
                var fromdate = $('from_date5').value;
                var todate = $('to_date5').value;
                var request_status = $('request_status').value;
                var pat_type = $('patient_type3').value;
                var rpt_group = $('report_group3').value;
                var radtech = $('radtech_choices').value;
                var doctor_nr = $('doctor_choices2').value;
                var status_report = $('report_kind3').value;
                
                var is_alphabetical;
                if ($('is_alphabetical2').checked)
                    is_alphabetical =1;
                else
                    is_alphabetical =0;
                
                //alert(fromdate+", "+todate+", "+request_status+", "+pat_type+", "+rpt_group+", "+radtech+", "+doctor_nr);
                window.open("seg-radio-logbook-pdf.php?fromdate="+fromdate+"&todate="+todate+"&request_status="+request_status+"&pat_type="+pat_type+"&rpt_group="+rpt_group+"&radtech="+radtech+"&doctor_nr="+doctor_nr+"&is_alphabetical="+is_alphabetical+"&status_report="+status_report+"&showBrowser=1","viewLogbook","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
            }    
        }

        function viewMonthlyreportReport(){
        	var bol = prufform();
        	if (bol){
        		var fromdate = $('from_date6').value;
                var todate = $('to_date6').value;
                // var radtech = $('radtech_choices2').value;
                var doctor_nr = $('doctor_choices3').value;
                var department = $('department_choices').value;

                // window.open("seg-radio-monthlyreport-pdf.php?fromdate="+fromdate+"&todate="+todate+"&radtech="+radtech+"&doctor_nr="+doctor_nr+"&department="+department+"&showBrowser=1","viewMonthlyreport","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
                window.open("seg-radio-monthlyreport-pdf.php?fromdate="+fromdate+"&todate="+todate+"&doctor_nr="+doctor_nr+"&department="+department+"&showBrowser=1","viewMonthlyreport","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
        	}
        }

	</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

	$smarty->assign('sHiddenInputs',$sTemp);
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
	#$smarty->assign('sContinueButton','<input type="image" '.createLDImgSrc($root_path,'continue.gif','0','left').' align="absmiddle">');
	#added by VAN 06-20-08
	$smarty->assign('sResultsButton','<img name="viewreport" id="viewreport" onClick="viewReport(2);" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showresults.gif','0','left') . ' border="0">');
	$smarty->assign('sContinueButton2','<img name="viewreport2" id="viewreport2" onClick="viewClassReport();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif','0','left') . ' border="0">');
	#-------

	$smarty->assign('sContinueButton','<img name="viewreport" id="viewreport" onClick="viewConsumptionReport();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif','0','left') . ' border="0">');
	$smarty->assign('sReportButton','<img name="viewlabreport" id="viewlabreport" onClick="viewReport(0);" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport2.gif','0','left') . ' border="0">');

	$smarty->assign('sStatButton','<img name="viewStat" id="viewStat" onClick="viewStatistics();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showstatreport.gif','0','left') . ' border="0">');
    
    $smarty->assign('sLogbookButton','<img name="viewLogbook" id="viewLogbook" onClick="viewLogbookReport();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif','0','left') . ' border="0">');

    $smarty->assign('sMonthlyreportButton','<img name="viewMonthlyreport" id="viewMonthlyreport" onClick="viewMonthlyreportReport();" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif','0','left') . ' border="0">');

	# Assign the form template to mainframe
	$smarty->assign('sMainBlockIncludeFile','radiology/form_report.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>