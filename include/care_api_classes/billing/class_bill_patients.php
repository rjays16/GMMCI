<?php
//---------------------------------------------------------------------
// Class for retrieving or updating the saved patients corresponds with admission date.
// Created: 05-22-2014 (Genesis D. Ortiz)
//---------------------------------------------------------------------

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');

define('SPONSORED', 'SPONSORED'); 
define('WELLBABY', 12); 

class BillPatients extends Core {
    var $memcategory = ''; 
    var $errmsg; 

	function BillPatients() {
		$this->coretable = "care_encounter";
	}

	function concatname($slast, $sfirst, $smid) {
		$stmp = "";

		if (!empty($slast)) $stmp .= $slast;
		if (!empty($sfirst)) {
			if (!empty($stmp)) $stmp .= ", ";
			$stmp .= $sfirst;
		}
		if (!empty($smid)) {
			if (!empty($stmp)) $stmp .= " ";
			$stmp .= $smid;
		}
		return($stmp);
	}

	function getDischargedPatients($filters, $offset=0, $rowcount=15, $keyword) { //added by mai
		global $db;
	
		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;

		$filter_err = '';
		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'datetoday':
						$phFilters.= "AND DATE(discharge_date) = DATE(NOW()) ";
					break;
					case 'datethisweek':
						$phFilters.= " AND YEAR(discharge_date)=YEAR(NOW()) AND WEEK(discharge_date)=WEEK(NOW()) ";
					break;
					case 'datethismonth':
						$phFilters.= " AND YEAR(discharge_date)=YEAR(NOW()) AND MONTH(discharge_date)=MONTH(NOW()) ";
					break;
					case 'date':
						$phFilters.= " AND DATE(discharge_date)=".$db->qstr($v)." ";
					break;
					case 'datebetween':
						$phFilters.= " AND discharge_date>=".$db->qstr($v[0])." AND discharge_date<=".$db->qstr($v[1])." ";
					break;
					case 'inpatient':
						$phFilters.= " AND (encounter_type = 3 OR encounter_type = 4) ";
					break;
					case 'outpatient':
						$phFilters.= " AND encounter_type = 2 ";
					break;
					case 'erpatient':
						$phFilters.= " AND encounter_type = 1 ";
					break;
					case 'mgh':
						$phFilters.= "  AND (encounter_type = 3 OR encounter_type = 4) AND is_maygohome = 1 ";
					break;
					case 'encounter_nr':
						$phFilters.= " AND encounter_nr = ".$db->qstr($keyword)." ";
					break;
					case 'pid':
						$phFilters = " AND cp.pid = ".$db->qstr($keyword)." ";
					break;
					case 'name':
						if (strpos($keyword, ",") === false) {
							$phFilters.= " AND name_last like '".trim($keyword)."%' ";
							if ( (trim($keyword) == '') || (strlen(trim($keyword)) < 2) ) $filter_err = "Specify at least 2 characters in patient's family name!";
						}
						else {
							$tmp = explode(",", $v);
							$phFilters.= " AND name_last like '".trim($tmp[0])."%' ";
							$phFilters.= " AND name_first like '".trim($tmp[1])."%' ";

							if ( (trim($tmp[0]) == '') || (strlen(trim($tmp[0])) < 2) )
								$filter_err = "Specify at least 2 characters in patient's family name!";
							else
								if ( (trim($tmp[1]) == '') || (strlen(trim($tmp[1])) < 2) ) $filter_err = "Specify at least 2 characters in patient's first name!";
						}
					break;
				}
			}
		}

		if ($filter_err != '') {
			$this->error_msg = $filter_err;
			return false;
		}

		$whereClause = " WHERE is_discharged = 1 ".$phFilters;
		
		$this->sql = "SELECT 
						  SQL_CALC_FOUND_ROWS
						  encounter_nr,
						  encounter_type,
						  ce.pid,
						  ce.current_att_dr_nr,
						  concat(discharge_date,' ', discharge_time) as encounter_date,
						  cp.name_first,
						  cp.name_middle,
						  cp.name_last,
						  cw.name AS ward_name,
						  is_maygohome,
						  fn_isPHIC(encounter_nr) isphic
						FROM
						  care_encounter ce 
						  LEFT JOIN care_person cp 
						    ON ce.pid = cp.pid
						  LEFT JOIN care_ward AS cw 
    						ON ce.current_ward_nr = cw.nr  
						$whereClause
						ORDER BY encounter_date DESC LIMIT $offset, $rowcount";
	
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }

	}

	function getSavedPatients($filters, $offset=0, $rowcount=15, $keyword) { //updated by mai
		global $db;
	
		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;

		$filter_err = '';
		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'datetoday':
						$phFilters.= "AND DATE(encounter_date) = DATE(NOW()) ";
					break;
					case 'datethisweek':
						$phFilters.= " AND YEAR(encounter_date)=YEAR(NOW()) AND WEEK(encounter_date)=WEEK(NOW()) ";
					break;
					case 'datethismonth':
						$phFilters.= " AND YEAR(encounter_date)=YEAR(NOW()) AND MONTH(encounter_date)=MONTH(NOW()) ";
					break;
					case 'date':
						$phFilters.= " AND DATE(encounter_date)=".$db->qstr($v)." ";
					break;
					case 'datebetween':
						$phFilters.= " AND encounter_date>=".$db->qstr($v[0])." AND encounter_date<=".$db->qstr($v[1])." ";
					break;
					case 'inpatient':
						$phFilters.= " AND (encounter_type = 3 OR encounter_type = 4) ";
					break;
					case 'outpatient':
						$phFilters.= " AND encounter_type = 2 ";
					break;
					case 'erpatient':
						$phFilters.= " AND encounter_type = 1 ";
					break;
					case 'mgh':
						$phFilters.= "  AND (encounter_type = 3 OR encounter_type = 4) AND is_maygohome = 1 ";
					break;
					case 'encounter_nr':
						$phFilters.= " AND encounter_nr = ".$db->qstr($keyword)." ";
					break;
					case 'pid':
						$phFilters = " AND cp.pid = ".$db->qstr($keyword)." ";
					break;
					case 'name':
						if (strpos($keyword, ",") === false) {
							$phFilters.= " AND name_last like '".trim($keyword)."%' ";
							if ( (trim($keyword) == '') || (strlen(trim($keyword)) < 2) ) $filter_err = "Specify at least 2 characters in patient's family name!";
						}
						else {
							$tmp = explode(",", $v);
							$phFilters.= " AND name_last like '".trim($tmp[0])."%' ";
							$phFilters.= " AND name_first like '".trim($tmp[1])."%' ";

							if ( (trim($tmp[0]) == '') || (strlen(trim($tmp[0])) < 2) )
								$filter_err = "Specify at least 2 characters in patient's family name!";
							else
								if ( (trim($tmp[1]) == '') || (strlen(trim($tmp[1])) < 2) ) $filter_err = "Specify at least 2 characters in patient's first name!";
						}
					break;
				}
			}
		}

		if ($filter_err != '') {
			$this->error_msg = $filter_err;
			return false;
		}

		$whereClause = " WHERE is_discharged != 1 ".$phFilters;
		
		$this->sql = "SELECT 
						  SQL_CALC_FOUND_ROWS 
						  encounter_nr,
						  encounter_type,
						  encounter_date,
						  cp.name_first,
						  cp.name_middle,
						  cp.name_last,
						  cw.name AS ward_name,
						  is_maygohome,
						  fn_isPHIC(encounter_nr) isphic
						FROM
						  care_encounter ce 
						  LEFT JOIN care_person cp 
						    ON ce.pid = cp.pid
						  LEFT JOIN care_ward AS cw 
    						ON ce.current_ward_nr = cw.nr  
						$whereClause
						ORDER BY encounter_date DESC LIMIT $offset, $rowcount";
	
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }

	}

	

function GetTypeBilling($bill_nr){
	global $db;
	$rs="";
	$strSQL = "SELECT bill_nr 
				FROM seg_billing_encounter_details
				WHERE bill_nr = '$bill_nr'";
	if($result=$db->Execute($strSQL)){
		if($result->RecordCount()){
			if($row=$result->FetchRow()){
				$rs=$row['bill_nr'];
			}
		}
	}
	return $rs;
}


function gethospitalDC($bill_nr){
	global $db;
	$rs = 0;
	$sql = "SELECT hospital_discount
			FROM seg_other_payment
			WHERE bill_nr = ".$db->qstr($bill_nr)."
			AND is_deleted != '1'";
	if($result=$db->Execute($sql)){
    	if($row=$result->FetchRow()){
    		$rs=$row['hospital_discount'];
    		}
    }
    
    return $rs;
}

}//end of class
?>