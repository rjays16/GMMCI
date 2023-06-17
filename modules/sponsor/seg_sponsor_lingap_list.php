<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/sponsor/class_request.php';
require_once $root_path.'modules/sponsor/ajax/lingap_list.common.php';

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
0* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/

define('NO_CHAIN',1);
require_once $root_path.'include/inc_front_chain_lang.php';

# Create products object
$GLOBAL_CONFIG=array();

if (!$_GET['from'])
	$breakfile=$root_path."modules/sponsor/seg-sponsor-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile=$root_path."modules/sponsor/seg-sponsor-functions.php".URL_APPEND;
}

$thisfile=basename(__FILE__);


// check for valid permissions
require_once $root_path.'include/care_api_classes/class_user.php';
$user = SegUser::getCurrentUser();

$permissionSet = array('_a_1_cmaplist');
$allow = $user->hasPermission($permissionSet);
if (!$allow)
{
	header('Location:'.$root_path.'main/login.php?'.
		'forward='.urlencode('modules/sponsor/'.$thisfile).
		'&break='.urlencode('modules/sponsor/seg-sponsor-functions.php'));
	exit;
}

# Start Smarty templating here
/**
* LOAD Smarty
*/
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

global $db;

# href for the close button
$smarty->assign('breakfile',$breakfile);
$title = "Lingap :: List of Lingap referrals";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);


# Collect javascript code
ob_start();
	 # Load the javascript code
?>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>

<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>

<script type="text/javascript">

$J = jQuery.noConflict();

function refreshPage() {
	//window.location.reload();
	$('show_requests').list.refresh();
}

function initialize() {

	$J("#patient_options_panel :input, #cost_center_options_panel :input").each(function(index, obj){
		obj.disabled = true;
	});

	ListGen.create( $('show_requests'), {
		id: 'requests',
		url: '<?=$root_path?>modules/sponsor/ajax/lingap_list.ajax.php',
		params: { 'type':'all'},
		width: 'auto',
		height: 'auto',
		autoLoad: true,
		effects: true,
		rowHeight: 32,
		columnModel: [
			{
				name: 'date',
				label: 'Date',
				width: 80,
				sorting: ListGen.SORTING.desc,
				sortable: true,
				styles:{
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'normal',
					textAlign: 'center'
				}
			},
			{
				name: 'controlNo',
				label: 'Control No.',
				width: 100,
				sorting: ListGen.SORTING.none,
				sortable: true,
				styles:{
				},
				render: function(data, idx){
					var row = data[idx];
					var returnHtml = '<span>'+row.controlNo+'</span>';
					if (row.ssNo)
					{
						returnHtml += '<br/><span style="font:normal 11px Tahoma; color:#000066">'+row.ssNo+'</span>';
					}
					return returnHtml;
				}
			},
			{
				name: 'fullName',
				label: 'Full Name',
				width: 140,
				sorting: ListGen.SORTING.none,
				sortable: true,
				styles:{
					fontSize: '11px'
				}
			},
			{
				name: 'costCenter',
				label: 'Cost center',
				width: 100,
				sortable: false,
				styles:{
					font:'normal 11px Tahoma',
					color: '#660000'
				}
			},
			{
				name: 'itemName',
				label: 'Service/Item name',
				width: 170,
				sortable: false,
				styles:{
					fontSize: '12px'
				},
				render: function(data, idx){
					var row = data[idx];
					return '<span>'+row.itemName+'</span><br/><span style="font-size:11px; color:#000066">'+row.itemGroup+'</span>';
				}
			},
			{
				name: 'amount',
				label: 'Amount',
				width: 70,
				sortable: false,
				styles:{
					color: '#006',
					fontWeight: 'bold',
					textAlign: 'right'
				}
			},
			{
				name: 'options',
				label: 'Options',
				width: 100,
				sortable: false,
				styles: {
					textAlign: 'center'
				},
				render: function(data, idx){
					var row = data[idx];
					if (row.served === 0)
						return '<button class="button" onclick="cancelEntry(\''+row.id+'\', '+row.source+', \''+row.refNo+'\', \''+row.itemCode+'\'); return false;"><img <?php echo createComIcon($root_path, 'delete.png') ?>/>Cancel</button>'
					else
						return '<img src="../../images/flag_served.gif" align="absmiddle" />';
				}
			}
		]
	});
}

function startSearch()
{
	$('show_requests').list.params = {
		type: $('cost_center_options').checked ? $('select_cost_center').value : 'all',
		name: $('patient_options').checked ? $('name').value : ''
	};
	$('show_requests').list.refresh();
}


function cancelEntry(id, source, ref, code)
{
	//alert('Deleting id ['+id+'] from source ['+source+'], ref:['+ref+'] item:['+code+']');
	if (!confirm('Do you wish to cancel this entry?')) return false;

	xajax.call('cancelEntry', {
		parameters: [{
			id: id,
			source: source,
			refNo: ref,
			itemCode: code
		}]
	})
}


function changePatientOptions(val)
{
}

document.observe('dom:loaded', initialize);
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format);
$curDate_show = date($fulltime_format);

$smarty->assign('patientCheck', '<input type="checkbox" id="patient_options" name="patient_options" onclick="$J(\'#patient_options_panel :input\').each(function(index, obj) { obj.disabled = !this.checked }.bind(this))" />');
$smarty->assign('statusCheck', '<input type="checkbox" id="cost_center_options" name="cost_center_options" onclick="$J(\'#cost_center_options_panel :input\').each(function(index, obj) { obj.disabled = !this.checked }.bind(this))" />');
$smarty->assign('patientName', '<input type="text" size="40" id="name" class="input" value=""  onfocus="this.select()" />');

$costCenterOptions = SegRequest::getRequestTypes();
$options = '<option value="all">--Show all--</option>'."\n";
foreach ($costCenterOptions as $j=>$option)
{
	$options .= '<option value="'.$j.'">'.htmlentities($option).'</option>'."\n";
}
$smarty->assign('costCenterOptions', '<select class="segInput" id="select_cost_center" name="select_cost_center" >'."\n".
	$options.
'</select>');

$smarty->assign('sFormStart','<form enctype="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform">');
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

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);


# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','sponsor/lingap_list.tpl');
$smarty->display('common/mainframe.tpl');