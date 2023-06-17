<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/radiology/ajax/radio-patient-common.php');
//require_once($root_path.'include/care_api_classes/class_radioservices_transaction.php');

require_once($root_path.'include/care_api_classes/class_paginator.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
include_once($root_path.'include/inc_date_format_functions.php');
	include_once($root_path.'include/care_api_classes/class_department.php');
#	$dept_obj=new Department;
	include_once($root_path.'include/care_api_classes/class_personell.php');
#	$pers_obj=new Personell;
require_once($root_path.'include/care_api_classes/class_radiology.php');
	#$objService = new SegRadio;

require_once($root_path.'include/care_api_classes/class_tabview.php');
require($root_path.'include/care_api_classes/class_discount.php');



function PopulateRadioRequest($tbId, $pid, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $mode, $oitem, $odir ){
	global $root_path;
	$objResponse = new xajaxResponse();	

#$objResponse->addAlert("PopulateRadioRequest : pid ='".$pid."'");
	
	#$objResponse->addAlert("ajax : tbid =".$tbId. "\n tbody = ".$tbody."\n searchkey = ".$searchkey."\n sub_dept_nr=".$sub_dept_nr."\n pgx=".$pgx."\n thisfile=".$thisfile." \n rpath= ".$rpath."\n mode=".$mode."\n oitem=".$oitem."\n odir=". $odir);
	//Display table header 
	RadioRequestHeader($objResponse,$tbId,$sub_dept_nr,$oitem, $odir);
		
	//Paginate & display list of radiology request
	populateRadioPatientRecords($objResponse, $pid, $searchkey, $sub_dept_nr, $pgx, $thisfile, $rpath, $odir, $oitem);
	
	return $objResponse;
}//end of PopulateRadioRequest


function RadioRequestHeader(&$objResponse,$tbId, $sub_dept_nr, $oitem, $odir){
/*
batch_nr
service_code
request_doctor
request_date
service_date
*/
	$tr  = "<thead>";
	$tr .= "<tr><th colspan=\"9\" id=\"mainHead".$sub_dept_nr."\"></th></tr>";
	$tr .= "<tr>";
	$tr .= "<th width=\"1%\">";
	$tr .= '<input id="chkall" type="checkbox" onClick="checkAll(\'Ttab'.$sub_dept_nr.'\',this.checked);$(\'selectedcount\').innerHTML=countSelected(\'Ttab'.$sub_dept_nr.'\');">';
	$tr .= "</th>";
	$tr .= "<th width=\"2%\"></th>";
	$tr .= makeSortLink('Batch No.','batch_nr', $oitem, $odir, $sub_dept_nr,'10%');
	$tr .= makeSortLink('Service Code','service_code', $oitem, $odir, $sub_dept_nr,'12%');
	$tr .= makeSortLink('Service Name','service_name', $oitem, $odir, $sub_dept_nr);
	$tr .= makeSortLink('Requesting Doctor','request_doctor_name', $oitem, $odir, $sub_dept_nr,'16%');
	$tr .= makeSortLink('Date Requested','request_date', $oitem, $odir, $sub_dept_nr,'14%');
	$tr .= makeSortLink('Date Serviced','service_date', $oitem, $odir, $sub_dept_nr,'12%');
	$tr .= "<th width=\"5%\">Status</th>";
	$tr .= "</tr>";
	$tr .= "</thead> \n";

#$objResponse->addAlert("tr: \n".$tr);		

	$tbody="<tbody id=\"TBodytab".$sub_dept_nr."\"></tbody>";
#	$prevNextTR = "<tr><td id=\"prevRow\" colspan=\"6\"></td>";
#	$prevNextTR .=    "<td id=\"nextRow\" align=right></td></tr>";
	
#	$HTML = $tr.$tbody.$prevNextTR;
	$HTML = $tr.$tbody;
    
	#$objResponse->addAlert("item=".$item."\n oitem=".$oitem."\n odir=".$odir."\n sub_dept_nr=".$sub_dept_nr);
	#$objResponse->addAlert("tbId=".$tbId);
	$objResponse->addAssign($tbId,"innerHTML",$HTML);				
	
} // end of RadioRequestHeader

function makeSortLink($txt='SORT',$item, $oitem,$odir='ASC', $subDeptNr='', $width='', $align='center'){
	if($item == $oitem){
		if($odir == 'ASC'){
			$img = "<img src=\"../../gui/img/common/default/arrow_red_up_sm.gif\">";
		}else{
			$img = "<img src=\"../../gui/img/common/default/arrow_red_dwn_sm.gif\">";
		}
	}else{
		$img='&nbsp;';
	}
	
	if($odir=='ASC') $dir ='DESC';
	else $dir = 'ASC';
											 #jsSortHandler(items, oitem, dir, sub_dept_nr)			
	$td = "<th width=\"".$width."\" align=\"".$align."\" onClick=\"jsSortHandler('$item', '$oitem','$dir', '$subDeptNr');\">".$img."<b>".$txt."</b></th> ";
	
	return $td;
} // end of function makeSortLink

function populateRadioPatientRecords(&$objResponse, $pid, $searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $odir='ASC', $oitem='create_dt'){
	global $date_format;
		# NOTE: $pid SHOULD BE set in order for `getRefCode()` function to work
	$objRadio = new SegRadio($pid);   

#	$objResponse->addAlert("populateRadioPatientRecords:: pid='$pid' \n objRadio->getRefCode() = ".$objRadio->getRefCode());	
	
	#Instantiate paginator 
	$pagen = new Paginator($pgx, $thisfile, $searchkey, $rpath, $oitem, $odir);
	
	$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('pagin_patient_search_max_block_rows');
	# Last resort, use the default defined at the start of this page
	if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); 
    else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);
		
