<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/supply_office/ajax/databank.common.php");

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
global $db;

$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$breakfile=$root_path."modules/supply_office/seg-supply-functions.php".URL_APPEND."&userck=$userck";
$imgpath=$root_path."pharma/img/";
$thisfile='seg-inventory-product-edit.php';


# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/class_pharma_product.php");
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
include_once($root_path."include/care_api_classes/curl/class_curl.php");
include_once($root_path."include/care_api_classes/inventory/class_sku_inventory.php");
$smarty = new smarty_care('common');


$pclass = new SegPharmaProduct();
$curl = new Rest_Curl();
$skuObj = new SkuInventory();

# Saving
if (isset($_POST["submitted"])) {
	$db->StartTrans();
//    $db->debug = true;
	$type_nr = $pclass->getTypebyProdClass($_POST['prod_class']);

	$data = array(
		'bestellnum'=>$_POST['bestellnum'],
		'generic'=>$_POST['generic'],
		'artikelname'=>$_POST['artikelname'],
		'description'=>$_POST['description'],
		'is_socialized'=>(isset($_POST['is_socialized']) ? 1 : 0),
		'is_restricted'=>(isset($_POST['is_restricted']) ? 1 : 0),
		'type_nr' => $type_nr['nr'],
		'packing' => $_POST['packing'],
		'unit' => $_POST['pc_unit_id'],
		'prod_class'=>$_POST['prod_class'],
		'price_cash'=>$_POST['price_cash'],
		'price_charge'=>$_POST['price_charge'],
		'create_id'=>$_SESSION['sess_temp_userid'],
		'modify_id'=>$_SESSION['sess_temp_userid'],
		'modify_time'=>date('YmdHis'),
		'create_time'=>date('YmdHis'),
		'is_compensable'=>(isset($_POST['is_compensable']) ? '1' : '0'),
		'supplier_price'=>$_POST['supplier_price'],
		'exclude_hcareid'=>(($_POST['exclude_hcareid']) ? $_POST['exclude_hcareid'] : 'NULL')    // Added by LST for PHIC Billing -- 04.13.2010
	);

	//fis-data
	$fis_data=array(
				"id"=>$_POST['bestellnum'],
				"item_desc"=>$_POST["artikelname"],
				"item_category"=>$_POST["category_string"],
				"unit"=>$_POST['pack_unit_id']
			);

	if ($_GET['nr']) {
		$data["history"] = $pclass->ConcatHistoryType("Update: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");

        $pclass->setDataArray($data);
		$pclass->where = "bestellnum=".$db->qstr($_GET['nr']);
		$saveok=$pclass->updateDataFromInternalArray($_GET["nr"],FALSE);

		//fis-integration
		if($saveok){
			$saveok=$curl->updateItem($fis_data);
		}
	}
	else {
		$_POST['bestellnum']= $pclass->createNR();
        //$pclass->getCode($_POST['artikelname'], $type_nr['nr'], $_POST['pc_unit_id']);
		$data["history"] = $pclass->ConcatHistoryType('Created: '.date('Y-m-d H:i:s').' ['.$_SESSION['sess_temp_userid'].']\n');
		$pclass->setDataArray($data);
		$saveok=$pclass->insertDataFromInternalArray();
		$_GET['nr'] = $_POST['bestellnum'];

		//fis-integration
		if($saveok){
			$saveok=$curl->insertItem($fis_data);
		}
	}

	if ($saveok) {
		$savok = $pclass->clearProductAvailability($_GET['nr']);
	}

	if ($saveok) {
		$saveok=$pclass->clearProductClassification($_GET['nr']);
	}

	if ($saveok) {
		$saveok=$pclass->clearProductDiscounts($_GET['nr']);
	}

//	if ($saveok) {
//		if ($_POST['availability']) {
//			$saveok=$pclass->setProductAvailability($_GET['nr'], $_POST['availability']);
//		}
//	}

	if ($saveok) {
		if ($_POST['classification']) {
			$classificationArr = explode(",",$_POST['classification']);
			$saveok=$pclass->setProductClassification($_GET['nr'], $classificationArr);
		}
	}

	if ($saveok) {
		if ($_POST['discounts']) {
			$saveok=$pclass->setProductDiscounts($_GET['nr'], $_POST['discounts'], $_POST['price']);
		}
	}

	if ($saveok) {
		if ($_POST['pc_unit_id']) {
			$RowExtended = $pclass->getExtendedProductInfo($_GET['nr']);
			if($RowExtended) {
				$saveok=$pclass->updateExtendedInfo($_POST['bestellnum'],$_POST['pc_unit_id'],$_POST['pack_unit_id'],$_POST['qty_per_pack'],$_POST['min_qty']);
			}
			else {
				$saveok=$pclass->setExtendedInfo($_POST['bestellnum'],$_POST['pc_unit_id'],$_POST['pack_unit_id'],$_POST['qty_per_pack'],$_POST['min_qty']);
			}
		}
	}
//    $db->failTrans();
	if ($saveok) {
		$db->CompleteTrans();
		$smarty->assign('sysInfoMessage',"Product successfully saved!");
	}
	else {
		$error_msg = $db->ErrorMsg();
		$db->FailTrans();
		$db->CompleteTrans();
		$smarty->assign('sysErrorMessage',"Error processing request...<br>Error:".$error_msg."<br/>SQL:".$pclass->sql);
	}

	$Row = $_POST;
	$RowExtended = $_POST;
	$Row['availability'] = implode(",",$_POST['availability']);

}
else {
	if ($_GET['nr']) {
		$NR = $_GET['nr'];
		$Row = $pclass->getProductInfo($NR);
		$RowExtended = $pclass->getExtendedProductInfo($NR);
		if (!$Row) {
			die("Invalid product code.");
			exit;
		}
	}
}


