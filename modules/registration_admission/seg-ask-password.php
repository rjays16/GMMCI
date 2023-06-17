<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/registration_admission/doctor-dept.common.php');

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_lab_user';
require_once($root_path.'include/inc_front_chain_lang.php');

ob_start();
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript">
		function validateform(){
			var type = '<?=$_GET['type'];?>';
			var username = $('username').value;
			var password = $('password').value;
			var encounter_nr ='<?=$_GET['encounter_nr']?>';
			var encounter_type ='<?=$_GET['encounter_type']?>';

			if ((username!='')&&(password!='')){
				xajax_checkAccess(username, password);
			}else{
				if ((username=='')&&(password=='')){
					alert('Please enter your username and your password.');
					$('username').focus();
				}else if (username==''){
					alert('Please enter your username.');
					$('username').focus();
				}else if (password==''){
					alert('Please enter your password.');
					$('password').focus();
				}
			}

		}

		function submitform(){
			//window.parent.submitform();
			window.parent.cClick();
		}

		function print_true(){
			var type = '<?=$_GET['type'];?>';
			var username = $('username').value;
			var password = $('password').value;
			var encounter_nr ='<?=$_GET['encounter_nr']?>';
			var encounter_type ='<?=$_GET['encounter_type']?>';

			if (type==1){
				 window.parent.location.href="aufnahme_cancel.php<?php echo URL_REDIRECT_APPEND ?>&mode=cancel&encounter_nr="+encounter_nr+"&cby="+username+"&pw="+password;
			}else if (type==2){
				 window.parent.location.href="aufnahme_admit.php<?php echo URL_REDIRECT_APPEND ?>&mode=admit&encounter_nr="+encounter_nr+"&encounter_type="+encounter_type+"&cby="+username+"&pw="+password;
			}
			submitform();
		}

		function print_false(){
			 alert('Your login or password is wrong');
		}

</script>
<body bgcolor="#FFFFFF" onLoad="javascript:document.getElementById('username').focus();">
<form ENCTYPE="multipart/form-data" action="<?=$thisfile?>" method="POST" name="inputgroupform" id="inputgroupform">
	<div style="background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden;">
	<table border="0" width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%; font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden">
		<tbody>
			<tr>
				<td>Username: </td>
				<td>
					<input type="text" name="username" id="username" size="30" value="">
				</td>
			</tr>
			<tr>
				<td>password: </td>
				<td>
					<input type="password" name="password" id="password" size="30" value="">
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<img id="save" name="save" src="../../gui/img/control/default/en/en_ok.gif" border=0 alt="Ok" title="Ok" style="cursor:pointer" onClick="validateform();">
					<img id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" onClick="javascript:window.parent.cClick();" title="Cancel" style="cursor:pointer">
				</td>
			</tr>
		</tbody>
	</table>
	</div>
</form>
</body>