#$pagen->setMaxCount(MAX_BLOCK_ROWS); 

#	if(($mode == 'search' || $mode == 'paginate') && !empty($searchkey)){
		$searchkey = strtr($searchkey, '*?', '%_');
#	}

#	$objResponse->addAlert("populateRadioPatientRecords:: searchkey = ".$searchkey."\n sub_dept_nr =".$sub_dept_nr); 
	#$ergebnis = &$objRadio->searchLimitBasicInfoRadioPending($searchkey,$sub_dept_nr,$pagen->MaxCount(), $pgx, $oitem, $odir);
	$ergebnis = &$objRadio->searchLimitRadioPatientRecords($searchkey,$sub_dept_nr, $pagen->MaxCount(), $pgx, $oitem, $odir);
	
#$objResponse->addAlert("populateRadioPatientRecords:: SQL objRadio->sql = ".$objRadio->sql);
#$objResponse->addAlert("populateRadioPatientRecords:: ergebnis = ".print_r($ergebnis,true));

	$linecount = $objRadio->LastRecordCount();
	$pagen->setTotalBlockCount($linecount);
	
	
	if(isset($totalcount)&& $totalcount){
		$pagen->setTotalDataCount($totalcount);	
	}else{
		@$objRadio->_searchRadioPatientRecords($searchkey, $sub_dept_nr,1);	
		#@$objRadio->_searchBasicInfoRadioPending($searchkey, $sub_dept_nr);
		$totalcount = $objRadio->LastRecordCount();
		$pagen->setTotalDataCount($totalcount);	
	}
	$pagen->setSortItem($oitem);
	$pagen->setSortDirection($odir);
	
	$msg = "\n ergebnis = '".$ergebnis."'\n linecount='".$linecount."' \n totalcount='".$totalcount."'";
#$objResponse->addAlert("populateRadioPatientRecords:: $msg");
#$objResponse->addAlert("populateRadioPatientRecords:: ergebnis = ".print_r($ergebnis,true));

	$LDSearchFound = "The search found <font color=red><b>~nr~</b></font> relevant data.";
	if ($linecount) 
		$textResult = '<hr width="80%" align="center">'.str_replace("~nr~",$totalcount,$LDSearchFound).' Showing '.$pagen->BlockStartNr().' to '.$pagen->BlockEndNr().'.';
