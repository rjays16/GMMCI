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
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);

$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);

$breakfile=$root_path."modules/laboratory/seg-close-window.php".URL_APPEND."&userck=$userck";
#$imgpath=$root_path."pharma/img/";

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
 $smarty->assign('sToolbarTitle',"$title $LDLabDb $LDSearch");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title $LDLabDb $LDSearch");

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
			//alert(em.value);
			
			if ((em.value=="C1") || (em.value=="C2") || (em.value=="C3")){
				window.parent.document.getElementById('dname').value = em.value;
				window.parent.document.getElementById('dname2').value = em.value;
				//alert("social service");
			}
			
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
if ($result = $discountClass->getAllDataObject()) {
	$count=0;
	while ($row = $result->FetchRow()) {
		$count++;
		$alt = ($count%2)+1;
		$src .= "
			<tr class=\"wardlistrow$alt\" style=\"font-size:12px\">
				<td width=\"1\"><input id=\"chk".$row['discountid']."\" name=\"\" type=\"checkbox\" onclick=\"changeDiscount('".$row['discountid']."', this.checked)\"></td>
				<td style=\"font-weight:bold\">".$row["discountdesc"]."</td>
				<td align=\"right\">".($row["discount"]*100)."%</td>
			</tr>
";
	}
}
else {
}


ob_start();
?>

<table id="discount-list" class="segList" width="100%" cellpadding="1" cellspacing="1" style="margin:0.5%">
	<thead>
		<tr>
			<th width="1"></th>
			<th>Description</th>
			<th width="10%">Discount</th>
		</tr>
	</thead>
	<tbody>
<?=	 $src ?>
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
