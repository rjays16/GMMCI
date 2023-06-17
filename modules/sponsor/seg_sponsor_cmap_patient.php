<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/sponsor/ajax/cmap_patient.common.php");


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

$title="Sponsor grants";
if (!$_GET['from'])
	$breakfile=$root_path."modules/sponsor/seg-sponsor-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/cashier/seg-cashier-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}
$thisfile='seg_sponsor_cmap_patient.php';



// check for valid permissions
require_once $root_path.'include/care_api_classes/class_user.php';
$user = SegUser::getCurrentUser();

$permissionSet = array('_a_1_cmaprequest');
$allow = $user->hasPermission($permissionSet);
if (!$allow)
{
	header('Location:'.$root_path.'main/login.php?'.
		'forward='.urlencode('modules/sponsor/'.$thisfile).
		'&break='.urlencode('modules/sponsor/seg-sponsor-functions.php'));
	exit;
}




//LISTGEN YEHEY
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
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
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>

<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>

<link rel="stylesheet" type="text/css" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/modalbox/modalbox.js"></script>
<link rel="stylesheet" href="<?=$root_path?>css/themes/default/modalbox.css" type="text/css" media="screen" />
<script type="text/javascript">

var $J = jQuery.noConflict();

var glst, grid, buffer,
	isLoading=false

var cClickOverride = cClick;
cClick = function() {
	if (OLloaded && OLgateOK) {
		if (over && OLshowingsticky) {

			var hasSelected = ($('pid').value != '')
			$('name').disabled = hasSelected;
			$('select-enc').disabled = hasSelected;
			$('clear-enc').disabled = !hasSelected;
			$('add-referral').disabled = !hasSelected;
			//$('ext-request').disabled = !hasSelected;
			$('add-request').disabled = !hasSelected;
			$('print-request').disabled = !hasSelected;	//added by cha, 11-11-2010
			$('reg-walkin').disabled = true;	//added by cha, 11-11-2010

			search();
			cClickOverride();
			return false;
		}
	}
	return false;
}

function openRegisterWalkin() {
	overlib(
		OLiframeContent('<?= $root_path ?>modules/registration_admission/walkin_register.php?name='+encodeURIComponent($('name').value)+'&from=CLOSE_WINDOW',
			500, 300, 'fWizard', 0, 'no'),
			WIDTH,500, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION,'Register Walkin',
			MIDX,0, MIDY,0,
			STATUS,'Register Walkin');
			return false;
}

function resetControls() {
	$('name').value="";
	$('name').disabled = false;

	$('pid').value="";
	$('encounter_nr').value="";
	$('sw-class').innerHTML = 'None';
	$('encounter_type_show').innerHTML = 'N/A';
	$('encounter_type').value = '';
	$('select-enc').disabled = false;
	$('clear-enc').disabled = true;
	$('add-referral').disabled = true;
	//$('ext-request').disabled = true;
	$('add-request').disabled = true;
	$('print-request').disabled = true;	//added by cha, 11-11-2010
	$('reg-walkin').disabled = true;	//added by cha, 11-11-2010
	$('bal').value = '0.00';

	//alert('msg:'+rlst.initialMessage)
	if (typeof(flst) == 'object') {
		flst.fetcherParams = {};
		flst.clear({message:flst.initialMessage});
	}
	if (typeof(rlst) == 'object') {
		rlst.fetcherParams = {};
		rlst.clear({message:rlst.initialMessage});
	}
}

function search() {
	var o = new Object();
	if (!$('pid').value) return false;
	o['pid'] = $('pid').value;

	showBalance();
	if (typeof(flst)=='object') {
		flst.fetcherParams = o;
		flst.reload();
	}

	if (typeof(rlst)=='object') {
		rlst.fetcherParams = o;
		rlst.reload();
	}
}


