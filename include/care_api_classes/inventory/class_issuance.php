<?php
require_once('roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/class_pharma_product.php');
require_once($root_path.'include/care_api_classes/inventory/class_inventory.php');
include_once($root_path . "include/care_api_classes/inventory/class_inventory_helper.php");

class Issuance extends Core {
    #created by bryan
    /**
    * @var String
    */    
    var $tb_issue = "seg_issuance";
    /**
    * @var String
    */    
    var $tb_issue_details = "seg_issuance_details";
    
    var $tb_req_served = "seg_requests_served"; 
    /**
    * @var String holds the sql queries for the class
    */    
    var $sql;
    /**
    * @var String holds the result of the queries for the class
    */    
    var $result;
    /**
    * @var String
    */    
    var $refno;
    /**
    * @var date
    */    
    var $issue_date;
    /**
    * @var string
    */    
    var $dept_issued;
    /**
    * @var string
    */    
    var $authorizing_id;
    /**
    * @var String
    */    
    var $issuing_id;
    /**
    * @var issuedItem[]
    */    
    var $issued_items = array();
    /**
    * @var array   For internal update/insert operations
    */
    var $data_array = array();
    /**
    * @var array   For internal update/insert operations
    */
    var $buffer_array = array();
    
    var $fld_issue = array(
        "refno",
        "issue_date",
        "src_area_code",
        "area_code",
        "authorizing_id",
        "issuing_id",
        "issue_type",
        "acknowledging_id",
        "acknowledge_date",
        "status"
    ); 
    
    var $fld_issue_details = array(
        "refno",
        "item_code",
        "serial_no",
        "expiry_date",
        "item_qty",
        "unit_id",
        "avg_cost",
        "is_unitperpc",
        "is_acknowledged",
        "status"
    ); 
    
    function prepareIssuance() {
        $this->coretable = $this->tb_issue;
        $this->setRefArray($this->fld_issue);
    }
    
    function prepareIssuanceDetails() {
        $this->coretable = $this->tb_issue_details;
        $this->setRefArray($this->fld_issue_details);
    }
    
    #added by Bryan on Nov 18, 2008
    function getIssuancebyArea($area){
        
        global $db;
        
        $this->sql = "SELECT * FROM $this->tb_issue WHERE area_code ='$area'";
        //echo "sulod ko sa method<br>";
        //echo $this->sql."<br>";
        $this->result = $db->Execute($this->sql);

        return $this->result;

    }
    
    #added by Bryan on Nov 18, 2008
    function getIssuancebyDepartment($dept, $filters=NULL){
        global $db;

        if (is_array($filters)) {
            foreach ($filters as $i=>$v) {
                switch (strtolower($i)) {
                    case 'datetoday':
                        $phFilters[] = 'DATE(a.issue_date)=DATE(NOW())';
                        break;
                    case 'datethisweek':
                        $phFilters[] = 'YEAR(a.issue_date)=YEAR(NOW()) AND WEEK(a.issue_date)=WEEK(NOW())';
                        break;
                    case 'datethismonth':
                        $phFilters[] = 'YEAR(a.issue_date)=YEAR(NOW()) AND MONTH(a.issue_date)=MONTH(NOW())';
                        break;
                    case 'date':
                        $phFilters[] = "DATE(a.issue_date)='$v'";
                        break;
                    case 'datebetween':
                        $phFilters[] = "DATE(a.issue_date)>='".$v[0]."' AND DATE(a.issue_date)<='".$v[1]."'";
                        break;
                }
            }
        }

        if($phFilters){
            $phWhere = " AND ".implode(") AND (",$phFilters);
        }else{
            $phWhere = " AND DATE(a.issue_date) = DATE(NOW())";
        }

        if (!(empty($dept)) && $dept != "''")
        $this->sql = "select * from $this->tb_issue as a 
                            JOIN seg_areas as b ON a.area_code=b.area_code 
                            JOIN $this->tb_issue_details as c ON a.refno=c.refno
                            where (b.dept_nr in ($dept) AND c.status=0 ".$phWhere.") order by a.issue_date";
        else   
        $this->sql = "select * from $this->tb_issue as a 
                            JOIN seg_areas as b ON a.area_code=b.area_code 
                            JOIN $this->tb_issue_details as c ON a.refno=c.refno
                            where (c.status=0 ".$phWhere.") order by a.issue_date";
                                              
        $this->result = $db->Execute($this->sql);

        return $this->result;

    }
    
