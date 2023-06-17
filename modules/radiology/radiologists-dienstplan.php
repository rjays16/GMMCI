<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables[]='departments.php';
define('LANG_FILE','doctors.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_lab_user';
require_once($root_path.'include/inc_front_chain_lang.php');

setcookie(username,"");
setcookie(ck_plan,"1");
if($dept=="") $dept="plast";
if($pmonth=="") $pmonth=date('n');
if($pyear=="") $pyear=date('Y');
$thisfile=basename(__FILE__);

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
$dept_obj->preloadDept($dept_nr);

#echo "radiologists-dienstplan.php : dept_nr = '".$dept_nr."' <br> \n";
#echo "radiologists-dienstplan.php : 1 dept_obj->sql = '".$dept_obj->sql."' <br> \n";
 
require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;
$dutyplan=&$pers_obj->getDOCDutyplan($dept_nr,$pyear,$pmonth);


$firstday=date("w",mktime(0,0,0,$pmonth,1,$pyear));

$maxdays=date("t",mktime(0,0,0,$pmonth,1,$pyear));

switch($retpath)
{
	case "menu": $rettarget='radiolog.php'.URL_APPEND; break;
	case "qview": $rettarget='doctors-dienst-schnellsicht.php'.URL_APPEND.'&hilitedept='.$dept_nr; break;
	default: $rettarget="javascript:window.history.back()";
}

# Prepare page title
 $sTitle = "$LDDoctors::$LDDutyPlan::";
 $LDvar=$dept_obj->LDvar();
# echo "radiologists-dienstplan.php : 2 dept_obj->sql = '".$dept_obj->sql."' <br> \n";
# echo "radiologists-dienstplan.php : LDvar = '".$LDvar."' <br> \n";
 if(isset($$LDvar)&&$$LDvar) $sTitle = $sTitle.$$LDvar;
   else $sTitle = $sTitle.$dept_obj->FormalName();

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 $smarty->assign('sToolbarTitle',$sTitle);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('docs_dutyplan.php','show','$rows')");

 # href for close button
 $smarty->assign('breakfile',$rettarget);

 # Window bar title
 $smarty->assign('sWindowTitle',$sTitle);

 # Collect extra javascript

 ob_start();

?>

<script language="javascript">

  var urlholder;
  var infowinflag=0;

function popinfo(l)
{
	w=window.screen.width;
	h=window.screen.height;
	ww=400;
	wh=400;
//	urlholder="doctors-dienstplan-popinfo.php<?php echo URL_REDIRECT_APPEND ?>&nr="+l+"&dept_nr=<?php echo $dept_nr ?>&route=validroute&user=<?php echo $aufnahme_user.'"' ?>;
	urlholder="<?php echo $root_path; ?>modules/doctors/doctors-dienstplan-popinfo.php<?php echo URL_REDIRECT_APPEND ?>&nr="+l+"&dept_nr=<?php echo $dept_nr ?>&route=validroute&user=<?php echo $aufnahme_user;?>";
	infowin<?php echo $sid ?>=window.open(urlholder,"infowin<?php echo $sid ?>","width=" + ww + ",height=" + wh +",menubar=no,resizable=yes,scrollbars=yes");
	window.infowin<?php echo $sid ?>.moveTo((w/2)+20,(h/2)-(wh/2));

}
</script>

<?php 

 $sTemp=ob_get_contents();
 ob_end_clean();
 $smarty->append('JavaScript',$sTemp);

# $smarty->assign('LDStandbyPerson',$LDDoc1);   # burn commented : July 20, 2007
# $smarty->assign('LDOnCall',$LDDoc2);   # burn commented : July 20, 2007

# Prepare the month links
# Previous month
$sBuffer = '<a href="'.$thisfile.URL_APPEND.'&retpath='.$retpath.'&dept_nr='.$dept_nr.'&pmonth=';

if ($pmonth==1) $sBuffer = $sBuffer.'12'.'&pyear='.($pyear-1).'">';
	else $sBuffer = $sBuffer.($pmonth-1).'&pyear='.$pyear.'">';
if ($pmonth==1) $sBuffer = $sBuffer.$monat[12];
	else $sBuffer = $sBuffer.$monat[$pmonth-1];
 $smarty->assign('sPrevMonth',$sBuffer.'</a>');

 # This month
$smarty->assign('sThisMonth',ucfirst($monat[$pmonth]).'&nbsp;&nbsp;'.$pyear);

# Next month
$sBuffer ='<a href="'.$thisfile.URL_APPEND.'&retpath='.$retpath.'&dept_nr='.$dept_nr.'&pmonth=';
if ($pmonth==12) $sBuffer = $sBuffer.'1'.'&pyear='.($pyear+1).'">';
	else $sBuffer = $sBuffer.($pmonth+1).'&pyear='.$pyear.'">';
if ($pmonth==12) $sBuffer = $sBuffer.$monat[1];
	else $sBuffer = $sBuffer.$monat[$pmonth+1];

$smarty->assign('sNextMonth',$sBuffer.'</a>');

# Assign control links
#$smarty->assign('sNewPlan',"<a href=\"labor_test_request_pass.php".URL_APPEND."&target=dutyplan&dept_nr=$dept_nr&pmonth=$pmonth&pyear=$pyear&retpath=$retpath\"><img ".createLDImgSrc($root_path,'newplan.gif','0')."  alt=\"$LDNewPlan\"></a>");   # burn commented : July 20, 2007
$smarty->assign('sNewPlan',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=segradioDOCScheduler&user_origin=radiology&dept_nr=$dept_nr&pmonth=$pmonth&pyear=$pyear&retpath=$retpath\"><img ".createLDImgSrc($root_path,'newplan.gif','0')."  alt=\"$LDNewPlan\"></a>");   # burn added : July 20, 2007

$smarty->assign('sCancel',"<a href=\"$rettarget\"><img ".createLDImgSrc($root_path,'close2.gif','0')." alt=\"$LDClosePlan\"></a>");
/*
 # Buffer form

 ob_start();
for ($i=1,$n=0,$wd=$firstday;$i<=$maxdays;$i++,$n++,$wd++){
	//$wd=weekday($i,$pmonth,$pyear);
	switch ($wd){
		//case 6: $backcolor="bgcolor=#ffffcc";break;
		//case 0: $backcolor="bgcolor=#ffff00";break;
		//default: $backcolor="bgcolor=white";
		case 6: $backcolor='class="saturday"';break;
		case 0: $backcolor='class="sunday"';break;
		default: $backcolor='class="weekday"';;
	}
	
	$aelems=unserialize($dutyplan['duty_1_txt']);
	$relems=unserialize($dutyplan['duty_2_txt']);
	$a_pnr=unserialize($dutyplan['duty_1_pnr']);
	$r_pnr=unserialize($dutyplan['duty_2_pnr']);

	echo '
	<tr >
	<td  height=5 '.$backcolor.'>'.$i.'
	</td>
	<td height=5 '.$backcolor.'>';
	//if (!$wd) echo '<font color=red>';
	echo $LDShortDay[$wd].'
	</td>
	<td height=5 '.$backcolor.'>';
	echo '&nbsp;<a href="javascript:popinfo(\''.$a_pnr['ha'.$n].'\')">'.$aelems['a'.$n].'</a>
	</td>
	<td height=5 '.$backcolor.'>';
	echo '&nbsp;<a href="javascript:popinfo(\''.$r_pnr['hr'.$n].'\')">'.$relems['r'.$n].'</a>
	</td>
	</tr>';
	if ($wd==6)  $wd=-1;
}

$sTemp = ob_get_contents();
 ob_end_clean();
*/

# burn added : July 20, 2007
$smarty->assign('segDutyPlanRadiologyMode',TRUE);

	$radioElems=unserialize($dutyplan['duty_1_txt']);   # burn added : July 20, 2007

	$list = $dept_obj->getAncestorChildrenDept($dept_nr);   # burn added : July 20, 2007
	$radiology_sections = split(",",$list);

	ob_start();
	foreach($radiology_sections as $r_section_nr){
		$r_sub_section = $dept_obj->getDeptAllInfo($r_section_nr);
	
		echo "
					<tr bgcolor='#FFFFFF' style='font-size:16px'>
						<td colspan='6' bgcolor='#88B9EE'>
						<span style='font-family: Arial, Helvetica, sans-serif;font-weight: bold;color: #FFFFFF;'>
							".$r_sub_section['name_formal']."
						</span></td>
					</tr>
		";
		echo "
					<tr class='submenu2_titlebar' style='font-size:16px' align='center'>
						<td colspan='2' bgcolor='#C3DCF8'>
							<span style='font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: #003366; font-size: 12px;'>
								Resident-in-Charge 1
							</span>
						</td>
						<td colspan='2' bgcolor='#C3DCF8'>
							<span style='font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: #003366; font-size: 12px;'>
								Resident-in-Charge 2
							</span>
						</td>
						<td colspan='2' bgcolor='#C3DCF8'>
							<span style='font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: #003366; font-size: 12px;'>
								Resident-in-Charge 3
							</span>
						</td>

					</tr>
		";	
		$docA='';
		if ($pers_obj->loadPersonellData($radioElems["ID_docA".$r_section_nr])){
			$f_name = $pers_obj->personell_data['name_first'];
			$l_name = $pers_obj->personell_data['name_last'];
			$docA ='&nbsp;<a href="javascript:popinfo(\''.$radioElems["ID_docA".$r_section_nr].'\')">'.$l_name.', '.$f_name.'</a>';
		}
		$docB='';
		if ($pers_obj->loadPersonellData($radioElems["ID_docB".$r_section_nr])){
			$f_name = $pers_obj->personell_data['name_first'];
			$l_name = $pers_obj->personell_data['name_last'];
			$docB ='&nbsp;<a href="javascript:popinfo(\''.$radioElems["ID_docB".$r_section_nr].'\')">'.$l_name.', '.$f_name.'</a>';
		}
		$docC='';
		if ($pers_obj->loadPersonellData($radioElems["ID_docC".$r_section_nr])){
			$f_name = $pers_obj->personell_data['name_first'];
			$l_name = $pers_obj->personell_data['name_last'];
			$docC ='&nbsp;<a href="javascript:popinfo(\''.$radioElems["ID_docC".$r_section_nr].'\')">'.$l_name.', '.$f_name.'</a>';
		}
			echo "
						<tr class='weekday' style='font-size:16px'>
							<td colspan='2' height=5>".$docA."</td>
							<td colspan='2' height=5>".$docB."</td>
							<td colspan='2' height=5>".$docC."</td>
						</tr>
			";
	}# end of foreach loop

	$sTemp = ob_get_contents();
	ob_end_clean();

# Assign the duty plan rows to sub frame template

$smarty->assign('sDutyRows',$sTemp);

#edited by VAN 03-24-08
 #$smarty->assign('sMainBlockIncludeFile','common/duty_plan.tpl');
 $smarty->assign('sMainBlockIncludeFile','radiology/duty_plan.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>