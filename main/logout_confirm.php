<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
?>

<script type="text/javascript">
function ReloadMenu()
{
var x=document.getElementById("okbut");
window.parent.location.href=window.parent.location.href;
x.submit();
}
</script>

<?php html_rtl($lang); ?>
<HEAD>
<?php echo setCharSet(); ?>
<TITLE></TITLE>
<?php require($root_path.'include/inc_css_a_hilitebu.php'); ?>
</head>
<BODY>
	<center>
		<FONT  SIZE=+4 ><b><?php echo $LDLogoutConfirm ?></b></FONT>
		<p>
		<br>
		<FONT  SIZE=5 color=navy>
		<?php echo $nm.'<br>'; ?>
		</FONT>
		<form name="okbut" action="logout.php">
			<input type="hidden"  name="sid" value="<?php echo $sid ?>" >
			<input type="hidden"  name="lang" value="<?php echo $lang ?>" >
			<input type="hidden"  name="logout" value="1" >
			<!--<input type="submit" value=" <?php echo $LDYes ?> " class="button_yes">-->
			<button class="segButton"><img src="<?=$root_path?>gui/img/common/default/tick.png" /><?=$LDYes?></button>
			<input type="button" value="<?php echo $LDNotReally ?>" onClick="javascript:window.history.back()" style="cursor: pointer;" class="button_cancel">
			<!--<button class="segButton" onClick="javascript:window.history.back()"><img src="<?=$root_path?>gui/img/common/default/cancel.png" /><?= $LDNotReally ?></button>-->
		</form>
	</center>
</BODY>
</HTML>