    #added by Bryan on Nov 17, 2008
    function getIssuance($refno){
        
        global $db;
        
        //$this->sql = "SELECT * FROM $this->tb_issue WHERE (refno = '$refno' AND status<2)";
        $this->sql = "SELECT * FROM $this->tb_issue as a JOIN $this->tb_issue_details as b ON a.refno=b.refno WHERE (b.refno = '$refno' AND b.status = 0)";  
        $this->result = $db->Execute($this->sql);
        if ($this->result){
            return $this->result;
        }
        else {
            return false;
        } 
    }
    
    #added by Bryan on Nov 17, 2008
    function getIssuanceDetailsbyRef($refno){
        
        global $db;
        
        $this->sql = "SELECT * FROM $this->tb_issue_details WHERE (refno = '$refno' AND status=0)";
        //echo $this->sql."<br>";
        $this->result = $db->Execute($this->sql);

        return $this->result;
    }
    
    #added by Bryan on Nov 17, 2008
    function acknowledgeIssuance($refno, $acknowledging_id, $item_code){
        
        global $db;
//        $db->debug = true;
        $checkok = 0;
        $counter = 0;
        $acknowledged = 0;
                    

        $bSuccess = FALSE; 
        
        $this->sql = "UPDATE $this->tb_issue SET acknowledging_id=$acknowledging_id, acknowledge_date=NOW(), status=1 WHERE refno='$refno'";
        #$this->result = $db->Execute($this->sql);
        
        $bSuccess = $db->Execute($this->sql);
        
        $checkok = $db->Affected_Rows();
        //echo "<br>1st checkok:".$checkok."<br>";
        
        // if ($checkok!=0){
        if($bSuccess) {
            $checkok = 0;
            $this->sql = "UPDATE $this->tb_issue_details SET status=2 WHERE (refno='$refno' AND item_code='$item_code')";
            #$this->result = $db->Execute($this->sql);
            $bSuccess = $db->Execute($this->sql);   
              
            $checkok = $db->Affected_Rows();
            
            #echo "2nd checkok:".$checkok."<br>";
            if($bSuccess){
                if ($checkok!= 0){
                     $this->sql = "SELECT * from $this->tb_issue_details WHERE refno='$refno'";
                     $this->result = $db->Execute($this->sql);
                     $counter = $db->Affected_Rows();
                     
                    if($counter!=0){
                        $this->sql = "SELECT * from $this->tb_issue_details WHERE (refno='$refno' AND status=2)";
                        $this->result = $db->Execute($this->sql);
                        $acknowledged = $db->Affected_Rows();
                        if($counter==$acknowledged){
                            $this->sql = "UPDATE $this->tb_issue SET status=2 WHERE refno='$refno'";
                            #$this->result = $db->Execute($this->sql);
                            $bSuccess = $db->Execute($this->sql); 
                            $checkok = $db->Affected_Rows();
                        }
                     }

                     return TRUE;
                      
                }
                else {

                    return false;
                }
            }
            
        }
        
        if ($bSuccess){

            return TRUE;
        }
        else {

            return FALSE;
        }
            
        //}
        //else {
        //    return false;
        //} 
    }
    
