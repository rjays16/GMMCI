<?php
	# Start Smarty templating here
 /**
 * LOAD Smarty
 */
$_GET['popUp'] = 1;
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');


/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
	$lang_tables[]='search.php';
	$lang_tables[]='actions.php';
	define('LANG_FILE','or.php');
#	define('NO_2LEVEL_CHK',1);

#added by VAN 02-07-08
	define('NO_2LEVEL_CHK',1);

	$local_user='ck_op_pflegelogbuch_user';   # burn added : October 2, 2007
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');

	require($root_path.'modules/or/ajax/op-request-new.common.php');

	# Create global config object
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/inc_date_format_functions.php');

	/* Create the personell object */
	require_once($root_path.'include/care_api_classes/class_personell.php');
	$pers_obj=new Personell;

	# Create operation billing object
	require_once($root_path.'include/care_api_classes/billing/class_ops.php');
	$ops_obj = new SegOps;

switch($personnel_type)
{
	case 'doctor':
							#only doctor that belong in Radiology Department
							$element='doctor';
							//$maxelement=10;
							$quickid='Doctor';
							$quicklist=$pers_obj->getDoctorsOfDept($dept_nr);
							break;
	default:{header('Location:'.$root_path.'language/'.$lang.'/lang_'.$lang.'_invalid-access-warning.php'); exit;};
}

#echo "seg-op-request-select-personnel.php : quicklist : <br> \n"; print_r($quicklist); echo " <br> \n";
$list_personnel ='';
if ($quicklist){
	while($pers_info = $quicklist->FetchRow()){
#		echo "<br>\n";
#		print_r($pers_info);

		$personnel_fullname=trim($pers_info['name_last']).', '.trim($pers_info['name_first']);
		$personnel_fullname_temp = $pers_info['name_first'];
		if (!empty($pers_info['name_middle'])){
			$personnel_fullname .= ' <font style="font-style:italic; color:#FF0000">'.trim($pers_info['name_middle']).'</font>';
			$personnel_fullname_temp .= ' '.strtoupper(substr(trim($pers_info['name_middle']),0,1)).'.';
		}
		$personnel_fullname_temp .= ' '.$pers_info['name_last'];

		$list_personnel .= '
			<tr>
				<td>'.$personnel_fullname.'
					<input type="hidden" name="personnel'.$pers_info['personell_nr'].'" id="personnel'.$pers_info['personell_nr'].'" value="'.$personnel_fullname_temp.'">
				</td>
				<td>&nbsp;</td>
				<td>
					<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px" onclick="prepareSelect('.$pers_info['personell_nr'].')">
				</td>
			</tr>';
	}#end of while loop
}
#[personell_nr] => 100105 [name_last] => Ferrer [name_first] => Odelio [name_middle] => Y)
#exit();
//print_r($quicklist);

if($pers_obj->record_count) $quickexist=true;


#	global $db;

	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('or');

 # Title in the title bar
# $smarty->assign('sToolbarTitle',"$LDRadiology::$LDDiagnosticTest");
 $smarty->assign('sToolbarTitle',"Radiolgy :: Co-reader Physician :: ".$pers_type);

		# href for the help button
#	$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");
	$smarty->assign('pbHelp',"");

		# href for the close button
#	$smarty->assign('breakfile',$breakfile);
		# CLOSE button for pop-ups
	$breakfile = 'javascript:window.parent.pSearchClose();';
	$smarty->assign('breakfile',$breakfile);
	$smarty->assign('pbBack','');

 # Window bar title
 $smarty->assign('sWindowTitle',"Radiolgy :: Co-reader Physician");

 # Assign Body Onload javascript code

# $onLoadJS='onLoad="preSet();"';
 #echo "onLoadJS = ".$onLoadJS;
# $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

ob_start();
	 # Load the javascript code
		$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
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
.olfgright {text-align: right;}
.olfgjustify {background-color:#cceecc; text-align: justify;}

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
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>

			<!-- START for settin the DATE (NOTE: should be IN this ORDER...i think soo..) -->
<script type="text/javascript" language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script language="javascript" src="<?=$root_path?>js/dtpick_care2x.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
			<!-- END for setting the DATE (NOTE: should be IN this ORDER...i think soo..) -->

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>


<script type="text/javascript" language="javascript">
<!--
	function prepareSelect(nr){
		var details = new Object();

		details.id= nr;
		details.name_pers = $('personnel'+nr).value;

		var msg = "details='"+details+
					 "\ndetails.id='"+details.id+
					 "'\ndetails.name_pers='"+details.name_pers+
					 "'\nnr='"+nr+
					 "'\n$('personnel'+nr).value='"+$('personnel'+nr).value+"'\n";
//	alert("prepareSelect :: \n"+msg);
		result = window.parent.appendPersonnel(window.parent.$($F('table_name')),$F('personnel_type')+'[]',details);
	}//end of function prepareSelect
-->
</script>

<?php
	if ($popUp=='1'){
		#echo $reloadParentWindow;
	}
	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->append('JavaScript',$sTemp);

	ob_start();
	$sTemp='';
?>
	<table id="personnel-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr id="personnel-list-header">
				<th width="*" nowrap align="center">&nbsp;&nbsp;Name of <?=$pers_type?>(s)</th>
				<th width="5%"></th>
				<th width="15%">Select</th>
			</tr>
		</thead>
		<tbody>
			<!-- list of personnel -->
			<?=$list_personnel?>
		</tbody>
	</table>

<?php
	$sTemp = ob_get_contents();
	ob_end_clean();

	$smarty->assign('sPersonnelList',$sTemp);

	ob_start();
	$sTemp='';
?>

	<input type="hidden" name="submit" value="1">
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">
<!--
	<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
-->
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">

	<input type="hidden" name="dept_nr" id="dept_nr" value="<?=$dept_nr?$dept_nr:'0'?>">
	<input type="hidden" name="personnel_type" id="personnel_type" value="<?=$personnel_type?$personnel_type:''?>">
	<input type="hidden" name="table_name" id="table_name" value="<?=$table_name?$table_name:''?>">

<?php
#echo "seg-op-request-select_doctor.php : HTTP_SESSION_VARS : "; print_r($HTTP_SESSION_VARS); echo " <br><br> \n";
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sHiddenInputs',$sTemp);
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';alert($F(\'mode\'));" onsubmit="return false;" style="cursor:pointer">');
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';alert($F(\'mode\'));" style="cursor:pointer">');
/*
if (($mode=="update") && ($popUp!='1')){
	$sBreakImg ='cancel.gif';
	$smarty->assign('sBreakButton','<input type="image" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' align="center" alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';" style="cursor:pointer">');
}elseif ($popUp!='1'){
	$sBreakImg ='close2.gif';
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}
$smarty->assign('sContinueButton','<input type="image" src="'.$root_path.'images/btn_submit.gif" align="center">');
*/
$sBreakImg ='close2.gif';
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="'.$breakfile.'" onsubmit="return false;" style="cursor:pointer">');
# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','or/op-request-select-doctor.tpl');
$smarty->display('common/mainframe.tpl');

?>