<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/nursing/ajax/nursing-station-radio-common.php');
//require_once($root_path.'include/care_api_classes/class_radioservices_transaction.php');

	include_once($root_path.'include/care_api_classes/class_department.php');
#	$dept_obj=new Department;
	include_once($root_path.'include/care_api_classes/class_personell.php');
#	$pers_obj=new Personell;
require_once($root_path.'include/care_api_classes/class_radiology.php');
	#$objService = new SegRadio;

require_once($root_path.'include/care_api_classes/class_tabview.php');
	require($root_path.'include/care_api_classes/class_discount.php');

/**
 * populate services
 * Fetches the database for available services associated 
 * with a specific service group
 */
function psrv($grp){
	$objResponse = new xajaxResponse();
	$objService = new SegRadio();
		
	$recordSet = $objService->getRadioServices("group_code='$grp'");
	
	$objResponse->addScriptCall("crow");
	$recCount = $objService->count;
	$count = 0;
	
	if($recCount>0){
		
		$objResponse->addScriptCall("ajxClearTable", $myid);
		$chk=0;
		//$objResponse->addAlert("recordset->".$recordSet);
		if($recordSet){
			while($row = $recordSet->FetchRow()){
				$count++;
				$price = $iscash? $row['price_cash']: $row['price_charge'];
				if(!$price) $price="N/A";
				else $price = number_format($price,2,'.','');
			//	$objResponse->addAlert("service->".$row['service_code']." row->".$row['name']." price->".$price." chk->".$chk);
				 //$objResponse->addScriptCall("nrow",$row['service_code'],$row['name'], $row['price_cash']);
				$objResponse->addScriptCall("appendServiceItemGroup",$row['group_code'],$row['service_code'], $row['name'], $price, $chk);
			}
		}else{
			$objResponse->addScriptCall("ajxClearTable", $grp);
			$objResponse->addScriptCall("appendServiceItemToGroup2", $grp);			
		}
	}else{
		//$objResponse->addScriptCall("nrow", NULL);
		$objResponse->addScriptCall("ajxClearTable", $grp);
		$objResponse->addScriptCall("appendServiceItemToGroup2", $grp);
		
	}
	return $objResponse;
} // end of function psrv


function srvList($enc){
	$objResponse = new xajaxResponse();
	$objSrv = new SegRadio();
	
	#$objResponse->addAlert("srvList(enc)=" . $enc);
	
	$srvRecord = $objSrv->getAllRadioInfoByEncounter($enc);
	//$srvRecord = $objSrv->getAllRadioInfoByBatch($batch_nr);
	
	if($srvRecord){
	#	$objResponse->addAlert("xajax_srvList() srvRecord->".$srvRecord);
	 //  $objResponse->addAlert(print_r($srvRecord));
		$count = 1;	
		while($row = $srvRecord->FetchRow()){
		 #	$objResponse->addAlert("row-> srevice code=".$row['service_code'], "\n count=".$count."\n batch_nr=".$row['batch_nr']);
			#$objResponse->addAlert("service_dept_nr->".$row['service_dept_nr']);
			$objResponse->addScriptCall("guiSrvTabContent",$count,$row['batch_nr'],$row['request_date'],$row['service_code'],$row['service_name'],$row['service_dept_nr'], $enc);
			$objResponse->addScriptCall("guiSrvTabAll",$count,$row['batch_nr'],$row['request_date'],$row['service_code'],$row['service_name'],$row['service_dept_nr'], $enc);
			$count++;
			
		}
		//
	}else{
		$objResponse->addScriptCall("clrSrvList");
		$objResponse->addScriptCall("srvListNoRecord");		
	}
	
	return $objResponse;
} // end of function srvList

function populateSrvListAll($enc){
	$objResponse = new xajaxResponse();
	$objRadio = new SegRadio();
	
	$objResponse->addAlert("populateSrvListAll: encounter=".$enc);
	
	$recordSet = $objRadio->getAllRadioInfoByEncounter($enc);
	if($recordSet){
		$count = 1;
		while($row = $recordSet->FetchRow()){
			$objResponse->addScriptCall("guiSrvTabAll",$count,$row['batch_nr'],$row['request_date'],$row['service_code'],$row['service_name'],$row['service_dept_nr'], $enc);
			$count++;
		}
	}
	return $objResponse;
}

