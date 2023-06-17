<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

require($root_path.'modules/nursing/ajax/nursing-station-new-common.php');



define('LANG_FILE','nursing.php');
$local_user='ck_pflege_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);
/* Load the ward object */
require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward($ward_nr);

$rows=0;

//$db->debug=1;
    /* Load the date formatter */
    include_once($root_path.'include/inc_date_format_functions.php');

#echo "mode = ".$mode;
	switch($mode){	
		case 'show': 
		{
			if($ward=&$ward_obj->getWardInfo($ward_nr)){
				$rooms=&$ward_obj->getAllActiveRoomsInfo();
				#echo "sql = ".$ward_obj->sql;
				$rows=true;
				extract($ward);
				// Get all medical departments
				/* Load the dept object */
/*				if($edit){
					include_once($root_path.'include/care_api_classes/class_department.php');
					$dept=new Department;							
					$depts=&$dept->getAllMedical();
				}
*/							
			}else{
				header('location:nursing-station-info.php'.URL_REDIRECT_APPEND);
				exit;
			}
			#$breakfile='nursing-station-info.php'.URL_APPEND;
			#edited by VAN 04-11-08
			$breakfile='nursing-station-info.php'.URL_APPEND.'&key='.$_GET['key'].'&pagekey='.$_GET['pagekey'];
			break;
		}
		
		case 'update':
		{
			$HTTP_POST_VARS['nr']=$HTTP_POST_VARS['ward_nr'];
			if($ward_obj->updateWard($ward_nr,$HTTP_POST_VARS)){
				header("location:nursing-station-info.php".URL_REDIRECT_APPEND."&edit=0&mode=show&ward_id=$station&ward_nr=$ward_nr");
				exit;
			}else{
				echo $ward_obj->getLastQuery()."<br>$LDDbNoSave";
			}
							
			break;
		}
		
		case 'close_ward':
		{
			if($ward_obj->hasPatient($ward_nr)){
				header("location:nursing-station-noclose.php".URL_REDIRECT_APPEND."&ward_id=$ward_id&ward_nr=$ward_nr");
				exit;
			}else{  
				switch($close_type)
				{
					case 'temporary':		
					{
						$ward_obj->closeWardTemporary($ward_nr);
						#echo "ward = ".$ward_obj->sql;
						break;
					}
					
					case 'nonreversible':	
					{
						$ward_obj->closeWardNonReversible($ward_nr);
						break;
					}
					
					case 're_open':	
					{
						$ward_obj->reOpenWard($ward_nr);
						#echo "ward = ".$ward_obj->sql;
						break;
					}
				}
				
				header("location:nursing-station-info.php".URL_REDIRECT_APPEND);
				exit;
			}
		}
							
		default:					
		{
			if($wards=&$ward_obj->getAllActiveWards()){
				# Count wards
				$rows=$wards->RecordCount();

				if($rows==1){
					# If only one ward, fetch the ward
					$ward=$wards->FetchRow();
					# globalize ward values
					extract($ward);
					# Get ward´s active rooms info
					$rooms=&$ward_obj->getAllActiveRoomsInfo($ward['nr']);
				}else{
					$rooms=$ward_obj->countCreatedRooms();
				}
			}else{
			 	//echo $ward_obj->getLastQuery()."<br>$LDDbNoRead";
			}
			
			#edited by VAN 04-11-08				
			$breakfile='nursing-station-manage.php?sid='.$sid.'&lang='.$lang;
			#$breakfile='nursing.php?sid='.$sid.'&lang='.$lang;
			#$breakfile='nursing-station-manage.php?sid='.$sid.'&lang='.$lang.'&key='.$_GET['key'].'&pagekey='.$_GET['pagekey'];
		}
	} # End of switch($mode)

# Start the smarty templating
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');

# Added for the common header top block

 $smarty->assign('sToolbarTitle',"$LDNursing $LDStation - $LDProfile");

 $smarty->assign('pbHelp',"javascript:gethelp('nursing_ward_mng.php','$mode','$edit')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDNursing $LDStation - $LDProfile");

 #added by VAN 04-11-08
 #$smarty->assign('sOnLoadJs','onLoad="preSet(); startAJAXSearch(\'search\', 0);"');

	if ($_GET['key'])
		$keyword = $_GET['key'];
	else
		$keyword = ' ';	
		
	if ($_GET['pagekey'])
		$page = $_GET['pagekey'];
	else
		$page = 0;	
		
 $smarty->assign('sOnLoadJs','onLoad="startAJAXSearch2(\''.$keyword.'\', '.$page.');"');
 
# Buffer page output

ob_start();

?>