$smarty->assign('sRootPath',$root_path);
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

# Title in the title bar
$smarty->assign('sToolbarTitle',"Inventory::Supply databank");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Inventory::Supply databank");

# Assign Body Onload javascript code
$onLoadJS='onload="onChangeProdClass();optTransfer.init(document.forms[0]);$(\'generic\').focus(); loadHealthInsurances('.$Row['exclude_hcareid'].');"';   //toggleCheckboxesByName(\'availability[]\',true);

#$onLoadJS='onload="$(\'generic\').focus()"';
$smarty->assign('sOnLoadJs',$onLoadJS);

# Collect javascript code

ob_start();
		 # Load the javascript code
?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
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
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/OptionTransfer.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/cashier-main.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
var trayItems = 0;
var suspendAjax=0;

var optTransfer = new OptionTransfer("srclist","destlist");
optTransfer.setAutoSort(true);
optTransfer.setDelimiter(",");
optTransfer.setStaticOptionRegex("");
optTransfer.saveNewRightOptions("classification");

function validate(f) {
	if (!f.prod_class.value) {
		alert('Please select the product type...');
		f.artikelname.focus();
		return false;
	}
	if (!f.artikelname.value) {
		alert('Please enter the product name...');
		f.artikelname.focus();
		return false;
	}
	if (!f.artikelname.value) {
		alert('Please enter the product name...');
		f.artikelname.focus();
		return false;
	}
	if (!f.qty_per_pack.value || f.qty_per_pack.value <= 0) {
		alert('Please enter a valid value for the quantity per pack...');
		f.qty_per_pack.focus();
		return false;
	}
	if (!f.min_qty.value || f.min_qty.value <= 0) {
		alert('Please enter a valid value for the reorder point...');
		f.min_qty.focus();
		return false;
	}
	return true;
}

function onChangeProdClass(val) {
	var dropdown = $('prod_class');
	
	$('generic').disabled = ($('prod_class').value != 'M')
	$('class_equip').hide();
	$('class_meds').hide();
	$('class_nms').hide();
	$('class_blood').hide();
	if ((val=='S')||(val=='M')){
		$('class_meds').show();
	}
	if ((val=='E')){
		$('class_equip').show();
	}
	if ((val=='NS')){
		$('class_nms').show();
	}
	if ((val=='B')){
		$('class_blood').show();
	}

	$("category_string").value = dropdown.options[dropdown.selectedIndex].text;
}

