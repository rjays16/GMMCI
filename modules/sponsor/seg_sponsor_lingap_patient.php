<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path."modules/sponsor/ajax/lingap_patient.common.php";


/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
0* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');

$local_user='ck_grants_user';
require_once($root_path.'include/inc_front_chain_lang.php');

# Create products object
$GLOBAL_CONFIG=array();

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

# $phpfd = config date format in PHP date() specification

if (!$_GET['from'])
	$breakfile=$root_path."modules/sponsor/seg-sponsor-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/cashier/seg-sponsor-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}
$thisfile='seg_sponsor_lingap_patient.php';


// check for valid permissions
require_once $root_path.'include/care_api_classes/class_user.php';
$user = SegUser::getCurrentUser();

$permissionSet = array('_a_1_lingaprequest');
$allow = $user->hasPermission($permissionSet);
if (!$allow)
{
	header('Location:'.$root_path.'main/login.php?'.
		'forward='.urlencode('modules/sponsor/'.$thisfile).
		'&break='.urlencode('modules/sponsor/seg-sponsor-functions.php'));
	exit;
}


require_once $root_path.'modules/listgen/listgen.php';
$listgen = new ListGen($root_path);


require_once $root_path."include/care_api_classes/sponsor/class_request.php";

global $db;

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);


//if (isset($_POST["submitted"]) && !$_REQUEST['viewonly']) {
//	$data = array(
//		'control_nr'=>$_POST['control_nr'],
//		'encounter_nr'=>$_POST['encounter_nr'],
//		'pid'=>$_POST['pid'],
//		'name'=>$_POST['name'],
//		'entry_date'=>$_POST['entry_date'],
//		'remarks'=>$_POST['remarks'],
//		'modify_id'=>$_SESSION['sess_temp_userid']
//	);

//	$lc->setDataArray($data);
//	$db->StartTrans();

//	if ($_POST['mode']=='edit') {
//		$data["history"]=$lc->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");
//		$lc->setDataArray($data);
//		$lc->where = "control_nr=".$db->qstr($_POST['control_nr']);
//		$saveok=$lc->updateDataFromInternalArray($_POST['control_nr'],FALSE);
//	}
//	else {
//		$data['create_id']=$_SESSION['sess_temp_userid'];
//		$data['history']="Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n";
//		$lc->setDataArray($data);
//		$saveok = $lc->insertDataFromInternalArray();
//	}

//	if ($saveok) {
//		# Bulk write entry items
//		$bulk = array();
//		foreach ($_POST["item"] as $i=>$v) {
//			$bulk[] = array(
//				$_POST["src"][$i],
//				$_POST["ref"][$i],
//				$_POST["item"][$i],
//				$_POST["service"][$i],
//				$_POST['amount'][$i],
//			);
//		}
//		$saveok=$lc->clearEntry($_POST['control_nr']);
//		if ($saveok) $saveok=$lc->addDetails($_POST['control_nr'], $bulk);
//	}

//	if ($saveok) {
//		$smarty->assign('sysInfoMessage','<div style="margin:6px">Lingap entry successfully saved!</div>');
//	}
//	else {
//		$db->FailTrans();
//		$errorMsg = $db->ErrorMsg();
//		if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
//			$smarty->assign('sysErrorMessage','<br><strong>Error:</strong>An entry with the same control number already exists in the database.');
//		else {
//			if ($errorMsg)
//				$smarty->assign('sysErrorMessage',"<br><strong>Error:</strong> $errorMsg");
//			else
//				$smarty->assign('sysErrorMessage',"<br><strong>Unable to save Lingap entry...</strong>");
//			#print_r($order_obj->sql);
//		}
//	}
//	$db->CompleteTrans();
//}


# Collect javascript code
ob_start();
	 # Load the javascript code
?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/modalbox/modalbox.js"></script>
<link rel="stylesheet" href="<?=$root_path?>css/themes/default/modalbox.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" language="javascript">