function delSrv($tabValue,$RowNo,$batchNr, $enc){
	$objResponse =  new xajaxResponse();	
	$objRadio = new SegRadio();
	
	$objResponse->addAlert("tabvalue=".$tabValue."\n RowNo=".$RowNo."\n batch_nr=".$batchNr." \n enc=".$enc);
	#$objResponse->addAlert("Request service with Batch No. ".$batchNr." has been deleted.");
	
	$result = $objRadio->deleteRadioRequest($batchNr);
	#$result = true;
	if($result){
		$objResponse->addScriptCall("guiSrvDelete", $tabValue, $RowNo);
		$objResponse->addAlert("Request service with Batch No. ".$batchNr." has been deleted.");
		
		$objResponse->addAlert("delSrv : enc =".$enc);
		$objResponse->addScriptCall("guiSrvClearRows", 'all');
		$objResponse->addScriptCall("xajax_populateSrvListAll", $enc);
	}else{
		$objResponse->addAlert("Failed to delete. Batch No. ".$batchNr);
	}
	
	//guiSrvDelete(tabvalue, rowno)
	
	return $objResponse; 
}

//function getConstructedTab($tabArray){
function getConstructedTab(){
	$objResponse = new xajaxResponse();
	$objTab = new GuiTabView;
	$objTab->setTabViewName("mainTab");
	$objTab->setTabViewRoot($root_path);
	
	$tbody1 = "<tbody id=\"grpTabALL\"></tbody></table></div>";
	$tbody2 = "<tbody id=\"grpTabCT\"></tbody></table></div>";
	$tbody3 = "<tbody id=\"grpTabGR\"></tbody></table></div>";
	$tbody4 = "<tbody id=\"grpTabSP\"></tbody></table></div>";
	$tbody5 = "<tbody id=\"grpTabUS\"></tbody></table></div>";
	//ContentPane
	//$objResponse->addAlert("service Group->".$srvGrp);<div style="width:85%;height:90%;overflow:scroll;border:1px solid black">
	$tableAll	 =	 "<br><div style=\"width:98%;height:90%;overflow:auto;border:0px solid black\"><table id=\"srvTableALL\" style=\"border:1px solid #666666;border-bottom:0px\" width=\"98%\" border=\"0\" cellpadding=\"1\" cellspacing=\"1\">";
	$tableCT	 =	 "<br><div style=\"width:98%;height:90%;overflow:auto;border:0px solid black\"><table id=\"srvTableCT\" style=\"border:1px solid #666666;border-bottom:0px\" width=\"98%\" border=\"0\" cellpadding=\"1\" cellspacing=\"1\">";
	$tableGR	 =	 "<br><div style=\"width:98%;height:90%;overflow:auto;border:0px solid black\"><table id=\"srvTableGR\" style=\"border:1px solid #666666;border-bottom:0px\" width=\"98%\" border=\"0\" cellpadding=\"1\" cellspacing=\"1\">";
	$tableSP	 =	 "<br><div style=\"width:98%;height:90%;overflow:auto;border:0px solid black\"><table id=\"srvTableSP\" style=\"border:1px solid #666666;border-bottom:0px\" width=\"98%\" border=\"0\" cellpadding=\"1\" cellspacing=\"1\">";
	$tableUS	 =	 "<br><div style=\"width:98%;height:90%;overflow:auto;border:0px solid black\"><table id=\"srvTableUS\" style=\"border:1px solid #666666;border-bottom:0px\" width=\"98%\" border=\"0\" cellpadding=\"1\" cellspacing=\"1\">";
	
	$thead1  =   	"<thead id=\"grphead\" class=\"reg_list_titlebar\" style=\"height:0;overflow:visible;font-weight:bold;padding:4px;\" >";
	$thead1	.=	 		"<td width=\"2%\" nowrap>No.</td>";
	$thead1 .=   		"<td width=\"10%\" nowrap>Batch No.</td>";
	$thead1 .=	 		"<td width=\"15%\" nowrap>Date Requested</td>";
	$thead1 .=	 		"<td width=\"15%\" nowrap>Service Code</td>";
	$thead1 .=	 		"<td width=\"70%\" nowrap>Description</td>";
	$thead1 .=	 		"<td width=\"5%\" nowrap>Delete</td>";
	$thead1	.=	 	"</thead>";
	#$thead1	.=	  $tbody;
	//$thead1	.=	  "<tr><td><div id=\"divTbodyTab\"></div></td></tr>";
	//$thead1	.=	"</table>";
	
	//$sTabContents <div id="all"></div><div id="ct"></div><div id="gr"></div>
   $tabArray = array(array("all", "All", $tableAll.$thead1.$tbody1),
    				array("ct", "Computed Tomography", $tableCT.$thead1.$tbody2),
	  				array("gr", "General Radiography", $tableGR.$thead1.$tbody3),
	   				array("sp", "Special Procedure", $tableSP.$thead1.$tbody4),
	   				array("us", "Ultrasound", $tableUS.$thead1.$tbody5));
	

    $tabBody = "<tr bgcolor=\"#ffffff\"><td valign=\"top\" colspan=2>";
	$tabBody .= $objTab->getTabContainer($tabArray);
	$tabBody .= "</td></tr>";
		
	$objResponse->addAssign("tbViewTabs", "innerHTML", $tabBody);
	return $objResponse;
}// end of fucntion getConstructedTab


