<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'modules/billing_new/ajax/billing-dialysis.common.php');
require_once($root_path.'include/care_api_classes/class_dialysis_billing.php');
require_once($root_path.'include/care_api_classes/billing/class_ops_new.php');
require_once($root_path.'include/care_api_classes/class_caserate_icd_icp.php');

function getSessions($id){
	$objResponse = new xajaxResponse();
	$objDia = new Dialysis_billing();
	$objOps = new SegOps();
	$objIcd = new Icd_Icp();

	$objDia->details = array("id"=>$id);
	$res_sessions=$objDia->getSessions();

	$objResponse->call("clearData");
	
	if($res_sessions){
		//empty table
		while($row = $res_sessions->FetchRow()){
			$procedures = array();
			$diagnosis = array();

			//procedures
			$res_ops = $objOps->SearchCurrentOP($row['encounter_nr'], $row['case_date'], $row['bill_dte']);
			if($res_ops){
				while($row1=$res_ops->FetchRow()){
					$procedures[] = $row1['code'];
				}
			}

			//diagnosis
			$res_icd = $objIcd->searchIcd($row['encounter_nr']);
			if($res_icd){
				while($row2=$res_icd->FetchRow()){
					$diagnosis[] = $row2['code'];
				}
			}
			
			$obj->no = $row['entry_no']+1;
			$obj->enc_nr = $row['encounter_nr'];
			$obj->enc_dte = $row['case_date'];
			$obj->bill_nr = $row['bill_nr'];
			$obj->bill_dte = $row['bill_dte'];
			$obj->proc = implode(", ",$procedures);
			$obj->diag = implode(", ",$diagnosis);
			$obj->enc_type = $row['type'];

			$objResponse->call("appendSessions", $obj);
		}
	}else{
		$objResponse->alert("error fetching data");
	}

	return $objResponse;
}

function getEncData($bill_nr){
	$objResponse = new xajaxResponse();
	$objDia = new Dialysis_billing();
	$objOps = new SegOps();
	$objIcd = new Icd_Icp();

	$objDia->details = array("bill_nr"=>$bill_nr);
	$row=$objDia->getThisBilling();

	if($row){
		$procedures = array();
		$diagnosis = array();
		
		//procedures
		$res_ops = $objOps->SearchCurrentOP($row['encounter_nr'], $row['case_date'], $row['bill_dte']);
		if($res_ops){
			while($row1=$res_ops->FetchRow()){
				$procedures[] = $row1['code'];
			}
		}

		//diagnosis
		$res_icd = $objIcd->searchIcd($row['encounter_nr']);
		if($res_icd){
			while($row2=$res_icd->FetchRow()){
				$diagnosis[] = $row2['code'];
			}
		}

		$obj->no = 0;
		$obj->enc_nr = $row['encounter_nr'];
		$obj->enc_dte = $row['case_date'];
		$obj->bill_nr = $row['bill_nr'];
		$obj->bill_dte = $row['bill_dte'];
		$obj->proc = implode(", ",$procedures);
		$obj->diag = implode(", ",$diagnosis);
		$obj->enc_type = $row['type'];

		$objResponse->call("appendSessions", $obj);
	}else{
		$objResponse->call("alertNodata");
	}

	return $objResponse;
}

function updateCycle($details){
	$objResponse = new xajaxResponse();
	$objDia = new Dialysis_billing();

	if(!checkTransmittal($details)){
		$objDia->details = array("id"=>$details['id'],
								"trans_flag"=>$details['trans_flag'],
								"history"=>$details['history']);

		if($objDia->updateCycle()){
			$objResponse->call("pageReload", $details['id']);
		}else{
			$objResponse->alert("Error saving data.");
		}
	}else{
		$objResponse->alert("Cannot Execute Transaction. This has already a transmittal");
	}
	
	return $objResponse;
}

function saveCycle($data, $details){
	$objResponse = new xajaxResponse();
	$objDia = new Dialysis_billing();

	$objDia->details = array("id"=>$details['id'],
							"trans_flag"=>$details['trans_flag'],
							"history"=>$details['history'],
							"pid"=>$details['pid']);
	$id = $objDia->saveDia($data);

	if($id){
		$objResponse->call("pageReload", $id);
	}else{
		$objResponse->alert("Unable to save data.");
	}

	return $objResponse;
}

function checkTransmittal($details){
	$objDia = new Dialysis_billing();

	$objDia->details = array("encounter_nr"=>$details['enc_nr']);
	return $objDia->hasTransmittal();
}

$xajax->processRequest();

?>