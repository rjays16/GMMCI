<?php
/**
* ajax/deleteDashlet.ajax.php
*
* AJAX Response for the Dashboard.dashlets.remove AJAX call. The script accepts a single
* request parameter named <code>dashlet</code> which pertains to the unique id of the
* dashlet to be removed/deleted. The response returns a JSON-encoded object with 2
* properties: <code>status</code> and <code>error</code>
*
*/

define('NO_CHAIN',1);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'classes/json/json.php';
require_once $root_path.'include/care_api_classes/class_core.php';


global $db;

// set Content type to JSON
Services_JSON::sendHeaders();



$core = new Core;
$core->setTable('seg_dashboards', $fetchMetadata=true);
if (false !== ($saveOk = $core->delete(Array('id' => $_REQUEST['dashlet']))))
{
	$saveOk = 1;
	$errorMessage = '';
}
else
{
	$saveOk = 0;
	$errorMessage = $core->getErrorMsg();
}

$json = new Services_JSON();
print $json->encode(Array(
	'status' => $saveOk,
	'error' => $errorMessage
));