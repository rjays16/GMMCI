<?php
function populateAppliedOpsList($senc_nr, $sElem, $searchkey, $page, $b_drchrg, $dr_nr=0){
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);

		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		$srv=new SegOps;
		#$objResponse->addAlert('page = '.$page);
		$offset = $page * $maxRows;
		$searchkey = utf8_decode($searchkey);
		$total_srv = $srv->countAppliedOP($senc_nr,$searchkey,$maxRows,$offset,(strcmp($b_drchrg, 'true') == 0 ? 1 : 0), $dr_nr);
		
		#$objResponse->addAlert($srv->sql);        
		$total = $srv->count;
				
		$lastPage = floor($total/$maxRows);
		
		if ((floor($total%$maxRows))==0)
				$lastPage = $lastPage-1;
		
		if ($page > $lastPage) $page=$lastPage;                
		$ergebnis=$srv->SearchAppliedOP($senc_nr,$searchkey,$maxRows,$offset,(strcmp($b_drchrg, 'true') == 0 ? 1 : 0), $dr_nr);
						
		//$objResponse->addAlert("sql = ".$srv->sql);
		
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","procedure-list");
				
		if ($ergebnis) {
				$rows=$ergebnis->RecordCount();
				while($result=$ergebnis->FetchRow()) {
						$description_short = $result["description"];
						if (strlen($description_short)>50)
								$description_short = substr(trim($result["description"]),0,50)."...";

						$bselected = $result["bselected"];
//            if (isset($_SESSION['opstaken'])) {                                
//                $bselected = (isset($_SESSION['opstaken'][$result["refno"].';'.$result["entry_no"].';'.$result["ops_code"]]) ? 1 : 0);
//            }                        
						
						$objResponse->addScriptCall("addAppliedOPtoList","procedure-list",trim($result["ops_code"]),trim($description_short),trim($result["description"]),$result["rvu"],$result["multiplier"],$bselected,$result["entry_no"],$result["refno"],(strcmp($b_drchrg, 'true') == 0 ? 1 : 0));
				}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addAppliedOPtoList","procedure-list",NULL);
		if ($sElem) {
				$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}               
		
		return $objResponse;
}
		
require('./roots.php');

require($root_path.'include/inc_environment_global.php');    

require_once($root_path.'include/care_api_classes/billing/class_ops.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

require($root_path."modules/or/ajax/or-select-ops.common.php");
$xajax->processRequests();      
?>
