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
# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
define('MAX_BLOCK_ROWS',30); 

$lang_tables[]='search.php';
$lang_tables[]='actions.php';
define('LANG_FILE','lab.php');
$local_user='ck_lab_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$toggle=0;

#$append=URL_APPEND."&target=$target&noresize=1&user_origin=$user_origin";
$append=URL_APPEND."&target=".$target."&noresize=1&user_origin=".$user_origin."&dept_nr=".$dept_nr;   # burn added: Oct. 3, 2006
$breakfile="radiolog.php$append";   # burn added: Oct. 2, 2006
$entry_block_bgcolor="#efefef";   # burn added: Oct. 2, 2006
$entry_border_bgcolor="#fcfcfc";   # burn added: Oct. 2, 2006
$entry_body_bgcolor="#ffffff";   # burn added: Oct. 2, 2006

$breakfile=$root_path.'modules/radiology/'.$breakfile;   # burn added: Oct. 2, 2006
# $breakfile=$root_path.'modules/nursing/'.$breakfile;   
$thisfile=basename(__FILE__);
# Data to append to url
$append='&status='.$status.'&target='.$target.'&user_origin='.$user_origin."&dept_nr=".$dept_nr;

//echo "radiology/radiology_undone_request.php : mode = '".$mode."' <br> \n";
require($root_path.'modules/radiology/ajax/radio-request.common.php');

# Initialize page�s control variables
//echo "sub_dept_nr = '".$sub_dept_nr."' <br> \n";
if($mode=='paginate'){
	$searchkey=$HTTP_SESSION_VARS['sess_searchkey'];
	//$searchkey='USE_SESSION_SEARCHKEY';
	//$mode='search';
}else{
	# Reset paginator variables
	$pgx=0;
	$totalcount=0;
	$odir='ASC';
#	$oitem='name_last';   # burn commented :  July 23, 2007
	$oitem='create_dt';   # burn added :  July 23, 2007

	if (empty($searchkey)){   # burn added :  August 3, 2007
		$searchkey='*';   # default search key
		$mode = 'search';
	}
}
# Paginator object
require_once($root_path.'include/care_api_classes/class_paginator.php');
$pagen=new Paginator($pgx,$thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);

require_once($root_path.'include/care_api_classes/class_globalconfig.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);

echo "before radio_obj=new SegRadio(); <br> \n";

# Radiology object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj=new SegRadio();

echo "after radio_obj=new SegRadio(); <br> \n";

# Get the max nr of rows from global config
$glob_obj->getConfig('pagin_patient_search_max_block_rows');
if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); # Last resort, use the default defined at the start of this page
	else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);

echo "mode = '".$mode."' <br> \n";
if(($mode=='search'||$mode=='paginate')&&!empty($searchkey)){
	# Convert other wildcards
	$searchkey=strtr($searchkey,'*?','%_');
	# Save the search keyword for eventual pagination routines
	if($mode=='search') $HTTP_SESSION_VARS['sess_searchkey']=$searchkey;

	include_once($root_path.'include/inc_date_format_functions.php');
	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

#	$encounter=& $radio_obj->searchLimitEncounterBasicInfo($searchkey,$pagen->MaxCount(),$pgx,$oitem,$odir);
	$encounter=& $radio_obj->searchLimitBasicInfoRadioPending($searchkey,$sub_dept_nr,$pagen->MaxCount(),$pgx,$oitem,$odir);
//	echo "radiology/radiology_undone_request.php : encounter = '".$encounter."' <br>";
//	echo "radiology/radiology_undone_request.php : radio_obj->sql = '".$radio_obj->sql."' <br>";
	$i=0;

	# Get the resulting record count
	$linecount=$radio_obj->LastRecordCount();
	echo "radiology/radiology_undone_request.php : linecount = ".$linecount." <br> ";

		# the if stmt below should be deleted LATER
		# replace with a message "NO UNDONE REQUESTS" if $linecount=0
/*
	if($linecount==1&&$mode=='search'){
		$row=$encounter->FetchRow();
		header("location:".$root_path."modules/nursing/nursing-station-patientdaten-doconsil-".$target.".php".URL_REDIRECT_APPEND."&pn=".$row['encounter_nr']."&edit=1&status=".$status."&target=".$target."&user_origin=".$user_origin."&noresize=1&mode=");
		exit;
	}
*/
	//$linecount=$address_obj->LastRecordCount();
	$pagen->setTotalBlockCount($linecount);
	# Count total available data
	if(isset($totalcount)&&$totalcount){
		$pagen->setTotalDataCount($totalcount);
		echo " totalcount above 2 = ".$totalcount." <br> ";
	}else{
		echo " totalcount above 3 A= ".$totalcount." <br> ";
		@$radio_obj->_searchBasicInfoRadioPending($searchkey,$sub_dept_nr);
#		@$radio_obj->searchEncounterBasicInfo($searchkey);   # burn comment: Oct 11, 2006
		$totalcount=$radio_obj->LastRecordCount();
		echo " totalcount above 3 B = ".$totalcount." <br> ";
		$pagen->setTotalDataCount($totalcount);
	}
	$pagen->setSortItem($oitem);
	$pagen->setSortDirection($odir);
}
//echo $target;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');