function showBalance()
{
	if (!$('pid').value) return false;
	xajax.call('updateBalance', { parameters: [$('pid').value] } );
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

function validate() {
}



function openPatientSelect() {
	if ($('select-enc').hasClassName('disabled')) return false;
<?php
$var_arr = array(
	"var_pid"=>"pid",
	"var_encounter_nr"=>"encounter_nr",
	"var_name"=>"name",
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
//			FILTER,
//			FADEOUT, 25,
			CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION,'Select registered person',
			MIDX,0, MIDY,0,
			STATUS,'Select registered person');
	return false;
}

function openWindow(url) {
	window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
}

function openPrintout(id) {
	if (!$('pid').value) return false;
	openWindow('<?php echo $root_path ?>modules/sponsor/seg_cmap_report_cmap_patient_record.php?referral='+id+'&pid='+$('pid').value);
}

function deleteReferral(id)
{
	if (confirm("Do you wish to delete this Referral?"))
	{
		xajax.call("deleteReferral", {
			parameters: [id]
		});
	}
}

function openTransfer(mode, id) {     //modified by cha, june 6, 2010
	var tray_headerlabel = "Process referral entry";
	if(mode=="edit")
		tray_headerlabel = "Edit referral entry";
	if (!$('pid').value) return false;
	overlib(
		OLiframeContent('<?= $root_path ?>modules/sponsor/seg-sponsor-cmap-transfer.php?pid='+encodeURIComponent($('pid').value)+'&name='+encodeURIComponent($('name').value)+"&entry_id="+id,
			550, 380, 'fWizard', 0, 'no'),
			WIDTH,550, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
//			FILTER,
			CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION, tray_headerlabel,
			MIDX,0, MIDY,0,
			STATUS, tray_headerlabel);
	return false;
}

function newExternalRequest() {
	if (!$('pid').value) return false;
	overlib(
		OLiframeContent('<?= $root_path ?>modules/sponsor/seg_sponsor_cmap_walkin_request.php?pid='+encodeURIComponent($('pid').value)+'&from=CLOSE_WINDOW',
			750, 400, 'fWizard', 0, 'auto'),
			WIDTH,750, TEXTPADDING,0, BORDER,0,
//			FILTER,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION,'Process CMAP walkin request entry',
			MIDX,0, MIDY,0,
			STATUS,'Process CMAP walkin request entry');
	return false;
}

function newRequest() {
	if (!$('pid').value) return false;
	overlib(
		OLiframeContent('<?= $root_path ?>modules/sponsor/seg_sponsor_cmap_patient_request.php?pid='+encodeURIComponent($('pid').value)+'&from=CLOSE_WINDOW',
			750, 400, 'fWizard', 0, 'auto'),
			WIDTH,750, TEXTPADDING,0, BORDER,0,
//			FILTER,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION,'Process CMAP request entry',
			MIDX,0, MIDY,0,
			STATUS,'Process CMAP request entry');
	return false;
}

//added by cha, 11-11-2010
function printRequest() {
	if (!$('pid').value) return false;
	overlib(
		OLiframeContent('<?= $root_path ?>modules/sponsor/seg_sponsor_cmap_patient_request_printout.php?pid='+encodeURIComponent($('pid').value)+'&from=CLOSE_WINDOW',
			750, 400, 'fWizard', 0, 'auto'),
			WIDTH,750, TEXTPADDING,0, BORDER,0,
//			FILTER,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION,'View CMAP request entry',
			MIDX,0, MIDY,0,
			STATUS,'View CMAP request entry');
	return false;
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

function addFundsTransfer(details) {
	list = $('flst');
	if (list) {
		var dBody=list.select("tbody")[0];
		if (dBody) {
//			if (!details) details = { FLAG: false};
			if (typeof(details)=='object') {
				details = Object.extend({
					nr:'',
					refnr:'',
					date: '',
					account_id:'',
					account_name:'',
					amount:'',
					balance:'',
					encoder:'',
					remarks:'',
					id: '',
					status: ''
				}, details);

				var nr=details["nr"],
					refnr=details["refnr"],
					date=details["date"],
					account_id=details["account_id"],
					account_name=details["account_name"],
					amount=details["amount"],
					balance=details["balance"],
					encoder=details["encoder"],
					remarks=details["remarks"],
					id=details["id"],
					status=details["status"];

				var dRows = dBody.select("tr");
				var alt = (dRows.length%2>0) ? 'alt':'';

				var row = new Element('tr', { class: alt, id:'fi_'+id , style:'height:26px' } ).update(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'fi_date_'+id}).update(date)
					)
				).insert(
					new Element('td', { class:'leftAlign' } ).update(
						new Element('span', { id: 'fi_nr_'+id }).update(nr)
					)
				).insert(
					new Element('td', { class:'leftAlign' } ).update(
						new Element('span', { id: 'fi_refnr_'+id }).update(refnr)
					)
				).insert(
					new Element('td', { class:'leftAlign' } ).update(
						new Element('span', { id: 'fi_account_'+id }).update(account_name)
					)
				).insert(
					new Element('td', { class:'rightAlign' } ).update(
						new Element('span', { id: 'fi_amount_'+id, style:'color:#660000'}).update(formatNumber(amount,2))
					)
				).insert(
					new Element('td', { class:'rightAlign' } ).update(
						new Element('span', { id: 'fi_balance_'+id, style:'color:#660000'}).update(formatNumber(balance,2))
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'fi_encoder_'+id, style:'color:#660000'}).update(encoder)
					)
				).insert(
					new Element('td', { class:'leftAlign' } ).update(
						new Element('span', { id: 'fi_remarks_'+id }).update(remarks)
					)
//        ).insert(
//          new Element('td', { class:'centerAlign' } ).update(
//            new Element('span', { id: 'fi_status_'+id}).update(status)
//          )
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('img',{ id:'fi_print_'+id, class:'link', src:'../../images/cashier_print.gif' }
						).setStyle(
							{ margin: '2px' }
						).observe( 'click',
							function(event) {
								//openTransfer("edit", id);	// added by cha, june 6, 2010
								openPrintout(id);	// added by cha, june 6, 2010
							}
						)
					).insert(
						new Element('img',{ id:'fi_delete_'+id, class:'link', src:'../../images/cashier_delete.gif' }
						).setStyle(
							{ margin: '2px' }
						).observe( 'click',
							function(event) {
								if (parseFloat(amount) != parseFloat(balance))
									alert("Referrals with granted requests cannot be deleted. Please cancel \nall granted requests for this referral first!")
								else
									deleteReferral(id)
							}
						)
					)
					/*.insert(
						new Element('img',{ id:'fi_edit_'+id, class:'disabled', src:'../../images/cashier_edit.gif' }
						).setStyle(
							{ margin: '1px' }
						).observe( 'click',
							function(event) {
								//openTransfer("edit", id);	// added by cha, june 6, 2010
							}
						)
					).insert(
						new Element('img',{ id:'fi_delete_'+id, class:'disabled', src:'../../images/cashier_delete.gif' }
						).setStyle(
							{ margin: '1px' }
						).observe( 'click',
							function(event) {
							}
						)
					) */
				);
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

