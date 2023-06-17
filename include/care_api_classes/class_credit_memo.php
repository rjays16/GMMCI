<?php

// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require("./roots.php");	
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/sponsor/class_request.php');
require_once($root_path.'include/care_api_classes/class_cashier.php');

class SegCreditMemo extends Core {
	
	var $target;
	var $items_tb;
	var $discounts_tb;
	var $prod_tb;
	var $memo_tb = "seg_credit_memos";
	var $memo_details_tb = "seg_credit_memo_details";

	function SegCreditMemo() {
		global $db;		
		$this->fld_memo = $db->MetaColumnNames($this->memo_tb);
		$this->fld_memo_details = $db->MetaColumnNames($this->memo_details_tb);
		$this->useMemo();
	}
	
	function useMemo() {
		$this->coretable = $this->memo_tb;
		$this->setRefArray($this->fld_memo);
	}
	
	function useMemoDetails() {
		$this->coretable = $this->memo_details_tb;
		$this->setRefArray($this->fld_memo_details);
	}
	
	function getLastNr($today) {
		global $db;
		$this->useMemo();
		$today = $db->qstr($today);
		$this->sql="SELECT IFNULL(MAX(CAST(memo_nr AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->coretable WHERE SUBSTRING(memo_nr,1,4)=EXTRACT(YEAR FROM NOW())";
		return $db->GetOne($this->sql);
	}
	
	function deleteMemo($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->useMemo();
		$this->sql = "DELETE FROM $this->coretable WHERE memo_nr=$nr";
        return $this->Transact();
	}
	