# Title in toolbar
 $smarty->assign('sToolbarTitle', $LDTestRequest." - ".$LDSearchPatient);

  # hide back button
 $smarty->assign('pbBack',FALSE);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('request_search.php')");

 # href for close button
 if($HTTP_COOKIE_VARS["ck_login_logged".$sid]) $smarty->assign('breakfile',$root_path.'main/startframe.php'.URL_APPEND);
	else  $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',$LDTestRequest." - ".$LDSearchPatient);

# Body onload javascript code
$smarty->assign('sOnLoadJs','onLoad="document.searchform.searchkey.select()"');

ob_start();

echo "<script type=\"text/javascript\" src=\"".$root_path."js/dojo/dojo.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"".$root_path."js/jsprototype/prototype1.5.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"js/radio-request-gui.js\"></script>";
?>
<!-- Include dojoTab Dependencies -->
<script type="text/javascript">
	dojo.require("dojo.widget.TabContainer");
	dojo.require("dojo.widget.LinkPane");
	dojo.require("dojo.widget.ContentPane");
	dojo.require("dojo.widget.LayoutContainer");
	dojo.require("dojo.event.*");
</script>
<style type="text/css">
	body{font-family : sans-serif;}
	dojoTabPaneWrapper{ padding : 10px 10px 10px;}
</style>
<!--  Dojo script function for undone request -->
<script language="javascript">
 //load eventOnClick
 dojo.addOnLoad(eventOnClick);
</script>

<?php

$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

$tmp1 = ob_get_contents();
ob_end_clean();
$smarty->assign('yhScript', $tmp1);

# Collect extra javascript code

ob_start();

?>
<ul>
<FONT  class="prompt"><?php echo $LDTestType[$target]; #echo $LDTestRequestFor.$LDTestType[$target]; ?></font>
<table width=90% border=0 cellpadding="0" cellspacing="0">
	<tr bgcolor="<?php echo $entry_block_bgcolor ?>" >
		<td>
			<p><br>			
			<ul>
				<table border=0 cellpadding=10 bgcolor="<?php echo $entry_border_bgcolor ?>">
					<tr>
						<td>
<?php
	   
				$searchmask_bgcolor="#f3f3f3";
				include($root_path.'include/inc_test_request_searchmask.php');
