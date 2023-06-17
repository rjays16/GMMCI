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
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','prompt.php');
$local_user='ck_prod_order_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path."modules/pharmacy/seg-pharma-order-functions.php".URL_APPEND."&userck=$userck";

if(empty($pday)) $pday=date('j');
if(empty($pmonth)) $pmonth=date('n');
if(empty($pyear)) $pyear=date('Y');
$abtarr=array();
$abtname=array();
$datum=date('d.m.Y');

# Load the medical department list
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department();
if ($_GET['dept_nr'])
    $dept_nr=$_GET['dept_nr'];

$res = $dept_obj->getDeptAllInfo($dept_nr);
#$dept=$prod_obj->getAllPharmaAreas();

$title=$LDSelectDept;
# Set forward file
/*
switch($target){
    case 'catalog': $fileforward=$root_path."modules/products/products-bestellkatalog-edit.php".URL_APPEND."&cat=$cat";
                            break;
    default : $fileforward=$root_path."modules/products/products-bestellung.php".URL_APPEND."&cat=$cat";
}
*/
$fileforward = $root_path.'modules/pharmacy/apotheke-pass.php'. URL_APPEND."&userck=$userck".'&target='.$target;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"Pharmacy::Select Pharmacy Area");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_select.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',$title);

$smarty->assign('sMascotImg','<img '.createMascot($root_path,'mascot1_r.gif','0','bottom').' align="absmiddle">');
$smarty->assign('LDPlsSelectDept',$LDPlsSelectDept);

 # Buffer department rows output
 ob_start();

echo '
<tr class="wardlistrow1">
    <td>&nbsp;<strong>All areas (Requires access privelege)</strong></td>
    <td width="1">
        <a href="'.$fileforward.'&area=all">
            <img '.createLDImgSrc($root_path,'ok_small.gif','0','absmiddle').' alt="'.$LDShowActualPlan.'" >
        </a>
    </td>
</tr>';
$toggler=1;
while($res=$dept_obj->FetchRow()){
        
    $bold='';
    $boldx='';
    #if($hilitedept==$v['nr'])     { echo '<tr bgcolor="yellow">'; $bold="<font color=\"red\" size=2><b>";$boldx="</b></font>"; } 
    #else
        if ($toggler==0) 
            { echo '<tr class="wardlistrow1">'; $toggler=1;}
                else { echo '<tr class="wardlistrow2">'; $toggler=0;}
    echo '<td>&nbsp;'.$bold;
    echo $res["name_formal"];
    echo $boldx.'&nbsp;</td>';
    echo '<td width="1"><a href="'.$fileforward.'&area='.strtolower($row['area_code']).'">
    <img '.createLDImgSrc($root_path,'ok_small.gif','0','absmiddle').' alt="'.$LDShowActualPlan.'" ></a> </td></tr>';
    echo "\n";

    }

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the dept rows  to the frame template

 $smarty->assign('sDeptRows',$sTemp);

$smarty->assign('sBackLink','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'close2.gif','0').' alt="'.$LDCloseAlt.'">');

 $smarty->assign('sMainBlockIncludeFile','order/select_area.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
