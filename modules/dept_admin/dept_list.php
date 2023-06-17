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
$lang_tables=array('departments.php');
define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/inc_editor_fx.php');

$breakfile=$root_path.'modules/system_admin/edv-system-admi-welcome.php'.URL_APPEND	;

if($pday=='') $pday=date('d');
if($pmonth=='') $pmonth=date('m');
if($pyear=='') $pyear=date('Y');
$t_date=$pday.'.'.$pmonth.'.'.$pyear;

$dept_obj=new Department;

$deptarray=$dept_obj->getAllActiveSort('name_formal');

#----------------
#echo "dept_list = ";
#print_r ($deptarray);
#echo "/n<br>";
#---------------
# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$LDDepartment :: $LDList");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_list.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDDepartment :: $LDList");

 # Buffer page output
 ob_start();
?>

<style type="text/css" name="formstyle">
td.pblock{ font-family: verdana,arial; font-size: 12}

div.box { border: solid; border-width: thin; width: 100% }

div.pcont{ margin-left: 3; }

</style>

<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output
ob_start();

?>

<table border=0 cellpadding=3>
  <tr class="wardlisttitlerow">
<!-- 	<td bgcolor="#e9e9e9"></td>

 -->    <td class=pblock align=center><?php echo $LDDept ?></td>
    <td class=pblock align=center><?php echo $LDDescription ?></td>
 </tr> 
  
<?php
while(list($x,$dept)=each($deptarray)){
?>
  <tr>
<!-- 	<td bgcolor="#e9e9e9"><img <?php echo createComIcon($root_path,'arrow_blueW.gif','0'); ?>></td>
 -->    <td class=pblock  bgColor="#eeeeee"><a href="dept_info.php<?php echo URL_APPEND."&dept_nr=".$dept['nr']; ?>">
 <?php 
		if(isset($$dept['LD_var'])&&!empty($$dept['LD_var'])) echo $$dept['LD_var'];
				#else echo $dept['name_formal'];
				#-----------edit 02-22-07---------------		
				else{ 
					#echo "dept_type = ".$dept['type'];
					echo $dept['name_formal'];

/*
					echo $dept['admit_outpatient']." : ";
					if (($dept['admit_inpatient'] == 1) && ($dept['type']==1)){
						echo $dept['name_formal']." - IPD";
					}elseif (($dept['admit_inpatient'] == 0) && ($dept['type']==1)) {
						echo $dept['name_formal']." - OPD";
					}elseif (($dept['admit_inpatient'] == 0) && ($dept['type']==2)){
						echo $dept['name_formal']." - Non-Medical";
					}elseif (($dept['admit_inpatient'] == 0) && ($dept['type']==3)){
						echo $dept['name_formal']." - News";
					}
*/
				}	
				if ($dept['type']==1){
					if (($dept['admit_inpatient'] == 1)&&($dept['admit_outpatient'] == 1))
						echo " (IPD/OPD)";
					if (($dept['admit_inpatient'] == 1)&&($dept['admit_outpatient'] == 0))
						echo " (IPD)";
					if (($dept['admit_inpatient'] == 0)&&($dept['admit_outpatient'] == 1))
						echo " (OPD)";
				}elseif ($dept['type']==2){
					echo " (Non-Medical)";
				}elseif ($dept['type']==3){
					echo " (News)";
				}

			 #-----------edit 02-22-07---------------	
 ?>
 </a> </td>
    <td class=pblock  bgColor="#eeeeee"><?php echo deactivateHotHtml(nl2br($dept['description'])); ?> </td>
 </tr> 
<?php
}
 ?>
 
</table>

<p>

<a href="javascript:history.back()"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a>

<?php

$sTemp = ob_get_contents();
 ob_end_clean();

# Assign the data  to the main frame template

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