    #added by Bryan on Nov 17, 2008
    function cancelIssuance($refno, $acknowledging_id, $item_code){
        
        global $db;
        $checkok = 0;
        $counter = 0;
        $acknowledged = 0;
                    
        #start db trans                    
        $db->StartTrans();
        
        $bSuccess = FALSE; 
        
        $this->sql = "UPDATE $this->tb_issue SET acknowledging_id=$acknowledging_id, acknowledge_date=NOW(), status=1 WHERE refno='$refno'";
        #$this->result = $db->Execute($this->sql);
        
        $bSuccess = $db->Execute($this->sql);
        
        $checkok = $db->Affected_Rows();
        //echo "<br>1st checkok:".$checkok."<br>";
        
        // if ($checkok!=0){
        if($bSuccess) {
            $checkok = 0;
            $this->sql = "UPDATE $this->tb_issue_details SET status=1 WHERE (refno='$refno' AND item_code='$item_code')";
            #$this->result = $db->Execute($this->sql);
            
            $bSuccess = $db->Execute($this->sql);
            
            $checkok = $db->Affected_Rows();
            //echo "<br>1st checkok:".$checkok."<br>";
            
            if($bSuccess){
                    if ($checkok!= 0){
                         $this->sql = "SELECT * from $this->tb_issue_details WHERE refno='$refno'";
                         $this->result = $db->Execute($this->sql);
                         $counter = $db->Affected_Rows();
                         
                        if($counter!=0){
                            $this->sql = "SELECT * from $this->tb_issue_details WHERE (refno='$refno' AND status=1)";
                            $this->result = $db->Execute($this->sql);
                            $acknowledged = $db->Affected_Rows();
                            if($counter==$acknowledged){
                                $this->sql = "UPDATE $this->tb_issue SET status=3 WHERE refno='$refno'";
                                #$this->result = $db->Execute($this->sql);
                                $bSuccess = $db->Execute($this->sql); 
                                $checkok = $db->Affected_Rows();
                            }
                         }
                         $db->CompleteTrans();
                         return TRUE;
                          
                    }
                    else {
                        $db->FailTrans(); 
                        return false;
                    }
                }
        }
        
        if ($bSuccess){
            $db->CompleteTrans();
            return TRUE;
        }
        else {
            $db->FailTrans();
            return FALSE;
        }
            
        //}
        //else {
        //    return false;
        //} 
    }
    
    function setDataArray(&$array){
         $this->data_array=$array;
         
    }
    
    function deleteIssuance($refno) {
        global $db;
        $db->StartTrans();
        $bSuccess = FALSE;
        
        
        $this->sql = "DELETE FROM seg_issuance WHERE refno=$refno";
        $bSuccess = $this->Execute($this->sql);
        if ($bSuccess){
            $db->CompleteTrans();
            return TRUE;
        }
        else {
            $db->FailTrans();
            return FALSE;
        }
    }
    
    function getIssueType() {
        global $db;
        
        $this->sql = "SELECT * FROM seg_issuance_type ".
                         "   order by iss_type_name";        
        if ($this->result=$db->Execute($this->sql)){
            if ($this->result->RecordCount())
                return $this->result;
            else
                return FALSE;
        }else
            return FALSE;        
    }
    
    function getIssueTypeOptions($sclass = '') {
        global $db;
        
        
        $soption = "<option".($sclass == ''   ? " selected " : " ") ."value = ''>-- Select Issue Type --</option>".
                   "<option".($sclass == 'CH'  ? " selected " : " ") ."value = 'CH'>Charge Slip</option>".
                   "<option".($sclass == 'D'  ? " selected " : " ") ."value = 'D'>Damage</option>".
                   "<option".($sclass == 'EX'  ? " selected " : " ") ."value = 'EX'>Expense</option>".
                   "<option".($sclass == 'ST' ? " selected " : " ") ."value = 'ST'>Stock Transfer</option>";
        return $soption;    
    }
    
    function startTrans() {
        global $db;
        $db->StartTrans();
    }
    
    function failTrans() {
        global $db;
        $db->FailTrans();
    }
    
    function completeTrans() {
        global $db;
        $db->CompleteTrans();        
    }
    
    function delIssuance($srefno) {        
        global $db;
        
        $bSuccess = true;                
        
        $trns_dte = $this->getIssuanceDate($srefno);        
        if ($trns_dte) {
            $this->startTrans();
            
            $bSuccess = $this->delIssuanceDetails($srefno, $trns_dte);            
            if ($bSuccess) {
                $this->sql = "delete from {$this->tb_issue} ".
                             "   where refno = '{$srefno}'";
                $bSuccess = $db->Execute($this->sql);
            }            
            
            if (!$bSuccess) $this->failTrans();
            $this->completeTrans();            
        }
        
        return $bSuccess;        
// ------------------- replaced with above code by LST ----- 11.19.2009 --------------------                                         
//        $this->sql = "delete from $this->tb_issue ".
//                     "   where refno = '".$srefno."'";
//        return $this->Transact();
// ------------------- replaced with above code by LST ----- 11.19.2009 --------------------
    }
    
