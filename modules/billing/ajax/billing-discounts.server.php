<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/billing/ajax/billing-discounts.common.php');
require_once($root_path.'include/care_api_classes/class_discount.php');

function getApplicableDiscounts($s_encnr, $frmdte, $billdte) {
	global $db;
	
	$objResponse = new xajaxResponse();
	//removed by jasper 07/17/2013, notified by bong FOR BUG#120
	/*$strSQL = "select 0 as entry_no, discountid, discountdesc, discount, discount_amnt, '' as billareas_applied, '' as areas, '' as remarks from ".
			  "      (select scg.discountid, discountdesc, scg.discount, scg.discount_amnt ".
			  "         from (seg_charity_grants_pid as scg inner join seg_discount as sd ".
              "            on scg.discountid = sd.discountid) inner join care_encounter as ce ".
              "            on ce.pid = scg.pid ".
			  "         where ce.encounter_nr = '". $s_encnr. "' ".
			  "            and str_to_date(grant_dte, '%Y-%m-%d %H:%i:%s') < '".$billdte."' ".
			  "         order by grant_dte desc limit 1) as t ".
			  "   union ".
			  "select 0 as e_no, discountid, discountdesc, discount, discount_amnt, billareas_applied, ".
			  "   (select group_concat(distinct benefit_desc order by benefit_desc separator '; ') as area_desc ".
              "       from seg_hcare_benefits as shb ".
              "       where sbd.billareas_applied regexp concat(shb.bill_area,':')) as areas, remarks ".
			  "   from seg_billingapplied_discount as sbd ".
			  "   where encounter_nr = '". $s_encnr. "' ".
			  "      and str_to_date(entry_dte, '%Y-%m-%d %H:%i:%s') < '".$frmdte."' ".
			  "   union ".
			  "select entry_no, discountid, discountdesc, discount, discount_amnt, billareas_applied, ".
			  "   (select group_concat(distinct benefit_desc order by benefit_desc separator '; ') as area_desc ".
              "       from seg_hcare_benefits as shb ".
              "       where sbd.billareas_applied regexp concat(shb.bill_area,':')) as areas, remarks ".			  
			  "   from seg_billingapplied_discount as sbd ".
			  "   where encounter_nr = '". $s_encnr. "' and (str_to_date(entry_dte, '%Y-%m-%d %H:%i:%s') >= '".$frmdte."' ".
			  "      and str_to_date(entry_dte, '%Y-%m-%d %H:%i:%s') < '".$billdte."')";*/

    $strSQL = "select 0 as entry_no, discountid, discountdesc, discount, discount_amnt, billareas_applied, ".
              "   (select group_concat(distinct benefit_desc order by benefit_desc separator '; ') as area_desc ".
              "       from seg_hcare_benefits as shb ".
              "       where sbd.billareas_applied regexp concat(shb.bill_area,':')) as areas, remarks ".
              "   from seg_billingapplied_discount as sbd ".
              "   where encounter_nr = '". $s_encnr. "' ".
              "      and str_to_date(entry_dte, '%Y-%m-%d %H:%i:%s') < '".$frmdte."' ".
              "   union ".
              "select entry_no, discountid, discountdesc, discount, discount_amnt, billareas_applied, ".
              "   (select group_concat(distinct benefit_desc order by benefit_desc separator '; ') as area_desc ".
              "       from seg_hcare_benefits as shb ".
              "       where sbd.billareas_applied regexp concat(shb.bill_area,':')) as areas, remarks ".
              "   from seg_billingapplied_discount as sbd ".
              "   where encounter_nr = '". $s_encnr. "' and (str_to_date(entry_dte, '%Y-%m-%d %H:%i:%s') >= '".$frmdte."' ".
			  "      and str_to_date(entry_dte, '%Y-%m-%d %H:%i:%s') < '".$billdte."')";

	$objResponse->addScriptCall("jsClearList", "discount_details");				  
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) 
			while ($row = $result->FetchRow()) 				
				$objResponse->addScriptCall("addApplicableDiscount", $s_encnr, $row['entry_no'], $row['discountid'], $row['discountdesc'], $row['billareas_applied'], $row['areas'], $row['remarks'], $row['discount'], $row['discount_amnt']);
		else
			$objResponse->addScriptCall("addApplicableDiscount", NULL, NULL, '', '', '', '', '', 0, 0);
	} 
	else 
		$objResponse->addScriptCall("addApplicableDiscount", NULL, NULL, '', '', '', '', '', 0, 0);
			
	return $objResponse;
}

function fillDiscountsCbo($s_id = '') {
	global $db;
	
	$objResponse = new xajaxResponse();

	$strSQL = "select * ".
   			  "   from seg_discount ".
   			  "   where (area_used = 'B' or isnull(area_used) or area_used = '') ".
	  		  "      and not is_charity ".
   			  "   order by discountdesc";
			  
	if ($result = $db->Execute($strSQL)) {
		$objResponse->addScriptCall("js_ClearOptions","discount_list");
	
		if ($result->RecordCount()) 
			$objResponse->addScriptCall("js_AddOptions","discount_list","- Select Discount -", '-');
		else 
			$objResponse->addScriptCall("js_AddOptions","discount_list","- No Discounts Available -", '-');
		
		while ($row = $result->FetchRow()) 
				$objResponse->addScriptCall("js_AddOptions","discount_list", $row['discountdesc'], $row['discountid'], ($s_id == $row['discountid']));						
	}
	else 
		$objResponse->addAlert("ERROR: ".$db->ErrorMsg());
	
	return $objResponse;
}

