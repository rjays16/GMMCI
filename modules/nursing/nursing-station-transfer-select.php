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
$lang_tables=array('prompt.php');
define('LANG_FILE','nursing.php');
$local_user='ck_pflege_user';
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'include/care_api_classes/class_ward.php');
## Load all wards info
$ward_obj=new Ward;
$items='nr,ward_id,name,is_temp_closed';
$ward_info=&$ward_obj->getAllWardsItemsObject($items);
$ward_count=$ward_obj->LastRecordCount();

$modetransfer = $_GET['modetransfer'];

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');

# Title in toolbar
 $smarty->assign('sToolbarTitle', $LDTransferPatient);

	# hide back button
 $smarty->assign('pbBack',FALSE);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('inpatient_transfer_select.php','$element','','','$title')");

 # href for close button
 $smarty->assign('breakfile',"javascript:window.close();");

 # OnLoad Javascript code
 $smarty->assign('sOnLoadJs','onLoad="if (window.focus) window.focus();"');

 # Window bar title
 $smarty->assign('sWindowTitle',$LDTransferPatient);

 # Hide Copyright footer
 $smarty->assign('bHideCopyright',TRUE);

 if ($_GET['waiting'])
		$waiting = $_GET['waiting'];
 elseif ($_POST['waiting'])
		$waiting = $_POST['waiting'];
 else
		$waiting = 0;

 # Collect extra javascript code

 ob_start();
?>

<script language="javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript">
<!--
var urlholder;

/*
function TransferWard(wd){
	<?php
		echo '
			urlholder="nursing-station-transfer-save.php?mode=transferward&sid='.$sid.'&lang='.$lang.'&pyear='.$pyear.'&pmonth='.$pmonth.'&pday='.$pday.'&pn='.$pn.'&station='.$station.'&ward_nr='.$ward_nr.'&trwd="+wd;
		';
	?>

	window.opener.location.replace(urlholder);
	window.close();
}
*/
//edited by VAN 01-24-08
function TransferWard(wd, pn, pw){
	var modetransfer;
	var waiting='<?=$waiting?>';
	var waiting = document.getElementById('waiting').value;
    var popUp = $('popUp').value;
    
	//if ((document.getElementById('correct').checked)||(waiting==1))
	if (document.getElementById('correct').checked)
		// to correct the ward assignment
		modetransfer = 'correct';
	else
		// to transfer to another ward
		modetransfer = 'transferward';

	//urlholder="nursing-station-assignwaiting.php<?php echo URL_REDIRECT_APPEND ?>&pn="+pn+"&pat_station="+pw+"&ward_nr="+wd+"&station="+pw+"&transfer=1&modetransfer=transferward";

	//edited by Cherry 10-27-10
	urlholder="nursing-station-assignwaiting.php<?php echo URL_REDIRECT_APPEND ?>&pn="+pn+"&pat_station="+pw+"&ward_nr="+wd+"&station="+pw+"&transfer=1&modetransfer="+modetransfer+"&waiting="+waiting+"&popUp="+popUp;
	//asswin<?php echo $sid ?>=window.open(urlholder,"asswind<?php echo $sid ?>","width=650,height=600,menubar=no,resizable=yes,scrollbars=yes");
	window.location.href = urlholder;

	/*return overlib(
					OLiframeContent('<?php echo $root_path ?>modules/nursing/nursing-station-assignwaiting.php<?php echo URL_REDIRECT_APPEND ?>&pn='+pn+'&pat_station='+pw+'&ward_nr='+wd+'&station='+pw+'&transfer=1&modetransfer='+modetransfer+'&waiting='+waiting,
																	800, 400, 'fGroupTray', 0, 'auto'),
																	WIDTH,800, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick=" pSearchClose();">',
																 CAPTIONPADDING,2, CAPTION,'Assign Bed',
																 MIDX,0, MIDY,0,
																 STATUS,'Assign Bed');        */

	//window.close();
}