    function getPostedIssuances($filters, $offset=0, $rowcount=15, $areas = '') {
        global $db;

        if (!$offset) $offset = 0;
        if (!$rowcount) $rowcount = 15;
        
        if (is_array($filters)) {
            foreach ($filters as $i=>$v) {
                switch (strtolower($i)) {
                    case 'datetoday':
                        $phFilters[] = 'DATE(si.issue_date)=DATE(NOW())';
                        break;
                    case 'datethisweek':
                        $phFilters[] = 'YEAR(si.issue_date)=YEAR(NOW()) AND WEEK(si.issue_date)=WEEK(NOW())';
                        break;
                    case 'datethismonth':
                        $phFilters[] = 'YEAR(si.issue_date)=YEAR(NOW()) AND MONTH(si.issue_date)=MONTH(NOW())';
                        break;
                    case 'date':
                        $phFilters[] = "DATE(si.issue_date)='$v'";
                        break;
                    case 'datebetween':
                        $phFilters[] = "DATE(si.issue_date)>='".$v[0]."' AND DATE(si.issue_date)<='".$v[1]."'";
                        break;
                    case 'name':
                        $phFilters[] = "concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) REGEXP '[[:<:]]".substr($db->qstr($v),1);
                        break;
                    case 'item_desc':
                        $phFilters[] = "cppm.`artikelname` LIKE ".$db->qstr($v."%");
                        break;
                    case 'ref_no':
                        $phFilters[] = "si.refno REGEXP ".$db->qstr($v);
                        break;
                }
            }
        }
        
        /*if ($areas != '') {
            $phFilters[] = "src_area_code in ($areas)";    
        }    */    
        
        $phWhere=implode(") AND (",$phFilters);
        if ($phWhere) $phWhere = "WHERE ($phWhere)";
        else $phWhere = "1";   
        
        $this->sql = "SELECT 
                          SQL_CALC_FOUND_ROWS si.`refno`,
                          si.`issue_date`,
                          si.`status`,
                          (SELECT 
                            area_name 
                          FROM
                            seg_areas AS sa1 
                          WHERE sa1.area_code = si.src_area_code) AS issuing_area,
                          (SELECT 
                            area_name 
                          FROM
                            seg_areas AS sa2 
                          WHERE sa2.area_code = si.area_code) AS issued_area,
                          fn_get_personellname_lastfirstmi (si.`issuing_id`) AS issued_by,
                          GROUP_CONCAT(cppm.`artikelname`) AS particulars,
                          IFNULL(
                            (SELECT 
                              dept_nr 
                            FROM
                              seg_areas 
                            WHERE area_code = si.`src_area_code` 
                              AND dept_nr IN 
                              (SELECT 
                                fn_get_children_dept (''))),
                            0
                          ) dept_nr 
                        FROM
                          seg_issuance_details sid 
                          LEFT JOIN seg_issuance si 
                            ON si.`refno` = sid.`refno` 
                          LEFT JOIN care_pharma_products_main cppm 
                            ON cppm.`bestellnum` = sid.`item_code` 
                          LEFT JOIN care_personell cpl 
                            ON cpl.`nr` = si.`issuing_id` 
                          LEFT JOIN care_person cp 
                            ON cp.`pid` = cpl.`pid` 
                        ".$phWhere."
                        GROUP BY sid.`refno` 
                        ORDER BY DATE(si.`issue_date`) ASC  
                        LIMIT $offset, $rowcount";                                                        
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }                
    }
    
    function getIssuanceHeader($srefno) {
        global $db;  
        
        $this->sql = "select h.*, concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) as issuer  
                         from ($this->tb_issue as h inner join care_personell as cpl on h.issuing_id = cpl.nr) 
                            inner join care_person as cp on cpl.pid = cp.pid 
                         where refno = '$srefno'";                         
        if($this->result=$db->Execute($this->sql)) {
            if ($this->result->RecordCount())
                return $this->result->FetchRow();
            else
                return false;
        } else { return false; }        
    }
    
    function getIssuanceDetails($srefno) {
        global $db;
        
        $this->sql = "select * from $this->tb_issue_details where refno = '$srefno'";

        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }        
    }
    
    function countAllIssuancesThisMonth($date, $item_code, $area) {
        global $db;
        $totalqty = 0;
        
        $obj = new SegPharmaProduct();
        
        $rowextended = $obj->getExtendedProductInfo($item_code);
        
        $this->sql = "select sum(a.item_qty) as total from seg_issuance_details as a 
                        LEFT JOIN seg_issuance as b ON a.refno=b.refno 
                        WHERE a.item_code='$item_code' AND b.src_area_code='$area' AND YEAR(b.issue_date)=YEAR('$date') AND MONTH(b.issue_date)=MONTH('$date') AND a.unit_id='".$rowextended['pc_unit_id']."'";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty = $rowResult['total'];    
        }
        $this->sql = "select sum(a.item_qty) as total from seg_issuance_details as a 
                        LEFT JOIN seg_issuance as b ON a.refno=b.refno 
                        WHERE a.item_code='$item_code' AND b.src_area_code='$area' AND YEAR(b.issue_date)=YEAR('$date') AND MONTH(b.issue_date)=MONTH('$date') AND a.unit_id='".$rowextended['pack_unit_id']."'";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty += ($rowResult['total'] * $rowextended['qty_per_pack']);    
        }
        
        return $totalqty;       
    }
    
    function countAllIncomingIssuancesThisMonth($date, $item_code, $area) {
        global $db;
        $totalqty = 0;
        
        $obj = new SegPharmaProduct();
        
        $rowextended = $obj->getExtendedProductInfo($item_code);
        
        $this->sql = "select sum(a.item_qty) as total from seg_issuance_details as a 
                        LEFT JOIN seg_issuance as b ON a.refno=b.refno 
                        WHERE b.item_code='$item_code' AND b.area_code='$area' AND YEAR(b.issue_date)=YEAR('$date') AND MONTH(b.issue_date)=MONTH('$date') AND a.unit_id='".$rowextended['pc_unit_id']."'";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty = $rowResult['total'];    
        }
        $this->sql = "select sum(a.item_qty) as total from seg_issuance_details as a 
                        LEFT JOIN seg_issuance as b ON a.refno=b.refno 
                        WHERE b.item_code='$item_code' AND b.area_code='$area' AND YEAR(b.issue_date)=YEAR('$date') AND MONTH(b.issue_date)=MONTH('$date') AND a.unit_id='".$rowextended['pack_unit_id']."'";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty += ($rowResult['total'] * $rowextended['qty_per_pack']);    
        }
        
        return $totalqty;        
    }
    
    function countAllIncomingDeliveriesThisMonth($date, $item_code, $area) {
        global $db;
        $totalqty = 0;
        
        $obj = new SegPharmaProduct();
        
        $rowextended = $obj->getExtendedProductInfo($item_code);
        
        $this->sql = "select sum(a.item_qty) as total from seg_delivery_details as a 
                        LEFT JOIN seg_delivery as b ON a.refno=b.refno 
                        WHERE a.item_code='$item_code' AND b.area_code='$area' AND YEAR(b.receipt_date)=YEAR('$date') AND MONTH(b.receipt_date)=MONTH('$date') AND a.unit_id='".$rowextended['pc_unit_id']."'";
        //echo $this->sql;
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty = $rowResult['total'];    
        }
        $this->sql = "select sum(a.item_qty) as total from seg_delivery_details as a 
                        LEFT JOIN seg_delivery as b ON a.refno=b.refno 
                        WHERE a.item_code='$item_code' AND b.area_code='$area' AND YEAR(b.receipt_date)=YEAR('$date') AND MONTH(b.receipt_date)=MONTH('$date') AND a.unit_id='".$rowextended['pack_unit_id']."'";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty += ($rowResult['total'] * $rowextended['qty_per_pack']);    
        }
        
        return $totalqty;       
    }
    
    function countAllIncomingDeliveriesInDates($startdate, $enddate, $item_code) {
        global $db;
        $totalqty = 0;
        $obj = new SegPharmaProduct();
        
        $rowextended = $obj->getExtendedProductInfo($item_code);
        
        $this->sql = "select sum(a.item_qty) as total from seg_delivery_details as a 
                        LEFT JOIN seg_delivery as b ON a.refno=b.refno 
                        WHERE a.item_code='$item_code' AND (b.receipt_date > '".$startdate."' OR b.receipt_date = '".$startdate."') AND (b.receipt_date < '".$enddate."' OR b.receipt_date = '".$enddate."') AND a.unit_id='".$rowextended['pc_unit_id']."'";
        
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty = $rowResult['total'];    
        }
        $this->sql = "select sum(a.item_qty) as total from seg_delivery_details as a 
                        LEFT JOIN seg_delivery as b ON a.refno=b.refno 
                        WHERE a.item_code='$item_code' AND (b.receipt_date > '".$startdate."' OR b.receipt_date = '".$startdate."') AND (b.receipt_date < '".$enddate."' OR b.receipt_date = '".$enddate."') AND a.unit_id='".$rowextended['pack_unit_id']."'";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty += ($rowResult['total'] * $rowextended['qty_per_pack']);    
        }
        
        return $totalqty;       
    }
    
     function delIss($refno) {  
        return $this->delIssuance($refno);
//  ----------------- commented out by LST ----- 11.19.2009 -------- same method as delIssuance ----   
//        $this->sql = "delete from $this->tb_issue ".
//                     "  where refno = '".$refno."'";
//        return $this->Transact();
//  ----------------- commented out by LST ----- 11.19.2009 -------- same method as delIssuance ---- 
    }                                          
    