var isLoading=false

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function tooltip(text) {
	return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function resetControls() {
	$('name').value="";
	$('pid').value="";
	$('encounter_nr').value="";
	$('select-enc').disabled = false;

	if (typeof(rlst) == 'object') {
		rlst.fetcherParams = {};
		rlst.reload();
	}
	//new Effect.Appear($('rqsearch'),{ duration:0.5 });
}

function reclassRows(list,startIndex) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var dRows = dBody.getElementsByTagName("tr");
			if (dRows) {
				for (i=startIndex;i<dRows.length;i++) {
					dRows[i].className = "wardlistrow"+(i%2+1);
				}
			}
		}
	}
}

function addSSRequest(details) {
	list = $('rlst');
	if (list) {
		var dBody=list.select("tbody")[0];
		if (dBody) {
			if (!details) details = { FLAG: false};
			if (details['FLAG']) {
				var id=details["nr"],
					date=details["date"],
					entry=details["entry"],
					name=details["name"],
					ap=details["is_advance"],
					disabled=(details["disabled"]=='1');

				var dRows = dBody.select("tr");
				var alt = (dRows.length%2>0) ? 'alt':'';
				var disabledAttrib = disabled ? 'disabled="disabled"' : "";

				var row = new Element('tr', { class: alt, id:'ri_'+id , style:'height:26px' } );

				row.update( new Element('td', { class:'centerAlign' } ).update(
					new Element('span', { id: 'ri_date_'+id}).update(date))
				);

				row.insert( new Element('td', { class:'centerAlign' } ).update(
					new Element('span', { id: 'nr_'+id }).update(id))
				);

				row.insert( new Element('td', { class:'leftAlign' } ).update(
					new Element('span', { id: 'ri_name_'+id, style:'color:#660000'}).update(name))
				);

				var apImg='';
				if (entry) {
					apImg = (ap=='1' ?
						new Element('img', { id: 'ri_ap_'+id, src:'../../gui/img/common/default/warn2.gif', align: 'absmiddle', title: 'Advance purchase' }	).tooltip('Advance purchase') :
						new Element('img', { id: 'ri_ap_'+id, src:'../../gui/img/common/default/tick.png', align: 'absmiddle', title: 'Processed' } ).tooltip('Processed')
					);
				}

				row.insert(new Element('td', { class:'centerAlign' } ).update(apImg));

				row.insert(
					new Element('td', { class:'centerAlign' } ).update(
						entry ?
							new Element('img',
								{ id: 'ri_entry_'+id, src:'../../images/lingap_item.gif', class: 'link', align: 'absmiddle', title: 'Lingap entry' }
							)
							:
							''
					)
				);

				if (entry) {
					allowDelete = true;
				}
				else {
					allowDelete = false;
				}

				options = new Element('td', { class:'centerAlign' } ).update(
					new Element('img',{ id:'ri_edit_'+id, class:'link', src:'../../images/cashier_edit.gif' }
					).setStyle( { margin:'1px' }
					).observe( 'click',
						function(event) {
							openSSRequest(id);
						}
					).tooltip('Process SS request')
				).insert(
					new Element('img',{ id:'ri_view_'+id, class:'link', src:'../../images/cashier_view.gif' }
					).setStyle( { margin:'1px' }
					).observe( 'click',
						function(event) {
							openSSPrintout(id);
						}
					).tooltip('View Lingap printout')
				);

				if (allowDelete) {
					options.insert(
						new Element('img',{ id:'ri_delete_'+id, class:'link', src:'../../images/cashier_delete.gif' }
						).setStyle( { margin:'1px' }
						).observe( 'click',
							function(event) {
								confirmDelete(id);
							}
						).tooltip('Delete Lingap referral')
					);
				}

				row.insert(options);
				dBody.insert(row);
			}
			else {
				dBody.update('<tr><td colspan="6">List is currently empty...</td></tr>');
			}
			return true;
		}
	}
	return false;
}

function removeItem(id) {
	var destTable, destRows;
	var table = $('order-list');
	var rmvRow=$('row'+id);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		rmvRow.remove();
		if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
			appendOrder(table, null);
		reclassRows(table,rndx);
	}
}

