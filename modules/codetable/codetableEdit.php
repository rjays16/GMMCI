<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require './roots.php';
require $root_path.'include/inc_environment_global.php';
require $root_path.'modules/codetable/ajax/edit.common.php';

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'modules/codetable/dynamicfields/class_dynamicfield.php');
$dF = new DynamicField();

if (!$_REQUEST['object']) {
	die('Object name not specified...');
}
$objectName = $_REQUEST['object'];

// get bean
require_once "{$root_path}modules/codetable/beans/bean_{$objectName}.php";
$beanClass = "{$objectName}bean";
$bean = new $beanClass();
if ($_REQUEST['pk']) {
	$primaryKeys = $_REQUEST['pk'];
	if (!is_array($primaryKeys)) {
		$pKeys = array($primaryKeys);
	}
	$bean->setKeyValues($primaryKeys);
	$bean->fetch();
}

// create a quick reference to the EditView metadata definition
require_once "{$root_path}modules/codetable/metadata/{$objectName}/{$objectName}_editview.php";
$editView =& $Views['Edit'][$objectName];

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
$smarty->assign('sToolbarTitle', $editView['title']);

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

# href for the close button
$smarty->assign('breakfile', $breakfile);

# Window bar title
$smarty->assign('sWindowTitle', $editView['title']);

# Collect javascript code
ob_start()

?>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery.hotkeys.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery.number_format.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/ui/jquery-ui.js"></script>
<style type="text/css" media="screen">
body {
	background-color: transparent !important;
}
.panel div.dataLabel {
	-moz-box-sizing: border-box;
	float: left;
	width: <?= $editView['widths']['label'] ?>%;
	font: bold 12px Arial;
	text-align: left;
	height: auto;
	padding:2px;
}

.panel div.dataField {
	float: left;
	padding:2px;
	-moz-box-sizing: border-box;
	width: <?= $editView['widths']['field'] ?>%;
	text-align: left;
}

.panel div.dataDescription {
	-moz-box-sizing: border-box;
	float: left;
	width: <?= $editView['widths']['description'] ?>%;
	padding:2px;
	text-align: left;
	overflow: hidden;
	font: bold 11px Tahoma;
	color: #585E66;
}
</style>
<script type="text/javascript">
var $J = jQuery.noConflict();