?>
						</td>
					</tr>
				</table>
			<p>
				<a href="<?php	echo $breakfile; ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>></a>
			<p>
				<script language="javascript">
				
				var urlholder;
				
				function popselectRadiologists(elem,mode){
					var date = new Date();
//					alert("date.getMonth() = '"+date.getMonth()+"' \ndate.getFullYear() = '"+date.getFullYear()+"'");
					tmonth = date.getMonth()+1;
					tyear = date.getFullYear();
					w=window.screen.width;
					h=window.screen.height;
					ww=300;
					wh=500;
				//	var tmonth=document.dienstplan.month.value;
				//	var tyear=document.dienstplan.jahr.value;
				//	urlholder="radiologists-dienstplan-poppersonselect.php?elemid="+elem + "&dept_nr=<?php echo $dept_nr ?>&month="+tmonth+"&year="+tyear+ "&mode=" + mode + "&retpath=<?php echo $retpath ?>&user=<?php echo $ck_doctors_dienstplan_user."&lang=$lang&sid=$sid"; ?>";
					urlholder="radiologists-dienstplan-poppersonselect.php?formNumber=1&elemid="+elem + "&dept_nr=<?php echo $dept_nr ?>&month="+tmonth+"&year="+tyear+"&mode=" + mode + "&retpath=<?php echo $retpath ?>&user=<?php echo $ck_doctors_dienstplan_user."&lang=$lang&sid=$sid"; ?>";
					popselectwin=window.open(urlholder,"pop","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
					window.popselectwin.moveTo((w/2)+80,(h/2)-(wh/2));
				}
				</script>
<?php
//echo $mode;
//echo "linecount = $linecount <br>";
//echo "totalcount = $totalcount <br>";
//echo "LDSearchFound = $LDSearchFound <br>";
//echo "LDShowing = $LDShowing <br>";
//echo "pagen->BlockStartNr() = ".$pagen->BlockStartNr()."<br>";
//echo "LDTo = $LDTo <br>";
//echo "pagen->BlockEndNr() = ".$pagen->BlockEndNr()."<br>";

if ($linecount) echo '<hr width=80% align=left>'.str_replace("~nr~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.';
	else echo str_replace('~nr~','0',$LDSearchFound); 

#Department object
include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department;

$radio_sub_dept=$dept_obj->getSubDept($dept_nr);

#$img_tab=createComIcon($root_path,'tab_04.gif','0','',TRUE);
#$img_tab_highlight=createComIcon($root_path,'tab1_highlight.gif','0','',TRUE);
$img_tab= $root_path."gui/img/common/default/tab_04.gif";
$img_tab1= $root_path."gui/img/common/default/tab_03.gif";
$img_tab2= $root_path."gui/img/common/default/tab_05.gif";
$img_tab_h= $root_path."gui/img/common/default/tab_hl_04.gif";
$img_tab_h1= $root_path."gui/img/common/default/tab_hl_03.gif";
$img_tab_h2= $root_path."gui/img/common/default/tab_hl_05.gif";
/*$img_tab_highlight=$root_path."gui/img/common/default/tab1_highlight.gif";*/

#echo "radiology/radiology_undone_request.php : dept_obj->rec_count = ".$dept_obj->rec_count." <br> \n";

if ($dept_obj->rec_count){
#if ($radio_sub_dept){
		if (!$sub_dept_nr){
		$text_color = " color:#FF6600; ";
			$background = $img_tab_h;   # highlight
			$background1 = $img_tab_h1;
			$background2 = $img_tab_h2;
		}else{
			$background = $img_tab;
			$background1 = $img_tab1;
			$background2 = $img_tab2;
		}
?>
				<br>
				<table cellspacing="0 "cellpadding="0" border="0" class="frame">
					<tr height="25">
				<td background="<?= $background1 ?>">&nbsp;&nbsp;</td>
						<td background="<?= $background ?>">
<?php 
	echo '<a href="'.$thisfile.URL_APPEND.'&mode=search&pgx='.$pgx.'&totalcount='.$totalcount.'&oitem='.$oitem.'&odir='.$odir.$append.'&searchkey=*">&nbsp;&nbsp;&nbsp; ALL &nbsp;&nbsp;&nbsp;</a>';
?>							
						</td>
						<td background="<?= $background2 ?>">&nbsp;&nbsp;</td>

<?php
	while ($rowSubDept = $radio_sub_dept->FetchRow()){
		if (trim($rowSubDept['name_short'])!=''){		
			$text_name = trim($rowSubDept['name_short']);
		}elseif (trim($rowSubDept['id'])!=''){
			$text_name = trim($rowSubDept['id']);
		}else{
			$text_name = trim($rowSubDept['name_formal']);
		}
		
		if ($rowSubDept['nr']==$sub_dept_nr){
			   # highlight
			/*$background = $img_tab_highlight;*/
			$background = $img_tab_h;
			$background1 = $img_tab_h1;
			$background2 = $img_tab_h2;
			$text_color = " color:#FF6600; ";
		}else{
			$background = $img_tab;
			$background1 = $img_tab1;
			$background2 = $img_tab2;
			$text_color = " color:#FFFFFF; ";
		}
?>						<td background="<?= $background1 ?>">&nbsp;&nbsp;</td>
						<td background="<?= $background ?>">
<?php 
	echo '<a href="'.$thisfile.URL_APPEND.'&mode=search&pgx='.$pgx.'&totalcount='.$totalcount.'&oitem='.$oitem.'&odir='.$odir.$append.'&searchkey='.$searchkey.'&sub_dept_nr='.$rowSubDept['nr'].'">&nbsp;&nbsp;&nbsp; '.$text_name.' &nbsp;&nbsp;&nbsp;</a>';
?>							
						</td>
						<td background="<?= $background2 ?>">&nbsp;&nbsp;</td>
<?php
	}   # end of while loop
?>
					</tr>
				</table>
<?php
}   # end of if-stmt 'if ($radio_sub_dept)'

/*
	echo "<br><b> \n";
	echo '<a href="'.$thisfile.URL_APPEND.'&mode=search&pgx='.$pgx.'&totalcount='.$totalcount.'&oitem='.$oitem.'&odir='.$odir.$append.'&searchkey=*"><img '.$img_tab_highlight.'>ALL</a>';
	echo "&nbsp;&nbsp;&nbsp;";
	echo '<a href="'.$thisfile.URL_APPEND.'&mode=search&pgx='.$pgx.'&totalcount='.$totalcount.'&oitem='.$oitem.'&odir='.$odir.$append.'&sub_dept_nr=164"><img '.$img_tab.'>General Radiography</a>';
	echo "&nbsp;&nbsp;&nbsp;";
	echo '<a href="'.$thisfile.URL_APPEND.'&mode=search&pgx='.$pgx.'&totalcount='.$totalcount.'&oitem='.$oitem.'&odir='.$odir.$append.'&sub_dept_nr=165">Ultrasound</a>';
	echo "&nbsp;&nbsp;&nbsp;";
	echo '<a href="'.$thisfile.URL_APPEND.'&mode=search&pgx='.$pgx.'&totalcount='.$totalcount.'&oitem='.$oitem.'&odir='.$odir.$append.'&sub_dept_nr=166">Special Procedures</a>';
	echo "&nbsp;&nbsp;&nbsp;";
	echo '<a href="'.$thisfile.URL_APPEND.'&mode=search&pgx='.$pgx.'&totalcount='.$totalcount.'&oitem='.$oitem.'&odir='.$odir.$append.'&sub_dept_nr=167">Computed Tomography</a>';
	echo '</b><br>';
*/
if ($radio_obj->record_count) { 
	# Preload  common icon images
	$img_male=createComIcon($root_path,'spm.gif','0','',TRUE);
	$img_female=createComIcon($root_path,'spf.gif','0','',TRUE);
	$bgimg='tableHeaderbg3.gif';
	//$tbg= 'background="'.$root_path.'gui/img/common/'.$theme_com_icon.'/'.$bgimg.'"';
	$tbg= 'class="adm_list_titlebar"';
?>

<!--  Test for dojo tab event  -->	
<div id="tbContainer" dojoType="TabContainer" style="width: 100%; height:28em; "> 
 <!-- List of all pending request tab#1 -->   
 <div dojoType="ContentPane" widgetId="tab1" label="All" style="display:none; overflow:auto">
 <!--  List of pending request -->
 <form name="dienstplanDoctorAssign" action="<?= $thisfile ?>" method="post">
	<table cellspacing="0 "cellpadding="0" border="0" class="segList" width="100%">
		<thead>
			<tr width="100%">
				<th colspan="11" align="left">List of Pending Requests</th>
			</tr>
		</thead>
		<thead>
			<tr> <!-- class="reg_list_titlebar" style="font-weight:bold;padding:0px" -->
				<td <?php echo $tbg; ?>><b>
					<?php echo "No.";  ?></b>
				</td>
				<td <?php echo $tbg; ?>><b>
					<?php echo $pagen->makeSortLink('Batch No.','batch_nr',$oitem,$odir,$append);  ?></b>
				</td>
				<td <?php echo $tbg; ?>><b>
					<?php echo $pagen->makeSortLink('Date Requested','request_date',$oitem,$odir,$append);  ?></b>
				</td>
				<td <?php echo $tbg; ?>><b>
					<?php echo $pagen->makeSortLink('Deparment','sub_dept_id',$oitem,$odir,$append);  ?></b>
				</td>
				<td <?php echo $tbg; ?>><b>
					<?php echo $pagen->makeSortLink('Patient No.','pid',$oitem,$odir,$append);  ?></b>
				</td>
				<td <?php echo $tbg; ?>><b>
					<?php echo $pagen->makeSortLink('Sex','sex',$oitem,$odir,$append);  ?></b>
				</td>
				<td <?php echo $tbg; ?>><b>
					<?php echo $pagen->makeSortLink('Family Name','name_last',$oitem,$odir,$append);  ?></b>
				</td>
				<td <?php echo $tbg; ?>><b>
					<?php echo $pagen->makeSortLink('Name','name_first',$oitem,$odir,$append); ?></b>
				</td>
				<td <?php echo $tbg; ?>><b>
					<?php echo $pagen->makeSortLink('Birthdate','date_birth',$oitem,$odir,$append); ?></b>
				</td>
				<td <?php echo $tbg; ?>><b>
					<?php echo $pagen->makeSortLink('Request Status','status',$oitem,$odir,$append); ?></b>
				</td>
				<td <?php echo $tbg; ?>><b>
					<?php echo "Details";  ?></b>
				</td>
			</tr>
		</thead>
		<tbody>		
<?php
	$toggle=0;
#	$my_count=1;
	$my_count=$pagen->BlockStartNr();
//  No use ;comment by mark Aug 22, 2007	
//	require_once($root_path.'include/care_api_classes/class_request_sked.php');
//	$sked_obj=new SegRequestSked;

#	echo "b4 while loop <br> encounter->FetchRow() =".$encounter->FetchRow()." <br>";
	
	while ($rowRequest = $encounter->FetchRow()){
		#echo "inside while loop <br>";
		echo "					<tr class=";
		if($toggle) { echo '"wardlistrow2">'; $toggle=0;} 
		else {echo '"wardlistrow1">'; $toggle=1;};
		echo "						<td>&nbsp;".$my_count."&nbsp;</td> \n";
		echo "						<td>&nbsp;".$rowRequest['batch_nr']."</td> \n";
		echo "						<td>&nbsp;".formatDate2Local($rowRequest['request_date'],$date_format)."</td> \n";
#		echo "						<td>&nbsp;".$rowRequest['encounter_nr']."</td> \n";
		echo "						<td>&nbsp;".$rowRequest['sub_dept_id']."</td> \n";
		echo "						<td>&nbsp;".$rowRequest['pid']."</td> \n";
		echo "						<td>";
		
			switch($rowRequest['sex']){
				case 'f': echo '<img '.$img_female.'>'; break;
				case 'm': echo '<img '.$img_male.'>'; break;
				default: echo '&nbsp;'; break;
			}	
		echo "						</td> \n";
		echo "						<td>&nbsp;".$rowRequest['name_last']."</td> \n";
		echo "						<td>&nbsp;".$rowRequest['name_first']."</td> \n";

			$date_birth = formatDate2Local($rowRequest['date_birth'],$date_format);
			$bdateMonth = substr($date_birth,0,2);
			$bdateDay = substr($date_birth,3,2);
			$bdateYear = substr($date_birth,6,4);
			if (!checkdate($bdateMonth, $bdateDay, $bdateYear)){
				# invalid birthdate
				$date_birth='';
			}
#		echo "						<td>&nbsp;".formatDate2Local($rowRequest['date_birth'],$date_format)."</td> \n";
		echo "						<td>&nbsp;".$date_birth."</td> \n";
		echo "						<td>&nbsp;".$rowRequest['status']."</td> \n";
		$radio_findings_link="<a href=seg-radio-findings.php".URL_APPEND."&user_origin=lab&batch_nr=".$rowRequest['batch_nr']."&pid=".$rowRequest['pid'].">Findings</a>";
		echo "						<td>$radio_findings_link</td> \n";
		echo "					</tr> \n";
		$my_count++;
   }/* end of while loop */
?>
			<tr>
				<td colspan="10"><?php echo $pagen->makePrevLink($LDPrevious,$append); ?></td>
				<td align=right><?php echo $pagen->makeNextLink($LDNext,$append); ?></td>
			</tr>
		</tbody>
	</table>
</form>
	</div>

	<!--  Computed Tomography tab#2 --> 
    <div dojoType="ContentPane" widgetId="tab2" label="Computed Tomography" style="display:none" >
    	<table id="cttab2" cellpadding="0" cellspacing="0" class="segList">
    		<!-- List of Pending Requests  -->
    		<tbody id="ttab2"></tbody>
    	</table>
    </div>
    <!--  General Radiology tab#3 -->
    <div dojoType="ContentPane" widgetId="tab3" label="General Radiology" style="display:none" >
    	<table id="grtab3" cellpadding="0" cellspacing="0" class="segList">
    		<!-- List of Pending Requests  -->
    		<tbody id="ttab3"></tbody>
    	</table>
	</div>
	<!--  Special Procedures tab#4 -->
	<div dojoType="ContentPane" widgetId="tab4" label="Special Procedures" style="display:none" >
		<table id="sptab4" cellpadding="0" cellspacing="0" class="segList">
			<!-- List of Pending Requests  -->
    		<tbody id="ttab4"></tbody>
		</table>
	</div>
	<!--  Ultrasound tab#5 -->
	<div dojoType="ContentPane" widgetId="tab5" label="Ultrasound" style="display:none">
		<table id="ustab5" cellpadding="0" cellspacing="0" class="segList">
			<!-- List of Pending Requests  -->
    		<tbody id="ttab5"></tbody>
		</table>
	</div>
</div>

<?php
	if($linecount>$pagen->MaxCount()){
?>
	<table border=0 cellpadding=10 bgcolor="<?php echo $entry_border_bgcolor ?>">
		<tr>
			<td>
	   <?php
	        $searchform_count=2;
            include($root_path.'include/inc_test_request_searchmask.php');
	   ?>
			</td>
		</tr>
	</table>
<?php
	}
}else{   # else of 'if ($radio_obj->record_count)'
?>
	<div align="center">
	<table cellspacing="0 "cellpadding="0" border="0" width="100%" class="frame">
		<tbody>
			<tr>
				<td style="color:white; background-color: #376bb3; font-weight:bold;">
					&nbsp;List of Pending Requests
				</td>
			</tr>
			<tr>
				<td align="center" bgcolor="#FFFFFF" style="color:#FF0000; font-family:'Arial', Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;"> 
					NO PENDING REQUEST FOUND  </td>
			</tr>
		</tbody>
	</table>
	</div>
<?php
}
?>
	</ul>
�
			<p>
		</td>
	</tr>
</table>
			
			<input type="hidden" name="skey" id="skey" value="<?php echo $HTTP_SESSION_VARS['sess_searchkey']; ?>"> 
			<input type="hidden" name="smode" id="smode" value="<?php echo $mode; ?>">
			<input type="hidden" name="starget" id="starget" value="<?php echo $target; ?>">
			<input type="hidden" name="thisfile" id="thisfile" value="<?php echo $thisfile; ?>">
			<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path; ?>">
			<input type="hidden" name="pgx" id="pgx" value="<?php echo $pgx; ?>">
			<input type="hidden" name="totalcount" id="totalcount" value="<?php echo $totalcount; ?>">

<table>
	<tr align="center" style="width:auto">
		<td>
			<?php 
#				$requestFileForward = "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio&user_origin=lab\">Test request</a>";
#				echo $requestFileForward;
				$requestFileForward = $root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio&user_origin=lab";
				echo '<a href="'.$requestFileForward.'"><img '.createLDImgSrc($root_path,'newrequest.gif','0','left').' border=0 alt="Enter New Lab Request"></a>';
			?>
		</td>
	<tr>
</table>

</ul>
<p>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

# Assign to page template object
$smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
// require($root_path.'js/floatscroll.js');

?>