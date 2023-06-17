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

$smarty = new smarty_care('common');

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('QuickMenu',FALSE);
$smarty->assign('bHideCopyright',TRUE);


global $db;

if ($_GET['dept'])
{
	$deptId = $_GET['dept'];
	$info = $db->GetRow("SELECT name_formal `name`,\n".
			"IFNULL(decking_limit,6) `limit`\n".
		"FROM care_department d\n".
			"LEFT JOIN seg_or_scheduler_decking_limit l ON l.dept_nr=d.nr\n".
		"WHERE nr=".$db->qstr($deptId)
	);
	if ($info === false)
	{
		echo "SELECT name_formal `name`,\n".
			"IFNULL(main_deciking_limit,6) `limit_ormain`,\n".
			"IFNULL(asu_decking_limit,6) `limit_orasu`\n".
		"FROM care_department d\n".
			"LEFT JOIN seg_or_scheduler_decking_limit l ON l.dept_nr=d.nr\n".
		"WHERE nr=".$db->qstr($deptId);
		die('Invalid department id ...');
	}

	if ($_GET['m'] != 'orasu' && $_GET['m'] != 'ormain')
	{
		die('Invalid module name ['.$_GET['m'].']...');
	}

	$deptName = $info['name'];
	$decking = $info['limit'];
}
else
{
	$deptId = null;
	die("No department selected ...");
}


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
var sv;


document.observe("dom:loaded", function() {
	sv = ScheduleViewer.create('scheduleViewer', {
		id: 'sv_effect',
		url: '../ajax/orSchedules.ajax.php',
		width: 'auto',
		height: <?php echo $decking*90 ?>,
		title: '<?php echo addslashes($deptName).($_GET['priority']!="" ? "(".strtoupper($_GET['priority']).")" : '') ?>', //added by cha, 11-18-2010
		maxDecking: <?php echo $decking ?>,
		parameters: {
			dept: '<?php echo $_GET['dept'] ?>',
			module:'<?php echo $_GET['m'] ?>',
			priority:'<?php echo $_GET['priority']?>' //added by cha, 11-18-2010
		},
		callbacks: {
			day: {
				click: function(e) {
					window.parent.$('or_operation_date').value = this.date;
					window.parent.checkDate();
					window.parent.cClick();
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
<div id="scheduleViewer" style="width:720px; padding:10px; -moz-box-sizing: content-box;"></div>
<div id="legend_box" style="width:150px; margin-left:-570px" align="right">
	<table width="100%" cellpadding="0" cellspacing="0" style="font:11px Arial bold; margin-top:5px" class="segPanel" align="left">
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