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
define('LANG_FILE','place.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Load the insurance object
require_once($root_path.'include/care_api_classes/class_address.php');
$address_brgy=new Address('barangay');
#$address_brgy->_useBarangays();

switch($retpath)
{
	case 'list': $breakfile='brgy_list.php'.URL_APPEND; break;
	case 'search': $breakfile='brgy_search.php'.URL_APPEND; break;
	default: $breakfile='brgy_manage.php'.URL_APPEND; 
}

if(isset($brgy_nr)&&$brgy_nr&&($row=&$address_brgy->getAddressInfo($brgy_nr,TRUE))){
	$address=$row->FetchRow();
    //echo print_r($address);
    //3 is the of municity code as a result from the SQL statement which has 2 same field name
    $brgy_list = $address['3'];
	$edit=true;
}else{
	# Redirect to search function
}


# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$segBrgy :: $LDData");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('address_info.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$segBrgy :: $LDData");

# Buffer page output

ob_start();

?>

<ul>
<?php
if(isset($save_ok)&&$save_ok){ 
?>
<img <?php echo createMascot($root_path,'mascot1_r.gif','0','absmiddle') ?>><font face="Verdana, Arial" size=3 color="#880000">
<b>
<?php 
 	echo $LDAddressInfoSaved;
?>
</b></font>
<?php 
} 
?>
<table border=0 cellpadding=4 >
	<tr>
        <td align=right class="adm_item"></font>Barangay Code: </td>
        <td class="adm_input"><?php echo $brgy_list; //$address['code'] ?><br></td>
	</tr> 
	<tr>
		<td align=right class="adm_item"></font><?php echo $segBrgyName ?>: </td>
		<td class="adm_input"><?php echo $address['brgy_name'] ?><br></td>
	</tr> 
	<tr>
		<td align=right class="adm_item"></font><?php echo $segMuniCityName ?>: </td>
		<td class="adm_input"><?php echo $address['mun_name'] ?><br></td>
	</tr> 
	<tr>
		<td>
			<a href="brgy_update.php<?php echo URL_APPEND.'&retpath='.$retpath.'&brgy_nr='.$address['brgy_nr']; ?>"><img <?php echo createLDImgSrc($root_path,'update.gif','0') ?>></a>
		</td>
		<td  align=right>
			<a href="brgy_list.php<?php echo URL_APPEND; ?>"><img <?php echo createLDImgSrc($root_path,'list_all.gif','0') ?>></a> 
			<a href="<?php echo $breakfile; ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>></a>
		</td>
	</tr>
</table>
<p>
<form action="brgy_new.php" method="post">
	<input type="hidden" name="lang" value="<?php echo $lang ?>">
	<input type="hidden" name="sid" value="<?php echo $sid ?>">
	<input type="hidden" name="retpath" value="<?php echo $retpath ?>">
	<input type="submit" value="<?php echo $LDNeedEmptyFormPls ?>">
</form>

</ul>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign page output to the mainframe template

$smarty->assign('sMainFrameBlockData',$sTemp);
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
