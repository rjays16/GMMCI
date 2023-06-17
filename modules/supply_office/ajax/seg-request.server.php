<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path."include/care_api_classes/class_pharma_ward.php");
require_once($root_path.'modules/pharmacy/ajax/wardstock-list.common.php');

function reset_stocknr() {
        global $db;
        $objResponse = new xajaxResponse();
        
        $wc = new SegPharmaWard();
        $lastnr = $wc->getLastNr();

        if ($lastnr)
            $objResponse->addScriptCall("resetRefNo",$lastnr);
        else
            $objResponse->addScriptCall("resetRefNo","Error!",1);
        return $objResponse;
    }
    
    function populate_stock( $nr, $disabled=NULL ) {
        global $db;
        $objResponse = new xajaxResponse();
        $wc = new SegPharmaWard();        
        $objResponse->addScriptCall("clearOrder",NULL);
        $rows = 0;
        if ($nr) {
            $result = $wc->getStockItems($nr);
            if ($result) {
                $rows=$result->RecordCount();
                 while ($row=$result->FetchRow()) {
                    $obj = (object) 'details';
                    $obj->id = $row["bestellnum"];
                    $obj->name = $row["artikelname"];
                    $obj->qty = $row["quantity"];
                    $obj->desc= $result["generic"];
                    $objResponse->addScriptCall("appendOrder", NULL, $obj, $disabled);
                    #$objResponse->addAlert(print_r($row,TRUE));
                }
                if (!$rows) $objResponse->addScriptCall("appendOrder",NULL,NULL);
            }
            else {            
                if (true) {
                    $objResponse->addScriptCall("display",$order_obj->sql);
                    # $objResponse->addAlert($sql);
                }
                else {
                    $objResponse->addAlert("A database error has occurred. Please contact your system administrator...");
                }
            }
        }
        else $objResponse->addScriptCall("appendOrder", NULL, NULL, $disabled);
        return $objResponse;
    }


function populateWardstockList($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
    $objResponse = new xajaxResponse();    
    $wclass = new SegPharmaWard();
    $area = $args[0];
    if ($_REQUEST['area']) {
        $filters["AREA"] = $area;
    }
    
    $filters["ENCODER"] = $_SESSION['sess_temp_userid'];
    $filters["THISSHIFT"] = "";

    $offset = $page_num * $max_rows;
    $sortColumns = array('stock_date','stock_nr','ward_name','items','encoder','area_full');
    $sort = array();
    if (is_array($sort_obj)) {
        foreach ($sort_obj as $i=>$v) {
            $col = $sortColumns[$i] ? $sortColumns[$i] : "stock_date";
            if ((int)$v < 0) $sort[] = "$col DESC";
            elseif ((int)$v > 0) $sort[] = "$col ASC";
        }
    }
    if ($sort) $sort_sql = implode(',', $sort);
    else $sort_sql = 'stock_date DESC';
    
    #task for tomorrow
    //$result=$pclass->searchProducts($codename, $generic, $classification, $prodclass, $offset, $max_rows, $sort_sql);
    $result=$wclass->getStockList($filters, $offset, $max_rows, $sort_sql);
    
    if($result) {
        $found_rows = $wclass->FoundRows();
        $last_page = ceil($found_rows/$max_rows)-1;
        if ($page_num > $last_page) $page_num=$last_page;
        
        if($data_size=$result->RecordCount()) {
            $temp=0;
            $i=0;
            $objResponse->contextAssign('currentPage', $page_num);
            $objResponse->contextAssign('lastPage', $last_page);
            $objResponse->contextAssign('maxRows', $max_rows);
            $objResponse->contextAssign('listSize', $found_rows);
            
            $DATA = array();
            while($row = $result->FetchRow()) {
            
            $items = explode("\n",$row["items"]);
            $items = implode(", ",$items);
            
            $total_items = (int) $row['count_total_items'];
            $total_served = (int) $row['count_served_items'];
        //'stock_date','stock_nr','ward_name','items','encoder','area_full',        
   
            $DATA[$i]['stock_date'] = $row['stock_date'];
            $DATA[$i]['stock_nr'] = $row['stock_nr'];
            $DATA[$i]['ward_name'] = $row['ward_name'];
            $DATA[$i]['items'] = $items;
            $DATA[$i]['encoder'] = $row['encoder'];
            $DATA[$i]['area_full'] = $row['area_full'];
            $DATA[$i]['FLAG'] = 1;
            $i++;
            } //end while
            
            $objResponse->contextAssign('dataSize', $data_size);
            $objResponse->contextAssign('listData', $DATA);
        }
        else {
            $objResponse->contextAssign('dataSize', 0);
            $objResponse->contextAssign('listData', NULL);
        }
        
    } else {
        // error
        $objResponse->alert($objSS->sql);
        $objResponse->contextAssign('dataSize', -1);
        $objResponse->contextAssign('listData', NULL);
    }    
    $objResponse->script('this.fetchDone()');
    return $objResponse;
}

$xajax->processRequest();
?>