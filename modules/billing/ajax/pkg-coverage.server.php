<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/billing/ajax/pkg-coverage.common.php');

function showOverrideFlag($ref_no, $pkg_id) {
    global $db;
    
    $objResponse = new xajaxResponse();    
    $strSQL = "SELECT 
                  is_freedist  
                FROM
                  seg_billing_pkg sbp 
                WHERE sbp.ref_no = '$ref_no' 
                  AND sbp.package_id = $pkg_id";
    $row = $db->GetRow($strSQL);
    $objResponse->call("setOverrideFlag", (is_null($row['is_freedist']) ? "0" : $row['is_freedist']));
    return $objResponse;
}

function saveCoverage($ref_no, $data, $pkg_id=0, $isfreedist='0') {
		global $db;
		$objResponse = new xajaxResponse();

		$db->StartTrans();

//  switch ($mode) {
//    case "M": $source = array('M'); break;
//    default: $source = array('R','L','O','E','S'); break;
//  }

		$sql = "DELETE FROM seg_applied_pkgcoverage WHERE ref_no=".$db->qstr($ref_no);
		$saveok = $db->Execute($sql);

		if ($saveok && ($pkg_id != 0)) {
				$sql = "delete from seg_billing_pkg where ref_no = ".$db->qstr($ref_no);
				$saveok = $db->Execute($sql);
		}

		if ($saveok) {
			 if (!empty($data)) {
					$sql = "INSERT INTO seg_applied_pkgcoverage(ref_no, bill_area, hcare_id, coverage, priority) ".
								 "VALUES(".$db->qstr($ref_no).",?,?,?,?)";
					$saveok = $db->Execute( $sql, $data );
			 }

			 if ($saveok && ($pkg_id != 0)) {
					$sql = "insert into seg_billing_pkg (ref_no, package_id, is_freedist) ".
								 "   values(".$db->qstr($ref_no).", {$pkg_id}, {$isfreedist})";
					$saveok = $db->Execute( $sql );
			 }
		}

		if ($saveok) {
				$db->CompleteTrans();
				$objResponse->alert('Package coverage distribution saved successfully!');
				$objResponse->call("assignPkgID");
		}
		else {
				$db->FailTrans();
				$db->CompleteTrans();
				$objResponse->alert('Error: '.$db->ErrorMsg()."\n$sql");
		}
		return $objResponse;
}

$xajax->processRequest();


?>