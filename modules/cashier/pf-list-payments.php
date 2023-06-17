<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/curl/class_curl.php');
require_once($root_path.'include/care_api_classes/class_pf.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');

$encounter_nr = $_GET['encounter_nr'] ? $_GET['encounter_nr'] : $_POST['encounter_nr'];
$dr_nr = $_GET['dr_nr'] ? $_GET['dr_nr'] : $_POST['dr_nr'];
$data['amount'] = $_GET['amount'] ? $_GET['amount'] : $_POST['amount'];
$dr_name = $_GET['dr_name'] ? $_GET['dr_name'] : $_POST['dr_name'];

$pf= new Pf();
$curl = new Rest_Curl();
$enc = new Encounter();

$enc_data = $enc->getEncounterInfo($encounter_nr);

if($_POST['is_submitted']){
	
	$data = array("or_no"=>$_POST['or_no'],
						"amount"=>$_POST['amount'],
						"notes"=>$_POST['notes'],
						"encounter_nr"=>$_POST['encounter_nr'],
						"dr_nr"=>$_POST['dr_nr'],
						"old_or"=>$_POST['old_or'],
						"refno"=>$_POST['refno'],
						"pid"=>$enc_data['pid'],
						"date"=>date('F j Y h:i:s A')
					);
	
	$pf->data = $data;
	$refno = $pf->saveOr();

	if($refno){
		$data['refno'] = $refno;
		$data['old_or'] = $_POST['or_no'];
		
		//$curl->inpatientPfAmount($data);

		echo "Succefully updated payment.";
	}else{
		echo "Error Saving Data.";
	}
}else{
	$pf->data = array("encounter_nr"=>$encounter_nr,
					"dr_nr"=>$dr_nr);

	$result = $pf->getOr();
	if($result->RecordCount()){
		while($row = $result->FetchRow()){
			$data = array("or_no"=>$row['or_no'],
								"amount"=>$row['amount'],
								"notes"=>$row['notes'],
								"old_or"=>$row['or_no'],
								"refno"=>$row['refno']
							);
		}
	}
}

?>

<form method="POST">
 	<table border=0 cellpadding=1 cellspacing=1 align="center" >
		<tr class="adm_item">
			<td>Patient</td>
			<td><input type='text' disabled size="30" value="<?=$enc_data['name_first'].' '.$enc_data['name_last']?>" required/>
		</tr>
		<tr class="adm_item">
			<td>Encounter #</td>
			<td><input type='text' disabled value="<?=$encounter_nr?>" required/>
		</tr>
		<tr class="adm_item">
			<td>Doctor</td>
			<td><input type='text' size="30" disabled name="dr_name" value="<?=$dr_name?>" required/>
		</tr>
		<tr class="adm_item">
			<td>OR #</td>
			<td><input type='text' id="or_no" name='or_no' value="<?=$data['or_no']?>" required/>
				<input type='hidden' id="old_or" name='old_or' value="<?=$data['old_or']?>" required/></td>
				<input type='hidden' id="refno" name='refno' value="<?=$data['refno']?>" required/></td>
		</tr>
		<tr class="adm_item">
			<td>Amount</td>
			<td><input type='text' readOnly id="amount" name='amount' value="<?=$data['amount']?>" required/></td>
		</tr>
		<tr class="adm_item">
			<td>Remarks</td>
			<td><textarea id="notes" name='notes'/><?=$data['notes']?></textarea></td>
		</tr>
		<tr>
			<td colspan='2' align="center">
				<input  height="23" width="72" type="image" src="../../gui/img/control/default/en/en_savedisc.gif">
			</td>
		</tr>
	</table>
	
	<input type="hidden" name="encounter_nr" id="encounter" value="<?=$encounter_nr?>"/>
	<input type="hidden" name="dr_nr" id="dr_nr" value="<?=$dr_nr?>"/>
	<input type="hidden" name="is_submitted" id="is_submitted" value="1"/>
</form>