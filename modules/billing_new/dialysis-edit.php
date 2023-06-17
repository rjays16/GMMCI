<?
/**
*Created by mai
*Created on 02-16-2015
*/

require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
$local_user='ck_pflege_user';
$breakfile=$root_path.'modules/billing/bill-main-menu.php'.URL_APPEND."&userck=$userck";

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
require_once($root_path.'modules/billing_new/ajax/billing-dialysis.common.php');
require_once($root_path.'include/care_api_classes/class_dialysis_billing.php');

$smarty = new Smarty_Care('common');
$objDia = new Dialysis_billing();
$breakfile = 'javascript:window.parent.cClick();';
$smarty->assign('sToolbarTitle',"Dialysis");
$smarty->assign('breakfile', $breakfile);
ob_start();
?>

<!-- Core module and plugins: -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> 
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript" src="js/billing-dialysis.js"></script>
<?
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
?>

<?
$id = $_GET['nr'];

if($id){
	$objDia->details = array('id'=>$id);
	$patient_info=$objDia->getCycleInfo();

	if(strtolower($patient_info['trans_flag']) == 'active'){
		$smarty->assign('doneBtn', '<button class="jedButton" onclick="clickFn(\'done\');"><img src="'.$root_path.'gui/img/common/default/accept.png"><span>Done</span></button>');
	}else if($patient_info['trans_flag'] == 'done'){
		$smarty->assign('doneBtn', '<button class="jedButton" onclick="clickFn(\'undone\');"><img src="'.$root_path.'gui/img/common/default/arrow_rotate_anticlockwise.png"><span>Undone</span></button>');
	}	
}else{
	$patient_info['id'] = $id;

	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer"
						 onclick="if (warnClear()) { emptyDataDialysisBilling(); clickFn(\'search\')}"/>');
}

$smarty->assign('addBtn', '<button class="jedButton" id="addBtn" style="display:none;" onclick="clickFn(\'add\');"><img src="'.$root_path.'gui/img/common/default/add.png"><span>Add</span></button>');
$smarty->assign('saveBtn', '<button class="jedButton" id="saveBtn" style="display:none;" onclick="clickFn(\'save\')"><img src="'.$root_path.'gui/img/common/default/page_save.png"><span>Save</span></button>');

$smarty->assign('sPid', '<input type="text" id="pid" class="segInput" readOnly value="'.$patient_info['pid'].'">');
$smarty->assign('sRefNo', '<input type="text" id="refno" class="segInput" readOnly value="'.$patient_info['id'].'">');
$smarty->assign('sPatientName', '<input id="p_name" type="text" class="segInput" readOnly value="'.$patient_info['p_name'].'">');
$smarty->assign('sAge', '<input type="text" id="p_age" class="segInput" readOnly value="'.$patient_info['age'].'">');
$smarty->assign('sPatientAddress', '<textarea id="p_add" class="segInput" readOnly>'.$patient_info['address'].'</textarea>');
$smarty->assign('sStatus', '<input type="text" class="segInput" readOnly id="status" value="'.strtoupper($patient_info['trans_flag']).'">');
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&target=new&clear_ck_sid=".$clear_ck_sid.$qs.'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return false;">');
$smarty->assign('sFormEnd','</form>');
?>

<input type="hidden" id="ref_no" name="ref_no" value="<?=$id?>" />
<input type="hidden" id="url" name="url" value="<?=URL_APPEND?>" />

<?
$smarty->assign('sMainBlockIncludeFile','billing_new/dialysis-cycle.tpl');
$smarty->display('common/mainframe.tpl');
?>