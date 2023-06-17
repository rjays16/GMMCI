<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

#added by VAN 06-24-08
require($root_path.'modules/or/ajax/op-request-new.common.php');

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables=array('departments.php');

define('LANG_FILE','or.php');
$local_user='ck_op_pflegelogbuch_user';

#added by VAN 02-07-08
define('NO_2LEVEL_CHK',1);

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/inc_config_color.php'); // load color preferences
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
$thisfile=basename(__FILE__);

switch($target){
	case 'search': $title="$LDOrLogBook :: $LDSearch";
						//$fileforward='op-pflege-logbuch-such-javastart.php'.URL_APPEND.'&retpath='.$retpath;
						$targetfile='op-pflege-logbuch-such-javastart.php';
						break;
	case 'archiv': $title="$LDOrLogBook :: $LDArchive";
						//$fileforward='op-pflege-logbuch-arch-javastart.php'.URL_APPEND.'&retpath='.$retpath;
						$targetfile='op-pflege-logbuch-arch-javastart.php';
						break;

	default: $title=$LDOrLogBook;
						//$fileforward='op-pflege-logbuch-javastart.php'.URL_APPEND.'&retpath='.$retpath;
						$targetfile='op-pflege-logbuch-javastart.php';
						break;
}

//Very confusing
$title= "OR New Request :: Department ";
$targetfile='seg-op-request-new.php';
$dept_ok=false;
$or_ok=false;

if ($target=='or_main') {
		$targetfile='seg-or-main.php';
}
/*
elseif ($target=='or_main_request') {
		$targetfile = $root_path.'modules/or/or_main/or_main_request.php';
		//$targetfile = $root_path.'modules/or/or_asu/or_asu_request.php';
}

elseif ($target=='or_main_request_get') {
		$targetfile = $root_path.'modules/or/or_main/or_main_request_get.php';
		//$targetfile = $root_path.'modules/or/or_asu/or_asu_request_get.php';
}  */
elseif ($target=='or_asu_request') {
	$targetfile = $root_path.'modules/or/or_asu/or_asu_request.php';
}

elseif ($target=='or_asu_request_get') {
		//$targetfile = $root_path.'modules/or/or_main/or_main_request_get.php';
		$targetfile = $root_path.'modules/or/or_asu/or_asu_request_get.php';
}

elseif ($target=='or_main_new_request'){
		$targetfile = $root_path.'modules/or/or_main/or_main_request.php';
}

elseif ($target=='or_main_new_request_get') {
		$targetfile = $root_path.'modules/or/or_main/or_main_request_get.php';
}


if(isset($_POST['dept_nr'])&&!empty($_POST['dept_nr'])&&$dept_obj->isSurgery($_POST['dept_nr'])){
	$dept_nr=$_POST['dept_nr'];
	$dept_ok=true;
}
if(isset($_POST['saal'])&&!empty($_POST['saal'])&&$dept_obj->isOR($_POST['saal'])){
	$saal=$_POST['saal'];
	$or_ok=true;
}

/*if($dept_ok&&$or_ok){
	#header("Location:$targetfile".URL_REDIRECT_APPEND."&dept_nr=".$dept_nr."&op_room=".$saal."&op_nr=".$op_nr."&retpath=".$retpath);
	#edited by VAN 08-08-08
	header("Location:$targetfile".URL_REDIRECT_APPEND."&dept_nr=".$dept_nr."&op_room=".$saal."&op_nr=".$op_nr."&retpath=".$retpath."&area=".$area."&pid=".$pid."&encounter_nr=".$encounter_nr);
	exit;
} */


$popUp = $_GET['popUp'];


if ($_GET['encounter_nr'])
	$encounter_nr = $_GET['encounter_nr'];

if ($_GET['area'])
	$area = $_GET['area'];

if ($_GET['pid'])
	$pid = $_GET['pid'];

#added by CHA, April 6, 2010
if($_GET['ptype'])
	$ptype = $_GET['ptype'];



	//edited by CHA 10-03-09, 04-06-2010
		global $db;
		$sql = "select current_dept_nr from care_encounter where encounter_nr=".$db->qstr($encounter_nr);
		$result=$db->Execute($sql);
		if($result)
		{
			$row=$result->FetchRow();
			$dept_nr = $row['current_dept_nr'];
		}
		header("Location:$targetfile".URL_REDIRECT_APPEND."&dept_nr=".$dept_nr."&area=".$area."&pid=".$pid."&encounter_nr=".$encounter_nr."&ptype=".$ptype);
	//end cha

#$breakfile = $root_path.'main/op-doku.php'.URL_APPEND;
if ($popUp!='1'){
	# href for the close button
	$breakfile = $root_path.'main/op-doku.php'.URL_APPEND;
}else{
	 # CLOSE button for pop-ups
	 $breakfile = 'javascript:window.parent.cClick();';
}


/* Load the department list with oncall doctors */
		$dept_DOC=&$dept_obj->getAllActiveWithSurgery();
if(is_array($dept_DOC)) $dlen=sizeof($dept_DOC);
	else $dlen=0;

