<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

global $db;


define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');


$GLOBAL_CONFIG=array();
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);


if (!$_SESSION['sess_temp_userid'])
{
	die('You are not allowed access to this feature...');
}

$thisfile=basename(__FILE__);


# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# href for the back button
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

# Window bar title
$smarty->assign('sWindowTitle',"Dashboard");

// get all Dashboard objects from database
$tabs = array();
$active = null;

$query = "SELECT id,title,icon FROM seg_dashboards WHERE NOT is_deleted AND owner=".$db->qstr($_SESSION['sess_temp_userid'])." ORDER BY position";
$rs = $db->Execute($query);
if ($rs !== false)
{

	$rows = $rs->GetRows();
	foreach ($rows as $row)
	{
		if ($row['id'] == $_REQUEST['tab'])
		{
			$active = $row['id'];
		}

		$tabs[$row['id']] = Array(
			'id' => $row['id'],
			'title' => $row['title'],
			'icon'	=> $row['icon'],
			'url' => $thisfile."?tab=".$row['id'],
			'isActive' => ($row['id'] == $active)
		);
	}

	if (!$active && $tabs)
	{
		$first = reset($tabs);
		$active = $first['id'];
		$tabs[$active]['isActive'] = true;
	}
}


require_once $root_path.'include/care_api_classes/dashboard/Dashboard.php';
if ($active)
{
	$dashboard = Dashboard::loadDashboard($active);
}
else
{
	$dashboard = new Dashboard;
	$dashboard->setOwner($_SESSION['sess_temp_userid']);
	$dashboard->setColumnCount(3);
	$dashboard->setIcon('home');
	$dashboard->setPosition(0);
	$dashboard->setTitle('Home page');
	$dashboard->save();

	$active = $dashboard->getId();
	$tabs[] = Array(
		'id' => $dashboard->getId(),
		'title' => $dashboard->getTitle(),
		'icon' => $dashboard->getIcon(),
		'url' => $thisfile."?tab=".$dashboard->getId(),
		'isActive' => true
	);

}


$_SESSION['activeDashboard'] = $active;
$smarty->assign('tabs', $tabs);

$columns = Array();
$widths = $dashboard->getColumnWidths();
for ($i=0; $i<$dashboard->getColumnCount(); $i++) {

	$columns[$i] = Array(
		'width' => $widths[$i].'%',
		'dashlets' => $dashlets
	);

}

$setup = Array();
$setup['columns'] = $columns;

$smarty->assign('dashboard', $setup);





# Collect javascript code
ob_start()

?>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $root_path ?>js/listgen/css/blackice/blackice.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jquery/themes/seg-ui/jquery-ui-1.8.5.custom.css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.blockUI.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="css/theme.css" />
<script type="text/javascript" src="js/Dashboard.js"></script>
<script type="text/javascript">
(function($) {
	$.fn.ellipsis = function(enableUpdating){
		var s = document.documentElement.style;
		if (!('textOverflow' in s || 'OTextOverflow' in s)) {
			return this.each(function(){
				var el = $(this);
				if(el.css("overflow") == "hidden"){
					var originalText = el.html();
					var w = el.width();

					var t = $(this.cloneNode(true)).css('position', 'absolute').hide()
					.css('width', 'auto').css('overflow', 'visible');
					el.after(t);

					var text = originalText;
					while(text.length > 0 && t.width() > el.width()){
						text = text.substr(0, text.length - 1);
						t.html(text + "...");
					}
					el.html(t.html());

					t.remove();

					if(enableUpdating == true){
						var oldW = el.width();
						setInterval(function(){
							if(el.width() != oldW){
								oldW = el.width();
								el.html(originalText);
								el.ellipsis();
							}
						}, 200);
					}
				}
			});
		} else return this;
	};
})(jQuery);


$J = jQuery.noConflict();
$J.ajaxSetup({
	contentType: 'application/x-www-form-urlencoded; charset=iso-8859-1',
	scriptCharset: 'iso-8859-1'
});

$J(function() {

	Dashboard.initialize('<?php echo $dashboard->getId() ?>');

	$J("#dashboard-settings").click(function(){
		Dashboard.dialog.open({
			title: 'Dashboard layout settings',
			parameters: {
				ui:'settings'
			}
		});
		return false;
	});

	$J("#dashboard-create").click(function(){
		Dashboard.dialog.open({
			title: 'Create dashboard',
			parameters: {
				ui:'createDashboard'
			}
		});
		return false;
	});

	$J("#dashlet-add").click(function(){
		Dashboard.dialog.open({
			title: 'Add dashlet',
			width: 200,
			height: 450,
			position: "right",
			parameters: {
				ui:'addDashlet'
			}
		});
		return false;
	});

	Dashboard.lock('Loading Dashlets...');
<?php

$dashlets = $dashboard->getDashlets();
foreach ($dashlets as $i=>$column)
{
	foreach ($column as $dashlet)
	{
?>
	Dashboard.dashlets.add({
		dashlet: '<?php echo $dashlet->getId() ?>',
		column: <?php echo $i ?>,
		saveOnAdd: false
	});
<?php
	}
}

?>
	Dashboard.locked = false;
	Dashboard.unblockUI();
});

</script>

<?php

$sJavaScript = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sJavaScript);


ob_start();
?>

<?php
$blockData = ob_get_contents();
ob_end_clean();

$smarty->caching=0;

$query = 'SELECT fn_get_personell_name(personell_nr) FROM care_users WHERE login_id='.$db->qstr($_SESSION['sess_temp_userid']);
$fullname = $db->GetOne($query);

$smarty->assign('Name',$fullname);

$smarty->assign('user', array(
	'username' => $_SESSION['sess_temp_userid'],
	'fullname' => $fullname
));

$smarty->assign('sMainBlockIncludeFile','templates/dashboard.tpl');

/**
* show Template
*/
$smarty->display('common/main.tpl');

?>