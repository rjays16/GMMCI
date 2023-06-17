<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$Nr = $_GET['nr'];
$is_refund = ($_GET['refund'] != "no");

$thisfile=basename(__FILE__);

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
$smarty->assign('sToolbarTitle',"$title $LDPharmaDb $LDSearch");

# href for the back button
// $smarty->assign('pbBack',$returnfile);

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"$title $LDPharmaDb $LDSearch");

# Assign Body Onload javascript code
$smarty->assign('sOnLoadJs','');

# Collect javascript code
ob_start()
?>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js" ></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js" ></script>
<script language="javascript" >

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function search() {
	if ($('retlist').list) {
		var $rl = $('retlist').list;
		$rl.params.name = $('search-input').value;
		$rl.refresh();
	}
}

function prepareAdd( id ) {
	while (true) {
		var qty = prompt('Enter the quantity to be returned: ');
		if (qty === null)
			return false;
		if (isNaN( parseFloatEx(qty) )) {

		}
		else {
			window.parent.returnItem( id, qty );
			return true;
		}
	}
}

document.observe("dom:loaded", function() {

	// create ListGen
	ListGen.create('retlist', {
		id:'rlst',
		width: 'auto',
		height: 230,
		rowHeight: 30,
		url: 'ajax/returnables.ajax.php',
		params: {
			enc: '<?= $_REQUEST['enc'] ?>'
		},
		showFooter: false,
		iconsOnly: false,
		effects: true,
		autoLoad: true,
		columnModel:[
			{
				name: 'Name',
				label: 'Item Name',
				width: 250,
				sorting: ListGen.SORTING.asc,
				sortable: true,
				visible: true,
				render: function(data, i, col) {
					var r = {
						id:data[i]['Id'],
						name:data[i]['Name'],
						generic: data[i]['Generic'],
						packing:data[i]['Packing']
					};
					var ret = "<span>"+r.name+'</span>';
					if (r.generic)
						ret += '<br/><span style="color:#000066; font:normal 11px Arial;">'+r.generic+'</span>'
					return ret;
				}
			},
			{
				name: 'Served',
				label: 'Served',
				width: 80,
				align: 'center',
				sorting: ListGen.SORTING.none,
				sortable: true,
				visible: true,
				styles: {
					textAlign: 'center',
					color: '#000080'
				}
			},
			{
				name: 'Returns',
				label: 'Returns',
				width: 80,
				align: 'center',
				sorting: ListGen.SORTING.none,
				sortable: true,
				visible: true,
				styles: {
					textAlign: 'center',
					color: '#000080'
				}
			},
			{
				name: 'Options',
				label: 'Options',
				width: 120,
				align: 'center',
				sorting: ListGen.SORTING.none,
				sortable: false,
				visible: true,
				styles: {textAlign: 'center'},
				render: function(data, i, col) {
					var item=data[i]['Id'];
					var returns=parseInt( data[i]['Returns'] );
					var served=parseInt( data[i]['Served'] );
					if (item && returns<served) {
						return "<button class=\"segButton\" onclick=\"prepareAdd('"+item+"'); return false;\"><img src=\"<?=$root_path?>gui/img/common/default/add.png\"/>Return</button>";
					}
					else {
						return "<button class=\"segButton\" onclick=\"return false;\" disabled=\"disabled\"><img src=\"<?=$root_path?>gui/img/common/default/add.png\"/>Return</button>";
					}
				}
			}
		]
	});

	// capture keypresses
	$('search-input').observe('keypress', function(event) {
		if (event.keyCode == Event.KEY_RETURN) {
			$('search').click();
		}
	});

	document.observe('keypress', function(event) {
		switch(event.keyCode) {
			case Event.KEY_ESC:
				if (window.parent.cClick) window.parent.cClick();
				break;
		}
	});
});
</script>
<style type="text/css" media="screen">
#searchWrapper {
	height: 30px;
	padding: 5px;
}

#searchLabel {
	float: left;
	font: bold 12px Arial;
	margin-top: 4px;
	margin-left: 4px;
}

#searchField {
	float: left;
	margin-top: 2px;
	margin-left: 4px;
}

#search-input {
	width: 200px;
}

#searchButton {
	float: left;
	margin-left: 4px;
}
</style>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
<div style="width: 98%;">
	<div class="panel">
		<div class="panelHeader">Search options</div>
		<div class="panelContent">
			<div id="searchWrapper">
				<div id="searchLabel"><span>Filter item name</span></div>
				<div id="searchField">
					<input id="search-input" class="segInput" type="text" />
				</div>
				<div id="searchButton">
					<button id="search" class="segButton" onclick="search(); return false;">
						<img src="<?= $root_path ?>gui/img/common/default/magnifier.png" />Search
					</button>
				</div>
			</div>
		</div>
	</div>
	<div id="retlist" style="width:100%; margin-top:4px">
	</div>
</div>
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