//  ----------------- commented out by LST ----- 11.19.2009 ------------------------
//    function delIssDetails($refno) {        
//        $this->sql = "delete from ".$this->tb_issue_details." ".
//                     "  where refno = '".$refno."'";
//        return $this->Transact();
//    }
//    
//    function delIssServed($refno) {    --- this method is not necessary anymore .... LST added trigger in deleting seg_issuance_details (tg_seg_issuance_details_ad).
//        $this->sql = "delete from ".$this->tb_req_served." ".
//                     "  where issue_refno = '".$refno."'";
//        return $this->Transact();
//    }
//  ----------------- commented out by LST ----- 11.19.2009 ------------------------        

    /**
    * @internal     Return the date of particular issuance.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        srefno - reference no. to identify the issuance.
    * @return       the issuance date.
    */      
    function getIssuanceDate($srefno) {
        global $db;  
        
        $this->sql = "select issue_date from {$this->tb_issue} where refno = '$srefno'";
        if($this->result=$db->Execute($this->sql)) {
            if ($this->result->RecordCount()) {
                $row = $this->result->FetchRow();
                return $row["issue_date"];
            }
            else
                return false;
        } else { return false; }
    }

    /**
    * @internal     Return the source area_code of particular issuance.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        srefno - reference no. to identify the issuance.
    * @return       the area_code.
    */         
    function getCurrentAreaCode($srefno) {
        global $db;  
        
        $this->sql = "select src_area_code from {$this->tb_issue} where refno = '$srefno'";
        if($this->result=$db->Execute($this->sql)) {
            if ($this->result->RecordCount()) {
                $row = $this->result->FetchRow();
                return $row["src_area_code"];
            }
            else
                return false;
        } else { return false; }
    }

    /**
    * @internal     Delete issuance detail and update the inventory accordingly.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        ref_no   - reference no. of issuance transaction
    * @param        trns_dte - transaction date (issuance date)
    * @return       boolean TRUE if successful, FALSE otherwise.
    */ 
    function delIssuanceDetails($refno, $trns_dte) {
        global $db;
        
        $bSuccess = true;
        
        // Update the inventory of each item in the issuance detail to delete before 
        // deleting the issuance transaction ...
       /*$this->sql = "select * from {$this->tb_issue_details}
                     where refno = '$refno'";
       $result = $db->Execute($this->sql);
       $area = $this->getCurrentAreaCode($refno);
       $invHelper = new InventoryHelper();
       if ($result->RecordCount()) {
            while($row = $result->FetchRow()){
                $invHelper->addStock($row['item_code'], $area, $row['item_qty'], '', $row['refno'], $row['refno'], PHRETURN, $row['unit_id'], $row['is_unitperpc']);
            }                                     
       } */
            
       if ($bSuccess) {
            //... if successfully updated the inventory, delete the issuance details.
                
            $this->sql = "delete from {$this->tb_issue_details} ".
                         "   where refno = '$refno'";
            $bSuccess = $db->Execute($this->sql);                   
            
        }                    
        
        return $bSuccess;       
    }
    
    function getReqServedbyIss($refno) {
        global $db;
        
        $this->sql = "select * from $this->tb_req_served where issue_refno='$refno'";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            return $this->result;
        }
    }

    function getRequestQty($issueRef, $itemCode) {
        global $db;
        $qty = $db->GetOne('SELECT served_qty
                    FROM seg_requests_served
                    WHERE issue_refno = ? AND item_code = ?', array($issueRef, $itemCode));
        return isset($qty) ? intval($qty) : 0;
    }

    function cancelRequestServed($issueRef, $itemCode, $qty) {
        global $db;
        $servedQty = $this->getRequestQty($issueRef, $itemCode) - intval($qty);
        $this->sql = 'UPDATE seg_requests_served
                      SET served_qty = ?
                      WHERE item_code = ? AND issue_refno = ?';
        $saveOk = $db->Execute($this->sql, array($servedQty, $itemCode, $issueRef));
        if($saveOk)
            return true;
        return false;
    }
    
    function getIssuanceDetailInfo($refno,$item_code){
        global $db;
        
        $this->sql = "select * from $this->tb_issue_details where refno='$refno' AND item_code='$item_code'";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            return $this->result;
        }
        else
            return false;
    }
    
    function getLastNr($today) {
        global $db;
        $today = $db->qstr($today);
        $this->sql="SELECT IFNULL(MAX(CAST(refno AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->tb_issue WHERE SUBSTRING(refno,1,4)=EXTRACT(YEAR FROM NOW())";
        return $db->GetOne($this->sql);
    }     
    
    function setCustodianDetails($refno,$item,$expiry,$serial,$prop,$life){
        global $db;
        $this->sql="INSERT INTO seg_custody_extension_details VALUES ('".$refno."','".$item."','".$expiry."','".$serial."','".$prop."',".$life.")";
        $result=$db->Execute($this->sql);
        return $result;                                                                           
    } 
    
    function editCustodianDetails($refno,$item,$expiry,$serial,$prop,$life){
        global $db;
        $this->sql="UPDATE seg_custody_extension_details SET property_no='".$prop."',estimated_life=".$life." WHERE refno='".$refno."' AND item_code='".$item."' AND expiry_date='".$expiry."' AND serial_no='".$serial."'";
        $result=$db->Execute($this->sql);
        return $result;                                                                           
    } 
    
    function getCustodianDetails($refno,$item,$expiry,$serial){
        global $db;
        $this->sql="SELECT * from seg_custody_extension_details WHERE refno='".$refno."' AND item_code='".$item."' AND expiry_date='".$expiry."' AND serial_no='".$serial."'";
        $result=$db->Execute($this->sql);
        return $result;                                                                           
    } 
    
    function checkIfHavingEquipment($refno) {
        global $db;
        
        $status = false;
        
        $prod_obj = new SegPharmaProduct();
        $iss_obj = new Issuance();
        
        $result = $iss_obj->getIssuanceDetails($refno);
        
        if($result){
            while($row=$result->FetchRow()){
                $prod_det = $prod_obj->getProductInfo($row['item_code']);
                
                if($prod_det['prod_class'] == 'E'){
                    $status = true;
                    break;
                }
            }
        }
        
        return $status;
    }
    
    /***
     * Public routine that get the source of the issuance transaction given the reference no.
     * 
     * @param  string  $refno
     * 
     * @return string areacode of source, otherwise false.
     */
    function getSourceArea($refno) {
        global $db;
        
        $this->sql = "SELECT 
                        src_area_code 
                      FROM
                        seg_issuance 
                      WHERE refno = '{$refno}'";
        $result = $db->Execute($this->sql);
        if ($result) {            
            if ($result->RecordCount()) {
                $row = $result->FetchRow();                
                return $row['src_area_code'];
            }
        }        
        return false;       
    }

    //added by julz
    function getIssuanceNoByRequest($request_refno) {
        global $db;

        return $db->GetOne("SELECT 
                              issue_refno 
                            FROM
                              seg_requests_served 
                            WHERE request_refno = ?", $request_refno);
    }


}   
?>