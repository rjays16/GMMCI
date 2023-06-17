<?php
define('NO_CHAIN',1);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/inc_front_chain_lang.php';
require_once $root_path.'include/care_api_classes/class_core.php';
require_once $root_path.'classes/json/json.php';

global $db;

// set Content type to JSON
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");


$db->StartTrans();

// Save Dashboard settings
$core = new Core;
$core->setTable('seg_dashboards', $fetchMetadata=true);
$data = Array( 'id' => $_REQUEST['dashboard']);
if ($_REQUEST['title'])
{
	$data['title'] = $_REQUEST['title'];
}
if ($_REQUEST['columns'])
{
	$data['columns'] = $_REQUEST['columns'];
}
if ($_REQUEST['columnWidths'])
{
	if (is_array($_REQUEST['columnWidths']))
	{
		$data['column_widths'] = implode('|', $_REQUEST['columnWidths']);
	}
}
$ok = $core->save($data);

if ($ok)
{
	// Clear dashlets
	$query = "UPDATE seg_dashlets SET is_deleted=1 WHERE dashboard=".$db->qstr($_REQUEST['dashboard']);
	$ok = $db->Execute($query);
}
else
{
}

if ($ok)
{

	// Save Dashlet layout
	$dashletCore = new Core;
	$dashletCore->setTable('seg_dashlets', $fetchMetadata=true);

	$debug = print_r($_REQUEST['dashlets'], true);

	if (is_array($_REQUEST['dashlets']))
	{

		foreach ($_REQUEST['dashlets'] as $column=>$list)
		{
			if (is_array($list))
			{
				$rank = count($list) * 10;
				foreach ($list as $dashlet)
				{
					$data = Array(
						'id' 					=> $dashlet,
						'column_no' 	=> $column,
						'rank' 				=> $rank,
						'is_deleted' 	=> 0
					);

					$ok = $dashletCore->save($data);
					if (!$ok)
					{
						break;
					}
					$rank -= 10;

				}
			}
			if (!$ok) {
				break;
			}
		}

	}

}

if (!$ok)
{
	$db->FailTrans();
}
$db->CompleteTrans();


$data = Array(
	'success' => ($ok ? 1 : 0)
	//'debug' => $debug

);
$json = new Services_JSON();
print $json->encode($data);