<?php   
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/class_pharma_product.php');  

class Request extends Core {
    
    var $tb_request = "seg_internal_request";
    var $tb_request_detail = "seg_internal_request_details";
    
    var $request_hdr = array(
        "refno",
        "request_date",
        "requestor_id",
        "area_code",
        "area_code_dest"
    );        
    
    var $request_detail = array(
        "refno",
        "item_code",
        "item_qty",
        "unit_id",
        "is_unitperpc"
    );    
        
    function setRequestHdr() {
        $this->coretable = $this->tb_request;
        $this->setTable($this->coretable);
        $this->setRefArray($this->request_hdr);
    }
    
    function setRequestDetail() {
        $this->coretable = $this->tb_request_detail;
        $this->setTable($this->coretable);
        $this->setRefArray($this->request_detail);
    }    
    
    function insertRequestDetail() {
        global $db;
        
        $strSQL = "select * from $this->coretable 
                      where refno = '".$this->data_array['refno']."' 
                         and item_code = '".$this->data_array['item_code']."' 
                         and unit_id = ".$this->data_array['unit_id']." for update";
                         
        $result = $db->Execute($strSQL);
        if (!$result->EOF) { 
            $strSQL = "update $this->coretable set 
                             item_qty = item_qty + ".$this->data_array['item_qty']."
                          where refno = '".$this->data_array['refno']."' 
                             and item_code = '".$this->data_array['item_code']."' 
                             and unit_id = ".$this->data_array['unit_id']; 
            $bSuccess = $db->Execute($strSQL);   
        }                         
        else
            $bSuccess = $this->insertDataFromInternalArray();

        return $bSuccess;
    }
    
    function getLastNr($today) {
        global $db;
        $today = $db->qstr($today);
        $this->sql="SELECT IFNULL(MAX(CAST(refno AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->tb_request WHERE SUBSTRING(refno,1,4)=EXTRACT(YEAR FROM NOW())";
        return $db->GetOne($this->sql);
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
    
    function delRequest($srefno) {        
        $this->sql = "delete from ".$this->tb_request." ".
                     "   where refno = '".$srefno."'";
        return $this->Transact();
    }    
    
    function delRqstDetails($srefno) {        
        $this->sql = "delete from ".$this->tb_request_detail." ".
                     "   where refno = '".$srefno."'";
        return $this->Transact();
    }
    
    /**
    * @internal     Return the header information of particular request.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        srefno - reference no. to identify the header information.
    * @return       resultset of extracted records.
    */     
    function getRequestHeader($srefno) {
        global $db;  
        
        $this->sql = "select h.*,sa.`area_name`, concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) as requestor  
                         from ($this->tb_request as h inner join care_personell as cpl on h.requestor_id = cpl.nr INNER JOIN seg_areas AS sa ON sa.`area_code` = h.`area_code`) 
                            inner join care_person as cp on cpl.pid = cp.pid 
                         where refno = '$srefno'";                         
        if($this->result=$db->Execute($this->sql)) {
            if ($this->result->RecordCount())
                return $this->result->FetchRow();
            else
                return false;
        } else { return false; }        
    }
    
    /**
    * @internal     Return the details of particular request.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        srefno - reference no. to identify the request details.
    * @return       resultset of extracted records.
    */     
    function getRequestDetails($srefno) {
        global $db;
        
        $this->sql = "select * from $this->tb_request_detail where refno = '$srefno'";
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }        
    }
    
