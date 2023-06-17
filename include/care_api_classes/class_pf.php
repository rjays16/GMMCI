<?php

require_once('roots.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class Pf extends Core{
	var $result;
	var $sql;
	var $data;
	var $tb_paid = "seg_pf_payments";
	var $tb_excess = "seg_pf_excess";

	function selectList($filters, $offset=0, $rowcount=15, $keyword){
		global $db;

		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;

		$filter_err = '';
		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'datetoday':
						$phFilters.= "AND DATE(ce.encounter_date) = DATE(NOW()) ";
					break;
					case 'datethisweek':
						$phFilters.= " AND YEAR(ce.encounter_date)=YEAR(NOW()) AND WEEK(ce.encounter_date)=WEEK(NOW()) ";
					break;
					case 'datethismonth':
						$phFilters.= " AND YEAR(ce.encounter_date)=YEAR(NOW()) AND MONTH(ce.encounter_date)=MONTH(NOW()) ";
					break;
					case 'date':
						$phFilters.= " AND DATE(ce.encounter_date)=".$db->qstr($v)." ";
					break;
					case 'datebetween':
						$phFilters.= " AND ce.encounter_date>=".$db->qstr($v[0])." AND ce.encounter_date<=".$db->qstr($v[1])." ";
					break;
					case 'inpatient':
						$phFilters.= " AND (ce.encounter_type = 3 OR ce.encounter_type = 4) ";
					break;
					case 'outpatient':
						$phFilters.= " AND ce.encounter_type = 2 ";
					break;
					case 'erpatient':
						$phFilters.= " AND ce.encounter_type = 1 ";
					break;
					case 'mgh':
						$phFilters.= "  AND (ce.encounter_type = 3 OR ce.encounter_type = 4) AND ce.is_maygohome = 1 ";
					break;
					case 'encounter_nr':
						$phFilters.= " AND ce.encounter_nr = ".$db->qstr($keyword)." ";
					break;
					case 'pid':
						$phFilters.= " AND cp.pid = ".$db->qstr($keyword)." ";
					break;
					case 'name':
						if (strpos($keyword, ",") === false) {
							$phFilters.= " AND cp.name_last like '".trim($keyword)."%' ";
							if ( (trim($keyword) == '') || (strlen(trim($keyword)) < 2) ) $filter_err = "Specify at least 2 characters in patient's family name!";
						}
						else {
							$tmp = explode(",", $v);
							$phFilters.= " AND cp.name_last like '".trim($tmp[0])."%' ";
							$phFilters.= " AND cp.name_first like '".trim($tmp[1])."%' ";

							if ( (trim($tmp[0]) == '') || (strlen(trim($tmp[0])) < 2) )
								$filter_err = "Specify at least 2 characters in patient's family name!";
							else
								if ( (trim($tmp[1]) == '') || (strlen(trim($tmp[1])) < 2) ) $filter_err = "Specify at least 2 characters in patient's first name!";
						}
					break;
					case 'doc_name':
						$havingFilters[] = " dr LIKE '%".$v."%' ";
					break;
					case 'paid':
						$havingFilters[] = " excess = payment ";
					break;
					case 'unpaid':
						$havingFilters[] = " (payment <= 0 AND excess > 0)";
					break;
					case 'partial':
						$havingFilters[] = " (payment > 0 && payment <> excess)";
					break;
				}
			}
		}

		if ($filter_err != '') {
			$this->error_msg = $filter_err;
			return false;
		}

		$whereClause = " WHERE (spp.is_deleted = 0 or spp.is_deleted is NULL) AND sbe.`bill_nr` IS NOT NULL AND sbe.`is_final` = 1  ".$phFilters;

		if(count($havingFilters)>0){
			$havingFilters = implode(" AND ", $havingFilters);
			$havingFilters = " HAVING ".$havingFilters;
		}
		
		$this->sql = "SELECT 
						  t.refno,
						  t.or_no,
						  t.bill_nr,
						  t.bill_frmdte,
						  t.encounter_nr,
						  t.encounter_type,
						  t.patient,
						  t.dr,
						  t.dr_nr,
						  t.excess,
						  t.payment,
						  t.role_area 
						FROM (SELECT 
		 				  spp.`refno`,
		 				  spp.`or_no`,
						  sbe.`bill_nr`,
						  sbe.`bill_frmdte`,
						  sepd.`encounter_nr`,
						  ce.`encounter_type`,
						  fn_get_person_name (ce.`pid`) patient,
						  fn_get_personell_lastname_first (sepd.`dr_nr`) dr,
						  sepd.`dr_nr`,
						 SUM(sepd.`dr_charge`) - (
						  IFNULL(
						    (SELECT 
						      SUM(dr_claim) 
						    FROM
						      seg_billing_pf 
						    WHERE bill_nr = sbe.bill_nr 
						      AND dr_nr = sepd.dr_nr),
						    0
						  ) +
						  (SELECT 
						    IFNULL(
						      SUM(
						        IFNULL(ar_discount, 0) + IFNULL(sc_discount, 0)
						      ),
						      0
						    ) 
						  FROM
						    seg_billing_other_discounts 
						  WHERE refno = sbe.`bill_nr` 
						    AND dr_nr = sepd.`dr_nr` 
						    AND bill_areas = crp.`role_area`)) excess,
						  IFNULL(spp.`amount`, 0) payment,
						  crp.`role_area` 
						FROM
						  seg_encounter_privy_dr sepd 
						  LEFT JOIN seg_billing_encounter sbe 
						    ON sbe.`encounter_nr` = sepd.`encounter_nr` 
						    AND (
						      sbe.`is_deleted` <> 1 
						      OR sbe.`is_deleted` IS NULL
						    ) 
						  LEFT JOIN care_role_person crp 
						    ON crp.`nr` = sepd.`dr_role_type_nr` 
						  LEFT JOIN care_encounter ce 
						    ON ce.`encounter_nr` = sepd.`encounter_nr` 
						  LEFT JOIN care_person cp 
						    ON cp.`pid` = ce.`pid` 
						  LEFT JOIN seg_pf_payments spp 
						    ON (
						      spp.`encounter_nr` = sepd.`encounter_nr` 
						      AND spp.`dr_nr` = sepd.`dr_nr`
						      AND (spp.`is_deleted` = 0 OR spp.`is_deleted` IS NULL)
						    ) 
						$whereClause 
						GROUP BY patient,
						  sbe.`bill_nr`,
						  sepd.`dr_nr` $havingFilters 
						  UNION 
						  SELECT 
			 				  spp.`refno`,
			 				  spp.`or_no`,
							  sbe.`bill_nr`,
							  sbe.`bill_frmdte`,
							  sepd.`encounter_nr`,
							  ce.`encounter_type`,
							  fn_get_person_name (ce.`pid`) patient,
							  fn_get_personell_lastname_first (sepd.`dr_nr`) dr,
							  sepd.`dr_nr`,
							 SUM(sepd.`dr_charge`) - (
							  IFNULL(
							    (SELECT 
							      SUM(dr_claim) 
							    FROM
							      seg_billing_pf 
							    WHERE bill_nr = sbe.bill_nr 
							      AND dr_nr = sepd.dr_nr),
							    0
							  ) +
							  (SELECT 
							    IFNULL(
							      SUM(
							        IFNULL(ar_discount, 0) + IFNULL(sc_discount, 0)
							      ),
							      0
							    ) 
							  FROM
							    seg_billing_other_discounts 
							  WHERE refno = sbe.`bill_nr` 
							    AND dr_nr = sepd.`dr_nr` 
							    AND bill_areas = 'D1')) excess,
							  IFNULL(spp.`amount`, 0) payment,
							  'D1' AS `role_area` 
							FROM
							  seg_encounter_dr_add sepd 
							  LEFT JOIN seg_billing_encounter sbe 
							    ON sbe.`encounter_nr` = sepd.`encounter_nr` 
							    AND (
							      sbe.`is_deleted` <> 1 
							      OR sbe.`is_deleted` IS NULL
							    ) 
							  LEFT JOIN care_encounter ce 
							    ON ce.`encounter_nr` = sepd.`encounter_nr` 
							  LEFT JOIN care_person cp 
							    ON cp.`pid` = ce.`pid` 
							  LEFT JOIN seg_pf_payments spp 
							    ON (
							      spp.`encounter_nr` = sepd.`encounter_nr` 
							      AND spp.`dr_nr` = sepd.`dr_nr`
							      AND (spp.`is_deleted` = 0 OR spp.`is_deleted` IS NULL)
							    ) 
							$whereClause 
							GROUP BY patient,
							  sbe.`bill_nr`,
							  sepd.`dr_nr` $havingFilters
						  ) t ORDER BY t.patient, t.dr LIMIT $offset, $rowcount ";
		
		//die($this->sql);				
		if($this->result = $db->Execute($this->sql)){
			return $this->result;
		}


		return false;

	}

	function getList($filters, $offset=0, $rowcount=15, $keyword){
		global $db;

		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;

		$filter_err = '';
		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'datetoday':
						$phFilters.= "AND DATE(sbe.bill_dte) = DATE(NOW()) ";
					break;
					case 'datethisweek':
						$phFilters.= " AND YEAR(sbe.bill_dte)=YEAR(NOW()) AND WEEK(sbe.bill_dte)=WEEK(NOW()) ";
					break;
					case 'datethismonth':
						$phFilters.= " AND YEAR(sbe.bill_dte)=YEAR(NOW()) AND MONTH(sbe.bill_dte)=MONTH(NOW()) ";
					break;
					case 'date':
						$phFilters.= " AND DATE(sbe.bill_dte)=".$db->qstr($v)." ";
					break;
					case 'datebetween':
						$phFilters.= " AND sbe.bill_dte>=".$db->qstr($v[0])." AND sbe.bill_dte<=".$db->qstr($v[1])." ";
					break;
					case 'inpatient':
						$phFilters.= " AND (ce.encounter_type = 3 OR ce.encounter_type = 4) ";
					break;
					case 'outpatient':
						$phFilters.= " AND ce.encounter_type = 2 ";
					break;
					case 'erpatient':
						$phFilters.= " AND ce.encounter_type = 1 ";
					break;
					case 'mgh':
						$phFilters.= "  AND (ce.encounter_type = 3 OR ce.encounter_type = 4) AND ce.is_maygohome = 1 ";
					break;
					case 'encounter_nr':
						$phFilters.= " AND ce.encounter_nr = ".$db->qstr($keyword)." ";
					break;
					case 'pid':
						$phFilters.= " AND cp.pid = ".$db->qstr($keyword)." ";
					break;
					case 'name':
						if (strpos($keyword, ",") === false) {
							$phFilters.= " AND cp.name_last like '".trim($keyword)."%' ";
							if ( (trim($keyword) == '') || (strlen(trim($keyword)) < 2) ) $filter_err = "Specify at least 2 characters in patient's family name!";
						}
						else {
							$tmp = explode(",", $v);
							$phFilters.= " AND cp.name_last like '".trim($tmp[0])."%' ";
							$phFilters.= " AND cp.name_first like '".trim($tmp[1])."%' ";

							if ( (trim($tmp[0]) == '') || (strlen(trim($tmp[0])) < 2) )
								$filter_err = "Specify at least 2 characters in patient's family name!";
							else
								if ( (trim($tmp[1]) == '') || (strlen(trim($tmp[1])) < 2) ) $filter_err = "Specify at least 2 characters in patient's first name!";
						}
					break;
					case 'doc_name':
						$havingFilters[] = " dr LIKE '%".$v."%' ";
					break;
				}
			}
		}

		if ($filter_err != '') {
			$this->error_msg = $filter_err;
			return false;
		}

		$whereClause = " WHERE sepd.dr_charge > 0 AND sbe.`bill_nr` IS NOT NULL AND sbe.`is_final` = 1  ".$phFilters;

		if(count($havingFilters)>0){
			$havingFilters = implode(" AND ", $havingFilters);
			$havingFilters = " HAVING ".$havingFilters;
		}
		
		$this->sql = "SELECT 
						  t.bill_nr,
						  t.bill_frmdte,
						  t.encounter_nr,
						  t.encounter_type,
						  t.patient,
						  t.dr,
						  t.dr_nr,
						  t.role_area 
						FROM (SELECT 
						  sbe.`bill_nr`,
						  sbe.`bill_frmdte`,
						  sepd.`encounter_nr`,
						  ce.`encounter_type`,
						  fn_get_person_name (ce.`pid`) patient,
						  fn_get_personell_lastname_first (sepd.`dr_nr`) dr,
						  sepd.`dr_nr`,
						  crp.`role_area` 
						FROM
						  seg_encounter_privy_dr sepd 
						  LEFT JOIN seg_billing_encounter sbe 
						    ON sbe.`encounter_nr` = sepd.`encounter_nr` 
						    AND (
						      sbe.`is_deleted` <> 1 
						      OR sbe.`is_deleted` IS NULL
						    ) 
						  LEFT JOIN care_role_person crp 
						    ON crp.`nr` = sepd.`dr_role_type_nr` 
						  LEFT JOIN care_encounter ce 
						    ON ce.`encounter_nr` = sepd.`encounter_nr` 
						  LEFT JOIN care_person cp 
						    ON cp.`pid` = ce.`pid` 
						$whereClause 
						GROUP BY patient,
						  sbe.`bill_nr`,
						  sepd.`dr_nr` $havingFilters 
						  UNION 
						  SELECT 
							  sbe.`bill_nr`,
							  sbe.`bill_frmdte`,
							  sepd.`encounter_nr`,
							  ce.`encounter_type`,
							  fn_get_person_name (ce.`pid`) patient,
							  fn_get_personell_lastname_first (sepd.`dr_nr`) dr,
							  sepd.`dr_nr`,
							  'D1' AS `role_area` 
							FROM
							  seg_encounter_dr_add sepd 
							  LEFT JOIN seg_billing_encounter sbe 
							    ON sbe.`encounter_nr` = sepd.`encounter_nr` 
							    AND (
							      sbe.`is_deleted` <> 1 
							      OR sbe.`is_deleted` IS NULL
							    ) 
							  LEFT JOIN care_encounter ce 
							    ON ce.`encounter_nr` = sepd.`encounter_nr` 
							  LEFT JOIN care_person cp 
							    ON cp.`pid` = ce.`pid` 
							$whereClause 
							GROUP BY patient,
							  sbe.`bill_nr`,
							  sepd.`dr_nr` $havingFilters
						  ) t ORDER BY t.patient, t.dr LIMIT $offset, $rowcount ";
						
		if($this->result = $db->Execute($this->sql)){
			return $this->result;
		}


		return false;

	}

	function cancelPayment(){
		global $db;

		$this->sql = "UPDATE ".$this->tb_paid." SET is_deleted = 1";

		if($db->Execute($this->sql)){
			return true;
		}

		return false;
	}

	function sumPf($encounter_nr){
		global $db;

		$this->sql = "SELECT 
						  SUM(amount) pf 
						FROM
						  seg_pf_payments 
						WHERE encounter_nr = ".$db->qstr($encounter_nr);
			
		return $db->GetOne($this->sql);

		return false;
	}

	function saveOr(){

		if($this->selectOr())
			return $this->updateOr();
		else 
			return $this->insertOr();
	}

	function getRefno(){
		global $db;

		$this->sql = "SELECT fn_get_new_refno_pf()";

		return $db->GetOne($this->sql);
	}

	function deletePf($refno){
		global $db;

		$this->sql = "UPDATE $this->tb_paid SET is_deleted = 1, ".
						"modify_id = ".$db->qstr($_SESSION['sess_user_name']).",".
						"modify_dt = NOW()".
						"WHERE refno = ".$db->qstr($refno);

		if($db->Execute($this->sql)){
			return true;
		}

		return false;
	}

	function insertOr(){
		global $db;

		$refno = $this->getRefno();

		$this->sql = "INSERT INTO $this->tb_paid (refno, encounter_nr, dr_nr, or_no, amount, notes, create_id, create_dt, modify_id, modify_dt) VALUES(".
							$db->qstr($refno).",".
							$db->qstr($this->data['encounter_nr']).",".
							$db->qstr($this->data['dr_nr']).",".
							$db->qstr($this->data['or_no']).",".
							$db->qstr($this->data['amount']).",".
							$db->qstr($this->data['notes']).",".
							$db->qstr($_SESSION['sess_user_name']).", NOW(),".
							$db->qstr($_SESSION['sess_user_name']).", NOW())";
							
		if($db->Execute($this->sql)){
			return $refno;
		}

		return false;
	}

	function updateOr(){
		global $db;

		$this->sql = "UPDATE $this->tb_paid SET 
								encounter_nr = ".$db->qstr($this->data['encounter_nr']).", 
								dr_nr = ".$db->qstr($this->data['dr_nr']).", 
								or_no = ".$db->qstr($this->data['or_no']).", 
								amount = ".$db->qstr($this->data['amount']).", 
								notes = ".$db->qstr($this->data['notes']).", 
								modify_id = ".$db->qstr($_SESSION['sess_user_name']).", 
								modify_dt = NOW()
								WHERE refno = ".$db->qstr($this->data['refno']);
							
		if($db->Execute($this->sql)){
			return $this->data['refno'];
		}

		return false;
	}

	function getOr(){
		global $db;

		$this->sql = " SELECT * FROM $this->tb_paid 
							WHERE encounter_nr = ".$db->qstr($this->data['encounter_nr']).
							" AND dr_nr=".$db->qstr($this->data['dr_nr']);

		if($this->result = $db->Execute($this->sql)){
			return $this->result;
		}

		return false;
	}

	function selectOr(){
		global $db;

		$this->sql = " SELECT * FROM $this->tb_paid 
							WHERE encounter_nr = ".$db->qstr($this->data['encounter_nr']).
							" AND dr_nr=".$db->qstr($this->data['dr_nr']).
							" AND or_no=".$db->qstr($this->data['old_or']).
							" AND is_deleted = 0";

		if($db->Execute($this->sql)->RecordCount() > 0){
			return true;
		}

		return false;
	}

	function saveExcess(){
		
		if($this->selectExcess())
			return $this->updateExcess();
		else 
			return $this->insertExcess();
	}

	function selectExcess(){
		global $db;

		$this->sql = "SELECT encounter_nr, dr_nr, amount, notes FROM $this->tb_excess 
						WHERE encounter_nr = ".$db->qstr($this->data['encounter_nr'])
						." AND dr_nr = ".$db->qstr($this->data['dr_nr']);

		$result = $db->Execute($this->sql);

		if($result){
			if($result->RecordCount()){
				return $result;
			}
		}

		return false;
	}

	function insertExcess(){
		global $db;

		$this->sql = "INSERT INTO $this->tb_excess (encounter_nr, dr_nr, amount, notes, create_id, create_dt)
									VALUES (".$db->qstr($this->data['encounter_nr']).",".$db->qstr($this->data['dr_nr'])
													.",".$db->qstr($this->data['amount']).",".$db->qstr($this->data['notes'])
													.",".$db->qstr($_SESSION['sess_user_name']).", NOW())";
		if($db->Execute($this->sql)){
			return true;
		}

		return false;

	}

	function updateExcess(){
		global $db;

		$this->sql = "UPDATE $this->tb_excess 
						SET amount = ".$db->qstr($this->data['amount'])
						.", notes = ".$db->qstr($this->data['notes'])
						.", modify_id = ".$db->qstr($_SESSION['sess_user_name'])
						.", modify_dt = NOW()"
						." WHERE encounter_nr = ".$db->qstr($this->data['encounter_nr'])
						." AND dr_nr = ".$db->qstr($this->data['dr_nr']);
		if($db->Execute($this->sql)){
			return true;
		}

		return false;
	}

	function getPfExcess($dr_nr=0, $from, $to, $senior=''){
		global $db;

		if($dr_nr){
			$where[] = " AND h.dr_nr = ".$db->qstr($dr_nr);
			$where2[] = " AND spp.dr_nr = ".$db->qstr($dr_nr);
		}

		if($senior){
			switch($senior){
	            case 'senior':
	                $where[]=" fn_get_age(DATE(sbe.`bill_dte`), DATE(cp.`date_birth`)) >= 60";
	            break;
	            case 'non-senior':
	                $where[]=" fn_get_age(DATE(sbe.`bill_dte`), DATE(cp.`date_birth`)) < 60";
	            break;
	        }
		}
		
		if($where)
			$where = implode(" AND ", $where);
		else
			$where = "";


		if($where2){
			$where2 = implode(" AND ", $where2);
		}else{
			$where2 = "";
		}

		$this->sql = "SELECT 
					  h.`encounter_nr`,
					  h.create_dt, 
					  fn_get_person_lastname_first (ce.`pid`) AS name,
					  fn_get_personell_firstname_last (h.`dr_nr`) AS doctor,
					  CONCAT(
					    DATE_FORMAT(
					      (
					        CASE
					          WHEN admission_dt IS NULL 
					          OR admission_dt = '' 
					          THEN encounter_date 
					          ELSE admission_dt 
					        END
					      ),
					      '%b %e, %Y'
					    ),
					    ' to ',
					    (
					      CASE
					        WHEN ce.discharge_date IS NULL 
					        OR ce.discharge_date = '' 
					        THEN 'present' 
					        ELSE DATE_FORMAT(
					          STR_TO_DATE(ce.discharge_date, '%Y-%m-%d'),
					          '%b %e, %Y'
					        ) 
					      END
					    )
					  ) AS confine_period,
					  h.`amount` 
					FROM
					  seg_pf_excess h 
					  LEFT JOIN care_encounter ce 
					    ON ce.`encounter_nr` = h.`encounter_nr` 
					  LEFT JOIN seg_billing_encounter sbe 
					  	ON (sbe.encounter_nr = ce.encounter_nr AND sbe.is_final = 1 and sbe.is_deleted IS NULL)
					  LEFT JOIN care_person cp 
					    ON cp.`pid` = ce.`pid` 
					WHERE h.amount > 0 AND DATE(ce.discharge_date) BETWEEN DATE(".$db->qstr($from).") 
					  AND DATE(".$db->qstr($to).") $where 
					UNION 
					SELECT
					spp.`encounter_nr`,
					spp.`create_dt`,
					fn_get_person_lastname_first (ce.`pid`) AS NAME,
					fn_get_personell_firstname_last (spp.`dr_nr`) AS doctor,
					CONCAT(
					    DATE_FORMAT(
					      (
					        CASE
					          WHEN admission_dt IS NULL 
					          OR admission_dt = '' 
					          THEN encounter_date 
					          ELSE admission_dt 
					        END
					      ),
					      '%b %e, %Y'
					    ),
					    ' to ',
					    (
					      CASE
					        WHEN ce.discharge_date IS NULL 
					        OR ce.discharge_date = '' 
					        THEN 'present' 
					        ELSE DATE_FORMAT(
					          STR_TO_DATE(ce.discharge_date, '%Y-%m-%d'),
					          '%b %e, %Y'
					        ) 
					      END
					    )
					  ) AS confine_period,
					  spp.`amount`
					FROM
					seg_pf_payments spp
					LEFT JOIN care_encounter ce 
					    ON ce.`encounter_nr` = spp.`encounter_nr` 
					  LEFT JOIN seg_billing_encounter sbe 
					    ON (
					      sbe.encounter_nr = ce.encounter_nr 
					      AND sbe.is_final = 1 
					      AND sbe.is_deleted IS NULL
					    ) 
					 LEFT JOIN care_person cp 
					    ON cp.`pid` = ce.`pid` 
					WHERE spp.amount > 0 AND DATE(ce.discharge_date) BETWEEN DATE(".$db->qstr($from).") 
					  AND DATE(".$db->qstr($to).") $where2  
					ORDER BY name, doctor ASC ";

		//die($this->sql);
		if($this->result = $db->Execute($this->sql)){
			return $this->result;
		}

		return false;
	}
	function getMonthlyPfExcess($dr_nr=0, $from, $to, $senior=''){
		global $db;

		if($dr_nr){
			$where[] = " AND h.dr_nr = ".$db->qstr($dr_nr);
			$where2[] = " AND spp.dr_nr = ".$db->qstr($dr_nr);
		}

		if($senior){
			switch($senior){
	            case 'senior':
	                $where[]=" fn_get_age(DATE(sbe.`bill_dte`), DATE(cp.`date_birth`)) >= 60";
	            break;
	            case 'non-senior':
	                $where[]=" fn_get_age(DATE(sbe.`bill_dte`), DATE(cp.`date_birth`)) < 60";
	            break;
	        }
		}
		
		if($where)
			$where = implode(" AND ", $where);
		else
			$where = "";


		if($where2){
			$where2 = implode(" AND ", $where2);
		}else{
			$where2 = "";
		}

		$this->sql = "SELECT 
					  h.`encounter_nr`,
					  h.create_dt, 
					  fn_get_person_lastname_first (ce.`pid`) AS name,
					  fn_get_personell_firstname_last (h.`dr_nr`) AS doctor,
					  CONCAT(
					    DATE_FORMAT(
					      (
					        CASE
					          WHEN admission_dt IS NULL 
					          OR admission_dt = '' 
					          THEN encounter_date 
					          ELSE admission_dt 
					        END
					      ),
					      '%b %e, %Y'
					    ),
					    ' to ',
					    (
					      CASE
					        WHEN ce.discharge_date IS NULL 
					        OR ce.discharge_date = '' 
					        THEN 'present' 
					        ELSE DATE_FORMAT(
					          STR_TO_DATE(ce.discharge_date, '%Y-%m-%d'),
					          '%b %e, %Y'
					        ) 
					      END
					    )
					  ) AS confine_period,
					  h.`amount` 
					FROM
					  seg_pf_excess h 
					  LEFT JOIN care_encounter ce 
					    ON ce.`encounter_nr` = h.`encounter_nr` 
					  LEFT JOIN seg_billing_encounter sbe 
					  	ON (sbe.encounter_nr = ce.encounter_nr AND sbe.is_final = 1 and sbe.is_deleted IS NULL)
					  LEFT JOIN care_person cp 
					    ON cp.`pid` = ce.`pid` 
					WHERE h.amount > 0 AND DATE(ce.admission_dt) BETWEEN DATE(".$db->qstr($from).") 
					  AND DATE(".$db->qstr($to).") $where 
					UNION 
					SELECT
					spp.`encounter_nr`,
					spp.`create_dt`,
					fn_get_person_lastname_first (ce.`pid`) AS NAME,
					fn_get_personell_firstname_last (spp.`dr_nr`) AS doctor,
					CONCAT(
					    DATE_FORMAT(
					      (
					        CASE
					          WHEN admission_dt IS NULL 
					          OR admission_dt = '' 
					          THEN encounter_date 
					          ELSE admission_dt 
					        END
					      ),
					      '%b %e, %Y'
					    ),
					    ' to ',
					    (
					      CASE
					        WHEN ce.discharge_date IS NULL 
					        OR ce.discharge_date = '' 
					        THEN 'present' 
					        ELSE DATE_FORMAT(
					          STR_TO_DATE(ce.discharge_date, '%Y-%m-%d'),
					          '%b %e, %Y'
					        ) 
					      END
					    )
					  ) AS confine_period,
					  spp.`amount`
					FROM
					seg_pf_payments spp
					LEFT JOIN care_encounter ce 
					    ON ce.`encounter_nr` = spp.`encounter_nr` 
					  LEFT JOIN seg_billing_encounter sbe 
					    ON (
					      sbe.encounter_nr = ce.encounter_nr 
					      AND sbe.is_final = 1 
					      AND sbe.is_deleted IS NULL
					    ) 
					 LEFT JOIN care_person cp 
					    ON cp.`pid` = ce.`pid` 
					WHERE spp.amount > 0 AND DATE(ce.admission_dt) BETWEEN DATE(".$db->qstr($from).") 
					  AND DATE(".$db->qstr($to).") $where2  
					ORDER BY name, doctor ASC ";

		//die($this->sql);
		if($this->result = $db->Execute($this->sql)){
			return $this->result;
		}

		return false;
	}

}

?>