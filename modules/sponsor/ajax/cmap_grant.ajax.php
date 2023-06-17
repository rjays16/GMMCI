<?php
define('NO_CHAIN',1);
require './roots.php';
require_once "{$root_path}include/inc_environment_global.php";
require_once($root_path.'include/inc_front_chain_lang.php');
require_once $root_path."include/care_api_classes/sponsor/class_request.php";
require_once $root_path."include/care_api_classes/sponsor/class_grant.php";

//$form_id = $_GET['src'].$_GET['nr'].$_GET['code'];

// Fetch request info
$request = new SegRequest( $type=$_GET['src'], $keys=Array('refNo'=>$_GET['nr'], 'itemNo'=>$_GET['code'], 'entryNo'=>$_GET['entry']) );
$info = $request->fetch();

if ($info===false) {
	die('Unable to fetch request details. Please contact your administrator...');
}

// compute total
$info['total'] = (float)$info['unitPrice']*(float)$info['quantity'];

// get total grant amount
$grant = SegGrant::getInstance();
$info['grant'] = (float)$grant->getTotalGrants( $request );

// compute total due
$info['totalDue'] = $info['total'] - $info['grant'];
if ($info['totalDue'] < 0)
	$info['totalDue'] = 0;

// Get remaining balance
require_once "{$root_path}include/care_api_classes/sponsor/class_cmap_patient.php";
if($info['pid']!="") {
	$pc = new SegCmapPatient('pid', $info['pid']);
}
else if($info['walkinPid']!="") {
	$pc = new SegCmapPatient('walkin', $info['walkinPid']);
}
$balance = (float) $pc->getBalance();
?>
<div class="container">
	<form id="<?= $_GET['wid'] ?>_transfer_data" style="margin:0">
	<div style="width:96%">
		<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="">
			<tbody>
				<tr>
					<td class="segPanel" align="right" valign="middle" width="18%"><strong>Balance</strong></td>
					<td class="" align="left" valign="middle" width="30%" style="" nowrap="nowrap">
						<input id="<?= $_GET['wid'] ?>_src" name="src" type="hidden" value="<?= $_GET['src'] ?>" />
						<input id="<?= $_GET['wid'] ?>_nr" name="ref_no" type="hidden" value="<?= $_GET['nr'] ?>" />
						<input id="<?= $_GET['wid'] ?>_nr" name="entry" type="hidden" value="<?= $_GET['entry'] ?>" />
						<input id="<?= $_GET['wid'] ?>_code" name="service_code" type="hidden" value="<?= $_GET['code'] ?>" />
						<input id="<?= $_GET['wid'] ?>_pid" name="pid" type="hidden" value="<?= $info['pid'] ?>" />
						<input id="<?= $_GET['wid'] ?>_service" name="service_name" type="hidden" value="<?= $info['itemName'] ?>" />
						<input id="<?= $_GET['wid'] ?>_referral_show" class="clear" type="text" size="12" value="<?= number_format($balance,2) ?>" disabled="disabled" style="text-align:right; font-size:12px; font-family:Tahoma; color:#000060" >
						<input id="<?= $_GET['wid'] ?>_referral" type="hidden" value="<?= $balance ?>" disabled="disabled" >
						<!-- <button class="segButton" onclick="return false;"><img <?= createComIcon('../../', 'info.png') ?>>View referrals</button> -->
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle"><strong>Total due</strong></td>
					<td class="" align="left" valign="middle" style="border-right:0" nowrap="">
						<input id="<?= $_GET['wid'] ?>_due" type="hidden" value="<?= (float) $info['totalDue'] ?>" />
						<input id="<?= $_GET['wid'] ?>_show_due" class="clear" type="text" value="<?= number_format($info['totalDue'],2) ?>" size="12" disabled="disabled" style="text-align:right; font-size:12px; font-family:Tahoma; color:#000060" />
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle"><strong>Amount</strong></td>
					<td class="" align="left" valign="middle" style="border-right:0" nowrap="">
<?php
$allow_full = ($_GET['src']==SegRequest::BILLING_REQUEST) || ($balance >= $info['totalDue']);
$allow_partial = ($_GET['src']==SegRequest::BILLING_REQUEST);
?>
						<input id="<?= $_GET['wid'] ?>_show_amount" class="segInput" type="text" value="0.00" size="12" readonly="readonly" style="text-align:right; font:bold 12px Tahoma" />
						<input id="<?= $_GET['wid'] ?>_amount" name="amount" type="hidden" value="0.00" />
						<button id="<?= $_GET['wid'] ?>_full" class="segButton" onclick="fullAmount('<?= $_GET['wid'] ?>'); return false;" <?= $allow_full ? '' : 'disabled="disabled"' ?>><img src="../../gui/img/common/default/emoticon_happy.png" />Full</button>
						<button id="<?= $_GET['wid'] ?>_partial" class="segButton" onclick="partialAmount('<?= $_GET['wid'] ?>'); return false" <?= $allow_partial ? '' : 'style="display:none"' ?>><img src="../../gui/img/common/default/emoticon_smile.png" />Partial</button>
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle">
						<strong>Remarks</strong>
					</td>
					<td class="" align="left" valign="middle" style="border-right:0">
						<textarea class="segInput" id="<?= $_GET['wid'] ?>_remarks" name="remarks" rows="1" cols="20"></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div style="padding:2px; padding-left:26%; text-align:left">
		<!--<input class="segButton" type="button" value="Grant request" onclick="save('<?= $_GET['wid'] ?>')" style="color:#000066"/>-->
		<!--<input class="segButton" type="button" value="Close" onclick="closeGrant('<?= $_GET['wid'] ?>')"/>-->
		<button class="segButton" onclick="save('<?= $_GET['wid'] ?>'); return false;"><img src="../../gui/img/common/default/tick.png" />Grant request</button>
		<button class="segButton" onclick="closeGrant('<?= $_GET['wid'] ?>'); return false;"><img src="../../gui/img/common/default/cancel.png" />Cancel</button>
	</div>
	</form>
</div>
<?php