$ORNrs=&$dept_obj->getAllActiveORNrs();
#echo "op-nursing-select-dept.php :: dept_obj->sql ='".$dept_obj->sql."'<br> \n";
#echo "op-nursing-select-dept.php :: ORNrs : <br>\n"; print_r($ORNrs); echo"<br> \n";
if(is_object($ORNrs)) $slen=$ORNrs->RecordCount();
	else $slen=0;



# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');

# Title in toolbar
 $smarty->assign('sToolbarTitle',$title);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_op_select.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',$title);

# Collect js code

ob_start();


?>
<script language="javascript">

function ShowContinueButton(objValue){
	if (objValue)
		document.getElementById('button_continue').style.display = '';
	else
		document.getElementById('button_continue').style.display = 'none';
}

</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>

<ul>
<form action="<?php echo $thisfile ?>" method="post" name="dept_select">
<table  cellpadding="2"  border=0>
	<tr>
		<td valign="bottom">
		<img <?php echo createComIcon($root_path,'angle_down_l.gif','0','bottom') ?> align="top">
	</td>
	<td valign="top">

		<font class="prompt">
		<?php echo $LDPlsSelectDept; ?>

	</td>
	<td  valign="top">&nbsp;

	</td>

	<tr>
		<td colspan=2 valign="top">

	<!--  The department list block  -->
		<table  cellpadding="2" cellspacing=0 border="0">
		<?php

			$toggler=0;
			#echo "dept_DOC=".$dept_DOC;

			while(list($x,$v)=each($dept_DOC)){

				$bold='';
				$boldx='';
				if($dept_nr==$v['nr']) 	{ echo '<tr bgcolor="yellow">'; $bold="<font color=\"red\" size=2><b>";$boldx="</b></font>"; }
					elseif ($toggler==0){ echo '<tr class="wardlistrow1">'; }
						else { echo '<tr class="wardlistrow2">';}
				$toggler=!$toggler;
				echo '<td >&nbsp;';

				echo '<input type="radio" name="dept_nr" value="'.$v['nr'].'"';

				if($dept_nr==$v['nr']) echo ' checked';

				echo '">&nbsp;&nbsp;'.$bold;

				if(isset($$v['LD_var'])&&!empty($$v['LD_var'])) echo $$v['LD_var'];
					else echo $v['name_formal'];
				echo $boldx.'&nbsp;</td>';
				echo '<td >&nbsp;';


				echo '</td></tr>';
						echo "\n";
			}
		?>
		</table>
	<!--  End of department list block  -->

	</td>
		<td>
	<!--  Start of the OR room numbers block -->
		<table  cellpadding="2" cellspacing=0 border="0">
				<tr>
						<td><font class="prompt"><?php echo $LDSelectORoomNr; ?></td>
				</tr>
			<tr>
			<td>
			<!-- edited by VAN 06-24-08 -->
			<table id="ORRoomList"  cellpadding="2" cellspacing=0 border="0">
			<tbody id="ORRoomList-body">
		<?php

		if(is_object($ORNrs)){
			$toggler=0;

			while($room=$ORNrs->FetchRow()){

				$bold='';
				$boldx='';
				if($saal==$room['room_nr']) 	{ echo '<tr bgcolor="yellow">'; $bold="<font color=\"red\" size=2><b>";$boldx="</b></font>"; }
					elseif ($toggler==0){ echo '<tr class="wardlistrow1">'; }
						else { echo '<tr class="wardlistrow2">';}
				$toggler=!$toggler;
				echo '<td >&nbsp;';

				echo '<input type="radio" name="saal" value="'.$room['room_nr'].'" id="'.$room['nr'].'" onClick="document.getElementById(\'op_nr\').value=this.id; ShowContinueButton(this.value);"';
				if($saal==$room['room_nr']) echo ' checked';
				echo '>&nbsp;&nbsp;'.$bold;
				echo $LDORoom.' '.$room['room_nr'];

				echo '&nbsp;</td>';

				echo '<td >&nbsp;';

				echo $LDORoom.' '.$room['info'];

				echo '&nbsp;</td>
						</tr>
						';
						echo "\n";
			}
		}

		?>
		</tbody>
		</table>
		</td>
		</tr>
		</table>
	<!--  End of the OR room numbers block -->
	</td>
	</tr>
	</tr>


		<tr>
			<td colspan=2><a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> alt="<?php echo $LDCloseAlt ?>"></a></td>
			<td align="right" id="button_continue" style="display:none"><input type="image" <?php echo createLDImgSrc($root_path,'continue.gif','0') ?>></td>
		</tr>

</table>
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<!--
<input type="hidden" name="target" value="<?php echo $target ?>">
-->

<input type="hidden" name="target" value="<?=$target?>" /> <!-- Edited by Omick December 03, 2008 (old_value: or_new_request)-->
<input type="hidden" name="op_nr" id="op_nr" value="">
<!-- added by VAN 08-08-08 -->
<input type="hidden" name="encounter_nr" id="encounter_nr" value="<?=$encounter_nr?>">
<input type="hidden" name="area" id="area" value="<?=$area?>">
<input type="hidden" name="pid" id="pid" value="<?=$pid?>">
<!-- -->
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