    /**
    * @internal     Extract the history of requests given a particular filter.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        filters - array of filters.
    * @param        offset - from which record.
    * @param        rowcount - how many records (rows) to extract.
    * @param        areas    - string of area codes.
    * @return       resultset of extracted records.
    */       
    function getPostedRequests($filters, $offset=0, $rowcount=15, $areas = '') {
        global $db;

        if (!$offset) $offset = 0;
        if (!$rowcount) $rowcount = 15;
        
        if (is_array($filters)) {
            foreach ($filters as $i=>$v) {
                switch (strtolower($i)) {
                    case 'datetoday':
                        $phFilters[] = 'DATE(request_date)=DATE(NOW())';
                        break;
                    case 'datethisweek':
                        $phFilters[] = 'YEAR(request_date)=YEAR(NOW()) AND WEEK(request_date)=WEEK(NOW())';
                        break;
                    case 'datethismonth':
                        $phFilters[] = 'YEAR(request_date)=YEAR(NOW()) AND MONTH(request_date)=MONTH(NOW())';
                        break;
                    case 'date':
                        $phFilters[] = "DATE(request_date)='$v'";
                        break;
                    case 'datebetween':
                        $phFilters[] = "DATE(request_date)>='".$v[0]."' AND DATE(request_date)<='".$v[1]."'";
                        break;
                    case 'name':
                        $phFilters[] = "concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) REGEXP '[[:<:]]".substr($db->qstr($v),1);
                        break;
                    case 'item_desc':
                        $phFilters[] = "(select count(sird3.refno) as ncount  
                            from $this->tb_request_detail as sird3 inner join care_pharma_products_main as cppm on sird3.item_code = cppm.bestellnum
                            where cppm.artikelname REGEXP ".$db->qstr($v)." and sird3.refno = sirh.refno) > 0";
                        break;
                    case 'ref_no':
                        $phFilters[] = "sirh.refno REGEXP ".$db->qstr($v);
                        break;
                }
            }
        }
        
        /*if ($areas != '') {
            $phFilters[] = "area_code in ($areas)";    
        }*/
        
        $phWhere=implode(") AND (",$phFilters);
        if ($phWhere) $phWhere = "($phWhere)";
        else $phWhere = "1";   
        
