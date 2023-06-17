<?php

//LISTGEN YEHEY
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/sponsor/class_sponsor.php");
include_once($root_path."include/care_api_classes/sponsor/class_lingap.php");
$sc = new SegSponsor();
$lc = new SegLingap();
global $db;

$Nr = $_GET['nr'];

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Assign Body Onload javascript code
/*
if ($view_only) {     
	$onLoadJS='onload="eraseCookie(\'__ret_ck\');'.($Nr ? 'xajax_populate_items(\''.$Nr.'\',1)' : '').'"';
}
else {
 $onLoadJS='onload="eraseCookie(\'__ret_ck\');'.($Nr ? 'xajax_populate_items(\''.$Nr.'\')' : '').'"';
}
*/
$smarty->assign('sOnLoadJs',$onLoadJS);

if (isset($_POST["submitted"]) && !$_REQUEST['viewonly']) {
	$data = array(
		'control_nr'=>$_POST['control_nr'],
		'encounter_nr'=>$_POST['encounter_nr'],
		'pid'=>$_POST['pid'],
		'name'=>$_POST['name'],
		'address'=>$_POST['address'],
		'entry_date'=>$_POST['entry_date'],
		'remarks'=>$_POST['remarks'],
		'modify_id'=>$_SESSION['sess_temp_userid']
	);
	
	$lc->setDataArray($data);
	$db->StartTrans();
	
	if ($_POST['mode']=='edit') {
		$data["history"]=$lc->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");
		$lc->setDataArray($data);
		$lc->where = "control_nr=".$db->qstr($_POST['control_nr']);
		$saveok=$lc->updateDataFromInternalArray($_POST['control_nr'],FALSE);
	}
	else {
		$data['create_id']=$_SESSION['sess_temp_userid'];
		$data['history']="Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n";
		$lc->setDataArray($data);
		$saveok = $lc->insertDataFromInternalArray();
	}
	
	if ($saveok) {
		$id=$db->Insert_ID();
		# Bulk write entry items
		$bulk = array();
		foreach ($_POST["item"] as $i=>$v) {
			$bulk[] = array(
				$_POST["src"][$i],
				$_POST["ref"][$i],
				$_POST["item"][$i],
				$_POST["service"][$i],
				$_POST['amount'][$i],
			);
		}
		$saveok=$lc->clearEntry($id);
		if ($saveok) $saveok=$lc->addDetails($id, $bulk);
	}
	
	if ($saveok) {
		$smarty->assign('sysInfoMessage','<div style="margin:6px">Lingap entry successfully saved!</div>');
	}
	else {
		$db->FailTrans();
		$errorMsg = $db->ErrorMsg();
		if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
			$smarty->assign('sysErrorMessage','<br><strong>Error:</strong>An entry with the same control number already exists in the database.');
		else {
			if ($errorMsg)
				$smarty->assign('sysErrorMessage',"<br><strong>Error:</strong> $errorMsg");
			else
				$smarty->assign('sysErrorMessage',"<br><strong>Unable to save Lingap entry...</strong>");
			#print_r($order_obj->sql);
		}
	}
	$db->CompleteTrans();
}


# Collect javascript code
ob_start();
	 # Load the javascript code
?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<!-- <script type="text/javascript" src="js/lingap.js?t=<?=time()?>"></script> -->
<script type="text/javascript" language="javascript">
var glst, grid, buffer, 
	isLoading=false
		