function srvGui($grpCode, $grpName){
	$objResponse = new xajaxResponse();
	
	$thead  =	"<thead class=\"\"><td colspan=\"4\">";
	$thead .=	"<table width=\"100%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\"><tr>";
	$thead .=    "<td width=\"*\" class=\"reg_header\">".$grpName."</td>";
	$thead .=	"<td width=\"1%\" align=\"right\" style=\"padding:2px;2px;font-weight:normal\" class=\"reg_header\">";
	$thead .=	"<span class=\"reglink\" onclick=\"toggleDisplay2('grpBody".$grpCode."');\">Show/Hide</span>";
	$thead .=	"</td>";
	$thead .=    "</tr></table>";
	$thead .=	"</td></thead>";
	
	//$thead1  =   "<table width=\"100%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">";
	$thead1 =   "<thead id=\"grphead".$grpCode."\" class=\"reg_list_titlebar\" style=\"height:0;overflow:visible;font-weight:bold;padding:4px;\" cellpadding=\"1\" cellspacing=\"1\">";
	$thead1 .=   "<td width=\"1\"><input type=\"checkbox\" id=\"chk_all_".$grpCode."\" name=\"chk_all_".$grpCode."\" onChange=\"checkAll(this.checked);countItem('".$grpCode."', 1);\"></td>";
	//$thead1 .=   "<td width=\"1\"><input id=\"chk_all_".$grpCode."\" name=\"chk_all\" type=\"checkbox\" onClink=\"countItem(1);\"></td>";
	$thead1	.=	 "<td width=\"15%\" nowrap>Code</td>";
	$thead1 .=   "<td width=\"60%\" nowrap>Description</td>";
	$thead1 .=	 "<td width=\"15%\" nowrap>Price</td>";
	$thead1	.=	 "</thead>";

	#$objResponse->addAlert("thead1->".$thead1);
	
	$tbody = "<tbody id=\"grpBody".$grpCode."\" style=\"height:0; overflow:visible\"></tbody>";
	
	#$objResponse->addAlert("grpCode->".$grpCode);
	
	$html = $thead.$thead1.$tbody;
	
	$objResponse->addAssign("srcRowsTable", "innerHTML", $html);
	
	return $objResponse;
}

function getAjxGui($grp){
	$objResponse = new xajaxResponse();
	//$objResponse->addScriptCall("xajax_srvGui", $grp, $name);
	$objResponse->addScriptCall("xajax_psrv", $grp);
	
	return $objResponse;
}

function getServiceGroup($dept_nr=''){
	$objResponse = new xajaxResponse();
	$objService = new SegRadio();
	
	$rs = $objService->getRadioServiceGroups2("department_nr='$dept_nr'");
	
	if($rs){
		$objResponse->addScriptCall("ajxClearOptions");
		if($objService->count > 0){
			$objResponse->addScriptCall("ajxAddOption", "Select Service Group", 0);
		}else{
			$objResponse->addScriptCall("ajxAddOption", "No Service Group", 0);
		}
	    
		while ($row = $rs->FetchRow()){
			$objResponse->addScriptCall("ajxAddOption", $row['name'], $row['group_code']);	
		}
	}else{
		$objResponse->addScriptCall("ajxClearOptions");
		$objResponse->addScriptCall("ajxAddOption", "No Service Group", 0);
		
		
		#$objResponse->addAlert("hello mark ajxclearTable is next to be executed");
		#$objResponse->addScriptCall("ajxClearTable");
		#$objResponse->addScriptCall("xajax_getAjxGui", 0);
	}
	return $objResponse;
}

