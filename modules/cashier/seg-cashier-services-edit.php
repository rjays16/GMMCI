<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

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

$title=$LDPharmacy;
$breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND."&userck=$userck";
$thisfile='seg-cashier-services-edit.php';


# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/class_cashier_service.php");
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');


$pclass = new SegCashierService();
$service_code = $_REQUEST['service_code'];
	
# Saving	
if (isset($_POST["submitted"])) {
	$data = array(
		'name'=>$_POST['name'],
		'name_short'=>$_POST['name_short'],
		'price'=>$_POST['price'],
		'description'=>$_POST['description'],
		'account_type'=>$_POST['account_type'],
		'lockflag'=>($_POST['lockflag'] ? '1' : '0'),
        'is_ER_default'=>($_POST['er_default'] ? '1' : '0'),    // Added by LST for ER Billing -- 12.02.2008
                'dept_nr'=>$_POST['department'],	//added by cha, for cmap use- 11.26.2010
		'modify_id'=>$_SESSION['sess_temp_userid'],
		'modify_time'=>date('YmdHis'),
	);
	
//	if ($_REQUEST['target'] == 'databank') $data['is_billing_related'] = 0;
//	if ($_REQUEST['target'] == 'miscellaneous') $data['is_billing_related'] = 1;

	if (!$service_code) {
		$data['service_code']=$pclass->getLastNr();
		$data['create_id']=$_SESSION['sess_temp_userid'];
		$data['create_time']=date('YmdHis');
		$data["history"] = "Create: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n";
		$pclass->setDataArray($data);
		$saveok=$pclass->insertDataFromInternalArray();
		$service_code=$data['service_code'];
	}
	else {
		$data["history"] = $pclass->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");
		$pclass->setDataArray($data);
		$pclass->where = "service_code=".$db->qstr($service_code);
		$saveok=$pclass->updateDataFromInternalArray();
		$service_code =$_REQUEST['service_code'];
	}
	
	if ($saveok) {
		$smarty->assign('sysInfoMessage',"Product successfully saved!");
	}
	else {
		# Payment not saved
		$smarty->assign('sysErrorMessage',"Error processing request...<br>Error:".$db->ErrorMsg()."<br>".$pclass->sql);
	}
}

if ($service_code) {
	$smarty->assign('bEditMode',1);
	$Row = $pclass->getServiceInfo($service_code);
	if (!$Row) {
		die("Invalid product code.<br>SQL:$pclass->sql<br>DBError:".$db->ErrorMsg());
		exit;
	}
}
else {
	$Row['service_code'] = $pclass->getLastNr();
}

if ($saveok) {
	ob_start();
		$code = $service_code;
		$sTemp='';
?>
<script language="javascript" type="text/javascript">
<!--
	window.parent.$('name_<?= $code ?>').innerHTML = "<?= addslashes($Row['name']) ?>";
	window.parent.$('desc_<?= $code ?>').innerHTML = "<?= addslashes($Row['name_short']) ?>";
	window.parent.$('price_<?= $code ?>').innerHTML = "<?= ((float)$Row['price'] == 0) ? 'Arbitrary' : number_format($Row['price'],2,'.',',') ?>";
<?php if ($Row['_pt']) { ?>window.parent.$('ptype_<?= $code ?>').innerHTML = "<?= $Row['_pt'] ?>"; <?php } ?>
<?php if ($Row['_ct']) { ?>window.parent.$('type_<?= $code ?>').innerHTML = "<?= $Row['_ct'] ?>"; <?php } ?>
	window.parent.$('lock_<?= $code ?>').style.display = "<?= ($Row['lockflag']==1) ? '' : 'none'?>";
-->
</script>

<?php 
		$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->assign('sUpdateParentScript',$sTemp);
}

// Added by LST for ER Billing -- 12.02.2008
$smarty->assign('bBillingArea',($_REQUEST['target'] == 'miscellaneous' ? TRUE : FALSE));
	
$smarty->assign('sRootPath',$root_path);
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

# Title in the title bar
$smarty->assign('sToolbarTitle',"Cashier::Cashier services");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Cashier::Cashier services");

# Assign Body Onload javascript code
$onLoadJS='onload=""';
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

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	function updateTypeNames(obj) {
		var sel = obj.options[obj.options.selectedIndex];
		ct = sel.text;
		pt = sel.parentNode.label;
		$('_ct').value = ct;
		$('_pt').value = pt;
	}

	function validate(f) {
		// !! - Do this
    if (!f.account_type.value) {
      alert('Please select the account type for this service');
      f.account_type.focus();
      return false;
    }
		if (!f.name.value) {
			alert("Please enter a name for this service/item.");
			f.name.focus();
			return false;
		}
		if (!f.name_short.value) {
			alert("Please enter the shorthand name for this service/item.");
			f.name.focus();
			return false;
		}
		if (isNaN(f.price.value)) {
			alert("Please enter a valid value this service/item's price.");
			f.name.focus();
			return false;
		}
		return true;
	}
-->
</script>

<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages

