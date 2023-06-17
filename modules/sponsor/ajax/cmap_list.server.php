<?php
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'modules/sponsor/ajax/cmap_list.common.php';

function cancelEntry($params)
{
	global $root_path, $db;

	$objResponse = new xajaxResponse();

	require_once $root_path.'include/care_api_classes/sponsor/class_request.php';
	require_once $root_path.'include/care_api_classes/sponsor/class_cmap_referral.php';
	require_once $root_path.'include/care_api_classes/sponsor/grantors/class_cmap_grantor.php';

	//$referral = new SegLingapReferral($params['id']);
	$request = new SegRequest($params['source'], array(
		'refNo' => $params['refNo'],
		'itemNo' => $params['itemCode']
	));
	$cmap = new SegCmapGrantor($params['id'], $request->getRequestType());
	if ($cmap->getId())
	{
		$db->StartTrans();

		if (false !== ($cmap->ungrant($request)))
		{
		}
		else
		{
			$objResponse->alert("ungrant:".$db->ErrorMsg());
			$db->FailTrans();
		}


		if ($db->HasFailedTrans())
		{
			$db->CompleteTrans();
			$objResponse->alert('An error occurred while attempting to cancel the entry! Please contact your system administrator.');
		}
		else
		{
			$db->CompleteTrans();
			$objResponse->alert('The Lingap entry was successfully cancelled!');
			$objResponse->call('startSearch');
		}
	}
	else
	{
		$objResponse->alert('Invalid Referral ID encountered...');
	}

	return $objResponse;
}
$xajax->processRequest();