if (typeof(CodeTable)=='undefined') CodeTable = {};
CodeTable = Object.extend({
	elements: [],
	keyValues: <?php
$pkEscaped = array();
foreach ($primaryKeys as $pk) {
	$pkEscaped[] = addslashes($pk);
}
if ($pkEscaped) {
	echo "['".implode("','",$pkEscaped)."']";
}
else {
	echo "null";
}
?>,
	required: [],
	validators: {},
	init : function() {
		shortcut.add('ESC', closeMe,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
	},
	alert: function(title, message) {
		$J('#editview_info').hide();
		$J('#editview_alert_title').text(title);
		$J('#editview_alert_message').text(message);
		$J('#editview_alert').removeAttr('hide').fadeIn('fast')
	},
	info: function(title, message) {
		$J('#editview_alert').hide();
		$J('#editview_info_title').text(title);
		$J('#editview_info_message').text(message);
		$J('#editview_info').removeAttr('hide').fadeIn('fast')
	},
	validate : function() {
		var ok=true;
		CodeTable.elements.each( function(elem) {
			if (elem.element.validator) {
				if (elem.element.validator() === false) {
					elem.element.addClassName('errorInput');
					ok=false;
				}
				else {
					elem.element.removeClassName('errorInput');
				}
			}
		});
		return ok;
	},
	setKeys: function(keys) {
		CodeTable.keyValues = keys;
	},
	getKeys : function() {
		return CodeTable.keyValues;
	},
	getFormData : function() {
		var formData = {};
		CodeTable.elements.each( function(obj) {
			var element = obj.element;
			formData[element.id] = element.value;
		});
		return formData;
	},
	doDelete: function() {
		var comment = prompt('Enter reason for deletion: ');
		if (comment) {
			xajax.call('delete', {
				parameters:['<?= $objectName ?>', CodeTable.getKeys(), comment]
			});
		}
	},
	doClose: function () {
		if (window.parent.closeEditView())	window.parent.closeEditView();
	},
	doSave : function() {
		if (confirm('Save changes?')) {
			if (CodeTable.validate()) {
				xajax.call('save', {
					parameters:['<?= $objectName ?>', CodeTable.getKeys(), CodeTable.getFormData()]
				});
			}
			else {
				CodeTable.alert('Error','Some inputs are incorrect...');
			}
		}
	}
}, CodeTable);

// jQuery onDOMReady initializer
jQuery(function() {
	CodeTable.init;

	$J('#editview_info,#editview_alert').click(function() {
		$J(this).attr('hide','1').fadeOut('fast');
	}).mouseenter(function() {
		if (!$J(this).is('[hide]')) {
			$J(this).fadeTo('fast', 1)
		}
	}).mouseleave(function() {
		if (!$J(this).is('[hide]')) {
			$J(this).fadeTo('fast', 0.7)
		}
	})

	$J(document).bind( 'keydown', 'esc', CodeTable.doClose );
});

// prototype onDOMReady initializer
document.observe("dom:loaded", function() {
});
</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sJavascript = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript', $sJavascript);

# Buffer page output
ob_start();
?>
<div id="editview_info" class="ui-widget" style="display:none; width:80%; cursor:pointer; -moz-user-select:none; opacity:0.7">
	<div class="ui-state-highlight ui-corner-all" style="margin: 10px 0; padding: 0 .5em;">
		<p><span id="editview_info_icon" class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong id="editview_info_title"></strong>
		<span id="editview_info_message"></span></p>
	</div>
</div>
<div id="editview_alert" class="ui-widget" style="display:none; width:80%; cursor:pointer; -moz-user-select:none; opacity:0.7">
	<div class="ui-state-error ui-corner-all" style="margin: 10px 0; padding: 0 .5em;">
		<p><span id="editview_alert_icon" class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<strong id="editview_alert_title">Hey!</strong>
		<span id="editview_alert_message">Sample ui-state-highlight style.</span></p>
	</div>
</div>
<div id="editview_controls" style="margin:2px; text-align:left">
	<button class="button" onclick="CodeTable.doSave();return false"><img src="<?= $root_path ?>gui/img/common/default/disk.png"/>Save</button><button class="button" onclick="CodeTable.doDelete();return false"><img src="<?= $root_path ?>gui/img/common/default/delete.png"/>Delete</button><button class="button" onclick="CodeTable.doClose();return false"><img src="<?= $root_path ?>gui/img/common/default/cancel.png"/>Close</button>
</div>
<div id="editview">
<?php
foreach ($editView['panels'] as $panelId=>$panel) {
?>
	<div id="PANEL_<?= $panelId ?>" class="panel">
		<div class="panelHeader"><span><?= htmlentities($panel['label']) ?></span></div>
		<div class="panelContent">
<?php
	foreach ($panel['items'] as $itemId=>$panelItem) {
?>
			<div id="PANELITEM_<?= $itemId ?>" class="panelItem">
					<div id="LBL_<?= $itemId ?>" class="dataLabel"><label><?= htmlentities($panelItem['label']) ?><?= $panelItem['required'] ? '<span class="required">*</span>' : "" ?></label></div>
				<div id="FLD_<?= $itemId ?>" class="dataField">
<?php
		$fieldData =& $bean->dictionary['fields'][$panelItem['field']];
		if ($bean->fields[$panelItem['field']]) {
			$value = $bean->fields[$panelItem['field']];
		}
		else {
			$value = $panelItem['default'];
		}
		$fieldObj = $dF->getField($fieldData['type'], $value, $fieldData['metaOptions']);

		$options = $panelItem['editOptions'];
		$options['name'] = $itemId;
		$options['id'] = $itemId;
		$options['required'] = $panelItem['required'];
		echo $fieldObj->getEditView( $options );
?>
<script type="text/javascript">
CodeTable.elements.push({
	element : $('<?= $options['id'] ?>'),
	type : '<?= $fieldData['type'] ?>'
});
<?php
		if ($panelItem['required']) {
?>
CodeTable.required.push('<?= $options['id'] ?>');
<?php
		}
?>
</script>
				</div>
				<div id="DESC_<?= $itemId ?>" class="dataDescription"><span><?= htmlentities($panelItem['description']) ?></span></div>
			</div>
<?php
	}
?>
		</div>
	</div>
<?php
}
?>
</div>

<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$dF->appendScripts($smarty);

# Assign the form template to mainframe
$smarty->assign('sMainFrameBlockData',$sTemp);
//$smarty->assign('Javascript',$sTemp);

/**
* show Template
*/
$smarty->display('common/mainframe.tpl');