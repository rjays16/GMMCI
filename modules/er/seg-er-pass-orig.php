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
define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'global_conf/areas_allow.php');
$src = $_GET['from'];
$append=URL_REDIRECT_APPEND.'&userck=';
switch($target)
{
	case "ersearchpatient": 	
		$title="ER::Search patient";
		$userck="ck_opd_user";
		$allowedarea=array("_a_1_pharmaordermanage");
		$fileforward=$root_path."modules/registration_admission/aufnahme_daten_such.php".$append."&target=list&from=".$src;
	break;

	default: 	{header("Location:".$root_path."language/".$lang."/lang_".$lang."_invalid-access-warning.php"); exit;}; 
}
$thisfile=basename(__FILE__)."?".$_SERVER['QUERY_STRING'];
$breakfile='seg-pharma-order-functions.php'.URL_APPEND;
$lognote="$title ok";

// reset all 2nd level lock cookies
require($root_path.'include/inc_2level_reset.php'); 
setcookie('ck_2level_sid'.$sid,'',0,'/');

require($root_path.'include/inc_passcheck_internchk.php');
if ($pass=='check') include($root_path.'include/inc_passcheck.php');

$errbuf="$title";
$minimal=1;
require($root_path.'include/inc_passcheck_head.php');

?>

<BODY  <?php if (!$nofocus) echo 'onLoad="document.passwindow.userid.focus()"'; echo  ' bgcolor='.$cfg['body_bgcolor']; 
 if (!$cfg['dhtml']){ echo ' link='.$cfg['body_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['body_txtcolor']; } 
?>>

<p>
<P>
<img src="../../gui/img/common/default/lampboard.gif" border=0 align="middle">
<FONT  COLOR="<?php echo $cfg[top_txtcolor] ?>"  SIZE=5  FACE="verdana"> <b><?php echo "$title" ?></b></font>
<p>
<table width=100% border=0 cellpadding="0" cellspacing="0"> 

<?php require($root_path.'include/inc_passcheck_mask.php') ?>  

<p>
<!-- <img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$LDIntro2 $LDPharmacy $title " ?></a><br>
<img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$LDWhat2Do $LDPharmacy $title " ?>?</a><br>
 -->
<p>
</TABLE>

<?php
require($root_path.'include/inc_load_copyrite.php');
?>

</BODY>
</HTML>