# Render form values
if (!$Row['price']) $Row['price']='0.00';
$smarty->assign('sServiceCode','<input class="jedInput" type="text" name="service_code_ex" id="service_code" size="10" value="'.$Row['service_code'].'" disabled="disabled"/>');
#$smarty->assign('sServiceName','<input class="jedInput" type="text" name="name" id="name" size="30" value="'.$Row['name'].'"/>');

#edited by VAN 10-20-2011
#change to textarea and eliminate the maxsize of the string
$smarty->assign('sServiceName','<textarea class="jedInput" id="name" name="name" cols="37" rows="2">'.$Row['name'].'</textarea>');

$smarty->assign('sPrice','<input class="jedInput" type="text" name="price" id="price" style="" value="'.number_format($Row['price'],2,'.','').'"/>');
$smarty->assign('sShortName','<input class="jedInput" type="text" maxlength="10" name="name_short" id="name_short" style="" value="'.$Row['name_short'].'"/>');
$smarty->assign('sDescription','<textarea class="jedInput" cols="27" rows="2" name="description" id="description">'.$Row['description'].'</textarea>');
$smarty->assign('sIsLocked','<input class="jedInput" type="checkbox" name="lockflag" id="lockflag" '.($Row['lockflag'] ? 'checked="checked"' : '').'/>');

$smarty->assign('sIsERDefault','<input class="jedInput" type="checkbox" name="er_default" id="er_default" '.($Row['is_ER_default'] ? 'checked="checked"' : '').'/>');

# Product classification
	$types = array();
	if ($_REQUEST['target'] == 'miscellaneous') 
		$result = $pclass->getAccountTypes(FALSE,NULL,TRUE);
	else
		$result = $pclass->getAccountTypes();
	if ($result) {
		while ($row=$result->FetchRow()) $types[] = $row;
	}

	$subtypes = array();
	if ($_REQUEST['target'] == 'miscellaneous') 
		$result = $pclass->getSubAccountTypes(NULL,FALSE,NULL,TRUE);
	else
		$result = $pclass->getSubAccountTypes();
	if ($result) {
		while ($row=$result->FetchRow()) {
			if (!$subtypes[$row['parent_type']]) $subtypes[$row['parent_type']] = array();
				$subtypes[$row['parent_type']][] = $row;
		}
	}
	
	$typeHTML = "";
	foreach ($types as $type) {
		$typeHTML.= '						<optgroup label="'.$type['name_long'].'">';
		if (is_array($subtypes[$type['type_id']])) {
			foreach ($subtypes[$type['type_id']] as $subtype) {
				$checked=strtolower($subtype['type_id'])==strtolower($Row['account_type']) ? 'selected="selected"' : "";
				$typeHTML.="							<option value=\"".$subtype["type_id"]."\" $checked>".$subtype['name_long']."</option>\n";
				$count++;
			}
		}
/*
		else {
			$checked=strtolower($subtype['type_id'])==strtolower($_REQUEST['type']) ? 'selected="selected"' : "";
			$typeHTML.="							<option value=\"".$type["type_id"]."\" $checked>".$type['name_long']."</option>\n";
		}
		$typeHTML.= '						</optgroup>'; */
	}

	$typeHTML = "<select id=\"account_type\" name=\"account_type\" class=\"jedInput\" style=\"width:170px\" onchange=\"updateTypeNames(this)\">
  <option value=\"\">-Select account type-</option>
".
		$typeHTML. 
		"					</select>";$smarty->assign('sSelectAccountType',$typeHTML);
$smarty->assign('sAccountType',$typeHTML);

//added by cha, 11-26-2010
$dept_sql = "SELECT nr, id, name_formal FROM care_department \n".
												"WHERE type=1 AND status='' \n".
												"ORDER BY name_formal";
$res = $db->Execute($dept_sql);
$departmentHTML = "<select id=\"department\" name=\"department\" class=\"jedInput\" style=\"width:170px\">
	<option value=\"\">-Select department-</option>";
while($row=$res->FetchRow()) {
	$departmentHTML.="<option value='".$row['nr']."' ".($row['nr']==$Row['dept_nr']? "selected='selected'" : "").">".$row['name_formal']."</option>";
}
$smarty->assign('sDepartment',$departmentHTML);

if ($service_code)
 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&service_code='.$service_code.'&target='.$_REQUEST['target'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate(this)">');
else
 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target='.$_REQUEST['target'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate(this)">');
$smarty->assign('sFormEnd','</form>');


ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1" />
  <input type="hidden" name="service_code" value="<?php echo $_REQUEST['service_code'] ?>">
  <input type="hidden" name="dept" value="<?php echo $sDept?>">
  <input type="hidden" name="sid" value="<?php echo $sid?>">
  <input type="hidden" name="lang" value="<?php echo $lang?>">
  <input type="hidden" name="cat" value="<?php echo $cat?>">
  <input type="hidden" name="userck" value="<?php echo $userck?>">  
  <input type="hidden" name="mode" id="modeval" value="<?php if($saveok||$service_code) echo "update"; else echo "save"; ?>">
  <input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
  <input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
  <input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
  <input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
  <input type="hidden" id="_pt" name="_pt" value="">
  <input type="hidden" id="_ct" name="_ct" value="">
<?php 

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenInputs',$sTemp);

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','cashier/cashier_databank_form.tpl');
$smarty->display('common/mainframe.tpl');

?>