function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function resetControls() {
	$('name').value="";
	$('address').value="";
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = false;
	$('sw-class').update('None');
	$('encounter_type_show').update('WALK-IN');
	$('encounter_type').value = '';
	$('select-enc').className = 'link';
	
	//alert('msg:'+rlst.initialMessage)
	if (typeof(rlst) == 'object') {
		rlst.fetcherParams = {};
		rlst.reload();
	}
	new Effect.Appear($('rqsearch'),{ duration:0.5 });
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

function addPatientRequest(details) {
	list = $('rlst');
	if (list) {    
		var dBody=list.select("tbody")[0];
		if (dBody) {
			if (!details) details = { FLAG: false};
			if (details['FLAG']) {
				var source=details["source"],
					nr=details["refno"],
					item=details["itemno"],
					id=source+nr+item,
					date=details["date"],
					name=details["name"],
					qty=details["qty"],
					total=details["total"],
					status=details["status"],
					discounted=details["discounted"],
					status=details["status"],
					disabled=(details["disabled"]=='1');

				var dRows = dBody.select("tr");
				var alt = (dRows.length%2>0) ? 'alt':'';
				var disabledAttrib = disabled ? 'disabled="disabled"' : "";
				
				var row = new Element('tr', { class: alt, id:'ri_'+id , style:'height:26px' } ).update(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_date_'+id}).update(date)
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_source_'+id }).update(source)
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_nr_'+id, style:'color:#660000'}).update(nr)
					)
				).insert(
					new Element('td', { class:'leftAlign' } ).update(
						new Element('span', { id: 'ri_name_'+id }).update(name).setStyle( { font:'bold 11px Tahoma' } )
					).insert(
						new Element('input', { id:'ri_itemno_'+id, type:'hidden', value:item } )
					)
				).insert(
					new Element('td', { class:'rightAlign' } ).update(
						new Element('span', { id: 'ri_discounted_'+id}).update( formatNumber(discounted,2) )
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						(status=='1') ?
							new Element('img', { id: 'ri_status_'+id, src:'../../images/lingap_item.jpg', border:0, title:'Lingap item'}) :
							''
					)
				).insert(
					new Element('td', { class:'centerAlign' }).update(
						new Element('input',{ id:'ri_add_'+id, class:'segButton', type:'button', value:'>', disabled : ($('li_'+id) || status==1 ? true : false) }
						).observe( 'click',
							function(event) {
								if( addLingapItem( { source:source,nr:nr,item:item,name: name,amount: discounted,FLAG: 1} )) {
									this.disabled = true;
									alert('Item successfully added!')
								}
							}
						)
					)
				);
				dBody.insert(row);
			}
			else {
				dBody.update('<tr><td colspan="4">List is currently empty...</td></tr>');
			}
			return true;
		}
	}
	return false;
}

function calculateTotals() {
	var totals=$$('[name="amount[]"]');
	var total=0;
	if (totals) {
		totals.each( function (x) { total+=parseFloatEx(x.value) } );
	}
	$('lingap-totals').update( formatNumber(total,2) );
}

