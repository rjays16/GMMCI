<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require './roots.php';
require $root_path.'include/inc_environment_global.php';
require $root_path."modules/sponsor/ajax/lingap_walkin_request.common.php";

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
require_once $root_path.'include/inc_front_chain_lang.php';

# Create products object
$GLOBAL_CONFIG=array();

# Create global config object
//require_once $root_path.'include/care_api_classes/class_globalconfig.php';
//$glob_obj=new GlobalConfig($GLOBAL_CONFIG);

# $phpfd = config date format in PHP date() specification
 #$title="Sponsor grants";

if (!$_REQUEST['from'])
	$breakfile=$root_path."modules/sponsor/seg-sponsor-functions.php".URL_APPEND;
else {
	if ($_REQUEST['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/cashier/seg-sponsor-pass.php'.URL_APPEND."&userck=$userck&target=".$_REQUEST['from'];
}

$thisfile='seg_sponsor_lingap_walkin_request.php';

//LISTGEN YEHEY
require_once $root_path.'modules/listgen/listgen.php';
$listgen = new ListGen($root_path);

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

global $db;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$title = 'Lingap referral entry (Walk-in)';

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);
$smarty->assign('sWindowTitle', $title);

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

$smarty->assign('QuickMenu', FALSE);
$smarty->assign('bHideCopyright',TRUE);


if ( isset($_POST["submitted"]) ) {

	require_once $root_path."include/care_api_classes/sponsor/class_lingap_referral.php";
	$referral = new SegLingapReferral();
//	$total = 0;

//	$bulk = array();
//	foreach ($_POST["item"] as $i=>$v) {
//		$bulk[] = array(
//			$_POST["type"][$i],
//			$_POST["ref"][$i],
//			$_POST["item"][$i],
//			$_POST["service"][$i],
//			$_POST["qty"][$i],
//			$_POST['amount'][$i],
//		);
//		$total += (float) $_POST['amount'][$i];
//	}

	$saveok = TRUE;

	require_once $root_path."include/care_api_classes/sponsor/class_lingap_referral.php";
	$referral = new SegLingapReferral();

	$db->StartTrans();

	$data = array(
		'control_nr'=>$_POST['control_nr'],
		'encounter_nr'=>$_POST['encounter_nr'],
		'walkin_pid'=>$_POST['walkin_pid'],
		'name'=>$_POST['name'],
		'is_advance'=>isset($_POST['is_advance']) ? 1:0,
		'entry_date'=>date('Y-m-d', strtotime($_POST['entry_date'])),
		'remarks'=>$_POST['remarks'],
	);

	if ($_REQUEST['entry']) {
		$data['id'] = $_REQUEST['entry'];
	}
	else {
		$data['id'] = create_guid();
		$do_insert = true;
	}

	$saveok = $referral->save($data);
	if (!$saveok) {
		$errorMessage = "Unable to save Lingap referral info...";
	}

	if ($saveok) {
		require_once $root_path."include/care_api_classes/sponsor/class_request.php";
		require_once $root_path."include/care_api_classes/sponsor/grantors/class_lingap_grantor.php";

		$total = 0;
		$lingapGrantor = new SegLingapGrantor($referral);


		$oldItems = Array();

		$types = SegRequest::getRequestTypes();
		$type_keys = array_keys($types);
		foreach ($type_keys as $type) {
			$oldItems[$type] = $lingapGrantor->get($type);
		}

		$newItems = Array();
		foreach ($_POST["source"] as $i=>$v) {
			$type			= $_POST["source"][$i];
			$refNo 		= $_POST["refNo"][$i];
			$itemNo 	= $_POST["itemNo"][$i];
			$entryNo 	= $_POST["entryNo"][$i];
			$itemName = $_POST["itemName"][$i];
			$quantity = $_POST["quantity"][$i];
			$amount 	= $_POST['totalAmount'][$i];
			$total += (float) $_POST['totalAmount'][$i];

			if ( !isset($newItems[$type]) ) {
				$newItems[$type] = array();
			}

			$newItems[$type][] = Array(
				'refNo' 			=> $refNo,
				'itemNo'			=> $itemNo,
				'entryNo'			=> $entryNo,
				'quantity'		=> $quantity,
				'totalAmount' => $amount
			);
		}

		// ungrant old items
		foreach ($oldItems as $type=>$items) {

			if (is_array($items)) {
				foreach ($items as $item) {
					$request = new SegRequest($type, Array(
						'refNo' => $item['refNo'],
						'itemNo' => $item['itemNo'],
						'entryNo' => $item['entryNo']
					));

					$saveok = $lingapGrantor->ungrant( $request );
					if ( $saveok ) {
						// VOID
					}
					else {
						$errorMessage = "Failed to remove Lingap grant...";
						break;
					}
				}
			}

			if (!$saveok) {
				break;
			}
		}


		if ($saveok) {

			// grant new items
			foreach ($newItems as $type=>$items) {

				if (is_array($items)) {
					foreach ($items as $item) {
						$request = new SegRequest($type, Array(
							'refNo' => $item['refNo'],
							'itemNo' => $item['itemNo'],
							'entryNo' => $item['entryNo']
						));

						$saveok = $lingapGrantor->grant( $request, $item['totalAmount'] );
						if ( $saveok ) {
							// VOID
						}
						else {
							$errorMessage = "Failed to save Lingap grant...";
							break;
						}
					}
				}

				if (!$saveok) {
					break;
				}
			}
		}
	}

	if ($saveok) {
		$smarty->assign('sysInfoMessage','Lingap referral successfully saved!');
		//$db->FailTrans();
		$db->CompleteTrans();
		$_REQUEST['entry'] = $data['id'];
		$ReadOnly = true;
	}
	else {
		$db->FailTrans();
		$db->CompleteTrans();
		$smarty->assign('sysErrorMessage',$errorMessage);
	}
}


# Collect javascript code
ob_start();
# Load the javascript code
?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript">
var isLoading=false;

var LingapWalkin = {

	id: null,
	isInitialized: false,
	cookie: 'ck_lingap_walkin',


	initialize: function(id) {
		eraseCookie(this.cookie);
		if (this.isInitialized)
			return false;
		if (id) {
			this.id=id;
		}
		this.isInitialized = true;
		return true;
	},


	search: function() {
		if (!$('pid').value) return false;

		var params;
		if (this.id) {
			params = {
				pid: 	$('pid').value,
				entry: this.id
			};
		}
		else {
		 params = {
			pid: 	$('pid').value,
			date: $('date_request').value
		 };
		}
		if (typeof(rlst)=='object') {
			rlst.fetcherParams = params;
			rlst.reload();
		}
	},



	validate: function() {
		clearErroneousInputs();

		if (!$('is_advance').checked && !$('control_nr').value) {
			$('control_nr').addClassName('errorInput').focus();
			alert('Enter the Control number for this entry...');
			return false;
		}

		if (!$('entry_date').value) {
			$('entry_date').addClassName('errorInput').focus();
			alert('Please select entry date...');
			return false;
		}

		if (!$$('[name="totalAmount[]"]').length) {
			alert('No items selected...');
			return false;
		}

		return true;
	},




	addRequest: function(details) {
		var list = $('rlst');
		if (list) {
			var dBody=list.select("tbody").first();
			if (dBody) {
				if (typeof(details)=='object') {
					var source=details["source"],
						nr=details["refno"],
						item=details["itemno"],
						id=source+nr+item,
						date=details["date"],
						name=details["name"],
						qty=details["qty"],
						total=details["total"],
						status=details["status"],
						flag=details["flag"],
						entryId=details.entryId,
						disabled=<?= $ReadOnly ? 'true' : 'false' ?>;

					var dRows = dBody.select("tr");
					var alt = (dRows.length%2>0) ? 'alt':'';
					var disabledAttrib = disabled ? 'disabled="disabled"' : "";

					var lCookie = readCookie(LingapWalkin.cookie), tempAdded=false;
					if (lCookie) {
						tempAdded = lCookie.indexOf('<'+id+'>') !== -1;
					}

					if (entryId) {
						tempAdded = true;
						ok = LingapWalkin.check({
							source:source,
							refNo:nr,
							itemNo:item,
							itemName:name,
							quantity:qty,
							totalAmount:total
						});
						if (ok) {
							// Set cookie
							if (!lCookie)
								lCookie = '<'+id+'>';
							else {
								if (lCookie.indexOf("<"+id+">") === -1)
									lCookie += '<'+id+'>';
							}
							createCookie(LingapWalkin.cookie,lCookie,1);
						}
						else {
							alert('Failed to add item');
							this.checked = false;
						}
						LingapWalkin.calculateTotals();
					}

					var options;
					if (flag && !entryId) {
						options = new Element('img',
							{ src:'../../images/flag_'+flag.toLowerCase()+'.gif', title: flag.toUpperCase(), align:'absmiddle' }
						);
					}
					else {
						options = new Element('input',
							{ id:'ri_add_'+id, class:'segInput', type:'checkbox', value: id, checked: tempAdded, disabled:disabled }
						).observe( 'click',
							function(event) {
								var lCookie = readCookie(LingapWalkin.cookie);

								if (this.checked) {
									ok = LingapWalkin.check({
										source:source,
										refNo:nr,
										itemNo:item,
										itemName:name,
										quantity:qty,
										totalAmount:total
									});
									if (ok) {
										LingapWalkin.calculateTotals();
										// Set cookie
										if (!lCookie)
											lCookie = '<'+id+'>';
										else {
											if (lCookie.indexOf("<"+id+">") === -1)
												lCookie += '<'+id+'>';
										}
										createCookie(LingapWalkin.cookie,lCookie,1);
									}
									else {
										alert('Failed to add item');
										this.checked = false;
									}
								}
								else {
									if (LingapWalkin.uncheck(id) ) {
										LingapWalkin.calculateTotals();
										// unset cookie
										if (lCookie) {
											key = '<'+id+'>';
											pos = lCookie.indexOf(key);
											if (pos !== -1) {
												lCookie = lCookie.split(key).join('');
												createCookie(LingapWalkin.cookie,lCookie,1);
											}
										}
									}
									else {
										alert('Failed to remove item');
										this.checked = true;
									}
								}
							}
						);

					}

					var row = new Element('tr', { class: alt, id:'ri_'+id , style:'height:26px' } ).update(
						new Element('td', { class:'centerAlign' } ).update(
							new Element('span', { id: 'ri_date_'+id}).update(date)
						)
	//				).insert(
	//					new Element('td', { class:'centerAlign' } ).update(
	//						new Element('span', { id: 'ri_source_'+id }).update(source)
	//					)
					).insert(
						new Element('td', { class:'centerAlign' } ).update(
							new Element('span', { id: 'ri_nr_'+id}).update(nr)
						)
					).insert(
						new Element('td', { class:'leftAlign' } ).update(
							new Element('span', { id: 'ri_name_'+id }).update(name)
						).insert(
							new Element('input', { id:'ri_itemno_'+id, type:'hidden', value:item } )
						)
					).insert(
						new Element('td', { class:'centerAlign' } ).update(
							new Element('span', { id: 'ri_qty_show_'+id }).update(formatNumber(qty,4))
						).insert(
							new Element('input', { id:'ri_qty_'+id, type:'hidden', value:qty } )
						)
					).insert(
						new Element('td', { class:'rightAlign' } ).update(
							new Element('span', { id: 'ri_total_'+id}).update( formatNumber(total,2) )
						)
					).insert(
						new Element('td', { class:'centerAlign' }).update( options )
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
	},



	check: function (details) {
		var container = $('hidden-inputs');
		if (container && typeof(details)=='object') {
			var source=details["source"],
					refNo=details["refNo"],
					itemNo=details["itemNo"],
					itemName=details["itemName"],
					quantity=details["quantity"],
					totalAmount=details["totalAmount"],
					id=source+refNo+itemNo;

			if ( $('li_'+id) ) {
				$('li_'+id).remove();
			}

			var row = new Element('fieldset', { 	id:'li_'+id , style:'display:none' } ).update(
					new Element('input', { id:'li_itemno_'+id, name:'itemNo[]', type:'hidden', value:itemNo } )
				).insert(
					new Element('input', { id:'li_source_'+id, name:'source[]', type:'hidden', value:source } )
				).insert(
					new Element('input', { id:'li_nr_'+id, name:'refNo[]', type:'hidden', value:refNo } )
				).insert(
					new Element('input', { id:'li_qty_'+id, name:'quantity[]', type:'hidden', value:quantity } )
				).insert(
					new Element('input', { id:'li_amount_'+id, name:'totalAmount[]', type:'hidden', value:totalAmount } )
				).insert(
					new Element('input', { id:'li_name_'+id, name:'itemName[]', type:'hidden', value:itemName } )
				);

			container.insert(row);
			return true;
		}
		else
			return false;
	},


	uncheck: function(id) {
		var removeObj=$('li_'+id);
		if (removeObj) {
			removeObj.remove();
			return true;
		}
		else
			return false;
	},


	calculateTotals: function() {
		var totals=$$('[name="totalAmount[]"]');
		var total=0;
		if (totals) {
			totals.each( function (x) { total+=parseFloatEx(x.value) } );
		}
		$('show-total').update(formatNumber(total,2) ).setAttribute('value', total);
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

function reclassRows(list,startIndex) {
	list = $(list);
	if (list) {
		var dBody=list.select("tbody").first();
		if (dBody) {
			var dRows = dBody.select("tr");
			if (dRows) {
				for (i=startIndex;i<dRows.length;i++) {
					dRows[i].className = (i%2 === 0 ? '' : 'alt');
				}
			}
		}
	}
}


LingapWalkin.initialize(<?php echo "'".addslashes($_REQUEST['entry'])."'" ?>);
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

# Setup dyynamic lists
$listgen->setListSettings('MAX_ROWS','25');
$listgen->setListSettings('RELOAD_ONLOAD', FALSE);

$options =
	array(
		'LIST_ID' => 'rlst',
		'COLUMN_HEADERS' => array('Date','Reference','Item name','Quantity','Total due','Options'),
		'COLUMN_SORTING' => array(LG_SORT_DESC, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE, LG_SORT_UNSORTABLE, LG_SORT_UNSORTABLE),
		'AJAX_FETCHER' => 'populateWalkinRequestList',
		'INITIAL_MESSAGE' => "Please select a patient first...",
		'ADD_METHOD' => 'LingapWalkin.addRequest',
		'RELOAD_ONLOAD' => TRUE,
		'COLUMN_WIDTHS' => array("15%", "15%", "*", "10%", "15%", "10%")
	);
if ($_REQUEST['entry']) {
	$options['FETCHER_PARAMS'] = array('pid'=>$_REQUEST['pid'], 'entry'=>$_REQUEST['entry']);
}
else {
	$options['FETCHER_PARAMS'] = array('pid'=>$_REQUEST['pid'], 'date'=>date('Y-m-d'));
}
$rlst = &$listgen->createList($options);
$smarty->assign('lstRequest',$rlst->getHTML());

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Check if Lingap walkin entry exists
if ($_REQUEST['entry']) {
	$sql = "SELECT id,walkin_pid,control_nr,entry_date,remarks,is_advance FROM seg_lingap_entries WHERE id=".$db->qstr($_REQUEST['entry']);
	if ($lingap_info=$db->GetRow($sql)) {
		$_REQUEST['walkin_pid']=$lingap_info['walkin_pid'];
		$isAdvance = $lingap_info['is_advance']==='1';
	}
}

# Get person info
if (!$_REQUEST['walkin_pid'] && $_REQUEST['pid']) {
	$_REQUEST['walkin_pid'] = $_REQUEST['pid'];
}

$sql = "SELECT fn_get_walkin_name(".$db->qstr($_REQUEST['walkin_pid']).") AS `name`";
$p_info=$db->GetRow($sql);

# Controls
$smarty->assign('sControlNo','<input id="control_nr" name="control_nr" class="segInput" type="text" autocomplete="off" value="'.$lingap_info["control_nr"].'" '.($isAdvance||$ReadOnly ? 'disabled="disabled"' : '').' />');
$smarty->assign('sPatientID','<input id="walkin_pid" name="walkin_pid" class="segInput" type="text" value="'.$_REQUEST['walkin_pid'].'" readonly="readonly" '.($ReadOnly ? 'disabled="disabled"': '').'/>');
$smarty->assign('sPatientName','<input class="segInput" id="name" name="name" type="text" size="30" style="" readonly="readonly" value="'.strtoupper($p_info['name']).'" '.($ReadOnly ? 'disabled="disabled"': '').' />');
$smarty->assign('sIsAdvance','<input class="segInput" id="is_advance" name="is_advance" type="checkbox" value="1" '.($isAdvance ? 'checked="checked"' : '').'  onclick="$(\'control_nr\').disabled=this.checked" '.($ReadOnly ? 'disabled="disabled"': '').'/>');
$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="30" rows="2" '.($ReadOnly ? 'disabled="disabled"': '').'>'.$lingap_info['remarks'].'</textarea>');




$time_format = "F j, Y";
if ($lingap_info['entry_date']) {
	$date_show = date($time_format,strtotime($lingap_info['entry_date']));
}
else {
	$date_show = date($time_format,time());
}
@ob_start();
?>
<input type="text" name="entry_date" id="entry_date" class="segInput" value="<?php echo $date_show ?>" style="width:100px" readonly="readonly" onchange="LingapWalkin.search();" <?= ($ReadOnly ? 'disabled="disabled"': '') ?>/>
<button id="entry_date_trigger" class="segButton" onclick="return false;" <?= ($ReadOnly ? 'disabled="disabled"': '') ?>><img <?= createComIcon($root_path,'calendar.png','0') ?>>Set</button>
<button id="entry_date_clear" class="segButton" onclick="$('entry_date').value='';return false;" <?= ($ReadOnly ? 'disabled="disabled"': '') ?>><img <?= createComIcon($root_path,'delete.png','0') ?>>Clear</button>
<script type="text/javascript">
	Calendar.setup ({
		inputField: "entry_date",
		dateFormat: "%B %e, %Y",
		trigger: "entry_date_trigger",
		showTime: false,
		onSelect: function() { this.hide() }
	});
</script>
<?php
$entryDate = @ob_get_contents();
@ob_end_clean();
$smarty->assign('sEntryDate', $entryDate);




//if (!$_REQUEST['entry']) {
//	$dbtime_format = "Y-m-d";
//	$curDate = date($dbtime_format);
//	$smarty->assign('sRequestFilterDate','
//<input class="segInput" name="date_request" id="date_request" type="text" size="8" value="'.$curDate.'" onchange="search()"/>
//<img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_date_request" align="absmiddle" class="link"  />
//<script type="text/javascript">
//	Calendar.setup ({
//		inputField : "date_request", ifFormat : "%Y-%m-%d", showsTime : false, button : "tg_date_request", singleClick : true, step : 1
//	});
//</script>
//');
//}
//else {
//	$smarty->assign('sRequestFilterDate','
//	<input class="segInput" name="date_request" id="date_request" type="text" size="8" value="" disabled="disabled"/>
//	<img src="'. $root_path .'gui/img/common/default/show-calendar.gif" align="absmiddle" class="disabled"  />
//');
//}








$time_format = "F j, Y";
$date_show = date($time_format,time());
@ob_start();
?>
<input type="text" name="date_request" id="date_request" class="segInput" value="<?php echo $date_show; ?>" style="width:100px" readonly="readonly" <?= ($ReadOnly ? 'disabled="disabled"': '') ?> />
<button id="date_request_trigger" class="segButton" onclick="return false;" <?= ($ReadOnly ? 'disabled="disabled"': '') ?>><img <?= createComIcon($root_path,'calendar.png','0') ?>>Set</button>
<button id="date_request_clear" class="segButton" onclick="$('date_request').value=''; LingapWalkin.search(); return false;" <?= ($ReadOnly ? 'disabled="disabled"': '') ?>><img <?= createComIcon($root_path,'delete.png','0') ?>>Clear</button>
<script type="text/javascript">
	Calendar.setup ({
		inputField: "date_request",
		dateFormat: "%B %e, %Y",
		trigger: "date_request_trigger",
		showTime: false,
		onSelect: function() {
			LingapWalkin.search();
			this.hide();
		}
	});
</script>
<?php
$dateFilter = @ob_get_contents();
@ob_end_clean();
$smarty->assign('sRequestFilterDate', $dateFilter);














//if ($lingap_info['entry_date'])
//	$dEntryDate = strtotime($lingap_info['entry_date']);
//else
//	$dEntryDate = time();

//$dbtime_format = "Y-m-d H:i";
//$fulltime_format = "F j, Y g:ia";
//$curDate = date($dbtime_format,$dEntryDate);
//$curDate_show = date($fulltime_format,$dEntryDate);

//$smarty->assign('sEntryDate',
//'<span id="show_entry_date" class="segInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.
//$curDate_show.'</span>
//<input class="segInput" name="entry_date" id="entry_date" type="hidden" value="'.
//$curDate.'" style="font:bold 12px Arial">');

//if ($view_only)
//	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="entry_date_trigger" align="absmiddle" style="margin-left:2px;opacity:0.2">');
//else {
//	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="entry_date_trigger" class="link" align="absmiddle" style="margin-left:2px;cursor:pointer">');
//	$jsCalScript = "<script type=\"text/javascript\">
//	Calendar.setup ({
//		displayArea : \"show_entry_date\",
//		inputField : \"entry_date\",
//		ifFormat : \"%Y-%m-%d %H:%M\",
//		daFormat : \"  %B %e, %Y %I:%M%P\",
//		showsTime : true,
//		button : \"entry_date_trigger\",
//		singleClick : true,
//		step : 1
//	});
//	</script>";
//	$smarty->assign('jsCalendarSetup', $jsCalScript);
//}

# Totals
require_once "{$root_path}include/care_api_classes/sponsor/class_lingap_patient.php";
$pc = new SegLingapPatient;
$smarty->assign('sAccountBalance', $pc->getBalance($_REQUEST['pid']));

$smarty->assign('sCoverageTotal',0);


# Save/Cancel buttons
//$smarty->assign('sContinueButton','<input type="image" src="'.$root_path.'images/btn_submitorder.gif" align="center">');
//$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&from='.$_REQUEST['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return LingapWalkin.validate()">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1" />
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$_COOKIE[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value= "<?php echo  $lockflag?>">
	<input type="hidden" id="pid" name="pid" value="<?= $_REQUEST['pid'] ?>">
	<input type="hidden" id="entry" name="entry" value="<?= $_REQUEST['entry'] ?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<button class="button"'.($ReadOnly ? 'disabled="disabled"': '').'><img '.createComIcon($root_path, 'disk.png').'/>Save</button>');
	$smarty->assign('sBreakButton','<button class="button" onclick="window.location=\''.$breakfile.'\'; return false;"><img '.createComIcon($root_path, 'cancel.png').' />Close</button>');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','sponsor/lingap_walkin_request.tpl');
$smarty->display('common/mainframe.tpl');

