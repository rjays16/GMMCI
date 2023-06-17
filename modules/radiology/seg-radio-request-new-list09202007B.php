<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
#include xajax common
require($root_path.'modules/radiology/ajax/radio-request-list.common.php');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
define('MAX_BLOCK_ROWS',30); 

$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
//$db->debug=1;
#$append=URL_APPEND."&target=".$target."&noresize=1&user_origin=".$user_origin."&dept_nr=".$dept_nr;   

$append=URL_APPEND."&status=".$status."&target=".$target."&noresize=1&user_origin=".$user_origin."&dept_nr=".$dept_nr;   # burn added: Septmeber 19, 2007
$breakfile="radiolog.php$append";   # burn added: Septmeber 19, 2007

$breakfile=$root_path.'modules/radiology/'.$breakfile;

$append='&status='.$status.'&target='.$target.'&user_origin='.$user_origin."&dept_nr=".$dept_nr;

$thisfile=basename(__FILE__);

$toggle=0;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Radiology:: Service Request List");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Radiology:: Service Request List");

 # Assign Body Onload javascript code
# $smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');

 # Collect javascript code
 ob_start();
 
echo "<!--Include dojo toolkit -->";
echo "<script type=\"text/javascript\" src=\"".$root_path."js/dojo/dojo.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"".$root_path."js/jsprototype/prototype1.5.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"js/radio-request-list.js\"></script>";
 
?>
<!-- Include dojoTab Dependencies -->
<script type="text/javascript">
	dojo.require("dojo.widget.TabContainer");
	dojo.require("dojo.widget.LinkPane");
	dojo.require("dojo.widget.ContentPane");
	dojo.require("dojo.widget.LayoutContainer");
	dojo.require("dojo.event.*");
</script>

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
	dojo.addOnLoad(evtOnClick);
</script>

<?php
//Print xajax script
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Radiology object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj=new SegRadio();
/*
$rows = "";
if ($result) {
	$count=0;
	while ($row=$result->FetchRow()) {
		$urgency = $row["is_urgent"]?"Urgent":"Normal";
		if ($row["pid"]) 
			$name = $row["name_last"].", ".$row["name_first"]." ".$row["name_middle"];
		else
			$name = $row["ordername"];
		if (!$name) $name='<i style="font-weight:normal">No name</i>';
		$class = (($count%2)==0)?"":"wardlistrow2";
		
		$rows .= "		<tr class=\"$class\">
				<td><img src=\"../../images/btn_delitem.gif\" border=\"0\"/></td>
				<td>".$row["refno"]."</td>
				<td>".$name."</td>
				<td align=\"center\">".$row["serv_dt"]."</td>
				<td style=\"color:#007\" align=\"center\">$urgency</td>
				<td align=\"center\"><a href=\"seg-lab-request-new-edit.php".URL_APPEND."&clear_ck_sid=$clear_ck_sid&mode=edit&ref=".$row["refno"]."\">Edit</a></td>
			</tr>\n";	
		$count++;
	}
}

if (!$rows) {
	$rows = '		<tr><td colspan="6">No requests available at this time...</td></tr>';
}
*/
#Department object
include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department;

//Start check for mode & pagination 
if($mode == 'paginate'){
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

# Paginator object
require_once($root_path.'include/care_api_classes/class_paginator.php');
$pagen=new Paginator($pgx,$thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);

require_once($root_path.'include/care_api_classes/class_globalconfig.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);

# Get the max nr of rows from global config
$glob_obj->getConfig('pagin_patient_search_max_block_rows');
if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); # Last resort, use the default defined at the start of this page
	else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);


if(($mode=='search'||$mode=='paginate')&&!empty($searchkey)){
	# Convert other wildcards
	$searchkey=strtr($searchkey,'*?','%_');
	# Save the search keyword for eventual pagination routines
	if($mode=='search') $HTTP_SESSION_VARS['sess_searchkey']=$searchkey;

	include_once($root_path.'include/inc_date_format_functions.php');
	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

	$encounter=& $radio_obj->searchLimitBasicInfoRadioPending($searchkey,$sub_dept_nr,$pagen->MaxCount(),$pgx,$oitem,$odir);
#	$encounter=& $radio_obj->searchLimitBasicInfoRadioRefNo($searchkey,$sub_dept_nr, $pagen->MaxCount(), $pgx, $oitem, $odir);
#exit();
	$i=0;

	# Get the resulting record count
	$linecount=$radio_obj->LastRecordCount();
//	echo "radiology/radiology_undone_request.php : linecount = ".$linecount." <br> ";

		# the if stmt below should be deleted LATER
		# replace with a message "NO UNDONE REQUESTS" if $linecount=0
	
	$pagen->setTotalBlockCount($linecount);
	# Count total available data
	if(isset($totalcount)&&$totalcount){
		$pagen->setTotalDataCount($totalcount);
		#echo " totalcount above 2 = ".$totalcount." <br> ";
	}else{
		#echo " totalcount above 3 A= ".$totalcount." <br> ";
		@$radio_obj->_searchBasicInfoRadioPending($searchkey,$sub_dept_nr);
		$totalcount=$radio_obj->LastRecordCount();
		#echo " totalcount above 3 B = ".$totalcount." <br> ";
		$pagen->setTotalDataCount($totalcount);
	}
	$pagen->setSortItem($oitem);
	$pagen->setSortDirection($odir);
}

