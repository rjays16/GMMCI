<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/class_dialysis_billing.php');
$diaObj = new Dialysis_billing();

define('LANG_FILE','products.php');
$local_user='aufnahme_user';

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
require_once($root_path.'modules/billing_new/ajax/billing-dialysis.common.php');

$smarty = new smarty_care('common');

$breakfile = 'javascript:window.parent.cClick();';
$smarty->assign('breakfile', $breakfile);
ob_start();

?>

<?
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
?>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> 
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script>
	function addData(bill_nr){
		xajax_getEncData(bill_nr);
	}

	function alertNodata(){
		alert("No data found.");
	}

	function appendSessions(details){
		window.parent.appendSessions(details);
	}
</script>
<?
//check pid
$pid=$_GET['pid'];
$refno=$_GET['refno'];
$tr = '<tr><td colspan="4" style="font-weight:normal">No remaining billed accounts found...</td></tr>';

if($pid){
	$diaObj->details = array("pid"=>$pid,
								"id"=>$refno);
	
	$res_dia = $diaObj->getBilled();

	if($res_dia && $res_dia->RecordCount()){
		$tr='';
		while($row=$res_dia->FetchRow()){
			$tr .="<tr onclick='addData(\"".$row['bill_nr']."\");' align='center'>";
			$tr .="<td>".$row['encounter_date']."</td>";
			$tr .="<td>".$row['encounter_nr']."</td>";
			$tr .="<td>".$row['type']."</td>";
			$tr .="<td>".$row['insurance']."</td>";
			$tr .="</tr>";
		}
	}
}
?>


<table width="98%" cellspacing="2" cellpadding="2" style="margin:1%">
    <tbody>
        <tr>
            <td>
                <div style="display:block; border:1px solid #8cadc0; overflow-y:auto; height:360px; width:100%; background-color:#e5e5e5">
                    <table id="person-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
                        <thead>
                            <tr>
                                <th width="20%">Case Date</th>
                                <th width="15%">Case No.</th>
                                <th width="10%">Case Type</th>
                                <th width="*">Insurance</th>
                            </tr>
                        </thead>
                        <tbody id="person-list-body">
                           <?=$tr;?>
                        </tbody>
                    </table>
                    <img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
                </div>
            </td>
        </tr>
    </tbody>
</table>

<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->display('common/mainframe.tpl');
?>