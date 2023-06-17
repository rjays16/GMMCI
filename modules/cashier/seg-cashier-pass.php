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
	case "functions":
		$title="Cashier";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermanageentry","_a_2_cashiercreate");
		$fileforward=$root_path."modules/cashier/seg-cashier-functions.php".$append.$userck."&from=".$src;
	break;
	case "requestlist":
		$title="Cashier::Process Requests";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermanageentry","_a_2_cashiercreate");
		$fileforward=$root_path."modules/cashier/seg-cashier-requests.php".$append.$userck."&from=".$src;
	break;

	case "recent":
		$title="Cashier::View Recent";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermanageentry","_a_2_cashiercreate");
		$fileforward=$root_path."modules/cashier/seg-cashier-recent.php".$append.$userck."&from=".$src;
	break;

	case "billinglist":
		$title="Cashier::Process billing";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermanageentry","_a_2_cashiercreate");
		$fileforward=$root_path."modules/cashier/seg-cashier-main.php".$append.$userck."&tab=billing&from=".$src;
		#$fileforward=$root_path."modules/cashier/seg-cashier-billing-list.php".$append.$userck."&from=".$src;
	break;

	case "services":
		$title="Cashier::Other services";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermanageentry","_a_2_cashiercreate");
		#$fileforward=$root_path."modules/cashier/seg-cashier-others.php".$append.$userck."&from=".$src;
		$fileforward=$root_path."modules/cashier/seg-cashier-main.php".$append.$userck."&tab=other&from=".$src;
	break;

	case "vatable":
		$title="Cashier::Vatable services";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermanageentry","_a_2_cashiercreate");
		#$fileforward=$root_path."modules/cashier/seg-cashier-others.php".$append.$userck."&from=".$src;
		$fileforward=$root_path."modules/cashier/seg-cashier-main.php".$append.$userck."&tab=other&vat=yes&from=".$src;
	break;
	case "deposit":
		$title="Cashier::Deposit";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermanageentry","_a_2_cashiercreate");
		$fileforward=$root_path."modules/cashier/seg-cashier-main.php".$append.$userck."&tab=deposit&from=".$src;
		#$fileforward=$root_path."modules/cashier/seg-cashier-billing-list.php".$append.$userck."&from=".$src;
	break;

	case "voucherlist":
		$title="Cashier :: Cash voucher :: List of requests";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermanageentry");
		$fileforward=$root_path."modules/cashier/seg-cashier-voucher.php".$append.$userck."&target=list&from=".$src;
	break;

	case "voucheredit":
		$title="Cashier :: Cash voucher :: Add cash vouchers";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermanageentry");
		$fileforward=$root_path."modules/cashier/seg-cashier-voucher.php".$append.$userck."&target=edit&dept=".$_GET['dept']."&ref=".$_GET['ref']."&from=".$src;
	break;

	case "memonew":
		$title="Cashier :: Credit memo :: New credit memo";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermemo");
		$fileforward=$root_path."modules/cashier/seg-cashier-cm.php".$append.$userck."&target=edit&from=".$src;
	break;

	case "memoedit":
		$title="Cashier :: Credit memo :: Edit credit memo";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermemo");
		$fileforward=$root_path."modules/cashier/seg-cashier-cm.php".$append.$userck."&target=edit&nr=".$_GET['nr']."&from=".$src;
	break;

	case "memoarchives":
		$title="Cashier :: Credit memo :: Archives";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermemo");
		$fileforward=$root_path."modules/cashier/seg-cashier-cm.php".$append.$userck."&target=list&from=".$src;
	break;

	case "databank":
	case "miscellaneous":
		if ($target == "databank") {
			$allowedarea=array("_a_1_cashierdatabank");
			#$title="Cashier::Other Services Databank";
		}
		else {
			$allowedarea=array("_a_1_billmiscellaneousmanage");
			# $title="Billing :: Miscellaneous Charge Items";
		}
		$userck="ck_cashier_user";
		$fileforward=$root_path."modules/cashier/seg-cashier-services-main.php".$append.$userck."&from=".$src."&target=".$target;
	break;

	case "archives":
		$title="Cashier::Archives";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashierarchives");
		$fileforward=$root_path."modules/cashier/seg-cashier-archives.php".$append.$userck."&from=".$src;
	break;

	case "reports":
		$title="Cashier::Reports";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashierreports");
		$fileforward=$root_path."modules/cashier/seg-cashier-reports.php".$append.$userck."&from=".$src;
	break;

	case "report_launcher":
		$title="Cashier::Reports";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashierreports");
		$fileforward=$root_path."modules/reports/report_launcher.php".$append.$userck."&from=".$src."&dept_nr=170";
	break;

	case "assignorno":
		$title="Cashier::OR Assignment";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashierassignorno");
		$fileforward=$root_path."modules/cashier/seg-or-assign.php".$append.$userck."&from=".$src;
	break;

	case "editorno":
		$title="Cashier::OR Editing";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiereditorno");
		$fileforward=$root_path."modules/cashier/seg-cashier-edit-or-no.php".$append.$userck."&from=".$src;
	break;

	case "setupprinter":
		$title="Cashier::Setup Printer";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiersetupprinter");
		$fileforward=$root_path."modules/cashier/seg-cashier-setup-printer.php".$append.$userck."&from=".$src;
	break;

	#added by cha 11-06-2009
	case "walkinrequestlist":
		$title="Cashier::Process Walkin Requests";
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermanageentry","_a_2_cashiercreate");
		$fileforward=$root_path."modules/cashier/seg-cashier-requests.php".$append.$userck."&from=".$src."&target=walkin";
	break;
	#end cha

	case "pf":
		$title="Cashier::Tag PF Payments";;
		$userck="ck_cashier_user";
		$allowedarea=array("_a_1_cashiermanageentry","_a_2_cashiercreate");
		$fileforward=$root_path."modules/cashier/cashier-pf-tagging.php".$append.$userck."&from=".$src."&user_origin=cashier";
	break;

	default: 	{header("Location:".$root_path."language/".$lang."/lang_".$lang."_invalid-access-warning.php"); exit;};
}
$thisfile=basename(__FILE__)."?".$_SERVER['QUERY_STRING'];
$breakfile='seg-cashier-functions.php'.URL_APPEND;
$lognote="Cashier $title ok";

// reset all 2nd level lock cookies
require($root_path.'include/inc_2level_reset.php');
setcookie('ck_2level_sid'.$sid,'',0,'/');

require($root_path.'include/inc_passcheck_internchk.php');
if ($pass=='check') include($root_path.'include/inc_passcheck.php');

$errbuf="Cashier";
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
<!-- <img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$title " ?></a><br>
<img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$title " ?>?</a><br>
 -->
<p>

</TABLE>

<?php

require($root_path.'include/inc_load_copyrite.php');
?>

</BODY>
</HTML>