function addPatientRequest(details) {
	list = $('rlst');
	if (list) {
		var dBody=list.select("tbody")[0];
		if (dBody) {
			if (!details) details = { FLAG: false};
			if (details['FLAG']) {
				var source=details["source"],
					nr=details["refno"],
					entry=details["entry_id"],
					item=details["itemno"],
					id=source+entry,
					date=details["date"],
					name=details["name"],
					qty=details["qty"],
					account=details["account"],
					total=details["total"],
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
						new Element('span', { id: 'ri_name_'+id }).update(name)
					).insert(
						new Element('input', { id:'ri_itemno_'+id, type:'hidden', value:item } )
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_account_'+id}).update( account )
					)
				).insert(
					new Element('td', { class:'rightAlign' } ).update(
						new Element('span', { id: 'ri_discounted_'+id}).update( formatNumber(discounted,2) )
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'ri_status_'+id}).update(status)
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('img',{ id:'ri_print_'+id, class:'disabled', src:'../../images/cashier_view.gif' }
						).setStyle( { margin:'1px' }
						).observe( 'click',
							function(event) {
							}
						).observe( 'mouseover',
							function(event) {
								tooltip('View printout for this grant');
							}
						).observe( 'mouseout',
							function(event) {
								nd();
							}
						)
					).insert(
						new Element('img',{ id:'ri_delete_'+id, class:'segSimulatedLink', src:'../../images/cashier_delete.gif' }
						).setStyle( { margin:'1px' }
						).observe( 'click',
							function(event) {
								confirmDelete(source, entry);
							}
						).observe( 'mouseover',
							function(event) {
								tooltip('Cancel this grant');
							}
						).observe( 'mouseout',
							function(event) {
								nd();
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


function removeItem(id) {
	var destTable, destRows;
	var table = $('order-list');
	var rmvRow=$('row'+id);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		rmvRow.remove();
		if ( $$("[name=items[]]").length )
			appendOrder(table, null);
		reclassRows(table,rndx);
	}
}

function changeTransactionType() {
	clearEncounter();
	refreshDiscount();
}

function clickGrant() {
	if (parseFloatEx($('grant-amount').value)==0) {
		alert('Please enter an amount...');
		return false;
	}
	xajax.call('addGrant', { parameters:[ iSrc, iNr, iCode, iArea, $('grant-account').value, parseFloatEx($('grant-amount').value) ] });
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

// Prototype ondomready handler
document.observe('dom:loaded', function() {
	$$('[tooltip]').each(function(element) {
		createTooltip(element);
	});
	$('name').focus();
});


// jQuery ondomready handler
$J(function($) {
	$( "#name" ).change( function() {
		$('#add-referral').attr('disabled', $('#name').val()=='');
		//$('#ext-request').attr('disabled', $('#name').val()=='');
		$('#add-request').attr('disabled', $('#name').val()=='');
		$('#print-request').attr('disabled', $('#name').val()=='');
		$('#reg-walkin').attr('disabled', $('#name').val()=='');
	});
	$( "#name" ).keyup( function() {
		$('#add-referral').attr('disabled', $('#name').val()=='');
		//$('#ext-request').attr('disabled', $('#name').val()=='');
		$('#add-request').attr('disabled', $('#name').val()=='');
		$('#print-request').attr('disabled', $('#name').val()=='');
		$('#reg-walkin').attr('disabled', $('#name').val()=='');
	});

	$( "#name" ).autocomplete({
		source: "ajax/walkinSearch.php",
		minLength: 3,
		select: function( event, ui ) {
			$( "#pid" ).val( ui.item.value );
			$( "#name" ).val( ui.item.name );
			$("#name").attr('disabled', 'disabled');
			$("#clear-enc").attr('disabled', false);
			$("#select-enc").attr('disabled', true);
			$("#reg-walkin").attr('disabled', true);

			search();
			return false;
		},
		focus: function(event, ui) {
			$( "#name" ).val( ui.item.name );
			return false;
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li></li>" )
			.data( "item.autocomplete", item )
			.append( "<a>" + item.name + "</a>" )
			.appendTo( ul );
	};
});


</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

# Setup dyynamic lists
$listgen->setListSettings('MAX_ROWS','10');
$listgen->setListSettings('RELOAD_ONLOAD', FALSE);

# Funds transfer list
$flst = &$listgen->createList(
	array(
		'LIST_ID' => 'flst',
		'COLUMN_HEADERS' => array('Date','CMAP No.','Referral No.','Account','Allotted','Balance','Encoder', 'Remarks', ''),
		'COLUMN_SORTING' => array(LG_SORT_DESC, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE, LG_SORT_UNSORTABLE),
		'AJAX_FETCHER' => 'populateFundsTransfers',
		'INITIAL_MESSAGE' => "Please select a patient first...",
		'ADD_METHOD' => 'addFundsTransfer',
		'FETCHER_PARAMS' => array(),
		'COLUMN_WIDTHS' => array("12%", "12%", "15%", '12%', '12%', "15%", '*', "5%")
	)
);
$smarty->assign('lstFunds',$flst->getHTML());

# Grants list
$rlst = &$listgen->createList(
	array(
		'LIST_ID' => 'rlst',
		'COLUMN_HEADERS' => array('Date','Source','Reference','Item name','Account','Amount','Status',''),
		'COLUMN_SORTING' => array(LG_SORT_DESC, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE, LG_SORT_UNSORTABLE),
		'AJAX_FETCHER' => 'populatePatientRequestList',
		'INITIAL_MESSAGE' => "Please select a patient first...",
		'ADD_METHOD' => 'addPatientRequest',
		'FETCHER_PARAMS' => array(),
		'COLUMN_WIDTHS' => array("12%", "8%", "12%", "*", "15%", "10%", "10%","7%")
	)
);
$smarty->assign('lstRequest',$rlst->getHTML());

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$title = "CMAP :: Patient account manager";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

$smarty->assign('sSelectEnc','<button id="select-enc" class="segButton" onclick="openPatientSelect(); return false;"><img src="'.$root_path.'gui/img/common/default/user.png" />Search</button>');
$smarty->assign('sPatientEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
$smarty->assign('sPatientID','<input id="pid" name="pid" class="segInput" type="text" size="30" value="'.$_POST["pid"].'" readonly="readonly"/>');
$smarty->assign('sPatientName','<input class="segInput" id="name" name="name" type="text" size="30" autocomplete="off" value="'.$_POST["name"].'"/>');

$smarty->assign('sRunningBalance','<input class="segClearInput" id="bal" name="bal" type="text" size="10" style="margin-left:5px; font:bold 16px Arial; color:#000080; background-color:#fff; border:1px dashed #4e8ccf; text-align:right" readonly="readonly" value=""/>');

$smarty->assign('sClearEnc','<button class="segButton" id="clear-enc" value="Clear" disabled="disabled" onclick="if (confirm(\'Search for another patient?\')) resetControls(); return false;"><img '.createComIcon($root_path, 'flag_red.png', 0).' />Clear</button>');
$smarty->assign('sPatientEncType','<input id="encounter_type" name="encounter_type" type="hidden" value="'.$_POST["encounter_type"].'"/>');
$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');
if ($_POST['encounter_type'])  $smarty->assign('sOrderEncTypeShow',$enc[$_POST['encounter_type']]);
else {
	if ($person['encounter_type'])
		$smarty->assign('sOrderEncTypeShow',$enc[$person['encounter_type']]);
	else  $smarty->assign('sOrderEncTypeShow', 'N/A');
}
$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));

$dbtime_format = "Y-m-d";
//$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format);

$smarty->assign('sRequestFilterDate','
	<input class="segInput" name="date_request" id="date_request" type="text" size="8" value="'.$curDate.'"/>
	<img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_date_request" align="absmiddle" class="segSimulatedLink"  />
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
$curDate.'" style="">');

if ($view_only)
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="entry_date_trigger" align="absmiddle" style="margin-left:2px;opacity:0.2">');
else {
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="entry_date_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
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
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input type="image" class="segSimulatedLink" src="'.$root_path.'images/btn_submitorder.gif" align="absmiddle" alt="Submit">');
	$smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'images/btn_cancelorder.gif" alt="'.$LDBack2Menu.'" align="absmiddle" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','sponsor/cmap_patient.tpl');
$smarty->display('common/mainframe.tpl');