function addLingapItem(details) {
	list = $('llst');
	if (list) {    
		var dBody=list.select("tbody")[0];
		if (dBody) {
			if (!details) details = { FLAG: false};
			if (details['FLAG']) {
				var source=details["source"],
					nr=details["nr"],
					item=details["item"],
					name=details["name"],
					amount=details["amount"],
					id=source+nr+item,
					disabled=(details["disabled"]=='1');

				var dRows = dBody.select('tr[name="li_rows"]');
				var alt = (dRows.length%2>0) ? 'alt':'';
				var disabledAttrib = disabled ? 'disabled="disabled"' : "";

				/* LI Lingap item */
				var row = new Element('tr', { name:'li_rows', class: alt, id:'li_'+id , style:'height:32px' } ).update(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'li_source_span_'+id }).update(source)
					).insert(
						new Element('input', { id:'li_itemno_'+id, name:'item[]', type:'hidden', value:item } )
					).insert(
						new Element('input', { id:'li_source_'+id, name:'src[]', type:'hidden', value:source } )
					).insert(
						new Element('input', { id:'li_nr_'+id, name:'ref[]', type:'hidden', value:nr } )
					).insert(
						new Element('input', { id:'li_amount_'+id, name:'amount[]', type:'hidden', value:amount } )
					).insert(
						new Element('input', { id:'li_name_'+id, name:'service[]', type:'hidden', value:name } )
					)          
				).insert(
					new Element('td', { class:'leftAlign' } ).update(
						new Element('span', { id: 'li_name_span_'+id }).update(name).setStyle( { font:'bold 11px Tahoma' } )
					)
				).insert(
					new Element('td', { class:'rightAlign' } ).update(
						new Element('span', { id: 'li_amount_span_'+id }).update(formatNumber(amount,2)).setStyle( { color:'#000080' } )
					)
				).insert(
					new Element('td', { class:'centerAlign' }).update(
						new Element('img',{ id:'li_del_'+id, class:'link', src:'../../images/cashier_delete.gif', border:0, title:'Remove item' }
						).observe( 'click',
							function(event) {
								
							}
						)
					)
				);
				if (dRows.length)
					dBody.insert(row);
				else
					dBody.update(row);
				
				calculateTotals();
			}
			else {
				dBody.update('<tr><td colspan="8">List is currently empty...</td></tr>');
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

// callback triggered when Patient Search window is closed
function pSearchClose() {
	//$('select-enc').className = ($('pid').value == '') ? 'segSimulatedLink' : 'segDisabledLink';
	if ($('pid').value) $('select-enc').removeClassName('link').addClassName('disabled');
	else $('select-enc').addClassName('link').removeClassName('disabled');
		
	//$('rqsearch').show();
	search();
	cClick();
}

function search() {
	var o = new Object();
	if (!$('pid').value) return false;
	o['pid'] = $('pid').value;
	o['date'] = $('date_request').value;
	if (typeof(rlst)=='object') {
		rlst.fetcherParams = o;
		rlst.reload();
	}
}

function startLoading() {
	if (!isLoading) {
		isLoading = 1;
		return overlib('<strong>Loading items...</strong><br/><img src="../../images/ajax_bar.gif"/>',
			WIDTH,300, TEXTPADDING,5, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			NOCLOSE, TIMEOUT, 10000, OFFDELAY, 10000,
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

function clearErroneousInputs( ) {
	var clearErrors = $$('.errorInput');
	if (clearErrors) clearErrors.each( function(x) { x.removeClassName('errorInput') } );
}

function validate() {
	clearErroneousInputs();
	
	if (!$('control_nr').value) {
		$('control_nr').addClassName('errorInput').focus();
		alert('Enter the Control Number for this entry...');
		return false;
	}
	
	if (!$('pid').value) {
		$('name').addClassName('errorInput').focus();
		alert('Select a patient for this entry...');
		return false;
	}
	
	return true;
}

function openPatientSelect() {
	if ($('select-enc').hasClassName('disabled')) return false;
<?php
$var_arr = array(
	"var_pid"=>"pid",
	"var_encounter_nr"=>"encounter_nr",
	"var_name"=>"name",
	"var_addr"=>"address",
	"var_clear"=>"clear-enc",
	"var_discount"=>"sw-class",
	"var_enctype"=>"encounter_type",
	"var_enctype_show"=>"encounter_type_show",
	"var_include_walkin"=>"0",
	"var_reg_walkin"=>"0"
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
			CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2, 
			CAPTION,'Select registered person',
			MIDX,0, MIDY,0, 
			STATUS,'Select registered person');
	return false;
}
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
		'COLUMN_HEADERS' => array('Date','Src','Reference','Item name','Total due','Status',''),
		'COLUMN_SORTING' => array(LG_SORT_DESC, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE),
		'AJAX_FETCHER' => 'populatePatientRequestList',
		'INITIAL_MESSAGE' => "Please select a patient first...",
		'ADD_METHOD' => 'addPatientRequest',
		'FETCHER_PARAMS' => array(),
		'COLUMN_WIDTHS' => array("10%", "8%", "10%", "*", "12%", "12%", "6%")
	)
);
$smarty->assign('lstRequest',$rlst->getHTML());

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$title = "Lingap :: Edit Lingap entry";

# Title in the title bar 
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);  
$smarty->assign('sControlNo','<input id="control_nr" name="control_nr" class="segInput" type="text" value="'.$_POST["control_nr"].'" />');
$smarty->assign('sSelectEnc','<img id="select-enc" class="link" src="../../images/btn_encounter_small.gif" border="0" onclick="openPatientSelect()" />');
$smarty->assign('sPatientEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');  
$smarty->assign('sPatientID','<input id="pid" name="pid" class="segInput" type="text" value="'.$_POST["pid"].'" readonly="readonly"/>');
$smarty->assign('sPatientName','<input class="segInput" id="name" name="name" type="text" size="30" style="font:bold 12px Arial;" readonly="readonly" value="'.$_POST["name"].'"/>');
$smarty->assign('sAddress','<textarea class="segInput" id="address" name="address" cols="27" rows="2" style="font:bold 12px Arial" readonly="readonly">'.$_POST["address"].'</textarea>');
$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Clear" disabled="disabled" onclick="if (confirm(\'Search for another patient?\')) resetControls()"/>');
$smarty->assign('sPatientEncType','<input id="encounter_type" name="encounter_type" type="hidden" value="'.$_POST["encounter_type"].'"/>');
$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="30" rows="2" style="">'.$_POST['remarks'].'</textarea>');
$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');
if ($_POST['encounter_type'])  $smarty->assign('sOrderEncTypeShow',$enc[$_POST['encounter_type']]);
else {
	if ($person['encounter_type'])
		$smarty->assign('sOrderEncTypeShow',$enc[$person['encounter_type']]);
	else  $smarty->assign('sOrderEncTypeShow', 'WALK-IN');
}
$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));

