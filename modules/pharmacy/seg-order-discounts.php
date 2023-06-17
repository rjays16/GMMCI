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
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);

switch($cat)
{
	case "pharma":
							$title=$LDPharmacy;
							//$breakfile=$root_path."modules/pharmacy/apotheke-datenbank-functions.php".URL_APPEND."&userck=$userck";
							$breakfile=$root_path."modules/pharmacy/seg-close-window.php".URL_APPEND."&userck=$userck";
							$imgpath=$root_path."pharma/img/";
							break;
	case "medlager":
							$title=$LDMedDepot;
							//$breakfile=$root_path."modules/med_depot/medlager-datenbank-functions.php".URL_APPEND."&userck=$userck";
							$breakfile=$root_path."modules/pharmacy/seg-close-window.php".URL_APPEND."&userck=$userck";
							$imgpath=$root_path."med_depot/img/";
							break;
	default:  
							$cat = "pharma";
							$title=$LDMedDepot;
							$breakfile=$root_path."modules/pharmacy/seg-close-window.php".URL_APPEND."&userck=$userck";
							$imgpath=$root_path."pharma/img/";
							break;
}

require($root_path."include/inc_products_search_mod.php");

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');


 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title $LDPharmaDb $LDSearch");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title $LDPharmaDb $LDSearch");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad="loadDiscounts()"');

 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
	function loadDiscounts() {
		var nodes = window.parent.document.getElementsByName('discount[]');
		if (nodes) {
			for (var i=0;i<nodes.length;i++) {
				if (nodes[i].value) {
					var m = document.getElementById('chk'+nodes[i].value);
					if (m) {
						m.checked = true;
					}
				}
			}
		}
	}

	function changeDiscount(id,checked) {
		var em = window.parent.document.getElementById('discount_'+id);
		if (em) {
			em.value = checked ? id : '';
			if (window.parent.refreshDiscount)
				window.parent.refreshDiscount();
			return true;
		}
		return false;
	}
// -->
</script> 

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

include_once($root_path."include/care_api_classes/class_discount.php");
$discountClass = new SegDiscount();
$src = "";
if ($result = $discountClass->GetDiscounts()) {
	$count=0;
	while ($row = $result->FetchRow()) {
		if ($row["is_charity"]) continue;
		$count++;
		$alt = ($count%2)+1;
		$disabled = $row["is_charity"] ? "disabled" : "";
		$color = $row["is_charity"] ? "color:#999999;" : "";
		$src .= "
			<tr class=\"wardlistrow$alt\" style=\"font-size:12px\">
				<td width=\"1\"><input id=\"chk".$row['discountid']."\" name=\"\" type=\"checkbox\" onclick=\"changeDiscount('".$row['discountid']."', this.checked)\" $disabled></td>
				<td style=\"font-weight:bold; $color\">".$row["discountdesc"]."</td>
				<td style=\"$color\" align=\"right\">".($row["discount"]*100)."%</td>
			</tr>
";
	}
}
else {
}


ob_start();
?>

<table id="discount-list" class="segList" width="100%" cellpadding="0" cellspacing="0" style="margin:0.5%">
	<thead>
		<tr>
			<th width="1"></th>
			<th>Description</th>
			<th width="10%">Discount</th>
		</tr>
	</thead>
	<tbody>
<?=	$src ?>
	</tbody>
</table>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>