/*******       burn added : August 31, 2007       *******/

	function populateRequestListByRefNo($refno=0){
		global $db;
		$objResponse = new xajaxResponse();
		$objRadio = new SegRadio();

#$objResponse->addAlert("populateRequestListByRefNo : refno='".$refno."'");
		$rs = $objRadio->getAllRadioInfoByRefNo($refno);
#		$objResponse->addAlert("populateRequestListByRefNo : objRadio->sql='".$objRadio->sql."'");
#		$objResponse->addAlert("populateRequestListByRefNo : rs : \n".print_r($rs,TRUE));
		if ($rs){
			while($result=$rs->FetchRow()) {
	#			$objResponse->addAlert("populateRequestListByRefNo : inside while loop : result : \n".print_r($result,TRUE));
#$objResponse->addAlert("populateRequestListByRefNo : result['hasPaid']='".$result['hasPaid']."'");
				$name = $result["name"];
				if (strlen($name)>40)
					$name = substr($result["name"],0,40)."...";
				$objResponse->addScriptCall("initialRequestList",$result['service_code'],$result['group_code'],
											$name, $result['clinical_info'], $result['request_doctor'],
											$result['request_doctor_name'], $result['is_in_house'], $result['price_cash_orig'], 
											$result['price_charge'],$result['hasPaid'],$result['is_socialized']);
			}
		}else{
			$objResponse->addScriptCall("emptyIntialRequestList");		
		}
//		$objResponse->addScriptCall("refreshTotal");
		$objResponse->addScriptCall("refreshDiscount");
		return $objResponse;
	}# end of function populateRequestListByRefNo

	function get_charity_discounts($nr=0) {
		
		
	} // end of get_charity_discounts
	
	function getCharityDiscounts($refno=''){
		global $db;
		$objResponse = new xajaxResponse();
		
		$sql = "SELECT * FROM seg_charity_amount WHERE ref_no='".$refno."'";
		
		if($result = $db->Execute($sql)){
			if($result->RecordCount()){
				//$objResponse->addAlert("hello world1 =".print_r($row, true));
				$row = $result->FetchRow();
				$amount = sprintf('%01.2f', $row['amount']);
				$objResponse->addScriptCall("eDiscount",$amount , true); 
				
			}else{
				//$objResponse->addAlert("No record found");
				$objResponse->addScriptCall("eDiscount", '' ,false);
			}
		}
		
		return $objResponse;
	}// end of getCharityDiscounts
	
		/*
		*	burn created: October 26, 2007
		*/
	function existSegCharityAmount($ref_no){
		global $db;

		if (!$ref_no)
			return FALSE;
	
		$sql="SELECT *	FROM seg_charity_amount
					WHERE ref_no='".$ref_no."' AND ref_source='RD'";

		if ($buf=$db->Execute($sql)){
			if($buf->RecordCount()) {
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }		
	}#end of function existSegCharityAmount
	
	function setCharityDiscounts($ref_no,$sw_nr,$amount){
		global $db;
		$objResponse = new xajaxResponse();
		
		$grand_dte =  date('Y-m-d H:i:s');
		$ref_source = 'RD';

		if (existSegCharityAmount($ref_no)){
			$sql="UPDATE seg_charity_amount
					SET grant_dte=NOW(), sw_nr=".$sw_nr.", amount=".$amount."
					WHERE ref_no='".$ref_no."' AND ref_source='RD'";
		}else{
			$sql = "INSERT INTO seg_charity_amount (ref_no, ref_source, grant_dte, sw_nr, amount) ".
				 "\n VALUES('".$ref_no."', '".$ref_source."', '".$grand_dte."', '".$sw_nr."' , '".$amount."' )";
		}			
		if($db->Execute($sql)){
			$objResponse->addAlert("Successfully save data.");
		}else{
			$objResponse->addAlert("ErrorMsg : ".$sql); 
		}
							
		return $objResponse;
	}// edn of setCharityDiscounts
	
/*
		global $db;
		$objResponse = new xajaxResponse();
		$discount= new SegDiscount();
		$ergebnis=$discount->GetEncounterCharityGrants( $nr );
		$objResponse->addAlert("get_charity_discounts : ergebnis='".$ergebnis."'; \ndiscount->sql".$discount->sql."'");
		$objResponse->addAlert("get_charity_discounts : ".print_r($ergebnis,TRUE));
		$objResponse->addScriptCall("clearCharityDiscounts");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				$objResponse->addScriptCall("addCharityDiscount",$result["discountid"],$result["discount"]);
			}
		}
		return $objResponse;
*/



$xajax->processRequests();
?>