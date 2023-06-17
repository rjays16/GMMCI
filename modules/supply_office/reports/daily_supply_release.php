<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/inventory/class_inventory_report.php');
require_once($root_path.'include/care_api_classes/class_area.php');

$area = @$_GET['area'];
$dateFrom = @$_GET['from_date'];
$dateTo = @$_GET['to_date'];
$area_obj = new SegArea();
$areaname = $area_obj->getAreaName($area);

function getData($area, $dateFrom, $dateTo) {
    global $db;
    $invr_obj = new SegInventoryReport();
    $result = $invr_obj->getMovementinAreaForSC($area, NULL, 1, $dateFrom, $dateTo);

    if ($result) {
        $this->Data=array();
        while ($row=$result->FetchRow()) {
            $account = array();
            $collection = array();
            $a_types = explode("\n",$row['a_types']);
            foreach ($a_types as $i=>$type) {
                $type_arr = explode('|',$type);
                if (count($type_arr) == 1) $type_arr[1] = $type_arr[0];
                if (!in_array($type_arr[0],$account)) $account[] = $type_arr[0];
                if (!in_array($type_arr[1],$collection)) $collection[] = $type_arr[1];
            }
            if($row['status']=='1') $stat = 'cancelled';
            else if (($row['status']=='2')) $stat = 'approved';
            else $stat = 'issued';

            if($row['outqty'] <= 0) continue;
            $this->Data[]=array(
                date("m/d/Y",strtotime($row['cutoff_date'])),
                $areaname,
                $row['bestellnum'],
                $row['artikelname'],
                number_format($row['outqty'],0,'.',','),
                $row['unit_name'],
                $row['area'],
            );
            //$this->_total+=$row['amount_due'];
        }
        //$this->_count = count($this->Data);

    } else {
        die('no data..');
    }
}

getData();