#		echo '<hr width=80% align=left>'.str_replace("~nr~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.';
	else 
		$textResult = '<hr width="80%" align="center">'.str_replace('~nr~','0',$LDSearchFound);
#		echo str_replace('~nr~','0',$LDSearchFound); 
	$objResponse->addAssign('textResult',"innerHTML", $textResult);
#return;	
	$my_count=$pagen->BlockStartNr();
	if($ergebnis){
		$temp=0;
#$objResponse->addAlert("populateRadioPatientRecords : ergebnis = ".$ergebnis);
		while($row = $ergebnis->FetchRow() ){
#			if ($temp==0){
#				$objResponse->addAlert(" populateRadioPatientRecords : row : \n".print_r($row,true));		   		
#				$temp++;
#			}
#$objResponse->addAlert(" populateRadioPatientRecords : row : \n".print_r($row,true));		   		
			$gender = $row['sex'];	
			if ($row['request_date']!='0000-00-00')
				$date_request = @formatDate2Local($row['request_date'], $date_format);
			else
				$date_request ='';
			if ($row['service_date']!='0000-00-00')
				$date_service = @formatDate2Local($row['service_date'], $date_format);
			else
				$date_service ='yet to be served';
			
			$service_name = $row["service_name"];
			if (strlen($service_name)>40)
				$service_name = substr($row["service_name"],0,40)."...";
			$available=TRUE;
			if ($row["is_borrowed"]=='borrowed')			
				$available=FALSE;
			#$objResponse->addAlert("sub_dept_nr=".$sub_dept_nr);
			
			#note: ang mga result deri gikan sa lain nga query
			//$refRow = &$objRadio->searchLimitRadioPatientRecords($searchkey,$sub_dept_nr,$pagen->MaxCount(), $pgx, $oitem, $odir);
			//$sub_dept_name = htmlentities($row['sub_dept_id']);
			//$rw = $objRadio->getAllRadioInfoByBatch($row['batch_nr']);

			#$objResponse->addAlert("ROW if ergebnis = ".$ergebnis ." \n row =".$row."\n  date_request=".$date_request."\n name=".$name."\n gender=".$gender);
									//jsListRows(sub_dept_nr,No,refNo,name,sex,dateRequest,priority)				
$msg ="\n date_format = '".$date_format."'".
		"\n row['sex'] = '".$row['sex']."'".
		"\n row['request_date'] = '".$row['request_date']."'".
		"\n row['service_date'] = '".$row['service_date']."'".
		"\n service_name ='".$service_name."'".
		"\n date_request ='".$date_request."'".
		"\n date_service ='".$date_service."'";
#$objResponse->addAlert("populateRadioPatientRecords: $msg");		   		
			$objResponse->addScriptCall("jsRadioRequest",$sub_dept_nr,$my_count,$row['batch_nr'],
														$row['service_code'],$service_name,$row['request_doctor_name'],
														$date_request,$date_service,$available);
			$my_count++;
		}//end while loop
	//end if (ergebnis)
	}else{
		//$tr = "<tr><td colspan=\"8\" align=\"center\" bgcolor=\"#FFFFFF\" style=\"color:#FF0000; font-family:\"Arial\",Courier, mono; font-style:Bold; font-weight:Bold; font-size:12px;\">NO MATCHING REQUEST FOUND</td></tr>";
#		$tr = "<tr><td colspan=\"9\"  style=\"\">No such record exists...</td></tr>";
#		$objResponse->addAssign("TBodytab".$sub_dept_nr, "innerHTML", $tr);
		$objResponse->addScriptCall("jsRadioNoFoundRequest",$sub_dept_nr);
	}