function changeTransactionType() {
	clearEncounter();
	refreshDiscount();
}

//function clickGrant() {
//	if (parseFloatEx($('grant-amount').value)==0) {
//		alert('Please enter an amount...');
//		return false;
//	}
//	xajax.call('addGrant', { parameters:[ iSrc, iNr, iCode, iArea, $('grant-account').value, parseFloatEx($('grant-amount').value) ] });
//}

// callback triggered when Patient Search window is closed
function pSearchClose() {
	refreshLists();
	cClick();
}

function refreshLists() {
	var o = new Object();
	if (!$('pid').value) {
		rlst.clear();
	}
	else {
		o['pid'] = $('pid').value;
		if (typeof(rlst)=='object') {
			rlst.fetcherParams = o;
			rlst.reload();
		}
	}
}

function startLoading() {
	if (!isLoading) {
		isLoading = 1;
		return overlib('<strong>Loading items...</strong><br/><img src="../../images/ajax_bar.gif"/>',
			WIDTH,300, TEXTPADDING,5, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src="" border="0" style="display:none" />',
			CAPTION,'Loading',
			MIDX,0, MIDY,0,
			STATUS,'Loading');
	}
}

function doneLoading() {
	if (isLoading) {
		setTimeout('cClick()', 500);
		isLoading = 0;
	}
}

function validate() {
}

function openPatientSelect() {
	if ($('select-enc').hasClassName('disabled')) return false;
<?php
$var_arr = array(
	"var_pid"						=>"pid",
	"var_encounter_nr"	=>"encounter_nr",
	"var_name"					=>"name",
	"var_addr"					=>"address",
	"var_clear"					=>"clear-enc",
	"var_include_walkin"=>"0",
	"var_reg_walkin"		=>"0"
);
$vas = array();
foreach($var_arr as $i=>$v) {
	$vars[] = "$i=$v";
}
$var_qry = implode("&",$vars);
?>
	overlib(
			OLiframeContent('<?= $root_path ?>modules/registration_admission/seg-select-enc.php?<?=$var_qry?>&var_include_enc=0',
			700, 400, 'fSelEnc', 0, 'no'),
			WIDTH,700, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src="<?= $root_path ?>/images/close_red.gif" border="0" />',
			CAPTIONPADDING,2,
			CAPTION,'Select registered person',
			MIDX,0, MIDY,0,
			STATUS,'Select registered person');
	return false;
}

function confirmDelete(id) {
	Effect.Fade('ri_'+id, { duration: 0.5, from: 1, to: 0.5});
	var html =
		'<div class="MB_alert"><p>Cancel this Lingap referral?</p>'+
			'<button class="segButton" onclick="Modalbox.hide();xajax.call(\'cancelEntry\',{ parameters:[\''+id+'\']} )">'+
				'<img src="../../gui/img/common/default/folder_delete.png" />Cancel entry'+
			'</button>'+
			'<button class="segButton" onclick="cancelDelete(\''+id+'\'); Modalbox.hide()">'+
				'<img src="../../gui/img/common/default/cancel.png" />Do not cancel'+
			'</button>'
		'</div>';
	Modalbox.show(html, {title: 'Confirm delete', width: 300, overlayOpacity: .4, beforeHide: function() { cancelDelete(id) } });
}

function prepareDelete(id) {
	var node = $('ri_'+id);
	if (node) {
		Effect.Fade(node, { duration: 0.5, to: 0});
	}
}

function cancelDelete(id) {
	var node = $('ri_'+id);
	if (node) {
		Effect.Appear(node, { duration: 0.5});
	}
}

function lateAlert(msg, timeout) {
	$('rlst').reload();
	//setTimeout(function() { alert(msg) }, timeout);
}