ob_start();
?>
<a name="pagetop"></a>
<br>
<div style="padding-left:10px">
<!-- <form action="<?php echo $thisfile?>" method="post" name="suchform" onSubmit="">  -->
	<div id="tabFpanel">
		<div align="center" style="display:">
			<table width="100%" cellpadding="4">
				<tr>
					<td width="30%" align="center">
						Enter the reference no.
						<input class="segInput" id="search-refno" name="srefno" type="text" size="30" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial"/>
						<input type="image" src="../../images/his_searchbtn.gif" align="absmiddle" onClick="$('skey').value=$('search-refno').value; jsOnClick();">
					</td>
				</tr>
			</table>
		</div>
		<div align="center">
			<table cellpadding="4" style="display:none">
				<tr>
					<td align="center">Enter the person's Identification No. (PID)</td>
				</tr>
				<tr>
					<td align="center">
						<input class="segInput" id="search" type="text" size="50" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial"/>
						<input type="image" src="../../images/his_searchbtn.gif" align="absmiddle" onclick="startAJAXSearch('search');return false;" />
					</td>
				</tr>
			</table>
		</div>
		<div align="center">
			<table cellpadding="4" style="display:none">
				<tr>
					<td align="center">Enter the search keyword (e.g. First name, or family name)</td>
				</tr>
				<tr>
					<td align="center">
						<input class="segInput" id="search-name" name="sname" type="text" size="50" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial"/>
						<input type="image" src="../../images/his_searchbtn.gif" align="absmiddle" />
					</td>
				</tr>
			</table>

		</div>
	</div>
</div>
	<input type="hidden" name="sid" id="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" id="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">
<!-- </form> -->
<br>

<br>
<!--  Tab Container for radiology request list -->
<div id="rlistContainer"  dojoType="TabContainer" style="width:90%; height:28em;" align="center">
	<div dojoType="ContentPane" widgetId="tab0" label="All" style="display:none;overflow:auto">
		<!--  Table:list of request -->
		<table id="Ttab0" class="segList" border="0" cellpadding="0" cellspacing="0">
			<!-- List of all radiology request -->
		</table>
		<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
	</div>
	<!-- tabcontent for radiology sub-department -->
<?php

$radio_sub_dept=$dept_obj->getSubDept($dept_nr);

if($dept_obj->rec_count){
	$dept_counter=2;
	while ($rowSubDept = $radio_sub_dept->FetchRow()){
		if (trim($rowSubDept['name_short'])!=''){		
			$text_name = trim($rowSubDept['name_short']);
		}elseif (trim($rowSubDept['id'])!=''){
			$text_name = trim($rowSubDept['id']);
		}else{
			$text_name = trim($rowSubDept['name_formal']);
		}
?>		
<div dojoType="ContentPane" widgetId="tab<?=$rowSubDept['nr']?>" label="<?=$text_name?>" style="display:none;overflow:auto" >
   	<table id="Ttab<?=$rowSubDept['nr']?>" cellpadding="0" cellspacing="0" class="segList">
   		<!-- List of Radiology Requests  -->
   	</table>
   	<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
</div>
<?php 
		$dept_counter++;
	} # end of while loop
}   # end of if-stmt 'if ($dept_obj->rec_count)'
?>
</div>
	<input type="hidden" name="skey" id="skey" value="<?php echo $HTTP_SESSION_VARS['sess_searchkey']; ?>"> 
	<input type="hidden" name="smode" id="smode" value="<?php echo $mode; ?>">
	<input type="hidden" name="starget" id="starget" value="<?php echo $target; ?>">
	<input type="hidden" name="thisfile" id="thisfile" value="<?php echo $thisfile; ?>">
	<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path; ?>">
	<input type="hidden" name="pgx" id="pgx" value="<?php echo $pgx; ?>">
	<input type="hidden" name="oitem" id="oitem" value="<?= $oitem? $oitem:'create_dt' ?>">
	<input type="hidden" name="odir" id="odir" value="<?= $odir? $odir:'ASC' ?>">
	<input type="hidden" name="totalcount" id="totalcount" value="<?php echo $totalcount; ?>">

<script language="javascript">
	jsOnClick();
</script>

<br />
<hr>

<?php

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
	/**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
	include_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common',FALSE,FALSE,FALSE);
	
	# Set a flag to display this page as standalone
	$bShowThisForm=TRUE;
}

?>

<form action="<?php echo $breakfile?>" method="post">
	<input type="hidden" name="searchkey" id="searchkey" onchange="trimStringSearchMask(this)" >
	<input type="hidden" name="sid" value="<?php echo $sid ?>">
	<input type="hidden" name="lang" value="<?php echo $lang ?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" id="mode" value="search">
	
</form> 


<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