/*
	$nextIndex = $pagen->nextIndex();
	$prevIndex = $pagen->prevIndex();
	if($pagen->csx){
		$temp = "jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr');";
		$img = '<img src="../../gui/img/common/default/prev1.png" height="20" width="94" id="prevButton" name="prevButton" onClick="setPgx('.$prevIndex.'); '.$temp.'">';
		$objResponse->addAssign("prevRow","innerHTML", $img);
	}
	if($nextIndex){
		$temp = "jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr');";
	    $img = '<img src="../../gui/img/common/default/next1.png" height="20" width="60" id="nextButton" name="nextButton" onClick="setPgx('.$nextIndex.'); '.$temp.'">';
	    $objResponse->addAssign("nextRow","innerHTML", $img);		
	}
*/
		# Previous and Next button generation
	$nextIndex = $pagen->nextIndex();
	$prevIndex = $pagen->prevIndex();
#$objResponse->addAlert("populateRadioPatientRecords : \nnextIndex='".$nextIndex."'; \nprevIndex='".$prevIndex."' \npagen->csx=".$pagen->csx."' \npagen->max_nr=".$pagen->max_nr);	
	$pageFirstOffset = 0;
	$pagePrevOffset = $prevIndex;
	$pageNextOffset = $nextIndex;		
	$pageLastOffset = $totalcount-($totalcount%$pagen->MaxCount());
	if ($pagen->csx){
		$pageFirstClass = "segSimulatedLink";
		$pageFirstOnClick = " setPgx($pageFirstOffset); jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr'); ";
		$pagePrevClass = "segSimulatedLink";
		$pagePrevOnClick = " setPgx($pagePrevOffset); jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr'); ";
	}else{
		$pageFirstClass = "segDisabledLink";
		$pagePrevClass = "segDisabledLink";
	}
	if ($nextIndex){
		$pageNextClass = "segSimulatedLink";
		$pageNextOnClick = " setPgx($pageNextOffset); jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr'); ";
		$pageLastClass = "segSimulatedLink";
		$pageLastOnClick = " setPgx($pageLastOffset); jsSortHandler('$oitem','$oitem','$odir','$sub_dept_nr'); ";
	}else{
		$pageNextClass = "segDisabledLink";
		$pageNextOffset = $pageLastOffset;		
		$pageLastClass = "segDisabledLink";
	}

	$img ='										<div id="pageFirst" class="'.$pageFirstClass.'" style="float:left" onclick="'.$pageFirstOnClick.'"> '.
			'											<img title="First" src="../../images/start.gif" border="0" align="absmiddle"/> '.
			'											<span title="First">First</span> '.
			'										</div> '.
			'										<div id="pagePrev" class="'.$pagePrevClass.'" style="float:left" onclick="'.$pagePrevOnClick.'"> '.
			'											<img title="Previous" src="../../images/previous.gif" border="0" align="absmiddle"/> '.
			'											<span title="Previous">Previous</span> '.
			'										</div> '.
			'										<div id="pageShow" style="float:left;margin-left:10px;"> '.
			'											<span>List of Service Requests</span> '.
			'										</div> '.
			'										<div id="pageLast" class="'.$pageLastClass.'" style="float:right" onclick="'.$pageLastOnClick.'"> '.
			'											<span title="Last">Last</span> '.
			'											<img title="Last" src="../../images/end.gif" border="0" align="absmiddle"/> '.
			'										</div> '.
			'										<div id="pageNext" class="'.$pageNextClass.'" style="float:right" onclick="'.$pageNextOnClick.'"> '.
			'											<span title="Next">Next</span> '.
			'											<img title="Next" src="../../images/next.gif" border="0" align="absmiddle"/> '.
			'										</div> ';
	$objResponse->addAssign("mainHead".$sub_dept_nr,"innerHTML", $img);
}// end of function populateRadioPatientRecords


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
	
	function setCharityDiscounts($ref_no,$sw_nr,$amount){
		global $db;
		$objResponse = new xajaxResponse();
		
		$grand_dte =  date('Y-m-d H:i:s');
		$ref_source = 'RD';
		
		$sql = "INSERT INTO seg_charity_amount (ref_no, ref_source, grant_dte, sw_nr, amount) ".
			 "\n VALUES('".$ref_no."', '".$ref_source."', '".$grand_dte."', '".$sw_nr."' , '".$amount."' )";
			
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