<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/or/ajax/ajax_anesthesia_gui.common.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php');

function anesthesia_procedure_save($ids, $names, $category_id, $category_name)
{
	global $db;
	$objResponse = new xajaxResponse();
	$author = $_SESSION['sess_temp_userid'];
	$no_error = true;
	$db->StartTrans();

	#save anesthesia category
	$sql = "INSERT INTO care_type_anaesthesia (id, name, create_id, create_time, modify_id, modify_time)".
	" VALUES ('$category_id', '$category_name', '$author', NOW(), '$author', NOW())";
	if($result = $db->Execute($sql))
	{
		if($db->Affected_Rows()>=1)
		{
			#save anesthesia specific
			for($i=0;$i<count($ids);$i++)
			{
				$sql = "INSERT INTO seg_or_sub_anesthesia (anesthesia_id, sub_anesth_id, description, status, create_id, create_dt, modify_id, modify_dt)".
				" VALUES ('$category_id', '$ids[$i]', '$names[$i]', '', '$author', NOW(), '$author', NOW())";
				if($result = $db->Execute($sql)){
					if($db->Affected_Rows()<1){
						 echo "<br>ERROR1 @ anesthesia specific save:".$sql."<br>".$db->ErrorMsg()."<br>";
						 $no_error=false;
					}
				}else{
					echo "<br>ERROR2 @ anesthesia specific save:".$sql."<br>".$db->ErrorMsg()."<br>";
					$no_error=false;
				}
			}
			$db->CompleteTrans();
		}else{
			echo "<br>ERROR1 @ anesthesia category save:".$sql."<br>".$db->ErrorMsg()."<br>";
			$no_error=false;
		}
	}else{
		echo "<br>ERROR2 @ anesthesia category save:".$sql."<br>".$db->ErrorMsg()."<br>";
		$no_error=false;
		$db->FailTrans();
	}

	if($no_error)
	{
		$objResponse->alert("Save successful!");
		$objResponse->call("window.parent.location.reload()");
	}
	else
	{
		$objResponse->alert("ERROR!");
	}
	return $objResponse;
}

//start--------celsy-----------
function anesthesia_category_delete($id)
{
		global $db;
		$objResponse = new xajaxResponse();
		$db->StartTrans();
		#delete anesthesia category
		$sql = "DELETE FROM care_type_anaesthesia where id=".$db->qstr($id);

		if($result = $db->Execute($sql)){
			$objResponse->alert("Deletion successful!");
			$objResponse->call("window.parent.location.reload()");
			$db->CompleteTrans();
		}
		else
		{
			$objResponse->alert("ERROR!");
			echo "<br>ERROR1 @ anesthesia category delete:".$sql."<br>".$db->ErrorMsg()."<br>";
			$db->FailTrans();
		}
		return $objResponse;
}

function anesthesia_edit_category_name($name, $id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$db->StartTrans();
	#update anesthesia category name
	$sql ="UPDATE care_type_anaesthesia SET name=".$db->qstr($name)." WHERE id=".$db->qstr($id);
	if($result = $db->Execute($sql)){
		$objResponse->alert("Update successful!");
		$objResponse->call("window.parent.location.reload()");
		$db->CompleteTrans();
	}
	else
	{
		$objResponse->alert("ERROR!");
		echo "<br>ERROR1 @ anesthesia category update:".$sql."<br>".$db->ErrorMsg()."<br>";
		$db->FailTrans();
	}
	return $objResponse;
}


function anesthesia_specific_delete($spec_id, $spec_name, $cat_id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$db->StartTrans();
	#delete specific anesthesia
	$sql = "DELETE FROM seg_or_sub_anesthesia WHERE anesthesia_id=".$db->qstr($cat_id)." AND sub_anesth_id=".$db->qstr($spec_id)." AND description=".$db->qstr($spec_name);

	if($result = $db->Execute($sql)){
		//$objResponse->alert("Deletion successful!");
		$objResponse->call("location.reload()");
		$db->CompleteTrans();
	}
	else
	{
		$objResponse->alert("ERROR!");
		echo "<br>ERROR1 @ anesthesia specific delete:".$sql."<br>".$db->ErrorMsg()."<br>";
		$db->FailTrans();
	}
	return $objResponse;
}


function anesthesia_specific_edit($old_spec_id, $old_spec_name, $new_spec_id, $new_spec_name, $cat_id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$db->StartTrans();
	#edit specific anesthesia
	$sql = "UPDATE seg_or_sub_anesthesia SET sub_anesth_id=".$db->qstr($new_spec_id).", description=".$db->qstr($new_spec_name)."WHERE anesthesia_id=".$db->qstr($cat_id)." AND sub_anesth_id=".$db->qstr($old_spec_id)." AND description=".$db->qstr($old_spec_name);
	if($result = $db->Execute($sql)){
		$objResponse->call("location.reload()");
		//edit code for loading new data
		$db->CompleteTrans();
	}
	else
	{
		$objResponse->alert("ERROR!");
		echo "<br>ERROR1 @ anesthesia specific edit:".$sql."<br>".$db->ErrorMsg()."<br>";
		$db->FailTrans();
	}
	return $objResponse;
}

function anesthesia_new_specific_save($specific_id, $specific_name, $category_id)
{
	#save new specific anesthesia on edit mode
	global $db;
	$objResponse = new xajaxResponse();
	$author = $_SESSION['sess_temp_userid'];
	$db->StartTrans();
	#validate if new data is unique
	$sql ="SELECT * FROM seg_or_sub_anesthesia WHERE anesthesia_id =".$db->qstr($category_id)." AND sub_anesth_id =".$db->qstr($specific_id);
	$result = $db->Execute($sql);
	$num_rows = $result->RecordCount();
	if($num_rows==0)
	{
		$sql = "INSERT INTO seg_or_sub_anesthesia (anesthesia_id, sub_anesth_id, description, status, create_id, create_dt, modify_id, modify_dt)".
				" VALUES ('$category_id', '$specific_id', '$specific_name', '', '$author', NOW(), '$author', NOW())";
		if($result = $db->Execute($sql)){
			//$objResponse->alert("Update successful!");
			//$objResponse->call("location.reload()"); //comment out by cha, june 22, 2010
			$objResponse->call("$('anesthesia_specific_list').list.refresh()");
			$db->CompleteTrans();
		}
		else
		{
			$objResponse->alert("ERROR!");
			echo "<br>ERROR1 @ anesthesia specific add:".$sql."<br>".$db->ErrorMsg()."<br>";
			$db->FailTrans();
		}
	}
	else
	{
		$objResponse->alert("A similar specific anesthesia already exists!");

	}
	return $objResponse;
}


$xajax->processRequest();
?>
