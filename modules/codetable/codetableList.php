<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'modules/codetable/ajax/list.common.php';

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

if (!$_REQUEST['object']) {
	die('Object name not specified...');
} else {
	$objectName = $_REQUEST['object'];
	require_once $root_path.'modules/codetable/beans/bean_'.$objectName.'.php';
	require_once $root_path.'modules/codetable/metadata/'.$objectName.'/'.$objectName.'_listview.php';
	require_once $root_path.'modules/codetable/metadata/'.$objectName.'/'.$objectName.'_editview.php';

	$editView =& $Views['Edit'][$objectName];
	$listView =& $Views['List'][$objectName];
	$beanClass = "{$objectName}bean";
	$bean = new $beanClass();

	require "{$root_path}modules/codetable/dynamicfields/class_dynamicfield.php";
	$dynField = new DynamicField();

}

$thisfile=basename(__FILE__);

if ($_GET['from']=='CLOSE_WINDOW')
	$breakfile = "javascript:if (window.parent.myClick) window.parent.myClick(); else window.parent.cClick();";
else {
	$breakfile = $root_path.'modules/pharmacy/apotheke-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

# Title in the title bar
$smarty->assign('sToolbarTitle', $listView['title'] );

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

# href for the close button
$smarty->assign('breakfile', $breakfile);

# Window bar title
$smarty->assign('sWindowTitle', $listView['title'] );

# Collect javascript code
ob_start()

?>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css" />
<script type="text/javascript" src="<?= $root_path ?>js/listgen/listgen.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/ui/jquery-ui.js"></script>
<style type="text/css" media="screen">
.filterLabel {
	font: bold 12px Arial;
}
.filterField {
}
.filterFiller {
}
</style>
<script language="javascript">

// resolve jQuery conflict
var $J = jQuery.noConflict();

//var cClickTwo = cClick;
//cClick = function() {
//	if (OLloaded && OLgateOK) {
//		if (over && OLshowingsticky) {
//			$('listview').list.refresh();
//			cClickTwo();
//		}
//	}
//	return false;
//}

if (typeof(CodeTable)!='object') CodeTable = {};
CodeTable = $J.extend({
	filters: {},
	alert: function(title, message) {
		$J('#listview_info').hide();
		$J('#listview_alert_title').text(title);
		$J('#listview_alert_message').text(message);
		$J('#listview_alert').fadeIn('fast')
	},

	info: function(title, message) {
		$J('#listview_alert').hide();
		$J('#listview_info_title').text(title);
		$J('#listview_info_message').text(message);
		$J('#listview_info').fadeIn('fast')
	},

	prepareDelete: function(pk) {
		var comment = prompt('Enter reason for deletion: ');
		if (comment) {
			xajax.call('delete', {
				parameters:['<?= $objectName ?>', pk, comment]
			});
		}
	},

	prepareRestore: function(pk) {
		var comment = prompt('Enter reason for restoration: ');
		if (comment) {
			xajax.call('restore', {
				parameters:['<?= $objectName ?>', pk, comment]
			});
		}
	},

	getFilters: function(panel) {
		// get active search tab panel hack
		var filters = {};
		$J('[filter]', panel).each( function(i) {
			var filter = $J(this).attr('filter');
			var param = $J(this).attr('param');
			if (!filters[filter]) filters[filter]={};
			filters[filter][param]=this.value;
		});
		return filters;
	},

	refresh: function(restartSearch) {
		var selected = $J("#listview_search div.ui-tabs-panel:not(.ui-tabs-hide)");
		var filters = this.getFilters(selected);
		$('listview').list.params = {
			object: '<?= $objectName ?>',
			search: selected.attr('id'),
			filters: Object.toJSON(filters)
		}
		$('listview').list.refresh(1);
	}

}, CodeTable);

function tooltip(text) {
	return overlib('<span style="font:bold 11px Tahoma">'+text+'</span>',
		TEXTPADDING,2, BORDER,0,
		VAUTO, WRAP,
		BGCLASS,'olTooltipBG',
		FGCLASS,'olTooltipFG',
		TEXTFONTCLASS,'olTooltipTxt',
		SHADOW, 0
	);
}

function openEditView(pk) {
	var pkQ = {};
	pkQ['object'] = '<?= $_REQUEST['object'] ?>';
	pkQ['from'] = 'CLOSE_WINDOW';
	if (pk) {
		pkQ['pk[]'] = pk;
	}
	var url = '<?= $root_path ?>modules/codetable/codetableEdit.php<?= URL_APPEND ?>&'+Object.toQueryString(pkQ);
	$J('#listview_edit').attr('src',url).dialog('open').width(520);;
}


function closeEditView() {
	$J('#listview_edit').dialog('close');
}

function openHistory(pk) {
	var params = {};
	params['object'] = '<?= $_REQUEST['object'] ?>';
	params['from'] = 'CLOSE_WINDOW';
	if (pk) {
		params['pk[]'] = pk;
	}

	$('listview_history').list.params = params;
	$('listview_history').list.refresh();
	$J('#listview_history').dialog('open');
}

// jQuery onDOMReady initializer
jQuery(function() {
	$J('#listview_info,#listview_alert').click(function() {
		$J(this).fadeOut('fast');
	}).mouseenter(function() {
		$J(this).fadeTo('fast', 1)
	}).mouseleave(function() {
		$J(this).fadeTo('fast', 0.7)
	});

	$J.fx.speeds._default = 600;

	// Audit history dialog
	$J('#listview_history').dialog({
		title: 'Audit logs',
		autoOpen: false,
		width: 540,
		modal: true,
		show: 'fade',
		hide: 'fade',
		resizable: false,
		closeOnEscape: true,
		close: function() {
		}
	});

	// EditView dialog
	$J('#listview_edit').dialog({
		title: '<?= addslashes($editView['title']) ?>',
		autoOpen: false,
		width: 540,
		height: 300,
		modal: true,
		show: 'fade',
		hide: 'fade',
		resizable: false,
		closeOnEscape: true,
		close: function() {
			$('listview').list.refresh();
		},
		overlay: {}
	}).width(520);

	$J('#listview_search').tabs();
});

// prototype onDOMReady initializer
document.observe("dom:loaded", function() {
	//	create dynamic audit history ListGen object
	ListGen.create( $('listview_history'),{
		id: 'hList',
		width: 'auto',
		height: 230,
		url: 'ajax/history.ajax.php<?= URL_APPEND ?>',
		method: 'get',
		params: {},
		autoLoad: false,
		enablePagination: true,
		effects: true,
		onSuccess: function() {
		},
		columnModel: [
			{
				name: 'dt', label: 'Date/Time', width: 80,
				sorting: ListGen.SORTING.desc, sortable: true,
				styles: {
					fontFamily: 'Tahoma',
					fontSize: '11px',
					fontWeight: 'bold',
					textAlign: 'center',
					color: '#800000'
				}
			},
			{
				name: 'act', label: 'Action', width: 60,
				sorting: ListGen.SORTING.none, sortable: true,
				styles: {
					textAlign: 'center'
				},
				render: function(data,i) {
					var action=data[i]['act'];
					switch (data[i]['act'].charAt(0)) {
						case 'D':
							action = '<span style="color:#c00000">'+action+'</span>'
						break;
						case 'R':
							action = '<span style="color:#0000c0">'+action+'</span>'
						break;
						case 'C':
							action = '<span style="color:blue">'+action+'</span>'
						break;
					}
					return action;
				}
			},
			{
				name: 'details', label: 'Audit details', width: 200,
				sorting: ListGen.SORTING.none, sortable: false,
				styles: {
					font: 'bold 12px Arial',
					textAlign: 'left'
				}
			},
			{
				name: 'user', label: 'Initiated by', width: 120,
				sorting: ListGen.SORTING.none, sortable: true,
				styles: {
					textAlign: 'left',
					color: '#000066'
				}
			}
		]
	});

	// create static entry ListGen object
	ListGen.create( $('listview'),{
		id: 'codetableList',
		width: 'auto',
		height: 230,
		params: {object: '<?= $objectName ?>'},
		url: 'ajax/list.ajax.php<?= URL_APPEND ?>',
		method: 'post',
		autoLoad: true,
		enablePagination: true,
		effects: true,
		columnModel: [
<?php
	global $Views;

	$columns = $listView['columns'];
	$column_defs = array();
	foreach ($columns as $key=>$column) {
?>
			{
				name: '<?= addslashes($key) ?>', label: '<?= addslashes($column['label']) ?>', width: <?= $column['width'] ?>,
				sortable: <?= $column['sortable'] ? 'true' : 'false' ?>,
<?php
		if ($column['sortable']) {
			if (strtolower($column['sorting']) == 'asc') {?>
				sorting: ListGen.SORTING.asc,
<?php
			}
			elseif (strtolower($column['sorting']) == 'desc') {?>
				sorting: ListGen.SORTING.desc,
<?php
			}
			else {?>
				sorting: ListGen.SORTING.none,
<?php
			}
		}
?>
				nothing: null
			},
<?php
	}
?>
			{
				name: 'OPTIONS', label: 'Options', width: 70,
				sortable: false,
				render: function(data, i) {
					var args = $A(arguments);
					var data=args[0], i=args[1];
					var edit = new Element('img', {
						src:'../../images/cashier_edit.gif',
						className: 'link',
					}).setStyle({margin:'1px'}
					).observe('click', function(event) {
						openEditView(data[i]['PK']);
						event.stop();
					}).observe('mouseover', function(){
						tooltip('Edit entry')
					}).observe('mouseout', function() {
						nd();
					});

					if (data[i]['_deleted']==='1') {
						var del = new Element('img', {
							src:'../../images/cashier_uncancel.gif',
							className: 'link'
						}).setStyle({margin:'1px'}
						).observe('click', function(event) {
							CodeTable.prepareRestore(data[i]['PK']);
							event.stop();
						}).observe('mouseover', function() {
							tooltip('Restore entry')
						}).observe('mouseout', function() {
							nd();
						});
					}
					else {
						var del = new Element('img', {
							src:'../../images/cashier_delete.gif',
							className: 'link'
						}).setStyle({margin:'1px'}
						).observe('click', function(event) {
							CodeTable.prepareDelete(data[i]['PK']);
							event.stop();
						}).observe('mouseover', function() {
							tooltip('Delete entry')
						}).observe('mouseout', function() {
							nd();
						});
					}

					var audit = new Element('img', {
						src:'../../images/cashier_print.gif',
						className: 'link'
					}).setStyle({margin:'1px'}
					).observe('click', function(event) {
						openHistory(data[i]['PK']);
						event.stop();
					}).observe('mouseover', function() {
						tooltip('View history')
					}).observe('mouseout', function() {
						nd();
					});

					var wrapper = new Element('div').setStyle({textAlign:'center'});
					wrapper.insert(edit)
						.insert(del)
						.insert(audit);
					return wrapper;
				}
			}
		]
	});
});

</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sJavascript = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript', $sJavascript);
$searchFilters = array();

// Parse ListView options
foreach ($listView['search'] as $id=>$search) {
	$filters = array();
	foreach ($search['filters'] as $filterId=>$filter) {
		$fieldDef = $bean->dictionary['fields'][$filter['field']];
		$field =& $dynField->getField( $fieldDef['type'], $bean->emptyValue, $fieldDef['metaOptions'] );

		// for now, id and name fields are equal
		// might trigger some weird behavior later
		$filter['searchOptions']['filter'] = $filterId;
		$filter['searchOptions']['id'] = $filterId;
		$filter['searchOptions']['name'] = $filterId;

		$filterView = $field->getSearchView($filter['searchOptions']);
		$filters[$filterId] = Array(
			'filterId' => $filterId,
			'label' => $filter['label'],
			'field' => $filterView
		);
	}
	$searchFilters[$id] = $filters;
}
# Assign the form template to mainframe
$smarty->left_delimiter = '{';
$smarty->right_delimiter = '}';
$smarty->assign('listview', $listView);
$smarty->assign('filters', $searchFilters);
$sBlock = $smarty->fetch('ListView.tpl');
$smarty->left_delimiter = '{{';
$smarty->right_delimiter = '}}';
$smarty->assign('sMainFrameBlockData',$sBlock);

/**
* show Template
*/
$smarty->display('common/mainframe.tpl');
