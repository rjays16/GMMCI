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

$dbtable='care_admission_patient';

$toggle=0;

#$append=URL_APPEND."&target=$target&noresize=1&user_origin=$user_origin";
$append=URL_APPEND."&target=".$target."&noresize=1&user_origin=".$user_origin."&dept_nr=".$dept_nr;   # burn added: Oct. 3, 2006
$breakfile="radiolog.php$append";   # burn added: Oct. 2, 2006
$entry_block_bgcolor="#efefef";   # burn added: Oct. 2, 2006
$entry_border_bgcolor="#fcfcfc";   # burn added: Oct. 2, 2006
$entry_body_bgcolor="#ffffff";   # burn added: Oct. 2, 2006
echo "radiology/radiology_undone_request.php : dept_nr = '".$dept_nr."'<br> \n";
echo "radiology/radiology_undone_request.php : append = '".$append."' <br> \n";
echo "radiology/radiology_undone_request.php : breakfile = '".$breakfile."' <br> \n";

/*
switch($target)
{
  case 'chemlabor': $entry_block_bgcolor="#fff3f3";
                          $entry_border_bgcolor="#ee6666";
						  $entry_body_bgcolor="#ffffff";
						  $breakfile="nursing-station-patientdaten-doconsil-chemlabor.php$append";
						  break;
  case 'baclabor': $entry_block_bgcolor="#fff3f3";
                          $entry_border_bgcolor="#ee6666";
						  $entry_body_bgcolor="#ffffff";
						  $breakfile="nursing-station-patientdaten-doconsil-baclabor.php$append";
						  break;
  case 'patho': $entry_block_bgcolor="#cde1ec";
                          $entry_border_bgcolor="#cde1ec";
						  $entry_body_bgcolor="#ffffff";
						  $breakfile="nursing-station-patientdaten-doconsil-patho.php$append";
						  break;
  case 'blood': $entry_block_bgcolor="#99ffcc";
                          $entry_border_bgcolor="#99ffcc";
						  $entry_body_bgcolor="#ffffff";
						  $breakfile="nursing-station-patientdaten-doconsil-blood.php$append";
						  break;
  case 'radio': $entry_block_bgcolor="#efefef";
                          $entry_border_bgcolor="#fcfcfc";
						  $entry_body_bgcolor="#ffffff";
						  $breakfile="nursing-station-patientdaten-doconsil-radio.php$append";
						  break;
  default            : $entry_block_bgcolor="#fff3f3";
                          $entry_border_bgcolor="#ee6666";
						  $entry_body_bgcolor="#ffffff";
						  $breakfile="nursing-station-patientdaten-doconsil-baclabor.php$append";
}
*/
$breakfile=$root_path.'modules/radiology/'.$breakfile;   # burn added: Oct. 2, 2006
# $breakfile=$root_path.'modules/nursing/'.$breakfile;   
$thisfile=basename(__FILE__);
# Data to append to url
$append='&status='.$status.'&target='.$target.'&user_origin='.$user_origin."&dept_nr=".$dept_nr;

echo "radiology/radiology_undone_request.php : mode = '".$mode."' <br> \n";

# Initialize page´s control variables
echo "sub_dept_nr = '".$sub_dept_nr."' <br> \n";
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
	if (!empty($sub_dept_nr))
		$searchkey="r.dept_nr=".$sub_dept_nr;   # burn added :  July 23, 2007
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


