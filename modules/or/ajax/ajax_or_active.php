<?php
require_once('roots.php');

require($root_path.'include/inc_environment_global.php');   




   
   function countRec($where) {
       global $db;
       global $join;
    $sql = "SELECT count(sos.refno)
    
FROM seg_ops_serv sos
INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN care_department cd ON (cd.nr = ceo.dept_nr)
INNER JOIN care_room cr ON (cr.room_nr=ceo.op_room)
INNER JOIN seg_pharma_or_main spom ON (spom.or_main_refno = sos.refno) $join INNER JOIN seg_pharma_orders spo ON (spo.refno = spom.pharma_refno)
 $where $sort $limit";
    $result = $db->Execute($sql);
    $row = $result->FetchRow();
    return $row[0];    
}



global $db;
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];
$target = $_GET['target'];
if (!$sortname) $sortname = 'orderdate';
if (!$sortorder) $sortorder = 'desc';

if ($sortname == 'refno') {
  $sortname = 'sos.refno';
}
$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 3;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$query = $_POST['query'];
//$qtype = $_POST['qtype'];
$where = "";
if ($query) $where = " WHERE sos.ordername LIKE '%$query%' OR spo.refno='$query' OR sos.pid='$query' OR DATE_FORMAT(spo.orderdate, '%m.%d.%Y')='$query'";

if ($target == 'select_or_request') {
  $join = 'INNER JOIN seg_or_main som ON (som.ceo_refno <> ceo.refno)';
}
elseif ($target == 'select_or_main_request') {
  $join = 'INNER JOIN seg_or_main som ON (som.ceo_refno = ceo.refno)';
}

$total = countRec($where);   



header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-type: text/x-json");
$json = "";
$json .= "{\n";
$json .= "page: $page,\n";
$json .= "total: $total,\n";
$json .= "rows: [";
$rc = false;
$query = "SELECT sos.refno as request_refno, spo.refno as pharma_refno,
spo.orderdate,
sos.pid, sos.ordername,
GROUP_CONCAT(cppm.artikelname) as items
FROM seg_ops_serv sos
INNER JOIN care_encounter_op ceo ON (sos.refno = ceo.refno)
INNER JOIN care_department cd ON (cd.nr = ceo.dept_nr)
INNER JOIN care_room cr ON (cr.room_nr=ceo.op_room)
INNER JOIN seg_pharma_or_main spom ON (spom.or_main_refno = sos.refno)
INNER JOIN seg_pharma_orders spo ON (spo.refno = spom.pharma_refno)
INNER JOIN seg_pharma_order_items spoi ON (spoi.refno = spo.refno) $join INNER JOIN care_pharma_products_main cppm ON (cppm.bestellnum = spoi.bestellnum) $where GROUP BY spo.refno $sort $limit";
// $query = "SELECT * FROM care_type_anaesthesia $where $sort $limit";
   $result = $db->Execute($query);
   while($row = $result->FetchRow()) {
     //$js = "javascript:add_or_main_anesthesia(\'$table_name\', {$row['nr']}, \'{$row['id']}\')";
     $edit = '<a href="'.$root_path.'modules/or/request/edit_or_charge.php'.URL_APPEND.'&refno='.$row['request_refno'].'&pharma_refno='.$row['pharma_refno'].'&target='.$target.'" id="edit_charge"></a>';
     $delete = '<a href="#" onclick="confirmDelete(\''.$row['pharma_refno'].'\')" id="delete_charge"></a>';
     if ($rc) $json .= ",";
    $json .= "\n{";
    $json .= "id:'".$row['pharma_refno']."',";
    $json .= "cell:['".$row['pharma_refno']."'";
    $json .= ",'".$row['orderdate']."'";
    $json .= ",'".$row['pid']."'";
    $json .= ",'".ucwords(strtolower($row['ordername']))."'";
    $json .= ",'".$row['items']."'";
    
    $json .= ",'".$edit.addslashes($delete)."']";
    $json .= "}";
    $rc = true;                    
 
   }


$json .= "]\n";
$json .= "}";
echo $json;
 
?>