        $this->sql = "select SQL_CALC_FOUND_ROWS sirh.refno, request_date, 
                            (select area_name from seg_areas as sa1 where sa1.area_code = sirh.area_code) as requesting_area, 
                            (select area_name from seg_areas as sa2 where sa2.area_code = sirh.area_code_dest) as requested_area, 
                            concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) as requestor, 
                            (select GROUP_CONCAT(DISTINCT artikelname ORDER BY artikelname DESC SEPARATOR ', ') as items 
                               from $this->tb_request_detail as sird inner join care_pharma_products_main as cppm on sird.item_code = cppm.bestellnum
                               group by sird.refno having sird.refno = sirh.refno) as particulars                         
                         from ($this->tb_request as sirh inner join care_personell as cpl on 
                            sirh.requestor_id = cpl.nr) inner join care_person as cp on cpl.pid = cp.pid 
                         where ($phWhere)                            
                         order by request_date asc 
                         limit $offset, $rowcount";                         
                                 
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }                
    }
    
    /**
    * @internal     Extract the history of pending requests given a particular filter.
    * @access       public
    * @author       Bryan Inno N. Wong
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        filters - array of filters.
    * @param        offset - from which record.
    * @param        rowcount - how many records (rows) to extract.
    * @param        areas    - string of area codes.
    * @return       resultset of extracted records.
    */       
    function getPostedPendingRequests($filters, $offset=0, $rowcount=15, $areas = '') {
        global $db;

        if (!$offset) $offset = 0;
        if (!$rowcount) $rowcount = 15;
        
        if (is_array($filters)) {
            foreach ($filters as $i=>$v) {
                switch (strtolower($i)) {
                    case 'datetoday':
                        $phFilters[] = 'DATE(request_date)=DATE(NOW())';
                        break;
                    case 'datethisweek':
                        $phFilters[] = 'YEAR(request_date)=YEAR(NOW()) AND WEEK(request_date)=WEEK(NOW())';
                        break;
                    case 'datethismonth':
                        $phFilters[] = 'YEAR(request_date)=YEAR(NOW()) AND MONTH(request_date)=MONTH(NOW())';
                        break;
                    case 'date':
                        $phFilters[] = "DATE(request_date)='$v'";
                        break;
                    case 'datebetween':
                        $phFilters[] = "DATE(request_date)>='".$v[0]."' AND DATE(request_date)<='".$v[1]."'";
                        break;
                    case 'name':
                        $phFilters[] = "concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) REGEXP '[[:<:]]".substr($db->qstr($v),1);
                        break;
                    case 'item_desc':
                        $phFilters[] = "(select count(sird3.refno) as ncount  
                            from $this->tb_request_detail as sird3 inner join care_pharma_products_main as cppm on sird3.item_code = cppm.bestellnum
                            where cppm.artikelname REGEXP ".$db->qstr($v)." and sird3.refno = sirh.refno) > 0";
                        break;
                    case 'ref_no':
                        $phFilters[] = "sirh.refno REGEXP ".$db->qstr($v);
                        break;
                }
            }
        }
        
        /*if ($areas != '') {
            $phFilters[] = "area_code_dest in ($areas)";    
        }*/
        
        $phWhere=implode(") AND (",$phFilters);
        if ($phWhere) $phWhere = "($phWhere)";
        else $phWhere = "1";   
        
        $this->sql = "select SQL_CALC_FOUND_ROWS sirh.refno, request_date, sirh.area_code, sirh.area_code_dest,  
                            (select area_name from seg_areas as sa1 where sa1.area_code = sirh.area_code) as requesting_area, 
                            (select area_name from seg_areas as sa2 where sa2.area_code = sirh.area_code_dest) as requested_area, 
                            concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) as requestor, 
                            (select GROUP_CONCAT(DISTINCT artikelname ORDER BY artikelname DESC SEPARATOR ', ') as items 
                               from $this->tb_request_detail as sird inner join care_pharma_products_main as cppm on sird.item_code = cppm.bestellnum
                               LEFT JOIN 
                                  (SELECT 
                                    srs.`item_code`,
                                    srs.`request_refno`,
                                    IFNULL(SUM(srs.`served_qty`), 0) AS served_qty 
                                  FROM
                                    seg_requests_served srs 
                                  GROUP BY srs.`request_refno`,
                                    srs.`item_code`) t 
                                  ON (
                                    t.request_refno = sird.`refno` 
                                    AND t.item_code = sird.`item_code`
                                  ) 
                              WHERE (
                                  sird.`item_qty` <> t.served_qty 
                                  OR t.served_qty IS NULL
                                ) 
                              GROUP BY sird.refno 
                              HAVING sird.refno = sirh.refno) AS particulars                          
                         from ($this->tb_request as sirh inner join care_personell as cpl on 
                            sirh.requestor_id = cpl.nr) inner join care_person as cp on cpl.pid = cp.pid 
                         where ($phWhere)                            
                         order by request_date asc 
                         limit $offset, $rowcount";                         
                                 
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }                
    }
    
    //added by bryan 120809
    function checkIfNoPendingDetails($refno){
      
      $cpp = new SegPharmaProduct();
      global $db;
      $checker = 0;
      
      $this->result = $this->getRequestDetails($refno);
      //print_r($this->result);
      while($row = $this->result->FetchRow()){
        $sqty = $row["item_qty"];   
        
        
        if($row["is_unitperpc"]==0){
            $extend = $cpp->getExtendedProductInfo($row["item_code"]);
            $sqty = $sqty * $extend["qty_per_pack"];   
        }
          
        $checker = $this->checkServedQty($row["refno"], $row["item_code"], $sqty);
        //echo "checker ".$checker;
        //echo "qty ".$sqty." refno".$row["refno"]." item".$row["item_code"];
        
        if($checker == 0) {
            //echo "sulod";
            return 0;
        }
       
      }
      
      return $checker;
    }
    
    function checkServedQty($refno, $item_code, $smallqty){
        global $db;
        $checkqty=$smallqty;
        $compqty=0;
        $flag = 0;
        
        $this->sql = "select request_refno,item_code,SUM(served_qty) as qty from seg_requests_served where item_code='$item_code' and request_refno='$refno'";
        //echo "smallqty ".$smallqty." ".$this->sql;
        $result = $db->Execute($this->sql);
        
        if($result){
            while($row = $result->FetchRow()){
                $compqty = $row["qty"];
            }
        }
        //echo "req:".$checkqty."serve:".$compqty;
        if($compqty == $checkqty) $flag = 1;
        
        return $flag;         
    }
    
    
    function getRequestDetailInfo($refno,$item_code){
        global $db;
        
        $this->sql = "select * from $this->tb_request_detail where refno='$refno' AND item_code='$item_code'";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            return $this->result;
        }
        else
            return false;
    }

    function getDeliveryInfo($item_code){
        global $db;
        $item_code = $db->qstr($item_code);
        return $db->getAll("SELECT 
                              GROUP_CONCAT(t.lot_no SEPARATOR ' / ') AS lot_no_group,
                              t.lot_no,
                              t.manufacturer,
                              t.expiry_date,
                              t.item_code,
                              t.serial_no,
                              t.invoice_no,
                              t.unit_price
                            FROM
                              (SELECT 
                                sdd.lot_no,
                                sdd.manufacturer,
                                sdd.expiry_date,
                                sdd.item_code,
                                sdd.refno,
                                sdd.serial_no,
                                sd.invoice_no,
                                sdd.unit_price
                              FROM
                                seg_delivery_details sdd
                                LEFT JOIN seg_delivery sd 
                                ON sd.refno = sdd.refno 
                              WHERE sdd.item_code = $item_code
                              ORDER BY sdd.refno DESC ) AS t 
                            ORDER BY t.refno DESC ");
    }


    // function getDeliveryInfo($item_code){
    //     global $db;
    //     $item_code = $db->qstr($item_code);
    //     return $db->getAll("SELECT sdd.lot_no,sdd.manufacturer,sdd.expiry_date,sdd.item_code FROM seg_delivery_details sdd WHERE sdd.item_code=$item_code
    //                         ORDER BY sdd.refno DESC ");
    // }
    
    function getRequestsInAreas($areas){
        global $db;
        $this->sql = "
        select f.item, SUM(f.qty)-(IF(((
select SUM(served_qty) from seg_requests_served WHERE request_refno=f.refno AND item_code=f.item GROUP BY request_refno,item_code)>0),
(select SUM(served_qty) from seg_requests_served WHERE request_refno=f.refno AND item_code=f.item GROUP BY request_refno,item_code)
,0))
as qty from
((Select ir.refno,ird.item_code as item, (ird.item_qty * ie.qty_per_pack) as qty from seg_internal_request_details as ird
    LEFT JOIN seg_internal_request as ir ON ird.refno=ir.refno
    LEFT JOIN seg_item_extended as ie ON ird.item_code=ie.item_code
    WHERE ir.area_code_dest IN ($areas) and ird.is_unitperpc=0 
)
UNION ALL 
(
Select ir.refno,ird.item_code as item, ird.item_qty as qty from seg_internal_request_details as ird
    LEFT JOIN seg_internal_request as ir ON ird.refno=ir.refno
    LEFT JOIN seg_item_extended as ie ON ird.item_code=ie.item_code
    WHERE ir.area_code_dest IN ($areas) and ird.is_unitperpc=1 
)) as f
GROUP BY f.item;
        ";
        
        $this->result = $db->Execute($this->sql);
        if($this->result){
            return $this->result;
        }
        else
            return false;
    } 

    //Created by EJ 11/08/2014
    function checkIfNoRequestPending($refno) {
        global $db;

        $this->sql = $db->Prepare("SELECT 
                      sird.item_code AS item_code,
                      SUM(IF(sird.is_unitperpc = 0, sird.item_qty * sie.qty_per_pack, sird.item_qty)) AS rqty
                    FROM
                      seg_internal_request_details AS sird 
                      LEFT JOIN seg_item_extended AS sie 
                        ON sird.item_code = sie.item_code 
                    WHERE sird.refno = ?
                    GROUP BY sird.item_code");
        
        $result = $db->Execute($this->sql,$refno);
        if($result && $request = $result->FetchRow()) {
            
            $item_code = $request['item_code'];
            $this->sql = $db->Prepare("SELECT 
                          request_refno,
                          item_code,
                          SUM(served_qty) AS sqty 
                        FROM
                          seg_requests_served 
                        WHERE item_code = ? 
                          AND request_refno = ?");
            
            $result = $db->Execute($this->sql,array($item_code,$refno));
            if($result && $served = $result->FetchRow()) {
                if ($request['rqty'] == $served['sqty']) {
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }
}
?>