function previewCode() {
	var name = $('artikelname').value;
	var unit = $('pc_unit_id').value;
	var prod = $('prod_class').value;
	$('ajax-load').show();
	xajax.call('getCode',
		{
			parameters:[name, prod, unit],
			onComplete:function() {
				$('ajax-load').hide();
			}
		}
	)
}

// --- added by LST --- 04.13.2010 ------ start ------------
function js_setOption(tagId, value){
	document.getElementById(tagId).value = value;
}// end of function js_setOption

function js_AddOptions(tagId, text, value){
	var elTarget = document.getElementById(tagId);
	if(elTarget){
		var opt = new Option(text, value);
		//var opt = new Option(value, value);
		opt.id = value;
		elTarget.appendChild(opt);
	}
	var optionsList = elTarget.getElementsByTagName('OPTION');
}//end of function js_AddOption

function js_ClearOptions(tagId){
	var optionsList, el=document.getElementById(tagId);
	if(el){
		optionsList = el.getElementsByTagName('OPTION');
		for(var i=optionsList.length-1; i >=0 ; i--){
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}//end of function js_ClearOptions

function loadHealthInsurances(hcare_id) {
	if (typeof(hcare_id) == 'undefined') hcare_id = 0;
	xajax_getHealthInsurances(hcare_id);
}
// --- added by LST --- 04.13.2010 ------  end -------------
-->
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages

# Render form values
if (!$_GET['nr']) {
    $nr = $pclass->createNR();
	$smarty->assign('sProductCode','<input class="segInput" type="text" name="bestellnum" id="bestellnum" size="20" value="'.$nr.'" readonly="readonly"/>');
}
else {
	$smarty->assign('sProductCode','<input class="segInput" type="text" name="bestellnum" id="bestellnum" size="20" value="'.$_GET['nr'].'" readonly="readonly"/>');
}

$smarty->assign('sGenericName','<input class="segInput" type="text" name="generic" id="generic" size="30" value="'.$Row['generic'].'" '.($Row['prod_class']=='S' ? 'disabled="disabled"' : '').'/>');
$smarty->assign('sProductName','<input class="segInput" type="text" name="artikelname" id="artikelname" size="30" value="'.htmlentities($Row['artikelname']).'" '.($_GET['nr']?'':'onblur="previewCode()" onfocus="previewCode()"').'/>');
$smarty->assign('sDescription','<textarea class="segInput" cols="27" rows="2" name="description" id="description">'.$Row['description'].'</textarea>');
$smarty->assign('sIsSocialized','<input class="segInput" type="checkbox" name="is_socialized" id="is_socialized" '.($Row['is_socialized'] ? 'checked="checked"' : '').'/>');
$smarty->assign('sIsRestricted','<input class="segInput" type="checkbox" name="is_restricted" id="is_restricted" '.($Row['is_restricted'] ? 'checked="checked"' : '').'/>');
$smarty->assign('sIsCompensable','<input class="segInput" type="checkbox" name="is_compensable" id="is_compensable" '.($Row['is_compensable'] ? 'checked="checked"' : '').'/>');
$smarty->assign('sExcludeHID','<select id="exclude_hcareid" name="exclude_hcareid" style="font:bold 12px Arial">
										<option value="0">- Select Insurance -</option>
									</select>');
$smarty->assign('sCashPrice','<input class="segInput" type="text" name="price_cash" id="price_cash" style="text-align:right" value="'.number_format($Row['price_cash'],2).'"/>');
$smarty->assign('sChargePrice','<input class="segInput" type="text" name="price_charge" id="price_charge" style="text-align:right" value="'.number_format($Row['price_charge'],2).'"/>');
$smarty->assign('sPreparation','<input class="segInput" type="text" name="packing" id="packing" size="15" value="'.$Row['packing'].'" />');
$smarty->assign('sSuppPrice','<input class="segInput" type="text" name="supplier_price" id="supplier_price" style="text-align:right" value="'.number_format($skuObj->getItemAvgCost($Row['bestellnum'],'','IP'),2).'"/>');
$smarty->assign('sProductType',
'<select class="segInput" id="prod_class" name="prod_class" onchange="onChangeProdClass(this.value);'.($_GET['nr']?'':'previewCode()"').'">
	<option value="">--Select the product type--</option>
	<option value="M" '.($Row['prod_class']=='M' ? 'selected="selected"' : '').'>Medicines</option>
	<option value="S" '.($Row['prod_class']=='S' ? 'selected="selected"' : '').'>Supplies</option>
	<option value="E" '.($Row['prod_class']=='E' ? 'selected="selected"' : '').'>Equipments</option>
	<option value="NS" '.($Row['prod_class']=='NS' ? 'selected="selected"' : '').'>Non Medical Supplies</option>
	<option value="B" '.($Row['prod_class']=='B' ? 'selected="selected"' : '').'>Blood</option>
	<option value="HS" '.($Row['prod_class']=='HS' ? 'selected="selected"' : '').'>Housekeeping Supplies</option>
</select>');

$smarty->assign('sCategoryString', '<input type="hidden" id="category_string" name="category_string">');

# Product classification
$classificationHTML = "<select id=\"srclist\" name=\"srclist\" class=\"segInput\" size=\"10\" multiple=\"multiple\" style=\"width:200px\">\n";
if ($Row["classification"])
	$result = $db->Execute("SELECT * FROM seg_product_classification WHERE lockflag=0 AND class_code NOT IN (".$Row["classification"].") ORDER BY class_name");
else
	$result = $db->Execute("SELECT * FROM seg_product_classification WHERE lockflag=0 ORDER BY class_name");
if ($result) {
	while ($row=$result->FetchRow()) {
		$classificationHTML.="                        <option value=\"".$row["class_code"]."\">".$row['class_name']."</option>\n";
	}
}
$classificationHTML .= "                    </select>";
$smarty->assign('sSelectClassification',$classificationHTML);

if ($Row["classification"]) {
	$result = $db->Execute("SELECT * FROM seg_product_classification WHERE lockflag=0 AND class_code IN (".$Row["classification"].") ORDER BY class_name");
	$destHTML = "<select id=\"destlist\" name=\"destlist\" class=\"segInput\" size=\"10\" multiple=\"multiple\" style=\"width:200px\">\n";
	while ($row=$result->FetchRow()) {
		$destHTML.="                        <option value=\"".$row["class_code"]."\">".$row['class_name']."</option>\n";
	}
	$destHTML.="                    </select>";
	$smarty->assign('sSelectClassification2',$destHTML);
}
else
	$smarty->assign('sSelectClassification2','<select id="destlist" name="destlist" class="segInput" size="10" multiple="multiple" style="width:200px"></select>');

# Availability
$availHTML = "";
# Load the areas list
require_once($root_path.'include/care_api_classes/class_product.php');
$prod_obj=new Product;
$result=$prod_obj->getAllPharmaAreas();
if ($result) {
	while ($row=$result->FetchRow()) {
		$checked = ((strpos($Row['availability'], $row['area_code']) !== FALSE) || $_GET['stat']=="new") ? 'checked="checked"' : '';
		$availHTML.="
			<span style=\"white-space:nowrap\">
					<input class=\"segInput\" id=\"avail".$row['area_code']."\" name=\"availability[]\" type=\"checkbox\" value=\"".$row["area_code"]."\" $checked /><label class=\"segInput\" for=\"avail".$row['area_code']."\">".$row["area_name"]."</label>
			</span>\n";
	}
}
$smarty->assign('sAvailability',$availHTML);

# Discounts
$discountHTML = "<select id=\"sel-discount\" class=\"segInput\">\n";
$result = $db->Execute("SELECT * FROM seg_discount WHERE lockflag=0 ORDER BY discountdesc");
if ($result) {
		while ($row=$result->FetchRow()) {
				$discountHTML.="                        <option value=\"".$row["discountid"]."\">".$row['discountdesc']."</option>\n";
		}
}
$discountHTML .= "                    </select>";
$smarty->assign('sSelectDiscount',$discountHTML);

if ($_GET['nr'] && !$_POST['submitted']) {
		$result=$pclass->getProductDiscounts($nr);
		if ($result) {
				$count=0;
				$Row['discounts'] = array();
				$Row['price'] = array();
				while ($row=$result->FetchRow()) {
						$Row['discounts'][] = $row['discountid'];
						$Row['price'][] = $row['price'];
				}
		}
}

$count = 0;
if ($Row['discounts']) {
	foreach ($Row['discounts'] as $i=>$v) {
		$class = (($count%2)==0)?"":"wardlistrow2";
		if ($Row['price'][$i]==0)
				$showPrice = 'Arbitrary';
		else
				$showPrice = number_format($Row['price'][$i],2);
		$name = $db->GetOne("SELECT discountdesc FROM seg_discount WHERE discountid='$v'");
		$rows .= '<tr class="'.$class.'" id="row'.$v.'">
	<td>
		<input type="hidden" name="discounts[]" id="id'.$v.'" value="'.$v.'" />
		<input type="hidden" name="price[]" id="price'.$v.'" value="'.$Row['price'][$i].'" />
		<span style="color:#660000">'.$name.'</span>
	</td>
	<td class="rightAlign">
		<span id="show-price'.$v.'">'.$showPrice.'</span>
	</td>
	<td class="centerAlign">
		<img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeDiscount(\''.$Row['discounts'][$i].'\')"/>
	</td>
</tr>';
		$count++;
	}
}

#for units and shared info for pc_unit_id tab -- added by bryan 03-25-09
$smallunit = "";
$bigunit = "";

$smallunit = $pclass->getSmallUnitOption();

//$smallunit = $pclass->getAllUnitOption();
$bigunit = $pclass->getBigUnitOption();
//$bigunit = $pclass->getAllUnitOption();

if($RowExtended['pc_unit_id']){
	$unitifoadd = $pclass->getUnitInfo($RowExtended['pc_unit_id']);
	$smallunit .= "<option value='".$unitifoadd['unit_id']."' selected >".$unitifoadd['unit_name']."</option>";
}

if($RowExtended['pack_unit_id']){
	$unitifoadd = $pclass->getUnitInfo($RowExtended['pack_unit_id']);
	$bigunit .= "<option value='".$unitifoadd['unit_id']."' selected >".$unitifoadd['unit_name']."</option>";
}

$smarty->assign('sSmallUnit',"<select class='segInput' id='pc_unit_id' name='pc_unit_id' ".($_GET['nr']?'':'onchange="previewCode()"').">$smallunit</select>");
$smarty->assign('sBigUnit',"<select class='segInput' id='pack_unit_id' name='pack_unit_id'> $bigunit </select>");

$smarty->assign('sPerPack','<input class="segInput" type="text" name="qty_per_pack" id="qty_per_pack" size="10" value="'.number_format(isset($RowExtended['qty_per_pack'])?$RowExtended['qty_per_pack']:1,0).'" />');
$smarty->assign('sMinQty','<input class="segInput" type="text" name="min_qty" id="min_qty" size="10" value="'.number_format(isset($RowExtended['min_qty'])?$RowExtended['min_qty']:1,0).'" />');
#

if (!$rows)
	$rows = '        <tr><td colspan="3">No discounts set...</td></tr>';
$smarty->assign('sDiscounts',$rows);

if ($_GET['nr'])
	$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&nr='.$_GET['nr'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate(this)">');
else
	$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'" method="POST" id="orderForm" name="inputform" onSubmit="return validate(this)">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1" />
	<input type="hidden" name="refno" value="<?php echo $sRefNo?>">
	<input type="hidden" name="dept" value="<?php echo $sDept?>">
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">
	<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<input class="segInput" type="button" align="center" value="Cancel payment">');
$smarty->assign('sContinueButton','<input class="segInput" type="submit" src="'.$root_path.'images/btn_submitorder.gif" align="center" value="Process payment">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','supply_office/inventory-databank-form.tpl');
$smarty->display('common/mainframe.tpl');

?>