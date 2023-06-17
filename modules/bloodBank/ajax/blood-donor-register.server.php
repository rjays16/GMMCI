<?php
//created by cha 08-03-09
	 function registerBloodDonor($donor_details)
	 {
			global $db;
			$objResponse = new xajaxResponse();
			$bloodObj = new SegBloodBank();
			//$objResponse->alert("brgy=".$donor_details[6]." mun=".$donor_details[7]);
			$output=$bloodObj->saveBloodDonorDetails($donor_details);
			if($output)
			{
				$objResponse->call("refreshFrame","Save successful!");
			}
			else
			{
				$objResponse->call("refreshFrame","Save not successful!");
			}
			return $objResponse;
	 }

	function computeAge($birthdate)
	{
		global $db;
		$objResponse = new xajaxResponse();
		$personObj = new Person();
		$age=$personObj->getAge(date("m/d/Y",strtotime($birthdate)));
		$objResponse->call("printAge",$age);
		return $objResponse;
	}

	function populateDonorList($searchID,$page)
	{
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		$objResponse = new xajaxResponse();
		$bloodObj = new SegBloodBank();

		$offset = $page * $maxRows;
		$total_donor = $bloodObj->countBloodDonor($searchID,0,$maxRows,$offset);
		$total = $bloodObj->count;
				$lastPage = floor($total/$maxRows);

				if ((floor($total%10))==0)
						$lastPage = $lastPage-1;

				if ($page > $lastPage) $page=$lastPage;
			 # $objResponse->alert('searchid='.$searchID);
				$dataRow=$bloodObj->getDonorData($searchID,0,$maxRows,$offset);
				#$objResponse->alert("sql = ".$bloodObj->sql);
				$rows=0;
				$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->call("clearList","donorlist");
				if ($dataRow) {
						$rows=$dataRow->RecordCount();
						while($result=$dataRow->FetchRow())
						{
								$objResponse->call("viewDonorList","donorlist",trim($result["donor_id"]),ucwords(trim($result["Name"])),trim($result["Address"]),trim(number_format($result["age"],0)),trim($result['register_date']),trim($result['blood_type']));
								#$objResponse->alert("viewDonorList-".trim($result["donor_id"])." ".trim($result["Name"])." ".trim($result["Address"])." ".trim(number_format($result["age"],0))." ".trim($result['register_date'])." ".trim($result['blood_type']));
						}#end of while
				} #end of if
				if (!$rows) $objResponse->call("viewDonorList","donorlist",NULL);
				$objResponse->call("endAJAXSearch",$sElem);

		return $objResponse;
	}

		function getMuniCityandProv($brgy_nr) {
				global $db;

				$objResponse = new xajaxResponse();

				$strSQL = "SELECT p.prov_nr, m.mun_nr, p.prov_name, m.mun_name \n
											FROM (seg_barangays as b inner join seg_municity as m \n
												 on b.mun_nr = m.mun_nr) inner join seg_provinces as p \n
												 on m.prov_nr = p.prov_nr \n
												 where b.brgy_nr = $brgy_nr";

				if ($result = $db->Execute($strSQL)) {
						if ($row = $result->FetchRow()) {

								$objResponse->call("setMuniCity", (is_null($row['mun_nr']) ? 0 : $row['mun_nr']), (is_null($row['mun_name']) ? '' : $row['mun_name']));
								//$objResponse->call("setProvince", (is_null($row['prov_nr']) ? 0 : $row['prov_nr']), (is_null($row['prov_name']) ? '' : $row['prov_name']));
						}
				}

				return $objResponse;
		}

	function deleteBloodDonor($deleteID)
	{
		 global $db;
		 $objResponse = new xajaxResponse();
		 $bloodObj = new SegBloodBank();
		 $output = $bloodObj->deleteBloodDonor($deleteID);
		 if($output)
			{
				$objResponse->call("refreshFrame","Delete successful!");
			}
			else
			{
				$objResponse->call("refreshFrame","Delete not successful!");
			}
		 return $objResponse;
	}

	function getDonorDetails($donorID)
	{
		 global $db;
		 $objResponse = new xajaxResponse();
		 $bloodObj = new SegBloodBank();
		 $output = $bloodObj->getEditDonorDetails($donorID);
		 if($output)
		 {
				 $details_array = array();
				 while($result = $output->FetchRow())
				 {
						#echo "<br>".$result['donor_id'].",".$result['last_name'].",".$result['first_name'].",".$result['middle_name'].",".$result['birth_date'].",".$result['age'].",".$result['sex'].",".$result['street_name'].",".$result['brgy_nr'].",".$result['mun_nr'].",".$result['civil_status'].",".$result['blood_type']." ";
						$details_array[0] = $result['donor_id'];
						$details_array[1] = $result['last_name'];
						$details_array[2] = $result['first_name'];
						$details_array[3] = $result['middle_name'];
						$details_array[4] = $result['birth_date'];
						$details_array[5] = $result['age'];
						$details_array[6] = $result['sex'];
						$details_array[7] = $result['street_name'];
						$details_array[10] = $result['civil_status'];
						$details_array[11] = $result['blood_type'];

						$sql = "select brgy_name from seg_barangays where brgy_nr='".$result['brgy_nr']."'";
						if($result2=$db->Execute($sql))
						{
							$row=$result2->FetchRow();
							$details_array[8] = $row['brgy_name'];
							//echo "brgy_name=".$details_array[8];
						}
						$sql = "select mun_name from seg_municity where mun_nr='".$result['mun_nr']."'";
						if($result2=$db->Execute($sql))
						{
							$row=$result2->FetchRow();
							$details_array[9] = $row['mun_name'];
							//echo "mun_name=".$details_array[9];
						}
				 }
				 $objResponse->call("setDonorDetails",$details_array);
		 }
		 return $objResponse;
	}

	function updateBloodDonor($donor_details)
	{
			global $db;
			$objResponse = new xajaxResponse();
			$bloodObj = new SegBloodBank();
			$output=$bloodObj->updateBloodDonorDetails($donor_details);
			if($output)
			{
				$objResponse->call("refreshFrame","Update successful!");
			}
			else
			{
				$objResponse->call("refreshFrame","Update not successful!");
			}
			$objResponse->alert($bloodObj->sql);
			return $objResponse;
	}

	function saveBloodDetails($donorID, $blood_qty, $blood_unit, $donate_date)
	{
				global $db;
				$objResponse = new xajaxResponse();
				$bloodObj = new SegBloodBank();
				$donate_time = date("H:i:s");
				$output=$bloodObj->saveBloodDetails($donorID, $blood_qty, $blood_unit, $donate_date, $donate_time);
				$objResponse->alert($bloodObj->sql);
				if($output)
				{
						$objResponse->call("refreshFrame","Add item successful!");
				}
				else
				{
						$objResponse->call("refreshFrame","Add item not successful!");
				}
				return $objResponse;
	}

	function populateDonationList($donorID, $page)
	{
				global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
				$objResponse = new xajaxResponse();
				$bloodObj = new SegBloodBank();

				$offset = $page * $maxRows;
				$total_donor = $bloodObj->countBloodDonation($donorID,0,$maxRows,$offset);
				$total = $bloodObj->count;
						$lastPage = floor($total/$maxRows);

						if ((floor($total%10))==0)
								$lastPage = $lastPage-1;

						if ($page > $lastPage) $page=$lastPage;
						$dataRow=$bloodObj->getDonationData($donorID,0,$maxRows,$offset);
						$rows=0;
						$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
						$objResponse->call("clearList","donorlist");
						if ($dataRow) {
								$rows=$dataRow->RecordCount();
								while($result=$dataRow->FetchRow())
								{
										$converted = date("m-d-Y", strtotime($result['donor_date']));
										$date_new = $converted." ".$result['donor_time'];
										$objResponse->call("viewDonationList","donorlist",trim($result["donor_id"]),trim($result["item_id"]),trim($date_new),trim($result["qty"]),trim($result['unit']));
								}#end of while
						} #end of if
						if (!$rows) $objResponse->call("viewDonationList","donorlist",NULL);
						$objResponse->call("endAJAXSearch",$sElem);

				return $objResponse;
	}

	function deleteBloodItem($donorID, $itemID)
	{
		 global $db;
		 $objResponse = new xajaxResponse();
		 $bloodObj = new SegBloodBank();
		 $output = $bloodObj->deleteBloodItem($donorID, $itemID);
		 //echo $bloodObj->sql;
		 if($output)
			{
				$objResponse->alert("Delete successful!");
			}
			else
			{
				$objResponse->alert("Delete not successful!");
			}
		 return $objResponse;
	}

	function updateBloodItem($donorID, $itemID, $new_qty, $new_unit)
	{
			global $db;
			$objResponse = new xajaxResponse();
			$bloodObj = new SegBloodBank();
			$output=$bloodObj->updateBloodItemDetails($donorID, $itemID, $new_qty, $new_unit);
			if($output)
			{
				$objResponse->call("refreshFrame","Update successful!");
			}
			else
			{
				$objResponse->call("refreshFrame","Update not successful!");
			}
			return $objResponse;
	}

	function selectDonorList($searchID,$page)
	{
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		$objResponse = new xajaxResponse();
		$bloodObj = new SegBloodBank();

		$offset = $page * $maxRows;
		$total_donor = $bloodObj->countBloodDonor($searchID,0,$maxRows,$offset);
		$total = $bloodObj->count;
				$lastPage = floor($total/$maxRows);

				if ((floor($total%10))==0)
						$lastPage = $lastPage-1;

				if ($page > $lastPage) $page=$lastPage;
				$dataRow=$bloodObj->getDonorData($searchID,0,$maxRows,$offset);
				$rows=0;
				$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->call("clearList","person-list");
				if ($dataRow) {
						$rows=$dataRow->RecordCount();
						while($result=$dataRow->FetchRow())
						{
								$objResponse->call("selectDonorList","person-list",trim($result["donor_id"]),trim($result["Name"]),trim($result["Address"]),trim(number_format($result["age"],0)),trim($result['register_date']),trim($result['blood_type']));
						}#end of while
				} #end of if
				if (!$rows) $objResponse->call("selectDonorList","person-list",NULL);
				$objResponse->call("endAJAXSearch",$sElem);

		return $objResponse;
	}
	 require('./roots.php');
	 include_once($root_path.'include/care_api_classes/class_globalconfig.php');
	 require($root_path.'include/inc_environment_global.php');
	 require($root_path.'include/care_api_classes/class_blood_bank.php');
	 require($root_path.'include/care_api_classes/class_person.php');
	 require($root_path.'modules/bloodBank/ajax/blood-donor-register.common.php');
	 $xajax->processRequest();
?>
