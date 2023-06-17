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

$allowedarea=&$allow_area['register'];

// --- Added by LST --- 2-25-2009 ----
unset($_SESSION["filteroption"]);
unset($_SESSION["filtertype"]);
unset($_SESSION["filter"]);
unset($_SESSION["current_page"]);
//------------------------------------

$src = $_GET['from'];
$append=URL_REDIRECT_APPEND.'&userck=';
switch($target)
{
    case "requestnew":     
        $title="Inventory::New Request";
        $userck="ck_inventory_user";
        $allowedarea=array("_a_1_inrequestall", "_a_2_inrequestadd"); 
        $area = $_GET['area'];
        if (!$area)
            $area='all';
//        $area=strtolower($area);        
//        $level2_permission=array("_a_1_pharmaallareas");
//        if ($area!='all') $level2_permission[] = "_a_2_pharmaarea".$area;
        #$fileforward=$root_path."modules/supply_office/seg-supply-office-dep.php".$append.$userck."&target=new&area=$area&from=".$src;
        
        //edited by cha 10-07-09
        if($_GET['src']=="or") 
        		$fileforward=$root_path."modules/supply_office/seg-supply-office-req.php".$append.$userck."&target=New&from=OR";
        else
        		$fileforward=$root_path."modules/supply_office/seg-supply-office-req.php".$append.$userck."&target=New&from=".$src;
    break;
    
    case "managereq":
        $title="Inventory::Manage Request";
        $userck="ck_inventory_user";
        $allowedarea=array("_a_1_inrequestall", "_a_2_inrequestview");
        $fileforward=$root_path."modules/inventory/seg-trans-list.php".$append.$userck."&list=requests";  
    break;
    
    case "pending_requests":
        $title="Inventory::Pending Requests";
        $userck="ck_inventory_user";
        $allowedarea=array("_a_1_inrequestall", "_a_2_inrequestview");
        $fileforward=$root_path."modules/inventory/seg-trans-list.php".$append.$userck."&list=pending_requests";  
    break;
                                  
    case "issuancenew":
        $title="Inventory::Issue Requested Items"; 
        $userck="ck_inventory_user";
        $allowedarea=array("_a_1_issuanceall", "_a_2_issuanceadd"); 
        $fileforward=$root_path."modules/supply_office/seg-issuance-test.php".$append.$userck."&target=issuancenew&from=".$src;
    break;
    
    case "issuanceack":
        $title="Inventory::Acknowledge Issuances"; 
        $userck="ck_inventory_user";
        $allowedarea=array("_a_1_issuanceall", "_a_2_issuanceacknowledge");
        $fileforward=$root_path."modules/supply_office/seg-issuance-acknowledge.php".$append.$userck."&target=issuanceack&from=".$src;
    break;
    
    case "newdelivery":
        $title="Inventory::Delivery"; 
        $userck="ck_inventory_user";
        $allowedarea=array("_a_1_deliveryall", "_a_2_deliveryadd");
        $fileforward=$root_path."modules/inventory/seg-delivery.php".$append.$userck."&target=New&from=".$src."&src=Inventory";        
        break;
        
    case "deliveries":
        $title="Inventory::Posted Deliveries"; 
        $userck="ck_inventory_user"; 
        $allowedarea=array("_a_1_deliveryall", "_a_2_deliveryview");
        $fileforward=$root_path."modules/inventory/seg-trans-list.php".$append.$userck."&list=deliveries";        
        break;
        
    case "newextreq":
        $title="Inventory::External Request"; 
        $userck="ck_inventory_user";
        $allowedarea=array("_a_1_deliveryall", "_a_2_deliveryadd");
        $fileforward=$root_path."modules/supply_office/seg-inventory-ext-req.php".$append.$userck."&target=New&from=".$src;        
        break;
        
    case "extreqs":
        $title="Inventory::Posted External Requests"; 
        $userck="ck_inventory_user"; 
        $allowedarea=array("_a_1_deliveryall", "_a_2_deliveryview");
        $fileforward=$root_path."modules/inventory/seg-trans-list.php".$append.$userck."&list=external_requests";        
        break;
        
    case "reports":
        $title="Inventory::Reports"; 
        $userck="ck_inventory_user";
        $allowedarea=array("_a_1_inreports");
        $fileforward=$root_path."modules/supply_office/seg-inventory-report.php".$append.$userck."&target=reports&from=".$src;
        break;
          
    case "adjustment":
        $title="Inventory::Adjustment"; 
        $userck="ck_inventory_user";
        $allowedarea=array("_a_1_inadjustall", "_a_2_inadjustadd");
        $fileforward=$root_path."modules/supply_office/seg-inventory-adjustment.php".$append.$userck."&target=adjustment&from=".$src;
        break;


    case "bulk_adjust":
        $title = "Inventory::Bulk Adjustments";
        $userck = "ck_inventory_user";
        $allowedarea = array("_a_1_inadjustall", "_a_2_inadjustadd");
        $fileforward = $root_path . 'index.php?r=inventory/bulkAdjustment';
        break;
        
    case "hisadjustments":
        $title="Inventory::Adjustment History"; 
        $userck="ck_inventory_user";
        $allowedarea=array("_a_1_inadjustall", "_a_2_inadjustadd");
        $fileforward=$root_path."modules/inventory/seg-trans-list.php".$append.$userck."&list=adjustments";
        break;
    
    case "prodbank":
        $title="Inventory::Supplies databank";
        $userck="ck_inventory_user";
        $allowedarea=array("_a_1_indatabank");
        $fileforward=$root_path."modules/supply_office/seg-inventory-databank.php".$append.$userck."&target=prodbank&from=".$src;
        break;
        
    case "stockcard":
        $title="Inventory::Stock card"; 
        $userck="ck_inventory_user";
        $allowedarea=array("_a_1_inreports");
        $fileforward=$root_path."modules/supply_office/seg-inventory-stockcard.php".$append.$userck."&target=stockcard&from=".$src;
        break;
        
    case "manageiss":
        $title="Inventory::Issuance History"; 
        $userck="ck_inventory_user";
        $allowedarea=array("_a_1_inreports");
        $fileforward=$root_path."modules/inventory/seg-trans-list.php".$append.$userck."&list=issuances";
        break;
        
    default:     {header("Location:".$root_path."language/".$lang."/lang_".$lang."_invalid-access-warning.php"); exit;}; 
}
$thisfile=basename(__FILE__)."?".$_SERVER['QUERY_STRING'];
$breakfile='seg-supply-functions.php?ntid=false&lang=en'.URL_APPEND;
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