$dbtime_format = "Y-m-d";
//$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format);

$smarty->assign('sRequestFilterDate','
	<input class="segInput" name="date_request" id="date_request" type="text" size="8" value="'.$curDate.'"/>
	<img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_date_request" align="absmiddle" class="link"  />
	<script type="text/javascript">
		Calendar.setup ({
			inputField : "date_request", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_date_request", singleClick : true, step : 1
		});
	</script>
');

if ($_POST['entry_date'])
	$dEntryDate = strtotime($_POST['entry_date']);
else
	$dEntryDate = time();

$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format,$dEntryDate);
$curDate_show = date($fulltime_format,$dEntryDate);

$smarty->assign('sEntryDate',
'<span id="show_entry_date" class="segInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.
$curDate_show.'</span>
<input class="segInput" name="entry_date" id="entry_date" type="hidden" value="'.
$curDate.'" style="font:bold 12px Arial">');

if ($view_only) 
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="entry_date_trigger" align="absmiddle" style="margin-left:2px;opacity:0.2">');
else {
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="entry_date_trigger" class="link" align="absmiddle" style="margin-left:2px;cursor:pointer">');
	$jsCalScript = "<script type=\"text/javascript\">
	Calendar.setup ({
		displayArea : \"show_entry_date\",
		inputField : \"entry_date\",
		ifFormat : \"%Y-%m-%d %H:%M\",
		daFormat : \"  %B %e, %Y %I:%M%P\",
		showsTime : true,
		button : \"entry_date_trigger\", 
		singleClick : true,
		step : 1
	});
	</script>";
	$smarty->assign('jsCalendarSetup', $jsCalScript);  
}

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
if (!$view_only) {
	$smarty->assign('sContinueButton','<button id="form_save" class="segButton" onclick="save(); return false;"><img src="'.$root_path.'gui/img/common/default/disk.png" />Save</button>');
	$smarty->assign('sBreakButton','<button id="form_cancel" class="segButton" onclick="window.location=\''.$breakfile.'\'; return false"><img src="'.$root_path.'gui/img/common/default/cancel.png" />Cancel</button>');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','sponsor/lingap_entry.tpl');
$smarty->display('common/mainframe.tpl');

