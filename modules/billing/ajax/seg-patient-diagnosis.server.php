<?php
	function populateFinalDiagnosisList($encounter_nr, $sElem, $page) {
		global $db;
		
		$objResponse = new xajaxResponse();		
		
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];										       
		
		$offset = $page * $maxRows;
		$searchkey = utf8_decode($searchkey);
		$objbill = new BillInfo();
		$ergebnis = $objbill->getDiagnosisList($encounter_nr, $maxRows, $offset);		
		
		$total = $objbill->rec_count;        
		
		$lastPage = floor($total/$maxRows);
		
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;
		
		if ($page > $lastPage) $page=$lastPage;       
		$rows=0;		

		$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->call("clearList","DiagnosisList");
		if ($ergebnis) {			
			$rows=$ergebnis->RecordCount();			
			while($result=$ergebnis->FetchRow()) {																										
				$objResponse->call("addDiagnosisToList","DiagnosisList",$result["entry_no"],$result["description"]);
			}#end of while
		} #end of if

		if (!$rows) $objResponse->call("addDiagnosisToList","DiagnosisList",NULL);
		if ($sElem) {
			$objResponse->call("endAJAXSearch",$sElem);
		}
		
		return $objResponse;
	}
			
	function addDiagnosis($enc_nr, $diagdesc, $create_id) {
		$objResponse = new xajaxResponse(); 
		
		$objbill = new BillInfo();
		$bSuccess = $objbill->addDiagnosis($enc_nr, $diagdesc, 0, $create_id);
		if (!$bSuccess) {            
			$objResponse->alert("ERROR in saving diagnosis!\n".$objbill->getErrorMsg());   
		}
									
		return $objResponse;   
	}
	
	function updateDiagnosis($enc_nr, $entry_no, $diagdesc, $user_id) {  
		$objResponse = new xajaxResponse(); 
		
		$objbill = new BillInfo();
		$bSuccess = $objbill->updateDiagnosis($enc_nr, $entry_no, $diagdesc, $user_id);
		if (!$bSuccess) {            
			$objResponse->alert("ERROR in updating description of diagnosis!\n".$objbill->getErrorMsg());   
		}
									
		return $objResponse;  
	}
	
	function remDiagnosis($enc_nr, $entry_no, $create_id) {
		$objResponse = new xajaxResponse(); 
		
		$objbill = new BillInfo();
		$bSuccess = $objbill->delDiagnosis($enc_nr, $entry_no, $create_id);
		if ($bSuccess) {
			$objResponse->call("removeDiagnosisInList",$entry_no);  
			$objResponse->alert("Diagnosis has been successfully deleted");              
		}
		else {            
			$objResponse->alert("ERROR in deleting diagnosis!\n".$objbill->sql);   
		}						             
				
		return $objResponse;		        
	}
	
	require('roots.php');
	require($root_path.'include/inc_environment_global.php');        
	require_once($root_path.'include/care_api_classes/class_globalconfig.php'); 
	require_once($root_path."include/care_api_classes/billing/class_bill_info.php");
	require_once($root_path."modules/billing/ajax/seg-patient-diagnosis.common.php");
	$xajax->processRequest();    
?>