<?php
//created by cha August 12, 2010

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/system_admin/fismapping/ajax/fis_mapping.common.php');

define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

$itemcode = $_GET['itemcode'];
$tanscode = $_GET['acctype'];
$area = $_GET['area'];
$entry = "";
if($_GET['set']){
	$entry = $_GET['set'];
}

if($_GET['TranCode']){
	$transactionCode = strtolower($_GET['TranCode']);
}

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');


$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$thisfile='seg_fis_mapping.php';

//initialize smarty
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

require_once($root_path.'include/care_api_classes/class_seg_fis_mapping.php');
$objfis = new FisMapping();

#added by janken 10/13/2014 for requiring curl class
require_once $root_path.'include/care_api_classes/curl/class_curl.php';
$curl_obj = new Rest_Curl;
# Toolbar title
$smarty->assign('sToolbarTitle','Update Account Mapping');

# href for the  button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<script type="text/javascript" src="js/fis_mapping_update.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript" src="js/md5.js"></script>
<?
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
//company discount insurance
$CDI = array('dc', 'ins', 'com');

for ($i=0; $i < count($CDI) ; $i++) { 
	
	if($transactionCode == $CDI[$i]){
		$ItemName = $objfis->GetCDIName($itemcode, $CDI[$i]);
	}
}
if($ItemName == "" && $area){
	$ItemName = $objfis->GetItemName($itemcode, $area);
}
if($ItemName == ""){
	$ItemName = $objfis->GetAccount($itemcode);
	$ItemName = $ItemName['code_desc'];
	$$tanscode = $itemCode;
}	


$smarty->assign('sItemName', '<input type="text" style="width:40%" id="ItemName" class="segInput" value="'.$ItemName.'" disabled="disabled"/>');

$selectedLD == "";
$selectedRD == "";
$selectedPH == "";
$selectedMS == "";

if($area == 'LD')
	$selectedLD = "selected";
else if($area =='RD')
	$selectedRD = "selected";
else if($area == 'PH')
	$selectedPH = "selected";
else if($area == 'OT')
	$selectedMS = "selected";
else
	$selected = '0';

