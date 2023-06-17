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
	case "newgrant": 	
		$title="Sponsor Grants::New grant";
		$userck="ck_grant_user";
		$allowedarea=array("_a_1_pharmaordermanage","_a_2_pharmaordercreate");
		$fileforward=$root_path."modules/sponsor/seg-sponsor-grant.php".$append.$userck."&target=edit&from=".$src;
	break;
						
	case "orderlist": 	
		$title="Pharmacy::Order list";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmaordermanage");
		$fileforward=$root_path."modules/pharmacy/seg-pharma-order.php".$append.$userck."&target=list&from=".$src;
	break;
  
  case "setarea":
  break;
	
	case "order_er":
		include_once($root_path."include/care_api_classes/class_order.php");
		$oc = new SegOrder("pharma");
		$nr = $oc->getERRequest($_GET['encounter_nr']);
		$title="Pharmacy::ER Medicines and Supplies";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmaordermanage");
		if ($nr)
			$fileforward=$root_path."modules/pharmacy/seg-pharma-order.php".$append.$userck."&target=edit&ref=$nr&encounterset=".$_GET['encounter_nr']."&billing=1&area=ER&from=CLOSE_WINDOW";
		else
			$fileforward=$root_path."modules/pharmacy/seg-pharma-order.php".$append.$userck."&target=new&encounterset=".$_GET['encounter_nr']."&billing=1&area=ER&from=CLOSE_WINDOW";
	break;

	case "orderbilling":
		include_once($root_path."include/care_api_classes/class_order.php");
		$oc = new SegOrder("pharma");
		$nr = $oc->getRecentWardRefInDateRange($_GET['from_dt'], $_GET['to_dt'], $_GET['encounter_nr']);
		$title="Pharmacy::Billing Medicines and Supplies";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_erbilling","_a_1_billmanage","_a_2_billviewsave");
		$date = (int)$_GET['to_dt']-1;
		if ($nr)
			$fileforward=$root_path."modules/pharmacy/seg-pharma-order.php".$append.$userck."&target=edit&ref=$nr&dateset=$date&encounterset=".$_GET['encounter_nr']."&billing=1&area=WD&from=CLOSE_WINDOW";
		else
			$fileforward=$root_path."modules/pharmacy/seg-pharma-order.php".$append.$userck."&target=new&dateset=$date&encounterset=".$_GET['encounter_nr']."&billing=1&area=WD&from=CLOSE_WINDOW&todt=".$_GET['to_dt'];
	break;

	case "orderedit":
		$title="Pharmacy::Update order";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmaordermanage");
		$fileforward=$root_path."modules/pharmacy/seg-pharma-order.php".$append.$userck."&target=edit&ref=$ref&from=".$src;
	break;
	
	case "orderview":
		$title="Pharmacy::View order";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmaordermanage","_a_2_pharmaorderview");
		$fileforward=$root_path."modules/pharmacy/seg-pharma-order.php".$append.$userck."&target=edit&ref=$ref&viewonly=1&from=".$src;
	break;
	
	case "newstock":
		$title="Pharmacy::Ward Stocks::New ward stock";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmawardstocksmanage","_a_2_pharmawardstockscreate");
		$level2_permission=array("_a_1_pharmaallareas");
		if ($area!='all') $level2_permission[] = "_a_2_pharmaarea".$area;
		$fileforward=$root_path."modules/pharmacy/seg-pharma-wardstock.php".$append.$userck."&target=edit&area=$area&from=".$src;
	break;
	
	case "recentstock":
		$title="Pharmacy::Ward Stocks::Recent ward stocks";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmawardstocksmanage","_a_2_pharmawardstockscreate");
		$level2_permission=array("_a_1_pharmaallareas");
		if ($area!='all') $level2_permission[] = "_a_2_pharmaarea".$area;
		$fileforward=$root_path."modules/pharmacy/seg-pharma-wardstock.php".$append.$userck."&target=recent&area=$area&from=".$src;
	break;
	
	case "editstock":
		$title="Pharmacy::Ward Stocks::Edit ward stock";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmawardstocksmanage","_a_2_pharmawardstockscreate");
		$level2_permission=array("_a_1_pharmaallareas");
		if ($area!='all') $level2_permission[] = "_a_2_pharmaarea".$area;
		$fileforward=$root_path."modules/pharmacy/seg-pharma-wardstock.php".$append.$userck."&target=edit&area=$area&from=".$src."&nr=$nr";
	break;
	
	case "managestock":
		$title="Pharmacy::Ward Stocks::Ward stocks";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmawardstocksmanage");
		$fileforward=$root_path."modules/pharmacy/seg-pharma-wardstock.php".$append.$userck."&target=list&area=$area&from=".$src;
	break;
	
	case "manageward":
		$title="Pharmacy::Wards list";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmawardstocksmanage");
		$fileforward=$root_path."modules/pharmacy/seg-pharma-wards.php".$append.$userck."&from=".$src;
	break;
	
	case "servelist":
		$title="Pharmacy::Serve order";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmaordermanage","_a_2_pharmaorderserve");
		$level2_permission=array("_a_1_pharmaallareas");
		if ($area!='all') $level2_permission[] = "_a_2_pharmaarea".$area;
		$fileforward=$root_path."modules/pharmacy/seg-pharma-order.php".$append.$userck."&target=servelist&area=$area&ref=$ref&from=".$src;
	break;
	
	case "serveorder":
		$title="Pharmacy::Serve order";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmaordermanage","_a_1_pharmaorderserve");
		$fileforward=$root_path."modules/pharmacy/seg-pharma-order.php".$append.$userck."&target=serve&ref=$ref&from=".$src;
	break;
	
	case "returnnew":
		$title="Pharmacy::Return Meds::Create return entry";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmarefundmanage","_a_2_pharmarefundcreate");
		$fileforward=$root_path."modules/pharmacy/seg-pharma-return.php".$append.$userck."&target=edit&refund=no&from=".$src;
	break;
	
	case "refundnew":
		$title="Pharmacy::Return Meds::Create refund entry";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmarefundmanage","_a_2_pharmarefundcreate");
		$fileforward=$root_path."modules/pharmacy/seg-pharma-return.php".$append.$userck."&target=edit&from=".$src;
	break;
	
	case "returnedit":
		$title="Pharmacy::Return Meds::Create return entry";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmarefundmanage");
		$fileforward=$root_path."modules/pharmacy/seg-pharma-return.php".$append.$userck."&target=edit&&from=".$src."&nr=$nr";
	break;
	
	case "returnlist":
		$title="Pharmacy::Return Meds::List of pharmacy returns";
		$userck="ck_prod_order_user";
		$allowedarea=array("_a_1_pharmarefundmanage");
		$fileforward=$root_path."modules/pharmacy/seg-pharma-return.php".$append.$userck."&target=list&&from=".$src;
	break;
	
	case "databank":
		$title="Pharmacy::Product databank";
		$userck="ck_prod_db_user";
		$allowedarea=array("_a_1_pharmadatabank");
		$fileforward="seg-pharma-products-main.php".$append.$userck."&from=".$src;
	break;
	
	case "reports":
		$title="Pharmacy::Reports";
		$userck="ck_prod_db_user";
		$allowedarea=array("_a_1_pharmareports");
		$fileforward="seg-pharma-reports.php".$append.$userck."&from=".$src;
	break;

	case "archive":$title=$LDOrderArchive;
						$userck="ck_prod_arch_user";
						$fileforward=$root_path."modules/products/products-archive.php".$append.$userck."&from=".$src;
						break;
	case "dbank":  $title=$LDPharmaDb;
						$userck="ck_prod_db_user";
						$fileforward="apotheke-datenbank-functions.php".$append.$userck."&from=".$src;
						break;
	case "catalog":  $title=$LDOrderCat;
						$userck="ck_prod_order_user";
						$fileforward=$root_path."modules/products/products-bestellkatalog-edit.php".$append.$userck."&target=catalog&from=".$src;
						break;
  case "menu":
    if ($_GET["area"]) $_SESSION['sess_pharma_area']=$_GET['area'];
    header("Location:seg-pharma-order-functions.php".$append.$userck);
    exit;
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