function openSSRequest(ss) {
	if (!$('pid').value) return false;
	overlib(
		OLiframeContent('<?= $root_path ?>modules/sponsor/seg_sponsor_lingap_patient_request.php?ss='+encodeURIComponent(ss)+'&name='+encodeURIComponent($('name').value)+'&from=CLOSE_WINDOW',
			750, 400, 'fWizard', 0, 'auto'),
			WIDTH,750, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION,'Process Social Service request',
			MIDX,0, MIDY,0,
			STATUS,'Process Social Service request');
	return false;
}

function openSSPrintout(ss) {
	if (!$('pid').value) return false;
	window.open('../social_service/seg-report-patrequest-for-lingap.php?control_nr='+ss,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
	return false;
}


// tooltips!!!
function tooltip(text) {
	return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}

function createTooltip(element, tip) {
	if ($(element)) {
		var tip = tip || $(element).readAttribute('tooltip');
		if (tip) {
			$(element).observe('mouseover', function() {
					tooltip(tip)
				}).observe('mouseout', function(){
					nd();
				});
		}
		else {
			return false;
		}
	}
}

document.observe('dom:loaded', function() {
	Element.addMethods({
		tooltip: function(element, tip) {
			element = $(element);
			createTooltip(element, tip);
			return element;
		}
	});
	$$('[tooltip]').each(function(element) {
		createTooltip(element);
	});
});

</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

# Setup dyynamic lists
$listgen->setListSettings('MAX_ROWS','10');
$listgen->setListSettings('RELOAD_ONLOAD', FALSE);

$rlst = &$listgen->createList(
	array(
		'LIST_ID' => 'rlst',
		'COLUMN_HEADERS' => array('Process date','SS Control no.','SS Worker', 'Adv Purchase', 'Status','Options'),
		'COLUMN_SORTING' => array(LG_SORT_DESC, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE, LG_SORT_UNSORTABLE),
		'AJAX_FETCHER' => 'populateSSRequests',
		'INITIAL_MESSAGE' => "Please select a patient first...",
		'EMPTY_MESSAGE' => "Patient has no pending Lingap requests from Social Service...",
		'ADD_METHOD' => 'addSSRequest',
		'FETCHER_PARAMS' => array(),
		'COLUMN_WIDTHS' => array("15%", "20%", "*", "15%", "12%", "12%")
	)
);
$smarty->assign('lstRequest',$rlst->getHTML());

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$title = "Lingap :: View requests";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

$smarty->assign('sSelectEnc','<button id="select-enc" class="button" onclick="openPatientSelect();return false"><img '.createComIcon($root_path, 'user.png').' />Select</button>');
$smarty->assign('sPatientEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
$smarty->assign('sPatientID','<input id="pid" name="pid" class="clear" type="text" value="'.$_POST["pid"].'" readonly="readonly" style="color:#006600; font:bold 16px Arial"/>');
$smarty->assign('sPatientName','<input class="segInput" id="name" name="name" type="text" size="30" style="" readonly="readonly" value="'.$_POST["name"].'"/>');
$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Reset" disabled="disabled" onclick="if (confirm(\'Search for another patient?\')) resetControls()"/>');
$smarty->assign('sAddress','<textarea class="segInput" id="address" name="address" style="width:100%" readonly="readonly"></textarea>');
$smarty->assign('sSelectService','<label for="source-select">Select area</label>&nbsp;<select class="input" id="source-select"><option value="'.SegRequest::PHARMACY_REQUEST.'">PHARMACY</option></select>');
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');


$smarty->assign('sContinueButton','<input type="image" src="'.$root_path.'images/btn_submitorder.gif" align="center">');
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1" />
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value= "<?php echo  $lockflag?>">
	<input type="hidden" id="refno" name="refno" value="">
	<input type="hidden" id="refsource" name="refsource" value="">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input type="image" class="segSimulatedLink" src="'.$root_path.'images/btn_submitorder.gif" align="absmiddle" alt="Submit">');
	$smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'images/btn_cancelorder.gif" alt="'.$LDBack2Menu.'" align="absmiddle" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','sponsor/lingap_patient.tpl');
$smarty->display('common/mainframe.tpl');