if(($mode=='search'||$mode=='paginate')&&!empty($searchkey)){
	# Convert other wildcards
	$searchkey=strtr($searchkey,'*?','%_');
	# Save the search keyword for eventual pagination routines
	if($mode=='search') $HTTP_SESSION_VARS['sess_searchkey']=$searchkey;

	include_once($root_path.'include/inc_date_format_functions.php');
	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

#	$encounter=& $radio_obj->searchLimitEncounterBasicInfo($searchkey,$pagen->MaxCount(),$pgx,$oitem,$odir);
/*
searchLimitBasicInfoRadioPending
from 
searchLimitEncounterBasicInfoPending
*/
	$encounter=& $radio_obj->searchLimitBasicInfoRadioPending($searchkey,$pagen->MaxCount(),$pgx,$oitem,$odir);
	echo "radiology/radiology_undone_request.php : encounter = '".$encounter."' <br>";
	echo "radiology/radiology_undone_request.php : radio_obj->sql = '".$radio_obj->sql."' <br>";
	$i=0;
/*
    while($rowRequest=$encounter->FetchRow()){
			echo "Entry[".$i++."] : batch_nr = ".$rowRequest['batch_nr']." encounter_nr=".
			       $rowRequest['encounter_nr']." request_date=".$rowRequest['request_date'].
				   " dept_nr=".$rowRequest['dept_nr']." status=".$rowRequest['status']. 
				   " Lastname=".$rowRequest['name_last']." Firstname=".$rowRequest['name_first'].
				   " DOB=".$rowRequest['date_birth']." sex=".$rowRequest['sex'].
				   " pid=".$rowRequest['pid']." personell_nr=".$rowRequest['personell_nr'].
				   " trace=".$rowRequest['trace']." <br> ";	    
	 }
*/	echo "radiology/radiology_undone_request.php : thisfile = ".$thisfile." <br> ";
	echo "radiology/radiology_undone_request.php : totalcount above 1 = ".$totalcount." <br> ";
	# exit();
	//echo $radio_obj->getLastQuery();

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
		@$radio_obj->_searchBasicInfoRadioPending($searchkey);
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
echo "linecount = $linecount <br>";
echo "totalcount = $totalcount <br>";
echo "LDSearchFound = $LDSearchFound <br>";
echo "LDShowing = $LDShowing <br>";
echo "pagen->BlockStartNr() = ".$pagen->BlockStartNr()."<br>";
echo "LDTo = $LDTo <br>";
echo "pagen->BlockEndNr() = ".$pagen->BlockEndNr()."<br>";

if ($linecount) echo '<hr width=80% align=left>'.str_replace("~nr~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.';
	else echo str_replace('~nr~','0',$LDSearchFound); 


	echo "<br><b> \n";
	echo '<a href="'.$thisfile.URL_APPEND.'&mode=search&pgx='.$pgx.'&totalcount='.$totalcount.'&oitem='.$oitem.'&odir='.$odir.$append.'&searchkey=*">ALL</a>';
	echo "&nbsp;&nbsp;&nbsp;";
	echo '<a href="'.$thisfile.URL_APPEND.'&mode=search&pgx='.$pgx.'&totalcount='.$totalcount.'&oitem='.$oitem.'&odir='.$odir.$append.'&sub_dept_nr=164">General Radiography</a>';
	echo "&nbsp;&nbsp;&nbsp;";
	echo '<a href="'.$thisfile.URL_APPEND.'&mode=search&pgx='.$pgx.'&totalcount='.$totalcount.'&oitem='.$oitem.'&odir='.$odir.$append.'&sub_dept_nr=165">Ultrasound</a>';
	echo "&nbsp;&nbsp;&nbsp;";
	echo '<a href="'.$thisfile.URL_APPEND.'&mode=search&pgx='.$pgx.'&totalcount='.$totalcount.'&oitem='.$oitem.'&odir='.$odir.$append.'&sub_dept_nr=166">Special Procedures</a>';
	echo "&nbsp;&nbsp;&nbsp;";
	echo '<a href="'.$thisfile.URL_APPEND.'&mode=search&pgx='.$pgx.'&totalcount='.$totalcount.'&oitem='.$oitem.'&odir='.$odir.$append.'&sub_dept_nr=167">Computed Tomography</a>';
	echo '</b><br>';

if ($radio_obj->record_count) { 
	# Preload  common icon images
	$img_male=createComIcon($root_path,'spm.gif','0','',TRUE);
	$img_female=createComIcon($root_path,'spf.gif','0','',TRUE);
	$bgimg='tableHeaderbg3.gif';
	//$tbg= 'background="'.$root_path.'gui/img/common/'.$theme_com_icon.'/'.$bgimg.'"';
	$tbg= 'class="adm_list_titlebar"';
?>

<form name="dienstplanDoctorAssign" action="<?= $thisfile ?>" method="post">
				<table cellspacing="0 "cellpadding="0" border="0" class="frame">
				  <tbody>
						<tr>
						<td style="color:white; background-color: red; font-weight:bold;">
							&nbsp;List of Requestors
						</td>
					 </tr>
					 <tr>
						<td bgcolor="#ffffff">
							<table id="serviceTable" border="0" cellpadding="1" cellspacing="1" width="600" style="border:1px solid #666666;border-bottom:0px">
									<thead>
										<tr class="reg_list_titlebar" style="font-weight:bold;padding:0px">
											<td valign="middle" align="center" width="10%" rowspan="2">Patient ID</td>
											<td valign="middle" align="center" width="45%" rowspan="2">Requestor's Name</td>
											<td valign="middle" align="center" width="15%" rowspan="2">Date Requested</td>
											<td valign="middle" align="center" width="10%" rowspan="2">Patient Type</td>
											<td valign="middle" width="10%" align="center" rowspan="2">Edit</td>
											<td valign="middle" width="10%" align="center" rowspan="2">Delete</td>
										</tr>						
									</thead>
									<tbody>
									</tbody>
								</table>
								<table border="0" cellpadding="2" cellspacing="1" width="600" style="border:1px solid #666666;border-top:0px;margin-top:-1px">
									<tr class="reg_list_titlebar" >
									</tr>
									
								</table>
						  </td>
					 </tr>
				  </tbody>
				</table>
				<table border=0 cellpadding=2 cellspacing=1> 
					<tr bgcolor="#abcdef">				
<!--
 r.batch_nr, r.encounter_nr, r.request_date, r.dept_nr, r.status,
 p.name_last, p.name_first, p.date_birth, p.sex, p.pid, 
						s.personell_nr, s.trace
-->
						<td <?php echo $tbg; ?>><b>
							<?php echo "No.";  ?></b>
						</td>
						<td <?php echo $tbg; ?>><b>
							<?php echo $pagen->makeSortLink('Batch No.','batch_nr',$oitem,$odir,$append);  ?></b>
						</td>
						<td <?php echo $tbg; ?>><b>
							<?php echo $pagen->makeSortLink('Date Send','request_date',$oitem,$odir,$append);  ?></b>
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
							<?php echo "Track Record";  ?></b>
						</td>
						<td <?php echo $tbg; ?>><b>
							<?php echo "Resident in-charged";  ?></b>
						</td>
					</tr>
<?php

	$toggle=0;
	$my_count=1;
	require_once($root_path.'include/care_api_classes/class_request_sked.php');
	$sked_obj=new SegRequestSked;

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
		echo "						<td>&nbsp;</td> \n";
		echo "						<td>&nbsp; \n ";

         /* get the assign doctor, if any */
      $doctorInfo;
      if ($this->doctorInfo = $sked_obj->getDoctorName($rowRequest['batch_nr'])){
		 /* personell_nr, name_last, name_first, title, sex, pid */
		 $temp_row = $this->doctorInfo->FetchRow();
		 #array_walk($temp_row, 'test_print');
	     echo ' <input type="hidden" name="ID_doc'.$my_count.'" value="'.$temp_row['personell_nr'].'"> 
		      <input type="text" size="25" name="name_doc'.$my_count.'" onFocus=this.select() value="'.$temp_row['title'].'&nbsp;'.$temp_row['name_first'].'&nbsp;'.$temp_row['name_last'].'"> 
              <a href="javascript:popselectRadiologists(\''.$my_count.'\',\'doc\')"> 
	          <button onclick="javascript:popselectRadiologists(\''.$my_count.'\',\'doc\')"><img '.createComIcon($root_path,'patdata.gif','0').' alt="'.$LDClk2Plan.'"></button></a> ';
      }
      else{
	     echo ' <input type="hidden" name="ID_doc'.$my_count.'" value=""> 
		      <input type="text" size="25" name="name_doc'.$my_count.'" onFocus=this.select() value=""> 
              <a href="javascript:popselectRadiologists(\''.$my_count.'\',\'doc\')"> 
	          <button onclick="javascript:popselectRadiologists(\''.$my_count.'\',\'doc\')"><img '.createComIcon($root_path,'patdata.gif','0').' alt="'.$LDClk2Plan.'"></button></a> ';
      }
		echo "						</td> \n";
		echo "					</tr> \n";
		$my_count++;
   }/* end of while loop */
/*
					echo '
					<tr>
						<td colspan=6>'.$pagen->makePrevLink($LDPrevious,$append).'</td>
						<td align=right>'.$pagen->makeNextLink($LDNext,$append).'</td>
					</tr>
				</table>
			</form>';
*/
?>
					<tr>
						<td colspan=6><?php echo $pagen->makePrevLink($LDPrevious,$append); ?></td>
						<td align=right><?php echo $pagen->makeNextLink($LDNext,$append); ?></td>
					</tr>
				</table>
</form>
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
				<table width="100%" border=0 cellpadding=10 bgcolor="<?php echo $entry_border_bgcolor ?>">
					<tr>
						<td align="center" style="color:#FF0000; font-family:'Courier New', Courier, mono; font-style:italic; font-weight:bold; font-size:16px;"> 
							NO PENDING REQUEST 
						</td>
					</tr>
				</table>
				</div>
<?php
}
?>
			</ul>
&nbsp;
			<p>
		</td>
	</tr>
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
 require($root_path.'js/floatscroll.js');

?>