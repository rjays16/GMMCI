<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/ICD10/ajax/icd10_list.common.php");
/**
* Segworks Technologies Corporation 2007
*
*/
# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
define('MAX_BLOCK_ROWS',30);

$lang_tables[]='search.php';
define('LANG_FILE','icd10icpm.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Load the insurance object
require_once($root_path.'include/care_api_classes/class_icd10.php');
$icd_obj=new Icd;

$breakfile='icd10_manage.php'.URL_APPEND;
$thisfile=basename(__FILE__);

# Initialize pageï¿½s control variables
if($mode!='paginate'){
	# Reset paginator variables
	$pgx=0;
	$totalcount=0;
}else{
	$searchkey=$HTTP_SESSION_VARS['sess_searchkey']; # dummy search key to get past the search routine
}
# Set the sort parameters
if(empty($oitem)) $oitem='diagnosis_code';
if(empty($odir)) $odir='ASC';

# Get global configuration
$GLOBAL_CONFIG=array();
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('pagin_icd10_search_max_block_rows');
if(empty($GLOBAL_CONFIG['pagin_icd10_search_max_block_rows'])) $GLOBAL_CONFIG['pagin_icd10_search_max_block_rows']=MAX_BLOCK_ROWS; # Last resort, use the default defined at the start of this page

#Load and create paginator object
require_once($root_path.'include/care_api_classes/class_paginator.php');
$pagen=new Paginator($pgx,$thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);
# Adjust the max nr of rows in a block
$pagen->setMaxCount($GLOBAL_CONFIG['pagin_icd10_search_max_block_rows']);

if(isset($mode)&&($mode=='search'||$mode=='paginate')&&!empty($searchkey)){

	# Convert wildcards
	$searchkey=strtr($searchkey,'*?','%_');
	# Save the search keyword for eventual pagination routines
	if($mode=='search') $HTTP_SESSION_VARS['sess_searchkey']=$searchkey;

	# Search for the icd10 code
	$refCode=$icd_obj->searchLimitActiveIcd10($searchkey,$GLOBAL_CONFIG['pagin_icd10_search_max_block_rows'],$pgx,$oitem,$odir);

	# Get the resulting record count
	$linecount=$icd_obj->LastRecordCount();
	$pagen->setTotalBlockCount($linecount);

	# Count total available data
	if(isset($totalcount)&&$totalcount){
		$pagen->setTotalDataCount($totalcount);
	}else{
		$totalcount=$icd_obj->searchCountActiveIcd10($searchkey);
		$pagen->setTotalDataCount($totalcount);
	}
	$pagen->setSortItem($oitem);
	$pagen->setSortDirection($odir);
}

$bgc=$root_path.'gui/img/skin/default/tableHeaderbg3.gif';
$bgc2='#eeeeee';

# Set color values for the search mask
$entry_block_bgcolor='#fff3f3';
$entry_border_bgcolor='#abcdef';
$entry_body_bgcolor='#ffffff';

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$LDicd10 :: $LDSearch");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('icd10_search.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDicd10 :: $LDSearch");

 # Body onLoad Javascript code
 $smarty->assign('sOnLoadJs','onLoad="document.searchform.searchkey.select()"');

# Buffer page output

ob_start();
$xajax->printJavascript($root_path.'classes/xajax_0.5');
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript">
<!--
    function delSelectedICDs() {      
        var codes = $$('[name="icdcodes"]');
        var bulk = [];
        var ndx=0;

        codes.each (
          function(code) {
              if (code.checked) {
                bulk[ndx++] = code.value;
              }
          }
        );            

        xajax.call('deleteSelectedICDs', { 
            parameters : [bulk],            
            onComplete: function() {
                alert('Deletion completed!');
                location.href='icd10_search.php';
            }
        });   
    }

//-->
</script>

 <div align="left">
   <ul>
        <font size="2" face="verdana,Arial">&nbsp;
        <br>
        <!--  The search mask  -->
     </font>
     <table border=0 cellpadding=10 bgcolor="<?php echo $entry_border_bgcolor ?>">
       <tr>
         <td>
             <font size="2" face="verdana,Arial">
             <?php
	   		$searchprompt=$LDSearchPrompt;
	    	include($root_path.'include/inc_searchmask.php');
		?>
         </font></td>
       </tr>
       </table>
     <font size="2" face="verdana,Arial"><br>
     <?php
if(is_object($refCode)){

	if ($linecount){
		echo str_replace("~nr~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.';
	}else{
		echo str_replace('~nr~','0',$LDSearchFound);
	}

?>
        </font>
     <table border=0 cellpadding=2 cellspacing=1>
       <tr class="wardlisttitlerow">
         <td><font size="2" face="verdana,Arial"><b>
          <?php echo $pagen->makeSortLink($LDicd10Code,'diagnosis_code',$oitem,$odir);  ?></b>
           </font></td>
         <td><font size="2" face="verdana,Arial"><b>
          <?php echo $pagen->makeSortLink($LDdescriptionA,'description',$oitem,$odir);  ?></b>
           </font></td>
         <td><font size="2" face="verdana,Arial"><b>Select</b></font></td>
      </tr>
       <?php
	$toggle=0;
	while($refcode=$refCode->FetchRow()){
		if($toggle) $bgc='wardlistrow2';
			else $bgc='wardlistrow1';
		$toggle=!$toggle;

//		if(strlen($refcode['diagnosis_code']) == 11){
//			$icdcodeA = substr($refcode['diagnosis_code'],0,5);
//			$icdcodeB = substr($refcode['diagnosis_code'],6,10);
//			$diagnosis_code = $icdcodeA."-".$icdcodeB;
//		}else{
			$diagnosis_code = $refcode['diagnosis_code'];
//		}
?>
       <tr  class="<?php echo $bgc ?>">
            <td><font size="2" face="verdana,Arial"><a href="icd10_info.php<?php echo URL_APPEND.'&retpath=search&diagnosis_code='.urlencode( $diagnosis_code); //$refcode['diagnosis_code']; ?>"><?php echo $diagnosis_code;//echo $refcode['diagnosis_code']; ?></a></font></td>
            <td><font size="2" face="verdana,Arial"><?php echo $refcode['description']; ?></font></td>
            <td align="center"><input type="checkbox" id="code_<?php echo $diagnosis_code; ?>" name="icdcodes" style="cursor:pointer" value="'<?php echo $diagnosis_code; ?>'" /></td>
      </tr>
       <?php
	}
	echo '
	<tr><td colspan=4><font face=arial size=2>'.$pagen->makePrevLink($LDPrevious).'</td>
	<td align=right><font face=arial size=2>'.$pagen->makeNextLink($LDNext).'</td>
	</tr>';
?>
       </table>
     <font size="2" face="verdana,Arial">
     <?php
}elseif($mode=='search'||$mode=='paginate'){
	echo str_replace('~nr~','0',$LDSearchFound);
}
?>
        </font>
   </ul>
 </div>
 <ul>
   <p align="left">

<form action="icd10_new.php" method="post">
  <div align="left">
      <input type="hidden" name="lang" value="<?php echo $lang ?>">
      <input type="hidden" name="sid" value="<?php echo $sid ?>">
      <input type="submit" style="cursor:pointer" value="<?php echo $LDNeedEmptyFormPls ?>">
      <?php if(is_object($refCode)) { ?>                    
        <input type="button" style="cursor:pointer" value="<?php echo $segICDDeleteLabel ?>" onclick="delSelectedICDs();" >
      <?php } ?>
  </div>
</form>
</ul>

 <div align="left">
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
 </div>