	function getORDetailsForCM($orno, $memoNr=NULL) {
		global $db;
		$orno = $db->qstr($orno);
		$this->useMemo();
		if ($memoNr) $prevWhere = " AND cd.memo_nr!=".$db->qstr($memoNr);
        #edited by VAS 09-10-2012
        #added line 65
        #AND ((SELECT serve_status FROM seg_pharma_order_items AS ro WHERE ro.refno=r.ref_no AND ro.bestellnum=r.service_code)='S')
        #should check if medicines were served before checking for returned items
		$this->sql = "
SELECT 
	r.ref_no,r.service_code,r.ref_source,
	IFNULL(CASE (r.ref_source)
		WHEN 'PH' 
            AND ((SELECT serve_status FROM seg_pharma_order_items AS ro WHERE ro.refno=r.ref_no AND ro.bestellnum=r.service_code)='S')
        THEN
			(SELECT SUM(quantity) FROM seg_pharma_return_items AS ri WHERE ri.ref_no=r.ref_no AND ri.bestellnum=r.service_code)
		/*added by Dommie 4/30/14 should check if laboratory were served before checking for returned item*/
		WHEN 'LD'
			AND ((SELECT is_served FROM	seg_lab_servdetails AS seg_l_s_d WHERE seg_l_s_d.refno=r.ref_no AND seg_l_s_d.service_code=r.service_code)=1)
		THEN
			(SELECT SUM(quantity) FROM seg_lab_servdetails AS seg_l_s_d WHERE seg_l_s_d.refno=r.ref_no AND seg_l_s_d.service_code=r.service_code)
		/*end Dommie*/
		ELSE r.qty
	END,0) AS quantity,
	r.amount_due,
	/*Dommie: Modified: add discount*/
	r.amount_due/r.qty-(seg_p.discount_tendered/COUNT(r.or_no)) AS price,
	/*end Dommie*/
	IFNULL((SELECT SUM(quantity) FROM seg_credit_memo_details AS cd WHERE cd.or_no=r.or_no AND cd.ref_source=r.ref_source AND cd.ref_no=r.ref_no AND cd.service_code=r.service_code $prevWhere),0) AS refunded,
	CASE (r.ref_source)
		WHEN 'LD' THEN
			(SELECT CONCAT(ls.name,'\\n',lg.name) 
				FROM seg_lab_services AS ls
					LEFT JOIN seg_lab_service_groups AS lg ON lg.group_code=ls.group_code
				WHERE ls.service_code=r.service_code)
		WHEN 'RD' THEN
			(SELECT CONCAT(rs.name,'\\n',rg.name) 
				FROM seg_radio_services AS rs
					LEFT JOIN seg_radio_service_groups AS rg ON rg.group_code=rs.group_code
				WHERE rs.service_code=r.service_code)
		WHEN 'PH' THEN
			(SELECT CONCAT(p.artikelname,'\\n',CAST(IF(p.prod_class='M','Medicine','Supply') AS BINARY))
				FROM care_pharma_products_main AS p
				WHERE p.bestellnum=r.service_code)
		WHEN 'FB' THEN
			CONCAT('Final Billing','\\n','Payward')
		WHEN 'PP' THEN
			IF(r.service_code='PARTIAL',CAST(CONCAT('Partial payment','\\n','Deposit') AS BINARY),
				IF(r.service_code='DEPOSIT',CAST(CONCAT('Deposit:Hospital Fees','\\n','Deposit') AS BINARY),
					(CASE (UPPER(SUBSTRING(r.service_code,1,1)))
						WHEN 'O' THEN
							(SELECT CONCAT(o.description,' (',o.code,')','\\n','Deposit') 
								FROM care_ops301_en AS o WHERE o.code=SUBSTRING(r.service_code,2))
						WHEN 'L' THEN
							(SELECT CONCAT(ls.name,' (',lg.name,')','\\n','Deposit')
								FROM seg_lab_services AS ls
								LEFT JOIN seg_lab_service_groups AS lg ON lg.group_code=ls.group_code
								WHERE ls.service_code=SUBSTRING(r.service_code,2))
						WHEN 'R' THEN
							(SELECT CONCAT(rs.name,' (',rg.name,')','\\n','Deposit')
								FROM seg_radio_services AS rs
								LEFT JOIN seg_radio_service_groups AS rg ON rg.group_code=rs.group_code
								WHERE rs.service_code=SUBSTRING(r.service_code,2))
						ELSE 'Unknown service'
					END)
				)
			)
		WHEN 'OTHER' THEN
			(SELECT CONCAT(os.name,'\\n',og.name_long)
				FROM seg_other_services AS os
				LEFT JOIN seg_cashier_account_subtypes AS og ON og.type_id=os.account_type
				WHERE os.service_code=SUBSTRING(r.service_code,1,LENGTH(r.service_code)-1))
		ELSE
			CONCAT('Unknown item','\\n',r.service_code)
	END AS name_group
FROM seg_pay_request AS r
/*Added by Dommie 4/30/14*/ 
LEFT JOIN seg_pay AS seg_p
ON seg_p.or_no = r.or_no
/*end Dommie*/
WHERE r.or_no=$orno
ORDER BY ref_source
";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}
	
	function addMemoItems($nr, $itemArray) {
		global $db;
		$nr = $db->qstr($nr);
		$this->useMemoDetails();
		$this->sql = "INSERT INTO seg_credit_memo_details(memo_nr,or_no,ref_source,ref_no,service_code,service_name,service_desc,quantity,price) VALUES($nr,?,?,?,?,?,?,?,?)";
		if($buf=$db->Execute($this->sql,$itemArray)) {
			if($buf->RecordCount()) { 
                #added by VAS 09-10-2012
                #$this->FlagItems($itemArray);
                
                return true;
			} else { return false; }
		} else { return false; }
	}
	
	function clearMemoItems($nr) {
		global $db;
		$this->useMemoDetails();
		$nr = $db->qstr($nr);
		$this->sql = "DELETE FROM seg_credit_memo_details WHERE memo_nr=$nr";
    return $this->Transact();
	}

	function getMemoInfo($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->useMemo();
		$this->sql = "SELECT c.memo_nr,c.issue_date,c.memo_name,c.memo_address,c.pid,c.encounter_nr,u.name AS `personnel_name`,c.personnel,c.refund_amount,c.remarks FROM $this->coretable AS c 
LEFT JOIN care_users AS u ON u.login_id=c.personnel WHERE c.memo_nr=$nr";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result->FetchRow();
		} else { return false; }
	}
	
