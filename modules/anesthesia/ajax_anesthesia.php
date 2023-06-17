<?php
require_once('roots.php');

require($root_path.'include/inc_environment_global.php');   



class Omick  {
   
   function countRec() {
       global $db;
    $sql = "SELECT count(*) FROM care_type_anaesthesia $sort $limit";
    $result = $db->Execute($sql);
    $row = $result->FetchRow();
    return $row[0];    
}
}

$omick = new Omick();
global $db;
$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];
$table_name = $_GET['table'];
if (!$sortname) $sortname = 'nr';
if (!$sortorder) $sortorder = 'desc';

$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 3;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$query = $_POST['query'];
$qtype = '';
$where = "";
if ($query) $where = " WHERE name LIKE '%$query%' ";
$total = $omick->countRec();   

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
 $query = "SELECT nr, name FROM care_type_anaesthesia $where $sort $limit";
   $result = $db->Execute($query);
   while($row = $result->FetchRow()) {
     $js = "javascript:add_or_main_anesthesia(\'$table_name\', {$row['nr']}, \'{$row['name']}\')";
     $select = '<input type="button" value="Select" onclick="'.$js.'"/>';
     if ($rc) $json .= ",";
    $json .= "\n{";
    $json .= "id:'".$row['nr']."',";
    $json .= "cell:['".$row['name']."'";
    $json .= ",'".$select."']";
    $json .= "}";
    $rc = true; 
   }


$json .= "]\n";
$json .= "}";
echo $json;
 
?>