// -->
</script>

<?php

$sTemp = ob_get_contents();

ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

if ($modetransfer=='correct')
	$checked = 'checked';
else
	$checked = '';

?>
<form method="post" name="transbed" id="transbed" action="nursing-station-assignwaiting.php">
<table border=0>
	<tr>
		<td><input type="checkbox" name="correct" <?=$checked?> id="correct" value="1" style="cursor:pointer" /></td>
	<td><font color="RED"><strong>PLS CHECK IF YOU WANT TO CORRECT THE WARD ASSIGNMENT</strong></font></td>
	</tr>
</table>


<table border=0>
	<tr>
		<td><img <?php 	echo createMascot($root_path,'mascot2_r.gif','0'); ?>></td>
		<td class="prompt"><?php 	echo $LDWhereToTransfer; ?></td>
	</tr>
</table>

 <table border=0 cellpadding=4 cellspacing=1 width=100%>
	 <?php
				if($wardrow=&$ward_obj->getWardInfo($ward_nr)){
					$station =  $wardrow[ward_id];
				}
	 ?>
	<tr>
		<td colspan=2 bgcolor="#f6f6f6"><?php 	echo $LDTransferToBed.' ('.$station.')'; ?></td>
		<td bgcolor="#f6f6f6">
<input type="submit" value="<?php echo $LDShowBeds; ?>" style="cursor:pointer">
<input type="hidden" name="sid" value="<?php echo $sid; ?>">
<input type="hidden" name="lang" value="<?php echo $lang; ?>">
<input type="hidden" name="pn" value="<?php echo $pn; ?>">
<input type="hidden" name="ward_nr" value="<?php echo $ward_nr; ?>">
<input type="hidden" name="station" value="<?php echo $station; ?>">
<input type="hidden" name="pat_station" value="<?php echo $station; ?>">
<input type="hidden" name="transfer" value="1">

<input type="hidden" name="waiting" id="waiting" value="<?=$waiting?>">
<input type="hidden" name="popUp" id="popUp" value="<?=$_GET['popUp']?>">

	</td>
	</tr>

<tr>
		<td colspan=3>&nbsp;</td>
	</tr>
	<tr bgcolor="#f6f6f6">
		<td colspan=3><?php echo $LDTransferToWard; ?></td>
	</tr>

<?php

while($ward=$ward_info->FetchRow()){
	if($ward['nr']==$ward_nr) continue;
	#commented by VAN 01-24-08
	/*
	echo '<tr bgcolor="#f6f6f6"><td>'.$ward['ward_id'].'</td>
	 <td>'.$ward['name'].'</td>
	 <td><a href="javascript:TransferWard(\''.$ward['nr'].'\')"><img '.createLDImgSrc($root_path,'transfer_sm.gif','0').'></a></td></tr>';
	*/
	if ($ward['is_temp_closed'])
		$button = '<font color="RED"><strong>Temporarily closed</strong></font>';
	else
		$button = '<a href="javascript:TransferWard(\''.$ward['nr'].'\',\''.$pn.'\',\''.$station.'\')"><img '.createLDImgSrc($root_path,'transfer_sm.gif','0').'></a>';
	/*
	echo '<tr bgcolor="#f6f6f6"><td>'.$ward['ward_id'].'</td>
	 <td>'.$ward['name'].'</td>
	 <td><a href="javascript:TransferWard(\''.$ward['nr'].'\',\''.$pn.'\',\''.$station.'\')"><img '.createLDImgSrc($root_path,'transfer_sm.gif','0').'></a></td></tr>';
	*/
	echo '<tr bgcolor="#f6f6f6"><td>'.$ward['ward_id'].'</td>
	 <td>'.$ward['name'].'</td>
	 <td>'.$button.'</td></tr>';
}

?>

</table>
</form>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the page output to the mainframe center block

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

 ?>
