<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path."modules/sponsor/ajax/lingap_patient_request.common.php";

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
require_once $root_path.'include/care_api_classes/class_globalconfig.php';
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);

if (!$_GET['from'])
	$breakfile=$root_path."modules/sponsor/seg-sponsor-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/cashier/seg-sponsor-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$thisfile='seg_sponsor_lingap_patient_request.php';


//require_once $root_path.'modules/listgen/listgen.php';
//$listgen = new ListGen($root_path);

//include_once($root_path."include/care_api_classes/sponsor/class_lingap.php");
//$lc = new SegLingap;

require_once($root_path."include/care_api_classes/sponsor/class_lingap_patient.php");
$pc = new SegLingappatient;

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

$smarty->assign('QuickMenu', FALSE);
$smarty->assign('bHideCopyright',TRUE);


if (isset($_POST["submitted"])) {

	$saveok = TRUE;

	require_once $root_path."include/care_api_classes/sponsor/class_lingap_referral.php";
	$referral = new SegLingapReferral();

	$db->StartTrans();

	$data = array(
		'control_nr'=>$_POST['control_nr'],
		'ss_nr'=>$_POST['ss'],
		'encounter_nr'=>$_POST['encounter_nr'],
		'pid'=>$_POST['pid'],
		'name'=>$_POST['name'],
		'entry_date'=>date('Y-m-d', strtotime($_POST['entry_date'])),
		'is_advance'=>$_POST['is_advance'],
		'remarks'=>$_POST['remarks'],
	);
	if ($_POST['id']) {
		$data['id'] = $_POST['id'];
	}
	else {
		$data['id'] = create_guid();
		$do_insert = true;
	}

	$saveok = $referral->save($data);
	if (!$saveok) {
		$errorMessage = "Unable to save Lingap referral info...";
	}

	if ($saveok && $do_insert) {
		require_once $root_path."include/care_api_classes/sponsor/class_request.php";
		require_once $root_path."include/care_api_classes/sponsor/grantors/class_lingap_grantor.php";

		$total = 0;
		$lingapGrantor = new SegLingapGrantor($referral);

		foreach ($_POST["type"] as $i=>$v) {
			$type			= $_POST["type"][$i];
			$refNo 		= $_POST["refNo"][$i];
			$itemNo 	= $_POST["itemNo"][$i];
			//$entryNo = $_POST["entryNo"][$i];
			$itemName = $_POST["itemName"][$i];
			$quantity = $_POST["quantity"][$i];
			$amount 	= $_POST['totalAmount'][$i];
			$total += (float) $_POST['totalAmount'][$i];


			$request = new SegRequest($type, array(
				'refNo'=>$refNo,
				'itemNo'=>$itemNo,
				'entryNo'=>1
			));

			$saveok = $lingapGrantor->grant($request, $amount);
			if ( $saveok ) {
				// successful
			}
			else {
				$errorMessage = "Unable to save Lingap details...";
				break;
			}
		}
	}

	if ($saveok) {
		$smarty->assign('sysInfoMessage','Lingap entry successfully saved!');
		$db->CompleteTrans();
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
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" language="javascript">

eraseCookie('__lingap_ck');

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

function selectAll(checked) {
	var totals=$$('[name="pick"]');
	if (totals) {
		totals.each(
			function(x) {
				if (x.checked != checked)
					x.click();
			}
		);
	}
}


function addRequest(data) {
	var container = $('requests');

	var source = container.select('#request_'+data['type']).first();
	if (!source) {
		wrapper = new Element('div').setStyle({
			marginBottom:'20px'
		});

		source = new Element('table', {
			id: 'request_'+data['type'],
			className: 'segList',
			cellSpacing: 0,
			cellPadding: 0,
			border: 0,
			width: '100%'
		});

		source.update(
			new Element('thead').update(
				new Element( 'tr', {className: 'nav'} ).update(
					new Element('th',{colSpan:7}).update(data['typeName']).setStyle({textAlign:'left'})
				)
			)
		);

		source.insert(
			new Element('thead').update(
				new Element('tr').update(
					new Element('th', { width:'14%' }).update('Date')
				).insert(
					new Element('th', { width:'14%' }).update('Ref no.')
				).insert(
					new Element('th', { width:'*' }).update('Item name')
				).insert(
					new Element('th', { width:'8%' }).update('Quantity')
				).insert(
					new Element('th', { width:'8%' }).update('Total due')
				).insert(
					new Element('th', { width:'10%' }).update('Options')
				)
			)
		);

		source.insert(
			new Element('tbody', { id: 'request_data_'+data['type'] })
		);

		wrapper.update(source);
		container.insert(wrapper);
	}


	var tbody = $('request_data_'+data['type']);
	var rows = tbody.select('tr');
	var id=data['type']+data['refNo']+data['itemNo']+data['entryNo'];

	var flag = data['requestFlag'].toLowerCase();
    
    if ($(id)) removeItem(id);

	if (flag) {
		options = new Element('img',
			{ src:'../../images/flag_'+flag+'.gif', title: flag, align:'absmiddle' }
		);
		data['totalAmount'] = 0;
	}
	else {
		options = new Element('input',
			{ id:'ri_add_'+id, class:'segInput', name:'pick', type:'checkbox', value: id, checked: false }
		).observe( 'click',
			function(event) {
				if (this.checked) {

					var addOk = addLingapItem({
						type:data['type'],
						refNo:data['refNo'],
						itemNo:data['itemNo'],
						entryNo:data['entryNo'],
						itemName:data['itemName'],
						quantity:data['quantity'],
						totalAmount:data['totalAmount']
					});

					if ( addOk ) {
						calculateTotals();
					}
					else {
						alert('Failed to add item');
						this.checked = false;
					}
				}
				else {
					if ( removeItem(id) ) {
						calculateTotals();
					}
					else {
						alert('Failed to remove item');
						this.checked = true;
					}
				}
			}
		);
	}

	tbody.insert(
		new Element('tr',{ classaName:(rows.length%2!=0 ? 'alt' : '') }).update(
			new Element('td', { align:'center' }).update(data['date'])
		).insert(
			new Element('td', { align:'center' }).update(data['refNo'])
		).insert(
			new Element('td', { align:'left' }).update(data['itemName'])
		).insert(
			new Element('td', { align:'right' }).update( formatNumber(parseFloatEx(data['quantity']),4 ) )
		).insert(
			new Element('td', { align:'right', name:'dues' }).update( formatNumber(parseFloatEx(data['totalAmount']),2 ) )
		).insert(
			new Element('td', { align:'center' }).update( options )
		)
	);


}

function clearRequestList(type) {
    var tbody = $('request_data_'+type);
    if (tbody) {
        tbody.innerHTML = "";
        return true;
    }
    return false;    
}


function addPatientRequest(details) {
	list = $('rlst');
	if (list) {
		var dBody=list.select("tbody")[0];
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
					discounted=details["discounted"],
					flag=details["flag"].toLowerCase(),
					disabled=(details["disabled"]=='1');

				var dRows = dBody.select("tr");
				var alt = (dRows.length%2>0) ? 'alt':'';
				var disabledAttrib = disabled ? 'disabled="disabled"' : "";

				var lCookie = readCookie('__lingap_ck'), tempAdded=false;
				if (lCookie) {
					tempAdded = lCookie.indexOf('<'+id+'>') !== -1;
				}
				else {
					lCookie='';
				}

				var options;

				if (flag == 'lingap') {
					lCookie += '<'+id+'>';
					createCookie('__lingap_ck',lCookie,1);
					addLingapItem( { source:source,nr:nr,item:item,name:name,qty:qty,amount:discounted} );
					tempAdded = true;
				}



				if (flag && flag!='lingap') {
					options = new Element('img',
						{ src:'../../images/flag_'+flag+'.gif', title: flag, align:'absmiddle' }
					);
				}
				else {
					options = new Element('input',
						{ id:'ri_add_'+id, class:'segInput', name:'pick', type:'checkbox', value: id, checked: tempAdded }
					).observe( 'click',
						function(event) {
							var lCookie = readCookie('__lingap_ck');
							if (this.checked) {
								//if (discounted > parseFloatEx($('show-balance').getAttribute('value'))) {
								if (false) {
									alert('Item amount exceeds remaining account balance...');
									this.checked = false;
								}
								else {

									if( addLingapItem( { source:source,nr:nr,item:item,name:name,qty:qty,amount:discounted} )) {
										calculateTotals();
										// Set cookie
										if (!lCookie)
											lCookie = '<'+id+'>';
										else {
											if (lCookie.indexOf("<"+id+">") === -1)
												lCookie += '<'+id+'>';
										}
										createCookie('__lingap_ck',lCookie,1);
									}
									else {
										alert('Failed to add item');
										this.checked = false;
									}
								}
							}
							else {
								if ( removeItem(id) ) {
									calculateTotals();
									// unset cookie
									if (lCookie) {
										key = '<'+id+'>';
										pos = lCookie.indexOf(key);
										if (pos !== -1) {
											lCookie = lCookie.split(key).join('');
											createCookie('__lingap_ck',lCookie,1);
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
						new Element('span', { id: 'ri_date_'+id}).update(date).setStyle( {fontWeight:'bold', fontSize:'11px', fontFamily:'Tahoma', color:'#000066'} )
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_source_'+id }).update(source)
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_nr_'+id}).update(nr).setStyle( {fontFamily:'Arial', color:'#660000'} )
					)
				).insert(
					new Element('td', { class:'leftAlign' } ).update(
						new Element('span', { id: 'ri_name_'+id }).update(name).setStyle( { fontFamily:'Arial' } )
					).insert(
						new Element('input', { id:'ri_itemno_'+id, type:'hidden', value:item } )
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_qty_show_'+id }).update(qty)
					).insert(
						new Element('input', { id:'ri_qty_'+id, type:'hidden', value:qty } )
					)
				).insert(
					new Element('td', { class:'rightAlign' } ).update(
						new Element('span', { id: 'ri_discounted_'+id, name:'dues' } ).update( formatNumber(discounted,2) )
					)
				).insert(
					new Element('td', { class:'centerAlign' }).update( options )
				);
				dBody.insert(row);
				calculateTotals();
			}
			else {
				dBody.update('<tr><td colspan="4">No items found on this request...</td></tr>');
			}
			return true;
		}
	}
	return false;
}




function calculateTotals() {
	var dues=$$('[name="dues"]');
	var due=0;
	if (dues) {
		dues.each( function (x) {
			due+=parseFloatEx(x.innerHTML);
		});
	}

	var totals=$$('[name="totalAmount[]"]');
	var total=0;
	if (totals) {
		totals.each( function (x) {
			total+=parseFloatEx(x.value)
		});
	}

	$('show-account').update(formatNumber(due,2) ).setAttribute('value', due);
	$('show-total').update(formatNumber(total,2) ).setAttribute('value', total);
	$('show-balance').update(formatNumber(due-total,2) ).setAttribute('value', due-total);
}


/**
 *
 */
function addLingapItem(details) {
	var container = $('hidden-inputs');
	if (container && typeof(details)=='object') {
		var type=details["type"],
				refNo=details["refNo"],
				itemNo=details["itemNo"],
				entryNo=details["entryNo"],
				itemName=details["itemName"],
				quantity=details["quantity"],
				totalAmount=details["totalAmount"],
				id=type+refNo+itemNo+entryNo,
				disabled=(details["disabled"]=='1');

		if ( $('li_'+id) ) {
			$('li_'+id).remove();
		}

		var row = new Element('fieldset', { id:'li_'+id , style:'display:none' } ).update(
				new Element('input', { id:'li_itemno_'+id, name:'itemNo[]', type:'hidden', value:itemNo } )
			).insert(
				new Element('input', { id:'li_source_'+id, name:'type[]', type:'hidden', value:type } )
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
}

function removeItem(id) {
	var removeObj=$('li_'+id);
	if (removeObj) {
		removeObj.remove();
		return true;
	}
	else
		return false;
}

function search() {
	var filters = new Object();
	if (!$('pid').value) return false;
	filters['PID'] = $('pid').value;
	filters['DATE'] = $('date_request').value;
	startLoading();
    
	xajax.call('populateRequestList', {
		parameters: [filters],
		onSuccess: function() {
			doneLoading();
		}
	});
}

function startLoading() {
	if (!isLoading) {
		isLoading = 1;
		return overlib('<strong>Loading items...</strong><br/><img src="../../images/ajax_bar.gif"/>',
			WIDTH,300, TEXTPADDING,5, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src="" style="display:none"/>',
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

	if (!$$('[name="totalAmount[]"]').length) {
		alert('No items selected...');
		return false;
	}

	return true;
}


// tooltips!!!
function tooltip(text) {
	return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}

function createTooltip(element) {
	if ($(element)) {
		var tip;
		if (tip = $(element).readAttribute('tooltip')) {
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
	$$('[tooltip]').each(function(element) {
		createTooltip(element);
	});
	search();
});
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
//$listgen->printJavascript($root_path);

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$title = 'Lingap referral';

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

# Check if Lingap entry already exists
$sql = "SELECT id,control_nr,entry_date,remarks,is_advance FROM seg_lingap_entries WHERE ss_nr=".$db->qstr($_REQUEST['ss']);
if ($lingap_info=$db->GetRow($sql)) {
	//print_r($lingap_info);
}

# Get social_lingap info
$sql =	"SELECT fn_get_person_name(sl.pid) AS `name`,sl.pid,sl.date_generated\n".
	"FROM seg_social_lingap sl\n".
		"INNER JOIN care_person p ON p.pid=sl.pid\n".
	"WHERE control_nr=".$db->qstr($_REQUEST['ss']);
$ss_info=$db->GetRow($sql);
$request_pid=$ss_info['pid'];
$request_date=$ss_info['date_generated'];
$request_name=$ss_info['name'];

# Controls
$smarty->assign('sControlNo','<input id="control_nr" name="control_nr" class="segInput" autocomplete="off" type="text" value="'.$lingap_info["control_nr"].'" '.($isAdvance ? 'disabled="disabled"' : '').' />');
$smarty->assign('sPatientID','<input id="pid" name="pid" class="segInput" type="text" value="'.$request_pid.'" readonly="readonly"/>');
$smarty->assign('sPatientName','<input class="segInput" id="name" name="name" type="text" size="30" style="font:bold 12px Arial;" readonly="readonly" value="'.strtoupper($ss_info['name']).'"/>');
$smarty->assign('sIsAdvance','<input class="segInput" id="is_advance" name="is_advance" type="checkbox" value="1" '.($isAdvance ? 'checked="checked"' : '').'  onclick="$(\'control_nr\').disabled=this.checked"/>');
$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="30" rows="2" style="">'.$lingap_info['remarks'].'</textarea>');


$time_format = "F j, Y";
if ($lingap_info['entry_date']) {
	$date_show = date($time_format,strtotime($lingap_info['entry_date']));
}
else {
	$date_show = date($time_format,time());
}
@ob_start();
?>
<input type="text" name="entry_date" id="entry_date" class="segInput" value="<?php echo $date_show ?>" style="width:100px" readonly="readonly" onchange="search();" />
<button id="date_entry_trigger" class="segButton" onclick="return false;"><img <?= createComIcon($root_path,'calendar.png','0') ?>>Set</button>
<button id="date_entry_clear" class="segButton" onclick="$('entry_date').value='';return false;"><img <?= createComIcon($root_path,'delete.png','0') ?>>Clear</button>
<script type="text/javascript">
	Calendar.setup ({
		inputField: "entry_date",
		dateFormat: "%B %e, %Y",
		trigger: "date_entry_trigger",
		showTime: false,
		onSelect: function() { search(); this.hide() }
	});
</script>
<?php
$entryDate = @ob_get_contents();
@ob_end_clean();
$smarty->assign('sEntryDate', $entryDate);


$date_set=$request_date;
//$dbtime_format = "Y-m-d";
//$curDate = date($dbtime_format);
$smarty->assign('sRequestFilterDate','
<input class="segInput" name="date_request" id="date_request" type="text" size="8" value="'.$date_set.'" onchange="" readonly="readonly"/>
<!-- <img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_date_request" align="absmiddle" class="disabled"  /> -->
');

# Setup dyynamic lists
//$listgen->setListSettings('MAX_ROWS','30');
//$listgen->setListSettings('RELOAD_ONLOAD', FALSE);

//$rlst = &$listgen->createList(
//	array(
//		'LIST_ID' => 'rlst',
//		'COLUMN_HEADERS' => array(
//			'Date',
//			'Src',
//			'Reference',
//			'Item name',
//			'Quantity',
//			'Total due',
//			'<input type="checkbox" onclick="selectAll(this.checked)" />'),
//		'COLUMN_SORTING' => array(LG_SORT_DESC, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE),
//		'AJAX_FETCHER' => 'populatePatientRequestList',
//		'INITIAL_MESSAGE' => "Please select a patient first...",
//		'ADD_METHOD' => 'addPatientRequest',
//		'FETCHER_PARAMS' => array('pid'=>$request_pid, 'date'=>$request_date),
//		'RELOAD_ONLOAD' => TRUE,
//		'COLUMN_WIDTHS' => array("14%", "8%", "14%", "*", "8%", "12%", "8%")
//	)
//);
//$smarty->assign('lstRequest',$rlst->getHTML());

# Totals
require_once "{$root_path}include/care_api_classes/sponsor/class_lingap_patient.php";
$pc = new SegLingapPatient;
$smarty->assign('sAccountBalance', $pc->getBalance($_GET['pid']));

$smarty->assign('sCoverageTotal',0);


# Save/Cancel buttons

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&from='.$_GET['from'].'" method="POST" id="formData" name="formData" onSubmit="return validate()">');
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
	<input type="hidden" id="entry_id" name="entry_id" value="<?= $lingap_info['entry_id'] ?>">
	<input type="hidden" id="ss" name="ss" value="<?= $_REQUEST['ss'] ?>">
	<input type="hidden" id="refno" name="refno" value="">
	<input type="hidden" id="refsource" name="refsource" value="">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$view_only) {
	$smarty->assign('sContinueButton','<button id="form_save" class="segButton"><img src="'.$root_path.'gui/img/common/default/disk.png" />Save</button>');
	$smarty->assign('sBreakButton','<button id="form_cancel" class="segButton" onclick="window.location=\''.$breakfile.'\'; return false"><img src="'.$root_path.'gui/img/common/default/cancel.png" />Close</button>');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','sponsor/lingap_patient_request.tpl');
$smarty->display('common/mainframe.tpl');

