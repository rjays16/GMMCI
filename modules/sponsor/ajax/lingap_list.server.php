<?php
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'modules/sponsor/ajax/lingap_list.common.php';

function cancelEntry($params)
{
	global $root_path, $db;

	$objResponse = new xajaxResponse();

	require_once $root_path.'include/care_api_classes/sponsor/class_request.php';
	require_once $root_path.'include/care_api_classes/sponsor/class_lingap_referral.php';
	require_once $root_path.'include/care_api_classes/sponsor/grantors/class_lingap_grantor.php';

	$referral = new SegLingapReferral($params['id']);
	if ($referral->getId())
	{
		$lingap = new SegLingapGrantor($referral);
		$request = new SegRequest($params['source'], array(
			'refNo' => $params['refNo'],
			'itemNo' => $params['itemCode']
		));

//		$objResponse->alert(var_export($request, true));

//		return $objResponse;
		$db->StartTrans();
		if (false !== ($lingap->ungrant($request)))
		{
			$grants = $lingap->getGrants();
			if (is_array($grants) && count($grants)===0)
			{
				if (false === $referral->cancel())
				{
					$db->FailTrans();
				}
			}

		}
		else
		{
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
