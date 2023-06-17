<?php
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
define('NO_CHAIN',1);
require_once $root_path.'include/inc_front_chain_lang.php';


# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
require_once $root_path.'gui/smarty_template/smarty_care.class.php';

//$smarty->assign('bHideTitleBar',TRUE);
//$smarty->assign('QuickMenu',FALSE);
//$smarty->assign('bHideCopyright',TRUE);

$title='OR Main';
if (!$_GET['from'])
	$breakfile=$root_path."main/op-doku.php".URL_APPEND."&userck=$userck";
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/or/request/op_request_pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}
$smarty = new smarty_care('common');
$smarty->assign('sToolbarTitle',"OR Main::List of Schedules");
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");
$smarty->assign('breakfile',$breakfile);
$smarty->assign('sWindowTitle',"OR Main::List of Schedules");
$smarty->assign('sOnLoadJs','onLoad=""');

$thisfile='or_view_schedule.php';


global $db;


# Collect javascript code

ob_start();
# Load the javascript code
?>
<link rel="stylesheet" href="<?=$root_path?>modules/or/js/scheduleViewer/viewer.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/builder.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/effects.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/dragdrop.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$root_path?>modules/or/js/scheduleViewer/ScheduleViewer.js"></script>
<style type="text/css">
ul.doodad {
	list-style: none;
	margin: 0;
	padding: 0;
}

ul.doodad li {
	margin: 2px;
	text-align: left;
}

ul.doodad li label {
	font: bold 12px Arial;
	color: yellow;
	width: 75px;
	display: inline-block;
	text-shadow: 0 1px 0 #000;
}

ul.doodad li span {
	display: inline-block;
	color: #fff;
	font: bold 12px Arial;
	width: 180px;
	text-shadow: 0 1px 0 #000;
}


</style>
<script type="text/javascript">

var sv;

function changeDepartment()
{
	if ('undefined' == typeof sv) return false;
	var selected = $('department').options[$('department').selectedIndex];
	var priority = $('priority').options[$('priority').selectedIndex];	//added by cha, 11-18-2010
	$('scheduleViewer').select('.title').first().update(selected.text+' ('+priority.text+')');
	sv.me.parameters.dept = selected.value;
	sv.me.parameters.module = selected.getAttribute('module');
	sv.me.parameters.priority = $('priority').value;	//added by cha, 11-18-2010
	sv.fetch();
}


function tooltip (text) {
	return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}

function Doodad(d) {
	return '<ul class="doodad">'+
		'<li><label>PID:</label><span>'+d.Pid+'</span></li>'+
		'<li><label>Name:</label><span>'+d.Name+'</span></li>'+
		'<li><label>Schedule:</label><span>'+d.Schedule+'</span></li>'+
		'<li><label>Procedure:</label><span>'+d.Procedure+'</span></li>'+
		'<li><label>Status:</label><span>'+d.Status+'</span></li>'+
		'<li><label>Priority:</label><span>'+d.Priority+'</span></li>'+
		'</ul>';
}

document.observe("dom:loaded", function() {
	sv = ScheduleViewer.create('scheduleViewer', {
		id: '__sv',
		url: '../ajax/orSchedules.ajax.php',
		width: 'auto',
		height: 'auto',
		title: $('department').options[$('department').selectedIndex].text+ ($('priority').disabled==false ? ' ('+$('priority').options[$('priority').selectedIndex].text+')' : ''),	//modified by cha, 11-18-2010
		parameters: {
			dept: $('department').value,
			module:'orasu',
			priority: $('priority').disabled==false ? $('priority').value : null
		},
		callbacks: {
			day: {
				click: function(e) {
					if (window.parent.$('or_operation_date')) {
						window.parent.$('or_operation_date').value = this.date;
						window.parent.checkDate();
						window.parent.cClick();
					}
				}
			},
			entry: {
				mouseover: function(e) {
					tooltip(Doodad(this))
				},
				mouseout: function(e) {
					nd();
				}
			}
		}
	});

	//added by cha, 11-18-2010
	var type = "<?=$_GET['target']?>";
	if(type=="or_main_view_calendar") {
		$('view_priority').style.display = "";
		$('priority').disabled = false;
	} else {
		$('view_priority').style.display = "none";
		$('priority').disabled = true;
	}
});
document.observe( 'unload', Event.unloadCache );
</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);


