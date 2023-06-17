<?php
define('NO_CHAIN',1);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/inc_front_chain_lang.php';
require_once $root_path.'gui/smarty_template/smarty_care.class.php';

$smarty = new smarty_care('common');

$suffix = uniqid();
$smarty->assign('sRootPath', '../../');
$smarty->assign('suffix', $suffix);


require_once $root_path.'include/care_api_classes/dashboard/Dashboard.php';

// ui parameter determines what UI component/s to send back
switch( $_REQUEST['ui'] ) {


	// loads UI layout for the Create dashboard dialog
	case 'createDashboard':
		$smarty->assign('settings', Array(
			'title' => '',
		));
		$html = $smarty->fetch($root_path.'modules/dashboard/templates/ui/dashboard_create.tpl');
		break;


	// loads UI for the Dashboard settings dialog
	case 'settings':
		$id = $_REQUEST['dashboard'];

		// load the Dashboard, but do not load the Dashlets since we are only interested in retrieving the Dashboard info
		// this prevents unenecessary usage of computer resources as retrieving Dashlet information is rather expensive
		$dashboard = Dashboard::loadDashboard($id, $loadDashlets=false);

		if (false !== $dashboard)
		{
			$widths = $dashboard->getColumnWidths();
			foreach ($widths as $i=>$v)
			{
				if (!$widths[$i]) $widths[$i] = 0;
			}

			//$totalWidth = array_sum($widths);
			$smarty->assign('settings', Array(
				'title' => $dashboard->getTitle(),
				'columns' => $dashboard->getColumnCount(),
				'widths' => '['.implode(',', $widths).']',
				'totalWidth' => $totalWidth
			));
			$html = $smarty->fetch($root_path.'modules/dashboard/templates/ui/dashboard_settings.tpl');
		}
		else
		{
			// Show error message
			$html = 'Failed to load dashboard settings...';
		}

		break;


	// UI components for the Add dashlet dialog
	case 'addDashlet':

		require_once $root_path.'include/care_api_classes/dashboard/DashletManager.php';
		$manager = DashletManager::getInstance();

		// get the list of Published Dashlets from the DashletManager
		$list = $manager->getDashlets();

		$categories = array();
		foreach ($list as $name=>$dashlet)
		{
			$category = $dashlet['category'];
			if (!$categories[$category])
			{
				$categories[$category] = array( 'name'=>$category, 'dashlets'=> array());
			}
			$categories[$category]['dashlets'][] = $dashlet;
		}

		$smarty->assign('categories', $categories);

		$html = $smarty->fetch($root_path.'modules/dashboard/templates/ui/dashlet_add.tpl');
		break;
}

echo $html;