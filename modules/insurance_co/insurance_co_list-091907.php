<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

require_once($root_path.'modules/insurance_co/ajax/hcplan-admin.common.php');
// require($root_path."modules/insurance_co/ajax/hcplan-admin.common.php");	// Added by LST -- 2007-08-23
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/

# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
define('MAX_BLOCK_ROWS',30); 

$lang_tables[]='search.php';
define('LANG_FILE','finance.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Load the insurance object
require_once($root_path.'include/care_api_classes/class_insurance.php');
$ins_obj=new Insurance;

// $breakfile='insurance_co_manage.php'.URL_APPEND;
$breakfile=$root_path.'main/spediens.php'.URL_APPEND;
$thisfile=basename(__FILE__);

# Initialize page�s control variables
if($mode!='paginate'){

	$searchkey=$HTTP_SESSION_VARS['sess_searchkey'];
	# Reset paginator variables
	$pgx=0;
	$totalcount=0;
	# Set the sort parameters
	if(empty($oitem)) $oitem='name';
	if(empty($odir)) $odir='ASC';
	
	if(empty($searchkey)){
		$searchkey = '*';
		$mode = 'search';
	}
	
}

//Start check for mode & pagination 
/*if($mode == 'panigate'){
	$searchkey=$HTTP_SESSION_VARS['sess_searchkey'];
}else{
	#Reset Paginator
	$pgx=0;
	$totalcount=0;
	$odir='ASC';
	$oitem='create_dt';
	
	if(empty($searchkey)){
		$searchkey ='*';
		$mode = 'search';
	}
}
*/
$GLOBAL_CONFIG=array();
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('pagin_insurance_list_max_block_rows');
if(empty($GLOBAL_CONFIG['pagin_insurance_list_max_block_rows'])) $GLOBAL_CONFIG['pagin_insurance_list_max_block_rows']=MAX_BLOCK_ROWS; # Last resort, use the default defined at the start of this page

#Load and create paginator object
require_once($root_path.'include/care_api_classes/class_paginator.php');
$pagen=new Paginator($pgx,$thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);
# Adjust the max nr of rows in a block
$pagen->setMaxCount($GLOBAL_CONFIG['pagin_insurance_list_max_block_rows']);

# Get all the active firms info
$firms=$ins_obj->getLimitActiveFirmsInfo($GLOBAL_CONFIG['pagin_insurance_list_max_block_rows'],$pgx,$oitem,$odir);

$linecount=$ins_obj->LastRecordCount();
$pagen->setTotalBlockCount($linecount);
# Count total available data
if(isset($totalcount)&&$totalcount){
	$pagen->setTotalDataCount($totalcount);
}else{
	$totalcount=$ins_obj->countAllActiveFirms();
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

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$LDInsuranceCo :: $LDManager");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('insurance_list.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDInsuranceCo :: $LDListAll");

# Buffer page output
ob_start();

  # Load the javascript code
	echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype1.5.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="js/hcplan-listing-functions.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="js/hcplan-listing.js"></script>'."\r\n";
	//echo '<script type="text/javascript" src="'.$root_path.'js/pagingajax.js"></script>'."\r\n";
	
	$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
	
	
		
	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->append('JavaScript',$sTemp);

ob_start();

?>

<script type="text/javascript">
<!--
	jsOnClick();
	
	-->
</script>
<?php

if(is_object($firms)){
	if ($linecount) echo str_replace("~nr~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.';
		else echo str_replace('~nr~','0',$LDSearchFound);
?>
<br>
<table id="hcplanlistTable" class="seglist" width="90%" border=0 cellpadding=2 cellspacing=1>
  <!--  list of insurances -->
</table>
<br><br>
<?php
}else{
	 echo str_replace('~nr~','0',$LDSearchFound);
}
?>
<p>
<form action="insurance_co_new.php" method="post">
<input type="hidden" name="lang" id="lang" value="<?php echo $lang ?>" />
<input type="hidden" name="sid" id="sid" value="<?php echo $sid ?>" />

<table align="center" width="90%">
<tr>
	<td align="center"><input type="submit" value="<?php echo $LDNeedEmptyFormPls ?>"></td>
	<td align="center"><input type="button" value="<?php echo $LDBenefitsManager ?>"></td>
</tr>
</table>
</form>
</ul>
<input type="hidden" name="thisfile" id="thisfile" value="<?php echo $thisfile ?>" />
<input type="hidden" name="searchkey" id="searchkey" value="<?php echo $searchkey ?>" />
<input type="hidden" name="mode" id="mode" value="<?php echo $mode ?>" />
<input type="hidden" name="odir" id="odir" value="<?php echo $odir ?>" />
<input type="hidden" name="oitem" id="oitem" value="<?php echo $oitem ?>" />
<input type="hidden" name="totalcount" id="totalcount" value="<?php echo $totalcount ?>" />
<input type="hidden" name="pgx" id="pgx" value="<?php echo $pgx ?>" />
<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path ?>" />

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<!-- Core module and plugins: -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<!--  Overlib stylesheet -->
<link rel="stylesheet" type="text/css" href="js/overlib-radio-list.css">

<!--  For Dojo -->
<style type="text/css">
	body{font-family : sans-serif;}
	dojoTabPaneWrapper{ padding : 10px 10px 10px;}
</style>

<script type="text/javascript">
<!--
 OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>


<script language="javascript">
	jsOnClick();
</script>
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