<?php
require_once('roots.php');

require($root_path.'include/inc_environment_global.php');   



   
function countRec($where) {
       global $db, $area;
    $sql = "SELECT COUNT(serial_no) FROM seg_inventory WHERE item_code='OT' AND area_code='$area' $where $sort $limit";
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
$area = $_GET['area'];
if (!$sortname) $sortname = 'expiry_date';
if (!$sortorder) $sortorder = 'desc';


$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 3;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$query = $_POST['query'];
//$qtype = $_POST['qtype'];
$where = "";
if ($query) $where = " AND serial_no LIKE '%$query%'";
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

 $query = "SELECT serial_no, qty, expiry_date FROM seg_inventory WHERE item_code='OT' AND area_code='$area' $where $sort $limit";
   $result = $db->Execute($query);
   while($row = $result->FetchRow()) {
     $js = "javascript:xajax_add_oxygen(\'$table_name\', \'{$row['serial_no']}\')";
     $select = '<input type="button" value="Select" onclick="'.$js.'"/>';
    
     if ($rc) $json .= ",";
    $json .= "\n{";
    $json .= "id:'".$row['serial_no']."',";
    $json .= "cell:['".$row['serial_no']."'";
    $json .= ",'".$row['qty']."'";
    $json .= ",'".$row['expiry_date']."'";
    $json .= ",'".$select."']";
    $json .= "}";
    $rc = true;                    
 
   }


$json .= "]\n";
$json .= "}";
echo $json;
 
?>



