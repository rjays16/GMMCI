<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* Segworks Technologies Corporation 2007
* icd list 
* MLHE
*/

# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
define('MAX_BLOCK_ROWS',30); 

$lang_tables[]='search.php';
define('LANG_FILE','icd10icpm.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Load the insurance object
require_once($root_path.'include/care_api_classes/class_icpm.php');
$icpm_obj=new Icpm;

$phic = $_GET['phic'];
#echo "phic = ".$phic;

$breakfile='icpm_manage.php'.URL_APPEND.'&phic='.$phic;

if ($phic)
	$thisfile=basename(__FILE__)."?phic=".$phic;
else	
	$thisfile=basename(__FILE__);


# Initialize page�s control variables
if($mode!='paginate'){
	# Reset paginator variables
	$pgx=0;
	$totalcount=0;
	# Set the sort parameters
	//if(empty($oitem)) $oitem='procedure_code';
	if(empty($oitem)) $oitem='code';
	if(empty($odir)) $odir='ASC';
}

$GLOBAL_CONFIG=array();
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
//For insurance_list control
/*
$glob_obj->getConfig('pagin_insurance_list_max_block_rows');
if(empty($GLOBAL_CONFIG['pagin_insurance_list_max_block_rows'])) $GLOBAL_CONFIG['pagin_insurance_list_max_block_rows']=MAX_BLOCK_ROWS; # Last resort, use the default defined at the start of this page
*/
//For icd code_list control
$glob_obj->getConfig('pagin_icpm_list_max_block_rows');
if(empty($GLOBAL_CONFIG['pagin_icpm_list_max_block_rows'])) $GLOBAL_CONFIG['pagin_icpm_list_max_block_rows']= MAX_BLOCK_ROWS;

#Load and create paginator object
require_once($root_path.'include/care_api_classes/class_paginator.php');
$pagen=new Paginator($pgx,$thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);
# Adjust the max nr of rows in a block
$pagen->setMaxCount($GLOBAL_CONFIG['pagin_icpm_list_max_block_rows']);

//getLimitActiveFirmsInfo($len=30,$so=0,$sortby='name',$sortdir='ASC')

# Get all the active firms infos
//$firms=$ins_obj->getLimitActiveFirmsInfo($GLOBAL_CONFIG['pagin_insurance_list_max_block_rows'],$pgx,$oitem,$odir);
$refCode=$icpm_obj->getLimitIcpmInfo($GLOBAL_CONFIG['pagin_icpm_list_max_block_rows'], $pgx, $oitem, $odir, $phic);

$linecount=$icpm_obj->LastRecordCount();
$pagen->setTotalBlockCount($linecount);
# Count total available data

if(isset($totalcount)&&$totalcount){
	$pagen->setTotalDataCount($totalcount);
}else{
	$totalcount=$icpm_obj->countAllIcpm();
	$pagen->setTotalDataCount($totalcount);
}

$pagen->setSortItem($oitem);
$pagen->setSortDirection($odir);



# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');
 
 if ($phic)
 	$phic_caption = " For PHIC";

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$segICPM :: $LDListAll $phic_caption");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('icd10_list.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$segICPM :: $LDListAll $phic_caption");

# Buffer page output

ob_start();

?>

<ul>

<?php 
if(is_object($refCode)){
		
	if ($linecount) echo str_replace("~nr~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.';
		else echo str_replace('~nr~','0',$LDSearchFound);

?>
<table border=0 cellpadding=2 cellspacing=1>
  <tr class="wardlisttitlerow">
      <td><b>
	  	<?php echo $pagen->makeSortLink($segICPMCode,'code',$oitem,$odir);  ?></b>
	  </td>
      <td><b>
	  	<?php echo $pagen->makeSortLink($LDdescriptionA,'description',$oitem,$odir);  ?></b>
	  </td>
	  <td><b>
	  	<?php echo 'RVU'; ?></b>	
	  </td>
  </tr> 
<?php
	$toggle=0;
	$listID=0;
	while($refcode=$refCode->FetchRow()){
		if($toggle) $bgc='#dddddd';
		else $bgc='#efefef';
		$toggle=!$toggle;
?>
  <tr  bgcolor="<?php echo $bgc ?>" id="<?= $listID; ?>" >
    <td><a href="icpm_info.php<?php echo URL_APPEND.'&retpath=list&ref_code='.$refcode['code'].'&phic='.$phic; ?>"><?php echo $refcode['code']; ?></a></td>
    <td> <?php echo $refcode['description']; ?></td> 
	 <td> <?php echo $refcode['rvu']; ?></td>   
  </tr> 
<?php $listID++;
	}
	echo '
	<tr><td colspan=4>'.$pagen->makePrevLink($LDPrevious).'</td>
	<td align=right>'.$pagen->makeNextLink($LDNext).'</td>
	</tr>';
?>
  </table>
<?php
}else{
	 echo str_replace('~nr~','0',$LDSearchFound);
}
?>
<p>
<form action="icpm_new.php" method="post">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="sid" value="<?php echo $sid ?>">
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