<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	background-color:#0000ff;
	border:1px solid #4d4d4d;
}
.olcg {
	background-color:#aa00aa; 
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffcc; 
	text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
	font-family:Arial; font-size:13px; 
	font-weight:bold; 
	color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style> 


<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js" ></script>

<script type="text/javascript" src="./js/nursing-station-new.js"></script>

<style type="text/css" name="formstyle">

td.pblock{ font-family: verdana,arial; font-size: 12; background-color: #ffffff}
td.pv{ font-family: verdana,arial; font-size: 12; color: #0000cc; background-color: #eeeeee}
div.box { border: solid; border-width: thin; width: 100% }
div.pcont{ margin-left: 3; }

</style>

<script language="javascript">
<!-- 
function check(d){
	if((d.description.value=="")||(d.roomprefix.value=="")){
		alert("<?php echo $LDAlertIncomplete ?>");
		return false;
	}
	if(d.room_nr_start.value>=d.room_nr_end.value){
		alert("<?php echo $LDAlertRoomNr ?>");
		return false;
	}
}
function checkTempClose(){
	if(confirm("<?php echo $LDSureTemporaryClose ?>")) return true;
		else return false;
}
function checkReopen(){
	if(confirm("<?php echo $LDSureReopenWard ?>")) return true;
		else return false;
}
function checkClose(f){
	if(confirm("<?php echo $LDSureIrreversibleClose ?>")){
		f.close_type.value="nonreversible";
		f.submit();
		return true;
	}else{
		return false;
	}
}
// -->
</script>
<script>
	YAHOO.util.Event.on(window, "load", init);
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# If one station is available, show its profile

if($rows==1) {

	# Assign table items
	$smarty->assign('LDStation',$LDStation);
	$smarty->assign('LDWard_ID',$LDWard_ID);
	$smarty->assign('LDDept',$LDDept);
	$smarty->assign('LDDescription',$LDDescription);
	$smarty->assign('LDRoom1Nr',$LDRoom1Nr);
	$smarty->assign('LDRoom2Nr',$LDRoom2Nr);
	$smarty->assign('LDRoomPrefix',$LDRoomPrefix);
    //added by shand nursing mandatory excess-------------->>
    $smarty->assign('isViewMandatory',FALSE);
    #1 == charity, 2==payward
    if ($accomodation_type==1) {
       $smarty->assign('isViewMandatory',TRUE); 
    }
    $smarty->assign('LDMandatory','Mandatory Excess');
    $smarty->assign('segMandatory',number_format($mandatory_excess,2,'.',','));  
    //-------------------------
	$smarty->assign('LDCreatedOn',$LDCreatedOn);
	$smarty->assign('LDCreatedBy',$LDCreatedBy);

	# Assign input values
	$smarty->assign('name',$name);
	$smarty->assign('ward_id',$ward_id);
	$smarty->assign('dept_name',$dept_name);
	$smarty->assign('description',$description);
	$smarty->assign('room_nr_start',$room_nr_start);
	$smarty->assign('room_nr_end',$room_nr_end);
	$smarty->assign('roomprefix',$roomprefix);
	$smarty->assign('date_create',formatDate2Local($date_create,$date_format));
	$smarty->assign('create_id',$create_id);
	
	#added by VAN 04-11-08
	$smarty->assign('LDAccommodation','Accommodation Type');
	if ($accomodation_type==1)
		$accomodation = "Charity";
	elseif ($accomodation_type==2)
		$accomodation = "Payward";
		
	$smarty->assign('accommodation',$accomodation);
	#echo "nr = ".$ward_nr;
	$editward = '<div>
						<a href="javascript:void(0);"
						    onclick="EditWardForm('.$ward_nr.');"
				 		    onmouseout="nd();">
						<img name="edit_ward" id="edit_ward" src="'.$root_path.'gui/img/control/default/en/en_edit_ward.gif" border=0 alt="Edit Ward" title="Edit Ward"></a>
					</div>';
	/*
	$editroom = '<div>
						<a href="javascript:void(0);"
						    onclick="EditRoomForm(\''.$ward_id.'\');"
				 		    onmouseout="nd();">
						<img name="edit_room" id="edit_room" src="'.$root_path.'gui/img/control/default/en/en_edit_room.gif" border=0 alt="Edit Ward" title="Edit Ward"></a>
					</div>';				
	*/
	#$smarty->assign('LDEditWard','<input type="button" name="edit_ward" id="edit_ward" value="Edit Ward">');
	$smarty->assign('LDEditWard',$editward);
	#$smarty->assign('LDEditRoom','<input type="button" name="edit_room" id="edit_room" value="Edit Room">');
	#$smarty->assign('LDEditRoom',$editroom);
	
	#$smarty->assign('LDWardRate','Ward Rate');
	#$smarty->assign('ward_rate','Php '.number_format($ward_rate,2));
	#------------------
	
	# If rooms available, create list and show them

	if(is_object($rooms)){

		$smarty->assign('bShowRooms',TRUE);
		$smarty->assign('LDRoom',$LDRoom);
		$smarty->assign('LDBedNr',$LDBedNr);
		$smarty->assign('LDRoomShortDescription',$LDRoomShortDescription);
		
		$smarty->assign('LDRoomRate','Room Rate');
		$smarty->assign('LDRoomType','Room Type');

		$toggle=0;
		$sTemp='';
		while($room=$rooms->FetchRow()){
			if($toggle)	$trc='#dedede';
				else $trc='#efefef';
			$toggle=!$toggle;
			/*
			$sTemp=$sTemp.'
				<tr bgcolor="'.$trc.'">
				<td>&nbsp;'.strtoupper($ward['roomprefix']).' '.$room['room_nr'].'&nbsp;
				</td>
				<td class=pv >&nbsp;<font color="#ff0000">&nbsp;'.$room['nr_of_beds'].'</td>
				<td class=pv >&nbsp;'.$room['info'].'</td>
				<td class=pv align="right">&nbsp;'.number_format($room['room_rate'],2,".",",").'</td>
				</tr>';
			*/
			$sTemp=$sTemp.'
				<tr bgcolor="'.$trc.'">
				<td>&nbsp;'.strtoupper($ward['roomprefix']).' '.$room['room_nr'].'&nbsp;
				</td>
				<td class=pv >&nbsp;<font color="#ff0000">&nbsp;'.$room['nr_of_beds'].'</td>
				<td class=pv >&nbsp;'.$room['info'].'</td>
				<td class=pv >&nbsp;'.$room['roomtype'].'</td>
				<td class=pv align="right">&nbsp;'.number_format($room['room_rate'],2,".",",").'</td>
				</tr>';	
		}

		$smarty->assign('sRoomRows',$sTemp);
	}

	#$smarty->assign('sClose','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'close2.gif','0','absmiddle').' border="0"></a>');

	if($ward['is_temp_closed']){

		ob_start();
?>
		<form name="closer" method="post" action="<?php echo $thisfile ?>" onSubmit="return checkReopen()" onReset="return checkClose(this)">
			<input type="hidden" name="ward_nr" value="<?php echo $ward['nr'] ?>">
			<input type="hidden" name="mode" value="close_ward">
			<input type="hidden" name="close_type" value="re_open">
			<input type="hidden" name="sid" value="<?php echo $sid ?>">
			<input type="hidden" name="lang" value="<?php echo $lang ?>">
			<input type="hidden" name="ward_id" value="<?php echo $ward['ward_id'] ?>">
			<!--
			<input type="submit" value="<?php echo $LDReopenWard ?>">
			<input type="reset" value="<?php echo $LDIrreversiblyCloseWard ?>">
			-->
			<br>	
			<center>
			<!------edited by VAN 04-11-08 -->
			<table>
				<tr>
					<td><a href="<?=$breakfile?>"><img <?=createLDImgSrc($root_path,'close2.gif','0','absmiddle')?> border="0"></a></td>
					<td><input type="submit" value="<?php echo $LDReopenWard ?>"></td>
					<td><input type="reset" value="<?php echo $LDIrreversiblyCloseWard ?>"></td>
				</tr>
			</table>
			</center>
		</form>
<?php

		$sTemp=ob_get_contents();
		ob_end_clean();

		$smarty->assign('sWardClosure',$sTemp);

	}else{
		ob_start();
?>
<form name="closer" method="post" action="<?php echo $thisfile ?>" onSubmit="return checkTempClose()" onReset="return checkClose(this)">
				<input type="hidden" name="ward_nr" value="<?php echo $ward['nr'] ?>">
				<input type="hidden" name="mode" value="close_ward">
				<input type="hidden" name="close_type" value="temporary">
				<input type="hidden" name="sid" value="<?php echo $sid ?>">
				<input type="hidden" name="lang" value="<?php echo $lang ?>">
				<input type="hidden" name="ward_id" value="<?php echo $ward['ward_id'] ?>">  
				<!--
				<input type="submit" value="<?php echo $LDTemporaryCloseWard ?>">
				<input type="reset" value="<?php echo $LDIrreversiblyCloseWard ?>">
				-->
			<br>	
			<center>
			<table border="0">
				<tr>
					<td><a href="<?=$breakfile?>"><img <?=createLDImgSrc($root_path,'close2.gif','0','absmiddle')?> border="0"></a></td>
					<td><input type="submit" value="<?php echo $LDTemporaryCloseWard ?>"></td>
					<td><input type="reset" value="<?php echo $LDIrreversiblyCloseWard ?>"></td>
				</tr>
			</table>
			</center>
		</form>
<?php

		$sTemp=ob_get_contents();
		ob_end_clean();

		$smarty->assign('sWardClosure',$sTemp);
	
	}

}elseif($rows){
	
	# If more than one station available, create list and show

	ob_start();

?>
	<!--<ul>-->
	<font class="prompt"><?php echo $LDExistStations ?></font><p>
<script>
	//edited by VAN 04-09-08
	//xajax_PopulateRow('<?=$ward_nr?>');
	
	//var keyword=' ';
	//var page = 0;
	/*
	var keyword = "<?= $_GET['key']?>";
	var page = "<?= $_GET['pagekey']?>";

	if (keyword)
		keyword = keyword
	else
		keyword = ' ';	
		
	if (page)
		page = page
	else
		page = 0;	
		
	
	if (AJAXTimerID) clearTimeout(AJAXTimerID);
	AJAXTimerID = setTimeout("xajax_PopulateRow('search','"+keyword+"',"+page+")",50);
	*/
</script>
<div style="padding-left:10px">
<form action="<?php echo $thisfile?>" method="post" name="suchform" onSubmit="">
	<div id="tabFpanel">
		<div align="center" style="display:">
			<table width="100%" cellpadding="4">
				<tr>
					<td width="30%" align="center">
						<span>Enter search keyword: Ward ID or name, all data (just type: * or space)</span>
						<br><br>
						<input id="search" name="search" class="segInput" type="text" size="30" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0)" />
						<input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" /><br />
						<br>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?php
			#echo "key = ".$_GET['key'];
			#echo "<br>pagekey = ".$_GET['pagekey'];
			if ($_GET['key'])
				$key = $_GET['key'];
			else
				$key = '*';
				
			if ($_GET['pagekey'])
				$pagekey = $_GET['pagekey'];
			else
				$pagekey = 0;	
					
	?>
	<input type="hidden" name="key" id="key" value="<?=$key?>">
	<input type="hidden" name="pagekey" id="pagekey" value="<?=$pagekey?>">
</form>
<div>
<div>
	<table border="0" cellpadding="0" cellspacing="0">
		<!--<tr align="left"><td><button id="btnaddWard">Add</button></td></tr>	-->
		<tr align="left">
			<td>
				<img <?php echo createLDImgSrc($root_path,'add_new.gif','0','absmiddle') ?> id="btnaddWard" name="btnaddWard" border="0" style="cursor:pointer" >
			</td>
		</tr>	
		<input type="hidden" name="rpath" id="rpath" value="<?=$root_path?>"/>
		<input type="hidden" name="sid" id="sid" value="<?=$sid?>" />
		<input type="hidden" name="url_append" id="url_append" value="<?=URL_APPEND?>" />
	</table>
</div>
<br>
<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; overflow-x:hidden; height:45px; width:99%; background-color:#e5e5e5">	
	<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
		<thead>
			<tr class="nav">
			<th colspan="9">
				<div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE)">
					<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
					<span title="First">First</span>
				</div>
				<div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
					<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
					<span title="Previous">Previous</span>
				</div>
				<div id="pageShow" style="float:left; margin-left:10px">
					<span></span>
				</div>
				<div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
					<span title="Last">Last</span>
					<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
				</div>
				<div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
					<span title="Next">Next</span>
					<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
				</div>
			</th>
		</tr>
			<tr>
				<th width="3%"></th>
				<th width="20%" align="left">&nbsp;&nbsp;&nbsp;<?=$LDStation?></th>
				<th width="22%" align="left"><?=$LDWard_ID?></th>
				<th width="*" align="left"><?=$LDDescription?></th>
				<th width="15%">Type</th>
				<th width="1%">&nbsp;</th>
				<th width="15%" align="left"><?=$LDStatus?></th>				
			</tr>
		</thead>
	</table>
</div>

<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; overflow-x:hidden; height:305px; width:99%; background-color:#e5e5e5">	
	<table id="wardList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
		<tbody id="twardList">
			<!-- list of ward display -->
		</tbody>
	</table>
<!--<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>	-->
<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" />	
</div>
	<p>
	<a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'close2.gif','0','absmiddle') ?> border="0"></a>
	<!--</ul>-->
<?php

	$sTemp = ob_get_contents();
	ob_end_clean();

}else{

	# If no wards available, prompt no ward
	
	$sTemp = '<p><font size=2 face="verdana,arial,helvetica">'.$LDNoWardsYet.'<br><img '.createComIcon($root_path,'redpfeil.gif','0','absmiddle').'> <a href="nursing-station-new.php'.URL_APPEND.'">'.$LDClk2CreateWard.'</a></font>';

}

if($rows==1){
	$smarty->assign('sMainBlockIncludeFile','nursing/ward_profile.tpl');
}else{
	# Assign the page output to main frame template
	$smarty->assign('sMainFrameBlockData',$sTemp);
}

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
