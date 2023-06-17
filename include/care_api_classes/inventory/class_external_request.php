<?php
/*
 * @package care_api
 * Class of External Requests.
 *
 * Created: June 16, 2009 (Bryan)
 * 
 */
require_once($root_path.'include/care_api_classes/class_core.php'); 

class SegExternalRequest extends Core{
    
    var $tb_extrequest = "seg_external_request";
    var $tb_extrequest_detail = "seg_external_request_details";
    
    var $exrequest_hdr = array(
        "refno",
        "request_date",
        "requestor_id",
        "area_code",
        "procurer_nr",
        "remarks"
    );        
    
    var $exrequest_detail = array(
        "refno",
        "item_code",
        "item_qty",
        "unit_id",
        "is_unitperpc"
    );    
        
    function setExternalRequestHdr() {
        $this->coretable = $this->tb_extrequest;
        $this->setTable($this->coretable);
        $this->setRefArray($this->exrequest_hdr);
    }
    
    function setExternalRequestDetail() {
        $this->coretable = $this->tb_extrequest_detail;
        $this->setTable($this->coretable);
        $this->setRefArray($this->exrequest_detail);
    }  
    
    function insertExternalRequestDetail() {
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
        $this->sql="SELECT IFNULL(MAX(CAST(refno AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->tb_extrequest WHERE SUBSTRING(refno,1,4)=EXTRACT(YEAR FROM NOW())";
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
    
    function delExternalRequest($srefno) {        
        $this->sql = "delete from ".$this->tb_extrequest." ".
                     "   where refno = '".$srefno."'";
        return $this->Transact();
    }    
    
    function delExternalRqstDetails($srefno) {        
        $this->sql = "delete from ".$this->tb_extrequest_detail." ".
                     "   where refno = '".$srefno."'";
        return $this->Transact();
    }
    
    function getExternalRequestHeader($srefno) {
        global $db;  
        
        $this->sql = "select h.*, concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) as requestor  
                         from ($this->tb_extrequest as h inner join care_personell as cpl on h.requestor_id = cpl.nr) 
                            inner join care_person as cp on cpl.pid = cp.pid 
                         where refno = '$srefno'";                         
        if($this->result=$db->Execute($this->sql)) {
            if ($this->result->RecordCount())
                return $this->result->FetchRow();
            else
                return false;
        } else { return false; }        
    }
    
    function getExternalRequestDetails($srefno) {
        global $db;
        
        $this->sql = "select * from $this->tb_extrequest_detail where refno = '$srefno'";
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }        
    }
    
    function getPostedExternalRequests($filters, $offset=0, $rowcount=15, $areas = '') {
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
                            from $this->tb_extrequest_detail as sird3 inner join care_pharma_products_main as cppm on sird3.item_code = cppm.bestellnum
                            where cppm.artikelname REGEXP ".$db->qstr($v)." and sird3.refno = sirh.refno) > 0";
                        break;
                    case 'ref_no':
                        $phFilters[] = "sirh.refno REGEXP ".$db->qstr($v);
                        break;
                }
            }
        }
        
        if ($areas != '') {
            $phFilters[] = "area_code in ($areas)";    
        }        
        
        $phWhere=implode(") AND (",$phFilters);
        if ($phWhere) $phWhere = "($phWhere)";
        else $phWhere = "1";   
        
        $this->sql = "select SQL_CALC_FOUND_ROWS sirh.refno, request_date, 
                            (select area_name from seg_areas as sa1 where sa1.area_code = sirh.area_code) as requesting_area, 
                            (select name from seg_supplier as sa2 where sa2.supplier_id = sirh.procurer_nr) as procurer, 
                            concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) as requestor, 
                            (select GROUP_CONCAT(DISTINCT artikelname ORDER BY artikelname DESC SEPARATOR ', ') as items 
                               from $this->tb_extrequest_detail as sird inner join care_pharma_products_main as cppm on sird.item_code = cppm.bestellnum
                               group by sird.refno having sird.refno = sirh.refno) as particulars                         
                         from ($this->tb_extrequest as sirh inner join care_personell as cpl on 
                            sirh.requestor_id = cpl.nr) inner join care_person as cp on cpl.pid = cp.pid 
                         where ($phWhere)                            
                         order by request_date asc 
                         limit $offset, $rowcount";                         
                                 
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }                
    }

}  
  
?>
