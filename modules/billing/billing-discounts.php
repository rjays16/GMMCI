<?php
/**
* SegHIS (Billing Module)
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'modules/billing/ajax/billing-discounts.common.php');

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

 # Collect javascript code
 ob_start()
?>
<!-- prototype -->
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>/js/shortcut.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins: -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<!-- YUI Library -->
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/connection/connection.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dragdrop/dragdrop.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/container/container_core.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/container/assets/container.css">

<script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script>
<script type="text/javascript" src="js/billing-discounts.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax');
?>
<script>
	YAHOO.namespace("discount.container");
	YAHOO.util.Event.onDOMReady(initDiscountDialogBox);
	YAHOO.util.Event.addListener(window, "load", init);
</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$smarty->assign('sProgressBar','<img id="ajax-loading" src="'.$root_path.'/images/loading6.gif" align="absmiddle" border="0"/>');
$smarty->assign('sAddButton','<img id="btnAdd" style="cursor:pointer" src="'.$root_path.'/images/btn_add.gif" border=0 >');

$smarty->assign('sOnLoadJs','onLoad="showProgressBar();js_getApplicableDiscounts()"');

ob_start();
?>
<input type="hidden" id="enc_nr" name="enc_nr" value="" />
<input type="hidden" id="entry_no" name="entry_no" value="" />
<input type="hidden" id="discount_id" name="discount_id" value="" />
<input type="hidden" id="discount_desc" name="discount_desc" value="" />
<input type="hidden" id="bill_dte" name="bill_dte" value="" />
<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenInputs', $sTemp);

ob_start();
?>
<input type="hidden" id="root_path" name="root_path" value="<?php echo $root_path ?>" />
<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sMainHiddenInputs', $sTemp);

$smarty->assign('sMainBlockIncludeFile','billing/billing_discounts.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