$smarty->assign('sCostArea',"<select class='segInput' id='CostCenterArea' name='CostCenterArea' style='width:80%'' disabled='disabled'> 
						<option value='0'>-Select an area-</option>
	                    <option value='LD' ".$selectedLD.">Laboratory</option>
	                    <option value='RD' ".$selectedRD.">Radiology</option>
	                    <option value='PH' ".$selectedPH.">Pharmacy</option>
	                    <option value='OT' ".$selectedMS.">Miscellaneous</option>
					</select>");

$AccountSelected = $objfis->GetAccountlist($tanscode);

if($AccountSelected){
    $accountOption ="<option value =".$AccountSelected[0][0]." selected>".$AccountSelected[0][1]."</option>";
}

$smarty->assign('sTransaction', "<select class='segInput' id='accountTransaction' name='accountTransaction' style='width:80%' disabled='disabled'> 
						<option value='0'>-Select an area-</option>
						".$accountOption."
					</select>");


$AccountChart = $objfis->GetAccountChart();

$FisCharts = $curl_obj->getAllAccounts();

$FisCharts = json_decode($FisCharts, true);

// print_r($FisCharts);exit;

$areaArray = array('LD','RD','PH','OT');
$hasAreaCheck = "";
for ($i=0; $i <count($areaArray) ; $i++) { 
	if($areaArray[$i] == $area){
		$hasAreaCheck = 1;
	}
}

if($hasAreaCheck !== 1){
		$GetAccount = $objfis->Getdbaccountid($tanscode, $area="", $itemcode);
		for ($i=0; $i < count($FisCharts); $i++) { 
			if($FisCharts[$i]['account_code'] == $GetAccount['debit_id']){
				$AccTransOptDebit .="<option value =".$FisCharts[$i]['account_code']." selected>".$FisCharts[$i]['account_name']."</option>";
			}else{
				$AccTransOptDebit .="<option value =".$FisCharts[$i]['account_code'].">".$FisCharts[$i]['account_name']."</option>";
			}
			
			if($FisCharts[$i]['account_code'] == $GetAccount['credit_id']){
				$AccTransOptCredit .="<option value =".$FisCharts[$i]['account_code']." selected>".$FisCharts[$i]['account_name']."</option>";
			}else{
				$AccTransOptCredit .="<option value =".$FisCharts[$i]['account_code'].">".$FisCharts[$i]['account_name']."</option>";
			}

			if($transactionCode == "ins"){
				if($FisCharts[$i]['account_code'] == $GetAccount['tax_account']){
					$AccTransOptTax .="<option value =".$FisCharts[$i]['account_code']." selected>".$FisCharts[$i]['account_name']."</option>";
				}else{
					$AccTransOptTax .="<option value =".$FisCharts[$i]['account_code'].">".$FisCharts[$i]['account_name']."</option>";
				}

				if($FisCharts[$i]['account_code'] == $GetAccount['income_account']){
					$AccTransOptIncome .="<option value =".$FisCharts[$i]['account_code']." selected>".$FisCharts[$i]['account_name']."</option>";
				}else{
					$AccTransOptIncome .="<option value =".$FisCharts[$i]['account_code'].">".$FisCharts[$i]['account_name']."</option>";
				}
				
				if($FisCharts[$i]['account_code'] == $GetAccount['cash_account']){
					$AccTransOptCash .="<option value =".$FisCharts[$i]['account_code']." selected>".$FisCharts[$i]['account_name']."</option>";
				}else{
					$AccTransOptCash .="<option value =".$FisCharts[$i]['account_code'].">".$FisCharts[$i]['account_name']."</option>";
				}
				
			}
			
		}

			$smarty->assign('sDebit', "<tr><td width='25%' align='right'><strong>Debit</strong></td>");
			$smarty->assign('sDebitAccount', "<td align='left'><select class='segInput' id='DebitAccount' name ='DebitAccount' style='width:80%'> 
												<option value='0'>-Select an area-</option>
												".$AccTransOptDebit."
												</select>
											</td></tr>");

			$smarty->assign('sCredit', "<tr><td width='25%' align='right'><strong>Credit</strong></td>");
			$smarty->assign('sCreditAccount', "<td align='left'><select class='segInput' id='CreditAccount' name ='CreditAccount' style='width:80%'> 
												<option value='0'>-Select an area-</option>
												".$AccTransOptCredit."
												</select>
												</td></tr>");

			if($transactionCode == "ins"){ //if transaction is for insurance
				$smarty->assign('sTax',"<tr><td width='25%' align='right'><strong>Witholding Tax</strong></td>");
				$smarty->assign('sTaxAccount', "<td align='left'><select class='segInput' id='TaxAccount' name='TaxAccount' style='width:80%'> 
													<option value='0'>-Select an area-</option>
													".$AccTransOptTax."
													</select>
												</td></tr>");
				
				$smarty->assign('sIncome',"<tr><td width='25%' align='right'><strong>Excess Amount</strong></td>");
				$smarty->assign('sIncomeAccount', "<td align='left'><select class='segInput' id='IncomeAccount' name='IncomeAccount' style='width:80%'> 
													<option value='0'>-Select an area-</option>
													".$AccTransOptIncome."
													</select>
												</td></tr>");

				$smarty->assign('sCash',"<tr><td width='25%' align='right'><strong>Unclaimed Amount</strong></td>");
				$smarty->assign('sCashAccount', "<td align='left'><select class='segInput' id='CashAccount' name='CashAccount' style='width:80%'> 
													<option value='0'>-Select an area-</option>
													".$AccTransOptCash."
													</select>
												</td></tr>");
			}
}else{
		
		$GetAccount = $objfis->Getdbaccountid($tanscode, $area, $itemcode);

		for ($i=0; $i < count($FisCharts); $i++) { 
			if($FisCharts[$i]['account_code'] == $GetAccount['income_account']){
				$AccTransOptIncome .="<option value =".$FisCharts[$i]['account_code']." selected>".$FisCharts[$i]['account_name']."</option>";
			}else{
				$AccTransOptIncome .="<option value =".$FisCharts[$i]['account_code'].">".$FisCharts[$i]['account_name']."</option>";
			}
			
			if($FisCharts[$i]['account_code'] == $GetAccount['cash_account']){
				$AccTransOptCash .="<option value =".$FisCharts[$i]['account_code']." selected>".$FisCharts[$i]['account_name']."</option>";
			}else{
				$AccTransOptCash .="<option value =".$FisCharts[$i]['account_code'].">".$FisCharts[$i]['account_name']."</option>";
			}
			
			if($FisCharts[$i]['account_code'] == $GetAccount['tax_account']){
				$AccTransOptTax .="<option value =".$FisCharts[$i]['account_code']." selected>".$FisCharts[$i]['account_name']."</option>";
			}else{
				$AccTransOptTax .="<option value =".$FisCharts[$i]['account_code'].">".$FisCharts[$i]['account_name']."</option>";
			}
			
			if($FisCharts[$i]['account_code'] == $GetAccount['inventory_account']){
				$AccTransOptInventory .="<option value =".$FisCharts[$i]['account_code']." selected>".$FisCharts[$i]['account_name']."</option>";
			}else{
				$AccTransOptInventory .="<option value =".$FisCharts[$i]['account_code'].">".$FisCharts[$i]['account_name']."</option>";
			}

			if($FisCharts[$i]['account_code'] == $GetAccount['cogs_account']){
				$AccTransOptCOGS .="<option value =".$FisCharts[$i]['account_code']." selected>".$FisCharts[$i]['account_name']."</option>";
			}else{
				$AccTransOptCOGS .="<option value =".$FisCharts[$i]['account_code'].">".$FisCharts[$i]['account_name']."</option>";
		
			}
		}

		$smarty->assign('sIncome',"<tr><td width='25%' align='right'><strong>Income Account</strong></td>");
		$smarty->assign('sIncomeAccount', "<td align='left'><select class='segInput' id='IncomeAccount' name='IncomeAccount' style='width:80%'> 
											<option value='0'>-Select an area-</option>
											".$AccTransOptIncome."
											</select>
										</td></tr>");

		$smarty->assign('sCash',"<tr><td width='25%' align='right'><strong>Cash Account</strong></td>");
		$smarty->assign('sCashAccount', "<td align='left'><select class='segInput' id='CashAccount' name='CashAccount' style='width:80%'> 
											<option value='0'>-Select an area-</option>
											".$AccTransOptCash."
											</select>
										</td></tr>");

		$smarty->assign('sTax',"<tr><td width='25%' align='right'><strong>Tax (VAT) Account</strong></td>");
		$smarty->assign('sTaxAccount', "<td align='left'><select class='segInput' id='TaxAccount' name='TaxAccount' style='width:80%'> 
											<option value='0'>-Select an area-</option>
											".$AccTransOptTax."
											</select>
										</td></tr>");

		$smarty->assign('sInventory',"<tr><td width='25%' align='right'><strong>Inventory Account</strong></td>");
		$smarty->assign('sInventoryAccount', "<td align='left'><select class='segInput' id='InventoryAccount' name='InventoryAccount' style='width:80%'> 
											<option value='0'>-Select an area-</option>
											".$AccTransOptInventory."
											</select>
										</td></tr>");

		$smarty->assign('sCOGS',"<tr><td width='25%' align='right'><strong>COGS Account</strong></td>");
		$smarty->assign('sCOGSCredit', "<td align='left'><select class='segInput' id='COGSAccount' name='COGSAccount' style='width:80%'> 
											<option value='0'>-Select an area-</option>
											".$AccTransOptCOGS."
											</select>
										</td></tr>");
}



$smarty->assign('sbtnsave','<button class="segButton" onclick="CheckFields();">Save</button>');



?>
<input type="hidden" name="itemCode" id="itemCode" value="<?echo $itemcode?>">
<input type="hidden" name="area" id="area" value="<?echo $area?>">
<?

$smarty->assign('rootpath', $root_path);
ob_start();
?>


<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" id="userck" value="<?php echo $userck?>">
<input type="hidden" name="encoder" id="encoder" value="<?php echo  str_replace(" ","+",$_COOKIE[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">

<?
$sTemp = ob_get_contents();
$sTable = ob_get_contents();
ob_end_clean();
$smarty->assign('sTable',$sTable);
$smarty->assign('sHiddenInputs',$sTemp);
//$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

$smarty->assign('sMainBlockIncludeFile','system_admin/fis_mapping_update.tpl');
$smarty->display('common/mainframefis.tpl');

?>