ob_start();
$sTemp='';

?>
<div>
<table width="100%" align="center">
	<tr>
		<td align="right" width="50%"><span style="font:bold 12px Verdana">Select department</span></td>
		<td>
				<select class="segInput" id="department" onchange="changeDepartment()">
				<?php

				$query = "SELECT nr `id`,name_formal `name` FROM care_department\n".
					"WHERE does_surgery=1\n".
					"ORDER BY name";

				//$departments = array( 'MAIN' => array(), 'ASU' => array() );
				$ormain_departments = array();
				$orasu_departments = array();
				$rs = $db->Execute($query);
				while ($row = $rs->FetchRow())
				{
					if (strtoupper(substr($row['name'],0,3)) == 'SUR' && $_GET['target']=='or_main_view_calendar') {
						$ormain_departments [$row['id']] = $row['name'];
					} else if(strtoupper(substr($row['name'],0,3)) != 'SUR' && $_GET['target']=='or_asu_view_calendar') {
						$orasu_departments [$row['id']] = $row['name'];
					}
				}

				 if($_GET['target']=='or_main_view_calendar') {
					 foreach($ormain_departments as $i =>$v)
					 {
						 ?>
							<option module="ormain" value="<?php echo $i?>"><?php echo $v?></option>
						 <?
					 }
				 } else {
					 foreach($orasu_departments as $i => $v)
					 {
						 ?>
							<option module="orasu" value="<?php echo $i?>"><?php echo $v?></option>
						 <?
					 }
				 }
				?>
					</select>
		</td>
	</tr>
	<tr style="display:none" id="view_priority">
		<td align="right" width="50%"><span style="font:bold 12px Verdana">Select priority</span></td>
		<td>
				<select class="segInput" id="priority" onchange="changeDepartment()" disabled="disabled">
					<option value="Elective">Elective</option>
					<option value="Emergency">Emergency</option>
				</select>
		</td>
	</tr>
</table>
</div>

<div style="width:850px">
<table width="100%" align="center">
	<tr>
		<td align="left" width="*">
			<div id="scheduleViewer" style="width:700px; padding:10px; -moz-box-sizing: content-box;" align="left"></div>
		</td>
		<td align="right" valign="bottom" width="20%">
				<table width="150px" cellpadding="0" cellspacing="0" style="font:11px Arial bold; margin-left:10px" class="segPanel" align="right">
					<tr>
						<td align="left"><input type="text" style="width:50px; height:10px; background-color:mediumblue;"/>Request.</td>
					</tr>
					<tr>
						<td align="left"><input type="text" style="width:50px; height:10px; background-color:lightcoral;"/>Cancelled.</td>
					</tr>
					<tr>
						<td align="left"><input type="text" style="width:50px; height:10px; background-color:orangered;"/>Post-op.</td>
					</tr>
					<tr>
						<td align="left"><input type="text" style="width:50px; height:10px; background-color:blueviolet;"/>Pre-op.</td>
					</tr>
					<tr>
						<td align="left"><input type="text" style="width:50px; height:10px; background-color:green;"/>Approved</td>
					</tr>
					<tr>
						<td align="left"><input type="text" style="width:50px; height:10px; background-color:crimson;"/>Disapproved.</td>
					</tr>
					<tr>
						<td align="left"><input type="text" style="width:50px; height:10px; background-color:pink;"/>Reschedule.</td>
					</tr>
				</table>
		</td>
	</tr>
</table>
</div>

<input type="hidden" name="submit" value="1" />
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$_COOKIES[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sSubmitImg ='update.gif';
$sBreakImg ='close2.gif';
$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->display('common/mainframe.tpl');

?>