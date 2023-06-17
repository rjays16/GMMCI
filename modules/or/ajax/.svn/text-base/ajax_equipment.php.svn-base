<?php
require_once('roots.php');

require($root_path.'include/inc_environment_global.php');   



   
function countRec($where) {
       global $db;
    $sql = "SELECT COUNT(bestellnum) FROM care_pharma_products_main WHERE type_nr=4 $where $sort $limit";
    $result = $db->Execute($sql);
    $row = $result->FetchRow();
    return $row[0];    
}



global $db;
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];
$table_name = $_GET['table'];
if (!$sortname) $sortname = 'equipment_id';
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
if ($query) $where = " AND (bestellnum='$query' OR artikelname LIKE '%$query%' OR description LIKE '%$query%')";
$total = countRec($where);   


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

 $query = "SELECT bestellnum as equipment_id, artikelname as equipment_name, description as equipment_description, 
           unit as equipment_unit, price_cash as equipment_cash, price_charge as equipment_charge, 
           is_socialized FROM care_pharma_products_main WHERE type_nr=4 $where $sort $limit";
   $result = $db->Execute($query);
   while($row = $result->FetchRow()) {
     $js = "javascript:xajax_add_equipment(\'$table_name\', \'{$row['equipment_id']}\')";
     $select = '<input type="button" value="Select" onclick="'.$js.'"/>';
     $is_socialized = ($row['is_socialized'] == 1) ? 'Yes' : 'No';
     if ($rc) $json .= ",";
    $json .= "\n{";
    $json .= "id:'".$row['equipment_id']."',";
    $json .= "cell:['".$row['equipment_name']."'";
    $json .= ",'".$row['equipment_description']."'";
    $json .= ",'".$row['equipment_unit']."'";
    $json .= ",'".number_format($row['equipment_cash'], 2, '.', ',')."'";
	$json .= ",'".number_format($row['equipment_charge'], 2, '.', ',')."'";
    $json .= ",'".$is_socialized."'";
    
    $json .= ",'".$select."']";
    $json .= "}";
    $rc = true;                    
 
   }


$json .= "]\n";
$json .= "}";
echo $json;
 
?>



