<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

$breakfile='seg_hospital_info.php'.URL_APPEND;

# Create Hospital Info object
$hosp_obj=new Hospital_Admin;

$hosp = $hosp_obj->getAllHospitalInfo();
$hosp_count = $hosp_obj->count;

# Prepare title
$sTitle = "$LDHospInfo :: View";

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',$sTitle);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_info.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',$sTitle);

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

<ul>
<?php if($rows) : ?>

<img <?php echo createMascot($root_path,'mascot1_r.gif','0','bottom') ?> align="absmiddle"><font face="Verdana, Arial" size=3 color="#880000">
<b><?php echo str_replace("~station~",strtoupper($station),$LDStationExists) ?></b></font><p>
<?php endif ?>
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>

<form action="seg_hospital_info_edit.php" method="post" name="hospinfo_edit">

<table border=0 cellpadding=4>
<tbody class="submenu">
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"></font><?php echo $LDHospType ?>: </td>
    <td class=pblock width="70%">
			<?php 	
				$hosp_type_obj = $hosp_obj->getHospitalType($hosp['hosp_type']);
				echo $hosp_type_obj['hosp_desc'];
 			?>
	 </td>
  </tr> 
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"></font><?php echo $LDHospName ?>: </td>
    <td class=pblock><?php echo $hosp['hosp_name']; ?></td>
  </tr> 

  <tr>
    <td class=pblock align=right bgColor="#eeeeee"></font><?php echo $LDHospNameShort ?>: </td>
    <td class=pblock><?php echo $hosp['hosp_id']; ?></td>
  </tr>

  <tr>
    <td class=pblock align=right bgColor="#eeeeee"></font><?php echo $LDDoc_Rate ?>: Php</td>
    <td class=pblock><?php echo number_format($hosp['house_case_dailyrate'],2); ?></td>
  </tr>
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee">No., Street: </td>
    <td class=pblock><?php echo trim($hosp['addr_no_street']); ?></td>
  </tr>   
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee">Barangay: </td>
    <td class=pblock><?php echo trim($hosp['brgy_name']); ?></td>
  </tr>   
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee">Municipality/City: </td>
    <td class=pblock><?php echo trim($hosp['mun_name']); ?></td>
  </tr>  
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee">Province: </td>
    <td class=pblock><?php echo trim($hosp['prov_name']); ?></td>
  </tr>      
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee">Zip Code: </td>
    <td class=pblock><?php echo trim($hosp['zip_code']); ?></td>        
  </tr>    
  
<!--  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDAddr2 ?>: </td>
    <td class=pblock><textarea name="hosp_addr2" id="hosp_addr2" cols=40 rows=4 wrap="physical"><?php echo trim($hosp['hosp_addr2']); ?></textarea></td>
  </tr> -->    
  
<!--  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDAddr1 ?>: </td>
    <td class=pblock><?php echo trim(nl2br($hosp['hosp_addr1'])); ?></td>
  </tr>
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDAddr2 ?>: </td>
    <td class=pblock><?php echo trim(nl2br($hosp['hosp_addr2'])); ?></td>
  </tr>  -->
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDHospAgency ?>: </td>
    <td class=pblock><?php echo trim(nl2br($hosp['hosp_agency'])); ?></td>
  </tr>
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDHospCountry ?>: </td>
    <td class=pblock><?php echo trim(nl2br($hosp['hosp_country'])); ?></td>
  </tr>      
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"></font><?php echo $LDHospAccommodationCutOff ?>: </td>
    <td class=pblock><?php echo number_format($hosp['accom_hrs_cutoff'], 0); ?></td>
  </tr>  
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"></font><?php echo $LDHospPCF ?>: </td>
    <td class=pblock><?php echo number_format($hosp['pcf'], 2); ?></td>
  </tr>    
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"></font><?php echo $LDHospAuthorizedRep ?>: </td>
    <td class=pblock><?php echo $hosp['authrep']; ?></td>
  </tr>    
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"></font><?php echo $LDCapacity ?>: </td>
    <td class=pblock><?php echo $hosp['designation']; ?></td>
  </tr>  
  
</tbody>
</table>
<input type="hidden" name="sid" value="<?php echo $sid; ?>">
<input type="hidden" name="lang" value="<?php echo $lang; ?>">
<br>
<input type="submit" value="<?php echo $LDUpdateData; ?>">
</form>
<p>

<a href="javascript:history.back()"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a>

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