	function getMemoDetails($nr) {
		global $db;
		if ($nr) $prevWhere = " AND cmx.memo_nr!=".$db->qstr($nr);
		$nr = $db->qstr($nr);
		$this->useMemoDetails();
		$this->sql = "SELECT cm.memo_nr,cm.or_no,cm.ref_source,cm.ref_no,cm.service_code,cm.service_name,cm.service_desc,cm.quantity AS refund,cm.price,
IFNULL(CASE (cm.ref_source)
	WHEN 'PH' THEN
		(SELECT SUM(quantity) FROM seg_pharma_return_items AS ri WHERE ri.ref_no=cm.ref_no AND ri.bestellnum=cm.service_code)
	ELSE (SELECT qty FROM seg_pay_request AS pr WHERE pr.or_no=cm.or_no AND pr.ref_source=cm.ref_source AND pr.ref_no=cm.ref_no AND pr.service_code=cm.service_code)
END,0) AS quantity,
(SELECT SUM(quantity) FROM seg_credit_memo_details AS cmx WHERE cmx.or_no=cm.or_no AND cmx.ref_source=cm.ref_source AND cmx.ref_no=cm.ref_no AND cmx.service_code=cm.service_code $prevWhere) AS previous
FROM seg_credit_memo_details AS cm
WHERE cm.memo_nr=$nr";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function get($filters, $offset=0, $rowcount=15) {
    global $db;
		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;
		
		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'nr':
						$phFilters[] = "cm.memo_nr=".$db->qstr($v);
					break;
					case 'name':
						$wFilters[] = "cm.memo_name REGEXP '[[:<:]]".substr($db->qstr($v),1);
					break;
					case 'pid':
						$wFilters[] = "cm.pid REGEXP ".$db->qstr($v);
					break;
					case 'patient':
						$wFilters[] = "cm.pid=".$db->qstr($v);
					break;
					case 'inpatient':
						$wFilters[] = "cm.encounter_nr=".$db->qstr($v);
					break;
					case 'datetoday':
						$phFilters[] = 'DATE(cm.issue_date)=DATE(NOW())';
					break;
					case 'datethisweek':
						$phFilters[] = 'YEAR(cm.issue_date)=YEAR(NOW()) AND WEEK(cm.issue_date)=WEEK(NOW())';
					break;
					break;
					case 'datethismonth':
						$phFilters[] = 'YEAR(cm.issue_date)=YEAR(NOW()) AND MONTH(cm.issue_date)=MONTH(NOW())';
					break;
					case 'date':
						$phFilters[] = "DATE(cm.issue_date)='$v'";
					break;
					case 'datebetween':
						$phFilters[] = "cm.issue_date BETWEEN ".$db->qstr($v[0])." AND ".$db->qstr($v[1]);
					break;
					case 'source':
						$phFilters[] = "EXISTS(SELECT * FROM seg_credit_memo_details AS cd WHERE cd.ref_source=".$db->qstr($v)." AND cd.memo_nr=cm.memo_nr)";
					break;
					case 'personnel':
						$phFilters[] = "personnel=".$db->qstr($v);
					break;
				}
			}
		}
		
		$phWhere=implode(") AND (",$phFilters);
		if ($phWhere) $phWhere = "($phWhere)";
		else $phWhere = "1";
		
		$this->sql="SELECT SQL_CALC_FOUND_ROWS
cm.memo_nr,cm.issue_date,cm.memo_name,cm.memo_address,cm.pid,cm.encounter_nr,cm.remarks,cm.refund_amount,cm.personnel,
(SELECT GROUP_CONCAT(CONCAT(d.service_name,' (',d.service_desc,')') SEPARATOR '\\n') FROM seg_credit_memo_details AS d
WHERE d.memo_nr=cm.memo_nr) AS `items`
FROM seg_credit_memos AS cm
WHERE ($phWhere)\n";
		$this->sql .= "ORDER BY cm.issue_date DESC\n" . 
			"LIMIT $offset, $rowcount";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;	 
		} else { return false; }
	}
	
    /**
    * Unflag request at the COST CENTERS
    *
    * @author Vanessa A. Saren (09-10-2012)
    */
    function FlagItems($itemArray){
        global $db;
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        
        if (!is_array($itemArray)) $itemArray = array($itemArray);

        $by = $db->qstr($_SESSION['sess_temp_userid']);
        $saveOK = false;
        
        for ($i=0; $i<sizeof($itemArray);$i++){
            #payment Items
            $orno = $itemArray[$i][0]; 
            $service_code = $itemArray[$i][3]; 
            $ref_source = $itemArray[$i][1];
            $refno = $itemArray[$i][2]; 
           
            switch (strtoupper($ref_source)) {
                case 'PH': $type=SegRequest::PHARMACY_REQUEST; break;
                case 'LD': $type=SegRequest::LABORATORY_REQUEST; break;
                case 'RD': $type=SegRequest::RADIOLOGY_REQUEST; break;
                case 'FB': $type=SegRequest::BILLING_REQUEST; break;
                case 'MISC': $type=SegRequest::MISC_REQUEST; break;
            }
            
            // for Cost Centers
            if ($type) {
                $request = new SegRequest( $type, array('refNo'=>$refno, 'itemNo'=>$service_code));
                $saveOK=$request->flag(null);
                if (!$saveOK) {
                    $this->setErrorMsg('Unable to unflag request...');
                    break;
                }
            }
        }
        return $saveOK;    
    }

}

?>