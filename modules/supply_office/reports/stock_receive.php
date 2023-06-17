<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/inventory/class_adjustment.php');
require_once($root_path.'include/care_api_classes/reports/JasperReport.php');
require_once($root_path.'include/care_api_classes/class_area.php');
require_once($root_path.'include/care_api_classes/inventory/class_delivery.php');
require_once($root_path.'include/care_api_classes/inventory/class_unit.php');
require_once($root_path.'include/care_api_classes/curl/class_curl.php');

$adjustment = new SegAdjustment();
$areaObj = new SegArea();
$item = new Item();
$curl = new Rest_Curl();

$jasper = new JasperReport();
$refno = @$_GET['refno'];
$unit = new Unit();

$delivery = new Delivery();

$header = $delivery->getDeliveryHeader($refno);
$details = $delivery->getDeliveryDetails($refno);
$supplier = json_decode($curl->getSupplier($header['supplier_id']), true); //added by maimai 12-15-2014

$area = $header['area_code'];
$data = array();
$total = 0;
$totalQty = 0;
foreach ($details as $d) {
    $extended = $item->getExtendedProductInfo($d['item_code']);
    $packQty = 1;
    if($extended) {
        if($d['unit_id'] == $extended['pack_unit_id']) {
            $packQty = $extended['qty_per_pack'];
        }
    }

  
    $totalQty += $d['item_qty'] * $packQty;

    $allqty=$d['item_qty'] * $d['qty_per_pck'];


    $total +=  $allqty * $d['unit_price'];

    $data[] = array(
        'description' => $item->getItemDesc($d['item_code']),
        //'qty' => $d['item_qty'] . ' ' . $unit->getUnitName($d['unit_id']),
         'qty' => $allqty,
        'price' => number_format($d['unit_price'],2),
        'amount' => number_format($allqty * $d['unit_price'],2),
        'expiry_date' => $d['expiry_date'] == '0000-00-00' ? '' :  date('m/d/Y', strtotime($d['expiry_date'])),
        'lot_no' => !is_null($d['lot_no']) ? $d['lot_no'] : '',
        'manufacturer' => !is_null($d['manufacturer']) ? $d['manufacturer'] : '',
    );
}

if($area === '')
    $area = 'All Areas';
else {
    $area = $areaObj->getAreaName($area);
}

$params = array(
    'areaname' => $area,
    'delivery_date' => date('m/d/Y', strtotime($header['receipt_date'])),
    'refno' => $refno,
    'total' => number_format($total,2),
    'discount' => number_format(0,2),
    'amount_due' => number_format($total,2),
    'total_item_count' => number_format($totalQty,0),
    'supplier'=> $header['supplier_id'] ? $supplier[1] : ""
);


$jasper->setParams($params);

$jasper->setData($data);
$jasper->setJrxmlFilePath('INV_stock_receive.jrxml');
$jasper->run();