function SaveAppliedDiscount($aFormValues, $bill_dt = "0000-00-00 00:00:00") {
	global $db;
	$objResponse = new xajaxResponse();
	$bolError = false;	
	
	if(array_key_exists("enc_nr", $aFormValues)) {		
		// Adjust current time by 1 second earlier than cut-off date in billing ...			
		if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0) 
			$tmp_dte = $bill_dt;
		else
			$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");	
		$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));		
						
		if (!$bolError) {
			if (($aFormValues['entry_no'] != '0') && ($aFormValues['entry_no'] != '')) {						
				$strSQL = "update seg_billingapplied_discount set ".
						  "   discountid        = '".$aFormValues['discount_id']."', ".
						  "   discountdesc      = '".$aFormValues['discount_desc']."', ".
						  "   discount          =  ".( ($aFormValues['discount'] == '') ? '0.0000' : $aFormValues['discount']).", ".
                          "   discount_amnt     =  ".( ($aFormValues['discountamnt'] == '') ? '0.00' :  $aFormValues['discountamnt']).", ".
						  "   remarks           = '".$aFormValues['remarks']."', ".
						  "   billareas_applied = '".$aFormValues['areas_id']."', ".
						  "   modify_id         = '".$_SESSION['sess_user_name']."' ".
						  "   where encounter_nr = '".$aFormValues['enc_nr']."' ".
						  "      and entry_no    = ".$aFormValues['entry_no'];						  
			}
			else {
				$strSQL = "insert into seg_billingapplied_discount (encounter_nr, entry_dte, discountid, discountdesc, discount, discount_amnt, remarks, billareas_applied, modify_id, create_id) ".
						  "   values ('".$aFormValues['enc_nr']."', '".$tmp_dte."', '".$aFormValues['discount_id']."', '".$aFormValues['discount_desc']."', ".
						  "            ".( ($aFormValues['discount'] == '') ? '0.0000' : $aFormValues['discount']).", ".( ($aFormValues['discountamnt'] == '') ? '0.00' :  $aFormValues['discountamnt']).", '".$aFormValues['remarks']."', '".$aFormValues['areas_id']."', '".$_SESSION['sess_user_name']."', '".$_SESSION['sess_user_name']."')";
			}
			
			if ($db->Execute($strSQL))
				$objResponse->addScriptCall("js_getApplicableDiscounts");
			else 
				$objResponse->alert("ERROR: ".$db->ErrorMsg()."\n".$strSQL);
		}
	}
	
	return $objResponse;
}// end of function SaveAppliedDiscount()

function getDiscountInfo($enc_nr, $entry_no) {
	global $db;
	
	$objResponse = new xajaxResponse();
	
	$strSQL = "select sdb.*, ".
			  "   (select group_concat(distinct benefit_desc order by benefit_desc separator '\n') as area_desc ".
			  "       from seg_hcare_benefits as shb where sdb.billareas_applied regexp concat(shb.bill_area,':')) as areas ".		
	          "   from seg_billingapplied_discount as sdb ".
			  "   where sdb.encounter_nr = '".$enc_nr."' and sdb.entry_no = ".$entry_no;
	if ($result = $db->Execute($strSQL)) {			
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) 
				$objResponse->addScriptCall("js_showDiscountInfo", $row['discountid'], $row['discountdesc'], $row['remarks'], $row['billareas_applied'], $row['areas'], $row['discount']);
			else
				$objResponse->addScriptCall("js_showDiscountInfo", '', '', '', '', '', 0);
		}
	}
	else
		$objResponse->alert("ERROR: ".$db->ErrorMsg());

	return $objResponse;
}

function deleteDiscount($enc_nr, $entry_no) {
	global $db;
	
	$objResponse = new xajaxResponse();
	
	$strSQL = "delete from seg_billingapplied_discount ".
			  "   where encounter_nr = '".$enc_nr."' and entry_no = ".$entry_no;
	if ($db->Execute($strSQL)) 
		$objResponse->addScriptCall("js_getApplicableDiscounts");
	else
		$objResponse->alert("ERROR: ".$db->ErrorMsg());

	return $objResponse;
}

function getBillAreasApplied($sdiscount_id) {
	global $db;

	$objd  = new SegDiscount();		
	$areas = $objd->getBillAreas($sdiscount_id);
	
	$areas_desc = '';
	
	$objResponse = new xajaxResponse();
	
	if ($areas) {
		$strSQL = "select group_concat(distinct benefit_desc order by benefit_desc separator '\n') as area_desc ".
   				  "   from seg_hcare_benefits as shb where '".$areas."' regexp concat(shb.bill_area,':')";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow()) $areas_desc = $row['area_desc'];
			}
		}
	}
	else
		$areas = '';
	
	$objResponse->addScriptCall("js_showBillAreas", $areas, $areas_desc);
	
	return $objResponse;	
}

function getDiscount($sdiscount_id) {
	global $db;
	
	$objResponse = new xajaxResponse();	

	$objd   = new SegDiscount();		
	$n_rate = $objd->getDiscount($sdiscount_id);
	if ($n_rate) {
		$objResponse->addScriptCall("js_showDiscount", number_format($n_rate, 4, '.', ''));
	}
	
	return $objResponse;
}

$xajax->processRequests();
?>
